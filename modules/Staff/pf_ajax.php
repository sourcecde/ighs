<?php
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
extract($_POST);
if($action=='fetchECRData'){
		$sql="SELECT `name` FROM `gibbonschoolyear` WHERE `gibbonSchoolYearID`=$yearID";
		$result=$connection2->prepare($sql);
		$result->execute();
		$yearData=$result->fetch();
		$tmp=explode('-',$yearData['name']);
//	$year=$month<4?$tmp[1]:$tmp[0];
	$year=$tmp[0];
//  $year=2020;
	$numOfDays=cal_days_in_month(CAL_GREGORIAN,$month,$year);
//	$numOfDays=cal_days_in_month(CAL_GREGORIAN,01,2020);
//	$numOfDays=31;
	$dateRangeStart="$year-$month-01";
	$dateRangeEnd="$year-$month-$numOfDays";

//	$dateRangeStart="2020-01-01";
//	$dateRangeEnd="2020-01-31";
	
//	$sql="SELECT `gibbonStaffID`,`pf_no`,`guardian`,`relationship`,`reasonOfLeaving`,`gender`,`dob`,`dateStart`,`dateEnd`,`preferredName`
//			FROM `gibbonstaff` 
//			LEFT JOIN `gibbonperson` ON `gibbonperson`.`gibbonPersonID`=`gibbonstaff`.`gibbonPersonID` WHERE `pf_no`!='' AND (`dateEnd` IS NULL OR `dateEnd`>='$dateRangeStart') AND (`dateStart`<='$dateRangeEnd')";

//	$sql="SELECT `gibbonStaffID`,`pf_no`,`guardian`,`relationship`,`reasonOfLeaving`,`gibbonstaff`.`gender`,`gibbonstaff`.`dob`,`gibbonstaff`.`dateStart`,`gibbonstaff`.`dateEnd`,`gibbonstaff`.`preferredName`,`uan_no`  
//			FROM `gibbonstaff` 
//			LEFT JOIN `gibbonperson` ON `gibbonperson`.`gibbonPersonID`=`gibbonstaff`.`gibbonPersonID` WHERE `pf_no`!='' AND (`dateEnd` IS NULL OR `dateEnd`>='$dateRangeStart') AND (`dateStart`<='$dateRangeEnd')";

	$sql="SELECT `gibbonStaffID`,`pf_no`,`guardian`,`relationship`,`reasonOfLeaving`,`gender`,`dob`,`dateStart`,`dateEnd`,`preferredName`,`uan_no`  
			FROM `gibbonstaff` WHERE `pf_no`!='' AND (`dateEnd` IS NULL OR `dateEnd`>='$dateRangeStart') AND (`dateStart`<='$dateRangeEnd')";
			
	$result=$connection2->prepare($sql);
	$result->execute();
	$data=$result->fetchAll();


	$sql1="SELECT `staff_id`,`paid_amount` 
			FROM `lakshyasalarymaster` 
			LEFT JOIN `lakshyasalarypayment` ON `lakshyasalarypayment`.`master_id`=`lakshyasalarymaster`.`master_id`
			WHERE `rule_id`=1 AND `month`=$month AND `year_id`=$yearID";
	$result1=$connection2->prepare($sql1);
	$result1->execute();
	$data1=$result1->fetchAll();

	$sql2="SELECT `staff_id`,`attended` FROM `lakshyasalaryattendance` WHERE `month`=$month AND `year_id`=$yearID";
	$result2=$connection2->prepare($sql2);
	$result2->execute();
	$data2=$result2->fetchAll();
	
	$paidAmount=array();
	foreach($data1 as $d){
		$paidAmount[$d['staff_id']]=$d['paid_amount'];
	}
	$attendedDay=array();
	foreach($data2 as $d){
		$attendedDay[$d['staff_id']]=$d['attended'];
	}
	
	$dataArray=array();
	foreach($data as $d){
		$id=$d['gibbonStaffID']+0;
		if(array_key_exists($id,$paidAmount)){
		    $tmp=explode('WB/CA/54346/',$d['pf_no']);
			@$member_id=$tmp[1]+0;
			$memeber_name=$d['preferredName'];
			$memeber_uan=$d['uan_no'];
			$epf_gross=$paidAmount[$id];
			$eps_gross=$epf_gross<15000?$epf_gross:15000;
			$epf=round($epf_gross*0.12);
			$eps=round($eps_gross*0.0833);
			$difference=round($eps_gross*0.12)-$eps;
			$guardian="";
			$relationship="";
			$dob="";
			$gender="";
			$doj="";
			$dol="";
			$reason="";
			$w_day=$numOfDays;
			if(($d['dateStart'])>=strtotime($dateRangeStart)){
				$guardian=$d['guardian'];
				$relationship=$d['relationship'];
				$dob=date('d/m/Y',strtotime($d['dob']));
				$gender=$d['gender'];
				$doj=date('d/m/Y',strtotime($d['dateStart']));
				$tmp=explode('-',$d['dateStart']);
				$startD=$tmp[2];
				$w_day=$numOfDays+1-$startD;
			}
			else if($d['dateEnd']!=''){
			if(($d['dateEnd'])<=strtotime($dateRangeEnd)){
				$dol=date('d/m/Y',strtotime($d['dateEnd']));
				$reason=$d['reasonOfLeaving'];
				$tmp=explode('-',$d['dateEnd']);
				$endD=$tmp[2];
				$w_day=$endD;
			}
			}
			$ncp=$w_day-$attendedDay[$id];
		//	$dataArray[$member_id]=array($member_id,$memeber_name,$epf_gross,$eps_gross,$epf,$epf,$eps,$eps,$difference,$difference,$ncp,0,0,0,0,0,$guardian,$relationship,$dob,$gender,$doj,$doj,$dol,$dol,$reason);
		    $dataArray[$member_id]=array($memeber_uan,$memeber_name,$epf_gross,$epf_gross,$eps_gross,$eps_gross,$epf,$eps,$difference,$ncp,0);
		}
	}
  
   
   ksort($dataArray);
   echo json_encode($dataArray);
}
}
?>