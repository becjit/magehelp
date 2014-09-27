<?php
//delete after test
//require_once BASEPATH .'libraries/Zend/Acl.php';
//require_once BASEPATH .'libraries/Zend/Exception.php';
//require_once BASEPATH .'libraries/Zend/Acl/Role/Interface.php';
//require_once BASEPATH .'libraries/Zend/Acl/Role.php';
//require_once BASEPATH .'libraries/Zend/Acl/Resource/Interface.php';
//require_once BASEPATH .'libraries/Zend/Acl/Resource.php';
//require_once BASEPATH .'libraries/Zend/Acl/Exception.php';
//require_once BASEPATH .'libraries/Zend/Acl/Role/Registry.php';
//require_once BASEPATH .'libraries/Zend/Acl/Role/Registry/Exception.php';
//require_once BASEPATH .'libraries/Zend/Acl/Assert/Interface.php';
//delete

require_once ("secure_area.php");
class Utilities extends Secure_area 
{
        private $user;
        private $username;
        
        
        const SIZE ='size';
        const UOM ='uom';
        
       
        
	function __construct()
	{
    		parent::__construct('utilities');
                //$this->load->model('Invoice_master');
                //$this->load->model('Invoice_item');
                $this->load->model('Delivery_vehicle');
                $this->load->model('Delivery_point');
                $this->load->model('Unitofmeasure');
                $this->load->model('Packaging');
                $this->load->model('Manufacturer');
                $this->load->model('Mfr_model');
                $this->load->model('Product');
                $this->load->helper('grid_helper');
                $this->load->model('Invoice_master');
                $this->load->model('Invoice_item');
                $this->load->model('acl/Permission','Permission');
                $this->load->model('acl/User','Userview');
                $this->load->model('acl/Role','Role');
//                require_once('class/BCGFontFile.php');
//                require_once('class/BCGColor.php');
//                require_once('class/BCGDrawing.php');
//
//                // Including the barcode technology
//                require_once('class/BCGcode39.barcode.php');
                $this->load->library('barcode-library/Barcode_lib','','barcode');    
                 $param = array('user' => 'admin');
                $this->load->library('acl-library/Acl',$param,'acl');
                
                //$this->load->model('Shipment_master');
//                $this->user= $this->Employee->get_logged_in_employee_info();
//                $this->username = $this->user->last_name." ".$this->user->first_name;
                //$param = array('user' => 'admin');
                //$this->load->library('Acl',$param);
                
//		$this->load->library('sale_lib');
	}
        
        function index (){
           $data['numdp'] = $this->Delivery_point->totalNoOfRows();
           $data['nummfr']= $this->Manufacturer->totalNoOfRows();
           $data['nummodel'] = $this->Mfr_model->totalNoOfRows();
           $data['numuom']= $this->Unitofmeasure->totalNoOfRows();
           $data['numpkg']= $this->Packaging->totalNoOfRows();
           $this->load->view("utilities/dashboard",$data);
       }
        
       function createDeliveryPoint (){
           $name = $_POST['name'];
           $delivery_point_data = array ('name'=> $name,
              'address'=> $_POST['address'],
                   'city'=> $_POST['city'],
               'postcode'=> $_POST['pin'],
               'contact_number'=> $_POST['contactNumber']);
           $status = $this->Delivery_point->insert($delivery_point_data);
           
           if ($status) {
               $data['message'] = "Delivery Point $name is successfully created ";
               $this->load->view("common/message",$data);
           }
       }
       
       
           
       
       
       function createMeasurementUnits (){
           $name = $_POST['name'];
           $denom = $_POST['denom'];
           $status = $this->createMeasurementUnitsGeneric($name,$denom);
            $data['message'] = $status['message'];
            $this->load->view("common/message",$data);
       }
       
       private function createMeasurementUnitsGeneric($name,$denom){
           $error = false;
           if ($this->Unitofmeasure->exists($name)){
               $json = $this->Unitofmeasure->getJson($name);
               $array = json_decode($json,true);
               
               if (!in_array(array(Utilities::SIZE => $denom), $array)){
                   array_push($array,array(Utilities::SIZE => $denom));
                   $encoded_denom = json_encode($array);
                   $data = array ('denom_json' => $encoded_denom);
                   $status = $this->Unitofmeasure->update($name,$data);
                   if ($status ){
                       $message = "This denomination $denom for $name is updated";
                   }
                   else {
                        $message = "Error in updating denomination $denom for $name";
                        $error = true;
                   }
                   
               }
               else {
                   $message = "This denomination $denom for $name already exists";
               }
               
           }
           else {
                $encoded_denom = json_encode(array(array(Utilities::SIZE => $denom))); 
                $data = array ('unit_of_measure' =>$name,'denom_json' => $encoded_denom);
                $status = $this->Unitofmeasure->insert($data);
                if ($status){
                    $message = "This denomination $denom for $name is successfully created";
                }
                else {
                    $message = "Error in creating denomination";
                    $error = true;
                }
           }
            return array('message'=>$message,'error'=> $error);
       }
       
