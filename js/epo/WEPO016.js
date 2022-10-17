$(document).ready(function() {
    /**
     * =========================================================================================================
     * INITIAL VALUE
     * =========================================================================================================
     **/
    $("#newProcInCharged option[value='"+$("#userId").val()+"']").prop("selected", true); 
    
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
    $("#btn-save").click(function(){
        /** Save confirmation */
        $("#dialog-confirm-save").html("Do you want save?");
        $("#dialog-confirm-save").dialog({
            //autoOpen    : false,
            height      : 155,
            width       : 400,
            modal       : true,
            buttons     : {
                "Yes": function(){
                    $(this).dialog("close");
                    var edataPr;
                    var prNoVal             = $("#prNo").val();
                    var newProcInCharge     = $("#newProcInCharged").val();
                    var procPrUpdateDateVal = $("#procPrUpdateDate").val();
                    
                    edataPr = "prNoPrm="+prNoVal+"&newProcInChargePrm="+newProcInCharge
                            +"&procPrUpdateDatePrm="+procPrUpdateDateVal;

                    $.ajax({
                        type: 'GET',
                        url: '../db/PR_to_PO/EPS_T_TRANSFER.php?action=EditPrWaiting',
                        data: edataPr,
                        success: function(data){
                            //alert(data);
                            $("div#mandatory-msg-1.alert").css('display','none');
                            $("div#dialog-undefined-msg.alert").css('display','none');
                            
                            if($.trim(data) == 'Success')
                            {
                                $("div#success-msg.alert.alert-success").css('display','block');
                                
                                $("button#btn-save.btn.btn-primary").attr('disabled', true);
                                $("select#newProcInCharged").attr('disabled', true);
                                window.location = "WEPO001_.php";
                            }
                            else if($.trim(data) == 'Mandatory_1')
                            {
                                $("div#mandatory-msg-1.alert").css('display','block');
                                $("div#undefined-msg.alert").css('display','none');
                            }
                            else if($.trim(data) == 'SessionExpired')
                            {
                                $("#dialog-confirm-session").dialog('open');
                            }
                            else
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','block');
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
    
    $("#btn-back").click(function(){
        /** Back confirmation */
        $("#dialog-confirm-back").html("Do you want back to menu PR Waiting?");
        $("#dialog-confirm-back").dialog({
            //autoOpen    : false,
            height      : 155,
            width       : 400,
            modal       : true,
            buttons     : {
                "Yes": function(){
                    $(this).dialog("close");
                    window.location = "WEPO001_.php";
                },
                "No": function(){
                    $(this).dialog("close");
                }
            }
        });
        //$("#dialog-confirm-back").dialog("open");
    });
    
    $("table .btn-info").click(function() {
        var prNo        = $.trim($(this).closest('tr').find('td:eq(1)').text());
        var refItemName = $.trim($(this).closest('tr').find('td:eq(5)').text());
        var edataAttach;
        edataAttach = "prNoPrm="+prNo+"&refItemNamePrm="+encodeURIComponent(refItemName);
         
        $.ajax({
            type: 'GET',
            url: '../db/GET_TABLE/EPS_T_PR_ATTACHMENT.php?criteria=AttachmentPRItem',
            data: edataAttach,
            success: function(data){
                //alert($.trim(data));
                /** Dialog attachment table */
                $("#dialog-attach-table").dialog({
                    //autoOpen        : false,
                    closeOnEscape   : false,
                    height          : 350,
                    width           : 630,
                    position        : { my: "center", at: "center", of: $("body"), within: $("body") },
                    modal           : true,
                    open            : function() {                         // open event handler
                        $(this)                                // the element being dialogged
                            .parent()                          // get the dialog widget element
                            .find(".ui-dialog-titlebar-close") // find the close button for this dialog
                            .hide();                           // hide it
                    },
                    buttons     : {
                        "Close"  : function(){
                            $("#table-attach-item").remove();
                            $("#dialog-attach-table").dialog("close");
                        }
                    }
                });
                //$("#dialog-attach-table").dialog("open");
                $("#dialog-control-group-attach").append($.trim(data));
            }
        });
    });
});


