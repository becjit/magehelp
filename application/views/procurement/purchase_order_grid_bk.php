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
            
        </style>
       
        <script type="text/javascript">
                $(function() {
        
        //form validation 
        
         $("#quoteForm").validate();
         $("#itemForm").validate();
         
        // datepicker for Add Form 
        
        function quotegriddates(id){
            jQuery("#"+id+"_needed_by_date","#orders").datepicker({dateFormat:"yy-mm-dd",minDate:0});
        }
       
        //end datepicker
         
        var myGridPkd = $("#lineItems");
              
       $( "#dialog-form" ).dialog({
                                autoOpen: false,
                                height: 'auto',
                                width: '52%',
                                modal: true,
                                buttons: {
                                    "DoneButton": {
                                        id:"doneBtn",
                                        text:"Create Quote",
                                        click:function() {
                                        var isValid = $("#quoteForm").valid();
                                        if (isValid){
                                           $.ajax({url:"index.php/procurement/createPO",
                                                type:"POST",
                                                data:{  orderId:$("#orderId").val(),
                                                        supplierId:$("#supplierOp").val(),
                                                        warehouseId:$("#warehouseOp").val(),
                                                        reqdate:$("#reqdate").val(),
                                                        notes:$("#desc").val()
                                                        
                                                    },

                                                success:function(response)
                                                {}
                                                 
                                            }) //end ajax
                                            if ($("#orderId").val()!=""){
                                                $( this ).dialog( "close" );
                                            } 
 
                                        } //end ifvalid
                                        
                                    }}, //end of Create button
                                    Cancel: function() {
                                        $("#orderId").val("");
                                        $( this ).dialog( "close" );
                                    }
                                },//end buttons
                                open: function(){
                                     $('#tabs-movie').tabs({
            create: function(e, ui) {
                $('#closeBtn').click(function() {
                    $('#dialog-form').dialog('close');
                });
            }
        });
        $(this).parent().children('.ui-dialog-titlebar').remove(); 
                                     
//                                    $.ajax({
//                                       method:"POST",
//                                       url:'index.php/procurement/getOrderDetails',
//                                       data:{orderId: myGrid.getGridParam('selrow')},
//                                       success:function(response){
//                                           console.log(response);
//                                           var resJson = JSON.parse(response);
//                                           console.log(resJson);
//                                           
//                                           $("#supplierOP").text(resJson.quote_supplier_name);
//                                           $("#warehouseOP").text(resJson.quote_warehouse);
//                                           $("#quoteOP").text(resJson.quote_reference);
//                                           $("#raisedByOP").text(resJson.quote_raised_by_name);
//                                           $("#approvedByOP").text(resJson.quote_approved_by_name);
//                                           $("#estValueOP").text(resJson.quote_estimated_value);
//                                       }
//                                    })
//                                    $("#lineItems").jqGrid({
//                                        url:'index.php/procurement/populateOrderItems',
//                                        datatype: 'json',
//                                        mtype: 'POST',
//                                        postData:{orderId: myGrid.getGridParam('selrow')},
//                                        colNames:['Product','Quantity','Need By Date','Expected Price','Estimated Value',/*'Notes'*/],
//                                        colModel :[ 
//                                            {name:'name', index:'name',editable:false, width:80, align:'right'},
//                                            {name:'quoted_quantity', index:'quoted_quantity', editable:true,width:140, align:'right'},
//                                            {name:'needed_by_date',index:'needed_by_date',editable:true, width:80, align:'right'},
//                                            {name:'expected_price',index:'expected_price',editable:true, width:80, align:'right'},
//                                            {name:'estimated_value', index:'estimated_value',editable:false, width:80, align:'right'},
////                                            {name:'comments', index:'comments',editable:true, width:180, align:'right'}
//
//                                        ],
//                                        pager: '#pagerPk',
//                                        rowNum:10,
//                                        rowList:[5,10,20],
//                                        sortname: 'id',
//                                        sortorder: 'desc',
//                                        viewrecords: true,
//                                        gridview: true,
//                                        ignoreCase:true,
//                                        rownumbers:true,
//                                        height:'auto',
//                                        width:'50%',
//                                        caption: 'Line Items',
//
//                                        jsonReader : {
//                                            root:"quoteitemdata",
//                                            page: "page",
//                                            total: "total",
//                                            records: "records",
//                                            cell: "dprow",
//                                            id: "id"
//                                        }
//
//                                    }).navGrid("#pagerPk",{edit:false,add:false,del:true,search:false},{},{},{},{},{});
                                                            
                                },

                                close: function() {
                                    //allFields.val( "" ).removeClass( "ui-state-error" );
                                    $("#quoteForm").data('validator').resetForm();
                                    $("#quoteForm")[0].reset();
                                    myGridPkd.jqGrid("GridUnload");
                                    $("pkdheader").hide();
                                    $("#doneBtn > span").text("Create Quote");
                                    $("#orders").trigger("reloadGrid");
                                    
                                }
                            });
        
       
        // Main Request For Quotation Grid                    
        
        var myGrid = $("#orders"),lastsel2;
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
                    url:'index.php/procurement/populatePO',
                    datatype: 'json',
                    mtype: 'GET',
                    colNames:['Reference','Supplier','Estimated Value',/*'Owner',*/'Status','Raised By',/*'Owner',*/'Needed By Date'],
                    colModel :[ 
                        {name:'reference', index:'reference', width:80, align:'right',editable:false},
                        {name:'quote_supplier_name', index:'supplier_name', width:140, align:'right',editable:false},
                        {name:'quote_estimated_value', index:'estimated_value', width:100, align:'right',editable:false,editoptions:{size:"20",maxlength:"30"}},
//                        {name:'owner', index:'owner', width:140, align:'right',editable:true,editoptions:{size:"20",maxlength:"30"}},
                        {name:'status', index:'status', width:60, align:'right',editable:false,editoptions:{size:"20",maxlength:"30"}},
                        {name:'quote_raised_by_name', index:'raised_by_name',editable:false, width:80, align:'right'},
//                        {name:'owner_name', index:'owner_name',editable:false, width:80, align:'right'},
                        {name:'quote_needed_by_date', index:'needed_by_date',editable:false, width:120, sorttype:'date'}
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
                    onSelectRow: function(id){if(id && id!==lastsel2){
                            myGrid.restoreRow(lastsel2);
                            myGrid.editRow(id,editparameters);
                            lastsel2=id;
                        }
                    }
                    ,editurl:'index.php/procurement/modifyOrder'
                    ,subGrid:true,
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
                                    url:'index.php/procurement/populateOrderItems?q=2&orderId='+row_id,
                                    datatype: 'json',
                                    colNames:['Product','Quantity','Need By Date','Unit Price','Estimated Value','Notes'],
                                    colModel :[ 
                                        {name:'name', index:'name',editable:false, width:120, align:'right'},
                                        {name:'quoted_quantity', index:'quoted_quantity', editable:false,width:50, align:'right'},
                                        {name:'needed_by_date',index:'needed_by_date',editable:false, width:80, align:'right'},
                                        {name:'expected_price',index:'expected_price',editable:false, width:50, align:'right'},
                                        {name:'estimated_value', index:'estimated_value',editable:false, width:60, align:'right'},
                                        {name:'comments', index:'comments',editable:false, width:180, align:'right'}

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
                                    }
                                });
                            jQuery("#"+subgrid_table_id).jqGrid('navGrid',"#"+pager_id,{edit:false,add:false,del:false,search:false,view:true});
                             
                    }
                }).navGrid("#pager",{edit:false,add:false,view:true,del:true,search:false},{height:280,reloadAfterSubmit:false,closeAfterEdit:true,recreateForm:true,checkOnSubmit:true},{},{},{},{});
               
                myGrid.jqGrid('navButtonAdd','#pager',{
                   caption:"", 
                   title:"Create Product",
                   buttonicon:"ui-icon-plus",
                   id:"add_orders",
                   onClickButton : function () { 
                        $( "#dialog-form" ).dialog( "open" );
                    } 
                });
                myGrid.jqGrid('navButtonAdd','#pager',{
                   caption:"", 
                   title:"Generate Purchase Order",
                   buttonicon:"ui-icon-cart",
                   id:"po_orders",
                   onClickButton : function () { 
                        $( "#dialog-form" ).dialog( "open" );
                    } 
                });
                myGrid.jqGrid('filterToolbar', {stringResult: true, searchOnEnter: true, defaultSearch : "cn"});
                $("#del_orders").insertAfter("#add_orders");
                
                
    
    
    });        

        </script>
        
    </head>
     
    <body>
        <?php  $this->load->view("common/menubar"); ?>
        <div id ="dialog-form">
            <h1 id="formHeader">Purchase Order Details</h1>  
                    <div id="tabs-movie">
            <ul>
              <li><a href="form.html">Information</a></li>
              <li><a href="notes.html">Cast List</a></li>
              <li class="ui-tabs-close-button"><button id="closeBtn">X</button></li>
            </ul>
            
            
          </div>
          
        </div>
        
        <div style="display: block;height: 100%;" class="shopifine-ui-dialog ui-widget ui-widget-content ui-corner-all" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-dialog-form">
            <div class="table-grid">
                <h1 id="table header">Request For Quotations</h1>
                <table id="orders"><tr><td/></tr></table> 
                <div id="pager"></div>
            </div>
            
        </div>
        
        <?php $this->load->view("partial/footer"); ?>       
</body>   
</html>



    
   