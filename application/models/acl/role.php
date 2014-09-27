<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of role
 *
 * @author abhijit
 */

class Role extends CI_Model {	
	
        function insert($role_data,$parents = array())
	{
            //$this->db->insert('invoice',$invoice_data);
            //return $this->db->insert('roles',$role_data);
            $this->db->trans_start();
            $this->db->insert('acl_roles',$role_data);
            $id = $this->db->insert_id();
            if (!empty($parents)){
                foreach ($parents as $parent){
                    $parent_name = $this->getName($parent);
                    if (!empty($parent_name)){
                        $role_inherit_data = array('role_id'=>$id,'role_name'=>$role_data['role_name'],
                            'parent_role_id'=>$parent,'parent_role_name'=>$parent_name);
                        $this->db->insert('acl_role_inheritance_mapping',$role_inherit_data);
                    }
                }
            }
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE)
            {
                //echo $this->db->_error_message();
                die( 'Shipping  Failed.Please check log ');
            }
            else {
                $success = true;
            }
	}
        
        
        function totalNoOfRoles () {
            
            $this->db->from('acl_roles');
            return $this->db->count_all_results() ;
            
            
        }
        
        function getAllParentsMapping($csv = false,$whereClause=null,$order_limit_clause=array(),$like_fields_array=null,$or_where_clause_array=null){
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
            
            if (!empty($like_fields_array)){
                $this->db->like($like_fields_array);
            }
            $this->db->order_by($orderBy,$orderDir);
            $this->db->limit($limit,$startLimit);
            $query = $this->db->get('acl_role_inheritance_mapping');
            
            if ($csv){
                return $this->dbutil->csv_from_result($query);
            }
            return $query->result_array();
        }
        
        function totalNoOfRowsInParentMapping ($where_clause_array=null,$like_fields_array=null,$or_where_clause_array=null) {
            if (!empty($where_clause_array)){
                $this->db->where($where_clause_array);
            }
            
            if (!empty($or_where_clause_array)){
                $this->db->or_where($or_where_clause_array);
            }
            
            if (!empty($like_fields_array)){
                $this->db->like($like_fields_array);
            }
            
            $this->db->from('acl_role_inheritance_mapping');
            return $this->db->count_all_results() ;
            
            
        }
        function getAll(){
            $this->db->select('*');
            $query = $this->db->get('acl_roles');
            return $query->result_array();
        }
        
	
        function getAllParents($id){
            $this->db->select('parent_role_name');
            $this->db->where('role_id',$id);
            $this->db->order_by('relative_order_parent','asc');
            $query = $this->db->get('acl_role_inheritance_mapping');
            //log_message('debug','parent roles '.$this->db->last_query());
            return $query->result_array();
        }
        
        function getId($role_name){
            $this->db->select('id');
            $this->db->where('role_name',$role_name);
            $query = $this->db->get('acl_roles');
            if ($query->num_rows() > 0)
            {
                $row = $query->row_array(); 

                return  $row['id'];
            
            } 
            return null;
        }
        
        function getName($id){
            $this->db->select('role_name');
            $this->db->where('id',$id);
            $query = $this->db->get('acl_roles');
            if ($query->num_rows() > 0)
            {
                $row = $query->row_array(); 

                return  $row['role_name'];
            
            } 
            return null;
        }
        
        function parentExists($roleid,$parentroleid) {
            
            $this->db->from('acl_role_inheritance_mapping');
            $this->db->where('role_id',$roleid);
            $this->db->where('parent_role_id',$parentroleid);
            
            
            $query = $this->db->get();
            return ($query->num_rows()==1);
            
        }
        
        function parentExistsById($id) {
            
            $this->db->from('acl_role_inheritance_mapping');
            $this->db->where('id',$id);
            
            
            $query = $this->db->get();
            return ($query->num_rows()==1);
            
        }
        
        function roleExists($id) {
            
            $this->db->from('acl_roles');
            $this->db->where('id',$id);
            
            
            $query = $this->db->get();
            return ($query->num_rows()==1);
            
        }
        
        function saveParent($parent_data,$id=false){
            $success=false;
            //Run these queries as a transaction, we want to make sure we do all or nothing
            if (!empty($parent_data)){
                try {
                    if (!$id or !$this->parentExistsById($id)){
                        $success = $this->db->insert('acl_role_inheritance_mapping',$parent_data);
                    }
                    else{

                        $this->db->where('id', $id);
                        $success = $this->db->update('acl_role_inheritance_mapping',$parent_data);

                    }
                    if ($success){
                        log_message('debug','Parent Role Suceesfully Modified');
                    }
                }
                catch (Exception $e){
                    log_message('Parent Role Creation Failed '.$this->db->_error_message() );
                    throw new Exception('Parent Role Creation Failed' );
                }
                
            }
            
            
            return $success;
        }
        
        function save($role_data,$id=false){
            $success=false;
            //Run these queries as a transaction, we want to make sure we do all or nothing
            if (!empty($role_data)){
                try {
                    if (!$id or !$this->roleExists($id)){
                        $success = $this->db->insert('acl_roles',$role_data);
                    }
                    else{

                        $this->db->where('id', $id);
                        $success = $this->db->update('acl_roles',$role_data);

                    }
                    if ($success){
                        log_message('debug','Role Suceesfully Modified');
                    }
                }
                catch (Exception $e){
                    log_message('Parent Role Creation Failed '.$this->db->_error_message() );
                    throw new Exception('Parent Role Creation Failed' );
                }
                
            }
            
            
            return $success;
        }
	
}

?>
