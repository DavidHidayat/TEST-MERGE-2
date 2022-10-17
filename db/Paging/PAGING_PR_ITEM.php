<?php session_start();
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Common.php";

$buCd       = $_SESSION['sBunit'];
$buLogin    = $_SESSION['sBuLogin'];
$userId     = trim($_SESSION['sUserId']);
$npk        = trim($_SESSION['sNPK']);
$limit      = $_REQUEST['limit'];
$page       = $_REQUEST['page'];
$start      = $_REQUEST['start'];
$next       = $limit*$page;

$json = '['; // start the json array element
$json_names = array();

$prItemCount = '1';

$wherePrSearch  = array();
$prDateVal      = stripslashes($_REQUEST['prDateVal']);
$prNoVal        = stripslashes($_REQUEST['prNoVal']);
$requesterVal   = stripslashes($_REQUEST['requesterVal']);
$approverVal    = stripslashes($_REQUEST['approverVal']);
$prStatusVal    = stripslashes($_REQUEST['prStatusVal']);
$itemTypeVal    = stripslashes($_REQUEST['itemTypeVal']);
$itemNameVal    = stripslashes($_REQUEST['itemNameVal']);
$accountNoVal   = stripslashes($_REQUEST['accountNoVal']);
$rfiNoVal       = stripslashes($_REQUEST['rfiNoVal']);
$unitCdVal      = stripslashes($_REQUEST['unitCdVal']);
$deliveryDateVal= stripslashes($_REQUEST['deliveryDateVal']);
$supplierNameVal= stripslashes($_REQUEST['supplierNameVal']);
$prIssuerVal    = stripslashes($_REQUEST['prIssuerVal']);
$prChargedVal    = stripslashes($_REQUEST['prChargedVal']);

