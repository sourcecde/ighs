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
	if($action=='addProgressAchievement'){
		extract($data);
		$date=dateFormatter($date);
		if($type=='P'){
			try{
			$sql1="INSERT INTO `lakshya_activity_progress`(`progressID`, `activityMasterID`, `enrolmentID`, `date`, `progress`)  
					VALUES (NULL,$activityMasterID,$enrolmentID,'$date','$remark')";
			$result1=$connection2->prepare($sql1);
			$result1->execute();
			echo "Added Successfully!!";
			}
			catch(PDOException $e) { 
			echo $e;
			}
		}
		else if($type=='A'){
			try{
			$sql1="INSERT INTO `lakshya_activity_achievement`(`achievementID`, `enrolmentID`, `activityID`, `date`, `name`, `remarks`)
					VALUES (NULL,$enrolmentID,$activityID,'$date','$name','$remark')";
			$result1=$connection2->prepare($sql1);
			$result1->execute();
			echo "Added Successfully!!";
			}
			catch(PDOException $e) { 
			echo $e;
			}
		}
	}
}
function dateFormatter($d){
	$tmp=explode("/",$d);
	return $tmp[2]."-".$tmp[1]."-".$tmp[0];
}
 ?>