$(document).ready(function() {
    /**
     * =========================================================================================================
     * INITIAL VALUE
     * =========================================================================================================
     **/
    $('#selectAll').click(function(event) {  //on click
        var countCheck = 1;
        var countCheckMax = 0;
        if(this.checked) { // check select status
            $('.selectItem').each(function() { //loop through each checkbox
               this.checked = true;  //select all checkboxes with class "checkbox1"  
               if(countCheck > 15)
                {
                    this.checked = false;  
                }
                countCheck++;
                
                
                if(this.checked == true)
                {
                    countCheckMax++;
                    $("#countCheck").val(countCheckMax);
                }
                    
            });
        }else{
            $('.selectItem').each(function() { //loop through each checkbox
                $("#countCheck").val('');
                this.checked = false; //deselect all checkboxes with class "checkbox1"                      
            });        
        }
    });
    
    $(".selectItem").change(function() {
        var countCheckCurrent = parseInt($("#countCheck").val());
        var countCheckDeduct = 0;
        var countCheckAdd = 0;
        var ischecked= $(this).is(':checked');
        
        if(!ischecked)
        {
            if($.trim(countCheckCurrent) == '' || isNaN(countCheckCurrent))
            {
                countCheckCurrent = 0;
            }
            if(countCheckCurrent > 0)
            {
                countCheckDeduct = countCheckCurrent - 1;
            }
            if(countCheckCurrent < 16) 
            {
                 $("#countCheck").val(countCheckDeduct);
            }
            else
            {
                this.checked = false;  
            }
        }
            
        if(ischecked)
        {
            if($.trim(countCheckCurrent) == '' || isNaN(countCheckCurrent))
            {
                countCheckCurrent = 0;
            }
            countCheckAdd = countCheckCurrent + 1;
            
            if(countCheckAdd < 16)
            {        
                $("#countCheck").val(countCheckAdd);
            }
            else
            {
                this.checked = false;  
            }
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
            var userIdApprover = $("userIdApprover").val();
            
            $("table#poListTable.table.table-striped.table-bordered tbody tr").filter(":has(:checkbox:checked)").each(function(){
                var index = this.id;
                poNo = $.trim($("table#poListTable.table.table-striped.table-bordered tbody tr#"+index+" td#getPoNo"+index).text());
                recs.push([poNo]);
            });
            eDataPo = "poNoArray="+recs+"&userIdApproverPrm="+userIdApprover;
            if(recs.length > 0)
            {
                if(recs.length <= 15)
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
	$("table .btn-info").click(function() {
        var refTransferId = $.trim($(this).closest('tr').find('td:eq(0)').text());
        var edataRefSupplier;
        edataRefSupplier = "refTransferIdPrm="+refTransferId;
        $.ajax({
            type: 'GET',
            url: '../db/GET_TABLE/EPS_T_TRANSFER_SUPPLIER.php',
            data: edataRefSupplier,
            success: function(data){
                //alert($.trim(data));
                
                /** Dialog reference supplier table */
                $("#dialog-refsupplier-table").dialog({
                    //autoOpen        : false,
                    closeOnEscape   : false,
                    height          : 400,
                    width           : 850,
                    position        : {my: "center", at: "center", of: $("body"), within: $("body")},
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
                $("#dialog-control-group-refsupplier").append($.trim(data));
            }
        });
        //$("#dialog-refsupplier-table").dialog("open");
    });
});

