<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE html>
<html>
    <html>
        <head>
            <?php $this->load->view("common/header"); ?>

            <!--<link rel="stylesheet" rev="stylesheet" href="<?php echo base_url(); ?>js/jqplot/jquery.jqplot.css" />-->
<!--            <script type="text/javascript" src="<?php echo base_url(); ?>js/jqplot/jquery.jqplot.min.js"></script>
            <script type="text/javascript" src="<?php echo base_url(); ?>js/jqplot/plugins/jquery.jqplot.min.js"></script>
            <script type="text/javascript" src="<?php echo base_url(); ?>js/jqplot/plugins/jqplot.barRenderer.min.js"></script>
            <script type="text/javascript" src="<?php echo base_url(); ?>js/jqplot/plugins/jqplot.categoryAxisRenderer.min.js"></script>
            <script type="text/javascript" src="<?php echo base_url(); ?>js/jqplot/plugins/jqplot.pointLabels.min.js"></script>-->
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
            <script>
                function checkStatus(){
                       var error= '<?php echo $error ?>';
                       if (error=="true"){
                           $("#mainCntnr").hide();
                           $("#forceMessageCntnr").show();
                       }
                       
                    }
                $(function() {
                    
                    var idsOfSelectedRows =[];
                    var updateIdsOfSelectedRows = function (id, isSelected) {
                        var contains = $.inArray(id, idsOfSelectedRows)
                            //idsOfSelectedRows.contains(id);
                        if (!isSelected && contains>-1) {
                            for(var i=0; i<idsOfSelectedRows.length; i++) {
                                if(idsOfSelectedRows[i] == id) {
                                    idsOfSelectedRows.splice(i, 1);
                                    break;
                                }
                            }
                        }
                        else if (contains==-1) {
                            idsOfSelectedRows.push(id);
                        }
                        console.log(idsOfSelectedRows);
                };
   
                    var myGrid = $("#product_stock");
               
                    myGrid.jqGrid({
                        url:'index.php/impex/populateProductsStocksToExport',
                        datatype: 'json',
                        mtype: 'GET',
                        colNames:['Barcode','Product ID','Stocks To Add'],
                        colModel :[ 
                            
                            {name:'barcode', index:'barcode', width:200, align:'right',editable:false},
                            {name:'product_id', index:'product_id', align:'right',hidden:true},
                            {name:'balance_to_update', index:'balance_to_update', width:100, align:'right'},
                            

                        ],
                        pager: '#pager',
                        rowNum:5,
                        rowList:[5,10,50],
                        sortname: 'id',
                        sortorder: 'desc',
                        viewrecords: true,
                        gridview: true,
                        ignoreCase:true,
                        rownumbers:true,
                        height:'auto',
                        width:250,
                        multiselect:true,
                        caption: 'Inventory To Be Updated',

                        jsonReader : {
                            root:"productstockdata",
                            page: "page",
                            total: "total",
                            records: "records",
                            cell: "dprow",
                            id: "id"
                        },
                        onSelectRow: function(rowid, status){
                            updateIdsOfSelectedRows(rowid, status);
                        },
                        onSelectAll: function (aRowids, status) {
                            var i, count, id;
                            for (i = 0, count = aRowids.length; i < count; i++) {
                                id = aRowids[i];
                                updateIdsOfSelectedRows(id, status);
                            }
                        }

                    }).navGrid("#pager",{edit:false,add:false,del:false,search:false},
                   /* edit Option*/ {},
                /* Add Option*/     {},
                                                     {},{},{});
                                                     
        myGrid.jqGrid('navButtonAdd','#pager',{
            caption:"", 
            title:"Create CSV To Begin Export Process",
            buttonicon:"ui-icon-newwin",
            
            onClickButton : function () { 
                var rowid = myGrid.jqGrid('getGridParam', 'selarrrow');
                var noOfRows = rowid.length;
                console.log(rowid);
               
                if (noOfRows == 0){
                    $( "#modal-warning-none" ).dialog("open");
                }
                else{
                    $(".inithide").hide();
                    $.ajax({
                        url:'index.php/impex/createExportStockFile',
                        method:'post',
                        data:{selected:idsOfSelectedRows},
                        success:function(response){
                            var responseObj = JSON.parse(response);
                            $(".inithide").hide();
                            if (responseObj.status == "success"){
                                $("#successCntnr").slideDown("slow");
                                $("#success-text").text(responseObj.message);
                                $("#selected_ids_hidden").val(idsOfSelectedRows);
                            }
                            else if (responseObj.status == "error"){
                                $("#failureCntnr").slideDown("slow");
                                $("#failure-text").text(responseObj.message);
                                $("#selected_ids_hidden").val("");
                            }
                            
                        },
                        error:function(response){
                            var responseObj = JSON.parse(response);
                            $(".inithide").hide();
                           
                                $("#failureCntnr").show();
                                $("#failure-text").text("File Could Not Ve Exported Due To Internal Error");
                                $("#selected_ids_hidden").val("");
                            }
                    })
                }
                
            }
        });
        $("#magmiBtn").click(function(){
            $("#syncBtn").show()
            $.ajax({
                        url:'index.php/impex/syncNeeeded',
                        method:'post',
                        data:{selected:$("#selected_ids_hidden").val()}
            });
        })
        $("#syncBtn").click(function(){
          
             $( "#dialog-confirm" ).dialog("open");
        });
         $("#forceSyncBtn").click(function(){
          
             $.ajax({
                           url:'index.php/impex/doSync',
                           method:'get',
                           
                           success:function (response){
                               var responseObj = JSON.parse(response);
                               if (responseObj.status=="success"){
                                   $("#mainCntnr").show();
                                   $("#forceMessageCntnr").hide();
                                   $("#success-text").text("Sync Successful");
                                   
                               }
                               else{
                                   $("#force-text").text("Force Sync Failed");
                               }
                                 
                               
                           },
                           error:function (response){
                               $("#force-text").text("Force Sync Failed");
                               
                           }
                       });
        })
        $( "#dialog-confirm" ).dialog({
            resizable: false,
            height:200,
             width:500,
            modal: true,
            autoOpen:false,
            buttons: {
                "Confirm": function() {
                     $.ajax({
                           url:'index.php/impex/doSync',
                           method:'get',
                           
                           success:function (response){
                               
                                 $("#success-text").text("Sync Completed");
                               
                           },
                           error:function (response){
                               $("#success-text").text("Sync Failed");
                               
                           }
                       });
                       $( this ).dialog( "close" );
                    
                },
                Cancel: function() {
                    $( this ).dialog( "close" );
                }
            }
        });
                 
                });
            </script>
        </head>

        <body onload="return checkStatus();">
            <?php $this->load->view("common/menubar"); ?>
            <div id="dialog-confirm" title="Warning" class="inithide">
            <p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>
                Please See The Console To See If  Processing Complete.Sync Only When The Processing Is Over;Else System Will Be Inconsistent and Sync May Not Work. Is Processing Over?</p>
            </div>
             <?php //if (!$error): ?>
            <div id="mainCntnr" style="display: block;height: auto;" class="shopifine-ui-dialog ui-dialog-medium ui-widget ui-widget-content ui-corner-all" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-dialog-form">
                <div class="table-grid">
                    <h1 id="table header">Product Stock</h1>
                    <table id="product_stock"><tr><td/></tr></table> 
                    <div id="pager"></div>
                </div>
               
                <div id="successCntnr" class="inithide">
                    <h1>Export To CSV For New Product Creation  Completed</h1>
                    <h3 id="success-text"></h3>
                     <div  class="ui-dialog-content" style="width: auto; min-height: 65.1333px; height: auto;" scrolltop="0" scrollleft="0">
    <!--                    <h1 id="formHeader">Add Delivery Point</h1> -->
                        <form id="deliverypointform" target="iframeMagmi" method="post" action="<?php echo $server_url ?>/magmi/web/magmi.php">
                           <input type="hidden" name="logfile" value="progress.txt"/>
                            <input type="hidden" name="mode" value="<?php echo $mode ?>"/>
                            <input type="hidden" name="profile" value="<?php echo $profile ?>"/>
                            <input type="hidden" name="run" value="import"/>
                            <input id ="selected_ids_hidden" type="hidden" name="selected_ids_hidden" value="import"/>
                            <div class="ui-dialog-buttonpane shopifine-ui-widget-content ui-helper-clearfix">
                            <div class="shopifine-ui-dialog-buttonset">
                                <input id="magmiBtn" type="submit" class="shopifine-ui-button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false" value="Process For  Magento">
                                <input id="syncBtn"  class="inithide shopifine-ui-button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false" value="Sync After Process Completes!!">
                            </div> 
                        </div>
                        </form>
                    </div>


                   <iframe id="iframeMagmi" name="iframeMagmi" style="height:900px;width:100%" frameborder="no">
                   </iframe>
                </div>
                
               <?php //else: ?>
               <div id="faliureCntnr" class="inithide">
                   <h1 id ="failure-text"></h1>
               </div>
               
               <?php //endif; ?>
            </div>
             <?php //else: ?>
                <div id="forceMessageCntnr" style=" height: auto;" class="inithide shopifine-ui-dialog ui-dialog-medium ui-widget ui-widget-content ui-corner-all" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-dialog-form">
                   <h3 id ="force-text">Sync Was Not Properly Done. You Can Not Update Stock Before The Previous Sync Is Done Properly. Please Do Force Sync And System Will Try To Resolve The Issue Else ConTact Admin </h3>
                   <div class="shopifine-ui-dialog-buttonset">
                    <input id="forceSyncBtn"  class="shopifine-ui-button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false" value="Force Sync!!">
                    </div> 
               </div>
                 
             <?php //endif; ?>
<!--             <div style="display: block;height: 100%;left:0em; overflow: hidden;" class="shopifine-ui-dialog  ui-widget ui-widget-content ui-corner-all" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-dialog-form">
                <?php //$this->load->view("common/message"); ?>
                
            </div>-->
            <?php $this->load->view("partial/footer"); ?>

        </body>
    </html>
    <script>
        function setOption(){
        $("#mode").val("xcreate");
        }
    </script>
    