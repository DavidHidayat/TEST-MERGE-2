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
            
        if($action == 'SavePo')
        {
            $getCurrentDate     = date('d/m/Y');
            $poNo               = trim($_GET['poNoPrm']);
            $deliveryDate       = encodeDate($_GET['deliveryDatePrm']);
            $addRemark          = strtoupper(trim($_GET['addRemarkPrm']));
            $addRemark          = str_replace("'", "''", $addRemark);
            $npkApproverArray   = $_GET['npkApproverArray'];
            $newNpkApproverArray= explode(",", $npkApproverArray);
            $bypassApproverArray= $_GET['bypassApproverArray'];
            $approverNo         = 1;
            $poItemData         = ($_SESSION['poDetail']);
            
			/**
             * SELECT EPS_T_PO_HEADER
             */
            $query_select_t_po_header = "select 
                                            CURRENCY_CD
                                         from
                                            EPS_T_PO_HEADER
                                         where
                                            PO_NO = '$poNo'";
            $sql_select_t_po_header = $conn->query($query_select_t_po_header);
            $row_select_t_po_header = $sql_select_t_po_header->fetch(PDO::FETCH_ASSOC);
            $currencyCd             = $row_select_t_po_header['CURRENCY_CD'];
            
            if(strtotime(str_replace('/', '-', $deliveryDate)) < strtotime(str_replace('/', '-', $getCurrentDate)))
            {
                $msg = 'Mandatory_1';
            }
            else if($currencyCd != 'IDR' && $addRemark == '')
            {
                $msg = 'Mandatory_6';
            }
            else
            {
                /**
                 * Unset SESSION 
                 **/
                unset($_SESSION['poStatus']);
                
                /**
                 * UPDATE EPS_T_PO_HEADER
                 */ 
                $query_update_po = "update
                                        EPS_T_PO_HEADER
                                    set
                                        DELIVERY_DATE = '$deliveryDate'
                                        ,ADDITIONAL_REMARK = '$addRemark'
                                        ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                        ,UPDATE_BY = '$sUserId'
                                    where
                                        PO_NO = '$poNo'";
                $conn->query($query_update_po);

                /**
                * UPDATE EPS_T_PO_DETAIL
                */
                for($j = 0; $j < count($poItemData); $j++){
                    $poNoVal        = $poItemData[$j]['poNo'];
                    $transferIdVal  = $poItemData[$j]['refTransferId'];
                    $itemCdVal      = $poItemData[$j]['itemCd'];
                    $itemNameVal    = $poItemData[$j]['itemName'];
                    $itemNameVal    = str_replace("'", "''", $itemNameVal);
                    $itemNameVal    = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $itemNameVal);
                    $itemNameVal    = preg_replace('/\s+/', ' ',$itemNameVal);
                    $qtyVal         = $poItemData[$j]['qty'];
                    $itemPriceVal   = $poItemData[$j]['itemPrice'];
                    $amountVal      = $poItemData[$j]['amount'];
                    $unitCdVal      = $poItemData[$j]['unitCd'];
                    $itemStatusVal  = $poItemData[$j]['itemStatus'];
                    $cipVal  = $poItemData[$j]['cip'];
                    $newQtyPo       = $qtyVal;
				
					$query_select_t_po_detail = "select
                                                    QTY
                                                from
                                                    EPS_T_PO_DETAIL
                                                where
                                                    REF_TRANSFER_ID = '$transferIdVal'
                                                    and PO_NO = '$poNo'";
                    $sql_select_t_po_detail = $conn->query($query_select_t_po_detail);
                    $row_select_t_po_detail = $sql_select_t_po_detail->fetch(PDO::FETCH_ASSOC);
                    $initialQtyPo           = $row_select_t_po_detail['QTY'];
					
					/*if($initialQtyPo != $qtyVal)
                    {
                        $itemStatusVal  = '1160';
                        $newQtyPo       = $qtyVal;
                        /**
                         * SELECT EPS_T_TRANSFER
                         **/ 
                        /*$query_select_t_transfer = "select
                                                        ACTUAL_QTY
                                                    from
                                                        EPS_T_TRANSFER
                                                    where
                                                        TRANSFER_ID = '$transferIdVal'";
                        $sql_select_t_transfer = $conn->query($query_select_t_transfer);
                        $row_select_t_transfer = $sql_select_t_transfer->fetch(PDO::FETCH_ASSOC);
                        $initialQtyTransfer = $row_select_t_transfer['ACTUAL_QTY'];
                        
                        $newActualQtyTransfer       = ($initialQtyTransfer - $initialQtyPo) + $qtyVal;
                        $newActualQtyTransfer       = number_format($newActualQtyTransfer);
                        $newActualQtyTransfer       = str_replace(',', '',$newActualQtyTransfer);
                        $newActualAmountTransfer    = $newActualQtyTransfer * $itemPriceVal;
                    }
                    else
                    {
                        $newQtyPo                   = $qtyVal;
                        $newActualQtyTransfer       = $qtyVal;
                        $newActualQtyTransfer       = number_format($newActualQtyTransfer);
                        $newActualQtyTransfer       = str_replace(',', '',$newActualQtyTransfer);
                        $newActualAmountTransfer    = $newActualQtyTransfer * $itemPriceVal;
                    }*/
					
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
                    $initialQtyTransfer = $row_select_t_transfer['NEW_QTY'];
                    $actualQtyTransfer  = $row_select_t_transfer['ACTUAL_QTY']; 
                    
					/**
                     * SELECT EPS_T_PO_DETAIL
                     **/ 
					$query_select_sum_t_po_detail = "select
                                                        sum(QTY) as ACTUAL_QTY
                                                    from         
                                                        EPS_T_PO_DETAIL
                                                    left join
                                                        EPS_T_PO_HEADER
                                                    on 
                                                        EPS_T_PO_HEADER.PO_NO = EPS_T_PO_DETAIL.PO_NO
                                                    where     
                                                        EPS_T_PO_DETAIL.REF_TRANSFER_ID = '$transferIdVal'
                                                        and EPS_T_PO_HEADER.PO_STATUS in ('1210','1220','1230','1250','1280','1330','1370')";
                    $sql_select_sum_t_po_detail = $conn->query($query_select_sum_t_po_detail);
                    $row_select_sum_t_po_detail = $sql_select_sum_t_po_detail->fetch(PDO::FETCH_ASSOC);
                    $actualPoQty = $row_select_sum_t_po_detail['ACTUAL_QTY'];
                    
					if($itemStatusVal == '1130' && $initialQtyTransfer != $actualQtyTransfer)
                    {
                        $newActualQtyTransfer = $actualPoQty - $initialQtyPo;
                    }
                    else
                    {
                        $newActualQtyTransfer = ($actualPoQty - $initialQtyPo) + $qtyVal;
                    }

                    if($itemStatusVal == '1130'){
                        $query_delete_po_detail = "delete from
                                                        EPS_T_PO_DETAIL
                                                    where
                                                        PO_NO = '$poNoVal'
                                                        and REF_TRANSFER_ID = '$transferIdVal'";
                        $conn->query($query_delete_po_detail);
                    }
                    
                    /**
                     * UPDATE PROGRAM FOR PARTIAL QTY ISSUE
                     * UPDATED BY   : BYAN PURBAPRANIDHANA
                     * UPDATE DATE  : SEP 29, 2015. 16.00
                     * UPDATE DATE  : NOV 8, 2016. 18.40
                     **/
                    if($initialQtyPo != $qtyVal || $initialQtyTransfer != $newActualQtyTransfer)
                    {
                        $itemStatusVal  = '1170';
                    }
                    
                     /**if($initialQtyTransfer == $actualQtyTransfer)
                    {
                        $newActualQtyTransfer = $qtyVal;
                    }
                    else
                    {
                        $newActualQtyTransfer = ($actualPoQty - $initialQtyPo) + $qtyVal;
                    }**/
                    //$newActualQtyTransfer = ($actualPoQty - $initialQtyPo) + $qtyVal;
					
                    $split_qty = explode('.', $newActualQtyTransfer);
                    if($split_qty[1] == 0)
                    {
                        $newActualQtyTransfer = number_format($newActualQtyTransfer);
                    }
                    $newActualQtyTransfer       = str_replace(',', '',$newActualQtyTransfer);
					
                    $split_price = explode('.', $itemPriceVal);
                    if($split_price[1] == 0)
                    {
                        $itemPriceVal = number_format($itemPriceVal);
                    }
					else
					{
						$itemPriceVal = number_format($itemPriceVal, 2);
					}
                    $itemPriceVal       = str_replace(',', '',$itemPriceVal);
                    $itemPriceVal = rtrim(rtrim(number_format($itemPriceVal, 2, ".", ""), '0'), '.');
                    
                    // New Amount Value
                    $newActualAmountTransfer    = $newActualQtyTransfer * $itemPriceVal;
                    
                    $split_amount = explode('.', $newActualAmountTransfer);
                    if($split_amount[1] == 0)
                    {
                        $newActualAmountTransfer = number_format($newActualAmountTransfer);
                    }
                    else
                    {
                        $newActualAmountTransfer = number_format($newActualAmountTransfer,2);
                    }
                    $newActualAmountTransfer       = str_replace(',', '',$newActualAmountTransfer);
                    $newActualAmountTransfer = rtrim(rtrim(number_format($newActualAmountTransfer, 2, ".", ""), '0'), '.');
					
                    /**
                     * UPDATE EPS_T_TRANSFER
                     */
                    $query_update_t_transfer = "update
                                                    EPS_T_TRANSFER
                                                set
                                                    NEW_ITEM_CD = '$itemCdVal'
                                                    ,NEW_ITEM_NAME = '$itemNameVal'
                                                    ,ACTUAL_QTY = '$newActualQtyTransfer'
                                                    ,NEW_ITEM_PRICE = '$itemPriceVal'
                                                    ,NEW_AMOUNT = '$newActualAmountTransfer'
                                                    ,NEW_UNIT_CD = '$unitCdVal'
                                                    ,ITEM_STATUS = '$itemStatusVal'
                                                    ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                    ,UPDATE_BY = '$sUserId'
                                                where
                                                    TRANSFER_ID = '$transferIdVal'";
                    $conn->query($query_update_t_transfer);

                    if($itemStatusVal == '1270' || $itemStatusVal == '1170'){
                        $query_insert_po_detail = "update
                                                        EPS_T_PO_DETAIL
                                                    set
                                                        ITEM_CD = '$itemCdVal'
                                                        ,ITEM_NAME = '$itemNameVal'
                                                        ,QTY = '$newQtyPo'
                                                        ,ITEM_PRICE = '$itemPriceVal'
                                                        ,AMOUNT = '$amountVal'
                                                        ,UNIT_CD = '$unitCdVal'
                                                        ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                        ,UPDATE_BY = '$sUserId'
                                                        ,ATTACHMENT = '$cipVal'
                                                    where
                                                        PO_NO = '$poNoVal'
                                                        and REF_TRANSFER_ID = '$transferIdVal'";
                        $conn->query($query_insert_po_detail);
                    }
                }

               /**
                * DELETE EPS_T_PO_APPROVER
                */ 
                $query_delete_po_app = "delete
                                            EPS_T_PO_APPROVER
                                        where
                                            PO_NO = '$poNo'";
                $conn->query($query_delete_po_app);

                /**
                * INSERT EPS_T_PO_APPROVER
                */
                $newBypassApprover =  explode(",", $bypassApproverArray);
                for($x = 0; $x < count($newNpkApproverArray); $x++){
                    $npkApproverVal     = $newNpkApproverArray[$x];
                    $statusApproverVal  = '';
                    $remarkApproverVal  = '';
                    for($y = 0; $y < count($newBypassApprover); $y++){
                        $approverNoByPass = substr($newBypassApprover[$y],0,1);
                        if($approverNo == $approverNoByPass){
                            $statusApproverVal = constant('BP');
                            $remarkApproverVal = strtoupper(trim(substr($newBypassApprover[$y],1)));
                            $remarkApproverVal = str_replace("'", "''", $remarkApproverVal);
                            break;
                        }
                    }

                    $query_insert_po_approver = "insert into
                                                    EPS_T_PO_APPROVER
                                                    (
                                                        PO_NO
                                                        ,APPROVER_NO
                                                        ,NPK
                                                        ,APPROVAL_STATUS
                                                        ,APPROVAL_REMARK
                                                        ,CREATE_DATE
                                                        ,CREATE_BY
                                                        ,UPDATE_DATE
                                                        ,UPDATE_BY
                                                    )
                                                values
                                                    (
                                                        '$poNo'
                                                        ,'$approverNo'
                                                        ,'$npkApproverVal'
                                                        ,'$statusApproverVal'
                                                        ,'$remarkApproverVal'
                                                        ,convert(VARCHAR(24), GETDATE(), 120)
                                                        ,'$sUserId'
                                                        ,convert(VARCHAR(24), GETDATE(), 120)
                                                        ,'$sUserId'
                                                    )";
                    $conn->query($query_insert_po_approver);
                    $approverNo++;
                }
                $msg = 'Success';
            }
            
        }
        
        if($action == 'SendPo')
        {
            $getCurrentDate     = date('d/m/Y');
            $poNo               = trim($_GET['poNoPrm']);
            $deliveryDate       = encodeDate($_GET['deliveryDatePrm']);
            $addRemark          = strtoupper(trim($_GET['addRemarkPrm']));
            $addRemark          = str_replace("'", "''", $addRemark);
            $npkApproverArray   = $_GET['npkApproverArray'];
            $newNpkApproverArray= explode(",", $npkApproverArray);
            $bypassApproverArray= $_GET['bypassApproverArray'];
            $approverNo         = 1;
            $poItemData         = ($_SESSION['poDetail']);
            $nextApprover       = $newNpkApproverArray[0];
            
			/**
             * SELECT EPS_T_PO_HEADER
             */
            $query_select_t_po_header = "select 
                                            CURRENCY_CD
                                         from
                                            EPS_T_PO_HEADER
                                         where
                                            PO_NO = '$poNo'";
            $sql_select_t_po_header = $conn->query($query_select_t_po_header);
            $row_select_t_po_header = $sql_select_t_po_header->fetch(PDO::FETCH_ASSOC);
            $currencyCd             = $row_select_t_po_header['CURRENCY_CD'];
			
            if(strtotime(str_replace('/', '-', $deliveryDate)) < strtotime(str_replace('/', '-', $getCurrentDate)))
            {
                $msg = 'Mandatory_1';
            }
            else if($currencyCd != 'IDR' && $addRemark == '')
            {
                $msg = 'Mandatory_6';
            }
            else
            {
                /**
                 * Unset SESSION 
                 **/
                unset($_SESSION['poStatus']);
                
                /**
                 * UPDATE EPS_T_PO_HEADER
                 */
                $query_update_po = "update
                                        EPS_T_PO_HEADER
                                    set
                                        DELIVERY_DATE = '$deliveryDate'
                                        ,ADDITIONAL_REMARK = '$addRemark'
                                        ,PO_STATUS = '1220'
                                        ,APPROVER = '$nextApprover'
                                        ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                        ,UPDATE_BY = '$sUserId'
                                    where
                                        PO_NO = '$poNo'";
                $conn->query($query_update_po);

                /**
                * UPDATE EPS_T_PO_DETAIL
                */
                for($j = 0; $j < count($poItemData); $j++){
                    $poNoVal        = $poItemData[$j]['poNo'];
                    $transferIdVal  = $poItemData[$j]['refTransferId'];
                    $itemCdVal      = $poItemData[$j]['itemCd'];
                    $itemNameVal    = $poItemData[$j]['itemName'];
                    $itemNameVal    = str_replace("'", "''", $itemNameVal);
                    $itemNameVal    = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $itemNameVal);
                    $itemNameVal    = preg_replace('/\s+/', ' ',$itemNameVal);
                    $qtyVal         = $poItemData[$j]['qty'];
                    $itemPriceVal   = $poItemData[$j]['itemPrice'];
                    $amountVal      = $poItemData[$j]['amount'];
                    $unitCdVal      = $poItemData[$j]['unitCd'];
                    $itemStatusVal  = $poItemData[$j]['itemStatus'];
                    $cipVal         = $poItemData[$j]['cip'];
					$newQtyPo       = $qtyVal;

					$query_select_t_po_detail = "select
                                                    QTY
                                                from
                                                    EPS_T_PO_DETAIL
                                                where
                                                    REF_TRANSFER_ID = '$transferIdVal'
                                                    and PO_NO = '$poNo'";
                    $sql_select_t_po_detail = $conn->query($query_select_t_po_detail);
                    $row_select_t_po_detail = $sql_select_t_po_detail->fetch(PDO::FETCH_ASSOC);
                    $initialQtyPo           = $row_select_t_po_detail['QTY'];
					
					/*if($initialQtyPo != $qtyVal)
                    {
                        $itemStatusVal  = '1160';
                        $newQtyPo       = $qtyVal;
                        /**
                         * SELECT EPS_T_TRANSFER
                         **/ 
                        /*$query_select_t_transfer = "select
                                                        ACTUAL_QTY
                                                    from
                                                        EPS_T_TRANSFER
                                                    where
                                                        TRANSFER_ID = '$transferIdVal'";
                        $sql_select_t_transfer = $conn->query($query_select_t_transfer);
                        $row_select_t_transfer = $sql_select_t_transfer->fetch(PDO::FETCH_ASSOC);
                        $initialQtyTransfer = $row_select_t_transfer['ACTUAL_QTY'];
                        
                        $newActualQtyTransfer       = ($initialQtyTransfer - $initialQtyPo) + $qtyVal;
                        $newActualQtyTransfer       = number_format($newActualQtyTransfer);
                        $newActualQtyTransfer       = str_replace(',', '',$newActualQtyTransfer);
                        $newActualAmountTransfer    = $newActualQtyTransfer * $itemPriceVal;
                    }
                    else
                    {
                        $newQtyPo                   = $qtyVal;
                        $newActualQtyTransfer       = $qtyVal;
                        $newActualQtyTransfer       = number_format($newActualQtyTransfer);
                        $newActualQtyTransfer       = str_replace(',', '',$newActualQtyTransfer);
                        $newActualAmountTransfer    = $newActualQtyTransfer * $itemPriceVal;
                    }*/
					
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
                    $initialQtyTransfer = $row_select_t_transfer['NEW_QTY'];
                    $actualQtyTransfer  = $row_select_t_transfer['ACTUAL_QTY']; 
                    
					/**
                     * SELECT EPS_T_PO_DETAIL
                     **/ 
                    $query_select_sum_t_po_detail = "select
                                                        sum(QTY) as ACTUAL_QTY
                                                    from         
                                                        EPS_T_PO_DETAIL
                                                    left join
                                                        EPS_T_PO_HEADER
                                                    on 
                                                        EPS_T_PO_HEADER.PO_NO = EPS_T_PO_DETAIL.PO_NO
                                                    where     
                                                        EPS_T_PO_DETAIL.REF_TRANSFER_ID = '$transferIdVal'
                                                        and EPS_T_PO_HEADER.PO_STATUS in ('1210','1220','1230','1250','1280','1330','1370')";
                    $sql_select_sum_t_po_detail = $conn->query($query_select_sum_t_po_detail);
                    $row_select_sum_t_po_detail = $sql_select_sum_t_po_detail->fetch(PDO::FETCH_ASSOC);
                    $actualPoQty = $row_select_sum_t_po_detail['ACTUAL_QTY'];
					
                    if($itemStatusVal == '1130' && $initialQtyTransfer != $actualQtyTransfer)
                    {
                        $newActualQtyTransfer = $actualPoQty - $initialQtyPo;
                    }
                    else
                    {
                        $newActualQtyTransfer = ($actualPoQty - $initialQtyPo) + $qtyVal;
                    }

                    if($itemStatusVal == '1130'){
                        $query_delete_po_detail = "delete from
                                                        EPS_T_PO_DETAIL
                                                    where
                                                        PO_NO = '$poNoVal'
                                                        and REF_TRANSFER_ID = '$transferIdVal'";
                        $conn->query($query_delete_po_detail);
                    }
                    
                    /**
                     * UPDATE PROGRAM FOR PARTIAL QTY ISSUE
                     * UPDATED BY   : BYAN PURBAPRANIDHANA
                     * UPDATE DATE  : SEP 29, 2015. 16.00
                     * UPDATE DATE  : NOV 8, 2016. 18.40
                     **/
                    if($initialQtyPo != $qtyVal || $initialQtyTransfer != $newActualQtyTransfer)
                    {
                        $itemStatusVal  = '1170';
                    }
                    
                     /**if($initialQtyTransfer == $actualQtyTransfer)
                    {
                        $newActualQtyTransfer = $qtyVal;
                    }
                    else
                    {
                        $newActualQtyTransfer = ($actualPoQty - $initialQtyPo) + $qtyVal;
                    }**/
                    //$newActualQtyTransfer = ($actualPoQty - $initialQtyPo) + $qtyVal;
					
                    $split_qty = explode('.', $newActualQtyTransfer);
                    if($split_qty[1] == 0)
                    {
                        $newActualQtyTransfer = number_format($newActualQtyTransfer);
                    }
                    $newActualQtyTransfer       = str_replace(',', '',$newActualQtyTransfer);
					
                    $split_price = explode('.', $itemPriceVal);
                    if($split_price[1] == 0)
                    {
                        $itemPriceVal = number_format($itemPriceVal);
                    }
					else
					{
						$itemPriceVal = number_format($itemPriceVal, 2);
					}
                    $itemPriceVal       = str_replace(',', '',$itemPriceVal);
                    $itemPriceVal = rtrim(rtrim(number_format($itemPriceVal, 2, ".", ""), '0'), '.');
					
                    // New Amount Value
                    $newActualAmountTransfer    = $newActualQtyTransfer * $itemPriceVal;
                    
                    $split_amount = explode('.', $newActualAmountTransfer);
                    if($split_amount[1] == 0)
                    {
                        $newActualAmountTransfer = number_format($newActualAmountTransfer);
                    }
                    else
                    {
                        $newActualAmountTransfer = number_format($newActualAmountTransfer,2);
                    }
                    $newActualAmountTransfer       = str_replace(',', '',$newActualAmountTransfer);
                    $newActualAmountTransfer = rtrim(rtrim(number_format($newActualAmountTransfer, 2, ".", ""), '0'), '.');
					
                    /**
                    * UPDATE EPS_T_TRANSFER
                    */
                    $query_update_t_transfer = "update
                                                    EPS_T_TRANSFER
                                                set
                                                    NEW_ITEM_CD = '$itemCdVal'
                                                    ,NEW_ITEM_NAME = '$itemNameVal'
                                                    ,ACTUAL_QTY = '$newActualQtyTransfer'
                                                    ,NEW_ITEM_PRICE = '$itemPriceVal'
                                                    ,NEW_AMOUNT = '$newActualAmountTransfer'
                                                    ,NEW_UNIT_CD = '$unitCdVal'
                                                    ,ITEM_STATUS = '$itemStatusVal'
                                                    ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                    ,UPDATE_BY = '$sUserId'
                                                where
                                                    TRANSFER_ID = '$transferIdVal'";
                    $conn->query($query_update_t_transfer);

                    if($itemStatusVal == '1270' || $itemStatusVal == '1170'){
                        $query_insert_po_detail = "update
                                                        EPS_T_PO_DETAIL
                                                    set
                                                        ITEM_CD = '$itemCdVal'
                                                        ,ITEM_NAME = '$itemNameVal'
                                                        ,QTY = '$newQtyPo'
                                                        ,ITEM_PRICE = '$itemPriceVal'
                                                        ,AMOUNT = '$amountVal'
                                                        ,UNIT_CD = '$unitCdVal'
                                                        ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                        ,UPDATE_BY = '$sUserId'
                                                        ,ATTACHMENT = '$cipVal'
                                                    where
                                                        PO_NO = '$poNoVal'
                                                        and REF_TRANSFER_ID = '$transferIdVal'";
                        $conn->query($query_insert_po_detail);
                    }
                }

               /**
                * DELETE EPS_T_PO_APPROVER
                */
                $query_delete_po_app = "delete
                                            EPS_T_PO_APPROVER
                                        where
                                            PO_NO = '$poNo'";
                $conn->query($query_delete_po_app);

               /**
                * INSERT EPS_T_PO_APPROVER
                */  
                $newBypassApprover =  explode(",", $bypassApproverArray);   
                $flagAppNo = 0;
                $flagApp = '';
                for($x = 0; $x < count($newNpkApproverArray); $x++){
                    $npkApproverVal =  $newNpkApproverArray[$x];
                    $statusApproverVal  = '';
                    $remarkApproverVal  = '';

                    for($y = 0; $y < count($newBypassApprover); $y++){
                        $approverNoByPass = substr($newBypassApprover[$y],0,1);
                        if($approverNo == $approverNoByPass){
                            $statusApproverVal = constant('BP');
                            $remarkApproverVal = strtoupper(trim(substr($newBypassApprover[$y],1)));
                            $remarkApproverVal = str_replace("'", "''", $remarkApproverVal);
                            break;
                        }
                    }

                    if($flagApp == '' && $statusApproverVal != constant('BP')){
                        $flagAppNo = $x;
                        $flagApp = '1';
                    }
                    if($x == 0){
                        if($statusApproverVal != constant('BP')){
                            $statusApproverVal = constant('WA');
                        }
                    }
                    else{
                        if($flagAppNo == $x){
                            $statusApproverVal = constant('WA');
                        }  
                    }

                    $query_insert_po_approver = "insert into
                                                    EPS_T_PO_APPROVER
                                                    (
                                                        PO_NO
                                                        ,APPROVER_NO
                                                        ,NPK
                                                        ,APPROVAL_STATUS
                                                        ,APPROVAL_REMARK
                                                        ,CREATE_DATE
                                                        ,CREATE_BY
                                                        ,UPDATE_DATE
                                                        ,UPDATE_BY
                                                    )
                                                values
                                                    (
                                                        '$poNo'
                                                        ,'$approverNo'
                                                        ,'$npkApproverVal'
                                                        ,'$statusApproverVal'
                                                        ,'$remarkApproverVal'
                                                        ,convert(VARCHAR(24), GETDATE(), 120)
                                                        ,'$sUserId'
                                                        ,convert(VARCHAR(24), GETDATE(), 120)
                                                        ,'$sUserId'
                                                    )";
                    $conn->query($query_insert_po_approver);

                    if($statusApproverVal == 'BP'){
                        $query_update_po_approver_bypass = "update
                                                                EPS_T_PO_APPROVER
                                                            set
                                                                APPROVAL_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                                ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                                ,UPDATE_BY = '$sUserId'
                                                            where
                                                                PO_NO = '$poNo'
                                                                and APPROVAL_STATUS = 'BP'"; 
                        $conn->query($query_update_po_approver_bypass);
                    }
                    $approverNo++;
                }
               /**
                * UPDATE EPS_T_PO_HEADER
                */  
                $nextApprover = $newNpkApproverArray[$flagAppNo];
                $query_update_po_2 = "update 
                                        EPS_T_PO_HEADER 
                                    set
                                        APPROVER = '$nextApprover'
                                        ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                        ,UPDATE_BY = '$sUserId'
                                    where
                                        PO_NO = '$poNo'";
                $sql_update_po_2 = $conn->query($query_update_po_2);

               /**********************************************************************
                * SEND MAIL
                **********************************************************************/
                $mailFrom       = $sInet;
                $mailFromName   = $sNotes;  

               /**
                * TO NEXT APPROVER
                **/
                $approvalStatus     = "Waiting for Approval";
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
                                        rtrim(ltrim(EPS_M_DSCID.INOPOK)) = ltrim('".$nextApprover."')";
                $sql_select_dscid = $conn->query($query_select_dscid);
                $row_select_dscid = $sql_select_dscid->fetch(PDO::FETCH_ASSOC);
                if($row_select_dscid){
                        $mailTo           = $row_select_dscid['INETML'];
                        //$mailTo             = 'muh.iqbal@taci.toyota-industries.com';
                        $passwordApprover  = $row_select_dscid['PASSWORD'];
                        $getParamLink  = paramEncrypt("action=open&poNo=$poNo&userId=$nextApprover&password=$passwordApprover");
                        $mailSubject = "[EPS] WAITING APPROVAL. PO No: ".$poNo;
                        $mailMessage = "<table style='font-family: Arial; font-size: 12px;'>";
                        $mailMessage .= "<tr><td>PO No</td><td>: </td><td>".$poNo."</td></tr>";
                        $mailMessage .= "<tr><td>Approval Status</td><td>:</td><td>".$approvalStatus."</td></tr>";
                        $mailMessage .= "</table></font>";
                        //$mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage
//                        poSendMail($poNo, $mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage);
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
                * TO BYPASS APPROVER
                **/
                $approvalStatus     = "Bypass for Approval";
                $query_select_bypass = "select 
                                            NPK 
                                        from 
                                            EPS_T_PO_APPROVER 
                                        where
                                            PO_NO = '$poNo'
                                            and APPROVAL_STATUS = '".constant('BP')."'";
                $sql_select_bypass= $conn->query($query_select_bypass);
                while($row_select_bypass = $sql_select_bypass->fetch(PDO::FETCH_ASSOC)){
                    $npkByPassApprover = $row_select_bypass['NPK'];

                    $query_select_dscid_2 = "select 
                                                EPS_M_DSCID.INETML
                                                ,EPS_M_USER.PASSWORD 
                                            from 
                                                EPS_M_DSCID 
                                            inner join 
                                                EPS_M_USER 
                                            on 
                                                ltrim(EPS_M_DSCID.INOPOK) = ltrim(EPS_M_USER.NPK) 
                                            where  
                                                rtrim(ltrim(EPS_M_DSCID.INOPOK)) = ltrim('".$npkByPassApprover."')";               
					$sql_select_dscid_2 = $conn->query($query_select_dscid_2);
                    $row_select_dscid_2 = $sql_select_dscid_2->fetch(PDO::FETCH_ASSOC);
                    if($row_select_dscid_2){
                        $mailTo        = $row_select_dscid_2['INETML'];
                        //$mailTo          = 'BYAN_PURBA@denso.co.id';
                        $passwordApprover= $row_select_dscid_2['PASSWORD'];
                        $getParamLink   = paramEncrypt("action=open&poNo=$poNo&userId=$nextApprover&password=$passwordApprover");
                        $mailSubject = "[EPS] BYPASS APPROVAL. PO No: ".$poNo;
                        $mailMessage = "<table style='font-family: Arial; font-size: 12px;'>";
                        $mailMessage .= "<tr><td>PO No</td><td>: </td><td>".$poNo."</td></tr>";
                        $mailMessage .= "<tr><td>Approval Status</td><td>:</td><td>".$approvalStatus."</td></tr>";
                        $mailMessage .= "</table></font>";
                        //$mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage
                        poSendMail($poNo, $mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage);
                        
                        
                    }
                }
                $msg = 'Success';
            }
            
        }
        
        if($action == 'SavePoAfterEdit')
        {
            $getCurrentDate     = date('d/m/Y');
            $poNo               = trim($_GET['poNoPrm']);
            $deliveryDate       = encodeDate($_GET['deliveryDatePrm']);
            $addRemark          = strtoupper(trim($_GET['addRemarkPrm']));
            $npkApproverArray   = $_GET['npkApproverArray'];
            $newNpkApproverArray= explode(",", $npkApproverArray);
            $bypassApproverArray= $_GET['bypassApproverArray'];
            $approverNo         = 1;
            $poItemData         = ($_SESSION['poDetail']);
            
            if(strtotime(str_replace('/', '-', $deliveryDate)) < strtotime(str_replace('/', '-', $getCurrentDate)))
            {
                $msg = 'Mandatory_1';
            }
            else
            {
                /**
                 * UPDATE EPS_T_PO_HEADER
                 */ 
                $query_update_po = "update
                                        EPS_T_PO_HEADER
                                    set
                                        DELIVERY_DATE = '$deliveryDate'
                                        ,ADDITIONAL_REMARK = '$addRemark'
                                        ,PO_STATUS = '1210'
                                        ,APPROVER = ''
                                        ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                        ,UPDATE_BY = '$sUserId'
                                    where
                                        PO_NO = '$poNo'";
                $conn->query($query_update_po);

                /**
                * UPDATE EPS_T_PO_DETAIL
                */
                for($j = 0; $j < count($poItemData); $j++){
                    $poNoVal        = $poItemData[$j]['poNo'];
                    $transferIdVal  = $poItemData[$j]['refTransferId'];
                    $itemCdVal      = $poItemData[$j]['itemCd'];
                    $itemNameVal    = $poItemData[$j]['itemName'];
                    $qtyVal         = $poItemData[$j]['qty'];
                    $itemPriceVal   = $poItemData[$j]['itemPrice'];
                    $amountVal      = $poItemData[$j]['amount'];
                    $unitCdVal      = $poItemData[$j]['unitCd'];
                    $itemStatusVal  = $poItemData[$j]['itemStatus'];

                    /**
                    * UPDATE EPS_T_TRANSFER
                    */
                    $query_update_t_transfer = "update
                                                    EPS_T_TRANSFER
                                                set
                                                    NEW_ITEM_CD = '$itemCdVal'
                                                    ,NEW_ITEM_NAME = '$itemNameVal'
                                                    ,NEW_QTY = '$qtyVal'
                                                    ,NEW_ITEM_PRICE = '$itemPriceVal'
                                                    ,NEW_AMOUNT = '$amountVal'
                                                    ,NEW_UNIT_CD = '$unitCdVal'
                                                    ,ITEM_STATUS = '$itemStatusVal'
                                                    ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                    ,UPDATE_BY = '$sUserId'
                                                where
                                                    TRANSFER_ID = '$transferIdVal'";
                    $conn->query($query_update_t_transfer);

                    if($itemStatusVal == '1270'){
                        $query_insert_po_detail = "update
                                                        EPS_T_PO_DETAIL
                                                    set
                                                        ITEM_CD = '$itemCdVal'
                                                        ,ITEM_NAME = '$itemNameVal'
                                                        ,QTY = '$qtyVal'
                                                        ,ITEM_PRICE = '$itemPriceVal'
                                                        ,AMOUNT = '$amountVal'
                                                        ,UNIT_CD = '$unitCdVal'
                                                        ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                        ,UPDATE_BY = '$sUserId'
                                                    where
                                                        PO_NO = '$poNoVal'
                                                        and REF_TRANSFER_ID = '$transferIdVal'";
                        $conn->query($query_insert_po_detail);
                    }

                    if($itemStatusVal == '1130'){
                        $query_delete_po_detail = "delete from
                                                        EPS_T_PO_DETAIL
                                                    where
                                                        PO_NO = '$poNoVal'
                                                        and REF_TRANSFER_ID = '$transferIdVal'";
                        $conn->query($query_delete_po_detail);
                    }
                }

                /**
                * DELETE EPS_T_PO_APPROVER
                */ 
                $query_delete_po_app = "delete
                                            EPS_T_PO_APPROVER
                                        where
                                            PO_NO = '$poNo'";
                $conn->query($query_delete_po_app);

                /**
                * INSERT EPS_T_PO_APPROVER
                */
                $newBypassApprover =  explode(",", $bypassApproverArray);
                for($x = 0; $x < count($newNpkApproverArray); $x++){
                    $npkApproverVal     = $newNpkApproverArray[$x];
                    $statusApproverVal  = '';
                    $remarkApproverVal  = '';
                    for($y = 0; $y < count($newBypassApprover); $y++){
                        $approverNoByPass = substr($newBypassApprover[$y],0,1);
                        if($approverNo == $approverNoByPass){
                            $statusApproverVal = constant('BP');
                            $remarkApproverVal = strtoupper(trim(substr($newBypassApprover[$y],1)));
                            $remarkApproverVal = str_replace("'", "''", $remarkApproverVal);
                            break;
                        }
                    }

                    $query_insert_po_approver = "insert into
                                                    EPS_T_PO_APPROVER
                                                    (
                                                        PO_NO
                                                        ,APPROVER_NO
                                                        ,NPK
                                                        ,APPROVAL_STATUS
                                                        ,APPROVAL_REMARK
                                                        ,CREATE_DATE
                                                        ,CREATE_BY
                                                        ,UPDATE_DATE
                                                        ,UPDATE_BY
                                                    )
                                                values
                                                    (
                                                        '$poNo'
                                                        ,'$approverNo'
                                                        ,'$npkApproverVal'
                                                        ,'$statusApproverVal'
                                                        ,'$remarkApproverVal'
                                                        ,convert(VARCHAR(24), GETDATE(), 120)
                                                        ,'$sUserId'
                                                        ,convert(VARCHAR(24), GETDATE(), 120)
                                                        ,'$sUserId'
                                                    )";
                    $conn->query($query_insert_po_approver);
                    $approverNo++;
                }
                $msg = 'Success';
            }
        }
        
        if($action == 'SendPoAfterEdit')
        {
            $getCurrentDate     = date('d/m/Y');
            $poNo               = trim($_GET['poNoPrm']);
            $deliveryDate       = encodeDate($_GET['deliveryDatePrm']);
            $addRemark          = strtoupper(trim($_GET['addRemarkPrm']));
            $npkApproverArray   = $_GET['npkApproverArray'];
            $newNpkApproverArray= explode(",", $npkApproverArray);
            $bypassApproverArray= $_GET['bypassApproverArray'];
            $approverNo         = 1;
            $poItemData         = ($_SESSION['poDetail']);
            $nextApprover       = $newNpkApproverArray[0];
            
            if(strtotime(str_replace('/', '-', $deliveryDate)) < strtotime(str_replace('/', '-', $getCurrentDate)))
            {
                $msg = 'Mandatory_1';
            }
            else
            {
                /**
                 * UPDATE EPS_T_PO_HEADER
                 */
                $query_update_po = "update
                                        EPS_T_PO_HEADER
                                    set
                                        DELIVERY_DATE = '$deliveryDate'
                                        ,ADDITIONAL_REMARK = '$addRemark'
                                        ,PO_STATUS = '1220'
                                        ,APPROVER = '$nextApprover'
                                        ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                        ,UPDATE_BY = '$sUserId'
                                    where
                                        PO_NO = '$poNo'";
                $conn->query($query_update_po);

                /**
                * UPDATE EPS_T_PO_DETAIL
                */
                for($j = 0; $j < count($poItemData); $j++){
                    $poNoVal        = $poItemData[$j]['poNo'];
                    $transferIdVal  = $poItemData[$j]['refTransferId'];
                    $itemCdVal      = $poItemData[$j]['itemCd'];
                    $itemNameVal    = $poItemData[$j]['itemName'];
                    $qtyVal         = $poItemData[$j]['qty'];
                    $itemPriceVal   = $poItemData[$j]['itemPrice'];
                    $amountVal      = $poItemData[$j]['amount'];
                    $unitCdVal      = $poItemData[$j]['unitCd'];
                    $itemStatusVal  = $poItemData[$j]['itemStatus'];

                    /**
                    * UPDATE EPS_T_TRANSFER
                    */
                    $query_update_t_transfer = "update
                                                    EPS_T_TRANSFER
                                                set
                                                    NEW_ITEM_CD = '$itemCdVal'
                                                    ,NEW_ITEM_NAME = '$itemNameVal'
                                                    ,NEW_QTY = '$qtyVal'
                                                    ,NEW_ITEM_PRICE = '$itemPriceVal'
                                                    ,NEW_AMOUNT = '$amountVal'
                                                    ,NEW_UNIT_CD = '$unitCdVal'
                                                    ,ITEM_STATUS = '$itemStatusVal'
                                                    ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                    ,UPDATE_BY = '$sUserId'
                                                where
                                                    TRANSFER_ID = '$transferIdVal'";
                    $conn->query($query_update_t_transfer);

                    if($itemStatusVal == '1270'){
                        $query_update_po_detail = "update
                                                        EPS_T_PO_DETAIL
                                                    set
                                                        ITEM_CD = '$itemCdVal'
                                                        ,ITEM_NAME = '$itemNameVal'
                                                        ,QTY = '$qtyVal'
                                                        ,ITEM_PRICE = '$itemPriceVal'
                                                        ,AMOUNT = '$amountVal'
                                                        ,UNIT_CD = '$unitCdVal'
                                                        ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                        ,UPDATE_BY = '$sUserId'
                                                    where
                                                        PO_NO = '$poNoVal'
                                                        and REF_TRANSFER_ID = '$transferIdVal'";
                        $conn->query($query_update_po_detail);
                    }

                    if($itemStatusVal == '1130'){
                        $query_delete_po_detail = "delete from
                                                        EPS_T_PO_DETAIL
                                                    where
                                                        PO_NO = '$poNoVal'
                                                        and REF_TRANSFER_ID = '$transferIdVal'";
                        $conn->query($query_delete_po_detail);
                    }
                }

                /**
                * DELETE EPS_T_PO_APPROVER
                */
                $query_delete_po_app = "delete
                                            EPS_T_PO_APPROVER
                                        where
                                            PO_NO = '$poNo'";
                $conn->query($query_delete_po_app);

                /**
                * INSERT EPS_T_PO_APPROVER
                */  
                $newBypassApprover =  explode(",", $bypassApproverArray);   
                $flagAppNo = 0;
                $flagApp = '';
                for($x = 0; $x < count($newNpkApproverArray); $x++){
                    $npkApproverVal =  $newNpkApproverArray[$x];
                    $statusApproverVal  = '';
                    $remarkApproverVal  = '';

                    for($y = 0; $y < count($newBypassApprover); $y++){
                        $approverNoByPass = substr($newBypassApprover[$y],0,1);
                        if($approverNo == $approverNoByPass){
                            $statusApproverVal = constant('BP');
                            $remarkApproverVal = strtoupper(trim(substr($newBypassApprover[$y],1)));
                            $remarkApproverVal = str_replace("'", "''", $remarkApproverVal);
                            break;
                        }
                    }

                    if($flagApp == '' && $statusApproverVal != constant('BP')){
                        $flagAppNo = $x;
                        $flagApp = '1';
                    }
                    if($x == 0){
                        if($statusApproverVal != constant('BP')){
                            $statusApproverVal = constant('WA');
                        }
                    }
                    else{
                        if($flagAppNo == $x){
                            $statusApproverVal = constant('WA');
                        }  
                    }

                    $query_insert_po_approver = "insert into
                                                    EPS_T_PO_APPROVER
                                                    (
                                                        PO_NO
                                                        ,APPROVER_NO
                                                        ,NPK
                                                        ,APPROVAL_STATUS
                                                        ,APPROVAL_REMARK
                                                        ,CREATE_DATE
                                                        ,CREATE_BY
                                                        ,UPDATE_DATE
                                                        ,UPDATE_BY
                                                    )
                                                values
                                                    (
                                                        '$poNo'
                                                        ,'$approverNo'
                                                        ,'$npkApproverVal'
                                                        ,'$statusApproverVal'
                                                        ,'$remarkApproverVal'
                                                        ,convert(VARCHAR(24), GETDATE(), 120)
                                                        ,'$sUserId'
                                                        ,convert(VARCHAR(24), GETDATE(), 120)
                                                        ,'$sUserId'
                                                    )";
                    $conn->query($query_insert_po_approver);

                    if($statusApproverVal == 'BP'){
                        $query_update_po_approver_bypass = "update
                                                                EPS_T_PO_APPROVER
                                                            set
                                                                APPROVAL_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                                ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                                ,UPDATE_BY = '$sUserId'
                                                            where
                                                                PO_NO = '$poNo'
                                                                and APPROVAL_STATUS = 'BP'"; 
                        $conn->query($query_update_po_approver_bypass);
                    }
                    $approverNo++;
                }

                /**
                * UPDATE EPS_T_PO_HEADER
                */  
                $nextApprover = $newNpkApproverArray[$flagAppNo];
                $query_update_po_2 = "update 
                                        EPS_T_PO_HEADER 
                                    set
                                        APPROVER = '$nextApprover'
                                        ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                        ,UPDATE_BY = '$sUserId'
                                    where
                                        PO_NO = '$poNo'";
                $sql_update_po_2 = $conn->query($query_update_po_2);

                /**********************************************************************
                * SEND MAIL
                **********************************************************************/
                $mailFrom       = $sInet;
                $mailFromName   = $sNotes;  

                /**
                * TO NEXT APPROVER
                **/
                $approvalStatus     = "Waiting for Approval";
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
                                        rtrim(ltrim(EPS_M_DSCID.INOPOK)) = ltrim('".$nextApprover."')";
                $sql_select_dscid = $conn->query($query_select_dscid);
                $row_select_dscid = $sql_select_dscid->fetch(PDO::FETCH_ASSOC);
                if($row_select_dscid){
                        $mailTo           = $row_select_dscid['INETML'];
                        //$mailTo             = 'BYAN_PURBA@denso.co.id';
                        $passwordApprover  = $row_select_dscid['PASSWORD'];
                        $getParamLink  = paramEncrypt("action=open&poNo=$poNo&userId=$nextApprover&password=$passwordApprover");
                        $mailSubject = "[EPS] WAITING APPROVAL. PO No: ".$poNo;
                        $mailMessage = "<font face='Trebuchet MS' size='-1'><table>";
                        $mailMessage .= "<tr><td>PO No</td><td>: </td><td>".$poNo."</td></tr>";
                        $mailMessage .= "<tr><td>Approval Status</td><td>:</td><td>".$approvalStatus."</td></tr>";
                        $mailMessage .= "</table></font>";
                        //$mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage
                        poSendMail($poNo, $mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage);

                }

                /**
                * TO BYPASS APPROVER
                **/
                $approvalStatus     = "Bypass for Approval";
                $query_select_bypass = "select 
                                            NPK 
                                        from 
                                            EPS_T_PO_APPROVER 
                                        where
                                            PO_NO = '$poNo'
                                            and APPROVAL_STATUS = '".constant('BP')."'";
                $sql_select_bypass= $conn->query($query_select_bypass);
                while($row_select_bypass = $sql_select_bypass->fetch(PDO::FETCH_ASSOC)){
                    $npkByPassApprover = $row_select_bypass['NPK'];

                    $query_select_dscid_2 = "select 
                                                EPS_M_DSCID.INETML
                                                ,EPS_M_USER.PASSWORD 
                                            from 
                                                EPS_M_DSCID 
                                            inner join 
                                                EPS_M_USER 
                                            on 
                                                ltrim(EPS_M_DSCID.INOPOK) = ltrim(EPS_M_USER.NPK) 
                                            where  
                                                rtrim(ltrim(EPS_M_DSCID.INOPOK)) = ltrim('".$npkByPassApprover."')";               $sql_select_dscid_2 = $conn->query($query_select_dscid_2);
                    $row_select_dscid_2 = $sql_select_dscid_2->fetch(PDO::FETCH_ASSOC);
                    if($row_select_dscid_2){
                        $mailTo        = $row_select_dscid_2['INETML'];
                        //$mailTo          = 'BYAN_PURBA@denso.co.id';
                        $passwordApprover= $row_select_dscid_2['PASSWORD'];
                        $getParamLink   = paramEncrypt("action=open&poNo=$poNo&userId=$nextApprover&password=$passwordApprover");
                        $mailSubject = "[EPS] BYPASS APPROVAL. PO No: ".$poNo;
                        $mailMessage = "<font face='Trebuchet MS' size='-1'><table>";
                        $mailMessage .= "<tr><td>PO No</td><td>: </td><td>".$poNo."</td></tr>";
                        $mailMessage .= "<tr><td>Approval Status</td><td>:</td><td>".$approvalStatus."</td></tr>";
                        $mailMessage .= "</table></font>";
                        //$mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage
                        poSendMail($poNo, $mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage);

                    }
                }
                $msg = 'Success';
            }
        }
        
        if($action == 'CancelPo')
        {
            $poNo               = trim($_GET['poNoPrm']);
            $poItemData         = ($_SESSION['poDetail']);
            
            /**
             * UPDATE EPS_T_PO_HEADER
             */
            $query_update_po = "update
                                    EPS_T_PO_HEADER
                                set
                                    PO_STATUS = '1290'
                                    ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                    ,UPDATE_BY = '$sUserId'
                                where
                                    PO_NO = '$poNo'";
            $conn->query($query_update_po);
               
			/**
             * SELECT EPS_T_APPROVER
             */
            $query_select_t_po_approver = "select
                                            APPROVER_NO 
                                           from 
                                            EPS_T_PO_APPROVER
                                           where
                                            PO_NO = '$poNo'";
            $sql_select_t_po_approver = $conn->query($query_select_t_po_approver);
            $row_select_t_po_approver = $sql_select_t_po_approver->fetch(PDO::FETCH_ASSOC);
			
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
				
                if($actualPoQty == $qtyActual && $row_select_t_po_approver)
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
            $msg = 'Success';
        }
        
        
        if($action == 'UpdatePoAfterSent')
		{
            $poNo               = trim($_GET['poNoPrm']);
            $addRemark          = trim($_GET['addRemarkPrm']);
            $poItemData         = ($_SESSION['poDetail']);
            
            /**
             * UPDATE EPS_T_PO_HEADER
             */ 
            $query_update_po = "update
                                    EPS_T_PO_HEADER
                                set
                                    ADDITIONAL_REMARK = '$addRemark'
                                    ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                    ,UPDATE_BY = '$sUserId'
                                where
                                    PO_NO = '$poNo'";
            $conn->query($query_update_po);
           
            
            for($j = 0; $j < count($poItemData); $j++){
                $poNoVal        = $poItemData[$j]['poNo'];
                $transferIdVal  = $poItemData[$j]['refTransferId'];
                $itemCdVal      = $poItemData[$j]['itemCd'];
                $itemNameVal    = $poItemData[$j]['itemName'];
                $qtyVal         = $poItemData[$j]['qty'];
                $itemPriceVal   = $poItemData[$j]['itemPrice'];
                $amountVal      = $poItemData[$j]['amount'];
                $unitCdVal      = $poItemData[$j]['unitCd'];
                $itemStatusVal  = $poItemData[$j]['itemStatus'];
                
                /**
                 * UPDATE EPS_T_TRANSFER
                 */
                $query_update_t_transfer = "update
                                                EPS_T_TRANSFER
                                            set
                                                NEW_ITEM_CD = '$itemCdVal'
                                                ,NEW_ITEM_NAME = '$itemNameVal'
                                                ,NEW_QTY = '$qtyVal'
                                                ,NEW_ITEM_PRICE = '$itemPriceVal'
                                                ,NEW_AMOUNT = '$amountVal'
                                                ,NEW_UNIT_CD = '$unitCdVal'
                                                ,ITEM_STATUS = '$itemStatusVal'
                                                ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                ,UPDATE_BY = '$sUserId'
                                            where
                                                TRANSFER_ID = '$transferIdVal'";
                $conn->query($query_update_t_transfer);
                
                /**
                 * UPDATE EPS_T_PO_DETAIL
                 */
                $query_update_po_detail = "update
                                                EPS_T_PO_DETAIL
                                           set
                                                ITEM_CD = '$itemCdVal'
                                                ,ITEM_NAME = '$itemNameVal'
                                                ,QTY = '$qtyVal'
                                                ,ITEM_PRICE = '$itemPriceVal'
                                                ,AMOUNT = '$amountVal'
                                                ,UNIT_CD = '$unitCdVal'
                                                ,RO_STATUS = '$itemStatusVal'
                                                ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                ,UPDATE_BY = '$sUserId'
                                            where
                                                PO_NO = '$poNoVal'
                                                and REF_TRANSFER_ID = '$transferIdVal'";
                $conn->query($query_update_po_detail);
                    
                if($itemStatusVal == '1130'){
                    $query_delete_po_detail = "delete from
                                                    EPS_T_PO_DETAIL
                                                where
                                                    PO_NO = '$poNoVal'
                                                    and REF_TRANSFER_ID = '$transferIdVal'";
                    $conn->query($query_delete_po_detail);
                }
            }
            
            
            /**
             * SELECT EPS_T_PO_DETAIL
             */
            $query_count_t_po_detail = "select
                                            count(*) as ITEM_OPEN
                                        from
                                            EPS_T_PO_DETAIL
                                        where
                                            PO_NO = '$poNo'
                                            and RO_STATUS = '1310'";
            $sql_count_t_po_detaill = $conn->query($query_count_t_po_detail);
            $row_count_t_po_detail = $sql_count_t_po_detaill->fetch(PDO::FETCH_ASSOC);
            $countItemOpen = $row_count_t_po_detail['ITEM_OPEN'];
            if($countItemOpen == 0)
            {
                /**
                 * UPDATE EPS_T_PO_HEADER
                 */
                $query_update_t_po_header = "update
                                                EPS_T_PO_HEADER
                                             set
                                                PO_STATUS = '1280'
                                                ,CLOSED_PO_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                ,UPDATE_BY = '$sUserId'
                                             where
                                                PO_NO = '$poNo' ";
                $conn->query($query_update_t_po_header);
                $msg = 'Success_Closed_Po';
            }
            else{
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
