<?php session_start(); 
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PR/EPS_T_PR_SEQUENCE.php";
if(isset($_SESSION['sNPK']))
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
        $sUserId    = $_SESSION['sUserId'];
        $sBuLogin   = $_SESSION['sBuLogin'];
        $sUserType  = $_SESSION['sUserType'];
        
    }
    else
    {
    ?>
        <script language="javascript"> document.location="../ecom/WCOM011.php"; </script> 
    <?php    
    }
}
else
{	
?>
    <script language="javascript"> document.location="../ecom/WCOM010.php"; </script> 
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
    
    $query = "select count(*) as UPLOAD_COUNT from EPS_T_PR_UPLOAD";
    $sql = $conn->query($query);
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    $uploadCount = $row['UPLOAD_COUNT'];
    
    if($uploadCount == 0){
        $sequences = '1';
    } else{
        $query = "select UPLOAD_ID from EPS_T_PR_UPLOAD order by UPLOAD_ID desc";
        $sql = $conn->query($query);
        $row = $sql->fetch(PDO::FETCH_ASSOC);
        $uploadId = $row['UPLOAD_ID'];
        $companyCd = substr($uploadId, 0, 1);
        if($companyCd == 'H'){
            $sequences = substr($uploadId, 13);
        }else{
            $sequences = substr($uploadId, 12);
        }
        $sequences = $sequences + 1;
    }
    $uploadId = trim($sBunit).$currentDate.$sequences;
    
    // read row start from 2nd row
    for ($i=2; $i <= $rowData; $i++)
    {
        $uploadItemStatus= '';
        $msgError       = '';
        
        $itemCd         = '99';
        $supplierCd     = 'SUP99';
        $currencyCd     = 'IDR';
        $qty            = 0;
        $itemName       = trim($data->val($i, 1));
        $itemName       = preg_replace('/\s+/', ' ',$itemName);
        $deliveryDate   = $data->val($i, 2);
        
        $getYear        = substr($deliveryDate, 0, 4);
        $getMonth       = substr($deliveryDate, 4, 2);
        $getDate        = substr($deliveryDate, 6, 2);
        $accountNo      = trim($data->val($i, 3));
        $qty            = trim($data->val($i, 4));   
        if(!is_numeric($qty)){
            $qty = 0;
        }
        $query = "select     
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
                right join
                    EPS_M_ITEM 
                on 
                    EPS_M_ITEM_PRICE.ITEM_CD = EPS_M_ITEM.ITEM_CD 
                left join
                    EPS_M_SUPPLIER 
                on 
                    EPS_M_ITEM_PRICE.SUPPLIER_CD = EPS_M_SUPPLIER.SUPPLIER_CD
                where     
                    1=1 and (EPS_M_ITEM.ITEM_NAME = '$itemName')
                order by 
                    EPS_M_ITEM_PRICE.EFFECTIVE_DATE_FROM desc";
        $sql = $conn->query($query);
        $row = $sql->fetch(PDO::FETCH_ASSOC);
        if($row){
            $itemCd         = $row['ITEM_CD'];
            $itemName       = $row['ITEM_NAME'];
            $unitCd         = $row['UNIT_CD'];
            $supplierCd     = $row['SUPPLIER_CD'];
            $supplierName   = $row['SUPPLIER_NAME'];
            $itemPrice      = $row['ITEM_PRICE'];
            $itemPrice      = number_format($itemPrice);
            $itemPrice      = str_replace(',', '',$itemPrice);
            $currencyCd     = $row['CURRENCY_CD'];
        }
        $amount         = $itemPrice * $qty;
        
        if($itemName != '')
        {
            if($row)
            {
                if($deliveryDate != '')
                {
                    if(is_numeric($deliveryDate))
                    {
                        if(checkdate((int)$getMonth, (int)$getDate , (int)$getYear) == 1)
                        {
                            if($deliveryDate > $currentDate)
                            {
                                if($accountNo != '')
                                {
                                    $query = "select ACCOUNT_NO from EPS_M_ACCOUNT where ACCOUNT_NO='$accountNo'";
                                    $sql = $conn-> query($query);
                                    $row = $sql->fetch(PDO::FETCH_ASSOC);
                                    if($row)
                                    {
                                        if($qty <= 0)
                                        {
                                            $uploadItemStatus = 'E';
                                            $msgError = 'Mandatory. Please fill in the Qty column > 0.';
                                            $countError++;
                                        }
                                    }
                                    else{
                                        $uploadItemStatus = 'E';
                                        $msgError = 'Existence Error. Expense No. is not found Master.';
                                        $countError++;
                                    }
                                }
                                else{
                                    $uploadItemStatus = 'E';
                                    $msgError = 'Mandatory. Please fill in the Expense No. column.';
                                    $countError++;
                                }
                            }
                            else
                            {
                                $uploadItemStatus = 'E';
                                $msgError = 'Range Error. Please input delivery date > current date.';
                                $countError++;
                            }
                        }
                        else
                        {
                            $uploadItemStatus = 'E';
                            $msgError = 'Digit Error. Please input correct delivery date (YYYYMMDD).';
                            $countError++;
                        }
                    }
                    else
                    {
                        $uploadItemStatus = 'E';
                        $msgError = 'Type Error. Please input Delivery Date in numeric type.';
                        $countError++;
                    }
                }
                else
                {
                    $uploadItemStatus = 'E';
                    $msgError = 'Mandatory. Please fill in the Delivery Date column.';
                    $countError++;
                }
            }
            else{
                $itemPrice = 0;
                $uploadItemStatus = 'E';
                $msgError = 'Existence Error. Item Name is not found in Master.';
                $countError++;
            }
        }
        else{
            $itemPrice = 0;
            $uploadItemStatus = 'E';
            $msgError = 'Mandatory. Please fill in the Item Name column.';
            $countError++;
        }
        $prNo = getPrNo($sNPK, trim($sBuLogin), 'getPrNo');
        
        $query = "insert into
                    EPS_T_PR_UPLOAD
                    (
                        UPLOAD_ID
                        ,PR_NO
                        ,REQUESTER
                        ,BU_CD
                        ,USERID
                        ,ITEM_CD
                        ,ITEM_NAME
                        ,DELIVERY_DATE
                        ,QTY
                        ,ITEM_PRICE
                        ,AMOUNT
                        ,CURRENCY_CD
                        ,ITEM_TYPE_CD
                        ,ACCOUNT_NO
                        ,UNIT_CD
                        ,SUPPLIER_CD
                        ,SUPPLIER_NAME
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
                        ,'$prNo'
                        ,'$sNPK'
                        ,'$sBunit'
                        ,'$sUserId'
                        ,'$itemCd'
                        ,'$itemName'
                        ,'$deliveryDate'
                        ,'$qty'
                        ,'$itemPrice'
                        ,'$amount'
                        ,'$currencyCd'
                        ,'1'
                        ,'$accountNo'
                        ,'$unitCd'
                        ,'$supplierCd'
                        ,'$supplierName'
                        ,'$uploadItemStatus'
                        ,'$msgError'
                        ,convert(VARCHAR(24), GETDATE(), 120)
                        ,'$sNPK'
                        ,convert(VARCHAR(24), GETDATE(), 120)
                        ,'$sNPK'
                    )";
        $sql = $conn->query($query);
        if($sql){
            $countSuccess++;
        }else{
            $countFailed++;
        }
    }
    if($countError > 0){
        $html ="<br><h3 id=warn>Upload process finished</h3>";
        $html.="<p id=par>Success data upload: ".$countSuccess;
        $html.="<br>Failure data upload: ".$countFailed;
        $html.="<br>Error data upload: ".$countError;
        $html.="<br><br>";
        $html.="<table border=1 class=upload-table>";
        $html.="<thead><tr><td width=20px align=center>NO.</td>";
        $html.="<td width=50px align=center>CODE</td>";
        $html.="<td width=320px align=center>ITEM NAME</td>";
        $html.="<td width=100px align=center>DELIVERY DATE</td>";
        $html.="<td width=60px align=center>EXPENSE</td>";
        $html.="<td width=40px align=center>QTY</td>";
        $html.="<td width=290px align=center>ERROR MESSAGE</td></tr></thead>";
        $i = 1;
        $query = "select 
                    ITEM_CD
                    ,ITEM_NAME
                    ,DELIVERY_DATE
                    ,ACCOUNT_NO
                    ,QTY
                    ,UPLOAD_MESSAGE
                  from 
                    EPS_T_PR_UPLOAD 
                  where 
                    UPLOAD_ID = '$uploadId'";
        $sql = $conn->query($query);
        $html.="<tbody>";
        while($row = $sql->fetch(PDO::FETCH_ASSOC)){
            $itemCd         = $row['ITEM_CD'];
            $itemName       = $row['ITEM_NAME'];
            $deliveryDate   = $row['DELIVERY_DATE'];
            $accountNo      = $row['ACCOUNT_NO'];
            $qty            = $row['QTY'];
            $msgError       = $row['UPLOAD_MESSAGE'];
            $html.="<tr><td>".$i.".</td><td>".$itemCd."</td><td>".$itemName."</td><td>".$deliveryDate."</td><td>".$accountNo."</td><td>".$qty."</td><td>".$msgError."</td></tr>";
            $i++;
        }
        $html.="</tbody>";
        $html.="</table>";
            
        $query = "delete from EPS_T_PR_UPLOAD where UPLOAD_ID = '$uploadId'";
        $sql = $conn->query($query);
    }else{
        // Redirect page
        echo "<script type='text/javascript'>document.location.href='WEPR008.php?uploadId=$uploadId'</script>";
        $_SESSION['EPSAuthority']='EPSUploadPrScreen';    
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
        <script type="text/javascript" src="../lib/jquery/jquery-1.8.3.min.js" ></script>
        <script type="text/javascript" src="../js/epr/WEPR009.js"></script>
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
            var roleIdLogin = '<?php echo $_SESSION['sRoleId'];?>';
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
                    title: 'PR',
                    items: [{
                        xtype: 'button',
                        text: 'PR List',
                        handler: function(){
                            window.location='../epr_/WEPR001.php'
                        }
                    },{
                        xtype: 'button',
                        text: 'PR Waiting',
                        handler: function(){
                            window.location='../epr_/WEPR013.php'
                        }
                    },{
                        xtype: 'button',
                        text: 'Create New PR',
                        handler: function(){
                            window.location='WEPR002.php'
                        }
                    },{
                        xtype: 'button',
                        text: 'Upload PR',
                        handler: function(){
                            window.location='WEPR007.php'
                        }
                    }]
                },{
                    xtype: 'buttongroup',
                    title: 'Search',
                    items: [{
                        xtype: 'button',
                        text: 'PR Search',
                        handler: function(){
                            
                            window.location='../epr_/WEPR090.php'
                        }
                    },{
                        xtype: 'button',
                        text: 'PO Search',
                        handler: function(){
                            if(roleIdLogin == "ROLE_03" || roleIdLogin == "ROLE_08")
                            {
                                window.location='../epr_/WEPR091.php'
                            }
                            else
                            {
                                Ext.Msg.alert('Message','Sorry, you are not auhotrized to access this menu.');
                            }
                        }
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
                title: 'Upload Purchase Requisition',
                html: '<form method="post" enctype="multipart/form-data" action="WEPR007.php" accept="application/msexcel">'
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
	         +'<br>*Note: Click <a href="../lib/PR_UPLOAD.xls">here</a> to download example file to upload PR'
                      +'<?php echo '<br>'.$html; ?>'
            });
            /** 
             * =======================================
             * Define Layout
             * =======================================
             **/
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