$(document).ready(function() {
    /**
     * =========================================================================================================
     * INITIAL VALUE
     * =========================================================================================================
     **/
    $("#um option[value='"+$.trim($("#umHidden").val())+"']").prop("selected", true); 
    
    /**
     * =========================================================================================================
     * INPUT FUNCTION
     * =========================================================================================================
     **/
    /*$("#receivedQty").keypress(function(e) {
        //if the letter is not digit then display error and don't type anything
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            //display error message
            //$("#errmsg").html("Digits Only").show().fadeOut("slow");
            return false;
        }
    });*/
    
    $("#receivedQty").keypress(function(event) {
        return isNumber(event, this);
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
		height      : 330,
		width       : 500,
        position    : {my: "center", at: "top", of: $("body"), within: $("body")},
		modal       : true,
        open            : function() {                         // open event handler
            $(this)                                // the element being dialogged
                .parent()                          // get the dialog widget element
                .find(".ui-dialog-titlebar-close") // find the close button for this dialog
                .hide();                           // hide it
        },
        buttons     : {
            "Save"  : function(){
                var edata;
                var poNo            = $.trim($("#poNo").val());
                var refTransferId   = $.trim($("#refTransferIdHidden").val());
                var roNo            = $.trim($("#roNo").val());
                var qty             = $.trim($("#qty").val().replace(/,/g, ''));
                var roSeq           = $.trim($("#roSeq").val());
                var receivedQty     = $.trim($("#receivedQty").val());
                var receivedDate    = $.trim($("#receivedDate").val());
                var initialReceivedQty= $.trim($("#initialReceivedQty").val());
                var action          = $.trim($("#dialog-form").dialog("option","title")).substr(0,4);
                var totalReceivedQty= $.trim($("#tempTotalReceivedQty").val());
                
                edata = "poNoPrm="+poNo+"&refTransferIdPrm="+refTransferId
                        +"&roNoPrm="+roNo+"&qtyPrm="+qty+"&totalReceivedQtyPrm="+totalReceivedQty
                        +"&receivedQtyPrm="+receivedQty+"&receivedDatePrm="+receivedDate
                        +"&initialReceivedQtyPrm="+initialReceivedQty
                        +"&roSeqPrm="+roSeq+"&actionPrm="+action;
                    
                $.ajax({
                    type: 'GET',
                    url: '../db/RO/UPDATE_RECEIVED_SESSION.php?action=UpdateReceiving',
                    data: edata,
                    success: function(data){
                        //alert(data);
                        if($.trim(data) == 'Success')
                        {
                            $("#dialog-form").dialog("close");
                            window.location.reload();
                        }
                        else if($.trim(data) == 'Mandatory_1')
                        {
                            $("div#dialog-mandatory-msg-1.alert").css('display','block');
                            $("div#dialog-mandatory-msg-2.alert").css('display','none');
                            $("div#dialog-mandatory-msg-3.alert").css('display','none');
                            $("div#dialog-mandatory-msg-4.alert").css('display','none');
                            $("div#dialog-undefined-msg.alert").css('display','none');
                        }
                        else if($.trim(data) == 'Mandatory_2')
                        {
                            $("div#dialog-mandatory-msg-1.alert").css('display','none');
                            $("div#dialog-mandatory-msg-2.alert").css('display','block');
                            $("div#dialog-mandatory-msg-3.alert").css('display','none');
                            $("div#dialog-mandatory-msg-4.alert").css('display','none');
                            $("div#dialog-undefined-msg.alert").css('display','none');
                        }
                        else if($.trim(data) == 'Mandatory_3')
                        {
                            $("div#dialog-mandatory-msg-1.alert").css('display','none');
                            $("div#dialog-mandatory-msg-2.alert").css('display','none');
                            $("div#dialog-mandatory-msg-3.alert").css('display','block');
                            $("div#dialog-mandatory-msg-4.alert").css('display','none');
                            $("div#dialog-undefined-msg.alert").css('display','none');
                        }
                        else if($.trim(data) == 'Mandatory_4')
                        {
                            $("div#dialog-mandatory-msg-1.alert").css('display','none');
                            $("div#dialog-mandatory-msg-2.alert").css('display','none');
                            $("div#dialog-mandatory-msg-3.alert").css('display','none');
                            $("div#dialog-mandatory-msg-4.alert").css('display','block');
                            $("div#dialog-undefined-msg.alert").css('display','none');
                        }
                        else{
                            $("div#dialog-mandatory-msg-1.alert").css('display','none');
                            $("div#dialog-mandatory-msg-2.alert").css('display','none');
                            $("div#dialog-mandatory-msg-3.alert").css('display','none');
                            $("div#dialog-mandatory-msg-4.alert").css('display','none');
                            $("div#dialog-undefined-msg.alert").css('display','block');
                        }
                    }
                });
                
                /*var edata;
                var poNo            = $.trim($("#poNo").val());
                var refTransferId   = $.trim($("#refTransferIdHidden").val());
                var roNo            = $.trim($("#roNo").val());
                var qty             = $.trim($("#qty").val());
                var receivedSeq     = $.trim($("#receivedSeq").val());
                var receivedQty     = $.trim($("#receivedQty").val());
                var receivedDate    = $.trim($("#receivedDate").val());
                var initialReceivedQty= $.trim($("#initialReceivedQty").val());
                var action          = $.trim($("#dialog-form").dialog("option","title")).substr(0,4);
                var totalReceivedQty= $.trim($("#tempTotalReceivedQty").val());
                
                edata = "poNoPrm="+poNo+"&refTransferIdPrm="+refTransferId
                        +"&roNoPrm="+roNo+"&qtyPrm="+qty+"&totalReceivedQtyPrm="+totalReceivedQty
                        +"&receivedQtyPrm="+receivedQty+"&receivedDatePrm="+receivedDate
                        +"&initialReceivedQtyPrm="+initialReceivedQty
                        +"&receivedSeqPrm="+receivedSeq+"&actionPrm="+action;
                
                $.ajax({
                    type: 'GET',
                    url: '../db/RO/UPDATE_RECEIVED_SESSION.php?action=UpdateReceiving',
                    data: edata,
                    success: function(data){
                        //alert(data);
                        if($.trim(data) == 'Success')
                        {
                            $("#dialog-form").dialog("close");
                            window.location.reload();
                        }
                        else if($.trim(data) == 'Mandatory_1')
                        {
                            $("div#dialog-mandatory-msg-1.alert").css('display','block');
                            $("div#dialog-mandatory-msg-2.alert").css('display','none');
                            $("div#dialog-mandatory-msg-3.alert").css('display','none');
                            $("div#dialog-mandatory-msg-4.alert").css('display','none');
                            $("div#dialog-undefined-msg.alert").css('display','none');
                        }
                        else if($.trim(data) == 'Mandatory_2')
                        {
                            $("div#dialog-mandatory-msg-1.alert").css('display','none');
                            $("div#dialog-mandatory-msg-2.alert").css('display','block');
                            $("div#dialog-mandatory-msg-3.alert").css('display','none');
                            $("div#dialog-mandatory-msg-4.alert").css('display','none');
                            $("div#dialog-undefined-msg.alert").css('display','none');
                        }
                        else if($.trim(data) == 'Mandatory_3')
                        {
                            $("div#dialog-mandatory-msg-1.alert").css('display','none');
                            $("div#dialog-mandatory-msg-2.alert").css('display','none');
                            $("div#dialog-mandatory-msg-3.alert").css('display','block');
                            $("div#dialog-mandatory-msg-4.alert").css('display','none');
                            $("div#dialog-undefined-msg.alert").css('display','none');
                        }
                        else if($.trim(data) == 'Mandatory_4')
                        {
                            $("div#dialog-mandatory-msg-1.alert").css('display','none');
                            $("div#dialog-mandatory-msg-2.alert").css('display','none');
                            $("div#dialog-mandatory-msg-3.alert").css('display','none');
                            $("div#dialog-mandatory-msg-4.alert").css('display','block');
                            $("div#dialog-undefined-msg.alert").css('display','none');
                        }
                        else{
                            $("div#dialog-mandatory-msg-1.alert").css('display','none');
                            $("div#dialog-mandatory-msg-2.alert").css('display','none');
                            $("div#dialog-mandatory-msg-3.alert").css('display','none');
                            $("div#dialog-mandatory-msg-4.alert").css('display','none');
                            $("div#dialog-undefined-msg.alert").css('display','block');
                        }
                    }
                });*/
            },
            "Cancel": function(){
                $(this).dialog("close");
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
    
    /**
     * =========================================================================================================
     * BUTTON FUNCTION
     * =========================================================================================================
     **/
    $("#btn-save-item").click(function(){
        /** Save confirmation */
        $("#dialog-confirm-save").html("Do you want save?");
        $("#dialog-confirm-save").dialog({
            //autoOpen    : false,
            height      : 155,
            width       : 400,
            //position    : {my: "center", at: "top", of: $("body"), within: $("body")},
            modal       : true,
            buttons     : {
                "Yes": function(){
                    $(this).dialog("close");
                    
                    var edataItem;
                    var poNo        = $("#poNo").val();
                    var transferId  = $("#refTransferIdHidden").val();
                    var updateDate  = $("#updateDate").val();
                    var totalReceivedQty= $.trim($("#tempTotalReceivedQty").val());
                    var poQty       = $.trim($("#qty").val().replace(/,/g, ''));
					
                    edataItem = "poNoPrm="+poNo+"&transferIdValPrm="+transferId+"&updateDatePrm="+updateDate
                                +"&totalReceivedQtyPrm="+totalReceivedQty+"&poQtyPrm="+poQty;
                    
                    $.ajax({
                        type: 'GET',
                        url: '../db/RO/CREATE_RO.php?action=SaveReceiving',
                        data: edataItem,
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
                                $("div#success-msg-close-item.alert.alert-success").css('display','none');
                                $("div#success-msg-close-po.alert.alert-success").css('display','none');

                                $("button#btn-save-item.btn.btn-primary").attr('disabled', true);

                                $("a#window-add.btn.btn-small.btn-warning").css('display', 'none');
                                $("a#window-edit.btn.btn-small.btn-success").css('display', 'none');
                                $("a#window-delete.btn.btn-small.btn-danger").css('display', 'none');
								
								window.location = "WERO001.php?poNo="+poNo;
                            }
                            else if($.trim(data) == 'Success_Closed_Item')
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');   
                                $("div#mandatory-msg-2.alert").css('display','none');
                                $("div#mandatory-msg-3.alert").css('display','none');
                                $("div#session-msg.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','none');
                                $("div#success-msg.alert.alert-success").css('display','none');
                                $("div#success-msg-close-item.alert.alert-success").css('display','block');
                                $("div#success-msg-close-po.alert.alert-success").css('display','none');

                                $("button#btn-save-item.btn.btn-primary").attr('disabled', true);

                                $("a#window-add.btn.btn-small.btn-warning").css('display', 'none');
                                $("a#window-edit.btn.btn-small.btn-success").css('display', 'none');
                                $("a#window-delete.btn.btn-small.btn-danger").css('display', 'none');
								
								window.location = "WERO001.php?poNo="+poNo;
                            }
                            else if($.trim(data) == 'Success_Closed_Po')
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#mandatory-msg-2.alert").css('display','none');
                                $("div#mandatory-msg-3.alert").css('display','none');
                                $("div#session-msg.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','none');
                                $("div#success-msg.alert.alert-success").css('display','none');
                                $("div#success-msg-close-item.alert.alert-success").css('display','none');
                                $("div#success-msg-close-po.alert.alert-success").css('display','block');

                                $("button#btn-save-item.btn.btn-primary").attr('disabled', true);

                                $("a#window-add.btn.btn-small.btn-warning").css('display', 'none');
                                $("a#window-edit.btn.btn-small.btn-success").css('display', 'none');
                                $("a#window-delete.btn.btn-small.btn-danger").css('display', 'none');
								
								window.location = "WERO001.php?poNo="+poNo;
                            }
                            else if($.trim(data) == 'Mandatory_1')
                            {
                                $("div#mandatory-msg-1.alert").css('display','block');
                                $("div#mandatory-msg-2.alert").css('display','none');
                                $("div#mandatory-msg-3.alert").css('display','none');
                                $("div#session-msg.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','none');
                                $("div#success-msg.alert.alert-success").css('display','none');
                                $("div#success-msg-close-item.alert.alert-success").css('display','none');
                                $("div#success-msg-close-po.alert.alert-success").css('display','none');
                            }
                            else if($.trim(data) == 'Mandatory_2')
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#mandatory-msg-2.alert").css('display','block');
                                $("div#mandatory-msg-3.alert").css('display','none');
                                $("div#session-msg.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','none');
                                $("div#success-msg.alert.alert-success").css('display','none');
                                $("div#success-msg-close-item.alert.alert-success").css('display','none');
                                $("div#success-msg-close-po.alert.alert-success").css('display','none');
                            }
                            else if($.trim(data) == 'Mandatory_3')
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#mandatory-msg-2.alert").css('display','none');
                                $("div#mandatory-msg-3.alert").css('display','block');
                                $("div#session-msg.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','none');
                                $("div#success-msg.alert.alert-success").css('display','none');
                                $("div#success-msg-close-item.alert.alert-success").css('display','none');
                                $("div#success-msg-close-po.alert.alert-success").css('display','none');
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
                                $("div#success-msg-close-item.alert.alert-success").css('display','none');
                                $("div#success-msg-close-po.alert.alert-success").css('display','none');
                            }
                        }
                    });
                    
                    /*var edataItem;
                    var poNo        = $("#poNo").val();
                    var transferId  = $("#refTransferIdHidden").val();
                    var qty         = $("#qty").val();
                    edataItem = "poNoPrm="+poNo+"&transferIdValPrm="+transferId+"&qtyPrm="+qty;

                    $.ajax({
                        type: 'GET',
                        url: '../db/RO/CREATE_RO.php?action=SaveReceiving',
                        data: edataItem,
                        success: function(data){
                            //alert($.trim(data));
                            $("div#mandatory-msg-1.alert").css('display','none');
                            $("div#session-msg.alert").css('display','none');
                            $("div#undefined-msg.alert").css('display','none');

                            if($.trim(data) == 'Success')
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#session-msg.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','none');
                                $("div#success-msg.alert.alert-success").css('display','block');
                                $("div#success-msg-close-item.alert.alert-success").css('display','none');
                                $("div#success-msg-close-po.alert.alert-success").css('display','none');

                                $("button#btn-save-item.btn.btn-primary").attr('disabled', true);

                                $("a#window-add.btn.btn-small.btn-warning").css('display', 'none');
                                $("a#window-edit.btn.btn-small.btn-success").css('display', 'none');
                                $("a#window-delete.btn.btn-small.btn-danger").css('display', 'none');
                            }
                            else if($.trim(data) == 'Success_Closed_Item')
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#session-msg.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','none');
                                $("div#success-msg.alert.alert-success").css('display','none');
                                $("div#success-msg-close-item.alert.alert-success").css('display','block');
                                $("div#success-msg-close-po.alert.alert-success").css('display','none');

                                $("button#btn-save-item.btn.btn-primary").attr('disabled', true);

                                $("a#window-add.btn.btn-small.btn-warning").css('display', 'none');
                                $("a#window-edit.btn.btn-small.btn-success").css('display', 'none');
                                $("a#window-delete.btn.btn-small.btn-danger").css('display', 'none');
                            }
                            else if($.trim(data) == 'Success_Closed_Po')
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#session-msg.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','none');
                                $("div#success-msg.alert.alert-success").css('display','none');
                                $("div#success-msg-close-item.alert.alert-success").css('display','none');
                                $("div#success-msg-close-po.alert.alert-success").css('display','block');

                                $("button#btn-save-item.btn.btn-primary").attr('disabled', true);

                                $("a#window-add.btn.btn-small.btn-warning").css('display', 'none');
                                $("a#window-edit.btn.btn-small.btn-success").css('display', 'none');
                                $("a#window-delete.btn.btn-small.btn-danger").css('display', 'none');
                            }
                            else if($.trim(data) == 'Mandatory_1')
                            {
                                $("div#mandatory-msg-1.alert").css('display','block');
                                $("div#session-msg.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','none');
                                $("div#success-msg.alert.alert-success").css('display','none');
                                $("div#success-msg-close-item.alert.alert-success").css('display','none');
                                $("div#success-msg-close-po.alert.alert-success").css('display','none');
                            }
                            else if($.trim(data) == 'SessionExpired')
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#session-msg.alert").css('display','block');
                                $("div#undefined-msg.alert").css('display','none');
                                $("div#success-msg.alert.alert-success").css('display','none');
                                $("div#success-msg-close-item.alert.alert-success").css('display','none');
                                $("div#success-msg-close-po.alert.alert-success").css('display','none');
                            }
                            else
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#session-msg.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','block');
                                $("div#success-msg.alert.alert-success").css('display','none');
                                $("div#success-msg-close-item.alert.alert-success").css('display','none');
                                $("div#success-msg-close-po.alert.alert-success").css('display','none');
                            }
                        }
                    });*/
                },
                "No": function(){
                    $(this).dialog("close");
                }
            }
        });
        //$("#dialog-confirm-save").dialog("open");
    });
    
    $("#btn-back").click(function(){
        /** Back confirmation */
        $("#dialog-confirm-back").html("Do you want to Open Delivery list?");
        $("#dialog-confirm-back").dialog({
            //autoOpen    : false,
            height      : 155,
            width       : 400,
            //position    : {my: "center", at: "top", of: $("body"), within: $("body")},
            modal       : true,
            buttons     : {
                "Yes": function(){
                    var poNo        = $("#poNo").val();
                    $(this).dialog("close");
                    window.location = "WERO001.php?poNo="+poNo;
                },
                "No": function(){
                    $(this).dialog("close");
                }
            }
        });
        //$("#dialog-confirm-back").dialog("open");
    });
    
    $("table .btn-warning").click(function() {
        var rowCount = $("#receivingListTable > tbody >tr").length;
        var fullDate = new Date();
        //Thu May 19 2011 17:25:38 GMT+1000 {}

        //convert month to 2 digits
        var twoDigitMonth = ((fullDate.getMonth().length+1) === 1)? (fullDate.getMonth()+1) : '0' + (fullDate.getMonth()+1);
        //var twoDigitDate = ((String(fullDate.getDate()).length) === 1)? (fullDate.getDate()) : '0' + (fullDate.getDate());
        var twoDigitDate;
        var twoDigitDateLength = String(fullDate.getDate()).length;
        var twoDigitMonthLength = String(fullDate.getMonth()+1).length;
        
        if(twoDigitDateLength == 1){
            twoDigitDate = '0' + (fullDate.getDate());
        }
        else
        {
            twoDigitDate = (fullDate.getDate());
        }
        if(twoDigitMonthLength == 1){
            twoDigitMonth = '0' + (fullDate.getMonth()+1);
        }
        else
        {
            twoDigitMonth = (fullDate.getMonth()+1);
        }
        var currentDate = twoDigitDate + "/" + twoDigitMonth + "/" + fullDate.getFullYear();
        
        $("div#dialog-mandatory-msg-1.alert").css('display','none');
        $("div#dialog-mandatory-msg-2.alert").css('display','none');
        $("div#dialog-mandatory-msg-3.alert").css('display','none');
        $("div#dialog-mandatory-msg-4.alert").css('display','none');
        $("div#dialog-undefined-msg.alert").css('display','none');
                            
        $("#receivedQty").val('');
        $("#receivedDate").val(currentDate);
        $("#roSeq").val(rowCount);
        $("#dialog-form").dialog('option', 'title', 'Add Received Qty');
        $("#dialog-form").dialog("open");
    });
    
    /*$("table .btn-success").click(function() {
        var roNo            = $.trim($(this).closest('tr').find('td:eq(2)').text());
        var poNo            = $.trim($(this).closest('tr').find('td:eq(3)').text());
        var refTransferId   = $.trim($(this).closest('tr').find('td:eq(4)').text());
        var receivedDate    = $.trim($(this).closest('tr').find('td:eq(5)').text());
        var receivedQty     = $.trim($(this).closest('tr').find('td:eq(6)').text());
        var receivedSeq     = $.trim($(this).closest('tr').find('td:eq(7)').text());
        var itemNo          = $.trim($(this).closest('tr').find('td:eq(0)').text()).substr(0,1);
        
        $("div#dialog-mandatory-msg-1.alert").css('display','none');
        $("div#dialog-mandatory-msg-2.alert").css('display','none');
        $("div#dialog-mandatory-msg-3.alert").css('display','none');
        $("div#dialog-mandatory-msg-4.alert").css('display','none');
        $("div#dialog-undefined-msg.alert").css('display','none');
        
        $("#roNo").val('');
        $("#receivedQty").val('');
        $("#receivedDate").val('');
        $("#receivedSeq").val('');
        $("#initialReceivedQty").val('');
        $("#dialog-form").dialog('option', 'title', 'Edit Received Qty');
        $("#dialog-form").dialog("open");
        
        $("#roNo").val(roNo);
        $("#receivedQty").val(receivedQty);
        $("#receivedDate").val(receivedDate);
        $("#receivedSeq").val(itemNo);
        $("#initialReceivedQty").val(receivedQty);
    });*/
    
    /*$("table .btn-danger").click(function() {
        var eDeleteData;
        var index       = $(this).closest('tr').index();
        var receivedSeq = $.trim(index);
        
        eDeleteData = "receivedSeqPrm="+receivedSeq;
                    
        $.ajax({
            type: 'GET',
            url: '../db/RO/UPDATE_RECEIVED_SESSION.php?action=DeleteReceiving',
            data: eDeleteData,
            success: function(data){
                //alert(data);
                window.location.reload();
            }
        });
    });*/
	function isNumber(evt, element) {

        var charCode = (evt.which) ? evt.which : evt.keyCode

        if (
            //(charCode != 45 || $(element).val().indexOf('-') != -1) &&      // “-” CHECK MINUS, AND ONLY ONE.
            (charCode != 46 || $(element).val().indexOf('.') != -1) &&      // “.” CHECK DOT, AND ONLY ONE.
            (charCode < 48 || charCode > 57))
            return false;
        
        if($(element).val().indexOf('.') != -1)
            {
                if($(element).val().split(".")[1].length > 0){
                    return false;
                }
            }

        return true;
    }
});


