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

include "functions.php" ;
include "config.php" ;

@session_start() ;

//Set timezone from session variable
date_default_timezone_set($_SESSION[$guid]["timezone"]);

//New PDO DB connection
try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}

$URL="./index.php" ;
$role=$_GET["gibbonRoleID"] ;
$_SESSION[$guid]["pageLoads"]=NULL ;


//Check for parameter
if ($role=="") {
	$URL.="?switchReturn=fail0" ;
	header("Location: {$URL}");
}
//Check for access to role
else {
	try {
		$data=array("username"=>$_SESSION[$guid]["username"], "gibbonRoleIDAll"=>"%$role%"); 
		$sql="SELECT * FROM gibbonperson WHERE (username=:username) AND (gibbonRoleIDAll LIKE :gibbonRoleIDAll)";
		$result=$connection2->prepare($sql);
		$result->execute($data);
	}
	catch(PDOException $e) { 
		print "<div class='error'>" . $e->getMessage() . "</div>" ; 
	}
	
	if ($result->rowCount()!=1) {
		$URL.="?switchReturn=fail1" ;
		header("Location: {$URL}");
	}
	else {
		//Make the switch
		$_SESSION[$guid]["gibbonRoleIDCurrent"]=$role;
		$_SESSION[$guid]["mainMenu"]=mainMenu($connection2, $guid) ;
		$URL.="?switchReturn=success0" ;
		header("Location: {$URL}");
	}
}
?>