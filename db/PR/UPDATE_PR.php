<?php
session_start();
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Common.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Constant.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PR/EPS_T_PR.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Email/SendEmail.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Email/PR_EMAIL.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/lib/mail_lib/crypt.php";
require $_SERVER['DOCUMENT_ROOT'].'/EPS/lib/mail_lib/class.phpmailer.php';
require $_SERVER['DOCUMENT_ROOT'].'/EPS/lib/mail_lib/class.smtp.php';
if(!isset($_SESSION['sNPK'])){
    //echo "<script>document.location.href='".constant('SessionTimeout')."';</script>";
}
$requesterMail      = $_SESSION['sinet'];
$requesterMailName  = $_SESSION['snotes'];
//$userId             = $_SESSION['sUserId'];
$userId             = $_POST['userIdLoginVal'];
$userName           = $_SESSION['sNama'];
$buLogin            = $_SESSION['sBuLogin'];
$action             = $_GET['action'];
$prNo               = $_POST['prNo'];
$buCd               = $_POST['buCd'];
$requester          = $_POST['requester'];
$purpose            = strtoupper($_POST['purposeVal']);
$purpose            = str_replace("'", "''", $purpose);
$purpose            = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $purpose);
$purpose            = preg_replace('/\s+/', ' ',$purpose);
$prCharged          = $_POST['prChargedVal'];
$prItemData         = $_POST['prItemData'];
$prAttachmentData   = $_POST['prAttachmentData'];
$reason             = strtoupper($_POST['reason']);
$approver           = '';
$prStatus;
$deviceId           = $_SERVER['REMOTE_ADDR'];
$dirNameTemp        = $_SERVER['DOCUMENT_ROOT']."/EPS/db/Attachment/Temporary/".$prNo;
$dirName            = $_SERVER['DOCUMENT_ROOT']."/EPS/db/Attachment/Fixed/".$prNo;

