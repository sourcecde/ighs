<?php
session_start();
if(isset($_SESSION['user'])){
	if (file_exists("./dbCon.php")) {
		include "./dbCon.php" ;
	}
	extract($_SESSION);
	if($_GET){

		$bankRefNo=$_GET['RefNo'];
		global $FeeDb;
		function GetFeeDb($head,$amount){
			global $FeeDb;
			if($amount>0){
				$FeeDb[$head]=$amount;
			}
		}
		try {
			$sql="SELECT `payment_master_id`, `gibbonStudentEnrolmentID`,`fine_amount`,`net_total_amount`,`voucher_number`,`payment_date`,`gibbonschoolyear`.`name` AS session 
					FROM `payment_master` 
					LEFT JOIN `gibbonschoolyear` ON `payment_master`.`gibbonSchoolYearID`= `gibbonschoolyear`.`gibbonSchoolYearID`
					WHERE `payment_master_id`=(SELECT `payment_master_id` FROM `lakshya_online_payment_reference` WHERE `bank_ref_number`='$bankRefNo')";
			$result=$connection1->prepare($sql);
			$result->execute();
			$paymentMaster=$result->fetch();
			
			$sql1="SELECT `gibbonyeargroup`.`name` class,`gibbonrollgroup`.`name` section,`gibbonstudentenrolment`.`rollOrder`
					FROM `gibbonstudentenrolment` 
					JOIN `gibbonyeargroup` ON `gibbonyeargroup`.`gibbonYearGroupID`=`gibbonstudentenrolment`.`gibbonYearGroupID`
					JOIN `gibbonrollgroup` ON `gibbonrollgroup`.`gibbonRollGroupID`=`gibbonstudentenrolment`.`gibbonRollGroupID`
					WHERE `gibbonstudentenrolment`.`gibbonStudentEnrolmentID`=".$paymentMaster['gibbonStudentEnrolmentID'];
			$result1=$connection1->prepare($sql1);
			$result1->execute();
			$profile=$result1->fetch();
			
			$sql2="SELECT `fee_type_master`.`fee_type_name`,SUM(`amount`) AS amount 
					FROM `fee_payable` 
					LEFT JOIN `fee_type_master` ON `fee_payable`.`fee_type_master_id`= `fee_type_master`.`fee_type_master_id` 
					WHERE `payment_master_id`= ".$paymentMaster['payment_master_id']."
					GROUP BY `fee_payable`.`fee_type_master_id`";
			$result2=$connection1->prepare($sql2);
			$result2->execute();
			$result2->fetchAll(PDO::FETCH_FUNC,"GetFeeDb");
			

			$sql3="SELECT 'Transport', SUM(`price`) AS amount FROM `transport_month_entry` WHERE `payment_master_id`=".$paymentMaster['payment_master_id'];
			$result3=$connection1->prepare($sql3);
			$result3->execute();
			$result3->fetchAll(PDO::FETCH_FUNC,"GetFeeDb");
			

			if($paymentMaster['fine_amount']>0){
				$FeeDb['Fine']=$paymentMaster['fine_amount'];
			}
			
			$sql4="SELECT DISTINCT month_name 
						FROM `fee_payable` 
						WHERE `payment_master_id`=".$paymentMaster['payment_master_id']." 
					UNION 
					SELECT DISTINCT `month_name` 
						FROM `transport_month_entry` 
						WHERE `payment_master_id`=".$paymentMaster['payment_master_id'];
			$result4=$connection1->prepare($sql4);
			$result4->execute();
			$monthNames=$result4->fetchAll();
			$paidMonths=GetPaidMonths($monthNames,$paymentMaster['session']);
			
			include("../partials/reciept.php");

		} catch (PDOException $e) {
		  //echo $e->getMessage();
		}
	}
}
 ?>