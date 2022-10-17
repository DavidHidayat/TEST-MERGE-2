$(document).ready(function() {
	
    /** Firefox & Chrome */
    $(document).keydown(function(event) {
	    	    if (event.ctrlKey==true && (event.which == '61' || event.which == '107' || event.which == '173' || event.which == '109'  || event.which == '187'  || event.which == '189'  ) ){
     
		event.preventDefault();
		// 107 Num Key  +
		//109 Num Key  -
		//173 Min Key  hyphen/underscor Hey
		// 61 Plus key  +/=
	     }
	});
    /** Firefox  */
    $(window).bind('mousewheel DOMMouseScroll', function (event) {
	if (event.ctrlKey == true) {
            event.preventDefault();
	}
    });
	
	$.fn.animateProgress = function(progress, callback) {
        return this.each(function() {
			$(this).animate({
				width: progress+'%'
			}, {
				duration: 2000,

				easing: 'swing',

				step: function( progress ){
				var labelEl = $('.ui-label', this);

				if (progress >= 7 && labelEl.is(':hidden')) {
					labelEl.html('').fadeIn();
				};

				if (Math.ceil(progress) == 100) {
					labelEl.html('Done');
					setTimeout(function() {
					labelEl.fadeOut();
					}, 1000);
				}else if (progress >= 10) {
					labelEl.html('Processing <b>' + Math.ceil(progress) + '%</b>');
				} 
				},
				complete: function(scope, i, elem) {
					if (callback) {
						callback.call(this, i, elem );
					};
				}
			});
        });
    };
    /**
     * =========================================================================================================
     * DEFINE DIALOG
     * =========================================================================================================
     **/
    /** Logout confirmation */
    $("#dialog-confirm-logout").html("Do you want to Logout?");
    $("#dialog-confirm-logout").dialog({
        autoOpen    : false,
	height      : 155,
	width       : 400,
        position    : { my: "center", at: "top", of: $("body"), within: $("body") },
	modal       : true,
        buttons     : {
            "Yes": function(){
                $(this).dialog("close");
                window.location = "../db/Login/Logout.php";
            },
            "No": function(){
                $(this).dialog("close");
            }
        }
    });
    
    /**
     * =========================================================================================================
     * BUTTON FUNCTION
     * =========================================================================================================
     **/
    $("li#signout").click(function() {
        $("#dialog-confirm-logout").dialog("open");
    });
});
/**
 * =========================================================================================================
 * FUNCTION
 * =========================================================================================================
 **/
    /* create an array of days which need to be disabled */
    var disabledDays = ["5-5-2016","5-6-2016"
                        ,"7-4-2016","7-5-2016","7-6-2016","7-7-2016","7-8-2016"
                        ,"8-17-2016"
                        ,"9-12-2016"
                        ,"12-12-2016","12-26-2016","12-27-2016","12-28-2016","12-29-2016","12-30-2016"
                        ,"3-28-2017"
                        ,"4-14-2017","4-24-2017"
                        ,"5-1-2017","5-11-2017","5-25-2017"
                        ,"6-22-2017","6-23-2017","6-26-2017","6-27-2017","6-28-2017","6-29-2017","6-30-2017"
                        ,"8-17-2017"
                        ,"9-1-2017","9-21-2017"
                        ,"12-1-2017","12-25-2017","12-29-2017"
                        ,"1-1-2018"
                        ,"2-16-2018"
                        ,"3-30-2018"
                        ,"4-13-2018"
                        ,"5-1-2018","5-10-2018","5-29-2018"
                        ,"6-1-2018","6-15-2018"
                        ,"8-17-2018","8-22-2018"
                        ,"9-12-2018"
                        ,"11-20-2018"
                        ,"12-25-2018"];
    /* utility functions */
    function annualLeaveDays(date) {
            var m = date.getMonth(), d = date.getDate(), y = date.getFullYear();
            //console.log('Checking (raw): ' + m + '-' + d + '-' + y);
            for (i = 0; i < disabledDays.length; i++) {
                    if($.inArray((m+1) + '-' + d + '-' + y,disabledDays) != -1 || new Date() > date) {
                            //console.log('bad:  ' + (m+1) + '-' + d + '-' + y + ' / ' + disabledDays[i]);
                            return [false];
                    }
            }
            //console.log('good:  ' + (m+1) + '-' + d + '-' + y);
            return [true];
    }
    
    function noWeekendsOrHolidays(date) {
            var noWeekend = jQuery.datepicker.noWeekends(date);
            return noWeekend[0] ? annualLeaveDays(date) : noWeekend;
    }
