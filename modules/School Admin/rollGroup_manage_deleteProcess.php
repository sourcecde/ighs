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
$gibbonSchoolYearID=$_POST["gibbonSchoolYearID"] ;
$gibbonRollGroupID=$_GET["gibbonRollGroupID"] ;
$URL=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_POST["address"]) . "/rollGroup_manage_delete.php&gibbonRollGroupID=$gibbonRollGroupID&gibbonSchoolYearID=$gibbonSchoolYearID" ;
$URLDelete=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_POST["address"]) . "/rollGroup_manage.php&gibbonSchoolYearID=$gibbonSchoolYearID" ;

if (isActionAccessible($guid, $connection2, "/modules/School Admin/rollGroup_manage_delete.php")==FALSE) {
	//Fail 0
	$URL.="&deleteReturn=fail0" ;
	header("Location: {$URL}");
}
else {
	//Proceed!
	//Check if school year specified
	if ($gibbonRollGroupID=="") {
		//Fail1
		$URL.="&deleteReturn=fail1" ;
		header("Location: {$URL}");
	}
	else {
		try {
			$data=array("gibbonRollGroupID"=>$gibbonRollGroupID, "gibbonSchoolYearID"=>$gibbonSchoolYearID); 
			$sql="SELECT * FROM gibbonrollgroup WHERE gibbonRollGroupID=:gibbonRollGroupID AND gibbonSchoolYearID=:gibbonSchoolYearID" ;
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
			//Write to database
			try {
				$data=array("gibbonRollGroupID"=>$gibbonRollGroupID); 
				$sql="DELETE FROM gibbonrollgroup WHERE gibbonRollGroupID=:gibbonRollGroupID" ;
				$result=$connection2->prepare($sql);
				$result->execute($data);
			}
			catch(PDOException $e) { 
				//Fail 2
				$URL.="&deleteReturn=fail2" ;
				header("Location: {$URL}");
				break ;
			}
			
			try {
				$data=array("gibbonRollGroupID"=>$gibbonRollGroupID); 
				$sql="DELETE FROM gibbonstudentenrolment WHERE gibbonRollGroupID=:gibbonRollGroupID" ;	
				$result=$connection2->prepare($sql);
				$result->execute($data);
			}
			catch(PDOException $e) { 
			}
			
			//Success 0
			$URLDelete=$URLDelete . "&deleteReturn=success0" ;
			header("Location: {$URLDelete}");
		}
	}
}
?>