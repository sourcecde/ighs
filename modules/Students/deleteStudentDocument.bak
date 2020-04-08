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
if($_POST){
	extract($_POST);
	if($action=='delete'){
		
		$sql="SELECT  `name` FROM `studentdocuments` WHERE `documentsID`=$id";
		$result=$connection2->prepare($sql);
		$result->execute();
		$name=$result->fetch();
		unlink("../../".$name['name']);
		$sql="DELETE FROM `studentdocuments` WHERE `documentsID`=$id";
		$result=$connection2->prepare($sql);
		$result->execute();
	}
}
 ?>