<html>
    <head>
       <?php $this->load->view("common/header"); ?>
<!--        <script type="text/javascript" src="http://jzaefferer.github.com/jquery-validation/jquery.validate.js"></script>-->
        <style>
            .column {
            float: left;
            padding: 1em;
            width:30%;
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
            
            .table-grid {
                padding-top: 2em;
            }
            
         
            .help-message {
                color: green;
                float: right;
                font-size: 90%;
                font-style: italic;
                width: 65%;
            }
        </style>
       
        <script type="text/javascript">
                $(function() {
         $.validator.addMethod('tallyamount', function (value, el) {
            var already_paid = parseFloat($("#amount_paid_form").text());
            var pay_amount = parseFloat($("#amount").val());
            var total_invoiced_value = parseFloat($("#total_value_form").text());
            var prev_amount = $("#prev_amount").val();
            var total_paid= already_paid + pay_amount-prev_amount;
            if (total_paid>total_invoiced_value){
                return false;
            }
            
            else {
                return true;
            }
                },"Total Payment Exceeds Invoiced ");
                
        $.validator.addMethod('tallyadvance', function (value, el) {
            var advance_value = parseFloat($("#advance_value").text());
            var pay_amount = parseFloat($("#amount").val());
            var adjusted_advance = parseFloat($("#adjusted_advance").text());
            var prev_amount = $("#prev_amount").val();
            var total_paid= adjusted_advance + pay_amount - prev_amount;
            if (total_paid>advance_value){
                return false;
            }
            
            else {
                return true;
            }
                },"Total Adjusted Payment Exceeds Advance Payment ");
                
         $("#paymentForm").validate({rules:{
                 
                 amount:{                  
                     required:true,
                     number:true,
                     minStrict:0,
                     tallyamount:true,
                     tallyadvance:true
                 }
                 
         }}
     );
        // Main Request For Quotation Grid    
        function invFormatter ( cellvalue, options, rowObject )
        {
        // format the cellvalue to new format
        return 'PINV-' + cellvalue;
        }
        function poFormatter ( cellvalue, options, rowObject )
        {
        // format the cellvalue to new format
        return 'PO-' + cellvalue;
        }
        
        var myGrid = $("#orders");
                
                
                myGrid.jqGrid({
                    url:'index.php/procurement/populateInvoicesToPay',
                    datatype: 'json',
                    mtype: 'GET',
                    colNames:['Invoice Reference','Payment Process','Total Value','Amount Paid','Status','Payee Id','Payee','Owner Id','Owner Name','Order Id','Order Ref'],
                    colModel :[ 
                        {name:'reference', index:'reference', width:120, align:'right',editable:false,formatter:invFormatter},
                        {name:'payment_process_type', index:'payment_process_type', width:120, align:'right'},
                        {name:'total_value', index:'total_value',editable:false,align:'right',width:100},
                        {name:'amount_paid', index:'amount_paid',editable:false,align:'right',width:120},
                        {name:'status', index:'status',editable:false,align:'right'},
                        {name:'payee', index:'payee',hidden:true},
                        {name:'payee_name', index:'payee_name', width:70, align:'right',editable:false},
                        {name:'owner_id', index:'owner_id',hidden:true},
                        {name:'owner_name', index:'owner_name', width:70, align:'right',editable:false},
                        {name:'order_id', index:'order_id',hidden:true},
                        {name:'order_reference', index:'order_reference',editable:false, width:100, align:'right',formatter:poFormatter}
                        
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
                    onSelectRow: function (){
                        $( "#dialog-form" ).dialog("open");
                    },
            
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
                                    url:'index.php/procurement/populateInvoiceItems?invoiceId='+row_id,
                                    datatype: 'json',
                                    colNames:['Product Id','Product','Invoiced Quantity','Invoiced Value'/*,'Owner','Status','Order Id','Order Ref','Quote Ref','Owner','Needed By Date'*/],
                                        colModel :[ 
//                                            {name:'reference', index:'reference', width:80, align:'right',editable:false},
                                            {name:'product_id', index:'product_id',editable:false,align:'right',hidden:true},
                                            {name:'name', index:'name', width:140, align:'right',editable:false},
                    //                        {name:'estimated_value', index:'estimated_value', width:100, align:'right',editable:false,editoptions:{size:"20",maxlength:"30"}},
                    //                        {name:'owner', index:'owner', width:140, align:'right',editable:true,editoptions:{size:"20",maxlength:"30"}},
                                            {name:'invoiced_quantiy', index:'invoiced_quantiy', width:100, align:'right',editable:false,editoptions:{size:"20",maxlength:"30"}},
                                            {name:'total_invoiced_value', index:'total_invoiced_value',editable:false,align:'right'}
                                            
                                        ],
                                    rowNum:20,
                                    pager: pager_id,
                                    sortname: 'id',
                                    sortorder: "asc",
                                    height: '100%',
                                   
                                    
                                    jsonReader : {
                                        root:"invoiceitemdata",
                                        page: "page",
                                        total: "total",
                                        records: "records",
                                        cell: "dprow",
                                        id: "id"
                                    }
                                });
                            jQuery("#"+subgrid_table_id).jqGrid('navGrid',"#"+pager_id,{edit:false,add:false,del:false,search:false,view:true});
                             
                    }
                }).navGrid("#pager",{edit:false,add:false,view:false,del:false,search:false},{height:280,reloadAfterSubmit:false,closeAfterEdit:true,recreateForm:true,checkOnSubmit:true},{},{},{},{});
               
               
                myGrid.jqGrid('filterToolbar', {stringResult: true, searchOnEnter: true, defaultSearch : "cn"});
                
                //$("#del_orders").insertAfter("#add_orders");
            
        $( "#dialog-form" ).dialog({
            autoOpen: false,
            height: 'auto',
            width: '55%',
            position:[300,25],
            modal: true,
            buttons: {
                
                Cancel: function() {

                    $( this ).dialog( "close" );
                }
            },
            open: function(event,ui){
                var invoice_id = myGrid.getGridParam('selrow');
                var settingsObj ={grid_id:'payments',pager:'pagerPayments',multiselect:true};
                var postData={invoiceId: myGrid.getGridParam('selrow')};
                preparePaymentsGrid(settingsObj,postData);
                $("#payments").navGrid("#pagerPayments",{edit:false,add:false,del:false,search:false,view:true},{},{},{},{},{});
                var btns = {add:true,edit:true,adjust:true,data:{type:'general',invoice_id:invoice_id,order_id:myGrid.getCell(invoice_id,'order_id')}};
                addCustomButtonsInPaymentsGrid('payments','pagerPayments',null,btns);
                $("#payments").jqGrid('filterToolbar', {stringResult: true, searchOnEnter: true, defaultSearch : "cn"});
                console.log(invoice_id);
                $("#invoice").text(myGrid.getCell(invoice_id,'reference'));
                $("#total_value").text(myGrid.getCell(invoice_id,'total_value'));
                $("#order_ref").text(myGrid.getCell(invoice_id,'order_reference'));
                $("#amount_paid").text(myGrid.getCell(invoice_id,'amount_paid'));
                

            },
            close: function(event,ui) {
                
                $("#estValue").text("");
                $("#curPrice").text("");
                $("#status-message-li").empty();
                
                $("#payments").jqGrid("GridUnload");
                myGrid.trigger("reloadGrid");
            }
        });    
        
        
    });     
    
     $(window).load(function(){
       
        var warningDialogs={one:true,none:true,status:true};
        initDialogs(warningDialogs);
        initPaymentDialog();
        initErrorDialogs({total:true,advance:true});
        $("#advance_ref").change(function(){
            $.ajax({
                url:"index.php/procurement/getPaymentDetails",
                type:"POST",
                data:{pay_id:$(this).val()                          
                },
                success:function (response){
                    var resObj = JSON.parse(response);
                     $(".paymentCntnr,#advancePaymentAmountCntnr").show();
                    $("#oper_payment").val("assign");
                    $("#payment_id").val(resObj.id);
                    $("#payment_ref").val(resObj.payment_reference);
                    $("#payment_mode").val(resObj.payment_mode);
                    $("#bankcomments").val(resObj.comments);
                    //$("#amount").val($("#payments").getCell(pay_id,'amount'));
                    $("#payment_ref").attr('readonly','readonly');
                    $("#payment_mode").attr('readonly','readonly');
                    $("#advance_value").text(resObj.amount);
                    $("#adjusted_advance").text(resObj.invoiced_amount);

                },
                error:function (response){

                }
            });
        });
    });

        </script>
        
    </head>
     
    <body>
        <?php  $this->load->view("common/menubar"); ?>
       <?php  $this->load->view("common/dialogs"); ?>
        
        <div style="display: block;height: 100%;" class="shopifine-ui-dialog ui-widget ui-widget-content ui-corner-all" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-dialog-form">
            <?php  $this->load->view("common/message"); ?>
            <div class="table-grid">
                <h1 id="table header">Invoices To Pay</h1>
                <table id="orders"><tr><td/></tr></table> 
                <div id="pager"></div>
            </div>
            
        </div>
        <div id ="dialog-form">
           
             
            <h1 id="formHeader">Add Payments For Invoice #: <span id="invoice" name ="invoice" ></span>  </h1>   
           
                    <div id ="status-message-li" class="ui-corner-all">
                
                     </div>
                    <div class="row ">
                        <div class="column ">
                           <div class="field">
                                <div class="labeldiv">Total Value:</div>  
                                <div class="valuediv" id="total_value" name ="total_value" ></div>
                            </div>
                            
                        </div>
                        <div class="column ">
                            <div class="field">
                                <div class="labeldiv">Amount Paid :</div>  
                                <div class="valuediv" name="amount_paid" id="amount_paid" ></div>
                            </div>
                        </div> 
                        <div class="column ">
                            <div class="field">
                                <div class="labeldiv">Order Reference:</div>  
                                <div class="valuediv" name="order_ref" id="order_ref" ></div>
                            </div>
                        </div> 
                    </div>
                    
                    <div id ="status-message-li" class="ui-corner-all">
                
                    </div>
                    <div class="table-grid">
                        <h1 id="table_header_payment">Payment Details</h1>
                        <table id="payments"><tr><td/></tr></table> 
                        <div id="pagerPayments"></div>
                    </div>
                    
             
        </div>
