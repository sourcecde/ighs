<?php include('Crypto.php')?>
<?php include "./dbCon.php"; ?>
<?php
	$ref = $_SERVER['HTTP_REFERER'];
	$refData = parse_url($ref);
	if($ref ==null || $refData['host'] != $PaymentGatewayDomain) {
		die("Invalid Referrer!!");
	}
	error_reporting(0);
	session_start();
	$workingKey=$Payment["WorkingKey"];		//Working Key should be provided here.
	$encResponse=$_POST["encResp"];			//This is the response sent by the CCAvenue Server
	$rcvdString=decrypt($encResponse,$workingKey);		//Crypto Decryption used as per the specified working key.
	$order_id="";
	$tracking_id="";
	$bank_ref_no="";
	$order_status="";
	$decryptValues=explode('&', $rcvdString);
	$dataSize=sizeof($decryptValues);
	$paidAmount=0;
	for($i = 0; $i < $dataSize; $i++) 
	{
		echo $decryptValues[$i]."<br>";
		$information=explode('=',$decryptValues[$i]);
		if($i==0)	$order_id=$information[1];
		else if($i==1)	$tracking_id=$information[1];
		else if($i==2)	$bank_ref_no="".$information[1];
		else if($i==3)	$order_status=$information[1];
		else if($i==10)	$paidAmount=$information[1];
	}
	$_SESSION["PaymentResponse"]=array("order_id"=>$order_id, "tracking_id"=>$tracking_id, "bank_ref_no"=>$bank_ref_no, "order_status"=>$order_status);
	print_r($_SESSION);
	extract($_SESSION["PaymentResponse"]);
	$personId=$_SESSION['user']['gibbonPersonID'];
	

	try{
		$date=date("Y-m-d");
		$dateTime=date("Y-m-d H:i:s");
		$sql4="SELECT * FROM `lakshya_online_payment_reference` WHERE `order_id`='$order_id'";
		$result4=$connection1->prepare($sql4);
		$result4->execute();
		$paymentDb=$result4->fetch();
		
		if($paymentDb['status']==null && intval($paidAmount)==intval($paymentDb['amount'])){
			if($order_status=='Success'){
				
				$voucherNumber=generateVoucherNumber($connection1,$date);
				
				
				$monthsArray=explode(",", $paymentDb['months']);
				$monthsName=join(",",array_map("monthNumToMonthName", $monthsArray));
				$yearId=$paymentDb['yearId'];
				$finePaid=$paymentDb['fine'];
				$paidMonths=$paymentDb['months'];
				$totalAmountC=intval($paidAmount) - intval($finePaid);
				
				$sql="INSERT INTO `payment_master`(`gibbonPersonID`, `gibbonStudentEnrolmentID`, `fine_amount`, `total_amount`, `net_total_amount`, `voucher_number`, `payment_mode`, `bankID`,  `payment_date`, `gibbonSchoolYearID`, `cheque_bank`,`lock`,`cheque_no`, `cheque_date`) VALUES 
						($personId,(SELECT `gibbonStudentEnrolmentID` FROM `gibbonstudentenrolment` e WHERE e.`gibbonPersonID`=$personId AND e.`gibbonSchoolYearID`=$yearId),
							'$finePaid',{$totalAmountC},$paidAmount,$voucherNumber,'online',$PaymentBankId,'$date',$yearId,0,0,'$order_id','$date'
							)";
				$result=$connection1->prepare($sql);
				$result->execute();
				$paymentMasterId=$connection1->lastInsertId();
				
				$sql1="UPDATE `lakshya_online_payment_reference` SET `tracking_id`='{$tracking_id}',`bank_ref_number`='{$bank_ref_no}',`status`='{$order_status}',`Time`='{$dateTime}',`payment_master_id`='{$paymentMasterId}',`paidAmount`='{$paidAmount}' WHERE `order_id`='{$order_id}' ";
				$result1=$connection1->prepare($sql1);
				$result1->execute();
				
				$sql2="UPDATE `fee_payable` SET `payment_staus`='paid',`payment_master_id`=$paymentMasterId,`payment_date`='$date',`voucher_number`='$voucherNumber' 
						WHERE `gibbonPersonID`=$personId AND `gibbonSchoolYearID`=$yearId AND `month_no` IN ( $paidMonths )";
				$result2=$connection1->prepare($sql2);
				$result2->execute();

				$sql3="UPDATE `transport_month_entry` SET `payment_master_id`=$paymentMasterId 
						WHERE `gibbonPersonID`=$personId AND `gibbonSchoolYearID`=$yearId AND `month_name` IN($monthsName)";
				$result3=$connection1->prepare($sql3);
				$result3->execute();
				
			}
			else{
				$sql1="UPDATE `lakshya_online_payment_reference` SET `tracking_id`='{$tracking_id}',`bank_ref_number`='{$bank_ref_no}',`status`='{$order_status}',`Time`='{$dateTime}',`payment_master_id`='0',`paidAmount`='{$paidAmount}' WHERE `order_id`='{$order_id}'";
				$result1=$connection1->prepare($sql1);
				$result1->execute();
			}
		}
		else{
			$sql5="UPDATE `lakshya_online_payment_reference` SET `tracking_id`='{$tracking_id}',`bank_ref_number`='{$bank_ref_no}',`status`='{$order_status}',`Time`='{$dateTime}',`payment_master_id`='0',`paidAmount`='{$paidAmount}' WHERE `order_id`='{$order_id}'";
			$result5=$connection1->prepare($sql5);
			$result5->execute();
			$_SESSION['security']=true;
		}
		//unset($_SESSION["PaymentResponse"]);
		unset($_SESSION["payment"]);
		
	}
	catch(PDOException $e) {
		echo $e->getMessage();
	}
	$responseUrl="../response.php";
	header('Location: '.$responseUrl);
?>
