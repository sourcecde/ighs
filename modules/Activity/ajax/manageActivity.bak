<?php
@session_start() ;
//Including Global Functions & Dtabase Configuration.
include "../../../functions.php" ;
include "../../../config.php" ;

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
	extract($_REQUEST);
	if($action=='addActivity'){
		try{
		$sql1="INSERT INTO `lakshya_activity_activities`(`activityID`, `activityName`, `isPaid`) VALUES(NULL,'$activityName',$isPaid)";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		}
		catch(PDOException $e) { 
		echo $e;
		}
		echo "Added Sucessfully!!";
	}
	else if($action=='editActivity'){
		try{
		$sql1="UPDATE `lakshya_activity_activities` SET `activityName`='$activityName',`isPaid`=$isPaid WHERE `activityID`=$activityID";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		}
		catch(PDOException $e) { 
		echo $e;
		}
		echo "Updated Sucessfully!!";
	}
	else if($action=='deleteActivity'){
		try{
		$sql1="DELETE FROM `lakshya_activity_activities` WHERE `activityID`=$activityID";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		}
		catch(PDOException $e) { 
		echo $e;
		}
		echo "Deleted Sucessfully!!";
	}
}
 ?>