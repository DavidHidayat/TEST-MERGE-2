<?php session_start(); 
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Constant.php";
if(isset($_SESSION['sNPK']))
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
    $action     = 'Edit';
    
}else{	
?>
    <script language="javascript"> alert("Sorry, you are not authorized to access this page.");
    document.location="../db/Login/Logout.php"; </script>
<?
}
$criteria       = trim($_GET['criteria']);
$poNo           = strtoupper(trim($_GET['poNoPrm']));
$refTransferId  = strtoupper(trim($_GET['refTransferIdPrm']));
$initialFlag    = '';
$newFlag        = ''; 
$itemNo         = 0;

if($criteria== 'receivingHeader'){
    $htmlTable      = 
                "<table class='table table-striped table-bordered' id='table-receiving-item'>
                        <thead>
                            <tr>
                                <th rowspan='2'>ITEM NAME</th>
                                <th rowspan='2'>QTY</th>
                                <th rowspan='2'>UM</th>
                                <th colspan='2'>RECEIVING</th>
                            </tr>
                            <tr>
                                <th>DATE</th>
                                <th>QTY</th>
                            </tr>
                        </thead>
                        <tbody>";
    $query = "select 
                EPS_T_PO_DETAIL.ITEM_NAME
                ,EPS_T_PO_DETAIL.QTY
                ,EPS_T_PO_DETAIL.UNIT_CD
                ,substring(EPS_T_RO_DETAIL.RECEIVED_DATE,7,2)+'/'+substring(EPS_T_RO_DETAIL.RECEIVED_DATE,5,2)+'/'+substring(EPS_T_RO_DETAIL.RECEIVED_DATE,1,4) as RECEIVED_DATE
                ,EPS_T_RO_DETAIL.RECEIVED_QTY
            from 
                EPS_T_PO_DETAIL
            left join
                EPS_T_RO_DETAIL
            on
                EPS_T_PO_DETAIL.PO_NO = EPS_T_RO_DETAIL.PO_NO
                and EPS_T_PO_DETAIL.REF_TRANSFER_ID = EPS_T_RO_DETAIL.REF_TRANSFER_ID
            where
                EPS_T_PO_DETAIL.PO_NO = '$poNo'";
    $sql = $conn->query($query);
    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
        $itemNameVal    = $row['ITEM_NAME'];
        $qtyVal         = $row['QTY'];
        $unitCdVal      = $row['UNIT_CD'];
        $receivedDateVal= $row['RECEIVED_DATE'];
        $receivedQtyVal = $row['RECEIVED_QTY'];
        $itemNo++;

		$split = explode('.', $qtyVal);
        if($split[1] == 0)
        {
            $qtyVal = number_format($qtyVal);
        }
		
        if($itemNo == 1){
            $initialItemName = $row['ITEM_NAME'];
            $itemNameVal = $initialItemName;
        }
        else
        {
            if($initialItemName == $row['ITEM_NAME']){
                $itemNameVal= '';
                $qtyVal     = '';
                $unitCdVal  = '';

            }else{
                $itemNameVal = $row['ITEM_NAME'];
                $qtyVal      = $row['QTY'];
                $unitCdVal   = $row['UNIT_CD'];
            }
        }

        $htmlTable .= "<tr>
                    <td>
                        $itemNameVal
                    </td>
                    <td>
                        $qtyVal
                    </td>
                    <td>
                        $unitCdVal
                    </td>
                    <td>
                        $receivedDateVal
                    </td>
                    <td>
                        $receivedQtyVal
                    </td>
                </tr>";
    }
    $htmlTable      .= "</tbody>
                    </table>";
}
if($criteria == 'receivingDetail')
{
    $itemNo = 1;
    $htmlTable      = 
                "<table class='table table-striped table-bordered' id='table-receiving-item'>
                        <thead>
                            <tr>
                                <th colspan=7 style='text-align: right; color: #3F85F5'>
                                    ** A: Add || C: Cancel || O: Opened
                                </th>
                            </tr>
                            <tr>
                                <th>NO</th>
                                <th>DATE</th>
                                <th>QTY</th>
                                <th style='width: 50%'>REMARK</th>
                                <th>ACTION **</th>
                                <th>CREATE<br>DATE</th>
                                <th>CREATE<br>BY</th>
                            </tr>
                        </thead>
                        <tbody>";
    $query = "select 
                EPS_T_RO_DETAIL.RO_SEQ
                ,substring(EPS_T_RO_DETAIL.TRANSACTION_DATE,7,2)+'/'+substring(EPS_T_RO_DETAIL.TRANSACTION_DATE,5,2)+'/'+substring(EPS_T_RO_DETAIL.TRANSACTION_DATE,1,4) as TRANSACTION_DATE
                ,EPS_T_RO_DETAIL.TRANSACTION_QTY
                ,EPS_T_RO_DETAIL.TRANSACTION_FLAG
                ,EPS_T_RO_DETAIL.RO_REMARK
                ,EPS_T_RO_DETAIL.CREATE_DATE
                ,EPS_M_EMPLOYEE.NAMA1 as CREATE_BY_NAME
            from 
                EPS_T_RO_DETAIL
            left join
                EPS_T_PO_DETAIL
            on
                EPS_T_PO_DETAIL.PO_NO = EPS_T_RO_DETAIL.PO_NO
                and EPS_T_PO_DETAIL.REF_TRANSFER_ID = EPS_T_RO_DETAIL.REF_TRANSFER_ID
            left join
                EPS_M_EMPLOYEE
            on
                EPS_T_RO_DETAIL.CREATE_BY = EPS_M_EMPLOYEE.NPK
            where
                EPS_T_PO_DETAIL.PO_NO = '$poNo'
                and EPS_T_PO_DETAIL.REF_TRANSFER_ID = '$refTransferId'
            order by
                EPS_T_RO_DETAIL.CREATE_DATE";
    $sql = $conn->query($query);
    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
        $roSeqVal           = $row['RO_SEQ'];
        $transactionDateVal = $row['TRANSACTION_DATE'];
        $transactionQtyVal  = $row['TRANSACTION_QTY'];
        $transactionFlagVal = $row['TRANSACTION_FLAG'];
        $roRemarkVal        = $row['RO_REMARK'];
        $createDateVal      = $row['CREATE_DATE'];
        $createByNameVal    = $row['CREATE_BY_NAME'];
        
		$split = explode('.', $transactionQtyVal);
        if($split[1] == 0)
        {
            $transactionQtyVal = number_format($transactionQtyVal);
        }
		
        if($transactionFlagVal == 'C' || $transactionFlagVal == 'O')
        {
            $transactionQtyVal = '-'.$transactionQtyVal;
        }
        $htmlTable .= "<tr>
                            <td style='text-align: right'>
                                $itemNo.
                            </td>
                            <td>
                                $transactionDateVal
                            </td>
                            <td style='text-align: right'>
                                $transactionQtyVal
                            </td>
                            <td>
                                $roRemarkVal
                            </td>
                            <td>
                                $transactionFlagVal
                            </td>
                            <td>
                                $createDateVal
                            </td>
                            <td>
                                ".substr($createByNameVal, 0, strpos($createByNameVal, ' '))."
                            </td>
                       </tr>";
        $itemNo++;
    }
    $htmlTable      .= "</tbody>
                    </table>";
}

echo $htmlTable; 
?>