if($action=='approvePr'){
	/** Check next approver in EPS_T_PR_HEADER */
    $query_select_t_pr_header = "select
                                    APPROVER
                                 from
                                    EPS_T_PR_HEADER
                                 where
                                    PR_NO = '$prNo'";
    $sql_select_t_pr_header = $conn->query($query_select_t_pr_header);
    $row_select_t_pr_header = $sql_select_t_pr_header->fetch(PDO::FETCH_ASSOC);
    $prHeaderApprover = $row_select_t_pr_header['APPROVER'];
	if($prHeaderApprover == $userId)
    {
		/** Update in EPS_T_PR_HEADER */
		$query = "update
					EPS_T_PR_HEADER
				  set
					CHARGED_BU_CD = '$prCharged'
					,PURPOSE = '$purpose'
					,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
					,UPDATE_BY = '$userId'
				  where
					PR_NO = '$prNo'";
		$sql = $conn->query($query);
		
		/************************
		 ** Delete in EPS_T_PR_DETAIL
		 ************************/
		$query = "delete 
				  from 
					EPS_T_PR_DETAIL
				  where
					PR_NO = '$prNo'";
		$sql = $conn->query($query);
		/************************
		 ** Create in EPS_T_PR_DETAIL
		 ************************/
		//createPrItem($prNo, $prItemData, $userId);
		/** Read Pr Item */
                $prItemData = json_decode(stripslashes($prItemData));
                $countSpecialItem=0;
                for($x=0 ; $x < count($prItemData); $x++){
                    $itemCd         = $prItemData[$x][0];
                    $itemName       = strtoupper(trim($prItemData[$x][1]));
                    $itemName       = str_replace("'", "''", $itemName);
                    $itemName       = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $itemName);
                    $itemName       = preg_replace('/\s+/', ' ',$itemName);
                    $remark         = $prItemData[$x][2];
                    $remark         = str_replace("'", "''", $remark);
                    $remark         = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $remark);
                    $remark         = preg_replace('/\s+/', ' ',$remark);
                    $deliveryDate   = encodeDate($prItemData[$x][3]);
                    $itemType       = $prItemData[$x][4];
                    $rfiNo          = $prItemData[$x][5];
                    $accountNo      = $prItemData[$x][6];
                    $currencyCd     = $prItemData[$x][7];
                    $supplierCd     = trim($prItemData[$x][8]);
                    $supllierName   = strtoupper(trim($prItemData[$x][9]));
                    $supllierName   = str_replace("'", "''", $supllierName);
                    $supllierName   = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $supllierName);
                    $supllierName   = preg_replace('/\s+/', ' ',$supllierName);
                    $unitCd         = $prItemData[$x][10];
                    $qty            = $prItemData[$x][11];
                    $itemPrice      = $prItemData[$x][12];
                    $amount         = $prItemData[$x][13];
                    $itemStatus     = trim($prItemData[$x][14]);
                    $reasonToReject = trim($prItemData[$x][15]);
                    $rejectItemBy   = $prItemData[$x][16];
                    $inOrder        = $prItemData[$x][18];
                    $currencyCd     = "IDR";
                    $itemPrice      = number_format($itemPrice);
                    $itemPrice      = str_replace(',', '',$itemPrice);
                    $amount         = number_format($amount);
                    $amount         = str_replace(',', '',$amount);
                    
                    /**
                     * SELECT EPS_M_SUPPLIER
                     **/
                    $query_select_m_supplier = "select
                                                    SUPPLIER_CD
                                                from
                                                    EPS_M_SUPPLIER
                                                where
                                                    SUPPLIER_NAME = '$supllierName'
                                                and CURRENCY_CD = '$currencyCd'";
                    $sql_select_m_supplier = $conn->query($query_select_m_supplier);
                    $row_select_m_supplier = $sql_select_m_supplier->fetch(PDO::FETCH_ASSOC);
                    $supplierCd = $row_select_m_supplier['SUPPLIER_CD'];
                    
                   /**
                    * SELECT EPS_M_ITEM
                    **/
                    $query_select_m_item = "select
                                                ITEM_CD
                                                ,ITEM_NAME
                                            from
                                                EPS_M_ITEM
                                            where
                                                (REPLACE(REPLACE(REPLACE(ITEM_NAME, CHAR(13), ''), CHAR(9), ''), ' ', '') = REPLACE('$itemName', ' ', ''))
                                                and ITEM_CD = '".$itemCd."'
                                                and ACTIVE_FLAG = 'A'";
                    $sql_select_m_item = $conn->query($query_select_m_item);
                    $row_select_m_item = $sql_select_m_item->fetch(PDO::FETCH_ASSOC);
                    $itemCd = $row_select_m_item['ITEM_CD'];
                    if($row_select_m_item)
                    {
                        $itemName = $row_select_m_item['ITEM_NAME'];
                    }
                    
                    if($itemCd==''){
                        $itemCd='99';
                    }
                    if($supplierCd==''){
                        $supplierCd='SUP99';
                    }
                    $query = "insert into 
                                EPS_T_PR_DETAIL
                                (
                                    PR_NO
                                    ,ITEM_CD
                                    ,ITEM_NAME
                                    ,DELIVERY_DATE
                                    ,QTY
                                    ,ITEM_PRICE
                                    ,AMOUNT
                                    ,CURRENCY_CD
                                    ,ITEM_TYPE_CD
                                    ,ACCOUNT_NO
                                    ,RFI_NO
                                    ,UNIT_CD
                                    ,SUPPLIER_CD
                                    ,SUPPLIER_NAME
                                    ,REMARK
                                    ,ITEM_STATUS
                                    ,REASON_TO_REJECT_ITEM
                                    ,REJECT_ITEM_BY
                                    ,CREATE_DATE
                                    ,CREATE_BY
                                    ,UPDATE_DATE
                                    ,UPDATE_BY
                                ) 
                            values 
                                (
                                    '$prNo'
                                    ,'$itemCd'
                                    ,'$itemName'
                                    ,'$deliveryDate'
                                    ,'$qty'
                                    ,'$itemPrice'
                                    ,'$amount'
                                    ,'$currencyCd'
                                    ,'$itemType'
                                    ,'$accountNo'
                                    ,'$rfiNo'
                                    ,'$unitCd'
                                    ,'$supplierCd'
                                    ,'$supllierName'
                                    ,'$remark'
                                    ,'$itemStatus'
                                    ,'$reasonToReject'
                                    ,'$rejectItemBy'
                                    ,convert(VARCHAR(24), GETDATE(), 120)
                                    ,'$userId'
                                    ,convert(VARCHAR(24), GETDATE(), 120)
                                    ,'$userId'
                            )";
                    $sql = $conn->query($query);

                    $query = "select ITEM_GROUP_CD from EPS_M_ITEM WHERE ITEM_NAME='$itemName'";
                    $sql = $conn->query($query);
                    $row = $sql->fetch(PDO::FETCH_ASSOC);
                    $itemGroupCd = $row['ITEM_GROUP_CD'];
                    if($itemGroupCd == 'KOMPUTER'){
                        $countSpecialItem++;
                    }
                }
		/************************
		 ** Delete in EPS_T_PR_ATTACHMENT
		 ************************/
		$query = "delete 
				  from 
					EPS_T_PR_ATTACHMENT
				  where
					PR_NO = '$prNo'";
		$sql = $conn->query($query);
		/************************
		 ** Create in EPS_T_PR_ATTACHMENT
		 ************************/
		createPrAttachment($prNo, $prAttachmentData, $userId);
		/************************
		 ** Update file in EPS_T_PR_ATTACHMENT
		 ************************/
		updatePrAttachment($prNo, $prAttachmentData, $dirNameTemp, $dirName);
			
		/** Select in EPS_T_PR_HEADER */
		$query = "select 
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
		$sql = $conn->query($query);
		$row = $sql->fetch(PDO::FETCH_ASSOC);
		$approver       = $row['APPROVER'];
		$approverNo     = $row['APPROVER_NO'];
		$prUserId       = $row['USERID'];
		$countApprover  = $row['COUNT_APPROVER'];
		/** Update in EPS_T_PR_APPROVER */
		$query = "update 
					EPS_T_PR_APPROVER 
				  set 
					APPROVAL_STATUS = '".constant('AP')."'
					,APPROVAL_DATE = convert(VARCHAR(24), GETDATE(), 120)
					,DEVICE_ADDRESS = '$deviceId'
					,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
					,UPDATE_BY = '$userId'
				  where 
					PR_NO = '$prNo' 
					and NPK = '$approver'
					and APPROVER_NO = '$approverNo'";
		$sql = $conn->query($query);
		// If not last approver    
		if($approverNo < $countApprover){
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
			/** Update in EPS_T_PR_APPROVER */
			$query = "update 
						EPS_T_PR_APPROVER 
					  set 
						APPROVAL_STATUS = '".constant('WA')."'
						,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
						,UPDATE_BY = '$userId'
					  where 
						PR_NO = '$prNo'  
						and NPK ='$newApprover'
						and APPROVER_NO = '$newApproverNo'";
			$sql = $conn ->query($query);
			/** Update in EPS_T_PR_HEADER */
			$query = "update 
						EPS_T_PR_HEADER 
					  set 
						APPROVER = '$newApprover' 
						,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
						,UPDATE_BY = '$userId'
					  where 
						PR_NO = '$prNo'";
			$sql = $conn ->query($query);
			$approver = $newApprover;
			$prStatus = constant('1020');
		}
		// If last approver
		else {
			$query = "select 
						PLANT_CD
						,BU_CD
						,CHARGED_BU_CD
						,REQ_BU_CD
					  from 
						EPS_T_PR_HEADER
					  where
						PR_NO = '$prNo'";
			$sql = $conn->query($query);
			$row = $sql->fetch(PDO::FETCH_ASSOC);
			$plantCd    = $row['PLANT_CD'];
			$buCd       = $row['BU_CD'];
			$prCharged  = $row['CHARGED_BU_CD'];
			$prIssuer   = $row['REQ_BU_CD'];
			$query = "select 
						NPK 
					  from 
						EPS_M_PR_PROC_APPROVER 
					  where 
						 ltrim(EPS_M_PR_PROC_APPROVER.PLANT_CD) = ltrim('$plantCd')
						and ltrim(EPS_M_PR_PROC_APPROVER.BU_CD) = ltrim('$prIssuer')";
			$sql = $conn->query($query);
			$row = $sql->fetch(PDO::FETCH_ASSOC);
			$procInCharge = $row['NPK'];
			$prStatus = constant('1030');
			
			if($procInCharge == ''){
				$query4 = "select
							NPK
						  from
							EPS_M_PR_PROC_APPROVER
						  where
							ltrim(EPS_M_PR_PROC_APPROVER.BU_CD) = ltrim('$prIssuer')";
				$sql4 = $conn->query($query4);
				$row4 = $sql4->fetch(PDO::FETCH_ASSOC);
				$procInCharge = $row4['NPK'];
			}
			
			/** Update in EPS_T_PR_HEADER */
			$query = "update 
						EPS_T_PR_HEADER 
					  set 
						PROC_IN_CHARGE = '$procInCharge'
						,APPROVER = ''
						,PR_STATUS = '$prStatus'
						,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
						,UPDATE_BY = '$userId'
					  where 
						PR_NO = '$prNo'";
			$sql = $conn ->query($query);
			$approver = $procInCharge;
			
		}
        echo '{success:true, msg:'.json_encode(array('message'=>'update_success')).'}';
		
		/** Check existing item reject */
		$query = "select count(*) 
					as COUNT_REJECT_ITEM
				  from 
					EPS_T_PR_DETAIL
				  where
					PR_NO = '$prNo'
					and ltrim(REJECT_ITEM_BY) = ltrim('$userId') ";
		$sql = $conn->query($query);
		$row = $sql->fetch(PDO::FETCH_ASSOC);
		$countRejectItem = $row['COUNT_REJECT_ITEM'];
		
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
				
		/** Send mail to Approver */
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
					ltrim(ltrim(EPS_M_DSCID.INOPOK)) = ltrim('".$approver."')";
		$sql= $conn->query($query);
		$row = $sql->fetch(PDO::FETCH_ASSOC);
		if($row){
			$mailApprover = $row['INETML'];
			//$mailApprover = 'muh.iqbal@taci.toyota-industries.com';
			$passwordApprover = $row['PASSWORD'];
			$getParamApprover = paramEncrypt("action=open&prNo=$prNo&userId=$approver&password=$passwordApprover");
			if($prStatus == constant('1030')){
				//$mailMessage='waiting acceptance by Procurement.';
				//sendMail($prNo, $mailApprover, $requesterMail, $requesterMailName, $getParamApprover, $mailMessage);
				$mailSubject        = "[EPS] AWAITING ACCEPTANCE. PR No: ".$prNo;
                $mailMessage        = "<table style='font-family: Arial; font-size: 12px;'>";
                $mailMessage        .= "<tr><td>PR No</td><td>: </td><td>".$prNo."</td></tr>";
                $mailMessage        .= "<tr><td>PR Status</td><td>:</td><td>".$prStatusName."</td></tr>";
                $mailMessage        .= "</table></font>";
                //prSendMail($prNo, $mailApprover, $requesterMail, $requesterMailName, $getParamApprover, $mailSubject, $mailMessage);
                
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
                                            ,APPROVAL_REMARK
                                            ) 
                                    VALUES
                                            (
                                            '$prNo'
                                            ,'$company'
                                            ,'$prIssuer'
                                            ,'$prCharged'
                                            ,'$requesterMail'
                                            ,'$requesterMailName'
                                            ,'$mailApprover'
                                            ,'$getParamApprover'
                                            ,'0'
                                            ,'$mailSubject'     
                                            ,'$prStatusName'
                                            ,'$approvalStatus'
                                            ,'$approvalRemark'    
)";
		$sql= $conn->query($query_send_mail);
		$row = $sql->fetch(PDO::FETCH_ASSOC);
			}else{
				$approvalStatus     = "Waiting Approval";
                $mailSubject        = "[EPS] WAITING APPROVAL. PR No: ".$prNo;
                $mailMessage        = "<table style='font-family: Arial; font-size: 12px;'>";
                $mailMessage        .= "<tr><td>PR No</td><td>: </td><td>".$prNo."</td></tr>";
                $mailMessage        .= "<tr><td>PR Status</td><td>:</td><td>".$prStatusName."</td></tr>";
                $mailMessage        .= "<tr><td>Approval Status</td><td>:</td><td>".$approvalStatus."</td></tr>";
                $mailMessage        .= "<tr><td>Remark</td><td>:</td><td>".$approvalRemark."</td></tr>";
                $mailMessage        .= "</table></font>";
                //prSendMail($prNo, $mailApprover, $requesterMail, $requesterMailName, $getParamApprover, $mailSubject, $mailMessage);
				//$mailMessage='requires your approval.';
				//sendMail($prNo, $mailApprover, $requesterMail, $requesterMailName, $getParamApprover, $mailMessage);
                
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
                                            ,APPROVAL_REMARK
                                            ) 
                                    VALUES
                                            (
                                            '$prNo'
                                            ,'T'
                                            ,'$prIssuer'
                                            ,'$prCharged'
                                            ,'$requesterMail'
                                            ,'$requesterMailName'
                                            ,'$mailApprover'
                                            ,'$getParamApprover'
                                            ,'0'
                                            ,'$mailSubject'     
                                            ,'$prStatusName'
                                            ,'$approvalRemark' 
)";
		$sql= $conn->query($query_send_mail);
		$row = $sql->fetch(PDO::FETCH_ASSOC);
                
			}
		}
		/** Send mail to Requester */
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
					ltrim(EPS_M_USER.USERID) = ltrim('".$prUserId."')";
		$sql= $conn->query($query);
		$row = $sql->fetch(PDO::FETCH_ASSOC);
		if($row){
			$mailRequester = $row['INETML'];
			//$mailRequester = 'BYAN_PURBA@denso.co.id';
			$passwordRequester = $row['PASSWORD'];
			$getParamRequester  = paramEncrypt("action=open&prNo=$prNo&userId=$prUserId&password=$passwordRequester");
			if($countRejectItem > 0){
				$mailSubject = "[EPS] APPROVED (WITH ITEM REJECTED). PR No: ".$prNo;
				//$mailMessage = 'has been approved with some item rejected.';
			}else{
				$mailSubject = "[EPS] APPROVED. PR No: ".$prNo;
				//$mailMessage = 'has been approved.';
			}
			$approvalStatus = "Approved";
            $mailMessage = "<table style='font-family: Arial; font-size: 12px;'>";
            $mailMessage .= "<tr><td>PR No</td><td>: </td><td>".$prNo."</td></tr>";
            $mailMessage .= "<tr><td>PR Status</td><td>:</td><td>".$prStatusName."</td></tr>";
            $mailMessage .= "<tr><td>Approval Status</td><td>:</td><td>".$approvalStatus."</td></tr>";
            $mailMessage .= "<tr><td>Remark</td><td>:</td><td>".$approvalRemark."</td></tr>";
            $mailMessage .= "</table></font>";
//			sendMail($prNo, $mailRequester, $requesterMail, $requesterMailName, $getParamRequester, $mailMessage);
                        //update by Iqbal 13 Sept 2022 => Menambahkan email ketika di reject 
                        if($countRejectItem > 0){
//				prSendMail($prNo, $mailRequester, $requesterMail, $requesterMailName, $getParamRequester, $mailSubject, $mailMessage);
			}
			
                        
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
                                            ,APPROVAL_REMARK
                                            ) 
                                    VALUES
                                            (
                                            '$prNo'
                                            ,'$company'
                                            ,'$prIssuer'
                                            ,'$prCharged'
                                            ,'$requesterMail'
                                            ,'$requesterMailName'
                                            ,'$mailRequester'
                                            ,'$getParamRequester'
                                            ,'0'
                                            ,'$mailSubject'     
                                            ,'$prStatusName' 
                                            ,'$approvalStatus' 
                                            ,'$approvalRemark'
)";
		$sql= $conn->query($query_send_mail);
		$row = $sql->fetch(PDO::FETCH_ASSOC);
		}
	}
    else
    {
        echo '{success:true, msg:'.json_encode(array('message'=>'already_approved')).'}';
    }
}

