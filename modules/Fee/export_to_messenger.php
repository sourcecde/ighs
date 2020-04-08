<?php
include "../../config.php" ;
@session_start();
try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}
@session_start() ;
if($_POST){
	$personID=$_REQUEST['value'];
	$sql="TRUNCATE TABLE `export_to_messenger`";
	$result=$connection2->prepare($sql);
	$result->execute();
	$sql="INSERT INTO `export_to_messenger` (`gibbonPersonID`) VALUES $personID";
	$result=$connection2->prepare($sql);
	$result->execute();
	echo "Exported Succesfully";
}