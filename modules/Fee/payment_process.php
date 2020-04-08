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

//print_r($_REQUEST['fine'][0]);
/*
foreach ($_REQUEST as $value) {
	print_r($value);
}
*/
$lastpaymnentid=0;

if($_POST)
{
$URL=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Fee/payment.php" ;
 $paymontharr=explode(",", $_REQUEST['paymonths']);

try {
	$dataFile=array("gibbonPersonID"=>0, "gibbonStudentEnrolmentID"=>$_POST['gibbonStudentEnrolmentID'],"total_amount"=>$_POST["grand_total"]); 
	$sqlFile="Insert into  payment_master SET gibbonPersonID=:gibbonPersonID,gibbonStudentEnrolmentID=:gibbonStudentEnrolmentID,total_amount=:total_amount" ;
	$resultFile=$connection2->prepare($sqlFile);
	$resultFile->execute($dataFile);
	$lastpaymnentid=$connection2->lastInsertId();
	}
	catch(PDOException $e) { }
	//header("Location: {$URL}");

foreach ($paymontharr as $key=>$value) {
	try {
		// insert into payment detail
		$amount=($_POST['month_total'][$key]-$_POST['fine'][$key]);
	$dataFile=array("payment_master_id"=>$lastpaymnentid, 							"amount"=>$amount,"fine_amount"=>$_POST['fine'][$key],"netamount"=>$_POST['month_total'][$key],"payment_month"=>$value); 
	$sqlFile="Insert into  payment_detail SET payment_master_id=:payment_master_id,amount=:amount,							fine_amount=:fine_amount,			netamount=:netamount,					payment_month=:payment_month";
	$resultFile=$connection2->prepare($sqlFile);
	$resultFile->execute($dataFile);
	
	//update fee payable table
	$dataFile=array("gibbonStudentEnrolmentID"=>$_POST['gibbonStudentEnrolmentID'],	"month_no"=>$value,"payment_staus"=>'paid'); 
	$sqlFile="UPDATE fee_payable SET payment_staus=:payment_staus where gibbonStudentEnrolmentID=:gibbonStudentEnrolmentID AND 	month_no=:month_no";
	$resultFile=$connection2->prepare($sqlFile);
	$resultFile->execute($dataFile);
	}
	catch(PDOException $e) { 
		echo $e;
	}
}
header("Location: {$URL}");
exit;
}
?>