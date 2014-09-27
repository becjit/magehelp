<html><head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <title> Add product details and generate barcode</title>
  <?php $this->load->view("common/header"); ?>
<!--  <link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.0/themes/ui-lightness/jquery-ui.css">-->
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url();?>css/ui.jqgrid.css" />
<!--    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>-->
    <script src="<?php echo base_url();?>js/i18n/grid.locale-en.js" type="text/javascript"></script>
    <script src="<?php echo base_url();?>js/jquery.jqGrid.min.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
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
* { font-family: Verdana; font-size: 98%; }
em {color:red;}
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
  
  


  


<script type="text/javascript">
    
    
    
    //<![CDATA[ 

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
                 
        
        
        $( "#dialog-form" ).dialog({
            autoOpen: false,
            height: 'auto',
            width: 450,
            position:[400,25],
            modal: true,
            buttons: {
                "Scan and update stock": function() {
                   var bValid =  $("#productForm").valid();
                    
                    if (bValid ){
                        
                        $.ajax({
                            url:"index.php/products/updateStocksByBarcode",
                            data: {
                                stock_add: $("#noOfItems").val(),
                                barcode: $("#stockbarcode-hidden").val() },
                                
                            type:"POST",
                            success:function(response)
                            {
                            
                                myGrid.trigger("reloadGrid");
                            }
                        });
                        
                        
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
            myGrid.trigger("reloadGrid");
            return true;
        },
	"aftersavefunc" : null,
	"errorfunc": null,
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
            {name:'stock', index:'stock', width:80, align:'right',editable:false,editoptions:{size:"20",maxlength:"30"}},
            {name:'stock_add', index:'stock_add', width:80, align:'right',editrules:{custom:true,custom_func:negativeValCheck},editable:true,editoptions:{size:"20",maxlength:"30"}},
            {name:'isactive', index:'isactive', width:80, align:'right',editable:false,edittype:"select", formatter:'select', editoptions:{value:"1:Yes;0:No"}},
            
            
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
        
        
        
        //myGrid.jqGrid('filterToolbar', {stringResult: true, searchOnEnter: false, defaultSearch : "cn"});
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
                    if (hiddenVal!= null){
                        if (hiddenVal != ui.item.label){
                            console.log ("hiddenval = " + hiddenVal);
                            console.log ("ui.item.label = " + ui.item.label);
                            $("#noOfItems").val(0);
                        }
                    }
                    
                    $( "#stockbarcode-hidden" ).val( ui.item.value );                 
                    return false;             
                }
            }); 
            
    });
    
   

 
//]]>  

</script>


</head>
 <body>
         <?php  $this->load->view("common/menubar"); ?>
        <div id ="dialog-form">
        <h1 id="formHeader">Add New Product Entity</h1>   
        <form id="productForm">
            <fieldset>
                
                
                 <div class="row">
                        <div class="column">
                            <div class="ui-widget field">
                                <label for="stockbarcode">Scan/Input Barcode:</label><em>*</em>  
                                <input id="stockbarcode" name ="stockbarcode" type="text"/>

                            </div>
                            <input id="stockbarcode-hidden" name ="stockbarcode-hidden" type="hidden"/>
                        </div>
                        
                        <div class="column">
                            <div class=" ui-widget field">
                                <label for="noOfItems">Items Scanned:</label><em>*</em>    
                                <input id="noOfItems" name ="noOfItems" type="text" value="0" class="required"/>

                            </div>
                            
                        </div>
                 </div>
               
               
            </fieldset>
        </form>
    </div>
        

        
        <table id="products"><tr><td/></tr></table> 
        <div id="pager"></div> 
<!--    </div>-->
    <button id="create-product">Update Stocks</button>
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
        $("#stockbarcode").on("keypress",addItem)
    </script>


 