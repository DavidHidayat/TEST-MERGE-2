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
    
    $("button#btn-upload.btn.btn-primary").click(function() {
        var uploadFileVal      = $.trim($("#uploadFile").val());
        var mstTypeVal         = $.trim($("#mstType").val());
        var actionTypeVal       = $.trim($("#actionType").val());
        
        if(uploadFileVal != '' || mstTypeVal != '' || actionTypeVal != '')
        {
            $("div#mandatory-msg-1.alert").css('display','none');
            $("div#mandatory-msg-2.alert").css('display','none');
            //$("#WMST009Form").attr('action', 'WMST009.php');
            //$("#WMST009Form").submit();
        }
        else
        {
            $("div#mandatory-msg-1.alert").css('display','block');
            $("div#mandatory-msg-2.alert").css('display','none');
        }
    });
     **/
    $("#effectiveDateFrom, #effectiveDateEnd").datepicker({
        dateFormat: 'dd/mm/yy',
        //defaultDate: "+1w",
        autoClose: true,
        beforeShowDay: $.datepicker.noWeekends
    });

    $("#btn-reset").click(function() {
        $("#uploadFile").val('');
        $("#mstType").val('');
        $("#actionType").val('');
        $("div#mandatory-msg-1.alert").css('display','none');
        $("div#mandatory-msg-2.alert").css('display','none');
        $("#WMST009Form").attr('action', 'WMST009.php');
        $("#WMST009Form").submit();
    });
    $("#btn-save").click(function (e) { 
        let formData = new FormData();
        $('.table input, .table select').each(function (index, element) {
            formData.append(this.name, $(this).val());
        });
        if (formData.get('itemCd') == '99') {
            $('#dialog-alert').removeClass('alert-success');
            $('#dialog-alert').html(`<strong>Mandatory!</strong> Can't use item code 99`).show();
        }
        if (formData.get('supplierCd')=='SUP99') {
            $('#dialog-alert').removeClass('alert-success');
            $('#dialog-alert').html(`<strong>Mandatory!</strong> Can't use supplier code SUP99`).show();
        }
        $.ajax({
            method: "POST",
            url: "../db/MASTER/EPS_M_ITEM_PRICE.php?action=Add",
            data: formData,
            dataType: "json",
            processData: false,
            contentType: false,
            success: function (response) {
                console.log(response);
                $('#dialog-alert').removeClass('alert-success');
                if (response == 'Success') {
                    $("input").val("");
                    $('#dialog-alert').addClass('alert-success');
                    $('#dialog-alert').html(`<strong>Success</strong> Add price for '${formData.get('itemCd')}'`).show();
                } else if (response =='Undefined') {
                    $('#dialog-alert').html(`<strong>Undefined Error!</strong> System Error occurs. Please report to system administrator.`).show();
                } else if (response =='Mandatory_1') {
                    $('#dialog-alert').html(`<strong>Mandatory!</strong> Please fill all the field.`).show();
                } else if (response =='NotExist') {
                    $('#dialog-alert').html(`<strong>Existence Error!</strong> Item code does not exist in master data.`).show();
                } else{
                    $('#dialog-alert').html(`<strong>Undefined Error!</strong> System Error occurs. Please report to system administrator.`).show();
                }
            }
        });
        e.preventDefault();
        
    });
});
