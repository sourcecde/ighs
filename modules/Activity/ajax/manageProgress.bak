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
	if($action=='fetchProgressAchievement'){
		/* Progress */
		try{
		$sql1="SELECT `lakshya_activity_master`.*,`activityName` FROM `lakshya_activity_master` 
				LEFT JOIN `lakshya_activity_activities` ON `lakshya_activity_activities`.`activityID`=`lakshya_activity_master`.`activityID` 
				WHERE `personID`=$personID";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		$masterD=$result1->fetchAll();
		}
		catch(PDOException $e) { 
			echo $e;
		}
		echo $masterIDs="";
		foreach($masterD as $m){
			$masterIDs.=$masterIDs!=""?",":"";
			$masterIDs.=$m['activityMasterID'];
		}
		if($masterIDs!=""){
			try{
			$sql1="SELECT `lakshya_activity_progress`.*,`gibbonschoolyear`.`name` as Year FROM `lakshya_activity_progress` 
					LEFT JOIN `gibbonstudentenrolment` ON `gibbonstudentenrolment`.`gibbonStudentEnrolmentID`=`lakshya_activity_progress`.`enrolmentID` 
					LEFT JOIN `gibbonschoolyear` ON `gibbonschoolyear`.`gibbonSchoolYearID`=`gibbonstudentenrolment`.`gibbonSchoolYearID` 
					WHERE `activityMasterID` IN ($masterIDs) ORDER BY `date`";
			$result1=$connection2->prepare($sql1);
			$result1->execute();
			$progressD=$result1->fetchAll();
			}
			catch(PDOException $e) { 
				echo $e;
			}
			$progressData=array();
			foreach($progressD as $p){
				$progressData[$p['activityMasterID']][$p['Year']][]=$p;
			}
			$progress="";
			foreach($masterD as $m){
				$progress.="<h1>{$m['activityName']}</h1>";
				$progress.="<div class='collapse'>";
				if(array_key_exists($m['activityMasterID'],$progressData)){
					foreach($progressData[$m['activityMasterID']] as $year=>$pl){
						$progress.="<h1>$year</h1>";
						$progress.="<div><table width='100%'><tr><th>Date</th><th>Progress</th><th>Action</th></tr>";
						foreach($pl as $p){
							$date=dateFormatter($p['date']);
							$action="<a class='editProgress' id='e_{$p['progressID']}'>Edit</a> | <a class='deleteProgress' id='d_{$p['progressID']}'>Delete</a>";
							$progress.="<tr><td>$date</td><td>{$p['progress']}</td><td>$action</td></tr>";
						}
						$progress.="</table></div>";
					}
				}
					$progress.="<h5>Enrolment History</h5>";
					$progress.="<div><table width='100%'><tr><th>Start Date</th><th>Entry Level</th><th>Remark</th><th>Action</th></tr>";
						$date=dateFormatter($m['startDate']);
						$edit="<a class='editEnrolment' id='am_{$m['activityMasterID']}'>Edit</a>";
					$progress.="<tr><td>$date</td><td>{$m['entryLevel']}</td><td>{$m['remark']}</td><td>$edit</td></tr>";
					$progress.="</table></div>";
				$progress.="</div>";
			}
			//echo $progress;
		}
		else
			$progress="<b>No activity records Found</b>";
		/* Progress */
		//echo "_break_";
		/* Achievement */
		try{
		$sql1="SELECT `lakshya_activity_achievement`.*,`gibbonschoolyear`.`name` AS Year,`activityName` FROM `lakshya_activity_achievement`
				LEFT JOIN `gibbonstudentenrolment` ON `gibbonstudentenrolment`.`gibbonStudentEnrolmentID`=`lakshya_activity_achievement`.`enrolmentID` 
				LEFT JOIN `gibbonschoolyear` ON `gibbonschoolyear`.`gibbonSchoolYearID`=`gibbonstudentenrolment`.`gibbonSchoolYearID` 
				LEFT JOIN `lakshya_activity_activities` ON `lakshya_activity_activities`.`activityID`=`lakshya_activity_achievement`.`activityID` 
				LEFT JOIN `gibbonperson` ON `gibbonperson`.`gibbonPersonID`=`gibbonstudentenrolment`.`gibbonPersonID` 
				WHERE `gibbonperson`.`gibbonPersonID`=$personID";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		$achvD=$result1->fetchAll();
		}
		catch(PDOException $e) { 
			echo $e;
		}
		$achvdData=array();
		$i=0;
		foreach($achvD as $a){
			$achvdData[$a['Year']][]=$a;
			$i++;
		}
		$achievement="";
		if($i>0){
			foreach($achvdData as $year=>$al){
				$achievement.="<h5>$year</h5>";
				$achievement.="<div>";
				$achievement.="<table width='100%'>";
				$achievement.="<tr><th rowspan='2'>Date</th><th>Competition</th><th>Activity</th><th rowspan='2'>Action</th></tr>
									<tr><th colspan='2'>Achievement</th></tr>";
				foreach($al as $a){
					$date=dateFormatter($a['date']);
					$action="<a class='editAchievement' id='ea_{$a['achievementID']}'>Edit</a> | <a class='deleteAchievement' id='da_{$a['achievementID']}'>Delete</a>";
					$achievement.="<tr><td rowspan='2'>$date</td><td>{$a['name']}</td><td>{$a['activityName']}</td><td rowspan='2'>$action</td></tr>
									<tr><td colspan='2'>{$a['remarks']}</td></tr>";
				}
				$achievement.="</table>";
				$achievement.="</div>";
			}
		}
		else{
			$achievement="<b>No achievement found</b>";
		}
		//echo $achievement;
		echo json_encode(array('Progress'=>$progress,'Achievement'=>$achievement));
		/* Achievement */
	}
	else if($action=='fetchMasterData'){
		try{
		$sql1="SELECT * FROM `lakshya_activity_master` WHERE `activityMasterID`=$masterID";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		$masterD=$result1->fetch();
		}
		catch(PDOException $e) { 
			echo $e;
		}
		echo json_encode($masterD);
	}
	else if($action=='fetchProgressData'){
		try{
		$sql1="SELECT  `date`, `progress` FROM `lakshya_activity_progress` WHERE `progressID`=$progressID";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		$progressD=$result1->fetch();
		}
		catch(PDOException $e) { 
			echo $e;
		}
		echo json_encode($progressD);
	}
	else if($action=='fetchAchievementData'){
		try{
		$sql1="SELECT  `activityID`, `date`, `name`, `remarks` FROM `lakshya_activity_achievement` WHERE `achievementID`=$achievementID";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		$achievementD=$result1->fetch();
		}
		catch(PDOException $e) { 
			echo $e;
		}
		echo json_encode($achievementD);
	}
	else if($action=='updateMasterData'){
		extract($data);
		$date=dateFormatterR($startDate);
		try{
		$sql1="UPDATE `lakshya_activity_master` SET `startDate`='$date',`entryLevel`='$entryLevel',`remark`='$remark' WHERE `activityMasterID`=$activityMasterID";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		echo "Updated Sucessfully!!";
		}
		catch(PDOException $e) { 
			echo $e;
		}
	}
	else if($action=='updateProgressData'){
		extract($data);
		$date=dateFormatterR($date);
		try{
		$sql1="UPDATE `lakshya_activity_progress` SET `date`='$date',`progress`='$progress' WHERE `progressID`=$progressID";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		echo "Updated Sucessfully!!";
		}
		catch(PDOException $e) { 
			echo $e;
		}
	}
	else if($action=='updateAchievementData'){
		extract($data);
		$date=dateFormatterR($date);
		try{
		$sql1="UPDATE `lakshya_activity_achievement` SET `activityID`=$activityID,`date`='$date',`name`='$name',`remarks`='$remarks' WHERE `achievementID`=$achievementID";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		echo "Updated Sucessfully!!";
		}
		catch(PDOException $e) { 
			echo $e;
		}
	}
}
function dateFormatter($d){
	$tmp=explode("-",$d);
	return $tmp[2]."/".$tmp[1]."/".$tmp[0];
}
function dateFormatterR($d){
	$tmp=explode("/",$d);
	return $tmp[2]."-".$tmp[1]."-".$tmp[0];
}
 ?>