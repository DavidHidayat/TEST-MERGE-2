<?php
   $driver    = "{Client Access ODBC Driver (32-bit)}";
    //$driver    = "{iSeries Access ODBC Driver}";
    $server    = "DJOBLIB";
    $systemx='ndas0001.asia.denso';
    $namausr   = "EDP3";
    $pwd       = "nuel2811";
    $db        = "";
    try
    {
   	//$conn_db2= new PDO('odbc:DJOBLIB', $namausr, $pwd);
        $pdo = new PDO("odbc:DRIVER={iSeries Access ODBC Driver};SYSTEM=$systemx;PROTOCOL=TCPIP", $namausr, $pwd);
    }
    catch(PDOException $e) 
    {
        echo $e->getMessage();
    }
   
 
?>
