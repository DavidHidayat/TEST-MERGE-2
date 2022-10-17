<?php session_start();
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PDOdb.php";

$query = "select 
            PLANT_CD 
            ,PLANT_NAME
          from 
            EPS_M_PLANT 
          where
            PLANT_CD = '0'
            or PLANT_CD = '1'
            or PLANT_CD = '5'";
$sql = $conn->query($query);
$row = $sql->fetchAll(PDO::FETCH_ASSOC);
if ($row>0){
    echo '{success:true, rows:'.json_encode($row).'}';
}else{
    echo '{success:false}';
}
?>
