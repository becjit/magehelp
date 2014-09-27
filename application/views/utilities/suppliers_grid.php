<html>
    <head>
       <?php $this->load->view("common/header"); ?>
        <style>
            #content_area {width:90%;}
        </style>

         <script>
            $(document).ready(function(){
                 
                function phoneCheck (value,colname){
                   var success =  /^(\d{7}(\d{1,7})?)?$/.test(value);
                   if (!success) 
                        return [false,"Invalid Phone. Phone number should be minimum 7 or maximum 14 digits"];
                    else 
                        return [true,""];
                }
                var myGrid = $("#suppliers"),lastsel2;
                var editparameters = {
                    "keys" : true,
                    "oneditfunc" : null,
                    "successfunc" : function(){
                        var successStatus = "Supplier Successfully Modified";
                        showSuccessMessage(successStatus);
                        lastsel2=undefined;
                        myGrid.trigger("reloadGrid");
                        return true;
                    },
                    "aftersavefunc" : null,
                    "errorfunc": function(response)
                        {
                            var errorStatus = " Supplier could not be updated due to internal error";
                            showErrorMessage(errorStatus);
                            lastsel2=undefined;
                        },
                    "afterrestorefunc" : null,
                    "restoreAfterError" : true,
                    "mtype" : "POST"
                };
                myGrid.jqGrid({
                    url:'index.php/utilities/populateSuppliersInGrid',
                    datatype: 'json',
                    mtype: 'GET',
                    colNames:['Supplier Name','Registration Number','Contact Person','Address','City','State','Email','Contant Number'],
                    colModel :[ 
                        
                        {name:'supplier_name', index:'supplier_name', width:100, align:'right',editable:true,editrules:{required:true},editoptions:{size:"20",maxlength:"30"}},
                        {name:'registration_number', index:'registration_number', width:120, align:'right',editable:true,editrules:{},editoptions:{size:"20",maxlength:"30"}},
                        {name:'contact_person', index:'contact_person', width:100, align:'right',editable:true,editrules:{},editoptions:{size:"20",maxlength:"30"}},
                        {name:'address', index:'address', width:140, align:'right',editable:true,editrules:{},editoptions:{size:"20",maxlength:"30"}},
                        {name:'city', index:'city', width:80, align:'right',editable:true,editrules:{required:true},editoptions:{size:"20",maxlength:"30"}},
                        {name:'state', index:'state', width:80, align:'right',editable:true,editrules:{},editoptions:{size:"20",maxlength:"30"}},
                        {name:'email', index:'email', width:140, align:'right',editable:true,editrules:{email:true},editoptions:{size:"20",maxlength:"30"}},
                        {name:'contact_number', index:'contact_number', width:80, align:'right',editable:true,editrules:{custom:true,custom_func:phoneCheck},editoptions:{size:"20",maxlength:"30"}},
                        
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
                    caption: 'Suppliers',
            
                    jsonReader : {
                        root:"supplierdata",
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
                    editurl:'index.php/utilities/modifySuppliers'
                    
                }).navGrid("#pager",{edit:true,add:true,del:true,search:false},
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
        
        <div style="display: block;height: 100%;" class="shopifine-ui-dialog ui-widget ui-widget-content ui-corner-all" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-dialog-form">
            <?php $this->load->view("common/message"); ?>
            <div class="table-grid">
                <h1 id="table header">Suppliers</h1>
                <table id="suppliers"><tr><td/></tr></table> 
                <div id="pager"></div>
            </div>
        </div>
        <?php $this->load->view("partial/footer"); ?>
    </body>
</html>
