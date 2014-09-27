<html>
    <head>
       <?php $this->load->view("common/header"); ?>
<!--        <script type="text/javascript" src="http://jzaefferer.github.com/jquery-validation/jquery.validate.js"></script>-->
        <link rel="stylesheet" rev="stylesheet" href="<?php echo base_url();?>css/shopifine.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>css/ui.jqgrid.css" />
        <script src="<?php echo base_url(); ?>js/i18n/grid.locale-en.js" type="text/javascript"></script>
        <script src="<?php echo base_url(); ?>js/jquery.jqGrid.min.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
        <style>
            .ui-button{ margin:.5em;}
        </style>
         <script>
             var isCancel = false;
            $(document).ready(function(){
                
                
                
                $("#invoiceForm").validate();


               //invoice grid
               
                var myGrid = $("#invoices"),lastsel2;
                
                var editparameters = {
                    "keys" : true,
                    "oneditfunc" : null,
                    "successfunc" : null,
                    "aftersavefunc" : function(rowid,response){
                        if (myGrid.getCell(rowid,'packed_number')>0){
                            $("#isScanned").val('1');
                        }
                        $("#isAllSaved").val('1');
                        lastsel2=undefined;
                    },
                    "errorfunc": null,
                    "afterrestorefunc" : null,
                    "restoreAfterError" : true,
                    "mtype" : "POST"
                    
                };
                myGrid.jqGrid({
                    url:'index.php/invoice/populateInvoiceItems',
                    datatype: 'json',
                    mtype: 'POST',
                    colNames:['SKU','Name','Invoiced','Packed'],
                    colModel :[ 
                        {name:'sku', index:'sku', width:80, align:'right',search:false,editable:false},
                        {name:'name', index:'status', width:140, align:'right',search:false,editable:false},
                        {name:'invoiced_number',index:'invoiced_number', width:80, align:'right',search:false,editable:false},
                        {name:'packed_number', index:'packed_number', width:80, align:'right',search:false,editable:true},
                        
                    ],
                    pager: '#pager',
                    rowNum:10,
                    rowList:[5,10,20],
                    sortname: 'id',
                    sortorder: 'desc',
                    viewrecords: true,
                    gridview: true,
                    ignoreCase:true,
                    rownumbers:true,
                    height:'auto',
                    width:680,
                    caption: 'Invoices',
                    postData:{invoiceId: '<?php echo $invoice_id ?>'},
                    editurl:'clientArray',
                    jsonReader : {
                        root:"invoicedata",
                        page: "page",
                        total: "total",
                        records: "records",
                        cell: "dprow",
                        id: "id"
                    },
                    onSelectRow: function(id){if(id && id!==lastsel2){
                            myGrid.restoreRow(lastsel2);
                            $("#isAllSaved").val('0');
                            myGrid.editRow(id,editparameters);
                            lastsel2=id;
                        }
                    }
                    
                }).navGrid("#pager",{edit:false,add:false,del:false,search:false},{height:280,reloadAfterSubmit:false,closeAfterEdit:true,recreateForm:true,checkOnSubmit:true},{},{},{},{});
               
               
                
               
                
    $("#invoiceForm").submit(function(){
        if (!isCancel){
            if ($("#isScanned").val() == "0"){
                var errorStatus = " Please scan one item!!";
                showErrorMessage(errorStatus);
                return false;
            }
//            if ($("#isAllSaved").val() == "0"){
//                var errorStatus = " Please save after edit!!";
//                showErrorMessage(errorStatus)
//                return false;
//            }
            $("#packedJson").val(JSON.stringify(myGrid.getCol('packed_number',true)));
            var details = myGrid.getCol('packed_number',true);
            
            $.each(details, function(intIndex,objValue){

                    var val = parseInt(myGrid.getCell(objValue.id,'packed_number'));
                    var invoicedVal = parseInt(myGrid.getCell(objValue.id,'invoiced_number'));
                    
                    if (val != invoicedVal){
                        $("#isMatched").val('0');
                        //return false;
                    }
            });
            var matched=$("#isMatched").val();
            if (matched==0){
                var errorStatus = " Invoiced and Packed items must match";
                showErrorMessage(errorStatus)
                return false;
            }
            
        }
       
    });
    

                    
            })
        </script>
    </head>
     
    <body>
         <?php $this->load->view("common/menubar"); ?>
       
        <div style="display: block;height: auto;" class="shopifine-ui-dialog ui-widget ui-widget-content ui-corner-all" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-dialog-form">
            <div class="form-container">
                <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
                    <span class="ui-dialog-title" id="ui-dialog-title-dialog-form">&nbsp;</span>
                    <a href="#" class="ui-dialog-titlebar-close ui-corner-all" role="button">

                    </a>
                </div>
                <div  class="ui-dialog-content" style="width: auto; min-height: 65.1333px; height: auto;" scrolltop="0" scrollleft="0">
                   
                    
                    <div class="row single-column-row">
                        <div class="column single-column">
                        <div class="field">
                                <label for="scannedSKU">Scan Item :</label>  
                                <input id="scannedSKU" name ="scannedSKU" type="text" class="required"/>  
                            </div> 
                        </div>

                    </div>
                </div>
                
            </div>
            <?php $this->load->view("common/message"); ?>
            <div class="table-grid">
                <h1 id="table header">Items to be packed</h1>
                <table id="invoices"><tr><td/></tr></table> 
                <div id="pager"></div>
            </div>
            <div>
                <form method="post" id="invoiceForm" name="invoiceForm">
                    <b id="commentsLabel" style="vertical-align:top;">Comments   </b><textarea id="comments" name ="comments" rows="4" cols="40" class="required" ></textarea><br/>
                    <input type="hidden" id ="invoiceId" name="invoiceId" value='<?php echo $invoice_id ?>' />
                    <input type="hidden" id ="isMatched" name="isMatched" value="1"/>
                    <input type="hidden" id ="isScanned" name="isScanned" value="0"/>
                    <input type="hidden" id ="isAllSaved" name="isAllSaved" value="0"/>
                    
                    <input type="hidden" id="packedJson" name="packedJson"/>
                    <div class="ui-dialog-buttonpane shopifine-ui-widget-content ui-helper-clearfix">
                        <div class="shopifine-ui-dialog-buttonset">

                            <input type="submit" class="shopifine-ui-button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" value="Done" onclick="formaction('addMore')"/>
                            <input type="submit" class="shopifine-ui-button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" value="Cancel " onclick="formaction('cancel')"/>
                        </div>
                       
                            
                   </div>
                </form>
                
            </div>
        </div>
        <?php $this->load->view("partial/footer"); ?>
        <div id="dialog-conf" title="Confirm">
            
        </div>
    </body>
