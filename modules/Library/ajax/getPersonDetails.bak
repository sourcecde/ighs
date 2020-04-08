<?php
/* 
	This File Url:
	$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajax/getPersonDetails.php" ;
*/
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
	if($action=='getPersonIDbyAccountNo'){
		try{
		$sql1="SELECT `gibbonPersonID` FROM `gibbonperson` WHERE `account_number`=$account_number";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		$data=$result1->fetch();
		}
		catch(PDOException $e) { 
		echo $e;
		}
		echo $data['gibbonPersonID']+0;
	}
	else if($action=='fetchStudentData'){
		$data=array();
		try{
		$sql="SELECT `gibbonperson`.`preferredName`, `gibbonperson`.`account_number`,`gibbonstudentenrolment`.`rollOrder`,`gibbonrollgroup`.`name` AS class   
				FROM `gibbonperson` 
				LEFT JOIN `gibbonstudentenrolment` ON `gibbonstudentenrolment`.`gibbonPersonID`=`gibbonperson`.`gibbonPersonID` 
				LEFT JOIN `gibbonrollgroup` ON `gibbonrollgroup`.`gibbonRollGroupID`=`gibbonstudentenrolment`.`gibbonRollGroupID`
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
	else if($action=='fetchBorrowData'){
		$data=array();
		try{
		echo $sql="SELECT `gibbonStudentEnrolmentID` FROM `gibbonstudentenrolment` WHERE `gibbonPersonID`=$personID AND `gibbonSchoolYearID`=$yearID";
		$result=$connection2->prepare($sql);
		$result->execute();
		$ID=$result->fetch();
		}
		catch(PDOException $e) {
		  echo $e->getMessage();
		}
		print_r($ID);
		$enrollID=$ID['gibbonStudentEnrolmentID'];
		
		try{
		$sql="SELECT `dateBorrow`,`dateDue`,`lakshya_library_bookmaster`.`acc_no`,`lakshya_library_booknamemaster`.`title`,`lakshya_library_booknamemaster`.`author`  
				FROM `lakshya_library_borrowmaster` 
				LEFT JOIN `lakshya_library_bookmaster` ON `lakshya_library_bookmaster`.`bookID`=`lakshya_library_borrowmaster`.`bookID`
				LEFT JOIN `lakshya_library_booknamemaster` ON `lakshya_library_booknamemaster`.`bookNameID`=`lakshya_library_bookmaster`.`bookNameID`
				WHERE `borrowStatus`='Pending' AND `studentID`=$enrollID";
		$result=$connection2->prepare($sql);
		$result->execute();
		$data=$result->fetchAll();
		}
		catch(PDOException $e) {
		  echo $e->getMessage();
		}
		$size=sizeOf($data);
		if($size==0)
			echo "<tr><td colspan='5'><b>No borrowed history.</b></td></tr>";
		else{
			foreach($data as $d){
				echo "<tr>";
					echo "<td>{$d['acc_no']}</td>";
					echo "<td>{$d['title']}</td>";
					echo "<td>{$d['author']}</td>";
					echo "<td>{$d['dateBorrow']}</td>";
					echo "<td>{$d['dateDue']}</td>";
				echo "</tr>";
			}
		}
	}
}
 ?>