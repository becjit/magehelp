function prepareRulesGrid(settingObj,pricelist_id,product_id,customadd,customedit){
    var url = 'index.php/procurement/populatePriceRules?pricelist_id='+pricelist_id+'&product_id='+product_id;
    var datatype = 'json';
    var grid_id = settingObj.grid_id;
    var pager = settingObj.pager;
    
    
    var multiselect = settingObj.multiselect;
    if (multiselect!=true){
        multiselect = false;
    }
    if (settingObj.islocal==true){
        url='clientArray';
        datatype = 'local';
    }
    
    jQuery("#"+grid_id).jqGrid({
        height: 'auto',
        url:url,
        datatype: datatype,
        colNames:[/*'Product ',*/'Rule Type','Inherited', 'Precedence', 'Qualifier','Condition', 'Discount Type','Value','Operator','Qual Val Frm','Qual Val To','Qual Val Pnt'],
        colModel:[
        //                                                    {name:'product_name',index:'product_name', width:55,editable:false},
        {
            name:'rule_type',
            index:'rule_type', 
            width:80
        },

        {
            name:'inherited',
            index:'inherited', 
            width:60
        },

        {
            name:'precedence',
            index:'precedence', 
            width:60, 
            align:"right"
        },

        {
            name:'qualifier',
            index:'qualifier', 
            width:60, 
            align:"right"
        },		

        {
            name:'condition',
            index:'condition', 
            width:180,
            align:"right", 
            sortable:false, 
            search:false,
            editable:false
        },

        {
            name:'discount_type',
            index:'discount_type', 
            width:70, 
            align:"right"
        },		

        {
            name:'discount_value',
            index:'discount_value', 
            width:40,
            align:"right", 
            sortable:false, 
            search:false,
            editable:false
        },

        {
            name:'operator',
            index:'operator', 
            hidden:true
        },

        {
            name:'qualifier_value_from',
            index:'qualifier_value_from', 
            hidden:true
        },

        {
            name:'qualifier_value_to',
            index:'qualifier_value_to', 
            hidden:true
        },

        {
            name:'qualifier_value_point',
            index:'qualifier_value_point', 
            hidden:true
        }
        ],      
        rowNum:5,
        rowList:[5,10,20],
        pager: '#'+pager,
        sortname: 'precedence',
        viewrecords: true,
        sortorder: "asc",
        multiselect: multiselect,
        caption:"Price Rules",
        //                hiddengrid:true,    
                
        jsonReader : {
            root:"rulesdata",
            page: "page",
            total: "total",
            records: "records",
            cell: "dprow",
            id: "id"
        }
    })
    if (customadd===true){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
            caption:"", 
            title:"Add Rules",
            buttonicon:"ui-icon-plus",
            id:"add_"+grid_id,
            onClickButton : function () { 
                //need to pass grid id for dynamic reload;
                var gridData ={
                    'grid_id':grid_id,
                    'action':'add_rule',
                    'pricelist_id':pricelist_id,
                    'url':'index.php/procurement/addSupplierRule'
                };
                $( "#dialog-form-item" ).data('grid_data',gridData).dialog( "open" );
            } 
        });
    }
    if (customedit===true){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
            caption:"", 
            title:"Edit Rules",
            buttonicon:"ui-icon-pencil",
            id:"edit_"+grid_id,
            onClickButton : function () { 
                //need to pass grid id for dynamic reload;
                var rowid = $("#"+grid_id).getGridParam('selrow');
                if (rowid !=null && rowid!=undefined){
                    var gridData ={
                        'grid_id':grid_id,
                        'action':'edit_rule',
                        'pricelist_id':pricelist_id,
                        'url':'index.php/procurement/addSupplierRule'
                    };
                    $( "#dialog-form-item" ).data('grid_data',gridData).dialog( "open" );
                }
                else {
                    $( "#modal-warning-exactly-one" ).dialog( "open" );
                }
            } 
        });
    }
       
       
}
// Quotes Grid
function prepareQuotesGrid(settingObj,postData,subgrid,editSettingObj,hiddenSettingObj,subGridSettings){

    var grid_id = settingObj.grid_id;
    var pager = settingObj.pager;
    var subgridMultiselect;
    var subGridCustomButtons;
    var multiselect = settingObj.multiselect;
    var hiddenObjSubgrid ;
    if (multiselect!=true){
        multiselect = false;
    }
    if (isEmpty(editSettingObj)){
        editSettingObj= {};
    }

    if (isEmpty(hiddenSettingObj)){
        hiddenSettingObj= {};
    }
    if (isEmpty(postData)){
        postData= {};
    }
    if (subgrid!=true){
        subgrid = false;
        subGridSettings={};
    }
    else {
        if (isEmpty(subGridSettings)){
            subGridSettings= {};
        }
        else {
            subGridCustomButtons = subGridSettings.customButtons;
            subgridMultiselect = subGridSettings.multiselect;
            if (subgridMultiselect!=true){
                subgridMultiselect = false;
            }
            if (!isEmpty(subGridSettings.hiddenObj)){
                hiddenObjSubgrid= subGridSettings.hiddenObj;
            }
        }
    }
    
    
    $("#"+grid_id).jqGrid({
        url:'index.php/procurement/populateQuotes',
        datatype: 'json',
        mtype: 'POST',
        postData:postData,
        colNames:['Reference','RFQ #','Supplier','Estimated Amt','Disc Type','Disc /Value','Pricelist Id','Disc w/Quote','Final Amt','Owner','Status','Raised By','Owner','Needed By','Approver','Supplier Id','Warehouse Id','Approver Id'],
        colModel :[ 
        {
            name:'reference', 
            index:'reference', 
            width:80, 
            align:'right',
            editable:editSettingObj.reference
            },

            {
            name:'rfq_reference', 
            index:'rfq_reference', 
            width:60, 
            align:'right',
            editable:editSettingObj.rfq_reference
            },

            {
            name:'supplier_name', 
            index:'supplier_name', 
            width:90, 
            align:'right',
            editable:editSettingObj.supplier_name,
            edittype:"select",
            editoptions:{
                dataUrl:"index.php/procurement/populateSuppliers",
                buildSelect:function(response)

                {
                    var select = "<select name=" + "mfrPkEdit" + "id =" +"mfrPkEdit" +">" +
                    "<option value=" + ">Select one..." + response + "</select>";
                    return select;

                }
            }
        },

        {
        name:'estimated_value', 
        index:'estimated_value', 
        width:90, 
        align:'right',
        editable:editSettingObj.estimated_value
        },

        {
        name:'discount_type', 
        index:'discount_type', 
        width:70, 
        align:'right',
        editable:editSettingObj.discount_type,
        hidden:hiddenSettingObj.discount_type,
        formatter:discountTypeFormat,
        unformat:discountTypeUnFormat
    },

    {
        name:'discount_value', 
        index:'discount_value', 
        width:70, 
        align:'right',
        editable:editSettingObj.discount_value,
        hidden:hiddenSettingObj.discount_value
        },

        {
        name:'pricelist_id', 
        index:'pricelist_id',  
        align:'right',
        editable:false,
        hidden:true
    },

    {
        name:'discount_amount', 
        index:'discount_amount', 
        width:80, 
        align:'right',
        editable:editSettingObj.discount_amount,
        hidden:hiddenSettingObj.discount_amount
        },

        {
        name:'final_total', 
        index:'final_total', 
        width:70, 
        align:'right',
        editable:editSettingObj.final_total,
        hidden:hiddenSettingObj.final_total
        },

        {
        name:'owner_id', 
        index:'owner_id', 
        hidden:true
    },

    {
        name:'status', 
        index:'status', 
        width:60, 
        align:'right',
        editable:editSettingObj.status,
        hidden:hiddenSettingObj.status
        },

        {
        name:'raised_by_name', 
        index:'raised_by_name',
        editable:false, 
        width:60, 
        align:'right'
    },

    {
        name:'owner_name', 
        index:'owner_name',
        editable:false, 
        width:60, 
        align:'right'
    },

    {
        name:'needed_by_date', 
        index:'needed_by_date',
        editable:false, 
        width:60, 
        sorttype:'date',
        align:'right'
    },
                

    {
        name:'approved_by', 
        index:'approved_by',
        width:60, 
        align:'right',
        hidden:hiddenSettingObj.approved_by
        },

        {
        name:'supplier_id', 
        index:'supplier_id',
        editable:false, 
        hidden:true
    },

    {
        name:'warehouse_id', 
        index:'warehouse_id',
        editable:false, 
        hidden:true
    },

    {
        name:'approver_id', 
        index:'approver_id',
        editable:false, 
        hidden:true
    }
    ],
    pager: '#'+pager,
    rowNum:10,
    rowList:[5,10,20],
    sortname: 'id',
    sortorder: 'desc',
    viewrecords: true,
    gridview: true,
    multiselect:multiselect,
    ignoreCase:true,
    rownumbers:true,
    height:'auto',
            
    caption: 'Quotations',

    jsonReader : {
        root:"quotedata",
        page: "page",
        total: "total",
        records: "records",
        cell: "dprow",
        id: "id"
    },
    editurl:'index.php/procurement/modifyQuote',
    subGrid:subgrid,
    subGridRowExpanded: function(subgrid_id, row_id) {
        var subgrid_table_id, pager_id;
        subgrid_table_id = subgrid_id+"_t";
                   
        pager_id = "p_"+subgrid_table_id;
        // we need to set #quoteId as we are reusing dialog-form-item
        $("#quoteSubgridId").val(row_id);
        $("#"+subgrid_id).html("<table id='"+subgrid_table_id+"' class='scroll'></table><div id='"+pager_id+"' class='scroll'></div>");
        var subgridSettingsObj ={
            grid_id:subgrid_table_id,
            pager:pager_id,
            addFooter:true,
            parent_grid:grid_id
           
        };
        prepareQuoteItemsGrid(subgridSettingsObj,{
            quoteId:row_id
        });
        $("#"+subgrid_table_id).navGrid("#"+pager_id,{
            edit:false,
            add:false,
            del:true,
            search:false
        },{},{},{},{},{});
        var buttons = {
            add:true,
            edit:true,
            chooser:true,
            data:{
                quote_id:row_id
            }
        };
        addCustomButtonsInQuoteItemGrid(subgridSettingsObj,buttons );
    $("#del_"+grid_id).insertAfter("#add_"+grid_id);
    $("#"+subgrid_table_id).jqGrid("setGridParam",{
        userDataOnFooter : true
    });
},
subGridRowColapsed: function(subgrid_id, row_id) {
    $("#quoteSubgridId").val("");
}
})
//        .navGrid("#pager",{edit:false,add:false,del:true,search:false},{},{},{caption: "Cancel",
//    msg: "Cancel Selected Quotations(s)?"},{});
        

}
function addCustomButtonsQuoteGrid(settingObj,buttons){
    var permission = isAllowedBulk([{
        element:'approve_quote_button',
        resource:'quote',
        permission:'approve'
    },

    {
        element:'mark_approver_quote_button',
        resource:'quote',
        permission:'approve'
    },

    {
        element:'po_direct_quote_button',
        resource:'quote',
        permission:'generatedirect'
    },

    {
        element:'manage_quote_button',
        resource:'quote',
        permission:'manage'
    },

    {
        element:'assign_quote_button',
        resource:'quote',
        permission:'assign'
    }]);
    
    var grid_id = settingObj.grid_id;
    var pager = settingObj.pager;
    if (buttons.add==true && permission.manage_quote_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
            caption:"", 
            title:"Create Quotation",
            buttonicon:"ui-icon-plus",
            id:"add_"+grid_id,
            onClickButton : function () { 
                var grid_data={
                    mode:'add'
                }
                $( "#dialog-form" ).data('grid_data',grid_data).dialog( "open" );

            } 
        });
    }
   
    if (buttons.edit==true  && permission.manage_quote_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
            caption:"", 
            title:"Edit Quotation",
            buttonicon:"ui-icon-pencil",
            id:"edit_"+grid_id,
            onClickButton : function () { 
                var selectedRows = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var noOfRows = selectedRows.length;
                var onlyOpen = false;
                if (noOfRows != 1){
                    $( "#modal-warning-exactly-one" ).dialog("open");
                }
                else {
                    var row_id = $("#"+grid_id).jqGrid('getGridParam', 'selrow');
                    var status = $("#"+grid_id).getCell(row_id,'status');
                    var owner_id = $("#"+grid_id).getCell(row_id,'owner_id');
                    
                    if (status=='open' || status=='draft'){
                        onlyOpen = true;
                    }
                    if (!onlyOpen){
                        $("#status_warning_text").text("Quotations With Status 'Open' or 'Draft' Can Only Be Edited");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                    else {
                        if (user_id == owner_id){
                            var grid_data={
                                quote_id:row_id,
                                mode:'edit'
                            }
                            $( "#dialog-form" ).data('grid_data',grid_data).dialog( "open" );

                        }
                        else {
                           
                            $("#status_warning_text").text("You Can Only Modify Quotes Owned By You");
                            $( "#modal-warning-general" ).dialog("open");
                        }
                        
                    }
                }
               
                
            } 
        });
    }
    if (buttons.del==true && permission.manage_quote_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager ,{
            caption:"", 
            title:"Cancel",
            buttonicon:"ui-icon-trash",
            id:"del_"+grid_id,
            onClickButton : function () { 
                var rowid = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var noOfRows = rowid.length;
                var onlyOpen= true;
                var onlySelf= true;
               
                if (noOfRows == 0){
                    $( "#modal-warning-none" ).dialog("open");
                }
                else{
                    
                    $.each(rowid, function(){
                        var status = $("#"+grid_id).getCell(this,'status');
                        if (status !='open' &&  status !='draft'){
                            onlyOpen = false;
                            return false;
                        }
                    });
                    if (!onlyOpen){
                        $("#status_warning_text").text("Quotes With Status 'Open' Or 'Draft' Can Only Be Cancelled");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                    else{
                        $.each(rowid, function(){
                         
                            if ($("#"+grid_id).getCell(this,'owner_id')!=user_id){
                                onlySelf = false;
                                return false;
                            }
                        });
                        if (!onlySelf){
                            $("#status_warning_text").text("You Can Only Cancel Quotes Owned By You");
                            $( "#modal-warning-general" ).dialog("open");
                        }
                        
                        else  {
                            var grid_data = {
                                grid_id:grid_id,
                                url:'index.php/procurement/modifyQuote'
                            }
                            $( "#delete-common-dialog" ).data('grid_data',grid_data).dialog("open") ;
                        }
                        
                    }
                } 
            } 
        });
    }
    
    if (buttons.submit_approve==true  && permission.manage_quote_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
            caption:"", 
            title:"Submit for Approval",
            buttonicon:"ui-icon-flag",
            id:"approve_"+grid_id,
            onClickButton : function () { 
                var selectedRows = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var noOfRows = selectedRows.length;
                var error = false;
                var onlyOpen = true;
                var onlySelf = true;
                if (noOfRows == 0){
                    $( "#modal-warning-none" ).dialog("open");
                }
                else{
                    $.each(selectedRows, function(){

                        if ($("#"+grid_id).getCell(this,'status')!='open'){
                            onlyOpen = false;
                            return false;
                        }
                    })
                    if (!onlyOpen){
                        $("#status_warning_text").text("Line Items With Status 'Open' Can Only Be Submitted For Approval");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                    else {
                        $.each(selectedRows, function(index,value){

                            var val = $("#"+grid_id).getCell(value,'estimated_value');
                            if (val ==0){
                                $("#refIds").text($("#"+grid_id).getCell(value,'reference')+" ")
                                error=true;
                            }
                        })
                        if (error){
                            $( "#dialog-modal" ).dialog("open");
                        }
                        else{
                            $.each(selectedRows, function(){
                         
                                if ($("#"+grid_id).getCell(this,'owner_id')!=user_id){
                                    onlySelf = false;
                                    return false;
                                }
                            });
                            if (!onlySelf){
                                $("#status_warning_text").text("You Can Only Cancel Quotes Owned By You");
                                $( "#modal-warning-general" ).dialog("open");
                            }
                            else{
                                $.ajax({
                                    method:"POST",
                                    url:"index.php/procurement/submitForApproval",
                                    data: {
                                        ids : selectedRows,
                                        entity:'quote'
                                    },
                                    success: function(){
                                        $("#"+grid_id).trigger("reloadGrid");
                                        emptyMessages()
                                        showSuccessMessage("Selected Quotations Are Submitted For Approval");
                                    }
                                });
                            }
                             
                        }
                    }
                }
            } 
        });
    }
    if (buttons.po_direct==true && permission.po_direct_quote_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
            caption:"", 
            title:"Generate Purchase Order Directly",
            buttonicon:"ui-icon-cart",
            id:"po_"+grid_id,
            onClickButton : function () { 

                var selectedRows = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var noOfRows = selectedRows.length;
                var error = false;
                var onlyOpen = true;
                var onlySelf = true;
                if (noOfRows == 0){
                    $( "#modal-warning-none" ).dialog("open");
                }
                else{
                   
                    $.each(selectedRows, function(){

                        if ($("#"+grid_id).getCell(this,'status')!='open'){
                            onlyOpen = false;
                            return false;
                        }
                    })
                    if (!onlyOpen){
                        $("#status_warning_text").text("Line Items With Status 'Open' Can Only Be Submitted For Purchase Order Generation");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                    else {
                        $.each(selectedRows, function(index,value){

                            var val = $("#"+grid_id).getCell(value,'estimated_value');
                            if (val ==0){
                                $("#refIds").text($("#"+grid_id).getCell(value,'reference')+" ")
                                error=true;
                            }
                        });
                        if (error){
                            $( "#dialog-modal" ).dialog("open");
                        }
                        else{
                            $.each(selectedRows, function(){
                         
                                if ($("#"+grid_id).getCell(this,'owner_id')!=user_id){
                                    onlySelf = false;
                                    return false;
                                }
                            });
                            if (!onlySelf){
                                $("#status_warning_text").text("You Can Only Cancel Quotes Owned By You");
                                $( "#modal-warning-general" ).dialog("open");
                            }
                            else{
                                var rowid = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                                $.ajax({
                                    method:"POST",
                                    url:"index.php/procurement/generatePOFromRFQ",
                                    data: {
                                        ids : rowid
                                    },
                                    success: function(){
                                        $("#"+grid_id).trigger("reloadGrid");
                                        showSuccessMessage("success");
                                    }
                                })
                            }
                            
                        } 
                    }
                    
                }                       
            } 
        });
    }
    if (buttons.reopen==true  && permission.manage_quote_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
            caption:"", 
            title:"Reopen",
            buttonicon:"ui-icon-folder-open",
            id:"reopen_"+grid_id,
            onClickButton : function () { 
                var rowid = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var selectedRows = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var noOfRows = selectedRows.length;
                var onlyRejected = true;
                var onlySelf = true;
                       
                if (noOfRows == 0){
                    $( "#modal-warning-none" ).dialog("open");
                }
                else{
                    $.each(selectedRows, function(){

                        if ($("#"+grid_id).getCell(this,'status')!='rejected'){
                            onlyRejected = false;
                            return false;
                        }
                    })
                    if (!onlyRejected){
                        $("#status_warning_text").text("Line Items With Status 'Rejected' Can Only Be Reopened");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                    else {
                        $.each(selectedRows, function(){
                         
                            if ($("#"+grid_id).getCell(this,'owner_id')!=user_id){
                                onlySelf = false;
                                return false;
                            }
                        });
                        if (!onlySelf){
                            $("#status_warning_text").text("You Can Only Cancel Quotes Owned By You");
                            $( "#modal-warning-general" ).dialog("open");
                        }
                        else{
                            $.ajax({
                                method:"POST",
                                url:"index.php/procurement/reopen",
                                data: {
                                    ids : rowid,
                                    entity:'quote'
                                },
                                success: function(){
                                    $("#"+grid_id).trigger("reloadGrid");
                                    showSuccessMessage("Reopened");
                                }
                            });
                        }
                               
                    }
                }
            } 
        });
    } 
    
    if (buttons.approve==true && permission.approve_quote_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
            caption:"", 
            title:"Approve And Generate Purchase Order",
            buttonicon:"ui-icon-check",
            id:"approve_"+grid_id,
            onClickButton : function () { 
                var selectedRows = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var row_id = $("#"+grid_id).jqGrid('getGridParam', 'selrow');
                var noOfRows = selectedRows.length;
                var onlyWFA= false;
                

                if (noOfRows == 0){
                    $( "#modal-warning-one" ).dialog("open");
                }
                else if (noOfRows == 1){
                    var selrow = $("#"+grid_id).jqGrid('getGridParam', 'selrow');  
                    var status = $("#"+grid_id).getCell(selrow,'status');
                    var approver_id = $("#"+grid_id).getCell(selrow,'approver_id');
                    if (status=='waitingforapproval' ){
                        onlyWFA = true;

                    }
                    if (!onlyWFA){
                        $("#status_warning_text").text("RFQs With Status 'Waiting For Approval ' Can Only Be Approved");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                    else{
                        
                        if (user_id == approver_id){
                            $( "#dialog-form" ).dialog( "open" );

                        }
                        else {
                            $("#status_warning_text").text("You Need to Be Approver For This RFQ To Approve.Please Mark Yourself As Approver For This RFQ");
                            $( "#modal-warning-general" ).dialog("open"); 
                        }
                        
                    }
                }
                else {
                    $( "#modal-warning-morethanone" ).dialog("open") ;
                }
            } 
        });
    }
    if (buttons.approve_bulk==true  && permission.approve_quote_button=="true"){
        //if (buttons.approve_bulk==true  && permission.approve_bulk_rfq_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
            caption:"", 
            title:"Approve And Generate Purchase Order In Bulk",
            buttonicon:"ui-icon-circle-check",
            id:"approve_bulk_"+grid_id,
            onClickButton : function () { 
                var rowid = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var selectedRows = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var noOfRows = selectedRows.length;
                var onlywfa= true;
                var onlySelf= true;
                if (noOfRows == 0){
                    $( "#modal-warning-none" ).dialog("open");
                }
                else{
                    $.each(selectedRows, function(){
                         
                        if ($("#"+grid_id).getCell(this,'status')!='waitingforapproval'){
                            onlywfa = false;
                            return false;
                        }
                    })
                    if (!onlywfa){
                        $("#status_warning_text").text("RFQs With Status 'Waiting For Approval ' Can Only Be Approved");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                    else {
                        $.each(rowid, function(){

                            if ($("#"+grid_id).getCell(this,'approver_id')!=user_id){
                                onlySelf = false;
                                return false;
                            }
                        });
                        if (!onlySelf){
                            $("#status_warning_text").text("You Need to Be Approver For *ALL* Selected RFQs .Please Mark Yourself As Approver For *ALL*  Selected RFQs");
                            $( "#modal-warning-general" ).dialog("open");
                        }
                        else {
                            $.ajax({
                                method:"POST",
                                url:"index.php/procurement/generatePOFromQuote",
                                data: {
                                    ids : rowid
                                },
                                success: function(){
                                    $("#"+grid_id).trigger("reloadGrid");
                                    showSuccessMessage("success");
                                }
                            });
                        }
                                
                    }
                }
                        
            } 
        });
    }
    
    if (buttons.comments == true /*&& permission.comments_button=="true"*/)  {
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
            caption:"", 
            title:"Show Comments",
            buttonicon:"ui-icon-script",
            id:"comments_"+grid_id,
            onClickButton : function () { 

                var rowid = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var noOfRows = rowid.length;
                //var onlyOpen = false;
                if (noOfRows != 1){
                    $( "#modal-warning-exactly-one" ).dialog("open");
                }
                else{
                    var quote_id = $("#"+grid_id).jqGrid('getGridParam', 'selrow');
                    $.ajax({
                        method:"POST",
                        url:"index.php/procurement/getCommentsForEntity",
                        data: {
                            entity:'quote',
                            id : quote_id
                        },
                        success: function(response){
                            var resObj = JSON.parse(response);
                            console.log(resObj);
                            $( "#comment-quote-dialog" ).data('grid_data',resObj).dialog("open") ;
                        }
                    });
                }                       
            } 
        });
    } 
    if (buttons.mark_approver==true  && permission.mark_approver_quote_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
            caption:"", 
            title:"Mark Yourself As Approver",
            buttonicon:"ui-icon-star",
            id:"mark_approver_"+grid_id,
            onClickButton : function () { 
                var rowid = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var selectedRows = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var noOfRows = selectedRows.length;
                var onlywfa= true;
                var onlyEmpty= true;
                if (noOfRows == 0){
                    $( "#modal-warning-none" ).dialog("open");
                }
                else{
                    $.each(selectedRows, function(){
                         
                        if ($("#"+grid_id).getCell(this,'status')!='waitingforapproval'){
                            onlywfa = false;
                            return false;
                        }
                    })
                    if (!onlywfa){
                        $("#status_warning_text").text("Quotess With Status 'Waiting For Approval ' Can Only Be Permitted For Marking");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                    else {
                        $.each(rowid, function(){

                            if (!isEmpty($("#"+grid_id).getCell(this,'approver_id'))){
                                onlyEmpty = false;
                                return false;
                            }
                        });
                        if (!onlyEmpty){
                            $("#status_warning_text").text("Selected Quotes(s) already has approver. Please Select Records Without Any Approver");
                            $( "#modal-warning-general" ).dialog("open");
                        }
                        else {
                            $.ajax({
                                method:"POST",
                                url:"index.php/procurement/assignEntityToUser",
                                data: {
                                    ids : rowid,
                                    entity:'quote',
                                    user_id:user_id,
                                    role:'approver'
                                },
                                success: function(){
                                    $("#"+grid_id).trigger("reloadGrid");
                                    emptyMessages();
                                    showSuccessMessage("success");
                                }
                            });
                        }
                                
                    }
                }
                        
            } 
        });
    }
    if (buttons.assign==true && permission.assign_quote_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
            caption:"", 
            title:"Assign",
            buttonicon:"ui-icon-transferthick-e-w",
            id:"assign_"+grid_id,
            onClickButton : function () { 
                var rowid = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var noOfRows = rowid.length;
                var onlyOpen = false;
                if (noOfRows == 0){
                    $( "#modal-warning-none" ).dialog("open");
                }
                else{
                    var selrow = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');  
                    var approver_name = $("#"+grid_id).getCell(selrow,'approved_by');
                    var owner_name = $("#"+grid_id).getCell(selrow,'owner_name');
                    console.log(owner_name);
                    if (isEmpty(approver_name)){
                        approver_name ='None';
                    }
                    var grid_data = {
                        entity:'quote',
                        current_owner:owner_name,
                        current_approver:approver_name,
                        row_id:selrow,
                        grid_id:grid_id
                    };
                    $( "#assignment-common-dialog" ).data('grid_data',grid_data).dialog( "open" );
                }
                
            } 
        });
    }
    if (buttons.load_owner_all==true){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
            caption:"", 
            title:"Reload w/ all User Records",
            buttonicon:"ui-icon-arrow-4",
            id:"reload_user_"+grid_id,
            onClickButton : function () { 
                var postData = $("#"+grid_id).getGridParam('postData');
                postData.owner_id = 'all';
                $("#"+grid_id).setGridParam('postData',postData);
                $("#"+grid_id).trigger("reloadGrid");
            } 
        });
    }
    
    if (buttons.load_approver_all==true){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
            caption:"", 
            title:"Reload w/ all/No Approvers ",
            buttonicon:"ui-icon-arrow-4",
            id:"reload_approver_"+grid_id,
            onClickButton : function () { 
                var postData = $("#"+grid_id).getGridParam('postData');
                postData.approved_by = 'all';
                $("#"+grid_id).setGridParam('postData',postData);
                $("#"+grid_id).trigger("reloadGrid");
            } 
        });
    }
    if (buttons.assign==true && permission.assign_quote_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
            caption:"", 
            title:"Assign",
            buttonicon:"ui-icon-transferthick-e-w",
            id:"assign_"+grid_id,
            onClickButton : function () { 
                var rowid = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var noOfRows = rowid.length;
                
                if (noOfRows == 0){
                    $( "#modal-warning-none" ).dialog("open");
                }
                else{
                    var selrow = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');  
                    var approver_name = $("#"+grid_id).getCell(selrow,'approved_by');
                   
                    if (isEmpty(approver_name)){
                        approver_name ='None';
                    }
                    var grid_data = {
                        entity:'receipt_item',
                        current_approver:approver_name,
                        row_id:selrow,
                        grid_id:grid_id
                    };
                    $( "#assignment-common-dialog" ).data('grid_data',grid_data).dialog( "open" );
                }
                
            } 
        });
    }
}

