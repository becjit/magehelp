<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


    
    
    function check_if_exists_in_array($array, $key, $value)
    {
        $results = array();

        if (is_array($array))
        {
            if (isset($array[$key]) && $array[$key] == $value)
                $results[] = $array;

            foreach ($array as $subarray)
                $results = array_merge($results, check_if_exists_in_array($subarray, $key, $value));
        }

        return $results;
    }
    
    function   appendComments($notes,$notecolumn){
        //$fmatted_rcvd_note = "&#013;&#010; **** ".date("Y-m-d H:i:s", time()). " ****&#013;&#010;";
        $CI = & get_instance();
        $user = $CI->User->get_logged_in_employee_info();
        $username = $user->last_name." ".$user->first_name;
        if (empty($notes)){
            $notes = "No Comments Found For This Modification";
        }
        $fmatted_rcvd_note .= "&#160;******** Start Entry ********&#013;&#010;****".$notes;
        $fmatted_rcvd_note .= "****&#013;&#010; ** At ".date("Y-m-d H:i:s", time())." By ".$username. " ****&#013;&#010;"."&#160;******** End Entry  ********&#013;&#010;";
        // to workaround when column is null
        return "CONCAT(IFNULL(`$notecolumn`,'------ First Entry ------ '), '$fmatted_rcvd_note')";
    }
    
    function populateSuppliers(){
        $CI = & get_instance();
        $suppliers= $CI->Supplier->getAll();
        foreach($suppliers as $supplier) { 

            $id=$supplier["id"]; 
            $thing=$supplier["supplier_name"]; 
            $supplieroptions.="<OPTION VALUE=\"$id\">".$thing; 
        } 
        return $supplieroptions;
    }
    function populateWarehouses(){
         $CI = & get_instance();
        $deliveryPoints = $CI->Delivery_point->getAll();
        foreach($deliveryPoints as $deliveryPoint) { 

            $id=$deliveryPoint["id"]; 
            $thing=$deliveryPoint["name"]; 
            $options.="<OPTION VALUE=\"$id\">".$thing; 
        }
        return $options;
    }
    
    function populateManfacturers(){
        $CI = & get_instance();
        $mfrArray = $CI->Manufacturer->getAll();
           
        foreach ($mfrArray as $mfr){
            $mfr_id=$mfr["id"]; 
            $mfr_name=$mfr["manufacturer_name"]; 
            $mfrOptions.="<OPTION VALUE=\"$mfr_id\">".trim($mfr_name)."</OPTION>";
        }
        return $mfrOptions;
    }
    
    function populatePackages(){
        $CI = & get_instance();
        $packageTypes = $CI->Packaging->getAllPackageDetails();
        $pkgOptions = null;

        foreach($packageTypes as $packageType) { 
             $pkg_id=$packageType["package_id"]; 
             $pkg_name=$packageType["package_name"]; 
             $pkgOptions.="<OPTION VALUE=\"$pkg_id\">".$pkg_name;

         } 
         return $pkgOptions;
     }
     
     function populateAdvancePayments($order_id){
        $CI = & get_instance();
        if (!empty($order_id)){
            $where['order_id']=$order_id;
        }
        $where['payment_type']='advance';
        //array_push($where,'`invoiced_amount` < `amount`');
        
        $total = $CI->Outgoing_payment->totalNoOfRows($where);
        $advances = $CI->Outgoing_payment->getAll(false,$where,array('id','reference'));
        $advanceOptions = null;

        foreach($advances as $adv) { 
             $id=$adv["id"]; 
             $ref=$adv["reference"]; 
             $advanceOptions.="<OPTION VALUE=\"$id\">".$ref;

         }
         $selectdata['total'] = $total;
         $selectdata['advanceOptions'] = $advanceOptions;
         return $selectdata;
     }
    
     function populateModels($mfr){
        $CI = & get_instance();
        $models = $CI->Mfr_model->getAllByMfrId($mfr);
        $htmlData= null;
        foreach ($models as $model){
            //$data = array('label' => $model['model_name'], 'value' => $model['id']);
            //array_push($autoData,$data);
            $id = $model['id'];
            $name = $model['model_name'];
            $htmlData.="<OPTION VALUE=\"$id\">".$name;
        }
        return $htmlData;
     }
     
    function populateAttributeSets(){
        $CI = & get_instance();
        $attributeSets = $CI->Attribute_set->getAll();
           
        foreach ($attributeSets as $set){
           $set_name=$set["attribute_set_name"]; 
           $setOptions.="<OPTION VALUE=\"$set_name\">".trim($set_name)."</OPTION>";
        }
        return $setOptions;
     }
    function populateProductsBySupplier($supplier_id){
        $CI = & get_instance();
        if (!empty($supplier_id)){
            $where['supplier_id']=$supplier_id;
        }
        $products = $CI->Supplier->getAllProductsSupplierMapping(false, $where, array('product_id','product_name','manufacturer','model','measurement_denomination','uom','barcode'), array('orderBy'=>'product_id'), null, null, null,$not_in_where_clause);
            foreach ($products as $product){
                $id = $product['product_id'];
                $name = $product['product_name'].', '.$product['barcode'].', '.$product['manufacturer'].' '.$product['model']
                        .' '.$product['measurement_denomination'].' '.$product['uom'];
                $productOptions.="<OPTION VALUE=\"$id\">".$name;
            }
        return $productOptions;
     }
     
     function populateProducts(){
          $CI = & get_instance();
        $productArray = $CI->Product->getValues();
        foreach ($productArray as $product){
            //$data = array('label' => $model['model_name'], 'value' => $model['id']);
            //array_push($autoData,$data);
            $id = $product['id'];
            $name = $product['product_name'].', '.$product['barcode'].', '.$product['manufacturer'].' '.$product['model']
                    .' '.$product['measurement_denomination'].' '.$product['uom'];
            $productOptions.="<OPTION VALUE=\"$id\">".$name;
        }
        return $productOptions;
     }
     
     function populateBaseContracts(){
        $CI = & get_instance();
        $pricelists = $CI->Pricelist_master->getAll(false,array('type'=>'main'),array('id','name'));
        $htmlData= null;
        foreach ($pricelists as $pricelist){
            //$data = array('label' => $model['model_name'], 'value' => $model['id']);
            //array_push($autoData,$data);
            $id = $pricelist['id'];
            $name = $pricelist['name'];
            $htmlData.="<OPTION VALUE=\"$id\">".$name;
        }
        return $htmlData;
     }
     function populateReceiptOptions ($order_id){
            
            $where = array();
            if (!empty($order_id)){
                $where['order_id'] = $order_id;
            }
             $CI = & get_instance();
            $receipts = $CI->Receipt_master->getAll(false,$where,array('id','supplier_receipt_number'));
            foreach($receipts as $receipt) { 
                $id=$receipt["id"]; 
                $details=$receipt["supplier_receipt_number"]; 
                //$value = $denom["denom_json"];
                if (!empty($id)){
                    $receiptOptions.="<OPTION VALUE=\"$id\">".$details; 
                }             
            } 
            
            return $receiptOptions;
        }
     function createWhereClause(&$where,$op,$field,$input){
         switch ($op) {
             case "eq":
                 $where[$field]=$input;
                 break;
              case "ge":
                 $where[$field.' >=']=$input;
                 break;
              case "gt":
                  $where[$field.' >']=$input;
                 break;
             case "le":
                 $where[$field.' <=']=$input;
                 break;
              case "lt":
                  $where[$field.' <']=$input;
                 break;
             case "ne":
                  $where[$field.' !=']=$input;
                 break;
             default:
                 break;
         }
     }
     
     function populateGridCommon($model,$where=array(),$fields_array=array(),$in_where_clause_array=array(),$or_where_clause_array=array(),$not_in_where_clause_array=array(),$isunion=false){
            $CI = & get_instance();
            $page = $_REQUEST['page'];
            $limit = $_REQUEST['rows'];
            $sidx = $_REQUEST['sidx'];
            $sord = $_REQUEST['sord'];
            $searchOn = $_REQUEST['_search'];
            //standard response parameters 
            $count = $CI->$model->totalNoOfRows($where,null,$or_where_clause_array,$in_where_clause_array,$not_in_where_clause_array);
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
                    if ($op=="cn"){
                    $like_condition[$field] = trim($input);
                    }
                    else {
                        createWhereClause($where,$op,$field,$input);
                    }
                   
                }
                $dbrowdata = $CI->$model->getAll(false,$where,$fields_array,$clauses,$like_condition,$in_where_clause_array,$or_where_clause_array,$not_in_where_clause_array);
            }
            else {
                $dbrowdata = $CI->$model->getAll(false,$where,$fields_array,$clauses,null,$in_where_clause_array,$or_where_clause_array,$not_in_where_clause_array);
            }


            return array('grid_metadata'=>$data,'db_data'=>$dbrowdata);
            
     }
     
     function tallyOrderedDelivery($received,$returned,$cnbd){
         
     }
     
     function populateResourceTypesCommon(){
            $CI = & get_instance();
            $resourceTypeOptions = null;
            $resources = $CI->Resource->getAllResourceTypes();
            
            foreach($resources as $resource) { 
                
                $name=$resource["name"]; 
                $id=$resource["id"]; 
                if (!empty($name)){
                   $resourceTypeOptions.="<OPTION VALUE=\"$id\">".$name;  
                }
            }
            return $resourceTypeOptions;
     }
     
     function populateParentResourcesEditCommon(){
            $CI = & get_instance();
            $parentOptions = null;
            $parents = $CI->Resource->getAll();
            
            foreach($parents as $parent) { 
                
                $name=$parent["resource"]; 
                $id=$parent["id"]; 
                if (!empty($name)){
                   $parentOptions.="<OPTION VALUE=\"$id\">".$name;  
                }
            } 
             return $parentOptions;
        }
        
        function populatePermissionTypesEditCommon ($onlyvalue=false){
            $CI = & get_instance();
            $permissionTypeOptions = null;
            $permissions = $CI->Permission->getAllPermissionTypes();
            
            foreach($permissions as $permission) { 
                
                $name=$permission["permission"]; 
                $id=$permission["id"]; 
                if (!empty($name)){
                    if ($onlyvalue){
                        $permissionTypeOptions.="<OPTION VALUE=\"$name\">".$name;  
                    }
                    else {
                        $permissionTypeOptions.="<OPTION VALUE=\"$id\">".$name;  
                    }
                   
                }
            } 
             return $permissionTypeOptions;
            
        }
        
        function setOwnerStatusCommon(&$where,&$in_where){
            $CI = & get_instance();
            $status = $_REQUEST['_status'];
            $pp_status = $_REQUEST['_pp_status'];
            $owner_id = $_REQUEST['owner_id'];
            $mode = $_REQUEST['mode'];
            $approved_by = $_REQUEST['approved_by'];
             if (!empty($status)){
                //if not check if it is an array: If a srray use in_where clause
                if (is_array($status)){
                   //$where['owner_id'] = $this->user->person_id; 
                   $in_where['field_name'] = 'status' ;
                   $in_where['value_array'] = $_REQUEST['_status'] ;
                }
                else {
                    //check is if wee need all RFQs: If not add where clause
                     if ($status!='all'){
                        //$where['owner_id'] = $this->user->person_id;
                        $where['status'] = $status;
                     }
                }
            }
             if (!empty($pp_status)){
                //if not check if it is an array: If a srray use in_where clause
                if (is_array($pp_status)){
                   //$where['owner_id'] = $this->user->person_id; 
                   $in_where['field_name'] = 'pp_status' ;
                   $in_where['value_array'] = $_REQUEST['_pp_status'] ;
                }
                else {
                    //check is if wee need all RFQs: If not add where clause
                     if ($pp_status!='all'){
                        //$where['owner_id'] = $this->user->person_id;
                        $where['pp_status'] = $pp_status;
                     }
                }
            }
            if ($mode=='admin'){
                if (!empty($approved_by)){
                //if not check if it is an array: If a srray use in_where clause
                    if (is_array($approved_by)){
                       //$where['owner_id'] = $this->user->person_id; 
                       $in_where['field_name'] = 'approved_by' ;
                       $in_where['value_array'] = $approved_by ;
                    }
                    else {
                        //check is if wee need all users: If not add where clause
                         if ($approved_by!='all'){
                            //$where['owner_id'] = $this->user->person_id;
                            $where['approved_by'] = $approved_by;
                         }
                    }
                }
                else {
                    $where['approved_by'] = $CI->User->get_logged_in_employee_info()->person_id;
                }
            }
//            else{
                if (!empty($owner_id)){
                //if not check if it is an array: If a srray use in_where clause
                    if (is_array($owner_id)){
                       //$where['owner_id'] = $this->user->person_id; 
                       $in_where['field_name'] = 'owner_id' ;
                       $in_where['value_array'] = $owner_id ;
                    }
                    else {
                        //check is if wee need all users: If not add where clause
                         if ($owner_id!='all'){
                            //$where['owner_id'] = $this->user->person_id;
                            $where['owner_id'] = $owner_id;
                         }
//                         else {
//                             $in_where['field_name'] = 'owner_id' ;
//                             $all_users = $this->User->getAllUsersView(false,null,array('person_id'));
//                             foreach($all_users as $k=>$v) {
//                                $val_arr[$k] = $v['person_id'];
//                             }
//
//                             $in_where['value_array'] = $val_arr;
//                         }
                    }
                }
                else {
                    // if mode is not admin then if no owner id is there set current user as owner_id
                    if ($mode!='admin'){
                        $where['owner_id'] = $CI->User->get_logged_in_employee_info()->person_id;
                    }
                    
                }
            //}
        }
            
