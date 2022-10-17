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
    $("#progressbar").hide();
    $("#btn-cn-report").click(function() {
        /** Save confirmation */
        $("#dialog-confirm-save").html("Do you want process?");
        $("#dialog-confirm-save").dialog({
            //autoOpen    : false,
            height      : 155,
            width       : 400,
            //position    : {my: "center", at: "top", of: $("body"), within: $("body")},
            modal       : true,
            buttons     : {
                "Yes": function(){ 
                    $(this).dialog("close"); 
                    $("#btn-cn-report").attr('disabled','disabled');
                    $("div#mandatory-msg-1.alert").css('display','none');
                    $("div#undefined-msg.alert").css('display','none');
                    var edata;
                    var cnDate = $.trim($("#cnDate").val());
                    if(cnDate != '')
                    {
                        var homeLoader = $('body').loadingIndicator({
                            useImage: false
                        }).data("loadingIndicator");
                        edata = "cnDatePrm="+cnDate;
                        $.ajax({
                            type: 'GET',
                            url: '../db/REPORT/CN_CREATE_FILE.php',
                            data: edata,
                            success: function(data){
                                //console.log($.trim(data));
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','none');
                                if($.trim(data) == 'Success')
                                {
                                    $("div#mandatory-msg-1.alert").css('display','none');
                                    $("div#undefined-msg.alert").css('display','none');
                                    $("#transfer-msg").slideDown();
                                    homeLoader.hide();
                                }
                                else if($.trim(data) == 'Mandatory_1')
                                {
                                    $("div#mandatory-msg-1.alert").css('display','block');
                                    $("div#undefined-msg.alert").css('display','none');
                                    $("#btn-cn-report").removeAttr('disabled');
                                    homeLoader.hide();
                                }
                                else
                                {
                                    $("div#mandatory-msg-1.alert").css('display','none');
                                    $("div#undefined-msg.alert").css('display','block');
                                }
                            }
                        });
                    }
                    else if(cnDate == '')
                    {
                        $("div#mandatory-msg-1.alert").css('display','block');
                        $("div#undefined-msg.alert").css('display','none');
                        $("#btn-cn-process").removeAttr('disabled');
                    }
                    else
                    {
                        $("div#mandatory-msg-1.alert").css('display','none');
                        $("div#undefined-msg.alert").css('display','block');
                    }
                },
                "No": function(){
                    $(this).dialog("close");
                }
            }
        });
    });
});

