<?php
class Secure_area extends CI_Controller 
{
	
        const ORDERED = 'ordered';
        const CANCELLED = 'cancelled';
        const OPEN = 'open';
        const PENDING = 'pending';
        const COMPLETE = 'complete';
        const PARTIAL = 'partiallypaid';
        const WAITING_FOR_APPROVAL = 'waitingforapproval';
        const READY = 'ready';
        const RECEIVING = 'receiving';
        const RECEIVED = 'received';
        const REJECTED = 'rejected';
        const INVOICING = 'invoicing';
        const INVOICED = 'invoiced';
        const PROCESSED_FOR_PAYMENT = 'processedforpayment';
        const READYTOINVOICEMEMO = 'readytoinvoicememo';
        const RECEIPTSSUBMITTED = 'receiptssubmitted';
        const DRAFT = 'draft';
        const VERSION = 'version';
        const SUPPLIERPRICELIST = 'supplierpl';
        const ROOT = 'root';
        const BETWEEN = 'between';
        const MORETHAN = 'morethan';
        const LESSTHAN = 'lessthan';
        const EQUAL = 'equal';
        const PRODUCT = 'product';
        const ALL = 'all';
        const SOME = 'some';
        const NONE = 'none';
        const SUBMITTEDTOQUOTE = 'submittedtoquote';
        const EXISTING = 'existing';
        const DIRECTPERCENTAGE = 'directpercentage';
        const DIRECTAMOUNTTOTAL = 'directamounttotal';
        const DIRECTAMOUNTUNIT = 'directamountunit';
        const ADD = 'add';
        const EDIT = 'edit';
        const FULLPAYMENT = 'fullpayment';
        const PARTPAYMENT = 'partpayment';
        const ADVANCEPAYMENT = 'advancepayment';
        const ADVANCE = 'advance';
        const GENERAL = 'general';
        const NOTAPPLICABLE = 'notapplicable';
        const READYFORSHIPPING = 'readyforshipping';
        /*
	Controllers that are considered secure extend Secure_area, optionally a $module_id can
	be set to also check if a user can access a particular module in the system.
	*/
	function __construct($module_id=null,$type='menu')
	{
		parent::__construct();	
                $this->load->library('acl-library/Acl','','Acl');
		$this->load->model('acl/User','User');
                $this->load->model('acl/Role','Role');
                $this->load->model('acl/Permission','Permission');
		if(!$this->User->is_logged_in())
		{
			redirect('login');
		}
                $username = $this->User->get_logged_in_employee_info()->username;
		
		if(!$this->Acl->isUserAllowed($module_id.'-'.$type,'all',$username))
		{
			redirect('no_access/'.$module_id);
		}
		
		//load up global data
//                $CI =& get_instance();
//$CI->magento = $this->load->database('magento', TRUE);
//$this->magento =$CI->magento; 
                $CI =& get_instance();
                $CI->magento = $CI->load->database('magento', TRUE);
		$logged_in_employee_info=$this->User->get_logged_in_employee_info();
                $html = $this->buildMenu($logged_in_employee_info);
                $adminhtml = $this->buildAdminMenu($logged_in_employee_info);
                $data['menu'] = $html;
                $data['adminmenu'] = $adminhtml;
		$data['allowed_modules']=$this->Module->get_allowed_modules($logged_in_employee_info->person_id);
		$data['user_info']=$logged_in_employee_info;
		$this->load->vars($data);
	}
        
//        function __construct($module_id=null)
//	{
//		parent::__construct();	
//		$this->load->model('Employee');
//		if(!$this->Employee->is_logged_in())
//		{
//			redirect('login');
//		}
//		
//		if(!$this->Employee->has_permission($module_id,$this->Employee->get_logged_in_employee_info()->person_id))
//		{
//			redirect('no_access/'.$module_id);
//		}
//		
//		//load up global data
////                $CI =& get_instance();
////$CI->magento = $this->load->database('magento', TRUE);
////$this->magento =$CI->magento; 
//                $CI =& get_instance();
//                $CI->magento = $CI->load->database('magento', TRUE);
//		$logged_in_employee_info=$this->Employee->get_logged_in_employee_info();
//                $html = $this->buildMenu($logged_in_employee_info);
//                $data['menu'] = $html;
//		$data['allowed_modules']=$this->Module->get_allowed_modules($logged_in_employee_info->person_id);
//		$data['user_info']=$logged_in_employee_info;
//		$this->load->vars($data);
//	}
        
//        private function buildMenu ($logged_in_employee_info){
//            $allowedModules = $this->Permission->getMenu($logged_in_employee_info->username);
//            $html;
//            $style = addslashes("border-top-left-radius: 5px;
//    border-top-right-radius: 5px;");
//            foreach($allowedModules as $module)
//            {
//                $html .= "<li><a style='".$style."' class="."fNiv"." href=".site_url($module->relative_path_link).">".$module->ui_display_name."</a>";
//                $allowedSubMenu =  $this->Permission->getSubMenu($logged_in_employee_info->username,$module->id);
//                if (!empty($allowedSubMenu)){
//                    $html.= "<ul>";
//                }
//                
//                foreach($allowedSubMenu as $submenu){
//                    $html .= "<li>
//                                    <a href=".site_url($submenu->relative_path_link). ">".$submenu->ui_display_name."
//                                        </a>
//                                </li>";                 
//                }
//                if (!empty($allowedSubMenu)){
//                    $html.= "</ul>";
//                }
//                $html .= "</li>";
//            }
//               	
//            return $html;
//        }
        
