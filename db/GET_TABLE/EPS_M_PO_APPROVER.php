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
        $maxPoAmount    = strtoupper(trim($_GET['maxPoAmountPrm']));
        $currencyCd     = strtoupper(trim($_GET['currencyCdPrm']));
        
        $countApprover  = 0;
        if($maxPoAmount > 0){
            /**$query = "select 
                        EPS_M_PO_APPROVER.APPROVER_NO
                        ,EPS_M_PO_APPROVER.NPK
                        ,EPS_M_LIMIT.LIMIT_AMOUNT
                      from 
                        EPS_M_PO_APPROVER 
                      inner join
                        EPS_M_LIMIT 
                      on
                        EPS_M_PO_APPROVER.APPROVER_LEVEL = EPS_M_LIMIT.LEVEL_ID
                      where
                        EPS_M_LIMIT.CURRENCY_CD = '$currencyCd'
                      order by
                        EPS_M_PO_APPROVER.APPROVER_NO";**/
			$query = "select 
                        EPS_M_PO_APPROVER.APPROVER_NO
                        ,EPS_M_PO_APPROVER.NPK
                        ,EPS_M_LIMIT_PO.LIMIT_AMOUNT
                      from 
                        EPS_M_PO_APPROVER 
                      inner join
                        EPS_M_LIMIT_PO 
                      on
                        EPS_M_PO_APPROVER.APPROVER_LEVEL = EPS_M_LIMIT_PO.LEVEL_ID
                      where
                        EPS_M_LIMIT_PO.CURRENCY_CD = '$currencyCd'
                      order by
                        EPS_M_PO_APPROVER.APPROVER_NO";
            $sql = $conn->query($query);
            while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                if($maxPoAmount > $row['LIMIT_AMOUNT']){
                 // addition because of Pak Haryanto cuti
                    //$countApprover = $row['APPROVER_NO'];

                }else{
                    $countApprover = $row['APPROVER_NO'];
                    break;
                }
            }
			
            if($countApprover == 0){
                $query2 = "select max(APPROVER_NO) as MAX_PO_APPROVER
                           from
                            EPS_M_PO_APPROVER";
                $sql2 = $conn->query($query2);
                $row2 = $sql2->fetch(PDO::FETCH_ASSOC);
                $maxPoApprover = $row2['MAX_PO_APPROVER'];
                $countApprover = $maxPoApprover;
            }
            $msg = 'Success'.$countApprover;
        }else{
            $msg = 'ZeroMaxAmount';
        }
        
    }else{
        $msg = 'SessionExpired';
    }
    
    echo $msg;
}else{	
?>
    <script language="javascript"> alert("Sorry, you are not authorized to access this page.");
    document.location="../db/Login/Logout.php"; </script>
<?
}
?>
