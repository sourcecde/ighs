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
	if($action=='addGrade'){
		try{
		$sql1="INSERT INTO `lakshya_exam_grade`(`gradeID`, `grade`, `isfail`) VALUES (NULL,'$gradeName',$isfail)";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		}
		catch(PDOException $e) { 
		echo $e;
		}
		echo "Added Sucessfully!!";
	}
	else if($action=='editGrade'){
		try{
		$sql1="UPDATE `lakshya_exam_grade` SET `grade`='$gradeName' WHERE `gradeID`=$gradeID";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		}
		catch(PDOException $e) { 
		echo $e;
		}
		echo "Updated Sucessfully!!";
	}
	else if($action=='delGrade'){
		try{
		$sql1="DELETE FROM `lakshya_exam_grade` WHERE `gradeID`=$gradeID";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		}
		catch(PDOException $e) { 
		echo $e;
		}
		echo "Deleted Sucessfully!!";
	}
	else if($action=='isfail'){
		try{
		$sql1="UPDATE `lakshya_exam_grade` SET `isfail`=$isfail WHERE `gradeID`=$gradeID";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		}
		catch(PDOException $e) { 
		echo $e;
		}
		echo "Set to fail";
	}
}
 ?>