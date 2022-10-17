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
<!-- /subnavbar -->

<div class="subnavbar">
    <div class="subnavbar-inner">
        <div class="container">
            <ul class="mainnav">
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
        <div class="span3">
            <div class="widget">
                <div class="widget-content">
                    <a href="../epr_/WEPR001.php" class="shortcut"><h2><i class="shortcut-icon icon-file"></i><br>Purchase<br>Requisition</h2></a>
                    <p>are issued by each Section to notify the Procurement Department (General Supplies) of items(goods or services) 
                       it needs to order.</p>	
				</div> <!-- /widget-content -->
            </div> <!-- /widget -->
		</div> <!-- /span3 -->
        <?php 
        if($sRoleId == 'ROLE_02' || $sRoleId == 'ROLE_03' || $sRoleId == 'ROLE_04' || $sRoleId == 'ROLE_06' || $sRoleId == 'ROLE_07' || $sRoleId == 'ROLE_09' || $sRoleId == 'ROLE_10' || $sRoleId == 'ROLE_11')
        {
        ?>         
        <div class="span3">
            <div class="widget">
                <div class="widget-content">
                    <a href="../epo/WEPO001_.php" class="shortcut"><h2><i class="shortcut-icon icon-paste"></i><br>Purchase<br>Order</h2></a>
                    <p>are issued by the Procurement Department (General Supplies) to Supplier, indicating types, quantities, and prices. It also outlines the delivery date. </p>	
		</div> <!-- /widget-content -->
            </div> <!-- /widget -->
        </div> <!-- /span3 -->
        <?php 
        }
        ?>
        <?php 
        if($sRoleId == 'ROLE_03' || $sRoleId == 'ROLE_04' || $sRoleId == 'ROLE_05' || $sRoleId == 'ROLE_06' || $sRoleId == 'ROLE_09' || $sRoleId == 'ROLE_11')
        {
        ?>  
        <div class="span3">
            <div class="widget">
                <div class="widget-content">
                    <a href="../ero/WERO001.php" class="shortcut"><h2><i class="shortcut-icon icon-calendar "></i><br>Receiving<br>Order</h2></a>
                    <p>are issued by Receiving area to collect or receive items and matching the actual number received with the number on the purchase order.</p>	
                </div> <!-- /widget-content -->
            </div> <!-- /widget -->
        </div> <!-- /span3 -->
        <?php 
        }
        if($sRoleId == 'ROLE_03' || $sRoleId == 'ROLE_06' || $sNPK == '2150726'|| $sNPK == '2101164' || $sNPK == '2181074')
        {
        ?>  
        <div class="span3">
            <div class="widget">
                <div class="widget-content">
                    <a href="../ecn/WECN003.php" class="shortcut"><h2><i class="shortcut-icon icon-book"></i><br>Credit<br>Notes</h2></a>
                    <p>are receipt given by the Procurement (General Supplies) to Supplier, it informing amount of the purchase with status closed and will pay.</p>	
				</div> <!-- /widget-content -->
            </div> <!-- /widget -->
        </div> <!-- /span3 -->
        <?php 
        }
        if($sRoleId == 'ROLE_02' || $sRoleId == 'ROLE_03' || $sRoleId == 'ROLE_04' || $sRoleId == 'ROLE_06' || $sRoleId == 'ROLE_07' || $sRoleId == 'ROLE_10' || $sRoleId == 'ROLE_11')
        {
        ?>  
        <div class="span3">
            <div class="widget">
                <div class="widget-content">
                    <a href="../emst_/WMST003.php" class="shortcut"><h2><i class="shortcut-icon icon-tasks"></i><br>Master<br>Data</h2></a>
                    <p>is any information that considered to play a key role in the core operation of EPS include Item, Price, Supplier, Approver and User ID.</p>	
				</div> <!-- /widget-content -->
            </div> <!-- /widget -->
        </div> <!-- /span3 -->
        <?php 
        }
		if($sRoleId == 'ROLE_03' || $sRoleId == 'ROLE_04' || $sRoleId == 'ROLE_06')
        {
        ?>
        <div class="span3">
            <div class="widget">
                <div class="widget-content">
                    <a href="../entf/WNTF001.php" class="shortcut"><h2><i class="shortcut-icon icon-envelope-alt"></i><br>Mail<br>Report</h2></a>
                    <p>are manually sending daily report to supplier for purchase order (PO), outstanding po and PO with delay delivery information.</p>	
				</div> <!-- /widget-content -->
            </div> <!-- /widget -->
        </div> <!-- /span3 -->    
        <?php    
        }
        ?> 
        <div class="span3">
            <div class="widget">
                <div class="widget-content">
                    <a href="../ecom/WCOM003.php" class="shortcut"><h2><i class="shortcut-icon icon-user "></i><br>Change<br>Password</h1></a>
                    <p>is information that considered to user login in EPS and information about userid and email. Change your password for security reasons.</p>	
				</div> <!-- /widget-content -->
            </div> <!-- /widget -->
        </div> <!-- /span3 -->
        
      </div>
      <!-- /row --> 
    </div>
    <!-- /container --> 
  </div>
  <!-- /main-inner --> 
</div>
<!-- /main -->

<div class="footer">
    <div class="footer-inner">
	<div class="container">
            <div class="row">
		<div class="span12">
                    &copy; 2018 PT.TD AUTOMOTIVE COMPRESSOR All rights reserved. 
                </div> <!-- /span12 -->	
            </div> <!-- /row -->
	</div> <!-- /container -->		
    </div> <!-- /footer-inner -->	
</div> <!-- /footer -->

<div id="dialog-confirm-logout" title="Confirm" style="display: none;"></div>
    </body>
</html>
