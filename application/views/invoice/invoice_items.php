<html>
    <head>
       <?php $this->load->view("common/header"); ?> 
        <style>
            .ui-button {margin:.5em;}
        </style>
    </head>
    <body onLoad="extractOrder()">
        <?php $this->load->view("common/menubar"); ?>
        <?php if (isset($invoiceId)) :?>
         SKU: <input type="text" id="scannedSKU" name="scannedSKU" autofocus="autofocus"/>
        
        <form id="invoiceform" method="post" name="invoiceform">
            
           <div id ="formdata">
               
                <input type="hidden" id="invoiceId" name="invoiceId" value=""/>
                 <table id="skutable" border="1">
                     <thead>
                         <tr>

                            <th>Sku</th>
                            <th>Name</th>
                            <th>Number Invoiced</th>
                            <th>Number Packed</th>
                        </tr>
                     </thead>
                     <tbody>
                         <tr/>
                     </tbody>
               
                </table>
 
                <br/>
                <br/>
                <b id="commentsLabel" style="vertical-align:top;">Comments   </b><textarea id="comments" name ="comments" rows="4" cols="40" ></textarea><br/>
                <input type="hidden" id ="isMatched" name="isMatched" value="1"/>
                <input type="hidden" id ="jsonItems" name="jsonItems" value=""/>
                <input type="hidden" id ="orderId" name="orderId" value=""/>
                <input type="submit" value="Pack and Get Another Invoice" onclick="formaction('addMore')"/>
                <input type="submit" value="Cancel and Get Another Invoice " onclick="formaction('cancel')"/>
                <input type="submit" value="Go For Shipment" onclick="formaction('shipment')"/>
                
                </div> 
        </form>
        <?php else: ?>
             <a href="index.php/invoice/addInvoice">Get Magento Invoice</a>
        <?php endif; ?>
    
    </body>
</html>

