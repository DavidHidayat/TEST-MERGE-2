$(document).ready(function() {
    
    /**
     * =========================================================================================================
     * INITIAL VALUE
     * =========================================================================================================
     **/
    $("#newPrCharged option[value='"+$("#prCharged").val()+"']").prop("selected", true); 
    /*$("#deliveryDate").css('background-color', '#ffffff');*/
	
    /**
     * =========================================================================================================
     * INPUT FUNCTION
     * =========================================================================================================
     **/
    $("#qty").keypress(function(e) {
        //if the letter is not digit then display error and don't type anything
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            //display error message
            //$("#errmsg").html("Digits Only").show().fadeOut("slow");
            return false;
        }
    });
    
    $("input#price").keyup(function(event) {
        // skip for arrow keys
        if(event.which >= 37 && event.which <= 40) return;

        // format number
        $(this).val(function(index, value) {
            return value
                .replace(/\D/g, '')
                .replace(/\B(?=(\d{3})+(?!\d))/g, ",")
            ;
        });
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
    
    $("#itemType").change(function(){
        var str = "";
        $( "#itemType option:selected" ).each(function() {
            str += $( this ).text() + " ";
        });
        
        if($.trim(str) == 'EXP'){
            $("td#td-exp-no").css('display','block');
            $("td#td-inv-no").css('display','none');
            $("td#td-rfi-no").css('display','none');
              
            $("#invNo").val('');
            $("#rfiNo").val('');
        }else if($.trim(str) == 'INV'){
            $("td#td-exp-no").css('display','none');
            $("td#td-inv-no").css('display','block');
            $("td#td-rfi-no").css('display','none');
            
            $("#expNo").val(''); 
            $("#rfiNo").val('');
        }else{
            if($.trim(str) == 'RFI'){
                $("td#td-exp-no").css('display','none');
                $("td#td-inv-no").css('display','none');
                $("td#td-rfi-no").css('display','block');
                
                $("#expNo").val(''); 
                $("#invNo").val('');
                
                $("#rfiNo").removeAttr('readonly');
            }
        }
        
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
		height      : 600,
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
                var prNoItem    = $.trim($("#prNoItem").val());
                var refItemName = $.trim($("#refItemName").val());
                var itemSts     = $.trim($("#itemStatus").val());
                var itemType    = $.trim($("#itemType").val());
                var accountNo   = $.trim($("#expNo").val());
                var invNo       = $.trim($("#invNo").val());
                var rfiNo       = $.trim($("#rfiNo").val());
                var itemCd      = $.trim($("#itemCd").val());
                var itemName    = $.trim($("#itemName").val());
                var deliveryDate= $.trim($("#deliveryDate").val());
                var qty         = $.trim($("#qty").val());
                var unitCd      = $.trim($("#um").val());
                var price       = $.trim($("#price").val().replace(/,/g, ''));
                var amount      = qty * price;
                var supplierCd  = $.trim($("#supplierCd").val());
                var supplierName= $.trim($("#supplierName option[value="+supplierCd+"]").text());
                var remark      = $.trim($("#remark").val());
                var seqItem     = $.trim($("#seqItem").val());
                var currencyCd  = $.trim($("#currencyCd").val());
                var itemNameGet = $.trim($("table#prItemTable.table.table-striped.table-bordered tbody tr#"+seqItem+" td#getItemName"+seqItem).text());
                var itemStsAlias= $.trim($("#itemStatus option:selected" ).text()).substr(0,3);

                edata = "itemCdPrm="+itemCd+"&itemNamePrm="+encodeURIComponent(itemName)
                        +"&remarkPrm="+encodeURIComponent(remark)+"&deliveryDatePrm="+deliveryDate+"&itemTypePrm="+itemType
                        +"&rfiNoPrm="+rfiNo+"&accountNoPrm="+accountNo+"&invNoPrm="+invNo
                        +"&supplierCdPrm="+supplierCd+"&supplierNamePrm="+encodeURIComponent(supplierName)
                        +"&unitCdPrm="+unitCd+"&qtyPrm="+qty+"&itemPricePrm="+price+"&amountPrm="+amount+"&currencyCdPrm="+currencyCd
                        +"&itemStsPrm="+itemSts+"&prNoPrm="+prNoItem +"&refItemNamePrm="+encodeURIComponent(refItemName)+"&seqItemPrm="+seqItem
                        +"&itemNameGetPrm="+encodeURIComponent(itemNameGet)+"&itemStsAliasPrm="+itemStsAlias;
                    
                $.ajax({
                    type: 'GET',
                    url: '../db/PR_to_PO/UPDATE_ITEM_SESSION.php',
                    data: edata,
                    success: function(data){
                        //alert($.trim(data));
                        $("div#dialog-mandatory-msg-1.alert").css('display','none');
                        $("div#dialog-mandatory-msg-2.alert").css('display','none');
                        $("div#dialog-mandatory-msg-3.alert").css('display','none');
                        $("div#dialog-mandatory-msg-4.alert").css('display','none');
                        $("div#dialog-mandatory-msg-5.alert").css('display','none');
                        $("div#dialog-mandatory-msg-6.alert").css('display','none');
                        $("div#dialog-mandatory-msg-7.alert").css('display','none');
                        $("div#dialog-mandatory-msg-8.alert").css('display','none');
                        $("div#dialog-duplicate-msg.alert").css('display','none');
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
                            $("div#dialog-mandatory-msg-7.alert").css('display','none');
                            $("div#dialog-mandatory-msg-8.alert").css('display','none');
                            $("div#dialog-duplicate-msg.alert").css('display','none');
                            $("div#dialog-undefined-msg.alert").css('display','none');
                        }
                        else if($.trim(data) == 'Mandatory_2')
                        {
                            $("div#dialog-mandatory-msg-1.alert").css('display','none');
                            $("div#dialog-mandatory-msg-2.alert").css('display','block');
                            $("div#dialog-duplicate-msg-3.alert").css('display','none');
                            $("div#dialog-mandatory-msg-4.alert").css('display','none');
                            $("div#dialog-mandatory-msg-5.alert").css('display','none');
                            $("div#dialog-mandatory-msg-6.alert").css('display','none');
                            $("div#dialog-mandatory-msg-7.alert").css('display','none');
                            $("div#dialog-mandatory-msg-8.alert").css('display','none');
                            $("div#dialog-duplicate-msg.alert").css('display','none');
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
                            $("div#dialog-mandatory-msg-7.alert").css('display','none');
                            $("div#dialog-mandatory-msg-8.alert").css('display','none');
                            $("div#dialog-duplicate-msg.alert").css('display','none');
                            $("div#dialog-undefined-msg.alert").css('display','none');
                        }
                        else if($.trim(data) == 'Mandatory_4')
                        {
                            $("div#dialog-mandatory-msg-1.alert").css('display','none');
                            $("div#dialog-mandatory-msg-2.alert").css('display','none');
                            $("div#dialog-mandatory-msg-3.alert").css('display','none');
                            $("div#dialog-mandatory-msg-4.alert").css('display','block');
                            $("div#dialog-mandatory-msg-5.alert").css('display','none');
                            $("div#dialog-mandatory-msg-6.alert").css('display','none');
                            $("div#dialog-mandatory-msg-7.alert").css('display','none');
                            $("div#dialog-mandatory-msg-8.alert").css('display','none');
                            $("div#dialog-duplicate-msg.alert").css('display','none');
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
                            $("div#dialog-mandatory-msg-7.alert").css('display','none');
                            $("div#dialog-mandatory-msg-8.alert").css('display','none');
                            $("div#dialog-duplicate-msg.alert").css('display','none');
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
                            $("div#dialog-mandatory-msg-7.alert").css('display','none');
                            $("div#dialog-mandatory-msg-8.alert").css('display','none');
                            $("div#dialog-duplicate-msg.alert").css('display','none');
                            $("div#dialog-undefined-msg.alert").css('display','none');
                        }
                        else if($.trim(data) == 'Mandatory_7')
                        {
                            $("div#dialog-mandatory-msg-1.alert").css('display','none');
                            $("div#dialog-mandatory-msg-2.alert").css('display','none');
                            $("div#dialog-duplicate-msg-3.alert").css('display','none');
                            $("div#dialog-mandatory-msg-4.alert").css('display','none');
                            $("div#dialog-mandatory-msg-5.alert").css('display','none');
                            $("div#dialog-mandatory-msg-6.alert").css('display','none');
                            $("div#dialog-mandatory-msg-7.alert").css('display','block');
                            $("div#dialog-mandatory-msg-8.alert").css('display','none');
                            $("div#dialog-duplicate-msg.alert").css('display','none');
                            $("div#dialog-undefined-msg.alert").css('display','none');
                        }
                        else if($.trim(data) == 'Mandatory_8')
                        {
                            $("div#dialog-mandatory-msg-1.alert").css('display','none');
                            $("div#dialog-mandatory-msg-2.alert").css('display','none');
                            $("div#dialog-duplicate-msg-3.alert").css('display','none');
                            $("div#dialog-mandatory-msg-4.alert").css('display','none');
                            $("div#dialog-mandatory-msg-5.alert").css('display','none');
                            $("div#dialog-mandatory-msg-6.alert").css('display','none');
                            $("div#dialog-mandatory-msg-7.alert").css('display','none');
                            $("div#dialog-mandatory-msg-8.alert").css('display','block');
                            $("div#dialog-duplicate-msg.alert").css('display','none');
                            $("div#dialog-undefined-msg.alert").css('display','none');
                        }
                        else if($.trim(data) == 'Duplicate')
                        {
                            $("div#dialog-mandatory-msg-1.alert").css('display','none');
                            $("div#dialog-mandatory-msg-2.alert").css('display','none');
                            $("div#dialog-mandatory-msg-3.alert").css('display','none');
                            $("div#dialog-mandatory-msg-4.alert").css('display','none');
                            $("div#dialog-mandatory-msg-5.alert").css('display','none');
                            $("div#dialog-mandatory-msg-6.alert").css('display','none');
                            $("div#dialog-mandatory-msg-7.alert").css('display','none');
                            $("div#dialog-mandatory-msg-8.alert").css('display','none');
                            $("div#dialog-duplicate-msg.alert").css('display','block');
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
                            $("div#dialog-mandatory-msg-7.alert").css('display','none');
                            $("div#dialog-mandatory-msg-8.alert").css('display','none');
                            $("div#dialog-duplicate-msg.alert").css('display','none');
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
    $("#btn-accept").click(function(){
		/** Accept confirmation */
		$("#dialog-confirm-accept").html("Do you want accept this PR?");
		$("#dialog-confirm-accept").dialog({
			//autoOpen    : false,
			height      : 155,
			width       : 400,
			//position    : { my: "center", at: "top", of: $("body"), within: $("body") },
			modal       : true,
			buttons     : {
				"Yes": function(){
					$(this).dialog("close");
					var edataPr;
					var prNoVal         = $("#prNo").val();
					var requesterVal    = $("#requester").val();
					var prChargedVal    = $("#prCharged").val();
					var newPrChargedVal = $("#newPrCharged").val();
					var remarkProcVal   = $("#remarkProc").val();
                    var procPrUpdateDateVal = $("#procPrUpdateDate").val();
                    var rowCount            = ($("#prItemTable > tbody >tr").length) - 1;
                    var itemStatusArray     = new Array();
                    var itemStatusArrayMulti= new Array();
					
                    for(var i = 0; i < rowCount; i++){
                        var itemStatusGet = $.trim($("table#prItemTable.table.table-striped.table-bordered tbody tr#"+i+" td select#getItemStatus"+i).val());
                        var itemNameGet   = $.trim($("table#prItemTable.table.table-striped.table-bordered tbody tr#"+i+" td#getItemName"+i).text());
                        var deliveryDateGet= $.trim($("table#prItemTable.table.table-striped.table-bordered tbody tr#"+i+" td input#getDeliveryDate"+i+".hasDatepicker").val());
                        
                        itemStatusArray.push(itemStatusGet+deliveryDateGet+itemNameGet);
                        itemStatusArrayMulti = itemStatusArray.join("::");
                    }
					edataPr = "prNoPrm="+prNoVal+"&requesterPrm="+requesterVal
                            +"&prChargedPrm="+prChargedVal+"&newPrChargedPrm="+newPrChargedVal
                            +"&remarkProcPrm="+encodeURIComponent(remarkProcVal)+"&procPrUpdateDatePrm="+procPrUpdateDateVal
                            +"&itemStatusArrayPrm="+encodeURIComponent(itemStatusArrayMulti);

					$.ajax({
						type: 'GET',
						url: '../db/PR_to_PO/EPS_T_TRANSFER.php?action=AcceptPr',
						data: edataPr,
						success: function(data){
							//alert(data);
							if($.trim(data) == 'Success_Accept')
							{
								$("div#mandatory-msg-1.alert").css('display','none');
								$("div#mandatory-msg-2.alert").css('display','none');
								$("div#mandatory-msg-3.alert").css('display','none');
                                $("div#mandatory-msg-4.alert").css('display','none');
                                $("div#mandatory-msg-5.alert").css('display','none');
								$("div#undefined-msg.alert").css('display','none');
								$("div#session-msg.alert").css('display','none');
								$("div#success-msg.alert.alert-success").css('display','block');
								
								$("div#reject-msg.alert.alert-success").css('display','none');
								$("button#btn-accept.btn.btn-primary").attr('disabled', true);
								$("button#btn-reject.btn.btn-danger").attr('disabled', true);
								$("#remarkProc").attr('readonly', true);
								$("select#newPrCharged").attr('disabled', true);
								
								$("a#window-edit.btn.btn-small.btn-success").css('display', 'none');
								for(var i = 0; i < itemStatusArray.length; i++){
                                    $("select#getItemStatus"+i).attr('disabled', true);
                                    $("input#getDeliveryDate"+i+".hasDatepicker").attr('disabled', true);
                                    $("input#getDeliveryDate"+i+".hasDatepicker").css('background-color', '#EEE');
                                }
                                window.location = "WEPO001_.php";
							}
							else if($.trim(data) == 'Mandatory_1')
							{
								$("div#mandatory-msg-1.alert").css('display','block');
								$("div#mandatory-msg-2.alert").css('display','none');
								$("div#mandatory-msg-3.alert").css('display','none');
                                $("div#mandatory-msg-4.alert").css('display','none');
                                $("div#mandatory-msg-5.alert").css('display','none');
								$("div#undefined-msg.alert").css('display','none');
								$("div#session-msg.alert").css('display','none');
							}
							else if($.trim(data) == 'Mandatory_2')
							{
								$("div#mandatory-msg-1.alert").css('display','none');
								$("div#mandatory-msg-2.alert").css('display','block');
								$("div#mandatory-msg-3.alert").css('display','none');
                                $("div#mandatory-msg-4.alert").css('display','none');
                                $("div#mandatory-msg-5.alert").css('display','none');
								$("div#undefined-msg.alert").css('display','none');
								$("div#session-msg.alert").css('display','none');
							}
							else if($.trim(data) == 'Mandatory_3')
							{
								$("div#mandatory-msg-1.alert").css('display','none');
								$("div#mandatory-msg-2.alert").css('display','none');
								$("div#mandatory-msg-3.alert").css('display','block');
                                $("div#mandatory-msg-4.alert").css('display','none');
                                $("div#mandatory-msg-5.alert").css('display','none');
								$("div#undefined-msg.alert").css('display','none');
								$("div#session-msg.alert").css('display','none');
								
								$("#remarkProc").attr('readonly', false);
							}
                            else if($.trim(data) == 'Mandatory_4')
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#mandatory-msg-2.alert").css('display','none');
                                $("div#mandatory-msg-3.alert").css('display','none');
                                $("div#mandatory-msg-4.alert").css('display','block');
                                $("div#mandatory-msg-5.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','none');
                                $("div#session-msg.alert").css('display','none');

                                $("#remarkProc").attr('readonly', false);
                            }
                            else if($.trim(data) == 'Mandatory_5')
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#mandatory-msg-2.alert").css('display','none');
                                $("div#mandatory-msg-3.alert").css('display','none');
                                $("div#mandatory-msg-4.alert").css('display','none');
                                $("div#mandatory-msg-5.alert").css('display','block');
                                $("div#undefined-msg.alert").css('display','none');
                                $("div#session-msg.alert").css('display','none');

                                $("#remarkProc").attr('readonly', false);
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
				},
				"No": function(){
					$(this).dialog("close");
				}
			}
		});
        //$("#dialog-confirm-accept").dialog("open");
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
					var edataPr;
					var prNoVal         = $("#prNo").val();
					var remarkProcVal   = $.trim($("#remarkProc").val());
                    var procPrUpdateDateVal = $("#procPrUpdateDate").val();
                    var rowCount            = ($("#prItemTable > tbody >tr").length) - 1;
					
                    edataPr = "prNoPrm="+prNoVal+"&remarkProcPrm="+encodeURIComponent(remarkProcVal)
                            +"&procPrUpdateDatePrm="+procPrUpdateDateVal;
					
					$.ajax({
						type: 'GET',
						url: '../db/PR_to_PO/EPS_T_TRANSFER.php?action=RejectPr',
						data: edataPr,
						success: function(data){
							//console.log(data);
							if($.trim(data) == 'Success_Reject')
							{
								$("div#mandatory-msg-1.alert").css('display','none');
								$("div#mandatory-msg-2.alert").css('display','none');
								$("div#mandatory-msg-3.alert").css('display','none');
                                $("div#mandatory-msg-4.alert").css('display','none');
                                $("div#mandatory-msg-5.alert").css('display','none');
								$("div#undefined-msg.alert").css('display','none');
								$("div#session-msg.alert").css('display','none');
								$("div#success-msg.alert.alert-success").css('display','none');
								$("div#reject-msg.alert.alert-success").css('display','block');

								$("button#btn-accept.btn.btn-primary").attr('disabled', true);
								$("button#btn-reject.btn.btn-danger").attr('disabled', true);
								$("#remarkProc").attr('readonly', true);
								$("select#newPrCharged").attr('disabled', true);

								$("a#window-edit.btn.btn-small.btn-success").css('display', 'none');
								for(var i = 0; i < rowCount; i++){
                                    $("select#getItemStatus"+i).attr('disabled', true);
                                    $("input#getDeliveryDate"+i+".hasDatepicker").attr('disabled', true);
                                    $("input#getDeliveryDate"+i+".hasDatepicker").css('background-color', '#EEE');
                                }
                                window.location = "WEPO001_.php";
							}
							else if($.trim(data) == 'Mandatory_1')
							{
								$("div#mandatory-msg-1.alert").css('display','none');
								$("div#mandatory-msg-2.alert").css('display','none');
								$("div#mandatory-msg-3.alert").css('display','block');
                                $("div#mandatory-msg-4.alert").css('display','none');
                                $("div#mandatory-msg-5.alert").css('display','none');
								$("div#undefined-msg.alert").css('display','none');
								$("div#session-msg.alert").css('display','none');
								
								$("#remarkProc").attr('readonly', false);
							}
                            else if($.trim(data) == 'Mandatory_5')
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#mandatory-msg-2.alert").css('display','none');
                                $("div#mandatory-msg-3.alert").css('display','none');
                                $("div#mandatory-msg-4.alert").css('display','none');
                                $("div#mandatory-msg-5.alert").css('display','block');
                                $("div#undefined-msg.alert").css('display','none');
                                $("div#session-msg.alert").css('display','none');

                                $("#remarkProc").attr('readonly', false);
                            }
							else if($.trim(data) == 'SessionExpired')
							{
								/*$("div#mandatory-msg-1.alert").css('display','none');
								$("div#mandatory-msg-2.alert").css('display','none');
								$("div#mandatory-msg-3.alert").css('display','none');
								$("div#undefined-msg.alert").css('display','none');
								$("div#session-msg.alert").css('display','block');*/
								$("#dialog-confirm-session").dialog('open');
							}
							else
							{
								$("div#mandatory-msg-1.alert").css('display','none');
								$("div#mandatory-msg-2.alert").css('display','none');
								$("div#mandatory-msg-3.alert").css('display','none');
                                $("div#mandatory-msg-4.alert").css('display','none');
                                $("div#mandatory-msg-5.alert").css('display','none');
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
		$("#dialog-confirm-back").html("Do you want back to PR List?");
		$("#dialog-confirm-back").dialog({
			//autoOpen    : false,
			height      : 155,
			width       : 400,
			//position    : { my: "center", at: "top", of: $("body"), within: $("body") },
			modal       : true,
			buttons     : {
				"Yes": function(){
					$(this).dialog("close");
					window.location = "WEPO001_.php";
				},
				"No": function(){
					$(this).dialog("close");
				}
			}
		});
        //$("#dialog-confirm-back").dialog("open");
    });
    
    $("table .btn-success").click(function() {
        var prNo        = $.trim($(this).closest('tr').find('td:eq(2)').text());
        var itemSts     = $.trim($(this).closest('tr').find('td:eq(3)').text());
        var itemStsAlias= $.trim($(this).closest('tr').find('td:eq(4)').text());
        var itemCd      = $.trim($(this).closest('tr').find('td:eq(5)').text());
        var itemName    = $.trim($(this).closest('tr').find('td:eq(6)').text());
        var refItemName = $.trim($(this).closest('tr').find('td:eq(7)').text());
        var dueDate     = $.trim($(this).closest('tr').find('td:eq(8)').text());
        var itemType    = $.trim($(this).closest('tr').find('td:eq(9)').text());
        var rfiNo       = $.trim($(this).closest('tr').find('td:eq(10)').text());
        var expense     = $.trim($(this).closest('tr').find('td:eq(11)').text());
        var um          = $.trim($(this).closest('tr').find('td:eq(12)').text());
        var qty         = $.trim($(this).closest('tr').find('td:eq(13)').text());
        var price       = $.trim($(this).closest('tr').find('td:eq(14)').text());
        var supplierName= $.trim($(this).closest('tr').find('td:eq(15)').text());
        var supplierCd  = $.trim($(this).closest('tr').find('td:eq(16)').text());
        var remark      = $.trim($(this).closest('tr').find('td:eq(18)').text());
        var seqItem     = $.trim($(this).closest('tr').find('td:eq(19)').text());
        var currencyCd  = $.trim($(this).closest('tr').find('td:eq(20)').text());
        
        $("div#dialog-mandatory-msg-1.alert").css('display','none');
        $("div#dialog-duplicate-msg-2.alert").css('display','none');
        $("div#dialog-duplicate-msg-3.alert").css('display','none');
        $("div#dialog-mandatory-msg-4.alert").css('display','none');
        $("div#dialog-mandatory-msg-5.alert").css('display','none');
        $("div#dialog-mandatory-msg-6.alert").css('display','none');
        $("div#dialog-mandatory-msg-7.alert").css('display','none');
        $("div#dialog-duplicate-msg.alert").css('display','none');
        $("div#dialog-undefined-msg.alert").css('display','none');
        
        $("#supplierCd").attr('disabled', false);
        $("#supplierName").attr('disabled', false);
        $("#um").attr('disabled', false);
        $("#price").attr('readonly', false);
        
        $("#prNo").val('');
        $("#refItemName").val('');
        $("#itemStatus").val('');
        $("#itemType").val('');
        $("#expNo").val('');
        $("#invNo").val('');
        $("#rfiNo").val('');
        $("#itemCd").val('');
        $("#itemName").val('');
        $("#deliveryDate").val('');
        $("#qty").val('');
        $("#um").val('');
        $("#price").val('');
        $("#supplierName").val('');
        $("#supplierCd").val('');
        $("#remark").val('');
        $("#seqItem").val('');
        $("#currencyCd").val('');
        
        $("#dialog-form").dialog("open");
        
        $("#prNoItem").val(prNo);
        $("#refItemName").val(refItemName);
        $("#itemStatus").val(itemSts);
        $("#itemType").val(itemType);
        $("#expNo").val(expense);
        $("#invNo").val(expense);
        $("#rfiNo").val(rfiNo);
        $("#itemCd").val(itemCd);
        $("#itemName").val(itemName);
        $("#deliveryDate").val(dueDate);
        $("#qty").val(qty);
        $("#um").val(um);
        $("#price").val(price);
        $("#supplierName").val(supplierCd);
        $("#supplierCd").val(supplierCd);
        $("#remark").val(remark);
        $("#seqItem").val(seqItem);
        $("#currencyCd").val(currencyCd);
        
        var str = "";
        $( "#itemType option:selected" ).each(function() {
            str += $( this ).text() + " ";
        });
        if($.trim(str) == 'EXP'){
            $("td#td-exp-no").css('display','block');
            $("td#td-inv-no").css('display','none');
            $("td#td-rfi-no").css('display','none');
              
            $("#invNo").val('');
            $("#rfiNo").val('');
        }else if($.trim(str) == 'INV'){
            $("td#td-exp-no").css('display','none');
            $("td#td-inv-no").css('display','block');
            $("td#td-rfi-no").css('display','none');
            
            $("#expNo").val(''); 
            $("#rfiNo").val('');
        }else{
            if($.trim(str) == 'RFI'){
                $("td#td-exp-no").css('display','none');
                $("td#td-inv-no").css('display','none');
                $("td#td-rfi-no").css('display','block');
                
                $("#expNo").val(''); 
                $("#invNo").val('');
                
                $("#rfiNo").removeAttr('readonly');
            }
        }
         
        if($.trim($("#itemCd").val()) != '99'){
            
            $("#supplierCd").attr('disabled', true);
            $("#supplierName").attr('disabled', true);
            $("#um").attr('disabled', true);
            $("#price").attr('readonly', true);
        }
    });
    
    $("table .btn-info").click(function() {
        var prNo        = $.trim($(this).closest('tr').find('td:eq(2)').text());
        var refItemName = $.trim($(this).closest('tr').find('td:eq(7)').text());
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

