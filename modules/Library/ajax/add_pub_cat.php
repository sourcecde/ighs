<?php
/* 
	This File Url:
	$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajax/add_pub_cat.php" ;
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
	$message="";
	if($action=='publisher'){
		try{
		$sql1="INSERT INTO `lakshya_library_publisher`(`publisherID`, `publisher`) VALUES (NULL, '$name')";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		}
		catch(PDOException $e) { 
		echo $e;
		}
		try{
		$sql1="SELECT * FROM `lakshya_library_publisher`";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		$publishers=$result1->fetchAll();
		}
		catch(PDOException $e) { 
		echo $e;
		}
		foreach($publishers as $p){
			$message.="<option value='{$p['publisherID']}'>{$p['publisher']}</option>";
		}
	}
	else if($action=='category'){
		try{
		$sql1="INSERT INTO `lakshya_library_category`(`categoryID`, `category`) VALUES (NULL,'$name')";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		}
		catch(PDOException $e) { 
		echo $e;
		}
		try{
		$sql2="SELECT * FROM `lakshya_library_category`";
		$result2=$connection2->prepare($sql2);
		$result2->execute();
		$categories=$result2->fetchAll();
		}
		catch(PDOException $e) { 
		echo $e;
		}
		foreach($categories as $c){
			$message.="<option value='{$c['categoryID']}'>{$c['category']}</option>";
		}
	}
	echo $message;
}
 ?>