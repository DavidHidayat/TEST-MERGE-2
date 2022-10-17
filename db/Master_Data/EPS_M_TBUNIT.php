<?php session_start();
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PDOdb.php";

$query = "select 
            KDBU as BU_CD
            ,NMBU1 as BU_NAME
            ,KDBU + '- ' + NMBU1 as BU_CD_NAME
          from 
            EPS_M_TBUNIT 
          where
            KDBU like 'T%' and KDBU <> 'T1000'
            and KDAKT = 'A'
          order by 
            KDBU";
$sql = $conn->query($query);
$row = $sql->fetchAll(PDO::FETCH_ASSOC);
if ($row>0){
    echo '{success:true, rows:'.json_encode($row).'}';
}else{
    echo '{success:false}';
}
?>
