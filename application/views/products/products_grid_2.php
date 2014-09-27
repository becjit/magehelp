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
        </style>
        <script type="text/javascript">
            $(function(){
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
                
                
                $( "#dialog:ui-dialog" ).dialog( "destroy" );

                $( "#productForm" ).validate({
                    ignore: [] ,
                    errorPlacement: function(error, element) {
                        error.appendTo( element.parent());
                    }//,
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

                             if (bValid ){
                                 var selectedIds = [];
                                 $('#treeViewDiv').jstree('get_selected').each(function(){
                                     selectedIds.push($(this).attr('id'));
                                 });

                                 $.ajax({
                                     url:"index.php/products/createBarcodeAndProduct",
                                     data: {barcodeData :{
                                             scannedBarcode : $("#productbarcode").val(),
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
                                             reorderLevel: $("#reorderLevel").val(),
     //                                        costPrice: $("#costPrice").val(),
     //                                        price: $("#price").val(),
                                             attributeSet:$("#attributeSetOp").val(),
                                             packageOp: $("#packageOp").val() }},

                                     type:"POST",
                                     success:function(serverresponse)
                                     {
                                         var response = JSON.parse(serverresponse);
                                         emptyMessages();
                                         if (response.status=='error'){

                                             showErrorMessage(response.message)
                                         }
                                         else  {
                                             showSuccessMessage(response.message)
                                             $("#product").trigger("reloadGrid");
                                         }
                                     }
                                 });

                                 $( this ).dialog( "close" );
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

            var myGrid = $("#product"),lastsel2,selectedMfrId,selectedModelId,selectedPkgId,selectUom,selectedDenom;

            myGrid.jqGrid({
                url:'index.php/products/populateProductsInGrid',
                datatype: 'json',
                mtype: 'GET',
                colNames:['Barcode',/*'System Name',*/'Product','Description','manufacturer_id','model_id','Manufacturer','Model',/*'Supplier',*/'package_id','Package Type',/*'Category',*/'Unit','Size/Quantity','Action'],
                colModel :[ 
                    //{name:'id', index:'id', width:55}, 
                    {name:'barcode',index:'barcode',width:120,align:'right',editable:false}, 
            //                        {name:'system_name',index:'system_name',width:90,align:'right',editable:false}, 
                    {name:'product_name', index:'product_name', width:80, align:'right',editable:true,editoptions:{size:"20",maxlength:"30"}},
                    {name:'description', index:'description', width:80, align:'right',editable:true,editoptions:{size:"20",maxlength:"30"}},
                    {name:'manufacturer_id', index:'manufacturer_id', hidden:true},
                    {name:'model_id', index:'model_id', hidden:true},
                    {name:'manufacturer', index:'manufacturer', width:80, align:'right',editable:true,edittype:"select",editoptions:{dataEvents: [
                  {  type: 'change',
                     fn: function(e) {

                        var val = $("#manufacturer").val();
                        $.ajax({type:'post',
                                data:{mfr:val},
                                url:'index.php/products/populateModel',
                                success: function(modelHtml){
                                    //console.log ("change mfr");
                                    $("#model").children('option:not(:first)').remove();
                                    $("#model").append(modelHtml);
                                }

                        });


                     }
                  }
               ],dataUrl:"index.php/products/populateMfrs",buildSelect:function(response)
                    {
                        var select = "<select name=" + "mfrOpEdit" + "id =" +"mfrOpEdit" +">" +
                                    "<option value=" + ">Select one..." + response + "</select>";


                                $.ajax({type:'post',
                                data:{mfr:selectedMfrId},
                                url:'index.php/products/populateModel',
                                success: function(modelHtml){
                                    //console.log("ajax mfr" + selectedModelId );
                                    $("#model").children('option:not(:first)').remove();
                                    $("#model").append(modelHtml);
                                    $("#model").val(selectedModelId);
                                }

                        });
                                return select;
                    }}},
                    {name:'model', index:'model', width:80, align:'right',editable:true,edittype:"select",editoptions:{dataUrl:"index.php/products/doNothing",buildSelect:function(response)
                    {
                            //console.log("build select");
                            var select = "<select name=" + "modelOpEdit" + "id =" +"modelOpEdit" +">" +
                                    "<option value=" + ">Select one..."  + "</select>";

                                return select;
                    }}},
//                    {name:'supplier', index:'supplier', width:80, align:'right',editable:true,editoptions:{size:"20",maxlength:"30"}},
                    {name:'package_id', index:'package_id', hidden:true},
                    {name:'package_name', index:'package_name', width:80, align:'right',editable:true,edittype:"select",editoptions:{dataEvents: [
                  {  type: 'change',
                     fn: function(e) {
                         var val = $("#package_name").val();
                         //console.log ("while changing" + val)
                        $.ajax({type:"post",
                                    url:"index.php/products/populateMeasurementDropDowns",
                                    data: {pkgId : val},
                                    success: function(uomHtml){
                                        //console.log ("in success " + val);
                                        //console.log ("in success uomHtml " + uomHtml);
                                        $("#uom").children('option:not(:first)').remove();
                                        $("#uom").append(uomHtml);

                                    }
                                }); 
                     }
                  }
               ],dataUrl:"index.php/products/populatePackages",buildSelect:function(response)
                    {
                        var select = "<select name=" + "mfrPkEdit" + "id =" +"mfrPkEdit" +">" +
                                    "<option value=" + ">Select one..." + response + "</select>";

                                //console.log ("package type val " + selectedPkgId);
                                $.ajax({type:"post",
                                    url:"index.php/products/populateMeasurementDropDowns",
                                    data: {pkgId : selectedPkgId},
                                    success: function(uomHtml){
                                        $("#uom").children('option:not(:first)').remove();
                                        $("#uom").append(uomHtml);
                                        //console.log ("before setting uom  val " + selectUom);
                                        $("#uom").val(selectUom);
                                    }
                                }); 

                                return select;
                    }}},
            //                        {name:'category', index:'category', width:100, align:'right',editable:false,editoptions:{size:"20",maxlength:"30"}},
                    {name:'uom', index:'uom', width:40, align:'right',editable:true,edittype:"select",editoptions:{dataEvents: [
                  {  type: 'change',
                     fn: function(e) {
                         var val = $("#uom").val();
                         //console.log ("while changing uom" + val)
                         $.ajax({type:"post",
                                url:"index.php/products/populateDenomDropdown",
                                data: {uom : val},
                                success: function(sizeHtml){
                                    if (sizeHtml!=null && sizeHtml!="") {
                                        if ($("#measurement_denomination").is(':disabled')){
                                            $("#measurement_denomination").attr('disabled','false');
                                        }
                                            $("#measurement_denomination").children('option:not(:first)').remove();
                                            $("#measurement_denomination").append(sizeHtml); 


                                    }
                                    else {
                                        if (!$("#measurement_denomination").is(':disabled')){
                                            $("#measurement_denomination").attr('disabled','true');
                                        }
                                    }

                                }
                            });   
                     }
                  }
               ],dataUrl:"index.php/products/populateMeasurementDropDowns",buildSelect:function(response)
                    {
                            var val = selectUom;
                            //console.log("build uom" + val);
                            var select = "<select name=" + "modelOpEdit" + "id =" +"modelOpEdit" +">" +
                                    "<option value=" + ">Select one..."  + "</select>";

                                return select;
                    }}},
                    {name:'measurement_denomination', index:'measurement_denomination', width:60, align:'right',editable:true,edittype:"select",editoptions:{dataUrl:"index.php/products/doNothing",buildSelect:function(response)
                    {
                            var val = selectUom;
                            //console.log("build size" + val);
                            var select = "<select name=" + "modelOpEdit" + "id =" +"modelOpEdit" +">" +
                                    "<option value=" + ">Select one..."  + "</select>";

                             $.ajax({type:"post",
                                    url:"index.php/products/populateDenomDropdown",
                                    data: {uom : val},
                                    success:  function(sizeHtml){
                                        if (sizeHtml!=null && sizeHtml!="") {
                                            if ($("#measurement_denomination").is(':disabled')){
                                                $("#measurement_denomination").attr('disabled','false');
                                            } 
                                            //console.log ("measurement denom " + sizeHtml);
                                            //console.log ("measurement denom  sel " + selectedDenom);
                                                $("#measurement_denomination").children('option:not(:first)').remove();
                                                $("#measurement_denomination").append(sizeHtml);
                                                $("#measurement_denomination").val(selectedDenom);


                                        }
                                        else {
                                            if (!$("#measurement_denomination").is(':disabled')){
                                                $("#measurement_denomination").attr('disabled','true');
                                            }
                                        }

                                    }
                                });    
                                return select;
                    }}},
                    //{name:'isactive', index:'isactive', width:30, align:'right',editable:true,edittype:"select", formatter:'select', editoptions:{value:"1:Yes;0:No"}}
                    {name:'generate', index:'generate', width:80, align:'right',editable:false,search:false,formatter:'showlink', formatoptions:{baseLinkUrl:'index.php/products/printBarcode'},cellattr: function (rowId, val, rawObject, cm, rdata) 
                    {     
                        //console.log(rawObject[0]);
                        return 'title="'  + rawObject[0]+'"';     
                    }}

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
                caption: 'Products',
                multiselect:true,
                height: '100%',
                width:'90%',
                jsonReader : {
                    root:"productdata",
                    page: "page",
                    total: "total",
                    records: "records",
                    cell: "dprow",
                    id: "id"
                },
                onSelectRow: function(id){
                    lastsel2 = id;
                    selectedMfrId = myGrid.jqGrid("getCell",id,"manufacturer_id");
                    selectedModelId = myGrid.jqGrid("getCell",id,"model_id");
                    selectedPkgId =  myGrid.jqGrid("getCell",id,"package_id");
                    selectUom = myGrid.jqGrid("getCell",id,"uom");
                    selectedDenom =  myGrid.jqGrid("getCell",id,"measurement_denomination");
                 
                },
                editurl:'index.php/products/editProduct',
                postData:{"test":"val"}


            }).navGrid("#pager",{edit:true,add:false,del:false,search:false},{height:280,reloadAfterSubmit:false,closeAfterEdit:true,recreateForm:true,checkOnSubmit:true},{},{},{},{});

        myGrid.jqGrid('navButtonAdd','#pager',{
           caption:"", 
           title:"Export as csv",
           id:"export_product",
           onClickButton : function () { 
               myGrid.jqGrid('excelExport',{tag:"csv","url":"index.php/products/exportProductsInGrid"});
           } 
        });
    
        myGrid.jqGrid('navButtonAdd','#pager',{
           caption:"", 
           title:"Create Product",
           buttonicon:"ui-icon-plus",
           id:"add_product",
           onClickButton : function () {       
                $("#newModelCtnr").parent().css("display","none");
                $("#modelCtnr").parent().css("display","none");
                $( "#dialog-form" ).dialog( "open" );
                } 
        });

        myGrid.jqGrid('navButtonAdd','#pager',{
            caption:"",
            title:"Mark as inactive",
            id:"inactive_product",
            buttonicon:"ui-icon-locked",
            onClickButton : function (id) { 
                    var rowid = myGrid.jqGrid('getGridParam', 'selarrrow');
                    $.ajax({type:"post",
                        url:"index.php/products/deactivate",
                        data: {ids : rowid},
                        success: function(){
                            $("#product").trigger("reloadGrid");
                        }
                    }); 
            } 
        });

        myGrid.jqGrid('navButtonAdd','#pager',{
            caption:"",
            title:"Mark as active",
            id:"active_product",
            buttonicon:"ui-icon-unlocked",
            onClickButton : function (id) { 
                var rowid = myGrid.jqGrid('getGridParam', 'selarrrow');
                $.ajax({type:"post",
                    url:"index.php/products/activate",
                    data: {ids : rowid},
                    success: function(){
                        $("#product").trigger("reloadGrid");
                    }
                }); 
            } 
        });

        myGrid.jqGrid('navButtonAdd','#pager',{
            caption:"",
            title:"Load all ",
            id:"load_product",
            buttonicon:"ui-icon-arrow-4-diag",
            onClickButton : function (id) { 
                var rowid = myGrid.jqGrid('getGridParam', 'selarrrow');
                $.ajax({type:"post",
                    url:"index.php/products/populateProductsInGrid",
                    data: {loadall : true},
                    success: function(response){
                        //$("#product").trigger("reloadGrid");
                        var grid = jQuery("#product")[0];
                        var myjsongrid = eval("("+response+")"); 
                        grid.addJSONData(myjsongrid); 


                    }
                }); 
            } 
        });

        myGrid.jqGrid ('navButtonAdd', '#pager',
                 { caption: "", buttonicon: "ui-icon-calculator",
                   title: "Choose Columns",
                   onClickButton: function() {
                        myGrid.jqGrid('columnChooser');
                   }
                 });

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
    
    
    
        </script>



    </head>
    <body>
        <?php $this->load->view("common/menubar"); ?>
<!--        <button id="create-product">Create New Product</button> -->
        <!--<button id="inv-management" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"><span class="ui-button-text"> Manage Inventory</span></button>-->
        <div id ="dialog-form" title="Add New Product Entity">
<!--            <h1 id="formHeader">Add New Product Entity</h1>   -->
            <form id="productForm">
                <fieldset>
                    <div class="row">
                        <div class="column">
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
                    <div class="row inithide">
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
                    <div class="row inithide">
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
                    
                    

                    <div class="row">
                        <div class="column">
                            <div id="mfrCtnr">
                                <div class="ui-widget field">
                                    <label for="mfrOp" class="labeldiv">Manufacturer :</label> 
                                    <select name="mfrOp" id ="mfrOp"> 
                                        <option value="">Select one...
                                            <?= $mfrOptions?> 
                                    </select>
                                    <input id="reqBarcodeMfr" name ="reqBarcodeMfr" type="checkbox" class="reqBarcode reqBarcodeAC"/>
                                </div>  
                            </div>

                        </div>
                        <div class="column" style="display:none">
                            <div id="modelCtnr">
                                <div class="ui-widget field">
                                    <label for="modelOp" class="labeldiv">Model :</label>  
                                    <select name="modelOp" id ="modelOp" > 
                                        <option value="">Select one...
                                            <?= $modelOptions?> 
                                    </select>
                                    <input id="reqBarcodeModel" name ="reqBarcodeModel" type="checkbox" class="reqBarcode reqBarcodeAC"/>
                                </div>
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

                    <div class="row">
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

                    <div class="row">
                        <div class="column">
                            <div class="field">
                                <label for="packageOp" class="labeldiv">Package:</label>  
                                <select name="packageOp" id ="packageOp" class="required"> 
                                    <option value="">Choose 
                                        <?= $pkgOptions ?> 
                                </select>

                            </div>
                            
                        </div>
                        <div class="column">
                            <div class="field">
                                <label for="reorderLevel" class="labeldiv">Reorder Level :</label>  
                                <input id="reorderLevel" name="reorderLevel" type="text"/>

                            </div>
                        </div>

                    </div>

                    <div class="row">
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
        if (checked){
            $(this).parent().find('select').addClass("comboBoxrequired");
       
        }
        else {
            $(this).parent().find('select').removeClass("comboBoxrequired");
        }
    })
    
    
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