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
class Impex extends Secure_area {
    function __construct()
	{
		parent::__construct('impex','adminmenu');
                $this->load->model('Product_stock');
               
	}
	
	function index(){
                 
               
		
	}
        
        public function importAttributeSets (){
            $status = $this->Attribute_set->importAttributeSets();
            log_message('debug', 'status of Attribute set import '.$status);
        }
        
        public function importCategories(){
           $status =  $this->Category->importCategories();
           log_message('debug', 'status of category import '.$status);
        }
        
        public function importInvoices(){
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
        }
        
        function exportProductsMagmi (){
            $error = false;
            $result = $this->Product->getMagentoExportView();
            $resource = $this->Resource->getByResourceName('exportdir-impexconfig');
            
            $filename = $resource['relative_path_link'] . $this->Appconfig->get('create_product_file');
//            $filename = $result['relative_path_link'];
            $this->load->helper('file');
            
            //var_dump(($result));
            //delete_files($filename);
            //delete_files($filename);
             if ( ! write_file($filename,$result ,'w+'))
            {
               $error = true;
            }
            $data['mode'] = $this->Appconfig->get('create_product_mode');
            $data['profile'] = $this->Appconfig->get('create_product_profile');
            $data['filename'] = $filename;
            $data['error'] = $error;
            $this->load->view('impex/process_magento',$data);
            
        }
        function updateStocksInMagmi (){
            $error = "false";
            //$result = $this->Product->getMagentoExportView();
            $resource = $this->Resource->getByResourceName('exportdir-impexconfig');
            
            $filename = $resource['relative_path_link'] . $this->Appconfig->get('update_stock_csv');
//            $filename = $result['relative_path_link'];
            $this->load->helper('file');
            
            //var_dump(($result));
            //delete_files($filename);
            //delete_files($filename);
//             if ( ! write_file($filename,$result ,'w+'))
//            {
//               $error = true;
//            }
            if ($this->Product_stock->totalNoOfRowsInHistory (array('sync_status'=>0,'update_status' =>'inprocess','initiated_by'=>$this->User->get_logged_in_employee_info()->person_id))>0){
                 $error = "true";
            }
            $data['mode'] = $this->Appconfig->get('update_stock_mode');
            $data['profile'] = $this->Appconfig->get('update_stock_profile');
            $data['server_url'] = $this->Appconfig->get('server_url');
            $data['filename'] = $filename;
            $data['error'] = $error;
            $this->load->view('impex/update_stock',$data);
            
        }
        