if($action=='rejectPr'){
			/** Update in EPS_T_PR_HEADER */
			$query = "update
						EPS_T_PR_HEADER
					  set
						PR_STATUS = '".constant('1050')."'
						,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
						,UPDATE_BY = '$userId'
					  where
						PR_NO = '$prNo'";
			$sql = $conn->query($query);
			
			/** Search in EPS_T_APPROVER */
			$query = "select 
						APPROVER_NO 
					  from
						EPS_T_PR_APPROVER
					  where 
						PR_NO = '$prNo'
						and ltrim(NPK) = ltrim('$userId')
						and APPROVAL_STATUS = '".constant('WA')."'";
			$sql = $conn->query($query);
			$row = $sql->fetch(PDO::FETCH_ASSOC);
			$approverNo = $row['APPROVER_NO'];
			
			/** Update in EPS_T_PR_APPROVER */
			$query = "update
						EPS_T_PR_APPROVER
					  set
						APPROVAL_STATUS = '".constant('RE')."'
						,APPROVAL_REMARK = '$reason'
						,APPROVAL_DATE = convert(VARCHAR(24), GETDATE(), 120)
						,DEVICE_ADDRESS = '$deviceId'
						,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
						,UPDATE_BY = '$userId'
					  where
						PR_NO = '$prNo'
						and ltrim(NPK) = ltrim('$userId')
						and APPROVER_NO = '$approverNo'";
			$sql = $conn->query($query);
	
			/** Remove attachment for temporary folder */
			$dirNameTemp = $_SERVER['DOCUMENT_ROOT']."/EPS/db/Attachment/Temporary/".$prNo.'-temp/';
			if(is_dir($dirNameTemp)){
				$files = scandir($dirNameTemp);
				unset($files[array_search(".",$files)]);
				unset($files[array_search("..",$files)]);

				if(count($files) == 0) {
					rmdir($dirNameTemp);
				}else{
					$dh = opendir($dirNameTemp);
					while($file = readdir($dh)){
						if(!is_dir($file)){
							@unlink($dirNameTemp.$file);
						}
					}
					@unlink($dirNameTemp.'Thumbs.db');
					closedir($dh);
					rmdir($dirNameTemp);
				}
			}
	
			/**
             * SELECT EPS_T_PR_APPROVER
             **/
            $query_select_t_pr_approver = "select
                                            EPS_T_PR_APPROVER.APPROVAL_REMARK
                                            ,EPS_T_PR_APPROVER.APPROVAL_STATUS
                                            ,EPS_M_APPROVAL_STATUS.APPROVAL_STATUS_NAME
                                           from
                                            EPS_T_PR_APPROVER
                                           left join
                                            EPS_M_APPROVAL_STATUS
                                           on
                                            EPS_T_PR_APPROVER.APPROVAL_STATUS = EPS_M_APPROVAL_STATUS.APPROVAL_STATUS_CD
                                           where
                                            EPS_T_PR_APPROVER.PR_NO = '$prNo'
                                            and EPS_T_PR_APPROVER.NPK = '$userId'
                                            and EPS_T_PR_APPROVER.APPROVAL_STATUS = 'RE'";
            $sql_select_t_pr_approver = $conn->query($query_select_t_pr_approver);
            while($row_select_t_pr_approver = $sql_select_t_pr_approver->fetch(PDO::FETCH_ASSOC)){
                $approvalStatus = $row_select_t_pr_approver['APPROVAL_STATUS_NAME'];
                $approvalRemark = $row_select_t_pr_approver['APPROVAL_REMARK'];
            }
            /**
             * SELECT EPS_M_APP_STATUS
             **/
            $query_select_m_app_status = "select
                                            APP_STATUS_NAME
                                          from
                                            EPS_M_APP_STATUS
                                          where
                                            APP_STATUS_CD = '1050'";
            $sql_select_m_app_status = $conn->query($query_select_m_app_status);
            while($row_select_m_app_status = $sql_select_m_app_status->fetch(PDO::FETCH_ASSOC)){
                $prStatus = $row_select_m_app_status['APP_STATUS_NAME'];
            }
			
			/** Send mail to prev. approver from current approver */
            $query_select_count_eps_t_pr_approver = "select 
                        COUNT(*) as APPROVER_COUNT
                    from
                        EPS_T_PR_APPROVER
                    where 
                        PR_NO = '$prNo'
                        and APPROVER_NO < $approverNo";
            $sql_select_count_eps_t_pr_approver = $conn->query($query_select_count_eps_t_pr_approver);
            $row_select_count_eps_t_pr_approver = $sql_select_count_eps_t_pr_approver->fetch(PDO::FETCH_ASSOC);
            if($row_select_count_eps_t_pr_approver){
                $query2 = "select 
                        NPK
                    from
                        EPS_T_PR_APPROVER
                    where 
                        PR_NO = '$prNo'
                        and APPROVER_NO < $approverNo";
                $sql2 = $conn->query($query2);
                while($row2 = $sql2->fetch(PDO::FETCH_ASSOC)){
                    $npkApprover = $row2['NPK'];
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
                        $mailSubject = "[EPS] REJECTED (FOR APPROVER). PR No: ".$prNo;
                        $mailMessage = "<table style='font-family: Arial; font-size: 12px;'>";
                        $mailMessage .= "<tr><td>PR No</td><td>: </td><td>".$prNo."</td></tr>";
                        $mailMessage .= "<tr><td>PR Status</td><td>:</td><td>".$prStatus."</td></tr>";
                        $mailMessage .= "<tr><td>Approval Status</td><td>:</td><td>".$approvalStatus."</td></tr>";
                        $mailMessage .= "<tr><td>Remark</td><td>:</td><td>".$approvalRemark."</td></tr>";
                        $mailMessage .= "</table></font>";
                        //prSendMail($prNo, $mailApprover, $requesterMail, $requesterMailName, $getParamApprover, $mailSubject, $mailMessage);
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
                                            ,APPROVAL_REMARK
                                            ) 
                                    VALUES
                                            (
                                            '$prNo'
                                            ,'$company'
                                            ,'$prIssuer'
                                            ,'$prCharged'
                                            ,'$requesterMail'
                                            ,'$requesterMailName'
                                            ,'$mailApprover'
                                            ,'$getParamApprover'
                                            ,'0'
                                            ,'$mailSubject'     
                                            ,'$prStatus' 
                                            ,'$approvalStatus' 
                                            ,'$approvalRemark'
)";
		$sql= $conn->query($query_send_mail);
		$row = $sql->fetch(PDO::FETCH_ASSOC);
                    }
                }
            }
    
			/** Search in EPS_T_PR_HEADER */
			$query = "select 
						USERID
					  from
						EPS_T_PR_HEADER
					  where
						PR_NO = '$prNo'";
			$sql = $conn->query($query);
			$row = $sql->fetch(PDO::FETCH_ASSOC);
			$prUserId = $row['USERID'];
			
			/** Send mail to Requester */
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
						ltrim(EPS_M_USER.USERID) = ltrim('".$prUserId."')";
			$sql= $conn->query($query);
			$row = $sql->fetch(PDO::FETCH_ASSOC);
			if($row){
				$mailRequester = $row['INETML'];
				//$mailRequester = 'BYAN_PURBA@denso.co.id';
				$passwordRequester = $row['PASSWORD'];
				$getParamRequester  = paramEncrypt("action=open&prNo=$prNo&userId=$prUserId&password=$passwordRequester");
				//$mailMessage = 'has been rejected (notification for requester).';
				//sendMail($prNo, $mailRequester, $requesterMail, $requesterMailName, $getParamRequester, $mailMessage);
				$mailSubject = "[EPS] REJECTED (FOR REQUESTER). PR No: ".$prNo;
				$mailMessage = "<table style='font-family: Arial; font-size: 12px;'>";
				$mailMessage .= "<tr><td>PR No</td><td>: </td><td>".$prNo."</td></tr>";
				$mailMessage .= "<tr><td>PR Status</td><td>:</td><td>".$prStatus."</td></tr>";
				$mailMessage .= "<tr><td>Approval Status</td><td>:</td><td>".$approvalStatus."</td></tr>";
				$mailMessage .= "<tr><td>Remark</td><td>:</td><td>".$approvalRemark."</td></tr>";
				$mailMessage .= "</table></font>";
				//prSendMail($prNo, $mailRequester, $requesterMail, $requesterMailName, $getParamRequester, $mailSubject, $mailMessage);
                                
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
                                            ,APPROVAL_REMARK
                                            ) 
                                    VALUES
                                            (
                                            '$prNo'
                                            ,'$company'
                                            ,'$prIssuer'
                                            ,'$prCharged'
                                            ,'$requesterMail'
                                            ,'$requesterMailName'
                                            ,'$mailRequester'
                                            ,'$getParamRequester'
                                            ,'0'
                                            ,'$mailSubject'     
                                            ,'$prStatus' 
                                            ,'$approvalStatus' 
                                            ,'$approvalRemark'
)";
		$sql= $conn->query($query_send_mail);
		$row = $sql->fetch(PDO::FETCH_ASSOC);
			}
}

