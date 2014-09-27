<?php
class Purchase_order_master extends Base_model 
{	
	
        function insert($insert_data,$set_array=array())
	{
            return parent::insertInternal('purchase_order',$insert_data, $set_array);
	}
        
        function update($where_clause_array,$update_data_array,$set_array=array())
	{
            return parent::updateInternal('purchase_order',$where_clause_array, $update_data_array, $set_array);
	}
        
        function getById($id,$column_array,$isarray){
            
            return parent::getUniqueRow('purchase_order',array('id'=>$id),$column_array,$isarray);
        }
        
         function exists($id)
	{
		return (parent::existInternal('purchase_order', $id));
	}
        
        function getGridById($id,$column_array,$isarray=true){
           return parent::getUniqueRow('purchase_order_grid',array('id'=>$id),$column_array,$isarray);
        }
        
        function getGridByReference($reference,$isarray=true){
            return parent::getUniqueRow('purchase_order_grid',array('reference'=>$reference),null,$isarray);
        }
        
        function getGridByQuoteReference($reference,$isarray=true){
            return parent::getUniqueRow('purchase_order_grid',array('quote_reference'=>$reference),null,$isarray);
        }
        
        function getAll($csv =false,$whereClause=null,$columns_array = null,$order_limit_clause=array(),$like_fields_array=null,$in_where_clause_array=null,$or_where_clause_array=null,$not_in_where_clause_array=null){
            return parent::getResults('purchase_order_grid', $csv, $whereClause, $columns_array, $order_limit_clause, $like_fields_array, $in_where_clause_array, $or_where_clause_array,$not_in_where_clause_array);
        }
        
        function totalNoOfRows ($where_clause_array=null,$like_fields_array=null,$or_where_clause_array=null) {
            return parent::totalNoOfRowsForResults('purchase_order_grid', $where_clause_array, $like_fields_array, $or_where_clause_array);
       }
}

?>