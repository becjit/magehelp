//(function($) {
//    $.widget("ui.combobox", {
//        _create: function() {
//            var self = this,
//                select = this.element.hide(),
//                selected = select.children(":selected"),
//                value = selected.val() ? selected.text() : "";
//            var input = this.input = $("<input>").insertAfter(select).val(value).autocomplete({
//                delay: 0,
//                minLength: 0,
//                source: function(request, response) {
//                    var matcher = new RegExp($.ui.autocomplete.escapeRegex(request.term), "i");
//                    response(select.children("option").map(function() {
//                        var text = $(this).text();
//                        if (this.value && (!request.term || matcher.test(text))) return {
//                            label: text.replace(
//                            new RegExp("(?![^&;]+;)(?!<[^<>]*)(" + $.ui.autocomplete.escapeRegex(request.term) + ")(?![^<>]*>)(?![^&;]+;)", "gi"), "<strong>$1</strong>"),
//                            value: text,
//                            option: this
//                        };
//                    }));
//                },
//                select: function(event, ui) {
//                    ui.item.option.selected = true;
//                    self._trigger("selected", event, {
//                        item: ui.item.option
//                    });
//                },
//                change: function(event, ui) {
//                    if (!ui.item) {
//                        var matcher = new RegExp("^" + $.ui.autocomplete.escapeRegex($(this).val()) + "$", "i"),
//                            valid = false;
//                        select.children("option").each(function() {
//                            if ($(this).text().match(matcher)) {
//                                this.selected = valid = true;
//                                return false;
//                            }
//                        });
//                        if (!valid) {
//                            // remove invalid value, as it didn't match anything
//                            $(this).val("");
//                            select.val("");
//                            input.data("autocomplete").term = "";
//                            return false;
//                        }
//                    }
//                }
//            }).addClass("ui-widget ui-widget-content ui-corner-left");
//            input.data("autocomplete")._renderItem = function(ul, item) {
//                return $("<li></li>").data("item.autocomplete", item).append("<a>" + item.label + "</a>").appendTo(ul);
//            };
//            this.button = $("<button type='button'>&nbsp;</button>").attr("tabIndex", -1).attr("title", "Show All Items").insertAfter(input).button({
//                icons: {
//                    primary: "ui-icon-triangle-1-s"
//                },
//                text: false
//            }).removeClass("ui-corner-all").addClass("ui-corner-right ui-button-icon").click(function() {
//                // close if already visible
//                if (input.autocomplete("widget").is(":visible")) {
//                    input.autocomplete("close");
//                    return;
//                }
//                // work around a bug (likely same cause as #5265)
//                $(this).blur();
//                // pass empty string as value to search for, displaying all results
//                input.autocomplete("search", "");
//                input.focus();
//            });
//        },
//        destroy: function() {
//            this.input.remove();
//            this.button.remove();
//            this.element.show();
//            $.Widget.prototype.destroy.call(this);
//        }
//    });
//})(jQuery);

     
            (function( $ ) {
                $.widget( "ui.combobox", {
                    options: {
                        strict: false,
                        customChange:null
                            
                    },
                    _create: function() {
                        
                             
                        var input,
                        self = this,
                        id = this.element[0].id + "-input",
                        select = this.element.hide(),
                        selected = select.children( ":selected" ),
                        value = selected.val() ? selected.text() : "",
                        strict = this.options.strict,
                                        
                        wrapper = this.wrapper = $( "<span>" )
                        .addClass( "ui-combobox" )
                        .insertAfter( select );

                        input = $( "<input>" ).attr("id",id)
                        .appendTo( wrapper )
                        .val( value )
                        .addClass( "ui-state-default ui-combobox-input" )
                        .autocomplete({
                            delay: 0,
                            minLength: 0,
                            source: function( request, response ) {
                                var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
                                response( select.children( "option" ).map(function() {
                                    var text = $( this ).text();
                                    if ( this.value && ( !request.term || matcher.test(text) ) )
                                        return {
                                            label: text.replace(
                                            new RegExp(
                                            "(?![^&;]+;)(?!<[^<>]*)(" +
                                                $.ui.autocomplete.escapeRegex(request.term) +
                                                ")(?![^<>]*>)(?![^&;]+;)", "gi"
                                        ), "<strong>$1</strong>" ),
                                            value: text,
                                            option: this
                                        };
                                }) );
                            },
                            
                            select: function( event, ui ) {
                                
                                ui.item.option.selected = true;
                               
                                self._trigger( "selected", event, {
                                                            
                                    item: ui.item.option
                                });
                            },
                            change: function( event, ui ) {
                                //self.off();
                                                 
                                if ( !ui.item ) {
                                                                
                                    var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( $(this).val() ) + "$", "i" ),
                                    valid = false;
                                    select.children( "option" ).each(function() {
                                        if ( $( this ).text().match( matcher ) ) {
                                            this.selected = valid = true;
                                            return false;
                                        }
                                    });
                                    if ( !valid ) {
                                        // remove invalid value, as it didn't match anything
                                        select.val( "" );
                                        
                                        if (!strict) {
                                            callback = self.options.customChange;
                                            if ($.isFunction(callback)){
                                               
                                                    callback();
                                            }
                                            return;
                                        }
                                        $( this ).val( "" );
                                        input.data( "autocomplete" ).term = "";
                                        return false;
                                    }
                                }
                                callback = self.options.customChange;
                                if ($.isFunction(callback)){
                                    callback();
                                }
                                                        
                            }
                        })
                        .addClass( "ui-widget ui-widget-content ui-corner-left" ).keypress(function (e){
                        //console.log(e.which);
                        if (e.which== 13){
                           //$(".ui.combobox").trigger("autocompletechange");
                           //autocomplete
                           //$("#mfrOp").combobox().trigger("comboboxkeypress","");
                           //console.log($("#mfrOp").combobox());
                           //console.log( $(this).data( "autocomplete" ));
                           $(this).data( "autocomplete" )._trigger("change");
                           //$("#mfrOp").combobox("keypress");
                        }
                        
                        
                        });

                        input.data( "autocomplete" )._renderItem = function( ul, item ) {
//                            console.log("item label " + item.label);
                            return $( "<li></li>" )
                            .data( "item.autocomplete", item )
                            .append( "<a style>" + item.label + "</a>" )
                            .appendTo( ul );
                        };

                        $( "<a>" )
                        
                        .attr( "tabIndex", -1 )
                        .attr( "title", "Show All Items" )
                        .appendTo( wrapper )
                        .button({
                            icons: {
                                primary: "ui-icon-triangle-1-s"
                            },
                            text: false
                        })
                        .removeClass( "ui-corner-all" )
                        .addClass( "ui-corner-right ui-combobox-toggle" )
                        .click(function() {
                            // close if already visible
                            if ( input.autocomplete( "widget" ).is( ":visible" ) ) {
                                input.autocomplete( "close" );
                                return;
                            }

                            // work around a bug (likely same cause as #5265)
                            $( this ).blur();

                            // pass empty string as value to search for, displaying all results
                            input.autocomplete( "search", "" );
                            input.focus();
                        });
                    },
                    setselected: function (val){
                        
                        $("#"+this.element[0].id).val(val);
                        $("#"+this.element[0].id+'-input').val($("#"+this.element[0].id ).find(':selected').text())

//                       
                    },
                    destroy: function() {
                        this.wrapper.remove();
                        this.element.show();
                        $.Widget.prototype.destroy.call( this );
                    },
                    disable: function() {
                        
                        this.element.attr("disabled",true);
                        var id = this.element[0].id + "-input";
                        
                        $("#"+id).attr("disabled",true);
                        
                        
                    }
                   
                });
               
                $.validator.addMethod("phonenumber", function  (value,element){
                   var success =  /^(\d{7}(\d{1,7})?)?$/.test(value);
                   if (!success) 
                        return false;
                    else 
                        return [true,""];
                },"Invalid Phone. Phone number should be minimum 7 or maximum 14 digits");
                $.validator.addMethod("pin", function  (value,element){
                    var success = /^\d{5}$/.test(value);
                    
                    if (!success) 
                        return false;
                    else 
                        return true
                },"Invalid Pincode. Please enter valid one e.g. '560059' ");
                
                $.validator.addMethod("dateValidate", function(value, element) {
                   
                      var currVal = value;
                      if(currVal == '')
                        return false;

                      //Declare Regex  
                      var rxDatePattern = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{4})$/; 
                      var dtArray = currVal.match(rxDatePattern); // is format OK?

                      if (dtArray == null)
                         return false;

                      //Checks for mm/dd/yyyy format.
                      var dtDay = dtArray[1];
                      var dtMonth= dtArray[3];
                      var dtYear = dtArray[5];
                      

                      if (dtMonth < 1 || dtMonth > 12)
                          return false;
                      else if (dtDay < 1 || dtDay> 31)
                          return false;
                      else if ((dtMonth==4 || dtMonth==6 || dtMonth==9 || dtMonth==11) && dtDay ==31)
                          return false;
                      else if (dtMonth == 2)
                      {
                         var isleap = (dtYear % 4 == 0 && (dtYear % 100 != 0 || dtYear % 400 == 0));
                         if (dtDay> 29 || (dtDay ==29 && !isleap))
                              return false;
                      }
                      return true;
                    

                }, "Please Enter A Valid date");
                $.validator.addMethod('minStrict', function (value, el, param) {
                    return this.optional(el) || value > param;
                },"Price must be more than {0}");
                $.validator.addMethod('noBlank', function (value, el, param) {
                    if ($.trim(value).length==0){
                        return false;
                    }
                    return true;
                },"Blank values are not acceptable. Use 0 instead");
                 
            })( jQuery );




