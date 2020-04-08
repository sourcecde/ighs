<?php
@session_start() ;
if($_POST){
	extract($_POST);
/* For Lakshya Database */
include "../../config.php" ;
try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}
/* For Lakshya Database */

$sql="SELECT `firstDay`,`lastDay`,`sequenceNumber` FROM `gibbonschoolyear` WHERE `gibbonSchoolYearID`=$yearID";
$result=$connection2->prepare($sql);
$result->execute();
$yData=$result->fetch();
$from_date=$yData['firstDay'];
$to_date=$yData['lastDay'];
$yearSequenceNumber=$yData['sequenceNumber'];
$feeData=array();
/// Default ID for cash entry
$cashID=1;
//Pending and Rejected cheques
try{
	$sql="Select * from `cheque_master` where `cheque_status_id`!=1";
	$result1=$connection2->prepare($sql);
	$result1->execute();
	$abc=$result1->fetchAll();
}
catch(PDOException $e) { 
	echo $e;
}
$cStr="-1";
foreach($abc as $a){
	$cStr.=",".$a['payment_master_id'];
}
//Pending and Rejected cheques transport
try{
	$sql="Select * from `transport_month_entry` where `payment_master_id` IN ($cStr)";
	$result1=$connection2->prepare($sql);
	$result1->execute();
	$def=$result1->fetchAll();
}
catch(PDOException $e) { 
	echo $e;
}
$tStr="-1";
foreach($def as $d){
		$tStr.=",".$d['transport_month_entryid'];
}
//For Account Wise
try{
$sql1="SELECT `fee_payable`.`payment_date`,SUM(`net_amount`) AS 'total_amount',`fee_type_master`.`fee_type_name`,`gibbonschoolyear`.`sequenceNumber` as `status`,`payment_master`.`payment_mode`,`IDinAccount`,`cheque_no`,`cheque_date`,CASE WHEN `payment_mode`!='cash' THEN `payment_master`.`gibbonPersonID` ELSE 0 END AS `filter`
		FROM `fee_payable` 
		LEFT JOIN `fee_type_master` ON `fee_type_master`.`fee_type_master_id`=`fee_payable`.`fee_type_master_id` 
		LEFT JOIN `payment_master` ON `payment_master`.`payment_master_id`=`fee_payable`.`payment_master_id` 
		LEFT JOIN `gibbonschoolyear` ON `gibbonschoolyear`.`gibbonSchoolYearID`=`fee_payable`.`gibbonSchoolYearID`  
		LEFT JOIN `payment_bankaccount` ON `payment_bankaccount`.`bankID`=`payment_master`.`bankID` 
		WHERE `fee_payable`.`payment_date`>='$from_date' and `fee_payable`.`payment_date`<='$to_date' and `fee_payable`.`payment_master_id` NOT IN ($cStr)
		GROUP BY `fee_payable`.`payment_date`,`filter`, `IDinAccount`, `fee_payable`.`gibbonSchoolYearID`,`fee_payable`.`fee_type_short_name` ,`payment_master`.`payment_mode` HAVING `total_amount`>0";
$result1=$connection2->prepare($sql1);
$result1->execute();
$accDB=$result1->fetchAll();
//echo "<br>$sql1<br>";
//print_r($accDB);
}
catch(PDOException $e) { echo $e; }

foreach($accDB as $value){
	//$status=$value['status']=='Upcoming'?'Advance':'Income';
	$status=$value['status'];
	$new=true;
	$entryID=$value['payment_mode']=='cash'?$cashID:$value['IDinAccount'];
	$cheque_no=$value['payment_mode']=='cash'?NULL:$value['cheque_no'];
	if(array_key_exists($value['payment_mode'],$feeData))
		if(array_key_exists($value['payment_date'],$feeData[$value['payment_mode']]))
			if(array_key_exists($value['filter'],$feeData[$value['payment_mode']][$value['payment_date']]))
			if(array_key_exists($cheque_no,$feeData[$value['payment_mode']][$value['payment_date']][$value['filter']]))
				if(array_key_exists($value['cheque_date'],$feeData[$value['payment_mode']][$value['payment_date']][$value['filter']][$cheque_no]))	
					if(array_key_exists($entryID,$feeData[$value['payment_mode']][$value['payment_date']][$value['filter']][$cheque_no][$value['cheque_date']]))
						if(array_key_exists($status,$feeData[$value['payment_mode']][$value['payment_date']][$value['filter']][$cheque_no][$value['cheque_date']][$entryID]))
							if(array_key_exists($value['fee_type_name'],$feeData[$value['payment_mode']][$value['payment_date']][$value['filter']][$cheque_no][$value['cheque_date']][$entryID][$status]))
								$new=false;
	if(!$new)
		$feeData[$value['payment_mode']][$value['payment_date']][$value['filter']][$cheque_no][$value['cheque_date']][$entryID][$status][$value['fee_type_name']]+=$value['total_amount'];
	else
		$feeData[$value['payment_mode']][$value['payment_date']][$value['filter']][$cheque_no][$value['cheque_date']][$entryID][$status][$value['fee_type_name']]=$value['total_amount'];
}

