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
	$("#btn-replicate").click(function(){
        /** Replicate confirmation */
        $("#dialog-confirm-replicate").html("Do you want replicate this PR?");
        $("#dialog-confirm-replicate").dialog({
            //autoOpen    : false,
            height      : 155,
            width       : 400,
            //position    : { my: "center", at: "top", of: $("body"), within: $("body") },
            modal       : true,
            buttons     : {
                "Yes": function(){
                    $(this).dialog("close");
                    
                    var eDataPr;
                    var userIdLoginVal  = $("#userIdLoginHidden").val();
                    var buLoginHiddenVal  = $("#buLoginHidden").val();
                    eDataPr = "userId="+userIdLoginVal+"&buLogin="+buLoginHiddenVal+"&action=getCurrentPrNo";
                    $.ajax({
                        type: 'GET',
                        url: '../db/PR/EPS_T_PR_SEQUENCE.php',
                        data: eDataPr,
                        success: function(data){
                            var newPrNo = $.trim(data);
                            var oldPrNo = $("#prNo").val();
                            window.location='../epr/WEPR011.php?prNo='+newPrNo+'&oldPrNo='+oldPrNo;
                        }
                    });
                }
                ,"No": function(){
                    $(this).dialog("close");
                }
            }
        });
    });
    $("#btn-resend-mail").click(function(){
        /** Replicate confirmation */
        $("#dialog-confirm-resend-mail").html("Do you want re-send notification for this PR?");
        $("#dialog-confirm-resend-mail").dialog({
            //autoOpen    : false,
            height      : 155,
            width       : 400,
            //position    : { my: "center", at: "top", of: $("body"), within: $("body") },
            modal       : true,
            buttons     : {
                "Yes": function(){
                    $(this).dialog("close");
                    var eDataPr;
                    var prNoVal  = $("#prNo").val();
                    eDataPr = "prNoPrm="+prNoVal;
                    $.ajax({
                        type: 'GET',
                        url: '../db/PR_/UPDATE_PR.php?action=ResendMailPr',
                        data: eDataPr,
                        success: function(data){
                            if($.trim(data) == 'Success'){
                                $("div#success-msg.alert.alert-success").css('display','block');
                            }
                        }
                    });
                }
                ,"No": function(){
                    $(this).dialog("close");
                }
            }
        });
    });
    
    $("#btn-takeover-mail").click(function(){
        /** Replicate confirmation */
        $("#dialog-confirm-takeover-mail").html("Do you want send for Takover notification for this PR?");
        $("#dialog-confirm-takeover-mail").dialog({
            //autoOpen    : false,
            height      : 155,
            width       : 400,
            //position    : { my: "center", at: "top", of: $("body"), within: $("body") },
            modal       : true,
            buttons     : {
                "Yes": function(){
                    $(this).dialog("close");
                    var eDataPr;
                    var prNoVal  = $("#prNo").val();
                    var approverTakeoverVal  = $("#approverTakeover").val();
                    eDataPr = "prNoPrm="+prNoVal+"&approverTakoverPrm="+approverTakeoverVal;
                    $.ajax({
                        type: 'GET',
                        url: '../db/PR_/UPDATE_PR.php?action=ResendMailTakover',
                        data: eDataPr,
                        success: function(data){
                            if($.trim(data) == 'Success'){
                                $("div#success-msg.alert.alert-success").css('display','block');
                            }
                        }
                    });
                }
                ,"No": function(){
                    $(this).dialog("close");
                }
            }
        });
    });
	
    $("#btn-back").click(function(){
        window.location = "WEPR001.php";
                
    });
    $("table .btn-info").click(function() {
        var prNo        = $.trim($("#prNo").val());
        var refItemName = $.trim($(this).closest('tr').find('td:eq(4)').text());
        
        var edataAttach;
        edataAttach = "prNoPrm="+prNo+"&refItemNamePrm="+encodeURIComponent(refItemName);
         
        $.ajax({
            type: 'GET',
            url: '../db/GET_TABLE/EPS_T_PR_ATTACHMENT.php?criteria=AttachmentPRItem',
            data: edataAttach,
            success: function(data){
                //alert($.trim(data));
                /** Dialog attachment table */
                $("#dialog-attach-table").dialog({
                    //autoOpen        : false,
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
                //$("#dialog-attach-table").dialog("open");
                $("#dialog-control-group-attach").append($.trim(data));
            }
        });
    });
	$("td a.faq-list.po-no").click(function() {
        var transferId = $.trim($(this).closest('tr').find('td:eq(1)').text());
        var edataPo;
        
        edataPo = "refTransferIdPrm="+transferId;
        $.ajax({
            type: 'GET',
            url: '../db/GET_TABLE/EPS_T_PO.php?criteria=PoDetail',
            data: edataPo,
            success: function(data){
                $("#dialog-control-group-po").append($.trim(data));
            }
        });
        
        $("#dialog-po-table").dialog({
            //autoOpen        : false,
            closeOnEscape   : false,
            height          : 550,
            width           : 970,
            //position        : {my: "center", at: "top", of: $("body"), within: $("body")},
            modal           : true,
            open            : function() {                         // open event handler
                $(this)                                // the element being dialogged
                    .parent()                          // get the dialog widget element
                    .find(".ui-dialog-titlebar-close") // find the close button for this dialog
                    .hide();                           // hide it
            },
            buttons     : {
                "Close"  : function(){
                    $("#table-po").remove();
                    $("#dialog-po-table").dialog("close");
                }
            }
        });
    });
});
