<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of user
 *
 * @author abhijit
 */
class User extends Person{
	/*
	Determines if a given person_id is an employee
	*/
       protected $CI;
       function __construct()
	{
		parent::__construct();
                $this->CI = &get_instance();
                $this->CI->load->model('acl/Role','Role');
	}
        
	function exists($person_id)
	{
		$this->db->from('acl_users');	
		//$this->db->join('people', 'people.person_id = employees.person_id');
		$this->db->where('person_id',$person_id);
		$query = $this->db->get();
                return ($query->num_rows()==1);
	}	
	
	/*
	Returns all the employees
	*/
//	function get_all($limit=10000, $offset=0)
//	{
//		$this->db->from('acl_users');
//		$this->db->where('deleted',0);		
//		$this->db->join('people','employees.person_id=people.person_id');			
//		$this->db->order_by("last_name", "asc");
//		$this->db->limit($limit);
//		$this->db->offset($offset);
//		return $this->db->get();		
//	}
	
	function count_all()
	{
		$this->db->from('acl_users');
		$this->db->where('deleted',0);
		return $this->db->count_all_results();
	}
        
        function totalNoOfRows ($where_clause_array=null,$or_where_clause_array=null,$getAll=false) {
            
            if (!empty($where_clause_array)){
                $this->db->where($where_clause_array);
            }
            if (!$getAll){
                $this->db->where('deleted',0);
            }
            
            
            if (!empty($or_where_clause_array)){
                $this->db->or_where($or_where_clause_array);
            }
            
            $this->db->from('acl_users');
            return $this->db->count_all_results() ;
            
            
        }
	
	/*
	Gets information about a particular employee
	*/
	function get_info($employee_id)
	{
		$this->db->from('acl_users');	
		$this->db->join('people', 'people.person_id = acl_users.person_id');
		$this->db->where('acl_users.person_id',$employee_id);
		$query = $this->db->get();
		
		if($query->num_rows()==1)
		{
			return $query->row();
		}
		
	}
	
	/*
	Gets information about multiple employees
	*/
	function get_multiple_info($employee_ids)
	{
		$this->db->from('employees');
		$this->db->join('people', 'people.person_id = employees.person_id');		
		$this->db->where_in('employees.person_id',$employee_ids);
		$this->db->order_by("last_name", "asc");
		return $this->db->get();		
	}
       
	/*
	Inserts or updates an user
	*/
	function save(&$person_data, &$user_data,$user_id=false)
	{
		$success=false;
		
		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();              
		if(parent::save($person_data,$user_id))
		{
			
                        if (!$user_id or !$this->exists($user_id))
			{
				
                                $user_data['person_id'] = $user_id = $person_data['person_id'];
                                $success = $this->db->insert('acl_users',$user_data);
			}
			else
			{       
//                            if (!empty($user_data)){
//                                $user_data = array();
//                            }
//                            $user_data['role_id']=$role_id;
                            if (!empty($user_data)){

                                $this->db->where('person_id', $user_id);
                                $success = $this->db->update('acl_users',$user_data);
                            }
			}
		}
                
		
		$this->db->trans_complete();		
		return $success;
	}
	
	/*
	Deletes one employee
	*/
	function delete($employee_id)
	{
		$success=false;
		
		//Don't let employee delete their self
		if($employee_id==$this->g()->person_id)
			return false;
		
		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();
		
		//Delete permissions
		if($this->db->delete('permissions', array('person_id' => $employee_id)))
		{	
			$this->db->where('person_id', $employee_id);
			$success = $this->db->update('employees', array('deleted' => 1));
		}
		$this->db->trans_complete();		
		return $success;
	}
	
	/*
	Deletes a list of employees
	*/
	function delete_list($employee_ids)
	{
		$success=false;
		
		//Don't let employee delete their self
		if(in_array($this->get_logged_in_employee_info()->person_id,$employee_ids))
			return false;

		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();

		$this->db->where_in('person_id',$employee_ids);
		//Delete permissions
		if ($this->db->delete('permissions'))
		{
			//delete from employee table
			$this->db->where_in('person_id',$employee_ids);
			$success = $this->db->update('employees', array('deleted' => 1));
		}
		$this->db->trans_complete();		
		return $success;
 	}
	
	
	
	/*
	Attempts to login employee and set session. Returns boolean based on outcome.
	*/
	function login($username, $password)
	{
		$query = $this->db->get_where('acl_users', array('username' => $username,'password'=>md5($password), 'deleted'=>0), 1);
		if ($query->num_rows() ==1)
		{
			$row=$query->row();
			$this->session->set_userdata('person_id', $row->person_id);
			return true;
		}
		return false;
	}
	
	/*
	Logs out a user by destorying all session data and redirect to login
	*/
	function logout()
	{
		$this->session->sess_destroy();
		redirect('login');
	}
	
	/*
	Determins if a employee is logged in
	*/
	function is_logged_in()
	{
		return $this->session->userdata('person_id')!=false;
	}
	
	/*
	Gets information about the currently logged in employee.
	*/
	function get_logged_in_employee_info()
	{
		if($this->is_logged_in())
		{
			return $this->get_info($this->session->userdata('person_id'));
		}
		
		return false;
	}
	
