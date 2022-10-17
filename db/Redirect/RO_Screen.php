<?php session_start();
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Constant.php";

$criteria   = $_GET['criteria'];

if($criteria == 'roDetail')
{
    $getTransferId  = $_GET['paramRefTransferId'];
    $getPoNo        = $_GET['paramPoNo'];
    
    /**
     * SELECT EPS_T_PO_HEADER
     */
    $query_select_t_po_header = "select 
                                    UPDATE_DATE
                                 from
                                    EPS_T_PO_HEADER
                                 where
                                    PO_NO = '$getPoNo'";
    $sql_select_t_po_header = $conn->query($query_select_t_po_header);
    $row_select_t_po_header = $sql_select_t_po_header->fetch(PDO::FETCH_ASSOC);
    $poHeaderUpdateDate = $row_select_t_po_header['UPDATE_DATE'];
    
    /**
     * SELECT EPS_T_RO_DETAIL
     */
    $query = "select 
                EPS_T_RO_DETAIL.RO_NO
                ,EPS_T_RO_DETAIL.RO_SEQ
                ,EPS_T_RO_DETAIL.PO_NO
                ,EPS_T_RO_DETAIL.REF_TRANSFER_ID
                ,EPS_T_RO_DETAIL.TRANSACTION_QTY
                ,EPS_T_RO_DETAIL.TRANSACTION_FLAG
                ,EPS_T_RO_DETAIL.RO_REMARK
                ,substring(EPS_T_RO_DETAIL.TRANSACTION_DATE, 7, 2) 
                + '/' + substring(EPS_T_RO_DETAIL.TRANSACTION_DATE, 5, 2) 
                + '/' + substring(EPS_T_RO_DETAIL.TRANSACTION_DATE, 1, 4) as TRANSACTION_DATE 
            from 
                EPS_T_RO_DETAIL
            where 
                REF_TRANSFER_ID ='".$getTransferId."'
                and PO_NO = '".$getPoNo."'";
    $sql = $conn->query($query);
    $receivedSeq = 1;
    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
        $roNo           = $row['RO_NO'];
        $roSeq          = $row['RO_SEQ'];
        $poNo           = $row['PO_NO'];
        $refTransferId  = $row['REF_TRANSFER_ID'];
        $transactionQty = $row['TRANSACTION_QTY'];
        $transactionFlag= $row['TRANSACTION_FLAG'];
        $transactionDate= $row['TRANSACTION_DATE'];
        $roRemark       = $row['RO_REMARK'];
        
        $roDetail[] = array(
                        'roNo'=> $roNo
                        ,'roSeq'=> $roSeq
                        ,'poNo'=> $poNo
                        ,'refTransferId'=> $refTransferId
                        ,'transactionQty'=> $transactionQty
                        ,'transactionFlag'=> $transactionFlag
                        ,'transactionDate'=> $transactionDate
                        ,'roRemark'=> $roRemark
        );
        $receivedSeq++;
    }
    
    /**
     * SELECT EPS_T_PO_DETAIL
     */
    $query_t_select_po_detail = "select 
                                    RO_STATUS
                                 from
                                    EPS_T_PO_DETAIL
                                 where
                                    REF_TRANSFER_ID = '$getTransferId'
                                    and PO_NO = '$getPoNo'";
    $sql_t_select_po_detail = $conn->query($query_t_select_po_detail);
    $row_t_select_po_detail = $sql_t_select_po_detail->fetch(PDO::FETCH_ASSOC);
    $roStatusSession = $row_t_select_po_detail['RO_STATUS'];
    
    $redirectPage = "../../ero/WERO002.php?paramRefTransferId=".$getTransferId."&xParamPoNo=".$getPoNo;
    $_SESSION['roDetail']               = $roDetail;
    $_SESSION['roScreen']               = 'EditOpenRoScreen';
    $_SESSION['refTransferIdSession']   = $getTransferId;
    $_SESSION['poNoSession']            = $getPoNo;
    $_SESSION['roStatus']               = $roStatusSession;
    $_SESSION['poHeaderUpdateDate']     = $poHeaderUpdateDate;
}

