<?php
$conn_as = odbc_connect('epstaci', 'ITODBC', 'ITODBC');
//$setClosingMonth = substr($currentMonthClosing, 4, 2);
//$setClosingYear = substr($currentMonthClosing, 0, 4);
$setClosingMonth = '02';
$setClosingYear = '2021';
$test = 'LIVE';

//Nyalain nanti pas Closing... iqbal 
//ADA PERUBAHAN ADD PARAMETER (IQBAL)
$query_call_as400_cl = "call PCRGEN.XJDESBM PARM('" . $setClosingMonth . "', '" . $setClosingYear . "', '" . $test . "')";
$result_call_cl = odbc_exec($conn_as, $query_call_as400_cl);
echo "$query_call_as400_cl";
if($result_call_cl){
    echo 'Berhasil Tf JDE';
}else{
    echo 'Tf JDE Failed';
}
