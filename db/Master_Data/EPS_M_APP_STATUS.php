<?php session_start();
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PDOdb.php";

if(isset($_GET['action'])){
    $action= $_GET['action'];
}
if($action=='view'){
    $query = "select 
                APP_STATUS_CD
                ,APP_STATUS_NAME
              from 
                EPS_M_APP_STATUS
              order by
                APP_STATUS_CD";
    $sql = $conn->query($query);
    $row = $sql->fetchAll(PDO::FETCH_ASSOC);

    if ($row > 0){
        echo '{success:true, rows:'.json_encode($row).'}';
    }else{
        echo '{success:false}';
    }
}
?>
