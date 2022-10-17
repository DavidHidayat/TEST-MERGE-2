<?php session_start();
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Constant.php";

if(isset($_SESSION['sUserId']))
{      
    $sUserId    = $_SESSION['sUserId'];
    
    if($sUserId != '')
    {
        $prNoCriteria   = $_GET['prNo'];
        $sRoleId    	= $_SESSION['sRoleId'];
        $sWarga     	= $_SESSION['sWarga'];
        $criteria   	= $_GET['criteria'];
        $paramPrNo  	= $_GET['paramPrNo'];
        $chargedBu  	= $_GET['chargedBu'];
        $sNpk       	= $_SESSION['sNPK'];
        $sCompanyCd     = $_SESSION['sKdper'];
        
//        if($sNpk == '2140195'){
//            $sWarga = 'U';
//        }else{
//            $sWarga = $sWarga;
//        }
	//echo $sNpk;	
        if($paramPrNo == "")
        {
            $paramPrNo = $prNoCriteria;
        }
       /**
        * SELECT EPS_M_EMPLOYEE
        */
        $query_select_m_employee = "select
                                        EPS_M_EMPLOYEE.LEMBG
                                        ,EPS_M_USER.ROLE_ID
                                    from
                                        EPS_M_EMPLOYEE
                                    left join
                                        EPS_M_USER
                                    on
                                        EPS_M_EMPLOYEE.NPK = EPS_M_USER.NPK
                                    where
                                        EPS_M_EMPLOYEE.NPK = '$sNpk'";
        $sql_select_m_employee = $conn->query($query_select_m_employee);
        $row_select_m_employee = $sql_select_m_employee->fetch(PDO::FETCH_ASSOC);
        $lembg  = $row_select_m_employee['LEMBG'];
        $roleId = $row_select_m_employee['ROLE_ID'];
        
       /**
        * SELECT EPS_T_PR_HEADER
        **/
        $query_select_t_pr_header = "select 
                                        PR_STATUS
                                        ,REQUESTER 
                                        ,BU_CD
                                        ,REQ_BU_CD
                                        ,APPROVER
                                        ,USERID
                                        ,SPECIAL_TYPE_ID
                                        ,PROC_IN_CHARGE
                                    from
                                        EPS_T_PR_HEADER
                                    where
                                        PR_NO = '".$paramPrNo."'";
        $sql_select_t_pr_header = $conn->query($query_select_t_pr_header);
        $row_select_t_pr_header = $sql_select_t_pr_header->fetch(PDO::FETCH_ASSOC);
        if($row_select_t_pr_header){
            $prStatus   = $row_select_t_pr_header['PR_STATUS'];
            $requester  = $row_select_t_pr_header['REQUESTER'];
            $prBuCd     = $row_select_t_pr_header['BU_CD'];
            $prIssuer   = $row_select_t_pr_header['REQ_BU_CD'];
            $approver   = $row_select_t_pr_header['APPROVER'];
            $prUserId   = $row_select_t_pr_header['USERID'];
            $specialType= $row_select_t_pr_header['SPECIAL_TYPE_ID'];
            $procInCharge= $row_select_t_pr_header['PROC_IN_CHARGE'];
        }
        
       /**
        * SELECT EPS_M_PR_APPROVER
        **/
        $query_select_m_pr_approver = "select 
                                            APPROVER_NO
                                        from
                                            EPS_M_PR_APPROVER
                                        where
                                            NPK = '$sNpk'
                                            and BU_CD = '$prIssuer'";
        $sql_select_m_pr_approver = $conn->query($query_select_m_pr_approver);
        $row_select_m_pr_approver = $sql_select_m_pr_approver->fetch(PDO::FETCH_ASSOC);
        $approverNo = $row_select_m_pr_approver['APPROVER_NO'];
        
       /**
        * Search in EPS_T_PR_APPROVER
        * Check approver number by next approver (EPS_T_PR_HEADER)
        **/
        $query_select_t_pr_approver = "select 
                                            APPROVER_NO
                                        from
                                            EPS_T_PR_APPROVER
                                        where
                                            NPK = '$approver'
                                            and PR_NO = '$paramPrNo'";
        $sql_select_t_pr_approver = $conn->query($query_select_t_pr_approver);
        $row_select_t_pr_approver = $sql_select_t_pr_approver->fetch(PDO::FETCH_ASSOC);
        $prApproverNo = $row_select_t_pr_approver['APPROVER_NO'];
        
       /**
        * Search in EPS_T_PR_APPROVER
        * Check NPK exist or not as approver in EPS_T_PR_APPROVER
        **/
        $query_select_count_t_pr_approver = "select 
                                                count(NPK)
                                            as 
                                                APPROVER
                                            from
                                                EPS_T_PR_APPROVER
                                            where
                                                NPK = '$sNpk'
                                                and PR_NO = '$paramPrNo'";
        $sql_select_count_t_pr_approver = $conn->query($query_select_count_t_pr_approver);
        $row_select_count_t_pr_approver = $sql_select_count_t_pr_approver->fetch(PDO::FETCH_ASSOC);
        $approverPr = $row_select_count_t_pr_approver['APPROVER'];

        if($prStatus == constant('1010') && trim($prUserId) == trim($sUserId))
		{
            $redirectPage                   = "../../epr/WEPR003.php?prNo=".$paramPrNo;
            $_SESSION['EPSAuthority']       ='EPSEditPrScreen';
        }
        else if($prStatus == constant('1020') && trim($approver) == trim($sUserId) && $chargedBu == '' && trim($sWarga) == 'U' && $sCompanyCd != 'S')
		//else if($prStatus == constant('1020') && trim($approver) == trim($sUserId) && $chargedBu == '' && trim($sWarga) == 'I' && (int)$lembg < (int)'091')
        //else if($prStatus == constant('1020') && trim($approver) == trim($sUserId) && $chargedBu == '' && trim($sWarga) == 'I' && (int)$lembg < (int)'091' && strlen(trim($lembg)) == 3)
		{	
			$redirectPage                   = "../../epr/WEPR004.php?prNo=".$paramPrNo;
            $_SESSION['EPSAuthority']       ='EPSApprovePrScreen';
            //$_SESSION['prScreen']           = 'ApprovalPrScreen';
            //$_SESSION['prNoSession']        = $paramPrNo;
            //$_SESSION['prStatusSession']    = $prStatus;
        }
        else if($prStatus == constant('1020') && trim($approver) == trim($sUserId) && $chargedBu == '' && trim($sWarga) == 'I' && $sCompanyCd != 'S')
		//else if($prStatus == constant('1020') && trim($approver) == trim($sUserId) && $chargedBu == '' && trim($sWarga) == 'I' && (int)$lembg < (int)'091')
        //else if($prStatus == constant('1020') && trim($approver) == trim($sUserId) && $chargedBu == '' && trim($sWarga) == 'I' && (int)$lembg < (int)'091' && strlen(trim($lembg)) == 3)
		{	
			$redirectPage                   = "../../epr_/WEPR004.php?prNo=".$paramPrNo;
            //$_SESSION['EPSAuthority']       ='EPSApprovePrScreen';
            $_SESSION['prScreen']           = 'ApprovalPrScreen';
            $_SESSION['prNoSession']        = $paramPrNo;
            $_SESSION['prStatusSession']    = $prStatus;
        }
        else if($prStatus == constant('1020') && trim($approver) == trim($sUserId) && $chargedBu == '' && (trim($sWarga) == 'A' || $sCompanyCd == 'S' ) )
        //else if($prStatus == constant('1020') && trim($approver) == trim($sUserId) && (trim($sWarga) == 'A' || (trim($sWarga) == 'I' && (int)$lembg >= (int)'091')))
        //else if($prStatus == constant('1020') && trim($approver) == trim($sUserId) && (trim($sWarga) == 'A' || (trim($sWarga) == 'I' && (int)$lembg >= (int)'091') || (trim($sWarga) == 'I' && strlen(trim($lembg)) == 2)))
        {
            $redirectPage                   = "../../epr_/WEPR004.php?prNo=".$paramPrNo;
            $_SESSION['prScreen']           = 'ApprovalPrScreen';
            $_SESSION['prNoSession']        = $paramPrNo;
            $_SESSION['prStatusSession']    = $prStatus;
        }
        else if($prStatus == constant('1020') && $approverNo > $prApproverNo && $approverPr != 0 && $chargedBu == '')
		{
            $redirectPage                   = "../../epr_/WEPR005.php?prNo=".$paramPrNo;
            $_SESSION['EPSAuthority']       ='EPSTakeOverPrScreen';
            $_SESSION['prNoSession']        = $paramPrNo;
            $_SESSION['prStatusSession']    = $prStatus;
        }
        else if($prStatus == constant('1030') && ($roleId == 'ROLE_02' || $roleId == 'ROLE_04'))
        {
            $redirectPage                   = "PO_Screen.php?criteria=prDetail&paramPrNo=".$paramPrNo;
        }
        else
        {
            $redirectPage                   = "../../epr_/WEPR006.php?paramPrNo=".$paramPrNo;
            $_SESSION['prScreen']           = 'DetailAcceptPrScreen';
            $_SESSION['prNoSession']        = $paramPrNo;
            $_SESSION['prStatusSession']    = $prStatus;
        }
		echo "<script>document.location.href='".$redirectPage."';</script>";
		//echo "$redirectPage";
    }
    else
    {
?>
    <script language="javascript"> alert("Sorry, your session to EPS has expired. Please login again.");
     document.location="../Login/Logout.php"; </script>
<?php
    }
    
}
else
{
?>
    <script language="javascript"> document.location="../Login/Logout.php"; </script>
<?php   
}
?>

