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
		echo "<pre>";
		//print_r($data);
		echo "</pre>";
		try {
		$sql1="SELECT `gibbonStudentEnrolmentID` FROM `gibbonstudentenrolment` WHERE `gibbonSchoolYearID`=$yearID AND `gibbonYearGroupID`=$yearGroupID ORDER BY `rollOrder`";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		$students=$result1->fetchAll();
		}
		catch(PDOException $e) { 
		echo $e;
		}
		foreach($students as $s){
			$enID=$s['gibbonStudentEnrolmentID']+0;
			if(array_key_exists($enID,$data)){
				
				try {
				$sql1="INSERT INTO `lakshya_exam_results`(`resultsID`, `studentID`, `termID`, `conduct`, `application`, `remarks`) VALUES (NULL,$enID,$termID,'{$data[$enID]['conduct']}','{$data[$enID]['application']}','{$data[$enID]['remarks']}')";
				$result1=$connection2->prepare($sql1);
				$result1->execute();
				$resultsID=$connection2->lastInsertId();
				}
				catch(PDOException $e) { 
				echo $e;
				}
				$sql="INSERT INTO `lakshya_exam_marks`(`marksID`, `resultsID`, `examID`, `theoryMarks`, `practicalMarks`, `gradeO`,`isabsent`) VALUES ";
				$i=0;
				foreach($data[$enID]['marks'] as $examID=>$v){
					$theoryMarks=isset($v['theoryMarks'])?$v['theoryMarks']:0;
					$practicalMarks=isset($v['practicalMarks'])?$v['practicalMarks']:0;
					$gradeO=isset($v['gradeO'])?$v['gradeO']:'';
					$isabsent=isset($v['isabsent'])?1:0;
					if($i++!=0)
						$sql.=", ";
					$sql.="(NULL,$resultsID,$examID,'$theoryMarks','$practicalMarks','$gradeO','$isabsent')";
				}
				
				try {
				$result1=$connection2->prepare($sql);
				$result1->execute();
				}
				catch(PDOException $e) { 
				echo $e;
				}
				
			}
		}
	}
	if($action='edit'){
		foreach($data as $resultsID=>$resultArr){
			if(array_key_exists('marks',$resultArr)){
				foreach($resultArr['marks'] as $marksID=>$marks){
					$theoryMarks=isset($marks['theoryMarks'])?$marks['theoryMarks']:0;
					$practicalMarks=isset($marks['practicalMarks'])?$marks['practicalMarks']:0;
					$gradeO=isset($marks['gradeO'])?$marks['gradeO']:'';
					$isabsent=isset($marks['isabsent'])?1:0;
					try{
						$sql="UPDATE `lakshya_exam_marks` SET `theoryMarks`='$theoryMarks',`practicalMarks`='$practicalMarks', `gradeO`='$gradeO',`isabsent`='$isabsent'
						WHERE `marksID`='$marksID'";
						$result1=$connection2->prepare($sql);
						$result1->execute();
					}
					catch(PDOException $e) { 
						echo $e;
					}
				}
			}
			try{
				$sql1="UPDATE `lakshya_exam_results` SET `conduct`='{$resultArr['conduct']}', `application`='{$resultArr['application']}', `remarks`='{$resultArr['remarks']}' WHERE `resultsID`='$resultsID'";
				$result1=$connection2->prepare($sql1);
				$result1->execute();
			}
			catch(PDOException $e) { 
				echo $e;
			}
		}		
	}
	$url=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/rankCalculator.php&termID=".$termID."&yearGroupID=".$yearGroupID."&yearID=".$yearID;
	header("Location:$url");
}
 ?>