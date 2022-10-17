<?php session_start(); 
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Constant.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Common.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Email/PR_EMAIL.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Email/PO_EMAIL.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/lib/mail_lib/crypt.php";
require $_SERVER['DOCUMENT_ROOT'].'/EPS/lib/mail_lib/class.phpmailer.php';
require $_SERVER['DOCUMENT_ROOT'].'/EPS/lib/mail_lib/class.smtp.php';
if(isset($_SESSION['sUserId']))
{    
    $sUserId    = $_SESSION['sUserId'];
    
    if(trim($sUserId) != '')
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
        $sBuLogin   = $_SESSION['sBuLogin'];
        $sUserType  = $_SESSION['sUserType'];
        $action     = $_GET['action'];
        $deviceId   = $_SERVER['REMOTE_ADDR'];
        
        if($action == 'ApprovePo')
		{
            /**
             * Unset SESSION 
             **/
            unset($_SESSION['poStatus']);
                
            $poNo               = trim($_GET['poNoPrm']);
            $remarkApp          = strtoupper(trim($_GET['remarkAppPrm']));
            $remarkApp          = str_replace("'", "''", $remarkApp);
            $addRemark          = strtoupper(trim($_GET['addRemarkPrm']));
            $addRemark          = str_replace("'", "''", $addRemark);
            $npkApproverArray   = $_GET['npkApproverArray'];
            $newNpkApproverArray= explode(",", $npkApproverArray);
            
            /**
             *  UPDATE EPS_T_PO_HEADER
             */
            $query_update_po_header_sts = "update
                                            EPS_T_PO_HEADER
                                       set
                                            PO_STATUS = '".constant('1220')."'
                                            ,ADDITIONAL_REMARK = '$addRemark'
                                            ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                            ,UPDATE_BY = '$sUserId'
                                       where
                                            PO_NO = '$poNo'";
            $conn->query($query_update_po_header_sts);
            
            /**
             *  SELECT EPS_T_PO_APPROVER & EPS_T_PO_HEADER
             */
            $query_select_t_po_header_app = "select 
                                                EPS_T_PO_HEADER.APPROVER
                                                ,EPS_T_PO_HEADER.SUPPLIER_CD
                                                ,EPS_T_PO_HEADER.ISSUED_BY
                                                ,EPS_T_PO_APPROVER.APPROVER_NO
                                                ,(select count(*)
                                                from          
                                                    EPS_T_PO_APPROVER
                                                where      
                                                    EPS_T_PO_APPROVER.PO_NO = '$poNo') as COUNT_APPROVER
                                            from
                                                EPS_T_PO_HEADER
                                            left join
                                                EPS_T_PO_APPROVER
                                            on
                                                ltrim(EPS_T_PO_HEADER.APPROVER) = ltrim(EPS_T_PO_APPROVER.NPK)
                                                and EPS_T_PO_HEADER.PO_NO = EPS_T_PO_APPROVER.PO_NO
                                            where
                                                EPS_T_PO_HEADER.PO_NO = '$poNo'
                                                and EPS_T_PO_APPROVER.APPROVAL_STATUS = '".constant('WA')."'";
            $sql_select_t_po_header_app = $conn->query($query_select_t_po_header_app);
            $row_select_t_po_header_app = $sql_select_t_po_header_app->fetch(PDO::FETCH_ASSOC);
            $approver       = $row_select_t_po_header_app['APPROVER'];
            $approverNo     = $row_select_t_po_header_app['APPROVER_NO'];
            $countApprover  = $row_select_t_po_header_app['COUNT_APPROVER'];
            $supplierCd     = $row_select_t_po_header_app['SUPPLIER_CD'];
			$issuedBy       = $row_select_t_po_header_app['ISSUED_BY'];
            
            /**
             *  UPDATE EPS_T_PO_APPROVER TO APPROVED
             */
            $query_update_po_approver_sts_app = "update 
                                            EPS_T_PO_APPROVER 
                                         set 
                                            APPROVAL_STATUS = '".constant('AP')."'
                                            ,APPROVAL_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                            ,APPROVAL_REMARK = '$remarkApp'
                                            ,DEVICE_ADDRESS = '$deviceId'
                                            ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                            ,UPDATE_BY = '$sUserId'
                                         where 
                                            PO_NO = '$poNo' 
                                            and ltrim(NPK) = ltrim('$sUserId')
                                            and APPROVER_NO = '$approverNo'";
            $conn->query($query_update_po_approver_sts_app);
    
            /** 
             *  UPDATE EPS_T_PO_APPROVER TO NEW APPROVER
             **/
            for($x = 0; $x < count($newNpkApproverArray); $x++){
                $approverNoArray = substr($newNpkApproverArray[$x],0,1);
                if($approverNoArray > $approverNo){
                    $approverNpkArray = substr($newNpkApproverArray[$x],2);
                    $query_update_t_po_approver_npk = "update
                                                        EPS_T_PO_APPROVER
                                                       set
                                                        NPK = '$approverNpkArray'
                                                        ,CREATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                        ,CREATE_BY = '$sUserId'
                                                        ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                        ,UPDATE_BY = '$sUserId'
                                                       where
                                                        PO_NO = '$poNo'
                                                        and APPROVER_NO = '$approverNoArray'";
                    $conn->query($query_update_t_po_approver_npk);
                }
            }
            
            // If not last approver    
            if($approverNo < $countApprover)
            {
                $newApproverNo = $approverNo+1;
                // Select in EPS_T_PO_APPROVER to get next approver
                $query_select_t_approver = "select 
                                                APPROVER_NO
                                                ,NPK
                                                ,APPROVAL_STATUS
                                            from 
                                                EPS_T_PO_APPROVER 
                                            where 
                                                PO_NO = '$poNo' 
                                                and APPROVER_NO >= '$newApproverNo'
                                            order by
                                                APPROVER_NO";
                $sql_select_t_approver = $conn->query($query_select_t_approver);
                while($row_select_t_approver = $sql_select_t_approver->fetch(PDO::FETCH_ASSOC)){
                    if($row_select_t_approver['APPROVAL_STATUS'] != constant('BP')){
                        $newApprover    = $row_select_t_approver['NPK'];        // not trim agar save di DB sesuai format
                        $newApproverNo  = $row_select_t_approver['APPROVER_NO'];
                        break;
                    }
                }
                
                /** 
                 * UPDATE EPS_T_PO_APPROVER 
                 */
                $query_update_t_approver_wa = "update 
                                                EPS_T_PO_APPROVER 
                                            set 
                                                APPROVAL_STATUS = '".constant('WA')."'
                                                ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                ,UPDATE_BY = '$sUserId'
                                            where 
                                                PO_NO = '$poNo' 
                                                and NPK ='$newApprover'
                                                and APPROVER_NO = '$newApproverNo'";
                $conn ->query($query_update_t_approver_wa);
                
                /** 
                 * UPDATE EPS_T_PR_HEADER 
                 */
                $query_update_t_po_header = "update 
                                                EPS_T_PO_HEADER 
                                            set 
                                                APPROVER = '$newApprover' 
                                                ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                ,UPDATE_BY = '$sUserId'
                                            where 
                                                PO_NO = '$poNo' ";
                $conn ->query($query_update_t_po_header);
                $approver = $newApprover;
                
                /**********************************************************************
                 * SEND MAIL
                 **********************************************************************/
                $mailFrom       = $sInet;
                $mailFromName   = $sNotes;  

                /**
                 * TO NEXT APPROVER
                 **/
                $approvalStatus = "Waiting for Approval";
                $query_m_dscid = "select 
                                    EPS_M_DSCID.INETML
                                    ,EPS_M_USER.PASSWORD 
                                from 
                                    EPS_M_DSCID 
                                inner join 
                                    EPS_M_USER 
                                on 
                                    ltrim(EPS_M_DSCID.INOPOK) = ltrim(EPS_M_USER.NPK) 
                                where  
                                    ltrim(ltrim(EPS_M_DSCID.INOPOK)) = ltrim('".$approver."')";
                $sql_m_dscid = $conn->query($query_m_dscid);
                $row_m_dscid = $sql_m_dscid->fetch(PDO::FETCH_ASSOC);
                if($row_m_dscid){
                    $mailTo    = $row_m_dscid['INETML'];
                    //$mailTo      = 'muh.iqbal@taci.toyota-industries.com';
                    $passwordApprover  = $row_m_dscid['PASSWORD'];
                    $getParamLink  = paramEncrypt("action=open&poNo=$poNo&userId=$approver&password=$passwordApprover");
                    $mailSubject = "[EPS] WAITING APPROVAL. PO No: ".$poNo;
                    $mailMessage = "<table style='font-family: Arial; font-size: 12px;'>";
                    $mailMessage .= "<tr><td>PO No</td><td>: </td><td>".$poNo."</td></tr>";
                    $mailMessage .= "<tr><td>Approval Status</td><td>:</td><td>".$approvalStatus."</td></tr>";
                    $mailMessage .= "</table></font>";
                    //$mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage
                    //poSendMail($poNo, $mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage);
                    
                    $query_send_mail = "insert into 
                                            EPS_T_PR_MAIL 
                                            (
                                            PR_NO
                                            ,COMPANY 
                                            ,PR_ISSUER
                                            ,PR_CHARGED
                                            ,REQUESTER_MAIL
                                            ,REQUESTER_MAIL_NAME
                                            ,NEW_APPROVER
                                            ,GET_PARAM_APPROVER
                                            ,SENT
                                            ,SUBJECT_MAIL
                                            ,PR_STATUS
                                            ,APPROVAL_STATUS
                                            ) 
                                    VALUES
                                            (
                                            '$poNo'
                                            ,'$company'
                                            ,'$prIssuer'
                                            ,'$prCharged'
                                            ,'$mailFrom'
                                            ,'$mailFromName'
                                            ,'$mailTo'
                                            ,'$getParamLink'
                                            ,'2'
                                            ,'$mailSubject'
                                            ,'$prStatus'
                                            ,'$approvalStatus'
)";
		$sql= $conn->query($query_send_mail);
		$row = $sql->fetch(PDO::FETCH_ASSOC);
                }
				
				/**
                 * TO REQUESTER
                 */
                if($remarkApp != '')
                {
                    $approvalStatus = "Approved (With Remark)";
                    $query_m_dscid = "select 
                                        EPS_M_DSCID.INETML
                                        ,EPS_M_USER.PASSWORD 
                                    from 
                                        EPS_M_DSCID 
                                    inner join 
                                        EPS_M_USER 
                                    on 
                                        ltrim(EPS_M_DSCID.INOPOK) = ltrim(EPS_M_USER.NPK) 
                                    where  
                                        ltrim(ltrim(EPS_M_DSCID.INOPOK)) = ltrim('".$issuedBy."')";
                    $sql_m_dscid = $conn->query($query_m_dscid);
                    $row_m_dscid = $sql_m_dscid->fetch(PDO::FETCH_ASSOC);
                    if($row_m_dscid){
                        $mailTo    = $row_m_dscid['INETML'];
                        //$mailTo      = 'BYAN_PURBA@denso.co.id';
                        $passwordIssued  = $row_m_dscid['PASSWORD'];
                        $getParamLink  = paramEncrypt("action=open&poNo=$poNo&userId=$issuedBy&password=$passwordIssued");
                        $mailSubject = "[EPS] APPROVED (WITH REMARK). PO No: ".$poNo;
                        $mailMessage = "<table style='font-family: Arial; font-size: 12px;'>";
                        $mailMessage .= "<tr><td>PO No</td><td>: </td><td>".$poNo."</td></tr>";
                        $mailMessage .= "<tr><td>Approval Status</td><td>:</td><td>".$approvalStatus."</td></tr>";
                        $mailMessage .= "<tr><td>Comment</td><td>:</td><td>".$remarkApp."</td></tr>";
                        $mailMessage .= "</table>";
                        //$mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage
                        //poSendMail($poNo, $mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage);
                        $query_send_mail = "insert into 
                                            EPS_T_PR_MAIL 
                                            (
                                            PR_NO
                                            ,COMPANY 
                                            ,PR_ISSUER
                                            ,PR_CHARGED
                                            ,REQUESTER_MAIL
                                            ,REQUESTER_MAIL_NAME
                                            ,NEW_APPROVER
                                            ,GET_PARAM_APPROVER
                                            ,SENT
                                            ,SUBJECT_MAIL
                                            ,PR_STATUS
                                            ,APPROVAL_STATUS
                                            ) 
                                    VALUES
                                            (
                                            '$poNo'
                                            ,'$company'
                                            ,'$prIssuer'
                                            ,'$prCharged'
                                            ,'$mailFrom'
                                            ,'$mailFromName'
                                            ,'$mailTo'
                                            ,'$getParamLink'
                                            ,'2'
                                            ,'$mailSubject'
                                            ,'$prStatus'
                                            ,'$approvalStatus'
)";
		$sql= $conn->query($query_send_mail);
		$row = $sql->fetch(PDO::FETCH_ASSOC);
                    }
                }
            }
            else
            {
                $approver = '';
                /** 
                 * UPDATE EPS_T_PO_HEADER 
                 */
                $query_update_t_po_header_last = "update 
                                                    EPS_T_PO_HEADER 
                                                set 
                                                    APPROVER = '$approver'
                                                    ,PO_STATUS = '".constant('1230')."'
                                                    ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                    ,UPDATE_BY = '$sUserId'
                                                where 
                                                    PO_NO = '$poNo'";
                $conn ->query($query_update_t_po_header_last);
				
				/**********************************************************************
                 * SEND MAIL
                 **********************************************************************/
                $mailFrom       = $sInet;
                $mailFromName   = $sNotes;  

                /**
                 * TO REQUESTER
                 */
                if($remarkApp != '')
                {
                    $approvalStatus = "Approved (With Remark)";
                    $query_m_dscid = "select 
                                        EPS_M_DSCID.INETML
                                        ,EPS_M_USER.PASSWORD 
                                    from 
                                        EPS_M_DSCID 
                                    inner join 
                                        EPS_M_USER 
                                    on 
                                        ltrim(EPS_M_DSCID.INOPOK) = ltrim(EPS_M_USER.NPK) 
                                    where  
                                        ltrim(ltrim(EPS_M_DSCID.INOPOK)) = ltrim('".$issuedBy."')";
                    $sql_m_dscid = $conn->query($query_m_dscid);
                    $row_m_dscid = $sql_m_dscid->fetch(PDO::FETCH_ASSOC);
                    if($row_m_dscid){
                        $mailTo    = $row_m_dscid['INETML'];
                        //$mailTo      = 'BYAN_PURBA@denso.co.id';
                        $passwordIssued  = $row_m_dscid['PASSWORD'];
                        $getParamLink  = paramEncrypt("action=open&poNo=$poNo&userId=$issuedBy&password=$passwordIssued");
                        $mailSubject = "[EPS] APPROVED (WITH REMARK). PO No: ".$poNo;
                        $mailMessage = "<table style='font-family: Arial; font-size: 12px;'>";
                        $mailMessage .= "<tr><td>PO No</td><td>: </td><td>".$poNo."</td></tr>";
                        $mailMessage .= "<tr><td>Approval Status</td><td>:</td><td>".$approvalStatus."</td></tr>";
                        $mailMessage .= "<tr><td>Comment</td><td>:</td><td>".$remarkApp."</td></tr>";
                        $mailMessage .= "</table>";
                        //$mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage
                        //poSendMail($poNo, $mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage);
                        $query_send_mail = "insert into 
                                            EPS_T_PR_MAIL 
                                            (
                                            PR_NO
                                            ,COMPANY 
                                            ,PR_ISSUER
                                            ,PR_CHARGED
                                            ,REQUESTER_MAIL
                                            ,REQUESTER_MAIL_NAME
                                            ,NEW_APPROVER
                                            ,GET_PARAM_APPROVER
                                            ,SENT
                                            ,SUBJECT_MAIL
                                            ,PR_STATUS
                                            ,APPROVAL_STATUS
                                            ) 
                                    VALUES
                                            (
                                            '$poNo'
                                            ,'$company'
                                            ,'$prIssuer'
                                            ,'$prCharged'
                                            ,'$mailFrom'
                                            ,'$mailFromName'
                                            ,'$mailTo'
                                            ,'$getParamLink'
                                            ,'2'
                                            ,'$mailSubject'
                                            ,'$prStatus'
                                            ,'$approvalStatus'
)";
		$sql= $conn->query($query_send_mail);
		$row = $sql->fetch(PDO::FETCH_ASSOC);
                    }
                }
            }
            $msg = "Success";
        }
		
        if($action == 'ApprovePoArray')
        {
            $userIdApprover = trim($_GET['userIdApproverPrm']);
            $poNoArray    = trim($_GET['poNoArray']);
            $newPoNoArray = explode(",", $poNoArray);
            if($sUserId == "")
            {
                $sUserId = $userIdApprover;
            }
            for($x = 0; $x < count($newPoNoArray); $x++)
			{
                $poNoVal = "";
                $poNoVal =  $newPoNoArray[$x];
                
                /**
                 *  UPDATE EPS_T_PO_HEADER
                 */
                $query_update_po_header = "update
                                                EPS_T_PO_HEADER
											set
                                                PO_STATUS = '1220'
                                                ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                ,UPDATE_BY = '$sUserId'
											where
                                                PO_NO = '$poNoVal'";
                $conn->query($query_update_po_header);

                /**
                 *  SELECT EPS_T_PO_APPROVER & EPS_T_PO_HEADER
                 */
                $query_select_t_po_header_app = "select 
                                                    EPS_T_PO_HEADER.APPROVER
                                                    ,EPS_T_PO_HEADER.SUPPLIER_CD
                                                    ,EPS_T_PO_APPROVER.APPROVER_NO
                                                    ,(select count(*)
                                                    from          
                                                        EPS_T_PO_APPROVER
                                                    where      
                                                        EPS_T_PO_APPROVER.PO_NO = '$poNoVal') as COUNT_APPROVER
                                                from
                                                    EPS_T_PO_HEADER
                                                left join
                                                    EPS_T_PO_APPROVER
                                                on
                                                    ltrim(EPS_T_PO_HEADER.APPROVER) = ltrim(EPS_T_PO_APPROVER.NPK)
                                                    and EPS_T_PO_HEADER.PO_NO = EPS_T_PO_APPROVER.PO_NO
                                                where
                                                    EPS_T_PO_HEADER.PO_NO = '$poNoVal'
                                                    and EPS_T_PO_APPROVER.APPROVAL_STATUS = 'WA'";
                $sql_select_t_po_header_app = $conn->query($query_select_t_po_header_app);
                $row_select_t_po_header_app = $sql_select_t_po_header_app->fetch(PDO::FETCH_ASSOC);
                $approver       = $row_select_t_po_header_app['APPROVER'];
                $approverNo     = $row_select_t_po_header_app['APPROVER_NO'];
                $countApprover  = $row_select_t_po_header_app['COUNT_APPROVER'];
                $supplierCd     = $row_select_t_po_header_app['SUPPLIER_CD'];
                
                /**
                 *  UPDATE EPS_T_PO_APPROVER TO APPROVED
                 */
                $query_update_po_approver = "update 
                                                EPS_T_PO_APPROVER 
                                            set 
                                                APPROVAL_STATUS = 'AP'
                                                ,APPROVAL_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                ,DEVICE_ADDRESS = '$deviceId'
                                                ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                ,UPDATE_BY = '$sUserId'
                                            where 
                                                PO_NO = '$poNoVal' 
                                                and ltrim(NPK) = ltrim('$sUserId')
                                                and APPROVER_NO = '$approverNo'";
                $conn->query($query_update_po_approver);

			   /**
                * SELECT EPS_T_PO_APPROVER
                **/
                $query_select_t_po_approver_last = "select
                                                        APPROVAL_STATUS
                                                    from
                                                        EPS_T_PO_APPROVER
                                                    where
                                                        PO_NO = '$poNoVal' 
                                                        and APPROVER_NO = '$countApprover'";
                $sql_select_t_po_approver_last = $conn->query($query_select_t_po_approver_last);
                $row_select_t_po_approver_last = $sql_select_t_po_approver_last->fetch(PDO::FETCH_ASSOC);
                $approverStatusLast = $row_select_t_po_approver_last['APPROVAL_STATUS'];
				
                //if((int)$approverNo == (int)$countApprover && $approverStatusLast == "AP")
				if(intval($approverNo) == intval($countApprover) && $approverStatusLast == "AP")
                {
                    $approver = '';
                   /** 
                    * UPDATE EPS_T_PO_HEADER 
                    */
                    $query_update_t_po_header_sent = "update 
                                                    EPS_T_PO_HEADER 
                                                set 
                                                    APPROVER = '$approver'
                                                    ,PO_STATUS = '1230'
                                                    ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                    ,UPDATE_BY = '$sUserId'
                                                where 
                                                    PO_NO = '$poNoVal'";
                    $conn->query($query_update_t_po_header_sent);
                }
                else
                {
					// If not last approver    
					if($approverNo < $countApprover)
					{
                    $newApproverNo = $approverNo+1;
                    // Select in EPS_T_PO_APPROVER to get next approver
                    $query_select_t_approver = "select 
													APPROVER_NO
													,NPK
													,APPROVAL_STATUS
												from 
													EPS_T_PO_APPROVER 
												where 
													PO_NO = '$poNoVal' 
													and APPROVER_NO >= '$newApproverNo'
												order by
													APPROVER_NO";
					$sql_select_t_approver = $conn->query($query_select_t_approver);
					while($row_select_t_approver = $sql_select_t_approver->fetch(PDO::FETCH_ASSOC)){
						if($row_select_t_approver['APPROVAL_STATUS'] != constant('BP')){
							$newApprover    = $row_select_t_approver['NPK'];        // not trim agar save di DB sesuai format
							$newApproverNo  = $row_select_t_approver['APPROVER_NO'];
							break;
						}
					}

                   /** 
                    * UPDATE EPS_T_PO_APPROVER 
                    */
                    $query_update_po_approver_new_sts = "update 
                                                            EPS_T_PO_APPROVER 
                                                        set 
                                                            APPROVAL_STATUS = '".constant('WA')."'
                                                            ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                            ,UPDATE_BY = '$sUserId'
                                                        where 
                                                            PO_NO = '$poNoVal' 
                                                            and NPK = '$newApprover'
                                                            and APPROVER_NO = '$newApproverNo'";
                    $conn->query($query_update_po_approver_new_sts);

                   /** 
                    * UPDATE EPS_T_PR_HEADER 
                    */
                    $query_update_po_header_new_app = "update 
                                                            EPS_T_PO_HEADER 
                                                        set 
                                                            APPROVER = '$newApprover' 
                                                            ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                            ,UPDATE_BY = '$sUserId'
                                                        where 
                                                            PO_NO = '$poNoVal' ";
                    $conn->query($query_update_po_header_new_app);
                    $approver = $newApprover;

                   /**********************************************************************
                    * SEND MAIL
                    **********************************************************************/
                    $mailFrom       = $sInet;
                    $mailFromName   = $sNotes;  

                   /**
                    * TO NEXT APPROVER
                    **/
                    $approvalStatus = "Waiting for Approval";
                    $query_m_dscid = "select 
                                        EPS_M_DSCID.INETML
                                        ,EPS_M_USER.PASSWORD 
                                    from 
                                        EPS_M_DSCID 
                                    inner join 
                                        EPS_M_USER 
                                    on 
                                        ltrim(EPS_M_DSCID.INOPOK) = ltrim(EPS_M_USER.NPK) 
                                    where  
                                        ltrim(ltrim(EPS_M_DSCID.INOPOK)) = ltrim('".$approver."')";
                    $sql_m_dscid = $conn->query($query_m_dscid);
                    $row_m_dscid = $sql_m_dscid->fetch(PDO::FETCH_ASSOC);
                    if($row_m_dscid){
                        $mailTo    = $row_m_dscid['INETML'];
                        //$mailTo      = 'BYAN_PURBA@denso.co.id';
                        $passwordApprover  = $row_m_dscid['PASSWORD'];
                        $getParamLink  = paramEncrypt("action=open&poNo=$poNoVal&userId=$approver&password=$passwordApprover");
                        $mailSubject = "[EPS] WAITING APPROVAL. PO No: ".$poNoVal;
                        $mailMessage = "<table style='font-family: Arial; font-size: 12px;'>";
                        $mailMessage .= "<tr><td>PO No</td><td>: </td><td>".$poNoVal."</td></tr>";
                        $mailMessage .= "<tr><td>Approval Status</td><td>:</td><td>".$approvalStatus."</td></tr>";
                        $mailMessage .= "</table>";
                        //$mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage
                        //Di ganti menggunakan VB
                        //poSendMail($poNoVal, $mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage);
                         $query_send_mail = "insert into 
                                            EPS_T_PR_MAIL 
                                            (
                                            PR_NO
                                            ,COMPANY 
                                            ,PR_ISSUER
                                            ,PR_CHARGED
                                            ,REQUESTER_MAIL
                                            ,REQUESTER_MAIL_NAME
                                            ,NEW_APPROVER
                                            ,GET_PARAM_APPROVER
                                            ,SENT
                                            ,SUBJECT_MAIL
                                            ,PR_STATUS
                                            ,APPROVAL_STATUS
                                            ) 
                                    VALUES
                                            (
                                            '$poNoVal'
                                            ,'$company'
                                            ,'$prIssuer'
                                            ,'$prCharged'
                                            ,'$mailFrom'
                                            ,'$mailFromName'
                                            ,'$mailTo'
                                            ,'$getParamLink'
                                            ,'2'
                                            ,'$mailSubject'
                                            ,'$prStatus'
                                            ,'$approvalStatus'
)";
		$sql= $conn->query($query_send_mail);
		$row = $sql->fetch(PDO::FETCH_ASSOC);
                    }
					}
                }
                // If not last approver    
                /** if((int)$approverNo < (int)$countApprover)
                {
                    $newApproverNo = $approverNo+1;
                    // Select in EPS_T_PO_APPROVER to get next approver
                    $query = "select 
                                    APPROVER_NO
                                    ,NPK
                                    ,APPROVAL_STATUS
                                from 
                                    EPS_T_PO_APPROVER 
                                where 
                                    PO_NO = '$poNoVal' 
                                    and APPROVER_NO >= '$newApproverNo'
                                order by
                                    APPROVER_NO";
                    $sql = $conn->query($query);
                    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                        if($row['APPROVAL_STATUS'] != constant('BP')){
                            $newApprover = $row['NPK'];        // not trim agar save di DB sesuai format
                            $newApproverNo = $row['APPROVER_NO'];
                            break;
                        }
                    }

                   /** 
                    * UPDATE EPS_T_PO_APPROVER 
                    */
                    /**$query = "update 
                                EPS_T_PO_APPROVER 
                            set 
                                APPROVAL_STATUS = '".constant('WA')."'
                                ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                ,UPDATE_BY = '$sUserId'
                            where 
                                PO_NO = '$poNoVal' 
                                and NPK = '$newApprover'
                                and APPROVER_NO = '$newApproverNo'";
                    $sql = $conn ->query($query);

                   /** 
                    * UPDATE EPS_T_PR_HEADER 
                    */
                    /**$query = "update 
                                EPS_T_PO_HEADER 
                            set 
                                APPROVER = '$newApprover' 
                                ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                ,UPDATE_BY = '$sUserId'
                            where 
                                PO_NO = '$poNoVal' ";
                    $sql = $conn ->query($query);
                    $approver = $newApprover;

                   /**********************************************************************
                    * SEND MAIL
                    **********************************************************************/
                    /**$mailFrom       = $sInet;
                    $mailFromName   = $sNotes;  

                   /**
                    * TO NEXT APPROVER
                    **/
                    /**$approvalStatus = "Waiting for Approval";
                    $query_m_dscid = "select 
                                        EPS_M_DSCID.INETML
                                        ,EPS_M_USER.PASSWORD 
                                    from 
                                        EPS_M_DSCID 
                                    inner join 
                                        EPS_M_USER 
                                    on 
                                        ltrim(EPS_M_DSCID.INOPOK) = ltrim(EPS_M_USER.NPK) 
                                    where  
                                        ltrim(ltrim(EPS_M_DSCID.INOPOK)) = ltrim('".$approver."')";
                    $sql_m_dscid = $conn->query($query_m_dscid);
                    $row_m_dscid = $sql_m_dscid->fetch(PDO::FETCH_ASSOC);
                    if($row_m_dscid){
                        $mailTo    = $row_m_dscid['INETML'];
                        //$mailTo      = 'BYAN_PURBA@denso.co.id';
                        $passwordApprover  = $row_m_dscid['PASSWORD'];
                        $getParamLink  = paramEncrypt("action=open&poNo=$poNoVal&userId=$approver&password=$passwordApprover");
                        $mailSubject = "WAITING APPROVAL. PO No: ".$poNoVal;
                        $mailMessage = "<table style='font-family: Arial; font-size: 12px;'>";
                        $mailMessage .= "<tr><td>PO No</td><td>: </td><td>".$poNoVal."</td></tr>";
                        $mailMessage .= "<tr><td>Approval Status</td><td>:</td><td>".$approvalStatus."</td></tr>";
                        $mailMessage .= "</table>";
                        //$mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage
                        poSendMail($poNoVal, $mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage);
                    }
                }
                else
                {
                    $approver = '';
                    /** 
                    * UPDATE EPS_T_PO_HEADER 
                    */
                    /**$query_update_t_po_header = "update 
                                                    EPS_T_PO_HEADER 
                                                set 
                                                    APPROVER = '$approver'
                                                    ,PO_STATUS = '".constant('1230')."'
                                                    ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                    ,UPDATE_BY = '$sUserId'
                                                where 
                                                    PO_NO = '$poNoVal'";
                    $conn ->query($query_update_t_po_header);
                }**/
            }
            $msg = 'Success';
        }
        
        if($action == 'RejectPo')
		{
            $poNo       = trim($_GET['poNoPrm']);
            $remarkApp  = strtoupper(trim($_GET['remarkAppPrm']));
            $remarkApp  = str_replace("'", "''", $remarkApp);
            
            if($remarkApp != '')
            {
				/**
                 * Unset SESSION 
                 **/
                unset($_SESSION['poStatus']);
                
                /**
                 *  UPDATE EPS_T_PO_HEADER
                 **/
                $query_update_po_header = "update
                                                EPS_T_PO_HEADER
                                            set
                                                PO_STATUS = '".constant('1240')."'
                                                ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                ,UPDATE_BY = '$sUserId'
                                            where
                                                PO_NO = '$poNo'";
                $sql_update_po_header = $conn->query($query_update_po_header);
				/**
                 * SELECT EPS_T_PO_DETAIL
                 **/
                $query_select_po_detail = "select
                                                REF_TRANSFER_ID
                                                ,QTY
                                           from
                                                EPS_T_PO_DETAIL
                                           where
                                                PO_NO = '$poNo'";
                $sql_select_po_detail = $conn->query($query_select_po_detail);
                while($row_select_po_detail = $sql_select_po_detail->fetch(PDO::FETCH_ASSOC)){
                    $refTransferId  = $row_select_po_detail['REF_TRANSFER_ID'];
                    $qtyPo          = $row_select_po_detail['QTY'];
                    
                    /**
                     * SELECT EPS_T_TRANSFER
                     **/
                    $query_select_t_transfer = "select
                                                    NEW_QTY
                                                    ,ACTUAL_QTY
                                                from
                                                    EPS_T_TRANSFER
                                                where 
                                                    TRANSFER_ID = '$refTransferId'";
                    $sql_select_t_transfer = $conn->query($query_select_t_transfer);
                    $row_select_t_transfer = $sql_select_t_transfer->fetch(PDO::FETCH_ASSOC);
                    $qtyTransfer    = $row_select_t_transfer['NEW_QTY'];
                    $qtyActual      = $row_select_t_transfer['ACTUAL_QTY'];
					
					/**
                    * UPDATE PROGRAM FOR PARTIAL QTY ISSUE
                    * UPDATED BY   : BYAN PURBAPRANIDHANA
                    * UPDATE DATE  : OCT 10, 2015. 14.40
                    **/
                    $query_select_t_po_detail = "select
                                                    sum(QTY) as ACTUAL_QTY
                                                from         
                                                    EPS_T_PO_DETAIL
                                                left join
                                                    EPS_T_PO_HEADER
                                                on 
                                                    EPS_T_PO_HEADER.PO_NO = EPS_T_PO_DETAIL.PO_NO
                                                where     
                                                    EPS_T_PO_DETAIL.REF_TRANSFER_ID = '$refTransferId'
                                                    and EPS_T_PO_DETAIL.PO_NO != '$poNo'
                                                    and EPS_T_PO_HEADER.PO_STATUS in ('1210','1220','1230','1250','1280','1330')";
                    $sql_select_t_po_detail = $conn->query($query_select_t_po_detail);
                    $row_select_t_po_detail = $sql_select_t_po_detail->fetch(PDO::FETCH_ASSOC);
                    $actualPoQty = $row_select_t_po_detail['ACTUAL_QTY'];
                    $newActualQty = $actualPoQty;
					
					if($actualPoQty == $qtyActual)
                    {
                        $itemStatus     = '1130';
                    }
                    else
                    {
                        if($newActualQty == 0)
                        {
                            $itemStatus     = '1130';
                            $newActualQty   = $qtyTransfer;
                        }
                        else
                        {
                            $itemStatus     = '1170';
                        }
                    }
					
                    /*if($qtyTransfer != $qtyPo)
                    {
                        $itemStatus = '1170';
                        $newActualQty = $qtyActual - $qtyPo;
                        $newActualAmount= $newActualQty * $itemPriceVal;
                        if($newActualQty == 0)
                        {
						   /**
                            * UPDATE PROGRAM FOR PARTIAL QTY ISSUE
                            * UPDATED BY   : BYAN PURBAPRANIDHANA
                            * UPDATE DATE  : SEP 29, 2015. 16.00
                            *
                            $itemStatus     = '1130';
                            $newActualQty 	= $qtyTransfer;
                            $newActualAmount= $newActualQty * $itemPriceVal;
                        }
                    }
                    else
                    {
                        $itemStatus = '1130';
                        $newActualQty = $qtyPo;
                        $newActualAmount= $newActualQty * $itemPriceVal;
                    }*/
					
                    /**
                     * UPDATE EPS_T_TRANSFER
                     **/
                    $query_update_t_transfer = "update
                                                    EPS_T_TRANSFER
                                                set
                                                    ITEM_STATUS = '$itemStatus'
                                                    ,ACTUAL_QTY = '$newActualQty'
                                                    ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                    ,UPDATE_BY = '$sUserId'
                                                where
                                                    TRANSFER_ID = '$refTransferId'";
                    $sql_update_t_transfer = $conn->query($query_update_t_transfer);
                }

                /**
                 *  SELECT EPS_T_PO_APPROVER 
                 **/
                $query_select_po_approver = "select 
                                                APPROVER_NO 
                                            from
                                                EPS_T_PO_APPROVER
                                            where 
                                                PO_NO = '$poNo'
                                                and ltrim(NPK) = ltrim('$sUserId')
                                                and APPROVAL_STATUS = '".constant('WA')."'";
                $sql_select_po_approver = $conn->query($query_select_po_approver);
                $row_select_po_approver = $sql_select_po_approver->fetch(PDO::FETCH_ASSOC);
                $approverNo = $row_select_po_approver['APPROVER_NO'];

                /**
                 *  UPDATE EPS_T_PO_APPROVER
                 **/
                $query_update_po_approver = "update
                                                EPS_T_PO_APPROVER
                                            set
                                                APPROVAL_STATUS = '".constant('RE')."'
                                                ,APPROVAL_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                ,APPROVAL_REMARK = '$remarkApp'
                                                ,DEVICE_ADDRESS = '$deviceId'
                                                ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                ,UPDATE_BY = '$sUserId'
                                            where
                                                PO_NO = '$poNo'
                                                and ltrim(NPK) = ltrim('$sUserId')
                                                and APPROVER_NO = '$approverNo'";
                $sql_update_po_approver = $conn->query($query_update_po_approver);

                /**********************************************************************
                 * SEND MAIL
                 **********************************************************************/
                $mailFrom       = $sInet;
                $mailFromName   = $sNotes;  
                $approvalStatus = "Rejected";
                $poStatus       = "PO Rejected by Approver";
                
                /**
                 *  TO REQUESTER
                 **/ 
                /**
                 * SELECT EPS_T_PR_HEADER
                 **/
                $query_select_po_header = "select
                                            ISSUED_BY
                                           from
                                            EPS_T_PO_HEADER
                                           where
                                            PO_NO = '$poNo'";
                $sql_select_po_header = $conn->query($query_select_po_header);
                $row_select_po_header = $sql_select_po_header->fetch(PDO::FETCH_ASSOC);
                $poRequester = $row_select_po_header['ISSUED_BY'];
                
                $query_select_dscid = "select 
                                        EPS_M_DSCID.INETML
                                        ,EPS_M_USER.PASSWORD 
                                       from 
                                        EPS_M_DSCID 
                                       inner join 
                                        EPS_M_USER 
                                       on 
                                        ltrim(EPS_M_DSCID.INOPOK) = ltrim(EPS_M_USER.USERID) 
                                       where  
                                        ltrim(EPS_M_DSCID.INOPOK) = ltrim('".$poRequester."')";
                $sql_select_dscid = $conn->query($query_select_dscid);
                $row_select_dscid = $sql_select_dscid->fetch(PDO::FETCH_ASSOC);
                if($row_select_dscid){
                    $mailTo     = $row_select_dscid['INETML'];
                    //$mailTo       = 'BYAN_PURBA@denso.co.id';
                    $passwordRequester = $row_select_dscid['PASSWORD'];
                    $getParamLink       = paramEncrypt("action=open&poNo=$poNo&userId=$poRequester&password=$passwordRequester");
                    $mailSubject = "[EPS] REJECTED. PO No: ".$poNo;
                    $mailMessage = "<table style='font-family: Arial; font-size: 12px;'>";
                    $mailMessage .= "<tr><td>PO No</td><td>: </td><td>".$poNo."</td></tr>";
                    $mailMessage .= "<tr><td>PO Status</td><td>:</td><td>".$poStatus."</td></tr>";
                    $mailMessage .= "<tr><td>Comment</td><td>:</td><td>".$remarkApp."</td></tr>";
                    $mailMessage .= "</table></font>";
                    //$mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage
                    //poSendMail($poNo, $mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage);
                    
                    $query_send_mail = "insert into 
                                            EPS_T_PR_MAIL 
                                            (
                                            PR_NO
                                            ,COMPANY 
                                            ,PR_ISSUER
                                            ,PR_CHARGED
                                            ,REQUESTER_MAIL
                                            ,REQUESTER_MAIL_NAME
                                            ,NEW_APPROVER
                                            ,GET_PARAM_APPROVER
                                            ,SENT
                                            ,SUBJECT_MAIL
                                            ,PR_STATUS
                                            ,APPROVAL_STATUS
                                            ) 
                                    VALUES
                                            (
                                            '$poNo'
                                            ,'$company'
                                            ,'$prIssuer'
                                            ,'$prCharged'
                                            ,'$mailFrom'
                                            ,'$mailFromName'
                                            ,'$mailTo'
                                            ,'$getParamLink'
                                            ,'2'
                                            ,'$mailSubject'
                                            ,'$prStatus'
                                            ,'$approvalStatus'
)";
		$sql= $conn->query($query_send_mail);
		$row = $sql->fetch(PDO::FETCH_ASSOC);
                }
				
				/**
                 *  TO PREV APPROVER
                 **/ 
                $query_select_t_po_approver = "select 
                                                    NPK
                                                from
                                                    EPS_T_PO_APPROVER
                                                where 
                                                    PO_NO = '$poNo'
                                                    and APPROVER_NO < $approverNo";
                $sql_select_t_po_approver= $conn->query($query_select_t_po_approver);
                while($row_select_t_po_approver= $sql_select_t_po_approver->fetch(PDO::FETCH_ASSOC)){
                    $npkApprover = $row_select_t_po_approver['NPK'];
                    $query = "select 
                                    EPS_M_DSCID.INETML
                                    ,EPS_M_USER.PASSWORD 
                                from 
                                    EPS_M_DSCID 
                                inner join 
                                    EPS_M_USER 
                                on 
                                    ltrim(EPS_M_DSCID.INOPOK) = ltrim(EPS_M_USER.USERID) 
                                where  
                                    ltrim(EPS_M_DSCID.INOPOK) = ltrim('".$npkApprover."')";
                    $sql= $conn->query($query);
                    $row = $sql->fetch(PDO::FETCH_ASSOC);
                    if($row){
                        $mailTo      		= $row['INETML'];
                        //$mailTo             = 'BYAN_PURBA@denso.co.id';
                        $passwordApprover   = $row['PASSWORD'];
                        $getParamApprover   = paramEncrypt("action=open&poNo=$poNo&userId=$npkApprover&password=$passwordApprover");
                        $mailSubject = "[EPS] REJECTED. PO No: ".$poNo;
                        $mailMessage = "<table style='font-family: Arial; font-size: 12px;'>";
                        $mailMessage .= "<tr><td>PO No</td><td>: </td><td>".$poNo."</td></tr>";
                        $mailMessage .= "<tr><td>PO Status</td><td>:</td><td>".$poStatus."</td></tr>";
                        $mailMessage .= "<tr><td>Comment</td><td>:</td><td>".$remarkApp."</td></tr>";
                        $mailMessage .= "</table></font>";
                        //poSendMail($poNo, $mailTo, $mailFrom, $mailFromName, $getParamApprover, $mailSubject, $mailMessage);
                        $query_send_mail = "insert into 
                                            EPS_T_PR_MAIL 
                                            (
                                            PR_NO
                                            ,COMPANY 
                                            ,PR_ISSUER
                                            ,PR_CHARGED
                                            ,REQUESTER_MAIL
                                            ,REQUESTER_MAIL_NAME
                                            ,NEW_APPROVER
                                            ,GET_PARAM_APPROVER
                                            ,SENT
                                            ,SUBJECT_MAIL
                                            ,PR_STATUS
                                            ,APPROVAL_STATUS
                                            ) 
                                    VALUES
                                            (
                                            '$poNo'
                                            ,'$company'
                                            ,'$prIssuer'
                                            ,'$prCharged'
                                            ,'$mailFrom'
                                            ,'$mailFromName'
                                            ,'$mailTo'
                                            ,'$getParamLink'
                                            ,'2'
                                            ,'$mailSubject'
                                            ,'$prStatus'
                                            ,'$approvalStatus'
)";
		$sql= $conn->query($query_send_mail);
		$row = $sql->fetch(PDO::FETCH_ASSOC);
                    }
                }
                $msg = 'Success';
			}
            else
            {
				$msg = 'Mandatory_1';
            }
        }
		
		if($action == 'CancelPoAfterSent')
        { 
            $poNo               = trim($_GET['poNoPrm']);
            $remarkCancelPo     = strtoupper(trim($_GET['remarkCancelPoPrm']));
            $remarkCancelPo     = str_replace("'", "''", $remarkCancelPo);
            $updateDate         = strtoupper(trim($_GET['updateDatePrm']));
            $poItemData         = ($_SESSION['poDetail']);
            
            /**
             * SELECT EPS_T_PO_HEADER
             */
            $query_eps_t_po_header = "select
                                        EPS_T_PO_HEADER.SUPPLIER_CD
                                        ,EPS_T_PO_HEADER.ISSUED_BY
                                        ,EPS_M_EMPLOYEE.NAMA1 as ISSUED_NAME
                                        ,EPS_T_PO_HEADER.UPDATE_DATE
                                        ,EPS_M_DSCID.INETML
                                      from 
                                        EPS_T_PO_HEADER
                                      left join
                                        EPS_M_EMPLOYEE
                                      on
                                        EPS_T_PO_HEADER.ISSUED_BY = EPS_M_EMPLOYEE.NPK
                                      left join 
                                        EPS_M_DSCID
                                      on
                                        EPS_T_PO_HEADER.ISSUED_BY = EPS_M_DSCID.INOPOK
                                      where
                                        PO_NO = '$poNo'";
            $sql_eps_t_po_header = $conn->query($query_eps_t_po_header);
            $row_eps_t_po_header=$sql_eps_t_po_header->fetch(PDO::FETCH_ASSOC);
            $newUpdateDate = strtoupper($row_eps_t_po_header['UPDATE_DATE']);
            $supplierCd    = $row_eps_t_po_header['SUPPLIER_CD'];
            $issuedName    = $row_eps_t_po_header['ISSUED_NAME'];
            $issuedMail    = $row_eps_t_po_header['INETML'];
            
            if($updateDate == $newUpdateDate)
            {
                if($remarkCancelPo != '')
                {
                    /**
                     * UPDATE EPS_T_PO_HEADER
                     */
                    $query_update_po = "update
                                            EPS_T_PO_HEADER
                                        set
                                            PO_STATUS = '1340'
											,CLOSED_PO_MONTH = NULL
                                            ,REMARK_CANCEL_PO = '$remarkCancelPo'
                                            ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                            ,UPDATE_BY = '$sUserId'
                                        where
                                            PO_NO = '$poNo'";
                    $conn->query($query_update_po);

                   /**
                    * UPDATE EPS_T_PO_DETAIL
                    */
                    for($j = 0; $j < count($poItemData); $j++){
                        $transferIdVal  = $poItemData[$j]['refTransferId'];

                       /**
                        * SELECT EPS_T_TRANSFER
                        **/
                        $query_select_t_transfer = "select
                                                        NEW_QTY
                                                        ,ACTUAL_QTY
                                                    from
                                                        EPS_T_TRANSFER
                                                    where
                                                        TRANSFER_ID = '$transferIdVal'";
                        $sql_select_t_transfer = $conn->query($query_select_t_transfer);
                        $row_select_t_transfer = $sql_select_t_transfer->fetch(PDO::FETCH_ASSOC);
                        $qtyTransfer    = $row_select_t_transfer['NEW_QTY'];
                        $qtyActual      = $row_select_t_transfer['ACTUAL_QTY'];
                        
                        /**
                        * UPDATE PROGRAM FOR PARTIAL QTY ISSUE
                        * UPDATED BY   : BYAN PURBAPRANIDHANA
                        * UPDATE DATE  : OCT 10, 2015. 14.40
                        **/
                        $query_select_t_po_detail = "select
                                                        sum(QTY) as ACTUAL_QTY
                                                    from         
                                                        EPS_T_PO_DETAIL
                                                    left join
                                                        EPS_T_PO_HEADER
                                                    on 
                                                        EPS_T_PO_HEADER.PO_NO = EPS_T_PO_DETAIL.PO_NO
                                                    where     
                                                        EPS_T_PO_DETAIL.REF_TRANSFER_ID = '$transferIdVal'
                                                        and EPS_T_PO_DETAIL.PO_NO != '$poNo'
														and EPS_T_PO_HEADER.PO_STATUS in ('1210','1220','1230','1250','1280','1330','1370')";
                        $sql_select_t_po_detail = $conn->query($query_select_t_po_detail);
                        $row_select_t_po_detail = $sql_select_t_po_detail->fetch(PDO::FETCH_ASSOC);
                        $actualPoQty = $row_select_t_po_detail['ACTUAL_QTY'];
                        $newActualQty = $actualPoQty;

                        if($actualPoQty == $qtyActual)
                        {
                            $itemStatus     = '1130';
                        }
                        else
                        {
                            if($newActualQty == 0)
                            {
                                $itemStatus     = '1130';
                                $newActualQty   = $qtyTransfer;
                            }
                            else
                            {
                                $itemStatus     = '1170';
                            }
                        }
                       
                       /**
                        * UPDATE EPS_T_TRANSFER
                        */
                        $query_update_t_transfer = "update
                                                        EPS_T_TRANSFER
                                                    set
                                                        ITEM_STATUS = '$itemStatus'
                                                        ,ACTUAL_QTY = '$newActualQty'
                                                        ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                        ,UPDATE_BY = '$sUserId'
                                                    where
                                                        TRANSFER_ID = '$transferIdVal'";
                        $conn->query($query_update_t_transfer);

                    }
					
				   /**********************************************************************
                    * SEND MAIL
                    **********************************************************************/
                    $mailFrom       = $sInet;
                    $mailFromName   = $sNotes;  
                    $poStatus       = "PO Canceled After Sent";

                   /**
                    *  TO APPROVER 1 & 2
                    **/ 
                    /**
                    * SELECT EPS_T_PR_HEADER
                    **/
                    $query_select_po_approver = "select
                                                    NPK
                                                from
                                                    EPS_T_PO_APPROVER
                                                where
                                                    PO_NO = '$poNo'
                                                    and APPROVER_NO <= 2";
                    $sql_select_po_approver = $conn->query($query_select_po_approver);
                    while($row_select_po_approver = $sql_select_po_approver->fetch(PDO::FETCH_ASSOC))
                    {
						$poApprover = $row_select_po_approver['NPK'];
						$query_select_dscid = "select 
												EPS_M_DSCID.INETML
												,EPS_M_USER.PASSWORD 
											from 
												EPS_M_DSCID 
											inner join 
												EPS_M_USER 
											on 
												ltrim(EPS_M_DSCID.INOPOK) = ltrim(EPS_M_USER.USERID) 
											where  
												ltrim(EPS_M_DSCID.INOPOK) = ltrim('".$poApprover."')";
						$sql_select_dscid = $conn->query($query_select_dscid);
						$row_select_dscid = $sql_select_dscid->fetch(PDO::FETCH_ASSOC);
						if($row_select_dscid){
							$mailTo     = $row_select_dscid['INETML'];
							//$mailTo       = 'BYAN_PURBA@denso.co.id';
							$passwordApprover = $row_select_dscid['PASSWORD'];
							$getParamLink       = paramEncrypt("action=open&poNo=$poNo&userId=$poApprover&password=$passwordApprover");
							$mailSubject = "[EPS] CANCELED AFTER SENT. PO No: ".$poNo;
							$mailMessage = "<table style='font-family: Arial; font-size: 12px;'>";
							$mailMessage .= "<tr><td>PO No</td><td>: </td><td>".$poNo."</td></tr>";
							$mailMessage .= "<tr><td>PO Status</td><td>:</td><td>".$poStatus."</td></tr>";
							$mailMessage .= "<tr><td>Comment</td><td>:</td><td>".$remarkCancelPo."</td></tr>";
							$mailMessage .= "</table>";
							//$mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage
							//poSendMail($poNo, $mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage);
                                                        $query_send_mail = "insert into 
                                            EPS_T_PR_MAIL 
                                            (
                                            PR_NO
                                            ,COMPANY 
                                            ,PR_ISSUER
                                            ,PR_CHARGED
                                            ,REQUESTER_MAIL
                                            ,REQUESTER_MAIL_NAME
                                            ,NEW_APPROVER
                                            ,GET_PARAM_APPROVER
                                            ,SENT
                                            ,SUBJECT_MAIL
                                            ,PR_STATUS
                                            ,APPROVAL_STATUS
                                            ) 
                                    VALUES
                                            (
                                            '$poNo'
                                            ,'$company'
                                            ,'$prIssuer'
                                            ,'$prCharged'
                                            ,'$mailFrom'
                                            ,'$mailFromName'
                                            ,'$mailTo'
                                            ,'$getParamLink'
                                            ,'2'
                                            ,'$mailSubject'
                                            ,'$prStatus'
                                            ,'$approvalStatus'
)";
		$sql= $conn->query($query_send_mail);
		$row = $sql->fetch(PDO::FETCH_ASSOC);
						}
					}
                    
					
				   /**
                    *  TO SUPPLIER
                    **/ 
                    $query_select_m_supplier = "select
                                                    SUPPLIER_NAME
                                                    ,EMAIL
                                                    ,CURRENCY_CD
                                                from
                                                    EPS_M_SUPPLIER
                                                where
                                                    SUPPLIER_CD = '$supplierCd'";
                    $sql_select_m_supplier = $conn ->query($query_select_m_supplier);
                    $row_select_m_supplier = $sql_select_m_supplier->fetch(PDO::FETCH_ASSOC);
                    $supplierName       = $row_select_m_supplier['SUPPLIER_NAME'];
                    $supplierMail 		= $row_select_m_supplier['EMAIL'];
                    $supplierCurrency   = $row_select_m_supplier['CURRENCY_CD'];
					
					if(trim($supplierMail) == '')
                    {
                        $supplierMail = "wiharyo@taci.toyota-industries.com";
                    }
					$mailTo         = $supplierMail;
                    //$mailTo         = "byan_purba@denso.co.id";
                    $mailCc         = "wiharyo@taci.toyota-industries.com,i.softwan@taci.toyota-industries.com,wiharyo@taci.toyota-industries.com".",".$issuedMail;
                    //$mailCc         = "byan_purba@denso.co.id";
                    
					$sNmPlant       = trim($sNmPlant);
                    if($sKdPlant == '0')
                    {
                        $phoneNumber = "(+62)21-651 2279 Ext. 213 / 214";
                        $sNmPlant = $sNmPlant." Plant";
                    }
                    if($sKdPlant == '1')
                    {
                        $phoneNumber = "(+62)21-898 0303 Ext. 201 / 202";
                        $sNmPlant = $sNmPlant." Plant";
                    }
                    if($sKdPlant == '5')
                    {
                        $phoneNumber = "(+62)21-2957 7000 Ext. 405 / 406";
                        $sNmPlant = $sNmPlant;
                    }
					
                    $mailSubject    = "** [EPS] CANCELED Purchase Order No. $poNo ";
                    $mailMessage    = "<font face='Trebuchet MS' size='-1'>";
                    
                    if($supplierCurrency == 'IDR')
                    {
                        $mailMessage    .= "Yth. Bapak/Ibu Supplier TACI";
                        $mailMessage    .= "<br><br>Dengan ini kami informasikan bahwa PO $poNo kami batalkan.";
                        $mailMessage    .= "<br>Berikut adalah informasi lengkapnya :";
                        $mailMessage    .= "<br><br>Supplier : $supplierName";
                        $mailMessage    .= "<br>PIC pembuat PO : $issuedName";
                        $mailMessage    .= "<br>Email : $issuedMail";
                        $mailMessage    .= "<br>Lokasi : $sNmPlant";
                        $mailMessage    .= "<br>Telepon : $phoneNumber";
                        $mailMessage    .= "<br>Keterangan Pembatalan : $remarkCancelPo";
                        $mailMessage    .= "<br><br>Silahkan menghubungi PIC Pembuat PO jika ada hal yang kurang jelas.";
                        $mailMessage    .= "<br><br>Terima kasih atas perhatian dan kerjasamanya.";
                        $mailMessage    .= "<br><br>Hormat kami,";
                    }
                    else
                    {
                        $mailMessage    .= "Dear Sir or Madam,";
                        $mailMessage    .= "<br><br>Herewith we would like to inform you that PO $poNo is canceled.";
                        $mailMessage    .= "<br>Kindly see the complete information below:";
                        $mailMessage    .= "<br><br>Supplier : $supplierName";
                        $mailMessage    .= "<br>PO maker : $issuedName";
                        $mailMessage    .= "<br>Email : $issuedMail";
                        $mailMessage    .= "<br>Location : $sNmPlant Plant";
                        $mailMessage    .= "<br>Phone : $phoneNumber";
                        $mailMessage    .= "<br>Remark of cancelation : $remarkCancelPo";
                        $mailMessage    .= "<br><br>Please contact the PO maker if you need more information.";
                        $mailMessage    .= "<br><br>Thank you for your attention and cooperation.";
                        $mailMessage    .= "<br><br>Best regards,";
                    }
                    
                    $mailMessage    .= "<br>Procurement Dept. | General Supplies";
                    $mailMessage    .= "<br>PT. TD AUTOMOTIVE COMPRESSOR INDONESIA";
                    $mailMessage    .= "<br><br>";
                    $mailMessage    .= "</font>";
					poSendMailToSupplier($mailTo, $mailFrom, $mailFromName, $mailSubject, $mailMessage, $mailCc, $poNo);
					
                    $msg = 'Success';
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
        }
		
		if($action == 'UpdatePoAfterSent')
        {
            $poNo               = trim($_GET['poNoPrm']);
            $remarkCancelPo     = strtoupper(trim($_GET['remarkCancelPoPrm']));
            $remarkCancelPo     = str_replace("'", "''", $remarkCancelPo);
            $newDeliveryDate    = encodeDate(trim($_GET['newDeliveryDatePrm']));
            $updateDate         = strtoupper(trim($_GET['updateDatePrm']));
            
            /**
             * SELECT EPS_T_PO_HEADER
             */
            $query_eps_t_po_header = "select
                                        EPS_T_PO_HEADER.SUPPLIER_CD
                                        ,EPS_T_PO_HEADER.ISSUED_BY
                                        ,EPS_T_PO_HEADER.DELIVERY_DATE
                                        ,EPS_T_PO_HEADER.ORIGINAL_DELIVERY_DATE
                                        ,EPS_M_EMPLOYEE.NAMA1 as ISSUED_NAME
                                        ,EPS_T_PO_HEADER.UPDATE_DATE
                                        ,EPS_M_DSCID.INETML
                                      from 
                                        EPS_T_PO_HEADER
                                      left join
                                        EPS_M_EMPLOYEE
                                      on
                                        EPS_T_PO_HEADER.ISSUED_BY = EPS_M_EMPLOYEE.NPK
                                      left join 
                                        EPS_M_DSCID
                                      on
                                        EPS_T_PO_HEADER.ISSUED_BY = EPS_M_DSCID.INOPOK
                                      where
                                        PO_NO = '$poNo'";
            $sql_eps_t_po_header = $conn->query($query_eps_t_po_header);
            $row_eps_t_po_header=$sql_eps_t_po_header->fetch(PDO::FETCH_ASSOC);
            $newUpdateDate 			= strtoupper($row_eps_t_po_header['UPDATE_DATE']);
            $delieryDate   			= $row_eps_t_po_header['DELIVERY_DATE'];
            $originalDeliveryDate   = $row_eps_t_po_header['ORIGINAL_DELIVERY_DATE'];
            $supplierCd    			= $row_eps_t_po_header['SUPPLIER_CD'];
            $issuedName    			= $row_eps_t_po_header['ISSUED_NAME'];
            $issuedMail    			= $row_eps_t_po_header['INETML'];
            
            if($updateDate == $newUpdateDate)
            {
                if($newDeliveryDate != "" && $remarkCancelPo != "")
                {
                    if($originalDeliveryDate == "")
                    {
                       /**
                        * UPDATE EPS_T_PO_HEADER
                        */
                        $query_update_po = "update
                                                EPS_T_PO_HEADER
                                            set
                                                DELIVERY_DATE = '$newDeliveryDate'
                                                ,ORIGINAL_DELIVERY_DATE = '$delieryDate'
                                                ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                ,UPDATE_BY = '$sUserId'
                                            where
                                                PO_NO = '$poNo'";
                        $conn->query($query_update_po);
                    }
                    else
                    {
                       /**
                        * UPDATE EPS_T_PO_HEADER
                        */
                        $query_update_po = "update
                                                EPS_T_PO_HEADER
                                            set
                                                DELIVERY_DATE = '$newDeliveryDate'
                                                ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                ,UPDATE_BY = '$sUserId'
                                            where
                                                PO_NO = '$poNo'";
                        $conn->query($query_update_po);
                    }
                    
                    /**********************************************************************
                    * SEND MAIL
                    **********************************************************************/
                    $mailFrom       = $sInet;
                    $mailFromName   = $sNotes;  
                    $delieryDateYear = substr($delieryDate,0,4);
                    $delieryDateMonth= substr($delieryDate,4,2);
                    $delieryDateDate = substr($delieryDate,6,2);
                    
                    $newDeliveryDateYear = substr($newDeliveryDate,0,4);
                    $newDeliveryDateMonth= substr($newDeliveryDate,4,2);
                    $newDeliveryDateDate = substr($newDeliveryDate,6,2);
                   /**
                    *  TO APPROVER 1 & 2
                    **/ 
                    /**
                    * SELECT EPS_T_PR_HEADER
                    **/
                    $query_select_po_approver = "select
                                                    NPK
                                                from
                                                    EPS_T_PO_APPROVER
                                                where
                                                    PO_NO = '$poNo'
                                                    and APPROVER_NO <= 2";
                    $sql_select_po_approver = $conn->query($query_select_po_approver);
                    while($row_select_po_approver = $sql_select_po_approver->fetch(PDO::FETCH_ASSOC))
                    {
                        $poApprover = $row_select_po_approver['NPK'];
                        $query_select_dscid = "select 
                                                EPS_M_DSCID.INETML
                                                ,EPS_M_USER.PASSWORD 
                                            from 
                                                EPS_M_DSCID 
                                            inner join 
                                                EPS_M_USER 
                                            on 
                                                ltrim(EPS_M_DSCID.INOPOK) = ltrim(EPS_M_USER.USERID) 
                                            where  
                                                ltrim(EPS_M_DSCID.INOPOK) = ltrim('".$poApprover."')";
                        $sql_select_dscid = $conn->query($query_select_dscid);
                        $row_select_dscid = $sql_select_dscid->fetch(PDO::FETCH_ASSOC);
                        if($row_select_dscid){
                            $mailTo     = $row_select_dscid['INETML'];
                            //$mailTo       = 'SWAVID@denso.co.id';
                            $passwordApprover = $row_select_dscid['PASSWORD'];
                            $getParamLink       = paramEncrypt("action=open&poNo=$poNo&userId=$poApprover&password=$passwordApprover");
                            $mailSubject = "** [EPS] CHANGE DUE DATE (AFTER SENT). PO No: ".$poNo;
                            $mailMessage = "<table style='font-family: Arial; font-size: 12px;'>";
                            $mailMessage .= "<tr><td>PO No</td><td>: </td><td>".$poNo."</td></tr>";
                            $mailMessage .= "<tr><td>Due Date</td><td>:</td><td>".$delieryDateDate."/".$delieryDateMonth."/".$delieryDateYear."</td></tr>";
                            $mailMessage .= "<tr><td><b>NEW Due Date</b></td><td>:</td><td><b>".$newDeliveryDateDate."/".$newDeliveryDateMonth."/".$newDeliveryDateYear."</b></td></tr>";
                            $mailMessage .= "<tr><td>Comment</td><td>:</td><td>".$remarkCancelPo."</td></tr>";
                            $mailMessage .= "</table>";
                            //$mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage
                            //poSendMail($poNo, $mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage);
                        }
                    }
                    
				   /**
                    *  SELECT EPS_T_PO_DETAIL
                    **/ 
                    $query_select_t_po_detail = "select
                                                    EPS_T_PO_DETAIL.ITEM_NAME
                                                    ,EPS_T_PO_DETAIL.QTY
                                                    ,EPS_T_PO_DETAIL.UNIT_CD
                                                    ,EPS_T_PO_DETAIL.ITEM_PRICE
                                                    ,EPS_M_EMPLOYEE.NAMA1 as REQUESTER_NAME
                                                    ,EPS_M_BUNIT.BU_NAME as CHARGED_BU_NAME
                                                from
                                                    EPS_T_PO_DETAIL
                                                left join
                                                    EPS_T_TRANSFER
                                                on 
                                                    EPS_T_PO_DETAIL.REF_TRANSFER_ID = EPS_T_TRANSFER.TRANSFER_ID
                                                left join
                                                    EPS_M_EMPLOYEE
                                                on 
                                                    EPS_M_EMPLOYEE.NPK = EPS_T_TRANSFER.REQUESTER
                                                left join
                                                    EPS_M_BUNIT
                                                on 
                                                    EPS_M_BUNIT.BU_CD = EPS_T_TRANSFER.NEW_CHARGED_BU
                                                where
                                                    PO_NO = '$poNo'";
                    $sql_select_t_po_detail= $conn ->query($query_select_t_po_detail);
					
                   /**
                    *  TO SUPPLIER
                    **/ 
                    $query_select_m_supplier = "select
                                                    SUPPLIER_NAME
                                                    ,EMAIL
                                                    ,CURRENCY_CD
                                                from
                                                    EPS_M_SUPPLIER
                                                where
                                                    SUPPLIER_CD = '$supplierCd'";
                    $sql_select_m_supplier = $conn ->query($query_select_m_supplier);
                    $row_select_m_supplier = $sql_select_m_supplier->fetch(PDO::FETCH_ASSOC);
                    $supplierName       = $row_select_m_supplier['SUPPLIER_NAME'];
                    $supplierMail       = $row_select_m_supplier['EMAIL'];
                    $supplierCurrency   = $row_select_m_supplier['CURRENCY_CD'];
                    
                    if(trim($supplierMail) == '')
                    {
                        $supplierMail = "ahmadjafar@taci.toyota-industries.com";
                    }
                    $mailTo         = $supplierMail;
                    $mailCc         = $issuedMail.","."ahmadjafar@taci.toyota-industries.com,wiharyo@taci.toyota-industries.com,i.softwan@taci.toyota-industries.com,wiharyo@taci.toyota-industries.com";
                    
                    $sNmPlant       = trim($sNmPlant);
                    if($sKdPlant == '0')
                    {
                        $phoneNumber = "(+62)21-651 2279 Ext. 213 / 214";
                        $sNmPlant = $sNmPlant." Plant";
                    }
                    if($sKdPlant == '1')
                    {
                        $phoneNumber = "(+62)21-898 0303 Ext. 201 / 202";
                        $sNmPlant = $sNmPlant." Plant";
                    }
                    if($sKdPlant == '5')
                    {
                        $phoneNumber = "(+62)21-2957 7000 Ext. 405 / 406";
                        $sNmPlant = $sNmPlant;
                    }
                    
                    $mailSubject    = "** [EPS] CHANGE DUE DATE Purchase Order No. $poNo ";
                    $mailMessage    = "<font face='Trebuchet MS' size='-1'>";
                    
                    if($supplierCurrency == 'IDR')
                    {
                        $mailMessage    .= "Yth. Bapak/Ibu Supplier TACI";
                        $mailMessage    .= "<br><br>Dengan ini kami informasikan bahwa PO $poNo kami ubah due date - nya.";
                        $mailMessage    .= "<br>Berikut adalah informasi lengkapnya :";
                        $mailMessage    .= "<br><br>Supplier : $supplierName";
                        $mailMessage    .= "<br>PIC pembuat PO : $issuedName";
                        $mailMessage    .= "<br>Due Date : $delieryDateDate/$delieryDateMonth/$delieryDateYear";
                        $mailMessage    .= "<br>NEW Due Date : $newDeliveryDateDate/$newDeliveryDateMonth/$newDeliveryDateYear";
                        $mailMessage    .= "<br>Keterangan : $remarkCancelPo";
						$mailMessage    .= "<br><br><table style='font-family: Arial; font-size: 12px; border-bottom: 1px solid #000000; border-top: 1px solid #000000;'>";
                        $mailMessage    .= "<tr style='font-weight: bold; text-align: center;'>
                                            <td width= 550px>Item Name</td>
                                            <td width= 45px>Qty</td>
                                            <td width= 70px>U M</td>
                                            <td width= 200px>Requester</td>
                                            <td width= 180px>Charged BU Name</td>
                                        </tr>";
                        while($row_select_t_po_detail = $sql_select_t_po_detail->fetch(PDO::FETCH_ASSOC))
                        {
                            $itemNameVal        = $row_select_t_po_detail['ITEM_NAME'];
                            $qtyVal             = $row_select_t_po_detail['QTY'];
                            $unitCdVal          = $row_select_t_po_detail['UNIT_CD'];
                            $requesterNameVal   = $row_select_t_po_detail['REQUESTER_NAME'];
                            $chargedBuNameVal   = $row_select_t_po_detail['CHARGED_BU_NAME'];
                            $split = explode('.', $qtyVal);
                            if($split[1] == 0)
                            {
                                $qtyVal = number_format($qtyVal);
                            }
                            $mailMessage .= "<tr>
                                                <td>".$itemNameVal."</td>
                                                <td>".$qtyVal."</td>
                                                <td>".$unitCdVal."</td>
                                                <td>".$requesterNameVal."</td>
                                                <td>".$chargedBuNameVal."</td>
                                            </tr>";
                        }
                        $mailMessage .= "</table>";
                        $mailMessage    .= "<br><br>Silahkan menghubungi PIC Pembuat PO jika ada hal yang kurang jelas.";
                        $mailMessage    .= "<br><br>Terima kasih atas perhatian dan kerjasamanya.";
                        $mailMessage    .= "<br><br>Hormat kami,";
                    }
                    else
                    {
                        $mailMessage    .= "Dear Sir or Madam,";
                        $mailMessage    .= "<br><br>Herewith we would like to inform you that PO $poNo is changing due date.";
                        $mailMessage    .= "<br>Kindly see the complete information below:";
                        $mailMessage    .= "<br><br>Supplier : $supplierName";
                        $mailMessage    .= "<br>PO maker : $issuedName";
                        $mailMessage    .= "<br>Due Date : $delieryDateDate/$delieryDateMonth/$delieryDateYear";
                        $mailMessage    .= "<br><b>NEW Due Date : $newDeliveryDateDate/$newDeliveryDateMonth/$newDeliveryDateYear</b>";
                        $mailMessage    .= "<br>Remark : $remarkCancelPo";
						$mailMessage    .= "<br><br><table style='font-family: Arial; font-size: 12px; border-bottom: 1px solid #000000; border-top: 1px solid #000000;'>";
                        $mailMessage    .= "<tr style='font-weight: bold; text-align: center;'>
                                            <td width= 550px>Item Name</td>
                                            <td width= 45px>Qty</td>
                                            <td width= 70px>U M</td>
                                            <td width= 200px>Requester</td>
                                            <td width= 180px>Charged BU Name</td>
                                        </tr>";
                        while($row_select_t_po_detail = $sql_select_t_po_detail->fetch(PDO::FETCH_ASSOC))
                        {
                            $itemNameVal        = $row_select_t_po_detail['ITEM_NAME'];
                            $qtyVal             = $row_select_t_po_detail['QTY'];
                            $unitCdVal          = $row_select_t_po_detail['UNIT_CD'];
                            $requesterNameVal   = $row_select_t_po_detail['REQUESTER_NAME'];
                            $chargedBuNameVal   = $row_select_t_po_detail['CHARGED_BU_NAME'];
                            $split = explode('.', $qtyVal);
                            if($split[1] == 0)
                            {
                                $qtyVal = number_format($qtyVal);
                            }
                            $mailMessage .= "<tr>
                                                <td>".$itemNameVal."</td>
                                                <td>".$qtyVal."</td>
                                                <td>".$unitCdVal."</td>
                                                <td>".$requesterNameVal."</td>
                                                <td>".$chargedBuNameVal."</td>
                                            </tr>";
                        }
                        $mailMessage .= "</table>";
                        $mailMessage    .= "<br><br>Please contact the PO maker if you need more information.";
                        $mailMessage    .= "<br><br>Thank you for your attention and cooperation.";
                        $mailMessage    .= "<br><br>Best regards,";
                    }
                    
                    $mailMessage    .= "<br>Procurement Dept. | General Supplies";
                    $mailMessage    .= "<br>PT. TD AUTOMOTIVE COMPRESSOR INDONESIA";
                    $mailMessage    .= "<br><br>";
                    $mailMessage    .= "</font>";
                    poSendMailToSupplier($mailTo, $mailFrom, $mailFromName, $mailSubject, $mailMessage, $mailCc, $poNo);
                    $msg = 'Success';
                }
                else if($newDeliveryDate == "")
                {
                    $msg = 'Mandatory_3';
                }
                else if($remarkCancelPo == "")
                {
                    $msg = 'Mandatory_2';
                }
                else
                {
                    $msg = '';
                }
            }
            else
            {
                $msg = 'Mandatory_1';
            }
        }
		
		if($action == 'UpdatePoDetail')
        {
            $poNo               = trim($_GET['poNoPrm']);
            $updateDate         = strtoupper(trim($_GET['updateDatePrm']));
            $remarkCancelPo     = strtoupper(trim($_GET['remarkCancelPoPrm']));
            $poItemData         = ($_SESSION['poDetail']);
            
            $query_select_t_po_detail = "select 
                                            EPS_T_PO_HEADER.ISSUED_BY
                                            ,EPS_T_PO_DETAIL.UPDATE_DATE
                                         from
                                            EPS_T_PO_DETAIL
                                         left join
                                            EPS_T_PO_HEADER
                                         on
                                            EPS_T_PO_DETAIL.PO_NO =  EPS_T_PO_HEADER.PO_NO
                                         where
                                            EPS_T_PO_DETAIL.PO_NO = '$poNo'
                                         order by
                                            EPS_T_PO_DETAIL.UPDATE_DATE desc";
            $sql_select_t_po_detail = $conn->query($query_select_t_po_detail);
            $row_select_t_po_detail = $sql_select_t_po_detail->fetch(PDO::FETCH_ASSOC);
            $issuedBy = $row_select_t_po_detail['ISSUED_BY'];
            $newUpdateDate = strtoupper($row_select_t_po_detail['UPDATE_DATE']);
            
            if($updateDate == $newUpdateDate && count($poItemData) > 0 && $remarkCancelPo != "")
            {
                // Delete
                $query_delete_t_detail = "delete
                                          from
                                            EPS_T_PO_DETAIL
                                          where
                                            PO_NO = '$poNo'";
                $conn->query($query_delete_t_detail);
                
                /**
                * UPDATE EPS_T_PO_DETAIL
                */
                for($j = 0; $j < count($poItemData); $j++){
                    $poNoVal            = $poItemData[$j]['poNo'];
                    $refTransferIdVal   = $poItemData[$j]['refTransferId'];
                    $itemCdVal          = $poItemData[$j]['itemCd'];
                    $itemNameVal        = $poItemData[$j]['itemName'];
                    $itemNameVal        = str_replace("'", "''", $itemNameVal);
                    $itemNameVal        = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $itemNameVal);
                    $itemNameVal        = preg_replace('/\s+/', ' ',$itemNameVal);
                    $qtyVal             = $poItemData[$j]['qty'];
                    $itemPriceVal       = $poItemData[$j]['itemPrice'];
                    $amountVal          = $poItemData[$j]['amount'];
                    $unitCdVal          = $poItemData[$j]['unitCd'];
                    $itemStatusVal      = '1310';
                    $itemTypeCdVal      = $poItemData[$j]['itemTypeCd'];
                    $accountNoVal       = $poItemData[$j]['accountNo'];
                    $rfiNoVal           = $poItemData[$j]['rfiNo'];
                    
                   /**
                    * INSERT EPS_T_PO_DETAIL 
                    **/
                    $query_insert_po_detail = "insert into
                                                EPS_T_PO_DETAIL
                                                (
                                                    PO_NO
                                                    ,REF_TRANSFER_ID
                                                    ,ITEM_CD
                                                    ,ITEM_NAME
                                                    ,QTY
                                                    ,ITEM_PRICE
                                                    ,AMOUNT
                                                    ,UNIT_CD
                                                    ,ITEM_TYPE_CD
                                                    ,ACCOUNT_NO
                                                    ,RFI_NO
                                                    ,RO_STATUS
                                                    ,CREATE_DATE
                                                    ,CREATE_BY
                                                    ,UPDATE_DATE
                                                    ,UPDATE_BY
                                                )
                                            values
                                                (
                                                    '$poNoVal'
                                                    ,'$refTransferIdVal'
                                                    ,'$itemCdVal'
                                                    ,'$itemNameVal'
                                                    ,'$qtyVal'
                                                    ,'$itemPriceVal'
                                                    ,'$amountVal'
                                                    ,'$unitCdVal'
                                                    ,'$itemTypeCdVal'
                                                    ,'$accountNoVal'
                                                    ,'$rfiNoVal'
                                                    ,'$itemStatusVal'
                                                    ,convert(VARCHAR(24), GETDATE(), 120)
                                                    ,'$sUserId'
                                                    ,convert(VARCHAR(24), GETDATE(), 120)
                                                    ,'$sUserId'
                                                )";
                    $conn->query($query_insert_po_detail);
                    
                    // Check if partial.
                    
                   /**
                    * UPDATE EPS_T_TRANSFER 
                    **/
                    $query_update_t_transfer = "update
                                                    EPS_T_TRANSFER
                                                set
                                                    NEW_ITEM_CD = '$itemCdVal'
                                                    ,NEW_ITEM_NAME = '$itemNameVal'
                                                    ,NEW_QTY = '$qtyVal'
                                                    ,ACTUAL_QTY = '$qtyVal'
                                                    ,NEW_ITEM_PRICE = '$itemPriceVal'
                                                    ,NEW_AMOUNT = '$amountVal'
                                                    ,NEW_UNIT_CD = '$unitCdVal'
                                                    ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                    ,UPDATE_BY = '$sUserId'
                                                where
                                                    TRANSFER_ID = '$refTransferIdVal'";
                    $conn->query($query_update_t_transfer);
                    
                   /**********************************************************************
                    * SEND MAIL
                    **********************************************************************/
                    $mailFrom       = $sInet;
                    $mailFromName   = $sNotes;  
                
                    $query_select_dscid = "select 
                                            EPS_M_DSCID.INETML
                                            ,EPS_M_USER.PASSWORD 
                                        from 
                                            EPS_M_DSCID 
                                        inner join 
                                            EPS_M_USER 
                                        on 
                                            ltrim(EPS_M_DSCID.INOPOK) = ltrim(EPS_M_USER.NPK) 
                                        where  
                                            rtrim(ltrim(EPS_M_DSCID.INOPOK)) = ltrim('".$issuedBy."')";
                    $sql_select_dscid = $conn->query($query_select_dscid);
                    $row_select_dscid = $sql_select_dscid->fetch(PDO::FETCH_ASSOC);
                    if($row_select_dscid){
                        $mailTo           = $row_select_dscid['INETML'];
                        //$mailTo             = 'BYAN_PURBA@denso.co.id';
                        $passwordIssuedBy  = $row_select_dscid['PASSWORD'];
                        $getParamLink  = paramEncrypt("action=open&poNo=$poNo&userId=$issuedBy&password=$passwordIssuedBy");
                        $mailSubject = "[EPS] REVISE PO. PO No: ".$poNo;
                        $mailMessage = "<table style='font-family: Arial; font-size: 12px;'>";
                        $mailMessage .= "<tr><td>PO No</td><td>: </td><td>".$poNo."</td></tr>";
                        $mailMessage .= "</table>";
                        //$mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage
                        poSendMail($poNo, $mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage);

                    }

                    $msg = 'Success';
                }
            }
            else if($remarkCancelPo == "")
            {
                $msg = 'Mandatory_2';
            }
            else if(count($poItemData) == 0)
            {
                $msg = 'SessionExpired';
            }
            else
            {
                $msg = 'Mandatory_1';
            }
		}
		
		if($action == 'UpdatePoAfterClosed')
        {
            $poNo               = trim($_GET['poNoPrm']);
            $remarkCancelPo     = strtoupper(trim($_GET['remarkCancelPoPrm']));
            $remarkCancelPo     = str_replace("'", "''", $remarkCancelPo);
            $newClosingMonth    = trim($_GET['newClosingMonthPrm']);
            $newPoStatus        = trim($_GET['newPoStatusPrm']);
            $updateDate         = strtoupper(trim($_GET['updateDatePrm']));
            $action             = trim($_GET['actionPrm']);
            
            /**
             * SELECT EPS_T_PO_HEADER
             */
            $query_eps_t_po_header = "select
                                        EPS_T_PO_HEADER.SUPPLIER_CD
                                        ,EPS_T_PO_HEADER.ISSUED_BY
                                        ,EPS_T_PO_HEADER.DELIVERY_DATE
                                        ,EPS_T_PO_HEADER.ORIGINAL_DELIVERY_DATE
                                        ,EPS_M_EMPLOYEE.NAMA1 as ISSUED_NAME
                                        ,EPS_T_PO_HEADER.CLOSED_PO_MONTH
                                        ,EPS_M_APP_STATUS.APP_STATUS_NAME as PO_STATUS_NAME
                                        ,EPS_T_PO_HEADER.UPDATE_DATE
                                        ,EPS_M_DSCID.INETML
                                      from 
                                        EPS_T_PO_HEADER
                                      left join
                                        EPS_M_EMPLOYEE
                                      on
                                        EPS_T_PO_HEADER.ISSUED_BY = EPS_M_EMPLOYEE.NPK
                                      left join 
                                        EPS_M_DSCID
                                      on
                                        EPS_T_PO_HEADER.ISSUED_BY = EPS_M_DSCID.INOPOK
                                      left join 
                                        EPS_M_APP_STATUS 
                                      on 
                                        EPS_T_PO_HEADER.PO_STATUS = EPS_M_APP_STATUS.APP_STATUS_CD
                                      where
                                        PO_NO = '$poNo'";
            $sql_eps_t_po_header = $conn->query($query_eps_t_po_header);
            $row_eps_t_po_header=$sql_eps_t_po_header->fetch(PDO::FETCH_ASSOC);
            $newUpdateDate          = strtoupper($row_eps_t_po_header['UPDATE_DATE']);
            $delieryDate            = $row_eps_t_po_header['DELIVERY_DATE'];
            $originalDeliveryDate   = $row_eps_t_po_header['ORIGINAL_DELIVERY_DATE'];
            $supplierCd             = $row_eps_t_po_header['SUPPLIER_CD'];
            $issuedName             = $row_eps_t_po_header['ISSUED_NAME'];
            $issuedMail             = $row_eps_t_po_header['INETML'];
            $poStatusName           = $row_eps_t_po_header['PO_STATUS_NAME'];
            
            if($updateDate == $newUpdateDate)
            {
                if($action == "updateClosedMonth" && $newClosingMonth == "")
                {
                    $msg = 'Mandatory_3';
                }
                else if($action == "updatePoStatus" && $newPoStatus == "")
                {
                    $msg = 'Mandatory_4';
                }
                else if($action == "")
                {
                    $msg = 'Mandatory_5';
                }
                else if($remarkCancelPo == "")
                {
                    $msg = 'Mandatory_2';
                }
                else
                {
                    if($action == "updateClosedMonth")
                    {
                       /**
                        * UPDATE EPS_T_PO_HEADER
                        */
                        $query_update_po = "update
                                                EPS_T_PO_HEADER
                                            set
                                                CLOSED_PO_MONTH = '$newClosingMonth'
                                                ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                ,UPDATE_BY = '$sUserId'
                                            where
                                                PO_NO = '$poNo'";
                        $conn->query($query_update_po);
                        
                        $mailSubject    = "[EPS] CHANGE CLOSING MONTH. PO No. $poNo ";
                    }
                       
                    if($action == "updatePoStatus")
                    {
                       /**
                        * UPDATE EPS_T_PO_HEADER
                        */
                        $query_update_po = "update
                                                EPS_T_PO_HEADER
                                            set
                                                PO_STATUS = '$newPoStatus'
                                                ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                ,UPDATE_BY = '$sUserId'
                                            where
                                                PO_NO = '$poNo'";
                        $conn->query($query_update_po);
                        
                        $mailSubject    = "[EPS] COMPLETED (NOT CREATED CN). PO No. $poNo ";
                    }
					
                   /**
                    * SELECT EPS_M_APP_STATUS
                    */
                    $query_select_m_app_status = "select 
                                                    EPS_T_PO_HEADER.PO_STATUS
                                                    ,EPS_M_APP_STATUS.APP_STATUS_NAME
                                                  from
                                                    EPS_T_PO_HEADER
                                                  left join
                                                    EPS_M_APP_STATUS
                                                  on
                                                    EPS_T_PO_HEADER.PO_STATUS = EPS_M_APP_STATUS.APP_STATUS_CD
                                                  where
                                                    PO_NO = '$poNo'";
                    $sql_select_m_app_status = $conn->query($query_select_m_app_status);
                    $row_select_m_app_status = $sql_select_m_app_status->fetch(PDO::FETCH_ASSOC);
                    $poStatusName = $row_select_m_app_status['APP_STATUS_NAME'];
					
                   /**********************************************************************
                    * SEND MAIL
                    **********************************************************************/
                    $mailFrom       = $sInet;
                    $mailFromName   = $sNotes;  
                    $mailTo         = trim($issuedMail);
                    //$mailTo         = "byan_purba@denso.co.id";
                    $mailCc         = $mailFrom;
                    //$mailCc         = "byan_purba@denso.co.id";
                    
                    $mailMessage  = "<font face='Trebuchet MS' size='-1'>";
                    $mailMessage .= "<table style='font-family: Arial; font-size: 12px;'>";
                    $mailMessage .= "<tr><td>PO No</td><td>: </td><td>".$poNo."</td></tr>";
                    $mailMessage .= "<tr><td>PO Status</td><td>:</td><td>".$poStatusName."</td></tr>";
                    $mailMessage .= "<tr><td>Closed PO Month</td><td>:</td><td>".$newClosingMonth."</td></tr>";
                    $mailMessage .= "<tr><td>Comment</td><td>:</td><td>".$remarkCancelPo."</td></tr>";
                    $mailMessage .= "</table></font>";
                    poSendMailToSupplier($mailTo, $mailFrom, $mailFromName, $mailSubject, $mailMessage, $mailCc, $poNo);
                    $msg = "Success";
                    
                }
            }
            else
            {
                $msg = 'Mandatory_1';
            }
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