       function loadDeliveryPoint (){
           //$data['invoiceListSession'] =$_SESSION['invoiceList'];
            // = $this->Delivery_point->getmagentotest();
            $data['total']=$total;
            $this->load->view("utilities/add_delivery_point",$data);
       }
       
       
       function loadPackage (){
           //$data['invoiceListSession'] =$_SESSION['invoiceList'];
           
           $packageTypes = $this->Packaging->getAllPackageDetails();
           $options = null;
          
           foreach($packageTypes as $packageType) { 
                $name=$packageType["package_name"]; 
                $thing=$packageType["package_type"]; 
                $options.="<OPTION VALUE=\"$name\">".$name; 
                $arr[$name] = $thing;
            } 
            
            $data['options'] = $options;
            $data['packageMap'] = json_encode($arr);
            
            $denoms = $this->Unitofmeasure->getAll();
            
            foreach($denoms as $denom) { 
                $uom=$denom["unit_of_measure"]; 
                //$value = $denom["denom_json"];
                if (!empty($uom)){
                    $uomOptions.="<OPTION VALUE=\"$uom\">".$uom; 
                }
                
                
                
            } 
            
            
            $data['uomOptions'] = $uomOptions;
            $typeOptions = null;
            $types = $this->Packaging->getAllPackageTypes();
            
            foreach($types as $type) { 
                
                $thing=$type["package_type"]; 
                if (!empty($thing)){
                   $typeOptions.="<OPTION VALUE=\"$thing\">".$thing;  
                }
            } 
            
            $data['typeOptions'] = $typeOptions;
            $this->load->view("utilities/add_packaging",$data);
       }
       
       function loadOptions (){
            $packageTypes = $this->Packaging->getAllPackageDetails();
            $options = null;
          
           foreach($packageTypes as $packageType) { 
                $name=$packageType["package_name"]; 
                $thing=$packageType["package_type"]; 
                $options.="<OPTION VALUE=\"$name\">".$name; 
                $arr[$name] = $thing;
            } 
            
            $data['options'] = $options;
            $data['packageMap'] = json_encode($arr);
            
            $denoms = $this->Unitofmeasure->getAll();
            
            foreach($denoms as $denom) { 
                $uom=$denom["unit_of_measure"]; 
                //$value = $denom["denom_json"];
                if (!empty($uom)){
                    $uomOptions.="<OPTION VALUE=\"$uom\">".$uom; 
                }
                
                
                
            } 
            
            
            $data['uomOptions'] = $uomOptions;
            $typeOptions = null;
            $types = $this->Packaging->getAllPackageTypes();
            
            foreach($types as $type) { 
                
                $thing=$type["package_type"]; 
                if (!empty($thing)){
                   $typeOptions.="<OPTION VALUE=\"$thing\">".$thing;  
                }
            } 
            
            $data['typeOptions'] = $typeOptions;
            return $data;
       }
       
       function loadSize ($uomOp){
           $htmlSize = null;
           $uomSizeMap = json_decode($this->Unitofmeasure->getJson($uomOp),true);
           if (!empty($uomSizeMap)){
               asort($uomSizeMap);
               $htmlSize = "<OPTION VALUE=\"0\">"."Choose";
                foreach ($uomSizeMap as $validSize){
                    $size = $validSize['size'];
                    if (!empty($size)){
                        $htmlSize.= "<OPTION VALUE=\"$size\">".$size;
                    }
                    
                }
           }
           
           echo $htmlSize;
       }
       
       function loadMeasurementUnit (){
           //$data['invoiceListSession'] =$_SESSION['invoiceList'];
            $this->load->view("utilities/add_measurement_units");
       }
       
