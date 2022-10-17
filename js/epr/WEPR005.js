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
    /** Session confirmation */
    $("#dialog-confirm-session").html("<strong>Session expired!</strong> <br> Session timeout or user has not login. Please login again.");
    $("#dialog-confirm-session").dialog({
        autoOpen    : false,
        closeOnEscape   : false,
		height      : 175,
		width       : 400,
        position    : {my: "center", at: "top", of: $("body"), within: $("body")},
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
    $("#btn-takeover").click(function(){
        /** Approve confirmation */
        $("#dialog-confirm-takeover").html("Do you want takeover this PR?");
        $("#dialog-confirm-takeover").dialog({
            //autoOpen    : false,
            height      : 155,
            width       : 400,
            //position    : { my: "center", at: "top", of: $("body"), within: $("body") },
            modal       : true,
            buttons     : {
                "Yes": function(){
                    $(this).dialog("close");
                    
                    var eDataPr;
                    var prNo        = $.trim($("#prNo").val());
                    eDataPr = "prNoPrm="+prNo;
                    
                    $.ajax({
                        type: 'GET',
                        url: '../db/PR_/UPDATE_PR.php?action=TakeoverPr',
                        data: eDataPr,
                        success: function(data){
                            //alert(data);
                            if($.trim(data) == 'Success'){
                                $("div#undefined-msg.alert").css('display','none');
                                $("div#session-msg.alert").css('display','none');
                                $("div#success-msg.alert.alert-success").css('display','block');

                                $("button#btn-takeover.btn.btn-primary").attr('disabled', true);
                                
                                window.location = "../epr_/WEPR013.php";
                            }
                            else if($.trim(data) == 'SessionExpired')
                            {
                                $("#dialog-confirm-session").dialog('open');
                            }
                            else
                            {
                                $("div#undefined-msg.alert").css('display','block');
                                $("div#session-msg.alert").css('display','none');
                            }
                        }
                    });
                },
                "No": function(){
                    $(this).dialog("close");
                }
            }
        });
        //$("#dialog-confirm-approve").dialog("open");
    });
    
    $("#btn-back").click(function(){
        window.location = "WEPR013.php";       
    });
    
});


