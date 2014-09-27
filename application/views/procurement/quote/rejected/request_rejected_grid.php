<html>
    <head>
       <?php $this->load->view("common/header"); ?>      
        <script type="text/javascript">
        $(function() {
        
        // Main Rejected Quotation Grid  
        
        var myGrid = $("#quotes");
        var myGrid = $("#quotes");
        var settingsObj ={grid_id:'quotes',pager:'pager',multiselect:true};
        
        prepareRFQGrid(settingsObj,{_status: 'rejected'},true);       
        myGrid.navGrid("#pager",{edit:false,add:false,del:false,search:false},{},{},{},{},{});
        var buttons = {reopen:true};
        addCustomButtonsInRFQGrid('quotes', 'pager', null, buttons, null);
        myGrid.jqGrid('filterToolbar', {stringResult: true, searchOnEnter: true, defaultSearch : "cn"});


        $( "#modal-warning" ).dialog({
            autoOpen:false,
            height: 80,
            modal: true
        });
 
    });        

        </script>
        
    </head>
     
    <body>
        <?php  $this->load->view("common/menubar"); ?>
        
         <div id="modal-warning" title="Warning">
            <p>Please Select At Least One Row</p>
        </div>
        
        
        <div style="display: block;height: 100%;" class="shopifine-ui-dialog ui-widget ui-widget-content ui-corner-all" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-dialog-form">
            <?php  $this->load->view("common/message"); ?>
            <div class="table-grid">
                <h1 id="table header">Rejected Requests For Quotations</h1>
                <table id="quotes"><tr><td/></tr></table> 
                <div id="pager"></div>
            </div>
            
        </div>
        <?php $this->load->view("partial/footer"); ?>       
</body>   
</html>



    
   