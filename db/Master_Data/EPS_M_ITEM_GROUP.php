<?php session_start();
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";
if(isset($_GET['action'])){
    $action= $_GET['action'];
}
if($action == 'search')
{
    $query = "select 
                    ITEM_GROUP_CD
                    ,ITEM_GROUP_NAME
                    ,convert(VARCHAR(24), CREATE_DATE, 120) as CREATE_DATE
                    ,CREATE_BY
                    ,convert(VARCHAR(24), UPDATE_DATE, 120) as UPDATE_DATE
                    ,UPDATE_BY
                from 
                    EPS_M_ITEM_GROUP
                order by 
                    ITEM_GROUP_CD";
        $sql = $conn->query($query);
        $row = $sql->fetchAll(PDO::FETCH_ASSOC);

    if ($row>0){
        echo '{success:true, rows:'.json_encode($row).'}';
    }else{
        echo '{success:false}';
    }
}
if($action=='Add')
{
    $itemGroupCd         = stripslashes(strtoupper($_POST['itemGroupCdVal']));
    $itemGroupCd         = str_replace("'", "''", $itemGroupCd);
    $itemGroupCd         = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $itemGroupCd);
    
    $itemGroupName       = stripslashes(strtoupper($_POST['itemGroupNameVal']));
    $itemGroupName       = str_replace("'", "''", $itemGroupName);
    $itemGroupName       = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $itemGroupName);
	
    $query = "insert into
                EPS_M_ITEM_GROUP
                (
                    ITEM_GROUP_CD
                    ,ITEM_GROUP_NAME
                    ,CREATE_DATE
                    ,CREATE_BY
                    ,UPDATE_DATE
                    ,UPDATE_BY
                )
              values
                (
                    '$itemGroupCd'
                    ,'$itemGroupName'
                    ,convert(VARCHAR(24), GETDATE(), 120)
                    ,'$userId'
                    ,convert(VARCHAR(24), GETDATE(), 120)
                    ,'$userId'
                )";
    $conn->query($query);
}
if($action=='Edit')
{
    $itemGroupCd         = stripslashes(strtoupper($_POST['itemGroupCdVal']));
    $itemGroupCd         = str_replace("'", "''", $itemGroupCd);
    $itemGroupCd         = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $itemGroupCd);
    
    $itemGroupName       = stripslashes(strtoupper($_POST['itemGroupNameVal']));
    $itemGroupName       = str_replace("'", "''", $itemGroupName);
    $itemGroupName       = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $itemGroupName);
	
    $query = "update
                EPS_M_ITEM_GROUP
              set
                ITEM_GROUP_NAME = '$itemGroupName'
                ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                ,UPDATE_BY = '$userId'
              where
                ITEM_GROUP_CD = '$itemGroupCd'";
    $conn->query($query);
}
if($action == 'Delete')
{
    $itemGroupCd    = $_POST['itemGroupCdVal'];
    $query = "delete from
                EPS_M_ITEM_GROUP
              where
                ITEM_GROUP_CD = '$itemGroupCd'";
    $conn->query($query);
}
?>