       function loadMfrModel (){
           $mfrTypes = $this->Manufacturer->getAll();
           $options = null;
          
           foreach($mfrTypes as $mfrType) { 
                $id=$mfrType["id"]; 
                $name=$mfrType["manufacturer_name"]; 
                $options.="<OPTION VALUE=\"$id\">".$name; 
                
            } 
            
            $data['options'] = $options;
            $this->load->view("utilities/add_manufacturer_model",$data);
       }
       
       function  createPackagingUnits (){
           //$isnewPackage = $_POST['newPkHidden'];
           //$isnewUOM = $_POST['newUnitHidden'];
           
           $name = $_POST['packageOptions'];
           if (empty($name)){
            $name = $_POST['namePkg'];
           }
           
           $uom= $_POST['uomOp'];
           $denom = $_POST['sizeOp'];
           if (empty($uom) || empty($denom)){
               $uom = $_POST['uomIp'];
               $denom = $_POST['denomIp'];
               $status= $this->createMeasurementUnitsGeneric($uom,$denom);
               $error = $status['error'];
               $message = $status['message'];
               if ($error){
                  $data['message'] = $message;
                  $this->load->view("common/message",$data); 
                  return;
               }
               
               
           }
           $desc =  $_POST['desc'];
           $type = $_POST['typeOp'];
           
           
           if ($this->Packaging->exists($name)){
               $json = $this->Packaging->getJson($name);
               $array = json_decode($json,true);
               
               if (!in_array(array('uom' => $uom,
                       'denom' => $denom), $array)){
                   array_push($array,array('uom' => $uom,
                       'denom' => $denom));
                   $encoded_denom = json_encode($array);
                   $data = array ('applicable_uom_json' => $encoded_denom,'package_description'=>$desc);
                   $status = $this->Packaging->update($name,$data);
                   if ($status ){
                       $message = "This package  $name is updated";
                   }
                   else {
                        $message = "Error in updating $name package";
                   }
                   
               }
               else {
                   $message = "This denomination $denom for $name already exists";
               }
               
           }
           else {
                $encoded_denom = json_encode(array(array('uom' => $uom,
                       'denom' => $denom))); 
                $data = array ('package_name' =>$name,'applicable_uom_json' => $encoded_denom,'package_description'=>$desc,'package_type' =>$type);
                $status = $this->Packaging->insert($data);
                if ($status){
                    $message = "This denomination $denom for $name is successfully created";
                }
                else {
                    $message = "Error in creating denomination";
                }
           }
            $data['message'] = $message;
            $this->load->view("common/message",$data);
       }

       function  createMfrModel (){
           //$isnewPackage = $_POST['newPkHidden'];
           //$isnewUOM = $_POST['newUnitHidden'];
           $newMfr = false;
           $mfrId = $_POST['mfrOptions'];
           if (empty($mfrId)){
                $mfrName = $_POST['nameMfr'];
                $descMfr= $_POST['mfrDesc'];
                $newMfr = true;
           }
           $modelName = $_POST['modelName'];
           $modelDesc = $_POST['modelDesc'];
           $this->db->trans_start();
           
           if ($newMfr){
               $mfrData = array ('manufacturer_name' =>$mfrName,'description'=>$descMfr);
               $mfrId = $this->Manufacturer->insert($mfrData);
           }
           $modelData = array('manufacturer_id' => $mfrId,'model_name'=>$modelName,'description'=> $modelDesc);
           $this->Mfr_model->insert($modelData);
           
           $this->db->trans_complete();
           
           if ($this->db->trans_status() === FALSE)
            {
                //echo $this->db->_error_message();
                $message= 'Model and manufacturer could not be added. Database Opearation Failed';
            }   
            else {
                $message= 'Model and manufacturer successfully added';
            }

           
          
           
            $data['message'] = $message;
            $this->load->view("common/message",$data);
       }



       function test(){
           //$data['invoiceListSession'] =$_SESSION['invoiceList'];
           $this->load->view("utilities/test");
           
//           $uomJson = $this->Packaging->getJsonById('6');
//                $uomArray = json_decode($uomJson,true);
//                //var_dump($uomArray);
//                $uomList = array();
//                $uomSizeMap = array();
//                $htmlUom = null;
//                
//                foreach ($uomArray as $uomDetails){
//                    $thisUom = $uomDetails['uom'];
//                    $denom = $uomDetails['denom'];
//                    if (!in_array($thisUom, $uomList)){
//                        array_push($uomList, $thisUom);
//                        $htmlUom.="<OPTION VALUE=\"$thisUom\">".$thisUom;
//                        $uomSizeMap[$thisUom] = array(array('denom' => $denom));
//                    }
//                    else {
//                        array_push($uomSizeMap[$thisUom], array('denom' => $denom));
//                    }
//                }
//                //var_dump ($uomList);
//                //var_dump ($htmlUom);
//                var_dump (json_encode($uomSizeMap));
                
                
       }
       
