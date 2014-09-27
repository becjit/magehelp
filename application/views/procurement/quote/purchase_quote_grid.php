<html>
    <head>
       <?php $this->load->view("common/header"); ?>
        <style>
            #content_area {
                width:90%;
            }
            .ui-combobox{
                left: -5em;
            }
        </style>
        <script type="text/javascript">
        $(function() {
        
        $("#userOp").combobox();
        $( "#dialog-form" ).dialog({
                    autoOpen: false,
                    height: 'auto',
                    width: 400,
                    position:[400,20],
                    modal: true,
                    buttons: {
                        "Assign": function() {
                           var bValid =  $("#userform").valid();
                    
                            if (bValid ){
                        
                                $.ajax({
                                    url:"index.php/procurement/assign",
                                    data: {                                          
                                            userId: $("#userOp").val(),
                                            sel_quote: JSON.stringify((myGrid.getGridParam('selarrrow'))),
                                            assignment_notes:$("assignNotes").val()},
                                    type:"POST",
                                    success:function(response)
                                    {
                            
                                        myGrid.trigger("reloadGrid");
                                        
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
        // Main Rejected Quotation Grid  
        
        var myGrid = $("#quotes");
                
        
        myGrid.jqGrid({
                    url:'index.php/procurement/populateQuotes?_status='+'all',
                    datatype: 'json',
                    mtype: 'GET',
                   
                    colNames:['Reference','Supplier','Value','Status','Raised By','Owner','Needed Date','Notes','App/Rej By','App/Rej Notes'],
                    colModel :[ 
                        {name:'reference', index:'reference', width:60, align:'right',editable:false},
                        {name:'supplier_name', index:'supplier_name', width:120, align:'right',editable:false},
                        {name:'estimated_value', index:'estimated_value', width:80, align:'right',editable:false},
                        
                        {name:'status', index:'status', width:80, align:'right',editable:false},
                        {name:'raised_by_name', index:'raised_by_name',hidden:true,editable:false, width:80, align:'right'},
                        {name:'owner_name', index:'owner_name',editable:false, width:80, align:'right'},
                        {name:'needed_by_date', index:'needed_by_date',hidden:true,editable:false, width:100, sorttype:'date'},
                        {name:'notes', index:'notes',editable:false, width:200, sorttype:'date'},
                        {name:'approved_by', index:'approved_by',editable:false, width:100, align:'right'},
                        {name:'approval_notes', index:'approval_notes',editable:false, width:200}
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
                    width:'90%',
                    caption: 'Request For Quote',
            
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
                            // we pass two parameters
                            // subgrid_id is a id of the div tag created whitin a table data
                            // the id of this elemenet is a combination of the "sg_" + id of the row
                            // the row_id is the id of the row
                            // If we wan to pass additinal parameters to the url we can use
                            // a method getRowData(row_id) - which returns associative array in type name-value
                            // here we can easy construct the flowing
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
                                        {name:'quoted_quantity', index:'quoted_quantity', editable:true,width:50, align:'right'},
                                        {name:'needed_by_date',index:'needed_by_date',editable:true, width:80, align:'right'},
                                        {name:'expected_price',index:'expected_price',editable:true, width:50, align:'right'},
                                        {name:'estimated_value', index:'estimated_value',editable:false, width:60, align:'right'},
                                        {name:'comments', index:'comments',editable:true, width:180, align:'right'}

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
                            jQuery("#"+subgrid_table_id).jqGrid('navGrid',"#"+pager_id,{edit:false,add:false,del:false,search:false,view:true});
                             
                    }}).navGrid("#pager",{view:true,edit:false,add:false,del:false,search:false},{},{},{},{},{});
               
               
               myGrid.jqGrid('navButtonAdd','#pager',{
                   caption:"", 
                   title:"Assign",
                   buttonicon:"ui-icon-shuffle",
                   id:"add_quotes",
                   onClickButton : function () { 
                       var selectedRows = myGrid.jqGrid('getGridParam', 'selarrrow');
                       var noOfRows = selectedRows.length;
                       var error=false;
                       $("#refIds").empty();
                       
                       if (noOfRows == 0){
                           $( "#modal-warning" ).dialog("open");
                       }
                       else {
                           $.each(selectedRows, function(index,value){

                                var val = myGrid.getCell(value,'status');
                                if (val =='cancelled'){
                                    $("#refIds").append(myGrid.getCell(value,'reference')+" ")
                                    error = true;
                                }
                            })
                            if (error){
                                    $( "#modal-error" ).dialog("open");
                            }
                            else{
                                    $( "#dialog-form" ).dialog( "open" );
                            }
                       }
//                        
                    } 
                });
                
                myGrid.jqGrid ('navButtonAdd', '#pager',
                { caption: "", buttonicon: "ui-icon-calculator",
                  title: "Choose Columns",
                  onClickButton: function() {
                       myGrid.jqGrid('columnChooser');
                  }
                });
             
                myGrid.jqGrid('filterToolbar', {stringResult: true, searchOnEnter: true, defaultSearch : "cn"});
                
                
               $( "#modal-warning-morethanone" ).dialog({
                    autoOpen:false,
                    height: 80,
                    modal: true
                });
                $( "#modal-warning" ).dialog({
                    autoOpen:false,
                    height: 80,
                    modal: true
                });
                 $( "#modal-error" ).dialog({
            autoOpen:false,
            height: 140,
            modal: true
       });
 
    });        

        </script>
        
    </head>
     
    <body>
        <?php  $this->load->view("common/menubar"); ?>
        <div id="modal-error" title="Error">
            <p>Reference Number(s) <span id="refIds" style="font-weight:bold;"></span> Are Already Canceled.Canceled Quotes Can Not Be Reassigned </p>
        </div>
        
         <div id="modal-warning" title="Warning">
            <p>Please Select At Least One Row</p>
        </div>
        <div id="modal-warning-morethanone" title="Warning">
            <p>More Than One Row  Selected.Please Select Exactly Row</p>
        </div>
        
        <div style="display: block;height: 100%;" class="shopifine-ui-dialog ui-dialog-extra-wide ui-widget ui-widget-content ui-corner-all" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-dialog-form">
            <?php  $this->load->view("common/message"); ?>
            <div class="table-grid">
                <h1 id="table header">All Request For Quotations</h1>
                <table id="quotes"><tr><td/></tr></table> 
                <div id="pager"></div>
            </div>
            
        </div>
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
                    <div class="row">
                        <div class="column">
                            <div class="field">
                                <label for="assignNotes">Assignment Notes:</label>  
                                <textarea id="assignNotes" name ="assignNotes" rows="4" cols="50"></textarea>      

                            </div>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>
        <?php $this->load->view("partial/footer"); ?>       
</body>   
</html>



    
   