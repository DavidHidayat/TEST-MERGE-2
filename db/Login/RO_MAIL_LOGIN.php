<?php session_start();
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/lib/mail_lib/crypt.php";

$var            = decode($_SERVER['REQUEST_URI']);
$action         = trim($var['action']);
$poNo           = trim($var['poNo']);
$userId         = trim($var['userId']);
$password       = trim($var['password']);

$query_select_m_user = "select 
                            USERID
                            ,NPK
                            ,BU_CD
                            ,USER_TYPE
                            ,ACTIVE_FLAG 
                        from 
                            EPS_M_USER 
                        where ltrim(USERID)='$userId' and ltrim(PASSWORD)='$password'";
$sql_select_m_user = $conn->query($query_select_m_user);
$row_select_m_user = $sql_select_m_user->fetch(PDO::FETCH_ASSOC);
$userId     = $row_select_m_user['USERID'];
$npk        = $row_select_m_user['NPK'];
$buLogin    = $row_select_m_user['BU_CD'];
$userType   = $row_select_m_user['USER_TYPE'];
$activeFlag = $row_select_m_user['ACTIVE_FLAG'];

if($row_select_m_user)
{
    if($activeFlag == 'A'){
        $query = "select     
                    EPS_M_EMPLOYEE.NPK
                    ,EPS_M_USER.BU_CD as BU_LOGIN
                    ,EPS_M_USER.ROLE_ID
                    ,EPS_M_USER.USER_TYPE
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
            $_SESSION['sWarga']     = $row['WARGA'];
            $_SESSION['sBunit']     = $row['LKDP'];	
            $_SESSION['sSeksi']     = $row['SEKSI'];
            $_SESSION['sKdper']     = $row['PERSH'];
            $_SESSION['sNmper']     = $row['COMPANY_NAME'];
            $_SESSION['sKDPL']      = $row['PLANT'];
            $_SESSION['sNMPL']      = $row['PLANT_NAME'];
            $_SESSION['sRoleId']    = $row['ROLE_ID'];
            $_SESSION['sinet']      = $row['INETML'];
            $_SESSION['snotes']     = $row['INMAIL'];
            $_SESSION['sactiveFlag']= $row['AKTIF'];
            $_SESSION['sactiveFlagLogin']=$activeFlag;
            $_SESSION['sUserId']    = $userId;
            $_SESSION['sBuLogin']   = $buLogin;
            $_SESSION['sUserType']  = $userType;
            $_SESSION['sapproval']  = '';    
            $_SESSION['sMax']       = 0; 
            
            /** Update in EPS_M_USER */
            $ip = $_SERVER['REMOTE_ADDR'];
            $query_update_m_user = "update
                                        EPS_M_USER
                                    set
                                        LAST_UPDATE = convert(VARCHAR(24), GETDATE(), 120)
                                        ,DEVICE_ADDRESS = '$ip'
                                    where
                                        ltrim(USERID) = ltrim('$userId')";
            $conn->query($query_update_m_user);
            echo '{success:true, msg:'.json_encode(array('message'=>'Exist')).'}';
            
        }
        //$pageLocation = "../Redirect/RO_Screen.php?criteria=roDetail&paramPoNo=".$poNo;
        $pageLocation = "../../ero/WERO001.php?poNo=".$poNo;
    }
    else
    {
        $pageLocation = "../../index.php";
    }
}
else
{
    $pageLocation = "../../index.php";
}
echo "<script>document.location.href='".$pageLocation."';</script>";
?>
