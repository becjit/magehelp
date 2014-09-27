<!DOCTYPE html>
<html>
    <head>
        <title>Product Details</title>
        <?php $this->load->view("common/header"); ?>
<!--       <script type="text/javascript" src="http://static.jstree.com/v.1.0pre/jquery.jstree.js"></script>-->
        <link rel="stylesheet" rev="stylesheet" href="<?php echo base_url();?>css/chosen.css" />
        <script src="<?php echo base_url();?>js/chosen.jquery.min.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
        <script src="<?php echo base_url();?>js/jquery.jstree.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
        <style type="text/css">
            #menubar_admin_navigation {
                top:49px;
            }
            
            #content_area{
                width:95%;
            }
            .column{width:40em;padding:1em;}
            html {
                overflow-x:hidden;
            }
            .chzn-container{
                font-size:inherit;
            }
            .chzn-container-multi .chzn-choices .search-field input {
                height:1em;
            }
            .field{
                width:100%;
            }
            .labeldiv{
                width:25%;
            }
            .ui-combobox{
                width:40%;
            }
            
            label.error{
                width:30%
            }
        </style>
        <script type="text/javascript">
            $(function(){
                $.validator.addMethod('comboBoxrequired', function(value, element,param) {
                    var selectId = param;
                    value = $("#"+selectId).val();
                    var inputIdSelector = "#" + selectId + "-input";
                    if (value == ""){
                        inputVal = $(inputIdSelector).val();
                        if (inputVal == "" || inputVal == null){
                            return false;
                        }
                    }
                    return true;
                }, 'Please select from the dropdown or add new element in box');
                
                
                $( "#dialog:ui-dialog" ).dialog( "destroy" );

                $( "#productForm" ).validate({
//                   ignore:[],
                    
                    errorPlacement: function(error, element) {
                       console.log(element[0].id);
                        if (element[0].id=='reqBarcodeMfr'){
                            error.appendTo( $("#mfrOp").parent());
                        }
                        else if (element[0].id=='reqBarcodeModel'){
                            error.appendTo( $("#modelOp").parent());
                        }
                        else{
                            error.appendTo( element.parent());
                        }
                        
                    },
                    rules :{reqBarcodeMfr:{
                           comboBoxrequired:'mfrOp' 
                    },
                    reqBarcodeModel:{
                           comboBoxrequired:'modelOp' 
                    },
                    reorderLevel:{
                     
                     digits:true
                     //min:1//,
                     //threshold:'#pp_quantity_receipt'
                 },
                 vat:{                  
                     
                     number:true
                    // minStrict:0//,
                     //threshold:'#pp_amount_receipt'
                 },
                 margin_value:{                  
                     
                     number:true
                    // minStrict:0//,
                     //threshold:'#pp_amount_receipt'
                 }
                }
                }
            );   
            $( "#dialog-form" ).dialog({
                autoOpen: false,
                height: 'auto',
                width: '70%',
                position:[200,25],
                modal: true,
                buttons: {
                    "Create the Product": {
                        id:"create_product",
                        text:"Create the Product",
                        click:function() {
                        
                            var bValid =  $("#productForm").valid();
                            var mode = $(this).data('grid_data').mode;
                            var product_id = $(this).data('grid_data').product_id;
                            var url = "index.php/products/createBarcodeAndProduct";
                            if (mode=='edit'){
                                url = 'index.php/products/editProduct';
                            }
                            console.log(bValid);

                             if (bValid ){
                                 var selectedIds = [];
                                 $('#treeViewDiv').jstree('get_selected').each(function(){
                                     selectedIds.push($(this).attr('id'));
                                 });
                                 var checked =  $('#hasBarcode').is(':checked');
                                 var barcode = $("#productbarcode").val();
                                 if (checked){
                                     barcode="";
                                 }
                                 $.ajax({
                                     url:url,
                                     data: {barcodeData :{
                                             scannedBarcode : barcode,
                                             mfrOp : $("#mfrOp").val(),
                                             mfrIp: $("#mfrOp-input").val(),
                                             modelOp: $("#modelOp").val(),
                                             modelIp: $("#modelOp-input").val(),
                                             newModelIp: $("#newModelIp").val(),
                                             name: $("#name").val(),
                                             desc: $("#desc").val(),
                                             category:JSON.stringify(selectedIds),
                                             supplierOp: $("#supplierOp").val(),
                                             uomOp: $("#uomOp").val(),
                                             sizeOp: $("#sizeOp").val(),
                                             
     //                                        costPrice: $("#costPrice").val(),
     //                                        price: $("#price").val(),
                                             attributeSet:$("#attributeSetOp").val(),
                                             packageOp: $("#packageOp").val(),override:$("#createOverride").val() },
                                         editData:
                                             {id:product_id,
                                             reorderLevel: $("#reorderLevel").val(),
                                             vat: $("#vat").val(),
                                             name: $("#name").val(),
                                             desc: $("#desc").val(),
                                             metadesc: $("#metadesc").val(),
                                             attributeSet:$("#attributeSetOp").val(),
                                             margin_type:$("#margin_type").val(),
                                             margin_value:$("#margin_value").val()
                                         }
                                     },

                                     type:"POST",
                                     success:function(serverresponse)
                                     {
                                         var response = JSON.parse(serverresponse);
                                         emptyMessages();
                                         if (response.status=='error'){
                                             
                                             //showErrorMessage(response.message)
                                              $("#status_warning_text").text(response.message);
                                             $( "#modal-warning-general" ).dialog("open");
                                         }
                                         else if (response.status=='exists'){
                                             emptyMessagesGen('status-message-product')
                                             showErrorMessageGen(response.message, 'status-message-product');
                                             $("#possibleProductCntnr").show();
                                             $("#create_product >span").text("Still Create A New Product")
                                             $("#createOverride").val("1");
                                             var settingsObjMatching= {grid_id:'matchingproduct',pager:'pagerMatchingProduct'};
                                             var postdata = {
                                                     mfr_id:$("#mfrOp").val(),
                                                     model_id:$("#modelOp").val(),
                                                     uom:$("#uomOp").val(),
                                                     size:$("#sizeOp").val(),
                                                     package_id:$("#packageOp").val(),
                                                     product_name:$("#name").val()
                                                 };
                                             prepareProductsGrid(settingsObjMatching,postdata,false,{},{description:true,uom:true,measurement_denomination:true,reorder:true,vat:true,generate:true,uomdenom:false});
//                                             $("#matchingproduct").jqGrid('setGridParam',{});
//                                              $("#matchingproduct").trigger("reloadGrid")
                                             //$("#status_warning_text").text(response.message);
                                             //$( "#modal-warning-general" ).dialog("open");
//                                             $( this ).dialog( "close" );
                                         }
                                         else  {
                                             $( "#dialog-form" ).dialog( "close" );
                                             showSuccessMessage(response.message)
                                             $("#product").trigger("reloadGrid");
                                         }
                                          
                                     }
                                 });

                                 
                             }

                         }
                    }
                        ,
                        
                    Cancel: function() {
                        $( this ).dialog( "close" );
                    }
                },
                close: function() {
                    //allFields.val( "" ).removeClass( "ui-state-error" );
                    $("#productbarcode").attr('disabled',false);
                    $("#productForm").data('validator').resetForm();
                    $('#productForm')[0].reset();
                    $("#supplierOp").val("").trigger('liszt:updated');;
                    $('#attributeSetOp').val("");
                    $('.inithide').hide();
                    $("#productbarcode").removeProp("readonly");
                    $('.noedit').show();
                    $("#createOverride").val("0");
                    $("#create_product >span").text("Create A New Product");
                    $("#matchingproduct").jqGrid("GridDestroy");
                    
                },
                open :function(){
                    $('.inithide').hide();
                    var product_id = $(this).data('grid_data').product_id;
                    var barcode = myGrid.getCell(product_id,'barcode');
                    var mode = $(this).data('grid_data').mode;
                    console.log("mode = "+ mode + " product_id = " + product_id);
                    if (mode=='edit'){
                        $('.noedit').hide();
                        $("#create_product >span").text("Edit Product");
                        $('.onlyedit').show();
                        $("#productbarcode").val(barcode);
                        $("#productbarcode").prop("readonly","readonly");
                        $("#name").val(myGrid.getCell(product_id,'product_name'));
                        $("#desc").val(myGrid.getCell(product_id,'description'));
                        $("#vat").val(myGrid.getCell(product_id,'vat'));
                        $("#reorderLevel").val(myGrid.getCell(product_id,'reorder'));
                        $("#margin_type").val(myGrid.getCell(product_id,'margin_type'));
                        $("#margin_value").val(myGrid.getCell(product_id,'margin_value'));
                        var optText = myGrid.getCell(product_id,'attributeset');
                         console.log("optText == " + optText);
                        $("#attributeSetOp option").filter(function() {
                        //may want to use $.trim in here
                        return $.trim($(this).text().toLowerCase())== $.trim(optText).toLowerCase(); 
                        }).attr('selected', true);
                    }
                    else {
                        
                    }
                }
            });

          $("#mfrOp").combobox({
                customChange: function () {
                    if ($("#mfrOp").val()!="") {
                        $.ajax({
                            type:"post",
                            data:{mfrName:$("#mfrOp-input").val(),mfr: $("#mfrOp").val()},
                            url:"index.php/products/populateModel",
                            success: function(data){
                                $("#modelOp").children('option:not(:first)').remove();
                                $("#modelOp").append(data); 
                                $("#modelOp").combobox(); 
                                $("#modelCtnr").parent().slideDown(50);
                                if (!$("#newModelCtnr").parent().is(':hidden')){
                                    $("#newModelCtnr").parent().slideUp(50);
                                }
                            }
                        }); 
                    }
                    else {
                        $("#newModelCtnr").parent().slideDown(50);
                        if (!$("#modelCtnr").parent().is(':hidden')){
                            $("#modelCtnr").parent().slideUp(50);
                        }
                    }
                }
            });
   
            $("#categoryOp").combobox();  

            var myGrid = $("#product");
            var settingsObj= {grid_id:'product',pager:'pager',multiselect:true};
            prepareProductsGrid(settingsObj);
            myGrid .navGrid("#pager",{edit:false,add:false,del:false,search:false},{},{},{},{},{});
            var buttons = {add:true,edit:true,load_all:true,activate:true,deactivate:true,export_all:true};
            addCustomButtonsInProductsGrid(settingsObj, buttons);

//        myGrid.jqGrid('navButtonAdd','#pager',{
//           caption:"", 
//           title:"Export as csv",
//           id:"export_product",
//           onClickButton : function () { 
//               myGrid.jqGrid('excelExport',{tag:"csv","url":"index.php/products/exportProductsInGrid"});
//           } 
//        });
    
//        myGrid.jqGrid('navButtonAdd','#pager',{
//           caption:"", 
//           title:"Create Product",
//           buttonicon:"ui-icon-plus",
//           id:"add_product",
//           onClickButton : function () {       
//                $("#newModelCtnr").parent().css("display","none");
//                $("#modelCtnr").parent().css("display","none");
//                var grid_data = {mode:'add'};
//                $( "#dialog-form" ).data('grid_data',grid_data).dialog( "open" );
//                } 
//        });
//        myGrid.jqGrid('navButtonAdd','#pager',{
//           caption:"", 
//           title:"Edit Product",
//           buttonicon:"ui-icon-pencil",
//           id:"edit_product",
//           onClickButton : function () {       
//                $("#newModelCtnr").parent().css("display","none");
//                $("#modelCtnr").parent().css("display","none");
//                var grid_data = {product_id:myGrid.getGridParam('selrow'),mode:'edit'};
//                $( "#dialog-form" ).data('grid_data',grid_data).dialog( "open" );
//                } 
//        });
//
//        myGrid.jqGrid('navButtonAdd','#pager',{
//            caption:"",
//            title:"Mark as inactive",
//            id:"inactive_product",
//            buttonicon:"ui-icon-locked",
//            onClickButton : function (id) { 
//                    var rowid = myGrid.jqGrid('getGridParam', 'selarrrow');
//                    $.ajax({type:"post",
//                        url:"index.php/products/deactivate",
//                        data: {ids : rowid},
//                        success: function(){
//                            $("#product").trigger("reloadGrid");
//                        }
//                    }); 
//            } 
//        });
//
//        myGrid.jqGrid('navButtonAdd','#pager',{
//            caption:"",
//            title:"Mark as active",
//            id:"active_product",
//            buttonicon:"ui-icon-unlocked",
//            onClickButton : function (id) { 
//                var rowid = myGrid.jqGrid('getGridParam', 'selarrrow');
//                $.ajax({type:"post",
//                    url:"index.php/products/activate",
//                    data: {ids : rowid},
//                    success: function(){
//                        $("#product").trigger("reloadGrid");
//                    }
//                }); 
//            } 
//        });
//
//        myGrid.jqGrid('navButtonAdd','#pager',{
//            caption:"",
//            title:"Load all ",
//            id:"load_product",
//            buttonicon:"ui-icon-arrow-4-diag",
//            onClickButton : function (id) { 
//                var rowid = myGrid.jqGrid('getGridParam', 'selarrrow');
//                $.ajax({type:"post",
//                    url:"index.php/products/populateProductsInGrid",
//                    data: {loadall : true},
//                    success: function(response){
//                        //$("#product").trigger("reloadGrid");
//                        var grid = jQuery("#product")[0];
//                        var myjsongrid = eval("("+response+")"); 
//                        grid.addJSONData(myjsongrid); 
//
//
//                    }
//                }); 
//            } 
//        });
//
//        myGrid.jqGrid ('navButtonAdd', '#pager',
//                 { caption: "", buttonicon: "ui-icon-calculator",
//                   title: "Choose Columns",
//                   onClickButton: function() {
//                        myGrid.jqGrid('columnChooser');
//                   }
//                 });

        $("#add_product").insertBefore("#edit_product");
        myGrid.jqGrid('filterToolbar', {stringResult: true, searchOnEnter: true, defaultSearch : "cn"});
        $(".chzn-select").chosen();
        
        

         $("#treeViewDiv")
                     .jstree({
            "plugins" : ["themes", "json_data", "ui"],
            "json_data" : {
                "ajax" : {
                    "type": 'GET',
                    "url": function (node) {
                        var nodeId = "";
                        var url = ""
                        if (node == -1)
                        {
                            url = "index.php/utilities/renderParents";
                        }
                        else
                        {
                            nodeId = node.attr('id');
                            url = "index.php/utilities/renderChildren";
                        }

                        return url;
                    },
                    data : function(node) {
                        if (node != -1){
                            return {

                              "nodeid":$.trim(node.attr('id'))
                            }
                        }
                },
                    "success": function (new_data) {
                        return new_data;
                    }
                }
            }})
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
        <?php $this->load->view("common/menubar"); ?>
        <?php  $this->load->view("common/dialogs"); ?>
<!--        <button id="create-product">Create New Product</button> -->
        <!--<button id="inv-management" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"><span class="ui-button-text"> Manage Inventory</span></button>-->
        <div id ="dialog-form" title="Add New Product Entity">
<!--            <h1 id="formHeader">Add New Product Entity</h1>   -->
            <form id="productForm">
                <fieldset>
                    <div class="row ">
                        <div class="column noedit">
                            <div class="field">
                                <label for="hasBarcode" class="labeldiv">Product Do Not Have Barcode</label> 
                                <input id="hasBarcode" name ="hasBarcode" type="checkbox"/>
                            </div>

                        </div>
                        <div class="column">
                            <div class="field">
                                <label for="productbarcode" class="labeldiv">Scan/ Type Barcode:</label>  
                                <input id="productbarcode" name ="productbarcode" type="text" class="required"/>
                                
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="column">
                            <div class="field">
                                <label for="name" class="labeldiv">Name of Product Entity:</label>  
                                <input id="name" name ="name" type="text" class="required"/>
                            </div>
                        </div>
                        <div class="column">
                            <div class="field">
                                <label for="attributeSetOp" class="labeldiv">Attribute Set:</label>  
                                <select name="attributeSetOp" id ="attributeSetOp" class="required"> 
                                    <option value="">Choose 
                                        <?= $attributeSetOptions ?> 
                                </select>
                                
                            </div>
                        </div>
                        
                    </div>
                    <div class="row inithide onlyedit">
                        <div class="column">
                            <div class="field">
                                <label for="desc" class="labeldiv">Description:</label>  
                                <textarea id="desc" name ="desc" rows="3" cols="30" class="required"></textarea>
                            </div>
                        </div>
                        <div class="column">
                            <div class="field">
                                <label for="metadesc" class="labeldiv">Meta Description:</label>  
                                <textarea id="metadesc" name ="metadesc" rows="3" cols="30"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    

                    <div class="row noedit">
                        <div class="column">
                            <div id="mfrCtnr">
                                <div class="ui-widget field">
                                    <label for="mfrOp" class="labeldiv">Manufacturer :</label> 
                                    <select name="mfrOp" id ="mfrOp" > 
                                        <option value="">Select one...
                                            <?= $mfrOptions?> 
                                    </select>
                                    
                                </div>  
                            </div>
                            <div class="row" style="padding-top:5px;">
                                <input id="reqBarcodeMfr" name ="reqBarcodeMfr" type="checkbox" class="reqBarcode reqBarcodeAC" style="left:26%"/>
                                <div class="help-message">Caution:Mark As Not Required Only In Exception Circumstances</div>
                            </div>

                        </div>
                        <div class="column" style="display:none">
                            <div id="modelCtnr">
                                <div class="ui-widget field">
                                    <label for="modelOp" class="labeldiv">Model :</label>  
                                    <select name="modelOp" id ="modelOp"> 
                                        <option value="">Select one...
                                            <?= $modelOptions?> 
                                    </select>
                                </div>
                            </div>
                            <div class="row" style="padding-top:5px;">
                                <input id="reqBarcodeModel" name ="reqBarcodeModel" type="checkbox" class="reqBarcode reqBarcodeAC" style="left:26%"/>
                                <div class="help-message">Caution:Mark As Not Required Only In Exception Circumstances</div>
                            </div>
                        </div>
                        <div class="column" style="display:none">
                            <div id="newModelCtnr">
                                <div class="field">
                                    <label for="newModelIp" class="labeldiv">Specify New Model Type :</label>  
                                    <input id="newModelIp" name ="newModelIp" type="text"/>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row noedit">
                        <div class="column">
                            <div class="field">
                                <label for="categoryOp" class="labeldiv">Category:</label>  
<!--                                <select name="categoryOp" id ="categoryOp" > 
                                    <option value="">Choose 
                                        <?= $categoryOptions ?> 
                                </select>-->
                                <div id="treeViewDiv" style="display:inline-block">
                                </div>
                                <!--<input id="categoryReq" name ="reqBarcodeCat" type="checkbox" class="reqBarcodeAC "/>-->
                            </div>
                        </div>
                        <div class="column">
                            <div class="field">
                                <label for="supplierOp" class="labeldiv">Supplier:</label>  
                                <select name="supplierOp[]" id ="supplierOp" class="chzn-select" multiple="multiple" style="width:15em;;height:1em;"> 
                                        <?= $supplierOptions ?> 
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="row noedit">
                        <div class="column ">
                            <div class="field">
                                <label for="packageOp" class="labeldiv">Package:</label>  
                                <select name="packageOp" id ="packageOp" class="required"> 
                                    <option value="">Choose 
                                        <?= $pkgOptions ?> 
                                </select>

                            </div>
                            
                        </div>
<!--                        <div class="column inithide onlyedit">
                            <div class="field">
                                <label for="reorderLevel" class="labeldiv">Reorder Level :</label>  
                                <input id="reorderLevel" name="reorderLevel" type="text"/>

                            </div>
                        </div>-->

                    </div>

                    <div class="row noedit">
                        <div class="column">
                            <div class="field">
                                <label for="uomOp" class="labeldiv">Unit Of Measurement:</label>  
                                <select name="uomOp" id ="uomOp" class="required"> 
                                    <option value="">Choose   
                                </select>

                            </div>
                        </div>
                        <div class="column">
                            <div class="field" style="display:none">
                                <label for="sizeOp" class="labeldiv">Measurement Denomination:</label>  
                                <select name="sizeOp" id ="sizeOp" class="required"> 
                                    <option value="">Choose 

                                </select>

                            </div>
                        </div>
                    </div>
                    <div class="row inithide onlyedit">
                        <div class="column ">
                            <div class="field">
                                <label for="vat" class="labeldiv">Vat :</label>  
                                <input id="vat" name="vat" type="text"/>

                            </div>
                            
                        </div>
                        <div class="column ">
                            <div class="field">
                                <label for="reorderLevel" class="labeldiv">Reorder Level :</label>  
                                <input id="reorderLevel" name="reorderLevel" type="text"/>

                            </div>
                        </div>

                    </div>
                     <div class="row inithide onlyedit">
                        <div class="column ">
                            <div class="field">
                                <label for="margin_type" class="labeldiv">Margin Type :</label>  
                                <select id="margin_type" name="margin_type">
                                    <option value="none">None</option>
                                    <option value="percentage">Percentage</option>
                                    <option value="amount">Amount</option>
                                </select>

                            </div>
                            
                        </div>
                        <div class="column ">
                            <div class="field">
                                <label for="margin_value" class="labeldiv">Margin Value :</label>  
                                <input id="margin_value" name="margin_value" type="text"/>

                            </div>
                        </div>

                    </div>
                    <div id="possibleProductCntnr" class="inithide" style="margin-top:5em">
                        <div class="ui-widget">
                            <div id ="status-message-product" class="ui-corner-all" style="margin: 0 auto; padding: 0 .7em;"> 
                                    <p><span style="float: left; margin-right: .3em;"></span>
                                    </p>
                            </div>
                        </div>
                         <div class="table-grid" style="padding-top:2em;">
                            <h1 id="pkdheader_product">Matching Products</h1>
                            <table id="matchingproduct"><tr><td/></tr></table> 
                            <div id="pagerMatchingProduct"></div>
                        </div>
<!--                        <div class="table-grid" style="padding-top:2em;">
                            <h1 id="pkdheader_attribute">Attributes Used In Barcode </h1>
                            <table id="barcodeAttributes"><tr><td/></tr></table> 
                            <div id="pagerBarcodeAttributes"></div>
                        </div>-->
                    </div>
                    <input id="createOverride" name="createOverride" type="hidden" value="0"/>
<!--                    <div class="row">
                        <div class="column">
                            <div class="field">
                                <label for="costPrice" class="labeldiv">Cost Price :</label>  
                                <input id="costPrice" name="costPrice" type="text"/>

                            </div>
                        </div>
                        <div class="column">
                            <div class="field">
                                <label for="price" class="labeldiv">Final Price :</label>  
                                <input id="price" name="price" type="text"/>

                            </div>
                        </div>
                    </div>-->


                </fieldset>
            </form>
        </div>
        <div style="display: block;height: 100%;width:90%;left:0em;" class="shopifine-ui-dialog ui-widget ui-widget-content ui-corner-all" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-dialog-form">
            <?php $this->load->view("common/message"); ?>
            <table id="product"><tr><td/></tr></table> 
            <div id="pager"></div> 
        </div>
<!--        <button id="create-product">Create New Product</button>-->
<!--        <div id="feedback_bar"></div>-->
        <?php $this->load->view("partial/footer"); ?>
    </body>
</html>

<script type="text/javascript">
    $("#packageOp").change(function(){
        var val = $(this).val();
        $.ajax({type:"post",
            url:"index.php/products/populateMeasurementDropDowns",
            data: {pkgId : val},
            success: function(uomHtml){
                $("#uomOp").children('option:not(:first)').remove();
                $("#uomOp").append(uomHtml); 
            }
        }); 
    });
   
    $("#uomOp").change(function(){
        var val = $(this).val();
        $.ajax({type:"post",
            url:"index.php/products/populateDenomDropdown",
            data: {uom : val},
            success: function(uomHtml){
                if (uomHtml!=null && uomHtml!="") {
                    if ($("#sizeOp").parent().is(':hidden')){
                        $("#sizeOp").parent().slideDown(50);
                    }
                        $("#sizeOp").children('option:not(:first)').remove();
                        $("#sizeOp").append(uomHtml); 
                    
                       
                }
                else {
                    if (!$("#sizeOp").parent().is(':hidden')){
                        $("#sizeOp").parent().slideUp(50);
                    }
                }
                       
            }
        }); 
    });
   
   
    $(".reqBarcodeAC").change(function (){
        var checked =  $(this).is(':checked');
        var element;
        console.log(this.id);
//        if (this.id =='reqBarcodeMfr'){
//            element ='mfrOp';
//        }
        if (checked){
            //$(this).parent().find('select').addClass("comboBoxrequired");
            console.log(element);
            $("#"+this.id).rules("remove","comboBoxrequired");
       
        }
        else {
            //$(this).parent().find('select').removeClass("comboBoxrequired");
            console.log("else "+element);
            if (this.id =='reqBarcodeMfr'){
                param ='mfrOp';
            }
            else if (this.id =='reqBarcodeModel'){
                param ='modelOp';
            }
            console.log("else "+this.id  + " param " + param);
           $("#"+this.id).rules("add",{comboBoxrequired:param});
        }
    })
//     $("#reqBarcodeModel").change(function (){
//        var checked =  $(this).is(':checked');
//        var element;
//        console.log(this.id);
////        if (this.id =='reqBarcodeMfr'){
////            element ='mfrOp';
////        }
//        if (checked){
//            //$(this).parent().find('select').addClass("comboBoxrequired");
//            console.log(element);
//            $("#modelOp").removeClass("comboBoxrequired");
//       
//        }
//        else {
//            //$(this).parent().find('select').removeClass("comboBoxrequired");
//            console.log("else "+element);
//            
//            $("#modelOp").addClass("comboBoxrequired");
//        }
//    })
//    
    
//    $(".reqBarcodetree").change(function (){
//        var checked =  $(this).is(':checked');
//        if (checked){
//            $(this).parent().find('select').addClass("comboBoxrequired");
//       
//        }
//        else {
//            $(this).parent().find('select').removeClass("comboBoxrequired");
//        }
//    })
    
    $("#hasBarcode").change(function (){
        var checked =  $(this).is(':checked');
        
        if (checked){
            $("#productbarcode").attr('disabled',true);
       
        }
        else {
            $("#productbarcode").attr('disabled',false);
        }
    })
    $("#inv-management").click(function (){
        document.location.href="index.php/products/loadInventory";
    })

</script>