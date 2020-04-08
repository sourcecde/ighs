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
function generateVoucherNumber($connection2,$payment_date)
{
	$sql="SELECT count(*) as tableid FROM payment_master where payment_date='".$payment_date."' AND `voucher_number`!=0";
	$result=$connection2->prepare($sql);
	$result->execute();
	$dboutbut=$result->fetch();
	if($dboutbut['tableid']==0)
	{
		$tableid='001';
	}
	else 
	{
		$tableid=$dboutbut['tableid']+1;
		$tableidlen=strlen($tableid);
		switch ($tableidlen) {
			case 1:
				$tableid='00'.$tableid;
			break;
			
			case 2:
				$tableid='0'.$tableid;
			break;
			
			case 3:
				$tableid=$tableid;
			break;
		}
	}
	
	$date=explode("-", $payment_date);
	$voucharnumber=$date[2].$date[1].$tableid;
	return $voucharnumber;
}
if(isset($_REQUEST)){
	extract($_REQUEST);
	if($action=='accept'){
		try{
		$sql1="UPDATE `cheque_master` SET `cheque_status_id` = '1' WHERE `cheque_master_id` = $id";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		}
		catch(PDOException $e) { 
		echo $e;
		}
		try{
			$sql1="SELECT `payment_master_id` FROM `cheque_master` WHERE `cheque_master_id` = $id";
			$result1=$connection2->prepare($sql1);
			$result1->execute();
			$pID=$result1->fetch();
		}
		catch(PDOException $e) { 
		echo $e;
		}
		try{
			$sql1="SELECT * FROM `payment_master` WHERE `payment_master_id` IN (".$pID['payment_master_id'].")";
			$result1=$connection2->prepare($sql1);
			$result1->execute();
			$payment_arr=$result1->fetchAll();
		}
		catch(PDOException $e) { 
		echo $e;
		}
		foreach($payment_arr as $p){
			if($p['voucher_number']==0){
				$voucher_no=generateVoucherNumber($connection2,$p['payment_date']);
				try{
					$sql1="UPDATE `payment_master` SET `voucher_number` = $voucher_no WHERE `payment_master_id` =".$p['payment_master_id'];
					$result1=$connection2->prepare($sql1);
					$result1->execute();
				}
				catch(PDOException $e) { 
					echo $e;
				}
			}
		}
		echo "Cheque Accepted";
		
	}
	if($action=='reject'){
		try{
			$sql1="UPDATE `cheque_master` SET `cheque_status_id` = '0',`reason`='$reason' WHERE `cheque_master_id` = $id";
			$result1=$connection2->prepare($sql1);
			$result1->execute();
		}
		catch(PDOException $e) { 
			echo $e;
		}
		echo "Cheque Rejected";
		try{
			$sql="Select * from `cheque_master` where `cheque_master_id`=$id";
			$result1=$connection2->prepare($sql);
			$result1->execute();
			$abc=$result1->fetchAll();
		}
		catch(PDOException $e) { 
			echo $e;
		}
		$cStr="";
		foreach($abc as $a){
			$cStr=$a['payment_master_id'];
		}
		try{
			$sql="Select * FROM `fee_payable` WHERE payment_master_id IN ($cStr) AND `rule_id`>0";
			$resultFile=$connection2->prepare($sql);
			$resultFile->execute();
			$mData=$resultFile->fetchAll();
		}
		catch(PDOException $e) { echo $e;}
		try{
			$sql="Select `net_amount` FROM `fee_payable` WHERE payment_master_id IN ($cStr) AND `fee_type_master_id`='43'";
			$resultFile=$connection2->prepare($sql);
			$resultFile->execute();
			$bCharge=$resultFile->fetch();
		}
		catch(PDOException $e) { echo $e;}
		if(!empty($bCharge)){
			$bankCharge=$bankCharge+$bCharge['net_amount'];
		}
		$a=0;
		foreach($mData as $m){
			if($a==0 && $bankCharge!=0){
				$a=$a+1;
				$sql="INSERT INTO `fee_payable`(`fee_payable_id`, `gibbonSchoolYearID`, `gibbonPersonID`, `gibbonStudentEnrolmentID`, `rule_id`, `fee_type_master_id`, `month_no`, `month_name`, `amount`, `concession`, `net_amount`, `payment_staus`, `created_date`, `payment_master_id`, `payment_date`, `voucher_number`, `fee_type_short_name`, `pseq`)
					VALUES (NULL, '{$m['gibbonSchoolYearID']}', '{$m['gibbonPersonID']}', '{$m['gibbonStudentEnrolmentID']}', 0, '43', '{$m['month_no']}', '{$m['month_name']}', '$bankCharge', '0', '$bankCharge', 'unpaid', '".date("Y-m-d H:i:s")."',NULL, NULL, NULL, 'BC', '0')";
				$result=$connection2->prepare($sql);
				$result->execute();
			}
			if((int)$m['fee_type_master_id']!=43){
				$sql1="INSERT INTO `fee_payable`(`fee_payable_id`, `gibbonSchoolYearID`, `gibbonPersonID`, `gibbonStudentEnrolmentID`, `rule_id`, `fee_type_master_id`, `month_no`, `month_name`, `amount`, `concession`, `net_amount`, `payment_staus`, `created_date`, `payment_master_id`, `payment_date`, `voucher_number`, `fee_type_short_name`, `pseq`) 
					VALUES (NULL, '{$m['gibbonSchoolYearID']}', '{$m['gibbonPersonID']}', '{$m['gibbonStudentEnrolmentID']}', '{$m['rule_id']}', '{$m['fee_type_master_id']}', '{$m['month_no']}', '{$m['month_name']}', '{$m['amount']}', '{$m['concession']}', '{$m['net_amount']}', 'unpaid', '".date("Y-m-d H:i:s")."',NULL, NULL, NULL, '{$m['fee_type_short_name']}', '{$m['pseq']}')";
				$result=$connection2->prepare($sql1);
				$result->execute();
			}
		}
		/*try {
			$sqlFile="SELECT * FROM `transport_month_entry` WHERE payment_master_id IN ($cStr)";
			$resultFile=$connection2->prepare($sqlFile);
			$resultFile->execute();
			$tData=$$resultFile->fetchAll();
		}
		catch(PDOException $e) { echo $e;}
		foreach($tData as $t){
			
		}*/
	}
	if($action=='pending'){
		try{
		$sql1="UPDATE `cheque_master` SET `cheque_status_id` = '0' WHERE `cheque_master_id` = $id";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		}
		catch(PDOException $e) { 
		echo $e;
		}
		echo "Cheque marked as pending";
	}
}
 ?>