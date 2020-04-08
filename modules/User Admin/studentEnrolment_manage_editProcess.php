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
include "custom_funcions.php";
//Set timezone from session variable
date_default_timezone_set($_SESSION[$guid]["timezone"]);

$gibbonSchoolYearID=$_GET["gibbonSchoolYearID"] ;
$gibbonStudentEnrolmentID=$_POST["gibbonStudentEnrolmentID"] ;
$gibbonPersonID=$_REQUEST['gibbonPersonID'];
$search=$_GET["search"] ;
$enrollmentdatetemparr=explode("/", $_REQUEST['enrollment_date']);
$enrollmentdate=$enrollmentdatetemparr[2].'-'.$enrollmentdatetemparr[1].'-'.$enrollmentdatetemparr[0];
$accountnumber=(int)$_REQUEST['account_number'];
$admissionnumber=$_REQUEST['admission_number'];

if ($gibbonStudentEnrolmentID=="" OR $gibbonSchoolYearID=="") {
	print "Fatal error loading this page!" ;
}
else {
	$URL=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_POST["address"]) . "/studentEnrolment_manage_edit.php&gibbonStudentEnrolmentID=$gibbonStudentEnrolmentID&gibbonSchoolYearID=$gibbonSchoolYearID&search=$search" ;
	$query="SELECT officialName FROM gibbonperson WHERE account_number=$accountnumber AND `gibbonPersonID`!=$gibbonPersonID";
	$result=$connection2->prepare($query);
	$result->execute();
	$person=$result->fetch();
	if($result->rowCount()>0){
			//Fail 5
			$URL.="&updateReturn=fail5&p=".$person['officialName'] ;
			header("Location: {$URL}");
	}
else{
	updateGibbonPeronAccountEnrolldate($gibbonPersonID,$enrollmentdate,$accountnumber,$admissionnumber,$connection2);
	if (isActionAccessible($guid, $connection2, "/modules/User Admin/studentEnrolment_manage_edit.php")==FALSE) {
		//Fail 0
		$URL.="&updateReturn=fail0" ;
		header("Location: {$URL}");
	}
	else {
		//Proceed!
		//Check if person specified
		if ($gibbonStudentEnrolmentID=="") {
			//Fail1
			$URL.="&updateReturn=fail1" ;
			header("Location: {$URL}");
		}
		else {
			try {
				$data=array("gibbonSchoolYearID"=>$gibbonSchoolYearID, "gibbonStudentEnrolmentID"=>$gibbonStudentEnrolmentID); 
				$sql="SELECT gibbonrollgroup.gibbonRollGroupID, gibbonyeargroup.gibbonYearGroupID,gibbonStudentEnrolmentID, surname, preferredName, gibbonyeargroup.nameShort AS yearGroup, gibbonrollgroup.nameShort AS rollGroup,gibbonperson.gibbonPersonID FROM gibbonperson, gibbonstudentenrolment, gibbonyeargroup, gibbonrollgroup WHERE (gibbonperson.gibbonPersonID=gibbonstudentenrolment.gibbonPersonID) AND (gibbonstudentenrolment.gibbonYearGroupID=gibbonyeargroup.gibbonYearGroupID) AND (gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID) AND gibbonrollgroup.gibbonSchoolYearID=:gibbonSchoolYearID AND gibbonStudentEnrolmentID=:gibbonStudentEnrolmentID ORDER BY surname, preferredName" ; 
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
				$URL.="&updateReturn=fail2" ;
				header("Location: {$URL}");
			}
			else {
				$gibbonYearGroupID=$_POST["gibbonYearGroupID"] ;
				$gibbonRollGroupID=$_POST["gibbonRollGroupID"] ;
				
				$rollOrder=$_POST["rollOrder"] ;
				if ($rollOrder=="") {
					$rollOrder=NULL ;
				}
			
				//Check unique inputs for uniquness
				try {
					$data=array("gibbonStudentEnrolmentID"=>$gibbonStudentEnrolmentID, "rollOrder"=>$rollOrder, "gibbonRollGroupID"=>$gibbonRollGroupID); 
					$sql="SELECT * FROM gibbonstudentenrolment WHERE rollOrder=:rollOrder AND gibbonRollGroupID=:gibbonRollGroupID AND NOT gibbonStudentEnrolmentID=:gibbonStudentEnrolmentID AND NOT rollOrder=''" ;
					$result=$connection2->prepare($sql);
					$result->execute($data);
				}
				catch(PDOException $e) { 
					//Fail 2
					$URL.="&updateReturn=fail2" ;
					header("Location: {$URL}");
				}
				//echo $result->rowCount();
				//exit;
				if ($result->rowCount()>0) {
					//Fail 4
					$URL.="&updateReturn=fail4" ;
					header("Location: {$URL}");
				}
				else {
					
								//Write to database
								try {
									$data=array("gibbonYearGroupID"=>$gibbonYearGroupID, "gibbonRollGroupID"=>$gibbonRollGroupID, "rollOrder"=>$rollOrder, "gibbonStudentEnrolmentID"=>$gibbonStudentEnrolmentID); 
									$sql="UPDATE gibbonstudentenrolment SET gibbonYearGroupID=:gibbonYearGroupID, gibbonRollGroupID=:gibbonRollGroupID, rollOrder=:rollOrder WHERE gibbonStudentEnrolmentID=:gibbonStudentEnrolmentID" ;
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
}
?>