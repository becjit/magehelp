<?php
class Purchase_quote_item extends Base_Model 
{	
	function insert($insert_data,$set_array)
	{
            return parent::insertInternal('purchase_quote_item',$insert_data, $set_array);
	}
        function update($where_clause_array,$update_data_array,$set_array)
	{
            return parent::updateInternal('purchase_quote_item',$where_clause_array, $update_data_array, $set_array);
	}
        
        function delete($id)
	{
            
            return $this->db->delete('purchase_quote_item', array('id' => $id)); 
            
	}
        
        function getById($id,$column_array,$isarray){
            
            return parent::getUniqueRow('purchase_quote_item',array('id'=>$id),$column_array,$isarray);
        }
        
        function getByQuoteId($id){
            return parent::getResults('purchase_quote_item_grid', false, array('quote_id'=>$id));
        }
        function getAll($csv =false,$whereClause=null,$columns_array = null,$order_limit_clause=array(),$like_fields_array=null,$in_where_clause_array=null,$or_where_clause_array=null,$not_in_where_clause_array=null){
            return parent::getResults('purchase_quote_item_grid', $csv, $whereClause, $columns_array, $order_limit_clause, $like_fields_array, $in_where_clause_array, $or_where_clause_array,$not_in_where_clause_array);
        }
        
        
        function totalNoOfRows ($where_clause_array=null,$like_fields_array=null,$or_where_clause_array=null) {
            return parent::totalNoOfRowsForResults('purchase_quote_item_grid', $where_clause_array, $like_fields_array, $or_where_clause_array);
        }
       
}

?>