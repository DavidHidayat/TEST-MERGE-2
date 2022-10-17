<?php session_start(); 
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Constant.php";

if(isset($_SESSION['sUserId']))
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
    $sUserId    = $_SESSION['sUserId'];
    $sBuLogin   = $_SESSION['sBuLogin'];
    $sUserType  = $_SESSION['sUserType'];
    
}
else
{	
?>
    <script language="javascript"> alert("Sorry, you are not authorized to access this page.");
    document.location="../db/Login/Logout.php"; </script>
<?
}    

$criteria       = trim($_GET['criteria']);
$refTransferId  = strtoupper(trim($_GET['refTransferIdPrm']));
$poNoPrm        = strtoupper(trim($_GET['poNoPrm']));


if($criteria== 'PoDetail')
{
    $htmlTableHeader = 
                "<table class='table table-striped table-bordered' id='table-po'>
                        <thead>
                            <tr>
                                <th colspan='14'>PO</th>
                                <th colspan='4'>RO</th>
                            </tr>
                            <tr>
                                <th colspan='7'>HEADER</th>
                                <th colspan='7'>DETAIL</th>
                                <th colspan='4' style='text-align: right; color: #3F85F5'>A: Add || C: Cancel || O: Opened</th>
                            </tr>
                            <tr>
                                <th rowspan='2'>PO NO</th>
                                <th rowspan='2'>STATUS</th>
                                <th rowspan='2'>ISSUER</th>
                                <th rowspan='2'>SUPPLIER</th>
                                <th rowspan='2'>CUR</th>
                                <th rowspan='2'>DUE DATE</th>
                                <th rowspan='2'>SENT DATE</th>
                                <th rowspan='2'>CODE</th>
                                <th rowspan='2'>NAME</th>
                                <th rowspan='2'>QTY</th>
                                <th rowspan='2'>UM</th>
                                <th rowspan='2'>PRICE</th>
                                <th rowspan='2'>AMOUNT</th>
                                <th rowspan='2'>CHARGED<br>BU</th>
                                <th rowspan='2'>DATE</th>
                                <th rowspan='2'>QTY</th>
                                <th rowspan='2'>REMARK</th>
                                <th rowspan='2' width='60px'>ACTION **</th>
                            </tr>
                        </thead>
                        <tbody>";
    $itemNo = 0; 
    $query_select_t_po_detail = "select     
                                    EPS_T_PO_DETAIL.PO_NO
                                    ,EPS_T_PO_HEADER.SUPPLIER_NAME
                                    ,EPS_T_PO_HEADER.CURRENCY_CD
                                    ,substring(EPS_T_PO_HEADER.DELIVERY_DATE,7,2)+'/'+substring(EPS_T_PO_HEADER.DELIVERY_DATE,5,2)+'/'+substring(EPS_T_PO_HEADER.DELIVERY_DATE,1,4) as DELIVERY_DATE
                                    ,EPS_T_PO_HEADER.PO_STATUS
                                    ,convert(VARCHAR(24), EPS_T_PO_HEADER.SEND_PO_DATE, 103) as SEND_PO_DATE
                                    ,EPS_M_APP_STATUS.APP_STATUS_NAME as PO_STATUS_NAME
                                    ,EPS_T_PO_DETAIL.ITEM_CD
                                    ,EPS_T_PO_DETAIL.ITEM_NAME
                                    ,EPS_T_PO_DETAIL.QTY
                                    ,EPS_T_PO_DETAIL.ITEM_PRICE
                                    ,EPS_T_PO_DETAIL.AMOUNT
                                    ,EPS_T_PO_DETAIL.UNIT_CD
                                    ,EPS_T_RO_DETAIL.TRANSACTION_QTY
                                    ,substring(EPS_T_RO_DETAIL.TRANSACTION_DATE,7,2)+'/'+substring(EPS_T_RO_DETAIL.TRANSACTION_DATE,5,2)+'/'+substring(EPS_T_RO_DETAIL.TRANSACTION_DATE,1,4) as TRANSACTION_DATE
                                    ,EPS_T_RO_DETAIL.TRANSACTION_FLAG
                                    ,EPS_T_RO_DETAIL.RO_REMARK
                                    ,EPS_M_EMPLOYEE.NAMA1 as ISSUER_NAME
                                    ,EPS_T_TRANSFER.NEW_CHARGED_BU
                                from        
                                    EPS_T_PO_DETAIL 
                                left join
                                    EPS_T_PO_HEADER 
                                on 
                                    EPS_T_PO_HEADER.PO_NO = EPS_T_PO_DETAIL.PO_NO
                                left join
                                    EPS_M_APP_STATUS
                                on
                                    EPS_T_PO_HEADER.PO_STATUS = EPS_M_APP_STATUS.APP_STATUS_CD
                                left join
                                    EPS_T_RO_DETAIL 
                                on 
                                    EPS_T_PO_DETAIL.PO_NO = EPS_T_RO_DETAIL.PO_NO 
                                    and EPS_T_PO_DETAIL.REF_TRANSFER_ID = EPS_T_RO_DETAIL.REF_TRANSFER_ID
                                left join
                                    EPS_M_EMPLOYEE
                                on
                                    EPS_T_PO_HEADER.ISSUED_BY = EPS_M_EMPLOYEE.NPK
                                left join
                                    EPS_T_TRANSFER
                                on
                                    EPS_T_PO_DETAIL.REF_TRANSFER_ID = EPS_T_TRANSFER.TRANSFER_ID
                                where     
                                    EPS_T_PO_DETAIL.REF_TRANSFER_ID = '$refTransferId'
                                order by
                                    EPS_T_PO_HEADER.PO_NO
                                    ,EPS_T_RO_DETAIL.RO_SEQ ";
    $sql_select_t_po_detail = $conn->query($query_select_t_po_detail);
    while($row_select_t_po_detail = $sql_select_t_po_detail->fetch(PDO::FETCH_ASSOC)){
        $poNo           = $row_select_t_po_detail['PO_NO'];
        $poStsName      = $row_select_t_po_detail['PO_STATUS_NAME'];
        $supplierName   = $row_select_t_po_detail['SUPPLIER_NAME'];
        $currencyCd     = $row_select_t_po_detail['CURRENCY_CD'];
        $deliveryDate   = $row_select_t_po_detail['DELIVERY_DATE'];
        $sendPoDate     = $row_select_t_po_detail['SEND_PO_DATE'];
        $itemCd         = $row_select_t_po_detail['ITEM_CD'];
        $itemName       = $row_select_t_po_detail['ITEM_NAME'];
        $qty            = $row_select_t_po_detail['QTY'];
        $itemPrice      = $row_select_t_po_detail['ITEM_PRICE'];
        $amount         = $row_select_t_po_detail['AMOUNT'];
        $unitCd         = $row_select_t_po_detail['UNIT_CD'];
        $transactionQty = $row_select_t_po_detail['TRANSACTION_QTY'];
        $transactionDate= $row_select_t_po_detail['TRANSACTION_DATE'];
        $transactionFlag= $row_select_t_po_detail['TRANSACTION_FLAG'];
        $roRemark       = $row_select_t_po_detail['RO_REMARK'];
        $issuerName     = $row_select_t_po_detail['ISSUER_NAME'];
        $issuerName     = substr($issuerName, 0, strpos($issuerName, ' '));
        $prChargedBu    = $row_select_t_po_detail['NEW_CHARGED_BU'];
        if(strlen($transactionDate) == 2)
        {
            $transactionDate = "";
        }
        $itemNo++;
        
        $split = explode('.', $qty);
        if($split[1] == 0)
        {
            $qty = number_format($qty);
        }
        
        $split2 = explode('.', $transactionQty);
        if($split2[1] == 0)
        {
            $transactionQty = number_format($transactionQty);
        }
        if($transactionQty == 0)
        {
            $transactionQty = "";
        }
        
        $split_price = explode('.', $itemPrice);
        if($split_price[1] == 0)
        {
            $itemPrice = number_format($itemPrice);
        }
        else
        {
            $itemPrice = number_format($itemPrice,2);
        }
        
        $split_amount = explode('.', $amount);
        if($split_amount[1] == 0)
        {
            $amount = number_format($amount);
        }
        else
        {
            $amount = number_format($amount,2);
        }
        
        if($itemNo == 1)
        {
            $initialPoNo = $row_select_t_po_detail['PO_NO'];
            $poNo = $initialPoNo;
        }
        else
        {
            if($initialPoNo == $row_select_t_po_detail['PO_NO'])
            {
                $poNo           = '';
            }
            else
            {
                $poNo = $row_select_t_po_detail['PO_NO'];
                $initialPoNo = $row_select_t_po_detail['PO_NO'];
            }
        }
        $htmlTableHeader .= "<tr>
                    <td>
                        $poNo
                    </td>
                    <td>
                        $poStsName
                    </td>
                    <td>
                        $issuerName
                    </td>
                    <td>
                        $supplierName
                    </td>
                    <td>
                        $currencyCd
                    </td>
                    <td>
                        $deliveryDate
                    </td>
                    <td>
                        $sendPoDate
                    </td>
                    <td>
                        $itemCd
                    </td>
                    <td>
                        $itemName
                    </td>
                    <td style='text-align: right'>
                        $qty
                    </td>
                    <td>
                        $unitCd
                    </td>
                    <td style='text-align: right'>
                        $itemPrice
                    </td>
                    <td style='text-align: right'>
                        $amount
                    </td>
                    <td>
                        $prChargedBu
                    </td>
                    <td>
                        $transactionDate
                    </td>
                    <td style='text-align: right'>
                        $transactionQty
                    </td>
                    <td>
                        $roRemark
                    </td>
                    <td>
                        $transactionFlag
                    </td>
                </tr>";
    }
    $htmlTableHeader      .= "
            </tbody>
        </table>";
}

