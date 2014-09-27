<html>
    <head>
       <?php $this->load->view("common/header"); ?>
        <script src="<?php echo base_url();?>js/dialogs.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
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
            width: 45em;
            }
            .ui-combobox-input{
                width:23em;
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
            .shopifine-ui-dialog {
             padding: 1em 1em;
            }
            
            .ui-tabs {
                height: 80%;
                margin: 0 auto;
                width: 100%;
                left:0;
            }
            #notetab {
                height:20em;
            }
             .ui-tabs-nav{
                height:22px;
            }
            #status-message{
                margin:5px;
            }
            p {
            padding: 0;
            width: 70%;
            word-wrap: break-word;
            
        }
        </style>
       
        <script type="text/javascript">
                $(function() {
        
        //form validation 
      
        
         $("#quoteForm").validate();
         
        
              
       $( "#dialog-form" ).dialog({
                                autoOpen: false,
                                height: 'auto',
                                width: '52%',
                                position:[350,25],
                                modal: true,
                                buttons: {
                                    "DoneButton": {
                                        id:"doneBtn",
                                        text:"Approve And Generate PO",
                                        click:function() {
                                        var isValid = $("#quoteForm").valid();
                                        if (isValid){
                                           $.ajax({url:"index.php/procurement/approveOrReject",
                                                type:"POST",
                                                data:{  
                                                        action:'approve',
                                                        quoteId:myGrid.getGridParam('selrow'),
                                                        quote_approval_notes:$("#approveNotes").val()
                                                    },

                                                success:function(response)
                                                {
                                                    emptyMessages();
                                                    showSuccessMessage("Quoation Has Been Approved And Purchase Order Generated");
                                                    myGrid.trigger("reloadGrid");
                                                }
                                                 
                                            }) //end ajax                                          
                                            $( this ).dialog( "close" );
                                        } //end ifvalid
                                        
                                    }}, //end of Create button
                                    "RejectButton": {id:"rejectBtn",
                                        text:"Reject",
                                        click:function() {
                                        var isValid = $("#quoteForm").valid();
                                        if (isValid){
                                           $.ajax({url:"index.php/procurement/approveOrReject",
                                                type:"POST",
                                                data:{  
                                                        action:'reject',
                                                        quoteId:myGrid.getGridParam('selrow'),
                                                        quote_approval_notes:$("#approveNotes").val()
                                                    },

                                                success:function(response)
                                                {
                                                    emptyMessages();
                                                    showSuccessMessage("Quoation Has Been Rejected.Please Modify And Resubmit");
                                                    myGrid.trigger("reloadGrid");
                                                }
                                                 
                                            }) //end ajax                                          
                                            $( this ).dialog( "close" );
                                        } //end ifvalid
                                        
                                    }}
                                },//end buttons
                                open: function(){
                                   
                                     $( "#tabs" ).tabs({
                                         
                                       
                                        //load contents after the tab loading is complete
                                        load: function(event,ui){
                                            
                                                    $.ajax({
                                               method:"POST",
                                               url:'index.php/procurement/getQuoteDetails',
                                               data:{quoteId: myGrid.getGridParam('selrow')},
                                               success:function(response){
                                                   //console.log(response);
                                                   var resJson = JSON.parse(response);
                                                   //console.log(resJson);
                                                   $("#quoteRef").text(resJson.reference);
                                                   $("#supplierOP").text(resJson.supplier_name);
                                                   $("#warehouseOP").text(resJson.warehouse);
                                                   $("#quoteOP").text(resJson.reference);
                                                   $("#raisedByOP").text(resJson.raised_by_name);
                                                   $("#approvedByOP").text(resJson.approved_by_name);
                                                   $("#estValueOP").text(resJson.estimated_value);
                                               }
                                            })
                                            $("#lineItems").jqGrid({
                                                url:'index.php/procurement/populateQuoteItems?q=2&quoteId='+myGrid.getGridParam('selrow'),
                                                datatype: 'json',
                                                colNames:['Product','Quantity','Need By Date','Unit Price','Estimated Value','Notes'],
                                                colModel :[ 
                                                    {name:'name', index:'name',editable:false, width:120, align:'right'},
                                                    {name:'quoted_quantity', index:'quoted_quantity', editable:true,width:50, align:'right'},
                                                    {name:'needed_by_date',index:'needed_by_date',editable:true, width:80, align:'right'},
                                                    {name:'expected_price',index:'expected_price',editable:true, width:50, align:'right'},
                                                    {name:'estimated_value', index:'estimated_value',editable:false, width:60, align:'right'},
                                                    {name:'comments', index:'comments',editable:true, width:180, align:'right'}

                                                ],
                                                rowNum:20,
                                                pager: '#pagerPk',
                                                sortname: 'id',
                                                sortorder: "asc",
                                                height: '100%',

                                                jsonReader : {
                                                    root:"quoteitemdata",
                                                    page: "page",
                                                    total: "total",
                                                    records: "records",
                                                    cell: "dprow",
                                                    id: "id"
                                                }
                                            }).navGrid("#pagerPk",{edit:false,add:false,del:true,search:false},{},{},{},{},{});
                                        }
                                    }); 
                                     
                                    
                                                            
                                },

                                close: function() {
                                    //allFields.val( "" ).removeClass( "ui-state-error" );
                                    $("#quoteForm").data('validator').resetForm();
                                    $("#quoteForm")[0].reset();
                                    $("#lineItems").jqGrid("GridUnload");
                                    $( "#tabs" ).tabs("destroy");
                                    
                                }
                            });
        
       
        // Main Request For Quotation Grid  
        
        var myGrid = $("#quotes");
        var settingsObj ={grid_id:'quotes',pager:'pager',multiselect:true};
        var hiddenObj = {discount_type:true,discount_value:true,pricelist_id:true,discount_amount:true,final_total:true};
        prepareQuotesGrid(settingsObj,{_status: 'waitingforapproval',mode:'admin'},true,{},hiddenObj,{});       
        myGrid.navGrid("#pager",{edit:false,add:false,del:false,search:false},{},{},{},{},{});
        var buttons = {approve:true,approve_bulk:true,comments:true,mark_approver:true,assign:true,load_approver_all:true};
        addCustomButtonsQuoteGrid(settingsObj,buttons);
        myGrid.jqGrid('filterToolbar', {stringResult: true, searchOnEnter: true, defaultSearch : "cn"});
               
    });        
    $(window).load(function(){
         var warningDialogs={one:true,none:true,morethanone:true,exactlyone:true};
        initDialogs(warningDialogs);
        initCommentsForQuote();
        initAssignmentCommon();
    });
        </script>
        
    </head>
     
    <body>
        <?php  $this->load->view("common/menubar"); ?>
        <?php  $this->load->view("common/dialogs"); ?>
        
        <div id ="dialog-form">
            <h1 id="formHeader">Quote # <span id="quoteRef"></span> Details</h1>   
            <form id="quoteForm">
                <fieldset>
                    <div id="tabs">
                        <ul>
                            <li id="baseLink"><a href="<?php echo site_url('procurement/loadQuoteFormFragment') ?>">Basic Details</a></li>
                            <li id="notesLink"><a href="<?php echo site_url('procurement/loadQuoteNotesFragment') ?>">Notes</a></li>

                        </ul>
                    </div>
                </fieldset>
            </form>
        </div>
        
        <div style="display: block;height: 100%;" class="shopifine-ui-dialog ui-widget ui-widget-content ui-corner-all" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-dialog-form">
            <?php  $this->load->view("common/message"); ?>
            <div class="table-grid">
                <h1 id="quote_header">Quotes</h1>
                <table id="quotes"><tr><td/></tr></table> 
                <div id="pager"></div>
            </div>
            
        </div>
        <?php $this->load->view("partial/footer"); ?>       
</body>   
</html>



    
   