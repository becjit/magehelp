<?php
require_once ("secure_area.php");

class Products extends Secure_area
{
	const ID = "id";
        const PRODUCT_NAME = "product_name";
        const SYSTEM_NAME = "system_name";
        const DESC = "description";
        const BARCODE = "barcode";
        const MFR = "manufacturer";
        const MODEL = "model";
        const SUPPLIER = "supplier";
    
        function __construct()
	{
		parent::__construct('products');
                $this->load->model('Unitofmeasure');
                $this->load->model('Category');
                $this->load->model('Packaging');
                $this->load->model('Manufacturer');
                $this->load->model('Mfr_model');
                $this->load->model('Product');
                $this->load->model('Product_stock');
                $this->load->model('Product_price');
                $this->load->helper('grid_helper');
                $this->load->library('barcode-library/Barcode_lib','','barcode');    
	}

//	function index()
//	{
//           $packageTypes = $this->Packaging->getAllPackageDetails();
//           $pkgOptions = null;
//           
//          
//           foreach($packageTypes as $packageType) { 
//                $pkg_id=$packageType["package_id"]; 
//                $pkg_name=$packageType["package_name"]; 
//                $pkgOptions.="<OPTION VALUE=\"$pkg_id\">".$pkg_name;
//                
//            } 
////            $mfrs = $this->Manufacturer->getAllMfrs();
////            
////            foreach ($mfrs as &$mfr){
////                $mfr = "\"".$mfr['manufacturer_name']."\"";
////            }
////            //$values = array_map('array_pop', $mfrs);
////            $imploded = implode(",",$mfrs);
////            $data['mfrs'] = $imploded;
//            
//            $mfrs = $this->Manufacturer->getAll();
//            $autoData = array();
//            foreach ($mfrs as $mfr){
//                $data = array('label' => $mfr['manufacturer_name'], 'value' => $mfr['id']);
//                array_push($autoData,$data);
//            }
//            $values = json_encode($autoData);
//            //$imploded = implode(",",$mfrs);
//            $data['mfrs'] = $values;
//            
//            $data['pkgOptions'] = $pkgOptions;
//            $this->load->view('products/product_details',$data);
//	}
        
        function index()
	{
           $packageTypes = $this->Packaging->getAllPackageDetails();
           $pkgOptions = null;
           $mfrOptions = null;
           
          
           foreach($packageTypes as $packageType) { 
                $pkg_id=$packageType["package_id"]; 
                $pkg_name=$packageType["package_name"]; 
                $pkgOptions.="<OPTION VALUE=\"$pkg_id\">".$pkg_name;
                
            } 
            
            
            //$values = json_encode($autoData);
            //$imploded = implode(",",$mfrs);
            $mfrs = $this->Manufacturer->getAll();
           
            foreach ($mfrs as $mfr){
                $mfr_id=$mfr["id"]; 
                $mfr_name=$mfr["manufacturer_name"]; 
                $mfrOptions.="<OPTION VALUE=\"$mfr_id\">".trim($mfr_name)."</OPTION>";
            }
            $data['mfrOptions'] = $mfrOptions;
            
            $data['pkgOptions'] = $pkgOptions;
            $this->load->view('products/products_grid',$data);
	}
        
        function populateMfrs(){
            $mfrs = $this->Manufacturer->getAll();
           
            foreach ($mfrs as $mfr){
                $mfr_id=$mfr["id"]; 
                $mfr_name=$mfr["manufacturer_name"]; 
                $mfrOptions.="<OPTION VALUE=\"$mfr_id\">".trim($mfr_name)."</OPTION>";
            }
            echo $mfrOptions;
        }
        
        function populatePackages(){
           $packageTypes = $this->Packaging->getAllPackageDetails();
           $pkgOptions = null;
           //$mfrOptions = null;
           
          
           foreach($packageTypes as $packageType) { 
                $pkg_id=$packageType["package_id"]; 
                $pkg_name=$packageType["package_name"]; 
                $pkgOptions.="<OPTION VALUE=\"$pkg_id\">".$pkg_name;
                
            } 
            echo $pkgOptions;
        }
        
