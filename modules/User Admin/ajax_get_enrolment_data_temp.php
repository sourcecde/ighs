<?php
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
$gibbonPersonID=$_POST["gibbonPersonID"];
	try {
	$sql="SELECT `account_number`,`gibbonRollGroupID`,`gibbonYearGroupID` FROM `gibbonperson` WHERE `gibbonPersonID`=$gibbonPersonID" ;
	$result=$connection2->prepare($sql);
	$result->execute();
	$dboutput=$result->fetch();
	}
	catch(PDOException $e) {
	echo $e;
	}
	
	echo json_encode($dboutput);
?>