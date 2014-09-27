<?php
require_once ("secure_area.php");

class Procurement extends Secure_area 
{
        private $user;
        private $username;
        
	function __construct()
	{
    		parent::__construct('procurement');
                $this->user= $this->User->get_logged_in_employee_info();
                $this->username = $this->user->last_name." ".$this->user->first_name;
                $param = array('user' => $this->user->username);
                $this->load->library('acl-library/Acl',$param,'Acl');
                
	}
        //moved to parent class
//        function assignEntityToUser(){
//            $ids = $_REQUEST['ids'];
//            $entity = $_REQUEST['entity'];
//            $role = $_REQUEST['role'];
//            $user_id = $_REQUEST['user_id'];
//            if ($entity=='rfq'){
//                $model = 'Request_quote_master';
//            }
//            else if ($entity=='quote'){
//                $model = 'Purchase_quote_master';
//            }
//             else if ($entity=='order'){
//                $model = 'Purchase_order_master';
//            }
//            else if ($entity=='receipt_item'){
//                $model = 'Receipt_item';
//            }
//            else if ($entity=='receipt'){
//                $model = 'Receipt_master';
//            }
//            else if ($entity=='invoice'){
//                $model = 'Purchase_invoice_master';
//            }
//            if (!empty($ids) && !empty($role) ){
//                if (empty($user_id)){
//                    $user_id = $this->user->person_id;
//                }
//                if ($role =='approver'){
//                    $upd_data['approved_by'] = $user_id;
//                }
//                else if ($role =='receiver'){
//                    $upd_data['received_by'] = $user_id;
//                    $upd_data['owner_id'] = $user_id;
//                }
//                else if ($role =='payer'){
//                    $upd_data['payer_id'] = $user_id;
//                    $upd_data['owner_id'] = $user_id;
//                }
//                else if ($role =='owner'){
//                    $upd_data['owner_id'] = $user_id;
//                }
//                foreach($ids as $id){
//                    
//                    $this->$model->update(array('id'=>$id),$upd_data);
//                }
//            }
//        }
//        function prepareAssignDialog(){
////            var_dump(($this->User->getAllEligibleOwners('rfq')));
////            var_dump(($this->User->getAllEligibleApprovers('rfq')));
//            $entity = $_REQUEST['entity'];
//            if ($entity=='order'){
//                $owners = $this->User->getAllEligibleReceivers($_REQUEST['entity']);
//            }
//            else{
//                $owners = $this->User->getAllEligibleOwners($_REQUEST['entity']);
//            }
//            
//            
//            $approvers = $this->User->getAllEligibleApprovers($_REQUEST['entity']);
//            $adminUserOptions =null;
//            $userOptions = null;
//            foreach($owners as $owner) { 
//                
//                $name=$owner["username"]; 
//                $id=$owner["person_id"]; 
//                if (!empty($name)){
//                    $userOptions.="<OPTION VALUE=\"$id\">".$name;  
//                }
//            }
//            foreach($approvers as $approver) { 
//                
//                $name=$approver["username"]; 
//                $id=$approver["person_id"]; 
//                if (!empty($name)){
//                    $adminUserOptions.="<OPTION VALUE=\"$id\">".$name;  
//                }
//            }
//            $data['userOptions'] = $userOptions;
//            $data['adminUserOptions'] = $adminUserOptions;
//            echo json_encode($data);
//        }
        /* PURCHASE QUOTE GRID START */
        
	function index()
	{
            $users = $this->User->getAllUsersByRole(null,array('field_name'=>'role_name',array('Everyone','Guest')));
            
            foreach($users as $user) { 
                $user_id=$user["person_id"]; 
                $username=$user["username"]; 
                //$value = $denom["denom_json"];
                if (!empty($user_id)){
                    $userOptions.="<OPTION VALUE=\"$user_id\">".$username; 
                }             
            } 
            $data['userOptions'] = $userOptions;
            $this->load->view("procurement/quote/purchase_quote_grid",$data);
	}
        
        
       /* common functions */
        function populateSuppliers(){
            echo populateSuppliers();
        }
        
        function getCostPrice(){
            $id = $_REQUEST['productid'];
            $productDetails = $this->Product_price->getByProductId($id);
            echo $productDetails->cost_price;
        }
        
        function getPriceFromPricelist(){
            $product_id = $_REQUEST['product_id'];
            $pricelist_id = $_REQUEST['pricelist_id'];
            $supplier_id = $_REQUEST['supplier_id'];
            $results = $this->Supplier_rate_items->getPriceForProduct($product_id,$pricelist_id,$supplier_id);
            echo $results[0]['base_price'] ;
        }
        
        /* end common */
        
        /*start quote */
        
        function populateRFQ(){
            $quotedata=array();
            $status = $_REQUEST['_status'];
           
            $where = array();
            $in_where = array();
            setOwnerStatusCommon($where,$in_where);
            
            $griddata=  populateGridCommon('Request_quote_master',$where,null,$in_where);
            $dbrows = $griddata['db_data'];
            $data = $griddata['grid_metadata'];
            foreach ($dbrows as $dp){
               if ($status==self::WAITING_FOR_APPROVAL){
                   /* we dont need actions column in approval grid . So not passing the blank */
                   array_push($quotedata, array('id'=> $dp['id'],'dprow' => array($dp['reference'],$dp['supplier_name'],$dp['estimated_value'],$dp['owner_id'],$dp['status'],$dp['raised_by_name'],$dp['owner_name'],$dp['needed_by_date'],$dp['approved_by_name'],$dp['supplier_id'],$dp['warehouse_id'],$dp['approved_by'])));
               }
               //remove
//               else if ($status==self::REJECTED){
//                   /* Add Extra Columns Rejected and Rejected Notes Remove Status */
//                   array_push($quotedata, array('id'=> $dp['id'],'dprow' => array($dp['reference'],$dp['supplier_name'],$dp['estimated_value'],$dp['raised_by_name'],$dp['owner_name'],$dp['needed_by_date'],$dp['approved_by_name'],$dp['approval_notes'])));
//               }
//               //remove
//               else if ($status==self::OPEN){
//                   array_push($quotedata, array('id'=> $dp['id'],'dprow' => array('',$dp['reference'],$dp['supplier_name'],$dp['estimated_value'],$dp['status'],$dp['raised_by_name'],$dp['owner_name'],$dp['needed_by_date'])));
//               }
               else if (is_array($status)){
                   array_push($quotedata, array('id'=> $dp['id'],'dprow' => array($dp['reference'],$dp['supplier_name'],$dp['estimated_value'],$dp['owner_id'],$dp['status'],$dp['raised_by_name'],$dp['owner_name'],$dp['needed_by_date'],$dp['approved_by_name'],$dp['supplier_id'],$dp['warehouse_id'])));
               }
               else {
                   array_push($quotedata, array('id'=> $dp['id'],'dprow' => array($dp['reference'],$dp['supplier_name'],$dp['estimated_value'],$dp['status'],$dp['raised_by_name'],$dp['owner_name'],$dp['needed_by_date'],$dp['notes'],$dp['approved_by_name'],$dp['approval_notes'])));
               }
           }
           $data['quotedata'] = $quotedata;
           echo json_encode($data);
        }
        function createRFQ(){
            $id = $_REQUEST['quoteId'];
            $purchase_quote_data['supplier_id'] = $_REQUEST['supplierId'];
            $purchase_quote_data['warehouse_id'] = $_REQUEST['warehouseId'];
            $dateObj = DateTime::createFromFormat('d/m/Y', $_REQUEST['reqdate']);
            log_message('debug','converted '.$dateObj->format('Y-m-d'));
            $purchase_quote_data['needed_by_date'] = $dateObj->format('Y-m-d');
            $comments = appendComments($_REQUEST['notes'], 'notes');
            $purchase_quote_data['owner_id'] = $this->user->person_id;
            
            if (empty($id)){
                $purchase_quote_data['raised_by'] = $this->user->person_id;
                $id =  $this->Request_quote_master->createRequest($purchase_quote_data,array('notes'=>$comments));
            }
            else {
                $where_clause = array('id'=>$id);
                $this->Request_quote_master->update($where_clause, $purchase_quote_data,array('notes'=>$comments));
            }
            echo $id;
        }
        //this function validates the record after close button of dialog being pressed. If no items are aadded the record is deleted
        function closeValidate(){
            $id = $_REQUEST['id'];   
            $entity = $_REQUEST['entity'];   
            $items_count = $_REQUEST['items_count'];  
            if ($entity=='quote'){
                $model = 'Purchase_quote_master';
                $itemModel= 'Purchase_quote_item';
                $where_clause_quote_item = array('quote_id'=>$id);
            }
            else if ($entity=='rfq'){
                $model = 'Request_quote_master';
                $itemModel= 'Request_quote_item';
                $where_clause_quote_item = array('rfq_id'=>$id);
            } 
            
            if (!empty($id)){
               
                if ($items_count>0){

                    $where_clause_quote = array('id'=>$id);
                    $this->$model->update($where_clause_quote,array('status'=>self::OPEN));
                    $this->$itemModel->update($where_clause_quote_item,array('status'=>self::OPEN));
                }
            }
        }
        
        function modifyRFQ (){
            $id = $_REQUEST['id'];
            
            $oper = $_REQUEST['oper'];
            $this->db->trans_start();
            if ($oper=='edit'){
                if (!empty($_REQUEST['needed_date'])){
                    $dateObj = DateTime::createFromFormat('d/m/Y', $_REQUEST['needed_date']);
                    log_message('debug','converted item date '.$dateObj->format('Y-m-d'));
                    $purchase_quote_data['needed_by_date'] = $dateObj->format('Y-m-d');
                }
                
                if (!empty($_REQUEST['warehouse_id'])){
                    $purchase_quote_data['warehouse_id'] = $_REQUEST['warehouse_id'];
                }
                $comments = appendComments($_REQUEST['notes'], 'notes');
                //while editing the supplier id is passed.
                $purchase_quote_data['supplier_id'] = $_REQUEST['supplier_name'];
                $where_clause_quote = array('id'=>$id);
                
                $this->Request_quote_master->update($where_clause_quote,$purchase_quote_data,array('notes'=>$comments));
                
            }
            else if ($oper=='del'){
                $idAraay =  $_REQUEST['id'];
                foreach($idAraay as $tempId){
                    $where_clause_quote = array('id'=>$tempId);
                    $this->Request_quote_master->update($where_clause_quote,array('status'=>'cancelled'));

                    $where_clause_quote_item = array('rfq_id'=>$tempId);
                    $this->Request_quote_item->update($where_clause_quote_item,array('status'=>'cancelled'));
                }
                
            }
            $this->db->trans_complete();
        }
       
	function addRFQItemBulk (){
            //$id = $_REQUEST['quoteId'];
            $data= $_REQUEST['data'];
            $purchase_quote_data['rfq_id'] = $_REQUEST['rfq'];
            $this->db->trans_start();
            foreach($data as $itemDetails){
                $purchase_quote_data['product_id'] = $itemDetails['id'];
                $purchase_quote_data['requested_quantity'] = $itemDetails['quantity'];
                $this->createRFQItem($purchase_quote_data);
            }
            $this->db->trans_complete();
        }
        
        function addRFQItem (){
            //$id = $_REQUEST['quoteId'];
            $product_id = $_REQUEST['productid'];
            $purchase_quote_data['rfq_id'] = $_REQUEST['rfq'];
            $purchase_quote_data['product_id'] = $product_id;
            $needed_by = $_REQUEST['needeedByDate'];
            if (!empty($needed_by)){
                $dateObj = DateTime::createFromFormat('d/m/Y', $needed_by);
                log_message('debug','converted item date '.$dateObj->format('Y-m-d'));
                $purchase_quote_data['needed_by_date'] = $dateObj->format('Y-m-d');
            }
            $purchase_quote_data['requested_quantity'] = $_REQUEST['quantity'];
            $purchase_quote_data['expected_price'] = $_REQUEST['exprice'];
            $purchase_quote_data['estimated_value'] = $_REQUEST['quantity']*$_REQUEST['exprice'];
            $purchase_quote_data['comments'] = $_REQUEST['descItem'];
            $id =  $this->createRFQItem($purchase_quote_data);
        }
        //business logic
        function createRFQItem($purchase_quote_data){
            $this->db->trans_start();
            /* insert into quote item */
            
            $this->Request_quote_item->insert($purchase_quote_data);
            log_message('debug','insert statement ='.$this->db->last_query());
            $id = $this->db->insert_id();
            /* end insert  */
            
            /* update reference number in  quote item */
            $where_clause = array('id'=>$id);
            $this->Request_quote_item->update($where_clause, array('reference' => 10000000 + $id));
            /* end update */
            
            /* update estimated value in quote master */
            $rfq_id = $purchase_quote_data['rfq_id'];
            $quote_details=$this->Request_quote_master->getById($rfq_id);
            $estimated_value = $quote_details->estimated_value + $purchase_quote_data['estimated_value'];
            $upd_data['estimated_value'] = $estimated_value;
            if ($quote_details->status==self::DRAFT){
                //if the status is draft that means previously there was no items and for the first time we are adding items.
                // so we set the status as open
                $upd_data['status'] = self::OPEN;
            }
            $this->Request_quote_master->update(array('id'=>$rfq_id),$upd_data);
            log_message('debug','update statement ='.$this->db->last_query());
            /* end update estimated value in quote master */
            
            $this->db->trans_complete();
            return $id;
        }
        function modifyRFQItem (){
            $id = $_REQUEST['line_id'];
            $item_details=$this->Request_quote_item->getById($id);
            $rfq_id = $item_details->rfq_id;
            $current_est_value = $item_details->estimated_value;
            $oper = $_REQUEST['oper'];
            $this->db->trans_start();
            if ($oper=='edit'){
                if (!empty($_REQUEST['needed_by_date'])){
                    $dateObj = DateTime::createFromFormat('d/m/Y', $_REQUEST['needed_by_date']);
                    log_message('debug','converted item date '.$dateObj->format('Y-m-d'));
                    $purchase_quote_data['needed_by_date'] = $dateObj->format('Y-m-d');
                }
                $purchase_quote_data['requested_quantity'] = $_REQUEST['quantity'];
                $purchase_quote_data['expected_price'] = $_REQUEST['expected_price'];
                $comments = appendComments($_REQUEST['comments'], 'comments');
                $purchase_quote_data['estimated_value'] = $_REQUEST['requested_quantity']*$_REQUEST['expected_price'];

                $where_clause = array('id'=>$id);
                
                $this->Request_quote_item->update($where_clause,$purchase_quote_data,array('comments'=>$comments));
                
                $quote_details=$this->Request_quote_master->getById($rfq_id);
                $estimated_value = $quote_details->estimated_value - $current_est_value + $purchase_quote_data['estimated_value'];
                $this->Request_quote_master->update(array('id'=>$rfq_id),array('estimated_value'=>$estimated_value));
            }
            else if ($oper=='del'){
                $quote_details=$this->Request_quote_master->getById($rfq_id);
                $estimated_value = $quote_details->estimated_value - $item_details->estimated_value;
                $this->Request_quote_master->update(array('id'=>$rfq_id),array('estimated_value'=>$estimated_value));
                $where_clause = array('id'=>$id);
                $this->Request_quote_item->update($where_clause,array('status'=>'cancelled'));
                
            }
            $this->db->trans_complete();
            
        }
        
