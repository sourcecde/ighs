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
		$sql="UPDATE `transport_fee_yearwise` SET `amount`=$price WHERE `feeID`=$id" ;
		$result=$connection2->prepare($sql);
		$result->execute($data);
		}
		catch(PDOException $e) { }
	}
	else if($action=='delete'){
		try {
		$sql="DELETE FROM `transport_fee_yearwise` WHERE `feeID`=$id" ;
		$result=$connection2->prepare($sql);
		$result->execute($data);
		}
		catch(PDOException $e) { }
	}
	else if($action=='add'){
		$sql1="SELECT `transport_spot_price_id`, `spot_name` FROM `transport_spot_price`";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		$location=$result1->fetchAll();
		
		$sql="INSERT INTO `transport_fee_yearwise`(`feeID`, `transport_spot_price_id`, `amount`, `gibbonSchoolYearID`) VALUES ";
		$sqlp="";
		$i=0;
		foreach($location as $l){
			$id=$l['transport_spot_price_id'];
			$tmp='price_'.$id;
			if(isset($$tmp)){
				if($$tmp>0){
					if($i++>0)
						$sqlp.=" ,";
					$sqlp.="(NULL,$id,{$$tmp},$gibbonSchoolYearID)";
				}
			}
		}
		if(strlen($sqlp)>0){
			$result=$connection2->prepare($sql.$sqlp);
			$result->execute();
		}
		$url=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/dropLocationPrice.php";
		header("Location:{$url}");
	}
}
?>