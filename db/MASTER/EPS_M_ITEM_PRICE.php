<?php session_start();
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."//db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."//db/Common.php";

if (isset($_GET['action'])) {
    $action = $_GET['action'];
}
function convert_param($value)
{
    $result      = stripslashes(strtoupper(trim($value)));
    $result      = str_replace("'", "''", $result);
    $result      = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $result);
    $result      = preg_replace('/\s+/', ' ', $result);
    return $result;
}
function convert_param_original($value)
{
    $result      = stripslashes(trim($value));
    $result      = str_replace("'", "''", $result);
    $result      = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $result);
    $result      = preg_replace('/\s+/', ' ', $result);
    return $result;
}
$itemName   = stripslashes($_REQUEST['term']);
$itemName   = str_replace("'", "''", $itemName);
$itemCdPrm             = convert_param($_GET['itemCdPrm']);
$itemCdPrm             = $itemCdPrm?$itemCdPrm:$_POST['itemCode'];

$itemCategoryPrm       = convert_param($_GET['itemCategoryPrm']);
$itemRevisePricePrm    = convert_param($_GET['itemRevisePricePrm']);
$itemEffectiveDateFromPrm = convert_param($_GET['itemEffectiveDateFromPrm']);
$itemEffectiveDateEndPrm   = convert_param($_GET['itemEffectiveDateEndPrm']);
$itemAttachmentPrm        = convert_param_original($_GET['itemAttachmentPrm']);
/**
 * SELECT EPS_M_ITEM_PRICE
 */
$query_select_m_item = "select
                                    ITEM_CD
                                from
                                    EPS_M_ITEM_PRICE
                                where
                                    ITEM_CD = '$itemCdPrm' ";
$sql_select_m_item = $conn->query($query_select_m_item);
$row_select_m_item = $sql_select_m_item->fetch(PDO::FETCH_ASSOC);

