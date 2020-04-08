<?php
@session_start() ;
//Including Global Functions & Dtabase Configuration.
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
	if($action=='add'){
		$sql="INSERT INTO `lakshya_exam_optionalsubjects`(`optionalID`, `studentID`, `examID`, `subjectID`) VALUES ";
		$tmp="";
		foreach($data as $studentID=>$d){
			foreach($d as $examID=>$subjectID){
				$tmp.=$tmp!=""?", ":"";
				$tmp.="(NULL,$studentID,$examID,$subjectID)";
			}
		}
		if($tmp!=""){
			$sql.=$tmp;
			try{
			$result=$connection2->prepare($sql);
			$result->execute();
			}
			catch(PDOException $e) {
			  echo $e->getMessage();
			}
		}
	}
	if($action=='edit'){
		foreach($data as $optionalID=>$subjectID){
			try{
				$sql="UPDATE `lakshya_exam_optionalsubjects` SET  `subjectID`=$subjectID WHERE `optionalID`=$optionalID";
				$result=$connection2->prepare($sql);
				$result->execute();
			}
			catch(PDOException $e) {
				echo $e->getMessage();
			}
		}	
	}
	$url=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/addOptionalSubjects.php";
	header("Location:$url");
}
 ?>