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
<?php
}
$transferId = strtoupper(trim($_GET['refTransferIdPrm']));

$htmlTable  = 
    "
                <table class='table table-striped table-bordered' id='table-refsupplier'>
                    <thead>
                        <tr>
                            <th colspan=3>SUPPLIER</th>
                            <th rowspan=2>PRICE</th>
                            <th colspan=2 rowspan=2>LEAD TIME</th>
                            <th rowspan=2>ATTACHMENT</th>
                            <th rowspan=2>ATTACHMENT CIP</th>
                            <th rowspan=2>REMARK</th>
                        </tr>
                        <tr>
                            <th>CODE</th>
                            <th>NAME</th>
                            <th>CUR</th>
                        </tr>
                    </thead>
                    <tbody>";
$query = "select 
            SUPPLIER_CD
            ,SUPPLIER_NAME
            ,CURRENCY_CD
            ,ITEM_PRICE
            ,LEAD_TIME
            ,UNIT_TIME
            ,ATTACHMENT_LOC
            ,ATTACHMENT_CIP
            ,REMARK
          from 
            EPS_T_TRANSFER_SUPPLIER
          where
            TRANSFER_ID = '$transferId'";
$sql = $conn->query($query);
while($row = $sql->fetch(PDO::FETCH_ASSOC)){
    $supplierCdVal  = $row['SUPPLIER_CD'];
    $supplierNameVal= $row['SUPPLIER_NAME'];
    $currencyCdVal  = $row['CURRENCY_CD'];
    $itemPriceVal   = $row['ITEM_PRICE'];
    $leadTimeVal    = $row['LEAD_TIME'];
    $unitTimeVal    = $row['UNIT_TIME'];
    $attachmentLocVal= $row['ATTACHMENT_LOC'];
    $attachmentCipVal= $row['ATTACHMENT_CIP'];
    $remarkVal      = $row['REMARK'];
	
    $split_item_price = explode('.', $itemPriceVal);
    if($split_item_price[1] == 0)
    {
        $itemPriceVal = number_format($itemPriceVal);
    }
    else
    {
        $itemPriceVal = number_format($itemPriceVal,2);
    }
	
    $attachmentYearVal  = substr($attachmentLocVal,0,4);
    $attachmentMonthVal = substr($attachmentLocVal,5,2);
    $attachmentFileVal  = substr($attachmentLocVal,8);

    $htmlTable .= "<tr>
                        <td>$supplierCdVal</td>
                        <td>$supplierNameVal</td>
                        <td>$currencyCdVal</td>
                        <td style='text-align: right'>$itemPriceVal</td>
                        <td>$leadTimeVal</td>
                        <td>$unitTimeVal</td>
                        <td><a href='file://///10.82.101.2/EPS/Quotation/$supplierCdVal/$attachmentYearVal/$attachmentMonthVal/$attachmentFileVal' target='_blank'  style='color: #19BC9C'>$attachmentLocVal</a></td>
                        <td><a href='file://///10.82.101.2/EPS/Rfi/$attachmentCipVal' target='_blank'  style='color: #19BC9C'>$attachmentCipVal</a></td>
<!--                        <td><a href='file://///10.82.101.31/tacifss02/TACI General Database/C000 - Supporting/C020 - Indirect/C020-T4100 - Procurement/PROCUREMENT/EPS/Quotation/$supplierCdVal/$attachmentYearVal/$attachmentMonthVal/$attachmentFileVal' target='_blank'  style='color: #19BC9C'>$attachmentLocVal</a></td>
                        <td><a href='file://///10.82.101.31/tacifss02/TACI General Database/C000 - Supporting/C020 - Indirect/C020-T4100 - Procurement/PROCUREMENT/EPS/Rfi/$attachmentCipVal' target='_blank'  style='color: #19BC9C'>$attachmentCipVal</a></td>-->
                        <td>$remarkVal</td>
                   </tr>";
}
$htmlTable      .= "</tbody>
                </table>";
echo $htmlTable; 
?>