<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of users
 *
 * @author abhijit
 */
require_once ("secure_area.php");
class Acls extends Secure_area {
    function __construct()
	{
		parent::__construct('acls','adminmenu');
                $this->load->model('acl/User','Userview');
                //$this->load->model('acl/Resource','Resource');
                $this->load->model('acl/Role','Role');
                $this->load->model('acl/Permission','Permission');
	}
	
	function index(){
                 
                $data['num_perm']= $this->Permission->totalNoOfRowsPermissionMapping();
                $data['num_res']= $this->Resource->totalNoOfRows();
                $data['num_role']= $this->Role->totalNoOfRoles();
                $data['num_users']= $this->User->count_all();
		$this->load->view("acl/dashboard",$data);
	}
        
        function loadUser(){
		$this->load->view("acl/user_grid");
	}
        
        function loadPermission (){
            $this->load->view("acl/permission_grid");
        }
        
        function loadResource (){
            $data['resourceTypeOptions'] = populateResourceTypesCommon();
            $data['parentOptions'] = populateParentResourcesEditCommon();
            $data['permissionTypeOptions']= populatePermissionTypesEditCommon(true);
            $this->load->view("acl/resource_grid",$data);
        }
        
        function loadRole (){
            $roleOptions = null;
            $roles = $this->Role->getAll();
            
            foreach($roles as $role) { 
                
                $name=$role["role_name"]; 
                $id=$role["id"]; 
                if (!empty($name)){
                   $roleOptions.="<OPTION VALUE=\"$id\">".$name;  
                }
            } 
            $data['roleOptions'] = $roleOptions;
            $this->load->view("acl/role_grid",$data);
        }

        
        function populateUser(){
          
           $searchOn = strip($_REQUEST['_search']);
           $page = $_REQUEST['page'];
           $limit = $_REQUEST['rows'];
           $sidx = $_REQUEST['sidx'];
           $sord = $_REQUEST['sord'];
            
            
           $usersdata = array();
           $count = $this->Userview->totalNoOfRowsUsersView();
           if( $count > 0 && $limit > 0) { 
                $total_pages = ceil($count/$limit); 
           } else { 
               $total_pages = 0; 
           } 
           if ($page > $total_pages) $page=$total_pages;

           $start = $limit*$page - $limit;
 
            // if for some reasons start position is negative set it to 0 
            // typical case is that the user type 0 for the requested page 
           if($start <0) $start = 0; 
           $clauses = array('orderBy'=>$sidx,'orderDir'=>$sord,'startLimit'=>$start,'limit'=>$limit);
           $data['total'] = $total_pages;
           $data['page'] = $page;
           $data['records'] = $count; 
          
           if($searchOn=='true') {
                
                $filters = json_decode($_REQUEST['filters'],true);
                $groupOp = $filters['groupOp'];
                $rules = $filters['rules'];
                $like_condition = array();
                foreach ($rules as $rule){
                    $field = $rule['field'];
                    $op= $rule['op'];
                    $input = $rule['data'];
                    $like_condition[$field] = $input;
                }
                $users = $this->Userview->getAllUsersView(false,null,null,$clauses,$like_condition);
            }
            else {
                $users = $this->Userview->getAllUsersView(false,null,null,$clauses);
            }
            
            //$dp['system_name']
            foreach ($users as $dp){
                array_push($usersdata, array('id'=> $dp['person_id'],'dprow' => array($dp['username'],$dp['role_name'],$dp['first_name'],$dp['last_name'],$dp['phone_number'],$dp['email'])));
            }
            $data['userdata'] = $usersdata;
            
            echo json_encode($data);
        }
        
        function populateRolesEdit (){
            $roleOptions = null;
            $roles = $this->Role->getAll();
            
            foreach($roles as $role) { 
                
                $name=$role["role_name"]; 
                $id=$role["id"]; 
                if (!empty($name)){
                   $roleOptions.="<OPTION VALUE=\"$id\">".$name;  
                }
            } 
            echo $roleOptions;
            
        }
        
