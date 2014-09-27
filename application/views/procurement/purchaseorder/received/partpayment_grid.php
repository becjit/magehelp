<html>
    <head>
       <?php $this->load->view("common/header"); ?>
<!--        <script type="text/javascript" src="http://jzaefferer.github.com/jquery-validation/jquery.validate.js"></script>-->
        <style>
            .column {
            float: left;
            padding: 1em;
            width:45%;
            }
            .base-column{
                width:100%;
            }
            #content_area{
                width:1100px;
            }
            .extra-wide{
                width:95%;
            }
            .field{
                width:100%;
            }
            .ui-widget-header {height:12px;}
            .quote-column {
            float: left;
            padding-bottom: 0.5em;
            width: 95%;
            }
            .ui-combobox {
                width:14em;
            }
            .ui-combobox-input{
                width:12em;
            }
             #supplierOp-input{
                width:10em;
            }
            #warehouseOp-input{
                width:10em;
            }
             
            .calculated {
                color: green;
                font-size: 90%;
            }
            .row{
                width:95%;
            }
            
            .shopifine-ro-label {
                float: left;
                padding-right: 0.5em;
                width: 50%;
                word-wrap: break-word;
                color:#2E6E9E;
            }

            .shopifine-output {
             float: right;
             width: 45%;
             word-wrap: break-word;
             font-weight:bold;
            }
            
            .ui-tabs {
                height: 80%;
                margin: 0 auto;
                width: 70%;
                left:0;
            }
            #notetab {
                height:30em;
            }
            #details {
                height:12em;
            }
            .ui-tabs-nav{
                height:22px;
            }
            .labeldiv {
                color: #2E6E9E;
                float: left;
                font-size: 110%;
                font-weight: bold;
                margin-right: .5em;
                width: 35%;
                word-wrap: break-word;
            }
            .valuediv {
                float: left;
                font-weight: bold;
                width: 45%;
                word-wrap: break-word;
            }
            label.error {
                margin-right: .5em;
            }
            #status-message-li{
                color: red;
                font-size: 110%;
                font-style: italic;
                margin: 0 auto;
                width: 80%;
            }
            
       
        </style>
       
        <script type="text/javascript">
                $(function() {
       $.validator.addMethod('lessthanallowed', function (value, element, param) {
                    var allowed_id = element.id + '_total';
                    var received = parseInt($("#already_received_rli").text());
                    var pp_qty = parseInt($("#"+allowed_id).text());
                    var pp_old = parseInt($("#pp_old_qty").val());
                  
                    if (parseInt(value-pp_old)>received-pp_qty){
                        return false;
                    }
                    return true;
                },"{0} Quantity Can Not Be More Than Total Received For This Receipt Line"); 
       
       $("#ppItemForm").validate({
             rules:{
                 pp_quantity_received:
                        {required:true,
                        digits:true,
                        lessthanallowed:"Part Payable Received"},
                    pp_calculated_value:
                        {
                            required:true,
                        number:true,
                        minStrict:0
                        }
             }
         });
        // Main Request For Quotation Grid                    
        
        var myGrid = $("#receiptitems");
        
        var settingObj = {grid_id:'receiptitems',pager:'pager',multiselect:true};
        var hiddenObj = {line_reference:true,returned_quantity:true,returned_value:true,receiving_notes:true,returned_notes:true,pp_status:false};
        var subgridSettingsObj = {multiselect:true,customButtons:{add:true,edit:true,process:true,approval:true,cancel:true},status:['open','waitingforapproval','processedforpayment','rejected'],filter:true};
        var postData={_pp_status:['open','none','waitingforapproval','rejected'],oper:'pp'};
        prepareReceiptItemsGrid(settingObj,postData,true,{},hiddenObj,subgridSettingsObj);
        
        myGrid.navGrid('#pager',{edit:false,add:false,view:true,del:false,search:false},{},{},{},{},{});
        addCustomButtonsInReceiptItemGrid('receiptitems', 'pager', null, {load_status_all:true,comments:true,load_owner_all:true}, null)
        
        $( "#dialog-form-partpayment" ).dialog({
            autoOpen: false,
            height: 'auto',
            width: '35%',
            position:[450,25],
            modal: true,
            buttons: {
                "Done": {
                id:"processItems",
                text:"Process For PartPayment",
                click:function() {
                    var isvalid = $("#ppItemForm").valid();
                    var receipt_line_id =  $(this).data('grid_data').receipt_line_id;
                    var receipt_line_grid = $(this).data('grid_data').receipt_line_grid;
                                              

                   if (isvalid){
                       $.ajax({
                           url:'index.php/procurement/processPartpayment',
                           type:"POST",
                           data:{
                               
                               order_line_id:$(this).data('grid_data').order_line_id,
                               receipt_line_id:receipt_line_id,
                               action:$(this).data('grid_data').action,
                               product_id:$("#"+receipt_line_grid).getCell(receipt_line_id,'product_id'),
                               pp_quantity:$("#pp_quantity_received").val(),
                               pp_value:$("#pp_calculated_value").val(),
                               pp_notes:$("#pp_notes").val(),
                               pp_id:$(this).data('grid_data').pp_id,
                               pp_old_qty:$(this).data('grid_data').old_qty ,
                               pp_old_val:$(this).data('grid_data').old_val
                           },
                           success:function (response){
                               //console.log("grid " + grid);
                               //$("#"+grid).trigger("reloadGrid");
                          }
                       })
                       $( this ).dialog( "close" );
                   }

                }},
                Cancel: function() {

                    $( this ).dialog( "close" );
                }
            },
            open: function(event,ui){
                
                var pp_grid = $(this).data('grid_data').pp_grid;
                var receipt_line_grid = $(this).data('grid_data').receipt_line_grid;
                var receipt_line_id =  $(this).data('grid_data').receipt_line_id;
                var action =  $(this).data('grid_data').action;
                var pp_id = $("#"+pp_grid).getGridParam('selrow');
                $(this).data('grid_data').pp_id = pp_id;
                
                $("#product_pp").text($("#"+receipt_line_grid).getCell(receipt_line_id,'name'));
                $("#sup_rec").text($("#"+receipt_line_grid).getCell(receipt_line_id,'supplier_receipt_number'));
                $("#receipt_line_pp").text($("#"+receipt_line_grid).getCell(receipt_line_id,'reference'))
                $("#pp_quantity_received_total").text(parseInt($("#"+receipt_line_grid).getCell(receipt_line_id,'pp_quantity')));
                $("#already_received_rli").text($("#"+receipt_line_grid).getCell(receipt_line_id,'received_quantity'));
                $("#already_pp_value_rli").text(parseInt($("#"+receipt_line_grid).getCell(receipt_line_id,'pp_value')));
                
                $("#already_received_value_rli").text($("#"+receipt_line_grid).getCell(receipt_line_id,'received_value'));
                $("#pp_old_qty").val("0");
                if (action=='edit'){
                    var old_qty = $("#"+pp_grid).getCell(pp_id,'pp_quantity');
                    var old_val = $("#"+pp_grid).getCell(pp_id,'pp_value');
                    $(this).data('grid_data').old_qty =old_qty;
                    $(this).data('grid_data').old_val =old_val;
                    $("#pp_quantity_received").val(old_qty);
                    $("#pp_calculated_value").val(old_val);
                    $("#pp_old_qty").val(old_qty);
                }
                

            },
            close: function(event,ui) {
                //$("#ppItemForm").data('validator').resetForm();
                var pp_grid = $(this).data('grid_data').pp_grid;
                var receipt_line_grid = $(this).data('grid_data').receipt_line_grid;
                console.log("receipt_line_grid " + receipt_line_grid + "pp_grid " + pp_grid);
                $('#ppItemForm')[0].reset();
               $("#pp_calculated_value").text("");
               $("#"+pp_grid).trigger("reloadGrid");
               $("#"+receipt_line_grid).trigger("reloadGrid");
                
            }
        });
            
        
        $( "#modal-warning" ).dialog({
            autoOpen:false,
            height: 80,
            modal: true
        });
        $( "#modal-warning-open-status" ).dialog({
            autoOpen:false,
            height: 80,
            modal: true
        });
        
         $("#pp_quantity_received").change(function(){
            var received_value = parseFloat($("#already_received_value_rli").text());
            var received_qty = parseFloat($("#already_received_rli").text());
            var unit_price = (received_value/received_qty).toFixed(2);
            
            $("#pp_calculated_value").val(unit_price*$(this).val());
            
        });
    
    });    
     $(window).load(function(){
       
        var warningDialogs={one:true,none:true,status:true,exactlyone:true,morethanone:true};
        initDialogs(warningDialogs);
        initPaymentDialog();
        initCommentsForQuote();
        initAssignmentCommon();
        //initErrorDialogs({total:true,advance:true});
        
        //this is from payment dialog.The Select Inout is in dialogs.php
        
    });


        </script>
        
    </head>
     
    <body>
        <?php  $this->load->view("common/menubar"); ?>
        <?php  $this->load->view("common/dialogs"); ?>
