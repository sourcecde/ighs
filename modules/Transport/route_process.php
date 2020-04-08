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
echo "HULULU";
if($_GET){
if($_GET['event']=='delete')
{
	$id=$_REQUEST['route_id'];
try {
		$dataFile=array("id"=>$id); 
		$sqlFile="DELETE from  transport_route where route_id=:id" ;
		$resultFile=$connection2->prepare($sqlFile);
		$resultFile->execute($dataFile);
		header("Location: ".$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Transport/route_details.php");
		}
		catch(PDOException $e) { }
}
}
if($_POST) {
	if($_REQUEST['action']=='edit')
	{
		$id=$_REQUEST['id'];
		$vehicle_id=$_REQUEST['vehicle_id'];
		$route=$_REQUEST['route'];

		try{
		$sql="UPDATE transport_route SET route='".$route."',vehicle_id='".$vehicle_id."' WHERE route_id=".$id;
		$result=$connection2->prepare($sql);
		$result->execute();
		}
		catch(PDOException $e) {}
	}
	if($_REQUEST['action']=='add')
	{

		$route=$_REQUEST['route'];
		$vehicle_id=$_REQUEST['vehicle_id'];
		try{
		$sql="INSERT INTO `transport_route` (`route_id`,`route`,`vehicle_id`) VALUES (NULL,'".$route."','".$vehicle_id."')";
		$result=$connection2->prepare($sql);
		$result->execute();
		}
		catch(PDOException $e) {}
	}
}
