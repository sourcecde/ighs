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

//Set timezone from session variable
date_default_timezone_set($_SESSION[$guid]["timezone"]);

$gibbonApplicationFormID=$_POST["gibbonApplicationFormID"] ;
$gibbonSchoolYearID=$_POST["gibbonSchoolYearID"] ;
$search=$_GET["search"] ;
$URL=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_POST["address"]) . "/applicationForm_manage_edit.php&gibbonApplicationFormID=$gibbonApplicationFormID&gibbonSchoolYearID=$gibbonSchoolYearID&search=$search" ;

if (isActionAccessible($guid, $connection2, "/modules/User Admin/applicationForm_manage_edit.php")==FALSE) {
	//Fail 0
	$URL.="&updateReturn=fail0" ;
	header("Location: {$URL}");
}
else {
	//Proceed!
	//Check if school year specified
	
	if ($gibbonApplicationFormID=="" OR $gibbonSchoolYearID=="") {
		//Fail1
		$URL.="&updateReturn=fail1" ;
		header("Location: {$URL}");
	}
	else {
		try {
			$data=array("gibbonApplicationFormID"=>$gibbonApplicationFormID); 
			$sql="SELECT * FROM gibbonapplicationform WHERE gibbonApplicationFormID=:gibbonApplicationFormID" ;
			$result=$connection2->prepare($sql);
			$result->execute($data);
		}
		catch(PDOException $e) { 
			//Fail2
			$URL.="&updateReturn=fail2" ;
			header("Location: {$URL}");
			break ;
		}

		if ($result->rowCount()!=1) {
			//Fail 2
			$URL.="&updateReturn=fail2" ;
			header("Location: {$URL}");
		}
		else {
			//Proceed!
			//Get student fields
			$priority=$_POST["priority"] ;
			$status=$_POST["status"] ;
			$milestones="" ;
			$milestonesMaster=explode(",", getSettingByScope($connection2, "Application Form", "milestones")) ;
			foreach ($milestonesMaster as $milestoneMaster) {
				if (isset($_POST["milestone_" . preg_replace('/\s+/', '', $milestoneMaster)])) {
					if ($_POST["milestone_" . preg_replace('/\s+/', '', $milestoneMaster)]=="on") {
						$milestones.=trim($milestoneMaster) . "," ;
					}
				}
			}
			$milestones=substr($milestones,0,-1) ;
			$dateStart=NULL ;
			if ($_POST["dateStart"]!="") {
				$dateStart=dateConvert($guid, $_POST["dateStart"]) ;
			}
			$gibbonRollGroupID=NULL ;
			if ($_POST["gibbonRollGroupID"]!="") {
				$gibbonRollGroupID=$_POST["gibbonRollGroupID"] ;
			}
			$paymentMade="N" ;
			if (isset($_POST["paymentMade"])) {
				$paymentMade=$_POST["paymentMade"] ;
			}
			$notes=$_POST["notes"] ;
			$surname=$_POST["surname"] ;
			$firstName=$_POST["firstName"] ;
			$preferredName=$_POST["officialName"] ;
			//$preferredName=$_POST["preferredName"] ;
			//$officialName=$_POST["officialName"] ;
			$officialName=$_POST["officialName"];
			//$nameInCharacters=$_POST["nameInCharacters"] ;
			$nameInCharacters=$_POST["officialName"] ;
			$gender=$_POST["gender"] ;
			$dob=$_POST["dob"] ;
			if ($dob=="") {
				$dob=NULL ;
			}
			else {
				$dob=dateConvert($guid, $dob) ;
			}
			$languageHome=$_POST["languageHome"] ;
			$languageFirst=$_POST["languageFirst"] ;
			$languageSecond=$_POST["languageSecond"] ;
			$languageThird=$_POST["languageThird"] ;
			$countryOfBirth=$_POST["countryOfBirth"] ;
			$citizenship1=$_POST["citizenship1"] ;
			//$citizenship1Passport=$_POST["citizenship1Passport"] ;
			$citizenship1Passport='01';
			$nationalIDCardNumber=$_POST["nationalIDCardNumber"] ;
			//$residencyStatus=$_POST["residencyStatus"] ;
			$residencyStatus='Indian';
			//$visaExpiryDate=$_POST["visaExpiryDate"] ;
			$visaExpiryDate='2020-01-01';
			if ($visaExpiryDate=="") {
				$visaExpiryDate=NULL ;
			}
			else {
				$visaExpiryDate=dateConvert($guid, $visaExpiryDate) ;
			}
			$email=$_POST["email"] ;
			$phone1Type=$_POST["phone1Type"] ; 
			$phone1CountryCode=$_POST["phone1CountryCode"] ; 
			$phone1=preg_replace('/[^0-9+]/', '', $_POST["phone1"]) ; 
			$phone2Type=$_POST["phone2Type"] ; 
			$phone2CountryCode=$_POST["phone2CountryCode"] ; 
			$phone2=preg_replace('/[^0-9+]/', '', $_POST["phone2"]) ; 
			//$medicalInformation=$_POST["medicalInformation"] ;
			$medicalInformation='Meidcal Info';
			//$developmentInformation=$_POST["developmentInformation"] ;
			$developmentInformation='Development info';
			$gibbonSchoolYearIDEntry=$_POST["gibbonSchoolYearIDEntry"] ;
			$gibbonYearGroupIDEntry=$_POST["gibbonYearGroupIDEntry"] ;
			$dayType=NULL ;
			if (isset($_POST["dayType"])) {
				$dayType=$_POST["dayType"] ;
			}
			$schoolName1=$_POST["schoolName1"] ;
			$schoolAddress1=$_POST["schoolAddress1"] ;
			$schoolGrades1=$_POST["schoolGrades1"] ;
			$schoolGrades1=$_POST["schoolGrades1"] ;
			$schoolDate1=$_POST["schoolDate1"] ;
			if ($schoolDate1=="") {
				$schoolDate1=NULL ;
			}
			else {
				$schoolDate1=dateConvert($guid, $schoolDate1);
			}
			$schoolName2=$_POST["schoolName2"] ;
			$schoolAddress2=$_POST["schoolAddress2"] ;
			$schoolGrades2=$_POST["schoolGrades2"] ;
			$schoolGrades2=$_POST["schoolGrades2"] ;
			$schoolDate2=$_POST["schoolDate2"] ;
			if ($schoolDate2=="") {
				$schoolDate2=NULL ;
			}
			else {
				$schoolDate2=dateConvert($guid, $schoolDate2) ;
			}
			
			//GET FAMILY FEILDS
			$gibbonfamily=$_POST["gibbonfamily"] ;
			if ($gibbonfamily=="TRUE") {
				$gibbonFamilyID=$_POST["gibbonFamilyID"] ;
			}
			else {
				$gibbonFamilyID=NULL ;
			}
			$homeAddress=NULL ;
			if (isset($_POST["homeAddress"])) {
				$homeAddress=$_POST["homeAddress"] ;
			}
			$homeAddressDistrict=NULL ;
			if (isset($_POST["homeAddressDistrict"])) {
				$homeAddressDistrict=$_POST["homeAddressDistrict"] ;
			}
			$homeAddressCountry=NULL ;
			if (isset($_POST["homeAddressCountry"])) {
				$homeAddressCountry=$_POST["homeAddressCountry"] ;
			}
			
			
			//GET PARENT1 FEILDS
			$parent1gibbonPersonID=NULL ;
			if (isset($_POST["parent1gibbonPersonID"])) {
				$parent1gibbonPersonID=$_POST["parent1gibbonPersonID"] ;
			}
			$parent1title=NULL ;
			if (isset($_POST["parent1title"])) {
				$parent1title=$_POST["parent1title"] ;
			}
			$parent1surname=NULL ;
			if (isset($_POST["parent1surname"])) {
				$parent1surname=$_POST["parent1surname"] ;
			}
			$parent1firstName=NULL ;
			if (isset($_POST["parent1firstName"])) {
				$parent1firstName=$_POST["parent1firstName"] ;
			}
			$parent1preferredName=$parent1firstName ;
			if (isset($_POST["parent1preferredName"])) {
				$parent1preferredName=$_POST["parent1preferredName"] ;
			}
			$parent1officialName=$parent1firstName." ".$parent1surname;
			if (isset($_POST["parent1officialName"])) {
				$parent1officialName=$_POST["parent1officialName"] ;
			}
			$parent1nameInCharacters=$parent1firstName ;
			if (isset($_POST["parent1nameInCharacters"])) {
				$parent1nameInCharacters=$_POST["parent1nameInCharacters"] ;
			}
			$parent1gender=NULL ;
			if (isset($_POST["parent1gender"])) {
				$parent1gender=$_POST["parent1gender"] ;
			}
			$parent1relationship=NULL ;
			if (isset($_POST["parent1relationship"])) {
				$parent1relationship=$_POST["parent1relationship"] ;
			}
			$parent1languageFirst=NULL ;
			if (isset($_POST["parent1languageFirst"])) {
				$parent1languageFirst=$_POST["parent1languageFirst"] ;
			}
			$parent1languageSecond=NULL ;
			if (isset($_POST["parent1languageSecond"])) {
				$parent1languageSecond=$_POST["parent1languageSecond"] ;
			}
			$parent1citizenship1='India' ;
			if (isset($_POST["parent1citizenship1"])) {
				$parent1citizenship1=$_POST["parent1citizenship1"] ;
			}
			$parent1nationalIDCardNumber=NULL ;
			if (isset($_POST["parent1nationalIDCardNumber"])) {
				$parent1nationalIDCardNumber=$_POST["parent1nationalIDCardNumber"] ;
			}
			$parent1residencyStatus='Inidan' ;
			if (isset($_POST["parent1residencyStatus"])) {
				$parent1residencyStatus=$_POST["parent1residencyStatus"] ;
			}
			$parent1visaExpiryDate='2020-01-01' ;
			if (isset($_POST["parent1visaExpiryDate"])) {
				if ($_POST["parent1visaExpiryDate"]!="") {
					$parent1visaExpiryDate=dateConvert($guid, $_POST["parent1visaExpiryDate"]) ;
				}
			}
			$parent1email=NULL ;
			if (isset($_POST["parent1email"])) {
				$parent1email=$_POST["parent1email"] ;
			}
			$parent1phone1Type=NULL ;
			if (isset($_POST["parent1phone1Type"])) {
				$parent1phone1Type=$_POST["parent1phone1Type"] ;
			}
			if (isset($_POST["parent1phone1"]) AND $parent1phone1Type=="") {
				$parent1phone1Type="Other" ;
			} 
			$parent1phone1CountryCode=NULL ;
			if (isset($_POST["parent1phone1CountryCode"])) {
				$parent1phone1CountryCode=$_POST["parent1phone1CountryCode"] ;
			}
			$parent1phone1=NULL ;
			if (isset($_POST["parent1phone1"])) {
				$parent1phone1=$_POST["parent1phone1"] ;
			}
			$parent1phone2Type=NULL ;
			if (isset($_POST["parent1phone2Type"])) {
				$parent1phone2Type=$_POST["parent1phone2Type"] ;
			}
			if (isset($_POST["parent1phone2"]) AND $parent1phone2Type=="") {
				$parent1phone2Type="Other" ;
			} 
			$parent1phone2CountryCode=NULL ;
			if (isset($_POST["parent1phone2CountryCode"])) {
				$parent1phone2CountryCode=$_POST["parent1phone2CountryCode"] ;
			}
			$parent1phone2=NULL ;
			if (isset($_POST["parent1phone2"])) {
				$parent1phone2=$_POST["parent1phone2"] ;
			}
			$parent1profession=NULL ;
			if (isset($_POST["parent1profession"])) {
				$parent1profession=$_POST["parent1profession"] ;
			}
			$parent1employer=NULL ;
			if (isset($_POST["parent1employer"])) {
				$parent1employer=$_POST["parent1employer"] ;
			}
		
		
			//GET PARENT2 FEILDS
			$parent2title=NULL ;
			if (isset($_POST["parent2title"])) {
				$parent2title=$_POST["parent2title"] ;
			}
			$parent2surname=NULL ;
			if (isset($_POST["parent2surname"])) {
				$parent2surname=$_POST["parent2surname"] ;
			}
			$parent2firstName=NULL ;
			if (isset($_POST["parent2firstName"])) {
				$parent2firstName=$_POST["parent2firstName"] ;
			}
			$parent2preferredName=$parent2firstName ;
			if (isset($_POST["parent2preferredName"])) {
				$parent2preferredName=$_POST["parent2preferredName"] ;
			}
			$parent2officialName=$parent2firstName." ".$parent2firstName ;
			if (isset($_POST["parent2officialName"])) {
				$parent2officialName=$_POST["parent2officialName"] ;
			}
			$parent2nameInCharacters=$parent2firstName ;
			if (isset($_POST["parent2nameInCharacters"])) {
				$parent2nameInCharacters=$_POST["parent2nameInCharacters"] ;
			}
			$parent2gender=NULL ;
			if (isset($_POST["parent2gender"])) {
				$parent2gender=$_POST["parent2gender"] ;
			}
			$parent2relationship=NULL ;
			if (isset($_POST["parent2relationship"])) {
				$parent2relationship=$_POST["parent2relationship"] ;
			}
			$parent2languageFirst=NULL ;
			if (isset($_POST["parent2languageFirst"])) {
				$parent2languageFirst=$_POST["parent2languageFirst"] ;
			}
			$parent2languageSecond=NULL ;
			if (isset($_POST["parent2languageSecond"])) {
				$parent2languageSecond=$_POST["parent2languageSecond"] ;
			}
			$parent2citizenship1='Inida' ;
			if (isset($_POST["parent2citizenship1"])) {
				$parent2citizenship1=$_POST["parent2citizenship1"] ;
			}
			$parent2nationalIDCardNumber=NULL ;
			if (isset($_POST["parent2nationalIDCardNumber"])) {
				$parent2nationalIDCardNumber=$_POST["parent2nationalIDCardNumber"] ;
			}
			$parent2residencyStatus='Indian' ;
			if (isset($_POST["parent2residencyStatus"])) {
				$parent2residencyStatus=$_POST["parent2residencyStatus"] ;
			}
			$parent2visaExpiryDate='2020-01-01' ;
			if (isset($_POST["parent2visaExpiryDate"])) {
				if ($_POST["parent2visaExpiryDate"]!="") {
					$parent2visaExpiryDate=dateConvert($guid, $_POST["parent2visaExpiryDate"]) ;
				}
			}
			$parent2email=NULL ;
			if (isset($_POST["parent2email"])) {
				$parent2email=$_POST["parent2email"] ;
			}
			$parent2phone1Type=NULL ;
			if (isset($_POST["parent2phone1Type"])) {
				$parent2phone1Type=$_POST["parent2phone1Type"] ;
			}
			if (isset($_POST["parent2phone1"]) AND $parent2phone1Type=="") {
				$parent2phone1Type="Other" ;
			} 
			$parent2phone1CountryCode=NULL ;
			if (isset($_POST["parent2phone1CountryCode"])) {
				$parent2phone1CountryCode=$_POST["parent2phone1CountryCode"] ;
			}
			$parent2phone1=NULL ;
			if (isset($_POST["parent2phone1"])) {
				$parent2phone1=$_POST["parent2phone1"] ;
			}
			$parent2phone2Type=NULL ;
			if (isset($_POST["parent2phone2Type"])) {
				$parent2phone2Type=$_POST["parent2phone2Type"] ;
			}
			if (isset($_POST["parent2phone2"]) AND $parent2phone2Type=="") {
				$parent2phone2Type="Other" ;
			} 
			$parent2phone2CountryCode=NULL ;
			if (isset($_POST["parent2phone2CountryCode"])) {
				$parent2phone2CountryCode=$_POST["parent2phone2CountryCode"] ;
			}
			$parent2phone2=NULL ;
			if (isset($_POST["parent2phone2"])) {
				$parent2phone2=$_POST["parent2phone2"] ;
			}
			$parent2profession=NULL ;
			if (isset($_POST["parent2profession"])) {
				$parent2profession=$_POST["parent2profession"] ;
			}
			$parent2employer=NULL ;
			if (isset($_POST["parent2employer"])) {
				$parent2employer=$_POST["parent2employer"] ;
			}
			
			//GET SIBLING FIELDS
			$siblingName1=$_POST["siblingName1"] ;
			$siblingDOB1=$_POST["siblingDOB1"] ;
			if ($siblingDOB1=="") {
				$siblingDOB1=NULL ;
			}
			else {
				$siblingDOB1=dateConvert($guid, $siblingDOB1);
			}
			$siblingSchool1=$_POST["siblingSchool1"] ;
			$siblingSchoolJoiningDate1=$_POST["siblingSchoolJoiningDate1"] ;
			if ($siblingSchoolJoiningDate1=="") {
				$siblingSchoolJoiningDate1=NULL ;
			}
			else {
				$siblingSchoolJoiningDate1=dateConvert($guid, $siblingSchoolJoiningDate1) ;
			}
			$siblingName2=$_POST["siblingName2"] ;
			$siblingDOB2=$_POST["siblingDOB2"] ;
			if ($siblingDOB2=="") {
				$siblingDOB2=NULL ;
			}
			else {
				$siblingDOB2=dateConvert($guid, $siblingDOB2);
			}
			$siblingSchool2=$_POST["siblingSchool2"] ;
			$siblingSchoolJoiningDate2=$_POST["siblingSchoolJoiningDate2"] ;
			if ($siblingSchoolJoiningDate2=="") {
				$siblingSchoolJoiningDate2=NULL ;
			}
			else {
				$siblingSchoolJoiningDate2=dateConvert($guid, $siblingSchoolJoiningDate2) ;
			}
			$siblingName3=$_POST["siblingName3"] ;
			$siblingDOB3=$_POST["siblingDOB3"] ;
			if ($siblingDOB3=="") {
				$siblingDOB3=NULL ;
			}
			else {
				$siblingDOB3=dateConvert($guid, $siblingDOB3) ;
			}
			$siblingSchool3=$_POST["siblingSchool3"] ;
			$siblingSchoolJoiningDate3=$_POST["siblingSchoolJoiningDate3"] ;
			if ($siblingSchoolJoiningDate3=="") {
				$siblingSchoolJoiningDate3=NULL ;
			}
			else {
				$siblingSchoolJoiningDate3=dateConvert($guid, $siblingSchoolJoiningDate3) ;
			}
			
			//GET PAYMENT FIELDS
			//$payment=$_POST["payment"] ;
			$payment='Family';
			$companyName=NULL ;
			if (isset($_POST["companyName"])) {
				$companyName=$_POST["companyName"] ;
			}
			$companyContact=NULL ;
			if (isset($_POST["companyContact"])) {
				$companyContact=$_POST["companyContact"] ;
			}
			$companyAddress=NULL ;
			if (isset($_POST["companyAddress"])) {
				$companyAddress=$_POST["companyAddress"] ;
			}
			$companyEmail=NULL ;
			if (isset($_POST["companyEmail"])) {
				$companyEmail=$_POST["companyEmail"] ;
			}
			$companyCCFamily=NULL ;
			if (isset($_POST["companyCCFamily"])) {
				$companyCCFamily=$_POST["companyCCFamily"] ;
			}
			$companyPhone=NULL ;
			if (isset($_POST["companyPhone"])) {
				$companyPhone=$_POST["companyPhone"] ;
			}
			$companyAll=NULL ;
			if (isset($_POST["companyAll"])) {
				$companyAll=$_POST["companyAll"] ;
			}
			$gibbonFinanceFeeCategoryIDList=NULL ;
			if (isset($_POST["gibbonFinanceFeeCategoryIDList"])) {
				$gibbonFinanceFeeCategoryIDArray=$_POST["gibbonFinanceFeeCategoryIDList"] ;
				if (count($gibbonFinanceFeeCategoryIDArray)>0) {
					foreach ($gibbonFinanceFeeCategoryIDArray AS $gibbonFinanceFeeCategoryID) {
						$gibbonFinanceFeeCategoryIDList.=$gibbonFinanceFeeCategoryID . "," ;
					}
					$gibbonFinanceFeeCategoryIDList=substr($gibbonFinanceFeeCategoryIDList,0,-1) ;
				}
			}

		
			//GET OTHER FIELDS
			$languageChoice=NULL ;
			if (isset($_POST["languageChoice"])) {
				$languageChoice=$_POST["languageChoice"] ;
			}
			$languageChoiceExperience=NULL ;
			if (isset($_POST["languageChoiceExperience"])) {
				$languageChoiceExperience=$_POST["languageChoiceExperience"] ;
			}
			$scholarshipInterest=NULL ;
			if (isset($_POST["scholarshipInterest"])) {
				$scholarshipInterest=$_POST["scholarshipInterest"] ;
			}
			$scholarshipRequired=NULL ;
			if (isset($_POST["scholarshipRequired"])) {
				$scholarshipRequired=$_POST["scholarshipRequired"] ;
			}
			$howDidYouHear=NULL ;
			if (isset($_POST["howDidYouHear"])) {
				$howDidYouHear=$_POST["howDidYouHear"] ;
			}
			$howDidYouHearMore=NULL ;
			if (isset($_POST["howDidYouHearMore"])) {
				$howDidYouHearMore=$_POST["howDidYouHearMore"] ;
			}
			$privacy=NULL ;
			if (isset($_POST["privacyOptions"])) {
				$privacyOptions=$_POST["privacyOptions"] ;
				foreach ($privacyOptions AS $privacyOption) {
					if ($privacyOption!="") {
						$privacy.=$privacyOption . ", " ;
					}
				}
				if ($privacy!="") {
					$privacy=substr($privacy,0,-2) ;
				}
				else {
					$privacy=NULL ;
				}
			}
			
			//VALIDATE INPUTS
			$familyFail=FALSE ;
			if ($gibbonfamily=="TRUE") {
				if ($gibbonFamilyID=="") {
					$familyFail=TRUE ;
				}
			}
			else {
				if ($homeAddress=="" OR $homeAddressDistrict=="" OR $homeAddressCountry=="") {
					$familyFail=TRUE ;
				}
				
				if ($parent1gibbonPersonID==NULL) {
					if ($parent1title=="" OR $parent1surname=="" OR $parent1firstName=="" OR $parent1preferredName=="" OR $parent1officialName=="" OR $parent1gender=="" OR $parent1relationship=="" OR $parent1phone1==""OR $parent1profession=="") {
						$familyFail=TRUE ;
					}
				}
			}
			/*
			if ($priority=="" OR $surname=="" OR $firstName=="" OR $preferredName=="" OR $officialName=="" OR $gender=="" OR $dob=="" OR $languageHome=="" OR $languageFirst=="" OR $gibbonSchoolYearIDEntry=="" OR $dateStart=="" OR $gibbonYearGroupIDEntry=="" OR $howDidYouHear=="" OR $familyFail) {
				//Fail 3
				$URL.="&addReturn=fail3" ;
				header("Location: {$URL}");
			}
			*/
		if ($priority=="" OR $surname=="" OR $firstName=="" OR $preferredName=="" OR $officialName=="" OR $gender=="" OR $dob=="" OR $languageHome=="" OR $languageFirst=="" OR $gibbonSchoolYearIDEntry=="" OR $dateStart=="" OR $gibbonYearGroupIDEntry=="" OR   $familyFail) {
				//Fail 3
				
				$URL.="&addReturn=fail3" ;
				header("Location: {$URL}");
			}
			else {
				//Write to database
				try {
					$data=array("priority"=>$priority,  "milestones"=>$milestones,  "gibbonRollGroupID"=>$gibbonRollGroupID, "paymentMade"=>$paymentMade, "notes"=>$notes, "surname"=>$surname, "firstName"=>$firstName, "preferredName"=>$preferredName, "officialName"=>$officialName, "nameInCharacters"=>$nameInCharacters, "gender"=>$gender, "dob"=>$dob, "languageHome"=>$languageHome, "languageFirst"=>$languageFirst, "languageSecond"=>$languageSecond, "languageThird"=>$languageThird, "countryOfBirth"=>$countryOfBirth, "citizenship1"=>$citizenship1, "citizenship1Passport"=>$citizenship1Passport, "nationalIDCardNumber"=>$nationalIDCardNumber, "residencyStatus"=>$residencyStatus, "visaExpiryDate"=>$visaExpiryDate, "email"=>$email, "homeAddress"=>$homeAddress, "homeAddressDistrict"=>$homeAddressDistrict, "homeAddressCountry"=>$homeAddressCountry, "phone1Type"=>$phone1Type, "phone1CountryCode"=>$phone1CountryCode, "phone1"=>$phone1, "phone2Type"=>$phone2Type, "phone2CountryCode"=>$phone2CountryCode, "phone2"=>$phone2, "medicalInformation"=>$medicalInformation, "developmentInformation"=>$developmentInformation, "gibbonSchoolYearIDEntry"=>$gibbonSchoolYearIDEntry, "gibbonYearGroupIDEntry"=>$gibbonYearGroupIDEntry, "dayType"=>$dayType, "schoolName1"=>$schoolName1, "schoolAddress1"=>$schoolAddress1, "schoolGrades1"=>$schoolGrades1, "schoolDate1"=>$schoolDate1, "schoolName2"=>$schoolName2, "schoolAddress2"=>$schoolAddress2, "schoolGrades2"=>$schoolGrades2, "schoolDate2"=>$schoolDate2, "gibbonFamilyID"=>$gibbonFamilyID, "parent1gibbonPersonID"=>$parent1gibbonPersonID, "parent1title"=>$parent1title, "parent1surname"=>$parent1surname, "parent1firstName"=>$parent1firstName, "parent1preferredName"=>$parent1preferredName, "parent1officialName"=>$parent1officialName, "parent1nameInCharacters"=>$parent1nameInCharacters, "parent1gender"=>$parent1gender, "parent1relationship"=>$parent1relationship, "parent1languageFirst"=>$parent1languageFirst, "parent1languageSecond"=>$parent1languageSecond, "parent1citizenship1"=>$parent1citizenship1, "parent1nationalIDCardNumber"=>$parent1nationalIDCardNumber, "parent1residencyStatus"=>$parent1residencyStatus, "parent1visaExpiryDate"=>$parent1visaExpiryDate, "parent1email"=>$parent1email, "parent1phone1Type"=>$parent1phone1Type, "parent1phone1CountryCode"=>$parent1phone1CountryCode, "parent1phone1"=>$parent1phone1, "parent1phone2Type"=>$parent1phone2Type, "parent1phone2CountryCode"=>$parent1phone2CountryCode, "parent1phone2"=>$parent1phone2, "parent1profession"=>$parent1profession, "parent1employer"=>$parent1employer, "parent2title"=>$parent2title, "parent2surname"=>$parent2surname, "parent2firstName"=>$parent2firstName, "parent2preferredName"=>$parent2preferredName, "parent2officialName"=>$parent2officialName, "parent2nameInCharacters"=>$parent2nameInCharacters, "parent2gender"=>$parent2gender, "parent2relationship"=>$parent2relationship, "parent2languageFirst"=>$parent2languageFirst, "parent2languageSecond"=>$parent2languageSecond, "parent2citizenship1"=>$parent2citizenship1, "parent2nationalIDCardNumber"=>$parent2nationalIDCardNumber, "parent2residencyStatus"=>$parent2residencyStatus, "parent2visaExpiryDate"=>$parent2visaExpiryDate, "parent2email"=>$parent2email, "parent2phone1Type"=>$parent2phone1Type, "parent2phone1CountryCode"=>$parent2phone1CountryCode, "parent2phone1"=>$parent2phone1, "parent2phone2Type"=>$parent2phone2Type, "parent2phone2CountryCode"=>$parent2phone2CountryCode, "parent2phone2"=>$parent2phone2, "parent2profession"=>$parent2profession, "parent2employer"=>$parent2employer, "siblingName1"=>$siblingName1, "siblingDOB1"=>$siblingDOB1, "siblingSchool1"=>$siblingSchool1, "siblingSchoolJoiningDate1"=>$siblingSchoolJoiningDate1, "siblingName2"=>$siblingName2, "siblingDOB2"=>$siblingDOB2, "siblingSchool2"=>$siblingSchool2, "siblingSchoolJoiningDate2"=>$siblingSchoolJoiningDate2, "siblingName3"=>$siblingName3, "siblingDOB3"=>$siblingDOB3, "siblingSchool3"=>$siblingSchool3, "siblingSchoolJoiningDate3"=>$siblingSchoolJoiningDate3, "languageChoice"=>$languageChoice, "languageChoiceExperience"=>$languageChoiceExperience, "scholarshipInterest"=>$scholarshipInterest, "scholarshipRequired"=>$scholarshipRequired, "payment"=>$payment, "companyName"=>$companyName, "companyContact"=>$companyContact, "companyAddress"=>$companyAddress, "companyEmail"=>$companyEmail, "companyCCFamily"=>$companyCCFamily, "companyPhone"=>$companyPhone, "companyAll"=>$companyAll, "gibbonFinanceFeeCategoryIDList"=>$gibbonFinanceFeeCategoryIDList, "howDidYouHear"=>$howDidYouHear, "howDidYouHearMore"=>$howDidYouHearMore, "privacy"=>$privacy, "gibbonApplicationFormID"=>$gibbonApplicationFormID); 
					$sql="UPDATE gibbonapplicationform SET priority=:priority,  milestones=:milestones, gibbonRollGroupID=:gibbonRollGroupID, paymentMade=:paymentMade, notes=:notes, surname=:surname, firstName=:firstName, preferredName=:preferredName, officialName=:officialName, nameInCharacters=:nameInCharacters, gender=:gender, dob=:dob, languageHome=:languageHome, languageFirst=:languageFirst, languageSecond=:languageSecond, languageThird=:languageThird, countryOfBirth=:countryOfBirth, citizenship1=:citizenship1, citizenship1Passport=:citizenship1Passport, nationalIDCardNumber=:nationalIDCardNumber, residencyStatus=:residencyStatus, visaExpiryDate=:visaExpiryDate, email=:email, homeAddress=:homeAddress, homeAddressDistrict=:homeAddressDistrict, homeAddressCountry=:homeAddressCountry, phone1Type=:phone1Type, phone1CountryCode=:phone1CountryCode, phone1=:phone1, phone2Type=:phone2Type, phone2CountryCode=:phone2CountryCode, phone2=:phone2, medicalInformation=:medicalInformation, developmentInformation=:developmentInformation, gibbonSchoolYearIDEntry=:gibbonSchoolYearIDEntry, gibbonYearGroupIDEntry=:gibbonYearGroupIDEntry, dayType=:dayType, schoolName1=:schoolName1, schoolAddress1=:schoolAddress1, schoolGrades1=:schoolGrades1, schoolDate1=:schoolDate1, schoolName2=:schoolName2, schoolAddress2=:schoolAddress2, schoolGrades2=:schoolGrades2, schoolDate2=:schoolDate2, gibbonFamilyID=:gibbonFamilyID, parent1gibbonPersonID=:parent1gibbonPersonID, parent1title=:parent1title, parent1surname=:parent1surname, parent1firstName=:parent1firstName, parent1preferredName=:parent1preferredName, parent1officialName=:parent1officialName, parent1nameInCharacters=:parent1nameInCharacters, parent1gender=:parent1gender, parent1relationship=:parent1relationship, parent1languageFirst=:parent1languageFirst, parent1languageSecond=:parent1languageSecond, parent1citizenship1=:parent1citizenship1, parent1nationalIDCardNumber=:parent1nationalIDCardNumber, parent1residencyStatus=:parent1residencyStatus, parent1visaExpiryDate=:parent1visaExpiryDate, parent1email=:parent1email, parent1phone1Type=:parent1phone1Type, parent1phone1CountryCode=:parent1phone1CountryCode, parent1phone1=:parent1phone1, parent1phone2Type=:parent1phone2Type, parent1phone2CountryCode=:parent1phone2CountryCode, parent1phone2=:parent1phone2, parent1profession=:parent1profession, parent1employer=:parent1employer, parent2title=:parent2title, parent2surname=:parent2surname, parent2firstName=:parent2firstName, parent2preferredName=:parent2preferredName, parent2officialName=:parent2officialName, parent2nameInCharacters=:parent2nameInCharacters, parent2gender=:parent2gender, parent2relationship=:parent2relationship, parent2languageFirst=:parent2languageFirst, parent2languageSecond=:parent2languageSecond, parent2citizenship1=:parent2citizenship1, parent2nationalIDCardNumber=:parent2nationalIDCardNumber, parent2residencyStatus=:parent2residencyStatus, parent2visaExpiryDate=:parent2visaExpiryDate, parent2email=:parent2email, parent2phone1Type=:parent2phone1Type, parent2phone1CountryCode=:parent2phone1CountryCode, parent2phone1=:parent2phone1, parent2phone2Type=:parent2phone2Type, parent2phone2CountryCode=:parent2phone2CountryCode, parent2phone2=:parent2phone2, parent2profession=:parent2profession, parent2employer=:parent2employer, siblingName1=:siblingName1, siblingDOB1=:siblingDOB1, siblingSchool1=:siblingSchool1, siblingSchoolJoiningDate1=:siblingSchoolJoiningDate1, siblingName2=:siblingName2, siblingDOB2=:siblingDOB2, siblingSchool2=:siblingSchool2, siblingSchoolJoiningDate2=:siblingSchoolJoiningDate2, siblingName3=:siblingName3, siblingDOB3=:siblingDOB3, siblingSchool3=:siblingSchool3, siblingSchoolJoiningDate3=:siblingSchoolJoiningDate3, languageChoice=:languageChoice, languageChoiceExperience=:languageChoiceExperience, scholarshipInterest=:scholarshipInterest, scholarshipRequired=:scholarshipRequired, payment=:payment, companyName=:companyName, companyContact=:companyContact, companyAddress=:companyAddress, companyEmail=:companyEmail, companyCCFamily=:companyCCFamily, companyPhone=:companyPhone, companyAll=:companyAll, gibbonFinanceFeeCategoryIDList=:gibbonFinanceFeeCategoryIDList, howDidYouHear=:howDidYouHear, howDidYouHearMore=:howDidYouHearMore, privacy=:privacy WHERE gibbonApplicationFormID=:gibbonApplicationFormID" ;
					//echo $sql;
					$result=$connection2->prepare($sql);
					$result->execute($data);
					//$connection2->debugDumpParams();
				}
				catch(PDOException $e) { 
					echo $e;
					//Fail 2
					$URL.="&updateReturn=fail2" ;
					header("Location: {$URL}");
					break ;
				}
				
				//Deal with required documents
				$requiredDocuments=getSettingByScope($connection2, "Application Form", "requiredDocuments") ;
				if ($requiredDocuments!="" AND $requiredDocuments!=FALSE) {
					$fileCount=0 ;
					if (isset($_POST["fileCount"])) {
						$fileCount=$_POST["fileCount"] ;
					}
					for ($i=0; $i<$fileCount; $i++) {
						$fileName=$_POST["fileName$i"] ;
						$time=time() ;
						//Move attached file, if there is one
						if ($_FILES["file$i"]["tmp_name"]!="") {
							//Check for folder in uploads based on today's date
							$path=$_SESSION[$guid]["absolutePath"] ;
							if (is_dir($path ."/uploads/" . date("Y", $time) . "/" . date("m", $time))==FALSE) {
								mkdir($path ."/uploads/" . date("Y", $time) . "/" . date("m", $time), 0777, TRUE) ;
							}
							$unique=FALSE;
							$count=0 ;
							while ($unique==FALSE AND $count<100) {
								$suffix=randomPassword(16) ;
								$attachment="uploads/" . date("Y", $time) . "/" . date("m", $time) . "/Application Document_$suffix" . strrchr($_FILES["file$i"]["name"], ".") ;
								if (!(file_exists($path . "/" . $attachment))) {
									$unique=TRUE ;
								}
								$count++ ;
							}
							if (!(move_uploaded_file($_FILES["file$i"]["tmp_name"],$path . "/" . $attachment))) {
							}
						
							//Write files to database
							try {
								$dataFile=array("gibbonApplicationFormID"=>$gibbonApplicationFormID, "name"=>$fileName, "path"=>$attachment); 
								$sqlFile="INSERT INTO gibbonapplicationformfile SET gibbonApplicationFormID=:gibbonApplicationFormID, name=:name, path=:path" ;
								$resultFile=$connection2->prepare($sqlFile);
								$resultFile->execute($dataFile);
							}
							catch(PDOException $e) { }
						}
					}
				}
			
				
				//Success 0
				$URL.="&updateReturn=success0" ;
				header("Location: {$URL}");
			}
		}
	}
}
?>