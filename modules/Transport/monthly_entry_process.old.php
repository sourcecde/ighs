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
@session_start() ;
if($_POST)
{
	if($_REQUEST['action']=='monthlyentryprocess')
	{
		$enrollid=$_REQUEST['enrollid'];
		$month=$_REQUEST['month'];
		$year=$_REQUEST['year'];
		foreach ($enrollid as $value) {
			$sql="SELECT gibbonperson.*,transport_spot_price.price,gibbonstudentenrolment.gibbonStudentEnrolmentID FROM gibbonperson 
LEFT JOIN transport_spot_price ON gibbonperson.transport_spot_price_id=transport_spot_price.transport_spot_price_id 
LEFT JOIN gibbonstudentenrolment ON gibbonstudentenrolment.gibbonPersonId=gibbonperson.gibbonPersonId  
WHERE gibbonperson.gibbonPersonID=".$value." AND gibbonperson.gibbonPersonID NOT IN (SELECT gibbonPersonID FROM transport_month_entry WHERE 
transport_month_entry.gibbonPersonID=".$value." AND month_name='".$month."' AND gibbonSchoolYearID='".$year."')";
			
			$result=$connection2->prepare($sql);
			$result->execute();
			$sportlist=$result->fetch();
			//echo $sportlist['vehicle_id'];
			if($sportlist['transport_spot_price_id'])
			{
				$dataFile=array("gibbonPersonID"=>$value,"gibbonStudentEnrolmentID"=>$sportlist['gibbonStudentEnrolmentID'],"transport_spot_price_id"=>$sportlist['transport_spot_price_id'],"price"=>$sportlist['price'], "month_name"=>$month,"year"=>$year);
				$sqlFile="Insert into  transport_month_entry SET gibbonPersonID=:gibbonPersonID,gibbonStudentEnrolmentID=:gibbonStudentEnrolmentID,transport_spot_price_id=:transport_spot_price_id,price=:price,vehicle_id=".$sportlist['vehicle_id'].",month_name=:month_name,gibbonSchoolYearID=:year" ;
				$resultFile=$connection2->prepare($sqlFile);
				$resultFile->execute($dataFile);
			}
			
		}
	}
}
?>