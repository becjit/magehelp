<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php $this->load->view("common/header"); ?>
<script type="text/javascript">
$(document).ready(function()
{
	
        $("#deliverypointform").validate({
            rules :{
                password: {
                    minlength:5
                },
                confpassword: {
                    minlength:5,
                    equalTo:'#password'
                }
            }
        });
        
});
</script>
<style>
    * {
        font-size:100%;
    }
    .single-column-form{
        width:85%;
    }
    label.error {
        margin:0;
        width:20em;
    }
    .field {
        width:90%;
    }
</style>
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
                    <h1 id="formHeader">Profile Details</h1> 
                    <form id="deliverypointform">
                        <fieldset>
                            <div class="row single-column-row">
                                <div class="column single-column">
                                    <div class="field">
                                        <label for="username">User Name:</label>  
                                        <input id="username" name ="username" type="text" disabled="true" class="required" value='<?php echo $username ?>'/>  
                                    </div> 
                                </div>
                                <div class="column single-column">
                                    <div class="field">
                                        <label for="role">Role :</label>  
                                        <input id="role" name ="role" type="text" disabled="true" value='<?php echo $role_name ?>'/>  
                                    </div>
                                </div>
                            </div>
                            <div class="row single-column-row">
                                <div class="column single-column">
                                    <div class="field">
                                        <label for="password">Password:</label>  
                                        <input id="password" name ="password" type="password" value=""/>  
                                    </div> 
                                </div>
                                <div class="column single-column">
                                    <div class="field">
                                        <label for="confpassword">Confirm Password:</label>  
                                        <input id="confpassword" name ="confpassword" type="password" value=""/>  
                                        
                                    </div>
                                </div>
                            </div>
                             <div class="row single-column-row">
                                 <div class="column single-column">
                                    <div class="field">
                                        <label for="firstname">First Name:</label>  
                                        <input id="firstname" name ="firstname" type="text" class="required" value='<?php echo $first_name ?>'/>  
                                    </div>
                                </div>
                                <div class="column single-column">
                                    <div class="field">
                                        <label for="lastname">Last Name:</label>  
                                        <input id="lastname" name ="lastname" type="text" class="required" value='<?php echo $last_name ?>'/>
                                    </div> 
                                </div>
                                
                            </div>
                            <div class="row single-column-row">
                                <div class="column single-column">
                                    <div class="field">
                                        <label for="address1">Address 1:</label>  
                                        <input id="address1" name ="address1" type="text" class="required" value='<?php echo $address_1 ?>'/> 
                                    </div>
                                </div>
                                <div class="column single-column">
                                    <div class="field">
                                        <label for="address2">Address 1:</label>  
                                        <input id="address2" name ="address2" type="text" value='<?php echo $address_2 ?>'/>
                                    </div>
                                </div>
                            </div>
                            <div class="row single-column-row">
                                <div class="column single-column">
                                    <div class="field">
                                    <label for="city">City :</label>  
                                        <input id="city" name ="city" type="text"  value='<?php echo $city ?>'/> 
                                    </div> 
                                </div>
                                <div class="column single-column">
                                    <div class="field">
                                    <label for="state">State :</label>  
                                        <input id="state" name ="state" type="text"  value='<?php echo $state ?>'/>
                                    </div> 
                                </div>
                                <div class="column single-column">
                                    <div class="field">
                                        <label for="pin">PIN :</label>  
                                            <input id="pin" name ="pin" type="text" class="pin" value='<?php echo $zip ?>'/>
                                    </div>
                                </div>
                            </div>
                            <div class="row single-column-row">
                                <div class="column single-column">
                                    <div class="field"> 
                                        <label for="contactNumber">Contact Number :</label>  
                                            <input id="contactNumber" name ="contactNumber" type="text" class="required phonenumber" value='<?php echo $phone_number?>'/>     
                                    </div>
                                </div>
                                <div class="column single-column">
                                    <div class="field"> 
                                        <label for="email">Email :</label>  
                                            <input id="email" name ="email" type="text" class="required email" value='<?php echo $email ?>'/>
                                    </div>
                                </div>
                            </div>
                            <input id="person_id" name ="person_id" type="hidden" value='<?php echo $person_id ?>'/>
                        </fieldset>
                    </form>
                </div>
                <div class="ui-dialog-buttonpane shopifine-ui-widget-content ui-helper-clearfix">
                    <div class="shopifine-ui-dialog-buttonset">
                        <button id="updateBtn" type="button" class="shopifine-ui-button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false">
                            <span class="ui-button-text">Update Profile</span>
                        </button>
                    </div> 
                </div>
            </div>
            
        </div>
        <?php $this->load->view("partial/footer"); ?>

</body>
</html>
<script>
   $("#updateBtn").click (function (){
       $.ajax({
           type:"POST",
           url:"index.php/home/update",
           data:$("#deliverypointform").serialize(),
           success: function (){
               alert("suceess");
           },
           error: function (){
               alert("error");
           }
       })
       
   })
</script>
