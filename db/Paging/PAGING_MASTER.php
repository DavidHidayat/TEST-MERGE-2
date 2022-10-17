<?php session_start();
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Common.php";
$criteria   = $_GET['criteria'];
$limit      = $_REQUEST['limit'];
$page       = $_REQUEST['page'];
$start      = $_REQUEST['start'];
$next       = $limit*$page;

$json = '['; // start the json array element
$json_names = array();

if($criteria == 'Item'){
    $whereItem  = array();
    $itemCd     = stripslashes($_REQUEST['itemCdVal']);
    $itemName   = stripslashes($_REQUEST['itemNameVal']);
    $itemGroup  = stripslashes($_REQUEST['itemGroupVal']);
    if($itemCd){
        $whereItem[] = "EPS_M_ITEM.ITEM_CD = '".$itemCd."'";
    }
    if($itemName){
        $whereItem[] = "EPS_M_ITEM.ITEM_NAME like '".$itemName."%'";
    }
    if($itemGroup){
        $whereItem[] = "EPS_M_ITEM.ITEM_GROUP_CD = '".$itemGroup."'";
    }
    $query = "select count
                    (*)
                as
                    COUNT_DATA
                from
                    EPS_M_ITEM ";
    if(count($whereItem)) {
        $query .= "where " . implode('and ', $whereItem);
    }
    $sql = $conn->query($query);
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    $countData = $row['COUNT_DATA'];
    if ($page != '1'){
        if($next > $countData){
            $limit = $countData -(($page-1) * $limit);
        }
    }
    $query = "select * from 
                (select top $limit * from          
                    (select top $next 
                        ITEM_CD
                        ,ITEM_NAME
                        ,ITEM_GROUP_CD
                        ,convert(VARCHAR(24), CREATE_DATE, 120) as CREATE_DATE
                        ,CREATE_BY
                        ,convert(VARCHAR(24), UPDATE_DATE, 120) as UPDATE_DATE
                        ,UPDATE_BY
                    from 
                        EPS_M_ITEM ";
    if(count($whereItem)) {
        $query .= "where " . implode('and ', $whereItem);
    }
    $query .= "     order by 
                        ITEM_CD asc) as EPS_M_ITEM
                 order by ITEM_CD desc) as EPS_M_ITEM
              order by ITEM_CD";
    $sql = $conn->query($query);
    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
         $json_names[] = "{ 
                            ITEM_CD     :'".$row['ITEM_CD']."'
                            ,ITEM_NAME  :'".$row['ITEM_NAME']."'
                            ,ITEM_GROUP_CD  :'".$row['ITEM_GROUP_CD']."'
                            ,CREATE_DATE:'".$row['CREATE_DATE']."'
                            ,CREATE_BY  :'".$row['CREATE_BY']."'
                            ,UPDATE_DATE:'".$row['UPDATE_DATE']."'
                            ,UPDATE_BY  :'".$row['UPDATE_BY']."'
                          }";
    }
}
if($criteria == 'ItemGroup'){
    $whereItemGroup = array();
    $itemGroupCd    = $_REQUEST['itemGroupCdVal'];
    $itemGroupName  = $_REQUEST['itemGroupNameVal'];
    if($itemGroupCd){
        $whereItemGroup[] = "EPS_M_ITEM_GROUP.ITEM_GROUP_CD = '".$itemGroupCd."'";
    }
    if($itemGroupName){
        $whereItemGroup[] = "EPS_M_ITEM_GROUP.ITEM_GROUP_NAME like '".$itemGroupName."%'";
    }
    $query = "select count
            (*)
                as
                    COUNT_DATA
                from
                    EPS_M_ITEM_GROUP ";
    if(count($whereItemGroup)) {
        $query .= "where " . implode('and ', $whereItemGroup);
    }
    $sql = $conn->query($query);
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    $countData = $row['COUNT_DATA'];
    if ($page != '1'){
        if($next > $countData){
            $limit = $countData -(($page-1) * $limit);
        }
    }
    $query = "select * from 
                (select top $limit * from          
                    (select top $next 
                        ITEM_GROUP_CD
                        ,ITEM_GROUP_NAME
                        ,convert(VARCHAR(24), CREATE_DATE, 120) as CREATE_DATE
                        ,CREATE_BY
                        ,convert(VARCHAR(24), UPDATE_DATE, 120) as UPDATE_DATE
                        ,UPDATE_BY
                    from 
                        EPS_M_ITEM_GROUP ";
    if(count($whereItemGroup)) {
        $query .= "where " . implode('and ', $whereItemGroup);
    }
    $query .= "     order by 
                        ITEM_GROUP_CD asc) as EPS_M_ITEM_GROUP
                 order by ITEM_GROUP_CD desc) as EPS_M_ITEM_GROUP
               order by ITEM_GROUP_CD";
    $sql = $conn->query($query);
    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
         $json_names[] = "{ 
                            ITEM_GROUP_CD     :'".$row['ITEM_GROUP_CD']."'
                            ,ITEM_GROUP_NAME  :'".$row['ITEM_GROUP_NAME']."'
                            ,CREATE_DATE      :'".$row['CREATE_DATE']."'
                            ,CREATE_BY        :'".$row['CREATE_BY']."'
                            ,UPDATE_DATE      :'".$row['UPDATE_DATE']."'
                            ,UPDATE_BY        :'".$row['UPDATE_BY']."'
                          }";
    }
}
if($criteria == 'ItemPrice'){
    $itemCd     = $_REQUEST['itemCdVal'];
    $itemName   = stripslashes($_REQUEST['itemNameVal']);
    $unit       = $_REQUEST['unitVal'];
    $price      = $_REQUEST['priceVal'];
    $effectiveDate= encodeDate($_REQUEST['effectiveDateVal']);
    $supplierCd = $_REQUEST['supplierCdVal'];
    $supplierName= $_REQUEST['supplierNameVal'];
    $whereItemPrice = array();
    if($itemCd){
        $whereItemPrice[] = "EPS_M_ITEM_PRICE.ITEM_CD = '".$itemCd."'";
    }
    if($itemName){
        $whereItemPrice[] = "EPS_M_ITEM.ITEM_NAME like '".$itemName."%'";
    }
    if($unit){
        $whereItemPrice[] = "EPS_M_ITEM_PRICE.UNIT_CD = '".$unit."'";
    }
    if($price){
        $whereItemPrice[] = "EPS_M_ITEM_PRICE.ITEM_PRICE like '".$price."%'";
    }
    if($effectiveDate){
        $whereItemPrice[] = "EPS_M_ITEM_PRICE.EFFECTIVE_DATE_FROM = '".$effectiveDate."'";
    }
    if($supplierCd){
        $whereItemPrice[] = "EPS_M_ITEM_PRICE.SUPPLIER_CD = '".$supplierCd."'";
    }
    if($supplierName){
        $whereItemPrice[] = "EPS_M_SUPPLIER.SUPPLIER_NAME like '".$supplierName."%'";
    }
    $query = "select count
                    (*)
              as
                    COUNT_DATA
              from
                    EPS_M_ITEM_PRICE
              inner join
                    EPS_M_ITEM
              on
                    EPS_M_ITEM_PRICE.ITEM_CD = EPS_M_ITEM.ITEM_CD
              left join
                    EPS_M_SUPPLIER
              on
                    EPS_M_ITEM_PRICE.SUPPLIER_CD = EPS_M_SUPPLIER.SUPPLIER_CD ";
    if(count($whereItemPrice)) {
        $query .= "where " . implode('and ', $whereItemPrice);
    }
    $sql = $conn->query($query);
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    $countData = $row['COUNT_DATA'];
    if ($page != '1'){
        if($next > $countData){
            $limit = $countData -(($page-1) * $limit);
        }
    }
    $query = "select * from 
                (select top $limit * from          
                    (select top $next 
                        EPS_M_ITEM_PRICE.ITEM_CD
                        ,EPS_M_ITEM.ITEM_NAME
                        ,EPS_M_ITEM_PRICE.UNIT_CD
                        ,EPS_M_ITEM_PRICE.ITEM_PRICE
                        ,EPS_M_ITEM_PRICE.EFFECTIVE_DATE_FROM
                        ,EPS_M_ITEM_PRICE.SUPPLIER_CD
                        ,EPS_M_SUPPLIER.SUPPLIER_NAME
                        ,convert(VARCHAR(24), EPS_M_ITEM_PRICE.CREATE_DATE, 120) as CREATE_DATE
                        ,EPS_M_ITEM_PRICE.CREATE_BY
                        ,convert(VARCHAR(24), EPS_M_ITEM_PRICE.UPDATE_DATE, 120) as UPDATE_DATE
                        ,EPS_M_ITEM_PRICE.UPDATE_BY
                    from 
                        EPS_M_ITEM_PRICE
                    inner join
                        EPS_M_ITEM
                    on
                        EPS_M_ITEM_PRICE.ITEM_CD = EPS_M_ITEM.ITEM_CD
                    left join
                        EPS_M_SUPPLIER
                    on
                        EPS_M_ITEM_PRICE.SUPPLIER_CD = EPS_M_SUPPLIER.SUPPLIER_CD ";
    if(count($whereItemPrice)) {
        $query .= "where " . implode('and ', $whereItemPrice);
    }
    $query .= "     order by 
                        EPS_M_ITEM_PRICE.ITEM_CD asc) as EPS_M_ITEM_PRICE
                order by EPS_M_ITEM_PRICE.ITEM_CD desc) as EPS_M_ITEM_PRICE
              order by ITEM_CD";
    $sql = $conn->query($query);
    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
         $json_names[] = "{ 
                            ITEM_CD     :'".$row['ITEM_CD']."'
                            ,ITEM_NAME  :'".$row['ITEM_NAME']."'
                            ,UNIT_CD    :'".$row['UNIT_CD']."'
                            ,ITEM_PRICE :'".$row['ITEM_PRICE']."'
                            ,EFFECTIVE_DATE_FROM :'".$row['EFFECTIVE_DATE_FROM']."'
                            ,SUPPLIER_CD:'".$row['SUPPLIER_CD']."'
                            ,SUPPLIER_NAME :'".$row['SUPPLIER_NAME']."'
                            ,CREATE_DATE:'".$row['CREATE_DATE']."'
                            ,CREATE_BY  :'".$row['CREATE_BY']."'
                            ,UPDATE_DATE:'".$row['UPDATE_DATE']."'
                            ,UPDATE_BY  :'".$row['UPDATE_BY']."'
                          }";
    }
}
if($criteria == 'PrApproverMst'){
    $buCd               = $_REQUEST['buCdVal'];
    $buName             = $_REQUEST['buNameVal'];
    $approverNo         = $_REQUEST['approverNoVal'];
    $approverNpk        = $_REQUEST['approverNpkVal'];
    $approverName       = $_REQUEST['approverNameVal'];
    $approverLimit      = $_REQUEST['approverLimitVal'];
    $wherePrApproverMst = array();
    if($buCd){
        $wherePrApproverMst[] = "ltrim(EPS_M_PR_APPROVER.BU_CD) = '".$buCd."'";
    }
    if($buName){
        $wherePrApproverMst[] = "EPS_M_TBUNIT.NMBU1 = '".$buName."'";
    }
    if($approverNo){
        $wherePrApproverMst[] = "EPS_M_PR_APPROVER.APPROVER_NO = '".$approverNo."'";
    }
    if($approverNpk){
        $wherePrApproverMst[] = "EPS_M_PR_APPROVER.NPK = '".$approverNpk."'";
    }
    if($approverName){
        $wherePrApproverMst[] = "EPS_M_EMPLOYEE.NAMA1 LIKE '%".$approverName."%'";
    }
    if($approverLimit){
        $wherePrApproverMst[] = "EPS_M_LIMIT.LIMIT_AMOUNT = '".$approverLimit."'";
    }
    $query = "select count
                    (*)
              as
                    COUNT_DATA
              from
                EPS_M_PR_APPROVER
              left join
                EPS_M_EMPLOYEE
              on
                EPS_M_PR_APPROVER.NPK = EPS_M_EMPLOYEE.NPK
              left join
                EPS_M_TBUNIT
              on
                EPS_M_PR_APPROVER.BU_CD = EPS_M_TBUNIT.KDBU 
              left join
                EPS_M_LIMIT 
              on 
                EPS_M_PR_APPROVER.APPROVER_LEVEL = EPS_M_LIMIT.LEVEL_ID ";
    if(count($wherePrApproverMst)) {
        $query .= "where " . implode('and ', $wherePrApproverMst);
    }
    $sql = $conn->query($query);
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    $countData = $row['COUNT_DATA'];
    $query = "select 
                EPS_M_PR_APPROVER.BU_CD
                ,EPS_M_TBUNIT.NMBU1 as BU_NAME
                ,EPS_M_PR_APPROVER.APPROVER_NO
                ,EPS_M_PR_APPROVER.NPK
                ,EPS_M_EMPLOYEE.NAMA1 as APPROVER_NAME
                ,EPS_M_PR_APPROVER.APPROVER_LEVEL
                ,EPS_M_LIMIT.LIMIT_AMOUNT
              from
                EPS_M_PR_APPROVER
              left join
                EPS_M_EMPLOYEE
              on
                EPS_M_PR_APPROVER.NPK = EPS_M_EMPLOYEE.NPK
              left join
                EPS_M_TBUNIT
              on
                EPS_M_PR_APPROVER.BU_CD = EPS_M_TBUNIT.KDBU 
              left join
                EPS_M_LIMIT 
              on 
                EPS_M_PR_APPROVER.APPROVER_LEVEL = EPS_M_LIMIT.LEVEL_ID ";
    if(count($wherePrApproverMst)) {
        $query .= "where " . implode('and ', $wherePrApproverMst);
    }
    $sql = $conn->query($query);
    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
         $json_names[] = "{
                            BU_CD           :'".$row['BU_CD']."'
                            ,BU_NAME        :'".$row['BU_NAME']."'
                            ,APPROVER_NO    :'".$row['APPROVER_NO']."'
                            ,NPK            :'".$row['NPK']."'
                            ,APPROVER_NAME  :'".addslashes($row['APPROVER_NAME'])."'
                            ,APPROVER_LEVEL :'".$row['APPROVER_LEVEL']."'
                            ,LIMIT_AMOUNT   :'".$row['LIMIT_AMOUNT']."'
                          }";
    }
}
if($criteria == 'PrProcApproverMst'){
    $query = "select count
                    (*)
                as
                    COUNT_DATA
                from
                    EPS_M_PR_PROC_APPROVER";
    $sql = $conn->query($query);
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    $countData = $row['COUNT_DATA'];
    $query = "select 
                EPS_M_PR_PROC_APPROVER.PLANT_CD
                ,EPS_M_PLANT.PLANT_NAME
                ,EPS_M_PR_PROC_APPROVER.BU_CD
                ,EPS_M_TBUNIT.NMBU1 as BU_NAME
                ,EPS_M_PR_PROC_APPROVER.NPK
                ,EPS_M_EMPLOYEE.NAMA1 as APPROVER_NAME
              from
                EPS_M_PR_PROC_APPROVER
              left join
                EPS_M_EMPLOYEE
              on
                EPS_M_PR_PROC_APPROVER.NPK = EPS_M_EMPLOYEE.NPK
              left join
                EPS_M_PLANT
              on
                EPS_M_PR_PROC_APPROVER.PLANT_CD = EPS_M_PLANT.PLANT_CD
              left join
                EPS_M_TBUNIT
              on
                EPS_M_PR_PROC_APPROVER.BU_CD = EPS_M_TBUNIT.KDBU";
    $sql = $conn->query($query);
    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
         $json_names[] = "{
                            PLANT_CD        :'".$row['PLANT_CD']."'
                            ,PLANT_NAME     :'".$row['PLANT_NAME']."'
                            ,BU_CD          :'".$row['BU_CD']."'
                            ,BU_NAME        :'".$row['BU_NAME']."'
                            ,NPK            :'".$row['NPK']."'
                            ,APPROVER_NAME  :'".$row['APPROVER_NAME']."'
                          }";
    }
}
if($criteria == 'Supplier'){
    $whereSupplier = array();
    $supplierCd    = $_REQUEST['supplierCdVal'];
    $supplierName  = $_REQUEST['supplierNameVal'];
    $contact       = $_REQUEST['contactVal'];
    $email         = $_REQUEST['emailVal'];
    $phone         = $_REQUEST['phoneVal'];
    $fax           = $_REQUEST['faxVal'];
    $address       = $_REQUEST['addressVal'];
    
    if($supplierCd){
        $whereSupplier[] = "EPS_M_SUPPLIER.SUPPLIER_CD = '".$supplierCd."'";
    }
    if($supplierName){
        $whereSupplier[] = "EPS_M_SUPPLIER.SUPPLIER_NAME like '".$supplierName."%'";
    }
    if($contact){
        $whereSupplier[] = "EPS_M_SUPPLIER.CONTACT like '".$contact."%'";
    }
    if($email){
        $whereSupplier[] = "EPS_M_SUPPLIER.EMAIL like '".$email."%'";
    }
    if($phone){
        $whereSupplier[] = "EPS_M_SUPPLIER.PHONE like '".$phone."%'";
    }
    if($fax){
        $whereSupplier[] = "EPS_M_SUPPLIER.FAX like '".$fax."%'";
    }
    if($address){
        $whereSupplier[] = "EPS_M_SUPPLIER.ADDRESS like '%".$address."%'";
    }
    $query = "select count
            (*)
          as
            COUNT_DATA
          from
            EPS_M_SUPPLIER ";
    if(count($whereSupplier)) {
        $query .= "where " . implode('and ', $whereSupplier);
    }
    $sql = $conn->query($query);
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    $countData = $row['COUNT_DATA'];
    if ($page != '1'){
        if($next > $countData){
            $limit = $countData -(($page-1) * $limit);
        }
    }
    $query = "select * from 
                (select top $limit * from          
                    (select top $next 
                        SUPPLIER_CD
                        ,SUPPLIER_NAME 
                        ,CURRENCY_CD
                        ,VAT
                        ,CONTACT
                        ,EMAIL
                        ,EMAIL_CC
                        ,PHONE
                        ,FAX
                        ,ADDRESS
                        ,convert(VARCHAR(24), CREATE_DATE, 120) as CREATE_DATE
                        ,CREATE_BY
                        ,convert(VARCHAR(24), UPDATE_DATE, 120) as UPDATE_DATE
                        ,UPDATE_BY
                    from 
                        EPS_M_SUPPLIER ";
    if(count($whereSupplier)) {
        $query .= "where " . implode('and ', $whereSupplier);
    }
    $query .= "     order by 
                        SUPPLIER_CD asc) as EPS_M_SUPPLIER
                 order by SUPPLIER_CD desc) as EPS_M_SUPPLIER
               order by SUPPLIER_CD
              ";
    $sql = $conn->query($query);
    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
         $json_names[] = "{ 
                            SUPPLIER_CD     :'".$row['SUPPLIER_CD']."'
                            ,SUPPLIER_NAME  :'".$row['SUPPLIER_NAME']."'
                            ,CURRENCY_CD    :'".$row['CURRENCY_CD']."'
                            ,VAT            :'".$row['VAT']."'
                            ,CONTACT        :'".$row['CONTACT']."'
                            ,EMAIL          :'".$row['EMAIL']."'
                            ,EMAIL_CC       :'".$row['EMAIL_CC']."'
                            ,PHONE          :'".$row['PHONE']."'
                            ,FAX            :'".$row['FAX']."'
                            ,ADDRESS        :'".$row['ADDRESS']."'
                            ,CREATE_DATE    :'".$row['CREATE_DATE']."'
                            ,CREATE_BY      :'".$row['CREATE_BY']."'
                            ,UPDATE_DATE    :'".$row['UPDATE_DATE']."'
                            ,UPDATE_BY      :'".$row['UPDATE_BY']."'
                          }";
    }
}
if($criteria == 'UnitMeasure'){
    $whereUnitMeasure = array();
    $unitCd    = $_REQUEST['unitCdVal'];
    $unitName  = $_REQUEST['unitNameVal'];
    if($unitCd){
        $whereUnitMeasure[] = "EPS_M_UNIT_MEASURE.UNIT_CD = '".$unitCd."'";
    }
    if($unitName){
        $whereUnitMeasure[] = "EPS_M_UNIT_MEASURE.UNIT_NAME like '".$unitName."%'";
    }
    $query = "select count
            (*)
                as
                    COUNT_DATA
                from
                    EPS_M_UNIT_MEASURE ";
    if(count($whereUnitMeasure)) {
        $query .= "where " . implode('and ', $whereUnitMeasure);
    }
    $sql = $conn->query($query);
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    $countData = $row['COUNT_DATA'];
    if ($page != '1'){
        if($next > $countData){
            $limit = $countData -(($page-1) * $limit);
        }
    }
    $query = "select * from 
                (select top $limit * from          
                    (select top $next 
                        UNIT_CD
                        ,UNIT_NAME
                        ,convert(VARCHAR(24), CREATE_DATE, 120) as CREATE_DATE
                        ,CREATE_BY
                        ,convert(VARCHAR(24), UPDATE_DATE, 120) as UPDATE_DATE
                        ,UPDATE_BY
                    from 
                        EPS_M_UNIT_MEASURE ";
    if(count($whereUnitMeasure)) {
        $query .= "where " . implode('and ', $whereUnitMeasure);
    }
    $query .= "     order by 
                        UNIT_CD asc) as EPS_M_UNIT_MEASURE
                 order by UNIT_CD desc) as EPS_M_UNIT_MEASURE
               order by UNIT_CD";
    $sql = $conn->query($query);
    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
         $json_names[] = "{ 
                            UNIT_CD     :'".$row['UNIT_CD']."'
                            ,UNIT_NAME  :'".$row['UNIT_NAME']."'
                            ,CREATE_DATE:'".$row['CREATE_DATE']."'
                            ,CREATE_BY  :'".$row['CREATE_BY']."'
                            ,UPDATE_DATE:'".$row['UPDATE_DATE']."'
                            ,UPDATE_BY        :'".$row['UPDATE_BY']."'
                          }";
    }
}
$json .= implode(',', $json_names); // join the objects by commas;
$json .= ']'; // end the json array element
echo '{success: true, total:'.$countData.',rows:'.$json.'}';
?>
