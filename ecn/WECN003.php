<?php session_start(); 
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Constant.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Common.php";
if(isset($_SESSION['sUserId']))
{      
    $sUserId    = $_SESSION['sUserId'];
    if($sUserId != '')
    {
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
        
        if($sRoleId == 'ROLE_03' || $sRoleId == 'ROLE_06' || $sNPK == '2150726'|| $sNPK == '2101164')
        {
            
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
        
        <title>EPS</title>
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
                    <a href="WECN003.php">
                        <i class="icon-book"></i><span>CN Summary</span> 
                    </a> 
                </li> 
                <li>
                    <a href="WECN001.php">
                        <i class="icon-refresh"></i><span>CN Transfer</span> 
                    </a> 
                </li> 
                <li>
                    <a href="WECN002.php">
                        <i class="icon-envelope"></i><span>CN Report</span> 
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
                    <div class="widget ">
                        <div class="widget-header">
                            <i class="icon-book"></i>
                            <h3>Credit Notes Summary</h3>
                        </div>
                        <div class="widget-content">
                            <h6 class="bigstats">
                                SUMMARY GS PURCHASE AMOUNT BY SUPPLIER
                                <ul>
                                <?php
                                $query_select_m_company = "select
                                                            COMPANY_CD
                                                            ,COMPANY_NAME
                                                           from
                                                            EPS_M_COMPANY
                                                           where
                                                            COMPANY_CD in ('T','H','S')";
                                $sql_select_m_company = $conn->query($query_select_m_company);
                                while($row_select_m_company = $sql_select_m_company->fetch(PDO::FETCH_ASSOC))
                                {
                                    $companyCd  = $row_select_m_company['COMPANY_CD'];
                                    $companyName= $row_select_m_company['COMPANY_NAME'];
                                ?>
                                    <li style="line-height: 32px;">
                                        <a href="../db/REPORT/GS_SUMMARY.php?criteria=SummaryAmountBySupplier&companyCd=<?php echo trim($companyCd);?>" target="_blank">
                                            <?php echo trim($companyName);?>
                                        </a>
										<ul>
                                            <li>
                                                EXPENSE
                                                <ul>
                                                    <?php
                                                    $query_select_t_cn_period = "select
                                                                                    CN_RUNNING_YEAR
                                                                                 from         
                                                                                    EPS_T_CN_PERIOD
                                                                                 group by 
                                                                                    CN_RUNNING_YEAR";
                                                    $sql_select_t_cn_period = $conn->query($query_select_t_cn_period);
                                                    while($row_select_t_cn_period = $sql_select_t_cn_period->fetch(PDO::FETCH_ASSOC))
                                                    {
                                                        $periodyear = $row_select_t_cn_period['CN_RUNNING_YEAR'];
                                                    ?>
                                                    <a href="../db/REPORT/GS_SUMMARY.php?criteria=SummaryAmountByItemTypeFy&companyCd=<?php echo trim($companyCd);?>&itemType=E&periodYear=<?php echo trim($periodyear);?>" target="_blank">
                                                           
                                                    <?php
                                                        echo "<li class='year'>FY $periodyear</li></a>";
                                                    }
                                                    ?>
                                                </ul>
                                            </li>
                                            <li>INVESTMENT
                                                <ul>
                                                    <?php
                                                    $query_select_t_cn_period = "select
                                                                                    CN_RUNNING_YEAR
                                                                                 from         
                                                                                    EPS_T_CN_PERIOD
                                                                                 group by 
                                                                                    CN_RUNNING_YEAR";
                                                    $sql_select_t_cn_period = $conn->query($query_select_t_cn_period);
                                                    while($row_select_t_cn_period = $sql_select_t_cn_period->fetch(PDO::FETCH_ASSOC))
                                                    {
                                                        $periodyear = $row_select_t_cn_period['CN_RUNNING_YEAR'];
                                                    ?>
                                                    <a href="../db/REPORT/GS_SUMMARY.php?criteria=SummaryAmountByItemTypeFy&companyCd=<?php echo trim($companyCd);?>&itemType=I&periodYear=<?php echo trim($periodyear);?>" target="_blank">
                                                           
                                                    <?php
                                                        echo "<li class='year'>FY $periodyear</li></a>";
                                                    }
                                                    ?>
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>
                                <?php
                                }
                                ?>
                                </ul>  
                            </h6>
                            <h6 class="bigstats">
                                SUMMARY GS PURCHASE AMOUNT BY SECTION
                                <ul>
                                <?php
                                $query_select_m_currency = "select
                                                            CURRENCY_CD
                                                           from
                                                            EPS_T_CN_DETAIL
                                                           group by
                                                            CURRENCY_CD
                                                           order by 
                                                            CURRENCY_CD";
                                $sql_select_m_currency = $conn->query($query_select_m_currency);
                                while($row_select_m_currency = $sql_select_m_currency->fetch(PDO::FETCH_ASSOC))
                                {
                                    $currencyCd  = $row_select_m_currency['CURRENCY_CD'];
                                ?>
                                    <li style="line-height: 32px;">
                                        <a href="../db/REPORT/GS_SUMMARY.php?criteria=SummaryAmountBySection&currencyCd=<?php echo trim($currencyCd);?>" target="_blank">
                                            <?php echo trim($currencyCd);?>
                                        </a>
                                    </li>
                                <?php
                                }
                                ?>
                                </ul>  
                            </h6>
                            <h6 class="bigstats">
                                SUMMARY BUYER PURCHASE
                                <ul>
                                    <li>
                                        <a href="../db/REPORT/GS_SUMMARY.php?criteria=SummaryBuyerByItem" target="_blank">BY ITEM</a>
                                    </li>
                                </ul>
                            </h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
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

<div id="dialog-confirm-session" title="Message" style="display: none;"></div>
<div id="dialog-confirm-logout" title="Confirm" style="display: none;"></div>
    </body>
</html>
