$(document).ready(function() {
    var prItemPrice = 0;
    var supItemPrice = 0;
    var limitPrice = 0;
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
    $("#qty").keypress(function(event) {
        return isNumber(event, this);
    });
    
    $("#price").keyup(function(event) {
        //return isNumberForPrice(event, this);
        // skip for arrow keys
        if(event.which >= 37 && event.which <= 40){
            event.preventDefault();
        }
        
        $(this).val(function(index, value) {
            value = value.replace(/,/g,'');
            return numberWithCommas(value);
        });
        
        if($.trim($("#currencyCd").val()) == "IDR")
        {
            prItemPrice = $.trim($("#prItemPrice").val()).replace(/,/g, '');
            supItemPrice = $.trim($("#price").val()).replace(/,/g, '');
            var addPrice = (20 * prItemPrice) / 100;
            limitPrice = parseInt(prItemPrice) + parseInt(addPrice);
            
            if(supItemPrice > limitPrice)
            {
                $("div#dialog-mandatory-msg-6.alert").css('display','block');
            }
            else
            {
                $("div#dialog-mandatory-msg-6.alert").css('display','none');
            }
        }
        
    });  
    
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
    
    $("#supplierName").change(function(){
        $("#supplierCd option[value='"+$("#supplierName").val()+"']").prop("selected", true);
        var str = "";
        $( "#supplierName option:selected" ).each(function() {
            str += $( this ).text() + " ";
        });
        str = $.trim(str).substr(7,3);
        $("#currencyCd").val(str); 
    });
    
    $("#supplierCd").change(function(){
        $("#supplierName option[value='"+$("#supplierCd").val()+"']").prop("selected", true);
        var str = "";
        $( "#supplierCd option:selected" ).each(function() {
            str += $( this ).text() + " ";
        });
        str = $.trim(str).substr(7,3);
        $("#currencyCd").val(str); 
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
                var edata;
                var supplierCdSet   = $.trim($("#supplierCdHidden").val());
                var itemCd          = $.trim($("#itemCd").val());
                var itemName        = $.trim($("#itemName").val());
                var qty             = $.trim($("#qty").val().replace(/,/g, ''));
                var currencyCd      = $.trim($("#currencyCdHidden").val());
                var unitCd          = $.trim($("#um").val());
                var price           = $.trim($("#price").val().replace(/,/g, ''));
                var amount          = qty * price;
                var supplierCd      = $.trim($("#supplierCd").val());
                var supplierName    = $.trim($("#supplierName option[value="+supplierCd+"]").text());
                var seqItem         = $.trim($("#seqItem").val());
                var refTransferId   = $.trim($("#refTransferIdHidden").val());
                var poNo            = $.trim($("#poNo").val());
                var prItemPrice     = $.trim($("#prItemPrice").val().replace(/,/g, ''));
                
                edata = "supplierCdSetPrm="+supplierCdSet+"&supplierCdPrm="+supplierCd+"&supplierNamePrm="+supplierName
                        +"&itemCdPrm="+itemCd+"&itemNamePrm="+encodeURIComponent(itemName)+"&currencyCdPrm="+currencyCd
                        +"&unitCdPrm="+unitCd+"&qtyPrm="+qty+"&itemPricePrm="+price+"&amountPrm="+amount
                        +"&seqItemPrm="+seqItem+"&refTransferIdPrm="+refTransferId+"&poNoPrm="+poNo
                        +"&prItemPricePrm="+prItemPrice+"&limitPricePrm="+limitPrice;
                   
                var qtyCheck = $("#qty").val().substr(0,1);
                //if(qtyCheck > 0)
                //{
                    $.ajax({
                        type: 'GET',
                        url: '../db/PO/UPDATE_ITEM_SESSION.php?action=EditItemPo',
                        data: edata,
                        success: function(data){
                            //console.log($.trim(data));
                            $("div#dialog-mandatory-msg-1.alert").css('display','none');
                            $("div#dialog-mandatory-msg-2.alert").css('display','none');
                            $("div#dialog-mandatory-msg-3.alert").css('display','none');
                            $("div#dialog-mandatory-msg-4.alert").css('display','none');
                            $("div#dialog-mandatory-msg-5.alert").css('display','none');
                            $("div#dialog-mandatory-msg-6.alert").css('display','none');
                            $("div#dialog-undefined-msg.alert").css('display','none');

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
                                $("div#dialog-mandatory-msg-5.alert").css('display','none');
                                $("div#dialog-mandatory-msg-6.alert").css('display','none');
                                $("div#dialog-undefined-msg.alert").css('display','none');
                            }
                            else if($.trim(data) == 'Mandatory_2')
                            {
                                $("div#dialog-mandatory-msg-1.alert").css('display','none');
                                $("div#dialog-mandatory-msg-2.alert").css('display','block');
                                $("div#dialog-mandatory-msg-3.alert").css('display','none');
                                $("div#dialog-mandatory-msg-4.alert").css('display','none');
                                $("div#dialog-mandatory-msg-5.alert").css('display','none');
                                $("div#dialog-mandatory-msg-6.alert").css('display','none');
                                $("div#dialog-undefined-msg.alert").css('display','none');
                            }
                            else if($.trim(data) == 'Mandatory_3')
                            {
                                $("div#dialog-mandatory-msg-1.alert").css('display','none');
                                $("div#dialog-mandatory-msg-2.alert").css('display','none');
                                $("div#dialog-mandatory-msg-3.alert").css('display','block');
                                $("div#dialog-mandatory-msg-4.alert").css('display','none');
                                $("div#dialog-mandatory-msg-5.alert").css('display','none');
                                $("div#dialog-mandatory-msg-6.alert").css('display','none');
                                $("div#dialog-undefined-msg.alert").css('display','none');
                            }
                            else if($.trim(data) == 'Mandatory_5')
                            {
                                $("div#dialog-mandatory-msg-1.alert").css('display','none');
                                $("div#dialog-mandatory-msg-2.alert").css('display','none');
                                $("div#dialog-mandatory-msg-3.alert").css('display','none');
                                $("div#dialog-mandatory-msg-4.alert").css('display','none');
                                $("div#dialog-mandatory-msg-5.alert").css('display','block');
                                $("div#dialog-mandatory-msg-6.alert").css('display','none');
                                $("div#dialog-undefined-msg.alert").css('display','none');
                            }
                            else if($.trim(data) == 'Mandatory_6')
                            {
                                $("div#dialog-mandatory-msg-1.alert").css('display','none');
                                $("div#dialog-mandatory-msg-2.alert").css('display','none');
                                $("div#dialog-mandatory-msg-3.alert").css('display','none');
                                $("div#dialog-mandatory-msg-4.alert").css('display','none');
                                $("div#dialog-mandatory-msg-5.alert").css('display','none');
                                $("div#dialog-mandatory-msg-6.alert").css('display','block');
                                $("div#dialog-undefined-msg.alert").css('display','none');
                            }
                            else if($.trim(data) == 'SessionExpired')
                            {
                                $("#dialog-confirm-session").dialog('open');
                            }
                            else
                            {
                                $("div#dialog-mandatory-msg-1.alert").css('display','none');
                                $("div#dialog-mandatory-msg-2.alert").css('display','none');
                                $("div#dialog-mandatory-msg-3.alert").css('display','none');
                                $("div#dialog-mandatory-msg-4.alert").css('display','none');
                                $("div#dialog-mandatory-msg-5.alert").css('display','none');
                                $("div#dialog-mandatory-msg-6.alert").css('display','none');
                                $("div#dialog-undefined-msg.alert").css('display','block');
                            }    
                        }
                    });
                //}
                //else
                //{
                //    if($(this).val() === '0')
               //     {
               //         $("div#dialog-mandatory-msg-4.alert").css('display','block');
                //    }
               // }
                
            },
            "Cancel": function(){
                $(this).dialog("close");
                //window.location.reload();
            }
		}
	});
    /**
     * =========================================================================================================
     * BUTTON FUNCTION
     * =========================================================================================================
     **/
	 $("#btn-update-detail").click(function(){
        /** Update confirmation */
        $("#dialog-confirm-update-detail").html("Do you want update detail this PO?");
        $("#dialog-confirm-update-detail").dialog({
            //autoOpen    : false,
            height      : 155,
            width       : 400,
            //position    : { my: "center", at: "top", of: $("body"), within: $("body") },
            modal       : true,
            buttons     : {
                "Yes": function(){
                    $(this).dialog("close");
                    var poNo = $.trim($("#poNo").val());
                    var updateDateDetail = $("#updateDateDetail").val();
                    var remarkCancelPo  = $.trim($("#remarkCancelPo").val());
                    var eDataPo;
		
                    eDataPo = "poNoPrm="+poNo+"&updateDatePrm="+updateDateDetail+"&remarkCancelPoPrm="+remarkCancelPo;
					
                    $.ajax({
                        type: 'GET',
                        url: '../db/PO/UPDATE_PO.php?action=UpdatePoDetail',
                        data: eDataPo,
                        success: function(data){
                            $("div#mandatory-msg-1.alert").css('display','none');
                            $("div#mandatory-msg-2.alert").css('display','none');
                            $("div#mandatory-msg-3.alert").css('display','none');
                            $("div#session-msg.alert").css('display','none');
                            $("div#success-msg.alert.alert-success").css('display','none');
                            $("div#update-msg.alert.alert-success").css('display','none');
                            $("div#update-detail-msg.alert.alert-success").css('display','none');
                            $("div#undefined-msg.alert").css('display','none');
                            
                            if($.trim(data) == 'Success'){
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#mandatory-msg-2.alert").css('display','none');
                                $("div#mandatory-msg-3.alert").css('display','none');
                                $("div#session-msg.alert").css('display','none');
                                $("div#success-msg.alert.alert-success").css('display','none');
                                $("div#update-msg.alert.alert-success").css('display','none');
                                $("div#update-detail-msg.alert.alert-success").css('display','block');
                                $("div#undefined-msg.alert").css('display','none');
                                
                                $("a#window-edit.btn.btn-small.btn-success").css('display', 'none');
                                    
                                $("button#btn-cancel.btn.btn-danger").attr('disabled', true);
                                $("button#btn-update.btn.btn-warning").attr('disabled', true);
                                $("button#btn-update-detail.btn.btn-info").attr('disabled', true);
                                $("#remarkCancelPo").attr('readonly', true);
                                $("#newDeliveryDate").attr('readonly', true);
                            }
                            else if($.trim(data) == 'Mandatory_1')
                            {
                                $("div#mandatory-msg-1.alert").css('display','block');
                                $("div#mandatory-msg-2.alert").css('display','none');
                                $("div#mandatory-msg-3.alert").css('display','none');
                                $("div#session-msg.alert").css('display','none');
                                $("div#success-msg.alert.alert-success").css('display','none');
                                $("div#update-msg.alert.alert-success").css('display','none');
                                $("div#update-detail-msg.alert.alert-success").css('display','none');
                                $("div#undefined-msg.alert").css('display','none');
                            }
                            else if($.trim(data) == 'Mandatory_2')
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#mandatory-msg-2.alert").css('display','block');
                                $("div#mandatory-msg-3.alert").css('display','none');
                                $("div#session-msg.alert").css('display','none');
                                $("div#success-msg.alert.alert-success").css('display','none');
                                $("div#update-msg.alert.alert-success").css('display','none');
                                $("div#update-detail-msg.alert.alert-success").css('display','none');
                                $("div#undefined-msg.alert").css('display','none');
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
                                $("div#success-msg.alert.alert-success").css('display','none');
                                $("div#update-msg.alert.alert-success").css('display','none');
                                $("div#update-detail-msg.alert.alert-success").css('display','none');
                                $("div#undefined-msg.alert").css('display','block');
                            }
			}
                    });
		},
                "No": function(){
                    $(this).dialog("close");
                }
            }
		})
    });
	
	$("#btn-update").click(function(){
        /** Update confirmation */
        $("#dialog-confirm-update").html("Do you want update delivery due date of this PO?");
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
                    var newDeliveryDate = $("#newDeliveryDate").val();
                    
                    eDataPo         = "poNoPrm="+poNo+"&remarkCancelPoPrm="+encodeURIComponent(remarkCancelPo)
                                        +"&newDeliveryDatePrm="+newDeliveryDate+"&updateDatePrm="+updateDate;
                    $.ajax({
                        type: 'GET',
                        url: '../db/PO/UPDATE_PO.php?action=UpdatePoAfterSent',
                        data: eDataPo,
                        success: function(data){
                            
                            $("div#mandatory-msg-1.alert").css('display','none');
                            $("div#mandatory-msg-2.alert").css('display','none');
                            $("div#mandatory-msg-3.alert").css('display','none');
                            $("div#session-msg.alert").css('display','none');
                            $("div#undefined-msg.alert").css('display','none');
                            $("div#update-msg.alert.alert-success").css('display','none');
                            $("div#update-detail-msg.alert.alert-success").css('display','none');

                            if($.trim(data) == 'Success')
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#mandatory-msg-2.alert").css('display','none');
                                $("div#mandatory-msg-3.alert").css('display','none');
                                $("div#session-msg.alert").css('display','none');
                                $("div#success-msg.alert.alert-success").css('display','none');
                                $("div#update-msg.alert.alert-success").css('display','block');
                                $("div#update-detail-msg.alert.alert-success").css('display','none');
                                $("div#undefined-msg.alert").css('display','none');
                                
                                $("a#window-edit.btn.btn-small.btn-success").css('display', 'none');
                                    
                                $("button#btn-cancel.btn.btn-danger").attr('disabled', true);
                                $("button#btn-update.btn.btn-warning").attr('disabled', true);
                                $("button#btn-update-detail.btn.btn-info").attr('disabled', true);
                                $("#remarkCancelPo").attr('readonly', true);
                                $("#newDeliveryDate").attr('readonly', true);
                                
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
                            else if($.trim(data) == 'Mandatory_3')
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#mandatory-msg-2.alert").css('display','none');
                                $("div#mandatory-msg-3.alert").css('display','block');
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
                            //alert($.trim(data));
                            $("div#mandatory-msg-1.alert").css('display','none');
                            $("div#mandatory-msg-2.alert").css('display','none');
                            $("div#session-msg.alert").css('display','none');
                            $("div#undefined-msg.alert").css('display','none');

                            if($.trim(data) == 'Success')
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#mandatory-msg-2.alert").css('display','none');
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
                                $("div#session-msg.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','none');
                                $("div#success-msg.alert.alert-success").css('display','none');
                            }
                            else if($.trim(data) == 'Mandatory_2')
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#mandatory-msg-2.alert").css('display','block');
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
	
    $("table .btn-success").click(function(){
        var supplierCd  = $.trim($("#supplierCdHidden").val());
        var refTransferId= $.trim($(this).closest('tr').find('td:eq(2)').text());
        var itemCd      = $.trim($(this).closest('tr').find('td:eq(4)').text());
        var itemName    = $.trim($(this).closest('tr').find('td:eq(5)').text());
        var qty         = $.trim($(this).closest('tr').find('td:eq(6)').text());
        var um          = $.trim($(this).closest('tr').find('td:eq(7)').text());
        var price       = $.trim($(this).closest('tr').find('td:eq(8)').text());
        var currencyCd  = $.trim($(this).closest('tr').find('td:eq(9)').text());
        var seqItem     = $.trim($(this).closest('tr').find('td:eq(12)').text());
        var initialQty  = $.trim($(this).closest('tr').find('td:eq(14)').text());
        var prItemPrice = $.trim($(this).closest('tr').find('td:eq(16)').text());
		
		$("#refTransferIdHidden").val('');
        $("#currencyCdHidden").val('');
        $("#supplierCd").val('');
        $("#supplierName").val('');
        $("#itemCd").val('');
        $("#itemName").val('');
        $("#qty").val('');
        $("#um").val('');
        $("#price").val('');
        $("#seqItem").val('');
        $("#prItemPrice").val('');
        
        $("#dialog-form").dialog("open");
		
		$("#refTransferIdHidden").val(refTransferId);
        $("#currencyCdHidden").val(currencyCd);
        $("#supplierCd").val(supplierCd);
        $("#supplierName").val(supplierCd);
        $("#itemCd").val(itemCd);
        $("#itemName").val(itemName);
        $("#qty").val(qty);
        $("#um").val(um);
        $("#price").val(price);
        $("#seqItem").val(seqItem);
        $("#prItemPrice").val(prItemPrice);
        
        if($.trim($("#itemCd").val()) != '99'){
            $("#supplierCd").attr('disabled', true);
            $("#supplierName").attr('disabled', true);
            $("#um").attr('disabled', true);
            $("#price").attr('readonly', true);
        }
        else
        {
            $("#um").attr('disabled', false);
            $("#price").attr('readonly', false);
        }
		$("#qty").attr('readonly', true);
        $("#supplierCd").attr('disabled', true);
        $("#supplierName").attr('disabled', true);
        
        $("div#dialog-mandatory-msg-1.alert").css('display','none');
        $("div#dialog-mandatory-msg-2.alert").css('display','none');
        $("div#dialog-mandatory-msg-3.alert").css('display','none');
        $("div#dialog-mandatory-msg-4.alert").css('display','none');
        $("div#dialog-mandatory-msg-5.alert").css('display','none');
        $("div#dialog-undefined-msg.alert").css('display','none');
    });
	
    function isNumber(evt, element) {

        var charCode = (evt.which) ? evt.which : evt.keyCode

        if (
            //(charCode != 45 || $(element).val().indexOf('-') != -1) &&      // ?-? CHECK MINUS, AND ONLY ONE.
            (charCode != 46 || $(element).val().indexOf('.') != -1) &&      // ?.? CHECK DOT, AND ONLY ONE.
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