        function editUser (){
            
            $oper = $_REQUEST['oper'];
            $password = $_REQUEST['password'];
            $firstname = $_REQUEST['first_name'];
            $lastname = $_REQUEST['last_name'];
            $role = $_REQUEST['role_name'];
            $phone = $_REQUEST['phone_number'];
            $email = $_REQUEST['email'];
            $username = $_REQUEST['username'];
            $personid = $_REQUEST['id'];
            $userdata = array();
            
                            
            if (!empty($role)){

                if (!is_numeric($role)) {
                    $role_id= $this->Role->getId($role);
                }
                else {
                    $role_id = $role;
                }
            }
            if (empty($role) || empty($role_id) ){
                $role_id= $this->Role->getId('Guest');
            }
            if ($oper == 'add'){
                $userdata['username'] = $username;
                $userdata['password'] = md5($password);
                $userdata['role_id'] = $role_id;
            }
            
            $persondata = array('first_name'=>$firstname,'last_name'=>$lastname,'phone_number'=>$phone,'email'=>$email);
            
            $this->Userview->save($persondata,$userdata,$personid);           
        }
        
        function populateResources(){
          
           $searchOn = strip($_REQUEST['_search']);
           $page = $_REQUEST['page'];
           $limit = $_REQUEST['rows'];
           $sidx = $_REQUEST['sidx'];
           $sord = $_REQUEST['sord'];
            
           $resourcesdata = array();
           
           $count = $this->Resource->totalNoOfRows();
           
           
           
           if( $count > 0 && $limit > 0) { 
                $total_pages = ceil($count/$limit); 
            } else { 
                $total_pages = 0; 
            } 
            if ($page > $total_pages) $page=$total_pages;

            $start = $limit*$page - $limit;
 
            // if for some reasons start position is negative set it to 0 
            // typical case is that the user type 0 for the requested page 
           if($start <0) $start = 0; 
           $clauses = array('orderBy'=>$sidx,'orderDir'=>$sord,'startLimit'=>$start,'limit'=>$limit);
           $data['total'] = $total_pages;
           $data['page'] = $page;
           $data['records'] = strval($count); 
                      
           if($searchOn=='true') {
                $filters = json_decode($_REQUEST['filters'],true);
                $groupOp = $filters['groupOp'];
                $rules = $filters['rules'];
                $like_condition = array();
                foreach ($rules as $rule){
                    $field = $rule['field'];
                    $op= $rule['op'];
                    $input = $rule['data'];
                    if ($field=='parent_id'){
                        //$input = $this->Resource->getId($input);
                        //$field = 'resource';
                        $resourcesparents = $this->Resource->getAll(false,null,null,array('resource'=>$input));
                        $resourcesparentsid = array();
                        foreach($resourcesparents as $parents){
                            array_push($resourcesparentsid,$parents['id']);
                        }
                        $in_res_array = array('field_name'=>$field,'value_array'=>$resourcesparentsid);
                    }
                    else if ($field=='resource_type_id'){
                        $input = $this->Resource->getResourceTypeId($input);
                        $like_condition[$field] = $input;
                    }
                    else {
                    $like_condition[$field] = $input;
                    }
                }
                $resources = $this->Resource->getAll(false,null,$clauses,$like_condition,$in_res_array);
            }
            else {
                $resources = $this->Resource->getAll(false,null,$clauses);
            }
            
            
            foreach ($resources as $dp){
                array_push($resourcesdata, array('id'=> $dp['id'],'dprow' => array($dp['resource'],$dp['resource_type_id'],$dp['parent_id'],
                    $this->Resource->getResourceTypeName($dp['resource_type_id']),$this->Resource->getResourceName($dp['parent_id']),$dp['description'],$dp['ui_display_name'],$dp['relative_path_link'],$dp['relative_order_in_category'])));
            }
            $data['resourcedata'] = $resourcesdata;
            
            echo json_encode($data);
        }
        
        function populateResourceTypesEdit (){
           
             echo populateResourceTypesCommon();
            
        }
        
        function populatePermissionTypesEdit (){
            
             echo populatePermissionTypesEditCommon();
            
        }
        
        function populateParentResourcesEdit(){
             
             echo populateParentResourcesEditCommon();
        }
        