        function createExportStockFile (){
            
             $idArray = $_REQUEST['selected'];
             if (!empty($idArray)){
                 $in_where['field_name']='id';
                 $in_where['value_array']=$idArray;
             }
            $resource = $this->Resource->getByResourceName('exportdir-impexconfig');
            $products = $this->Product_stock->getAll(true,null,1,null,array('barcode as sku',"CONCAT('+',balance_to_update) as qty"),null,$in_where);
            //var_dump($products);
            $filename = $resource['relative_path_link'] . $this->Appconfig->get('update_stock_csv');
            $this->load->helper('file');
            
            //var_dump(($result));
            //delete_files($filename);
            //delete_files($filename);
            if ( ! write_file($filename,$products ,'w+'))
            {
               $error = true;
               $data['status'] = 'error';
               $data['message'] = 'The File' .$filename.' Could Not Be Created';
            }
            else {
                $data['status'] = 'success';
                $data['message'] = 'The File' .$filename.' Successfully Created';
            }
            
            echo json_encode($data);
            
        }
        function syncNeeeded(){
             $idArray = explode(',',$_REQUEST['selected']);
             //$idArray = explode(',','13,15,17,19');
            
             //var_dump($idArray);
             if (!empty($idArray)){
                 $in_where['field_name']='id';
                 $in_where['value_array']=$idArray;
                 $products = $this->Product_stock->getAll(false,null,1,null,array('barcode','product_id',"balance_to_update"),null,$in_where);
                 
                 foreach ($products as $index=>$array){
                     //array_push($product,array('initiated_by'=>$this->User->get_logged_in_employee_info()->person_id));
                     //$product['initiated_by']=$this->User->get_logged_in_employee_info()->person_id;
                     $products[$index]['initiated_by']=$this->User->get_logged_in_employee_info()->person_id;
                 }
                 //var_dump($products);
                 $this->Product_stock->insertStockUpdateHistoryBatch($products);
             }
            
            
        }
//        function createExportTest (){
//             //$idArray = $_REQUEST['selected'];
//             $products = array();
//             array_push($products, "1");
//             array_push($products, "2");
//             array_push($products, "3");
//             if (!empty($idArray)){
//                 $in_where['field_name']='id';
//                 $in_where['value_array']=$idArray;
//             }
//            $resource = $this->Resource->getByResourceName('exportdir-impexconfig');
////           /
//            foreach($products as $p){
//                $result .=$p.","."success,\n";
//            }
//            $filename = $resource['relative_path_link'] . 'test';
//            $this->load->helper('file');
//            
//            //var_dump(($result));
//            //delete_files($filename);
//            //delete_files($filename);
//             if ( ! write_file($filename,$result ,'w+'))
//            {
//               $error = true;
//            }
//            //echo 'test';
//        }
        function doSync(){
            $file = fopen('/var/www/magehelp/sku_status.csv', 'r+');
            while (($line = fgetcsv($file)) !== FALSE) {
              //$line is an array of the csv elements
              
              if ($line[1]=='success'){
                  $this->db->trans_start();
                  $result = $this->Product_stock->getHistoryData(array('barcode'=>$line[0],'update_status'=>'inprocess','sync_status'=>0),array('balance_to_update'));
                  $balance = $result->balance_to_update;
                  log_message("debug","baance ".$balance);
                  $this->Product_stock->updateStockUpdateHistory(array('barcode'=>$line[0],'update_status'=>'inprocess'),array('update_status'=>'success','sync_status'=>1));
                  $this->Product_stock->updateGeneral(array('barcode'=>$line[0]),null,array('balance_to_update'=>'balance_to_update -' .$balance));
                  $this->db->trans_complete();
              }
              else  if ($line[1]=='failure'){
                  $this->Product_stock->updateStockUpdateHistory(array('barcode'=>$line[0],'update_status'=>'inprocess'),array('update_status'=>'failure','sync_status'=>1));
              }
            }
            ftruncate($file, 0);
            fclose($file);
            $data['status'] = 'success';
            echo json_encode($data);
        }
          function populateProductsStocksToExport(){
           
           $page = $_REQUEST['page'];
           $limit = $_REQUEST['rows'];
           $sidx = $_REQUEST['sidx'];
           $sord = $_REQUEST['sord'];
           $where['balance_to_update >']  = 0;
            
           $productsdata = array();
           $count = $this->Product_stock->totalNoOfRows($where);
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
           
           $products = $this->Product_stock->getAll(false,$clauses,1,$where);
          
            //$dp['system_name']
            foreach ($products as $dp){
                array_push($productsdata, array('id'=> $dp['id'],'dprow' => array($dp['barcode'],$dp['product_id'],$dp['balance_to_update'])));
            }
            $data['productstockdata'] = $productsdata;
            
            echo json_encode($data);
            
        }
        