function prepareQuoteItemsGrid(settingsObj,postData){
    var grid_id = settingsObj.grid_id;
    var pager = settingsObj.pager;
    var defaultNavGrid = false;
    var footerrow ;
    if (settingsObj.addFooter==true) {
        footerrow = settingsObj.addFooter;
    }
    else {
        footerrow = false;
    }
    if (isEmpty(postData)){
        postData ={};
    }
    $("#"+grid_id).jqGrid({
        url:'index.php/procurement/populateQuoteItems',
        datatype: 'json',
        mtype: 'POST',
        postData:postData,
        colNames:['Product','Quantity','Free Qty','Base Unit Price','Unit Price Discount','Final Unit Price','w/o Discount','Discount','Total','Need By Date','Notes','Product Id','Price Rule Type','Price Rule Id','Discount Value'],
        colModel :[ 
        {
            name:'name', 
            index:'name',
            editable:false, 
            width:80, 
            align:'right'
        },

        {
            name:'quoted_quantity', 
            index:'quoted_quantity', 
            editable:false,
            width:30, 
            align:'right'
        },

        {
            name:'free_items', 
            index:'free_items', 
            editable:false,
            width:30, 
            align:'right'
        },

        {
            name:'expected_price', 
            index:'expected_price', 
            editable:false,
            width:30, 
            align:'right'
        },

        {
            name:'unit_price_discount', 
            index:'unit_price_discount', 
            editable:false,
            width:30, 
            align:'right',
            hidden:true
        },

        {
            name:'final_unit_price', 
            index:'final_unit_price', 
            editable:false,
            width:30, 
            align:'right'
        },

        {
            name:'total_before_discount', 
            index:'total_before_discount', 
            editable:false,
            width:40, 
            align:'right'
        },

        {
            name:'total_discount', 
            index:'total_discount', 
            editable:false,
            width:30, 
            align:'right'
        },

        {
            name:'final_total', 
            index:'final_total', 
            editable:false,
            width:30, 
            align:'right'
        },

        {
            name:'needed_by_date',
            index:'needed_by_date',
            editable:false, 
            width:60, 
            align:'right',
            hidden:true
        },

        {
            name:'comments', 
            index:'comments',
            editable:true, 
            width:180, 
            align:'right',
            hidden:true
        },

        {
            name:'product_id', 
            index:'product_id',
            editable:false,
            hidden:true
        },

        {
            name:'pricerule_type', 
            index:'pricerule_type',
            editable:false,
            hidden:true
        },

        {
            name:'pricelist_id', 
            index:'pricelist_id',
            editable:false,
            hidden:true
        },

        {
            name:'discount_value', 
            index:'discount_value',
            editable:false,
            hidden:true
        }
        ////,
        //{name:'barcode', index:'barcode',editable:true, width:180, align:'right'}

        ],
        pager: '#'+pager,
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
        caption: 'Items For This Quote',
        footerrow : footerrow,

        jsonReader : {
            root:"quoteitemdata",
            page: "page",
            total: "total",
            records: "records",
            cell: "dprow",
            id: "id"
        }

    });


}
function addCustomButtonsInQuoteItemGrid(settingObj,buttons){
    var permission = isAllowedBulk([
    {
        element:'manage_quote_button',
        resource:'quote',
        permission:'manage'
    }]);
    var grid_id = settingObj.grid_id;
    var pager = settingObj.pager;
    var parent_grid = settingObj.parent_grid;
    var quote_id = buttons.data.quote_id;
    var quote_owner_id =$("#"+parent_grid).getCell(quote_id,'owner_id');
    var status =$("#"+parent_grid).getCell(quote_id,'status');
    
    if (buttons.add==true && permission.manage_quote_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
            caption:"", 
            title:"Add Line Items",
            buttonicon:"ui-icon-plus",
            id:"add_"+grid_id,
            onClickButton : function () { 
                //need to pass grid id for dynamic reload;
                
                var onlyOpen = false;
                if (status=='open' || status=='draft'){
                    onlyOpen = true;

                }

                if (!onlyOpen){
                    $("#status_warning_text").text("Quotations With Status 'Open' or 'Draft' Can Only Be Edited");
                    $( "#modal-warning-general" ).dialog("open");
                }
                else{
                    if (user_id == quote_owner_id){
                       var gridData ={
                            'grid_id':grid_id,
                            'quote_id':buttons.data.quote_id
                            };
                        //$( "#dialog-form-item" ).dialog('option', 'title',
                        $( "#dialog-form-item" ).data('grid_data',gridData).dialog( "open" );
                    
                       
                    }
                    else{
                        $("#status_warning_text").text("You Can Only Modify RFQs Owned By You");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                }     
            } 
        });
        
    }
    if (buttons.edit==true && permission.manage_quote_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
            caption:"", 
            title:"Edit Line Items",
            buttonicon:"ui-icon-pencil",
            id:"edit_"+grid_id,
            onClickButton : function () { 
                //need to pass grid id for dynamic reload;
                var rowid = $("#"+grid_id).jqGrid('getGridParam', 'selrow');
                
                if (rowid !=null && rowid!=undefined){
                    
                    var onlyOpen = false;
                    if (status=='open' || status=='draft'){
                        onlyOpen = true;

                    }

                    if (!onlyOpen){
                        $("#status_warning_text").text("Quotations With Status 'Open' or 'Draft' Can Only Be Edited");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                    else{
                        if (user_id == quote_owner_id){
                            var gridData ={
                                'grid_id':grid_id,
                                'quote_id':quote_id,
                                'line_id':rowid,
                                    action:'edit'
                                };
                            $( "#dialog-form-item" ).data('grid_data',gridData).dialog( "open" );
                        }
                        else{
                            $("#status_warning_text").text("You Can Only Modify Quotes Owned By You");
                            $( "#modal-warning-general" ).dialog("open");
                        }
                    }
                    
                    
                }
                else{
                    $( "#modal-warning-exactly-one" ).dialog('open');
                }

            } 
        });
    }
    
    if (buttons.chooser==true){
        $("#"+grid_id).jqGrid ('navButtonAdd', "#"+pager,{
            caption: "", 
            buttonicon: "ui-icon-calculator",
            title: "Choose Columns",
            onClickButton: function() {
                $("#"+grid_id).jqGrid('columnChooser');
            }
        }); 
    }
    
                
}
// End Quotes Grid
// Order Grid
function prepareOrdersGrid(settingObj,postData,subgrid,editSettingObj,hiddenSettingObj,subGridSettings){

    var grid_id = settingObj.grid_id;
    var pager = settingObj.pager;
    var subgridMultiselect;
    var subGridCustomButtons;
    var multiselect = settingObj.multiselect;
    var hiddenObjSubgrid ;
    var width = '680';
    if (!isEmpty(settingObj.width)){
        width = settingObj.width;
    }
    if (multiselect!=true){
        multiselect = false;
    }
    if (isEmpty(editSettingObj)){
        editSettingObj= {};
    }

    if (isEmpty(hiddenSettingObj)){
        hiddenSettingObj= {};
    }
    if (isEmpty(postData)){
        postData= {};
    }
    if (subgrid!=true){
        subgrid = false;
        subGridSettings={};
    }
    else {
        if (isEmpty(subGridSettings)){
            subGridSettings= {};
        }
        else {
            subGridCustomButtons = subGridSettings.customButtons;
            subgridMultiselect = subGridSettings.multiselect;
            if (subgridMultiselect!=true){
                subgridMultiselect = false;
            }
            if (!isEmpty(subGridSettings.hiddenObj)){
                hiddenObjSubgrid= subGridSettings.hiddenObj;
            }
        }
    }
    
    
    $("#"+grid_id).jqGrid({
        url:'index.php/procurement/populatePOToReceive',
        datatype: 'json',
        mtype: 'POST',
        postData:postData,
        colNames:['Reference'
        ,'Quote Ref','Supplier','OrderAmt',
        'Owner Id','Status','Quote Raised/','Order Generated/',
        'Owner','Needed By','Receiver','Approver','Supplier Id','Warehouse Id','Receivero Id'],
        colModel :[ 
        //               
        {
            name:'reference', 
            index:'reference', 
            width:80, 
            align:'right',
            editable:editSettingObj.reference
            },

            {
            name:'quote_reference', 
            index:'quote_reference', 
            width:70, 
            align:'right',
            editable:editSettingObj.quote_reference
            },

            {
            name:'supplier_name', 
            index:'supplier_name', 
            width:140, 
            align:'right',
            editable:false
        },

        {
            name:'estimated_value', 
            index:'estimated_value', 
            width:90, 
            align:'right',
            editable:editSettingObj.estimated_value
            },
                

            {
            name:'owner_id', 
            index:'owner_id', 
            hidden:true
        },

        {
            name:'status', 
            index:'status', 
            width:60, 
            align:'right',
            editable:editSettingObj.status,
            hidden:hiddenSettingObj.status
            },

            {
            name:'raised_by_name', 
            index:'raised_by_name',
            editable:false, 
            width:90, 
            align:'right'
        },

        {
            name:'generated_by_name', 
            index:'generated_by_name',
            editable:false, 
            width:90, 
            align:'right'
        },
                

        {
            name:'owner_name', 
            index:'owner_name',
            editable:false, 
            width:80, 
            align:'right'
        },

        {
            name:'needed_by_date', 
            index:'needed_by_date',
            editable:false, 
            width:90, 
            sorttype:'date'
        },

        {
            name:'received_by', 
            index:'received_by',
            editable:false, 
            width:60, 
            sorttype:'date',
            align:'right'
        },

        {
            name:'approved_by', 
            index:'approved_by',
            width:80, 
            align:'right',
            hidden:hiddenSettingObj.approved_by
            },

            {
            name:'supplier_id', 
            index:'supplier_id',
            editable:false, 
            hidden:true
        },

        {
            name:'warehouse_id', 
            index:'warehouse_id',
            editable:false, 
            hidden:true
        },

        {
            name:'receiver_id', 
            index:'receiver_id',
            editable:false, 
            hidden:true
        }
        ],
        pager: '#'+pager,
        rowNum:10,
        rowList:[5,10,20],
        sortname: 'id',
        sortorder: 'desc',
        viewrecords: true,
        gridview: true,
        multiselect:multiselect,
        ignoreCase:true,
        rownumbers:true,
        height:'auto',
        //            width:width,
        caption: 'Purchase Orders',

        jsonReader : {
            root:"orderdata",
            page: "page",
            total: "total",
            records: "records",
            cell: "dprow",
            id: "id"
        },
            
        subGrid:subgrid,
        subGridRowExpanded: function(subgrid_id, row_id) {
            var subgrid_table_id, pager_id;
            subgrid_table_id = subgrid_id+"_t";
                            
            pager_id = "p_"+subgrid_table_id;
                            
            $("#"+subgrid_id).html("<table id='"+subgrid_table_id+"' class='scroll'></table><div id='"+pager_id+"' class='scroll'></div>");
            var settingsObj ={
                grid_id:subgrid_table_id,
                pager:pager_id
            };
                            
            prepareOrderItemsGrid(settingsObj,{
                orderId:row_id
            },false,{},{
                needed_by_date:true,
                returned_value:true
            });
            $("#"+subgrid_table_id).navGrid("#"+pager_id,{
                edit:false,
                add:false,
                del:false,
                search:false,
                view:true
            },{},{},{},{},{});
        }
    })

        

}
function addCustomButtonsOrderGrid(settingObj,buttons){
    var permission = isAllowedBulk([
    {
        element:'mark_receiver_order_button',
        resource:'order',
        permission:'mark'
    },

    {
        element:'assign_order_button',
        resource:'order',
        permission:'assign'
    },

    {
        element:'manage_order_button',
        resource:'order',
        permission:'manage'
    },
    ]);
    
    var grid_id = settingObj.grid_id;
    var pager = settingObj.pager;
    
   
    if (buttons.match==true  && permission.manage_order_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
            caption:"", 
            title:"Match With Received Goods",
            buttonicon:"ui-icon-newwin",
            id:"match_"+grid_id,
            onClickButton : function () { 
                var selectedRows = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var noOfRows = selectedRows.length;
                var onlyOpen = false;
                if (noOfRows != 1){
                    $( "#modal-warning-exactly-one" ).dialog("open");
                }
                else {
                    var row_id = $("#"+grid_id).jqGrid('getGridParam', 'selrow');
                   
                    var status = $("#"+grid_id).getCell(row_id,'status');
                    var receiver_id = $("#"+grid_id).getCell(row_id,'receiver_id');
                    if (status=='open' || status=='receiving' || status=='received'){
                        onlyOpen = true;
                    }
                    if (!onlyOpen){
                        $("#status_warning_text").text("Order With Status 'Open'/'Receiving'/'Received' Can Only Be Edited");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                    else {
                        
                        if (user_id == receiver_id){
                            var grid_data={
                                quote_id:row_id,
                                mode:'edit'
                            }
                            $( "#dialog-form" ).dialog( "open" );

                        }
                        else {
                           
                            $("#status_warning_text").text("You Must Be A Receiver For This Order To Receive Goods. Please Mark Yourself As Receiver Or Request For Same");
                            $( "#modal-warning-general" ).dialog("open");
                        }
                        
                    }
                }
               
                
            } 
        });
    }
    if (buttons.ready==true && permission.manage_order_button=="true"){
        //$('#del_'+grid_id).show();
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager ,{
            caption:"", 
            title:"Ready For Invoice",
            buttonicon:"ui-icon-tag",
            id:"ready_"+grid_id,
            onClickButton : function () { 
                var rowid = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var noOfRows = rowid.length;
                var onlyReceived= true;
                var onlySelf= true;
               
                if (noOfRows == 0){
                    $( "#modal-warning-none" ).dialog("open");
                }
                else{
                    
                    $.each(rowid, function(){
                        var status = $("#"+grid_id).getCell(this,'status');
                        console.log(this + " " + status)
                        if (status !='received'){
                            console.log("inside " + this + " " + status)
                            onlyReceived = false;
                            return false;
                        }
                    });
                    if (!onlyReceived){
                        
                        $( "#modal-warning-status" ).dialog("open");
                    }
                    else{
                        $.each(rowid, function(){
                            // we can also check withb receiver but leacving with owner as ideally at this stage the  receiver should be the owner.
                            // this will help during QA
                            if ($("#"+grid_id).getCell(this,'owner_id')!=user_id){
                                onlySelf = false;
                                return false;
                            }
                        });
                        if (!onlySelf){
                            $("#status_warning_text").text("You Can Only Mark  Orders Owned By You Ready For Invoice");
                            $( "#modal-warning-general" ).dialog("open");
                        }
                        
                        else  {
                            $( "#dialog-confirm" ).dialog( "open" );
                        }
                        
                    }
                } 
            } 
        });
    }
    
    
    
    if (buttons.comments == true /*&& permission.comments_button=="true"*/)  {
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
            caption:"", 
            title:"Show Comments",
            buttonicon:"ui-icon-script",
            id:"comments_"+grid_id,
            onClickButton : function () { 

                var rowid = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var noOfRows = rowid.length;
                //var onlyOpen = false;
                if (noOfRows != 1){
                    $( "#modal-warning-exactly-one" ).dialog("open");
                }
                else{
                    var order_id = $("#"+grid_id).jqGrid('getGridParam', 'selrow');
                    $.ajax({
                        method:"POST",
                        url:"index.php/procurement/getCommentsForEntity",
                        data: {
                            entity:'order',
                            id : order_id
                        },
                        success: function(response){
                            var resObj = JSON.parse(response);
                            resObj.entity = 'order';
                            console.log(resObj);
                            $( "#comment-quote-dialog" ).data('grid_data',resObj).dialog("open") ;
                        //                                $("#"+grid_id).trigger("reloadGrid");
                        //                                showSuccessMessage("Selected RFQ(s) Succesfully Converted To Quote(s)");
                        }
                    });
                //$( "#comment-quote-dialog" ).dialog("open") ;
                }                       
            } 
        });
    } 
    if (buttons.mark_receiver==true  && permission.mark_receiver_order_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
            caption:"", 
            title:"Mark Yourself As Receiver",
            buttonicon:"ui-icon-star",
            id:"mark_receiver_"+grid_id,
            onClickButton : function () { 
                var rowid = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var selectedRows = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var noOfRows = selectedRows.length;
                var onlyOpen= true;
                var onlyEmpty= true;
                if (noOfRows == 0){
                    $( "#modal-warning-none" ).dialog("open");
                }
                else{
                    $.each(selectedRows, function(){
                         
                        if ($("#"+grid_id).getCell(this,'status')!='open'){
                            onlyOpen = false;
                            return false;
                        }
                    })
                    if (!onlyOpen){
                        $("#status_warning_text").text("'Open ' Orders Can Only Be Permitted For Marking");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                    else {
                        $.each(rowid, function(){

                            if (!isEmpty($("#"+grid_id).getCell(this,'receiver_id'))){
                                onlyEmpty = false;
                                return false;
                            }
                        });
                        if (!onlyEmpty){
                            $("#status_warning_text").text("Some/All Selected Order(s) already have receiver. Please Select Records Without Any Receiver");
                            $( "#modal-warning-general" ).dialog("open");
                        }
                        else {
                            $.ajax({
                                method:"POST",
                                url:"index.php/procurement/assignEntityToUser",
                                data: {
                                    ids : rowid,
                                    entity:'order',
                                    user_id:user_id,
                                    role:'receiver'
                                },
                                success: function(){
                                    $("#"+grid_id).trigger("reloadGrid");
                                    emptyMessages();
                                    showSuccessMessage("success");
                                }
                            });
                        }
                                
                    }
                }
                        
            } 
        });
    }
    if (buttons.assign==true && permission.assign_order_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
            caption:"", 
            title:"Assign",
            buttonicon:"ui-icon-transferthick-e-w",
            id:"assign_"+grid_id,
            onClickButton : function () { 
                var rowid = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var noOfRows = rowid.length;
                var onlyOpen = true;
                if (noOfRows == 0){
                    $( "#modal-warning-none" ).dialog("open");
                }
                else{
                    var selrow = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');  
                    var receiver_name = $("#"+grid_id).getCell(selrow,'received_by');
                    var owner_name = $("#"+grid_id).getCell(selrow,'owner_name');
                    $.each(selrow, function(){
                         
                        if ($("#"+grid_id).getCell(this,'status')!='open' && $("#"+grid_id).getCell(this,'status')!='receiving' && $("#"+grid_id).getCell(this,'status')!='received'){
                            onlyOpen = false;
                            return false;
                        }
                    });
                    if (!onlyOpen){
                        $("#status_warning_text").text(" Orders With 'Open'/'Receiving'/'Received' Can Only Be Permitted For Assignment");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                    else {
                        console.log(owner_name);
                        if (isEmpty(receiver_name)){
                            receiver_name ='None';
                        }
                        var grid_data = {
                            entity:'order',
                            current_owner:owner_name,
                            current_receiver:receiver_name,
                            row_id:selrow,
                            grid_id:grid_id
                        };
                        $( "#assignment-common-dialog" ).data('grid_data',grid_data).dialog( "open" );
                    }
                    
                }
                
            } 
        });
    }
    
    if (buttons.load_status_all==true){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
            caption:"", 
            title:"Reload w/ all Status For This Owner",
            buttonicon:"ui-icon-arrowrefresh-1-s",
            id:"reload_status_"+grid_id,
            onClickButton : function () { 
                var postData = $("#"+grid_id).getGridParam('postData');
                postData._status = 'all';
                $("#"+grid_id).setGridParam('postData',postData);
                $("#"+grid_id).trigger("reloadGrid");
            } 
        });
    }
    if (buttons.load_owner_all==true){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
            caption:"", 
            title:"Reload w/ all User Records",
            buttonicon:"ui-icon-arrow-4",
            id:"reload_user_"+grid_id,
            onClickButton : function () { 
                var postData = $("#"+grid_id).getGridParam('postData');
                postData.owner_id = 'all';
                $("#"+grid_id).setGridParam('postData',postData);
                $("#"+grid_id).trigger("reloadGrid");
            } 
        });
    }
    
