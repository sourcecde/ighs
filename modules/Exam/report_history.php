<?php
@session_start();
/*
//Including Global Functions & Dtabase Configuration.
include "../../functions.php" ;
include "../../config.php" ;
try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}
*/
require_once('create_report_card.php');
	$personID=$gibbonPersonID;
	try{
		$sql1="SELECT * FROM `gibbonstudentenrolment` 
		WHERE `gibbonstudentenrolment`.`gibbonPersonID`=$personID";
		$result1=$connection2->prepare($sql1);
		$result1->execute();	
		$pData=$result1->fetchall();
	}
	catch(PDOException $e){
		echo $e;
	}
	foreach($pData as $p){
		try{
			$sql2="SELECT `gibbonSchoolYearTermID` FROM `gibbonschoolyearterm` WHERE `gibbonschoolyearID`='".$p['gibbonSchoolYearID']."'";
			$result1=$connection2->prepare($sql2);
			$result1->execute();	
			$termData=$result1->fetchall();
		}
		catch(PDOException $e){
			echo $e;
		}
		foreach($termData as $t){
			echo "<div class='collapse'>";
			create_report_card($p['gibbonStudentEnrolmentID'],$p['gibbonYearGroupID'],$t['gibbonSchoolYearTermID'],$p['gibbonSchoolYearID']);
			echo "</div>";
		}
	}
?>