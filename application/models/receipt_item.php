<?php
class Receipt_item extends Base_model
{	
	function insert($purchase_quote_data)
	{
            //$this->db->insert('invoice',$invoice_data);
            $purchase_quote_data['created_at']=date('Y-m-d H:i:s');
            $status = $this->db->insert('receipt_item',$purchase_quote_data);
            log_message('debug',$this->db->last_query());
            return $status;
	}
        
         function update($where_clause_array,$purchase_quote_data,$notes_array)
	{
            //$this->db->insert('purchase_order',$purchase_data);
            $purchase_quote_data['last_updated_at']=date('Y-m-d H:i:s');
            $this->db->where($where_clause_array);
            if (!empty($notes_array)){
                foreach ($notes_array as $key => $value) {
                    $this->db->set($key, $value, FALSE);
                }
            }
            $status = $this->db->update('receipt_item',$purchase_quote_data);
            log_message('debug',$this->db->last_query());
            return $status;
	}
        //remove later
        function updateWithNotes($where_clause_array,$update_data,$notes_array)
	{
            $update_data['last_updated_at']=date('Y-m-d H:i:s');
            $this->db->where($where_clause_array);
            if (!empty($notes_array)){
                foreach ($notes_array as $key => $value) {
                    $this->db->set($key, $value, FALSE);
                }
            }
            
            $status = $this->db->update('receipt_item',$update_data);
            log_message('debug',$this->db->last_query());
            return $status;
	}
        
       
        function getById($id,$column_array,$isarray=true){ 
           return parent::getUniqueRow('receipt_item_grid',array('id'=>$id),$column_array,$isarray);
        }
	
        
        function getAll($csv =false,$whereClause=null,$columns_array = null,$order_limit_clause=array(),$like_fields_array=null,$in_where_clause_array=null,$or_where_clause_array=null,$not_in_where_clause_array=null){
            return parent::getResults('receipt_item_grid', $csv, $whereClause, $columns_array, $order_limit_clause, $like_fields_array, $in_where_clause_array, $or_where_clause_array,$not_in_where_clause_array);
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
            
            $this->db->from('receipt_item_grid');
            return $this->db->count_all_results() ;
            
            
        }
        
         function exists($order_line_id,$receipt_id){
             $this->db->from('receipt_item_grid');	
		//$this->db->join('people', 'people.person_id = employees.person_id');
            $this->db->where('order_line_id',$order_line_id);
            $this->db->where('receipt_id',$receipt_id);
            $query = $this->db->get();
            return ($query->num_rows()==1);
            
         }
        function getByOrderLineReceiptBatch($order_line_id,$receipt_id,$batch_number,$result_column_array=array(),$isarray=true){
            return parent::getByFieldUnique('receipt_item_grid', array('order_line_id'=>$order_line_id,'receipt_id'=>$receipt_id,'batch_number'=>$batch_number), $result_column_array,$isarray);
        }
        function getByBatchNumber($batch_number,$result_column_array,$isarray){
            
            return parent::getByFieldUnique('receipt_item_grid', array('batch_number'=>$batch_number), $result_column_array,$isarray);
        }
        
        //this function is to show reeived total value recevied qyantity from receipt item table  in order item subgrid
        
        function getTotalReceivedItemByOrderLineItemId($order_line_id){
            $this->db->select_sum('received_quantity','total_received');
            $this->db->where('order_line_id',$order_line_id);
            $query = $this->db->from("receipt_item");
            if ($query->num_rows() > 0)
            {
                return $query->row_array(); 
                
            } 
            
        }
        
         function totalAmount($receipt_id){
            $this->db->select_sum('received_value','total');
            if (!empty($receipt_id)){
                $this->db->where('receipt_id',$receipt_id);
            }
            $query =  $this->db->get('receipt_item');
            log_message('debug',$this->db->last_query());
            return $query->row()->total;
        }
	
        function getSumTotal($column,$whereClause,
                $in_where_clause_array,$or_where_clause_array){
           return  parent::getSumTotal('receipt_item', $column, $whereClause, $in_where_clause_array,$or_where_clause_array);
            
        }
        
	
}

?>