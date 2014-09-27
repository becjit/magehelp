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
                $this->load->model('Product_stock');
                $this->load->model('Product_price');
                $this->load->helper('grid_helper');
                $this->load->library('barcode-library/Barcode_lib','','barcode');    
	}
        function index()
	{
            $data['mfrOptions'] = populateManfacturers();
            $data['attributeSetOptions'] = populateAttributeSets();
            $data['pkgOptions'] = populatePackages();
            $data['supplierOptions'] = populateSuppliers();
            $this->load->view('products/products_grid',$data);
	}
        
        function populateMfrs(){
            echo populateManfacturers();
        }
        
        function populatePackages(){
            echo populatePackages();
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
            echo populateModels($mfr);
        }
        
        function populateBarcodesForStock(){
            $barcodeData = $this->Product_stock->getAll(false,null,1);
            $barcodeArray = array();
            foreach ($barcodeData as $barcoderow){
                array_push($barcodeArray,$barcoderow['barcode']);
            }
            return $barcodeArray;
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
            $categoryCode = 100; //not used
//            $supplierCode = 100;
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
            $override = $barCodedata['override'];
//            $costPrice = $barCodedata['costPrice'];
//            $price = $barCodedata['price'];
            $attributeSet = $barCodedata['attributeSet'];
            $this->db->trans_start();
            if (!empty($uomOp)){
                 
                $uomId =  $this->Unitofmeasure->getByName($uomOp,array('id'),false)->id;
            }
            if (!empty($scannedBarcode)){
                if ($this->Product->ifExistsByBarcode($scannedBarcode)){
                    $response['status'] = 'error';
                    $response['message'] = 'The Product With Barcode ' . $scannedBarcode. ' Already Exists In The System' ;
                    echo json_encode($response);
                    return;
                }
            }
            else if (empty($scannedBarcode) && $override !=1){
                if (!empty($mfrOp)){
                    $where['manufacturer_id'] = $mfrOp;
                }
                if (!empty($modelOp)){
                    $where['model_id'] = $modelOp;
                }
                if (!empty($uomOp)){
                    $where['uom'] = $uomOp;
                }
                if (!empty($sizeOp)){
                    $where['measurement_denomination'] = $sizeOp;
                }
                if (!empty($packageOp)){
                    $where['package_id'] = $packageOp;
                }
                $approx_match=$this->Product->totalNoOfRows($where,null,array('product_name'=>$name));
                if ($approx_match>0){
                    $response['status'] = 'exists';
                    $response['message'] = 'We found  ' . $approx_match. ' Products Which Approximately Matches With Your Product' ;
                    echo json_encode($response);
                    log_message('debug','total statement ='.$this->db->last_query());
                    return;
                }
                //echo $this->Product->totalNoOfRows($where,null,array('product_name'=>$name));
                
//                if (){
//                    
//                }
            }
            
//            if (empty($reorderLevel)){
//                $reorderLevel = 0;
//            }
            if (!empty($mfrOp)){
                $mfrId = $mfrOp;
                $mfrCode += $mfrOp;
                if (!empty($modelOp)){
                    $modelId = $modelOp;
                    $modelCode += $modelOp;
                    $modelName = $this->Mfr_model->getName($modelOp);
                    
                }
                else {
                    if (!empty($modelIp)){
                        $modelData = array('manufacturer_id'=>$mfrId,'model_name' => $modelIp);
                    
                        $modelInsertId = $this->Mfr_model->insert($modelData);
                        $modelId = $modelInsertId;
                        $modelCode += $modelInsertId;
                        $modelName = $modelIp;
                    }
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
                    array_push($categorymapping,array('id'=>$category,'name'=> $categoryDetails['category_name']));
                    
                }
                
            }
            
            //print_r(json_en$categorymapping);
            log_message('debug',  json_encode($categorymapping));
//            
//            if (!empty($supplierOp)){
//                $supplierCode += $supplierOp;
//            }
            if (!empty($uomId)){
                $uomCode += $uomId;
            }
            if (!empty($sizeOp)){
                $sizeCode += $sizeOp;
            }
            if (!empty($packageOp)){
                $packageCode +=$packageOp;
            }
            
            $productid = $this->Product->lastIdPresent() + 1;
            
            if (empty($scannedBarcode)){
                $barcode = "1".$mfrCode. $modelCode.$packageCode .$uomCode.$sizeCode.$productid;
            }
            else {
               $barcode =  $scannedBarcode;
            }
            //$ifexist = false;
                      
//            if (empty($barcode)){
//                if (!empty($mfrId) && !empty($modelId)){
//                    $where_clause_if_exist = array(
//
//                                    'manufacturer_id'=>$mfrId,
//    //                                'category_id'=>$category,
//                                    'model_id'=>$modelId,
//                                    'package_id'=>$packageOp,
//                                    'uom'=>$uomOp,
//                                    'measurement_denomination'=>$sizeOp);
//                    $barcodeExist = $this->Product->getBarcode($where_clause_if_exist);
//                    if (!empty($barcodeExist)){
//                       $response['status'] = 'error';
//                       $response['message'] = 'The Product Your Are Trying To Add
//                           Looks Like Already Exists.Please Check Barcode  '.$barcodeExist;
//                       echo  json_encode($response);
//                       return; 
//                    }
//                }
                 
//                $where_clause_if_exist = array(
//
//                                'product_name'=>$name);
//                $barcodeExist = $this->Product->getBarcode($where_clause_if_exist);
//                if (!empty($barcodeExist)){
//                   $response['status'] = 'error';
//                   $response['message'] = 'The Product Your Are Trying To Add
//                       Looks Like Already Exists.Please Check Barcode  '.$barcodeExist;
//                   echo  json_encode($response);
//                   return; 
//                }
//            }
//            else {
//            $ifexist =  $this->Product->ifExistsByBarcode($barcode);
//            if ($ifexist){
//                $response['status'] = 'error';
//                $response['message'] = $barcode.' Already Exists';
//                echo  json_encode($response);
//                return;
//            }
           $defaultDesc = $name.'-'.$mfrName.'-'.$modelName.' '.$sizeOp.' '.$uomOp.' ';
            $productData = array('barcode'=>$barcode,
                    'product_name'=>$name,
                    'description'=> $defaultDesc,// by default
                    'meta_description'=>$defaultDesc, // by default
                    'manufacturer'=>$mfrName,
                    'manufacturer_id'=>$mfrId,
                    'model'=>$modelName,
                    'model_id'=>$modelId,
                    'package_id'=>$packageOp,
                    'uom'=>$uomOp,
                    'attribute_set'=>$attributeSet,
                    'measurement_denomination'=>$sizeOp);
            $insertedProductId = $this->Product->insert($productData);
            $this->Supplier->createProductSupplierMapping($insertedProductId,$supplierOp);
            $product_stock_data = array ('product_id'=>$insertedProductId,'barcode'=>$barcode);
            $this->Product_stock->insert($product_stock_data);
//            $product_price_data = array ('product_id'=>$insertedProductId,'barcode'=>$barcode);
//            if (!empty($costPrice)){
//                $product_price_data['cost_price'] = $costPrice;
//            }
//            if (!empty($price)){
//                $product_price_data['price'] = $price;
//            }
//            $this->Product_price->insert($product_price_data);
            foreach ($categorymapping as $mapping){
                $this->Category->saveProductCategoryMapping($mapping['id'],$insertedProductId,$mapping['name'],$barcode);
            }
            $this->db->trans_complete();
            
            
            if ($this->db->trans_status() === FALSE)
            {
                log_message('Product Insertion Failed');
            }
            
           $response['status'] = 'success';
           $response['message'] = 'Item '.$name. ' Has Been Successfully Added. The Barcode Is '.$barcode;
           echo  json_encode($response);
        }
        
        function editProduct (){
            $editdata = $_REQUEST['editData'];
            $id = $editdata['id'];
            
            
            $productData = array(
            'product_name'=>$editdata['name'],
                                'description'=>$editdata['desc'],
                                'meta_description'=>$editdata['meta_desc'],
                                'reorder_level'=>$editdata['reorderLevel'],
                                'vat'=>$editdata['vat'],
                                'attribute_set'=>$editdata['attributeSet'],
                                'margin_type'=>$editdata['margin_type'],
                                'margin_value'=>$editdata['margin_value'],
                                );
            $this->Product->update($id,$productData);
            $response['status'] = 'success';
           $response['message'] = 'Item '.$name. ' Has Been Successfully Modified';
           echo  json_encode($response);
        }
        
