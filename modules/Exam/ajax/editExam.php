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
	extract($_REQUEST);
	if($action=='updateExam'){
		extract($inputdata);
		if($subjectID=='')
			$subjectID=0;
		$optionalName=$optional=='N'?'NULL':"'$optionalName'";
		$groupName=$group=='N'?'':$groupName;
		$parentSubjectID=$parentSubject=='N'?'NULL':$parentSubjectID;
		try {
		$sql1="UPDATE `lakshya_exam_master` SET `termID`=$termID,`yearGroupID`=$yearGroupID,`optional`='$optional',`subjectID`=$subjectID,`grade`='$grade',`practical`='$practical',`theoryTotalMarks`='$theoryTotalMarks',`practicalTotalMarks`='$practicalTotalMarks',`theoryPassMarks`='$theoryPassMarks',`practicalPassMarks`='$practicalPassMarks',`optionalName`=$optionalName,`parentSubjectID`=$parentSubjectID,`groupName`='$groupName' WHERE `examID`=$examID";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		}
		catch(PDOException $e) { 
		echo $e;
		}
	}
}
 ?>