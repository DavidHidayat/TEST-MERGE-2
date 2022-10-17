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
                        edata = "cnDatePrm="+cnDate;
                        
                        $.ajax({
                            type: 'GET',
                            url: '../db/REPORT/CN_DRAFT_CREATE_FILE.php',
                            data: edata,
                            success: function(data){
                                //console.log($.trim(data));
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','none');
                                if($.trim(data) == 'Success')
                                {
                                    $("div#mandatory-msg-1.alert").css('display','none');
                                    $("div#undefined-msg.alert").css('display','none');
                                    $("div#success-msg.alert.alert-success").css('display','block');
                                    $("#btn-cn-report").removeAttr('disabled');
                                }
                                else if($.trim(data) == 'Mandatory_1')
                                {
                                    $("div#mandatory-msg-1.alert").css('display','block');
                                    $("div#undefined-msg.alert").css('display','none');
                                    $("#btn-cn-report").removeAttr('disabled');
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
    
    /**
     * =========================================================================================================
     * LINK FUNCTION
     * =========================================================================================================
     **/
     
});

