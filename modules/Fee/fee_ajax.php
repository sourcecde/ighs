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
if(isset($_POST))
{
	if(isset($_REQUEST['action'])=='delete_rule')
	{
	try {
	$dataFile=array("fee_rule_master_id"=>$_POST['id']); 
	$sqlFile="DELETE from  fee_rule_master where fee_rule_master_id=:fee_rule_master_id" ;
	$resultFile=$connection2->prepare($sqlFile);
	$resultFile->execute($dataFile);
	}
	catch(PDOException $e) {
	echo $e;
	}
	}
	
	if(isset($_REQUEST['action'])=='give_concession')
	{
	try {
	$dataFile=array("fee_payable_id"=>$_POST['id'],"concession"=>$_POST['concession_amount']); 
	$sqlFile="UPDATE fee_payable SET concession=:concession,net_amount=(amount-concession) where fee_payable_id=:fee_payable_id" ;
	$resultFile=$connection2->prepare($sqlFile);
	$resultFile->execute($dataFile);
	}
	catch(PDOException $e) {
	echo $e;
	}
	}
	
	
}