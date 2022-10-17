$(document).ready(function() { 
    /**
     * =========================================================================================================
     * INITIAL VALUE
     * =========================================================================================================
     **/
    $("input[type = 'text'],textarea").keydown(function(){
        $("div#mandatory-msg-1.alert.alert-danger").css('display','none');
        $("div#mandatory-msg-2.alert.alert-danger").css('display','none');
        $("div#mandatory-msg-3.alert.alert-danger").css('display','none');
        $("div#mandatory-msg-4.alert.alert-danger").css('display','none');
        $("div#mandatory-msg-5.alert.alert-danger").css('display','none');
        $("div#undefined-msg.alert.alert-danger").css('display','none');
        
        $("div#dialog-mandatory-msg-1.alert.alert-danger").css("display","none");
        $("div#dialog-mandatory-msg-2.alert.alert-danger").css("display","none");
        $("div#dialog-mandatory-msg-3.alert.alert-danger").css("display","none");
        $("div#dialog-mandatory-msg-4.alert.alert-danger").css("display","none");
        $("div#dialog-mandatory-msg-5.alert.alert-danger").css("display","none");
        $("div#dialog-mandatory-msg-6.alert.alert-danger").css("display","none");
        $("div#dialog-mandatory-msg-7.alert.alert-danger").css("display","none");
        $("div#dialog-mandatory-msg-8.alert.alert-danger").css("display","none");
        $("div#dialog-duplicate-msg.alert.alert-danger").css('display','none');
        $("div#dialog-undefined-msg.alert.alert-danger").css('display','none');
        
        $('#status').removeClass('alert alert-danger');
        $('#status').text('');
    });
    
    $("select").change(function(){
        $("div#mandatory-msg-1.alert.alert-danger").css('display','none');
        $("div#mandatory-msg-2.alert.alert-danger").css('display','none');
        $("div#mandatory-msg-3.alert.alert-danger").css('display','none');
        $("div#mandatory-msg-4.alert.alert-danger").css('display','none');
        $("div#mandatory-msg-5.alert.alert-danger").css('display','none');
        $("div#undefined-msg.alert.alert-danger").css('display','none');
        
        $("div#dialog-mandatory-msg-1.alert.alert-danger").css("display","none");
        $("div#dialog-mandatory-msg-2.alert.alert-danger").css("display","none");
        $("div#dialog-mandatory-msg-3.alert.alert-danger").css("display","none");
        $("div#dialog-mandatory-msg-4.alert.alert-danger").css("display","none");
        $("div#dialog-mandatory-msg-5.alert.alert-danger").css("display","none");
        $("div#dialog-mandatory-msg-6.alert.alert-danger").css("display","none");
        $("div#dialog-mandatory-msg-7.alert.alert-danger").css("display","none");
        $("div#dialog-mandatory-msg-8.alert.alert-danger").css("display","none");
        $("div#dialog-duplicate-msg.alert.alert-danger").css('display','none');
        $("div#dialog-undefined-msg.alert.alert-danger").css('display','none');
        
        $('#status').removeClass('alert alert-danger');
        $('#status').text('');
    });
    
    /** Attachment Form */ 
    var rowCountItem  = $("#prItemTable > tbody >tr").length;
    if(rowCountItem > 0)
    {
        $('select#itemNameFile').children('option:not(:first)').remove();
        for(var i = 0; i< rowCountItem; i++)
        {
            var itemCdVal = $("#prItemTable > tbody > tr:eq("+i+") td:eq(2)").text();
            var itemNameVal = $("#prItemTable > tbody > tr:eq("+i+") td:eq(3)").text();
            $('#itemNameFile').append($('<option>',
                {
                    value: itemCdVal,
                    text : itemNameVal
            }));
        }
        $("#itemNameFile").attr('disabled', false);
        $("input[type = 'file']").attr('disabled', true);
    }
    else
    {
        $("#itemNameFile").attr('disabled', true);
        $("input[type = 'file']").attr('disabled', true);
    }
   
    /** Max PR Amount Form */ 
    if(rowCountItem > 0)
    {
        var maxAmount = calculateMaxAmount();
        $("#prItemTotalHidden").val(maxAmount);
    }
    else
    {
        $("#prItemTotalHidden").val(0);
    }
    
    /** Approver Form */ 
    if($("#userTypeHidden").val() == "UT_01")
    {
        $("select[name=approverDept]").attr('disabled', true);
    }
    else
    {
        $("select[name=approverDept]").attr('disabled', false);
    }
    
	/** IT Approver Form */ 
    var specialTypeId = $("#specialTypeId").val();
    if(specialTypeId == "IT")
    {
        $("select#approverIT.form-control").attr('disabled', false);
    }
    else
    {
        $("select#approverIT.form-control").attr('disabled', true);
    }
    /**
     * =========================================================================================================
     * INPUT FUNCTION
     * =========================================================================================================
     **/
    var companyCd   = $("#companyCdHidden").val();
    var buCdLogin   = $("#buLoginHidden").val();
    var chargedBu   = $("#chargedBu").val();
    var invType     = $("#invTypeHidden").val();
    
    $('textarea').keypress(function(event) {
        if (event.keyCode == 13) {
            event.preventDefault();
        }
    });
    
    $("#niceNet").change(function(event) {
        var niceNetFmt = /^\(?([0-9]{3})\)?$/;
        if(!$.trim($("#niceNet").val()).match(niceNetFmt))
        {
            $("#niceNet").val('');
            alert("Digit Error. Incorrect ext format (xxx)");
        }
    });
    
    $("#specialTypeId").change(function(){
        $("div#mandatory-msg-1.alert.alert-danger").css('display','none');
        $("div#mandatory-msg-2.alert.alert-danger").css('display','none');
        $("div#mandatory-msg-3.alert.alert-danger").css('display','none');
        $("div#mandatory-msg-4.alert.alert-danger").css('display','none');
        $("div#mandatory-msg-5.alert.alert-danger").css('display','none');
        $("div#undefined-msg.alert.alert-danger").css('display','none');
        
        if($.trim($("#specialTypeId").val()) == "IT")
        {
            $("select#approverIT.form-control").attr('disabled', false);
            $("select#approverIT.form-control").val(''); 
        }
        else
        {
            $("select#approverIT.form-control").attr('disabled', true);
            $("select#approverIT.form-control").val(''); 
        }
    });

    var previousValue;
    $("#chargedBu").on("focus click",function () {
        previousValue = this.value; // Old vaue 
    }).change(function() {
        var newValue =  this.value; // New Value
        
        var prevCompany = previousValue.substr(0,1);
        var newCompany = newValue.substr(0,1);
        
        var countPrItem             = $("#prItemTable > tbody >tr").length;
        
        if(prevCompany != "" && countPrItem > 0 )
        {
            
            if(prevCompany == "H" || newCompany == "H")
            {
                // Check charged BU and RFI 
                var count = countCheckInvestment();
                if(prevCompany != newCompany && count > 0)
                {
                    alert("Existence Error! Different company for this BU Code. Please remove PR Detail first.");
                    $("#chargedBu").val(previousValue);
                }
            }
            
            checkInvestmentByChargedBu(newValue, previousValue);
            
        }
    });
        
    $("#itemNameFile").change(function(){
        var itemNameFileVal = $.trim($("#itemNameFile").val());
        
        if(itemNameFileVal !=  "")
        {
            $("#itemCdFile").val(itemNameFileVal);
            $("#fileUpload").attr('disabled', false);
        }
        else
        {
            $("#itemCdFile").val('');
            $("#fileUpload").attr('disabled', true);
        }
    });
    
    $("input[name=remarkBypass]").keyup(function(text){
         $(this).val($(this).val().replace(/,/g,''));
    });
    
    $("table#prAppTable select").change(function(){
        $("div#mandatory-msg-1.alert.alert-danger").css('display','none');
        $("div#mandatory-msg-2.alert.alert-danger").css('display','none');
        $("div#mandatory-msg-3.alert.alert-danger").css('display','none');
        $("div#mandatory-msg-4.alert.alert-danger").css('display','none');
        $("div#mandatory-msg-5.alert.alert-danger").css('display','none');
        $("div#undefined-msg.alert.alert-danger").css('display','none');
        
        var currentCountAppDept = $("#countAppDept").val();
        
        for(var i = 0; i < currentCountAppDept; i++)
        {
            if($("select#approverDept"+(i+1)+".form-control").val() != "" && (i+1) < currentCountAppDept){
                $("input#setBypassNoDept"+(i+1)).prop('disabled', false);
            }
            /** Check if approver dept blank value */
            else
            {
                $("input#setRemarkBypassDept"+(i+1)+".form-control").attr('readonly', true);
                $("input#setRemarkBypassDept"+(i+1)+".form-control").val('');
                $("input#setBypassNoDept"+(i+1)).prop('checked', false);
                $("input#setBypassNoDept"+(i+1)).prop('disabled', true);
            }
        }  
    });
    
    $("input[type='checkbox'][name='checkedBypass[]']").click(function () {
        var checkedVal      = $(this).prop('checked');
        var index           = $.trim($(this).attr("id").substr(15,1));
        var approverVal     = $("select#approverDept"+index+".form-control").val();
        
        if(checkedVal == true && approverVal == '')
        {
            $("div#mandatory-msg-3.alert.alert-danger").css('display','block');
            $("input#setBypassNoDept"+index).prop('checked', false);
            $("input#setRemarkBypassDept"+index+".form-control").attr('readonly', true);
            $("input#setRemarkBypassDept"+index+".form-control").val('');
            $("select#approverDept"+index+".form-control").attr('disabled', false);
        }
        else if(checkedVal == true && approverVal != '')
        {
            $("div#mandatory-msg-3.alert.alert-danger").css('display','none');
            $("input#setRemarkBypassDept"+index+".form-control").attr('readonly', false);
            $("select#approverDept"+index+".form-control").attr('disabled', true);
        }
        else
        {
            $("div#mandatory-msg-3.alert.alert-danger").css('display','none');
            $("input#setBypassNoDept"+index).prop('checked', false);
            $("input#setRemarkBypassDept"+index+".form-control").attr('readonly', true);
            $("input#setRemarkBypassDept"+index+".form-control").val('');
            $("select#approverDept"+index+".form-control").attr('disabled', false);
        }
    });
    
    $("input[name=remarkBypass]").keyup(function(text){
         $(this).val($(this).val().replace(/,/g,' '));
    });
	
    $("#itemType").change(function(event) {
        
        $("#expNo").val('');
        
        $("#rfiNo").val('');
        $("#faCd").val('');
        
        $("#rfiNo-H").val('');
        $("#faCd-H").val('');
        
        var itemTypeVal = $("#itemType").val();
        //alert(itemTypeVal);
        if(itemTypeVal == 1 || itemTypeVal == 3 || itemTypeVal == 4 || itemTypeVal == 5 || itemTypeVal == 6)
        {
            $("#expNo").attr('disabled', false);
            $.getJSON("../db/JSON/EPS_M_ACCOUNT.php?action=searchByUserType&itemTypeCd="+itemTypeVal+"&companyCd="+companyCd, function(data){
                var options = '';
                options += '<option value=""></option>';
                for (var i = 0; i < data.length; i++) {
                    options += '<option value="' + data[i].ACCOUNT_NO + '">' + data[i].ACCOUNT_CD_NAME + '</option>';		  
                }
                //alert(options);
                $("select#expNo.form-control").html(options);
            });
            
            $("input#rfiNo.form-control").attr('readonly', true);
            $("input#faCd-H.form-control").attr('readonly', true);
            
            $("select#rfiNo.form-control").attr('disabled', true);
            $("select#faCd.form-control").attr('disabled', true);
            
        }
        else 
            if(itemTypeVal == 2)
        {
            $("input#rfiNo-H.form-control").attr('readonly', false);
            $("#rfiNo").attr('readonly', false);
//            chargedBu = $("#chargedBu").val();
//            
            $("#expNo").attr('disabled', true);
//            
//            $("input#rfiNo-H.form-control").attr('readonly', true);
//            $("input#faCd-H.form-control").attr('readonly', true);
//            
//            $("select#rfiNo.form-control").attr('disabled', false);
//            $.getJSON("../db/JSON/EPS_M_RFI.php?action=searchByRfiNo&chargedBu="+chargedBu, function(data){
//                var options = '';
//                options += '<option value=""></option>';
//                for (var i = 0; i < data.length; i++) {
//                    options += '<option value="' + data[i].RFI_NO + '">' + data[i].RFI_NO + '</option>';		  
//                }
//                $("select#rfiNo.form-control").html(options);
//            });
//            $("select#faCd.form-control").attr('disabled', true);
        }
        else if(itemTypeVal == 7)
        {
            $("#expNo").attr('disabled', true);
            
            $("input#rfiNo-H.form-control").attr('readonly', false);
            $("input#faCd-H.form-control").attr('readonly', true);
            
            $("select#rfiNo.form-control").attr('disabled', true);
            $("select#faCd.form-control").attr('disabled', true);
        }
        else
        {
            $("#expNo").attr('disabled', true);
            
            $("input#rfiNo-H.form-control").attr('readonly', true);
            $("input#faCd-H.form-control").attr('readonly', true);
            
            $("select#rfiNo.form-control").attr('disabled', true);
            $("select#faCd.form-control").attr('disabled', true);
        }
    });
    
     $("input#rfiNo-H.form-control").keyup(function(event) {
        var rfiNoVal = $("input#rfiNo-H.form-control").val();
        if(rfiNoVal != "")
        {
            $("input#faCd-H.form-control").attr('readonly', false);
        }
        else
        {
            $("input#faCd-H.form-control").attr('readonly', true);
        }
    });  
        
    $("select#rfiNo.form-control").change(function(event) {
        $("select#faCd.form-control").val('');

        var rfiNoVal = $("select#rfiNo.form-control").val();
        
        if(rfiNoVal != "")
        {
            $("select#faCd.form-control").html("");
            $.getJSON("../db/JSON/EPS_M_RFI.php?action=searchByFaCd&rfiNo="+rfiNoVal, function(data){
                var options = '';
                for (var i = 0; i < data.length; i++) {
                    options += '<option value="' + data[i].FA_CD + '">' + data[i].FA_CD + " - " + data[i].DESCRIPTION + '</option>';		  
                }
                $("select#faCd.form-control").html(options);
            });
            $("select#faCd.form-control").attr('disabled', false);
        }
        else
        {
            $("select#faCd.form-control").attr('disabled', true);
        }
    });
        
    $("#itemName").keydown(function(){
        $('#itemCd').val('99');
        $("#um").attr('disabled', false);
        $("#supplierName").attr('disabled', false);
        $("#price").attr('readonly', false);
    });
    
    $(function(){
        $("#itemName").autocomplete({
            source      : '../db/MASTER/EPS_M_ITEM_PRICE.php?action=searchAutoItemPrice',
            minLength   : 2,//search after two characters
            select      : function (event, ui) {
                
                if($.trim(ui.item.itemCd) != '99'){
                    $('#itemCd').val(ui.item.itemCd);
                    $('#supplierCd').val(ui.item.supplierCd);
                    $('#supplierName').val(ui.item.supplierName);
                    $('#um').val(ui.item.unitCd);
                    $('#price').val(ui.item.price);
                    
                    // format number
                    $('#price').val(function(index, value) {
                        return value
                            .replace(/\D/g, '')
                            .replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                        ;
                    });
                    $("#um").attr('disabled', true);
                    $("#supplierName").attr('disabled', true);
                    $("#price").attr('readonly', true);
                    
                    var qtyVal          = $("#qty").val().replace(/,/g, '');
                    if(qtyVal > 0)
                    {
                        var itemPriceVal    = $("#price").val().replace(/,/g, '');
                        var amountVal       = qtyVal * itemPriceVal;
                        $("#amount").val(numberWithCommas(amountVal));
                        
                    }
                }
                else
                {
                    $("#um").attr('disabled', false);
                    $("#supplierName").attr('disabled', false);
                    $("#price").attr('readonly', false);
                }
                
                $("#qty").attr('readonly', false);
            }
        });
    });
    
    $("#supplierName").keydown(function(){
        $('#supplierCd').val('SUP99');
        $("#supplierName").attr('disabled', false);
    });
    
    $(function(){
        $("#supplierName").autocomplete({
            source      : '../db/MASTER/EPS_M_SUPPLIER.php?action=SEARCHAUTOSUPPLIER',
            minLength   : 2,//search after two characters
            select      : function (event, ui) {
                
                if($.trim(ui.item.supplierCd) != 'SUP99'){
                    $('#supplierCd').val(ui.item.supplierCd);
                    $('#supplierName').val(ui.item.supplierCd);
                }
                else
                {
                    $('#supplierCd').val('SUP99');
                    $("#supplierName").attr('disabled', false);
                }
            }
        });
    });
    
    /*$("#supplierName").change(function(){
        if($.trim($("#supplierName").val()) != "")
        {
            $("#supplierCd").val($.trim($("#supplierName").val()));
        }
        else
        {
            $("#supplierCd").val('');
        }
    });*/
    
    $("#price").keypress(function(event) {
        return isNumberPricePr(event, this);
    }); 
    
    $("#price").keyup(function(event){
        $("#qty").attr('readonly', false);
        
        var qtyVal          = $("#qty").val().replace(/,/g, '');
        var itemPriceVal    = $("#price").val().replace(/,/g, '');
        var amountVal       = qtyVal * itemPriceVal;
        $("#amount").val(numberWithCommas(amountVal));
        
        // skip for arrow keys
        if(event.which >= 37 && event.which <= 40){
            event.preventDefault();
        }
        
        $(this).val(function(index, value) {
            value = value.replace(/,/g,'');
            return numberWithCommas(value);
        });
    });
    
    $("#qty").keypress(function(event) {
        return isNumberQtyPr(event, this);
    }); 
    
    $("#qty").keyup(function(event){
        var qtyVal          = $("#qty").val().replace(/,/g, '');
        var itemPriceVal    = $("#price").val().replace(/,/g, '');
        var amountVal       = qtyVal * itemPriceVal;
        $("#amount").val(numberWithCommas(amountVal));
        
        // skip for arrow keys
        if(event.which >= 37 && event.which <= 40){
            event.preventDefault();
        }
        
        $(this).val(function(index, value) {
            value = value.replace(/,/g,'');
            return numberWithCommas(value);
        });
    });
    
    $("#deliveryDate").datepicker({
        dateFormat: 'dd/mm/yy',
        minDate: '+2W',
        maxDate: '+2Y',
        autoClose: true,
        //beforeShowDay: $.datepicker.noWeekends
        beforeShowDay: noWeekendsOrHolidays
    });
    /**
     * =========================================================================================================
     * DEFINE DIALOG
     * =========================================================================================================
     **/
     /** Dialog form budget */
    $("#dialog-form-item").dialog({
        autoOpen    : false,
        closeOnEscape   : false,
	height      : 570,
	width       : 780,
        position    : {my: "center", at: "center", of: $("#prItemTable"), within: $("#prItemTable")},
	modal       : true,
        open        : function() {                         // open event handler
            $(this)                                // the element being dialogged
                .parent()                          // get the dialog widget element
                .find(".ui-dialog-titlebar-close") // find the close button for this dialog
                .hide();                           // hide it
        },
        buttons     : {
            "Save"  : function(){
                var edataItem;
                var itemSeqHidden       = $("#itemSeqHidden").val();
                var itemType            = $("#itemType").val();
                var expNo               = $("#expNo").val();
                var rfiNo               = $("#rfiNo").val();
                var faCd                = "";
                var itemCd              = $("#itemCd").val();
                var itemName            = $.trim($("#itemName").val()).toUpperCase();
                var itemNameRefHidden   = $.trim($("#itemNameRefHidden").val()).toUpperCase();
                var supplierCd          = $.trim($("#supplierCd").val()).toUpperCase();
                var supplierName        = $.trim($("#supplierName").val()).toUpperCase();
                var um                  = $.trim($("#um").val()).toUpperCase();
                var price               = $("#price").val().replace(/,/g, '');
                var qty                 = $("#qty").val().replace(/,/g, '');
                var amount              = $("#amount").val().replace(/,/g, '');
                var deliveryDate        = $.trim($("#deliveryDate").val()).toUpperCase();
                var remark              = $.trim($("#remark").val()).toUpperCase();
                var action              = $.trim($("#dialog-form-item").dialog("option","title")).substr(0,4);
                
                // Non HDI Investment
                if(itemType == "2")
                {
                    rfiNo = $("#input#rfiNo.form-control").val();
                    alert('test');
                    alert(rfiNo);
                    //faCd = $("select#faCd.form-control").val();
                }
                // HDI Investment
                if(itemType == "7")
                {
                    rfiNo = $("input#rfiNo-H.form-control").val();
                    faCd = $("input#faCd-H.form-control").val();
                }
                
                if(rfiNo == null)
                {
                    rfiNo = "";
                }
                
                if(expNo == null)
                {
                    expNo = "";
                }
                
                edataItem = "itemSeqHiddenPrm="+itemSeqHidden+"&itemTypePrm="+itemType
                            +"&expNoPrm="+expNo+"&rfiNoPrm="+rfiNo+"&faCdPrm="+faCd
                            +"&itemCdPrm="+itemCd+"&itemNamePrm="+encodeURIComponent(itemName)+"&itemNameRefHiddenPrm="+encodeURIComponent(itemNameRefHidden)
                            +"&supplierCdPrm="+encodeURIComponent(supplierCd)+"&supplierNamePrm="+encodeURIComponent(supplierName)
                            +"&umPrm="+encodeURIComponent(um)+"&pricePrm="+price+"&qtyPrm="+qty+"&amountPrm="+amount
                            +"&deliveryDatePrm="+deliveryDate+"&remarkPrm="+encodeURIComponent(remark)
                            +"&actionPrm="+action;
                    
                $.ajax({
                    type: 'GET',
                    url: '../db/PR_SESSION/UPDATE_ITEM_TEMP.php',
                    data: edataItem,
                    success: function(data){
                        $("div#dialog-mandatory-msg-1.alert").css("display","none");
                        $("div#dialog-mandatory-msg-2.alert").css("display","none");
                        $("div#dialog-mandatory-msg-3.alert").css("display","none");
                        $("div#dialog-mandatory-msg-4.alert").css("display","none");
                        $("div#dialog-mandatory-msg-5.alert").css("display","none");
                        $("div#dialog-mandatory-msg-6.alert").css("display","none");
                        $("div#dialog-mandatory-msg-7.alert").css("display","none");
                        $("div#dialog-mandatory-msg-8.alert").css("display","none");
                        $("div#dialog-duplicate-msg.alert").css("display","none");
                        $("div#dialog-undefined-msg.alert").css("display","none");
                        
                        var msg         = data.split('||');
                        var rowPrItem   = msg[1];
                        
                        if($.trim(msg[0]) == "Success_Add" || $.trim(msg[0]) == 'Success_Edit')
                        {
                            $("#dialog-form-item").dialog("close");
                            $("#prItemTable tbody").html("");
                            $("#prItemTable tbody").append(rowPrItem);
                            
                            /** Attachment Form */
                            var rowCountItem  = $("#prItemTable > tbody >tr").length;
                            if(rowCountItem > 0)
                            {
                                $('select#itemNameFile').children('option:not(:first)').remove();
                                
                                for(var i = 0; i< rowCountItem; i++)
                                {
                                    
                                    var itemCdVal = $("#prItemTable > tbody > tr:eq("+i+") td:eq(2)").text();
                                    var itemNameVal = $("#prItemTable > tbody > tr:eq("+i+") td:eq(3)").text();
                                    $('#itemNameFile').append($('<option>',
                                    {
                                        value: itemCdVal,
                                        text : itemNameVal
                                    }));
                                }
                                $("#itemNameFile").attr('disabled', false);
                            }
                            else
                            {
                                $("#itemNameFile").attr('disabled', true);
                                $("input[type = 'file']").attr('disabled', true);
                            }
                            
                            /** Sum Total Amount */
                            var totalAmount = calculateTotalAmount();
                              
                            // Define row Total Amount
                            defineRowPRItemTotal(totalAmount);
                             
                            /** Max Sub Total Amount */
                            var maxAmount = calculateMaxAmount();
                            $("#prItemTotalHidden").val(maxAmount);
                            
                            /** Calculate Approver */
                            calculatePrApprover(maxAmount);
                            
                            /** Update attachment table */
                            if($.trim(msg[0]) == 'Success_Edit')
                            {
                                var rowPrAttachment   = msg[2];
                                
                                $("#prAttachmentTable tbody").html("");
                                $("#prAttachmentTable tbody").append(rowPrAttachment);
                                
                            }
                        }
                        else if($.trim(data) == 'Mandatory_1')
                        {
                            $("div#dialog-mandatory-msg-1.alert").css("display","block");
                        }
                        else if($.trim(data) == 'Mandatory_2')
                        {
                            $("div#dialog-mandatory-msg-2.alert").css("display","block");
                        }
                        else if($.trim(data) == 'Mandatory_3')
                        {
                            $("div#dialog-mandatory-msg-3.alert").css("display","block");
                        }
                        else if($.trim(data) == 'Mandatory_4')
                        {
                            $("div#dialog-mandatory-msg-4.alert").css("display","block");
                        }
                        else if($.trim(data) == 'Mandatory_5')
                        {
                            $("div#dialog-mandatory-msg-5.alert").css("display","block");
                        }
                        else if($.trim(data) == 'Mandatory_6')
                        {
                            $("div#dialog-mandatory-msg-6.alert").css("display","block");
                        }
                        else if($.trim(data) == 'Mandatory_7')
                        {
                            $("div#dialog-mandatory-msg-7.alert").css("display","block");
                        }
                        else if($.trim(data) == 'Mandatory_8')
                        {
                            $("div#dialog-mandatory-msg-8.alert").css("display","block");
                        }
                        else if($.trim(data) == 'Duplicate')
                        {
                            $("div#dialog-duplicate-msg.alert").css("display","block");
                        }
                        else if($.trim(data) == 'SessionExpired')
                        {
                            window.location = "../ecom/WCOM011.php";
                        }
                        else
                        {
                            $("div#dialog-undefined-msg.alert").css("display","block");
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
     $("a#btn-add-item").click(function() 
     {
        chargedBu   = $("#chargedBu").val();
       
        if(chargedBu != "")
        {
            $("div#dialog-mandatory-msg-1.alert").css('display','none');
            $("div#dialog-duplicate-msg-2.alert").css('display','none');
            $("div#dialog-duplicate-msg-3.alert").css('display','none');
            $("div#dialog-mandatory-msg-4.alert").css('display','none');
            $("div#dialog-mandatory-msg-5.alert").css('display','none');
            $("div#dialog-mandatory-msg-6.alert").css('display','none');
            $("div#dialog-mandatory-msg-7.alert").css('display','none');
            $("div#dialog-mandatory-msg-8.alert").css('display','none');
            $("div#dialog-duplicate-msg.alert").css('display','none');
            $("div#dialog-undefined-msg.alert").css('display','none');

            $("#dialog-form-item").dialog('option', 'title', 'Add PR Detail');
            $("#dialog-form-item").dialog("open");

            $("#expNo").attr('disabled', true);
            $("input#rfiNo-H.form-control").attr('readonly', true);
            $("select#rfiNo.form-control").attr('disabled', true);
            $("input#faCd-H.form-control").attr('readonly', true);
            $("select#faCd.form-control").attr('disabled', true);
            $("#supplierName").attr('disabled', false);
            $("#um").attr('disabled', false);
            $("#price").attr('readonly', false);

            $("#itemType").val('');
            $("#expNo").val('');
            $("input#rfiNo-H.form-control").val('');
            $("select#rfiNo.form-control").val('');
            $("input#faCd-H.form-control").val('');
            $("select#faCd.form-control").val('');
            $("#itemCd").val('');
            $("#itemName").val('');
            $("#supplierCd").val('');
            $("#supplierName").val('');
            $("#um").val('');
            $("#price").val('');
            $("#qty").val('');
            $("#amount").val('');
            $("#deliveryDate").val('');
            $("#remark").val('');

            $("#refItemName").val('');
            $("#seqItem").val('');
        }
        else
        {
            alert("Mandatory! Please select charged BU before add item.");
        }
        
        
    });
     
    $("a#btn-edit-item").click(function() 
    {
        var radioItemVal = $.trim($("input[name='radioItem']:checked").val());
        
        if(radioItemVal != "")
        {
            chargedBu   = $("#chargedBu").val();
            
            var rowIndex            = $("#prItemTable tbody input[name=radioItem]:checked").closest('tr').index();
            var itemSeqVal          = $.trim($("#prItemTable tbody tr:eq("+rowIndex+") td:eq(0)").text());
            var itemCdVal           = $.trim($("#prItemTable tbody tr:eq("+rowIndex+") td:eq(2)").text());
            var itemNameVal         = $.trim($("#prItemTable tbody tr:eq("+rowIndex+") td:eq(3)").text());
            var itemNameRefVal      = $.trim($("#prItemTable tbody tr:eq("+rowIndex+") td:eq(4)").text());
            var deliveryDateVal     = $.trim($("#prItemTable tbody tr:eq("+rowIndex+") td:eq(5)").text());
            var itemTypeVal         = $.trim($("#prItemTable tbody tr:eq("+rowIndex+") td:eq(6)").text());
            var expNoVal            = $.trim($("#prItemTable tbody tr:eq("+rowIndex+") td:eq(7)").text());
            var rfiNoVal            = $.trim($("#prItemTable tbody tr:eq("+rowIndex+") td:eq(8)").text());
            var faCdVal             = $.trim($("#prItemTable tbody tr:eq("+rowIndex+") td:eq(9)").text());
            var umVal               = $.trim($("#prItemTable tbody tr:eq("+rowIndex+") td:eq(10)").text());
            var qtyVal              = $.trim($("#prItemTable tbody tr:eq("+rowIndex+") td:eq(11)").text());
            var priceVal            = $.trim($("#prItemTable tbody tr:eq("+rowIndex+") td:eq(12)").text());
            var supplierCdVal       = $.trim($("#prItemTable tbody tr:eq("+rowIndex+") td:eq(13)").text());
            var supplierNameVal     = $.trim($("#prItemTable tbody tr:eq("+rowIndex+") td:eq(14)").text());
            var amountVal           = $.trim($("#prItemTable tbody tr:eq("+rowIndex+") td:eq(15)").text());
            var remarkVal           = $.trim($("#prItemTable tbody tr:eq("+rowIndex+") td:eq(16)").text());
            
            $("div#dialog-mandatory-msg-1.alert").css('display','none');
            $("div#dialog-duplicate-msg-2.alert").css('display','none');
            $("div#dialog-duplicate-msg-3.alert").css('display','none');
            $("div#dialog-mandatory-msg-4.alert").css('display','none');
            $("div#dialog-mandatory-msg-5.alert").css('display','none');
            $("div#dialog-mandatory-msg-6.alert").css('display','none');
            $("div#dialog-mandatory-msg-7.alert").css('display','none');
            $("div#dialog-mandatory-msg-8.alert").css('display','none');
            $("div#dialog-duplicate-msg.alert").css('display','none');
            $("div#dialog-undefined-msg.alert").css('display','none');

            $("#dialog-form-item").dialog('option', 'title', 'Edit PR Detail');
            $("#dialog-form-item").dialog("open");
            
            $("#expNo").attr('disabled', true);
            $("select#rfiNo.form-control").attr('disabled', true);
            $("select#faCd.form-control").attr('disabled', true);
            $("input#rfiNo-H.form-control").attr('readonly', true);
            $("input#faCd-H.form-control").attr('readonly', true);
            if(itemCdVal != "99")
            {
                $("#supplierName").attr('disabled', true);
                $("#um").attr('disabled', true);
                $("#price").attr('readonly', true);
            }
            else
            {
                $("#supplierName").attr('disabled', false);
                $("#um").attr('disabled', false);
                $("#price").attr('readonly', false);
            }
            $("#qty").attr('readonly', false);
            
            $("#itemType").val('');
            $("#expNo").val('');
            $("select#rfiNo.form-control").val('');
            $("select#faCd.form-control").val('');
            $("input#rfiNo-H.form-control").val('');
            $("input#faCd-H.form-control").val('');
            $("#itemCd").val('');
            $("#itemName").val('');
            $("#supplierCd").val('');
            $("#supplierName").val('');
            $("#um").val('');
            $("#price").val('');
            $("#qty").val('');
            $("#amount").val('');
            $("#deliveryDate").val('');
            $("#remark").val('');

            if(itemTypeVal == "1" || itemTypeVal == "3" || itemTypeVal == "4" || itemTypeVal == "5" || itemTypeVal == "6")
            {
                $("#expNo").attr('disabled', false);
                
                $.getJSON("../db/JSON/EPS_M_ACCOUNT.php?action=searchByUserType&itemTypeCd="+itemTypeVal+"&companyCd="+companyCd, function(data){
                    var options = '';
                    options += '<option value=""></option>';
                    for (var i = 0; i < data.length; i++) {
                        options += '<option value="' + data[i].ACCOUNT_NO + '">' + data[i].ACCOUNT_CD_NAME + '</option>';		  
                    }
                    $("select#expNo.form-control").html(options);
                    $("#expNo").val(expNoVal);
                });
                
            }
            else if (itemTypeVal == "2")
            {
               
                $("select#rfiNo.form-control").attr('disabled', false);
                $("select#faCd.form-control").attr('disabled', false);
                
                $.getJSON("../db/JSON/EPS_M_RFI.php?action=searchByRfiNo&chargedBu="+chargedBu, function(data){
                    var options = '';
                    options += '<option value=""></option>';
                    for (var i = 0; i < data.length; i++) {
                        options += '<option value="' + data[i].RFI_NO + '">' + data[i].RFI_NO + '</option>';		  
                    }
                    $("select#rfiNo.form-control").html(options);
                    $("#rfiNo").val(rfiNoVal);
                });
                
                $("select#faCd.form-control").html("");
                $.getJSON("../db/JSON/EPS_M_RFI.php?action=searchByFaCd&rfiNo="+rfiNoVal, function(data){
                    var options = '';
                    options += '<option value=""></option>';
                    for (var i = 0; i < data.length; i++) {
                        options += '<option value="' + data[i].FA_CD + '">' + data[i].FA_CD + " - " + data[i].DESCRIPTION + '</option>';		  
                    }
                    $("select#faCd.form-control").html(options);
                    $("#faCd").val(faCdVal);
                });
            }
            else if (itemTypeVal == "7")
            {
                $("input#rfiNo-H.form-control").attr('readonly', false);
                $("input#faCd-H.form-control").attr('readonly', false);
                
                $("#rfiNo-H").val(rfiNoVal);
                $("#faCd-H").val(faCdVal);
            }
            else
            {
                $("#expNo").attr('disabled', true);
                $("select#rfiNo.form-control").attr('disabled', true);
                $("select#faCd.form-control").attr('disabled', true);
                $("input#rfiNo-H.form-control").attr('readonly', true);
                $("input#faCd-H.form-control").attr('readonly', true);
            }
            
            $("#itemSeqHidden").val(itemSeqVal);
            $("#itemNameRefHidden").val(itemNameRefVal);
            $("#itemType").val(itemTypeVal);
            $("#itemCd").val(itemCdVal);
            $("#itemName").val(itemNameVal);
            $("#supplierCd").val(supplierCdVal);
            $("#supplierName").val(supplierNameVal);
            $("#um").val(umVal);
            $("#price").val(priceVal);
            $("#qty").val(qtyVal);
            $("#amount").val(amountVal);
            $("#deliveryDate").val(deliveryDateVal);
            $("#remark").val(remarkVal);
            
        }
        else
        {
            alert("Please select PR Item before edit.");
        }
        
    });
     
    $("a#btn-del-item").click(function() {
        var radioItemVal = $.trim($("input[name='radioItem']:checked").val());
        
        if(radioItemVal != "")
        {
            /** Save confirmation */
            $("#dialog-form-item-delete").html("Do you want delete item?");
            $("#dialog-form-item-delete").dialog({
                //autoOpen    : false,
                height      : 155,
                width       : 400,
                //position    : {my: "center", at: "top", of: $("body"), within: $("body")},
                modal       : true,
                buttons     : {
                    "Yes": function()
                    {
                        $(this).dialog("close");
                        
                        var rowIndex    = $("#prItemTable tbody input[name=radioItem]:checked").closest('tr').index();
                        var itemName    = $.trim($("#prItemTable tbody tr:eq("+rowIndex+") td:eq(3)").text());
                        var actionForm  = $.trim($("#actionFormHidden").val().toUpperCase());
                        var prNo        = $.trim($("#prNo").val());
                        
                        var eDelItem;
                        var action      = "DEL";
						//alert(actionForm);
                        var itemSeq     = $.trim(rowIndex);
                        
                        var countDel    = 0;
                        
                        eDelItem = "itemSeqPrm="+itemSeq+"&itemNamePrm="+encodeURIComponent(itemName)+"&prNoPrm="+prNo
                                    +"&actionFormPrm="+actionForm+"&actionPrm="+action;
                        $.ajax({
                            type: "GET",
                            url: '../db/PR_SESSION/UPDATE_ITEM_TEMP.php',
                            data: eDelItem,
                            success: function(data)
                            {
                                var msg = data.split('||');
                                $("div#undefined-msg.alert.alert-danger").css("display","none");
                                if($.trim(msg[0]) == "Success_Delete")
                                {
                                    var rowPrItem = msg[1];
                                    $("#dialog-form-item").dialog("close");
                                    $("#prItemTable tbody").html("");
                                    $("#prItemTable tbody").append(rowPrItem);

                                    /** Attachment Form */
                                    var rowCountItem  = $("#prItemTable > tbody >tr").length;
                                    if(rowCountItem > 0)
                                    {
                                     
                                        $('select#itemNameFile').children('option:not(:first)').remove();

                                        for(var i = 0; i< rowCountItem; i++)
                                        {
                                            var itemNameVal = $("#prItemTable > tbody > tr:eq("+i+") td:eq(3)").text();
                                            var itemCdVal = $("#prItemTable > tbody > tr:eq("+i+") td:eq(2)").text();
                                            $('#itemNameFile').append($('<option>',
                                            {
                                                value: itemCdVal,
                                                text : itemNameVal
                                            }));
                                        }
                                        $("#itemNameFile").attr('disabled', false);
                                         
                                        /** Sum Total Amount */
                                        var totalAmount = calculateTotalAmount();

                                        // Define row for Total PR Detail
                                        defineRowPRItemTotal(totalAmount);

                                        /** Max Sub Total Amount */
                                       
                                        var maxAmount = calculateMaxAmount();
                                        $("#prItemTotalHidden").val(maxAmount);
										
										/** Calculate Approver */
                                        calculatePrApprover(maxAmount);
                                    }
                                    else
                                    {
                                        $("#itemNameFile").attr('disabled', true);
                                        $("input[type = 'file']").attr('disabled', true);

                                        $("table#prItemTable.table.table-striped.table-bordered tfoot").html("");

                                        $("table#prAppTable select").val('');
                                        $("input[name=remarkBypass]").val('');
                                        $("input[type='checkbox'][name='checkedBypass[]']").prop('checked', false);

                                        if($("#userTypeHidden").val() == "UT_01")
                                        {
                                            $("table#prAppTable select").attr('disabled', true);
                                        }
                                        else
                                        {
                                            $("table#prAppTable select").attr('disabled', false);
                                        }
                                        $("input[name=remarkBypass]").attr('readonly', true);
                                        $("input[type='checkbox'][name='checkedBypass[]']").attr('disabled', true);

                                        $("#prItemTotalHidden").val(0);
                                    }
                                    
                                    /** Update attachment table */
                                    var rowPrAttachment   = msg[2];
                                    $("#prAttachmentTable tbody").html("");
                                    $("#prAttachmentTable tbody").append(rowPrAttachment);
                                }
                                else
                                {
                                    $("div#undefined-msg").css("display","block");
                                }
                                
                                if(actionForm == "APPROVAL")
                                {
                                    countDel++;
                                    ("#countDelItemHidden").val(countDel);
                                }
                            }
                        });
                    },
                    "No": function(){
                        $(this).dialog("close");
                    }
                }
            });

            
        }
        else
        {
            alert("Please select PR Item before delete.");
        }
    });
    
    $("a#btn-del-attachment").click(function() {
        var radioFileVal = $.trim($('input[name=radioFile]:checked').val());
        if(radioFileVal != "")
        {
            var rowIndex = $("#prAttachmentTable tbody input[name=radioFile]:checked").closest('tr').index();
            var eDelAttachment;
            var btn             = "DEL";
            var action          = $.trim($("#actionFormHidden").val().toUpperCase());
            var attachmentSeq   = $.trim(rowIndex);
            var fileName        = $.trim($("#prAttachmentTable tbody tr:eq("+rowIndex+") td:eq(4)").text());
            var prNo            = $.trim($("#prNo").val());
            
            eDelAttachment = "attachmentSeqPrm="+attachmentSeq+"&fileNamePrm="+fileName+"&prNoPrm="+prNo
                                +"&btnPrm="+btn+"&actionPrm="+action;
            
            $.ajax({
                type: "POST",
                url: "../db/PR_SESSION/UPDATE_ATTACHMENT_TEMP.php",
                data: eDelAttachment,
                success: function(data)
                {
                    var msg = data.split('||');
                    if($.trim(msg[0]) == "Success_Del")
                    {
                        var countRowFile= msg[1];
                        var rowFile     = msg[2];
                        var getRowFile  = JSON.parse(rowFile);
                        
                        $("#prAttachmentTable tbody").html("");
                        if(countRowFile > 0)
                        {
                            for(var i=0; i < countRowFile; i++)
                            {
                                var fileSeqHiddenVal= getRowFile[i].fileSeqHidden;
                                var itemCdFileVal   = getRowFile[i].itemCdFile; 
                                var itemNameFileVal = getRowFile[i].itemNameFile; 
                                var fileNameVal     = getRowFile[i].fileNameVal; 
                                var fileTypeVal     = getRowFile[i].fileTypeVal;
                                var fileSizeVal     = getRowFile[i].fileSizeVal; 
                                var addRowFile = "<tr>"
                                                    +"<td style='text-align: right;'>"+fileSeqHiddenVal+"</td>"
                                                    +"<td><input type='radio' name='radioFile' value="+fileSeqHiddenVal+"></td>"
                                                    +"<td>"+itemCdFileVal+"</td>"
                                                    +"<td>"+itemNameFileVal+"</td>"
                                                    +"<td>"+fileNameVal+"</td>"
                                                    +"<td>"+fileTypeVal+"</td>"
                                                    +"<td>"+fileSizeVal+"</td>"
                                                    +"</tr>";
                                $("#prAttachmentTable tbody").append(addRowFile);
                            }
                            
                        }
                    }
                }
            });
        }
        else
        {
            alert("Please select PR Attachment before delete.");
        }
    });
    
    /**
     * =========================================================================================================
     * FUNCTION
     * =========================================================================================================
     **/
    
    function defineRowItem(rowItem)
    {
        $("#prItemTable tbody").html("");
        $("#prItemTable tbody").append(rowItem);
    }
    
    function defineRowPRItemTotal(totalAmount)
    {
       
        $("table#prItemTable.table.table-striped.table-bordered tfoot").html("");
        
        var rowTotal = "<tr>"
                                +"<th colspan='12'>Total</th>"
                                +"<th style='text-align: right;'>"+addCommas(totalAmount)+"</th>"
                                +"<th></th>"
                                +"</tr>";
        $("table#prItemTable.table.table-striped.table-bordered tfoot").append(rowTotal);
         
    }
    
    function calculateMaxAmount()
    {
        //Array to hold the value
        var rowvalues = [];
                
        var maxAmount = 0
        $("#prItemTable tbody tr td.amount").each(function(index){
            var amount = $.trim($(this).text().replace(/,/g, ''));
            //Push the value to array
            var rowvalue = [];
            rowvalue.push(amount);
            rowvalues.push(rowvalue);
        });
        //Get the max value from array
        maxAmount = Math.max.apply(Math, rowvalues);
        
        return maxAmount;
        
        /*var max = 0;
        $("td.td-align-right.amount").each(function(){
            if( parseInt($(this).text().replace(/,/g, '')) > max){
                max = parseInt($(this).text().replace(/,/g, ''));
            }
        });
        return max;*/
    }
    
    function calculateTotalAmount()
    {
        var totalAmount = 0
        $("#prItemTable tbody tr td.amount").each(function(index){
            var amount = $.trim($(this).text().replace(/,/g, ''));
            totalAmount = parseInt(totalAmount) + parseInt(amount);
        });
        return totalAmount;
    }
    
    function calculatePrApprover(maxAmount)
    {
        var currencyCd = "IDR";
        var eMaxAmountNew;
        
        var initialCountApp = $("#maxPrApproverHidden").val();
          
        eMaxAmountNew = "maxPrAmountPrm="+maxAmount+"&currencyCdPrm="+currencyCd;
        $.ajax({
            type: 'GET',
            url: '../db/GET_TABLE/EPS_M_PR_APPROVER.php',
            data: eMaxAmountNew,
            success: function(data){
                if($.trim(data).substr(0,7) == 'Success')
                {
                    var countApp = parseInt($.trim(data).substr(7,1));
                    var appNoVal = 1;

                    if(countApp > 0)
                    {
						// Reset form
                        $("select[name=approverDept]").attr('disabled', true);
                        $("table#prAppTable select").val('');
                        $("input[type='checkbox'][name='checkedBypass[]']").prop('checked', false);
                        $("input[type='checkbox'][name='checkedBypass[]']").attr('disabled', true);
                        $("input[name=remarkBypass]").val('');
						
                        for(var i = 0; i < countApp; i++)
                        {
                            $("select#approverDept"+(i+1)+".form-control").attr('disabled', false);
                            appNoVal++;
                        }
                       
                        var actionFormHidden = $("#actionFormHidden").val();
                        if(actionFormHidden == "APPROVAL" && initialCountApp != countApp)
                        {
                            if(countApp > initialCountApp)
                            {
                                $("div#unmatch-limit-msg.alert.alert-info").css('display','block');
                            }
                            else
                            {
                                $("div#unmatch-limit-msg.alert.alert-info").css('display','none');
                            }
                            
                            $("#maxPrApproverHidden").val(countApp);
                        }
                    }
                    else
                    {
                        $("div#undefined-msg.alert.alert-danger").css('display','block');
                    }
                    $("#countAppDept").val(countApp);
                }
                else
                {
                    $("div#undefined-msg.alert.alert-danger").css('display','block');
                }
            }
        });
    }
    
    function countCheckInvestment()
    {
        var count = 0;
     
        $("#prItemTable tbody tr td:eq(6)").each(function(index){
            var itemType = $.trim($(this).text());
            
            if(itemType == '2')
            {
                count++;
            }
        });
        return count;
    }
    
    function checkInvestmentByChargedBu(chargedBu, prevChargedBu)
    {
        var rfiNoAll = "";
        var count = 0;
        var rowCountItem  = $("#prItemTable > tbody >tr").length;
        for(var x = 0; x < rowCountItem; x++)
        {
            var rfiNo   = $.trim($("#prItemTable tbody tr:eq("+x+") td:eq(8)").text()); 
            if(rfiNo != '')
            {
                if(x == 0)
                {
                    rfiNoAll = rfiNo;
                }
                else
                {
                    
                    rfiNoAll = rfiNoAll+","+rfiNo;
                }
            }
        }
        
        if(rfiNoAll != '')
        {
            var eData; 
            eData       = "rfiNoPrm="+rfiNoAll+"&chargedBuPrm="+chargedBu;
            $.ajax({
                type: 'GET',
                url: '../db/ERFI/ERFI_T_HEADER.php',
                data: eData,
                success: function(data){
                    $("#countInvestmentHidden").val(data);

                    if(data > 0)
                    {
                        $("#chargedBu").val(prevChargedBu);
                        alert("Existence Error! Different charged BU for this RFI. Please remove PR Detail first.");
                    }
                    else
                    {
                        $("#chargedBu").val(chargedBu);
                    }
                }
            });
        }
        return count;
    }
    
    function addCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
    
});


