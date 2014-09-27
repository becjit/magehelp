<html><head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <title> Add product details and generate barcode</title>
        <?php $this->load->view("common/header"); ?>
        <link rel="stylesheet" rev="stylesheet" href="<?php echo base_url();?>css/shopifine.css" />
        <!--  <link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.0/themes/ui-lightness/jquery-ui.css">-->
        <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>css/ui.jqgrid.css" />
        <script src="<?php echo base_url(); ?>js/i18n/grid.locale-en.js" type="text/javascript"></script>
        <script src="<?php echo base_url(); ?>js/jquery.jqGrid.min.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
<!--        <style type="text/css">
            html {height:100%;}
            body { font-size: 62.5%; height:100%;}
            label, input { display:inline; }
            input.text { margin-bottom:12px; width:95%; padding: .4em; }
            fieldset { padding:0; border:0; margin-top:25px; }
            h1 { font-size: 1.2em; margin: 1em 0; padding-left:2em;}
            div#users-contain { width: 350px; margin: 20px 0; }
            div#users-contain table { margin: 1em 0; border-collapse: collapse; width: 100%; }
            div#users-contain table td, div#users-contain table th { border: 1px solid #eee; padding: .6em 10px; text-align: left; }
            .ui-dialog .ui-state-error { padding: .3em; }
            .shopifine-ui-dialog 
            {overflow: hidden;
            padding: 4em;
            left: 65px;
            position: relative;
            
            width: 680px;}
            .validateTips { border: 1px solid transparent; padding: 0.3em; }
            .reqBarcodeAC { position:absolute;left:230px;}
            * { font-family: Verdana; font-size: 98%; }
            em {color:red;}
            .shopifine-ui-button{width:150px;float:right}
            .shopifine-ui-widget-content{background: url("images/ui-bg_inset-hard_100_fcfdfd_1x100.png") repeat-x scroll 50% bottom #FCFDFD;
                border-bottom: 1px solid #A6C9E2;
                color: #222222;
            }
            .form-container {
                border: 1px solid #A6C9E2;
                color: #222222;
            }
            .shopifine-ui-dialog-buttonset {
                margin: 1em;
                padding-bottom: 3em;
            }
            label { width: 6em; float: left; }
            label.error { float: right; color: red; padding-left: .5em; vertical-align: top; position: relative;width:100px;}
            p , .column{ float:left; padding:1em 1em 3em;width:310px}
            
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
            .table-grid {
                height: 60%;
                clear:both;
            }
            #status-message{height: 35px;
            margin: 20px;
            padding: 0 0.7em;
            position: relative;
            width: auto;}


        </style>-->







        <script type="text/javascript">
            //<![CDATA[ 

