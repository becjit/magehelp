<html>
    <head>
       <?php $this->load->view("common/header"); ?>
<!--        <script type="text/javascript" src="http://jzaefferer.github.com/jquery-validation/jquery.validate.js"></script>-->
        <style>
            .column {
            float: left;
            padding: 1em;
            width:45%;
            }
            .base-column{
                width:100%;
            }
            #content_area{
                width:1000px;
            }
            .extra-wide{
                width:95%;
            }
            .field{
                width:100%;
            }
            .ui-widget-header {height:12px;}
            .quote-column {
            float: left;
            padding-bottom: 0.5em;
            width: 95%;
            }
            .ui-combobox {
                width:14em;
            }
            .ui-combobox-input{
                width:12em;
            }
             #supplierOp-input{
                width:10em;
            }
            #warehouseOp-input{
                width:10em;
            }
             
            .calculated {
                color: green;
                font-size: 90%;
            }
            .row{
                width:95%;
            }
            
            .shopifine-ro-label {
                float: left;
                padding-right: 0.5em;
                width: 50%;
                word-wrap: break-word;
                color:#2E6E9E;
            }

            .shopifine-output {
             float: right;
             width: 45%;
             word-wrap: break-word;
             font-weight:bold;
            }
            
            .ui-tabs {
                height: 80%;
                margin: 0 auto;
                width: 70%;
                left:0;
            }
            #notetab {
                height:30em;
            }
            #details {
                height:12em;
            }
            .ui-tabs-nav{
                height:22px;
            }
            .labeldiv {
                color: #2E6E9E;
                float: left;
                font-size: 110%;
                font-weight: bold;
                margin-right: .5em;
                width: 35%;
                word-wrap: break-word;
            }
            .valuediv {
                float: left;
                font-weight: bold;
                width: 45%;
                word-wrap: break-word;
            }
            label.error {
                margin-right: .5em;
            }
            #status-message-li{
                color: red;
                font-size: 110%;
                font-style: italic;
                margin: 0 auto;
                width: 80%;
            }
            
            body{
                height:700px;
            }
            
       
        </style>
       
        <script type="text/javascript">
                $(function() {
        
        $("#notesForm").validate();
        // Main Request For Quotation Grid                    
        
        var myGrid = $("#receipts");
        var settingsObj ={grid_id:'receipts',pager:'pager',multiselect:true};
        var subgridSettingsObj = {customButtons:{reject:true},multiselect:true};
        prepareReceiptsGrid(settingsObj,{_status: 'waitingforapproval',mode:'admin'},true,{},{},subgridSettingsObj)
        myGrid.navGrid("#pager",{edit:false,add:false,view:false,del:false,search:false},{},{},{},{},{});
        var buttons = {approve:true,reject:true,mark_approver:true,assign:true,load_approver_all:true};
        addCustomButtonsInReceiptGrid('receipts','pager',null,buttons);
        myGrid.jqGrid('filterToolbar', {stringResult: true, searchOnEnter: true, defaultSearch : "cn"});
        
        var myGridRI = $("#receiptitems");
        var settingObj = {grid_id:'receiptitems',pager:'pagerRI',multiselect:true};
        var hiddenObj = {line_reference:true,returned_quantity:true,returned_value:true,receiving_notes:true,returned_notes:true};
        var subgridSettingsObj = {multiselect:true,customButtons:{reject:true,approve:true},status:['waitingforapproval'],filter:true};
        prepareReceiptItemsGrid(settingObj,{oper:'pp_approve',mode:'admin'},true,{},hiddenObj,subgridSettingsObj);
        myGridRI.navGrid('#pagerRI',{edit:false,add:false,view:true,del:false,search:false},{},{},{},{},{});
        var buttons = {mark_approver:true,assign:true,load_approver_all:true};
        addCustomButtonsInReceiptItemGrid('receiptitems','pagerRI',null,buttons);
        myGridRI.jqGrid('filterToolbar', {stringResult: true, searchOnEnter: true, defaultSearch : "cn"});
        
       
        $( "#modal-warning-order" ).dialog({
            autoOpen:false,
            height: 80,
            modal: true
        });
        $( "#dialog-rejection-notes" ).dialog({
                                autoOpen: false,
                                height: 'auto',
                                width: '35%',
                                position:[450,25],
                                modal: true,
                                buttons: {
                                    "DoneButton": {
                                        id:"doneBtn",
                                        text:"Reject",
                                        click:function() {
                                            var isvalid = $("#notesForm").valid();
                                            var grid_id =  $(this).data('grid_data').grid_id;
                                            var url =  $(this).data('grid_data').url;
                                           // "index.php/procurement/rejectReceipt"
                                            var multiple = $(this).data('grid_data').multiple;
                                            var receiptLineIds = $(this).data('grid_data').receiptLineIds;
                                            var receiptLineId = $(this).data('grid_data').receiptLineId;
                                            var receiptId = $(this).data('grid_data').receiptId;
                                            var ppIds = $(this).data('grid_data').ppIds;
                                            var ppId = $(this).data('grid_data').ppId;
                                            if (isvalid){
                                                $.ajax({
                                                    url:url,
                                                    type:"POST",
                                                    data:{
                                                        receiptId:receiptId,//ignore this for receipt line item reject in the back end
                                                        rejection_notes:$("#rejected_notes").val(),
                                                        multiple:multiple,
                                                        receiptLineIds:receiptLineIds,//ignore for single item reject and total receipt reject in the back end
                                                        receiptLineId:receiptLineId, //ignore for multiple item reject and total receipt reject in the back end
                                                            /* only for Part Payment  */       
                                                        ppIds:ppIds,//ignore for single item reject and total receipt reject in the back end(
                                                        ppId:ppId 
                                                   },
                                                    success:function (response){
                                                        //console.log("grid " + grid);
                                                        emptyMessages();
                                                        showSuccessMessage("Selected Receipt Has Been Rejected  ")
                                                        myGrid.trigger("reloadGrid");
                                                    },
                                                    error:function (response){
                                                        //console.log("grid " + grid);
                                                        emptyMessages();
                                                        showSuccessMessage("Selected Receipt Could Not Be Processed For Internal Error  ")
                                                        
                                                    }
                                                })
                                                $( this ).dialog( "close" );
                                            }
                                            
                                        }
                                    },
                                    Cancel: function() {
                                        
                                        $( this ).dialog( "close" );
                                    }
                                },
        close: function() {
            $("#notesForm").data('validator').resetForm();
            $('#notesForm')[0].reset();
        }
        }); 
    
    });
    
     $(window).load(function(){
       
        var warningDialogs={one:true,none:true,status:true,exactlyone:true,morethanone:true};
        initDialogs(warningDialogs);
        
        initCommentsForQuote();
        initAssignmentCommon();
        
    });

        </script>
        
    </head>
     
    <body>
        <?php  $this->load->view("common/menubar"); ?>
       <?php  $this->load->view("common/dialogs"); ?>
        <div id="modal-warning-order" title="Warning" class="inithide">
            <p>Please Select Receipt with The Same Order Reference</p>
        </div>
      
        
        <div style="display: block;height: 100%;" class="shopifine-ui-dialog  ui-widget ui-widget-content ui-corner-all" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-dialog-form">
            <?php  $this->load->view("common/message"); ?>
            <div class="table-grid" style="height:40%">
                <h1 id="table_header_receipt">Approve  Receipts</h1>
                <table id="receipts"><tr><td/></tr></table> 
                <div id="pager"></div>
            </div>
            
            <div class="table-grid" style="height:40%">
                <h1 id="table_header_receipt_item">Approve Part Payments</h1>
                <table id="receiptitems"><tr><td/></tr></table> 
                <div id="pagerRI"></div>
            </div>
            
        </div>
        <div id="dialog-rejection-notes">
           
            <h3 id="formHeaderItem">Provide Reason For Rejection</h3>   
            <div id ="status-message-li" class="ui-corner-all"></div>
            <form id="notesForm">             
                <fieldset>                  
                    <div id="rejected_notes_cntnr" class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="rejected_notes" class="labeldiv" style="width:25%">Returned Notes:</label>  
                                <textarea id="rejected_notes" name="rejected_notes" row="7" col="100" class="required" style="width:50%"></textarea>
                            </div>
                        </div>                        
                    </div>
                    
                </fieldset>
            </form>
        </div>
        
       
        <?php $this->load->view("partial/footer"); ?>  
        
</body>   
</html>



    
   