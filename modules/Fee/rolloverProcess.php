<?php
ini_set('max_execution_time', 1200);
@session_start();
include "../../config.php" ;
include "custom_funcions.php" ;
try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}
print_r($_POST);
if($_POST){
	extract($_POST);
	if(isset($re_enrolID)){
	if(sizeOf($re_enrolID)>0){
		$re_enrolID=array_unique($re_enrolID);
		foreach($re_enrolID as $id){
			$sqlEnrol="INSERT INTO `gibbonstudentenrolment`(`gibbonStudentEnrolmentID`, `gibbonPersonID`, `gibbonSchoolYearID`, `gibbonYearGroupID`, `gibbonRollGroupID`) VALUES ";
			$class="nextClass_".$id;
			$section="nextSection_".$id;
			$sqlEnrol.="(NULL,$id,$nextYearID,{$$class},{$$section})";
			$resultEnrol=$connection2->prepare($sqlEnrol);
			$resultEnrol->execute();
			$tmp=$connection2->lastInsertId();
			PopulateStudentPayableFee($id,$nextYearID,$$class,$$section,$tmp,$connection2,'old');
		}
	}
	}
	//$leftID=unserialize($leftID);
	/*if(isset($leftID)){
	if(sizeOf($leftID)>0){
		$ids=implode(",",$leftID);
		//$date=date('Y-m-d');
		echo $sqlLeft="UPDATE `gibbonperson` SET `dateEnd`=(SELECT `lastDay` FROM `gibbonschoolyear` WHERE `gibbonSchoolYearID`={$_SESSION[$guid]["gibbonSchoolYearID"]}) WHERE `gibbonPersonID` IN ($ids)";
		$resultLeft=$connection2->prepare($sqlLeft);
		$resultLeft->execute();
	}
	}*/
}
$URL=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/rollover.php";
header("Location: {$URL}");	
 ?>