<!--        <div id="modal-warning" title="Warning">
            <p>Please Select Row(s)</p>
        </div>-->
<!--        <div id="modal-warning-open-status" title="Warning">
            <p id="status_warning_text"></p>
        </div>-->
         <div id ="dialog-form-partpayment">
            <div id ="status-message-li-pp" class="ui-corner-all">
                
            </div>
             
            <h1 id="formPPHeaderItem">Request For Part Payment For Supplier Receipt # <div id="sup_rec"></div> </h1>   
            <form id="ppItemForm">
                <fieldset style="border:1px solid #A6C9E2">
                    <legend style="color: #2E6E9E;font-size: 110%;font-weight: bold;margin-left: 15px;">
                        Receipt Line Related Info</legend>  
                    
                    <div class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <div class="labeldiv">Receipt Line Reference:</div>  
                                <div class="valuediv" name="receipt_line_pp" id="receipt_line_pp" ></div>
                                <div id ="receipt_line_pp_help_rli" class="ui-corner-all help-message-left">
                                    (This Is Receipt Line Reference And Not <b>Receipt</b> Reference.System Receipt Reference Can Be Found In Title)
                                </div>
                            </div>
                        </div>                        
                    </div>
                    <div class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <div class="labeldiv">Products:</div>  
                                <div class="valuediv" id="product_pp" name ="product_pp" ></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <div class="labeldiv">Part Paid Quantity:</div>  
                                <div class="valuediv" name="pp_quantity_received_total" id="pp_quantity_received_total" ></div>
                                 <div id ="rli-help" class="ui-corner-all help-message-left">
                                    (Part Paid Quantity This Receipt Line Item )
                                </div>
                            </div>
                        </div>                        
                    </div>
                    <div class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <div class="labeldiv">Received  Quantity:</div>  
                                <div class="valuediv" name="already_received_rli" id="already_received_rli" ></div>
                                <div id ="received_help_rli" class="ui-corner-all help-message-left">
                                    (For This Receipt Line Item Till Date)
                                </div>
                            </div>
                        </div>                        
                    </div>
                   
                    
                    <div class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <div class="labeldiv">Received Amount  Till Date:</div>  
                                <div class="valuediv" id="already_received_value_rli" name="already_received_value_rli" ></div>  
