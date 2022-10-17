$(document).ready(function() {
    /**
     * =========================================================================================================
     * INITIAL VALUE
     * =========================================================================================================
     **/
    $("#deliveryDate").css('background-color', '#ffffff');
    /*var getCountAppVal = $("#countApp").val();
    
    for(var u = 0; u < getCountAppVal; u++){
        $("select#approverNo"+(u+1)+".span3 option[value='"+$("#setApproverNpk"+(u+1)).val()+"']").prop("selected", true);
        if($("#setApproverNpk"+(u+1)).val() != ''){
            $("select#approverNo"+(u+1)+".span3").attr('disabled', false);
            $("input#setBypassNo"+(u+1)).attr('disabled', false);
            //$("button#btn-send.btn.btn-primary").attr('disabled', false);
            //$("button#btn-save.btn.btn-success").attr('disabled', false);
            //$("button#btn-calculate.btn.btn-warning").attr('disabled', true);
        }
        if($("#setApproverSts"+(u+1)).val() == 'BP'){
            $("select#approverNo"+(u+1)+".span3").attr('disabled', true);
            $("input#setBypassNo"+(u+1)).prop('checked', true);
        }
    }*/
    
    /**
     * =========================================================================================================
     * INPUT FUNCTION
     * =========================================================================================================
     **/
    $("input[type='checkbox']").click(function () {
        var checkedLength   = $("input[type='checkbox']:checked").length;
        var checkedVal      = $(this).prop('checked');
        var index           = $.trim($(this).attr("id").substr(11,1));
        var approverVal     = $("select#approverNo"+index+".span3").val();
        
        if(checkedVal == true && approverVal == '')
        {
            $("input#setBypassNo"+index).prop('checked', false);
            $("div#mandatory-msg-3.alert").css('display','block');
        }
        else if(checkedVal == true && approverVal != '')
        {
            $("div#mandatory-msg-3.alert").css('display','none');
            $("#setReasonNo"+index).attr('readonly', false);
            $("select#approverNo"+index+".span3").attr('disabled', true);
            $("button#btn-save-bypass-"+index+".btn").attr('disabled', false);  
        }
        else{
            $("div#mandatory-msg-3.alert").css('display','none');
            $("#setReasonNo"+index).val('');
            $("#setReasonNo"+index).attr('readonly', true);
            $("select#approverNo"+index+".span3").attr('disabled', false);
            $("button#btn-save-bypass-"+index+".btn").attr('disabled', true);
        }
    });
    
    $("#qty").keypress(function(e) {
        //if the letter is not digit then display error and don't type anything
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            //display error message
            //$("#errmsg").html("Digits Only").show().fadeOut("slow");
            return false;
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
    /** Dialog form */
    $("#dialog-form").dialog({
        autoOpen    : false,
        closeOnEscape   : false,
		height      : 450,
		width       : 650,
        position    : { my: "center", at: "top", of: $("body"), within: $("body") },
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
                var qty             = $.trim($("#qty").val());
                var currencyCd      = $.trim($("#currencyCdHidden").val());
                var unitCd          = $.trim($("#um").val());
                var price           = $.trim($("#price").val().replace(/,/g, ''));
                var amount          = qty * price;
                var supplierCd      = $.trim($("#supplierCd").val());
                var supplierName    = $.trim($("#supplierName option[value="+supplierCd+"]").text());
                var seqItem         = $.trim($("#seqItem").val());
                var refTransferId   = $.trim($("#refTransferIdHidden").val());
                
                edata = "supplierCdSetPrm="+supplierCdSet+"&supplierCdPrm="+supplierCd+"&supplierNamePrm="+supplierName
                        +"&itemCdPrm="+itemCd+"&itemNamePrm="+itemName+"&currencyCdPrm="+currencyCd
                        +"&unitCdPrm="+unitCd+"&qtyPrm="+qty+"&itemPricePrm="+price+"&amountPrm="+amount
                        +"&seqItemPrm="+seqItem+"&refTransferIdPrm="+refTransferId;
                    
                $.ajax({
                    type: 'GET',
                    url: '../db/PO/UPDATE_ITEM_SESSION.php?action=EditItemPo',
                    data: edata,
                    success: function(data){
                        //alert($.trim(data));
                        $("div#dialog-mandatory-msg-1.alert").css('display','none');
                        $("div#dialog-mandatory-msg-2.alert").css('display','none');
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
                            $("div#dialog-undefined-msg.alert").css('display','none');
                        }
                        else if($.trim(data) == 'Mandatory_2')
                        {
                            $("div#dialog-mandatory-msg-1.alert").css('display','none');
                            $("div#dialog-mandatory-msg-2.alert").css('display','block');
                            $("div#dialog-mandatory-msg-3.alert").css('display','none');
                            $("div#dialog-undefined-msg.alert").css('display','none');
                        }
                        else if($.trim(data) == 'Mandatory_3')
                        {
                            $("div#dialog-mandatory-msg-1.alert").css('display','none');
                            $("div#dialog-mandatory-msg-2.alert").css('display','none');
                            $("div#dialog-mandatory-msg-3.alert").css('display','block');
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
                            $("div#dialog-undefined-msg.alert").css('display','block');
                        }    
                    }
                });
            },
            "Cancel": function(){
                $(this).dialog("close");
                window.location.reload();
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
    $("#btn-save").click(function(){
		/** Save confirmation */
		$("#dialog-confirm-save").html("Do you want save this PO?");
		$("#dialog-confirm-save").dialog({
			//autoOpen    : false,
			height      : 155,
			width       : 400,
			//position    : { my: "center", at: "top", of: $("body"), within: $("body") },
			modal       : true,
			buttons     : {
				"Yes": function(){
					$(this).dialog("close");
					var poNo                = $.trim($("#poNo").val());
					var deliveryDate        = $.trim($("#deliveryDate").val());
					var addRemark           = $.trim($("#addRemark").val());
					var countApp            = $("#countApp").val();
					var appNoVal            = 1;
					var appNoVal2           = 1;
					var countErrorSelectbox = 0;
					var countErrorChceckbox = 0;
					var poApprover          = [];
					var bypassApprover      = new Array();
					var eDataPo;
					
					for(var i = 0; i < countApp; i++){
						/** Check if approver selectbox blank value */
						if($("select#approverNo"+appNoVal+".span3").val() == ''){
							countErrorSelectbox++;
						}
						/** Check if approver reason bypass blank value */
						if($("input#setBypassNo"+appNoVal).prop('checked') == true
							&& $.trim($("input#setReasonNo"+appNoVal).val()) == ''){
							countErrorChceckbox++;
						}
						appNoVal++;
						
					}  
					
					if(countErrorSelectbox > 0)
					{
						$("div#mandatory-msg-1.alert").css('display','block');
						$("div#mandatory-msg-2.alert").css('display','none');
						$("div#mandatory-msg-3.alert").css('display','none');
						$("div#mandatory-msg-4.alert").css('display','none');
						$("div#save-msg.alert").css('display','none');
					}
					else if(countErrorChceckbox > 0)
					{
						$("div#mandatory-msg-1.alert").css('display','none');
						$("div#mandatory-msg-2.alert").css('display','block');
						$("div#mandatory-msg-3.alert").css('display','none');
						$("div#mandatory-msg-4.alert").css('display','none');
						$("div#save-msg.alert").css('display','none');    
					}
					else
					{
						for(var j = 0; j < countApp; j++){
							/** Get approver selectbox value */
							var npkApprover = $("select#approverNo"+appNoVal2+".span3").val();
							poApprover.push([npkApprover]);  
							
							/** Get approver bypass reason value */
							if($("input#setBypassNo"+appNoVal2).prop('checked') == true){
								var bypassReason = $.trim($("input#setReasonNo"+appNoVal2).val());
								bypassApprover.push(appNoVal2+bypassReason);
							}
							appNoVal2++;
						}
						eDataPo = "poNoPrm="+poNo+"&deliveryDatePrm="+deliveryDate+"&addRemarkPrm="+addRemark
									+"&npkApproverArray="+poApprover
									+"&bypassApproverArray="+bypassApprover;
						
						$.ajax({
							type: 'GET',
							url: '../db/PO/CREATE_PO.php?action=SavePoAfterEdit',
							data: eDataPo,
							success: function(data){
								//alert(data);
								if($.trim(data) == 'Success'){
									$("div#mandatory-msg-1.alert").css('display','none');
									$("div#mandatory-msg-2.alert").css('display','none');
									$("div#mandatory-msg-3.alert").css('display','none');
									$("div#mandatory-msg-4.alert").css('display','none');
									$("div#undefined-msg.alert").css('display','none');
									$("div#session-msg.alert").css('display','none');
									$("div#save-msg.alert").css('display','block');

									$("button#btn-send.btn.btn-primary").attr('disabled', true);
									$("button#btn-save.btn.btn-success").attr('disabled', true);
                                    $("button#btn-calculate.btn.btn-warning").attr('disabled', true);
									$("button#btn-cancel.btn.btn-danger").attr('disabled', true);
									$("#addRemark").attr('readonly', true);
									$("#deliveryDate").attr('readonly', true);

									$("a#window-edit.btn.btn-small.btn-success").css('display', 'none');
									$("a#window-delete.btn.btn-small.btn-danger").css('display', 'none');

									$("select.span3").attr('disabled', true);
									$("input[type='checkbox']").attr('disabled', true);
								}
								else if($.trim(data) == 'Mandatory_1')
								{
									$("div#mandatory-msg-1.alert").css('display','none');
									$("div#mandatory-msg-2.alert").css('display','none');
									$("div#mandatory-msg-3.alert").css('display','none');
									$("div#mandatory-msg-4.alert").css('display','block');
									$("div#undefined-msg.alert").css('display','none');
									$("div#session-msg.alert").css('display','none');
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
									$("div#undefined-msg.alert").css('display','block');
									$("div#session-msg.alert").css('display','none');
								}
							} 
						});
					}
				},
				"No": function(){
					$(this).dialog("close");
				}
			}
		});
		//$("#dialog-confirm-save").dialog("open");
    });
    
    $("#btn-send").click(function(){
		/** Send confirmation */
		$("#dialog-confirm-send").html("Do you want send this PO?");
		$("#dialog-confirm-send").dialog({
			//autoOpen    : false,
			height      : 155,
			width       : 400,
			//position    : { my: "center", at: "top", of: $("body"), within: $("body") },
			modal       : true,
			buttons     : {
				"Yes": function(){
					$(this).dialog("close");
					var poNo                = $.trim($("#poNo").val());
					var deliveryDate        = $.trim($("#deliveryDate").val());
					var addRemark           = $.trim($("#addRemark").val());
					var countApp            = $("#countApp").val();
					var appNoVal            = 1;
					var appNoVal2           = 1;
					var countErrorSelectbox = 0;
					var countErrorChceckbox = 0;
					var poApprover          = [];
					var bypassApprover      = new Array();
					var eDataPo;
					
					for(var i = 0; i < countApp; i++){
						/** Check if approver selectbox blank value */
						if($("select#approverNo"+appNoVal+".span3").val() == ''){
							countErrorSelectbox++;
						}
						/** Check if approver reason bypass blank value */
						if($("input#setBypassNo"+appNoVal).prop('checked') == true
							&& $.trim($("input#setReasonNo"+appNoVal).val()) == ''){
							countErrorChceckbox++;
						}
						appNoVal++;
						
					}   
					if(countErrorSelectbox > 0)
					{
						$("div#mandatory-msg-1.alert").css('display','block');
						$("div#mandatory-msg-2.alert").css('display','none');
						$("div#mandatory-msg-3.alert").css('display','none');
						$("div#mandatory-msg-4.alert").css('display','none');
						$("div#save-msg.alert").css('display','none');
					}
					else if(countErrorChceckbox > 0)
					{
						$("div#mandatory-msg-1.alert").css('display','none');
						$("div#mandatory-msg-2.alert").css('display','block');
						$("div#mandatory-msg-3.alert").css('display','none');
						$("div#mandatory-msg-4.alert").css('display','none');
						$("div#save-msg.alert").css('display','none');    
					}
					else{
						for(var j = 0; j < countApp; j++){
							/** Get approver selectbox value */
							var npkApprover = $("select#approverNo"+appNoVal2+".span3").val();
							poApprover.push([npkApprover]);  
							
							/** Get approver bypass reason value */
							if($("input#setBypassNo"+appNoVal2).prop('checked') == true){
								var bypassReason = $.trim($("input#setReasonNo"+appNoVal2).val());
								bypassApprover.push(appNoVal2+bypassReason);
							}
							appNoVal2++;
						}
						eDataPo = "poNoPrm="+poNo+"&deliveryDatePrm="+deliveryDate+"&addRemarkPrm="+addRemark
									+"&npkApproverArray="+poApprover
									+"&bypassApproverArray="+bypassApprover;
						
						$.ajax({
							type: 'GET',
							url: '../db/PO/CREATE_PO.php?action=SendPoAfterEdit',
							data: eDataPo,
							success: function(data){
								//alert(data);
								if($.trim(data) == 'Success'){
									$("div#mandatory-msg-1.alert").css('display','none');
									$("div#mandatory-msg-2.alert").css('display','none');
									$("div#mandatory-msg-3.alert").css('display','none');
									$("div#mandatory-msg-4.alert").css('display','none');
									$("div#undefined-msg.alert").css('display','none');
									$("div#session-msg.alert").css('display','none');
									$("div#send-msg.alert").css('display','block');

									$("button#btn-send.btn.btn-primary").attr('disabled', true);
									$("button#btn-save.btn.btn-success").attr('disabled', true);
                                    $("button#btn-calculate.btn.btn-warning").attr('disabled', true);
									$("button#btn-cancel.btn.btn-danger").attr('disabled', true);
									$("#addRemark").attr('readonly', true);
									$("#deliveryDate").attr('readonly', true);
									
									$("a#window-edit.btn.btn-small.btn-success").css('display', 'none');
									$("a#window-delete.btn.btn-small.btn-danger").css('display', 'none');

									$("select.span3").attr('disabled', true);
									$("input[type='checkbox']").attr('disabled', true);
								}
								else if($.trim(data) == 'Mandatory_1')
								{
									$("div#mandatory-msg-1.alert").css('display','none');
									$("div#mandatory-msg-2.alert").css('display','none');
									$("div#mandatory-msg-3.alert").css('display','none');
									$("div#mandatory-msg-4.alert").css('display','block');
									$("div#undefined-msg.alert").css('display','none');
									$("div#session-msg.alert").css('display','none');
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
									$("div#undefined-msg.alert").css('display','block');
									$("div#session-msg.alert").css('display','none');
								}
							} 
						});
					}
				},
				"No": function(){
					$(this).dialog("close");
				}
			} 
		});
        //$("#dialog-confirm-send").dialog("open");
    });
    
    $("#btn-calculate").click(function(){
        var max = getMaxPoAmount();
        var currencyCd = $.trim($("#currencyCd").val());
        var eMaxAmount;
        
        eMaxAmount = "maxPoAmountPrm="+max+"&currencyCdPrm="+currencyCd;
        $.ajax({
            type: 'GET',
            url: '../db/GET_TABLE/EPS_M_PO_APPROVER.php',
            data: eMaxAmount,
            success: function(data){
                if($.trim(data).substr(0,7) == 'Success')
                {
                    var countApp = parseInt($.trim(data).substr(7,1));
                    var appNoVal = 1;
                    
                    for(var i = 0; i < countApp; i++){
                        $("select#approverNo"+appNoVal+".span3").attr('disabled', false);
                        
                        if(countApp != appNoVal){
                           $("input#setBypassNo"+appNoVal).attr('disabled', false);
                        }
                        appNoVal++;
                        
                    }
                    $("button#btn-send.btn.btn-primary").attr('disabled', false);
                    $("button#btn-save.btn.btn-success").attr('disabled', false);
                    $("button#btn-calculate.btn-warning").attr('disabled', true);
                    $("div#session-msg.alert").css('display','none');
                    $("#countApp").val(countApp);
                }else{
                    $("div#session-msg.alert").css('display','block');
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
			//position    : {my: "center", at: "top", of: $("body"), within: $("body")},
			modal       : true,
			buttons     : {
				"Yes": function(){
					$(this).dialog("close");
					var poNo    = $.trim($("#poNo").val());
					var eDataPo;
					eDataPo     = "poNoPrm="+poNo;
					
					$.ajax({
						type: 'GET',
						url: '../db/PO/CREATE_PO.php?action=CancelPo',
						data: eDataPo,
						success: function(data){
							if($.trim(data) == 'Success'){
								$("div#mandatory-msg-1.alert").css('display','none');
								$("div#mandatory-msg-2.alert").css('display','none');
								$("div#mandatory-msg-3.alert").css('display','none');
								$("div#mandatory-msg-4.alert").css('display','none');
								$("div#undefined-msg.alert").css('display','none');
								$("div#session-msg.alert").css('display','none');
								$("div#cancel-msg.alert").css('display','block');

								$("button#btn-send.btn.btn-primary").attr('disabled', true);
								$("button#btn-save.btn.btn-success").attr('disabled', true);
                                $("button#btn-calculate.btn.btn-warning").attr('disabled', true);
								$("button#btn-cancel.btn.btn-danger").attr('disabled', true);
								$("#addRemark").attr('readonly', true);
								$("#deliveryDate").attr('readonly', true);

								$("a#window-edit.btn.btn-small.btn-success").css('display', 'none');
								$("a#window-delete.btn.btn-small.btn-danger").css('display', 'none');

								$("select.span3").attr('disabled', true);
								$("input[type='checkbox']").attr('disabled', true);
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
        //$("#dialog-confirm-cancel").dialog("open");
    });
    
    $("#btn-back").click(function(){
		/** Back confirmation */
		$("#dialog-confirm-back").html("Do you want back to PO list?");
		$("#dialog-confirm-back").dialog({
			//autoOpen    : false,
			height      : 155,
			width       : 400,
			//position    : {my: "center", at: "top", of: $("body"), within: $("body")},
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
        
        if($.trim($("#itemCd").val()) != '99'){
            $("#supplierCd").attr('disabled', true);
            $("#supplierName").attr('disabled', true);
            $("#um").attr('disabled', true);
            $("#price").attr('readonly', true);
        }
    });
    
    $("table .btn-danger").click(function() {
        var eDeleteData;
        var index       = $(this).closest('tr').index();
        var indexArray  = $.trim(index);
        index = index + 1;
        
        var refTransferIdVal= $.trim($("table#poItemTable.table.table-striped.table-bordered tbody tr#"+index+" td#getTransferId"+index).text());
        var itemCdVal       = $.trim($("table#poItemTable.table.table-striped.table-bordered tbody tr#"+index+" td#getItemCd"+index).text());
        var itemNameVal     = $.trim($("table#poItemTable.table.table-striped.table-bordered tbody tr#"+index+" td#getItemName"+index).text());
        var qtyVal          = $.trim($("table#poItemTable.table.table-striped.table-bordered tbody tr#"+index+" td#getQty"+index).text());
        var priceVal        = $.trim($("table#poItemTable.table.table-striped.table-bordered tbody tr#"+index+" td#getItemPrice"+index).text()).replace(/,/g, '');
        var amountVal       = qtyVal * priceVal;
        var currencyCdVal   = $.trim($("table#poItemTable.table.table-striped.table-bordered tbody tr#"+index+" td#getCurrencyCd"+index).text());
        var unitCdVal       = $.trim($("table#poItemTable.table.table-striped.table-bordered tbody tr#"+index+" td#getUnitCd"+index).text());
        var seqPoItemVal    = $.trim($("table#poItemTable.table.table-striped.table-bordered tbody tr#"+index+" td#getSeqPoItem"+index).text());
        var totalSupplierVal= $.trim($("table#poItemTable.table.table-striped.table-bordered tbody tr#"+index+" td#getTotalSupplier"+index).text());
        
        eDeleteData     = "indexArrayPrm="+indexArray+"&refTransferIdValPrm="+refTransferIdVal
                            +"&itemCdValPrm="+itemCdVal+"&itemNameValPrm="+itemNameVal
                            +"&qtyValPrm="+qtyVal+"&priceValPrm="+priceVal
                            +"&amountValPrm="+amountVal+"&currencyCdValPrm="+currencyCdVal
                            +"&unitCdValPrm="+unitCdVal+"&seqPoItemValPrm="+seqPoItemVal
                            +"&totalSupplierValPrm="+totalSupplierVal;
                        
        $("select").attr("disabled", true);            
        $.ajax({
            type: 'GET',
            url: '../db/PO/UPDATE_ITEM_SESSION.php?action=DeleteItemPo',
            data: eDeleteData,
            success: function(data){
                window.location.reload();
            }
        });
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
    
    function getMaxPoAmount(){
        var max = 0;
        $("td.td-align-right.amount").each(function(){
            if( parseInt($(this).text().replace(/,/g, '')) > max){
                max = parseInt($(this).text().replace(/,/g, ''));
            }
        });
        return max;
    }
});