       function populateDeliveryPoint (){
           // standard request parameters
           $searchOn = strip($_REQUEST['_search']);
           $page = $_REQUEST['page'];
           $limit = $_REQUEST['rows'];
           $sidx = $_REQUEST['sidx'];
           $sord = $_REQUEST['sord'];
            
           //standard response parameters 
           $deliverypointdata = array();
           $count = $this->Delivery_point->totalNoOfRows();
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
           
           $searchOn = strip($_REQUEST['_search']);
           if($searchOn=='true') {
                $filters = json_decode($_REQUEST['filters'],true);
                $groupOp = $filters['groupOp'];
                $rules = $filters['rules'];

                $where_condition = array();
                foreach ($rules as $rule){
                    $field = $rule['field'];
                    $op= $rule['op'];
                    $input = $rule['data'];
                    $where_condition[$field]=$input;
                }
                $deliveryPoints = $this->Delivery_point->getAll(false,$clauses,$where_condition);
           }
           else {
               $deliveryPoints = $this->Delivery_point->getAll();
           }
           
           foreach ($deliveryPoints as $dp){
               array_push($deliverypointdata, array('id'=> $dp['id'],'dprow' => array($dp['name'],$dp['address'],$dp['city'],$dp['postcode'],$dp['contact_number'])));
           }
           $data['deliverypointdata'] = $deliverypointdata;
           echo json_encode($data);
       }
       
       function updateDeliverypoints (){
            $id = strip($_REQUEST['id']);
            $oper = strip($_REQUEST['oper']);
            $name = strip($_REQUEST['name']);
            $address = strip($_REQUEST['address']);
            $city = strip($_REQUEST['city']);
            $postcode = strip($_REQUEST['postcode']);
            $contact_number = strip($_REQUEST['contact_number']);
            
            $dp_data = array ('name' => $name,'address'=>$address,'city'=>$city,'postcode'=>$postcode,'contact_number'=>$contact_number);
            $this->Delivery_point->update($id,$dp_data);
        }
        
        function populateMfrs (){
           // standard request parameters
           $searchOn = strip($_REQUEST['_search']);
           $page = $_REQUEST['page'];
           $limit = $_REQUEST['rows'];
           $sidx = $_REQUEST['sidx'];
           $sord = $_REQUEST['sord'];
            
           //standard response parameters 
           $mfrsdata = array();
           $count = $this->Manufacturer->totalNoOfRows();
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
           
           $searchOn = strip($_REQUEST['_search']);
           if($searchOn=='true') {
                $filters = json_decode($_REQUEST['filters'],true);
                $groupOp = $filters['groupOp'];
                $rules = $filters['rules'];

                $where_condition = array();
                foreach ($rules as $rule){
                    $field = $rule['field'];
                    $op= $rule['op'];
                    $input = $rule['data'];
                    $where_condition[$field]=$input;
                }
                $mfrs = $this->Manufacturer->getAll(false,$clauses,$where_condition);
           }
           else {
               $mfrs = $this->Manufacturer->getAll();
           }
           
           foreach ($mfrs as $dp){
               array_push($mfrsdata, array('id'=> $dp['id'],'dprow' => array($dp['manufacturer_name'],$dp['description'])));
           }
           $data['mfrdata'] = $mfrsdata;
           echo json_encode($data);
       }
       
       function updateMfrs (){
            $id = strip($_REQUEST['id']);
            $oper = strip($_REQUEST['oper']);
            $name = strip($_REQUEST['manufacturer_name']);
            $desc = strip($_REQUEST['description']);
            
            $mfr_data = array ('manufacturer_name' => $name,'description'=>$desc);
            $this->Manufacturer->update($id,$mfr_data);
        }
        