<!--                                <div id ="recval-help-rli" class="ui-corner-all help-message-left">
                                    (Payment Amount Subject To Appropriate Approval)
                                </div>-->
                            </div>
                           
                        </div>                        
                    </div>
                    <div class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <div class="labeldiv">Part Payment Amount :</div>  
                                <div class="valuediv" id="already_pp_value_rli" name="already_pp_value_rli"></div> 
                                <div id ="already_ppval-help-rli" class="ui-corner-all help-message-left">
                                    (Part Paid  Amount )
                                </div>
                            </div>
                           
                        </div>                        
                    </div>
                    </fieldset>
                    <fieldset>
                    
                    <div id="pp_received_quantity_cntnr" class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="pp_quantity_received" class="labeldiv">Part Payment Qty:</label>  
                                <input id="pp_quantity_received" name="pp_quantity_received" class="required"/>
                                
                            </div>
                        </div>                        
                    </div>
                    <div id="pp_calculated_value_cntnr" class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="pp_calculated_value" class="labeldiv">Payment Value:</label>  
                                <input  id="pp_calculated_value" name="pp_calculated_value" readonly="readonly"></input> 
                                <div id ="pp_calculated_value_help_rli" class="ui-corner-all help-message-left">
                                    (Calculated Value. Read-only)
                                </div>
                            </div>
                        </div>                        
                    </div>

                    <div id="pp_notes_cntnr" class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="pp_notes" class="labeldiv">Part Payment Notes:</label>  
                                <textarea id="pp_notes" name="pp_notes" row="5" col="50"></textarea>
                            </div>
                        </div>                        
                    </div>

                    <input id ="receipt_line_ref_pp" name ="receipt_line_ref_pp" type="hidden" value="">
                     <input id ="pp_old_qty" name ="receipt_line_ref_pp" type="hidden" value="">
                </fieldset>
            </form>
        </div>
        
        <div style="display: block;height: 100%;" class="shopifine-ui-dialog  ui-widget ui-widget-content ui-corner-all" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-dialog-form">
            <?php  $this->load->view("common/message"); ?>
            <div class="table-grid">
                <h1 id="table header">Generate Part Payment</h1>
                <table id="receiptitems"><tr><td/></tr></table> 
                <div id="pager"></div>
            </div>
            
        </div>
        
       
        <?php $this->load->view("partial/footer"); ?>  
        
</body>   
</html>



    
   