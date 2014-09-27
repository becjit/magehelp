<html>
    <head>
       <?php $this->load->view("common/header"); ?>
<!--        <script type="text/javascript" src="http://jzaefferer.github.com/jquery-validation/jquery.validate.js"></script>-->
        <link rel="stylesheet" rev="stylesheet" href="<?php echo base_url();?>css/shopifine.css" />
        <script>
            $(document).ready(function(){
                $.validator.addMethod('notZero', function(value, element) {
                    return (value != '0');
                }, 'Please select an option.');
                
                $("#packagingForm").validate({
                    onfocusout: false,
                    
                                rules: {
                        packageOptions: {
                            notZero: true
                        },
                        uomOp: {
                            notZero: true
                        }
                    }
                });
            });
            
            
        </script>
        <script type="text/javascript">
                $(function() {
        
        
        $( "#dialog-form" ).dialog({
            autoOpen: false,
            height: 200,
            width: 300,
            modal: true,
            buttons: {
                "Create package type": function() {
                    var value = $("#nameType").val();
                    if ( value!= ""){
                        $("#typeOp").append("<OPTION VALUE=\"" + value + "\">" + value); 
                        $( this ).dialog( "close" );
                }
                    
                },
                Cancel: function() {
                    $( this ).dialog( "close" );
                }
            },
            close: function() {
                //allFields.val( "" ).removeClass( "ui-state-error" );
            }
        });


        $( "#newTypeDefLink" )
            .click(function() {
                $( "#dialog-form" ).dialog( "open" );
            });
    });        

        </script>
    </head>
     
    <body onload="init()">
         <?php  $this->load->view("common/menubar"); ?>
        <div class="row">
            <a id="packLink" href="#">Define A New Package</a>
            <a id="exPackLink" href="#" style="display:none">Modify Existing Package</a>
        </div>
        <div style="display: block;height: auto;" class="shopifine-ui-dialog ui-widget ui-widget-content ui-corner-all" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-dialog-form">
            <div class="form-container">
                <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
                    <span class="ui-dialog-title" id="ui-dialog-title-dialog-form">&nbsp;</span>
                    <a href="#" class="ui-dialog-titlebar-close ui-corner-all" role="button">

                    </a>
                </div>
                <div  class="ui-dialog-content" style="width: auto; min-height: 65.1333px; height: auto;" scrolltop="0" scrollleft="0">
                    <h1 id="formHeader"> Add new UOM to Existing Package</h1>   
                    <form action="index.php/utilities/createPackagingUnits" method="post" id="packagingForm">
                        <div class="row">
                            <div id="packDiv">
                                <div class="column">
                                    <label for="packageOptions">Available Package Types:</label>  
                                    <select name="packageOptions" id ="packageOptions" class="opt required"> 
                                            <option value=0>Choose 
                                            <?=$options?> 
                                    </select>
                                </div>
                                <div class="column"> 
                                    <div id="typeCon" style="display:none" class="field">
                                        <label for="packtype">Package Type: </label>  
                                        <input id="packtype" name ="packtype" type="text"/>  
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="display:none">
                            <div  class="packContainer">
                                <div class="column">
                                    <div class="field">
                                        <label for="namePkg">Name of Packaging:</label>  
                                        <input id="namePkg" name ="namePkg" type="text" class="required"/>

                                    </div>
                                </div>
                                <div class="column">
                                    <div class="field">
                                        <label for="desc">Description:</label>  
                                        <input id="desc" name ="desc" type="text"/>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="display:none">
                            <div  id ="existTypeContainer" class="packContainer">
                                <div class="column">
                                    <div class="field">
                                        <label for="typeOp">Type :</label>  
                                        <select name="typeOp" id="typeOp"> 
                                                <option value=0>Choose 
                                                <?=$typeOptions?> 
                                        </select>
                                        <a id ="newTypeDefLink"  title="Add new type"><img src="<?php echo base_url().'images/shopifine/plus.jpg';?>" border="0" alt="Add new type"/></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div id="existingUOMContainer">
                                <div class="column">
                                    <div class="field">
                                        <label for="uomOp">Choose Unit</label>  
                                        <select name="uomOp" id="uomOp"> 
                                                <option value=0>Choose 
                                                <?=$uomOptions?> 
                                            </select>
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="field">
                                        <label for="sizeOp">Choose Size</label>  
                                        <select name="sizeOp" id="sizeOp"> 
                                        </select>
                                    </div>   
                                </div>
                            </div>

                        </div>

                        <div class="row" style="display:none">
                            <div id="newUOMContainer">
                                <div class="column">
                                    <div class="field">
                                        <label for="uomIp">Define Unit : </label>  
                                        <input id="uomIp" name ="uomIp" type="text" class="required"/>
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="field">
                                        <label for="denomIp">Denomination : </label>  
                                        <input id="denomIp" name ="denomIp" type="text" class="required"/>
                                    </div>   
                                </div>
                            </div>
                        </div>
                        <div class="row">
                        <div class="column" id="newMeasurementTypeLinkContainer">
        <!--                         <div class="field">
                                    <label for="uom">Define Unit : </label>  
                                    <input id="uom" name ="uom" type="text" class="required"/>
                                </div>-->
                                <h6> Didn't Find The Right Unit of Measurement? </h6>
                                <a id="newMeasurementTypeLink" href="#">Define A New Measurement Unit</a>
                            </div>

                        </div>
                        <div class="row" style="display:none">
                            <div class="column" id="existingMeasurementTypeLinkContainer">
        <!--                      <div class="field">
                                    <label for="uom">Define Unit : </label>  
                                    <input id="uom" name ="uom" type="text" class="required"/>
                                </div>-->
                                <h6> Want to check Existing Measurement Types </h6>
                                <a id="existMeasurementTypeLink" href="#">Show Existing Measurement Types</a>
                        </div>

                        </div>
                    </form>
                </div>
                <div class="row">
                    <input type="submit" value="Submit"/>
                </div>
            </div>
        </div>
  
    <div id="dialog-form" title="Create New Type">
        <form>
            <label for="nameType">Name of Type: </label>
            <input type="text" name="nameType" id="nameType" class="text ui-widget-content ui-corner-all" />
        </form>
    </div>
