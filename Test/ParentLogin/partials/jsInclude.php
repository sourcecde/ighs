<script src="./js/jquery-1.7.2.min.js"></script>
<script src="./js/bootstrap.js"></script>
<?php
	$flag=false;
	
	if(isset($_SESSION['SchoolDetails'])){
		$flag=true;
		$uri= $_SERVER['REQUEST_URI'];
		$address=$_SESSION['SchoolDetails']['Address'];
		if (strpos($uri, 'lakshya_green_an') !== false) {
			if(!(strpos($address, 'Aswininagar') !== false)){
				$flag=false;
			}
		}
		elseif (strpos($uri, 'lakshya_green_jm') !== false){
			if(!(strpos($address, 'Joramandir') !== false)){
				$flag=false;
			}
		}
	}
	
	
	if($flag){
		echo "<script>$('#school-address').html('".$_SESSION['SchoolDetails']['Address']."')</script>";
		echo "<script>$('#school-phone').html('".$_SESSION['SchoolDetails']['ContactNo']."')</script>";
	}
	else{
?>
		<script>
			$(function(){
				$.ajax({
					url:'./Core/getSchoolYears.php',
					data:{'action':'getSchoolDetails'},
					method: "GET",
					success: function(data){
						var details=JSON.parse(data);
						$('#school-address').html(details.Address);
						$('#school-phone').html(details.ContactNo);
					}
				});
			})
		</script
<?php
	}
?>