        function editResource (){
            
            $form_data = $_REQUEST['form_data'];
            $resource = $form_data['resource'];
            $resource_type_id = $form_data['resourceType'];
            $parent_id = $form_data['parent'];
            $description = $form_data['description'];
            $ui_display_name = $form_data['uiDisplayName'];
            $relative_path_link = $form_data['relativePath'];
            $relative_order_in_category = $form_data['relativeOrder'];
            $id = $form_data['resource_id_hidden'];
            $oper = $form_data['oper_hidden'];
            
            if ($oper=='add'){
                $id=false;
                $default_perm = $form_data['permissionType'];  
            }
            $resourcedata = array();
//           
            $resourcedata['resource'] = $resource;
            $resourcedata['resource_type_id'] = $resource_type_id;
            $resourcedata['parent_id'] = $parent_id;
            $resourcedata['description'] = $description;
            $resourcedata['ui_display_name'] = $ui_display_name;
            $resourcedata['relative_order_in_category'] = $relative_order_in_category;
            $resourcedata['relative_path_link'] = $relative_path_link;
            
            $this->Resource->save($resourcedata,$id,$default_perm);           
        }
        
        function populatePermissions(){
           $searchOn = strip($_REQUEST['_search']);
           $page = $_REQUEST['page'];
           $limit = $_REQUEST['rows'];
           $sidx = $_REQUEST['sidx'];
           $sord = $_REQUEST['sord'];
            
           $permissionsdata = array();
           
           $count = $this->Permission->totalNoOfRowsPermissionMapping();
           
           
           
           if( $count > 0 && $limit > 0) { 
                $total_pages = ceil($count/$limit); 
            } else { 
                $total_pages = 0; 
            } 
            if ($page > $total_pages) $page=$total_pages;

            $start = $limit*$page - $limit;
 
            // if for some reasons start position is negative set it to 0 
            // typical case is that the user type 0 for the requested page 
           if($start <0) $start = 0; 
           $clauses = array('orderBy'=>$sidx,'orderDir'=>$sord,'startLimit'=>$start,'limit'=>$limit);
           $data['total'] = $total_pages;
           $data['page'] = $page;
           $data['records'] = strval($count); 
                      
           if($searchOn=='true') {
                $filters = json_decode($_REQUEST['filters'],true);
                $groupOp = $filters['groupOp'];
                $rules = $filters['rules'];
                $like_condition = array();
                foreach ($rules as $rule){
                    $field = $rule['field'];
                    $op= $rule['op'];
                    $input = $rule['data'];
                    $like_condition[$field] = $input;
                }
                $permissions = $this->Permission->getAllPermissionMapping(false,null,$clauses,$like_condition);
            }
            else {
                $permissions = $this->Permission->getAllPermissionMapping(false,null,$clauses);
            }
            
            
            foreach ($permissions as $dp){
                array_push($permissionsdata, array('id'=> $dp['id'],'dprow' => array($dp['role_name'],$dp['resource_name'],$dp['permission_name'],$dp['isAllowed'])));
            }
            $data['permissiondata'] = $permissionsdata;
            
            echo json_encode($data);
        }
        
        function checkIfExists (){
            $roleid = $_REQUEST['role_name'];
            $resourceid = $_REQUEST['resource_name'];
            $permissionid = $_REQUEST['permission_name'];
            
            $status = $this->Permission->permissionExists($roleid,$resourceid,$permissionid);
            if ($status){
                return true;
            }
            else {
                return false;
            }
        }
        function editPermission (){
            $oper = $_REQUEST['oper'];
           if($oper == 'add' && $this->checkIfExists()){
                echo 'error';
                
           }
           else {
                
                $roleid = $_REQUEST['role_name'];
                $resourceid = $_REQUEST['resource_name'];
                $permissionid = $_REQUEST['permission_name'];
                $isAllowed = $_REQUEST['isAllowed'];
                $role_name = $this->Role->getName($roleid);
                $resource_name = $this->Resource->getResourceName($resourceid);
                $permission_name = $this->Permission->getPermissionName($permissionid);
                
                $id = $_REQUEST['id'];
                if ($id=='_empty'){
                    $id=false;
                }
                $permissiondata = array();
               
                $permissiondata['role_id'] = $roleid;
                $permissiondata['role_name'] = $role_name;
                $permissiondata['resource_id'] = $resourceid;
                $permissiondata['resource_name'] = $resource_name;
                $permissiondata['permission_id'] = $permissionid;
                $permissiondata['permission_name'] = $permission_name;
                $permissiondata['isAllowed'] = $isAllowed;

                $this->Permission->save($permissiondata,$id);  
                echo 'success';
           }
             
        }
        
