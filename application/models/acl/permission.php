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
class Permission extends Base_model {	
	
//        function insert($role_data,$parents = array())
//	{
//            //$this->db->insert('invoice',$invoice_data);
//            //return $this->db->insert('roles',$role_data);
//            $this->db->trans_start();
//            $this->db->insert('roles',$role_data);
//            $id = $this->db->insert_id();
//            if (!empty($parents)){
//                foreach ($parents as $parent){
//                    $parent_name = $this->getName($parent);
//                    if (!empty($parent_name)){
//                        $role_inherit_data = array('role_id'=>$id,'role_name'=>$role_data['role_name'],
//                            'parent_role_id'=>$parent,'parent_role_name'=>$parent_name);
//                        $this->db->insert('role_inheritance_mapping',$role_inherit_data);
//                    }
//                }
//            }
//            $this->db->trans_complete();
//            if ($this->db->trans_status() === FALSE)
//            {
//                //echo $this->db->_error_message();
//                die( 'Shipping  Failed.Please check log ');
//            }
//            else {
//                $success = true;
//            }
//	}
        
        function save($permission_data,$id=false){
            $success=false;
            //Run these queries as a transaction, we want to make sure we do all or nothing
            if (!empty($permission_data)){
                try {
                    if (!$id or !$this->permissionExistsById($id)){
                        $success = $this->db->insert('acl_role_resource_permission_mapping',$permission_data);
                    }
                    else{

                        $this->db->where('id', $id);
                        $success = $this->db->update('acl_role_resource_permission_mapping',$permission_data);

                    }
                    if ($success){
                        log_message('debug','Permission Suceesfully Created');
                    }
                }
                catch (Exception $e){
                    log_message('Permission Creation Failed '.$this->db->_error_message() );
                    throw new Exception('RePermissionsource Creation Failed' );
                }
                
            }
            
            
            return $success;
        }
        
        function getRoleResourcePermissionMapping($where_clause=null){
            $this->db->select('*');
            if (!empty($where_clause)){
                $this->db->where($where_clause);
            }
            $query = $this->db->get('acl_role_resource_permission_mapping');
            return $query->result_array();
        }
        
	
        function getParent($child){
            $sql = "SELECT parent.resource parentresource FROM " .$this->db->dbprefix
               ."acl_resources child left join ".$this->db->dbprefix."acl_resources parent on 
                child.parent_id=parent.id where child.resource =?";
            $query = $this->db->query($sql,$child);
            
            if($query->num_rows()>0){
                $row = $query->row();
                return $row->parentresource;
            }
            
            
        }
        
        function getId($role_name){
            $this->db->select('id');
            $this->db->where('role_name',$role_name);
            $query = $this->db->get('roles');
            if ($query->num_rows() > 0)
            {
                $row = $query->row_array(); 

                return  $row['id'];
            
            } 
            return null;
        }
        
        function getPermissionName($id){
            $this->db->select('permission');
            $this->db->where('id',$id);
            $query = $this->db->get('acl_permissions');
            if ($query->num_rows() > 0)
            {
                $row = $query->row_array(); 

                return  $row['permission'];
            
            } 
            return null;
        }
        
//        function getMenu($userName){
//            $menu ="-menu";
//            $sql = "SELECT resource.id,resource.resource, resource.ui_display_name, resource.relative_path_link, user.username
//                FROM ".$this->db->dbprefix."acl_resources AS resource
//                LEFT JOIN ".$this->db->dbprefix."acl_role_resource_permission_mapping AS permission ON resource.resource = permission.resource_name
//                LEFT JOIN ".$this->db->dbprefix."acl_users AS user ON permission.role_id = user.role_id
//                WHERE user.username = ?
//                AND permission.isAllowed =1
//                AND resource.resource LIKE '%".$this->db->escape_like_str($menu)."%' order by relative_order_in_category";
//            $query = $this->db->query($sql,array($userName));
//            log_message('debug', $this->db->last_query());
//            //echo $this->db->last_query();
//            return $query->result();
//        }
        