if($criteria== 'PoInformation')
{
     $htmlTableHeader = 
                "<table class='table table-striped table-bordered' id='table-poheader'>
                    <thead>
                        <tr>
                            <th colspan='9'>PO HEADER</th>
                        </tr>
                        <tr>
                            <th rowspan='2'>PO NO</th>
                            <th colspan='2'>ISSUED</th>
                            <th colspan='2'>DELIVERY</th>
                            <th rowspan='2'>SENT PO DATE</th>
                            <th colspan='2'>CLOSING</th>
                            <th rowspan='2'>ADDITIONAL REMARK</th>
                        </tr>
                        <tr>
                            <th>BY</th>
                            <th>DATE</th>
                            <th>PLANT</th>
                            <th>DUE DATE</th>
                            <th>PO DATE</th>
                            <th>MONTH</th>
                        </tr>
                    </thead>
                    <tbody>";
    $itemNo = 0; 
    $query_select_t_po_header = "select
                                    EPS_T_PO_HEADER.PO_NO
                                    ,EPS_M_EMPLOYEE.NAMA1 as ISSUED_NAME
                                    ,substring(EPS_T_PO_HEADER.ISSUED_DATE,7,2)+'/'+substring(EPS_T_PO_HEADER.ISSUED_DATE,5,2)+'/'+substring(EPS_T_PO_HEADER.ISSUED_DATE,1,4) as ISSUED_DATE
                                    ,EPS_T_PO_HEADER.DELIVERY_PLANT
                                    ,substring(EPS_T_PO_HEADER.DELIVERY_DATE,7,2)+'/'+substring(EPS_T_PO_HEADER.DELIVERY_DATE,5,2)+'/'+substring(EPS_T_PO_HEADER.DELIVERY_DATE,1,4) as DELIVERY_DATE
                                    ,EPS_T_PO_HEADER.SEND_PO_DATE
                                    ,EPS_T_PO_HEADER.CLOSED_PO_DATE
                                    ,EPS_T_PO_HEADER.CLOSED_PO_MONTH
                                    ,EPS_T_PO_HEADER.ADDITIONAL_REMARK
                                from        
                                    EPS_T_PO_HEADER 
                                left join
                                    EPS_M_EMPLOYEE 
                                on 
                                    EPS_T_PO_HEADER.ISSUED_BY = EPS_M_EMPLOYEE.NPK
                                where
                                    EPS_T_PO_HEADER.PO_NO = '$poNoPrm'";
    $sql_select_t_po_header = $conn->query($query_select_t_po_header);
    while($row_select_t_po_header = $sql_select_t_po_header->fetch(PDO::FETCH_ASSOC)){
        $poNo               = $row_select_t_po_header['PO_NO'];
        $issuedName         = $row_select_t_po_header['ISSUED_NAME'];
        $issuedDate         = $row_select_t_po_header['ISSUED_DATE'];
        $deliveryPlant      = $row_select_t_po_header['DELIVERY_PLANT'];
        $deliveryDate       = $row_select_t_po_header['DELIVERY_DATE'];
        $sendPoDate         = $row_select_t_po_header['SEND_PO_DATE'];
        $closedPoDate       = $row_select_t_po_header['CLOSED_PO_DATE'];
        $closedPoMonth      = $row_select_t_po_header['CLOSED_PO_MONTH'];
        $additionalRemark   = $row_select_t_po_header['ADDITIONAL_REMARK'];
        $itemNo++;
        
        $htmlTableHeader .= "<tr>
                    <td>
                        $poNo
                    </td>
                    <td>
                        $issuedName
                    </td>
                    <td>
                        $issuedDate
                    </td>
                    <td>
                        $deliveryPlant
                    </td>
                    <td>
                        $deliveryDate
                    </td>
                    <td>
                        $sendPoDate
                    </td>
                    <td>
                        $closedPoDate
                    </td>
                    <td>
                        $closedPoMonth
                    </td>
                    <td>
                        $additionalRemark
                    </td>
                </tr>";
    }
    $htmlTableHeader      .= "
            </tbody>
        </table>";
    $htmlTableDetail = 
                "<table class='table table-striped table-bordered' id='table-podetail'>
                    <thead>
                        <tr>
                            <th colspan='9'>PO DETAIL</th>
                        </tr>
                        <tr>
                            <th colspan='2'>ITEM</th>
                            <th rowspan='2'>QTY</th>
                            <th rowspan='2'>UM</th>
                            <th rowspan='2'>PRICE</th>
                            <th rowspan='2'>CUR</th>
                            <th rowspan='2'>TOTAL</th>
                            <th rowspan='2'>RECEIVING STATUS</th>
                        </tr>
                        <tr>
                            <th>CODE</th>
                            <th>NAME</th>
                        </tr>
                    </thead>
                    <tbody>";
    $query_select_t_po_detail = "select 
                                    EPS_T_PO_DETAIL.PO_NO
                                    ,EPS_T_PO_DETAIL.REF_TRANSFER_ID
                                    ,EPS_T_PO_DETAIL.ITEM_CD
                                    ,EPS_T_PO_DETAIL.ITEM_NAME
                                    ,EPS_T_PO_DETAIL.QTY
                                    ,EPS_T_PO_DETAIL.ITEM_PRICE
                                    ,EPS_T_PO_DETAIL.AMOUNT
                                    ,EPS_T_PO_HEADER.CURRENCY_CD
                                    ,EPS_T_PO_DETAIL.UNIT_CD
                                    ,EPS_M_APP_STATUS.APP_STATUS_NAME as RECEIVING_STATUS_NAME
                                 from
                                    EPS_T_PO_DETAIL
                                 left join
                                    EPS_T_PO_HEADER
                                 on
                                    EPS_T_PO_DETAIL.PO_NO = EPS_T_PO_HEADER.PO_NO
                                 left join
                                    EPS_M_APP_STATUS
                                 on
                                    EPS_T_PO_DETAIL.RO_STATUS = EPS_M_APP_STATUS.APP_STATUS_CD
                                 where
                                    EPS_T_PO_HEADER.PO_NO = '$poNoPrm'
                                    and EPS_T_PO_DETAIL.REF_TRANSFER_ID = '$refTransferId'";
    $sql_select_t_po_detail = $conn->query($query_select_t_po_detail);
    while($row_select_t_po_detail = $sql_select_t_po_detail->fetch(PDO::FETCH_ASSOC)){
        $itemCd          = $row_select_t_po_detail['ITEM_CD'];
        $itemName        = $row_select_t_po_detail['ITEM_NAME'];  
        $qty             = $row_select_t_po_detail['QTY'];
        $itemPrice       = $row_select_t_po_detail['ITEM_PRICE'];  
        $amount          = $row_select_t_po_detail['AMOUNT']; 
        $currencyCd      = $row_select_t_po_detail['CURRENCY_CD'];
        $unitCd          = $row_select_t_po_detail['UNIT_CD'];  
        $roStsName       = $row_select_t_po_detail['RECEIVING_STATUS_NAME'];
        
        $split = explode('.', $qty);
        if($split[1] == 0)
        {                                     
            $qty = number_format($qty);
        }
        
        $split_item_price = explode('.', $itemPrice);
        if($split_item_price[1] == 0)
        {
            $itemPrice = number_format($itemPrice);
        }
        else
        {
            $itemPrice = number_format($itemPrice,2);
        }
         
        $split_amount = explode('.', $amount);
        if($split_amount[1] == 0)
        {
            $amount = number_format($amount);
        }
        else
        {
            $amount = number_format($amount,2);
        }
                                                
        $htmlTableDetail .= "<tr>
                    <td>
                        $itemCd
                    </td>
                    <td>
                        $itemName
                    </td>
                    <td style='text-align: right'>
                        $qty
                    </td>
                    <td>
                        $unitCd
                    </td>
                    <td style='text-align: right'>
                        $itemPrice
                    </td>
                    <td>
                        $currencyCd
                    </td>
                    <td style='text-align: right'>
                        $amount
                    </td>
                    <td>
                        $roStsName
                    </td>
                </tr>";
    }
    $htmlTableDetail      .= "
            </tbody>
        </table>";
}
echo $htmlTableHeader.$htmlTableDetail;

?>