//    if (buttons.load_approver_all==true){
//        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
//        caption:"", 
//        title:"Reload w/ all/No Approvers ",
//        buttonicon:"ui-icon-arrow-4",
//        id:"reload_approver_"+grid_id,
//        onClickButton : function () { 
//            var postData = $("#"+grid_id).getGridParam('postData');
//            postData.approved_by = 'all';
//            $("#"+grid_id).setGridParam('postData',postData);
//            $("#"+grid_id).trigger("reloadGrid");
//         } 
//     });
//    }
}
// Start Order Item
function prepareOrderItemsGrid (settingObj,postData,subgrid,editSettingObj,hiddenSettingObj,subGridSettings){
    var grid_id = settingObj.grid_id;
    var pager = settingObj.pager;
    var subgridMultiselect;
    var subGridCustomButtons;
    var multiselect = settingObj.multiselect;
    if (editSettingObj=="" || editSettingObj==null || editSettingObj==undefined){
        editSettingObj= {};
    }

    if (hiddenSettingObj=="" || hiddenSettingObj==null || hiddenSettingObj==undefined){
        hiddenSettingObj= {};
    }
    if (postData=="" || postData==null || postData==undefined){
        postData= {};
    }
    if (subgrid!=true){
        subgrid = false;
        subGridSettings={};
    }
    else {
        if (isEmpty(subGridSettings)){
            subGridSettings={};
        }
        else {
            subGridCustomButtons = subGridSettings.customButtons;
            subgridMultiselect = subGridSettings.multiselect;
            if (subgridMultiselect!=true){
                subgridMultiselect = false;
            }
        }
    }
    $("#"+grid_id).jqGrid({
        url:'index.php/procurement/populateOrderItems',
        datatype: 'json',
        mtype: 'POST',
        postData:postData,
        colNames:['Product id','Product','Quantity','Rcvd Qty','Rtrnd Qty','Not Delivered','PP Qty','Need By Date','Price','Ordered Amt','Rcvd Amt','Rtrnd Amt','PP Amt','Notes'],
        colModel :[ 
        {
            name:'product_id', 
            index:'product_id', 
            hidden:true
        },

        {
            name:'name', 
            index:'name', 
            width:160, 
            align:'right'
        },

        {
            name:'quoted_quantity', 
            index:'quoted_quantity', 
            editable:false,
            width:50, 
            align:'right'
        },

        {
            name:'received_quantity', 
            index:'received_quantity', 
            editable:false,
            width:50, 
            align:'right'
        },

        {
            name:'returned_quantity', 
            index:'returned_quantity', 
            editable:false,
            width:50, 
            align:'right'
        },

        {
            name:'cnbd_quantity', 
            index:'cnbd_quantity', 
            editable:false,
            width:50, 
            align:'right'
        },

        {
            name:'pp_quantity', 
            index:'pp_quantity', 
            editable:false,
            width:50, 
            align:'right'
        },

        {
            name:'needed_by_date',
            index:'needed_by_date',
            editable:false, 
            width:80, 
            align:'right',
            hidden:hiddenSettingObj.needed_by_date
            },

            {
            name:'expected_price',
            index:'expected_price',
            editable:false, 
            width:60, 
            align:'right',
            hidden:hiddenSettingObj.expected_price
            },

            {
            name:'estimated_value', 
            index:'estimated_value',
            editable:false, 
            width:60, 
            align:'right'
        },

        {
            name:'received_value', 
            index:'received_value',
            editable:false, 
            width:60, 
            align:'right'
        },

        {
            name:'returned_value', 
            index:'returned_value',
            editable:false, 
            width:60, 
            align:'right'
        },

        {
            name:'pp_value', 
            index:'pp_value',
            editable:false, 
            width:60, 
            align:'right'
        },

        {
            name:'comments', 
            index:'comments',
            editable:true, 
            width:180, 
            align:'right',
            hidden:hiddenSettingObj.comments
            }

        ],
        pager: '#'+pager,
        rowNum:10,
        rowList:[5,10,20],
        sortname: 'id',
        sortorder: 'desc',
        viewrecords: true,
        gridview: true,
        ignoreCase:true,
        rownumbers:true,
        multiselect:multiselect,
        height:'auto',
        width:'60%',
        caption: 'Order Line Items',

        jsonReader : {
            root:"orderitemdata",
            page: "page",
            total: "total",
            records: "records",
            cell: "dprow",
            id: "id"
        },
        //receipt Item subgrid
        subGrid:subgrid,
        subGridRowExpanded: function(subgrid_id, row_id) {

            var subgrid_table_id, pager_id;
            subgrid_table_id = subgrid_id+"_t";

            pager_id = "p_"+subgrid_table_id;

            $("#"+subgrid_id).html("<table id='"+subgrid_table_id+"' class='scroll'></table><div id='"+pager_id+"' class='scroll'></div>");
            var hiddenObj = {
                line_reference:true,
                pp_quantity:true,
                pp_value:true,
                receiving_notes:true,
                returned_notes:true
            };
            var settingObj = {
                grid_id:subgrid_table_id,
                pager:pager_id,
                multiselect:subgridMultiselect
            };
            prepareReceiptItemsGrid(settingObj,{
                oper:'orderline',
                orderLineId:row_id
            },false,{},hiddenObj);
            $("#"+subgrid_table_id).jqGrid('navGrid',"#"+pager_id,{
                edit:false,
                add:false,
                del:false,
                search:false,
                view:true
            });
            addCustomButtonsInReceiptItemGrid(subgrid_table_id, pager_id,grid_id,subGridCustomButtons,row_id);
                 
        //end sub grid navigator custom buttons
        }

    })
}
function addCustomButtonsToOrderItemGrid(grid_id,pager,buttons,owner_id,status){
    var permission = isAllowedBulk([
        
    {
            element:'manage_order_button',
            resource:'order',
            permission:'manage'
        }
        ]);
    if (isEmpty(buttons))   {
        buttons ={};
    } 
    if (buttons.receive==true  && permission.manage_order_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
            caption:"", 
            title:"Receive Items",
            buttonicon:"ui-icon-newwin",
            id:"receive_"+grid_id,
            onClickButton : function () { 
                //need to pass grid id for dynamic reload;
                var noOfRows = $("#"+grid_id).getGridParam("selarrrow").length;
                if (noOfRows == 1){
                    
                    var onlyOpen =false;
                    if (status=='open' || status=='receiving' || status=='received'){
                        onlyOpen = true;
                    }
                    if (!onlyOpen){
                        $("#status_warning_text").text("Order With Status 'Open'/'Receiving'/'Received' Can Only Be Edited");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                    else{
                        if (user_id == owner_id){
                            var gridData ={
                                'grid_id':grid_id,
                                'order_line_id':$("#"+grid_id).getGridParam("selrow"),
                                'action':'add',
                                'oper':'receive',
                                'url':"index.php/procurement/receiveItems"
                            }

                            $( "#dialog-form-item" ).data('grid_data',gridData).dialog( "open" );
                            $("#receiptOp").combobox();


                        }
                        else {

                            $("#status_warning_text").text("You Can Only Modify Quotes Owned By You");
                            $( "#modal-warning-general" ).dialog("open");
                        }
                    }
                }
                else {
                    $( "#modal-warning-one" ).dialog("open") ;
                }

            } 
        });
    }
    if (buttons.return_item==true  && permission.manage_order_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
            caption:"", 
            title:"Return Items",
            buttonicon:"ui-icon-arrowreturnthick-1-w",
            id:"return_"+grid_id,
            onClickButton : function () { 
                //need to pass grid id for dynamic reload;
                var noOfRows = $("#"+grid_id).getGridParam("selarrrow").length;
                if (noOfRows == 1){
                    var onlyOpen =false;
                    if (status=='open' || status=='receiving' || status=='received'){
                        onlyOpen = true;
                    }
                    if (!onlyOpen){
                        $("#status_warning_text").text("Order With Status 'Open'/'Receiving'/'Received' Can Only Be Edited");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                    else{
                        if (user_id == owner_id){
                            var gridData ={
                                'grid_id':grid_id,
                                'order_line_id':$("#"+grid_id).getGridParam("selrow"),
                                'action':'add',
                                'oper':'return',
                                'url':"index.php/procurement/receiveItems"
                            }

                            $( "#dialog-form-item" ).data('grid_data',gridData).dialog( "open" );
                            $("#receiptOp").combobox();


                        }
                        else {

                            $("#status_warning_text").text("You Can Only Modify Quotes Owned By You");
                            $( "#modal-warning-general" ).dialog("open");
                        }
                    }
                }
                else {
                    $( "#modal-warning-one" ).dialog("open") ;
                }

            } 
        });
    }
    if (buttons.cnbd_item==true  && permission.manage_order_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
            caption:"", 
            title:"Register Undelivered",
            buttonicon:"ui-icon-closethick",
            id:"cnbd_"+grid_id,
            onClickButton : function () { 
                //need to pass grid id for dynamic reload;
                var noOfRows = $("#"+grid_id).getGridParam("selarrrow").length;
                if (noOfRows == 1){
                    var onlyOpen =false;
                    if (status=='open' || status=='receiving' || status=='received'){
                        onlyOpen = true;
                    }
                    if (!onlyOpen){
                        $("#status_warning_text").text("Order With Status 'Open'/'Receiving'/'Received' Can Only Be Edited");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                    else {
                        if (user_id == owner_id){
                            var gridData ={
                                'grid_id':grid_id,
                                'order_line_id':$("#"+grid_id).getGridParam("selrow")
                                };

                            $( "#dialog-form-cnbd" ).data('grid_data',gridData).dialog( "open" );


                        }
                        else {

                            $("#status_warning_text").text("You Can Only Modify Quotes Owned By You");
                            $( "#modal-warning-general" ).dialog("open");
                        }
                    }
                }
                else {
                    $( "#modal-warning-one" ).dialog("open") ;
                }

            } 
        });
    }
     
}
// End Order Grid
function discountTypeFormat( cellvalue, options, rowObject ){
    var display;
    if (cellvalue=='directpercentage'){
        display = 'Percentage';
    }
    else if (cellvalue=='directamounttotal'){
        display = 'Amount On Total';
    }
    else if (cellvalue=='directamountunit'){
        display = 'Amount On Unit Price';
    }
    else if (cellvalue=='existing'){
        display = 'Predefined Rules';
    }
    else {
        display = 'NA';
    }

    return display;
}
function discountTypeUnFormat( cellvalue, options, cell){
    var value;
    if (cellvalue=='Percentage'){
        value = 'directpercentage';
    }
    else if (cellvalue=='Amount On Total'){
        value = 'directamounttotal';
    }
    else if (cellvalue=='Amount On Unit Price'){
        value = 'directamountunit';
    }
    else if (cellvalue=='Predefined Rules'){
        value = 'existing';
    }
    else {
        value = 'none';
    }

    return value;
}

function prepareReceiptItemsGrid(settingObj,postData,subgrid,editSettingObj,hiddenSettingObj,subGridSettings){
    var grid_id = settingObj.grid_id;
    var pager = settingObj.pager;
    var subgridMultiselect;
    var subGridCustomButtons={};
    var multiselect = settingObj.multiselect;
    if (multiselect!=true){
        multiselect = false;
    }
    if (editSettingObj=="" || editSettingObj==null || editSettingObj==undefined){
        editSettingObj= {};
    }

    if (hiddenSettingObj=="" || hiddenSettingObj==null || hiddenSettingObj==undefined){
        hiddenSettingObj= {
            expiry_date:true,
            vat:true,
            pp_status:true
        };
       
    }
    
    else {
        console.log(hiddenSettingObj);
        if (isEmpty(hiddenSettingObj.expiry_date)){
            hiddenSettingObj.expiry_date = true;
        }
        if (isEmpty(hiddenSettingObj.vat)){
            hiddenSettingObj.vat = true;
        }
        if (isEmpty(hiddenSettingObj.pp_status)){
            hiddenSettingObj.pp_status = true;
        }
    }
    if (postData=="" || postData==null || postData==undefined){
        postData= {};
    }
    if (subgrid!=true){
        subgrid = false;
        subGridSettings={};
    }
    else {
        if (isEmpty(subGridSettings)){
            subGridSettings= {};
        }
        else {
            if (!isEmpty(subGridSettings.customButtons)){
                subGridCustomButtons = subGridSettings.customButtons;
            }
            
            subgridMultiselect = subGridSettings.multiselect;
            if (subgridMultiselect!=true){
                subgridMultiselect = false;
            }
        }
        
    }
    
    $("#"+grid_id).jqGrid({
        url:"index.php/procurement/populateReceiptItems",
        datatype: 'json',
        mtype: 'POST',
        postData:postData,
        colNames:['Receipt Line Ref','Batch #','Supplier Receipt','Receipt Ref','Receipt Id','Order Id','Order Line Id','Order Line Ref','Product Id','Product','Best Before','VAT %','Qty','Qty Rcvd','Rcvd Value','Total PP Qty','Total PP Amount','Qty Rtrnd','Rtrnd Value','PP Status','Owner','Owner Id','Approver','Approver Id'],
        colModel :[ 
        {
            name:'line_reference', 
            index:'line_reference',
            editable:false,
            width:60, 
            align:'right',
            hidden:hiddenSettingObj.line_reference
            },

            {
            name:'batch_number', 
            index:'batch_number',
            editable:editSettingObj.batch_number,
            width:60, 
            align:'right',
            hidden:hiddenSettingObj.batch_number
            },

            {
            name:'supplier_receipt_number', 
            index:'supplier_receipt_number',
            editable:editSettingObj.supplier_receipt_number,
            width:120, 
            align:'right',
            hidden:hiddenSettingObj.supplier_receipt_number
            },

            {
            name:'reference', 
            index:'reference',
            editable:false,
            width:60, 
            align:'right'
        },

        {
            name:'receipt_id', 
            index:'receipt_id',
            editable:false, 
            hidden:true
        },

        {
            name:'order_id', 
            index:'order_id',
            editable:false, 
            hidden:true
        },

        {
            name:'order_line_id', 
            index:'order_line_id',
            editable:false, 
            hidden:true
        },

        {
            name:'order_line_reference', 
            index:'order_line_id',
            editable:false, 
            hidden:true
        },

        {
            name:'product_id', 
            index:'product_id',
            editable:false, 
            hidden:true
        },

        {
            name:'name', 
            index:'name',
            editable:false, 
            width:140, 
            align:'right'
        },

        {
            name:'expiry_date', 
            index:'expiry_date',
            editable:editSettingObj.batch_number,
            width:60, 
            align:'right',
            hidden:hiddenSettingObj.expiry_date
            },

            {
            name:'vat', 
            index:'vat',
            editable:editSettingObj.batch_number,
            width:40, 
            align:'right',
            hidden:hiddenSettingObj.vat
            },

            {
            name:'ordered_quantity', 
            index:'ordered_quantity', 
            editable:false,
            width:60, 
            align:'right',
            hidden:hiddenSettingObj.ordered_quantity
            },

            {
            name:'received_quantity', 
            index:'received_quantity', 
            editable:false,
            width:50, 
            align:'right',
            hidden:hiddenSettingObj.received_quantity
            },

            {
            name:'received_value', 
            index:'received_value',
            editable:false, 
            width:70, 
            align:'right',
            hidden:hiddenSettingObj.received_value
            },

            {
            name:'pp_quantity', 
            index:'pp_quantity', 
            editable:false,
            width:50, 
            align:'right',
            hidden:hiddenSettingObj.pp_quantity
            },

            {
            name:'pp_value', 
            index:'pp_value',
            editable:false, 
            width:70, 
            align:'right',
            hidden:hiddenSettingObj.pp_value
            },

            {
            name:'returned_quantity', 
            index:'returned_quantity', 
            editable:false,
            width:60, 
            align:'right',
            hidden:hiddenSettingObj.returned_quantity
            },

            {
            name:'returned_value', 
            index:'returned_value',
            editable:false, 
            width:80, 
            align:'right',
            hidden:hiddenSettingObj.returned_value
            },

            {
            name:'pp_status', 
            index:'pp_status',
            editable:false,
            width:70, 
            hidden:hiddenSettingObj.pp_status
            },

            {
            name:'owner_name', 
            index:'owner_name',
            editable:false,
            width:80, 
            align:'right',
            hidden:hiddenSettingObj.owner_name
            },

            //                        {name:'receiving_notes', index:'receiving_notes',editable:false, width:160, align:'right',hidden:hiddenSettingObj.receiving_notes},

            //                        {name:'returned_notes', index:'returned_notes', editable:false,width:160, align:'right',hidden:hiddenSettingObj.receiving_notes},

            {
            name:'owner_id', 
            index:'owner_id',
            editable:false, 
            hidden:true
        },

        {
            name:'approved_by_name', 
            index:'approved_by_name',
            editable:false,
            width:80, 
            align:'right',
            hidden:hiddenSettingObj.approved_by_name
            },

            //                        {name:'receiving_notes', index:'receiving_notes',editable:false, width:160, align:'right',hidden:hiddenSettingObj.receiving_notes},

            //                        {name:'returned_notes', index:'returned_notes', editable:false,width:160, align:'right',hidden:hiddenSettingObj.receiving_notes},

            {
            name:'approved_by', 
            index:'approved_by',
            editable:false, 
            hidden:true
        }
                        
        ],
        pager: '#'+pager,
        rowNum:10,
        rowList:[5,10,20],
        sortname: 'id',
        sortorder: 'desc',
        viewrecords: true,
        gridview: true,
        multiselect:multiselect,
                    
        ignoreCase:true,
        rownumbers:true,
        height:'auto',
                    
        caption: 'Receipt Items',
            
        jsonReader : {
            root:"receiptitemdata",
            page: "page",
            total: "total",
            records: "records",
            cell: "dprow",
            id: "id"
        },
                    
        subGrid:subgrid,
        subGridRowExpanded: function(subgrid_id, row_id) {
            var subgrid_table_id, pager_id;
            subgrid_table_id = subgrid_id+"_t";
                            
            pager_id = "p_"+subgrid_table_id;
                            
            $("#"+subgrid_id).html("<table id='"+subgrid_table_id+"' class='scroll'></table><div id='"+pager_id+"' class='scroll'></div>");
            var subgridSettingObj= {
                grid_id:subgrid_table_id,
                pager:pager_id,
                multiselect:subgridMultiselect
            };
            var postData = {
                receipt_line_id:row_id
            };
            postData._status = subGridSettings.status;
            preparePartPaymentGrid(subgridSettingObj,postData)
            $("#"+subgrid_table_id).jqGrid('navGrid',"#"+pager_id,{
                edit:false,
                add:false,
                del:false,
                search:false,
                view:true
            });
            addCustomButtonsInPartPaymentGrid(subgrid_table_id,pager_id,grid_id,subGridCustomButtons,row_id);
            if (subGridSettings.filter==true){
                $("#"+subgrid_table_id).jqGrid('filterToolbar', {
                    stringResult: true, 
                    searchOnEnter: true, 
                    defaultSearch : "cn"
                });
            }
                            
                             
        }
    })
}
function addCustomButtonsInReceiptItemGrid(grid_id,pager_id,parent_grid,buttons,row_id){
    var permission = isAllowedBulk([
    {
        element:'approver_receipt_item_button',
        resource:'receipt_item',
        permission:'approve'
    },

    {
        element:'approve_receipt_button',
        resource:'receipt',
        permission:'approve'
    },

    {
        element:'manage_receipt_item_button',
        resource:'receipt_item',
        permission:'manage'
    },

    {
        element:'manage_receipt_button',
        resource:'receipt',
        permission:'manage'
    },

    {
        element:'pp_assign_receipt_item_button',
        resource:'receipt',
        permission:'assign'
    }
    ]);
    if (buttons.edit_received == true && (permission.manage_receipt_item_button=="true" || permission.manage_receipt_button=="true")){
        $("#"+grid_id).jqGrid ('navButtonAdd', "#"+pager_id,
        {
            caption:"", 
            title:"Edit Received For This Receipt",
            buttonicon:"ui-icon-newwin",
            id:"receive_"+grid_id,
            onClickButton : function () { 
                //need to pass grid id for dynamic reload;
                var noOfRows = $("#"+grid_id).getGridParam("selarrrow").length;
                if (noOfRows==0){
                    // check for single select
                    if ($("#"+grid_id).getGridParam("selrow")){
                        noOfRows=1;
                    }
                }
                if (noOfRows == 1){
                    var lineId = $("#"+grid_id).getGridParam("selrow");
                    // grid id would help us in getting order line related info from lineItem grid using order_line_id 
                    // Sub grid would help us in getting receipt related information using receipt id
                    var gridData ={
                        'grid_id':parent_grid,
                        'sub_grid_id':grid_id,
                        'order_line_id':$("#"+grid_id).getCell(lineId,'order_line_id'),
                        'receipt_line_id':$("#"+grid_id).getGridParam("selrow"),
                        'action':'edit',
                        'oper':'receive',
                        'url':"index.php/procurement/receiveItems"
                    }
                    $( "#dialog-form-item" ).data('grid_data',gridData).dialog( "open" );

                }
                else {
                    $( "#modal-warning-one" ).dialog("open") ;
                }

            } 
        });
    }
    if (buttons.edit_returned == true && (permission.manage_receipt_item_button=="true" || permission.manage_receipt_button=="true")){
        $("#"+grid_id).jqGrid ('navButtonAdd', "#"+pager_id,
        {
            caption:"", 
            title:"Edit Returned For This Receipt",
            buttonicon:"ui-icon-arrowreturnthick-1-w",
            id:"return_"+grid_id,
            onClickButton : function () { 
                //need to pass grid id for dynamic reload;
                var noOfRows = $("#"+grid_id).getGridParam("selarrrow").length;
                if (noOfRows == 1){
                    var lineId = $("#"+grid_id).getGridParam("selrow");
                    // grid id would help us in getting order line related info from lineItem grid using order_line_id 
                    // Sub grid would help us in getting receipt related information using receipt id
                    var gridData ={
                        'grid_id':parent_grid,
                        'sub_grid_id':grid_id,
                        'order_line_id':$("#"+grid_id).getCell(lineId,'order_line_id'),
                        'receipt_line_id':$("#"+grid_id).getGridParam("selrow"),
                        'action':'edit',
                        'oper':'return',
                        'url':"index.php/procurement/receiveItems"
                    }
                    $( "#dialog-form-item" ).data('grid_data',gridData).dialog( "open" );
                }
                else {
                    $( "#modal-warning-one" ).dialog("open") ;
                }

            } 
        });

    }   
    if (buttons.columnchooser == true){     
        $("#"+grid_id).jqGrid ('navButtonAdd', "#"+pager_id,
        {
            caption: "", 
            buttonicon: "ui-icon-calculator",
            title: "Choose Columns",
            onClickButton: function() {
                $("#"+grid_id).jqGrid('columnChooser');
            }
        });
    }
    if (buttons.reject == true && (permission.approve_receipt_button=="true" || permission.approver_receipt_item_button =="true" )){
        
        $("#"+grid_id).jqGrid('navButtonAdd',"#"+pager_id,{
            caption:"", 
            title:"Reject Receipt Line Items",
            buttonicon:"ui-icon-cancel",
            id:"reject_"+grid_id,
            onClickButton : function () {
                var selectRows = $("#"+grid_id).getGridParam('selarrrow');
                var noOfRows = selectRows.length;

                if (noOfRows ==0){
                    $( "#modal-warning" ).dialog("open");
                }
                else if (noOfRows ==1) {
                    var receiptLineId = $("#"+grid_id).getGridParam('selrow');
                    var grid_data = {
                        'grid_id':grid_id,
                        'url':"index.php/procurement/rejectReceiptLineItem",
                        'receiptLineId':receiptLineId,
                        'receiptId':$("#"+grid_id).getCell(receiptLineId,'receipt_id')
                        } 
                    $( "#dialog-rejection-notes" ).data('grid_data',grid_data).dialog("open") ;
                } 
                else  {

                    grid_data = {
                        'grid_id':grid_id,
                        'url':"index.php/procurement/rejectReceiptLineItem",
                        'receiptLineIds':selectRows,
                        'multiple':true,
                        'receiptId':
                        $("#"+grid_id).getCell(selectRows[0],'receipt_id')
                        } 
                    $( "#dialog-rejection-notes" ).data('grid_data',grid_data).dialog("open") ;
                } 


            } 
        });    
                             
    }
    if (buttons.comments == true /*&& permission.comments_button=="true"*/)  {
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager_id,{
            caption:"", 
            title:"Show Comments",
            buttonicon:"ui-icon-script",
            id:"comments_"+grid_id,
            onClickButton : function () { 

                var rowid = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var noOfRows = rowid.length;
                //var onlyOpen = false;
                if (noOfRows != 1){
                    $( "#modal-warning-exactly-one" ).dialog("open");
                }
                else{
                    var receipt_item_id = $("#"+grid_id).jqGrid('getGridParam', 'selrow');
                    $.ajax({
                        method:"POST",
                        url:"index.php/procurement/getCommentsForEntity",
                        data: {
                            entity:'receipt_item',
                            id : receipt_item_id
                        },
                        success: function(response){
                            var resObj = JSON.parse(response);
                            console.log(resObj);
                            resObj.entity = 'receipt_item';
                            $( "#comment-quote-dialog" ).data('grid_data',resObj).dialog("open") ;
                        //                                $("#"+grid_id).trigger("reloadGrid");
                        //                                showSuccessMessage("Selected RFQ(s) Succesfully Converted To Quote(s)");
                        }
                    });
                //$( "#comment-quote-dialog" ).dialog("open") ;
                }                       
            } 
        });
    } 
    if (buttons.mark_approver==true  && (permission.approve_receipt_button=="true" || permission.approver_receipt_item_button =="true" )){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager_id,{
            caption:"", 
            title:"Mark Yourself As Approver",
            buttonicon:"ui-icon-star",
            id:"mark_approver_"+grid_id,
            onClickButton : function () { 
                var rowid = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var selectedRows = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var noOfRows = selectedRows.length;
                var onlywfa= true;
                var onlyEmpty= true;
                if (noOfRows == 0){
                    $( "#modal-warning-none" ).dialog("open");
                }
                else{
                    $.each(selectedRows, function(){
                         
                        if ($("#"+grid_id).getCell(this,'pp_status')!='waitingforapproval'){
                            onlywfa = false;
                            return false;
                        }
                    })
                    if (!onlywfa){
                        $("#status_warning_text").text("Receipts With Status 'Waiting For Approval ' Can Only Be Permitted For Marking");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                    else {
                        $.each(rowid, function(){

                            if (!isEmpty($("#"+grid_id).getCell(this,'approved_by'))){
                                onlyEmpty = false;
                                return false;
                            }
                        });
                        if (!onlyEmpty){
                            $("#status_warning_text").text("Selected Receipts(s) already has approver. Please Select Records Without Any Approver");
                            $( "#modal-warning-general" ).dialog("open");
                        }
                        else {
                            $.ajax({
                                method:"POST",
                                url:"index.php/procurement/assignEntityToUser",
                                data: {
                                    ids : rowid,
                                    entity:'receipt_item',
                                    user_id:user_id,
                                    role:'approver'
                                },
                                success: function(){
                                    $("#"+grid_id).trigger("reloadGrid");
                                    emptyMessages();
                                    showSuccessMessage("success");
                                }
                            });
                        }
                                
                    }
                }
                        
            } 
        });
    }
    if (buttons.assign==true && permission.pp_assign_receipt_item_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager_id,{
            caption:"", 
            title:"Assign",
            buttonicon:"ui-icon-transferthick-e-w",
            id:"assign_"+grid_id,
            onClickButton : function () { 
                var rowid = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var noOfRows = rowid.length;
                
                if (noOfRows == 0){
                    $( "#modal-warning-none" ).dialog("open");
                }
                else{
                    var selrow = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');  
                    var approver_name = $("#"+grid_id).getCell(selrow,'approved_by_name');
                    
                    if (isEmpty(approver_name)){
                        approver_name ='None';
                    }
                    var grid_data = {
                        entity:'receipt_item',
                        current_approver:approver_name,
                        row_id:selrow,
                        grid_id:grid_id
                    };
                    $( "#assignment-common-dialog" ).data('grid_data',grid_data).dialog( "open" );
                }
                
            } 
        });
    }
    if (buttons.load_approver_all==true){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager_id,{
            caption:"", 
            title:"Reload w/ all/No Approvers ",
            buttonicon:"ui-icon-arrow-4",
            id:"reload_approver_"+grid_id,
            onClickButton : function () { 
                var postData = $("#"+grid_id).getGridParam('postData');
                postData.approved_by = 'all';
                $("#"+grid_id).setGridParam('postData',postData);
                $("#"+grid_id).trigger("reloadGrid");
            } 
        });
    }
    if (buttons.load_status_all==true){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager_id,{
            caption:"", 
            title:"Reload w/ all Status For This Owner",
            buttonicon:"ui-icon-arrowrefresh-1-s",
            id:"reload_status_"+grid_id,
            onClickButton : function () { 
                var postData = $("#"+grid_id).getGridParam('postData');
                postData._status = 'all';
                $("#"+grid_id).setGridParam('postData',postData);
                $("#"+grid_id).trigger("reloadGrid");
            } 
        });
    }
    if (buttons.load_owner_all==true){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager_id,{
            caption:"", 
            title:"Reload w/ all User Records",
            buttonicon:"ui-icon-arrow-4",
            id:"reload_user_"+grid_id,
            onClickButton : function () { 
                var postData = $("#"+grid_id).getGridParam('postData');
                postData.owner_id = 'all';
                $("#"+grid_id).setGridParam('postData',postData);
                $("#"+grid_id).trigger("reloadGrid");
            } 
        });
    }
    if (buttons.resubmit==true  && (permission.manage_receipt_item_button=="true" || permission.manage_receipt_button=="true")){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager_id,{
            caption:"", 
            title:"Resubmit For Approval",
            buttonicon:"ui-icon-flag",
            id:"invoice_"+grid_id,
            onClickButton : function () {
                var rowid =  $("#"+grid_id).getGridParam('selrow');
                       
                var owner_id = $("#"+parent_grid).getCell(row_id,'owner_id');
                if (rowid!=null && rowid!= undefined){
                           
                            
                    if ($("#"+grid_id).getCell(rowid,'status')!='rejected'){
                        $("#status_warning_text").text("Receipts With Status 'Rejected ' Can Only Be Permitted For Marking");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                    else {
                        if (user_id == owner_id){
                            var receiptLineId = $("#"+grid_id).getGridParam('selrow');
                            var grid_data = {'grid_id':grid_id,'url':"index.php/procurement/resubmitReceiptLineItem",'receiptLineId':receiptLineId,'receiptId':$("#"+grid_id).getCell(receiptLineId,'receipt_id')} 
                            $( "#dialog-receipt-items" ).data('grid_data',grid_data).dialog("open") ;
                        }
                        else {

                            $("#status_warning_text").text("You Can Resubmit  Rejected Receipts  Owned By You");
                            $( "#modal-warning-general" ).dialog("open");
                        }
                                
                    }
                            
                }
                else {
                    $( "#modal-warning-none" ).dialog("open");
                }
                               
            } 
        });
    }
    
}