        function populateMeasurementDropDowns(){
            $package_id = $_POST[pkgId];
            if (!empty($package_id)){
                $htmlUom = $this->getUOMDetails($package_id);
                echo $htmlUom;
            }
        }
        
        function populateMeasurementDropDownsByName(){
            $package_name = $_POST[pkgName];
            $package_id = $this->Packaging->getIdByPkgName($package_name);
            if (!empty($package_id)){
                $htmlUom = $this->getUOMDetails($package_id);
                echo $htmlUom;
            }
        }
        
        private function getUOMDetails($package_id){
                $uomJson = $this->Packaging->getJsonById($package_id);
                $uomArray = json_decode($uomJson,true);
                $uomList = array();
                $uomSizeMap = array();
                $htmlUom = null;
                
                foreach ($uomArray as $uomDetails){
                    $thisUom = $uomDetails['uom'];
                    //$uomId = 
                    $denom = $uomDetails['denom'];
                    if (!in_array($thisUom, $uomList)){
                        array_push($uomList, $thisUom);
                        $htmlUom.="<OPTION VALUE=\"$thisUom\">".$thisUom;
                        $uomSizeMap[$thisUom] = array(array('denom' => $denom));
                    }
                    else {
                        array_push($uomSizeMap[$thisUom], array('denom' => $denom));
                    }
                }
                session_start();
                $_SESSION['denomJson'] = json_encode($uomSizeMap);
                return $htmlUom;
        }
        
        function populateModel(){

            
            $mfr = $_POST[mfr];
                    $models = $this->Mfr_model->getAllByMfrId($mfr);
                    $htmlData= null;
            foreach ($models as $model){
                //$data = array('label' => $model['model_name'], 'value' => $model['id']);
                //array_push($autoData,$data);
                $id = $model['id'];
                $name = $model['model_name'];
                $htmlData.="<OPTION VALUE=\"$id\">".$name;
            }
            //$values = json_encode($autoData);
            echo $htmlData;
        }
        
        function populateBarcodesForStock(){
            $barcodeData = $this->Product_stock->getAll(false,null,1);
            $barcodeArray = array();
            foreach ($barcodeData as $barcoderow){
                array_push($barcodeArray,$barcoderow['barcode']);
            }
            return $barcodeArray;
            //echo json_encode($barcodeArray);
        }
        //hack for jqgrid dataUrl
        function doNothing (){
            
        }

	function populateDenomDropdown (){
            session_start();
            $uom = $_POST[uom];
            $htmlSize = null;
            
            if (isset($_SESSION['denomJson'])) {
                $allUomSizeMap = json_decode($_SESSION['denomJson'],true);
                //unset($_SESSION['denomJson']); //done. Now unset. very important
                $uomSizeMap = $allUomSizeMap[$uom];
                
                if (!empty($uomSizeMap)) {
                    asort($uomSizeMap);
                    foreach ($uomSizeMap as $validSize){
                        $size = $validSize['denom'];
                        $htmlSize.= "<OPTION VALUE=\"$size\">".$size;
                    }
                    
                }
                echo $htmlSize;
            }
        }
        