        function populateModels (){
           // standard request parameters
           $searchOn = strip($_REQUEST['_search']);
           $page = $_REQUEST['page'];
           $limit = $_REQUEST['rows'];
           $sidx = $_REQUEST['sidx'];
           $sord = $_REQUEST['sord'];
            
           //standard response parameters 
           $mfrsdata = array();
           $count = $this->Mfr_model->totalNoOfRows();
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
           
           $searchOn = strip($_REQUEST['_search']);
           if($searchOn=='true') {
                $filters = json_decode($_REQUEST['filters'],true);
                $groupOp = $filters['groupOp'];
                $rules = $filters['rules'];

                $where_condition = array();
                foreach ($rules as $rule){
                    $field = $rule['field'];
                    $op= $rule['op'];
                    $input = $rule['data'];
                    $where_condition[$field]=$input;
                }
                $mfrs = $this->Mfr_model->getAll(false,$clauses,$where_condition);
           }
           else {
               $mfrs = $this->Mfr_model->getAll();
           }
           
           foreach ($mfrs as $dp){
               array_push($mfrsdata, array('id'=> $dp['id'],'dprow' => array($dp['model_name'],$dp['description'],$dp['manufacturer_id'],$dp['manufacturer_name'])));
           }
           $data['modeldata'] = $mfrsdata;
           echo json_encode($data);
       }
       
       function updateModels (){
            $id = strip($_REQUEST['id']);
            $oper = strip($_REQUEST['oper']);
            $name = strip($_REQUEST['model_name']);
            $desc = strip($_REQUEST['description']);
            $mfr_id = strip($_REQUEST['manufacturer']);
            
            
            $model_data = array ('model_name' => $name,'description'=>$desc,'manufacturer_id'=>$mfr_id);
            $this->Mfr_model->update($id,$model_data);
        }
        
        function populatePackageInGrid (){
           // standard request parameters
           $searchOn = strip($_REQUEST['_search']);
           $page = $_REQUEST['page'];
           $limit = $_REQUEST['rows'];
           $sidx = $_REQUEST['sidx'];
           $sord = $_REQUEST['sord'];
            
           //standard response parameters 
           $pkgsdata = array();
           $count = $this->Packaging->totalNoOfRows();
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
           
           $searchOn = strip($_REQUEST['_search']);
           if($searchOn=='true') {
                $filters = json_decode($_REQUEST['filters'],true);
                $groupOp = $filters['groupOp'];
                $rules = $filters['rules'];

                $where_condition = array();
                foreach ($rules as $rule){
                    $field = $rule['field'];
                    $op= $rule['op'];
                    $input = $rule['data'];
                    $where_condition[$field]=$input;
                }
                $pkgs = $this->Packaging->getAll(false,$clauses,$where_condition);
           }
           else {
               $pkgs = $this->Packaging->getAll();
           }
           
           foreach ($pkgs as $dp){
               array_push($pkgsdata, array('package_id'=> $dp['package_id'],'dprow' => array($dp['package_name'],$dp['package_description'],$dp['package_type'])));
           }
           $data['packagingdata'] = $pkgsdata;
           echo json_encode($data);
       }
       
       function populateUomInSubgrid(){
           //$searchOn = strip($_REQUEST['_search']);
           $page = $_REQUEST['page'];
           $limit = $_REQUEST['rows'];
           $id= $_REQUEST['id'];
           
            
           //standard response parameters 
           $uomsdata = array();
           $uomsarray = $this->Packaging->getAllUoms($id);
           $count = count($uomsarray);
           if( $count > 0 && $limit > 0) { 
               $total_pages = ceil($count/$limit);
           } else { 
               $total_pages = 0; 
           } 
           if ($page > $total_pages) $page=$total_pages;

           //$start = $limit*$page - $limit;
 
            // if for some reasons start position is negative set it to 0 
            // typical case is that the user type 0 for the requested page 
           //if($start <0) $start = 0; 
           //$clauses = array('orderBy'=>$sidx,'orderDir'=>$sord,'startLimit'=>$start,'limit'=>$limit);
            
           $data['total'] = $total_pages;
           $data['page'] = $page;
           $data['records'] = $count; 
           
           $count = 1;
           foreach ($uomsarray as $dp){
                array_push($uomsdata, array('id'=> $count++,'dprow' => array($dp['denom'],$dp['uom'])));
           }
           $data['uomdata'] = $uomsdata;
           echo json_encode($data);
       }
       
