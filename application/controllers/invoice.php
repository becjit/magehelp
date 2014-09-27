<?php
require_once ("secure_area.php");

class Invoice extends Secure_area 
{
        private $user;
        private $username;
        
	function __construct()
	{
    		parent::__construct('invoice');
                $this->load->model('Invoice_master');
                $this->load->model('Invoice_item');
                $this->load->model('Delivery_vehicle');
                $this->load->model('Delivery_point');
                $this->load->model('Shipment_master');
                $this->load->model('acl/User','Userview');
                $this->user= $this->Userview->get_logged_in_employee_info();
                $this->username = $this->user->last_name." ".$this->user->first_name;
                $param = array('user' => $this->user->username);
                $this->load->library('acl-library/Acl',$param,'Acl');
                
	}
        
	function index()
	{
            $users = $this->Userview->getAllUsersByRole(null,array('field_name'=>'role_name',array('Everyone','Guest')));
            
            foreach($users as $user) { 
                $user_id=$user["person_id"]; 
                $username=$user["username"]; 
                //$value = $denom["denom_json"];
                if (!empty($user_id)){
                    $userOptions.="<OPTION VALUE=\"$user_id\">".$username; 
                }             
            } 
            $data['userOptions'] = $userOptions;
            $this->load->view("invoice/invoice_list");
	}
        
//        function assignEntityToUser(){
//            $ids = $_REQUEST['ids'];
//            $entity = $_REQUEST['entity'];
//            $role = $_REQUEST['role'];
//            $user_id = $_REQUEST['user_id'];
//            if ($entity=='incoming_invoice'){
//                $model = 'Invoice_master';
//            }
//           
//            if (!empty($ids) && !empty($role) ){
//                if (empty($user_id)){
//                    $user_id = $this->user->person_id;
//                }
//               
//                else if ($role =='owner'){
//                    $upd_data['owner_id'] = $user_id;
//                }
//                foreach($ids as $id){
//                    
//                    $this->$model->update(array('id'=>$id),$upd_data);
//                }
//            }
//        }
        
        function packInvoice(){
            $id = $_REQUEST['id'];
            $data['invoice_id']= $id;
            $inv_data = array
            (
                'owner_id'=>$this->user->person_id,
                'last_updated_by'=>$this->user->person_id,
            );
            $where_clause_array= array('magento_invoice_entity_id'=>$id);
            $this->Invoice_master->update($where_clause_array,$inv_data);
            $this->load->view("invoice/pack_invoice",$data);
        }
        
        function populateInvoiceItems(){
            $invoiceid = $_REQUEST['invoiceId'];
            $page = $_REQUEST['page'];
            $limit = $_REQUEST['rows'];
            $sidx = $_REQUEST['sidx'];
            $sord = $_REQUEST['sord'];
            $whereClause = array();
            if (!empty($invoiceid)){
                $whereClause['magento_invoice_entity_id'] = $invoiceid;
            }
            
            $whereClause['type'] = 'simple';
            

            //standard response parameters 
            $mfrsdata = array();
            $count = $this->Invoice_item->totalNoOfRows($whereClause);
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
            $mfrs = $this->Invoice_item->getAll(false,$whereClause,$clauses);
            

            foreach ($mfrs as $dp){
                array_push($mfrsdata, array('id'=> $dp['magento_entity_id'],'dprow' => array($dp['sku'],$dp['name'],$dp['invoiced_number'],$dp['packed_number'])));
            }
            $data['invoicedata'] = $mfrsdata;
            echo json_encode($data);
        } 
        
        
        
        function completePacking(){
//            
                $invoiceId = $_POST['invoiceId'];
                $isMatched = $_POST['isMatched'];
                $comments = $_POST['comments'];
                $jsonItems = $_POST['packedJson'];
                $items = json_decode($jsonItems,true);
                
                $inv_data = array
                (
                    'status'=>'packed',
                    'comments'=>$comments,
                    'matched'=>$isMatched,
                    'last_updated_by'=>$this->user->person_id,
                    'packed_by'=>$this->user->person_id,
                );
                $where_clause_array= array('magento_invoice_entity_id'=>$invoiceId);
                $this->Invoice_master->update($where_clause_array,$inv_data);
                $this->Invoice_item->updateMultipleByEntityId($items);
          
            //$data['invoiceListSession'] =$_SESSION['invoiceList'];
            $this->load->view("invoice/invoice_list");
        }
        
