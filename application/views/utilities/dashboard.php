<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE html>
<html>
    <html>
        <head>
            <?php $this->load->view("common/header"); ?>

            <link rel="stylesheet" rev="stylesheet" href="<?php echo base_url(); ?>js/jqplot/jquery.jqplot.css" />
            <script type="text/javascript" src="<?php echo base_url(); ?>js/jqplot/jquery.jqplot.min.js"></script>
            <!--<script type="text/javascript" src="<?php echo base_url(); ?>js/jqplot/plugins/jquery.jqplot.min.js"></script>-->
            <script type="text/javascript" src="<?php echo base_url(); ?>js/jqplot/plugins/jqplot.barRenderer.min.js"></script>
            <script type="text/javascript" src="<?php echo base_url(); ?>js/jqplot/plugins/jqplot.categoryAxisRenderer.min.js"></script>
            <script type="text/javascript" src="<?php echo base_url(); ?>js/jqplot/plugins/jqplot.pointLabels.min.js"></script>
                     <!--<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/shopifine/message.css" />-->
            <style type="text/css">
                .menu_item {
                    display: inline;
                }
            </style>
            <style type="text/css">
                #menubar_admin_navigation {
                    top:49px;
                }
                
                 #content_area {width:100%;}
                
            </style>
            
        </head>

        <body>
            <?php $this->load->view("common/menubar"); ?>
            <div style="display: block;height: auto;" class="shopifine-ui-dialog ui-dialog-medium ui-widget ui-widget-content ui-corner-all" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-dialog-form">
                <h1>Welcome To The Utilities Dashboard</h1>
                <div  class="shopifine-ui-dialog ui-dialog-nested ui-widget ui-widget-content ui-corner-all" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-dialog-form">
                     <h3>You have <?php echo $numdp ?> Warehouses</h3>
                     <div class="ui-dialog-buttonpane button-content-dashboard ui-helper-clearfix">
                        <div class="shopifine-ui-dialog-buttonset">
                            <button id="deliveryPointButton" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"><span class="ui-button-text"> Manage Warehouses </span></button>
                         </div>
                     </div>
                </div>
                <div  class="shopifine-ui-dialog ui-dialog-nested ui-widget ui-widget-content ui-corner-all" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-dialog-form">
                    <h3>You have <?php echo $numpkg ?> Packages</h3>
                     <div class="ui-dialog-buttonpane button-content-dashboard ui-helper-clearfix">
                        <div class="shopifine-ui-dialog-buttonset">
                            <button id="pkgButton" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"><span class="ui-button-text"> Manage Packages </span></button>
                         </div>
                     </div>
                </div>
                <div  class="shopifine-ui-dialog ui-dialog-nested ui-widget ui-widget-content ui-corner-all" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-dialog-form">
                     <h3>You have <?php echo $nummfr ?> Manufacturers and <?php echo $nummodel ?> Models</h3>
                     <div class="ui-dialog-buttonpane button-content-dashboard ui-helper-clearfix">
                        <div class="shopifine-ui-dialog-buttonset">
                            <button id="mfrButton" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"><span class="ui-button-text"> Manage Manufacturers And Models </span></button>
                         </div>
                     </div>
                </div>
                <div  class="shopifine-ui-dialog ui-dialog-nested ui-widget ui-widget-content ui-corner-all" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-dialog-form">
                    <h3>You have <?php echo $numuom ?> Units of Measurements</h3>
                     <div class="ui-dialog-buttonpane button-content-dashboard ui-helper-clearfix">
                        <div class="shopifine-ui-dialog-buttonset">
                            <button id="uomButton" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"><span class="ui-button-text"> Manage UOMs </span></button>
                         </div>
                     </div>
                </div>
            </div>
            <!--        <div id="chart1" style="height:400px;width:600px; "></div>​-->
            <!--        <div>
                        <ul>
                            <li>
                                <a href="index.php/utilities/loadMfrModel">Create Manufacturer or Model</a>
                            </li>
                            <br/>
                            <li>
                                <a href="index.php/utilities/loadDeliveryPoint">Create Delivery Point</a>
                            </li>
                            <br/>
                            <li>
                                <a href="index.php/utilities/loadMeasurementUnit">Create Units of Measurement</a>
                            </li>
                            <br/>
                            <li>
                                <a href="index.php/utilities/loadPackage">Create Package</a>
                            </li>
                             
                        </ul>
                    </div>-->
            <?php $this->load->view("partial/footer"); ?>

        </body>
    </html>
    <script>
//    $("#content_area").addClass("content-area-extra-wide");

 $("#deliveryPointButton").click(function (){
        document.location.href="index.php/utilities/loadDeliveryPoint";
    });
    $("#mfrButton").click(function (){
        document.location.href="index.php/utilities/loadMfrModel";
    });
    $("#uomButton").click(function (){
        document.location.href="index.php/utilities/loadMeasurementUnit";
    });
    $("#pkgButton").click(function (){
        document.location.href="index.php/utilities/loadPackage";
    });
</script>
