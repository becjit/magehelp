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
                
                //delivery grid validation
                
                function phoneCheck (value,colname){
                   var success =  /^(\d{7}(\d{1,7})?)?$/.test(value);
                   if (!success) 
                        return [false,"Invalid Phone. Phone number should be minimum 7 or maximum 14 digits"];
                    else 
                        return [true,""];
                }
                
                var myGrid = $("#users"),lastsel2;
                var editparameters = {
                    "keys" : true,
                    "oneditfunc" : null,
                    "successfunc" : function(){
                        var successStatus = "User details successfully edited";
                        showSuccessMessage(successStatus);
                        lastsel2=undefined;
                        myGrid.trigger("reloadGrid");
                        return true;
                    },
                    "aftersavefunc" : null,
                    "errorfunc": function(response)
                        {
                            var errorStatus = " User details could not be updated due to internal error";
                            showErrorMessage(errorStatus);
                            lastsel2=undefined;
                        },
                    "afterrestorefunc" : null,
                    "restoreAfterError" : true,
                    "mtype" : "POST"
                };
                myGrid.jqGrid({
                    url:'index.php/acls/populateUser',
                    datatype: 'json',
                    mtype: 'GET',
                    colNames:['User Name','Role','First Name','Last Name','Phone','Email'],
                    colModel :[ 
                        {name:'username', index:'username', width:80, align:'right',editable:false,editrules:{required:true},editoptions:{size:"20",maxlength:"30"}},
                        {name:'role_name', index:'role_name', width:80, align:'right',editable:true,editrules:{required:true},edittype:"select",editoptions:{dataUrl:"index.php/acls/populateRolesEdit",buildSelect:function(response)
                        {
                            var select = "<select name=" + "roleOpEdit" + "id =" +"roleOpEdit" +">" +
                                        "<option value=" + ">Select one..." + response + "</select>";
                                    
                            return select;
                        }}},
                        {name:'first_name',index:'first_name', width:70, align:'right',editable:true,editrules:{required:true},editoptions:{size:"20",maxlength:"30"}},
                        {name:'last_name', index:'last_name', width:70, align:'right',editable:true,editrules:{required:true},editoptions:{size:"20",maxlength:"30"}},
                        {name:'phone_number', index:'phone_number', width:80, align:'right',editrules:{custom:true,custom_func:phoneCheck,required:true},editable:true,editoptions:{size:"20",maxlength:"30"}},
                        {name:'email', index:'email', width:120, align:'right',editrules:{required:true,email:true},editable:true,editoptions:{size:"20",maxlength:"30"}}
                    ],
                    pager: '#pager',
                    rowNum:10,
                    rowList:[5,10,20],
                    sortname: 'person_id',
                    sortorder: 'desc',
                    viewrecords: true,
                    gridview: true,
                    ignoreCase:true,
                    rownumbers:true,
                    height:'auto',
                    width:680,
                    caption: 'Users',
            
                    jsonReader : {
                        root:"userdata",
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
                    editurl:'index.php/acls/editUser'
                    
                }).navGrid("#pager",{edit:false,add:true,del:false,search:false},
               /* edit Option*/ {height:280,reloadAfterSubmit:false,closeAfterEdit:true,recreateForm:true,checkOnSubmit:true},
            /* Add Option*/     {reloadAfterSubmit:true,recreateForm:true,beforeShowForm:function(form){
                                var roleElem = $("#tr_role_name",form);
                                $('<tr class="FormData" id="tr_username">\n\
                                    <td class="CaptionTD">User Name</td>\n\
                                    <td class="DataTD">&nbsp;<input type="text" size="20" maxlength="30" id="username" \n\
                                    name="username" role="textbox" class="FormElement ui-widget-content ui-corner-all">\n\
                                    </td></tr>').insertBefore(roleElem);
                                $('<tr class="FormData" id="tr_password">\n\
                                    <td class="CaptionTD">Temporary Password</td>\n\
                                    <td class="DataTD">&nbsp;<input type="text" size="20" maxlength="30" id="password" \n\
                                    name="password" role="textbox" class="FormElement ui-widget-content ui-corner-all">\n\
                                    </td></tr>').insertBefore(roleElem);
                                                   
                                }                  
                },{},{},{});
               
                myGrid.jqGrid('filterToolbar', {stringResult: true, searchOnEnter: true, defaultSearch : "cn"});
                
                    
            });
        </script>
    </head>
     
    <body>
         <?php $this->load->view("common/menubar"); ?>
        
        <div style="display: block;height: auto;" class="shopifine-ui-dialog ui-widget ui-widget-content ui-corner-all" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-dialog-form">
            <?php $this->load->view("common/message"); ?>
            <div class="table-grid">
                <h1 id="table header">Users</h1>
                <table id="users"><tr><td/></tr></table> 
                <div id="pager"></div>
            </div>
        </div>
        <?php $this->load->view("partial/footer"); ?>
    </body>
</html>
