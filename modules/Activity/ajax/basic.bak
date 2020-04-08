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
	if($action=='sectionIDbyYearID'){
		try{
			$sql="SELECT `gibbonRollGroupID`,`name` FROM `gibbonrollgroup` 
			WHERE `gibbonSchoolYearID`=$yearID";
			$result=$connection2->prepare($sql);
			$result->execute();
			$sections=$result->fetchAll();
			$msg="";
			foreach($sections as $s){
				$msg.="<option value='{$s['gibbonRollGroupID']}'>{$s['name']}</option>";
			}
			echo $msg;
		}
		catch(PDOException $e){
			echo $e;
		}
	}
	else if($action=='fetchActivityMasterData'){
		try{
			$sql="SELECT `activityMasterID`,`activityName` 
					FROM `lakshya_activity_master` 
					LEFT JOIN `lakshya_activity_activities` ON `lakshya_activity_activities`.`activityID`=`lakshya_activity_master`.`activityID`
					WHERE `personID`=(SELECT `gibbonPersonID` FROM `gibbonstudentenrolment` WHERE `gibbonStudentEnrolmentID`=$enrolmentID)";
			$result=$connection2->prepare($sql);
			$result->execute();
			$activities=$result->fetchAll();
			$msg="";
			foreach($activities as $a){
				$msg.="<option value='{$a['activityMasterID']}'>{$a['activityName']}</option>";
			}
			echo $msg;
		}
		catch(PDOException $e){
			echo $e;
		}
	}

}
?>