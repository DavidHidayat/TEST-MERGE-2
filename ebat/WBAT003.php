<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <!--<meta http-equiv="X-UA-Compatible" content="IE=8">-->
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta http-equiv="refresh" content="86400; url=WBAT003.php">
        
        <link rel="stylesheet" href="../css/bootstrap.min.css" ></link>
        <link rel="stylesheet" href="../css/bootstrap-responsive.min.css"></link>
        <link rel="stylesheet" href="../css/font-awesome.css">
        <link rel="stylesheet" href="../css/style.css" ></link>
        <link rel="stylesheet" href="../css/dashboard.css" ></link>
        <link rel="stylesheet" href="../css/additional.css" ></link>
        <link rel="stylesheet" href="../lib/jquery/jquery-ui-1.11.2.custom/jquery-ui.css">
        
        <script src="../lib/jquery/jquery-1.11.0.min.js"></script> 
        <script src="../lib/jquery/jquery-ui-1.11.2.custom/jquery-ui.min.js"></script> 
        <script src="../lib/jquery/jquery-ui-1.11.2.custom/jquery-ui.js"></script> 
        <script type="text/javascript" src="../js/Common.js"></script>
        <script type="text/javascript" src="../js/Common_JQuery.js"></script>
        <script>
            $(function() {
                var plantCdArray = [7];
				var eDataPo;
                for(var i=0; i < plantCdArray.length; i++)
				{
                    var plantCd = plantCdArray[i];
                    eDataPo = "plantCdVal="+plantCd;
                    $.ajax({
                        type: 'GET',
                        url: '../db/CREATE_FILE/CREATE_DELAY_DELIVERY_FILE.php',
                        data: eDataPo,
                        success: function(data){
                            console.log(data);
                        }
                    });
                }
            });
        </script>
        <title>EPS</title>
    </head>
    <body> 
        <p>
            This screen has purpose for Sending Delay Delivery to Supplier for each day.
        </p>
        <p>
            Please, do not close this screen from browser.
        </p>
    </body>
</html>