if($action=='takeOverPr'){
    $query = "select
                APPROVER_NO
              from
                EPS_T_PR_APPROVER
              where
                PR_NO = '$prNo'
                and ltrim(NPK) = ltrim('$userId')";
    $sql = $conn->query($query);
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    $approverNo = $row['APPROVER_NO'];
    $approvelRemark = "Taken over approval by ".trim($userName);
    /** Update EPS_T_PR_APPROVER for takeover approval */
    date_default_timezone_set('Asia/Jakarta');
    $dateOfByPass = date("n/j/Y H:i:s A");
    $query = "update
                EPS_T_PR_APPROVER
              set
                APPROVAL_STATUS = '".constant('TO')."'
                ,APPROVAL_REMARK = '$approvelRemark'
                ,DATE_OF_BYPASS = '$dateOfByPass'
                ,DEVICE_ADDRESS = '$deviceId'
                ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                ,UPDATE_BY = '$userId'
              where
                PR_NO = '$prNo'
                and APPROVER_NO < $approverNo
                and APPROVAL_STATUS != '".constant('BP')."'
                and APPROVAL_STATUS != '".constant('AP')."'
                and APPROVAL_STATUS != '".constant('TO')."'";
    $sql = $conn->query($query);
    /** Update EPS_T_PR_APPROVER for new approval */
    $query = "update
                EPS_T_PR_APPROVER
              set
                APPROVAL_STATUS = '".constant('WA')."'
                ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                ,UPDATE_BY = '$userId'
              where
                PR_NO = '$prNo'
                and ltrim(NPK) = ltrim('$userId')";
    $sql = $conn->query($query);
    /** Update in EPS_T_PR_HEADER */
    $query = "update
                EPS_T_PR_HEADER
              set
                APPROVER = '$userId'
                ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                ,UPDATE_BY = '$userId'
              where
                PR_NO = '$prNo'";
    $sql = $conn->query($query);
    /** Search in EPS_T_PR_APPROVER */
    $query = "select
                NPK
              from
                EPS_T_PR_APPROVER
              where
                PR_NO = '$prNo'
                and APPROVAL_STATUS = '".constant('TO')."'";
    $sql = $conn->query($query);
    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
        $npkTakeOverApprover = $row['NPK'];
        /** Send mail to Takeover Approval */
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
            $mailMessage = 'has been taken over (notification for approver).';
            sendMail($prNo, $mailTakeOverApprover, $requesterMail, $requesterMailName, $getParamTakeOverApprover, $mailMessage);
    	}
    }
    
    /** Search in EPS_T_PR_HEADER */
    $query = "select 
                USERID
              from
                EPS_T_PR_HEADER
              where
                PR_NO = '$prNo'";
    $sql = $conn->query($query);
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    $prUserId = $row['USERID'];
    
    /** Send mail to Requester */
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
                ltrim(EPS_M_DSCID.INOPOK) = ltrim('".$prUserId."')";
    $sql= $conn->query($query);
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    if($row){
        $mailRequester = $row['INETML'];
        //$mailRequester = 'BYAN_PURBA@denso.co.id';
        $passwordRequester = $row['PASSWORD'];
        $getParamRequester = paramEncrypt("action=open&prNo=$prNo&userId=$prUserId&password=$passwordRequester");
        $mailMessage = 'has been taken over (notification for requester).';
        sendMail($prNo, $mailRequester, $requesterMail, $requesterMailName, $getParamRequester, $mailMessage);
    }
    
    /** Send mail to Approver */
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
                ltrim(ltrim(EPS_M_DSCID.INOPOK)) = ltrim('".$userId."')";
    $sql= $conn->query($query);
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    if($row){
        $mailApprover = $row['INETML'];
        //$mailApprover = 'BYAN_PURBA@denso.co.id';
        $passwordApprover= $row['PASSWORD'];
        $getParamApprover = paramEncrypt("action=open&prNo=$prNo&npk=$userId&password=$passwordApprover");
        $mailMessage = 'requires your approval.';
        sendMail($prNo, $mailApprover, $requesterMail, $requesterMailName, $getParamApprover, $mailMessage);
    }
}

