$(document).ready(function() {
    /**
     * =========================================================================================================
     * INITIAL VALUE
     * =========================================================================================================
     **/
    $('#selectAll').click(function(event) {  //on click
        if(this.checked) { // check select status
            $('.selectItem').each(function() { //loop through each checkbox
                this.checked = true;  //select all checkboxes with class "checkbox1"              
            });
        }else{
            $('.selectItem').each(function() { //loop through each checkbox
                this.checked = false; //deselect all checkboxes with class "checkbox1"                      
            });        
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
    $("#btn-generate").click(function(){
        var itemCount = $('#itemPOListTable').find("input[type='checkbox']:checked").length;
        if(itemCount == 0)
        {
            $("div#mandatory-msg-1.alert").css('display','block');
            $("div#undefined-msg.alert").css('display','none');
            $("div#success-msg.alert-success").css('display','none');
        }
        else
        {
			$("#btn-generate").attr('disabled','disabled');
            var transferId;
            var recs = [];
            var eDataItem;
            
            $("table#itemPOListTable.table.table-striped.table-bordered tbody tr").filter(":has(:checkbox:checked)").each(function(){
                var index = this.id;
                transferId = $.trim($("table#itemPOListTable.table.table-striped.table-bordered tbody tr#"+index+" td#getTransferId"+index).text());
                recs.push([transferId]);
            });
            
            eDataItem = "transferIdArray="+recs;
            
            $.ajax({
               type: 'GET',
               url: '../db/PR_to_PO/EPS_T_TRANSFER.php?action=GeneratePoNumber',
               data: eDataItem,
               success: function(data){
                   if($.trim(data) == 'Success'){
                        $("div#mandatory-msg-1.alert").css('display','none');
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
        }
    });
});


