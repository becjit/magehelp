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

/**
 * Description of Registry
 *
 * @author abhijit
 */
class Registry extends Zend_Acl_Role_Registry {
    
    //put your code here
    /**
     * Adds a Role having an identifier unique to the registry
     *
     * The $parents parameter may be a reference to, or the string identifier for,
     * a Role existing in the registry, or $parents may be passed as an array of
     * these - mixing string identifiers and objects is ok - to indicate the Roles
     * from which the newly added Role will directly inherit.
     *
     * In order to resolve potential ambiguities with conflicting rules inherited
     * from different parents, the most recently added parent takes precedence over
     * parents that were previously added. In other words, the first parent added
     * will have the least priority, and the last parent added will have the
     * highest priority.
     *
     * @param  Zend_Acl_Role_Interface              $role
     * @param  Zend_Acl_Role_Interface|string|array $parents
     * @throws Zend_Acl_Role_Registry_Exception
     * @return Zend_Acl_Role_Registry Provides a fluent interface
     */
    public function addParents($role, $parents = null)
    {
        //$roleId = $role->getRoleId();
        
        if ($role instanceof Zend_Acl_Role_Interface) {
            $roleId = $role->getRoleId();
        } elseif (is_string($role)) {
            $roleId = $role;
        }
        else {
            throw new Zend_Acl_Role_Registry_Exception("The first parameter  must implement interface Zend_Acl_Role_Interface or be a string. ".get_class($role)." was provided");
        }
                    

        if (!$this->has($roleId)) {
            /**
             * @see Zend_Acl_Role_Registry_Exception
             */
            #require_once 'Zend/Acl/Role/Registry/Exception.php';
            throw new Zend_Acl_Role_Registry_Exception("Role id '$roleId' does not exist in the registry.Please add '$roleId' before adding a parent");
        }

        $roleParents = array();

        if (null !== $parents) {
            if (!is_array($parents)) {
                $parents = array($parents);
            }
            /**
             * @see Zend_Acl_Role_Registry_Exception
             */
            #require_once 'Zend/Acl/Role/Registry/Exception.php';
            $existingparents=$this->getParents($role);
            $newparents = $existingparents;
            foreach ($parents as $parent) {
                try {
                    if ($parent instanceof Zend_Acl_Role_Interface) {
                        $roleParentId = $parent->getRoleId();
                    } else {
                        $roleParentId = $parent;
                    }
                    $roleParent = $this->get($roleParentId);
                } catch (Zend_Acl_Role_Registry_Exception $e) {
                    throw new Zend_Acl_Role_Registry_Exception("Parent Role id '$roleParentId' does not exist", 0, $e);
                }
                if (!array_key_exists($roleParentId, $existingparents)){
                    $roleParents[$roleParentId] = $roleParent;
                    $newparents[$roleParentId] = $roleParent;
                }
                
                $this->_roles[$roleParentId]['children'][$roleId] = $role;
            }
             $this->_roles[$roleId]['parents'] = $newparents;
        }
        
        return $this;
    }
    
    

}

?>
