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
    $("input:radio[name=radioAction]").click(function(){
        var radioAction = ($('input:radio[name=radioAction]:checked').val());
        if(radioAction == "updateClosedMonth")
        {
            $("#remarkCancelPo").removeAttr('readonly');
            $("input#newClosingMonth.span3").removeAttr('readonly');
            $("select#newPoStatus.span4").attr('disabled', true);
        }
        else if(radioAction == "updatePoStatus")
        {
            $("#remarkCancelPo").removeAttr('readonly');
            $("input#newClosingMonth.span3").attr('readonly', true);
            $("select#newPoStatus.span4").attr('disabled', false);
        }
        else
        {
            $("input#newClosingMonth.span3").attr('readonly', true);
            $("select#newPoStatus.span4").attr('disabled', true);
            
        }
    });
    
    /**
     * =========================================================================================================
     * DEFINE DIALOG
     * =========================================================================================================
     **/
    /** Session confirmation */
    $("#dialog-confirm-session").html("<strong>Session expired!</strong> <br> Session timeout or user has not login. Please login again.");
    $("#dialog-confirm-session").dialog({
        autoOpen    : false,
        closeOnEscape   : false,
	height      : 175,
	width       : 400,
        position    : { my: "center", at: "top", of: $("body"), within: $("body") },
		modal       : true,
        open        : function() {                         // open event handler
            $(this)                                // the element being dialogged
                .parent()                          // get the dialog widget element
                .find(".ui-dialog-titlebar-close") // find the close button for this dialog
                .hide();                           // hide it
        },
        buttons     : {
            "Yes": function(){
                $(this).dialog("close");
                window.location = "../db/Login/Logout.php";
            }
        }
    });
    
	
    /** Dialog reference supplier table */
    $("#dialog-refsupplier-table").dialog({
        autoOpen        : false,
        closeOnEscape   : false,
		height          : 350,
		width           : 630,
        position        : {my: "center", at: "top", of: $("body"), within: $("body")},
		modal           : true,
        open            : function() {                         // open event handler
            $(this)                                // the element being dialogged
                .parent()                          // get the dialog widget element
                .find(".ui-dialog-titlebar-close") // find the close button for this dialog
                .hide();                           // hide it
        },
        buttons     : {
            "Close"  : function(){
                $("#table-refsupplier").remove();
                $("#dialog-refsupplier-table").dialog("close");
            }
        }
    });
	
    /** Dialog reference pr item table */
    $("#dialog-pritem-table").dialog({
        autoOpen        : false,
        closeOnEscape   : false,
	height          : 580,
	width           : 800,
        position        : {my: "center", at: "top", of: $("body"), within: $("body")},
	modal           : true,
        open            : function() {                         // open event handler
            $(this)                                // the element being dialogged
                .parent()                          // get the dialog widget element
                .find(".ui-dialog-titlebar-close") // find the close button for this dialog
                .hide();                           // hide it
        },
        buttons     : {
            "Close"  : function(){
                $("#table-prheader").remove();
                $("#table-pritem").remove();
                $("#table-pr-approver").remove();
                $("#dialog-pritem-table").dialog("close");
            }
        }
    });
    
    /** Dialog receiving table */
    $("#dialog-receiving-table").dialog({
        autoOpen        : false,
        closeOnEscape   : false,
	height          : 550,
	width           : 680,
        position        : { my: "center", at: "top", of: $("body"), within: $("body") },
	modal           : true,
        open            : function() {                         // open event handler
            $(this)                                // the element being dialogged
                .parent()                          // get the dialog widget element
                .find(".ui-dialog-titlebar-close") // find the close button for this dialog
                .hide();                           // hide it
        },
        buttons     : {
            "Close"  : function(){
                $("#table-receiving-item").remove();
                $("#dialog-receiving-table").dialog("close");
            }
        }
    });
    /**
     * =========================================================================================================
     * BUTTON FUNCTION
     * =========================================================================================================
     **/
    $("#btn-update-po-closed").click(function(){
        /** Update confirmation */
        $("#dialog-confirm-update").html("Do you want update this PO?");
        $("#dialog-confirm-update").dialog({
            //autoOpen    : false,
            height      : 155,
            width       : 400,
            //position    : { my: "center", at: "top", of: $("body"), within: $("body") },
            modal       : true,
            buttons     : {
                "Yes": function(){
                    $(this).dialog("close");
                    var eDataPo;
                    var poNo            = $.trim($("#poNo").val());
                    var remarkCancelPo  = $.trim($("#remarkCancelPo").val());
                    var updateDate      = $("#updateDate").val();
                    var newClosingMonth = $("#newClosingMonth").val();
                    var newPoStatus     = $("#newPoStatus").val();
                    var action          = $.trim($('input:radio[name=radioAction]:checked').val());
                    if(action == "undefined")
                    {
                        action = "";
                    }
                    
                    eDataPo         = "poNoPrm="+poNo+"&remarkCancelPoPrm="+encodeURIComponent(remarkCancelPo)
                                        +"&newClosingMonthPrm="+newClosingMonth+"&newPoStatusPrm="+newPoStatus
                                        +"&updateDatePrm="+updateDate+"&actionPrm="+action;
                    $.ajax({
                        type: 'GET',
                        url: '../db/PO/UPDATE_PO.php?action=UpdatePoAfterClosed',
                        data: eDataPo,
                        success: function(data){
                            
                            $("div#mandatory-msg-1.alert").css('display','none');
                            $("div#mandatory-msg-2.alert").css('display','none');
                            $("div#mandatory-msg-3.alert").css('display','none');
                            $("div#mandatory-msg-4.alert").css('display','none');
                            $("div#mandatory-msg-5.alert").css('display','none');
                            $("div#session-msg.alert").css('display','none');
                            $("div#undefined-msg.alert").css('display','none');

                            if($.trim(data) == 'Success')
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#mandatory-msg-2.alert").css('display','none');
                                $("div#mandatory-msg-3.alert").css('display','none');
                                $("div#mandatory-msg-4.alert").css('display','none');
                                $("div#mandatory-msg-5.alert").css('display','none');
                                $("div#session-msg.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','none');
                                $("div#update-msg.alert.alert-success").css('display','block');
                                
                                $("button#btn-update-po-closed.btn.btn-warning").attr('disabled', true);
                                $("input[type = 'radio']").attr('disabled', true);
                                $("#remarkCancelPo").attr('readonly', true);
                                $("#newClosingMonth").attr('readonly', true);
                                $("#newPoStatus").attr('readonly', true);
                                //window.location = "WEPO013.php";
                            }
                            else if($.trim(data) == 'Mandatory_1')
                            {
                                $("div#mandatory-msg-1.alert").css('display','block');
                                $("div#mandatory-msg-2.alert").css('display','none');
                                $("div#mandatory-msg-3.alert").css('display','none');
                                $("div#mandatory-msg-4.alert").css('display','none');
                                $("div#mandatory-msg-5.alert").css('display','none');
                                $("div#session-msg.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','none');
                                $("div#success-msg.alert.alert-success").css('display','none');
                            }
                            else if($.trim(data) == 'Mandatory_2')
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#mandatory-msg-2.alert").css('display','block');
                                $("div#mandatory-msg-3.alert").css('display','none');
                                $("div#mandatory-msg-4.alert").css('display','none');
                                $("div#mandatory-msg-5.alert").css('display','none');
                                $("div#session-msg.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','none');
                                $("div#success-msg.alert.alert-success").css('display','none');
                            }
                            else if($.trim(data) == 'Mandatory_3')
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#mandatory-msg-2.alert").css('display','none');
                                $("div#mandatory-msg-3.alert").css('display','block');
                                $("div#mandatory-msg-4.alert").css('display','none');
                                $("div#mandatory-msg-5.alert").css('display','none');
                                $("div#session-msg.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','none');
                                $("div#success-msg.alert.alert-success").css('display','none');
                            }
                            else if($.trim(data) == 'Mandatory_4')
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#mandatory-msg-2.alert").css('display','none');
                                $("div#mandatory-msg-3.alert").css('display','none');
                                $("div#mandatory-msg-4.alert").css('display','block');
                                $("div#mandatory-msg-5.alert").css('display','none');
                                $("div#session-msg.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','none');
                                $("div#success-msg.alert.alert-success").css('display','none');
                            }
                            else if($.trim(data) == 'Mandatory_5')
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#mandatory-msg-2.alert").css('display','none');
                                $("div#mandatory-msg-3.alert").css('display','none');
                                $("div#mandatory-msg-4.alert").css('display','none');
                                $("div#mandatory-msg-5.alert").css('display','block');
                                $("div#session-msg.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','none');
                                $("div#success-msg.alert.alert-success").css('display','none');
                            }
                            else if($.trim(data) == 'SessionExpired')
                            {
                                $("#dialog-confirm-session").dialog('open');
                            }
                            else
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#mandatory-msg-2.alert").css('display','none');
                                $("div#mandatory-msg-3.alert").css('display','none');
                                $("div#mandatory-msg-4.alert").css('display','none');
                                $("div#mandatory-msg-5.alert").css('display','none');
                                $("div#session-msg.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','block');
                                $("div#success-msg.alert.alert-success").css('display','none');
                            }
                        }
                    });
                },
                "No": function(){
                    $(this).dialog("close");
                }
            }
        });
    });
    $("#btn-cancel").click(function(){
        /** Cancel confirmation */
        $("#dialog-confirm-cancel").html("Do you want cancel this PO?");
        $("#dialog-confirm-cancel").dialog({
            //autoOpen    : false,
            height      : 155,
            width       : 400,
            //position    : { my: "center", at: "top", of: $("body"), within: $("body") },
            modal       : true,
            buttons     : {
                "Yes": function(){
                    $(this).dialog("close");
                    var eDataPo;
                    var poNo            = $.trim($("#poNo").val());
                    var remarkCancelPo  = $.trim($("#remarkCancelPo").val());
                    var updateDate      = $("#updateDate").val();
                    
                    eDataPo         = "poNoPrm="+poNo+"&remarkCancelPoPrm="+encodeURIComponent(remarkCancelPo)+"&updateDatePrm="+updateDate;
                    
                    $.ajax({
                        type: 'GET',
                        url: '../db/PO/UPDATE_PO.php?action=CancelPoAfterSent',
                        data: eDataPo,
                        success: function(data){
                            
                            $("div#mandatory-msg-1.alert").css('display','none');
                            $("div#mandatory-msg-2.alert").css('display','none');
                            $("div#mandatory-msg-3.alert").css('display','none');
                            $("div#session-msg.alert").css('display','none');
                            $("div#undefined-msg.alert").css('display','none');

                            if($.trim(data) == 'Success')
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#mandatory-msg-2.alert").css('display','none');
                                $("div#mandatory-msg-3.alert").css('display','none');
                                $("div#session-msg.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','none');
                                $("div#success-msg.alert.alert-success").css('display','block');
                                
                                $("button#btn-cancel.btn.btn-danger").attr('disabled', true);
                                $("#remarkCancelPo").attr('readonly', true);
                                window.location = "WEPO013.php";
                            }
                            else if($.trim(data) == 'Mandatory_1')
                            {
                                $("div#mandatory-msg-1.alert").css('display','block');
                                $("div#mandatory-msg-2.alert").css('display','none');
                                $("div#mandatory-msg-3.alert").css('display','none');
                                $("div#session-msg.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','none');
                                $("div#success-msg.alert.alert-success").css('display','none');
                            }
                            else if($.trim(data) == 'Mandatory_2')
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#mandatory-msg-2.alert").css('display','block');
                                $("div#mandatory-msg-3.alert").css('display','none');
                                $("div#session-msg.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','none');
                                $("div#success-msg.alert.alert-success").css('display','none');
                            }
                            else if($.trim(data) == 'SessionExpired')
                            {
                                $("#dialog-confirm-session").dialog('open');
                            }
                            else
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#mandatory-msg-2.alert").css('display','none');
                                $("div#mandatory-msg-3.alert").css('display','none');
                                $("div#session-msg.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','block');
                                $("div#success-msg.alert.alert-success").css('display','none');
                            }
                        }
                    });
                },
                "No": function(){
                    $(this).dialog("close");
                }
            }
        });
    });
    $("#btn-back").click(function(){
        /** Back confirmation */
        $("#dialog-confirm-back").html("Do you want back to PO Sent?");
        $("#dialog-confirm-back").dialog({
            //autoOpen    : false,
            height      : 155,
            width       : 400,
            //position    : {my: "center", at: "top", of: $("body"), within: $("body")},
            modal       : true,
            buttons     : {
                "Yes": function(){
                    $(this).dialog("close");
                    window.location = "WEPO013.php";
                },
                "No": function(){
                    $(this).dialog("close");
                }
            }
        });

        //$("#dialog-confirm-back").dialog("open");
    });
    
    $("table .btn-info").click(function() {
        var refTransferId = $.trim($(this).closest('tr').find('td:eq(2)').text());
        var edataRefSupplier;
        edataRefSupplier = "refTransferIdPrm="+refTransferId;
		
        $.ajax({
            type: 'GET',
            url: '../db/GET_TABLE/EPS_T_TRANSFER_SUPPLIER.php',
            data: edataRefSupplier,
            success: function(data){
                //alert($.trim(data));
                $("#dialog-control-group-refsupplier").append($.trim(data));
            }
        });
        $("#dialog-refsupplier-table").dialog("open");
    });
    
    /**
     * =========================================================================================================
     * LINK FUNCTION
     * =========================================================================================================
     **/
    $("td a.faq-list b").click(function() {
        var refTransferId = $.trim($(this).closest('tr').find('td:eq(2)').text());
        var edataRefSupplier;
        
        edataRefSupplier = "refTransferIdPrm="+refTransferId;
        $.ajax({
            type: 'GET',
            url: '../db/GET_TABLE/EPS_T_PR.php?criteria=PrDetail',
            data: edataRefSupplier,
            success: function(data){
                //alert($.trim(data));
                $("#dialog-control-group-pritem").append($.trim(data));
            }
        });
        $("#dialog-pritem-table").dialog("open");
    });
    $("table .btn-inverse").click(function() {
        var refTransferId   = $.trim($(this).closest('tr').find('td:eq(2)').text());
        var poNo            = $.trim($("#poNo").val());
        var edataReceiving;
        edataReceiving = "poNoPrm="+poNo+"&refTransferIdPrm="+refTransferId;
        
        $.ajax({
            type: 'GET',
            url: '../db/GET_TABLE/EPS_T_RO_DETAIL.php?criteria=receivingDetail',
            data: edataReceiving,
            success: function(data){
                //alert($.trim(data));
                $("#dialog-control-group-receiving").append($.trim(data));
            }
        });
        $("#dialog-receiving-table").dialog("open");
    });
});



