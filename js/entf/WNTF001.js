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
    
    
    /**
     * =========================================================================================================
     * LINK FUNCTION
     * =========================================================================================================
     **/
     $("a#link-send-po").click(function(){
         /** Save confirmation */
        $("#dialog-confirm-send-po").html("Do you want send this PO manually?");
        $("#dialog-confirm-send-po").dialog({
            //autoOpen    : false,
            height      : 155,
            width       : 400,
            //position    : { my: "center", at: "top", of: $("body"), within: $("body") },
            modal       : true,
            buttons     : {
                "Yes": function(){
                    $(this).dialog("close");
                    
                    var eDataPo;
                    eDataPo = "action=ManualSendPo";
                    
                    $.ajax({
                        type: 'GET',
                        url: '../db/CREATE_FILE/CREATE_PO_FILE.php',
                        data: eDataPo,
                        cache: false,
                        success: function(data){
                            $("div#success-msg.alert.alert-info").css('display','block');
                        }
                    });
                },
                "No": function(){
                    $(this).dialog("close");
                }
            }
        });
     });
     
     $("a#link-send-outstanding-po").click(function(){
        /** Save confirmation */
        $("#dialog-confirm-send-po").html("Do you want send this outstanding PO manually?");
        $("#dialog-confirm-send-po").dialog({
            //autoOpen    : false,
            height      : 155,
            width       : 400,
            //position    : { my: "center", at: "top", of: $("body"), within: $("body") },
            modal       : true,
            buttons     : {
                "Yes": function(){
                    $(this).dialog("close");
                    var plantCdArray = [0,1,5];
                    var eDataPo;
                    for(var i=0; i < plantCdArray.length; i++)
                    {
                        var plantCd = plantCdArray[i];
                        eDataPo = "plantCdVal="+plantCd;
                        $.ajax({
                            type: 'GET',
                            url: '../db/REPORT/OUTSTANDING_PO.php?action=ManualSentOutstandingPo',
                            data: eDataPo,
                            success: function(data){
                                $("div#success-msg.alert.alert-info").css('display','block');
                            }
                        });
                    }
                },
                "No": function(){
                    $(this).dialog("close");
                }
            }
        });
     });
     
     $("a#link-send-delay-delivery").click(function(){
        /** Save confirmation */
        $("#dialog-confirm-send-po").html("Do you want send this Delay Delivery manually?");
        $("#dialog-confirm-send-po").dialog({
            //autoOpen    : false,
            height      : 155,
            width       : 400,
            //position    : { my: "center", at: "top", of: $("body"), within: $("body") },
            modal       : true,
            buttons     : {
                "Yes": function(){
                    $(this).dialog("close");
                    var plantCdArray = [7];
                    var eDataPo;
                    for(var i=0; i < plantCdArray.length; i++)
                    {
                        var plantCd = plantCdArray[i];
                        eDataPo = "plantCdVal="+plantCd+"&action=ManualSentDelayDelivery";
                        $.ajax({
                            type: 'GET',
                            url: '../db/CREATE_FILE/CREATE_DELAY_DELIVERY_FILE.php',
                            data: eDataPo,
                            success: function(data){
                                $("div#success-msg.alert.alert-info").css('display','block');
                            }
                        });
                    }
                },
                "No": function(){
                    $(this).dialog("close");
                }
            }
        });
     });
});

