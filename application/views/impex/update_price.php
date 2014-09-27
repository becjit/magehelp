<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE html>
<html>
    <html>
        <head>
            <?php $this->load->view("common/header"); ?>
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
   
                    var myGrid = $("#product_price");
               
                    myGrid.jqGrid({
                        url:'index.php/impex/populateProductsPrice',
                        datatype: 'json',
                        mtype: 'GET',
                        colNames:['Barcode','Product Name','Total Cost Inventory','Inventory Qty','Avg Cost Price','VAT',
                        /*'Salex Tax',*/'Margin Type','Margin Value','Final Price'],
                        colModel :[ 
//                            {name:'product_id', index:'product_id', align:'right',hidden:true},
                            {name:'barcode', index:'barcode', width:150, align:'right',editable:false},
                            {name:'product_name', index:'product_name', width:150, align:'right',editable:false},
                            {name:'inv_total_cost', index:'inv_total_cost', width:120, align:'right'},
                            {name:'total_balance', index:'total_balance', width:80, align:'right',editable:false},
                            {name:'inv_cost_price', index:'inv_cost_price', align:'right',width:70},
                            {name:'vat', index:'vat', width:70, align:'right'},
//                            {name:'sales_tax', index:'sales_tax', width:70, align:'right',editable:false},
                            {name:'margin_type', index:'margin_type', align:'right',width:70},
                            {name:'margin_value', index:'margin_value', width:80, align:'right'},
                            {name:'default_calculated', index:'default_calculated', width:70, align:'right'}

                        ],
                        pager: '#pager',
                        rowNum:10,
                        rowList:[10,20,50],
                        sortname: 'product_id',
                        sortorder: 'desc',
                        viewrecords: true,
                        gridview: true,
                        ignoreCase:true,
                        rownumbers:true,
                        height:'auto',
                        
                        multiselect:true,
                        caption: 'Price Calculation Table',

                        jsonReader : {
                            root:"productstockdata",
                            page: "page",
                            total: "total",
                            records: "records",
                            cell: "dprow",
                            id: "product_id"
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
                        url:'index.php/impex/createExportPriceFile',
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
       
                 
                });
            </script>
        </head>

        <body>
            <?php $this->load->view("common/menubar"); ?>
            <div id="dialog-confirm" title="Warning" class="inithide">
            <p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>
                Please See The Console To See If  Processing Complete.Sync Only When The Processing Is Over;Else System Will Be Inconsistent and Sync May Not Work. Is Processing Over?</p>
            </div>
             
            <div id="mainCntnr" style="display: block;height: auto;" class="shopifine-ui-dialog ui-dialog-medium ui-widget ui-widget-content ui-corner-all" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-dialog-form">
                <div class="table-grid">
                    <h1 id="table-header-price">Product Price</h1>
                    <table id="product_price"><tr><td/></tr></table> 
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
                            <div class="ui-dialog-buttonpane shopifine-ui-widget-content ui-helper-clearfix">
                            <div class="shopifine-ui-dialog-buttonset">
                                <input id="magmiBtn" type="submit" class="shopifine-ui-button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false" value="Process For  Magento">
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
            </div>
             
            <?php $this->load->view("partial/footer"); ?>

        </body>
    </html>
    <script>
        function setOption(){
        $("#mode").val("xcreate");
        }
    </script>
    