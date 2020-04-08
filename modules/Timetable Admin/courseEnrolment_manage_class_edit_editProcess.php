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
$gibbonPersonID=$_POST["gibbonPersonID"] ;

if ($gibbonCourseClassID=="" OR $gibbonCourseID=="" OR $gibbonSchoolYearID=="" OR $gibbonPersonID=="") {
	print "Fatal error loading this page!" ;
}
else {
	$URL=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_POST["address"]) . "/courseEnrolment_manage_class_edit_edit.php&gibbonCourseID=$gibbonCourseID&gibbonPersonID=$gibbonPersonID&gibbonSchoolYearID=$gibbonSchoolYearID&gibbonCourseClassID=$gibbonCourseClassID" ;
	
	if (isActionAccessible($guid, $connection2, "/modules/Timetable Admin/courseEnrolment_manage_class_edit_edit.php")==FALSE) {
		//Fail 0
		$URL.="&updateReturn=fail0" ;
		header("Location: {$URL}");
	}
	else {
		//Proceed!
		//Check if person specified
		if ($gibbonPersonID=="") {
			//Fail1
			$URL.="&updateReturn=fail1" ;
			header("Location: {$URL}");
		}
		else {
			try {
				$data=array("gibbonCourseID"=>$gibbonCourseID, "gibbonCourseClassID"=>$gibbonCourseClassID, "gibbonPersonID"=>$gibbonPersonID); 
				$sql="SELECT role, gibbonperson.preferredName, gibbonperson.surname, gibbonperson.gibbonPersonID, gibboncourseclass.gibbonCourseClassID, gibboncourseclass.name, gibboncourseclass.nameShort, gibboncourse.gibbonCourseID, gibboncourse.name AS courseName, gibboncourse.nameShort as courseNameShort, gibboncourse.description AS courseDescription, gibboncourse.gibbonSchoolYearID, gibbonschoolyear.name as yearName FROM gibbonperson, gibboncourseclass, gibboncourseclassperson,gibboncourse, gibbonschoolyear WHERE gibbonperson.gibbonPersonID=gibboncourseclassperson.gibbonPersonID AND gibboncourseclassperson.gibbonCourseClassID=gibboncourseclass.gibbonCourseClassID AND gibboncourse.gibbonCourseID=gibboncourseclass.gibbonCourseID AND gibboncourse.gibbonSchoolYearID=gibbonschoolyear.gibbonSchoolYearID AND gibboncourse.gibbonCourseID=:gibbonCourseID AND gibboncourseclass.gibbonCourseClassID=:gibbonCourseClassID AND gibbonperson.gibbonPersonID=:gibbonPersonID AND (gibbonperson.status='Full' OR gibbonperson.status='Expected')" ;
				$result=$connection2->prepare($sql);
				$result->execute($data);
			}
			catch(PDOException $e) { 
				//Fail2
				$URL.="&updateReturn=fail2" ;
				header("Location: {$URL}");
				break ;
			}
			
			if ($result->rowCount()!=1) {
				//Fail 2
				$URL.="&updateReturn=fail2" ;
				header("Location: {$URL}");
			}
			else {
				//Validate Inputs
				$role=$_POST["role"] ;
				$reportable=$_POST["reportable"] ;

				if ($role=="") {
					//Fail 3
					$URL.="&updateReturn=fail3" ;
					header("Location: {$URL}");
				}
				else {
					//Write to database
					try {
						$data=array("role"=>$role, "reportable"=>$reportable, "gibbonCourseClassID"=>$gibbonCourseClassID, "gibbonPersonID"=>$gibbonPersonID); 
						$sql="UPDATE gibboncourseclassperson SET role=:role, reportable=:reportable WHERE gibbonCourseClassID=:gibbonCourseClassID AND gibbonPersonID=:gibbonPersonID" ;
						$result=$connection2->prepare($sql);
						$result->execute($data);
					}
					catch(PDOException $e) { 
						//Fail 2
						$URL.="&updateReturn=fail2" ;
						header("Location: {$URL}");
						break ;
					}
					
					//Success 0
					$URL.="&updateReturn=success0" ;
					header("Location: {$URL}");
				}
			}
		}
	}
}
?>