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

if($_POST)
{
	//spot price add
	if($_REQUEST['event']=='add')
	{
	$spot_name=$_REQUEST['spot_name'];
	$price=$_REQUEST['price'];
	$distance=$_REQUEST['distance'];
	
		try {
		$dataFile=array("spot_name"=>$spot_name, "price"=>$price, "distance"=>$distance); 
		$sqlFile="Insert into  transport_spot_price SET spot_name=:spot_name,distance=:distance,price=:price" ;
		$resultFile=$connection2->prepare($sqlFile);
		$resultFile->execute($dataFile);
		header("Location: ".$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Transport/index.php");
		}
		catch(PDOException $e) { }
	}
	
if($_REQUEST['event']=='edit')
	{
	$spot_name=$_REQUEST['spot_name'];
	$price=$_REQUEST['price'];
	$distance=$_REQUEST['distance'];
	$id=$_REQUEST['id'];
		try {
		$dataFile=array("spot_name"=>$spot_name, "price"=>$price, "distance"=>$distance,"transport_spot_price_id"=>$id); 
		$sqlFile="UPDATE  transport_spot_price SET spot_name=:spot_name,distance=:distance,price=:price where transport_spot_price_id=:transport_spot_price_id" ;
		$resultFile=$connection2->prepare($sqlFile);
		$resultFile->execute($dataFile);
		header("Location: ".$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Transport/index.php");
		}
		catch(PDOException $e) { }
	}
	
}

if($_GET['event']=='delete')
{
	$id=$_REQUEST['transport_spot_price_id'];
try {
		$dataFile=array("transport_spot_price_id"=>$id); 
		$sqlFile="DELETE from  transport_spot_price where transport_spot_price_id=:transport_spot_price_id" ;
		$resultFile=$connection2->prepare($sqlFile);
		$resultFile->execute($dataFile);
		header("Location: ".$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Transport/index.php");
		}
		catch(PDOException $e) { }
}