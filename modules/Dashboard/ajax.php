<?php
/*
	Data Source for all dashboard elements.
	@Nazmul
*/ 
include "../../config.php" ;
@session_start();
date_default_timezone_set($_SESSION[$guid]["timezone"]);
try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}
@session_start() ;
if($_POST){
$month_arr1=array(1=>'jan',2=>'feb',3=>'mar',4=>'apr',5=>'may',6=>'jun',7=>'jul',8=>'aug',9=>'sep',10=>'oct',11=>'nov',12=>'dec');
$month_arr2=array('jan'=>'January','feb'=>'February','mar'=>'March','apr'=>'April','may'=>'May','jun'=>'June','jul'=>'July','aug'=>'August','sep'=>'September','oct'=>'October','nov'=>'November','dec'=>'December');
$month_sequence=array('April','May','June','July','August','September','October','November','December','January','February','March');

extract($_POST);
if($action=='getStudentNO'){
$sql="SELECT count(distinct `gibbonperson`.`gibbonPersonID`) as N,`gibbonyeargroup`.`nameShort` 
	FROM `gibbonstudentenrolment`
	LEFT JOIN `gibbonperson` ON `gibbonperson`.`gibbonPersonID`=`gibbonstudentenrolment`.`gibbonPersonID`
	LEFT JOIN `gibbonyeargroup` ON `gibbonyeargroup`.`gibbonYearGroupID`=`gibbonstudentenrolment`.`gibbonYearGroupID` 
	WHERE status='Full' AND `gibbonstudentenrolment`.`gibbonSchoolYearID`=$year_id ";
	//$sql.=" AND (dateStart IS NULL OR dateStart<='" . date("Y-m-d") . "')";
	$sql.=" AND (dateEnd IS NULL OR dateEnd>='" . date("Y-m-d") . "')";
	$sql.=" GROUP BY `gibbonyeargroup`.`gibbonYearGroupID`";
//echo $sql;
$result=$connection2->prepare($sql);
$result->execute();
$data=$result->fetchAll();	
$dataArray=array();
$total=0;
foreach($data as $d){
	$dataArray[]=array('y'=>$d['N'],'label'=>$d['nameShort']);
	$total+=$d['N'];
}
	echo json_encode(array($total,$dataArray));
}
else if($action=='getTodaysCollection'){
			$date_a=explode('/',$date);
			$date=$date_a[2]."-".$date_a[1]."-".$date_a[0];
	$sql="SELECT SUM(net_amount) AS 'total_amount',fee_type_master.`fee_type_name` 
			FROM fee_payable 
			LEFT JOIN fee_type_master ON fee_type_master.`fee_type_master_id`=fee_payable.`fee_type_master_id` 
			LEFT JOIN payment_master ON payment_master.payment_master_id=fee_payable.payment_master_id 
			WHERE fee_payable.payment_date='$date' 
			AND fee_payable.gibbonSchoolYearID=$year_id 
			GROUP BY fee_payable.fee_type_short_name HAVING total_amount>0";
			
	$result=$connection2->prepare($sql);
	$result->execute();
	$fee_data=$result->fetchAll();	
	$dataArray=array();
	$total=0;
		foreach($fee_data as $f){
		$total+=$f['total_amount'];	
		}
		
	$sql="SELECT SUM(fine_amount) AS 'fine' 
	FROM `payment_master` 
	WHERE  payment_date='$date' AND gibbonSchoolYearID=$year_id ";
	$result=$connection2->prepare($sql);
	$result->execute();
	$fine_data=$result->fetch();
		$total+=$fine_data['fine'];
	$sql="SELECT SUM(price) AS transport 
	FROM transport_month_entry
	LEFT JOIN payment_master ON payment_master.payment_master_id=transport_month_entry.payment_master_id
	WHERE  payment_master.payment_date='$date' AND payment_master.gibbonSchoolYearID=$year_id  ";
	$result=$connection2->prepare($sql);
	$result->execute();
	$trnsprt_data=$result->fetch();
		$total+=$trnsprt_data['transport'];
		
		foreach($fee_data as $f){
			$dataArray[]=array('y'=>$f['total_amount']+0,'name'=>$f['fee_type_name'] ,'legendMarkerType'=>"triangle");
		}
		if($trnsprt_data['transport']>0)	
		$dataArray[]=array('y'=>$trnsprt_data['transport']+0,'name'=>'Transport','legendMarkerType'=>"triangle");
		if($fine_data['fine']>0)	
		$dataArray[]=array('y'=>$fine_data['fine']+0,'name'=>'Fine','legendMarkerType'=>"circle");
	echo json_encode(array($total,$dataArray));
}
else if($action=='getTodaysAttendance'){
	$date_a=explode('/',$date);
	$dateF=$date_a[2]."-".$date_a[1]."-".$date_a[0];
	$sql="SELECT COUNT(`gibbonAttendanceLogPersonID`) AS N,`direction`,`gibbonrollgroup`.`gibbonRollGroupID` 
		FROM `gibbonattendancelogperson` 
		LEFT JOIN `gibbonstudentenrolment` ON `gibbonstudentenrolment`.`gibbonPersonID` = `gibbonattendancelogperson`.`gibbonPersonID` 
		LEFT JOIN `gibbonrollgroup` ON `gibbonrollgroup`.`gibbonRollGroupID` =`gibbonstudentenrolment`.`gibbonRollGroupID` 
		WHERE `date`='$dateF' AND `gibbonstudentenrolment`.`gibbonSchoolYearID`=$year_id 
		GROUP BY `gibbonrollgroup`.`gibbonRollGroupID`,`direction`";
	$result=$connection2->prepare($sql);
	$result->execute();
	$data=$result->fetchAll();
	$t_data=array();
		foreach($data as $d){
			$t_data[$d['gibbonRollGroupID']+0][$d['direction']]=$d['N']+0;
		}
	$sql="SELECT `gibbonYearGroupID`,`nameShort` FROM `gibbonyeargroup` ORDER BY `nameShort`";
	$result=$connection2->prepare($sql);
	$result->execute();
	$data_section=$result->fetchAll();
	
	$dataArray=array();
	
		foreach($data_section as $d){
			$id=$d['gibbonRollGroupID']+0;
			if(array_key_exists($id,$t_data)){
				if(array_key_exists('In',$t_data[$id]))
					$dataArray['In'][]=array('y'=>$t_data[$id]['In'],'label'=>$d['name']);
				else
					$dataArray['In'][]=array('y'=>0,'label'=>$d['name']);
				if(array_key_exists('Out',$t_data[$id]))
					$dataArray['Out'][]=array('y'=>$t_data[$id]['Out'],'label'=>$d['name']);
				else
					$dataArray['Out'][]=array('y'=>0,'label'=>$d['name']);
			}
		}
	echo json_encode(array($dataArray,$date));
	
}
else if($action=='getPaymentHistory'){
	$sql1="SELECT `name` FROM `gibbonschoolyear` WHERE `gibbonSchoolYearID`= $year";
	$result1=$connection2->prepare($sql1);
	$result1->execute();
	$yearName=$result1->fetch();
	if($month<4)
		$Y=substr($yearName['name'],5,4);
	else
		$Y=substr($yearName['name'],0,4);
	$sql="SELECT SUM(`net_total_amount`) AS N,`payment_date` FROM `payment_master` WHERE `payment_date` LIKE '$Y-$month-__' GROUP BY `payment_date`";
	//$sql="SELECT SUM(`net_total_amount`) AS N,`payment_date` FROM `payment_master` WHERE `payment_date` LIKE '2016-01-__' GROUP BY `payment_date`";
	$result=$connection2->prepare($sql);
	$result->execute();
	$data=$result->fetchAll();
	$dataArray=array();
		
		foreach($data as $d){
			$m=date('jS',strtotime($d['payment_date']));
			$dataArray[]=array('label'=>$m,'y'=>$d['N']+0);
		}
		$monthD=$month_arr1[$month+0]."-".$Y;
	echo json_encode(array($monthD,$dataArray));
}
else if($action=='getStaff'){
	$sql="SELECT count(`gibbonStaffID`) AS N FROM `gibbonstaff` LEFT JOIN `gibbonperson` ON `gibbonperson`.`gibbonPersonID`=`gibbonstaff`.`gibbonPersonID` WHERE (`gibbonstaff`.`dateEnd` IS NULL) OR `gibbonstaff`.`dateEnd`>='".date('Y-m-d')."'";
	$result=$connection2->prepare($sql);
	$result->execute();
	$data=$result->fetch();
	echo $data['N'];
}
else if($action=='getFeeCount'){
	$date_a=explode('/',$date);
	$date=$date_a[2]."-".$date_a[1]."-".$date_a[0];
	$sql="SELECT COUNT(DISTINCT `payment_master_id`) AS N FROM `payment_master` WHERE `payment_date`='$date'";
	$result=$connection2->prepare($sql);
	$result->execute();
	$data=$result->fetch();
	echo $data['N'];
}
else if($action=='getTransportUser'){
	$sql="SELECT count(`gibbonPersonID`) as N FROM `gibbonperson` WHERE `avail_transport`='Y' AND `active_transport`='Y' AND (`dateEnd` IS NULL OR `dateEnd`>'".date('Y-m-d')."')";
	$result=$connection2->prepare($sql);
	$result->execute();
	$data=$result->fetch();
	echo $data['N'];
}
else if($action=='getPendingApplication'){
	$sql="SELECT COUNT(`gibbonApplicationFormID`) AS N FROM `gibbonapplicationform` WHERE `status` IN ('Pending', 'Waiting List') AND `gibbonSchoolYearIDEntry`=$year_id";
	$result=$connection2->prepare($sql);
	$result->execute();
	$data=$result->fetch();
	echo $data['N'];
}
else if($action=='getBirthday'){
	$sql="SELECT `preferredName`,`account_number` FROM `gibbonperson` WHERE `dob` like '____-".date('m-d')."' AND (`dateEnd` IS NULL OR `dateEnd`>'".date('Y-m-d')."')";
	//$sql="SELECT `preferredName`,`account_number` FROM `gibbonperson` WHERE `dob` like '____-08-16' AND (`dateEnd` IS NULL OR `dateEnd`>'".date('Y-m-d')."')";
	$result=$connection2->prepare($sql);
	$result->execute();
	$data=$result->fetchAll();
	$msg="";
	if($result->rowCount()>0){
		foreach($data as $d){
			$ac=$d['account_number']+0;
			$msg.="<p style='color:#FFEB3B'>{$d['preferredName']}<br><small style='float:right;'>A/c No: <b class='d'>{$ac}</b></small></p><br>";
		}
		echo $msg;
	}
	else
		echo "<p class='d' style='color:#FFEB3B; font-size:20px;'>0<p>";
}
}	
?>
								