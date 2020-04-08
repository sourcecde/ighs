<?php 
session_start();
if(isset($_SESSION['user'])){
	if (file_exists("./dbCon.php")) {
		include "./dbCon.php" ;
	}
	if($_GET){
			$personId=$_GET['personId'];
			$yearId=$_GET['yearId'];
			global $FeeDb;
			global $FeeDetailsDb;
			global $FineDb;
			$month_arr1=array(0=>'Yearly',1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December');
			$month_arr2=array('yearly'=>'Yearly','jan'=>'January','feb'=>'February','mar'=>'March','apr'=>'April','may'=>'May','jun'=>'June','jul'=>'July','aug'=>'August','sep'=>'September','oct'=>'October','nov'=>'November','dec'=>'December');
			$month_sequence=array('Yearly','April','May','June','July','August','September','October','November','December','January','February','March');
			class Fee{
				public function __construct($month,$amount,$status,$name,$reference){
					$this->Name=$name;
					$this->Month=$month;
					$this->Amount=$amount;
					$this->Status=$status;
					$this->Reference=$reference;
				}
			}
			class FeeDetails{
				public function __construct($month, $amount,$status,$reference,$fees,$dueDate){
					$this->Month=$month;
					$this->Amount=$amount;
					$this->Status=$status;
					$this->Reference=$reference;
					$this->DueDate=$dueDate==''?'-':date_format(date_create($dueDate),"d/m/Y");
					$this->Fees=$fees;
				}
			}
			function GetFeeDb($month_no,$amount,$status,$name,$reference){
				global $FeeDb;
				global $month_arr1;
				$month=$month_arr1[$month_no];
				$p_status=$status=='paid'?'Paid':'Unpaid';
				$FeeDb[$month][]=new Fee($month,$amount,$p_status,$name,$reference);
			}
			function GetTransportDb($month_name,$price,$payment_master_id,$reference){
				global $FeeDb;
				global $month_arr2;
				$month=$month_arr2[$month_name];
				$p_status=$payment_master_id>0?'Paid':'Unpaid';
				$FeeDb[$month][]=new Fee($month,$price,$p_status,'Transport',$reference);
			}
			function GetFineDb($monthNo, $dueDate){
				global $FineDb;
				$month_arr1=array(0=>'Yearly',1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December');
				$FineDb[$month_arr1[$monthNo]]=$dueDate;
			}
			try{
			$sql="SELECT  `month_no`,`net_amount`, `payment_staus`,`fee_type_master`.`fee_type_name`,`bank_ref_number` 
					FROM `fee_payable` 
					LEFT JOIN fee_rule_master ON fee_payable.rule_id=fee_rule_master.fee_rule_master_id 
					LEFT JOIN fee_type_master ON fee_type_master.fee_type_master_id=fee_payable.fee_type_master_id 
					LEFT JOIN `lakshya_online_payment_reference` ON `fee_payable`.`payment_master_id`=`lakshya_online_payment_reference`.`payment_master_id`
					WHERE `gibbonPersonID`=$personId AND `fee_payable`.`gibbonSchoolYearID`=$yearId AND (`net_amount` > 0 OR `concession`>0)";
			$result=$connection1->prepare($sql);
			$result->execute();
			$result->fetchAll(PDO::FETCH_FUNC,"GetFeeDb");
			
			$sql1="SELECT `month_name`,`price`,`transport_month_entry`.`payment_master_id`,`bank_ref_number` FROM `transport_month_entry`
					LEFT JOIN `payment_master` ON `payment_master`.`payment_master_id`= `transport_month_entry`.`payment_master_id` 
					LEFT JOIN `lakshya_online_payment_reference` ON `transport_month_entry`.`payment_master_id`=`lakshya_online_payment_reference`.`payment_master_id`
					WHERE `transport_month_entry`.`gibbonPersonID`=$personId AND `transport_month_entry`.`gibbonSchoolYearID`=$yearId";
			$result1=$connection1->prepare($sql1);
			$result1->execute();
			$result1->fetchAll(PDO::FETCH_FUNC,"GetTransportDb");
			
			$sql2="SELECT `month_no`,`due_date` FROM `lakshya_fine_rule` WHERE `gibbonSchoolYearID`=$yearId";
			$result2=$connection1->prepare($sql2);
			$result2->execute();
			$fineDB=$result2->fetchAll(PDO::FETCH_FUNC,"GetFineDb");
			
			}
			catch(PDOException $e) {
			  echo $e->getMessage();
			}
		if(!empty($FeeDb)){
			foreach($month_sequence as $month){
			if(array_key_exists($month,$FeeDb)){
				$flag=false;
				$status;
				$reference;
				$totalAmount=0;
				$dueDate='';
				if(!empty($FineDb)){
					if(array_key_exists($month,$FineDb)){
						$dueDate=$FineDb[$month];
					}
				}
				foreach($FeeDb[$month] as $fee){
					if(!$flag){
						$status=$fee->Status;
						$reference=$fee->Reference;
						$flag=true;
					}
					$totalAmount+=$fee->Amount;
				}
				$FeeDetailsDb[]=new FeeDetails($month,$totalAmount,$status,$reference,$FeeDb[$month],$dueDate);
			}
		}
		}
		echo json_encode($FeeDetailsDb);
	}
}

?>