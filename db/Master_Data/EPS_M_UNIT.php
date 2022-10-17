<?php session_start();
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PDOdb.php";

if(isset($_GET['action'])){
    $action= $_GET['action'];
}
$userId     = $_SESSION['sNPK'];
if($action=='view'){
    $query = "select UNIT_CD, UNIT_NAME from EPS_M_UNIT_MEASURE";
    $sql = $conn->query($query);
    $row = $sql->fetchAll(PDO::FETCH_ASSOC);

    if ($row>0){
        echo '{success:true, rows:'.json_encode($row).'}';
    }else{
        echo '{success:false}';
    }
}
if($action=='Add'){
    $unitCd         = stripslashes(strtoupper($_POST['unitCdVal']));
    $unitCd         = str_replace("'", "''", $unitCd);
    $unitCd         = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $unitCd);
    
    $unitName       = stripslashes(strtoupper($_POST['unitNameVal']));
    $unitName       = str_replace("'", "''", $unitName);
    $unitName       = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $unitName);
    $query = "insert into
                EPS_M_UNIT_MEASURE
                (
                    UNIT_CD
                    ,UNIT_NAME
                    ,CREATE_DATE
                    ,CREATE_BY
                    ,UPDATE_DATE
                    ,UPDATE_BY
                )
              values
                (
                    '$unitCd'
                    ,'$unitName'
                    ,convert(VARCHAR(24), GETDATE(), 120)
                    ,'$userId'
                    ,convert(VARCHAR(24), GETDATE(), 120)
                    ,'$userId'
                )";
    $conn->query($query);
}
if($action == 'Edit'){
    $unitCd         = stripslashes(strtoupper($_POST['unitCdVal']));
    $unitCd         = str_replace("'", "''", $unitCd);
    $unitCd         = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $unitCd);
    
    $unitName       = stripslashes(strtoupper($_POST['unitNameVal']));
    $unitName       = str_replace("'", "''", $unitName);
    $unitName       = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $unitName);
    $query = "update
                EPS_M_UNIT_MEASURE
              set
                UNIT_NAME = '$unitName'
                ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                ,UPDATE_BY = '$userId'
              where
                UNIT_CD = '$unitCd'";
    $conn->query($query);
}
if($action == 'Delete'){
    $unitCd  = $_POST['unitCdVal'];
    $query = "delete from
                EPS_M_UNIT_MEASURE
              where
                UNIT_CD = '$unitCd'";
    $conn->query($query);
}
?>
