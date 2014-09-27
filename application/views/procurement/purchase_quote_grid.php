<html>
    <head>
       <?php $this->load->view("common/header"); ?>
<!--        <script type="text/javascript" src="http://jzaefferer.github.com/jquery-validation/jquery.validate.js"></script>-->
        <style>
            .column {
            float: left;
            padding: 1em 1em 3em;
            width: 26em;
            }
            #dialog-form *{
                font-size:96%;
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
             
            .calculated {
                color: green;
                font-size: 90%;
            }
        </style>
       
        <script type="text/javascript">
                $(function() {
        $.validator.addMethod('minStrict', function (value, el, param) {
                    return value > param;
                },"Price must be more than 0");
        $.validator.addMethod('hasItems', function (value, el, param) {
            if($("#quoteId").val()!=""){
                var noOfRows = myGridPkd.getGridParam("records");
                if (noOfRows<1){
                    return false;
                }
            }
            return true;
        },"Items must be added  for Quote");
        //form validation 
        
         $("#quoteForm").validate({
            
             rules:{
                quoteId:{
                    hasItems:true
                } 
             }
             
         }
     );
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
         $( "#neededdate" ).datepicker({
            showOn: "button",
            buttonImage: "images/calendar.gif",
            buttonImageOnly: true,
             dateFormat:"dd/mm/yy",
             minDate:0
        });
        function quotegriddates(id){
            jQuery("#"+id+"_needed_by_date","#quotes").datepicker({dateFormat:"yy-mm-dd",minDate:0});
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
        
        var myGridPkd = $("#lineItems"),lastsel;
                var editparametersitems = {
                    "keys" : true,
                    "oneditfunc" : quoteitemgriddates,
                    "successfunc" : function(){
                        lastsel=undefined;
                        $("#lineItems").trigger("reloadGrid");
                    },
                    "aftersavefunc" : null,
                    "errorfunc": function(){
                        lastsel=undefined;
                        
                    },
                    "afterrestorefunc" : null,
                    "restoreAfterError" : true,
                    "mtype" : "POST"
                    
                }; 
         
       $( "#dialog-form" ).dialog({
                                autoOpen: false,
                                height: 'auto',
                                width: '70%',
                                modal: true,
                                buttons: {
                                    "DoneButton": {
                                        id:"doneBtn",
                                        text:"Create Quote",
                                        click:function() {
                                        var isValid = $("#quoteForm").valid();
                                        if (isValid){
                                           $.ajax({url:"index.php/procurement/createQuotes",
                                                type:"POST",
                                                data:{  quoteId:$("#quoteId").val(),
                                                        supplierId:$("#supplierOp").val(),
                                                        warehouseId:$("#warehouseOp").val(),
                                                        reqdate:$("#reqdate").val(),
                                                        notes:$("#desc").val()
                                                        
                                                    },

                                                success:function(response)
                                                {
                                                   
                                                    if ($("#quoteId").val()==""){
                                                        console.log("if");
                                                        if (response !='error'){
                                                            $("#quoteId").val(response);
                                                            $("#pkdheader").show();
                                                            $("#doneBtn > span").text("Done");
                                                            $("#lineItems").jqGrid({
                                                                url:'index.php/procurement/populateQuoteItems',
                                                                datatype: 'json',
                                                                mtype: 'POST',
                                                                postData:{quoteId: $("#quoteId").val()},
                                                                colNames:['Product','Quantity','Need By Date','Expected Price','Estimated Value','Notes'],
                                                                colModel :[ 
                                                                    {name:'name', index:'name',editable:false, width:80, align:'right'},
                                                                    {name:'quoted_quantity', index:'quoted_quantity', editable:true,width:140, align:'right'},
                                                                    {name:'needed_by_date',index:'needed_by_date',editable:true, width:80, align:'right'},
                                                                    {name:'expected_price',index:'expected_price',editable:true, width:80, align:'right'},
                                                                    {name:'estimated_value', index:'estimated_value',editable:false, width:80, align:'right'},
                                                                    {name:'comments', index:'comments',editable:true, width:180, align:'right'}

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
                                                                height:'auto',
                                                                width:680,
                                                                caption: 'Line Items',

                                                                jsonReader : {
                                                                    root:"quoteitemdata",
                                                                    page: "page",
                                                                    total: "total",
                                                                    records: "records",
                                                                    cell: "dprow",
                                                                    id: "id"
                                                                },
                                                                onSelectRow: function(id){if(id && id!==lastsel){
                                                                        myGridPkd.restoreRow(lastsel);
                                                                        myGridPkd.editRow(id,editparametersitems);
                                                                        lastsel=id;
                                                                    }
                                                                },
                                                                editurl:'index.php/procurement/modifyQuoteItem'

                                                            }).navGrid("#pagerPk",{edit:false,add:false,del:true,search:false},{},{},{},{},{});
                                                            $("#lineItems").jqGrid('navButtonAdd','#pagerPk',{
                                                                caption:"", 
                                                                title:"Add Line Items",
                                                                buttonicon:"ui-icon-plus",
                                                                id:"add_lineItems",
                                                                onClickButton : function () { 
                                                                    //need to pass grid id for dynamic reload;
                                                                     $( "#dialog-form-item" ).data('grid_id','lineItems').dialog( "open" );
                                                                 } 
                                                             });
                                                             $("#del_lineItems").insertAfter("#add_lineItems");
                                                        }
                                                    }
                                                    else {
                                                    console.log("else");
                                                     $("#quoteId").val("");   
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
                                        url:'index.php/procurement/modifyQuote'
                                    })
                                        $("#quoteId").val("");
                                        $( this ).dialog( "close" );
                                    }
                                },//end buttons

                                close: function(event,ui) {
                                    //allFields.val( "" ).removeClass( "ui-state-error" );
                                    
                                    var noOfRec =myGridPkd.getGridParam("records");
                                    if (noOfRec<1){
                                            $.ajax({
                                            method:"POST",
                                            data:{id:$("#quoteId").val(),entity:'quote',items_count:noOfRec},
                                            url:'index.php/procurement/closeValidate'
                                        })
                                    }
                                    $("#quoteForm").data('validator').resetForm();
                                    $("#quoteForm")[0].reset();
                                    myGridPkd.jqGrid("GridUnload");
                                    $("pkdheader").hide();
                                    $("#doneBtn > span").text("Create Quote");
                                    $("#quotes").trigger("reloadGrid");
                                    
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
                           var grid = $(this).data('grid_id');
                                       
                           if (isvalid){
                               $.ajax({
                                   url:"index.php/procurement/addQuoteItem",
                                   type:"POST",
                                   data:{
                                       quoteid:$("#quoteId").val(),
                                       productid:$("#productOp").val(),
                                       needeedByDate:$("#neededdate").val(),
                                       quantity:$("#quantity").val(),
                                       exprice:$("#exPrice").val(),
                                       descItem:$("#descItem").val()
                                   },
                                   success:function (response){
//                                        $("#lineItems").trigger("reloadGrid");
                                       console.log("grid " + grid);
                                       $("#"+grid).trigger("reloadGrid");
                                   }
                               })
                               $( this ).dialog( "close" );
                           }
                    
                        },
                        Cancel: function() {
                           
                            $( this ).dialog( "close" );
                        }
                    },
                    close: function(event,ui) {
                        //allFields.val( "" ).removeClass( "ui-state-error" );
                        
                        $("#itemForm").data('validator').resetForm();
                        $('#itemForm')[0].reset();
                        $("#estValue").text("");
                        $("#curPrice").text("");
                    }
                });

        // Main Request For Quotation Grid                    
        
        var myGrid = $("#quotes"),lastsel2;
                var editparameters = {
                    "keys" : true,
                    "oneditfunc" : quotegriddates,
                    "successfunc" : function(){
                        lastsel2=undefined;
                        myGrid.trigger("reloadGrid");
                        return true;
                    },
                    "aftersavefunc" : null,
                    "errorfunc": function(){
                        lastsel2=undefined;
                        //myGrid.trigger("reloadGrid");
                       
                    },
                    "afterrestorefunc" : null,
                    "restoreAfterError" : true,
                    "mtype" : "POST"
                };
                
                myGrid.jqGrid({
                    url:'index.php/procurement/populateQuotes?_status='+'open',
                    datatype: 'json',
                    mtype: 'GET',
//                    postData:{status:'open'},
                    colNames:['Actions','Reference','Supplier','Estimated Value',/*'Owner',*/'Status','Raised By','Owner','Needed By Date'],
                    colModel :[ 
                        {name:'act',index:'act',width:70,search:false,align:'center',sortable:false,formatter:'actions',
                        formatoptions:{
                            keys: true, // we want use [Enter] key to save the row and [Esc] to cancel editing.
                            onEdit:quotegriddates,
                            onSuccess:function(jqXHR) {
                               myGrid.trigger("reloadGrid");
                                return true;
                            }
                                                                
                        }},
                        {name:'reference', index:'reference', width:80, align:'right',editable:false},
                        {name:'supplier_name', index:'supplier_name', width:140, align:'right',editable:true,edittype:"select",editoptions:{dataUrl:"index.php/procurement/populateSuppliers",buildSelect:function(response)
                        {
                            var select = "<select name=" + "mfrPkEdit" + "id =" +"mfrPkEdit" +">" +
                                        "<option value=" + ">Select one..." + response + "</select>";
                                    return select;
                                   
                        }}},
                        {name:'estimated_value', index:'estimated_value', width:100, align:'right',editable:false,editoptions:{size:"20",maxlength:"30"}},
//                        {name:'owner', index:'owner', width:140, align:'right',editable:true,editoptions:{size:"20",maxlength:"30"}},
                        {name:'status', index:'status', width:60, align:'right',editable:false,editoptions:{size:"20",maxlength:"30"}},
                        {name:'raised_by_name', index:'raised_by_name',editable:false, width:80, align:'right'},
                        {name:'owner_name', index:'owner_name',editable:false, width:80, align:'right'},
                        {name:'needed_by_date', index:'needed_by_date',editable:true, width:120, sorttype:'date'}
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
                    caption: 'Request For Quote',
            
                    jsonReader : {
                        root:"quotedata",
                        page: "page",
                        total: "total",
                        records: "records",
                        cell: "dprow",
                        id: "id"
                    },
                    editurl:'index.php/procurement/modifyQuote',
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
                            var editparametersitemssubgrid = {
                                "keys" : true,
                                "oneditfunc" : null,
                                "successfunc" : function(){
                                    lastsel=undefined;
                                    myGrid.trigger("reloadGrid");
                                    //$("#"+subgrid_id).trigger("reloadGrid");
                                    return true;
                                },
                                "aftersavefunc" : null,
                                "errorfunc": function(){
                                    lastsel=undefined;
                                    //myGrid.trigger("reloadGrid");

                                },
                                "afterrestorefunc" : null,
                                "restoreAfterError" : true,
                                "mtype" : "POST"
                            };
                            pager_id = "p_"+subgrid_table_id;
                            $("#quoteId").val(row_id);
                            $("#"+subgrid_id).html("<table id='"+subgrid_table_id+"' class='scroll'></table><div id='"+pager_id+"' class='scroll'></div>");
                            jQuery("#"+subgrid_table_id).jqGrid({
                                    url:'index.php/procurement/populateQuoteItems?q=2&quoteId='+row_id,
                                    datatype: 'json',
                                    colNames:['Product','Quantity','Need By Date','Unit Price','Estimated Value','Notes'],
                                    colModel :[ 
                                        {name:'name', index:'name',editable:false, width:120, align:'right'},
                                        {name:'quoted_quantity', index:'quoted_quantity', editable:true,width:50, align:'right'},
                                        {name:'needed_by_date',index:'needed_by_date',editable:true, width:80, align:'right'},
                                        {name:'expected_price',index:'expected_price',editable:true, width:50, align:'right'},
                                        {name:'estimated_value', index:'estimated_value',editable:false, width:60, align:'right'},
                                        {name:'comments', index:'comments',editable:true, width:180, align:'right'}

                                    ],
                                    rowNum:20,
                                    pager: pager_id,
                                    sortname: 'id',
                                    sortorder: "asc",
                                    height: '100%',
                                    
                                    jsonReader : {
                                        root:"quoteitemdata",
                                        page: "page",
                                        total: "total",
                                        records: "records",
                                        cell: "dprow",
                                        id: "id"
                                    },
                                    editurl:'index.php/procurement/modifyQuoteItem',
                                    onSelectRow: function(id){if(id && id!==lastsel){
                                            $("#"+subgrid_table_id).restoreRow(lastsel);
                                            $("#"+subgrid_table_id).editRow(id,editparametersitemssubgrid);
                                            lastsel=id;
                                        }
                                    }
                                });
                            jQuery("#"+subgrid_table_id).jqGrid('navGrid',"#"+pager_id,{edit:false,add:false,del:true,search:false});
                             jQuery("#"+subgrid_table_id).jqGrid('navButtonAdd',"#"+pager_id,{
                                                                caption:"", 
                                                                title:"Add Line Items",
                                                                buttonicon:"ui-icon-plus",
                                                                id:"add_lineItems",
                                                                onClickButton : function () { 
                                                                    //pass the grid id to dialog very important
                                                                     $( "#dialog-form-item" ).data('grid_id',subgrid_table_id).dialog( "open" );
                                                                 } 
                                                             });
                    },
                    subGridRowColapsed: function(subgrid_id, row_id) {
                            // this function is called before removing the data
                            //var subgrid_table_id;
                            //subgrid_table_id = subgrid_id+"_t";
                            //jQuery("#"+subgrid_table_id).remove();
                            $("#quoteId").val("");
                    }
                }).navGrid("#pager",{edit:false,add:false,del:true,search:false},{height:280,reloadAfterSubmit:false,closeAfterEdit:true,recreateForm:true,checkOnSubmit:true},{},{},{},{});
               
                myGrid.jqGrid('navButtonAdd','#pager',{
                   caption:"", 
                   title:"Create Product",
                   buttonicon:"ui-icon-plus",
                   id:"add_quotes",
                   onClickButton : function () { 
                        $( "#dialog-form" ).dialog( "open" );
                    } 
                });
                myGrid.jqGrid('navButtonAdd','#pager',{
                   caption:"", 
                   title:"Generate Purchase Order",
                   buttonicon:"ui-icon-cart",
                   id:"po_quotes",
                   onClickButton : function () { 
                       
                       var selectedRows = myGrid.jqGrid('getGridParam', 'selarrrow');
                       var noOfRows = selectedRows.length;
                       var error = false;
                       if (noOfRows == 0){
                           $( "#dialog-modal-warning" ).dialog("open");
                       }
                       else{
                           $.each(selectedRows, function(index,value){
                           
                           var val = myGrid.getCell(value,'estimated_value');
                           if (val ==0){
                               $("#refIds").text(myGrid.getCell(value,'reference')+" ")
                               error=true;
                           }
                           
                        })
                        if (error){
                             $( "#dialog-modal" ).dialog("open");
                        }
                        else{
                            var rowid = myGrid.jqGrid('getGridParam', 'selarrrow');
                             $.ajax({
                                 method:"POST",
                                 url:"index.php/procurement/generatePOFromQuote",
                                 data: {ids : rowid},
                                 success: function(){
                                     myGrid.trigger("reloadGrid");
                                     showSuccessMessage("success");
                                 }
                             })
                         }
                       }
                        
//                       
                    } 
                });
                 myGrid.jqGrid('navButtonAdd','#pager',{
                   caption:"", 
                   title:"Submit for Approval",
                   buttonicon:"ui-icon-flag",
                   id:"approve_quotes",
                   onClickButton : function () { 
                       var rowid = myGrid.jqGrid('getGridParam', 'selarrrow');
                        $.ajax({
                            method:"POST",
                            url:"index.php/procurement/submitForApproval",
                            data: {ids : rowid},
                            success: function(){
                                myGrid.trigger("reloadGrid");
                                showSuccessMessage("success");
                            }
                        })
                    } 
                });
                myGrid.jqGrid('filterToolbar', {stringResult: true, searchOnEnter: true, defaultSearch : "cn"});
                $("#del_quotes").insertAfter("#add_quotes");
                $( "#dialog-modal" ).dialog({
                                    autoOpen:false,
                                    height: 140,
                                    modal: true
                                });
                $( "#dialog-modal-warning" ).dialog({
                    autoOpen:false,
                    height: 80,
                    modal: true
                });
    
    
    });        

        </script>
        
    </head>
     
    <body>
        <?php  $this->load->view("common/menubar"); ?>
        <div id="dialog-modal" title="Error">
            <p>Reference Number(s) <span id="refIds" style="font-weight:bold;"></span> Does Not Have Estimated Value. Please Check The Quantity Or Price Of Line Items</p>
        </div>
         <div id="dialog-modal-warning" title="Warning">
            <p>Please Select At Least A Row</p>
        </div>
        <div id ="dialog-form">
            <h1 id="formHeader">Create Request For Quotation</h1>   
            
            <form id="quoteForm">
                <fieldset>
                    
                    <div class="row">
                        
                        <div class="column">
                            <div class="field">
                                <label for="supplierOp">Supplier:</label>  
                                <select name="supplierOp" id ="supplierOp" class="required"> 
                                    <option value="">Choose 
                                        <?= $supplierOptions ?> 
                                </select>
                                
                            </div>
                        </div>
                        <div class="column">
                            <div class="field">
                                <label for="warehouseOp">Warehouse:</label>  
                                <select name="warehouseOp" id ="warehouseOp"> 
                                    <option value="">Choose 
                                        <?= $warehouseOptions ?> 
                                </select>
                                
                            </div>
                            
                        </div>
                        <div class="column" style="width:35em;">
                            <div class="field">
                                <label for="reqdate">Request By Date:</label>  
                                <input id="reqdate" name ="reqdate" type="text" class="dateValidate"/>
                            </div>
                        </div>
                        
                    </div>
                    <div class="row">
                        <div class="column extra-wide">
                            <div class="field">
                                <label for="desc">Notes:</label>  
                                <textarea id="desc" name ="desc" rows="4" cols="120"></textarea>
                            </div>
                        </div>
                        
                    </div>
                    <input id="quoteId" name ="quoteId" type="hidden" value=""/>
                </fieldset>
            </form>
            <div class="table-grid">
                <h1 id="pkdheader" style="display:none">Add Line Items</h1>
                <table id="lineItems"><tr><td/></tr></table> 
                <div id="pagerPk"></div>
            </div>
        </div>
        <div id ="dialog-form-item">
            <h1 id="formHeader">Add New Line Item</h1>   
            <form id="itemForm">
                <fieldset>
                    <div class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="productOp">Products:</label>  
                                <select name="productOp" id ="productOp" class="required"> 
                                    <option value="">Choose 
                                        <?= $productOptions ?> 
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row single-column-row">
                        <div class="column quote-column single-column">
                           <div class="field">
                                <label for="neededdate">Needed By Date:</label>  
                                <input id="neededdate" name ="neededdate" type="text"/>
                            </div>
                        </div>
                    </div>
                    <div class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="quantity">Quantity:</label>  
                                <input id="quantity" name="quantity" class="calinput"/>
                            </div>
                        </div>                        
                    </div>
                    <div class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="exPrice">Expected Price:</label>  
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
                                <label for="estValue">Estimated Value:</label>  
                                <label id="estValue" name="estValue" class="calculated"/>
                                
                            </div>
                        </div>                        
                    </div>
                    <div class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="descItem">Notes:</label>  
                                <input id="descItem" name="descItem" size="40" maxlength="60"/>
                            </div>
                        </div>
                        
                    </div>
                    
                </fieldset>
            </form>
        </div>
        
        <div style="display: block;height: 100%;" class="shopifine-ui-dialog ui-widget ui-widget-content ui-corner-all" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-dialog-form">
            <?php $this->load->view("common/message"); ?>
            <div class="table-grid">
                <h1 id="table header">Request1 For Quotations</h1>
                <table id="quotes"><tr><td/></tr></table> 
                <div id="pager"></div>
            </div>
            
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


    
   