<?php session_start();
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PDOdb.php";

$sBuLogin   = $_SESSION['sBuLogin'];
$sInvType   = $_SESSION['sInvType'];

if(isset($_GET['action'])){
    $action= $_GET['action'];
}
if($action=='view'){
    $query = "select 
                 CONVERT(int, ACCOUNT_NO) as ACCOUNT_NO
                ,ACCOUNT_CD
                ,ACCOUNT_NAME
                ,(ACCOUNT_NO + ' - '+ACCOUNT_CD + ' - ' + ACCOUNT_NAME)as ACCOUNT_CD_NAME 
              from 
                EPS_M_ACCOUNT
              where
                ITEM_TYPE_CD = '1'
              order by 
                ACCOUNT_NO";
    $sql = $conn->query($query);
    $row = $sql->fetchAll(PDO::FETCH_ASSOC);

    if ($row > 0){
        echo '{success:true, rows:'.json_encode($row).'}';
    }else{
        echo '{success:false}';
    }
}
if($action=='viewInv'){
    $query = "select 
                 CONVERT(int, ACCOUNT_NO) as ACCOUNT_NO
                ,ACCOUNT_CD
                ,ACCOUNT_NAME
                ,(ACCOUNT_NO + ' - '+ACCOUNT_CD + ' - ' + ACCOUNT_NAME)as ACCOUNT_CD_NAME 
              from 
                EPS_M_ACCOUNT ";
    if($sInvType == "N1000" &&  $sBuLogin == "T4420 ")
    {
        $query .=  " where
                        ITEM_TYPE_CD = '5' ";
    }
    else
    {
        $query .=  " where
                        ITEM_TYPE_CD = '3' ";
    }
    $query .= "order by 
                ACCOUNT_NO";
    $sql = $conn->query($query);
    $row = $sql->fetchAll(PDO::FETCH_ASSOC);

    if ($row > 0){
        echo '{success:true, rows:'.json_encode($row).'}';
    }else{
        echo '{success:false}';
    }
}

if($action=='viewInvS'){
    $query = "select 
                 CONVERT(int, ACCOUNT_NO) as ACCOUNT_NO
                ,ACCOUNT_CD
                ,ACCOUNT_NAME
                ,(ACCOUNT_NO + ' - '+ACCOUNT_CD + ' - ' + ACCOUNT_NAME)as ACCOUNT_CD_NAME 
              from 
                EPS_M_ACCOUNT
              where
                ITEM_TYPE_CD = '4'
              order by 
                ACCOUNT_NO";
    $sql = $conn->query($query);
    $row = $sql->fetchAll(PDO::FETCH_ASSOC);

    if ($row > 0){
        echo '{success:true, rows:'.json_encode($row).'}';
    }else{
        echo '{success:false}';
    }
}
?>