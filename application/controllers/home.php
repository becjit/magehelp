<?php
require_once ("secure_area.php");

class Home extends Secure_area 
{
	function __construct()
	{
		parent::__construct('home','module');
                $this->load->model('Invoice_master');
                $this->load->model('Invoice_item');
	}
	
	function index()
	{
                
                $status ="invoiced";
                
                        $this->db->trans_start();
                        $magentoInvoices = $this->Invoice_master->getUnprocessedInvoicesFromMagento();

                        //first make an entry in invoice master
                        //if (!$this->Invoice_master->exists($invoiceId)) { //insert

                        foreach ($magentoInvoices as $inv){
                            $inv_entity_id = $inv['entity_id'];
                            $inv_increment_id = $inv['invoice_increment_id'];
                            $order_id = $inv['order_id'];
                            $order_increment_id = $inv['order_increment_id'];
                            $inv_data = array
                            (
                                    'magento_invoice_increment_id'=>$inv_increment_id,
                                    'magento_invoice_entity_id'=>$inv_entity_id,
                                    'magento_order_increment_id'=>$order_increment_id,
                                    'magento_order_id'=>$order_id,
                                    'status'=>$status,
                                    'created_at'=>date('Y-m-d H:i:s') 

                            );

                            $this->Invoice_master->insert($inv_data);

                        }

                        $items = $this->Invoice_item->getUnprocessedInvoiceItemsFromMagento();
                        foreach ($items as $item){
                            $magento_entity_id = $item['entity_id'];
                            $inv_entity_id = $item['parent_id'];
                            $inv_increment_id = $item['increment_id'];
                            $product_id = $item['product_id'];
                            $type = $item['type_id'];
                            $sku= $item['sku'];
                            $name = $item['name'];
                            $qty = $item['qty'];
                            $inv_item_data = array
                                (
                                        'magento_entity_id'=>$magento_entity_id,
                                        'magento_invoice_entity_id'=>$inv_entity_id,
                                        'invoice_id' =>$inv_increment_id,
                                        'sku'=>$sku,
                                        'name'=>$name,
                                        'type'=>$type,
                                        'magento_product_id'=>$product_id,
                                        'invoiced_number'=>$qty,
                                        'created_at'=>date('Y-m-d H:i:s'),
                                        
                                );
                            $this->Invoice_item->insert($inv_item_data);

                        }


                        $this->db->trans_complete();

                        if ($this->db->trans_status() === FALSE)
                        {
                            //echo $this->db->_error_message();
                            //die( 'Transaction Failed while inserting invoice records. Please check log');
                        }
		$this->load->view("home");
	}
	
	function logout()
	{
		$this->Employee->logout();
	}
        
         function loadProfile(){
           //change model 
            $loggedinfo = $this->User->get_logged_in_employee_info();
            $info = $this->User->getUserInfo($loggedinfo->person_id,true);

            $this->load->view("profile.php",$info);
        }
        function update(){
            $person_data['first_name'] = $_REQUEST['firstname'];
            $person_data['last_name'] = $_REQUEST['lastname'];
            $person_data['address_1'] = $_REQUEST['address1'];
            $person_data['address_2']= $_REQUEST['address2'];
            $person_data['city'] = $_REQUEST['city'];
            $password = $_REQUEST['password'];
            $person_data['zip'] = $_REQUEST['pin'];
            $person_data['state'] = $_REQUEST['state'];
            $person_data['phone_number'] = $_REQUEST['contactNumber'];
            $person_data['email'] = $_REQUEST['email'];
            $user_id = $_REQUEST['person_id'];
            if (!empty($password)){
                $user_data['password'] = md5($password);
            }
            $this->User->save($person_data,$user_data,$user_id);       
        }
            
}
?>