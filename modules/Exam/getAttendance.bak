<?php
function getWorkingDays($rollGroupID){
	include "C:\wamp\www\lakshya\config.php";
	try {
		$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
		$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	}
	catch(PDOException $e) {
		echo $e->getMessage();
	}
	try{
		$sql1="SELECT COUNT(*) AS Workingday FROM `gibbonattendancelogperson` 
				LEFT JOIN `gibbonstudentenrolment` ON `gibbonstudentenrolment`.`gibbonPersonID`=`gibbonattendancelogperson`.`gibbonPersonID` 
				WHERE `gibbonstudentenrolment`.`gibbonRollGroupID`=$rollGroupID AND 
				`date` BETWEEN (SELECT `firstDay` FROM `gibbonschoolyearterm` WHERE `gibbonSchoolYearTermID`=10) AND (SELECT `lastDay` FROM `gibbonschoolyearterm` WHERE `gibbonSchoolYearTermID`=10) 
				GROUP BY `gibbonstudentenrolment`.`gibbonStudentEnrolmentID` 
				ORDER BY COUNT(*) DESC 
				LIMIT 1";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		$workingDay=$result1->fetch();
	}
	catch(PDOException $e){
		echo $e;
	}
	return $workingDay['Workingday'];
}
function getAttendance($studentID){
	include "C:\wamp\www\lakshya\config.php";
	try {
		$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
		$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	}
	catch(PDOException $e) {
		echo $e->getMessage();
	}
	try{
		$sql1="SELECT COUNT(*) as attendance FROM `gibbonattendancelogperson` 
			LEFT JOIN `gibbonstudentenrolment` ON `gibbonstudentenrolment`.`gibbonPersonID`=`gibbonattendancelogperson`.`gibbonPersonID` 
			WHERE `gibbonstudentenrolment`.`gibbonStudentEnrolmentID`=$studentID
			AND `date` BETWEEN (SELECT `firstDay` FROM `gibbonschoolyearterm` WHERE `gibbonSchoolYearTermID`=10) 
			AND (SELECT `lastDay` FROM `gibbonschoolyearterm` WHERE `gibbonSchoolYearTermID`=10) AND `type`!= 'absent'";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		$attendance=$result1->fetch();
	}
	catch(PDOException $e){
		echo $e;
	}
	return $attendance['attendance'];
}
?>