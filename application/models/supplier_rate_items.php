<?php
class Supplier_rate_items extends Base_model
{	
	function insert($insert_data,$set_array)
	{
            return parent::insertInternal('pricelist_items',$insert_data, $set_array);
	}
        
        function update($where_clause_array,$update_data_array,$set_array)
	{
            return parent::updateInternal('pricelist_items',$where_clause_array, $update_data_array, $set_array);
	}
        function delete($lineitem_id){
            $this->db->delete('pricelist_items',array('id'=>$lineitem_id));
        }
       
        function getById($id,$column_array,$isarray){
            
            return parent::getUniqueRow('supplier_rate_items',array('id'=>$id),$column_array,$isarray);
        }
	
        
        function getAll($csv =false,$whereClause=null,$columns_array = null,$order_limit_clause=array(),$like_fields_array=null,$in_where_clause_array=null,$or_where_clause_array=null,$not_in_where_clause_array=null){
            return parent::getResults('supplier_rate_items', $csv, $whereClause, $columns_array, $order_limit_clause, $like_fields_array, $in_where_clause_array, $or_where_clause_array,$not_in_where_clause_array);
        }
        
        
        function totalNoOfRows ($where_clause_array=null,$like_fields_array=null,$or_where_clause_array=null) {
            return parent::totalNoOfRowsForResults('supplier_rate_items', $where_clause_array, $like_fields_array, $or_where_clause_array);
       }
        
        function getAllUnion($column_array,$pricelistid,$supplier_id,$where_array,$order_limit_clause,$like_fields_array,$iscount=false){
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
            $sql = "SELECT ";
            if (!empty($column_array)){
               $count = count($column_array);
               
               $index = 0;
               foreach ($column_array as $column) {
                   $sql .= '`'.$column.'` ';
                   $index++;
                   if ($index!= $count){
                       $sql .= ' , ';
                   }
               }
            }
            else {
                $sql .= ' * ';
            }
            $sql .= " FROM `".$this->db->dbprefix."supplier_rate_items` where `pricelist_id` = (select `parent` from `".$this->db->dbprefix."pricelist`
                    where `id`= ? ) and `product_id` not in 
                    (select `product_id` FROM `".$this->db->dbprefix."supplier_rate_items` where `pricelist_id`= ? ) ";
            if (!empty($like_fields_array)){
                foreach($like_fields_array as $key=>$value){
                    $sql .=" and ";
                    $sql .= $key." LIKE '%".$this->db->escape_like_str($value)."%'";
                }
                
            }
            if (!empty($where_array)){
                foreach($where_array as $key=>$value){
                    $sql .=" and ";
                    $sql .= $key." = '".$value."'";
                }
                
            }
            
            $sql .=" UNION SELECT ";
            if (!empty($column_array)){
                $index = 0;
               foreach ($column_array as $column) {
                   $sql .= '`'.$column.'` ';
                   $index++;
                   if ($index!= $count){
                       $sql .= ' , ';
                   }
               }
            }
            else {
                $sql .= ' * ';
            }
            $sql .= " FROM `".$this->db->dbprefix."supplier_rate_items` where `pricelist_id` in (0,?) and supplier_id=? ";
            if (!empty($like_fields_array)){
                foreach($like_fields_array as $key=>$value){
                    $sql .=" and ";
                    $sql .= $key." LIKE '%".$this->db->escape_like_str($value)."%'";
                }
                
            }
            if (!empty($where_array)){
                foreach($where_array as $key=>$value){
                    $sql .=" and ";
                    $sql .= $key." = '".$value."'";
                }
                
            }
            if ($iscount){
                $sql = "select count(*) as total from ( ".$sql." ) as itemsforcount";
            }
            else {
                $sql.=" order by `".$orderBy."` ".$orderDir." LIMIT ".$startLimit.",".$limit;
            }
            
            $query = $this->db->query($sql,array($pricelistid,$pricelistid,$pricelistid,$supplier_id));
            log_message('debug', $this->db->last_query());
            if ($iscount){
                return $query->row()->total;
            }
            return $query->result_array();
        }
        
         function getPriceForProduct($product_id,$pricelist_id,$supplier_id){
            $where_array= array('product_id'=>$product_id);
            return $this->getAllUnion(array('id','base_price','pricelist_id'), $pricelist_id, $supplier_id, $where_array);
        }
        

}

?>