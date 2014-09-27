<html>
    <head>
       <?php $this->load->view("common/header"); ?>
        

        <style type="text/css">
/*        .column {
            padding: 1em 1em 3em 2em;
        }
        label {
            width: 10em;
        }*/
       </style>
        <script>
            $(document).ready(function(){
                $("#mfrForm").validate();
                //mfrGrid
                var myGrid = $("#mfrs"),lastsel2;
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
                    url:'index.php/utilities/populateMfrs',
                    datatype: 'json',
                    mtype: 'GET',
                    colNames:['Manufacturer','Description'],
                    colModel :[ 
                        {name:'manufacturer_name', index:'manufacturer_name', width:80, align:'right',editable:true,editoptions:{size:"20",maxlength:"30"}},
                        {name:'description', index:'description', width:140, align:'right',editable:true,editoptions:{size:"20",maxlength:"30"}},
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
                    width:'auto',
                    caption: 'Manufacturers',
            
                    jsonReader : {
                        root:"mfrdata",
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
                    ,editurl:'index.php/utilities/updateMfrs'
                }).navGrid("#pager",{edit:false,add:false,del:false,search:false},{height:280,reloadAfterSubmit:false,closeAfterEdit:true,recreateForm:true,checkOnSubmit:true},{},{},{},{});
                myGrid.jqGrid('filterToolbar', {stringResult: true, searchOnEnter: false, defaultSearch : "cn"});
                //end grid
                
                //model grid
                var modelGrid = $("#models"),lastselmodel;
                var editparametersmodel = {
                    "keys" : true,
                    "oneditfunc" : null,
                    "successfunc" : function(){
                        modelGrid.trigger("reloadGrid");
                        return true;
                    },
                    "aftersavefunc" : null,
                    "errorfunc": null,
                    "afterrestorefunc" : null,
                    "restoreAfterError" : true,
                    "mtype" : "POST"
                };
                modelGrid.jqGrid({
                    url:'index.php/utilities/populateModels',
                    datatype: 'json',
                    mtype: 'GET',
                    colNames:['Model','Description','Manufacturer Id','Manufacturer'],
                    colModel :[ 
                        {name:'model_name', index:'model_name', width:80, align:'right',editable:true,editoptions:{size:"20",maxlength:"30"}},
                        {name:'description', index:'description', width:140, align:'right',editable:true,editoptions:{size:"20",maxlength:"30"}},
                        {name:'manufacturer_id', index:'manufacturer_id', hidden:true},
                        {name:'manufacturer', index:'manufacturer', width:140, align:'right',editable:true,edittype:"select",editoptions:{dataUrl:"index.php/products/populateMfrs",buildSelect:function(response)
                        {
                            var select = "<select name=" + "mfrOpEdit" + "id =" +"mfrOpEdit" +">" +
                                        "<option value=" + ">Select one..." + response + "</select>";
                                    
                            return select;
                        }}}
                    ],
                    pager: '#pagerModel',
                    rowNum:10,
                    rowList:[5,10,20],
                    sortname: 'id',
                    sortorder: 'desc',
                    viewrecords: true,
                    gridview: true,
                    ignoreCase:true,
                    rownumbers:true,
                    height:'auto',
                    width:320,
                    caption: 'Model',
            
                    jsonReader : {
                        root:"modeldata",
                        page: "page",
                        total: "total",
                        records: "records",
                        cell: "dprow",
                        id: "id"
                    },
                    onSelectRow: function(id){if(id && id!==lastselmodel){
                            modelGrid.restoreRow(lastselmodel);
                            modelGrid.editRow(id,editparametersmodel);
                            lastselmodel=id;
                        }
                    }
                    ,editurl:'index.php/utilities/updateModels'
                }).navGrid("#pagerModel",{edit:false,add:false,del:false,search:false},{height:280,reloadAfterSubmit:false,closeAfterEdit:true,recreateForm:true,checkOnSubmit:true},{},{},{},{});
                modelGrid.jqGrid('filterToolbar', {stringResult: true, searchOnEnter: false, defaultSearch : "cn"});
                
                //end model  grid
                
                $("#addBtn").click(function(){
                    var bValid =  $("#mfrForm").valid();
                    if (bValid ){
                        var nameMfr = $("#nameMfr").val();
                        var mfrDesc = $("#mfrDesc").val();
                        var modelName = $("#modelName").val();
                        var modelDesc = $("#modelDesc").val();
                        var mfrOptions = $("#mfrOptions").val();
                        $.ajax({url:"index.php/utilities/createMfrModel",
                                type:"POST",
                                data:{nameMfr:nameMfr,mfrDesc:mfrDesc,modelName:modelName,modelDesc:modelDesc,mfrOptions:mfrOptions},

                                success:function(response)
                                {
                                    myGrid.trigger("reloadGrid");
                                }
                    })
                    }
                });
            });
           
        </script>
    </head>
     
    <body onload="init()">
         <?php  $this->load->view("common/menubar"); ?>
