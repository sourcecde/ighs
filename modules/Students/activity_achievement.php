<?php
function fetchProgressAchievement($connection2,$personID){
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
						$progress.="<div><table width='100%'><tr><th>Date</th><th>Progress</th></tr>";
						foreach($pl as $p){
							$date=dateFormatter($p['date']);
							$progress.="<tr><td>$date</td><td>{$p['progress']}</td></tr>";
						}
						$progress.="</table></div>";
					}
				}
					$progress.="<h5>Enrolment History</h5>";
					$progress.="<div><table width='100%'><tr><th>Start Date</th><th>Entry Level</th><th>Remark</th></tr>";
						$date=dateFormatter($m['startDate']);
					$progress.="<tr><td>$date</td><td>{$m['entryLevel']}</td><td>{$m['remark']}</td></tr>";
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
				$achievement.="<tr><th>Date</th><th>Competition</th><th>Activity</th><th>Achievement</th></tr>";
				foreach($al as $a){
					$date=dateFormatter($a['date']);
					$achievement.="<tr><td rowspan='2'>$date</td><td>{$a['name']}</td><td>{$a['activityName']}</td><td>{$a['remarks']}</td></tr>";
				}
				$achievement.="</table>";
				$achievement.="</div>";
			}
		}
		else{
			$achievement="<b>No achievement found</b>";
		}
		//echo $achievement;
		return array('Progress'=>$progress,'Achievement'=>$achievement);
		/* Achievement */
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