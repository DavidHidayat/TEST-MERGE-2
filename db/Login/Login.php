<?php session_start();
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";

if(isset($_POST['userId'])){
    $userIdForm = $_POST["userId"];
    $password = $_POST["password"];
}

$query = "select 
            USERID
            ,NPK
            ,BU_CD
            ,USER_TYPE
            ,ACTIVE_FLAG
            ,INV_TYPE 
          from 
            EPS_M_USER 
          where 
            ltrim(USERID)='$userIdForm' and ltrim(PASSWORD)='$password'";
$sql = $conn->query($query);
$row = $sql->fetch(PDO::FETCH_ASSOC);
$userId     = $row['USERID'];
$npk        = $row['NPK'];
$buLogin    = $row['BU_CD'];
$userType   = $row['USER_TYPE'];
$activeFlag = $row['ACTIVE_FLAG'];
$invType    = $row['INV_TYPE'];
if($row){
    if($activeFlag == 'A'){
        $query = "select     
                    EPS_M_EMPLOYEE.NPK
                    ,EPS_M_USER.BU_CD as BU_LOGIN
                    ,EPS_M_USER.ROLE_ID
                    ,EPS_M_EMPLOYEE.AKTIF
                    ,EPS_M_EMPLOYEE.NAMA1
                    ,EPS_M_EMPLOYEE.LKDP
                    ,EPS_M_EMPLOYEE.SEKSI
                    ,EPS_M_EMPLOYEE.PERSH
                    ,EPS_M_EMPLOYEE.WARGA
                    ,EPS_M_COMPANY.COMPANY_NAME
                    ,EPS_M_EMPLOYEE.PLANT
                    ,EPS_M_PLANT.PLANT_NAME
                    ,EPS_M_DSCID.INETML
                    ,EPS_M_DSCID.INMAIL
                from         
                    EPS_M_EMPLOYEE 
                inner join
                    EPS_M_DSCID 
                on 
                    ltrim(EPS_M_EMPLOYEE.NPK) = ltrim(EPS_M_DSCID.INOPOK) 
                inner join
                    EPS_M_PLANT 
                on
                    EPS_M_EMPLOYEE.PLANT = EPS_M_PLANT.PLANT_CD 
                inner join
                    EPS_M_COMPANY 
                on
                    EPS_M_EMPLOYEE.PERSH = EPS_M_COMPANY.COMPANY_CD
                inner join
                    EPS_M_USER 
                on 
                    EPS_M_EMPLOYEE.NPK = EPS_M_USER.USERID
                where 
                    ltrim(EPS_M_EMPLOYEE.NPK)=ltrim('$npk')";
        $sql = $conn->query($query);
        $row = $sql->fetch(PDO::FETCH_ASSOC);
        if($row){
            $_SESSION['sNPK']       = $row['NPK'];
            $_SESSION['sNama']      = addslashes($row['NAMA1']);
            $_SESSION['sBunit']     = $row['LKDP'];	
            $_SESSION['sSeksi']     = $row['SEKSI'];
            $_SESSION['sKDPL']      = $row['PLANT'];
            $_SESSION['sNMPL']      = $row['PLANT_NAME'];
            $_SESSION['sKdper']     = $row['PERSH'];
            $_SESSION['sWarga']     = $row['WARGA'];
            $_SESSION['sNmper']     = $row['COMPANY_NAME'];
            $_SESSION['sRoleId']    = $row['ROLE_ID'];
            $_SESSION['sinet']      = $row['INETML'];
            $_SESSION['snotes']     = $row['INMAIL'];
            $_SESSION['sactiveFlag']= $row['AKTIF'];
            $_SESSION['sactiveFlagLogin']=$activeFlag;
            $_SESSION['sUserId']    = $userId;
            $_SESSION['sBuLogin']   = $buLogin;
            $_SESSION['sUserType']  = $userType;
            $_SESSION['sInvType']   = $invType;
            $_SESSION['sapproval']  = '';    
            $_SESSION['sMax']       = 0;  
            
            /** Search IS Approver and set as session */
            $query = "select 
                        NAMA1
                      from
                        EPS_M_PR_SPECIAL_APPROVER
                      inner join
                        EPS_M_EMPLOYEE
                      on
                        EPS_M_PR_SPECIAL_APPROVER.NPK = EPS_M_EMPLOYEE.NPK
                      where
                        SPECIAL_APPROVER_CD = '001'";
            $sql = $conn->query($query);
            $row3 = $sql->fetch(PDO::FETCH_ASSOC);
            $_SESSION['sIsApproval'] = $row3['NAMA1'];
            
            /** Update in EPS_M_USER */
            $ip = $_SERVER['REMOTE_ADDR'];
            $query2 = "update
                        EPS_M_USER
                    set
                        LAST_UPDATE = convert(VARCHAR(24), GETDATE(), 120)
                        ,DEVICE_ADDRESS = '$ip'
                    where
                        ltrim(USERID) = ltrim('$userId')";
            $conn->query($query2);
            echo '{success:true, msg:'.json_encode(array('message'=>'Exist')).'}'; 
        } else{
            echo '{success:true, msg:'.json_encode(array('message'=>'EmailNotExist')).'}'; 
        }
    }else{
        echo '{success:true, msg:'.json_encode(array('message'=>'UserInactive')).'}'; 
    }
}else{
    echo '{success:true, msg:'.json_encode(array('message'=>'UserNotExist')).'}'; 
}
?>
