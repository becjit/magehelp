<html>
    <head>
       <?php $this->load->view("common/header"); ?>
        
        <script>
            $(document).ready(function(){
                $("#measureform").validate();
                var myGrid = $("#uoms"),lastsel2;
                var editparameters = {
                    "keys" : true,
                    "oneditfunc" : null,
                    "successfunc" : function(){
                        myGrid.trigger("reloadGrid");
                        return true;
                    },
                    "aftersavefunc" : null,
                    "errorfunc": null,
                    "afterrestorefunc" : null,
                    "restoreAfterError" : true,
                    "mtype" : "POST"
                };
                myGrid.jqGrid({
                    url:'index.php/utilities/populateUoms',
                    datatype: 'json',
                    mtype: 'GET',
                    colNames:['Denomination','Unit Of Measurement'],
                    colModel :[ 
                        {name:'denom', index:'denom', width:40, align:'right'},
                        {name:'uom', index:'uom', width:60, align:'left'}
                       
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
                    caption: 'Measurement Units',
            
                    jsonReader : {
                        root:"uomdata",
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
                    
                }).navGrid("#pager",{edit:false,add:false,del:false,search:false},{height:280,reloadAfterSubmit:false,closeAfterEdit:true,recreateForm:true,checkOnSubmit:true},{},{},{},{});
               
               // myGrid.jqGrid('filterToolbar', {stringResult: true, searchOnEnter: true, defaultSearch : "cn"});
               
               $("#addBtn").click(function(){
                        var bValid =  $("#measureform").valid();
                        if (bValid ){
                            var name = $("#name").val();
                            var denom = $("#denom").val();
                            
                            $.ajax({url:"index.php/utilities/createMeasurementUnits",
                                    type:"POST",
                                    data:{name:name,denom:denom},

                                    success:function(response)
                                    {   
                                        var successStatus = "Unit of measurement is  successfully added";
                                        $("#measureform").data('validator').resetForm();
                                        $('#measureform')[0].reset();
                                        emptyMessages();
                                        showSuccessMessage(successStatus);
                                        $("#uoms").trigger("reloadGrid");
                                    },
                                    error:function(response)
                                    {
                                        var errorStatus = " Unit of measurement could not be added due to internal error";
                                        emptyMessages();
                                        showErrorMessage(errorStatus);

                                    }
                        })
                    }
                });
            });
        </script>
    </head>
     
    <body>
         <?php $this->load->view("common/menubar"); ?>
        <div style="display: block;height: auto;" class="shopifine-ui-dialog ui-dialog-small ui-widget ui-widget-content ui-corner-all" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-dialog-form">
            <div class="form-container single-column-form " >
                <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
                    <span class="ui-dialog-title" id="ui-dialog-title-dialog-form">&nbsp;</span>
                    <a href="#" class="ui-dialog-titlebar-close ui-corner-all" role="button">

                    </a>
                </div>
                <div  class="ui-dialog-content" style="width: auto; min-height: 65.1333px; height: auto;" scrolltop="0" scrollleft="0">
                    <h1 id="formHeader">Add Measurement Units</h1> 
                    <form id="measureform">
                        <fieldset>
                            <div class="row single-column-row">
                                <div class="column single-column">
                                    <div class="field">
                                        <label for="name">Measurement Unit :</label>  
                                        <input id="name" name ="name" type="text" class="required"/>  
                                    </div>
                                </div>
                                <div class="column single-column">
                                    <div class="field">
                                        <label for="denom">Denomination :</label>  
                                        <input id="denom" name ="denom" type="text" class="required digits"/>  
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
                <div class="ui-dialog-buttonpane shopifine-ui-widget-content ui-helper-clearfix">
                    <div class="shopifine-ui-dialog-buttonset">
                        <button id="addBtn" type="button" class="shopifine-ui-button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false">
                            <span class="ui-button-text">Add Measurement Unit</span>
                    </div> 
                </div>
            </div>
            <?php $this->load->view("common/message"); ?>
            <div class="table-grid">
                <h1 id="table_header" style="left:30%;position:relative;">Unit Of Measurements</h1>
                <table id="uoms"><tr><td/></tr></table> 
                <div id="pager"></div>
            </div>
        </div>
        <?php $this->load->view("partial/footer"); ?>
    </body>
</html>
 

 