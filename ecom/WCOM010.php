<?php session_start();

if(isset($_SESSION['sUserId']))
{
    unset($_SESSION['sUserId']);
    unset($_SESSION['sNPK']);
    unset($_SESSION['sNama']);
    unset($_SESSION['sBunit']);
    unset($_SESSION['sSeksi']);
    unset($_SESSION['sWarga']);
    unset($_SESSION['sKDPL']);
    unset($_SESSION['sNMPL']);
    unset($_SESSION['sKdper']);
    unset($_SESSION['sNmper']);
    unset($_SESSION['sRoleId']);
    unset($_SESSION['sinet']);
    unset($_SESSION['snotes']);
    unset($_SESSION['sBuLogin']);
    unset($_SESSION['sBuLoginName']);
    unset($_SESSION['sUserType']);
    session_destroy();   
}       
?>
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
                        <span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
                    </a>
                    <a class="brand" href="index.html">
                        e-Purchase System		
                    </a>		
                    <div class="nav-collapse">
                        <ul class="nav pull-right">
                            <li class="">						
                                <a href="../index.php" class="">
                                    <i class="icon-chevron-left"></i>
                                    Back to Login
				</a>
                            </li>
			</ul>	
                    </div><!--/.nav-collapse -->
		</div> <!-- /container -->
            </div> <!-- /navbar-inner -->
        </div> <!-- /navbar -->

        <div class="container">
            <div class="row">
		<div class="span12">
                    <div class="error-container">
                        <h1>403</h1>
			<h2>Access Denied!</h2>
			<div class="error-details">
                            Sorry, you don't have permission to access on this server. If you would like to using, you must login first!
			</div> <!-- /error-details -->
			<div class="error-actions">
                            <a href="../index.php" class="btn btn-large btn-primary">
				<i class="icon-chevron-left"></i>
                                &nbsp;
				Back to Login						
                            </a>
			</div> <!-- /error-actions -->
                    </div> <!-- /error-container -->	
		</div> <!-- /span12 -->
            </div> <!-- /row -->
        </div> <!-- /container -->
    </body>
</html>