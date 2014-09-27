<?php
class Receipt_master extends Base_model 
{	
	function insert($purchase_quote_data)
	{
            $CI = & get_instance();
            $user = $CI->User->get_logged_in_employee_info();
            $last_updated_by = $user->person_id;
            $purchase_quote_data['created_at']=date('Y-m-d H:i:s');
            $purchase_quote_data['last_updated_by'] = $last_updated_by;
            $status =  $this->db->insert('receipt',$purchase_quote_data);
            log_message('debug','insert statement ='.$this->db->last_query());
            return $status;
	}
        
        function update($where_clause_array,$purchase_quote_data,$notes_array)
	{
            //$this->db->insert('purchase_order',$purchase_data);
            $CI = & get_instance();
            $user = $CI->User->get_logged_in_employee_info();
            $last_updated_by = $user->person_id;
            $purchase_quote_data['last_updated_at']=date('Y-m-d H:i:s');
            $purchase_quote_data['last_updated_by'] = $last_updated_by;
            $this->db->where($where_clause_array);
            if (!empty($notes_array)){
                foreach ($notes_array as $key => $value) {
                    $this->db->set($key, $value, FALSE);
                }
            }
            $status =  $this->db->update('receipt',$purchase_quote_data);
            log_message('debug','update statement ='.$this->db->last_query());
            return $status;
	}
        
        function exists($id)
	{
		$this->db->from('receipt');
		$this->db->where('id',$id);
		$query = $this->db->get();

		$ifExists = $query->num_rows()==1;

		return ($ifExists);
	}
        function getById($id,$column_array,$isarray=false){
            
            if (empty($column_array)){
                $this->db->select('*');
            }
            else {
                $this->db->select($column_array);
            }
            $this->db->where('id',$id);
            
            $query = $this->db->get('receipt_grid');
            if ($query->num_rows() > 0)
            {
                if ($isarray){
                    return $query->row_array(); 
                }
                else {
                   return $query->row();  
                }
                
                
            } 
        }
        
        	
       
        
        function getAll($csv = false,$whereClause=null,$columns_array=null,$order_limit_clause=array(),$like_fields_array=null,$in_where_clause_array=null,$or_where_clause_array=null){
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
            if (!empty($columns_array)){
                $this->db->select($columns_array);
            }
            else {
            $this->db->select('*');
            }
           
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
            $query = $this->db->get('receipt_grid');
            log_message('debug',$this->db->last_query());
            if ($csv){
                return $this->dbutil->csv_from_result($query);
            }
            return $query->result_array();
        }
        
        
        function totalNoOfRows ($where_clause_array=null,$like_fields_array=null,$or_where_clause_array=null,$in_where_clause_array=null,$not_in_where_clause_array=null) {
            parent::totalNoOfRowsForResults ('receipt',$where_clause_array,$like_fields_array,$or_where_clause_array,$in_where_clause_array,$not_in_where_clause_array);
            
        }
        
        //business logic ..use this to create quote
        
        public function createReceipt($purchase_quote_data){
            $this->db->trans_start();
            $this->insert($purchase_quote_data);
            log_message('debug','insert statement ='.$this->db->last_query());
            $id = $this->db->insert_id();
            
            $where_clause = array('id'=>$id);
            $this->update($where_clause, array('reference' => 10000000 + $id));
            log_message('debug','update statement ='.$this->db->last_query());
            $this->db->trans_complete();
            return $id;
        }
       
}

?>