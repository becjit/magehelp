<html>
    <head>
       <?php $this->load->view("common/header"); ?>
<!--        <script type="text/javascript" src="http://jzaefferer.github.com/jquery-validation/jquery.validate.js"></script>-->
        <link rel="stylesheet" rev="stylesheet" href="<?php echo base_url();?>css/shopifine.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>css/ui.jqgrid.css" />
        <script src="<?php echo base_url(); ?>js/i18n/grid.locale-en.js" type="text/javascript"></script>
        <script src="<?php echo base_url(); ?>js/jquery.jqGrid.min.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
<!--        <script src="http://crypto-js.googlecode.com/svn/tags/3.0.2/build/rollups/md5.js"></script>-->
         <script>
            $(document).ready(function(){
                
                
                //user grid
                $("#permissionForm").validate({
                    rules:{
                        relativeOrder:{
                            digits:true
                        }
                    }
                });
                $("#parent").combobox();
                var myGrid = $("#resources"),lastsel2;
                var editparameters = {
                    "keys" : true,
                    "oneditfunc" : null,
                    "successfunc" : function(){
                        var successStatus = "Resources successfully edited";
                        showSuccessMessage(successStatus);
                        lastsel2=undefined;
                        myGrid.trigger("reloadGrid");
                        return true;
                    },
                    "aftersavefunc" : null,
                    "errorfunc": function(response)
                        {
                            var errorStatus = " Resources could not be updated due to internal error";
                            showErrorMessage(errorStatus);
                            lastsel2=undefined;
                        },
                    "afterrestorefunc" : null,
                    "restoreAfterError" : true,
                    "mtype" : "POST"
                };
                myGrid.jqGrid({
                    url:'index.php/acls/populateResources',
                    datatype: 'json',
                    mtype: 'GET',
                    colNames:['Resource','Resource Type Id','Parent Resource Id','Resource Type','Parent Resource','Description','UI Display Name','Relative Path','Relative Order'],
                    colModel :[ 
                        {name:'resource', index:'resource', width:100, align:'right',editable:false,editrules:{required:true},editoptions:{size:"20",maxlength:"30"}},
                        {name:'resource_type_id', index:'resource_type_id', hidden:true},
                        {name:'parent_id', index:'parent_id', hidden:true},
                        {name:'resource_type', index:'resource_type', width:80, align:'right',editable:false,editrules:{required:true},edittype:"select",editoptions:{dataUrl:"index.php/acls/populateResourceTypesEdit",buildSelect:function(response)
                        {
                            var select = "<select name=" + "resOpEdit" + "id =" +"resOpEdit" +">" +
                                        "<option value=" + ">Select one..." + response + "</select>";
                                    
                            return select;
                        }}},
                        {name:'parent', index:'parent', width:100, align:'right',editable:false,edittype:"select",editoptions:{dataUrl:"index.php/acls/populateParentResourcesEdit",buildSelect:function(response)
                        {
                            var select = "<select name=" + "parentOpEdit" + "id =" +"parentOpEdit" +">" +
                                        "<option value=" + ">Select one..." + response + "</select>";
                                    
                            return select;
                        }}},
                        {name:'description',index:'description', width:120, align:'right',editable:true,editoptions:{size:"20",maxlength:"30"}},
                        {name:'ui_display_name', index:'ui_display_name', width:100, align:'right',editable:true,editrules:{},editoptions:{size:"20",maxlength:"30"}},
                        {name:'relative_path_link', index:'relative_path_link', width:140, align:'right',editable:true,editoptions:{size:"30",maxlength:"80"}},
                        {name:'relative_order_in_category', index:'relative_order_in_category', width:80, align:'right',editable:true,editoptions:{size:"20",maxlength:"30"}}
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
                    caption: 'Resources',
            
                    jsonReader : {
                        root:"resourcedata",
                        page: "page",
                        total: "total",
                        records: "records",
                        cell: "dprow",
                        id: "id"
                    },
//                    onSelectRow: function(id){if(id && id!==lastsel2){
//                            myGrid.restoreRow(lastsel2);
//                            myGrid.editRow(id,editparameters);
//                            lastsel2=id;
//                        }
//                    },
                    editurl:'index.php/acls/editResource'
                    
                }).navGrid("#pager",{edit:false,add:false,del:false,search:false},
               /* edit Option*/ {height:280,reloadAfterSubmit:false,closeAfterEdit:true,recreateForm:true,checkOnSubmit:true},
            /* Add Option*/     {                  
                },{},{},{});
        myGrid.jqGrid('navButtonAdd','pager',{
            caption:"", 
            title:"Add Resource",
            buttonicon:"ui-icon-plus",
            id:"add_resources",
            onClickButton : function () { 
                //need to pass grid id for dynamic reload;
                
                var gridData ={'oper':'add'}
                 $( "#permission-form-dialog" ).data('grid_data',gridData).dialog( "open" );
             } 
         });
          myGrid.jqGrid('navButtonAdd','pager',{
            caption:"", 
            title:"Edit Resource",
            buttonicon:"ui-icon-pencil",
            id:"edit_resources",
            onClickButton : function () { 
                //need to pass grid id for dynamic reload;
                var rowid = myGrid.getGridParam('selrow');
                if (rowid !=null && rowid!=undefined){
                    var gridData ={'oper':'edit','resource_id':rowid};
                     $( "#permission-form-dialog" ).data('grid_data',gridData).dialog( "open" );
                }
                else{
                    $( "#modal-warning-one" ).dialog('open');
                }

             } 
         });
        myGrid.jqGrid('filterToolbar', {stringResult: true, searchOnEnter: true, defaultSearch : "cn"});
                
        $( "#permission-form-dialog" ).dialog({
            autoOpen: false,
            height: 'auto',
            width: '35%',
            position:[450,25],
            modal: true,
            buttons: {
                "Add Resource": function() {
                    //default form name
                  
                   var isvalid = $("#permissionForm").valid();
                   
                   if (isvalid){
                       $.ajax({
                           url:"index.php/acls/editResource",
                           type:"POST",
                           data:{
                              
                               form_data:$("#permissionForm").toObject()
                               
                           },
                               
                           success:function (response){
                               console.log("success" + response)
                           },
                           error :function (response){
                                                            
                           }
                           
                       })
                       $( this ).dialog( "close" );
                   }

                },
                Cancel: function() {

                    $( this ).dialog( "close" );
                }
            },
            open: function(event,ui){
                var oper = $(this).data("grid_data").oper;
                var resource_id = $(this).data("grid_data").resource_id;
                
                if (oper=='add'){
                    $("#permTypeCntnr").show();
                    $("#oper_hidden").val(oper);
                }
                else if (oper=='edit'){
                    //no change of default during edit.
                    $("#permTypeCntnr").hide();
                    var parent_id = $("#resources").getCell(resource_id,'parent_id');
                    if (!isEmpty(parent_id)){
                        $("#parent").combobox("setselected",parent_id);
                    }
                    
                    $("#oper_hidden").val(oper);
                    $("#resource_id_hidden").val(resource_id);
                    $("#resource").val($("#resources").getCell(resource_id,'resource'));
                    $("#resourceType").val($("#resources").getCell(resource_id,'resource_type_id'));
                    
                    $("#description").val($("#resources").getCell(resource_id,'description'));
                    $("#uiDisplayName").val($("#resources").getCell(resource_id,'ui_display_name'));
                    $("#relativePath").val($("#resources").getCell(resource_id,'relative_path_link'));
                    $("#relativeOrder").val($("#resources").getCell(resource_id,'relative_order_in_category'));
                  
                }

            },
            close: function(event,ui) {
                $("#permissionForm").data('validator').resetForm();
                $('#permissionForm')[0].reset();
                $("#resources").trigger('reloadGrid');
            }
        });
        //for submenu parent is required
        $("#resourceType").change(function(){
            if ($(this).val()=="3"){
                $("#parent-input").addClass("required");
            }
            else {
                $("#parent-input").removeClass("required");
            }
        });

    });
     $(window).load(function(){
       
        var warningDialogs={one:true,none:true};
        initDialogs(warningDialogs);
        
    });
        </script>
    </head>
     
    <body>
         <?php $this->load->view("common/menubar"); ?>
        <?php $this->load->view("common/dialogs"); ?>
        
        <div style="display: block;height: 100%;width:90%;left:0em;" class="shopifine-ui-dialog ui-widget ui-widget-content ui-corner-all" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-dialog-form">
            <?php //$this->load->view("common/message"); ?>
            <div class="table-grid">
                <h1 id="table header">Resources</h1>
                <table id="resources"><tr><td/></tr></table> 
                <div id="pager"></div>
            </div>
        </div>
        <div id ="permission-form-dialog">
            <h1 id="formHeader">Add New Resource</h1>   
            <form id="permissionForm">
                <fieldset>
                    
                    <div class="row single-column-row">
                        <div class="column quote-column single-column">
                           <div class="field">
                                <label for="resource" class="labeldiv-edit">Resource:</label>  
                                <input id="resource" name ="resource" type="text" class="required"/>
                            </div>
                        </div>
                    </div>
                    <div class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="resourceType" class="labeldiv-edit">Resource Type:</label>  
                                <select name="resourceType" id ="resourceType" class="required"> 
                                    <option value="">Choose 
                                        <?= $resourceTypeOptions ?> 
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row single-column-row" id="permTypeCntnr">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="permissionType" class="labeldiv-edit">Default Permission Type:</label>  
                                <select name="permissionType" id ="permissionType" class="required"> 
                                    <option value="">Choose 
                                        <?= $permissionTypeOptions ?> 
                                </select>
                                <div id ="permission-help" class="ui-corner-all help-message-left">
                                    (For Administrator Role)
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="parent" class="labeldiv-edit">Parent Resource:</label>  
                                <select name="parent" id ="parent"> 
                                    <option value="">Choose 
                                        <?= $parentOptions ?> 
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="description" class="labeldiv-edit">Description:</label>  
                                <input id="description" name="description"/>
                            </div>
                        </div>                        
                    </div>
                    <div class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="uiDisplayName" class="labeldiv-edit">UI Display Name:</label>  
                                <input id="uiDisplayName" name="uiDisplayName"/>
                                
                            </div>
                           
                        </div>                        
                    </div>
                    <div class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="relativePath" class="labeldiv-edit">Relative Path:</label>  
                                <input id="relativePath" name="relativePath" size="30" maxlength="100"/>
                                
                            </div>
                        </div>                        
                    </div>
                    <div class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="relativeOrder" class="labeldiv-edit">Relative Order:</label>  
                                <input id="relativeOrder" name="relativeOrder"/>
                            </div>
                        </div>
                        
                    </div>
                    
                </fieldset>
                <input id="resource_id_hidden" name="resource_id_hidden" type="hidden"/>
                <input id="oper_hidden" name="oper_hidden" type="hidden"/>
            </form>
        </div>
        <?php $this->load->view("partial/footer"); ?>
    </body>
</html>
