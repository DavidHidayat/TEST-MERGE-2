<?php session_start(); 
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Constant.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Common.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Email/PR_EMAIL.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Email/PO_EMAIL.php";
//include $_SERVER['DOCUMENT_ROOT']."/EPS/db/CONTROLLER/PR_MAIL.php";
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
        
        if($action == 'ApprovePr')
        {  
            $prNo               = trim($_GET['prNoPrm']);
            $remarkApp          = strtoupper(trim($_GET['remarkAppPrm']));
            $remarkApp          = str_replace("'", "''", $remarkApp);
            $remarkApp          = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $remarkApp);
            $remarkApp          = preg_replace('/\s+/', ' ',$remarkApp);
			
            /** Check next approver in EPS_T_PR_HEADER */
            $query_select_t_pr_header = "select
                                            APPROVER
                                            ,PR_STATUS
                                            ,APPROVAL_STATUS
                                        from
                                            EPS_T_PR_HEADER
                                        left join
                                            EPS_T_PR_APPROVER
                                        on
                                            EPS_T_PR_HEADER.PR_NO = EPS_T_PR_APPROVER.PR_NO
                                            and EPS_T_PR_HEADER.APPROVER = EPS_T_PR_APPROVER.NPK
                                        where
                                            EPS_T_PR_HEADER.PR_NO = '$prNo'
                                            and APPROVAL_STATUS is not null
											and APPROVAL_STATUS ='WA'
                                        order by
                                            APPROVER_NO asc
                                            ,EPS_T_PR_APPROVER.UPDATE_DATE desc";
            $sql_select_t_pr_header = $conn->query($query_select_t_pr_header);
            while($row_select_t_pr_header = $sql_select_t_pr_header->fetch(PDO::FETCH_ASSOC))
            {
                $prHeaderApprover   = $row_select_t_pr_header['APPROVER'];
                $prStatus           = $row_select_t_pr_header['PR_STATUS'];
                $approvalStatus     = $row_select_t_pr_header['APPROVAL_STATUS'];
            }
			
			if((trim($prHeaderApprover) == trim($sUserId)) && $prStatus == "1020" && $approvalStatus == "WA")
            {
			
				/**
				 * Unset SESSION 
				 **/
				unset($_SESSION['prStatusSession']);
				
				
				$prCharged          = $_GET['prChargedValPrm'];
				if(strlen(trim($prCharged)) == 4)
				{
					$prCharged = trim($prCharged)." ";
				}
				
				/**
				 *  UPDATE EPS_T_PR_HEADER
				 */
				$query_update_t_pr_header = "update
												EPS_T_PR_HEADER
											set
												UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
												,UPDATE_BY = '$sUserId'
											where
												PR_NO = '$prNo'";
				$conn->query($query_update_t_pr_header);
					
				/**
				 *  SELECT EPS_T_PR_HEADER & EPS_T_PR_APPROVER
				 */
				$query_select_t_pr_header_approver = "select 
														EPS_T_PR_HEADER.PR_NO
														,EPS_T_PR_HEADER.APPROVER
														,EPS_T_PR_APPROVER.APPROVER_NO
														,EPS_T_PR_HEADER.USERID
														,(select count(*)
														from          
															EPS_T_PR_APPROVER
														where      
															EPS_T_PR_APPROVER.PR_NO = '$prNo') as COUNT_APPROVER
													from
														EPS_T_PR_HEADER
													left join
														EPS_T_PR_APPROVER
													on
														ltrim(EPS_T_PR_HEADER.APPROVER) = ltrim(EPS_T_PR_APPROVER.NPK)
														and EPS_T_PR_HEADER.PR_NO = EPS_T_PR_APPROVER.PR_NO
													where
														EPS_T_PR_HEADER.PR_NO = '$prNo'
														and EPS_T_PR_APPROVER.APPROVAL_STATUS = '".constant('WA')."'";
				$sql_select_t_pr_header_approver = $conn->query($query_select_t_pr_header_approver);
				$row_select_t_pr_header_approver = $sql_select_t_pr_header_approver->fetch(PDO::FETCH_ASSOC);
				$approver       = $row_select_t_pr_header_approver['APPROVER'];
				$approverNo     = $row_select_t_pr_header_approver['APPROVER_NO'];
				$prUserId       = $row_select_t_pr_header_approver['USERID'];
				$countApprover  = $row_select_t_pr_header_approver['COUNT_APPROVER'];
				
				/**
				 *  UPDATE EPS_T_PR_APPROVER TO APPROVED
				 */
				$query_update_t_po_approver_ap = "update 
													EPS_T_PR_APPROVER 
												set 
													APPROVAL_STATUS = '".constant('AP')."'
													,APPROVAL_DATE = convert(VARCHAR(24), GETDATE(), 120)
													,DEVICE_ADDRESS = '$deviceId'
                                                    ,APPROVAL_REMARK = '$remarkApp'
													,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
													,UPDATE_BY = '$sUserId'
												where 
													PR_NO = '$prNo' 
													and NPK = '$approver'
													and APPROVER_NO = '$approverNo'";
													//ECHO $query_update_t_po_approver_ap;
				$conn->query($query_update_t_po_approver_ap);
				
				// If not last approver    
				if($approverNo < $countApprover)
				{
					$newApproverNo = $approverNo+1;
					// Select in EPS_T_PR_APPROVER to get next approver
					$query = "select 
									APPROVER_NO
									,NPK
									,APPROVAL_STATUS
								from 
									EPS_T_PR_APPROVER 
								where 
									PR_NO = '$prNo' 
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
					 * UPDATE EPS_T_PR_APPROVER 
					 */
					$query_update_t_po_approver_wa = "update 
														EPS_T_PR_APPROVER 
													set 
														APPROVAL_STATUS = '".constant('WA')."'
														,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
														,UPDATE_BY = '$sUserId'
													where 
														PR_NO = '$prNo'  
														and NPK ='$newApprover'
														and APPROVER_NO = '$newApproverNo'";
					$conn ->query($query_update_t_po_approver_wa);
					
					/** 
					 * UPDATE EPS_T_PR_HEADER 
					 */
					$query_update_t_pr_header = "update 
													EPS_T_PR_HEADER 
												set 
													APPROVER = '$newApprover' 
													,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
													,UPDATE_BY = '$sUserId'
												where 
													PR_NO = '$prNo'";
					$conn->query($query_update_t_pr_header);
					
					$approver = $newApprover;
					$prStatus = constant('1020');
				}
				// If last approver
				else 
				{
					/** 
					 * SELECT EPS_T_PR_HEADER 
					 */
					$query_select_t_pr_header = "select 
													PLANT_CD
													,BU_CD
													,CHARGED_BU_CD
													,REQ_BU_CD
												from 
													EPS_T_PR_HEADER
												where
													PR_NO = '$prNo'";
					$sql_select_t_pr_header = $conn->query($query_select_t_pr_header);
					$row_select_t_pr_header = $sql_select_t_pr_header->fetch(PDO::FETCH_ASSOC);
					$plantCd    = $row_select_t_pr_header['PLANT_CD'];
					$buCd       = $row_select_t_pr_header['BU_CD'];
					$prCharged  = $row_select_t_pr_header['CHARGED_BU_CD'];
					$prIssuer   = $row_select_t_pr_header['REQ_BU_CD'];
					
					/** 
					 * SELECT EPS_M_PR_PROC_APPROVER 
					 */
					$query_select_m_pr_proc_app = "select 
														NPK 
													from 
														EPS_M_PR_PROC_APPROVER 
													where 
														ltrim(EPS_M_PR_PROC_APPROVER.PLANT_CD) = ltrim('$plantCd')
														and ltrim(EPS_M_PR_PROC_APPROVER.BU_CD) = ltrim('$prIssuer')";
					$sql_select_m_pr_proc_app = $conn->query($query_select_m_pr_proc_app);
					$row_select_m_pr_proc_app = $sql_select_m_pr_proc_app->fetch(PDO::FETCH_ASSOC);
					$procInCharge = $row_select_m_pr_proc_app['NPK'];
					$prStatus = constant('1030');

					if($procInCharge == '')
					{
						$query_select_m_pr_proc_app_2 = "select
															NPK
														from
															EPS_M_PR_PROC_APPROVER
														where
															ltrim(EPS_M_PR_PROC_APPROVER.BU_CD) = ltrim('$prIssuer')";
						$sql_select_m_pr_proc_app_2 = $conn->query($query_select_m_pr_proc_app_2);
						$row_select_m_pr_proc_app_2 = $sql_select_m_pr_proc_app_2->fetch(PDO::FETCH_ASSOC);
						$procInCharge = $row_select_m_pr_proc_app_2['NPK'];
					}
					
					/** 
					 * UPDATE EPS_T_PR_HEADER 
					 */
					$query_update_t_pr_header = "update 
													EPS_T_PR_HEADER 
												set 
													PROC_IN_CHARGE = '$procInCharge'
													,APPROVER = ''
													,PR_STATUS = '$prStatus'
													,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
													,UPDATE_BY = '$sUserId'
												where 
													PR_NO = '$prNo'";
				   $conn ->query($query_update_t_pr_header);
				   $approver = $procInCharge;

				}
				 
				/**
				 * Search EPS_M_APP_STATUS
				 **/
				$query_select_m_app_status = "select
												APP_STATUS_NAME
											  from
												EPS_M_APP_STATUS
											  where
												APP_STATUS_CD = '$prStatus'";
				$sql_select_m_app_status= $conn->query($query_select_m_app_status);
				$row_select_m_app_status = $sql_select_m_app_status->fetch(PDO::FETCH_ASSOC);
				$prStatusName = $row_select_m_app_status['APP_STATUS_NAME'];
					
				/**********************************************************************
				 * SEND MAIL
				 **********************************************************************/
				$mailFrom       = $sInet;
				$mailFromName   = $sNotes;  
					
				/**
				 * TO NEXT APPROVER
				 **/
				$query_select_m_dscid_app = "select 
												EPS_M_DSCID.INETML
												,EPS_M_USER.PASSWORD 
											from 
												EPS_M_DSCID 
											inner join 
												EPS_M_USER 
											on 
												ltrim(EPS_M_DSCID.INOPOK) = ltrim(EPS_M_USER.USERID) 
											where  
												ltrim(ltrim(EPS_M_DSCID.INOPOK)) = ltrim('".$approver."')";
				$sql_select_m_dscid_app = $conn->query($query_select_m_dscid_app);
				$row_select_m_dscid_app = $sql_select_m_dscid_app->fetch(PDO::FETCH_ASSOC);
				if($row_select_m_dscid_app)
				{
					$mailApprover     = $row_select_m_dscid_app['INETML'];
					//$mailApprover       = 'BYAN_PURBA@denso.co.id';
					$passwordApprover   = $row_select_m_dscid_app['PASSWORD'];
					$getParamApprover   = paramEncrypt("action=open&prNo=$prNo&userId=$approver&password=$passwordApprover");
					if($prStatus == "1030")
                    {
                        $mailSubject    = MailSubjectPR("WAITING ACCEPTANCE",$prNo);
                        $mailMessage    = MailMessagePR($prNo, $sNama, $getParamApprover, $remark, "WAITING ACCEPTANCE");
                    }
                    else
                    {
                        $mailSubject    = MailSubjectPR("WAITING APPROVAL",$prNo);
                        $mailMessage    = MailMessagePR($prNo, $sNama, $getParamApprover, $remarkApp, "WAITING APPROVAL");
                    }
                    SendMailPR ($mailApprover, $mailFrom, $mailFromName, $mailSubject, $mailMessage);
				}
				
				/**
                * TO REQUESTER
                **/
                $query_select_m_dscid_req = "select 
                                                EPS_M_DSCID.INETML
                                                ,EPS_M_USER.PASSWORD 
                                            from 
                                                EPS_M_DSCID 
                                            inner join 
                                                EPS_M_USER 
                                            on 
                                                ltrim(EPS_M_DSCID.INOPOK) = ltrim(EPS_M_USER.NPK) 
                                            where  
                                                ltrim(ltrim(EPS_M_USER.USERID)) = ltrim('".$prUserId."')";
                $sql_select_m_dscid_req= $conn->query($query_select_m_dscid_req);
                $row_select_m_dscid_req = $sql_select_m_dscid_req->fetch(PDO::FETCH_ASSOC);
                if($row_select_m_dscid_req)
                {
                    $mailRequester    = $row_select_m_dscid_req['INETML'];
                    //$mailRequester      = 'BYAN_PURBA@denso.co.id';
                    $passwordRequester  = $row_select_m_dscid_req['PASSWORD'];
                    $getParamRequester  = paramEncrypt("action=open&prNo=$prNo&userId=$prUserId&password=$passwordRequester");

                    $mailSubject    = MailSubjectPR("APPROVED",$prNo);
                    $mailMessage    = MailMessagePR($prNo, $sNama, $getParamRequester, $remarkApp, "APPROVED");
                    SendMailPR ($mailRequester, $mailFrom, $mailFromName, $mailSubject, $mailMessage);
                }
				
				$msg = 'Success';
			
			}
            else
            {
                $msg = "Mandatory_2";
            }
        }
        
        if($action == 'RejectPr')
        {
            $prNo       = trim($_GET['prNoPrm']);
            $remarkApp  = strtoupper(trim($_GET['remarkAppPrm']));
            $remarkApp  = str_replace("'", "''", $remarkApp);
            $remarkApp  = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $remarkApp);
            $remarkApp  = preg_replace('/\s+/', ' ',$remarkApp);
            
            if($remarkApp != '')
            {
                unset($_SESSION['prStatusSession']);
                
                /** 
                 * UPDATE EPS_T_PR_HEADER 
                 **/
                $query_update_t_pr_header = "update
                                                EPS_T_PR_HEADER
                                            set
                                                PR_STATUS = '".constant('1050')."'
                                                ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                ,UPDATE_BY = '$sUserId'
                                            where
                                                PR_NO = '$prNo'";
                $conn->query($query_update_t_pr_header);

                /** 
                 * SELECT EPS_T_PR_APPROVER 
                 **/
                $query_select_t_pr_approver = "select 
                                                    APPROVER_NO 
                                                from
                                                    EPS_T_PR_APPROVER
                                                where 
                                                    PR_NO = '$prNo'
                                                    and ltrim(NPK) = ltrim('$sUserId')
                                                    and APPROVAL_STATUS = '".constant('WA')."'";
                $sql_select_t_pr_approver = $conn->query($query_select_t_pr_approver);
                $row_select_t_pr_approver = $sql_select_t_pr_approver->fetch(PDO::FETCH_ASSOC);
                $approverNo = $row_select_t_pr_approver['APPROVER_NO'];
                
                /** 
                 * UPDATE EPS_T_PR_APPROVER 
                 **/
                $query_update_t_pr_approver = "update
                            EPS_T_PR_APPROVER
                        set
                            APPROVAL_STATUS = '".constant('RE')."'
                            ,APPROVAL_REMARK = '$remarkApp'
                            ,APPROVAL_DATE = convert(VARCHAR(24), GETDATE(), 120)
                            ,DEVICE_ADDRESS = '$deviceId'
                            ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                            ,UPDATE_BY = '$sUserId'
                        where
                            PR_NO = '$prNo'
                            and ltrim(NPK) = ltrim('$sUserId')
                            and APPROVER_NO = '$approverNo'";
                $conn->query($query_update_t_pr_approver);
            
                /**********************************************************************
                 * SEND MAIL
                 **********************************************************************/
                $mailFrom       = $sInet;
                $mailFromName   = $sNotes;  
                
                /**
                 * TO PREV APPROVER
                 **/
                $query_select_t_pr_approver = "select 
                                                    NPK
                                                from
                                                    EPS_T_PR_APPROVER
                                                where 
                                                    PR_NO = '$prNo'
                                                    and APPROVER_NO < $approverNo";
                $sql_select_t_pr_approver = $conn->query($query_select_t_pr_approver);
                while($row_select_t_pr_approver = $sql_select_t_pr_approver->fetch(PDO::FETCH_ASSOC)){
                    $npkApprover = $row_select_t_pr_approver['NPK'];
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
                            $mailApprover     = $row['INETML'];
                            //$mailApprover       = 'BYAN_PURBA@denso.co.id';
                            $passwordApprover   = $row['PASSWORD'];
                            $getParamApprover   = paramEncrypt("action=open&prNo=$prNo&userId=$npkApprover&password=$passwordApprover");
							
                            $mailSubject = MailSubjectPR("REJECTED",$prNo);
                            $mailMessage = MailMessagePR($prNo, $sNama, $getParamApprover, $remarkApp, "REJECTED");
                            SendMailPR ($mailApprover, $mailFrom, $mailFromName, $mailSubject, $mailMessage);
                        }
                }
                    
                /** 
                 * SELECT EPS_T_PR_HEADER 
                 **/
                $query_select_t_pr_header = "select 
                                                USERID
                                            from
                                                EPS_T_PR_HEADER
                                            where
                                                PR_NO = '$prNo'";
                                    $sql_select_t_pr_header = $conn->query($query_select_t_pr_header);
                $row_select_t_pr_header = $sql_select_t_pr_header->fetch(PDO::FETCH_ASSOC);
                $prUserId = $row_select_t_pr_header['USERID'];

                /**
                 * TO REQUESTER
                 **/
                $query_select_m_dscid_req = "select 
                                                EPS_M_DSCID.INETML
                                                ,EPS_M_USER.PASSWORD 
                                            from 
                                                EPS_M_DSCID 
                                            inner join 
                                                EPS_M_USER 
                                            on 
                                                ltrim(EPS_M_DSCID.INOPOK) = ltrim(EPS_M_USER.NPK) 
                                            where  
                                                ltrim(EPS_M_USER.USERID) = ltrim('".$prUserId."')";
                $sql_select_m_dscid_req = $conn->query($query_select_m_dscid_req);
                $row_select_m_dscid_req = $sql_select_m_dscid_req->fetch(PDO::FETCH_ASSOC);
                if($row_select_m_dscid_req){
                    $mailRequester    = $row_select_m_dscid_req['INETML'];
                    //$mailRequester      = 'BYAN_PURBA@denso.co.id';
                    $passwordRequester  = $row_select_m_dscid_req['PASSWORD'];
                    $getParamRequester  = paramEncrypt("action=open&prNo=$prNo&userId=$prUserId&password=$passwordRequester");
                    
                    $mailSubject    = MailSubjectPR("REJECTED",$prNo);
                    $mailMessage    = MailMessagePR($prNo, $sNama, $getParamRequester, $remarkApp, "REJECTED");
                    SendMailPR ($mailRequester, $mailFrom, $mailFromName, $mailSubject, $mailMessage);
                }
                $msg = 'Success';
            }
            else
            {
                $msg = 'Mandatory_1';
            }
        }
        
        if($action == 'TakeoverPr')
        { 
            /**
             * Unset SESSION 
             **/
            unset($_SESSION['prStatusSession']);
            
            $prNo           = trim($_GET['prNoPrm']);
            $prStatus       = "Waiting for Approval";
            
            $query_select_t_pr_approver = "select
                                                APPROVER_NO
                                            from
                                                EPS_T_PR_APPROVER
                                            where
                                                PR_NO = '$prNo'
                                                and ltrim(NPK) = ltrim('$sUserId')";
            $sql_select_t_pr_approver = $conn->query($query_select_t_pr_approver);
            $row_select_t_pr_approver = $sql_select_t_pr_approver->fetch(PDO::FETCH_ASSOC);
            $approverNo = $row_select_t_pr_approver['APPROVER_NO'];
            $approvelRemark = "Taken over approval by ".trim($sNama);
            
            /** 
             * UPDATE EPS_T_PR_APPROVER 
             * for takeover approval 
             **/
            date_default_timezone_set('Asia/Jakarta');
            $dateOfByPass = date("n/j/Y H:i:s A");
            $query_update_t_pr_approver_to = "update
                                                EPS_T_PR_APPROVER
                                            set
                                                APPROVAL_STATUS = '".constant('TO')."'
                                                ,APPROVAL_REMARK = '$approvelRemark'
                                                ,DATE_OF_BYPASS = '$dateOfByPass'
                                                ,DEVICE_ADDRESS = '$deviceId'
                                                ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                ,UPDATE_BY = '$sUserId'
                                            where
                                                PR_NO = '$prNo'
                                                and APPROVER_NO < $approverNo
                                                and APPROVAL_STATUS != '".constant('BP')."'
                                                and APPROVAL_STATUS != '".constant('AP')."'
                                                and APPROVAL_STATUS != '".constant('TO')."'";
            $conn->query($query_update_t_pr_approver_to);
            
            /** 
             * UPDATE EPS_T_PR_APPROVER 
             * for new approval 
             **/
            $query_update_t_pr_approver_wa = "update
                                                EPS_T_PR_APPROVER
                                            set
                                                APPROVAL_STATUS = '".constant('WA')."'
                                                ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                ,UPDATE_BY = '$sUserId'
                                            where
                                                PR_NO = '$prNo'
                                                and ltrim(NPK) = ltrim('$sUserId')";
            $conn->query($query_update_t_pr_approver_wa);
            
            /** 
             * UPDATE EPS_T_PR_HEADER 
             **/
            $query_update_t_pr_header = "update
                                            EPS_T_PR_HEADER
                                        set
                                            APPROVER = '$sUserId'
                                            ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                            ,UPDATE_BY = '$sUserId'
                                        where
                                            PR_NO = '$prNo'";
            $conn->query($query_update_t_pr_header);
            
            /**********************************************************************
             * SEND MAIL
             **********************************************************************/
            $mailFrom       = $sInet;
            $mailFromName   = $sNotes;  
            
            /** 
             * SELECT EPS_T_PR_APPROVER 
             **/
            $query_select_t_pr_approver_to = "select
                                                EPS_T_PR_APPROVER.NPK 
                                                ,EPS_T_PR_APPROVER.APPROVAL_REMARK
                                                ,EPS_M_APPROVAL_STATUS.APPROVAL_STATUS_NAME
                                            from
                                                EPS_T_PR_APPROVER
                                            left join
                                                EPS_M_APPROVAL_STATUS
                                            on
                                                EPS_T_PR_APPROVER.APPROVAL_STATUS = EPS_M_APPROVAL_STATUS.APPROVAL_STATUS_CD
                                            where
                                                PR_NO = '$prNo'
                                                and APPROVAL_STATUS = '".constant('TO')."'";
            $sql_select_t_pr_approver_to = $conn->query($query_select_t_pr_approver_to);
            while($row_select_t_pr_approver_to = $sql_select_t_pr_approver_to->fetch(PDO::FETCH_ASSOC)){
                $npkTakeOverApprover= $row_select_t_pr_approver_to['NPK'];
                $approvalStatus     = $row_select_t_pr_approver_to['APPROVAL_STATUS_NAME'];
                $approvalRemark     = $row_select_t_pr_approver_to['APPROVAL_REMARK'];
                /**
                 * TO TAKEOVER APPROVER
                 **/
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
                            ltrim(ltrim(EPS_M_DSCID.INOPOK)) = ltrim('".$npkTakeOverApprover."')";
                $sql= $conn->query($query);
                $row = $sql->fetch(PDO::FETCH_ASSOC);
                if($row){
                    $mailTakeOverApprover     = $row['INETML'];
                    //$mailTakeOverApprover       = 'BYAN_PURBA@denso.co.id';
                    $passwordTakeOverApprover   = $row['PASSWORD'];
                    $getParamTakeOverApprover   = paramEncrypt("action=open&prNo=$prNo&userId=$npkTakeOverApprover&password=$passwordTakeOverApprover");
                    $mailSubject        = "[EPS] TAKEN OVER (FOR APPROVER). PR No: ".$prNo;
                    $mailMessage        = "<table style='font-family: Arial; font-size: 12px;'>";
                    $mailMessage        .= "<tr><td>PR No</td><td>: </td><td>".$prNo."</td></tr>";
                    $mailMessage        .= "<tr><td>PR Status</td><td>:</td><td>".$prStatus."</td></tr>";
                    $mailMessage        .= "<tr><td>Approval Status</td><td>:</td><td>".$approvalStatus."</td></tr>";
                    $mailMessage        .= "<tr><td>Remark</td><td>:</td><td>".$approvalRemark."</td></tr>";
                    $mailMessage        .= "</table></font>";
                    prSendMail($prNo, $mailTakeOverApprover, $mailFrom, $mailFromName, $getParamTakeOverApprover, $mailSubject, $mailMessage);
                }
            }

            /** 
             * SELECT EPS_T_PR_HEADER 
             **/
            $query_select_t_pr_header = "select 
                                            USERID
                                        from
                                            EPS_T_PR_HEADER
                                        where
                                            PR_NO = '$prNo'";
            $sql_select_t_pr_header = $conn->query($query_select_t_pr_header);
            $row_select_t_pr_header = $sql_select_t_pr_header->fetch(PDO::FETCH_ASSOC);
            $prUserId = $row_select_t_pr_header['USERID'];

            /**
             * TO REQUESTER
             **/
            $query = "select 
                        EPS_M_DSCID.INETML
                        ,EPS_M_USER.PASSWORD 
                    from 
                        EPS_M_DSCID 
                    inner join 
                        EPS_M_USER 
                    on 
                        ltrim(EPS_M_DSCID.INOPOK) = ltrim(EPS_M_USER.NPK) 
                    where  
                        ltrim(ltrim(EPS_M_DSCID.INOPOK)) = ltrim('".$prUserId."')";
            $sql= $conn->query($query);
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            if($row){
                $mailRequester    = $row['INETML'];
                //$mailRequester      = 'BYAN_PURBA@denso.co.id';
                $passwordRequester  = $row['PASSWORD'];
                $getParamRequester  = paramEncrypt("action=open&prNo=$prNo&userId=$prUserId&password=$passwordRequester");
                $mailSubject        = "[EPS] TAKEN OVER (FOR REQUESTER). PR No: ".$prNo;
                $mailMessage        = "<table style='font-family: Arial; font-size: 12px;'>";
                $mailMessage        .= "<tr><td>PR No</td><td>: </td><td>".$prNo."</td></tr>";
                $mailMessage        .= "<tr><td>PR Status</td><td>:</td><td>".$prStatus."</td></tr>";
                $mailMessage        .= "</table></font>";
                prSendMail($prNo, $mailRequester, $mailFrom, $mailFromName, $getParamRequester, $mailSubject, $mailMessage);
            }

            /**
             * TO NEXT APPROVER
             **/
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
                        ltrim(ltrim(EPS_M_DSCID.INOPOK)) = ltrim('".$sUserId."')";
            $sql= $conn->query($query);
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            if($row){
                $prStatus           = "Waiting Approval";
                $approvalStatus     = "Waiting for Approval";
                $approvalRemark     = "";
                $mailApprover     = $row['INETML'];
                //$mailApprover       = 'BYAN_PURBA@denso.co.id';
                $passwordApprover   = $row['PASSWORD'];
                $getParamApprover   = paramEncrypt("action=open&prNo=$prNo&npk=$sUserId&password=$passwordApprover");
                $mailSubject        = "[EPS] WAITING APPROVAL. PR No: ".$prNo;
                $mailMessage        = "<table style='font-family: Arial; font-size: 12px;'>";
                $mailMessage        .= "<tr><td>PR No</td><td>: </td><td>".$prNo."</td></tr>";
                $mailMessage        .= "<tr><td>PR Status</td><td>:</td><td>".$prStatus."</td></tr>";
                $mailMessage        .= "<tr><td>Approval Status</td><td>:</td><td>".$approvalStatus."</td></tr>";
                $mailMessage        .= "<tr><td>Remark</td><td>:</td><td>".$approvalRemark."</td></tr>";
                $mailMessage        .= "</table></font>";
                prSendMail($prNo, $mailApprover, $mailFrom, $mailFromName, $getParamApprover, $mailSubject, $mailMessage);
            }
            $msg = 'Success';
        }
		
		if($action == 'BypassApproveItPr')
        {
            /**
             * Unset SESSION 
             **/
            unset($_SESSION['prStatusSession']);
            
            $prNo           = trim($_GET['prNoPrm']);
            
            /** 
             * SELECT EPS_T_PR_HEADER 
             **/
            $query_select_t_pr_header = "select 
                                            EPS_T_PR_HEADER.PR_NO
                                            ,EPS_T_PR_HEADER.APPROVER
                                            ,EPS_T_PR_APPROVER.APPROVER_NO
                                            ,EPS_T_PR_HEADER.USERID
                                            ,(select count(*)
                                            from          
                                                EPS_T_PR_APPROVER
                                            where      
                                                EPS_T_PR_APPROVER.PR_NO = '$prNo') as COUNT_APPROVER
                                        from
                                            EPS_T_PR_HEADER
                                        left join
                                            EPS_T_PR_APPROVER
                                        on
                                            ltrim(EPS_T_PR_HEADER.APPROVER) = ltrim(EPS_T_PR_APPROVER.NPK)
                                            and EPS_T_PR_HEADER.PR_NO = EPS_T_PR_APPROVER.PR_NO
                                        where
                                            EPS_T_PR_HEADER.PR_NO = '$prNo'
                                            and EPS_T_PR_APPROVER.APPROVAL_STATUS = 'WA'";
            $sql_select_t_pr_header = $conn->query($query_select_t_pr_header);
            $row_select_t_pr_header = $sql_select_t_pr_header->fetch(PDO::FETCH_ASSOC);
            $approver       = $row_select_t_pr_header['APPROVER'];
            $approverNo     = $row_select_t_pr_header['APPROVER_NO'];
            $prUserId       = $row_select_t_pr_header['USERID'];
            
            /** 
             * UPDATE EPS_T_PR_APPROVER 
             **/
            $query_update_t_approver = "update 
                                            EPS_T_PR_APPROVER 
                                        set 
                                            APPROVAL_STATUS = 'BP'
                                            ,APPROVAL_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                            ,DEVICE_ADDRESS = '$deviceId'
                                            ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                            ,UPDATE_BY = '$sUserId'
                                        where 
                                            PR_NO = '$prNo' 
                                            and NPK = '$approver'
                                            and APPROVER_NO = '$approverNo'";
            $conn->query($query_update_t_approver);
            
            /** 
             * SELECT EPS_T_PR_HEADER 
             */
            $query_select_t_pr_header = "select 
                                                PLANT_CD
                                                ,BU_CD
                                                ,CHARGED_BU_CD
                                                ,REQ_BU_CD
                                            from 
                                                EPS_T_PR_HEADER
                                            where
                                                PR_NO = '$prNo'";
            $sql_select_t_pr_header = $conn->query($query_select_t_pr_header);
            $row_select_t_pr_header = $sql_select_t_pr_header->fetch(PDO::FETCH_ASSOC);
            $plantCd    = $row_select_t_pr_header['PLANT_CD'];
            $buCd       = $row_select_t_pr_header['BU_CD'];
            $prCharged  = $row_select_t_pr_header['CHARGED_BU_CD'];
            $prIssuer   = $row_select_t_pr_header['REQ_BU_CD'];
                
            /** 
             * SELECT EPS_M_PR_PROC_APPROVER 
             */
            $query_select_m_pr_proc_app = "select 
                                            NPK 
                                           from 
                                            EPS_M_PR_PROC_APPROVER 
                                           where 
                                            ltrim(EPS_M_PR_PROC_APPROVER.PLANT_CD) = ltrim('$plantCd')
                                            and ltrim(EPS_M_PR_PROC_APPROVER.BU_CD) = ltrim('$prIssuer')";
            $sql_select_m_pr_proc_app = $conn->query($query_select_m_pr_proc_app);
            $row_select_m_pr_proc_app = $sql_select_m_pr_proc_app->fetch(PDO::FETCH_ASSOC);
            $procInCharge = $row_select_m_pr_proc_app['NPK'];
            $prStatus = constant('1030');

            if($procInCharge == '')
            {
                $query_select_m_pr_proc_app_2 = "select
                                                        NPK
                                                    from
                                                        EPS_M_PR_PROC_APPROVER
                                                    where
                                                        ltrim(EPS_M_PR_PROC_APPROVER.BU_CD) = ltrim('$prIssuer')";
                $sql_select_m_pr_proc_app_2 = $conn->query($query_select_m_pr_proc_app_2);
                $row_select_m_pr_proc_app_2 = $sql_select_m_pr_proc_app_2->fetch(PDO::FETCH_ASSOC);
                $procInCharge = $row_select_m_pr_proc_app_2['NPK'];
            }
                
            /** 
             * UPDATE EPS_T_PR_HEADER 
             */
            $query_update_t_pr_header = "update 
                                                EPS_T_PR_HEADER 
                                            set 
                                                PROC_IN_CHARGE = '$procInCharge'
                                                ,APPROVER = ''
                                                ,PR_STATUS = '$prStatus'
                                                ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                ,UPDATE_BY = '$sUserId'
                                            where 
                                                PR_NO = '$prNo'";
            $conn ->query($query_update_t_pr_header);
            $approver = $procInCharge;
            
            /**
             * Search EPS_M_APP_STATUS
             **/
            $query_select_m_app_status = "select
                                            APP_STATUS_NAME
                                          from
                                            EPS_M_APP_STATUS
                                          where
                                            APP_STATUS_CD = '$prStatus'";
            $sql_select_m_app_status= $conn->query($query_select_m_app_status);
            $row_select_m_app_status = $sql_select_m_app_status->fetch(PDO::FETCH_ASSOC);
            $prStatusName = $row_select_m_app_status['APP_STATUS_NAME'];
                
            /**********************************************************************
             * SEND MAIL
             **********************************************************************/
            $mailFrom       = $sInet;
            $mailFromName   = $sNotes;  
                
            /**
             * TO NEXT APPROVER
             **/
            $query_select_m_dscid_app = "select 
                                            EPS_M_DSCID.INETML
                                            ,EPS_M_USER.PASSWORD 
                                        from 
                                            EPS_M_DSCID 
                                        inner join 
                                            EPS_M_USER 
                                        on 
                                            ltrim(EPS_M_DSCID.INOPOK) = ltrim(EPS_M_USER.USERID) 
                                        where  
                                            ltrim(ltrim(EPS_M_DSCID.INOPOK)) = ltrim('".$approver."')";
            $sql_select_m_dscid_app = $conn->query($query_select_m_dscid_app);
            $row_select_m_dscid_app = $sql_select_m_dscid_app->fetch(PDO::FETCH_ASSOC);
            if($row_select_m_dscid_app)
            {
                $mailApprover     = $row_select_m_dscid_app['INETML'];
                //$mailApprover       = 'BYAN_PURBA@denso.co.id';
                $passwordApprover   = $row_select_m_dscid_app['PASSWORD'];
                $getParamApprover   = paramEncrypt("action=open&prNo=$prNo&userId=$approver&password=$passwordApprover");
                $mailSubject        = "[EPS] AWAITING ACCEPTANCE. PR No: ".$prNo;
                $mailMessage        = "<table style='font-family: Arial; font-size: 12px;'>";
                $mailMessage        .= "<tr><td>PR No</td><td>: </td><td>".$prNo."</td></tr>";
                $mailMessage        .= "<tr><td>PR Status</td><td>:</td><td>".$prStatusName."</td></tr>";
                $mailMessage        .= "</table></font>";
                prSendMail($prNo, $mailApprover, $mailFrom, $mailFromName, $getParamApprover, $mailSubject, $mailMessage);
                
            }
            
            /**
             * TO REQUESTER
             **/
            $query_select_m_dscid_req = "select 
                                            EPS_M_DSCID.INETML
                                            ,EPS_M_USER.PASSWORD 
                                        from 
                                            EPS_M_DSCID 
                                        inner join 
                                            EPS_M_USER 
                                        on 
                                            ltrim(EPS_M_DSCID.INOPOK) = ltrim(EPS_M_USER.NPK) 
                                        where  
                                            ltrim(ltrim(EPS_M_USER.USERID)) = ltrim('".$prUserId."')";
            $sql_select_m_dscid_req= $conn->query($query_select_m_dscid_req);
            $row_select_m_dscid_req = $sql_select_m_dscid_req->fetch(PDO::FETCH_ASSOC);
            if($row_select_m_dscid_req)
            {
                $mailRequester    = $row_select_m_dscid_req['INETML'];
                //$mailRequester      = 'BYAN_PURBA@denso.co.id';
                $passwordRequester  = $row_select_m_dscid_req['PASSWORD'];
                $getParamRequester  = paramEncrypt("action=open&prNo=$prNo&userId=$prUserId&password=$passwordRequester");
                
                $mailSubject = "[EPS] BYPASS IT APPROVAL. PR No: ".$prNo;
                $approvalStatus = "Bypass IT Approval";
                $mailMessage = "<table style='font-family: Arial; font-size: 12px;'>";
                $mailMessage .= "<tr><td>PR No</td><td>: </td><td>".$prNo."</td></tr>";
                $mailMessage .= "<tr><td>PR Status</td><td>:</td><td>".$prStatusName."</td></tr>";
                $mailMessage .= "<tr><td>Approval Status</td><td>:</td><td>".$approvalStatus."</td></tr>";
                $mailMessage .= "<tr><td>Remark</td><td>:</td><td>".$approvalRemark."</td></tr>";
                $mailMessage .= "</table></font>";
                prSendMail($prNo, $mailRequester, $mailFrom, $mailFromName, $getParamRequester, $mailSubject, $mailMessage);
            }
            $msg = 'Success';
            
        }
		
		if($action == 'ResendMailPr')
        { 
            $prNo               = trim($_GET['prNoPrm']);
            $query_select_t_pr_header = "select
                                            APPROVER
                                         from
                                            EPS_T_PR_HEADER
                                         where
                                            PR_NO = '$prNo'";
            $sql_select_t_pr_header = $conn->query($query_select_t_pr_header);
            $row_select_t_pr_header = $sql_select_t_pr_header->fetch(PDO::FETCH_ASSOC);
            $approver = $row_select_t_pr_header['APPROVER'];
            
            /**********************************************************************
             * SEND MAIL
             **********************************************************************/
            $mailFrom       = $sInet;
            $mailFromName   = $sNotes;  
            
           /**
            * TO NEXT APPROVER
            **/
            $query_select_eps_m_dscid = "select 
                                            EPS_M_DSCID.INETML
                                            ,EPS_M_USER.PASSWORD 
                                         from 
                                            EPS_M_DSCID 
                                         inner join 
                                            EPS_M_USER 
                                         on 
                                            ltrim(EPS_M_DSCID.INOPOK) = ltrim(EPS_M_USER.NPK) 
                                         where  
                                            rtrim(ltrim(EPS_M_DSCID.INOPOK)) = ltrim('".$approver."')";
            $sql_select_eps_m_dscid = $conn->query($query_select_eps_m_dscid);
            $row_select_eps_m_dscid = $sql_select_eps_m_dscid->fetch(PDO::FETCH_ASSOC);
            if($row_select_eps_m_dscid){
                $mailTo           = $row_select_eps_m_dscid['INETML'];
                //$mailTo             = 'BYAN_PURBA@denso.co.id';
                $passwordApprover   = $row_select_eps_m_dscid['PASSWORD'];
                $getParamLink  = paramEncrypt("action=open&prNo=$prNo&userId=$approver&password=$passwordApprover");
                        
                $mailSubject    = MailSubjectPR("RESEND",$prNo);
                $mailMessage    = MailMessagePR($prNo, $sNama, $getParamLink, $remark, "RESEND");
                SendMailPR ($mailTo, $mailFrom, $mailFromName, $mailSubject, $mailMessage);
            }
            $msg = 'Success';
        }
        
        	if($action == 'ResendMailTakover')
        { 
            $prNo               = trim($_GET['prNoPrm']);
            $approverTakover    = trim($_GET['approverTakoverPrm']);
            $query_select_t_pr_header = "select
                                            APPROVER
                                         from
                                            EPS_T_PR_HEADER
                                         where
                                            PR_NO = '$prNo'";
            $sql_select_t_pr_header = $conn->query($query_select_t_pr_header);
            $row_select_t_pr_header = $sql_select_t_pr_header->fetch(PDO::FETCH_ASSOC);
            $approver = $row_select_t_pr_header['APPROVER'];
            
            /**********************************************************************
             * SEND MAIL
             **********************************************************************/
            $mailFrom       = $sInet;
            $mailFromName   = $sNotes;  
            
           /**
            * TO NEXT APPROVER
            **/
            $query_select_eps_m_dscid = "select 
                                            EPS_M_DSCID.INETML
                                            ,EPS_M_USER.PASSWORD 
                                         from 
                                            EPS_M_DSCID 
                                         inner join 
                                            EPS_M_USER 
                                         on 
                                            ltrim(EPS_M_DSCID.INOPOK) = ltrim(EPS_M_USER.NPK) 
                                         where  
                                            rtrim(ltrim(EPS_M_DSCID.INOPOK)) = ltrim('".$approverTakover."')";
            $sql_select_eps_m_dscid = $conn->query($query_select_eps_m_dscid);
            $row_select_eps_m_dscid = $sql_select_eps_m_dscid->fetch(PDO::FETCH_ASSOC);
            if($row_select_eps_m_dscid){
//                $mailTo           = $row_select_eps_m_dscid['INETML'];
//                //$mailTo             = 'BYAN_PURBA@denso.co.id';
//                $passwordApprover   = $row_select_eps_m_dscid['PASSWORD'];
//                $getParamLink  = paramEncrypt("action=open&prNo=$prNo&userId=$approverTakover&password=$passwordApprover");
//                        
//                $mailSubject    = MailSubjectPR("TAKEOVER",$prNo);
//                $mailMessage    = MailMessagePR($prNo, $sNama, $getParamLink, $remark, "TAKOVER");
//                SendMailPR ($mailTo, $mailFrom, $mailFromName, $mailSubject, $mailMessage);
                
                $mailFrom = 'muh.iqbal@taci.toyota-industries.com';
                $prStatus           = "Waiting Approval";
                $approvalStatus     = "Waiting for Approval";
                $approvalRemark     = "";
                //$mailApprover     = $row['INETML'];
                $mailApprover       = 'muh.iqbal@taci.toyota-industries.com';
                $passwordApprover   = $row_select_eps_m_dscid['PASSWORD'];
                $getParamApprover   = paramEncrypt("action=open&prNo=$prNo&userId=$approverTakover&password=$passwordApprover");
                $mailSubject        = "[EPS] [TAKEOVER] WAITING APPROVAL. PR No: ".$prNo;
                $mailMessage        = "<table style='font-family: Arial; font-size: 12px;'>";
                $mailMessage        .= "<tr><td>PR No</td><td>: </td><td>".$prNo."</td></tr>";
                $mailMessage        .= "<tr><td>PR Status</td><td>:</td><td>".$prStatus."</td></tr>";
                $mailMessage        .= "<tr><td>Approval Status</td><td>:</td><td>".$approvalStatus."</td></tr>";
                $mailMessage        .= "<tr><td>Remark</td><td>:</td><td>".$approvalRemark."</td></tr>";
                $mailMessage        .= "</table></font>";
                prSendMail($prNo, $mailApprover, $mailFrom, $mailFromName, $getParamApprover, $mailSubject, $mailMessage);
            }
            $msg = 'Success';
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
