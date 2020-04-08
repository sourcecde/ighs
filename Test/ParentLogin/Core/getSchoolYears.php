<?php 
session_start();
if (file_exists("./dbCon.php")) {
	include "./dbCon.php" ;
}
if($_GET){
	if($_GET['action']=='getSchoolYears'){
		try{
		$sql="SELECT `gibbonSchoolYearID`, `name`, `status` FROM `gibbonschoolyear` WHERE `gibbonSchoolYearID`>=$LastSchoolYear";
		$result=$connection1->prepare($sql);
		$result->execute();
		$years=$result->fetchAll();
		echo json_encode($years);
		}
		catch(PDOException $e) {
		  echo $e->getMessage();
		}
	}
	else if($_GET['action']=='getSchoolDetails'){
		$Address='';
		$ContactNo='';
		try{
		$sql="SELECT `name`,`value` FROM `gibbonsetting` WHERE `name` LIKE 'header%'";
		$result=$connection1->prepare($sql);
		$result->execute();
		$headers=$result->fetchAll();
			foreach ($headers as $value) {
				if($value['name']=="header1"){
					$Address=ucwords(strtolower($value['value']));
				}
				else if($value['name']=="header2"){
					$contact=$value['value'];
					$contactArr=explode('.', $contact);
					
					$ContactNo=ltrim($contactArr[1]);
				}
			}
		$_SESSION['SchoolDetails']=array('Address'=>$Address,'ContactNo'=>$ContactNo);
		echo json_encode($_SESSION['SchoolDetails']);
		}
		catch(PDOException $e) {
		  echo $e->getMessage();
		}
	}
}
?>