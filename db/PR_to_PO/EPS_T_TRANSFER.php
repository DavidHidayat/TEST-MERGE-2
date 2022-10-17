<?php session_start(); 
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Constant.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Common.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Email/PR_EMAIL.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/lib/mail_lib/crypt.php";
require $_SERVER['DOCUMENT_ROOT'].'/EPS/lib/mail_lib/class.phpmailer.php';
require $_SERVER['DOCUMENT_ROOT'].'/EPS/lib/mail_lib/class.smtp.php';
if(isset($_SESSION['sUserId']))
{      
    $sUserId    = $_SESSION['sUserId'];
    
    if($sUserId != '')
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
        
        if($action == 'AcceptPr')
		{
			ini_set('memory_limit','128M');
			
            $prNo           = strtoupper(trim($_GET['prNoPrm']));
            $requester      = strtoupper($_GET['requesterPrm']);
            $prCharged      = strtoupper($_GET['prChargedPrm']);
            $newPrCharged   = strtoupper($_GET['newPrChargedPrm']);
            $remarkProc     = strtoupper(trim($_GET['remarkProcPrm']));
            $remarkProc     = str_replace("'", "''", $remarkProc);
            $procPrUpdateDate= strtoupper($_GET['procPrUpdateDatePrm']);
            $itemStatusArray= strtoupper(trim($_GET['itemStatusArrayPrm']));
            $prItemData     = ($_SESSION['prDetail']);
            $currentDate    = date(Ymd);
            
            $countWait      = 0;
            $countReject    = 0;
            $countInvalidDate   = 0;
            $msg            = '';
            
			$query_eps_t_pr_header = "select
                                        UPDATE_DATE
                                      from 
                                        EPS_T_PR_HEADER
                                      where
                                        PR_NO = '$prNo'";
            $sql_eps_t_pr_header = $conn->query($query_eps_t_pr_header);
            $row_eps_t_pr_header=$sql_eps_t_pr_header->fetch(PDO::FETCH_ASSOC);
            $newUpdateDate = strtoupper($row_eps_t_pr_header['UPDATE_DATE']);
			
            if(count($prItemData) > 0)
            {
				if($newUpdateDate == $procPrUpdateDate)
                {
					$getCurrentDate = date('d/m/Y');
                    $newItemStatusArray =  explode("::", $itemStatusArray);  
                    for($i = 0; $i < count($newItemStatusArray); $i++){
                        $itemStatusPr   = substr($newItemStatusArray[$i],0,4);
                        $deliveryDatePr = substr($newItemStatusArray[$i],4,10);
                        $itemNamePr     = substr($newItemStatusArray[$i],14);

                        if($itemStatusPr == '1110'){
                            $countWait++;
                        }
                        if($itemStatusPr == '1140'){
                            $countReject++;
                        }
                        if(strtotime(str_replace('/', '-', $deliveryDatePr)) < strtotime(str_replace('/', '-', $getCurrentDate)))
                        {
							$countInvalidDate++;
                        }
                    }
					
					if(($countWait == 0 && $countReject == 0 && $countInvalidDate == 0)
                            || ($countWait == 0 && $countReject !=0 && $remarkProc != '' && $countInvalidDate == 0))
                    {
                        unset($_SESSION['prStatus']);
                        unset($_SESSION['procPrUpdateDate']);
                        
						$mailSubject            = '';
                        $mailMessage            = '';
                        $flagSubjectItemReject  = '';
                        $flagSubjectChangeDate  = '';
                        $itemRejectArray        = array();
						
                        for($j = 0; $j < count($newItemStatusArray); $j++){
                            $itemStatusSet   = substr($newItemStatusArray[$j],0,4);
                            $deliveryDateSet = substr($newItemStatusArray[$j],4,10);
                            $itemNameSet     = substr($newItemStatusArray[$j],14);
                            $itemNameSet     = stripslashes($itemNameSet);
                            $itemNameSet     = str_replace("'", "''", $itemNameSet);
                            $itemNameSet     = str_replace(array("\n", "\r"), '', $itemNameSet);
                            
                            /**
                             * SELECT EPS_T_PR_DETAIL
                             */
                            $query_select_t_pr_detail = "select
                                                            ITEM_CD
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
                                                         from
                                                            EPS_T_PR_DETAIL
                                                         where
                                                            PR_NO = '$prNo'
                                                            and (replace(replace(replace(ITEM_NAME, CHAR(13), ''), CHAR(9), ''), ' ', '') = replace('$itemNameSet', ' ', ''))";
                            $sql_select_t_pr_detail = $conn->query($query_select_t_pr_detail);
                            $row_select_t_pr_detail = $sql_select_t_pr_detail->fetch(PDO::FETCH_ASSOC);
                            
                            $newItemCd         = $row_select_t_pr_detail['ITEM_CD'];
                            $newItemName       = $row_select_t_pr_detail['ITEM_NAME'];
							$newItemName       = str_replace("'", "''", $newItemName);
							$newItemName       = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $newItemName);
                            $newRemark         = $row_select_t_pr_detail['REMARK'];
                            $newRemark         = str_replace("'", "''", $newRemark);
                            $newRemark         = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $newRemark);
                            $newDeliveryDate   = encodeDate($deliveryDateSet);
                            $deliveryDate      = $row_select_t_pr_detail['DELIVERY_DATE'];
                            $newItemType       = $row_select_t_pr_detail['ITEM_TYPE_CD'];
                            $newRfiNo          = $row_select_t_pr_detail['RFI_NO'];
                            $newAccountNo      = substr($row_select_t_pr_detail['ACCOUNT_NO'],0,2);
                            $newSupplierCd     = $row_select_t_pr_detail['SUPPLIER_CD'];
                            $newSupplierName   = $row_select_t_pr_detail['SUPPLIER_NAME'];
                            $newSupplierName   = str_replace("'", "''", $newSupplierName);
                            $newSupplierName   = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $newSupplierName);
                            $newUnitCd         = $row_select_t_pr_detail['UNIT_CD'];
                            $newQty            = $row_select_t_pr_detail['QTY'];
                            $newItemPrice      = $row_select_t_pr_detail['ITEM_PRICE'];
                            $newAmount         = $row_select_t_pr_detail['AMOUNT'];
                            $newCurrencyCd     = $row_select_t_pr_detail['CURRENCY_CD'];
                            $newItemStatus     = $itemStatusSet;
                                
                            // Define Transfer Id
                            $query2 = "select count(*) as TRANSFER_COUNT from EPS_T_TRANSFER where substring(TRANSFER_ID, 1, 8) = '$currentDate'";
                            $sql2 = $conn->query($query2);
                            $row2 = $sql2->fetch(PDO::FETCH_ASSOC);
                            $transferCount = $row2['TRANSFER_COUNT'];

                            if($transferCount == 0){
                                $sequences = '1';
                            }else{
                                $sequences = $transferCount + 1;
                            }
                            //$transferId = $currentDate.'T'.$sequences;
							$transferNo = str_pad($sequences, 5, "0", STR_PAD_LEFT);
                            //$transferId = $currentDate.'T'.$transferNo;
                            $transferId = $currentDate.trim($sUserId).'T'.$transferNo;

                            /**
                             * INSERT to EPS_T_TRANSFER
                             */
                            $query_insert_transfer = 
                                    "insert into 
                                        EPS_T_TRANSFER
                                        (
                                            TRANSFER_ID
                                            ,ITEM_STATUS
                                            ,PR_NO
                                            ,REQUESTER
                                            ,CHARGED_BU
                                            ,NEW_CHARGED_BU
                                            ,NEW_ITEM_CD
                                            ,ITEM_NAME
                                            ,NEW_ITEM_NAME
                                            ,NEW_DELIVERY_DATE
                                            ,NEW_QTY
                                            ,ACTUAL_QTY
                                            ,NEW_ITEM_PRICE
                                            ,NEW_AMOUNT
                                            ,NEW_CURRENCY_CD
                                            ,NEW_ITEM_TYPE_CD
                                            ,NEW_ACCOUNT_NO
                                            ,NEW_RFI_NO
                                            ,NEW_UNIT_CD
                                            ,NEW_SUPPLIER_CD
                                            ,NEW_SUPPLIER_NAME
                                            ,NEW_REMARK
                                            ,CREATE_DATE
                                            ,CREATE_BY
                                            ,UPDATE_DATE
                                            ,UPDATE_BY
                                        ) 
                                    values 
                                        (
                                            '$transferId'
                                            ,'$newItemStatus'
                                            ,'$prNo'
                                            ,'$requester'
                                            ,'$prCharged'
                                            ,'$newPrCharged'
                                            ,'$newItemCd'
                                            ,'$newItemName'
                                            ,'$newItemName'
                                            ,'$newDeliveryDate'
                                            ,'$newQty'
                                            ,'$newQty'
                                            ,'$newItemPrice'
                                            ,'$newAmount'
                                            ,'$newCurrencyCd'
                                            ,'$newItemType'
                                            ,'$newAccountNo'
                                            ,'$newRfiNo'
                                            ,'$newUnitCd'
                                            ,'$newSupplierCd'
                                            ,'$newSupplierName'
                                            ,'$newRemark'
                                            ,convert(VARCHAR(24), GETDATE(), 120)
                                            ,'$sUserId'
                                            ,convert(VARCHAR(24), GETDATE(), 120)
                                            ,'$sUserId'
                                    )";
                            $sql_insert_transfer = $conn->query($query_insert_transfer);
							
							/**
                             * SELECT EPS_M_APP
                             */
                            $query_m_app_status = "select
                                                    APP_STATUS_NAME
                                                   from
                                                    EPS_M_APP_STATUS
                                                   where
                                                    APP_STATUS_CD = '$newItemStatus'";
                            $sql_m_app_status = $conn->query($query_m_app_status);
                            $row_m_app_status = $sql_m_app_status->fetch(PDO::FETCH_ASSOC);
                            $itemStatusName   = $row_m_app_status['APP_STATUS_NAME'];
                            
                            if($newItemStatus == '1140'
                                || strtotime(str_replace('/', '-', $newDeliveryDate)) > strtotime(str_replace('/', '-', $deliveryDate)))
                            {
                                $whereTransfer = array();
								if($prNo)
                                {
                                    $whereTransfer[] = "EPS_T_PR_DETAIL.PR_NO = '$prNo' ";
                                }
                                $whereTransfer[] = "(REPLACE(REPLACE(REPLACE(EPS_T_PR_DETAIL.ITEM_NAME, CHAR(13), ''), CHAR(9), ''), ' ', '') = REPLACE('$itemNameSet', ' ', '')) ";
                                
                                $query_select_t_transfer = "select     
                                                                    EPS_T_PR_DETAIL.ITEM_NAME
                                                                    ,EPS_T_PR_DETAIL.DELIVERY_DATE
                                                                    ,EPS_T_PR_DETAIL.ITEM_TYPE_CD
                                                                    ,EPS_T_PR_DETAIL.QTY
                                                                    ,EPS_T_PR_DETAIL.ITEM_PRICE
                                                                    ,EPS_T_PR_DETAIL.AMOUNT
                                                                    ,EPS_T_PR_DETAIL.CURRENCY_CD
                                                                    ,EPS_T_PR_DETAIL.ACCOUNT_NO
                                                                    ,EPS_T_PR_DETAIL.RFI_NO
                                                                    ,EPS_T_PR_DETAIL.UNIT_CD
                                                                    ,EPS_T_PR_DETAIL.SUPPLIER_NAME
                                                                    ,EPS_T_PR_DETAIL.REMARK
                                                                   from         
                                                                    EPS_T_PR_DETAIL 
                                                                   left join
                                                                    EPS_T_TRANSFER 
                                                                   on 
                                                                    EPS_T_PR_DETAIL.PR_NO = EPS_T_TRANSFER.PR_NO 
                                                                    and REPLACE(REPLACE(REPLACE(EPS_T_PR_DETAIL.ITEM_NAME, CHAR(13), ''),CHAR(9), ''), ' ', '') = REPLACE(EPS_T_TRANSFER.ITEM_NAME, ' ', '') ";
                                
                                if($newItemStatus == '1140')
                                {
                                    /**
                                     * Item Rejected
                                     */
                                    if($flagSubjectItemReject == '')
                                    {
                                        $flagSubjectItemReject = '1';
                                        if($mailSubject == '')
                                        {
                                            $mailSubject = 'ITEM REJECTED';
                                        }
                                        else
                                        {
                                            $mailSubject .= ' & ITEM REJECTED';
                                        }
                                    }
                                    /*if(count($whereTransfer)) {
                                        $query_select_t_transfer .= "where
                                                                    EPS_T_TRANSFER.ITEM_STATUS = '1140'
                                                                    and EPS_T_PR_DETAIL.PR_NO = '$prNo'
                                                                    and (REPLACE(REPLACE(REPLACE(EPS_T_PR_DETAIL.ITEM_NAME, CHAR(13), ''), CHAR(9), ''), ' ', '') = REPLACE('$itemNameSet', ' ', ''))";
                                    }*/
                                    $whereTransfer[] =  " EPS_T_TRANSFER.ITEM_STATUS = '1140'";
                                }
                                
                                if(strtotime(str_replace('/', '-', $newDeliveryDate)) > strtotime(str_replace('/', '-', $deliveryDate)))
                                {
                                    /**
                                     * Change Due Date
                                     */
                                    if($flagSubjectChangeDate == '')
                                    {
                                        $flagSubjectChangeDate = '1';
                                        if($mailSubject == '')
                                        {
                                            $mailSubject = 'CHANGE DUE DATE';
                                        }
                                        else
                                        {
                                            $mailSubject .= ' & CHANGE DUE DATE';
                                        }
                                    }
                                    /*$query_select_t_transfer .= "where
                                                                    EPS_T_PR_DETAIL.PR_NO = '$prNo'
                                                                    and (REPLACE(REPLACE(REPLACE(EPS_T_PR_DETAIL.ITEM_NAME, CHAR(13), ''), CHAR(9), ''), ' ', '') = REPLACE('$itemNameSet', ' ', ''))";
									*/
								}
								
                                if(count($whereTransfer)) {
                                    $query_select_t_transfer .= "where " . implode('and ', $whereTransfer);
                                }
								
                                $sql_select_t_transfer = $conn->query($query_select_t_transfer);
                                $row_select_t_transfer = $sql_select_t_transfer->fetch(PDO::FETCH_ASSOC);
                                $itemRejectArray[] = array(
                                    'itemNameVal'           => $row_select_t_transfer['ITEM_NAME']
                                    ,'deliveryDatePrDetail' => $row_select_t_transfer['DELIVERY_DATE']
                                    ,'deliveryDateTransfer' => $newDeliveryDate
                                    ,'itemStatusName'       => $itemStatusName
                                );
                                $addItemRejectArray = $itemRejectArray;
                            }
                        }
                         
                        /** 
                         * CHANGE CHARGED BU
                         **/
                        if($prCharged != $newPrCharged){ 
                            if($mailSubject == '')
                            {
                                $mailSubject = 'CHANGE CHARGED BU';
                            }
                            else
                            {
                                $mailSubject .= ' & CHANGE CHARGED BU';
                            }
                            
                        } 
						
                        /**
                         * UPDATE to EPS_T_PR_HEADER
                         */
                        $query_update_pr_header = 
                                "update
                                    EPS_T_PR_HEADER
                                set
                                    PROC_REMARK = '$remarkProc'
                                    ,PR_STATUS = '".constant('1040')."'
                                    ,PROC_ACCEPT_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                    ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                    ,UPDATE_BY = '$sUserId'
                                where
                                    PR_NO = '$prNo'";
                        $sql_update_pr_header = $conn->query($query_update_pr_header);
						
						/**
                         * SELECT EPS_T_PR_HEADER
                         **/
                        $query_select_pr_header = "select
                                                        EPS_T_PR_HEADER.USERID
                                                        ,EPS_T_PR_HEADER.PR_STATUS
                                                        ,EPS_M_APP_STATUS.APP_STATUS_NAME
                                                    from
                                                        EPS_T_PR_HEADER
                                                    left join
                                                        EPS_M_APP_STATUS
                                                    on
                                                        EPS_T_PR_HEADER.PR_STATUS = EPS_M_APP_STATUS.APP_STATUS_CD
                                                    where
                                                        PR_NO = '$prNo'";
                        $sql_select_pr_header = $conn->query($query_select_pr_header);
                        $row_select_pr_header = $sql_select_pr_header->fetch(PDO::FETCH_ASSOC);
                        $prUserId   = $row_select_pr_header['USERID'];
                        $newPrStatus= $row_select_pr_header['APP_STATUS_NAME'];

                        /**********************************************************************
                         * SEND MAIL
                         **********************************************************************/
                        $mailFrom       = $sInet;
                        $mailFromName   = $sNotes; 

						if($prCharged != $newPrCharged
                                || $addItemRejectArray > 0)
                        {
							$mailSubject .= ". PR No: ".$prNo;
							
							/**
                             * TO APPROVER
                             **/
                            $query_select_t_app = "select 
                                                        EPS_T_PR_APPROVER.APPROVER_NO
                                                        ,EPS_T_PR_APPROVER.NPK
                                                        ,EPS_M_DSCID.INETML
                                                        ,EPS_M_USER.USERID
                                                        ,EPS_M_USER.PASSWORD 
                                                     from
                                                        EPS_T_PR_APPROVER
                                                     left join
                                                        EPS_M_DSCID
                                                     on
                                                        EPS_T_PR_APPROVER.NPK = EPS_M_DSCID.INOPOK
                                                     inner join 
                                                        EPS_M_USER 
                                                    on 
                                                        ltrim(EPS_M_DSCID.INOPOK) = ltrim(EPS_M_USER.NPK)    
                                                     where
                                                        EPS_T_PR_APPROVER.PR_NO = '$prNo'
                                                        and EPS_T_PR_APPROVER.APPROVER_NO <= 3";
                            $sql_select_t_app = $conn->query($query_select_t_app);
                            while($row_select_t_app = $sql_select_t_app->fetch(PDO::FETCH_ASSOC))
                            {
                                $userIdApprover = $row_select_t_app['USERID'];
                                $mailTo    = $row_select_t_app['INETML'];
                                //$mailTo      = 'muh.iqbal@taci.toyota-industries.com';
                                $passwordApprover  = $row_select_t_app['PASSWORD'];
                                $getParamLink   = paramEncrypt("action=open&prNo=$prNo&userId=$userIdApprover&password=$passwordApprover");
                                $mailMessage = "<table style='font-family: Arial; font-size: 12px;'>";
                                $mailMessage .= "<tr><td>PR No</td><td>: </td><td>".$prNo."</td></tr>";
                                $mailMessage .= "<tr><td>PR Status</td><td>: </td><td>".$newPrStatus."</td></tr>";
                                $mailMessage .= "<tr><td>Remark from Procurement</td><td>:</td><td>".$remarkProc."</td></tr>";
                                if($prCharged != $newPrCharged){ 
                                    $mailMessage .= "<tr><td>Charged BU</td><td>:</td><td>".$prCharged."</td></tr>";
                                    $mailMessage .= "<tr><td>New Charged BU</td><td>:</td><td>".$newPrCharged."</td></tr>";
                                }
                                $mailMessage .= "</table></font>";

                                $itemNo = 1;
                                if($addItemRejectArray > 0)
                                {
                                    $mailMessage .= "<table style='font-family: Arial; font-size: 12px; border-bottom: 1px solid #000000; border-top: 1px solid #000000;'>";
                                    $mailMessage .= "<tr style='font-weight: bold; text-align: center;'>
                                                        <td width= 30px rowspan=2 align=center>No.</td>
                                                        <td width= 370px rowspan=2 align=center>Item Name</td>
                                                        <td width= 250px colspan=2 align=center>Due Date</td>
                                                        <td width= 230px rowspan=2 align=center>Status</td>
                                                    </tr>
                                                    <tr style='font-weight: bold; text-align: center;'>
                                                        <td width= 120px align=center>Requester</td>
                                                        <td width= 120px align=center>Procurement</td>
                                                    </tr>
                                                    ";
                                    foreach ($addItemRejectArray as $addItemRejectArrays)
                                    {
                                        $itemNameArray = strtoupper(trim($addItemRejectArrays['itemNameVal']));
                                        $deliveryDatePrDetailArray = strtoupper(trim($addItemRejectArrays['deliveryDatePrDetail']));
                                        $deliveryDateTransferArray = strtoupper(trim($addItemRejectArrays['deliveryDateTransfer']));
                                        $itemStatusNameArray = strtoupper(trim($addItemRejectArrays['itemStatusName']));
                                        $mailMessage .= "<tr>
                                                            <td>".$itemNo."</td>
                                                            <td>".$itemNameArray."</td>
                                                            <td>".$deliveryDatePrDetailArray."</td>
                                                            <td>".$deliveryDateTransferArray."</td>
                                                            <td>".$itemStatusNameArray."</td>
                                                        </tr>";
                                        $itemNo++;
                                    }
                                    $mailMessage .= "</table>";
                                }
                                //$mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage
                                //SEMENTARA DI MATIKAN UNTUK REJECT PR
//                                prSendMail($prNo, $mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage);
                            }
							
							/**
							 * TO REQUESTER
							 **/
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
													ltrim(ltrim(EPS_M_USER.USERID)) = ltrim('".$prUserId."')";
							$sql_select_dscid = $conn->query($query_select_dscid);
							$row_select_dscid = $sql_select_dscid->fetch(PDO::FETCH_ASSOC);
							if($row_select_dscid){
								$mailTo    = $row_select_dscid['INETML'];
								//$mailTo      = 'BYAN_PURBA@denso.co.id';
								$passwordRequester  = $row_select_dscid['PASSWORD'];
								
								$getParamLink   = paramEncrypt("action=open&prNo=$prNo&userId=$prUserId&password=$passwordRequester");

								
								$mailMessage = "<table style='font-family: Arial; font-size: 12px;'>";
								$mailMessage .= "<tr><td>PR No</td><td>: </td><td>".$prNo."</td></tr>";
								$mailMessage .= "<tr><td>PR Status</td><td>: </td><td>".$newPrStatus."</td></tr>";
								$mailMessage .= "<tr><td>Remark from Procurement</td><td>:</td><td>".$remarkProc."</td></tr>";
								if($prCharged != $newPrCharged){ 
									$mailMessage .= "<tr><td>Charged BU</td><td>:</td><td>".$prCharged."</td></tr>";
									$mailMessage .= "<tr><td>New Charged BU</td><td>:</td><td>".$newPrCharged."</td></tr>";
								}
								$mailMessage .= "</table></font>";
								
								$itemNo = 1;
								if($addItemRejectArray > 0)
								{
									$mailMessage .= "<table style='font-family: Arial; font-size: 12px; border-bottom: 1px solid #000000; border-top: 1px solid #000000;'>";
									$mailMessage .= "<tr style='font-weight: bold; text-align: center;'>
														<td width= 30px rowspan=2 align=center>No.</td>
														<td width= 370px rowspan=2 align=center>Item Name</td>
														<td width= 250px colspan=2 align=center>Due Date</td>
														<td width= 230px rowspan=2 align=center>Status</td>
													</tr>
													<tr style='font-weight: bold; text-align: center;'>
														<td width= 120px align=center>Requester</td>
														<td width= 120px align=center>Procurement</td>
													</tr>
													";
									foreach ($addItemRejectArray as $addItemRejectArrays)
									{
										$itemNameArray = strtoupper(trim($addItemRejectArrays['itemNameVal']));
										$deliveryDatePrDetailArray = strtoupper(trim($addItemRejectArrays['deliveryDatePrDetail']));
										$deliveryDateTransferArray = strtoupper(trim($addItemRejectArrays['deliveryDateTransfer']));
										$itemStatusNameArray = strtoupper(trim($addItemRejectArrays['itemStatusName']));
										$mailMessage .= "<tr>
															<td>".$itemNo."</td>
															<td>".$itemNameArray."</td>
															<td>".$deliveryDatePrDetailArray."</td>
															<td>".$deliveryDateTransferArray."</td>
															<td>".$itemStatusNameArray."</td>
														 </tr>";
										$itemNo++;
									}
									$mailMessage .= "</table>";
								}
								prSendMail($prNo, $mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage);
							} 
						}
                        
						
                        /** 
                         * ACCEPT with REMARK
                         *
                        if($countReject > 0){ 
                            $getParamLink   = paramEncrypt("action=open&prNo=$prNo&userId=$prUserId&password=$passwordRequester");

                            $mailSubject = "ACCEPTED (with remark). PR No: ".$prNo;
                            $mailMessage = "<font face='Trebuchet MS' size='-1'><table>";
                            $mailMessage .= "<tr><td>PR No</td><td>: </td><td>".$prNo."</td></tr>";
                            $mailMessage .= "<tr><td>Remark from Procurement</td><td>:</td><td>".$remarkProc."</td></tr>";
                            $mailMessage .= "</table></font>";
                            //$mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage
                            prSendMail($prNo, $mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage);
                        }
                        /** 
                         * CHANGE CHARGED BU
                         *
                        if($prCharged != $newPrCharged){ 
                            $getParamLink   = paramEncrypt("action=open&prNo=$prNo&userId=$prUserId&password=$passwordRequester");

                            $mailSubject = "CHANGE CHARGED BU (by Procurement). PR No: ".$prNo;
                            $mailMessage = "<font face='Trebuchet MS' size='-1'><table>";
                            $mailMessage .= "<tr><td>PR No</td><td>: </td><td>".$prNo."</td></tr>";
                            $mailMessage .= "<tr><td>Charged BU</td><td>: </td><td>".$prCharged."</td></tr>";
                            $mailMessage .= "<tr><td>New Charged BU</td><td>: </td><td>".$newPrCharged."</td></tr>";
                            $mailMessage .= "</table></font>";
                            //$mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage
                            prSendMail($prNo, $mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage);
                        }*/
                        $msg = 'Success_Accept';
                    }
                    else if($countWait > 0)
                    {
                        $msg = 'Mandatory_1';
                    }   
                    else if($countReject == count($prItemData))
                    {
                        $msg = 'Mandatory_2';
                    }
                    else if($countReject != 0 && $remarkProc == '')
                    {
                        $msg = 'Mandatory_3';
                    }
                    else if($countInvalidDate > 0)
                    {
                        $msg = 'Mandatory_4';
                    }
                    else
                    {
                        $msg = 'UndefinedError';
                    }
					
					/*for($i = 0; $i < count($prItemData); $i++){
						$itemStatus     = $prItemData[$i]['itemStatus'];
						if($itemStatus == '1110'){
							$countWait++;
						}
						if($itemStatus == '1140'){
							$countReject++;
						}
					}
					if( (($countWait == 0 && $countReject == 0)) || ($countWait == 0 && $countReject !=0 && $remarkProc != ''))
					{
						$msg = 'Success_Accept';
					}
					else if($countWait > 0)
					{
						$msg = 'Mandatory_1';
					}
					else if($countReject == count($prItemData))
					{
						$msg = 'Mandatory_2';
					}
					else if($countReject != 0 && $remarkProc == '')
					{
						$msg = 'Mandatory_3';
					}
					else{
						$msg = 'UndefinedError';
					}

					if($msg == 'Success_Accept')
					{
						$currentDate    = date(Ymd);
						for($j = 0; $j < count($prItemData); $j++){
							$newItemCd         = $prItemData[$j]['itemCd'];
							$newItemName       = $prItemData[$j]['itemName'];
							$newRemark         = $prItemData[$j]['remark'];
							$newDeliveryDate   = encodeDate($prItemData[$j]['deliveryDate']);
							$newItemType       = $prItemData[$j]['itemType'];
							$newRfiNo          = $prItemData[$j]['rfiNo'];
							$newAccountNo      = $prItemData[$j]['accountNo'];
							$newSupplierCd     = $prItemData[$j]['supplierCd'];
							$newSupplierName   = $prItemData[$j]['supplierName'];
							$newUnitCd         = $prItemData[$j]['unitCd'];
							$newQty            = $prItemData[$j]['qty'];
							$newItemPrice      = $prItemData[$j]['itemPrice'];
							$newAmount         = $prItemData[$j]['amount'];
							$newCurrencyCd     = $prItemData[$j]['currencyCd'];
							$newItemStatus     = $prItemData[$j]['itemStatus'];
							$itemName          = $prItemData[$j]['refItemName'];

							if($newItemCd == ''){
								$newItemCd = '99';
							}
							if($newSupplierCd == ''){
								$newSupplierCd = 'SUP99';
							}
							$newItemPrice = str_replace(',', '', $newItemPrice);
							$newAmount = $newItemPrice * $newQty;
							if($newItemStatus == '1140'){
								$newSupplierCd      = '';
								$newSupplierName    = '';
								$newCurrencyCd      = '';
							}

							// Define Transfer Id
							$query2 = "select count(*) as TRANSFER_COUNT from EPS_T_TRANSFER where substring(TRANSFER_ID, 1, 8) = '$currentDate'";
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
							 * INSERT to EPS_T_TRANSFER
							 */
							/*$query_insert_transfer = 
									"insert into 
										EPS_T_TRANSFER
										(
											TRANSFER_ID
											,ITEM_STATUS
											,PR_NO
											,REQUESTER
											,CHARGED_BU
											,NEW_CHARGED_BU
											,NEW_ITEM_CD
											,ITEM_NAME
											,NEW_ITEM_NAME
											,NEW_DELIVERY_DATE
											,NEW_QTY
											,NEW_ITEM_PRICE
											,NEW_AMOUNT
											,NEW_CURRENCY_CD
											,NEW_ITEM_TYPE_CD
											,NEW_ACCOUNT_NO
											,NEW_RFI_NO
											,NEW_UNIT_CD
											,NEW_SUPPLIER_CD
											,NEW_SUPPLIER_NAME
											,NEW_REMARK
											,CREATE_DATE
											,CREATE_BY
											,UPDATE_DATE
											,UPDATE_BY
										) 
									values 
										(
											'$transferId'
											,'$newItemStatus'
											,'$prNo'
											,'$requester'
											,'$prCharged'
											,'$newPrCharged'
											,'$newItemCd'
											,'$itemName'
											,'$newItemName'
											,'$newDeliveryDate'
											,'$newQty'
											,'$newItemPrice'
											,'$newAmount'
											,'$newCurrencyCd'
											,'$newItemType'
											,'$newAccountNo'
											,'$newRfiNo'
											,'$newUnitCd'
											,'$newSupplierCd'
											,'$newSupplierName'
											,'$newRemark'
											,convert(VARCHAR(24), GETDATE(), 120)
											,'$sUserId'
											,convert(VARCHAR(24), GETDATE(), 120)
											,'$sUserId'
									)";
							$sql_insert_transfer = $conn->query($query_insert_transfer);
						}
						
						/**
						 * SELECT EPS_T_PR_HEADER
						 **/
						/*$query_select_pr_header = "select
														USERID
													from
														EPS_T_PR_HEADER
													where
														PR_NO = '$prNo'";
						$sql_select_pr_header = $conn->query($query_select_pr_header);
						$row_select_pr_header = $sql_select_pr_header->fetch(PDO::FETCH_ASSOC);
						$prUserId = $row_select_pr_header['USERID'];
						
						/**
						 * UPDATE to EPS_T_PR_HEADER
						 */
						/*$query_update_pr_header = 
								"update
									EPS_T_PR_HEADER
								set
									PROC_REMARK = '$remarkProc'
									,PR_STATUS = '".constant('1040')."'
									,PROC_ACCEPT_DATE = convert(VARCHAR(24), GETDATE(), 120)
									,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
									,UPDATE_BY = '$sUserId'
								where
									PR_NO = '$prNo'";
						$sql_update_pr_header = $conn->query($query_update_pr_header);

						/**********************************************************************
						 * SEND MAIL
						 **********************************************************************/
						/*$mailFrom       = $sInet;
						$mailFromName   = $sNotes; 
						
						/**
						 * TO REQUESTER
						 **/
						/*$query_select_dscid = "select 
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
						$sql_select_dscid = $conn->query($query_select_dscid);
						$row_select_dscid = $sql_select_dscid->fetch(PDO::FETCH_ASSOC);
						if($row_select_dscid){
							$mailTo    = $row_select_dscid['INETML'];
							//$mailTo      = 'BYAN_PURBA@denso.co.id';
							$passwordRequester  = $row_select_dscid['PASSWORD'];
						}         
						/** 
						 * ACCEPT with REMARK
						 **/
						/*if($countReject > 0){ 
							$getParamLink   = paramEncrypt("action=open&prNo=$prNo&userId=$prUserId&password=$passwordRequester");
							
							$mailSubject = "ACCEPTED (with remark). PR No: ".$prNo;
							$mailMessage = "<font face='Trebuchet MS' size='-1'><table>";
							$mailMessage .= "<tr><td>PR No</td><td>: </td><td>".$prNo."</td></tr>";
							$mailMessage .= "<tr><td>Remark from Procurement</td><td>:</td><td>".$remarkProc."</td></tr>";
							$mailMessage .= "</table></font>";
							//$mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage
							prSendMail($prNo, $mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage);
						}
						/** 
						 * CHANGE CHARGED BU
						 **/
						/*if($prCharged != $newPrCharged){ 
							$getParamLink   = paramEncrypt("action=open&prNo=$prNo&userId=$prUserId&password=$passwordRequester");
							
							$mailSubject = "CHANGE CHARGED BU (by Procurement). PR No: ".$prNo;
							$mailMessage = "<font face='Trebuchet MS' size='-1'><table>";
							$mailMessage .= "<tr><td>PR No</td><td>: </td><td>".$prNo."</td></tr>";
							$mailMessage .= "<tr><td>Charged BU</td><td>: </td><td>".$prCharged."</td></tr>";
							$mailMessage .= "<tr><td>New Charged BU</td><td>: </td><td>".$newPrCharged."</td></tr>";
							$mailMessage .= "</table></font>";
							//$mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage
							prSendMail($prNo, $mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage);
						}
						
					}*/
					
				}
                else
                {
                    $msg = 'Mandatory_5';
                }
				
                
            }
            else
            {
                $msg = "SessionExpired";
            }
        }
        
        if($action == 'RejectPr'){
            $prNo           = strtoupper(trim($_GET['prNoPrm']));
            $remarkProc     = strtoupper(trim($_GET['remarkProcPrm']));
            $remarkProc     = str_replace("'", "''", $remarkProc);
            $procPrUpdateDate= strtoupper($_GET['procPrUpdateDatePrm']);
            
			$query_eps_t_pr_header = "select
                                        UPDATE_DATE
                                      from 
                                        EPS_T_PR_HEADER
                                      where
                                        PR_NO = '$prNo'";
            $sql_eps_t_pr_header = $conn->query($query_eps_t_pr_header);
            $row_eps_t_pr_header=$sql_eps_t_pr_header->fetch(PDO::FETCH_ASSOC);
            $newUpdateDate = strtoupper($row_eps_t_pr_header['UPDATE_DATE']);
			
			if($procPrUpdateDate == $newUpdateDate)
            {
				if($remarkProc != '')
				{
					/**
					 * UPDATE EPS_T_PR_HEADER
					 **/
					$query_update_pr_header = "update
													EPS_T_PR_HEADER
												set
													PROC_REMARK = '$remarkProc'
													,PR_STATUS = '".constant('1080')."'
													,PROC_ACCEPT_DATE = convert(VARCHAR(24), GETDATE(), 120)
													,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
													,UPDATE_BY = '$sUserId'
												where
													PR_NO = '$prNo'";
					$sql_update_pr_header = $conn->query($query_update_pr_header);
					
					/**
					 * SELECT EPS_T_PR_HEADER
					 **/
					$query_select_pr_header = "select
												USERID
											   from
												EPS_T_PR_HEADER
											   where
												PR_NO = '$prNo'";
					$sql_select_pr_header = $conn->query($query_select_pr_header);
					$row_select_pr_header = $sql_select_pr_header->fetch(PDO::FETCH_ASSOC);
					$prUserId = $row_select_pr_header['USERID'];
					
					/**********************************************************************
					 * SEND MAIL
					 **********************************************************************/
					$mailFrom       = $sInet;
					$mailFromName   = $sNotes;  
					$prStatus       = "PR Rejected by Procurement";
					
					/**
					 * TO REQUESTER
					 **/
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
												ltrim(ltrim(EPS_M_USER.USERID)) = ltrim('".$prUserId."')";
					$sql_select_dscid = $conn->query($query_select_dscid);
					$row_select_dscid = $sql_select_dscid->fetch(PDO::FETCH_ASSOC);
					if($row_select_dscid){
						$mailTo           = $row_select_dscid['INETML'];
						//$mailTo             = 'BYAN_PURBA@denso.co.id';
						$passwordRequester  = $row_select_dscid['PASSWORD'];
						$getParamLink       = paramEncrypt("action=open&prNo=$prNo&userId=$prUserId&password=$passwordRequester");
						$mailSubject = "REJECTED (by Procurement). PR No: ".$prNo;
						$mailMessage = "<table style='font-family: Arial; font-size: 12px;'>";
						$mailMessage .= "<tr><td>PR No</td><td>: </td><td>".$prNo."</td></tr>";
						$mailMessage .= "<tr><td>PR Status</td><td>:</td><td>".$prStatus."</td></tr>";
						$mailMessage .= "<tr><td>Remark from Procurement</td><td>:</td><td>".$remarkProc."</td></tr>";
						$mailMessage .= "</table></font>";
						//$mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage
						prSendMail($prNo, $mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage);
					}
					
					/**
					 * TO APPROVER
					 */
					$query_select_max_t_pr_approver = "select 
														max(APPROVER_NO) as MAX_APPROVER
													   from         
														EPS_T_PR_APPROVER
													   where   
														PR_NO = '$prNo'";
					$sql_select_max_t_pr_approver = $conn->query($query_select_max_t_pr_approver);
					$row_select_max_t_pr_approver = $sql_select_max_t_pr_approver->fetch(PDO::FETCH_ASSOC);
					$maxApprover = $row_select_max_t_pr_approver['MAX_APPROVER'];
					
					$query_select_t_pr_approver = "select 
													NPK
												   from
													EPS_T_PR_APPROVER
												   where
													PR_NO = '$prNo'
													and APPROVER_NO < $maxApprover";
					$sql_select_t_pr_approver = $conn->query($query_select_t_pr_approver);
					while($row_select_t_pr_approver = $sql_select_t_pr_approver->fetch(PDO::FETCH_ASSOC)){
						$approverNpk = $row_select_t_pr_approver['NPK'];
						$query_select_dscid_approver = "select 
															EPS_M_DSCID.INETML
															,EPS_M_USER.PASSWORD 
														from 
															EPS_M_DSCID 
														inner join 
															EPS_M_USER 
														on 
															ltrim(EPS_M_DSCID.INOPOK) = ltrim(EPS_M_USER.NPK) 
														where  
															ltrim(ltrim(EPS_M_USER.USERID)) = ltrim('".$approverNpk."')";
						$sql_select_dscid_approver = $conn->query($query_select_dscid_approver);
						$row_select_dscid_approver = $sql_select_dscid_approver->fetch(PDO::FETCH_ASSOC);
						if($row_select_dscid_approver){
							$mailToApprover   = $row_select_dscid_approver['INETML'];
							//$mailToApprover     = 'BYAN_PURBA@denso.co.id';
							$passwordApprover   = $row_select_dscid_approver['PASSWORD'];
							$getParamLinkApprover= paramEncrypt("action=open&prNo=$prNo&userId=$approverNpk&password=$passwordApprover");
							$mailSubject = "REJECTED (by Procurement). PR No: ".$prNo;
							$mailMessage = "<table style='font-family: Arial; font-size: 12px;'>";
							$mailMessage .= "<tr><td>PR No</td><td>: </td><td>".$prNo."</td></tr>";
							$mailMessage .= "<tr><td>PR Status</td><td>:</td><td>".$prStatus."</td></tr>";
							$mailMessage .= "<tr><td>Remark from Procurement</td><td>:</td><td>".$remarkProc."</td></tr>";
							$mailMessage .= "</table></font>";
							//$mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage
//							prSendMail($prNo, $mailToApprover, $mailFrom, $mailFromName, $getParamLinkApprover, $mailSubject, $mailMessage);

						}
					}
					
					/**
                    * TO PIC
                    */
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
                                                ltrim(ltrim(EPS_M_USER.USERID)) = ltrim('".$sUserId."')";
                    $sql_select_dscid = $conn->query($query_select_dscid);
                    $row_select_dscid = $sql_select_dscid->fetch(PDO::FETCH_ASSOC);
                    if($row_select_dscid){
                        $mailTo           = $row_select_dscid['INETML'];
                        //$mailTo             = 'BYAN_PURBA@denso.co.id';
                        $passwordPic  = $row_select_dscid['PASSWORD'];
                        $getParamLink       = paramEncrypt("action=open&prNo=$prNo&userId=$sUserId&password=$passwordPic");
                        $mailSubject = "REJECTED (by Procurement). PR No: ".$prNo;
                        $mailMessage = "<table style='font-family: Arial; font-size: 12px;'>";
                        $mailMessage .= "<tr><td>PR No</td><td>: </td><td>".$prNo."</td></tr>";
                        $mailMessage .= "<tr><td>PR Status</td><td>:</td><td>".$prStatus."</td></tr>";
                        $mailMessage .= "<tr><td>Remark from Procurement</td><td>:</td><td>".$remarkProc."</td></tr>";
                        $mailMessage .= "</table></font>";
                        //$mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage
//                        prSendMail($prNo, $mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage);
                    }
					$msg = 'Success_Reject';
				}
				else if($remarkProc == '')
				{
					$msg = 'Mandatory_1';
				}
				else 
				{
					$msg = 'UndefinedError';
				}
			}
            else
            {
                $msg = 'Mandatory_5';
            }
        }
        
		if($action == 'EditPrWaiting'){
            $prNo               = strtoupper(trim($_GET['prNoPrm']));
            $newProcInCharge    = strtoupper($_GET['newProcInChargePrm']);
            $procPrUpdateDate   = strtoupper(trim($_GET['procPrUpdateDatePrm']));
            
            $query_eps_t_pr_header = "select
                                        UPDATE_DATE
                                      from 
                                        EPS_T_PR_HEADER
                                      where
                                        PR_NO = '$prNo'";
            $sql_eps_t_pr_header = $conn->query($query_eps_t_pr_header);
            $row_eps_t_pr_header=$sql_eps_t_pr_header->fetch(PDO::FETCH_ASSOC);
            $newUpdateDate = strtoupper($row_eps_t_pr_header['UPDATE_DATE']);
            
            if($procPrUpdateDate == $newUpdateDate)
            {
                /**
                 * Unset SESSION 
                 **/
                unset($_SESSION['prStatus']);
                unset($_SESSION['procPrUpdateDate']);
                
                $query_update_t_pr_header = "update
                                                EPS_T_PR_HEADER
                                            set
                                                PROC_IN_CHARGE = '$newProcInCharge'
                                                ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                ,UPDATE_BY = '$sUserId'
                                            where
                                                PR_NO = '$prNo'";
                $conn->query($query_update_t_pr_header);
                $msg = 'Success';
            }
            else if($procPrUpdateDate != $newUpdateDate)
            {
                $msg = 'Mandatory_1';
            }
            else
            {
                $msg = 'UndefinedError';
            }
        }
        
        if($action == 'GeneratePoNumber')
		{
            $transferIdArray    = trim($_GET['transferIdArray']);
            $newTransferIdArray = explode(",", $transferIdArray);
            $currentTransferIdArray = array();  
            
            for($x = 0; $x < count($newTransferIdArray); $x++){
                $transferIdVal =  $newTransferIdArray[$x];
                $query_update = "update
                                    EPS_T_TRANSFER
                                 set
                                    ITEM_STATUS = '1260'
                                    ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                    ,UPDATE_BY = '$sUserId'
                                 where
                                    TRANSFER_ID = '$transferIdVal'";
                $sql_update = $conn->query($query_update);
				if($x == 0)
                {
                    $currentTransferIdArray = "'".$transferIdVal."'";
                }
                else
                {
                    $currentTransferIdArray = $currentTransferIdArray.","."'".$transferIdVal."'";
                }
            } 
            /**
             * INITIAL VALUE 
             **/
            $initialFlag    = ''; 
            $newFlag        = ''; 
            $countItem      = 1;
            $setQtyPo       = 0;
            $setAmountPo    = 0;
			
            $query_select = "select
                                EPS_M_BUNIT.PLANT_ALIAS
                                ,EPS_M_BUNIT.COMPANY_CD
                                ,EPS_T_TRANSFER.TRANSFER_ID
                                ,EPS_T_TRANSFER.NEW_CHARGED_BU
                                ,EPS_T_TRANSFER.NEW_SUPPLIER_CD
                                ,EPS_T_TRANSFER.NEW_SUPPLIER_NAME
                                ,EPS_T_TRANSFER.NEW_CURRENCY_CD
                                ,EPS_M_SUPPLIER.VAT
                                ,EPS_T_TRANSFER.NEW_DELIVERY_DATE
                                ,EPS_T_TRANSFER.NEW_ITEM_CD
                                ,EPS_T_TRANSFER.NEW_ITEM_NAME
                                ,EPS_T_TRANSFER.NEW_QTY
                                ,EPS_T_TRANSFER.ACTUAL_QTY
                                ,EPS_T_TRANSFER.NEW_ITEM_PRICE
                                ,EPS_T_TRANSFER.NEW_AMOUNT
                                ,EPS_T_TRANSFER.NEW_UNIT_CD
                                ,EPS_T_TRANSFER.NEW_ITEM_TYPE_CD
                                ,EPS_T_TRANSFER.NEW_ACCOUNT_NO
                                ,EPS_T_TRANSFER.NEW_RFI_NO
                            from
                                EPS_T_TRANSFER
                            left join
                                EPS_M_BUNIT 
                            on 
                                EPS_T_TRANSFER.NEW_CHARGED_BU = EPS_M_BUNIT.BU_CD
                            left join
                                EPS_M_SUPPLIER 
                            on 
                                EPS_T_TRANSFER.NEW_SUPPLIER_CD = EPS_M_SUPPLIER.SUPPLIER_CD
                            where
                                EPS_T_TRANSFER.ITEM_STATUS = '1260'
                                and EPS_T_TRANSFER.TRANSFER_ID in ($currentTransferIdArray)
                            order by
                                NEW_SUPPLIER_CD, NEW_DELIVERY_DATE, PLANT_ALIAS ";
            $sql_select = $conn->query($query_select);
            
            while($row_select=$sql_select->fetch(PDO::FETCH_ASSOC)){
                if($initialFlag == ''){
                    $setPlantAlias      = $row_select['PLANT_ALIAS'];
                    $setCompanyCd       = $row_select['COMPANY_CD'];
                    $setNewChargedBu    = $row_select['NEW_CHARGED_BU'];
                    $setSupplierCd      = $row_select['NEW_SUPPLIER_CD'];
                    $setSupplierName    = $row_select['NEW_SUPPLIER_NAME'];
                    $setCurrencyCd      = $row_select['NEW_CURRENCY_CD'];
                    $setVat             = $row_select['VAT'];
                    $setDeliveryDate    = $row_select['NEW_DELIVERY_DATE'];
                    $setTransferId      = $row_select['TRANSFER_ID'];
                    $setItemCd          = $row_select['NEW_ITEM_CD'];
                    $setItemName        = $row_select['NEW_ITEM_NAME'];
                    $setItemName        = str_replace("'", "''", $setItemName);
                    $setItemName        = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $setItemName);
                    $setItemName        = preg_replace('/\s+/', ' ',$setItemName);
                    $setQty             = $row_select['NEW_QTY'];
                    $setActualQty       = $row_select['ACTUAL_QTY'];
                    $setItemPrice       = $row_select['NEW_ITEM_PRICE'];
                    $setAmount          = $row_select['NEW_AMOUNT'];
                    $setUnitCd          = $row_select['NEW_UNIT_CD'];
                    $setItemTypeCd      = $row_select['NEW_ITEM_TYPE_CD'];
                    $setAccountNo       = $row_select['NEW_ACCOUNT_NO'];
                    $setRfiNo           = $row_select['NEW_RFI_NO'];
                    $initialFlag        = '1';
                }
                
                if( $setPlantAlias == $row_select['PLANT_ALIAS']
                        && trim($setSupplierCd) == trim($row_select['NEW_SUPPLIER_CD']) 
                        && $setDeliveryDate == $row_select['NEW_DELIVERY_DATE'])
                {
                    $setPlantAlias      = $row_select['PLANT_ALIAS'];
                    $setCompanyCd       = $row_select['COMPANY_CD'];
                    $setNewChargedBu    = $row_select['NEW_CHARGED_BU'];
                    $setSupplierCd      = $row_select['NEW_SUPPLIER_CD'];
                    $setSupplierName    = $row_select['NEW_SUPPLIER_NAME'];
                    $setCurrencyCd      = $row_select['NEW_CURRENCY_CD'];
                    $setVat             = $row_select['VAT'];
                    $setDeliveryDate    = $row_select['NEW_DELIVERY_DATE'];
                    $setTransferId      = $row_select['TRANSFER_ID'];
                    $setItemCd          = $row_select['NEW_ITEM_CD'];
                    $setItemName        = $row_select['NEW_ITEM_NAME'];
                    $setItemName        = str_replace("'", "''", $setItemName);
                    $setItemName        = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $setItemName);
                    $setItemName        = preg_replace('/\s+/', ' ',$setItemName);
                    $setQty             = $row_select['NEW_QTY'];
                    $setActualQty       = $row_select['ACTUAL_QTY'];
                    $setItemPrice       = $row_select['NEW_ITEM_PRICE'];
                    $setAmount          = $row_select['NEW_AMOUNT'];
                    $setUnitCd          = $row_select['NEW_UNIT_CD'];
                    $setItemTypeCd      = $row_select['NEW_ITEM_TYPE_CD'];
                    $setAccountNo       = $row_select['NEW_ACCOUNT_NO'];
                    $setRfiNo           = $row_select['NEW_RFI_NO'];
                    
                    $split_item_price = explode('.', $setItemPrice);
                    if($split_item_price[1] == 0)
                    {
                        $setItemPrice = number_format($setItemPrice);
                    }
                    else
                    {
                        $setItemPrice = number_format($setItemPrice, 2);
                    }
                    $setItemPrice       = str_replace(',', '',$setItemPrice);
                    
                    $split_qty = explode('.', $setQty);
                    if($split_qty[1] == 0)
                    {
                        $setQty = number_format($setQty);
                    }
                    $setQty       = str_replace(',', '',$setQty);
					
					$split_act_qty = explode('.', $setActualQty);
                    if($split_act_qty[1] == 0)
                    {
                        $setActualQty = number_format($setActualQty);
                    }
                    $setActualQty       = str_replace(',', '',$setActualQty);
                    
					if($setQty != $setActualQty)
                    {
                        $setQtyPo   = $setQty - $setActualQty;
                        $setAmountPo= $setQtyPo * $setItemPrice;
                    }
                    else
                    {
                        $setQtyPo   = $setQty;
                        $setAmountPo= $setQtyPo * $setItemPrice;
                    }
					
					$split_amount   = explode('.', $setAmountPo);
                    if($split_amount[1] == 0)
                    {
                        $setAmountPo = number_format($setAmountPo);
                    }
                    else
                    {
                        $setAmountPo = number_format($setAmountPo, 2);
                    }
                    $setAmountPo = str_replace(',', '',$setAmountPo);
                    $setAmountPo = rtrim(rtrim(number_format($setAmountPo, 2, ".", ""), '0'), '.');
					
                    if($newFlag == ''){
                        $currentYear        = (int)date(y);
                        $currentMonth       = date(m);
                        $currenctYearMonth  = ($currentYear + 30).$currentMonth;
                        
                        $query_select_po_seq = "select 
                                                    PO_RUNNING_NO
                                                    ,PO_RUNNING_DATE
                                                from
                                                    EPS_T_PO_SEQUENCE
                                                where
                                                    PO_RUNNING_DATE = '$currenctYearMonth'";
                        $sql_select_po_seq = $conn->query($query_select_po_seq);
                        $row_selec_po_seq = $sql_select_po_seq->fetch(PDO::FETCH_ASSOC);
                        if($row_selec_po_seq){
                            $poRunNo    = $row_selec_po_seq['PO_RUNNING_NO'];
                            $poRunDate  = $row_selec_po_seq['PO_RUNNING_DATE'];
                        }
                        if($currenctYearMonth == $poRunDate){
                            $poRunNo = $poRunNo + 1;
                            $query_update_po_seq = "update
                                                        EPS_T_PO_SEQUENCE
                                                    set
                                                        PO_RUNNING_NO = '$poRunNo'
                                                        ,PO_RUNNING_DATE = '$poRunDate'
                                                        ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120) 
                                                        ,UPDATE_BY = '$sUserId'
                                                    where 
                                                        PO_RUNNING_DATE = '$poRunDate'";
                            $conn->query($query_update_po_seq);
                            
                        }else{
                            $poRunNo = 1;
                            $poRunDate = $currenctYearMonth;
                            $query_insert_po_seq = "insert
                                                        EPS_T_PO_SEQUENCE
                                                        (
                                                             PO_RUNNING_NO
                                                            ,PO_RUNNING_DATE
                                                            ,CREATE_DATE
                                                            ,CREATE_BY
                                                            ,UPDATE_DATE
                                                            ,UPDATE_BY
                                                        )
                                                    values
                                                        (
                                                            '$poRunNo'
                                                            ,'$poRunDate'
                                                            ,convert(VARCHAR(24), GETDATE(), 120)
                                                            ,'$sUserId'
                                                            ,convert(VARCHAR(24), GETDATE(), 120)
                                                            ,'$sUserId'
                                                        )";
                             $conn->query($query_insert_po_seq);
                        }
                        /**
                        * INITIAL VALUE 
                        **/
                        $sequence       = str_pad($poRunNo, 4, "0", STR_PAD_LEFT);
                        $poNo           = $poRunDate.$sequence;
                        $poIssuedDate   = (int)date(Y).date(m).date(d);
                        
                        /**
                         * INSERT EPS_T_PO_HEADER 
                         **/
                        $query_insert_po = "insert into
                                                EPS_T_PO_HEADER
                                                (
                                                    PO_NO
                                                    ,PO_STATUS
                                                    ,ISSUED_DATE
                                                    ,ISSUED_BY
                                                    ,SUPPLIER_CD
                                                    ,SUPPLIER_NAME
                                                    ,CURRENCY_CD
                                                    ,VAT
                                                    ,DELIVERY_DATE
                                                    ,DELIVERY_PLANT
                                                    ,COMPANY_CD
                                                    ,APPROVER
                                                    ,CREATE_DATE
                                                    ,CREATE_BY
                                                    ,UPDATE_DATE
                                                    ,UPDATE_BY
                                                )
                                            values
                                                (
                                                    '$poNo'
                                                    ,'1210'
                                                    ,'$poIssuedDate'
                                                    ,'$sUserId'
                                                    ,'$setSupplierCd'
                                                    ,'$setSupplierName'
                                                    ,'$setCurrencyCd'
                                                    ,'$setVat'
                                                    ,'$setDeliveryDate'
                                                    ,'$setPlantAlias'
                                                    ,'$setCompanyCd'
                                                    ,''
                                                    ,convert(VARCHAR(24), GETDATE(), 120)
                                                    ,'$sUserId'
                                                    ,convert(VARCHAR(24), GETDATE(), 120)
                                                    ,'$sUserId'
                                                )";
                        $conn->query($query_insert_po);
                        $newFlag = '1';
                    }
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
                                                    ,CREATE_DATE
                                                    ,CREATE_BY
                                                    ,UPDATE_DATE
                                                    ,UPDATE_BY
                                                )
                                            values
                                                (
                                                    '$poNo'
                                                    ,'$setTransferId'
                                                    ,'$setItemCd'
                                                    ,'$setItemName'
                                                    ,'$setQtyPo'
                                                    ,'$setItemPrice'
                                                    ,'$setAmountPo'
                                                    ,'$setUnitCd'
                                                    ,'$setItemTypeCd'
                                                    ,'$setAccountNo'
                                                    ,'$setRfiNo'
                                                    ,convert(VARCHAR(24), GETDATE(), 120)
                                                    ,'$sUserId'
                                                    ,convert(VARCHAR(24), GETDATE(), 120)
                                                    ,'$sUserId'
                                                )";
                    $conn->query($query_insert_po_detail);
                    $countItem++;
                       
					/**
                     * UPDATE PROGRAM FOR PARTIAL QTY ISSUE
                     * UPDATED BY   : BYAN PURBAPRANIDHANA
                     * UPDATE DATE  : SEP 29, 2015. 16.00
                     **/
					if($setQty != $setActualQty)
                    {
                        $newActualQty      = $setQtyPo + $setActualQty;
                        $newActualAmount   = $newActualQty * $setItemPrice;
                        
                        $split_act_amount   = explode('.', $newActualAmount);
                        if($split_act_amount[1] == 0)
                        {
                            $newActualAmount = number_format($newActualAmount);
                        }
                        else
                        {
                            $newActualAmount = number_format($newActualAmount, 2);
                        }
                        $newActualAmount = str_replace(',', '',$newActualAmount);
                        $newActualAmount   = rtrim(rtrim(number_format($newActualAmount, 2, ".", ""), '0'), '.');
						
                        /**
                         * UPDATE EPS_T_TRANSFER 
                         **/
                        $query_update_transfer_by_qty = "update
                                                            EPS_T_TRANSFER
                                                        set
                                                            ACTUAL_QTY = '$newActualQty'
                                                            ,NEW_AMOUNT = '$newActualAmount'
                                                            ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                            ,UPDATE_BY = '$sUserId'
                                                        where
                                                            TRANSFER_ID = '$setTransferId'";
                        //$conn->query($query_update_transfer_by_qty);
                    }
					
					/**
                     * UPDATE EPS_T_TRANSFER 
                     **/
                    $query_update_transfer = "update
                                                EPS_T_TRANSFER
                                              set
                                                ITEM_STATUS = '1270'
                                                ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                ,UPDATE_BY = '$sUserId'
                                              where
                                                TRANSFER_ID = '$setTransferId' 
                                              ";
                    $conn->query($query_update_transfer);
					
                    if($countItem > 10){ /** limit until 10 */
                        $initialFlag= '';
                        $newFlag    = '';
                        $countItem  = 1;
                    }
                    
                }
				else{
                    $currentYear        = (int)date(y);
                    $currentMonth       = date(m);
                    $currenctYearMonth  = ($currentYear + 30).$currentMonth;
                        
                    $query_select_po_seq = "select 
                                                PO_RUNNING_NO
                                                ,PO_RUNNING_DATE
                                            from
                                                EPS_T_PO_SEQUENCE
                                            where
                                                PO_RUNNING_DATE = '$currenctYearMonth'";
                    $sql_select_po_seq = $conn->query($query_select_po_seq);
                    $row_selec_po_seq = $sql_select_po_seq->fetch(PDO::FETCH_ASSOC);
                    
                    if($row_selec_po_seq){
                        $poRunNo    = $row_selec_po_seq['PO_RUNNING_NO'];
                        $poRunDate  = $row_selec_po_seq['PO_RUNNING_DATE'];
                    }
                    
                    if($currenctYearMonth == $poRunDate){
                        $poRunNo = $poRunNo + 1;
                        $query_update_po_seq = "update
                                                    EPS_T_PO_SEQUENCE
                                                set
                                                    PO_RUNNING_NO = '$poRunNo'
                                                    ,PO_RUNNING_DATE = '$poRunDate'
                                                    ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                    ,UPDATE_BY = '$sUserId'
                                                where
                                                    PO_RUNNING_DATE = '$poRunDate'";
                        $conn->query($query_update_po_seq);
                          
                    }else{
                        $poRunNo = 1;
                        $poRunDate = $currenctYearMonth;
                        $query_insert_po_seq = "insert
                                                    EPS_T_PO_SEQUENCE
                                                    (
                                                        PO_RUNNING_NO
                                                        ,PO_RUNNING_DATE
                                                        ,CREATE_DATE
                                                        ,CREATE_BY
                                                        ,UPDATE_DATE
                                                        ,UPDATE_BY
                                                    )
                                                values
                                                    (
                                                        '$poRunNo'
                                                        ,'$poRunDate'
                                                        ,convert(VARCHAR(24), GETDATE(), 120)
                                                        ,'$sUserId'
                                                        ,convert(VARCHAR(24), GETDATE(), 120)
                                                        ,'$sUserId'
                                                    )";
                        $conn->query($query_insert_po_seq);
                    }
                    $setPlantAlias      = $row_select['PLANT_ALIAS'];
                    $setCompanyCd       = $row_select['COMPANY_CD'];
                    $setNewChargedBu    = $row_select['NEW_CHARGED_BU'];
                    $setSupplierCd      = $row_select['NEW_SUPPLIER_CD'];
                    $setSupplierName    = $row_select['NEW_SUPPLIER_NAME'];
                    $setCurrencyCd      = $row_select['NEW_CURRENCY_CD'];
                    $setVat             = $row_select['VAT'];
                    $setDeliveryDate    = $row_select['NEW_DELIVERY_DATE'];
                    $setTransferId      = $row_select['TRANSFER_ID'];
                    $setItemCd          = $row_select['NEW_ITEM_CD'];
                    $setItemName        = $row_select['NEW_ITEM_NAME'];
                    $setItemName        = str_replace("'", "''", $setItemName);
                    $setItemName        = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $setItemName);
                    $setItemName        = preg_replace('/\s+/', ' ',$setItemName);
                    $setQty             = $row_select['NEW_QTY'];
                    $setActualQty       = $row_select['ACTUAL_QTY'];
                    $setItemPrice       = $row_select['NEW_ITEM_PRICE'];
                    $setAmount          = $row_select['NEW_AMOUNT'];
                    $setUnitCd          = $row_select['NEW_UNIT_CD'];
                    $setItemTypeCd      = $row_select['NEW_ITEM_TYPE_CD'];
                    $setAccountNo       = $row_select['NEW_ACCOUNT_NO'];
                    $setRfiNo           = $row_select['NEW_RFI_NO'];
                    //$setItemPrice       = number_format($setItemPrice);
                    //$setItemPrice       = str_replace(',', '',$setItemPrice);
					$split_item_price = explode('.', $setItemPrice);
                    if($split_item_price[1] == 0)
                    {
                        $setItemPrice = number_format($setItemPrice);
                    }
                    else
                    {
                        $setItemPrice = number_format($setItemPrice, 2);
                    }
                    $setItemPrice       = str_replace(',', '',$setItemPrice);
                    
                    $split_qty = explode('.', $setQty);
                    if($split_qty[1] == 0)
                    {
                        $setQty = number_format($setQty);
                    }
                    $setQty       = str_replace(',', '',$setQty);
                    
					$split_act_qty = explode('.', $setActualQty);
                    if($split_act_qty[1] == 0)
                    {
                        $setActualQty = number_format($setActualQty);
                    }
                    $setActualQty       = str_replace(',', '',$setActualQty);
					
					if($setQty != $setActualQty)
                    {
                        $setQtyPo   = $setQty - $setActualQty;
                        $setAmountPo= $setQtyPo * $setItemPrice;
                    }
                    else
                    {
                        $setQtyPo   = $setQty;
                        $setAmountPo= $setQtyPo * $setItemPrice;
                    }
					
					$split_amount   = explode('.', $setAmountPo);
                    if($split_amount[1] == 0)
                    {
                        $setAmountPo = number_format($setAmountPo);
                    }
                    else
                    {
                        $setAmountPo = number_format($setAmountPo, 2);
                    }
                    $setAmountPo = str_replace(',', '',$setAmountPo);
                    $setAmountPo = rtrim(rtrim(number_format($setAmountPo, 2, ".", ""), '0'), '.');
                   /**
                    * INITIAL VALUE 
                    **/
                    $sequence       = str_pad($poRunNo, 4, "0", STR_PAD_LEFT);
                    $poNo           = $poRunDate.$sequence;
                    $poIssuedDate   = (int)date(Y).date(m).date(d);
                        
                    /**
                    * INSERT EPS_T_PO_HEADER 
                    **/
                    $query_insert_po = "insert into
                                                EPS_T_PO_HEADER
                                                (
                                                    PO_NO
                                                    ,PO_STATUS
                                                    ,ISSUED_DATE
                                                    ,ISSUED_BY
                                                    ,SUPPLIER_CD
                                                    ,SUPPLIER_NAME
                                                    ,CURRENCY_CD
                                                    ,VAT
                                                    ,DELIVERY_DATE
                                                    ,DELIVERY_PLANT
                                                    ,COMPANY_CD
                                                    ,APPROVER
                                                    ,CREATE_DATE
                                                    ,CREATE_BY
                                                    ,UPDATE_DATE
                                                    ,UPDATE_BY
                                                )
                                            values
                                                (
                                                    '$poNo'
                                                    ,'1210'
                                                    ,'$poIssuedDate'
                                                    ,'$sUserId'
                                                    ,'$setSupplierCd'
                                                    ,'$setSupplierName'
                                                    ,'$setCurrencyCd'
                                                    ,'$setVat'
                                                    ,'$setDeliveryDate'
                                                    ,'$setPlantAlias'
                                                    ,'$setCompanyCd'
                                                    ,''
                                                    ,convert(VARCHAR(24), GETDATE(), 120)
                                                    ,'$sUserId'
                                                    ,convert(VARCHAR(24), GETDATE(), 120)
                                                    ,'$sUserId'
                                                )";
                    $conn->query($query_insert_po);
                    
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
                                                    ,CREATE_DATE
                                                    ,CREATE_BY
                                                    ,UPDATE_DATE
                                                    ,UPDATE_BY
                                                )
                                            values
                                                (
                                                    '$poNo'
                                                    ,'$setTransferId'
                                                    ,'$setItemCd'
                                                    ,'$setItemName'
                                                    ,'$setQtyPo'
                                                    ,'$setItemPrice'
                                                    ,'$setAmountPo'
                                                    ,'$setUnitCd'
                                                    ,'$setItemTypeCd'
                                                    ,'$setAccountNo'
                                                    ,'$setRfiNo'
                                                    ,convert(VARCHAR(24), GETDATE(), 120)
                                                    ,'$sUserId'
                                                    ,convert(VARCHAR(24), GETDATE(), 120)
                                                    ,'$sUserId'
                                                )";
                    $conn->query($query_insert_po_detail);
                    
					if($setQty != $setActualQty)
                    {
                        $newActualQty       = $setQtyPo + $setActualQty;
                        $newActualAmount   	= $newActualQty * $setItemPrice;
                        
                        $split_act_amount   = explode('.', $newActualAmount);
                        if($split_act_amount[1] == 0)
                        {
                            $newActualAmount = number_format($newActualAmount);
                        }
                        else
                        {
                            $newActualAmount = number_format($newActualAmount, 2);
                        }
                        $newActualAmount = str_replace(',', '',$newActualAmount);
                        $newActualAmount   = rtrim(rtrim(number_format($newActualAmount, 2, ".", ""), '0'), '.');
                        /**
                         * UPDATE EPS_T_TRANSFER 
                         **/
                        $query_update_transfer_by_qty = "update
                                                            EPS_T_TRANSFER
                                                        set
                                                            ACTUAL_QTY = '$newActualQty'
                                                            ,NEW_AMOUNT = '$newActualAmount'
                                                            ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                            ,UPDATE_BY = '$sUserId'
                                                        where
                                                            TRANSFER_ID = '$setTransferId'";
                        //$conn->query($query_update_transfer_by_qty);
                    }
					
                    /**
                     * UPDATE EPS_T_TRANSFER 
                     **/
                    $query_update_transfer = "update
                                                EPS_T_TRANSFER
                                              set
                                                ITEM_STATUS = '1270'
                                                ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                ,UPDATE_BY = '$sUserId'
                                              where
                                                TRANSFER_ID = '$setTransferId' 
                                              ";
                    $conn->query($query_update_transfer);
                }
            }
            $msg = 'Success';
        }
        
        if($action == 'UpdateWaitingPoNumber'){
            $getCurrentDate = date('d/m/Y');
            $transferIdSet  = strtoupper(trim($_GET['transferIdValPrm'])); 
            $itemStatusSet  = strtoupper($_GET['itemStatusValPrm']);
            $itemTypeSet    = strtoupper(trim($_GET['itemTypeValPrm'])); 
            $invNoSet       = trim($_GET['invNoValPrm']);
            $invsNoSet      = trim($_GET['invsNoValPrm']);
            $rfiNoSet       = strtoupper(trim($_GET['rfiNoValPrm']));
            $accountNoSet   = strtoupper(trim($_GET['accountNoValPrm']));
            $qtySet         = strtoupper($_GET['qtyValPrm']);
            $actualQtySet   = strtoupper($_GET['actualQtyValPrm']);
            $remainQtySet   = strtoupper($_GET['remainQtyValPrm']);
            $priceSet       = strtoupper(trim($_GET['priceValPrm']));
            $amountSet      = $priceSet * $actualQtySet;
			$split_amount   = explode('.', $amountSet);
            if($split_amount[1] == 0)
            {
                $amountSet = number_format($amountSet);
            }
            else
            {
                $amountSet = number_format($amountSet, 2);
            }
            $amountSet       = str_replace(',', '',$amountSet);
            $amountSet = rtrim(rtrim(number_format($amountSet, 2, ".", ""), '0'), '.');
            $deliveryDateSet= strtoupper(encodeDate($_GET['deliveryDateValPrm']));
            $remarkProcSet  = strtoupper(trim($_GET['remarkProcValPrm']));
            $remarkProcSet  = str_replace("'", "''", $remarkProcSet);
            
            if($itemTypeSet == '1' && $accountNoSet == '')
            {
                $msg = 'Mandatory_1';
            }
            else if($itemTypeSet == '2' && $rfiNoSet == '')
            {
                $msg = 'Mandatory_2';
            }
            else if(($itemTypeSet == '3' || $itemTypeSet == '5') && $invNoSet == '' )
            {
                $msg = 'Mandatory_6';
            }
            else if($itemTypeSet == '4' && $invsNoSet == '' )
            {
                $msg = 'Mandatory_7';
            }
            else if(strtotime(str_replace('/', '-', $deliveryDateSet)) < strtotime(str_replace('/', '-', $getCurrentDate)))
            {
                $msg = 'Mandatory_3';
            }
            else if($qtySet <= 0 || $amountSet <= 0)
            {
                $msg = 'Mandatory_4';
            }
            else if($remarkProcSet == '' && $itemStatusSet == '1150')
			{
                $msg = 'Mandatory_5';
            }
            else if(($prCompanyCd == "D" ||  $prCompanyCd == "S" ) && !preg_match("/^[0-9]{2}-[0-9]{3}$/", $rfiNoSet) && $rfiNoSet != '')
            {           
                $msg = 'Mandatory_9';
            }
            else if($prCompanyCd == "H" && !preg_match("/^[0-9]{3}-[0-9]{2}$/", $rfiNoSet) && $rfiNoSet != '')
            {           
                $msg = 'Mandatory_9';
            }
            else{
                
				unset($_SESSION['itemStatusSession']);
				
                if($itemTypeSet == '3' || $itemTypeSet == '5'){
                    $accountNoSet = $invNoSet;
                }
                if($itemTypeSet == '4'){
                    $accountNoSet = $invsNoSet;
                }
                /**
                 * UPDATE in EPS_T_TRANSFER
                 */
                $query_update_transfer = "update
                                            EPS_T_TRANSFER
                                        set
                                            ITEM_STATUS = '$itemStatusSet'
                                            ,NEW_DELIVERY_DATE = '$deliveryDateSet'
                                            ,NEW_QTY = '$qtySet'
                                            ,ACTUAL_QTY = '$actualQtySet'
                                            ,NEW_AMOUNT = '$amountSet'
                                            ,NEW_ITEM_TYPE_CD = '$itemTypeSet'
                                            ,NEW_ACCOUNT_NO = '$accountNoSet'
                                            ,NEW_RFI_NO = '$rfiNoSet'
                                            ,CANCEL_REMARK = '$remarkProcSet'
                                            ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                            ,UPDATE_BY = '$sUserId'
                                        where
                                            TRANSFER_ID = '$transferIdSet'";
                $sql_update_transfer = $conn->query($query_update_transfer);
                $msg = 'Success';
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
