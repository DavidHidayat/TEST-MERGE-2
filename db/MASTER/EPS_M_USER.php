<?php session_start();
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Common.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Email/COM_EMAIL.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/lib/mail_lib/crypt.php";
require $_SERVER['DOCUMENT_ROOT']."/EPS/lib/mail_lib/class.phpmailer.php";
require $_SERVER['DOCUMENT_ROOT']."/EPS/lib/mail_lib/class.smtp.php";

if(isset($_GET['action']))
{
    $action= $_GET['action'];

    if($action=='UpdatePassword')
    {
        if(isset($_SESSION['sUserId']))
        {
            $sUserId    = $_SESSION['sUserId'];
            $sNPK       = $_SESSION['sNPK'];

            if($sUserId != '')
            {
                $oldPassword    = trim($_GET['oldPasswordPrm']);
                $newPassword    = trim($_GET['newPasswordPrm']);
                $reNewPassword  = trim($_GET['reNewPasswordPrm']);
                
               /**
                * SELECT EPS_M_USER
                */
                $query_select_m_user = "select
                                            USERID
                                            ,NPK
                                            ,PASSWORD
                                        from
                                            EPS_M_USER
                                        where
                                            ltrim(USERID) = ltrim('$sUserId')
                                            and ltrim(PASSWORD) = '$oldPassword'";
                $sql_select_m_user = $conn->query($query_select_m_user);
                $row_select_m_user = $sql_select_m_user->fetch(PDO::FETCH_ASSOC);
                $currentPassword   = $row_select_m_user['PASSWORD'];

                if($oldPassword == '' || $newPassword == '' || $reNewPassword == '')
                {
                    $msg = "Mandatory_1";
                }
                else if(!$row_select_m_user)
                {
                    $msg = "Mandatory_2";
                }
                else if ($newPassword != $reNewPassword)    
                {
                    $msg = "Mandatory_3";
                }
                else if ($newPassword == trim($sUserId) || $newPassword == trim($sNPK))
                {
                    $msg = "Mandatory_4";
                }
                else
                {
                    $query_update_m_user = "update 
                                                EPS_M_USER
                                            set 
                                                PASSWORD = '$newPassword'
                                                ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                ,UPDATE_BY = '$sUserId'
                                            where 
                                                (ltrim(USERID) = ltrim('$sUserId')) 
                                                and (ltrim(PASSWORD) = ltrim('$currentPassword'))";
                    $conn->query($query_update_m_user);
                    $msg = "Success";
                }

            }
            else
            {	
                $msg = "SessionExpired";
            }
        }
        else
        {	
            $msg = "SessionExpired";
        }
    }
    
    if($action=='ResetPassword')
    {
        $userIdPrm  = trim($_GET['userIdPrm']);
        $emailPrm   = trim($_GET['emailPrm']);
            
        /**
         * SELECT EPS_M_USER
         */
        $query_select_m_user = "select
                                        EPS_M_USER.USERID
                                        ,EPS_M_USER.NPK
                                        ,EPS_M_USER.PASSWORD
                                        ,EPS_M_DSCID.INETML
                                        ,EPS_M_EMPLOYEE.NAMA1
                                    from
                                        EPS_M_USER
                                    left join
                                        EPS_M_DSCID
                                    on 
                                        EPS_M_USER.NPK = EPS_M_DSCID.INOPOK 
                                    left join
                                        EPS_M_EMPLOYEE
                                    on
                                        EPS_M_USER.NPK = EPS_M_EMPLOYEE.NPK
                                    where
                                        ltrim(EPS_M_USER.USERID) = '$userIdPrm'";
        $sql_select_m_user = $conn->query($query_select_m_user);
        $row_select_m_user = $sql_select_m_user->fetch(PDO::FETCH_ASSOC);
        $currentEmail   = $row_select_m_user['INETML'];
        $userName       = $row_select_m_user['NAMA1'];
		$userId			= $row_select_m_user['USERID'];
            
        if($userIdPrm == '' || $emailPrm == '')
        {
            $msg = "Mandatory_1";
        }
        else if (strtoupper(trim($emailPrm)) != strtoupper(trim($currentEmail)))
        {
            $msg = "Mandatory_2";
        }
        else
        {
            $newPassword    = substr(md5(uniqid()), 0, 8);
            $senderMail     = 'muh.iqbal@taci.toyota-industries.com';
            $senderMailName = 'EPS ADMINISTRATOR/DNIA';
            
            $query_update_m_user = "update
                                        EPS_M_USER
                                    set
                                        PASSWORD = '$newPassword'
                                        ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                        ,UPDATE_BY = '$userId'
                                    where
                                        ltrim(EPS_M_USER.USERID) = '$userIdPrm' ";
            $conn->query($query_update_m_user);
            resetPasswordMail($currentEmail, $senderMail, $senderMailName, $userName, $userIdPrm, $newPassword);
            $msg = "Success";
        }
    }
}
echo $msg;
?>