function preparePartPaymentGrid(settingObj,postData,editSettingObj,hiddenSettingObj){
    var grid_id = settingObj.grid_id;
    var pager = settingObj.pager;
    
    
    var multiselect = settingObj.multiselect;
    if (multiselect!=true){
        multiselect = false;
    }
    if (editSettingObj=="" || editSettingObj==null || editSettingObj==undefined){
        editSettingObj= {};
    }

    if (hiddenSettingObj=="" || hiddenSettingObj==null || hiddenSettingObj==undefined){
        hiddenSettingObj= {};
    }
    if (postData=="" || postData==null || postData==undefined){
        postData= {};
    }
    
    jQuery("#"+grid_id).jqGrid({
        url:'index.php/procurement/populatePartpaymentItems',
        datatype: 'json',
        mtype:'POST',
        postData:postData,
        colNames:['Receipt Id','Receipt Line Id','Part Payment Qty','Part Payment Amt','Part Payment Notes','Status'],
        colModel :[ 

        {
            name:'receipt_id', 
            index:'receipt_id',
            editable:false, 
            hidden:true
        },

        {
            name:'receipt_line_id', 
            index:'receipt_id',
            editable:false, 
            hidden:true
        },

        {
            name:'pp_quantity', 
            index:'pp_quantity', 
            editable:false,
            width:120, 
            align:'right'
        },

        {
            name:'pp_value', 
            index:'pp_value',
            editable:false, 
            width:120, 
            align:'right'
        },
                

        {
            name:'pp_notes', 
            index:'pp_notes',
            editable:false, 
            width:200, 
            align:'right'
        },

        {
            name:'status', 
            index:'status',
            editable:false, 
            width:120, 
            align:'right'
        },


        ],
        rowNum:20,
        pager: pager,
        sortname: 'id',
        sortorder: "asc",
        height: '100%',
        multiselect:multiselect,

        jsonReader : {
            root:"ppdata",
            page: "page",
            total: "total",
            records: "records",
            cell: "dprow",
            id: "id"
        }
    });
}
function addCustomButtonsInPartPaymentGrid(grid_id,pager_id,parent_grid,buttons,receipt_line_id){
    if (isEmpty(buttons)){
        buttons= {};
    }
    
   
    var permission = isAllowedBulk([
    {
        element:'approve_receipt_button',
        resource:'receipt',
        permission:'approve'
    },

    {
        element:'assign_receipt_button',
        resource:'receipt',
        permission:'assign'
    },

    {
        element:'manage_receipt_button',
        resource:'receipt',
        permission:'manage'
    },

    {
        element:'gen_invoice_receipt_button',
        resource:'receipt',
        permission:'generatedirect'
    },
    ]);
       
    if (buttons.add == true && permission.manage_receipt_button=="true"){
        $("#"+grid_id).jqGrid ('navButtonAdd', "#"+pager_id,
        {
            caption:"", 
            title:"Add Part Payment",
            buttonicon:"ui-icon-plus",
            id:"pp_add_"+grid_id,
            onClickButton : function () { 
                //need to pass grid id for dynamic reload;
                //           var noOfRows = $("#"+grid_id).getGridParam("selarrrow").length;
                //           if (noOfRows == 1){

                var lineId = receipt_line_id;
                var owner_id = $("#"+parent_grid).getCell(lineId,'owner_id');
                if (user_id!=owner_id){
                    $("#status_warning_text").text("You Are Not Authorised.You Can Only Part Pay Orders/Receipts Owned/Received By You");
                    $( "#modal-warning-general" ).dialog("open");
                        
                }
                else {
                    var gridData ={
                        'pp_grid':grid_id,
                        'receipt_line_grid':parent_grid,
                        'order_line_id':$("#"+parent_grid).getCell(lineId,'order_line_id'),
                        'receipt_line_id':lineId,
                        'action':'add'
                    }
                    $( "#dialog-form-partpayment" ).dialog('option','title','REC-'+$("#"+parent_grid).getCell(lineId,'line_reference'))
                    $( "#dialog-form-partpayment" ).data('grid_data',gridData).dialog( "open" );
                }
            // grid id would help us in getting order line related info from lineItem grid using order_line_id 
            // Sub grid would help us in getting receipt related information using receipt id
                   
            //           }
            //           else {
            //              $( "#modal-warning" ).dialog("open") ;
            //           }

            } 
        });
    }
    if (buttons.edit == true  && permission.manage_receipt_button=="true"){
        $("#"+grid_id).jqGrid ('navButtonAdd', "#"+pager_id,
        {
            caption:"", 
            title:"Edit Part Payment",
            buttonicon:"ui-icon-pencil",
            id:"pp_edit_ss"+grid_id,
            onClickButton : function () { 
                //need to pass grid id for dynamic reload;
                var selectRows = $("#"+grid_id).getGridParam('selarrrow');
                var noOfRows = selectRows.length;
                if (noOfRows == 1){
                    if ($("#"+grid_id).getCell($("#"+grid_id).getGridParam('selrow'),'status')!='open'){
                        $("#status_warning_text").text("Only Items With 'Open' Status Can be Edited");
                        $( "#modal-warning-general" ).dialog("open");
                             
                    }
                    else {
                        var lineId = receipt_line_id;
                             
                        var owner_id = $("#"+parent_grid).getCell(lineId,'owner_id');
                        if (user_id!=owner_id){
                            $("#status_warning_text").text("You Are Not Authorised.You Can Only Edit Part Payment Orders/Receipts Owned/Received By You");
                            $( "#modal-warning-general" ).dialog("open");

                        }
                            
                        else {
                            var gridData ={
                                'pp_grid':grid_id,
                                'receipt_line_grid':parent_grid,
                                'order_line_id':$("#"+parent_grid).getCell(lineId,'order_line_id'),
                                'receipt_line_id':lineId,
                                'action':'edit'
                            }
                            $( "#dialog-form-partpayment" ).dialog('option','title','REC-'+$("#"+parent_grid).getCell(lineId,'line_reference'))
                            $( "#dialog-form-partpayment" ).data('grid_data',gridData).dialog( "open" );
                        }
                    // grid id would help us in getting order line related info from lineItem grid using order_line_id 
                    // Sub grid would help us in getting receipt related information using receipt id
                           
                    }
                   
                }
                else {
                    $( "#modal-warning-exactly-one" ).dialog("open") ;
                }

            } 
        });
    }
    if (buttons.approval==true  && permission.manage_receipt_button=="true"){
        $("#"+grid_id).jqGrid ('navButtonAdd', "#"+pager_id,
        {
            caption:"", 
            title:"Submit For Approval",
            buttonicon:"ui-icon-flag",
            id:"pp_submit_approve_"+grid_id,
            onClickButton : function () {
                var selectRows = $("#"+grid_id).getGridParam('selarrrow');
                var noOfRows = selectRows.length;
                var onlyOpen = true;
                var onlySelf = true;

                if (noOfRows ==0){
                    $( "#modal-warning" ).dialog("open");
                }
                 
                else {

                    var lineId = receipt_line_id;
                             
                    var owner_id = $("#"+parent_grid).getCell(lineId,'owner_id');
                    if (user_id!=owner_id){
                        $("#status_warning_text").text("You Are Not Authorised.You Can Only Submit  Part Payment Orders/Receipts Owned/Received By You");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                        
                    else{
                        $.each(selectRows, function(){
                         
                            if ($("#"+grid_id).getCell(this,'status')!='open'){
                                onlyOpen = false;
                                return false;
                            }
                        })
                        if (!onlyOpen){
                            $("#status_warning_text").text("Line Items With Status 'Open' Can Only Be Submitted For Approval");
                            $( "#modal-warning-open-status" ).dialog("open");
                        }
                        else {

                            $.ajax({
                                type:"POST",
                                url:"index.php/procurement/submitForApprovalPartPayment",
                                data:{
                                    pp_ids:selectRows,
                                    receipt_line_id:lineId
                                },
                                success:function (jqXHR){
                                    emptyMessages();
                                    showSuccessMessage(" Selected Records Have Been Submitted For Approval");
                                    $("#"+grid_id).trigger("reloadGrid");
                                },
                                error:function (jqXHR){
                                    emptyMessages();
                                    showErrorMessage(" Selected Records Could Not Be Submitted For Approval Due To Internal Error");
                                }
                            });
                        }
                    }
                     
                     
                }

            } 
        });
    }
    if (buttons.approve==true  && permission.approve_receipt_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager_id,{
            caption:"", 
            title:"Approve And Generate Invoice",
            buttonicon:"ui-icon-check",
            id:"invoice_"+grid_id,
            onClickButton : function () {
                var selectRows = $("#"+grid_id).getGridParam('selarrrow');
                var noOfRows = selectRows.length;
                var onlyOpen = true;
                

                if (noOfRows ==0){
                    $( "#modal-warning-none" ).dialog("open");
                }
                else {
                    var lineId = receipt_line_id;
                             
                    var approver_id = $("#"+parent_grid).getCell(lineId,'approved_by');
                    if (user_id!=approver_id){
                        $("#status_warning_text").text("You Are Not Authorised.You Need To Be Approver To Perform This Operation");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                    else {
                        $.each(selectRows, function(){

                            if ($("#"+grid_id).getCell(this,'status')!='waitingforapproval'){
                                onlyOpen = false;
                                return false;
                            }
                        });
                        if (!onlyOpen){
                            $("#status_warning_text").text("Line Items With Status 'waitingforapproval' Can Only Be Submitted For Invoicing");
                            $( "#modal-warning-open-status" ).dialog("open");
                        }
                        else {
                            $.ajax({
                                type:"POST",
                                url:"index.php/procurement/createInvoicePPUpdateOrderReceipt",
                                data:{
                                    pp_ids:selectRows,
                                    receipt_line_id:receipt_line_id,
                                    order_id:$("#"+parent_grid).getCell(receipt_line_id,'order_id')
                                    },
                                success:function (jqXHR){
                                    emptyMessages();
                                    var jsonRes = JSON.parse(jqXHR);
                                    if (jsonRes.invoice_id!=null){
                                        showSuccessMessage("Invoice No Inv-" + jsonRes.invoice_id + " Is Created Successfully");
                                    }
                                    $("#"+grid_id).trigger("reloadGrid");
                                    $("#"+parent_grid).trigger("reloadGrid");
                                }
                            });
                        }
                    }
                    
                }
            } 
        });
    }
    if (buttons.reject == true  && permission.approve_receipt_button=="true"){
        
        $("#"+grid_id).jqGrid('navButtonAdd',"#"+pager_id,{
            caption:"", 
            title:"Reject Receipt Line Items",
            buttonicon:"ui-icon-alert",
            id:"reject_"+grid_id,
            onClickButton : function () {
                var selectRows = $("#"+grid_id).getGridParam('selarrrow');
                var noOfRows = selectRows.length;
                var lineId = receipt_line_id;
                             
                var approver_id = $("#"+parent_grid).getCell(lineId,'approved_by');
                
                if (noOfRows ==0){
                    $( "#modal-warning-none" ).dialog("open");
                }
                else if (noOfRows ==1) {
                    
                    if (user_id!=approver_id){
                        $("#status_warning_text").text("You Are Not Authorised.You Need To Be Approver To Perform This Operation");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                    else{
                        var ppID = $("#"+grid_id).getGridParam('selrow');
                        var grid_data = {
                            'grid_id':grid_id,
                            'url':"index.php/procurement/rejectPPItem",
                            'ppId':ppID
                        } ;
                        $( "#dialog-rejection-notes" ).data('grid_data',grid_data).dialog("open") ;
                    }
                } 
                else  {
                    
                    if (user_id!=approver_id){
                        $("#status_warning_text").text("You Are Not Authorised.You Need To Be Approver To Perform This Operation");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                    else{
                        grid_data = {
                            'grid_id':grid_id,
                            'url':"index.php/procurement/rejectPPItem",
                            'ppIds':selectRows,
                            'multiple':true
                        };
                        $( "#dialog-rejection-notes" ).data('grid_data',grid_data).dialog("open") ;
                    }
                } 


            } 
        });    
                             
    }
    if (buttons.process==true  && permission.gen_invoice_receipt_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager_id,{
            caption:"", 
            title:" Generate Invoice Without Approval",
            buttonicon:"ui-icon-check",
            id:"invoice_"+grid_id,
            onClickButton : function () {
                var selectRows = $("#"+grid_id).getGridParam('selarrrow');
                var noOfRows = selectRows.length;
                var onlyOpen = true;
                

                if (noOfRows ==0){
                    $( "#modal-warning" ).dialog("open");
                }
                else {
                    var lineId = receipt_line_id;        
                    var owner_id = $("#"+parent_grid).getCell(lineId,'owner_id');
                    if (user_id!=owner_id){
                        $("#status_warning_text").text("You Are Not Authorised.You Can Only Submit  Part Payment Orders/Receipts Owned/Received By You");
                        $( "#modal-warning-general" ).dialog("open");
                    }

                    else{
                        $.each(selectRows, function(){
                         
                            if ($("#"+grid_id).getCell(this,'status')!='open'){
                                onlyOpen = false;
                                return false;
                            }
                        });
                        if (!onlyOpen){
                            $("#status_warning_text").text("Line Items With Status 'Open' Can Only Be Submitted For Invoicing");
                            $( "#modal-warning-open-status" ).dialog("open");
                        }
                        else {
                            $.ajax({
                                type:"POST",
                                url:"index.php/procurement/createInvoicePPUpdateOrderReceipt",
                                data:{
                                    pp_ids:selectRows,
                                    receipt_line_id:receipt_line_id,
                                    order_id:$("#"+parent_grid).getCell(receipt_line_id,'order_id')
                                    },
                                success:function (jqXHR){
                                    emptyMessages();
                                    var jsonRes = JSON.parse(jqXHR);
                                    if (jsonRes.invoice_id!=null){
                                        showSuccessMessage("Invoice No Inv-" + jsonRes.invoice_id + " Is Created Successfully");
                                    }
                                    $("#"+grid_id).trigger("reloadGrid");
                                    $("#"+parent_grid).trigger("reloadGrid");
                                }
                            });
                        }
                    }
                     
                    
                }
            } 
        });
    }
    if (buttons.cancel == true && permission.manage_receipt_button=="true"){
        $("#"+grid_id).jqGrid ('navButtonAdd', "#"+pager_id,
        {
            caption:"", 
            title:"Cancel Part Payment",
            buttonicon:"ui-icon-cancel",
            id:"pp_cancel_"+grid_id,
            onClickButton : function () {
                var selectRows = $("#"+grid_id).getGridParam('selarrrow');
                var noOfRows = selectRows.length;
                var processed = false;
                

                if (noOfRows ==0){
                    $( "#modal-warning" ).dialog("open");
                }
                 
                else {
                    var lineId = receipt_line_id;        
                    var owner_id = $("#"+parent_grid).getCell(lineId,'owner_id');
                    if (user_id!=owner_id){
                        $("#status_warning_text").text("You Are Not Authorised.You Can Only Cancel  Part Payment Orders/Receipts Owned/Received By You");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                    else{
                        $.each(selectRows, function(){
                         
                            if ($("#"+grid_id).getCell(this,'status')=='processedforpayment'){
                                processed = true;
                                return false;
                            }
                        })

                        if (processed){
                            $("#status_warning_text").text("Line Items With Status 'processedforpayment' Can Not Be Cancelled");
                            $( "#modal-warning-open-status" ).dialog("open");
                        }
                        else {
                            $.ajax({
                                type:"POST",
                                url:"index.php/procurement/cancelPartPayment",
                                data:{
                                    pp_ids:selectRows,
                                    receipt_line_id:receipt_line_id,
                                    order_line_id:$("#"+parent_grid).getCell(receipt_line_id,'order_line_id')
                                    },
                                success:function (jqXHR){
                                    emptyMessages();
                                    showSuccessMessage(" Selected Records Have Been Cancelled");
                                    $("#"+grid_id).trigger("reloadGrid");
                                },
                                error:function (jqXHR){
                                    emptyMessages();
                                    showErrorMessage(" Selected Records Could Not Be Submitted For Approval Due To Internal Error");
                                }
                            })
                        } 
                    }
                     
                     
                }

            } 
        });
    }
    
    
}

function preparePaymentsGrid(settingObj,postData,editSettingObj,hiddenSettingObj){
    var grid_id = settingObj.grid_id;
    var pager = settingObj.pager;
    
    
    var multiselect = settingObj.multiselect;
    if (multiselect!=true){
        multiselect = false;
    }
    if (isEmpty(editSettingObj)){
        editSettingObj= {};
    }

    if (isEmpty(hiddenSettingObj)){
        hiddenSettingObj= {
            payment_type:true
        };
    }
    if (postData=="" || postData==null || postData==undefined){
        postData= {};
    }
    
    $("#"+grid_id).jqGrid({
        url:'index.php/procurement/populatePayments',
        datatype: 'json',
        mtype: 'POST',
        postData:postData,
        colNames:['Payment Reference','Pay Mode','Pay Type','Amount','Comments','Adj w/Advance','Parent Id'],
        colModel :[ 
        {
            name:'payment_reference', 
            index:'payment_reference'
        },

        {
            name:'payment_mode', 
            index:'payment_mode',
            editable:false, 
            width:70, 
            align:'right'
        },

        {
            name:'payment_type', 
            index:'payment_type',
            editable:false, 
            width:17000, 
            align:'right',
            hidden:hiddenSettingObj.payment_type
            },

            {
            name:'amount', 
            index:'amount', 
            editable:false,
            width:60, 
            align:'right'
        },

        {
            name:'comments', 
            index:'comments', 
            editable:false,
            width:150, 
            align:'right'
        },

        {
            name:'adjusted', 
            index:'adjusted', 
            editable:false,
            width:50, 
            align:'right'
        },

        {
            name:'parent_id', 
            index:'parent_id', 
            editable:false,
            width:150, 
            align:'right',
            hidden:true
        }

        //                                            {name:'comments', index:'comments',editable:true, width:180, align:'right'}

        ],
        pager: '#'+pager,
        rowNum:10,
        rowList:[5,10,20],
        sortname: 'id',
        sortorder: 'desc',
        viewrecords: true,
        gridview: true,
        ignoreCase:true,
        rownumbers:true,
        multiselect:true,
        height:'auto',
        width:'50%',
        caption: 'Payments',

        jsonReader : {
            root:"paymentdata",
            page: "page",
            total: "total",
            records: "records",
            cell: "dprow",
            id: "id"
        }

    });
}
function addCustomButtonsInPaymentsGrid(grid_id,pager_id,parent_grid,buttons,row_id){
    var permission = isAllowedBulk([
       
    {
            element:'approve_payment_button',
            resource:'payment',
            permission:'approve'
        },

        {
            element:'manage_payment_button',
            resource:'payment',
            permission:'manage'
        }
       
        ]);
    if (buttons.add ==true && permission.manage_payment_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager_id,{
            caption:"", 
            title:"Add Payment Details",
            buttonicon:"ui-icon-plus",
            id:"add_"+grid_id,
            onClickButton : function () { 
                // only the owner of this order/inbvoice be able to add advance
                if (user_id == buttons.data.owner_id){
                    var total_value = parseFloat($("#total_value").text());
                    var amount_paid = parseFloat($("#amount_paid").text());
                    if (total_value==amount_paid && buttons.data.type!='advance'){
                        $( "#modal-error-total-payment" ).dialog( "open" );
                    }
                    else {
                        var grid_data ={
                            'oper':'add',
                            'type':buttons.data.type,
                            pay_id:$("#"+grid_id).getGridParam('selrow'),
                            'order_id':buttons.data.order_id,
                            'invoice_id':buttons.data.invoice_id
                            }
                        $( "#dialog-payment" ).data('grid_data',grid_data).dialog( "open" );
                    }
                }
                else {
                    $("#status_warning_text").text("You Must Own Order/Invoice to make payment");
                    $( "#modal-warning-general" ).dialog("open");
                }
                

            } 
        });
    }
    if (buttons.edit ==true && permission.manage_payment_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager_id,{
            caption:"", 
            title:"Edit Payment Details",
            buttonicon:"ui-icon-pencil",
            id:"edit_"+grid_id,
            onClickButton : function () { 
                var noOfRows = $("#"+grid_id).getGridParam('selarrrow').length;
                if (noOfRows!=1){
                    $( "#modal-warning-one" ).dialog( "open" );
                }
                else {
                    if (user_id == buttons.data.owner_id){
                        var grid_data ={
                            'oper':'edit',
                            'type':buttons.data.type,
                            pay_id:$("#"+grid_id).getGridParam('selrow'),
                            'order_id':buttons.data.order_id,
                            'invoice_id':buttons.data.invoice_id
                            };
                        $( "#dialog-payment" ).data('grid_data',grid_data).dialog( "open" );
                    }
                    else {
                        $("#status_warning_text").text("You Must Own Order/Invoice to edit payment");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                       
                }


            } 
        });
    }
    if (buttons.adjust ==true && permission.manage_payment_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager_id,{
            caption:"", 
            title:"Adjust With Advance Payment",
            buttonicon:"ui-icon-arrowthickstop-1-e",
            id:"assign_"+grid_id,
            onClickButton : function () { 
                $.ajax({
                    url:"index.php/procurement/populateAdvancePayments",
                    type:"POST",
                    data:{
                        order_id:buttons.data.order_id
                           
                    },
                    success:function (response){
                        var responseObj = JSON.parse(response);
                        var num = parseInt(responseObj.total);
                        if (num==0){
                            $( "#modal-error-advance-payment" ).dialog("open");
                        }
                        else {
                            if (user_id == buttons.data.owner_id){
                                var grid_data ={
                                    'oper':'assign',
                                    'type':'assign',
                                    pay_id:$("#"+grid_id).getGridParam('selrow'),
                                    'order_id':buttons.data.order_id,
                                    'invoice_id':buttons.data.invoice_id
                                    };
                                $( "#dialog-payment" ).data('grid_data',grid_data).dialog( "open" );
                                console.log(responseObj.advanceOptions)
                                $("#advance_ref").children().not(':eq(0)').remove();
                                $("#advance_ref").append(responseObj.advanceOptions); 

                            }
                            else {

                                $("#status_warning_text").text("You Must Be A Owner For This Order To Receive Goods. Please Mark Yourself As Receiver Or Request For Same");
                                $( "#modal-warning-general" ).dialog("open");
                            }
                                 
                        }
                            
                            
                    }
                });
                       
                   
            } 
        });
    }
                
}

function prepareReceiptsGrid(settingObj,postData,subgrid,editSettingObj,hiddenSettingObj,subGridSettings){
    var grid_id = settingObj.grid_id;
    var pager = settingObj.pager;
    var subgridMultiselect;
    var subGridCustomButtons;
    var multiselect = settingObj.multiselect;
    var hiddenObjSubgrid = {
        line_reference:true,
        pp_quantity:true,
        pp_value:true,
        receiving_notes:true,
        returned_notes:true
    };
    if (multiselect!=true){
        multiselect = false;
    }
    if (editSettingObj=="" || editSettingObj==null || editSettingObj==undefined){
        editSettingObj= {};
    }

    if (hiddenSettingObj=="" || hiddenSettingObj==null || hiddenSettingObj==undefined){
        hiddenSettingObj= {};
    }
    if (postData=="" || postData==null || postData==undefined){
        postData= {};
    }
    if (subgrid!=true){
        subgrid = false;
        subGridSettings={};
    }
    else {
        if (isEmpty(subGridSettings)){
            subGridSettings= {};
        }
        else {
            subGridCustomButtons = subGridSettings.customButtons;
            subgridMultiselect = subGridSettings.multiselect;
            if (subgridMultiselect!=true){
                subgridMultiselect = false;
            }
            if (!isEmpty(subGridSettings.hiddenObj)){
                hiddenObjSubgrid= subGridSettings.hiddenObj;
            }
        }
    }
    
    $("#"+grid_id).jqGrid({
        url:"index.php/procurement/populateReceipts",
        datatype: 'json',
        mtype: 'POST',
        postData:postData,
        colNames:['Receipt Reference','Supplier Receipt','Supplier',/*'Estimated Value','Owner',*/'Status','Order Id','Order Ref','Quote Ref','Owner','Owner Id','Approver','Approver Id'],
        colModel :[ 
        {
            name:'reference', 
            index:'reference', 
            width:100, 
            align:'right',
            editable:false
        },

        {
            name:'supplier_receipt_number',
            width:140, 
            index:'supplier_receipt_number',
            editable:false,
            align:'right'
        },

        {
            name:'supplier_name', 
            index:'supplier_name', 
            width:140, 
            align:'right',
            editable:false
        },

        {
            name:'status', 
            index:'status', 
            width:100, 
            align:'right',
            editable:false,
            editoptions:{
                size:"20",
                maxlength:"30"
            }
        },

        {
        name:'order_id', 
        index:'order_id',
        hidden:true
    },

    {
        name:'order_reference', 
        index:'order_reference',
        editable:false, 
        width:100, 
        align:'right'
    },

    {
        name:'quote_reference', 
        index:'quote_reference',
        editable:false, 
        width:100, 
        align:'right'
    },

    {
        name:'owner_name', 
        index:'owner_name',
        editable:false,
        width:80, 
        align:'right',
        hidden:hiddenSettingObj.owner_name
        },

        {
        name:'owner_id', 
        index:'owner_id',
        editable:false, 
        hidden:true
    },

    {
        name:'approved_by_name', 
        index:'approved_by_name',
        editable:false,
        width:80, 
        align:'right',
        hidden:hiddenSettingObj.approved_by_name
        },

        {
        name:'approved_by', 
        index:'approved_by',
        editable:false, 
        hidden:true
    }
    ],
    pager: '#'+pager,
    rowNum:10,
    rowList:[5,10,20],
    sortname: 'id',
    sortorder: 'desc',
    viewrecords: true,
    gridview: true,
    multiselect:multiselect,
                    
    ignoreCase:true,
    rownumbers:true,
    height:'auto',
    width:'90%',
    caption: 'Receipts',
            
    jsonReader : {
        root:"quotedata",
        page: "page",
        total: "total",
        records: "records",
        cell: "dprow",
        id: "id"
    },
                    
    subGrid:subgrid,
    subGridRowExpanded: function(subgrid_id, row_id) {
        var subgrid_table_id, pager_id;
        subgrid_table_id = subgrid_id+"_t";
        console.log(subgrid_id + " row " + row_id);
        pager_id = "p_"+subgrid_table_id;
                            
        $("#"+subgrid_id).html("<table id='"+subgrid_table_id+"' class='scroll'></table><div id='"+pager_id+"' class='scroll'></div>");
                          
                            
        var settingObj = {
            grid_id:subgrid_table_id,
            pager:pager_id,
            multiselect:subgridMultiselect
        };
        prepareReceiptItemsGrid(settingObj,{
            oper:'receipt',
            receiptId:row_id
        },false,{},hiddenObjSubgrid);
                            
        $("#"+subgrid_table_id).jqGrid('navGrid',"#"+pager_id,{
            edit:false,
            add:false,
            del:false,
            search:false,
            view:true
        });
        addCustomButtonsInReceiptItemGrid(subgrid_table_id, pager_id,grid_id,subGridCustomButtons,row_id);
                            
    }
    });
}
function addCustomButtonsInReceiptGrid(grid_id,pager_id,parent_grid,buttons,row_id){
    
    var permission = isAllowedBulk([
    {
        element:'mark_approver_receipt_button',
        resource:'receipt',
        permission:'approve'
    },

    {
        element:'approve_receipt_button',
        resource:'receipt',
        permission:'approve'
    },

    {
        element:'manage_receipt_button',
        resource:'receipt',
        permission:'manage'
    },

    {
        element:'assign_receipt_button',
        resource:'receipt',
        permission:'assign'
    },

    {
        element:'gen_invoice_receipt_button',
        resource:'receipt',
        permission:'generatedirect'
    },
    ]);
    if (buttons.submit==true && permission.manage_receipt_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#pager',{
            caption:"", 
            title:"Submit For Approval",
            buttonicon:"ui-icon-flag",
            id:"submit_approve_"+grid_id,
            onClickButton : function () {
                var selectRows = $("#"+grid_id).getGridParam('selarrrow');
                var noOfRows = selectRows.length;
                var onlySelf = true;
                var onlyOpen = true;
                       
                if (noOfRows ==0){
                    $( "#modal-warning-none" ).dialog("open");
                }
                else 
                    $.each(selectRows, function(){
                        var status = $("#"+grid_id).getCell(this,'status');
                        console.log(this + " " + status)
                        if (status !='readytoinvoicememo' ){
                                   
                            onlyOpen = false;
                            return false;
                        }
                    });
                if (!onlyOpen){
                    $("#status_warning_text").text("Receipts With Status 'readytoinvoicememo' Or 'Draft' Can Only Be processed");
                    $( "#modal-warning-general" ).dialog("open");
                }
                else {
                    $.each(selectRows, function(){

                        if ($("#"+grid_id).getCell(this,'owner_id')!=user_id){
                            onlySelf = false;
                            return false;
                        }
                    });
                    if (!onlySelf){
                        $("#status_warning_text").text("You Need to Be Owner For *ALL* Selected Receipts ");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                    else{
                        $.ajax({
                            type:"POST",
                            url:"index.php/procurement/submitForApprovalBeforeInvoice",
                            data:{
                                receipt_ids:selectRows
                            },
                            success:function (jqXHR){
                                emptyMessages();
                                showSuccessMessage(" Selected Records Have Been Submitted For Approval");
                                $("#"+grid_id).trigger("reloadGrid");
                            },
                            error:function (jqXHR){
                                emptyMessages();
                                showErrorMessage(" Selected Records Could Not Be Submitted For Approval Due To Internal Error");
                            }
                        });
                    }
                }
                               
            } 
        });
    }
    if (buttons.process==true && permission.gen_invoice_receipt_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#pager',{
            caption:"", 
            title:"Create Invoice And Memo" ,
            buttonicon:"ui-icon-note",
            id:"process_"+grid_id,
            onClickButton : function () {
                var selectRows = $("#"+grid_id).getGridParam('selarrrow');
                var noOfRows = selectRows.length;
                var orderId = 0;
                var mismatch = 0;
                var onlySelf=true;
                var onlyOpen = true;
                if (noOfRows ==0){
                    $( "#modal-warning-none" ).dialog("open");
                }
                else {
                    $.each(selectRows, function(){
                        var status = $("#"+grid_id).getCell(this,'status');
                        console.log(this + " " + status)
                        if (status !='readytoinvoicememo' ){
                                   
                            onlyOpen = false;
                            return false;
                        }
                    });
                    if (!onlyOpen){
                        $("#status_warning_text").text("Receipts With Status 'readytoinvoicememo' Or 'Draft' Can Only Be processed");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                    else {
                        $.each(selectRows, function(){

                            if ($("#"+grid_id).getCell(this,'owner_id')!=user_id){
                                onlySelf = false;
                                return false;
                            }
                        });
                        if (!onlySelf){
                            $("#status_warning_text").text("You Need to Be Owner For *ALL* Selected Receipts ");
                            $( "#modal-warning-general" ).dialog("open");
                        }
                        else{
                            for (i=0;i<noOfRows;i++){
                                //console.log(selectRows[i]);
                                var newOrderId = $("#"+grid_id).getCell(selectRows[i],'order_id');
                                //console.log(newOrderId);
                                if (orderId==0){
                                    orderId = newOrderId;
                                }
                                else if (newOrderId!=orderId){
                                    $( "#modal-warning-order" ).dialog("open");
                                    mismatch = 1;
                                    break;
                                }
                            }
                            if (mismatch==0){
                                $.ajax({
                                    type:"POST",
                                    url:"index.php/procurement/createInvoiceMemoUpdateOrderReceipt",
                                    data:{
                                        receipt_ids:selectRows,
                                        order_id:orderId
                                    },
                                    success:function (jqXHR){
                                        emptyMessages();
                                        var jsonRes = JSON.parse(jqXHR);
                                        if (jsonRes.invoice_id!=null){
                                            showSuccessMessage("Invoice No Inv-" + jsonRes.invoice_id + " Is Created Successfully");
                                        }
                                        if (jsonRes.memo_id!=null){
                                            showSuccessMessage("CreditMemo No CM-" + jsonRes.memo_id + " Is Created Successfully");
                                        }
                                        $("#"+grid_id).trigger("reloadGrid");
                                    }
                                })
                            }
                               
                        }
                               
                    }
                            
                             
                }
            }
        });
    }
    
    if (buttons.approve==true && permission.approve_receipt_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager_id,{
            caption:"", 
            title:"Approve And Generate Invoice/Memo",
            buttonicon:"ui-icon-check",
            id:"invoice_"+grid_id,
            onClickButton : function () {
                var selectRows = $("#"+grid_id).getGridParam('selarrrow');
                var noOfRows = selectRows.length;
                var orderId = 0;
                var mismatch = 0;
                var onlySelf = true;
                var onlyOpen = true;
                if (noOfRows ==0){
                    $( "#modal-warning-none" ).dialog("open");
                }
                else {
                    $.each(selectRows, function(){
                        var status = $("#"+grid_id).getCell(this,'status');
                        console.log(this + " " + status)
                        if (status !='waitingforapproval' ){
                                   
                            onlyOpen = false;
                            return false;
                        }
                    });
                    if (!onlyOpen){
                        $("#status_warning_text").text("Receipts With Status 'readytoinvoicememo' Or 'Draft' Can Only Be processed");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                    else {
                        $.each(selectRows, function(){

                            if ($("#"+grid_id).getCell(this,'approved_by')!=user_id){
                                onlySelf = false;
                                return false;
                            }
                        });
                        if (!onlySelf){
                            $("#status_warning_text").text("You Need to Be Approver For *ALL* Selected Receipts ");
                            $( "#modal-warning-general" ).dialog("open");
                        }
                        else {
                            for (i=0;i<noOfRows;i++){
                                //console.log(selectRows[i]);
                                var newOrderId = $("#"+grid_id).getCell(selectRows[i],'order_id');
                                //console.log(newOrderId);
                                if (orderId==0){
                                    orderId = newOrderId;
                                }
                                else if (newOrderId!=orderId){
                                    $( "#modal-warning-order" ).dialog("open");
                                    mismatch = 1;
                                    break;
                                }
                            }
                            if (mismatch==0){
                                $.ajax({
                                    type:"POST",
                                    url:"index.php/procurement/createInvoiceMemoUpdateOrderReceipt",
                                    data:{
                                        receipt_ids:selectRows,
                                        order_id:orderId
                                    },
                                    success:function (jqXHR){
                                        emptyMessages();
                                        var jsonRes = JSON.parse(jqXHR);
                                        if (jsonRes.invoice_id!=null){
                                            showSuccessMessage("Invoice No Inv-" + jsonRes.invoice_id + " Is Created Successfully");
                                        }
                                        if (jsonRes.memo_id!=null){
                                            showSuccessMessage("CreditMemo No CM-" + jsonRes.memo_id + " Is Created Successfully");
                                        }
                                        $("#"+grid_id).trigger("reloadGrid");
                                    }
                                })
                            }
                        }
                     
                    }
                    
                }


            } 
        });
    }
    if (buttons.reject==true && permission.approve_receipt_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager_id,{
            caption:"", 
            title:"Reject Entire Receipt",
            buttonicon:"ui-icon-cancel",
            id:"reject_"+grid_id,
            onClickButton : function () {
                var selectRows = $("#"+grid_id).getGridParam('selarrrow');
                var noOfRows = selectRows.length;
                var onlySelf = true;
                var onlyOpen = true;
                       
                if (noOfRows !=1){
                    $( "#modal-warning-one" ).dialog("open");
                }
                else{
                    $.each(selectRows, function(){
                        var status = $("#"+grid_id).getCell(this,'status');
                        console.log(this + " " + status)
                        if (status !='waitingforapproval' ){
                                   
                            onlyOpen = false;
                            return false;
                        }
                    });
                    if (!onlyOpen){
                        $("#status_warning_text").text("Receipts With Status 'readytoinvoicememo' Or 'Draft' Can Only Be processed");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                    else {
                        $.each(selectRows, function(){

                            if ($("#"+grid_id).getCell(this,'approved_by')!=user_id){
                                onlySelf = false;
                                return false;
                            }
                        });
                        if (!onlySelf){
                            $("#status_warning_text").text("You Need to Be Approver For *ALL* Selected Receipts ");
                            $( "#modal-warning-general" ).dialog("open");
                        }
                        else {
                            var grid_data = {
                                'grid_id':grid_id,
                                'url':"index.php/procurement/rejectReceipt",
                                'receiptId':$("#"+grid_id).getGridParam('selrow')
                                } 
                            $( "#dialog-rejection-notes" ).data('grid_data',grid_data).dialog("open") ;
                        }
                    }
                            
                           
                } 
                            
                               
            } 
        });
    }
    if (buttons.mark_approver==true  && permission.mark_approver_receipt_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager_id,{
            caption:"", 
            title:"Mark Yourself As Approver",
            buttonicon:"ui-icon-star",
            id:"mark_approver_"+grid_id,
            onClickButton : function () { 
                var rowid = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var selectedRows = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var noOfRows = selectedRows.length;
                var onlywfa= true;
                var onlyEmpty= true;
                if (noOfRows == 0){
                    $( "#modal-warning-none" ).dialog("open");
                }
                else{
                    $.each(selectedRows, function(){
                         
                        if ($("#"+grid_id).getCell(this,'status')!='waitingforapproval'){
                            onlywfa = false;
                            return false;
                        }
                    })
                    if (!onlywfa){
                        $("#status_warning_text").text("Receipts With Status 'Waiting For Approval ' Can Only Be Permitted For Marking");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                    else {
                        $.each(rowid, function(){

                            if (!isEmpty($("#"+grid_id).getCell(this,'approved_by'))){
                                onlyEmpty = false;
                                return false;
                            }
                        });
                        if (!onlyEmpty){
                            $("#status_warning_text").text("Selected Receipts(s) already has approver. Please Select Records Without Any Approver");
                            $( "#modal-warning-general" ).dialog("open");
                        }
                        else {
                            $.ajax({
                                method:"POST",
                                url:"index.php/procurement/assignEntityToUser",
                                data: {
                                    ids : rowid,
                                    entity:'receipt',
                                    user_id:user_id,
                                    role:'approver'
                                },
                                success: function(){
                                    $("#"+grid_id).trigger("reloadGrid");
                                    emptyMessages();
                                    showSuccessMessage("success");
                                }
                            });
                        }
                                
                    }
                }
                        
            } 
        });
    }
    if (buttons.assign==true && permission.assign_receipt_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager_id,{
            caption:"", 
            title:"Assign",
            buttonicon:"ui-icon-transferthick-e-w",
            id:"assign_"+grid_id,
            onClickButton : function () { 
                var rowid = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var noOfRows = rowid.length;
                
                if (noOfRows == 0){
                    $( "#modal-warning-none" ).dialog("open");
                }
                else{
                    var selrow = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');  
                    var approver_name = $("#"+grid_id).getCell(selrow,'approved_by_name');
                    
                    if (isEmpty(approver_name)){
                        approver_name ='None';
                    }
                    var grid_data = {
                        entity:'receipt',
                        current_approver:approver_name,
                        row_id:selrow,
                        grid_id:grid_id
                    };
                    $( "#assignment-common-dialog" ).data('grid_data',grid_data).dialog( "open" );
                }
                
            } 
        });
    }
    if (buttons.load_approver_all==true){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager_id,{
            caption:"", 
            title:"Reload w/ all/No Approvers ",
            buttonicon:"ui-icon-arrow-4",
            id:"reload_approver_"+grid_id,
            onClickButton : function () { 
                var postData = $("#"+grid_id).getGridParam('postData');
                postData.approved_by = 'all';
                $("#"+grid_id).setGridParam('postData',postData);
                $("#"+grid_id).trigger("reloadGrid");
            } 
        });
    }
    if (buttons.load_owner_all==true){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager_id,{
            caption:"", 
            title:"Reload w/ all User Records",
            buttonicon:"ui-icon-arrow-4",
            id:"reload_user_"+grid_id,
            onClickButton : function () { 
                var postData = $("#"+grid_id).getGridParam('postData');
                postData.owner_id = 'all';
                $("#"+grid_id).setGridParam('postData',postData);
                $("#"+grid_id).trigger("reloadGrid");
            } 
        });
    }
    if (buttons.resubmit==true && permission.manage_receipt_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#pager',{
            caption:"", 
            title:"Resubmit For Approval",
            buttonicon:"ui-icon-bookmark",
            id:"invoice_orders",
            onClickButton : function () {
                var selectRows = $("#"+grid_id).getGridParam('selarrrow');
                var noOfRows = selectRows.length;
                var onlyRejected = true;
                var onlySelf = true;
                if (noOfRows ==0){
                    $( "#modal-warning" ).dialog("open");
                }
                else {
                    $.each(selectRows, function(){
                        var status = $("#"+grid_id).getCell(this,'status');
                        console.log(this + " " + status)
                        if (status !='rejected' ){
                                   
                            onlyRejected = false;
                            return false;
                        }
                    });
                    if (!onlyRejected){
                        $("#status_warning_text").text("Receipts With Status 'Rejected'  Can Only Be Resubmitted");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                    else {
                        $.each(selectRows, function(){

                            if ($("#"+grid_id).getCell(this,'owner_id')!=user_id){
                                onlySelf = false;
                                return false;
                            }
                        });
                        if (!onlySelf){
                            $("#status_warning_text").text("You Need to Be Owner For *ALL* Selected Receipts ");
                            $( "#modal-warning-general" ).dialog("open");
                        }
                        else {
                            $.ajax({
                                type:"POST",
                                url:"index.php/procurement/resubmitReceipts",
                                data:{
                                    receipt_ids:selectRows
                                },
                                success:function (jqXHR){
                                    var jsonRespons = JSON.parse(jqXHR)
                                    console.log(jsonRespons);
                                    emptyMessages();
                                    if (jsonRespons.success!=undefined){
                                        showSuccessMessage("Receipt(s) " + jsonRespons.success + " Has Been Suceesfuly Resubmitted For Re-Approval.");
                                        $("orders").trigger("reloadGrid");
                                    }

                                    if (jsonRespons.failed!=undefined){
                                        showSuccessMessage(" Receipt(s) " + jsonRespons.failed + " Could Not Be Resubmitted As These Still Has 'Rejected Line Items.");
                                    }


                                }
                            })
                        }
                    }
                            
                }
                        
                               
            } 
        });
    }
                
}

