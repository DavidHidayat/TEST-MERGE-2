$(document).ready(function() {
    /**
     * =========================================================================================================
     * DEFINE DIALOG
     * =========================================================================================================
     **/
    /** Dialog table */
    $("#dialog-attach-table").dialog({
        autoOpen        : false,
        closeOnEscape   : false,
	height          : 350,
	width           : 630,
        position        : { my: "center", at: "top", of: $("body"), within: $("body") },
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
    
    /**
     * =========================================================================================================
     * BUTTON FUNCTION
     * =========================================================================================================
     **/
    $("#btn-search").click(function() {
        var prNoVal             = $("#prNo").val();
        var requesterNameVal    = $("#requesterName").val();
        var prChargedBuVal      = $("#prChargedBu").val();
        var acceptedDateVal     = $("#acceptedDate").val();
        
        if(prNoVal != '' || requesterNameVal != '' || prChargedBuVal != '' || acceptedDateVal != '')
        {
            $("#WEPO012Form").attr('action', 'WEPO012.php');
            $("#WEPO012Form").submit();
        }
        else if(prNoVal == '' || requesterNameVal == '' || prChargedBuVal == '' || acceptedDateVal == '')
        {
            $("div#mandatory-msg-1.alert").css('display','block');
        }
        else
        {
            $("div#undefined-msg.alert").css('display','block');
        }
    });
    
    $("#btn-reset").click(function() {
        $("#prNo").val('');
        $("#requesterName").val('');
        $("#prChargedBu").val('');
        $("#acceptedDate").val('');
    });
    
    $("table .btn-info").click(function() {
        var prNo        = $.trim($(this).closest('tr').find('td:eq(2)').text());
        var edataAttach;
        edataAttach = "prNoPrm="+prNo;
        
        $.ajax({
            type: 'GET',
            url: '../db/GET_TABLE/EPS_T_PR_ATTACHMENT.php?criteria=AttachmentPRHeader',
            data: edataAttach,
            success: function(data){
                //alert($.trim(data));
                $("#dialog-control-group-attach").append($.trim(data));
            }
        });
        $("#dialog-attach-table").dialog("open");
    });
});


