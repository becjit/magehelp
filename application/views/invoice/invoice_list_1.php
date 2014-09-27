<html>
    <head>
       <?php $this->load->view("common/header"); ?>
<!--        <script type="text/javascript" src="http://jzaefferer.github.com/jquery-validation/jquery.validate.js"></script>-->
        
         <script>
            $(document).ready(function(){
                
                
                //dialog
                 $( "#userForm" ).validate();
//                 
//                 $( "#dialog:ui-dialog" ).dialog( "destroy" );
                
                  $( "#dialog-form" ).dialog({
                    autoOpen: false,
                    height: 'auto',
                    width: 400,
                    position:[400,25],
                    modal: true,
                    buttons: {
                        "Assign": function() {
                           var bValid =  $("#userform").valid();
                    
                            if (bValid ){
                        
                                $.ajax({
                                    url:"index.php/invoice/assign",
                                    data: {                                          
                                            userId: $("#userOp").val(),
                                            selInv: JSON.stringify((myGrid.getGridParam('selarrrow')))},
                                    type:"POST",
                                    success:function(response)
                                    {
                            
                                        $("#invoices").trigger("reloadGrid");
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
                        $("#userform").data('validator').resetForm();
                        $('#userform')[0].reset();
                    }
                });

                //end dialog
               
               //invoice grid
               
                var myGrid = $("#invoices"),lastsel2;
                
                myGrid.jqGrid({
                    url:'index.php/invoice/populateInvoices',
                    datatype: 'json',
                    mtype: 'POST',
                    colNames:['Invoice Id','Status','Order Id','Comments','Owner','Action'],
                    colModel :[ 
                        {name:'magento_invoice_increment_id', index:'magento_invoice_increment_id', width:80, align:'right'},
                        {name:'status', index:'status', width:60, align:'right'},
                        {name:'magento_order_increment_id',index:'magento_order_increment_id', width:80, align:'right'},
                        {name:'comments', index:'comments', width:80, align:'right'},
                        {name:'owner', index:'owner', width:80, align:'right'},
                        {name:'pack', index:'pack', width:40, align:'right',editable:false,search:false,formatter:'showlink', formatoptions:{baseLinkUrl:'index.php/invoice/packInvoice'}}
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
                    caption: 'Invoices',
                    multiselect:true,
                    jsonReader : {
                        root:"invoicedata",
                        page: "page",
                        total: "total",
                        records: "records",
                        cell: "dprow",
                        id: "id"
                    },
                    onSelectRow: function(id){if(id && id!==lastsel2){
                            myGrid.restoreRow(lastsel2);
                            //myGrid.editRow(id,editparameters);
                            lastsel2=id;
                        }
                    }
                    
                }).navGrid("#pager",{edit:false,add:false,del:false,search:false},{},{},{},{},{});
                
                myGrid.jqGrid('navButtonAdd','#pager',{
                                    caption:"", 
                                    title:"Acquire",
                                    buttonicon:"ui-icon-locked",
                                    id:"acquire_invoices",
                                    onClickButton : function () {
                                                var selNum = myGrid.getGridParam('selarrrow').length;
                                                    if (selNum==0){
                                                    
                                                    }
                                                    else {
                                                        $.ajax({url:"index.php/invoice/acquire",
                                                            method:'POST',
                                                            data:{selInv:JSON.stringify((myGrid.getGridParam('selarrrow'))) },
                                                            success:function (){
                                                                 $("#invoices").trigger("reloadGrid");
                                                            }
                                                        }) 
                                                    }
                                                
                                                 } 
                                 });
                myGrid.jqGrid('navButtonAdd','#pager',{
                                    caption:"", 
                                    title:"Transfer",
                                    buttonicon:"ui-icon-transferthick-e-w",
                                    id:"transfer_invoices",
                                    onClickButton : function () {
                                                    var selNum = myGrid.getGridParam('selarrrow').length;
                                                    if (selNum==0){
                                                    
                                                    }
                                                    else {$( "#dialog-form" ).dialog( "open" );}
                                                //$( "#dialog-form" ).dialog( "open" );
                                                 } 
                                 });
               
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
                

            })
        </script>
    </head>
     
    <body>
         <?php $this->load->view("common/menubar"); ?>
        
        
       
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
        <div id ="dialog-form">
            <h1 id="formHeader">Select User</h1>   
            <form id="userform">
                <fieldset>                   
                    <div class="row">
                        <div class="column">
                            <div class="field">
                                <label for="userOp">Users:</label>  
                                <select name="userOp" id ="userOp" class="required"> 
                                    <option value="">Choose 
                                        <?= $userOptions ?> 
                                </select>

                            </div>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>
    </body>
</html>
<script>
    $("#shipmentBtn").click(function(){
        window.location.href="index.php/invoice/ship";
    })

</script>



 