if ($action == 'searchAutoItemPrice') {
    $query = "select     
                EPS_M_ITEM_PRICE.ITEM_CD
                ,EPS_M_ITEM.ITEM_NAME
                ,EPS_M_ITEM_PRICE.UNIT_CD
                ,EPS_M_ITEM_PRICE.SUPPLIER_CD
                ,EPS_M_SUPPLIER.SUPPLIER_NAME
                ,EPS_M_ITEM_PRICE.ITEM_PRICE
                ,EPS_M_ITEM_PRICE.EFFECTIVE_DATE_FROM
                ,EPS_M_ITEM_PRICE.CURRENCY_CD
                ,EPS_M_ITEM_PRICE.ITEM_CATEGORY
            from         
                EPS_M_ITEM_PRICE
            inner join
                EPS_M_ITEM 
            on 
                EPS_M_ITEM_PRICE.ITEM_CD = EPS_M_ITEM.ITEM_CD 
            inner join
                EPS_M_SUPPLIER 
            on 
                EPS_M_SUPPLIER.SUPPLIER_CD = EPS_M_ITEM_PRICE.SUPPLIER_CD
            where     
                (EPS_M_ITEM.ITEM_NAME LIKE '%" . trim($itemName) . "%') 
                and EPS_M_ITEM.ACTIVE_FLAG = 'A'
                and (EPS_M_ITEM_PRICE.EFFECTIVE_DATE_FROM >=
                        (select 
                            max(EFFECTIVE_DATE_FROM)
                        from          
                            EPS_M_ITEM_PRICE T2
                        where      
                            EPS_M_ITEM_PRICE.ITEM_CD = T2.ITEM_CD)) ";
    /**
     * Update on Apr 28, 2016
     */
    /*$query= "select     
                EPS_M_ITEM_PRICE.ITEM_CD
                ,EPS_M_ITEM.ITEM_NAME
                ,EPS_M_ITEM_PRICE.UNIT_CD
                ,EPS_M_ITEM_PRICE.SUPPLIER_CD
                ,EPS_M_SUPPLIER.SUPPLIER_NAME
                ,EPS_M_ITEM_PRICE.ITEM_PRICE
                ,EPS_M_ITEM_PRICE.EFFECTIVE_DATE_FROM
                ,EPS_M_ITEM_PRICE.CURRENCY_CD
            from         
                EPS_M_ITEM_PRICE
            inner join
                EPS_M_ITEM 
            on 
                EPS_M_ITEM_PRICE.ITEM_CD = EPS_M_ITEM.ITEM_CD 
            inner join
                EPS_M_SUPPLIER 
            on 
                EPS_M_SUPPLIER.SUPPLIER_CD = EPS_M_ITEM_PRICE.SUPPLIER_CD
            where     
                (EPS_M_ITEM.ITEM_NAME = '".trim($itemName)."') 
                and EPS_M_ITEM.ACTIVE_FLAG = 'A'
                and (EPS_M_ITEM_PRICE.EFFECTIVE_DATE_FROM >=
                        (select 
                            max(EFFECTIVE_DATE_FROM)
                        from          
                            EPS_M_ITEM_PRICE T2
                        where      
                            EPS_M_ITEM_PRICE.ITEM_CD = T2.ITEM_CD)) ";*/
    $sql = $conn->query($query);
    while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
        $itemCd     = $row['ITEM_CD'];
        $itemName   = $row['ITEM_NAME'];
        $unitCd     = $row['UNIT_CD'];
        $supplierCd = $row['SUPPLIER_CD'];
        $supplierName = $row['SUPPLIER_NAME'];
        $price      = $row['ITEM_PRICE'];
        $currencyCd = $row['CURRENCY_CD'];
        $itemCategory = $row['ITEM_CATEGORY'];

        $result[] = array(
            'id' => $itemCd, 'value' => $itemName, 'itemCd' => $itemCd, 'unitCd' => $unitCd, 'supplierCd' => $supplierCd, 'supplierName' => $supplierName, 'price' => number_format($price), 'currencyCd' => $currencyCd, 'itemCategory' => $itemCategory
        );
    }
    echo json_encode($result);
}elseif ($action == 'Edit') {
    $sUserId            = $_SESSION['sUserId'];
    if ($itemCdPrm != '' && $itemCategoryPrm != '' && $itemRevisePricePrm != '' && $itemEffectiveDateFromPrm != '' && $itemEffectiveDateEndPrm != '' && $itemAttachmentPrm != '' && $row_select_m_item) {
        $itemEffectiveDateFromPrm = explode("/",$itemEffectiveDateFromPrm);
        $itemEffectiveDateFromPrm = implode("",array_reverse($itemEffectiveDateFromPrm));

        $itemEffectiveDateEndPrm = explode("/",$itemEffectiveDateEndPrm);
        $itemEffectiveDateEndPrm = implode("",array_reverse($itemEffectiveDateEndPrm));
        $query = "select     
        EPS_M_ITEM_PRICE.ITEM_CD
        ,EPS_M_ITEM.ITEM_NAME
        ,EPS_M_ITEM_PRICE.UNIT_CD
        ,EPS_M_ITEM_PRICE.SUPPLIER_CD
        ,EPS_M_SUPPLIER.SUPPLIER_NAME
        ,EPS_M_ITEM_PRICE.ITEM_PRICE
        ,EPS_M_ITEM_PRICE.EFFECTIVE_DATE_FROM
        ,EPS_M_ITEM_PRICE.CURRENCY_CD
        ,EPS_M_ITEM_PRICE.ITEM_CATEGORY
    from         
        EPS_M_ITEM_PRICE
    inner join
        EPS_M_ITEM 
    on 
        EPS_M_ITEM_PRICE.ITEM_CD = EPS_M_ITEM.ITEM_CD 
    inner join
        EPS_M_SUPPLIER 
    on 
        EPS_M_SUPPLIER.SUPPLIER_CD = EPS_M_ITEM_PRICE.SUPPLIER_CD
    where     
    EPS_M_ITEM_PRICE.ITEM_CD = '" . trim($itemCdPrm) . "'";
        $sql = $conn->query($query);
        $itemData = $sql->fetch(PDO::FETCH_ASSOC);
        $itemAttachmentPrm = "file://10.82.101.2/EPS/Quotation/".$itemData['SUPPLIER_CD']."/".$itemAttachmentPrm;
        $query_update_m_item = "update
        EPS_M_ITEM_PRICE
    set
        ITEM_CATEGORY = '$itemCategoryPrm'
        ,ITEM_PRICE = '$itemRevisePricePrm'
        ,EFFECTIVE_DATE_FROM = '$itemEffectiveDateFromPrm'
        ,EFFECTIVE_DATE_END = '$itemEffectiveDateEndPrm'
        ,ATTACHMENT_QUOTATION = '$itemAttachmentPrm'
        ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
        ,UPDATE_BY = '$sUserId'
    where
        ITEM_CD = '$itemCdPrm'";
        $conn->query($query_update_m_item);
        $msg = "Success";
    } else if ($itemCdPrm == '' || $itemCategoryPrm == '' || $itemRevisePricePrm == '' || $itemEffectiveDateFromPrm == '' || $itemEffectiveDateEndPrm == '' || $itemAttachmentPrm == '') {
        $msg = "Mandatory_1";
    } else if (!$row_select_m_item) {
        $msg = "NotExist";
    } else {
        $msg = "Undefined";
    }
    echo $msg;
}elseif($action == 'Add') {
    $query_select_m_item = "select
                                    ITEM_CD
                                from
                                    EPS_M_ITEM
                                where
                                    ITEM_CD = '$itemCdPrm' ";
    $sql_select_m_item = $conn->query($query_select_m_item);
    $row_select_m_item = $sql_select_m_item->fetch(PDO::FETCH_ASSOC);
    if (!$row_select_m_item) {
        $msg = "NotExist";
    } else if(count($_POST)) {
        $data = array();
        foreach ($_POST as $key => $value) {
            if (!$value) {
                die("Mandatory_1");
            }
            $data[$key] = convert_param($value);
        }
        extract($data);
        $quotation = "file://10.82.101.2/EPS/Quotation/".$supplierCd."/".$quotation;
        $sUserId            = $_SESSION['sUserId'];
        $currDate = date('Y/m/d h:i:s');
        $query = "
            INSERT INTO [dbo].[EPS_M_ITEM_PRICE]
            ([ITEM_CD]
            ,[SUPPLIER_CD]
            ,[UNIT_CD]
            ,[CURRENCY_CD]
            ,[ITEM_PRICE]
            ,[EFFECTIVE_DATE_FROM]
            ,[EFFECTIVE_DATE_END]
            ,[ATTACHMENT_QUOTATION]
            ,[LEAD_TIME]
            ,[CREATE_DATE]
            ,[CREATE_BY]
            ,[ITEM_CATEGORY])
            VALUES
                ('$itemCd'
                ,'$supplierCd'
                ,'$um'
                ,'$currency'
                ,$itemPrice
                ,'".stringToDate($effectiveDateFrom,'Ymd')."'
                ,'".stringToDate($effectiveDateEnd,'Ymd')."'
                ,'$quotation'
                ,$leadTime
                ,'$currDate'
                ,'$sUserId'
                ,'$itemCategory')";
                // die(nl2br($query));
        $insert= $conn->query($query);
        if ($insert) {
            $msg = "Success";
        } else {
            $msg = "Undefined";
        }        
    }else{
        $msg = "Undefined";
    }
    die(json_encode($msg));
    
}
