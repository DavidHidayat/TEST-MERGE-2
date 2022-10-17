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
        var poNoVal = $.trim($("#poNo").val());
        
        if(poNoVal == '')
        {
            $("div#mandatory-msg-1.alert").css('display','block');
            $("div#mandatory-msg-2.alert").css('display','none');
        }
        else
        {
            $("div#mandatory-msg-1.alert").css('display','none');
            $("div#mandatory-msg-2.alert").css('display','none');
            $("#WERO001Form").attr('action', 'WERO001.php');
            $("#WERO001Form").submit();
        }
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
});


