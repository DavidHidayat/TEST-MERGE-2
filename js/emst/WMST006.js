$(document).ready(function() {
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
    $("#currencyCd-dialog").change(function(){
        var str = "";
        $( "#currencyCd-dialog option:selected" ).each(function() {
            str += $( this ).text() + " ";
        });
        if($.trim(str) == "IDR")
        {
            $("#vat-dialog").val('');
            $("#vat-dialog").attr('disabled', false);
        } 
        else if($.trim(str) == "")
        {
            $("#vat-dialog").val('');
            $("#vat-dialog").attr('disabled', true);
        }
        else
        {
            $("#vat-dialog").val("NON VAT");
            $("#vat-dialog").attr('disabled', true);
        }
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
	width       : 680,
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
                var supplierCdVal       = $.trim($("#supplierCd-dialog").val());
                var supplierNameVal     = $.trim($("#supplierName-dialog").val());
                var supplierNumberVal   = $.trim($("#supplierNumber-dialog").val());
                var currencyCdVal       = $.trim($("#currencyCd-dialog").val());
                var vatVal              = $.trim($("#vat-dialog").val());
                var npwpVal             = $.trim($("#npwp-dialog").val());
                var contactVal          = $.trim($("#contact-dialog").val());
                var phoneVal            = $.trim($("#phone-dialog").val());
                var outstandingFlagVal  = $.trim($("#outstandingFlag-dialog").val());
                var activeFlagVal       = $.trim($("#activeFlag-dialog").val());
                var addressVal          = $.trim($("#address-dialog").val());
                var emailVal            = $.trim($("#email-dialog").val());
                var emailCcVal          = $.trim($("#email-cc-dialog").val());
                var emailCcUpVal        = $.trim($("#email-cc-up-dialog").val());
                var action              = $.trim($("#dialog-form").dialog("option","title").substr(0,4));
                
                edata = "action="+encodeURIComponent(action)+"&supplierCdPrm="
                        +encodeURIComponent(supplierCdVal)+"&supplierNamePrm="+encodeURIComponent(supplierNameVal)
                        +"&supplierNumberPrm="+encodeURIComponent(supplierNumberVal)
                        +"&currencyCdPrm="+encodeURIComponent(currencyCdVal)+"&vatPrm="+encodeURIComponent(vatVal)
                        +"&npwpPrm="+encodeURIComponent(npwpVal)+"&contactPrm="+encodeURIComponent(contactVal)
                        +"&phonePrm="+encodeURIComponent(phoneVal)+"&outstandingFlagPrm="+encodeURIComponent(outstandingFlagVal)
                        +"&activeFlagPrm="+encodeURIComponent(activeFlagVal)+"&addressPrm="+encodeURIComponent(addressVal)
                        +"&emailPrm="+encodeURIComponent(emailVal)+"&emailCcPrm="+encodeURIComponent(emailCcVal)
                        +"&emailCcUpPrm="+encodeURIComponent(emailCcUpVal);
                
                $.ajax({
                    type: 'GET',
                    url: '../db/MASTER/EPS_M_SUPPLIER.php',
                    data: edata,
                    success: function(data){
                        $("div#dialog-mandatory-msg-1.alert").css('display','none');
                        $("div#dialog-duplicate-msg.alert").css('display','none');
                        $("div#dialog-notexist-msg.alert").css('display','none');
                        $("div#dialog-notallowedit-msg.alert").css('display','none');
                        $("div#dialog-undefined-msg.alert").css('display','none');
                        if($.trim(data) == 'Success')
                        {
                            $("#dialog-form").dialog("close");
                            window.location.reload();
                            $("#supplierCd").val(supplierCdVal);
                            $("#WMST006Form").attr('action', 'WMST006.php');
                            $("#WMST006Form").submit();
                        }
                        else if($.trim(data) == 'Mandatory_1')
                        {
                            $("div#dialog-mandatory-msg-1.alert").css('display','block');
                            $("div#dialog-duplicate-msg.alert").css('display','none');
                            $("div#dialog-notexist-msg.alert").css('display','none');
                            $("div#dialog-notallowedit-msg.alert").css('display','none');
                            $("div#dialog-undefined-msg.alert").css('display','none');
                        }
                        else if($.trim(data) == 'Duplicate')
                        {
                            $("div#dialog-mandatory-msg-1.alert").css('display','none');
                            $("div#dialog-duplicate-msg.alert").css('display','block');
                            $("div#dialog-notexist-msg.alert").css('display','none');
                            $("div#dialog-notallowedit-msg.alert").css('display','none');
                            $("div#dialog-undefined-msg.alert").css('display','none');
                        }
                        else if($.trim(data) == 'NotExist')
                        {
                            $("div#dialog-mandatory-msg-1.alert").css('display','none');
                            $("div#dialog-duplicate-msg.alert").css('display','block');
                            $("div#dialog-notexist-msg.alert").css('display','none');
                            $("div#dialog-notallowedit-msg.alert").css('display','none');
                            $("div#dialog-undefined-msg.alert").css('display','none');
                        }
                        else if($.trim(data) == 'NotAllowEdit')
                        {
                            $("div#dialog-mandatory-msg-1.alert").css('display','none');
                            $("div#dialog-duplicate-msg.alert").css('display','none');
                            $("div#dialog-notexist-msg.alert").css('display','none');
                            $("div#dialog-notallowedit-msg.alert").css('display','block');
                            $("div#dialog-undefined-msg.alert").css('display','none');
                        }
                        else if($.trim(data) == 'SessionExpired')
                        {
                            $("#dialog-confirm-session").dialog('open');
                        }
                        else
                        {
                            $("div#dialog-mandatory-msg-1.alert").css('display','none');
                            $("div#dialog-duplicate-msg.alert").css('display','none');
                            $("div#dialog-notexist-msg.alert").css('display','none');
                            $("div#dialog-notallowedit-msg.alert").css('display','none');
                            $("div#dialog-undefined-msg.alert").css('display','block');
                        }  
                    }
                });
            },
            "Cancel": function(){
                $(this).dialog("close");
            }
        }
    });
    /**
     * =========================================================================================================
     * BUTTON FUNCTION
     * =========================================================================================================
     **/
    $("#btn-search").click(function() {
        var supplierCdVal  = $.trim($("#supplierCd").val());
        var supplierNameVal= $.trim($("#supplierName").val());
        var currencyCdVal  = $.trim($("#currencyCd").val());
        var vatCdVal       = $.trim($("#vat").val());
        var outVal          = $.trim($("#out").val());
        
        if(supplierCdVal != '' || supplierNameVal != '' || currencyCdVal != '' || vatCdVal != '' || outVal != '')
        {
            $("div#mandatory-msg-1.alert").css('display','none');
            $("div#mandatory-msg-2.alert").css('display','none');
            $("#WMST006Form").attr('action', 'WMST006.php');
            $("#WMST006Form").submit();
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
        $("#currencyCd").val('');
        $("#vat").val('');
        $("#out").val('');
        $("div#mandatory-msg-1.alert").css('display','none');
        $("div#mandatory-msg-2.alert").css('display','none');
        $("#WMST006Form").attr('action', 'WMST006.php');
        $("#WMST006Form").submit();
    });
    
    /**
     * =========================================================================================================
     * LINK FUNCTION
     * =========================================================================================================
     **/
    $("a#link-register.news-item-title").click(function() {
        $("#supplierCd-dialog").val('');
        $("#supplierName-dialog").val('');
        $("#supplierNumber-dialog").val('');
        $("#currencyCd-dialog").val('');
        $("#vat-dialog").val('');
        $("#npwp-dialog").val('');
        $("#contact-dialog").val('');
        $("#phone-dialog").val('');
        $("#address-dialog").val('');
        $("#email-dialog").val('');
        $("#supplierCd-dialog").attr('readonly', false);
        $("#supplierName-dialog").attr('readonly', false);
        $("#supplierNumber-dialog").attr('readonly', false);
        $("#currencyCd-dialog").attr('disabled', false);
        
        $("div#dialog-mandatory-msg-1.alert").css('display','none');
        $("div#dialog-duplicate-msg.alert").css('display','none');
        $("div#dialog-undefined-msg.alert").css('display','none');
                        
        $('#dialog-form').dialog('option', 'title', 'Add Supplier');
        
        $("#dialog-form").dialog("open");
    });
    
    $("table.table.table-striped.table-bordered tbody tr td a").click(function() {
        var supplierCd      = $.trim($(this).closest('tr').find('td:eq(1)').text());
        var supplierName    = $.trim($(this).closest('tr').find('td:eq(2)').text());
        var currencyCd      = $.trim($(this).closest('tr').find('td:eq(3)').text());
        var vat             = $.trim($(this).closest('tr').find('td:eq(4)').text());
        var npwp            = $.trim($(this).closest('tr').find('td:eq(5)').text());
        var contact         = $.trim($(this).closest('tr').find('td:eq(6)').text());
        var phone           = $.trim($(this).closest('tr').find('td:eq(7)').text());
        var address         = $.trim($(this).closest('tr').find('td:eq(8)').text());
        var email           = $.trim($(this).closest('tr').find('td:eq(9)').text());
        var outstandingFlag = $.trim($(this).closest('tr').find('td:eq(10)').text());
        var activeFlag      = $.trim($(this).closest('tr').find('td:eq(11)').text());
        var supplierNumber  = $.trim($(this).closest('tr').find('td:eq(12)').text());
        var emailCc         = $.trim($(this).closest('tr').find('td:eq(13)').text());
        var emailCcUp       = $.trim($(this).closest('tr').find('td:eq(14)').text());
        
        $("#supplierCd-dialog").val(supplierCd);
        $("#supplierName-dialog").val(supplierName);
        $("#supplierNumber-dialog").val(supplierNumber);
        $("#currencyCd-dialog").val(currencyCd);
        $("#vat-dialog").val(vat);
        $("#npwp-dialog").val(npwp);
        $("#contact-dialog").val(contact);
        $("#phone-dialog").val(phone);
        $("#activeFlag-dialog").val(activeFlag);
        $("#outstandingFlag-dialog").val(outstandingFlag);
        $("#address-dialog").val(address);
        $("#email-dialog").val(email);
        $("#email-cc-dialog").val(emailCc);
        $("#email-cc-up-dialog").val(emailCcUp);
        $("#supplierCd-dialog").attr('readonly', true);
        $("#supplierName-dialog").attr('readonly', false);
        $("#supplierNumber-dialog").attr('readonly', false);
        $("#currencyCd-dialog").attr('disabled', true);
        
        $("div#dialog-mandatory-msg-1.alert").css('display','none');
        $("div#dialog-duplicate-msg.alert").css('display','none');
        $("div#dialog-undefined-msg.alert").css('display','none');
        
        $('#dialog-form').dialog('option', 'title', 'Edit Supplier');
        
        $("#dialog-form").dialog("open");
    });
});
