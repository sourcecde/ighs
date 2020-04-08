<?php
@session_start() ;
//Including Global Functions & Dtabase Configuration.
include "../../functions.php" ;
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
if(isset($_REQUEST)){
	extract($_REQUEST);
	if($monthno!=0){
	try {
	if($monthno>3 && $monthno!=4)
		$sql1="SELECT * FROM `fee_payable` WHERE `gibbonStudentEnrolmentID`=$studentEnrolmentID AND ((`month_no`<$monthno AND `month_no`>3 AND `gibbonSchoolYearID`=$financialyear) || `gibbonSchoolYearID`<$financialyear) AND `payment_staus` LIKE 'unpaid' and net_amount> 0";
	else if($monthno==4)
		$sql1="SELECT * FROM `fee_payable` WHERE `gibbonStudentEnrolmentID`=$studentEnrolmentID AND ((`month_no`=0 AND `gibbonSchoolYearID`=$financialyear) || `gibbonSchoolYearID`<$financialyear) AND `payment_staus` LIKE 'unpaid' and net_amount>0";
	else
		$sql1="SELECT * FROM `fee_payable` WHERE `gibbonStudentEnrolmentID`=$studentEnrolmentID AND (((`month_no`<$monthno || `month_no`>3)  AND `gibbonSchoolYearID`=$financialyear) || `gibbonSchoolYearID`<$financialyear) AND `payment_staus` LIKE 'unpaid' and net_amount>0";
	$result1=$connection2->prepare($sql1);
	$result1->execute();
	}
	catch(PDOException $e) { 
	echo $e;
	}
	if($result1->rowCount()>=1){
		echo "You have not paid previous fees";
	}
	}
}