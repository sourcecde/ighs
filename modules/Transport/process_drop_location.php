<?php
include "../../config.php" ;
@session_start();
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
	if($action=='edit'){
		try {
		$sql="UPDATE  `transport_spot_price` SET `spot_name`='$location',`distance`='$distance'  WHERE `transport_spot_price_id`=$id" ;
		$result=$connection2->prepare($sql);
		$result->execute();
		}
		catch(PDOException $e) { }
	}
	else if($action=='add'){
		try{
			$sql="INSERT INTO `transport_spot_price`(`transport_spot_price_id`, `spot_name`, `distance`, `created_date`) VALUES (NULL,'$location','$distance','".date('Y-m-d h:i:s')."')" ;
			$result=$connection2->prepare($sql);
			$result->execute();
		}
		catch(PDOException $e) { }
	}
	
}
else if($_GET['event']=='delete')
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
 ?>