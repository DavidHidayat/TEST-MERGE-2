<?php session_start(); 
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Constant.php";
if(isset($_SESSION['sUserId']))
{      
    $sNPK       = $_SESSION['sNPK'];
    $sNama      = $_SESSION['sNama'];
    $sBunit     = $_SESSION['sBunit'];
    $sSeksi     = $_SESSION['sSeksi'];
    $sKdper     = $_SESSION['sKdper'];
    $sNmPer     = $_SESSION['sNmper'];
    $sKdPlant   = $_SESSION['sKDPL'];
    $sNmPlant   = $_SESSION['sNMPL'];
    $sRoleId    = $_SESSION['sRoleId'];
    $sInet      = $_SESSION['sinet'];
    $sNotes     = $_SESSION['snotes'];
    $sUserId    = $_SESSION['sUserId'];
    $sBuLogin   = $_SESSION['sBuLogin'];
    $sUserType  = $_SESSION['sUserType'];
    
    if($sUserId != ''){
        $maxPrAmount    = strtoupper(trim($_GET['maxPrAmountPrm']));
        $currencyCd     = strtoupper(trim($_GET['currencyCdPrm']));
        $countApprover  = 0;
        
        if($maxPrAmount > 0)
        {
            $query = "select 
                        EPS_M_PR_APPROVER.APPROVER_NO
                        ,EPS_M_PR_APPROVER.NPK
                        ,EPS_M_LIMIT.LIMIT_AMOUNT
                      from 
                        EPS_M_PR_APPROVER 
                      inner join
                        EPS_M_LIMIT
                      on
                        EPS_M_PR_APPROVER.APPROVER_LEVEL = EPS_M_LIMIT.LEVEL_ID
                      inner join
                        EPS_M_EMPLOYEE
                      on
                        EPS_M_PR_APPROVER.NPK = EPS_M_EMPLOYEE.NPK
                      where
                        EPS_M_LIMIT.CURRENCY_CD = '$currencyCd'
                        and EPS_M_PR_APPROVER.BU_CD = '$sBuLogin'
                        and AKTIF = 'A'
                      order by
                        EPS_M_PR_APPROVER.APPROVER_NO";
            $sql = $conn->query($query);
            while($row = $sql->fetch(PDO::FETCH_ASSOC))
            {
                if($maxPrAmount > $row['LIMIT_AMOUNT'])
                {
                    
                }
                else
                {
                    $countApprover = $row['APPROVER_NO'];
                    break;
                }
            }
            
            if($countApprover == 0 || $sUserType == "UT_02")
            {
                $query2 = "select 
                            max(APPROVER_NO) as MAX_PR_APPROVER
                           from
                            EPS_M_PR_APPROVER
                           where 
                            EPS_M_PR_APPROVER.BU_CD = '$sBuLogin'";
                $sql2 = $conn->query($query2);
                $row2 = $sql2->fetch(PDO::FETCH_ASSOC);
                $maxPrApprover = $row2['MAX_PR_APPROVER'];
                $countApprover = $maxPrApprover;
            }
            $msg = 'Success'.$countApprover;
        }
        else
        {
            $msg = 'ZeroMaxAmount';
        }
        
    }
    else
    {
        $msg = 'SessionExpired';
    }
}
else
{
    $msg = 'SessionExpired';
}
echo $msg;
?>
