<?php
@session_start() ;
//Including Global Functions & Dtabase Configuration.
include "../../functions.php" ;
include "../../config.php" ;
//New PDO DB connection
try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}
if(isset($_REQUEST)){
	//echo "<pre>";
	//print_r($_POST);
	//echo "</pre>";
	$url="";
	extract($_REQUEST);
	if($action=='addAttendance'){
		$sql="INSERT INTO `lakshya_activity_attendance`(`attendanceID`, `enrolmentID`, `activityID`, `date`, `type`) VALUES ";
		$i=0;
		foreach($attendance as $enrolmentID=>$type){
			if($i++!=0)
				$sql.=", ";
			$sql.="(NULL,$enrolmentID,$activityID,'$date','$type')";
		}
		try{
			$result=$connection2->prepare($sql);
			$result->execute();
			echo "Added Successfully!!";
		}
		catch(PDOException $e) { 
			echo $e;
		}
		echo $url=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/takeAttendance.php";
	}
	else if($action=='updateAttendance'){
		$sql="";
		foreach($attendance as $attendanceID=>$type){
			$sql.="UPDATE `lakshya_activity_attendance` SET `type`='$type' WHERE `attendanceID`=$attendanceID; ";
		}
		try{
			$result=$connection2->prepare($sql);
			$result->execute();
			echo "Updated Successfully!!";
		}
		catch(PDOException $e) { 
			echo $e;
		}
		$url=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/takeAttendance.php";
	}
	header("Location: {$url}");
}
 ?>