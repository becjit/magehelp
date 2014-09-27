<html>
    <head>
       <?php $this->load->view("common/header"); ?>
         <link rel="stylesheet" rev="stylesheet" href="<?php echo base_url();?>css/chosen.css" />
        <script src="<?php echo base_url();?>js/chosen.jquery.min.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
        <style>
            .ui-combobox > input{
                width:20em;
            }
            
            .ui-combobox > a{
                left:170px;
            }
            .column {width:400px;}
            .field {width:45em;}
        </style>
         <script>
            $(document).ready(function(){
                
                
                

                $( "#productForm" ).validate({
                    errorPlacement: function(error, element) {
                        error.appendTo( element.parent());
                    }//,
                    //            rules: {
                    //                        mfrOp: {
                    //                            comboBoxrequired: true
                    //                        }
                    //                    }
            
                }
            );   
                
                
                 $( "#dialog:ui-dialog" ).dialog( "destroy" );
                 
                  $("#productOp").combobox();  
                  
                  
                 
                $( "#dialog-form" ).dialog({
                    autoOpen: false,
                    height: 'auto',
                    width: '40%',
                    position:[350,25],
                    modal: true,
                    buttons: {
                        "Add Supplier To Products": function() {
                           var bValid =  $("#productForm").valid();
                    
                            if (bValid ){
//                                var selectedIds = [];
//                                $('#treeViewDiv').jstree('get_selected').each(function(){
//                                    selectedIds.push($(this).attr('id'));
//                                });
                        
                                $.ajax({
                                    url:"index.php/products/addSupplierToProduct",
                                    data: {
                                            
                                            productOp : $("#productOp").val(),
                                            productIp: $("#productOp-input").val(),
                                            supplier: $("#supplierOp").val()
                                            },
                                
                                    type:"POST",
                                    success:function(serverresponse)
                                    {
                                        showSuccessMessage("Product Suceessfully Added To category");
                                        $("#resources").trigger("reloadGrid");
                                    }
                                });
                        
                                $( this ).dialog( "close" );
                            }
                    
                        },
                        Cancel: function() {
                            $( this ).dialog( "close" );
                        }
                    },
                    close: function() {
                        $("#productForm").data('validator').resetForm();
                        $('#productForm')[0].reset();
                        $("#supplierOp").val("").trigger('liszt:updated');;
                    }
                });
                

                var myGrid = $("#resources");
               
                myGrid.jqGrid({
                    url:'index.php/products/populateProductsSuppliersGrid',
                    datatype: 'json',
                    mtype: 'GET',
                    colNames:['Product','Barcode','Product ID','Supplier ID','Supplier Name'],
                    colModel :[ 
                        {name:'product_name', index:'product_name', width:140, align:'right',editable:false},
                        {name:'barcode', index:'barcode', width:140, align:'right',editable:false},
                        {name:'product_id', index:'product_id', width:100, align:'right',hidden:true},
                        {name:'supplier_id', index:'category_id', width:100, align:'right',hidden:true},
                        {name:'supplier_name', index:'category_name', width:140, align:'right',editable:false},
                        
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
                    width:'auto',
                    multiselect:true,
                    caption: 'Product Supplier Mapping',
            
                    jsonReader : {
                        root:"productsupplierdata",
                        page: "page",
                        total: "total",
                        records: "records",
                        cell: "dprow",
                        id: "id"
                    }
                   
                    
                }).navGrid("#pager",{edit:false,add:false,del:false,search:false},
               /* edit Option*/ {},
            /* Add Option*/     {},
                                                 {},{},{});
               
                myGrid.jqGrid('filterToolbar', {stringResult: true, searchOnEnter: true, defaultSearch : "cn"});
                var settingsObj ={grid_id:'resources',pager:'pager'};
                var buttons = {add_sup:true,del_sup:true};
                addCustomButtonsInProductsGrid(settingsObj, buttons);
                $("#add_resources").insertBefore("#del_resources");
                $(".chzn-select").chosen();
                 
                 
            });
            $(window).load(function(){
       
                var warningDialogs={one:true,none:true,status:true,exactlyone:true,morethanone:true};
                initDialogs(warningDialogs);
                initDeleteDialog();
                
            });
        </script>
    </head>
     
    <body>
         <?php $this->load->view("common/menubar"); ?>
        <?php  $this->load->view("common/dialogs"); ?>
        <div id ="dialog-form" style="overflow: hidden;">
            <h1 id="formHeader">Add Supplier</h1>   
            <form id="productForm">
                <fieldset>
                    
                    <div class="row">
                        <div class="column">
                            <div class="field">
                                <label for="productOp" class="labeldiv">Products:</label>  
                                <select name="productOp" id ="productOp" class="required"> 
                                    <option value="">Choose 
                                        <?= $productOptions ?> 
                                </select>
                                <!--<button id="test"> test</button>-->
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                         <div class="column">
                            <div class="field">
                                <label for="supplierOp" class="labeldiv">Supplier:</label>  
                                <select name="supplierOp[]" id ="supplierOp" class="chzn-select" multiple="multiple" style="width:15em;;height:1em;"> 
                                        <?= $supplierOptions ?> 
                                </select>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>
        
        <div style="display: block;height: 100%;left:0em; overflow: hidden;" class="shopifine-ui-dialog  ui-widget ui-widget-content ui-corner-all" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-dialog-form">
            <?php $this->load->view("common/message"); ?>
            <div class="table-grid">
                <h1 id="table header">Products And Suppliers</h1>
                <table id="resources"><tr><td/></tr></table> 
                <div id="pager"></div>
            </div>
        </div>
        <?php $this->load->view("partial/footer"); ?>
    </body>
</html>
