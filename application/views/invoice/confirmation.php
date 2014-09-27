<html>
     <head>
       <?php $this->load->view("common/header"); ?>
<!--        <script type="text/javascript" src="http://jzaefferer.github.com/jquery-validation/jquery.validate.js"></script>-->
        <link rel="stylesheet" rev="stylesheet" href="<?php echo base_url();?>css/shopifine.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>css/ui.jqgrid.css" />
        <script src="<?php echo base_url(); ?>js/i18n/grid.locale-en.js" type="text/javascript"></script>
        <script src="<?php echo base_url(); ?>js/jquery.jqGrid.min.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
        <style>
            .ui-button{ margin:.5em;}
        </style>
         <script>
             var isCancel = false;
             
             
            $(document).ready(function(){
                
               //shipping grid
               
                var myGrid = $("#invoices"),lastsel2;
                
                
                myGrid.jqGrid({
                    url:'index.php/invoice/populateShippedInvoices',
                    datatype: 'json',
                    mtype: 'POST',
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
                    caption: 'Shipped Invoices',
                    postData:{trackingNumber: '<?php echo $trackingNumber ?>'},
                    jsonReader : {
                        root:"invoicedata",
                        page: "page",
                        total: "total",
                        records: "records",
                        cell: "dprow",
                        id: "id"
                    }
                })
                })
        </script>
    </head>

    <body onload="showTrackingNumber()"> 
         <?php $this->load->view("common/menubar"); ?>
       
       <div style="display: block;height: auto;" class="shopifine-ui-dialog ui-widget ui-widget-content ui-corner-all" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-dialog-form">
            
            <?php $this->load->view("common/message"); ?>
            <div class="table-grid">
                <h1 id="table header">Shipped Invoices</h1>
                <table id="invoices"><tr><td/></tr></table> 
                <div id="pager"></div>
            </div>
            
        </div>
        <?php $this->load->view("partial/footer"); ?>
    </body>
</html>
<script>
        function showTrackingNumber (){
                 var successMessage = "Shipping Confirmed For Following Invoices.Tracking Number Is " + '<?php echo $trackingNumber ?>';
                 showSuccessMessage(successMessage);
             };
             
</script>