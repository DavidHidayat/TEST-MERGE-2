<?php session_start();
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";

if(isset($_GET['action'])){
    $action= $_GET['action'];
}
$userId     = $_SESSION['sNPK'];
$supplierName=stripslashes($_GET['supplierName']);
$supplierName   = str_replace("'", "''", $supplierName);

if($action=='search'){
    $query = "select 
                SUPPLIER_CD
                ,SUPPLIER_NAME 
                ,CONTACT
                ,EMAIL
                ,PHONE
                ,FAX
                ,ADDRESS
                ,convert(VARCHAR(24), CREATE_DATE, 120) as CREATE_DATE
                ,CREATE_BY
                ,convert(VARCHAR(24), UPDATE_DATE, 120) as UPDATE_DATE
                ,UPDATE_BY
              from 
                EPS_M_SUPPLIER
			  where
				CURRENCY_CD = 'IDR'
                and ACTIVE_FLAG = 'A'
              order by 
                SUPPLIER_CD asc";
    $sql = $conn->query($query);
    $row = $sql->fetchAll(PDO::FETCH_ASSOC);

    if ($row>0){
        echo '{success:true, rows:'.json_encode($row).'}';
    }else{
        echo '{success:false}';
    }
}
if($action=='detail'){
    $json = '['; // start the json array element
    $json_names = array();

    $query= "select 
                SUPPLIER_CD
             from 
                EPS_M_SUPPLIER
            where     
                1=1 and (SUPPLIER_NAME = '$supplierName') and CURRENCY_CD = 'IDR'";
    $sql=$conn->query($query);
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    $supplierCd=$row['SUPPLIER_CD'];
    
    $json_names[] = "{  supplierCd:'$supplierCd'}";

    $json .= implode(',', $json_names); // join the objects by commas;
    $json .= ']'; // end the json array element

    if($row){
        echo '{success:true, rows:'.$json.', msg:'.json_encode(array('message'=>'Exist')).'}';
    }else{
        echo '{success:true, msg:'.json_encode(array('message'=>'NotExist')).'}';   
    }
}
if($action == 'Add'){
    $supplierCd    = stripslashes($_POST['supplierCdVal']);
    $supplierName  = stripslashes($_POST['supplierNameVal']);
    $contact       = stripslashes($_POST['contactVal']);
    $email         = stripslashes($_POST['emailVal']);
    $phone         = stripslashes($_POST['phoneVal']);
    $fax           = stripslashes($_POST['faxVal']);
    $address       = stripslashes($_POST['addressVal']);
    $query = "insert into
                EPS_M_SUPPLIER
                (
                    SUPPLIER_CD
                    ,SUPPLIER_NAME
                    ,CONTACT
                    ,EMAIL
                    ,PHONE
                    ,FAX
                    ,ADDRESS
                    ,CREATE_DATE
                    ,CREATE_BY
                    ,UPDATE_DATE
                    ,UPDATE_BY
                )
              values
                (
                    '$supplierCd'
                    ,'$supplierName'
                    ,'$contact'
                    ,'$email'
                    ,'$phone'
                    ,'$fax'
                    ,'$address'
                    ,convert(VARCHAR(24), GETDATE(), 120)
                    ,'$userId'
                    ,convert(VARCHAR(24), GETDATE(), 120)
                    ,'$userId'
                )";
    $conn->query($query);
}
if($action == 'Edit'){
    $supplierCd    = stripslashes($_POST['supplierCdVal']);
    $supplierName  = stripslashes($_POST['supplierNameVal']);
    $contact       = stripslashes($_POST['contactVal']);
    $email         = stripslashes($_POST['emailVal']);
    $phone         = stripslashes($_POST['phoneVal']);
    $fax           = stripslashes($_POST['faxVal']);
    $address       = stripslashes($_POST['addressVal']);
    $query = "update
                EPS_M_SUPPLIER
              set
                SUPPLIER_NAME = '$supplierName'
                ,CONTACT = '$contact'
                ,EMAIL = '$email'
                ,PHONE = '$phone'
                ,FAX = '$fax'
                ,ADDRESS = '$address'
                ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                ,UPDATE_BY = '$userId'
              where
                SUPPLIER_CD = '$supplierCd'";
    $conn->query($query);
}
if($action == 'Delete'){
    $supplierCd         = $_POST['supplierCdVal'];
    $query = "delete from
                EPS_M_SUPPLIER
              where
                SUPPLIER_CD = '$supplierCd'";
    $conn->query($query);
}
?>