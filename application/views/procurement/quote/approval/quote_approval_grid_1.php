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
                
        
        myGrid.jqGrid({
                    url:'index.php/procurement/populateQuotes?_status='+'waitingforapproval',
                    datatype: 'json',
                    mtype: 'GET',
                   
                    colNames:['Reference','Supplier','Estimated Value',/*'Owner',*/'Status','Raised By','Owner','Needed By Date'],
                    colModel :[ 
                        {name:'reference', index:'reference', width:80, align:'right',editable:false},
                        {name:'supplier_name', index:'supplier_name', width:140, align:'right',editable:false},
                        {name:'estimated_value', index:'estimated_value', width:100, align:'right',editable:false,editoptions:{size:"20",maxlength:"30"}},
//                        {name:'owner', index:'owner', width:140, align:'right',editable:true,editoptions:{size:"20",maxlength:"30"}},
                        {name:'status', index:'status', width:60, align:'right',editable:false,editoptions:{size:"20",maxlength:"30"}},
                        {name:'raised_by_name', index:'raised_by_name',editable:false, width:80, align:'right'},
                        {name:'owner_name', index:'owner_name',editable:false, width:80, align:'right'},
                        {name:'needed_by_date', index:'needed_by_date',editable:false, width:120, sorttype:'date'}
                    ],
                    pager: '#pager',
                    rowNum:10,
                    rowList:[5,10,20],
                    sortname: 'id',
                    sortorder: 'desc',
                    viewrecords: true,
                    gridview: true,
                    multiselect:true,
                    
                    ignoreCase:true,
                    rownumbers:true,
                    height:'auto',
                    width:680,
                    caption: 'Quotes',
            
                    jsonReader : {
                        root:"quotedata",
                        page: "page",
                        total: "total",
                        records: "records",
                        cell: "dprow",
                        id: "id"
                    },
                    subGrid:true,
                    subGridRowExpanded: function(subgrid_id, row_id) {
                            var subgrid_table_id, pager_id;
                            subgrid_table_id = subgrid_id+"_t";
                            
                            pager_id = "p_"+subgrid_table_id;
                            
                            $("#"+subgrid_id).html("<table id='"+subgrid_table_id+"' class='scroll'></table><div id='"+pager_id+"' class='scroll'></div>");
                            jQuery("#"+subgrid_table_id).jqGrid({
                                    url:'index.php/procurement/populateQuoteItems?q=2&quoteId='+row_id,
                                    datatype: 'json',
                                    colNames:['Product','Quantity','Need By Date','Unit Price','Estimated Value','Notes'],
                                    colModel :[ 
                                        {name:'name', index:'name',editable:false, width:120, align:'right'},
                                        {name:'quoted_quantity', index:'quoted_quantity', editable:false,width:50, align:'right'},
                                        {name:'needed_by_date',index:'needed_by_date',editable:false, width:80, align:'right'},
                                        {name:'expected_price',index:'expected_price',editable:false, width:50, align:'right'},
                                        {name:'estimated_value', index:'estimated_value',editable:false, width:60, align:'right'},
                                        {name:'comments', index:'comments',editable:false, width:180, align:'right'}

                                    ],
                                    rowNum:20,
                                    pager: pager_id,
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
                                });
                            jQuery("#"+subgrid_table_id).jqGrid('navGrid',"#"+pager_id,{edit:false,add:false,del:true,search:false});
                             
                    }}).navGrid("#pager",{edit:false,add:false,del:true,search:false},{height:280,reloadAfterSubmit:false,closeAfterEdit:true,recreateForm:true,checkOnSubmit:true},{},{},{},{});
               
                myGrid.jqGrid('navButtonAdd','#pager',{
                   caption:"", 
                   title:"Approve And Generate Purchase Order",
                   buttonicon:"ui-icon-check",
                   id:"add_quotes",
                   onClickButton : function () { 
                       var selectedRows = myGrid.jqGrid('getGridParam', 'selarrrow');
                       var noOfRows = selectedRows.length;
                       
                       if (noOfRows == 0){
                           $( "#modal-warning-none" ).dialog("open");
                       }
                       else if (noOfRows == 1){
                            $( "#dialog-form" ).dialog( "open" );
                        }
                        else {
                           $( "#modal-warning-one" ).dialog("open") ;
                        }
                    } 
                });
                myGrid.jqGrid('navButtonAdd','#pager',{
                   caption:"", 
                   title:"Approve And Generate Purchase Order In Bulk",
                   buttonicon:"ui-icon-cart",
                   id:"po_quotes",
                   onClickButton : function () { 
                       var rowid = myGrid.jqGrid('getGridParam', 'selarrrow');
                       var selectedRows = myGrid.jqGrid('getGridParam', 'selarrrow');
                       var noOfRows = selectedRows.length;
                       
                       if (noOfRows == 0){
                           $( "#modal-warning" ).dialog("open");
                       }
                       else{
                           $.ajax({
                            method:"POST",
                            url:"index.php/procurement/generatePOFromQuote",
                            data: {ids : rowid},
                            success: function(){
                                myGrid.trigger("reloadGrid");
                                showSuccessMessage("success");
                            }
                        })
                       }
                        
                    } 
                });
                myGrid.jqGrid('filterToolbar', {stringResult: true, searchOnEnter: true, defaultSearch : "cn"});
                $("#del_quotes").insertAfter("#add_quotes");
               
    });        
    $(window).load(function(){
        var dialogs={one:true,none:true,status:true};
        initDialogs(dialogs);
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



    
   