<html>
    <head>
       <?php $this->load->view("common/header"); ?>
        
         <script>
            $(document).ready(function(){
                $("#deliverypointform").validate();
                
                //delivery grid
                
                //delivery grid validation
                
                function phoneCheck (value,colname){
                   var success =  /^(\d{7}(\d{7})?)?$/.test(value);
                   if (!success) 
                        return [false,"Invalid Phone. Phone number should be minimum 7 or maximum 14 digits"];
                    else 
                        return [true,""];
                }
                function pinCheck(value,colname){
                    var success = /^\d{5}$/.test(value);
                    
                    if (!success) 
                        return [false,"Invalid Pincode. Please enter valid one e.g. '560059' "];
                    else 
                        return [true,""];
                }
                
                var myGrid = $("#deliveryPoints"),lastsel2;
                var editparameters = {
                    "keys" : true,
                    "oneditfunc" : null,
                    "successfunc" : function(){
                        var successStatus = "Delivery point successfully edited";
                        $("#status-message").addClass("ui-state-highlight");
                        $("#status-message > p > span").addClass("ui-icon");
                        $("#status-message > p > span").addClass("ui-icon-info");
                        $("#status-message > p").append(successStatus);
                        myGrid.trigger("reloadGrid");
                        return true;
                    },
                    "aftersavefunc" : null,
                    "errorfunc": function(response)
                        {
                            var errorStatus = " Delivery point could not be updated due to internal error";
                            $("#status-message").addClass("ui-state-error");
                            $("#status-message > p > span").addClass("ui-icon");
                            $("#status-message > p > span").addClass("ui-icon-alert");
                            $("#status-message > p").append(errorStatus);

                        },
                    "afterrestorefunc" : null,
                    "restoreAfterError" : true,
                    "mtype" : "POST"
                };
                myGrid.jqGrid({
                    url:'index.php/utilities/populateDeliveryPoint',
                    datatype: 'json',
                    mtype: 'GET',
                    colNames:['Name','Address','City','Pin Code','Contact Number'],
                    colModel :[ 
                        {name:'name', index:'name', width:80, align:'right',editable:true,editoptions:{size:"20",maxlength:"30"}},
                        {name:'address', index:'address', width:140, align:'right',editable:true,editoptions:{size:"20",maxlength:"30"}},
                        {name:'city',index:'city', width:80, align:'right',editable:true,editoptions:{size:"20",maxlength:"30"}},
                        {name:'postcode', index:'postcode', width:80, align:'right',editrules:{custom:true,custom_func:pinCheck},editable:true,editoptions:{size:"20",maxlength:"30"}},
                        {name:'contact_number', index:'contact_number', width:80, align:'right',editrules:{custom:true,custom_func:phoneCheck},editable:true,editoptions:{size:"20",maxlength:"30"}}
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
                    width:680,
                    caption: 'Delivery Points',
            
                    jsonReader : {
                        root:"deliverypointdata",
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
                    ,editurl:'index.php/utilities/updateDeliverypoints'
                }).navGrid("#pager",{edit:false,add:false,del:false,search:false},{height:280,reloadAfterSubmit:false,closeAfterEdit:true,recreateForm:true,checkOnSubmit:true},{},{},{},{});
               
                myGrid.jqGrid('filterToolbar', {stringResult: true, searchOnEnter: true, defaultSearch : "cn"});
                
                    $("#addBtn").click(function(){
                        var bValid =  $("#deliverypointform").valid();
                        if (bValid ){
                            var name = $("#name").val();
                            var address = $("#address").val();
                            var city = $("#city").val();
                            var pin = $("#pin").val();
                            var contactNumber = $("#contactNumber").val();
                            $.ajax({url:"index.php/utilities/createDeliveryPoint",
                                    type:"POST",
                                    data:{name:name,address:address,city:city,pin:pin,contactNumber:contactNumber},

                                    success:function(response)
                                    {
                                        var successStatus = "Delivery point successfully added";
                                        $("#status-message").addClass("ui-state-highlight");
                                        $("#status-message > p > span").addClass("ui-icon");
                                        $("#status-message > p > span").addClass("ui-icon-info");
                                        $("#status-message > p").append(successStatus);
                                        myGrid.trigger("reloadGrid");
                                    },
                                    error:function(response)
                                    {
                                        var errorStatus = " Delivery point could not be added due to internal error";
                                        $("#status-message").addClass("ui-state-error");
                                        $("#status-message > p > span").addClass("ui-icon");
                                        $("#status-message > p > span").addClass("ui-icon-alert");
                                        $("#status-message > p").append(errorStatus);

                                    }
                        })
                    }
                });
            });
        </script>
    </head>
     
    <body>
         <?php $this->load->view("common/menubar"); ?>
        
        <div style="display: block;height: auto;" class="shopifine-ui-dialog ui-dialog-medium ui-widget ui-widget-content ui-corner-all" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-dialog-form">
            <div class="form-container single-column-form">
                <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
                    <span class="ui-dialog-title" id="ui-dialog-title-dialog-form">&nbsp;</span>
                    <a href="#" class="ui-dialog-titlebar-close ui-corner-all" role="button">

                    </a>
                </div>
                <div  class="ui-dialog-content" style="width: auto; min-height: 65.1333px; height: auto;" scrolltop="0" scrollleft="0">
                    <h1 id="formHeader">Add Delivery Point</h1> 
                    <form id="deliverypointform">
                        <fieldset>
                            <div class="row single-column-row">
                                <div class="column single-column">
                                <div class="field">
                                        <label for="name">Name of delivery point :</label>  
                                        <input id="name" name ="name" type="text" class="required"/>  
                                    </div> 
                                </div>
                                <div class="column single-column">
                                    <div class="field">
                                        <label for="address">Address :</label>  
                                        <input id="address" name ="address" type="text" class="required"/>  
                                    </div>
                                </div>
                            </div>
                            <div class="row single-column-row">
                                <div class="column single-column">
                                    <div class="field">
                                    <label for="city">City :</label>  
                                        <input id="city" name ="city" type="text" class="required"/>  
                                    </div> 
                                </div>
                                <div class="column single-column">
                                    <div class="field">
                                        <label for="pin">PIN :</label>  
                                            <input id="pin" name ="pin" type="text" class="required"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row single-column-row">
                                <div class="column single-column">
                                    <div class="field"> 
                                        <label for="contactNumber">Contact Number :</label>  
                                            <input id="contactNumber" name ="contactNumber" type="text"/>     
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
                <div class="ui-dialog-buttonpane shopifine-ui-widget-content ui-helper-clearfix">
                    <div class="shopifine-ui-dialog-buttonset">
                        <button id="addBtn" type="button" class="shopifine-ui-button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false">
                            <span class="ui-button-text">Add Delivery Point</span>
                        </button>
                    </div> 
                </div>
            </div>
            <?php $this->load->view("common/message"); ?>
            <div class="table-grid">
                <h1 id="table header">Delivery Points</h1>
                <table id="deliveryPoints"><tr><td/></tr></table> 
                <div id="pager"></div>
            </div>
        </div>
        <?php $this->load->view("partial/footer"); ?>
    </body>
</html>
<script>
    
    
</script>

 