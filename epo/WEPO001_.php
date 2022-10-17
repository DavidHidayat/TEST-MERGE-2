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
            $prNo           = trim($_GET['prNo']);
            $requesterName  = trim($_GET['requesterName']);
            $plantCd        = $_GET['plantCd'];
            $inChargeName   = trim($_GET['procInCharge']);  
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
        <script src="../js/epo/WEPO001.js"></script>
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
                    <!---------------------------------- Message --------------------------------->
                    <div class="alert" id="mandatory-msg-1" style="display: none">
                        <strong>Mandatory!</strong> Please fill the search criteria.
                    </div>
                    <div class="alert" id="undefined-msg" style="display: none">
                        <strong>Undefined Error!</strong> System Error occurs. Please report to system administrator.
                    </div>
                    <!---------------------------------- Search -------------------------------->
                    <?php
                    if($sRoleId == 'ROLE_03' || $sRoleId == 'ROLE_04' || $sRoleId == 'ROLE_06')
                    {
                    ?>
                    <div class="widget ">
                        <div class="widget-header">
                            <i class="icon-search"></i>
                            <h3>Search</h3>
                        </div>
                        <div class="widget-content">
                            <form id="WEPO001Form">
                                <div class="control-group">	
                                    <table class="table-non-bordered">
                                        <tr>
                                            <td>
                                                <label class="control-label" for="prNo">PR No: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" id="prNo" name="prNo" maxlength="10"  value="<?php echo $prNo?>" />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="requesterName">Requester Name: </label>
                                                <div class="controls">
                                                    <input type="text" id="requesterName" name="requesterName" maxlength="20" value="<?php echo $requesterName;?>" />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="procInCharge">Proc. In Charge: </label>
                                                <div class="controls"><input type="text" id="procInCharge" name="procInCharge" maxlength="20" /></div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="plantCd">Requester Plant: </label>
                                                <div class="controls">
                                                    <select class="span2" name="plantCd" id="plantCd">
                                                        <option value=""></option>
                                                        <?php
                                                            $query = "select 
                                                                        PLANT_CD 
                                                                        ,PLANT_NAME
                                                                      from 
                                                                        EPS_M_PLANT 
                                                                      where
                                                                        PLANT_CD = '7'";
                                                            $sql = $conn->query($query);
                                                            while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                                                                $plantCdSelect   = $row['PLANT_CD'];
                                                                $plantNameSelect = $row['PLANT_NAME'];
                                                        ?>
                                                        <option value="<?php echo $plantCdSelect;?>" <?php if($plantCd == $plantCdSelect) echo "selected"; ?>><?php echo $plantNameSelect;?></option>
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
                    }
                    ?>
                    
                    <!----- PR List ---->
                    <div class="widget widget-table action-table">
                        <div class="widget-header"> <i class="icon-list-alt"></i>
                            <h3>PR Waiting for Procurement</h3>
                        </div>
                        <div class="widget-content">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th rowspan="2">NO</th>
                                        <th rowspan="2">DOWNLOAD</th>
                                        <th rowspan="2">PR NO</th>
                                        <th rowspan="2">LAST DATE APPROVED</th>
                                        <th rowspan="2">REQUESTER</th>
                                        <th rowspan="2" style="display: none">BU CD</th>
                                        <th colspan="2">BU</th>
                                        <th rowspan="2">SPECIAL TYPE<br>(IT Equipment)</th>
                                        <th rowspan="2">PROC. IN CHARGE</th>
                                    </tr>
                                    <tr>
                                        <th>ISSUER</th>
                                        <th>CHARGED</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    $wherePrHeader  = array();
                                
                                    $wherePrHeader[] = "EPS_T_PR_HEADER.PR_STATUS = '".constant('1030')."' ";
                                    
                                    if($sRoleId == 'ROLE_04')
									{
                                        if($prNo == '' && strlen($plantCd) == 0 && $inChargeName == '' && $requesterName == ''){
                                            $wherePrHeader[] .= "EPS_T_PR_HEADER.PROC_IN_CHARGE = '$sUserId' ";
                                        }
                                        
                                    }
                                    if($sRoleId == 'ROLE_02' || $sRoleId == 'ROLE_07' || $sRoleId == 'ROLE_09' || $sRoleId == 'ROLE_10' || $sRoleId == 'ROLE_11')
                                    {
                                        $wherePrHeader[] .= "EPS_T_PR_HEADER.PROC_IN_CHARGE = '$sUserId' ";
                                    }
                                    if($prNo)
									{
										$wherePrHeader[] = "EPS_T_PR_HEADER.PR_NO = '".$prNo."'";
                                    }
                                    if(strlen($plantCd) > 0)
									{
										$wherePrHeader[] .= "EPS_T_PR_HEADER.PLANT_CD = '$plantCd' ";
                                    }
                                    if($inChargeName)
									{
                                        $wherePrHeader[] .= "EPS_M_EMPLOYEE_2.NAMA1 like '".$inChargeName."%' ";
                                    }
                                    if($requesterName)
									{
										$wherePrHeader[] = "EPS_M_EMPLOYEE.NAMA1 like '".$requesterName."%'";
                                    }
                                    $prListNo = 0;
                                    $query = "select 
                                                EPS_T_PR_HEADER.PR_NO
                                                ,(select top 1 
                                                    convert(VARCHAR(24), EPS_T_PR_APPROVER.APPROVAL_DATE, 120)
                                                from 
                                                    EPS_T_PR_APPROVER
                                                where      
                                                    EPS_T_PR_APPROVER.PR_NO = EPS_T_PR_HEADER.PR_NO
                                                order by 
                                                    APPROVAL_DATE desc
                                                ) as LAST_APPROVAL_DATE
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
                                                ,EPS_T_PR_HEADER.CHARGED_BU_CD
                                                ,EPS_T_PR_HEADER.SPECIAL_TYPE_ID
                                                ,EPS_T_PR_HEADER.PURPOSE
                                                ,EPS_T_PR_HEADER.PROC_IN_CHARGE
                                                ,EPS_M_EMPLOYEE_2.NAMA1 as PROC_NAME
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
                                                EPS_M_EMPLOYEE EPS_M_EMPLOYEE_2
                                            on
                                                EPS_T_PR_HEADER.PROC_IN_CHARGE = EPS_M_EMPLOYEE_2.NPK ";
                                    if(count($wherePrHeader)) {
                                        $query .= "where " . implode('and ', $wherePrHeader);
                                    }
                                    $query .= " order by LAST_APPROVAL_DATE ";
                                    $sql = $conn->query($query);
                                    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                                        $prNo           = $row['PR_NO'];
                                        $issuedDate     = $row['ISSUED_DATE'];
                                        $buCd           = $row['BU_CD'];
                                        $requester      = $row['REQUESTER'];
                                        $requesterName  = $row['REQUESTER_NAME'];
                                        $sectionCd      = $row['SECTION_CD'];
                                        $plant          = $row['PLANT_CD'];
                                        $plantName      = $row['PLANT_NAME'];
                                        $company        = $row['COMPANY_CD'];
                                        $companyName    = $row['COMPANY_NAME'];
                                        $extNo          = $row['EXT_NO'];
                                        $prIssuer       = $row['REQ_BU_CD'];
                                        $prCharged      = $row['CHARGED_BU_CD'];
                                        $specialType    = $row['SPECIAL_TYPE_ID'];
                                        $prInCharge   	= $row['PROC_IN_CHARGE'];
                                        $procName       = $row['PROC_NAME'];
                                        if($specialType == 'IT'){
                                            $specialType = 'IT Equipment';
                                        }
                                        if($specialType == 'NIT'){
                                            $specialType = '';
                                        }
                                        $purpose        = $row['PURPOSE'];
                                        $attachmentCount= $row['ATTACHMENT_COUNT'];
                                        $lastApprovalDate= $row['LAST_APPROVAL_DATE'];
                                        $prListNo++;
                                    
                                ?>
                                    <tr>
                                        <td class="td-number">
                                            <?php echo $prListNo;?>.
                                        </td>
                                        <td style="text-align: center">
                                        <?php
                                            if(substr($buCd,0,1) == 'H'){
                                        ?>
                                            <a href="../lib/pdf/PR_HDI.php?prNo=<?php echo $prNo;?>" target="_blank" class="btn btn-small btn-linkedin-alt">
                                                <i class="btn-icon-only icon-download-alt"> </i>
                                            </a>
                                        <?php
                                            }
                                            else
                                            {
                                        ?>
                                            <a href="../lib/pdf/PR_TACI.php?prNo=<?php echo $prNo;?>" target="_blank" class="btn btn-small btn-linkedin-alt">
                                                <i class="btn-icon-only icon-download-alt"> </i>
                                            </a>
                                        <?php        
                                            }
                                        ?>
                                        </td>
                                        <td>
                                        <?php
                                            if($prInCharge == $sUserId || $sRoleId == 'ROLE_03')
                                            {
                                        ?>
                                            <a href="../db/Redirect/PO_Screen.php?criteria=prDetail&paramPrNo=<?php echo $prNo;?>" class="faq-list">
                                                <b><?php echo $prNo ;?></b>
                                            </a>
                                        <?php
                                            }
                                            else
                                            {
                                                echo $prNo;
                                            }
                                        ?>
                                            
                                        </td>
                                        <td>
                                            <?php echo $lastApprovalDate;?>
                                        </td>
                                        <td>
                                            <?php echo stripslashes($requesterName);?>
                                        </td>
                                        <td style="display: none">
                                            <?php echo $buCd;?>
                                        </td>
                                        <td>
                                            <?php echo $prIssuer;?>
                                        </td>
                                        <td>
                                            <?php echo $prCharged;?>
                                        </td>
                                        <td>
                                            <?php echo $specialType;?>
                                        </td>
                                        <td>
                                        <?php
                                            if($sRoleId == 'ROLE_03' || $sRoleId == 'ROLE_04' || $sRoleId == 'ROLE_06')
                                            {
                                        ?>
                                            <a href="../db/Redirect/PO_Screen.php?criteria=prInCharge&paramPrNo=<?php echo $prNo;?>" class="faq-list">
                                                <b><?php echo $procName;?></b>
                                            </a>
                                        <?php        
                                            }
                                            else
                                            {
                                                echo $procName;
                                            }
                                        ?>
                                            
                                        </td>
                                    </tr>
                                <?php
                                    }
                                ?>
                                </tbody>
                            </table>
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
                    &copy; 2018 TD AUTOMOTIVE COMPRESSOR INDONESIA. All rights reserved. 
                </div> <!-- /span12 -->	
            </div> <!-- /row -->
	</div> <!-- /container -->		
    </div> <!-- /footer-inner -->	
</div> <!-- /footer -->

<div id="dialog-confirm-logout" title="Confirm" style="display: none;"></div>
    </body>
</html>
