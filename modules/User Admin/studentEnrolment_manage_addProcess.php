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
$gibbonPersonID=$_POST["gibbonPersonID"] ;
$search=$_GET["search"] ;
$enrollmentdatetemparr=explode("/", $_REQUEST['enrollment_date']);
$enrollmentdate=$enrollmentdatetemparr[2].'-'.$enrollmentdatetemparr[1].'-'.$enrollmentdatetemparr[0];
$accountnumber=$_REQUEST['account_number'];
$admissionnumber=$_REQUEST['admission_number'];
							
if ($gibbonSchoolYearID=="") {
	print "Fatal error loading this page!" ;
}
else {
	$URL=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_POST["address"]) . "/studentEnrolment_manage_add.php&gibbonSchoolYearID=$gibbonSchoolYearID&search=$search" ;
	

	
	if (isActionAccessible($guid, $connection2, "/modules/User Admin/studentEnrolment_manage_add.php")==FALSE) {
		//Fail 0
		$URL.="&addReturn=fail0" ;
		header("Location: {$URL}");
	}
	else {
		//Proceed!
		//Check if person specified
		if ($gibbonPersonID=="") {
			//Fail1
			$URL.="&addReturn=fail1" ;
			header("Location: {$URL}");
		}
		else {
			//Temporary Section For CPS Kolkata
			try{
				$sql="SELECT `new` FROM `gibbonperson` WHERE `gibbonPersonID`=$gibbonperson";
				$result=$connection2->prepare($sql);
				$result->execute();
				$new=$result->fetch();
			}
			catch(PDOException $e){echo $e;}
			//Temporary Section for CPS Kolkata
			try {
				$data=array("gibbonPersonID"=>$gibbonPersonID); 
				$sql="SELECT gibbonPersonID FROM gibbonperson WHERE gibbonPersonID=:gibbonPersonID AND gibbonperson.status='Full'" ;
				$result=$connection2->prepare($sql);
				$result->execute($data);
			}
			catch(PDOException $e) { 
				//Fail2
				$URL.="&addReturn=fail2" ;
				header("Location: {$URL}");
				break ;
			}

			if ($result->rowCount()!=1) {
				//Fail 2
				$URL.="&addReturn=fail3" ;
				header("Location: {$URL}");
			}
			else {
				//Check for existing enrolment
				try {
					$data=array("gibbonPersonID"=>$gibbonPersonID, "gibbonSchoolYearID"=>$gibbonSchoolYearID); 
					$sql="SELECT * FROM gibbonstudentenrolment WHERE gibbonPersonID=:gibbonPersonID AND gibbonSchoolYearID=:gibbonSchoolYearID" ;
					$result=$connection2->prepare($sql);
					$result->execute($data);
				}
				catch(PDOException $e) { 
					//Fail2
					$URL.="&addReturn=fail5" ;
					header("Location: {$URL}");
					break ;
				}
				
				if ($result->rowCount()>0) {
					//Fail 6
					$URL.="&addReturn=fail6" ;
					header("Location: {$URL}");
				}
				else {
					$gibbonYearGroupID=$_POST["gibbonYearGroupID"];
					$gibbonRollGroupID=$_POST["gibbonRollGroupID"];
					$rollOrder=$_POST["rollOrder"] ;
					if ($rollOrder=="") {
						$rollOrder=NULL ;
					}
					//Check unique inputs for uniquness
					try {
						$data=array("rollOrder"=>$rollOrder, "gibbonRollGroupID"=>$gibbonRollGroupID); 
						$sql="SELECT * FROM gibbonstudentenrolment WHERE rollOrder=:rollOrder AND gibbonRollGroupID=:gibbonRollGroupID AND rollOrder!=''" ;
						$result=$connection2->prepare($sql);
						$result->execute($data);
					}
					catch(PDOException $e) { 
						//Fail 2
						$URL.="&addReturn=fail7" ;
						header("Location: {$URL}");
					}
		
					if ($result->rowCount()>0) {
						//Fail 4
						$URL.="&addReturn=fail4" ;
						header("Location: {$URL}");
					}
					else {
						//Write to database
						
						try {
							$data=array("gibbonPersonID"=>$gibbonPersonID, "gibbonSchoolYearID"=>$gibbonSchoolYearID, "gibbonYearGroupID"=>$gibbonYearGroupID, "gibbonRollGroupID"=>$gibbonRollGroupID, "rollOrder"=>$rollOrder); 
							$sql="INSERT INTO gibbonstudentenrolment SET gibbonPersonID=:gibbonPersonID, gibbonSchoolYearID=:gibbonSchoolYearID, gibbonYearGroupID=:gibbonYearGroupID, gibbonRollGroupID=:gibbonRollGroupID, rollOrder=:rollOrder" ;
							$result=$connection2->prepare($sql);
							$result->execute($data);
							$temp=$connection2->lastInsertId();
							
							//Temporary Section for CPS KOLKATA
							if($new['new']=='Y')
								PopulateStudentPayableFee($gibbonPersonID,$gibbonSchoolYearID,$gibbonYearGroupID,$gibbonRollGroupID,$temp,$connection2,'new');
							else
								PopulateStudentPayableFee($gibbonPersonID,$gibbonSchoolYearID,$gibbonYearGroupID,$gibbonRollGroupID,$temp,$connection2,'old');
							//Temporary Section for CPS KOLKATA
							
							//Disabled section For CPS Kolkata
							//PopulateStudentPayableFee($gibbonPersonID,$gibbonSchoolYearID,$gibbonYearGroupID,$gibbonRollGroupID,$temp,$connection2,'new');
							//Disabled section For CPS Kolkata
							
							updateGibbonPeronAccountEnrolldate($gibbonPersonID,$enrollmentdate,$accountnumber,$admissionnumber,$connection2);
							
						}
						catch(PDOException $e) { 
							//Fail2
							$URL.="&addReturn=fail8" ;
							header("Location: {$URL}");
							break ;
						}
					
						//Success 0
						$URL.="&addReturn=success0" ;
						header("Location: {$URL}");
					}
				}
			}
		}
	}
}
?>
<?php
ob_flush();
?>