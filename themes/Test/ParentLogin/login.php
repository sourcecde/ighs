<?php
if(empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off"){
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $redirect);
    exit();
}
session_start();
if(isset($_SESSION['user'])){
	$homeUrl="./";
	header('Location: '.$homeUrl);
}

$errorMsg="";
if(isset($_GET['error'])){
	switch($_GET['error']){
		case 1:
			$errorMsg="Username doesn't exists";
			break;
		case 2:
			$errorMsg="Incorrect password";
			break;
	}
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Lakshya</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes"> 
	<link href="css/pages/signin.css" rel="stylesheet" type="text/css">
	<?php include('./partials/cssInclude.php')?>
</head>

<body>
<?php include('./partials/navbar.php') ?>
	<div class="container">
        <div class="row">
            <img class="logo-img" src="./img/lakshyalogo.png" alt="Lakshya Logo" width="300" style="">
        </div>
    </div>
<div class="account-container">
	
	<div class="content clearfix">
		
		<form action="./Core/login.php" method="post" id="login-form">
		
			<h1>Parent Login</h1>		
			<div class="alert alert-danger hidden" id='validation-alert'>
			  <strong>Error!</strong><span id="validation-message"></span>
			</div>
			<div class="login-fields">
				
				<p>Please provide your details</p>
				
				<div class="field">
					<label for="username">Username</label>
					<input type="text" id="username" name="username" value="" placeholder="Username" class="login username-field" />
				</div> <!-- /field -->
				
				<div class="field">
					<label for="password">Password:</label>
					<input type="password" id="password" name="password" value="" placeholder="Password" class="login password-field"/>
				</div> <!-- /password -->
				
			</div> <!-- /login-fields -->
			<?php
			if($errorMsg!=""){
			?>
			<div class="alert alert-danger">
			  <strong>Error!</strong> <?=$errorMsg?>
			</div>
			<?php
			}
			?>
			<div class="login-actions">					
				<button class="button btn btn-success btn-large">Sign In</button>
			</div> <!-- .actions -->
	
		</form>
		
	</div> <!-- /content -->
	
</div> <!-- /account-container -->



<div class="login-extra">
	Managed by <a href="http://www.hirventures.com" target="_blank">H.I.R. Ventures</a>. Powered by <a target="_blank" href="http://gibbonedu.org">Gibbon</a>
</div> <!-- /login-extra -->
<?php include('./partials/jsInclude.php')?>

</body>

</html>
<script>
$("#login-form").on('submit',function(e){
	if($('#username').val().trim().length==0){
		$('#username').focus();
		$("#validation-alert").html("Please enter username");
		$("#validation-alert").removeClass('hidden');
		return false;
	}
	if($('#password').val().trim().length==0){
		$('#password').focus();
		$("#validation-alert").html("Please enter password");
		$("#validation-alert").removeClass('hidden');
		return false;
	}
})
$("#username,#password").on('blur', function(){
	if($(this).val().length==0){
		$(this).focus();
		$("#validation-alert").html("Please enter "+$(this).attr('id'));
		$("#validation-alert").removeClass('hidden');
	}
	else{
		$("#validation-alert").addClass('hidden');
	}
})
</script>