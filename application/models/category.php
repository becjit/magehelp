<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of category
 *
 * @author abhijit
 */
class Category extends CI_Model 
{	
	function save($category_data,$magento_entity_id=false){
            $success=false;
            //Run these queries as a transaction, we want to make sure we do all or nothing
            if (!empty($category_data)){
                if (!$magento_entity_id or !$this->exists($magento_entity_id)){
                    $success = $this->db->insert('category',$category_data);
                    if ($success){
                        log_message('debug','Category Inserted');
                    }
                }
                else{
                    $this->db->where('magento_entity_id', $magento_entity_id);
                    $success = $this->db->update('category',$category_data);
                    if ($success){
                        log_message('debug','Category Updated');
                    }
                }
            }
            
            if (!$success)
            {
                //echo $this->db->_error_message();
               log_message('Category Creation/Updation Failed '.$this->db->_error_message() );
               throw new Exception('Category Creation/Updation Failed' );
            }
           
            return $success;
        }
        
        function exists($magento_entity_id)
	{
		$this->db->from('category');	
		$this->db->where('magento_entity_id',$magento_entity_id);
		$query = $this->db->get();
                return ($query->num_rows()==1);
	}
        
//        function getAll($csv = false,$whereClause=null,$owner_null=false,$or_where_clause_array=null,$clause=array(),$fields_array=null){
//            $orderBy = 'id';
//            $orderDir= 'desc';
//            $startLimit = 0;
//            $limit = 1000;
//            
//            if (!empty($clause['orderBy'])){
//                $orderBy = $clause['orderBy'];
//            }
//            if (!empty($clause['orderDir'])){
//                $orderDir = $clause['orderDir'];
//            }
//            if (!empty($clause['startLimit'])){
//                $startLimit = $clause['startLimit'];
//            }
//            if (!empty($clause['limit'])){
//                $limit = $clause['limit'];
//            }
//        
//            $this->load->dbutil();  
//            $this->db->select('*');
//            if (!empty($whereClause)){
//                $this->db->where($whereClause);
//            }
//            if ($owner_null){
//                $this->db->or_where('OWNER_ID IS NULL',null,false);
//            }
//            if (!empty($or_where_clause_array)){
//                $this->db->or_where($or_where_clause_array);
//            }
//            
//            if (!empty($fields_array)){
//                $this->db->like($fields_array);
//            }
//            $this->db->order_by($orderBy,$orderDir);
//            $this->db->limit($limit,$startLimit);
//            $query = $this->db->get('invoice');
//            
//            if ($csv){
//                return $this->dbutil->csv_from_result($query);
//            }
//            return $query->result_array();
//        }
        
        
        function getCategoriesFromMagento()
	{
		//$entityId = $this->lastEntityIdFetched();
                $select = "SELECT ent.`entity_id` as magento_entity_id , ent.`parent_id` parent_category_id , ent.`path`, ent.`position`, 
                    ent.`level`, ent.`children_count`, entvar.value category_name FROM `catalog_category_entity` ent 
                    LEFT JOIN catalog_category_entity_varchar entvar on ent.entity_id = entvar.entity_id 
                    where entvar.attribute_id = 111 union SELECT ent.`entity_id`, ent.`parent_id`, ent.`path`, ent.`position`, 
                    ent.`level`, ent.`children_count`,'root' FROM `catalog_category_entity` ent where ent.entity_id = 1 order by magento_entity_id";
                
                $sql = $select;
		
		$query = $this->magento->query($sql);

		return $query->result_array();
	}
        
        
        
