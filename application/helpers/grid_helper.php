<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


    
    
    function generateBarcode($barCodedata)
    {
        $name = $barCodedata['name'];
        $desc = $barCodedata['desc'];
        $mfrOp = $barCodedata['mfrOp'];
        $mfrIp = $barCodedata['mfrIp'];
        $modelOp = $barCodedata['modelOp'];
        $modelIp = $barCodedata['modelIp'];
        $categoryOp = $barCodedata['categoryOp'];
        $categoryIp = $barCodedata['categoryIp'];
        $supplierOp = $barCodedata['supplierOp'];
        $uomOp = $barCodedata['uomOp'];
        $sizeOp = $barCodedata['sizeOp'];
        $packageOp = $barCodedata['packageOp'];
        

        return $results;
    }
    
    function strip($value)
    {
            if(get_magic_quotes_gpc() != 0)
            {
            if(is_array($value))  
                            if ( array_is_associative($value) )
                            {
                                    foreach( $value as $k=>$v)
                                            $tmp_val[$k] = stripslashes($v);
                                    $value = $tmp_val; 
                            }				
                            else  
                                    for($j = 0; $j < sizeof($value); $j++)
                                    $value[$j] = stripslashes($value[$j]);
                    else
                            $value = stripslashes($value);
            }
            return $value;
    }
    

?>
