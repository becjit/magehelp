<html>
    <head>
       <?php $this->load->view("common/header"); ?>
<!--        <script type="text/javascript" src="http://jzaefferer.github.com/jquery-validation/jquery.validate.js"></script>-->
        <link rel="stylesheet" rev="stylesheet" href="<?php echo base_url();?>css/shopifine.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>css/ui.jqgrid.css" />
        <link rel="stylesheet" rev="stylesheet" href="<?php echo base_url();?>css/chosen.css" />
        <script src="<?php echo base_url(); ?>js/i18n/grid.locale-en.js" type="text/javascript"></script>
        <script src="<?php echo base_url(); ?>js/jquery.jqGrid.min.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
        <script src="<?php echo base_url();?>js/chosen.jquery.min.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
<!--        <script src="http://crypto-js.googlecode.com/svn/tags/3.0.2/build/rollups/md5.js"></script>-->
         <script>
            $(document).ready(function(){
                
                
                //user grid
                $("#roleform").validate();
                
                
                var myGrid = $("#roles"),lastsel2;
                var editparameters = {
                    "keys" : true,
                    "oneditfunc" : null,
                    "successfunc" : function(){
                        var successStatus = "Roles successfully edited";
                        showSuccessMessage(successStatus);
                        lastsel2=undefined;
                        myGrid.trigger("reloadGrid");
                        return true;
                    },
                    "aftersavefunc" : null,
                    "errorfunc": function(response)
                        {
                            var errorStatus = " Roles could not be updated due to internal error";
                            showErrorMessage(errorStatus);
                            lastsel2=undefined;
                        },
                    "afterrestorefunc" : null,
                    "restoreAfterError" : true,
                    "mtype" : "POST"
                };
                myGrid.jqGrid({
                    url:'index.php/acls/populateRoles',
                    datatype: 'json',
                    mtype: 'GET',
                    colNames:['Role','Parent Role'],
                    colModel :[ 
                        {name:'role_id', index:'role_id', width:120, align:'right',editable:true,edittype:"select",editoptions:{dataUrl:"index.php/acls/populateRolesEdit",buildSelect:function(response)
                        {
                            var select = "<select name=" + "parentOpEdit" + "id =" +"parentOpEdit" +">" +
                                        "<option value=" + ">Select one..." + response + "</select>";
                                    
                            return select;
                        }}},
                        /*{name:'description',index:'description', width:120, align:'right',editable:true,editoptions:{size:"20",maxlength:"30"}},*/
                        {name:'parent_role_id', index:'parent_role_id', width:120, align:'right',editable:true,edittype:"select",editoptions:{dataUrl:"index.php/acls/populateRolesEdit",buildSelect:function(response)
                        {
                            var select = "<select name=" + "parentOpEdit" + "id =" +"parentOpEdit" +">" +
                                        "<option value=" + ">Select one..." + response + "</select>";
                                    
                            return select;
                        }}},
                        
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
                    width:'300px',
                    caption: 'Roles',
            
                    jsonReader : {
                        root:"roledata",
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
                    },
                    editurl:'index.php/acls/editRoleInheritance'
                    
                }).navGrid("#pager",{edit:false,add:true,del:false,search:false},
               /* edit Option*/ {height:280,reloadAfterSubmit:false,closeAfterEdit:true,recreateForm:true,checkOnSubmit:true},
            /* Add Option*/     {reloadAfterSubmit:true,recreateForm:true /*, beforeShowForm:function(form){
                                var roleElem = $("#tr_role_name",form);
                                $('<tr class="FormData" id="tr_username" disply>\n\
                                    <td class="CaptionTD">User Name</td>\n\
                                    <td class="DataTD">&nbsp;<input type="text" size="20" maxlength="30" id="username" \n\
                                    name="username" role="textbox" class="FormElement ui-widget-content ui-corner-all">\n\
                                    </td></tr>').insertBefore(roleElem);
                                $('<tr class="FormData" id="tr_password">\n\
                                    <td class="CaptionTD">Temporary Password</td>\n\
                                    <td class="DataTD">&nbsp;<input type="text" size="20" maxlength="30" id="password" \n\
                                    name="password" role="textbox" class="FormElement ui-widget-content ui-corner-all">\n\
                                    </td></tr>').insertBefore(roleElem);
                                                   
                                }            */      
                                ,afterSubmit: function (response,postdata){
                                var success = false;
                                var message;
                                var res = response.responseText;
                                if (res == 'error')
                                    return [false,'This Parent Already Exists for Role'];
                                else {
                                    return [true,''];
                                }


                                } 
                },{},{},{});
               
                myGrid.jqGrid('filterToolbar', {stringResult: true, searchOnEnter: true, defaultSearch : "cn"});
                
                $("#addBtn").click(function(e){
                    //e.preventDefault();
                        var bValid =  $("#roleform").valid();
                        if (bValid ){
                            var name = $("#name").val();
                            var parent = $("#parentOptions").val();
                            console.log(parent);
                            $.ajax({url:"index.php/acls/createRole",
                                    type:"POST",
                                    data:{name:name,parent:parent},

                                    success:function(response)
                                    {
                                        var successStatus = "Role successfully added";
                                        showSuccessMessage(successStatus);
                                        myGrid.trigger("reloadGrid");
                                    },
                                    error:function(response)
                                    {
                                        var errorStatus = " Role could not be added due to internal error";
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
        
        <div style="display: block;height: 500px;width:50%;left:0em;margin:0 auto;" class="shopifine-ui-dialog ui-widget ui-widget-content ui-corner-all" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-dialog-form">
            <div class="form-container">
                <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
                    <span class="ui-dialog-title" id="ui-dialog-title-dialog-form">&nbsp;</span>
                    <a href="#" class="ui-dialog-titlebar-close ui-corner-all" role="button">

                    </a>
                </div>
                <div  class="ui-dialog-content" style="width: auto; min-height: 65.1333px; height: auto;" scrolltop="0" scrollleft="0">
                    <h1 id="formHeader">Add Role </h1> 
                    <form id="roleform" class="single-column-form" style="left:0%">
                        <fieldset>
                            <div class="row single-column-row">
                                <div class="column single-column">
                                <div class="field">
                                        <label for="name">Name of Role :</label>  
                                        <input id="name" name ="name" type="text" class="required"/>  
                                    </div> 
                                </div>
                                
                            </div>
                            <div class="row single-column-row">
                                <div class="column single-column">
                                    <div class="field">
                                        
                                        <label for="parentOptions">Choose Parents :</label>  
                                        <select name="parentOptions[]" id ="parentOptions" class="chzn-select" multiple="multiple" style="width:200px;height:20px;"> 
                                                <option value="">Choose 
                                                <?=$roleOptions?> 
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                        </fieldset>
                    </form>
                </div>
                <div class="ui-dialog-buttonpane shopifine-ui-widget-content ui-helper-clearfix">
                    <div class="shopifine-ui-dialog-buttonset">
                        <button id="addBtn" type="button" class="shopifine-ui-button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false">
                            <span class="ui-button-text">Add Roles</span>
                    </div> 
                </div>
            </div>
            <?php $this->load->view("common/message"); ?>
            <div class="table-grid" style="margin: 0 auto; width:65%">
                <h1 id="table header">Parents to Roles</h1>
                <table id="roles"><tr><td/></tr></table> 
                <div id="pager"></div>
            </div>
        </div>
        <?php $this->load->view("partial/footer"); ?>
    </body>
</html>
<script>
    $(".chzn-select").chosen();
    </script>
