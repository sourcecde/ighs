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
//print_r($_POST);
extract($_POST);
$officialName= $firstName." ".$surname;
echo $sql="UPDATE `gibbonperson` SET `firstName`='$firstName',`surname`='$surname',`officialName`='$officialName',`preferredName`='$officialName',
		`nameInCharacters`='$officialName',`gender`='$gender',`dob`='".dateformat($dob)."',`category`='$category',`citizenship1`='$citizenship1'
		,`religion`='$religion',`nationalIDCardNumber`='$nationalIDCardNumber' WHERE `gibbonPersonID`='$gibbonPersonID'";
$result=$connection2->prepare($sql);
$result->execute();

echo $sql="UPDATE `gibbonstudentenrolment` 
      SET `gibbonYearGroupID`=$gibbonYearGroupID,
	      `gibbonRollGroupID`=$gibbonRollGroupID,
		  `rollOrder`=$rollOrder
   WHERE `gibbonPersonID`=$gibbonPersonID 
     AND `gibbonSchoolYearID`=".$_SESSION[$guid]['gibbonSchoolYearIDCurrent'];

	/* echo $sql;
	 echo $gibbonYearGroupID;
	 echo $gibbonRollGroupID;
	 echo $gibbonPersonID;
	 echo $_SESSION[$guid]['gibbonSchoolYearIDCurrent'];*/
	 
$result=$connection2->prepare($sql);
$result->execute();




echo $sql="UPDATE `gibbonfamily` SET `homeAddress`='$homeAddress' WHERE `gibbonFamilyID`=$gibbonFamilyID";
$result=$connection2->prepare($sql);
$result->execute();

echo $sql="UPDATE `gibbonfamilyadult` SET `officialName`='$officialName0',`profession`='$profession0',`phone1CountryCode`='$phone1CountryCode0',`phone1`='$phone10',`email`='$email0' WHERE `gibbonFamilyAdultID`=$gibbonFamilyAdultID0";
$result=$connection2->prepare($sql);
$result->execute();

echo $sql="UPDATE `gibbonfamilyadult` SET `officialName`='$officialName1',`profession`='$profession1',`phone1CountryCode`='$phone1CountryCode1',`phone1`='$phone11',`email`='$email1' WHERE `gibbonFamilyAdultID`=$gibbonFamilyAdultID1";
$result=$connection2->prepare($sql);
$result->execute();

echo $sql="UPDATE `gibbonpersonmedical` SET `bloodType`='$bloodType' WHERE `gibbonPersonID`=$gibbonPersonID";
$result=$connection2->prepare($sql);
$result->execute();


function dateformat($a){
	$dob=explode("/",$a);
	return $dob[2]."-".$dob[1]."-".$dob[0];
}

//$URL=$_SESSION[$guid]["absoluteURL"] . "index.php?q=/modules/Students/student_detail_basic_update.php";
$URL=$_SESSION[$guid]["absoluteURL"] . "index.php?q=/modules/Students/student_detail_basic_update.php";
  
header("Location: {$URL}");
?>