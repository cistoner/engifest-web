<?php
	session_start();
	$checkVar = true;
	include 'config/config.php';
	include 'libs/error.php';
	include 'libs/db.php';
	include 'libs/login.php';
	
	/** 
	 * code to check if session already exists 
	 * and redirect to index.php if it passes authentication result
	 * or clear session in case of failed authentication
	 */
	if( doesSessionAlreadyExists() )
	{
		$status = null;
		dbase::start_connection();
		try
		{
			
			$status = login::authenticate($_SESSION[username_key],$_SESSION[password_key]);
			
		}
		catch(dbError $ex)
		{
			/** 
		 	 * for debugging: shall be changed to some action later
			 */
			echo $ex->getMessage();
			exit;
		}
		catch(Exception $ex)
		{
			if(isset($_SESSION[username_key])) unset($_SESSION[username_key]);
			if(isset($_SESSION[password_key])) unset($_SESSION[password_key]);
			header("location: login.php?message=sesion+expired");
			exit;
		}
		switch($status)
		{
			case 0:	clearSession(); break;
			case 1:	clearSession(); break;
			case 2: header("location: index.php");
					exit;
					break;
		}
		dbase::close_connection();
	}
	
	/**
	 * code to decide weather to show captcha or not
	 * depending upon no of failed login
	 */
	$showCaptcha = false;
	if(isset($_SESSION['failure']) && $_SESSION['failure'] > login::$maxInvalidAttempts)
	{
		$showCaptcha = true;
	}
	
	/**
	 * code to decide weather login failed
	 */
	$hasLoginFailed = false;
	$errorMessage = null;
	if(isset($_GET['success']) && $_GET['success'] == 'false')
	{
		$hasLoginFailed = true;
		$errorMessage = "Invalid Username or Password";
		if(isset($_GET['message'])) $errorMessage = $_GET['message'];
	}
	
	/**
	 * code to set login redirect location in case it is session redirect
	 */
	 if(isset($_GET['at']))
	 {
		$_SESSION['login_location'] = $_GET['at'] .".php";
	 }
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>MMT control panel</title>
	<!-- The styles -->
	<link id="bs-css" href="css/bootstrap-cerulean.css" rel="stylesheet">
	<style type="text/css">
	  body {
		padding-bottom: 40px;
		}
	  .sidebar-nav {
		padding: 9px 0;
	  }
	</style>
	<link href="css/bootstrap-responsive.css" rel="stylesheet">
	<link href="css/charisma-app.css" rel="stylesheet">
	<link href="css/jquery-ui-1.8.21.custom.css" rel="stylesheet">
	<link href='css/fullcalendar.css' rel='stylesheet'>
	<link href='css/fullcalendar.print.css' rel='stylesheet'  media='print'>
	<link href='css/chosen.css' rel='stylesheet'>
	<link href='css/uniform.default.css' rel='stylesheet'>
	<link href='css/colorbox.css' rel='stylesheet'>
	<link href='css/jquery.cleditor.css' rel='stylesheet'>
	<link href='css/jquery.noty.css' rel='stylesheet'>
	<link href='css/noty_theme_default.css' rel='stylesheet'>
	<link href='css/elfinder.min.css' rel='stylesheet'>
	<link href='css/elfinder.theme.css' rel='stylesheet'>
	<link href='css/jquery.iphone.toggle.css' rel='stylesheet'>
	<link href='css/opa-icons.css' rel='stylesheet'>
	<link href='css/uploadify.css' rel='stylesheet'>

	<!-- The HTML5 shim, for IE6-8 support of HTML5 elements -->
	<!--[if lt IE 9]>
	  <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<!-- The fav icon -->
	<link rel="shortcut icon" href="img/favicon.ico">
		
</head>