//        function populateProductsInGrid (){
//            session_start();
//            $searchOn = strip($_REQUEST['_search']);
//            $page = $_REQUEST['page'];
//            $limit = $_REQUEST['rows'];
//            $sidx = $_REQUEST['sidx'];
//            $sord = $_REQUEST['sord'];
//            $loadAll  = false;
//           if (!empty($_REQUEST['loadall'])){
//               $loadAll = $_REQUEST['loadall'];
//               $_SESSION['load'] = true;
//            }
//            
//            
//           $productsdata = array();
//           $count = $this->Product->totalNoOfRows();
//           if( $count > 0 && $limit > 0) { 
//                $total_pages = ceil($count/$limit); 
//            } else { 
//                $total_pages = 0; 
//            } 
//            if ($page > $total_pages) $page=$total_pages;
//            
//            $start = $limit*$page - $limit;
// 
//            // if for some reasons start position is negative set it to 0 
//            // typical case is that the user type 0 for the requested page 
//            if($start <0) $start = 0; 
//            $clauses = array('orderBy'=>$sidx,'orderDir'=>$sord,'startLimit'=>$start,'limit'=>$limit);
//            
//           $data['total'] = $total_pages;
//           $data['page'] = $page;
//            $data['records'] = $count; 
//           if($searchOn=='true') {
//                    $sessionLoadAll = $_SESSION['load'];
//                    $filters = json_decode($_REQUEST['filters'],true);
//                    $groupOp = $filters['groupOp'];
//                    $rules = $filters['rules'];
//                    $where_condition = array();
//                    foreach ($rules as $rule){
//                        $field = $rule['field'];
//                        $op= $rule['op'];
//                        $input = $rule['data'];
//                        //$
//                        $where_condition[$field] = $input;
//                    }
//                    if ($sessionLoadAll){
//                        $products = $this->Product->getAllFlattenedByLike($where_condition,false,$clauses);
//                        
//                    }
//                    else {
//                        $products = $this->Product->getAllFlattenedByLike($where_condition,false,$clauses,1);
//                    }
//                    
//                    
//            }
//            else {
//                if (empty($_REQUEST['loadall'])) {
//                    $_SESSION['load'] = false;
//                }
//                if ($loadAll){
//                    $products = $this->Product->getAllFlattened(false,$clauses);
//                }
//                else {
//                     $products = $this->Product->getAllFlattened(false,$clauses,1);
//                }
//                
//                
//            }
//            //$dp['system_name']
//            foreach ($products as $dp){
//                array_push($productsdata, array('id'=> $dp['id'],'dprow' => array($dp['barcode'],/*$dp['system_name'],*/$dp['product_name'],$dp['description'],$dp['manufacturer_id'],$dp['model_id'],$dp['manufacturer'],$dp['model'],/*$dp['supplier'],*/$dp['package_id'],$dp['package_name'],/*$dp['category_name'],*/$dp['uom'],$dp['measurement_denomination'],$dp['attribute_set'],$dp['reorder_level'],$dp['vat'],$dp['meta_description'],'Print Barcode'),$dp['measurement_denomination'] ." ".$dp['uom']));
//            }
//            $data['productdata'] = $productsdata;
//            //$export = $_REQUEST['oper'];
//            echo json_encode($data);
//        }
        
         function populateProductsInGrid (){
            
            $productsdata = array();
            $where = array();
            $in_where = array();
            $loadall = $_REQUEST['loadall'];
            if (!$loadall){
                $where['isactive'] = 1;
            }
            //setOwnerStatusCommon($where,$in_where);
            if (!empty($_REQUEST['mfr_id'])){
                $where['manufacturer_id'] = $_REQUEST['mfr_id'];
            }
            if (!empty($_REQUEST['model_id'])){
                $where['model_id'] = $_REQUEST['model_id'];
            }
            if (!empty($_REQUEST['uom'])){
                $where['uom'] = $_REQUEST['uom'];
            }
            if (!empty($_REQUEST['size'])){
                $where['measurement_denomination'] = $_REQUEST['size'];
            }
            if (!empty($_REQUEST['package_id'])){
                $where['package_id'] = $_REQUEST['package_id'];
            }
            $or_where = array();
            if (!empty($_REQUEST['product_name'])){
                $or_where['product_name'] = $_REQUEST['product_name'];
            }

            //standard response parameters 
            $griddata=  populateGridCommon('Product',$where,null,$in_where,$or_where);
            $dbrows = $griddata['db_data'];
            $data = $griddata['grid_metadata'];

//            
            foreach ($dbrows as $dp){
                array_push($productsdata, array('id'=> $dp['id'],'dprow' => array($dp['barcode'],/*$dp['system_name'],*/$dp['product_name'],$dp['description'],$dp['manufacturer_id'],$dp['model_id'],$dp['manufacturer'],$dp['model'],/*$dp['supplier'],*/$dp['package_id'],$dp['package_name'],/*$dp['category_name'],*/$dp['uom'],$dp['measurement_denomination'],$dp['attribute_set'],$dp['reorder_level'],$dp['vat'],$dp['margin_type'],$dp['margin_value'],$dp['meta_description'],'Print Barcode'),$dp['measurement_denomination'] ." ".$dp['uom']));
            }
            $data['productdata'] = $productsdata;
            //$export = $_REQUEST['oper'];
            echo json_encode($data);
        }
        
        function populateProductsSuppliersGrid (){
            
           $searchOn = strip($_REQUEST['_search']);
           $page = $_REQUEST['page'];
           $limit = $_REQUEST['rows'];
           $sidx = $_REQUEST['sidx'];
           $sord = $_REQUEST['sord'];
            
           $productcategorydata = array();
           
           $count = $this->Supplier->totalNoOfRowsInProductSupplierMapping();
           
           
           
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
           $data['records'] = strval($count); 
                      
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
                $productsuppliers = $this->Supplier->getAllProductsSupplierMapping(false,null,array('id','product_name','barcode','product_id','supplier_id','supplier_name'),$clauses,$like_condition);
            }
            else {
                $productsuppliers = $this->Supplier->getAllProductsSupplierMapping(false,null,array('id','product_name','barcode','product_id','supplier_id','supplier_name'),$clauses);
            }
