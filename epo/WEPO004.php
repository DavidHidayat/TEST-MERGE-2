<?php session_start(); 
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Constant.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Common.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/CONTROLLER/PAGING.php";
set_time_limit(1800);
ini_set('memory_limit', '512M'); 

if(isset($_SESSION['sUserId']))
{      
    $sUserId            = $_SESSION['sUserId'];
    $sActiveFlag		= $_SESSION['sactiveFlag'];
    $sActiveFlagLogin	= $_SESSION['sactiveFlagLogin'];
    
    if($sUserId != '' && $sActiveFlag == 'A' && $sActiveFlagLogin == 'A')
    {
        /** Unset SESSION */
        unset($_SESSION['prStatus']);
        unset($_SESSION['poStatus']);
        unset($_SESSION['itemStatusSession']);
		
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
        
        if($sRoleId == 'ROLE_02' || $sRoleId == 'ROLE_03' || $sRoleId == 'ROLE_04' || $sRoleId == 'ROLE_06' || $sRoleId == 'ROLE_07' || $sRoleId == 'ROLE_09' || $sRoleId == 'ROLE_10' || $sRoleId == 'ROLE_11')
        {
			$prNoCriteria   = trim($_GET['prNoCriteria']);
			
			if(isset($_GET['mpage']))
            {
                $mpage = trim($_GET['mpage']);
            }
            else
            {
                $mpage = 1;
            }
            $max_per_page   = 50;
            $num            = 5;
                                    
            if($mpage)
            { 
                $start = ($mpage) * $max_per_page; 
            }
            else
            {
                $start  = 50;	
                $mpage  = 1;
            }
                                    
            if($mpage == 1)
            {
                $itemNo = 0;
            }
            else
            {
                $itemNo = ($max_per_page * ($mpage - 1));
            }
                
            $wherePrHeader  = array();
            $wherePrHeader[] = "(EPS_T_TRANSFER.ITEM_STATUS = '".constant('1120')."'
                                    or EPS_T_TRANSFER.ITEM_STATUS = '".constant('1160')."') ";
            if($sRoleId == 'ROLE_02' || $sRoleId == 'ROLE_04' || $sRoleId == 'ROLE_07' || $sRoleId == 'ROLE_09' || $sRoleId == 'ROLE_10' || $sRoleId == 'ROLE_11'){
                $wherePrHeader[] = "EPS_T_TRANSFER.CREATE_BY = '".$sUserId."'";
                if($prNoCriteria)
                {
                    $wherePrHeader[] = "EPS_T_TRANSFER.PR_NO = '".$prNoCriteria."'";
                }
            }
            if($prNoCriteria)
            {
                $wherePrHeader[] = "EPS_T_TRANSFER.PR_NO = '".$prNoCriteria."'";
            }
                                
           /**
            * SELECT COUNT EPS_T_PR_HEADER
            **/
            $query_count_t_pr_header = "select 
                                            count (*) as COUNT_TRANSFER
                                        from
                                            EPS_T_TRANSFER
                                        inner join
                                            EPS_T_PR_HEADER 
                                        on 
                                            EPS_T_TRANSFER.PR_NO = EPS_T_PR_HEADER.PR_NO
                                        left join
                                            EPS_M_BUNIT
                                        on
                                            EPS_T_TRANSFER.NEW_CHARGED_BU = EPS_M_BUNIT.BU_CD
                                        left join
                                            EPS_M_EMPLOYEE 
                                        on 
                                            EPS_T_TRANSFER.CREATE_BY = EPS_M_EMPLOYEE.NPK ";
        
            if(count($wherePrHeader)) {
                $query_count_t_pr_header .= "where " . implode('and ', $wherePrHeader);
            }
            $sql_count_t_pr_header = $conn->query($query_count_t_pr_header);
            $row_count_t_pr_header = $sql_count_t_pr_header->fetch(PDO::FETCH_ASSOC);
            $countTransfer    = $row_count_t_pr_header['COUNT_TRANSFER'];
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
        <script src="../js/epo/WEPO004.js"></script>
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
					<!---------------------------------- Message --------------------------------->
                    <div class="alert" id="mandatory-msg-1" style="display: none">
                        <strong>Mandatory!</strong> Please fill the search criteria.
                    </div> 
                    <div class="widget ">
                        <div class="widget-header">
                            <i class="icon-search"></i>
                            <h3>Search</h3>
                        </div>
                        <div class="widget-content">
                            <form id="WEPO004Form">
                                <div class="control-group">	
                                    <table class="table-non-bordered">
                                        <tr>
                                            <td>
                                                <label class="control-label" for="prNo">PR No: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" id="prNo" name="prNoCriteria" maxlength="10" value="<?php echo $prNoCriteria;?>" />
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </form>
                            <div>
                                <button class="btn btn-primary" id="btn-search" name="btn-search">Search</button> 
                                <button class="btn" id="btn-reset">Reset</button>
                            </div> 
                        </div>
                    </div> 
                    <div class="widget widget-table action-table">
                        <div class="widget-header">
                            <i class="icon-bookmark"></i>
                            <h3>Outstanding PO</h3>
                        </div>
                        <div class="widget-content">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th colspan="15" style="text-align: left">
                                            <!--<a href="../db/REPORT/EXCEL/OUTSTANDING_PO.php" target="_blank" class="btn btn-small btn-linkedin-alt" id="btn-download">
                                                Download
                                                <i class="btn-icon-only icon-download-alt"> </i>
                                            </a>-->
                                            <a href="../db/REPORT/OUTSTANDING_PO.php" target="_blank" class="btn btn-small btn-linkedin-alt" id="btn-download">
                                                Download
                                                <i class="btn-icon-only icon-download-alt"> </i>
                                            </a>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th rowspan="2">NO</th>
                                        <th rowspan="2" style="display: none">ACTION</th>
                                        <th rowspan="2" style="display: none">TRANSFER ID</th>
                                        <th rowspan="2" style="display: none">ITEM NAME</th>
                                        <th rowspan="2">PR NO</th>
                                        <th rowspan="2">REQUESTER</th>
                                        <th rowspan="2">ACCEPTED<br>DATE</th>
                                        <th rowspan="2" style="display: none">PROC.<br>IN CHARGE</th>
                                        <th rowspan="2">DUE<br>DATE</th>
                                        <th colspan="2">ITEM</th>
                                        <th rowspan="2">QTY</th>
                                        <th rowspan="2">UM</th>
                                        <th colspan="2">CHARGED</th>
                                        <th rowspan="2">SUPPLIER<br>REF</th>
                                        <th rowspan="2" style="display: none">DATE DIFF</th>
                                        <th rowspan="2">OUT<br>FLAG</th>
                                        <th rowspan="2">USER<br>ATTACH</th>
                                    </tr>
                                    <tr>
                                        <th>CODE</td>
                                        <th>NAME</td>
                                        <th>BU</td>
                                        <th>PLANT</td>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                /*$wherePrHeader  = array();
                                $itemNo         = 0;
                                
                                $wherePrHeader[] = "(EPS_T_TRANSFER.ITEM_STATUS = '".constant('1120')."'
                                                    or EPS_T_TRANSFER.ITEM_STATUS = '".constant('1160')."') ";
                                if($sRoleId == 'ROLE_02' || $sRoleId == 'ROLE_04' || $sRoleId == 'ROLE_07' || $sRoleId == 'ROLE_09' || $sRoleId == 'ROLE_10' || $sRoleId == 'ROLE_11'){
                                    $wherePrHeader[] = "EPS_T_TRANSFER.CREATE_BY = '".$sUserId."'";
									if($prNoCriteria)
                                    {
                                        $wherePrHeader[] = "EPS_T_TRANSFER.PR_NO = '".$prNoCriteria."'";
                                    }
                                }
                                if($prNoCriteria)
                                {
                                    $wherePrHeader[] = "EPS_T_TRANSFER.PR_NO = '".$prNoCriteria."'";
                                }
                                
                                $query = "select 
                                            EPS_T_TRANSFER.TRANSFER_ID
                                            ,EPS_T_TRANSFER.PR_NO
                                            ,substring(EPS_T_PR_HEADER.ISSUED_DATE,7,2)+'/'+substring(EPS_T_PR_HEADER.ISSUED_DATE,5,2)+'/'+substring(EPS_T_PR_HEADER.ISSUED_DATE,1,4) as ISSUED_DATE
                                            ,convert(VARCHAR(24), EPS_T_PR_HEADER.PROC_ACCEPT_DATE, 103) as PROC_ACCEPT_DATE
                                            ,EPS_T_TRANSFER.NEW_CHARGED_BU
                                            ,EPS_T_TRANSFER.ITEM_STATUS
                                            ,EPS_T_TRANSFER.ITEM_NAME
                                            ,EPS_T_TRANSFER.NEW_SUPPLIER_CD
                                            ,EPS_T_TRANSFER.NEW_SUPPLIER_NAME
                                            ,substring(EPS_T_TRANSFER.NEW_DELIVERY_DATE,7,2)+'/'+substring(EPS_T_TRANSFER.NEW_DELIVERY_DATE,5,2)+'/'+substring(EPS_T_TRANSFER.NEW_DELIVERY_DATE,1,4) as NEW_DELIVERY_DATE
                                            ,EPS_T_TRANSFER.NEW_ITEM_CD
                                            ,EPS_T_TRANSFER.NEW_ITEM_NAME
                                            ,EPS_T_TRANSFER.NEW_QTY
                                            ,EPS_T_TRANSFER.ACTUAL_QTY
                                            ,EPS_T_TRANSFER.NEW_UNIT_CD
                                            ,EPS_T_TRANSFER.NEW_ITEM_PRICE
                                            ,EPS_M_BUNIT.PLANT_ALIAS
                                            ,(select count(*)
                                                from         
                                                    EPS_T_PR_ATTACHMENT
                                                where      
                                                    EPS_T_TRANSFER.PR_NO = EPS_T_PR_ATTACHMENT.PR_NO 
                                                    and replace(replace(replace(EPS_T_TRANSFER.ITEM_NAME, char(13), ''), char(9), ''), ' ', '') = replace(EPS_T_PR_ATTACHMENT.ITEM_NAME, ' ', '') ) 
                                              as ATTACHMENT_ITEM_COUNT
                                            ,(select count(*)
                                                from          
                                                    EPS_T_TRANSFER_SUPPLIER
                                                where      
                                                    EPS_T_TRANSFER.TRANSFER_ID = EPS_T_TRANSFER_SUPPLIER.TRANSFER_ID) 
                                              as TOTAL_SUPPLIER
                                            ,datediff(day, PROC_ACCEPT_DATE, GETDATE()) as COUNT_DATE_DIFF
                                            ,EPS_T_PR_HEADER.PROC_ACCEPT_DATE as PROC_ACCEPT_DATE_2
                                            ,EPS_T_TRANSFER.CREATE_BY
                                            ,EPS_M_EMPLOYEE.NAMA1 as CREATE_BY_NAME
                                            ,EPS_M_EMPLOYEE_2.NAMA1 as REQUESTER_NAME
                                          from
                                            EPS_T_TRANSFER
                                          inner join
                                            EPS_T_PR_HEADER 
                                          on 
                                            EPS_T_TRANSFER.PR_NO = EPS_T_PR_HEADER.PR_NO
                                          left join
                                            EPS_M_BUNIT
                                          on
                                            EPS_T_TRANSFER.NEW_CHARGED_BU = EPS_M_BUNIT.BU_CD
                                          left join
                                            EPS_M_EMPLOYEE 
                                          on 
                                            EPS_T_TRANSFER.CREATE_BY = EPS_M_EMPLOYEE.NPK
                                          left join
                                            EPS_M_EMPLOYEE EPS_M_EMPLOYEE_2
                                          on 
                                            EPS_T_TRANSFER.REQUESTER = EPS_M_EMPLOYEE_2.NPK ";
                                if(count($wherePrHeader)) {
                                    $query .= "where " . implode('and ', $wherePrHeader);
                                }
                                $query .= "order by PROC_ACCEPT_DATE_2 ";
                                $sql = $conn->query($query);
                                while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                                    $transferId     = $row['TRANSFER_ID'];
                                    $prNo           = $row['PR_NO'];
                                    $procAcceptDate = $row['PROC_ACCEPT_DATE'];
                                    $newPrCharged   = $row['NEW_CHARGED_BU'];
                                    $itemStatus     = $row['ITEM_STATUS'];
                                    $itemName       = $row['ITEM_NAME'];
                                    $newSupplierCd  = $row['NEW_SUPPLIER_CD'];
                                    $newSupplierName= $row['NEW_SUPPLIER_NAME'];
                                    $newDeliveryDate= $row['NEW_DELIVERY_DATE'];
                                    $newItemCd      = $row['NEW_ITEM_CD'];
                                    $newItemName    = $row['NEW_ITEM_NAME'];
                                    $newQty         = $row['NEW_QTY'];
                                    $actualQty      = $row['ACTUAL_QTY'];
                                    $newUnitCd      = $row['NEW_UNIT_CD'];
                                    $newItemPrice   = $row['NEW_ITEM_PRICE'];
                                    $attachmentItemCount= $row['ATTACHMENT_ITEM_COUNT'];
                                    $totalSupplier  = $row['TOTAL_SUPPLIER'];
                                    $plantAlias     = $row['PLANT_ALIAS'];
                                    $countDateDiff  = $row['COUNT_DATE_DIFF'];
                                    $createBy       = $row['CREATE_BY'];
                                    $createByName   = $row['CREATE_BY_NAME'];
                                    $requesterName  = $row['REQUESTER_NAME'];
                                    $itemNo++;*/
								if($start > $countTransfer)
                                    {
                                        $lgenap     = $start - $max_per_page;
                                        $max_per_pages = $countTransfer - $lgenap;
                                        $start      = $countTransfer;
                                    }
                                    else
                                    {
                                        $max_per_pages = $max_per_page;
                                    }
                                    
                                    /**
                                     * SELECT EPS_T_PR_HEADER
                                     **/
                                    $query = "select 
                                                    * 
                                                  from 
                                                    (select top  $max_per_pages  
                                                        * 
                                                     from      
                                                        (select top $start 
                                                            EPS_T_TRANSFER.TRANSFER_ID
                                                            ,EPS_T_TRANSFER.PR_NO
                                                            ,substring(EPS_T_PR_HEADER.ISSUED_DATE,7,2)+'/'+substring(EPS_T_PR_HEADER.ISSUED_DATE,5,2)+'/'+substring(EPS_T_PR_HEADER.ISSUED_DATE,1,4) as ISSUED_DATE
                                                            ,convert(VARCHAR(24), EPS_T_PR_HEADER.PROC_ACCEPT_DATE, 103) as PROC_ACCEPT_DATE
                                                            ,EPS_T_TRANSFER.NEW_CHARGED_BU
                                                            ,EPS_T_TRANSFER.ITEM_STATUS
                                                            ,EPS_T_TRANSFER.ITEM_NAME
                                                            ,EPS_T_TRANSFER.NEW_SUPPLIER_CD
                                                            ,EPS_T_TRANSFER.NEW_SUPPLIER_NAME
                                                            ,substring(EPS_T_TRANSFER.NEW_DELIVERY_DATE,7,2)+'/'+substring(EPS_T_TRANSFER.NEW_DELIVERY_DATE,5,2)+'/'+substring(EPS_T_TRANSFER.NEW_DELIVERY_DATE,1,4) as NEW_DELIVERY_DATE
                                                            ,EPS_T_TRANSFER.NEW_ITEM_CD
                                                            ,EPS_T_TRANSFER.NEW_ITEM_NAME
                                                            ,EPS_T_TRANSFER.NEW_QTY
                                                            ,EPS_T_TRANSFER.ACTUAL_QTY
                                                            ,EPS_T_TRANSFER.NEW_UNIT_CD
                                                            ,EPS_T_TRANSFER.NEW_ITEM_PRICE
                                                            ,EPS_M_BUNIT.PLANT_ALIAS
                                                            ,(select count(*)
                                                                from         
                                                                    EPS_T_PR_ATTACHMENT
                                                                where      
                                                                    EPS_T_TRANSFER.PR_NO = EPS_T_PR_ATTACHMENT.PR_NO 
                                                                    and replace(replace(replace(EPS_T_TRANSFER.ITEM_NAME, char(13), ''), char(9), ''), ' ', '') = replace(EPS_T_PR_ATTACHMENT.ITEM_NAME, ' ', '') ) 
                                                            as ATTACHMENT_ITEM_COUNT
                                                            ,(select count(*)
                                                                from          
                                                                    EPS_T_TRANSFER_SUPPLIER
                                                                where      
                                                                    EPS_T_TRANSFER.TRANSFER_ID = EPS_T_TRANSFER_SUPPLIER.TRANSFER_ID) 
                                                            as TOTAL_SUPPLIER
                                                            ,datediff(day, PROC_ACCEPT_DATE, GETDATE()) - (2 * datediff(wk,PROC_ACCEPT_DATE,GETDATE())) as WEEKDAYS
                                                            ,datediff(day, PROC_ACCEPT_DATE, GETDATE()) as COUNT_DATE_DIFF
                                                            ,EPS_T_PR_HEADER.PROC_ACCEPT_DATE as PROC_ACCEPT_DATE_2
                                                            ,EPS_T_TRANSFER.CREATE_BY
                                                            ,EPS_M_EMPLOYEE.NAMA1 as REQUESTER_NAME
                                                        from
                                                            EPS_T_TRANSFER
                                                        inner join
                                                            EPS_T_PR_HEADER 
                                                        on 
                                                            EPS_T_TRANSFER.PR_NO = EPS_T_PR_HEADER.PR_NO
                                                        left join
                                                            EPS_M_BUNIT
                                                        on
                                                            EPS_T_TRANSFER.NEW_CHARGED_BU = EPS_M_BUNIT.BU_CD
                                                        left join
                                                            EPS_M_EMPLOYEE 
                                                        on 
                                                            EPS_T_TRANSFER.REQUESTER = EPS_M_EMPLOYEE.NPK ";
                                if(count($wherePrHeader)) {
                                    $query .= "where " . implode('and ', $wherePrHeader);
                                }
                                $query .= "
                                                            order by 
                                                                TRANSFER_ID
                                                                ,PROC_ACCEPT_DATE_2) as T1
                                                        order by 
                                                            TRANSFER_ID desc
                                                            ,T1.PROC_ACCEPT_DATE_2 desc
                                                    ) as T2
                                                    order by 
                                                        TRANSFER_ID
                                                        ,T2.PROC_ACCEPT_DATE_2 ";
                                $sql = $conn->query($query);
                                while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                                    $transferId     = $row['TRANSFER_ID'];
                                    $prNo           = $row['PR_NO'];
                                    $procAcceptDate = $row['PROC_ACCEPT_DATE'];
                                    $newPrCharged   = $row['NEW_CHARGED_BU'];
                                    $itemStatus     = $row['ITEM_STATUS'];
                                    $itemName       = $row['ITEM_NAME'];
                                    $newSupplierCd  = $row['NEW_SUPPLIER_CD'];
                                    $newSupplierName= $row['NEW_SUPPLIER_NAME'];
                                    $newDeliveryDate= $row['NEW_DELIVERY_DATE'];
                                    $newItemCd      = $row['NEW_ITEM_CD'];
                                    $newItemName    = $row['NEW_ITEM_NAME'];
                                    $newQty         = $row['NEW_QTY'];
                                    $actualQty      = $row['ACTUAL_QTY'];
                                    $newUnitCd      = $row['NEW_UNIT_CD'];
                                    $newItemPrice   = $row['NEW_ITEM_PRICE'];
                                    $attachmentItemCount= $row['ATTACHMENT_ITEM_COUNT'];
                                    $totalSupplier  = $row['TOTAL_SUPPLIER'];
                                    $plantAlias     = $row['PLANT_ALIAS'];
                                    $countDateDiff  = $row['COUNT_DATE_DIFF'];
                                    $createBy       = $row['CREATE_BY'];
                                    //$createByName   = $row['CREATE_BY_NAME'];
                                    $requesterName  = $row['REQUESTER_NAME'];
                                    $itemNo++;
                                ?>
                                    <tr>
                                        <td class="td-number">
                                            <?php echo $itemNo;?>.
                                        </td>
                                        <td class="td-actions" style="display: none">
                                            <a href="#" class="btn btn-small btn-success" id="window-edit">
                                                <i class="btn-icon-only icon-ok"> </i>
                                            </a>
                                        </td>
                                        <td style="display: none">
                                            <?php echo $transferId;?>
                                        </td>
                                        <td style="display: none">
                                            <?php echo $itemName;?>
                                        </td>
                                        <td>
                                            <?php
                                            if($createBy == $sUserId || $sRoleId == 'ROLE_03' || $sRoleId == 'ROLE_06')
                                            {
                                            ?>
                                                <a href="../db/Redirect/PO_Screen.php?criteria=outstandingPo&transferId=<?php echo $transferId;?>" class="faq-list">
                                                    <?php echo $prNo;?>
                                                </a>
                                            <?    
                                            }
                                            else
                                            {
                                                echo $prNo;
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php echo $requesterName;?>
                                        </td>
                                        <td class="td-date-column">
                                            <?php echo $procAcceptDate;?>
                                        </td>
                                        <td style="display: none">
                                            <?php echo substr($createByName, 0, strpos($createByName, ' '));?>
                                        </td>
                                        <td class="td-date-column">
                                            <?php echo $newDeliveryDate;?>
                                        </td>
                                        <td>
                                            <?php echo $newItemCd;?>
                                        </td>
                                        <td>
                                        <?php
                                        if($itemStatus == '1160')
                                        {
                                        ?>
                                            <u><?php echo $newItemName;?></u>
                                        <?php    
                                        }
                                        else
                                        {
                                             echo $newItemName;
                                        }
                                        ?>
                                        </td>
                                        <td class="td-align-right">
                                            <?php
                                                $qty = 0;
                                                if($newQty == $actualQty)
                                                {
                                                    $qty = $newQty;
                                                }
                                                else
                                                {
                                                    $qty = $newQty - $actualQty;
                                                }
                                                $split = explode('.', $qty);
                                                if($split[1] == 0)
                                                {
                                                    echo number_format($qty);
                                                }
                                                else
                                                {
                                                    echo $qty;
                                                }
                                                
                                            ?>
                                        </td>
                                        <td>
                                            <?php echo $newUnitCd;?>
                                        </td>
                                        <td>
                                            <?php echo $newPrCharged;?>
                                        </td>
                                        <td>
                                            <?php echo $plantAlias;?>
                                        </td>
                                        <td class="td-align-right">
                                            <?php echo $totalSupplier;?>
                                        </td>
                                        <td style="display: none">
                                            <?php echo $countDateDiff;?>
                                        </td>
                                        <td>
                                        <?php
                                            if($countDateDiff < 8){
                                                echo '';
                                            }
                                            else if($countDateDiff >= 8 && $countDateDiff < 15){
                                                echo '*';
                                            } 
                                            else if($countDateDiff >= 15 && $countDateDiff < 22){
                                                echo '**';
                                            }
                                            else{
                                                echo '***';
                                            }
                                        ?>
                                        </td>
                                        <?php
                                            if($attachmentItemCount > 0){
                                        ?>
                                        <td class="td-actions">
                                            <a href="#" class="btn btn-small btn-info" id="window-attach">
                                                <i class="btn-icon-only icon-paper-clip"> </i>
                                            </a>
                                        </td>
                                        <?        
                                            }else{
                                        ?>
                                        <td></td>
                                        <?php
                                            }
                                        ?>
                                    </tr>
                                <?php    
                                }
                                ?>
									<tr>
                                        <th colspan="19">
                                            <?php
                                                if($countTransfer > $max_per_page)
                                                {    
                                                    echo "<div id=\"pagination\" >";
                                                    if ($query != "")
                                                    {
                                                        $fld = "prNo=$prNoCriteria";
                                                        $fld .= "&mpage";
                                                    }
                                                    else
                                                    {
                                                        $fld = "mpage";
                                                    }
                                                    paging($query,$max_per_page,$num,$mpage,$fld,$countTransfer);
                                                    echo "</div>";
                                                }
                                            ?>
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                        </div><!-- /widget-content --> 
                    </div><!-- /widget --> 
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
                    &copy; 2014 PT. TD AUTOMOTIVE COMPRESSOR INDONESIA. All rights reserved. 
                </div> <!-- /span12 -->	
            </div> <!-- /row -->
	</div> <!-- /container -->		
    </div> <!-- /footer-inner -->	
</div> <!-- /footer -->

<div id="dialog-confirm-logout" title="Confirm" style="display: none;"></div>
<div id="dialog-attach-table" title="Item Attachment" style="display: none;">
    <div class="widget">
        <div class='widget-content'>
            <div class='control-group' id="dialog-control-group-attach">
            </div>
        </div>
    </div>
</div>
    </body>
</html>
