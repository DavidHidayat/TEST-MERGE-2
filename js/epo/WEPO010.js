$(document).ready(function() {
    /**
     * =========================================================================================================
     * INITIAL VALUE
     * =========================================================================================================
     **/
    if($("#itemCd").val() != '99'){
        $("#um").attr('disabled', true);
    } 
    
    var str2 = "";
    $( "#itemType option:selected" ).each(function() {
        str2 += $( this ).text() + " ";
    });
    if($.trim(str2) == 'EXP'){
        $("td#td-exp-no").css('display','block');
        $("td#td-inv-no").css('display','none');
        $("td#td-invs-no").css('display','none');
        $("td#td-rfi-no").css('display','none');
              
        $("#invNo").val('');
        $("#invsNo").val('');
        $("#rfiNo").val('');
    }else if($.trim(str2) == 'INV' || $.trim(str2) == 'INV-MCH'){
        $("td#td-exp-no").css('display','none');
        $("td#td-inv-no").css('display','block');
        $("td#td-invs-no").css('display','none');
        $("td#td-rfi-no").css('display','none');
            
        $("#expNo").val(''); 
        $("#invsNo").val('');
        $("#rfiNo").val('');
    }else if($.trim(str2) == 'INV-S'){
        $("td#td-exp-no").css('display','none');
        $("td#td-inv-no").css('display','none');
        $("td#td-invs-no").css('display','block');
        $("td#td-rfi-no").css('display','none');
            
        $("#expNo").val(''); 
        $("#invNo").val('');
        $("#rfiNo").val('');
    }else{
        if($.trim(str2) == 'RFI'){
            $("td#td-exp-no").css('display','none');
            $("td#td-inv-no").css('display','none');
            $("td#td-invs-no").css('display','none');
            $("td#td-rfi-no").css('display','block');
                
            $("#expNo").val(''); 
            $("#invNo").val('');
            $("#invsNo").val('');   
            $("#rfiNo").removeAttr('readonly');
        }
    }
    
    /**
     * =========================================================================================================
     * INPUT FUNCTION
     * =========================================================================================================
     **/
    $("#qty").keyup(function(e){
        $("#actualQty").val($("#qty").val());
        var remainQty = $("#qty").val() - $("#actualQty").val();
        $("#remainQty").val(remainQty);
    });
    /**$("#qty").keypress(function(e) {
        //if the letter is not digit then display error and don't type anything
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            //display error message
            //$("#errmsg").html("Digits Only").show().fadeOut("slow");
            return false;
        }
    });**/
	$("#qty").keypress(function(event) {
        return isNumber(event, this);
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
    
    $("#itemType").change(function(){
        var str = "";
        $( "#itemType option:selected" ).each(function() {
            str += $( this ).text() + " ";
        });
        if($.trim(str) == 'EXP'){
            $("td#td-exp-no").css('display','block');
            $("td#td-inv-no").css('display','none');
            $("td#td-invs-no").css('display','none');
            $("td#td-rfi-no").css('display','none');

            $("#invNo").val('');
            $("#invsNo").val('');
            $("#rfiNo").val('');
        }else if($.trim(str) == 'INV' || $.trim(str) == 'INV-MCH'){
            $("td#td-exp-no").css('display','none');
            $("td#td-inv-no").css('display','block');
            $("td#td-invs-no").css('display','none');
            $("td#td-rfi-no").css('display','none');

            $("#expNo").val(''); 
            $("#invsNo").val('');
            $("#rfiNo").val('');
        }else if($.trim(str) == 'INV-S'){
            $("td#td-exp-no").css('display','none');
            $("td#td-inv-no").css('display','none');
            $("td#td-invs-no").css('display','block');
            $("td#td-rfi-no").css('display','none');

            $("#expNo").val('');
            $("#invNo").val('');
            $("#rfiNo").val('');
        }else{
            if($.trim(str) == 'RFI'){
                $("td#td-exp-no").css('display','none');
                $("td#td-inv-no").css('display','none');
                $("td#td-invs-no").css('display','none');
                $("td#td-rfi-no").css('display','block');

                $("#expNo").val(''); 
                $("#invNo").val('');
                $("#invsNo").val('');
                $("#rfiNo").removeAttr('readonly');
            }
        }
    });
    
    $("#itemStatus").change(function(){
        var str = "";
        $( "#itemStatus option:selected" ).each(function() {
            str += $( this ).val() + " ";
        });
        if($.trim(str) == '1150'){
            $("#remarkProc").attr('readonly', false);
        }else{
            $("#remarkProc").attr('readonly', true);
        }
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
                    var transferIdVal   = $("#transferIdHidden").val();
                    var prNoVal         = $("#prNoHidden").val();
                    var itemTypeVal     = $("#itemType").val();
                    var accountNoVal    = $.trim($("#expNo").val());
                    var invNo           = $.trim($("#invNo").val());
                    var invsNo          = $.trim($("#invsNo").val());
                    var rfiNoVal        = $.trim($("#rfiNo").val());
                    var umVal           = $("#um").val(); 
                    var qtyVal          = $("#qty").val().replace(/,/g, '');
                    var actualQtyVal    = $("#actualQty").val().replace(/,/g, '');
                    var remainQtyVal    = $("#remainQty").val().replace(/,/g, '');
                    var deliveryDateVal = $("#deliveryDate").val();
                    var itemStatusVal   = $("#itemStatus").val();
                    var priceVal        = $.trim($("#price").val().replace(/,/g, ''));
                    var remarkProcVal   = $.trim($("#remarkProc").val());

                    edataItem = "prNoValPrm="+prNoVal+"&transferIdValPrm="+transferIdVal
                                +"&itemTypeValPrm="+itemTypeVal+"&rfiNoValPrm="+rfiNoVal
                                +"&accountNoValPrm="+accountNoVal+"&invNoValPrm="+invNo+"&invsNoValPrm="+invsNo+"&umValPrm="+umVal
                                +"&qtyValPrm="+qtyVal+"&actualQtyValPrm="+actualQtyVal+"&remainQtyValPrm="+remainQtyVal
                                +"&priceValPrm="+priceVal+"&deliveryDateValPrm="+deliveryDateVal
                                +"&itemStatusValPrm="+itemStatusVal+"&remarkProcValPrm="+encodeURIComponent(remarkProcVal);
                    $.ajax({
                        type: 'GET',
                        url: '../db/PR_to_PO/EPS_T_TRANSFER.php?action=UpdateWaitingPoNumber',
                        data: edataItem,
                        success: function(data){
                            //alert(data);
                            $("div#mandatory-msg-1.alert").css('display','none');
                            $("div#mandatory-msg-2.alert").css('display','none');
                            $("div#mandatory-msg-3.alert").css('display','none');
                            $("div#mandatory-msg-4.alert").css('display','none');
                            $("div#mandatory-msg-5.alert").css('display','none');
                            $("div#mandatory-msg-6.alert").css('display','none');
                            $("div#mandatory-msg-7.alert").css('display','none');
                            $("div#session-msg.alert").css('display','none');
                            $("div#undefined-msg.alert").css('display','none');

                            if($.trim(data) == 'Success')
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#mandatory-msg-2.alert").css('display','none');
                                $("div#mandatory-msg-3.alert").css('display','none');
                                $("div#mandatory-msg-4.alert").css('display','none');
                                $("div#mandatory-msg-5.alert").css('display','none');
                                $("div#mandatory-msg-6.alert").css('display','none');
                                $("div#mandatory-msg-7.alert").css('display','none');
                                $("div#session-msg.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','none');
                                $("div#success-msg.alert.alert-success").css('display','block');

                                $("button#btn-save-item.btn.btn-primary").attr('disabled', true);
                                $("select#itemType").attr('disabled', true);
                                $("select#expNo").attr('disabled', true);
                                $("#rfiNo").attr('readonly', true);
                                $("select#um").attr('disabled', true);
                                $("#qty").attr('readonly', true);
                                $("#deliveryDate").attr('readonly', true);
                                $("select#itemStatus").attr('disabled', true);
                                $("#remarkProc").attr('readonly', true);
                                $("#deliveryDate").attr('disabled', true);
                                $("input#deliveryDate.span2.hasDatepicker").css('background-color', '#EEE');
                                window.location = "WEPO003.php";
                            }
                            else if($.trim(data) == 'Mandatory_1')
                            {
                                $("div#mandatory-msg-1.alert").css('display','block');
                                $("div#mandatory-msg-2.alert").css('display','none');
                                $("div#mandatory-msg-3.alert").css('display','none');
                                $("div#mandatory-msg-4.alert").css('display','none');
                                $("div#mandatory-msg-5.alert").css('display','none');
                                $("div#mandatory-msg-6.alert").css('display','none');
                                $("div#mandatory-msg-7.alert").css('display','none');
                                $("div#mandatory-msg-8.alert").css('display','none');
                                $("div#mandatory-msg-9.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','none');
                            }
                            else if($.trim(data) == 'Mandatory_2')
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#mandatory-msg-2.alert").css('display','block');
                                $("div#mandatory-msg-3.alert").css('display','none');
                                $("div#mandatory-msg-4.alert").css('display','none');
                                $("div#mandatory-msg-5.alert").css('display','none');
                                $("div#mandatory-msg-6.alert").css('display','none');
                                $("div#mandatory-msg-7.alert").css('display','none');
                                $("div#mandatory-msg-8.alert").css('display','none');
                                $("div#mandatory-msg-9.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','none');
                            }
                            else if($.trim(data) == 'Mandatory_3')
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#mandatory-msg-2.alert").css('display','none');
                                $("div#mandatory-msg-3.alert").css('display','block');
                                $("div#mandatory-msg-4.alert").css('display','none');
                                $("div#mandatory-msg-5.alert").css('display','none');
                                $("div#mandatory-msg-6.alert").css('display','none');
                                $("div#mandatory-msg-7.alert").css('display','none');
                                $("div#mandatory-msg-8.alert").css('display','none');
                                $("div#mandatory-msg-9.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','none');
                            }
                            else if($.trim(data) == 'Mandatory_4')
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#mandatory-msg-2.alert").css('display','none');
                                $("div#mandatory-msg-3.alert").css('display','none');
                                $("div#mandatory-msg-4.alert").css('display','block');
                                $("div#mandatory-msg-5.alert").css('display','none');
                                $("div#mandatory-msg-6.alert").css('display','none');
                                $("div#mandatory-msg-7.alert").css('display','none');
                                $("div#mandatory-msg-8.alert").css('display','none');
                                $("div#mandatory-msg-9.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','none');
                            }
                            else if($.trim(data) == 'Mandatory_5')
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#mandatory-msg-2.alert").css('display','none');
                                $("div#mandatory-msg-3.alert").css('display','none');
                                $("div#mandatory-msg-4.alert").css('display','none');
                                $("div#mandatory-msg-5.alert").css('display','block');
                                $("div#mandatory-msg-6.alert").css('display','none');
                                $("div#mandatory-msg-7.alert").css('display','none');
                                $("div#mandatory-msg-8.alert").css('display','none');
                                $("div#mandatory-msg-9.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','none');
                            }
                            else if($.trim(data) == 'Mandatory_6')
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#mandatory-msg-2.alert").css('display','none');
                                $("div#mandatory-msg-3.alert").css('display','none');
                                $("div#mandatory-msg-4.alert").css('display','none');
                                $("div#mandatory-msg-5.alert").css('display','none');
                                $("div#mandatory-msg-6.alert").css('display','block');
                                $("div#mandatory-msg-7.alert").css('display','none');
                                $("div#mandatory-msg-8.alert").css('display','none');
                                $("div#mandatory-msg-9.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','none');
                            }
                            else if($.trim(data) == 'Mandatory_7')
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#mandatory-msg-2.alert").css('display','none');
                                $("div#mandatory-msg-3.alert").css('display','none');
                                $("div#mandatory-msg-4.alert").css('display','none');
                                $("div#mandatory-msg-5.alert").css('display','none');
                                $("div#mandatory-msg-6.alert").css('display','none');
                                $("div#mandatory-msg-7.alert").css('display','block');
                                $("div#mandatory-msg-8.alert").css('display','none');
                                $("div#mandatory-msg-9.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','none');
                            }
                            else if($.trim(data) == 'Mandatory_8')
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#mandatory-msg-2.alert").css('display','none');
                                $("div#mandatory-msg-3.alert").css('display','none');
                                $("div#mandatory-msg-4.alert").css('display','none');
                                $("div#mandatory-msg-5.alert").css('display','none');
                                $("div#mandatory-msg-6.alert").css('display','none');
                                $("div#mandatory-msg-7.alert").css('display','none');
                                $("div#mandatory-msg-8.alert").css('display','block');
                                $("div#mandatory-msg-9.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','none');
                            }
                            else if($.trim(data) == 'Mandatory_9')
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#mandatory-msg-2.alert").css('display','none');
                                $("div#mandatory-msg-3.alert").css('display','none');
                                $("div#mandatory-msg-4.alert").css('display','none');
                                $("div#mandatory-msg-5.alert").css('display','none');
                                $("div#mandatory-msg-6.alert").css('display','none');
                                $("div#mandatory-msg-7.alert").css('display','none');
                                $("div#mandatory-msg-8.alert").css('display','none');
                                $("div#mandatory-msg-9.alert").css('display','block');
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
                                $("div#mandatory-msg-4.alert").css('display','none');
                                $("div#mandatory-msg-5.alert").css('display','none');
                                $("div#mandatory-msg-6.alert").css('display','none');
                                $("div#mandatory-msg-7.alert").css('display','none');
                                $("div#mandatory-msg-9.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','block');
                            }
                        } 
                    });
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
        $("#dialog-confirm-back").html("Do you want back to menu Generate PO?");
        $("#dialog-confirm-back").dialog({
            //autoOpen    : false,
            height      : 155,
            width       : 400,
            //position    : {my: "center", at: "top", of: $("body"), within: $("body")},
            modal       : true,
            buttons     : {
                "Yes": function(){
                    $(this).dialog("close");
                    window.location = "WEPO003.php";
                },
                "No": function(){
                    $(this).dialog("close");
                }
            }
        });
        //$("#dialog-confirm-back").dialog("open");
    });
});


