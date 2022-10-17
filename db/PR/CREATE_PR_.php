<?php session_start();
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Common.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Constant.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PR/EPS_T_PR_SEQUENCE.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PR/EPS_T_PR.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Email/PR_EMAIL.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Email/SendEmail.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/lib/mail_lib/crypt.php";
require $_SERVER['DOCUMENT_ROOT'].'/EPS/lib/mail_lib/class.phpmailer.php';
require $_SERVER['DOCUMENT_ROOT'].'/EPS/lib/mail_lib/class.smtp.php';

if(isset($_SESSION['sNPK'])){
    $sNPK               = $_SESSION['sNPK'];
    if($sNPK  != ''){
        
        $requesterMail      = $_SESSION['sinet'];
        $requesterMailName  = $_SESSION['snotes'];
        //$userId             = $_SESSION['sUserId'];
        //$buLogin            = $_SESSION['sBuLogin'];
        $userId             = $_POST['userIdLoginVal'];
        $buLogin            = $_POST['buLoginVal'];
        $oldPrNo            = $_POST['oldPrNoVal'];
        $prNo               = $_POST['prNoVal'];
        $prDate             = encodeDate($_POST['prDateVal']);
        $specialType        = $_POST['specialTypeVal'];
        $purpose            = strtoupper($_POST['purposeVal']);
        $purpose            = str_replace("'", "''", $purpose);
        $purpose            = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $purpose);
        $purpose            = preg_replace('/\s+/', ' ',$purpose);
        $requester          = $_POST['requesterVal'];
        $plant              = $_POST['plantCdVal'];
        $company            = $_POST['companyCdVal'];
        $extNo              = $_POST['extNoVal'];
        $buCd               = $_POST['buCdVal'];
        $sectionCd          = $_POST['sectionCdVal'];
        $prIssuer           = $_POST['prIssuerVal'];
        $prCharged          = $_POST['prChargedVal'];
        $specialApprover    = $_POST['specialApproverVal'];
        $actionBtn          = $_POST['actionBtnVal'];
        $actionPr           = $_POST['actionPrVal'];
        $prItemData         = $_POST['prItemData'];
        $prAttachmentData   = $_POST['prAttachmentData'];
        $prApproverData     = $_POST['prApproverData'];
        $prApproverByPassData= $_POST['prApproverByPassData'];     
        $prStatus           = ''; 
        $dirNameTemp        =$_SERVER['DOCUMENT_ROOT']."/EPS/db/Attachment/Temporary/".$prNo;
        $dirName            =$_SERVER['DOCUMENT_ROOT']."/EPS/db/Attachment/Fixed/".$prNo;
		/**
		 * Check existing PR number
		 **/
		$query = "select PR_NO from EPS_T_PR_HEADER where PR_NO = '$prNo'";
		$sql = $conn->query($query);
		$row = $sql->fetch(PDO::FETCH_ASSOC);
		$currentPrNo=$row['PR_NO'];

		/** Set initial value from array of approver */
		$prApproverData = json_decode(stripslashes($prApproverData)); 
		if($actionBtn=='Send'){
			$nextApprover = $prApproverData[0];         // put index 0 in array
			$prStatus = constant('1020');
		}else{
			$nextApprover = '';
			$prStatus = constant('1010');
		}
			
		if(!$currentPrNo){
            /************************
             ** Update in EPS_T_SEQUENCES
             ************************/
            getPrNo($userId, trim($buLogin), 'updatePrNo');
		
            /************************
             ** Create in EPS_T_PR_HEADER
             ************************/
            $query = "insert into
						EPS_T_PR_HEADER
                      (
						PR_NO
						,PR_STATUS
						,ISSUED_DATE
						,REQUESTER
						,BU_CD
						,SECTION_CD
						,PLANT_CD
						,COMPANY_CD
						,EXT_NO
						,REQ_BU_CD
						,CHARGED_BU_CD
						,SPECIAL_TYPE_ID
						,PURPOSE
						,APPROVER
						,USERID
						,CREATE_DATE
						,CREATE_BY
						,UPDATE_DATE
						,UPDATE_BY
                      )
                      values
                      (
						'$prNo'
						,'$prStatus'
						,'$prDate'
						,'$requester'
						,'$buCd'
						,'$sectionCd'
						,'$plant'
						,'$company'
						,'$extNo'
						,'$prIssuer'
						,'$prCharged'
						,'$specialType'
						,'$purpose'
						,'$nextApprover'
						,'$userId'
						,convert(VARCHAR(24), GETDATE(), 120)
						,'$userId'
						,convert(VARCHAR(24), GETDATE(), 120)
						,'$userId'
                      )";
            $sql =$conn->query($query);
		
            /************************
             ** Create in EPS_T_PR_DETAIL
             ************************/
            //$countSpecialItem = createPrItem($prNo, $prItemData, $userId);
			/** Read Pr Item */
            $prItemData = json_decode(stripslashes($prItemData));
            $countSpecialItem=0;
            for($x=0 ; $x < count($prItemData); $x++){
                $itemCd         = $prItemData[$x][0];
                $itemName       = strtoupper(trim($prItemData[$x][1]));
                $itemName       = str_replace("'", "''", $itemName);
                $itemName       = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $itemName);
                $itemName       = preg_replace('/\s+/', ' ',$itemName);
                $itemNameSplit  = explode("~", $itemName);
                $itemNameTrim   = trim($itemNameSplit[0]);
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
				$currencyCd		= "IDR";
                $itemPrice      = number_format($itemPrice);
                $itemPrice      = str_replace(',', '',$itemPrice);
                $amount         = number_format($amount);
                $amount         = str_replace(',', '',$amount);
                $remark2         = $prItemData[$x][18];
                $remark2         = str_replace("'", "''", $remark2);
                $remark2         = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $remark2);
                $remark2         = preg_replace('/\s+/', ' ',$remark2);
				
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
                                            (REPLACE(REPLACE(REPLACE(ITEM_NAME, CHAR(13), ''), CHAR(9), ''), ' ', '') = REPLACE('$itemNameTrim', ' ', ''))
                                            and ITEM_CD = '".$itemCd."'
                                            and ACTIVE_FLAG = 'A'";
                $sql_select_m_item = $conn->query($query_select_m_item);
                $row_select_m_item = $sql_select_m_item->fetch(PDO::FETCH_ASSOC);
                $itemCd = $row_select_m_item['ITEM_CD'];
                if($row_select_m_item)
                {
                    $itemName = $row_select_m_item['ITEM_NAME'];
                    
                }
				
				if(trim($itemCd)==''){
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
                                ,REMARK_2
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
                                ,'$remark2'
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
             ** Create in EPS_T_PR_APPROVER
             ************************/
            //$newApproverNo = createPrApprover($prNo, $buLogin, $actionBtn, $prApproverData, $prApproverByPassData, $userId);
			/** Read Pr Approver */
            $prApproverByPassData= json_decode(stripslashes($prApproverByPassData));
            $flagApp='';
            $newApproverNo='';
            date_default_timezone_set('Asia/Jakarta');
            for($z = 0; $z < count($prApproverData); $z++){
                $approver       = $prApproverData[$z];
                $approverNo     = $z + 1;
                $approvalStatus = '';
                $approvalRemark = '';
                $dateOfByPass   = '';
                for($k = 0; $k < count($prApproverByPassData); $k++){
                    $approverNoByPass = $prApproverByPassData[$k][0];
                    if($approverNo == $approverNoByPass){
                        $approvalStatus = constant('BP');
                        $approvalRemark = $prApproverByPassData[$k][1];  
                        $dateOfByPass = date("n/j/Y H:i:s A");
                        break; 
                    }
                } 
                if($flagApp == '' && $approvalStatus != constant('BP')){
                    $newApproverNo = $z;
                    $flagApp = '1';
                } 
                if($z == 0){
                    if($actionBtn == 'Send'){
                        if($approvalStatus != constant('BP')){
                            $approvalStatus = constant('WA');
                        }
                    }
                }else{
                    if($actionBtn == 'Send'){
                        if($newApproverNo == $z){
                            $approvalStatus = constant('WA');
                        }     
                    }   
                }
                $query = "insert into 
                            EPS_T_PR_APPROVER
                            (
                                PR_NO
                                ,BU_CD
                                ,APPROVER_NO
                                ,NPK
                                ,APPROVAL_STATUS
                                ,APPROVAL_REMARK
                                ,DATE_OF_BYPASS
                                ,CREATE_DATE
                                ,CREATE_BY
                                ,UPDATE_DATE
                                ,UPDATE_BY
                            ) 
                        values 
                            (
                                '$prNo'
                                ,'$buLogin'
                                ,'$approverNo'
                                ,'$approver'
                                ,'$approvalStatus'
                                ,'$approvalRemark'
                                ,'$dateOfByPass'
                                ,convert(VARCHAR(24), GETDATE(), 120)
                                ,'$userId'
                                ,convert(VARCHAR(24), GETDATE(), 120)
                                ,'$userId'
                            )";
                $sql = $conn->query($query);
            }
			
            /************************
             ** Insert Special Approver in EPS_T_PR_APPROVER
             ************************/
            if(($countSpecialItem > 0 || $specialType == 'IT') && trim($buCd) != '3300'){
                createPrSpecialApprover($prNo, $buLogin, $prApproverData, $userId);
            }
		
            /************************
             ** Update in EPS_T_PR_HEADER
             ************************/
            if($countSpecialItem > 0){
                updatePrHeaderSpecialType($prNo, $userId);
            }
		
            /** Check existing Fix folder to remove Fix folder **/ 
            if(is_dir($dirName)){
                $files = scandir($dirName);
				unset($files[array_search(".",$files)]);
				unset($files[array_search("..",$files)]);
					
				if(count($files) == 0) {
                    rmdir($dirName);
				}else{
                    $dh = opendir($dirName);
                    while($file = readdir($dh)){
						if(!is_dir($file)){
                            @unlink($dirName.'/'.$file);
						}
                    }
                    closedir($dh);
                    rmdir($dirName);
				}
            }
            if($oldPrNo != $prNo){
            	$dirNameTemp        = $_SERVER['DOCUMENT_ROOT']."/EPS/db/Attachment/Temporary/".$oldPrNo;
            }
            $dirNameTemp = $dirNameTemp.'-'.$userId;
            /** Check existing Temp folder to create Fix folder **/ 
            if(is_dir($dirNameTemp.'-temp')){               // If Temp folder exist
                $files = scandir($dirNameTemp.'-temp');
				unset($files[array_search(".",$files)]);
				unset($files[array_search("..",$files)]);
		
                if(count($files) == 0) {                    // If empty folder
                    @unlink($dirNameTemp.'-temp'.'/'.'Thumbs.db');
					rmdir($dirNameTemp.'-temp');
                }else{
                    if(count($prAttachmentData) > 0){
                        mkdir($dirName);                     // Create Fix folder  
                    }
				}
            }
            if($oldPrNo != $prNo){
				$dirNameTemp        = $_SERVER['DOCUMENT_ROOT']."/EPS/db/Attachment/Temporary/".$oldPrNo;
            }else{
				$dirNameTemp        = $_SERVER['DOCUMENT_ROOT']."/EPS/db/Attachment/Temporary/".$prNo;
            }
            /************************
             ** Create in EPS_T_PR_ATTACHMENT
             ************************/
            if(count($prAttachmentData) > 0){
            	createPrAttachment($prNo,$prAttachmentData,$userId);
				$query = "select 
                            FILE_NAME
						  from
                            EPS_T_PR_ATTACHMENT
						  where
                            PR_NO = '$prNo'";
				$sql = $conn->query($query);
				while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                    $fileName = $row['FILE_NAME'];
                    copy($dirNameTemp.'-'.$userId.'-temp/'.$fileName,$dirName.'/'.$fileName);
                    unlink($dirNameTemp.'-'.$userId.'-temp/'.$fileName);
				}
            }
		
            if($oldPrNo != $prNo){
                $dirNameTemp        = $_SERVER['DOCUMENT_ROOT']."/EPS/db/Attachment/Temporary/".$oldPrNo;
            }
            $dirNameTemp = $dirNameTemp.'-'.$userId;
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
            
            /************************
             ** Action Send PR 
             ************************/
            $newApprover = $prApproverData[$newApproverNo];
            if($actionBtn == 'Send'){
				$nextApprover = $newApprover;
				$query = "update 
                            EPS_T_PR_HEADER 
						  set
                            APPROVER = '$nextApprover'
                            ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                            ,UPDATE_BY = '$userId'
						  where
                            PR_NO = '$prNo'";
				$sql = $conn->query($query);
				sendPr($prNo, $company, $prIssuer, $prCharged, $requesterMail, $requesterMailName, $newApprover);
            }
            echo '{success:true, msg:'.json_encode(array('message'=>'success_create')).'}';
		}
		else{
            if($actionPr == 'Edit'){
                /** Update in EPS_T_PR_HEADER */
                $query = "update
                            EPS_T_PR_HEADER
						  set
                            PR_STATUS       = '$prStatus'
                            ,ISSUED_DATE    = '$prDate'
                            ,EXT_NO         = '$extNo'
                            ,REQ_BU_CD      = '$prIssuer'
                            ,CHARGED_BU_CD  = '$prCharged'
                            ,SPECIAL_TYPE_ID= '$specialType'
                            ,PURPOSE        = '$purpose'
                            ,APPROVER       = '$nextApprover'
                            ,UPDATE_DATE    = convert(VARCHAR(24), GETDATE(), 120)
                            ,UPDATE_BY      = '$userId'
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
				//$countSpecialItem = createPrItem($prNo, $prItemData, $userId);
				/** Read Pr Item */
				$prItemData = json_decode(stripslashes($prItemData));
				$countSpecialItem=0;
				for($x=0 ; $x < count($prItemData); $x++){
					$itemCd         = $prItemData[$x][0];
					$itemName       = strtoupper(trim($prItemData[$x][1]));
					$itemName       = str_replace("'", "''", $itemName);
                    $itemName       = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $itemName);
					$itemName       = preg_replace('/\s+/', ' ',$itemName);
                                        $itemNameSplit  = explode("~", $itemName);
                $itemNameTrim   = trim($itemNameSplit[0]);
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
                    $currencyCd     = "IDR";
					$itemPrice      = number_format($itemPrice);
					$itemPrice      = str_replace(',', '',$itemPrice);
                    $amount         = number_format($amount);
                    $amount         = str_replace(',', '',$amount);
                    $remark2         = $prItemData[$x][18];
					$remark2         = str_replace("'", "''", $remark2);
                    $remark2         = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $remark2);
					$remark2         = preg_replace('/\s+/', ' ',$remark2);
					
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
                                                (REPLACE(REPLACE(REPLACE(ITEM_NAME, CHAR(13), ''), CHAR(9), ''), ' ', '') = REPLACE('$itemNameTrim', ' ', ''))
                                                and ITEM_CD = '".$itemCd."'
                                                and ACTIVE_FLAG = 'A'";
                    $sql_select_m_item = $conn->query($query_select_m_item);
                    $row_select_m_item = $sql_select_m_item->fetch(PDO::FETCH_ASSOC);
                    $itemCd = $row_select_m_item['ITEM_CD'];
                    if($row_select_m_item)
                    {
                        $itemName = $row_select_m_item['ITEM_NAME'];
                    }
					
					if(trim($itemCd)==''){
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
                                                                                ,REMARK_2
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
                                                                                ,'$remark2'
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
				 ** Delete in EPS_T_PR_APPROVER
				 ************************/
				$query = "delete 
							from 
							EPS_T_PR_APPROVER 
						  where
							PR_NO = '$prNo'";
				$sql = $conn->query($query);
				/************************
				 ** Create in EPS_T_PR_APPROVER
				 ************************/
				$newApproverNo = createPrApprover($prNo, $buLogin, $actionBtn, $prApproverData, $prApproverByPassData, $userId);
				
				/************************
				 ** Insert Special Approver in EPS_T_PR_APPROVER
				 ************************/
				if(($countSpecialItem > 0 || $specialType == 'IT') && trim($buCd) != '3300'){
					createPrSpecialApprover($prNo, $buLogin, $prApproverData, $userId);
				}
		
				/************************
				** Update in EPS_T_PR_HEADER
				************************/
				if($countSpecialItem > 0){
					updatePrHeaderSpecialType($prNo, $userId);
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
		
				/************************
				 ** Action Send PR 
				 ************************/
				$newApprover = $prApproverData[$newApproverNo];
				if($actionBtn == 'Send'){
					$nextApprover = $newApprover;
					$query = "update 
								EPS_T_PR_HEADER 
							  set
								APPROVER = '$nextApprover'
								,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
								,UPDATE_BY = '$userId'
							  where
								PR_NO = '$prNo'";
					$sql = $conn->query($query);
					sendPr($prNo, $company, $prIssuer, $prCharged, $requesterMail, $requesterMailName, $newApprover);
				}
				echo '{success:true, msg:'.json_encode(array('message'=>'success_edit')).'}';
			}else{
				if($actionPr == 'Crea' || $actionPr == 'Uplo' || $actionPr == 'Repl'){
					echo '{success:true, msg:'.json_encode(array('message'=>'failed_create')).'}';
				}else{
                    echo '{success: true, msg:'.json_encode(array('message'=>'SessionTimeout')).'}';
				}
            }
		}
    }else{
        echo '{success: true, msg:'.json_encode(array('message'=>'SessionTimeout')).'}';
    }
}else{
    echo '{success: true, msg:'.json_encode(array('message'=>'SessionTimeout')).'}';
}
?>