if($prDateVal != '' || $prNoVal != '' || $requesterVal != '' || $approverVal != '' || $prStatusVal != '' 
        || $itemTypeVal != '' || $itemNameVal != '' || $accountNoVal != '' || $rfiNoVal != '' 
        || $unitCdVal != '' || $deliveryDateVal != '' || $supplierNameVal != ''
        || $prIssuerVal != '' || $prChargedVal != ''){
    if($prDateVal){
        $wherePrSearch[] = "EPS_T_PR_HEADER.ISSUED_DATE = '".encodeDate($prDateVal)."'";
    }
    if($prNoVal){
        $wherePrSearch[] = "EPS_T_PR_HEADER.PR_NO = '".$prNoVal."'";
    }
    if($requesterVal){
        $wherePrSearch[] = "EPS_M_EMPLOYEE_2.NAMA1 LIKE '".$requesterVal."%'";
    }
    if($approverVal){
        $wherePrSearch[] = "EPS_M_EMPLOYEE.NAMA1 LIKE '".$approverVal."%'";
    }
    if($prStatusVal){
        $wherePrSearch[] = "EPS_T_PR_HEADER.PR_STATUS = '".$prStatusVal."'";
    }
    if($itemTypeVal){
        $wherePrSearch[] = "EPS_T_PR_DETAIL.ITEM_TYPE_CD = '".$itemTypeVal."'";
    }
    if($itemNameVal){
        $wherePrSearch[] = "EPS_T_PR_DETAIL.ITEM_NAME LIKE '".$itemNameVal."%'";
    }
    if($accountNoVal){
        $wherePrSearch[] = "EPS_T_PR_DETAIL.ACCOUNT_NO = '".$accountNoVal."'";
    }
    if($rfiNoVal){
        $wherePrSearch[] = "EPS_T_PR_DETAIL.RFI_NO = '".$rfiNoVal."'";
    }
    if($unitCdVal){
        $wherePrSearch[] = "EPS_T_PR_DETAIL.UNIT_CD = '".$unitCdVal."'";
    }
    if($deliveryDateVal){
        $wherePrSearch[] = "EPS_T_PR_DETAIL.DELIVERY_DATE = '".$deliveryDateVal."'";
    }
    if($supplierNameVal){
        $wherePrSearch[] = "EPS_T_PR_DETAIL.SUPPLIER_NAME LIKE '".$supplierNameVal."%'";
    }
    if($prIssuerVal){
        $wherePrSearch[] = "EPS_T_PR_HEADER.REQ_BU_CD = '".$prIssuerVal."'";
    }
    if($prChargedVal){
        $wherePrSearch[] = "EPS_T_PR_HEADER.CHARGED_BU_CD = '".$prChargedVal."'";
    }
    $query = "select     
                EPS_T_PR_DETAIL.PR_NO
                ,EPS_T_PR_HEADER.ISSUED_DATE
                ,EPS_M_EMPLOYEE_2.NAMA1 as REQUESTER_NAME
                ,EPS_T_PR_DETAIL.ITEM_CD
                ,EPS_T_PR_DETAIL.ITEM_NAME
                ,substring(EPS_T_PR_DETAIL.DELIVERY_DATE, 7, 2) + '/' + substring(EPS_T_PR_DETAIL.DELIVERY_DATE, 5, 2) + '/' + substring(EPS_T_PR_DETAIL.DELIVERY_DATE, 1, 4) as DELIVERY_DATE 
                ,EPS_T_PR_DETAIL.QTY
                ,EPS_T_PR_DETAIL.ITEM_PRICE
                ,EPS_T_PR_DETAIL.AMOUNT
                ,EPS_T_PR_DETAIL.CURRENCY_CD
                ,EPS_T_PR_DETAIL.ITEM_TYPE_CD
                ,EPS_T_PR_DETAIL.ACCOUNT_NO
                ,EPS_T_PR_DETAIL.RFI_NO
                ,EPS_T_PR_DETAIL.UNIT_CD
                ,EPS_T_PR_DETAIL.SUPPLIER_CD
                ,EPS_T_PR_DETAIL.SUPPLIER_NAME
                ,EPS_T_PR_HEADER.PR_STATUS as PR_STATUS
                ,EPS_M_APP_STATUS.APP_STATUS_NAME as PR_STATUS_NAME
                ,EPS_M_EMPLOYEE.NAMA1 as APPROVER_NAME
                ,EPS_T_TRANSFER.ITEM_STATUS
                ,EPS_M_APP_STATUS_1.APP_STATUS_NAME as ITEM_STATUS_NAME
                ,EPS_T_PR_HEADER.REQ_BU_CD 
                ,EPS_T_PR_HEADER.CHARGED_BU_CD
            from         
                EPS_T_PR_DETAIL 
            inner join
                EPS_T_PR_HEADER 
            on 
                EPS_T_PR_DETAIL.PR_NO = EPS_T_PR_HEADER.PR_NO 
            left join
                EPS_M_EMPLOYEE 
            on 
                EPS_T_PR_HEADER.APPROVER = EPS_M_EMPLOYEE.NPK
            left join
                EPS_M_APP_STATUS 
            on 
                EPS_T_PR_HEADER.PR_STATUS = EPS_M_APP_STATUS.APP_STATUS_CD
            inner join
                EPS_M_EMPLOYEE EPS_M_EMPLOYEE_2
            on 
                EPS_T_PR_HEADER.REQUESTER = EPS_M_EMPLOYEE_2.NPK
            left join
                EPS_T_TRANSFER 
            on 
                EPS_T_PR_DETAIL.PR_NO = EPS_T_TRANSFER.PR_NO 
                and EPS_T_PR_DETAIL.ITEM_NAME = EPS_T_TRANSFER.ITEM_NAME 
            left join
                EPS_M_APP_STATUS EPS_M_APP_STATUS_1 
            on 
                EPS_T_TRANSFER.ITEM_STATUS = EPS_M_APP_STATUS_1.APP_STATUS_CD
            where 
                (
                    ltrim(EPS_T_PR_HEADER.BU_CD) = '$buCd'
                or 
                    ltrim(EPS_T_PR_HEADER.CHARGED_BU_CD) = '$buCd'
                ) ";
    
    if(count($wherePrSearch)) {
        $query .= "and " . implode(' and ', $wherePrSearch);
    }
    $query .= " order by EPS_T_PR_HEADER.PR_NO ";
    $sql = $conn->query($query);
    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
        $prNo           = $row['PR_NO'];
        $issuedDate     = $row['ISSUED_DATE'];
        $requesterName  = addslashes($row['REQUESTER_NAME']);
        $itemCd         = $row['ITEM_CD'];
        $itemName       = addslashes($row['ITEM_NAME']);
        $deliveryDate   = $row['DELIVERY_DATE'];
        $qty            = $row['QTY'];
        $itemPrice      = $row['ITEM_PRICE'];
        $amount         = $row['AMOUNT'];
        $currencyCd     = $row['CURRENCY_CD'];
        $itemType       = $row['ITEM_TYPE_CD'];
        $accountNo      = $row['ACCOUNT_NO'];
        $rfiNo          = $row['RFI_NO'];
        $unitCd         = $row['UNIT_CD'];
        $supplierCd     = $row['SUPPLIER_CD'];
        $supplierName   = $row['SUPPLIER_NAME'];
        $prStatusName   = $row['PR_STATUS_NAME'];
        $approverName   = $row['APPROVER_NAME'];
        $itemStatusName = $row['ITEM_STATUS_NAME'];
        $prIssuerBu     = $row['REQ_BU_CD'];
        $prChargedBu    = $row['CHARGED_BU_CD'];

        $json_names[] = "{ PR_NO            : '$prNo'
                            ,ISSUED_DATE    : '$issuedDate'
                            ,REQUESTER_NAME : '$requesterName'
                            ,ITEM_CD        : '$itemCd'
                            ,ITEM_NAME      : '$itemName'
                            ,DELIVERY_DATE  : '$deliveryDate'
                            ,QTY            : '$qty'
                            ,ITEM_PRICE     : '$itemPrice'
                            ,AMOUNT         : '$amount'
                            ,CURRENCY_CD    : '$currencyCd'
                            ,ITEM_TYPE_CD   : '$itemType'
                            ,ACCOUNT_NO     : '$accountNo'
                            ,RFI_NO         : '$rfiNo'
                            ,UNIT_CD        : '$unitCd'
                            ,SUPPLIER_CD    : '$supplierCd'
                            ,SUPPLIER_NAME  : '$supplierName'
                            ,PR_STATUS_NAME : '$prStatusName'
                            ,APPROVER_NAME  : '$approverName'
                            ,ITEM_STATUS_NAME: '$itemStatusName'
                            ,REQ_BU_CD      : '$prIssuerBu'
                            ,CHARGED_BU_CD  : '$prChargedBu' }";
    }
}
$json .= implode(',', $json_names); // join the objects by commas;
$json .= ']'; // end the json array element

echo '{success: true, total:'.$prItemCount.',rows:'.$json.'}';
?>
