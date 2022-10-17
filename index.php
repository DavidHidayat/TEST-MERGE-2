
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" href="css/bootstrap.min.css" ></link>
        <link rel="stylesheet" href="css/bootstrap-responsive.min.css"></link>
        <link rel="stylesheet" href="css/font-awesome.css">
        <link rel="stylesheet" href="css/style.css" ></link>
        <link rel="stylesheet" href="css/dashboard.css" ></link>
        <link rel="stylesheet" href="css/pages/signin.css" type="text/css">
        <link rel="stylesheet" href="css/additional.css" ></link>
        <link rel="stylesheet" href="lib/jquery/jquery-ui-1.11.2.custom/jquery-ui.css">
        
        <script src="lib/jquery/jquery-1.11.0.min.js"></script> 
        <script src="lib/jquery/jquery-ui-1.11.2.custom/jquery-ui.min.js"></script> 
        <script src="lib/jquery/jquery-ui-1.11.2.custom/jquery-ui.js"></script> 
        <script type="text/javascript" src="js/Common.js"></script>
        <script type="text/javascript" src="js/Common_JQuery.js"></script>
        <script type="text/javascript" src="js/index.js"></script>
        <script src="js/signin.js"></script>
        <script>
            maximize();
        </script>
        <title>EPS</title>
        <style>
            body {
            background-image: url("eps2.png");
            }
        </style>
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
        </div><!-- /container --> 
    </div><!-- /navbar-inner --> 
</div><!-- /navbar -->  

<div class="account-container">
    <div class="content clearfix">
	<!--<form>--->
            <h1>Member Login</h1>		
            <div class="login-fields">
		<p>Please provide your details</p>
		<div class="field">
                    <label for="username" style="padding-bottom: 5px;">User ID :</label>
                    <input type="text" id="userId" name="userId" value="" placeholder="User ID" class="login username-field" maxlength="8" />
		</div> <!-- /field -->
		<div class="field">
                    <label for="password">Password :</label>
                    <input type="password" id="password" name="password" value="" placeholder="Password" class="login password-field"/>
		</div> <!-- /password -->		
            </div> <!-- /login-fields -->
            <div class="login-actions">
		<!--<span class="login-checkbox">
                    <input id="Field" name="Field" type="checkbox" class="field login-checkbox" value="First Choice" tabindex="4" />
                    <label class="choice" for="Field">Keep me signed in</label>
                </span>-->
		<button class="button btn btn-success btn-large" id="btn-sign-in">Sign In</button>
            </div> <!-- .actions -->
	<!--</form>--->
    </div> <!-- /content -->
    <div class="content clearfix">
        <div class="alert" id="mandatory-msg-1" style="display: none">
            <strong>Mandatory!</strong> Please input user id and password.
        </div>
        <div class="alert" id="mandatory-msg-2" style="display: none">
            <strong>Existence Error!</strong> User ID does not exist.
        </div>
        <div class="alert" id="mandatory-msg-3" style="display: none">
            <strong>Existence Error!</strong> User ID and password does not match.
        </div>
        <div class="alert" id="mandatory-msg-4" style="display: none">
            <strong>Existence Error!</strong> User ID does not active.
        </div>
        <div class="alert" id="mandatory-msg-5" style="display: none">
            <strong>Existence Error!</strong> Employee ID does not exist.
        </div>
        <div class="alert" id="mandatory-msg-6" style="display: none">
            <strong>Existence Error!</strong> Employee ID does not active.
        </div>
        <div class="alert" id="undefined-msg" style="display: none">
            <strong>Undefined Error!</strong> System Error occurs. Please report to system administrator.
        </div>
    </div>  
</div> <!-- /account-container -->

<div class="login-extra" >
	<a href="ecom/WCOM001.php" style="color: green">Forgot Password?</a>
</div> <!-- /login-extra -->

<!--<div class="footer">
    <div class="footer-inner">
	<div class="container">
            <div class="row">
		<div class="span12">
                    &copy; 2018 PT. TD AUTOMOTIVE COMPRESSOR INDONESIA. All rights reserved. 
                </div>  /span12 	
            </div>  /row 
	</div>  /container 		
    </div>  /footer-inner 	
</div>  /footer -->
    </body>
</html>