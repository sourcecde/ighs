<?php
	$maleImgSrc='./img/male.jpg';
	$femaleImgSrc='./img/female.png';
	$ImgSrc=$_SESSION['user']['gender']=="M"?$maleImgSrc:$femaleImgSrc;
	$photoSrc=file_exists($_SESSION['user']['image_240'])?$_SESSION['user']['image_240']:$ImgSrc;
?>
<div class="plan green">
	<div class="plan-header">
		<div class="plan-title">
			<?=$_SESSION['user']['preferredName']?>	        		
		</div> 
		<div class="plan-price">
			<img src="<?=$photoSrc?>" class="logo-img img-circle" alt="student photo" width="100" height="100">
		</div> 
	</div> 
	<div class="plan-features">
		<ul>					
			<li><strong>Class : </strong> <?=$_SESSION['profile']['class']?> </li>
			<li><strong>Section : </strong> <?=$_SESSION['profile']['section']?>  </li>
			<?php if($_SESSION['profile']['rollOrder']!=""){?>
			<li><strong>Roll : </strong> <?=$_SESSION['profile']['rollOrder']?>  </li>
			<?php } ?>
			<li><strong>Acount No : </strong> <?=$_SESSION['user']['account_number']+0?> </li>
		</ul>
	</div> 
	<div class="plan-actions">				
		<a href="./payment.php" class="btn btn-info">Pay Fees</a>			
	</div>
</div>