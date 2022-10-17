$(document).ready(function() {
    /**
     * =========================================================================================================
     * INITIAL VALUE
     * =========================================================================================================
     **/
    var fullDate = new Date();
    //Thu May 19 2011 17:25:38 GMT+1000 {}

    //convert month to 2 digits
    var twoDigitMonth = ((fullDate.getMonth().length+1) === 1)? (fullDate.getMonth()+1) : '0' + (fullDate.getMonth()+1);
    //var twoDigitDate = ((fullDate.getDate().length) === 1)? (fullDate.getDate()) : '0' + (fullDate.getDate());
    var currentDate = fullDate.getDate() + "/" + twoDigitMonth + "/" + fullDate.getFullYear();
        
    //$("#sendPoDate").val(currentDate);
     
    /**
     * =========================================================================================================
     * BUTTON FUNCTION
     * =========================================================================================================
     **/
    $("#btn-search").click(function(){
        $("#WEPO013Form").attr('action', 'WEPO013.php');
        $("#WEPO013Form").submit();
    });
    $("#btn-reset").click(function(){
        $("#poNo").val('');
        $("#sendPoDate").val('');
    });
    
    $("#btn-send-po-mail").click(function(){
        var eDataPo;
        $.ajax({
            type: 'GET',
            url: '../db/CREATE_FILE/CREATE_PO_FILE.php',
            data: eDataPo,
            cache: false,
            success: function(data){
                //console.log(data);
            }
        });
    });
});


