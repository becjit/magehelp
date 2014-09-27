
function initDialogs(dialogs){
    if (dialogs.one==true){
        $( "#modal-warning-one" ).show();
        $( "#modal-warning-one" ).dialog({
                autoOpen:false,
                height: 90,
                modal: true
        });
    }
    if (dialogs.none==true){
        $( "#modal-warning-none" ).show();
        $( "#modal-warning-none" ).dialog({
                autoOpen:false,
                height: 90,
                modal: true
        });
    }
    if (dialogs.morethanone==true){
        $( "#modal-warning-morethanone" ).show();
        $( "#modal-warning-morethanone" ).dialog({
                autoOpen:false,
                height: 90,
                modal: true
        });
    }
    if (dialogs.exactlyone==true){
        $( "#modal-warning-exactly-one" ).show();
        $( "#modal-warning-exactly-one" ).dialog({
                autoOpen:false,
                height: 90,
                modal: true
        });
    }
 
    if (dialogs.status==true){
        $( "#modal-warning-status" ).show();
        $( "#modal-warning-status" ).dialog({
            autoOpen:false,
            height: 120,
            modal: true
        });
    }
    $( "#modal-warning-general" ).dialog({
        autoOpen:false,
        height: 120,
        modal: true
    });
}

function initPaymentDialog(){
    $( "#dialog-payment" ).show();
     $( "#dialog-payment" ).dialog({
            autoOpen: false,
            height: 'auto',
            width: '35%',
            position:[450,25],
            modal: true,
            buttons: {
                "Register Payment": function() {
                    //default form name
                   var form_name = 'paymentForm';
                   var grid_form = $(this).data('grid_data').form_name;
                   if (grid_form!="" && grid_form!=undefined && grid_form!=null){
                        form_name =    '#'+$(this).data('grid_data').form_name;
                    }
                   var isvalid = $("#"+form_name).valid();
                   var invoice_id = $(this).data('grid_data').invoice_id;
                   var order_id = $(this).data('grid_data').order_id;
                   var amount = parseFloat($("#amount").val());
                   var prev_amount = parseFloat($("#prev_amount").val());
                   var oper =$("#oper_payment").val();
                   var amount_paid = parseFloat($("#amount_paid_form").text());
                   
                   if (isNaN(amount_paid) ){
                       amount_paid = 0;
                   }
                   var total = amount + amount_paid;
                   var total_invoiced = parseFloat($("#total_value_form").text());
                   if (isNaN(total_invoiced) ){
                       total_invoiced = 0;
                   }
                   if (oper=='edit'){
                       total = total - prev_amount;
                   }
                   console.log ("data " +" " + invoice_id);
                   if (isvalid){
                       $.ajax({
                           url:"index.php/procurement/registerPayment",
                           type:"POST",
                           data:{
                               invoice_id:invoice_id,
                               order_id:order_id,
                               form_data:$("#"+form_name).toObject(),
                               total_value:total,
                               total_invoiced:total_invoiced
                           },
                           success:function (response){
                               console.log("success" + response)
                               $("#amount_paid").text(response)
                               $("#payments").trigger("reloadGrid");                               
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
                var type = $(this).data("grid_data").type;
                var pay_id = $(this).data("grid_data").pay_id;
                var order_id = $(this).data("grid_data").order_id;
                $("#type_payment").val(type);
                if (type=='advance'){
                    $("#generalPaymentCntnr").hide();
                }
               
                if (oper=='add'){
                    $("#oper_payment").val(oper);
                }
                else if (oper=='edit'){
                    
                    $("#oper_payment").val(oper);
                    $("#payment_id").val(pay_id);
                    $("#payment_ref").val($("#payments").getCell(pay_id,'payment_reference'));
                    $("#payment_mode").val($("#payments").getCell(pay_id,'payment_mode'));
                    $("#comments").val($("#payments").getCell(pay_id,'comments'));
                    $("#amount").val($("#payments").getCell(pay_id,'amount'));
                    $("#prev_amount").val($("#payments").getCell(pay_id,'amount'));
                    $("#parent_id").val($("#payments").getCell(pay_id,'parent_id'));
                    $("#payment_ref").attr('readonly','readonly');
                    $("#payment_mode").attr('readonly','readonly');
                }
                if (type=='assign'){
                    $("#advancePaymentListCntnr").show();
                    $(".paymentCntnr").hide();
                    
                    //$("#commonPaymentCntnr input").attr("readonly","readonly");
                   
                }
                $("#total_value_form").text($("#total_value").text());
                $("#amount_paid_form").text($("#amount_paid").text());
                
            },
            close: function(event,ui) {
                $("#paymentForm").data('validator').resetForm();
                $('#paymentForm')[0].reset();
                $('.inithide').hide();
                $(".paymentCntnr .valuediv").text("");
                $("#commonPaymentCntnr input").removeAttr("readonly");
                $("#payment_mode").removeAttr("readonly");
                $("#status-message-li").empty();
                $("#prev_amount").val("0");
            }
        });
        

        $("#payment_mode").change(function(){
            if ($(this).val()!='cash'){
                $("#comments").addClass("required");
                $("#payment_ref").addClass("required");
                $("#payment_ref").val("");
                $("#payment_ref").removeAttr("readonly");
            }
            else {
                $("#comments").removeClass("required");
                $("#payment_ref").removeClass("required");
                $("#payment_ref").val("Cash");
                $("#payment_ref").attr("readonly","readonly");
            }
        })
      
}
function initErrorDialogs(dialogs){
    if (dialogs.total==true){
        $("#modal-error-total-payment").show();
        $( "#modal-error-total-payment" ).dialog({
                autoOpen:false,
                height: 80,
                modal: true
            });
    }
     if (dialogs.advance==true){
         $("#modal-error-advance-payment").show();
        $( "#modal-error-advance-payment" ).dialog({
            autoOpen:false,
            height: 80,
            modal: true
        });
     }
}
function initCommentsForQuote(){
     $("#comment-quote-dialog").show();
     $( "#comment-quote-dialog" ).dialog({
            autoOpen: false,
            height: 'auto',
            width: '480',
            position:[350,25],
            modal: true,
            buttons: {
                
                "Close": function() {
                    $(".inithide").hide();
                    $(".inithide").hide();
                    $( this ).dialog( "close" );
                }
            },
            open: function(event,ui){
                var app_notes = $(this).data('grid_data').approval_notes;
                var notes = $(this).data('grid_data').notes;
                var receiving_notes = $(this).data('grid_data').receiving_notes;
                var entity = $(this).data('grid_data').entity;
                $("#approval_notes_comment_quote").html(app_notes);
                
                $("#notes_comment_quote").html(notes);
                if (entity=='order'){
                    $("#receivingNotesCntnr").show();
                    $("#receiving_notes_comment_quote").html(receiving_notes);
                }
                else if (entity =='receipt_item'){
                    $("#receivingNotesCntnr").show();
                    $("#receiving_notes_comment_quote").html(receiving_notes);
                     $("#approval_notes_comment_quote").html($(this).data('grid_data').pp_approval_notes);
                     $("#genNotesCntnr").hide();
                }
                
            }
        });
}

function initAssignmentCommon(){
     $("#assignment-common-form").validate();
     $( "#assignment-common-dialog" ).dialog({
            autoOpen: false,
            height: 'auto',
            width: '350',
            position:[350,25],
            modal: true,
            buttons: {
                "Assign": function() {
                    //default form name
                   var isvalid = $("#assignment-common-form").valid();
                   var entity = $(this).data('grid_data').entity;
                   var rowid = $(this).data('grid_data').row_id;
                   var grid_id = $(this).data('grid_data').grid_id;
                   var user_id;
                   if (isvalid){
                       var role;
                       if (entity=='order'){
                           role='receiver';
                           user_id = $("#user_assign").val();
                       }
                       else if (entity =='incoming_invoice'){
                           role='owner';
                           user_id = $("#user_assign").val();
                        }
                       else if (entity=='invoice'){
                           role='payer';
                           user_id = $("#user_assign").val();
                       }
                       else if (entity =='receipt' || entity =='receipt_item'){
                           role='approver';
                           user_id = $("#adminUser_assign").val();
                        }
                       else{
                           role = $("#role_assign").val();
                            if (role=='approver'){
                                user_id = $("#adminUser_assign").val();
                            }
                            else if (role=='owner'){
                                user_id = $("#user_assign").val();
                            }
                       }
                           
                        $.ajax({
                            method:"POST",
                            url:"index.php/procurement/assignEntityToUser",
                            data: {ids : rowid,entity:entity,user_id:user_id,role:role},
                            success: function(){
                                $("#"+grid_id).trigger("reloadGrid");
                                emptyMessages();
                                showSuccessMessage("success");
                            }
                        });
                       $( this ).dialog( "close" );
                   }

                },
                "Close": function() {

                    $( this ).dialog( "close" );
                }
            },
            open: function(event,ui){
                var entity = $(this).data('grid_data').entity;
                var current_approver = $(this).data('grid_data').current_approver;
                var current_owner= $(this).data('grid_data').current_owner;
                var current_receiver= $(this).data('grid_data').current_receiver;
                $("#current_owner").text(current_owner);
                $("#current_approver").text(current_approver);
                $("#current_receiver").text(current_receiver);
                if (entity =='order'){
                    $(".orderAssign").show();
                }
                else if (entity =='invoice'){
                    $(".invoiceassign").show();
                }
                 else if (entity =='incoming_invoice'){
                    $(".incominginvassign").show();
                }
                else if (entity =='receipt_item' || entity =='receipt'){
                    $(".approveAssign").show();
                }
                else{
                    $(".quoteAssign").show();
                }
                $.ajax({
                           url:"index.php/procurement/prepareAssignDialog",
                           type:"POST",
                           data:{
                               entity:entity
                               
                           },
                           success:function (response){
                               var responseObj = JSON.parse(response); 
                               console.log(responseObj);
                               $("#adminUser_assign").append(responseObj.adminUserOptions);
                               $("#user_assign").append(responseObj.userOptions);
                           },
                           error :function (response){
                                                            
                           }
                           
                       })
            },
             close: function(event,ui) {
                $("#assignment-common-form").data('validator').resetForm();
                $('#assignment-common-form')[0].reset();
                $('#assignment-common-form .valuediv').text("");
                $('.inithide').hide();
                $('.userOptions option:gt(0)').remove(); 
            }
        });
         $("#role_assign").change(function(){
            if ($(this).val()=='approver'){
                $("#adminUserCntnr_assign").show();
                $("#userCntnr_assign").hide();
            }
            else if ($(this).val()=='owner'){
                $("#adminUserCntnr_assign").hide();
                $("#userCntnr_assign").show();
            }
        })
}

function initDeleteDialog(){
    $( "#delete-common-dialog" ).dialog({
            autoOpen: false,
            height: '20',
            width: '300',
            position:[350,25],
            modal: true,
            buttons: {
                "Cancel/Delete ?": function() {
                        var grid_id = $(this).data('grid_data').grid_id;
                        var url = $(this).data('grid_data').url;
                       $.ajax({
                           url:url,
                           type:"POST",
                           data:{
                               oper:'del',
                               id:$("#"+grid_id).getGridParam("selarrrow")
                              
                           },
                           success:function (response){
                               //console.log("grid " + grid);
                               $("#"+grid_id).trigger("reloadGrid");
                              
                           }
                       })
                       $( this ).dialog( "close" );
                   

                }
            }
           
            
        });
}

function initAddNotes(){
     
     $( "#dialog-notes-common" ).dialog({
            autoOpen: false,
            height: 'auto',
            width: '480',
            position:[350,25],
            modal: true,
            buttons: {
                
                "Add Comments": function() {
                    
                    var entity = $(this).data('grid_data').entity;
                    var note_type = $(this).data('grid_data').note_type;
                    var entity_id = $(this).data('grid_data').entity_id;
                    $.ajax({
                           url:"index,php/procurement/addCommentsForEntity",
                           type:"POST",
                           data:{
                               entity:entity,
                               note_type:note_type,
                               id:entity_id,
                               notes:$("#comments_common").text()
                              
                           },
                           success:function (response){
                              
                              
                           }
                       })
                       $( this ).dialog( "close" );
                }
            },
            open: function(event,ui){
//                var app_notes = $(this).data('grid_data').approval_notes;
//                var notes = $(this).data('grid_data').notes;
//                var receiving_notes = $(this).data('grid_data').receiving_notes;
//                var entity = $(this).data('grid_data').entity;
//                $("#entity_hidden").val()
                
            }
        });
}
