<?php
class Product extends Base_model 
{	
	function insert($mfr_data)
	{
            //$this->db->insert('invoice',$invoice_data);
           $this->db->insert('products',$mfr_data);
           
           return $this->db->insert_id();
	}
        
        function update($id,$product_data)
	{
            //$this->db->insert('invoice',$invoice_data);
           $this->db->where('id',$id);
           return $this->db->update('products',$product_data);
                      
	}
        
        
        
        function activate($ids)
	{
            //$this->db->insert('invoice',$invoice_data);
           $this->db->where_in('id',$ids);
           return $this->db->update('products',array('isactive'=>1));
                      
	}
        
        function deactivate($ids)
	{
            //$this->db->insert('invoice',$invoice_data);
           $this->db->where_in('id',$ids);
           return $this->db->update('products',array('isactive'=>0));
                      
	}
        
        
	
        function getAll($csv =false,$whereClause=null,$columns_array = null,$order_limit_clause=array(),$like_fields_array=null,$in_where_clause_array=null,$or_where_clause_array=null,$not_in_where_clause_array=null){
            return parent::getResults('products_grid', $csv, $whereClause, $columns_array, $order_limit_clause, $like_fields_array, $in_where_clause_array, $or_where_clause_array,$not_in_where_clause_array);
        }
        
        function getAllByLike($fields_array,$csv = false){
            $this->load->dbutil();
            $this->db->select('*');
            $this->db->like($fields_array);
            $query = $this->db->get('products');
            if ($csv){
                return $this->dbutil->csv_from_result($query);
            }
            return $query->result_array();
        }
        
//        function getAllCsv(){
//            $this->load->dbutil();
//            $this->db->select('*');
//            $query = $this->db->get('products');
//            return $this->dbutil->csv_from_result($query);
//        }
        
        function getAllFlattened($csv = false,$clause=array(),$isactive=null){
            $orderBy = 'id';
            $orderDir= 'desc';
            $startLimit = 0;
            $limit = 10;
            
            if (!empty($clause['orderBy'])){
                $orderBy = $clause['orderBy'];
            }
            if (!empty($clause['orderDir'])){
                $orderDir = $clause['orderDir'];
            }
            if (!empty($clause['startLimit'])){
                $startLimit = $clause['startLimit'];
            }
            if (!empty($clause['limit'])){
                $limit = $clause['limit'];
            }
            
            $orderbyClause = " ORDER BY ".$orderBy." ".$orderDir;
            $limitClause = " LIMIT ".$startLimit.", ".$limit;
            
            $this->load->dbutil();
            $baseSql = "select product.id,product.barcode,product.description,
                product.manufacturer_id,product.model_id,product.attribute_set, 
                product.meta_description,
                
                product.reorder_level,
                product.vat,
                product.manufacturer,product.uom,product.measurement_denomination,
                product.product_name,product.model, product.package_id,product.isactive,package.package_name,
                package.package_description from ".$this->db->dbprefix."products product 
                left join ".$this->db->dbprefix."package package using (package_id)";
            
            if (!empty($isactive)){
                //only add for boolean ;ignore any other value;
                if ($isactive == 1 || $isactive == 0){
                    $baseSql .= ' where isactive = '.$isactive;
                }
                 
            }
            $sql=$baseSql.$orderbyClause.$limitClause;
            $query = $this->db->query($sql);
           
            //$query = $this->db->get('products');
            if ($csv){
                return $this->dbutil->csv_from_result($query);
            }
            return $query->result_array();
        }
        
