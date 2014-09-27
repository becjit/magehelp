<html>
    <head>
       <?php $this->load->view("common/header"); ?>
        <link rel="stylesheet" rev="stylesheet" href="<?php echo base_url();?>css/chosen.css" />
        <script src="<?php echo base_url();?>js/chosen.jquery.min.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
        
<!--        <script type="text/javascript" src="http://jzaefferer.github.com/jquery-validation/jquery.validate.js"></script>-->
        <style>
            .column {
            float: left;
            padding: 1em;
            width:100%;
            }
            
            .extra-wide{
                width:95%;
            }
            .field{
                width:100%;
            }
            .ui-widget-header {height:12px;}
           
            .ui-combobox {
                width:14em;
            }
            .ui-combobox-input{
                width:12em;
            }
             #supplierOp-input{
                width:10em;
            }
            #warehouseOp-input{
                width:10em;
            }
             
            .calculated {
                color: green;
                font-size: 90%;
            }
            .row{
                width:100%;
            }
            
            .shopifine-ro-label {
                float: left;
                padding-right: 0.5em;
                width: 50%;
                word-wrap: break-word;
                color:#2E6E9E;
                font-size: 110%
            }

            .shopifine-output {
             float: right;
             width: 45%;
             word-wrap: break-word;
             font-weight:bold;
            }
            
            .ui-tabs {
                height: 80%;
                margin: 0 auto;
                width: 85%;
                left:0;
            }
            #notetab {
                height:30em;
            }
            #details {
                height:12em;
            }
            .ui-tabs-nav{
                height:22px;
            }
            .labeldiv {
                color: #2E6E9E;
                float: left;
                font-size: 110%;
                font-weight: bold;
                margin-right: .5em;
                width: 18%;
                word-wrap: break-word;
            }
            .valuediv {
                float: left;
                font-weight: bold;
                width: 45%;
                word-wrap: break-word;
            }
            label.error {
                width:8em ;
                margin-right: 0;
            }
            #status-message-li{
                color: red;
                font-size: 110%;
                font-style: italic;
                margin: 0 auto;
                width: 80%;
            }
            
            .help-message-left{
                color: green;
                font-size: 90%;
                font-style: italic;
                margin: 0 auto;
                width: 90%;
                float:left
            }
           
            .chzn-container{
                font-size:inherit;
            }
            fieldset{
                padding: 1em;
                margin-top: 0;
            }
            .single-column-row-item{
                margin-left:0;
            }
            .edit-column{
                width:46%;
            }
            .labeldiv-edit{width:25%}
            .valuediv-edit{width:70%}
            #content_area{width:80%}
       
        </style>
       
        <script type="text/javascript">
                $(function() {
       
         $.validator.addMethod('productrequired', function(value, element) {
              
              var products = $("#productOptions").val();
              value = $("#"+element.id).is(":checked");
              if (!value  && products == null){
                      return false;
              }
              return true;
          }, 'Select From Products Dropdownor Select Apply To All CheckBox '); 
          
         $("#itemForm").validate({
             rules:{
                 allCB:{productrequired:true}
             }
         });
         $("#pricelistForm").validate({});
         $("#pricelistFormEdit").validate({});
         $("#priceFormEdit").validate({});
         
         

         
        // datepicker for Add Form 
        
        $( "#validfrom ,#validto ,#validfrom_edit, #validto_edit" ).datepicker({
            showOn: "button",
            buttonImage: "images/calendar.gif",
            buttonImageOnly: true,
            dateFormat:"dd/mm/yy",
            minDate:0
        });
        
       
        //end datepicker
              
       $( "#dialog-form" ).dialog({
                                autoOpen: false,
                                height: 'auto',
                                width: '65%',
                                position:[220,25],
                                modal: true,
                                buttons: {
                                    "DoneButton": {
                                        id:"doneBtn",
                                        text:"Done",
                                        click:function() {
                                       
                                        var isValid = $("#pricelistFormEdit").valid();
//                                       
                                        if (isValid){
                                            $.ajax({url:"index.php/procurement/createPricelist",
                                                 type:"POST",
                                                 data:{  pricelistId:myGrid.getGridParam('selrow'),
                                                         isactive:$("#active_edit").val(),
                                                         validfrom:$("#validfrom_edit").val(),
                                                         validto:$("#validto_edit").val(),
                                                         ischanged:$("#ischanged").val(),
                                                         typeOp:$("#type_edit").text()
                                                     },

                                                 success:function(response)
                                                 {
                                                    

                                                 } //end success of Ajax
                                             }) //end ajax
                                            $( this ).dialog( "close" );
                                         }  //end ifvalid
                                        
                                    }}, //end of Create button
                                    Cancel: function() {
                                        //in cancel just recset notes
                                        $("#receivingNotes").val("");
                                        $( this ).dialog( "close" );
                                    }
                                },//end buttons
                                open: function(){
                                    
                                    var pricelist_id = myGrid.getGridParam('selrow');
                                    var supplier_id = myGrid.getCell(pricelist_id,'supplier_id');
                                    $("#pl_name_edit").text(myGrid.getCell(pricelist_id,'name'));
                                    $("#supplier_name_edit").text(myGrid.getCell(pricelist_id,'supplier_name'));
                                    
                                    $("#type_edit").text(myGrid.getCell(pricelist_id,'type'));
                                    $("#active_edit").val(myGrid.getCell(pricelist_id,'active'));
                                    if (myGrid.getCell(pricelist_id,'type')=='version'){
                                        $("#versionCntnrEdit").show();
                                        $("#bv_edit").text(myGrid.getCell(pricelist_id,'parent_name'));
                                        $("#vn_edit").text(myGrid.getCell(pricelist_id,'version'));
                                        var d1 =Date.parse(myGrid.getCell(pricelist_id,'valid_from'));
                                        $("#validfrom_edit").val(d1.toString('dd/MM/yyyy'));
                                        d1 =Date.parse(myGrid.getCell(pricelist_id,'valid_to'));
                                        $("#validto_edit").val(d1.toString('dd/MM/yyyy'));
                                    }
                                    
                                    //$("#pricelistRef").text(myGrid.getCell(pricelist_id,'name'));
                                     $("#price_grid").jqGrid({
                                        url:'index.php/procurement/populatePriceData',
                                        datatype: 'json',
                                        mtype: 'POST',
                                        postData:{pricelist_id: pricelist_id,supplier_id:supplier_id},
                                        colNames:['LI','Barcode','Product','Product Id','Manufacturer','Model','Base Price','Inherited'],
                                        colModel :[
                                            {name:'lineitem_id', index:'lineitem_id',hidden:true},
                                            {name:'barcode', index:'barcode',editable:false, width:100, align:'right'},
                                            {name:'product_name', index:'product_name',editable:false, width:100, align:'right'},
                                            {name:'product_id', index:'product_id',hidden:true},
                                            {name:'manufacturer', index:'manufacturer', editable:true,width:80, align:'right'},
                                            {name:'model',index:'model',editable:true, width:80, align:'right'},
                                            {name:'base_price',index:'base_price',editable:true, width:80, align:'right'},
                                            {name:'inherited',index:'inherited',editable:true, width:40, align:'right'},
                                            

                                        ],
                                        pager: '#pagerPk',
                                        rowNum:10,
                                        rowList:[5,10,20],
                                        sortname: 'id',
                                        sortorder: 'asc',
                                        viewrecords: true,
                                        gridview: true,
                                        ignoreCase:true,
                                        rownumbers:true,
                                        height:'auto',
                                        width:680,
                                        caption: 'Line Items',

                                        jsonReader : {
                                            root:"ratecontractdata",
                                            page: "page",
                                            total: "total",
                                            records: "records",
                                            cell: "dprow",
                                            id: "id"
                                        },
                                        onSelectRow: function(ids) {
                                                var product_id = $("#price_grid").getCell(ids,'product_id');
                                                var product_name = $("#price_grid").getCell(ids,'product_name');
                                                var pricelist_id= myGrid.getGridParam('selrow');
                                                if(ids == null) {
                                                        
                                                        if(jQuery("#rules_grid").jqGrid('getGridParam','records') >0 )
                                                        {
                                                                jQuery("#rules_grid").jqGrid('setGridParam',{url:"index.php/procurement/populatePriceRules?pricelist_id=0",page:1});
                                                                jQuery("#rules_grid").jqGrid('setCaption',"Price Rules For The Product "+ids)
                                                                .trigger('reloadGrid');
                                                        }
                                                } else {
                                                        jQuery("#rules_grid").jqGrid('setGridParam',{url:"index.php/procurement/populatePriceRules?pricelist_id="+pricelist_id+"&product_id="+product_id,page:1});
                                                        jQuery("#rules_grid").jqGrid('setCaption',"Price Rules For The Product "+product_name)
                                                        .trigger('reloadGrid');			
                                                }
                                        },
                                        editurl:'index.php/procurement/modifyQuoteItem'

                                    }).navGrid("#pagerPk",{view:true,edit:false,add:false,del:false,search:false},{},{},{},{},{});
                                    $("#price_grid").jqGrid('filterToolbar', {stringResult: true, searchOnEnter: true, defaultSearch : "cn"});
                                    $("#price_grid").jqGrid('navButtonAdd','#pagerPk',{
                                        caption:"", 
                                        title:"Add Pricing Details",
                                        buttonicon:"ui-icon-plus",
                                        id:"add_price_grid",
                                        onClickButton : function () { 
                                            //need to pass grid id for dynamic reload;
                                            var gridData ={'grid_id':'price_grid','pricelist_id':myGrid.getGridParam('selrow'),'action':'add_price','url':'index.php/procurement/addSupplierRate'};
                                             $( "#dialog-form-item" ).data('grid_data',gridData).dialog( "open" );
                                         } 
                                     });
                                     $("#price_grid").jqGrid('navButtonAdd','#pagerPk',{
                                        caption:"", 
                                        title:"Edit Price",
                                        buttonicon:"ui-icon-pencil",
                                        id:"edit_price_grid",
                                        onClickButton : function () { 
                                            //need to pass grid id for dynamic reload;
                                            var rowid = $("#price_grid").getGridParam('selrow');
                                            if (rowid !=null && rowid!=undefined){
                                                if ($("#price_grid").getCell(rowid,'inherited') == "No"){
                                                    $( "#dialog-price-edit" ).dialog( "open" );
                                                }
                                                else {
                                                    $( "#modal-warning-inherited" ).dialog( "open" );
                                                    
                                                }
                                                 
                                                 
                                            }
                                            else {
                                                 $( "#modal-warning" ).dialog( "open" );
                                            }
                                         } 
                                     });
                                      $("#price_grid").jqGrid('navButtonAdd','#pagerPk',{
                                        caption:"", 
                                        title:"Delete Price",
                                        buttonicon:"ui-icon-trash",
                                        id:"delete_price_grid",
                                        onClickButton : function () { 
                                            //need to pass grid id for dynamic reload;
                                            var rowid = $("#price_grid").getGridParam('selrow');
                                            if (rowid !=null && rowid!=undefined){
                                                if ($("#price_grid").getCell(rowid,'inherited') == "No"){
                                                     var gridData ={'grid_id':'price_grid',
                                                                        'id':$("#price_grid").getCell(rowid,'lineitem_id'),'url':"index.php/procurement/deletePrice"}
                                                    $( "#dialog-confirm" ).data('grid_data',gridData).dialog( "open" );
                                                }
                                                else {
                                                    $( "#modal-warning-inherited" ).dialog( "open" );
                                                    
                                                }
                                                 
                                                 
                                            }
                                            else {
                                                 $( "#modal-warning" ).dialog( "open" );
                                            }
                                         } 
                                     });
                                     $("#del_price_grid").insertAfter("#edit_price_grid");    
//                                     jQuery("#rules_grid").jqGrid({
//                                            height: 'auto',
//                                            url:'index.php/procurement/populatePriceRules?pricelist_id=0',
//                                            datatype: "json",
//                                            colNames:[/*'Product ',*/'Rule Type', 'Inherited','Precedence', 'Qualifier','Condition', 'Discount Type','Value','Operator','Qual Val Frm','Qual Val To','Qual Val Pnt'],
//                                            colModel:[
////                                                    {name:'product_name',index:'product_name', width:55,editable:false},
//                                                    {name:'rule_type',index:'rule_type', width:80},
//                                                     {name:'inherited',index:'inherited', width:60},
//                                                    {name:'precedence',index:'precedence', width:60, align:"right"},
//                                                   
//                                                    {name:'qualifier',index:'qualifier', width:60, align:"right"},		
//                                                    {name:'condition',index:'condition', width:180,align:"right", sortable:false, search:false,editable:false},
//                                                    {name:'discount_type',index:'discount_type', width:80, align:"right"},		
//                                                    {name:'discount_value',index:'discount_value', width:80,align:"right", sortable:false, search:false,editable:false},
//                                                    {name:'operator',index:'operator', hidden:true},
//                                                    {name:'qualifier_value_from',index:'qualifier_value_from', hidden:true},
//                                                    {name:'qualifier_value_to',index:'qualifier_value_to', hidden:true},
//                                                    {name:'qualifier_value_point',index:'qualifier_value_point', hidden:true}
//                                            ],      
//                                            rowNum:5,
//                                            rowList:[5,10,20],
//                                            pager: '#pagerRules',
//                                            sortname: 'product_name',
//                                        viewrecords: true,
//                                        sortorder: "asc",
//                                            multiselect: true,
//                                            caption:"Price Rules",
//                                            jsonReader : {
//                                            root:"rulesdata",
//                                            page: "page",
//                                            total: "total",
//                                            records: "records",
//                                            cell: "dprow",
//                                            id: "id"
//                                        }
//                                    }).navGrid('#pagerRules',{add:false,edit:false,del:false,view:true});
                                    var settingsObj = {grid_id:'rules_grid',pager:'pagerRules'};
                                    prepareRulesGrid(settingsObj);
                                    $("#rules_grid").navGrid('#pagerRules',{add:false,edit:false,del:false,view:true});
                                    $("#rules_grid").jqGrid('navButtonAdd','#pagerRules',{
                                        caption:"", 
                                        title:"Add Rules",
                                        buttonicon:"ui-icon-plus",
                                        id:"add_rules_grid",
                                        onClickButton : function () { 
                                            //need to pass grid id for dynamic reload;
                                            var gridData ={'grid_id':'rules_grid','action':'add_rule','pricelist_id':myGrid.getGridParam('selrow'),'url':'index.php/procurement/addSupplierRule'};
                                             $( "#dialog-form-item" ).data('grid_data',gridData).dialog( "open" );
                                         } 
                                     });
                                     $("#rules_grid").jqGrid('navButtonAdd','#pagerRules',{
                                        caption:"", 
                                        title:"Edit Rules",
                                        buttonicon:"ui-icon-pencil",
                                        id:"edit_rules_grid",
                                        onClickButton : function () { 
                                            //need to pass grid id for dynamic reload;
                                            var rowid = $("#rules_grid").getGridParam('selrow');
                                            if (rowid !=null && rowid!=undefined){
                                                var gridData ={'grid_id':'rules_grid','action':'edit_rule','pricelist_id':myGrid.getGridParam('selrow'),'url':'index.php/procurement/addSupplierRule'};
                                             $( "#dialog-form-item" ).data('grid_data',gridData).dialog( "open" );
                                            }
                                            else {
                                                $( "#modal-warning" ).dialog( "open" );
                                            }
                                         } 
                                     });
                                },

                                close: function() {
                                    $("#pricelistFormEdit").data('validator').resetForm();
                                    $('#pricelistFormEdit')[0].reset();
                                    $("#versionCntnrEdit").hide();
                                    $("#pricelistFormEdit .valuediv").text("");
                                    $("#price_grid").jqGrid("GridUnload");
                                    $("#rules_grid").jqGrid("GridUnload");
                                    $("#pricelist_grid").trigger("reloadGrid");
                                    
                                }
                            });
        
       
        // Main Request For Quotation Grid                    
        
        var myGrid = $("#pricelist_grid"),lastsel2;
        
       
                
                
                myGrid.jqGrid({
                    url:'index.php/procurement/populatePriceLists',
                    datatype: 'json',
                    mtype: 'GET',
                    colNames:['Name','Parent Name','Supplier Id','Supplier','Valid From','Valid Till','Active','Type','Version'],
                    colModel :[ 
                        
                        {name:'name', index:'name', width:220, align:'right',editable:false,searchoptions: { sopt: ['cn','eq']}},
                        {name:'parent_name', index:'parent_name', width:220, align:'right',editable:false,searchoptions: { sopt: ['cn','eq']}},
                        {name:'supplier_id', index:'supplier_id',hidden:true},
                        {name:'supplier_name', index:'supplier_name', width:100, align:'right',editable:false,searchoptions: { sopt: ['cn','eq']}},
                        {name:'valid_from', index:'valid_from', width:80, align:'right',editable:true,sorttype:"date",searchoptions: { sopt: ['eq', 'ne','ge','lt','gt','le'], 
                        dataInit: function (elem) { $(elem).datepicker({ showButtonPanel: true,dateFormat:"yy-mm-dd" }) } }},
                        {name:'valid_to', index:'valid_to', width:80, align:'right',editable:true,sorttype:"date",searchoptions: { sopt: ['eq', 'ne','ge','lt','gt','le'], 
                        dataInit: function (elem) { $(elem).datepicker({ showButtonPanel: true,dateFormat:"yy-mm-dd"}) } }},
                        {name:'active', index:'active', width:40, align:'right',editable:true,edittype:"select", formatter:'select', editoptions:{value:"1:Yes;0:No"},searchoptions: { sopt: ['eq']}},
                        {name:'type', index:'type',editable:false, width:40, align:'right',searchoptions: { sopt: ['eq']}},
                        {name:'version', index:'version',editable:false, width:40,align:'center',searchoptions: { sopt: ['eq']}}
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
                    width:800,
                    caption: 'Pricelists',
            
                    jsonReader : {
                        root:"pricelistdata",
                        page: "page",
                        total: "total",
                        records: "records",
                        cell: "dprow",
                        id: "id"
                    }
                }).navGrid("#pager",{edit:false,add:false,view:false,del:false,search:true},{},{},{},{multipleSearch:true},{});
               
                myGrid.jqGrid('navButtonAdd','#pager',{
                   caption:"", 
                   title:"Edit Price List",
                   buttonicon:"ui-icon-pencil",
                   id:"add_pricelist_grid",
                   onClickButton : function () {
                   var rowid = myGrid.getGridParam('selrow');
                   
                   if (rowid!=null && rowid!=undefined){
                       $( "#dialog-form" ).dialog('option', 'title', 'Details Of ' + myGrid.getCell(myGrid.getGridParam('selrow'),'name') + ' Pricelist');
                        $( "#dialog-form" ).dialog( "open" );
                   } 
                    else {
                        $( "#modal-warning" ).dialog("open") ;
                    }
                        
                    } 
                });
                
                myGrid.jqGrid('filterToolbar', {stringResult: true, searchOnEnter: true, defaultSearch : "cn"});
                
        //line item dialog   
        $( "#dialog-form-item" ).dialog({
            autoOpen: false,
            height: 'auto',
            width: '40%',
            position:[350,25],
            modal: true,
            buttons: {
                "Done": function() {
                   var isvalid = $("#itemForm").valid();
                   var grid = $(this).data('grid_data').grid_id;
                   var pricelist_id = $(this).data('grid_data').pricelist_id;
                   var url = $(this).data('grid_data').url;
                   var action = $(this).data('grid_data').action;
                   var isprecchnged = 1;
                   var oldprecedence;
                   if (action =='edit_rule'){
                       oldprecedence=$("#rules_grid").getCell($("#rules_grid").getGridParam('selrow'),'precedence')
                       if ($("#precedence").val()==oldprecedence){
                          isprecchnged = 0; 
                       }
                   }
                   if (action=='edit_rule' && $("#ischangeditem").val()==0){
                       //dont submit. Nothing has changed
                       $( this ).dialog( "close" );
                   }
                   else {
                       if (isvalid){
                            $.ajax({
                                url:url,
                                type:"POST",
                                data:{

                                    productOptions:$("#productOptions").val(),
                                    qualifier:$("#qualifier").val(),
                                    operator:$("#operator").val(),
                                    discount_type:$("#discount_type").val(),
                                    qualifier_value_from:$("#qualifier_value_from").val(),
                                    qualifier_value_to:$("#qualifier_value_to").val(),
                                    qualifier_value:$("#qualifier_value").val(),
                                    discount_value:$("#discount_value").val(),
                                    product_price:$("#product_price").val(),
                                    pricelist_id:pricelist_id,
                                    precedence:$("#precedence").val(),
                                    apply_to_all:$("#allCB").is(":checked"),
                                    action:action,
                                    precedencechanged:isprecchnged,
                                    oldprecedence:oldprecedence,
                                    rule_id:$("#"+grid).getGridParam('selrow'),
                                    product_id:$("#price_grid").getCell($("#price_grid").getGridParam('selrow'),'product_id')
                                },
                                success:function (response){
                                    //console.log("grid " + grid);
                                    $("#"+grid).trigger("reloadGrid");
                                    $("#status-message-li").empty();
                                    //reload Main Grid
                                    $("#quotes").trigger("reloadGrid");
                                }
                            })
                            $( this ).dialog( "close" );
                        }
                   }
                   

                },
                Cancel: function() {

                    $( this ).dialog( "close" );
                }
            },
            open: function(event,ui){
                var grid_id = $(this).data('grid_data').grid_id;
                var action = $(this).data('grid_data').action;
                var rowid=$("#"+grid_id).getGridParam('selrow');
                var url="index.php/procurement/getProductsToAddRateDropDown";
                
                $("#productOptions").hide();
                if (action =='add_rule' || action =='edit_rule'){
                    $("#priceCntnr").hide();
                    $('#ruleCntnr').show();
                    
                    if (action =='add_rule'){
                        $("#formHeaderItem").text("Add Rule");
                        $('#allCBCntnr').show();
                        url = "index.php/procurement/getAllProductsDropDownForSupplier";
                    }
                    else if (action =='edit_rule'){
                        $("#formHeaderItem").text("Edit Rule");
                        $("#product_rule_edit").show();
                        $("#productOptions_chzn").hide();
                        var op = $("#"+grid_id).getCell(rowid,'operator');
                        var qualifier = $("#"+grid_id).getCell(rowid,'qualifier');
                        if (qualifier!='product'){
                            $("#operatorCntnr").show();
                            if (op!="between"){
                                $("#qualRangeCntnr").hide();
                                $("#qualvalCntnr").show();
                            }
                            else {
                                $("#qualRangeCntnr").show();
                                $("#qualvalCntnr").hide();
                            }
                        }
                        
                        $("#product_rule_edit").text($("#price_grid").getCell($("#price_grid").getGridParam('selrow'),'product_name'));
                        $("#qualifier").val(qualifier);
                        $("#precedence").val($("#"+grid_id).getCell(rowid,'precedence'));
                        $("#discount_type").val($("#"+grid_id).getCell(rowid,'discount_type'));
                        $("#operator").val($("#"+grid_id).getCell(rowid,'operator'));
                        $("#qualifier_value_from").val($("#"+grid_id).getCell(rowid,'qualifier_value_from'));
                        $("#qualifier_value_to").val($("#"+grid_id).getCell(rowid,'qualifier_value_to'));
                        $("#qualifier_value").val($("#"+grid_id).getCell(rowid,'qualifier_value_point'));
                        $("#discount_value").val($("#"+grid_id).getCell(rowid,'discount_value'));
                        
                    }
                }
                else if (action =='add_price'){
                    $("#formHeaderItem").text("Add Price & Rule");
                     $("#priceCntnr").show();
                    $('#ruleCntnr').hide();
                }
                if (action =='add_rule' || action =='add_price'){
                    $.ajax({
                        url:url,
                        type:"POST",
                        data:{
                            pricelist_id:myGrid.getGridParam('selrow'),
                            supplier_id:myGrid.getCell(myGrid.getGridParam('selrow'),'supplier_id')
                        },
                        success:function (response){
                            $("#productOptions").children().remove();
                            $("#productOptions").append(response); 
                            $('.chzn-select').trigger('liszt:updated');
                        }
                    });
                }
            },
            close: function(event,ui) { 
                
                $("#itemForm").data('validator').resetForm();
                $('#itemForm')[0].reset();
                $('#itemForm .inithide').hide();
                $('#itemForm .initshow').show();
                $("#ischangeditem").val("0");
                $('#itemForm .valuediv').text("");
                $("#prodCntnr").show();
                $("#productOptions_chzn").show();
                $('.chzn-select').val('').trigger('liszt:updated');
            }
        });

        
        $( "#modal-warning" ).dialog({
            autoOpen:false,
            height: 90,
            modal: true
        });
        $( "#modal-warning-inherited" ).dialog({
            autoOpen:false,
            height: 120,
            modal: true
        });
        $( "#modal-warning-status" ).dialog({
            autoOpen:false,
            height: 120,
           
            modal: true
        });
        $( "#dialog-price-edit" ).dialog({
            autoOpen: false,
            height: 'auto',
            width: '30%',
            position:[500,25],
            modal: true,
            buttons: {
                "Edit": function() {
                   var isvalid = $("#priceFormEdit").valid();
                   if (isvalid){
                       $.ajax({
                           url:"index.php/procurement/editPrice",
                           type:"POST",
                           data:{
                               lineitem_id:$("#price_grid").getCell($("#price_grid").getGridParam("selrow"),'lineitem_id'),
                               price:$("#product_price_edit").val()
                               
                           },
                           success:function (response){
                               $("#price_grid").trigger("reloadGrid");
                           }
                              
                       })
                       $( this ).dialog( "close" );
                   }

                },
                Cancel: function() {
                    $( this ).dialog( "close" );
                }
            },
            close: function(event,ui) { 
                $("#priceFormEdit").data('validator').resetForm();
                $('#priceFormEdit')[0].reset();
            }
        });
        
        $( "#dialog-confirm" ).dialog({
            resizable: false,
            height:150,
             width:500,
            modal: true,
            autoOpen:false,
            buttons: {
                "I Am Sure": function() {
                    var grid = $(this).data('grid_data').grid_id;
                    
                    var url = $(this).data('grid_data').url;
                    var id=  $(this).data('grid_data').id;
                     $.ajax({
                            url:url,
                            type:"POST",
                            data:{
                                id:id
                            },
                            success:function (response){
                                $("#"+grid).trigger("reloadGrid");
                            },
                            error:function (response){
                                emptyMessages();
                                 showErrorMessage("The Selected Orders Could Not Be Processed Due To Internal Error")

                            }

                        });
                           
                       
                       $( this ).dialog( "close" );
                    
                },
                Cancel: function() {
                    $( this ).dialog( "close" );
                }
            }
        });
       
       $("#typeOp").change(function (){
           var val= $(this).val();
           if (val=='version'){
               $("#versionCntnr").show();
               $("#supplilerPricelistCntnr").hide();
               //$("#nameCntnr").hide();
           }
           else if (val=='supplierpl'){
               $("#versionCntnr").hide();
               $("#supplilerPricelistCntnr").show();
               //$("#nameCntnr").show();
           }
//           else {
//               $("#versionCntnr").hide();
//               $("#supplilerPricelistCntnr").hide();
//               $("#nameCntnr").show();
//           }
           
        }
       )
       $("#qualifier").change(function(){
           var val = $(this).val();
           if (val!='product'){
               $("#operatorCntnr").show();
           }
           else {
               $("#operatorCntnr").hide();
           }
       });
       $("#operator").change(function(){
           var val = $(this).val();
           console.log(val);
           
           if (val!="between"){
               $("#qualRangeCntnr").hide();
               $("#qualvalCntnr").show();
           }
           else {
               $("#qualRangeCntnr").show();
               $("#qualvalCntnr").hide();
           }
       });
       $("#ruleCB").change(function(){
            if (this.checked){
                    $("#ruleCntnr").show();
                }
                else {
                    $("#ruleCntnr").hide();
                }
       })
       $("#allCB").change(function(){
            if (this.checked){
                    $("#prodCntnr").hide();
                    //$("#allCB").val("all");
                    
                }
                else {
                    $("#prodCntnr").show();
                    //$("#allCB").val("");
                }
       })
       $("#discount_type").change(function(){
           var val = $(this).val();
           if (val!="free"){
               $("#discount_value").addClass("number");
               $("#discount_value").removeClass("digits");
           }
           else {
                $("#discount_value").removeClass("number");
                $("#discount_value").addClass("digits");
           }
       });
       $("#addBtn").click(function(){
                var bValid =  $("#pricelistForm").valid();
                if (bValid ){
                    
                   $.ajax({url:"index.php/procurement/createPricelist",
                        type:"POST",
                        data:{  pricelistId:$("#pricelistId").val(),
                                typeOp:$("#typeOp").val(),
                                name:$("#name").val(),
                                supplierPriceListOp:$("#supplierPriceListOp").val(),
                                supplierPriceListName:$("#supplierPriceListOp option:selected").text(),
                                supplierOp:$("#supplierOp").val(),
                                supplierName:$("#supplierOp option:selected").text(),
                                validfrom:$("#validfrom").val(),
                                validto:$("#validto").val(),
                                rootPriceListOp:$("#rootPriceListOp").val()

                            },

                            success:function(response)
                            {
                                var successStatus = "Pricelist successfully added";
                                emptyMessages ();
                                showSuccessMessage(successStatus);
                                myGrid.trigger("reloadGrid");
                            },
                            error:function(response)
                            {
                                emptyMessages ();
                                var errorStatus = "Pricelist could not be added due to internal error";
                                showErrorMessage(errorStatus);

                            }
                })
            }
        });
        $("#active_edit,#validfrom_edit,#validto_edit").change(function (){
        $("#ischanged").val("1");
        })
        $(".chzn-select").chosen();

        $("#itemForm input,#itemForm select").change(function (){
            $("#ischangeditem").val("1");
        });
       
    
    });        

        </script>
        
    </head>
     
    <body>
        <?php  $this->load->view("common/menubar"); ?>
        <div id="modal-warning" title="Warning">
            <p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>Please Select Exactly One Row</p>
        </div>
         <div id="modal-warning-status" title="Error" class="ui-state-error" style="border:none;">
            <p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>Only Completely Received Orders i.e. With Status "Received" 
                Can Be Readied For Invoice</p>
        </div>
        <div id="modal-warning-inherited" title="Warning">
            <p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>Inherited Prices Can Not Be Edited. Undefined  Prices Should Be Added Using 'Add Pricing' Option </p>
        </div>
        <div id="dialog-confirm" title="Warning">
            <p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>
                Once Deleted These Rates Con Not Be Recovered Again.Are You Sure?</p>
        </div>
        <div id="dialog-price-edit" title="Edit Price">
            <form id="priceFormEdit">
                <div class="column single-column" style="width:85%">
                    <div class="field">
                        <label for="product_price_edit" class="labeldiv">Price:</label>  
                       <input id="product_price_edit" id="product_price_edit" type="text" class="required number"/>

                    </div>
                </div>
            </form>
        </div>
        
        <div id ="dialog-form">
            
            <form id="pricelistFormEdit" style="width:65%; margin:0 auto;">
                <fieldset style="border:1px solid #A6C9E2">
                     <legend style="color: #2E6E9E;font-size: 110%;font-weight: bold;margin-left: 15px;">
                                Price List Basic</legend>
                    <div class="row">
                        <div class="column edit-column">
                            
                            <div class="field">
                                <div class="labeldiv labeldiv-edit">PriceList Name:</div>  
                                <div class="valuediv valuediv-edit" id="pl_name_edit" name ="pl_name_edit"></div>
                            </div>
                         
                        </div>
                        <div class="column edit-column">
                            
                            <div class="field">
                                <div class="labeldiv labeldiv-edit">Supplier Name:</div>  
                                <div class="valuediv valuediv-edit" id="supplier_name_edit" name ="supplier_name_edit"></div>
                            </div>
                         
                        </div> 
                    </div>
                    <div class="row">
                        <div class="column edit-column">
                            
                            <div class="field">
                                <div class="labeldiv labeldiv-edit">Type:</div>  
                                <div class="valuediv valuediv-edit" id="type_edit" name ="type_edit"></div>
                            </div>
                         
                        </div>
                        <div class="column edit-column">
                            
                            <div class="field">
                                <div class="labeldiv labeldiv-edit">Active:</div>  
                                <select name="active_edit" id ="active_edit" class="required">
                                            
                                    <option value="1">Yes
                                    <option value="0">No
                                </select>
                            </div>
                         
                        </div> 
                    </div>
                   

                      <div id="versionCntnrEdit" style="display:none">
                            <div class="column edit-column">
                                
                                <div class="field">
                                    <div class="labeldiv labeldiv-edit">Base Version:</div>  
                                    <div class="valuediv valuediv-edit" id="bv_edit" name ="bv_edit"></div>
                                </div>

                            </div>
                          <div class="column edit-column">
                                
                                <div class="field">
                                    <div class="labeldiv labeldiv-edit">Version #:</div>  
                                    <div class="valuediv valuediv-edit" id="vn_edit" name ="vn_edit"></div>
                                </div>

                            </div>
                          <div class="column edit-column">
                                
                               <div class="field">
                                    <label for="validfrom_edit" class="labeldiv">Valid From Date:</label>  
                                    <input id="validfrom_edit" name ="validfrom_edit" type="text" class="required dateValidate" style="width:12em;"/>
                                </div>

                            </div>
                          <div class="column edit-column">
                                
                                <div class="field">
                                    <label for="validto_edit" class="labeldiv">Valid To Date:</label>  
                                    <input id="validto_edit" name ="validto_edit" type="text" class="required dateValidate" style="width:12em;"/>
                                </div>

                            </div>
                      </div>
                      <input type="hidden" id="ischanged" name="ischanged" value="0"/>
                </fieldset>
            </form>
                  
            <div class="table-grid" style="padding-top:2em;">
                <div>
                    <h1 id="pkdheader">Product Prices And Rules  </h1>
                    
                </div>
                
                <table id="price_grid"><tr><td/></tr></table> 
                <div id="pagerPk"></div>
            </div>
            <div class="table-grid" style="padding-top:2em;">
                
                <table id="rules_grid"><tr><td/></tr></table> 
                <div id="pagerRules"></div>
            </div>
        </div>
        
        <div style="display: block;height: auto" class="shopifine-ui-dialog ui-widget ui-widget-content ui-corner-all" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-dialog-form">
            
            
            <div class="form-container single-column-form">
                <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
                    <span class="ui-dialog-title" id="ui-dialog-title-dialog-form">&nbsp;</span>
                    <a href="#" class="ui-dialog-titlebar-close ui-corner-all" role="button">

                    </a>
                </div>
                <div  class="ui-dialog-content" style="width: auto;height: auto;" scrolltop="0" scrollleft="0">
                    <h1 id="formHeader">Create Price List</h1>   
                    <form id="pricelistForm" class="single-column-form" style="width:90%">
                        <fieldset>
                       <!--<fieldset style="border:1px solid #A6C9E2">-->
<!--                            <legend style="color: #2E6E9E;font-size: 110%;font-weight: bold;margin-left: 15px;">
                                Price List Informations</legend>  -->
                            <div class="row single-column-row">
                                <div class="column single-column pricelist-col">
                                    <div class="field">
                                        <label for="typeOp" class="labeldiv">Pricelist Type</label>  
                                        <select name="typeOp" id ="typeOp" class="required">
                                            <option value="">Choose
                                            <option value="supplierpl">Supplier Rate Contract
                                            <option value="version">Version
                                        </select>
                                    </div>   
                                </div> 
      
                                <div id="versionCntnr" style="display:none">
                                    <div class="column single-column pricelist-col">
                                        <div class="field">
                                           <label for="supplierPriceListOpsupplierPriceListOp" class="labeldiv">Version Of:</label>  
                                           <select name="supplierPriceListOp" id ="supplierPriceListOp" class="required">
                                            <option value="">Choose
                                            <?=$supplierPriceListOptions ?>
                                        </select>
                                       </div>
                                    </div> 
                                    <div class="column single-column pricelist-col">
                                        <div class="field">
                                            <label for="validfrom" class="labeldiv">Valid From Date:</label>  
                                            <input id="validfrom" name ="validfrom" type="text" class="required dateValidate" style="width:12em;"/>
                                        </div>
                                    </div>
                                    <div class="column single-column pricelist-col">
                                        <div class="field">
                                            <label for="validto" class="labeldiv">Valid From Date:</label>  
                                            <input id="validto" name ="validto" type="text" class=" required dateValidate" style="width:12em;"/>
                                        </div>
                                    </div>
                                </div>
                                <div id="supplilerPricelistCntnr" style="display:none">
                                    <div class="column single-column pricelist-col">
                                        <div class="field">
                                            <label for="supplierOp" class="labeldiv">Supplier</label>  
                                            <select name="supplierOp" id ="supplierOp" class="required">
                                                <option value="">Choose
                                                <?=$supplierOptions ?>
                                            </select>

                                        </div>
                                    </div>
       
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
                 <div class="ui-dialog-buttonpane shopifine-ui-widget-content ui-helper-clearfix">
                    <div class="shopifine-ui-dialog-buttonset">
                        <button id="addBtn" type="button" class="shopifine-ui-button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false">
                            <span class="ui-button-text">Create Rate Contract</span>
                        </button>
                    </div> 
                </div>
            </div>
            <?php $this->load->view("common/message"); ?>
            <div class="table-grid" style="height:100%">
                <h1 id="table header">Pricelist Grid</h1>
                <table id="pricelist_grid"><tr><td/></tr></table> 
                <div id="pager"></div>
            </div>
            
        </div>
        <div id ="dialog-form-item" style="overflow:hidden">
<!--            <div id ="status-message-li" class="ui-corner-all">
                Note:You Can Define New Rules. These Rules Will Not Be ready To Use Unless The Sequence And Exclusivity Is Not Defined./n/
                Sequence And Rules Can Be Added In The "PriceList" Dialog.
            </div>-->
             
            <h1 id="formHeaderItem"></h1>   
            <form id="itemForm" class="single-column-form" style="width:100%; margin-left: 0;">
                <fieldset>
                    <div class="row single-column-row single-column-row-item" id="prodCntnr">
                        <div class="column single-column">
                            <div class="field">
                                <label for="productOptions" class="labeldiv">Products:</label>  
                                <select name="productOptions[]" id ="productOptions" class="chzn-select required initshow" multiple="multiple" style="width:200px;height:20px;"> 
                                        
                                        <?=$productOptions?> 
                                </select> 
                               <div class="valuediv valuediv-edit inithide" id="product_rule_edit" name ="product_rule_edit" style="display:none"></div>
                                
                            </div>
                        </div> 
                    </div>
                   <div id="allCBCntnr"class="row single-column-row single-column-row-item inithide" style="display:none">
                            <div class="column single-column">
                                <div class="field">
                                   <label for="allCB" class="labeldiv">Apply To All Products?:</label>  
                                   <input id="allCB" name="allCB" type="checkbox"/>
                                    <div id ="all-help" class="ui-corner-all help-message-left">
                                        (If  Selected This Rule Will Be Applied To All The Products for This Supplier)
                                    </div>
                                </div>
                            </div> 
                    </div>
                    <div id="priceCntnr" class="initshow">
                        <div class="row single-column-row single-column-row-item">
                            <div class="column single-column">
                                <div class="field">
                                    <label for="product_price" class="labeldiv">Price:</label>  
                                   <input id="product_price" id="product_price" type="text" class="required number "/>

                                </div>
                            </div> 
                        </div>
                        <div class="row single-column-row single-column-row-item">
                            <div class="column single-column">
                                <div class="field">
                                    <label for="ruleCB" class="labeldiv">Define Rules?:</label>  
                                   <input id="ruleCB" id="ruleCB" type="checkbox"/>

                                </div>
                            </div> 
                        </div>
                    </div>
                    <div id="ruleCntnr" style="display:none" class="inithide">
                        <div class="row single-column-row single-column-row-item">
                            <div class="column single-column">
                                <div class="field">
                                    <label for="qualifier" class="labeldiv">Applicable On:</label>  
                                    <select name="qualifier" id ="qualifier" class="required" > 
                                                    <option value="">Choose 
                                                    <option value="product">Product
                                                    <option value="quantity">Quantity
                                                    <option value="amount">Amount
                                    </select> 
                                </div>
                              </div> 
                        </div>
                        <div class="row single-column-row single-column-row-item">
                            <div class="column single-column">
                                <div class="field">
                                    <label for="precedence" class="labeldiv">Precedence:</label>  
                                    <input id="precedence" id="precedence" type="text" class=""/>
                                    <div id ="precedence-help" class="ui-corner-all help-message-left">
                                        (Please Specify The Precedence If Known.If The Precedence Already Exists Then Next Lower Precedence Will Be Assigned .If Not Specified The Lowest Precedence Will Be Set)
                                    </div>
                                </div>

                            </div> 
                        </div>
                        <div class="row single-column-row single-column-row-item">
                            <div class="column single-column">
                                <div class="field">
                                    <label for="discount_type" class="labeldiv">Discount Type:</label>  
                                    <select name="discount_type" id ="discount_type" class="required">
                                            <option value="">Choose 
                                            <option value="percentage">Percentage
                                            <option value="flat">Flat Amount
                                            <option value="free">Free Goods       
                                    </select> 
                                </div>
                            </div> 
                        </div>
                        <div class="row single-column-row single-column-row-item inithide" id="operatorCntnr" style="display:none">
                            <div class="column single-column">
                                <div class="field">
                                    <label for="operator" class="labeldiv">Operator:</label>  
                                    <select name="operator" id ="operator" class="required" > 
                                        <option value="">Choose 
                                            <option value="equal">Equal
                                            <option value="more">More Than
                                            <option value="less">Less Than
                                            <option value="between">Between
                                    </select> 
                                </div>
                            </div>  
                        </div>
                        <div id ="qualRangeCntnr" class="inithide" style="display:none">
                             <div class="row single-column-row single-column-row-item">
                                <div class="column single-column">
                                    <div class="field">
                                        <label for="qualifier_value_from" class="labeldiv" id="qualifier_value_point_lbl">Applicable Qty/Amount From:</label>  
                                        <input id="qualifier_value_from" id="qualifier_value_from" type="text" class="required"/>
                                    </div>

                                </div> 
                            </div>
                            <div class="row single-column-row single-column-row-item">
                                <div class="column single-column">
                                    <div class="field">
                                        <label for="qualifier_value_to" class="labeldiv" > Applicable Qty/Amount  Till:</label>  
                                        <input id="qualifier_value_to" id="qualifier_value_to" type="text" class="required"/>
                                    </div>

                                </div> 
                            </div>
                        </div>

                        <div class="row single-column-row single-column-row-item inithide" id="qualvalCntnr" style="display:none">
                            <div class="column single-column">
                                <div class="field">
                                    <label for="qualifier_value" class="labeldiv">Applicable Qty/Amount:</label>  
                                    <input id="qualifier_value" id="qualifier_value" type="text" class="required"/>
                                </div>
                            </div> 
                        </div>
                        <div class="row single-column-row single-column-row-item">
                            <div class="column single-column">
                                <div class="field">
                                    <label for="discount_value" class="labeldiv">Discount:</label>  
                                    <input id="discount_value" id="discount_value" type="text" class="required"/>
                                    <div id ="discount-help" class="ui-corner-all help-message-left">
                                        (Depends On The Discount Type. If "%" or "flat" Decimal. If "Free Goods" Only Digits)
                                    </div>
                                </div>

                            </div> 
                        </div>
                    </div>
                     <input id="ischangeditem" name="ischangeditem" type="hidden" value="0"/>
                </fieldset>
            </form>
        </div>
         
       
        <?php $this->load->view("partial/footer"); ?>  
        
</body>   
</html>