function isEmpty(obj){
    
    if (obj===false){
        return false;
    }
    else if (obj=="" || obj==null || obj==undefined){
        return true;
    }
    return false;
}

function prepareRFQGrid(settingObj,postData,subgrid,editSettingObj,hiddenSettingObj,subGridSettings){
    var grid_id = settingObj.grid_id;
    var pager = settingObj.pager;
    var subgridMultiselect;
    var subGridCustomButtons;
    var multiselect = settingObj.multiselect;
    var hiddenObjSubgrid ;
    if (multiselect!=true){
        multiselect = false;
    }
    if (isEmpty(editSettingObj)){
        editSettingObj= {};
    }

    if (isEmpty(hiddenSettingObj)){
        hiddenSettingObj= {};
    }
    if (isEmpty(postData)){
        postData= {};
    }
    if (subgrid!=true){
        subgrid = false;
        subGridSettings={};
    }
    else {
        if (isEmpty(subGridSettings)){
            subGridSettings= {};
        }
        else {
            subGridCustomButtons = subGridSettings.customButtons;
            subgridMultiselect = subGridSettings.multiselect;
            if (subgridMultiselect!=true){
                subgridMultiselect = false;
            }
            if (!isEmpty(subGridSettings.hiddenObj)){
                hiddenObjSubgrid= subGridSettings.hiddenObj;
            }
        }
    }
    
    $("#"+grid_id).jqGrid({
        url:'index.php/procurement/populateRFQ',
        datatype: 'json',
        mtype: 'POST',
        postData:postData,
        colNames:['Reference','Supplier','Estimated Value','Owner Id','Status','Raised By','Owner','Needed By Date','Approver','Supplier Id','Warehouse Id','Approver Id'],
        colModel :[ 
        {
            name:'reference', 
            index:'reference', 
            width:80, 
            align:'right',
            hidden:hiddenSettingObj.reference
            },

            {
            name:'supplier_name', 
            index:'supplier_name', 
            width:140, 
            align:'right',
            hidden:hiddenSettingObj.supplier_name
            },

            {
            name:'estimated_value', 
            index:'estimated_value', 
            width:100, 
            align:'right',
            editable:false,
            editoptions:{
                size:"20",
                maxlength:"30"
            },
            hidden:hiddenSettingObj.estimated_value
            },

            {
            name:'owner_id', 
            index:'owner_id', 
            width:140, 
            align:'right',
            editable:true,
            editoptions:{
                size:"20",
                maxlength:"30"
            },
            hidden:true
        },

        {
            name:'status', 
            index:'status', 
            width:60, 
            align:'right',
            editable:false,
            editoptions:{
                size:"20",
                maxlength:"30"
            },
            hidden:hiddenSettingObj.status
            },

            {
            name:'raised_by_name', 
            index:'raised_by_name',
            editable:false, 
            width:80, 
            align:'right',
            hidden:hiddenSettingObj.raised_by_name
            },

            {
            name:'owner_name', 
            index:'owner_name',
            editable:false, 
            width:80, 
            align:'right',
            hidden:hiddenSettingObj.owner_name
            },

            {
            name:'needed_by_date', 
            index:'needed_by_date',
            editable:false, 
            width:120, 
            sorttype:'date',
            hidden:hiddenSettingObj.needed_by_date
            },

            {
            name:'approved_by', 
            index:'approved_by',
            width:80, 
            align:'right',
            hidden:hiddenSettingObj.approved_by
            },

            {
            name:'supplier_id', 
            index:'supplier_id',
            editable:false, 
            hidden:'true'
        },

        {
            name:'warehouse_id', 
            index:'warehouse_id',
            editable:false, 
            hidden:'true'
        },

        {
            name:'approver_id', 
            index:'approver_id',
            editable:false, 
            hidden:'true'
        },
        //                        {name:'approval_notes', index:'approval_notes', width:200,hidden:hiddenSettingObj.approval_notes}
        ],
        pager: '#'+pager,
        rowNum:10,
        rowList:[5,10,20],
        sortname: 'id',
        sortorder: 'desc',
        viewrecords: true,
        gridview: true,
        multiselect:true,
                    
        ignoreCase:true,
        rownumbers:true,
        height:'auto',
        width:700,
        caption: 'Request For Quotations',
            
        jsonReader : {
            root:"quotedata",
            page: "page",
            total: "total",
            records: "records",
            cell: "dprow",
            id: "id"
        },
        subGrid:true,
        subGridRowExpanded: function(subgrid_id, row_id) {
            var subgrid_table_id, pager_id;
            subgrid_table_id = subgrid_id+"_t";
                            
            pager_id = "p_"+subgrid_table_id;
                            
            $("#"+subgrid_id).html("<table id='"+subgrid_table_id+"' class='scroll'></table><div id='"+pager_id+"' class='scroll'></div>");
            var subGridSettingObj = {
                grid_id:subgrid_table_id,
                pager:pager_id,
                multiselect:subgridMultiselect
            };
            prepareRFQItemsGrid(subGridSettingObj,{
                quoteId:row_id
            },{},hiddenObjSubgrid);
            //                            var rfq_status = myGrid.getCell(row_id,'status');
            //                            var del = false;
            //                            var buttons ={};
            //                            if (!empty(subGridCustomButtons) ){
            //                                buttons = subGridCustomButtons;
            //                            }
            //                            
            //                            // if status is open or draft then only add modify buttons
            //                            if (rfq_status=='open' || rfq_status=='draft'){
            //                                del = buttons.del;
            //                                buttons = {add:true,edit:true,bulk:true,data:{quote_id:$("#quoteSubgridId").val(),bulk_grid_id:'lineItemsbulk'}};
            //                            }
            //                            else {
            //                                buttons = {add:false,edit:true,bulk:true,data:{quote_id:$("#quoteSubgridId").val(),bulk_grid_id:'lineItemsbulk'}};
            //                            }
            $("#"+subgrid_table_id).jqGrid('navGrid',"#"+pager_id,{
                edit:false,
                add:false,
                del:false,
                search:false
            });
                             
        }
    });
}
function addCustomButtonsInRFQGrid(grid_id,pager_id,parent_grid,buttons,row_id){
    var permission = isAllowedBulk([{
        element:'approve_rfq_button',
        resource:'rfq',
        permission:'approve'
    },

    {
        element:'mark_approver_rfq_button',
        resource:'rfq',
        permission:'approve'
    },

    {
        element:'gen_quote_rfq_button',
        resource:'rfq',
        permission:'generatedirect'
    },

    {
        element:'manage_rfq_button',
        resource:'rfq',
        permission:'manage'
    },

    {
        element:'assign_rfq_button',
        resource:'rfq',
        permission:'assign'
    }]);
    
    // if (buttons.add==true && permission.add_rfq_button=="true"){
    if (buttons.add==true && permission.manage_rfq_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager_id,{
            caption:"", 
            title:"Create RFQ",
            buttonicon:"ui-icon-plus",
            id:"add_"+grid_id,
            onClickButton : function () { 
                $( "#dialog-form" ).dialog( "open" );
            } 
        });
    }
    //if (buttons.submit_approval==true && permission.submit_approval_rfq_button=="true"){
    if (buttons.submit_approval==true && permission.manage_rfq_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager_id,{
            caption:"", 
            title:"Submit for Approval",
            buttonicon:"ui-icon-flag",
            id:"submit_approve_"+grid_id,
            onClickButton : function () { 
                var rowid = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var noOfRows = rowid.length;
                var onlyOpen= true;
                var onlySelf = true;
               
                if (noOfRows == 0){
                    $( "#modal-warning-none" ).dialog("open");
                }
                else{
                    $.each(rowid, function(){
                         
                        if ($("#"+grid_id).getCell(this,'status')!='open'){
                            onlyOpen = false;
                            return false;
                        }
                    })
                    if (!onlyOpen){
                        $("#status_warning_text").text("Line Items With Status 'Open' Can Only Be Submitted For Approval");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                    else{
                        $.each(rowid, function(){

                            if ($("#"+grid_id).getCell(this,'owner_id')!=user_id){
                                onlySelf = false;
                                return false;
                            }
                        });
                        if (!onlySelf){
                            $("#status_warning_text").text("RFQs Owned By You Can Only Be Submitted For Approval");
                            $( "#modal-warning-general" ).dialog("open");
                        }
                        else {
                            $.ajax({
                                method:"POST",
                                url:"index.php/procurement/submitForApproval",
                                data: {
                                    ids : rowid,
                                    entity:'rfq'
                                },
                                success: function(){
                                    $("#"+grid_id).trigger("reloadGrid");
                                    emptyMessages()
                                    showSuccessMessage("Selected Quotations Are Submitted For Approval");
                                },
                                error:function(){

                                    emptyMessages()
                                    showErrorMessage("Selected Quotations Could Not Be Submitted For Approval Due To Inetrnal Error");
                                }
                            });
                        }
                    
                    }
                } 
            } 
        });
    }
    //if (buttons.del==true && permission.del_rfq_button=="true"){
    if (buttons.del==true && permission.manage_rfq_button=="true"){
        //$('#del_'+grid_id).show();
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager_id,{
            caption:"", 
            title:"Cancel",
            buttonicon:"ui-icon-trash",
            id:"del_"+grid_id,
            onClickButton : function () { 
                var rowid = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var noOfRows = rowid.length;
                var onlyOpen= true;
                var onlySelf= true;
               
                if (noOfRows == 0){
                    $( "#modal-warning-none" ).dialog("open");
                }
                else{
                    
                    $.each(rowid, function(){
                         
                        if ($("#"+grid_id).getCell(this,'status')!='open' && $("#"+grid_id).getCell(this,'status')!='draft'){
                            onlyOpen = false;
                            return false;
                        }
                    });
                    if (!onlyOpen){
                        $("#status_warning_text").text("Line Items With Status 'Open' Or 'Draft' Can Only Be Cancelled");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                    else{
                        $.each(rowid, function(){
                         
                            if ($("#"+grid_id).getCell(this,'owner_id')!=user_id){
                                onlySelf = false;
                                return false;
                            }
                        });
                        if (!onlySelf){
                            $("#status_warning_text").text("You Can Only Cancel RFQs Owned By You");
                            $( "#modal-warning-general" ).dialog("open");
                        }
                        
                        else  {
                            $( "#delete-quote-dialog" ).dialog("open") ;
                        }
                        
                    }
                } 
            } 
        });
    }
    if (buttons.edit==true && permission.manage_rfq_button=="true"){
        //if (buttons.edit==true && permission.edit_rfq_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager_id,{
            caption:"", 
            title:"Edit Quotation",
            buttonicon:"ui-icon-pencil",
            id:"edit_"+grid_id,
            onClickButton : function () { 

                var rowid = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var noOfRows = rowid.length;
                var onlyOpen = false;
                if (noOfRows != 1){
                    $("#status_warning_text").text("Please Select Exactly One Row");
                    $( "#modal-warning-general" ).dialog("open");
                }
                else{
                    var selrow = $("#"+grid_id).jqGrid('getGridParam', 'selrow');  
                    var status = $("#"+grid_id).getCell(selrow,'status');
                    var owner_id = $("#"+grid_id).getCell(selrow,'owner_id');
                    if (status=='open' || status=='draft'){
                        onlyOpen = true;
                        
                    }
                
                    if (!onlyOpen){
                        $("#status_warning_text").text("Quotations With Status 'Open' or 'Draft' Can Only Be Edited");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                    else{

                        if (user_id == owner_id){
                            $( "#edit-quote-dialog" ).dialog("open") ;

                        }
                        else {
                            $("#status_warning_text").text("You Can Only Modify RFQs Owned By You");
                            $( "#modal-warning-general" ).dialog("open");
                        }

                    }
                }                       
            } 
        });
    }   
    if (buttons.approve==true && permission.approve_rfq_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager_id,{
            caption:"", 
            title:"Approve RFQ",
            buttonicon:"ui-icon-check",
            id:"approve_"+grid_id,
            onClickButton : function () { 
                var selectedRows = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var row_id = $("#"+grid_id).jqGrid('getGridParam', 'selrow');
                var noOfRows = selectedRows.length;
                var onlyWFA= false;
                

                if (noOfRows == 0){
                    $( "#modal-warning-one" ).dialog("open");
                }
                else if (noOfRows == 1){
                    var selrow = $("#"+grid_id).jqGrid('getGridParam', 'selrow');  
                    var status = $("#"+grid_id).getCell(selrow,'status');
                    var approver_id = $("#"+grid_id).getCell(selrow,'approver_id');
                    if (status=='waitingforapproval' ){
                        onlyWFA = true;

                    }
                    if (!onlyWFA){
                        $("#status_warning_text").text("RFQs With Status 'Waiting For Approval ' Can Only Be Approved");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                    else{
                        
                        if (user_id == approver_id){
                            $( "#dialog-form" ).dialog( "open" );

                        }
                        else {
                            $("#status_warning_text").text("You Need to Be Approver For This RFQ To Approve.Please Mark Yourself As Approver For This RFQ");
                            $( "#modal-warning-general" ).dialog("open"); 
                        }
                        
                    }
                }
                else {
                    $( "#modal-warning-morethanone" ).dialog("open") ;
                }
            } 
        });
    }
    if (buttons.approve_bulk==true  && permission.approve_rfq_button=="true"){
        //if (buttons.approve_bulk==true  && permission.approve_bulk_rfq_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager_id,{
            caption:"", 
            title:"Approve RFQ In Bulk",
            buttonicon:"ui-icon-circle-check",
            id:"approve_bulk_"+grid_id,
            onClickButton : function () { 
                var rowid = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var selectedRows = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var noOfRows = selectedRows.length;
                var onlywfa= true;
                var onlySelf= true;
                if (noOfRows == 0){
                    $( "#modal-warning-none" ).dialog("open");
                }
                else{
                    $.each(selectedRows, function(){
                         
                        if ($("#"+grid_id).getCell(this,'status')!='waitingforapproval'){
                            onlywfa = false;
                            return false;
                        }
                    })
                    if (!onlywfa){
                        $("#status_warning_text").text("RFQs With Status 'Waiting For Approval ' Can Only Be Approved");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                    else {
                        $.each(rowid, function(){

                            if ($("#"+grid_id).getCell(this,'approver_id')!=user_id){
                                onlySelf = false;
                                return false;
                            }
                        });
                        if (!onlySelf){
                            $("#status_warning_text").text("You Need to Be Approver For *ALL* Selected RFQs .Please Mark Yourself As Approver For *ALL*  Selected RFQs");
                            $( "#modal-warning-general" ).dialog("open");
                        }
                        else {
                            $.ajax({
                                method:"POST",
                                url:"index.php/procurement/approveQuotesInBulk",
                                data: {
                                    ids : rowid
                                },
                                success: function(){
                                    $("#"+grid_id).trigger("reloadGrid");
                                    showSuccessMessage("success");
                                }
                            });
                        }
                                
                    }
                }
                        
            } 
        });
    }
    //if (buttons.reopen==true && permission.reopen_rfq_button=="true"){
    if (buttons.reopen==true && permission.manage_rfq_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager_id,{
            caption:"", 
            title:"Reopen",
            buttonicon:"ui-icon-folder-open",
            id:"reopen_"+grid_id,
            onClickButton : function () { 
                var rowid = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var selectedRows = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var noOfRows = selectedRows.length;
                var onlyRejected= true;
                var onlySelf = true;
                if (noOfRows == 0){
                    $( "#modal-warning-none" ).dialog("open");
                }
                else{
                    $.each(selectedRows, function(){
                         
                        if ($("#"+grid_id).getCell(this,'status')!='rejected'){
                            onlyRejected = false;
                            return false;
                        }
                    })
                    if (!onlyRejected){
                        $("#status_warning_text").text("Line Items With Status 'Rejected' Can  Be Reopened");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                    else {
                        $.each(rowid, function(){
                         
                            if ($("#"+grid_id).getCell(this,'owner_id')!=user_id){
                                onlySelf = false;
                                return false;
                            }
                        });
                        if (!onlySelf){
                            $("#status_warning_text").text("You Can Only Reopen RFQs Owned By You");
                            $( "#modal-warning-general" ).dialog("open");
                        }
                        else{
                            $.ajax({
                                method:"POST",
                                url:"index.php/procurement/reopen",
                                data: {
                                    ids : rowid,
                                    entity:'rfq'
                                },
                                success: function(){
                                    $("#"+grid_id).trigger("reloadGrid");
                                    showSuccessMessage("Reopened");
                                }
                            });
                        }
                    }
                }
            } 
        });
    }
    
    if (buttons.gen_quote==true && permission.gen_quote_rfq_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager_id,{
            caption:"", 
            title:"Generate Quote Without Approval",
            buttonicon:"ui-icon-tag",
            id:"gen_quote_"+grid_id,
            onClickButton : function () { 
                var rowid = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var selectedRows = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var noOfRows = selectedRows.length;
                var onlyOpen= true;
                var onlySelf= true;
                
                if (noOfRows == 0){
                    $( "#modal-warning-none" ).dialog("open");
                }
                else{
                    $.each(selectedRows, function(){
                         
                        if ($("#"+grid_id).getCell(this,'status')!='open'){
                            onlyOpen = false;
                            return false;
                        }
                    })
                    if (!onlyOpen){
                        $("#status_warning_text").text("RFQs With Status 'Open' Can Only  Be Converted To Quote");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                    else {
                        $.each(rowid, function(){
                         
                            if ($("#"+grid_id).getCell(this,'owner_id')!=user_id){
                                onlySelf = false;
                                return false;
                            }
                        });
                        if (!onlySelf){
                            $("#status_warning_text").text("You Can Only Generate Quote From RFQs Owned By You");
                            $( "#modal-warning-general" ).dialog("open");
                        }
                        else{
                            $.ajax({
                                method:"POST",
                                url:"index.php/procurement/generateQuoteFromRFQ",
                                data: {
                                    ids : rowid
                                },
                                success: function(){
                                    $("#"+grid_id).trigger("reloadGrid");
                                    showSuccessMessage("Selected RFQ(s) Succesfully Converted To Quote(s)");
                                }
                            });
                        }
                        
                    }
                }
            } 
        });
    }
    if (buttons.comments == true /*&& permission.comments_button=="true"*/)  {
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager_id,{
            caption:"", 
            title:"Show Comments",
            buttonicon:"ui-icon-script",
            id:"comments_"+grid_id,
            onClickButton : function () { 

                var rowid = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var noOfRows = rowid.length;
                //var onlyOpen = false;
                if (noOfRows != 1){
                    $( "#modal-warning-exactly-one" ).dialog("open");
                }
                else{
                    var rfq_id = $("#"+grid_id).jqGrid('getGridParam', 'selrow');
                    $.ajax({
                        method:"POST",
                        url:"index.php/procurement/getCommentsForEntity",
                        data: {
                            entity:'rfq',
                            id : rfq_id
                        },
                        success: function(response){
                            var resObj = JSON.parse(response);
                            console.log(resObj);
                            $( "#comment-quote-dialog" ).data('grid_data',resObj).dialog("open") ;
                        //                                $("#"+grid_id).trigger("reloadGrid");
                        //                                showSuccessMessage("Selected RFQ(s) Succesfully Converted To Quote(s)");
                        }
                    });
                //$( "#comment-quote-dialog" ).dialog("open") ;
                }                       
            } 
        });
    } 
    if (buttons.mark_approver==true  && permission.mark_approver_rfq_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager_id,{
            caption:"", 
            title:"Mark Yourself As Approver",
            buttonicon:"ui-icon-star",
            id:"mark_approver_"+grid_id,
            onClickButton : function () { 
                var rowid = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var selectedRows = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var noOfRows = selectedRows.length;
                var onlywfa= true;
                var onlyEmpty= true;
                if (noOfRows == 0){
                    $( "#modal-warning-none" ).dialog("open");
                }
                else{
                    $.each(selectedRows, function(){
                         
                        if ($("#"+grid_id).getCell(this,'status')!='waitingforapproval'){
                            onlywfa = false;
                            return false;
                        }
                    })
                    if (!onlywfa){
                        $("#status_warning_text").text("RFQs With Status 'Waiting For Approval ' Can Only Be Permitted For Marking");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                    else {
                        $.each(rowid, function(){

                            if (!isEmpty($("#"+grid_id).getCell(this,'approver_id'))){
                                onlyEmpty = false;
                                return false;
                            }
                        });
                        if (!onlyEmpty){
                            $("#status_warning_text").text("Selected RFQ(s) already has approver. Please Select Records Without Any Approver");
                            $( "#modal-warning-general" ).dialog("open");
                        }
                        else {
                            $.ajax({
                                method:"POST",
                                url:"index.php/procurement/assignEntityToUser",
                                data: {
                                    ids : rowid,
                                    entity:'rfq',
                                    user_id:user_id,
                                    role:'approver'
                                },
                                success: function(){
                                    $("#"+grid_id).trigger("reloadGrid");
                                    emptyMessages();
                                    showSuccessMessage("success");
                                }
                            });
                        }
                                
                    }
                }
                        
            } 
        });
    }
    if (buttons.assign==true  && permission.assign_rfq_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager_id,{
            caption:"", 
            title:"Assign",
            buttonicon:"ui-icon-transferthick-e-w",
            id:"assign_"+grid_id,
            onClickButton : function () { 
                var rowid = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var noOfRows = rowid.length;
                var onlyOpen = false;
                if (noOfRows == 0){
                    $( "#modal-warning-none" ).dialog("open");
                }
                else{
                    var selrow = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');  
                    var approver_name = $("#"+grid_id).getCell(selrow,'approved_by');
                    var owner_name = $("#"+grid_id).getCell(selrow,'owner_name');
                    console.log(owner_name);
                    if (isEmpty(approver_name)){
                        approver_name ='None';
                    }
                    var grid_data = {
                        entity:'rfq',
                        current_owner:owner_name,
                        current_approver:approver_name,
                        row_id:selrow,
                        grid_id:grid_id
                    };
                    $( "#assignment-common-dialog" ).data('grid_data',grid_data).dialog( "open" );
                }
                
            } 
        });
    }
    if (buttons.load_owner_all==true){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager_id,{
            caption:"", 
            title:"Reload w/ all User Records",
            buttonicon:"ui-icon-arrow-4",
            id:"reload_user_"+grid_id,
            onClickButton : function () { 
                var postData = $("#"+grid_id).getGridParam('postData');
                postData.owner_id = 'all';
                $("#"+grid_id).setGridParam('postData',postData);
                $("#"+grid_id).trigger("reloadGrid");
            } 
        });
    }
    
    if (buttons.load_approver_all==true){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager_id,{
            caption:"", 
            title:"Reload w/ all/No Approvers ",
            buttonicon:"ui-icon-arrow-4",
            id:"reload_approver_"+grid_id,
            onClickButton : function () { 
                var postData = $("#"+grid_id).getGridParam('postData');
                postData.approved_by = 'all';
                $("#"+grid_id).setGridParam('postData',postData);
                $("#"+grid_id).trigger("reloadGrid");
            } 
        });
    }
     
                
}

