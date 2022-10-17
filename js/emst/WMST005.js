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
        var buCdVal         = $.trim($("#buCd").val());
        var buNameVal       = $.trim($("#buName").val());
        var approverNoVal   = $.trim($("#approverNo").val());
        var npkVal          = $.trim($("#npk").val());
        var approverNameVal = $.trim($("#approverName").val());
        var currencyCdVal   = $.trim($("#currencyCd").val());
        
        if(buCdVal != '' || buNameVal != '' || approverNoVal != '' || npkVal != '' || approverNameVal != '' || currencyCdVal != '')
        {
            $("div#mandatory-msg-1.alert").css('display','none');
            $("div#mandatory-msg-2.alert").css('display','none');
            $("#WMST005Form").attr('action', 'WMST005.php');
            $("#WMST005Form").submit();
        }
        else
        {
            $("div#mandatory-msg-1.alert").css('display','block');
            $("div#mandatory-msg-2.alert").css('display','none');
        }
    });
    
    $("#btn-reset").click(function() {
        $("#buCd").val('');
        $("#buName").val('');
        $("#approverNo").val('');
        $("#npk").val('');
        $("#approverName").val('');
        $("#currencyCd").val('');
        $("div#mandatory-msg-1.alert").css('display','none');
        $("div#mandatory-msg-2.alert").css('display','none');
    });
});
