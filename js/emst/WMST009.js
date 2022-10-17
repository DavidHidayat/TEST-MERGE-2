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
    
    $("button#btn-upload.btn.btn-primary").click(function() {
        var uploadFileVal      = $.trim($("#uploadFile").val());
        var mstTypeVal         = $.trim($("#mstType").val());
        var actionTypeVal       = $.trim($("#actionType").val());
        
        if(uploadFileVal != '' || mstTypeVal != '' || actionTypeVal != '')
        {
            $("div#mandatory-msg-1.alert").css('display','none');
            $("div#mandatory-msg-2.alert").css('display','none');
            //$("#WMST009Form").attr('action', 'WMST009.php');
            //$("#WMST009Form").submit();
        }
        else
        {
            $("div#mandatory-msg-1.alert").css('display','block');
            $("div#mandatory-msg-2.alert").css('display','none');
        }
    });
     **/
    $("#btn-reset").click(function() {
        $("#uploadFile").val('');
        $("#mstType").val('');
        $("#actionType").val('');
        $("div#mandatory-msg-1.alert").css('display','none');
        $("div#mandatory-msg-2.alert").css('display','none');
        $("#WMST009Form").attr('action', 'WMST009.php');
        $("#WMST009Form").submit();
    });
});
