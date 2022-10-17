<?php session_start(); 
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Constant.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Common.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/CONTROLLER/PAGING.php";
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
        
        if($sRoleId == 'ROLE_02' || $sRoleId == 'ROLE_03' || $sRoleId == 'ROLE_04' || $sRoleId == 'ROLE_06' || $sRoleId == 'ROLE_07' || $sRoleId == 'ROLE_11')
        {
            $buCdCriteria        = trim($_GET['buCdCriteria']); 
            $buNameCriteria      = trim($_GET['buNameCriteria']);
            $approverNoCriteria  = trim($_GET['approverNoCriteria']);
            $npkCriteria         = trim($_GET['npkCriteria']);  
            $approverNameCriteria= trim($_GET['approverNameCriteria']);  
            $currencyCdCriteria  = trim($_GET['currencyCdCriteria']);  
            
            $wherePrApproverMaster = array(); 
			$wherePrApproverMaster[] = "EPS_M_TBUNIT.KDAKT = 'A'";
			$wherePrApproverMaster[] = "EPS_M_TBUNIT.KDBU LIKE 'T%'";
            if($buCdCriteria)
            {
                $wherePrApproverMaster[] = "EPS_M_PR_APPROVER.BU_CD = '".$buCdCriteria."'";
            }
            if($buNameCriteria)
            {
                $wherePrApproverMaster[] = "EPS_M_TBUNIT.NMBU1 like '%".$buNameCriteria."%'";
            }              
            if($approverNoCriteria)
            {
                $wherePrApproverMaster[] = "EPS_M_PR_APPROVER.APPROVER_NO = '".$approverNoCriteria."'";
            }
            if($npkCriteria)
            {
                $wherePrApproverMaster[] = "ltrim(EPS_M_PR_APPROVER.NPK) = '".trim($npkCriteria)."'";
            }
            if($approverNameCriteria)
            {
                $wherePrApproverMaster[] = "EPS_M_EMPLOYEE.NAMA1 like '%".$approverNameCriteria."%'";
            } 
            if($currencyCdCriteria)
            {
                $wherePrApproverMaster[] = "EPS_M_LIMIT.CURRENCY_CD = '".$currencyCdCriteria."'";
            }
            
            $itemNo = 0;
            if(isset($_GET['mpage']))
            {
                $mpage = trim($_GET['mpage']);
            }
            else
            {
                $mpage = 1;
            }
            $max_per_page   = constant('20');
            $num            = 5;
                                    
            if($mpage)
            { 
                $start = ($mpage) * $max_per_page; 
            }
            else
            {
                $start  = constant('20');	
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
                                    
            /**
             * SELECT COUNT EPS_T_PR_HEADER
             **/
            $query_select_count_m_pr_approver ="select 
                                                    count (*) as COUNT_PR_APPROVER
                                                from
                                                    EPS_M_PR_APPROVER
                                                left join
                                                    EPS_M_EMPLOYEE
                                                on
                                                    EPS_M_PR_APPROVER.NPK = EPS_M_EMPLOYEE.NPK
                                                left join
                                                    EPS_M_TBUNIT
                                                on
                                                    EPS_M_PR_APPROVER.BU_CD = EPS_M_TBUNIT.KDBU 
                                                left join
                                                    EPS_M_LIMIT 
                                                on 
                                                    EPS_M_PR_APPROVER.APPROVER_LEVEL = EPS_M_LIMIT.LEVEL_ID ";
            if(count($wherePrApproverMaster)) {
                $query_select_count_m_pr_approver .= "where " . implode('and ', $wherePrApproverMaster);
            }
            $sql_select_count_m_pr_approver = $conn->query($query_select_count_m_pr_approver);
            $row_select_count_m_pr_approver = $sql_select_count_m_pr_approver->fetch(PDO::FETCH_ASSOC);
            $countPrApprover = $row_select_count_m_pr_approver['COUNT_PR_APPROVER'];
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
        <script src="../js/emst/WMST005.js"></script>
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
                <li>
                    <a href="WMST004.php">
                        <i class="icon-money"></i><span>Item Price</span> 
                    </a> 
                </li>
                <li class="active">
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
                    <!---------------------------------- Message --------------------------------->
                    <div class="alert" id="mandatory-msg-1" style="display: none">
                        <strong>Mandatory!</strong> Please fill the search criteria.
                    </div>
                    <?php
                    if($buCdCriteria || $buNameCriteria || $approverNoCriteria || $npkCriteria || $approverNameCriteria || $currencyCdCriteria)
                    {
                        if($countPrApprover == 0)
                        {
                    ?>
                    <div class="alert" id="mandatory-msg-2">
                        <strong>Data not found!</strong> No results match with your search.
                    </div>
                    <?php    
                        }
                    }
                    ?>
                    
                    <!----- Item Master ---->
                    <div class="widget ">
                        <div class="widget-header">
                            <i class="icon-search"></i>
                            <h3>Search</h3>
                        </div>
                        <div class="widget-content">
                            <form id="WMST005Form">
                                <div class="control-group">	
                                    <table class="table-non-bordered">
                                        <tr>
                                            <td>
                                                <label class="control-label" for="buCd">BU Code: </label>
                                                <div class="controls">
                                                    <select id="buCd" class="span2" name="buCdCriteria">
                                                        <option value=""></option>
                                                        <?php 
                                                            $query_select_m_bunit = "select 
                                                                                        BU_CD
                                                                                     from 
                                                                                        EPS_M_BUNIT 
																					 where 
																						ACTIVE_FLAG = 'A' AND BU_CD LIKE 'T%'";
                                                            $sql_select_m_bunit= $conn->query($query_select_m_bunit);
                                                            while($row_select_m_bunit = $sql_select_m_bunit->fetch(PDO::FETCH_ASSOC)){
                                                                $buCdSelect   = $row_select_m_bunit['BU_CD'];
                                                        ?>
                                                        <option value="<?php echo $buCdSelect;?>" <?php if(trim($buCdCriteria) == trim($buCdSelect)) echo "selected"; ?>><?php echo $buCdSelect;?></option>
                                                        <?php         
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="buName">BU Name: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" id="buName" name="buNameCriteria" value="<?php echo $buNameCriteria;?>" />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="approverNo">Approver No: </label>
                                                <div class="controls">
                                                    <select id="approverNo" class="span2" name="approverNoCriteria">
                                                        <option value=""></option>
                                                        <?php 
                                                            $query_select_m_pr_app_by_appno = "select 
                                                                                                    APPROVER_NO
                                                                                                from 
                                                                                                    EPS_M_PR_APPROVER
                                                                                                group by 
                                                                                                    APPROVER_NO
                                                                                                order by
                                                                                                    APPROVER_NO ";
                                                            $sql_select_m_pr_app_by_appno = $conn->query($query_select_m_pr_app_by_appno);
                                                            while($row_select_m_pr_app_by_appno = $sql_select_m_pr_app_by_appno->fetch(PDO::FETCH_ASSOC)){
                                                                $approverNoSelect   = $row_select_m_pr_app_by_appno['APPROVER_NO'];
                                                        ?>
                                                        <option value="<?php echo $approverNoSelect;?>" <?php if($approverNoCriteria == $approverNoSelect) echo "selected"; ?>><?php echo $approverNoSelect;?></option>
                                                        <?php         
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="npk">NPK </label>
                                                <div class="controls">
                                                    <select id="npk" class="span2" name="npkCriteria">
                                                        <option value=""></option>
                                                        <?php 
                                                            $query_select_m_pr_app_by_npk = "select 
                                                                                                    NPK
                                                                                                from 
                                                                                                    EPS_M_PR_APPROVER WHERE BU_CD LIKE 'T%'
                                                                                                group by 
                                                                                                    NPK
                                                                                                order by
                                                                                                    NPK ";
                                                            $sql_select_m_pr_app_by_npk = $conn->query($query_select_m_pr_app_by_npk);
                                                            while($row_select_m_pr_app_by_npk = $sql_select_m_pr_app_by_npk->fetch(PDO::FETCH_ASSOC)){
                                                                $npkSelect   = $row_select_m_pr_app_by_npk['NPK'];
                                                        ?>
                                                        <option value="<?php echo $npkSelect;?>" <?php if($npkCriteria == $npkSelect) echo "selected"; ?>><?php echo $npkSelect;?></option>
                                                        <?php         
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="approverName">Name: </label>
                                                <div class="controls">
                                                    <input type="text" class="span3" id="approverName" name="approverNameCriteria" value="<?php echo $approverNameCriteria;?>" />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="currencyCd">Currency: </label>
                                                <div class="controls">
                                                    <select id="currencyCd" class="span2" name="currencyCdCriteria">
                                                        <option value=""></option>
                                                        <?php 
                                                            $query_select_m_currency = "select 
                                                                                            CURRENCY_CD
                                                                                        from 
                                                                                            EPS_M_CURRENCY
                                                                                        order by 
                                                                                            CURRENCY_CD ";
                                                            $sql_select_m_currency = $conn->query($query_select_m_currency);
                                                            while($row_select_m_currency = $sql_select_m_currency->fetch(PDO::FETCH_ASSOC)){
                                                                $currencyCdSelect   = $row_select_m_currency['CURRENCY_CD'];
                                                        ?>
                                                        <option value="<?php echo $currencyCdSelect;?>" <?php if($currencyCdCriteria == $currencyCdSelect) echo "selected"; ?>><?php echo $currencyCdSelect;?></option>
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
                    
                    <!----- Item Master ---->
                    <div class="widget widget-table action-table">
                            <div class="widget-header"> 
                                <i class="icon-group"></i>
                                <h3>PR Approver Master</h3>
                            </div>
                            <div class="widget-content">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th colspan="11" style="text-align: left">
                                                <a href="../db/REPORT/MASTER_SEARCH.php?criteria=PrApprover&buCd=<?php echo $buCdCriteria;?>&buName=<?php echo $buNameCriteria;?>&approverNo=<?php echo $approverNoCriteria;?>&npk=<?php echo $npkCriteria;?>&approverName=<?php echo $approverNameCriteria;?>&currencyCd=<?php echo $currencyCdCriteria;?>" target="_blank" class="btn btn-small btn-linkedin-alt" id="btn-download">
                                                    Download
                                                    <i class="btn-icon-only icon-download-alt"> </i>
                                                </a>
                                            </th>
                                        </tr> 
                                        <tr>
                                            <th rowspan="2">NO</th>
                                            <th colspan="2">BU</th>
                                            <th colspan="3">APPROVER</th>
                                            <th rowspan="2">LIMIT AMOUNT</th>
                                            <th rowspan="2">CURRENCY</th>
                                            <th colspan="2">UPDATE</th>
                                        </tr>  
                                        <tr>
                                            <th>CODE</th>
                                            <th>NAME</th>
                                            <th>NO</th>
                                            <th>NPK</th>
                                            <th>NAME</th>
                                            <th>DATE</th>
                                            <th>BY</th>
                                        </tr> 
                                    </thead>
                                    <tbody>
                                    <?php
                                    if($start > $countPrApprover)
                                    {
                                        $lgenap     = $start - $max_per_page;
                                        $max_per_pages = $countPrApprover - $lgenap;
                                        $start      = $countPrApprover;
                                    }
                                    else
                                    {
                                        $max_per_pages = $max_per_page;
                                    }
                                    
                                    /**
                                     * SELECT EPS_M_ITEM
                                     **/
                                    $query_select_m_pr_approver = "select 
                                                                    * 
                                                                  from 
                                                                    (select top  $max_per_pages  
                                                                        * 
                                                                    from      
                                                                        (select top $start 
                                                                            EPS_M_PR_APPROVER.BU_CD
                                                                            ,EPS_M_TBUNIT.NMBU1 as BU_NAME
                                                                            ,EPS_M_PR_APPROVER.APPROVER_NO
                                                                            ,EPS_M_PR_APPROVER.NPK
                                                                            ,EPS_M_EMPLOYEE.NAMA1 as APPROVER_NAME
                                                                            ,EPS_M_PR_APPROVER.APPROVER_LEVEL
                                                                            ,EPS_M_LIMIT.LIMIT_AMOUNT
                                                                            ,EPS_M_LIMIT.CURRENCY_CD
                                                                            ,CONVERT(VARCHAR(24), EPS_M_PR_APPROVER.UPDATE_DATE, 103) as UPDATE_DATE
                                                                            ,CONVERT(VARCHAR(24), EPS_M_PR_APPROVER.UPDATE_DATE, 108) as UPDATE_TIME
                                                                        from
                                                                            EPS_M_PR_APPROVER
                                                                        left join
                                                                            EPS_M_EMPLOYEE
                                                                        on
                                                                            EPS_M_PR_APPROVER.NPK = EPS_M_EMPLOYEE.NPK
                                                                        left join
                                                                            EPS_M_TBUNIT
                                                                        on
                                                                            EPS_M_PR_APPROVER.BU_CD = EPS_M_TBUNIT.KDBU 
                                                                        left join
                                                                            EPS_M_LIMIT 
                                                                        on 
                                                                            EPS_M_PR_APPROVER.APPROVER_LEVEL = EPS_M_LIMIT.LEVEL_ID ";
                                    if(count($wherePrApproverMaster)) {
                                        $query_select_m_pr_approver .= "where " . implode(' and ', $wherePrApproverMaster);
                                    }
                                    $query_select_m_pr_approver .= "
                                                                        order by 
                                                                            EPS_M_PR_APPROVER.BU_CD asc
                                                                            ,EPS_M_LIMIT.CURRENCY_CD asc ) 
                                                                        as T1 
                                                                    order by 
                                                                        BU_CD desc
                                                                        ,CURRENCY_CD desc ) 
                                                                    as T2 
                                                                order by 
                                                                    BU_CD
                                                                    ,CURRENCY_CD
                                                                    ,APPROVER_NO
                                                                    ,NPK ";
                                    $sql_select_m_pr_approver = $conn->query($query_select_m_pr_approver);
                                    while($row_select_m_pr_approver = $sql_select_m_pr_approver->fetch(PDO::FETCH_ASSOC))
                                    {
                                        $buCd           = $row_select_m_pr_approver['BU_CD'];
                                        $buName         = $row_select_m_pr_approver['BU_NAME'];
                                        $approverNo     = $row_select_m_pr_approver['APPROVER_NO'];
                                        $npk            = $row_select_m_pr_approver['NPK'];
                                        $approverName   = $row_select_m_pr_approver['APPROVER_NAME'];
                                        $approverLevel  = $row_select_m_pr_approver['APPROVER_LEVEL'];
                                        $limitAmount    = $row_select_m_pr_approver['LIMIT_AMOUNT'];
                                        $currencyCd     = $row_select_m_pr_approver['CURRENCY_CD'];
                                        $updateDate     = $row_select_m_pr_approver['UPDATE_DATE'];
                                        $updateTime     = $row_select_m_pr_approver['UPDATE_TIME'];
                                        $updateBy       = $row_select_m_pr_approver['UPDATE_BY_NAME'];
                                        
                                        if(trim($updateBy) == "")
                                        {
                                            $updateBy = "Administrator";
                                        }
                                        $itemNo++;
                                    ?>
                                        <tr>
                                            <td class="td-number">
                                                <?php echo $itemNo;?>.
                                            </td>
                                            <td>
                                                <?php echo $buCd;?>
                                            </td>
                                            <td>
                                                <?php echo $buName;?>
                                            </td>
                                            <td>
                                                <?php echo $approverNo;?>
                                            </td>
                                            <td>
                                                <?php echo $npk;?>
                                            </td>
                                            <td>
                                                <?php echo $approverName;?>
                                            </td>
                                            <td class="td-align-right">
                                                <?php echo number_format($limitAmount,0);?>
                                            </td>
                                            <td>
                                                <?php echo $currencyCd;?>
                                            </td>
                                            <td>
                                                <?php echo $updateDate." ".$updateTime;?>
                                            </td>
                                            <td>
                                                <?php echo substr($updateBy, 0, strpos($updateBy, ' '));?>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>    
                                    <tr>
                                        <th colspan="11">
                                        <?php
                                            if($countPrApprover > $max_per_page)
                                            {         
                                                echo "<div id=\"pagination\" >";
                                                if ($query_select_m_pr_approver != "")
                                                {  
                                                    $fld = "buCdCriteria=$buCdCriteria&buNameCriteria=$buNameCriteria&approverNoCriteria=$approverNoCriteria&npkCriteria=$npkCriteria&approverNameCriteria=$approverNameCriteria&currencyCdCriteria=$currencyCdCriteria&mpage";
                                                }
                                                else
                                                {
                                                    $fld = "mpage";
                                                }
                                                paging($query_select_m_pr_approver,$max_per_page,$num,$mpage,$fld,$countPrApprover);
                                                echo "</div>";
                                            }
                                        ?>
                                        </th>
                                    </tr>
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
                    &copy; 2018 PT.TD AUTOMOTIVE COMPRESSOR INDONESIA. All rights reserved. 
                </div> <!-- /span12 -->	
            </div> <!-- /row -->
	</div> <!-- /container -->		
    </div> <!-- /footer-inner -->	
</div> <!-- /footer -->
<div id="dialog-confirm-logout" title="Confirm" style="display: none;"></div>
    </body>
</html>