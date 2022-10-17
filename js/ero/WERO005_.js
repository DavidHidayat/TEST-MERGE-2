$(document).ready(function() {
    /**
     * =========================================================================================================
     * INITIAL VALUE
     * =========================================================================================================
     **/
    var getCountItemVal = $("#countItem").val();
    
    for(var u = 0; u < getCountItemVal; u++){
        $("select#roStatus"+(u+1)+" option[value='"+$("input#setRoStatus"+(u+1)+".span2").val()+"']").prop("selected", true);
    } 
    
    /**
     * =========================================================================================================
     * DEFINE DIALOG
     * =========================================================================================================
     **/
    
    /**
     * =========================================================================================================
     * BUTTON FUNCTION
     * =========================================================================================================
     **/
    $("#btn-save").click(function(){
        /** Save confirmation */
        $("#dialog-confirm-save").html("Do you want save?");
        $("#dialog-confirm-save").dialog({
            //autoOpen    : false,
            height      : 155,
            width       : 400,
            //position    : { my: "center", at: "top", of: $("body"), within: $("body") },
            modal       : true,
            buttons     : {
                "Yes": function(){
                    $(this).dialog("close");
                    var poNo        = $.trim($("#poNo").val());
                    var countItem   = $("#countItem").val();
                    var itemNo      = 1;
                    var roDetail    = new Array();
                    var eDataPo;

                    for(var i=0; i < countItem; i++){
                        var index = i+1;
                        var roStatusItem    = $("select#roStatus"+itemNo+".span2").val();
						var openRemark		= $("input#setOpenRemark"+itemNo+".span3").val();
                        var refTransferId   = $.trim($("table#poListTable.table.table-striped.table-bordered tbody tr#"+index+" td#getRefTransferId"+index).text());
                        roDetail.push([poNo+roStatusItem+refTransferId]);
                        itemNo++;
                    }
                    eDataPo = "poNoPrm="+poNo+"&roDetailArray="+roDetail;
                    /*$.ajax({
                        type: 'GET',
                        url: '../db/RO/UPDATE_RO.php',
                        data: eDataPo,
                        success: function(data){
                            alert(data);
                            if($.trim(data) == 'Success'){
                                $("div#undefined-msg.alert").css('display','none');
                                $("div#session-msg.alert").css('display','none');
                                $("div#save-msg.alert").css('display','block');

                                $("button#btn-save.btn.btn-primary").attr('disabled', true);

                                $("select.span2").attr('disabled', true);
                            }
                            else if($.trim(data) == 'SessionExpired')
                            {
                                $("div#undefined-msg.alert").css('display','none');
                                $("div#session-msg.alert").css('display','block');
                                $("div#save-msg.alert").css('display','none');
                            }
                            else
                            {
                                $("div#undefined-msg.alert").css('display','block');
                                $("div#session-msg.alert").css('display','none');
                                $("div#save-msg.alert").css('display','none');   
                            }
                        }
                    });*/
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
        $("#dialog-confirm-back").html("Do you want back to Closed Delivery list?");
        $("#dialog-confirm-back").dialog({
            //autoOpen    : false,
            height      : 155,
            width       : 400,
            //position    : {my: "center", at: "top", of: $("body"), within: $("body")},
            modal       : true,
            buttons     : {
                "Yes": function(){
                    $(this).dialog("close");
                    window.location = "WERO003.php";
                },
                "No": function(){
                    $(this).dialog("close");
                }
            }
        });
        //$("#dialog-confirm-back").dialog("open");
    });
    
    $("table .btn-inverse").click(function() {
        var poNo            = $.trim($(this).closest('tr').find('td:eq(2)').text());
        var refTransferId   = $.trim($(this).closest('tr').find('td:eq(3)').text());
        var edataReceiving;
        edataReceiving = "poNoPrm="+poNo+"&refTransferIdPrm="+refTransferId;
        
        $.ajax({
            type: 'GET',
            url: '../db/GET_TABLE/EPS_T_RO_DETAIL.php?criteria=receivingDetail',
            data: edataReceiving,
            success: function(data){
                //alert($.trim(data));
                $("#dialog-control-group-receiving").append($.trim(data));
            }
        });
        
        /** Dialog receiving table */
        $("#dialog-receiving-table").dialog({
            //autoOpen        : false,
            closeOnEscape   : false,
            height          : 350,
            width           : 550,
            //position        : { my: "center", at: "top", of: $("body"), within: $("body") },
            modal           : true,
            open            : function() {                         // open event handler
                $(this)                                // the element being dialogged
                    .parent()                          // get the dialog widget element
                    .find(".ui-dialog-titlebar-close") // find the close button for this dialog
                    .hide();                           // hide it
            },
            buttons     : {
                "Close"  : function(){
                    $("#table-receiving-item").remove();
                    $("#dialog-receiving-table").dialog("close");
                }
            }
        });
        //$("#dialog-receiving-table").dialog("open");
    });
});


