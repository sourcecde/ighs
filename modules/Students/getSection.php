<?php
@session_start() ;
//Including Global Functions & Dtabase Configuration.
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
if(isset($_REQUEST)){
	extract($_REQUEST);
	try {
	$sql1="SELECT DISTINCT `Section` FROM `a_view_create` WHERE `Class`=".$yearGroup;
	$result1=$connection2->prepare($sql1);
	$result1->execute();
	$section=$result1->fetchAll();
	}
	catch(PDOException $e) { 
	echo $e;
	}
	foreach($section as $s){
		echo "<option value='{$s['Section']}'>{$s['Section']}</option>";
	}
}