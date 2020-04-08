<?php 
if($_POST){
@session_start() ;
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
extract($_POST);
if($action=='feeDelete'){
	try{
		$sql="DELETE FROM `fee_payable` WHERE `fee_payable_id`=$id";
		$result=$connection2->prepare($sql);
		$result->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage();
	}
}
else if($action=='transportDelete'){
	try{
		$sql="DELETE FROM `transport_month_entry` WHERE `transport_month_entryid`=$id";
		$result=$connection2->prepare($sql);
		$result->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage();
	}
}	
}
?>