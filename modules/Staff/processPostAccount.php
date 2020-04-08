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
if(isset($_POST)){
	extract($_POST);
	$startDate=DateConverter($from_date);
	$endDate=DateConverter($to_date);
	$cashID=1;
	$entryType="C";
	try{
	$sql1="SELECT  `preferredName`,`lakshyasalaryadvance`.`date`,`lakshyasalaryadvance`.`amount`,`lakshyasalaryadvance`.`type` 
		FROM `lakshyasalaryadvance` 
		LEFT JOIN `gibbonstaff` ON `lakshyasalaryadvance`.`staffID`=`gibbonstaff`.`gibbonStaffID` 
		LEFT JOIN `gibbonperson` ON `gibbonstaff`.`gibbonPersonID`=`gibbonperson`.`gibbonPersonID` 
		WHERE `salaryMonth`=0 AND `lakshyasalaryadvance`.`date` BETWEEN '$startDate' AND '$endDate'";
	$result1=$connection2->prepare($sql1);
	$result1->execute();
	$advanceDB=$result1->fetchAll();
	}
	catch(PDOException $e) { echo $e; }
	$advanceData=array();
	foreach($advanceDB as $a){
		$advanceData[$entryType][$cashID][]=$a;
	}
	//print_r($advanceData);
	/* Curl Part */
	$post_data=array('organizationID'=>1,'advanceData'=>$advanceData);
	$post_data_string=json_encode($post_data);
	$exportURL='http://'.$_SERVER['HTTP_HOST'].'/accounts/api/import/lakshya';
	//$exportURL='http://192.168.2.2/accounts/api/import/lakshya';
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,  $exportURL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data_string);
	$result = curl_exec($ch);
	curl_close($ch);
	echo $result;
	/* Curl Part */
}
function DateConverter($date)
{
	$datearr=explode("/", $date);
	$systemdate=$datearr[2].'-'.$datearr[1].'-'.$datearr[0];
	return $systemdate;
}
 ?>
 