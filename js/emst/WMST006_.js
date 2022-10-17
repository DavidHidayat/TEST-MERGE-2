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
        var supplierCdVal  = $.trim($("#supplierCd").val());
        var supplierNameVal= $.trim($("#supplierName").val());
        var currencyCdVal  = $.trim($("#currencyCd").val());
        var vatCdVal       = $.trim($("#vat").val());
        
        if(supplierCdVal != '' || supplierNameVal != '' || currencyCdVal != '' || vatCdVal != '')
        {
            $("div#mandatory-msg-1.alert").css('display','none');
            $("div#mandatory-msg-2.alert").css('display','none');
            $("#WMST006Form").attr('action', 'WMST006.php');
            $("#WMST006Form").submit();
        }
        else
        {
            $("div#mandatory-msg-1.alert").css('display','block');
            $("div#mandatory-msg-2.alert").css('display','none');
        }
    });
    
    $("#btn-reset").click(function() {
        $("#supplierCd").val('');
        $("#supplierName").val('');
        $("#currencyCd").val('');
        $("#vat").val('');
        $("div#mandatory-msg-1.alert").css('display','none');
        $("div#mandatory-msg-2.alert").css('display','none');
    });
});
