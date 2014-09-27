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
                
        
        $.validator.addMethod('comboBoxrequired', function(value, element) {
              var selectId = element.id;
              var inputIdSelector = "#" + selectId + "-input";
              if (value == ""){
                  inputVal = $(inputIdSelector).val();
                  if (inputVal == "" || inputVal == null){
                      return false;
                  }
              }
              return true;
          }, 'Please select from the dropdown or add new element in box');  
          $.validator.addMethod('lessthanallowed', function (value, element, param) {
                    var allowed_id = element.id + '-total';
                  
                    if (parseFloat(value)>parseFloat($("#"+allowed_id).val())){
                        return false;
                    }
                    return true;
                },"{0} Quantity Can Not Be More Than Total Received/Returned For This Line Item");
          
          $.validator.addMethod('tallyquantity', function(value, element) {
              var ordered_quantity = parseInt($("#quantity").text());
              var already_received = parseInt($("#already_received").text());
              var received_quantity = parseInt($("#received_quantity").val());
              var returned_quantity = parseInt($("#returned_quantity").val());
              var subgrid=$("#subgrid_ref").val();
              var receipt_line_id = $("#receipt_line_ref").val();
              var action = $("#action").val();
              var oper = $("#oper").val();
              console.log("already_received" + already_received + "received_quantity" + received_quantity + "returned_quantity" + returned_quantity);
              var total_received = already_received + received_quantity + returned_quantity;
              if (action=='edit'){
                  // for edit actionold values need to be subtracted
                  
                      total_received = total_received - $("#"+subgrid).getCell(receipt_line_id,'received_quantity')
                  //}
                  //else if (oper=="return"){
                      total_received = total_received - $("#"+subgrid).getCell(receipt_line_id,'returned_quantity');
                  //}
                  
              }
              console.log("total_received/returned" + total_received);
              console.log("ordered_quantity" + ordered_quantity);
              if (total_received>ordered_quantity){
                  
                      return false;
                  
              }
              return true;
          }, 'Total Received And Returned Can Not Be More Than Ordered'); 
        //form validation 
         $("#quoteForm").validate();
         $("#ppItemForm").validate({
             rules:{
                 pp_quantity_received:
                        {required:true,
                        lessthanallowed:"Part Payable Received"}
             }
         });
         
         $("#itemForm").validate({
             ignore:".ignore-fields",
             rules:{
                 received_quantity:{
                     required:true,
                     //noBlank:true,
                     digits:true,
                     min:1
                 },
                 received_value:{                  
                     required:true,
                     //noBlank:true,
                     number:true,
                     minStrict:0
                 },
                 returned_quantity:{
                     required:true,
                     //noBlank:true,
                     digits:true,
                     min:1
                 },
                 returned_value:{                  
                     required:true,
                     //noBlank:true,
                     number:true,
                     minStrict:0
                 }
                 
         }
     }
     );
         
         
        // datepicker for Add Form 
        
        function quotegriddates(id){
            jQuery("#"+id+"_needed_by_date","#orders").datepicker({dateFormat:"yy-mm-dd",minDate:0});
        }
       
        //end datepicker
         
       var myGridPkd = $("#lineItems");
              
       $( "#dialog-form" ).dialog({
                                autoOpen: false,
                                height: 'auto',
                                width: '65%',
                                position:[220,25],
                                modal: true,
                                buttons: {
                                    "DoneButton": {
                                        id:"doneBtn",
                                        text:"Done",
                                        click:function() {
                                       
                                        var isValid = $("#quoteForm").valid();
                                       
                                        if (isValid){
                                            
                                            $( this ).dialog( "close" );
                                            
                                        } //end ifvalid
                                        
                                    }}, //end of Create button
                                    Cancel: function() {
                                        //in cancel just recset notes
                                        $("#receivingNotes").val("");
                                        $( this ).dialog( "close" );
                                    }
                                },//end buttons
                                open: function(){
                                     $( "#tabs" ).tabs({ 
                                        //cache
                                        beforeLoad: function( event, ui ) {
                                            var tabcache = $("#tabcache").val();
                                            if (tabcache==1){
                                                if ( ui.tab.data( "loaded" ) ) {
                                                event.preventDefault();
                                                    return;
                                                }

                                                ui.jqXHR.success(function() {
                                                    ui.tab.data( "loaded", true );
                                                });
                                            }
                                            
                                        },
                                        beforeActivate: function (event,ui){


                                        console.log(ui.newTab[0].id);
                                        console.log(ui.oldTab[0].id);
                                        if (ui.oldTab[0].id=="notesLink"){

                                           $("#tabcache").val("1");
                                        }
                                        
                                        
                                    },
                                         
                                       
                                        //load contents after the tab loading is complete
                                        load: function(event,ui){
                                                
                                                    $.ajax({
                                               method:"POST",
                                               url:'index.php/procurement/getOrderDetails',
                                               data:{orderId: myGrid.getGridParam('selrow')},
                                               success:function(response){
                                                   //console.log(response);
                                                   var resJson = JSON.parse(response);
                                                   //console.log(resJson);
                                                   $("#orderRef").text(resJson.reference);
                                                   $("#supplierOP").text(resJson.supplier_name);
                                                   $("#warehouseOP").text(resJson.warehouse);
                                                   $("#quoteOP").text(resJson.quote_reference);
                                                   $("#raisedByOP").text(resJson.raised_by_name);
                                                   $("#approvedByOP").text(resJson.approved_by_name);
                                                   $("#estValueOP").text(resJson.estimated_value);
                                                   $("#receivingNotes").val(resJson.receiving_notes);
                                                   //$("#receivingNotes").val("abcc");
                                               }
                                            })
                                            $("#lineItems").jqGrid({
                                                url:'index.php/procurement/populateOrderItems',
                                                datatype: 'json',
                                                mtype: 'POST',
                                                postData:{orderId: myGrid.getGridParam('selrow')},
                                                colNames:['Product id','Product','Quantity','Received','Returned','Need By Date','Price','Ordered Value','Received Value','Returned Value'/*'Notes'*/],
                                                colModel :[ 
                                                    {name:'product_id', index:'product_id',editable:false, hidden:true},
                                                    {name:'name', index:'name',editable:false, width:160, align:'right'},
                                                    {name:'quoted_quantity', index:'quoted_quantity', editable:false,width:60, align:'right'},
                                                    {name:'received_quantity', index:'received_quantity', editable:false,width:60, align:'right'},
                                                    {name:'returned_quantity', index:'returned_quantity', editable:false,width:60, align:'right'},
                                                    {name:'needed_by_date',index:'needed_by_date',editable:false, width:80, align:'right',hidden:true},
                                                    {name:'expected_price',index:'expected_price',editable:false, width:60, align:'right',hidden:true},
                                                    {name:'estimated_value', index:'estimated_value',editable:false, width:100, align:'right'},
                                                    {name:'received_value', index:'received_value',editable:false, width:100, align:'right'},
                                                    {name:'returned_value', index:'returned_value',editable:false, width:100, align:'right'},
        //                                            {name:'comments', index:'comments',editable:true, width:180, align:'right'}

                                                ],
                                                pager: '#pagerPk',
                                                rowNum:10,
                                                rowList:[5,10,20],
                                                sortname: 'id',
                                                sortorder: 'desc',
                                                viewrecords: true,
                                                gridview: true,
                                                ignoreCase:true,
                                                rownumbers:true,
                                                multiselect:true,
                                                height:'auto',
                                                width:'60%',
                                                caption: 'Line Items',

                                                jsonReader : {
                                                    root:"orderitemdata",
                                                    page: "page",
                                                    total: "total",
                                                    records: "records",
                                                    cell: "dprow",
                                                    id: "id"
                                                },
                                                //receipt Item subgrid
                                                 subGrid:true,
                                                subGridRowExpanded: function(subgrid_id, row_id) {

                                                        var subgrid_table_id, pager_id;
                                                        subgrid_table_id = subgrid_id+"_t";

                                                        pager_id = "p_"+subgrid_table_id;

                                                        $("#"+subgrid_id).html("<table id='"+subgrid_table_id+"' class='scroll'></table><div id='"+pager_id+"' class='scroll'></div>");
                                                        jQuery("#"+subgrid_table_id).jqGrid({
                                                                url:'index.php/procurement/populateReceiptItems?oper='+"orderline"+'&orderLineId='+row_id,
                                                                datatype: 'json',
                                                                colNames:['Line ref','Supplier Receipt','Reference','Receipt Id','Order Line Id','Order Line  Ref','Product Id','Product','Qty','Qty Rcvd','Rcvd Value','Qty Rtrnd','Rtrnd Value','Rcvd Notes','Rtrnd Notes'],
                                                                colModel :[ 
                                                                    {name:'line_reference', index:'line_reference',editable:false,width:60, align:'right',hidden:true},
                                                                    {name:'supplier_receipt_number', index:'supplier_receipt_number',editable:false,width:120, align:'right'},
                                                                    {name:'reference', index:'reference',editable:false,width:60, align:'right'},
                                                                    {name:'receipt_id', index:'receipt_id',editable:false, hidden:true},
                                                                    {name:'order_line_id', index:'order_line_id',editable:false, hidden:true},
                                                                    {name:'product_id', index:'product_id',editable:false, hidden:true},
                                                                    {name:'order_line_ref', index:'order_line_ref',editable:false, width:100, align:'right',hidden:true},
                                                                    {name:'name', index:'name',editable:false, width:100, align:'right',hidden:true},
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
                                                                multiselect:true,
                                                                jsonReader : {
                                                                    root:"receiptitemdata",
                                                                    page: "page",
                                                                    total: "total",
                                                                    records: "records",
                                                                    cell: "dprow",
                                                                    id: "id"
                                                                }
                                                            });
                                                        $("#"+subgrid_table_id).jqGrid('navGrid',"#"+pager_id,{edit:false,add:false,del:false,search:false,view:true});
                                                        // subgrid custom buttin navigation bar
                                                        $("#"+subgrid_table_id).jqGrid ('navButtonAdd', "#"+pager_id,
                                                        {
                                                            caption:"", 
                                                            title:"Edit Received For This Receipt",
                                                            buttonicon:"ui-icon-newwin",
                                                            id:"receive_lineItems",
                                                            onClickButton : function () { 
                                                                //need to pass grid id for dynamic reload;
                                                                var noOfRows = $("#"+subgrid_table_id).getGridParam("selarrrow").length;
                                                                if (noOfRows == 1){
                                                                    var lineId = $("#"+subgrid_table_id).getGridParam("selrow");
                                                                    // grid id would help us in getting order line related info from lineItem grid using order_line_id 
                                                                    // Sub grid would help us in getting receipt related information using receipt id
                                                                    var gridData ={'grid_id':'lineItems','sub_grid_id':subgrid_table_id,'order_line_id':$("#"+subgrid_table_id).getCell(lineId,'order_line_id'),
                                                                        'receipt_line_id':$("#"+subgrid_table_id).getGridParam("selrow"),'action':'edit','oper':'receive','url':"index.php/procurement/receiveItems"}
                                                                    $( "#dialog-form-item" ).data('grid_data',gridData).dialog( "open" );

                                                                }
                                                                else {
                                                                   $( "#modal-warning" ).dialog("open") ;
                                                                }

                                                             } 
                                                         });
                                                         $("#"+subgrid_table_id).jqGrid ('navButtonAdd', "#"+pager_id,
                                                         {
                                                            caption:"", 
                                                            title:"Edit Returned For This Receipt",
                                                            buttonicon:"ui-icon-arrowreturnthick-1-w",
                                                            id:"return_lineItems",
                                                            onClickButton : function () { 
                                                                //need to pass grid id for dynamic reload;
                                                                var noOfRows = $("#"+subgrid_table_id).getGridParam("selarrrow").length;
                                                                if (noOfRows == 1){
                                                                    var lineId = $("#"+subgrid_table_id).getGridParam("selrow");
                                                                    // grid id would help us in getting order line related info from lineItem grid using order_line_id 
                                                                    // Sub grid would help us in getting receipt related information using receipt id
                                                                    var gridData ={'grid_id':'lineItems','sub_grid_id':subgrid_table_id,'order_line_id':$("#"+subgrid_table_id).getCell(lineId,'order_line_id'),
                                                                        'receipt_line_id':$("#"+subgrid_table_id).getGridParam("selrow"),'action':'edit','oper':'return','url':"index.php/procurement/receiveItems"}
                                                                    $( "#dialog-form-item" ).data('grid_data',gridData).dialog( "open" );
                                                                }
                                                                else {
                                                                   $( "#modal-warning" ).dialog("open") ;
                                                                }

                                                             } 
                                                         });
                                                         
                                                         $("#"+subgrid_table_id).jqGrid ('navButtonAdd', "#"+pager_id,
                                                         {
                                                            caption:"", 
                                                            title:"Process For Part Payment",
                                                            buttonicon:"ui-icon-clipboard",
                                                            id:"pp_lineItems",
                                                            onClickButton : function () { 
                                                                //need to pass grid id for dynamic reload;
                                                                var noOfRows = $("#"+subgrid_table_id).getGridParam("selarrrow").length;
                                                                if (noOfRows == 1){
                                                                    var lineId = $("#"+subgrid_table_id).getGridParam("selrow");
                                                                    // grid id would help us in getting order line related info from lineItem grid using order_line_id 
                                                                    // Sub grid would help us in getting receipt related information using receipt id
                                                                    var gridData ={'grid_id':'lineItems','sub_grid_id':subgrid_table_id,'order_line_id':$("#"+subgrid_table_id).getCell(lineId,'order_line_id'),
                                                                        'receipt_line_id':$("#"+subgrid_table_id).getGridParam("selrow"),'action':'edit','oper':'return','url':"index.php/procurement/receiveItems"}
                                                                    $( "#dialog-form-partpayment" ).dialog('option','title','REC-'+$("#"+subgrid_table_id).getCell(lineId,'line_reference'))
                                                                    $( "#dialog-form-partpayment" ).data('grid_data',gridData).dialog( "open" );
                                                                }
                                                                else {
                                                                   $( "#modal-warning" ).dialog("open") ;
                                                                }

                                                             } 
                                                         });
                                                        $("#"+subgrid_table_id).jqGrid ('navButtonAdd', "#"+pager_id,
                                                            { caption: "", buttonicon: "ui-icon-calculator",
                                                              title: "Choose Columns",
                                                              onClickButton: function() {
                                                                   $("#"+subgrid_table_id).jqGrid('columnChooser');
                                                              }
                                                            });
                                                            //end sub grid navigator custom buttons
                                                }

                                            }).navGrid("#pagerPk",{edit:false,add:false,del:false,search:false,view:true},{},{},{},{},{});
                                            $("#lineItems").jqGrid('navButtonAdd','#pagerPk',{
                                            caption:"", 
                                            title:"Receive Items",
                                            buttonicon:"ui-icon-newwin",
                                            id:"receive_lineItems",
                                            onClickButton : function () { 
                                                //need to pass grid id for dynamic reload;
                                                var noOfRows = $("#lineItems").getGridParam("selarrrow").length;
                                                if (noOfRows == 1){
                                                    var gridData ={'grid_id':'lineItems','order_line_id':$("#lineItems").getGridParam("selrow"),
                                                        'action':'add','oper':'receive','url':"index.php/procurement/receiveItems"}
                                               
                                                 $( "#dialog-form-item" ).data('grid_data',gridData).dialog( "open" );
                                                 $("#receiptOp").combobox();
                                                  
                                                }
                                                else {
                                                   $( "#modal-warning" ).dialog("open") ;
                                                }
                                                
                                             } 
                                         });
                                         $("#lineItems").jqGrid('navButtonAdd','#pagerPk',{
                                            caption:"", 
                                            title:"Return Items",
                                            buttonicon:"ui-icon-arrowreturnthick-1-w",
                                            id:"return_lineItems",
                                            onClickButton : function () { 
                                                //need to pass grid id for dynamic reload;
                                                var noOfRows = $("#lineItems").getGridParam("selarrrow").length;
                                                if (noOfRows == 1){
                                                    var gridData ={'grid_id':'lineItems','order_line_id':$("#lineItems").getGridParam("selrow"),
                                                        'action':'add','oper':'return','url':"index.php/procurement/receiveItems"}
                                               
                                                 $( "#dialog-form-item" ).data('grid_data',gridData).dialog( "open" );
                                                 $("#receiptOp").combobox();
                                                  
                                                }
                                                else {
                                                   $( "#modal-warning" ).dialog("open") ;
                                                }
                                                
                                             } 
                                         });
                                        }
                                    }); 
                                     
                                    
                                                            
                                },

                                close: function() {
                                    //allFields.val( "" ).removeClass( "ui-state-error" 
                                    var receiving_notes = $("#receivingNotes").val();
                                    console.log(receiving_notes);
                                    $("#tabcache").val("0");
                                    
                                    $.ajax({url:"index.php/procurement/validateReceiving",
                                        type:"POST",
                                        data:{  
                                                receiving_notes:$("#receivingNotes").val(),
                                                order_id: $("#orders").getGridParam('selrow') 
                                            },

                                        success:function(response)
                                        {}

                                    }); //end ajax
                                    $("#quoteForm").data('validator').resetForm();
                                    $('#quoteForm')[0].reset();
                                    $("#lineItems").jqGrid("GridUnload");
                                    $("#tabs").tabs("destroy");
                                    $("#orders").trigger("reloadGrid");
                                    
                                    
                                }
                            });
        
       
        // Main Request For Quotation Grid                    
        
        var myGrid = $("#orders");
                
                
                myGrid.jqGrid({
                    url:'index.php/procurement/populatePOToReceive',
                    datatype: 'json',
                    mtype: 'GET',
                    colNames:['Reference','Supplier Id','Supplier','Estimated Value',/*'Owner',*/'Status','Raised By',/*'Owner',*/'Needed By Date'],
                    colModel :[ 
                        {name:'reference', index:'reference', width:80, align:'right',editable:false},
                        {name:'supplier_id', index:'supplier_id',hidden:true},
                        {name:'supplier_name', index:'supplier_name', width:140, align:'right',editable:false},
                        {name:'estimated_value', index:'estimated_value', width:100, align:'right',editable:false,editoptions:{size:"20",maxlength:"30"}},
//                        {name:'owner', index:'owner', width:140, align:'right',editable:true,editoptions:{size:"20",maxlength:"30"}},
                        {name:'status', index:'status', width:60, align:'right',editable:false,editoptions:{size:"20",maxlength:"30"}},
                        {name:'raised_by_name', index:'raised_by_name',editable:false, width:80, align:'right'},
//                        {name:'owner_name', index:'owner_name',editable:false, width:80, align:'right'},
                        {name:'needed_by_date', index:'needed_by_date',editable:false, width:120, sorttype:'date'}
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
                    width:680,
                    caption: 'Purchase Orders',
            
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
                            
                            var subgrid_table_id, pager_id;
                            subgrid_table_id = subgrid_id+"_t";
                            
                            pager_id = "p_"+subgrid_table_id;
                            
                            $("#"+subgrid_id).html("<table id='"+subgrid_table_id+"' class='scroll'></table><div id='"+pager_id+"' class='scroll'></div>");
                            jQuery("#"+subgrid_table_id).jqGrid({
                                    url:'index.php/procurement/populateOrderItems?q=2&orderId='+row_id,
                                    datatype: 'json',
                                    colNames:['Product Id','Product','Quantity','Received','Returned','Need By Date','Unit Price','Estimated Value','Received Value','Returned Value','Notes'],
                                    colModel :[ 
                                        {name:'product_id', index:'product_id',editable:false, hidden:true},
                                        {name:'name', index:'name',editable:false, width:120, align:'right'},
                                        {name:'quoted_quantity', index:'quoted_quantity', editable:false,width:50, align:'right'},
                                        {name:'received_quantity', index:'received_quantity', editable:false,width:50, align:'right'},
                                        {name:'returned_quantity', index:'returned_quantity', editable:false,width:50, align:'right'},
                                        {name:'needed_by_date',index:'needed_by_date',editable:false, width:60, align:'right',hidden:true},
                                        {name:'expected_price',index:'expected_price',hidden:true, width:50, align:'right'},
                                        {name:'estimated_value', index:'estimated_value',editable:false, width:60, align:'right'},
                                        {name:'received_value', index:'received_value',editable:false, width:60, align:'right'},
                                        {name:'returned_value', index:'returned_value',editable:false, width:60, align:'right',hidden:true},
                                        {name:'comments', index:'comments',editable:false, width:160, align:'right'}

                                    ],
                                    rowNum:20,
                                    pager: pager_id,
                                    sortname: 'id',
                                    sortorder: "asc",
                                    height: '100%',
                                    
                                    jsonReader : {
                                        root:"orderitemdata",
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
               
                myGrid.jqGrid('navButtonAdd','#pager',{
                   caption:"", 
                   title:"Match With Received Goods",
                   buttonicon:"ui-icon-newwin",
                   id:"add_orders",
                   onClickButton : function () {
                       var selectRows = myGrid.getGridParam('selarrrow');
                       var noOfRows = selectRows.length;
                      
                       if (noOfRows != 1){
                           $( "#modal-warning" ).dialog("open");
                       }
                        else {
                        $( "#dialog-form" ).dialog( "open" );
                        }
                    } 
                });
                myGrid.jqGrid('navButtonAdd','#pager',{
                   caption:"", 
                   title:"Ready For Invoice",
                   buttonicon:"ui-icon-tag",
                   id:"ready_orders",
                   onClickButton : function () {
                       var selectRows = myGrid.getGridParam('selarrrow');
                       var noOfRows = selectRows.length;
                       var mismatch = 0;
                      
                       if (noOfRows == 0){
                           $( "#modal-warning-none" ).dialog("open");
                       }
                       else {
                           for (i=0;i<noOfRows;i++){
                                //console.log(selectRows[i]);
                                var status = myGrid.getCell(selectRows[i],'status');
                                
                                if (status!='received'){
                                     $( "#modal-warning-status" ).dialog("open");
                                     mismatch = 1;
                                     break;
                                 }
                            }
                            if (mismatch==0){
                                $( "#dialog-confirm" ).dialog( "open" );
                            }
                           
                        }
                    } 
                });
                myGrid.jqGrid('filterToolbar', {stringResult: true, searchOnEnter: true, defaultSearch : "cn"});
                
        //line item dialog        
        $( "#dialog-form-item" ).dialog({
            autoOpen: false,
            height: 'auto',
            width: '35%',
            position:[450,25],
            modal: true,
            buttons: {
                "Done": {
                id:"processItems",
                text:"Confirm",
                click:function() {
                    var isvalid = $("#itemForm").valid();
                    
                    var grid = $(this).data('grid_data').grid_id;
                
                    var order_line_id = $(this).data('grid_data').order_line_id;
                    var oper = $(this).data('grid_data').oper;
                    var url = $(this).data('grid_data').url;

                    var order_id = myGrid.getGridParam('selrow');

                    //relevant only in case of edit
                    var subgrid = $(this).data('grid_data').sub_grid_id;
                    var receipt_line_id =  $(this).data('grid_data').receipt_line_id;
                    var action = $(this).data('grid_data').action;
                    
                   // by default assume action is "add"
                    var total_received_quantity = parseInt($("#received_quantity").val())+parseInt($("#already_received").text());
                    var total_received_value = parseFloat($("#received_value").val())+parseFloat($("#already_received_value").text());
                    var total_returned_quantity=parseInt($("#returned_quantity").val())+parseInt($("#already_returned").text());
                    var total_returned_value=parseFloat($("#returned_value").val())+parseFloat($("#already_returned_value").text());

                    if (action=='edit'){
                        //if (oper=='receive'){
                            //subtract old received
                            total_received_value = total_received_value - $("#"+subgrid).getCell(receipt_line_id,'received_value');
                            total_received_quantity = total_received_quantity - $("#"+subgrid).getCell(receipt_line_id,'received_quantity');
                        //}
                        //else if (oper=='return'){
                            //subtract old returned
                            total_returned_value = total_returned_value - $("#"+subgrid).getCell(receipt_line_id,'returned_value');
                            total_returned_quantity = total_returned_quantity - $("#"+subgrid).getCell(receipt_line_id,'returned_quantity');
                        //}
                    }


                   if (isvalid){
                       $.ajax({
                           url:url,
                           type:"POST",
                           data:{
                               order_id:order_id,
                               order_line_id:order_line_id,
                               product_id:$("#"+grid).getCell(order_line_id,'product_id'),
                               supplier_id:myGrid.getCell(order_id,'supplier_id'),
                               receipt:$("#receiptOp").val(),
                               receipt_ip:$("#receiptOp-input").val(),
                               total_received_quantity:total_received_quantity,
                               received_value:$("#received_value").val(),
                               total_received_value:total_received_value,
                               received_quantity:$("#received_quantity").val(),
                               quantity:parseInt($("#quantity").text()),
                               returned_quantity:$("#returned_quantity").val(),
                               returned_value:$("#returned_value").val(),
                               total_returned_quantity:total_returned_quantity,
                               total_returned_value:total_returned_value,
                               receiving_notes:$("#receiving_notes").val(),
                               returned_notes:$("#returned_notes").val(),
                               oper:$("#oper").val(),
                               action:$("#action").val()
                               
                           },
                           success:function (response){
                               //console.log("grid " + grid);
                               $("#"+grid).trigger("reloadGrid");
                               
                              //reload Main Grid
                               
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
                
                var grid = $(this).data('grid_data').grid_id;
                
                var order_line_id = $(this).data('grid_data').order_line_id;
                var oper = $(this).data('grid_data').oper;
                
                var order_id = myGrid.getGridParam('selrow');
                
                //relevant only in case of edit
                var subgrid = $(this).data('grid_data').sub_grid_id;
                var receipt_line_id =  $(this).data('grid_data').receipt_line_id;
                var action = $(this).data('grid_data').action;
                $.ajax({
                    url:"index.php/procurement/populateReceiptOptions",
                    method:"get",
                    data:{order_id:order_id},
                    success : function (response){
                        $("#receiptOp option").remove();
                        
                        var opts = '<option value="">Choose ';
                        opts +=response;
                        console.log(opts);
                        $("#receiptOp").html(opts);
                        
                        if (action=="edit"){
                            var selVal =$("#"+subgrid).getCell(receipt_line_id,'receipt_id')  ;
                            
                            $("#receiptOp").val(selVal);
                            $("#receiptOp").attr("disabled",true);
                            
                        }
                    }
                })
                if (oper == "receive"){
                    $("#received_quantity_cntnr,#received_value_cntnr,#receiving_notes_cntnr").show();
                    $("#returned_quantity,#returned_value").val("0");
                    //to be ignored by validation
                    $("#returned_quantity,#returned_value").addClass("ignore-fields");
                    
                    $("#formHeaderItem").text("Receive Items");
                    
                }
                else if (oper=="return"){
                    $("#returned_quantity_cntnr,#returned_value_cntnr,#returned_notes_cntnr").show();
                    $("#received_quantity,#received_value").val("0");
                    //to be ignored by validation
                    $("#received_quantity,#received_value").addClass("ignore-fields");
                    $("#formHeaderItem").text("Return Items");
                }
                if (action=="edit"){
                    
                    $("#status-message-li").text("Warning!! You are about to edit earlier receive/return entry.This will \n\
                    override  previous entry. This is not for NEW entry.Use main grid buttons to add entries");
                    $("#received_value").val($("#"+subgrid).getCell(receipt_line_id,'received_value'));
                    $("#received_quantity").val($("#"+subgrid).getCell(receipt_line_id,'received_quantity'));
                    //$("#receiving_notes").val($("#"+subgrid).getCell(receipt_line_id,'receiving_notes'));
                    $("#returned_value").val($("#"+subgrid).getCell(receipt_line_id,'returned_value'));
                    $("#returned_quantity").val($("#"+subgrid).getCell(receipt_line_id,'returned_quantity'));
                    //$("#returned_notes").val($("#"+subgrid).getCell(receipt_line_id,'returned_notes'));
                    // we need to store these for tally quantity validation
                    $("#subgrid_ref").val(subgrid);
                    $("#receipt_line_ref").val(receipt_line_id);
                 
                }
                
                $("#oper").val(oper);
                $("#action").val(action);
                
                $("#product").text($("#"+grid).getCell(order_line_id,'name'));
                $("#neededdate").text($("#"+grid).getCell(order_line_id,'needed_by_date'));
                $("#quantity").text($("#"+grid).getCell(order_line_id,'quoted_quantity'));
                $("#already_received").text($("#"+grid).getCell(order_line_id,'received_quantity'));
                $("#already_returned").text($("#"+grid).getCell(order_line_id,'returned_quantity'));
                //$("#already_received").text(100);
                $("#orderprice").text($("#"+grid).getCell(order_line_id,'expected_price'));
                $("#estimated_value").text($("#"+grid).getCell(order_line_id,'estimated_value'));
                $("#already_received_value").text($("#"+grid).getCell(order_line_id,'received_value'));
                $("#already_returned_value").text($("#"+grid).getCell(order_line_id,'returned_value'));

            },
            close: function(event,ui) {
                $("#itemForm").data('validator').resetForm();
                $('#itemForm')[0].reset();
                $("#estValue").text("");
                $("#curPrice").text("");
                $("#status-message-li").empty();
                $("#received_quantity,#received_value,#returned_quantity,#returned_value").removeClass("ignore-fields");
                $("#received_quantity_cntnr,#received_value_cntnr,\n\
                #returned_quantity_cntnr,#returned_value_cntnr\n\
                ,#receiving_notes_cntnr,#returned_notes_cntnr").hide();
                $("#receiptOp").attr("disbled",false);
               
                //try to destroy only if action is not edit as we are not using this during edit action
                if ($(this).data('grid_data').action!='edit'){
                    $("#receiptOp").combobox("destroy");
                }
                
                
            }
        });
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
                    var isvalid = $("#itemForm").valid();
                    
                    var grid = $(this).data('grid_data').grid_id;
                    var order_line_id = $(this).data('grid_data').order_line_id;                 
                    var url = $(this).data('grid_data').url;
                    var receipt_line_id =  $(this).data('grid_data').receipt_line_id;
                    

                   if (isvalid){
                       $.ajax({
                           url:'test',
                           type:"POST",
                           data:{
                               order_id:order_id,
                               order_line_id:order_line_id,
                               receipt_line_id:receipt_line_id,
                               pp_quantity:$("#pp_quantity_received").val(),
                               received_value:$("#pp_calculated_value").val(),
                               returned_notes:$("#pp_notes").val()
                           },
                           success:function (response){
                               //console.log("grid " + grid);
                               $("#"+grid).trigger("reloadGrid");
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
                
                var grid = $(this).data('grid_data').grid_id;
                
                var order_line_id = $(this).data('grid_data').order_line_id;
                var oper = $(this).data('grid_data').oper;
                
                var order_id = myGrid.getGridParam('selrow');
                
                //relevant only in case of edit
                var subgrid = $(this).data('grid_data').sub_grid_id;
                var receipt_line_id =  $(this).data('grid_data').receipt_line_id;
                
                
                $("#product_pp").text($("#"+grid).getCell(order_line_id,'name'));
                $("#sup_rec").text($("#"+subgrid).getCell(receipt_line_id,'supplier_receipt_number'));
                $("#receipt_line_pp").text($("#"+subgrid).getCell(receipt_line_id,'reference'))
                $("#pp_quantity_received_total").text(parseInt($("#"+subgrid).getCell(receipt_line_id,'received_quantity'))+parseInt($("#"+subgrid).getCell(receipt_line_id,'returned_quantity')));
                $("#already_received_rli").text($("#"+subgrid).getCell(receipt_line_id,'received_quantity'));
                $("#already_returned_rli").text($("#"+subgrid).getCell(receipt_line_id,'returned_quantity'));
                
                $("#already_received_value_rli").text($("#"+subgrid).getCell(receipt_line_id,'received_value'));
                $("#already_returned_value_rli").text($("#"+subgrid).getCell(receipt_line_id,'returned_value'));

            },
            close: function(event,ui) {
                $("#itemForm").data('validator').resetForm();
                $('#itemForm')[0].reset();
                $("#estValue").text("");
                $("#curPrice").text("");
                $("#status-message-li").empty();
                $("#received_quantity,#received_value,#returned_quantity,#returned_value").removeClass("ignore-fields");
                $("#received_quantity_cntnr,#received_value_cntnr,\n\
                #returned_quantity_cntnr,#returned_value_cntnr\n\
                ,#receiving_notes_cntnr,#returned_notes_cntnr").hide();
                $("#receiptOp").attr("disbled",false);
               
                //try to destroy only if action is not edit as we are not using this during edit action
                if ($(this).data('grid_data').action!='edit'){
                    $("#receiptOp").combobox("destroy");
                }
                
                
            }
        });
        $( "#modal-warning" ).dialog({
            autoOpen:false,
            height: 90,
            modal: true
        });
        $( "#modal-warning-none" ).dialog({
            autoOpen:false,
            height: 90,
            modal: true
        });
        $( "#modal-warning-status" ).dialog({
            autoOpen:false,
            height: 120,
           
            modal: true
        });
        $("#received_value, #returned_value").change(function (){
              var ordered_quantity = parseInt($("#quantity").text());
//             
              var already_received = parseInt($("#already_received").text());
              var received_quantity = parseInt($("#received_quantity").val());
              var returned_quantity = parseInt($("#returned_quantity").val());
//              var subgrid=$("#subgrid_ref").val();
//              var receipt_line_id = $("#receipt_line_ref").val();
//              var action = $("#action").val();
//              var oper = $("#oper").val();
//              console.log("already_received" + already_received + "received_quantity" + received_quantity + "returned_quantity" + returned_quantity);
              var total_received = already_received + received_quantity + returned_quantity;
              if (action=='edit'){
                 
                      total_received = total_received - $("#"+subgrid).getCell(receipt_line_id,'received_quantity')- $("#"+subgrid).getCell(receipt_line_id,'returned_quantity');
              }
//
//
//            if (total_received>=ordered_quantity){
                var action = $("#action").val();
                var oper = $("#oper").val();
                var subgrid=$("#subgrid_ref").val();
                var receipt_line_id = $("#receipt_line_ref").val();
                var received = parseFloat($("#received_value").val());
                var returned = parseFloat($("#returned_value").val());
                var already_received_value = parseFloat($("#already_received_value").text());
                var estimated  = parseFloat($("#estimated_value").text());
                var total = already_received_value + received + returned;
                console.log("already_received val " + already_received_value + "received_val" + received + "returned_val" + returned);
                if (action=='edit'){
                    total = total - $("#"+subgrid).getCell(receipt_line_id,'received_value')- $("#"+subgrid).getCell(receipt_line_id,'returned_value');
                }
                console.log(total);
                var unit_price = total/total_received;
                var order_init_price = parseFloat($("#orderprice").text());
                if (unit_price>order_init_price){
                    $("#status-message-li").text("Warning!! Current Unit Price Of Received/Returned Goods > Ordered");
                }
                else if (unit_price<order_init_price){
                    $("#status-message-li").text("Warning!! Current Unit Price Of Received/Returned Goods < Ordered");
                }
                else {
                    $("#status-message-li").empty();
                }
            //}
        });
        $( "#dialog-confirm" ).dialog({
            resizable: false,
            height:200,
             width:500,
            modal: true,
            autoOpen:false,
            buttons: {
                "I Am Sure": function() {
                     $.ajax({
                           url:"index.php/procurement/markForInvoicing",
                           type:"POST",
                           data:{orderIds:myGrid.getGridParam('selarrrow')                            
                           },
                           success:function (response){
                               
                                emptyMessages();
                                showSuccessMessage("The Selected Orders Are Now Ready To Invoice")
                                myGrid.trigger("reloadGrid");
                               
                           },
                           error:function (response){
                               emptyMessages();
                                showErrorMessage("The Selected Orders Could Not Be Processed Due To Internal Error")
                               
                           }
                       });
                       $( this ).dialog( "close" );
                    
                },
                Cancel: function() {
                    $( this ).dialog( "close" );
                }
            }
        });
        
    
    });        

        </script>
        
    </head>
     
    <body>
        <?php  $this->load->view("common/menubar"); ?>
        <div id="modal-warning" title="Warning">
            <p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>Please Select Exactly One Row</p>
        </div>
         <div id="modal-warning-status" title="Error" class="ui-state-error" style="border:none;">
            <p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>Only Completely Received Orders i.e. With Status "Received" 
                Can Be Readied For Invoice</p>
        </div>
        <div id="modal-warning-none" title="Warning">
            <p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>Please Select Record(s) To Continue</p>
        </div>
        <div id="dialog-confirm" title="Warning">
            <p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>
                Once Marked As "Ready For Invoice" Signifies All The Items For This Order Are 
                Dispatched By The Supplier And Marked As Received/Returned.
                # Of Returned And Received Quantities Can Be Edited ONLY If REJECTED In Due Approval Process But Total Quantity Should Always Match Ordered.Are You Sure?</p>
        </div>
        <div id ="dialog-form">
            <h1 id="formHeader">Purchase Order # <span id="orderRef"></span> Details</h1>   
            <form id="quoteForm">
                <fieldset>
                    <div id="tabs">
                        <ul>
                            <li id="baseLink"><a id="basic" href="<?php echo site_url('procurement/loadFormFragment') ?>">Basic Details</a></li>
                            <li id="notesLink"><a id="notes" href="<?php echo site_url('procurement/loadNotesFragment') ?>">Notes</a></li>

                        </ul>
                    </div>
                </fieldset>
            </form>
                  
            <div class="table-grid" style="padding-top:2em;">
                <h1 id="pkdheader">Line Items For This Purchase Order</h1>
                <table id="lineItems"><tr><td/></tr></table> 
                <div id="pagerPk"></div>
            </div>
        </div>
        
        <div style="display: block;height: 100%;" class="shopifine-ui-dialog ui-widget ui-widget-content ui-corner-all" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-dialog-form">
            <?php $this->load->view("common/message"); ?>  
            <div class="table-grid">
                <h1 id="table header">Open Purchase Orders</h1>
                <table id="orders"><tr><td/></tr></table> 
                <div id="pager"></div>
            </div>
            
        </div>
        
         <div id ="dialog-form-item">
            <div id ="status-message-li" class="ui-corner-all">
                
            </div>
             
            <h1 id="formHeaderItem">Receive Items</h1>   
            <form id="itemForm">
                <fieldset style="border:1px solid #A6C9E2">
                    <legend style="color: #2E6E9E;font-size: 110%;font-weight: bold;margin-left: 15px;">
                        Order Line Related Info</legend>  
                    
                    <div class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <div class="labeldiv">Products:</div>  
                                <div class="valuediv" id="product" name ="product" class="required"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row single-column-row">
                        <div class="column quote-column single-column">
                           <div class="field">
                                <div class="labeldiv">Needed By Date:</div>  
                                <div class="valuediv" id="neededdate" name ="neededdate" ></div>
                            </div>
                        </div>
                    </div>
                    <div class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <div class="labeldiv">Ordered Quantity:</div>  
                                <div class="valuediv" name="quantity" id="quantity" ></div>
                            </div>
                        </div>                        
                    </div>
                    <div class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <div class="labeldiv">Received  Quantity Till Date:</div>  
                                <div class="valuediv" name="already_received" id="already_received" ></div>
                                <div id ="return-help" class="ui-corner-all help-message-left">
                                    (Ready For Payment Subject To Appropriate Approval)
                                </div>
                            </div>
                        </div>                        
                    </div>
                    <div class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <div class="labeldiv">Marked For Return :</div>  
                                <div class="valuediv" name="already_returned" id="already_returned" ></div>
                                <div id ="return-help" class="ui-corner-all help-message-left">
                                    (Till Date)
                                </div>
                            </div>
                        </div>                        
                    </div>
                    <div class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <div class="labeldiv">Ordered Price:</div>  
                                <div class="valuediv" id="orderprice" name="orderprice" ></div>                               
                            </div>
                           
                        </div>                        
                    </div>
                    <div class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <div class="labeldiv">Estimated Value :</div>  
                                <div class="valuediv" id="estimated_value" name="estimated_value" ></div>                               
                            </div>
                           
                        </div>                        
                    </div>
                    <div class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <div class="labeldiv">Received Value  Till Date:</div>  
                                <div class="valuediv" id="already_received_value" name="already_received_value" ></div>  
                                <div id ="return-help" class="ui-corner-all help-message-left">
                                    (Payment Amount Subject To Appropriate Approval)
                                </div>
                            </div>
                           
                        </div>                        
                    </div>
                    <div class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <div class="labeldiv">Marked For Return Value :</div>  
                                <div class="valuediv" id="already_returned_value" name="already_returned_value"></div> 
                                <div id ="return-help" class="ui-corner-all help-message-left">
                                    (Till Date)
                                </div>
                            </div>
                           
                        </div>                        
                    </div>
                    </fieldset>
                    <fieldset>
                    <div class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="receipt" class="labeldiv">Receipt #:</label>  
                                <select name="receiptOp" id ="receiptOp" class="comboBoxrequired"> 
                                </select>
                                
                            </div>
                        </div>                        
                    </div>
                    <div id="received_quantity_cntnr" class="row single-column-row" style="display:none">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="received_quantity" class="labeldiv">Received Quantity:</label>  
                                <input id="received_quantity" name="received_quantity" class="required tallyquantity noBlank"/>
                                
                            </div>
                        </div>                        
                    </div>
                    <div id="received_value_cntnr" class="row single-column-row" style="display:none">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="received_value" class="labeldiv">Received Value:</label>  
                                <input id="received_value" name="received_value" class="required noBlank"/>
                                
                            </div>
                        </div>                        
                    </div>
                    <div id="returned_quantity_cntnr" class="row single-column-row" style="display:none">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="returned_quantity" class="labeldiv">Returned Quantity:</label>  
                                <input id="returned_quantity" name="returned_quantity" class="required tallyquantity noBlank"/>
                                
                            </div>
                        </div>                        
                    </div>
                    <div id="returned_value_cntnr" class="row single-column-row" style="display:none">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="returned_value" class="labeldiv">Returned Value:</label>  
                                <input id="returned_value" name="returned_value" class="required noBlank"/>
                                
                            </div>
                        </div>                        
                    </div>
                    <div id="receiving_notes_cntnr" class="row single-column-row" style="display:none">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="receiving_notes" class="labeldiv">Receiving Notes:</label>  
                                <textarea id="receiving_notes" name="receiving_notes" row="5" col="50"></textarea>
                            </div>
                        </div>                        
                    </div>
                    <div id="returned_notes_cntnr" class="row single-column-row" style="display:none">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="returned_notes" class="labeldiv">Returned Notes:</label>  
                                <textarea id="returned_notes" name="returned_notes" row="5" col="50"></textarea>
                            </div>
                        </div>                        
                    </div>
                    <input id ="oper" name ="oper" type="hidden" value="">
                    <input id ="action" name ="action" type="hidden" value="">
                    <input id ="subgrid_ref" name ="subgrid_ref" type="hidden" value="">
                    <input id ="receipt_line_ref" name ="receipt_line_ref" type="hidden" value="">
                </fieldset>
            </form>
        </div>
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
                                <div class="labeldiv">Total Quantity:</div>  
                                <div class="valuediv" name="pp_quantity_received_total" id="pp_quantity_received_total" ></div>
                                 <div id ="rli-help" class="ui-corner-all help-message-left">
                                    (Total Quantity Received/Marked For Return For This Receipt Line Item)
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
                                <div class="labeldiv">Marked For Return :</div>  
                                <div class="valuediv" name="already_returned_rli" id="already_returned_rli" ></div>
                                <div id ="return-help_rli" class="ui-corner-all help-message-left">
                                    (For This Receipt Line Item Till Date)
                                </div>
                            </div>
                        </div>                        
                    </div>
                    
                    <div class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <div class="labeldiv">Received Value  Till Date:</div>  
                                <div class="valuediv" id="already_received_value_rli" name="already_received_value_rli" ></div>  
                                <div id ="recval-help-rli" class="ui-corner-all help-message-left">
                                    (Payment Amount Subject To Appropriate Approval)
                                </div>
                            </div>
                           
                        </div>                        
                    </div>
                    <div class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <div class="labeldiv">Marked For Return Value :</div>  
                                <div class="valuediv" id="already_returned_value_rli" name="already_returned_value_rli"></div> 
                                <div id ="memval-help-rli" class="ui-corner-all help-message-left">
                                    (Memo Amount Subject To Appropriate Approval)
                                </div>
                            </div>
                           
                        </div>                        
                    </div>
                    </fieldset>
                    <fieldset>
                    
                    <div id="received_quantity_cntnr" class="row single-column-row">
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
                                <div class="valuediv" id="pp_calculated_value" name="pp_calculated_value"></div> 
                            </div>
                        </div>                        
                    </div>
<!--                    <div id="returned_quantity_cntnr" class="row single-column-row" style="display:none">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="returned_quantity" class="labeldiv">Quantity For Part Memo:</label>  
                                <input id="returned_quantity" name="returned_quantity" class="required tallyquantity noBlank"/>
                                
                            </div>
                        </div>                        
                    </div>
                    <div id="returned_value_cntnr" class="row single-column-row" style="display:none">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="returned_value" class="labeldiv">Returned Value:</label>  
                                <input id="returned_value" name="returned_value" class="required noBlank"/>
                                
                            </div>
                        </div>                        
                    </div>-->
                    <div id="pp_notes_cntnr" class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="pp_notes" class="labeldiv">Part Payment Notes:</label>  
                                <textarea id="pp_notes" name="pp_notes" row="5" col="50"></textarea>
                            </div>
                        </div>                        
                    </div>
<!--                    <div id="returned_notes_cntnr" class="row single-column-row" style="display:none">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="returned_notes" class="labeldiv">Returned Notes:</label>  
                                <textarea id="returned_notes" name="returned_notes" row="5" col="50"></textarea>
                            </div>
                        </div>                        
                    </div>-->
                    
                    <input id ="receipt_line_ref_pp" name ="receipt_line_ref_pp" type="hidden" value="">
                </fieldset>
            </form>
        </div>
        <?php $this->load->view("partial/footer"); ?>  
        <input id="tabcache" name="tabcache" value="0"/>
</body>   
</html>



    
   