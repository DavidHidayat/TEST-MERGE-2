<?php session_start();
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Constant.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Email/SendEmail.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/lib/mail_lib/crypt.php";
require $_SERVER['DOCUMENT_ROOT'].'/EPS/lib/mail_lib/class.phpmailer.php';
require $_SERVER['DOCUMENT_ROOT'].'/EPS/lib/mail_lib/class.smtp.php';

$mail   = $_POST['mailVal'];
$userId = $_POST['userIdVal'];
$query = "select
            NPK
            ,USERID
          from 
            EPS_M_USER
          where
            ltrim(USERID) = ltrim('$userId')";
$sql = $conn->query($query);
$row = $sql->fetch(PDO::FETCH_ASSOC);
if($row){
    $npk    = $row['NPK'];
    $userId = $row['USERID']; 
    $query = "select 
                EPS_M_DSCID.INETML
                ,EPS_M_DSCID.INOPOK
                ,EPS_M_DSCID.INMKAR
            from 
                EPS_M_DSCID 
            where
                ltrim(EPS_M_DSCID.INOPOK) = ltrim('".$npk."')
                and ltrim(INETML) = ltrim('".$mail."')";
    $sql = $conn->query($query);
    $row2 = $sql->fetch(PDO::FETCH_ASSOC);
    if($row2){
        $userMail       = $mail;
        $senderMail     = 'muh.iqbal@taci.toyota-industries.com';
        $senderMailName = 'EPS ADMINISTRATOR/DNIA';
        $newPassword    = substr(md5(uniqid()), 0, 8);
        $userName       = $row2['INMKAR'];
        $query = "update
                    EPS_M_USER
                set
                    PASSWORD = '$newPassword'
                    ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                    ,UPDATE_BY = '$userId'
                where
                    USERID = '$userId' ";
        $sql = $conn->query($query);
        sendMailNewPassword($userMail, $senderMail, $senderMailName, $userName, $userId, $newPassword);
        echo '{success:true, msg:'.json_encode(array('message'=>'success_reset')).'}';
    }else{
        echo '{success:true, msg:'.json_encode(array('message'=>'incorrect')).'}';
    }
}else{
    echo '{success:true, msg:'.json_encode(array('message'=>'UserIdNotExist')).'}';
}
?>
