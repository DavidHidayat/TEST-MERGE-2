$(document).ready(function() {
    
    
    /**
     * =========================================================================================================
     * INITIAL VALUE
     * =========================================================================================================
     **/
    
    /**
     * =========================================================================================================
     * INPUT FUNCTION
     * =========================================================================================================
     **/
    
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
    $("#btn-sign-in").click(function(){
        var edata;
        var userId      = $.trim($("#userId").val());
        var password    = $.trim($("#password").val());
        edata = "userIdPrm="+userId+"&passwordPrm="+password
        
        $.ajax({
            type: 'GET',
            url: 'db/Login/WEB_LOGIN.php',
            data: edata,
            success: function(data){
                //console.log($.trim(data));
                $("div#mandatory-msg-1.alert").css('display','none');
                $("div#mandatory-msg-2.alert").css('display','none');
                $("div#mandatory-msg-3.alert").css('display','none');
                $("div#mandatory-msg-4.alert").css('display','none');
                $("div#mandatory-msg-5.alert").css('display','none');
                $("div#mandatory-msg-6.alert").css('display','none');
                $("div#undefined-msg.alert").css('display','none');
                if($.trim(data) == 'Success')
                {
                    window.location = "ecom/WCOM002.php";
                }
                else if($.trim(data) == 'Mandatory_1')
                {
                    $("div#mandatory-msg-1.alert").css('display','block');
                    $("div#mandatory-msg-2.alert").css('display','none');
                    $("div#mandatory-msg-3.alert").css('display','none');
                    $("div#mandatory-msg-4.alert").css('display','none');
                    $("div#mandatory-msg-5.alert").css('display','none');
                    $("div#mandatory-msg-6.alert").css('display','none');
                    $("div#undefined-msg.alert").css('display','none');
                }
                else if($.trim(data) == 'Mandatory_2')
                {
                    $("div#mandatory-msg-1.alert").css('display','none');
                    $("div#mandatory-msg-2.alert").css('display','block');
                    $("div#mandatory-msg-3.alert").css('display','none');
                    $("div#mandatory-msg-4.alert").css('display','none');
                    $("div#mandatory-msg-5.alert").css('display','none');
                    $("div#mandatory-msg-6.alert").css('display','none');
                    $("div#undefined-msg.alert").css('display','none');
                }
                else if($.trim(data) == 'Mandatory_3')
                {
                    $("div#mandatory-msg-1.alert").css('display','none');
                    $("div#mandatory-msg-2.alert").css('display','none');
                    $("div#mandatory-msg-3.alert").css('display','block');
                    $("div#mandatory-msg-4.alert").css('display','none');
                    $("div#mandatory-msg-5.alert").css('display','none');
                    $("div#mandatory-msg-6.alert").css('display','none');
                    $("div#undefined-msg.alert").css('display','none');
                }
                else if($.trim(data) == 'Mandatory_4')
                {
                    $("div#mandatory-msg-1.alert").css('display','none');
                    $("div#mandatory-msg-2.alert").css('display','none');
                    $("div#mandatory-msg-3.alert").css('display','none');
                    $("div#mandatory-msg-4.alert").css('display','block');
                    $("div#mandatory-msg-5.alert").css('display','none');
                    $("div#mandatory-msg-6.alert").css('display','none');
                    $("div#undefined-msg.alert").css('display','none');
                }
                else if($.trim(data) == 'Mandatory_5')
                {
                    $("div#mandatory-msg-1.alert").css('display','none');
                    $("div#mandatory-msg-2.alert").css('display','none');
                    $("div#mandatory-msg-3.alert").css('display','none');
                    $("div#mandatory-msg-4.alert").css('display','none');
                    $("div#mandatory-msg-5.alert").css('display','block');
                    $("div#mandatory-msg-6.alert").css('display','none');
                    $("div#undefined-msg.alert").css('display','none');
                }
                else if($.trim(data) == 'Mandatory_6')
                {
                    $("div#mandatory-msg-1.alert").css('display','none');
                    $("div#mandatory-msg-2.alert").css('display','none');
                    $("div#mandatory-msg-3.alert").css('display','none');
                    $("div#mandatory-msg-4.alert").css('display','none');
                    $("div#mandatory-msg-5.alert").css('display','none');
                    $("div#mandatory-msg-6.alert").css('display','block');
                    $("div#undefined-msg.alert").css('display','none');
                }
                else 
                {
                    $("div#mandatory-msg-1.alert").css('display','none');
                    $("div#mandatory-msg-2.alert").css('display','none');
                    $("div#mandatory-msg-3.alert").css('display','none');
                    $("div#mandatory-msg-4.alert").css('display','none');
                    $("div#mandatory-msg-5.alert").css('display','none');
                    $("div#mandatory-msg-6.alert").css('display','none');
                    $("div#undefined-msg.alert").css('display','block');
                }
            }
        });     
    });
});


