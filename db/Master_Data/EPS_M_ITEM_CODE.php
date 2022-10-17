<?php session_start();
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";

if(isset($_GET['action'])){
    $action= $_GET['action'];
}
$userId     = $_SESSION['sNPK'];
if($action=='view'){
    $query = "select CONVERT(int, ACCOUNT_NO) as ACCOUNT_NO, ACCOUNT_CD,  ACCOUNT_NO + ' - ' + ACCOUNT_CD + ' - ' + ACCOUNT_NAME  AS ACCOUNT_NAME from EPS_M_ACCOUNT WHERE ITEM_TYPE_CD = '1' 
              order by 
                ACCOUNT_NO ";
    $sql = $conn->query($query);
    $row = $sql->fetchAll(PDO::FETCH_ASSOC);

    if ($row>0){
        echo '{success:true, rows:'.json_encode($row).'}';
    }else{
        echo '{success:false}';
    }
}
