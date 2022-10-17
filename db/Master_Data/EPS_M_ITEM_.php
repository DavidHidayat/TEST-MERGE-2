<?php session_start();
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Common.php";

if(isset($_GET['action'])){
    $action= $_GET['action'];
}
$userId     = $_SESSION['sNPK'];
$sBuLogin = $_SESSION['sBuLogin'];
$itemName1   = stripslashes($_GET['itemName']);
$itemName2 = explode(" ~ ", $itemName1);
$objectAccount   = stripslashes($_GET['objectAccount']);
$itemName = trim($itemName2[0]);
$itemName   = str_replace("'", "''", $itemName);
$objectAccount   = str_replace("'", "''", $objectAccount);
$currenctDate= date(Ymd);

$object_account_split = explode("-", $objectAccount);
$object_account_trim = trim($object_account_split[1]);
echo "$object_account_trim";
if($action=='searchItemPrice'){
    $whereItemPrice = array();
    $whereItemPrice[] = "EPS_M_ITEM.ACTIVE_FLAG = 'A'";
    if($itemName){
        $whereItemPrice[] = "EPS_M_ITEM.ITEM_NAME = '".$itemName."'";
    }
    if($currenctDate){
        $whereItemPrice[] = "EPS_M_ITEM_PRICE.EFFECTIVE_DATE_FROM <= '".$currenctDate."'";
    }
//    if($objectAccount){
//        $whereItemPrice[] = "EPS_M_ITEM.OBJECT_ACCOUNT_CD = '".$object_account_trim."'";
//    }
    $json = '['; // start the json array element
    $json_names = array();

    $query= "select     
                EPS_M_ITEM_PRICE.ITEM_CD
                ,EPS_M_ITEM.ITEM_NAME
                ,EPS_M_ITEM.OBJECT_ACCOUNT_CD
                ,EPS_M_ITEM_PRICE.UNIT_CD
                ,EPS_M_ITEM_PRICE.SUPPLIER_CD
                ,EPS_M_SUPPLIER.SUPPLIER_NAME
                ,EPS_M_ITEM_PRICE.ITEM_PRICE
                ,EPS_M_ITEM_PRICE.EFFECTIVE_DATE_FROM
                ,EPS_M_ITEM_PRICE.CURRENCY_CD
                ,EPS_M_OBJECT_ACCOUNT.ITEM_CODE
            from         
                EPS_M_ITEM_PRICE 
            inner join
                EPS_M_ITEM 
            on 
                EPS_M_ITEM_PRICE.ITEM_CD = EPS_M_ITEM.ITEM_CD 
            INNER JOIN 
		EPS_M_OBJECT_ACCOUNT
            on  
                EPS_M_ITEM.OBJECT_ACCOUNT_CD = EPS_M_OBJECT_ACCOUNT.OBJECT_ACCOUNT_CD
            inner join
                EPS_M_SUPPLIER 
            on 
                EPS_M_ITEM_PRICE.SUPPLIER_CD = EPS_M_SUPPLIER.SUPPLIER_CD ";
    if(count($whereItemPrice)) {
        $query .= "where " . implode('and ', $whereItemPrice);
    }
    $query .= " order by 
                EPS_M_ITEM_PRICE.EFFECTIVE_DATE_FROM desc";
   //echo "$query";
    $sql = $conn->query($query);
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    $itemCd     = $row['ITEM_CD'];
    $itemName   = addslashes($row['ITEM_NAME']);
    $unitCd     = $row['UNIT_CD'];
    $supplierCd = $row['SUPPLIER_CD'];
    $supplierName=$row['SUPPLIER_NAME'];
    $price      = $row['ITEM_PRICE'];
    $currencyCd = $row['CURRENCY_CD'];
    $objectAccountCd = $row['OBJECT_ACCOUNT_CD'];
    $itemCode = $row['ITEM_CODE'];
    
    $json_names[] = "{  itemCd:'$itemCd'
                        ,itemName:'$itemName'
                        ,unitCd:'$unitCd'
                        ,supplierCd:'$supplierCd'
                        ,supplierName:'$supplierName'
                        ,price:'$price'
                        ,currencyCd: '$currencyCd'
                        ,objectAccountCd: '$objectAccountCd'
                        ,itemCode: '$itemCode'}";

    $json .= implode(',', $json_names); // join the objects by commas;
    $json .= ']'; // end the json array element

    if($row){
        echo '{success:true, rows:'.$json.', msg:'.json_encode(array('message'=>'Exist')).'}';
        //echo "$query";
    }else{
        echo '{success:true, msg:'.json_encode(array('message'=>'NotExist')).'}';   
        //echo "$query";
    }
    //echo "$query";
}
if($action=='AddItemPrice'){
    $itemCd         = stripslashes($_POST['itemCdVal']);
    $supplierCd     = stripslashes($_POST['supplierCdVal']);
    $unitCd         = stripslashes($_POST['unitCdVal']);
    $price          = stripslashes($_POST['priceVal']);
    $effectiveDate  = encodeDate($_POST['effectiveDateVal']);
    $query = "insert into
                EPS_M_ITEM_PRICE
                (
                    ITEM_CD
                    ,SUPPLIER_CD
                    ,UNIT_CD
                    ,CURRENCY_CD
                    ,ITEM_PRICE
                    ,EFFECTIVE_DATE_FROM
                    ,CREATE_DATE
                    ,CREATE_BY
                    ,UPDATE_DATE
                    ,UPDATE_BY
                )
              values
                (
                    '$itemCd'
                    ,'$supplierCd'
                    ,'$unitCd'
                    ,'IDR'
                    ,'$price'
                    ,'$effectiveDate'
                    ,convert(VARCHAR(24), GETDATE(), 120)
                    ,'$userId'
                    ,convert(VARCHAR(24), GETDATE(), 120)
                    ,'$userId'
                )";
    $conn->query($query);
}
if($action=='searchItem'){
    $object_account_trim;
    $oa = $object_account_trim;
//    $query = "select 
//                ITEM_CD
//                ,ITEM_NAME + ' ~ '+ OBJECT_ACCOUNT_CD AS ITEM_NAME
//                ,convert(VARCHAR(24), CREATE_DATE, 120) as CREATE_DATE
//                ,CREATE_BY
//                ,convert(VARCHAR(24), UPDATE_DATE, 120) as UPDATE_DATE
//                ,UPDATE_BY
//                ,ITEM_NAME AS ITEM_NAME_VALUES
//              from 
//                EPS_M_ITEM 
//              where 
//                EPS_M_ITEM.ACTIVE_FLAG = 'A'
//              order by 
//                ITEM_NAME";
    $query = "select 
                EPS_M_ITEM.ITEM_CD
		        ,EPS_M_ITEM.ITEM_NAME
                ,convert(VARCHAR(24), EPS_M_ITEM.CREATE_DATE, 120) as CREATE_DATE
                ,EPS_M_ITEM.CREATE_BY
                ,convert(VARCHAR(24), EPS_M_ITEM.UPDATE_DATE, 120) as UPDATE_DATE
                ,EPS_M_ITEM.UPDATE_BY
              from 
                EPS_M_ITEM 
			  inner join EPS_M_ITEM_PRICE
			  ON EPS_M_ITEM.ITEM_CD=EPS_M_ITEM_PRICE.ITEM_CD
			  INNER JOIN IMS_M_STOCK 
			  ON SUBSTRING(EPS_M_ITEM.ITEM_CD,1,9)=IMS_M_STOCK.ITEMCODE
              where 
                EPS_M_ITEM_PRICE.BU_CD = '$sBuLogin' AND  EPS_M_ITEM.ACTIVE_FLAG = 'A' 
              order by 
                ITEM_NAME";
    $sql = $conn->query($query);
    $row = $sql->fetchAll(PDO::FETCH_ASSOC);
    
    if ($row>0){
        echo '{success:true, rows:'.json_encode($row).'}';
        echo "$oa";
    }else{
        echo '{success:false}';
    }
}
if($action=='detailItem'){
    $json = '['; // start the json array element
    $json_names = array();

    $query= "select 
                ITEM_CD
             from 
                EPS_M_ITEM
            where     
                1=1 and (ITEM_NAME = '$itemName')";
    $sql = $conn->query($query);
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    $itemCd =$row['ITEM_CD'];
    
    $json_names[] = "{  itemCd:'$itemCd'}";

    $json .= implode(',', $json_names); // join the objects by commas;
    $json .= ']'; // end the json array element

    if($row){
        echo '{success:true, rows:'.$json.', msg:'.json_encode(array('message'=>'Exist')).'}';
    }else{
        echo '{success:true, msg:'.json_encode(array('message'=>'NotExist')).'}';   
    }
}
if($action=='AddItem'){
    $itemCd         = stripslashes($_POST['itemCdVal']);
    $itemName       = stripslashes($_POST['itemNameVal']);
    $itemGroupCd    = stripslashes($_POST['itemGroupCdVal']);
    $query = "insert into
                EPS_M_ITEM
                (
                    ITEM_CD
                    ,ITEM_NAME
                    ,ITEM_GROUP_CD
                    ,ACTIVE_FLAG
                    ,CREATE_DATE
                    ,CREATE_BY
                    ,UPDATE_DATE
                    ,UPDATE_BY
                )
              values
                (
                    '$itemCd'
                    ,'$itemName'
                    ,'$itemGroupCd'
                    ,'A'
                    ,convert(VARCHAR(24), GETDATE(), 120)
                    ,'$userId'
                    ,convert(VARCHAR(24), GETDATE(), 120)
                    ,'$userId'
                )";
    $conn->query($query);
}
if($action == 'EditItem'){
    $itemCd         = stripslashes($_POST['itemCdVal']);
    $itemName       = stripslashes($_POST['itemNameVal']);
    $itemGroupCd    = stripslashes($_POST['itemGroupCdVal']);
    $query = "update
                EPS_M_ITEM
              set
                ITEM_NAME = '$itemName'
                ,ITEM_GROUP_CD = '$itemGroupCd'
                ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                ,UPDATE_BY = '$userId'
              where
                ITEM_CD = '$itemCd'";
    $conn->query($query);
}
if($action == 'DeleteItem'){
    $itemCd         = $_POST['itemCdVal'];
    $query = "delete from
                EPS_M_ITEM
              where
                ITEM_CD = '$itemCd'";
    $conn->query($query);
}

?>
