<?php
session_start();
if(!isset($_SESSION['user'])){
	$loginUrl="./login.php";
	header('Location: '.$loginUrl);
}
else if(!isset($_SESSION["PaymentResponse"])){
	$homeUrl="./";
	header('Location: '.$homeUrl);
}
$order_status=$_SESSION["PaymentResponse"]["order_status"];
$bank_ref_no=$_SESSION["PaymentResponse"]["bank_ref_no"];
unset($_SESSION["PaymentResponse"]);
print_r($_SESSION['refData']);
$isSecurityIssue=false;
if(isset($_SESSION['security'])){
	$isSecurityIssue=true;	
}
unset($_SESSION['security']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Lakshya</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes"> 
	<link href="./css/pages/plans.css" rel="stylesheet" type="text/css">
	<?php include('./partials/cssInclude.php')?>
</head>
<body>
<?php include('./partials/navbar.php') ?>
<div class="container">
	
	<div class="row">
		
		<div class="span12">
		<?php
		if($isSecurityIssue){
			?>
			<div class="error-container">
				<h1><i class="icon-large icon-remove-circle" style="color:#db3325"></i></h1>
				<h2>Security Error!</h2>
				
				<div class="error-actions">
					<a href="./" class="btn btn-large btn-primary">
						<i class="icon-chevron-left"></i>
						&nbsp;
						Dashboard						
					</a>
					
					
					
				</div> <!-- /error-actions -->
							
			</div> <!-- /error-container -->
			<?php
		}
		else if($order_status=='Success')
		{ 
		?>
			<div class="error-container">
				<h1><i class="icon-large icon-ok-sign" style="color:#00ba8b"></i></h1>
				<h2>Payment has been successful!</h2>
				<h2>Reference No: <?=$bank_ref_no?></h2>
				<div class="error-details">
					Your fee details has been updated. Thank you.
					
				</div> <!-- /error-details -->
				
				<div class="error-actions">
					<a href="./" class="btn btn-large btn-primary">
						<i class="icon-chevron-left"></i>
						&nbsp;
						Dashboard						
					</a>
					
					
					
				</div> <!-- /error-actions -->
							
			</div> <!-- /error-container -->			
		<?php
		}
		else if($order_status=="Aborted"){
		?>
			<div class="error-container">
				<h1><i class="icon-large icon-question-sign" style="color:#f5a732"></i></h1>
				<h2>Unable to process payment now!</h2>
				<div class="error-details">
					Please try to make <a href="./payment.php">payment</a> again. Thank You!
				</div> <!-- /error-details -->
				
				<div class="error-actions">
					<a href="./" class="btn btn-large btn-primary">
						<i class="icon-chevron-left"></i>
						&nbsp;
						Dashboard						
					</a>
					
					
					
				</div> <!-- /error-actions -->
							
			</div> <!-- /error-container -->
		<?php
		}
		else if($order_status=="Failure"){
		?>
			<div class="error-container">
				<h1><i class="icon-large icon-remove-circle" style="color:#db3325"></i></h1>
				<h2>Payment has been declined!</h2>
				<h2>Reference No: <?=$bank_ref_no?></h2>
				<div class="error-details">
					Please try to make <a href="./payment.php">payment</a> again. Thank You!
				</div> <!-- /error-details -->
				
				<div class="error-actions">
					<a href="./" class="btn btn-large btn-primary">
						<i class="icon-chevron-left"></i>
						&nbsp;
						Dashboard						
					</a>
					
					
					
				</div> <!-- /error-actions -->
							
			</div> <!-- /error-container -->
		<?php
		}
		else{
		?>
			<div class="error-container">
				<h1><i class="icon-large icon-remove-circle" style="color:#db3325"></i></h1>
				<h2>Security Error!</h2>
				
				<div class="error-actions">
					<a href="./" class="btn btn-large btn-primary">
						<i class="icon-chevron-left"></i>
						&nbsp;
						Dashboard						
					</a>
					
					
					
				</div> <!-- /error-actions -->
							
			</div> <!-- /error-container -->
		<?php
		}
		?>
		</div> <!-- /span12 -->
		
	</div> <!-- /row -->
	
</div>
<?php include('./partials/jsInclude.php')?>
</body>
</html>
