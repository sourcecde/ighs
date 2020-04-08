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
if($_POST){
	if($_REQUEST['action']=='fetch_data')	{
		$id=$_REQUEST['id'];
		try{
			$sql="SELECT * FROM `lakshyasalaryrule` where rule_id=".$id;
		$result=$connection2->prepare($sql);
		$result->execute();
		$rule=$result->fetch();
		}
		catch(PDOException $e){
			echo $e;
		}
		echo $rule['caption']."_".$rule['impact']."_".$rule['active'];
	}
	if($_REQUEST['action']=='add')	{
		$caption=$_REQUEST['caption'];
		$impact=$_REQUEST['impact'];
		$active=$_REQUEST['active'];
		//echo $caption." ".$impact." ".$active;
		try{
		$sql="INSERT INTO `lakshyasalaryrule`(`rule_id`, `caption`, `impact`, `active`) VALUES (NULL,'".$caption."','".$impact."','".$active."')";
		$result=$connection2->prepare($sql);
		$result->execute();
		}
		catch(PDOException $e){
			echo $e;
		}
	}
	if($_REQUEST['action']=='update')	{
		$caption=$_REQUEST['caption'];
		$impact=$_REQUEST['impact'];
		$active=$_REQUEST['active'];
		$id=$_REQUEST['id'];
		try{
		$sql="UPDATE `lakshyasalaryrule` SET `caption`='".$caption."',`impact`='".$impact."',`active`='".$active."' WHERE `rule_id`=".$id;
		$result=$connection2->prepare($sql);
		$result->execute();
		}
		catch(PDOException $e){
			echo $e;
		}
	}
	if($_REQUEST['action']=='delete')	{
		$id=$_REQUEST['id'];
		try{
		$sql="DELETE FROM `lakshyasalaryrule` WHERE `rule_id`=".$id;
		$result=$connection2->prepare($sql);
		$result->execute();
		}
		catch(PDOException $e){
			echo $e;
		}
	}
	
}
?>