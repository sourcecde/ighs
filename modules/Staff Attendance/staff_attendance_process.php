<?php
ob_start();
@session_start() ;
include "../../functions.php" ;
include "../../config.php" ;

//New PDO DB connection
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
$t_date_a=explode('/',$date);
$n_date=$t_date_a[2]."-".$t_date_a[1]."-".$t_date_a[0];
	$sql="SELECT `gibbonStaffID` FROM `gibbonstaff`
		LEFT JOIN gibbonperson on gibbonperson.gibbonPersonID=gibbonstaff.gibbonPersonID WHERE (gibbonperson.dateEnd IS NULL OR gibbonperson.dateEnd>'$n_date') AND gibbonperson.dateStart <='$n_date' ORDER BY gibbonstaff.priority";
		$result1=$connection2->prepare($sql);
		$result1->execute();
		$staffs=$result1->fetchAll();
		
		/* Insert Into database */
		$sql="INSERT INTO `lakshyastaffattendancelog`(`attendanceLogID`, `date`, `gibbonStaffID`, `type`, `comment`, `StaffIDTaker`, `timeStamp`) VALUES ";
		$i=0;
		foreach($staffs as $staff){
			if($i++!=0)
				$sql.=", ";
			$attd='attd_'.$staff['gibbonStaffID'];
			$reason='reason_'.$staff['gibbonStaffID'];
			$timeStamp=time();
			$sql.= "(NULL,'$date',{$staff['gibbonStaffID']},'{$$attd}','{$$reason}',{$_SESSION[$guid]['gibbonPersonID']},$timeStamp)";
		}
		//echo $sql;
		$result=$connection2->prepare($sql);
		$result->execute();
		/* Insert Into database */
		$url="{$_SESSION[$guid]["absoluteURL"]}/index.php?q=/modules/{$_SESSION[$guid]["module"]}/staff_attendance.php";
		header("Location:$url");
		
}
ob_flush();
 ?>