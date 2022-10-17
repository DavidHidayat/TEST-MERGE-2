<?php session_start(); 
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Constant.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Common.php";
if(isset($_SESSION['sUserId']))
{      
    $sUserId            = $_SESSION['sUserId'];
    $sActiveFlag		= $_SESSION['sactiveFlag'];
    $sActiveFlagLogin	= $_SESSION['sactiveFlagLogin'];
    
    if($sUserId != '' && $sActiveFlag == 'A' && $sActiveFlagLogin == 'A')
    {
        /** Unset SESSION */
        unset($_SESSION['prStatus']);
		
        $sNPK               = $_SESSION['sNPK'];
        $sNama              = $_SESSION['sNama'];
        $sBunit             = $_SESSION['sBunit'];
        $sSeksi             = $_SESSION['sSeksi'];
        $sKdper             = $_SESSION['sKdper'];
        $sNmPer             = $_SESSION['sNmper'];
        $sKdPlant           = $_SESSION['sKDPL'];
        $sNmPlant           = $_SESSION['sNMPL'];
        $sRoleId            = $_SESSION['sRoleId'];
        $sInet              = $_SESSION['sinet'];
        $sNotes             = $_SESSION['snotes'];
        $sBuLogin           = $_SESSION['sBuLogin'];
        $sUserType          = $_SESSION['sUserType'];
        $poScreen           = $_SESSION['poScreen'];
        $itemStatusSesssion = $_SESSION['itemStatusSession'];
        $updateDateSession  = $_SESSION['updateDateSession'];
        
        if($sRoleId == 'ROLE_02' || $sRoleId == 'ROLE_03' || $sRoleId == 'ROLE_04' || $sRoleId == 'ROLE_06' || $sRoleId == 'ROLE_07' || $sRoleId == 'ROLE_09' || $sRoleId == 'ROLE_10' || $sRoleId == 'ROLE_11')
        {
            if($poScreen == 'OutstandingPoDetailScreen')
            {
                $transferIdSession  = $_SESSION['transferIdSession'];
                $transferId         = $_GET['transferId'];

                if($transferIdSession == $transferId)
                {   
                    if($itemStatusSesssion == '1120' || $itemStatusSesssion == '1160')
                    {
                        $whereTransfer = array();
                        if($transferId){
                            $whereTransfer[] = "EPS_T_TRANSFER.TRANSFER_ID = '".$transferId."'";
                        }
                        $query = "select 
                                    convert(VARCHAR(24), EPS_T_PR_HEADER.PROC_ACCEPT_DATE, 103) as PROC_ACCEPT_DATE
                                    ,EPS_T_PR_HEADER.REQUESTER
                                    ,EPS_M_EMPLOYEE.NAMA1 as REQUESTER_NAME
                                    ,EPS_T_PR_HEADER.EXT_NO
                                    ,EPS_T_PR_HEADER.BU_CD
                                    ,EPS_T_PR_HEADER.PLANT_CD
                                    ,EPS_M_PLANT.PLANT_NAME
                                    ,EPS_T_PR_HEADER.PURPOSE
                                    ,EPS_T_PR_HEADER.REQ_BU_CD
                                    ,EPS_T_TRANSFER.PR_NO
                                    ,substring(EPS_T_PR_HEADER.ISSUED_DATE,7,2)+'/'+substring(EPS_T_PR_HEADER.ISSUED_DATE,5,2)+'/'+substring(EPS_T_PR_HEADER.ISSUED_DATE,1,4) as ISSUED_DATE
                                    ,EPS_T_TRANSFER.NEW_CHARGED_BU
                                    ,EPS_T_TRANSFER.ITEM_STATUS
                                    ,EPS_T_TRANSFER.NEW_ITEM_CD
                                    ,EPS_T_TRANSFER.NEW_ITEM_NAME
                                    ,substring(NEW_DELIVERY_DATE,7,2)+'/'+substring(NEW_DELIVERY_DATE,5,2)+'/'+substring(NEW_DELIVERY_DATE,1,4) as NEW_DELIVERY_DATE
                                    ,EPS_T_TRANSFER.NEW_QTY
                                    ,EPS_T_TRANSFER.ACTUAL_QTY
                                    ,EPS_T_TRANSFER.NEW_UNIT_CD
                                    ,EPS_T_TRANSFER.NEW_ITEM_TYPE_CD
                                    ,EPS_T_TRANSFER.NEW_ACCOUNT_NO
                                    ,EPS_T_TRANSFER.NEW_RFI_NO
                                    ,EPS_T_TRANSFER.NEW_SUPPLIER_CD
                                    ,EPS_T_TRANSFER.NEW_SUPPLIER_NAME
                                    ,EPS_T_TRANSFER.NEW_CURRENCY_CD
                                    ,EPS_T_PR_DETAIL.ITEM_PRICE
                                    ,EPS_T_TRANSFER.NEW_ITEM_PRICE
                                    ,EPS_T_TRANSFER.NEW_REMARK
                                    ,EPS_T_TRANSFER_SUPPLIER.LEAD_TIME
                                    ,EPS_T_TRANSFER_SUPPLIER.UNIT_TIME
                                    ,EPS_T_TRANSFER_SUPPLIER.ATTACHMENT_LOC
                                    ,EPS_T_TRANSFER_SUPPLIER.REMARK
                                    ,EPS_M_BUNIT.PLANT_ALIAS
                                    ,EPS_M_USER.INV_TYPE
                                    ,EPS_M_BUNIT.BU_NAME
                                    ,EPS_T_TRANSFER.CREATE_BY
                                    ,EPS_M_EMPLOYEE_2.NAMA1 as PROC_IN_CHARGE_NAME
                                from
                                    EPS_T_TRANSFER
                                left join
                                    EPS_T_PR_HEADER
                                on 
                                    EPS_T_TRANSFER.PR_NO = EPS_T_PR_HEADER.PR_NO
                                left join
                                    EPS_T_TRANSFER_SUPPLIER
                                on
                                    EPS_T_TRANSFER.TRANSFER_ID = EPS_T_TRANSFER_SUPPLIER.TRANSFER_ID
                                    and EPS_T_TRANSFER.NEW_SUPPLIER_CD =  EPS_T_TRANSFER_SUPPLIER.SUPPLIER_CD
                                left join
                                    EPS_M_BUNIT
                                on
                                    EPS_T_TRANSFER.NEW_CHARGED_BU = EPS_M_BUNIT.BU_CD
                                inner join
                                    EPS_M_EMPLOYEE
                                on 
                                    EPS_T_PR_HEADER.REQUESTER = EPS_M_EMPLOYEE.NPK
                                left join
                                    EPS_M_PLANT
                                on
                                    EPS_T_PR_HEADER.PLANT_CD = EPS_M_PLANT.PLANT_CD
                                left join
                                    EPS_M_USER 
                                on
                                    EPS_T_PR_HEADER.USERID = EPS_M_USER.USERID
                                left join
                                    EPS_M_EMPLOYEE EPS_M_EMPLOYEE_2
                                on
                                    EPS_T_PR_HEADER.PROC_IN_CHARGE = EPS_M_EMPLOYEE_2.NPK
                                left join
                                    EPS_T_PR_DETAIL 
                                on 
                                    EPS_T_PR_DETAIL.PR_NO = EPS_T_TRANSFER.PR_NO 
                                    and (REPLACE(REPLACE(REPLACE(EPS_T_PR_DETAIL.ITEM_NAME, CHAR(13), ''), CHAR(9), ''), ' ', '') = REPLACE(EPS_T_TRANSFER.ITEM_NAME, ' ', '')) ";
                        if(count($whereTransfer)) {
                            $query .= "where " . implode('and ', $whereTransfer);
                        }
//                        echo "$query";
                        $sql = $conn->query($query);
                        while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                            $procAcceptDate = $row['PROC_ACCEPT_DATE'];
                            $requester      = $row['REQUESTER'];
                            $requesterName  = $row['REQUESTER_NAME'];
                            $extNo          = $row['EXT_NO'];
                            $prBuCd         = $row['BU_CD'];
                            $plantName      = $row['PLANT_NAME'];
                            $purpose        = $row['PURPOSE'];
                            $prIssuer       = $row['REQ_BU_CD'];
                            $prNo           = $row['PR_NO'];
                            $issuedDate     = $row['ISSUED_DATE'];
                            $newPrCharged   = $row['NEW_CHARGED_BU'];
                            $itemStatus     = $row['ITEM_STATUS'];
                            $newItemCd      = $row['NEW_ITEM_CD'];
                            $newItemName    = htmlspecialchars($row['NEW_ITEM_NAME']);
                            $newDeliveryDate= $row['NEW_DELIVERY_DATE'];
                            $newQty         = $row['NEW_QTY'];
                            $actualQty      = $row['ACTUAL_QTY'];
                            $newUnitCd      = $row['NEW_UNIT_CD'];
                            $newItemType    = $row['NEW_ITEM_TYPE_CD'];
                            $newAccountNo   = $row['NEW_ACCOUNT_NO'];
                            $newRfiNo       = $row['NEW_RFI_NO'];
                            $newSupplierCd  = $row['NEW_SUPPLIER_CD'];
                            $newSupplierName= $row['NEW_SUPPLIER_NAME'];
                            $newCurrencyCd  = $row['NEW_CURRENCY_CD'];
                            $itemPrice      = $row['ITEM_PRICE'];
                            $newItemPrice   = $row['NEW_ITEM_PRICE'];
                            $newRemarkTransfer= $row['NEW_REMARK'];
                            $newLeadTime    = $row['LEAD_TIME'];
                            $newUnitTime    = $row['UNIT_TIME'];
                            $newAttachmentLoc= $row['ATTACHMENT_LOC'];
                            $newRemark      = $row['REMARK'];
                            $plantAlias     = $row['PLANT_ALIAS'];
                            $invType        = $row['INV_TYPE'];
                            $prChargedBuName= $row['BU_NAME'];
                            $createBy       = $row['CREATE_BY'];
                            $procInChargeName= $row['PROC_IN_CHARGE_NAME'];
                            $remainQty      = $newQty - $actualQty;
							
                            if($newSupplierCd == 'SUP99'){
                                $newSupplierCd = '';
                            }
                            
                            $split_qty = explode('.', $newQty);
                            if($split_qty[1] == 0)
                            {
                                $newQty = number_format($newQty);
                            }
                            
                            $split_actual_qty = explode('.', $actualQty);
                            if($split_actual_qty[1] == 0)
                            {
                                $actualQty = number_format($actualQty);
                            }
                            
                            $split_remain_qty = explode('.', $remainQty);
                            if($split_remain_qty[1] == 0)
                            {
                                $remainQty = number_format($remainQty);
                            }
							
							$split_item_price = explode('.', $newItemPrice);
                            if($split_item_price[1] == 0)
                            {
                                $newItemPrice = number_format($newItemPrice);
                            }
                            else
                            {
                                $newItemPrice = number_format($newItemPrice, 2);
                            }
                        }
						/**********************************************************************
                        * UPDATE ITEM PRICE MASTER
                        **********************************************************************/
//                        echo "$newItemCd";
                        if($newItemCd != "99")
                        {
                            
                           /**
                            * SELECT EPS_M_ITEM
                            * Check active flag
                            */
                            $query_select_m_item = "select 
                                                    ITEM_NAME
                                                    ,ACTIVE_FLAG
                                                from
                                                    EPS_M_ITEM
                                                where
                                                    ITEM_CD = '$newItemCd'
                                                    and ITEM_NAME = '$newItemName' ";
                            $sql_select_m_item = $conn->query($query_select_m_item);
                            $row_select_m_item = $sql_select_m_item->fetch(PDO::FETCH_ASSOC);
                            $activeFlag     = $row_select_m_item['ACTIVE_FLAG'];
                            $itemNameCurrent= $row_select_m_item['ITEM_NAME'];
//                            echo $activeFlag;
                            if($activeFlag == 'A')
                            {
                               /**
                                * SELECT EPS_M_ITEM_PRICE
                                */
                                $currentDate= date(Ymd);
                                $query_select_m_item_price = "select     
                                                                EPS_M_ITEM_PRICE.ITEM_CD
                                                                ,EPS_M_ITEM_PRICE.UNIT_CD
                                                                ,EPS_M_ITEM_PRICE.SUPPLIER_CD
                                                                ,EPS_M_ITEM_PRICE.ITEM_PRICE
                                                                ,EPS_M_ITEM_PRICE.EFFECTIVE_DATE_FROM
                                                                ,EPS_M_ITEM_PRICE.CURRENCY_CD
                                                                ,EPS_M_SUPPLIER.SUPPLIER_NAME
                                                                ,EPS_M_ITEM_PRICE.LEAD_TIME
                                                            from         
                                                                EPS_M_ITEM_PRICE 
                                                            inner join
                                                                EPS_M_SUPPLIER 
                                                            on 
                                                                EPS_M_ITEM_PRICE.SUPPLIER_CD = EPS_M_SUPPLIER.SUPPLIER_CD
                                                            where 
                                                                EPS_M_ITEM_PRICE.ITEM_CD = '".$newItemCd."' 
                                                                and EPS_M_ITEM_PRICE.EFFECTIVE_DATE_FROM <= '".$currentDate."'
                                                            order by 
                                                                    EPS_M_ITEM_PRICE.EFFECTIVE_DATE_FROM desc ";
//                                echo "$query_select_m_item_price";
                                $sql_select_m_item_price = $conn->query($query_select_m_item_price);
                                $row_select_m_item_price = $sql_select_m_item_price->fetch(PDO::FETCH_ASSOC);
                                $newItemCd          = $row_select_m_item_price['ITEM_CD'];
                                $newItemName        = htmlspecialchars($itemNameCurrent);
                                $newUnitCd          = $row_select_m_item_price['UNIT_CD'];
                                $newSupplierCd      = $row_select_m_item_price['SUPPLIER_CD'];
                                $newSupplierName    = $row_select_m_item_price['SUPPLIER_NAME'];
                                $newItemPrice       = $row_select_m_item_price['ITEM_PRICE'];
                                $newCurrencyCd      = $row_select_m_item_price['CURRENCY_CD'];
                                $leadTime           = $row_select_m_item_price['LEAD_TIME'];

								$deliveryDateMst    = strtotime("+".$leadTime." day", time());
                                $newDeliveryDate_2  = date('d/m/Y', $deliveryDateMst);
                                if(strtotime(str_replace('/', '-', $newDeliveryDate)) < strtotime(str_replace('/', '-', $newDeliveryDate_2)))
                                {
                                    $newDeliveryDate = $newDeliveryDate_2;
                                }
                                
                                $split_item_price = explode('.', $newItemPrice);
                                if($split_item_price[1] == 0)
                                {
                                    $newItemPrice = number_format($newItemPrice);
                                }
                                else
                                {
                                    $newItemPrice = number_format($newItemPrice, 2);
                                }
                            }
                            else
                            {
                               
                                $newItemCd = "99";
                            }
//                            echo $newItemCd;
                        }
                    }
                    else
                    {
                    ?>
                        <script language="javascript"> document.location="../ecom/WCOM013.php"; </script> 
                    <?php
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
                <script language="javascript"> document.location="../ecom/WCOM012.php"; </script> 
            <?php
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
        <script src="../js/epo/WEPO011.js"></script>
        <script>
            maximize();
        </script>
        <title>EPS</title>
        <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
            <script src="../js/html5.js"></script>
        <![endif]-->
        <script>
            $(function() {
                var dateToday = new Date();
                $("#deliveryDate").datepicker({
                    dateFormat: 'dd/mm/yy',
                    defaultDate: "+1w",
                    minDate: dateToday,
                    maxDate: '+2Y',
                    autoClose: true,
                    beforeShowDay: noWeekendsOrHolidays
                });
            });
        </script>
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
                    <a href="WEPO001_.php">
                        <i class="icon-list-alt"></i><span>PR Waiting</span>  
                    </a> 
                </li>
                <li>
                    <a href="WEPO012.php">
                        <i class="icon-credit-card "></i><span>PR Accepted</span> 
                    </a> 
                </li>
                <!--<li class="dropdown">
                    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown"> 
                        <i class="icon-long-arrow-down"></i><span>PR to PO</span> <b class="caret"></b>
                    </a> 
                    <ul class="dropdown-menu">
                        <li><a href="WEPO003.php">Generate PO Number</a></li>
                        <li><a href="WEPO004.php">Outstanding PO</a></li>
                    </ul>
                </li>-->
                <li class="active">
                    <a href="WEPO004.php">
                        <i class="icon-bookmark"></i><span>Outstanding PO</span> 
                    </a> 
                </li>
                <li>
                    <a href="WEPO003.php">
                        <i class="icon-tags"></i><span>Generate PO</span> 
                    </a> 
                </li>
                <li>
                    <a href="WEPO005.php">
                        <i class="icon-list-ul"></i><span>PO List</span> 
                    </a>
                </li>
                <li>
                    <a href="WEPO018.php">
                        <i class="icon-th"></i><span>PO Waiting</span> 
                    </a> 
                </li>
                <li>
                    <a href="WEPO013.php">
                        <i class="icon-copy"></i><span>PO Sent</span> 
                    </a>
                </li>
                <li>
                    <a href="WEPO090.php">
                        <i class="icon-search"></i><span>PO Search</span> 
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
                    
                    <!---------------------------------- Form --------------------------------->
                    <!--<form> -->  
                        <!---------------------------------- Hidden Parameter --------------------------------->
                        <input type="hidden" id="prNoHidden" />
                        <input type="hidden" value="<?php echo $updateDateSession;?>" id="updateDate" readonly />
                        
                        <!---------------------------------- PR Requester --------------------------------->
                        <div class="widget ">
                            <div class="widget-header">
                                <i class="icon-user"></i>
                                <h3>Requester Information</h3>
                            </div>
                            <div class="widget-content">
                                <div class="control-group">	
                                    <table class="table-non-bordered">
                                        <tr>
                                            <td>
                                                <label class="control-label" for="requesterName">Name: </label>
                                                <div class="controls">
                                                    <input type="text" class="span5" value="<?php echo $requesterName;?>" readonly />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="buCd">BU Code: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" value="<?php echo $prBuCd;?>" readonly />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="plant">Plant: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" value="<?php echo $plantName;?>" readonly />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="extNo">Nice-net: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" value="<?php echo $extNo;?>" readonly />
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div><!-- /control-group -->
                            </div><!-- /widget-content -->
                        </div><!-- /widget -->
                        
						<!---------------------------------- PR Approver --------------------------------->
                        <div class="widget widget-table action-table">
                            <div class="widget-header"> 
                                <i class="icon-list-ol "></i>
                                <h3>PR Approver</h3>
                            </div>
                            <div class="widget-content">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th rowspan="2">NO</th>
                                            <th rowspan="2">NAME</th>
                                            <th rowspan="2">STATUS</th>
                                            <th colspan="2">DATE</th>
                                            <th rowspan="2" width="280px">REMARK</th>
                                        </tr>
                                        <tr>
                                            <th width="130px">APPROVAL</td>
                                            <th width="130px">BYPASS</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
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
                                                    ,EPS_T_PR_APPROVER.DATE_OF_BYPASS
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
                                            $approverName       = stripslashes($row['APPROVER_NAME']);
                                            $approvalStatus     = $row['APPROVAL_STATUS'];
                                            $approvalStatusName = $row['APPROVAL_STATUS_NAME'];
                                            $approvalDate       = $row['APPROVAL_DATE'];
                                            $approvalRemark     = $row['APPROVAL_REMARK'];
                                            $dateByPass         = $row['DATE_OF_BYPASS'];
                                            $specialType        = $row['SPECIAL_TYPE_ID'];
                                            if(strlen(trim($approvalDate)) != 0){
                                                date_default_timezone_set('Asia/Jakarta');
                                                $approvalDate   = date("d/m/Y H:i:s A", strtotime($approvalDate));
                                            }
                                            if(strlen(trim($dateByPass)) != 0){
                                                date_default_timezone_set('Asia/Jakarta');
                                                if(strlen($dateByPass) == 22){
                                                    $newMonth = substr($dateByPass,0,2);
                                                    $newDate = substr($dateByPass,3,2).'/';
                                                    $newYear = substr($dateByPass,5);
                                                }
                                                if(strlen($dateByPass) == 21){
                                                    if(substr($dateByPass,0,1) != 0){
                                                        $newMonth = '0'.substr($dateByPass,0,1).'/';
                                                    }
                                                    $newDate = substr($dateByPass,2,2).'/';
                                                    $newYear = substr($dateByPass,5);
                                                }
                                                if(strlen($dateByPass) == 20){
                                                    if(substr($dateByPass,0,1) != 0){
                                                        $newMonth = '0'.substr($dateByPass,0,1).'/';
                                                    }
                                                    $newDate = '0'.substr($dateByPass,2,1).'/';
                                                    $newYear = substr($dateByPass,4);
                                                }   
                                                
                                                $dateByPass = $newDate.$newMonth.$newYear;
                                            }
                                    ?>
                                        <tr>
                                            <td class="td-number"><?php echo $approverNo;?>.</td>
                                            <td><?php echo $approverName;?></td>
                                            <td><?php echo $approvalStatusName;?></td>
                                            <td><?php echo $approvalDate;?></td>
                                            <td><?php echo $dateByPass;?></td>
                                            <td><?php echo $approvalRemark;?></td>
                                        </tr>
                                    <?php
                                        }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
						
						<!---------------------------------- PR Attachment --------------------------------->
                        <?php
                            $query_select_t_pr_attachment = "select
                                                                ITEM_CD
                                                                ,ITEM_NAME
                                                                ,FILE_NAME
                                                                ,FILE_TYPE
                                                                ,FILE_SIZE
                                                             from
                                                                EPS_T_PR_ATTACHMENT
                                                             where
                                                                PR_NO = '$prNo'";
                            $sql_select_t_pr_attachment = $conn->query($query_select_t_pr_attachment);
                            $row = $sql_select_t_pr_attachment->fetch(PDO::FETCH_ASSOC);
                            if($row){
                                
                        ?>
                        <div class="widget widget-table action-table">
                            <div class="widget-header"> 
                                <i class="icon-paper-clip "></i>
                                <h3>PR Attachment</h3>
                            </div>
                            <div class="widget-content">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th rowspan="2">NO</th>
                                            <th colspan="2">ITEM </th>
                                            <th colspan="3">FILE</th>
                                        </tr>
                                        <tr>
                                            <th>CODE</td>
                                            <th>NAME</td>
                                            <th>NAME</td>
                                            <th>SIZE</td>
                                            <th>TYPE</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $itemNo2 = 1;
                                        $sql_select_t_pr_attachment2 = $conn->query($query_select_t_pr_attachment);
                                        while($row2 = $sql_select_t_pr_attachment2->fetch(PDO::FETCH_ASSOC))
                                        {
                                            $itemCd2    = $row2['ITEM_CD'];
                                            $itemName2  = $row2['ITEM_NAME'];
                                            $fileName2  = $row2['FILE_NAME'];
                                            $fileType2  = $row2['FILE_TYPE'];
                                            $fileSize2  = $row2['FILE_SIZE'];
                                        ?>
                                        <tr>
                                            <td class="td-number"><?php echo $itemNo2;?>.</td>
                                            <td><?php echo $itemCd2;?></td>
                                            <td><?php echo $itemName2;?></td>
                                            <td>
                                                <a href="../db/Attachment/Fixed/<?php echo $prNo."/".$fileName2;?>" target="_blank"><?php echo $fileName2;?></a>
                                            </td>
                                            <td><?php echo $fileSize2;?></td>
                                            <td><?php echo $fileType2;?></td>
                                        </tr>
                                        <?php
                                            $itemNo2++;
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php        
                            }
                        ?>
                        
                        <!---------------------------------- PR Detail --------------------------------->
                        <div class="widget ">
                            <div class="widget-header">
                                <i class="icon-th-large"></i>
                                <h3>Item Information</h3>
                            </div>
                            <div class="widget-content">
                                <div class="control-group">	
                                    <table class="table-non-bordered">
                                        <tr>
                                            <td>
                                                <label class="control-label" for="prNo">PR No: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" value="<?php echo $prNo;?>" id="prNo" readonly />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="prDate">Accepted Date: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" value="<?php echo $procAcceptDate;?>" readonly />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="prPlant">Plant: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" value="<?php echo $plantAlias;?>" readonly />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="prCharged">Charged BU: </label>
                                                <div class="controls">
                                                    <input type="text" class="span4" value="<?php echo $newPrCharged.' - '. $prChargedBuName;?>" readonly /> 
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="control-label" for="deliveryDate">Due Date: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" value="<?php echo $newDeliveryDate;?>" id="deliveryDate" readonly />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="um">UM: </label>
                                                <div class="controls">
                                                    <select id="um" class="span2" >
                                                    <?php
                                                        $query = "select 
                                                                    UNIT_CD
                                                                    ,UNIT_NAME 
                                                                from 
                                                                    EPS_M_UNIT_MEASURE";
                                                        $sql = $conn->query($query);
                                                        while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                                                            $unitCd   = $row['UNIT_CD'];
                                                    ?>
                                                        <option value="<?php echo $unitCd;?>" <?php if($unitCd == $newUnitCd) echo "selected"; ?>><?php echo $unitCd;?></option>
                                                    <?php         
                                                        }
                                                    ?>
                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="itemType">Type: </label>
                                                <div class="controls">
                                                    <select id="itemType" class="span2">
                                                    <?php
                                                        $whereItemTypeMaster = array(); 
                                                        $query = "select 
                                                                        ITEM_TYPE_CD
                                                                        ,ITEM_TYPE_NAME
                                                                        ,ITEM_TYPE_ALIAS
                                                                    from 
                                                                        EPS_M_ITEM_TYPE ";
                                                        if($invType == 'N1000' && $prIssuer == '4420 ')
                                                        {
                                                            $whereItemTypeMaster[] = "ITEM_TYPE_CD = '1'
                                                                                    or ITEM_TYPE_CD = '2'
                                                                                    or ITEM_TYPE_CD = '5'";
                                                        }
                                                        else if($invType == 'N1000')
                                                        {
                                                            $whereItemTypeMaster[] = "ITEM_TYPE_CD = '1'
                                                                                        or ITEM_TYPE_CD = '2'
                                                                                        or ITEM_TYPE_CD = '3'";
                                                        }
                                                        else if($invType == 'N1001')
                                                        {
                                                            $whereItemTypeMaster[] = "ITEM_TYPE_CD = '1'
                                                                                        or ITEM_TYPE_CD = '2'
                                                                                        or ITEM_TYPE_CD = '4'";
                                                        } 
                                                        else
                                                        {
                                                            $whereItemTypeMaster[] = "ITEM_TYPE_CD = '1'
                                                                                        or ITEM_TYPE_CD = '2'";
                                                        }
                                                        if(count($whereItemTypeMaster)) {
                                                            $query .= "where " . implode('or ', $whereItemTypeMaster);
                                                        }
                                                        $query .= "order by ITEM_TYPE_CD";
                                                        $sql = $conn->query($query);
                                                        while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                                                            $itemType       = $row['ITEM_TYPE_CD'];
                                                            $itemTypeAlias  = $row['ITEM_TYPE_ALIAS'];
                                                    ?>
                                                        <option value="<?php echo $itemType;?>" <?php if($itemType == $newItemType) echo "selected"; ?> ><?php echo $itemTypeAlias;?></option>
                                                    <?php         
                                                        }
                                                    ?>
                                                    </select>
                                                </div>
                                            </td>
                                            <td id="td-exp-no" style="display: none">
                                                <label class="control-label" for="expNo">Expense No: </label>
                                                <div class="controls">
                                                    <select id="expNo" class="span4">
                                                        <option value=""></option>
                                                        <?php
                                                            $query = "select 
                                                                        convert(int, ACCOUNT_NO) as ACCOUNT_NO
                                                                        ,ACCOUNT_CD
                                                                        ,ACCOUNT_NAME
                                                                    from 
                                                                        EPS_M_ACCOUNT
                                                                    where
                                                                        ITEM_TYPE_CD = '1'
                                                                    order by 
                                                                        ACCOUNT_NO";
                                                            $sql = $conn->query($query);
                                                            while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                                                                $accountNo   = $row['ACCOUNT_NO'];
                                                                $accountCd   = $row['ACCOUNT_CD'];
                                                                $accountName = strtoupper($row['ACCOUNT_NAME']);
                                                        ?>
                                                        <option value="<?php echo $accountNo;?>" <?php if($accountNo == $newAccountNo) echo "selected"; ?>><?php echo $accountNo.'-'.$accountCd.'-'.$accountName;?></option>
                                                        <?php         
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </td>
                                            <td id="td-inv-no" style="display: none">
                                                <label class="control-label" for="invNo">Inventory No (**PTIC): </label>
                                                <div class="controls">
                                                    <select id="invNo" class="span4">
                                                        <option value=""></option>
                                                        <?php
                                                            $query = "select 
                                                                        CONVERT(int, ACCOUNT_NO) as ACCOUNT_NO
                                                                        ,ACCOUNT_CD
                                                                        ,ACCOUNT_NAME
                                                                    from 
                                                                        EPS_M_ACCOUNT ";
                                                            if($invType == 'N1000' && $prIssuer == '4420 ')
                                                            {
                                                                $query .="
                                                                        where
                                                                            ITEM_TYPE_CD = '5'";    
                                                            }
                                                            else
                                                            {
                                                                $query .="
                                                                        where
                                                                            ITEM_TYPE_CD = '3'";
                                                            }
                                                            $query .="
                                                                    order by 
                                                                        ACCOUNT_NO";
                                                            $sql = $conn->query($query);
                                                            while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                                                                $accountNo   = $row['ACCOUNT_NO'];
                                                                $accountCd   = $row['ACCOUNT_CD'];
                                                                $accountName = strtoupper($row['ACCOUNT_NAME']);
                                                        ?>
                                                        <option value="<?php echo $accountNo;?>" <?php if($accountNo == $newAccountNo) echo "selected"; ?>><?php echo $accountNo.'-'.$accountCd.'-'.$accountName;?></option>
                                                        <?php         
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </td>
                                            <td id="td-invs-no" style="display: none">
                                                <label class="control-label" for="invsNo">Inventory No (**DSIA): </label>
                                                <div class="controls">
                                                    <select id="invsNo" class="span4">
                                                        <option value=""></option>
                                                        <?php
                                                            $query = "select 
                                                                        CONVERT(int, ACCOUNT_NO) as ACCOUNT_NO
                                                                        ,ACCOUNT_CD
                                                                        ,ACCOUNT_NAME
                                                                    from 
                                                                        EPS_M_ACCOUNT
                                                                    where
                                                                        ITEM_TYPE_CD = '4'
                                                                    order by 
                                                                        ACCOUNT_NO";
                                                            $sql = $conn->query($query);
                                                            while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                                                                $accountNo   = $row['ACCOUNT_NO'];
                                                                $accountCd   = $row['ACCOUNT_CD'];
                                                                $accountName = strtoupper($row['ACCOUNT_NAME']);
                                                        ?>
                                                        <option value="<?php echo $accountNo;?>" <?php if($accountNo == $newAccountNo) echo "selected"; ?>><?php echo $accountNo.'-'.$accountCd.'-'.$accountName;?></option>
                                                        <?php         
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </td>
                                            <td id="td-rfi-no" style="display: none">
                                                <label class="control-label" for="rfiNo">RFI No: </label>
                                                <div class="controls">
                                                    <input type="text" id="rfiNo" class="span2" value="<?php echo $newRfiNo;?>" maxlength="6" />
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="control-label" for="qty">Qty: </label>
                                                <div class="controls">
                                                <?php
                                                if($itemStatus == '1120')
                                                {
                                                ?>
                                                <input type="text" id="qty" class="span2 input-align-right" value="<?php echo $newQty;?>"  maxlength="5" /> 
                                                <?php  
                                                }
                                                else
                                                {
                                                ?>
                                                <input type="text" id="qty" class="span2 input-align-right" value="<?php echo $newQty;?>"  maxlength="5" readonly /> 
                                                <?php    
                                                }
                                                ?> 
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="actualQty">Actual Qty: </label>
                                                <div class="controls">
                                                    <input type="text" id="actualQty" class="span2 input-align-right" value="<?php echo $actualQty;?>"  maxlength="5" readonly /> 
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="remainQty">Remain Qty: </label>
                                                <div class="controls">
                                                    <input type="text" id="remainQty" class="span2 input-align-right" value="<?php echo $remainQty;?>"  maxlength="5" readonly /> 
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="itemStatus">Status: </label>
                                                <div class="controls">
                                                    <select class="span4" id="itemStatus">
                                                    <?php
                                                        if($itemStatus == '1160')
                                                        {
                                                            $query2 = "select 
                                                                    APP_STATUS_CD 
                                                                    ,APP_STATUS_NAME
                                                                    ,APP_STATUS_ALIAS
                                                                from 
                                                                    EPS_M_APP_STATUS 
                                                                where
                                                                    APP_STATUS_CD = '1150'
                                                                    or APP_STATUS_CD = '1160'
                                                                    or APP_STATUS_CD = '1170'";
                                                        }
                                                        else
                                                        {
                                                            $query2 = "select 
                                                                    APP_STATUS_CD 
                                                                    ,APP_STATUS_NAME
                                                                    ,APP_STATUS_ALIAS
                                                                from 
                                                                    EPS_M_APP_STATUS 
                                                                where
                                                                    APP_STATUS_CD = '1120'
                                                                    or APP_STATUS_CD = '1130'
                                                                    or APP_STATUS_CD = '1150'";
                                                        }
                                                        $sql2 = $conn->query($query2);
                                                        while($row2 = $sql2->fetch(PDO::FETCH_ASSOC)){
                                                            $appStsCd   = $row2['APP_STATUS_CD'];
                                                            $appStsName = strtoupper($row2['APP_STATUS_NAME']);
                                                            $appStsAlias= $row2['APP_STATUS_ALIAS'];
                                                    ?>
                                                        <option value="<?php echo $appStsCd;?>" <?php if($appStsCd == $itemStatus) echo "selected"; ?> ><?php echo $appStsAlias.'-'.$appStsName;?></option>
                                                    <?php         
                                                        }
                                                    ?>                 
                                                    </select>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="control-label" for="itemCd">Item Code: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" value="<?php echo $newItemCd;?>" id="itemCd" maxlength="5" readonly />
                                                </div>
                                            </td>
                                            <td colspan="3">
                                                <label class="control-label" for="itemName">Item Name: </label>
                                                <div class="controls">
                                                    <input type="text" class="full-width-input" value="<?php echo $newItemName;?>" id="itemName" />
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="control-label" for="prItemPrice">Item Price: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2 input-align-right" style="width:125px;" value="<?php echo number_format($itemPrice);?>" id="prItemPrice" readonly />
                                                </div>
                                            </td>
                                            <td colspan="3">
                                                <label class="control-label" for="itemRemark">Item Remark: </label>
                                                <div class="controls">
                                                    <input type="text" class="full-width-input" value="<?php echo $newRemarkTransfer;?>" id="itemRemark" readonly />
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="control-label" for="transferId">Transfer ID: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" style="width:125px;" value="<?php echo $transferId;?>" id="transferId" readonly />
                                                </div>
                                            </td>
                                            <td colspan="3">
                                                <label class="control-label" for="itemRemark">Purpose: </label>
                                                <div class="controls">
                                                    <input type="text" class="full-width-input" value="<?php echo $purpose;?>" id="prPurpose" readonly />
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!---------------------------------- Supplier --------------------------------->
                        <div class="widget ">
                            <div class="widget-header">
                                <i class="icon-user"></i>
                                <h3>Supplier Information</h3>
                            </div>
                            <div class="widget-content">
                                <div class="control-group">	
                                    <table class="table-non-bordered">
                                        <tr>
                                            <td>
                                                <label class="control-label" for="supplierCdSet">Code: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" id="supplierCdSet" value="<?php echo $newSupplierCd;?>" readonly />
                                                </div>	
                                            </td>
                                            <td>
                                                <label class="control-label" for="supplierNameSet">Name: </label>
                                                <div class="controls">
                                                    <input type="text" class="span6" id="supplierNameSet" value="<?php echo $newSupplierName;?>" readonly />
                                                </div>	
                                            </td>
                                            <td>
                                                <label class="control-label" for="currencyCdSet">Currency: </label>
                                                <div class="controls">
                                                    <input type="text" class="span1" id="currencyCdSet" value="<?php echo $newCurrencyCd;?>" readonly />
                                                </div>	
                                            </td>
                                            <td>
                                                <label class="control-label" for="priceSet">Price: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2 input-align-right" id="priceSet" value="<?php echo $newItemPrice;?>" readonly />
                                                </div>	
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="control-label" for="leadTimeSet">Lead Time: </label>
                                                <div class="controls">
                                                    <input type="text" class="span1" id="leadTimeSet" value="<?php echo $newLeadTime;?>" readonly />
                                                    <input type="text" class="span1" id="unitTimeSet" value="<?php echo $newUnitTime;?>" readonly />
                                                </div>	
                                            </td>
                                            <td>
                                                <label class="control-label" for="remarkSet">Remark: </label>
                                                <div class="controls">
                                                    <input type="text" class="full-width-input" id="remarkSet" value="<?php echo $newRemark;?>" readonly />
                                                </div>
                                            </td>
                                            <td colspan="2">
                                                <label class="control-label" for="attachmentLocSet">Attachment: </label>
                                                <div class="controls">
                                                    <input type="text" class="span3" id="attachmentLocSet" value="<?php echo $newAttachmentLoc;?>" readonly />
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!---------------------------------- Supplier List --------------------------------->
                        <div class="widget widget-table action-table">
                            <div class="widget-header"> 
                                <i class="icon-th-list"></i>
                                <h3>Reference Supplier List</h3>
                            </div>
                            <div class="widget-content">
                                <table class="table table-striped table-bordered" id="supplierListTable">
                                    <thead>
                                        <tr>
                                            <th class="td-actions" colspan="11" style="text-align: left">
                                                <a href="#" class="btn btn-small btn-warning" id="window-add">
                                                    <i class="btn-icon-only icon-plus"> </i>
                                                </a>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th rowspan="2">NO</th>
                                            <th rowspan="2" class="td-actions-3columns">ACTION</th>
                                            <th rowspan="2" style="display: none">TRANSFER ID</th>
                                            <th colspan="3">SUPPLIER</th>
                                            <th rowspan="2">PRICE</th>
                                            <th colspan="2" rowspan="2">LEAD TIME</th>
                                            <th rowspan="2">ATTACHMENT</th>
                                            <th rowspan="2">ATTACHMENT CIP</th>
                                            <th rowspan="2">REMARK</th>
                                            <th rowspan="2" style="display: none">SEQ SUPPLIER</th>
                                        </tr>
                                        <tr>
                                            <th>CODE</th>
                                            <th>NAME</th>
                                            <th>CUR</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $itemNo = 0;
                                    if(strlen($_SESSION['transferSupplier']) > 0){
                                        foreach ($_SESSION['transferSupplier'] as $transferSupplier ) 
                                        { 
                                            $transferIdSession      = $transferSupplier['transferId'];
                                            $supplierCdSession      = $transferSupplier['supplierCd']; 
                                            $supplierNameSession    = $transferSupplier['supplierName'];  
                                            $currencyCdSession      = $transferSupplier['currencyCd'];
                                            $itemPriceSession       = $transferSupplier['itemPrice'];
                                            $leadTimeSession        = $transferSupplier['leadTime'];  
                                            $unitTimeSession        = $transferSupplier['unitTime']; 
                                            $attachmentLocSession   = $transferSupplier['attachmentLoc']; 
                                            $remarkSession          = $transferSupplier['remark'];
                                            $seqSupplierSession     = $transferSupplier['seqSupplier'];
                                            $attachmentCipSession     = $transferSupplier['attachmentCip'];
                                            $itemNo++;
											
											$attachmentYearVal  = substr($attachmentLocSession,0,4);
                                            $attachmentMonthVal = substr($attachmentLocSession,5,2);
                                            $attachmentFileVal  = substr($attachmentLocSession,8);
                                    ?>
                                        <tr id="<?php echo $seqSupplierSession;?>">
                                            <td class="td-number">
                                                <?php echo $itemNo; ?>.
                                            </td>
                                            <td class="td-actions-3columns">
                                                <a href="#" class="btn btn-small btn-success" id="window-edit">
                                                    <i class="btn-icon-only icon-edit"> </i>
                                                </a>
                                                <a href="#" class="btn btn-small btn-danger" id="window-delete">
                                                    <i class="btn-icon-only icon-remove"> </i>
                                                </a>
                                                <a href="#" class="btn btn-small btn-info" id="window-set">
                                                    <i class="btn-icon-only icon-ok"> </i>
                                                </a>
                                            </td>
                                            <td style="display: none">
                                                <?php echo $transferIdSession; ?>
                                            </td>
                                            <td id="getSupplierCd<?php echo $seqSupplierSession;?>">
                                                <?php echo $supplierCdSession; ?>
                                            </td>
                                            <td id="getSupplierName<?php echo $seqSupplierSession;?>">
                                                <?php echo $supplierNameSession; ?>
                                            </td>
                                            <td id="getCurrencyCd<?php echo $seqSupplierSession;?>" class="span1">
                                                <?php echo $currencyCdSession; ?>
                                            </td>
                                            <td id="getPrice<?php echo $seqSupplierSession;?>" class="td-align-right">
                                            <?php 
                                                $split = explode('.', $itemPriceSession);
                                                if($split[1] == 0)
                                                {
                                                    echo number_format($itemPriceSession);
                                                }
                                                else
                                                {
                                                    echo number_format($itemPriceSession, 2);
                                                }
                                            ?>
                                            </td>
                                            <td id="getLeadTime<?php echo $seqSupplierSession;?>"  class="td-align-right">
                                                <?php echo $leadTimeSession; ?>
                                            </td>
                                            <td id="getUnitTime<?php echo $seqSupplierSession;?>" >
                                                <?php echo $unitTimeSession; ?>
                                            </td>
                                            <td id="getAttachmentLoc<?php echo $seqSupplierSession;?>" >
                                                <a href="file://///10.82.101.2/EPS/Quotation/<?php echo $supplierCdSession."/".$attachmentYearVal."/".$attachmentMonthVal."/".$attachmentFileVal;?>" target="_blank">
                                                    <?php echo $attachmentLocSession; ?>
                                                </a>
<!--                                                <a href="file://///10.82.101.31/tacifss02/TACI General Database/C000 - Supporting/C020 - Indirect/C020-T4100 - Procurement/PROCUREMENT/EPS/Quotation/<?php echo $supplierCdSession."/".$attachmentYearVal."/".$attachmentMonthVal."/".$attachmentFileVal;?>" target="_blank">
                                                    <?php echo $attachmentLocSession; ?>
                                                </a>-->
                                            </td>
                                            <td id="getAttachmentCip<?php echo $seqSupplierSession;?>" >
<!--                                                <a href="file://///10.82.101.2/EPS/Rfi/<?php echo $attachmentCipSession;?>" target="_blank">
                                                    <?php echo $attachmentCipSession; ?>
                                                </a>-->
                                                <a href="file://///10.82.101.31/tacifss02/TACI General Database/C000 - Supporting/C020 - Indirect/C020-T4100 - Procurement/PROCUREMENT/EPS/Rfi/<?php echo $attachmentCipSession;?>" target="_blank">
                                                    <?php echo $attachmentCipSession; ?>
                                                </a>
                                            </td>
                                            <td id="getRemark<?php echo $seqSupplierSession;?>" >
                                                <?php echo $remarkSession; ?>
                                            </td>
                                            <td style="display: none">
                                                <?php echo $seqSupplierSession; ?>
                                            </td>
                                        </tr>
                                    <?php
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div><!-- widget widget-table action-table -->
						
						<!---------------------------------- Proc Information --------------------------------->
                        <div class="widget ">
                            <div class="widget-header">
                                <i class="icon-info-sign"></i>
                                <h3>Procurement Information</h3>
                            </div>
                            <div class="widget-content">
                                <div class="control-group">	
                                    <table class="table-non-bordered">
										<tr>
                                            <?php
                                            if($sRoleId == "ROLE_03" || $sRoleId == "ROLE_06")
                                            {
                                            ?>
                                            <td>
                                                <label class="control-label" for="procInCharged">Proc. In Charge: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" value="<?php echo $procInChargeName;?>" readonly />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="newProcInCharged">New Proc. In Charge: </label>
                                                <div class="controls">
                                                    <select id="newProcInCharged" class="span4">
                                                    <?php
                                                        $query_m_proc_approver = "select distinct 
                                                                                    EPS_M_PR_PROC_APPROVER.NPK
                                                                                    ,EPS_M_EMPLOYEE.NAMA1
                                                                                from 
                                                                                    EPS_M_PR_PROC_APPROVER
                                                                                left join
                                                                                    EPS_M_EMPLOYEE
                                                                                on
                                                                                    EPS_M_PR_PROC_APPROVER.NPK =  EPS_M_EMPLOYEE.NPK 
                                                                                where     
                                                                                    (EPS_M_PR_PROC_APPROVER.NPK <> '')
                                                                                    and (EPS_M_PR_PROC_APPROVER.NPK <> '$createBy') ";
                                                        $sql_m_proc_approver = $conn->query($query_m_proc_approver);
                                                        while($row_m_proc_approver = $sql_m_proc_approver->fetch(PDO::FETCH_ASSOC)){
                                                            $npk        = $row_m_proc_approver['NPK'];
                                                            $procName   = $row_m_proc_approver['NAMA1'];
                                                    ?>
                                                        <option value="<?php echo $npk;?>"><?php echo $procName;?></option>
                                                    <?php         
                                                        }
                                                    ?>
                                                    </select>
                                                </div>
                                            </td>
                                            <?php
                                            }
                                            ?>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <label class="control-label" for="remarkProc">Comment: </label>
                                                <div class="controls">
                                                    <input type="text" class="full-width-input" id="remarkProc" maxlength="200" />
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
									
									<!---------------------------------- Message --------------------------------->
                                    <div class="alert" id="mandatory-msg-1" style="display: none">
                                        <strong>Mandatory!</strong> Please fill Item Name.
                                    </div>
                                    <div class="alert" id="mandatory-msg-2" style="display: none">
                                        <strong>Mandatory!</strong> Please fill Expense No for Expense type.
                                    </div>
                                    <div class="alert" id="mandatory-msg-3" style="display: none">
                                        <strong>Mandatory!</strong> Please fill RFI No for RFI type.
                                    </div>
                                    <div class="alert" id="mandatory-msg-4" style="display: none">
                                        <strong>Mandatory!</strong> Please fill Inventory No (**PTIC) for Inventory type.
                                    </div>
                                    <div class="alert" id="mandatory-msg-5" style="display: none">
                                        <strong>Mandatory!</strong> Please fill Inventory No (**DSIA) for Inventory type.
                                    </div>
                                    <div class="alert" id="mandatory-msg-6" style="display: none">
                                        <strong>Mandatory!</strong> Please specify due date > current date.
                                    </div>
                                    <div class="alert" id="mandatory-msg-7" style="display: none">
                                        <strong>Mandatory!</strong> Please input "Comment" because item canceled.
                                    </div>
                                    <div class="alert" id="mandatory-msg-8" style="display: none">
                                        <strong>Range Error!</strong> Please input value > 0.
                                    </div>
                                    <div class="alert" id="mandatory-msg-9" style="display: none">
                                        <strong>Range Error!</strong> Please input remain qty value <= <?php echo $remainQty;?>.
                                    </div>
                                    <div class="alert" id="mandatory-msg-10" style="display: none">
                                        <strong>Mandatory!</strong> Reference supplier list empty. Please input supplier at least one supplier for "Waiting Generate PO" status because item code 99.
                                    </div>
                                    <div class="alert" id="mandatory-msg-11" style="display: none">
                                        <strong>Mandatory!</strong> Please set Supplier Information for "Waiting Generate PO".
                                    </div>
                                    <div class="alert" id="mandatory-msg-12" style="display: none">
                                        <strong>Existence Error!</strong> Selected supplier does not exist. Please update Reference Supplier List.
                                    </div>
                                    <div class="alert" id="mandatory-msg-13" style="display: none">
                                        <strong>Digit Error!</strong> Selected price does not match. Please update Reference Supplier List in Price column.
                                    </div>
                                    <div class="alert" id="mandatory-msg-14" style="display: none">
                                        <strong>Range Error!</strong> Maximum reference supplier list is 8.
                                    </div>
                                    <div class="alert" id="mandatory-msg-15" style="display: none">
                                        <strong>Format Error!</strong> Please check RFI No format (** xx-xxx for TACI).
                                    </div>
                                    <div class="alert" id="mandatory-msg-16" style="display: none">
                                        <strong>Failure!</strong> Data already updated by another user.
                                    </div>
                                    <div class="alert" id="mandatory-msg-17" style="display: none">
                                        <strong>Digit Error!</strong> Please input price less than 20% from item price in PR.
                                    </div>
                                    <div class="alert" id="undefined-msg" style="display: none">
                                        <strong>Undefined Error!</strong> System Error occurs. Please report to system administrator.
                                    </div>
                                    <div class="alert alert-success" id="success-msg" style="display: none">
                                        <strong>Success!</strong> Save item finished.
                                    </div>
									
									<!---------------------------------- Button --------------------------------->
									<div class="form-actions">
                                        <?php
                                            if($sRoleId == "ROLE_03" || $sRoleId == "ROLE_06")
                                            {
                                        ?>
                                            <button class="btn btn-warning" id="btn-update-item">Update</button> 
                                        <?php
                                            }
                                        ?>
										<button class="btn btn-primary" id="btn-save-item">Save Item</button> 
										<button class="btn" id="btn-back">Back</button>
									</div> <!-- /form-actions -->
									
                                </div><!-- /control-group -->
                            </div><!-- /widget-content -->
                        </div><!-- /widget -->
                    <!--</form>-->
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
                    &copy; 2018 PT. TD AUTOMOTIVE COMPRESSOR INDONESIA. All rights reserved. 
                </div> <!-- /span12 -->	
            </div> <!-- /row -->
	</div> <!-- /container -->		
    </div> <!-- /footer-inner -->	
</div> <!-- /footer -->

<div id="dialog-form" title="Add Supplier" style="display: none;">
    <div class="alert" id="dialog-mandatory-msg-1" style="display: none;">
        <strong>Mandatory!</strong> Please fill all the field.
    </div>
    <div class="alert" id="dialog-mandatory-msg-2" style="display: none;">
        <strong>Mandatory!</strong> Please input price > 0.
    </div>
    <div class="alert" id="dialog-mandatory-msg-3" style="display: none;">
        <strong>Mandatory!</strong> Please check action Add or Edit.
    </div>
    <div class="alert" id="dialog-mandatory-msg-4" style="display: none;">
        <strong>Digit Error!</strong> Please input price less than 20% from item price in PR.
    </div>
    <div class="alert" id="dialog-duplicate-msg" style="display: none;">
        <strong>Duplicate!</strong> Supplier already exist in Supplier List.
    </div>
    <div class="alert" id="dialog-undefined-msg" style="display: none">
        <strong>Undefined Error!</strong> System Error occurs. Please report to system administrator.
    </div>
    <div class="widget">
        <form id="WEPO002Form-dialog">
            <div class="widget-content">
                <div class="control-group">
                    <input type="hidden" id="transferIdSupplier" value="<?php echo $transferId;?>" />
                    <input type="hidden" id="seqSupplier" />
                    <table class="table-non-bordered" id="table-supplier-ref">
                        <tr>
                            <td colspan="2">
                                <label class="control-label" for="supplierCd">Supplier: </label>
                                <div class="dialog-controls">
                                    <select id="supplierCd" class="full-width-input">
                                    <?php
                                        $query = "select 
                                                    SUPPLIER_CD 
                                                    ,SUPPLIER_NAME
                                                    ,CURRENCY_CD
                                                from 
                                                    EPS_M_SUPPLIER WHERE ACTIVE_FLAG = 'A'";
                                        $sql = $conn->query($query);
                                        while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                                            $supplierCd     = $row['SUPPLIER_CD'];
                                            $supplierName   = $row['SUPPLIER_NAME'];
                                            $currencyCd     = $row['CURRENCY_CD'];
                                    ?>
                                        <option value="<?php echo $supplierCd;?>"><?php echo $supplierCd.' [ '.$currencyCd.' ]'.' - '.$supplierName;?></option>
                                    <?php         
                                        }
                                    ?>
                                    </select>
                                </div>
                            </td>
                            <td>
                                <label class="control-label" for="currency">Currency: </label>
                                <div class="dialog-controls">
                                    <select id="currencyCd" class="span2" disabled>
                                        <option value=""></option>
                                    <?php
                                        $query = "select 
                                                    CURRENCY_CD
                                                from 
                                                    EPS_M_CURRENCY";
                                        $sql = $conn->query($query);
                                        while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                                            $currencyCd     = $row['CURRENCY_CD'];
                                    ?>
                                        <option value="<?php echo $currencyCd;?>"><?php echo $currencyCd;?></option>
                                    <?php         
                                        }
                                    ?>
                                    </select>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class="control-label" for="price">Price: </label>
                                <div class="dialog-controls">
                                    <input type="text" id="price" class="input-align-right" maxlength="16" />
                                </div>
                            </td>
                            <td>
                                <label class="control-label" for="leadTime">Lead Time: </label>
                                <div class="dialog-controls">
                                    <input type="text" id="leadTime" class="input-align-right span1" maxlength="2" />
                                </div>
                            </td>
                            <td>
                                <label class="control-label" for="unitTime">&nbsp;</label>
                                <div class="dialog-controls">
                                    <select id="unitTime" class="span2">
                                        <option value="D">DAY</option>
                                        <option value="W">WEEK</option>
                                        <option value="M">MONTH</option>
                                        <option value="Y">YEAR</option>
                                    ?>
                                    </select>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <label class="control-label" for="remark">Remark: </label>
                                <div class="dialog-controls">
                                    <input type="text" id="remark" class="full-width-input" maxlength="300" />
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <label class="control-label" for="attachmentLoc">Attachment: </label>
                                <div class="dialog-controls">
                                    <input type="text" id="attachmentLoc" class="full-width-input" maxlength="300" />
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <label class="control-label" for="attachmentCip">Attachment CIP: </label>
                                <div class="dialog-controls">
                                    <input type="text" id="attachmentCip" class="full-width-input" maxlength="300" />
                                </div>
                            </td>
                        </tr>
                    </table>
                </div> 
            </div>
        </form>
    </div>
</div>

<div id="dialog-confirm-session" title="Message" style="display: none;"></div>
<div id="dialog-confirm-logout" title="Confirm" style="display: none;"></div>
<div id="dialog-confirm-save" title="Confirm" style="display: none;"></div>
<div id="dialog-confirm-update" title="Confirm" style="display: none;"></div>
<div id="dialog-confirm-back" title="Confirm" style="display: none;"></div>
    </body>
</html>