        function populateRFQItems(){
            $quoteid = $_REQUEST['quoteId'];
            if (!empty($quoteid)){
                $quotedata = array();
                $where = array('rfq_id' => $quoteid );
                $griddata=  populateGridCommon('Request_quote_item',$where);
                $dbrows = $griddata['db_data'];
                $data = $griddata['grid_metadata'];

                foreach ($dbrows as $dbrow){
                    array_push($quotedata, array('id'=> $dbrow['id'],'dprow' => array($dbrow['name'],$dbrow['requested_quantity'],$dbrow['needed_by_date'],$dbrow['expected_price'],$dbrow['estimated_value'],$dbrow['comments'])));
                }
                $data['quoteitemdata'] = $quotedata;
                echo json_encode($data);
           }
           
        }
        
        function generateQuoteFromRFQ(){
            $ids= $_REQUEST['ids'];
            
            foreach($ids as $id){
                
                log_message('debug',' id '.$id);
                $rfq_details=$this->Request_quote_master->getById($id,null,true);
                if ($rfq_details['status']==self::OPEN) {
                    $this->db->trans_start();
                    //$this->Request_quote_master->update(array('id'=>$id),array('approved_by'=>$this->user->person_id));
                    $this->generateQuoteInternal($rfq_details);
                    $this->db->trans_complete();
                }
                 
            }
           
        }
        
        function getCommentsForEntity(){
             $entity = $_REQUEST['entity']; 
             $id = $_REQUEST['id'];
             
             if ($entity == 'rfq'){
                 $model = 'Request_quote_master';
                 $col_array = array('notes','approval_notes');
             }
             else if ($entity == 'quote'){
                 $model = 'Purchase_quote_master';
                 $col_array = array('notes','approval_notes');
             }
              else if ($entity == 'order'){
                 $model = 'Purchase_order_master';
                 $col_array = array('receiving_notes');
                 
             }
             else if ($entity == 'receipt_item'){
                 $model = 'Receipt_item';
                 $col_array = array('receiving_notes','pp_approval_notes');
                 
             }
             
             if (!empty($id)){
                 
                     
                 $details=$this->$model->getById($id,$col_array,true);
                 echo json_encode($details);
             }
             
        }
        function addCommentsForEntity(){
             $entity = $_REQUEST['entity']; 
             $id = $_REQUEST['id'];
             $note_type = $_REQUEST['note_type'];
//             if ($entity == 'rfq'){
//                 $model = 'Request_quote_master';
//                 $col_array = array('notes','approval_notes');
//             }
//             else if ($entity == 'quote'){
//                 $model = 'Purchase_quote_master';
//                 $col_array = array('notes','approval_notes');
//             }
//              else if ($entity == 'order'){
//                 $model = 'Purchase_order_master';
//                 $col_array = array('receiving_notes');
//                 
//             }
             if ($entity == 'receipt_item'){
                 $model = 'Receipt_item';
                 if ($note_type=='pp_approval'){
                     $comments = appendComments($_REQUEST['notes'], 'pp_approval_notes');
                 }
                 
             }
             
             if (!empty($id)){
                    
                 $this->$model->update($id,null,array('pp_approval_notes'=>$comments));
             }
             
        }
        
        function generatePOFromQuote(){
            $ids= $_REQUEST['ids'];
            
            foreach($ids as $id){
                
                log_message('debug',' id '.$id);
                $quote_details=$this->Purchase_quote_master->getById($id,null,true);
                if ($quote_details['status']==self::OPEN ||$quote_details['status']==self::WAITING_FOR_APPROVAL ) {
                    $this->db->trans_start();
                    $this->Purchase_quote_master->update(array('id'=>$id),array('approved_by'=>$this->user->person_id));
                    $this->generatePOInternal($quote_details);
                    $this->db->trans_complete();
                }
                 
            }
           
        }
        
        /* PURCHASE QUOTE GRID END*/
        
        /* APPROVAL GRUD START */
        
        function approveOrReject(){
            $id= $_REQUEST['quoteId'];
            $action= $_REQUEST['action'];
            $entity= $_REQUEST['entity'];
            if ($entity=='quote'){
                $model = 'Purchase_quote_master';
                $itemModel= 'Purchase_quote_item';
                $parent_id = 'quote_id';
                
                }
                else if ($entity=='rfq'){
                    $model = 'Request_quote_master';
                    $itemModel= 'Request_quote_item';
                    $parent_id = 'rfq_id';
                    
                } 
            log_message('debug',' id '.$id);
            log_message('debug',' action '.$action);
            $quote_details=$this->$model->getById($id,null,true);
            if ($quote_details['status']==self::WAITING_FOR_APPROVAL ) {
                if ($action == 'approve'){
                    $this->db->trans_start();
                    
                    if ($entity=='quote'){
                        $this->$model->update(array('id'=>$id),array('approved_by'=>$this->user->person_id,'approval_notes'=>$_REQUEST['quote_approval_notes']));
                        $this->generatePOInternal($quote_details);
                    }
                    else if ($entity=='rfq'){
                        $this->$model->update(array('id'=>$id),array('approved_by'=>$this->user->person_id,'approval_notes'=>$_REQUEST['quote_approval_notes'],'status'=>self::SUBMITTEDTOQUOTE));
                        $this->generateQuoteInternal($quote_details);
                    }
                    $this->db->trans_complete();
                }
                else if ($action == 'reject'){
                    $this->db->trans_start();
                    $this->$model->update(array('id'=>$id),array('status'=>self::REJECTED,'approved_by'=>$this->user->person_id,'approval_notes'=>$_REQUEST['quote_approval_notes']));
                    $item_details=$this->$itemModel->getByQuoteId($id);
                    foreach ($item_details as $item_data){
                            $this->$itemModel->update(array('id'=>$item_data['id']),array('status'=>self::REJECTED));
                    }
                    $this->db->trans_complete();
                }
                
            }
 
        }
        function approveQuotesInBulk(){
            $ids= $_REQUEST['ids'];
           // $this->db->trans_start();
            foreach($ids as $id){
                $quote_details=$this->Request_quote_master->getById($id,null,true);
                if ($quote_details['status']==self::OPEN ||$quote_details['status']==self::WAITING_FOR_APPROVAL ) {
                    $this->db->trans_start();
                    $this->Request_quote_master->update(array('id'=>$id),array('approved_by'=>$this->user->person_id,'status'=>self::SUBMITTEDTOQUOTE));             
                    $this->generateQuoteInternal($quote_details);
                    $this->db->trans_complete();
                }
            }
            //$this->db->trans_complete();
           
        }
        
        /* APPROVAL GRID END */
        
        /* REJECTED GRID START*/
        function reopen (){
            $ids= $_REQUEST['ids'];
            $entity= $_REQUEST['entity'];
            if ($entity=='quote'){
                $model = 'Purchase_quote_master';
                $itemModel= 'Purchase_quote_item';
                $parent_id = 'quote_id';
                
                }
                else if ($entity=='rfq'){
                    $model = 'Request_quote_master';
                    $itemModel= 'Request_quote_item';
                    $parent_id = 'rfq_id';
                    
                } 
            
            foreach($ids as $id){
                
                log_message('debug',' id '.$id);
                $quote_details=$this->$model->getById($id,null,true);
                if ($quote_details['status']==self::REJECTED ) {
                    $this->db->trans_start();
                    $this->$model->update(array('id'=>$id),array('status'=>self::OPEN,'last_updated_by'=>$this->user->person_id));
                    $item_details=$this->$itemModel->getByQuoteId($id);
                    foreach ($item_details as $item_data){
                        
                            $this->$itemModel->update(array('id'=>$item_data['id']),array('status'=>self::OPEN));
                        
                    }
                    $this->db->trans_complete();
                }
                 
            }
        }
        /* REJECTED GRID END*/
        
        /* COMMON FUNCTIONS FOR RFQ GRIDS  */
        private function generatePOInternal($quote_details){
            
            $id=$quote_details['id'];
            $order_data['owner_id'] = $quote_details['owner_id'];
            $order_data['needed_by_date'] = $quote_details['needed_by_date'];
            $order_data['generated_by'] = $this->user->person_id;
            $order_data['quote_id'] = $quote_details['id'];
            log_message('debug',json_encode($order_data));
            $order_id = $this->createOrder($order_data);
            log_message('debug',$this->db->last_query());
            $this->Purchase_quote_master->update(array('id'=>$id),array('status'=>self::ORDERED,'order_id'=>$order_id));
            log_message('debug',$this->db->last_query());
            //now update line item status
            $item_details=$this->Purchase_quote_item->getByQuoteId($id);
            foreach ($item_details as $item_data){
                if ($item_data['status'] == self::OPEN || $item_data['status'] == self::WAITING_FOR_APPROVAL){
                    $order_item_data['name'] = $item_data['name'];
                    $order_item_data['sku'] = $item_data['sku'];
                    $order_item_data['product_id'] = $item_data['product_id'];
                    $order_item_data['estimated_value'] = $item_data['estimated_value'];
                    $order_item_data['needed_by_date'] = $item_data['needed_by_date'];
                    $order_item_data['quoted_quantity'] = $item_data['quoted_quantity'];
                    //repeatative ..as of now as no difference between quoted and ordered quantity
                    $order_item_data['ordered_quantity'] = $item_data['quoted_quantity'];
                    $order_item_data['expected_price'] = $item_data['expected_price'];
                    $order_item_data['order_id'] =$order_id;
                    $order_item_data['quote_line_id'] = $item_data['id'];
                    $order_line_id = $this->createOrderItem($order_item_data);
                    $this->Purchase_quote_item->update(array('id'=>$item_data['id']),array('status'=>self::ORDERED,'order_line_id'=>$order_line_id));
                }
            }
            
        }
        public function createOrder($purchase_quote_data){
            $this->db->trans_start();
            $this->Purchase_order_master->insert($purchase_quote_data);
            $id = $this->db->insert_id();
            $where_clause = array('id'=>$id);
            $this->Purchase_order_master->update($where_clause, array('reference' => 10000000 + $id));
            $this->db->trans_complete();
            return $id;
        }
        
        /*end quote */
        
        /* RECEIVING GRID START */
        
        function populatePOToReceive(){
//            $person_id = $this->user->person_id;
            //$in_where=array('field_name'=>'status','value_array'=>array(self::OPEN,self::RECEIVING,self::RECEIVED)) ;
            $this->populatePOInternal();
            
        }
        
        /* RECEIVING GRID END */
        
