$(document).ready(function() {
    /**
     * =========================================================================================================
     * INPUT FUNCTION
     * =========================================================================================================
     **/
    $(function(){
        $("#supplierName").autocomplete({
            source      : '../db/MASTER/EPS_M_SUPPLIER.php?action=searchAutoSupplier',
            minLength   : 2,//search after two characters
            select      : function (event, ui) {
                
            }
        });
    });
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
    /**
     * =========================================================================================================
     * BUTTON FUNCTION
     * =========================================================================================================
     **/
    $("#btn-search").click(function() {
        var supplierCdVal       = $.trim($("#supplierCd").val());
        var supplierNameVal     = $.trim($("#supplierName").val());
        var poNoVal             = $.trim($("#poNo").val());
        var requesterVal        = $.trim($("#requester").val());
        var prNoVal             = $.trim($("#prNo").val());
        
        if(poNoVal != '' || requesterVal != '' || prNoVal != ''
            || supplierCdVal != '' || supplierNameVal != '')
        {
            $("div#mandatory-msg-1.alert").css('display','none');
            $("div#mandatory-msg-2.alert").css('display','none');
            $("#WERO004Form").attr('action', 'WERO004.php');
            $("#WERO004Form").submit();
        }
        else
        {
            $("div#mandatory-msg-1.alert").css('display','block');
            $("div#mandatory-msg-2.alert").css('display','none');
        }
    });
    
    $("#btn-reset").click(function() { 
	$("#supplierCd").val('');
	$("#supplierName").val(''); 
        $("#poNo").val('');
	$("#requester").val('');
	$("#prNo").val('');
        $("div#mandatory-msg-1.alert").css('display','none');
        $("div#mandatory-msg-2.alert").css('display','none');
        $("#WERO004Form").attr('action', 'WERO004.php');
        $("#WERO004Form").submit();
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
    
    $("td a.faq-list").click(function() {
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