        function createBarcodeAndProduct(){
        
            
            $barCodedata = $_POST['barcodeData'];
            $barcode = null;
            $mfrCode =100;
            $modelCode = 100;
            $categoryCode = 100;
            $supplierCode = 100;
            $uomCode = 10;
            $sizeCode = 10;
            $packageCode = 10;
            $modelName = null;
            $mfrName = null;
            $modelId = null;
            $mfrId = null;
            $systemName = null;
           
            $scannedBarcode = $barCodedata['scannedBarcode'];
            $name = $barCodedata['name'];
            $desc = $barCodedata['desc'];
            $mfrOp = $barCodedata['mfrOp'];
            $mfrIp = $barCodedata['mfrIp'];
            $modelOp = $barCodedata['modelOp'];
            $modelIp = $barCodedata['modelIp'];
            $newModelIp = $barCodedata['newModelIp'];
            $categoryArray = json_decode($barCodedata['category']);
            //$categoryIp = $barCodedata['categoryIp'];
            $supplierOp = $barCodedata['supplierOp'];
            $uomOp = $barCodedata['uomOp'];
            $sizeOp = $barCodedata['sizeOp'];
            $packageOp = $barCodedata['packageOp'];
            $reorderLevel = $barCodedata['reorderLevel'];
            $costPrice = $barCodedata['costPrice'];
            $price = $barCodedata['price'];
            
            
            if (empty($reorderLevel)){
                $reorderLevel = 0;
            }
            if (!empty($mfrOp)){
                $mfrId = $mfrOp;
                $mfrCode += $mfrOp;
                if (!empty($modelOp)){
                    $modelId = $modelOp;
                    $modelCode += $modelOp;
                    $modelName = $this->Mfr_model->getName($modelOp);
                    
                }
                else {
                    if (!empty($modelIp))
                        $modelData = array('manufacturer_id'=>$mfrId,'model_name' => $modelIp);
                        $modelInsertId = $this->Mfr_model->insert($modelData);
                        $modelId = $modelInsertId;
                        $modelCode += $modelInsertId;
                        $modelName = $modelIp;
                }
                $mfrName = $this->Manufacturer->getName($mfrOp);
            }
            
            if (empty($mfrOp) && !empty($mfrIp)){
                $mfrData = array ('manufacturer_name'=>trim($mfrIp));
                $mfrName = $mfrIp;
                $mfrInsertId = $this->Manufacturer->insert($mfrData);
                $mfrCode += $mfrInsertId;
                $mfrId = $mfrInsertId;
                if ($mfrInsertId != 0  && $newModelIp!=""){
                    $modelData = array('manufacturer_id'=>$mfrInsertId,'model_name' => trim($newModelIp));
                    $modelInsertId = $this->Mfr_model->insert($modelData);
                    $modelId = $modelInsertId;
                    $modelCode += $modelInsertId;
                    $modelName = $newModelIp;
                }
                
            }
            $categorymapping = array();
            if (!empty($categoryArray)){
                foreach($categoryArray as $category){
                    $categoryCode += $category;
                    $categoryDetails = $this->Category->getById($category);
                    array_push(array('id'=>$category,'name'=> $categoryDetails['category_name']));
                    
                }
                
            }
            log_message('debug', $categorymapping);
            print_r($categorymapping);
            
            if (!empty($supplierOp)){
                $supplierCode += $supplierOp;
            }
            if (!empty($uomOp)){
                $uomCode += $uomOp;
            }
            if (!empty($sizeOp)){
                $sizeCode += $sizeOp;
            }
            if (!empty($packageOp)){
                $packageCode +=$packageOp;
            }
            
            $productid = $this->Product->lastIdPresent() + 1;
            
            if (empty($scannedBarcode)){
                $barcode = "1".$mfrCode. $modelCode . $supplierCode  .$packageCode .$uomCode.$sizeCode.$productid;
            }
            else {
               $barcode =  $scannedBarcode;
            }
            $ifexist = false;
                      
            if (empty($scannedBarcode)){
                $where_clause_if_exist = array(
                                
                                'manufacturer_id'=>$mfrId,
                                'category_id'=>$category,
                                'model_id'=>$modelId,
                                'package_id'=>$packageOp,
                                'uom'=>$uomOp,
                                'measurement_denomination'=>$sizeOp);
                $barcodeExist = $this->Product->getBarcode($where_clause_if_exist);
                if (!empty($barcodeExist)){
                   $response['status'] = 'error';
                   $response['message'] = 'The Product Your Are Trying To Add
                       Looks Like Already Exists.Please Check Barcode  '.$barcodeExist;
                   echo  json_encode($response);
                   return; 
                }
                
            }
            else {
               $ifexist =  $this->Product->ifExistsByBarcode($scannedBarcode);
               if ($ifexist){
                   $response['status'] = 'error';
                   $response['message'] = $scannedBarcode.' Already Exists';
                   echo  json_encode($response);
                   return;
               }
            }
            
//           $systemName = $name."-".$mfrName."-".$modelName."-".uom."-".$sizeOp;
            
//            $this->db->trans_start();
//            
//            $productData = array('barcode'=>$barcode,
//                                'product_name'=>$name,
////                                'system_name'=>$systemName,
//                                'description'=>$desc,
//                                'manufacturer'=>$mfrName,
//                                'manufacturer_id'=>$mfrId,
//                                'category_id'=>$category,
//                                'category_name' => $categoryName,
//                                'model'=>$modelName,
//                                'model_id'=>$modelId,
//                                'package_id'=>$packageOp,
//                                'reorder_level'=>$reorderLevel,
//                                'uom'=>$uomOp,
//                                'measurement_denomination'=>$sizeOp);
//            $insertedProductId = $this->Product->insert($productData);
//            $product_stock_data = array ('product_id'=>$insertedProductId,'barcode'=>$barcode);
//            $this->Product_stock->insert($product_stock_data);
//            $product_price_data = array ('product_id'=>$insertedProductId,'barcode'=>$barcode);
//            if (!empty($costPrice)){
//                $product_price_data['cost_price'] = $costPrice;
//            }
//            if (!empty($price)){
//                $product_price_data['price'] = $price;
//            }
//            $this->Product_price->insert($product_price_data);
//            $this->db->trans_complete();
//            
//            
//            if ($this->db->trans_status() === FALSE)
//            {
//                log_message('Product Insertion Failed');
//            }
//            
//           $response['status'] = 'success';
//            $response['message'] = 'Item '.$name. ' Has Been Successfully Added. The Barcode Is '.$barcode;
//            echo  json_encode($response);
                   
        }
        
