<?php session_start();
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PDOdb.php";
	$query = "select * from EPS_M_STATE";
    $sql = $conn->query($query);
    $row = $sql->fetchAll(PDO::FETCH_ASSOC);

    if ($row>0){
        echo '{success:true, data:'.json_encode($row).'}';
    }else{
        echo '{success:false}';
    }
?>