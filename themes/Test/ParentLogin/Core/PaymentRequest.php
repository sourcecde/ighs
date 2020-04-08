<?php
session_start();
if(isset($_SESSION['user'])){
	if (file_exists("./dbCon.php")) {
		include "./dbCon.php" ;
	}
	if (file_exists("./Crypto.php")) {
		include "./Crypto.php" ;
	}
	if($_POST){
		$yearId=$_POST['yearId'];
		$monthsString=$_POST['months'];
		$personId=$_SESSION['user']['gibbonPersonID'];
		$finePaid=0;
		$date=date("Y-m-d");
			try{
				$sql="SELECT SUM(`net_amount`) as total FROM `fee_payable` 
						WHERE `month_no` in ($monthsString) AND `gibbonPersonID`=$personId AND `gibbonSchoolYearID`=$yearId";
				$result=$connection1->prepare($sql);
				$result->execute();
				$totalAmount=$result->fetch();
				$amount=0;
				$amount+=$totalAmount['total'];
				
				$monthsArray=explode(",", $monthsString);
				$monthNamesString=join(",",array_map("monthNumToMonthName", $monthsArray));
				$sql1="SELECT SUM(`price`) AS amount FROM `transport_month_entry` 
						WHERE `month_name` in ($monthNamesString) AND `gibbonPersonID`=$personId AND `gibbonSchoolYearID`=$yearId";
				$result1=$connection1->prepare($sql1);
				$result1->execute();
				$feeTransport=$result1->fetch();
				if($feeTransport['amount']){
					$amount+=$feeTransport['amount'];
				}
				$sql2="SELECT SUM(`amount`) as total FROM `lakshya_fine_rule` WHERE `month_no` IN ($monthsString) AND `due_date` <'$date'";
				$result2=$connection1->prepare($sql2);
				$result2->execute();
				$fine=$result2->fetch();
				if($fine['total']){
					$amount+=$fine['total'];
					$finePaid=$fine['total'];
				}
			}
			catch(PDOException $e) {
			  echo $e->getMessage();
			}
			class PaymentRequest{
				public function __construct($tid,$merchant_id,$order_id,$amount,$url){
					$this->tid=$tid;
					$this->merchant_id=$merchant_id;
					$this->order_id=$order_id;
					$this->amount=$amount;
					$this->currency="INR";
					$this->redirect_url=$url;
					$this->cancel_url=$url;
					$this->language="EN";
				}
			}
			$tId=time();
			$oId=substr(Date('Ymdhis'),2,12);
			$paymentData=new PaymentRequest($tId,$Payment["MerchantId"],$oId,$amount,$ReturnURL);
			
			$sql3="INSERT INTO `lakshya_online_payment_reference`( `order_id`,  `personId`,  `amount`, `yearId`, `months`, `fine`) 
					VALUES ('{$paymentData->order_id}','{$personId}','{$paymentData->amount}',{$yearId},'{$monthsString}',{$finePaid})";
			$result3=$connection1->prepare($sql3);
			$result3->execute();
			
			
			$merchant_data="";
			foreach ($paymentData as $key => $value){
				$merchant_data.=$key.'='.$value.'&';
			}
			$working_key=$Payment["WorkingKey"];
			$access_code=$Payment["AccessCode"];
			$encrypted_data=encrypt($merchant_data,$working_key);
			echo "<form method='POST' name='redirect' action='".$Payment["PaymentUrl"]."'>";
			echo "<input type=hidden name=encRequest value=$encrypted_data>";
			echo "<input type=hidden name=access_code value=$access_code>";
			echo "</form>";
			echo "<center><h2>Please wait! We are redirecting you to secure payment gateway.</h2>";
			echo "<h3>Please don't refresh the page.</h3></center>";
			echo "<script>document.redirect.submit()</script>";
	}
}
?>