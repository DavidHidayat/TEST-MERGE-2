<?php session_start(); 
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Constant.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Common.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Email/PR_EMAIL.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Email/PO_EMAIL.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/lib/mail_lib/crypt.php";
require $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/')).'/lib/mail_lib/class.phpmailer.php';
require $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/')).'/lib/mail_lib/class.smtp.php';
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
            
        if($action == 'SaveItem')
		{
            $itemSupplier       = array();
            $itemSupplierTemp   = array();
            $getCurrentDate     = date('d/m/Y');
            
            $transferId     = strtoupper(trim($_GET['transferIdValPrm']));
            $prNo           = strtoupper(trim($_GET['prNoValPrm']));
			
			$prItemPrice    = strtoupper(trim($_GET['prItemPricePrm']));
            $split_pr_price   = explode('.', $prItemPrice);
            if($split_pr_price[1] == 0)
            {
                $prItemPrice = number_format($prItemPrice);
            }
            else
            {
                $prItemPrice = number_format($prItemPrice, 2);
            }
            $prItemPrice      = str_replace(',', '',$prItemPrice);
            $prItemPrice      = rtrim(rtrim(number_format($prItemPrice, 2, ".", ""), '0'), '.');
			
            $itemTypeSet    = strtoupper(trim($_GET['itemTypeValPrm'])); 
            $rfiNoSet       = strtoupper(trim($_GET['rfiNoValPrm']));
            $accountNoSet   = strtoupper(trim($_GET['accountNoValPrm']));
            $invNoSet       = trim($_GET['invNoValPrm']);
            $invsNoSet      = trim($_GET['invsNoValPrm']);
            $unitCdSet      = strtoupper($_GET['umValPrm']);
            $qtySet         = strtoupper($_GET['qtyValPrm']);
            $actualQty      = strtoupper($_GET['actualQtyValPrm']);
            $remainQty      = strtoupper($_GET['remainQtyValPrm']);
            $deliveryDateSet= strtoupper(encodeDate($_GET['deliveryDateValPrm']));
            $itemStatusSet  = strtoupper($_GET['itemStatusValPrm']);
            $itemCdSet      = strtoupper(trim($_GET['itemCdValPrm']));
            $itemNameSet    = strtoupper(trim($_GET['itemNameValPrm']));
            $itemNameSet    = stripslashes($itemNameSet);
            $itemNameSet    = str_replace("'", "''", $itemNameSet);
            $itemNameSet    = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $itemNameSet);
            $itemNameSet    = preg_replace('/\s+/', ' ',$itemNameSet);
            $supplierCdSet  = strtoupper(trim($_GET['supplierCdValPrm']));
            $supplierNameSet= strtoupper(trim($_GET['supplierNameValPrm']));
            $currencyCdSet  = strtoupper(trim($_GET['currencyCdValPrm']));
            $priceSet       = strtoupper(trim($_GET['priceValPrm']));
            $split_price    = explode('.', $priceSet);
            if($split_price[1] == 0)
            {
                $priceSet = number_format($priceSet);
            }
            else
            {
                $priceSet = number_format($priceSet, 2);
            }
            $priceSet       = str_replace(',', '',$priceSet);
            $remarkProcSet  = strtoupper(trim($_GET['remarkProcValPrm']));
            $remarkProcSet  = str_replace("'", "''", $remarkProcSet);
            $amountSet      = $priceSet * $actualQty;
            $split_amount   = explode('.', $amountSet);
            if($split_amount[1] == 0)
            {
                $amountSet = number_format($amountSet);
            }
            else
            {
                $amountSet = number_format($amountSet, 2);
            }
            $amountSet      = str_replace(',', '',$amountSet);
            $amountSet 		= rtrim(rtrim(number_format($amountSet, 2, ".", ""), '0'), '.');
            $itemSupplier   = ($_SESSION['transferSupplier']);
            $msg            = '';
            $validateSupplier= 0;
            $validatePrice  = 0;
            $validateSet    = 0;
            $updateDate     = strtoupper(trim($_GET['updateDatePrm']));
            
            /**
             * 
             */
            //tadinya 20 diganti 100 request by karyoto
            $addPrItemPrice = (20 * $prItemPrice) / 100;
            $split_add   = explode('.', $addPrItemPrice);
            if($split_add[1] == 0)
            {
                $addPrItemPrice = number_format($addPrItemPrice);
            }
            else
            {
                $addPrItemPrice = number_format($addPrItemPrice, 2);
            }
            $addPrItemPrice      = str_replace(',', '',$addPrItemPrice);
            $addPrItemPrice      = rtrim(rtrim(number_format($addPrItemPrice, 2, ".", ""), '0'), '.');
            $limitPrItemPrice    = $prItemPrice + $addPrItemPrice;
			
            /**
             * Select EPS_M_APP_STATUS
             */
            $query_select_t_transfer = "select 
                                            EPS_T_TRANSFER.ITEM_STATUS
                                            ,EPS_T_TRANSFER.NEW_QTY
                                            ,EPS_T_TRANSFER.ACTUAL_QTY
                                            ,EPS_T_PR_HEADER.COMPANY_CD
                                            ,EPS_T_PR_HEADER.CHARGED_BU_CD
                                            , EPS_M_BUNIT.COMPANY_CD as COMPANY_CD_CHARGED
                                        from
                                            EPS_T_TRANSFER
                                        left join
                                            EPS_T_PR_HEADER 
                                        on 
                                            EPS_T_TRANSFER.PR_NO = EPS_T_PR_HEADER.PR_NO
                                        left join
                                            EPS_M_BUNIT 
                                        on 
                                            EPS_T_PR_HEADER.CHARGED_BU_CD = EPS_M_BUNIT.BU_CD
                                        where
                                            EPS_T_TRANSFER.TRANSFER_ID = '$transferId' ";
            $sql_select_t_transfer = $conn->query($query_select_t_transfer);
            $row_select_t_transfer = $sql_select_t_transfer->fetch(PDO::FETCH_ASSOC);
            $currentItemStatus  = $row_select_t_transfer['ITEM_STATUS'];
            $currentNewQty      = $row_select_t_transfer['NEW_QTY'];
            $currentActualQty   = $row_select_t_transfer['ACTUAL_QTY'];
            $currentRemainQty   = $currentNewQty - $currentActualQty;
            $prCompanyCd        = $row_select_t_transfer['COMPANY_CD'];
            $chargedCompanyCd   = $row_select_t_transfer['COMPANY_CD_CHARGED'];
            
            if($itemNameSet == '')
            {
                $msg = 'Mandatory_1';
            }
            else if($itemTypeSet == '1' && $accountNoSet == '')
            {
                $msg = 'Mandatory_2';
            }
            else if($itemTypeSet == '2' && $rfiNoSet == '')
            {
                $msg = 'Mandatory_3';
            }
            else if(($itemTypeSet == '3' || $itemTypeSet == '5') && $invNoSet == '' )
            {
                $msg = 'Mandatory_4';
            }
            else if($itemTypeSet == '4' && $invsNoSet == '')
            {
                $msg = 'Mandatory_5';
            }
            else if($itemStatusSet != '1150' 
                    && strtotime(str_replace('/', '-', $deliveryDateSet)) < strtotime(str_replace('/', '-', $getCurrentDate)))
            {
                $msg = 'Mandatory_6';
            }
            else if($remarkProcSet == '' && $itemStatusSet == '1150')
            {
                $msg = 'Mandatory_7';
            }
            else if($qtySet <= 0 || $amountSet <= 0)
            {
                $msg = 'Mandatory_8';
            }
            
            //Sementara dimatikan Request By karyoto
