<?php session_start(); 
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb_ERFI.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Constant.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Common.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Email/PR_EMAIL.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Email/PO_EMAIL.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PR/EPS_T_PR_SEQUENCE.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/CONTROLLER/PR_MAIL.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/lib/mail_lib/crypt.php";
require $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/')).'/lib/mail_lib/class.phpmailer.php';
require $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/')).'/lib/mail_lib/class.smtp.php';
if(isset($_SESSION['sUserId']) && isset($_SESSION['sNPK']) )
{  
    $sUserId    = $_SESSION['sUserId'];
    $sNPK      = $_SESSION['sNPK'];
   
    if(trim($sUserId) != '' && trim($sNPK) != '')
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
        $actionForm     = strtoupper(trim($_GET['actionFormPrm']));
        $actionBtn      = strtoupper(trim($_GET['actionBtnPrm']));
        
        if($actionForm == "EDIT" || $actionForm == "CREATE")
        {
            $userId         = $_GET['userIdHiddenPrm'];
            $npkHidden      = $_GET['npkHiddenPrm'];
            $requester      = $_GET['npkHiddenPrm'];
            $buCd           = $_GET['buCdHiddenPrm'];
            $sectionCd      = $_GET['sectionCdHiddenPrm'];
            $plant          = $_GET['plantCdHiddenPrm'];
            $company        = $_GET['companyCdHiddenPrm'];
                    
            $prNo           = $_GET['prNoPrm'];
            
            $prDate         = encodeDate($_GET['prDatePrm']);
            $issuerBu       = $_GET['issuerBuPrm'];
            $chargedBu      = $_GET['chargedBuPrm'];
            $niceNet        = $_GET['niceNetPrm'];
            $specialTypeId  = $_GET['specialTypeIdPrm'];
            $purpose        = strtoupper($_GET['purposePrm']);
            $purpose        = str_replace("'", "''", $purpose);
            $purpose        = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $purpose);
            $purpose        = preg_replace('/\s+/', ' ',$purpose);
            
            $approverIT             = $_GET['approverITPrm'];
            $approverDeptArr        = $_GET['approverDeptArrPrm'];
            $newApproverDeptArr     = explode(",", $approverDeptArr);
            
            $currentPrItem          = $_SESSION['prItem'];
            $currentPrAttachment    = $_SESSION['prAttachment'];
            
            $currentMonth               = date(Ymd);
            $uploadDir                  = $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/ATTACHMENT/";
            $uploadDirTemp              = $uploadDir."TEMPORARY/";
            $uploadDirFix               = $uploadDir."FIXED/";
            
            $validateDeliveryDate   = 0;
            $getCurrentDate         = date('d/m/Y');
			
			/**
            * Check existing PR number
            **/
            $query_select_eps_t_pr_header = "select 
                                                PR_NO 
                                             from 
                                                EPS_T_PR_HEADER 
                                             where PR_NO = '$prNo'";
            $sql_select_eps_t_pr_header = $conn->query($query_select_eps_t_pr_header);
            $row_select_eps_t_pr_header = $sql_select_eps_t_pr_header->fetch(PDO::FETCH_ASSOC);
            $currentPrNo = $row_select_eps_t_pr_header['PR_NO'];
                    
            // Check due date
            foreach (array_values($_SESSION['prItem']) as $x => $value) 
            {
               /**
                * Check due date
                */
                $deliveryDateCheck = $value['deliveryDate'];
                if(strtotime(str_replace('/', '-', $deliveryDateCheck)) < strtotime(str_replace('/', '-', $getCurrentDate)))
                {
                    $validateDeliveryDate++;
                }
            }
					
            if($prDate == "" || $issuerBu == "" || $chargedBu == "" || $niceNet == ""
                    || $specialTypeId == "" || $purpose == "")
            {
                $msg = "Mandatory_1";
            }
            else if($specialTypeId == "IT" && $approverIT == "" )
            {
                $msg = "Mandatory_5";
            }
            else if(count($currentPrItem) == 0)
            {
                $msg = "Mandatory_6";
            }
            else if($currentPrNo && $actionForm == "CREATE")
            {
                $msg = "Mandatory_7";
            }
            else if($validateDeliveryDate > 0)
            {
                $msg = "Mandatory_8";
            }
            else
            {
                if($actionForm == "CREATE")
                {
                    //$newPrNo    = getPrNo($userId, trim($sBuLogin), "newPrNo");
                    //$prNo       = $newPrNo;
					
                    /************************
                     ** Update in EPS_T_SEQUENCES
                     ************************/
                    getPrNo($userId, trim($sBuLogin), 'updatePrNo');
                }
                
                if($actionBtn == "SAVE")
                {
                    $prStatus = "1010";
                }
                if($actionBtn == "SEND")
                {
                    $prStatus = "1020";
                } 
                
               /***************************************************************
                * INSERT EPS_T_HEADER
                ***************************************************************/ 
                if($actionForm == "CREATE")
                {
                   $query_insert_eps_t_pr_header = "insert into
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
                                                        ,'$niceNet'
                                                        ,'$issuerBu'
                                                        ,'$chargedBu'
                                                        ,'$specialTypeId'
                                                        ,'$purpose'
                                                        ,'$userId'
                                                        ,convert(VARCHAR(24), GETDATE(), 120)
                                                        ,'$userId'
                                                        ,convert(VARCHAR(24), GETDATE(), 120)
                                                        ,'$userId'
                                                    )";
                    $conn->query($query_insert_eps_t_pr_header); 
                }
                
               /***************************************************************
                * UPDATE EPS_T_HEADER
                ***************************************************************/ 
                if($actionForm == "EDIT")
                {
                    $query_update_eps_t_pr_header = "update
                                                        EPS_T_PR_HEADER
                                                     set
                                                        PR_STATUS       = '$prStatus'
                                                        ,ISSUED_DATE    = '$prDate'
                                                        ,REQUESTER      = '$npkHidden'
                                                        ,BU_CD          = '$sBunit'
                                                        ,SECTION_CD     = '$sSeksi'
                                                        ,PLANT_CD       = '$sKdPlant'
                                                        ,COMPANY_CD     = '$sKdper'
                                                        ,EXT_NO         = '$niceNet'
                                                        ,REQ_BU_CD      = '$issuerBu'
                                                        ,CHARGED_BU_CD  = '$chargedBu'
                                                        ,SPECIAL_TYPE_ID= '$specialTypeId'
                                                        ,PURPOSE        = '$purpose'
                                                        ,UPDATE_DATE    = convert(VARCHAR(24), GETDATE(), 120)
                                                        ,UPDATE_BY      = '$userId'
                                                     where
                                                        PR_NO = '$prNo'";
                    $conn->query($query_update_eps_t_pr_header); 
                }
                
               /***************************************************************
                * DELETE EPS_T_DETAIL
                ***************************************************************/ 
                if($actionForm == "EDIT")
                {
                    $query_delete_eps_t_pr_detail = "delete
                                                     from
                                                        EPS_T_PR_DETAIL
                                                     where
                                                        PR_NO = '$prNo'";
                    $conn->query($query_delete_eps_t_pr_detail); 
                    
                }
                
               /***************************************************************
                * INSERT EPS_T_DETAIL
                ***************************************************************/ 
                $countItemIt = 0;
                $rfiNoAll = "";
                for($x = 0; $x < count($currentPrItem); $x++)
                {
                        
                    $itemCd         = $currentPrItem[$x]['itemCd'];
                    $itemName       = $currentPrItem[$x]['itemName'];
                    $itemName       = str_replace("'", "''", $itemName);
                    $itemName       = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $itemName);
                    $itemName       = preg_replace('/\s+/', ' ',$itemName);
                    $deliveryDate   = encodeDate($currentPrItem[$x]['deliveryDate']);
                    $qty            = $currentPrItem[$x]['qty'];
                    $itemPrice      = $currentPrItem[$x]['price'];
                    $amount         = $currentPrItem[$x]['amount']; 
                    $currencyCd     = "IDR";
                    $itemType       = $currentPrItem[$x]['itemType'];
                    $accountNo      = $currentPrItem[$x]['expNo'];
                    $rfiNo          = $currentPrItem[$x]['rfiNo'];
                    $faCd           = $currentPrItem[$x]['faCd'];
                    $unitCd         = $currentPrItem[$x]['um'];
                    $supplierCd     = $currentPrItem[$x]['supplierCd'];
                    $supplierName   = $currentPrItem[$x]['supplierName'];
                    $remark         = $currentPrItem[$x]['remark'];
                    $remark         = str_replace("'", "''", $remark);
                    $remark         = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $remark);
                    $remark         = preg_replace('/\s+/', ' ',$remark);
                    $itemStatus     = "1060";
                    
					if($itemType == "2")
                    {
                        if($x==0)
                        {
                            $rfiNoAll = "'".$rfiNo."'";
                        }
                        else
                        {
                            $rfiNoAll = $rfiNoAll.","."'".$rfiNo."'";
                        }
                    }
					
                    if($itemType == "7")
                    {
                        $itemType = "2";
                    }
                    if($itemType == "6")
                    {
                        $itemType = "3";
                    }
                    $query_insert_eps_t_pr_detail = "insert into 
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
                                                            ,FA_CD
                                                            ,UNIT_CD
                                                            ,SUPPLIER_CD
                                                            ,SUPPLIER_NAME
                                                            ,REMARK
                                                            ,ITEM_STATUS
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
                                                            ,'$faCd'
                                                            ,'$unitCd'
                                                            ,'$supplierCd'
                                                            ,'$supplierName'
                                                            ,'$remark'
                                                            ,'$itemStatus'
                                                            ,convert(VARCHAR(24), GETDATE(), 120)
                                                            ,'$userId'
                                                            ,convert(VARCHAR(24), GETDATE(), 120)
                                                            ,'$userId'
                                                    )";
                    $conn->query($query_insert_eps_t_pr_detail);
                    
                    /**
                     * Check for ITEM_GROUP_CD = "KOMPUTER"
                     */
                    if($itemCd != "99")
                    {
                        $query_select_eps_m_item = "select 
                                                        ITEM_GROUP_CD 
                                                    from 
                                                        EPS_M_ITEM 
                                                    where 
                                                        ITEM_NAME='$itemName'";
                        $sql_select_eps_m_item = $conn->query($query_select_eps_m_item);
                        $row_select_eps_m_item = $sql_select_eps_m_item->fetch(PDO::FETCH_ASSOC);
                        $itemGroupCd = $row_select_eps_m_item['ITEM_GROUP_CD'];
                        if($itemGroupCd == 'KOMPUTER')
                        {
                            $countItemIt++;
                        }
                    }
                    
                }
               /***************************************************************
                * DELETE EPS_T_APPROVER
                ***************************************************************/ 
                if($actionForm == "EDIT")
                {
                    $query_delete_eps_t_pr_approver = "delete
                                                     from
                                                        EPS_T_PR_APPROVER
                                                     where
                                                        PR_NO = '$prNo'";
                    $conn->query($query_delete_eps_t_pr_approver); 
                    
                }
                /***************************************************************
                * INSERT EPS_T_APPROVER
                ***************************************************************/ 
                $buLogin    = $issuerBu;
                $flagAppNo  = 0;
                $flagApp    = "";
                date_default_timezone_set('Asia/Jakarta');
               /****************
                * Dept Approval
                ****************/
                for($y = 0; $y < count($newApproverDeptArr); $y++)
                {
                    $approverNo         = substr($newApproverDeptArr[$y],0,1);
                    $approver           = substr($newApproverDeptArr[$y],1,7);
                    $approverRemark     = strtoupper(substr($newApproverDeptArr[$y],8));
                    $approverRemark     = str_replace("'", "''", $approverRemark);
                    $approverRemark     = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $approverRemark);
                    $approverRemark     = preg_replace('/\s+/', ' ',$approverRemark);
                    $approvalStatus     = "";
                    $dateOfByPass       = "";
                    
                    if($approverRemark != "")
                    {
                        $approvalStatus = "BP";
                        $dateOfByPass   = date("n/j/Y H:i:s A");
                    }
                    
                    if($flagApp == "" && $approvalStatus != "BP"){
                        $flagAppNo = $y;
                        $flagApp = '1';
                    }
                    
                    if($y == 0)
                    {
                        if($approvalStatus != "BP" && $actionBtn == "SEND")
                        {
                            $approvalStatus     = "WA";
                        }
                    }
                    else
                    {
                        if( $flagAppNo == $y && $actionBtn == "SEND")
                        {
                            $approvalStatus     = "WA";
                        } 
                    }
                    
                    $query_insert_eps_t_pr_approver = "insert into 
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
                                                                ,'$approverRemark'
                                                                ,'$dateOfByPass'
                                                                ,convert(VARCHAR(24), GETDATE(), 120)
                                                                ,'$userId'
                                                                ,convert(VARCHAR(24), GETDATE(), 120)
                                                                ,'$userId'
                                                            )";
                    $conn->query($query_insert_eps_t_pr_approver);
                }
               /****************
                * IS Approval
                ****************/
                if(($specialTypeId == "IT" && $countItemIt > 0) || $countItemIt > 0 || $specialTypeId == "IT")
                {
                    $approverNo = $y + 1;
                    if($approverIT == "")
                    {
                        /*$query_select_m_app_special_it = "select 
                                                            EPS_M_PR_SPECIAL_APPROVER.NPK
                                                            ,EPS_M_EMPLOYEE.NAMA1 as APPROVER_NAME
                                                          from 
                                                            EPS_M_PR_SPECIAL_APPROVER
                                                          left join
                                                            EPS_M_EMPLOYEE
                                                          on 
                                                            EPS_M_PR_SPECIAL_APPROVER.NPK = EPS_M_EMPLOYEE.NPK
                                                          where 
                                                            SPECIAL_APPROVER_CD = '001'";*/
															
						$query_select_m_app_special_it = "select 
                                                            EPS_M_PR_SPECIAL_APPROVER.NPK
                                                            ,EPS_M_EMPLOYEE.NAMA1 as APPROVER_NAME
                                                          from 
                                                            EPS_M_PR_SPECIAL_APPROVER
                                                          left join
                                                            EPS_M_EMPLOYEE
                                                          on 
                                                            EPS_M_PR_SPECIAL_APPROVER.NPK = EPS_M_EMPLOYEE.NPK ";
                        if($sKdper == "M")
                        {
                            $query_select_m_app_special_it .= "where 
                                                                SPECIAL_APPROVER_CD = '004'"; 
                        }
                        else
                        {
                            $query_select_m_app_special_it .= "where 
                                                                SPECIAL_APPROVER_CD = '001'";
                        }									
                        $sql_select_m_app_special_it = $conn->query($query_select_m_app_special_it);
                        $row_select_m_app_special_it = $sql_select_m_app_special_it->fetch(PDO::FETCH_ASSOC);
                        $approverIT  = $row_select_m_app_special_it['NPK'];
                    }
                    
                    $query_insert_eps_t_pr_approver_it = "insert into 
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
                                                                ,'$approverIT'
                                                                ,''
                                                                ,''
                                                                ,''
                                                                ,convert(VARCHAR(24), GETDATE(), 120)
                                                                ,'$userId'
                                                                ,convert(VARCHAR(24), GETDATE(), 120)
                                                                ,'$userId'
                                                            )";
                    $conn->query($query_insert_eps_t_pr_approver_it);
                }
                
                /***************************************************************
                * DELETE EPS_T_ATTACHMENT
                ***************************************************************/ 
                if($actionForm == "EDIT")
                {
                    $query_delete_eps_t_pr_attachment = "delete
                                                        from
                                                            EPS_T_PR_ATTACHMENT
                                                        where
                                                            PR_NO = '$prNo'";
                    $conn->query($query_delete_eps_t_pr_attachment); 
                    
                }
                
                /***************************************************
                 * FOLDER CHECKING
                 ***************************************************/
                $dirByPrFix   = $uploadDirFix.$prNo."/";
                
                /*********************
                 * DELETE FOLDER FIXED
                 *********************/
                /** Check existing Fixed folder to Fixed Temp folder **/ 
                if(is_dir($dirByPrFix)){
                    $dh = opendir($dirByPrFix);
                    while($file = readdir($dh)){
                        if(!is_dir($file)){
                            @unlink($dirByPrFix.'/'.$file);
                        }
                    }
                    @unlink($dirByPrFix.'/'.'Thumbs.db');
                    closedir($dh);
                    rmdir($dirByPrFix);   
                }
                /**************************
                * CREATE FIXED FOLDER
                **************************/ 
                // Create directory by PR NO
                if(!is_dir($dirByPrFix))
                {    
                    mkdir($dirByPrFix);
                }
            
                if($actionForm == "CREATE" || $actionForm == "REPLICATE")
                {
                    $dirByDateTemp      = $uploadDirTemp.$currentMonth."/";
                    $dirTemp            = $dirByDateTemp.$sUserId."/";   
                }
                if($actionForm == "EDIT")
                {
                    $dirTemp            = $uploadDirTemp.$prNo."/"; 
                }
                
                /***************************************************************
                * INSERT EPS_T_ATTACHMENT
                ***************************************************************/ 
                for($z = 0; $z < count($currentPrAttachment); $z++)
                {
                    $itemCd     = $currentPrAttachment[$z]['itemCdFile'];
                    $itemName   = $currentPrAttachment[$z]['itemNameFile'];
                    $itemName   = str_replace("'", "''", $itemName);
                    $itemName   = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $itemName);
                    $itemName   = preg_replace('/\s+/', ' ',$itemName);
                    $fileName   = $currentPrAttachment[$z]['fileName'];
                    $fileType   = $currentPrAttachment[$z]['fileType'];
                    $fileSize   = $currentPrAttachment[$z]['fileSize'];
                    $fileTemp   = $currentPrAttachment[$z]['fileTemp'];
                    
                    $query_insert_eps_t_pr_attachment = "insert into 
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
                                                                ,'$itemName'
                                                                ,'$fileName'
                                                                ,'$fileType'
                                                                ,'$fileSize'
                                                                ,convert(VARCHAR(24), GETDATE(), 120)
                                                                ,'$userId'
                                                                ,convert(VARCHAR(24), GETDATE(), 120)
                                                                ,'$userId'
                                                            )";             
                    $conn->query($query_insert_eps_t_pr_attachment);
                    
                    /**************
                     * COPY FILE 
                     **************/
                    $fileNew    = $dirByPrFix.$fileName;
                    $fileTemp   = $dirTemp.$fileName;
                    copy($fileTemp,$fileNew);
                    unlink($fileTemp);
                
                }
                
                /*********************
                 * DELETE FOLDER TEMP
                 *********************/
                /** Check existing Temp folder to delete Temp folder **/
                if(is_dir($dirTemp)){
                    $dh = opendir($dirTemp);
                    while($file = readdir($dh)){
                        if(!is_dir($file)){
                            @unlink($dirTemp.'/'.$file);
                        }
                    }
                    @unlink($dirTemp.'/'.'Thumbs.db');
                    closedir($dh);
                    if($actionForm == "EDIT")
                    {
                        rmdir($dirTemp); 
                    }  
                } 
               
                $msg = "Success-Save";
                
                if($actionBtn == "SEND")
                {
                    $query_select_eps_t_pr_approver = "select
                                                            NPK
                                                            ,APPROVER_NO
                                                        from
                                                            EPS_T_PR_APPROVER
                                                        where 
                                                            PR_NO  = '$prNo'
                                                            and APPROVAL_STATUS = 'WA'";
                    $sql_select_eps_t_pr_approver = $conn->query($query_select_eps_t_pr_approver);
                    $row_select_eps_t_pr_approver = $sql_select_eps_t_pr_approver->fetch(PDO::FETCH_ASSOC);
                    $currentNpkApprover = $row_select_eps_t_pr_approver['NPK'];
                    
                    $query_update_eps_t_pr_header = "update
                                                        EPS_T_PR_HEADER
                                                    set
                                                        APPROVER = '$currentNpkApprover'
                                                       ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                       ,UPDATE_BY = '$userId'
                                                    where
                                                        PR_NO  = '$prNo'";
                    $conn->query($query_update_eps_t_pr_header);
                    
                    if($countItemIt > 0 && $specialTypeId == "NIT")
                    {
                        $query_update_eps_t_pr_header_by_specialTypeId = "update
                                                                            EPS_T_PR_HEADER
                                                                          set
                                                                            SPECIAL_TYPE_ID = 'IT'
                                                                            ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                                            ,UPDATE_BY = '$userId'
                                                                          where
                                                                            PR_NO  = '$prNo'";
                        $conn->query($query_update_eps_t_pr_header_by_specialTypeId);
                    }
                   /**********************************************************************
                    * SEND MAIL
                    **********************************************************************/
                    $mailFrom       = $sInet;
                    $mailFromName   = $sNotes;  
                    $remark         = "";
                    
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
                                                    ltrim(EPS_M_DSCID.INOPOK) = ltrim('".$currentNpkApprover."')";
                    $sql_select_eps_m_dscid = $conn->query($query_select_eps_m_dscid);
                    $row_select_eps_m_dscid = $sql_select_eps_m_dscid->fetch(PDO::FETCH_ASSOC);
                    if($row_select_eps_m_dscid){
                        $mailTo           = $row_select_eps_m_dscid['INETML'];
                        //$mailTo             = 'BYAN_PURBA@denso.co.id';
                        $passwordApprover   = $row_select_eps_m_dscid['PASSWORD'];
                        $getParamLink  = paramEncrypt("action=open&prNo=$prNo&userId=$currentNpkApprover&password=$passwordApprover");
                        
                        $mailSubject    = MailSubjectPR("WAITING APPROVAL",$prNo);
                        $mailMessage    = MailMessagePR($prNo, $sNama, $getParamLink, $remark, "WAITING APPROVAL");
                        SendMailPR ($mailTo, $mailFrom, $mailFromName, $mailSubject, $mailMessage);
                    }
                    
					/*
                     * TO BYPASS APPROVER
                     */
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
                                                    left join
                                                        EPS_M_EMPLOYEE
                                                    on
                                                        EPS_T_PR_APPROVER.NPK = EPS_M_EMPLOYEE.NPK
                                                    where
                                                        PR_NO = '$prNo'
                                                        and APPROVAL_STATUS = 'BP'";
                    $sql_select_t_pr_approver_bp = $conn->query($query_select_t_pr_approver_bp);
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
                            $mailByPassApprover       = $row_select_m_dscid_bp['INETML'];
                            //$mailByPassApprover         = 'BYAN_PURBA@denso.co.id';
                            $passwordByPassApprover     = $row_select_m_dscid_bp['PASSWORD'];
                            $getParamLinkByPassApprover = paramEncrypt("action=open&prNo=$prNo&userId=$npkByPassApprover&password=$passwordByPassApprover&prCharged=$prCharged");
                            
                            $mailSubjectByPassApprover  = MailSubjectPR("BYPASS APPROVAL",$prNo);
                            $mailMessageByPassApprover  = MailMessagePR($prNo, $sNama, $getParamLinkByPassApprover, $approvalRemark, "BYPASS APPROVAL");
                            SendMailPR ($mailByPassApprover, $mailFrom, $mailFromName, $mailSubjectByPassApprover, $mailMessageByPassApprover);
                        
                        }
                    }
                    
                    /*
                     * TO CHARGED BU APPROVER
                     */
                    if($issuerBu != $chargedBu)
                    {
                        $query_select_max_eps_m_pr_approver_bycharged = "select 
                                                                            max(APPROVER_NO) 
                                                                            as MAX_APPROVER
                                                                        from
                                                                            EPS_M_PR_APPROVER
                                                                        where 
                                                                            BU_CD = '$chargedBu'";
                        $sql_select_max_eps_m_pr_approver_bycharged = $conn->query($query_select_max_eps_m_pr_approver_bycharged);
                        $row_select_max_eps_m_pr_approver_bycharged = $sql_select_max_eps_m_pr_approver_bycharged->fetch(PDO::FETCH_ASSOC);
                        $maxApproverChargedBu = $row_select_max_eps_m_pr_approver_bycharged['MAX_APPROVER'];
                        if($maxApproverChargedBu == '' || $maxApproverChargedBu == 1)
                        {
                            $maxApproverChargedBu = 2;
                        }
                        $query_select_eps_m_pr_approver_bychaged = "select 
                                                                        EPS_M_PR_APPROVER.NPK
                                                                    from
                                                                        EPS_M_PR_APPROVER
                                                                    inner join
                                                                        EPS_M_EMPLOYEE
                                                                    on
                                                                        EPS_M_PR_APPROVER.NPK = EPS_M_EMPLOYEE.NPK
                                                                    where
                                                                        BU_CD = '$chargedBu'
                                                                        and APPROVER_NO < $maxApproverChargedBu
                                                                        and AKTIF = 'A'";
                        $sql_select_eps_m_pr_approver_bychaged = $conn->query($query_select_eps_m_pr_approver_bychaged);
                        while($row_select_eps_m_pr_approver_bychaged = $sql_select_eps_m_pr_approver_bychaged->fetch(PDO::FETCH_ASSOC))
                        {
                            $npkCharged = $row_select_eps_m_pr_approver_bychaged['NPK'];
                            $query_select_m_dscid_charged = "select 
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
                            $sql_select_m_dscid_charged = $conn->query($query_select_m_dscid_charged);
                            $row_select_m_dscid_charged = $sql_select_m_dscid_charged->fetch(PDO::FETCH_ASSOC);
                            if($row_select_m_dscid_charged){
                                $mailChargedApprover      = $row_select_m_dscid_charged['INETML'];
                                //$mailChargedApprover        = 'BYAN_PURBA@denso.co.id';
                                $passwordChargedApprover    = $row_select_m_dscid_charged['PASSWORD'];
                                $getParamLinkChargedApprover= paramEncrypt("action=open&prNo=$prNo&userId=$npkCharged&password=$passwordChargedApprover&prCharged=$chargedBu");
                            
                                $mailSubjectChargedApprover  = MailSubjectPR("CHARGED BU APPROVAL",$prNo);
                                $mailMessageChargedApprover  = MailMessagePR($prNo, $sNama, $getParamLinkChargedApprover, $approvalRemark, "CHARGED BU APPROVAL");
                                SendMailPR ($mailChargedApprover, $mailFrom, $mailFromName, $mailSubjectChargedApprover, $mailMessageChargedApprover);

                            }
                        }
                    }
					
					/*
                     * TO REQUESTER RFI
                     */
                    if($rfiNoAll && substr($chargedBu, 0,1) != 'H')
                    {
                        $query_select_erfi_t_header = "select
                                                            REQUESTER
                                                       from 
                                                            ERFI_T_HEADER 
                                                       inner join 
                                                            ERFI_T_REQUESTIONER
                                                       on 
                                                            ERFI_T_HEADER.RFI_ID = ERFI_T_REQUESTIONER.RFI_ID 
                                                       inner join 
                                                            ERFI_M_EMPLOYEE 
                                                       on 
                                                            ERFI_T_REQUESTIONER.REQUESTER = ERFI_M_EMPLOYEE.NPK
                                                       where 
                                                            RFI_NO in ($rfiNoAll)
                                                       group by
                                                            REQUESTER ";
                        $sql_select_erfi_t_header = $conn_erfi->query($query_select_erfi_t_header);
                        while($row_select_erfi_t_header = $sql_select_erfi_t_header->fetch(PDO::FETCH_ASSOC))
                        {
                            $requester = $row_select_erfi_t_header['REQUESTER'];
                            
                            $query_select_m_dscid_rfi = "select 
                                                                EPS_M_DSCID.INETML
                                                                ,EPS_M_USER.PASSWORD 
                                                            from 
                                                                EPS_M_DSCID 
                                                            inner join 
                                                                EPS_M_USER 
                                                            on 
                                                                ltrim(EPS_M_DSCID.INOPOK) = ltrim(EPS_M_USER.USERID) 
                                                            where  
                                                                rtrim(ltrim(EPS_M_DSCID.INOPOK)) = ltrim('".$requester."')";
                            $sql_select_m_dscid_rfi = $conn->query($query_select_m_dscid_rfi);
                            $row_select_m_dscid_rfi = $sql_select_m_dscid_rfi->fetch(PDO::FETCH_ASSOC);
                            if($row_select_m_dscid_rfi){
                                $mailRequesterRfi         = $row_select_m_dscid_rfi['INETML'];
                                //$mailRequesterRfi           = 'BYAN_PURBA@denso.co.id';
                                $passwordRequesterRfi       = $row_select_m_dscid_rfi['PASSWORD'];
                                $getParamLinkRequesterRfi   = paramEncrypt("action=open&prNo=$prNo&userId=$requester&password=$passwordRequesterRfi");
                            
                                $mailSubjectRequesterRfi    = MailSubjectPR("RFI APPROVAL",$prNo);
                                $mailMessageRequesterRfi    = MailMessagePR($prNo, $sNama, $getParamLinkRequesterRfi, $approvalRemark, "RFI APPROVAL");
                                SendMailPR ($mailRequesterRfi, $mailFrom, $mailFromName, $mailSubjectRequesterRfi, $mailMessageRequesterRfi);

                            }
                        }
                    }   
					
                    $msg = "Success-Send";
                }
            }
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
echo $msg;
?>