function prepareRFQItemsGrid(settingObj,postData,editSettingObj,hiddenSettingObj){
    var grid_id = settingObj.grid_id;
    var pager = settingObj.pager;
    var width = '450';
    
    if (!isEmpty(settingObj.width)){
        width = settingObj.width;
    }
    
    var multiselect = settingObj.multiselect;
    if (multiselect!=true){
        multiselect = false;
    }
    if (isEmpty(editSettingObj)){
        editSettingObj= {};
    }

    if (isEmpty(hiddenSettingObj)){
        hiddenSettingObj= {};
    }
    if (isEmpty(postData)){
        postData= {};
    }
   
    $("#"+grid_id).jqGrid({
        url:'index.php/procurement/populateRFQItems',
        datatype: 'json',
        mtype: 'POST',
        postData:postData,
        colNames:['Product','Quantity','Need By Date','Unit Price','Estimated Value'/*,'Notes'*/],
        colModel :[ 
        {
            name:'name', 
            index:'name',
            editable:false, 
            width:120, 
            align:'right'
        },

        {
            name:'quoted_quantity', 
            index:'quoted_quantity', 
            editable:false,
            width:50, 
            align:'right'
        },

        {
            name:'needed_by_date',
            index:'needed_by_date',
            editable:false, 
            width:80, 
            align:'right'
        },

        {
            name:'expected_price',
            index:'expected_price',
            editable:false, 
            width:50, 
            align:'right'
        },

        {
            name:'estimated_value', 
            index:'estimated_value',
            editable:false, 
            width:60, 
            align:'right'
        }//,
        //                        {name:'comments', index:'comments',editable:false, width:180, align:'right'}

        ],
        rowNum:20,
        pager: '#'+pager,
        sortname: 'id',
        sortorder: "asc",
        height: '100%',
        width:width,
        multiselect:multiselect,
        caption: 'Items For Quotations',
        jsonReader : {
            root:"quoteitemdata",
            page: "page",
            total: "total",
            records: "records",
            cell: "dprow",
            id: "id"
        }
    });
}
function addCustomButtonsInRFQItemGrid(settingObj,buttons){
    var grid_id = settingObj.grid_id;
    var pager_id = settingObj.pager;
    if (isEmpty(buttons)){
        buttons={};
    }
    var permission = isAllowedBulk([{
        element:'approve_rfq_button',
        resource:'rfq',
        permission:'approve'
    },

    {
        element:'mark_approver_rfq_button',
        resource:'rfq',
        permission:'approve'
    },

    {
        element:'gen_quote_rfq_button',
        resource:'rfq',
        permission:'generatedirect'
    },

    {
        element:'manage_rfq_button',
        resource:'rfq',
        permission:'manage'
    },

    {
        element:'assign_rfq_button',
        resource:'rfq',
        permission:'assign'
    }]);
    var owner_id = settingObj.owner_id;
    var status = settingObj.status;
    
    if (buttons.add==true && permission.manage_rfq_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager_id,{
            caption:"", 
            title:"Add Item Detailed",
            buttonicon:"ui-icon-plus",
            id:"add_"+grid_id,
            onClickButton : function () { 
                //need to pass grid id for dynamic reload;
                var onlyOpen = false;
                if (status=='open' || status=='draft'){
                    onlyOpen = true;

                }

                if (!onlyOpen){
                    $("#status_warning_text").text("Quotations With Status 'Open' or 'Draft' Can Only Be Edited");
                    $( "#modal-warning-general" ).dialog("open");
                }
                else{
                    if (user_id == owner_id){
                       var gridData ={
                            'grid_id':grid_id,
                            'quote_id':buttons.data.quote_id
                            }
                       $( "#dialog-form-item" ).data('grid_data',gridData).dialog( "open" );
                    
                       
                    }
                    else{
                        $("#status_warning_text").text("You Can Only Modify RFQs Owned By You");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                }     
            } 
        });
    }
    if (buttons.edit==true && permission.manage_rfq_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager_id,{
            caption:"", 
            title:"Edit Item Request",
            buttonicon:"ui-icon-pencil",
            id:"edit_"+grid_id,
            onClickButton : function () { 
                //need to pass grid id for dynamic reload;
                var rowid = $("#"+grid_id).getGridParam('selrow');
                var onlyOpen=false;
                if (rowid !=null && rowid!=undefined){
                    if (status=='open' || status=='draft'){
                        onlyOpen = true;
                        
                    }
                
                    if (!onlyOpen){
                        $("#status_warning_text").text("Quotations With Status 'Open' or 'Draft' Can Only Be Edited");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                    else{
                        if (user_id == owner_id){
                            var gridData ={
                                'grid_id':grid_id,
                                'quote_id':buttons.data.quote_id,
                                'action':'edit',
                                'line_id':rowid
                            };

                            $( "#dialog-form-item" ).data('grid_data',gridData).dialog( "open" );
                        }
                        else{
                            $("#status_warning_text").text("You Can Only Modify Quotes Owned By You");
                            $( "#modal-warning-general" ).dialog("open");
                        }
                    }
                    
                    
                    
                }
                else{
                    $( "#modal-warning-none" ).dialog('open');
                }

            } 
        });
    }
    
    if (buttons.bulk==true){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager_id,{
            caption:"", 
            title:"Add Items In Bulk",
            buttonicon:"ui-icon-suitcase",
            id:"add_bulk_"+grid_id,
            onClickButton : function () { 
                //need to pass grid id for dynamic reload;
                
                var rowid = $("#"+grid_id).getGridParam('selrow');
                var onlyOpen=false;
                if (rowid !=null && rowid!=undefined){
                    if (status=='open' || status=='draft'){
                        onlyOpen = true;
                        
                    }
                
                    if (!onlyOpen){
                        $("#status_warning_text").text("Quotations With Status 'Open' or 'Draft' Can Only Be Edited");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                    else{
                        if (user_id == owner_id){
                            var gridData ={
                            'grid_id':buttons.data.bulk_grid_id,
                            'quote_id':buttons.data.quote_id
                            }
                        $( "#dialog-form-bulk" ).data('grid_data',gridData).dialog( "open" );
                        }
                        else{
                            $("#status_warning_text").text("You Can Only Modify Quotes Owned By You");
                            $( "#modal-warning-general" ).dialog("open");
                        }
                    }
                    
                    
                    
                }
                else{
                    $( "#modal-warning-none" ).dialog('open');
                }
            } 
        });
    }
    
    
                
}

