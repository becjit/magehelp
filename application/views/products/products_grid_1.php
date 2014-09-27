<!DOCTYPE html>
<html>
    <head>
        <title>Product Details</title>
        <?php $this->load->view("common/header"); ?>
        
<!--            <link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.0/themes/ui-lightness/jquery-ui.css">-->
        <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>css/ui.jqgrid.css" />
<!--        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>-->
        <script src="<?php echo base_url(); ?>js/i18n/grid.locale-en.js" type="text/javascript"></script>
        <script src="<?php echo base_url(); ?>js/jquery.jqGrid.min.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
        <style type="text/css">
            body { font-size: 62.5%; }
            label, input { display:inline; }
            input.text { margin-bottom:12px; width:95%; padding: .4em; }
            fieldset { padding:0; border:0; margin-top:25px; }
            h1 { font-size: 1.2em; margin: .6em 0; }
            div#users-contain { width: 350px; margin: 20px 0; }
            div#users-contain table { margin: 1em 0; border-collapse: collapse; width: 100%; }
            div#users-contain table td, div#users-contain table th { border: 1px solid #eee; padding: .6em 10px; text-align: left; }
            .ui-dialog .ui-state-error { padding: .3em; }
            .validateTips { border: 1px solid transparent; padding: 0.3em; }
            .reqBarcodeAC { position:absolute;left:230px;}
            * { font-family: Verdana; font-size: 96%; }
            label { width: 10em; float: left; }
            label.error { float: right; color: red; padding-left: .5em; vertical-align: top; position: relative;width:130px;}
            p , .column{ clear: both; padding: 1em; }
            .submit { margin-left: 12em; }
            em { font-weight: bold; padding-right: 1em; vertical-align: top; }

            .ui-combobox {
                position: relative;
                display: inline-block;
            }
            .ui-combobox-toggle {
                position: absolute;
                top: 0;
                bottom: 0;
                margin-left: -1px;
                padding: 0;

            }
            .ui-combobox-input {
                margin: 0;
                padding: 0.3em;
            }         


        </style>
        <script>
            (function( $ ) {
                $.widget( "ui.combobox", {
                    options: {
                        strict: false,
                        customChange:null
                            
                    },
                    _create: function() {
                        var input,
                        self = this,
                        id = this.element[0].id + "-input",
                        select = this.element.hide(),
                        selected = select.children( ":selected" ),
                        value = selected.val() ? selected.text() : "",
                        strict = this.options.strict,
                                        
                        wrapper = this.wrapper = $( "<span>" )
                        .addClass( "ui-combobox" )
                        .insertAfter( select );

                        input = $( "<input>" ).attr("id",id)
                        .appendTo( wrapper )
                        .val( value )
                        .addClass( "ui-state-default ui-combobox-input" )
                        .autocomplete({
                            delay: 0,
                            minLength: 0,
                            source: function( request, response ) {
                                var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
                                response( select.children( "option" ).map(function() {
                                    var text = $( this ).text();
                                    if ( this.value && ( !request.term || matcher.test(text) ) )
                                        return {
                                            label: text.replace(
                                            new RegExp(
                                            "(?![^&;]+;)(?!<[^<>]*)(" +
                                                $.ui.autocomplete.escapeRegex(request.term) +
                                                ")(?![^<>]*>)(?![^&;]+;)", "gi"
                                        ), "<strong>$1</strong>" ),
                                            value: text,
                                            option: this
                                        };
                                }) );
                            },
                            
                            select: function( event, ui ) {
                                ui.item.option.selected = true;
                                self._trigger( "selected", event, {
                                                            
                                    item: ui.item.option
                                });
                            },
                            change: function( event, ui ) {
                                //self.off();
                                                        
                                if ( !ui.item ) {
                                                                
                                    var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( $(this).val() ) + "$", "i" ),
                                    valid = false;
                                    select.children( "option" ).each(function() {
                                        if ( $( this ).text().match( matcher ) ) {
                                            this.selected = valid = true;
                                            return false;
                                        }
                                    });
                                    if ( !valid ) {
                                        // remove invalid value, as it didn't match anything
                                        select.val( "" );
                                        
                                        if (!strict) {
                                            callback = self.options.customChange;
                                            if ($.isFunction(callback)){
                                               
                                                    callback();
                                            }
                                            return;
                                        }
                                        $( this ).val( "" );
                                        input.data( "autocomplete" ).term = "";
                                        return false;
                                    }
                                }
                                callback = self.options.customChange;
                                if ($.isFunction(callback)){
                                    callback();
                                }
                                                        
                            }
                        })
                        .addClass( "ui-widget ui-widget-content ui-corner-left" ).keypress(function (e){
                        console.log(e.which);
                        if (e.which== 13){
                           this._trigger("autocompletechange");
                        }
                        
                        
                        });

                        input.data( "autocomplete" )._renderItem = function( ul, item ) {
                            return $( "<li></li>" )
                            .data( "item.autocomplete", item )
                            .append( "<a style>" + item.label + "</a>" )
                            .appendTo( ul );
                        };

                        $( "<a>" )
                        .css("left", "100px")
                        .attr( "tabIndex", -1 )
                        .attr( "title", "Show All Items" )
                        .appendTo( wrapper )
                        .button({
                            icons: {
                                primary: "ui-icon-triangle-1-s"
                            },
                            text: false
                        })
                        .removeClass( "ui-corner-all" )
                        .addClass( "ui-corner-right ui-combobox-toggle" )
                        .click(function() {
                            // close if already visible
                            if ( input.autocomplete( "widget" ).is( ":visible" ) ) {
                                input.autocomplete( "close" );
                                return;
                            }

                            // work around a bug (likely same cause as #5265)
                            $( this ).blur();

                            // pass empty string as value to search for, displaying all results
                            input.autocomplete( "search", "" );
                            input.focus();
                        });
                    },
                    keypress: function (e){
                        console.log(e.which)
                    },
                    destroy: function() {
                        this.wrapper.remove();
                        this.element.show();
                        $.Widget.prototype.destroy.call( this );
                    }
                });
            })( jQuery );



        </script>
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
                 
        
        
                $( "#dialog-form" ).dialog({
                    autoOpen: false,
                    height: 'auto',
                    width: 400,
                    position:[400,25],
                    modal: true,
                    buttons: {
                        "Create the Product": function() {
                            bValid =  $("#productForm").valid();
                    
                            if (bValid ){
                        
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
                                            categoryOp: $("#categoryOp").val(),
                                            categoryIp: $("#categoryOp-input").val(),
                                            supplierOp: $("#supplierOp").val(),
                                            uomOp: $("#uomOp").val(),
                                            sizeOp: $("#sizeOp").val(),
                                            packageOp: $("#packageOp").val() }},
                                
                                    type:"POST",
                                    success:function(response)
                                    {
                            
                                        $("#product").trigger("reloadGrid");
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
                        //allFields.val( "" ).removeClass( "ui-state-error" );
                        $("#productForm").data('validator').resetForm();
                        $('#productForm')[0].reset();
                    }
                });


                $( "#create-product" )
                .button()
                .click(function() {
                    //if (!$("#newModelCtnr").parent().is(':hidden')){
                    $("#newModelCtnr").parent().css("display","none");
                    //}
                    //if (!$("#modelCtnr").parent().is(':hidden')){
                    $("#modelCtnr").parent().css("display","none");
                    //}
                    $( "#dialog-form" ).dialog( "open" );
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
    
                var myGrid = $("#product"),lastsel2,isModelPop;
                var editparameters = {
                    "keys" : false,
                    "oneditfunc" : null,
                    "successfunc" : null,
                    "url" : 'edittest',
                    "extraparam" : {},
                    "aftersavefunc" : null,
                    "errorfunc": null,
                    "afterrestorefunc" : null,
                    "restoreAfterError" : true,
                    "mtype" : "POST"
                };
                myGrid.jqGrid({
                    url:'index.php/products/populateProductsInGrid',
                    datatype: 'json',
                    mtype: 'GET',
                    colNames:['Barcode','System Name','Product','Description','mfr_id','model_id','Manufacturer','Model','Supplier','Package Type','Package Description','Unit','Size/Quantity'],
                    colModel :[ 
                        //{name:'id', index:'id', width:55}, 
                        {name:'barcode ', index:'barcode ',width:90,align:'right',editable:false,editoptions:{size:"20",maxlength:"30"}}, 
                        {name:'system_name ', index:'system_name ',width:90,align:'right',editable:false,editoptions:{size:"20",maxlength:"30"}}, 
                        {name:'product_name', index:'product_name', width:80, align:'right',editable:true,editoptions:{size:"20",maxlength:"30"}},
                        {name:'description', index:'description', width:80, align:'right',editable:true,editoptions:{size:"20",maxlength:"30"}},
                        {name:'mfr_id', index:'mfr_id', hidden:true},
                        {name:'model_id', index:'model_id', hidden:true},
                        {name:'manufacturer', index:'manufacturer', width:80, align:'right',editable:true,edittype:"select",editoptions:{dataEvents: [
                      {  type: 'change',
                         fn: function(e) {
                             
                            var val = $("#manufacturer").val();
                            isModelPop = true;
                            $.ajax({type:'post',
                                    data:{mfr:val},
                                    url:'index.php/products/populateModel',
                                    success: function(modelHtml){
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
                                    isModelPop = false;
                                    return select;
                        }}},
                        {name:'model', index:'model', width:80, align:'right',editable:true,edittype:"select",editoptions:{dataEvents: [
                      {  type: 'click',
                         fn: function(e) {
                             if (!isModelPop){
                                 var val = $("#manufacturer").val();
                                    $.ajax({type:'post',
                                            data:{mfr:val},
                                            url:'index.php/products/populateModel',
                                            success: function(modelHtml){
                                                $("#model").children('option:not(:first)').remove();
                                                $("#model").append(modelHtml);
                                            }

                                    });
                                    isModelPop = true;
                             }
                            
                            
				
                         }
                      }
                   ],dataUrl:"index.php/products/populateModel",buildSelect:function(response)
                        {
                            //var id = $("#manufacturer").val();
                            //alert ('hi' + id);
                            //var id = $("#mfr_id").val();
                            var cell = myGrid.jqGrid("getCell",lastsel2,"mfr_id");
                        console.log("cell test = " + cell);
                            var select = "<select name=" + "modelOpEdit" + "id =" +"modelOpEdit" +">" +
                                        "<option value=" + ">Select one..."  + "</select>";
                                    return select;
                        }}},
                        {name:'supplier', index:'supplier', width:80, align:'right',editable:true,editoptions:{size:"20",maxlength:"30"}},
                        {name:'package_name', index:'package_name', width:80, align:'right',editable:true,edittype:"select",editoptions:{dataEvents: [
                      {  type: 'change',
                         fn: function(e) {
                            
                         }
                      }
                   ],dataUrl:"index.php/products/populatePackages",buildSelect:function(response)
                        {
                            var select = "<select name=" + "mfrOpEdit" + "id =" +"mfrOpEdit" +">" +
                                        "<option value=" + ">Select one..." + response + "</select>";
                                    //isModelPop = false;
                                    return select;
                        }}},
                        {name:'package_description', index:'package_description', width:80, align:'right',editable:false,editoptions:{size:"20",maxlength:"30"}},
                        {name:'uom', index:'uom', width:80, align:'right',editable:true,editoptions:{size:"20",maxlength:"30"}},
                        {name:'measurement_denomination', index:'measurement_denomination', width:80, align:'right',editable:true,editoptions:{size:"20",maxlength:"30"}}
            
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
                    height: '100%',
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
                    },
                    editurl:'index.php/products/editProduct'

                }).navGrid("#pager",{edit:true,add:false,del:false,search:false},{height:280,reloadAfterSubmit:false,closeAfterEdit:true,onClose: function (){$("#model").children('option:not(:first)').remove();isModelPop=false;}},{},{},{},{});
        
                myGrid.jqGrid('navButtonAdd','#pager',{
       caption:"", 
       title:"Export as csv",
       onClickButton : function () { 
           myGrid.jqGrid('excelExport',{tag:"csv","url":"index.php/products/exportProductsInGrid"});
       } 
});

myGrid.jqGrid('navButtonAdd','#pager',{
       caption:"",
       title:"Mark as inactive",
       buttonicon:"ui-icon-locked",
       onClickButton : function (id) { 
            var rowid = myGrid.jqGrid('getGridParam', 'selrow');
           alert(rowid);
           //myGrid.jqGrid('excelExport',{tag:"csv","url":"index.php/products/exportProductsInGrid"});
       } 
});
        
                myGrid.jqGrid('filterToolbar', {stringResult: true, searchOnEnter: true, defaultSearch : "cn"});
            }); 
    
    
    
        </script>

<!-- <script type="text/javascript">
    $(function(){ 
        var myGrid = $("#product"),lastsel2;
        var editparameters = {
	"keys" : false,
	"oneditfunc" : null,
	"successfunc" : null,
	"url" : 'edittest',
        "extraparam" : {},
	"aftersavefunc" : null,
	"errorfunc": null,
	"afterrestorefunc" : null,
	"restoreAfterError" : true,
	"mtype" : "POST"
        };
        myGrid.jqGrid({
            url:'index.php/utilities/populateDeliveryPoint',
            datatype: 'json',
            mtype: 'GET',
            colNames:['Name','Address'],
            colModel :[ 
//            {name:'id', index:'id', width:55}, 
            {name:'name', index:'name',width:90,editable:true,editoptions:{size:"20",maxlength:"30"}}, 
            {name:'address', index:'address', width:80, align:'right',editable:true,editoptions:{size:"20",maxlength:"30"}}
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
            caption: 'My first grid',
            height: '100%',
            jsonReader : {
                root:"deliverypointdata",
                page: "page",
                total: "total",
                records: "records",
                cell: "dprow",
                id: "id"
            },
            onSelectRow: function(id){
                if(id && id!==lastsel2){
                    myGrid.restoreRow(lastsel2);
                    //myGrid.editRow(id,true);
                    lastsel2=id;
                    myGrid.jqGrid('editRow',id,  { 
    keys : true, 
    oneditfunc: function() {
        alert ("edited"); 
    },
    url:'edittest'
});
                }
            }
            ,editurl:'edittest'

        }).navGrid("#pager",{edit:true,add:true,del:false,search:false},{height:280,reloadAfterSubmit:false},{height:280,reloadAfterSubmit:false},{},{},{});
        
 
        myGrid.jqGrid('navButtonAdd','#pager',{
       caption:"", 
       onClickButton : function () { 
           myGrid.jqGrid('excelExport',{tag:"csv","url":"edittest"});
       } 
});
        
        myGrid.jqGrid('filterToolbar', {stringResult: true, searchOnEnter: true, defaultSearch : "cn"});
    }); 
    
    
    
    </script>-->

    </head>
    <body>
        <?php $this->load->view("common/menubar"); ?>
        <div id ="dialog-form">
            <h1 id="formHeader">Add New Product Entity</h1>   
            <form id="productForm">
                <fieldset>
                    <div class="row">
                        <div class="column">
                            <div class="field">
                                <label for="hasBarcode">Product Do Not Have Barcode</label> 
                                <input id="hasBarcode" name ="hasBarcode" type="checkbox"/>
                            </div>

                        </div>
                        <div class="column">
                            <div class="field">
                                <label for="productbarcode" >Scan/ Type Barcode:</label>  
                                <input id="productbarcode" name ="productbarcode" type="text" class="required"/>
                                
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="column">
                            <div class="field">
                                <label for="name">Name of Product Entity:</label>  
                                <input id="name" name ="name" type="text" class="required"/>

                            </div>

                        </div>
                        <div class="column">
                            <div class="field">
                                <label for="desc">Description:</label>  
                                <input id="desc" name ="desc" type="text"/>

                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="column">
                            <div id="mfrCtnr">
                                <div class="ui-widget field">
                                    <label for="mfrOp">Manufacturer:</label>  
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
                                    <label for="modelOp">Model :</label>  
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
                                    <label for="newModelIp">Specify New Model Type :</label>  
                                    <input id="newModelIp" name ="newModelIp" type="text"/>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="column">
                            <div class="field">
                                <label for="categoryOp">Category:</label>  
                                <select name="categoryOp" id ="categoryOp" > 
                                    <option value="">Choose 
                                        <?= $categoryOptions ?> 
                                </select>
                                <input id="categoryReq" name ="reqBarcodeCat" type="checkbox" class="reqBarcodeAC "/>
                            </div>
                        </div>
                        <div class="column">
                            <div class="field">
                                <label for="supplierOp">Supplier:</label>  
                                <select name="supplierOp" id ="supplierOp" class="opt required"> 
                                    <option value=0>Choose 
                                        <?= $supplierOptions ?> 
                                </select>
                                <input id="reqBarcodeSupp" name ="reqBarcodeSupp" type="checkbox" class="reqBarcode"/>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="column">
                            <div class="field">
                                <label for="packageOp">Package:</label>  
                                <select name="packageOp" id ="packageOp" class="required"> 
                                    <option value="">Choose 
                                        <?= $pkgOptions ?> 
                                </select>

                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="column">
                            <div class="field">
                                <label for="uomOp">Unit Of Measurement:</label>  
                                <select name="uomOp" id ="uomOp" class="required"> 
                                    <option value="">Choose   
                                </select>

                            </div>
                        </div>
                        <div class="column">
                            <div class="field" style="display:none">
                                <label for="sizeOp">Measurement Denomination:</label>  
                                <select name="sizeOp" id ="sizeOp" class="required"> 
                                    <option value="">Choose 

                                </select>

                            </div>
                        </div>
                    </div>


                </fieldset>
            </form>
        </div>

        <table id="product"><tr><td/></tr></table> 
        <div id="pager"></div> 
        <button id="create-product">Create New Product</button>
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
   
   
    $(",reqBarcodeAC").change(function (){
        var checked =  $(this).is(':checked');
        if (checked){
            $(this).parent().find('select').addClass("comboBoxrequired");
       
        }
        else {
            $(this).parent().find('select').removeClass("comboBoxrequired");
        }
    })
    
    $("#hasBarcode").change(function (){
        var checked =  $(this).is(':checked');
        if (checked){
            $("#productbarcode").attr('disabled',true)
       
        }
        else {
            $("#productbarcode").attr('disabled',false)
        }
    })

</script>