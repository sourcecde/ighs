<?php
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

@session_start() ;
if($_POST){
extract($_POST);
try {
  	$sql="UPDATE `gibbonstudentenrolment` SET `rollOrder`=$roll,`gibbonRollGroupID`=$section WHERE `gibbonStudentEnrolmentID`=$enrollID" ;
	$result=$connection2->prepare($sql);
	$result->execute();
}
catch(PDOException $e) {
  echo $e->getMessage();
}
try {
  	$sql="UPDATE `gibbonperson` SET `admission_number`='$admission', `nationalIDCardNumber` = '$aadhar', `phone1` = '$phone', `fatherName` = '$father', `mothername` = '$mother', `address1`='$address' WHERE `gibbonPersonID`=(SELECT `gibbonPersonID` FROM `gibbonstudentenrolment` WHERE `gibbonStudentEnrolmentID`=$enrollID)" ;
	$result=$connection2->prepare($sql);
	$result->execute();
}
catch(PDOException $e) {
  echo $e->getMessage();
}
try {
  	$sql1="SELECT `preferredName` FROM `gibbonperson` WHERE `account_number`=$account_no AND`gibbonPersonID`=(SELECT `gibbonPersonID` FROM `gibbonstudentenrolment` WHERE `gibbonStudentEnrolmentID`=$enrollID)" ;
	$result1=$connection2->prepare($sql1);
	$result1->execute();
}
catch(PDOException $e) {
  echo $e->getMessage();
}
if($result1->rowCount()==0){
	try {
		$sql2="SELECT `preferredName` FROM `gibbonperson` WHERE `account_number`=$account_no" ;
		$result2=$connection2->prepare($sql2);
		$result2->execute();
		$row=$result2->fetch();
	}
	catch(PDOException $e) {
	  echo $e->getMessage();
	}
	if($result2->rowCount()==0){
		try {
			$sql="UPDATE `gibbonperson` SET `account_number`='$account_no' WHERE `gibbonPersonID`=(SELECT `gibbonPersonID` FROM `gibbonstudentenrolment` WHERE `gibbonStudentEnrolmentID`=$enrollID)" ;
			$result=$connection2->prepare($sql);
			$result->execute();
		}
		catch(PDOException $e) {
		  echo $e->getMessage();
		}
		$message="1_Updated sucessfully!";
	}
	else
		$message="0_Account No already Exist. Its belong to ".$row['preferredName'];
}
else
	$message="1_Updated sucessfully!";

echo $message;
}
 ?>