function prepareInvoicesGrid(settingObj,postData,subgrid,editSettingObj,hiddenSettingObj,subGridSettings){

    var grid_id = settingObj.grid_id;
    var pager = settingObj.pager;
    var subgridMultiselect;
    var subGridCustomButtons;
    var multiselect = settingObj.multiselect;
    var hiddenObjSubgrid ;
    var width = '680';
    if (!isEmpty(settingObj.width)){
        width = settingObj.width;
    }
    if (multiselect!=true){
        multiselect = false;
    }
    if (isEmpty(editSettingObj)){
        editSettingObj= {};
    }

    if (isEmpty(hiddenSettingObj)){
        hiddenSettingObj= {};
    }
    if (isEmpty(postData)){
        postData= {};
    }
    if (subgrid!=true){
        subgrid = false;
        subGridSettings={};
    }
    else {
        if (isEmpty(subGridSettings)){
            subGridSettings= {};
        }
        else {
            subGridCustomButtons = subGridSettings.customButtons;
            subgridMultiselect = subGridSettings.multiselect;
            if (subgridMultiselect!=true){
                subgridMultiselect = false;
            }
            if (!isEmpty(subGridSettings.hiddenObj)){
                hiddenObjSubgrid= subGridSettings.hiddenObj;
            }
        }
    }
    
    
    $("#"+grid_id).jqGrid({
        url:'index.php/procurement/populateInvoicesToPay',
        datatype: 'json',
        mtype: 'POST',
        postData:postData,
        colNames:['Invoice Reference','Payment Process','Total Value','Amount Paid','Status','Payee Id','Payee','Owner Id','Owner','Order Id','Order Ref'],
        colModel :[ 
        {
            name:'reference', 
            index:'reference', 
            width:120, 
            align:'right',
            editable:false,
            formatter:invFormatter
        },

        {
            name:'payment_process_type', 
            index:'payment_process_type', 
            width:120, 
            align:'right'
        },

        {
            name:'total_value', 
            index:'total_value',
            editable:false,
            align:'right',
            width:100
        },

        {
            name:'amount_paid', 
            index:'amount_paid',
            editable:false,
            align:'right',
            width:120
        },

        {
            name:'status', 
            index:'status',
            editable:false,
            align:'right'
        },

        {
            name:'payer_id', 
            index:'payer_id',
            hidden:true
        },

        {
            name:'payer_name', 
            index:'payer_name', 
            width:70, 
            align:'right',
            editable:false
        },

        {
            name:'owner_id', 
            index:'owner_id',
            hidden:true
        },

        {
            name:'owner_name', 
            index:'owner_name', 
            width:70, 
            align:'right',
            editable:false
        },

        {
            name:'order_id', 
            index:'order_id',
            hidden:true
        },

        {
            name:'order_reference', 
            index:'order_reference',
            editable:false, 
            width:100, 
            align:'right',
            formatter:poFormatter
        }
                        
        ],
        pager: '#pager',
        rowNum:10,
        rowList:[5,10,20],
        sortname: 'id',
        sortorder: 'desc',
        viewrecords: true,
        gridview: true,
        multiselect:multiselect,
                    
        ignoreCase:true,
        rownumbers:true,
        height:'auto',
        width:width,
        caption: 'Invoices',
        //                    onSelectRow: function (){
        //                        $( "#dialog-form" ).dialog("open");
        //                    },
        //            
        jsonReader : {
            root:"quotedata",
            page: "page",
            total: "total",
            records: "records",
            cell: "dprow",
            id: "id"
        },
                    
        subGrid:true,
        subGridRowExpanded: function(subgrid_id, row_id) {
            var subgrid_table_id, pager_id;
            subgrid_table_id = subgrid_id+"_t";
                            
            pager_id = "p_"+subgrid_table_id;
                            
            $("#"+subgrid_id).html("<table id='"+subgrid_table_id+"' class='scroll'></table><div id='"+pager_id+"' class='scroll'></div>");
            jQuery("#"+subgrid_table_id).jqGrid({
                url:'index.php/procurement/populateInvoiceItems?invoiceId='+row_id,
                datatype: 'json',
                colNames:['Product Id','Product','Invoiced Quantity','Invoiced Value'/*,'Owner','Status','Order Id','Order Ref','Quote Ref','Owner','Needed By Date'*/],
                colModel :[ 
                //                                            {name:'reference', index:'reference', width:80, align:'right',editable:false},
                {
                    name:'product_id', 
                    index:'product_id',
                    editable:false,
                    align:'right',
                    hidden:true
                },

                {
                    name:'name', 
                    index:'name', 
                    width:140, 
                    align:'right',
                    editable:false
                },

                //                        {name:'estimated_value', index:'estimated_value', width:100, align:'right',editable:false,editoptions:{size:"20",maxlength:"30"}},

                //                        {name:'owner', index:'owner', width:140, align:'right',editable:true,editoptions:{size:"20",maxlength:"30"}},

                {
                    name:'invoiced_quantiy', 
                    index:'invoiced_quantiy', 
                    width:100, 
                    align:'right',
                    editable:false,
                    editoptions:{
                        size:"20",
                        maxlength:"30"
                    }
                },

                {
                name:'total_invoiced_value', 
                index:'total_invoiced_value',
                editable:false,
                align:'right'
            }
                                            
            ],
            rowNum:20,
            pager: pager_id,
            sortname: 'id',
            sortorder: "asc",
            height: '100%',
                                   
                                    
            jsonReader : {
                root:"invoiceitemdata",
                page: "page",
                total: "total",
                records: "records",
                cell: "dprow",
                id: "id"
            }
            });
        jQuery("#"+subgrid_table_id).jqGrid('navGrid',"#"+pager_id,{
            edit:false,
            add:false,
            del:false,
            search:false,
            view:true
        });
                             
    }
    });

        

}
function addCustomButtonsInVoiceGrid(settingObj,buttons) { 
    var grid_id = settingObj.grid_id;
    var pager = settingObj.pager;
    var permission = isAllowedBulk([
    {
        element:'mark_payer_invoice_button',
        resource:'invoice',
        permission:'approve'
    },

    {
        element:'manage_invoice_button',
        resource:'invoice',
        permission:'manage'
    },

    {
        element:'assign_invoice_button',
        resource:'invoice',
        permission:'assign'
    }]);
    if (buttons.manage==true  && permission.manage_invoice_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
            caption:"", 
            title:"Pay Invoices",
            buttonicon:"ui-icon-pencil",
            id:"pay_"+grid_id,
            onClickButton : function () { 
                var selectedRows = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var noOfRows = selectedRows.length;
                var onlyOpen = false;
                if (noOfRows != 1){
                    $( "#modal-warning-exactly-one" ).dialog("open");
                }
                else {
                    var row_id = $("#"+grid_id).jqGrid('getGridParam', 'selrow');
                   
                    var status = $("#"+grid_id).getCell(row_id,'status');
                    var payer_id = $("#"+grid_id).getCell(row_id,'payer_id');
                    if (status=='pending' || status=='partiallypaid'){
                        onlyOpen = true;
                    }
                    if (!onlyOpen){
                        $("#status_warning_text").text("Please Select Invoice With Status 'Pending' or 'partiallypaid' ");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                    else {
                        if (user_id == payer_id){
                            //                             var grid_data={quote_id:row_id,mode:'edit'}
                            $( "#dialog-form" ).dialog( "open" );

                        }
                        else {
                           
                            $("#status_warning_text").text("You Need To Be Payer For This Invoice ");
                            $( "#modal-warning-general" ).dialog("open");
                        }
                        
                    }
                }
               
                
            } 
        });
    }
    if (buttons.mark_payer==true  && permission.mark_payer_invoice_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
            caption:"", 
            title:"Mark Yourself As Payer",
            buttonicon:"ui-icon-star",
            id:"mark_receiver_"+grid_id,
            onClickButton : function () { 
                var rowid = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var selectedRows = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var noOfRows = selectedRows.length;
                var onlyOpen= true;
                var onlyEmpty= true;
                if (noOfRows == 0){
                    $( "#modal-warning-none" ).dialog("open");
                }
                else{
                    $.each(selectedRows, function(){
                         
                        if ($("#"+grid_id).getCell(this,'status')!='pending'){
                            onlyOpen = false;
                            return false;
                        }
                    })
                    if (!onlyOpen){
                        $("#status_warning_text").text("'Pending ' Invoices Are Only Be Permitted For Marking");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                    else {
                        $.each(rowid, function(){

                            if (!isEmpty($("#"+grid_id).getCell(this,'payer_id'))){
                                onlyEmpty = false;
                                return false;
                            }
                        });
                        if (!onlyEmpty){
                            $("#status_warning_text").text("Some/All Selected Invoice(s) already have receiver. Please Select Records Without Any Receiver");
                            $( "#modal-warning-general" ).dialog("open");
                        }
                        else {
                            $.ajax({
                                method:"POST",
                                url:"index.php/procurement/assignEntityToUser",
                                data: {
                                    ids : rowid,
                                    entity:'invoice',
                                    user_id:user_id,
                                    role:'payer'
                                },
                                success: function(){
                                    $("#"+grid_id).trigger("reloadGrid");
                                    emptyMessages();
                                    showSuccessMessage("success");
                                }
                            });
                        }
                                
                    }
                }
                        
            } 
        });
    }
    if (buttons.assign==true && permission.assign_invoice_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
            caption:"", 
            title:"Assign",
            buttonicon:"ui-icon-transferthick-e-w",
            id:"assign_"+grid_id,
            onClickButton : function () { 
                var rowid = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var noOfRows = rowid.length;
                var onlyOpen = true;
                if (noOfRows == 0){
                    $( "#modal-warning-none" ).dialog("open");
                }
                else{
                    var selrow = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');  
                    var payer_name = $("#"+grid_id).getCell(selrow,'payer_name');
                    var owner_name = $("#"+grid_id).getCell(selrow,'owner_name');
                    $.each(selrow, function(){
                         
                        if ($("#"+grid_id).getCell(this,'status')!='pending' && $("#"+grid_id).getCell(this,'status')!='partiallypaid' ){
                            onlyOpen = false;
                            return false;
                        }
                    });
                    if (!onlyOpen){
                        $("#status_warning_text").text(" Invoices With 'Pending'/'Partially Paid'Can Only Be Permitted For Assignment");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                    else {
                        console.log(owner_name);
                        if (isEmpty(payer_name)){
                            payer_name ='None';
                        }
                        var grid_data = {
                            entity:'invoice',
                            current_owner:owner_name,
                            current_payer:payer_name,
                            row_id:selrow,
                            grid_id:grid_id
                        };
                        $( "#assignment-common-dialog" ).data('grid_data',grid_data).dialog( "open" );
                    }
                    
                }
                
            } 
        });
    }
    if (buttons.load_owner_all==true){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
            caption:"", 
            title:"Reload w/ all User Records",
            buttonicon:"ui-icon-arrow-4",
            id:"reload_user_"+grid_id,
            onClickButton : function () { 
                var postData = $("#"+grid_id).getGridParam('postData');
                postData.owner_id = 'all';
                $("#"+grid_id).setGridParam('postData',postData);
                $("#"+grid_id).trigger("reloadGrid");
            } 
        });
    }
    if (buttons.load_status_all==true){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
            caption:"", 
            title:"Reload w/ all Status For This Owner",
            buttonicon:"ui-icon-arrowrefresh-1-s",
            id:"reload_status_"+grid_id,
            onClickButton : function () { 
                var postData = $("#"+grid_id).getGridParam('postData');
                postData._status = 'all';
                $("#"+grid_id).setGridParam('postData',postData);
                $("#"+grid_id).trigger("reloadGrid");
            } 
        });
    }
    
                
}