if($criteria == 'roDetailCancel')
{
    $getTransferId  = $_GET['paramRefTransferId'];
    $getPoNo        = $_GET['paramPoNo'];
    
    /**
     * SELECT EPS_T_PO_HEADER
     */
    $query_select_t_po_header = "select 
                                    UPDATE_DATE
                                 from
                                    EPS_T_PO_HEADER
                                 where
                                    PO_NO = '$getPoNo'";
    $sql_select_t_po_header = $conn->query($query_select_t_po_header);
    $row_select_t_po_header = $sql_select_t_po_header->fetch(PDO::FETCH_ASSOC);
    $poHeaderUpdateDate = $row_select_t_po_header['UPDATE_DATE'];
    
    /**
     * SELECT EPS_T_RO_DETAIL
     */
    $query = "select 
                EPS_T_RO_DETAIL.RO_NO
                ,EPS_T_RO_DETAIL.RO_SEQ
                ,EPS_T_RO_DETAIL.PO_NO
                ,EPS_T_RO_DETAIL.REF_TRANSFER_ID
                ,EPS_T_RO_DETAIL.TRANSACTION_QTY
                ,EPS_T_RO_DETAIL.TRANSACTION_FLAG
                ,EPS_T_RO_DETAIL.RO_REMARK
                ,substring(EPS_T_RO_DETAIL.TRANSACTION_DATE, 7, 2) 
                + '/' + substring(EPS_T_RO_DETAIL.TRANSACTION_DATE, 5, 2) 
                + '/' + substring(EPS_T_RO_DETAIL.TRANSACTION_DATE, 1, 4) as TRANSACTION_DATE 
            from 
                EPS_T_RO_DETAIL
            where 
                REF_TRANSFER_ID ='".$getTransferId."'
                and PO_NO = '".$getPoNo."'";
    $sql = $conn->query($query);
    $receivedSeq = 1;
    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
        $roNo           = $row['RO_NO'];
        $roSeq          = $row['RO_SEQ'];
        $poNo           = $row['PO_NO'];
        $refTransferId  = $row['REF_TRANSFER_ID'];
        $transactionQty = $row['TRANSACTION_QTY'];
        $transactionFlag= $row['TRANSACTION_FLAG'];
        $transactionDate= $row['TRANSACTION_DATE'];
        $roRemark       = $row['RO_REMARK'];
        
        $roDetail[] = array(
                        'roNo'=> $roNo
                        ,'roSeq'=> $roSeq
                        ,'poNo'=> $poNo
                        ,'refTransferId'=> $refTransferId
                        ,'transactionQty'=> $transactionQty
                        ,'transactionFlag'=> $transactionFlag
                        ,'transactionDate'=> $transactionDate
                        ,'roRemark'=> $roRemark
        );
        $receivedSeq++;
    }
    
    /**
     * SELECT EPS_T_PO_DETAIL
     */
    $query_t_select_po_detail = "select 
                                    RO_STATUS
                                 from
                                    EPS_T_PO_DETAIL
                                 where
                                    REF_TRANSFER_ID = '$getTransferId'
                                    and PO_NO = '$getPoNo'";
    $sql_t_select_po_detail = $conn->query($query_t_select_po_detail);
    $row_t_select_po_detail = $sql_t_select_po_detail->fetch(PDO::FETCH_ASSOC);
    $roStatusSession = $row_t_select_po_detail['RO_STATUS'];
    
    $redirectPage = "../../ero/WERO007.php?paramRefTransferId=".$getTransferId."&xParamPoNo=".$getPoNo;
    $_SESSION['roDetail']               = $roDetail;
    $_SESSION['roScreen']               = 'EditOpenRoScreen';
    $_SESSION['refTransferIdSession']   = $getTransferId;
    $_SESSION['poNoSession']            = $getPoNo;
    $_SESSION['roStatus']               = $roStatusSession;
    $_SESSION['poHeaderUpdateDate']     = $poHeaderUpdateDate;
}
if($criteria == 'roDetailClosed')
{
	$getTransferId  = $_GET['paramRefTransferId'];
    $getPoNo        = $_GET['paramPoNo'];
    
    /**
     * SELECT EPS_T_PO_HEADER
     */
    $query_select_t_po_header = "select 
                                    UPDATE_DATE
                                 from
                                    EPS_T_PO_HEADER
                                 where
                                    PO_NO = '$getPoNo'";
    $sql_select_t_po_header = $conn->query($query_select_t_po_header);
    $row_select_t_po_header = $sql_select_t_po_header->fetch(PDO::FETCH_ASSOC);
    $poHeaderUpdateDate = $row_select_t_po_header['UPDATE_DATE'];
    
    /**
     * SELECT EPS_T_RO_DETAIL
     */
    $query = "select 
                EPS_T_RO_DETAIL.RO_NO
                ,EPS_T_RO_DETAIL.RO_SEQ
                ,EPS_T_RO_DETAIL.PO_NO
                ,EPS_T_RO_DETAIL.REF_TRANSFER_ID
                ,EPS_T_RO_DETAIL.TRANSACTION_QTY
                ,EPS_T_RO_DETAIL.TRANSACTION_FLAG
                ,EPS_T_RO_DETAIL.RO_REMARK
                ,substring(EPS_T_RO_DETAIL.TRANSACTION_DATE, 7, 2) 
                + '/' + substring(EPS_T_RO_DETAIL.TRANSACTION_DATE, 5, 2) 
                + '/' + substring(EPS_T_RO_DETAIL.TRANSACTION_DATE, 1, 4) as TRANSACTION_DATE 
            from 
                EPS_T_RO_DETAIL
            where 
                REF_TRANSFER_ID ='".$getTransferId."'
                and PO_NO = '".$getPoNo."'";
    $sql = $conn->query($query);
    $receivedSeq = 1;
    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
        $roNo           = $row['RO_NO'];
        $roSeq          = $row['RO_SEQ'];
        $poNo           = $row['PO_NO'];
        $refTransferId  = $row['REF_TRANSFER_ID'];
        $transactionQty = $row['TRANSACTION_QTY'];
        $transactionFlag= $row['TRANSACTION_FLAG'];
        $transactionDate= $row['TRANSACTION_DATE'];
        $roRemark       = $row['RO_REMARK'];
        
        $roDetail[] = array(
                        'roNo'=> $roNo
                        ,'roSeq'=> $roSeq
                        ,'poNo'=> $poNo
                        ,'refTransferId'=> $refTransferId
                        ,'transactionQty'=> $transactionQty
                        ,'transactionFlag'=> $transactionFlag
                        ,'transactionDate'=> $transactionDate
                        ,'roRemark'=> $roRemark
        );
        $receivedSeq++;
    }
    
    /**
     * SELECT EPS_T_PO_DETAIL
     */
    $query_t_select_po_detail = "select 
                                    RO_STATUS
                                 from
                                    EPS_T_PO_DETAIL
                                 where
                                    REF_TRANSFER_ID = '$getTransferId'
                                    and PO_NO = '$getPoNo'";
    $sql_t_select_po_detail = $conn->query($query_t_select_po_detail);
    $row_t_select_po_detail = $sql_t_select_po_detail->fetch(PDO::FETCH_ASSOC);
    $roStatusSession = $row_t_select_po_detail['RO_STATUS'];
    
    $redirectPage = "../../ero/WERO005.php?paramRefTransferId=".$getTransferId."&xParamPoNo=".$getPoNo;
    $_SESSION['roDetail']               = $roDetail;
    $_SESSION['roScreen']               = 'EditClosedRoScreen';
    $_SESSION['refTransferIdSession']   = $getTransferId;
    $_SESSION['poNoSession']            = $getPoNo;
    $_SESSION['roStatus']               = $roStatusSession;
    $_SESSION['poHeaderUpdateDate']     = $poHeaderUpdateDate;
}
if($criteria == 'roClose')
{
    $poNo = $_GET['paramPoNo'];
    $query = "select 
                EPS_T_PO_DETAIL.PO_NO
                ,EPS_T_PO_DETAIL.REF_TRANSFER_ID
                ,EPS_T_PO_DETAIL.ITEM_CD
                ,EPS_T_PO_DETAIL.ITEM_NAME
                ,EPS_T_PO_DETAIL.QTY
                ,EPS_T_PO_DETAIL.UNIT_CD
                ,EPS_T_PO_DETAIL.ITEM_PRICE
                ,EPS_T_PO_DETAIL.RO_STATUS
                ,EPS_M_APP_STATUS.APP_STATUS_NAME as RO_STATUS_NAME
            from 
                EPS_T_PO_DETAIL
            left join
                EPS_M_APP_STATUS
            on
                EPS_T_PO_DETAIL.RO_STATUS = EPS_M_APP_STATUS.APP_STATUS_CD
            where
                EPS_T_PO_DETAIL.PO_NO = '$poNo'";
    $sql = $conn->query($query);
    $seqPoItem = 1;
    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
        $poNo           = $row['PO_NO'];
        $refTransferId  = $row['REF_TRANSFER_ID'];
        $itemCd         = $row['ITEM_CD'];
        $itemName       = $row['ITEM_NAME'];
        $qty            = $row['QTY'];
        $unitCd         = $row['UNIT_CD'];
        $itemPrice      = $row['ITEM_PRICE'];
        $roStatus       = $row['RO_STATUS'];
        $roStatusName   = $row['RO_STATUS_NAME'];
        
        $poDetail[] = array(
                        'poNo'=> $poNo
                        ,'refTransferId'=> $refTransferId
                        ,'itemCd'=> $itemCd
                        ,'itemName' => $itemName
                        ,'qty'=> $qty
                        ,'unitCd'=> $unitCd
                        ,'itemPrice'=> $itemPrice
                        ,'roStatus'=> $roStatus
                        ,'roStatusName'=> $roStatusName
                        ,'seqPoItem'=> $seqPoItem
                     );
        $seqPoItem++;
    }
    $redirectPage = "../../ero/WERO005.php?paramPoNo=".$poNo;
    $_SESSION['poDetail']   = $poDetail;
}
echo "<script>document.location.href='".$redirectPage."';</script>";
//echo $redirectPage;
?>
