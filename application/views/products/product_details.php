<html><head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <title> Add product details and generate barcode</title>
  
<!--  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
  <script type="text/javascript" src="http://code.jquery.com/ui/1.8.18/jquery-ui.min.js"></script>
  
  <link rel="stylesheet" type="text/css" href="/css/normalize.css">
  <link rel="stylesheet" type="text/css" href="/css/result-light.css">
  
    
      <link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.0/themes/ui-lightness/jquery-ui.css">
    
    
  
    
    
      <script type="text/javascript" src="http://ajax.microsoft.com/ajax/jquery.validate/1.7/jquery.validate.min.js"></script>-->
    
  <?php $this->load->view("common/header"); ?>
  <link rel="stylesheet" rev="stylesheet" href="<?php echo base_url();?>css/shopifine.css" />
<!--  <style type="text/css">
    body { font-size: 62.5%; }
        label, input { display:block; }
        input.text { margin-bottom:12px; width:95%; padding: .4em; }
        fieldset { padding:0; border:0; margin-top:25px; }
        h1 { font-size: 1.2em; margin: .6em 0; }
        div#users-contain { width: 350px; margin: 20px 0; }
        div#users-contain table { margin: 1em 0; border-collapse: collapse; width: 100%; }
        div#users-contain table td, div#users-contain table th { border: 1px solid #eee; padding: .6em 10px; text-align: left; }
        .ui-dialog .ui-state-error { padding: .3em; }
        .validateTips { border: 1px solid transparent; padding: 0.3em; }

* { font-family: Verdana; font-size: 96%; }
label { width: 10em; float: left; }
label.error { float: none; color: red; padding-left: .5em; vertical-align: top; }
p , .column{ clear: both; }
.submit { margin-left: 12em; }
em { font-weight: bold; padding-right: 1em; vertical-align: top; }


  </style>-->
  


<script type="text/javascript">//<![CDATA[ 

$(function() {
        // a workaround for a flaw in the demo system (http://dev.jqueryui.com/ticket/4375), ignore!
        $( "#dialog:ui-dialog" ).dialog( "destroy" );

         $( "#productForm" ).validate();   
                 
        
        
        $( "#dialog-form" ).dialog({
            autoOpen: false,
            height: 300,
            width: 350,
            position:[300,200],
            modal: true,
            buttons: {
                "Create the Product": function() {
                    bValid =  $("#productForm").valid();
                    
                    if (bValid ){
                        
                        $.ajax({
                            url:"/echo/html/",
                                                    data: { html: "<tr>" +
                                                    "<td>" +  $( "#name" ).val() + "</td>" + 
                                                    "<td>" + $( "#email" ).val() + "</td>" + 
                                                    "<td>" + $( "#password" ).val() + "</td>" +
                                                    "</tr>"},
                            type:"POST",
                            success:function(response)
                            {
                            console.log(response);
                                $( "#users tbody" ).append(response);
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
                
            }
        });


        $( "#create-product" )
            .button()
            .click(function() {
                $( "#dialog-form" ).dialog( "open" );
            });
            
            
            $("#mfr").autocomplete({
                source: <?php echo $mfrs ?>,
                focus: function( event, ui ) {
                $( "#mfr" ).val( ui.item.label );
                return false;  
                },   
                select: function( event, ui ) {                 
                    $( "#mfr" ).val( ui.item.label );                 
                    $( "#mfr-hidden" ).val( ui.item.value );                 
                    return false;             
                }
                
            });
         

            $("#model").autocomplete({
                source: "index.php/products/populateBarcodesForStock",
                focus: function( event, ui ) {
                $( "#model" ).val( ui.item.label );
                return false;  
                },   
                select: function( event, ui ) {                 
                    $( "#model" ).val( ui.item.label );                 
                    $( "#model-hidden" ).val( ui.item.value );                 
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
                            <div class="field">
                                <label for="mfr">Manufacturer:</label>  
                                <input id="mfr" name ="mfr" type="text" class="required"/>

                            </div>
                            <input id="mfr-hidden" name ="mfr-hidden" type="hidden"/>
                        </div>
                        <div class="column">
                            <div class="field">
                                <label for="model">Model:</label>  
                                <input id="model" name ="model" type="text"/>

                            </div>
                            <input id="model-hidden" name ="model-hidden" type="hidden"/>
                        </div>
                 </div>
                
                <div class="row">
                        <div class="column">
                            <div class="field">
                                <label for="categoryOp">Category:</label>  
                                <select name="categoryOp" id ="categoryOp" class="required"> 
                                    <option value=0>Choose 
                                    <?=$categoryOptions?> 
                                </select>

                            </div>
                        </div>
                        <div class="column">
                            <div class="field">
                                <label for="supplierOp">Supplier:</label>  
                                <select name="supplierOp" id ="supplierOp" class="opt required"> 
                                    <option value=0>Choose 
                                    <?=$supplierOptions?> 
                                </select>

                            </div>
                        </div>
                        
                 </div>
                
                <div class="row">
                        <div class="column">
                            <div class="field">
                                <label for="packageOp">Package:</label>  
                                <select name="packageOp" id ="packageOp" class="required"> 
                                    <option value=0>Choose 
                                    <?=$pkgOptions?> 
                                </select>

                            </div>
                        </div>
                        <div class="column">
                            <div class="field">
                                <label for="pkgType">Package Type:</label>  
                                <input id="pkgType" name ="pkgType" type="text"/>

                            </div>
                        </div>
                 </div>
                
                <div class="row">
                        <div class="column">
                            <div class="field">
                                <label for="uomOp">Unit Of Measurement:</label>  
                                <select name="uomOp" id ="uomOp" class="opt required"> 
                                  <option value=0>Choose   
                                </select>

                            </div>
                        </div>
                        <div class="column">
                            <div class="field">
                                <label for="sizeOp">Measurement Denomination:</label>  
                                <select name="sizeOp" id ="sizeOp" class="opt required"> 
                                    <option value=0>Choose 
                                    
                                </select>

                            </div>
                        </div>
                 </div>
               
               
            </fieldset>
        </form>
    </div>
        
    <div id="users-contain" class="ui-widget">
        <h1>Existing Users:</h1>
        <table id="products" class="ui-widget ui-widget-content">
            <thead>
                <tr class="ui-widget-header ">
                    <th>Barcode</th>
                    <th>Product</th>
                    <th>Description</th>
                    <th>Manufacturer</th>
                    <th>Model</th>
                    <th>Supplier</th>
                    <th>Package</th>
                    <th>Package Type</th>

                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
    <button id="create-product">Create new Product</button>
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
                    data:{uom:val},
                url:"index.php/products/populateDenomDropdown",
            success: function(sizeHtml){
                        $("#sizeOp").children('option:not(:first)').remove();
                        $("#sizeOp").append(sizeHtml); 
                }}
        )
            });
               
       
       
    </script>


 