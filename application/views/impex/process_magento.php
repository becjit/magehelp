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
                
                 #content_area {width:100%;height:auto;}
                 .logoauth {color:green;}
                .shopifine-ui-dialog-buttonset {
                    float: left;
                    margin: 1em;
                    padding-left: 2em;
                }
                
                .shopifine-ui-widget-content {
                    border:none;
                }
            </style>
            
        </head>

        <body>
            <?php $this->load->view("common/menubar"); ?>
            <div style="display: block;height: auto;" class="shopifine-ui-dialog ui-dialog-medium ui-widget ui-widget-content ui-corner-all" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-dialog-form">
                <?php if (!$error): ?>
                <h1>Export To CSV For New Product Creation  Completed</h1>
                <h3>The File Created Is '<?php echo $filename ?>'</h3>
                 <div  class="ui-dialog-content" style="width: auto; min-height: 65.1333px; height: auto;" scrolltop="0" scrollleft="0">
<!--                    <h1 id="formHeader">Add Delivery Point</h1> -->
                    <form id="deliverypointform" target="iframeMagmi" method="post" action="http://localhost/magmi/web/magmi.php">
                       <input type="hidden" name="logfile" value="progress.txt"/>
                        <input type="hidden" name="mode" value="<?php echo $mode ?>"/>
                        <input type="hidden" name="profile" value="<?php echo $profile ?>"/>
                        <input type="hidden" name="file" value="/home/abhijit/test1"/>
                        <input type="hidden" name="run" value="import"/>
                        <div class="ui-dialog-buttonpane shopifine-ui-widget-content ui-helper-clearfix">
                        <div class="shopifine-ui-dialog-buttonset">
                            <input id="magmiBtn" type="submit" class="shopifine-ui-button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false" value="Process For  Magento">
                                
                        </div> 
                    </div>
                    </form>
                </div>
                

               <iframe id="iframeMagmi" name="iframeMagmi" style="height:900px;width:100%" frameborder="no">
               </iframe>
               <?php else: ?>
               <h1>Export To CSV For New Product Creation Failed</h1>
               <?php endif; ?>
            </div>
           
            <?php $this->load->view("partial/footer"); ?>

        </body>
    </html>
    <script>
        function setOption(){
        $("#mode").val("xcreate");
        }
    </script>
    