        public function populateRoles(){
          
           $searchOn = strip($_REQUEST['_search']);
           $page = $_REQUEST['page'];
           $limit = $_REQUEST['rows'];
           $sidx = $_REQUEST['sidx'];
           $sord = $_REQUEST['sord'];
            
            
           $rolesdata = array();
           $count = $this->Role->totalNoOfRowsInParentMapping();
           if( $count > 0 && $limit > 0) { 
                $total_pages = ceil($count/$limit); 
            } else { 
                $total_pages = 0; 
            } 
            if ($page > $total_pages) $page=$total_pages;
            
            $start = $limit*$page - $limit;
 
            // if for some reasons start position is negative set it to 0 
            // typical case is that the user type 0 for the requested page 
            if($start <0) $start = 0; 
            $clauses = array('orderBy'=>$sidx,'orderDir'=>$sord,'startLimit'=>$start,'limit'=>$limit);
            
           $data['total'] = $total_pages;
           $data['page'] = $page;
           $data['records'] = $count; 
           if($searchOn=='true') {
                $filters = json_decode($_REQUEST['filters'],true);
                $groupOp = $filters['groupOp'];
                $rules = $filters['rules'];
                $like_condition = array();
                foreach ($rules as $rule){
                    $field = $rule['field'];
                    $op= $rule['op'];
                    $input = $rule['data'];
                    $like_condition[$field] = $input;
                }
                $roles = $this->Role->getAllParentsMapping(false,null,$clauses,$like_condition);
            }
            else {
                $roles = $this->Role->getAllParentsMapping(false,null,$clauses);
            }
            //$dp['system_name']
            foreach ($roles as $dp){
                array_push($rolesdata, array('id'=> $dp['id'],'dprow' => array($dp['role_name'],$dp['parent_role_name'])));
            }
            $data['roledata'] = $rolesdata;
            
            echo json_encode($data);
        }
        
        function checkIfRoleParentExists (){
            $roleid = $_REQUEST['role_id'];
            $parentroleid = $_REQUEST['parent_role_id'];
            
            
            $status = $this->Role->parentExists($roleid,$parentroleid);
            if ($status){
                return true;
            }
            else {
                return false;
            }
        }
        
        function editRoleInheritance (){
           if($this->checkIfRoleParentExists()){
                echo 'error';
                
           }
           else {
                $oper = $_REQUEST['oper'];
                $roleid = $_REQUEST['role_id'];
                $parentid = $_REQUEST['parent_role_id'];
                
                $role_name = $this->Role->getName($roleid);
                $parent_name = $this->Role->getName($parentid);
                
                
                $id = $_REQUEST['id'];
                if ($id=='_empty'){
                    $id=false;
                }
                $parentdata = array();
    //           
                $parentdata['role_id'] = $roleid;
                $parentdata['role_name'] = $role_name;
                $parentdata['parent_role_id'] = $parentid;
                $parentdata['parent_role_name'] = $parent_name;
                

                $this->Role->saveParent($parentdata,$id);  
                echo 'success';
           }
             
        }
        
//        function checkIfRoleExists


        function  createRole (){
            //$oper = $_REQUEST['oper'];
            $rolename= $_REQUEST['name'];
            $parents = $_REQUEST['parent'];
            
            $role_data = array('role_name'=>$rolename);
            $this->Role->insert($role_data,$parents);
            
            

//            $role_name = $this->Role->getName($roleid);
//            $parent_name = $this->Role->getName($parentid);
//
//
//            $id = $_REQUEST['id'];
//            if ($id=='_empty'){
//                $id=false;
//            }
//            $parentdata = array();
////           
//            $parentdata['role_id'] = $roleid;
//            $parentdata['role_name'] = $role_name;
//            $parentdata['parent_role_id'] = $parentid;
//            $parentdata['parent_role_name'] = $parent_name;
        }
}

?>