        function editProduct (){
            $mfrName = null;
            $modelName = null;
            $id = $_REQUEST[Products::ID];
            $mfrId = $_REQUEST[Products::MFR];
            $modelId = $_REQUEST[Products::MODEL];
            if (!empty($mfrId)) {
                $mfrName = $this->Manufacturer->getName($mfrId);
            }
            if (!empty($modelId)) {
                $modelName = $this->Mfr_model->getName($modelId);
            }
            $productData = array(
            Products::PRODUCT_NAME=>$_REQUEST[Products::PRODUCT_NAME],
                                Products::DESC=>$_REQUEST[Products::DESC],
                                'manufacturer'=>$mfrName,
                                'manufacturer_id'=>$mfrId,
                                'model'=>$modelName,
                                'model_id'=>$modelId
                                );
            $this->Product->update($id,$productData);
        }
        
        function populateProductsInGrid (){
            session_start();
            $searchOn = strip($_REQUEST['_search']);
            $page = $_REQUEST['page'];
            $limit = $_REQUEST['rows'];
            $sidx = $_REQUEST['sidx'];
            $sord = $_REQUEST['sord'];
            $loadAll  = false;
           if (!empty($_REQUEST['loadall'])){
               $loadAll = $_REQUEST['loadall'];
               $_SESSION['load'] = true;
            }
            
            
           $productsdata = array();
           $count = $this->Product->totalNoOfRows();
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
                    $sessionLoadAll = $_SESSION['load'];
                    $filters = json_decode($_REQUEST['filters'],true);
                    $groupOp = $filters['groupOp'];
                    $rules = $filters['rules'];
                    $where_condition = array();
                    foreach ($rules as $rule){
                        $field = $rule['field'];
                        $op= $rule['op'];
                        $input = $rule['data'];
                        //$
                        $where_condition[$field] = $input;
                    }
                    if ($sessionLoadAll){
                        $products = $this->Product->getAllFlattenedByLike($where_condition,false,$clauses);
                        
                    }
                    else {
                        $products = $this->Product->getAllFlattenedByLike($where_condition,false,$clauses,1);
                    }
                    
                    
            }
            else {
                if (empty($_REQUEST['loadall'])) {
                    $_SESSION['load'] = false;
                }
                if ($loadAll){
                    $products = $this->Product->getAllFlattened(false,$clauses);
                }
                else {
                     $products = $this->Product->getAllFlattened(false,$clauses,1);
                }
                
                
            }
            //$dp['system_name']
            foreach ($products as $dp){
                array_push($productsdata, array('id'=> $dp['id'],'dprow' => array($dp['barcode'],/*$dp['system_name'],*/$dp['product_name'],$dp['description'],$dp['manufacturer_id'],$dp['model_id'],$dp['manufacturer'],$dp['model'],$dp['supplier'],$dp['package_id'],$dp['package_name'],/*$dp['category_name'],*/$dp['uom'],$dp['measurement_denomination'],'Print Barcode')));
            }
            $data['productdata'] = $productsdata;
            //$export = $_REQUEST['oper'];
            echo json_encode($data);
        }
        
        function deactivate(){
            $ids = $_POST[ids];
            $this->Product->deactivate($ids);
        }
        
        
        function activate(){
            $ids = $_POST[ids];
            $this->Product->activate($ids);
        }
        
        function exportProductsInGrid (){
            $export = $_REQUEST['oper'];
            if ($export =='csv')
           $searchOn = strip($_REQUEST['_search']);
           $productsdata = array();
           
           if($searchOn=='true') {
                    $filters = json_decode($_REQUEST['filters'],true);
                    $groupOp = $filters['groupOp'];
                    $rules = $filters['rules'];
                    $where_condition = array();
                    foreach ($rules as $rule){
                        $field = $rule['field'];
                        $op= $rule['op'];
                        $input = $rule['data'];
                        //$
                        $where_condition[$field] = $input;
                    }
                    $products = $this->Product->getAllFlattenedByLike($where_condition,true);
                    
            }
            else {
                $products = $this->Product->getAllFlattened(true);
                
            }
            header("Content-type: application/octet-stream");
            header("Content-description: File Transfer");
            header("Content-disposition: attachment; filename=\"thefilename.csv\"");
            header("Pragma: public");
            header("Cache-control: max-age=0");
            header("Expires: 0");
            echo $products;
        }
        
        function loadInventory (){
            $data['barcodes'] = json_encode($this->populateBarcodesForStock());
            $this->load->view('products/product_stock_details',$data);
        }
        
        function populateProductStocksInGrid (){
            //session_start();
           $searchOn = strip($_REQUEST['_search']);
           $page = $_REQUEST['page'];
           $limit = $_REQUEST['rows'];
           $sidx = $_REQUEST['sidx'];
           $sord = $_REQUEST['sord'];
            
            
           $productsdata = array();
           $count = $this->Product_stock->totalNoOfRows();
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
                    $where_condition[$field] = $input;
                }
                $products = $this->Product_stock->getAllByLike($where_condition,false,$clauses,1);
            }
            else {
                $products = $this->Product_stock->getAll(false,$clauses,1);
            }
            //$dp['system_name']
            foreach ($products as $dp){
                array_push($productsdata, array('id'=> $dp['id'],'dprow' => array($dp['product_id'],$dp['barcode'],$dp['system_name'],$dp['stock'],0,$dp['isactive'])));
            }
            $data['productdata'] = $productsdata;
            
            echo json_encode($data);
        }
        function updateStocks (){
            $id = strip($_REQUEST['id']);
            $oper = strip($_REQUEST['oper']);
            $stockAdd = strip($_REQUEST['stock_add']);
            $lastaction = 'added';
            $stockLevel = $this->Product_stock->getStockLevel('id',$id);
            $stockLevel += $stockAdd;
            $stock_data = array ('stock' => $stockLevel,'stock_added'=>$stockAdd,'last_action'=>$lastaction);
            $this->Product_stock->update($id,$stock_data);
        }
        
        function updateStocksByBarcode (){
            $barcode = strip($_REQUEST['barcode']);
            //$oper = strip($_REQUEST['oper']);
            $stockAdd = strip($_REQUEST['stock_add']);
            $lastaction = 'added';
            $stockLevel = $this->Product_stock->getStockLevel('barcode',$barcode);
            $stockLevel += $stockAdd;
            $stock_data = array ('stock' => $stockLevel,'stock_added'=>$stockAdd,'last_action'=>$lastaction);
            $this->Product_stock->updateByBarcode($barcode,$stock_data);
        }
        
        function printBarcode(){
            $id =$_REQUEST['id'];
            $barcodeArray = $this->Product->getValues(array("id"=>$id),array("barcode"));
            $barcode = $barcodeArray[0]["barcode"];
            
            $filename = IMAGEPATH.'temp/barcodefinal.png';
            $this->load->helper('file');
            
            delete_files($filename);
                        if ( ! write_file($filename, ''))
            {
                echo 'Unable to write the file';
            }
            $this->barcode->generateBarcode($barcode,$filename);
            $data['filename'] = $filename;
             
            $this->load->view("utilities/barcode",$data);
            
        }
        
}
?>