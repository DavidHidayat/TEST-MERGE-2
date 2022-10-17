<?php session_start(); 
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Constant.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Common.php";
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
        $prScreen   = $_SESSION['prScreen'];
        $sPrStatus  = $_SESSION['prStatus'];
        $sProcInCharge  = $_SESSION['procInCharge'];
        $sProcPrUpdateDate  = $_SESSION['procPrUpdateDate'];
        
        if($sRoleId == 'ROLE_02' || $sRoleId == 'ROLE_03' || $sRoleId == 'ROLE_04' || $sRoleId == 'ROLE_06' || $sRoleId == 'ROLE_07' || $sRoleId == 'ROLE_09' || $sRoleId == 'ROLE_10' || $sRoleId == 'ROLE_11')
        {
			if($sProcInCharge == $sUserId || $sRoleId == 'ROLE_03')
            {
				if($prScreen == 'DetailPrScreen')
            {
                $prNoSession = $_SESSION['prNoSession'];
                $paramPrNo   = $_GET['paramPrNo'];

                if($prNoSession == $paramPrNo)
                {
                    if($sPrStatus == '1030')
                    {
                        $wherePrHeader = array();
                        if($paramPrNo){
                            $wherePrHeader[] = "EPS_T_PR_HEADER.PR_NO = '".$paramPrNo."'";
                        }
                        $query = "select 
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
                                    ,EPS_M_APP_STATUS.APP_STATUS_NAME as PR_STATUS_NAME
                                    ,EPS_T_PR_HEADER.UPDATE_DATE
                                    ,(select count(*)
										from          
                                            EPS_T_PR_DETAIL
										where      
                                            PR_NO = '$paramPrNo' 
                                            and ITEM_STATUS = '1060') as ITEM_COUNT
                                from
                                    EPS_T_PR_HEADER
                                inner join
                                    EPS_M_EMPLOYEE
                                on 
                                    EPS_T_PR_HEADER.REQUESTER = EPS_M_EMPLOYEE.NPK 
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
                                    EPS_T_PR_HEADER.PR_STATUS = EPS_M_APP_STATUS.APP_STATUS_CD  ";
                        if(count($wherePrHeader)) {
                            $query .= "where " . implode('and ', $wherePrHeader);
                        }
                        $sql = $conn->query($query);
                        while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                            $prNo           = $row['PR_NO'];
                            $issuedDate     = $row['ISSUED_DATE'];
                            $prBuCd         = $row['BU_CD'];
                            $requester      = $row['REQUESTER'];
                            $requesterName  = $row['REQUESTER_NAME'];
                            $sectionCd      = $row['SECTION_CD'];
                            $plant          = $row['PLANT_CD'];
                            $plantName      = $row['PLANT_NAME'];
                            $company        = $row['COMPANY_CD'];
                            $companyName    = $row['COMPANY_NAME'];
                            $extNo          = $row['EXT_NO'];
                            $prIssuer       = $row['REQ_BU_CD'];
                            $prIssuerName   = $row['REQ_BU_CD_NAME'];
                            $prCharged      = $row['CHARGED_BU_CD'];
                            $prChargedName  = $row['CHARGED_BU_CD_NAME'];
                            $specialType    = $row['SPECIAL_TYPE_ID'];
                            if($specialType == 'IT'){
                                $specialType = 'IT Equipment';
                            }
                            if($specialType == 'NIT'){
                                $specialType = 'Non IT Equipment';
                            }
                            $purpose        = $row['PURPOSE'];
                            $attachmentCount= $row['ATTACHMENT_COUNT'];
                            $prStatusName   = $row['PR_STATUS_NAME'];
                            $itemCount      = $row['ITEM_COUNT'];
                            $currentDate = date('Ymd');
                            $currentDate = date("d/m/Y", strtotime($currentDate) );
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
        <script src="../js/epo/WEPO002.js"></script>
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
				var rowCount        = ($("#prItemTable > tbody >tr").length) - 1;
                for(var i = 0; i < rowCount; i++){
                    //$("input#getDeliveryDate"+i).css('background-color', '#ffffff');
                    $("input#getDeliveryDate"+i).datepicker({
                        dateFormat: 'dd/mm/yy',
                        defaultDate: "+1w",
                        minDate: dateToday,
                        maxDate: '+2Y',
                        autoClose: true,
                        beforeShowDay: noWeekendsOrHolidays
                    });
                }
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
                <li class="active">
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
                    <!-- <form> -->
                        <!---------------------------------- Hidden Parameter --------------------------------->
                        <input type="hidden" value="<?php echo $prCharged;?>" id="prCharged" readonly />
                        <input type="hidden" value="<?php echo $requester;?>" id="requester" readonly />
                        <input type="hidden" value="<?php echo $companyName;?>" readonly />
                        <input type="hidden" value="<?php echo $sProcPrUpdateDate;?>" id="procPrUpdateDate" readonly />
                        
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
                                                    <input type="text" id="prNo" class="span2" value="<?php echo $paramPrNo;?>" readonly />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="prIssuer">Issuer BU Code/Name: </label>
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
                                                <label class="control-label" for="newPrCharged">New Charged BU Code/Name: </label>
                                                <div class="controls">
                                                    <select id="newPrCharged" class="span4">
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
                                                        <option value="<?php echo $buCd;?>"><?php echo $buCdName;?></option>
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
                                                <label class="control-label" for="extNo">Ext : </label>
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
                                            <th width="130px">APPROVAL</th>
                                            <th width="130px">BYPASS</th>
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
                        </div><!-- widget widget-table action-table -->
                        
                        <!---------------------------------- PR Detail --------------------------------->
                        <div class="widget widget-table action-table">
                            <div class="widget-header"> <i class="icon-th-list"></i>
                                <h3>PR Detail</h3>
                            </div>
                            <div class="widget-content">
                                <table class="table table-striped table-bordered" id="prItemTable">
                                    <thead>
                                        <tr>
                                            <th colspan="16" style="text-align: right; color: #3F85F5">
                                                ** Item Status => WAI : PR Item Waiting || REJ : PR Item Rejected || WPO : Waiting PO Number
                                                || OUT : Outstanding PO 
                                            </th>
                                        </tr>
                                        <tr>
                                            <th rowspan="2">NO</th>
                                            <th rowspan="2" style="display: none">ACTION</th>
                                            <th rowspan="2" style="display: none">PR NO</th>
                                            <th colspan="3">ITEM</th>
                                            <th rowspan="2">DUE DATE</th>
                                            <th rowspan="2" style="display: none">TYPE</th>
                                            <th rowspan="2">RFI</th>
                                            <th rowspan="2">EXP</th>
                                            <th rowspan="2">UM</th>
                                            <th rowspan="2">QTY</th>
                                            <th colspan="2">USER REFERENCE (ESTIMATE)</th>
                                            <th rowspan="2">AMOUNT</th>
                                            <th rowspan="2">REMARK</th>
                                            <th rowspan="2" style="display: none">SEQ ITEM</th>
                                            <th rowspan="2">CUR</th>
                                            <th rowspan="2">CATG.</th>
                                            <th rowspan="2">USER<br>ATTACH</th>
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
                                    $itemNo = 0;
                                    $prTotal = 0;
                                    foreach ( $_SESSION['prDetail'] as $prDetail ) 
                                    {  
                                        $itemCdSession              = $prDetail['itemCd'];
                                        $itemNameSession            = $prDetail['itemName'];
                                        $remarkSession              = $prDetail['remark'];
                                        $deliveryDateSession        = $prDetail['deliveryDate'];
                                        $itemTypeSession            = $prDetail['itemType'];
                                        $rfiNoSession               = $prDetail['rfiNo'];
                                        $accountNoSession           = $prDetail['accountNo'];
                                        $supplierCdSession          = $prDetail['supplierCd'];
                                        $supplierNameSession        = $prDetail['supplierName'];
                                        $unitCdSession              = $prDetail['unitCd'];
                                        $qtySession                 = $prDetail['qty'];
                                        $itemPriceSession           = $prDetail['itemPrice'];
                                        $amountSession              = $prDetail['amount'];
                                        $currencyCdSession          = $prDetail['currencyCd'];
                                        $itemStsSession             = $prDetail['itemStatus'];
                                        $prNoSession                = $prDetail['prNo'];
                                        $refItemNameSession         = $prDetail['refItemName'];
                                        $seqItemSession             = $prDetail['seqItem'];
                                        $itemStatusAliasSession     = $prDetail['itemStatusAlias'];
                                        $attachmentItemCountSession = $prDetail['attachmentItemCount'];
                                        $itemCategory               = $prDetail['itemCategory'];
                                        $itemNo++;
                                        $prTotal = $prTotal + $amountSession;
                                    ?>
                                        <tr id="<?php echo $seqItemSession;?>">
                                            <td class="td-number">
                                                <?php echo $itemNo;?>.
                                            </td>
                                            <td class="td-actions" style="display: none">
                                                <a href="#" class="btn btn-small btn-success" id="window-edit">
                                                    <i class="btn-icon-only icon-edit "> </i>
                                                </a>
                                            </td>
                                            <td style="display: none">
                                                <?php echo $prNoSession;?>
                                            </td>
                                            <td style="display: none">
                                                <?php echo $itemStsSession;?>
                                            </td>
                                            <td>
                                                <!--
                                                <?php echo $itemStatusAliasSession;?>
                                                -->
												<select id="getItemStatus<?php echo $seqItemSession;?>" style="width: 60px">
                                                <?php
                                                    $query3 = "select 
                                                                APP_STATUS_CD 
                                                                ,APP_STATUS_NAME
                                                                ,APP_STATUS_ALIAS
                                                            from 
                                                                EPS_M_APP_STATUS
                                                            where 
                                                                APP_TYPE = 'PR_TO_PO' ";
                                                    if($itemCount == 1 && $supplierCdSession != 'SUP99' && $itemCdSession != '99')
                                                    {
                                                        $query3 .= "and APP_STATUS_CD in ('1110','1120','1130')";
                                                    }
                                                    else if ($itemCount > 1 && $supplierCdSession != 'SUP99' && $itemCdSession != '99')
													{
                                                        $query3 .= "and APP_STATUS_CD in ('1110','1120','1130','1140')";
                                                    }
                                                    else if($itemCount == 1 && ($supplierCdSession == 'SUP99' || $itemCdSession == '99')){
                                                        $query3 .= "and APP_STATUS_CD in ('1110','1120')";
                                                    }
                                                    else
                                                    {
                                                        $query3 .= "and APP_STATUS_CD in ('1110','1120','1140')";
                                                    }
                                                    if ($itemCategory!== 'Y') {
                                                        $query3 .= "and APP_STATUS_ALIAS NOT IN ('WPO')";
                                                    }
                                                    $sql3 = $conn->query($query3);
                                                    while($row3 = $sql3->fetch(PDO::FETCH_ASSOC)){
                                                        $appStsCd   = $row3['APP_STATUS_CD'];
                                                        $appStsName = $row3['APP_STATUS_NAME'];
                                                        $appStsAlias= $row3['APP_STATUS_ALIAS'];
                                                ?>
                                                    <option value="<?php echo $appStsCd;?>"><?php echo $appStsAlias;?></option>
                                                <?php         
                                                    }
                                                ?>                 
                                                </select>
                                            </td>
                                            <td>
                                                <?php echo $itemCdSession;?>
                                            </td>
                                            <td id="getItemName<?php echo $seqItemSession;?>">
                                                <?php echo $itemNameSession;?>
                                            </td>
                                            <td style="display: none">
                                                <?php echo $refItemNameSession;?>
                                            </td>
                                            <td>
                                                <?php
                                                if(strtotime(str_replace('/', '-', $deliveryDateSession)) < strtotime(str_replace('/', '-', $currentDate)))
                                                {
                                                ?>
                                                    <input type="text" style="width: 65px; background-color: #FCF8E3" value="<?php echo $deliveryDateSession;?>" id="getDeliveryDate<?php echo $seqItemSession;?>" readonly />
                                                <?php
                                                }
                                                else
                                                {
                                                ?>
                                                    <input type="text" style="width: 65px; background-color: #FFFFFF" value="<?php echo $deliveryDateSession;?>" id="getDeliveryDate<?php echo $seqItemSession;?>" readonly />
                                                <?    
                                                }
                                                ?>
                                                <!--
                                                <?php echo $deliveryDateSession;?>
                                                -->
                                                
                                            </td>
                                            <td style="display: none">
                                                <?php echo $itemTypeSession;?>
                                            </td>
                                            <td>
                                                <?php echo $rfiNoSession;?>
                                            </td>
                                            <td>
                                                <?php echo $accountNoSession;?>
                                            </td>
                                            <td>
                                                <?php echo $unitCdSession;?>
                                            </td>
                                            <td class="td-align-right">
                                                <?php echo $qtySession;?>
                                            </td>
                                            <td class="td-align-right">
                                                <?php echo number_format($itemPriceSession);?>
                                            </td>
                                            <td>
                                                <?php echo $supplierNameSession;?>
                                            </td>
                                            <td style="display: none">
                                                <?php echo $supplierCdSession;?>
                                            </td>
                                            <td class="td-align-right">
                                                <?php echo number_format($amountSession);?>
                                            </td>
                                            <td>
                                                <?php echo $remarkSession;?>
                                            </td>
                                            <td style="display: none">
                                                <?php echo $seqItemSession;?>
                                            </td>
                                            <td>
                                                <?php echo $currencyCdSession;?>
                                            </td>
                                            <td data-item-category="<?php echo $itemCategory;?>" class="<?php echo $itemCategory!='Y'?'':'text-primary'; ?>">
                                                <?php echo $itemCategory!='Y'?'NON ':''; ?>ROUTINE
                                            </td>
                                            <?php
                                                if($attachmentItemCountSession > 0){
                                            ?>
                                            <td class="td-actions">
                                                <a href="#" class="btn btn-small btn-info" id="window-attach">
                                                    <i class="btn-icon-only icon-paper-clip "> </i>
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
                                        <th colspan="11" class="td-align-right">
                                            Total
                                        </th>
                                        <th style="text-align: right">
                                        <?php
                                            // Get total of amount item
                                            echo number_format($prTotal);
                                        ?>
                                        </th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
						
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
                                            <td>
                                                <label class="control-label" for="remarkProc">Comment: </label>
                                                <div class="controls">
                                                    <input type="text" class="full-width-input" id="remarkProc" maxlength="200" />
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
									
									<!---------------------------------- Message --------------------------------->
									<div class="alert" id="mandatory-msg-1" style="display: none">
										<strong>Mandatory!</strong> Please specify item status whether Waiting PO Number/ Outstanding PO/ Item Rejected.
									</div>
									<div class="alert" id="mandatory-msg-2" style="display: none">
										<strong>Failure!</strong> Can not "Accept" this PR because all item status has been rejected, please click "Reject".
									</div>
									<div class="alert" id="mandatory-msg-3" style="display: none">
										<strong>Mandatory!</strong> Please input "Comment" because there is rejection.
									</div>
                                    <div class="alert" id="mandatory-msg-4" style="display: none">
                                        <strong>Mandatory!</strong> Please specify due date > current date.
                                    </div>
                                    <div class="alert" id="mandatory-msg-5" style="display: none">
                                        <strong>Failure!</strong> Data already updated by another user.
                                    </div>
									<div class="alert" id="undefined-msg" style="display: none">
										<strong>Undefined Error!</strong> System Error occurs. Please report to system administrator.
									</div>
									<div class="alert" id="session-msg" style="display: none">
										<strong>Session expired!</strong> Session timeout or user has not login.<br>Please login again.
									</div>
									<div class="alert alert-success" id="success-msg" style="display: none">
										<strong>Success!</strong> Accept PR finished.
									</div>
									<div class="alert alert-success" id="reject-msg" style="display: none">
										<strong>Success!</strong> Reject PR finished.
									</div>
                    
									<!---------------------------------- Button --------------------------------->
									<div class="form-actions">
									<?php
										if($sPrStatus == '1030'){
									?>
										<button class="btn btn-primary" id="btn-accept">Accept</button> 
										<button class="btn btn-danger" id="btn-reject">Reject</button> 
									<?php        
										}
										else{
									?>
										<button class="btn btn-primary" id="btn-accept" disabled>Accept</button> 
										<button class="btn btn-danger" id="btn-reject" disabled>Reject</button> 
									<?php             
										}
									?>
										<button class="btn" id="btn-back">Back</button>
									</div> <!-- /form-actions -->
                                </div><!-- /control-group -->
                            </div><!-- /widget-content -->
                        </div><!-- /widget -->
                    <!-- </form> -->
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

<div id="dialog-form" title="Edit PR Item" style="display: none;">
    <div class="alert" id="dialog-mandatory-msg-1" style="display: none;">
        <strong>Mandatory!</strong> Please fill all the field.
    </div>
    <div class="alert" id="dialog-mandatory-msg-2" style="display: none;">
        <strong>Mandatory!</strong> Please fill Expense No for Expense type.
    </div>
    <div class="alert" id="dialog-mandatory-msg-3" style="display: none;">
        <strong>Mandatory!</strong> Please fill RFI No for RFI type.
    </div>
    <div class="alert" id="dialog-mandatory-msg-4" style="display: none;">
        <strong>Mandatory!</strong> Please fill supplier for status "Waiting PO Number".
    </div>
    <div class="alert" id="dialog-mandatory-msg-5" style="display: none">
        <strong>Mandatory!</strong> Please specify item status whether Waiting PO Number/ Outstanding PO/ Item Rejected.
    </div>
    <div class="alert" id="dialog-mandatory-msg-6" style="display: none">
        <strong>Mandatory!</strong> Please specify due date > current date.
    </div>
    <div class="alert" id="dialog-mandatory-msg-7" style="display: none">
        <strong>Range Error!</strong> Please input value > 0.
    </div>
    <div class="alert" id="dialog-mandatory-msg-8" style="display: none;">
        <strong>Mandatory!</strong> Please fill Inventory No for Inventory type.
    </div>
    <div class="alert" id="dialog-duplicate-msg" style="display: none;">
        <strong>Duplicate!</strong> Item name already exist in PR Detail.
    </div>
    <div class="alert" id="dialog-undefined-msg" style="display: none">
        <strong>Undefined Error!</strong> System Error occurs. Please report to system administrator.
    </div>
    <div class="widget ">
        <form id="WEPO002Form-dialog">
            <div class="widget-content">
                <div class="control-group">
                    <input type="hidden" id="prNoItem" />
                    <input type="hidden" id="seqItem" />
                    <input type="hidden" id="currencyCd" />
                    <input type="hidden" id="refItemName" />
                    <table class="table-non-bordered">
                        <tr>
                            <td>
                                <label class="control-label" for="deliveryDate">Due Date: </label>
                                <div class="dialog-controls">
                                    <input type="text" id="deliveryDate" class="span2" tabindex="-1" readonly />
                                </div>
                            </td>
                            <td colspan="2">
                                <label class="control-label" for="itemStatus">Status: </label>
                                <div class="controls">
                                    <select id="itemStatus">
                                    <?php
                                        $query2 = "select 
                                                    APP_STATUS_CD 
                                                    ,APP_STATUS_NAME
                                                    ,APP_STATUS_ALIAS
                                                   from 
                                                        EPS_M_APP_STATUS 
                                                   where
                                                        APP_TYPE = 'PR_TO_PO'
                                                        and APP_STATUS_CD != '1150'";
                                        $sql2 = $conn->query($query2);
                                        while($row2 = $sql2->fetch(PDO::FETCH_ASSOC)){
                                            $appStsCd   = $row2['APP_STATUS_CD'];
                                            $appStsName = $row2['APP_STATUS_NAME'];
                                            $appStsAlias= $row2['APP_STATUS_ALIAS'];
                                    ?>
                                        <option value="<?php echo $appStsCd;?>"><?php echo $appStsAlias.'-'.$appStsName;?></option>
                                    <?php         
                                        }
                                    ?>                 
                                    </select>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class="control-label" for="itemType">Type: </label>
                                <div class="controls">
                                    <select id="itemType" class="span2">
                                    <?php
                                        $query = "select 
                                                    ITEM_TYPE_CD
                                                    ,ITEM_TYPE_NAME
                                                    ,ITEM_TYPE_ALIAS
                                                  from 
                                                    EPS_M_ITEM_TYPE
                                                  order by 
                                                    ITEM_TYPE_CD";
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
                            <td style="display: none" colspan="2" id="td-exp-no">
                                <label class="control-label" for="expNo">Expense No: </label>
                                <div class="controls">
                                    <select id="expNo">
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
                                        <option value="<?php echo $accountNo;?>"><?php echo $accountNo.'-'.$accountCd.'-'.$accountName;?></option>
                                        <?php         
                                            }
                                        ?>
                                    </select>
                                </div>
                            </td>
                            <td style="display: none" colspan="2" id="td-inv-no">
                                <label class="control-label" for="invNo">Inventory No: </label>
                                <div class="controls">
                                    <select id="invNo" class="full-width-input">
                                        <option value=""></option>
                                        <?php
                                            $query = "select 
                                                        CONVERT(int, ACCOUNT_NO) as ACCOUNT_NO
                                                        ,ACCOUNT_CD
                                                        ,ACCOUNT_NAME
                                                      from 
                                                        EPS_M_ACCOUNT
                                                      where
                                                        ITEM_TYPE_CD = '3'
                                                      order by 
                                                        ACCOUNT_NO";
                                            $sql = $conn->query($query);
                                            while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                                                $accountNo   = $row['ACCOUNT_NO'];
                                                $accountCd   = $row['ACCOUNT_CD'];
                                                $accountName = $row['ACCOUNT_NAME'];
                                        ?>
                                        <option value="<?php echo $accountNo;?>"><?php echo $accountNo.'-'.$accountCd.'-'.$accountName;?></option>
                                        <?php         
                                            }
                                        ?>
                                    </select>
                                </div>
                            </td>
                            <td style="display: none" id="td-rfi-no">
                                <label class="control-label" for="rfiNo">RFI No: </label>
                                <div class="controls">
                                    <input type="text" id="rfiNo" class="span2" maxlength="6" />
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class="control-label" for="itemCd">Code: </label>
                                <div class="dialog-controls">
                                    <input type="text" id="itemCd" class="span2" readonly />
                                </div>
                            </td>
                            <td colspan="2">
                                <label class="control-label" for="itemName">Name: </label>
                                <div class="controls">
                                    <input type="text" id="itemName" class="full-width-input" maxlength="200" />
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class="control-label" for="supplierCd">Code: </label>
                                <div class="controls">
                                    <select id="supplierCd" class="span2">
                                        <option value=""></option>
                                        <?php
                                            $query = "select 
                                                        SUPPLIER_CD 
                                                        ,SUPPLIER_NAME
                                                      from 
                                                        EPS_M_SUPPLIER";
                                            $sql = $conn->query($query);
                                            while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                                                $supplierCd   = $row['SUPPLIER_CD'];
                                        ?>
                                        <option value="<?php echo $supplierCd;?>"><?php echo $supplierCd;?></option>
                                        <?php         
                                            }
                                        ?>
                                    </select>
                                </div>
                            </td>
                            <td colspan="2">
                                <label class="control-label" for="supplierName">Supplier: </label>
                                <div class="controls">
                                    <select id="supplierName" class="full-width-input">
                                        <option value=""></option>
                                        <?php
                                            $query2 = "select 
                                                        SUPPLIER_CD 
                                                        ,SUPPLIER_NAME
                                                        ,CURRENCY_CD
                                                      from 
                                                        EPS_M_SUPPLIER
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
                                    <select id="um" class="span2">
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
                                    <input type="text" id="price" class="span3 input-align-right" maxlength="16" />
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
                                <label class="control-label" for="remark">Remark: </label>
                                <div class="controls">
                                    <input type="text" id="remark" class="full-width-input" />
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
<div id="dialog-confirm-accept" title="Confirm" style="display: none;"></div>
<div id="dialog-confirm-reject" title="Confirm" style="display: none;"></div>
<div id="dialog-confirm-back" title="Confirm" style="display: none;"></div>
<div id="dialog-attach-table" title="PR Attachment" style="display: none;">
    <div class="widget">
        <div class='widget-content'>
            <div class='control-group' id="dialog-control-group-attach">
            </div>
        </div>
    </div>
</div>
    </body>
</html>
