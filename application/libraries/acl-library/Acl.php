<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//set_include_path(get_include_path() . PATH_SEPARATOR . BASEPATH . "application/libraries");
require_once BASEPATH .'libraries/Zend/Acl.php';
require_once BASEPATH .'libraries/Zend/Exception.php';
require_once BASEPATH .'libraries/Zend/Acl/Role/Interface.php';
require_once BASEPATH .'libraries/Zend/Acl/Role.php';
require_once BASEPATH .'libraries/Zend/Acl/Resource/Interface.php';
require_once BASEPATH .'libraries/Zend/Acl/Resource.php';
require_once BASEPATH .'libraries/Zend/Acl/Exception.php';
require_once BASEPATH .'libraries/Zend/Acl/Role/Registry.php';
require_once BASEPATH .'libraries/Zend/Acl/Role/Registry/Exception.php';
require_once BASEPATH .'libraries/Zend/Acl/Assert/Interface.php';
require_once 'Registry.php';

class Acl extends Zend_Acl {
 
    public $_getUserRoleName = null;
 
    public $_getUserRoleId = null;
 
    public $_user = null;
    
    var $CI;
	
    function __construct($user) {
            $this->CI = &get_instance();
            $this->CI->load->model('acl/Role','Role');
            $this->CI->load->model('acl/Resource','Resource');
            $this->CI->load->model('acl/Permission','Permission');
            $this->CI->load->model('acl/User','User');
            $this->_user = $user ? $user : 'Guest';
            $this->initRoles();
            $this->initResources();
            $this->initPermissions();

    }
  private function initRoles()
    {
        $roleArray = $this->CI->Role->getAll();
        //first create all the roles
        foreach ($roleArray as $role){
            $this->addRole($role['role_name']);
        }
        
        //then add the parents in ascending order that is lowest relative order parent (role_inheritence mapping table)
        //(in this case highest) goes in first
        
        foreach ($roleArray as $role){
            $parentsarray = $this->CI->Role->getAllParents($role['id']);
            
            foreach($parentsarray as $parent){
                $this->addParents($role['role_name'], $parent['parent_role_name']);
            }
        }
    }
    
    private function initPermissions (){
        $where_clause = array('isAllowed'=>1);
        $permission_mapping_array = $this->CI->Permission->getRoleResourcePermissionMapping($where_clause);             
        foreach ($permission_mapping_array as $row){
            $this->allow($row['role_name'], $row['resource_name'], $row['permission_name']);
        }
    }
 
    private function initResources()
    {
        $resourceArray = $this->CI->Resource->getAll();
        
        //first create all the resources
         foreach ($resourceArray as $resource){
             $this->addResource($resource['resource']);
           // var_dump($this->CI->Resource->getParent($resource['resource']));
        }
        //then add respective parents (
        
        foreach ($resourceArray as $resource){
            $parent = $this->CI->Resource->getParent($resource['resource']);
            if (!empty($parent)){
                $this->addParentToResource($resource['resource'], $parent);
            }
        }
        
    }
    
    public function addParentToResource($resource, $parent)
    {
        if (is_string($resource)) {
           $resourceId = $resource;
        }

        elseif ($resource instanceof Zend_Acl_Resource_Interface) {
            $resourceIns = $this->get($resource);
            $resourceId = $resourceIns->getResourceId();
            
        }
        else {
            
            throw new Zend_Acl_Exception('addParentToResource() expects $resource to be of type Zend_Acl_Resource_Interface or String');
        }

        

        if (!$this->has($resourceId)) {
            #require_once 'Zend/Acl/Exception.php';
            throw new Zend_Acl_Exception("Resource id '$resourceId' does not  exist in the ACL");
        }

        $resourceParent = null;

        if (null !== $parent) {
            try {
                if ($parent instanceof Zend_Acl_Resource_Interface) {
                    $resourceParentId = $parent->getResourceId();
                } else {
                    $resourceParentId = $parent;
                }
                $resourceParent = $this->get($resourceParentId);
            } catch (Zend_Acl_Exception $e) {
                #require_once 'Zend/Acl/Exception.php';
                throw new Zend_Acl_Exception("Parent Resource id '$resourceParentId' does not exist", 0, $e);
            }
            $existingparent = $this->_resources[$resourceId]['parent']; 
            if (!empty($existingparent)){
                throw new Zend_Acl_Exception("'$resourceId' already has a Parent Resource '$existingparent' ");
            }
            $this->_resources[$resourceParentId]['children'][$resourceId] = $resource;
            $this->_resources[$resourceId]['parent'] = $resourceParent;
        }
        return $this;
    }
    
    /**
     * Returns the Role registry for this ACL
     *
     * If no Role registry has been created yet, a new default Role registry
     * is created and returned.
     *
     * @return Zend_Acl_Role_Registry
     */
    protected function _getRoleRegistry()
    {
        if (null === $this->_roleRegistry) {
            $this->_roleRegistry = new Registry();
        }
        return $this->_roleRegistry;
    }
    
    
    
    public function addParents($role, $parents = null){
       return  $this->_getRoleRegistry()->addParents($role, $parents);
        
    }
    
    public function isUserAllowed($resource, $permission,$user=null)
    {   
        $rules = $this->_rules;
        
        if (empty($user)){
             $user = $this->_user;
             if (!empty($user)){
                 $role_id= $this->CI->User->getUser($user)->role_id;
                $role_name= $this->CI->Role->getName($role_id);
             }
             else {  
             $role_name = 'Guest';
             }
            
        }
        else {
           //$user = $this->_user;
            $role_id= $this->CI->User->getUser($user)->role_id;
            $role_name= $this->CI->Role->getName($role_id);
        }
        return ($this->isAllowed($role_name, $resource, $permission));
    }
        
       
}