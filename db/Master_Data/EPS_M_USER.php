<?php
session_start();
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PDOdb.php";

if(isset($_GET['action'])){
    $action= $_GET['action'];
}
$userId     = $_SESSION['sUserId'];

if($action == 'updatePassword'){
    $oldPassword    = $_POST['oldPassword'];
    $newPassword    = $_POST['newPassword'];
    
    $query = "select
                PASSWORD
              from
                EPS_M_USER
              where
                USERID = '$userId'";
    $sql = $conn->query($query);
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    if($row){
        $oldPasswordDb = $row['PASSWORD'];
    }
    if(trim($oldPassword) == trim($oldPasswordDb)){
        $q="update 
                EPS_M_USER
            set 
                PASSWORD = '$newPassword'
                ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                ,UPDATE_BY = '$userId'
            where 
                (ltrim(USERID) = ltrim('$userId')) 
                and (ltrim(PASSWORD) = ltrim('$oldPasswordDb'))";
        $conn->query($q);
        echo '{success: true}';
    }else{
        echo json_encode(array('message'=>'Error'));
    }
}
?>
