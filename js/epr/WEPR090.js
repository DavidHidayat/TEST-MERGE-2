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
    
    /**
     * =========================================================================================================
     * DEFINE DIALOG
     * =========================================================================================================
     **/
    
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
        var prNoVal             = $("#prNo").val();
        var prDateVal           = $.trim($("#prDate").val());
        var prDateEndVal        = $.trim($("#prDateEnd").val());
        var requesterVal        = $.trim($("#requester").val());
        var deliveryDateVal     = $.trim($("#deliveryDate").val());
        var prChargedVal        = $.trim($("#prCharged").val());
        var supplierCdVal       = $.trim($("#supplierCd").val());
        var supplierNameVal     = $.trim($("#supplierName").val());
        var itemNameVal         = $.trim($("#itemName").val());
        var itemStatusVal       = $.trim($("#itemStatus").val());
        var itemTypeVal         = $.trim($("#itemType").val());
        var expNoVal            = $.trim($("#expNo").val());
        var invNoVal            = $.trim($("#invNo").val());
        var rfiNoVal            = $.trim($("#rfiNo").val());
        var prStatusVal         = $.trim($("#prStatus").val());
        var roStatusVal         = $.trim($("#roSts").val());
        var itemCodeVal         = $.trim($("#itemCode").val());
        
        if(prNoVal != '' ||  prDateVal != '' ||  prDateEndVal != '' || requesterVal != ''
            || deliveryDateVal != '' || prChargedVal != '' || supplierCdVal != '' || supplierNameVal != ''
            || itemNameVal != '' || itemStatusVal != ''
            || itemTypeVal != '' || expNoVal != '' || invNoVal != '' || rfiNoVal != '' || prStatusVal != ''
            || roStatusVal != '' || itemCodeVal!= '')
        {
            $("#WEPR090Form").attr('action', 'WEPR090.php');
            $("#WEPR090Form").submit();
        }
        else if(prNoVal == '' ||  prDateVal == '' ||  prDateEndVal == '' || requesterVal == ''
            || deliveryDateVal == '' || prChargedVal == '' || supplierCdVal == ''
            || itemNameVal == '' || itemStatusVal == ''
            || itemTypeVal == '' || expNoVal == '' || invNoVal == '' || rfiNoVal == '' || prStatusVal== ''
            || roStatusVal == '' || itemCodeVal!= '')
        {
            $("div#mandatory-msg-1.alert").css('display','block');
        }
        else
        {
            $("div#undefined-msg.alert").css('display','block');
        }
    });
    
    $("#btn-reset").click(function() {
        $("#prNo").val('');
        $("#prDate").val('');
        $("#prDateEnd").val('');
        $("#requester").val('');
        $("#deliveryDate").val('');
        $("#prCharged").val('');
        $("#supplierCd").val('');
        $("#supplierName").val('');
        $("#itemName").val('');
        $("#itemStatus").val('');
        $("#itemType").val('');
        $("#expNo").val('');
        $("#invNo").val('');
        $("#rfiNo").val('');
        $("#prStatus").val('');
        $("#roSts").val('');
        $("#itemCode").val('');
    });
    
    $("a#window-po.btn.btn-facebook-alt").click(function() {
        var refTransferId = $.trim($(this).closest('tr').find('td:eq(1)').text());
        var edataRefSupplier;
        edataRefSupplier = "refTransferIdPrm="+refTransferId;
        
        $.ajax({
            type: 'GET',
            url: '../db/GET_TABLE/EPS_T_PO.php?criteria=PoDetail',
            data: edataRefSupplier,
            success: function(data){
                $("#dialog-control-group-po").append($.trim(data));
            }
        });
        
        $("#dialog-po-table").dialog({
            //autoOpen        : false,
            closeOnEscape   : false,
            height          : 550,
            width           : 970,
            //position        : {my: "center", at: "top", of: $("body"), within: $("body")},
            modal           : true,
            open            : function() {                         // open event handler
                $(this)                                // the element being dialogged
                    .parent()                          // get the dialog widget element
                    .find(".ui-dialog-titlebar-close") // find the close button for this dialog
                    .hide();                           // hide it
            },
            buttons     : {
                "Close"  : function(){
                    $("#table-po").remove();
                    $("#dialog-po-table").dialog("close");
                }
            }
        });
    });
    $("td a.faq-list.pr-no").click(function() {
        var prNo = $.trim($(this).closest('tr').find('td:eq(3)').text());
        var itemName = $.trim($(this).closest('tr').find('td:eq(7)').text());
        var edataRefSupplier;
        
        edataRefSupplier = "prNoPrm="+prNo+"&itemNamePrm="+encodeURIComponent(itemName);
        $.ajax({
            type: 'GET',
            url: '../db/GET_TABLE/EPS_T_PR.php?criteria=PrDetailByItemName',
            data: edataRefSupplier,
            success: function(data){
                //alert($.trim(data));
                $("#dialog-control-group-pritem").append($.trim(data));
            }
        });
        $("#dialog-pritem-table").dialog("open");
    });
});
