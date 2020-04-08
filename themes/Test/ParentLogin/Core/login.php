<?php 
session_start();
if (file_exists("./dbCon.php")) {
	include "./dbCon.php" ;
}
$homeUrl="../";
$loginUrl="../login.php";
if($_POST){
	extract($_POST);
	echo $username;	
	$error=0;
	try{
	$sql="SELECT `passwordStrongSalt` FROM `gibbonperson` WHERE username='$username'";
	$result=$connection1->prepare($sql);
	$result->execute();
	}
	catch(PDOException $e) {
	  echo $e->getMessage();
	}
	if($result->rowCount()>0){
		$auth=$result->fetch();
		$salt=$auth['passwordStrongSalt'];
		$passwordStrong=hash("sha256", $salt.$password);
		try{
		$sql1="SELECT `gibbonPersonID`,`preferredName`,`gender`,`username`,`passwordStrongSalt`,`image_240`,`account_number` 
		FROM `gibbonperson` WHERE username='$username' AND passwordStrong='$passwordStrong'";
		$result1=$connection1->prepare($sql1);
		$result1->execute();
		}
		catch(PDOException $e) {
		  echo $e->getMessage();
		}
		if($result1->rowCount()>0){
			//Authenticated
			$user=$result1->fetch();
			
			$_SESSION['user']=$user;
			$_SESSION['user']['image_240']=$_SESSION['user']['image_240']!=''?$ImageURL.$_SESSION['user']['image_240']:'';
			$gibbonPersonID=$_SESSION['user']['gibbonPersonID']+0;
			try{
			$sql="SELECT `gibbonyeargroup`.`name` class,`gibbonrollgroup`.`name` section,`gibbonstudentenrolment`.`rollOrder`
					FROM `gibbonstudentenrolment` 
					JOIN `gibbonschoolyear` ON `gibbonstudentenrolment`.`gibbonSchoolYearID`=`gibbonschoolyear`.`gibbonSchoolYearID`
					JOIN `gibbonyeargroup` ON `gibbonyeargroup`.`gibbonYearGroupID`=`gibbonstudentenrolment`.`gibbonYearGroupID`
					JOIN `gibbonrollgroup` ON `gibbonrollgroup`.`gibbonRollGroupID`=`gibbonstudentenrolment`.`gibbonRollGroupID`
					WHERE `gibbonstudentenrolment`.`gibbonPersonID`=$gibbonPersonID AND  `gibbonschoolyear`.`status`='Current'";
			$result=$connection1->prepare($sql);
			$result->execute();
			$profile=$result->fetch();
			}
			catch(PDOException $e) {
			  echo $e->getMessage();
			}
			$_SESSION['profile']=$profile;
			header('Location: '.$homeUrl);
		}
		else{
			//Invalid paaword
			$error=2;
		}
	}
	else{
		//Username doesn't exist
		$error=1;
	}
	if($error>0){
		header('Location: '.$loginUrl.'?error='.$error);
	}
}
?>