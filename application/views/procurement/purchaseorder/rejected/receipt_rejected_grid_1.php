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
             .help-message-left{
                color: green;
                font-size: 90%;
                font-style: italic;
                margin: 0 auto;
                width: 90%;
                float:left
            }
       
        </style>
       
        <script type="text/javascript">
                $(function() {
        
        $.validator.addMethod('tallyquantity', function(value, element) {
              var total_quantity = parseInt($("#total_quantity").text());
              
              var received_quantity = parseInt($("#received_quantity").val());
              var returned_quantity = parseInt($("#returned_quantity").val());
              
              var new_total = received_quantity + returned_quantity;
              
              console.log("old total" + total_quantity);
              console.log("new total" + new_total);
              if (total_quantity!=new_total){
                  
                      return false;
                  
              }
              return true;
          }, ' Received And Returned Must Match Total For This Line Item'); 
          $.validator.addMethod('nonzeroval', function(value, element,param) {
              
              var crrspndng_qty = $("#"+element.id.split("_")[0]+"_quantity").val();
              console.log("crrspndng_qty" + crrspndng_qty);
              if (crrspndng_qty>0 && value <= 0){
                  return false;
              }
              return true
          }, '{0} Value Must Be Non-Zero For Non-Zero {0} Quantity'); 
        $("#notesForm").validate({
            rules:{
                received_quantity:{
                     //required:true,
                     //noBlank:true,
                     digits:true
                     
                 },
                 received_value:{
                     nonzeroval:"Received",
                     //required:true,
                     //noBlank:true,
                     number:true
                     
                 },
                 returned_quantity:{
                     //required:true,
                     //noBlank:true,
                     digits:true
                     
                 },
                 returned_value:{
                     nonzeroval:"Returned",
                     //required:true,
                     //noBlank:true,
                     number:true
                     
                 }
            }
        });
        // Main Request For Quotation Grid                    
        
        var myGrid = $("#orders");
                
                
                myGrid.jqGrid({
                    url:"index.php/procurement/populateReceipts?_status=rejected",
                    datatype: 'json',
                    mtype: 'GET',
                    colNames:['Receipt Reference','Supplier Receipt','Supplier',/*'Estimated Value','Owner',*/'Status','Order Id','Order Ref','Quote Ref'/*,'Owner','Needed By Date'*/],
                    colModel :[ 
                        {name:'reference', index:'reference', width:100, align:'right',editable:false},
                        {name:'supplier_receipt_number',width:140, index:'supplier_receipt_number',editable:false,align:'right'},
                        {name:'supplier_name', index:'supplier_name', width:140, align:'right',editable:false},
                        {name:'status', index:'status', width:100, align:'right',editable:false,editoptions:{size:"20",maxlength:"30"}},
                        {name:'order_id', index:'order_id',hidden:true},
                        {name:'order_reference', index:'order_reference',editable:false, width:100, align:'right'},
                        {name:'quote_reference', index:'quote_reference',editable:false, width:100, align:'right'},
                    ],
                    pager: '#pager',
                    rowNum:10,
                    rowList:[5,10,20],
                    sortname: 'id',
                    sortorder: 'desc',
                    viewrecords: true,
                    gridview: true,
                    multiselect:true,
                    
                    ignoreCase:true,
                    rownumbers:true,
                    height:'auto',
                    width:'90%',
                    caption: 'Receipts',
            
                    jsonReader : {
                        root:"quotedata",
                        page: "page",
                        total: "total",
                        records: "records",
                        cell: "dprow",
                        id: "id"
                    },
                    
                    subGrid:true,
                    subGridRowExpanded: function(subgrid_id, row_id) {
                            // we pass two parameters
                            // subgrid_id is a id of the div tag created whitin a table data
                            // the id of this elemenet is a combination of the "sg_" + id of the row
                            // the row_id is the id of the row
                            // If we wan to pass additinal parameters to the url we can use
                            // a method getRowData(row_id) - which returns associative array in type name-value
                            // here we can easy construct the flowing
                            var subgrid_table_id, pager_id;
                            subgrid_table_id = subgrid_id+"_t";
                            
                            pager_id = "p_"+subgrid_table_id;
                            
                            $("#"+subgrid_id).html("<table id='"+subgrid_table_id+"' class='scroll'></table><div id='"+pager_id+"' class='scroll'></div>");
                            jQuery("#"+subgrid_table_id).jqGrid({
                                    url:'index.php/procurement/populateReceiptItems?oper='+"receipt"+'&receiptId='+row_id,
                                    datatype: 'json',
                                    colNames:['Line Reference','Supplier Receipt','Reference','Order Line','Receipt Id','Order Line Id','Product Id','Product','Qty Ordrd','Qty Rcvd','Rcvd Value','Qty Rtrnd','Rtrnd Value','Rcvd Notes','Rtrnd Notes'],
                                    colModel :[ 
                                        {name:'line_reference', index:'line_reference',editable:false,width:60, align:'right',hidden:true},
                                        {name:'supplier_receipt_number', index:'supplier_receipt_number',editable:false,width:120, align:'right'},
                                        {name:'reference', index:'reference',editable:false,width:60, align:'right'},
                                        {name:'receipt_id', index:'receipt_id',editable:false, hidden:true},
                                        {name:'order_line_id', index:'order_line_id',editable:false, hidden:true},
                                        {name:'order_line_reference', index:'order_line_id',editable:false, width:60},
                                        {name:'product_id', index:'product_id',editable:false, hidden:true},

                                        {name:'name', index:'name',editable:false, width:100, align:'right'},
                                        {name:'ordered_quantity', index:'ordered_quantity', editable:false,width:60, align:'right'},
                                        {name:'received_quantity', index:'received_quantity', editable:false,width:60, align:'right'},
                                        {name:'received_value', index:'received_value',editable:false, width:80, align:'right'},
                                        {name:'returned_quantity', index:'returned_quantity', editable:false,width:60, align:'right'},
                                        {name:'returned_value', index:'returned_value',editable:false, width:80, align:'right'},
                                        {name:'receiving_notes', index:'receiving_notes',editable:false, width:160, align:'right',hidden:true},
                                        {name:'returned_notes', index:'returned_notes', editable:false,width:160, align:'right',hidden:true}

//                                        {name:'comments', index:'comments',editable:false, width:160, align:'right'}

                                    ],
                                    rowNum:20,
                                    pager: pager_id,
                                    sortname: 'id',
                                    sortorder: "asc",
                                    height: '100%',
                                    //multiselect:true,
                                    
                                    jsonReader : {
                                        root:"receiptitemdata",
                                        page: "page",
                                        total: "total",
                                        records: "records",
                                        cell: "dprow",
                                        id: "id"
                                    }
                                });
                            jQuery("#"+subgrid_table_id).jqGrid('navGrid',"#"+pager_id,{edit:false,add:false,del:false,search:false,view:true});
                            $("#"+subgrid_table_id).jqGrid('navButtonAdd',"#"+pager_id,{
                                caption:"", 
                                title:"Resubmit For Approval",
                                buttonicon:"ui-icon-flag",
                                id:"approve_"+subgrid_table_id,
                                onClickButton : function () {
                                    var selectRows = $("#"+subgrid_table_id).getGridParam('selrow');
                                    var noOfRows = selectRows.length;

                                    if (noOfRows ==0){
                                        $( "#modal-warning" ).dialog("open");
                                     }
                                     else {
                                        var receiptLineId = $("#"+subgrid_table_id).getGridParam('selrow');
                                        var grid_data = {'grid_id':subgrid_table_id,'url':"index.php/procurement/resubmitReceiptLineItem",'receiptLineId':receiptLineId,'receiptId':$("#"+subgrid_table_id).getCell(receiptLineId,'receipt_id')} 
                                        $( "#dialog-receipt-items" ).data('grid_data',grid_data).dialog("open") ;
                                     } 
                                 } 
                             });    
                             
                    }
                }).navGrid("#pager",{edit:false,add:false,view:false,del:false,search:false},{},{},{},{},{});
               
                myGrid.jqGrid('navButtonAdd','#pager',{
                   caption:"", 
                   title:"Resubmit For Approval",
                   buttonicon:"ui-icon-flag",
                   id:"invoice_orders",
                   onClickButton : function () {
                       var selectRows = myGrid.getGridParam('selarrrow');
                       var noOfRows = selectRows.length;
                      
                       if (noOfRows ==0){
                           $( "#modal-warning" ).dialog("open");
                        }
                        else {
                            $.ajax({
                                 type:"POST",
                                 url:"index.php/procurement/resubmitReceipts",
                                 data:{receipt_ids:selectRows
                                        },
                                 success:function (jqXHR){
                                     var jsonRespons = JSON.parse(jqXHR)
                                     console.log(jsonRespons);
                                     emptyMessages();
                                     if (jsonRespons.success!=undefined){
                                         showSuccessMessage("Receipt(s) " + jsonRespons.success + " Has Been Suceesfuly Resubmitted For Re-Approval.");
                                         $("orders").trigger("reloadGrid");
                                     }

                                     if (jsonRespons.failed!=undefined){
                                         showSuccessMessage(" Receipt(s) " + jsonRespons.failed + " Could Not Be Resubmitted As These Still Has 'Rejected Line Items.");
                                     }


                                 }
                             })
                        }
                        
                               
                    } 
                });
                
                myGrid.jqGrid('filterToolbar', {stringResult: true, searchOnEnter: true, defaultSearch : "cn"});
                //$("#del_orders").insertAfter("#add_orders");
            
            
        
        $( "#modal-warning" ).dialog({
            autoOpen:false,
            height: 80,
            modal: true
        });
        $( "#modal-warning-order" ).dialog({
            autoOpen:false,
            height: 80,
            modal: true
        });
        $( "#dialog-receipt-items" ).dialog({
                                autoOpen: false,
                                height: 'auto',
                                width: '35%',
                                position:[450,25],
                                modal: true,
                                buttons: {
                                    "DoneButton": {
                                        id:"doneBtn",
                                        text:"Done",
                                        click:function() {
                                            var isvalid = $("#notesForm").valid();
                                            var url =  $(this).data('grid_data').url;
                                            var receiptLineId = $(this).data('grid_data').receiptLineId;
                                            var receiptId = $(this).data('grid_data').receiptId;
                                            if (isvalid){
                                                $.ajax({
                                                    url:url,
                                                    type:"POST",
                                                    data:{
                                                        receiptId:receiptId,
                                                        received_value:$("#received_value").val(),
                                                        received_quantity:$("#received_quantity").val(),
                                                        returned_quantity:$("#returned_quantity").val(),
                                                        returned_value:$("#returned_value").val(),
                                                        receiving_notes:$("#receiving_notes").val(),
                                                        returned_notes:$("#returned_notes").val(),
                                                        receiptLineId:receiptLineId 
                                                   },
                                                    success:function (response){
                                                        //console.log("grid " + grid);
                                                        emptyMessages();
                                                        showSuccessMessage("Selected Receipt Has Been Rejected  ")
                                                        $("#orders").trigger("reloadGrid");
                                                    },
                                                    error:function (response){
                                                        //console.log("grid " + grid);
                                                        emptyMessages();
                                                        showErrorMessage("Selected Receipt Could Not Be Processed For Internal Error  ")
                                                        
                                                    }
                                                })
                                                $( this ).dialog( "close" );
                                            }
                                            
                                        }
                                    },
                                    Cancel: function() {
                                        
                                        $( this ).dialog( "close" );
                                    }
                                },
                                
        open: function(){
                    var grid_id =  $(this).data('grid_data').grid_id;
                    var receiptLineId = $(this).data('grid_data').receiptLineId;
                    $("#total_quantity").text(parseFloat($("#"+grid_id).getCell(receiptLineId,'received_quantity'))+parseFloat($("#"+grid_id).getCell(receiptLineId,'returned_quantity')))
                    $("#received_value").val($("#"+grid_id).getCell(receiptLineId,'received_value'));
                    $("#received_quantity").val($("#"+grid_id).getCell(receiptLineId,'received_quantity'));
                    //$("#receiving_notes").val($("#"+subgrid).getCell(receipt_line_id,'receiving_notes'));
                    $("#returned_value").val($("#"+grid_id).getCell(receiptLineId,'returned_value'));
                    $("#returned_quantity").val($("#"+grid_id).getCell(receiptLineId,'returned_quantity'));
        },
        close: function() {
            $("#notesForm").data('validator').resetForm();
            $('#notesForm')[0].reset();
        }
        }); 
    
    });        

        </script>
        
    </head>
     
    <body>
        <?php  $this->load->view("common/menubar"); ?>
        <div id="modal-warning" title="Warning">
            <p>Please Select Row(s)</p>
        </div>
        <div id="modal-warning-order" title="Warning">
            <p>Please Select Receipt with The Same Order Reference</p>
        </div>
      
        
        <div style="display: block;height: 100%;" class="shopifine-ui-dialog  ui-widget ui-widget-content ui-corner-all" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-dialog-form">
            <?php  $this->load->view("common/message"); ?>
            <div class="table-grid">
                <h1 id="table header">Rejected Receipts</h1>
                <table id="orders"><tr><td/></tr></table> 
                <div id="pager"></div>
            </div>
            
        </div>
        <div id="dialog-receipt-items">
           
            <h3 id="formHeaderItem">Modify Line Item For Approval</h3>   
            <div id ="status-message-li" class="ui-corner-all"></div>
            <form id="notesForm">             
                <fieldset>
                    <div class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <div class="labeldiv">Total Quantity:</div>  
                                <div class="valuediv" name="total_quantity" id="total_quantity" ></div>
                                <div id ="return-help" class="ui-corner-all help-message-left">
                                    (Total Quantity For This Receipt Line. Received and Returned Must Add Up To This)
                                </div>
                            </div>
                        </div>                        
                    </div>
                    <div id="received_quantity_cntnr" class="row single-column-row" >
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="received_quantity" class="labeldiv">Received Quantity:</label>  
                                <input id="received_quantity" name="received_quantity" class="tallyquantity noBlank"/>
                                
                            </div>
                        </div>                        
                    </div>
                    <div id="received_value_cntnr" class="row single-column-row" >
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="received_value" class="labeldiv">Received Value:</label>  
                                <input id="received_value" name="received_value" class="noBlank"/>
                                
                            </div>
                        </div>                        
                    </div>
                    <div id="returned_quantity_cntnr" class="row single-column-row" >
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="returned_quantity" class="labeldiv">Returned Quantity:</label>  
                                <input id="returned_quantity" name="returned_quantity" class="tallyquantity noBlank"/>
                                
                            </div>
                        </div>                        
                    </div>
                    <div id="returned_value_cntnr" class="row single-column-row" >
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="returned_value" class="labeldiv">Returned Value:</label>  
                                <input id="returned_value" name="returned_value" class="noBlank"/>
                                
                            </div>
                        </div>                        
                    </div>
                    <div id="receiving_notes_cntnr" class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="receiving_notes" class="labeldiv">Receiving Notes:</label>  
                                <textarea id="receiving_notes" name="receiving_notes" row="5" col="50"></textarea>
                            </div>
                        </div>                        
                    </div>
                    <div id="returned_notes_cntnr" class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="returned_notes" class="labeldiv">Returned Notes:</label>  
                                <textarea id="returned_notes" name="returned_notes" row="5" col="50"></textarea>
                            </div>
                        </div>                        
                    </div>
                    <input id ="receipt_line_ref" name ="receiptreceipt_line_ref_line" type="hidden" value="">
                </fieldset>
            </form>
        </div>
        
       
        <?php $this->load->view("partial/footer"); ?>  
        
</body>   
</html>



    
   