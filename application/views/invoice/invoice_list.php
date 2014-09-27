<html>
    <head>
       <?php $this->load->view("common/header"); ?>
<!--        <script type="text/javascript" src="http://jzaefferer.github.com/jquery-validation/jquery.validate.js"></script>-->
        
         <script>
            $(document).ready(function(){
                 $.validator.addMethod('morethanallowed', function (value, element, param) {
                    var invoiced= $("#invoiced_qty").val();
                    
                    if (value>invoiced){
                        return false;
                    }
                    return true;
                },"Packed Quantity Can Not Be More Than Invoiced");
                
                //dialog
                 $( "#itemForm" ).validate({
                     rules:{pkd_qty:{required:true,
                             morethanallowed:true,
                     digits:true,
                     min:1}}
                 });
//                 
//                 $( "#dialog:ui-dialog" ).dialog( "destroy" );
                
                  $( "#dialog-form" ).dialog({
                    autoOpen: false,
                    height: 'auto',
                    width: 400,
                    position:[400,25],
                    modal: true,
                    buttons: {
                        "Pack": function() {
                           var grid_id =  $(this).data('grid_data').grid_id;
                           var row_id =  $(this).data('grid_data').row_id;
                           var bValid =  $("#itemForm").valid();
                    
                            if (bValid ){
                        
                                $.ajax({
                                    url:"index.php/invoice/pack",
                                    data: {
                                            rowid:row_id,
                                            no_of_items:$("#pkd_qty").val()
                                            },
                                    type:"POST",
                                    success:function(response)
                                    {
                            
                                        $("#"+grid_id).trigger("reloadGrid");
                                        $( this ).dialog( "close" );
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
                        $("#itemForm").data('validator').resetForm();
                        $('#itemForm')[0].reset();
                    },
                    open:function(){
                        var grid_id =  $(this).data('grid_data').grid_id;
                        var row_id =  $(this).data('grid_data').row_id;
                        $("#invoiced_qty").val($("#"+ grid_id).getCell(row_id,'invoiced_number'));
                        $("#pkd_qty").val($("#"+ grid_id).getCell(row_id,'packed_number'));
                    }
                });

                //end dialog
               
               //invoice grid
               
                var myGrid = $("#invoices"),lastsel2;
                
                var settingObj={grid_id:'invoices',pager:'pager',multiselect:true};
                var postData ={_status:'invoiced'};
                var subgridbuttons ={pack_button:true};
                prepareIncomingInvoicesGrid(settingObj,postData,true,null,null,{customButtons:subgridbuttons});
                myGrid.navGrid("#pager",{edit:false,add:false,del:false,search:false},{},{},{},{},{});
                var buttons ={mark_packer:true,assign:true,load_owner_all:true};
                addCustomButtonsIncomingInvoiceGrid(settingObj, buttons)
                
               
                //myGrid.jqGrid('filterToolbar', {stringResult: true, searchOnEnter: true, defaultSearch : "cn"});
                var myGridPkd = $("#packedinvoices"),lastsel2;
                
                myGridPkd.jqGrid({
                    url:'index.php/invoice/populatePackedInvoicesByUser',
                    datatype: 'json',
                    mtype: 'GET',
                    colNames:['Invoice Id','Status','Order Id','Comments'],
                    colModel :[ 
                        {name:'magento_invoice_increment_id', index:'magento_invoice_increment_id', width:80, align:'right'},
                        {name:'status', index:'status', width:140, align:'right'},
                        {name:'magento_order_increment_id',index:'magento_order_increment_id', width:80, align:'right'},
                        {name:'comments', index:'comments', width:80, align:'right'},
                        
                    ],
                    pager: '#pagerPk',
                    rowNum:10,
                    rowList:[5,10,20],
                    sortname: 'id',
                    sortorder: 'desc',
                    viewrecords: true,
                    gridview: true,
                    ignoreCase:true,
                    rownumbers:true,
                    height:'auto',
                    width:680,
                    caption: 'Invoices',
            
                    jsonReader : {
                        root:"invoicedata",
                        page: "page",
                        total: "total",
                        records: "records",
                        cell: "dprow",
                        id: "id"
                    },
                    onSelectRow: function(id){if(id && id!==lastsel2){
                            myGridPkd.restoreRow(lastsel2);
                            //myGridPkd.editRow(id,editparameters);
                            lastsel2=id;
                        }
                    }
                    
                }).navGrid("#pagerPk",{edit:false,add:false,del:false,search:false},{},{},{},{},{});
                myGridPkd.jqGrid('navButtonAdd','#pagerPk',{
                                    caption:"", 
                                    title:"Ship",
                                    buttonicon:"ui-icon-suitcase",
                                    id:"ship_invoices",
                                    onClickButton : function () {
                                                    var selNum = myGrid.getGridParam('selarrrow').length;
                                                    if (selNum==0){
                                                    
                                                    }
                                                    else {$( "#dialog-form" ).dialog( "open" );}
                                                //$( "#dialog-form" ).dialog( "open" );
                                                 } 
                                 });

            });
        $(window).load(function(){
       
        var warningDialogs={one:true,none:true,status:true,exactlyone:true,morethanone:true};
        initDialogs(warningDialogs);
        initPaymentDialog();
        initCommentsForQuote();
        initAssignmentCommon();
        //initErrorDialogs({total:true,advance:true});
        
        //this is from payment dialog.The Select Inout is in dialogs.php
        
    });

        </script>
    </head>
     
    <body>
         <?php $this->load->view("common/menubar"); ?>
        <?php  $this->load->view("common/dialogs"); ?>
        
       
        <div style="display: block;height: auto;" class="shopifine-ui-dialog ui-widget ui-widget-content ui-corner-all" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-dialog-form">
            <div class="table-grid">
                <h1>Invoice To Be Processed</h1>
                <table id="invoices"><tr><td/></tr></table> 
                <div id="pager"></div>
            </div>
            
            <div class="table-grid">
                <h1>Invoices Packed By This User</h1>
                <table id="packedinvoices"><tr><td/></tr></table> 
                <div id="pagerPk"></div>
            </div>
            <div class="ui-dialog-buttonpane shopifine-ui-widget-content ui-helper-clearfix">
                <div class="shopifine-ui-dialog-buttonset">
                    <button id="shipmentBtn" type="button" class="shopifine-ui-button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false">
                        <span class="ui-button-text">Go For Shipment</span>
                </div> 
            </div>
        </div>
        <?php $this->load->view("partial/footer"); ?>
        <div id ="dialog-form" class="inithide" style="overflow: hidden">
            <h1 id="formHeader">Pack Items</h1>
            <form method="post" id="itemForm" name="itemForm">
                <div class="row single-column-row">
                    <div class="column single-column">
                    <div class="field">
                            <label for="invoiced_qty"># Invoiced:</label>  
                            <input id="invoiced_qty" name ="invoiced_qty" type="text" class="required" readonly="readonly"/>  
                        </div> 
                    </div>

                </div>
                <div class="row single-column-row">
                    <div class="column single-column">
                    <div class="field">
                            <label for="pkd_qty"># Packed:</label>  
                            <input id="pkd_qty" name ="pkd_qty" type="text" class="required"/>  
                        </div> 
                    </div>

                </div>
            </form>
        </div>
            <!--
            <?php $this->load->view("common/message"); ?>
            <div class="table-grid">
                <h1 id="table header">Items to be packed</h1>
                <table id="invoices"><tr><td/></tr></table> 
                <div id="pager"></div>
            </div>
           <form method="post" id="invoiceForm" name="invoiceForm">
                   <div class="row single-column-row">
                        <div class="column single-column">
                        <div class="field">
                                <label for="scannedSKU">Scan Item :</label>  
                                <input id="scannedSKU" name ="scannedSKU" type="text" class="required"/>  
                            </div> 
                        </div>

                    </div>
                </form>
                  
           
        </div>-->
    </body>
</html>
<script>
//    $("#shipmentBtn").click(function(){
////        window.location.href="index.php/invoice/ship";
//        $.ajax({
//            url:'index.php/invoice/ship',
//            type:'post',
//            success:function(){
//                
//            }
//            
//        });
//    })

</script>



 