</body>   
</html>

<script type="text/javascript">
    
        function init(){
            var options = '<?php echo $options ?>';
            if (!options){
            $("#packDiv").hide();
            }
            
        }
        
        
        
       
    </script>
    
    <script>
        $("#packLink").click (function(event){
        event.preventDefault();
            if ($(".packContainer").parent().is(":hidden")){
                $("#packagingForm").data('validator').resetForm();
                $(".packContainer").parent().slideDown("fast");
                $("#existingTypeContainer").parent().slideDown("fast");
                $("#packDiv").parent().slideUp("fast");
                $("#formHeader").html("Define New Pakage");
                $("#packLink").hide();
                $("#exPackLink").show();
            }
        });
        
        $("#exPackLink").click (function(event){
        event.preventDefault();
            if ($("#packDiv").parent().is(":hidden")){
                $("#packagingForm").data('validator').resetForm();
                $("#packDiv").parent().slideDown("fast");
                $(".packContainer").parent().slideUp("fast");
                 $("#existingTypeContainer").parent().slideUp("fast");
                $("#measurementTypeLink").parent().slideUp("fast");
                $("#formHeader").html("Add new UOM to Existing Package");
                $("#packLink").show();
                $("#exPackLink").hide();
            }
        });
        
         $("#existMeasurementTypeLink").click (function(event){
        event.preventDefault();
            if ($("#existingUOMContainer").parent().is(":hidden")){
                
                $("#existingUOMContainer").parent().slideDown("fast");
                $("#newUOMContainer").parent().slideUp("fast");
                 
                $("#newMeasurementTypeLinkContainer").parent().show();
                $("#existingMeasurementTypeLinkContainer").parent().hide()
            }
        });
        
         $("#newMeasurementTypeLink").click (function(event){
        event.preventDefault();
            if ($("#newUOMContainer").parent().is(":hidden")){
                $("#newUOMContainer").parent().slideDown("fast");
                $("#existingUOMContainer").parent().slideUp("fast");
                 
                $("#existingMeasurementTypeLinkContainer").parent().show();
                $("#newMeasurementTypeLinkContainer").parent().hide();
            }
        });
        
        
        
        
         $(".opt").change(function(event){
        event.preventDefault();
        var optionsMap =jQuery.parseJSON('<?php echo $packageMap ?>');
        
        var type = optionsMap[$(this).val()];
        $("#typeCon").show();
        $("#packtype").val(type);
        $("#packtype").prop('disabled',true);
        
        
        });
        
        $("#uomOp").change(function(){
            var val = $(this).val();
            $.ajax({type:"post",
                    url:"index.php/utilities/loadSize/"+val,
                    success: function(sizeHtml){
                        $("#sizeOp").children().remove();
                        $("#sizeOp").append(sizeHtml); 
                    }});
        })
        
    </script>


 