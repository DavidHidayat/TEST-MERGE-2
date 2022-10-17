$(document).ready(function() {
    /**
     * =========================================================================================================
     * INITIAL VALUE
     * =========================================================================================================
     **/
    
    /**
     * =========================================================================================================
     * DEFINE DIALOG
     * =========================================================================================================
     **/
    $("#dialog-attach-table").dialog({
        autoOpen        : false,
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
    
    /**
     * =========================================================================================================
     * BUTTON FUNCTION
     * =========================================================================================================
     **/
	 $("#btn-search").click(function(e) {
        var prNoVal = $.trim($("#prNo").val());
        
        if(prNoVal != '')
        {
            $("div#mandatory-msg-1.alert").css('display','none');
            $("div#mandatory-msg-2.alert").css('display','none');
            $("#WEPO004Form").attr('action', 'WEPO004.php');
            $("#WEPO004Form").submit();
        }
        else
        {
            $("div#mandatory-msg-1.alert").css('display','block');
            $("div#mandatory-msg-2.alert").css('display','none');
        }
    });
    $("#btn-reset").click(function(e) {
        $("#prNo").val('');
    });
    $("table .btn-info").click(function() {
        var refItemName = $.trim($(this).closest('tr').find('td:eq(3)').text());
        var prNo        = $.trim($(this).closest('tr').find('td:eq(4)').text());
        var edataAttach;
        edataAttach = "prNoPrm="+prNo+"&refItemNamePrm="+encodeURIComponent(refItemName);
        
        $.ajax({
            type: 'GET',
            url: '../db/GET_TABLE/EPS_T_PR_ATTACHMENT.php?criteria=AttachmentPRItem',
            data: edataAttach,
            success: function(data){
                //alert($.trim(data));
                /** Dialog table */
                $("#dialog-control-group-attach").append($.trim(data));
            }
        });
        $("#dialog-attach-table").dialog("open");
    });
});

