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
	if($action=='createTimetable'){
		if(isset($data)){
			try{
			$sql="SELECT COUNT(`timetableID`) AS N FROM `lakshya_activity_timetable_master` WHERE `gibbonRollGroupID`=$rollID";
			$result=$connection2->prepare($sql);
			$result->execute();
			$n=$result->fetch();
			}
			catch(PDOException $e){
				echo $e;
			}
			if($n['N']>0){
				echo "Timetable Already created for selected section!!!";
			}
			else{
				try{
				$sql="INSERT INTO `lakshya_activity_timetable_master`(`timetableID`, `gibbonRollGroupID`) VALUES (NULL,$rollID)";
				$result=$connection2->prepare($sql);
				$result->execute();
				$id=$connection2->lastInsertId();
				}
				catch(PDOException $e){
					echo $e;
				}
				$sql="INSERT INTO `lakshya_activity_timetable_data`(`timetableDataID`, `timetableID`, `row`, `col`, `activityID`) VALUES ";
				$q="";
				foreach($data as $row=>$c){
					foreach($c as $col=>$activityID){
						$q.=$q!=""?", ":"";
						$q.="(NULL,$id,$row,$col,$activityID)";
					}
				}
				try{
				$sql.=$q;
				$result=$connection2->prepare($sql);
				$result->execute();
				echo "Created Successfully!!";
				}
				catch(PDOException $e){
					echo $e;
				}
			}
		}
		else
			echo "Timetableable is Empty";
	}
	else if($action=='updateTimetable'){
		try{
		$sql="SELECT `row`,`col`,`activityID` FROM `lakshya_activity_timetable_data` WHERE `timetableID`=$timetableID";
		$result=$connection2->prepare($sql);
		$result->execute();
		$tData=$result->fetchAll();
		}
		catch(PDOException $e){
			echo $e;
		}
		$timetableData=array();
		foreach($tData as $t){
			$timetableData[$t['row']][$t['col']]=$t['activityID'];
		}
		$sql="";
		foreach($data as $row=>$c){
			foreach($c as $col=>$activityID){
				$flag=true;
				if(array_key_exists($row,$timetableData)){
					if(array_key_exists($col,$timetableData[$row])){
						if($activityID!=$timetableData[$row][$col]){
							if($activityID!=0)
								$sql.="UPDATE `lakshya_activity_timetable_data` SET `activityID`=$activityID WHERE `row`=$row AND `col`=$col AND `timetableID`=$timetableID;  ";
							else	
								$sql.="DELETE FROM `lakshya_activity_timetable_data`  WHERE `row`=$row AND `col`=$col AND `timetableID`=$timetableID;  ";
						}
					}
					else
						$flag=false;
				}
				else
					$flag=false;
				
				if(!$flag){
					$sql.="INSERT INTO `lakshya_activity_timetable_data`(`timetableDataID`, `timetableID`, `row`, `col`, `activityID`) VALUES (NULL,$timetableID,$row,$col,$activityID); ";
				}	
			}
		}
		try{
		//echo $sql;
		$result=$connection2->prepare($sql);
		$result->execute();
		echo "Updated Successfully!!";
		}
		catch(PDOException $e){
			echo $e;
		}
	}
}
?>