<?php
@session_start() ;
//Including Global Functions & Dtabase Configuration.
include "../../../functions.php" ;
include "../../../config.php" ;

//New PDO DB connection
try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}
if(isset($_REQUEST)){
	print_r($_REQUEST);
	extract($_REQUEST);
	if($action=='addExam'){
		extract($inputdata);
		if($subjectID=='')
			$subjectID=0;
		$optionalName=$optional=='N'?'NULL':"'$optionalName'";
		$groupName=$group=='N'?'NULL':$groupName;
		$parentSubjectID=$parentSubject=='N'?'NULL':$parentSubjectID;
		try {
		echo $sql1="INSERT INTO `lakshya_exam_master`(`examID`, `termID`, `yearGroupID`, `optional`, `subjectID`, `grade`, `practical`, `theoryTotalMarks`, `practicalTotalMarks`, `theoryPassMarks`, `practicalPassMarks`, `optionalName`, `parentSubjectID`, `groupName`)
							VALUES (NULL, $termID, $yearGroupID, '$optional','$subjectID', '$grade', '$practical','$theoryTotalMarks','$practicalTotalMarks','$theoryPassMarks','$practicalPassMarks',$optionalName,$parentSubjectID,'$groupName')";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		}
		catch(PDOException $e) { 
		echo $e;
		}
	}
}
 ?>