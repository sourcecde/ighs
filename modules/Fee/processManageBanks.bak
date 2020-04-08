<?php
@session_start() ;
include "../../config.php" ;
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
	if($action=='addSub'){
		try{
		$sql1="INSERT INTO `fee_bank_master`(`bankMasterID`, `bankName`, `bankAbbr`) VALUES (NULL,'$bankName','$bankAbbr')";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		}
		catch(PDOException $e) { 
		echo $e;
		}
		echo "Added Sucessfully!!";
	}
	else if($action=='editSub'){
		try{
		$sql1="UPDATE `fee_bank_master` SET `bankName`='$bankName',`bankAbbr`='$bankAbbr' WHERE `bankMasterID`=$bankMasterID";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		}
		catch(PDOException $e) { 
		echo $e;
		}
		echo "Updated Sucessfully!!";
	}
	else if($action=='deleteSub'){
		try{
		$sql1="DELETE FROM `fee_bank_master` WHERE `bankMasterID`=$bankMasterID";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		}
		catch(PDOException $e) { 
		echo $e->getMessage();
		}
		echo "Deleted Sucessfully!!";
		echo $databaseName;
	}
}
 ?>