<html>
    <head>
       <?php $this->load->view("common/header"); ?>
        
         <script>
            $(document).ready(function(){
               
                               
                var myGrid = $("#configs"),lastsel2;
                var editparameters = {
                    "keys" : true,
                    "oneditfunc" : null,
                    "successfunc" : function(){
                        var successStatus = "Configuration successfully modified";
                        emptyMessages();
                        showSuccessMessage(successStatus);
                        myGrid.trigger("reloadGrid");
                        return true;
                    },
                    "aftersavefunc" : null,
                    "errorfunc": function(response)
                        {
                            var errorStatus = " Configuration could not be updated due to internal error";
                            emptyMessages();
                            showErrorMessage(errorStatus);

                        },
                    "afterrestorefunc" : null,
                    "restoreAfterError" : true,
                    "mtype" : "POST"
                };
                myGrid.jqGrid({
                    url:'index.php/config/populateConfigs',
                    datatype: 'json',
                    mtype: 'GET',
                    colNames:['Key','Value',],
                    colModel :[ 
                        {name:'key', index:'key', width:140, align:'right',editable:true,editoptions:{size:"20",maxlength:"50"}},
                        {name:'value', index:'value', width:140, align:'right',editable:true,editoptions:{size:"40",maxlength:"50"}},
                        
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
                    width:400,
                    caption: 'Application Configuration',
            
                    jsonReader : {
                        root:"configdata",
                        page: "page",
                        total: "total",
                        records: "records",
                        cell: "dprow",
                        id: "id"
                    },
                    onSelectRow: function(id){if(id && id!==lastsel2){
                            myGrid.restoreRow(lastsel2);
                            myGrid.editRow(id,editparameters);
                            lastsel2=id;
                        }
                    }
                    ,editurl:'index.php/config/updateConfigs'
                }).navGrid("#pager",{edit:false,add:true,del:true,search:false},{height:130,reloadAfterSubmit:true,closeAfterEdit:true,recreateForm:true,checkOnSubmit:true},{},{},{},{});
               
                myGrid.jqGrid('filterToolbar', {stringResult: true, searchOnEnter: true, defaultSearch : "cn"});
                
               
            });
        </script>
    </head>
     
    <body>
         <?php $this->load->view("common/menubar"); ?>
        <p><?php echo $total ?></p>
        <div style="display: block;height:400px;" class="shopifine-ui-dialog ui-dialog-extra-small ui-widget ui-widget-content ui-corner-all" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-dialog-form">

            <?php $this->load->view("common/message"); ?>
            <div class="table-grid">
                <h1 id="table header">Configuration Details</h1>
                <table id="configs"><tr><td/></tr></table> 
                <div id="pager"></div>
            </div>
        </div>
        <?php $this->load->view("partial/footer"); ?>
    </body>
</html>
<script>
    
    
</script>

 