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
	if($action=='deleteExam'){
		try {
		$sql1="DELETE FROM `lakshya_exam_master` WHERE `examID`=$examID";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		echo "Deleted Successfully!!!";
		}
		catch(PDOException $e) { 
		echo $e;
		}
	}
}