       function updatePackages (){
            $id = strip($_REQUEST['id']);
            $oper = strip($_REQUEST['oper']);
            $name = strip($_REQUEST['package_name']);
            $desc = strip($_REQUEST['package_description']);
            $type = strip($_REQUEST['package_type']);
            
            
            $dp_data = array ('package_description'=>$desc,'package_type'=>$type);
            $this->Packaging->updateById($id,$dp_data);
        }
        function populatePackageTypesEdit (){
            $typeOptions = null;
            $types = $this->Packaging->getAllPackageTypes();
            
            foreach($types as $type) { 
                
                $thing=$type["package_type"]; 
                if (!empty($thing)){
                   $typeOptions.="<OPTION VALUE=\"$thing\">".$thing;  
                }
            } 
             echo $typeOptions;
            
        }
        
        function populateUoms (){
            $page = $_REQUEST['page'];
            $limit = $_REQUEST['rows'];
            $id= $_REQUEST['id'];

            $uomsdata = array();
           
            $uoms = $this->Unitofmeasure->getAll();
            $count = 0;
            foreach  ($uoms as $uom){
                $uomName = $uom['unit_of_measure'];
                $denoms = json_decode($uom['denom_json'],true);
                
                foreach ($denoms as $dp){
                        array_push($uomsdata, array('id'=> ++$count,'dprow' => array($dp['size'],$uomName)));
                }
            }
            if( $count > 0 && $limit > 0) { 
               $total_pages = ceil($count/$limit);
            } else { 
                $total_pages = 0; 
            } 
            if ($page > $total_pages) $page=$total_pages;

            $data['total'] = $total_pages;
            $data['page'] = $page;
            $data['records'] = $count; 
            $data['uomdata'] = $uomsdata;
            echo json_encode($data);
        }
    
    function edittest (){
//        $invoices = $this->Invoice_master->getUnprocessedInvoicesFromMagento();
//        //var_dump($invoices) ;
//         foreach ($invoices as $inv){
//                    $inv_entity_id = $inv['entity_id'];
//                    $inv_increment_id = $inv['invoice_increment_id'];
//                    $order_id = $inv['order_id'];
//                    $order_increment_id = $inv['order_increment_id'];
//                    $items = $this->Invoice_item->getUnprocessedInvoiceItemsFromMagento($inv_entity_id);
//                    var_dump($items);
//                    echo 'next'.'<br/>';
//                }
       // $filename = $this->barcode->generateBarcode('test');
            $filename = '/var/www/opensourcepos/images/temp/barcode5.png';
            $this->load->helper('file');
            delete_files($filename);
                        if ( ! write_file($filename, ''))
            {
                echo 'Unable to write the file';
            }
            $this->barcode->generateBarcode('18000012020303030',$filename);
        
        //$searchOn = $this->Strip($_REQUEST['_search']);
        //echo $this->Product->lastIdPresent();
        //$csv = $this->Delivery_point->getAllCsv();
            //$data = 'Some file data';


            //header("Content-Type: image/png");
//  header("Content-description: File Transfer");
//  header("Content-disposition: attachment; filename=\"thefilename.csv\"");
//  header("Pragma: public");
//  header("Cache-control: max-age=0");
//  header("Expires: 0");
//$output = fopen('php://output','w');
//fputcsv($output,$csv);
 //echo $csv;

   
  $this->load->view("utilities/test",$data);
     
            //echo $csv;
        
    }

