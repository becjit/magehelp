<?php
class Supplier extends Base_model
{	
	/*
	Determines if a given person_id is a customer
	*/
	function exists($id)
	{
		$this->db->from('suppliers');	
		
		$this->db->where('id',$id);
		$query = $this->db->get();
		
		return ($query->num_rows()==1);
	}
	
	function getAll($csv = false,$whereClause=null,$order_limit_clause=array(),$like_fields_array=null,$in_where_clause_array=null,$or_where_clause_array=null,$deleted=0){
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
            $this->db->where('deleted',$deleted);
            if (!empty($whereClause)){
                $this->db->where($whereClause);
            }
            
            if (!empty($or_where_clause_array)){
                $this->db->or_where($or_where_clause_array);
            }
            
            if (!empty($in_where_clause_array)){
                $this->db->where_in($in_where_clause_array['field_name'],$in_where_clause_array['value_array']);
            }
            
            if (!empty($like_fields_array)){
                $this->db->like($like_fields_array);
            }
            $this->db->order_by($orderBy,$orderDir);
            $this->db->limit($limit,$startLimit);
            $query = $this->db->get('suppliers');
            log_message('debug',$this->db->last_query());
            if ($csv){
                return $this->dbutil->csv_from_result($query);
            }
            return $query->result_array();
        }
        
        
        function totalNoOfRows ($where_clause_array=null,$like_fields_array=null,$or_where_clause_array=null) {
            if (!empty($where_clause_array)){
                $this->db->where($where_clause_array);
            }
            
            if (!empty($or_where_clause_array)){
                $this->db->or_where($or_where_clause_array);
            }
            
            if (!empty($like_fields_array)){
                $this->db->like($like_fields_array);
            }
            $this->db->where('deleted',0);
            $this->db->from('suppliers');
            return $this->db->count_all_results() ;
            
            
        }
	
	
	/*
	Inserts or updates a suppliers
	*/
	function save($supplier_data,$supplier_id=false)
	{
		$success=false;
		
                if (!$supplier_id or !$this->exists($supplier_id))
                {

                        $success = $this->db->insert('suppliers',$supplier_data);				
                }
                else
                {
                        $this->db->where('id', $supplier_id);
                        $success = $this->db->update('suppliers',$supplier_data);
                }
				
		return $success;
	}
	
	/*
	Deletes one supplier
	*/
	function delete($supplier_id)
	{
		$this->db->where('id', $supplier_id);
		return $this->db->update('suppliers', array('deleted' => 1));
	}
        
        function getById($supplier_id,$isarray=true)
	{
		$this->db->where('id', $supplier_id);
                $this->db->where('deleted', 0);
                $query = $this->db->get('suppliers');
               if ($query->num_rows() > 0)
            {
                if($isarray){
                    return $query->row_array(); 
                }
                else {
                    return $query->row(); 
                }
                
                
            } 
	}
        function getByName($supplier_name)
	{
            $this->db->where('supplier_name', $supplier_name);
            $this->db->where('deleted', 0);
            return $this->db->get('suppliers');
	}
        
        function createProductSupplierMapping($product_id,$supplier_id_array)
	{
            if (!empty($supplier_id_array) && !empty($product_id)){
                $insert_batch_data = array();
                foreach($supplier_id_array as $supplier_id){
                    array_push($insert_batch_data,array(      
                    'product_id' => $product_id ,
                    'supplier_id' => $supplier_id
                 ));
                }
                $this->db->insert_batch('product_supplier_mapping', $insert_batch_data); 
            }
	}
        
        function getAllProductsSupplierMapping($csv =false,$whereClause=null,$columns_array = null,$order_limit_clause=array(),$like_fields_array=null,$in_where_clause_array=null,$or_where_clause_array=null,$not_in_where_clause_array=null){
            return parent::getResults('product_supplier_mapping_grid', $csv, $whereClause, $columns_array, $order_limit_clause, $like_fields_array, $in_where_clause_array, $or_where_clause_array,$not_in_where_clause_array);
           
        }
	
	 function totalNoOfRowsInProductSupplierMapping () {
            
            $this->db->from('product_supplier_mapping_grid');
            return $this->db->count_all_results() ;
            
            
        }
        function deleteSupplierMapping($id)
	{
		$this->db->where('id', $id);
		return $this->db->delete('product_supplier_mapping');
	}
//        function existsMapping($supplier_id,$product_id)
//	{
//		$this->db->from('product_supplier_mapping');	
//		
//		$this->db->where('supplier_id',$supplier_id);
//                $this->db->where('product_id',$product_id);
//		$query = $this->db->get();
//		
//		return ($query->num_rows()==1);
//	}

}
?>