</html>
<script>
$("#scannedSKU").keypress(function(event) {
            var itemGrid = $("#invoices");
            if ( event.which == 13 ) {
                event.preventDefault();
                
                var sku= $("#scannedSKU").val();
                var skus = itemGrid.getCol('sku',false);
                
                
                if (sku) {
                    if (!ifExists(skus, sku)){
                        alert ("The item your are trying to scan is not present in the Invoice. Please check")
                        }
                        else {
                            var details = itemGrid.getCol('sku',true);
                            
                            $.each(details, function(intIndex,objValue){
                                if(sku==objValue.value){
                                    var val = parseInt(itemGrid.getCell(objValue.id,'packed_number'));
                                    var invoicedVal = parseInt(itemGrid.getCell(objValue.id,'invoiced_number'));
                                    $("#isScanned").val("1");
                                    if (val == invoicedVal){
                                        var errorStatus = " Required quantities are already packed!!";
                                        showErrorMessage(errorStatus);
                                    }
                                    else{
                                       itemGrid.setCell(objValue.id,'packed_number',val+1); 
                                    }
                                        
                                }
                                
                            });

                        }
                    }
                    
                    $("#scannedSKU").val("");    
            }
        }); 
        //inArray is not working
        function ifExists(arr,val){
            if (arr==null){
                return false;
            }
            if (arr.length==0){
                return false;
            }

            for (i=0 ;i<arr.length;i++){
                if (arr[i]==val){
                    return true;
                }
            }
            return false;
         
        }
        
       function formaction( str )
        {
            isCancel = false;
            switch( str )
            {   
                case "addMore":
                    document.invoiceForm.action = 'index.php/invoice/completePacking';
                break;

                case "cancel":
                    document.invoiceForm.action = 'index.php/invoice';

                    $("#isMatched").val("");
                    $("#comments").removeClass("required");
                    $("#packedJson").val("");

                    isCancel = true;
                
                break;
            }
    }; 

</script>



 