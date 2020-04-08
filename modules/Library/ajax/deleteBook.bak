<?php
/* 
	This File Url:
	$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajax/deleteBook.php" ;
*/
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
		try{
		$sql1="UPDATE `lakshya_library_bookmaster` SET `archive`=1 WHERE `bookID`=$id";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		}
		catch(PDOException $e) { 
		echo $e;
		}
}
 ?>