        function getAllFlattenedByLike($fields_array,$csv = false,$clause=array(),$isactive=null){
            $oper ='and';
            $orderBy = 'id';
            $orderDir= 'desc';
            $startLimit = 0;
            $limit = 10;
            if (!empty($clause['oper'])){
                $oper = $clause['oper'];
            }
            if (!empty($clause['orderBy'])){
                $orderBy = $clause['orderBy'];
            }
            if (!empty($clause['orderDir'])){
                $orderDir = $clause['orderDir'];
            }
            if (!empty($clause['startLimit'])){
                $startLimit = $clause['startLimit'];
            }
            if (!empty($clause['limit'])){
                $limit = $clause['limit'];
            }
            
            $orderbyClause = " ORDER BY ".$orderBy." ".$orderDir;
            $limitClause = " LIMIT ".$startLimit.", ".$limit;
            
            
            $this->load->dbutil();
            $basesql = "select product.id,
                product.barcode,
               
                product.manufacturer,
                product.uom,
                product.description,
                product.measurement_denomination,
                product.product_name,
               
                product.model, 
                product.manufacturer_id,
                
                product.model_id,
                product.package_id,
                
                product.attribute_set, 
                product.meta_description,
                
                product.reorder_level,
                product.vat,
                
                product.isactive,
                package.package_name,
                package.package_description from ".$this->db->dbprefix."products product left join ".$this->db->dbprefix."package package using (package_id)";
            $whereClause = ' where';
            
            if (!empty($fields_array['barcode'])){
                $whereClause .= " product.barcode LIKE '%".$this->db->escape_like_str($fields_array['barcode'])."%'";
            }
            else {
                $whereClause .= " product.barcode is not null";
            }
            
//            if (!empty($fields_array['system_name'])){
//                
//                $whereClause .= " ".$oper." product.system_name LIKE '%".$this->db->escape_like_str($fields_array['system_name'])."%'";
//            }
            if (!empty($fields_array['manufacturer'])){
                
                $whereClause .= " ".$oper." product.manufacturer LIKE '%".$this->db->escape_like_str($fields_array['manufacturer'])."%'";
            }
            
            if (!empty($fields_array['uom'])){
                
                $whereClause .= " ".$oper." product.uom LIKE '%".$this->db->escape_like_str($fields_array['uom'])."%'";
            }
            
            if (!empty($fields_array['description'])){
                
                $whereClause .= " ".$oper." product.description LIKE '%".$this->db->escape_like_str($fields_array['description'])."%'";
            } 
            
            if (!empty($fields_array['measurement_denomination'])){
                
                $whereClause .= " ".$oper." product.measurement_denomination LIKE '%".$this->db->escape_like_str($fields_array['measurement_denomination'])."%'";
            }
            
            if (!empty($fields_array['product_name'])){
                
                $whereClause .= " ".$oper." product.product_name LIKE '%".$this->db->escape_like_str($fields_array['product_name'])."%'";
            }
            
            if (!empty($fields_array['model'])){
                
                $whereClause .= " ".$oper." product.model LIKE '%".$this->db->escape_like_str($fields_array['model'])."%'";
            }
            
            if (!empty($isactive)){
                
                if ( $isactive == 1 || $isactive == 0){
                    $whereClause .= " ".$oper." product.isactive =".$isactive;
                }
            }
            
            
            
            if (!empty($fields_array['package_name'])){
                
                $whereClause .= " ".$oper." package.package_name LIKE '%".$this->db->escape_like_str($fields_array['package_name'])."%'";
            }
            
            if (!empty($fields_array['package_description'])){
                
                $whereClause .= " ".$oper." package.package_description LIKE '%".$this->db->escape_like_str($fields_array['package_description'])."%'";
            }
            
            $sql = $basesql.$whereClause.$orderbyClause.$limitClause;
            $query = $this->db->query($sql);
            //$query = $this->db->get('products');
            if ($csv){
                return $this->dbutil->csv_from_result($query);
            }
            return $query->result_array();
        }
        
        

        
        function lastIdPresent () {
            $query = $this->db->query("select MAX(id) as lastproductid from ".$this->db->dbprefix."products");
            if ($query->num_rows() > 0)
            {
                $row = $query->first_row(); 
                $lastproductid = $row->lastproductid;
                if ($lastproductid == null){
                    $lastproductid = 0;
                }

                return  $lastproductid;
            
            } 
            
        }
        
        function totalNoOfRows ($where_clause_array=null,$like_fields_array=null,$or_where_clause_array=null) {
            return parent::totalNoOfRowsForResults('products', $where_clause_array, $like_fields_array, $or_where_clause_array);
       }
        
//        function totalNoOfRows () {
//            $query = $this->db->query("select count(*) as total from ".$this->db->dbprefix."products");
//            if ($query->num_rows() > 0)
//            {
//                $row = $query->first_row(); 
//                $lastproductid = $row->total;
//                if ($lastproductid == null){
//                    $lastproductid = 0;
//                }
//
//                return  $lastproductid;
//            
//            } 
//            
//        }
        
        function getValues($where_clause_array=array(),$column_array=array()){
            if (count($column_array)>0){
                $this->db->select($column_array);
            }
            else {
                $this->db->select('*');
            }
            if (count($where_clause_array)>0){
                $this->db->where($where_clause_array);
            }
            $result = $this->db->get('products');
            return $result->result_array();
            
        }
        
        function ifExistsByBarcode($barcode){
            $this->db->from('products');	
		
            $this->db->where('barcode',$barcode);
            $query = $this->db->get();
            return ($query->num_rows()==1);
            
        }
        function getBarcode($where_clause_array){
            
            $this->db->select('barcode');
           
            if (count($where_clause_array)>0){
                $this->db->where($where_clause_array);
            }
            $query = $this->db->get('products');
            if ($query->num_rows() > 0)
            {
                $row = $query->first_row(); 
                $barcode = $row->barcode;
                return  $barcode;
            
            } 
        }
        
        function getByProductId($id,$isArray=false){
            
            $this->db->select('*');
            $this->db->where('id',$id);
            
            $query = $this->db->get('products');
            if ($query->num_rows() > 0)
            {
                if ($isArray){
                    return $query->row_array();
                }
                else {
                    return $query->row();
                }
                
                
            } 
        }
        
        function getMagentoExportView(){
            
            $this->db->select('*');
            
            $query = $this->db->get('magento_product_export_view');
            return $this->dbutil->csv_from_result($query);
        }
}

?>