<?php
session_start();
if(!isset($_SESSION['user'])){
	$loginUrl="./login.php";
	header('Location: '.$loginUrl);
}

$errorMsg="";
$successMsg="";
if(isset($_GET['error'])){
	switch($_GET['error']){
		case 0:
			$successMsg="Password changed sucessfully.";
			break;
		case 1:
			$errorMsg="Please enter correct current password.";
			break;
		case 2:
			$errorMsg="Entered password and confirm password do not match.";
			break;
		case 3:
			$errorMsg="Entered password doesn't meet password policy.";
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

<div class="account-container" style="margin-top:20px;">
	
	<div class="content clearfix">
		
		<form action="./Core/changepassword.php" method="post" id="login-form">
		
			<h1>Change Password</h1>	
			<h5>The password policy stipulates that passwords must:</h5>
				<ul class="password-policy">
					<li>Contain at least one lowercase letter, and one uppercase letter.</li>
					<li>Contain at least one number.</li>
					<li>Must be at least 8 characters in length.</li>
				</ul>
			<div class="alert alert-danger hidden" id='validation-alert'>
			  <strong>Error!</strong> <span id="validation-message"></span>
			</div>
			<div class="login-fields">
				
				<div class="field">
					<label for="current-password">Current Password:</label>
					<input type="password" id="current-password" name="current_password" value="" placeholder="Current Password" class=" password-field"/>
				</div> <!-- /current-password -->
				<div class="field">
					<label for="new-password">New Password:</label>
					<input type="password" id="new-password" name="new_password" value="" placeholder="New Password" max-length="20" class=" password-field"/>
				</div> <!-- /New-password -->
				<div class="field">
					<label for="confirm-password">Confirm Password:</label>
					<input type="password" id="confirm-password" name="confirm_password" value="" placeholder="Confirm Password" max-length="20" class=" password-field"/>
				</div> <!-- /confirm-password -->
				
			</div> <!-- /login-fields -->
			<?php
			if($errorMsg!=""){
			?>
			<div class="alert alert-danger">
			  <strong>Error!</strong> <?=$errorMsg?>
			</div>
			<?php
			}
			else if($successMsg!=""){
			?>
			<div class="alert alert-success">
			  <strong>Success!</strong> <?=$successMsg?>
			</div>
			<?php
			}
			?>
			<div class="login-actions">					
				<button class="button btn btn-success btn-large">Change</button>
			</div> <!-- .actions -->
	
		</form>
		
	</div> <!-- /content -->
	
</div> <!-- /account-container -->
<a href="./" class="float-btn"><i class='icon-arrow-left'></i></a>

<?php include('./partials/jsInclude.php')?>

</body>

</html>
<script>
$("#login-form").on('submit',function(e){
	if(!CurrentPasswordValidation()){
		return false;
	}
	if(!NewPasswordValidation()){
		return false;
	}
	if(!ConfirmPasswordValidation()){
		return false;
	}
})

$("#new-password").on('change', function(){
	NewPasswordValidation();
})
$("#confirm-password").on('change', function(){
	ConfirmPasswordValidation();
}) 
$("#current-password").on('change', function(){
	CurrentPasswordValidation();
})
function NewPasswordValidation(){
	var pwd=$("#new-password").val();
	HideValidationError();
	if(pwd.trim()==""){
		ShowValidationError("Please enter new password.");
		return false;
	}
	else if(!PasswordPolicyChecker(pwd)){
		ShowValidationError("Entered password doesn't meet password policy.");
		return false;
	}
	return true;
}
function ConfirmPasswordValidation(){
	var pwd=$("#confirm-password").val();
	HideValidationError();
	if(pwd.trim()==""){
		ShowValidationError("Please enter confirm password.");
		return false;
	}
	else if(pwd!=$("#new-password").val()){
		ShowValidationError("Entered password and confirm password do not match.");
		return false;
	}
	else if(!PasswordPolicyChecker(pwd)){
		ShowValidationError("Entered password doesn't meet password policy.");
		return false;
	}
	return true;
}
function CurrentPasswordValidation(){
	var pwd=$("#current-password").val();
	HideValidationError();
	if(pwd.trim()==""){
		ShowValidationError("Please enter current password.");
		return false;
	}
	return true;
}

function PasswordPolicyChecker(string) { 
    return /[A-Z]+/.test(string) && /[a-z]+/.test(string) &&
    /[\d]/.test(string) && /\S{8,}/.test(string)
}
function ShowValidationError(message){
	$("#validation-message").text(message);
	$("#validation-alert").removeClass("hidden");
}
function HideValidationError(){
	$("#validation-alert").addClass("hidden");
	$("#validation-message").text("");
}
</script>