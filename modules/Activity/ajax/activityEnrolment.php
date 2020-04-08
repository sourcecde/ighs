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
	if($action=='addActivityEnrolment'){
		try{
		$sql1="SELECT COUNT(*) as C FROM `lakshya_activity_master` WHERE `personID`=$personID AND `activityID`=$activityID";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		$tmp=$result1->fetch();
		}
		catch(PDOException $e) { 
		echo $e;
		}
		if($tmp['C']==0){
			$startDate=dateFormatter($startDate);
			try{
			$sql1="INSERT INTO `lakshya_activity_master`(`activityMasterID`, `personID`, `activityID`, `startDate`, `endDate`, `entryLevel`, `remark`) 
									VALUES (NULL,$personID,$activityID,'$startDate',NULL,'$entryLevel','$remark')";
			$result1=$connection2->prepare($sql1);
			$result1->execute();
			}
			catch(PDOException $e) { 
			echo $e;
			}
			echo "Added Sucessfully!!";
		}
		else
			echo "Activity is already added for seleceted student!!";
	}
}
function dateFormatter($d){
	$tmp=explode("/",$d);
	return $tmp[2]."-".$tmp[1]."-".$tmp[0];
}
 ?>