<!--        <div id ="dialog-form-item">
            
            <h1 id="formHeader">Payment Details</h1>   
            <form id="paymentForm">
                <fieldset>
                    
                    
                    <div class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <div class="labeldiv">Total Value:</div>  
                                <div class="valuediv" id="total_value_form" name="total_value_form" ></div>                               
                            </div>
                           
                        </div>                        
                    </div>
                    <div class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <div class="labeldiv">Amount Paid:</div>  
                                <div class="valuediv" id="amount_paid_form" name="amount_paid_form" ></div>                               
                            </div>
                           
                        </div>                        
                    </div>
                    

                    <div class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="payment_mode" class="labeldiv">Payment Type:</label>  
                                <select id="payment_mode" name ="payment_mode" class="required">
                                    <option value="">Choose..</option>
                                    <option value="cash">Cash</option>
                                    <option value="cheque">Cheque</option>
                                    <option value="draft">Draft</option>
                                    <option value="online">Online</option>
                                </select>
                                
                            </div>
                        </div>                        
                    </div>
                    <div class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="payment_ref" class="labeldiv">Payment Reference:</label>  
                                <input id="payment_ref" name="payment_ref" class="required"/>
                                <div id ="receipt-help" class="ui-corner-all help-message">
                                    (Provide Cheque Number/Draft Number/Online Transaction Id)
                                </div>
                            </div>
                        </div>                        
                    </div>
                    
                    
                    <div class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="amount" class="labeldiv">Amount:</label>  
                                <input id="amount" name="amount" />
                                
                            </div>
                        </div>                        
                    </div>
                     <div class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="comments" class="labeldiv">Bank Details:</label>  
                                <textarea id="bankcomments" name="bankcomments" row="5" col="40"></textarea>
                                <div id ="receipt-help" class="ui-corner-all help-message">
                                    (Provide Account # , Branch Etc:)
                                </div>
                            </div>
                        </div>                        
                    </div>
                    <input id="oper" name="oper" type="hidden" value=""/>
                    <input id="type" name="type" type="hidden" value=""/>
                    <input id="payment_id" name="payment_id" type="hidden" value=""/>
                    
                </fieldset>
            </form>
        </div>-->
        <?php $this->load->view("partial/footer"); ?>  
        
</body>   
</html>



    
   