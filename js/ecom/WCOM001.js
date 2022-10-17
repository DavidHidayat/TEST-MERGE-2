$(document).ready(function() {
    
    /**
     * =========================================================================================================
     * DEFINE DIALOG
     * =========================================================================================================
     **/
    
    /**
     * =========================================================================================================
     * BUTTON FUNCTION
     * =========================================================================================================
     **/
    $("#btn-save").click(function() {
        /** Save confirmation */
        $("#dialog-confirm-save").html("Do you want reset password?");
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
                    var userId  = $.trim($("#userId").val());
                    var email   = $.trim($("#email").val());
                    edata = "userIdPrm="+userId+"&emailPrm="+email;
                    $.ajax({
                        type: 'GET',
                        url: '../db/MASTER/EPS_M_USER.php?action=ResetPassword',
                        data: edata,
                        success: function(data){
                            //console.log($.trim(data));
                            $("div#mandatory-msg-1.alert").css('display','none');
                            $("div#mandatory-msg-2.alert").css('display','none');
                            $("div#undefined-msg.alert").css('display','none');
                            $("div#save-msg.alert").css('display','none');
                            if($.trim(data) == 'Success')
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#mandatory-msg-2.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','none');
                                $("div#save-msg.alert").css('display','block');
                            }
                            else if($.trim(data) == 'Mandatory_1')
                            {
                                $("div#mandatory-msg-1.alert").css('display','block');
                                $("div#mandatory-msg-2.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','none');
                                $("div#save-msg.alert").css('display','none');
                            }
                            else if($.trim(data) == 'Mandatory_2')
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#mandatory-msg-2.alert").css('display','block');
                                $("div#undefined-msg.alert").css('display','none');
                                $("div#save-msg.alert").css('display','none');
                            }
                            else
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#mandatory-msg-2.alert").css('display','none');
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
   
});


