<?php
class Receipt_partpayment extends Base_Model 
{	
	function insert($insert_data,$set_array)
	{
            return parent::insertInternal('receipt_partpayment',$insert_data, $set_array);
	}
        function update_batch($values,$key)
	{
            $this->db->update_batch('receipt_partpayment',$values,$key);
            log_message('debug',$this->db->last_query());   
	}
        function update($where_clause_array,$update_data_array,$set_array)
	{
            return parent::updateInternal('receipt_partpayment',$where_clause_array, $update_data_array, $set_array);
	}
        
        function delete($id)
	{
            
            return $this->db->delete('receipt_partpayment', array('id' => $id)); 
            
	}
        
        function getById($id,$column_array,$isarray){
            
            return parent::getUniqueRow('receipt_partpayment_grid',array('id'=>$id),$column_array,$isarray);
        }
        
        function getAll($csv =false,$whereClause=null,$columns_array = null,$order_limit_clause=array(),$like_fields_array=null,$in_where_clause_array=null,$or_where_clause_array=null,$not_in_where_clause_array=null){
            return parent::getResults('receipt_partpayment_grid', $csv, $whereClause, $columns_array, $order_limit_clause, $like_fields_array, $in_where_clause_array, $or_where_clause_array,$not_in_where_clause_array);
        }
        
        
        function totalNoOfRows ($where_clause_array=null,$like_fields_array=null,$or_where_clause_array=null,$in_where_clause_array=null) {
            return parent::totalNoOfRowsForResults('receipt_partpayment_grid', $where_clause_array, $like_fields_array, $or_where_clause_array,$in_where_clause_array);
        }
        
        function getSumTotal($column,$whereClause,
                $in_where_clause_array,$or_where_clause_array){
           return  parent::getSumTotal('receipt_partpayment_grid', $column, $whereClause, $in_where_clause_array,$or_where_clause_array);
            
        }
       
}

?>