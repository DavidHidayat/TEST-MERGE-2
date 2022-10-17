$(document).ready(function() {
    /**
     * =========================================================================================================
     * INITIAL VALUE
     * =========================================================================================================
     **/
    
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
    $("#btn-approve").click(function(){
        var poCount = $('#poListTable').find("input[type='checkbox']:checked").length;
        if(poCount == 0)
        {
            $("div#mandatory-msg-1.alert").css('display','block');
            $("div#undefined-msg.alert").css('display','none');
            $("div#success-msg.alert-success").css('display','none');
        }
        else
        {
            var poNo;
            var recs = [];
            var eDataPo;
            
            $("table#poListTable.table.table-striped.table-bordered tbody tr").filter(":has(:checkbox:checked)").each(function(){
                var index = this.id;
                poNo = $.trim($("table#poListTable.table.table-striped.table-bordered tbody tr#"+index+" td#getPoNo"+index).text());
                recs.push([poNo]);
            });
            eDataPo = "poNoArray="+recs;
            
            if(recs.length > 0)
            {
                if(recs.length <= 10)
                {
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
                                $.ajax({
                                    type: 'GET',
                                    url: '../db/PO/UPDATE_PO.php?action=ApprovePoArray',
                                    data: eDataPo,
                                    success: function(data)
                                    {
                                        if($.trim(data) == 'Success')
                                        {
                                            $("div#mandatory-msg-1.alert").css('display','none');
                                            $("div#mandatory-msg-2.alert").css('display','none');
                                            $("div#undefined-msg.alert").css('display','none');
                                            $("div#success-msg.alert-success").css('display','block');
                                            window.location.reload();
                                        }
                                        else if($.trim(data) == 'SessionExpired')
                                        {
                                            $("#dialog-confirm-session").dialog('open');
                                        }
                                        else
                                        {
                                            $("div#mandatory-msg-1.alert").css('display','none');
                                            $("div#undefined-msg.alert").css('display','block');
                                            $("div#success-msg.alert-success").css('display','none');
                                        }
                                    }
                                });
                            },
                            "No": function(){
                                $(this).dialog("close");
                                $("div#mandatory-msg-1.alert").css('display','none');
                                $("div#undefined-msg.alert").css('display','none');
                                $("div#success-msg.alert-success").css('display','none');
                            }
                        }
                    });
                    
                }
                else
                {
                    $("div#mandatory-msg-1.alert").css('display','none');
                    $("div#mandatory-msg-2.alert").css('display','block');
                }
            }
            else if(recs.length == 0)
            {
                $("div#mandatory-msg-1.alert").css('display','block');
                $("div#undefined-msg.alert").css('display','none');
                $("div#success-msg.alert-success").css('display','none');
            }
            else
            {
                $("div#mandatory-msg-1.alert").css('display','none');
                $("div#undefined-msg.alert").css('display','block');
                $("div#success-msg.alert-success").css('display','none');
            }
        }
    });
});

