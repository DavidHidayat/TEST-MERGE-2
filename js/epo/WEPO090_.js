$(document).ready(function() {
    /**
     * =========================================================================================================
     * DEFINE DIALOG
     * =========================================================================================================
     **/
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
    $("#btn-search").click(function() {
        var poNoVal         = $.trim($("#poNo").val());
        var poDateVal       = $.trim($("#poDate").val());
        var poDateEndVal    = $.trim($("#poDateEnd").val()); 
        var deliveryDateVal = $.trim($("#deliveryDate").val());
        var prNoVal         = $.trim($("#prNo").val());
        var requesterVal    = $.trim($("#requester").val());
        var supplierCdVal   = $.trim($("#supplierCd").val());
        var supplierNameVal = $.trim($("#supplierName").val());
        var itemNameVal     = $.trim($("#itemName").val());
        var itemTypeVal     = $.trim($("#itemType").val());
        var expNoVal   		= $.trim($("#expNo").val());
        var invNoVal     	= $.trim($("#invNo").val());
        var rfiNoVal     	= $.trim($("#rfiNo").val());
        var itemsStsVal     = $.trim($("#itemStatus").val()); 
        var prChargedVal    = $.trim($("#prCharged").val()); 
        var poStsVal        = $.trim($("#poSts").val()); 
		var roStsVal        = $.trim($("#roSts").val());
        
        if(poNoVal != '' ||  poDateVal != '' || poDateEndVal != '' || deliveryDateVal != ''
            || prNoVal != '' || requesterVal != '' || supplierCdVal != '' || supplierNameVal != ''  || itemNameVal != ''
            || itemTypeVal != '' || expNoVal != '' || invNoVal != '' || rfiNoVal != ''
			|| itemsStsVal != '' || prChargedVal != '' || poStsVal != '' || roStsVal != '')
        {
            $("div#mandatory-msg-1.alert").css('display','none');
            $("div#mandatory-msg-2.alert").css('display','none');
            $("#WEPO090Form").attr('action', 'WEPO090.php');
            $("#WEPO090Form").submit();
        }
        else
        {
            $("div#mandatory-msg-1.alert").css('display','block');
            $("div#mandatory-msg-2.alert").css('display','none');
        }
    });
	$("#btn-reset").click(function() {
		$("#poNo").val('');
		$("#poDate").val('');
		$("#poDateEnd").val('');
		$("#deliveryDate").val('');
		$("#prNo").val('');
		$("#requester").val('');
		$("#supplierCd").val('');
		$("#supplierName").val('');
		$("#itemName").val('');
		$("#itemType").val('');
		$("#expNo").val('');
		$("#invNo").val('');
		$("#rfiNo").val('');
		$("#itemStatus").val('');
		$("#prCharged").val('');
		$("#poSts").val('');
		$("#roSts").val('');
        $("div#mandatory-msg-1.alert").css('display','none');
        $("div#mandatory-msg-2.alert").css('display','none');
	});
	$("table .btn-info").click(function() {
        var refTransferId = $.trim($(this).closest('tr').find('td:eq(1)').text());
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
    
    $("table .btn-inverse").click(function() {
        var refTransferId   = $.trim($(this).closest('tr').find('td:eq(1)').text());
        var poNo            = $.trim($(this).closest('tr').find('td:eq(2)').text());
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
	/**
     * =========================================================================================================
     * LINK FUNCTION
     * =========================================================================================================
     **/
    $("td a.faq-list.pr-no").click(function() {
        var refTransferId = $.trim($(this).closest('tr').find('td:eq(1)').text());
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
});


