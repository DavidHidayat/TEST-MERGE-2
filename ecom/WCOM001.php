<?php session_start(); 

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
        <link rel="stylesheet" href="../css/pages/signin.css" type="text/css">
        <link rel="stylesheet" href="../css/additional.css" ></link>
        <link rel="stylesheet" href="../lib/jquery/jquery-ui-1.11.2.custom/jquery-ui.css">
        
        <script src="../lib/jquery/jquery-1.11.0.min.js"></script> 
        <script src="../lib/jquery/jquery-ui-1.11.2.custom/jquery-ui.min.js"></script> 
        <script src="../lib/jquery/jquery-ui-1.11.2.custom/jquery-ui.js"></script> 
        <script type="text/javascript" src="../js/Common.js"></script>
        <script type="text/javascript" src="../js/Common_JQuery.js"></script>
        <script type="text/javascript" src="../js/Common_JQuery.js"></script>
        <script src="../js/ecom/WCOM001.js"></script>
        <script src="../js/signin.js"></script>
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
                    <li class="">						
			<a href="../index.php" class="">
                <i class="icon-chevron-left"></i>
                Back to Login page
			</a>
						
                    </li>
		</ul>
            </div><!--/.nav-collapse -->	
        </div><!-- /container --> 
        
    </div><!-- /navbar-inner --> 
</div><!-- /navbar -->  

<div class="account-container">
    <div class="content clearfix">
	<!--<form>--->
            <h1>Reset Password</h1>		
            <div class="login-fields">
		<p>Please provide your details</p>
		<div class="field">
                    <label for="username" style="padding-bottom: 5px;">User ID :</label>
                    <input type="text" id="userId" name="userId" value="" placeholder="User ID" class="login username-field" maxlength="8" />
		</div> <!-- /field -->
		<div class="field">
                    <label for="email">Email :</label>
                    <input type="email" id="email" name="email" value="" placeholder="Email" class="login password-field"/>
		</div> <!-- /password -->		
            </div> <!-- /login-fields -->
            <div class="login-actions">
		<button class="button btn btn-primary btn-large" id="btn-save">SAVE</button>
            </div> <!-- .actions -->
	<!--</form>--->
    </div> <!-- /content -->
    <div class="content clearfix">
        <div class="alert" id="mandatory-msg-1" style="display: none">
            <strong>Mandatory!</strong> Please input user id and email.
        </div>
        <div class="alert" id="mandatory-msg-2" style="display: none">
            <strong>Existence Error!</strong> Email does not match for this user ID.
        </div>
        <div class="alert" id="undefined-msg" style="display: none">
            <strong>Undefined Error!</strong> System Error occurs. Please report to system administrator.
        </div>
        <div class="alert alert-success" id="save-msg" style="display: none">
            <strong>Success!</strong> Reset password success. Please check your email.
        </div>
    </div>  
</div> <!-- /account-container -->
<br/>
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

<div id="dialog-confirm-save" title="Confirm" style="display: none;"></div>
    </body>
</html>