//            $id=1;
            
            foreach ($productsuppliers as $dp){
                array_push($productcategorydata, array('id'=> $dp['id'],'dprow' => array($dp['product_name'],$dp['barcode'],$dp['product_id'],$dp['supplier_id'],$dp['supplier_name'])));
            }
            $data['productsupplierdata'] = $productcategorydata;
            
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
            $stock_data = array ('stock' => $stockLevel,'p'=>$stockAdd,'last_action'=>$lastaction);
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
        
        function addcategoryToProduct () {
            log_message('debug','productop  '. $_REQUEST['productOp']);
            log_message('debug','productIp  '.$_REQUEST['productIp']);
            
            log_message('debug',  'category  '.$_REQUEST['category']);
            $categoryArray = $_REQUEST['category'];
            if (!empty($categoryArray)){
                foreach($categoryArray as $category){
                    $this->Category->saveProductCategoryMapping($category,$_REQUEST['productOp']);
                }
                
            }
        }
        
        function populateProductCategoryMapping () {
           $searchOn = strip($_REQUEST['_search']);
           $page = $_REQUEST['page'];
           $limit = $_REQUEST['rows'];
           $sidx = $_REQUEST['sidx'];
           $sord = $_REQUEST['sord'];
            
           $productcategorydata = array();
           
           $count = $this->Category->totalNoOfRowsInProductCategoryMapping();
           
           
           
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
           $data['records'] = strval($count); 
                      
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
                $productcategories = $this->Category->getAllProductCategoryMapping(false,null,$clauses,$like_condition);
            }
            else {
                $productcategories = $this->Category->getAllProductCategoryMapping(false,null,$clauses);
            }
            
            
            foreach ($productcategories as $dp){
                array_push($productcategorydata, array('id'=> $dp['id'],'dprow' => array($this->Product->getByProductId($dp['product_id'])->product_name,$dp['barcode'],$dp['product_id'],$dp['category_id'],$dp['category_name'])));
            }
            $data['productcategorydata'] = $productcategorydata;
            
            echo json_encode($data);
        }
        
         function loadProductCategoryMapping () {
            $productArray = $this->Product->getValues();
            foreach ($productArray as $product){
                //$data = array('label' => $model['model_name'], 'value' => $model['id']);
                //array_push($autoData,$data);
                $id = $product['id'];
                $name = $product['product_name'].'->'.$product['barcode'];
                $productOptions.="<OPTION VALUE=\"$id\">".$name;
            }
            $data['productOptions'] = $productOptions;
            $this->load->view("products/product_category_grid",$data);
        }
        function loadProductSupplierMapping () {
            $productArray = $this->Product->getValues();
            foreach ($productArray as $product){
                //$data = array('label' => $model['model_name'], 'value' => $model['id']);
                //array_push($autoData,$data);
                $id = $product['id'];
                $name = $product['product_name'].'->'.$product['barcode'];
                $productOptions.="<OPTION VALUE=\"$id\">".$name;
            }
            $data['productOptions'] = $productOptions;
            $data['supplierOptions'] = populateSuppliers();
            $this->load->view("products/product_supplier_grid",$data);
        }
        
        function deleteProductCategoryMapping(){
            $idAraay = $_REQUEST['id'];
            //$idAraay =  explode(",", $ids);
            //log_message('debug',  explode(",", $idAraay));
            foreach ($idAraay as $id){
                $this->Category->deleteProductCategoryMapping($id);
            }
        }
        
         function deleteSupplierProductMapping(){
            $idAraay = $_REQUEST['id'];
            //$idAraay =  explode(",", $ids);
            //log_message('debug',  explode(",", $idAraay));
            foreach ($idAraay as $id){
                $this->Supplier->deleteSupplierMapping($id);
            }
        }
        
        function addSupplierToProduct () {
            log_message('debug','productop  '. $_REQUEST['productOp']);
            log_message('debug','productIp  '.$_REQUEST['productIp']);
            
            $supplierArray = $_REQUEST['supplier'];
            
            
            if (!empty($supplierArray)){
                $this->Supplier->createProductSupplierMapping($_REQUEST['productOp'],$supplierArray);
                
            }
        }
        
        
}
?>