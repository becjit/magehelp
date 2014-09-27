<html>
    <head>
       <?php $this->load->view("common/header"); ?>
        <link rel="stylesheet" rev="stylesheet" href="<?php echo base_url();?>css/chosen.css" />
        <script src="<?php echo base_url();?>js/chosen.jquery.min.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
<!--        <script type="text/javascript" src="http://jzaefferer.github.com/jquery-validation/jquery.validate.js"></script>-->
        <style>
            .column {
            float: left;
            padding: 1em 1em 3em;
            width: 100%;
            }
            .base-column{
               padding: 1em 1em 1em; 
            }
           
            .field{
                width:100%;
            }
            .ui-widget-header {height:12px;}
            .quote-column {
            float: left;
            padding-bottom: 0.5em;
            width: 45em;
            }
            .ui-combobox-input{
                width:23em;
            }
             #supplierOp-input{
                width:10em;
            }
            #warehouseOp-input{
                width:10em;
            }
            .shopifine-ui-dialog{
                height:auto;
            }
            
            .ui-tabs {
                height: 20em;
                margin: 0 auto;
                width: 45%;
                left:0;
            }
             
            .calculated {
                color: green;
                font-size: 90%;
            }
            .ui-tabs-nav{
                height:22px;
            }
             .chzn-container{
                font-size:inherit;
            }
