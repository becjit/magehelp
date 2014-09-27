<html>
    <head>
       <?php $this->load->view("common/header"); ?>
        
        
        <style>
            .column {
            float: left;
            padding: 1em 1em 2em;
            width: 47%;
            }
            .base-column{
               padding: 1em 1em 1em;
               width: 100%;
            }
             .triple-column{
               padding: .5em .5em;
               width: 39%;
            }
           
            .field{
                width:100%;
            }
            .ui-widget-header {height:12px;}
            .item-column {
            
            width: 95%;
            padding:1em;
            }
            .table-column {
            
            width: 97%;
            padding:1em;
            }
            .ui-combobox-input{
                width:23em;
            }
             #supplierOp-input{
                width:10em;
            }
            #warehouseOp-input{
                width:10em;
            }
            .shopifine-ui-dialog{
                height:auto;
            }
            
            .ui-tabs {
                height: 20em;
                margin: 0 auto;
                width: 45%;
                left:0;
            }
             
            .calculated {
                color: green;
                font-size: 90%;
            }
            .ui-tabs-nav{
                height:22px;
            }
            .inithide{
                display:none;
            }
            #quoteForm .directlabel{
                width:30%;
            }
            #quoteForm .rolabel{
                width:40%;
            }
            #itemForm .labeldiv{
                width:30%;
            }
            #itemForm .centrelabel{
                width:18%;
            }
            #itemForm .directlabel{
                width:27%;
            }
            #content_area{
                width:95%;
            }
        </style>
       
    <script type="text/javascript">
                $(function() {
        
        //form validation 
        
         $.fn.toggleDisabled = function(){
            return this.each(function(){
                this.disabled = !this.disabled;
            });
        };
         $("#quoteForm").validate({
             rules:{
                 dir_disc_perc_quote:{
                     number:true,
                     minStrict:0
                     
                 },
                 dir_disc_total_quote:{
                     
                     number:true,
                     minStrict:0
                     
                 }}});
         $("#itemForm").validate({
             ignore:[],
             rules:{
                 quantity:{
                     required:true,
                     digits:true,
                     min:1
                 },
                 exPrice:{  
                     required:true,
                     number:true                   
                 },
                 dir_disc_perc:{
                     number:true,
                     minStrict:0
                 },
                 dir_disc_amount:{
                     
                     number:true,
                     minStrict:0
                 },
                 dir_disc_fg:{
                     
                     digits:true,
                     min:1
                 }
         }}
        );
         
        // datepicker for Add Form 
        $( "#reqdate,#neededdate" ).datepicker({
            showOn: "button",
            buttonImage: "images/calendar.gif",
            buttonImageOnly: true,
            dateFormat:"dd/mm/yy",
            minDate:0
        });

        function quotegriddates(id){
            jQuery("#"+id+"_needed_by_date","#quotes").datepicker({dateFormat:"yy-mm-dd",minDate:0});
        }
        function quoteitemgriddates(id){
            jQuery("#"+id+"_needed_by_date","#lineItems").datepicker({dateFormat:"yy-mm-dd",minDate:0});
        }
        //end datepicker
        $("#productOp").combobox({customChange:initPriceRules($("#productOp").val())}
        
    ); 
    //end combox
         
         
       $( "#dialog-form" ).dialog({
            autoOpen: false,
            height: 'auto',
            width: '60%',
            position:[300,15],
            modal: true,
            buttons: {
                "DoneButton": {
                    id:"doneBtn",
                    text:"Create Quote",
                    click:function() {
                    var quote_id = $("#quoteId").val();
                    var mode = $(this).data('grid_data').mode;
                    if (mode=='edit'){
                       quote_id = $(this).data('grid_data').quote_id;
                    }
                    var isValid = $("#quoteForm").valid();
                    if (isValid){
                       $.ajax({url:"index.php/procurement/createQuotes",
                            type:"POST",
                            data:{  quoteId:quote_id,
                                    form_data:$("#quoteForm").toObject(),
                                   total_before_discount:$("#lineItems").getGridParam("userData").final_total,
                                    total_discount:$("#discount_total_quote").text()
                                },
                            success:function(response)
                            {
                              
                                if ($("#quoteId").val()==""){
                                   
                                    if (mode!='edit'){
                                        if (response !='error'){
                                            $("#quoteId").val(response);
                                            $("#pkdheader").show();
                                            $("#doneBtn > span").text("Done");
                                            var subgridSettingsObj ={grid_id:'lineItems',pager:'pagerPk',addFooter:true};
                                            prepareQuoteItemsGrid(subgridSettingsObj,{quoteId:response});
                                            $("#lineItems").navGrid("#pagerPk",{edit:false,add:false,del:true,search:false},{},{},{},{},{});
                                            addCustomButtonsInQuoteItemGrid(subgridSettingsObj, {add:true,edit:true,chooser:true,data:{quote_id:response}});
                                            $("#del_lineItems").insertAfter("#add_lineItems");
                                            //prepareQuoteItemsGrid("lineItems","pagerPk",response,true,true,true,true);
                                            $("#lineItems").jqGrid("setGridParam",
                                            {userDataOnFooter:true,gridComplete:initDiscountCntnr});
                                        }
                                    }
                                    
                                }

                            } //end success of Ajax
                        }) //end ajax
                        if ($("#quoteId").val()!=""){
                            $( this ).dialog( "close" );
                        } 
                        if (mode=="edit"){
                            $( this ).dialog( "close" );
                        } 

                    } //end ifvalid

                }}, //end of Create button
                Cancel: function() {
                    $( this ).dialog( "close" );
                }
            },//end buttons
            open:function(event,ui){
                //reseting line item grids once again to be doubly sure
                $("#pkdheader").hide();
                $("#lineItems").jqGrid("GridUnload");
                $('#none_quote').attr('checked','checked');
                var quote_id = $(this).data('grid_data').quote_id;
                var mode = $(this).data('grid_data').mode;
                if (mode=='edit'){
                    var subgridSettingsObj ={grid_id:'lineItems',pager:'pagerPk',addFooter:true};
                    prepareQuoteItemsGrid(subgridSettingsObj,{quoteId:quote_id});
                    $("#lineItems").navGrid("#pagerPk",{edit:false,add:false,del:true,search:false},{},{},{},{},{});
                    addCustomButtonsInQuoteItemGrid(subgridSettingsObj, {add:true,edit:true,chooser:true,data:{quote_id:quote_id}});
                    $("#del_lineItems").insertAfter("#add_lineItems");
                    //prepareQuoteItemsGrid("lineItems","pagerPk",quote_id,true,true,true,true);
                    $("#lineItems").jqGrid("setGridParam",
                       {userDataOnFooter : true,gridComplete:initDiscountCntnr});
                        
                    $("#doneBtn > span").text("Done");
                   
                    $("#supval").val(myGrid.getCell(quote_id,'supplier_id'));
                    var type =myGrid.getCell(quote_id,'discount_type');
                    var val =myGrid.getCell(quote_id,'discount_value');
                    console.log(type);
                    if (type=='none'){
                        $("#none_quote").attr("checked","checked");
                    }
                    else if (type=='directpercentage'){
                        $("#dir_disc_perc_quote_radio").attr("checked","checked");
                        $("#dir_disc_perc_quote").val(val);
                    } 
                    else if (type=='directamounttotal'){
                        $("#dir_disc_total_quote_radio").attr("checked","checked");
                        $("#dir_disc_total_quote").val(val);
                    } 
                    $("#amount_total_quote").text(myGrid.getCell(quote_id,'final_total'));
                    $("#discount_total_quote").text(myGrid.getCell(quote_id,'discount_amount'));
                    $("#warehouseval").val(myGrid.getCell(quote_id,'warehouse_id'));
                    $("#pricelist_id_hidden").val(getPricelistId($("#supval").val()));
                    
                    var needDate = myGrid.getCell(quote_id,'needed_by_date');
                    if (needDate!=null && needDate!=""){
                        var d1 = Date.parse(needDate);
                        $("#reqdateval").val(d1.toString('dd/MM/yyyy'));
                    }
                }

                $("#tabs").tabs({

                    load: function(event,ui){
                            //console.log( ui);
                            $( "#reqdate" ).datepicker({
                                showOn: "button",
                                buttonImage: "images/calendar.gif",
                                buttonImageOnly: true,
                                dateFormat:"dd/mm/yy",
                                minDate:0
                            });

                            if (ui.tab.id=="base"){
                                
                                $("#supplierOp").val($("#supval").val());
                                $("#warehouseOp").val($("#warehouseval").val());
                                if (parseInt(myGrid.getCell(quote_id,'rfq_reference'))>0){
                                    $("#supplierOp, #warehouseOp").attr("disabled",true);
                                }
                                $("#reqdate").val($("#reqdateval").val());
                                // reset
                                $("#reqdateval").val("");
                                $("#warehouseval").val("");
                                $("#supval").val("");

                            }
                            else if (ui.tab.id=="notes"){
                                $("#approveNotes").val($("#notesval").val());
                                // reset
                                $("#notesval").val("");
                            }
                             $("#supplierOp").change(function(){
                                getPricelistId($("#supplierOp").val());
                            });
                    },
                    beforeActivate: function (event,ui){
                        if (ui.newTab[0].id=="notesLink"){
                            // save old values
                            $("#reqdateval").val($("#reqdate").val());
                            $("#warehouseval").val($("#warehouseOp").val());
                            $("#supval").val($("#supplierOp").val());

                        }
                        else if (ui.newTab[0].id=="baseLink"){
                            $("#notesval").val($("#approveNotes").val());
                        }
                    }
                });
                // this should be registered only after the tabs are loaded.
              

            },

            close: function(event,ui) {

                //validate so that close button is not pressed while there is no line item
                var noOfRec =$("#lineItems").getGridParam("records");
                if (noOfRec<1){
                        $.ajax({
                        method:"POST",
                        data:{id:$("#quoteId").val(),entity:'quote',items_count:noOfRec},
                        url:'index.php/procurement/closeValidate'
                    })
                };
                $("#quoteForm").data('validator').resetForm();
                $("#quoteForm .valuediv").text("");
                $('#quoteForm')[0].reset();
                $('input:radio[name=pricerule_quote]').removeAttr("checked")

                //reset line item grids
                $("#pkdheader").hide();
                $("#lineItems").jqGrid("GridUnload");
                 $("#supplierOp, #warehouseOp").attr("disabled",false);
                //reload main grid
                $("#quotes").trigger("reloadGrid");
                //destrying the tab to bring it to the initial state while opening it next
                $("#tabs").tabs("destroy");
                //change Button Text To original
                $("#doneBtn > span").text("Create Quote");
                $("#status-message-li").empty();

            }
        });
        
        //Line Item Dialog
        
       $( "#dialog-form-item" ).dialog({
            autoOpen: false,
            height: 'auto',
            width: '60%',
            position:[300,15],
            modal: true,
            buttons: {
                "addupdateitem":{
                    text:"Add",
                    id:"add_update_item", 
                    click:function() {
                        var isvalid = $("#itemForm").valid();
                        var grid = $(this).data('grid_data').grid_id;
                        var quote_id = $(this).data('grid_data').quote_id;
                        var line_id = $(this).data('grid_data').line_id;
                        var action = $(this).data('grid_data').action;
                        var url = "index.php/procurement/addQuoteItem";
                        if (action == 'edit'){
                            url = "index.php/procurement/modifyQuoteItem";

                        }
     //                   url="test";
                        if (isvalid){
                            $.ajax({
                                url:url,
                                type:"POST",
                                data:{
                                    quoteid:quote_id,
                                    form_data:$("#itemForm").toObject(),
                                    unit_discount:$("#unit_price_discount").text(),
                                    free_items:$("#free_items").text(),
                                    id:line_id,
                                    pricelist_id:$("#pricelist_id_hidden").val()
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
                }
                ,
                Cancel: function() {

                    $( this ).dialog( "close" );
                }
            },
            open: function(event,ui){
                var grid = $(this).data('grid_data').grid_id;
                var line_id = $(this).data('grid_data').line_id;
                var action = $(this).data('grid_data').action;
                var product_id = $("#"+grid).getCell(line_id,'product_id');
                $("#line_id_hidden").val(line_id);
                $("#action_item_hidden").val(action);
                
                //prepare Rules Grid
                var settingsObj = {grid_id:'rules_grid',pager:'pagerRules'};
                prepareRulesGrid(settingsObj,$("#pricelist_id_hidden").val(),product_id);
                $("#rules_grid").setGridWidth("550");
                $("#rules_grid").setCaption("Existing Price Rules");
                $("#rules_grid").navGrid('#pagerRules',{add:false,edit:false,del:true,view:true,search:false},{}, {}, {}, {}, {});
                
                // by default assume action 'add'.So initially Check The None Checked and Existing PR disabled
                $('#nonePR').attr('checked','checked');
                $("#existingPR").attr('disabled','disabled');
                
                //check if any pricelist for this supplier exist or not
                if ($("#pricelist_id_hidden").val()!="" ){
                    $("#pricelistNameCntnr").show();
                    $("#existingPRCntnr").show();
                    
                }
                
                // if Does not exist do not show the pricelist container or the pricerules
                else {
                    $("#pricelistNameCntnr").hide();
                    $("#existingPRCntnr").hide();
                }
                
                // if 'edit'
                if (action=='edit'){
                    $("#add_update_item .ui-button-text").text("Update");
                    var quoted_qty = parseInt($("#"+grid).getCell(line_id,'quoted_quantity'));
                    $("#productOp-input").parent().hide();
                    $("#productOp").val(product_id);
                    $("#product_name_edit").show().text($("#"+grid).getCell(line_id,'name'));
                    $("#quantity").val(quoted_qty);
                    $("#exPrice").val($("#"+grid).getCell(line_id,'expected_price'));
                    $("#estPrice").text($("#"+grid).getCell(line_id,'estimated_value'));
                    $("#notes").val($("#"+grid).getCell(line_id,'comments'));
                    var type = $("#"+grid).getCell(line_id,'pricerule_type');
                    var val =$("#"+grid).getCell(line_id,'discount_value');
                    if (type=='none'){
                        $("#nonePR").attr("checked","checked");
                    }
                    else if (type=='directpercentage'){
                        $("#dir_disc_perc_radio").attr("checked","checked");
                        $("#dir_disc_perc").val(val);
                    } 
                    else if (type=='directamounttotal'){
                        $("#dir_disc_total_radio").attr("checked","checked");
                        $("#dir_disc_total").val(val);
                    } 
                    else if (type=='existing'){
                        //if type existing then reload it with inherite dules and dir exactly the same way it was done during addition
                        $("#existingPR").attr("checked","checked");
                        initPriceRules(product_id);
                    } 
                    var needDate = $("#"+grid).getCell(line_id,'needed_by_date');
                     if (needDate!=null && needDate!=""){
                         var d1 = Date.parse(needDate);
                         $("#neededdate").val(d1.toString('dd/MM/yyyy'));
                     }
                    var free_items= parseInt($("#"+grid).getCell(line_id,'free_items'));
                    if (isNaN(free_items)){
                        free_items = parseInt("0");
                    }
                    $("#unit_price_final").text($("#"+grid).getCell(line_id,'final_unit_price'));
                    $("#unit_price_discount").text($("#"+grid).getCell(line_id,'unit_price_discount'));;
                    $("#free_items").text(free_items);
                    $("#items_total").text(free_items + quoted_qty);
                    $("#discount_total").text($("#"+grid).getCell(line_id,'total_discount'));
                    $("#amount_total").text($("#"+grid).getCell(line_id,'final_total'));
                }
                
                
            },
            close: function(event,ui) {
                $("#itemForm").data('validator').resetForm();
                $("#itemForm .inithide").hide();
                $("#itemForm .initshow").show();
                $("#itemForm .valuediv:not(#pricelist_name)").text("");
                $('#itemForm')[0].reset();
                $("#estValue").text("");
                $("#curPrice").text("");
                //$("#exPrice").removeAttr("disabled");
                $("#exPrice").removeAttr("readonly");
                $("#existingPR").attr('disabled','disabled');
                $("#rules_grid").jqGrid("GridUnload");
            }
        });

        // Main Request For Quotation Grid                    
        
        var myGrid = $("#quotes");
        //discount_type:true,discount_value:true
        var postData={_status:['open','draft','waitingforapproval','rejected']}
        var settingsObj = {grid_id:'quotes',pager:'pager',multiselect:true};
        prepareQuotesGrid(settingsObj,postData,true,{},{},{});
        myGrid.navGrid("#pager",{edit:false,add:false,del:false,search:false,view:true},{},{},{},{});
        
        var buttons = {add:true,edit:true,po_direct:true,submit_approve:true,reopen:true,del:true,comments:true,load_owner_all:true};
        addCustomButtonsQuoteGrid(settingsObj,buttons);
        myGrid.jqGrid('filterToolbar', {stringResult: true, searchOnEnter: true, defaultSearch : "cn"});
        $("#del_quotes").insertAfter("#edit_quotes");
//       

        //warning dialogs
       $( "#dialog-modal" ).dialog({
            autoOpen:false,
            height: 140,
            modal: true
       });
      
       
       function getPricelistId(supplier_id){
                $.ajax({
                    method:"POST",
                    url:"index.php/procurement/getRelevantPriceListId",
                    data: {supplier_id : supplier_id},
                    success: function(response){
                        var resObj = JSON.parse(response);
                        console.log("response == " + resObj);
                        console.log("id == " + resObj.id);
                        $("#pricelist_id_hidden").val(resObj.id);
                        $("#pricelist_name").text(resObj.name);
                        $("#inherit_rules").val(resObj.inherit_rules);
                        $("#inherit_rules_dir").val(resObj.inherit_rules_dir);

                        //pricelist_name
                    }
                });
       }
    
    $("#isOverride").change(function(){
        //$("#exPrice").toggleDisabled();
        if (this.checked){
                //$("#exPrice").removeAttr("disabled");
                $("#exPrice").removeAttr("readonly");
        }
            else {
                 $("#exPrice").attr("readonly","readonly");
            }
        
    })
    $("input:radio[name=pricerule]").change(function(){
        var selected = $(this).val();
        
        if (selected=='existing'||selected=='nonePR'){
            $("#dir_disc_fg").attr("disabled","disabled");
        }
        else {
            $("#dir_disc_fg").removeAttr("disabled");
        }
    })
    
    //$("input:radio[name=pricerule]").change(function(){
    $("#applyBtn_quote").click(function(){
        var isvalid = $("#quoteForm").valid();
        if (isvalid){
            calculateQuoteDiscount();
        }
        
    });
    
    function calculateQuoteDiscount(){
        var selected = $("input:radio[name=pricerule_quote]:checked").val() ;
        var total = $("#lineItems").getGridParam("userData").final_total;
            var totalDisc = 0;
            if (selected=='directamounttotal'){
               totalDisc = parseFloat($("#dir_disc_total_quote").val()).toFixed(2);
               
            }
            else if (selected=='directpercentage'){
                var totalDiscPerc = parseFloat($("#dir_disc_perc_quote").val());
                var totalDisc = (total*totalDiscPerc/100).toFixed(2);

            }
            $("#discount_total_quote").text(totalDisc);
            var total_after = (total-totalDisc).toFixed(2);
            $("#amount_total_quote").text(total_after);
    }
    
    $("#applyBtn").click(function(){
        var isvalid = $("#itemForm").valid();
        //var selected = $(this).val();
       var selected = $("input:radio[name=pricerule]:checked").val() ;
       
        //console.log("this "+ $(this).val());
        if (isvalid){
            var unitPrice = parseFloat($("#exPrice").val());
            var quantity = parseInt($("#quantity").val());
            var unitDiscount;
            var finalUnitPrice;
            var freeItems;
            if (selected=='existing'){
            
            $.ajax({
                url:"index.php/procurement/applyPriceRules",
                method:"POST",
                data:{product_id:$("#productOp").val(),
                    pricelist_id:$("#pricelist_id_hidden").val(),
                    quantity:quantity,
                    unit_price:unitPrice,
                    inherit_rules:$("#inherit_rules").val(),
                    inherit_rules_dir:$("#inherit_rules_dir").val()
                },
                success:function(response){
                    console.log(response);
                    var resJson = JSON.parse(response);
                    finalUnitPrice = parseFloat(resJson.final_unit_price).toFixed(2);
                    freeItems = parseInt(resJson.free_items);
                    unitDiscount = (unitPrice - finalUnitPrice).toFixed(2);
                    
                    $("#unit_price_final").text(finalUnitPrice);
                    $("#unit_price_discount").text(unitDiscount);;
                    $("#free_items").text(freeItems);
                    $("#items_total").text(quantity + freeItems);
                    $("#discount_total").text(unitDiscount*quantity);
                    $("#amount_total").text(finalUnitPrice*quantity);
                }
            })
            } 
            
            else if (selected=='directamountunit'){
            unitDiscount = parseFloat($("#dir_disc_unit").val()).toFixed(2);
            freeItems = parseInt($("#dir_disc_fg").val());
            }
            else if (selected=='directamounttotal'){
                var totalDisc = parseFloat($("#dir_disc_total").val()).toFixed(2);
                unitDiscount = totalDisc/quantity; 
                freeItems = parseInt($("#dir_disc_fg").val());
            }
            else if (selected=='directpercentage'){
                var totalDiscPerc = parseFloat($("#dir_disc_perc").val()).toFixed(2);
                unitDiscount = unitPrice*totalDiscPerc/100;
                freeItems = parseInt($("#dir_disc_fg").val());
            }
            
            if (isNaN(freeItems)){
                freeItems = 0;
            }
            finalUnitPrice = unitPrice-unitDiscount;
            var discTotal = unitDiscount*quantity;
            var amtTotal = finalUnitPrice*quantity;
            $("#unit_price_discount").text(unitDiscount.toFixed(2));
            $("#discount_total").text(discTotal.toFixed(2));
            $("#unit_price_final").text(finalUnitPrice.toFixed(2));
            $("#amount_total").text(amtTotal.toFixed(2));
            $("#free_items").text(freeItems);
            $("#items_total").text(quantity + freeItems);
        }
        
        
    })
    
    $("#pricelist_rule_fs input[type=text],#pricelist_rule_fs_quote input[type=text]").click(function(){
         $("#"+this.id+"_radio").attr("checked","checked");
         $("#pricelist_rule_fs input[type=text]:not(#this.id)").val("");
        });
        
    $("#pricelist_rule_fs_quote input[type=text]").click(function(){
     $("#"+this.id+"_radio").attr("checked","checked");
     $("#pricelist_rule_fs_quote input[type=text]:not(#this.id)").val("");
      
      
    })
    function initDiscountCntnr(){
       if (parseFloat($("#lineItems").getGridParam("userData").final_total)>0){
           $("#discountCntnr").show();
           calculateQuoteDiscount();
//           $("#lineItems").setGridParam({userDataOnFooter : true})
           
       }
       else {
           $("#discountCntnr").hide();
       }
       
      
    }
    
    function initPriceRules(product_id){
                if ($("#pricelist_id_hidden").val()!=""){
                    $.ajax({
                        type:"GET",
                        url:'index.php/procurement/getPriceFromPricelist',
                        data:{product_id:product_id,pricelist_id:$("#pricelist_id_hidden").val(),supplier_id:$("#supplierOp").val()},
                        success:function (response){
                            if (response!=null && response!=""){
                                $("#exPrice").val(response );
                                //$("#exPrice").attr("disabled","disabled");
                                $("#exPrice").attr("readonly","readonly");
                                $("#isOverride,#override-help").show();
                                
                            }
                            else{
                                 $("#exPrice").val("" );
                                //$("#exPrice").removeAttr("disabled");
                                 $("#exPrice").removeAttr("readonly");
                                $("#isOverride,#override-help").hide();
                            }
                            
                        }
                    })
                    
                    $("#rules_grid").jqGrid('setGridParam',{url:"index.php/procurement/populatePriceRules?pricelist_id="+$("#pricelist_id_hidden").val()+"&product_id="+product_id+"&inherit_rules="+$("#inherit_rules").val()+"&inherit_rules_dir="+$("#inherit_rules_dir").val(),page:1});
                    
                    $("#rules_grid").jqGrid('setGridParam',{
                        loadComplete:function(){
                            var num = $("#rules_grid").jqGrid('getGridParam','records');
                            //console.log("num = " + num);
                            if (num>0){
                                 //$( "#existingPRCntnr").show();
                                 $("#existingPR").removeAttr('disabled');
                                $("#existingPR").attr('checked','checked').change();
                                
                            }
                            else{
                                $("#existingPR").removeAttr('checked');
                                $("#existingPR").attr('disabled','disabled');
                                 $("#nonePR").attr('checked','checked').change();
                               //$( "#existingPRCntnr").hide();
                            }
                        }});
                    $("#rules_grid").trigger("reloadGrid");
                    
            }
        } 
    
    });        
 $(window).load(function(){
       
        var warningDialogs={one:true,none:true,morethanone:true,exactlyone:true};
        initDialogs(warningDialogs);
        initCommentsForQuote();
        initDeleteDialog();
    });
        </script>
        
    </head>
     
    <body>
        <?php  $this->load->view("common/menubar"); ?>
        <?php  $this->load->view("common/dialogs"); ?>
        <div id="dialog-modal" title="Error">
            <p>Reference Number(s) <span id="refIds" style="font-weight:bold;"></span> Does Not Have Estimated Value. Please Check The Quantity Or Price Of Line Items</p>
        </div>
        
        
        <div id ="dialog-form">
            <h1 id="formHeader">Quote # <span id="quoteRef"></span> Details</h1>   
            <form id="quoteForm">
                <fieldset>
                    <div id="tabs">
                        <ul>
                            <li id="baseLink"><a id="base" href="<?php echo site_url('procurement/loadCreateQuoteFormFragment') ?>">Basic Details</a></li>
                            <li id="notesLink"><a id ="notes" href="<?php echo site_url('procurement/loadCreateQuoteNotesFragment') ?>">Notes</a></li>

                        </ul>
                    </div>
                    <div id ="status-message-li" class="ui-corner-all" style="margin-top: 10px; width:15em;"> 
                                    
                    </div>
                    <input id="supval" type="hidden" value=""/>
                    <input id="warehouseval" type="hidden" value=""/>
                    <input id="pricelist_id_hidden" type="hidden" value=""/>
                    <input id="inherit_rules" type="hidden" value=""/>
                    <input id="inherit_rules_dir" type="hidden" value=""/>
                    <input id="reqdateval" type="hidden" value=""/>
                    <input id="notesval" type="hidden" value=""/>
                    <div class="table-grid" style="padding-top:2em;">
                        <h1 id="pkdheader">Add Line Items</h1>
                        <table id="lineItems"><tr><td/></tr></table> 
                        <div id="pagerPk"></div>
                    </div>
                    <div  id="discountCntnr" class="inithide">
                        <fieldset style="border:1px solid #A6C9E2; margin-bottom:2em;" id="pricelist_rule_fs_quote">
                            <legend style="color: #2E6E9E;font-size: 110%;font-weight: bold;margin-left: 15px;">
                            Discounts On Total Quote </legend>  
                            <div class="row" style="width:100%;">
                                <div class="column triple-column" >
                                    <div class="field">
                                        <label for="dir_disc_total_quote_radio" class="labeldiv directlabel">Discount Amount w/Total:</label> 
                                        <input type="radio" id="dir_disc_total_quote_radio" name="pricerule_quote" value="directamounttotal" style="float:left; margin-right: 1em;">
                                        <input type="text" id="dir_disc_total_quote" name="dir_disc_total_quote" style="float:left; margin-right: 1em;" size="10">
                                    </div>
                                </div>
                                <div class="column triple-column" >
                                    <div class="field">
                                        <label for="dir_disc_perc_quote_radio" class="labeldiv directlabel">Discount % w/Total:</label> 
                                        <input type="radio" id="dir_disc_perc_quote_radio" name="pricerule_quote" value="directpercentage" style="float:left; margin-right: 1em;">
                                        <input type="text" id="dir_disc_perc_quote" name="dir_disc_perc_quote" style="float:left; margin-right: 1em;" size="10">
                                    </div>
                                </div>
                                <div class="column triple-column" style="width:16%;float:right;">
                                    <div class="field">
                                        <label for="none_quote" class="labeldiv directlabel">None:</label> 
                                        <input type="radio" id="none_quote" name="pricerule_quote" value="none_quote" style="float:left; margin-right: 1em;">
                                    </div>
                                </div>
                            </div>


                            <div class="ui-dialog-buttonpane shopifine-ui-widget-content ui-helper-clearfix">
                                <div class="shopifine-ui-dialog-buttonset">
                                    <button id="applyBtn_quote" type="button" class="shopifine-ui-button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false" style="margin-top:2em;">
                                        <span class="ui-button-text">Apply Discounts</span>
                                    </button>
                                </div> 
                            </div>
                        </fieldset>
                    
                        <div class="row">
                            <div class="column">
                                <div class="field">
                                    <div  class="labeldiv rolabel">Discount On Quote:</div>  
                                    <div id="discount_total_quote" name="discount_total_quote" class="valuediv"></div>

                                </div>
                            </div>

                            <div class="column">
                                <div class="field">
                                    <div  class="labeldiv rolabel">Total After Discount:</div>  
                                    <div id="amount_total_quote" name="amount_total_quote" class="valuediv"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </fieldset>
            </form>
        </div>
       <input id="quoteSubgridId" name ="quoteSubgridId" type="hidden" value=""/>
        <div id ="dialog-form-item" title="Add/Edit Line Item">
<!--            <h1 id="formHeader">Add New Line Item</h1>   -->
            <form id="itemForm">
                <fieldset>
                    <div class="row">
                        <div class="column item-column initshow">
                            <div class="field" style="margin:0 auto;width:60%">
                                <label for="productOp" class="labeldiv centrelabel">Products:</label>  
                                <select name="productOp" id ="productOp" class="required"> 
                                    <option value="">Choose 
                                        <?= $productOptions ?> 
                                </select>
                                <div class="valuediv valuediv-edit inithide" id="product_name_edit" name ="product_name_edit" style="display:none"></div>
                            </div>
                            
                        </div>

                    </div>
                    <div class="row inithide" id="pricelistNameCntnr">
                        
                        <div class="column item-column"  >
                            <div class="field" style="width:60%; margin:0 auto;">
                                <div class="labeldiv centrelabel" id="pricelist_name_label" name ="pricelist_name">Pricelist:</div>
                                <div class="valuediv" id="pricelist_name" name ="pricelist_name" style="width:55%"></div>
                            </div>
                            
                        </div>
                    </div>
                    <div class="row">
                        <div class="column initshow">
                            
                                <div class="field">
                                    <label for="quantity"  class="labeldiv">Quantity:</label>  
                                    <input id="quantity" name="quantity" class="calinput"/>
                                </div>
                            
                            
                        </div>
                        <div class="column">
                            <div class="field">
                                <label for="exPrice" class="labeldiv">Price:</label>  
                                <input id="exPrice" name="exPrice" class="calinput"/>
                                 
                                <input id="isOverride" name="isOverride" type="checkbox" class="inithide"/>
                                <div id ="override-help" class="ui-corner-all help-message-left inithide">
                                    (Select The Checkbox To Override Pricelist Price)
                                </div>
                            </div>
                        </div>
                        
                        
                    </div>
                    <fieldset style="border:1px solid #A6C9E2; margin-top:5px;" id="pricelist_rule_fs">
                        <legend style="color: #2E6E9E;font-size: 110%;font-weight: bold;margin-left: 15px;">
                        Price Rules </legend>  
                        <div class="row">
                            <div class="column table-column inithide" id="existingPRCntnr">
                                <div class="field">
                                    <label for="existingPR" class="labeldiv" style="width:13%">Existing Price Rule:</label>  
                                    <input type="radio" id="existingPR" name="pricerule" value="existing" style="float:left;margin-right: 1em;"/>
                                    <div class="table-grid" style="width:80%; clear:none; float:left;">

                                        <table id="rules_grid"><tr><td/></tr></table> 
                                        <div id="pagerRules"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="column" >
                                <div class="field">
                                    <label for="dir_disc_unit_radio" class="labeldiv directlabel">Direct Discount w/Unit:</label> 
                                    <input type="radio" id="dir_disc_unit_radio" name="pricerule" value="directamountunit" style="float:left; margin-right: 1em;" >
                                    <input type="text" id="dir_disc_unit" name="dir_disc_unit" style="float:left; margin-right: 1em;">
                              </div>
                            </div>
                            <div class="column" >
                                <div class="field">
                                    <label for="dir_disc_total_radio" class="labeldiv directlabel">Direct Discount w/Total:</label> 
                                    <input type="radio" id="dir_disc_total_radio" name="pricerule" value="directamounttotal" style="float:left; margin-right: 1em;">
                                    <input type="text" id="dir_disc_total" name="dir_disc_total" style="float:left; margin-right: 1em;">
                                </div>
                            </div>
                        </div>
                       
                        <div class="row">
                            <div class="column" >
                                <div class="field">
                                    <label for="dir_disc_perc_radio" class="labeldiv directlabel">Direct Discount Percentage:</label> 
                                    <input type="radio" id="dir_disc_perc_radio" name="pricerule" value="directpercentage" style="float:left; margin-right: 1em;">
                                    <input type="text" id="dir_disc_perc" name="dir_disc_perc" style="float:left; margin-right: 1em;">
                                </div>
                            </div>
                            <div class="column " >
                                <div class="field">
<!--                                    <label for="dir_disc_fg_radio" class="labeldiv directlabel" >Free Items:</label> 
                                    <input type="radio" id="dir_disc_fg_radio" name="pricerule" value="dirFG" style="float:left; margin-right: 1em;">
                                    <input type="text" id="dir_disc_fg" name="dir_disc_fg" style="float:left; margin-right: 1em;">-->
                                    <label for="nonePR" class="labeldiv directlabel">None:</label> 
                                    <input type="radio" id="nonePR" name="pricerule" value="nonePR" style="float:left; margin-right: 1em;">
                                </div>
                            </div>
                            
                        </div>
                        <div class="row">
                            <div class="column table-column" >
                                <div class="field" style="margin:0 auto;width:60%">
                                    <label for="dir_disc_fg" class="labeldiv centrelabel">Free Items:</label> 
<!--                                    <input type="radio" id="nonePR" name="pricerule" value="nonePR" style="float:left; margin-right: 1em;">-->
                                    <input type="text" id="dir_disc_fg" name="dir_disc_fg" style="float:left; margin-right: 1em;">
                                </div>
                            </div>
                        </div>
                        <div class="ui-dialog-buttonpane shopifine-ui-widget-content ui-helper-clearfix">
                            <div class="shopifine-ui-dialog-buttonset">
                                <button id="applyBtn" type="button" class="shopifine-ui-button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false">
                                    <span class="ui-button-text">Apply Discounts</span>
                                </button>
                            </div> 
                        </div>
                    </fieldset>
                     <div class="row">
                        
                        
                        <div class="column">
                            <div class="field">
                                <label for="unit_price_discount" class="labeldiv">Unit Price discount</label>  
                                <label id="unit_price_discount" name="unit_price_discount" class="valuediv"/>
                                
                            </div>
                        </div>
                        <div class="column">
                            <div class="field">
                                <label for="free_items" class="labeldiv">Free Items:</label>  
                                <label id="free_items" name="free_items" class="valuediv"/>
                                
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="column">
                            <div class="field">
                                <label for="unit_price_final" class="labeldiv">Final Unit Price:</label>  
                                <label id="unit_price_final" name="finalUnitPrice" class="valuediv"/>
                                
                            </div>
                        </div>
                        
                        <div class="column">
                            <div class="field">
                                <label for="discount_total" class="labeldiv">Discount Amount:</label>  
                                <label id="discount_total" name="discount_total" class="valuediv"/>
                                
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="column">
                            <div class="field">
                                <label for="items_total" class="labeldiv">Total Items:</label>  
                                <label id="items_total" name="items_total" class="valuediv"/>
                                
                            </div>
                        </div>
                        
                        <div class="column">
                            <div class="field">
                                <label for="amount_total" class="labeldiv">Total Price:</label>  
                                <label id="amount_total" name="amount_total" class="valuediv"/>
                                
                            </div>
                        </div>
                    </div>
                    
                    <div class="row ">
                         
                        <div class="column item-column ">
                            <div class="field" style="width:50%; margin:0 auto;">
                                 <label for="neededdate" class="labeldiv centrelabel">Needed By Date:</label>  
                                 <input id="neededdate" name ="neededdate" type="text"/>
                             </div>
                         </div>
                    </div>
                    <div class="row ">
                         
                        <div class="column item-column ">
                            <div class="field" style="width:50%; margin:0 auto;">
                                 <label for="notes_item" class="labeldiv centrelabel">Notes:</label>  
                                 <textarea id="notes_item" name ="notes_item" column="100" row="5"></textarea>
                             </div>
                         </div>
                    </div>
                   
                    <input id="line_id_hidden" name="line_id_hidden" type="hidden" value=""/>
                    <input id="action_item_hidden" name="action_item_hidden" type="hidden" value=""/>
                </fieldset>
            </form>
        </div>
        
        <div style="display: block;height: 100%;" class="shopifine-ui-dialog ui-widget ui-widget-content ui-corner-all" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-dialog-form">
            <?php $this->load->view("common/message"); ?>
            <div class="table-grid">
                <h1 id="table header"> Quotations</h1>
                <table id="quotes"><tr><td/></tr></table> 
                <div id="pager"></div>
            </div>
            
        </div>
        
        <?php $this->load->view("partial/footer"); ?>       
</body>   
</html>
<script>
   
    $(".calinput").change(function(){
        var qty = parseInt($("#quantity").val());
        if (isNaN(qty)){
            qty = 0;
        }
        var exprice =$("#quantity").val()*$("#exPrice").val();
        var free = parseInt($("#free_items").val());
        if (isNaN(free)){
            free =0;
        }
        if ($("#action_item_hidden").val()=='edit'){
            var existing_disc = parseInt($("#discount_total").text());
            $("#amount_total").text(exprice-existing_disc);
        }
        else{
            $("#amount_total").text(exprice);
            
        }
        $("#items_total").text(parseInt($("#quantity").val())+free);  
        
    });
    
    
</script>


    
   