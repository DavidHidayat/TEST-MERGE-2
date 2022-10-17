<?php session_start(); 
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Constant.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Common.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/CONTROLLER/PAGING.php";
if(isset($_SESSION['sUserId']))
{         
    $sUserId            = $_SESSION['sUserId'];
    $sActiveFlag	= $_SESSION['sactiveFlag'];
    $sActiveFlagLogin	= $_SESSION['sactiveFlagLogin'];
    
    if($sUserId != '' && $sActiveFlag == 'A' && $sActiveFlagLogin == 'A')
    {
        /** Unset SESSION */
        unset($_SESSION['prStatus']);
        unset($_SESSION['poStatus']);
        
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
        $sInvType   = $_SESSION['sInvType'];
        $sBunitCriteria = substr($sBunit,0,3);
        
        if($sRoleId == 'ROLE_01' || $sRoleId == 'ROLE_02' || $sRoleId == 'ROLE_03' || $sRoleId == 'ROLE_04' || $sRoleId == 'ROLE_05' || $sRoleId == 'ROLE_06' || $sRoleId == 'ROLE_07' || $sRoleId == 'ROLE_08' || $sRoleId == 'ROLE_09' || $sRoleId == 'ROLE_10' || $sRoleId == 'ROLE_11' )
        {
            $prNoCriteria           = trim($_GET['prNo']);
            $prDateCriteria         = trim($_GET['prDate']);
            $prDateEndCriteria      = trim($_GET['prDateEnd']);
            $requesterNameCriteria  = trim($_GET['requester']);
            $deliveryDateCriteria   = trim($_GET['deliveryDate']);  
            $prChargedCriteria      = trim($_GET['prCharged']); 
            $supplierCdCriteria     = trim($_GET['supplierCd']); 
            $supplierNameCriteria   = trim($_GET['supplierName']); 
            $itemNameCriteria       = trim($_GET['itemName']); 
            $itemTypeCriteria       = trim($_GET['itemType']); 
            $expNoCriteria          = trim($_GET['expNo']); 
            $invNoCriteria          = trim($_GET['invNo']);
            $rfiNoCriteria          = trim($_GET['rfiNo']); 
            $itemStatusCriteria     = trim($_GET['itemStatus']); 
            $prStatusCriteria       = trim($_GET['prStatus']); 
            $roStatusCriteria       = $_GET['roSts'];
            $itemCodeCriteria       = trim($_GET['itemCode']);
           
            if($prNoCriteria || $prDateCriteria || $prDateEndCriteria
                    || $requesterNameCriteria || $deliveryDateCriteria || $prChargedCriteria
                    || $supplierCdCriteria || $supplierNameCriteria || $itemNameCriteria || $itemStatusCriteria || $itemTypeCriteria
                    || $expNoCriteria || $invNoCriteria || $rfiNoCriteria 
                    || $itemStatusCriteria || $prStatusCriteria || $roStatusCriteria || $itemCodeCriteria){
                
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
                    $prListNo = 0;
                }
                else
                {
                    $prListNo = ($max_per_page * ($mpage - 1));
                }
                
                $wherePrSearch      = array();
                if($prNoCriteria){
                    $wherePrSearch[] = "EPS_T_PR_HEADER.PR_NO = '".$prNoCriteria."'";
                }
                /*if($prDateCriteria){
                    $wherePrSearch[] = "EPS_T_PR_HEADER.ISSUED_DATE = '".encodeDate($prDateCriteria)."'";
                }*/
                if($prDateCriteria && !$prDateEndCriteria){
                    $wherePrSearch[] = "EPS_T_PR_HEADER.ISSUED_DATE = '".encodeDate($prDateCriteria)."'";
                }
                if(!$prDateCriteria && $prDateEndCriteria ){
                    $wherePrSearch[] = "EPS_T_PR_HEADER.ISSUED_DATE = '".encodeDate($prDateEndCriteria)."'";
                }
                if($prDateCriteria && $prDateEndCriteria){
                    $wherePrSearch[] = "EPS_T_PR_HEADER.ISSUED_DATE >= '".encodeDate($prDateCriteria)."'
                                        and EPS_T_PR_HEADER.ISSUED_DATE <= '".encodeDate($prDateEndCriteria)."'";
                }
                if($requesterNameCriteria){
                    $wherePrSearch[] = "EPS_M_EMPLOYEE_2.NAMA1 LIKE '".$requesterNameCriteria."%'";
                }
                if($deliveryDateCriteria){
                    $wherePrSearch[] = "EPS_T_PR_DETAIL.DELIVERY_DATE = '".encodeDate($deliveryDateCriteria)."'";
                }
                if($prChargedCriteria){
                    $wherePrSearch[] = "EPS_T_PR_HEADER.CHARGED_BU_CD = '".$prChargedCriteria."'";
                }
                if($supplierCdCriteria){
                    $wherePrSearch[] = "EPS_T_PR_DETAIL.SUPPLIER_CD = '".$supplierCdCriteria."'";
                }
                if($supplierNameCriteria){
                    $wherePrSearch[] = "EPS_T_PR_DETAIL.SUPPLIER_NAME LIKE '".$supplierNameCriteria."%'";
                }
                if($itemNameCriteria){
                    $wherePrSearch[] = "EPS_T_PR_DETAIL.ITEM_NAME LIKE '%".$itemNameCriteria."%'";
                }
                if($itemStatusCriteria){
                    $wherePrSearch[] = "EPS_T_TRANSFER.ITEM_STATUS = '".$itemStatusCriteria."'";
                }
                if($itemTypeCriteria){
                    $wherePrSearch[] = "EPS_T_PR_DETAIL.ITEM_TYPE_CD = '".$itemTypeCriteria."'";
                }
                if($expNoCriteria){
                    $wherePrSearch[] = "EPS_T_PR_DETAIL.ACCOUNT_NO = '".$expNoCriteria."'";
                }
                if($invNoCriteria){
                    $wherePrSearch[] = "EPS_T_PR_DETAIL.ACCOUNT_NO = '".$invNoCriteria."'";
                }
                if($rfiNoCriteria){
                    $wherePrSearch[] = "EPS_T_PR_DETAIL.RFI_NO = '".$rfiNoCriteria."'";
                }
                if($prStatusCriteria){
                    $wherePrSearch[] = "EPS_T_PR_HEADER.PR_STATUS = '".$prStatusCriteria."'";
                }
                if($roStatusCriteria){
                    $wherePrSearch[] = "EPS_T_PO_DETAIL.RO_STATUS = '".$roStatusCriteria."'";
                }
                if($itemCodeCriteria){
                    $wherePrSearch[] = "EPS_T_PR_DETAIL.ITEM_CD LIKE '%".$itemCodeCriteria."%'";
                }
                
                /**
                 * SELECT COUNT EPS_T_PR
                 **/
                $query_count_t_pr = "select     
                           count (*) as COUNT_PR
                        from         
                            EPS_T_PR_DETAIL 
                        inner join
                            EPS_T_PR_HEADER 
                        on 
                            EPS_T_PR_DETAIL.PR_NO = EPS_T_PR_HEADER.PR_NO 
                        left join
                            EPS_M_EMPLOYEE 
                        on 
                            EPS_T_PR_HEADER.APPROVER = EPS_M_EMPLOYEE.NPK
                        left join
                            EPS_M_APP_STATUS 
                        on 
                            EPS_T_PR_HEADER.PR_STATUS = EPS_M_APP_STATUS.APP_STATUS_CD
                        inner join
                            EPS_M_EMPLOYEE EPS_M_EMPLOYEE_2
                        on 
                            EPS_T_PR_HEADER.REQUESTER = EPS_M_EMPLOYEE_2.NPK
                        left join
                            EPS_T_TRANSFER 
                        on 
                            EPS_T_PR_DETAIL.PR_NO = EPS_T_TRANSFER.PR_NO 
                            and EPS_T_PR_DETAIL.ITEM_NAME = EPS_T_TRANSFER.ITEM_NAME 
                        left join
                            EPS_M_APP_STATUS EPS_M_APP_STATUS_1 
                        on 
                            EPS_T_TRANSFER.ITEM_STATUS = EPS_M_APP_STATUS_1.APP_STATUS_CD
                        left join
                            EPS_M_ITEM_TYPE
                        on
                            EPS_T_PR_DETAIL.ITEM_TYPE_CD = EPS_M_ITEM_TYPE.ITEM_TYPE_CD
                        left join
                            EPS_T_PO_DETAIL 
                        on 
                            EPS_T_PO_DETAIL.REF_TRANSFER_ID = EPS_T_TRANSFER.TRANSFER_ID ";
                if(count($wherePrSearch)) {
                    $query_count_t_pr .= "where " . implode(' and ', $wherePrSearch);
                }
                $sql_count_t_pr = $conn->query($query_count_t_pr);
                $row_count_t_pr = $sql_count_t_pr->fetch(PDO::FETCH_ASSOC);
                $countPr    = $row_count_t_pr['COUNT_PR'];
                
                if($start > $countPr)
                {
                    $lgenap     = $start - $max_per_page;
                    $max_per_pages = $countPr - $lgenap;
                    $start      = $countPr;
                }
                else
                {
                    $max_per_pages = $max_per_page;
                }
                
                /**
                 * SELECT EPS_T_PR
                 **/
                $query_select_pr = "
                        select 
                            * 
                        from 
                            (select top  $max_per_pages  
                                * 
                            from      
                                (select top $start 
                                    EPS_T_PR_DETAIL.PR_NO
                                    ,EPS_T_PR_HEADER.ISSUED_DATE
                                    ,EPS_M_EMPLOYEE_2.NAMA1 as REQUESTER_NAME
                                    ,EPS_T_PR_DETAIL.ITEM_CD
                                    ,EPS_T_PR_DETAIL.ITEM_NAME
                                    ,substring(EPS_T_PR_DETAIL.DELIVERY_DATE, 7, 2) + '/' + substring(EPS_T_PR_DETAIL.DELIVERY_DATE, 5, 2) + '/' + substring(EPS_T_PR_DETAIL.DELIVERY_DATE, 1, 4) as DELIVERY_DATE 
                                    ,EPS_T_PR_DETAIL.QTY
                                    ,EPS_T_PR_DETAIL.ITEM_PRICE
                                    ,EPS_T_PR_DETAIL.AMOUNT
                                    ,EPS_T_PR_DETAIL.CURRENCY_CD
                                    ,EPS_T_PR_DETAIL.ITEM_TYPE_CD
                                    ,EPS_T_PR_DETAIL.ACCOUNT_NO
                                    ,EPS_T_PR_DETAIL.RFI_NO
                                    ,EPS_T_PR_DETAIL.UNIT_CD
                                    ,EPS_T_PR_DETAIL.SUPPLIER_CD
                                    ,EPS_T_PR_DETAIL.SUPPLIER_NAME
                                    ,EPS_T_PR_HEADER.PR_STATUS as PR_STATUS
                                    ,EPS_M_APP_STATUS.APP_STATUS_NAME as PR_STATUS_NAME
                                    ,EPS_M_EMPLOYEE.NAMA1 as APPROVER_NAME
                                    ,EPS_T_TRANSFER.ITEM_STATUS
                                    ,EPS_T_TRANSFER.TRANSFER_ID
                                    ,EPS_M_APP_STATUS_1.APP_STATUS_NAME as ITEM_STATUS_NAME
                                    ,EPS_T_PR_HEADER.REQ_BU_CD 
                                    ,EPS_T_PR_HEADER.CHARGED_BU_CD
                                    ,EPS_M_ITEM_TYPE.ITEM_TYPE_ALIAS
                                    ,EPS_T_PR_HEADER.USERID
                                    ,case 
                                        when 
                                            CHARINDEX('.', EPS_T_PR_DETAIL.ITEM_NAME) - 1 > 0 
                                        then 
                                        case 
                                            when 
                                                ISNUMERIC(SUBSTRING(EPS_T_PR_DETAIL.ITEM_NAME, 1, CHARINDEX('.',EPS_T_PR_DETAIL.ITEM_NAME) - 1)) = 1 
                                            then 
                                                SUBSTRING(EPS_T_PR_DETAIL.ITEM_NAME, 1, CHARINDEX('.', EPS_T_PR_DETAIL.ITEM_NAME) - 1) 
                                        else 
                                            999 
                                        end 
                                    else 
                                        999 
                                    end 
                                        as INDEX_ITEM_NAME
                                    ,(select 
                                        count(*)
                                    from          
                                        EPS_T_PO_DETAIL
                                    where      
                                        EPS_T_PO_DETAIL.REF_TRANSFER_ID = EPS_T_TRANSFER.TRANSFER_ID) as COUNT_PO
                                    ,(select 
                                        count(*)
                                    from          
                                        EPS_T_RO_DETAIL
                                    where      
                                        EPS_T_RO_DETAIL.REF_TRANSFER_ID = EPS_T_TRANSFER.TRANSFER_ID) as COUNT_RO
                                from         
                                    EPS_T_PR_DETAIL 
                                inner join
                                    EPS_T_PR_HEADER 
                                on 
                                    EPS_T_PR_DETAIL.PR_NO = EPS_T_PR_HEADER.PR_NO 
                                left join
                                    EPS_M_EMPLOYEE 
                                on 
                                    EPS_T_PR_HEADER.APPROVER = EPS_M_EMPLOYEE.NPK
                                left join
                                    EPS_M_APP_STATUS 
                                on 
                                    EPS_T_PR_HEADER.PR_STATUS = EPS_M_APP_STATUS.APP_STATUS_CD
                                inner join
                                    EPS_M_EMPLOYEE EPS_M_EMPLOYEE_2
                                on 
                                    EPS_T_PR_HEADER.REQUESTER = EPS_M_EMPLOYEE_2.NPK
                                left join
                                    EPS_T_TRANSFER 
                                on 
                                    EPS_T_PR_DETAIL.PR_NO = EPS_T_TRANSFER.PR_NO 
                                    and EPS_T_PR_DETAIL.ITEM_NAME = EPS_T_TRANSFER.ITEM_NAME 
                                left join
                                    EPS_M_APP_STATUS EPS_M_APP_STATUS_1 
                                on 
                                    EPS_T_TRANSFER.ITEM_STATUS = EPS_M_APP_STATUS_1.APP_STATUS_CD
                                left join
                                    EPS_M_ITEM_TYPE
                                on
                                    EPS_T_PR_DETAIL.ITEM_TYPE_CD = EPS_M_ITEM_TYPE.ITEM_TYPE_CD
								left join
									EPS_T_PO_DETAIL 
								on 
									EPS_T_PO_DETAIL.REF_TRANSFER_ID = EPS_T_TRANSFER.TRANSFER_ID  ";
		if(count($wherePrSearch)) {
                    $query_select_pr .= "where " . implode(' and ', $wherePrSearch);
		}
                $query_select_pr .= " 
                                 order by 
                                    EPS_T_PR_HEADER.ISSUED_DATE desc
                                    ,EPS_T_PR_HEADER.PR_NO desc
                                    ,INDEX_ITEM_NAME desc )
                                 as T1
                            order by
                                T1.ISSUED_DATE asc
                                ,T1.PR_NO asc
                                ,T1.INDEX_ITEM_NAME asc )
                            as T2
                        order by
                            T2.ISSUED_DATE desc
                            ,T2.PR_NO desc
                            ,T2.INDEX_ITEM_NAME ";
                $sql_select_pr = $conn->query($query_select_pr);
                
                $row_select_pr = $sql_select_pr->fetch(PDO::FETCH_ASSOC);
				
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
        <script src="../js/epr/WEPR090.js"></script>
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
                $( "#prDate" ).datepicker({
                    dateFormat: 'dd/mm/yy'
                });
                $( "#prDateEnd" ).datepicker({
                    dateFormat: 'dd/mm/yy'
                });
                $( "#deliveryDate" ).datepicker({
                    dateFormat: 'dd/mm/yy'
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
                    <a href="WEPR001.php">
                        <i class="icon-list-ul"></i><span>PR List</span> 
                    </a> 
                </li>
                <li>
                    <a href="WEPR013.php">
                        <i class="icon-th"></i><span>PR Waiting</span> 
                    </a> 
                </li>
                <li>
                    <a href="../epr/WEPR002.php">
                        <i class="icon-pencil"></i><span>Create New PR</span> 
                    </a>
                </li>
                <li>
                    <a href="../epr/WEPR007.php">
                        <i class="icon-upload"></i><span>Upload PR</span> 
                    </a>
                </li>
                <li class="active">
                    <a href="WEPR090.php">
                        <i class="icon-search"></i><span>PR Search</span> 
                    </a>
                </li>
                <?php
                if($sRoleId == 'ROLE_03' || $sRoleId == 'ROLE_08')
                {
                ?>
                <li>
                    <a href="WEPR091.php">
                        <i class="icon-search"></i><span>PO Search</span> 
                    </a>
                </li>
                <?php 
                }
                ?>
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
                    <!---------------------------------- Hidden Parameter --------------------------------->
                    
                    <!---------------------------------- Message --------------------------------->
                    <div class="alert" id="mandatory-msg-1" style="display: none">
                        <strong>Mandatory!</strong> Please fill the search criteria.
                    </div>
                    <?php
                    if($prNoCriteria || $prDateCriteria || $prDateEndCriteria
                        || $requesterNameCriteria || $deliveryDateCriteria || $prChargedCriteria
                        || $supplierCdCriteria || $supplierNameCriteria || $itemNameCriteria || $itemStatusCriteria || $itemTypeCriteria
                        || $expNoCriteria || $invNoCriteria || $rfiNoCriteria || $prStatusCriteria || $roStatusCriteria || $itemCodeCriteria)
                    {
                        if(!$row_select_pr)
                        {
                    ?>
                    <div class="alert" id="mandatory-msg-2">
                        <strong>Data not found!</strong> No results match with your search.
                    </div>
                    <?php    
                        }
                    }
                    ?>
                    
                    <!----- PR Item ---->
                    <div class="widget ">
                        <div class="widget-header">
                            <i class="icon-search"></i>
                            <h3>Search</h3>
                        </div>
                        <div class="widget-content">
                            <form id="WEPR090Form">
                                <div class="control-group">	
                                    <table class="table-non-bordered">
                                        <tr>
                                            <td>
                                                <label class="control-label" for="prNo">PR No: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" id="prNo" name="prNo" maxlength="10" value="<?php echo $prNoCriteria;?>" />
                                                </div>
                                            </td>
                                            <td colspan="2">
                                                <label class="control-label" for="prDate">PR Date: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" id="prDate" name="prDate" maxlength="10" value="<?php echo $prDateCriteria;?>" />
                                                    -
                                                    <input type="text" class="span2" id="prDateEnd" name="prDateEnd" maxlength="10" value="<?php echo $prDateEndCriteria;?>" />
                                                </div>
                                            </td>
                                            <td colspan="2">
                                                <label class="control-label" for="prDate">Charged BU: </label>
                                                <div class="controls">
                                                    <select id="prCharged" class="span4" name="prCharged">
                                                        <option value=""></option>
                                                    <?php
                                                        $query_select_m_bunit = "select 
                                                                    KDBU as BU_CD
                                                                    ,NMBU1 as BU_NAME
                                                                    ,KDBU + '- ' + NMBU1 as BU_CD_NAME
                                                                  from 
                                                                    EPS_M_TBUNIT 
                                                                  where
                                                                    KDBU like 'T%'
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
                                            <td>
                                                <label class="control-label" for="requester">Due Date: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" id="deliveryDate" name="deliveryDate" maxlength="10" value="<?php echo $deliveryDateCriteria;?>" />
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                                <label class="control-label" for="supplierCd">Supplier Name: </label>
                                                <div class="controls">
                                                    <!--<select id="supplierCd" class="full-width-input" name="supplierCd">
                                                        <option value=""></option>
                                                    <?php
                                                        $query_select_m_supplier = "select 
                                                                    SUPPLIER_CD 
                                                                    ,SUPPLIER_NAME
                                                                    ,CURRENCY_CD
                                                                from 
                                                                    EPS_M_SUPPLIER";
                                                        $sql_select_m_supplier = $conn->query($query_select_m_supplier);
                                                        while($row_select_m_supplier = $sql_select_m_supplier->fetch(PDO::FETCH_ASSOC)){
                                                            $supplierCdSelect     = $row_select_m_supplier['SUPPLIER_CD'];
                                                            $supplierNameSelect   = $row_select_m_supplier['SUPPLIER_NAME'];
                                                            $currencyCdSelect     = $row_select_m_supplier['CURRENCY_CD'];
                                                    ?>
                                                        <option value="<?php echo $supplierCdSelect;?>" <?php if($supplierCdCriteria == $supplierCdSelect) echo "selected"; ?>><?php echo $supplierCdSelect.' [ '.$currencyCdSelect.' ]'.' - '.$supplierNameSelect;?></option>
                                                    <?php         
                                                        }
                                                    ?>
                                                    </select>-->
                                                    <input type="text" class="span6" id="supplierName" name="supplierName" maxlength="20" value="<?php echo $supplierNameCriteria;?>" />
                                                </div>
                                            </td>
                                            <td colspan="3">
                                                <label class="control-label" for="itemName">Item Name: </label>
                                                <div class="controls">
                                                    <input type="text" class="span6" id="itemName" name="itemName" value="<?php echo $itemNameCriteria?>" />
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="control-label" for="itemType">Type: </label>
                                                <div class="controls">
                                                    <select id="itemType" class="span2" name="itemType">
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
                                                    <select id="expNo" class="span4" name="expNo">
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
                                                    <select id="invNo" class="span4" name="invNo">
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
                                                    <input type="text" name="rfiNo" id="rfiNo" class="span2" value="<?php echo $rfiNoCriteria;?>" maxlength="6" />
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="control-label" for="requester">PR Requester: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" id="requester" name="requester" maxlength="15" value="<?php echo $requesterNameCriteria;?>" />
                                                </div>
                                            </td>
                                            <td colspan="2">
                                                <div class="controls">
                                                    <label class="control-label" for="prStatus">PR Status: </label>
                                                <div class="controls">
                                                    <select id="prStatus" class="span4" name="prStatus">
                                                        <option value=""></option>
                                                    <?php
                                                        $query_select_m_pr_status = "select 
                                                                    APP_STATUS_CD
                                                                    ,APP_STATUS_NAME
                                                                  from 
                                                                    EPS_M_APP_STATUS
                                                                  where
                                                                    APP_TYPE = 'PR'
                                                                  order by
                                                                    APP_STATUS_NAME";
                                                        $sql_select_m_pr_status = $conn->query($query_select_m_pr_status);
                                                        while($row_select_m_pr_status = $sql_select_m_pr_status->fetch(PDO::FETCH_ASSOC)){
                                                            $prStsCd       = $row_select_m_pr_status['APP_STATUS_CD'];
                                                            $prStsName   = $row_select_m_pr_status['APP_STATUS_NAME'];
                                                    ?>
                                                        <option value="<?php echo $prStsCd;?>" <?php if($prStatusCriteria == $prStsCd) echo "selected"; ?>><?php echo $prStsName;?></option>
                                                    <?php         
                                                        }
                                                    ?>
                                                    </select>
                                                </div>
                                                </div>
                                            </td>
                                            <td colspan="2">
                                                <!--<label class="control-label" for="poStatus">PO Status: </label>
                                                <div class="controls">
                                                    <select id="poStatus" class="span4" name="poStatus">
                                                        <option value=""></option>
                                                    <?php
                                                        $query_select_m_app_status = "select 
                                                                                        APP_STATUS_CD
                                                                                        ,APP_STATUS_NAME
                                                                                    from 
                                                                                        EPS_M_APP_STATUS
                                                                                    where
                                                                                        APP_STATUS_CD = '1210'
                                                                                        or APP_STATUS_CD = '1220'
                                                                                        or APP_STATUS_CD = '1230'
                                                                                        or APP_STATUS_CD = '1240'
                                                                                        or APP_STATUS_CD = '1250'
                                                                                        or APP_STATUS_CD = '1280'
                                                                                        or APP_STATUS_CD = '1290'
                                                                                        or APP_STATUS_CD = '1330'
                                                                                        or APP_STATUS_CD = '1340'
                                                                                    order by
                                                                                        APP_STATUS_NAME";
                                                        $sql_select_m_app_status = $conn->query($query_select_m_app_status);
                                                        while($row_select_m_app_status = $sql_select_m_app_status->fetch(PDO::FETCH_ASSOC)){
                                                            $appStsCd       = $row_select_m_app_status['APP_STATUS_CD'];
                                                            $appStsName   = $row_select_m_app_status['APP_STATUS_NAME'];
                                                    ?>
                                                        <option value="<?php echo $appStsCd;?>" <?php if($itemStatusCriteria == $appStsCd) echo "selected"; ?>><?php echo $appStsName;?></option>
                                                    <?php         
                                                        }
                                                    ?>
                                                    </select>
                                                </div>-->
												
                                                <label class="control-label" for="itemSts">Item Status: </label>
                                                <div class="controls">
                                                    <select id="itemStatus" class="span4" name="itemStatus">
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
                                            <td>
                                                <label class="control-label" for="roSts">Receiving Status: </label>
                                                <div class="controls">
                                                    <select id="roSts" class="span2" name="roSts">
                                                        <option value=""></option>
                                                    <?php
                                                        $query = "select 
                                                                    APP_STATUS_CD 
                                                                    ,APP_STATUS_NAME
                                                                from 
                                                                    EPS_M_APP_STATUS
                                                                where
                                                                    APP_STATUS_CD = '1310'
                                                                    or APP_STATUS_CD = '1320'
                                                                order by
                                                                    APP_STATUS_NAME ";
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
                                        <tr>
                                            <td>
                                                <label class="control-label" for="itemCode">Item Code: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" id="itemCode" name="itemCode" maxlength="20" value="<?php echo $itemCodeCriteria;?>" />
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
                    if($row_select_pr)
                    {
                    ?>
                        <!----- PR Item List ---->
                        <div class="widget widget-table action-table">
                            <div class="widget-header"> 
                                <i class="icon-search"></i>
                                <h3>PR Item Information</h3>
                            </div>
                            <div class="widget-content">
                                <table class="table table-striped table-bordered scroll">
                                    <thead>
                                        <tr>
                                            <th colspan="13" style="text-align: left">
                                                <!--<a href="../db/REPORT/EXCEL/PR.php?prNo=<?php echo trim($prNoCriteria);?>
                                                        &prDate=<?php echo trim($prDateCriteria);?>&requester=<?php echo $requesterNameCriteria;?>
                                                        &deliveryDate=<?php echo $deliveryDateCriteria;?>&prCharged=<?php echo $prChargedCriteria;?>
                                                        &supplierCd=<?php echo $supplierCdCriteria?>&supplierName=<?php echo $supplierNameCriteria;?>
                                                        &itemName=<?php echo $itemNameCriteria;?>
                                                        &itemType=<?php echo $itemTypeCriteria?>&expNo=<?php echo $expNoCriteria;?>
                                                        &rfiNo=<?php echo $rfiNoCriteria?>&invNo=<?php echo $invNoCriteria;?>
                                                        &prStatus=<?php echo $prStatusCriteria?>&itemStatus=<?php echo $itemStatusCriteria;?>"  
                                                   class="btn btn-small btn-linkedin-alt">
                                                    Download
                                                    <i class="btn-icon-only icon-download-alt"> </i>
                                                </a>-->
												<a href="../db/REPORT/PR_SEARCH.php?prNo=<?php echo trim($prNoCriteria);?>
                                                        &prDate=<?php echo $prDateCriteria;?>&prDateEnd=<?php echo $prDateEndCriteria;?>
                                                        &requester=<?php echo $requesterNameCriteria;?>
                                                        &deliveryDate=<?php echo $deliveryDateCriteria;?>&prCharged=<?php echo $prChargedCriteria;?>
                                                        &supplierCd=<?php echo $supplierCdCriteria?>&supplierName=<?php echo $supplierNameCriteria;?>
                                                        &itemName=<?php echo $itemNameCriteria;?>&itemStatus=<?php echo $itemStatusCriteria;?>
                                                        &itemType=<?php echo $itemTypeCriteria?>&expNo=<?php echo $expNoCriteria;?>
                                                        &rfiNo=<?php echo $rfiNoCriteria?>&invNo=<?php echo $invNoCriteria;?>
                                                        &prStatus=<?php echo $prStatusCriteria?>&roSts=<?php echo $roStatusCriteria;?>&itemCode=<?php echo $itemCodeCriteria;?>" class="btn btn-small btn-linkedin-alt" target="_blank">
                                                    Download
                                                    <i class="btn-icon-only icon-download-alt"> </i>
												</a>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th rowspan="2">NO</th>
                                            <th rowspan="2" style="display: none">REF TRANSFER ID</th>
                                            <th rowspan="2">ITEM<br>STATUS</th>
                                            <th colspan="9">PR</th>
                                            <th rowspan="2">PO & RO</th>
                                            <th rowspan="2">SUPPLIER</th>
                                        </tr>
                                        <tr>
                                            <th rowspan="2">PR NO</th>
                                            <th rowspan="2">STATUS</th>
                                            <th rowspan="2">CHARGED<br>BU</th>
                                            <th>ITEM CODE</th>
                                            <th>NAME</th>
                                            <th>QTY</th>
                                            <th>TYPE</th>
                                            <th>EXP<br>/RFI</th>
                                            <th>DUE<br>DATE</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        $sql_select_pr_2 = $conn->query($query_select_pr);
                                        while($row_select_pr_2 = $sql_select_pr_2->fetch(PDO::FETCH_ASSOC))
                                        {
                                            $prNo           = $row_select_pr_2['PR_NO'];
                                            $prStatusName   = $row_select_pr_2['PR_STATUS_NAME'];
                                            $itemCode       = $row_select_pr_2['ITEM_CD'];
                                            $itemName       = $row_select_pr_2['ITEM_NAME'];
                                            $qty            = $row_select_pr_2['QTY'];
                                            $deliveryDate   = $row_select_pr_2['DELIVERY_DATE'];
                                            $supplierName   = $row_select_pr_2['SUPPLIER_NAME'];
                                            $prCharged      = $row_select_pr_2['CHARGED_BU_CD'];
                                            $itemTypeCd     = $row_select_pr_2['ITEM_TYPE_CD'];
                                            $itemTypeAlias  = $row_select_pr_2['ITEM_TYPE_ALIAS'];
                                            $accountNo      = $row_select_pr_2['ACCOUNT_NO'];
                                            $rfiNo          = $row_select_pr_2['RFI_NO'];
                                            $itemStatusName = $row_select_pr_2['ITEM_STATUS_NAME'];
                                            $transferId     = $row_select_pr_2['TRANSFER_ID'];
                                            $itemStatus     = $row_select_pr_2['ITEM_STATUS'];
                                            $countPo        = $row_select_pr_2['COUNT_PO'];
                                            $countRo        = $row_select_pr_2['COUNT_RO'];
                                            $prUserID       = $row_select_pr_2['USERID'];
                                            $prListNo++;
                                        ?>
                                        <tr>   
                                            <td class="td-number">
                                                <?php echo $prListNo;?>.
                                            </td>
                                            <td style="display: none">
                                                <?php echo $transferId;?>
                                            </td>
                                            <td>
                                                <?php echo $itemStatusName;?>
                                            </td>
                                            <td>
                                                <?php
                                                if($prUserID == $sUserId)
                                                {
                                                ?>
                                                <a href="../db/Redirect/PR_Screen.php?paramPrNo=<?php echo $prNo;?>" class="faq-list">
                                                    <b><?php echo $prNo;?></b>
                                                </a> 
                                                <?php    
                                                }
                                                else
                                                {
                                                ?>
                                                <a href="#" class="faq-list pr-no">
                                                <?
                                                    echo $prNo;
                                                ?>
                                                </a>    
                                                <?php
                                                }
                                                ?> 
                                            </td>
                                            <td>
                                                <?php echo $prStatusName;?>
                                            </td>
                                            <td>
                                                <?php echo $prCharged;?>
                                            </td>
                                            <td>
                                                <?php echo $itemCode;?>
                                            </td>
                                            <td>
                                                <?php echo $itemName;?>
                                            </td>
                                            <td class="td-align-right">
                                                <?php echo $qty;?>
                                            </td>
                                            <td>
                                                <?php echo $itemTypeAlias;?>
                                            </td>
                                            <td>
                                            <?php
                                                if($itemTypeCd == '1' || $itemTypeCd == '3' || $itemTypeCd == '4')
                                                {
                                                    if(strlen($accountNo) == 1)
                                                    {
                                                        echo '0'.$accountNo;
                                                    }
                                                    else
                                                    {
                                                        echo $accountNo; 
                                                    }
                                                }
                                                if($itemTypeCd == '2')
                                                {
                                                    echo $rfiNo;
                                                }
                                            ?>
                                            </td>
                                            <td>
                                                <?php echo $deliveryDate;?>
                                            </td>
                                            <td>
                                                <?php echo $supplierName;?>
                                            </td>
                                            <td>
                                            <?php
                                                if($countPo > 0)
                                                {
                                            ?>
                                                    <a href="#" class="btn btn-facebook-alt" id="window-po">
                                                        <i class="btn-icon-only icon-paste "> </i>
                                                    </a>
                                            <?php         
                                                }
                                            ?>
                                            </td>
                                            <!--<td>
                                            <?php
                                                if($countRo > 0)
                                                {
                                            ?>
                                                    <a href="#" class="btn btn-google-alt" id="window-ro">
                                                        <i class="btn-icon-only icon-external-link "> </i>
                                                    </a>
                                            <?php         
                                                }
                                            ?>
                                            </td>-->
                                        </tr>
                                    <?php
                                        }
                                    ?>   
                                        <tr>
                                            <th colspan="19">
                                            <?php
                                                if($countPr > $max_per_page)
                                                {    
                                                    echo "<div id=\"pagination\" >";
                                                    if ($query_select_pr != "")
                                                    {
                                                        $fld = "prNo=$prNoCriteria";
                                                        $fld .= "&prDate=$prDateCriteria";
                                                        $fld .= "&prDateEnd=$prDateEndCriteria";
                                                        $fld .= "&requester=$requesterNameCriteria";
                                                        $fld .= "&deliveryDate=$deliveryDateCriteria";
                                                        $fld .= "&prCharged=$prChargedCriteria";
                                                        $fld .= "&supplierCd=$supplierCdCriteria";
                                                        $fld .= "&supplierName=$supplierNameCriteria";
                                                        $fld .= "&itemName=$itemNameCriteria";
                                                        $fld .= "&itemType=$itemTypeCriteria";
                                                        $fld .= "&expNo=$expNoCriteria";
                                                        $fld .= "&invNo=$invNoCriteria";
                                                        $fld .= "&rfiNo=$rfiNoCriteria";
                                                        $fld .= "&itemStatus=$itemStatusCriteria";
                                                        $fld .= "&prStatus=$prStatusCriteria";
                                                        $fld .= "&roSts=$roStatusCriteria";
                                                        $fld .= "&itemCode=$itemCodeCriteria";
                                                        $fld .= "&mpage";
                                                    }
                                                    else
                                                    {
                                                            $fld = "mpage";
                                                    }
                                                    paging($query_select_pr,$max_per_page,$num,$mpage,$fld,$countPr);
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

<div id="dialog-confirm-logout" title="Confirm" style="display: none;"></div>
<div id="dialog-po-table" title="PO Information" style="display: none;">
    <div class="widget">
        <div class='widget-content'>
            <div class='control-group' id="dialog-control-group-po">
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
    </body>
</html>
