$(document).ready(function() {
    /**
     * =========================================================================================================
     * INITIAL VALUE
     * =========================================================================================================
     **/
    var getCountAppVal = $("#countApp").val();
    
    for(var u = 0; u < getCountAppVal; u++){
        $("select#approverNo"+(u+1)+".span3 option[value='"+$("#setApproverNpk"+(u+1)).val()+"']").prop("selected", true);
        if( ($("#setApproverNpk"+(u+1)).val() != '' && ($("#setApproverSts"+(u+1)).val() != 'WA'))){
            $("select#approverNo"+(u+1)+".span3").attr('disabled', false);
        }
        if(($("#setApproverSts"+(u+1)).val() == 'AP') || ($("#setApproverSts"+(u+1)).val() == 'BP') ){
            $("select#approverNo"+(u+1)+".span3").attr('disabled', true);
        }
    }
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
    $("#btn-approve").click(function(){
		/** Approve confirmation */
		$("#dialog-confirm-approve").html("Do you want approve this PO?");
		$("#dialog-confirm-approve").dialog({
			//autoOpen    : false,
			height      : 155,
			width       : 400,
			//position    : { my: "center", at: "top", of: $("body"), within: $("body") },
			modal       : true,
			buttons     : {
				"Yes": function(){
					$(this).dialog("close");
					var eDataPo;
					var poNo        = $.trim($("#poNo").val());
					var countApp    = $("#countApp").val();
                    var remarkApp   = $.trim($("#remarkApprover").val());
                    var addRemark   = $.trim($("#addRemark").val());
					var appNoVal    = 1;
					var poApprover  = [];
					
					for(var j = 0; j < countApp; j++){
						/** Get approver selectbox value */
						var npkApprover = $("select#approverNo"+appNoVal+".span3").val();
						poApprover.push([appNoVal+'-'+npkApprover]);  
						appNoVal++;
					}
					eDataPo         = "poNoPrm="+poNo+"&remarkAppPrm="+encodeURIComponent(remarkApp)+"&addRemarkPrm="+encodeURIComponent(addRemark)+"&npkApproverArray="+poApprover;
					
					$.ajax({
						type: 'GET',
						url: '../db/PO/UPDATE_PO.php?action=ApprovePo',
						data: eDataPo,
						success: function(data){
							//console.log(data);
							if($.trim(data) == 'Success'){
								$("div#mandatory-msg-1.alert").css('display','none');
								$("div#undefined-msg.alert").css('display','none');
								$("div#session-msg.alert").css('display','none');
								$("div#success-msg.alert.alert-success").css('display','block');
								$("div#reject-msg.alert.alert-success").css('display','none');

								$("button#btn-approve.btn.btn-primary").attr('disabled', true);
								$("button#btn-reject.btn.btn-danger").attr('disabled', true);
								$("#remarkApprover").attr('readonly', true);
								
								$("select.span3").attr('disabled', true);
                                window.location = "WEPO018.php";
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
		$("#dialog-confirm-reject").html("Do you want reject this PO?");
		$("#dialog-confirm-reject").dialog({
			//autoOpen    : false,
			height      : 155,
			width       : 400,
			//position    : { my: "center", at: "top", of: $("body"), within: $("body") },
			modal       : true,
			buttons     : {
				"Yes": function(){
					$(this).dialog("close");
					$("#approver-information").css('display','block');
					var eDataPo;
					var poNo        = $.trim($("#poNo").val());
					var remarkApp   = $.trim($("#remarkApprover").val());
					eDataPo = "poNoPrm="+poNo+"&remarkAppPrm="+encodeURIComponent(remarkApp);
						
					$.ajax({
						type: 'GET',
						url: '../db/PO/UPDATE_PO.php?action=RejectPo',
						data: eDataPo,
						success: function(data){
							//alert(data);
							if($.trim(data) == 'Success'){
								$("div#mandatory-msg-1.alert").css('display','none');
								$("div#undefined-msg.alert").css('display','none');
								$("div#session-msg.alert").css('display','none');
								$("div#success-msg.alert.alert-success").css('display','none');
								$("div#reject-msg.alert.alert-success").css('display','block');

								$("button#btn-approve.btn.btn-primary").attr('disabled', true);
								$("button#btn-reject.btn.btn-danger").attr('disabled', true);
								$("#remarkApprover").attr('readonly', true);
								
								$("select.span3").attr('disabled', true);
                                window.location = "WEPO018.php";
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
		//$("#dialog-confirm-reject").dialog("open");
    });
    
    $("#btn-back").click(function(){
        /** Back confirmation */
        $("#dialog-confirm-back").html("Do you want back to menu PO List ?");
        $("#dialog-confirm-back").dialog({
            //autoOpen    : false,
            height      : 155,
            width       : 400,
            modal       : true,
            buttons     : {
                "Yes": function(){
                    $(this).dialog("close");
                    window.location = "WEPO005.php";
                },
                "No": function(){
                    $(this).dialog("close");
                }
            }
        });
        //$("#dialog-confirm-back").dialog("open");
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
    //attachment
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
});