    function roleTest (){
        
        //$this->acl->listRoles();
        //$this->load->view("acl/user_grid");
        $this->load->view("utilities/test");
//        var_dump($acl);
        
//        $acl = new Zend_Acl();
//        $acl->addRole('abc');
//        $acl->addRole('abc', 'xyz');
//        if (empty($acl->getRoles())) {
//            echo 'empty';
//        }
//        else {
//            echo 'ne';
//        }
//        //var_dump(count());
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
        
        public function testSelect(){
            $test = $_REQUEST['test'] ;
            define('FPDF_FONTPATH',$this->config->item('fonts_path'));
            $this->load->library('fpdf','','pdf');
//            $this->load->library('rotation');
//            $this->load->library('pdf','','pdf');
            
            
            $this->pdf->AddPage();
            $this->pdf->SetFont('Arial','B',16);
            $this->pdf->Cell(40,10,'Hello World!');

            $this->pdf->Output('/tmp/test.pdf', 'F');
        }
        
//        public function importCategories(){
//           $status =  $this->Category->importCategories();
//           log_message('debug', 'status of import '.$status);
//        }
        
        public function loadCat(){
            $this->load->view("utilities/add_category");
        }
        
         public function renderParents(){
            /* [
                            {
                                "data" : "Search engines",
                                "attr": {id:"23"},
                                "children" :[
                                             {"data":"Yahoo", "metadata":{"href":"http://www.yahoo.com"}},
                                             {"data":"Bing", "metadata":{"href":"http://www.bing.com"}},
                                             {"data":"Google", 
                                              
                                              "children":[{"data":"Youtube", "metadata":{"href":"http://youtube.com"}},{"data":"Gmail", "metadata":{"href":"http://www.gmail.com"}},{"data":"Orkut","metadata":{"href":"http://www.orkut.com"}}], "metadata" : {"href":"http://youtube.com"}}
                                            ]
                            },
                            {
                                "data" : "Networking sites",
                                "children" :[
                                    {"data":"Facebook", "metadata":{"href":"http://www.fb.com","id":"fb"}},
                                    {"data":"Twitter", "metadata":{"href":"http://twitter.com"}}
                                ]
                            }
                        ]*/ 
            //$this->load->view("utilities/add_category",$data);ge
             $result = $this->Category->getAll(2);
             $final = array();
             foreach ($result as $res){
                 $cat['data'] = $res['category_name'];
                 $cat['attr']['id']= $res['magento_entity_id'];
                 $cat['state'] = 'closed';
                 array_push($final,$cat);
             }
             $result_json = json_encode($final);
             echo  $result_json;
             //$data['treedata'] = $result_json;
             //$this->load->view("utilities/add_category",$data);
             
        }
        
        public function renderChildren(){
            $id = $_REQUEST['nodeid'];
            $result = $this->Category->getChildren($id);
             $final = array();
             foreach ($result as $res){
                 $cat['data'] = $res['category_name'];
                 $cat['attr']['id']= $res['magento_entity_id'];
                 $cat['state'] = 'closed';
                 array_push($final,$cat);
             }
             $result_json = json_encode($final);
             echo $result_json;
             //$data['treedata'] = $result_json;
             //$this->load->view("utilities/add_category",$data);
        }
        
        /*    Supplier */
        
        public function loadSupplersGrid(){
            $this->load->view('utilities/suppliers_grid',$data);
        }
        
        function populateSuppliersInGrid (){
           // standard request parameters
           $searchOn = strip($_REQUEST['_search']);
           $page = $_REQUEST['page'];
           $limit = $_REQUEST['rows'];
           $sidx = $_REQUEST['sidx'];
           $sord = $_REQUEST['sord'];
            
           //standard response parameters 
           $suppliersdata = array();
           $count = $this->Supplier->totalNoOfRows();
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

                $where_condition = array();
                foreach ($rules as $rule){
                    $field = $rule['field'];
                    $op= $rule['op'];
                    $input = $rule['data'];
                    $where_condition[$field]=$input;
                }
                $suppliers = $this->Supplier->getAll(false,$clauses,$where_condition);
           }
           else {
               $suppliers = $this->Supplier->getAll();
           }
           
           foreach ($suppliers as $dp){
               array_push($suppliersdata, array('id'=> $dp['id'],'dprow' => array($dp['supplier_name'],$dp['registration_number'],$dp['contact_person'],$dp['address'],$dp['city'],$dp['state'],$dp['email'],$dp['contact_number'])));
           }
           $data['supplierdata'] = $suppliersdata;
           echo json_encode($data);
       }
       
       public function modifySuppliers(){
            $oper = $_REQUEST['oper'];
            $id= $_REQUEST['id'];
            
            $data['supplier_name'] = $_REQUEST['supplier_name'];
            $data['registration_number']  = $_REQUEST['registration_number'];
            $data['contact_person'] = $_REQUEST['contact_person'];
            $data['address'] = $_REQUEST['address'];
            $data['city'] = $_REQUEST['city'];
            $data['state'] = $_REQUEST['state'];
            $data['email'] = $_REQUEST['email'];
            $data['contact_number'] = $_REQUEST['contact_number'];
            
            if ($oper== 'add'){
                $this->Supplier->save($data);
            }
            else if ($oper== 'edit'){
                $this->Supplier->save($data,$id);
            }
            else if ($oper== 'del'){
                $this->Supplier->delete($id);
            }
       }
       
       function loadExistingPackageFragment(){
           $this->load->view('utilities/partial/packaging/existing_package',$this->loadOptions());
       }
       
       function loadNewPackageFragment(){
           $this->load->view('utilities/partial/packaging/new_package',$this->loadOptions());
       }
	
}
?>