        function populatePayments(){
            $invoiceId = $_REQUEST['invoiceId'];
            $orderId = $_REQUEST['orderId'];
            $type = $_REQUEST['type'];
            if (!empty($invoiceId) || !empty($orderId)){
                $paymentdata = array();
                if (!empty($invoiceId)){
                    $where['invoice_id'] = $invoiceId;
                }
                if (!empty($orderId)){
                    $where['order_id'] = $orderId;
                }
                 if (!empty($type)){
                    $where['payment_type'] = $type;
                }
                
                $griddata=  populateGridCommon('Outgoing_payment',$where);
                $dbrows = $griddata['db_data'];
                $data = $griddata['grid_metadata'];

                foreach ($dbrows as $dbrow){
                    $isAdvance = 'No';
                    if ($dbrow['parent_id'] != 0){
                        // we have advance payment
                        $isAdvance = 'Yes';
                    }
                    array_push($paymentdata, array('id'=> $dbrow['id'],'dprow' => array($dbrow['payment_reference'],$dbrow['payment_mode'],$dbrow['payment_type'],$dbrow['amount'],$dbrow['comments'],$isAdvance,$dbrow['parent_id'])));
                }
                $data['paymentdata'] = $paymentdata;
                echo json_encode($data);
            }
        }
        
        
        function populateReceipts(){
            $where = array();
           $in_where = array();
           $status = $_REQUEST['_status'];
           //default status
           if (empty($status)){
               //$where['invoice_id'] = $invoice_id;
               $in_where=array('field_name'=>'status','value_array'=>array(self::READYTOINVOICEMEMO)) ;
               
           }
           setOwnerStatusCommon($where,$in_where);
           
           
           $page = $_REQUEST['page'];
           $limit = $_REQUEST['rows'];
           $sidx = $_REQUEST['sidx'];
           $sord = $_REQUEST['sord'];
           $searchOn = $_REQUEST['_search'];
           
           if (!empty($person_id)){
                $where['owner_id'] = $person_id;
           }
           
           //standard response parameters 
           $quotedata = array();
           $count = $this->Receipt_master->totalNoOfRows($where,null,null,$in_where);
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
                    if ($field == 'order_reference' ){
                        $orderDetails = $this->Purchase_order_master->getGridByReference($input);
                        $like_condition['order_id'] = $orderDetails['id'];
                    }
                    else if ($field == 'quote_reference'){
                        $orderDetails = $this->Purchase_order_master->getGridByQuoteReference($input);
                        $like_condition['order_id'] = $orderDetails['id'];
                    }
                    else if ($field == 'supplier_name'){
                        $supplierDetails = $this->Supplier->getByName($input);
                        $like_condition['supplier_id'] = $supplierDetails['id'];
                    }
                    else {
                        $like_condition[$field] = trim($input);
                    }
                    
                }
                $quotes = $this->Receipt_master->getAll(false,$where,null,$clauses,$like_condition,$in_where);
            }
            else {
                $quotes = $this->Receipt_master->getAll(false,$where,null,null,null,$in_where);
            }
           
           foreach ($quotes as $dp){
               $orderDetails = $this->Purchase_order_master->getGridById($dp['order_id']);
               
               array_push($quotedata, array('id'=> $dp['id'],'dprow' => array($dp['reference'],$dp['supplier_receipt_number'],$orderDetails['supplier_name'],$dp['status'],$dp['order_id'],$orderDetails['reference'],$orderDetails['quote_reference'],$dp['owner_name'],$dp['owner_id'],$dp['approved_by_name'],$dp['approved_by'])));
           }
           $data['quotedata'] = $quotedata;
           echo json_encode($data);
        }
        
       
        
        function populatePOInternal(){
           
           $where = array();
           $in_where = array();
           setOwnerStatusCommon($where,$in_where);
           
           
           //standard response parameters 
           $orderdata = array();
           $griddata=  populateGridCommon('Purchase_order_master',$where,null,$in_where);
           $dbrows = $griddata['db_data'];
           $data = $griddata['grid_metadata'];
           
           foreach ($dbrows as $dbrow){
               array_push($orderdata, array('id'=> $dbrow['id'],'dprow' => array($dbrow['reference'],$dbrow['quote_reference'],$dbrow['supplier_name'],$dbrow['estimated_value'],$dbrow['owner_id'],$dbrow['status'],$dbrow['raised_by_name'],$dbrow['order_generated_by_name'],$dbrow['owner_name'],$dbrow['needed_by_date'],$dbrow['received_by_name'],$dbrow['approved_by_name'],$dbrow['supplier_id'],$dbrow['warehouse_id'],$dbrow['received_by'])));
           }
           $data['orderdata'] = $orderdata;
           echo json_encode($data);
        }
        
        
        function validateReceiving(){
            $order_id = $_REQUEST['order_id'];
            // this note is for order 
            $receiving_notes = $_REQUEST['receiving_notes'];
            //check the no of order line items with status receiving. 
            $totalNoOfRowsByOrderIdReceived = $this->Purchase_order_item->totalNoOfRows(array('order_id'=>$order_id,'status'=>self::RECEIVED));
             $totalNoOfRowsByOrderIdTotal = $this->Purchase_order_item->totalNoOfRows(array('order_id'=>$order_id));
             if ($totalNoOfRowsByOrderIdReceived == $totalNoOfRowsByOrderIdTotal){
                 // all the order lines are received       
                 
                 $this->Purchase_order_master->update(array('id'=>$order_id,'status'=>self::RECEIVING),array('status'=>self::RECEIVED,'receiving_notes'=>$receiving_notes));
                 $this->Receipt_master->update(array('order_id'=>$order_id,'status'=>self::RECEIVING),array('status'=>self::RECEIVED));
             }
             else {
                  $this->Purchase_order_master->update(array('id'=>$order_id,'status'=>self::RECEIVING),array('receiving_notes'=>$receiving_notes));
             }
            

        }
        
        function modifyOrder (){
            $id = $_REQUEST['id'];
            
            $oper = $_REQUEST['oper'];
            $this->db->trans_start();
            if ($oper=='edit'){
                //$dateObj = DateTime::createFromFormat('d/m/Y', $_REQUEST['needed_by_date']);
                //log_message('debug','converted item date '.$dateObj->format('Y-m-d'));
                $purchase_quote_data['needed_by_date'] = $_REQUEST['needed_by_date'];
                $purchase_quote_data['supplier_name'] = $_REQUEST['supplier_name'];
                $where_clause_quote = array('id'=>$id);
                
                $this->Purchase_quote_master->update($where_clause_quote,$purchase_quote_data);
                
            }
            else if ($oper=='del'){
                $idAraay =  explode(",", $id);
                foreach($idAraay as $tempId){
                    $where_clause_quote = array('id'=>$tempId);
                    $this->Purchase_quote_master->update($where_clause_quote,array('status'=>'cancelled'));

                    $where_clause_quote_item = array('quote_id'=>$tempId);
                    $this->Purchase_quote_item->update($where_clause_quote_item,array('status'=>'cancelled'));
                }
                
            }
            $this->db->trans_complete();
        }
        
        function addOrderItem (){
            //$id = $_REQUEST['quoteId'];
            $product_id = $_REQUEST['productid'];
            $purchase_quote_data['quote_id'] = $_REQUEST['orderId'];
            $purchase_quote_data['product_id'] = $product_id;
            $dateObj = DateTime::createFromFormat('d/m/Y', $_REQUEST['needeedByDate']);
            log_message('debug','converted item date '.$dateObj->format('Y-m-d'));
            $purchase_quote_data['needed_by_date'] = $dateObj->format('Y-m-d');
            $purchase_quote_data['quoted_quantity'] = $_REQUEST['quantity'];
            $purchase_quote_data['expected_price'] = $_REQUEST['exprice'];
            $purchase_quote_data['estimated_value'] = $_REQUEST['quantity']*$_REQUEST['exprice'];
            $purchase_quote_data['comments'] = $_REQUEST['descItem'];
            $productDetails = $this->Product->getByProductId($product_id);
            $purchase_quote_data['sku'] = $productDetails->barcode;
            $purchase_quote_data['name'] = $productDetails->product_name;
            $id =  $this->createQuoteItem($purchase_quote_data);
        }
        //business logic
        function createOrderItem($purchase_quote_data){
            $this->db->trans_start();
            /* insert into quote item */
            
            $this->Purchase_order_item->insert($purchase_quote_data);
            log_message('debug','insert statement ='.$this->db->last_query());
            $id = $this->db->insert_id();
            /* end insert  */
            
            /* update reference number in  quote item */
            $where_clause = array('id'=>$id);
            $this->Purchase_order_item->update($where_clause, array('reference' => 10000000 + $id));
            /* end update */
            
            $this->db->trans_complete();
            return $id;
        }
        function modifyOrderItem (){
            $id = $_REQUEST['id'];
            $item_details=$this->Purchase_quote_item->getById($id);
            $quote_id = $item_details->quote_id;
            $current_est_value = $item_details->estimated_value;
            $oper = $_REQUEST['oper'];
            $this->db->trans_start();
            if ($oper=='edit'){
                //$dateObj = DateTime::createFromFormat('d/m/Y', $_REQUEST['needed_by_date']);
                //log_message('debug','converted item date '.$dateObj->format('Y-m-d'));
                $purchase_quote_data['needed_by_date'] = $_REQUEST['needed_by_date'];
                $purchase_quote_data['quoted_quantity'] = $_REQUEST['quoted_quantity'];
                $purchase_quote_data['expected_price'] = $_REQUEST['expected_price'];
                $purchase_quote_data['comments'] = $_REQUEST['comments'];
                $purchase_quote_data['estimated_value'] = $_REQUEST['quoted_quantity']*$_REQUEST['expected_price'];

                $where_clause = array('id'=>$id);
                
                $this->Purchase_quote_item->update($where_clause,$purchase_quote_data);
                
                $quote_details=$this->Purchase_quote_master->getById($quote_id);
                $estimated_value = $quote_details->estimated_value - $current_est_value + $purchase_quote_data['estimated_value'];
                $this->Purchase_quote_master->update(array('id'=>$quote_id),array('estimated_value'=>$estimated_value));
            }
            else if ($oper=='del'){
                $quote_details=$this->Purchase_quote_master->getById($quote_id);
                $estimated_value = $quote_details->estimated_value - $item_details->estimated_value;
                $this->Purchase_quote_master->update(array('id'=>$quote_id),array('estimated_value'=>$estimated_value));
                $where_clause = array('id'=>$id);
                
                $this->Purchase_quote_item->update($where_clause,array('status'=>'cancelled'));
            }
            $this->db->trans_complete();
            
        }
        
        function populateOrderItems(){
            $orderid = $_REQUEST['orderId'];
            if (!empty($orderid)){
                $orderitemdata = array();
                $where = array('order_id' => $orderid );
                $griddata=  populateGridCommon('Purchase_order_item',$where);
                $dbrows = $griddata['db_data'];
                $data = $griddata['grid_metadata'];

                foreach ($dbrows as $dbrow){
                    array_push($orderitemdata, array('id'=> $dbrow['id'],'dprow' => array($dbrow['product_id'],$dbrow['name'],$dbrow['quoted_quantity'],$dbrow['received_quantity'],$dbrow['returned_quantity'],$dbrow['cnbd_quantity'],$dbrow['pp_quantity'],$dbrow['needed_by_date'],$dbrow['expected_price'],$dbrow['estimated_value'],$dbrow['received_value'],$dbrow['returned_value'],$dbrow['pp_value'],$dbrow['comments'])));
                }
                $data['orderitemdata'] = $orderitemdata;
                echo json_encode($data);
           }         
           
        }
        
        function populateReceiptItems(){
            $receiptId = $_REQUEST['receiptId'];
            $orderLineId = $_REQUEST['orderLineId'];
            $receipt_line_value_in =array();
            $populateGrid= true;
            // as of now keep default oper as "receipt"
            $oper = "receipt";
            
            if (!empty($_REQUEST['oper'])){
                $oper = $_REQUEST['oper'];
            }
            $where = array();
            $in_where = array();
            setOwnerStatusCommon($where,$in_where);
            //standard response parameters 

            $quotedata = array();
            if ($oper=="receipt"){
                
               $where['receipt_id'] = $receiptId ; 
            }
            else if ($oper=="orderline"){
               $where['order_line_id'] =  $orderLineId ;  
            }
//            else if ($oper=="pp"){
//               $where= array(/*'pp_quantity >' => 0 ,*/'owner_id'=>$this->user->person_id,'status !='=>self::COMPLETE);  
//            }
            else if ($oper=="pp_approve"){
               $where['pp_quantity >']=  0;  
               $receipt_line_value_array = array_unique($this->Receipt_partpayment->getAll(false,array('status'=>self::WAITING_FOR_APPROVAL),array('receipt_line_id')));
               //dont fir the query if there is no pp item with wfa mode
               if (count($receipt_line_value_array)==0){
                   $populateGrid = false;
               }
               foreach ($receipt_line_value_array as $value){
                   array_push($receipt_line_value_in,$value['receipt_line_id']);
               }
              
            }
            if ($populateGrid===true){
                $griddata=  populateGridCommon('Receipt_item',$where,null,array('field_name'=>'id','value_array'=>$receipt_line_value_in));
                $quotes = $griddata['db_data'];
                $data = $griddata['grid_metadata'];


                foreach ($quotes as $dp){
                    $receipt_data = $this->Receipt_master->getById($dp['receipt_id'],array('supplier_receipt_number','reference','owner_id','owner_name'),true);
                    $po_item_data = $this->Purchase_order_item->getById($dp['order_line_id'],array('reference','order_id'),true);
                    //$test = $po_item_data['reference'];
                    array_push($quotedata, array('id'=> $dp['id'],'dprow' => array($dp['reference'],$dp['batch_number']/*receipt line reference*/,$receipt_data['supplier_receipt_number'],$receipt_data['reference'],/*receipt  reference*/
                       $dp['receipt_id'],$po_item_data['order_id'], $dp['order_line_id'],$po_item_data['reference'],$dp['product_id'],$dp['name'],$dp['expiry_date'],$dp['vat'],$dp['ordered_quantity'],$dp['received_quantity'],$dp['received_value'],$dp['pp_quantity'],$dp['pp_value'],$dp['returned_quantity'],$dp['returned_value'],$dp['pp_status'],/*$dp['receiving_notes'],$dp['returned_notes'],*/$receipt_data['owner_name'],$receipt_data['owner_id'],$dp['approved_by_name'],$dp['approved_by'])));
                }
            }
            
            $data['receiptitemdata'] = $quotedata;
            echo json_encode($data);
                    
           
        }
        
        function populatePartpaymentItems(){
            $ppdata = array();
            $receipt_line_id= $_REQUEST['receipt_line_id'];
            
            $where = array('receipt_line_id' => $receipt_line_id );  
            $in_where['field_name'] = 'status' ;
            $in_where['value_array'] = $_REQUEST['_status'] ;
            
            
            $griddata=  populateGridCommon('Receipt_partpayment',$where,array('id','receipt_id','receipt_line_id',
                'pp_quantity','pp_value','pp_notes','status'),$in_where);
            $dbrows = $griddata['db_data'];
            $data = $griddata['grid_metadata'];
            
            
            foreach ($dbrows as $row){
                array_push($ppdata, array('id'=> $row['id'],'dprow' => array($row['receipt_id'],$row['receipt_line_id'],
                   $row['pp_quantity'], $row['pp_value'],$row['pp_notes'],$row['status'])));
            }
            $data['ppdata'] = $ppdata;
            echo json_encode($data);
                    
           
        }
        
        function getOrderDetails(){
            $orderId=$_REQUEST['orderId'];
            if (!empty($orderId)){
                $order = $this->Purchase_order_master->getGridById($orderId);
                echo json_encode($order);
            }


        }
        function getQuoteDetails(){
            $quoteId=$_REQUEST['quoteId'];
            if (!empty($quoteId)){
                $quote = $this->Purchase_quote_master->getGridById($quoteId);
                echo json_encode($quote);
            }


        }
        function getRFQDetails(){
            $quoteId=$_REQUEST['quoteId'];
            if (!empty($quoteId)){
                $quote = $this->Request_quote_master->getGridById($quoteId);
                echo json_encode($quote);
            }


        }
        function loadPOGrid()
	{
            
            $this->load->view("procurement/purchaseorder/purchase_order_grid",$data);
	}
        function loadOpenInvoicesGrid()
	{
            
            $this->load->view("procurement/invoice/purchase_invoice_grid",$data);
	}
        function loadFormFragment(){
            $this->load->view("procurement/purchaseorder/po_details",$data);

        }
        function loadNotesFragment(){
            $this->load->view("procurement/purchaseorder/po_notes",$data);

        }
        
        function loadWaitingForApprovalQuotesGrid (){
            $this->load->view("procurement/quote/approval/quote_approval_grid",$data);
        }
        function loadRejectedQuotesGrid (){
            $this->load->view("procurement/quote/rejected/quote_rejected_grid",$data);
        }
        function loadRejectedRFQGrid (){
            $this->load->view("procurement/quote/rejected/request_rejected_grid",$data);
        }
        
        function loadQuoteFormFragment(){
            $this->load->view("procurement/quote/approval/quote_details",$data);

        }
        function loadQuoteNotesFragment(){
            $this->load->view("procurement/quote/approval/quote_notes",$data);

        }
        function loadWaitingForApprovalRFQGrid (){
            $this->load->view("procurement/quote/approval/request_approval_grid",$data);
        }
        function loadWaitingForApprovalRFQFormFragment(){
            $this->load->view("procurement/quote/approval/request_details",$data);

        }
        function loadWaitingForApprovalRFQNotesFragment(){
            $this->load->view("procurement/quote/approval/request_notes",$data);

        }
        
        
        function getProductsDropDown(){
            return populateProducts();
        }
        
        function loadReceivedGrid (){
            
            $this->load->view("procurement/purchaseorder/received/received_order_grid",$data);
        }
        
        function loadPPGrid (){
            
            $this->load->view("procurement/purchaseorder/received/partpayment_grid",$data);
        }
        
       
        function loadCreateQuotesGrid (){
            
            $data['productOptions'] = $this->getProductsDropDown();
            $this->load->view("procurement/quote/create/quote_create_grid",$data);
        }
        function loadCreateQuoteFormFragment(){
            
            $data['warehouseOptions']=  populateWarehouses();
            $data['supplierOptions']=  populateSuppliers();
            $this->load->view("procurement/quote/create/quote_details",$data);

        }
        function loadCreateQuoteNotesFragment(){
            $this->load->view("procurement/quote/create/quote_notes",$data);

        }
        function submitForApproval(){
            $idArray = $_REQUEST['ids'];
            $entity = $_REQUEST['entity'];
            if (!empty($idArray) && !empty($entity)){
                if ($entity=='quote'){
                $model = 'Purchase_quote_master';
                $itemModel= 'Purchase_quote_item';
                $parent_id = 'quote_id';
                
                }
                else if ($entity=='rfq'){
                    $model = 'Request_quote_master';
                    $itemModel= 'Request_quote_item';
                    $parent_id = 'rfq_id';
                    
                } 
                 foreach($idArray as $tempId){
                    $this->db->trans_start();
                    $where_clause_quote = array('id'=>$tempId);
                    $this->$model->update($where_clause_quote,array('status'=>  self::WAITING_FOR_APPROVAL));
                    log_message('debug',$this->db->last_query());
                    $where_clause_quote_item = array($parent_id=>$tempId);
                    $this->$itemModel->update($where_clause_quote_item,array('status'=>self::WAITING_FOR_APPROVAL));
                    log_message('debug',$this->db->last_query());
                    $this->db->trans_complete();
                } 
                
            }
                
        }
        function assign(){
            $inv_value_array  = json_decode($_REQUEST['sel_quote']);
            foreach($inv_value_array as $id){
                $quote_details = $this->Purchase_quote_master->getById($id);
                $notes= $quote_details->notes;
                $notes .= '<br/>Assigned By:' .$this->username.' Assignment Notes:'.$_REQUEST['assignment_notes'];
                $where_clause_array= array('id'=>$id);
                $inv_data = array
                (
                    'owner_id'=>$_REQUEST['userId'],
                    'notes'=>$notes
                    
                );
                $this->Purchase_quote_master->update($where_clause_array,$inv_data);
            }
        }
        
        function processPartpayment(){
            $action = $_REQUEST['action'];
            $qty = $_REQUEST['pp_quantity'];
            $value = $_REQUEST['pp_value'];
            $line_id = $_REQUEST['receipt_line_id'];
            $order_line_id=$_REQUEST['order_line_id'];
            $pp_id = $_REQUEST['pp_id'];
            $pp_data['order_line_id'] = $order_line_id;
            $pp_data['receipt_line_id'] = $line_id;
            $pp_data['pp_quantity'] = $qty;
            $pp_data['pp_value'] = $value;
            $old_qty = $_REQUEST['pp_old_qty'];
            $old_val = $_REQUEST['pp_old_val'];
            $pp_data['pp_notes'] = $_REQUEST['pp_notes'];
            $pp_data['product_id'] = $_REQUEST['product_id'];
            $this->db->trans_start();
            if ($action == self::ADD){
                $this->Receipt_partpayment->insert($pp_data);
            }
            else if ($action == self::EDIT){
                $this->Receipt_partpayment->update(array('id'=>$pp_id),$pp_data);
                $qty -= $old_qty;
                $value -= $old_val;
            }
            $this->Receipt_item->update(array('id'=>$line_id),null,array('pp_quantity'=>'pp_quantity + '.$qty,'pp_value'=>'pp_value + '.$value));
            $this->Purchase_order_item->update(array('id'=>$order_line_id),null,array('pp_quantity'=>'pp_quantity + '.$qty,'pp_value'=>'pp_value + '.$value));
            $this->db->trans_complete();
            
            //$this->Receipt_partpayment
        }
        
        function cancelPartPayment (){
            $ppIds = $_REQUEST['pp_ids'];
            $order_line_id = $_REQUEST['order_line_id'];
            $receipt_line_id = $_REQUEST['receipt_line_id'];
            $batch_array =array();
            foreach ($ppIds as $ppId){
                array_push($batch_array,array('id'=>$ppId,'status'=>self::CANCELLED));
            }
           $this->db->trans_start(); 
           //cancel in batch
           $this->Receipt_partpayment->update_batch($batch_array,'id');
           //reduce the part payment items from reciot and po line item
           $canceledQty = $this->Receipt_partpayment->getSumTotal('pp_quantity',null,array('field_name'=>'id','value_array'=>$ppIds));
           $canceledVal = $this->Receipt_partpayment->getSumTotal('pp_value',null,array('field_name'=>'id','value_array'=>$ppIds));
           $this->Receipt_item->update(array('id'=>$receipt_line_id),null,array('pp_quantity'=>'pp_quantity - '.$canceledQty,'pp_value'=>'pp_value - '.$canceledVal));
           $this->Purchase_order_item->update(array('id'=>$order_line_id),null,array('pp_quantity'=>'pp_quantity - '.$canceledQty,'pp_value'=>'pp_value - '.$canceledVal));
           $this->db->trans_complete();
                     
        }
        function submitForApprovalPartPayment (){
            $ppIds = $_REQUEST['pp_ids'];
            $receipt_line_id = $_REQUEST['receipt_line_id'];
            $batch_array =array();
            foreach ($ppIds as $ppId){
                array_push($batch_array,array('id'=>$ppId,'status'=>self::WAITING_FOR_APPROVAL));
            }
            
           $this->Receipt_partpayment->update_batch($batch_array,'id');
           $this->Receipt_item->update(array('id'=>$receipt_line_id),array('pp_status'=>self::WAITING_FOR_APPROVAL));
//            foreach ($ppIds as $ppId){
//                
//                $this->Receipt_partpayment->update(array('id'=>$ppId),array('status'=>self::WAITING_FOR_APPROVAL));
//                // as of now this is complete separate flow. Only thing is that less than 
//                //  # of items registers for part payment can npt be modified for received/returned unless those are cancelled. 
//            }           
        }
        // createInvoicePartPaymentUpdateOrderReceipt
        function  testTotal(){
            $ppIds[]=6;
            $ppIds[]=7;
            $len = count($ppIds);
            $final = array_combine($ppIds, array_fill(0,$len,'test'));
            
//             $total_invoice_value = 0;
//              $total_invoice_value +=$this->Receipt_partpayment->getSumTotal('pp_value',null,array('field_name'=>'id','value_array'=>$ppIds));
//           $total_invoice_value = $this->Receipt_item->getSumTotal('`received_quantity`-`pp_quantity`');
//              echo $total_invoice_value;
        }
