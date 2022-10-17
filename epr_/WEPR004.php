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
        $sPrScreen  = $_SESSION['prScreen'];
        $sPrStatus  = $_SESSION['prStatusSession'];
        
        if($sPrScreen == 'ApprovalPrScreen')
        { 
            $prNoSession    = $_SESSION['prNoSession'];
            $paramPrNo      = $_GET['prNo'];
            
            if($prNoSession == $paramPrNo)
            {
                if($sPrStatus == '1020' )
                {
					/**
                     * SELECT EPS_M_PR_SPECIAL_APPROVER
                     */
                    $query_select_m_pr_special_approver = "select 
                                                                NPK
                                                            from
                                                                EPS_M_PR_SPECIAL_APPROVER
                                                            where 
                                                                SPECIAL_APPROVER_CD = '001'";
                    $sql_select_m_pr_special_approver = $conn->query($query_select_m_pr_special_approver);
                    $row_select_m_pr_special_approver = $sql_select_m_pr_special_approver->fetch(PDO::FETCH_ASSOC);
                    $npkISApprover = $row_select_m_pr_special_approver['NPK'];
					
                    $wherePrHeader = array();
                    if($paramPrNo){
                        $wherePrHeader[] = "EPS_T_PR_HEADER.PR_NO = '".$paramPrNo."'";
                    }
                    $query_select_t_pr_header = "select 
                                                    EPS_T_PR_HEADER.PR_NO
                                                    ,substring(EPS_T_PR_HEADER.ISSUED_DATE,7,2)+'/'+substring(EPS_T_PR_HEADER.ISSUED_DATE,5,2)+'/'+substring(EPS_T_PR_HEADER.ISSUED_DATE,1,4) as ISSUED_DATE
                                                    ,EPS_T_PR_HEADER.BU_CD
                                                    ,EPS_T_PR_HEADER.REQUESTER
                                                    ,EPS_M_EMPLOYEE.NAMA1 as REQUESTER_NAME
                                                    ,EPS_T_PR_HEADER.SECTION_CD
                                                    ,EPS_T_PR_HEADER.PLANT_CD
                                                    ,EPS_M_PLANT.PLANT_NAME
                                                    ,EPS_T_PR_HEADER.COMPANY_CD
                                                    ,EPS_M_COMPANY.COMPANY_NAME
                                                    ,EPS_T_PR_HEADER.EXT_NO
                                                    ,EPS_T_PR_HEADER.REQ_BU_CD
                                                    ,EPS_T_PR_HEADER.REQ_BU_CD + '- ' + EPS_M_TBUNIT_2.NMBU1 AS REQ_BU_CD_NAME
                                                    ,EPS_T_PR_HEADER.CHARGED_BU_CD
                                                    ,EPS_T_PR_HEADER.CHARGED_BU_CD + '- ' + EPS_M_TBUNIT.NMBU1 AS CHARGED_BU_CD_NAME
                                                    ,EPS_T_PR_HEADER.SPECIAL_TYPE_ID
                                                    ,EPS_T_PR_HEADER.PURPOSE
                                                    ,EPS_T_PR_HEADER.UPDATE_DATE
                                                    ,EPS_T_PR_HEADER.PR_STATUS
                                                    ,EPS_M_APP_STATUS.APP_STATUS_NAME as PR_STATUS_NAME
                                                    ,EPS_T_PR_HEADER.PROC_IN_CHARGE
                                                    ,convert(VARCHAR(24), EPS_T_PR_HEADER.PROC_ACCEPT_DATE, 120) as PROC_ACCEPT_DATE
                                                    ,EPS_T_PR_HEADER.PROC_REMARK
                                                    ,EPS_M_EMPLOYEE_2.NAMA1 as PROC_IN_CHARGE_NAME
                                                    ,EPS_T_PR_APPROVER.APPROVER_NO
                                                from
                                                    EPS_T_PR_HEADER
                                                inner join
                                                    EPS_M_EMPLOYEE
                                                on 
                                                    EPS_T_PR_HEADER.REQUESTER = EPS_M_EMPLOYEE.NPK 
                                                left join
                                                    EPS_M_EMPLOYEE EPS_M_EMPLOYEE_2
                                                on
                                                    EPS_T_PR_HEADER.PROC_IN_CHARGE = EPS_M_EMPLOYEE_2.NPK 
                                                left join
                                                    EPS_M_PLANT
                                                on
                                                    EPS_T_PR_HEADER.PLANT_CD = EPS_M_PLANT.PLANT_CD
                                                left join
                                                    EPS_M_COMPANY
                                                on
                                                    EPS_T_PR_HEADER.COMPANY_CD = EPS_M_COMPANY.COMPANY_CD
                                                left join
                                                    EPS_M_TBUNIT 
                                                on 
                                                    EPS_T_PR_HEADER.CHARGED_BU_CD = EPS_M_TBUNIT.KDBU
                                                left join
                                                    EPS_M_TBUNIT EPS_M_TBUNIT_2
                                                on
                                                    EPS_T_PR_HEADER.REQ_BU_CD = EPS_M_TBUNIT_2.KDBU
                                                left join
                                                    EPS_M_APP_STATUS
                                                on
                                                    EPS_T_PR_HEADER.PR_STATUS = EPS_M_APP_STATUS.APP_STATUS_CD
                                                left join
                                                    EPS_T_PR_APPROVER 
                                                on 
                                                    EPS_T_PR_APPROVER.NPK = EPS_T_PR_HEADER.APPROVER 
                                                    and EPS_T_PR_HEADER.PR_NO = EPS_T_PR_APPROVER.PR_NO ";
                    if(count($wherePrHeader)) {
                        $query_select_t_pr_header .= "where " . implode('and ', $wherePrHeader);
                    }
                    $sql_select_t_pr_header = $conn->query($query_select_t_pr_header);
                    while($row_select_t_pr_header = $sql_select_t_pr_header->fetch(PDO::FETCH_ASSOC)){
                        $prNo           = $row_select_t_pr_header['PR_NO'];
                        $issuedDate     = $row_select_t_pr_header['ISSUED_DATE'];
                        $prStatus       = $row_select_t_pr_header['PR_STATUS'];
                        $prStatusName   = $row_select_t_pr_header['PR_STATUS_NAME'];
                        $prBuCd         = $row_select_t_pr_header['BU_CD'];
                        $requester      = $row_select_t_pr_header['REQUESTER'];
                        $requesterName  = $row_select_t_pr_header['REQUESTER_NAME'];
                        $sectionCd      = $row_select_t_pr_header['SECTION_CD'];
                        $plant          = $row_select_t_pr_header['PLANT_CD'];
                        $plantName      = $row_select_t_pr_header['PLANT_NAME'];
                        $company        = $row_select_t_pr_header['COMPANY_CD'];
                        $companyName    = $row_select_t_pr_header['COMPANY_NAME'];
                        $extNo          = $row_select_t_pr_header['EXT_NO'];
                        $prIssuer       = $row_select_t_pr_header['REQ_BU_CD'];
                        $prIssuerName   = $row_select_t_pr_header['REQ_BU_CD_NAME'];
                        $prCharged      = $row_select_t_pr_header['CHARGED_BU_CD'];
                        $prChargedName  = $row_select_t_pr_header['CHARGED_BU_CD_NAME'];
                        $specialType    = $row_select_t_pr_header['SPECIAL_TYPE_ID'];
                        $procInChargeName= $row_select_t_pr_header['PROC_IN_CHARGE_NAME'];
                        $procAcceptDate = $row_select_t_pr_header['PROC_ACCEPT_DATE'];
                        $procRemark     = $row_select_t_pr_header['PROC_REMARK'];
                        $currenctAppNo  = $row_select_t_pr_header['APPROVER_NO'];
                        $specialTypeCd  = $specialType;

                        if($specialType == 'IT'){
                            $specialType = 'IT Equipment';
                        }
                        if($specialType == 'NIT'){
                            $specialType = 'Non IT Equipment';
                        }
                        $purpose        = $row_select_t_pr_header['PURPOSE'];
                        $attachmentCount= $row_select_t_pr_header['ATTACHMENT_COUNT'];
                        $updateDate     = $row_select_t_pr_header['UPDATE_DATE'];
                        $currentDate = date(Ymd);
                        $currentDate = date("d/m/Y", strtotime($currentDate) );
                        
                        if(strlen(trim($procAcceptDate)) != 0){
                            date_default_timezone_set('Asia/Jakarta');
                            $procAcceptDate   = date("d/m/Y H:i:s A", strtotime($procAcceptDate));
                        }
                        if(substr($prNo,0,1) == 'H')
                        {
                            $buUserId = substr($prNo,0,5);
                        }
                        else
                        {
                            $buUserId = substr($prNo,0,4);
                            $buUserId = $buUserId." ";
                        }
                    }
                    $query_select_count_t_pr_app = "select 
                                                        count(*) as COUNT_APPROVER
                                                    from 
                                                        EPS_T_PR_APPROVER 
                                                    where
                                                        PR_NO = '$paramPrNo'";
                    $sql_select_count_t_pr_app = $conn->query($query_select_count_t_pr_app);
                    $row_select_count_t_pr_app = $sql_select_count_t_pr_app->fetch(PDO::FETCH_ASSOC);
                    $countPrApprover = $row_select_count_t_pr_app['COUNT_APPROVER'];

                    /** Check last approver status */
                    $query_select_last_t_pr_app = "select 
                                                        APPROVAL_STATUS
                                                        ,NPK
                                                        ,BU_CD
                                                    from
                                                        EPS_T_PR_APPROVER
                                                    where
                                                        PR_NO = '$paramPrNo'
                                                        and APPROVER_NO = '$countPrApprover'";
                    $sql_select_last_t_pr_app = $conn->query($query_select_last_t_pr_app);
                    $row_select_last_t_pr_app = $sql_select_last_t_pr_app->fetch(PDO::FETCH_ASSOC);
                    $lastApprovalStatus = $row_select_last_t_pr_app['APPROVAL_STATUS'];
                    $lastApprover       = $row_select_last_t_pr_app['NPK'];
                    $lastApproverBu     = $row_select_last_t_pr_app['BU_CD'];
                    
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
        <script language="javascript"> document.location="../ecom/WCOM011.php"; </script> 
    <?php
    }
}
else
{	
?>
    <script language="javascript"> document.location="../ecom/WCOM010.php"; </script> 
<?php
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
        <script src="../js/epr/WEPR004.js"></script>
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
                <li class="active">
                    <a href="../epr_/WEPR001.php">
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
                <li>
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
                        <!---------------------------------- PR Header --------------------------------->
                        <div class="widget ">
                            <div class="widget-header">
                                <i class="icon-file"></i>
                                <h3>PR Information</h3>
                            </div>
                            <div class="widget-content">
                                <div class="control-group">	
                                    <table class="table-non-bordered">
                                        <tr>
                                            <td>
                                                <label class="control-label" for="prNo">PR No: </label>
                                                <div class="controls">
                                                    <input type="text" id="prNo" class="span2" value="<?php echo $prNo;?>" readonly />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="prIssuer">Issuer BU: </label>
                                                <div class="controls">
                                                    <input type="text" class="span4" value="<?php echo $prIssuerName;?>" readonly />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="prType">Category: </label>
                                                <div class="controls">
                                                    <input type="text" class="span3" value="<?php echo $specialType;?>" readonly />
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="control-label" for="prDate">PR Date: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" value="<?php echo $issuedDate;?>" readonly />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="prCharged">Charged BU: </label>
                                                <div class="controls">
                                                    <select id="prCharged" class="span4">
                                                    <?php
                                                        $query = "select 
                                                                    KDBU as BU_CD
                                                                    ,NMBU1 as BU_NAME
                                                                    ,KDBU + '- ' + NMBU1 as BU_CD_NAME
                                                                  from 
                                                                    EPS_M_TBUNIT 
                                                                  where
                                                                    KDBU like 'T%'
                                                                    and KDAKT = 'A'
                                                                  order by 
                                                                    KDBU";
                                                        $sql = $conn->query($query);
                                                        while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                                                            $buCd   = $row['BU_CD'];
                                                            $buCdName = $row['BU_CD_NAME'];
                                                    ?>
                                                        <option value="<?php echo $buCd;?>" <?php if($prCharged == $buCd) echo "selected"; ?>><?php echo $buCdName;?></option>
                                                    <?php         
                                                        }
                                                    ?>
                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="prIssuer">PR Status: </label>
                                                <div class="controls">
                                                    <input type="text" class="span4" value="<?php echo $prStatusName;?>" readonly />
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                                <label class="control-label" for="purpose">Purpose: </label>
                                                <div class="controls">
                                                     <textarea rows="2" id="purpose" readonly ><?php echo $purpose;?></textarea>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div><!-- /control-group -->
                            </div><!-- /widget-content -->
                        </div><!-- /widget -->
                        
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
                                            <td style="display: none;">
                                                <label class="control-label" for="npk">NPK: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" value="<?php echo $requester;?>" readonly />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="requesterName">Name: </label>
                                                <div class="controls">
                                                    <input type="text" class="span4" value="<?php echo $requesterName;?>" readonly />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="plant">BU Code: </label>
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
                                                <label class="control-label" for="plant">Company: </label>
                                                <div class="controls">
                                                    <input type="text" class="span3" value="<?php echo $companyName;?>" readonly />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="extNo">Ext: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" value="<?php echo $extNo;?>" readonly />
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div><!-- /control-group -->
                            </div><!-- /widget-content -->
                        </div><!-- /widget -->
                         <!---------------------------------- PR Detail --------------------------------->
                        <div class="widget widget-table action-table">
                            <div class="widget-header"> <i class="icon-th-list"></i>
                                <h3>PR Detail</h3>
                            </div>
                            <div class="widget-content">
                                <table class="table table-striped table-bordered" id="prItemTable">
                                    <thead>
                                        <tr>
                                            <th colspan="18" style="text-align: right; color: #3F85F5">
                                                ** Item Status => WAI : PR Item Waiting || REJ : PR Item Rejected || WPO : Waiting PO Number
                                                || OUT : Outstanding PO || OPN : Open Receiving || CLS : Closed Receiving
                                            </th>
                                        </tr>
                                        <!--<tr>
                                            <th class="td-actions" colspan="16" style="text-align: left">
                                                <a href="#" class="btn btn-small btn-warning" id="window-add">
                                                    <i class="btn-icon-only icon-plus"> </i>ADD
                                                </a>
                                            </th>
                                        </tr>-->
                                        <tr>
                                            <th rowspan="2">NO</th>
                                            <th rowspan="2" style="width: 65px;">OPTIONS</th>
                                            <th rowspan="2" style="display: none">PR NO</th>
                                            <th colspan="3">ITEM</th>
                                            <th rowspan="2">DUE DATE</th>
                                            <th rowspan="2" style="display: none">TYPE</th>
                                            <th rowspan="2">EXP</th>
                                            <th rowspan="2">RFI</th>
                                            <th rowspan="2">UM</th>
                                            <th rowspan="2">QTY</th>
                                            <th rowspan="2">STOCK</th>
                                            <th rowspan="2">OP</th>
                                            <th colspan="2">USER REFERENCE (ESTIMATE)</th>
                                            <th rowspan="2">AMOUNT</th>
                                            <th rowspan="2">REMARK</th>
                                            <th rowspan="2" style="display: none">SEQ ITEM</th>
                                            <th rowspan="2">CUR</th>
                                            <th rowspan="2">FILE</th>
                                        </tr>
                                        <tr>
                                            <th style="display: none">STATUS CD</th>
                                            <th>STATUS</th>
                                            <th>CODE</th>
                                            <th>NAME</th>
                                            <th style="display: none">REF. NAME</th>
                                            <th>UNIT PRICE</td>
                                            <th>SUPPLIER</td>
                                            <th style="display: none">SUPPLIER CD</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $wherePrDetail = array();
                                    if($prNo){
                                        $wherePrDetail[] = "EPS_T_PR_DETAIL.PR_NO = '".$prNo."'";
                                    }
                                    $itemNo = 0;
                                    $query_select_t_pr_detail = "select 
                                                EPS_T_TRANSFER.ITEM_STATUS
                                                ,EPS_M_APP_STATUS.APP_STATUS_ALIAS as ITEM_STATUS_NAME
                                                ,EPS_T_PR_DETAIL.ITEM_CD
                                                ,EPS_T_PR_DETAIL.ITEM_NAME
                                                ,substring(EPS_T_PR_DETAIL.DELIVERY_DATE, 7, 2) 
                                                + '/' + substring(EPS_T_PR_DETAIL.DELIVERY_DATE, 5, 2) 
                                                + '/' + substring(EPS_T_PR_DETAIL.DELIVERY_DATE, 1, 4) as DELIVERY_DATE 
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
                                                ,EPS_T_PR_DETAIL.REMARK
                                                ,EPS_T_PR_DETAIL.ITEM_STATUS
                                                ,EPS_T_PR_DETAIL.REASON_TO_REJECT_ITEM
                                                ,EPS_T_PR_DETAIL.REJECT_ITEM_BY
                                                ,EPS_M_EMPLOYEE.NAMA1 as REJECT_ITEM_NAME_BY
                                                ,(select count (*)
                                                from          
                                                    EPS_T_PR_ATTACHMENT
                                                where      
                                                    EPS_T_PR_HEADER.PR_NO = EPS_T_PR_ATTACHMENT.PR_NO 
                                                and 
													(REPLACE(REPLACE(REPLACE(EPS_T_PR_DETAIL.ITEM_NAME, CHAR(13), ''), CHAR(9), ''), ' ', '') = REPLACE(EPS_T_PR_ATTACHMENT.ITEM_NAME, ' ', ''))) as ATTACHMENT_ITEM_COUNT
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
                                                    , Stock AS STOCK
						    , OrderPoint AS OP
                                            from 
                                                EPS_T_PR_DETAIL 
                                            inner join
                                                EPS_T_PR_HEADER
                                            on 
                                                EPS_T_PR_DETAIL.PR_NO = EPS_T_PR_HEADER.PR_NO
                                            left join
                                                EPS_M_EMPLOYEE
                                            on 
                                                EPS_T_PR_DETAIL.REJECT_ITEM_BY = EPS_M_EMPLOYEE.NPK
                                            left join
                                                EPS_T_TRANSFER 
                                            on 
                                                EPS_T_PR_DETAIL.PR_NO = EPS_T_TRANSFER.PR_NO
                                                and EPS_T_TRANSFER.ITEM_NAME = EPS_T_PR_DETAIL.ITEM_NAME
                                            left join
                                                EPS_M_APP_STATUS 
                                            on 
                                                EPS_T_TRANSFER.ITEM_STATUS = EPS_M_APP_STATUS.APP_STATUS_CD
                                                LEFT OUTER JOIN IMS_V_ITEM 
                                            on SUBSTRING(EPS_T_PR_DETAIL.ITEM_CD, 1 ,9) = IMS_V_ITEM.ItemCode  and SUBSTRING(EPS_T_PR_DETAIL.ITEM_CD,14,2) = IMS_V_ITEM.statusbarang ";
                                    if(count($wherePrDetail)) {
                                        $query_select_t_pr_detail .= "where " . implode('and ', $wherePrDetail);
                                    }
                                    $query_select_t_pr_detail .= " order by INDEX_ITEM_NAME, EPS_T_PR_DETAIL.ITEM_NAME";
                                    
                                    $sql_select_t_pr_detail = $conn->query($query_select_t_pr_detail);
                                    while($row_select_t_pr_detail = $sql_select_t_pr_detail->fetch(PDO::FETCH_ASSOC)){
                                        $itemStatus     = $row_select_t_pr_detail['ITEM_STATUS'];
                                        $itemStatusName = $row_select_t_pr_detail['ITEM_STATUS_NAME'];
                                        $itemCd         = $row_select_t_pr_detail['ITEM_CD'];
                                        $itemName       = stripslashes($row_select_t_pr_detail['ITEM_NAME']);
                                        $deliveryDate   = $row_select_t_pr_detail['DELIVERY_DATE'];
                                        $qty            = $row_select_t_pr_detail['QTY'];
                                        $stock            = $row_select_t_pr_detail['STOCK'];
                                        $op            = $row_select_t_pr_detail['OP'];
                                        $itemPrice      = $row_select_t_pr_detail['ITEM_PRICE'];
                                        $amount         = $row_select_t_pr_detail['AMOUNT'];
                                        $currencyCd     = $row_select_t_pr_detail['CURRENCY_CD'];
                                        $itemType       = $row_select_t_pr_detail['ITEM_TYPE_CD'];
                                        $accountCd      = $row_select_t_pr_detail['ACCOUNT_NO'];
                                        $rfiNo          = $row_select_t_pr_detail['RFI_NO'];
                                        $unitCd         = $row_select_t_pr_detail['UNIT_CD'];
                                        $supplierCd     = $row_select_t_pr_detail['SUPPLIER_CD'];
                                        $supplierName   = $row_select_t_pr_detail['SUPPLIER_NAME'];
                                        $remark         = $row_select_t_pr_detail['REMARK'];
                                        $itemStatus     = $row_select_t_pr_detail['ITEM_STATUS'];
                                        $reasonToReject = $row_select_t_pr_detail['REASON_TO_REJECT_ITEM'];
                                        $rejectItemBy   = $row_select_t_pr_detail['REJECT_ITEM_BY'];
                                        $rejectItemNameBy= $row_select_t_pr_detail['REJECT_ITEM_NAME_BY'];
                                        $attachmentItemCount = $row_select_t_pr_detail['ATTACHMENT_ITEM_COUNT'];
                                        
                                        $prTotal = $prTotal + $amount;
                                        $itemNo++;
                                        if(strlen($accountCd) == 1)
                                        {
                                            $accountCd = '0'.$accountCd;
                                        }
                                    ?>
                                        <tr>
                                            <td class="td-align-right">
                                                <?php echo $itemNo;?>.
                                            </td>
                                            <td>
                                                <!--<a href="#" class="btn btn-small btn-success" id="window-edit">
                                                    <i class="btn-icon-only icon-edit "> </i>
                                                </a>
                                                <?php
                                                //if(count($_SESSION['poDetail']) > 1 && $countItemDel == 0){
                                                ?>
                                                    <a href="#" class="btn btn-small btn-danger" id="window-delete">
                                                        <i class="btn-icon-only icon-remove"> </i>
                                                    </a>
                                                <?php
                                                //}
                                                ?>-->
                                            </td>
                                            <td>
                                                <?php echo $itemStatusName;?>
                                            </td>
                                            <td>
                                                <?php echo $itemCd;?>
                                            </td>
                                            <td>
                                                <?php echo $itemName;?>
                                            </td>
                                            <td>
                                                <?php echo $deliveryDate;?>
                                            </td>
                                            <td>
                                                <?php 
                                                if($itemType == '1' || $itemType == '3' || $itemType == '4')
                                                {
                                                    echo $accountCd;
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php 
                                                if($itemType == '2')
                                                {
                                                    echo $rfiNo;
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php echo $unitCd;?>
                                            </td>
                                            <td class="td-align-right">
                                                <?php echo $qty;?>
                                            </td>
                                            <td class="td-align-right">
                                                <?php echo $stock;?>
                                            </td>
                                            <td class="td-align-right">
                                                <?php echo $op;?>
                                            </td>
                                            <td class="td-align-right">
                                                <?php echo number_format($itemPrice);?>
                                            </td>
                                            <td>
                                                <?php echo $supplierName;?>
                                            </td>
                                            <td class="td-align-right">
                                                <?php echo number_format($amount);?>
                                            </td>
                                            <td>
                                                <?php echo $remark;?>
                                            </td>
                                            <td>
                                                <?php echo $currencyCd;?>
                                            </td>
                                            <td>
                                                <?php
                                                if($attachmentItemCount > 0)
                                                {
                                                ?>
                                                    <a href="#" class="btn btn-small btn-info" id="window-attach">
                                                        <i class="btn-icon-only icon-paper-clip "> </i>
                                                    </a>
                                                <?php    
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                        <tr>
                                            <th colspan="14" class="td-align-right">
                                                Total
                                            </th>
                                            <th style="text-align: right">
                                            <?php
                                                // Get total of amount item
                                                echo number_format($prTotal);
                                            ?>
                                            </th>
                                            <th colspan="3"></th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                         
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
                                            <th rowspan="2" width="120px">STATUS</th>
                                            <th colspan="2">DATE</th>
                                            <th rowspan="2" width="300px">REMARK</th>
                                        </tr>
                                        <tr>
                                            <th width="130px">APPROVAL</td>
                                            <th width="130px">BYPASS</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        $query_select_t_pr_approver = "select 
                                                        EPS_T_PR_APPROVER.PR_NO
                                                        ,EPS_T_PR_APPROVER.BU_CD
                                                        ,EPS_T_PR_APPROVER.APPROVER_NO
                                                        ,EPS_T_PR_APPROVER.NPK
                                                        ,EPS_M_EMPLOYEE.NAMA1 as APPROVER_NAME
                                                        ,EPS_T_PR_APPROVER.APPROVAL_STATUS
                                                        ,EPS_M_APPROVAL_STATUS.APPROVAL_STATUS_NAME
                                                        ,convert(VARCHAR(24), EPS_T_PR_APPROVER.APPROVAL_DATE, 120) as APPROVAL_DATE
                                                        ,EPS_T_PR_APPROVER.APPROVAL_REMARK
                                                        ,convert(VARCHAR(24), EPS_T_PR_APPROVER.DATE_OF_BYPASS, 120) as DATE_OF_BYPASS
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
                                                        EPS_T_PR_APPROVER.PR_NO = EPS_T_PR_HEADER.PR_NO";
                                        if($lastApproverBu != '3300 ' && $lastApproverBu != '3941 ' && $specialTypeCd == 'IT')
                                        {
                                             $query_select_t_pr_approver .="
                                                    where 
                                                        EPS_T_PR_APPROVER.PR_NO = '".$paramPrNo."'
                                                    and 
                                                        EPS_T_PR_APPROVER.APPROVER_NO < (select max(APPROVER_NO) from EPS_T_PR_APPROVER where PR_NO = '".$paramPrNo."')";
                                           
                                        }
                                        else
                                        {
                                            $query_select_t_pr_approver .= "
                                                    where 
                                                        EPS_T_PR_APPROVER.PR_NO ='".$paramPrNo."'";
                                        }
                                        $query_select_t_pr_approver .= "order by
                                                        EPS_T_PR_APPROVER.APPROVER_NO 
                                                    asc";
                                        $sql_select_t_pr_approver = $conn->query($query_select_t_pr_approver);
                                        while($row_select_t_pr_approver = $sql_select_t_pr_approver->fetch(PDO::FETCH_ASSOC)){
                                            $approverNo         = $row_select_t_pr_approver['APPROVER_NO'];
                                            $buCd               = $row_select_t_pr_approver['BU_CD'];
                                            $npk                = $row_select_t_pr_approver['NPK'];
                                            $approverName       = stripslashes($row_select_t_pr_approver['APPROVER_NAME']);
                                            $approvalStatus     = $row_select_t_pr_approver['APPROVAL_STATUS'];
                                            $approvalStatusName = $row_select_t_pr_approver['APPROVAL_STATUS_NAME'];
                                            $approvalDate       = $row_select_t_pr_approver['APPROVAL_DATE'];
                                            $approvalRemark     = $row_select_t_pr_approver['APPROVAL_REMARK'];
                                            $dateByPass         = $row_select_t_pr_approver['DATE_OF_BYPASS'];
                                            $specialType        = $row_select_t_pr_approver['SPECIAL_TYPE_ID'];
                                            
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
                        
                        <?php
                        $lastApprovalStatus = $row_select_last_t_pr_app['APPROVAL_STATUS'];
                        $lastApprover       = $row_select_last_t_pr_app['NPK'];
                        $lastApproverBu     = $row_select_last_t_pr_app['BU_CD'];
                        
                        if($lastApproverBu != '3300 ' && $lastApproverBu != '3941 ' && $specialTypeCd == 'IT')
                        {
                        ?>   
                        <!---------------------------------- IS PR Approver --------------------------------->
                        <div class="widget widget-table action-table">
                            <div class="widget-header"> 
                                <i class="icon-list-ol "></i>
                                <h3>IS Approver</h3>
                            </div>
                            <div class="widget-content">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th rowspan="2">NO</th>
                                            <th rowspan="2">NAME</th>
                                            <th rowspan="2">STATUS</th>
                                            <th colspan="2">DATE</th>
                                            <th rowspan="2" width="300px">REMARK</th>
                                        </tr>
                                        <tr>
                                            <th width="130px">APPROVAL</td>
                                            <th width="130px">BYPASS</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $query_select_special_app = "select top 1
                                                                    EPS_T_PR_APPROVER.PR_NO
                                                                    ,EPS_T_PR_APPROVER.BU_CD
                                                                    ,EPS_T_PR_APPROVER.APPROVER_NO
                                                                    ,EPS_T_PR_APPROVER.NPK
                                                                    ,EPS_M_EMPLOYEE.NAMA1 as APPROVER_NAME
                                                                    ,EPS_T_PR_APPROVER.APPROVAL_STATUS
                                                                    ,EPS_M_APPROVAL_STATUS.APPROVAL_STATUS_NAME
                                                                    ,convert(VARCHAR(24), EPS_T_PR_APPROVER.APPROVAL_DATE, 120) as APPROVAL_DATE
                                                                    ,EPS_T_PR_APPROVER.APPROVAL_REMARK
                                                                    ,convert(VARCHAR(24), EPS_T_PR_APPROVER.DATE_OF_BYPASS, 120) as DATE_OF_BYPASS
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
                                                                    EPS_T_PR_APPROVER.PR_NO ='".$paramPrNo."'
                                                                and 
                                                                    EPS_T_PR_APPROVER.NPK = (select NPK from EPS_M_PR_SPECIAL_APPROVER where SPECIAL_APPROVER_CD = '001')      
                                                                order by
                                                                    EPS_T_PR_APPROVER.APPROVER_NO 
                                                                desc";
                                    $sql_select_special_app = $conn->query($query_select_special_app);
                                    while($row_select_special_app = $sql_select_special_app->fetch(PDO::FETCH_ASSOC)){
                                        $approverNo                 = $row_select_special_app['APPROVER_NO'];
                                        $buCd                       = $row_select_special_app['BU_CD'];
                                        $npk                        = $row_select_special_app['NPK'];
                                        $specialApproverName        = stripslashes($row_select_special_app['APPROVER_NAME']);
                                        $specialApprovalStatus      = $row_select_special_app['APPROVAL_STATUS'];
                                        $specialApprovalStatusName  = $row_select_special_app['APPROVAL_STATUS_NAME'];
                                        $specialApprovalDate        = $row_select_special_app['APPROVAL_DATE'];
                                        $specialApprovalRemark      = $row_select_special_app['APPROVAL_REMARK'];
                                        $specialDateByPass          = $row_select_special_app['DATE_OF_BYPASS'];
                                        $specialType                = $row_select_special_app['SPECIAL_TYPE_ID'];
                                        
                                        if(strlen(trim($specialApprovalDate)) != 0){
                                            date_default_timezone_set('Asia/Jakarta');
                                            $specialApprovalDate   = date("d/m/Y H:i:s A", strtotime($specialApprovalDate));
                                        }
                                          
                                    ?>
                                        <tr>
                                            <td class="td-number">
                                                1.
                                            </td>
                                            <td>
                                                <?php echo $specialApproverName; ?>
                                            </td>
                                            <td width="120px">
                                                <?php echo $specialApprovalStatusName; ?>
                                            </td>
                                            <td>
                                                <?php echo $specialApprovalDate; ?>
                                            </td>
                                            <td>
                                                <?php echo $specialDateByPass; ?>
                                            </td>
                                            <td>
                                                <?php echo $specialApprovalRemark; ?>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php
                        }
                        ?>
                        
                        <!---------------------------------- Approver Information --------------------------------->
                        <div class="widget" id="approver-information">
                            <div class="widget-header">
                                <i class="icon-info-sign"></i>
                                <h3>Approver Information</h3>
                            </div>
                            <div class="widget-content">
                                <div class="control-group">	
                                    <table class="table-non-bordered">
                                        <tr>
                                            <td>
                                                <label class="control-label" for="remarkApprover">Comment:</label>
                                                <div class="controls">
                                                    <input type="text" class="full-width-input" id="remarkApprover" maxlength="200" />
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                    
                                    <!---------------------------------- Message --------------------------------->
                                    <div class="alert" id="mandatory-msg-1" style="display: none">
                                        <strong>Mandatory!</strong> Please fill "Comment" for Reject PR.
                                    </div>
                                    <div class="alert" id="undefined-msg" style="display: none">
                                        <strong>Undefined Error!</strong> System Error occurs. Please report to system administrator.
                                    </div>
                                    <div class="alert" id="session-msg" style="display: none">
                                        <strong>Session expired!</strong> Session timeout or user has not login. Please login again.
                                    </div>
                                    <div class="alert alert-success" id="success-msg" style="display: none">
                                        <strong>Success!</strong> Approve PR finished.
                                    </div>
                                    <div class="alert alert-success" id="bypass-it-msg" style="display: none">
                                        <strong>Success!</strong> Bypass IT Approval PR finished.
                                    </div>
                                    <div class="alert alert-success" id="reject-msg" style="display: none">
                                        <strong>Success!</strong> Reject PR finished.
                                    </div>
                    
                                    <!---------------------------------- Button --------------------------------->
                                    <div class="form-actions">
                                        <button class="btn btn-primary" id="btn-approve">Approve</button> 
                                        <button class="btn btn-danger" id="btn-reject">Reject</button> 
										<?php
                                        if($sUserId == $npkISApprover)
                                        {
                                        ?>
                                            <button class="btn btn-warning" id="btn-bypass-it">Bypass IT Approval</button> 
                                        <?    
                                        }
                                        ?>
                                        <button class="btn" id="btn-back">Back</button>
                                    </div> <!-- /form-actions -->
                                </div><!-- /control-group -->
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
                    &copy; 2018 PT. TD Automotive Compressor Indonesia. All rights reserved. 
                </div> <!-- /span12 -->	
            </div> <!-- /row -->
	</div> <!-- /container -->		
    </div> <!-- /footer-inner -->	
</div> <!-- /footer -->

<div id="dialog-form" title="Add PR Item" style="display: none;">
    <div class="alert" id="dialog-mandatory-msg-1" style="display: none;">
        <strong>Mandatory!</strong> Please fill all the field.
    </div>
    <div class="alert" id="dialog-mandatory-msg-2" style="display: none">
        <strong>Range Error!</strong> Please input value > 0.
    </div>
    <div class="alert" id="dialog-mandatory-msg-3" style="display: none">
        <strong>Existence Error!</strong> Selected supplier does not match with this PO.
    </div>
    <div class="alert" id="dialog-mandatory-msg-4" style="display: none">
        <strong>Range Error!</strong> 0 is not allowed as first character for Qty.
    </div>
    <div class="alert" id="dialog-mandatory-msg-5" style="display: none">
        <strong>Range Error!</strong> Please input qty less than qty PO.
    </div>
    <div class="alert" id="dialog-undefined-msg" style="display: none">
        <strong>Undefined Error!</strong> System Error occurs. Please report to system administrator.
    </div>
    
    <div class="widget ">
        <form id="WEPO006Form-dialog">
            <div class="widget-content">
                <div class="control-group">
                    <input type="hidden" id="seqItem" />
                    <input type="hidden" id="refTransferIdHidden" />
                    <input type="hidden" id="currencyCdHidden" />
                    <input type="hidden" id="totalSupplierHidden" />
                    <input type="hidden" id="initialQty" />
                    
                    <table class="table-non-bordered">
                        <tr>
                            <td>
                                <label class="control-label" for="itemCd">Item Type: </label>
                                <div class="dialog-controls">
                                    <select id="itemType" class="span2">
                                    <?php
                                        $whereItemTypeMaster = array(); 
                                        $query = "select 
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
                                            $query .= "where " . implode('or ', $whereItemTypeMaster);
                                        }
                                        $query .= "order by ITEM_TYPE_CD";
                                        $sql = $conn->query($query);
                                        while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                                                            $itemType       = $row['ITEM_TYPE_CD'];
                                                            $itemTypeAlias  = $row['ITEM_TYPE_ALIAS'];
                                    ?>
                                        <option value="<?php echo $itemType;?>"><?php echo $itemTypeAlias;?></option>
                                    <?php         
                                        }
                                    ?>
                                    </select>
                                </div>
                            </td>
                            <td>
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
                                                $accountName = $row['ACCOUNT_NAME'];
                                        ?>
                                        <option value="<?php echo $accountNo;?>" <?php if($accountNo == $newAccountNo) echo "selected"; ?>><?php echo $accountNo.'-'.$accountCd.'-'.$accountName;?></option>
                                        <?php         
                                            }
                                        ?>
                                    </select>
                                </div>
                            </td>
                            <td>
                                <label class="control-label" for="deliveryDate">Due Date: </label>
                                <div class="controls">
                                    <input type="text" class="span2" id="deliveryDate" />
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class="control-label" for="itemCd">Item Code: </label>
                                <div class="dialog-controls">
                                    <input type="text" id="itemCd" class="span2" readonly />
                                </div>
                            </td>
                            <td colspan="2">
                                <label class="control-label" for="itemName">Item Name: </label>
                                <div class="controls">
                                    <input type="text" id="itemName" class="full-width-input" maxlength="200" />
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class="control-label" for="supplierCd">Supplier Code: </label>
                                <div class="controls">
                                    <input type="text" id="supplierCd" class="span2" readonly />
                                </div>
                            </td>
                            <td colspan="2">
                                <label class="control-label" for="supplierName">Supplier Name: </label>
                                <div class="controls">
                                    <select id="supplierName" class="full-width-input-select">
                                        <option value=""></option>
                                        <?php
                                            $query2 = "select 
                                                        SUPPLIER_CD 
                                                        ,SUPPLIER_NAME
                                                        ,CURRENCY_CD
                                                      from 
                                                        EPS_M_SUPPLIER
                                                      where 
                                                        CURRENCY_CD = 'IDR'
                                                      order by
                                                        SUPPLIER_NAME";
                                            $sql2 = $conn->query($query2);
                                            while($row2 = $sql2->fetch(PDO::FETCH_ASSOC)){
                                                $supplierCd2   = $row2['SUPPLIER_CD'];
                                                $supplierName2 = $row2['SUPPLIER_NAME'];
                                                $currencyCd2 = $row2['CURRENCY_CD'];
                                        ?>
                                        <option value="<?php echo $supplierCd2;?>"><?php echo $supplierCd2.' [ '.$currencyCd2.' ]'.' - '.$supplierName2;?></option>
                                        <?php         
                                            }
                                        ?>
                                    </select>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class="control-label" for="um">U M: </label>
                                <div class="dialog-controls">
                                    <select id="um" class="full-width-input">
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
                                        <option value="<?php echo $unitCd;?>"><?php echo $unitCd;?></option>
                                    <?php         
                                        }
                                    ?>
                                    </select>
                                </div>
                            </td>
                            <td>
                                <label class="control-label" for="price">Price: </label>
                                <div class="dialog-controls">
                                    <input type="text" id="price" class="full-width-input input-align-right" maxlength="16" />
                                </div>
                            </td>
                            <td>
                                <label class="control-label" for="qty">Qty: </label>
                                <div class="dialog-controls">
                                    <input type="text" id="qty" class="span2 input-align-right" maxlength="6" />
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <label class="control-label" for="price">Remark: </label>
                                <div class="dialog-controls">
                                    <input type="text" id="remark" class="full-width-input-select" maxlength="16" />
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
<div id="dialog-confirm-approve" title="Confirm" style="display: none;"></div>
<div id="dialog-confirm-reject" title="Confirm" style="display: none;"></div>
<div id="dialog-confirm-bypass-it" title="Confirm" style="display: none;"></div>
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
