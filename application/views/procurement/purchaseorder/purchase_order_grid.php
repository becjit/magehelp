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
                width:1200px;
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
                width:100%;
            }
            
            .shopifine-ro-label {
                float: left;
                padding-right: 0.5em;
                width: 50%;
                word-wrap: break-word;
                color:#2E6E9E;
                font-size: 110%
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
                width: 55%;
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
                width:8em ;
                margin-right: 0;
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
            .single-column-row{
                margin-left:0;
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
          
          $.validator.addMethod("threshold", function(value, element, params) {
              console.log("param"+ params + $(params).text());
              
            var hi = typeof params == "string" ? 
                     parseFloat($(params).text(), 10) :
                     params;
                 if (hi===""){
                     hi=1;
                 }
                 console.log("limit"+hi);
                 params=hi;
            if (isNaN(hi))
              return true;
            else
              return parseInt(value) >= hi;
          }, $.validator.format("{1} Can Not Be Less Than  Than {0},The {1} Already in Partpayment Process ")); 


          
            $.validator.addMethod('lessthanallowednotdelivered', function (value, element, param) {
                    var order_line_id= $("#order_line_ref_cnbd").val();
                    var ordered = 0;
                    if (order_line_id!=""){
                        var already_received = parseInt($("#lineItems").getCell(order_line_id,'received_quantity'));
                        var already_returned = parseInt($("#lineItems").getCell(order_line_id,'returned_quantity'));
                        ordered = parseInt($("#lineItems").getCell(order_line_id,'quoted_quantity'));
                        
                        var total = already_received+already_returned + value;
                        console.log("tital = " + total);
                           
                        if (total>ordered){
                            return false;
                        }
                    }
                    return true;
                },"{0} Quantity Can Not Be More Than Total Ordered For This Line Item");
          
          $.validator.addMethod('tallyquantity', function(value, element) {
              var ordered_quantity = parseInt($("#quantity").text());
              var already_received = parseInt($("#already_received").text());
              var already_returned = parseInt($("#already_returned").text());
              var undelivered = parseInt($("#cnbd_quantity").text());
              var received_quantity = parseInt($("#received_quantity").val());
              var returned_quantity = parseInt($("#returned_quantity").val());
              var subgrid=$("#subgrid_ref").val();
              var receipt_line_id = $("#receipt_line_ref").val();
              var action = $("#action").val();
              var oper = $("#oper").val();
              console.log("already_received" + already_received + "received_quantity" + received_quantity + "returned_quantity" + returned_quantity);
              var total_received = already_received + already_returned + undelivered + received_quantity + returned_quantity;
              if (action=='edit'){
                  // for edit actionold values need to be subtracted
                  
                      total_received = total_received - $("#"+subgrid).getCell(receipt_line_id,'received_quantity')
                  //}
                  //else if (oper=="return"){
                      total_received = total_received - $("#"+subgrid).getCell(receipt_line_id,'returned_quantity');
                  //}
                  
              }
//              console.log("total_received/returned/notdelivered" + total_received);
//              console.log("ordered_quantity" + ordered_quantity);
              if (total_received>ordered_quantity){
                  
                      return false;
                  
              }
              return true;
          }, 'Total Received And Returned Can Not Be More Than Ordered'); 
        //form validation 
         $("#quoteForm").validate();
         
         $("#cnbdItemForm").validate({
             rules:{
                 cnbd_qty:{
                     required:true,                     
                     digits:true,
                     min:1,
                     lessthanallowednotdelivered:"Sum of Received/Returned/Not Delivered "
                 }
             }
         });
         
         $("#itemForm").validate({
             ignore:".ignore-fields",
             rules:{
                 received_quantity:{
                     required:true,
                     //noBlank:true,
                     digits:true,
                     min:1//,
                     //threshold:'#pp_quantity_receipt'
                 },
                 received_value:{                  
                     required:true,
                     //noBlank:true,
                     number:true,
                     minStrict:0//,
                     //threshold:'#pp_amount_receipt'
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
                 },
                 vat:{
                    required:true,
                    number:true,
                    minStrict:0 
                 }
                 
         }
     }
     );
       $("#paymentForm").validate({rules:{
                 
                 amount:{                  
                     required:true,
                     number:true,
                     minStrict:0
                 }
                 
         }}
        );   
         
        // datepicker for Add Form 
        
        $( "#expiry_date" ).datepicker({
            showOn: "button",
            buttonImage: "images/calendar.gif",
            buttonImageOnly: true,
            dateFormat:"dd/mm/yy",
            minDate:0
        });
        
        function quotegriddates(id){
            jQuery("#"+id+"_needed_by_date","#orders").datepicker({dateFormat:"yy-mm-dd",minDate:0});
        }
       
        //end datepicker
         
       var myGridPkd = $("#lineItems");
              
       $( "#dialog-form" ).dialog({
                                autoOpen: false,
                                height: 'auto',
                                width: '70%',
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
                                                    if (ui.oldTab[0].id=="notesLink"){
                                                       $("#tabcache").val("1");
                                                    }
                                            },
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
                                            });
                                            var settingsObj ={grid_id:'lineItems',pager:'pagerPk',multiselect:true};
                                            var subgridSettingsObj = {customButtons:{edit_received:true,edit_returned:true,columnchooser:true}};
                                            var order_id = myGrid.getGridParam('selrow');
                                            var owner_id = myGrid.getCell(order_id,'owner_id');
                                            var status = myGrid.getCell(order_id,'status');
                                            console.log("order_id" + order_id + "owner id = " +owner_id + "status "+ status)
                                            prepareOrderItemsGrid(settingsObj,{orderId: myGrid.getGridParam('selrow')},true,{},{needed_by_date:true,expected_price:true,comments:true},subgridSettingsObj)
                                            $("#lineItems").navGrid("#pagerPk",{edit:false,add:false,del:false,search:false,view:true},{},{},{},{},{});
                                            var buttons ={receive:true,return_item:true,cnbd_item:true};
                                            addCustomButtonsToOrderItemGrid('lineItems','pagerPk',buttons,owner_id,status);
                                            
                                            var settingsObj ={grid_id:'payments',pager:'pagerPayments'};
                                            var postData={orderId: order_id,type:'advance'};
                                            preparePaymentsGrid(settingsObj,postData);
                                            
                                            $("#payments").navGrid("#pagerPayments",{edit:false,add:false,del:false,search:false,view:true},{},{},{},{},{});
                                            var btns = {add:true,edit:true,data:{type:'advance',order_id:order_id,owner_id:owner_id}};
                                            addCustomButtonsInPaymentsGrid('payments','pagerPayments',null,btns);
                                            $("#payments").jqGrid('filterToolbar', {stringResult: true, searchOnEnter: true, defaultSearch : "cn"});
                                        }
                                    }); 
                                     
                                    
                                                            
                                },

                                close: function() {
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
        var postData={_status:['open','receiving','received']}
        var settingsObj = {grid_id:'orders',pager:'pager',multiselect:true,width:'1000'};
        
        prepareOrdersGrid(settingsObj,postData,true,{},{},{});               
        myGrid.navGrid("#pager",{edit:false,add:false,view:false,del:false,search:false},{height:280,reloadAfterSubmit:false,closeAfterEdit:true,recreateForm:true,checkOnSubmit:true},{},{},{},{});
               
        var buttons = {match:true,ready:true,comments:true,mark_receiver:true,assign:true,load_status_all:true,load_owner_all:true};
        addCustomButtonsOrderGrid(settingsObj,buttons);               
        myGrid.jqGrid('filterToolbar', {stringResult: true, searchOnEnter: true, defaultSearch : "cn"});
                
        //line item dialog        
        $( "#dialog-form-item" ).dialog({
            autoOpen: false,
            height: 'auto',
            width: '60%',
            position:[250,25],
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
                               receipt_line_id:receipt_line_id,
                               vat:$("#vat").val(),
                               expiry_date:$("#expiry_date").val(),
                               batch_number:$("#batch_number").val(),
                               
                               total_received_quantity:total_received_quantity,
                               received_value:$("#received_value").val(),
                               total_received_value:total_received_value,
                               received_quantity:$("#received_quantity").val(),
                               quantity:parseInt($("#quantity").text()),
                               returned_quantity:$("#returned_quantity").val(),
                               returned_value:$("#returned_value").val(),
                               cnbd_quantity : parseInt($("#cnbd_quantity").text()),
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
                    $("#returned_value").val($("#"+subgrid).getCell(receipt_line_id,'returned_value'));
                    $("#returned_quantity").val($("#"+subgrid).getCell(receipt_line_id,'returned_quantity'));
                    var expDate = $("#"+subgrid).getCell(receipt_line_id,'expiry_date');
                    if (expDate!=null && expDate!=""){
                        var d1 = Date.parse(expDate);
                        $("#expiry_date").val(d1.toString('dd/MM/yyyy'));
                    }
                    $("#batch_number").val($("#"+subgrid).getCell(receipt_line_id,'batch_number'));
                    $("#vat").val($("#"+subgrid).getCell(receipt_line_id,'vat'));
                    var pp_qty = parseInt($("#"+subgrid).getCell(receipt_line_id,'pp_quantity'));
                    $("#autogen-batch,#batch-help").hide();
                    if (pp_qty>0){
                        $("#pp_cntnr").show();
                        var pp_amt = $("#"+subgrid).getCell(receipt_line_id,'pp_value');
                        $("#pp_amount_receipt").text(pp_amt);
                        $("#pp_quantity_receipt").text(pp_qty);
                      
                        $("#received_quantity").rules("add", {
                                threshold: '#pp_quantity_receipt',
                                messages: {
                                  threshold: $.format("Quantity Can Not Be Less Than "+ pp_qty + ",The Quantity Already in Partpayment Process ")
                                }
                               });
                        $("#received_value").rules("add", {
                         threshold: '#pp_amount_receipt',
                         messages: {
                           threshold: $.format("Amount Can Not Be Less Than  Than "+ pp_amt+ ",The Amount Already in Partpayment Process ")
                         }
                        });
                    }
                    
                    
                    // we need to store these for tally quantity validation
                    $("#subgrid_ref").val(subgrid);
                    $("#receipt_line_ref").val(receipt_line_id);
                 
                }
                console.log($("#"+grid).getCell(order_line_id,'cnbd_quantity'));
                $("#cnbd_quantity").text($("#"+grid).getCell(order_line_id,'cnbd_quantity'));
                $("#oper").val(oper);
                $("#action").val(action);
                
                $("#product").text($("#"+grid).getCell(order_line_id,'name'));
                $("#neededdate").text($("#"+grid).getCell(order_line_id,'needed_by_date'));
                $("#quantity").text($("#"+grid).getCell(order_line_id,'quoted_quantity'));
                $("#already_received").text($("#"+grid).getCell(order_line_id,'received_quantity'));
                $("#already_returned").text($("#"+grid).getCell(order_line_id,'returned_quantity'));
                $("#pp_quantity").text($("#"+grid).getCell(order_line_id,'pp_quantity'));
                $("#pp_value").text($("#"+grid).getCell(order_line_id,'pp_value'));
                //$("#already_received").text(100);
                $("#orderprice").text($("#"+grid).getCell(order_line_id,'expected_price'));
                $("#estimated_value").text($("#"+grid).getCell(order_line_id,'estimated_value'));
                $("#already_received_value").text($("#"+grid).getCell(order_line_id,'received_value'));
                $("#already_returned_value").text($("#"+grid).getCell(order_line_id,'returned_value'));

            },
            close: function(event,ui) {
                $("#itemForm").data('validator').resetForm();
                $('#itemForm')[0].reset();
                $("#estValue,#pp_quantity_receipt,#pp_amount_receipt").text("");
                $("#curPrice").text("");
                $("#status-message-li").empty();
                $("#received_quantity,#received_value,#returned_quantity,#returned_value").removeClass("ignore-fields");
                $("#received_quantity_cntnr,#received_value_cntnr,\n\
                #returned_quantity_cntnr,#returned_value_cntnr\n\
                ,#receiving_notes_cntnr,#returned_notes_cntnr").hide();
                $(".inithide").hide();
                $("#autogen-batch,#batch-help").show();
                $("#receiptOp").attr("disbled",false);
                $("#received_quantity,#received_value").rules("remove", "threshold");
                //try to destroy only if action is not edit as we are not using this during edit action
                if ($(this).data('grid_data').action!='edit'){
                    $("#receiptOp").combobox("destroy");
                }
                
                
            }
        });
         
        $( "#dialog-form-cnbd" ).dialog({
            autoOpen: false,
            height: 'auto',
            width: '35%',
            position:[450,25],
            modal: true,
            buttons: {
                "Done": {
                id:"processItems",
                text:"Register",
                click:function() {
                    var isvalid = $("#cnbdItemForm").valid();
                    var grid = $(this).data('grid_data').grid_id;
                    var order_line_id = $(this).data('grid_data').order_line_id;             
                    
                    if (isvalid){
                       $.ajax({
                           url:'index.php/procurement/registerUndelivered',
                           type:"POST",
                           data:{
                               order_line_id:order_line_id,
                               cnbd_quantity:$("#cnbd_qty").val(),
                               received_quantity:$("#"+grid).getCell(order_line_id,'received_quantity'),
                               returned_quantity:$("#"+grid).getCell(order_line_id,'returned_quantity'),
                               ordered_quantity:$("#"+grid).getCell(order_line_id,'quoted_quantity'),
                               cnbd_notes:$("#cnbd_notes").val()
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
                console.log("cnbd" + $("#"+grid).getCell(order_line_id,'cnbd_quantity'));
                $("#order_line_ref_cnbd").val(order_line_id);
                $("#cnbd_qty").val($("#"+grid).getCell(order_line_id,'cnbd_quantity'));
            },
            close: function(event,ui) {
                $("#cnbdItemForm").data('validator').resetForm();
                $('#cnbdItemForm')[0].reset();
                $("#status-message-li-cnbd").empty();
                //try to destroy only if action is not edit as we are not using this during edit action
                if ($(this).data('grid_data').action!='edit'){
                    $("#receiptOp").combobox("destroy");
                }
                
                
            }
        });
       
        $("#received_value, #returned_value").change(function (){
              var ordered_quantity = parseInt($("#quantity").text());
//             
              var already_received = parseInt($("#already_received").text());
              var already_returned = parseInt($("#already_returned").text());
              var cnbd_quantity = parseInt($("#cnbd_quantity").text());
              var received_quantity = parseInt($("#received_quantity").val());
              var returned_quantity = parseInt($("#returned_quantity").val());
              var total_received = already_received + already_returned + cnbd_quantity+  received_quantity + returned_quantity;
              if (action=='edit'){
                        if (returned_quantity>0){
                            total_received = total_received - $("#"+subgrid).getCell(receipt_line_id,'returned_quantity');
                        }
                        else if (received_quantity>0){
                            total_received = total_received - $("#"+subgrid).getCell(receipt_line_id,'received_quantity');
                        }
                 
                      
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
        $("#autogen-batch").change(
            function (){
                if (this.checked){
                    $("#batch_number").removeClass("required");
                }
                else {
                    $("#batch_number").addClass("required");
                }
            }
        );
 
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
                <h1 id="pkdheader_item">Line Items For This Purchase Order</h1>
                <table id="lineItems"><tr><td/></tr></table> 
                <div id="pagerPk"></div>
            </div>
            <div class="table-grid" style="padding-top:2em;">
                <h1 id="pkdheader_advance">Manage Advance Payments</h1>
                <table id="payments"><tr><td/></tr></table> 
                <div id="pagerPayments"></div>
            </div>
        </div>
        
        <div style="display: block;height: 100%;" class="shopifine-ui-dialog ui-dialog-extra-wide ui-widget ui-widget-content ui-corner-all" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-dialog-form">
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
             
            <h1 id="formHeaderItem">Receive Items <div  id="neededdate" name ="neededdate" style="float:right;color:green;font-style: italic;text-decoration: underline" ></div></h1>   
            <form id="itemForm">
                <fieldset style="border:1px solid #A6C9E2">
                    <legend style="color: #2E6E9E;font-size: 110%;font-weight: bold;margin-left: 15px;">
                        Order Line Related Info </legend>  
                    
                    <div class="row">
                        <div class="column">
                            <div class="field">
                                <div class="labeldiv">Products:</div>  
                                <div class="valuediv" id="product" name ="product" class="required"></div>
                            </div>
                        </div>
                         <div class="column">
                            <div class="field">
                                <div class="labeldiv">Unit Price:</div>  
                                <div class="valuediv" id="orderprice" name="orderprice" ></div>                               
                            </div>
                           
                        </div> 
                        
                    </div>
                    
                    <div class="row">
                        <div class="column">
                            <div class="field">
                                <div class="labeldiv">Ordered Quantity:</div>  
                                <div class="valuediv" name="quantity" id="quantity" ></div>
                            </div>
                        </div>
                        <div class="column">
                            <div class="field">
                                <div class="labeldiv">Ordered Value :</div>  
                                <div class="valuediv" id="estimated_value" name="estimated_value" ></div>                               
                            </div>
                        </div>
                        
                    </div>
                    
                    
                    <div class="row">
                        <div class="column">
                            <div class="field">
                                <div class="labeldiv">Received  Quantity Till Date:</div>  
                                <div class="valuediv" name="already_received" id="already_received" ></div>
                                <div id ="return-help" class="ui-corner-all help-message-left">
                                    (Ready For Payment Subject To Appropriate Approval)
                                </div>
                            </div>
                        </div>   
                        
                        <div class="column">
                            <div class="field">
                                <div class="labeldiv">Received Value  Till Date:</div>  
                                <div class="valuediv" id="already_received_value" name="already_received_value" ></div>  
                                <div id ="return-help" class="ui-corner-all help-message-left">
                                    (Payment Amount Subject To Appropriate Approval)
                                </div>
                            </div>
                        </div>    
                    </div>
                    <div class="row">
                        <div class="column">
                            <div class="field">
                                <div class="labeldiv">Quantity In Part Payment Process:</div>  
                                <div class="valuediv" name="pp_quantity" id="pp_quantity" ></div>
                                <div id ="pp_quantity-help" class="ui-corner-all help-message-left">
                                    (Quantities  Getting Processed For Part Payment, Not Necessarily Already Paid For)
                                </div>
                            </div>
                        </div>   
                        
                        <div class="column">
                            <div class="field">
                                <div class="labeldiv">Amount In Part Payment Process:</div>  
                                <div class="valuediv" id="pp_value" name="pp_value" ></div>  
                                <div id ="pp_value-help" class="ui-corner-all help-message-left">
                                    (Amount  Getting Processed For Part Payment, Not Necessarily Already Paid)
                                </div>
                            </div>
                        </div>    
                    </div>
                    <div class="row">
                        <div class="column">
                            <div class="field">
                                <div class="labeldiv">Marked For Return :</div>  
                                <div class="valuediv" name="already_returned" id="already_returned" ></div>
                                <div id ="return-help" class="ui-corner-all help-message-left">
                                    (Till Date)
                                </div>
                            </div>
                        </div> 
                        <div class="column">
                            <div class="field">
                                <div class="labeldiv">Marked For Return Value :</div>  
                                <div class="valuediv" id="already_returned_value" name="already_returned_value"></div> 
                                <div id ="return-help" class="ui-corner-all help-message-left">
                                    (Till Date)
                                </div>
                            </div>
                           
                        </div>                        
                    </div>
                     <div class="row">
                        <div class="column">
                            <div class="field">
                                <div class="labeldiv">Could Not Be Delivered :</div>  
                                <div class="valuediv" name="cnbd_quantity" id="cnbd_quantity" ></div>
                                <div id ="cnbd-help" class="ui-corner-all help-message-left">
                                    (Quantity Which Supplier Could Not Deliver)
                                </div>
                            </div>
                        </div>                    
                    </div>
                    </fieldset>
                    <fieldset>
                    
                    <div class="row">
                        <div class="column">
                            <div class="field">
                                <label for="receipt" class="labeldiv">Receipt #:</label>  
                                <select name="receiptOp" id ="receiptOp" class="comboBoxrequired"> 
                                </select>
                                
                            </div>
                               
                        </div> 
                        <div class="column">
                            <div class="field">
                                <label for="batch_number" class="labeldiv">Batch Number:</label>  
                                <input id="batch_number" name="batch_number" class="required"/>
                                <input id="autogen-batch" name="autogen-batch"type ="checkbox"/>
                                <div id ="batch-help" class="ui-corner-all help-message-left">
                                Select The Check Box to Auto Generate ONLY If There Is No Batch Number In Exceptional Circumstances
                                </div>
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="column">
                            
                             <div class="field">
                                <label for="expiry_date" class="labeldiv">Best Before:</label>  
                                <input id="expiry_date" name="expiry_date" class="required"/>
                                
                            </div>
                               
                        </div> 
                        <div class="column">
                            <div class="field">
                                <label for="vat" class="labeldiv">Vat:</label>  
                                <input id="vat" name="vat" class="required"/><span style="color:green;font-weight:bold"> %</span>
                                
                            </div>
                        </div> 
                    </div>
                    
                        
                    <div class="row">
                         <div id="received_quantity_cntnr" class="row" style="display:none">
                            <div class="column">
                                <div class="field">
                                    <label for="received_quantity" class="labeldiv">Received Quantity:</label>  
                                    <input id="received_quantity" name="received_quantity" class="required tallyquantity noBlank"/>

                                </div>
                            </div>                        
                        </div>
                        <div id="received_value_cntnr" style="display:none">
                            <div class="column">
                                <div class="field">
                                    <label for="received_value" class="labeldiv">Received Value:</label>  
                                    <input id="received_value" name="received_value" class="required noBlank"/>
                                </div>
                            </div>                        
                        </div>
                        
                        
                    </div>
                    <div class="row">
                        <div id="returned_quantity_cntnr" style="display:none">
                            <div class="column">
                                <div class="field">
                                    <label for="returned_quantity" class="labeldiv">Returned Quantity:</label>  
                                    <input id="returned_quantity" name="returned_quantity" class="required tallyquantity noBlank"/>

                                </div>
                            </div>                        
                        </div>
                        <div id="returned_value_cntnr" style="display:none">
                            <div class="column quote-column single-column">
                                <div class="field">
                                    <label for="returned_value" class="labeldiv">Returned Value:</label>  
                                    <input id="returned_value" name="returned_value" class="required noBlank"/>

                                </div>
                            </div>                        
                        </div>
                    </div>
                    <div class="row inithide" id="pp_cntnr" style="display:none">
                        <div class="column">
                            <div class="field">
                                <div class="labeldiv">Part Process Quantity :</div>  
                                <div class="valuediv" id="pp_quantity_receipt" name="pp_quantity_receipt"></div> 
                                <div id ="pp_quantity_receipt-help" class="ui-corner-all help-message-left">
                                    (Quantity In Part Payment Process FOr This Receipt)
                                </div>
                            </div>
                        </div>                        

                        <div class="column">
                            <div class="field">
                               <div class="labeldiv">Part Process Amount :</div>  
                                <div class="valuediv" id="pp_amount_receipt" name="pp_amount_receipt"></div> 
                                <div id ="pp_amount_receipt-help" class="ui-corner-all help-message-left">
                                    (Amount In Part Payment Process FOr This Receipt)
                                </div>

                            </div>
                        </div>                        
                    </div>
                    <div class="row">
                        <div id="receiving_notes_cntnr"  style="display:none">
                            <div class="column">
                                <div class="field">
                                    <label for="receiving_notes" class="labeldiv">Receiving Notes:</label>  
                                    <textarea id="receiving_notes" name="receiving_notes" row="5" col="50"></textarea>
                                </div>
                            </div>                        
                        </div>
                        <div id="returned_notes_cntnr"  style="display:none">
                            <div class="column">
                                <div class="field">
                                    <label for="returned_notes" class="labeldiv">Returned Notes:</label>  
                                    <textarea id="returned_notes" name="returned_notes" row="5" col="50"></textarea>
                                </div>
                            </div>                        
                        </div>
                    </div>
                    
                    
                    <input id ="oper" name ="oper" type="hidden" value="">
                    <input id ="action" name ="action" type="hidden" value="">
                    <input id ="subgrid_ref" name ="subgrid_ref" type="hidden" value="">
                    <input id ="receipt_line_ref" name ="receipt_line_ref" type="hidden" value="">
                    <input id ="threshold_receipt" name ="threshold_receipt" type="hidden" value="">
                </fieldset>
            </form>
        </div>
        
        <div id ="dialog-form-cnbd">
            <div id ="status-message-li-cnbd" class="ui-corner-all">
                
            </div>
             
            <h1 id="formCnbdHeaderItem">Register Failure To Deliver</h1>   
            <form id="cnbdItemForm">
                    <fieldset>
                    
                    <div id="cnbd_quantity_cntnr" class="row single-column-row" style="width:90%">
                        <div class="column quote-column single-column" >
                            <div class="field">
                                <label for="cnbd_qty" class="labeldiv">Quantity:</label>  
                                <input id="cnbd_qty" name="cnbd_qty"/>
                                
                            </div>
                        </div>                        
                    </div>
                    

                    <div id="cnbd_notes_cntnr" class="row single-column-row" style="width:90%">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="cnbd_notes" class="labeldiv">Reason:</label>  
                                <textarea id="cnbd_notes" name="cnbd_notes" row="5" col="50"></textarea>
                            </div>
                        </div>                        
                    </div>
                </fieldset>
                 <input id ="order_line_ref_cnbd" name ="order_line_ref_cnbd" type="hidden" value="">
                 <input id ="threshold_cnbd" name ="threshold_cnbd" type="hidden" value="">
            </form>
        </div>
        <?php $this->load->view("partial/footer"); ?>  
        <input id="tabcache" name="tabcache" value="0" type="hidden"/>
</body>   
</html>



    
   