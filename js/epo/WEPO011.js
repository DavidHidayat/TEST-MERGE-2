$(document).ready(function() {
    var prItemPrice = 0;
    var supItemPrice = 0;
    var limitPrice = 0;
    /**
     * =========================================================================================================
     * INITIAL VALUE
     * =========================================================================================================
     **/
    $("#deliveryDate").css('background-color', '#ffffff'); 
    if($("#itemCd").val() != '99'){
        $("#um").attr('disabled', true);
    }
    
    var str2 = "";
    $("#itemType option:selected" ).each(function() {
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
    /*$("#qty").keydown(function(){
        $("div#mandatory-msg-13.alert").css('display','none'); 
    });
    $("#qty").keypress(function(e) {
        var qtyLength = $("#qty").val().length;
        if(qtyLength > 1)
        {
            //if the letter is not digit then display error and don't type anything
            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                //display error message
                //$("#errmsg").html("Digits Only").show().fadeOut("slow");
                return false;
            }
        }
        else
        {
            if($(this).val() === '0')
            {
                $("div#mandatory-msg-13.alert").css('display','block');
            }
        }
    });*/
    $("#qty").keyup(function(e){
        $("#actualQty").val($("#qty").val());
        var remainQty = $("#qty").val() - $("#actualQty").val();
        $("#remainQty").val(remainQty);
    });
    $("#qty").keypress(function(event) {
        return isNumber(event, this);
    });    
    $("input#price").keyup(function(event) {
        // skip for arrow keys
        //if(event.which >= 37 && event.which <= 40) return;

        // format number
        //$(this).val(function(index, value) {
        //    return value
        //        .replace(/\D/g, '')
        //        .replace(/\B(?=(\d{3})+(?!\d))/g, ",")
        //    ;
        //});
		
		//return isNumberForPrice(event, this);
		
        // skip for arrow keys
        if(event.which >= 37 && event.which <= 40){
			event.preventDefault();
        }

        $(this).val(function(index, value) {
            value = value.replace(/,/g,'');
            return numberWithCommas(value);
        });
		
		/*if($.trim($("#currencyCd").val()) == "IDR")
        {
            prItemPrice = $.trim($("#prItemPrice").val()).replace(/,/g, '');
            supItemPrice = $.trim($("#price").val()).replace(/,/g, '');
            var addPrice = (20 * prItemPrice) / 100;
            limitPrice = parseInt(prItemPrice) + parseInt(addPrice);
            
            if(supItemPrice > limitPrice)
            {
                $("div#dialog-mandatory-msg-4.alert").css('display','block');
            }
            else
            {
                $("div#dialog-mandatory-msg-4.alert").css('display','none');
            }
        }*/
    });
    
    $("#itemName").keydown(function(){
        $('#itemCd').val('99');
        $("#um").attr('disabled', false);
    });
    
    $(function(){
        $("#itemName").autocomplete({
            source      : '../db/MASTER/EPS_M_ITEM_PRICE.php?action=searchAutoItemPrice',
            minLength   : 2,//search after two characters
            select      : function (event, ui) {
                if($.trim(ui.item.itemCd) != '99'){
                    $('#itemCd').val(ui.item.itemCd);
                    $('#supplierCdSet').val(ui.item.supplierCd);
                    $('#supplierNameSet').val(ui.item.supplierName);
                    $('#um').val(ui.item.unitCd);
                    $('#priceSet').val(ui.item.price);
                    $('#currencyCdSet').val(ui.item.currencyCd);
                    
                    // format number
                    $('#priceSet').val(function(index, value) {
                        return value
                            .replace(/\D/g, '')
                            .replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                        ;
                    });
                    $("#um").attr('disabled', true);
                }
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
            $("td#td-invs-no").css('display','none');
            $("td#td-rfi-no").css('display','none');

            $("#invNo").val('');
            $("#rfiNo").val('');

        }else if($.trim(str) == 'INV' || $.trim(str) == 'INV-MCH'){
            $("td#td-exp-no").css('display','none');
            $("td#td-inv-no").css('display','block');
            $("td#td-invs-no").css('display','none');
            $("td#td-rfi-no").css('display','none');

            $("#expNo").val(''); 
            $("#rfiNo").val('');
        }else if($.trim(str) == 'INV-S'){
            $("td#td-exp-no").css('display','none');
            $("td#td-inv-no").css('display','none');
            $("td#td-invs-no").css('display','block');
            $("td#td-rfi-no").css('display','none');

            $("#expNo").val(''); 
            $("#rfiNo").val('');
        }else{
            if($.trim(str) == 'RFI'){
                $("td#td-exp-no").css('display','none');
                $("td#td-inv-no").css('display','none');
                $("td#td-invs-no").css('display','none');
                $("td#td-rfi-no").css('display','block');

                $("#expNo").val(''); 
                $("#invNo").val('');

                $("#rfiNo").removeAttr('readonly');
            }
        }
    });
    
    $("#supplierCd").change(function(){
        var str = "";
        $( "#supplierCd option:selected" ).each(function() {
            str += $( this ).text() + " ";
        });
        if(str.length == 19)
        {
            str = $.trim(str).substr(5,3);
        }
        else
        {   
            str = $.trim(str).substr(7,3);
        }
        $("#currencyCd option[value='"+str+"']").prop("selected", true); 
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
    /** Dialog form */
    $("#dialog-form").dialog({
        autoOpen    : false,
        closeOnEscape   : false,
		height      : 430,
		width       : 620,
        position    : {my: "center", at: "top", of: $("body"), within: $("body")},
		modal       : true,
        open            : function() {                         // open event handler
            $(this)                                // the element being dialogged
                .parent()                          // get the dialog widget element
                .find(".ui-dialog-titlebar-close") // find the close button for this dialog
                .hide();                           // hide it
        },
        buttons     : {
            "Save"  : function(){
                var edata;
                var transferId      = $.trim($("#transferIdSupplier").val());
                var supplierCd      = $.trim($("#supplierCd").val());
                var currencyCd      = $.trim($("#currencyCd").val());
                var leadTime        = $.trim($("#leadTime").val());
                var unitTime        = $.trim($("#unitTime").val());
                var price           = $.trim($("#price").val().replace(/,/g, ''));
                var attachmentLoc   = $.trim($("#attachmentLoc").val());
                var attachmentCip   = $.trim($("#attachmentCip").val());
                var remark          = $.trim($("#remark").val());
                var seqSupplier     = $.trim($("#seqSupplier").val());
                var action          = $.trim($("#dialog-form").dialog("option","title")).substr(0,4);
                var supplierCdGet   = $.trim($("table#supplierListTable.table.table-striped.table-bordered tbody tr#"+seqSupplier+" td#getSupplierCd"+seqSupplier).text());
                
                edata = "transferIdPrm="+transferId+"&supplierCdPrm="+supplierCd+"&currencyCdPrm="+currencyCd
                        +"&itemPricePrm="+price+"&limitPricePrm="+limitPrice
						+"&leadTimePrm="+leadTime+"&unitTimePrm="+unitTime
                        +"&attachmentLocPrm="+encodeURIComponent(attachmentLoc)+"&attachmentCipPrm="+encodeURIComponent(attachmentCip)+"&remarkPrm="+encodeURIComponent(remark)
                        +"&actionPrm="+action+"&supplierCdGetPrm="+supplierCdGet+"&seqSupplierPrm="+seqSupplier;
                
                $.ajax({
                    type: 'GET',
                    url: '../db/PR_to_PO/UPDATE_SUPPLIER_SESSION.php?action=UpdateSupplier',
                    data: edata,
                    success: function(data){
                        //alert(data);
                        $("div#dialog-mandatory-msg-1.alert").css('display','none');
                        $("div#dialog-mandatory-msg-2.alert").css('display','none');
                        $("div#dialog-mandatory-msg-3.alert").css('display','none');
                        $("div#dialog-mandatory-msg-4.alert").css('display','none');
                        $("div#dialog-duplicate-msg.alert").css('display','none');
                        $("div#dialog-undefined-msg.alert").css('display','none');
                        
                        if($.trim(data) == 'Success_Add' || $.trim(data) == 'Success_Edit')
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
                            $("div#dialog-duplicate-msg.alert").css('display','none');
                            $("div#dialog-undefined-msg.alert").css('display','none');
                        }
                        else if($.trim(data) == 'Mandatory_2')
                        {
                            $("div#dialog-mandatory-msg-1.alert").css('display','none');
                            $("div#dialog-mandatory-msg-2.alert").css('display','block');
                            $("div#dialog-mandatory-msg-3.alert").css('display','none');
                            $("div#dialog-mandatory-msg-4.alert").css('display','none');
                            $("div#dialog-duplicate-msg.alert").css('display','none');
                            $("div#dialog-undefined-msg.alert").css('display','none');
                        }
                        else if($.trim(data) == 'Mandatory_3')
                        {
                            $("div#dialog-mandatory-msg-1.alert").css('display','none');
                            $("div#dialog-mandatory-msg-2.alert").css('display','none');
                            $("div#dialog-mandatory-msg-3.alert").css('display','block');
                            $("div#dialog-mandatory-msg-4.alert").css('display','none');
                            $("div#dialog-duplicate-msg.alert").css('display','none');
                            $("div#dialog-undefined-msg.alert").css('display','none');
                        }
                        else if($.trim(data) == 'Mandatory_4')
                        {
                            $("div#dialog-mandatory-msg-1.alert").css('display','none');
                            $("div#dialog-mandatory-msg-2.alert").css('display','none');
                            $("div#dialog-mandatory-msg-3.alert").css('display','none');
                            $("div#dialog-mandatory-msg-4.alert").css('display','block');
                            $("div#dialog-duplicate-msg.alert").css('display','none');
                            $("div#dialog-undefined-msg.alert").css('display','none');
                        }
                        else if($.trim(data) == 'Duplicate')
                        {
                            $("div#dialog-mandatory-msg-1.alert").css('display','none');
                            $("div#dialog-mandatory-msg-2.alert").css('display','none');
                            $("div#dialog-mandatory-msg-3.alert").css('display','none');
                            $("div#dialog-mandatory-msg-4.alert").css('display','none');
                            $("div#dialog-duplicate-msg.alert").css('display','block');
                            $("div#dialog-undefined-msg.alert").css('display','none');
                        }
                        else if($.trim(data) == 'SessionExpired')
                        {
                            $("#dialog-confirm-session").dialog('open');
                        }
                        else{
                            $("div#dialog-mandatory-msg-1.alert").css('display','none');
                            $("div#dialog-mandatory-msg-2.alert").css('display','none');
                            $("div#dialog-mandatory-msg-3.alert").css('display','none');
                            $("div#dialog-mandatory-msg-4.alert").css('display','none');
                            $("div#dialog-duplicate-msg.alert").css('display','none');
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
                    var transferIdVal   = $("#transferIdSupplier").val();
                    var prNoVal         = $("#prNo").val();
                    var prItemPriceVal  = $.trim($("#prItemPrice").val().replace(/,/g, ''));
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
                    var itemCdVal       = $.trim($("#itemCd").val());
                    var itemNameVal     = $.trim($("#itemName").val());
                    var supplierCdVal   = $.trim($("#supplierCdSet").val());
                    var supplierNameVal = $.trim($("#supplierNameSet").val());
                    var currencyCdVal   = $.trim($("#currencyCdSet").val());
                    var priceVal        = $.trim($("#priceSet").val().replace(/,/g, ''));
                    var remarkProcVal   = $.trim($("#remarkProc").val());

                    edataItem = "prNoValPrm="+prNoVal+"&transferIdValPrm="+transferIdVal+"&prItemPricePrm="+prItemPriceVal
                                +"&itemTypeValPrm="+itemTypeVal+"&rfiNoValPrm="+rfiNoVal
                                +"&accountNoValPrm="+accountNoVal+"&invNoValPrm="+invNo+"&invsNoValPrm="+invsNo+"&umValPrm="+umVal
                                +"&qtyValPrm="+qtyVal+"&actualQtyValPrm="+actualQtyVal+"&remainQtyValPrm="+remainQtyVal
                                +"&deliveryDateValPrm="+deliveryDateVal+"&itemStatusValPrm="+itemStatusVal
                                +"&itemCdValPrm="+itemCdVal+"&itemNameValPrm="+encodeURIComponent(itemNameVal)
                                +"&supplierCdValPrm="+supplierCdVal+"&supplierNameValPrm="+encodeURIComponent(supplierNameVal)
                                +"&currencyCdValPrm="+currencyCdVal+"&priceValPrm="+priceVal
                                +"&remarkProcValPrm="+encodeURIComponent(remarkProcVal);

                    var qtyCheck = $("#qty").val().substr(0,1);
                    if(qtyCheck > 0)
                    {
                        $.ajax({
                            type: 'GET',
                            url: '../db/PR_to_PO/EPS_T_TRANSFER_SUPPLIER.php?action=SaveItem',
                            data: edataItem,
                            success: function(data){
                                //alert($.trim(data));
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#mandatory-msg-2.alert").css('display','none');
                                $("div#mandatory-msg-3.alert").css('display','none');
                                $("div#mandatory-msg-4.alert").css('display','none');
                                $("div#mandatory-msg-5.alert").css('display','none');
                                $("div#mandatory-msg-6.alert").css('display','none');
                                $("div#mandatory-msg-7.alert").css('display','none');
                                $("div#mandatory-msg-8.alert").css('display','none');
                                $("div#mandatory-msg-9.alert").css('display','none');
                                $("div#mandatory-msg-10.alert").css('display','none');
                                $("div#mandatory-msg-11.alert").css('display','none');
                                $("div#mandatory-msg-12.alert").css('display','none');
                                $("div#mandatory-msg-13.alert").css('display','none');
                                $("div#mandatory-msg-14.alert").css('display','none');
                                $("div#mandatory-msg-15.alert").css('display','none');
                                $("div#mandatory-msg-16.alert").css('display','none');
                                $("div#mandatory-msg-17.alert").css('display','none');
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
                                    $("div#mandatory-msg-8.alert").css('display','none');
                                    $("div#mandatory-msg-9.alert").css('display','none');
                                    $("div#mandatory-msg-10.alert").css('display','none');
                                    $("div#mandatory-msg-11.alert").css('display','none');
                                    $("div#mandatory-msg-12.alert").css('display','none');
                                    $("div#mandatory-msg-13.alert").css('display','none');
                                    $("div#mandatory-msg-14.alert").css('display','none');
                                    $("div#mandatory-msg-15.alert").css('display','none');
				    $("div#mandatory-msg-16.alert").css('display','none');
                                    $("div#mandatory-msg-17.alert").css('display','none');
                                    $("div#undefined-msg.alert").css('display','none');
                                    $("div#success-msg.alert.alert-success").css('display','block');

                                    $("button#btn-save-item.btn.btn-primary").attr('disabled', true);
                                    $("select#itemType").attr('disabled', true);
                                    $("select#expNo").attr('disabled', true);
                                    $("select#invNo").attr('disabled', true);
                                    $("#rfiNo").attr('readonly', true);
                                    $("select#um").attr('disabled', true);
                                    $("#qty").attr('readonly', true);
                                    $("#deliveryDate").attr('readonly', true);
                                    $("select#itemStatus").attr('disabled', true);
                                    $("#itemName").attr('readonly', true);
                                    $("#remarkProc").attr('readonly', true);
                                    $("#deliveryDate").attr('disabled', true);
                                    $("input[type = 'selectbox']").attr('disabled', true);
                                    $("input#deliveryDate.span2.hasDatepicker").css('background-color', '#EEE');

                                    $("a#window-add.btn.btn-small.btn-warning").css('display', 'none');
                                    $("a#window-edit.btn.btn-small.btn-success").css('display', 'none');
                                    $("a#window-delete.btn.btn-small.btn-danger").css('display', 'none');
                                    $("a#window-set.btn.btn-small.btn-info").css('display', 'none');
                                    window.location = "WEPO004.php?prNoCriteria="+prNoVal;
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
                                    $("div#mandatory-msg-10.alert").css('display','none');
                                    $("div#mandatory-msg-11.alert").css('display','none');
                                    $("div#mandatory-msg-12.alert").css('display','none');
                                    $("div#mandatory-msg-13.alert").css('display','none');
                                    $("div#mandatory-msg-14.alert").css('display','none');
                                    $("div#mandatory-msg-15.alert").css('display','none');
									$("div#mandatory-msg-16.alert").css('display','none');
                                    $("div#mandatory-msg-17.alert").css('display','none');
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
                                    $("div#mandatory-msg-10.alert").css('display','none');
                                    $("div#mandatory-msg-11.alert").css('display','none');
                                    $("div#mandatory-msg-12.alert").css('display','none');
                                    $("div#mandatory-msg-13.alert").css('display','none');
                                    $("div#mandatory-msg-14.alert").css('display','none');
                                    $("div#mandatory-msg-15.alert").css('display','none');
									$("div#mandatory-msg-16.alert").css('display','none');
                                    $("div#mandatory-msg-17.alert").css('display','none');
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
                                    $("div#mandatory-msg-10.alert").css('display','none');
                                    $("div#mandatory-msg-11.alert").css('display','none');
                                    $("div#mandatory-msg-12.alert").css('display','none');
                                    $("div#mandatory-msg-13.alert").css('display','none');
                                    $("div#mandatory-msg-14.alert").css('display','none');
                                    $("div#mandatory-msg-15.alert").css('display','none');
									$("div#mandatory-msg-16.alert").css('display','none');
                                    $("div#mandatory-msg-17.alert").css('display','none');
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
                                    $("div#mandatory-msg-10.alert").css('display','none');
                                    $("div#mandatory-msg-11.alert").css('display','none');
                                    $("div#mandatory-msg-12.alert").css('display','none');
                                    $("div#mandatory-msg-13.alert").css('display','none');
                                    $("div#mandatory-msg-14.alert").css('display','none');
                                    $("div#mandatory-msg-15.alert").css('display','none');
				    $("div#mandatory-msg-16.alert").css('display','none');
                                    $("div#mandatory-msg-17.alert").css('display','none');
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
                                    $("div#mandatory-msg-10.alert").css('display','none');
                                    $("div#mandatory-msg-11.alert").css('display','none');
                                    $("div#mandatory-msg-12.alert").css('display','none');
                                    $("div#mandatory-msg-13.alert").css('display','none');
                                    $("div#mandatory-msg-14.alert").css('display','none');
                                    $("div#mandatory-msg-15.alert").css('display','none');
									$("div#mandatory-msg-16.alert").css('display','none');
                                    $("div#mandatory-msg-17.alert").css('display','none');
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
                                    $("div#mandatory-msg-10.alert").css('display','none');
                                    $("div#mandatory-msg-11.alert").css('display','none');
                                    $("div#mandatory-msg-12.alert").css('display','none');
                                    $("div#mandatory-msg-13.alert").css('display','none');
                                    $("div#mandatory-msg-14.alert").css('display','none');
                                    $("div#mandatory-msg-15.alert").css('display','none');
									$("div#mandatory-msg-16.alert").css('display','none');
                                    $("div#mandatory-msg-17.alert").css('display','none');
                                    $("div#undefined-msg.alert").css('display','none');

                                    $("#remarkProc").attr('readonly', false);
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
                                    $("div#mandatory-msg-10.alert").css('display','none');
                                    $("div#mandatory-msg-11.alert").css('display','none');
                                    $("div#mandatory-msg-12.alert").css('display','none');
                                    $("div#mandatory-msg-13.alert").css('display','none');
                                    $("div#mandatory-msg-14.alert").css('display','none');
                                    $("div#mandatory-msg-15.alert").css('display','none');
									$("div#mandatory-msg-16.alert").css('display','none');
                                    $("div#mandatory-msg-17.alert").css('display','none');
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
                                    $("div#mandatory-msg-10.alert").css('display','none');
                                    $("div#mandatory-msg-11.alert").css('display','none');
                                    $("div#mandatory-msg-12.alert").css('display','none');
                                    $("div#mandatory-msg-13.alert").css('display','none');
                                    $("div#mandatory-msg-14.alert").css('display','none');
                                    $("div#mandatory-msg-15.alert").css('display','none');
									$("div#mandatory-msg-16.alert").css('display','none');
                                    $("div#mandatory-msg-17.alert").css('display','none');
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
                                    $("div#mandatory-msg-10.alert").css('display','none');
                                    $("div#mandatory-msg-11.alert").css('display','none');
                                    $("div#mandatory-msg-12.alert").css('display','none');
                                    $("div#mandatory-msg-13.alert").css('display','none');
                                    $("div#mandatory-msg-14.alert").css('display','none');
                                    $("div#mandatory-msg-15.alert").css('display','none');
									$("div#mandatory-msg-16.alert").css('display','none');
                                    $("div#mandatory-msg-17.alert").css('display','none');
                                    $("div#undefined-msg.alert").css('display','none');
                                }
                                else if($.trim(data) == 'Mandatory_10')
                                {
                                    $("div#mandatory-msg-1.alert").css('display','none');
                                    $("div#mandatory-msg-2.alert").css('display','none');
                                    $("div#mandatory-msg-3.alert").css('display','none');
                                    $("div#mandatory-msg-4.alert").css('display','none');
                                    $("div#mandatory-msg-5.alert").css('display','none');
                                    $("div#mandatory-msg-6.alert").css('display','none');
                                    $("div#mandatory-msg-7.alert").css('display','none');
                                    $("div#mandatory-msg-8.alert").css('display','none');
                                    $("div#mandatory-msg-9.alert").css('display','none');
                                    $("div#mandatory-msg-10.alert").css('display','block');
                                    $("div#mandatory-msg-11.alert").css('display','none');
                                    $("div#mandatory-msg-12.alert").css('display','none');
                                    $("div#mandatory-msg-13.alert").css('display','none');
                                    $("div#mandatory-msg-14.alert").css('display','none');
                                    $("div#mandatory-msg-15.alert").css('display','none');
									$("div#mandatory-msg-16.alert").css('display','none');
                                    $("div#mandatory-msg-17.alert").css('display','none');
                                    $("div#undefined-msg.alert").css('display','none');
                                }
                                else if($.trim(data) == 'Mandatory_11')
                                {
                                    $("div#mandatory-msg-1.alert").css('display','none');
                                    $("div#mandatory-msg-2.alert").css('display','none');
                                    $("div#mandatory-msg-3.alert").css('display','none');
                                    $("div#mandatory-msg-4.alert").css('display','none');
                                    $("div#mandatory-msg-5.alert").css('display','none');
                                    $("div#mandatory-msg-6.alert").css('display','none');
                                    $("div#mandatory-msg-7.alert").css('display','none');
                                    $("div#mandatory-msg-8.alert").css('display','none');
                                    $("div#mandatory-msg-9.alert").css('display','none');
                                    $("div#mandatory-msg-10.alert").css('display','none');
                                    $("div#mandatory-msg-11.alert").css('display','block');
                                    $("div#mandatory-msg-12.alert").css('display','none');
                                    $("div#mandatory-msg-13.alert").css('display','none');
                                    $("div#mandatory-msg-14.alert").css('display','none');
                                    $("div#mandatory-msg-15.alert").css('display','none');
									$("div#mandatory-msg-16.alert").css('display','none');
                                    $("div#mandatory-msg-17.alert").css('display','none');
                                    $("div#undefined-msg.alert").css('display','none');
                                }
                                else if($.trim(data) == 'Mandatory_12')
                                {
                                    $("div#mandatory-msg-1.alert").css('display','none');
                                    $("div#mandatory-msg-2.alert").css('display','none');
                                    $("div#mandatory-msg-3.alert").css('display','none');
                                    $("div#mandatory-msg-4.alert").css('display','none');
                                    $("div#mandatory-msg-5.alert").css('display','none');
                                    $("div#mandatory-msg-6.alert").css('display','none');
                                    $("div#mandatory-msg-7.alert").css('display','none');
                                    $("div#mandatory-msg-8.alert").css('display','none');
                                    $("div#mandatory-msg-9.alert").css('display','none');
                                    $("div#mandatory-msg-10.alert").css('display','none');
                                    $("div#mandatory-msg-11.alert").css('display','none');
                                    $("div#mandatory-msg-12.alert").css('display','block');
                                    $("div#mandatory-msg-13.alert").css('display','none');
                                    $("div#mandatory-msg-14.alert").css('display','none');
                                    $("div#mandatory-msg-15.alert").css('display','none');
									$("div#mandatory-msg-16.alert").css('display','none');
                                    $("div#mandatory-msg-17.alert").css('display','none');
                                    $("div#undefined-msg.alert").css('display','none');
                                }
                                else if($.trim(data) == 'Mandatory_13')
                                {
                                    $("div#mandatory-msg-1.alert").css('display','none');
                                    $("div#mandatory-msg-2.alert").css('display','none');
                                    $("div#mandatory-msg-3.alert").css('display','none');
                                    $("div#mandatory-msg-4.alert").css('display','none');
                                    $("div#mandatory-msg-5.alert").css('display','none');
                                    $("div#mandatory-msg-6.alert").css('display','none');
                                    $("div#mandatory-msg-7.alert").css('display','none');
                                    $("div#mandatory-msg-8.alert").css('display','none');
                                    $("div#mandatory-msg-9.alert").css('display','none');
                                    $("div#mandatory-msg-10.alert").css('display','none');
                                    $("div#mandatory-msg-11.alert").css('display','none');
                                    $("div#mandatory-msg-12.alert").css('display','none');
                                    $("div#mandatory-msg-13.alert").css('display','block');
                                    $("div#mandatory-msg-14.alert").css('display','none');
                                    $("div#mandatory-msg-15.alert").css('display','none');
									$("div#mandatory-msg-16.alert").css('display','none');
                                    $("div#mandatory-msg-17.alert").css('display','none');
                                    $("div#undefined-msg.alert").css('display','none');
                                }
                                else if($.trim(data) == 'Mandatory_15')
                                {
                                    $("div#mandatory-msg-1.alert").css('display','none');
                                    $("div#mandatory-msg-2.alert").css('display','none');
                                    $("div#mandatory-msg-3.alert").css('display','none');
                                    $("div#mandatory-msg-4.alert").css('display','none');
                                    $("div#mandatory-msg-5.alert").css('display','none');
                                    $("div#mandatory-msg-6.alert").css('display','none');
                                    $("div#mandatory-msg-7.alert").css('display','none');
                                    $("div#mandatory-msg-8.alert").css('display','none');
                                    $("div#mandatory-msg-9.alert").css('display','none');
                                    $("div#mandatory-msg-10.alert").css('display','none');
                                    $("div#mandatory-msg-11.alert").css('display','none');
                                    $("div#mandatory-msg-12.alert").css('display','none');
                                    $("div#mandatory-msg-13.alert").css('display','none');
                                    $("div#mandatory-msg-14.alert").css('display','none');
                                    $("div#mandatory-msg-15.alert").css('display','block');
									$("div#mandatory-msg-16.alert").css('display','none');
                                    $("div#mandatory-msg-17.alert").css('display','none');
                                    $("div#undefined-msg.alert").css('display','none');
                                }
                                else if($.trim(data) == 'Mandatory_16')
                                {
                                    $("div#mandatory-msg-1.alert").css('display','none');
                                    $("div#mandatory-msg-2.alert").css('display','none');
                                    $("div#mandatory-msg-3.alert").css('display','none');
                                    $("div#mandatory-msg-4.alert").css('display','none');
                                    $("div#mandatory-msg-5.alert").css('display','none');
                                    $("div#mandatory-msg-6.alert").css('display','none');
                                    $("div#mandatory-msg-7.alert").css('display','none');
                                    $("div#mandatory-msg-8.alert").css('display','none');
                                    $("div#mandatory-msg-9.alert").css('display','none');
                                    $("div#mandatory-msg-10.alert").css('display','none');
                                    $("div#mandatory-msg-11.alert").css('display','none');
                                    $("div#mandatory-msg-12.alert").css('display','none');
                                    $("div#mandatory-msg-13.alert").css('display','none');
                                    $("div#mandatory-msg-14.alert").css('display','none');
                                    $("div#mandatory-msg-15.alert").css('display','none');
                                    $("div#mandatory-msg-16.alert").css('display','block');
                                    $("div#mandatory-msg-17.alert").css('display','none');
                                    $("div#undefined-msg.alert").css('display','none');
                                }
                                else if($.trim(data) == 'Mandatory_17')
                                {
                                    $("div#mandatory-msg-1.alert").css('display','none');
                                    $("div#mandatory-msg-2.alert").css('display','none');
                                    $("div#mandatory-msg-3.alert").css('display','none');
                                    $("div#mandatory-msg-4.alert").css('display','none');
                                    $("div#mandatory-msg-5.alert").css('display','none');
                                    $("div#mandatory-msg-6.alert").css('display','none');
                                    $("div#mandatory-msg-7.alert").css('display','none');
                                    $("div#mandatory-msg-8.alert").css('display','none');
                                    $("div#mandatory-msg-9.alert").css('display','none');
                                    $("div#mandatory-msg-10.alert").css('display','none');
                                    $("div#mandatory-msg-11.alert").css('display','none');
                                    $("div#mandatory-msg-12.alert").css('display','none');
                                    $("div#mandatory-msg-13.alert").css('display','none');
                                    $("div#mandatory-msg-14.alert").css('display','none');
                                    $("div#mandatory-msg-15.alert").css('display','none');
                                    $("div#mandatory-msg-16.alert").css('display','none');
                                    $("div#mandatory-msg-17.alert").css('display','block');
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
                                    $("div#mandatory-msg-6.alert").css('display','none');
                                    $("div#mandatory-msg-7.alert").css('display','none');
                                    $("div#mandatory-msg-8.alert").css('display','none');
                                    $("div#mandatory-msg-9.alert").css('display','none');
                                    $("div#mandatory-msg-10.alert").css('display','none');
                                    $("div#mandatory-msg-11.alert").css('display','none');
                                    $("div#mandatory-msg-12.alert").css('display','none');
                                    $("div#mandatory-msg-13.alert").css('display','none');
                                    $("div#mandatory-msg-14.alert").css('display','none');
                                    $("div#mandatory-msg-15.alert").css('display','none');
                                    $("div#mandatory-msg-16.alert").css('display','none');
                                    $("div#mandatory-msg-17.alert").css('display','none');
                                    $("div#undefined-msg.alert").css('display','block');
                                }
                            }
                        });
                    }
                    else
                    {
                        $("div#mandatory-msg-13.alert").css('display','block');
                    }
                },
                "No": function(){
                    $(this).dialog("close");
                }
            }
        });
        //$("#dialog-confirm-save").dialog("open");
    });
    
	$("#btn-update-item").click(function(){
        /** Save confirmation */
        $("#dialog-confirm-update").html("Do you want update?");
        $("#dialog-confirm-update").dialog({
            //autoOpen    : false,
            height      : 155,
            width       : 400,
            //position    : {my: "center", at: "top", of: $("body"), within: $("body")},
            modal       : true,
            buttons     : {
                "Yes": function(){
                    $(this).dialog("close");
                    var edataItem;
                    var transferIdVal       = $("#transferIdSupplier").val();
                    var newProcInCharge     = $("#newProcInCharged").val();
                    var updateDateVal       = $("#updateDate").val();
                    var prNoVal             = $("#prNo").val();
                    
                    edataItem = "transferIdPrm="+transferIdVal+"&newProcInChargePrm="+newProcInCharge
                                +"&updateDatePrm="+updateDateVal;
                    $.ajax({
                        type: 'GET',
                        url: '../db/PR_to_PO/EPS_T_TRANSFER_SUPPLIER.php?action=UpdateItemInCharge',
                        data: edataItem,
                        success: function(data){
                            $("div#mandatory-msg-1.alert").css('display','none');
                            $("div#mandatory-msg-2.alert").css('display','none');
                            $("div#mandatory-msg-3.alert").css('display','none');
                            $("div#mandatory-msg-4.alert").css('display','none');
                            $("div#mandatory-msg-5.alert").css('display','none');
                            $("div#mandatory-msg-6.alert").css('display','none');
                            $("div#mandatory-msg-7.alert").css('display','none');
                            $("div#mandatory-msg-8.alert").css('display','none');
                            $("div#mandatory-msg-9.alert").css('display','none');
                            $("div#mandatory-msg-10.alert").css('display','none');
                            $("div#mandatory-msg-11.alert").css('display','none');
                            $("div#mandatory-msg-12.alert").css('display','none');
                            $("div#mandatory-msg-13.alert").css('display','none');
                            $("div#mandatory-msg-14.alert").css('display','none');
                            $("div#mandatory-msg-15.alert").css('display','none');
                            $("div#mandatory-msg-16.alert").css('display','none');
                            $("div#session-msg.alert").css('display','none');
                            $("div#undefined-msg.alert").css('display','none');
                            
                            if($.trim(data) == 'Success')
                            {
                                window.location = "WEPO004.php?prNoCriteria="+prNoVal;
                            }
                            else if($.trim(data) == 'Mandatory_16')
                            {
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#mandatory-msg-2.alert").css('display','none');
                                $("div#mandatory-msg-3.alert").css('display','none');
                                $("div#mandatory-msg-4.alert").css('display','none');
                                $("div#mandatory-msg-5.alert").css('display','none');
                                $("div#mandatory-msg-6.alert").css('display','none');
                                $("div#mandatory-msg-7.alert").css('display','none');
                                $("div#mandatory-msg-8.alert").css('display','none');
                                $("div#mandatory-msg-9.alert").css('display','none');
                                $("div#mandatory-msg-10.alert").css('display','none');
                                $("div#mandatory-msg-11.alert").css('display','none');
                                $("div#mandatory-msg-12.alert").css('display','none');
                                $("div#mandatory-msg-13.alert").css('display','none');
                                $("div#mandatory-msg-14.alert").css('display','none');
                                $("div#mandatory-msg-15.alert").css('display','none');
                                $("div#mandatory-msg-16.alert").css('display','block');
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
                                $("div#mandatory-msg-6.alert").css('display','none');
                                $("div#mandatory-msg-7.alert").css('display','none');
                                $("div#mandatory-msg-8.alert").css('display','none');
                                $("div#mandatory-msg-9.alert").css('display','none');
                                $("div#mandatory-msg-10.alert").css('display','none');
                                $("div#mandatory-msg-11.alert").css('display','none');
                                $("div#mandatory-msg-12.alert").css('display','none');
                                $("div#mandatory-msg-13.alert").css('display','none');
                                $("div#mandatory-msg-14.alert").css('display','none');
                                $("div#mandatory-msg-15.alert").css('display','none');
                                $("div#mandatory-msg-16.alert").css('display','none');
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
    });
	
    $("#btn-back").click(function(){
		var prNoVal         = $("#prNo").val();
        /** Back confirmation */
        $("#dialog-confirm-back").html("Do you want back to menu Outstanding PO?");
        $("#dialog-confirm-back").dialog({
            //autoOpen    : false,
            height      : 155,
            width       : 400,
            //position    : {my: "center", at: "top", of: $("body"), within: $("body")},
            modal       : true,
            buttons     : {
                "Yes": function(){
                    $(this).dialog("close");
                    window.location = "WEPO004.php?prNoCriteria="+prNoVal;
                },
                "No": function(){
                    $(this).dialog("close");
                }
            }
        });
        //$("#dialog-confirm-back").dialog("open");
    });
    
    $("table .btn-warning").click(function() {
        var rowCount = $("#supplierListTable > tbody >tr").length;
        if(rowCount < 8){
            $("div#dialog-mandatory-msg-1.alert").css('display','none');
            $("div#dialog-mandatory-msg-2.alert").css('display','none');
            $("div#dialog-mandatory-msg-3.alert").css('display','none');
            $("div#dialog-duplicate-msg.alert").css('display','none');
            $("div#dialog-undefined-msg.alert").css('display','none');
            
            $("#supplierCd").val('');
            $("#currencyCd").val('');
            $("#price").val('');
            $("#leadTime").val('');
            $("#unitTime").val('');
            $("#attachmentLoc").val('');
            $("#attachmentCip").val('');
            $("#remark").val('');
            $("#seqSupplier").val(rowCount);
            $('#dialog-form').dialog('option', 'title', 'Add Supplier');
            $("#dialog-form").dialog("open");
        }else{
            $("div#mandatory-msg-1.alert").css('display','none');
            $("div#mandatory-msg-2.alert").css('display','none');
            $("div#mandatory-msg-3.alert").css('display','none');
            $("div#mandatory-msg-4.alert").css('display','none');
            $("div#mandatory-msg-5.alert").css('display','none');
            $("div#mandatory-msg-6.alert").css('display','none');
            $("div#mandatory-msg-7.alert").css('display','none');
            $("div#mandatory-msg-8.alert").css('display','none');
            $("div#mandatory-msg-9.alert").css('display','none');
            $("div#mandatory-msg-10.alert").css('display','none');
            $("div#mandatory-msg-11.alert").css('display','none');
            $("div#mandatory-msg-12.alert").css('display','none');
            $("div#mandatory-msg-13.alert").css('display','none');
            $("div#mandatory-msg-14.alert").css('display','block');
            $("div#undefined-msg.alert").css('display','none');
        }
    });
    
    $("table .btn-success").click(function() {
        var transferId  = $.trim($(this).closest('tr').find('td:eq(2)').text());
        var supplierCd  = $.trim($(this).closest('tr').find('td:eq(3)').text());
        var currencyCd  = $.trim($(this).closest('tr').find('td:eq(5)').text());
        var itemPrice   = $.trim($(this).closest('tr').find('td:eq(6)').text());
        var leadTime    = $.trim($(this).closest('tr').find('td:eq(7)').text());
        var unitTime    = $.trim($(this).closest('tr').find('td:eq(8)').text());
        var attachmentLoc= $.trim($(this).closest('tr').find('td:eq(9)').text());
        var attachmentCip= $.trim($(this).closest('tr').find('td:eq(10)').text());
        var remark      = $.trim($(this).closest('tr').find('td:eq(11)').text());
        var seqSupplier = $.trim($(this).closest('tr').find('td:eq(12)').text());
        var itemNo      = $.trim($(this).closest('tr').find('td:eq(0)').text()).substr(0,1);
        
        $("div#dialog-mandatory-msg-1.alert").css('display','none');
        $("div#dialog-mandatory-msg-2.alert").css('display','none');
        $("div#dialog-mandatory-msg-3.alert").css('display','none');
        $("div#dialog-duplicate-msg.alert").css('display','none');
        $("div#dialog-undefined-msg.alert").css('display','none');
            
        $("#transferIdSupplier").val('');
        $("#supplierCd").val('');
        $("#currencyCd").val('');
        $("#price").val('');
        $("#leadTime").val('');
        $("#unitTime").val('');
        $("#attachmentLoc").val('');
        $("#attachmentCip").val('');
        $("#remark").val('');
        $("#seqSupplier").val('');
        $('#dialog-form').dialog('option', 'title', 'Edit Supplier');
        $("#dialog-form").dialog("open");
        
        $("#transferIdSupplier").val(transferId);
        $("#supplierCd").val(supplierCd);
        $("#currencyCd").val(currencyCd);
        $("#price").val(itemPrice);
        $("#leadTime").val(leadTime);
        $("#unitTime").val(unitTime);
        $("#attachmentLoc").val(attachmentLoc);
        $("#attachmentCip").val(attachmentCip);
        $("#remark").val(remark);
        $("#seqSupplier").val(itemNo);
    });
    
    $("table .btn-info").click(function() {
        var index = $(this).closest('tr').index();
        index = index + 1;
        var supplierCdVal   = $.trim($("table#supplierListTable.table.table-striped.table-bordered tbody tr#"+index+" td#getSupplierCd"+index).text());
        var supplierNameVal = $.trim($("table#supplierListTable.table.table-striped.table-bordered tbody tr#"+index+" td#getSupplierName"+index).text());
        var currencyCdVal   = $.trim($("table#supplierListTable.table.table-striped.table-bordered tbody tr#"+index+" td#getCurrencyCd"+index).text());
        var priceVal        = $.trim($("table#supplierListTable.table.table-striped.table-bordered tbody tr#"+index+" td#getPrice"+index).text());
        var leadTimeVal     = $.trim($("table#supplierListTable.table.table-striped.table-bordered tbody tr#"+index+" td#getLeadTime"+index).text());
        var unitTimeVal     = $.trim($("table#supplierListTable.table.table-striped.table-bordered tbody tr#"+index+" td#getUnitTime"+index).text());
        var attachmentLocVal= $.trim($("table#supplierListTable.table.table-striped.table-bordered tbody tr#"+index+" td#getAttachmentLoc"+index).text());
        var attachmentCipVal= $.trim($("table#supplierListTable.table.table-striped.table-bordered tbody tr#"+index+" td#getAttachmentCip"+index).text());
        var remarkVal       = $.trim($("table#supplierListTable.table.table-striped.table-bordered tbody tr#"+index+" td#getRemark"+index).text());
        
        $("#supplierCdSet").val(supplierCdVal);    
        $("#supplierNameSet").val(supplierNameVal); 
        $("#currencyCdSet").val(currencyCdVal); 
        $("#priceSet").val(priceVal); 
        $("#leadTimeSet").val(leadTimeVal); 
        $("#unitTimeSet").val(unitTimeVal); 
        $("#attachmentLocSet").val(attachmentLocVal);
        $("#attachmentCipSet").val(attachmentCipVal);
        $("#remarkSet").val(remarkVal); 
    });
    
    $("table .btn-danger").click(function() {
        var eDeleteData;
        var index       = $(this).closest('tr').index();
        var seqSupplier = $.trim(index);
        
        eDeleteData = "seqSupplierPrm="+seqSupplier;
        //alert(seqSupplier);
        $.ajax({
            type: 'GET',
            url: '../db/PR_to_PO/UPDATE_SUPPLIER_SESSION.php?action=DeleteSupplier',
            data: eDeleteData,
            success: function(data){
                alert(data);
                window.location.reload();
            }
        });
    });
    
    
    function isNumber(evt, element) {

        var charCode = (evt.which) ? evt.which : evt.keyCode

        if (
            //(charCode != 45 || $(element).val().indexOf('-') != -1) &&      // �-� CHECK MINUS, AND ONLY ONE.
            (charCode != 46 || $(element).val().indexOf('.') != -1) &&      // �.� CHECK DOT, AND ONLY ONE.
            (charCode < 48 || charCode > 57))
            return false;
        
        if($(element).val().indexOf('.') != -1)
            {
                if($(element).val().split(".")[1].length > 0){
                    return false;
                }
            }

        return true;
    }
});



