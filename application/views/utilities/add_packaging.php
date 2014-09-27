<html>
    <head>
       <?php $this->load->view("common/header"); ?>
<!--        <script type="text/javascript" src="http://jzaefferer.github.com/jquery-validation/jquery.validate.js"></script>-->
        <style>
            #tabs-1{
                height:20em;
            }
            #tabs-2{
                height:25em;
            }
        </style>
        <script>
            $(document).ready(function(){
                $.validator.addMethod('notZero', function(value, element) {
                    return (value != '0');
                }, 'Please select an option.');
                
                $.validator.addMethod("greaterThanZero", function(value, element) {
            return this.optional(element) || (parseInt(value) > 0);
        }, "Denomination  must be greater than zero");
                
            });
            
            
        </script>
        <script type="text/javascript">
                $(function() {
        $("#tabs").tabs({
            load:function (event,ui){
                 $(".opt").change(function(event){
        event.preventDefault();
        var optionsMap =jQuery.parseJSON('<?php echo $packageMap ?>');
        
        var type = optionsMap[$(this).val()];
        $("#typeCon").parent().show();
        $("#packtype").val(type);
        $("#packtype").prop('disabled',true);
        
        
        });
         $("#packagingForm").validate({
                    ignore: ":hidden",
                    onfocusout: false,
                    
                                rules: {
                        packageOptions: {
                            notZero: true
                        },
                        uomOp: {
                            notZero: true
                        },
                        denomIp: {
                            number:true,
                            greaterThanZero:true
                        }
                    }
                });
                
                $("#packagingFormNew").validate({
                    ignore: ":hidden",
                    onfocusout: false,
                    
                                rules: {
                        
                        packagingFormNew: {
                            notZero: true
                        }
                        ,
                        denomIp: {
                            number:true,
                            greaterThanZero:true
                        }
                    }
                });
                
        
        $( "#newTypeDefLink" )
            .click(function() {
                $( "#dialog-form" ).dialog( "open" );
            });

            $("#existingPkgBtn").click(function(){
                       var bValid =  $("#packagingForm").valid();
                       if (bValid ){
                           var packageOptions = $("#packageOptions").val();
                           var packtype = $("#packtype").val();
                           var uomOp = $("#uomOpTabOne").val();
                           var sizeOp = $("#sizeOpTabOne").val();
                           var uomIp = $("#uomIpTabOne").val();
                           var denomIp = $("#denomIpTabOne").val();
                           $.ajax({url:"index.php/utilities/testuom",
                                   type:"POST",
                                   data:{packageOptions:packageOptions,packtype:packtype,uomOp:uomOp,sizeOp:sizeOp,uomIp:uomIp,denomIp:denomIp},

                                   success:function(response)
                                   {
                                       //myGrid.trigger("reloadGrid");
                                       console.log("success");
                                   }
                       })
                   }
               });
               $("#newPkgBtn").click(function(){
                       var bValid =  $("#packagingFormNew").valid();
                       if (bValid ){
                           var namePkg = $("#namePkg").val();
                           var desc = $("#desc").val();
                           var typeOp = $("#typeOp").val();
                           var uomOp = $("#uomOpTabTwo").val();
                           var sizeOp = $("#uomOpTabTwo").val();
                           var uomIp = $("#uomOpTabTwo").val();
                           var denomIp = $("#uomOpTabTwo").val();
                           $.ajax({url:"index.php/utilities/testuom",
                                   type:"POST",
                                   data:{namePkg:namePkg,desc:desc,uomOp:uomOp,typeOp:typeOp,sizeOp:sizeOp,uomIp:uomIp,denomIp:denomIp},

                                   success:function(response)
                                   {
                                       //myGrid.trigger("reloadGrid");
                                       console.log("success");
                                   }
                       })
                   }
               });

               $(".uomOp").change(function(){
                   var val = $(this).val();
                   var tabId = this.id.split("-")[1];
                   $.ajax({type:"post",
                           url:"index.php/utilities/loadSize/"+val,
                           success: function(sizeHtml){
                               $("#sizeOp-"+tabId).children().remove();
                               $("#sizeOp-"+tabId).append(sizeHtml); 
                           }});
               });
                $("#existMeasurementTypeLinkTabOne").click (function(event){
                    event.preventDefault();
                    if ($("#existingUOMContainerTabOne").parent().is(":hidden")){

                        $("#existingUOMContainerTabOne").parent().show();
                        $("#newUOMContainerTabOne").parent().hide();

                        $("#newMeasurementTypeLinkContainerTabOne").parent().show();
                        $("#existingMeasurementTypeLinkContainerTabOne").parent().hide()
                    }
                });

                 $("#newMeasurementTypeLinkTabOne").click (function(event){
                    event.preventDefault();
                    if ($("#newUOMContainerTabOne").parent().is(":hidden")){
                        $("#newUOMContainerTabOne").parent().show();
                        $("#existingUOMContainerTabOne").parent().hide();

                        $("#existingMeasurementTypeLinkContainerTabOne").parent().show();
                        $("#newMeasurementTypeLinkContainerTabOne").parent().hide();
                    }
                });
                         $("#existMeasurementTypeLinkTabTwo").click (function(event){
                event.preventDefault();
                    if ($("#existingUOMContainerTabTwo").parent().is(":hidden")){

                        $("#existingUOMContainerTabTwo").parent().show();
                        $("#newUOMContainerTabTwo").parent().hide();

                        $("#newMeasurementTypeLinkContainerTabTwo").parent().show();
                        $("#existingMeasurementTypeLinkContainerTabTwo").parent().hide()
                    }
                });

                 $("#newMeasurementTypeLinkTabTwo").click (function(event){
                    event.preventDefault();
                    if ($("#newUOMContainerTabTwo").parent().is(":hidden")){
                        $("#newUOMContainerTabTwo").parent().show();
                        $("#existingUOMContainerTabTwo").parent().hide();

                        $("#existingMeasurementTypeLinkContainerTabTwo").parent().show();
                        $("#newMeasurementTypeLinkContainerTabTwo").parent().hide();
                    }
                });
        
            }
        });
        
       
        
        $( "#dialog-form" ).dialog({
            autoOpen: false,
            height: 150,
            width: 300,
            modal: true,
            buttons: {
                "Create package type": function() {
                    var value = $("#nameType").val();
                    if ( value!= ""){
                        $("#typeOp").append("<OPTION VALUE=\"" + value + "\">" + value); 
                        $( this ).dialog( "close" );
                }
                    
                },
                Cancel: function() {
                    $( this ).dialog( "close" );
                }
            },
            close: function() {
                //allFields.val( "" ).removeClass( "ui-state-error" );
            }
        });


            

        //package grid
        
        function selectValueCheck(value,colname){
                    //var success = /^\d{5}$/.test(value);
                    
                    if (value==0 || value =="" || value ==null) 
                        return [false,"Invalid packageType. Please enter Package Type "];
                    else 
                        return [true,""];
                }
        var myGrid = $("#packages"),lastsel2;
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
                    url:'index.php/utilities/populatePackageInGrid',
                    datatype: 'json',
                    mtype: 'GET',
                    colNames:['Package Name','Description','Package Type'],
                    colModel :[ 
                        {name:'package_name', index:'package_name', width:80, align:'right',editable:false},
                        {name:'package_description', index:'package_description', width:140, align:'right',editable:true,editoptions:{size:"20",maxlength:"30"}},
                        {name:'package_type',index:'package_type', width:80, align:'right',editrules:{custom:true,custom_func:selectValueCheck},editable:true,edittype:"select",editoptions:{dataUrl:"index.php/utilities/populatePackageTypesEdit",buildSelect:function(response)
                        {
                            var select = "<select name=" + "pkgOpEdit" + "id =" +"pkgOpEdit" +">" +
                                        "<option value=" + ">Select one..." + response + "</select>";
                                    
                            return select;
                        }}}
                        
                    ],
                    pager: '#pager',
                    rowNum:10,
                    rowList:[5,10,20],
                    sortname: 'package_id',
                    sortorder: 'desc',
                    viewrecords: true,
                    gridview: true,
                    ignoreCase:true,
                    rownumbers:true,
                    height:'auto',
                    width:680,
                    caption: 'Packaging',
            
                    jsonReader : {
                        root:"packagingdata",
                        page: "page",
                        total: "total",
                        records: "records",
                        cell: "dprow",
                        id: "package_id"
                    },
                    onSelectRow: function(id){if(id && id!==lastsel2){
                            myGrid.restoreRow(lastsel2);
                            myGrid.editRow(id,editparameters);
                            lastsel2=id;
                        }
                    }
                    ,editurl:'index.php/utilities/updatePackages',
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
                                    url:'index.php/utilities/populateUomInSubgrid?q=2&id='+row_id,
                                    datatype: 'json',
                                    colNames: ['Denomination','Unit'],
                                    colModel: [
                                            {name:"denom",index:"denom",width:80},
                                            {name:"uom",index:"uom",width:130},
                                            
                                    ],
                                    rowNum:20,
                                    pager: pager_id,
                                    sortname: 'uom',
                                    sortorder: "asc",
                                    height: '100%',
                                    jsonReader : {
                                        root:"uomdata",
                                        page: "page",
                                        total: "total",
                                        records: "records",
                                        cell: "dprow",
                                        id: "id"
                                    }
                                });
                            jQuery("#"+subgrid_table_id).jqGrid('navGrid',"#"+pager_id,{edit:false,add:false,del:false,search:false})
                    },
                    subGridRowColapsed: function(subgrid_id, row_id) {
                            // this function is called before removing the data
                            //var subgrid_table_id;
                            //subgrid_table_id = subgrid_id+"_t";
                            //jQuery("#"+subgrid_table_id).remove();
                    }
                }).navGrid("#pager",{edit:false,add:false,del:false,search:false},{height:280,reloadAfterSubmit:false,closeAfterEdit:true,recreateForm:true,checkOnSubmit:true},{},{},{},{});
               
                myGrid.jqGrid('filterToolbar', {stringResult: true, searchOnEnter: true, defaultSearch : "cn"});
    });        

        </script>
        <style>
            .ui-widget-header {height:23.5px;}
        </style>
    </head>
     
    <body onload="init()">
         <?php  $this->load->view("common/menubar"); ?>
        <div style="display: block;height: auto;" class="shopifine-ui-dialog ui-widget ui-widget-content ui-corner-all" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-dialog-form">
       <div class="form-container tab-container" style="height:80%">
        <div id="tabs">           
            <ul>
                <li id="existingPkgLine"><a id="existingPkgLink" href="<?php echo site_url('utilities/loadExistingPackageFragment') ?>">Existing Package</a></li>
                <li id="newPkgLine"><a id="newPkgLink" href="<?php echo site_url('utilities/loadNewPackageFragment') ?>">New package</a></li>
            </ul>
        </div>
       </div>
            
        <div class="table-grid">
            <h1 id="table header">Packages</h1>
            <table id="packages"><tr><td/></tr></table> 
            <div id="pager"></div>
        </div>
            </div>
        
    <div id="dialog-form" title="Create New Type">
        <form>
            <label for="nameType">Name of Type: </label>
            <input type="text" name="nameType" id="nameType" />
        </form>
    </div>
        
        <?php $this->load->view("partial/footer"); ?>       
</body>   
</html>

<script type="text/javascript">
    
        function init(){
            var options = '<?php echo $options ?>';
            if (!options){
            $("#packDiv").hide();
            }
            
        }     
    </script>
    
<