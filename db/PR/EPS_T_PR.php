<?php
/************************
 ** Update in EPS_T_PR_HEADER
 ************************/
function updatePrHeaderSpecialType($prNo, $userId){
    include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";
    /** Update in EPS_T_PR_HEADER */
    $query = "update 
                EPS_T_PR_HEADER 
              set 
                SPECIAL_TYPE_ID = 'IT'
                ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                ,UPDATE_BY = '$userId'
              where 
                PR_NO = '$prNo'";
    $sql = $conn->query($query);
}
/************************
 ** Create in EPS_T_PR_DETAIL
 ************************/
function createPrItem($prNo,$prItemData,$userId){
    include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";
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
		/**
         * SELECT EPS_M_SUPPLIER
         **/
        $query_select_m_supplier = "select
                                        SUPPLIER_CD
                                    from
                                        EPS_M_SUPPLIER
                                    where
                                        SUPPLIER_NAME = '$supllierName'";
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
                    ) 
                values 
                    (
                        '$prNo'
                        ,'$itemCd'
                        ,'$itemNameTrim'
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
    return $countSpecialItem;
}
/************************
 ** Create in EPS_T_PR_APPROVER
 ************************/
function createPrApprover($prNo, $buCd, $actionBtn, $prApproverData, $prApproverByPassData,$userId){
    include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";
    include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Constant.php";
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
                        ,'$buCd'
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
    return $newApproverNo;
}
/************************
 ** Insert Special Approver in EPS_T_PR_APPROVER
 ************************/
function createPrSpecialApprover($prNo, $buCd, $prApproverData ,$userId){
    include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";
    include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Constant.php";
    /** Select in EPS_M_PR_SPECIAL_APPROVER */
    $query = "select 
                NPK 
              from 
                EPS_M_PR_SPECIAL_APPROVER
              where
                SPECIAL_APPROVER_CD = '001'";
    $sql = $conn->query($query);
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    $specialApprover = $row['NPK'];
    $countApprover = (count($prApproverData)) + 1;
    /** Insert in EPS_T_PR_APPROVER */
    $query = "insert into
                EPS_T_PR_APPROVER
              (
                PR_NO
                ,BU_CD
                ,APPROVER_NO
                ,NPK
                ,CREATE_DATE
                ,CREATE_BY
                ,UPDATE_DATE
                ,UPDATE_BY
              ) 
              values 
              (
                '$prNo'
                ,'$buCd'
                ,'$countApprover'
                ,'$specialApprover'
                ,convert(VARCHAR(24), GETDATE(), 120)
                ,'$userId'
                ,convert(VARCHAR(24), GETDATE(), 120)
                ,'$userId'
              )";
    $sql = $conn->query($query);
}
/************************
 ** Create in EPS_T_PR_ATTACHMENT
 ************************/
