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
            #content_area{
                width:1000px;
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
        
        // Main Request For Quotation Grid                    
        
        var myGrid = $("#orders");
                
                
//                myGrid.jqGrid({
//                    url:"index.php/procurement/populateReceipts?status=readytoinvoicememo",
//                    datatype: 'json',
//                    mtype: 'GET',
//                    colNames:['Receipt Reference','Supplier Receipt','Supplier',/*'Estimated Value','Owner',*/'Status','Order Id','Order Ref','Quote Ref'/*,'Owner','Needed By Date'*/],
//                    colModel :[ 
//                        {name:'reference', index:'reference', width:100, align:'right',editable:false},
//                        {name:'supplier_receipt_number',width:140, index:'supplier_receipt_number',editable:false,align:'right'},
//                        {name:'supplier_name', index:'supplier_name', width:140, align:'right',editable:false},
////                        {name:'estimated_value', index:'estimated_value', width:100, align:'right',editable:false,editoptions:{size:"20",maxlength:"30"}},
////                        {name:'owner', index:'owner', width:140, align:'right',editable:true,editoptions:{size:"20",maxlength:"30"}},
//                        {name:'status', index:'status', width:100, align:'right',editable:false,editoptions:{size:"20",maxlength:"30"}},
//                        {name:'order_id', index:'order_id',hidden:true},
//                        {name:'order_reference', index:'order_reference',editable:false, width:100, align:'right'},
//                        {name:'quote_reference', index:'quote_reference',editable:false, width:100, align:'right'},
////                        {name:'owner_name', index:'owner_name',editable:false, width:80, align:'right'},
////                        {name:'needed_by_date', index:'needed_by_date',editable:false, width:120, sorttype:'date'}
//                    ],
//                    pager: '#pager',
//                    rowNum:10,
//                    rowList:[5,10,20],
//                    sortname: 'id',
//                    sortorder: 'desc',
//                    viewrecords: true,
//                    gridview: true,
//                    multiselect:true,
//                    
//                    ignoreCase:true,
//                    rownumbers:true,
//                    height:'auto',
//                    width:'90%',
//                    caption: 'Receipts',
//            
//                    jsonReader : {
//                        root:"quotedata",
//                        page: "page",
//                        total: "total",
//                        records: "records",
//                        cell: "dprow",
//                        id: "id"
//                    },
//                    
//                    subGrid:true,
//                    subGridRowExpanded: function(subgrid_id, row_id) {
//                            // we pass two parameters
//                            // subgrid_id is a id of the div tag created whitin a table data
//                            // the id of this elemenet is a combination of the "sg_" + id of the row
//                            // the row_id is the id of the row
//                            // If we wan to pass additinal parameters to the url we can use
//                            // a method getRowData(row_id) - which returns associative array in type name-value
//                            // here we can easy construct the flowing
//                            var subgrid_table_id, pager_id;
//                            subgrid_table_id = subgrid_id+"_t";
//                            
//                            pager_id = "p_"+subgrid_table_id;
//                            
//                            $("#"+subgrid_id).html("<table id='"+subgrid_table_id+"' class='scroll'></table><div id='"+pager_id+"' class='scroll'></div>");
////                           
//                            var settingObj = {grid_id:subgrid_table_id,pager:pager_id,multiselect:false};
//                            var hiddenObj = {line_reference:true,pp_quantity:true,pp_value:true,receiving_notes:true,returned_notes:true};
//                            prepareReceiptItemsGrid(settingObj,{oper:'receipt',receiptId:row_id},false,{},hiddenObj)
//                             
//                    }
//                })
                var settingsObj ={grid_id:'orders',pager:'pager',multiselect:true};
                var subgridSettingsObj = {customButtons:{resubmit:true},multiselect:false,hiddenObj:{owner_name:true,approved_by_name:true}};
                prepareReceiptsGrid(settingsObj,{_status: ['readytoinvoicememo','waitingforapproval','rejected']},true,{},{approved_by_name:true},subgridSettingsObj)
                myGrid.navGrid("#pager",{edit:false,add:false,view:false,del:false,search:false},{},{},{},{},{});
                var buttons = {submit:true,process:true,load_owner_all:true,resubmit:true};
                addCustomButtonsInReceiptGrid('orders','pager',null,buttons);
