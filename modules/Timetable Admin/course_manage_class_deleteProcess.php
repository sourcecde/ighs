<?php
/*
Gibbon, Flexible & Open School System
Copyright (C) 2010, Ross Parker

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

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

@session_start() ;

//Set timezone from session variable
date_default_timezone_set($_SESSION[$guid]["timezone"]);

$gibbonCourseClassID=$_GET["gibbonCourseClassID"] ;
$gibbonCourseID=$_GET["gibbonCourseID"] ;
$gibbonSchoolYearID=$_GET["gibbonSchoolYearID"] ;

if ($gibbonCourseID=="" OR $gibbonSchoolYearID=="") {
	print "Fatal error loading this page!" ;
}
else {

	$URL=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_POST["address"]) . "/course_manage_class_delete.php&gibbonCourseID=$gibbonCourseID&gibbonCourseClassID=$gibbonCourseClassID&gibbonSchoolYearID=$gibbonSchoolYearID" ;
	$URLDelete=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_POST["address"]) . "/course_manage_edit.php&gibbonCourseID=$gibbonCourseID&gibbonCourseClassID=$gibbonCourseClassID&gibbonSchoolYearID=$gibbonSchoolYearID" ;
	
	if (isActionAccessible($guid, $connection2, "/modules/Timetable Admin/course_manage_class_delete.php")==FALSE) {
		//Fail 0
		$URL.="&deleteReturn=fail0" ;
		header("Location: {$URL}");
	}
	else {
		//Proceed!
		//Check if school year specified
		if ($gibbonCourseClassID=="") {
			//Fail1
			$URL.="&deleteReturn=fail1" ;
			header("Location: {$URL}");
		}
		else {
			try {
				$data=array("gibbonCourseClassID"=>$gibbonCourseClassID); 
				$sql="SELECT * FROM gibboncourseclass WHERE gibbonCourseClassID=:gibbonCourseClassID" ;
				$result=$connection2->prepare($sql);
				$result->execute($data);
			}
			catch(PDOException $e) { 
				//Fail2
				$URL.="&deleteReturn=fail2" ;
				header("Location: {$URL}");
				break ;
			}
			
			if ($result->rowCount()!=1) {
				//Fail 2
				$URL.="&deleteReturn=fail2" ;
				header("Location: {$URL}");
			}
			else {
				//Try to delete entries in gibbonttdayrowclass
				try {
					$dataSelect=array("gibbonCourseClassID"=>$gibbonCourseClassID); 
					$sqlSelect="SELECT * FROM gibbonttdayrowclass WHERE gibbonCourseClassID=:gibbonCourseClassID" ;
					$resultSelect=$connection2->prepare($sqlSelect);
					$resultSelect->execute($dataSelect);
				}
				catch(PDOException $e) { }
				if ($resultSelect->rowCount()>0) {
					while ($rowSelect=$resultSelect->fetch()) {
						try {
							$dataDelete=array("gibbonTTDayRowClassID"=>$rowSelect["gibbonTTDayRowClassID"]); 
							$sqlDelete="DELETE FROM gibbonttdayrowclassexception WHERE gibbonTTDayRowClassID=:gibbonTTDayRowClassID" ;
							$resultDelete=$connection2->prepare($sqlDelete);
							$resultDelete->execute($dataDelete);
						}
						catch(PDOException $e) { }
					}
				}
				
				try {
					$dataDelete=array("gibbonCourseClassID"=>$gibbonCourseClassID); 
					$sqlDelete="DELETE FROM gibbonttdayrowclass WHERE gibbonCourseClassID=:gibbonCourseClassID" ;
					$resultDelete=$connection2->prepare($sqlDelete);
					$resultDelete->execute($dataDelete);
				}
				catch(PDOException $e) { }
				
				//Delete students and other participants
				try {
					$dataDelete=array("gibbonCourseClassID"=>$gibbonCourseClassID); 
					$sqlDelete="DELETE FROM gibboncourseclassperson WHERE gibbonCourseClassID=:gibbonCourseClassID" ;
					$resultDelete=$connection2->prepare($sqlDelete);
					$resultDelete->execute($dataDelete);
				}
				catch(PDOException $e) { }
				
				
				//Write to database
				try {
					$data=array("gibbonCourseClassID"=>$gibbonCourseClassID); 
					$sql="DELETE FROM gibboncourseclass WHERE gibbonCourseClassID=:gibbonCourseClassID" ;
					$result=$connection2->prepare($sql);
					$result->execute($data);
				}
				catch(PDOException $e) { 
					//Fail 2
					$URL.="&deleteReturn=fail2" ;
					header("Location: {$URL}");
					break ;
				}
				
				//Success 0
				$URLDelete=$URLDelete . "&deleteReturn=success0" ;
				header("Location: {$URLDelete}");
			}
		}
	}
}
?>