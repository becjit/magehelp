<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of purchase_invoice_master
 *
 * @author abhijit
 */
class Purchase_invoice_master extends Base_model  {
    //put your code here
    function insert($insert_data,$set_array)
	{
           return parent::insertInternal('purchase_invoice',$insert_data, $set_array);
	}
        
        function update($where_clause_array,$update_data_array,$set_array)
	{
            return parent::updateInternal('purchase_invoice',$where_clause_array, $update_data_array, $set_array);
           
	}
        
        function exists($id)
	{
            return (parent::existInternal('purchase_invoice_grid', $id));
	}
        
        function getById($id,$column_array,$isarray=false){
            return parent::getUniqueRow('purchase_invoice_grid',array('id'=>$id),$column_array,$isarray);
                        
        }
        
        function getAll($csv =false,$whereClause=null,$columns_array = null,$order_limit_clause=array(),$like_fields_array=null,$in_where_clause_array=null,$or_where_clause_array=null,$not_in_where_clause_array=null){
            return parent::getResults('purchase_invoice_grid', $csv, $whereClause, $columns_array, $order_limit_clause, $like_fields_array, $in_where_clause_array, $or_where_clause_array,$not_in_where_clause_array);
        }
        
        function totalNoOfRows ($where_clause_array=null,$like_fields_array=null,$or_where_clause_array=null) {
            return parent::totalNoOfRowsForResults('purchase_invoice_grid', $where_clause_array, $like_fields_array, $or_where_clause_array);
       }
        
}

?>
