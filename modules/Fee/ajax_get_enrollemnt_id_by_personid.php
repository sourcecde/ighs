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
$schoolyeararr=array(1=>'jan',2=>'feb',3=>'mar',4=>'apr',5=>'may',6=>'jun',7=>'jul',8=>'aug',9=>'sep',10=>'oct',11=>'nov',12=>'dec');
$paidmontharr=array();
$personid=0;
if($_POST)
{
	
		$sql="SELECT gibbonstudentenrolment.gibbonStudentEnrolmentID FROM gibbonstudentenrolment LEFT JOIN gibbonperson
ON gibbonperson.gibbonPersonID=gibbonstudentenrolment.gibbonPersonID
WHERE gibbonperson.account_number=".$_REQUEST['account_number'];
		$result=$connection2->prepare($sql);
		$result->execute();
		$dboutbut=$result->fetch();
		
	if($dboutbut)
	{
		echo $dboutbut['gibbonStudentEnrolmentID'];
	}
	else 
	{
		echo '0';
	}
	
	
}