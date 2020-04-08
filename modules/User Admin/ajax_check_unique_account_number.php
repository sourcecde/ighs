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
$dboutbut=array();
$accountno=(int)$_REQUEST['accountno'];
//$personid=$_REQUEST['personid'];	
	try {
	$sql="SELECT count(*) AS tot FROM gibbonperson WHERE account_number=".$accountno."" ;
	$result=$connection2->prepare($sql);
	$result->execute();
	$dboutbut=$result->fetch();
	}
	catch(PDOException $e) {
	echo $e;
	}
	
	echo $dboutbut['tot'];
?>