//        
         function createInvoicePPUpdateOrderReceipt (){
             
            $ppIds = $_REQUEST['pp_ids'];
            $order_id = $_REQUEST['order_id'];
            $receipt_line_Id = $_REQUEST['receipt_line_id'];
//            echo $ppIds;
//            echo $order_id;
//            echo $receipt_line_Id;
//            
            $total_invoice_qty = 0;
            $total_invoice_value = 0;
            $this->db->trans_start();
            
            $inv_ins_data=array('status'=>self::PENDING,'order_id'=>$order_id,
                    'payment_process_type'=>self::PARTPAYMENT);
            
            $newInvId = $this->createInvoice($inv_ins_data);
            $newInvRef = 10000000 + $newInvId;
           
            
            $total_invoice_value +=$this->Receipt_partpayment->getSumTotal('pp_value',null,array('field_name'=>'id','value_array'=>$ppIds));
            $total_invoice_qty +=$this->Receipt_partpayment->getSumTotal('pp_quantity',null,array('field_name'=>'id','value_array'=>$ppIds));

//            //foreach ($ppIds as $receiptId){
//                // create Invoice items
            $receipt_item = $this->Receipt_item->getById($receipt_line_Id,
                        array('sku','name','product_id','price','order_line_id','receipt_id','received_value','received_quantity'),true);
                
                //foreach ($receipt_items as $receipt_item){
                    
            $invoice_item_data = array();

            //common item data
            $invoice_item_data['sku'] = $receipt_item['sku'];
            $invoice_item_data['name'] = $receipt_item['name'];
            $invoice_item_data['product_id'] = $receipt_item['product_id'];
            $invoice_item_data['price'] = $receipt_item['price'];
            $invoice_item_data['order_line_id'] = $receipt_item['order_line_id'];
            $invoice_item_data['invoiced_quantity'] = $total_invoice_qty;
            $invoice_item_data['total_invoiced_value'] = $total_invoice_value;
            $invoice_item_data['invoice_id'] = $newInvId;
            $this->Purchase_invoice_item->insert($invoice_item_data);
            log_message('debug','insert statement ='.$this->db->last_query());
            $invoice_item_id = $this->db->insert_id();
            /* end insert  */

            /* update reference number in  line item */
            $where_clause = array('id'=>$invoice_item_id);
            $this->Purchase_invoice_item->update($where_clause, array('reference' => 10000000 + $invoice_item_id));
            $this->Purchase_order_item->update(array('id'=>$receipt_item['order_line_id']),null,array('invoiced_quantity'=>'invoiced_quantity + '.$total_invoice_qty));
            $this->Purchase_invoice_master->update(array('id'=>$newInvId),null,array('total_value'=>'total_value + '.$total_invoice_value));
            $batch_array =array();
            foreach ($ppIds as $ppId){
                array_push($batch_array,array('id'=>$ppId,'status'=>self::PROCESSED_FOR_PAYMENT,'invoice_id'=>$newInvId,'invoice_line_id'=>$invoice_item_id));
            }
            $this->Receipt_partpayment->update_batch($batch_array,'id');
            $where = array('status'=>self::PROCESSED_FOR_PAYMENT,'receipt_line_id'=>$receipt_line_Id);
            $in_where['field_name'] = 'pp_status';
            $in_where['value_array'] = array(self::OPEN,self::WAITING_FOR_APPROVAL);
//            $where['pp_quantity >']=  0;  
            $count = $this->Receipt_partpayment->totalNoOfRows($where);
            $countOpen = $this->Receipt_partpayment->totalNoOfRows(array('receipt_line_id'=>$receipt_line_Id),null,null,$in_where);
            $countAll = $this->Receipt_partpayment->totalNoOfRows(array('receipt_line_id'=>$receipt_line_Id));
            if ($countAll == $count){
                $this->Receipt_item->update(array('id'=>$receipt_line_Id),array('pp_status'=>self::PROCESSED_FOR_PAYMENT));
            }
            else if ($countOpen == 0){
                $this->Receipt_item->update(array('id'=>$receipt_line_Id),array('pp_status'=>self::NONE));
            }
            //if right now no item is in WFA state for this receiptline id mark the pp_staatus as processforpayment
            
           
            
            $this->db->trans_complete();
            if (!empty($newInvId)){
                $response['invoice_id'] = $newInvRef;
            }
            echo json_encode($response);
        }
        
        //receivings 
        function receiveItems(){
            $oper = $_REQUEST["oper"];
            $action = $_REQUEST["action"];
            $order_line_id = $_REQUEST["order_line_id"];
            $receipt_line_id = $_REQUEST["receipt_line_id"];
            $order_id = $_REQUEST["order_id"];
            $receiptOp = $_REQUEST["receipt"];
            $receiptIp = $_REQUEST["receipt_ip"];
            $received_quantity  = $_REQUEST["received_quantity"];
            $received_value = $_REQUEST["received_value"];
            $returned_quantity  = $_REQUEST["returned_quantity"];
            $returned_value = $_REQUEST["returned_value"];
            $cnbd_quantity  = $_REQUEST["cnbd_quantity"];
            $batch_number = $_REQUEST["batch_number"];
            $expiry_date = $_REQUEST["expiry_date"];
            $vat = $_REQUEST["vat"];
//            $discount = $_REQUEST["discount"];
//            $free_items = $_REQUEST["free_items"];
            if (!empty($cnbd_quantity)){
                $cnbd_quantity = 0;
            }
            if ($oper == "return"){
                if (!empty($_REQUEST["returned_notes"])){
                    $receipt_item_data['returned_notes']=$_REQUEST["returned_notes"];
                }
            }
            else if ($oper == "receive"){
                if (!empty($_REQUEST["receiving_notes"])){
                    $receipt_item_data['receiving_notes']=$_REQUEST["receiving_notes"];
                }
            }

           
            
            //if (!empty($rcvd_note)){
                $fmatted_rcvd_note = "&#013;&#010; **** ".date("Y-m-d H:i:s", time()). " ****&#013;&#010;".$_REQUEST["receiving_notes"];
                $rcvd_note="CONCAT(`receiving_notes`, '$fmatted_rcvd_note')";
                $notes_array['receiving_notes'] = $rcvd_note;
            //}
            

            //if (!empty($rtrnd_note)){
                $fmatted_rtrnd_note = "&#013;&#010; **** ".date("Y-m-d H:i:s", time()). " ****&#013;&#010;".$_REQUEST["returned_notes"];
                $rtrnd_note = "CONCAT(`returned_notes`, '$fmatted_rtrnd_note')";
               // $receipt_item_data['returned_notes']="CONCAT(`returned_notes`, $fmatted_rtrnd_note);";
                $notes_array['returned_notes'] = $rtrnd_note;
            //}
           
            $product_id = $_REQUEST["product_id"];
            $supplierId=$_REQUEST["supplier_id"];
//            $productdetails = $this->Product->getByProductId($product_id,true);
            $receipt_item_data['product_id'] = $product_id;
//            $receipt_item_data['name'] = $productdetails['product_name'];
//            $receipt_item_data['sku'] = $productdetails['barcode'];
            $receipt_item_data['order_line_id'] = $order_line_id;
            $receipt_item_data['received_quantity']=$received_quantity;
            $receipt_item_data['batch_number']=$batch_number;
            $receipt_item_data['expiry_date'] = $expiry_date;
            $receipt_item_data['vat']=$vat;
//            $receipt_item_data['free_items']=$free_items;
//            $receipt_item_data['discount']=$discount;
           
            // total received and value for order line items
            $total_received_for_order = $_REQUEST['total_received_quantity'];
            $total_received_value_for_order = $_REQUEST['total_received_value'];
            $total_returned_for_order = $_REQUEST['total_returned_quantity'];
            $total_returned_value_for_order = $_REQUEST['total_returned_value'];
            $ordered_quantity = $_REQUEST['quantity'];
            $order_line_item_matched = 0;
            
            
            $line_status= self::RECEIVING;
            if ($ordered_quantity == $total_received_for_order + $total_returned_for_order + $cnbd_quantity){
                $line_status= self::RECEIVED;
                $order_line_item_matched = 1;
            }
            
            
            
            $this->db->trans_start();
            //if new receipt number for this particular suppler create obe entry in recepipt table
            
            if (empty($receiptOp) && !empty($receiptIp)){
                $receiptData = array ('supplier_receipt_number'=>trim($receiptIp),'supplier_id'=>$supplierId
                    ,'order_id'=>$order_id,'status'=>self::RECEIVING,'owner_id'=>$this->user->person_id);
                
                $this->Receipt_master->insert($receiptData);
//                log_message('debug','insert statement ='.$this->db->last_query());
                $id = $this->db->insert_id();

                $where_clause = array('id'=>$id);
                $this->Receipt_master->update($where_clause, array('reference' => 10000000 + $id));
//                log_message('debug','update statement ='.$this->db->last_query());
                
                $receiptOp = $id;
               
            }
            $receipt_item_data['receipt_id'] = $receiptOp;
            ///****first cmmnt ****receipt line items are unique by order line id and receiot id.
            //a single order line can have multiple recepit
            // a single supplier receipt can also have multiple order line and multiple order
            // but system receipt can only have single order and lultiple order line
            
            //*** second comment** receipt line should be unique by batch number. During insert if no batch # the system reference is added as batch number
            //**** comment **** reverting to first because different receipts can have products with same batchnumber so during add we are going to use that
            //but in edit we can use receipt_line_id
            if ($action == 'add'){
                $itemDetails = $this->Receipt_item->getByOrderLineReceiptBatch($order_line_id,$receiptOp,$batch_number);
            }
            else if ($action == 'edit'){
                $itemDetails = $this->Receipt_item->getById($receipt_line_id);
            }
            //adding up with already received
            if (!empty($itemDetails) ){
                if ($action == 'add'){
                    // for received goods
                    $savedValue = $itemDetails['received_value']; //this is the receipt line items total value
                    $savedQuantity= $itemDetails['received_quantity'];
                    //update new total
                    $received_value += $savedValue;
                    $received_quantity += $savedQuantity;
                    //for returned goods
                    $savedReturnedValue = $itemDetails['returned_value']; //this is the receipt line items total value
                    $savedReturnedQuantity= $itemDetails['returned_quantity'];
                    //update new total
                    $returned_value += $savedReturnedValue;
                    $returned_quantity += $savedReturnedQuantity;
                }
                
            }
            $receipt_item_data['received_value'] = $received_value;
            $receipt_item_data['received_quantity'] = $received_quantity;
            $receipt_item_data['returned_value'] = $returned_value;
            $receipt_item_data['returned_quantity'] = $returned_quantity;
            $receipt_item_data['status'] = $line_status;
            $receipt_item_data['ordered_quantity'] = $ordered_quantity;
            //save receipt line item
            if (empty($itemDetails)){
                $this->Receipt_item->insert($receipt_item_data);
                $id = $this->db->insert_id();
                /* end insert  */

                /* update reference number in  quote item */
                $ref = 10000000 + $id;
                $upd_data['reference']= $ref;
                //if no batch number then reference is the batch number
                if (empty($batch_number)){
                    $batch_number= $ref;
                    $upd_data['batch_number']=$batch_number;
                }
                $where_clause = array('id'=>$id);
                $this->Receipt_item->update($where_clause, $upd_data );
                
            }
            else {
                $this->Receipt_item->updateWithNotes(array('id'=>$itemDetails['id']),array('received_quantity'=>$received_quantity,'received_value'=>$received_value,'returned_quantity'=>$returned_quantity,
                    'returned_value'=>$returned_value,'status'=>$line_status),$notes_array);
//                $this->Receipt_item->updateWithoutEscape(array('id'=>$itemDetails['id']),'receiving_notes',$rcvd_note);
//                $this->Receipt_item->updateWithoutEscape(array('id'=>$itemDetails['id']),'returned_notes',$rtrnd_note);
                
            }
            //if completed then update all the receipt line items with same order line id to "RECEIVED"
                // as the parent order line id status is soon going to be set as "RECEIVED"
            if ($line_status==self::RECEIVED){
                $this->Receipt_item->update(array('order_line_id'=>$order_line_id),array('status'=>$line_status));
            }
            // update PO line with total value. Note This could be different from the received quantity
            // as there could be multiple receipt line for a single order line
            
            $this->Purchase_order_item->update(array('id'=>$order_line_id),array('received_quantity'=>$total_received_for_order,'received_value'=>$total_received_value_for_order,
                'returned_quantity'=>$total_returned_for_order,'returned_value'=>$total_returned_value_for_order,'status'=>$line_status,'matched'=>$order_line_item_matched));
            //update Purchase Order So That It Can be Ready For Invoicing
            $this->Purchase_order_master->update(array('id'=>$order_id),array('status'=>self::RECEIVING));
            $this->db->trans_complete();
            
        }
        function registerUndelivered(){
            $received_quantity  = $_REQUEST["received_quantity"];
            $cnbd_quantity = $_REQUEST['cnbd_quantity'];
            $returned_quantity  = $_REQUEST["returned_quantity"];
            $ordered_quantity = $_REQUEST['ordered_quantity'];
            if ($ordered_quantity == $received_quantity + $returned_quantity + $cnbd_quantity){
                
                $order_line_item_matched = 1;
            }
            else {
                $order_line_item_matched = 0;
            }
            
            $this->Purchase_order_item->update(array('id'=>$_REQUEST['order_line_id']),array('cnbd_quantity'=>$_REQUEST['cnbd_quantity'],'matched'=>$order_line_item_matched),array('cnbd_reason'=>  appendComments($_REQUEST['cnbd_notes'], 'cnbd_reason')));
                
        }
        
        function populateReceiptOptions (){
            $order_id= $_REQUEST['order_id'];
            echo populateReceiptOptions($order_id);
        }
        
        function submitForApprovalBeforeInvoice (){
            $receiptIds = $_REQUEST['receipt_ids'];
            $this->db->trans_start();
            
            foreach ($receiptIds as $receiptId){
                
                $this->Receipt_master->update(array('id'=>$receiptId,'status'=>self::READYTOINVOICEMEMO),array('status'=>self::WAITING_FOR_APPROVAL));
                $this->Receipt_item->update(array('receipt_id'=>$receiptId),array('status'=>self::WAITING_FOR_APPROVAL));
                //we are not bothered about order status anymore. Goods are either received ok or returned and receipts are now processed for payments.
                //So the final status of Purchase Order is REceiptssubmitted. After this only returned and received wuantities can change but the total received + returned
                //is always going to be same as ordered quantity
                
                ///$this->Purchase_order_master->update(array('id'=>$order_id),array('status'=>self::WAITING_FOR_APPROVAL));
                //$this->Purchase_order_item->update(array('order_id'=>$order_id),array('status'=>self::WAITING_FOR_APPROVAL));
            }
            
            $this->db->trans_complete();
            
        }
        function markForInvoicing(){
            $orderIds = $_REQUEST['orderIds'];
            //very very important: Remember this means the end for order line level editing. Recap One Orderline
            // can have multiple receipts and multiple receipt lines. So Theoratically Before This point
            // it was possible to vary received /rejected at the receipt line items provided it does not exceed total          
            //order line quantity. For example a single order line with 10 Ordrd Qty can have 2 receipt lines
            // Before this Point for two receipt lines it was possible to have different total of received rejected
            //at receipt line level provided it does not exceed 10. But After This only Receipt Line Editing Is Possible
            // That means if Total No Of recived and rejected for a receit line is 6.. total nmber of received and rejected
            //can be varied but total # can not be changed from 6
            
            foreach ($orderIds as $orderId){
                $this->db->trans_start();
                $this->Purchase_order_master->update(array('id'=>$orderId),array('status'=>self::RECEIPTSSUBMITTED));
                $this->Purchase_order_item->update(array('order_id'=>$orderId),array('status'=>self::RECEIPTSSUBMITTED));
                $this->Receipt_master->update(array('order_id'=>$orderId),array('status'=>self::READYTOINVOICEMEMO));
                // update receipt line as invalid for partpayment where pp_quantity is still 0 while ready to invoice memo . 
                $this->Receipt_item->update(array('order_id'=>$orderId,'pp_quantity'=>0),array('status'=>self::NOTAPPLICABLE));
                //$this->Receipt_master->update(array('order_id'=>$orderId,'status'=>self::RECEIVED),array('status'=>self::READYTOINVOICE));
                //dont need to update recipt items here as these as the receipt is going to go tyhrough approval. Individual receipt items are not
                // later  all recipt items are either will be moved for invice or individual receipt items will be rejected and later corrected
                // if rejected set the receipt aster status as rejected and individualreceipt item as rejected. Let only rejected receipt
                //items to be modified
                $this->db->trans_complete();
            }
            
        }
        /*TODO: Manual Transaction */
        function createInvoiceMemoUpdateOrderReceipt (){
            $receiptIds = $_REQUEST['receipt_ids'];
            $do_invoice = false;
            $do_memo = false;
            $order_id = $_REQUEST['order_id'];
            $total_invoice_value = 0;
            $total_memo_value = 0;
            // lets see if we have inoice or memo to create. This is to take care of the  border condition 
            // where either all are received or all are returned
            
            if ($this->Receipt_item->getSumTotal('`received_quantity`-`pp_quantity`',null,array('field_name'=>'receipt_id','value_array'=>$receiptIds))>0){
                $do_invoice = true;
            }
            
            if ($this->Receipt_item->getSumTotal('returned_quantity',null,array('field_name'=>'receipt_id','value_array'=>$receiptIds))>0){
                $do_memo = true;
            }
            //if both are false there is a problem' Let the user know. There must be either rteturned or received
            // normally user should never come across this error. The validations shpuld already take care pf that
            if (!$do_invoice && !$do_memo){
                echo 'error_both_zero';
                return; //dont progress
            }
            // manual transaction for this
            $this->db->trans_start();
            
            $common_data=array('status'=>self::PENDING,'order_id'=>$order_id);
                    //,'owner_id'=>$this->user->person_id);
            // we have received wuantities so lets create Invoice
            if ($do_invoice){
                $inv_ins_data = $common_data;
                //$inv_ins_data['invoiced_by']=$this->user->person_id;
                $newInvId = $this->createInvoice($inv_ins_data);
                $newInvRef = 10000000 + $newInvId;
            }
            // we have returns wuantities so lets create Memo
            if ($do_memo){
                $inv_mem_data = $common_data;
                $inv_mem_data['memo_by']=$this->user->person_id;
                $newCreditMemoId = $this->createCreditmemo($inv_mem_data);
                $newmemoRef = 10000000 + $newCreditMemoId;
            }
            
            $order_line_array_invoiced = array();
            $order_line_array_memo = array();
            foreach ($receiptIds as $receiptId){
                // create Invoice items
                $receipt_items = $this->Receipt_item->getAll(false,array('receipt_id'=>$receiptId),
                        array('sku','name','product_id','price','order_line_id','received_value','received_quantity','returned_value','returned_quantity'));
                
                foreach ($receipt_items as $receipt_item){
                    
                    $invoice_item_data = array();
                    $memo_item_data = array();
                    //common item data
//                    $item_data['sku'] = $receipt_item['sku'];
//                    $item_data['name'] = $receipt_item['name'];
                    $item_data['product_id'] = $receipt_item['product_id'];
                    $item_data['price'] = $receipt_item['price'];
                    $item_data['order_line_id'] = $receipt_item['order_line_id'];
                    $received_qty = $receipt_item['received_quantity'];
                    $returned_qty = $receipt_item['returned_quantity'];
                    $pp_quantity =  $receipt_item['pp_quantity'];
                    $pp_value = $receipt_item['pp_value'];
                    //subtract part paid
                    $invoice_quantity = $received_qty-$pp_quantity;
                    // we are not checking "do" flags any more as anyway at this point we know receipt items
                    // must be having recvd or rtrnd other wise do flags would have failed
                    if ($invoice_quantity>0){
                        $invoice_item_data = $item_data;
                        $invoice_item_data['invoiced_quantity'] = $invoice_quantity;
                        $invoice_item_data['total_invoiced_value'] = $receipt_item['received_value']-$pp_value;
                        $invoice_item_data['invoice_id'] = $newInvId;
                        $this->Purchase_invoice_item->insert($invoice_item_data);
                        log_message('debug','insert statement ='.$this->db->last_query());
                        $id = $this->db->insert_id();
                        /* end insert  */

                        /* update reference number in  line item */
                        $where_clause = array('id'=>$id);
                        $this->Purchase_invoice_item->update($where_clause, array('reference' => 10000000 + $id));
                        // now prepare array to update order line items
                        if (array_key_exists($receipt_item['order_line_id'], $order_line_array_invoiced)){
                            // already exists .. that means in separate invoice item some of the quantity
                            //already invoiced.. get the old value add with the this invoice line items 
                            // invoiced_quantity. push iit back to array
                            $oldvalue = $order_line_array_invoiced[$item_data['order_line_id']];
                            $order_line_array_invoiced[$item_data['order_line_id']] =  $oldvalue + $received_qty;
                        }
                        else {
                            // first invoice item for this order line item
                            $order_line_array_invoiced[$item_data['order_line_id']] =  $received_qty;
                        }
                    }
                    
                    if ($returned_qty>0){
                        $memo_item_data = $item_data;
                        $memo_item_data['memo_quantity'] = $returned_qty;
                        $memo_item_data['total_memo_value'] = $receipt_item['returned_value'];
                        $memo_item_data['memo_id'] = $newCreditMemoId;
                        $this->Purchase_creditmemo_item->insert($memo_item_data);
                        log_message('debug','insert statement ='.$this->db->last_query());
                        $id = $this->db->insert_id();
                        /* end insert  */

                        /* update reference number in  line item */
                        $where_clause = array('id'=>$id);
                        $this->Purchase_creditmemo_item->update($where_clause, array('reference' => 10000000 + $id));
                        // now prepare array to update order line items
                        if (array_key_exists($receipt_item['order_line_id'], $order_line_array_memo)){
                            // already exists .. that means in separate invoice item some of the quantity
                            //already invoiced.. get the old value add with the this invoice line items 
                            // invoiced_quantity. push iit back to array
                            $oldvaluememo = $order_line_array_memo[$item_data['order_line_id']];
                            $order_line_array_memo[$item_data['order_line_id']] =  $oldvaluememo + $returned_qty;
                        }
                        else {
                            // first invoice item for this order line item
                            $order_line_array_memo[$item_data['order_line_id']] =  $returned_qty;
                        }
                    }
                    
                }
                //grab order line array .. update order line items 
                foreach ($order_line_array_invoiced as $key => $value){
                    $this->Purchase_order_item->update(array('id'=>$key),array('invoiced_quantity'=>$value));
                }
                foreach ($order_line_array_memo as $key => $value){
                    $this->Purchase_order_item->update(array('id'=>$key),array('memo_quantity'=>$value));
                }
                //calcultae total invoice value by receipt id 
                
                $total_invoice_value +=$this->Receipt_item->totalAmount($receiptId);
                $total_memo_value +=$this->Receipt_item->getSumTotal('returned_value',array('receipt_id'=>$receiptId));
                // as of now this is all or none.
                //later add invoiced quantity
                $this->Receipt_master->update(array('id'=>$receiptId),array('invoice_id'=>$newInvId,'memo_id'=>$newCreditMemoId,'status'=>self::PROCESSED_FOR_PAYMENT));
                $this->Receipt_item->update(array('receipt_id'=>$receiptId),array('status'=>self::PROCESSED_FOR_PAYMENT));
                
            }
            if ($do_invoice){
                $this->Purchase_invoice_master->update(array('id'=>$newInvId),array('total_value'=>$total_invoice_value));
            }
            if ($do_memo){
                $this->Purchase_creditmemo_master->update(array('id'=>$newCreditMemoId),array('total_value'=>$total_memo_value));
            }
            //validate by order if invoicing complete
            //first find order lines are received. If all order lines are received then there are entries in
            //receipt line item table with all order line items. Next Se if all receipt Items with receipt ids
            //with the order id  are invoiced. If Invoiced then mark all order lines and the order as invoiced
            //(Revisit  This Later)
//            $noOfRows = $this->Receipt_master()->totalNoOfRows(array('order_id'=>$order_id,'status'=>self::RECEIVED));
            $this->db->trans_complete();
            if (!empty($newInvId)){
                $response['invoice_id'] = $newInvRef;
            }
            if (!empty($newCreditMemoId)){
                $response['memo_id'] = $newmemoRef;
            }
            echo $response;
        }
        
        function createInvoice($invoiceData){
            $this->Purchase_invoice_master->insert($invoiceData);
            log_message('debug','insert statement ='.$this->db->last_query());
            $id = $this->db->insert_id();

            $where_clause = array('id'=>$id);
            $this->Purchase_invoice_master->update($where_clause, array('reference' => 10000000 + $id));
            log_message('debug','update statement ='.$this->db->last_query());

           return $id;
               
        }
        function createCreditmemo($memoData){
            $this->Purchase_creditmemo_master->insert($memoData);
            log_message('debug','insert statement ='.$this->db->last_query());
            $id = $this->db->insert_id();

            $where_clause = array('id'=>$id);
            $this->Purchase_creditmemo_master->update($where_clause, array('reference' => 10000000 + $id));
            log_message('debug','update statement ='.$this->db->last_query());

           return $id;
               
        }
        
        function registerPayment(){
            
            $form_data = $_REQUEST['form_data'] ;
            $total = $_REQUEST['total_value'] ;
            $amount = $form_data['amount'] ;
            $prevAmount = $form_data['prev_amount'] ;
            $parentId = $form_data['parent_id'] ;
            $invoice_id = $_REQUEST['invoice_id'];
            $order_id = $_REQUEST['order_id'];
            if (empty($invoice_id) && empty($order_id)){
                echo 'error';
                return;
            }
            if (!empty($invoice_id)){
                $data['invoice_id'] = $invoice_id ;
            }
            if (!empty($order_id)){
                $data['order_id'] = $order_id ;
            }
            $comment = appendComments( $form_data['comments'],'comments');
            
            
            $oper = $form_data['oper_payment'] ;
            $payment_id = $form_data['payment_id'] ;
            $data['payment_reference'] = $form_data['payment_ref'] ;
            $data['payment_mode'] = $form_data['payment_mode'] ;
            $data['amount'] = $amount;
            $type = $form_data['type_payment'] ;
//            if (empty($type) && $oper != 'edit'){
//                $type = 'general';
//            }
            if (!empty($type)){
                 $data['payment_type'] = $type ;
            }
            $total_invoiced = $_REQUEST['total_invoiced'];
             if ($type==self::GENERAL){
                  $data['invoiced_amount'] = $form_data['amount'];
              }
            $this->db->trans_start();
            if ($oper == 'add' || $oper == 'assign'){
             
                $this->Outgoing_payment->insert($data,array('comments'=>$comment));
                $id = $this->db->insert_id();

                $where_clause = array('id'=>$id);
                $update_data['reference'] = 10000000 + $id;
                 if ($oper == 'assign'){
                    //in case of advance payment record the parent child relationship. add the total invoiced
                  $update_data['parent_id'] = $payment_id;
                  $update_data['payment_type'] = self::GENERAL;
//                  $mapping_data['payment_id'] = $id;
//                  $update_data['has_advance'] = 1;
                  //$this->Outgoing_payment->createMapping($mapping_data);
                  $this->Outgoing_payment->update($where_clause, $update_data);
                  $this->Outgoing_payment->update(array('id'=>$payment_id), null,array('invoiced_amount'=>'invoiced_amount + '.$amount));
                }
                else {
                    $this->Outgoing_payment->update($where_clause, $update_data);
                }
               
            }
            else if ($oper == 'edit'){
                
                $this->Outgoing_payment->update(array('id'=>$payment_id),$data,array('comments'=>$comment));
                if (!empty($parentId)){
                    // this was adjusted with advance: SO we need to adjust the advance row as well
                    $adj_amt = $amount - $prevAmount;
                    $this->Outgoing_payment->update(array('id'=>$parentId), null,array('invoiced_amount'=>'invoiced_amount + '.$adj_amt));
                }
            }
            if ($type!='advance'){
                if ($total_invoiced == $total){
                $this->Purchase_invoice_master->update(array('id'=>$_REQUEST['invoice_id'] ),array('amount_paid'=>$total,'status'=>self::COMPLETE));
                }
                else {
                    $this->Purchase_invoice_master->update(array('id'=>$_REQUEST['invoice_id'] ),array('amount_paid'=>$total,'status'=>self::PARTIAL));
                }
            
            }
            
            $this->db->trans_complete();
            if ($this->db->trans_status()=== TRUE){
                echo $total;
            }
            
        }
        function getPaymentDetails(){
            $pay_id = $_REQUEST['pay_id'];
            $details = $this->Outgoing_payment->getByPaymentId($pay_id);
            echo json_encode($details);
            
        }
        function populateAdvancePayments(){
            echo json_encode(populateAdvancePayments($_REQUEST['order_id']));
        }
        function printInvoice(){
            define('FPDF_FONTPATH',$this->config->item('fonts_path'));
            //$this->load->library('fpdf','','fpdf');
            //$this->load->library('table_pdf','','pdf');
//            $this->load->library('rotation');
//            $this->load->library('pdf','','pdf');
            
            
//            $this->pdf->AddPage();
//            $this->pdf->SetFont('Arial','B',16);
//            $this->pdf->Cell(40,10,'Hello World!');
            $header = array('Type', 'Payment Ref', 'Amount');
            $data = $this->Outgoing_payment->getColumnValues(false,null,array('payment_type','payment_reference','amount'));
            $this->pdf->SetFont('Arial','',14);
//            $this->pdf->AddPage();
//            $this->pdf->BasicTable($header,$data);
//            $this->pdf->AddPage();
//            $this->pdf->ImprovedTable($header,$data);
            $this->pdf->AddPage();
            $this->pdf->FancyTable($header,$data);


            //$this->pdf->Output('/tmp/test'.time().'.pdf', 'F');
            $this->pdf->Output('/tmp/test.pdf', 'F');
        }
        
      
        /* Invoice Grid Start */
        
         function populateInvoicesToPay(){
            $quotedata=array();
            $where = array();
            $in_where = array();
            setOwnerStatusCommon($where,$in_where);
            
            $griddata=  populateGridCommon('Purchase_invoice_master',$where,null,$in_where);
            $dbrows = $griddata['db_data'];
            $data = $griddata['grid_metadata'];
           
           foreach ($dbrows as $dp){
//               $orderDetails = $this->Purchase_order_master->getGridById($dp['order_id']);
//               $userDetails = $this->User->getUserInfo($dp['invoiced_by'],true);
               array_push($quotedata, array('id'=> $dp['id'],'dprow' => array($dp['reference'],$dp['payment_process_type'],$dp['total_value'],$dp['amount_paid'],$dp['status'],$dp['payer_id'],$dp['payer_name'],$dp['owner_id'],$dp['owner_name'],$dp['order_id'],$dp['order_reference'])));
           }
           $data['quotedata'] = $quotedata;
           echo json_encode($data);
        }
        
        function populateInvoiceItems(){
            $invoiceId = $_REQUEST['invoiceId'];
            if (!empty($invoiceId)){
                $page = $_REQUEST['page'];
                $limit = $_REQUEST['rows'];
                $sidx = $_REQUEST['sidx'];
                $sord = $_REQUEST['sord'];

                //standard response parameters 

                $quotedata = array();
                $where = array('invoice_id' => $invoiceId );
                $count = $this->Purchase_invoice_item->totalNoOfRows($where);
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

                $quotes = $this->Purchase_invoice_item->getAll(false,$where);
                

                foreach ($quotes as $dp){
                    //$order_line = $this->Purchase_order_item->getById($dp['order_line_id']);
                    
                    array_push($quotedata, array('id'=> $dp['id'],'dprow' => array($dp['product_id'],$dp['name'],$dp['invoiced_quantity'],$dp['total_invoiced_value'])));
                }
                $data['invoiceitemdata'] = $quotedata;
                echo json_encode($data);
           }         
           
        }
        
        //receipt approval
        
        function loadReceiptsToApprove(){
            $this->load->view("procurement/purchaseorder/approval/receipt_approval_grid",$data);

        }
        
        function rejectReceipt (){
            
            $receiptId = $_REQUEST['receiptId'];
            if (!empty($receiptId)){
                $comment = appendComments( $_REQUEST['rejection_notes'],'rejection_notes');
                $this->Receipt_master->update(array('id'=>$receiptId),array('status'=>self::REJECTED),array('rejection_notes'=>$comment));
            }
                    
        }
        
        function rejectReceiptLineItem (){
            
            $receiptId = $_REQUEST['receiptId'];
            if ($_REQUEST['multiple']){
                // if multiple line items are rejected we are not updating rejection comments at line level
                // we are adding at the receipt level
                
                $receiptLineIds = $_REQUEST['receiptLineIds'];
                $this->db->trans_start();
                foreach ($receiptLineIds as $lineId){
                    $this->Receipt_item->update(array('id'=>$lineId),array('status'=>self::REJECTED));
                }
                $comment = appendComments( $_REQUEST['rejection_notes'],'rejection_notes');
                $this->Receipt_master->update(array('id'=>$receiptId),array('status'=>self::REJECTED),array('rejection_notes'=>$comment));
                $this->db->trans_complete();
            }
            else {
                $receiptLineId = $_REQUEST['receiptLineId'];
                $item_comment = appendComments( $_REQUEST['rejection_notes'],'rejection_notes');
                $this->db->trans_start();
                $this->Receipt_item->update(array('id'=>$receiptLineId),array('status'=>self::REJECTED),array('rejection_notes'=>$item_comment));
                // update omment about single line item rejection
                $comment = appendComments( 'Single Receipt Item Rejected.Check  Notes  At Line Item Level','rejection_notes');
                $this->Receipt_master->update(array('id'=>$receiptId),array('status'=>self::REJECTED),array('rejection_notes'=>$comment));
                $this->db->trans_complete();
            }
                 
        }
        
        //end approval grid
        
        //start rejected grid
        //receipt approval
        
        function loadRejectedReceipts(){
            
            
            $this->load->view("procurement/purchaseorder/rejected/receipt_rejected_grid",$data);

        }
        function resubmitReceiptLineItem(){
            $receiptLineId =$_REQUEST["receiptLineId"];
            $receiptId =$_REQUEST["receiptId"];
            $item_data['received_quantity']  = $_REQUEST["received_quantity"];
            $item_data['received_value'] = $_REQUEST["received_value"];
            $item_data['returned_quantity']  = $_REQUEST["returned_quantity"];
            $item_data['returned_value'] = $_REQUEST["returned_value"];
            $item_data['status'] = self::WAITING_FOR_APPROVAL;
            $returned_notes =$_REQUEST["returned_notes"];
            $receiving_notes =$_REQUEST["receiving_notes"];
            if (empty($receiving_notes)){
                $receiving_notes = 'Resubmitting For Approval';
            }
            
            if (empty($returned_notes)){
                $returned_notes = 'Resubmitting For Approval';
            }
            $notes['receiving_notes'] = appendComments($receiving_notes,'receiving_notes');
            $notes['returned_notes'] = appendComments( $returned_notes,'returned_notes');
            
            //$this->db->trans_start();
            $this->Receipt_item->update(array('id'=>$receiptLineId,'status'=>self::REJECTED),$item_data,$notes);
            
            //$this->db->trans_complete();
        }
         function resubmitReceipts(){
             $receiptIds =$_REQUEST["receipt_ids"];
             $still_rejected_receipts = array();
             $success_receipts = array();
             
             foreach($receiptIds as $receiptId){
                 // get the # of receipt line items for this receipt which are at rejected state
                $still_rejected_state =$this->Receipt_item->totalNoOfRows(array('receipt_id'=>$receiptId,'status'=>self::REJECTED));
                // if still there are line items in rejected state dont set the stuus to waitingforaprroval i.e. for reapproval
                if ($still_rejected_state>0){
                    array_push($still_rejected_receipts, $this->Receipt_master->getById($receiptId,array('reference'))->reference);
                }
                else {
                    $this->Receipt_master->update(array('id'=>$receiptId),array('status'=>self::WAITING_FOR_APPROVAL));
                    array_push($success_receipts, $this->Receipt_item->getById($receiptId,array('reference'))->reference);
                }
             }
             if (!empty($success_receipts)){
                 $response['success'] = $success_receipts;
             }
             
             if (!empty($still_rejected_receipts)){
                 $response['failed'] = $still_rejected_receipts;
             }
             
             echo json_encode($response);
         }
         
         function loadPricelist(){
             $data['supplierOptions']=  populateSuppliers();
             $data['productOptions'] = $this->getProductsDropDown();
             $data['supplierPriceListOptions'] = populateBaseContracts();
             
             $this->load->view('pricelist/define_pricelist',$data);
         }
         function createPricelist(){
            $id = $_REQUEST['pricelistId'];
            $ischanged = $_REQUEST['ischanged'];
            $pricelist_data['name'] = $_REQUEST['name'];
            $pricelist_data['type']  = $_REQUEST['typeOp'];
            $type =$_REQUEST['typeOp'];
            $supplierPriceListOp = $_REQUEST['supplierPriceListOp'];
            $rootPriceListOp =  $_REQUEST['rootPriceListOp'];
            if ($type==parent::SUPPLIERPRICELIST){
                //$pricelist_data['parent'] = $rootPriceListOp;
                $pricelist_data['supplier_id'] = $_REQUEST['supplierOp'];
                $pricelist_data['name'] = trim($_REQUEST['supplierName']).'- Base Rate Contract';
            }
            else if ($type==parent::VERSION){
                
                $pricelist_data['parent'] = $supplierPriceListOp;
                $parentDetails = $this->Pricelist_master->getById($supplierPriceListOp,array('supplier_id','name'),true);
                $pricelist_data['supplier_id'] = $parentDetails['supplier_id'];
                $pricelist_data['version'] = $this->Pricelist_master->getByHighestVersion($supplierPriceListOp)+1;
                $validfromdateObj = DateTime::createFromFormat('d/m/Y', $_REQUEST['validfrom']);
                log_message('debug','converted '.$validfromdateObj->format('Y-m-d'));
                $pricelist_data['valid_from'] = $validfromdateObj->format('Y-m-d');
                $validtodateObj = DateTime::createFromFormat('d/m/Y', $_REQUEST['validto']);
                log_message('debug','converted '.$validtodateObj->format('Y-m-d'));
                $pricelist_data['valid_to'] = $validtodateObj->format('Y-m-d');
                $pricelist_data['name'] = " V ".$pricelist_data['version']." ".$parentDetails['name'];
            }
            
            if (empty($id)){
                $this->Pricelist_master->insert($pricelist_data);
                $id =  $this->db->insert_id();
            }
            else {
                if ($ischanged){
                    $where_clause = array('id'=>$id);
                    if ($type == parent::VERSION){
                        $edit_data['valid_from'] = $pricelist_data['valid_from'];
                        $edit_data['valid_to'] = $pricelist_data['valid_to'];
                    }
                    $edit_data['active'] = $_REQUEST['isactive'];
                    $this->Pricelist_master->update($where_clause, $edit_data);
                    
                }
                
            }
            echo $id;
         }
         function addSupplierRate(){  
             $supplier_rate_list['base_price']= $_REQUEST['product_price'];
             $supplier_rate_list['pricelist_id']= $_REQUEST['pricelist_id'];
             $products = $_REQUEST['productOptions'];
             $this->db->trans_start();
             foreach($products as $product) {
                 $supplier_rate_list['product_id']=$product;
                 $this->Supplier_rate_items->insert($supplier_rate_list);
             }
             
             $discount= $_REQUEST['discount_value'];
             // non empty discount means rule exists
             //so call add Supllier Rule
             if (!empty($discount)){
                 $ruleMessage = $this->addSupplierRule(true);
             }
             $this->db->trans_complete();
             if ($this->db->trans_status()===FALSE){
                $response = " The Operation Could Not Be Completed Due To Internal Error";
            }
            else {
                 $response  = " Rates Are Succesfully Added For Selected Products";
                 if(!empty($ruleMessage)){
                     $response = $response .'/n/'.$ruleMessage;
                 }
            }
            echo $response;
             
         }
         function addSupplierRule($isInternal=false){
              $app= $_REQUEST['apply_to_all'];
              
             $supplier_rule_list['discount_type']= $_REQUEST['discount_type'];
             $supplier_rule_list['discount_value']= $_REQUEST['discount_value'];
             $supplier_rule_list['operator']= $_REQUEST['operator'];
             
             $supplier_rule_list['qualifier_value_point']= $_REQUEST['qualifier_value'];
             $supplier_rule_list['qualifier_value_from']= $_REQUEST['qualifier_value_from'];
             $supplier_rule_list['qualifier_value_to']= $_REQUEST['qualifier_value_to'];
             //$supplier_rate_list['base_price']= $_REQUEST['product_price'];
             $supplier_rule_list['discount_type']= $_REQUEST['discount_type'];
             $supplier_rule_list['qualifier']= $_REQUEST['qualifier'];
             $supplier_rule_list['pricelist_id']= $_REQUEST['pricelist_id'];
             $action = $_REQUEST['action'];
             $precedencechanged= $_REQUEST['precedencechanged'];
             $oldprecedence= $_REQUEST['oldprecedence'];
             $shiftneeded = false;
             
             if ($action=='edit_rule'){
                $products = array($_REQUEST['product_id']);
             }
             else {
                $products = $_REQUEST['productOptions'];
             }
             $precedence = $_REQUEST['precedence'];
             $rule_id = $_REQUEST['rule_id'];
             if (!$isInternal){
                $this->db->trans_start();
             }
             foreach($products as $product) {
                 $supplier_rule_list['product_id']=$product;
                 // if called from rate API we are already inside an transaction. So dont start a txn
                ///if precedence not specified find the highest. add one 
                 if (empty($precedence)){
                     // if edit and precedence is blank that means an existing precedence is going to be set as lowest precendence. So 
                     //we have to reaarange 
                     if ($action=='edit_rule'){
                         $shiftneeded = true;
                     }
                    $supplier_rule_list['precedence'] =  $this->Pricelist_rules->getHighestPrecedence($_REQUEST['pricelist_id'],$product)+1;
                 }
                 //else validate rearramge if required
                 else{
                     if ($precedencechanged!=0){
                        if ($this->validatePrecedence($_REQUEST['pricelist_id'],$product,$precedence,$action,$oldprecedence)){
                            $newPrecedence = $_REQUEST['precedence']+1;
                            $supplier_rule_list['precedence']= $newPrecedence;
                            $response ="The Precedence # Already Existed.So Next Lower Precedent ".$newPrecedence." Is Attached To The Rule. Rules With Lower Precedences Have Been Lowered  By One ";
                        }
                        else {
                            $supplier_rule_list['precedence']= $precedence;
                            $response ="The Precedence # $precedence Will Be  Assigned.";
                        }
                     }
                 }
                 if ($action=='edit_rule'){
                     $this->Pricelist_rules->update(array('id'=>$rule_id),$supplier_rule_list);
                     //if shift needed shift one back
                     if ($shiftneeded){
                         $this->Pricelist_rules->update(array('precedence > '=>$oldprecedence),null,array('precedence'=>'precedence - 1'));
                     }
                 }
                 else{
                    $this->Pricelist_rules->insert($supplier_rule_list);
                 }
                 
             }
             if (!$isInternal){
                $this->db->trans_complete();
             }
             if (!$isInternal){
                if ($this->db->trans_status()===FALSE){
                    $response = " The Operation Could Not Be Completed Due To Internal Error";
                }
             }
             if (!$isInternal){
                echo $response;
             }
             else return $response;
         }
         
         function validatePrecedence($pricelist_id,$product_id,$precedence,$action,$oldprecedence){
             $incrementprecedence = false;
             
             if (!empty($precedence)){
                 $where_clause = array('pricelist_id'=>$pricelist_id,'product_id'=>$product_id,'precedence'=>$precedence);
                 $precedencelist= $this->Pricelist_rules->getAll(false,$where_clause,array('id'));
             //this Precedence Already Exists. So We have To Re Arrange The Precedence
             if (!empty($precedencelist)){
                 if ($action =='add_rule'){
                     $this->Pricelist_rules->update(array('precedence > '=>$precedence),null,array('precedence'=>'precedence + 1'));
                     $incrementprecedence = true;
                 }
                 else if ($action =='edit_rule'){
                     if ($precedence<$oldprecedence){
                         $this->Pricelist_rules->update(array('precedence >= '=>$precedence,'precedence < '=>$oldprecedence),null,array('precedence'=>'precedence + 1'));
                     }
                     else if ($precedence>$oldprecedence){
                         $this->Pricelist_rules->update(array('precedence <= '=>$precedence,'precedence > '=>$oldprecedence),null,array('precedence'=>'precedence - 1'));
                     }
                     
                     
                 }
                 
                }
             }
             
             return $incrementprecedence;
         }
         
         
         
         function populatePriceLists(){
            $gridrowdata=array();
            $griddata=  populateGridCommon('Pricelist_master');
            $dbrows = $griddata['db_data'];
            $data = $griddata['grid_metadata'];
            foreach ($dbrows as $dbrow){
                //$order_line = $this->Purchase_order_item->getById($dp['order_line_id']);

                array_push($gridrowdata, array('id'=> $dbrow['id'],'dprow' => array($dbrow['name'],$dbrow['parent_name'],$dbrow['supplier_id'],$dbrow['supplier_name'],$dbrow['valid_from'],$dbrow['valid_to'],$dbrow['active'],$dbrow['type'],$dbrow['version'])));
            }
            $data['pricelistdata'] = $gridrowdata;
            echo json_encode($data);        
         }
         
         function populatePriceData(){
            $page = $_REQUEST['page'];
            $limit = $_REQUEST['rows'];
            $sidx = $_REQUEST['sidx'];
            $sord = $_REQUEST['sord'];
            $searchOn = $_REQUEST['_search'];
            //$where['active'] = 1;
            $pricelist_id = $_REQUEST['pricelist_id'];
            $supplier_id = $_REQUEST['supplier_id'];
            

            //standard response parameters 

            $pricelistdata = array();
            //$where = array();
            $count = $this->Supplier_rate_items->getAllUnion(null,$pricelist_id,$supplier_id,null,null,null,true);
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
                    
                    $like_condition[$field] = trim($input);
                   
                }
                $pricelists = $this->Supplier_rate_items->getAllUnion(array('id','lineitem_id','barcode','product_name','product_id','manufacturer','model','base_price','pricelist_id'),$pricelist_id,$supplier_id,null,$clauses,$like_condition,false);
            }
            else {
                $pricelists = $this->Supplier_rate_items->getAllUnion(array('id','lineitem_id','barcode','product_name','product_id','manufacturer','model','base_price','pricelist_id'),$pricelist_id,$supplier_id,null,$clauses);
            }


            foreach ($pricelists as $dp){
                //$order_line = $this->Purchase_order_item->getById($dp['order_line_id']);
                $base_price= "Not Defined";
                if (!empty($dp['base_price'])){
                    $base_price = $dp['base_price'];
                }
                
                if ($dp['pricelist_id']!=0  ){
                    if ($dp['pricelist_id']!=$pricelist_id){
                        $inherited= "Yes";
                    }
                    else {
                         $inherited= "No";
                    }
                    
                }
                else {
                    $inherited = "NA";
                }
                array_push($pricelistdata, array('id'=> $dp['id'],'dprow' => array($dp['lineitem_id'],$dp['barcode'],$dp['product_name'],$dp['product_id'],$dp['manufacturer'],$dp['model'],$base_price,$inherited)));
            }
            $data['ratecontractdata'] = $pricelistdata;
            echo json_encode($data);
         }
         function editPrice(){
             $lineitem_id = $_REQUEST['lineitem_id'];
             $price = $_REQUEST['price'];
             if (!empty($lineitem_id) && !empty($price)){
                 $this->Supplier_rate_items->update(array('id'=>$lineitem_id),array('base_price'=>$price));
                 echo "success";
             }
             else {
                 echo "notupdated";
             }
         }
         function deletePrice(){
             $lineitem_id = $_REQUEST['id'];
             if (!empty($lineitem_id) ){
                 $this->Supplier_rate_items->delete($lineitem_id);
                 echo "success";
             }
             else {
                 echo "notupdated";
             }
         }
         function ruleUnionTest(){
             var_dump($this->Pricelist_rules->getAllUnion());
         }
         function applyPriceRules(){
            $where['active'] = 1;
            $where['pricelist_id'] = $_REQUEST['pricelist_id'];
            $where['product_id'] = $_REQUEST['product_id'];
            $inherit_rules = $_REQUEST['inherit_rules'];
            $inherit_rules_dir = $_REQUEST['inherit_rules_dir'];
            $unit_price = $_REQUEST['unit_price'];
            $quantity = $_REQUEST['quantity'];
//            $where['pricelist_id'] = 19;
//            $where['product_id'] =17;
//            $unit_price = 20;
//            $quantity = 10;
            $clauses = array('orderDir'=>$inherit_rules_dir);
            
            $discounted_price = $unit_price;
            $free_quantity = 0;
            if ($inherit_rules== parent::NONE){
                
               $rules=  $this->Pricelist_rules->getAll(false,$where,null,array('orderBy'=>'precedence','orderDir'=>'asc'));
            }
            else if ($inherit_rules== parent::ALL){
                $rules = $this->Pricelist_rules->getAllUnion($where,false,$clauses);
            }
            else if ($inherit_rules== parent::SOME){
                $rules = $this->Pricelist_rules->getSomeUnion($where,false,$clauses);
            }
//            
           if ($discounted_price!=0){
               foreach ($rules as $rule){
               $qual = $rule['qualifier'];
               $op = $rule['operator'];
               $discount_type= $rule['discount_type'];
               $discount_value= $rule['discount_value'];
               if ($qual=='product'){
                   $this->calculateDiscount($discount_type,$discount_value,$discounted_price,$free_quantity);
               }
               else if ($qual=='quantity'){
                   $valuepoint = $rule['qualifier_value_point'];
                    if ($op=='between'){
                        $valuefrom = $rule['qualifier_value_from'];
                        $valueto = $rule['qualifier_value_to'];
                        if ($quantity >= $valuefrom&& $quantity<=$valueto){
                            $this->calculateDiscount($discount_type,$discount_value,$discounted_price,$free_quantity);
                         }
                     }
                     else if ($op=='equal'){
                         if ($quantity == $valuepoint){
                            $this->calculateDiscount($discount_type,$discount_value,$discounted_price,$free_quantity);
                         }
                     }
                     else if ($op=='more'){
                         if ($quantity >= $valuepoint){
                            $this->calculateDiscount($discount_type,$discount_value,$discounted_price,$free_quantity);
                         }
                     }
                     else if ($op=='less'){
                         if ($quantity <= $valuepoint){
                            $this->calculateDiscount($discount_type,$discount_value,$discounted_price,$free_quantity);
                         }
                     }
                }
            }
        }
        $data['final_unit_price'] = $discounted_price;
        $data['free_items'] = $free_quantity;
        echo json_encode($data);
    }
         
         function calculateDiscount($discount_type,$discount_value,&$discounted_price,&$free_quantity){
             if ($discount_type=='flat'){
                    $discount = $discount_value;
                    $discounted_price -=$discount;
                    if ($discounted_price<0){
                        $discounted_price  = 0;
                    }
                }
                else if ($discount_type=='percentage'){
                   // aappy on base price or discounted price
                   $discount = $discount_value*$discounted_price/100;


                   $discounted_price -=$discount;
                   if ($discounted_price<0){
                       $discounted_price  = 0;
                   }
               }
               else if ($discount_type=='free'){
                   // aappy on base price or discounted price
                   $free_quantity += $discount_value;
               }
         }
         function populatePriceRules(){
            $pricerulesdata=array();
            //default
            
            
            
            $pricelist_id = $_REQUEST['pricelist_id'];
            $product_id = $_REQUEST['product_id'];
            $inherit_rules = $_REQUEST['inherit_rules'];
            $inherit_rules_dir = $_REQUEST['inherit_rules_dir'];
            $where['active'] = 1;
            $where['pricelist_id'] = $pricelist_id;
            $where['product_id'] = $product_id;
            $page = $_REQUEST['page'];
            $limit = $_REQUEST['rows'];
            $sidx = $_REQUEST['sidx'];
            $sord = $_REQUEST['sord'];
            if (empty($inherit_rules)){$inherit_rules = parent::NONE;}
            //$count;
            
            if ($inherit_rules== parent::NONE){
               $count=  $this->Pricelist_rules->totalNoOfRows($where);
            }
            else if ($inherit_rules== parent::ALL){
                $sord = $inherit_rules_dir;
                $count = $this->Pricelist_rules->getAllUnion($where,true);
            }
            else if ($inherit_rules== parent::SOME){
                $sord = $inherit_rules_dir;
                $count = $this->Pricelist_rules->getSomeUnion($where,true);
            }
            //standard response parameters 
            
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
            //$dbrows;
            if ($inherit_rules== parent::NONE){
                //constant
                $clauses['orderBy'] ='precedence';
                $clauses['orderDir'] ='asc';
               $dbrows=  $this->Pricelist_rules->getAll(false,$where,null,$clauses);
            }
            else if ($inherit_rules== parent::ALL){
                $dbrows = $this->Pricelist_rules->getAllUnion($where,false,$clauses);
            }
            else if ($inherit_rules== parent::SOME){
                $dbrows = $this->Pricelist_rules->getSomeUnion($where,false,$clauses);
            }
            
            
            foreach ($dbrows as $dbrow){
                    //$order_line = $this->Purchase_order_item->getById($dp['order_line_id']);
                    if ($dbrow['operator']==self::BETWEEN){
                        $condition = $dbrow['qualifier'].' Ranges '.$dbrow['qualifier_value_from'].'-'.$dbrow['qualifier_value_to'];
                    }
                    else if ($dbrow['operator']==self::MORETHAN){
                        $condition = $dbrow['qualifier'].' Is More Than '.$dbrow['qualifier_value_point'];
                    }
                    else if ($dbrow['operator']==self::LESSTHAN){
                        $condition = $dbrow['qualifier'].' Is Less Than '.$dbrow['qualifier_value_point'];
                    }
                    else if ($dbrow['operator']==self::EQUAL){
                        $condition = $dbrow['qualifier'].' Is Equal To '.$dbrow['qualifier_value_point'];
                    }
                    if ($dbrow['qualifier'] ==self::PRODUCT){
                        $condition =  'The Product Is '.$dbrow['product_name'];
                    }
                    if ($dbrow['pricelist_id']!=0  ){
                        if ($dbrow['pricelist_id']!=$pricelist_id){
                            $inherited= "Yes";
                        }
                        else {
                             $inherited= "No";
                        }

                    }
                    
                    array_push($pricerulesdata, array('id'=> $dbrow['id'],'dprow' => array(/*$dp['product_name'],*/$dbrow['rule_type'],$inherited,$dbrow['precedence'],$dbrow['qualifier'],$condition,$dbrow['discount_type'],$dbrow['discount_value'],$dbrow['operator'],$dbrow['qualifier_value_from'],$dbrow['qualifier_value_to'],$dbrow['qualifier_value_point'])));
                }
            $data['rulesdata'] = $pricerulesdata;
            echo json_encode($data);        
         }
         function getAllProductsDropDownForSupplier(){
            echo populateProductsBySupplier($_REQUEST['supplier_id']);
         }
         
        
         function getProductsToAddRateDropDown(){
            $where['pricelist_id'] = $_REQUEST['pricelist_id'];
            $listed_products = $this->Supplier_rate_items->getAll(false,$where,array('product_id'));
            $listed_array = array();
            foreach ($listed_products as $product){
                array_push($listed_array, $product['product_id']);
            }
            $not_in_where_clause  = array('field_name'=>'product_id','value_array'=>$listed_array);
            $not_listed_products = $this->Supplier->getAllProductsSupplierMapping(false, null, array('product_id','product_name','manufacturer','model','measurement_denomination','uom','barcode'), array('orderBy'=>'product_id'), null, null, null,$not_in_where_clause);
            
            foreach ($not_listed_products as $product){
                $id = $product['product_id'];
                $name = $product['product_name'].', '.$product['barcode'].', '.$product['manufacturer'].' '.$product['model']
                        .' '.$product['measurement_denomination'].' '.$product['uom'];
                $productOptions.="<OPTION VALUE=\"$id\">".$name;
            }
            echo $productOptions;
        }
        
        function loadRFQGrid (){
            
            $data['productOptions'] = $this->getProductsDropDown();
            $deliveryPoints = $this->Delivery_point->getAll();
            foreach($deliveryPoints as $deliveryPoint) { 

                $id=$deliveryPoint["id"]; 
                $thing=$deliveryPoint["name"]; 
                $options.="<OPTION VALUE=\"$id\">".$thing; 
            } 
            
            $data['warehouseOptions']=$options;
            $data['supplierOptions']=  populateSuppliers();
            $this->load->view("procurement/quote/request/quote_request_grid",$data);
        }
        
        function loadRFQFormFragment(){
            $deliveryPoints = $this->Delivery_point->getAll();
            foreach($deliveryPoints as $deliveryPoint) { 

                $id=$deliveryPoint["id"]; 
                $thing=$deliveryPoint["name"]; 
                $options.="<OPTION VALUE=\"$id\">".$thing; 
            } 
            
            $data['warehouseOptions']=$options;
            $data['supplierOptions']=  populateSuppliers();
            $this->load->view("procurement/quote/request/request_details",$data);

        }
        
        function loadRFQNotesFragment(){
            $this->load->view("procurement/quote/request/request_notes",$data);

        }
        
       
        function populateQuotes(){
            $quotedata=array();
            
            $status = $_REQUEST['_status'];
            
            $where = array();
            $in_where = array();
            setOwnerStatusCommon($where,$in_where);
            $griddata=  populateGridCommon('Purchase_quote_master',$where,null,$in_where);
            $dbrows = $griddata['db_data'];
            $data = $griddata['grid_metadata'];
            foreach ($dbrows as $dp){
//               if ($status==self::WAITING_FOR_APPROVAL){
//                   /* we dont need actions column in approval grid . So not passing the blank */
//                   array_push($quotedata, array('id'=> $dp['id'],'dprow' => array($dp['reference'],$dp['supplier_name'],$dp['estimated_value'],$dp['status'],$dp['raised_by_name'],$dp['owner_name'],$dp['needed_by_date'])));
//               }
//               else if ($status==self::REJECTED){
//                   /* Add Extra Columns Rejected and Rejected Notes Remove Status */
//                   array_push($quotedata, array('id'=> $dp['id'],'dprow' => array($dp['reference'],$dp['supplier_name'],$dp['estimated_value'],$dp['raised_by_name'],$dp['owner_name'],$dp['needed_by_date'],$dp['approved_by_name'],$dp['approval_notes'])));
//               }
               if ($status==self::OPEN){
                   array_push($quotedata, array('id'=> $dp['id'],'dprow' => array($dp['reference'],$dp['rfq_reference'],$dp['supplier_name'],$dp['estimated_value'],$dp['discount_type'],$dp['discount_value'],$dp['pricelist_id'],$dp['discount_amount'],$dp['final_total'],$dp['status'],$dp['raised_by_name'],$dp['owner_name'],$dp['needed_by_date'],$dp['supplier_id'],$dp['warehouse_id'])));
               }
                else if (is_array($status) || $status==self::WAITING_FOR_APPROVAL){
                   array_push($quotedata, array('id'=> $dp['id'],'dprow' => array($dp['reference'],$dp['rfq_reference'],$dp['supplier_name'],$dp['estimated_value'],$dp['discount_type'],$dp['discount_value'],$dp['pricelist_id'],$dp['discount_amount'],$dp['final_total'],$dp['owner_id'],$dp['status'],$dp['raised_by_name'],$dp['owner_name'],$dp['needed_by_date'],$dp['approved_by_name'],$dp['supplier_id'],$dp['warehouse_id'],$dp['approved_by'])));
               }
               else {
                   array_push($quotedata, array('id'=> $dp['id'],'dprow' => array($dp['reference'],$dp['supplier_name'],$dp['estimated_value'],$dp['status'],$dp['raised_by_name'],$dp['owner_name'],$dp['needed_by_date'],$dp['notes'],$dp['approved_by_name'],$dp['approval_notes'])));
               }
           }
           $data['quotedata'] = $quotedata;
           echo json_encode($data);
        }
        private function generateQuoteInternal($rfq_details){
            
            $id=$rfq_details['id'];
            $quote_data['owner_id'] = $rfq_details['owner_id'];
            $quote_data['supplier_id'] = $rfq_details['supplier_id'];
            $quote_data['warehouse_id'] = $rfq_details['warehouse_id'];
            $quote_data['needed_by_date'] = $rfq_details['needed_by_date'];
            $quote_data['raised_by'] = $this->user->person_id;
            $quote_data['rfq_id'] = $rfq_details['id'];
            log_message('debug',json_encode($quote_data));
            $quote_id = $this->Purchase_quote_master->createQuote($quote_data);
            log_message('debug',$this->db->last_query());
            $this->Request_quote_master->update(array('id'=>$id),array('status'=>self::SUBMITTEDTOQUOTE,'quote_id'=>$quote_id));
            log_message('debug',$this->db->last_query());
            //now update line item status
            $item_details=$this->Request_quote_item->getByQuoteId($id);
            foreach ($item_details as $item_data){
                if ($item_data['status'] == self::OPEN || $item_data['status'] == self::WAITING_FOR_APPROVAL){
                    $quote_item_data['name'] = $item_data['name'];
                    $quote_item_data['sku'] = $item_data['sku'];
                    $quote_item_data['product_id'] = $item_data['product_id'];
                    $quote_item_data['estimated_value'] = $item_data['estimated_value'];
                    $quote_item_data['needed_by_date'] = $item_data['needed_by_date'];
                    $quote_item_data['quoted_quantity'] = $item_data['requested_quantity'];
                    $quote_item_data['expected_price'] = $item_data['expected_price'];
                    $quote_item_data['quote_id'] =$quote_id;
                    $quote_item_data['rfq_line_id'] = $item_data['id'];
                    $quote_line_id = $this->createQuoteItem($quote_item_data);
                    $this->Request_quote_item->update(array('id'=>$item_data['id']),array('status'=>self::SUBMITTEDTOQUOTE,'quote_line_id'=>$quote_line_id));
                }
            }
            
        }
        function createQuotes(){
            $id = $_REQUEST['quoteId'];
            $form_data=$_REQUEST['form_data'];
            $purchase_quote_data['supplier_id'] = $form_data['supplierOp'];
            $purchase_quote_data['warehouse_id'] = $form_data['warehouseOp'];
            $pricerule = $form_data['pricerule_quote'];
            $purchase_quote_data['discount_type'] = $pricerule;
            $discount = 0;
            
            if ($pricerule==self::EXISTING){
                $purchase_quote_data['pricelist_id'] = $form_data['pricelist_id'];
            }
            else if ($pricerule==self::DIRECTAMOUNTTOTAL){
                $purchase_quote_data['discount_value'] = $form_data['dir_disc_total_quote'];
            }
            else if ($pricerule==self::DIRECTPERCENTAGE){
                $purchase_quote_data['discount_value'] = $form_data['dir_disc_perc_quote'];
            }
            if (!empty($_REQUEST['total_before_discount'])){
                if (!empty($_REQUEST['total_discount'])){
                    $discount = $_REQUEST['total_discount'];
                }
                $purchase_quote_data['discount_amount'] = $discount;
                 
            }
            $purchase_quote_data['final_total'] = $_REQUEST['total_before_discount'] - $discount;
            if (!empty($form_data['reqdate'])){
                $dateObj = DateTime::createFromFormat('d/m/Y', $form_data['reqdate']);
                log_message('debug','converted '.$dateObj->format('Y-m-d'));
                $purchase_quote_data['needed_by_date'] = $dateObj->format('Y-m-d');
            }
            
            $comments = appendComments($form_data['notes'], 'notes');
            $purchase_quote_data['owner_id'] = $this->user->person_id;
            
            if (empty($id)){
                $purchase_quote_data['raised_by'] = $this->user->person_id;
                $id =  $this->Purchase_quote_master->createQuote($purchase_quote_data,array('notes'=>$comments));
            }
            else {
                $where_clause = array('id'=>$id);
                $this->Purchase_quote_master->update($where_clause, $purchase_quote_data,array('notes'=>$comments));
            }
            echo $id;
        }
        function modifyQuote (){
           
            $id = $_REQUEST['id'];
            
            $oper = $_REQUEST['oper'];
            $this->db->trans_start();
            if ($oper=='edit'){
                //$dateObj = DateTime::createFromFormat('d/m/Y', $_REQUEST['needed_by_date']);
                //log_message('debug','converted item date '.$dateObj->format('Y-m-d'));
                $purchase_quote_data['needed_by_date'] = $_REQUEST['needed_by_date'];
                //while editing the supplier id is passed.
                $purchase_quote_data['supplier_id'] = $_REQUEST['supplier_name'];
                $where_clause_quote = array('id'=>$id);
                
                $this->Purchase_quote_master->update($where_clause_quote,$purchase_quote_data);
                
            }
            else if ($oper=='del'){
                
                $idAraay =  $_REQUEST['id'];
                foreach($idAraay as $tempId){
                    $where_clause_quote = array('id'=>$tempId);
                    $this->Purchase_quote_master->update($where_clause_quote,array('status'=>'cancelled'));

                    $where_clause_quote_item = array('quote_id'=>$tempId);
                    $this->Purchase_quote_item->update($where_clause_quote_item,array('status'=>'cancelled'));
                }
//                
            }
            $this->db->trans_complete();
        }
        
         function addQuoteItem (){
            
            $form_data=$_REQUEST['form_data'];
            $pricerule= $form_data['pricerule'];
            $purchase_quote_data['pricerule_type'] = $pricerule;
            $qty=$form_data['quantity'];
            if ($pricerule==self::EXISTING){
                $purchase_quote_data['pricelist_id'] = $form_data['pricelist_id'];
            }
            else if ($pricerule==self::DIRECTAMOUNTUNIT){
                $purchase_quote_data['discount_value'] = $form_data['dir_disc_unit'];
            }
            else if ($pricerule==self::DIRECTAMOUNTTOTAL){
                $purchase_quote_data['discount_value'] = $form_data['dir_disc_total'];
            }
            else if ($pricerule==self::DIRECTPERCENTAGE){
                $purchase_quote_data['discount_value'] = $form_data['dir_disc_perc'];
            }
            
            $purchase_quote_data['product_id'] =$form_data['productOp'];
            $purchase_quote_data['quote_id'] = $_REQUEST['quoteid'];
            
            if (!empty($_REQUEST['unit_discount'])){
                $unit_discount = $_REQUEST['unit_discount'];
            }
            else {
                $unit_discount = 0;
            }
            if (!empty($_REQUEST['dir_disc_fg'])){
                $free_items = $_REQUEST['dir_disc_fg'];
            }
            else {
                $free_items = 0;
            }
            if (!empty($form_data['neededByDate'])){
                $dateObj = DateTime::createFromFormat('d/m/Y', $_REQUEST['neededByDate']);
                log_message('debug','converted item date '.$dateObj->format('Y-m-d'));
                $purchase_quote_data['needed_by_date'] = $dateObj->format('Y-m-d');
            }
            $unit_price = $form_data['exPrice'];
            $purchase_quote_data['quoted_quantity'] = $qty;
            $purchase_quote_data['expected_price'] = $unit_price;
            $final_unit_price = $unit_price-$unit_discount;
            $purchase_quote_data['final_unit_price'] = $final_unit_price;
            $purchase_quote_data['estimated_value'] = $qty*$final_unit_price;
            $purchase_quote_data['free_items'] = $free_items;
            $purchase_quote_data['comments'] = $form_data['notes_item'];

            $id =  $this->createQuoteItem($purchase_quote_data);
        }
        //business logic
        function createQuoteItem($purchase_quote_data){
            $this->db->trans_start();
            /* insert into quote item */
            
            $this->Purchase_quote_item->insert($purchase_quote_data);
            log_message('debug','insert statement ='.$this->db->last_query());
            $id = $this->db->insert_id();
            /* end insert  */
            
            /* update reference number in  quote item */
            $where_clause = array('id'=>$id);
            $this->Purchase_quote_item->update($where_clause, array('reference' => 10000000 + $id));
            /* end update */
            
            /* update estimated value in quote master */
            $quote_id = $purchase_quote_data['quote_id'];
            $quote_details=$this->Purchase_quote_master->getById($quote_id);
            $estimated_value = $quote_details->estimated_value + $purchase_quote_data['estimated_value'];
            $existing_discount = $quote_details->discount_amount;
            $final_total = $estimated_value-$existing_discount;
            $this->Purchase_quote_master->update(array('id'=>$quote_id),array('estimated_value'=>$estimated_value,'final_total'=>$final_total));
            log_message('debug','update statement ='.$this->db->last_query());
            /* end update estimated value in quote master */
            
            $this->db->trans_complete();
            return $id;
        }
        function modifyQuoteItem (){
            $id = $_REQUEST['id'];
            $item_details=$this->Purchase_quote_item->getById($id);
            $quote_id = $item_details->quote_id;
            $current_est_value = $item_details->estimated_value;
            $oper = $_REQUEST['oper'];
            $this->db->trans_start();
            if ($oper=='edit'){
                //$dateObj = DateTime::createFromFormat('d/m/Y', $_REQUEST['needed_by_date']);
                //log_message('debug','converted item date '.$dateObj->format('Y-m-d'));
                $purchase_quote_data['needed_by_date'] = $_REQUEST['needed_by_date'];
                $purchase_quote_data['quoted_quantity'] = $_REQUEST['quoted_quantity'];
                $purchase_quote_data['expected_price'] = $_REQUEST['expected_price'];
                $purchase_quote_data['comments'] = $_REQUEST['comments'];
                $purchase_quote_data['estimated_value'] = $_REQUEST['quoted_quantity']*$_REQUEST['expected_price'];

                $where_clause = array('id'=>$id);
                
                $this->Purchase_quote_item->update($where_clause,$purchase_quote_data);
                
                $quote_details=$this->Purchase_quote_master->getById($quote_id);
                $estimated_value = $quote_details->estimated_value - $current_est_value + $purchase_quote_data['estimated_value'];
                $existing_discount = $quote_details->discount_amount;
                $final_total = $estimated_value-$existing_discount;
                $this->Purchase_quote_master->update(array('id'=>$quote_id),array('estimated_value'=>$estimated_value,'final_total'=>$final_total));
            }
            else if ($oper=='del'){
                $quote_details=$this->Purchase_quote_master->getById($quote_id);
                $estimated_value = $quote_details->estimated_value - $item_details->estimated_value;
                $existing_discount = $quote_details->discount_amount;
                $final_total = $estimated_value-$existing_discount;
                $this->Purchase_quote_master->update(array('id'=>$quote_id),array('estimated_value'=>$estimated_value,'final_total'=>$final_total));
                $where_clause = array('id'=>$id);
                
                $this->Purchase_quote_item->update($where_clause,array('status'=>'cancelled'));
            }
            $this->db->trans_complete();
            
        }
        
        function populateQuoteItems(){
            $quoteid = $_REQUEST['quoteId'];
            if (!empty($quoteid)){
                $quotedata = array();
                $where = array('quote_id' => $quoteid );
                $griddata=  populateGridCommon('Purchase_quote_item',$where);
                $dbrows = $griddata['db_data'];
                $data = $griddata['grid_metadata'];
                //$data['userdata']['quoted_quantity']=0;
                //['userdata']['free_items']=0;

                foreach ($dbrows as $dbrow){
                    array_push($quotedata, array('id'=> $dbrow['id'],'dprow' => array($dbrow['name'].",".$dbrow['sku'],$dbrow['quoted_quantity'],$dbrow['free_items'],$dbrow['expected_price'],$dbrow['unit_price_discount'],$dbrow['final_unit_price'],$dbrow['total_before_discount'],$dbrow['total_discount'],$dbrow['final_total'],$dbrow['needed_by_date'],$dbrow['comments'],$dbrow['product_id'],$dbrow['pricerule_type'],$dbrow['pricelist_id'],$dbrow['discount_value'])));
                    $data['userdata']['quoted_quantity']+=$dbrow['quoted_quantity'];
                    $data['userdata']['free_items']+=$dbrow['free_items'];
                    $data['userdata']['total_before_discount']+=$dbrow['total_before_discount'];
                    $data['userdata']['total_discount']+=$dbrow['total_discount'];
                    $data['userdata']['final_total']+=$dbrow['final_total'];
                }
                $data['quoteitemdata'] = $quotedata;
                $data['userdata']['name'] = 'Total';
                echo json_encode($data);
           }
           
        }
        function getRelevantPriceListId(){
            $supplier_id= $_REQUEST['supplier_id'];
//           $today = date("Y-m-d"); 
            $version = $this->Pricelist_master->applicableVersionList(date("Y-m-d"),$supplier_id);
            $ret['id']=$version->id;
            $ret['name']=$version->name;
            $ret['inherit_rules']=$version->inherit_rules;
            $ret['inherit_rules_dir']=$version->inherit_rules_dir;
            
            echo json_encode($ret) ;
            
            
        }

}
?>