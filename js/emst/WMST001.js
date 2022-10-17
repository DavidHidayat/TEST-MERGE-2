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
    /** Dialog form */
    $("#dialog-form").dialog({
        autoOpen    : false,
        closeOnEscape   : false,
		height      : 370,
		width       : 550,
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
                var itemCdVal       = $.trim($("#itemCd-dialog").val());
                var itemNameVal     = $.trim($("#itemName-dialog").val());
                var itemGroupCdVal  = $.trim($("#itemGroupCd-dialog").val());
                var activeFlagVal   = $.trim($("#activeFlag-dialog").val());
                var action           = $.trim($("#dialog-form").dialog("option","title").substr(0,4));
                var objectAccount = $.trim($("#objectAccountCd-dialog").val());
                
                //alert(objectAccount)
                
                edata = "action="+encodeURIComponent(action)+"&itemCdPrm="
                        +encodeURIComponent(itemCdVal)+"&itemNamePrm="+encodeURIComponent(itemNameVal)
                        +"&itemGroupCdPrm="+encodeURIComponent(itemGroupCdVal)
                        +"&activeFlagPrm="+encodeURIComponent(activeFlagVal)
                        +"&objectAccountPrm="+encodeURIComponent(objectAccount);
                $.ajax({
                    type: 'GET',
                    url: '../db/MASTER/EPS_M_ITEM.php',
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
                            $("#itemCd").val(itemCdVal);
                            $("#WMST001Form").attr('action', 'WMST001.php');
                            $("#WMST001Form").submit();
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
        var itemCdVal       = $.trim($("#itemCd").val());
        var itemNameVal     = $.trim($("#itemName").val());
        var itemGroupCdVal  = $.trim($("#itemGroupCd").val());
        var activeFlagVal   = $.trim($("#activeFlag").val());
        
        if(itemCdVal != '' || itemNameVal != '' || itemGroupCdVal != '' || activeFlagVal != '')
        {
            $("div#mandatory-msg-1.alert").css('display','none');
            $("div#mandatory-msg-2.alert").css('display','none');
            $("#WMST001Form").attr('action', 'WMST001.php');
            $("#WMST001Form").submit();
        }
        else
        {
            $("div#mandatory-msg-1.alert").css('display','block');
            $("div#mandatory-msg-2.alert").css('display','none');
        }
    });
    
    $("#btn-reset").click(function() {
        $("#itemCd").val('');
        $("#itemName").val('');
        $("#itemGroupCd").val('');
        $("#activeFlag").val('');
        
        $("div#dialog-mandatory-msg-1.alert").css('display','none');
        $("div#dialog-duplicate-msg.alert").css('display','none');
        $("div#dialog-notexist-msg.alert").css('display','none');
        $("div#dialog-notallowedit-msg.alert").css('display','none');
        $("div#dialog-undefined-msg.alert").css('display','none');
		
        $("#WMST001Form").attr('action', 'WMST001.php');
        $("#WMST001Form").submit();
    });
    
    
    $("a#link-register.news-item-title").click(function() {
        $("#itemCd-dialog").val('');
        $("#itemName-dialog").val('');
        $("#objectAccountCd-dialog").val('');
        $("#itemGroupCd-dialog").val('');
        $("#activeFlag-dialog").val('');
        
        $("#itemCd-dialog").attr('readonly', false);
        
        $('#dialog-form').dialog('option', 'title', 'Add Item');
		
        $("#dialog-form").dialog("open");
    });
    
    //ambil data dari table untuk edit data
    $("table.table.table-striped.table-bordered tbody tr td a").click(function() {
        var itemCd      = $.trim($(this).closest('tr').find('td:eq(1)').text());
        var itemName    = $.trim($(this).closest('tr').find('td:eq(2)').text());
        var itemGroupCd = $.trim($(this).closest('tr').find('td:eq(3)').text());
        var objectAccount = $.trim($(this).closest('tr').find('td:eq(4)').text());
        var activeFlag  = $.trim($(this).closest('tr').find('td:eq(5)').text());
        
        
        $("div#dialog-mandatory-msg-1.alert").css('display','none');
        $("div#dialog-duplicate-msg.alert").css('display','none');
        $("div#dialog-notexist-msg.alert").css('display','none');
        $("div#dialog-notallowedit-msg.alert").css('display','none');
        $("div#dialog-undefined-msg.alert").css('display','none');
                        
        $('#dialog-form').dialog('option', 'title', 'Edit Item');
        
        $("#itemCd-dialog").val(itemCd);
        $("#itemName-dialog").val(itemName);
        $("#itemGroupCd-dialog").val(itemGroupCd);
        
        $("#activeFlag-dialog").val(activeFlag);
        $("#objectAccountCd-dialog").val(objectAccount);
        $("#itemCd-dialog").attr('readonly', true);
        
        $("#dialog-form").dialog("open");
    });
});
