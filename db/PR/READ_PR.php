<?php
session_start();
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Constant.php";
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
}
$userId     = $_SESSION['sNPK'];
$param      = $_GET['param'];
$action     = $_GET['action'];
$prNo       = $_GET['prNo'];
$prStatus   = $_GET['prStatus'];
$uploadId   = $_GET['uploadId'];
$dir_source = $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Attachment/Fixed/".$prNo;
$dir_dest   = $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Attachment/Temporary/".$prNo.'-temp/';

$json = '['; // start the json array element
$json_names = array();

if($param=='prHeader'){
    $wherePrHeader = array();
    if($prNo){
        $wherePrHeader[] = "EPS_T_PR_HEADER.PR_NO = '".$prNo."'";
    }
    if($prStatus){
        $wherePrHeader[] = "EPS_T_PR_HEADER.PR_STATUS = '".constant('1030')."'";
        $plantCd       = $_REQUEST['plantVal'];
        $inChargeName   = $_REQUEST['inChargeVal'];
        if($sRoleId == 'ROLE_02'){
            //$wherePrHeader[] .= "EPS_T_PR_HEADER.PLANT_CD = '$sKdPlant'";
            if(strlen($plantCd) == 0 && (!$inChargeName)){
                $wherePrHeader[] .= "EPS_T_PR_HEADER.PROC_IN_CHARGE = '$sUserId' ";
            }
        }
        if(strlen($plantCd) > 0){
            $wherePrHeader[] .= "EPS_T_PR_HEADER.PLANT_CD = '$plantCd' ";
        }
        if($inChargeName){
            $wherePrHeader[] .= "EPS_M_EMPLOYEE_2.NAMA1 LIKE '$inChargeName%' ";
        }
        $wherePrHeader[] .= "EPS_T_PR_DETAIL.ITEM_STATUS = '".constant('1060')."'";
    }
    $count=0;
    $query = "select 
                EPS_T_PR_HEADER.PR_NO
                ,substring(EPS_T_PR_HEADER.ISSUED_DATE,7,2)+'/'+substring(EPS_T_PR_HEADER.ISSUED_DATE,5,2)+'/'+substring(EPS_T_PR_HEADER.ISSUED_DATE,1,4) as ISSUED_DATE
                ,EPS_T_PR_HEADER.BU_CD
                ,EPS_T_PR_HEADER.REQUESTER
                ,EPS_M_EMPLOYEE.NAMA1 as REQUESTER_NAME
                ,EPS_T_PR_HEADER.SECTION_CD
                ,EPS_T_PR_HEADER.PLANT_CD
                ,EPS_M_PLANT.PLANT_NAME
                ,EPS_T_PR_HEADER.COMPANY_CD
                ,EPS_T_PR_HEADER.EXT_NO
                ,EPS_M_COMPANY.COMPANY_NAME
                ,EPS_T_PR_HEADER.REQ_BU_CD
                ,EPS_T_PR_HEADER.CHARGED_BU_CD
                ,EPS_T_PR_HEADER.SPECIAL_TYPE_ID
                ,EPS_T_PR_HEADER.PURPOSE
                ,EPS_T_PR_HEADER.PR_STATUS
                ,EPS_M_APP_STATUS.APP_STATUS_NAME as PR_STATUS_NAME
                ,EPS_M_EMPLOYEE_2.NAMA1 as PROC_IN_CHARGE_NAME
                ,(select 
                    count(*)
                   from          
                    EPS_T_PR_ATTACHMENT
                   where      
                    EPS_T_PR_HEADER.PR_NO = EPS_T_PR_ATTACHMENT.PR_NO) as ATTACHMENT_COUNT
              from
                EPS_T_PR_HEADER
              inner join
                EPS_M_EMPLOYEE
              on 
                EPS_T_PR_HEADER.REQUESTER = EPS_M_EMPLOYEE.NPK 
              left join
                EPS_M_PLANT
              on
                EPS_T_PR_HEADER.PLANT_CD = EPS_M_PLANT.PLANT_CD
              left join
                EPS_M_COMPANY
              on
                EPS_T_PR_HEADER.COMPANY_CD = EPS_M_COMPANY.COMPANY_CD
              inner join
                EPS_M_APP_STATUS
              on
                EPS_T_PR_HEADER.PR_STATUS = EPS_M_APP_STATUS.APP_STATUS_CD 
              inner join
                EPS_T_PR_DETAIL
              on
                EPS_T_PR_HEADER.PR_NO = EPS_T_PR_DETAIL.PR_NO
              left join
                EPS_M_EMPLOYEE EPS_M_EMPLOYEE_2
              on
                EPS_T_PR_HEADER.PROC_IN_CHARGE = EPS_M_EMPLOYEE_2.NPK ";
    if(count($wherePrHeader)) {
        $query .= "where " . implode('and ', $wherePrHeader);
    }
    $query .= "group by 
                EPS_T_PR_HEADER.PR_NO
                ,EPS_T_PR_HEADER.ISSUED_DATE
                ,EPS_T_PR_HEADER.BU_CD
                ,EPS_T_PR_HEADER.REQUESTER
                ,EPS_M_EMPLOYEE.NAMA1
                ,EPS_T_PR_HEADER.SECTION_CD
                ,EPS_T_PR_HEADER.PLANT_CD
                ,EPS_M_PLANT.PLANT_NAME
                ,EPS_T_PR_HEADER.COMPANY_CD
                ,EPS_M_COMPANY.COMPANY_NAME
                ,EPS_T_PR_HEADER.EXT_NO
                ,EPS_T_PR_HEADER.REQ_BU_CD
                ,EPS_T_PR_HEADER.CHARGED_BU_CD
                ,EPS_T_PR_HEADER.SPECIAL_TYPE_ID
                ,EPS_T_PR_HEADER.PURPOSE
                ,EPS_T_PR_HEADER.PR_STATUS
                ,EPS_M_APP_STATUS.APP_STATUS_NAME
                ,EPS_M_EMPLOYEE_2.NAMA1";
    $sql = $conn->query($query);
    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
        $prNo           = $row['PR_NO'];
        $issuedDate     = $row['ISSUED_DATE'];
        $prBuCd         = $row['BU_CD'];
        $requester      = $row['REQUESTER'];
        $requesterName  = addslashes($row['REQUESTER_NAME']);
        $sectionCd      = $row['SECTION_CD'];
        $plant          = $row['PLANT_CD'];
        $plantName      = $row['PLANT_NAME'];
        $company        = $row['COMPANY_CD'];
        $companyName    = $row['COMPANY_NAME'];
        $extNo          = $row['EXT_NO'];
        $prIssuer       = $row['REQ_BU_CD'];
        $prCharged      = $row['CHARGED_BU_CD'];
        $specialType    = $row['SPECIAL_TYPE_ID'];
        $purpose        = $row['PURPOSE'];
        $prStatus       = $row['PR_STATUS'];
        $prStatusName   = $row['PR_STATUS_NAME'];
        $procInCharge   = $row['PROC_IN_CHARGE_NAME'];
        $attachmentCount= $row['ATTACHMENT_COUNT'];

        $json_names[] = "{ PR_NO:'$prNo'
                            ,ISSUED_DATE:'$issuedDate'
                            ,BU_CD:'$prBuCd'
                            ,REQUESTER:'$requester'
                            ,REQUESTER_NAME: '$requesterName'
                            ,SECTION_CD: '$sectionCd'
                            ,PLANT_CD:'$plant'
                            ,PLANT_NAME:'$plantName'
                            ,COMPANY_CD:'$company'
                            ,COMPANY_NAME:'$companyName'
                            ,EXT_NO: '$extNo'
                            ,REQ_BU_CD:'$prIssuer'
                            ,CHARGED_BU_CD:'$prCharged'
                            ,SPECIAL_TYPE_ID:'$specialType'
                            ,PURPOSE:'$purpose'
                            ,PR_STATUS: '$prStatus'
                            ,PR_STATUS_NAME: '$prStatusName'
                            ,PROC_IN_CHARGE_NAME: '$procInCharge'
                            ,ATTACHMENT_COUNT: '$attachmentCount'}";
        $count++;
    }
}
if($param=='prDetail'){
    $wherePrDetail = array();
    if($prNo){
        $wherePrDetail[] = "EPS_T_PR_DETAIL.PR_NO = '".$prNo."'";
    }
    $count = 0;
    $query = "select 
                EPS_T_PR_DETAIL.ITEM_CD
                ,EPS_T_PR_DETAIL.ITEM_NAME
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
                ,EPS_T_PR_DETAIL.SUPPLIER_CD
                ,EPS_T_PR_DETAIL.SUPPLIER_NAME
                ,EPS_T_PR_DETAIL.REMARK
                ,EPS_T_PR_DETAIL.REMARK_2
                ,EPS_T_PR_DETAIL.RUTIN
                ,EPS_T_PR_DETAIL.SUBTITUSI
                ,EPS_T_PR_DETAIL.PR_CHARGED_BU
                ,EPS_T_PR_DETAIL.ITEM_STATUS
                ,EPS_T_PR_DETAIL.REASON_TO_REJECT_ITEM
                ,EPS_T_PR_DETAIL.REJECT_ITEM_BY
                ,EPS_M_EMPLOYEE.NAMA1 as REJECT_ITEM_NAME_BY
                ,(select count (*)
                  from          
                    EPS_T_PR_ATTACHMENT
                  where      
                    EPS_T_PR_HEADER.PR_NO = EPS_T_PR_ATTACHMENT.PR_NO 
                  and 
                    EPS_T_PR_DETAIL.ITEM_NAME = EPS_T_PR_ATTACHMENT.ITEM_NAME) as ATTACHMENT_ITEM_COUNT
				,case 
                    when 
                        CHARINDEX('.', ITEM_NAME) - 1 > 0 
                    then 
                    case 
                        when 
                            ISNUMERIC(SUBSTRING(ITEM_NAME, 1, CHARINDEX('.',ITEM_NAME) - 1)) = 1 
                        then 
                            SUBSTRING(ITEM_NAME, 1, CHARINDEX('.', ITEM_NAME) - 1) 
                    else 
                        999 
                    end 
                 else 
                    999 
                 end 
                    as INDEX_ITEM_NAME
                , Stock AS STOCK
                , OrderPoint AS OP
                , IN_ORDER
              from 
                EPS_T_PR_DETAIL 
              inner join
                EPS_T_PR_HEADER
              on 
                EPS_T_PR_DETAIL.PR_NO = EPS_T_PR_HEADER.PR_NO
              left join
                EPS_M_EMPLOYEE
              on 
                EPS_T_PR_DETAIL.REJECT_ITEM_BY = EPS_M_EMPLOYEE.NPK
                LEFT OUTER JOIN IMS_V_ITEM 
              on SUBSTRING(EPS_T_PR_DETAIL.ITEM_CD, 1 ,9) = IMS_V_ITEM.ItemCode  and SUBSTRING(EPS_T_PR_DETAIL.ITEM_CD,14,2) = IMS_V_ITEM.statusbarang
";
    if(count($wherePrDetail)) {
        $query .= "where " . implode('and ', $wherePrDetail);
    }
	$query .= " order by INDEX_ITEM_NAME, EPS_T_PR_DETAIL.ITEM_NAME";
    $sql = $conn->query($query);
    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
        $itemCd         = $row['ITEM_CD'];
        $itemName       = addslashes($row['ITEM_NAME']);
        $deliveryDate   = $row['DELIVERY_DATE'];
        $qty            = $row['QTY'];
        $itemPrice      = $row['ITEM_PRICE'];
        $amount         = $row['AMOUNT'];
        $currencyCd     = $row['CURRENCY_CD'];
        $itemType       = $row['ITEM_TYPE_CD'];
        $accountCd      = $row['ACCOUNT_NO'];
        $rfiNo          = $row['RFI_NO'];
        $unitCd         = $row['UNIT_CD'];
        $supplierCd     = $row['SUPPLIER_CD'];
        $supplierName   = addslashes($row['SUPPLIER_NAME']);
        $remark         = addslashes($row['REMARK']);
        $remark2         = addslashes($row['REMARK_2']);
        $rutin     = $row['RUTIN'];
        $subtitusi     = $row['SUBTITUSI'];
        $prChargedBu     = $row['PR_CHARGED_BU'];
        $itemStatus     = $row['ITEM_STATUS'];
        $reasonToReject = $row['REASON_TO_REJECT_ITEM'];
        $rejectItemBy   = $row['REJECT_ITEM_BY'];
        $rejectItemNameBy= $row['REJECT_ITEM_NAME_BY'];
        $attachmentItemCount = $row['ATTACHMENT_ITEM_COUNT'];
        $stock = $row['STOCK'];
        $op = $row['OP'];
        $inOrder = $row['IN_ORDER'];
        
        $json_names[] = "{ ITEM_CD: '$itemCd'
                            ,ITEM_NAME:'$itemName'
                            ,DELIVERY_DATE:'$deliveryDate'
                            ,QTY:'$qty'
                            ,ITEM_PRICE:'$itemPrice'
                            ,AMOUNT:'$amount'
                            ,CURRENCY_CD: '$currencyCd'
                            ,ITEM_TYPE_CD:'$itemType'
                            ,ACCOUNT_NO:'$accountCd'
                            ,RFI_NO:'$rfiNo'
                            ,UNIT_CD:'$unitCd'
                            ,SUPPLIER_CD:'$supplierCd'
                            ,SUPPLIER_NAME:'$supplierName'
                            ,REMARK:'$remark'
                            ,REMARK_2:'$remark2'
                            ,RUTIN:'$rutin'
                            ,SUBTITUSI:'$subtitusi'
                            ,PR_CHARGED_BU:'$prChargedBu'
                            ,ITEM_STATUS: '$itemStatus'
                            ,REASON_TO_REJECT_ITEM: '$reasonToReject'
                            ,REJECT_ITEM_BY: '$rejectItemBy'
                            ,REJECT_ITEM_NAME_BY: '$rejectItemNameBy'
                            ,ATTACHMENT_ITEM_COUNT: '$attachmentItemCount'
                            ,STOCK : '$stock'
                            ,OP : '$op'
                            ,IN_ORDER : '$inOrder'
                }";
        $count++;
    }
}
if($param=='prDetailReplicate'){
    $currentDate= date(Ymd);
    $wherePrDetail = array();
    if($prNo){
        $wherePrDetail[] = "EPS_T_PR_DETAIL.PR_NO = '".$prNo."'";
    }
    $count = 0;
    $query = "select 
                EPS_T_PR_DETAIL.ITEM_CD
                ,EPS_T_PR_DETAIL.ITEM_NAME
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
                ,EPS_T_PR_DETAIL.SUPPLIER_CD
                ,EPS_T_PR_DETAIL.SUPPLIER_NAME
                ,EPS_T_PR_DETAIL.REMARK
                ,EPS_T_PR_DETAIL.ITEM_STATUS
                ,EPS_T_PR_DETAIL.REASON_TO_REJECT_ITEM
                ,EPS_T_PR_DETAIL.REJECT_ITEM_BY
                ,EPS_M_EMPLOYEE.NAMA1 as REJECT_ITEM_NAME_BY
                ,(select count (*)
                  from          
                    EPS_T_PR_ATTACHMENT
                  where      
                    EPS_T_PR_HEADER.PR_NO = EPS_T_PR_ATTACHMENT.PR_NO 
                  and 
                    EPS_T_PR_DETAIL.ITEM_NAME = EPS_T_PR_ATTACHMENT.ITEM_NAME) as ATTACHMENT_ITEM_COUNT
                ,case 
                    when 
                        CHARINDEX('.', ITEM_NAME) - 1 > 0 
                    then 
                    case 
                        when 
                            ISNUMERIC(SUBSTRING(ITEM_NAME, 1, CHARINDEX('.',ITEM_NAME) - 1)) = 1 
                        then 
                            SUBSTRING(ITEM_NAME, 1, CHARINDEX('.', ITEM_NAME) - 1) 
                    else 
                        999 
                    end 
                 else 
                    999 
                 end 
                    as INDEX_ITEM_NAME
              from 
                EPS_T_PR_DETAIL 
              inner join
                EPS_T_PR_HEADER
              on 
                EPS_T_PR_DETAIL.PR_NO = EPS_T_PR_HEADER.PR_NO
              left join
                EPS_M_EMPLOYEE
              on 
                EPS_T_PR_DETAIL.REJECT_ITEM_BY = EPS_M_EMPLOYEE.NPK ";
    if(count($wherePrDetail)) {
        $query .= "where " . implode('and ', $wherePrDetail);
    }
    $query .= " order by INDEX_ITEM_NAME, EPS_T_PR_DETAIL.ITEM_NAME";
    $sql = $conn->query($query);
    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
        $itemCd         = $row['ITEM_CD'];
        $itemName       = addslashes($row['ITEM_NAME']);
        $deliveryDate   = $row['DELIVERY_DATE'];
        $deliveryDate   = date("Ymd",strtotime($currentDate."+15 day"));
        $newYear        = substr($deliveryDate, 0,4);
        $newMonth       = substr($deliveryDate, 4,2);
        $newDay         = substr($deliveryDate, 6,2);
        $deliveryDate   = $newDay."/".$newMonth."/".$newYear;
        $qty            = $row['QTY'];
        $itemPrice      = $row['ITEM_PRICE'];
        $amount         = $row['AMOUNT'];
        $currencyCd     = $row['CURRENCY_CD'];
        $itemType       = $row['ITEM_TYPE_CD'];
        $accountCd      = $row['ACCOUNT_NO'];
        $rfiNo          = $row['RFI_NO'];
        $unitCd         = $row['UNIT_CD'];
        $supplierCd     = $row['SUPPLIER_CD'];
        $supplierName   = $row['SUPPLIER_NAME'];
        $remark         = $row['REMARK'];
        $itemStatus     = $row['ITEM_STATUS'];
        $reasonToReject = $row['REASON_TO_REJECT_ITEM'];
        $rejectItemBy   = $row['REJECT_ITEM_BY'];
        $rejectItemNameBy= $row['REJECT_ITEM_NAME_BY'];
        $attachmentItemCount = 0;
        
		/**
         ** SELECT EPS_M_SUPPLIER
         **/
        $query_select_m_supplier = "select
                                        SUPPLIER_CD
                                    from
                                        EPS_M_SUPPLIER
                                    where
                                        SUPPLIER_NAME = '$supllierName'";
        $sql_select_m_supplier = $conn->query($query_select_m_supplier);
        $row_select_m_supplier = $sql_select_m_supplier->fetch(PDO::FETCH_ASSOC);
        $supplierCd = $row_select_m_supplier['SUPPLIER_CD'];
                    
        /**
         * SELECT EPS_M_ITEM
         **/
        /*$query_select_m_item = "select
                                    ITEM_CD
                                from
                                    EPS_M_ITEM
                                where
                                    ITEM_NAME like '%".$itemName."%'
                                    and ACTIVE_FLAG = 'A'";
       
        $sql_select_m_item = $conn->query($query_select_m_item);
        $row_select_m_item = $sql_select_m_item->fetch(PDO::FETCH_ASSOC);
        $itemCd = $row_select_m_item['ITEM_CD'];*/
		
	   /**
        * SELECT EPS_M_ITEM
        **/
        $query_select_m_item = "select
                                    ITEM_CD
                                    ,ITEM_NAME
                                from
                                    EPS_M_ITEM
                                where
                                    (REPLACE(REPLACE(REPLACE(ITEM_NAME, CHAR(13), ''), CHAR(9), ''), ' ', '') = REPLACE('$itemName', ' ', ''))
                                    and ITEM_CD = '".$itemCd."'
                                    and ACTIVE_FLAG = 'A'";
        $sql_select_m_item = $conn->query($query_select_m_item);
        $row_select_m_item = $sql_select_m_item->fetch(PDO::FETCH_ASSOC);
        $itemCd = $row_select_m_item['ITEM_CD'];
        if($row_select_m_item)
        {
            $itemName = $row_select_m_item['ITEM_NAME'];
        }
		
        if($itemCd == ''){
            $itemCd='99';
        }
		if($itemCd != "99")
        {
            $query_select_m_item_price = "select     
                                            EPS_M_ITEM_PRICE.ITEM_CD
                                            ,EPS_M_ITEM_PRICE.UNIT_CD
                                            ,EPS_M_ITEM_PRICE.SUPPLIER_CD
                                            ,EPS_M_ITEM_PRICE.ITEM_PRICE
                                            ,EPS_M_ITEM_PRICE.EFFECTIVE_DATE_FROM
                                            ,EPS_M_ITEM_PRICE.CURRENCY_CD
                                            ,EPS_M_SUPPLIER.SUPPLIER_NAME
                                        from         
                                            EPS_M_ITEM_PRICE 
                                        inner join
                                            EPS_M_SUPPLIER 
                                        on 
                                            EPS_M_ITEM_PRICE.SUPPLIER_CD = EPS_M_SUPPLIER.SUPPLIER_CD
                                        where 
                                            EPS_M_ITEM_PRICE.ITEM_CD = '".$itemCd."' 
                                            and EPS_M_ITEM_PRICE.EFFECTIVE_DATE_FROM <= '".$currentDate."'
                                        order by 
                                                EPS_M_ITEM_PRICE.EFFECTIVE_DATE_FROM desc ";
            $sql_select_m_item_price = $conn->query($query_select_m_item_price);
            $row_select_m_item_price = $sql_select_m_item_price->fetch(PDO::FETCH_ASSOC);
            $itemCd         = $row_select_m_item_price['ITEM_CD'];
            $itemName       = addslashes($row['ITEM_NAME']);
            $unitCd         = $row_select_m_item_price['UNIT_CD'];
            $supplierCd     = $row_select_m_item_price['SUPPLIER_CD'];
            $supplierName   = $row_select_m_item_price['SUPPLIER_NAME'];
            $itemPrice      = $row_select_m_item_price['ITEM_PRICE'];
            $currencyCd     = $row_select_m_item_price['CURRENCY_CD'];
            $amount         = $qty * $itemPrice;
        }
        $json_names[] = "{ ITEM_CD: '$itemCd'
                            ,ITEM_NAME:'$itemName'
                            ,DELIVERY_DATE:'$deliveryDate'
                            ,QTY:'$qty'
                            ,ITEM_PRICE:'$itemPrice'
                            ,AMOUNT:'$amount'
                            ,CURRENCY_CD: '$currencyCd'
                            ,ITEM_TYPE_CD:'$itemType'
                            ,ACCOUNT_NO:'$accountCd'
                            ,RFI_NO:'$rfiNo'
                            ,UNIT_CD:'$unitCd'
                            ,SUPPLIER_CD:'$supplierCd'
                            ,SUPPLIER_NAME:'$supplierName'
                            ,REMARK:'$remark'
                            ,ITEM_STATUS: '$itemStatus'
                            ,REASON_TO_REJECT_ITEM: '$reasonToReject'
                            ,REJECT_ITEM_BY: '$rejectItemBy'
                            ,REJECT_ITEM_NAME_BY: '$rejectItemNameBy'
                            ,ATTACHMENT_ITEM_COUNT: '$attachmentItemCount'}";
        $count++;
    }
}
if($param=='prApprover'){
    $count = 0;
    $query = "select 
                EPS_T_PR_APPROVER.PR_NO
                ,EPS_T_PR_APPROVER.BU_CD
                ,EPS_T_PR_APPROVER.APPROVER_NO
                ,EPS_T_PR_APPROVER.NPK
                ,EPS_M_EMPLOYEE.NAMA1 as APPROVER_NAME
                ,EPS_T_PR_APPROVER.APPROVAL_STATUS
                ,EPS_M_APPROVAL_STATUS.APPROVAL_STATUS_NAME
                ,convert(VARCHAR(24), EPS_T_PR_APPROVER.APPROVAL_DATE, 120) as APPROVAL_DATE
                ,EPS_T_PR_APPROVER.APPROVAL_REMARK
                ,convert(VARCHAR(24), EPS_T_PR_APPROVER.DATE_OF_BYPASS, 120) as DATE_OF_BYPASS
                ,EPS_T_PR_HEADER.SPECIAL_TYPE_ID
              from 
                EPS_T_PR_APPROVER 
              left join
                EPS_M_EMPLOYEE
              on
                EPS_T_PR_APPROVER.NPK = EPS_M_EMPLOYEE.NPK
              left join
                EPS_M_APPROVAL_STATUS
              on
                EPS_T_PR_APPROVER.APPROVAL_STATUS = EPS_M_APPROVAL_STATUS.APPROVAL_STATUS_CD
              left join
                EPS_T_PR_HEADER 
              on 
                EPS_T_PR_APPROVER.PR_NO = EPS_T_PR_HEADER.PR_NO
              where 
                EPS_T_PR_APPROVER.PR_NO ='".$prNo."'
              order by
                EPS_T_PR_APPROVER.APPROVER_NO 
              asc";
    $sql = $conn->query($query);
    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
        $approverNo         = $row['APPROVER_NO'];
        $buCd               = $row['BU_CD'];
        $npk                = $row['NPK'];
        $approverName       = addslashes($row['APPROVER_NAME']);
        $approvalStatus     = $row['APPROVAL_STATUS'];
        $approvalStatusName = $row['APPROVAL_STATUS_NAME'];
        $approvalDate       = $row['APPROVAL_DATE'];
        $approvalRemark     = $row['APPROVAL_REMARK'];
        $dateByPass         = $row['DATE_OF_BYPASS'];
        $specialType        = $row['SPECIAL_TYPE_ID'];
        
        $json_names[] = "{ APPROVER_NO: '$approverNo'
                            ,BU_CD: '$buCd'
                            ,NPK: '$npk'
                            ,APPROVER_NAME:'$approverName'
                            ,APPROVAL_STATUS:'$approvalStatus'
                            ,APPROVAL_STATUS_NAME:'$approvalStatusName'
                            ,APPROVAL_DATE:'$approvalDate'
                            ,APPROVAL_REMARK: '$approvalRemark'
                            ,DATE_OF_BYPASS: '$dateByPass'
                            ,SPECIAL_TYPE_ID: '$specialType'}";
        $count++;
    }
}
if($param=='prApproverEdit'){
    
    $prIssuerBu      = $_GET['prIssuerBu'];
    $count = 0;
    $query = "select     
                EPS_T_PR_APPROVER.PR_NO
                ,EPS_T_PR_APPROVER.BU_CD
                ,EPS_T_PR_APPROVER.APPROVER_NO
                ,EPS_M_PR_APPROVER.NPK
                ,EPS_M_EMPLOYEE.NAMA1 as APPROVER_NAME
                ,EPS_T_PR_APPROVER.APPROVAL_STATUS
                ,EPS_M_APPROVAL_STATUS.APPROVAL_STATUS_NAME
                ,CONVERT(VARCHAR(24), EPS_T_PR_APPROVER.APPROVAL_DATE, 120) as APPROVAL_DATE
                ,EPS_T_PR_APPROVER.APPROVAL_REMARK
                ,CONVERT(VARCHAR(24), EPS_T_PR_APPROVER.DATE_OF_BYPASS, 120) as DATE_OF_BYPASS
                ,EPS_T_PR_HEADER.SPECIAL_TYPE_ID
                ,EPS_T_PR_HEADER.BU_CD as BU_CD_REQUESTER
                ,EPS_T_PR_HEADER.REQ_BU_CD
                ,(select 
                    max(APPROVER_NO)
                  from          
                    EPS_M_PR_APPROVER
                  where      
                    BU_CD = '$prIssuerBu') as NEW_COUNT_APPROVER
              from         
                EPS_T_PR_APPROVER 
              left join
                EPS_M_PR_APPROVER 
              on 
                EPS_M_PR_APPROVER.NPK = EPS_T_PR_APPROVER.NPK 
                and EPS_M_PR_APPROVER.APPROVER_NO = EPS_T_PR_APPROVER.APPROVER_NO
                and EPS_M_PR_APPROVER.BU_CD = EPS_T_PR_APPROVER.BU_CD 
              left join
                EPS_M_EMPLOYEE 
              on 
                EPS_M_PR_APPROVER.NPK = EPS_M_EMPLOYEE.NPK 
              left join
                EPS_M_APPROVAL_STATUS 
              on 
                EPS_T_PR_APPROVER.APPROVAL_STATUS = EPS_M_APPROVAL_STATUS.APPROVAL_STATUS_CD 
              left join
                EPS_T_PR_HEADER 
              on 
                EPS_T_PR_APPROVER.PR_NO = EPS_T_PR_HEADER.PR_NO
              where 
                EPS_T_PR_APPROVER.PR_NO ='".$prNo."'
              order by
                EPS_T_PR_APPROVER.APPROVER_NO asc ";
    $sql = $conn->query($query);
    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
        $approverNo         = $row['APPROVER_NO'];
        $buCd               = $row['BU_CD'];
        $npk                = $row['NPK'];
        $approverName       = addslashes($row['APPROVER_NAME']);
        $approvalStatus     = $row['APPROVAL_STATUS'];
        $approvalStatusName = $row['APPROVAL_STATUS_NAME'];
        $approvalDate       = $row['APPROVAL_DATE'];
        $approvalRemark     = $row['APPROVAL_REMARK'];
        $dateByPass         = $row['DATE_OF_BYPASS'];
        $specialType        = $row['SPECIAL_TYPE_ID'];
        $newCount           = $row['NEW_COUNT_APPROVER'];
        $buCdRequester      = $row['BU_CD_REQUESTER'];
        $prIssuer           = $row['REQ_BU_CD'];
        
        $json_names[] = "{ APPROVER_NO: '$approverNo'
                            ,BU_CD: '$buCd'
                            ,NPK: '$npk'
                            ,APPROVER_NAME:'$approverName'
                            ,APPROVAL_STATUS:'$approvalStatus'
                            ,APPROVAL_STATUS_NAME:'$approvalStatusName'
                            ,APPROVAL_DATE:'$approvalDate'
                            ,APPROVAL_REMARK: '$approvalRemark'
                            ,DATE_OF_BYPASS: '$dateByPass'
                            ,SPECIAL_TYPE_ID: '$specialType'}";
        $count++;
    }
    /*if($count != $newCount)
    {
        $count = $newCount;
    }*/
	
    if($newCount != $count && ($buCdRequester != '3300 ' || $buCdRequester != '3941 '))
    {
        $count = $newCount;
    } 
}
if($param=='prApproverByPass'){
    $count = 0;
    $query = "select 
                EPS_T_PR_APPROVER.APPROVER_NO
                ,EPS_T_PR_APPROVER.APPROVAL_STATUS
                ,EPS_T_PR_APPROVER.APPROVAL_REMARK
                ,EPS_T_PR_HEADER.SPECIAL_TYPE_ID
              from 
                EPS_T_PR_APPROVER 
              left join
                EPS_T_PR_HEADER 
              on 
                EPS_T_PR_APPROVER.PR_NO = EPS_T_PR_HEADER.PR_NO
              where 
                EPS_T_PR_APPROVER.PR_NO = '".$prNo."'
                and EPS_T_PR_APPROVER.APPROVAL_STATUS = '".constant('BP').
              "' order by
                EPS_T_PR_APPROVER.APPROVER_NO 
              asc";
    $sql = $conn->query($query);
    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
        $approverNo     = $row['APPROVER_NO'];
        $approvalStatus = $row['APPROVAL_STATUS'];
        $approvalRemark = $row['APPROVAL_REMARK'];
        $specialType    = $row['SPECIAL_TYPE_ID'];
        
        $json_names[] = "{ APPROVER_NO: '$approverNo'
                            ,APPROVAL_STATUS:'$approvalStatus'
                            ,APPROVAL_REMARK: '$approvalRemark'
                            ,SPECIAL_TYPE_ID: '$specialType'}";
        $count++;
    }
}
if($param=='prApproverDept'){
    $count = 0;
    $query = "select 
                EPS_T_PR_APPROVER.PR_NO
                ,EPS_T_PR_APPROVER.BU_CD
                ,EPS_T_PR_APPROVER.APPROVER_NO
                ,EPS_T_PR_APPROVER.NPK
                ,EPS_M_EMPLOYEE.NAMA1 as APPROVER_NAME
                ,EPS_T_PR_APPROVER.APPROVAL_STATUS
                ,EPS_M_APPROVAL_STATUS.APPROVAL_STATUS_NAME
                ,convert(VARCHAR(24), EPS_T_PR_APPROVER.APPROVAL_DATE, 120) as APPROVAL_DATE
                ,EPS_T_PR_APPROVER.APPROVAL_REMARK
                ,convert(VARCHAR(24), EPS_T_PR_APPROVER.DATE_OF_BYPASS, 120) as DATE_OF_BYPASS
                ,EPS_T_PR_HEADER.SPECIAL_TYPE_ID
              from 
                EPS_T_PR_APPROVER 
              left join
                EPS_M_EMPLOYEE
              on
                EPS_T_PR_APPROVER.NPK = EPS_M_EMPLOYEE.NPK
              left join
                EPS_M_APPROVAL_STATUS
              on
                EPS_T_PR_APPROVER.APPROVAL_STATUS = EPS_M_APPROVAL_STATUS.APPROVAL_STATUS_CD
              left join
                EPS_T_PR_HEADER 
              on 
                EPS_T_PR_APPROVER.PR_NO = EPS_T_PR_HEADER.PR_NO
              where 
                EPS_T_PR_APPROVER.PR_NO ='".$prNo."'
              and 
                EPS_T_PR_APPROVER.NPK != (select NPK from EPS_M_PR_SPECIAL_APPROVER where SPECIAL_APPROVER_CD = '001')
              order by
                EPS_T_PR_APPROVER.APPROVER_NO 
              asc";
    $sql = $conn->query($query);
    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
        $approverNo         = $row['APPROVER_NO'];
        $buCd               = $row['BU_CD'];
        $npk                = $row['NPK'];
        $approverName       = addslashes($row['APPROVER_NAME']);
        $approvalStatus     = $row['APPROVAL_STATUS'];
        $approvalStatusName = $row['APPROVAL_STATUS_NAME'];
        $approvalDate       = $row['APPROVAL_DATE'];
        $approvalRemark     = $row['APPROVAL_REMARK'];
        $dateByPass         = $row['DATE_OF_BYPASS'];
        $specialType        = $row['SPECIAL_TYPE_ID'];
        
        $json_names[] = "{ APPROVER_NO: '$approverNo'
                            ,BU_CD: '$buCd'
                            ,NPK: '$npk'
                            ,APPROVER_NAME:'$approverName'
                            ,APPROVAL_STATUS:'$approvalStatus'
                            ,APPROVAL_STATUS_NAME:'$approvalStatusName'
                            ,APPROVAL_DATE:'$approvalDate'
                            ,APPROVAL_REMARK: '$approvalRemark'
                            ,DATE_OF_BYPASS: '$dateByPass'
                            ,SPECIAL_TYPE_ID: '$specialType'}";
        $count++;
    }
}
if($param=='prApproverSpecial'){
    $count = 0;
    $query = "select top 1
                EPS_T_PR_APPROVER.PR_NO
                ,EPS_T_PR_APPROVER.BU_CD
                ,EPS_T_PR_APPROVER.APPROVER_NO
                ,EPS_T_PR_APPROVER.NPK
                ,EPS_M_EMPLOYEE.NAMA1 as APPROVER_NAME
                ,EPS_T_PR_APPROVER.APPROVAL_STATUS
                ,EPS_M_APPROVAL_STATUS.APPROVAL_STATUS_NAME
                ,convert(VARCHAR(24), EPS_T_PR_APPROVER.APPROVAL_DATE, 120) as APPROVAL_DATE
                ,EPS_T_PR_APPROVER.APPROVAL_REMARK
                ,convert(VARCHAR(24), EPS_T_PR_APPROVER.DATE_OF_BYPASS, 120) as DATE_OF_BYPASS
                ,EPS_T_PR_HEADER.SPECIAL_TYPE_ID
              from 
                EPS_T_PR_APPROVER 
              left join
                EPS_M_EMPLOYEE
              on
                EPS_T_PR_APPROVER.NPK = EPS_M_EMPLOYEE.NPK
              left join
                EPS_M_APPROVAL_STATUS
              on
                EPS_T_PR_APPROVER.APPROVAL_STATUS = EPS_M_APPROVAL_STATUS.APPROVAL_STATUS_CD
              left join
                EPS_T_PR_HEADER 
              on 
                EPS_T_PR_APPROVER.PR_NO = EPS_T_PR_HEADER.PR_NO
              where 
                EPS_T_PR_APPROVER.PR_NO ='".$prNo."'
              and 
                EPS_T_PR_APPROVER.NPK = (select NPK from EPS_M_PR_SPECIAL_APPROVER where SPECIAL_APPROVER_CD = '001')      
              order by
                EPS_T_PR_APPROVER.APPROVER_NO 
              desc";
    $sql = $conn->query($query);
    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
        $approverNo         = $row['APPROVER_NO'];
        $buCd               = $row['BU_CD'];
        $npk                = $row['NPK'];
        $approverName       = addslashes($row['APPROVER_NAME']);
        $approvalStatus     = $row['APPROVAL_STATUS'];
        $approvalStatusName = $row['APPROVAL_STATUS_NAME'];
        $approvalDate       = $row['APPROVAL_DATE'];
        $approvalRemark     = $row['APPROVAL_REMARK'];
        $dateByPass         = $row['DATE_OF_BYPASS'];
        $specialType        = $row['SPECIAL_TYPE_ID'];
        
        $json_names[] = "{ APPROVER_NO: '$approverNo'
                            ,BU_CD: '$buCd'
                            ,NPK: '$npk'
                            ,APPROVER_NAME:'$approverName'
                            ,APPROVAL_STATUS:'$approvalStatus'
                            ,APPROVAL_STATUS_NAME:'$approvalStatusName'
                            ,APPROVAL_DATE:'$approvalDate'
                            ,APPROVAL_REMARK: '$approvalRemark'
                            ,DATE_OF_BYPASS: '$dateByPass'
                            ,SPECIAL_TYPE_ID: '$specialType'}";
        $count++;
    }
}
if($param=='prAttachment'){
    $wherePrAttachment = array();
    if($prNo){
        $wherePrAttachment[] = "PR_NO = '".$prNo."'";
    }
    if($_REQUEST['itemCd']){
        $wherePrAttachment[] = "ITEM_CD = '".$_REQUEST['itemCd']."'";
    }
    if($_REQUEST['itemName']){
        $wherePrAttachment[] = "ITEM_NAME = '".stripslashes($_REQUEST['itemName'])."'";
    }
    /** Check existing fixed folder to create temporary folder **/ 
    if($action=='edit'){
        if(is_dir($dir_source)){                    
            if(!is_dir($dir_dest)){                 
                mkdir($dir_dest,0777);              
            }
        }
    }
    $count = 0;
    $query = "select 
                PR_NO
                ,ITEM_CD
                ,ITEM_NAME
                ,FILE_NAME
                ,FILE_TYPE
                ,FILE_SIZE
              from 
                EPS_T_PR_ATTACHMENT ";
    if(count($wherePrAttachment)) {
        $query .= "where " . implode('and ', $wherePrAttachment);
    }
    $sql = $conn->query($query);
    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
        $prNo       = $row['PR_NO'];
        $itemCd     = $row['ITEM_CD'];
        $itemName   = addslashes($row['ITEM_NAME']);
        $fileName   = $row['FILE_NAME'];
        $fileType   = $row['FILE_TYPE'];
        $fileSize   = $row['FILE_SIZE'];
        
        $json_names[] = "{  PR_NO: '$prNo'
                            ,ITEM_CD: '$itemCd'
                            ,ITEM_NAME:'$itemName'
                            ,FILE_NAME:'$fileName'
                            ,FILE_TYPE:'$fileType'
                            ,FILE_SIZE:'$fileSize'}";
        $count++;
        if($action=='edit'){
            copy($dir_source.'/'.$fileName,$dir_dest.$fileName);
        }
    }
}
if($param=='prUpload'){
    $count = 0;
    $query = "select 
                PR_NO
                ,ITEM_CD
                ,ITEM_NAME
                ,substring(DELIVERY_DATE, 7, 2) + '/' + substring(DELIVERY_DATE, 5, 2) + '/' + substring(DELIVERY_DATE, 1, 4) as DELIVERY_DATE 
                ,QTY
                ,ITEM_PRICE
                ,AMOUNT
                ,ITEM_TYPE_CD
                ,ACCOUNT_NO
                ,UNIT_CD
                ,SUPPLIER_CD
                ,SUPPLIER_NAME
              from 
                EPS_T_PR_UPLOAD
              where 
                UPLOAD_ID ='".$uploadId."'";
    $sql = $conn->query($query);
    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
        $prNo           = $row['PR_NO'];
        $itemCd         = $row['ITEM_CD'];
        $itemName       = $row['ITEM_NAME'];
        $deliveryDate   = $row['DELIVERY_DATE'];
        $qty            = $row['QTY'];
        $itemPrice      = $row['ITEM_PRICE'];
        $amount         = $row['AMOUNT'];
        $itemType       = $row['ITEM_TYPE_CD'];
        $accountNo      = $row['ACCOUNT_NO'];
        $unitCd         = $row['UNIT_CD'];
        $supplierCd     = $row['SUPPLIER_CD'];
        $supplierName   = $row['SUPPLIER_NAME'];
        
        $json_names[] = "{ PR_NO: '$prNo'
                            ,ITEM_CD: '$itemCd'
                            ,ITEM_NAME:'$itemName'
                            ,DELIVERY_DATE:'$deliveryDate'
                            ,QTY:'$qty'
                            ,ITEM_PRICE:'$itemPrice'
                            ,AMOUNT:'$amount'
                            ,ITEM_TYPE_CD:'$itemType'
                            ,ACCOUNT_NO:'$accountNo'
                            ,UNIT_CD:'$unitCd'
                            ,SUPPLIER_CD:'$supplierCd'
                            ,SUPPLIER_NAME:'$supplierName'}";
        $count++;
    }
}
if($param=='prTransfer'){
    $wherePrTransfer        = array();
    $wherePrTransferSearch  = array();
    $prNoSearchVal          = $_REQUEST['prNoSearchVal'];
    $requesterSearchVal     = $_REQUEST['requesterSearchVal'];
    $procAcceptSearchVal    = $_REQUEST['procAcceptSearchVal'];
    $procInChargeSearchVal  = $_REQUEST['procInChargeSearchVal'];
    
    /**
     * Search by accepted date 
     */
    if($sRoleId == 'ROLE_02' || $sRoleId == 'ROLE_03'){
        if((!$prNoSearchVal) && (!$requesterSearchVal) && (!$procAcceptSearchVal) && (!$procInChargeSearchVal)){
            $wherePrTransfer[] .= "CONVERT(VARCHAR(10), EPS_T_PR_HEADER.PROC_ACCEPT_DATE, 103) = CONVERT(VARCHAR(10), GETDATE(), 103)";
        }
    }
    /**
     * Check criteria value if blank criteria
     */
    if($sRoleId == 'ROLE_02' && (!$prNoSearchVal) && (!$requesterSearchVal) && (!$procAcceptSearchVal) && (!$procInChargeSearchVal)){
        $wherePrTransfer[] .= "EPS_T_PR_TRANSFER.CREATE_BY = '$userId'";
    }
    /**
     * Check criteria value 
     */
    if($prNoSearchVal){
        $wherePrTransferSearch[] .= "EPS_T_PR_TRANSFER.PR_NO LIKE '$prNoSearchVal%'";
    }
    if($requesterSearchVal){
        $wherePrTransferSearch[] .= "EPS_M_EMPLOYEE.NAMA1 LIKE '$requesterSearchVal%'";
    }
    if($procAcceptSearchVal){
        $wherePrTransferSearch[] .= "CONVERT(VARCHAR(10), EPS_T_PR_HEADER.PROC_ACCEPT_DATE, 103) = '$procAcceptSearchVal'";
    }
    if($procInChargeSearchVal){
        $wherePrTransferSearch[] .= "EPS_M_EMPLOYEE_2.NAMA1 LIKE '$procInChargeSearchVal%'";
    }
    $count = 0;
    $query = "select 
                EPS_T_PR_TRANSFER.REQUESTER
                ,EPS_M_EMPLOYEE.NAMA1 as REQUESTER_NAME
                ,EPS_T_PR_HEADER.BU_CD
                ,EPS_M_PLANT.PLANT_NAME 
                ,EPS_M_COMPANY.COMPANY_NAME
                ,EPS_T_PR_HEADER.REQ_BU_CD
                ,EPS_T_PR_TRANSFER.CHARGED_BU_CD
                ,substring(EPS_T_PR_HEADER.ISSUED_DATE,7,2)+'/'+substring(EPS_T_PR_HEADER.ISSUED_DATE,5,2)+'/'+substring(EPS_T_PR_HEADER.ISSUED_DATE,1,4) as ISSUED_DATE
                ,EPS_T_PR_HEADER.SPECIAL_TYPE_ID
                ,EPS_T_PR_HEADER.PURPOSE
                ,EPS_T_PR_TRANSFER.PR_NO
                ,EPS_T_PR_TRANSFER.ITEM_CD
                ,EPS_T_PR_TRANSFER.ITEM_NAME
                ,substring(EPS_T_PR_TRANSFER.DELIVERY_DATE, 7, 2) + '/' + substring(EPS_T_PR_TRANSFER.DELIVERY_DATE, 5, 2) + '/' + substring(EPS_T_PR_TRANSFER.DELIVERY_DATE, 1, 4) as DELIVERY_DATE 
                ,EPS_T_PR_TRANSFER.QTY
                ,EPS_T_PR_TRANSFER.ITEM_PRICE
                ,EPS_T_PR_TRANSFER.AMOUNT
                ,EPS_T_PR_TRANSFER.ITEM_TYPE_CD
                ,EPS_T_PR_TRANSFER.ACCOUNT_NO
                ,EPS_T_PR_TRANSFER.RFI_NO
                ,EPS_T_PR_TRANSFER.UNIT_CD
                ,EPS_T_PR_TRANSFER.SUPPLIER_CD
                ,EPS_T_PR_TRANSFER.SUPPLIER_NAME
                ,convert(VARCHAR(24), EPS_T_PR_HEADER.PROC_ACCEPT_DATE, 120) as PROC_ACCEPT_DATE
                ,(select 
                    count(*)
                  from     
                    EPS_T_PR_ATTACHMENT
                  where      
                    EPS_T_PR_TRANSFER.PR_NO = EPS_T_PR_ATTACHMENT.PR_NO 
                    and EPS_T_PR_TRANSFER.ITEM_CD = EPS_T_PR_ATTACHMENT.ITEM_CD 
                    and EPS_T_PR_TRANSFER.ITEM_NAME = EPS_T_PR_ATTACHMENT.ITEM_NAME) as ATTACHMENT_COUNT
                ,EPS_M_EMPLOYEE_2.NAMA1 as CREATE_BY
				,convert(VARCHAR(24), EPS_T_PR_TRANSFER.CREATE_DATE, 120) as CREATE_DATE
              from 
                EPS_T_PR_TRANSFER
              left join
                EPS_M_EMPLOYEE
              on
                EPS_T_PR_TRANSFER.REQUESTER = EPS_M_EMPLOYEE.NPK
              left join 
                EPS_T_PR_HEADER
              on
                EPS_T_PR_TRANSFER.PR_NO = EPS_T_PR_HEADER.PR_NO
              left join 
                EPS_M_PLANT 
              on 
                EPS_T_PR_HEADER.PLANT_CD = EPS_M_PLANT.PLANT_CD 
              left join 
                EPS_M_COMPANY 
              on 
                EPS_T_PR_HEADER.COMPANY_CD = EPS_M_COMPANY.COMPANY_CD
              left join
                EPS_M_EMPLOYEE EPS_M_EMPLOYEE_2
              on
                EPS_T_PR_TRANSFER.CREATE_BY = EPS_M_EMPLOYEE_2.NPK ";
    if(count($wherePrTransfer)) {
        $query .= "where " . implode('and ', $wherePrTransfer);
    }
    if(count($wherePrTransfer)) {
        if(count($wherePrTransferSearch)) {
            $query .= "and " . implode('and ', $wherePrTransferSearch);
        }
    }else{
        if(count($wherePrTransferSearch)) {
            $query .= "where " . implode('and ', $wherePrTransferSearch);
        }
    }
    $query .= " order by 
                    EPS_T_PR_HEADER.PR_NO, EPS_T_PR_TRANSFER.ITEM_NAME
                asc";
    $sql = $conn->query($query);
    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
        $requester      = $row['REQUESTER'];
        $requesterName  = addslashes($row['REQUESTER_NAME']);
        $buCd           = $row['BU_CD'];
        $plantName      = $row['PLANT_NAME'];
        $companyName    = $row['COMPANY_NAME'];
        $prIssuer       = $row['REQ_BU_CD'];
        $prCharged      = $row['CHARGED_BU_CD'];
        $prDate         = $row['ISSUED_DATE'];
        $specialType    = $row['SPECIAL_TYPE_ID'];
        $purpose        = $row['PURPOSE'];
        $prNo           = $row['PR_NO'];
        $itemCd         = $row['ITEM_CD'];
        $itemName       = addslashes($row['ITEM_NAME']);
        $deliveryDate   = $row['DELIVERY_DATE'];
        $qty            = $row['QTY'];
        $itemPrice      = $row['ITEM_PRICE'];
        $amount         = $row['AMOUNT'];
        $itemType       = $row['ITEM_TYPE_CD'];
        $accountNo      = $row['ACCOUNT_NO'];
        $rfiNo          = $row['RFI_NO'];
        $unitCd         = $row['UNIT_CD'];
        $supplierCd     = $row['SUPPLIER_CD'];
        $supplierName   = $row['SUPPLIER_NAME'];
        $procAcceptDate = $row['PROC_ACCEPT_DATE'];
        $attachmentCount= $row['ATTACHMENT_COUNT'];
        $createBy       = $row['CREATE_BY'];
        $createDate     = $row['CREATE_DATE'];
		if($itemType == '3'){
            $prCharged = 'N1000';
        }
        $json_names[] = "{ REQUESTER        : '$requester'
                            ,REQUESTER_NAME : '$requesterName'
                            ,BU_CD          : '$buCd'
                            ,PLANT_NAME     : '$plantName'
                            ,COMPANY_NAME   : '$companyName'
                            ,REQ_BU_CD      : '$prIssuer'
                            ,CHARGED_BU_CD  : '$prCharged'
                            ,ISSUED_DATE    : '$prDate'
                            ,SPECIAL_TYPE_ID: '$specialType'
                            ,PURPOSE        : '$purpose'
                            ,PR_NO          : '$prNo'
                            ,ITEM_CD        : '$itemCd'
                            ,ITEM_NAME      :'$itemName'
                            ,DELIVERY_DATE  :'$deliveryDate'
                            ,QTY            :'$qty'
                            ,ITEM_PRICE     :'$itemPrice'
                            ,AMOUNT         :'$amount'
                            ,ITEM_TYPE_CD   :'$itemType'
                            ,ACCOUNT_NO     :'$accountNo'
                            ,RFI_NO         :'$rfiNo'
                            ,UNIT_CD        :'$unitCd'
                            ,SUPPLIER_CD    :'$supplierCd'
                            ,SUPPLIER_NAME  :'$supplierName'
                            ,PROC_ACCEPT_DATE: '$procAcceptDate'
                            ,ATTACHMENT_COUNT: '$attachmentCount'
                            ,CREATE_BY      : '$createBy'
                            ,CREATE_DATE    : '$createDate'}";
        $count++;
    }
}
$json .= implode(',', $json_names); // join the objects by commas;
$json .= ']'; // end the json array element
echo '{success: true, rows:'.$json.', count:'.$count.'}';
?>