//For Fine
try{
$sql2="SELECT `payment_date`, SUM(`fine_amount`) AS 'fine',`payment_master`.`payment_mode`,`IDinAccount`,`cheque_no`,`cheque_date`,CASE WHEN `payment_mode`!='cash' THEN `payment_master`.`gibbonPersonID` ELSE 0 END AS `filter`
		FROM `payment_master` 
		LEFT JOIN `payment_bankaccount` ON `payment_bankaccount`.`bankID`=`payment_master`.`bankID`
		WHERE `payment_date`>='$from_date' and `payment_date`<='$to_date' and `payment_master`.`payment_master_id` NOT IN ($cStr)
		GROUP BY `payment_date`,`IDinAccount`,`payment_master`.`payment_mode`,`filter`";
$result2=$connection2->prepare($sql2);
$result2->execute();
$fineDB=$result2->fetchAll();
//echo "<br>$sql2<br>";
//print_r($fineDB);
}
catch(PDOException $e) { echo $e; }

foreach($fineDB as $value){
	$entryID=$value['payment_mode']=='cash'?$cashID:$value['IDinAccount'];
	$cheque_no=$value['payment_mode']=='cash'?NULL:$value['cheque_no'];
	$feeData[$value['payment_mode']][$value['payment_date']][$value['filter']][$cheque_no][$value['cheque_date']][$entryID]['Current']['Fine']=$value['fine'];
}

//For Transport
try{
$sql3="SELECT `payment_master`.`payment_date`,SUM(`price`) AS `transport`,`gibbonschoolyear`.`sequenceNumber` as `status`,`payment_master`.`payment_mode`,`IDinAccount`,`cheque_no`,`cheque_date`,CASE WHEN `payment_mode`!='cash' THEN `payment_master`.`gibbonPersonID` ELSE 0 END AS `filter` 
		FROM `transport_month_entry` 
		LEFT JOIN `payment_master` ON `payment_master`.`payment_master_id`=`transport_month_entry`.`payment_master_id` 
		LEFT JOIN `gibbonschoolyear` ON `gibbonschoolyear`.`gibbonSchoolYearID`=`transport_month_entry`.`gibbonSchoolYearID` 
		LEFT JOIN `payment_bankaccount` ON `payment_bankaccount`.`bankID`=`payment_master`.`bankID`
		WHERE `payment_master`.`payment_date`>='$from_date' and `payment_master`.`payment_date`<='$to_date' AND `transport_month_entry`.`payment_master_id` >0 AND `transport_month_entry`.`transport_month_entryid` NOT IN ($tStr)
		GROUP BY `payment_master`.`payment_date`,`IDinAccount`,`transport_month_entry`.`gibbonSchoolYearID`,`payment_master`.`payment_mode`, `filter`";
$result3=$connection2->prepare($sql3);
$result3->execute();
$transDB=$result3->fetchAll();
//echo "<br>$sql3<br>";
//print_r($transDB);
}
catch(PDOException $e) { echo $e; }

foreach($transDB as $value){
	$entryID=$value['payment_mode']=='cash'?$cashID:$value['IDinAccount'];
	//$status=$value['status']=='Upcoming'?'Advance':'Income';
	$status=$value['status'];
	$cheque_no=$value['payment_mode']=='cash'?NULL:$value['cheque_no'];
	$new=true;
	if(array_key_exists($value['payment_mode'],$feeData))
		if(array_key_exists($value['payment_date'],$feeData[$value['payment_mode']]))
			if(array_key_exists($value['filter'],$feeData[$value['payment_mode']][$value['payment_date']]))
			if(array_key_exists($cheque_no,$feeData[$value['payment_mode']][$value['payment_date']][$value['filter']]))
				if(array_key_exists($value['cheque_date'],$feeData[$value['payment_mode']][$value['payment_date']][$value['filter']][$cheque_no]))	
					if(array_key_exists($entryID,$feeData[$value['payment_mode']][$value['payment_date']][$value['filter']][$cheque_no][$value['cheque_date']]))
						if(array_key_exists($status,$feeData[$value['payment_mode']][$value['payment_date']][$value['filter']][$cheque_no][$value['cheque_date']][$entryID]))
							if(array_key_exists('Transport',$feeData[$value['payment_mode']][$value['payment_date']][$value['filter']][$cheque_no][$value['cheque_date']][$entryID][$status]))
								$new=false;
	if(!$new)
		$feeData[$value['payment_mode']][$value['payment_date']][$value['filter']][$cheque_no][$value['cheque_date']][$entryID][$status]['Transport']+=$value['transport'];
	else
		$feeData[$value['payment_mode']][$value['payment_date']][$value['filter']][$cheque_no][$value['cheque_date']][$entryID][$status]['Transport']=$value['transport'];
}
/*echo "<pre>";
print_r($feeData);
echo "</pre><br>";
/* Curl Part */
	$post_data=array('organizationID'=>1,'feeData'=>$feeData, 'yearID'=>$yearID, 'yearSequenceNumber'=>$yearSequenceNumber);
	$post_data_string=json_encode($post_data);
	//$exportURL='http://'.$_SERVER['HTTP_HOST'].'/ng_app/api/import/lakshya';
	$exportURL='http://'.$_SERVER['HTTP_HOST'].'/accounts/api/import/lakshyaFee';
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
