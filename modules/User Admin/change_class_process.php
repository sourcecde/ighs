<?php
@session_start();
include "../../config.php" ;
try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}
if($_POST){
	//print_r($_POST);
	extract($_POST);
	$sql="SELECT * FROM `gibbonstudentenrolment` WHERE `gibbonPersonID`=$gibbonPersonID AND `gibbonSchoolYearID`=$gibbonSchoolYearID";
	$result=$connection2->prepare($sql);
	$result->execute();
	$enrolmentData=$result->fetch();
	//print_r($enrolmentData);
	if($result->rowCount()>0){
	$sql1="SELECT * FROM gibbonyeargroup WHERE gibbonYearGroupID='$next_class'" ;
	$result1=$connection2->prepare($sql1);
	$result1->execute();
	$class=$result1->fetch();
	
	$sql2="SELECT `fee_boarder_class_id` FROM fee_boarder_class WHERE class='{$class['name']}' AND border='N'";
	$result2=$connection2->prepare($sql2);
	$result2->execute();
	$fbc=$result2->fetch();
	$fee_boarder_class_id=$fbc['fee_boarder_class_id'];
	//echo "<br>$fee_boarder_class_id<br>";
	
	$sql3="SELECT `gibbonPersonID` FROM `gibbonstudentenrolment` WHERE `gibbonPersonID`=$gibbonPersonID AND `gibbonSchoolYearID`!=$gibbonSchoolYearID";
	$result3=$connection2->prepare($sql3);
	$result3->execute();
	$tmp=$result3->fetch();
	$student_type=$result3->rowCount()>0?'old':'new';
	//echo "<br>$student_type<br>";
	try{
	$sql4="SELECT fee_rule_master.*,fee_type_master.fee_type_desc as fee_short_type,fee_type_master.yearly,fee_type_master.jan,fee_type_master.feb,fee_type_master.mar,fee_type_master.apr,fee_type_master.may,
			fee_type_master.jun,fee_type_master.jul,fee_type_master.aug,fee_type_master.sep,fee_type_master.oct,
			fee_type_master.nov,fee_type_master.dec
			FROM fee_rule_master
			LEFT JOIN fee_type_master ON fee_rule_master.fee_type_master_id=fee_type_master.fee_type_master_id 
			WHERE fee_boarder_class_id=$fee_boarder_class_id AND fee_rule_master.gibbonSchoolYearID=$gibbonSchoolYearID";
	if($student_type=='old')
		$sql4.=" AND `fee_rule_master`.`onetime`=0";
	echo $sql4;
	$result4=$connection2->prepare($sql4);
	$result4->execute();
	}
	catch(PDOException $e){
		echo $e;
	}
	$feeDB=$result4->fetchAll();
	//print_r($feeDB);
	//echo "<br>$sql4<br>";
	
	//$months=implode(',',$selected_month);
	$sql5="DELETE FROM `fee_payable` WHERE `gibbonStudentEnrolmentID`={$enrolmentData['gibbonStudentEnrolmentID']}";
	$result5=$connection2->prepare($sql5);
	$result5->execute();
	//echo "<br>$sql5<br>";
	
	$amount=0;
	$date=date('Y-m-d');
	$schoolyeararr=array(0=>'yearly',1=>'jan',2=>'feb',3=>'mar',4=>'apr',5=>'may',6=>'jun',7=>'jul',8=>'aug',9=>'sep',10=>'oct',11=>'nov',12=>'dec');
	$sql6="INSERT INTO `fee_payable`(`fee_payable_id`, `gibbonSchoolYearID`, `gibbonPersonID`, `gibbonStudentEnrolmentID`, `rule_id`, `fee_type_master_id`, `month_no`, `month_name`, `amount`, `concession`, `net_amount`, `payment_staus`, `created_date`,`fee_type_short_name`) VALUES ";
	$i=0;
	foreach ($feeDB as $value) {
		foreach ($schoolyeararr as $key=>$monthvalue) {
			//echo $value[$monthvalue];
			if($value[$monthvalue]==1)
			{
				$amount=$value['amount'];
				if($i++!=0)
					$sql6.=", ";
			$sql6.="(NULL,$gibbonSchoolYearID,$gibbonPersonID,{$enrolmentData['gibbonStudentEnrolmentID']},{$value['fee_rule_master_id']},{$value['fee_type_master_id']},$key,'$monthvalue',$amount,'0.00',$amount,'unpaid','$date','{$value['fee_short_type']}' )";
			}
		}
	}
	//echo $sql6;
	$result6=$connection2->prepare($sql6);
	$result6->execute();
	
	$sql5="UPDATE `gibbonstudentenrolment` SET `gibbonYearGroupID`='$next_class',`gibbonRollGroupID`='$next_section' WHERE `gibbonStudentEnrolmentID`={$enrolmentData['gibbonStudentEnrolmentID']}";
	$result5=$connection2->prepare($sql5);
	$result5->execute();
	/*$sql7="UPDATE `gibbonperson` SET `boarder`='$next_boarder_type'	 WHERE `gibbonPersonID`=$gibbonPersonID";
	$result7=$connection2->prepare($sql7);
	$result7->execute();*/
	//echo"<br>$sql7<br>";
	}
}
$URL=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/change_class.php";
header("Location: {$URL}");		
?>