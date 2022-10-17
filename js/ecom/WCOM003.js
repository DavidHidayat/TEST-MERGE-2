$(document).ready(function() {
    
    /**
     * =========================================================================================================
     * DEFINE DIALOG
     * =========================================================================================================
     **/
    /** Session confirmation */
    $("#dialog-confirm-session").html("<strong>Session expired!</strong> <br> Session timeout or user has not login. Please login again.");
    $("#dialog-confirm-session").dialog({
        autoOpen    : false,
        closeOnEscape   : false,
	height      : 175,
	width       : 400,
        position    : { my: "center", at: "top", of: $("body"), within: $("body") },
	modal       : true,
        open        : function() {                         // open event handler
            $(this)                                // the element being dialogged
                .parent()                          // get the dialog widget element
                .find(".ui-dialog-titlebar-close") // find the close button for this dialog
                .hide();                           // hide it
        },
        buttons     : {
            "Yes": function(){
                $(this).dialog("close");
                window.location = "../db/Login/Logout.php";
            }
        }
    });
    
    /**
     * =========================================================================================================
     * BUTTON FUNCTION
     * =========================================================================================================
     **/
    $("#btn-save").click(function() {
        /** Save confirmation */
        $("#dialog-confirm-save").html("Do you want update password?");
        $("#dialog-confirm-save").dialog({
            //autoOpen    : false,
            height      : 155,
            width       : 400,
            //position    : {my: "center", at: "top", of: $("body"), within: $("body")},
            modal       : true,
            buttons     : {
                "Yes": function(){ 
                    $(this).dialog("close"); 
                    var edata;
                    var oldPassword     = $.trim($("#oldPassword").val());
                    var newPassword     = $.trim($("#newPassword").val());
                    var reNewPassword   = $.trim($("#reNewPassword").val());
                    edata = "oldPasswordPrm="+oldPassword+"&newPasswordPrm="+newPassword+"&reNewPasswordPrm="+reNewPassword;
                    $.ajax({
                        type: 'GET',
                        url: '../db/MASTER/EPS_M_USER.php?action=UpdatePassword',
                        data: edata,
                        success: function(data){
                            //console.log($.trim(data));
                            $("div#mandatory-msg-1.alert").css('display','none');
                            $("div#mandatory-msg-2.alert").css('display','none');
                            $("div#mandatory-msg-3.alert").css('display','none');
                            $("div#mandatory-msg-4.alert").css('display','none');
                            $("div#undefined-msg.alert").css('display','none');
                            $("div#save-msg.alert").css('display','none');
                            if($.trim(data) == 'Success')
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#mandatory-msg-2.alert").css('display','none');
                                $("div#mandatory-msg-3.alert").css('display','none');
                                $("div#mandatory-msg-4.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','none');
                                $("div#save-msg.alert").css('display','block');
                                window.location = "../db/LOGIN/logout.php";
                            }
                            else if($.trim(data) == 'Mandatory_1')
                            {
                                $("div#mandatory-msg-1.alert").css('display','block');
                                $("div#mandatory-msg-2.alert").css('display','none');
                                $("div#mandatory-msg-3.alert").css('display','none');
                                $("div#mandatory-msg-4.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','none');
                                $("div#save-msg.alert").css('display','none');
                            }
                            else if($.trim(data) == 'Mandatory_2')
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#mandatory-msg-2.alert").css('display','block');
                                $("div#mandatory-msg-3.alert").css('display','none');
                                $("div#mandatory-msg-4.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','none');
                                $("div#save-msg.alert").css('display','none');
                            }
                            else if($.trim(data) == 'Mandatory_3')
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#mandatory-msg-2.alert").css('display','none');
                                $("div#mandatory-msg-3.alert").css('display','block');
                                $("div#mandatory-msg-4.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','none');
                                $("div#save-msg.alert").css('display','none');
                            }
                            else if($.trim(data) == 'Mandatory_4')
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#mandatory-msg-2.alert").css('display','none');
                                $("div#mandatory-msg-3.alert").css('display','none');
                                $("div#mandatory-msg-4.alert").css('display','block');
                                $("div#undefined-msg.alert").css('display','none');
                                $("div#save-msg.alert").css('display','none');
                            }
                            else
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#mandatory-msg-2.alert").css('display','none');
                                $("div#mandatory-msg-3.alert").css('display','none');
                                $("div#mandatory-msg-4.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','block');
                                $("div#save-msg.alert").css('display','none');
                            }
                        } 
                    });
                },
                "No": function(){
                    $(this).dialog("close");
                }
            }
        });
    });
    
    $("#btn-reset").click(function() {
        $("#oldPassword").val('');
		$("#newPassword").val('');
		$("#reNewPassword").val('');
    });
});