        function ship(){
            $ids = $_REQUEST['ids'];
           
            
            $batch_array =array();
            foreach ($ids as $id){
                array_push($batch_array,array('id'=>$id,'status'=>self::READYFORSHIPPING,'shipped_by'=>$this->user->person_id));
            }
         
            $this->Invoice_master->update_batch($batch_array,'id');
            
            echo 'success';
            
        }
        
        function loadShipment(){
            $deliveryPoints = $this->Delivery_point->getAll();
            $deliveryVehicles = $this->Delivery_vehicle->getAll();
            
            $options=""; 
            $optionsVehicle = "";

            foreach($deliveryPoints as $deliveryPoint) { 

                $id=$deliveryPoint["id"]; 
                $thing=$deliveryPoint["name"]; 
                $options.="<OPTION VALUE=\"$thing\">".$thing; 
            } 

            foreach($deliveryVehicles as $deliveryVehicle) { 

                $id=$deliveryVehicle["id"]; 
                $reg=$deliveryVehicle["reg_number"]; 
                $optionsVehicle.="<OPTION VALUE=\"$reg\">".$reg; 
            }
            $data['options']=$options;
            $data['optionsVehicle']=$optionsVehicle;
           
            $this->load->view("invoice/shipment",$data);
        }
        
        function confirmation(){
            
            $deliveryPoint = $_POST['deliveryPointDD'];
            $deliveryVehicle= $_POST['deliveryVehicleDD'];
            $invoiceIDsJSON = $_POST['selectedInv'];
            $shippingArray = json_decode($invoiceIDsJSON,true);
            
            
            $this->db->trans_start();
            
            $trackingNumber = $this->_createTrackingNumber($deliveryVehicle, $deliveryPoint);
            $shipping_data = array
                    (
                            
                            'tracking_number'=>$trackingNumber,
                            'delivery_vehicle'=>$deliveryVehicle,
                            'delivery_point'=>$deliveryPoint
                            
                    );
            $this->Shipment_master->insert($shipping_data);

            foreach ($shippingArray as $shipping){
                $invoice_data_shipping = array(
                    'status' => 'shipped',
                    'shipping_tracking_number' => $trackingNumber
                );
                $where_clause = array('magento_invoice_entity_id' =>$shipping);
                $this->Invoice_master->update($where_clause, $invoice_data_shipping);
                
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
            

            $to = "abhijit.mazumder@gmail.com";
            if ($success){
                $subject = "Shipping Confirmattion Mail For $trackingNumber";
                $message = "Shipping Confirmed For ";
                foreach ($shippingArray as $shipping){
                    $message = $message."Invoice Number ".$shipping['invoice_id'];
                }
                $message = $message."Delivery Vehicle Number ".$deliveryVehicle."Delivery Point ".$deliveryPoint;
            }
            else {
                $subject = "Shipping Faliure Mail For $trackingNumber";
                $message = "Shipping Failed  For ";
                foreach ($shippingArray as $shipping){
                    $message = $message."Invoice Number ".$shipping['invoice_id'];
                }
            }

            $from = "shopifine@localshopifine.com";
            $headers = "From:" . $from;
            mail($to,$subject,$message,$headers);
            
            //$data['shippingArray'] = $shippingArray;
            $data['trackingNumber'] = $trackingNumber;
            
            
            $this->load->view('invoice/confirmation',$data);
            
            

            //TODO: if everything fine unset the invoice List in session

        }
        
        
        
        function _createTrackingNumber($reg_number,$delivery_point){
            $offset = 100000;
            $lastId = $this->Shipment_master->last_insert_id();
            
            $trackingNumber = $offset + $lastId + 1;
            $vehicleId = $this->Delivery_vehicle->getId($reg_number);
            $trackingNumber = $trackingNumber.'00' + $vehicleId ;
            $pointId = $this->Delivery_point->getId($delivery_point);
            $trackingNumber = $trackingNumber.'00' + $pointId;
            
            return $trackingNumber;
        
        }
        
        function populateInvoices(){

           $where  =array();
           $in_where  =array();
           $invoicedata = array();
           setOwnerStatusCommon($where,$in_where);
           $griddata=  populateGridCommon('Invoice_master',$where,null,$in_where);
           $dbrows = $griddata['db_data'];
           $data = $griddata['grid_metadata'];
         
           
           foreach ($dbrows as $dp){
               array_push($invoicedata, array('id'=> $dp['magento_invoice_entity_id'],'dprow' => array($dp['magento_invoice_increment_id'],$dp['status'],$dp['magento_order_increment_id'],$dp['comments'],$dp['owner_name'],'pack',$dp['owner_id'])));
           }
           $data['invoicedata'] = $invoicedata;
           echo json_encode($data);
        }
        
        function populatePackedInvoicesByUser(){
           
           $page = $_REQUEST['page'];
           $limit = $_REQUEST['rows'];
           $sidx = $_REQUEST['sidx'];
           $sord = $_REQUEST['sord'];
           $where = array('status'=>'packed');
           $where['owner_id'] = $this->user->person_id;
            
           //standard response parameters 
           $mfrsdata = array();
           $count = $this->Invoice_master->totalNoOfRows($where);
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
           
           $mfrs = $this->Invoice_master->getAll(false,$where);
           
           foreach ($mfrs as $dp){
               array_push($mfrsdata, array('id'=> $dp['magento_invoice_entity_id'],'dprow' => array($dp['magento_invoice_increment_id'],$dp['status'],$dp['magento_order_increment_id'],$dp['comments'])));
           }
           $data['invoicedata'] = $mfrsdata;
           echo json_encode($data);
        }
        
        function populateShipmentReadyInvoices(){
           $page = $_REQUEST['page'];
           $limit = $_REQUEST['rows'];
           $sidx = $_REQUEST['sidx'];
           $sord = $_REQUEST['sord'];
           $where = array('status'=>'readyforshipping');
           $where['owner_id'] = $this->user->person_id;
            
           //standard response parameters 
           $mfrsdata = array();
           $count = $this->Invoice_master->totalNoOfRows($where);
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
           
           $mfrs = $this->Invoice_master->getAll(false,$where);
           
           foreach ($mfrs as $dp){
               array_push($mfrsdata, array('id'=> $dp['magento_invoice_entity_id'],'dprow' => array($dp['magento_invoice_increment_id'],$dp['status'],$dp['magento_order_increment_id'],$dp['comments'])));
           }
           $data['invoicedata'] = $mfrsdata;
           echo json_encode($data);
        }
        
        function populateShippedInvoices(){
           $page = $_REQUEST['page'];
           $limit = $_REQUEST['rows'];
           $sidx = $_REQUEST['sidx'];
           $sord = $_REQUEST['sord'];
           $where = array('status'=>'shipped');
           $where['owner_id'] = $this->user->person_id;
           $where['shipping_tracking_number']= $_REQUEST['trackingNumber'];
            
           //standard response parameters 
           $mfrsdata = array();
           $count = $this->Invoice_master->totalNoOfRows($where);
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
           
           $mfrs = $this->Invoice_master->getAll(false,$where);
           
           foreach ($mfrs as $dp){
               array_push($mfrsdata, array('id'=> $dp['magento_invoice_entity_id'],'dprow' => array($dp['magento_invoice_increment_id'],$dp['status'],$dp['magento_order_increment_id'],$dp['comments'])));
           }
           $data['invoicedata'] = $mfrsdata;
           echo json_encode($data);
        }
        
        function assign(){
            $inv_id_array  = json_decode($_REQUEST['selInv']);
            foreach($inv_id_array as $id){
                $inv_data = array
                (
                    'owner_id'=>$_REQUEST['userId'],
                    'last_updated_by'=>$this->user->person_id,
                );
                $where_clause_array= array('magento_invoice_entity_id'=>$id);
                $this->Invoice_master->update($where_clause_array,$inv_data);
            }
        }
        
        function acquire(){           
            $inv_id_array  = json_decode($_REQUEST['selInv']);
            foreach($inv_id_array as $id){
                $inv_data = array
                (
                    'owner_id'=>$this->user->person_id,
                    'last_updated_by'=>$this->user->person_id,
                );
                $where_clause_array= array('magento_invoice_entity_id'=>$id);
                $this->Invoice_master->update($where_clause_array,$inv_data);
            }
        }

	
}
?>