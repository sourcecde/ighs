<?php
include "../../config.php" ;
@session_start();
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
	extract($_POST);
		$data=array();
		try{
		$sql="SELECT `gibbonperson`.`preferredName`, `gibbonperson`.`account_number`,`gibbonstudentenrolment`.`rollOrder`,`gibbonrollgroup`.`name` as `section`,
				`gibbonyeargroup`.`name` as `class` FROM `gibbonperson` 
				LEFT JOIN `gibbonstudentenrolment` ON `gibbonstudentenrolment`.`gibbonPersonID`=`gibbonperson`.`gibbonPersonID` 
				LEFT JOIN `gibbonrollgroup` ON `gibbonrollgroup`.`gibbonRollGroupID`=`gibbonstudentenrolment`.`gibbonRollGroupID`
				LEFT JOIN `gibbonyeargroup` ON `gibbonyeargroup`.`gibbonYearGroupID`=`gibbonstudentenrolment`.`gibbonYearGroupID`
				WHERE `gibbonperson`.`gibbonPersonID`=$personID AND `gibbonstudentenrolment`.`gibbonSchoolYearID`=$yearID";
		$result=$connection2->prepare($sql);
		$result->execute();
		$data=$result->fetch();
		}
		catch(PDOException $e) {
		  echo $e->getMessage();
		}
		echo json_encode($data);
}