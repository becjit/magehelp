<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of base_model
 *
 * @author abhijit
 */
class Base_model extends CI_Model {
    
    
    //put your code here
    
    function getResults($table_name,$csv = false,$whereClause=null,$columns_array = null,$order_limit_clause=array(),$like_fields_array=null,$in_where_clause_array=null,$or_where_clause_array=null,$not_in_where_clause_array=null){
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
                $this->db->select($columns_array,FALSE);
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
                if (!empty($in_where_clause_array['value_array'])){
                $this->db->where_in($in_where_clause_array['field_name'],$in_where_clause_array['value_array']);
                }
            }
            
            if (!empty($not_in_where_clause_array)){
                //added caution
                if (!empty($not_in_where_clause_array['value_array'])){
                    $this->db->where_not_in($not_in_where_clause_array['field_name'],$not_in_where_clause_array['value_array']);
                }
                
            }
            
            if (!empty($like_fields_array)){
                $this->db->like($like_fields_array);
            }
            $this->db->order_by($orderBy,$orderDir);
            $this->db->limit($limit,$startLimit);
            $query = $this->db->get($table_name);
            log_message('debug',$this->db->last_query());
            if ($csv){
                return $this->dbutil->csv_from_result($query);
            }
            return $query->result_array();
        }
        function totalNoOfRowsForResults ($table_name,$where_clause_array=null,$like_fields_array=null,$or_where_clause_array=null,$in_where_clause_array=null,$not_in_where_clause_array=null) {
            if (!empty($where_clause_array)){
                $this->db->where($where_clause_array);
            }
            
            if (!empty($or_where_clause_array)){
                $this->db->or_where($or_where_clause_array);
            }
            
            if (!empty($like_fields_array)){
                $this->db->like($like_fields_array);
            }
            
            if (!empty($in_where_clause_array)){
                if (!empty($in_where_clause_array['value_array'])){
                $this->db->where_in($in_where_clause_array['field_name'],$in_where_clause_array['value_array']);
                }
            }
            
            if (!empty($not_in_where_clause_array)){
                //added caution
                if (!empty($not_in_where_clause_array['value_array'])){
                    $this->db->where_not_in($not_in_where_clause_array['field_name'],$not_in_where_clause_array['value_array']);
                }
                
            }
            $this->db->from($table_name);
            
            return $this->db->count_all_results() ;
            
            
        }
        function updateInternal($table_name,$where_clause_array,$update_data_array,$set_data_array,$timestamp_needed=true)
	{
            $CI = & get_instance();
            $user = $CI->User->get_logged_in_employee_info();
            if ($timestamp_needed){
                $last_updated_by = $user->person_id;
                $update_data_array['last_updated_at']=date('Y-m-d H:i:s');
                $update_data_array['last_updated_by'] = $last_updated_by;
            }
            
            $this->db->where($where_clause_array);
            if (!empty($set_data_array)){
                foreach ($set_data_array as $key => $value) {
                    $this->db->set($key, $value, FALSE);
                }
            }
            if (!empty($update_data_array)){
               $status =  $this->db->update($table_name,$update_data_array); 
            }
            else {
                $status =  $this->db->update($table_name);
            }
            
            log_message('debug','update statement ='.$this->db->last_query());
            return $status;
	}
        function insertInternal($table_name,$insert_data_array,$set_data_array){
            $CI = & get_instance();
            $user = $CI->User->get_logged_in_employee_info();
            $last_updated_by = $user->person_id;
            $insert_data_array['created_at']=date('Y-m-d H:i:s');
            $insert_data_array['last_updated_by'] = $last_updated_by;
            if (!empty($set_data_array)){
                foreach ($set_data_array as $key => $value) {
                    $this->db->set($key, $value, FALSE);
                }
            }
            $status =  $this->db->insert($table_name,$insert_data_array);
            log_message('debug','insert statement ='.$this->db->last_query());
            return $status;
        }
        
        function getUniqueRow($table_name,$where_clause,$column_array,$isarray=false){
            
            
            if (empty($column_array)){
                $this->db->select('*');
            }
            else {
                $this->db->select($column_array);
            }
           if (!empty($where_clause)){
                $this->db->where($where_clause);
            }
            
            $query = $this->db->get($table_name);
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
        
        function getSumTotal($table_name,$column,$whereClause=null,
                $in_where_clause_array=null,$or_where_clause_array=null){
            
            $this->db->select_sum($column,'total');
            if (!empty($whereClause)){
                $this->db->where($whereClause);
            }
            if (!empty($or_where_clause_array)){
                $this->db->or_where($or_where_clause_array);
            }
            
            if (!empty($in_where_clause_array)){
                $this->db->where_in($in_where_clause_array['field_name'],$in_where_clause_array['value_array']);
            }
            $query =  $this->db->get($table_name);
            log_message('debug',$this->db->last_query());
            return $query->row()->total;
        }
        protected function getByFieldUnique($table_name,$cond_column_array,$result_column_array,$isarray=false){
            
            if (empty($cond_column_array)){
                log_message('error',"Condition Column Can Not Be Empty" );
                throw new Exception ("Condition Column Can Not Be Empty");
                
            }
            else {
                if (!empty($result_column_array)){
                    $this->db->select($result_column_array);
                }
                else {
                    $this->db->select('*');
                }
                foreach ($cond_column_array as $key => $value) {
                    $this->db->where($key,$value);
                }
                $query = $this->db->get($table_name);
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
        }
        
        function existInternal($table,$id)
	{
		$this->db->from($table);
		$this->db->where('id',$id);
		$query = $this->db->get();

		$ifExists = $query->num_rows()==1;

		return ($ifExists);
	}
}

?>
