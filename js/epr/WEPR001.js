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
    
    $("#btn-search").click(function() {
        var prNoVal             = $("#prNo").val();
        var requesterNameVal    = $.trim($("#requesterName").val());
        var approverNameVal     = $.trim($("#approverName").val());
        var prStatusVal         = $.trim($("#prStatus").val());
        
        if(prNoVal != ''||  requesterNameVal != '' || approverNameVal != ''
            || prStatusVal != '' )
        {
            $("#WEPR001Form").attr('action', 'WEPR001.php');
            $("#WEPR001Form").submit();
        }
        else if(prNoVal == ''||  requesterNameVal == '' || approverNameVal == ''
            || prStatusVal == '' )
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
        $("#approverName").val('');
        $("#prStatus").val('');
        $("div#mandatory-msg-1.alert").css('display','none');
        $("div#mandatory-msg-2.alert").css('display','none');
        $("div#undefined-msg.alert").css('display','none');
        $("#WEPR001Form").attr('action', 'WEPR001.php');
        $("#WEPR001Form").submit();
    });
});


