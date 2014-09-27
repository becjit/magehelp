<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of outgoing_payment
 *
 * @author abhijit
 */
class Outgoing_payment extends Base_model{
       function insert($insert_data,$set_array=array())
	{
            return parent::insertInternal('outgoing_payment',$insert_data, $set_array);
	}
        
         function update($where_clause_array,$update_data_array,$set_array)
	{
            return parent::updateInternal('outgoing_payment',$where_clause_array, $update_data_array, $set_array);
	}
        
        
        function getByPaymentId($id,$isarray=true){
             return parent::getUniqueRow('outgoing_payment',array('id'=>$id),null,$isarray);
        }
        function getByPaymentReference($ref,$isarray=true){
            return parent::getUniqueRow('outgoing_payment',array('payment_reference'=>$ref),null,$isarray);
            
        }
        
//        function getAll($csv = false,$whereClause=null,$order_limit_clause=array(),$like_fields_array=null,$in_where_clause_array=null,$or_where_clause_array=null){
//            $this->getColumnValues($csv,$whereClause,null,$order_limit_clause,$like_fields_array,$in_where_clause_array,$or_where_clause_array);
//        }
//        
        function getAll($csv =false,$whereClause=null,$columns_array = null,$order_limit_clause=array(),$like_fields_array=null,$in_where_clause_array=null,$or_where_clause_array=null,$not_in_where_clause_array=null){
            return parent::getResults('outgoing_payment', $csv, $whereClause, $columns_array, $order_limit_clause, $like_fields_array, $in_where_clause_array, $or_where_clause_array,$not_in_where_clause_array);
        }
        	
       
        
        
        
        function totalNoOfRows ($where_clause_array=null,$like_fields_array=null,$or_where_clause_array=null) {
            return parent::totalNoOfRowsForResults('outgoing_payment', $where_clause_array, $like_fields_array, $or_where_clause_array);
        }
        
         function totalAmount($invoice_id){
            $this->db->select_sum('total_value','total');
            if (!empty($invoice_id)){
                $this->db->where('invoice_id',$invoice_id);
            }
            $query =  $this->db->get('outgoing_payment');
            log_message('debug',$this->db->last_query());
            return $query->row()->total;
        }
	
        function createMapping($insert_data,$set_array)
	{
            return parent::insertInternal('payment_inheritence_mapping',$insert_data, $set_array);
	}
}

?>
