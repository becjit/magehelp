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
//                function permissionExistCallback (){
//                    $.ajax({method:"POST",
//                                                        url:'index.php/acls/checkIfExists',
//                                                        data:{roleid:postdata.role_name,
//                                                              permissionid:postdata.permission_name,
//                                                              resourceid:postdata.resource_name },
////                                                        error:function (){
////                                                            message="Permission Already Exists for Resource And Role";
////                                                        },
//                                                        success:function (data,textStatus){
//                                                            if (data == 'success')
//                                                                return [true,''];
//                                                            else {
//                                                               return [false,'Permission Already Exists for Resource And Role'];
//                                                            }
//                                                        }}
//                                                        )
//                }
                
                var myGrid = $("#permissions"),lastsel2;
                var editparameters = {
                    "keys" : true,
                    "oneditfunc" : null,
                    "successfunc" : function(){
                        var successStatus = "Permissions successfully edited";
                        showSuccessMessage(successStatus);
                        lastsel2=undefined;
                        myGrid.trigger("reloadGrid");
                        return true;
                    },
                    "aftersavefunc" : null,
                    "errorfunc": function(response)
                        {
                            var errorStatus = " Permissions could not be updated due to internal error";
                            showErrorMessage(errorStatus);
                            lastsel2=undefined;
                        },
                    "afterrestorefunc" : null,
                    "restoreAfterError" : true,
                    "mtype" : "POST"
                };
                myGrid.jqGrid({
                    url:'index.php/acls/populatePermissions',
                    datatype: 'json',
                    mtype: 'GET',
                    colNames:['Role','Resource','Permission','Is Allowed'],
                    colModel :[ 
                        
                        {name:'role_name', index:'role_name', width:80, align:'right',editable:true,editrules:{required:true},edittype:"select",editoptions:{dataUrl:"index.php/acls/populateRolesEdit",buildSelect:function(response)
                        {
                            var select = "<select name=" + "roleOpEdit" + "id =" +"roleOpEdit" +">" +
                                        "<option value=" + ">Select one..." + response + "</select>";
                                    
                            return select;
                        }}},
                        {name:'resource_name', index:'resource_name', width:140, align:'right',editable:true,edittype:"select",editoptions:{dataUrl:"index.php/acls/populateParentResourcesEdit",buildSelect:function(response)
                        {
                            var select = "<select name=" + "parentOpEdit" + "id =" +"parentOpEdit" +">" +
                                        "<option value=" + ">Select one..." + response + "</select>";
                                    
                            return select;
                        }}},
                        {name:'permission_name', index:'permission_name', width:80, align:'right',editable:true,edittype:"select",editoptions:{dataUrl:"index.php/acls/populatePermissionTypesEdit",buildSelect:function(response)
                        {
                            var select = "<select name=" + "parentOpEdit" + "id =" +"parentOpEdit" +">" +
                                        "<option value=" + ">Select one..." + response + "</select>";
                                    
                            return select;
                        }}},
                        {name:'isAllowed',index:'isAllowed', width:80, align:'right',editable:true,formatter:'select',edittype:'select', editoptions:{value:{1:'True',0:'False'}}}
                        
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
                    caption: 'Permission',
            
                    jsonReader : {
                        root:"permissiondata",
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
                    editurl:'index.php/acls/editPermission'
                    
                }).navGrid("#pager",{edit:false,add:true,del:false,search:false},
               /* edit Option*/ {height:280,reloadAfterSubmit:false,closeAfterEdit:true,recreateForm:true,checkOnSubmit:true},
            /* Add Option*/     {reloadAfterSubmit:true,recreateForm:true
                                ,afterSubmit: function (response,postdata){
                                                var success = false;
                                                var message;
                                                var res = response.responseText;
                                                if (res == 'error')
                                                    return [false,'Permission Already Exists for Resource And Role'];
                                                else {
                                                    return [true,''];
                                                }
                                                
                                                
                                                }                 
                },{},{},{});
               
                myGrid.jqGrid('filterToolbar', {stringResult: true, searchOnEnter: true, defaultSearch : "cn"});
                
                
                
                    
            });
        </script>
    </head>
     
    <body>
         <?php $this->load->view("common/menubar"); ?>
        
        <div style="display: block;height: 100%;" class="shopifine-ui-dialog ui-dialog-small ui-widget ui-widget-content ui-corner-all" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-dialog-form">
            <?php //$this->load->view("common/message"); ?>
            <div class="table-grid">
                <h1 id="table header">Permissions</h1>
                <table id="permissions"><tr><td/></tr></table> 
                <div id="pager"></div>
            </div>
        </div>
        <?php $this->load->view("partial/footer"); ?>
    </body>
</html>
