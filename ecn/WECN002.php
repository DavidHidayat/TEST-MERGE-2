<?php session_start(); 
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Constant.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Common.php";
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
        
        if($sRoleId == 'ROLE_03' || $sRoleId == 'ROLE_06' || $sNPK == ' 950611')
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
		
        <script src="../lib/jquery/jquery.loading-indicator-master/src/jquery.loading-indicator.js"></script>
        <link type="text/css" rel="stylesheet" href="../lib/jquery/jquery.loading-indicator-master/dist/jquery.loading-indicator.css" />
        
        <script src="../js/ecn/WECN002.js"></script>
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
                <li>
                    <a href="WECN003.php">
                        <i class="icon-book"></i><span>CN Summary</span> 
                    </a> 
                </li> 
                <li>
                    <a href="WECN001.php">
                        <i class="icon-refresh"></i><span>CN Transfer</span> 
                    </a> 
                </li> 
                <li class="active">
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
                            <i class="icon-envelope"></i>
                            <h3>Credit Notes Report</h3>
                        </div>
                        <div class="widget-content">
                            <div class="alert alert-info">
                                <strong>Information!</strong> This screen has purpose to send mail EPS Credit Notes for supplier after CN Transfer process success.
                            </div>
                            <form id="WERO001Form">
                                <div class="control-group">	
                                    <table class="table-non-bordered">
                                        <tr>
                                            <td>
                                                <label class="control-label" for="cnDate">Closing Month: (** i.e : 201508) </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" id="cnDate" name="cnDate" maxlength="6" value="" />
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </form>
                            <?php
                            if($sRoleId == 'ROLE_03' || $sNPK == ' 950611')
                            {
                            ?>
                                <div>
                                    <button class="btn btn-warning" id="btn-cn-report">CN Report Process</button> 
                                </div> 
                            <?php    
                            }
                            else
                            {
                            ?>
                                <div>
                                    <button class="btn btn-warning" id="btn-cn-report" disabled>CN Report Process</button> 
                                </div> 
                            <?php    
                            }
                            ?> 
                            <div class="control-group">
                                <label class="control-label">&nbsp;</label>
								<div class="controls">
                                    <div class="progress progress-striped active" id="progressbar">
                                        <div class="bar" style="width: 40%;"></div>
                                    </div>
                                </div> <!-- /controls -->	
                            </div> <!-- /control-group -->
                            <!---------------------------------- Message --------------------------------->
                            <div class="alert" id="mandatory-msg-1" style="display: none">
                                <strong>Mandatory!</strong> Please input closing month.
                            </div>
                            <div class="alert" id="undefined-msg" style="display: none">
                                <strong>Undefined Error!</strong> System Error occurs. Please report to system administrator.
                            </div>
                            <div class="alert alert-success" id="transfer-msg" style="display: none">
                                <strong>Success!</strong> Send Mail EPS Credit Notes to supplier finished.<br>Please check your email.
                            </div>
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
<div id="dialog-confirm-save" title="Confirm" style="display: none;"></div>
    </body>
</html>
