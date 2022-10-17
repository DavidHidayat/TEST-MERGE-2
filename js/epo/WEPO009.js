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

$("table .btn-success").click(function() {
        var prNo        = $.trim($(this).closest('tr').find('td:eq(10)').text());
        var refItemName = $.trim($(this).closest('tr').find('td:eq(3)').text());
        var edataAttach;
        edataAttach = "prNoPrm="+prNo+"&refItemNamePrm="+encodeURIComponent(refItemName);
        
        $.ajax({
            type: 'GET',
            url: '../db/GET_TABLE/EPS_T_PR_ATTACHMENT.php?criteria=AttachmentPRItem',
            data: edataAttach,
            success: function(data){
				
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
    /**
     * =========================================================================================================
     * LINK FUNCTION
     * =========================================================================================================
     **/
    $("td a.faq-list b").click(function() {
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
    
    $("table .btn-inverse").click(function() {
        var refTransferId   = $.trim($(this).closest('tr').find('td:eq(1)').text());
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


