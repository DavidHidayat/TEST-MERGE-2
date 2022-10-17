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
    $("#itemName").keydown(function(event){
        $('#itemCd').val('99');
        $('#supplierCd').attr('disabled', false);
        $('#supplierName').attr('disabled', false);
        $("#um").attr('disabled', false);
        $("#price").attr('readonly', false);
    });
    
    $(function(){
        $("#itemName").autocomplete({
            source      : '../db/MASTER/EPS_M_ITEM_PRICE.php?action=searchAutoItemPrice',
            minLength   : 2,//search after two characters
            select      : function (event, ui) {
                $('#itemCd').val(ui.item.itemCd);
                $('#supplierCd').val(ui.item.supplierCd);
                $('#supplierName').val(ui.item.supplierCd);
                $('#um').val(ui.item.unitCd);
                $('#price').val(ui.item.price);
                
                // format number
                $('#price').val(function(index, value) {
                    return value
                        .replace(/\D/g, '')
                        .replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                    ;
                });
        
                $("#itemCd").attr('readonly', true);
                $("#supplierCd").attr('disabled', true);
                $("#supplierName").attr('disabled', true);
                $("#um").attr('disabled', true);
                $("#price").attr('readonly', true);
            }
        });
    });
    /**
     * =========================================================================================================
     * DEFINE DIALOG
     * =========================================================================================================
     **/
    /** Dialog form */
    $("#dialog-form").dialog({
        autoOpen    : false,
        closeOnEscape   : false,
		height      : 450,
		width       : 650,
        position    : {my: "center", at: "top", of: $("body"), within: $("body")},
		modal       : true,
        open        : function() {                         // open event handler
            $(this)                                // the element being dialogged
                .parent()                          // get the dialog widget element
                .find(".ui-dialog-titlebar-close") // find the close button for this dialog
                .hide();                           // hide it
        },
        buttons     : {
            "Save"  : function(){
                
            },
            "Cancel": function(){
                $(this).dialog("close");
                //window.location.reload();
            }
        }
    });
    
    /** Session confirmation */
    $("#dialog-confirm-session").html("<strong>Session expired!</strong> <br> Session timeout or user has not login. Please login again.");
    $("#dialog-confirm-session").dialog({
        autoOpen    : false,
        closeOnEscape   : false,
		height      : 175,
		width       : 400,
        position    : {my: "center", at: "top", of: $("body"), within: $("body")},
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
    /**
     * =========================================================================================================
     * BUTTON FUNCTION
     * =========================================================================================================
     **/
    $("#btn-approve").click(function(){
        /** Approve confirmation */
        $("#dialog-confirm-approve").html("Do you want approve this PR?");
        $("#dialog-confirm-approve").dialog({
            //autoOpen    : false,
            height      : 155,
            width       : 400,
            //position    : { my: "center", at: "top", of: $("body"), within: $("body") },
            modal       : true,
            buttons     : {
                "Yes": function(){
                    $(this).dialog("close");
                    var eDataPr;
                    var prNo        = $.trim($("#prNo").val());
                    var prChargedVal= $("#prCharged").val();
                    var remarkApp   = $.trim($("#remarkApprover").val());
                    eDataPr = "prNoPrm="+prNo+"&prChargedValPrm="+prChargedVal+"&remarkAppPrm="+encodeURIComponent(remarkApp);
                    
                    $.ajax({
                        type: 'GET',
                        url: '../db/PR_/UPDATE_PR.php?action=ApprovePr',
                        data: eDataPr,
                        success: function(data){
                            //alert(data);
                            if($.trim(data) == 'Success'){
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','none');
                                $("div#session-msg.alert").css('display','none');
                                $("div#success-msg.alert.alert-success").css('display','block');
                                $("div#bypass-it-msg.alert.alert-success").css('display','none');
                                $("div#reject-msg.alert.alert-success").css('display','none');

                                $("button#btn-approve.btn.btn-primary").attr('disabled', true);
                                $("button#btn-reject.btn.btn-danger").attr('disabled', true);
                                $("#remarkApprover").attr('readonly', true);

                                window.location = "WEPR013.php";    
                            }
                            else if($.trim(data) == 'Mandatory_1')
                            {
                                $("div#mandatory-msg-1.alert").css('display','block');
                                $("div#undefined-msg.alert").css('display','none');
                                $("div#session-msg.alert").css('display','none');
                                $("#remarkApprover").attr('readonly', false);
                            }
                            else if($.trim(data) == 'SessionExpired')
                            {
                                $("#dialog-confirm-session").dialog('open');
                            }
                            else
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','block');
                                $("div#session-msg.alert").css('display','none');
                            }
                        }
                    });
                },
                "No": function(){
                    $(this).dialog("close");
                }
            }
        });
        //$("#dialog-confirm-approve").dialog("open");
    });
    
    $("#btn-reject").click(function(){
        /** Reject confirmation */
        $("#dialog-confirm-reject").html("Do you want reject this PR?");
        $("#dialog-confirm-reject").dialog({
            //autoOpen    : false,
            height      : 155,
            width       : 400,
            //position    : { my: "center", at: "top", of: $("body"), within: $("body") },
            modal       : true,
            buttons     : {
                "Yes": function(){
                    $(this).dialog("close");
                    
                    var eDataPr;
                    var prNo        = $.trim($("#prNo").val());
                    var remarkApp   = $.trim($("#remarkApprover").val());
                    eDataPr = "prNoPrm="+prNo+"&remarkAppPrm="+encodeURIComponent(remarkApp);

                    $.ajax({
                        type: 'GET',
                        url: '../db/PR_/UPDATE_PR.php?action=RejectPr',
                        data: eDataPr,
                        success: function(data){
                            //alert(data);
                            if($.trim(data) == 'Success'){
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','none');
                                $("div#session-msg.alert").css('display','none');
                                $("div#success-msg.alert.alert-success").css('display','none');
                                $("div#bypass-it-msg.alert.alert-success").css('display','none');
                                $("div#reject-msg.alert.alert-success").css('display','block');

                                $("button#btn-approve.btn.btn-primary").attr('disabled', true);
                                $("button#btn-reject.btn.btn-danger").attr('disabled', true);
                                $("#remarkApprover").attr('readonly', true);

                                window.location = "WEPR013.php";    
                            }
                            else if($.trim(data) == 'Mandatory_1')
                            {
                                $("div#mandatory-msg-1.alert").css('display','block');
                                $("div#undefined-msg.alert").css('display','none');
                                $("div#session-msg.alert").css('display','none');
                                $("#remarkApprover").attr('readonly', false);
                            }
                            else if($.trim(data) == 'SessionExpired')
                            {
                                $("#dialog-confirm-session").dialog('open');
                            }
                            else
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','block');
                                $("div#session-msg.alert").css('display','none');
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
	
	$("#btn-bypass-it").click(function(){
        /** Reject confirmation */
        $("#dialog-confirm-bypass-it").html("Do you want bypass this PR?");
        $("#dialog-confirm-bypass-it").dialog({
            //autoOpen    : false,
            height      : 155,
            width       : 400,
            //position    : { my: "center", at: "top", of: $("body"), within: $("body") },
            modal       : true,
            buttons     : {
                "Yes": function(){
                    $(this).dialog("close");
                    
                    var eDataPr;
                    var prNo        = $.trim($("#prNo").val());
                    eDataPr = "prNoPrm="+prNo;

                    $.ajax({
                        type: 'GET',
                        url: '../db/PR_/UPDATE_PR.php?action=BypassApproveItPr',
                        data: eDataPr,
                        success: function(data){
                            //alert(data);
                            if($.trim(data) == 'Success'){
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','none');
                                $("div#session-msg.alert").css('display','none');
                                $("div#success-msg.alert.alert-success").css('display','none');
                                $("div#bypass-it-msg.alert.alert-success").css('display','block');
                                $("div#reject-msg.alert.alert-success").css('display','none');

                                $("button#btn-approve.btn.btn-primary").attr('disabled', true);
                                $("button#btn-reject.btn.btn-danger").attr('disabled', true);
                                $("#remarkApprover").attr('readonly', true);

                                window.location = "WEPR013.php";      
                            }
                            else if($.trim(data) == 'Mandatory_1')
                            {
                                $("div#mandatory-msg-1.alert").css('display','block');
                                $("div#undefined-msg.alert").css('display','none');
                                $("div#session-msg.alert").css('display','none');
                                $("#remarkApprover").attr('readonly', false);
                            }
                            else if($.trim(data) == 'SessionExpired')
                            {
                                $("#dialog-confirm-session").dialog('open');
                            }
                            else
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','block');
                                $("div#session-msg.alert").css('display','none');
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
        window.location = "WEPR013.php";    
    });
    
    $("table .btn-success").click(function(){
        $("#dialog-form").dialog("open");
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
});