<body>
		<div class="container-fluid">
		<div class="row-fluid">
		
			<div class="row-fluid">
				<div class="span12 center login-header">
					<h2>Welcome to Mail Management Tool</h2>
				</div><!--/span-->
			</div><!--/row-->
			
			<div class="row-fluid">
				<div class="well span5 center login-box">
					<div class="alert alert-info">
						Please login with your Username and Password.
					</div>
					
						<?php 
						if($hasLoginFailed){
						?>
						<div class="alert alert-error">
							<?php echo $errorMessage; ?>
						</div>
						<?php } ?>
					
					<form class="form-horizontal" action="secure/processlogin.php" method="post">
						<fieldset>
							<div class="input-prepend" title="Username" data-rel="tooltip">
								<span class="add-on"><i class="icon-user"></i></span><input required autofocus class="input-large span10" name="username" id="username" type="text" value="admin" />
							</div>
							<div class="clearfix"></div>

							<div class="input-prepend" title="Password" data-rel="tooltip">
								<span class="add-on"><i class="icon-lock"></i></span><input  required class="input-large span10" name="password" id="password" type="password" value="admin123456" />
							</div>
							<div class="clearfix"></div>
							<?php 
							if($showCaptcha)
							{
							?>
							<div class="alert alert-info">
								Are you a real human?
							</div>
							captcha code here
							<div class="clearfix"></div>
							<?php 
							}
							?>
							<p class="center span5">
							<button type="submit" class="btn btn-primary">Login</button>
							<br>
							<div class="forgotPasswordBox" onclick="javascript: $('.resetpasswordbox').slideDown();$('.forgotPasswordBox').slideUp();">
								<a href="#" >Forgot password ? </a>
							</div>
							</p>
						</fieldset>
					</form>
					<div class="resetpasswordbox" style="display: none">
						<div class="alert alert-info">
							Forgot your password, Reset with secret PIN we mailed you!
						</div>
						<form class="form-horizontal" action="resetAccount.php" method="post">
							<fieldset>
								<div class="input-prepend" title="Pin" data-rel="tooltip">
									<span class="add-on"><i class="icon-lock"></i></span><input class="input-large span10" name="pin" id="pin" type="password" value="secretpin" />
								</div>
								<p class="center span5">
									<button type="submit" class="btn btn-primary">Reset Account</button>
								</p>
							</fieldset>
						</form>
					</div>
				</div><!--/span-->
			</div><!--/row-->
				</div><!--/fluid-row-->
		
	</div><!--/.fluid-container-->

	<!-- external javascript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->

	<!-- jQuery -->
	<script src="js/jquery-1.7.2.min.js"></script>
	<!-- jQuery UI -->
	<script src="js/jquery-ui-1.8.21.custom.min.js"></script>
	<!-- transition / effect library -->
	<script src="js/bootstrap-transition.js"></script>
	<!-- alert enhancer library -->
	<script src="js/bootstrap-alert.js"></script>
	<!-- modal / dialog library -->
	<script src="js/bootstrap-modal.js"></script>
	<!-- custom dropdown library -->
	<script src="js/bootstrap-dropdown.js"></script>
	<!-- scrolspy library -->
	<script src="js/bootstrap-scrollspy.js"></script>
	<!-- library for creating tabs -->
	<script src="js/bootstrap-tab.js"></script>
	<!-- library for advanced tooltip -->
	<script src="js/bootstrap-tooltip.js"></script>
	<!-- popover effect library -->
	<script src="js/bootstrap-popover.js"></script>
	<!-- button enhancer library -->
	<script src="js/bootstrap-button.js"></script>
	<!-- accordion library (optional, not used in demo) -->
	<script src="js/bootstrap-collapse.js"></script>
	<!-- carousel slideshow library (optional, not used in demo) -->
	<script src="js/bootstrap-carousel.js"></script>
	<!-- autocomplete library -->
	<script src="js/bootstrap-typeahead.js"></script>
	<!-- tour library -->
	<script src="js/bootstrap-tour.js"></script>
	<!-- library for cookie management -->
	<script src="js/jquery.cookie.js"></script>
	<!-- calander plugin -->
	<script src='js/fullcalendar.min.js'></script>
	<!-- data table plugin -->
	<script src='js/jquery.dataTables.min.js'></script>

	<!-- chart libraries start -->
	<script src="js/excanvas.js"></script>
	<script src="js/jquery.flot.min.js"></script>
	<script src="js/jquery.flot.pie.min.js"></script>
	<script src="js/jquery.flot.stack.js"></script>
	<script src="js/jquery.flot.resize.min.js"></script>
	<!-- chart libraries end -->

	<!-- select or dropdown enhancer -->
	<script src="js/jquery.chosen.min.js"></script>
	<!-- checkbox, radio, and file input styler -->
	<script src="js/jquery.uniform.min.js"></script>
	<!-- plugin for gallery image view -->
	<script src="js/jquery.colorbox.min.js"></script>
	<!-- rich text editor library -->
	<script src="js/jquery.cleditor.min.js"></script>
	<!-- notification plugin -->
	<script src="js/jquery.noty.js"></script>
	<!-- file manager library -->
	<script src="js/jquery.elfinder.min.js"></script>
	<!-- star rating plugin -->
	<script src="js/jquery.raty.min.js"></script>
	<!-- for iOS style toggle switch -->
	<script src="js/jquery.iphone.toggle.js"></script>
	<!-- autogrowing textarea plugin -->
	<script src="js/jquery.autogrow-textarea.js"></script>
	<!-- multiple file upload plugin -->
	<script src="js/jquery.uploadify-3.1.min.js"></script>
	<!-- history.js for cross-browser state change on ajax -->
	<script src="js/jquery.history.js"></script>
	<!-- application script for Charisma demo -->
	<script src="js/charisma.js"></script>
	
		
</body>
</html>
