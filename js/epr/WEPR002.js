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
    
    /**
     * =========================================================================================================
     * BUTTON FUNCTION
     * =========================================================================================================
     **/
    $('button[name=btn-save-send]').click(function() {
        var actionBtn = $(this).text();
        var id = $.trim($(this).attr("id")).substr(4,8);
        
        /** Save confirmation */
        $("#dialog-confirm-save-send").html("Do you want "+actionBtn+" ?");
        $("#dialog-confirm-save-send").dialog({
            //autoOpen    : false,
            height      : 155,
            width       : 400,
            //position    : {my: "center", at: "top", of: $("body"), within: $("body")},
            modal       : true,
            buttons     : {
                "Yes": function(){
                    $(this).dialog("close");
                    var edata;
                    var userIdHiddenVal             = $("#userIdLoginHidden").val();
                    var buLoginHiddenVal            = $("#buLoginHidden").val();
                    var npkHiddenVal                = $("#npkHidden").val();
                    var buCdHiddenVal               = $("#buCdHidden").val();
                    var sectionCdHiddenVal          = $("#sectionCdHidden").val();
                    var companyCdHiddenVal          = $("#companyCdHidden").val();
                    var plantCdHiddenVal            = $("#plantCdHidden").val();
                    var prNoVal                     = $("#prNo").val();
                    var prDateVal                   = $("#prDate").val();
                    var niceNetVal                  = $.trim($("#niceNet").val());
                    var issuerBuVal                 = $("#issuerBu").val().substr(0,5);
                    var chargedBuVal                = $("#chargedBu").val();
                    var specialTypeIdVal            = $("#specialTypeId").val();
                    var purposeVal                  = $.trim($("#purpose").val());
                    var countAppDeptVal             = $("#countAppDept").val();
                    var errorRemarkBypassDept       = 0;
                    var errorAppDept                = 0;
                    var approverDept                = [];
                    var actionBtn                   = $.trim(id);
                    var actionForm                  = "CREATE";
                    
                    for(var i = 0; i < countAppDeptVal; i++)
                    {
                        /** Check if approver dept blank value */
                        if($("select#approverDept"+(i+1)+".form-control").val() == ""){
                            errorAppDept++;
                        }
                        /** Check if approver remark bypass blank value */
                        if($("input#setBypassNoDept"+(i+1)).prop('checked') == true
                            && $.trim($("input#setRemarkBypassDept"+(i+1)+".form-control").val()) == ''){
                            errorRemarkBypassDept++;
                        }
                    }  
                    
                    if(countAppDeptVal > 0 && errorRemarkBypassDept == 0 && errorAppDept == 0)
                    {
                       /****************
                        * Dept Approval
                        ****************/
                        for(var j = 0; j < countAppDeptVal; j++){
                            /** Get approver dept selectbox value */
                            var appNoDept       = j+1;
                            var appNpkDept      = $("select#approverDept"+(appNoDept)+".form-control").val();
                            var remarkBypassDept= $.trim($("input#setRemarkBypassDept"+(appNoDept)+".form-control").val());
                            var approverDeptVal = appNoDept+appNpkDept+remarkBypassDept;
                            approverDept.push([approverDeptVal]);  
                        }
                        
                        /****************
                         * IT Approval
                         ****************/
                        var approverIT = "";
                        if(specialTypeIdVal == "IT")
                        {
                            approverIT =  $("select#approverIT.form-control").val();
                        }
                        
                        edata = "userIdHiddenPrm="+userIdHiddenVal+"&npkHiddenPrm="+npkHiddenVal
                                    +"&buCdHiddenPrm="+buCdHiddenVal+"&sectionCdHiddenPrm="+sectionCdHiddenVal
                                    +"&companyCdHiddenPrm="+companyCdHiddenVal+"&plantCdHiddenPrm="+plantCdHiddenVal
                                    +"&prNoPrm="+prNoVal+"&prDatePrm="+prDateVal+"&niceNetPrm="+niceNetVal
                                    +"&issuerBuPrm="+issuerBuVal+"&chargedBuPrm="+chargedBuVal
                                    +"&specialTypeIdPrm="+specialTypeIdVal+"&purposePrm="+encodeURIComponent(purposeVal)
                                    +"&approverDeptArrPrm="+encodeURIComponent(approverDept)
                                    +"&approverITPrm="+approverIT
                                    +"&actionBtnPrm="+actionBtn+"&actionFormPrm="+actionForm;
                    
                        $.ajax({
                            type: 'GET',
                            url: '../db/PR_/CREATE_PR.php',
                            data: edata,
                            success: function(data)
                            {
                                if($.trim(data) == "Success-Save" || $.trim(data) == "Success-Send")
                                {
                                    $("div#mandatory-msg-1.alert.alert-danger").css('display','none');
                                    $("div#mandatory-msg-2.alert.alert-danger").css('display','none');
                                    $("div#mandatory-msg-3.alert.alert-danger").css('display','none');
                                    $("div#mandatory-msg-4.alert.alert-danger").css('display','none');
                                    $("div#mandatory-msg-5.alert.alert-danger").css('display','none');
                                    $("div#mandatory-msg-6.alert.alert-danger").css('display','none');
                                    $("div#mandatory-msg-7.alert.alert-danger").css('display','none');
                                    $("div#mandatory-msg-8.alert.alert-danger").css('display','none');
                                    $("div#undefined-msg.alert.alert-danger").css('display','none');
                                    if($.trim(data) == "Success-Save")
                                    {
                                        $("div#save-msg.alert.alert-success").css('display','block');
                                    }
                                    if($.trim(data) == "Success-Send")
                                    {
                                        $("div#send-msg.alert.alert-success").css('display','block');
                                    }
                                    $("input[type = 'text'], textarea").attr('readonly', true);
                                    $("select").attr('disabled', true);
                                    $("input[type = 'radio']").attr('disabled', true);
                                    $("input[type = 'checkbox']").attr('disabled', true);
                                    $("input[type = 'file']").attr('disabled', true);
                                    $("tr#tr-first-item").css('display','none');
                                    $("tr#tr-first-attachment").css('display','none');
                                    $('button[name=btn-save-send]').attr('disabled', true);
									
                                    var count = 2;
                                    var countdown = setInterval(function(){
                                        $("p.countdown").html(count + " seconds remaining!");
                                        if (count == 0) {
                                        clearInterval(countdown);
                                        window.open('WEPR001.php', "_self");

                                        }
                                        count--;
                                    }, 1000);
                                }
                                else if($.trim(data) == "Mandatory_1")
                                {
                                    $("div#mandatory-msg-1.alert.alert-danger").css('display','block');
                                    $("div#mandatory-msg-2.alert.alert-danger").css('display','none');
                                    $("div#mandatory-msg-3.alert.alert-danger").css('display','none');
                                    $("div#mandatory-msg-4.alert.alert-danger").css('display','none');
                                    $("div#mandatory-msg-5.alert.alert-danger").css('display','none');
                                    $("div#mandatory-msg-6.alert.alert-danger").css('display','none');
                                    $("div#mandatory-msg-7.alert.alert-danger").css('display','none');
                                    $("div#mandatory-msg-8.alert.alert-danger").css('display','none');
                                    $("div#undefined-msg.alert.alert-danger").css('display','none');
                                }
                                else if($.trim(data) == "Mandatory_5")
                                {
                                    $("div#mandatory-msg-1.alert.alert-danger").css('display','none');
                                    $("div#mandatory-msg-2.alert.alert-danger").css('display','none');
                                    $("div#mandatory-msg-3.alert.alert-danger").css('display','none');
                                    $("div#mandatory-msg-4.alert.alert-danger").css('display','none');
                                    $("div#mandatory-msg-5.alert.alert-danger").css('display','block');
                                    $("div#mandatory-msg-6.alert.alert-danger").css('display','none');
                                    $("div#mandatory-msg-7.alert.alert-danger").css('display','none');
                                    $("div#mandatory-msg-8.alert.alert-danger").css('display','none');
                                    $("div#undefined-msg.alert.alert-danger").css('display','none');
                                }
                                else if($.trim(data) == "Mandatory_6")
                                {
                                    $("div#mandatory-msg-1.alert.alert-danger").css('display','none');
                                    $("div#mandatory-msg-2.alert.alert-danger").css('display','none');
                                    $("div#mandatory-msg-3.alert.alert-danger").css('display','none');
                                    $("div#mandatory-msg-4.alert.alert-danger").css('display','none');
                                    $("div#mandatory-msg-5.alert.alert-danger").css('display','none');
                                    $("div#mandatory-msg-6.alert.alert-danger").css('display','block');
                                    $("div#mandatory-msg-7.alert.alert-danger").css('display','none');
                                    $("div#mandatory-msg-8.alert.alert-danger").css('display','none');
                                    $("div#undefined-msg.alert.alert-danger").css('display','none');
                                }
								else if($.trim(data) == "Mandatory_7")
                                {
                                    $("div#mandatory-msg-1.alert.alert-danger").css('display','none');
                                    $("div#mandatory-msg-2.alert.alert-danger").css('display','none');
                                    $("div#mandatory-msg-3.alert.alert-danger").css('display','none');
                                    $("div#mandatory-msg-4.alert.alert-danger").css('display','none');
                                    $("div#mandatory-msg-5.alert.alert-danger").css('display','none');
                                    $("div#mandatory-msg-6.alert.alert-danger").css('display','none');
                                    $("div#mandatory-msg-7.alert.alert-info").css('display','block');
                                    $("div#mandatory-msg-8.alert.alert-danger").css('display','none');
                                    $("div#undefined-msg.alert.alert-danger").css('display','none');
                                    
                                    var eDataPr;
                                    eDataPr = "userId="+userIdHiddenVal+"&buLogin="+buLoginHiddenVal+"&action=getCurrentPrNo";
                                    $.ajax({
                                        type: 'GET',
                                        url: '../db/PR/EPS_T_PR_SEQUENCE.php',
                                        data: eDataPr,
                                        success: function(data){
                                            var newPrNo = $.trim(data);
                                            $("#prNo").val(newPrNo);
                                        }
                                    });
                                }
                                else if($.trim(data) == "Mandatory_8")
                                {
                                    $("div#mandatory-msg-1.alert.alert-danger").css('display','none');
                                    $("div#mandatory-msg-2.alert.alert-danger").css('display','none');
                                    $("div#mandatory-msg-3.alert.alert-danger").css('display','none');
                                    $("div#mandatory-msg-4.alert.alert-danger").css('display','none');
                                    $("div#mandatory-msg-5.alert.alert-danger").css('display','none');
                                    $("div#mandatory-msg-6.alert.alert-danger").css('display','none');
                                    $("div#mandatory-msg-7.alert.alert-danger").css('display','none');
                                    $("div#mandatory-msg-8.alert.alert-danger").css('display','block');
                                    $("div#undefined-msg.alert.alert-danger").css('display','none');
                                }
                                else if($.trim(data) == "SessionExpired")
                                {
                                    window.location = "../ecom/WCOM010.php";
                                }
                                else
                                {
                                    $("div#mandatory-msg-1.alert.alert-danger").css('display','none');
                                    $("div#mandatory-msg-2.alert.alert-danger").css('display','none');
                                    $("div#mandatory-msg-3.alert.alert-danger").css('display','none');
                                    $("div#mandatory-msg-4.alert.alert-danger").css('display','none');
                                    $("div#mandatory-msg-5.alert.alert-danger").css('display','none');
                                    $("div#mandatory-msg-6.alert.alert-danger").css('display','none');
                                    $("div#mandatory-msg-7.alert.alert-danger").css('display','none');
                                    $("div#mandatory-msg-8.alert.alert-danger").css('display','none');
                                    $("div#undefined-msg.alert.alert-danger").css('display','block');
                                }  
                            }
                        });
                    }
                    else if(countAppDeptVal == '' || countAppDeptVal == 0)
                    {
                        $("div#mandatory-msg-1.alert.alert-danger").css('display','none');
                        $("div#mandatory-msg-2.alert.alert-danger").css('display','block');
                        $("div#mandatory-msg-3.alert.alert-danger").css('display','none');
                        $("div#mandatory-msg-4.alert.alert-danger").css('display','none');
                        $("div#mandatory-msg-5.alert.alert-danger").css('display','none');
                        $("div#undefined-msg.alert.alert-danger").css('display','none');
                    }
                    else if(errorAppDept > 0)
                    {
                        $("div#mandatory-msg-1.alert.alert-danger").css('display','none');
                        $("div#mandatory-msg-2.alert.alert-danger").css('display','none');
                        $("div#mandatory-msg-3.alert.alert-danger").css('display','block');
                        $("div#mandatory-msg-4.alert.alert-danger").css('display','none');
                        $("div#mandatory-msg-5.alert.alert-danger").css('display','none');
                        $("div#undefined-msg.alert.alert-danger").css('display','none');
                    }
                    else if(errorRemarkBypassDept > 0)
                    {
                        $("div#mandatory-msg-1.alert.alert-danger").css('display','none');
                        $("div#mandatory-msg-2.alert.alert-danger").css('display','none');
                        $("div#mandatory-msg-3.alert.alert-danger").css('display','none');
                        $("div#mandatory-msg-4.alert.alert-danger").css('display','block');
                        $("div#mandatory-msg-5.alert.alert-danger").css('display','none');
                        $("div#undefined-msg.alert.alert-danger").css('display','none');
                    }
                    else
                    {
                        $("div#mandatory-msg-1.alert.alert-danger").css('display','none');
                        $("div#mandatory-msg-2.alert.alert-danger").css('display','none');
                        $("div#mandatory-msg-3.alert.alert-danger").css('display','none');
                        $("div#mandatory-msg-4.alert.alert-danger").css('display','none');
                        $("div#mandatory-msg-5.alert.alert-danger").css('display','none');
                        $("div#undefined-msg.alert.alert-danger").css('display','block');
                    }
                        
                }
                ,"No": function(){
                    $(this).dialog("close");
                }
            }
        });
    });
});


