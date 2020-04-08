<?php
include "../../config.php" ;
try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}
@session_start() ;
if($_GET){
if($_GET['event']=='delete')
{
	$id=$_REQUEST['vehicle_id'];
try {
		$dataFile=array("id"=>$id); 
		$sqlFile="DELETE from  vehicles where vehicle_id=:id" ;
		$resultFile=$connection2->prepare($sqlFile);
		$resultFile->execute($dataFile);
		header("Location: ".$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Transport/vehicles_details.php");
		}
		catch(PDOException $e) { }
}
}
if($_POST) {
	if($_REQUEST['action']=='edit')
	{
		$id=$_REQUEST['id'];

		$type=$_REQUEST['type'];
		$dtls=$_REQUEST['dtls'];

		try{
		$sql="UPDATE vehicles SET type='".$type."',details='".$dtls."' WHERE vehicle_id=".$id;
		$result=$connection2->prepare($sql);
		$result->execute();
		}
		catch(PDOException $e) {}
	}
		if($_REQUEST['action']=='add')
	{

		$type=$_REQUEST['type'];
		$dtls=$_REQUEST['dtls'];

		try{
		$sql="INSERT INTO `vehicles` (`vehicle_id`,`type`, `details`) VALUES (NULL,'".$type."', '".$dtls."')";
		$result=$connection2->prepare($sql);
		$result->execute();
		}
		catch(PDOException $e) {}
	}
}
