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
                $this->user= $this->Employee->get_logged_in_employee_info();
                $this->username = $this->user->last_name." ".$this->user->first_name;
                $param = array('user' => 'admin');
                $this->load->library('Acl',$param);
                
//		$this->load->library('sale_lib');
	}
        
        function unsetSession(){
            session_start();
            if (isset($_SESSION['invoiceList'])){
                unset($_SESSION['invoiceList']);

            }
        }

	function index()
	{
            $callbackUrl = "http://localhost/opensourcepos/index.php/invoice";
            $temporaryCredentialsRequestUrl = "http://localhost/magento/oauth/initiate?oauth_callback=" . urlencode($callbackUrl);
            $adminAuthorizationUrl = 'http://localhost/magento/admin/oauth_authorize';
            $accessTokenRequestUrl = 'http://localhost/magento/oauth/token';
            $apiUrl = 'http://localhost/magento/api/rest';
            $consumerKey = '93jffpt61prd21be2r1ioxwok613z38m';
            $consumerSecret = 'jqhdamkxjdch6neygjgm8luep9hbcpe6';
            session_start();
            
            
            
            if (isset($_POST['invoice_number'])){
                $_SESSION['invoice_number'] = $_POST['invoice_number'];
            }
            if (!isset($_GET['oauth_token']) && isset($_SESSION['state']) && $_SESSION['state'] == 1) {
                $_SESSION['state'] = 0;
            }
            try {
                $authType = ($_SESSION['state'] == 2) ? OAUTH_AUTH_TYPE_AUTHORIZATION : OAUTH_AUTH_TYPE_URI;
                $oauthClient = new OAuth($consumerKey, $consumerSecret, OAUTH_SIG_METHOD_PLAINTEXT, $authType);
                $oauthClient->enableDebug();

                if (!isset($_GET['oauth_token']) && !$_SESSION['state']) {
                    $requestToken = $oauthClient->getRequestToken($temporaryCredentialsRequestUrl);
                    $_SESSION['secret'] = $requestToken['oauth_token_secret'];
                    $_SESSION['state'] = 1;
                    redirect($adminAuthorizationUrl . '?oauth_token=' . $requestToken['oauth_token']);
                    exit;
                } else if ($_SESSION['state'] == 1) {
                    $oauthClient->setToken($_GET['oauth_token'], $_SESSION['secret']);
                    $accessToken = $oauthClient->getAccessToken($accessTokenRequestUrl);
                    $_SESSION['state'] = 2;
                    $_SESSION['token'] = $accessToken['oauth_token'];
                    $_SESSION['secret'] = $accessToken['oauth_token_secret'];
                    redirect($callbackUrl);
                    exit;
                } else {
                    $oauthClient->setToken($_SESSION['token'], $_SESSION['secret']);
                    //$resourceUrl = "$apiUrl/products?type=rest";
                    //$resourceUrl = "$apiUrl/orders/100000064/items?type=rest";
                    $invoiceId = $_SESSION['invoice_number'];
                    unset($_SESSION['invoice_number']) ;//now unset
                    if ($invoiceId){
                        $resourceUrl = "$apiUrl/invoice/".$invoiceId."/items?type=rest";
                        $oauthClient->fetch($resourceUrl);
                        $response = $oauthClient->getLastResponse();
                    }

                }
            } catch (OAuthException $e) {
                print_r($e);
            }
            $data['invoiceId'] = $invoiceId;
            $data['response'] = $response;
            $this->_processInvoiceData($invoiceId,$response);
            $this->load->view("invoice/invoice_items",$data);
	}
        
        
        
        function addInvoice(){
            session_start();
            if (!isset($_SESSION['invoiceList'])){
                $_SESSION['invoiceList']= array();

            }
            //unset($_SESSION['invoiceList']);
            $len =  sizeof($_SESSION['invoiceList']);

            if (isset($_POST['invoiceId'])){
                $invoiceId = $_POST['invoiceId'];
                $isMatched = $_POST['isMatched'];
                $comments = $_POST['comments'];
                $jsonItems = $_POST['jsonItems'];
                $skuItems = json_decode($jsonItems,true);
                $orderId = $_POST['orderId'];
                //if ($currentStatus!='readyforshipping' && $currentStatus!='packed'){
                    $_SESSION['invoiceList'][$len]= array('invoiceId'=> $invoiceId, 'isMatched' => $isMatched,'comments'=>$comments);
                     $inv_data = array
                    (
                            
                            'status'=>'packed',
                            'comments'=>$comments,
                            'matched'=>$isMatched,
                            'last_updated_by'=>$this->username,
                             'packed_by'=>$this->username,
                             'magento_order_id'=>$orderId
                    );
                    $this->Invoice_master->update($invoiceId,$inv_data);
                    $this->Invoice_item->updateMultiple($skuItems,$invoiceId);
                //}
            }
            $data['invoiceListSession'] =$_SESSION['invoiceList'];
            $this->load->view("invoice/add_invoice",$data);
        }
        
        function ship(){
            session_start();
            if (!isset($_SESSION['invoiceList'])){
                $_SESSION['invoiceList']= array();

            }
            $len =  sizeof($_SESSION['invoiceList']);
     
            if (isset($_POST['invoiceId'])){
                //$this->load->h
                $invoiceId = $_POST['invoiceId'];
                 $orderId = $_POST['orderId'];
                $isMatched = $_POST['isMatched'];
                $comments = $_POST['comments'];
                $jsonItems = $_POST['jsonItems'];
                $skuItems = json_decode($jsonItems,true);
                
                
                /*check if this is coming because of page refresh or back button: Process only if it does not 
                exists in seesion.*/
                
                $this->load->helper('common_helper');
                $results = check_if_exists_in_array($_SESSION['invoiceList'],'invoiceId',$invoiceId);
                if (sizeof($results) == 0){ 
              
                    $_SESSION['invoiceList'][$len]= array('invoiceId'=> $invoiceId, 'isMatched' => $isMatched,'comments'=>$comments);
                    $inv_data = array
                        (

                                'status'=>'packed',
                                'comments'=>$comments,
                                'matched'=>$isMatched,
                                'last_updated_by'=>$this->username,
                                'packed_by'=>$this->username,
                                'magento_order_id'=>$orderId
                        );
                        $this->db->trans_start();
                        
                        $this->Invoice_master->update($invoiceId,$inv_data);
                        $this->Invoice_item->updateMultiple($skuItems,$invoiceId);
                        
                        $this->db->trans_complete();
                        
                        if ($this->db->trans_status() === FALSE)
                        {
                            //echo $this->db->_error_message();
                            die($invoiceId .' could not be packed. Please check log ');
                        }
                        $invoiceList = $_SESSION['invoiceList'];
                }

               
            }
             /*check if this is coming because of page refresh or back button: Process only if it does not 
                exists in seesion.*/
            
            if (sizeof($results) == 0){ 
                foreach ($invoiceList as $invoice){
                //$invoiceMgr->updateInvoiceMaster($invoice['invoiceId'],'readyforshipping',$invoice['comments'],$invoice['isMatched']);
                $inv_data_ready = array
                    (
                            
                            'status'=>'readyforshipping',
                            'comments'=>$invoice['comments'],
                            'matched'=>$invoice['isMatched'],
                            'last_updated_by'=>$this->username, 
                            'shipped_by'=>$this->username
                    );
                    $this->Invoice_master->update($invoice['invoiceId'],$inv_data_ready);
                    
            }
            }
            
            
            
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
            $data['invoiceListSession'] =$_SESSION['invoiceList'];
            $this->load->view("invoice/shipment",$data);
            
        }
        
        function confirmation(){
            session_start();
            $success = false;

            $deliveryPoint = $_POST['deliveryPointDD'];
            $deliveryVehicle= $_POST['deliveryVehicleDD'];
            //$comments = $_POST['comments'];
            $invoiceIDsJSON = $_POST['invoiceIDsJSON'];
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
                $this->Invoice_master->update($shipping['invoice_id'], $invoice_data_shipping);
                

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
            
            $data['shippingArray'] = $shippingArray;
            $data['trackingNumber'] = $trackingNumber;
            
            
            $this->load->view('invoice/confirmation',$data);
            
            

            //TODO: if everything fine unset the invoice List in session

        }
        
        function _processInvoiceData($invoiceId,$response){
            
            if ($invoiceId){
                $invoice_items = json_decode($response,true);
                //$conn = $database->connection
                $skus;
                $status ="invoiced";
                
                $this->db->trans_start();

                //first make an entry in invoice master
                if (!$this->Invoice_master->exists($invoiceId)) { //insert

                    $inv_data = array
                    (
                            'magento_invoice_increment_id'=>$invoiceId,
                            'status'=>$status,
                            'created_at'=>date('Y-m-d H:i:s'),
                            'last_updated_by'=>$this->username 
                            
                    );

                    $this->Invoice_master->insert($inv_data);

                }

                foreach($invoice_items as $item){
                    $sku = $item['sku'];

                    if (!$skus[$sku]['inv_no']){
                        $skus[$sku]['sku'] = $sku;
                        $skus[$sku]['name'] = $item['name'];
                        //var_dump($skus[$sku]['name']);
                        $skus[$sku]['entity_id'] = $item['entity_id'];
                        $skus[$sku]['inv_no']=1;
                    }
                    else{
                    $skus[$sku]['inv_no'] = $skus[$sku]['inv_no'] + 1;
                    }

                }

                foreach($skus as $sku){
                    $magento_entity_id = $sku['entity_id'];
                    $sku_no = $sku['sku'];
                    $name = $sku['name'];

                    $invoiced_number= $sku['inv_no'];
                
                    if (!$this->Invoice_item->exists($invoiceId,$sku_no)){
                        $inv_item_data = array
                        (
                                'magento_entity_id'=>$magento_entity_id,
                                'sku'=>$sku_no,
                                'name'=>$name,
                                'invoice_id'=>$invoiceId,
                                'invoiced_number'=>$invoiced_number,
                                'created_at'=>date('Y-m-d H:i:s'),
                                'packed_by'=>$this->username 
                        );
                        $this->Invoice_item->insert($inv_item_data);
                    }
                }
                $this->db->trans_complete();
                
                if ($this->db->trans_status() === FALSE)
                {
                    //echo $this->db->_error_message();
                    die( 'Transaction Failed while inserting invoice records. Please check log');
                }
            }
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

	
}
?>