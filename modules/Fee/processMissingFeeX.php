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

if($_POST){
	$studentNo=0;
	$yearID=$_REQUEST["yearID"];
	$sql="SELECT `gibbonStudentEnrolmentID`,`gibbonyeargroup`.`name` AS class,`gibbonperson`.`boarder`,`gibbonperson`.`gibbonPersonID`  
			FROM `gibbonstudentenrolment` 
			LEFT JOIN `gibbonperson` ON `gibbonperson`.`gibbonPersonID`=`gibbonstudentenrolment`.`gibbonPersonID` 
			LEFT JOIN `gibbonyeargroup` ON `gibbonyeargroup`.`gibbonYearGroupID`=`gibbonstudentenrolment`.`gibbonYearGroupID` 
			WHERE `gibbonSchoolYearID`=$yearID";
	$result=$connection2->prepare($sql);
	$result->execute();
	$students=$result->fetchAll();
	echo "<pre>";
	//print_r($students);
	echo "</pre>";
	//Start of Student Loop
	foreach($students as $s){
		//Old or New Student Check.
		$sql1="SELECT COUNT(`gibbonStudentEnrolmentID`) AS N FROM `gibbonstudentenrolment` WHERE `gibbonPersonID`={$s['gibbonPersonID']}";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		$n=$result1->fetch();
		$type=$n['N']==1?'New':'Old';
		// Fee Boarder Class.
		$sql2="SELECT `fee_boarder_class_id` FROM `fee_boarder_class` WHERE class='{$s['class']}' AND border='{$s['boarder']}'";
		$result2=$connection2->prepare($sql2);
		$result2->execute();
		$fb=$result2->fetch();
		//Finding Missing fee
		$sql3="SELECT `fee_rule_master_id`
				FROM `fee_rule_master`
				LEFT JOIN `fee_type_master` ON `fee_rule_master`.`fee_type_master_id`=`fee_type_master`.`fee_type_master_id`
				WHERE `fee_boarder_class_id`={$fb['fee_boarder_class_id']} AND `fee_rule_master`.`gibbonSchoolYearID`='$yearID'
				AND `fee_type_master`.`fee_type_desc` NOT IN (SELECT `fee_type_short_name` FROM `fee_payable` WHERE `gibbonStudentEnrolmentID`='{$s['gibbonStudentEnrolmentID']}')"; 
			if($type=='Old')
			echo $sql3.=" AND `fee_rule_master`.`onetime`=0";	
		$result3=$connection2->prepare($sql3);
		$result3->execute();
		$mIDs=$result3->fetchAll();
		
		$mIDa=array();
		foreach($mIDs as $m){
			$mIDa[]=$m['fee_rule_master_id'];
		}
		
		if($mIDa){
			$studentNo++;
			$missingID=implode(',',$mIDa);
			//Collecting Fee Data of Missing Fees.
			$sql4="SELECT `fee_rule_master`.*,`fee_type_master`.fee_type_desc as fee_short_type,`fee_type_master`.yearly,`fee_type_master`.jan,`fee_type_master`.feb,`fee_type_master`.mar,`fee_type_master`.apr,`fee_type_master`.may,
					`fee_type_master`.jun,`fee_type_master`.jul,`fee_type_master`.aug,`fee_type_master`.sep,`fee_type_master`.oct,
					`fee_type_master`.nov,`fee_type_master`.dec
					FROM `fee_rule_master`
					LEFT JOIN `fee_type_master`  ON `fee_rule_master`.fee_type_master_id=`fee_type_master`.fee_type_master_id 
					WHERE `fee_rule_master`.`fee_rule_master_id` IN ($missingID)";
			$result4=$connection2->prepare($sql4);
			$result4->execute();
			$feeData=$result4->fetchAll();
				//Inserting Missing Fee Data.
				//$amount=0;
				$date=date('Y-m-d');
				$schoolyeararr=array(0=>'yearly',1=>'jan',2=>'feb',3=>'mar',4=>'apr',5=>'may',6=>'jun',7=>'jul',8=>'aug',9=>'sep',10=>'oct',11=>'nov',12=>'dec');
				$sql5="INSERT INTO `fee_payable`(`fee_payable_id`, `gibbonSchoolYearID`, `gibbonPersonID`, `gibbonStudentEnrolmentID`, `rule_id`, `fee_type_master_id`, `month_no`, `month_name`, `amount`, `concession`, `net_amount`, `payment_staus`, `created_date`,`fee_type_short_name`) VALUES ";
				$i=0;
				foreach ($feeData as $value) {
					foreach ($schoolyeararr as $key=>$monthvalue) {
						if($value[$monthvalue]==1)
						{
							$amount=$value['amount'];
							if($i++!=0)
								$sql5.=", ";
						$sql5.="(NULL,$yearID,{$s['gibbonPersonID']},{$s['gibbonStudentEnrolmentID']},{$value['fee_rule_master_id']},{$value['fee_type_master_id']},$key,'$monthvalue',$amount,'0.00',$amount,'unpaid','$date','{$value['fee_short_type']}' )";
						}
					}
				}
				
				try {
					
					$result5=$connection2->prepare($sql5);
					$result5->execute();
				}
				catch(PDOException $e) {
					echo $e;
				}
				
		}
	}
	//End of Student Loop
	echo "Missing Fees are Posted Successfully!!";
	echo "<br>Affected Student: $studentNo";
}	

 ?>