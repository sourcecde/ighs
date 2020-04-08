<?php 
session_start();
if(isset($_SESSION['user']) && isset($_SESSION["PaymentResponse"]) && isset($_SESSION["payment"])){
	if (file_exists("./dbCon.php")) {
		include "./dbCon.php" ;
	}
	extract($_SESSION["PaymentResponse"]);
	extract($_SESSION["payment"]);
	$personId=$_SESSION['user']['gibbonPersonID'];
	$monthsArray=explode(",", $months);
	$monthsName=join(",",array_map("monthNumToMonthName", $monthsArray));

	try{
		$date=date("Y-m-d");
		$dateTime=date("Y-m-d H:i:s");
		$totalAmount=$finePaid+$amount;
		if($order_status=='Success'){
			$voucherNumber=generateVoucherNumber($connection1,$date);
			$sql="INSERT INTO `payment_master`(`gibbonPersonID`, `gibbonStudentEnrolmentID`, `fine_amount`, `total_amount`, `net_total_amount`, `voucher_number`, `payment_mode`, `bankID`,  `payment_date`, `gibbonSchoolYearID`, `cheque_bank`,`lock`) VALUES 
					($personId,(SELECT `gibbonStudentEnrolmentID` FROM `gibbonstudentenrolment` e WHERE e.`gibbonPersonID`=$personId AND e.`gibbonSchoolYearID`=$yearId),
						$finePaid,$amount,$totalAmount,$voucherNumber,'online',$PaymentBankId,'$date',$yearId,0,0
						)";
			$result=$connection1->prepare($sql);
			$result->execute();
			$paymentMasterId=$connection1->lastInsertId();
			
			$sql1="INSERT INTO `lakshya_online_payment_reference`( `order_id`, `tracking_id`, `bank_ref_number`, `status`, `personId`, `Time`, `payment_master_id`) VALUES 
					('$order_id','$tracking_id','$bank_ref_no','$order_status',$personId,'$dateTime',$paymentMasterId)";
			$result1=$connection1->prepare($sql1);
			$result1->execute();
			
			$sql2="UPDATE `fee_payable` SET `payment_staus`='paid',`payment_master_id`=$paymentMasterId,`payment_date`='$date',`voucher_number`='$voucherNumber' 
					WHERE `gibbonPersonID`=$personId AND `gibbonSchoolYearID`=$yearId AND `month_no` IN ($months)";
			$result2=$connection1->prepare($sql2);
			$result2->execute();

			$sql3="UPDATE `transport_month_entry` SET `payment_master_id`=$paymentMasterId 
					WHERE `gibbonPersonID`=$personId AND `gibbonSchoolYearID`=$yearId AND `month_name` IN($monthsName)";
			$result3=$connection1->prepare($sql3);
			$result3->execute();
			
		}
		else{
			$sql1="INSERT INTO `lakshya_online_payment_reference`( `order_id`, `tracking_id`, `bank_ref_number`, `status`, `personId`, `Time`, `payment_master_id`) VALUES 
					('$order_id','$tracking_id','$bank_ref_no','$order_status',$personId,'$dateTime',0)";
			$result1=$connection1->prepare($sql1);
			$result1->execute();
		}
		unset($_SESSION["PaymentResponse"]);
		unset($_SESSION["payment"]);
		
	}
	catch(PDOException $e) {
		echo $e->getMessage();
	}
}
?>