//            else if($currencyCdSet == "IDR" && $priceSet > $limitPrItemPrice 
//                    && ($sRoleId == 'ROLE_02' || $sRoleId == 'ROLE_04' || $sRoleId == 'ROLE_05' 
//                            || $sRoleId == 'ROLE_07' || $sRoleId == 'ROLE_08' || $sRoleId == 'ROLE_09' || $sRoleId == 'ROLE_11'))
//            {
//                $msg = 'Mandatory_17';
//            }
            else if($itemStatusSet == '1160' && $remainQty < $currentRemainQty)
            {
                $msg = 'Mandatory_9';
            }
            else if(($itemStatusSet == '1130' || $itemStatusSet == '1170') && count($itemSupplier) <= 0 && $itemCdSet == '99')
            {
                $msg = 'Mandatory_10';
            }
            else if(($itemStatusSet == '1130' || $itemStatusSet == '1170') && $supplierCdSet == '')
            {
                $msg = 'Mandatory_11';
            }
//            else if(($chargedCompanyCd == "D" ||  $chargedCompanyCd == "S" ) && !preg_match("/^[0-9]{2}-[0-9]{3}$/", $rfiNoSet) && $rfiNoSet != '')
//            {           
//                $msg = 'Mandatory_15';
//            }
//            //RUBAH CHARGED COMPANY CODE DARI H KE T : TGL 9/1/2020
//            else if($chargedCompanyCd == "T" && !preg_match("/^[0-9]{3}-[0-9]{2}$/", $rfiNoSet) && $rfiNoSet != '')
//            {           
//                $msg = 'Mandatory_15';
//            }
            else{
                /**
                 * Check existing supplier in array
                 */
                for($i = 0; $i < count($itemSupplier); $i++){
                    $supplierCdVal  = $itemSupplier[$i]['supplierCd'];
                    $itemPriceVal   = $itemSupplier[$i]['itemPrice'];
                    //$itemPriceVal   = number_format($itemPriceVal);
                    //$itemPriceVal   = str_replace(',', '',$itemPriceVal);
                    
                    if($supplierCdVal == $supplierCdSet){
                        $validateSupplier++;
                        if($itemPriceVal == $priceSet)
                        {
                            $validateSet++;
                            
                        }
                    }
                }
                if(($itemStatusSet == '1130' || $itemStatusSet == '1170') && $supplierCdSet != '' && $validateSupplier != 1 && $itemCdSet == '99')
                {
                    $msg = 'Mandatory_12';
                }
                else if(($itemStatusSet == '1130' || $itemStatusSet == '1170') && $priceSet != '' && $validateSet != 1 && $itemCdSet == '99')
                {
                    $msg = "Mandatory_13";
                }
                else
                {
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
                                                ,NEW_ITEM_CD = '$itemCdSet'
                                                ,NEW_ITEM_NAME = '$itemNameSet'
                                                ,NEW_DELIVERY_DATE = '$deliveryDateSet'
                                                ,NEW_QTY = '$qtySet'
                                                ,ACTUAL_QTY = '$actualQty'
                                                ,NEW_ITEM_PRICE = '$priceSet'
                                                ,NEW_AMOUNT = '$amountSet'
                                                ,NEW_CURRENCY_CD = '$currencyCdSet'
                                                ,NEW_ITEM_TYPE_CD = '$itemTypeSet'
                                                ,NEW_ACCOUNT_NO = '$accountNoSet'
                                                ,NEW_RFI_NO = '$rfiNoSet'
                                                ,NEW_UNIT_CD = '$unitCdSet'
                                                ,NEW_SUPPLIER_CD = '$supplierCdSet'
                                                ,NEW_SUPPLIER_NAME = '$supplierNameSet'
                                                ,CANCEL_REMARK = '$remarkProcSet'
                                                ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                ,UPDATE_BY = '$sUserId'
                                            where
                                                TRANSFER_ID = '$transferId'";
                    $sql_update_transfer = $conn->query($query_update_transfer);
					
                    /**
                     * DELETE in EPS_T_TRANSFER_SUPPLIER
                     */
                    $query_del_transfer_supplier = 
                                 "delete from
                                    EPS_T_TRANSFER_SUPPLIER
                                  where
                                    TRANSFER_ID = '$transferId'";
                    $sql_del_transfer_supplier = $conn->query($query_del_transfer_supplier);

                    for($j = 0; $j < count($itemSupplier); $j++){
                            $transferIdItem = $itemSupplier[$j]['transferId'];
                            $supplierCd     = $itemSupplier[$j]['supplierCd'];
                            $supplierName   = $itemSupplier[$j]['supplierName'];
                            $currencyCd     = $itemSupplier[$j]['currencyCd'];
                            $itemPrice      = $itemSupplier[$j]['itemPrice'];
                            $leadTime       = $itemSupplier[$j]['leadTime'];
                            $unitTime       = $itemSupplier[$j]['unitTime'];
                            $attachmentLoc  = $itemSupplier[$j]['attachmentLoc'];
                            $attachmentCip  = $itemSupplier[$j]['attachmentCip'];
                            $remark         = trim($itemSupplier[$j]['remark']);
                            $seqSupplier    = $itemSupplier[$j]['seqSupplier'];
                            
                            /**
                             * INSERT in EPS_T_TRANSFER_SUPPLIER
                             */
                            $query_insert_transfer_supplier = 
                                    "insert into 
                                        EPS_T_TRANSFER_SUPPLIER
                                        (
                                            TRANSFER_ID
                                            ,SEQ_ID
                                            ,SUPPLIER_CD
                                            ,SUPPLIER_NAME
                                            ,CURRENCY_CD
                                            ,ITEM_PRICE
                                            ,LEAD_TIME
                                            ,UNIT_TIME
                                            ,ATTACHMENT_LOC
                                            ,REMARK
                                            ,CREATE_DATE
                                            ,CREATE_BY
                                            ,UPDATE_DATE
                                            ,UPDATE_BY
                                            ,ATTACHMENT_CIP
                                        )
                                    values
                                        (
                                            '$transferIdItem'
                                            ,'$seqSupplier'
                                            ,'$supplierCd'
                                            ,'$supplierName'
                                            ,'$currencyCd'
                                            ,'$itemPrice'
                                            ,'$leadTime'
                                            ,'$unitTime'
                                            ,'$attachmentLoc'
                                            ,'$remark'
                                            ,convert(VARCHAR(24), GETDATE(), 120)
                                            ,'$sUserId'
                                            ,convert(VARCHAR(24), GETDATE(), 120)
                                            ,'$sUserId'
                                            ,'$attachmentCip'
                                        )";
                            $sql_insert_transfer_supplier = $conn->query($query_insert_transfer_supplier);
                    }
                    
                    /**
                     * SEND MAIL TO REQUESTER
                     */
                    if($itemStatusSet == '1150')
                    {
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
                        
                        /**
                         * SELECT EPS_T_TRANSFER
                         */
                        $query_select_t_transfer = "select
                                                        EPS_T_TRANSFER.ITEM_NAME
                                                        ,EPS_T_TRANSFER.NEW_ITEM_NAME
                                                        ,substring(EPS_T_TRANSFER.NEW_DELIVERY_DATE, 7, 2) 
                                                        + '/' + substring(EPS_T_TRANSFER.NEW_DELIVERY_DATE, 5, 2) 
                                                        + '/' + substring(EPS_T_TRANSFER.NEW_DELIVERY_DATE, 1, 4) as NEW_DELIVERY_DATE 
                                                        ,EPS_T_TRANSFER.NEW_QTY
                                                        ,EPS_T_TRANSFER.NEW_ITEM_PRICE
                                                        ,EPS_T_TRANSFER.NEW_AMOUNT
                                                        ,EPS_T_TRANSFER.NEW_CURRENCY_CD
                                                        ,EPS_T_TRANSFER.NEW_ITEM_TYPE_CD
                                                        ,EPS_T_TRANSFER.NEW_ACCOUNT_NO
                                                        ,EPS_T_TRANSFER.NEW_RFI_NO
                                                        ,EPS_T_TRANSFER.NEW_UNIT_CD
                                                        ,EPS_T_TRANSFER.NEW_SUPPLIER_NAME
                                                        ,EPS_T_TRANSFER.NEW_REMARK
                                                        ,EPS_M_ACCOUNT.ACCOUNT_CD
                                                    from
                                                        EPS_T_TRANSFER
                                                    left join
                                                        EPS_M_ACCOUNT
                                                    on
                                                        EPS_T_TRANSFER.NEW_ACCOUNT_NO = EPS_M_ACCOUNT.ACCOUNT_NO
                                                    where
                                                        TRANSFER_ID = '$transferId'";
                        $sql_select_t_transfer = $conn->query($query_select_t_transfer);
                        $row_select_t_transfer = $sql_select_t_transfer->fetch(PDO::FETCH_ASSOC);
                        $itemNameTransfer       = $row_select_t_transfer['ITEM_NAME'];
                        $newItemNameTransfer    = $row_select_t_transfer['NEW_ITEM_NAME'];
                        $deliveryDateTransfer   = $row_select_t_transfer['NEW_DELIVERY_DATE'];
                        $qtyTransfer            = $row_select_t_transfer['NEW_QTY'];
                        $itemPriceTransfer      = $row_select_t_transfer['NEW_ITEM_PRICE'];
                        $amountTransfer         = $row_select_t_transfer['NEW_AMOUNT'];
                        $currencyCdTransfer     = $row_select_t_transfer['NEW_CURRENCY_CD'];
                        $itemTypeCdTransfer     = $row_select_t_transfer['NEW_ITEM_TYPE_CD'];
                        $accountTransfer        = $row_select_t_transfer['NEW_ACCOUNT_NO'];
                        $rfiNoTransfer          = $row_select_t_transfer['NEW_RFI_NO'];
                        $unitCdTransfer         = $row_select_t_transfer['NEW_UNIT_CD'];
                        $supplierNameTransfer   = $row_select_t_transfer['NEW_SUPPLIER_NAME'];
                        $remarkTransfer         = $row_select_t_transfer['NEW_REMARK'];
                        $accountCdTransfer      = $row_select_t_transfer['ACCOUNT_CD'];
						
                        if($itemTypeCdTransfer == 1)
                        {
                            $itemTypeCdTransfer = 'EXP';
                            $accountTransfer = $accountCdTransfer;
                        }
                        if($itemTypeCdTransfer == 2)
                        {
                            $itemTypeCdTransfer = 'RFI';
                        }
                        if($itemTypeCdTransfer == 3 || $itemTypeCdTransfer == 4 || $itemTypeCdTransfer == 5)
                        {
                            $itemTypeCdTransfer = 'INV';
                            $accountTransfer = $accountCdTransfer;
                        }
                        
						$split_qty = explode('.', $qtyTransfer);
                        if($split_qty[1] == 0)
                        {
                            $qtyTransfer = number_format($qtyTransfer);
                        }
                        
                        $split_item_price = explode('.', $itemPriceTransfer);
                        if($split_item_price[1] == 0)
                        {
                            $itemPriceTransfer = number_format($itemPriceTransfer);
                        }
                        else
                        {
                            $itemPriceTransfer = number_format($itemPriceTransfer, 2);
                        }
                         
                        $split_item_amount = explode('.', $amountTransfer);
                        if($split_item_amount[1] == 0)
                        {
                            $amountTransfer = number_format($amountTransfer);
                        }
                        else
                        {
                            $amountTransfer = number_format($amountTransfer, 2);
                        }
						
                        /**
                         * SELECT EPS_T_PR_DETAIL
                         */
                        $query_select_t_pr_detail = "select 
                                                        EPS_T_PR_DETAIL.ITEM_NAME
                                                        ,substring(EPS_T_PR_DETAIL.DELIVERY_DATE, 7, 2) 
                                                        + '/' + substring(EPS_T_PR_DETAIL.DELIVERY_DATE, 5, 2) 
                                                        + '/' + substring(EPS_T_PR_DETAIL.DELIVERY_DATE, 1, 4) as DELIVERY_DATE 
                                                        ,EPS_T_PR_DETAIL.QTY
                                                        ,EPS_T_PR_DETAIL.ITEM_PRICE
                                                        ,EPS_T_PR_DETAIL.AMOUNT
                                                        ,EPS_T_PR_DETAIL.CURRENCY_CD
                                                        ,EPS_T_PR_DETAIL.ITEM_TYPE_CD
                                                        ,EPS_T_PR_DETAIL.ACCOUNT_NO
                                                        ,EPS_T_PR_DETAIL.RFI_NO
                                                        ,EPS_T_PR_DETAIL.UNIT_CD
                                                        ,EPS_T_PR_DETAIL.SUPPLIER_NAME
                                                        ,EPS_T_PR_DETAIL.REMARK
                                                        ,EPS_M_ACCOUNT.ACCOUNT_CD
                                                    from 
                                                        EPS_T_PR_DETAIL
                                                    left join
                                                        EPS_M_ACCOUNT
                                                    on
                                                        EPS_T_PR_DETAIL.ACCOUNT_NO = EPS_M_ACCOUNT.ACCOUNT_NO
                                                    where
                                                        EPS_T_PR_DETAIL.PR_NO = '$prNo'
                                                        and replace(replace(replace(EPS_T_PR_DETAIL.ITEM_NAME, char(13), ''), char(9), ''), ' ', '') = replace('$itemNameTransfer', ' ', '')";
                        $sql_select_t_pr_detail = $conn->query($query_select_t_pr_detail);
                        $row_select_t_pr_detail = $sql_select_t_pr_detail->fetch(PDO::FETCH_ASSOC);
                        $itemNamePrDetail       = $row_select_t_pr_detail['ITEM_NAME'];
                        $deliveryDatePrDetail   = $row_select_t_pr_detail['DELIVERY_DATE'];
                        $qtyPrDetail            = $row_select_t_pr_detail['QTY'];
                        $itemPricePrDetail      = $row_select_t_pr_detail['ITEM_PRICE'];
                        $amountPrDetail         = $row_select_t_pr_detail['AMOUNT'];
                        $currencyCdPrDetail     = $row_select_t_pr_detail['CURRENCY_CD'];
                        $itemTypeCdPrDetail     = $row_select_t_pr_detail['ITEM_TYPE_CD'];
                        $accountPrDetail        = $row_select_t_pr_detail['ACCOUNT_NO'];
                        $rfiNoPrDetail          = $row_select_t_pr_detail['RFI_NO'];
                        $unitCdPrDetail         = $row_select_t_pr_detail['UNIT_CD'];
                        $supplierNamePrDetail   = $row_select_t_pr_detail['SUPPLIER_NAME'];
                        $remarkPrDetail         = $row_select_t_pr_detail['REMARK'];
                        $accountCdPrDetail      = $row_select_t_transfer['ACCOUNT_CD'];
						
                        if($itemTypeCdPrDetail == 1)
                        {
                            $itemTypeCdPrDetail = 'EXP';
                            $accountPrDetail = $accountCdPrDetail;
                        }
                        if($itemTypeCdPrDetail == 2)
                        {
                            $itemTypeCdPrDetail = 'RFI';
                        }
                        if($itemTypeCdTransfer == 3 || $itemTypeCdTransfer == 4 || $itemTypeCdTransfer == 5)
                        {
                            $itemTypeCdPrDetail = 'INV';
                            $accountPrDetail = $accountCdPrDetail;
                        }
                        /**********************************************************************
                         * SEND MAIL
                         **********************************************************************/
                        $mailFrom       = $sInet;
                        $mailFromName   = $sNotes;  
                        
                        $query_select_m_app_sts = "select 
                                                    APP_STATUS_NAME
                                                   from
                                                    EPS_M_APP_STATUS
                                                   where 
                                                    APP_STATUS_CD = '$itemStatusSet'";
                        $sql_select_m_app_sts = $conn->query($query_select_m_app_sts);
                        $row_select_m_app_sts = $sql_select_m_app_sts->fetch(PDO::FETCH_ASSOC);
                        $appStsName = $row_select_m_app_sts['APP_STATUS_NAME'];
                        
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
                            //$mailTo             = 'muh.iqbal@taci.toyota-industries.com';
                            $passwordRequester  = $row_select_dscid['PASSWORD'];
                            $getParamLink       = paramEncrypt("action=open&prNo=$prNo&userId=$prUserId&password=$passwordRequester");
                            $mailSubject = "ITEM CANCELED (by Procurement). PR No: ".$prNo;
                            $mailMessage = "<table style='font-family: Arial; font-size: 12px;'>";
                            $mailMessage .= "<tr><td>PR No</td><td>: </td><td>".$prNo."</td></tr>";
                            $mailMessage .= "<tr><td>Item Status</td><td>:</td><td>".$appStsName."</td></tr>";
                            $mailMessage .= "<tr><td>Remark from Procurement</td><td>:</td><td>".$remarkProcSet."</td></tr>";
                            $mailMessage .= "</table><br>";
                            $mailMessage .= "<table style='font-family: Arial; font-size: 12px; border: 1px solid #000000; padding:5px;'>";
                            $mailMessage .= "<tr style='font-weight: bold; text-align: center;'>
                                                <td width= 30px align=center>NO.</td>
                                                <td width= 110px align=center>DESCRIPTION</td>
                                                <td width= 360px align=center>REQUESTER</td>
                                                <td width= 360px align=center>PROCUREMENT</td>
                                             </tr>
                                             <tr>
                                                <td style='text-align: right;'>1.</td>
                                                <td>ITEM NAME</td>
                                                <td>$itemNamePrDetail</td>
                                                <td>$newItemNameTransfer</td>
                                             </tr>
                                             <tr>
                                                <td style='text-align: right;'>2.</td>
                                                <td>DUE DATE</td>
                                                <td>$deliveryDatePrDetail</td>
                                                <td>$deliveryDateTransfer</td>
                                             </tr>
                                             <tr>
                                                <td style='text-align: right;'>3.</td>
                                                <td>TYPE</td>
                                                <td>$itemTypeCdPrDetail</td>
                                                <td>$itemTypeCdTransfer</td>
                                             </tr>
                                             <tr>
                                                <td style='text-align: right;'>4.</td>
                                                <td>RFI</td>
                                                <td>$rfiNoPrDetail</td>
                                                <td>$rfiNoTransfer</td>
                                             </tr>
                                             <tr>
                                                <td style='text-align: right;'>5.</td>
                                                <td>EXP</td>
                                                <td>$accountPrDetail</td>
                                                <td>$accountTransfer</td>
                                             </tr>
                                             <tr>
                                                <td style='text-align: right;'>6.</td>
                                                <td>U M</td>
                                                <td>$unitCdPrDetail</td>
                                                <td>$unitCdTransfer</td>
                                             </tr>
                                             <tr>
                                                <td style='text-align: right;'>7.</td>
                                                <td>QTY</td>
                                                <td>$qtyPrDetail</td>
                                                <td>$qtyTransfer</td>
                                             </tr>
                                             <tr>
                                                <td style='text-align: right;'>8.</td>
                                                <td>PRICE</td>
                                                <td>".number_format($itemPricePrDetail)." (".$currencyCdPrDetail.")</td>
                                                <td>".$itemPriceTransfer." (".$currencyCdTransfer.")</td>
                                             </tr>
                                             <tr>
                                                <td style='text-align: right;'>9.</td>
                                                <td>AMOUNT</td>
                                                <td>".number_format($amountPrDetail)."</td>
                                                <td>".$amountTransfer."</td>
                                             </tr>
                                             <tr>
                                                <td style='text-align: right;'>10.</td>
                                                <td>REMARK</td>
                                                <td>$remarkPrDetail</td>
                                                <td>$remarkTransfer</td>
                                             </tr></table>";
                            //$mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage
                            prSendMail($prNo, $mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage);
                        }
                    }
                    $msg = 'Success';
                }
            }
        }
		
		if($action == 'UpdateItemInCharge')
        {
            $transferId         = strtoupper(trim($_GET['transferIdPrm']));
            $newProcInCharge    = strtoupper($_GET['newProcInChargePrm']);
            $updateDate         = strtoupper(trim($_GET['updateDatePrm']));
            
            $query_eps_t_transfer = "select
                                        UPDATE_DATE
                                     from 
                                        EPS_T_TRANSFER
                                     where
                                        TRANSFER_ID = '$transferId'";
            $sql_eps_t_transfer = $conn->query($query_eps_t_transfer);
            $row_eps_t_transfer =$sql_eps_t_transfer->fetch(PDO::FETCH_ASSOC);
            $newUpdateDate = strtoupper($row_eps_t_transfer['UPDATE_DATE']);
         
            if($updateDate == $newUpdateDate)
            {
                unset($_SESSION['updateDateSession']);
                
                $query_update_t_transfer = "update
                                                EPS_T_TRANSFER
                                            set
                                                CREATE_BY = '$newProcInCharge'
                                                ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                ,UPDATE_BY = '$sUserId'
                                            where
                                                TRANSFER_ID = '$transferId'";
                $conn->query($query_update_t_transfer);
                $msg = 'Success';
            }
            else if($updateDate != $newUpdateDate)
            {
                $msg = 'Mandatory_16';
            }
            else
            {
                $msg = 'UndefinedError';
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