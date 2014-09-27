<?php
class Purchase_order_item extends Base_Model 
{	
	function insert($insert_data,$set_array)
	{
            return parent::insertInternal('purchase_order_item',$insert_data, $set_array);
	}
        function update($where_clause_array,$update_data_array,$set_array)
	{
            return parent::updateInternal('purchase_order_item',$where_clause_array, $update_data_array, $set_array);
	}
        
      
        function getById($id,$column_array,$isarray){
            
            return parent::getUniqueRow('purchase_order_item',array('id'=>$id),$column_array,$isarray);
        }
        
        function getAll($csv =false,$whereClause=null,$columns_array = null,$order_limit_clause=array(),$like_fields_array=null,$in_where_clause_array=null,$or_where_clause_array=null,$not_in_where_clause_array=null){
            return parent::getResults('purchase_order_item', $csv, $whereClause, $columns_array, $order_limit_clause, $like_fields_array, $in_where_clause_array, $or_where_clause_array,$not_in_where_clause_array);
        }
        
        
        function totalNoOfRows ($where_clause_array=null,$like_fields_array=null,$or_where_clause_array=null) {
            return parent::totalNoOfRowsForResults('purchase_order_item', $where_clause_array, $like_fields_array, $or_where_clause_array);
        }
        
	
}

?>