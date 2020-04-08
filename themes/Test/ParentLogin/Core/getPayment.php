<?php
session_start();
if(isset($_SESSION['user'])){
	if (file_exists("./dbCon.php")) {
		include "./dbCon.php" ;
	}
	if($_GET){
		$month_arr1=array(0=>'Yearly',1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December');
		$month_arr2=array('yearly'=>'Yearly','jan'=>'January','feb'=>'February','mar'=>'March','apr'=>'April','may'=>'May','jun'=>'June','jul'=>'July','aug'=>'August','sep'=>'September','oct'=>'October','nov'=>'November','dec'=>'December');
		$month_sequence=array('Yearly','April','May','June','July','August','September','October','November','December','January','February','March');
		if($_GET['action']=='getPayableMonths'){
			$personId=$_GET['personId'];
			$yearId=$_GET['yearId'];
			global $MonthDb;
			function GetMonthDb($month_no){
				global $MonthDb;
				global $month_arr1;
				$MonthDb[$month_arr1[$month_no]]=$month_no;
			}
			function GetMonthDbByName($month_name){
				global $MonthDb;
				global $month_arr1;
				$month_no=monthNameToMonthNum($month_name);
				$MonthDb[$month_arr1[$month_no]]=$month_no;
			}
			try{
			$sql="SELECT distinct `month_no` FROM `fee_payable` 
					WHERE `payment_staus`='unpaid' AND `gibbonPersonID`=$personId AND `gibbonSchoolYearID`=$yearId 
					ORDER BY `month_no`";
			$result=$connection1->prepare($sql);
			$result->execute();
			$months=$result->fetchAll(PDO::FETCH_FUNC,"GetMonthDb");
			$sql1="SELECT DISTINCT `month_name` FROM `transport_month_entry` 
					WHERE  `gibbonPersonID`=$personId AND `gibbonSchoolYearID`=$yearId  AND `payment_master_id`=0";
			$result1=$connection1->prepare($sql1);
			$result1->execute();
			$months=$result1->fetchAll(PDO::FETCH_FUNC,"GetMonthDbByName");
			$Months=array();
			if(!empty($MonthDb)){
				
				foreach($month_sequence as $month){
					if(array_key_exists($month, $MonthDb)){
						$Months[][$month]=$MonthDb[$month];
					}
				}
				echo json_encode($Months);
			}
			
			}
			catch(PDOException $e) {
			  echo $e->getMessage();
			}
		}
		
		else if($_GET['action']=='getPayableFees'){
			$date=date("Y-m-d");
			$personId=$_GET['personId'];
			$yearId=$_GET['yearId'];
			$monthsArr=$_GET['months'];
			$monthsString=join(",",$monthsArr);
			try{
			$sql="SELECT `fee_type_master`.`fee_type_name`,`net_amount` FROM `fee_payable` 
					LEFT JOIN `fee_type_master` ON `fee_payable`.`fee_type_master_id` =`fee_type_master`.`fee_type_master_id`
					WHERE `month_no` in ($monthsString) AND `gibbonPersonID`=$personId AND `gibbonSchoolYearID`=$yearId";
			$result=$connection1->prepare($sql);
			$result->execute();
			$fees=$result->fetchAll();

			$monthNamesString=join(",",array_map("monthNumToMonthName", $monthsArr));
			$sql1="SELECT SUM(`price`) AS amount FROM `transport_month_entry` 
					WHERE `month_name` in ($monthNamesString) AND `gibbonPersonID`=$personId AND `gibbonSchoolYearID`=$yearId";
			$result1=$connection1->prepare($sql1);
			$result1->execute();
			$feeTransport=$result1->fetch();

			$sql2="SELECT SUM(`amount`) as total FROM `lakshya_fine_rule` WHERE `month_no` IN ($monthsString) AND `due_date` <'$date'";
			$result2=$connection1->prepare($sql2);
			$result2->execute();
			$fine=$result2->fetch();

			$FeeDb=array();
			foreach($fees as $fee){
				if(array_key_exists($fee['fee_type_name'],$FeeDb)){
					$FeeDb[$fee['fee_type_name']]+=$fee['net_amount'];
				}
				else{
					$FeeDb[$fee['fee_type_name']]=$fee['net_amount'];
				}
			}
			if($feeTransport['amount']){
				$FeeDb['Transport']=$feeTransport['amount'];
			}
			if($fine['total']){
				$FeeDb['Fine']=$fine['total'];
			}
			echo json_encode($FeeDb);
			}
			catch(PDOException $e) {
			  echo $e->getMessage();
			}
		}
		else if($_GET['action']=='getYearWiseData'){
			$personId=$_GET['personId'];
			$yearId=$_GET['yearId'];
			try{
			$sql="SELECT `gibbonyeargroup`.`name` class,`gibbonrollgroup`.`name` section,`gibbonstudentenrolment`.`rollOrder`
					FROM `gibbonstudentenrolment` 
					JOIN `gibbonyeargroup` ON `gibbonyeargroup`.`gibbonYearGroupID`=`gibbonstudentenrolment`.`gibbonYearGroupID`
					JOIN `gibbonrollgroup` ON `gibbonrollgroup`.`gibbonRollGroupID`=`gibbonstudentenrolment`.`gibbonRollGroupID`
					WHERE `gibbonstudentenrolment`.`gibbonPersonID`=$personId AND  `gibbonstudentenrolment`.`gibbonSchoolYearID`=$yearId";
			$result=$connection1->prepare($sql);
			$result->execute();
			$profile=$result->fetch();
			}
			catch(PDOException $e) {
			  echo $e->getMessage();
			}
			echo json_encode($profile);
		}
	}
}
?>