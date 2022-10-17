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
        var itemCdVal           = $.trim($("#itemCd").val());
        var itemNameVal         = $.trim($("#itemName").val());
        var itemGroupCdVal      = $.trim($("#itemGroupCd").val());
        var effectiveDateFromVal= $.trim($("#effectiveDateFrom").val());
        var supplierCdVal       = $.trim($("#supplierCd").val());
        
        if(itemCdVal != '' || itemNameVal != '' || itemGroupCdVal != '' || effectiveDateFromVal != '' || supplierCdVal != '')
        {
            $("div#mandatory-msg-1.alert").css('display','none');
            $("div#mandatory-msg-2.alert").css('display','none');
            $("#WMST004Form").attr('action', 'WMST004.php');
            $("#WMST004Form").submit();
        }
        else
        {
            $("div#mandatory-msg-1.alert").css('display','block');
            $("div#mandatory-msg-2.alert").css('display','none');
        }
    });
    
    $("#btn-reset").click(function() {
        $("#itemCd").val('');
        $("#itemName").val('');
        $("#itemGroupCd").val('');
        $("#effectiveDateFrom").val('');
        $("#supplierCd").val('');
        $("div#mandatory-msg-1.alert").css('display','none');
        $("div#mandatory-msg-2.alert").css('display','none');
    });
});