<script>
        var skus = {};
        
        var isCancel = false;
        
        var invoiceIdJS = '<?php echo $invoiceId ?>';
        
        var orderId = null; ;
        
            //var entityIDs = new Array();
        $("#scannedSKU").keypress(function(event) {
            if ( event.which == 13 ) {
                event.preventDefault();
                var sku= $("#scannedSKU").val();
                if (sku) {
                    if (typeof(skus[sku]) == 'undefined' || skus[sku] == null){
                        alert ("The item your are trying to scan is not present in the Invoice. Please check")
                        }
                        else {
                            var $rows = $("#skutable tr");
                            for (i=0 ; i<$rows.length; i++){
                                var $row = $rows.eq(i);
                                var $sku =$row.find('td:eq(0)');
                                

                                if ($sku.length > 0){
                                    var newSku = $sku.html().trim();
                                    var itemInv = $row.find('td:eq(2)').html().trim();
                                    
                                    if (sku == newSku){
                                        
                                        var $itemScanned = $row.find('td:eq(3)');
                                        var noOfItems = $itemScanned.html().trim();
                                        noOfItems++;
                                        $itemScanned.html(noOfItems);
                                        skus[sku] = noOfItems;
                                        if (itemInv == noOfItems){
                                          $($row).css("background-color","#86B404"); 
                                          
                                        }
                                       
                                        break;
                                    }
                                }       
                            }
                        }
                    }
                $("#scannedSKU").val("");    
            }
        }); 
                                  
            
            
    function extractOrder() {
        var orderStr =  jQuery.parseJSON('<?php echo $response ?>');

        for (i=0; i <orderStr.length; i++){
           var sku = orderStr[i]['sku'];
           var name = orderStr[i]['name'];
           
           if (orderId == null){
               orderId =  orderStr[i]['order_id'];
           }
          
           
           if (typeof(skus[sku]) == 'undefined' || skus[sku] == null ){
               
                skus[sku] = 0;
                $("#skutable > tbody tr:last").after(
                "<tr class=" + "scannedRow" + ">\n\
                    <td>"+ sku + "</td>\n\
                    <td>"+ name + "</td>\n\
                    <td>"+ 1 + "</td>\n\
                    <td>"+ 0 +"</td>\n\
                </tr>");
            }

            else {
                var $rows = $("#skutable tr");
                for (j=0 ; j<$rows.length; j++){
                    var $row = $rows.eq(j);
                    var $sku =$row.find('td:eq(0)');

                    if ($sku.length > 0){
                        var newSku = $sku.html().trim();
                        if (sku == newSku){
                            var $itemScanned = $row.find('td:eq(2)');
                            var noOfItems = $itemScanned.html().trim();
                            noOfItems++;
                            $itemScanned.html(noOfItems);

                        }
                    }       

                }
            }

        }

    }

                                        
                                        
  // live update 
    $(".scannedRow").live('click', function(event) {
        var sku = $(this).find('td:eq(0)').html().trim();
        var scannedItems = $(this).find('td:eq(3)').html().trim();
        var invItems = $(this).find('td:eq(2)').html().trim();
        if (scannedItems != 0){
            $(this).find('td:eq(3)').html(--scannedItems);
            if (invItems == scannedItems){
                $(this).css("background-color","#86B404"); 
                
            }
            else {
                $(this).css("background-color","#F5A9A9"); 
                //skus[sku] = 1;
                
            }
            skus[sku] = scannedItems;
        }                               
    });
    
    
    $("#invoiceform").submit(function(){
        var isValid = true;
        var flag = 0;
        var jsonVal =jsonify();
        if (isCancel){
            return isValid; //cancellation
        }
        
        
        if (!isMatched()){
            flag =1;
            $("#isMatched").val('0');
        }    
        
        if (isNothingScanned()){
           isValid = false; 
           $("#comments").after('<p class="validationMessage" id ="commentsvalidationMessage" style="color:#FF0040"> Nothing to pack</p>');
           $("#comments").focus();
           return isValid;
        }

        if (flag == 1 && !$.trim($("#comments").val())){

            if ($("#commentsLabel").children("."+ "required").length == 0){
                $("#commentsLabel").append('<em class = "required" style="color:#FF0040">*</em>&nbsp;&nbsp;&nbsp;&nbsp;');
                $("#comments").after('<p class="validationMessage" id ="commentsvalidationMessage" style="color:#FF0040"> Packed Items are not same as Invoiced. You Must tell us why</p>')
            }

            isValid = false;
            $("#comments").focus();
            return isValid;

        }
        
        if (isValid)  {
            $("#jsonItems").val(jsonVal);  
             $("#invoiceId").val(invoiceIdJS);  
             $("#orderId").val(orderId); 
        }
//        else {
//            preventDefault();
//        }

        return isValid;
//return false;
    });
    
    
    
    function isMatched(){
        var matched = true;
        var $rows = $("#skutable tr");
        for (i=0 ; i<$rows.length; i++){
            var $row = $rows.eq(i);
            var $sku =$row.find('td:eq(0)');
            if ($sku.length > 0){               
                var itemInv = $row.find('td:eq(2)').html().trim();
                var itemScanned = $row.find('td:eq(3)').html().trim();
                if (itemInv!=itemScanned){
                    matched = false;
                }
            }       
        }
        return matched;
    };
    
    function jsonify(){
        var json = "[";
        var $rows = $("#skutable tr");
        for (i=0 ; i<$rows.length; i++){
            
            var $row = $rows.eq(i);
            var $sku =$row.find('td:eq(0)');
            if ($sku.length > 0){ 
                var scanned =$row.find('td:eq(3)').html().trim();
                var sku = $sku.html().trim();
                json = json + "{" + "\"sku\":\"" + sku +"\"," + "\"items\":\"" + scanned + "\"}";
                if (i!=$rows.length - 1){
                    json  = json + ",";
                }
            }           
        }
        json = json + "]";
        return json;
        
    };
    
    
    function formaction( str )
    {
        isCancel = false;
        switch( str )
        {   
            case "addMore":
            document.invoiceform.action = 'index.php/invoice/addInvoice';
            //document.invoiceform.submit();
            break;

            case "shipment":
            document.invoiceform.action = 'index.php/invoice/ship';
            //document.invoiceform.submit();
            break;

            case "cancel":
            document.invoiceform.action = 'index.php/invoice/addInvoice';
            $("#invoiceId").val("");
            $("#isMatched").val("");
            $("#comments").val("");
            $("#jsonItems").val("");
            $("#orderId").val("");
            isCancel = true;
            //document.invoiceform.submit();
            break;
        }
} ;

function isNothingScanned(){
        var allEmpty = true;
        var $rows = $("#skutable tr");
        for (i=0 ; i<$rows.length; i++){
            var $row = $rows.eq(i);
            var $sku =$row.find('td:eq(0)');


            if ($sku.length > 0){               
                var itemScanned = $row.find('td:eq(3)').html().trim();
                if ( itemScanned > 0 ){
                    allEmpty = false;
                }
                 
            }       
        }
        return allEmpty;
    };
</script>
        
       