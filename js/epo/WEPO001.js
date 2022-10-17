$(document).ready(function() {
    /**
     * =========================================================================================================
     * INITIAL VALUE
     * =========================================================================================================
     **/
    
    /**
     * =========================================================================================================
     * BUTTON FUNCTION
     * =========================================================================================================
     **/
    
    $("#btn-search").click(function() {
        var prNoVal             = $("#prNo").val();
        var requesterNameVal    = $("#requesterName").val(); 
        var procInChargeVal     = $("#procInCharge").val(); 
        var plantCdVal          = $("#plantCd").val(); 
        
        if(prNoVal != '' || requesterNameVal != '' ||procInChargeVal != '' ||plantCdVal != '')
        {
            $("#WEPO001Form").attr('action', 'WEPO001_.php');
            $("#WEPO001Form").submit();
        }
        else if(prNoVal == '' || requesterNameVal == '' ||procInChargeVal == '' ||plantCdVal == '')
        {
            $("div#mandatory-msg-1.alert").css('display','block');
        }
        else
        {
            $("div#undefined-msg.alert").css('display','block');
        }
    });
    
    $("#btn-reset").click(function() {
        $("#WEPO001Form")[0].reset();
    });
});