        function populateProductsPrice(){
           
           $page = $_REQUEST['page'];
           $limit = $_REQUEST['rows'];
           $sidx = $_REQUEST['sidx'];
           $sord = $_REQUEST['sord'];
//           $where['balance_to_update >']  = 0;
            
           $productsdata = array();
           $count = $this->Product_price->totalNoOfRows($where);
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
            $clauses = array('orderBy'=>'product_id','orderDir'=>$sord,'startLimit'=>$start,'limit'=>$limit);
            
           $data['total'] = $total_pages;
           $data['page'] = $page;
           $data['records'] = $count; 
           $columns = array('product_id','barcode','product_name','inv_total_cost',
               'total_balance','inv_cost_price','vat','sales_tax','margin_type','margin_value',"CASE margin_type WHEN 'none' THEN ROUND(`inv_cost_price`*(1+(`vat`/100)),2) WHEN 'amount' THEN `inv_cost_price` + ROUND(`inv_cost_price`*(`vat`/100),2) + `margin_value` 
WHEN 'percentage' THEN ROUND((`inv_cost_price` + `inv_cost_price`*(`vat`/100))*(1+(`margin_value`/100)),2 )                  
ELSE 0 END 'calculated'");
               //'`inv_cost_price`*`vat` as calculated');
           
           $products = $this->Product_price->getAll(false,$where,$columns,$clauses);
           //var_dump($products);
          
            //$dp['system_name']
            foreach ($products as $dp){
                array_push($productsdata, array('product_id'=> $dp['product_id'],'dprow' => array($dp['barcode'],$dp['product_name'],$dp['inv_total_cost']
                    ,$dp['total_balance'],$dp['inv_cost_price'],$dp['vat']
                    ,/*$dp['sales_tax'],*/$dp['margin_type'],$dp['margin_value'],$dp['calculated']
                    )));
            }
            $data['productstockdata'] = $productsdata;
            
            echo json_encode($data);
            
        }
        
        function dbprefix (){
            var_dump ($this->db->dbprefix);
        }
        
        function updatePriceInMagmi (){
            $data['mode'] = $this->Appconfig->get('update_price_mode');
            $data['profile'] = $this->Appconfig->get('update_price_profile');
            $data['server_url'] = $this->Appconfig->get('server_url');
            $data['filename'] = $filename;
           
            $this->load->view('impex/update_price',$data);
            
        }
        function createExportPriceFile (){
            
             $idArray = $_REQUEST['selected'];
             if (!empty($idArray)){
                 $in_where['field_name']='product_id';
                 $in_where['value_array']=$idArray;
             }
            $resource = $this->Resource->getByResourceName('exportdir-impexconfig');
//            $columns = array('product_id','barcode','product_name','inv_total_cost',
//               'total_balance','inv_cost_price','vat','sales_tax','margin_type','margin_value',"CASE margin_type WHEN 'none' THEN ROUND(`inv_cost_price`*`vat`,2) WHEN 'amount' THEN `inv_cost_price` + ROUND(`inv_cost_price`*(`vat`/100),2) + `margin_value` 
//WHEN 'percentage' THEN ROUND((`inv_cost_price` + `inv_cost_price`*(`vat`/100))*(1+(`margin_value`/100)),2 )                  
//ELSE 0 END 'calculated'");
            $columns = array('barcode as sku','inv_cost_price as cost',"CASE margin_type WHEN 'none' THEN ROUND(`inv_cost_price`*(1+(`vat`/100)),2) WHEN 'amount' THEN `inv_cost_price` + ROUND(`inv_cost_price`*(`vat`/100),2) + `margin_value` 
WHEN 'percentage' THEN ROUND((`inv_cost_price` + `inv_cost_price`*(`vat`/100))*(1+(`margin_value`/100)),2 )                  
ELSE 0 END 'price'");
               //'`inv_cost_price`*`vat` as calculated');
           
           $products = $this->Product_price->getAll(true,null,$columns,null,null,$in_where);
            //var_dump($products);
            $filename = $resource['relative_path_link'] . $this->Appconfig->get('update_price_csv');
            $this->load->helper('file');
            
            //var_dump(($result));
            //delete_files($filename);
            //delete_files($filename);
            if ( ! write_file($filename,$products ,'w+'))
            {
               $error = true;
               $data['status'] = 'error';
               $data['message'] = 'The File' .$filename.' Could Not Be Created';
            }
            else {
                $data['status'] = 'success';
                $data['message'] = 'The File' .$filename.' Successfully Created';
            }
            
            echo json_encode($data);
            
        }
        
    }
    
    

?>