if($action=='transferPr'){
	$userId       	= $_SESSION['sUserId'];	
    $count 			= 0;
    $currentDate    = date(Ymd);
    $prTransferData = $_POST['prTransfer'];
    $prTransferData = json_decode(stripslashes($prTransferData));
    if(count($prTransferData) > 0 ){
        for($x = 0; $x < count($prTransferData); $x++){
            $prNo  = $prTransferData[$x];
            $query = "select     
                        EPS_T_PR_DETAIL.PR_NO
                        ,EPS_T_PR_HEADER.REQUESTER
                        ,EPS_T_PR_HEADER.CHARGED_BU_CD
                        ,EPS_T_PR_DETAIL.ITEM_CD
                        ,EPS_T_PR_DETAIL.ITEM_NAME
                        ,EPS_T_PR_DETAIL.DELIVERY_DATE
                        ,EPS_T_PR_DETAIL.QTY
                        ,EPS_T_PR_DETAIL.ITEM_PRICE
                        ,EPS_T_PR_DETAIL.AMOUNT
                        ,EPS_T_PR_DETAIL.ITEM_TYPE_CD
                        ,EPS_T_PR_DETAIL.ACCOUNT_NO
                        ,EPS_T_PR_DETAIL.RFI_NO
                        ,EPS_T_PR_DETAIL.UNIT_CD
                        ,EPS_T_PR_DETAIL.SUPPLIER_CD
                        ,EPS_T_PR_DETAIL.SUPPLIER_NAME
                        ,EPS_T_PR_DETAIL.REMARK
                    from         
                        EPS_T_PR_DETAIL 
                    left join
                        EPS_T_PR_HEADER 
                    on 
                        EPS_T_PR_DETAIL.PR_NO = EPS_T_PR_HEADER.PR_NO
                    where     
                        (EPS_T_PR_HEADER.PR_STATUS = '1030') 
                        and (EPS_T_PR_DETAIL.ITEM_STATUS = '1060') 
                        and (EPS_T_PR_HEADER.PR_NO = '$prNo')
                    order by
                        EPS_T_PR_DETAIL.PR_NO asc";
            $sql = $conn->query($query);
            while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                $prNo       = $row['PR_NO'];
                $requester  = $row['REQUESTER'];
                $prCharged  = $row['CHARGED_BU_CD'];
                $itemCd     = $row['ITEM_CD'];
                $itemName   = $row['ITEM_NAME'];
                $itemName   = str_replace("'", "''", $itemName);
                $deliveryDate= $row['DELIVERY_DATE'];
                $qty        = $row['QTY'];
                $itemPrice  = $row['ITEM_PRICE'];
                $amount     = $row['AMOUNT'];
                $itemType   = $row['ITEM_TYPE_CD'];
                $accountNo  = $row['ACCOUNT_NO'];
                $rfiNo      = $row['RFI_NO'];
                $unitCd     = $row['UNIT_CD'];
                $supplierCd = $row['SUPPLIER_CD'];
                $supplierName= $row['SUPPLIER_NAME'];
                $remark     = $row['REMARK'];

                // Define Transfer Id
                $query2 = "select count(*) as TRANSFER_COUNT from EPS_T_PR_TRANSFER where substring(TRANSFER_ID, 1, 8) = '$currentDate'";
                $sql2 = $conn->query($query2);
                $row2 = $sql2->fetch(PDO::FETCH_ASSOC);
                $transferCount = $row2['TRANSFER_COUNT'];

                if($transferCount == 0){
                    $sequences = '1';
                }else{
                    $sequences = $transferCount + 1;
                }
                $transferId = $currentDate.'T'.$sequences;
                /** 
                * Create in EPS_T_PR_TRANSFER
                **/
                $query3 = "insert into
                            EPS_T_PR_TRANSFER
                        (
                            TRANSFER_ID
                            ,PR_NO
                            ,REQUESTER
                            ,CHARGED_BU_CD
                            ,ITEM_CD
                            ,ITEM_NAME
                            ,DELIVERY_DATE
                            ,QTY
                            ,ITEM_PRICE
                            ,AMOUNT
                            ,ITEM_TYPE_CD
                            ,ACCOUNT_NO
                            ,RFI_NO
                            ,UNIT_CD
                            ,SUPPLIER_CD
                            ,SUPPLIER_NAME
                            ,REMARK
                            ,CREATE_DATE
                            ,CREATE_BY
                            ,UPDATE_DATE
                            ,UPDATE_BY
                        )
                        values
                        (
                            '$transferId'
                            ,'$prNo'
                            ,'$requester'
                            ,'$prCharged'
                            ,'$itemCd'
                            ,'$itemName'
                            ,'$deliveryDate'
                            ,'$qty'
                            ,'$itemPrice'
                            ,'$amount'
                            ,'$itemType'
                            ,'$accountNo'
                            ,'$rfiNo'
                            ,'$unitCd'
                            ,'$supplierCd'
                            ,'$supplierName'
                            ,'$remark'
                            ,convert(VARCHAR(24), GETDATE(), 120)
                            ,'$userId'
                            ,convert(VARCHAR(24), GETDATE(), 120)
                            ,'$userId'
                        )";
                $conn->query($query3);

                $query4 = "select     
                        EPS_T_PR_ATTACHMENT.PR_NO
                        ,EPS_T_PR_ATTACHMENT.ITEM_CD
                        ,EPS_T_PR_ATTACHMENT.ITEM_NAME
                        ,EPS_T_PR_ATTACHMENT.FILE_NAME
                        ,EPS_T_PR_ATTACHMENT.FILE_TYPE
                        ,EPS_T_PR_ATTACHMENT.FILE_SIZE
                    from         
                        EPS_T_PR_ATTACHMENT 
                    where 
                        EPS_T_PR_ATTACHMENT.PR_NO = '$prNo'
                        and EPS_T_PR_ATTACHMENT.ITEM_NAME = '$itemName')";
                $sql4 = $conn->query($query4);

                /** 
                * Update in EPS_T_PR_HEADER
                **/
                $query5 = "update 
                            EPS_T_PR_HEADER
                        set
                            PR_STATUS = '".constant('1040')."'
                            ,PROC_ACCEPT_DATE = convert(VARCHAR(24), GETDATE(), 120)
                            ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                            ,UPDATE_BY = '$userId'
                        where
                            PR_NO = '$prNo'";
                $conn->query($query5);
                $count++;
            }
        } 
    }else{
        if(count($prTransferData) == 0){
            $query = "select     
                        EPS_T_PR_DETAIL.PR_NO
                        ,EPS_T_PR_HEADER.REQUESTER
                        ,EPS_T_PR_HEADER.CHARGED_BU_CD
                        ,EPS_T_PR_DETAIL.ITEM_CD
                        ,EPS_T_PR_DETAIL.ITEM_NAME
                        ,EPS_T_PR_DETAIL.DELIVERY_DATE
                        ,EPS_T_PR_DETAIL.QTY
                        ,EPS_T_PR_DETAIL.ITEM_PRICE
                        ,EPS_T_PR_DETAIL.AMOUNT
                        ,EPS_T_PR_DETAIL.ITEM_TYPE_CD
                        ,EPS_T_PR_DETAIL.ACCOUNT_NO
                        ,EPS_T_PR_DETAIL.RFI_NO
                        ,EPS_T_PR_DETAIL.UNIT_CD
                        ,EPS_T_PR_DETAIL.SUPPLIER_CD
                        ,EPS_T_PR_DETAIL.SUPPLIER_NAME
                        ,EPS_T_PR_DETAIL.REMARK
                    from         
                        EPS_T_PR_DETAIL 
                    left join
                        EPS_T_PR_HEADER 
                    on 
                        EPS_T_PR_DETAIL.PR_NO = EPS_T_PR_HEADER.PR_NO
                    where     
                        (EPS_T_PR_HEADER.PR_STATUS = '1030') 
                        and (EPS_T_PR_DETAIL.ITEM_STATUS = '1060') 
                        and (EPS_T_PR_HEADER.PROC_IN_CHARGE = '$userId')
                    order by
                        EPS_T_PR_DETAIL.PR_NO asc";
            $sql = $conn->query($query);
            while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                $prNo       = $row['PR_NO'];
                $requester  = $row['REQUESTER'];
                $prCharged  = $row['CHARGED_BU_CD'];
                $itemCd     = $row['ITEM_CD'];
                $itemName   = $row['ITEM_NAME'];
                $itemName   = str_replace("'", "''", $itemName);
                $deliveryDate= $row['DELIVERY_DATE'];
                $qty        = $row['QTY'];
                $itemPrice  = $row['ITEM_PRICE'];
                $amount     = $row['AMOUNT'];
                $itemType   = $row['ITEM_TYPE_CD'];
                $accountNo  = $row['ACCOUNT_NO'];
                $rfiNo      = $row['RFI_NO'];
                $unitCd     = $row['UNIT_CD'];
                $supplierCd = $row['SUPPLIER_CD'];
                $supplierName= $row['SUPPLIER_NAME'];
                $remark     = $row['REMARK'];

                // Define Transfer Id
                $query2 = "select count(*) as TRANSFER_COUNT from EPS_T_PR_TRANSFER where substring(TRANSFER_ID, 1, 8) = '$currentDate'";
                $sql2 = $conn->query($query2);
                $row2 = $sql2->fetch(PDO::FETCH_ASSOC);
                $transferCount = $row2['TRANSFER_COUNT'];

                if($transferCount == 0){
                    $sequences = '1';
                }else{
                    $sequences = $transferCount + 1;
                }
                $transferId = $currentDate.'T'.$sequences;
                /** 
                * Create in EPS_T_PR_TRANSFER
                **/
                $query3 = "insert into
                            EPS_T_PR_TRANSFER
                        (
                            TRANSFER_ID
                            ,PR_NO
                            ,REQUESTER
                            ,CHARGED_BU_CD
                            ,ITEM_CD
                            ,ITEM_NAME
                            ,DELIVERY_DATE
                            ,QTY
                            ,ITEM_PRICE
                            ,AMOUNT
                            ,ITEM_TYPE_CD
                            ,ACCOUNT_NO
                            ,RFI_NO
                            ,UNIT_CD
                            ,SUPPLIER_CD
                            ,SUPPLIER_NAME
                            ,REMARK
                            ,CREATE_DATE
                            ,CREATE_BY
                            ,UPDATE_DATE
                            ,UPDATE_BY
                        )
                        values
                        (
                            '$transferId'
                            ,'$prNo'
                            ,'$requester'
                            ,'$prCharged'
                            ,'$itemCd'
                            ,'$itemName'
                            ,'$deliveryDate'
                            ,'$qty'
                            ,'$itemPrice'
                            ,'$amount'
                            ,'$itemType'
                            ,'$accountNo'
                            ,'$rfiNo'
                            ,'$unitCd'
                            ,'$supplierCd'
                            ,'$supplierName'
                            ,'$remark'
                            ,convert(VARCHAR(24), GETDATE(), 120)
                            ,'$userId'
                            ,convert(VARCHAR(24), GETDATE(), 120)
                            ,'$userId'
                        )";
                $conn->query($query3);

                $query4 = "select     
                        EPS_T_PR_ATTACHMENT.PR_NO
                        ,EPS_T_PR_ATTACHMENT.ITEM_CD
                        ,EPS_T_PR_ATTACHMENT.ITEM_NAME
                        ,EPS_T_PR_ATTACHMENT.FILE_NAME
                        ,EPS_T_PR_ATTACHMENT.FILE_TYPE
                        ,EPS_T_PR_ATTACHMENT.FILE_SIZE
                    from         
                        EPS_T_PR_ATTACHMENT 
                    where 
                        EPS_T_PR_ATTACHMENT.PR_NO = '$prNo'
                        and EPS_T_PR_ATTACHMENT.ITEM_NAME = '$itemName')";
                $sql4 = $conn->query($query4);

                /** 
                * Update in EPS_T_PR_HEADER
                **/
                $query5 = "update 
                            EPS_T_PR_HEADER
                        set
                            PR_STATUS = '".constant('1040')."'
                            ,PROC_ACCEPT_DATE = convert(VARCHAR(24), GETDATE(), 120)
                            ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                            ,UPDATE_BY = '$userId'
                        where
                            PR_NO = '$prNo'";
                $conn->query($query5);
                $count++;
            }
        }
    }
    echo '{success:true, msg:'.json_encode(array('message'=>$count)).'}';
}
	   /**
        * ============================================================================================
        * BYPASS IT APPROVAL PR
        * ============================================================================================
        **/
        if($action=='bypassITApprovalPr'){
            /** Select in EPS_T_PR_HEADER */
            $query = "select 
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
            $sql = $conn->query($query);
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            $approver       = $row['APPROVER'];
            $approverNo     = $row['APPROVER_NO'];
            $prUserId       = $row['USERID'];
            /** Update in EPS_T_PR_APPROVER */
            $query = "update 
			EPS_T_PR_APPROVER 
                      set 
			APPROVAL_STATUS = '".constant('BP')."'
			,APPROVAL_DATE = convert(VARCHAR(24), GETDATE(), 120)
			,DEVICE_ADDRESS = '$deviceId'
			,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
			,UPDATE_BY = '$userId'
                    where 
			PR_NO = '$prNo' 
			and NPK = '$approver'
			and APPROVER_NO = '$approverNo'";
            $sql = $conn->query($query);
            $query = "select 
                                PLANT_CD
                                ,BU_CD
                                ,CHARGED_BU_CD
                                ,REQ_BU_CD
                            from 
                                EPS_T_PR_HEADER
                            where
                                PR_NO = '$prNo'";
            $sql = $conn->query($query);
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            $plantCd    = $row['PLANT_CD'];
            $buCd       = $row['BU_CD'];
            $prCharged  = $row['CHARGED_BU_CD'];
            $prIssuer   = $row['REQ_BU_CD'];
            $query = "select 
                        NPK 
                      from 
                        EPS_M_PR_PROC_APPROVER 
                      where 
                        ltrim(EPS_M_PR_PROC_APPROVER.PLANT_CD) = ltrim('$plantCd')
                        and ltrim(EPS_M_PR_PROC_APPROVER.BU_CD) = ltrim('$prIssuer')";
            $sql = $conn->query($query);
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            $procInCharge = $row['NPK'];
            $prStatus = constant('1030');

            if($procInCharge == ''){
                $query4 = "select
                            NPK
                           from
                            EPS_M_PR_PROC_APPROVER
                           where
                            ltrim(EPS_M_PR_PROC_APPROVER.BU_CD) = ltrim('$prIssuer')";
                $sql4 = $conn->query($query4);
                $row4 = $sql4->fetch(PDO::FETCH_ASSOC);
                $procInCharge = $row4['NPK'];
            }
            /** Update in EPS_T_PR_HEADER */
            $query = "update 
                        EPS_T_PR_HEADER 
                      set 
                        PROC_IN_CHARGE = '$procInCharge'
                        ,APPROVER = ''
                        ,PR_STATUS = '$prStatus'
                        ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                        ,UPDATE_BY = '$userId'
                      where 
                         PR_NO = '$prNo'";
            $sql = $conn ->query($query);
            $approver = $procInCharge;
            
            $dirNameTemp        = $_SERVER['DOCUMENT_ROOT']."/EPS/db/Attachment/Temporary/".$prNo;
            /** Check existing Temp folder to delete Temp folder **/ 
            if(is_dir($dirNameTemp.'-temp')){
                $dh = opendir($dirNameTemp.'-temp');

                while($file = readdir($dh)){
                    if(!is_dir($file)){
                        @unlink($dirNameTemp.'-temp'.'/'.$file);
                    }
                }
                @unlink($dirNameTemp.'-temp'.'/'.'Thumbs.db');
                closedir($dh);
                rmdir($dirNameTemp.'-temp');   
            }
            
            echo '{success:true, msg:'.json_encode(array('message'=>'update_success')).'}';
            
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
                
            /** Send mail to Approver */
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
			ltrim(ltrim(EPS_M_DSCID.INOPOK)) = ltrim('".$approver."')";
            $sql= $conn->query($query);
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            if($row){
                $mailApprover = $row['INETML'];
		//$mailApprover = 'BYAN_PURBA@denso.co.id';
		$passwordApprover = $row['PASSWORD'];
		$getParamApprover = paramEncrypt("action=open&prNo=$prNo&userId=$approver&password=$passwordApprover");
		
		$mailSubject        = "[EPS] AWAITING ACCEPTANCE. PR No: ".$prNo;
                $mailMessage        = "<table style='font-family: Arial; font-size: 12px;'>";
                $mailMessage        .= "<tr><td>PR No</td><td>: </td><td>".$prNo."</td></tr>";
                $mailMessage        .= "<tr><td>PR Status</td><td>:</td><td>".$prStatusName."</td></tr>";
                $mailMessage        .= "</table></font>";
                prSendMail($prNo, $mailApprover, $requesterMail, $requesterMailName, $getParamApprover, $mailSubject, $mailMessage);
            }
            /** Send mail to Requester */
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
					ltrim(EPS_M_USER.USERID) = ltrim('".$prUserId."')";
            $sql= $conn->query($query);
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            if($row){
                $mailRequester = $row['INETML'];
		//$mailRequester = 'BYAN_PURBA@denso.co.id';
		$passwordRequester = $row['PASSWORD'];
		$getParamRequester  = paramEncrypt("action=open&prNo=$prNo&userId=$prUserId&password=$passwordRequester");
		$mailSubject = "[EPS] BYPASS IT APPROVAL. PR No: ".$prNo;
            }
            $approvalStatus = "Bypass IT Approval";
            
            $mailMessage = "<table style='font-family: Arial; font-size: 12px;'>";
            $mailMessage .= "<tr><td>PR No</td><td>: </td><td>".$prNo."</td></tr>";
            $mailMessage .= "<tr><td>PR Status</td><td>:</td><td>".$prStatusName."</td></tr>";
            $mailMessage .= "<tr><td>Approval Status</td><td>:</td><td>".$approvalStatus."</td></tr>";
            $mailMessage .= "<tr><td>Remark</td><td>:</td><td>".$approvalRemark."</td></tr>";
            $mailMessage .= "</table></font>";
            //sendMail($prNo, $mailRequester, $requesterMail, $requesterMailName, $getParamRequester, $mailMessage);
            prSendMail($prNo, $mailRequester, $requesterMail, $requesterMailName, $getParamRequester, $mailSubject, $mailMessage);
	}
?>
