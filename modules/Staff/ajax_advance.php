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
extract($_POST);
if($action=='addAdvance'){
extract($data);
$date=dateFormatter($date);
$staffID+=0;
		try{
		$sql="SELECT COUNT(`advanceID`) as N FROM `lakshyasalaryadvance` WHERE `staffID`=$staffID AND `isPaid`='N'";
		$result=$connection2->prepare($sql);
		$result->execute();
		$ID=$result->fetch();
		if($ID['N']==0){
			$sql="INSERT INTO `lakshyasalaryadvance`(`advanceID`, `staffID`, `amount`, `type`, `date`, `schoolYearID`, `nEMI`, `isPaid`, `emiAdvanceID`,`salaryMonth`) 
				VALUES (NULL,$staffID,$amount,'Dr','$date',$schoolYearID,$nEMI,'N',NULL,0);";
			$result=$connection2->prepare($sql);
			$result->execute();
			echo "Added Successfully!!";
		}
		else
			echo "Please pay unpaid advance first!!";
		}
		catch(PDOException $e){
			echo $e;
		}
}
else if($action=='returnAdvance'){
extract($data);
$date=dateFormatter($date);
$staffID+=0;
		try{
		$sql="SELECT `advanceID` FROM `lakshyasalaryadvance` WHERE `staffID`=$staffID AND `isPaid`='N'";
		$result=$connection2->prepare($sql);
		$result->execute();
		$ID=$result->fetch();
		$id=$ID['advanceID'];
		$sql="INSERT INTO `lakshyasalaryadvance`(`advanceID`, `staffID`, `amount`, `type`, `date`, `schoolYearID`, `nEMI`, `isPaid`, `emiAdvanceID`,`salaryMonth`) 
			VALUES (NULL,$staffID,$amount,'Cr','$date',$schoolYearID,0,NULL,$id,0);";
		$result=$connection2->prepare($sql);
		$result->execute();
		$sql="SELECT SUM(`amount`) AS paid,(SELECT `amount` FROM `lakshyasalaryadvance` WHERE `advanceID`=$id) AS total FROM `lakshyasalaryadvance` WHERE `emiAdvanceID`=$id";
		$result=$connection2->prepare($sql);
		$result->execute();
		$emi=$result->fetch();
		if($emi['total']==$emi['paid']){
			$sql="UPDATE `lakshyasalaryadvance` SET `isPaid`='Y' WHERE `advanceID`=$id";
			$result=$connection2->prepare($sql);
			$result->execute();
		}
		echo "Returned Successfully!!";
		}
		catch(PDOException $e){
			echo $e;
		}
}
else if($action=='fetchAdvanceData'){
		try{
		$sql="SELECT `amount`,`date`,`nEMI` FROM `lakshyasalaryadvance` WHERE `advanceID`=$advanceID";
		$result=$connection2->prepare($sql);
		$result->execute();
		$data=$result->fetch();
		$data['date']=dateFormatterR($data['date']);
		echo json_encode($data);
		}
		catch(PDOException $e){
			echo $e;
		}
}
else if($action=='advanceUpdate'){
	extract($data);
	$date=dateFormatter($date);
		try{
		$sql="UPDATE `lakshyasalaryadvance` SET `amount`=$amount,`date`='$date',`nEMI`=$nEMI WHERE `advanceID`=$advanceID";
		$result=$connection2->prepare($sql);
		$result->execute();
		echo "Updated sucessfully!!";
		}
		catch(PDOException $e){
			echo $e;
		}
}
else if($action=='deleteAdvance'){
		try{
		$sql="DELETE FROM `lakshyasalaryadvance` WHERE `advanceID`=$advanceID";
		$result=$connection2->prepare($sql);
		$result->execute();
		echo "Deleted sucessfully!!";
		}
		catch(PDOException $e){
			echo $e;
		}
}
}
function dateFormatter($date){
	$tmp=explode("/",$date);
	return $tmp[2]."-".$tmp[1]."-".$tmp[0];
}
function dateFormatterR($date){
	$tmp=explode("-",$date);
	return $tmp[2]."/".$tmp[1]."/".$tmp[0];
}
?>