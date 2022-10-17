<?php session_start();
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";

$query = "select 
            ITEM_TYPE_CD 
            ,ITEM_TYPE_NAME
          from 
            EPS_M_ITEM_TYPE";
$sql = $conn->query($query);
$row = $sql->fetchAll(PDO::FETCH_ASSOC);
if ($row>0){
    echo '{success:true, rows:'.json_encode($row).'}';
}else{
    echo '{success:false}';
}
?>
