<?php session_start();
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PDOdb.php";

$userId     = trim($_GET['userIdPrm']);
$password   = trim($_GET['passwordPrm']);

if($userId != '' && $password != '')
{      
    $query_m_user_by_userid = "select 
                                    USERID
                                    ,NPK
                                    ,BU_CD
                                    ,USER_TYPE
                                    ,ACTIVE_FLAG
                                    ,INV_TYPE 
                                from 
                                    EPS_M_USER 
                                where 
                                    ltrim(USERID)='$userId'";
    $sql_m_user_by_userid = $conn->query($query_m_user_by_userid);
    $row_m_user_by_userid = $sql_m_user_by_userid->fetch(PDO::FETCH_ASSOC);
    if($row_m_user_by_userid)
    {
        $query_m_user = "select 
                            USERID
                            ,NPK
                            ,BU_CD
                            ,USER_TYPE
                            ,ACTIVE_FLAG
                            ,INV_TYPE 
                        from 
                            EPS_M_USER 
                        where 
                            ltrim(USERID)='$userId' 
                            and ltrim(PASSWORD)='$password'";
        $sql_m_user = $conn->query($query_m_user);
        $row_m_user = $sql_m_user->fetch(PDO::FETCH_ASSOC);
        if($row_m_user)
        {
            $userId     = $row_m_user['USERID'];
            $npk        = $row_m_user['NPK'];
            $buLogin    = $row_m_user['BU_CD'];
            $userType   = $row_m_user['USER_TYPE'];
            $activeFlag = $row_m_user['ACTIVE_FLAG'];
            $invType    = $row_m_user['INV_TYPE'];
            if($activeFlag == 'A')
            {
                $query_select_m_employee = "select     
                                        EPS_M_EMPLOYEE.NPK
                                        ,EPS_M_EMPLOYEE.AKTIF
                                        ,EPS_M_USER.BU_CD as BU_LOGIN
                                        ,EPS_M_USER.ROLE_ID
                                        ,EPS_M_EMPLOYEE.NAMA1
                                        ,EPS_M_EMPLOYEE.LKDP
                                        ,EPS_M_EMPLOYEE.SEKSI
                                        ,EPS_M_EMPLOYEE.PERSH
                                        ,EPS_M_COMPANY.COMPANY_NAME
                                        ,EPS_M_EMPLOYEE.PLANT
                                        ,EPS_M_EMPLOYEE.WARGA
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
                $sql_select_m_employee = $conn->query($query_select_m_employee);
                $row_select_m_employee = $sql_select_m_employee->fetch(PDO::FETCH_ASSOC);
                if($row_select_m_employee)
                {
                    $npkActive = $row_select_m_employee['AKTIF'];
                    if($npkActive == 'A')
                    {
                        
                        $query_m_bu = "select 
                            NMBU1
                        from 
                            EPS_M_TBUNIT 
                        where 
                            KDBU='$buLogin'";
        $sql_m_bu = $conn->query($query_m_bu);
        $row_m_bu = $sql_m_bu->fetch(PDO::FETCH_ASSOC);
                        
                        
                        $_SESSION['sNPK']       = $row_select_m_employee['NPK'];
                        $_SESSION['sNama']      = addslashes($row_select_m_employee['NAMA1']);
                        $_SESSION['sBunit']     = $row_select_m_employee['LKDP'];	
                        $_SESSION['sSeksi']     = $row_select_m_employee['SEKSI'];
                        $_SESSION['sWarga']     = $row_select_m_employee['WARGA'];
                        $_SESSION['sKDPL']      = $row_select_m_employee['PLANT'];
                        $_SESSION['sNMPL']      = $row_select_m_employee['PLANT_NAME'];
                        $_SESSION['sKdper']     = $row_select_m_employee['PERSH'];
                        $_SESSION['sNmper']     = $row_select_m_employee['COMPANY_NAME'];
                        $_SESSION['sRoleId']    = $row_select_m_employee['ROLE_ID'];
                        $_SESSION['sinet']      = $row_select_m_employee['INETML'];
                        $_SESSION['snotes']     = $row_select_m_employee['INMAIL'];
                        $_SESSION['sactiveFlag']= $row_select_m_employee['AKTIF'];
						$_SESSION['sactiveFlagLogin']=$activeFlag;
                        $_SESSION['sUserId']    = $userId;
                        $_SESSION['sBuLogin']   = $buLogin;
                        $_SESSION['sBuLoginName']   = $row_m_bu['NMBU1'];
                        $_SESSION['sUserType']  = $userType;
                        $_SESSION['sInvType']   = $invType;
                        $_SESSION['sapproval']  = '';    
                        $_SESSION['sMax']       = 0; 
                        $_SESSION['sExpired']   = $_SESSION['start'] + (1 * 60);

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
						
						/** Search PR/PO Approval for Master List */
						$query = "select 
									NAMA1
								  from
									EPS_M_PR_SPECIAL_APPROVER
								  inner join
									EPS_M_EMPLOYEE
								  on
									EPS_M_PR_SPECIAL_APPROVER.NPK = EPS_M_EMPLOYEE.NPK
								  where
									SPECIAL_APPROVER_CD = '004'";
						$sql = $conn->query($query);
						$row4 = $sql->fetch(PDO::FETCH_ASSOC);
						$_SESSION['sMstApproval'] = $row4['NAMA1'];
			
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

                        $msg = 'Success';
                    }
                    else
                    {
                        $msg = 'Mandatory_6';
                    }
                }
                else
                {
                    $msg = 'Mandatory_5';
                }
            }
            else
            {
                $msg = 'Mandatory_4';
            }
        }
        else
        {
            $msg = 'Mandatory_3';
        }
    }
    else
    {
        $msg = 'Mandatory_2';
    }
}
else
{	
    $msg = 'Mandatory_1';
}
echo $msg;
?>
