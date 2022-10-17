/** IE 
document.attachEvent('onmousewheel', function(e){
     if (!e) var e = window.event;
     e.returnValue = false;
     e.cancelBubble = true;
     return false;
}, false);*/
/** 
* =======================================
* Get Current Date
* =======================================
**/
function getDate(){
    var currentTime = new Date();
    var month = addZero(currentTime.getMonth()+1); //January is 0
    var day = addZero(currentTime.getDate());
    var year = currentTime.getFullYear();
    return(day + "/" + month + "/" + year);
}
function addZero(num){
    (String(num).length < 2) ? num = String("0" + num) :  num = String(num);
    return num;        
}
function numberWithCommas(x) {
    var parts = x.toString().split(".");
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    return parts.join(".");
}
function maximize() {
    window.moveTo(0,0);
    if (document.all)
    {
        top.window.resizeTo(screen.availWidth,screen.availHeight);
    }

    else if (document.layers||document.getElementById)
    {
        if (top.window.outerHeight<screen.availHeight||top.window.outerWidth<screen.availWidth)
        {
            top.window.outerHeight = screen.availHeight;
            top.window.outerWidth = screen.availWidth;
        }
    }
}
// Handle function key on every pages
document.onkeydown = handleFunctionKey;
function handleFunctionKey(e){
    if (!e) {
        var e = window.event;
    }
    var key = e.keyCode;
    if ((key > 111) && (key < 123)){
        if (!e.stopPropagation){ // IE
            e.keyCode = 0;
            e.cancelBubble = true;
        } else {
            e.stopPropagation(); // FireFox
        }
        return false;
    }
}
/*
 * checkNullOrEmpty is used for checking value of input object. If input value
 * is NaN, undefined, empty or null, the result is "true" otherwise return
 * false. @param varInput @return true Or false
 */
function checkNullOrEmpty(varInput) {
    return ((varInput == NaN) || (varInput == undefined) || (varInput == null) || (varInput == ""));
}
function prSpecialTypeVal(val) {
    if(val == 'IT'){
        return '<span>IT Equipment</span>';
    } else{
        return '<span></span>';
    }
    return val;
}
function prItemStatusVal(val) {
    if (val == '1070'){
        val = 'Rejected';
        return '<span style="color:#eb071a;">'+val+'</span>';
    } 
    if (val == '1060'){
        val = '';
    }
    return val;
}
function prStatusVal(val) {
    if (val == 'PR Draft') {
        return '<span style="color:indigo;">'+val+'</span>';
    } else if (val == 'PR Waiting for Approval') {
        return '<span style="color:green;">'+val+'</span>';
    } else if (val == 'PR Rejected by Approver' || val == 'PR Rejected by Procurement'){
        return '<span style="color:#eb071a;">'+val+'</span>';
    } else if(val == 'PR Waiting Acceptance by Procurement'){
        return '<span style="color:blue">'+val+'</span>';
    } else if(val == 'PR Accepted by Procurement'){
        return '<span style="color:#000">'+val+'</span>';
    }else{
        return '<span style="color:#000">'+val+'</span>';
    }
    return val;
}
function approvalStatusVal(val) {
    if (val == 'Urgent Approval' || val == 'Rejected') {
        return '<span style="color:red;">'+val+'</span>';
    } else{
        return '<span style="color:#000">'+val+'</span>';
    }
    return val;
}