        private function buildMenu ($logged_in_employee_info){
            $allowedModules = $this->Permission->getMenu($logged_in_employee_info->username);
            $html;
            $finalpermissions = array();
            
            foreach($allowedModules as $module){
                $resource= $module['resource'];
                $permission=$module['isAllowed'];
                $finalpermissions[$resource] = $permission;
            }
            
            foreach ($allowedModules as $key => $row) {
                $order[] =$row['relative_order_in_category'] ; 
                // of course, replace 0 with whatever is the date field's index
            }
          
//
            array_multisort($order, SORT_ASC, $allowedModules);
            
            $style = addslashes("border-top-left-radius: 5px;
    border-top-right-radius: 5px;");
            foreach($allowedModules as $module)
            {
                if ($finalpermissions[$module['resource']]==1){
                    $html .= "<li><a style='".$style."' class="."fNiv"." href=".site_url($module['relative_path_link']).">".$module['ui_display_name']."</a>";
                    $allowedSubMenu =  $this->Permission->getSubMenu($logged_in_employee_info->username,$module['id']);
                    $finalsubmenupermissions = array();
                    foreach($allowedSubMenu as $submenu){
                        $subMenuResource= $submenu['resource'];
                        $subMenuPermission=$submenu['isAllowed'];
                        $finalsubmenupermissions[$subMenuResource] = $subMenuPermission;
                    }
                    //checking if there is any submenu at all
                        if (!empty($finalsubmenupermissions)){
                            $html.= "<ul>";
                        }
                        
                        foreach ($allowedSubMenu as $key => $row) {
                            $orderSM[] =$row['relative_order_in_category'] ; 
                            // of course, replace 0 with whatever is the date field's index
                        }
                        
                        if (!empty($orderSM)){
                            array_multisort($orderSM, SORT_ASC, $allowedSubMenu);
                            unset($orderSM);
                        }
            //
                        

                        foreach($allowedSubMenu as $submenu){
                            if ($finalsubmenupermissions[$submenu['resource']]==1){
                                $html .= "<li>
                                                <a href=".site_url($submenu['relative_path_link']). ">".$submenu['ui_display_name']."
                                                    </a>
                                            </li>";    
                                $finalsubmenupermissions[$submenu['resource']]==0;
                            }
                        }
                        if (!empty($allowedSubMenu)){
                            $html.= "</ul>";
                        }
                    $html .= "</li>";
                    $finalpermissions[$module['resource']]=0;
                }
            }
               	
            return $html;
        }
        
        private function buildAdminMenu ($logged_in_employee_info){
            
            $allowedModules = $this->Permission->getAdminMenu($logged_in_employee_info->username);
            $html;
            $finalpermissions = array();
            
            foreach($allowedModules as $module){
                $resource= $module['resource'];
                $permission=$module['isAllowed'];
                $finalpermissions[$resource] = $permission;
            }
            
            foreach ($allowedModules as $key => $row) {
                $order[] =$row['relative_order_in_category'] ; 
                // of course, replace 0 with whatever is the date field's index
            }
//
            array_multisort($order, SORT_ASC, $allowedModules);
            
            $style = addslashes("border-top-left-radius: 5px;
    border-top-right-radius: 5px;");
            foreach($allowedModules as $module)
            {
                if ($finalpermissions[$module['resource']]==1){
                    $html .= "<li><a style='".$style."' class="."fNiv"." href=".site_url($module['relative_path_link']).">".$module['ui_display_name']."</a>";
                    $allowedSubMenu =  $this->Permission->getSubMenu($logged_in_employee_info->username,$module['id']);
                    $finalsubmenupermissions = array();
                    foreach($allowedSubMenu as $submenu){
                        $subMenuResource= $submenu['resource'];
                        $subMenuPermission=$submenu['isAllowed'];
                        $finalsubmenupermissions[$subMenuResource] = $subMenuPermission;
                    }
                    //checking if there is any submenu at all
                        if (!empty($finalsubmenupermissions)){
                            $html.= "<ul>";
                        }
                        foreach ($allowedSubMenu as $key => $row) {
                            $orderSM[] =$row['relative_order_in_category'] ; 
                            // of course, replace 0 with whatever is the date field's index
                        }
            //
                        if (!empty($orderSM)){
                            array_multisort($orderSM, SORT_ASC, $allowedSubMenu);
                            unset($orderSM);
                        }

                        foreach($allowedSubMenu as $submenu){
                            if ($finalsubmenupermissions[$submenu['resource']]==1){
                                $html .= "<li>
                                                <a href=".site_url($submenu['relative_path_link']). ">".$submenu['ui_display_name']."
                                                    </a>
                                            </li>";    
                            }
                        }
                        if (!empty($allowedSubMenu)){
                            $html.= "</ul>";
                        }
                    $html .= "</li>";
                }
            }
               	
            return $html;
            
//            $allowedModules = $this->Permission->getAdminMenu($logged_in_employee_info->username);
//            $html;
//            foreach($allowedModules as $module){
//                $resource= $module->resource;
//                $permission=$module->isAllowed;
//                $finalpermissions[$resource] = $permission;
//            }
//            
//            $style = addslashes("border-top-left-radius: 5px;
//    border-top-right-radius: 5px;");
//            foreach($allowedModules as $module)
//            {
//                if ($finalpermissions[$module->resource]==1){
//                    $html .= "<li><a style='".$style."' class="."fNiv"." href=".site_url($module->relative_path_link).">".$module->ui_display_name."</a>";
//                    $allowedSubMenu =  $this->Permission->getSubMenu($logged_in_employee_info->username,$module->id);
//                    foreach($allowedSubMenu as $submenu){
//                        $subMenuResource= $submenu->resource;
//                        $subMenuPermission=$submenu->isAllowed;
//                        $finalsubmenupermissions[$subMenuResource] = $subMenuPermission;
//                    }
//                    //checking if there is any submenu at all
//                        if (!empty($finalsubmenupermissions)){
//                            $html.= "<ul>";
//                        }
//                        
//
//                        foreach($allowedSubMenu as $submenu){
//                            if ($finalsubmenupermissions[$submenu->resource]==1){
//                                $html .= "<li>
//                                                <a href=".site_url($submenu->relative_path_link). ">".$submenu->ui_display_name."
//                                                    </a>
//                                            </li>";    
//                            }
//                        }
//                        if (!empty($allowedSubMenu)){
//                            $html.= "</ul>";
//                        }
//                    $html .= "</li>";
//                }
//            }
//               	
//            return $html;
        }
        
//         private function buildAdminMenu ($logged_in_employee_info){
//            $allowedModules = $this->Permission->getAdminMenu($logged_in_employee_info->username);
//            $html;
//            $style = addslashes("border-top-left-radius: 5px;
//    border-top-right-radius: 5px;");
//            foreach($allowedModules as $module)
//            {
//                $html .= "<li><a style='".$style."' class="."settings-icon"." href=".site_url($module->relative_path_link).">&nbsp;&nbsp;&nbsp;&nbsp;".$module->ui_display_name."</a>";
//                $allowedSubMenu =  $this->Permission->getSubMenu($logged_in_employee_info->username,$module->id);
//                if (!empty($allowedSubMenu)){
//                    $html.= "<ul>";
//                }
//                
//                foreach($allowedSubMenu as $submenu){
//                    $html .= "<li>
//                                    <a href=".site_url($submenu->relative_path_link). ">".$submenu->ui_display_name."</a>
//                                </li>";                 
//                }
//                if (!empty($allowedSubMenu)){
//                    $html.= "</ul>";
//                }
//                $html .= "</li>";
//            }
//               	
//            return $html;
//        }
        
        function isAllowedInternal($module_id,$permission){
//            $module_id = $_REQUEST['module'];
//            $permission = $_REQUEST['permission'];
//            if (is_array($module_id)){
//                
//            }
//            else {
//                
//            }
            // very very important require heavy testing+++++++++++++++++++++
            if (empty($permission)){
                $permission = 'all';
            }
            $username = $this->User->get_logged_in_employee_info()->username;
//            $module_id = 'approve-rfq-button';
//            $permission = 'view';
            //optimistic i.e. if no permission eists for the specified resource we are assuming no restricions applied
            if ($this->Permission->anyPermissionExistsForResource($module_id)){
                // 'admin' is parent of approve,generatedirect,assign so while checking approve or assign we should also check if admin exists
                if ($permission=='approve' || $permission=='assign' || $permission=='generatedirect'){
                   if ($this->Acl->isUserAllowed($module_id,'all',$username) || $this->Acl->isUserAllowed($module_id,'admin',$username) || $this->Acl->isUserAllowed($module_id,$permission,$username) ){
                        return "true";
                    }
                    else {
                        return "false";
                    } 
                }
                 // 'admin' is parent of create,update,delete so while checking approve or assign we should also check if admin exists
                else if ($permission=='create' || $permission=='update' || $permission=='delete' || $permission=='receive'){
                   if ($this->Acl->isUserAllowed($module_id,'all',$username) ||  $this->Acl->isUserAllowed($module_id,'manage',$username) || $this->Acl->isUserAllowed($module_id,$permission,$username) ){
                        return "true";
                    }
                    else {
                        return "false";
                    } 
                }
                else if ($permission=='mark' ){
                   if ($this->Acl->isUserAllowed($module_id,'all',$username) ||  $this->Acl->isUserAllowed($module_id,'manage',$username) ||  $this->Acl->isUserAllowed($module_id,'admin',$username) || $this->Acl->isUserAllowed($module_id,$permission,$username) ){
                        return "true";
                    }
                    else {
                        return "false";
                    } 
                }
                else {
                   if ($this->Acl->isUserAllowed($module_id,'all',$username)  || $this->Acl->isUserAllowed($module_id,$permission,$username) ){
                        return "true";
                    }
                    else {
                        return "false";
                    } 
                } 
                
            }
            else {
                $role_id= $this->User->getUser($username)->role_id;
                $role_name= $this->Role->getName($role_id);
               
                if ($role_name=='Administrator'){
                    return "true";
                }
                else {
                    return "false";
                }
               
            }
            
        }
        
        function isAllowedBulk(){
            $resources = $_REQUEST['resource_perm'];
            $permission = array();
            foreach($resources as $resource){
                $allowed = $this->isAllowedInternal($resource['resource'],$resource['permission']);
                //array_push($permission, array($resource['resource']=>$allowed));
                $permission[$resource['element']] = $allowed;
                 log_message("debug",'print');
                log_message("debug",$resource['element']);
                log_message("debug",$resource['permission']);
                
            }
            echo json_encode($permission);
        }
        
        function isAllowed(){
            $module_id = $_REQUEST['module'];
            $permission = $_REQUEST['permission'];
            $allowed = $this->isAllowedInternal($module_id,$permission);
            echo $allowed;
        }
        
         function assignEntityToUser(){
            $ids = $_REQUEST['ids'];
            $entity = $_REQUEST['entity'];
            $role = $_REQUEST['role'];
            $user_id = $_REQUEST['user_id'];
            if ($entity=='rfq'){
                $model = 'Request_quote_master';
            }
            else if ($entity=='quote'){
                $model = 'Purchase_quote_master';
            }
             else if ($entity=='order'){
                $model = 'Purchase_order_master';
            }
            else if ($entity=='receipt_item'){
                $model = 'Receipt_item';
            }
            else if ($entity=='receipt'){
                $model = 'Receipt_master';
            }
            else if ($entity=='invoice'){
                $model = 'Purchase_invoice_master';
            }
            else if ($entity=='incoming_invoice'){
                $model = 'Invoice_master';
            }
           
            if (!empty($ids) && !empty($role) ){
                if (empty($user_id)){
                    $user_id = $this->User->get_logged_in_employee_info()->person_id;
                }
                if ($role =='approver'){
                    $upd_data['approved_by'] = $user_id;
                }
                else if ($role =='receiver'){
                    $upd_data['received_by'] = $user_id;
                    $upd_data['owner_id'] = $user_id;
                }
                else if ($role =='payer'){
                    $upd_data['payer_id'] = $user_id;
                    $upd_data['owner_id'] = $user_id;
                }
                else if ($role =='owner'){
                    $upd_data['owner_id'] = $user_id;
                }
                foreach($ids as $id){
                    
                    $this->$model->update(array('id'=>$id),$upd_data);
                }
            }
        }
        function prepareAssignDialog(){
//            var_dump(($this->User->getAllEligibleOwners('rfq')));
//            var_dump(($this->User->getAllEligibleApprovers('rfq')));
            $entity = $_REQUEST['entity'];
            if ($entity=='order'){
                $owners = $this->User->getAllEligibleReceivers($_REQUEST['entity']);
            }
            else{
                $owners = $this->User->getAllEligibleOwners($_REQUEST['entity']);
            }
            
            
            $approvers = $this->User->getAllEligibleApprovers($_REQUEST['entity']);
            $adminUserOptions =null;
            $userOptions = null;
            foreach($owners as $owner) { 
                
                $name=$owner["username"]; 
                $id=$owner["person_id"]; 
                if (!empty($name)){
                    $userOptions.="<OPTION VALUE=\"$id\">".$name;  
                }
            }
            foreach($approvers as $approver) { 
                
                $name=$approver["username"]; 
                $id=$approver["person_id"]; 
                if (!empty($name)){
                    $adminUserOptions.="<OPTION VALUE=\"$id\">".$name;  
                }
            }
            $data['userOptions'] = $userOptions;
            $data['adminUserOptions'] = $adminUserOptions;
            echo json_encode($data);
        }
}
?>