        function getMenu($userName){
            $menu ="-menu";
            $sql = "SELECT id,resource, ui_display_name,relative_path_link, relative_order_in_category,username,isAllowed,0 as relative_order_parent
                FROM ".$this->db->dbprefix."user_role_permission_mapping  WHERE username = ?  AND isAllowed is not null
                AND resource LIKE '%".$this->db->escape_like_str($menu)."%' UNION ".
                    "SELECT id,resource, ui_display_name,relative_path_link, relative_order_in_category,username,isAllowed,relative_order_parent
                FROM ".$this->db->dbprefix."user_parentrole_permission_mapping  WHERE username = ?  AND isAllowed is not null
                AND resource LIKE '%-menu%' ".
                " order by `relative_order_parent` desc ";
            $query = $this->db->query($sql,array($userName,$userName));
            //log_message('debug', $this->db->last_query());
            //echo $this->db->last_query();
            return $query->result_array();
        }
        
//        function getAdminMenu($userName){
//            $menu ="-adminmenu";
//            $sql = "SELECT resource.id,resource.resource, resource.ui_display_name, resource.relative_path_link, user.username
//                FROM ".$this->db->dbprefix."acl_resources AS resource
//                LEFT JOIN ".$this->db->dbprefix."acl_role_resource_permission_mapping AS permission ON resource.resource = permission.resource_name
//                LEFT JOIN ".$this->db->dbprefix."acl_users AS user ON permission.role_id = user.role_id
//                WHERE user.username = ?
//                AND permission.isAllowed =1
//                AND resource.resource LIKE '%".$this->db->escape_like_str($menu)."%' order by relative_order_in_category";
//            $query = $this->db->query($sql,array($userName));
//            //echo $this->db->last_query();
//            return $query->result();
//        }
        function getAdminMenu($userName){
            $menu ="-adminmenu";
            $sql = "SELECT id,resource, ui_display_name,relative_path_link, relative_order_in_category,username,isAllowed,0 as relative_order_parent
                FROM ".$this->db->dbprefix."user_role_permission_mapping  WHERE username = ? AND isAllowed is not null
                AND resource LIKE '%".$this->db->escape_like_str($menu)."%' UNION ".
                    "SELECT id,resource, ui_display_name,relative_path_link, relative_order_in_category,username,isAllowed,relative_order_parent
                FROM ".$this->db->dbprefix."user_parentrole_permission_mapping  WHERE username = ?  AND isAllowed is not null
                AND resource LIKE '%".$this->db->escape_like_str($menu)."%' ".
                " order by `relative_order_parent` desc ";
            $query = $this->db->query($sql,array($userName,$userName));
             //log_message('debug', 'admin menu ' .$this->db->last_query());
            return $query->result_array();
        }
        function getSubMenu($userName,$parent_resource_id){
            $menu ="-submenu";
            $sql = "SELECT id,resource, ui_display_name,relative_path_link, relative_order_in_category,username,isAllowed,0 as relative_order_parent
                FROM ".$this->db->dbprefix."user_role_permission_mapping  WHERE username = ?  AND parent_id = ? AND isAllowed is not null
                AND resource LIKE '%".$this->db->escape_like_str($menu)."%' UNION ".
                    "SELECT id,resource, ui_display_name,relative_path_link,relative_order_in_category, username,isAllowed,relative_order_parent
                FROM ".$this->db->dbprefix."user_parentrole_permission_mapping  WHERE username = ?  AND parent_id = ? AND isAllowed is not null
                AND resource LIKE '%".$this->db->escape_like_str($menu)."%' "." order by `relative_order_parent` desc ";
            $query = $this->db->query($sql,array($userName,$parent_resource_id,$userName,$parent_resource_id));
            //log_message('debug', 'sub menu'.$this->db->last_query());
            
            return $query->result_array();
        }
        
        function getAllPermissionTypes (){
             $this->db->select('*');
             $query =$this->db->get('acl_permissions');
             return $query->result_array();
        }
        
        function getAllPermissionMapping($csv = false,$whereClause=null,$order_limit_clause=array(),$like_fields_array=null,$or_where_clause_array=null){
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
            $query = $this->db->get('acl_role_resource_permission_mapping');
            
            if ($csv){
                return $this->dbutil->csv_from_result($query);
            }
            return $query->result_array();
        }
        
        
        function totalNoOfRowsPermissionMapping () {
            
            $this->db->from('acl_role_resource_permission_mapping');
            return $this->db->count_all_results() ;
            
            
        }
        
        function permissionExists($roleid,$resourceid,$permissionid) {
            
            $this->db->from('acl_role_resource_permission_mapping');
            $this->db->where('role_id',$roleid);
            $this->db->where('resource_id',$resourceid);
            $this->db->where('permission_id',$permissionid);
            
            $query = $this->db->get();
            return ($query->num_rows()==1);
            
        }
        
        function permissionExistsById($id) {
            
            $this->db->from('acl_role_resource_permission_mapping');
            $this->db->where('id',$id);
            
            
            $query = $this->db->get();
            return ($query->num_rows()==1);
            
        }
        
        function anyPermissionExistsForResource($resource) {
            
            $this->db->from('acl_role_resource_permission_mapping');
            
            $this->db->where('resource_name',$resource);
            
            $query = $this->db->get();
            return ($query->num_rows()>0);
            
        }
        
        
        
	
}


?>