function createPrAttachment($prNo,$prAttachmentData,$userId){
    include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";
    $prAttachmentData = json_decode(stripcslashes($prAttachmentData));
    for($y = 0; $y < count($prAttachmentData); $y++){
        $itemCd         = $prAttachmentData[$y][0];
        $itemName       = $prAttachmentData[$y][1];
        $itemName       = str_replace("'", "''", $itemName);
        $itemName       = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $itemName);
        $itemName       = preg_replace('/\s+/', ' ',$itemName);
        $itemNameSplit  = explode("~", $itemName);
        $itemNameTrim   = trim($itemNameSplit[0]);
        $fileName       = $prAttachmentData[$y][3];
        $fileSize       = $prAttachmentData[$y][4];
        $fileType       = $prAttachmentData[$y][5];
        if($itemCd==''){
            $itemCd='99';
        }
        $query = "insert into 
                    EPS_T_PR_ATTACHMENT
                    (
                        PR_NO
                        ,ITEM_CD
                        ,ITEM_NAME
                        ,FILE_NAME
                        ,FILE_TYPE
                        ,FILE_SIZE
                        ,CREATE_DATE
                        ,CREATE_BY
                        ,UPDATE_DATE
                        ,UPDATE_BY
                    ) 
                values 
                    (
                        '$prNo'
                        ,'$itemCd'
                        ,'$itemNameTrim'
                        ,'$fileName'
                        ,'$fileType'
                        ,'$fileSize'
                        ,convert(VARCHAR(24), GETDATE(), 120)
                        ,'$userId'
                        ,convert(VARCHAR(24), GETDATE(), 120)
                        ,'$userId'
                    )";
        $sql = $conn->query($query);
    }
}
function updatePrAttachment($prNo, $prAttachmentData, $dirNameTemp, $dirName){
    include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";
    $i = 0;
    $query = "select 
                FILE_NAME
              from
                EPS_T_PR_ATTACHMENT
              where
                PR_NO = '$prNo'";
    $sql = $conn->query($query);
    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
        $i++;
        $fileNameArray[$i] = $row['FILE_NAME'];
    }
    
    /** 
     * Check existing itemName in EPS_T_PR_DETAIL and EPS_T_PR_ATTACHMENT
     * Define array to save unmatch itemName => $itemNameOldArray
     * If doesn't match, set $count = 0 and itemName save in $itemNameOldArray
     ***/
    $k = 0;
    for($j = 1; $j <= $i; $j++){
        $countExist = 1;                     
        $sql = $conn->query("select count(*) 
                                as COUNT_EXIST 
                             from 
                                EPS_T_PR_DETAIL 
                             where 
                                PR_NO = '$prNo' 
                                and ITEM_NAME = '$fileNameArray[$j]'");
        $row = $sql->fetch(PDO::FETCH_ASSOC);
        $countExist = $row['COUNT_EXIST'];

        if($countExist == 0){
            $k++;
            $itemNameOldArray[$k]=$fileNameArray[$j];
        }
    }
    /** 
     * Delete in EPS_T_PR_ATTACHMENT for not existing itemName
     * Delete file from Temporary folder
     ***/
    for($i = 1; $i <= $k; $i++){
        $query = "select 
                    FILE_NAME 
                  from 
                    EPS_T_PR_ATTACHMENT 
                  where 
                    PR_NO = '$prNo' 
                    and ITEM_NAME = '$itemNameOldArray[$i]'";
        $sql = $conn->query($query);
        while($row = $sql->fetch(PDO::FETCH_ASSOC)){
            $fileName = $row['FILE_NAME'];
            unlink($dirNameTemp.'-temp/'.$fileName);
        }
        $sql = $conn->query("delete 
                             from 
                                EPS_T_PR_ATTACHMENT 
                             where 
                                PR_NO = '$prNo' 
                                and ITEM_NAME = '$itemNameOldArray[$i]'");
    }
        
    /** 
     * Check empty/not if Temporary folder exists.
     * If Empty, delete Temporary and Fixed folder
     ***/
    if(is_dir($dirNameTemp.'-temp')){
        $files = scandir($dirNameTemp.'-temp');
        unset($files[array_search(".",$files)]);
        unset($files[array_search("..",$files)]);
        if(count($files) == 0) {
			@unlink($dirNameTemp.'-temp'.'/'.'Thumbs.db');
            rmdir($dirNameTemp.'-temp');
            if(is_dir($dirName)){
                $dh = opendir($dirName);
                while($file = readdir($dh)){
                    if(!is_dir($file)){
                            @unlink($dirName.'/'.$file);
                    }
                }
				@unlink($dirName.'/'.'Thumbs.db');
				closedir($dh);
				rmdir($dirName);
            }
        }else{
            /** 
             * Check if Fixed folder exist/not
             * If exist, remove all file in Fixed folder 
             * If not exist, create Fixed folder
             ***/
            if(is_dir($dirName)){
                $dh = opendir($dirName);
                while($file = readdir($dh)){
                    if(!is_dir($file)){
                        @unlink($dirName.'/'.$file);
                    }
                }
				@unlink($dirName.'/'.'Thumbs.db');
                closedir($dh);
            }else{
                mkdir($dirName);
            }
        }
    }
    if(count($prAttachmentData) > 0){
        $query = "select 
                    FILE_NAME
                  from
                    EPS_T_PR_ATTACHMENT
                  where
                    PR_NO = '$prNo'";
        $sql = $conn->query($query);
        while($row = $sql->fetch(PDO::FETCH_ASSOC)){
            $fileName = $row['FILE_NAME'];
            copy($dirNameTemp.'-temp/'.$fileName,$dirName.'/'.$fileName);
            unlink($dirNameTemp.'-temp/'.$fileName);
        }
    }
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
}
/************************
 ** Action Send PR 
 ************************/
function sendPr($prNo, $company, $prIssuer, $prCharged, $requesterMail, $requesterMailName, $newApprover){
    include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";
    include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Constant.php";
    $nextApprover = $newApprover;
    $prStatus     = "Waiting for Approval";
	
    /*$query = "update 
                EPS_T_PR_HEADER 
              set
                APPROVER = '$nextApprover'
                ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                ,UPDATE_BY = '$requester'
              where
                PR_NO = '$prNo'";
    $sql = $conn->query($query);*/
	$query_select_t_po_approver = "select 
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
                and APPROVAL_STATUS = '".constant('WA')."'
                and NPK = '".$nextApprover."'";
    $sql_select_t_po_approver = $conn->query($query_select_t_po_approver);
    while($row_select_t_po_approver = $sql_select_t_po_approver->fetch(PDO::FETCH_ASSOC)){
        $approvalStatus     = $row_select_t_po_approver['APPROVAL_STATUS_NAME'];
        $approvalRemark     = $row_select_t_po_approver['APPROVAL_REMARK']; 
	
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
					rtrim(ltrim(EPS_M_DSCID.INOPOK)) = ltrim('".$nextApprover."')";
		$sql= $conn->query($query);
		$row = $sql->fetch(PDO::FETCH_ASSOC);
		if($row){
			$mailApprover = $row['INETML'];
			//$mailApprover = 'muh.iqbal@taci.toyota-industries.com';
			$passwordApprover = $row['PASSWORD'];
			$getParamApprover   = paramEncrypt("action=open&prNo=$prNo&userId=$nextApprover&password=$passwordApprover");
			//$mailMessage = 'requires your approval.';
			//sendMail($prNo, $mailApprover, $requesterMail, $requesterMailName, $getParamApprover, $mailMessage);
			$mailSubject        = "[EPS] WAITING APPROVAL. PR No: ".$prNo;
            $mailMessage        = "<table style='font-family: Arial; font-size: 12px;'>";
            $mailMessage        .= "<tr><td>PR No</td><td>: </td><td>".$prNo."</td></tr>";
            $mailMessage        .= "<tr><td>PR Status</td><td>:</td><td>".$prStatus."</td></tr>";
            $mailMessage        .= "<tr><td>Approval Status</td><td>:</td><td>".$approvalStatus."</td></tr>";
            $mailMessage        .= "<tr><td>Remark</td><td>:</td><td>".$approvalRemark."</td></tr>";
            $mailMessage        .= "</table></font>";
           // prSendMail($prNo, $mailApprover, $requesterMail, $requesterMailName, $getParamApprover, $mailSubject, $mailMessage);
            
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
)";
		$sql= $conn->query($query_send_mail);
		$row = $sql->fetch(PDO::FETCH_ASSOC);
		}
	}
     /** Send mail to bypass approver */
    $query_select_t_pr_approver_bp = "select 
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
                                        and APPROVAL_STATUS = '".constant('BP')."'";
    $sql_select_t_pr_approver_bp= $conn->query($query_select_t_pr_approver_bp);
    while($row_select_t_pr_approver_bp = $sql_select_t_pr_approver_bp->fetch(PDO::FETCH_ASSOC)){
        $npkByPassApprover  = $row_select_t_pr_approver_bp['NPK'];
        $approvalStatus     = $row_select_t_pr_approver_bp['APPROVAL_STATUS_NAME'];
        $approvalRemark     = $row_select_t_pr_approver_bp['APPROVAL_REMARK'];
		
        $query_select_m_dscid_bp = "select 
                                        EPS_M_DSCID.INETML
                                        ,EPS_M_USER.PASSWORD 
                                    from 
                                        EPS_M_DSCID 
                                    inner join 
                                        EPS_M_USER 
                                    on 
                                        ltrim(EPS_M_DSCID.INOPOK) = ltrim(EPS_M_USER.USERID) 
                                    where  
                                        rtrim(ltrim(EPS_M_DSCID.INOPOK)) = ltrim('".$npkByPassApprover."')";
        $sql_select_m_dscid_bp = $conn->query($query_select_m_dscid_bp);
        $row_select_m_dscid_bp = $sql_select_m_dscid_bp->fetch(PDO::FETCH_ASSOC);
        if($row_select_m_dscid_bp){
            $mailByPassApprover = $row_select_m_dscid_bp['INETML'];
            //$mailByPassApprover = 'BYAN_PURBA@denso.co.id';
            $passwordByPassApprover = $row_select_m_dscid_bp['PASSWORD'];
            $getParamByPassApprover = paramEncrypt("action=open&prNo=$prNo&userId=$npkByPassApprover&password=$passwordByPassApprover&prCharged=$prCharged");
            //$mailMessage = 'bypass your approval.';
            //sendMail($prNo, $mailByPassApprover, $requesterMail, $requesterMailName, $getParamByPassApprover, $mailMessage);
			$mailSubject        = "[EPS] BYPASS APPROVAL. PR No: ".$prNo;
            $mailMessage        = "<table style='font-family: Arial; font-size: 12px;'>";
            $mailMessage        .= "<tr><td>PR No</td><td>: </td><td>".$prNo."</td></tr>";
            $mailMessage        .= "<tr><td>PR Status</td><td>:</td><td>".$prStatus."</td></tr>";
            $mailMessage        .= "<tr><td>Approval Status</td><td>:</td><td>".$approvalStatus."</td></tr>";
            $mailMessage        .= "<tr><td>Remark</td><td>:</td><td>".$approvalRemark."</td></tr>";
            $mailMessage        .= "</table></font>";
            prSendMail($prNo, $mailByPassApprover, $requesterMail, $requesterMailName, $getParamByPassApprover, $mailSubject, $mailMessage);
        }
    }
    /** If diffrent Bu Charged & Issuer */
    if($prIssuer != $prCharged){
        $query = "select max(APPROVER_NO) 
                    as MAX_APPROVER
                  from
                    EPS_M_PR_APPROVER
                  where 
                    BU_CD = '$prCharged'";
        $sql4 = $conn->query($query);
        $row4 = $sql4->fetch(PDO::FETCH_ASSOC);
        $maxApprover = $row4['MAX_APPROVER'];
        if($maxApprover == '' || $maxApprover == 1)
        {
            $maxApprover = 2;
        }
        $query = "select 
                    NPK
                  from
                    EPS_M_PR_APPROVER
                  where
                    BU_CD = '$prCharged'
                  and
                    APPROVER_NO < $maxApprover";
        $sql2 = $conn->query($query);
        while($row2 = $sql2->fetch(PDO::FETCH_ASSOC)){
            $npkCharged = $row2['NPK'];
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
                        rtrim(ltrim(EPS_M_DSCID.INOPOK)) = ltrim('".$npkCharged."')";
            $sql3 = $conn->query($query);
            $row3 = $sql3->fetch(PDO::FETCH_ASSOC);
            if($row3){
                $mailChargedApprover   = $row3['INETML'];
                //$mailChargedApprover     = 'muh.iqbal@taci.toyota-industries.com';
                $passwordChargedApprover = $row3['PASSWORD'];
                $getParamChargedApprover = paramEncrypt("action=open&prNo=$prNo&userId=$npkCharged&password=$passwordChargedApprover&prCharged=$prCharged");
                //$mailMessage = 'using budget from BU '.trim($prCharged).' (Charged BU: '.trim($prCharged).').';
				//$mailMessage =  '- your budget is using by BU '.trim($prIssuer);
                //sendMail($prNo, $mailChargedApprover, $requesterMail, $requesterMailName, $getParamChargedApprover, $mailMessage);
				$mailSubject        = "[EPS] USING YOUR BUDGET. PR No: ".$prNo;
                $mailMessage        = "<table style='font-family: Arial; font-size: 12px;'>";
                $mailMessage        .= "<tr><td>PR No</td><td>: </td><td>".$prNo."</td></tr>";
                $mailMessage        .= "<tr><td>PR Status</td><td>:</td><td>".$prStatus."</td></tr>";
                $mailMessage        .= "<tr><td>Issuer BU</td><td>: </td><td>".$prIssuer."</td></tr>";
                $mailMessage        .= "<tr><td>Charged BU</td><td>:</td><td>".$prCharged."</td></tr>";
                $mailMessage        .= "</table></font>";
                prSendMail($prNo, $mailChargedApprover, $requesterMail, $requesterMailName, $getParamChargedApprover, $mailSubject, $mailMessage);
            }
        }
    }
    
     /** If diffrent Bu Charged & Issuer (PE Only)*/
//    if($prCharged = 'T6214'){
//        $query = "select max(APPROVER_NO) 
//                    as MAX_APPROVER
//                  from
//                    EPS_M_PR_APPROVER
//                  where 
//                    BU_CD = '$prCharged'";
//        $sql4 = $conn->query($query);
//        $row4 = $sql4->fetch(PDO::FETCH_ASSOC);
//        $maxApprover = $row4['MAX_APPROVER'];
//        if($maxApprover == '' || $maxApprover == 1)
//        {
//            $maxApprover = 2;
//        }
//        $query = "select 
//                    NPK
//                  from
//                    EPS_M_PR_APPROVER
//                  where
//                    BU_CD = '$prCharged'
//                  and
//                    APPROVER_NO < $maxApprover";
//        $sql2 = $conn->query($query);
//        while($row2 = $sql2->fetch(PDO::FETCH_ASSOC)){
//            $npkCharged = $row2['NPK'];
//            $query = "select 
//                        EPS_M_DSCID.INETML
//                        ,EPS_M_USER.PASSWORD 
//                      from 
//                        EPS_M_DSCID 
//                      inner join 
//                        EPS_M_USER 
//                      on 
//                        ltrim(EPS_M_DSCID.INOPOK) = ltrim(EPS_M_USER.USERID) 
//                      where  
//                        rtrim(ltrim(EPS_M_DSCID.INOPOK)) = ltrim('2111264')";
//            $sql3 = $conn->query($query);
//            $row3 = $sql3->fetch(PDO::FETCH_ASSOC);
//            if($row3){
//                //$mailChargedApprover   = $row3['INETML'];
//                $mailChargedApprover     = 'muh.iqbal@taci.toyota-industries.com';
//                $passwordChargedApprover = $row3['PASSWORD'];
//                $getParamChargedApprover = paramEncrypt("action=open&prNo=$prNo&userId=$npkCharged&password=$passwordChargedApprover&prCharged=$prCharged");
//                //$mailMessage = 'using budget from BU '.trim($prCharged).' (Charged BU: '.trim($prCharged).').';
//				//$mailMessage =  '- your budget is using by BU '.trim($prIssuer);
//                //sendMail($prNo, $mailChargedApprover, $requesterMail, $requesterMailName, $getParamChargedApprover, $mailMessage);
//				$mailSubject        = "[EPS] USING YOUR BUDGET. PR No: ".$prNo;
//                $mailMessage        = "<table style='font-family: Arial; font-size: 12px;'>";
//                $mailMessage        .= "<tr><td>PR No</td><td>: </td><td>".$prNo."</td></tr>";
//                $mailMessage        .= "<tr><td>PR Status</td><td>:</td><td>".$prStatus."</td></tr>";
//                $mailMessage        .= "<tr><td>Issuer BU</td><td>: </td><td>".$prIssuer."</td></tr>";
//                $mailMessage        .= "<tr><td>Charged BU</td><td>:</td><td>".$prCharged."</td></tr>";
//                $mailMessage        .= "</table></font>";
//                prSendMail($prNo, $mailChargedApprover, $requesterMail, $requesterMailName, $getParamChargedApprover, $mailSubject, $mailMessage);
//            }
//        }
//    }
    /** If diffrent Bu Charged & Issuer and Company Code = H 
    if($company != 'H'){
        if($prIssuer != $prCharged){
            $companyCdIssuer = substr($prIssuer, 0, 1);
            $companyCdCharged = substr($prCharged, 0, 1);

            if($companyCdIssuer=='H' || $companyCdCharged=='H'){
                
                $query = "select 
                            EPS_M_PR_SPECIAL_APPROVER.NPK
                          from 
                            EPS_M_PR_SPECIAL_APPROVER
                          where
                            EPS_M_PR_SPECIAL_APPROVER.SPECIAL_APPROVER_CD = '002'";
                $sql= $conn->query($query);
                $row = $sql->fetch(PDO::FETCH_ASSOC);
                $npkApproverHdi = $row['NPK'];
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
                            rtrim(ltrim(EPS_M_DSCID.INOPOK)) = ltrim('".$npkApproverHdi."')";
                $sql= $conn->query($query);
                $row = $sql->fetch(PDO::FETCH_ASSOC);
                if($row){
                    $mailHdi = $row['INETML'];
                    //$mailHdi = 'BYAN_PURBA@denso.co.id';
                    $passwordHdi = $row['PASSWORD'];
                    $getParamHdi        = paramEncrypt("action=open&prNo=$prNo&userId=$npkApproverHdi&password=$passwordHdi");
                    $mailMessage = 'using HDI budget.';
                    sendMail($prNo, $mailHdi, $requesterMail, $requesterMailName, $getParamHdi, $mailMessage);
                }
            }
        }
    }*/
}
?>