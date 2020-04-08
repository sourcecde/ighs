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
	extract($_POST);
	$month_name=array('apr','may','jun','jul','aug','sep','oct','nov','dec','jan','feb','mar');
	if(isset($personID))
	{
		foreach($personID as $id){
			$sql="SELECT `gibbonStudentEnrolmentID`,`gibbonperson`.`transport_spot_price_id`,`vehicle_id`,`transport_fee_yearwise`.`amount` 
					FROM `gibbonperson`
					LEFT JOIN `gibbonstudentenrolment` ON `gibbonstudentenrolment`.`gibbonPersonId`=`gibbonperson`.`gibbonPersonID` 
					LEFT JOIN `transport_fee_yearwise` ON  `transport_fee_yearwise`.`transport_spot_price_id`=`gibbonperson`.`transport_spot_price_id` 
					WHERE  `gibbonstudentenrolment`.`gibbonSchoolYearID`=$schoolYearID AND `transport_fee_yearwise`.`gibbonSchoolYearID`=$schoolYearID AND `gibbonperson`.`gibbonPersonID`=$id";
			$result=$connection2->prepare($sql);
			$result->execute();
			$data=$result->fetch();
			//echo $sql;
			$date=date('Y-m-d h:i:s');
			$sql1="INSERT INTO `transport_month_entry`(`transport_month_entryid`, `gibbonPersonID`, `gibbonStudentEnrolmentID`, `transport_spot_price_id`, `price`, `vehicle_id`, `month_name`, `gibbonSchoolYearID`, `created_date`, `payment_master_id`) VALUES ";
				for($i=0;$i<12;$i++){
					if($i!=0)
						$sql1.=", ";
					$sql1.="(NULL,$id,{$data['gibbonStudentEnrolmentID']},{$data['transport_spot_price_id']},{$data['amount']},{$data['vehicle_id']},'{$month_name[$i]}',$schoolYearID,'$date',0)";
				}
			//echo $sql1;
			$result1=$connection2->prepare($sql1);
			$result1->execute();
		}
	}
	$url=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/monthly_entry.php";
	header("Location:{$url}");
}
 ?>