//                myGrid.jqGrid('navButtonAdd','#pager',{
//                   caption:"", 
//                   title:"Create Invoice And Memo" ,
//                   buttonicon:"ui-icon-note",
//                   id:"add_orders",
//                   onClickButton : function () {
//                       var selectRows = myGrid.getGridParam('selarrrow');
//                       var noOfRows = selectRows.length;
//                       var orderId = 0;
//                       var mismatch = 0;
//                      
//                       if (noOfRows ==0){
//                           $( "#modal-warning" ).dialog("open");
//                        }
//                        else if (noOfRows > 0){
//                            for (i=0;i<noOfRows;i++){
//                                //console.log(selectRows[i]);
//                                var newOrderId = myGrid.getCell(selectRows[i],'order_id');
//                                //console.log(newOrderId);
//                                if (orderId==0){
//                                      orderId = newOrderId;
//                                  }
//                                  else if (newOrderId!=orderId){
//                                      $( "#modal-warning-order" ).dialog("open");
//                                      mismatch = 1;
//                                      break;
//                                  }
//                            }
//                            if (mismatch==0){
//                                $.ajax({
//                                    type:"POST",
//                                    url:"index.php/procurement/createInvoiceMemoUpdateOrderReceipt",
//                                    data:{receipt_ids:selectRows,
//                                            order_id:orderId},
//                                    success:function (jqXHR){
//                                        emptyMessages();
//                                        var jsonRes = JSON.parse(jqXHR);
//                                        if (jsonRes.invoice_id!=null){
//                                            showSuccessMessage("Invoice No Inv-" + jsonRes.invoice_id + " Is Created Successfully");
//                                        }
//                                        if (jsonRes.memo_id!=null){
//                                            showSuccessMessage("CreditMemo No CM-" + jsonRes.memo_id + " Is Created Successfully");
//                                        }
//                                        myGrid.trigger("reloadGrid");
//                                    }
//                                })
//                            }
//                        }
//                        
//                               
//                    } 
//                });
//                myGrid.jqGrid('navButtonAdd','#pager',{
//                   caption:"", 
//                   title:"Submit For Approval",
//                   buttonicon:"ui-icon-flag",
//                   id:"approved_orders",
//                   onClickButton : function () {
//                       var selectRows = myGrid.getGridParam('selarrrow');
//                       var noOfRows = selectRows.length;
//                       
//                       if (noOfRows ==0){
//                           $( "#modal-warning" ).dialog("open");
//                        }
//                        else 
//                            $.ajax({
//                                type:"POST",
//                                url:"index.php/procurement/submitForApprovalBeforeInvoice",
//                                data:{receipt_ids:selectRows
//                                        },
//                                success:function (jqXHR){
//                                    emptyMessages();
//                                    showSuccessMessage(" Selected Records Have Been Submitted For Approval");
//                                    myGrid.trigger("reloadGrid");
//                                },
//                                error:function (jqXHR){
//                                    emptyMessages();
//                                    showErrorMessage(" Selected Records Could Not Be Submitted For Approval Due To Internal Error");
//                                }
//                            })
//                               
//                    } 
//                });
                myGrid.jqGrid('filterToolbar', {stringResult: true, searchOnEnter: true, defaultSearch : "cn"});
                //$("#del_orders").insertAfter("#add_orders");
            
            
        
       
        $( "#modal-warning-order" ).dialog({
            autoOpen:false,
            height: 80,
            modal: true
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

        <div id="modal-warning-order" title="Warning">
            <p>Please Select Receipt with The Same Order Reference</p>
        </div>
      
        
        <div style="display: block;height: 100%;" class="shopifine-ui-dialog  ui-widget ui-widget-content ui-corner-all" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-dialog-form">
            <?php  $this->load->view("common/message"); ?>
            <div class="table-grid">
                <h1 id="table header">Create Invoice</h1>
                <table id="orders"><tr><td/></tr></table> 
                <div id="pager"></div>
            </div>
            
        </div>
        
       
        <?php $this->load->view("partial/footer"); ?>  
        
</body>   
</html>



    
   