/*            .labeldiv {
                color: #2E6E9E;
                float: left;
                font-size: 110%;
                font-weight: bold;
                margin-right: .5em;
                width: 18%;
                word-wrap: break-word;
            }*/
        </style>
       
    <script type="text/javascript">
                $(function() {
        $.validator.addMethod('minStrict', function (value, el, param) {
                    return value > param;
                },"Price must be more than 0");
        $.validator.addMethod('hasItems', function (value, el, param) {
            if($("#quoteId").val()!=""){
                var noOfRows = $("#lineItems").getGridParam("records");
                //alert(noOfRows);
                if (noOfRows<1){
                    return false;
                }
            }
            return true;
        },"Items must be added  for RFQ");
        //form validation 
        
         $("#quoteForm").validate({
            
             rules:{
                quoteId:{
                    hasItems:true
                } 
             },
             errorPlacement :function (error,element){
//                 console.log( error );
//                 console.log( element );
//                 console.log(element.parent() );
                 if (element[0].id=="quoteId"){
                     error.appendTo("#status-message-li");
                 }
                 else { //defailt
                     error.appendTo(element.parent()) ;
                 }
                 
             }
         }
     );
     $("#bulkItemForm").validate({rules:{
                 quantity_bulk:{
                     required:true,
                     digits:true,
                     min:1
                 },
                 productoptions_bulk:{                  
                     required:true                   
                 }
         }}
     );
      $("#edit-quote-form").validate();
    
        $("#itemForm").validate({rules:{
                quantity:{
                    required:true,
                    digits:true,
                    min:1
                },
                exPrice:{                  
                    number:true                   
                }
        }}
     );
         
        // datepicker for Add Form 
        $( "#reqdate" ).datepicker({
            showOn: "button",
            buttonImage: "images/calendar.gif",
            buttonImageOnly: true,
            dateFormat:"dd/mm/yy",
            minDate:0
        });
        $( "#reqdate-edit-quote" ).datepicker({
            showOn: "button",
            buttonImage: "images/calendar.gif",
            buttonImageOnly: true,
            dateFormat:"dd/mm/yy"
        });
         $( "#neededdate" ).datepicker({
            showOn: "button",
            buttonImage: "images/calendar.gif",
            buttonImageOnly: true,
             dateFormat:"dd/mm/yy",
             minDate:0
        });
        function quotegriddates(id){
            jQuery("#"+id+"_needed_by_date","#rfqs").datepicker({dateFormat:"yy-mm-dd",minDate:0});
        }
        function quoteitemgriddates(id){
            jQuery("#"+id+"_needed_by_date","#lineItems").datepicker({dateFormat:"yy-mm-dd",minDate:0});
        }
        //end datepicker
        //com bo boxes
//        $("#warehouseOp").combobox();
//        $("#supplierOp").combobox();
        $("#productOp").combobox({customChange:function(){
                console.log("product id = " + $("#productOp").val());
                $.ajax({
                    type:"GET",
                    url:'index.php/procurement/getCostPrice',
                    data:{productid:$("#productOp").val()},
                    success:function (response){
                        $("#curPrice").text(" The Current Average Cost Price Is " +response );
                    }
                })
        }}
        
    ); 
    //end combox
       
         
       $( "#dialog-form" ).dialog({
            autoOpen: false,
            height: 'auto',
            width: '60%',
            modal: true,
            buttons: {
                "DoneButton": {
                    id:"doneBtn",
                    text:"Create RFQ",
                    click:function() {
                    var isValid = $("#quoteForm").valid();
                    if (isValid){
                       $.ajax({url:"index.php/procurement/createRFQ",
                            type:"POST",
                            data:{  quoteId:$("#quoteId").val(),
                                    supplierId:$("#supplierOp").val(),
                                    warehouseId:$("#warehouseOp").val(),
                                    reqdate:$("#reqdate").val(),
                                    notes:$("#desc").val()

                                },

                            success:function(response)
                            {
                               console.log($("#quoteId").val());
                                if ($("#quoteId").val()==""){
                                    if (response !='error'){
                                        $("#quoteId").val(response);
                                        $("#pkdheader").show();
                                        $("#doneBtn > span").text("Done");
                                        var lineSettingObj ={grid_id:'lineItems',pager:'pagerPk',owner_id:user_id,status:'draft'};
                                        prepareRFQItemsGrid(lineSettingObj,{quoteId:$("#quoteId").val()}, {}, {});
                                        $("#lineItems").navGrid("#pagerPk",{edit:false,add:false,del:true,search:false},{},{},{},{},{});
                                        var buttons = {add:true,edit:true,bulk:true,data:{quote_id:$("#quoteId").val(),bulk_grid_id:'lineItemsbulk'}};
                                        addCustomButtonsInRFQItemGrid(lineSettingObj, buttons);
                                        $("#lineItems").jqGrid('filterToolbar', {stringResult: true, searchOnEnter: true, defaultSearch : "cn"}); 
                                        $("#edit_lineItems").insertAfter("#add_lineItems");
                                    }
                                }

                            } //end success of Ajax
                        }) //end ajax
                        if ($("#quoteId").val()!=""){
                            $( this ).dialog( "close" );
                        } 

                    } //end ifvalid

                }}, //end of Create button
                Cancel: function() {
                 $.ajax({
                    method:"POST",
                    data:{id:$("#quoteId").val(),oper:'del'},
                    url:'index.php/procurement/modifyRFQ'
                })

                    $( this ).dialog( "close" );
                }
            },//end buttons
            open:function(event,ui){
                //reseting line item grids once again to be doubly sure
                $("#pkdheader").hide();
                $("#lineItems").jqGrid("GridUnload");


                $("#tabs").tabs({

                    load: function(event,ui){
                            //console.log( ui);
                            $( "#reqdate" ).datepicker({
                                showOn: "button",
                                buttonImage: "images/calendar.gif",
                                buttonImageOnly: true,
                                dateFormat:"dd/mm/yy",
                                minDate:0
                            });

                            if (ui.tab.id=="base"){
                                $("#supplierOp").val($("#supval").val());
                                $("#warehouseOp").val($("#warehouseval").val());
                                $("#reqdate").val($("#reqdateval").val());
                                // reset
                                $("#reqdateval").val("");
                                $("#warehouseval").val("");
                                $("#supval").val("");

                            }
                            else if (ui.tab.id=="notes"){
                                $("#approveNotes").val($("#notesval").val());
                                // reset
                                $("#notesval").val("");
                            }
                    },
                    beforeActivate: function (event,ui){

                        if (ui.newTab[0].id=="notesLink"){
                            $("#reqdateval").val($("#reqdate").val());
                            $("#warehouseval").val($("#warehouseOp").val());
                            $("#supval").val($("#supplierOp").val());

                        }
                        else if (ui.newTab[0].id=="baseLink"){
                            $("#notesval").val($("#approveNotes").val());
                        }
                    }
                });

            },

            close: function(event,ui) {

                //validate so that close button is not pressed while there is no line item
                var noOfRec =$("#lineItems").getGridParam("records");
                //if (noOfRec<1){
                        $.ajax({
                        method:"POST",
                        data:{id:$("#quoteId").val(),entity:'rfq',items_count:noOfRec},
                        url:'index.php/procurement/closeValidate'
                    })
                //}

                //reset line item grids
                $("#pkdheader").hide();
                $("#lineItems").jqGrid("GridUnload");
                //reload main grid
                $("#rfqs").trigger("reloadGrid");
                //destrying the tab to bring it to the initial state while opening it next
                $("#tabs").tabs("destroy");
                //change Button Text To original
                $("#doneBtn > span").text("Create RFQ");
                $("#status-message-li").empty();

            }
        });
        
        //Line Item Dialog
        
       $( "#dialog-form-item" ).dialog({
            autoOpen: false,
            height: 'auto',
            width: '40%',
            position:[350,25],
            modal: true,
            buttons: {
                "Add Item": function() {
                   var isvalid = $("#itemForm").valid();
                   var grid = $(this).data('grid_data').grid_id;
                   var rfq_id = $(this).data('grid_data').quote_id;
                   var line_id = $(this).data('grid_data').line_id;
                   var action = $(this).data('grid_data').action;
                   var url = "index.php/procurement/addRFQItem";
                  
                   if (action =='edit'){
                       url = "index.php/procurement/modifyRFQItem";
                   }
                   console.log ("data " + grid + " " + rfq_id);
                   if (action=='edit' && $("#ischangeditem").val()==0){
                       //dont submit. Nothing has changed
                       $( this ).dialog( "close" );
                   }
                   else {

                   if (isvalid){
                       
                            $.ajax({
                                url:url,
                                type:"POST",
                                data:{
                                    rfq:rfq_id,
                                    productid:$("#productOp").val(),
                                    needeedByDate:$("#neededdate").val(),
                                    quantity:$("#quantity").val(),
                                    exprice:$("#exPrice").val(),
                                    descItem:$("#descItem").val(),
                                    oper:action,
                                    line_id:line_id
                                },
                                success:function (response){
                                        
                                    $("#rfqs").trigger("reloadGrid");
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
            open: function(event,ui){
                var action = $(this).data('grid_data').action;
                var grid_id = $(this).data('grid_data').grid_id;
                var line_id = $(this).data('grid_data').line_id;
                var quote_id = $(this).data('grid_data').quote_id;
                console.log("line_id = "+ line_id);
                console.log("quote_id = "+ quote_id + " name " + $("#"+grid_id).getCell(line_id,'name'));
                if (action=='edit'){
                    $("#productOp-input").parent().hide();
                    $("#product_name_edit").show();
                    $("#product_name_edit").text($("#"+grid_id).getCell(line_id,'name'));
                    $("#quantity").val($("#"+grid_id).getCell(line_id,'quoted_quantity'));
                    $("#exPrice").val($("#"+grid_id).getCell(line_id,'expected_price'));
                    $("#estValue").text($("#"+grid_id).getCell(line_id,'estimated_value'));
                    
                    var needDate = $("#"+grid_id).getCell(line_id,'needed_by_date');
                    
                    if (needDate!=null && needDate!=""){
                        var d1 = Date.parse(needDate);
                        $("#neededdate").val(d1.toString('dd/MM/yyyy'));
                    }
                }
            },
            close: function(event,ui) {
                $("#itemForm").data('validator').resetForm();
                $("#productOp-input").parent().show();
                $(".inithide").hide();
                $('#itemForm')[0].reset();
                $("#estValue").text("");
                $("#curPrice").text("");
            }
        });
        $( "#dialog-form-bulk" ).dialog({
            autoOpen: false,
            height: 'auto',
            width: '50%',
            position:[350,25],
            modal: true,
            buttons: {
                "Add Bulk": function() {
                   var isvalid = $("#bulkItemForm").valid();
                   var grid = $(this).data('grid_data').grid_id;
                   var rfq_id = $(this).data('grid_data').quote_id;
                   console.log ("data " + grid + " " + rfq_id);

                   if (isvalid){
                       $.ajax({
                           url:"index.php/procurement/addRFQItemBulk",
                           type:"POST",
                           data:{
                               rfq:rfq_id,
                               data:$("#"+grid).getGridParam('data')
                           },
                           success:function (response){
                               //console.log("grid " + grid);
                               $("#lineItems").trigger("reloadGrid");
                               $("#status-message-li").empty();
                               //reload Main Grid
                               
                           }
                       })
                       $( this ).dialog( "close" );
                   }

                },
                Cancel: function() {

                    $( this ).dialog( "close" );
                }
            },
            open: function(event,ui){
                $("#lineItemsbulk").jqGrid({
                    datatype: 'local',
                    colNames:['Product','Quantity'],
                    colModel :[ 
                        {name:'name', index:'name',editable:false, width:200, align:'right'},
                        {name:'quantity', index:'quantity', editable:true,width:80, align:'right'}
                    ],
                    pager: '#pagerPk_bulk',
                    rowNum:10,
                    rowList:[5,10,20],
                    sortname: 'id',
                    sortorder: 'desc',
                    viewrecords: true,
                    gridview: true,
                    ignoreCase:true,
                    rownumbers:true,
                    height:'auto',
                    width:400,
                    caption: 'Products & Quantities'

                }).navGrid("#pagerPk_bulk",{edit:true,add:false,del:true,search:false},{},{},{},{},{});
            },
            close: function(event,ui) {
                $("#bulkItemForm").data('validator').resetForm();
                $('#bulkItemForm')[0].reset();
                $(".chzn-select").val('').trigger("liszt:updated");
                 $("#lineItemsbulk").jqGrid("GridUnload");
            }
        });
        $( "#edit-quote-dialog" ).dialog({
            autoOpen: false,
            height: 'auto',
            width: '400',
            position:[350,25],
            modal: true,
            buttons: {
                "Edit Quote Details": function() {
                    var isvalid = $("#edit-quote-form").valid();

                   if (isvalid){
                       $.ajax({
                           url:'index.php/procurement/modifyRFQ',
                           type:"POST",
                           data:{
                               oper:'edit',
                               id:myGrid.getGridParam("selrow"),
                               supplier_name:$("#supplierOp-edit-quote").val(),
                               warehouse_id:$("#warehouseOp-edit-quote").val(),
                               needed_date:$("#reqdate-edit-quote").val(),
                               notes:$("#notes-edit-quote").val()
                           },
                           success:function (response){
                               //console.log("grid " + grid);
                               $("#rfqs").trigger("reloadGrid");
                              
                               //reload Main Grid
                               
                           }
                       })
                       $( this ).dialog( "close" );
                   }

                },
                Cancel: function() {

                    $( this ).dialog( "close" );
                }
            },
            open: function(event,ui){
                var quote_id = myGrid.getGridParam("selrow");
                var needDate = myGrid.getCell(quote_id,'needed_by_date');
                $("#supplierOp-edit-quote").val(myGrid.getCell(quote_id,'supplier_id')) ;  
                $("#warehouseOp-edit-quote").val(myGrid.getCell(quote_id,'warehouse_id')) ;  
                if (needDate!=null && needDate!=""){
                    var d1 = Date.parse(needDate);
                    $("#reqdate-edit-quote").val(d1.toString('dd/MM/yyyy'));
                }
            },
            close: function(event,ui) {
                $("#edit-quote-form").data('validator').resetForm();
                $('#edit-quote-form')[0].reset();
                
            }
        });
        $( "#delete-quote-dialog" ).dialog({
            autoOpen: false,
            height: '20',
            width: '300',
            position:[350,25],
            modal: true,
            buttons: {
                "Cancel Selected RFQ?": function() {
                    
                       $.ajax({
                           url:'index.php/procurement/modifyRFQ',
                           type:"POST",
                           data:{
                               oper:'del',
                               id:myGrid.getGridParam("selarrrow")
                              
                           },
                           success:function (response){
                               //console.log("grid " + grid);
                               $("#rfqs").trigger("reloadGrid");
                              
                           }
                       })
                       $( this ).dialog( "close" );
                   

                }
            }
           
            
        });
        // Main Request For Quotation Grid                    
        
        var myGrid = $("#rfqs");
        
        myGrid.jqGrid({
            url:'index.php/procurement/populateRFQ',
            datatype: 'json',
            mtype: 'POST',
            postData:{_status:['open','draft','waitingforapproval','rejected']},
            colNames:['Reference','Supplier','Estimated Value','Owner Id','Status','Raised By','Owner','Needed By Date','Approver','Supplier Id','Warehouse Id'],
            colModel :[ 

                {name:'reference', index:'reference', width:80, align:'right',editable:false},
                {name:'supplier_name', index:'supplier_name', width:140, align:'right'},
                {name:'estimated_value', index:'estimated_value', width:100, align:'right',editable:false,editoptions:{size:"20",maxlength:"30"}},
                {name:'owner_id', index:'owner_id', width:140, align:'right',editable:true,editoptions:{size:"20",maxlength:"30"},hidden:true},
                {name:'status', index:'status', width:60, align:'right',editable:false,editoptions:{size:"20",maxlength:"30"}},
                {name:'raised_by_name', index:'raised_by_name',editable:false, width:80, align:'right'},
                {name:'owner_name', index:'owner_name',editable:false, width:80, align:'right'},
                {name:'needed_by_date', index:'needed_by_date',editable:true, width:120, sorttype:'date',editrules:{date:true}},
                {name:'approved_by_name', index:'approved_by_name',editable:false, width:80, align:'right'},
                {name:'supplier_id', index:'supplier_id',editable:false, hidden:'true'},
                {name:'warehouse_id', index:'warehouse_id',editable:false, hidden:'true'}
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
            caption: 'Requests For Quotation',

            jsonReader : {
                root:"quotedata",
                page: "page",
                total: "total",
                records: "records",
                cell: "dprow",
                id: "id"
            },
            editurl:'index.php/procurement/modifyRFQ',
            subGrid:true,
            subGridRowExpanded: function(subgrid_id, row_id) {
                    var subgrid_table_id, pager_id;
                    subgrid_table_id = subgrid_id+"_t";
                    pager_id = "p_"+subgrid_table_id;
                    // we need to set #quoteId as we are reusing dialog-form-item
                    $("#quoteSubgridId").val(row_id);
                    $("#"+subgrid_id).html("<table id='"+subgrid_table_id+"' class='scroll'></table><div id='"+pager_id+"' class='scroll'></div>");
                    var owner_id = myGrid.getCell(row_id,'owner_id');
                    var rfq_status = myGrid.getCell(row_id,'status');
                    var lineSettingObj = {grid_id:subgrid_table_id,pager:pager_id,owner_id:owner_id,status:rfq_status};
                    prepareRFQItemsGrid(lineSettingObj,{quoteId:row_id}, {}, {});
                    
                    var del = false;
                    var buttons ={};
                    // if status is open or draft then only add modify buttons
                    if (rfq_status=='open' || rfq_status=='draft'){
                        if (user_id==owner_id){
                            del = true;
                        }
                        
                        buttons = {add:true,edit:true,bulk:true,data:{quote_id:$("#quoteSubgridId").val(),bulk_grid_id:'lineItemsbulk'}};
                    }
                    $("#"+subgrid_table_id).jqGrid('navGrid',"#"+pager_id,{edit:false,add:false,del:del,search:false,view:true});
                    
                    addCustomButtonsInRFQItemGrid(lineSettingObj, buttons);

            },
            subGridRowColapsed: function(subgrid_id, row_id) {
                    // this function is called before removing the data
                    //var subgrid_table_id;
                    //subgrid_table_id = subgrid_id+"_t";
                    //jQuery("#"+subgrid_table_id).remove();
                    $("#quoteSubgridId").val("");
            }
        }).navGrid("#pager",{edit:false,add:false,del:false,search:false,view:true},{},{},{},{});

        
        var buttons = {add:true,edit:true,del:true,reopen:true,submit_approval:true,gen_quote:true,comments:true,load_owner_all:true};
        addCustomButtonsInRFQGrid('rfqs', 'pager', null, buttons, null);
       myGrid.jqGrid('filterToolbar', {stringResult: true, searchOnEnter: true, defaultSearch : "cn"});
       //reArrange The Icons
       $("#edit_rfqs").insertAfter("#add_rfqs");
       $("#del_rfqs").insertAfter("#edit_rfqs");
       $("#comments_rfqs").insertAfter("#del_rfqs");
       $("#gen_quote_rfqs").insertAfter("#submit_approve_rfqs");

      
       
       $(".bulk-input").change (function(){
            var isvalid = $("#bulkItemForm").valid();
            if (isvalid){
               var products = $("#productOptions_bulk").val();
               $.each(products, function(){
                   console.log(" id "+ this);
                   console.log($("#productOptions_bulk option:eq("+this+")").text())
                   var qty = parseFloat($("#quantity_bulk").val());
                   var edit = false;
                   var existingQ = parseFloat($("#lineItemsbulk").getCell(this,'quantity'));
                   if (!isNaN(existingQ) && existingQ!=false){
                       qty +=existingQ;
                       edit = true;
                   }
                   var myrow = {name:$("#productOptions_bulk option:eq("+this+")").text(), quantity:qty};
                   if (edit){
                       $("#lineItemsbulk").setRowData(this, myrow);
                   }
                   else{
                       $("#lineItemsbulk").addRowData(this, myrow);
                   }
                   
               })
                
            }
            
       });
    $(".chzn-select").chosen();
     $("#itemForm input,#itemForm select").change(function (){
            $("#ischangeditem").val("1");
        });
    
    });        
   
    $(window).load(function(){
       
        var warningDialogs={one:true,none:true,morethanone:true,exactlyone:true};
        initDialogs(warningDialogs);
        initCommentsForQuote();
    });
        </script>
        
    </head>
     
    <body>
        <?php  $this->load->view("common/menubar"); ?>
        <?php  $this->load->view("common/dialogs"); ?>
        <div id ="dialog-form" class="inithide">
            <h1 id="formHeader">RFQ # <span id="quoteRef"></span> Details</h1>   
            <form id="quoteForm">
                <fieldset>
                    <div id="tabs">
                        <ul>
                            <li id="baseLink"><a id="base" href="<?php echo site_url('procurement/loadRFQFormFragment') ?>">Basic Details</a></li>
                            <li id="notesLink"><a id ="notes" href="<?php echo site_url('procurement/loadRFQNotesFragment') ?>">Notes</a></li>

                        </ul>
                    </div>
                    <div id ="status-message-li" class="ui-corner-all" style="margin-top: 10px; width:15em;"> 
                                    
                    </div>
                     
                    <div class="table-grid" style="padding-top:2em;">
                        <h1 id="pkdheader">Add Line Items</h1>
                        <table id="lineItems"><tr><td/></tr></table> 
                        <div id="pagerPk"></div>
                    </div>
                    
                    
                    

                </fieldset>
            </form>
        </div>
       <input id="quoteSubgridId" name ="quoteSubgridId" type="hidden" value=""/>
        <div id ="dialog-form-item" class="inithide">
            <h1 id="formHeader">Add New Line Item</h1>   
            <form id="itemForm">
                <fieldset>
                    <div class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="productOp" class="labeldiv">Products:</label>  
                                <select name="productOp" id ="productOp" class="required initshow"> 
                                    <option value="">Choose 
                                        <?= $productOptions ?> 
                                </select>
                                <div class="valuediv valuediv-edit inithide" id="product_name_edit" name ="product_name_edit" style="display:none"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row single-column-row">
                        <div class="column quote-column single-column">
                           <div class="field">
                                <label for="neededdate" class="labeldiv">Needed By Date:</label>  
                                <input id="neededdate" name ="neededdate" type="text"/>
                            </div>
                        </div>
                    </div>
                    <div class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="quantity" class="labeldiv">Quantity:</label>  
                                <input id="quantity" name="quantity" class="calinput"/>
                            </div>
                        </div>                        
                    </div>
                    <div class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="exPrice" class="labeldiv">Expected Price:</label>  
                                <input id="exPrice" name="exPrice" class="calinput"/>
                                
                            </div>
                            <div class="field">
                                <label style="width:100%;" id="curPrice" name="curPrice" class="calculated"/>
                            </div>
                        </div>                        
                    </div>
                    <div class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="estValue" class="labeldiv">Estimated Value:</label>  
                                <label id="estValue" name="estValue" class="calculated"/>
                                
                            </div>
                        </div>                        
                    </div>
                    <div class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="descItem" class="labeldiv">Notes:</label>  
                                <input id="descItem" name="descItem" size="40" maxlength="60"/>
                            </div>
                        </div>
                        <input id="ischangeditem" name="ischangeditem" type="hidden" value="0"/>
                    </div>
                    
                </fieldset>
            </form>
        </div>
        
        <div style="display: block;height: 100%;" class="shopifine-ui-dialog ui-widget ui-widget-content ui-corner-all" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-dialog-form">
            <?php $this->load->view("common/message"); ?>
            <div class="table-grid">
                <h1 id="table header">Request For Quotations</h1>
                <table id="rfqs"><tr><td/></tr></table> 
                <div id="pager"></div>
            </div>
            
        </div>
       <div id ="dialog-form-bulk" style="overflow:hidden;" class="inithide">
            <h1 id="formHeader">Add Bulk Quotations </h1>   
            <form id="bulkItemForm" class="single-column-form" style="width:100%; margin-left: 0;">
                <fieldset>
                    <div class="row single-column-row">
                        <div class="column single-column">
                            <div class="field">
                                <label for="productOptions_bulk" class="labeldiv">Products:</label>  
                                <select name="productOptions_bulk[]" id ="productOptions_bulk" class="chzn-select bulk-input" multiple="multiple" style="width:200px;height:20px;"> 
                                        
                                        <?=$productOptions?> 
                                </select>   
                            </div>
                        </div> 
                    </div>
                     <div class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="quantity_bulk" class="labeldiv">Quantity:</label>  
                                <input id="quantity_bulk" name="quantity_bulk" class="bulk-input"/>
                            </div>
                        </div>                        
                    </div>
                     
                    <div class="table-grid" style="padding-top:2em;">
                        <h1 id="pkdheader">Items For RFQ</h1>
                        <table id="lineItemsbulk"><tr><td/></tr></table> 
                        <div id="pagerPk_bulk"></div>
                    </div>
                    
                    
                    

                </fieldset>
            </form>
        </div>
       
       <div id ="edit-quote-dialog" style="overflow:hidden;" class="inithide">
            <h1 id="formHeader-edit-quote">Edit  Quotation </h1>   
            <form id="edit-quote-form" class="single-column-form" style="width:100%; margin-left: 0;">
                <fieldset>
                    <div class="row single-column-row">
                        <div class="column base-column">
                            <div class="field">
                                <label for="supplierOp-edit-quote" class="labeldiv-edit">Supplier:</label>  
                                <select name="supplierOp-edit-quote" id ="supplierOp-edit-quote" class="required"> 
                                    <option value="">Choose 
                                        <?= $supplierOptions ?> 
                                </select>

                            </div>
                        </div>
                    </div>
                     <div class="row single-column-row">
                        <div class="column base-column">
                            <div class="field">
                                <label for="warehouseOp-edit-quote" class="labeldiv-edit">Warehouse:</label>  
                                <select name="warehouseOp-edit-quote" id ="warehouseOp-edit-quote"> 
                                    <option value="">Choose 
                                        <?= $warehouseOptions ?> 
                                </select>

                            </div>

                        </div>                       
                    </div>
                    <div class="row single-column-row">
                        <div class="column base-column" style="width:35em;">
                            <div class="field">
                                <label for="reqdate-edit-quote"class="labeldiv-edit" >Request By Date:</label>  
                                <input id="reqdate-edit-quote" name ="reqdate-edit-quote" type="text" class="dateValidate" style="width:12em;"/>
                            </div>
                        </div>                      
                    </div>
                    <div class="row single-column-row">
                        <div class="column" style="width:80%">
                            <div class="field">
                                <label for="notes-edit-quote" class="labeldiv">Notes:</label>  
                                <textarea id="notes-edit-quote" name ="notes-edit-quote" rows="7" cols="45"></textarea>
                            </div>
                        </div>

                    </div>
                </fieldset>
            </form>
        </div>
       
       <div id ="delete-quote-dialog" style="overflow:hidden;" class="inithide" title="Cancel RFQs">
            
        </div>
        <?php $this->load->view("partial/footer"); ?>       
</body>   
</html>
<script>
   
    $(".calinput").change(function(){
        var exprice =$("#quantity").val()*$("#exPrice").val();
        $("#estValue").text(exprice);
    });
    
    
</script>


    
   