function showSuccessMessage (successStatus){
    $("#status-message").addClass("ui-state-highlight");
    $("#status-message > p > span").addClass("ui-icon");
    $("#status-message > p > span").addClass("ui-icon-info");
    $("#status-message > p").append(successStatus +'</br>');
}

function showErrorMessage (errorStatus){
    $("#status-message").addClass("ui-state-error");
    $("#status-message > p > span").addClass("ui-icon");
    $("#status-message > p > span").addClass("ui-icon-alert");
    $("#status-message > p").append(errorStatus +'</br>');
}

function emptyMessages (){
    $("#status-message").removeClass("ui-state-error");
    $("#status-message").removeClass("ui-state-highlight");
    $("#status-message > p > span").removeClass("ui-icon");
    $("#status-message > p > span").removeClass("ui-icon-alert");
    $("#status-message > p").empty();
}  

function showSuccessMessageGen (successStatus,element){
    $("#"+element).addClass("ui-state-highlight");
    $("#" +element +" > p > span").addClass("ui-icon");
    $("#" +element +" > p > span").addClass("ui-icon-info");
    $("#" +element +" > p").append(successStatus +'</br>');
}

function showErrorMessageGen (errorStatus,element){
    $("#" +element).addClass("ui-state-error");
    $("#" +element +" > p > span").addClass("ui-icon");
    $("#" +element +" > p > span").addClass("ui-icon-alert");
    $("#" +element +" > p").append(errorStatus +'</br>');
}

function emptyMessagesGen (element){
    $("#" +element).removeClass("ui-state-error");
    $("#" +element).removeClass("ui-state-highlight");
    $("#" +element +" > p > span").removeClass("ui-icon");
    $("#" +element +" > p > span").removeClass("ui-icon-alert");
    $("#" +element +" > p").empty();
}  