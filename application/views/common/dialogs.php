<div id="warningCntnr">
     <div id="modal-warning-morethanone" title="Warning" style="display:none;">
        <p>More Than One Row  Selected.Please Select Exactly One  Row</p>
    </div>
    <div id="modal-warning-one" title="Warning" style="display:none;">
        <p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>No Row Selected .Please Select Exactly One Row</p>
    </div>
     <div id="modal-warning-status" title="Error" class="ui-state-error" style="border:none; display:none;">
        <p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>Only Completely Received Orders i.e. With Status "Received" 
            Can Be Readied For Invoice</p>
    </div>
    <div id="modal-warning-none" title="Warning" style="display:none;">
        <p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>Please Select Record(s) To Continue</p>
    </div> 
    <div id="modal-warning-exactly-one" title="Warning" style="display:none;">
        <p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>Please Select Exactly One Row</p>
    </div>
    <div id="modal-warning-general" title="Warning">
        <p id="status_warning_text"></p>
    </div>
</div>

<div id ="dialog-payment" style="display:none">
            
            <h1 id="formHeader">Payment Details</h1>   
            <form id="paymentForm">
                <fieldset>
                   
                    <div class="row single-column-row inithide" id="advancePaymentListCntnr">
                         <div class="column quote-column single-column">
                             <div class="field">
                                 <label for="advance_ref" class="labeldiv">Advance Reference 1:</label>  
                                 <select id="advance_ref" name ="advance_ref" class="required">
                                     <option value="">Choose
                                     <?= $advanceOptions ?> 
                                 </select>

                             </div>
                         </div>                        
                     </div> 
                    
                    <div class="inithide paymentCntnr" id="advancePaymentAmountCntnr">
                        <div class="row single-column-row">
                            <div class="column quote-column single-column">
                                <div class="field">
                                    <div class="labeldiv">Advance Amount Paid:</div>  
                                    <div class="valuediv" id="advance_value" name="advance_value" ></div>  
                                    <div id ="advance-help" class="ui-corner-all help-message">
                                        (Total Advance Already Paid For The Associated Order:)
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="row single-column-row">
                             <div class="column quote-column single-column">
                                <div class="field">
                                    <div class="labeldiv">Already Invoiced Advance:</div>  
                                    <div class="valuediv" id="adjusted_advance" name="adjusted_advance" ></div>  
                                    <div id ="adjusted-advance-help" class="ui-corner-all help-message">
                                        (This Amount Already Been Adjusted Against Other Invoices. Thus Not Available For This Adjustment:)
                                    </div>
                                </div>

                            </div>
                        </div>
                        
                    </div>
                    
                    
                    <div id="generalPaymentCntnr" class="paymentCntnr">
                        <div class="row single-column-row">
                            <div class="column quote-column single-column">
                                <div class="field">
                                    <div class="labeldiv">Total Value:</div>  
                                    <div class="valuediv" id="total_value_form" name="total_value_form" ></div>                               
                                </div>

                            </div>                        
                        </div>
                        <div class="row single-column-row" >
                            <div class="column quote-column single-column">
                                <div class="field">
                                    <div class="labeldiv" id="balanceAmount" >Amount Paid:</div>  
                                    <div class="valuediv" id="amount_paid_form" name="amount_paid_form" ></div>                               
                                </div>

                            </div>                        
                        </div>
                    </div>
                    
                    <div id="commonPaymentCntnr"class="paymentCntnr">
                          <div class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="payment_mode" class="labeldiv">Payment Mode:</label>  
                                <select id="payment_mode" name ="payment_mode" class="required">
                                    <option value="">Choose..</option>
                                    <option value="cash">Cash</option>
                                    <option value="cheque">Cheque</option>
                                    <option value="draft">Draft</option>
                                    <option value="online">Online</option>
                                </select>
                                
                            </div>
                        </div>                        
                    </div>
                    <div class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="payment_ref" class="labeldiv">Payment Reference:</label>  
                                <input id="payment_ref" name="payment_ref" class="required"/>
                                <div id ="receipt-help" class="ui-corner-all help-message">
                                    (Provide Cheque Number/Draft Number/Online Transaction Id)
                                </div>
                            </div>
                        </div>                        
                    </div>
                    
                    
                    <div class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="amount" class="labeldiv">Amount:</label>  
                                <input id="amount" name="amount" />
                                
                            </div>
                        </div>                        
                    </div>
                     <div class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="comments" class="labeldiv">Bank Details:</label>  
                                <textarea id="bankcomments" name="bankcomments" row="5" col="40"></textarea>
                                <div id ="receipt-help" class="ui-corner-all help-message">
                                    (Provide Account # , Branch Etc:)
                                </div>
                            </div>
                        </div>                        
                    </div>
                    </div>

                  
                    <input id="oper_payment" name="oper_payment" type="hidden" value=""/>
                    <input id="type_payment" name="type_payment" type="hidden" value=""/>
                    <input id="payment_id" name="payment_id" type="hidden" value=""/>
                    <input id="prev_amount" name="prev_amount" type="hidden" value="0"/>
                    <input id="parent_id" name="parent_id" type="hidden" value=""/>
                </fieldset>
            </form>
        </div>

<div id="modal-error-total-payment" title="Error" style="border:none; display:none;">
    <p>Total Amount Paid Is Same As Invoiced Amount. No More Payment Allowed</p>
</div>

<div id="modal-error-advance-payment" title="Error" style="border:none; display:none;">
    <p>There Is no Advance Payment Available For Order</p>
</div>

<div id ="comment-quote-dialog" style="overflow:hidden;" title="Relevant Comments" class="inithide">
    
    <div class="row single-column-row initshow" id ="genNotesCntnr">
        <div class="column base-column">
            <div class="labeldiv" id="notes_comment_quote_label" >Notes:</div>  
            <pre class="valuediv" id="notes_comment_quote" ></pre> 
        </div>
    </div>
    <div class="row single-column-row initshow" id ="approvalNotesCntnr">
        <div class="column base-column">
            <div class="labeldiv" id="approval_notes_comment_quote_label" >Approval Notes:</div>  
            <pre class="valuediv" id="approval_notes_comment_quote" ></pre> 
        </div>
    </div>
    <div class="row single-column-row inithide" id ="receivingNotesCntnr">
        <div class="column base-column">
            <div class="labeldiv" id="receiving_notes_comment_quote_label" >Receiving Notes:</div>  
            <pre class="valuediv" id="receiving_notes_comment_quote" ></pre> 
        </div>
    </div>

</div>
<div id ="assignment-common-dialog" style="overflow:hidden;" title="Assign" class="inithide">
    <form id="assignment-common-form">
         <div class="row single-column-row inithide quoteassign incominginvassign" id="currentOwnerCntnr">
            <div class="column base-column ">
                <div class="labeldiv" id="current_owner_label" >Current Owner:</div>  
                <div class="valuediv" id="current_owner" ></div> 
            </div>
        </div> 
        <div class="row single-column-row inithide quoteassign approveAssign" id="currentApproverCntnr">
            <div class="column base-column">
                <div class="labeldiv" id="current_approver_label" >Current Approver:</div>  
                <div class="valuediv" id="current_approver" ></div> 
            </div>
        </div> 
        <div class="row single-column-row inithide orderassign" id="currentReceiverCntnr">
            <div class="column base-column">
                <div class="labeldiv" id="current_receiver_label" >Current Receiver:</div>  
                <div class="valuediv" id="current_receiver" ></div> 
            </div>
        </div> 
        <div class="row single-column-row inithide invoiceassign" id="currentReceiverCntnr">
            <div class="column base-column">
                <div class="labeldiv" id="current_payer_label" >Current Payer:</div>  
                <div class="valuediv" id="current_payer" ></div> 
            </div>
        </div> 

        <div class="row single-column-row inithide quoteassign" id="roleAssignQuoteCntnr">
            <div class="column base-column">
                <div class="labeldiv" id="role_label" >Assign:</div>  
               <select id="role_assign" name ="role" class="required">
                    <option value="">Choose..</option>
                    <option value="approver">Approver</option>
                    <option value="owner">Owner</option>
                    
                </select>
            </div>
        </div>
        
<!--         <div class="row single-column-row inithide" id="roleAssignPOCntnr">
            <div class="column base-column">
                <div class="labeldiv" id="role_label_po" >Assign:</div>  
               <select id="role_assign_po" name ="role" class="required">
                    <option value="">Choose..</option>
                    <option value="receiver">Receiver</option>
                    <option value="owner">Owner</option>

                </select>
            </div>
        </div>-->

        <div class="row single-column-row inithide quoteassign approveAssign" id="adminUserCntnr_assign">
            <div class="column base-column">
                <div class="labeldiv" id="adminUser_label" >Approvers:</div>  
                <select id="adminUser_assign" name ="adminUsers" class="required userOptions">
                    <option value="">Choose
                    
                </select>
            </div>
        </div>
         
        <div class="row single-column-row inithide quoteassign orderassign incominginvassign" id="userCntnr_assign">
            <div class="column base-column">
                <div class="labeldiv" id=user_label" >Users:</div>  
                <select id="user_assign" name ="users" class="required userOptions">
                    <option value="">Choose
                    
                </select>
            </div>
        </div>
        <input type="hidden" id="entity_hidden" name="entity_hidden"/>
    </form>
    
   
    
    

</div>
<div id ="delete-common-dialog" style="overflow:hidden;" class="inithide" title="Cancel">

</div>

<div id ="dialog-notes-common" style="display:none">
            
            <h1 id="formHeader">Add Notes</h1>   
            <form id="commonNotesForm">
                <fieldset>
                   
                   
                     <div class="row single-column-row">
                        <div class="column quote-column single-column">
                            <div class="field">
                                <label for="comments_common" class="labeldiv">Notes:</label>  
                                <textarea id="comments_common" name="comments_common" row="5" col="40"></textarea>
                                
                            </div>
                        </div>                        
                    </div>
                    
                    <input id="entity_id_hidden" name="entity_id_hidden" type="hidden" value=""/>
                    <input id="entity_hidden" name="entity_hidden" type="hidden" value=""/>
                    <input id="note_type_hidden" name="note_type_hidden" type="hidden" value=""/>
                    
                </fieldset>
            </form>
        </div>