<!--        <div class="row">
            <a id="packLink" href="#">Define A New Package</a>
            <a id="exPackLink" href="#" style="display:none">Modify Existing Package</a>
        </div>-->
        <div style="display: block;height: auto;" class="shopifine-ui-dialog ui-dialog-medium ui-widget ui-widget-content ui-corner-all" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-dialog-form">
            <div class="form-container single-column-form">            
                <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
                    <span class="ui-dialog-title" id="ui-dialog-title-dialog-form">&nbsp;</span>
                    <a href="#" class="ui-dialog-titlebar-close ui-corner-all" role="button">

                    </a>
                </div>
                <div  class="ui-dialog-content" style="width: auto; min-height: 65.1333px; height: auto;" scrolltop="0" scrollleft="0">
                <h1 id="formHeader">Add New Model/Manufacturer</h1>   
                    <form id="mfrForm">
                        <fieldset>

                            <div class="row single-column-row" style="display:none">
                                <div  id="newMfrContainer">
                                    <div class="column single-column">
                                        <div class="field">
                                            <label for="nameMfr">Name of Manufacturer:</label>  
                                            <input id="nameMfr" name ="nameMfr" type="text" class="required"/>

                                        </div>
                                    </div>
                                    <div class="column single-column">
                                        <div class="field">
                                            <label for="mfrDesc">Manufacturer Description:</label>  
                                            <input id="mfrDesc" name ="mfrDesc" type="text"/>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row single-column-row">
                                <div id="newModelContainer">
                                    <div class="column single-column">
                                        <div class="field">
                                            <label for="modelName">Name Of Model : </label>  
                                            <input id="modelName" name ="modelName" type="text" class="required"/>
                                        </div>
                                    </div>
                                    <div class="column single-column">
                                        <div class="field">
                                            <label for="modelDesc">Model Description : </label>  
                                            <input id="modelDesc" name ="modelDesc" type="text" class="required"/>
                                        </div>   
                                    </div>
                                </div>

                            </div>
                            <div class="row single-column-row">
                                <div id="existMfrContainer">
                                    <div class="column single-column">
                                        <label for="mfrOptions">Available Manufacturers :</label>  
                                        <select name="mfrOptions" id ="mfrOptions" class="opt required"> 
                                                <option value=0>Choose 
                                                <?=$options?> 
                                        </select>
                                    </div>
                                </div>
                            </div>
                        <div class="row single-column-row">
                            <div class="column single-column" id="newMfrLinkContainer">
                                <h6> Want to create a new Manufacturer </h6>
                                <a id="newMfrLink" href="#">Create New Manufacturer</a>
                            </div>

                        </div>
                         <div class="row single-column-row" style="display:none">
                            <div class="column single-column" id="existingMfrLinkContainer">
                                <h6> Select from Existing manufacturers </h6>
                                <a id="existingMfrLink" href="#">Existing Manufacturers</a>
                            </div>

                        </div>
                        </fieldset>
                    </form>
                </div>
                <div class="ui-dialog-buttonpane shopifine-ui-widget-content ui-helper-clearfix">
                    <div class="shopifine-ui-dialog-buttonset">
                        <button id="addBtn" type="button" class="shopifine-ui-button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false">
                            <span class="ui-button-text">Submit</span>
                    </div> 
                </div>
            </div>
            <div class="table-grid">
                <div class="row">
                    <div class="column">
                        <h1 id="table_header">Manufacturers</h1>
                        <table id="mfrs"><tr><td/></tr></table> 
                        <div id="pager"></div>
                    </div>
                    <div class="column">
                        <h1 id="table_header_model">Models</h1>
                        <table id="models"><tr><td/></tr></table> 
                        <div id="pagerModel"></div>
                    </div>
                </div>
                
            </div>
        </div>
        <?php $this->load->view("partial/footer"); ?>       
    </body>   
</html>

<script type="text/javascript">
    
        function init(){
            var options = '<?php echo $options ?>';
            if (!options){
            $("#existMfrContainer").parent().hide();
            $("#newMfrLinkContainer").parent().hide();
            $("#newMfrContainer").parent().show();
            
            
            }
            
        }
        
        
        
       
    </script>
    
    <script>
        
         $("#newMfrLink").click (function(event){
                event.preventDefault();
            if ($("#newMfrContainer").parent().is(":hidden")){
                $("#newMfrContainer").parent().slideDown("fast");
                $("#existMfrContainer").parent().slideUp("fast");
                 
                //$("#existingMeasurementTypeLinkContainer").parent().show();
                $("#newMfrLinkContainer").parent().hide();
                $("#existingMfrLinkContainer").parent().show();
                
            }
        });
        
        $("#existingMfrLink").click (function(event){
                event.preventDefault();
            if ($("#existMfrContainer").parent().is(":hidden")){
                $("#existMfrContainer").parent().slideDown("fast");
                $("#newMfrContainer").parent().slideUp("fast");
                 
                //$("#existingMeasurementTypeLinkContainer").parent().show();
                $("#newMfrLinkContainer").parent().show();
                $("#existingMfrLinkContainer").parent().hide();
            }
        });
        
        
        
        
         
        
    </script>


 