function prepareProductsGrid(settingObj,postData,subgrid,editSettingObj,hiddenSettingObj,subGridSettings){

    var grid_id = settingObj.grid_id;
    var pager = settingObj.pager;
    var subgridMultiselect;
    var subGridCustomButtons;
    var multiselect = settingObj.multiselect;
    var hiddenObjSubgrid ;
    var width = '680';
    if (!isEmpty(settingObj.width)){
        width = settingObj.width;
    }
    if (multiselect!=true){
        multiselect = false;
    }
    if (isEmpty(editSettingObj)){
        editSettingObj= {};
    }

    if (isEmpty(hiddenSettingObj)){
        hiddenSettingObj= {uomdenom:true,meta_desc:true,attributeset:true};
    }
    if (isEmpty(hiddenSettingObj.uomdenom)){
        hiddenSettingObj.uomdenom = true;
    }
    if (isEmpty(hiddenSettingObj.meta_desc)){
        hiddenSettingObj.meta_desc = true;
    }
    if (isEmpty(hiddenSettingObj.attributeset)){
        hiddenSettingObj.attributeset = true;
    }
    if (isEmpty(postData)){
        postData= {};
    }
    if (subgrid!=true){
        subgrid = false;
        subGridSettings={};
    }
    else {
        if (isEmpty(subGridSettings)){
            subGridSettings= {};
        }
        else {
            subGridCustomButtons = subGridSettings.customButtons;
            subgridMultiselect = subGridSettings.multiselect;
            if (subgridMultiselect!=true){
                subgridMultiselect = false;
            }
            if (!isEmpty(subGridSettings.hiddenObj)){
                hiddenObjSubgrid= subGridSettings.hiddenObj;
            }
        }
    }
    
    
    $("#"+grid_id).jqGrid({
        url:'index.php/products/populateProductsInGrid',
                datatype: 'json',
                mtype: 'POST',
                pastData:postData,
                colNames:['Barcode',/*'System Name',*/'Product','Description','manufacturer_id','model_id','Manufacturer','Model',/*'Supplier',*/'package_id','Package Type',/*'Category',*/'Unit','Size/Quantity','Atrribute Set','Reorder','Vat','Margin Type','Margin Value','MetaDesc',/*'Is Active',*/'Action','Measurement'],
                colModel :[ 
                    //{name:'id', index:'id', width:55}, 
                    {name:'barcode',index:'barcode',width:120,align:'right',editable:false}, 
            //                        {name:'system_name',index:'system_name',width:90,align:'right',editable:false}, 
                    {name:'product_name', index:'product_name', width:80, align:'right',editable:false},
                    {name:'description', index:'description', width:80, align:'right',editable:false,hidden:hiddenSettingObj.description},
                    {name:'manufacturer_id', index:'manufacturer_id', hidden:true},
                    {name:'model_id', index:'model_id', hidden:true},
                    {name:'manufacturer', index:'manufacturer', width:80, align:'right',editable:false,hidden:hiddenSettingObj.manufacturer},
              
                    {name:'model', index:'model', width:80, align:'right',editable:false,hidden:hiddenSettingObj.model},
//                    {name:'supplier', index:'supplier', width:80, align:'right',editable:true,editoptions:{size:"20",maxlength:"30"}},
                    {name:'package_id', index:'package_id', hidden:true},
                    {name:'package_name', index:'package_name', width:80, align:'right',editable:false,hidden:hiddenSettingObj.package_name},
            //                        {name:'category', index:'category', width:100, align:'right',editable:false,editoptions:{size:"20",maxlength:"30"}},
                    {name:'uom', index:'uom', width:40, align:'right',editable:false,hidden:hiddenSettingObj.uom},
                    {name:'measurement_denomination', index:'measurement_denomination', width:60, align:'right',editable:false ,hidden:hiddenSettingObj.measurement_denomination},
                    {name:'attributeset', index:'attributeset', width:40, align:'right',search:false,editable:false,hidden:hiddenSettingObj.attributeset},
                    {name:'reorder', index:'reorder', width:40, align:'right',search:false,editable:false,hidden:hiddenSettingObj.reorder},
                    
                    {name:'vat', index:'vat', width:40, align:'right',search:false,editable:false,hidden:hiddenSettingObj.vat},
                    {name:'margin_type', index:'margin_type', width:40, align:'right',search:false,editable:false,hidden:hiddenSettingObj.margin_type},
                    {name:'margin_value', index:'margin_value', width:40, align:'right',search:false,editable:false,hidden:hiddenSettingObj.margin_value},
                    {name:'meta_desc', index:'meta_desc', width:40, align:'right',search:false,editable:false,hidden:hiddenSettingObj.meta_desc},
                    //{name:'isactive', index:'isactive', width:30, align:'right',editable:false,hidden:true},//,edittype:"select", formatter:'select', editoptions:{value:"1:Yes;0:No"}},
                    {name:'generate', index:'generate', width:80, align:'right',editable:false,hidden:hiddenSettingObj.generate,search:false,formatter:'showlink', formatoptions:{baseLinkUrl:'index.php/products/printBarcode'},cellattr: function (rowId, val, rawObject, cm, rdata) 
                    {     
                        //console.log(rawObject[0]);
                        return 'title="'  + rawObject[0]+'"';     
                    }},
                    {name:'uomdenom', index:'uomdenom', width:40, align:'right',editable:false,hidden:hiddenSettingObj.uomdenom}
//                     

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
                caption: 'Products',
                multiselect:true,
                height: '100%',
                width:'90%',
                jsonReader : {
                    root:"productdata",
                    page: "page",
                    total: "total",
                    records: "records",
                    cell: "dprow",
                    id: "id"
                },
//                onSelectRow: function(id){
//                    lastsel2 = id;
//                    selectedMfrId = myGrid.jqGrid("getCell",id,"manufacturer_id");
//                    selectedModelId = myGrid.jqGrid("getCell",id,"model_id");
//                    selectedPkgId =  myGrid.jqGrid("getCell",id,"package_id");
//                    selectUom = myGrid.jqGrid("getCell",id,"uom");
//                    selectedDenom =  myGrid.jqGrid("getCell",id,"measurement_denomination");
//                 
//                },
                editurl:'index.php/products/editProduct'
                


            });

        

}
function addCustomButtonsInProductsGrid(settingObj,buttons) { 
    var grid_id = settingObj.grid_id;
    var pager = settingObj.pager;
    var permission = isAllowedBulk([
    
    {
        element:'manage_product_button',
        resource:'product',
        permission:'manage'
    }]);
    if (buttons.add==true && permission.manage_product_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
            caption:"", 
            title:"Create Product",
            buttonicon:"ui-icon-plus",
            id:"add_"+grid_id,
            onClickButton : function () { 
                $("#newModelCtnr").parent().css("display","none");
                $("#modelCtnr").parent().css("display","none");
                var grid_data = {mode:'add'};
                $( "#dialog-form" ).data('grid_data',grid_data).dialog( "open" );
                

            } 
        });
    }
   
    if (buttons.edit==true  && permission.manage_product_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
            caption:"", 
            title:"Edit Quotation",
            buttonicon:"ui-icon-pencil",
            id:"edit_"+grid_id,
            onClickButton : function () { 
                var selectedRows = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var noOfRows = selectedRows.length;
                var onlyOpen = false;
                if (noOfRows != 1){
                    $( "#modal-warning-exactly-one" ).dialog("open");
                }
                else {
                    var row_id = $("#"+grid_id).jqGrid('getGridParam', 'selrow');
                    var status = $("#"+grid_id).getCell(row_id,'isactive');
                    
                    
//                    if (status){
//                        onlyOpen = true;
//                    }
//                    if (!onlyOpen){
//                        $("#status_warning_text").text("Active Products Can Only Be Edited");
//                        $( "#modal-warning-general" ).dialog("open");
//                    }
//                    else {
                        $("#newModelCtnr").parent().css("display","none");
                        $("#modelCtnr").parent().css("display","none");
                        var grid_data = {product_id: $("#"+grid_id).getGridParam('selrow'),mode:'edit'};
                        $( "#dialog-form" ).data('grid_data',grid_data).dialog( "open" );
                
                        
                    //}
                }
               
                
            } 
        });
    }
    if (buttons.deactivate==true && permission.manage_product_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager ,{
            caption:"",
            title:"Mark as inactive",
            id:"inactive_"+ grid_id,
            buttonicon:"ui-icon-locked",
            onClickButton : function () { 
                var rowid = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var noOfRows = rowid.length;
                
               
                if (noOfRows == 0){
                    $( "#modal-warning-none" ).dialog("open");
                }
                else{
                    
                    $.ajax({type:"post",
                        url:"index.php/products/deactivate",
                        data: {ids : rowid},
                        success: function(){
                            $("#"+grid_id).trigger("reloadGrid");
                        }
                    });
                }
            } 
        });
    }
     if (buttons.activate==true && permission.manage_product_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager ,{
            caption:"",
            title:"Mark as active",
            id:"active_"+grid_id,
            buttonicon:"ui-icon-unlocked",
            onClickButton : function () { 
                var rowid = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var noOfRows = rowid.length;
                
               
                if (noOfRows == 0){
                    $( "#modal-warning-none" ).dialog("open");
                }
                else{
                    
                    $.ajax({type:"post",
                    url:"index.php/products/activate",
                    data: {ids : rowid},
                    success: function(){
                        $("#product").trigger("reloadGrid");
                    }
                });
                }
            } 
        });
    }
    if (buttons.load_all==true){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
            caption:"",
            title:"Load all ",
            id:"load_"+grid_id,
            buttonicon:"ui-icon-arrow-4-diag",
            onClickButton : function () { 
                $.ajax({type:"post",
                    url:"index.php/products/populateProductsInGrid",
                    data: {loadall : true},
                    success: function(response){
                        //$("#product").trigger("reloadGrid");
                        var grid = jQuery("#product")[0];
                        var myjsongrid = eval("("+response+")"); 
                        grid.addJSONData(myjsongrid); 


                    }
                }); 
            } 
        });
    }
    if (buttons.export_all==true){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
            caption:"",
            title:"Export As CSV ",
            id:"export_"+grid_id,
            buttonicon:"ui-icon-newwin",
            onClickButton : function () { 
                 $("#"+grid_id).jqGrid('excelExport',{tag:"csv","url":"index.php/products/exportProductsInGrid"});
            } 
        });
    }
    if (buttons.add_cat==true && permission.manage_product_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
            caption:"", 
            title:"Add Category To  Product",
            buttonicon:"ui-icon-plus",
            id:"add_"+grid_id,
            onClickButton : function () { 
                $('#treeViewDiv').jstree('deselect_all');
                $( "#dialog-form" ).dialog( "open" );
            }
        });
    }
    if (buttons.del_cat==true && permission.manage_product_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
            caption:"", 
            title:"Remove Category From  Product",
            buttonicon:"ui-icon-trash",
            id:"del_"+grid_id,
            onClickButton : function () { 
                var rowid = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var noOfRows = rowid.length;
                
               
                if (noOfRows == 0){
                    $( "#modal-warning-none" ).dialog("open");
                }
                else{
                    var grid_data = {
                        grid_id:grid_id,
                        url:'index.php/products/deleteProductCategoryMapping'
                    }
                    $( "#delete-common-dialog" ).data('grid_data',grid_data).dialog("open") ;
                }
                
            }
        });
    }
    if (buttons.add_sup==true && permission.manage_product_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
            caption:"", 
            title:"Add Supplier To  Product",
            buttonicon:"ui-icon-plus",
            id:"add_"+grid_id,
            onClickButton : function () { 
                //$('#treeViewDiv').jstree('deselect_all');
                $( "#dialog-form" ).dialog( "open" );
            }
        });
    }
    if (buttons.del_sup==true && permission.manage_product_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
            caption:"", 
            title:"Remove Supplier From  Product",
            buttonicon:"ui-icon-trash",
            id:"del_"+grid_id,
            onClickButton : function () { 
                var rowid = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var noOfRows = rowid.length;
                
               
                if (noOfRows == 0){
                    $( "#modal-warning-none" ).dialog("open");
                }
                else{
                    var grid_data = {
                        grid_id:grid_id,
                        url:'index.php/products/deleteSupplierProductMapping'
                    }
                    $( "#delete-common-dialog" ).data('grid_data',grid_data).dialog("open") ;
                }
                
            }
        });
    }
                
}

function prepareIncomingInvoicesGrid(settingObj,postData,subgrid,editSettingObj,hiddenSettingObj,subGridSettings){

    var grid_id = settingObj.grid_id;
    var pager = settingObj.pager;
    var subgridMultiselect;
    var subGridCustomButtons;
    var multiselect = settingObj.multiselect;
    var hiddenObjSubgrid ;
    var width = '680';
    if (!isEmpty(settingObj.width)){
        width = settingObj.width;
    }
    if (multiselect!=true){
        multiselect = false;
    }
    if (isEmpty(editSettingObj)){
        editSettingObj= {};
    }

    if (isEmpty(hiddenSettingObj)){
        hiddenSettingObj= {};
    }
    if (isEmpty(postData)){
        postData= {};
    }
    if (subgrid!=true){
        subgrid = false;
        subGridSettings={};
    }
    else {
        if (isEmpty(subGridSettings)){
            subGridSettings= {};
        }
        else {
            subGridCustomButtons = subGridSettings.customButtons;
            subgridMultiselect = subGridSettings.multiselect;
            if (subgridMultiselect!=true){
                subgridMultiselect = false;
            }
            if (!isEmpty(subGridSettings.hiddenObj)){
                hiddenObjSubgrid= subGridSettings.hiddenObj;
            }
        }
    }
    
    
    $("#"+grid_id).jqGrid({
        url:'index.php/invoice/populateInvoices',
        datatype: 'json',
        mtype: 'POST',
        postData:postData,
        colNames:['Invoice Id','Status','Order Id','Comments','Owner','Action','Owner Id'],
        colModel :[ 
            {name:'magento_invoice_increment_id', index:'magento_invoice_increment_id', width:80, align:'right'},
            {name:'status', index:'status', width:60, align:'right',hidden:hiddenSettingObj.status},
            {name:'magento_order_increment_id',index:'magento_order_increment_id', width:80, align:'right'},
            {name:'comments', index:'comments', width:80, align:'right',hidden:hiddenSettingObj.comments},
            {name:'owner', index:'owner', width:80, align:'right',hidden:hiddenSettingObj.owner},
            {name:'pack', index:'pack', width:40, align:'right',editable:false,search:false,formatter:'showlink', formatoptions:{baseLinkUrl:'index.php/invoice/packInvoice'},hidden:hiddenSettingObj.pack},
            {name:'owner_id', index:'owner_id', width:80, align:'right',hidden:true}
        ],
        pager: '#pager',
        rowNum:10,
        rowList:[5,10,20],
        sortname: 'id',
        sortorder: 'desc',
        viewrecords: true,
        gridview: true,
        multiselect:multiselect,           
        ignoreCase:true,
        rownumbers:true,
        height:'auto',
        width:width,
        caption: 'Invoices',
        jsonReader : {
            root:"invoicedata",
            page: "page",
            total: "total",
            records: "records",
            cell: "dprow",
            id: "id"
        },
            subGrid:subgrid,
            subGridRowExpanded: function(subgrid_id, row_id) {
                var subgrid_table_id, pager_id;
                subgrid_table_id = subgrid_id+"_t";

                pager_id = "p_"+subgrid_table_id;
                
                $("#"+subgrid_id).html("<table id='"+subgrid_table_id+"' class='scroll'></table><div id='"+pager_id+"' class='scroll'></div>");
                var subgridSettingsObj ={
                    grid_id:subgrid_table_id,
                    pager:pager_id,
                    parent_grid:grid_id

                };
                prepareIncomingInvoiceItemsGrid(subgridSettingsObj,{
                    invoiceId:row_id
                });
                $("#"+subgrid_table_id).jqGrid('navGrid',"#"+pager_id,{
                edit:false,
                add:false,
                del:false,
                search:false,
                view:true
            });
            var settingsObj ={grid_id:subgrid_table_id,pager:pager_id,parent_grid:grid_id,parent_row_id:row_id};
            addCustomButtonsIncomingInvoiceItemsGrid(settingsObj,subGridCustomButtons);
                
        }
    });
}
function addCustomButtonsIncomingInvoiceGrid(settingObj,buttons){
    var permission = isAllowedBulk([
    
    {
        element:'manage_incoming_invoice_button',
        resource:'incoming_invoice',
        permission:'manage'
    },
    {
        element:'mark_incoming_invoice_button',
        resource:'incoming_invoice',
        permission:'mark'
    },

    {
        element:'assign_incoming_invoice_button',
        resource:'incoming_invoice',
        permission:'assign'
    }]);
    
    var grid_id = settingObj.grid_id;
    var pager = settingObj.pager;
    
    
    if (buttons.manage_incoming_invoice_button==true  && permission.manage_incoming_invoice_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
            caption:"", 
            title:"Ship",
            buttonicon:"ui-icon-suitcase",
            id:"ship_"+grid_id,
            onClickButton : function () { 
                var selectedRows = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var noOfRows = selectedRows.length;
                var error = false;
                var onlyOpen = true;
                var onlySelf = true;
                if (noOfRows == 0){
                    $( "#modal-warning-none" ).dialog("open");
                }
                else{
                    $.each(selectedRows, function(){

                        if ($("#"+grid_id).getCell(this,'status')!='packed'){
                            onlyOpen = false;
                            return false;
                        }
                    })
                    if (!onlyOpen){
                        $("#status_warning_text").text("Line Items With Status 'packed' Can Only Be Submitted For Approval");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                    else {
                        
                        $.each(selectedRows, function(){

                            if ($("#"+grid_id).getCell(this,'owner_id')!=user_id){
                                onlySelf = false;
                                return false;
                            }
                        });
                        if (!onlySelf){
                            $("#status_warning_text").text("You Can Only Submit Invoices Owned By You");
                            $( "#modal-warning-general" ).dialog("open");
                        }
                        else{
                            $.ajax({
                                method:"POST",
                                url:"index.php/invoice/ship",
                                data: {
                                    ids : selectedRows
                                },
                                success: function(){
                                    $("#"+grid_id).trigger("reloadGrid");
                                    emptyMessages()
                                    showSuccessMessage("Selected Quotations Are Submitted For Shipment");
                                }
                            });
                        }
                    }
                }
            } 
        });
    }
    
    
    
   
    if (buttons.comments == true /*&& permission.comments_button=="true"*/)  {
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
            caption:"", 
            title:"Show Comments",
            buttonicon:"ui-icon-script",
            id:"comments_"+grid_id,
            onClickButton : function () { 

                var rowid = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var noOfRows = rowid.length;
                //var onlyOpen = false;
                if (noOfRows != 1){
                    $( "#modal-warning-exactly-one" ).dialog("open");
                }
                else{
                    var quote_id = $("#"+grid_id).jqGrid('getGridParam', 'selrow');
                    $.ajax({
                        method:"POST",
                        url:"index.php/procurement/getCommentsForEntity",
                        data: {
                            entity:'quote',
                            id : quote_id
                        },
                        success: function(response){
                            var resObj = JSON.parse(response);
                            console.log(resObj);
                            $( "#comment-quote-dialog" ).data('grid_data',resObj).dialog("open") ;
                        }
                    });
                }                       
            } 
        });
    } 
    
   if (buttons.mark_packer==true  && permission.mark_incoming_invoice_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
            caption:"", 
            title:"Mark Yourself As Owner",
            buttonicon:"ui-icon-star",
            id:"mark_owner_"+grid_id,
            onClickButton : function () { 
                var rowid = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var selectedRows = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var noOfRows = selectedRows.length;
                var onlyOpen= true;
                var onlyEmpty= true;
                if (noOfRows == 0){
                    $( "#modal-warning-none" ).dialog("open");
                }
                else{
//                   
                        $.each(rowid, function(){

                            if (!isEmpty($("#"+grid_id).getCell(this,'owner_id'))){
                                onlyEmpty = false;
                                return false;
                            }
                        });
                        if (!onlyEmpty){
                            $("#status_warning_text").text("Some/All Selected Order(s) already have Owner. Please Select Records Without Any Owner");
                            $( "#modal-warning-general" ).dialog("open");
                        }
                        else {
                            $.ajax({
                                method:"POST",
                                url:"index.php/invoice/assignEntityToUser",
                                data: {
                                    ids : rowid,
                                    entity:'incoming_invoice',
                                    user_id:user_id,
                                    role:'owner'
                                },
                                success: function(){
                                    $("#"+grid_id).trigger("reloadGrid");
                                    emptyMessages();
                                    showSuccessMessage("success");
                                }
                            });
                        }
                }
                        
            } 
        });
    }
    if (buttons.assign==true && permission.assign_incoming_invoice_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
            caption:"", 
            title:"Assign",
            buttonicon:"ui-icon-transferthick-e-w",
            id:"assign_"+grid_id,
            onClickButton : function () { 
                var rowid = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');
                var noOfRows = rowid.length;
                var onlyOpen = true;
                if (noOfRows == 0){
                    $( "#modal-warning-none" ).dialog("open");
                }
                else{
                    var selrow = $("#"+grid_id).jqGrid('getGridParam', 'selarrrow');  
                    var owner_name = $("#"+grid_id).getCell(selrow,'owner');
                    $.each(selrow, function(){
                         
                        if ($("#"+grid_id).getCell(this,'status')!='invoiced' ){
                            onlyOpen = false;
                            return false;
                        }
                    });
                    if (!onlyOpen){
                        $("#status_warning_text").text(" Orders With 'Invoiced' Can Only Be Permitted For Assignment");
                        $( "#modal-warning-general" ).dialog("open");
                    }
                    else {
                        console.log(owner_name);
                        var grid_data = {
                            entity:'incoming_invoice',
                            current_owner:owner_name,
                            row_id:selrow,
                            grid_id:grid_id
                        };
                        $( "#assignment-common-dialog" ).data('grid_data',grid_data).dialog( "open" );
                    }
                    
                }
                
            } 
        });
    }
    if (buttons.load_owner_all==true){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
            caption:"", 
            title:"Reload w/ all User Records",
            buttonicon:"ui-icon-arrow-4",
            id:"reload_user_"+grid_id,
            onClickButton : function () { 
                var postData = $("#"+grid_id).getGridParam('postData');
                postData.owner_id = 'all';
                $("#"+grid_id).setGridParam('postData',postData);
                $("#"+grid_id).trigger("reloadGrid");
            } 
        });
    }
    
}

function prepareIncomingInvoiceItemsGrid(settingObj,postData,editSettingObj,hiddenSettingObj){
    var grid_id = settingObj.grid_id;
    var pager = settingObj.pager;
    var width = '450';
    
    if (!isEmpty(settingObj.width)){
        width = settingObj.width;
    }
    
    var multiselect = settingObj.multiselect;
    if (multiselect!=true){
        multiselect = false;
    }
    if (isEmpty(editSettingObj)){
        editSettingObj= {};
    }

    if (isEmpty(hiddenSettingObj)){
        hiddenSettingObj= {};
    }
    if (isEmpty(postData)){
        postData= {};
    }
   
    $("#"+grid_id).jqGrid({
        url:'index.php/invoice/populateInvoiceItems',
                    datatype: 'json',
                    mtype: 'POST',
                    colNames:['SKU','Name','Invoiced','Packed'],
                    colModel :[ 
                        {name:'sku', index:'sku', width:80, align:'right',search:false,editable:false},
                        {name:'name', index:'status', width:140, align:'right',search:false,editable:false},
                        {name:'invoiced_number',index:'invoiced_number', width:80, align:'right',search:false,editable:false},
                        {name:'packed_number', index:'packed_number', width:80, align:'right',search:false,editable:true},
                        
                    ],
                    pager: '#'+pager,
                    rowNum:10,
                    rowList:[5,10,20],
                    sortname: 'id',
                    sortorder: 'desc',
                    viewrecords: true,
                    gridview: true,
                    ignoreCase:true,
                    rownumbers:true,
                    height:'auto',
                    width:width,
                    caption: 'Invoices',
                    postData:postData,
                    
                    jsonReader : {
                        root:"invoicedata",
                        page: "page",
                        total: "total",
                        records: "records",
                        cell: "dprow",
                        id: "id"
                    }
                });
}

function addCustomButtonsIncomingInvoiceItemsGrid(settingObj,buttons){
    var permission = isAllowedBulk([
    
    {
        element:'pack_incoming_invoice_button',
        resource:'incoming_invoice',
        permission:'manage'
    }
    ]);
    
    var grid_id = settingObj.grid_id;
    var pager = settingObj.pager;
    var parent_grid = settingObj.parent_grid;
    var parent_row_id = settingObj.parent_row_id;
    
    
    if (buttons.pack_button==true  && permission.pack_incoming_invoice_button=="true"){
        $("#"+grid_id).jqGrid('navButtonAdd','#'+pager,{
            caption:"", 
            title:"Pack",
            buttonicon:"ui-icon-tag",
            id:"pack"+grid_id,
            onClickButton : function () { 
                var row_id = $("#"+grid_id).jqGrid('getGridParam', 'selrow');
                
                var onlyOpen = true;
                var onlySelf = true;
                if (row_id == null ||row_id == undefined ){
                    $( "#modal-warning-none" ).dialog("open");
                }
                else{
                        if ($("#"+parent_grid).getCell(parent_row_id,'owner_id')!=user_id){
                            onlySelf = false;
                        }
                       
                        if (!onlySelf){
                            $("#status_warning_text").text("You Can Only Pack Invoices Owned By You");
                            $( "#modal-warning-general" ).dialog("open");
                        }
                        else{
                            var grid_data = {grid_id:grid_id,row_id:row_id};
                            $("#dialog-form").data('grid_data',grid_data).dialog("open");
                        }
                   //}
                }
            } 
        });
    }
    
}


function isAllowed(module_id,permission){
    var allow=false;
    if (!isEmpty(module_id)){
        $.ajax({
            method:"POST",
            url:"index.php/home/isAllowed",
            data: {
                module: module_id,
                permission:permission
            },
            async: false,
            success: function(response){
                if (response!="true"){
                    response = false;
                }
                else  {
                    response = true;
                }
            
                allow = response;
            }
        });
        
    }
    return allow;
}
function isAllowedBulk(resource_perm){
    var allow={};
    if (!isEmpty(resource_perm)){
        $.ajax({
            method:"POST",
            url:"index.php/home/isAllowedBulk",
            data: {
                resource_perm:resource_perm
            },
            async: false,
            success: function(response){
                //                if (response!="true"){
                //                    response = false;
                //                }
                //                else  {
                //                    response = true;
                //                }
            
                allow = JSON.parse(response);
            }
        });
        
    }
    return allow;
}

function invFormatter ( cellvalue, options, rowObject ){
    // format the cellvalue to new format
    return 'PINV-' + cellvalue;
}
function poFormatter ( cellvalue, options, rowObject ){
    // format the cellvalue to new format
    return 'PO-' + cellvalue;
}
        