<?php session_start();
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";

if(isset($_GET['action'])){
    $action= $_GET['action'];
}
$userId     = $_SESSION['sNPK'];
if($action=='view'){
    $query = "select * from EPS_M_STATE";
    $sql = $conn->query($query);
    $row = $sql->fetchAll(PDO::FETCH_ASSOC);

    if ($row>0){
        echo '{success:true, rows:'.json_encode($row).'}';
    }else{
        echo '{success:false}';
    }
}