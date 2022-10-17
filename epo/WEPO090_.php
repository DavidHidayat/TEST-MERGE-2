<?php session_start(); 
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Constant.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Common.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/CONTROLLER/PAGING.php";
set_time_limit(1800);
ini_set('memory_limit', '512M'); 
if(isset($_SESSION['sUserId']))
{ 
    $sUserId    = $_SESSION['sUserId'];
    if($sUserId != '')
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
        
        if($sRoleId == 'ROLE_02' || $sRoleId == 'ROLE_03' || $sRoleId == 'ROLE_04' || $sRoleId == 'ROLE_06' || $sRoleId == 'ROLE_07' || $sRoleId == 'ROLE_09')
        {
            $poNoCriteria       	= trim($_GET['poNoCriteria']);
            $poDateCriteria     	= trim($_GET['poDateCriteria']);
            $poDateEndCriteria  	= trim($_GET['poDateEndCriteria']);
            $deliveryDateCriteria	= trim($_GET['deliveryDateCriteria']);
            $prNoCriteria       	= trim($_GET['prNoCriteria']);
            $requesterCriteria  	= trim($_GET['requesterCriteria']);
            $supplierCdCriteria 	= trim($_GET['supplierCdCriteria']);
            $supplierNameCriteria   = trim($_GET['supplierNameCriteria']);
            $supplierNameCriteria   = trim($_GET['supplierNameCriteria']);
            $itemNameCriteria   	= trim($_GET['itemNameCriteria']);
            $itemTypeCriteria   	= trim($_GET['itemTypeCriteria']); 
            $expNoCriteria      	= trim($_GET['expNoCriteria']); 
            $invNoCriteria      	= trim($_GET['invNoCriteria']);
            $rfiNoCriteria      	= trim($_GET['rfiNoCriteria']); 
            $itemStatusCriteria 	= trim($_GET['itemStatusCriteria']);
            $prChargedCriteria 		= trim($_GET['prChargedCriteria']);
            $poStatusCriteria   	= trim($_GET['poStsCriteria']);
            $roStatusCriteria   	= trim($_GET['roStsCriteria']);
            
            if($poNoCriteria || $supplierCdCriteria || $poDateCriteria || $poDateEndCriteria
                    || $deliveryDateCriteria || $prNoCriteria || $requesterCriteria 
                    || $supplierCdCriteria || $supplierNameCriteria || $itemNameCriteria
                    || $itemTypeCriteria || $expNoCriteria || $invNoCriteria || $rfiNoCriteria
					|| $itemStatusCriteria || $prChargedCriteria || $poStatusCriteria || $roStatusCriteria)
            {
				if(isset($_GET['mpage']))
                {
                    $mpage = trim($_GET['mpage']);
                }
                else
                {
                    $mpage = 1;
                }
                $max_per_page   = constant('20');;
                $num            = 5;
                                    
                if($mpage)
                { 
                    $start = ($mpage) * $max_per_page; 
                }
                else
                {
                    $start  = constant('20');;	
                    $mpage  = 1;
                }
                                    
                if($mpage == 1)
                {
                    $poListNo = 0;
                }
                else
                {
                    $poListNo = ($max_per_page * ($mpage - 1));
                }
				
                $wherePoSelect = array();
                if($poNoCriteria){
                    $wherePoSelect[] = "EPS_T_PO_DETAIL.PO_NO = '".$poNoCriteria."'";
                }
                if($poDateCriteria && !$poDateEndCriteria){
                        $wherePoSelect[] = "EPS_T_PO_HEADER.ISSUED_DATE = '".encodeDate($poDateCriteria)."'";
                }
                if(!$poDateCriteria && $poDateEndCriteria ){
                        $wherePoSelect[] = "EPS_T_PO_HEADER.ISSUED_DATE = '".encodeDate($poDateEndCriteria)."'";
                }
                if($poDateCriteria && $poDateEndCriteria){
                    $wherePoSelect[] = "EPS_T_PO_HEADER.ISSUED_DATE >= '".encodeDate($poDateCriteria)."'
                                        and EPS_T_PO_HEADER.ISSUED_DATE <= '".encodeDate($poDateEndCriteria)."'";
                }
                if($deliveryDateCriteria){
                    $wherePoSelect[] = "EPS_T_PO_HEADER.DELIVERY_DATE = '".encodeDate($deliveryDateCriteria)."'";
                }
                if($prNoCriteria){
                    $wherePoSelect[] = "EPS_T_TRANSFER.PR_NO = '".$prNoCriteria."'";
                }
                if($requesterCriteria){
                    $wherePoSelect[] = "EPS_M_EMPLOYEE.NAMA1 like '".$requesterCriteria."%'";
                }
                if($supplierCdCriteria){
                    $wherePoSelect[] = "EPS_T_PO_HEADER.SUPPLIER_CD = '".$supplierCdCriteria."'";
                }
                if($supplierNameCriteria){
                    $wherePoSelect[] = "EPS_T_PO_HEADER.SUPPLIER_CD = '".$supplierNameCriteria."'";
                }
                if($itemNameCriteria){
                    $wherePoSelect[] = "EPS_T_PO_DETAIL.ITEM_NAME LIKE '%".$itemNameCriteria."%'";
                }
                if($itemTypeCriteria){
                    $wherePoSelect[] = "EPS_T_TRANSFER.NEW_ITEM_TYPE_CD = '".$itemTypeCriteria."'";
                }
                if($expNoCriteria){
                    $wherePoSelect[] = "EPS_T_TRANSFER.NEW_ACCOUNT_NO = '".$expNoCriteria."'";
                }
                if($invNoCriteria){
                    $wherePoSelect[] = "EPS_T_TRANSFER.NEW_ACCOUNT_NO = '".$invNoCriteria."'";
                }
				if($rfiNoCriteria){
                    $wherePoSelect[] = "EPS_T_TRANSFER.NEW_RFI_NO = '".$rfiNoCriteria."'";
                }
                if($itemStatusCriteria){
                    $wherePoSelect[] = "EPS_T_TRANSFER.ITEM_STATUS = '".$itemStatusCriteria."'";
                }
                if($prChargedCriteria){
                    $wherePoSelect[] = "EPS_T_TRANSFER.NEW_CHARGED_BU = '".$prChargedCriteria."'";
                }
                if($poStatusCriteria){
                    $wherePoSelect[] = "EPS_T_PO_HEADER.PO_STATUS = '".$poStatusCriteria."'";
                }
                if($roStatusCriteria){
                    $wherePoSelect[] = "EPS_T_PO_DETAIL.RO_STATUS = '".$roStatusCriteria."'";
                }
				/**
                 * SELECT COUNT EPS_T_PO
                 **/
                $query_count_t_po = "select 
                                        count (*) as COUNT_PO
                                    from
                                        EPS_T_PO_DETAIL 
                                    left join
                                        EPS_T_PO_HEADER
                                    on
                                        EPS_T_PO_DETAIL.PO_NO = EPS_T_PO_HEADER.PO_NO
                                    left join
                                        EPS_M_APP_STATUS 
                                    on 
                                        EPS_M_APP_STATUS.APP_STATUS_CD = EPS_T_PO_DETAIL.RO_STATUS
                                    left join
                                        EPS_M_APP_STATUS EPS_M_APP_STATUS_2
                                    on 
                                        EPS_M_APP_STATUS_2.APP_STATUS_CD = EPS_T_PO_HEADER.PO_STATUS
                                    left join
                                        EPS_T_TRANSFER
                                    on
                                        EPS_T_PO_DETAIL.REF_TRANSFER_ID = EPS_T_TRANSFER.TRANSFER_ID
                                    left join
                                        EPS_M_EMPLOYEE 
                                    on 
                                        EPS_T_TRANSFER.REQUESTER = EPS_M_EMPLOYEE.NPK
                                    left join
                                        EPS_M_EMPLOYEE EPS_M_EMPLOYEE_2 
                                    on 
                                        EPS_T_PO_HEADER.APPROVER = EPS_M_EMPLOYEE_2.NPK ";
                if(count($wherePoSelect)) {
                    $query_count_t_po .= "where " . implode(' and ', $wherePoSelect);
                }
                $sql_count_t_po = $conn->query($query_count_t_po);
                $row_count_t_po = $sql_count_t_po->fetch(PDO::FETCH_ASSOC);
                $countPo    = $row_count_t_po['COUNT_PO'];
               
                if($start > $countPo)
                {
                    $lgenap     = $start - $max_per_page;
                    $max_per_pages = $countPo - $lgenap;
                    $start      = $countPo;
                }
                else
                {
                    $max_per_pages = $max_per_page;
                }
                 
                /**
                 * SELECT EPS_T_PO
                 **/
                $query_t_po_select = "
                        select 
                            * 
                        from 
                            (select top  $max_per_pages  
                                * 
                            from      
                                (select top $start 
                                    EPS_T_PO_DETAIL.REF_TRANSFER_ID
                                    ,EPS_T_PO_DETAIL.PO_NO
                                    ,EPS_T_PO_HEADER.SUPPLIER_NAME
                                    ,EPS_T_PO_HEADER.CURRENCY_CD
                                    ,substring(EPS_T_PO_HEADER.DELIVERY_DATE,7,2)+'/'+substring(EPS_T_PO_HEADER.DELIVERY_DATE,5,2)+'/'+substring(EPS_T_PO_HEADER.DELIVERY_DATE,1,4) as DELIVERY_DATE
                                    ,EPS_T_PO_DETAIL.ITEM_CD
                                    ,EPS_T_PO_DETAIL.ITEM_NAME
                                    ,EPS_T_PO_DETAIL.QTY
                                    ,EPS_T_PO_DETAIL.UNIT_CD
                                    ,EPS_T_PO_DETAIL.ITEM_PRICE
                                    ,EPS_T_PO_DETAIL.AMOUNT
                                    ,EPS_T_PO_DETAIL.ITEM_TYPE_CD
                                    ,EPS_T_PO_DETAIL.ACCOUNT_NO
                                    ,EPS_T_PO_DETAIL.RFI_NO
                                    ,isnull(
                                        (select 
                                            sum(TRANSACTION_QTY)
                                        from 
                                            EPS_T_RO_DETAIL
                                        where   
                                            EPS_T_RO_DETAIL.REF_TRANSFER_ID = EPS_T_PO_DETAIL.REF_TRANSFER_ID
                                            and EPS_T_RO_DETAIL.PO_NO = EPS_T_PO_DETAIL.PO_NO
                                            and EPS_T_RO_DETAIL.TRANSACTION_FLAG = 'A')
                                    ,0
                                    )
                                    as TOTAL_RECEIVED_QTY
                                    ,isnull(
                                        (select 
                                            sum(TRANSACTION_QTY)
                                        from 
                                            EPS_T_RO_DETAIL
                                        where   
                                            EPS_T_RO_DETAIL.REF_TRANSFER_ID = EPS_T_PO_DETAIL.REF_TRANSFER_ID
                                            and EPS_T_RO_DETAIL.PO_NO = EPS_T_PO_DETAIL.PO_NO
                                            and EPS_T_RO_DETAIL.TRANSACTION_FLAG = 'C')
                                    ,0
                                    )
                                    as TOTAL_CANCELED_QTY
                                    ,isnull(
                                        (select 
                                            sum(TRANSACTION_QTY)
                                        from 
                                            EPS_T_RO_DETAIL
                                        where   
                                            EPS_T_RO_DETAIL.REF_TRANSFER_ID = EPS_T_PO_DETAIL.REF_TRANSFER_ID
                                            and EPS_T_RO_DETAIL.PO_NO = EPS_T_PO_DETAIL.PO_NO
                                            and EPS_T_RO_DETAIL.TRANSACTION_FLAG = 'O')
                                    ,0
                                    )
                                    as TOTAL_OPENED_QTY
                                    ,EPS_T_PO_DETAIL.RO_STATUS
                                    ,EPS_M_APP_STATUS.APP_STATUS_NAME as RO_STATUS_NAME
                                    ,EPS_T_TRANSFER.PR_NO
                                    ,EPS_M_EMPLOYEE.NAMA1 as REQUESTER_NAME
                                    ,EPS_T_PO_HEADER.PO_STATUS
                                    ,EPS_M_APP_STATUS_2.APP_STATUS_NAME as PO_STATUS_NAME
                                    ,(select count(*)
                                        from          
                                            EPS_T_TRANSFER_SUPPLIER
                                        where      
                                            EPS_T_PO_DETAIL.REF_TRANSFER_ID = EPS_T_TRANSFER_SUPPLIER.TRANSFER_ID) 
                                    as TOTAL_SUPPLIER
                                    ,EPS_T_PO_HEADER.APPROVER
                                    ,EPS_M_EMPLOYEE_2.NAMA1 as APPROVER_NAME
                                from
                                    EPS_T_PO_DETAIL 
                                left join
                                    EPS_T_PO_HEADER
                                on
                                    EPS_T_PO_DETAIL.PO_NO = EPS_T_PO_HEADER.PO_NO
                                left join
                                    EPS_M_APP_STATUS 
                                on 
                                    EPS_M_APP_STATUS.APP_STATUS_CD = EPS_T_PO_DETAIL.RO_STATUS
                                left join
                                    EPS_M_APP_STATUS EPS_M_APP_STATUS_2
                                on 
                                    EPS_M_APP_STATUS_2.APP_STATUS_CD = EPS_T_PO_HEADER.PO_STATUS
                                left join
                                    EPS_T_TRANSFER
                                on
                                    EPS_T_PO_DETAIL.REF_TRANSFER_ID = EPS_T_TRANSFER.TRANSFER_ID
                                left join
                                    EPS_M_EMPLOYEE 
                                on 
                                    EPS_T_TRANSFER.REQUESTER = EPS_M_EMPLOYEE.NPK
                                left join
                                    EPS_M_EMPLOYEE EPS_M_EMPLOYEE_2 
                                on 
                                    EPS_T_PO_HEADER.APPROVER = EPS_M_EMPLOYEE_2.NPK ";
                if(count($wherePoSelect)) {
                    $query_t_po_select .= "where " . implode(' and ', $wherePoSelect);
                }
                $query_t_po_select .= " 
                                order by 
                                   EPS_T_PO_HEADER.PO_NO desc
                                    ,EPS_T_PO_DETAIL.REF_TRANSFER_ID )
                                as T1
                            order by
                                T1.PO_NO asc
                                ,T1.REF_TRANSFER_ID asc )
                            as T2
                        order by
                            T2.PO_NO desc
                            ,T2.REF_TRANSFER_ID ";
                $sql_t_po_select = $conn->query($query_t_po_select);
                $row_t_ro_select = $sql_t_po_select->fetch(PDO::FETCH_ASSOC);
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
        <script src="../js/epo/WEPO090.js"></script>
        <script>
            $(function() {
                var dateToday = new Date();
                $("#poDate").datepicker({
                    dateFormat: 'dd/mm/yy',
                    //defaultDate: "+1w",
                    autoClose: true,
                    beforeShowDay: $.datepicker.noWeekends
                });
                $("#poDateEnd").datepicker({
                    dateFormat: 'dd/mm/yy',
                    //defaultDate: "+1w",
                    autoClose: true,
                    beforeShowDay: $.datepicker.noWeekends
                });
                $("#deliveryDate").datepicker({
                    dateFormat: 'dd/mm/yy',
                    //defaultDate: "+1w",
                    autoClose: true,
                    beforeShowDay: $.datepicker.noWeekends
                });
                
            });
        </script>
        <title>EPS</title>
    </head>
    <body> 
    </body>
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
                <li>
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
                <!--<li>
                    <a href="WEPO017.php">
                        <i class="icon-th"></i><span>PR Item</span> 
                    </a>
                </li>-->
                <li class="active">
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
                    <?php
                    if($poNoCriteria || $supplierCdCriteria || $poDateCriteria || $poDateEndCriteria
						|| $deliveryDateCriteria || $prNoCriteria || $requesterCriteria 
						|| $supplierCdCriteria || $supplierNameCriteria || $itemNameCriteria
						|| $itemTypeCriteria || $expNoCriteria || $invNoCriteria || $rfiNoCriteria
						|| $itemStatusCriteria || $prChargedCriteria || $poStatusCriteria || $roStatusCriteria)
                    {
                        if(!$row_t_ro_select)
                        {
                    ?>
                    <div class="alert" id="mandatory-msg-2">
                        <strong>Data not found!</strong> No results match with your search.
                    </div>
                    <?php    
                        }
                    }
                    ?>
                    <!----- PO Open Delivery ---->
                    <div class="widget ">
                        <div class="widget-header">
                            <i class="icon-search"></i>
                            <h3>Search</h3>
                        </div>
                        <div class="widget-content">
                            <form id="WEPO090Form">
                                <div class="control-group">	
                                    <table class="table-non-bordered">
                                        <tr>
                                            <td>
                                                <label class="control-label" for="poNo">PO No: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" id="poNo" name="poNoCriteria" maxlength="8" value="<?php echo $poNoCriteria;?>" />
                                                </div>
                                            </td>
                                            <td colspan="2">
                                                <label class="control-label" for="poDate">PO Date: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" id="poDate" name="poDateCriteria" maxlength="8" value="<?php echo $poDateCriteria;?>" />
                                                -
                                                    <input type="text" class="span2" id="poDateEnd" name="poDateEndCriteria" maxlength="8" value="<?php echo $poDateEndCriteria;?>" />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="deliveryDate">PO Due Date: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" id="deliveryDate" name="deliveryDateCriteria" value="<?php echo $deliveryDateCriteria;?>" />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="prNo">PR No: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" id="prNo" name="prNoCriteria" maxlength="10" value="<?php echo $prNoCriteria;?>"  />
                                                </div>
                                            </td>
                                            <td colspan="2">
                                                <label class="control-label" for="requester">PR Requester: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" id="requester" name="requesterCriteria" maxlength="10" value="<?php echo $requesterCriteria;?>" />
                                                </div>
                                            </td>
                                            
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="control-label" for="supplierCd">Supplier Code: </label>
                                                <div class="controls">
                                                    <select id="supplierCd" class="span2" name="supplierCdCriteria">
                                                        <option value=""></option>
                                                    <?php
                                                        $query = "select 
                                                                    SUPPLIER_CD 
                                                                    ,SUPPLIER_NAME
                                                                    ,CURRENCY_CD
                                                                from 
                                                                    EPS_M_SUPPLIER";
                                                        $sql = $conn->query($query);
                                                        while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                                                            $supplierCdSelect     = $row['SUPPLIER_CD'];
                                                            $supplierNameSelect   = $row['SUPPLIER_NAME'];
                                                            $currencyCdSelect     = $row['CURRENCY_CD'];
                                                    ?>
                                                        <option value="<?php echo $supplierCdSelect;?>" <?php if($supplierCdCriteria == $supplierCdSelect) echo "selected"; ?>><?php echo $supplierCdSelect;?></option>
                                                    <?php         
                                                        }
                                                    ?>
                                                    </select>
                                                </div>
                                            </td>
                                            <td colspan="2">
                                                <label class="control-label" for="supplierCd">Supplier Name: </label>
                                                <div class="controls">
                                                    <select id="supplierName" class="span4" name="supplierNameCriteria">
                                                        <option value=""></option>
                                                    <?php
                                                        $query = "select 
                                                                    SUPPLIER_CD 
                                                                    ,SUPPLIER_NAME
                                                                    ,CURRENCY_CD
                                                                from 
                                                                    EPS_M_SUPPLIER";
                                                        $sql = $conn->query($query);
                                                        while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                                                            $supplierCdSelect2     = $row['SUPPLIER_CD'];
                                                            $supplierNameSelect   = $row['SUPPLIER_NAME'];
                                                            $currencyCdSelect     = $row['CURRENCY_CD'];
                                                    ?>
                                                        <option value="<?php echo $supplierCdSelect2;?>" <?php if($supplierNameCriteria == $supplierCdSelect2) echo "selected"; ?>><?php echo $supplierNameSelect;?></option>
                                                    <?php         
                                                        }
                                                    ?>
                                                    </select>
                                                </div>
                                            </td>
                                            <td colspan="3">
                                                <label class="control-label" for="itemName">Item Name: </label>
                                                <div class="controls">
                                                    <input type="text" class="full-width-input" id="itemName" name="itemNameCriteria" value="<?php echo $itemNameCriteria;?>" />
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="control-label" for="itemType">Type: </label>
                                                <div class="controls">
                                                    <select id="itemType" class="span2" name="itemTypeCriteria">
                                                        <option value=""></option>
                                                    <?php 
                                                        $whereItemTypeMaster = array(); 
                                                        $query_select_m_item_type = "select 
                                                                        ITEM_TYPE_CD
                                                                        ,ITEM_TYPE_NAME
                                                                        ,ITEM_TYPE_ALIAS
                                                                    from 
                                                                        EPS_M_ITEM_TYPE ";
                                                        if($sInvType == 'N1000')
                                                        {
                                                            $whereItemTypeMaster[] = "ITEM_TYPE_CD = '1'
                                                                                        or ITEM_TYPE_CD = '2'
                                                                                        or ITEM_TYPE_CD = '3'";
                                                        }
                                                        else if($sInvType == 'N1001')
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
                                                            $query_select_m_item_type .= "where " . implode('or ', $whereItemTypeMaster);
                                                        }
                                                        $query_select_m_item_type .= "order by ITEM_TYPE_CD";
                                                        $sql_select_m_item_type = $conn->query($query_select_m_item_type);
                                                        while($row_select_m_item_type = $sql_select_m_item_type->fetch(PDO::FETCH_ASSOC)){
                                                            $itemType       = $row_select_m_item_type['ITEM_TYPE_CD'];
                                                            $itemTypeAlias  = $row_select_m_item_type['ITEM_TYPE_ALIAS'];
                                                    ?>
                                                        <option value="<?php echo $itemType;?>" <?php if($itemTypeCriteria == $itemType) echo "selected"; ?>><?php echo $itemTypeAlias;?></option>
                                                    <?php         
                                                        }
                                                    ?>
                                                    </select>
                                                </div>
                                            </td>
                                            <td id="td-exp-no" colspan="2">
                                                <label class="control-label" for="expNo">Expense No: </label>
                                                <div class="controls">
                                                    <select id="expNo" class="span4" name="expNoCriteria">
                                                        <option value=""></option>
                                                        <?php 
                                                            $query_select_m_account = "select 
                                                                        convert(int, ACCOUNT_NO) as ACCOUNT_NO
                                                                        ,ACCOUNT_CD
                                                                        ,ACCOUNT_NAME
                                                                    from 
                                                                        EPS_M_ACCOUNT
                                                                    where
                                                                        ITEM_TYPE_CD = '1'
                                                                    order by 
                                                                        ACCOUNT_NO";
                                                            $sql_select_m_account = $conn->query($query_select_m_account);
                                                            while($row_select_m_account = $sql_select_m_account->fetch(PDO::FETCH_ASSOC)){
                                                                $accountNo   = $row_select_m_account['ACCOUNT_NO'];
                                                                $accountCd   = $row_select_m_account['ACCOUNT_CD'];
                                                                $accountName = $row_select_m_account['ACCOUNT_NAME'];
                                                        ?>
                                                        <option value="<?php echo $accountNo;?>" <?php if($expNoCriteria == $accountNo) echo "selected"; ?>><?php echo $accountNo.'-'.$accountCd.'-'.$accountName;?></option>
                                                        <?php         
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </td>
                                            <td id="td-inv-no" colspan="2">
                                                <label class="control-label" for="invNo">Inventory No: </label>
                                                <div class="controls">
                                                    <select id="invNo" class="span4" name="invNoCriteria">
                                                        <option value=""></option>
                                                        <?php
                                                            $query_select_m_inventory = "select 
                                                                        CONVERT(int, ACCOUNT_NO) as ACCOUNT_NO
                                                                        ,ACCOUNT_CD
                                                                        ,ACCOUNT_NAME
                                                                    from 
                                                                        EPS_M_ACCOUNT
                                                                    where
                                                                        ITEM_TYPE_CD = '3'
                                                                        or ITEM_TYPE_CD = '4'
                                                                    order by 
                                                                        ACCOUNT_NO";
                                                            $sql_select_m_inventory = $conn->query($query_select_m_inventory);
                                                            while($row_select_m_inventory = $sql_select_m_inventory->fetch(PDO::FETCH_ASSOC)){
                                                                $accountNo   = $row_select_m_inventory['ACCOUNT_NO'];
                                                                $accountCd   = $row_select_m_inventory['ACCOUNT_CD'];
                                                                $accountName = $row_select_m_inventory['ACCOUNT_NAME'];
                                                        ?>
                                                        <option value="<?php echo $accountNo;?>" <?php if($invNoCriteria == $accountNo) echo "selected"; ?>><?php echo $accountNo.'-'.$accountCd.'-'.$accountName;?></option>
                                                        <?php         
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </td>
                                            <td id="td-rfi-no">
                                                <label class="control-label" for="rfiNo">RFI No: </label>
                                                <div class="controls">
                                                    <input type="text" id="rfiNo" class="span2" value="<?php echo $rfiNoCriteria;?>" name="rfiNoCriteria" maxlength="6" />
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="control-label" for="poStatus">Item Status: </label>
                                                <div class="controls">
                                                    <select id="itemStatus" class="span2" name="itemStatusCriteria">
                                                        <option value=""></option>
                                                    <?php
                                                        $query = "select 
                                                                    APP_STATUS_CD 
                                                                    ,APP_STATUS_NAME
                                                                from 
                                                                    EPS_M_APP_STATUS
                                                                where
                                                                    APP_STATUS_CD = '1110'
                                                                    or APP_STATUS_CD = '1120'
                                                                    or APP_STATUS_CD = '1130'
                                                                    or APP_STATUS_CD = '1140'
                                                                    or APP_STATUS_CD = '1150'
                                                                    or APP_STATUS_CD = '1160'
                                                                    or APP_STATUS_CD = '1170'
                                                                    or APP_STATUS_CD = '1270'
                                                                order by
                                                                    APP_STATUS_NAME ";
                                                        $sql = $conn->query($query);
                                                        while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                                                            $appStsCd    = $row['APP_STATUS_CD'];
                                                            $appStsName   = $row['APP_STATUS_NAME'];
                                                    ?>
                                                        <option value="<?php echo $appStsCd;?>" <?php if($itemStatusCriteria == $appStsCd) echo "selected"; ?>><?php echo $appStsName;?></option>
                                                    <?php         
                                                        }
                                                    ?>
                                                    </select>
                                                </div>
                                            </td>
                                            <td colspan="2">
                                                <label class="control-label" for="prDate">Charged BU: </label>
                                                <div class="controls">
                                                    <select id="prCharged" class="span4" name="prChargedCriteria">
                                                        <option value=""></option>
                                                    <?php
                                                        $query_select_m_bunit = "select 
                                                                    KDBU as BU_CD
                                                                    ,NMBU1 as BU_NAME
                                                                    ,KDBU + '- ' + NMBU1 as BU_CD_NAME
                                                                  from 
                                                                    EPS_M_TBUNIT 
                                                                  where
                                                                    KDBU not like 'T%'
                                                                  order by 
                                                                    KDBU";
                                                        $sql_select_m_bunit = $conn->query($query_select_m_bunit);
                                                        while($row_select_m_bunit = $sql_select_m_bunit->fetch(PDO::FETCH_ASSOC)){
                                                            $buCd   = $row_select_m_bunit['BU_CD'];
                                                            $buCdName = $row_select_m_bunit['BU_CD_NAME'];
                                                    ?>
                                                        <option value="<?php echo trim($buCd);?>"  <?php if(trim($prChargedCriteria) == trim($buCd)) echo "selected"; ?>><?php echo $buCdName;?></option>
                                                    <?php         
                                                        }
                                                    ?>
                                                    </select>
                                                </div>
                                            </td>
                                            <td colspan="2">
                                                <label class="control-label" for="poSts">PO Status: </label>
                                                <div class="controls">
                                                    <select id="poSts" class="span4" name="poStsCriteria">
                                                        <option value=""></option>
                                                    <?php 
                                                        $query = "select 
                                                                    APP_STATUS_CD 
                                                                    ,APP_STATUS_NAME
                                                                from 
                                                                    EPS_M_APP_STATUS
                                                                where
                                                                    APP_STATUS_CD in ('1210','1220','1230','1240','1250','1280','1290','1330','1340','1370')
                                                                order by
                                                                    APP_STATUS_NAME  ";
                                                        $sql = $conn->query($query);
                                                        while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                                                            $poStsCd    = $row['APP_STATUS_CD'];
                                                            $poStsName  = $row['APP_STATUS_NAME'];
                                                    ?>
                                                        <option value="<?php echo $poStsCd;?>" <?php if($poStatusCriteria == $poStsCd) echo "selected"; ?>><?php echo $poStsName;?></option>
                                                    <?php         
                                                        }
                                                    ?>
                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="roSts">Receiving Status: </label>
                                                <div class="controls">
                                                    <select id="roSts" class="span2" name="roStsCriteria">
                                                        <option value=""></option>
                                                    <?php
                                                        $query = "select 
                                                                    APP_STATUS_CD 
                                                                    ,APP_STATUS_NAME
                                                                from 
                                                                    EPS_M_APP_STATUS
                                                                where
                                                                    APP_STATUS_CD = '1310'
                                                                    or APP_STATUS_CD = '1320' ";
                                                        $sql = $conn->query($query);
                                                        while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                                                            $appStsCd    = $row['APP_STATUS_CD'];
                                                            $appStsName   = $row['APP_STATUS_NAME'];
                                                    ?>
                                                        <option value="<?php echo $appStsCd;?>" <?php if($roStatusCriteria == $appStsCd) echo "selected"; ?>><?php echo $appStsName;?></option>
                                                    <?php         
                                                        }
                                                    ?>
                                                    </select>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </form>
                            <div>
                                <button class="btn btn-primary" id="btn-search">Search</button> 
                                <button class="btn" id="btn-reset">Reset</button>
                            </div> 
                        </div>
                    </div>
                    <?php
                    if($row_t_ro_select)
                    {
                    ?>
                        <!----- PO Item List ---->
                        <div class="widget widget-table action-table">
                            <div class="widget-header"> 
                                <i class="icon-search"></i>
                                <h3>PO Information</h3>
                            </div>
                            <div class="widget-content">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th colspan="19" style="text-align: left">
													<a href="../db/REPORT/PO_SEARCH.php?poNo=<?php echo trim($poNoCriteria);?>
                                                        &poDate=<?php echo trim($poDateCriteria);?>&poDateEnd=<?php echo trim($poDateEndCriteria);?>
                                                        &deliveryDate=<?php echo $deliveryDateCriteria;?>&prNo=<?php echo trim($prNoCriteria);?>
                                                        &requester=<?php echo $requesterCriteria?>&supplierCd=<?php echo $supplierCdCriteria;?>
                                                        &supplierName=<?php echo $supplierNameCriteria;?>&itemName=<?php echo $itemNameCriteria;?>
                                                        &itemType=<?php echo $itemTypeCriteria?>&expNo=<?php echo $expNoCriteria;?>
                                                        &rfiNo=<?php echo $rfiNoCriteria?>&invNo=<?php echo $invNoCriteria;?>
                                                        &itemStatus=<?php echo $itemStatusCriteria?>&prCharged=<?php echo $prChargedCriteria;?>
                                                        &poSts=<?php echo $poStatusCriteria;?>&roSts=<?php echo $roStatusCriteria;?>" class="btn btn-small btn-linkedin-alt" target="_blank">
                                                    Download
                                                    <i class="btn-icon-only icon-download-alt"> </i>
													</a>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th rowspan="2">NO</th>
                                            <th rowspan="2" style="display: none">REF TRANSFER ID</th>
                                            <th colspan="6">PO</th>
                                            <th colspan="2">PR</th>
                                            <th colspan="5">ITEM</th>
                                            <th colspan="2">QTY</th>
                                            <th colspan="2">RECEIVING</th>
                                            <th rowspan="2">SUPPLIER<br>REF</th>
                                        </tr>
                                        <tr>
                                            <th>PO NO</th>
                                            <th>STATUS</th>
                                            <th>APPROVER</th>
                                            <th>SUPPLIER</th>
                                            <th>CUR</th>
                                            <th>DUE DATE</th>
                                            <th>PR NO</th>
                                            <th>REQUESTER</th>
                                            <th style="display: none">CODE</th>
                                            <th>EXP<br>/RFI</th>
                                            <th>NAME</th>
                                            <th>UM</th>
                                            <th>PRICE</th>
                                            <th>AMOUNT</th>
                                            <th>ORDER</th>
                                            <th>OPEN</th>
                                            <th>STATUS</th>
                                            <th>REF</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $sql_t_ro_select_2 = $conn->query($query_t_po_select);
                                    while($row_t_ro_select_2 = $sql_t_ro_select_2->fetch(PDO::FETCH_ASSOC))
                                    {
                                        $refTransferId  = $row_t_ro_select_2['REF_TRANSFER_ID'];
                                        $poNo           = $row_t_ro_select_2['PO_NO'];
                                        $supplierName   = $row_t_ro_select_2['SUPPLIER_NAME'];
                                        $currencyCd     = $row_t_ro_select_2['CURRENCY_CD'];
                                        $deliveryDate   = $row_t_ro_select_2['DELIVERY_DATE'];
                                        $itemCd         = $row_t_ro_select_2['ITEM_CD'];
                                        $itemName       = $row_t_ro_select_2['ITEM_NAME'];
                                        $itemPrice      = $row_t_ro_select_2['ITEM_PRICE'];
                                        $amount         = $row_t_ro_select_2['AMOUNT'];
                                        $qty            = $row_t_ro_select_2['QTY'];
                                        $totalReceivedQty= $row_t_ro_select_2['TOTAL_RECEIVED_QTY'];
                                        $totalCanceledQty= $row_t_ro_select_2['TOTAL_CANCELED_QTY'];
                                        $totalOpenedQty = $row_t_ro_select_2['TOTAL_OPENED_QTY'];
                                        $unitCd         = $row_t_ro_select_2['UNIT_CD'];
                                        $roStatus       = $row_t_ro_select_2['RO_STATUS'];
                                        $roStatusName   = $row_t_ro_select_2['RO_STATUS_NAME'];
                                        $prNoDtl        = $row_t_ro_select_2['PR_NO'];
                                        $requesterName  = $row_t_ro_select_2['REQUESTER_NAME'];
                                        $poStatusName   = $row_t_ro_select_2['PO_STATUS_NAME'];
                                        $itemTypeCd     = $row_t_ro_select_2['ITEM_TYPE_CD'];
                                        $accountNo      = $row_t_ro_select_2['ACCOUNT_NO'];
                                        $rfiNo          = $row_t_ro_select_2['RFI_NO'];
                                        $totalSupplier  = $row_t_ro_select_2['TOTAL_SUPPLIER'];
                                        $approverName   = $row_t_ro_select_2['APPROVER_NAME'];
                                        
                                        if($itemTypeCd == '1' || $itemTypeCd == '3' || $itemTypeCd == '4')
                                        {
                                            $objectAccount = $accountNo;
                                        }
                                        if($itemTypeCd == '2')
                                        {
                                            $objectAccount = $rfiNo;
                                        }
                                        if(strlen($objectAccount) == 1)
                                        {
                                            $objectAccount = '0'.$accountNo;
                                        }
                                            
                                        $totalOpenQty   = ($qty - $totalReceivedQty) + $totalCanceledQty + $totalOpenedQty;
                                        $poListNo++;   
                                    ?>
                                        <tr>
                                            <td class="td-number">
                                                <?php echo $poListNo;?>.
                                            </td>
                                            <td style="display: none">
                                                <?php echo $refTransferId;?>
                                            </td>
                                            <td>
                                                <a href="../db/Redirect/PO_Screen.php?criteria=poDetail&paramPoNo=<?php echo $poNo;?>" class="faq-list">
                                                    <?php echo $poNo;?>
                                                </a>
                                            </td>
                                            <td>
                                                <?php echo $poStatusName;?>
                                            </td>
                                            <td>
                                                <?php echo substr($approverName, 0, strpos($approverName, ' '));?>
                                            </td>
                                            <td>
                                                <?php echo $supplierName;?>
                                            </td>
                                            <td>
                                                <?php echo $currencyCd;?>
                                            </td>
                                            <td>
                                                <?php echo $deliveryDate;?>
                                            </td>
                                            <td>
                                                <a href="#" class="faq-list  pr-no">
                                                    <?php echo $prNoDtl;?>
                                                </a>
                                            </td>
                                            <td>
                                                <?php echo $requesterName;?>
                                            </td>
                                            <td style="display: none">
                                                <?php echo $itemCd;?>
                                            </td>
                                            <td>
                                            <?php
                                               echo $objectAccount;
                                            ?> 
                                            </td>
                                            <td>
                                            <?php
                                               echo $itemName;
                                            ?>
                                            </td>
                                            </td>
                                            <td>
                                                <?php echo $unitCd;?>
                                            </td>
                                            <td class="td-align-right">
                                                <?php 
                                                $split_item_price = explode('.', $itemPrice);
                                                if($split_item_price[1] == 0)
                                                {
                                                    echo number_format($itemPrice);
                                                }
                                                else
                                                {
                                                    echo number_format($itemPrice,2);
                                                }
                                                ?>
                                            </td>
                                            <td class="td-align-right">
                                                <?php 
                                                $split_item_amount = explode('.', $amount);
                                                if($split_item_amount[1] == 0)
                                                {
                                                    echo number_format($amount);
                                                }
                                                else
                                                {
                                                    echo number_format($amount,2);
                                                }
                                                ?>
                                            </td>
                                            <td class="td-align-right amount">
                                                <?php 
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
                                            <td class="td-align-right amount">
                                                <?php 
                                                $split = explode('.', $totalOpenQty);
                                                if($split[1] == 0)
                                                {
                                                    echo number_format($totalOpenQty);
                                                }
                                                else
                                                {
                                                    echo $totalOpenQty;
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php echo $roStatusName;?>
                                            </td>
                                            <td>
                                            <?php
                                                if($totalReceivedQty > 0 ){
                                            ?>
                                                <a href="#" class="btn btn-small btn-inverse" id="window-receiving">
                                                    <i class="btn-icon-only icon-external-link "> </i>
                                                </a>
                                            <?php        
                                                }
                                            ?>
                                            </td>
                                            <td style="text-align: center">
                                            <?php 
                                            if($totalSupplier == 0){
                                                echo '';
                                            }else{
                                            ?>
                                                <a href="#" class="btn btn-small btn-info" id="window-refsupplier">
                                                    <i class="btn-icon-only icon-bookmark"> </i>
                                                </a>
                                            <?
                                            }
                                            ?>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
										<tr>
                                            <th colspan="19">
                                            <?php
                                                if($countPo > $max_per_page)
                                                {    
                                                    echo "<div id=\"pagination\" >";
                                                    if ($query_t_po_select != "")
                                                    {
                                                            $fld = "poNoCriteria=$poNoCriteria";
                                                            $fld .= "&poDateCriteria=$poDateCriteria";
                                                            $fld .= "&poDateEndCriteria=$poDateEndCriteria";
                                                            $fld .= "&deliveryDateCriteria=$deliveryDateCriteria";
                                                            $fld .= "&prNoCriteria=$prNoCriteria";
                                                            $fld .= "&requesterCriteria=$requesterCriteria";
                                                            $fld .= "&supplierCdCriteria=$supplierCdCriteria";
															$fld .= "&supplierNameCriteria=$supplierNameCriteria";
                                                            $fld .= "&itemNameCriteria=$itemNameCriteria";
                                                            $fld .= "&itemTypeCriteria=$itemTypeCriteria";
                                                            $fld .= "&expNoCriteria=$expNoCriteria";
                                                            $fld .= "&invNoCriteria=$invNoCriteria";
                                                            $fld .= "&rfiNoCriteria=$rfiNoCriteria";
                                                            $fld .= "&itemStatusCriteria=$itemStatusCriteria";
                                                            $fld .= "&prChargedCriteria=$prChargedCriteria";
                                                            $fld .= "&poStsCriteria=$poStatusCriteria";
                                                            $fld .= "&roStsCriteria=$roStatusCriteria";
                                                            $fld .= "&mpage";
                                                    }
                                                    else
                                                    {
                                                            $fld = "mpage";
                                                    }
                                                    paging($query_t_po_select,$max_per_page,$num,$mpage,$fld,$countPo);
                                                    echo "</div>";
                                                }
                                            ?>
                                            </th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
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


<div id="dialog-refsupplier-table" title="Reference Supplier List" style="display: none;">
    <div class="widget">
        <div class='widget-content'>
            <div class='control-group' id="dialog-control-group-refsupplier">
            </div>
        </div>
    </div>
</div>
<div id="dialog-pritem-table" title="PR Item Information" style="display: none;">
    <div class="widget">
        <div class='widget-content'>
            <div class='control-group' id="dialog-control-group-pritem">
            </div>
        </div>
    </div>
</div>
<div id="dialog-receiving-table" title="Receiving List" style="display: none;">
    <div class="widget">
        <div class='widget-content'>
            <div class='control-group' id="dialog-control-group-receiving">
            </div>
        </div>
    </div>
</div>
<div id="dialog-confirm-logout" title="Confirm" style="display: none;"></div>
    </body>
</html>
