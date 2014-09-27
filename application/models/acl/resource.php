<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of resource
 *
 * @author abhijit
 */
class Resource extends CI_Model {	
	

        function save($resource_data,$id=false,$default_permission='all'){
            $success=false;
            //Run these queries as a transaction, we want to make sure we do all or nothing
            if (!empty($resource_data)){
                $this->db->trans_start();
            
                if (!$id or !$this->exists($id)){
                    $success = $this->db->insert('acl_resources',$resource_data);
                    $res_id= $this->db->insert_id();
                    //only for insert . Create default permission role resource mapping
                    $default_role_name = 'Administrator';
                    $default_permission = $default_permission;
                    $this->db->select('id');
                    $this->db->where('role_name',$default_role_name);
                    $queryRole = $this->db->get('acl_roles');
                    $resultRole = $queryRole->row();
                    $roleId= $resultRole->id;

                    $this->db->select('id');
                    $this->db->where('permission',$default_permission);
                    $query = $this->db->get('acl_permissions');
                    $result = $query->row();
                    $permId= $result->id;

                    $default_permission_role_mapping = array('role_id'=>$roleId,'role_name'=>$default_role_name,
                        'resource_id'=>$res_id,
                        'resource_name'=>$resource_data['resource'],
                        'permission_id'=>$permId,
                        'permission_name'=>$default_permission,
                        'isAllowed'=>1);
                    $this->db->insert('acl_role_resource_permission_mapping',$default_permission_role_mapping);
                    
                    }
                else{

                    $this->db->where('id', $id);
                    $success = $this->db->update('acl_resources',$resource_data);
                    
                }
            }
            
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE)
            {
                //echo $this->db->_error_message();
               log_message('Resource Creation Failed '.$this->db->_error_message() );
               throw new Exception('Resource Creation Failed' );
            }
            else {
                log_message('debug','Resource Successfully Created');
            }
            return $success;
        }
        
        function exists($id)
	{
		$this->db->from('acl_resources');	
		//$this->db->join('people', 'people.person_id = employees.person_id');
		$this->db->where('id',$id);
		$query = $this->db->get();
                return ($query->num_rows()==1);
	}
        function existsByResourceName($name)
	{
		$this->db->from('acl_resources');	
		//$this->db->join('people', 'people.person_id = employees.person_id');
		$this->db->where('resource',$name);
		$query = $this->db->get();
                return ($query->num_rows()==1);
	}
        
        
        function getAll($csv = false,$whereClause=null,$order_limit_clause=array(),$like_fields_array=null,$in_where_clause_array=null,$or_where_clause_array=null){
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
            $this->db->select('*');
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
            $query = $this->db->get('acl_resources_grid');
            log_message('debug',$this->db->last_query());
            if ($csv){
                return $this->dbutil->csv_from_result($query);
            }
            return $query->result_array();
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
            
            $this->db->from('acl_resources_grid');
            return $this->db->count_all_results() ;
            
            
        }
        
	
        function getParent($child){
            $sql = "SELECT parent.resource parentresource FROM 
                 ".$this->db->dbprefix."acl_resources child left join ".$this->db->dbprefix."acl_resources parent on 
                child.parent_id=parent.id where child.resource =?";
            $query = $this->db->query($sql,$child);
            
            if($query->num_rows()>0){
                $row = $query->row();
                return $row->parentresource;
            }
            
            
        }
        
        function getId($resource_name){
            $this->db->select('id');
            $this->db->where('resource',$resource_name);
            $query = $this->db->get('acl_resources');
            if ($query->num_rows() > 0)
            {
                $row = $query->row_array(); 

                return  $row['id'];
            
            } 
            return null;
        }
        
        function getByResourceName($resource_name){
            $this->db->select('*');
            $this->db->where('resource',$resource_name);
            $query = $this->db->get('acl_resources');
            if ($query->num_rows() > 0)
            {
                
                return $query->row_array();
            
            } 
            return null;
        }
        
        function getResourceName($id){
            $this->db->select('resource');
            $this->db->where('id',$id);
            $query = $this->db->get('acl_resources');
            if ($query->num_rows() > 0)
            {
                $row = $query->row_array(); 

                return  $row['resource'];
            
            } 
            return null;
        }
        
        function getResourceTypeName($id){
            $this->db->select('name');
            $this->db->where('id',$id);
            $query = $this->db->get('acl_resource_type');
            if ($query->num_rows() > 0)
            {
                $row = $query->row_array(); 

                return  $row['name'];
            
            } 
            return null;
        }
        
        
        function getResourceTypeId($name){
            $this->db->select('id');
            $this->db->where('name',$name);
            $query = $this->db->get('acl_resource_type');
            if ($query->num_rows() > 0)
            {
                $row = $query->row_array(); 

                return  $row['id'];
            
            } 
            return null;
        }
	 function getAllResourceTypes (){
             $this->db->select('*');
             $query =$this->db->get('acl_resource_type');
             return $query->result_array();
         }
}


?>