        function importCategories(){
           $success = false;
            
           $categories = $this->getCategoriesFromMagento();
           if (!empty($categories)){
                $this->db->trans_start();
                foreach ($categories as $category){
                    $this->save($category,$category['magento_entity_id']);
                }
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE)
                {
                    //echo $this->db->_error_message();
                    
                   log_message('Category Import Failed '.$this->db->_error_message() );
                   throw new Exception('Category Import Failed' );
                }
                else {
                    $success = true;
                    log_message('debug','Category Import Successful');
                }
           }
            return $success;
            
        }
        
        function getAll($level=null){
            $this->db->select('*');
            if (!empty($level)){
                $this->db->where('level',$level);
            }
            $query = $this->db->get('category');
            return $query->result_array();
        }
        
        function getChildren($parent_id = 0){
            $this->db->select('*');
            if (!empty($parent_id)){
                $this->db->where('parent_category_id',$parent_id);
            }
            $query = $this->db->get('category');
            return $query->result_array();
        }
        
        function getById($id){
            $this->db->select('*');
            if (!empty($id)){
                $this->db->where('magento_entity_id',$id);
            }
            $query = $this->db->get('category');
            return $query->row_array();
        }
        
        
        function mappingExists($magento_entity_id,$product_id)
	{
		$this->db->from('product_category_mapping');	
		$this->db->where('category_id',$magento_entity_id);
                $this->db->where('product_id',$product_id);
		$query = $this->db->get();
                return ($query->num_rows()==1);
	}
        
        function saveProductCategoryMapping($magento_category_entity_id,$product_id,$category=null,$barcode=null){
            $success=false;
            //Run these queries as a transaction, we want to make sure we do all or nothing
            if (!empty($magento_category_entity_id)&& !empty($product_id) ){
                if (empty($category)){
                    $categoryDetails = $this->getById($magento_category_entity_id);
                    if (!empty($categoryDetails)){
                        $category = $categoryDetails['category_name'];
                    }
                }
                if (empty($barcode)){
                    $barcode = $this->Product->getBarcode(array('id'=>$product_id));
                    
                }
                $mapping_data['product_id']=$product_id;
                $mapping_data['barcode']=$barcode;
                $mapping_data['category_id']=$magento_category_entity_id;
                $mapping_data['category_name']=$category;
                try {
                    if (!$this->mappingExists($magento_category_entity_id,$product_id)){
                        $success = $this->db->insert('product_category_mapping',$mapping_data);
                    }
//                    else{
//
//                        $this->db->where('id', $id);
//                        $success = $this->db->update('product_category_mapping',$mapping_data);
//
//                    }
                    if ($success){
                        log_message('debug','Product Category Mapping  Suceesfully Created');
                    }
                }
                catch (Exception $e){
                    log_message('Product Category Mapping Creation Failed '.$this->db->_error_message() );
                    throw new Exception('Product Category Mapping Creation Failed' );
                }
                
            }
            
            
            return $success;
        }
        
        function getAllProductCategoryMapping($csv = false,$whereClause=null,$order_limit_clause=array(),$like_fields_array=null,$or_where_clause_array=null){
            $orderBy = 'id';
            $orderDir= 'desc';
            $startLimit = 0;
            $limit = 1000;
            
            if (!empty($order_limit_clause['orderBy'])){
                $orderBy = $order_limit_clause['orderBy'];
            }
            if (!empty($order_limit_clause['orderDir'])){
                $orderDir = $order_limit_clause['orderDir'];
            }
            if (!empty($order_limit_clause['startLimit'])){
                $startLimit = $order_limit_clause['startLimit'];
            }
            if (!empty($order_limit_clause['limit'])){
                $limit = $order_limit_clause['limit'];
            }
        
            $this->load->dbutil();  
            $this->db->select('*');
            if (!empty($whereClause)){
                $this->db->where($whereClause);
            }
            
            if (!empty($or_where_clause_array)){
                $this->db->or_where($or_where_clause_array);
            }
            
            if (!empty($like_fields_array)){
                $this->db->like($like_fields_array);
            }
            $this->db->order_by($orderBy,$orderDir);
            $this->db->limit($limit,$startLimit);
            $query = $this->db->get('product_category_mapping');
            
            if ($csv){
                return $this->dbutil->csv_from_result($query);
            }
            return $query->result_array();
        }
        
        function totalNoOfRowsInProductCategoryMapping () {
            
            $this->db->from('product_category_mapping');
            return $this->db->count_all_results() ;
            
            
        }
        
        function deleteProductCategoryMapping ($id) {
            $this->db->where('id',$id);
            $this->db->delete('product_category_mapping');
            
            
        }
        
        
}





?>
