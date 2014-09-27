<html>
    <head>
       <?php $this->load->view("common/header"); ?>
<!--        <script type="text/javascript" src="http://jzaefferer.github.com/jquery-validation/jquery.validate.js"></script>-->
        <link rel="stylesheet" rev="stylesheet" href="<?php echo base_url();?>css/shopifine.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>css/ui.jqgrid.css" />
        <script src="<?php echo base_url(); ?>js/i18n/grid.locale-en.js" type="text/javascript"></script>
        <script src="<?php echo base_url(); ?>js/jquery.jqGrid.min.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
         <script>
            $(document).ready(function(){
                //validator
                 $.validator.addMethod("invoiceRequired", function(value, element) {
                    var length = JSON.parse($("#selectedInv").val()).length;
                    if (length==0){
                        return false;
                    }
                    return true;

                }, "Select Invoices From The List To Complete Shipping");
                
                $("#shipmentform").validate({
                    errorPlacement: function(error,element){
                        if(element.attr("name") == "selectedInv"){ 
                            var errorStatus = "Select Invoices From The List To Complete Shipping";
                            showErrorMessage(errorStatus);
                        } 
                        else {     
                            error.insertAfter(element);   
                        }
                    },
                    
                    rules: {
                        selectedInv: {
                            invoiceRequired: true
                        }
                    },
                    messages:{
                        deliveryVehicleDD:"Please Select A Delivery Vehicle",
                        deliveryPointDD:"Please Select A Delivery Point"
                    }

                });
                
                //shipment grid
                
                var myGrid = $("#invoices"),lastsel2;
                
                myGrid.jqGrid({
                    url:'index.php/invoice/populateShipmentReadyInvoices',
                    datatype: 'json',
                    mtype: 'GET',
                    colNames:['Invoice Id','Status','Order Id','Comments'],
                    colModel :[ 
                        {name:'magento_invoice_increment_id', index:'magento_invoice_increment_id', width:80, align:'right'},
                        {name:'status', index:'status', width:140, align:'right'},
                        {name:'magento_order_increment_id',index:'magento_order_increment_id', width:80, align:'right'},
                        {name:'comments', index:'comments', width:80, align:'right'}
                        
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
                    height:'auto',
                    width:680,
                    caption: 'Invoices Ready For Shipping',
                    multiselect:true,
                    jsonReader : {
                        root:"invoicedata",
                        page: "page",
                        total: "total",
                        records: "records",
                        cell: "dprow",
                        id: "id"
                    }
                       
                }).navGrid("#pager",{edit:false,add:false,del:false,search:false},{height:280,reloadAfterSubmit:false,closeAfterEdit:true,recreateForm:true,checkOnSubmit:true},{},{},{},{});
               
                $("#confirm-btn").click(function (){
                    $("#selectedInv").val(JSON.stringify((myGrid.getGridParam('selarrrow'))));
                    $("#shipmentform").submit();
   })
            });
        </script>
    </head>
     
    <body>
         <?php $this->load->view("common/menubar"); ?>
        <p><?php echo $total ?></p>
        <div style="display: block;height: auto;" class="shopifine-ui-dialog ui-widget ui-widget-content ui-corner-all" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-dialog-form">
            <div class="form-container">
                <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
                    <span class="ui-dialog-title" id="ui-dialog-title-dialog-form">&nbsp;</span>
                    <a href="#" class="ui-dialog-titlebar-close ui-corner-all" role="button">

                    </a>
                </div>
                <div  class="ui-dialog-content" style="width: auto; min-height: 65.1333px; height: auto;" scrolltop="0" scrollleft="0">
                    <h1 id="formHeader">Select Delivery Point and Delivery Vehicle</h1> 
                    <form id="shipmentform" action="index.php/invoice/confirmation" method="post">
                        <fieldset>
                            <div class="row">
                                <div class="column">
                                <div class="field">
                                        <label for ="deliveryPointDD"> Delivery Point</label>
                                        <select id="deliveryPointDD" name="deliveryPointDD" class="required"> 
                                            <option value="">Choose 
                                            <?=$options?> 
                                        </select>
                                    </div> 
                                </div>
                                <div class="column">
                                    <div class="field">
                                        <label for ="deliveryVehicleDD"> Delivery Vehicle</label>
                                        <select id="deliveryVehicleDD" name="deliveryVehicleDD" class="required"> 
                                            <option value="">Choose 
                                            <?=$optionsVehicle?> 
                                        </select> 
                                    </div>
                                </div>
                            </div>
                            
                            <input id="selectedInv" name="selectedInv" type="hidden"/>
                        </fieldset>
                    </form>
                </div>
                
            </div>
            <?php $this->load->view("common/message"); ?>
            <div class="table-grid">
                <h1 id="table header">Shipment Ready Invoices</h1>
                <table id="invoices"><tr><td/></tr></table> 
                <div id="pager"></div>
            </div>
            <div id="test"> test </div>
            <div class="shopifine-ui-dialog-buttonset">
                <input id="confirm-btn" type="submit" class="shopifine-ui-button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" value="Confirm"/>
            </div>
        </div>
        <?php $this->load->view("partial/footer"); ?>
    </body>
</html>


 