	/*
	Determins whether the employee specified employee has access the specific module.
	*/
	function getUser($username){
            $this->db->where('username',$username);
            $query = $this->db->get('acl_users');
            return $query->row();
            
        }
        
         function getAllUsersView($csv =false,$whereClause=null,$columns_array = null,$order_limit_clause=array(),$like_fields_array=null,$in_where_clause_array=null,$or_where_clause_array=null,$not_in_where_clause_array=null){
            if (empty($order_limit_clause['orderBy'])) {
                $order_limit_clause['orderBy'] ='person_id';
            }
            return parent::getResults('user_details_view', $csv, $whereClause, $columns_array, $order_limit_clause, $like_fields_array, $in_where_clause_array, $or_where_clause_array,$not_in_where_clause_array);
        }
        
//        function getAllUsersView($csv = false,$whereClause=null,$order_limit_clause=array(),$like_fields_array=null,$or_where_clause_array=null){
//            $orderBy = 'person_id';
//            $orderDir= 'desc';
//            $startLimit = 0;
//            $limit = 1000;
//            
//            if (!empty($order_limit_clause['orderBy'])){
//                $orderBy = $order_limit_clause['orderBy'];
//            }
//            if (!empty($order_limit_clause['orderDir'])){
//                $orderDir = $order_limit_clause['orderDir'];
//            }
//            if (!empty($order_limit_clause['startLimit'])){
//                $startLimit = $order_limit_clause['startLimit'];
//            }
//            if (!empty($order_limit_clause['limit'])){
//                $limit = $order_limit_clause['limit'];
//            }
//        
//            $this->load->dbutil();  
//            $this->db->select('*');
//            if (!empty($whereClause)){
//                $this->db->where($whereClause);
//            }
//            
//            if (!empty($or_where_clause_array)){
//                $this->db->or_where($or_where_clause_array);
//            }
//            
//            if (!empty($like_fields_array)){
//                $this->db->like($like_fields_array);
//            }
//            $this->db->order_by($orderBy,$orderDir);
//            $this->db->limit($limit,$startLimit);
//            $query = $this->db->get('user_details_view');
//            
//            if ($csv){
//                return $this->dbutil->csv_from_result($query);
//            }
//            //echo $this->db->last_query();
//            return $query->result_array();
//        }
        
        function totalNoOfRowsUsersView ($where_clause_array=null,$like_fields_array=null,$or_where_clause_array=null) {
            
            if (!empty($where_clause_array)){
                $this->db->where($where_clause_array);
            }
            
            if (!empty($or_where_clause_array)){
                $this->db->or_where($or_where_clause_array);
            }
            
            if (!empty($like_fields_array)){
                $this->db->like($like_fields_array);
            }
            
            $this->db->from('user_details_view');
            return $this->db->count_all_results() ;
            
            
        }
        
        
        function getAllUsersByRole($in_where_clause_array=null,$not_in_where_clause_array=null){
            $this->db->select('*');
            if (!empty($in_where_clause_array)){
                if (!empty($in_where_clause_array['field_name']) && !empty($in_where_clause_array['value_array'])){
                    $this->db->where_in($in_where_clause_array['field_name'],$in_where_clause_array['value_array']);
                }
            }
            
            if (!empty($not_in_where_clause_array)){
                if (!empty($not_in_where_clause_array['field_name']) && !empty($not_in_where_clause_array['value_array'])){
                    $this->db->where_not_in($not_in_where_clause_array['field_name'],$not_in_where_clause_array['value_array']);
                }
            }
            $query = $this->db->get('user_details_view');
            return $query->result_array();
        }
        
        function getUserInfo ($person_id,$isarray=false){
            if (!empty($person_id)){
                $this->db->select('*');
                $this->db->where('person_id',$person_id);
                $query = $this->db->get('user_details_view');
                if ($isarray){
                    return $query->row_array();
                }
                else {
                    return $query->row();
                }
            }
       
        }
       function getAllEligibleOwners($entity){
            if (!empty($entity)){
                $whereClause['resource'] = $entity;
                $in_where_clause_array = array('field_name'=>'permission_name','value_array'=>array('all','manage'));
                return parent::getResults('user_role_permission_mapping', false, $whereClause, array('username','person_id'), null, null, $in_where_clause_array);
            }
        }
        function getAllEligibleReceivers($entity){
            if (!empty($entity)){
                $whereClause['resource'] = $entity;
                $in_where_clause_array = array('field_name'=>'permission_name','value_array'=>array('all','manage','receive'));
                return parent::getResults('user_role_permission_mapping', false, $whereClause, array('username','person_id'), null, null, $in_where_clause_array);
            }
        }
        function getAllEligibleApprovers($entity){
            if (!empty($entity)){
                $whereClause['resource'] = $entity;
                $in_where_clause_array = array('field_name'=>'permission_name','value_array'=>array('all','admin','approve'));
                return parent::getResults('user_role_permission_mapping', false, $whereClause, array('username','person_id'), null, null, $in_where_clause_array);
            }
        }

}
?>
