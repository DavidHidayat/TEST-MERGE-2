<?php session_start();
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Common.php";

if(isset($_GET['action'])){
    $action= $_GET['action'];
}
$itemName   = stripslashes($_REQUEST['term']);
$itemName   = str_replace("'", "''", $itemName);

if($action=='searchAutoItemPrice'){
    $query= "select     
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
                (EPS_M_ITEM.ITEM_NAME LIKE '%".trim($itemName)."%') 
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
    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
        $itemCd     = $row['ITEM_CD'];
        $itemName   = $row['ITEM_NAME'];
        $unitCd     = $row['UNIT_CD'];
        $supplierCd = $row['SUPPLIER_CD'];
        $supplierName=$row['SUPPLIER_NAME'];
        $price      = $row['ITEM_PRICE'];
        $currencyCd = $row['CURRENCY_CD'];

        $result[] = array(
                        'id'=> $itemCd
                        ,'value'=> $itemName
                        ,'itemCd'=> $itemCd
                        ,'unitCd'=> $unitCd
                        ,'supplierCd'=> $supplierCd
                        ,'supplierName'=> $supplierName
                        ,'price'=> number_format($price)
                        ,'currencyCd'=> $currencyCd
                    );
    }
    echo json_encode($result);
}
?>