//validators
    $(function() {

        $.validator.addMethod("greaterThanZero", function(value, element) {
            return this.optional(element) || (parseInt(value) > 0);
        }, "* Number of Items must be greater than zero");

        $.validator.addMethod("barcodeRequired", function(value, element) {
            var value = $("#stockbarcode-hidden").val();
            if (value == null || value == ""){
                return false;
            }
            return true;

        }, "Please scan an item");

        $( "#dialog:ui-dialog" ).dialog( "destroy" );

        $( "#productForm" ).validate({
            errorPlacement: function(error, element) {
                error.appendTo( element.parent());
            },
            rules: {
                noOfItems: {
                    greaterThanZero: true
                },

                stockbarcode : {
                    barcodeRequired:true
                }
            }

        }
    );   
   
        function negativeValCheck(value,colname) {
            if (value < 0) 
                return [false,"Please enter a positive value"];
            else 
                return [true,""];
        }
        
                //jQGrid
    
                var myGrid = $("#products"),lastsel2;
                var editparameters = {
                    "keys" : true,
                    "oneditfunc" : null,
                    "successfunc" : function(){
                        var successStatus = " The product update is successful ";
                        $("#status-message").addClass("ui-state-highlight");
                        $("#status-message > p > span").addClass("ui-icon");
                        $("#status-message > p > span").addClass("ui-icon-info");
                        $("#status-message > p").append(successStatus);
                        myGrid.trigger("reloadGrid");
                        return true;
                    },
                    "aftersavefunc" : null,
                    "errorfunc": function(response)
                            {
                                var errorStatus = "Update error!! Please contact Administrator";
                                $("#status-message").addClass("ui-state-error");
                                $("#status-message > p > span").addClass("ui-icon");
                                $("#status-message > p > span").addClass("ui-icon-alert");
                                $("#status-message > p").append(errorStatus);
                                
                            },
                    "afterrestorefunc" : null,
                    "restoreAfterError" : true,
                    "mtype" : "POST"
                };
                myGrid.jqGrid({
                    url:'index.php/products/populateProductStocksInGrid',
                    datatype: 'json',
                    mtype: 'GET',
                    colNames:['Product Id','Barcode','Product Name','Stock','Quick Stock Add','Is Active'],
                    colModel :[ 
                        //            {name:'id', index:'id', width:55}, 
                        {name:'product_id', index:'product_id',hidden:true}, 
                        {name:'barcode', index:'barcode', width:80, align:'right'},
                        {name:'system_name', index:'system_name', width:140, align:'right',editable:false,editoptions:{size:"20",maxlength:"30"}},
                        {name:'stock',search:false, index:'stock', width:80, align:'right',editable:false,editoptions:{size:"20",maxlength:"30"}},
                        {name:'stock_add', search:false,index:'stock_add', width:80, align:'right',editrules:{custom:true,custom_func:negativeValCheck},editable:true,editoptions:{size:"20",maxlength:"30"}},
                        {name:'isactive',search:false, index:'isactive', width:80, align:'right',editable:false,edittype:"select", formatter:'select', editoptions:{value:"1:Yes;0:No"}},
            
            
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
                    height:'50%',
                    width:680,
                    caption: 'Product Stocks',
            
                    jsonReader : {
                        root:"productdata",
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
                    ,editurl:'index.php/products/updateStocks'

                });
        
        
        
                
                myGrid.jqGrid('filterToolbar', {stringResult: true, searchOnEnter: false, defaultSearch : "cn"});
                
                //end grid
                
                var availableBarcodes = <?php echo $barcodes ?>;
                $("#stockbarcode").autocomplete({
                    source: availableBarcodes,
                    focus: function( event, ui ) {
                        $( "#stockbarcode" ).val( ui.item.label );
                        return false;  
                    },   
                    select: function( event, ui ) {                 
                        $( "#stockbarcode" ).val( ui.item.label );
                        var hiddenVal = $( "#stockbarcode-hidden" ).val();
                        if (hiddenVal!= null && hiddenVal!= ""){
                            if (hiddenVal != ui.item.label){
                                //console.log ("hiddenval = " + hiddenVal);
                                //console.log ("ui.item.label = " + ui.item.label);
                                $("#noOfItems").val(0);
                            }
                        }
                    
                        $( "#stockbarcode-hidden" ).val( ui.item.value );                 
                        return false;             
                    }
                }); 
                
                $("#scanBtn").click(function() {
                   var bValid =  $("#productForm").valid();
                    var stock = $("#noOfItems").val();
                    var sku = $("#stockbarcode-hidden").val();    
                    if (bValid ){
                        
                        $.ajax({
                            url:"index.php/products/updateStocksByBarcode",
                            data: {
                                stock_add: $("#noOfItems").val(),
                                barcode: $("#stockbarcode-hidden").val() },
                                
                            type:"POST",
                            success:function(response)
                            {
                                var successStatus = stock + " items has been added in inventory for " + sku;
                                $("#status-message").addClass("ui-state-highlight");
                                $("#status-message > p > span").addClass("ui-icon");
                                $("#status-message > p > span").addClass("ui-icon-info");
                                $("#status-message > p").append(successStatus);
                                myGrid.trigger("reloadGrid");
                            },
                            error:function(response)
                            {
                                var errorStatus = stock + " items could not be added in inventory for " + sku + " due to error in backend";
                                $("#status-message").addClass("ui-state-error");
                                $("#status-message > p > span").addClass("ui-icon");
                                $("#status-message > p > span").addClass("ui-icon-alert");
                                $("#status-message > p").append(errorStatus);
                                
                            }
                        });
                        
                        
                }
                    
                })
            
            });
    
   

 
            //]]>  

</script>


    </head>
    <body>
        <?php $this->load->view("common/menubar"); ?>
        <div style="display: block;height: auto;" class="shopifine-ui-dialog ui-widget ui-widget-content ui-corner-all" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-dialog-form">
            <div class="form-container">
                <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
                <span class="ui-dialog-title" id="ui-dialog-title-dialog-form">&nbsp;</span>
                <a href="#" class="ui-dialog-titlebar-close ui-corner-all" role="button">
                    
                </a>
            </div>
            <div  class="ui-dialog-content" style="width: auto; min-height: 65.1333px; height: auto;" scrolltop="0" scrollleft="0">
                <h1 id="formHeader">Scan Items</h1>   
                <form id="productForm">
                    <fieldset>


                        <div class="row">
                            <div class="column">
                                <div class="field">
                                    <label for="stockbarcode">Scan/Input Barcode:</label><em>*</em>  
                                    <input type="text" name="stockbarcode" id="stockbarcode" class="ui-autocomplete-input" autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true">

                                </div>
                                <input type="hidden" name="stockbarcode-hidden" id="stockbarcode-hidden">
                            </div>

                            <div class="column">
                                <div class="field">
                                    <label for="noOfItems">Items Scanned:</label><em>*</em>    
                                    <input type="text" class="required" value="0" name="noOfItems" id="noOfItems">

                                </div>

                            </div>
                        </div>


                    </fieldset>
                </form>
            </div>
            
            <div class="ui-dialog-buttonpane shopifine-ui-widget-content ui-helper-clearfix">
                <div class="shopifine-ui-dialog-buttonset">
                    <button id="scanBtn" type="button" class="shopifine-ui-button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false">
                        <span class="ui-button-text">Scan and update stock</span>
<!--                    </button><button type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text">Cancel</span></button></div></div></div>-->
                </div> 
            </div>
                
            </div>
            <?php $this->load->view("common/message"); ?>
           
            <div class="table-grid">
            
            <h1 id="table header">Product Stock Table</h1>
            
             <table id="products"><tr><td/></tr></table> 
             <div id="pager"></div>
             </div>
        </div>
             
             <?php $this->load->view("partial/footer"); ?>
        
    </body>   
</html>



<script type="text/javascript">
    function addItem (e){
        console.log(e.which);
        if (e.which == 13){
            var value =  parseInt($("#noOfItems").val());
            value = value + 1;
            $("#noOfItems").val(value);
            $("#stockbarcode").val("");
        }
    }
    $("#stockbarcode").on("keypress",addItem);
    
    
    
</script>


