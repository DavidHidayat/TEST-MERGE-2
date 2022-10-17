<?php session_start(); 
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Constant.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Common.php";
if(isset($_SESSION['sUserId']))
{         
    $sUserId    = $_SESSION['sUserId'];
    if($sUserId != '')
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
        $sBuLogin   = $_SESSION['sBuLogin'];
        $sUserType  = $_SESSION['sUserType'];   

        if($sRoleId == 'ROLE_03' || $sRoleId == 'ROLE_04' || $sRoleId == 'ROLE_06' || $sRoleId == 'ROLE_07')
        {
            
        }
        else
        {
        ?>
            <script language="javascript"> alert("Sorry, this page only can be accessed by Procurement.");
            document.location="../db/Login/Logout.php"; </script>
        <?php
        }
    }
    else
    {
    ?>
        <script language="javascript"> alert("Sorry, your session to EPS has expired. Please login again.");
        document.location="../db/Login/Logout.php"; </script>
    <?php
    }
}
else
{	
?>
    <script language="javascript"> alert("Sorry, you are has not login. Please login first.");
    document.location="../db/Login/Logout.php"; </script>
<?
}

$fileType = $_FILES['upload-field']['type'];
if($fileType=="application/vnd.ms-excel"){
    
    // utilize class phpExcelReader
    include "../lib/excel/excel_reader2.php";
    
    // read excel file
    $data = new Spreadsheet_Excel_Reader($_FILES['upload-field']['tmp_name']);

    // read row
    $rowData = $data->rowcount($sheet_index=0);
        
    // initial value
    $countSuccess   = 0;
    $countFailed    = 0;
    $countError     = 0;
    $currentDate    = date(Ymd);
    
    $query = "select count(*) as UPLOAD_COUNT from EPS_M_ITEM_UPLOAD";
    $sql = $conn->query($query);
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    $uploadCount = $row['UPLOAD_COUNT'];
    
    if($uploadCount == 0){
        $sequences = '1';
    } else{
        $query = "select UPLOAD_ID from EPS_M_ITEM_UPLOAD order by UPLOAD_ID desc";
        $sql = $conn->query($query);
        $row = $sql->fetch(PDO::FETCH_ASSOC);
        $uploadId = $row['UPLOAD_ID'];
        $sequences = substr($uploadId, 11);
        $sequences = $sequences + 1;
    }
    $uploadId = 'MIP'.$currentDate.$sequences;
    
    // read row start from 2nd row
    for ($x=2; $x <= $rowData; $x++)
    {
        $uploadItemStatus= '';
        $msgError       = '';
        
        $itemCd         = trim($data->val($x, 1));
        //$itemName_1     = trim($data->val($x, 2));
        //$itemName_2     = trim($data->val($x, 3));
        $itemName       = strtoupper(trim($data->val($x, 2)));
        $itemName       = preg_replace('/\s+/', ' ',$itemName);
        $unitCd         = trim($data->val($x, 3));
        $itemPrice      = trim($data->val($x, 4));
        $supplierCd     = trim($data->val($x, 5));
        $itemGroupCd    = trim($data->val($x, 6));
        $effectiveDate  = trim($data->val($x, 7));
        $getYear        = substr($effectiveDate, 0, 4);
        $getMonth       = substr($effectiveDate, 4, 2);
        $getDate        = substr($effectiveDate, 6, 2);
        
        if(!is_numeric($itemPrice)){
            $itemPrice = 0;
        }
        $itemPrice      = number_format($itemPrice);
        $itemPrice      = str_replace(',', '',$itemPrice);
        //echo 'new price: '.$unitPrice.'<br>';
        /** Search UM */
        $query_unitCd = "select 
                            UNIT_CD
                            ,UNIT_NAME
                         from
                            EPS_M_UNIT_MEASURE
                         where
                            UNIT_CD = '$unitCd'";
        $sql_unitCd = $conn->query($query_unitCd);
        $row_unitCd = $sql_unitCd->fetch(PDO::FETCH_ASSOC);
        
        /** Search Item Group */
        $query_itemGroup = "select 
                                ITEM_GROUP_CD
                                ,ITEM_GROUP_NAME
                            from
                                EPS_M_ITEM_GROUP
                            where
                                ITEM_GROUP_CD = '$itemGroupCd'";
        $sql_itemGroup = $conn->query($query_itemGroup);
        $row_itemGroup = $sql_itemGroup->fetch(PDO::FETCH_ASSOC);
        
        /** Search Supplier */
        $query_supplier = "select 
                                SUPPLIER_CD
                                ,SUPPLIER_NAME
                            from
                                EPS_M_SUPPLIER
                            where
                                SUPPLIER_CD = '$supplierCd'";
        $sql_supplier = $conn->query($query_supplier);
        $row_supplier = $sql_supplier->fetch(PDO::FETCH_ASSOC);
        
        /** Search Item */
        $query_item = "select 
                                ITEM_CD
                                ,ITEM_NAME
                            from
                                EPS_M_ITEM
                            where
                                ITEM_CD = '$itemCd'";
        $sql_item = $conn->query($query_item);
        $row_item = $sql_item->fetch(PDO::FETCH_ASSOC);
        
        if($itemCd != '')
        {
            if($itemName != '')
            {
                if($unitCd != '')
                {
                    if($itemPrice != '')
                    {
                        if($supplierCd != '')
                        {
                            if($itemGroupCd != '')
                            {
                                if($effectiveDate != '')
                                {
                                    if(is_numeric($effectiveDate))
                                    {
                                        if(checkdate((int)$getMonth, (int)$getDate , (int)$getYear) == 1)
                                        {
                                            if($itemPrice > 0)
                                            {
                                                if($row_unitCd)
                                                {
                                                    if($row_itemGroup)
                                                    {
                                                        if($row_supplier)
                                                        {   
                                                            if($row_item){
                                                                $uploadItemStatus = 'E';
                                                                $msgError = 'Duplicate Error. Item Code already exist in Master Data.';
                                                                $countError++;
                                                            }
                                                        }else{
                                                            $uploadItemStatus = 'E';
                                                            $msgError = 'Existence Error. Supplier Code is not found Master.';
                                                            $countError++;
                                                        }
                                                    }else{
                                                        $uploadItemStatus = 'E';
                                                        $msgError = 'Existence Error. Category is not found Master.';
                                                        $countError++;
                                                    }
                                                }else{
                                                    $uploadItemStatus = 'E';
                                                    $msgError = 'Existence Error. U/M is not found Master.';
                                                    $countError++;
                                                }
                                            }else{
                                                $uploadItemStatus = 'E';
                                                $msgError = 'Mandatory. Please input price > 0.';
                                                $countError++;
                                            }
                                        }else{
                                            $uploadItemStatus = 'E';
                                            $msgError = 'Digit Error. Please input correct delivery date (YYYYMMDD).';
                                            $countError++;
                                        }
                                    }else{
                                        $uploadItemStatus = 'E';
                                        $msgError = 'Type Error. Please input Effective Date in numeric type.';
                                        $countError++;
                                    }
                                }else{
                                    $uploadItemStatus = 'E';
                                    $msgError = 'Mandatory. Please fill in the Effective Date column.';
                                    $countError++;
                                }
                            }else{
                                $uploadItemStatus = 'E';
                                $msgError = 'Mandatory. Please fill in the Item Group Code column.';
                                $countError++;
                            }
                        }else{
                            $uploadItemStatus = 'E';
                            $msgError = 'Mandatory. Please fill in the Supplier Code column.';
                            $countError++;
                        }
                    }else{
                        $uploadItemStatus = 'E';
                        $msgError = 'Mandatory. Please fill in the Price column.';
                        $countError++;
                    }
                }
                else{
                    $uploadItemStatus = 'E';
                    $msgError = 'Mandatory. Please fill in the U/M column.';
                    $countError++;
                }
            }
            else{
                $uploadItemStatus = 'E';
                $msgError = 'Mandatory. Please fill in the Item Name column.';
                $countError++;
            }
        }
        else{
            $uploadItemStatus = 'E';
            $msgError = 'Mandatory. Please fill in the Item Code column.';
            $countError++;
        }
        
        $query_insert = "insert into
                    EPS_M_ITEM_UPLOAD
                    (
                        UPLOAD_ID
                        ,ITEM_CD
                        ,ITEM_NAME
                        ,ITEM_GROUP_CD
                        ,SUPPLIER_CD
                        ,UNIT_CD
                        ,ITEM_PRICE
                        ,EFFECTIVE_DATE
                        ,UPLOAD_ITEM_STATUS
                        ,UPLOAD_MESSAGE
                        ,CREATE_DATE
                        ,CREATE_BY
                        ,UPDATE_DATE
                        ,UPDATE_BY
                    )
                  values
                    (
                        '$uploadId'
                        ,'$itemCd'
                        ,'$itemName'
                        ,'$itemGroupCd'
                        ,'$supplierCd'
                        ,'$unitCd'
                        ,'$itemPrice'
                        ,'$effectiveDate'
                        ,'$uploadItemStatus'
                        ,'$msgError'
                        ,convert(VARCHAR(24), GETDATE(), 120)
                        ,'$sUserId'
                        ,convert(VARCHAR(24), GETDATE(), 120)
                        ,'$sUserId'
                    )";
        $sql_insert = $conn->query($query_insert);
        
        if($sql_insert){
            $countSuccess++;
        }else{
            $countFailed++;
        }
    }
        
        $html ="<br><h3 id=warn>Upload process finished</h3>";
        $html.="<p id=par>Success data upload: ".$countSuccess;
        $html.="<br>Failure data upload: ".$countFailed;
        $html.="<br>Error data upload: ".$countError;
        $html.="<br><br>";
        $html.="<table border=1 class=upload-table>";
        $html.="<thead><tr><td width=20px align=center>NO.</td>";
        $html.="<td width=50px align=center>CODE</td>";
        $html.="<td width=320px align=center>ITEM NAME</td>";
        $html.="<td width=50px align=center>U/M</td>";
        $html.="<td width=50px align=center>PRICE</td>";
        $html.="<td width=60px align=center>SUPPLIER</td>";
        $html.="<td width=40px align=center>CATEGORY</td>";
        $html.="<td width=60px align=center>EFFECTIVE DATE</td>";
        $html.="<td width=290px align=center>ERROR MESSAGE</td></tr></thead>";
        $i = 1;
        $query_upload = "select 
                            ITEM_CD
                            ,ITEM_NAME
                            ,ITEM_GROUP_CD
                            ,SUPPLIER_CD
                            ,UNIT_CD
                            ,ITEM_PRICE
                            ,EFFECTIVE_DATE
                            ,UPLOAD_MESSAGE
                        from 
                            EPS_M_ITEM_UPLOAD 
                        where 
                            UPLOAD_ID = '$uploadId'
                        order by
                            ITEM_CD";
        $sql_upload = $conn->query($query_upload);
        $html.="<tbody>";
        while($row_upload = $sql_upload->fetch(PDO::FETCH_ASSOC)){
            $itemCd         = $row_upload['ITEM_CD'];
            $itemName       = $row_upload['ITEM_NAME'];
            $itemGroupCd    = $row_upload['ITEM_GROUP_CD'];
            $supplierCd     = $row_upload['SUPPLIER_CD'];
            $unitCd         = $row_upload['UNIT_CD'];
            $itemPrice      = $row_upload['ITEM_PRICE'];
            $effectiveDate  = $row_upload['EFFECTIVE_DATE'];
            $msgError       = $row_upload['UPLOAD_MESSAGE'];
            $html.="<tr><td>".$i.".</td><td>".$itemCd."</td><td>".$itemName."</td><td>".$unitCd."</td><td>".$itemPrice."</td><td>".$supplierCd."</td><td>".$itemGroupCd."</td><td>".$effectiveDate."</td><td>".$msgError."</td></tr>";
            $i++;
        }
    
    if($countError > 0){
        $query = "delete from
                    EPS_M_ITEM_UPLOAD
                  where 
                    UPLOAD_ID = '$uploadId'";
        $sql = $conn->query($query);
    }else{
        if($countError == 0){
            $query_search = "select 
                                ITEM_CD
                                ,ITEM_NAME
                                ,ITEM_GROUP_CD
                                ,SUPPLIER_CD
                                ,UNIT_CD
                                ,ITEM_PRICE
                                ,EFFECTIVE_DATE
                            from 
                                EPS_M_ITEM_UPLOAD 
                            where 
                                UPLOAD_ID = '$uploadId'
                            order by
                                ITEM_CD";
            $sql_search = $conn->query($query_search);
            while($row_search = $sql_search->fetch(PDO::FETCH_ASSOC)){
                $itemCd         = strtoupper($row_search['ITEM_CD']);
                $itemName       = strtoupper($row_search['ITEM_NAME']);
                $itemGroupCd    = strtoupper($row_search['ITEM_GROUP_CD']);
                $supplierCd     = strtoupper($row_search['SUPPLIER_CD']);
                $unitCd         = strtoupper($row_search['UNIT_CD']);
                $itemPrice      = $row_search['ITEM_PRICE'];
                $itemPrice      = number_format($itemPrice);
                $itemPrice      = str_replace(',', '',$itemPrice);
                $effectiveDate  = $row_search['EFFECTIVE_DATE'];
                
                $query_insert_item = "insert into
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
                                            ,'$sUserId'
                                            ,convert(VARCHAR(24), GETDATE(), 120)
                                            ,'$sUserId'
                                        )";
                $sql_insert_item = $conn->query($query_insert_item);
                
                $query_insert_price = "insert into
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
                                                ,'$itemPrice'
                                                ,'$effectiveDate'
                                                ,convert(VARCHAR(24), GETDATE(), 120)
                                                ,'$sUserId'
                                                ,convert(VARCHAR(24), GETDATE(), 120)
                                                ,'$sUserId'
                                            )";
                $sql_insert_item = $conn->query($query_insert_price);
            }
            $query = "delete from
                        EPS_M_ITEM_UPLOAD
                    where 
                        UPLOAD_ID = '$uploadId'";
            $sql = $conn->query($query);
        }
    }
    
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>EPS</title>
        <!--  CSS -->
        <link rel="stylesheet" type="text/css" href="../extjs/resources/css/ext-all.css"></link>
        <link rel="stylesheet" type="text/css" href="../css/eps.css"></link>
        <!--  Ext Js library -->
        <script type="text/javascript" src="../extjs/bootstrap.js"></script>
        <script type="text/javascript" src="../js/Common.js"></script>
        <script type="text/javascript" src="../js/Store_Master.js"></script>
        <style>
            .upload-field{
                position: relative;
                width: 350px; 
                height: 30px; 
                cursor: pointer;
                background: #fbfbfa;
                border: 1px solid #A8A49D; 
            }
            .upload-table {
                margin:0px;
                padding:0px;
                width:100%;	
                box-shadow: 10px 10px 5px #888888;
                border:1px solid #ffffff;

                -moz-border-radius-bottomleft:0px;
                -webkit-border-bottom-left-radius:0px;
                border-bottom-left-radius:0px;

                -moz-border-radius-bottomright:0px;
                -webkit-border-bottom-right-radius:0px;
                border-bottom-right-radius:0px;

                -moz-border-radius-topright:0px;
                -webkit-border-top-right-radius:0px;
                border-top-right-radius:0px;

                -moz-border-radius-topleft:0px;
                -webkit-border-top-left-radius:0px;
                border-top-left-radius:0px;
            }
            .upload-table table{
                width:100%;
                height:100%;
                margin:0px;
                padding:0px;
            }
            .upload-table tr:last-child td:last-child {
                -moz-border-radius-bottomright:0px;
                -webkit-border-bottom-right-radius:0px;
                border-bottom-right-radius:0px;
            }
            .upload-table table tr:first-child td:first-child {
                -moz-border-radius-topleft:0px;
                -webkit-border-top-left-radius:0px;
                border-top-left-radius:0px;
            }
            .upload-table table tr:first-child td:last-child {
                -moz-border-radius-topright:0px;
                -webkit-border-top-right-radius:0px;
                border-top-right-radius:0px;
            }
            .upload-table tr:last-child td:first-child{
                -moz-border-radius-bottomleft:0px;
                -webkit-border-bottom-left-radius:0px;
                border-bottom-left-radius:0px;
            }
            .upload-table tr:hover td{
                background-color:#e5e5e5;
            }
            .upload-table td{
                vertical-align:middle;
                background:-o-linear-gradient(bottom, #ffffff 5%, #e5e5e5 100%);	
                background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #ffffff), color-stop(1, #e5e5e5) ); 	
                background:-moz-linear-gradient( center top, #ffffff 5%, #e5e5e5 100% );	
                /*filter:progid:DXImageTransform.Microsoft.gradient(startColorstr="#ffffff", endColorstr="#e5e5e5");*/
                background: -o-linear-gradient(top,#ffffff,e5e5e5);
                background-color:#ffffff;
                border:1px solid #e5e5e5;
                border:1px solid #e5e5e5;
                border-width:0px 1px 1px 0px;
                text-align:left;
                padding:7px;
                /* font-weight:bold; */
                font-size:11px;
                font-family:tahoma;
                font-style: normal;
                color:#000000;
            }
            .upload-table tr:last-child td{
                border-width:0px 1px 0px 0px;
            }
            .upload-table tr td:last-child{
                border-width:0px 0px 1px 0px;
            }
            .upload-table tr:last-child td:last-child{
                border-width:0px 0px 0px 0px;
            }
            .upload-table thead tr:first-child td{
                /* background:-o-linear-gradient(bottom, #cccccc 5%, #b2b2b2 100%);	
                background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #cccccc), color-stop(1, #b2b2b2) );
                background:-moz-linear-gradient( center top, #cccccc 5%, #b2b2b2 100% );	
                filter:progid:DXImageTransform.Microsoft.gradient(startColorstr="#cccccc", endColorstr="#b2b2b2");	
                background: -o-linear-gradient(top,#cccccc,b2b2b2);
                background-color:#cccccc;
                border:0px solid #ffffff;*/
                background:-o-linear-gradient(bottom, #F9F9F9 5%, #E3E4E6 100%);	
                background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #F9F9F9), color-stop(1, #E3E4E6) );
                background:-moz-linear-gradient( center top, #F9F9F9 5%, #E3E4E6 100% );	
                filter:progid:DXImageTransform.Microsoft.gradient(startColorstr="#cccccc", endColorstr="#E3E4E6");	
                background: -o-linear-gradient(top,#F9F9F9,E3E4E6);
                background-color:#F9F9F9;
                border:0px solid #ffffff;
                text-align:center;
                border-width:0px 0px 1px 1px;
                font-size:13px;
                font-family:tahoma;
                font-weight:bold;
                color:#000000;
            }
            .upload-table thead tr:first-child:hover td{
                /* background:-o-linear-gradient(bottom, #cccccc 5%, #b2b2b2 100%);	
                background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #cccccc), color-stop(1, #b2b2b2) );	
                background:-moz-linear-gradient( center top, #cccccc 5%, #b2b2b2 100% );	
                filter:progid:DXImageTransform.Microsoft.gradient(startColorstr="#cccccc", endColorstr="#b2b2b2");	
                background: -o-linear-gradient(top,#cccccc,b2b2b2);
                background-color:#cccccc; */
            }
            .upload-table thead tr:first-child td:first-child{
                border-width:0px 0px 1px 0px;
            }
            .upload-table thead tr:first-child td:last-child{
                border-width:0px 0px 1px 1px;
            }
        </style>
        <script>
        if (Ext.BLANK_IMAGE_URL.substr(0, 5) != 'data:') {
            Ext.BLANK_IMAGE_URL = '../extjs/resources/images/default/s.gif';
        }
        Ext.QuickTips.init();
        var mainLayout = function(){
            var toolbarTop = new Ext.Toolbar ({
                id: 'toolbar', 
                items: [{
                    xtype: 'buttongroup',
                    title: 'Miscellaneous',
                    items: [{
                        xtype: 'button',
                        text: 'Main Screen',
                        handler: function(){
                            window.location='../ecom/WCOM002.php'
                        }   
                    }]
                },{
                    xtype: 'buttongroup',
                    title: 'Master',
                    items: [{
                        xtype: 'splitbutton',
                        text: 'Master Data',
                        menu: [{
                            text: 'Item',
                            handler: function(){
                                window.location='../emst_/WMST001.php'
                            }
                        },{
                            text: 'Item Group',
                            handler: function(){
                                window.location='../emst_/WMST003.php'
                            }
                        },{
                            text: 'Item Price',
                            handler: function(){
                                window.location='../emst_/WMST004.php'
                            }
                        },{
                            text: 'Approver',
                            menu: {
                                xtype: 'menu',
                                items: [{
                                    text: 'PR Approver',
                                    handler: function(){
                                        window.location='../emst_/WMST005.php'
                                    }
                                },{
                                    text: 'PR Procurement PIC',
                                    handler: function(){
                                        window.location='../emst_/WMST007.php'
                                    }
                                }]
                            }
                        },{
                            text: 'Supplier',
                            handler: function(){
                                window.location='../emst_/WMST006.php'
                            }
                        },{
                            text: 'Unit Measure',
                            handler: function(){
                                window.location='WMST008.php'
                            }
                        }]
                    },{
                        xtype: 'splitbutton',
                        text: 'Upload',
                        menu: [{
                            text: 'Register Item Price',
                            handler: function(){
                                window.location='WMST009.php'
                            }
                        },{
                            text: 'Update Item Price',
                            handler: function(){
                                window.location='WMST010.php'
                            }
                        }]
                    }]
                },'->',
                {
                    xtype: 'tbtext', //Logged is as:
                    text: '<h2>Welcome, <?php echo stripslashes(addslashes($sNama)); ?></h2>#USER ID: <?php echo $sUserId; ?> #BU: <?php echo $sBuLogin?>'
                },'-',{
                    xtype: 'button',
                    text: 'Logout',
                    handler:function(){  
                        Ext.Msg.confirm('Confirm', 'Do you want to Log out?', function(btn, text){
                            if (btn == 'yes'){
                                window.location='../db/Login/Logout.php';
                            }
                        })
                    }
                }]
            });
            var panelCenter = Ext.create('Ext.form.Panel', {
                border: false,  
                bodyPadding: '15',
                title: 'Upload Item Price Master',
                html: '<form method="post" enctype="multipart/form-data" action="WMST009.php" accept="application/msexcel">'
                        +'<div>'
                            +'<label>Please upload your Excel file (.xls) : </label>'
                        +'</div>'
                        +'<div class="file-div">'
                            +'<input type="file" class="upload-field" id="upload-field" name="upload-field" />'
                        +'</div>'
                        +'<div>'
                            +'<input id="submitBtn" name="submitBtn" type="submit" value="Upload" style="width: 70px; height: 30px;" />'
                        +'</div>'
                      +'</form>'
                      +'<br>*Note: Click <a href="../lib/FORMAT_UPLOAD_PRICE_LIST.xls">here</a> to download example file to upload Item Price Master'
                      +'<?php echo '<br>'.$html; ?>'
            });
            var mainView = new Ext.create('Ext.Viewport',{
                layout: 'border', 
                padding: '5',
                items: [{
                    region: 'north',
                    split:true, 
                    border:false, 
                    items: [toolbarTop]
                },{
                    region: 'center',
                    id: 'content', 
                    autoScroll: true,
                    items: [panelCenter]
                }],
                renderTo: Ext.getBody()
            });
        }
        Ext.onReady(mainLayout);
        </script>
    </head>
    <body>
    </body>
</html>