<?php
/*
Gibbon, Flexible & Open School System
Copyright (C) 2010, Ross Parker

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/


include "../../functions.php" ;
include "../../config.php" ;
//include "custom_funcions.php";

//New PDO DB connection
try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}


@session_start() ;

if($_POST){
	//print_r($_POST);
	extract($_POST);
	$fullName=$firstName." ".$surname;
	$salt=getSalt() ;
	$passwordStrong=hash("sha256", $salt."cps@1234") ;
	echo $username=strtolower(current(explode(' ',$firstName))).sprintf("%04d",$account_number);
	$dob=dateformat($dob);
	$dateStart=dateformat($dateStart);
	if($_FILES["file1"]['error']==0){
		$attachment1='';
		$size1=getimagesize($_FILES["file1"]["tmp_name"]);
		if($size1[0]==240 && $size1[1]==320){
		$attachment1="uploads/" . date("Y") . "/" . date("m") . "/" . $_REQUEST['gibbonPersonID'] . "_240" . strrchr($_FILES["file1"]["name"], ".") ;
		if (is_dir("../../uploads/" . date("Y") . "/" . date("m"))==FALSE) {
			mkdir("../../uploads/" . date("Y") . "/" . date("m"), 0777, TRUE) ;					
		}
		if(file_exists("../../".$attachment1))
			unlink("../../".$attachment1);
		move_uploaded_file($_FILES["file1"]["tmp_name"],"../../".$attachment1); 
		}
	}
	$fail=0;	
	try{
	$sql="INSERT INTO gibbonperson SET address1='$homeAddress', address1district='$homeAddressDistrict', address1country ='$homeAddressCountry',fatherName='$officialName1', phone1='$phone11', phone2='$phone12', fatherProfation='$profession1', mothername='$officialName2',motherprofetion='$profession2',firstName='$firstName',surname='$surname',officialName='$fullName',preferredName='$fullName',nameInCharacters='$fullName',username='$username',passwordStrong='$passwordStrong',passwordStrongSalt='$salt',passwordForceReset='Y',gibbonRoleIDPrimary='003',gibbonRoleIDAll='003',`boarder`='N',gender='$gender',email='$email',dob='$dob',category='$category',countryOfBirth='$countryOfBirth',lastSchool='$lastSchool',annual_income='$annual_income',languageFirst='$languageFirst',languageSecond='$languageSecond',dateStart='$dateStart',account_number='$account_number',admission_number='$admission_number',image_240='$attachment1',`religion`='$religion',`nationalIDCardNumber`='$nationalIDCardNumber',`gibbonSchoolYearIDEntry`=$gibbonSchoolYearID";
	$result=$connection2->prepare($sql);
	$result->execute();
	}
	catch(PDOException $e){
	echo $e;
	$fail_url.="&fail=1";
	$fail=1;
	}
	$gibbonPersonID=$connection2->lastInsertId();
	if($gibbonPersonID!=0){
	try{
	echo $sql="INSERT INTO `gibbonstudentenrolment`(`gibbonStudentEnrolmentID`, `gibbonPersonID`, `gibbonSchoolYearID`, `gibbonYearGroupID`, `gibbonRollGroupID`, `rollOrder`) VALUES(NULL,'$gibbonPersonID','$gibbonSchoolYearID','$filterClass','$filterSection','$rollOrder') ";
	$result=$connection2->prepare($sql);
	$result->execute();
	}
	catch(PDOException $e){
	echo $e;
	$fail_url="&fail=2";
	$fail=1;
	}
	$gibbonStudentEnrolmentID=$connection2->lastInsertId();
	try{
	$sql="INSERT INTO `lakshyasmsgroup`(`_id`, `gibbonPersonID`, `groupID`, `ref_id`) VALUES (NULL,'$gibbonPersonID','1','$gibbonStudentEnrolmentID')";
	$result=$connection2->prepare($sql);
	$result->execute();
	}
	catch(PDOException $e){
	echo $e;
	$fail_url.="&fail=2";
	$fail=1;
	}
	$familyName=current(explode(' ',$officialName2))." & ".$officialName1;
	try{
	echo $sql="INSERT INTO `gibbonfamily`(`gibbonFamilyID`, `name`, `homeAddress`, `homeAddressDistrict`, `homeAddressCountry` ) VALUES (NULL,'$familyName','$homeAddress','$homeAddressDistrict','$homeAddressCountry') ";
	$result=$connection2->prepare($sql);
	$result->execute();
	}
	catch(PDOException $e){
	$fail_url.="&fail=3";
	$fail=1;
	echo $e;
	}
	$gibbonFamilyID=$connection2->lastInsertId();
	if($gibbonFamilyID!=0){
	try{
	$sql="INSERT INTO `gibbonfamilychild`(`gibbonFamilyChildID`, `gibbonFamilyID`, `gibbonPersonID`, `comment`) VALUES (NULL,'$gibbonFamilyID','$gibbonPersonID','')";
	$result=$connection2->prepare($sql);
	$result->execute();
	}
	catch(PDOException $e){
	echo $e;
	$fail_url.="&fail=3";
	$fail=1;
	}
	$count=2;
	if($addGuardian=='on'){
		$count=3;
	}
	for($i=1;$i<=$count;$i++){
		try{
		echo $sql="INSERT INTO `gibbonfamilyadult` SET `gibbonFamilyID`='$gibbonFamilyID',`contactPriority`='${'contactPriority'.$i}' , `officialName`='${'officialName'.$i}' , `phone1Type`='${'phone1Type'.$i}' , `phone1CountryCode`='${'phone1CountryCode'.$i}' , `phone1`='${'phone1'.$i}' , `phone2Type`='${'phone2Type'.$i}' , `phone2CountryCode`='${'phone2CountryCode'.$i}' , `phone2`='${'phone2'.$i}' , `profession`='${'profession'.$i}',`email`='${'email'.$i}';";
		$result=$connection2->prepare($sql);
		$result->execute();
				if(${'contactPriority'.$i}==1){
			$sql="UPDATE `gibbonperson` SET `phone1`='${'phone1'.$i}' WHERE `gibbonPersonID`=$gibbonPersonID ";
			$result=$connection2->prepare($sql);
			$result->execute();
		}
		}

		catch(PDOException $e){
			echo $e;
			$fail_url.="&fail=3";
			$fail=1;
			
		}
		$gibbonFamilyAdultID=$connection2->lastInsertId();
		try{
			echo $relationship1;
		echo $sql="INSERT INTO `gibbonfamilyrelationship`(`gibbonFamilyRelationshipID`, `gibbonFamilyID`, `gibbonFamilyAdultID`, `gibbonPersonID`, `relationship`) VALUES (NULL,$gibbonFamilyID,$gibbonFamilyAdultID,$gibbonPersonID,'${'relationship'.$i}')";
		$result=$connection2->prepare($sql);
		$result->execute();
		}
		catch(PDOException $e){
			echo $e;
			$fail_url.="&fail=3";
			$fail=1;
		}
		try{
		echo $sql="INSERT INTO `gibbonpersonmedical`(`gibbonPersonMedicalID`, `gibbonPersonID`,`bloodType`) VALUES (NULL,'$gibbonPersonID','$bloodType')";
		$result=$connection2->prepare($sql);
		$result->execute();
		}
		catch(PDOException $e){
			echo $e;
			$fail_url.="&fail=3";
			$fail=1;
		}
	}
	}
	echo $dir="uploads/documents/" .$gibbonPersonID . "/";
	for($i=1;$i<6;$i++){
		if($_FILES["doc$i"]['error']==0){
			$note=$_POST["note".$i];
			$id=$_POST["overwriteDOC".$i];
			createDIR("../../".$dir);
			$attachment=$dir.$note;
			$attachment.=strrchr($_FILES["doc$i"]["name"], ".");
			fileUpload($_FILES["doc$i"],"../../".$attachment);
			
			if($id==0)
			$sql="INSERT INTO `studentdocuments`(`documentsID`, `gibbonPersonID`, `name`, `label`) VALUES (NULL,$gibbonPersonID,'$attachment','$note')";
			else
			$sql="UPDATE `studentdocuments` SET `name`='$attachment',`label`='$note' WHERE `documentsID`= $id";
			$result=$connection2->prepare($sql);
			$result->execute();
		}
	}
	}
		PopulateStudentPayableFee($gibbonPersonID,$gibbonSchoolYearID,$filterClass,$filterSection,$gibbonStudentEnrolmentID,$connection2,"new");
	
	echo $fail;
	if($fail==0){
		header('Location: '.$_SESSION[$guid]["absoluteURL"].'index.php?q=/modules/Students/new_student.php&success=0');
		
	}
	else{
		header('Location: '.$_SESSION[$guid]["absoluteURL"].'index.php?q=/modules/Students/new_student.php'.$fail_url);
	}
}

function dateformat($a){
	$b=explode('/',$a);
	return $b[2]."-".$b[1]."-".$b[0];
}

?>
