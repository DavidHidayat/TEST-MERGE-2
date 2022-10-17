<?php session_start(); 
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Constant.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Common.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/CONTROLLER/PAGING.php";
if(isset($_SESSION['sUserId']))
{      
    $sUserId            = $_SESSION['sUserId'];
    $sActiveFlag		= $_SESSION['sactiveFlag'];
    $sActiveFlagLogin	= $_SESSION['sactiveFlagLogin'];
    
    if($sUserId != '' && $sActiveFlag == 'A' && $sActiveFlagLogin == 'A')
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
        
        if($sRoleId == 'ROLE_03' || $sRoleId == 'ROLE_07' || $sRoleId == 'ROLE_04')
        {
            $fileType   = $_FILES['uploadFile']['type'];
            $fileName   = $_FILES['uploadFile']['name'];
            $mstType    = $_POST['mstType'];
            $actionType = $_POST['actionType'];
            
            if(isset($_POST['btn-upload']))
            {
                //if(!empty($fileName) && $actionType != "" && $mstType != "")
                if(!empty($fileName))
                {
                    if($fileType=="application/vnd.ms-excel")
                    {
                        // utilize class phpExcelReader
                        include "../lib/excel/excel_reader2.php";
                        
                        // read excel file
                        $data = new Spreadsheet_Excel_Reader($_FILES['uploadFile']['tmp_name']);

                        // read row
                        $rowData = $data->rowcount($sheet_index=0);

                        // initial value
                        $countSuccess   = 0;
                        $countFailed    = 0;
                        $countError     = 0;
                        $currentDate    = date(Ymd);
                        
                        // read row start from 2nd row
                        for ($x = 2; $x <= $rowData; $x++)
                        {
                            $uploadItemStatus= '';
                            $msgError       = '';

                            $itemCd         = strtoupper(trim($data->val($x, 1)));
                            $supplierCd     = trim($data->val($x, 2));
                            $unitCd         = trim($data->val($x, 3));
                            $itemPrice      = trim($data->val($x, 4));
                            $effectiveDate  = trim($data->val($x, 5));
                            $getYear        = substr($effectiveDate, 0, 4);
                            $getMonth       = substr($effectiveDate, 4, 2);
                            $getDate        = substr($effectiveDate, 6, 2);
                            $leadTime       = trim($data->val($x, 6));
                            
                            if(!is_numeric($itemPrice))
                            {
                                $itemPrice = 0;
                            }
                            $itemPrice      = number_format($itemPrice);
                            $itemPrice      = str_replace(',', '',$itemPrice);
                            
                            $query_select_count_m_item_upload = "select 
                                                                    count(*) as UPLOAD_COUNT 
                                                                 from 
                                                                    EPS_M_ITEM_UPLOAD";
                            $sql_select_count_m_item_upload = $conn->query($query_select_count_m_item_upload);
                            $row_select_count_m_item_upload = $sql_select_count_m_item_upload->fetch(PDO::FETCH_ASSOC);
                            $uploadCount = $row_select_count_m_item_upload['UPLOAD_COUNT'];

                            if($uploadCount == 0)
                            {
                                $sequences = '1';
                            } 
                            else
                            {
                                $sequences = $sequences + 1;
                            }
                            $sequencesNo = str_pad($sequences, 5, "0", STR_PAD_LEFT);
                            $uploadId = 'UPD'.$currentDate.$sequencesNo;
                            
                            /** 
                             * SELECT EPS_M_UNIT_MEASURE
                             *  Check UM exist or not
                             * **/
                            $query_select_m_unitCd = "select 
                                                        UNIT_CD
                                                        ,UNIT_NAME
                                                    from
                                                        EPS_M_UNIT_MEASURE
                                                    where
                                                        UNIT_CD = '$unitCd'";
                            $sql_select_m_unitCd = $conn->query($query_select_m_unitCd);
                            $row_select_m_unitCd = $sql_select_m_unitCd->fetch(PDO::FETCH_ASSOC);
        
                            /** 
                             * SELECT EPS_M_ITEM_GROUP
                             *  Check Item Group exist or not
                             * **/
                            $query_select_m_itemGroup = "select 
                                                            ITEM_GROUP_CD
                                                            ,ITEM_GROUP_NAME
                                                        from
                                                            EPS_M_ITEM_GROUP
                                                        where
                                                            ITEM_GROUP_CD = '$itemGroupCd'";
                            $sql_select_m_itemGroup = $conn->query($query_select_m_itemGroup);
                            $row_select_m_itemGroup = $sql_select_m_itemGroup->fetch(PDO::FETCH_ASSOC);
        
                            /** 
                             * SELECT EPS_M_SUPPLIER
                             *  Check Supplier exist or not
                             * **/
                            $query_select_m_supplier = "select 
                                                            SUPPLIER_CD
                                                            ,SUPPLIER_NAME
                                                        from
                                                            EPS_M_SUPPLIER
                                                        where
                                                            SUPPLIER_CD = '$supplierCd'";
                            $sql_select_m_supplier = $conn->query($query_select_m_supplier);
                            $row_select_m_supplier = $sql_select_m_supplier->fetch(PDO::FETCH_ASSOC);
        
                            /** 
                             * SELECT EPS_M_ITEM
                             *  Check Item exist or not
                             * **/
                            $query_select_m_item = "select 
                                                        ITEM_CD
                                                        ,ITEM_NAME
                                                    from
                                                        EPS_M_ITEM
                                                    where
                                                        ITEM_CD = '$itemCd'";
                            $sql_select_m_item = $conn->query($query_select_m_item);
                            $row_select_m_item = $sql_select_m_item->fetch(PDO::FETCH_ASSOC);
        
                            /** 
                             * SELECT EPS_M_ITEM_UPLOAD
                             *  Check Item Price exist or not
                             * **/
                            $query_select_m_item_price = "select 
                                                            ITEM_CD
                                                            ,SUPPLIER_CD
                                                        from
                                                            EPS_M_ITEM_PRICE
                                                        where
                                                            ITEM_CD = '$itemCd'
                                                            and SUPPLIER_CD = '$supplierCd'
                                                            and EFFECTIVE_DATE_FROM = '$effectiveDate'";
                            $sql_select_m_item_price = $conn->query($query_select_m_item_price);
                            $row_select_m_item_price = $sql_select_m_item_price->fetch(PDO::FETCH_ASSOC);

                            /** 
                             * SELECT EPS_M_ITEM_UPLOAD
                             *  Check Item Upload exist or not
                             * **/
                            $query_select_m_item_upload = "select 
                                                                ITEM_CD
                                                                ,ITEM_NAME
                                                            from
                                                                EPS_M_ITEM_UPLOAD
                                                            where
                                                                ITEM_CD = '$itemCd'";
                            $sql_select_m_item_upload = $conn->query($query_select_m_item_upload);
                            $row_select_m_item_upload = $sql_select_m_item_upload->fetch(PDO::FETCH_ASSOC);
                            
                            if($itemCd != '')
                            {
                                if($unitCd != '')
                                {
                                    if($itemPrice != '')
                                    {
                                        if($supplierCd != '')
                                        {
                                            if($effectiveDate != '')
                                            {
                                                if(is_numeric($effectiveDate))
                                                {
                                                    if(checkdate((int)$getMonth, (int)$getDate , (int)$getYear) == 1)
                                                    {
                                                        //if($leadTime != "" && is_numeric($leadTime))
                                                        //{
                                                            if($itemPrice > 0)
                                                            {
                                                                if($row_select_m_unitCd)
                                                                {
                                                                    if($row_select_m_supplier)
                                                                    {   
                                                                        if($row_select_m_item)
                                                                        {
                                                                            if(!$row_select_m_item_price)
                                                                            {
                                                                                if($row_select_m_item_upload)
                                                                                {
                                                                                    $uploadItemStatus = 'E';
                                                                                    $msgError = 'Duplicate Error. Item Code already exist in Upload Item Master Data.';
                                                                                    $countError++;
                                                                                }
                                                                            }else{
                                                                                $uploadItemStatus = 'E';
                                                                                $msgError = 'Duplicate Error. Item Price already exist in Master Data.';
                                                                                $countError++;
                                                                            }         
                                                                        }else{
                                                                            $uploadItemStatus = 'E';
                                                                            $msgError = 'Existence Error. Item Code is not found.';
                                                                            $countError++;
                                                                        }
                                                                    }else{
                                                                        $uploadItemStatus = 'E';
                                                                        $msgError = 'Existence Error. Supplier Code is not found.';
                                                                        $countError++;
                                                                    }        
                                                                }else{
                                                                    $uploadItemStatus = 'E';
                                                                    $msgError = 'Existence Error. U/M is not found.';
                                                                    $countError++;
                                                                }
                                                            }else{
                                                                $uploadItemStatus = 'E';
                                                                $msgError = 'Mandatory. Please input price > 0.';
                                                                $countError++;
                                                            }
                                                        //}else{
                                                        //    $uploadItemStatus = 'E';
                                                        //    $msgError = 'Digit Error. Please input correct lead time.';
                                                        //    $countError++;
                                                        //}
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
                                            $msgError = 'Mandatory. Please fill in the Supplier Code column.';
                                            $countError++;
                                        }
                                    }
                                    else{
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
                                $msgError = 'Mandatory. Please fill in the Item Code column.';
                                $countError++;
                            }
                            
                            $query_insert_item_upload = "insert into
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
                                                            ,LEAD_TIME
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
                                                            ,'$leadTime'
                                                            ,'$uploadItemStatus'
                                                            ,'$msgError'
                                                            ,convert(VARCHAR(24), GETDATE(), 120)
                                                            ,'$sUserId'
                                                            ,convert(VARCHAR(24), GETDATE(), 120)
                                                            ,'$sUserId'
                                                        )";
                            $sql_insert_item_upload = $conn->query($query_insert_item_upload);

                            if($sql_insert_item_upload)
                            {
                                $countSuccess++;
                            }
                            else
                            {
                                $countFailed++;
                            }
                        }
                        
                        $htmlMsg ="<h3 id=warn>Upload process finished</h3>";
                        $htmlMsg.="<p id=par>Success data upload: ".$countSuccess;
                        $htmlMsg.="<br>Failure data upload: ".$countFailed;
                        $htmlMsg.="<br>Error data upload: ".$countError;
                        $htmlMsg.="<br>";
                        $htmlMsg.="<table class='table table-striped table-bordered'>";
                        $htmlMsg.="<thead><tr><th width=20px>NO.</td>";
                        $htmlMsg.="<th width=50px>CODE</td>";
                        $htmlMsg.="<th width=320px>NAME</td>";
                        $htmlMsg.="<th width=50px>U/M</td>";
                        $htmlMsg.="<th width=50px>PRICE</td>";
                        $htmlMsg.="<th width=60px>SUPPLIER</td>";
                        $htmlMsg.="<th width=40px>CATEGORY</td>";
                        $htmlMsg.="<th width=60px>EFFECTIVE DATE</td>";
                        $htmlMsg.="<th width=290px>ERROR MESSAGE</td></tr></thead>";
                        
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
                                            order by
                                                ITEM_CD";
                        $sql_upload = $conn->query($query_upload);
                        $htmlMsg.="<tbody>";
                        while($row_upload = $sql_upload->fetch(PDO::FETCH_ASSOC)){
                            $itemCd         = $row_upload['ITEM_CD'];
                            $itemName       = $row_upload['ITEM_NAME'];
                            $itemGroupCd    = $row_upload['ITEM_GROUP_CD'];
                            $supplierCd     = $row_upload['SUPPLIER_CD'];
                            $unitCd         = $row_upload['UNIT_CD'];
                            $itemPrice      = $row_upload['ITEM_PRICE'];
                            $effectiveDate  = $row_upload['EFFECTIVE_DATE'];
                            $msgError       = $row_upload['UPLOAD_MESSAGE'];
                            $htmlMsg.="<tr><td class='td-number'>".$i.".</td><td>".$itemCd."</td><td>".$itemName."</td><td>".$unitCd."</td><td class='td-number'>".number_format($itemPrice)."</td><td>".$supplierCd."</td><td>".$itemGroupCd."</td><td>".$effectiveDate."</td><td>".$msgError."</td></tr>";
                            $i++;
                        }
                        $htmlMsg.= "</tbody></table>";
                        if($countError > 0)
                        {
                            $query_delete_m_item_upload = "delete from
                                                                EPS_M_ITEM_UPLOAD";
                            $conn->query($query_delete_m_item_upload);
                        }
                        else
                        {
                            if($countError == 0)
                            {
                                $query_select_m_item_upload = "select 
                                                                    ITEM_CD
                                                                    ,ITEM_NAME
                                                                    ,ITEM_GROUP_CD
                                                                    ,SUPPLIER_CD
                                                                    ,UNIT_CD
                                                                    ,ITEM_PRICE
                                                                    ,EFFECTIVE_DATE
                                                                    ,LEAD_TIME
                                                                from 
                                                                    EPS_M_ITEM_UPLOAD 
                                                                order by
                                                                    ITEM_CD ";
                                $sql_select_m_item_upload = $conn->query($query_select_m_item_upload);
                                while($row_select_m_item_upload = $sql_select_m_item_upload->fetch(PDO::FETCH_ASSOC)){
                                    $itemCd         = strtoupper($row_select_m_item_upload['ITEM_CD']);
                                    $itemName       = strtoupper($row_select_m_item_upload['ITEM_NAME']);
                                    $itemGroupCd    = strtoupper($row_select_m_item_upload['ITEM_GROUP_CD']);
                                    $supplierCd     = strtoupper($row_select_m_item_upload['SUPPLIER_CD']);
                                    $unitCd         = strtoupper($row_select_m_item_upload['UNIT_CD']);
                                    $itemPrice      = $row_select_m_item_upload['ITEM_PRICE'];
                                    $itemPrice      = number_format($itemPrice);
                                    $itemPrice      = str_replace(',', '',$itemPrice);
                                    $effectiveDate  = $row_select_m_item_upload['EFFECTIVE_DATE'];
                                    $leadTime       = $row_select_m_item_upload['LEAD_TIME'];

                                    $query_insert_m_item_price = "insert into
                                                                    EPS_M_ITEM_PRICE
                                                                    (
                                                                        ITEM_CD
                                                                        ,SUPPLIER_CD
                                                                        ,UNIT_CD
                                                                        ,CURRENCY_CD
                                                                        ,ITEM_PRICE
                                                                        ,EFFECTIVE_DATE_FROM
                                                                        ,LEAD_TIME
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
                                                                        ,'$leadTime'
                                                                        ,convert(VARCHAR(24), GETDATE(), 120)
                                                                        ,'$sUserId'
                                                                        ,convert(VARCHAR(24), GETDATE(), 120)
                                                                        ,'$sUserId'
                                                                    )";
                                    $conn->query($query_insert_m_item_price);
                                }
                                $htmlMsg .= "<div class='alert alert-success'><strong>Success!</strong> Process upload finish ".$countSuccess." records.</div>";
                                
                                $query_delete_m_item_upload = "delete from
                                                                    EPS_M_ITEM_UPLOAD";
                                $conn->query($query_delete_m_item_upload);
                            } 
                        }
                    }
                    else
                    {
                        $htmlMsgError = "<div class='alert'><strong>Mandatory!</strong> Please select file type .xls to upload.</div>";
                    }
                }
                /*else if(empty($fileName))
                {
                    $htmlMsgError = "<strong>Mandatory!</strong> Please select file to upload.";
                }
                else if($actionType == "" && $mstType == "")
                {
                    $htmlMsgError = "<strong>Mandatory!</strong> Please select Master and Action Type.";
                }*/
                else
                {
                    $htmlMsgError = "<div class='alert'><strong>Mandatory!</strong> Please fill all the field.</div>";
                }
            }
        }
        else
        {
        ?>
            <script language="javascript"> document.location="../ecom/WCOM012.php"; </script> 
        <?php
        }
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

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" href="../css/bootstrap.min.css" ></link>
        <link rel="stylesheet" href="../css/bootstrap-responsive.min.css"></link>
        <link rel="stylesheet" href="../css/font-awesome.css">
        <link rel="stylesheet" href="../css/style.css" ></link>
        <link rel="stylesheet" href="../css/dashboard.css" ></link>
        <link rel="stylesheet" href="../css/additional.css" ></link>
        <link rel="stylesheet" href="../lib/jquery/jquery-ui-1.11.2.custom/jquery-ui.css">
        
        <script src="../lib/jquery/jquery-1.11.0.min.js"></script> 
        <script src="../lib/jquery/jquery-ui-1.11.2.custom/jquery-ui.min.js"></script> 
        <script src="../lib/jquery/jquery-ui-1.11.2.custom/jquery-ui.js"></script> 
        <script type="text/javascript" src="../js/Common.js"></script>
        <script type="text/javascript" src="../js/Common_JQuery.js"></script>
        <script>
            maximize();
        </script>
        <title>EPS</title>
        <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
            <script src="../js/html5.js"></script>
        <![endif]-->
    </head>
    <body> 
<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container"> 
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span> 
            </a>
            <a class="brand" href="#">
                e-Purchase System
            </a>
            <div class="nav-collapse">
                <ul class="nav pull-right">
                <li>
                    <a href="#" class="dropdown-toggle">
                        <i class="icon-user"></i> Welcome, <?php echo stripslashes(addslashes($sNama)); ?> (#User ID: <?php echo $sUserId; ?> #BU Code: <?php echo trim($sBuLogin); ?>) 
                    </a>
                </li>
            </div><!--/.nav-collapse --> 
        </div><!-- /container --> 
    </div><!-- /navbar-inner --> 
</div><!-- /navbar -->
    
<div class="subnavbar">
    <div class="subnavbar-inner">
        <div class="container">
            <ul class="mainnav">
                <li>
                    <a href="../ecom/WCOM002.php">
                        <i class="icon-chevron-up"></i><span>Main</span> 
                    </a> 
                </li>
                <li>
                    <a href="WMST003.php">
                        <i class="icon-asterisk"></i><span>Item Group</span> 
                    </a> 
                </li> 
                <li>
                    <a href="WMST001.php">
                        <i class="icon-shopping-cart"></i><span>Item</span> 
                    </a> 
                </li> 
                <li class="active">
                    <a href="WMST004.php">
                        <i class="icon-money"></i><span>Item Price</span> 
                    </a> 
                </li>
                <li>
                    <a href="WMST005.php">
                        <i class="icon-group"></i><span>PR Approver</span> 
                    </a> 
                </li> 
                <li>
                    <a href="WMST007.php">
                        <i class="icon-sitemap"></i><span>Proc. In Charge</span> 
                    </a> 
                </li> 
                <li>
                    <a href="WMST006.php">
                        <i class="icon-truck"></i><span>Supplier</span> 
                    </a> 
                </li> 
                <li>
                    <a href="WMST010.php">
                        <i class="icon-key"></i><span>User ID</span> 
                    </a> 
                </li> 
                <li id="signout">
                    <a href="#">
                        <i class="icon-signout"></i><span>Logout</span> 
                    </a>
                </li>
            </ul>
        </div> <!-- /container --> 
    </div><!-- /subnavbar-inner --> 
</div><!-- /subnavbar -->

<div class="main">
    <div class="main-inner">
        <div class="container">
            <div class="row">
                <div class="span12"> 
                    <div class="alert" id="mandatory-msg-1" style="display: none">
                        <strong>Mandatory!</strong> Please fill all the field.
                    </div>
                    <div class="alert" id="mandatory-msg-2" style="display: none">
                        <strong>Data not found!</strong> No results match with your search.
                    </div>
                    
                    <!----- Item Master ---->
                    <div class="widget ">
                        <div class="widget-header">
                            <i class="icon-upload"></i>
                            <h3>Upload (Update Item Price)</h3>
                        </div>
                        <div class="widget-content">
                            <form id="WMST008Form"  method="post" enctype="multipart/form-data" action="WMST008.php">
                                <div class="control-group">	
                                    <table class="table-non-bordered">
                                        <tr>
                                            <td>
                                                <label class="control-label" for="plantCd">Select file to upload: </label>
                                                <input type="file" id="uploadFile" name="uploadFile" style="width: 95%;"></input>
                                            </td>
                                            <!--<td>
                                                <label class="control-label" for="mstType">Master Type: </label>
                                                <div class="controls">
                                                    <select id="mstType" class="span3" name="mstType">
                                                        <option value=""></option>
                                                        <option value="itemPriceMst">ITEM PRICE MASTER</option>
                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="actionType">Action Type: </label>
                                                <div class="controls">
                                                    <select id="actionType" class="span2" name="actionType">
                                                        <option value=""></option>
                                                        <option value="addItemPriceMst">REGISTER</option>
                                                        <option value="updateItemPriceMst">UPDATE</option>
                                                    </select>
                                                </div>
                                            </td>-->
                                        </tr>
                                    </table>
                                </div>
                                <div>
                                    <button class="btn btn-primary" id="btn-upload" type="submit" name="btn-upload">Upload</button> 
                                    <button class="btn" id="btn-reset">Reset</button>
                                </div> 
                            </form>
                            ** Click here to download example file to upload <a href="../lib/FORMAT_UPLOAD_UPDATE_PRICE_LIST.xls">Update Item Price Master </a>
                    
                    <!---------------------------------- Message --------------------------------->
                    <?php
                    if($htmlMsg != "")
                    {
                    ?>
                        <div>
                            <?php echo $htmlMsg;?>
                        </div>   
                    <?php    
                    }
                    else
                    {
                        if($htmlMsgError != "")
                        {
                            echo $htmlMsgError;
                        }
                    }
                    ?>
                        </div>
                    </div>
                </div><!-- /span12 -->
            </div><!-- /row -->
        </div><!-- /container -->
    </div><!-- /main-inner -->
</div><!-- /main -->

<div class="footer">
    <div class="footer-inner">
	<div class="container">
            <div class="row">
		<div class="span12">
                    &copy; 2018 PT.TD Automotive Compressor Indonesia. All rights reserved. 
                </div> <!-- /span12 -->	
            </div> <!-- /row -->
	</div> <!-- /container -->		
    </div> <!-- /footer-inner -->	
</div> <!-- /footer -->
<div id="dialog-confirm-logout" title="Confirm" style="display: none;"></div>

<!-- Le javascript
================================================== --> 
<!-- Placed at the end of the document so the pages load faster --> 
<script src="../js/bootstrap.js"></script>
    </body>
</html>