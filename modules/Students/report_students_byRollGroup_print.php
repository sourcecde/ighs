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

@session_start() ;

//Module includes
include "./modules/" . $_SESSION[$guid]["module"] . "/moduleFunctions.php" ;

if (isActionAccessible($guid, $connection2, "/modules/Students/report_students_byRollGroup_print.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
	$gibbonRollGroupID=$_GET["gibbonRollGroupID"] ;
	
	//Proceed!
	print "<h2 style='font-family:Arial, Helvetica, sans-serif; font-size:24px; color:#000000;'>" ;
	print _("Students by Section") ;
	print "</h2>" ;
	
	if ($gibbonRollGroupID!="") {
		if ($gibbonRollGroupID!="*") {
			try {
				$data=array("gibbonRollGroupID"=>$gibbonRollGroupID); 
				$sql="SELECT * FROM gibbonrollgroup WHERE gibbonRollGroupID=:gibbonRollGroupID" ;
				$result=$connection2->prepare($sql);
				$result->execute($data);
			}
			catch(PDOException $e) { 
				print "<div class='error'>" . $e->getMessage() . "</div>" ; 
			}
			
			if ($result->rowCount()==1) {
				$row=$result->fetch() ;
				print "<p style='font-family:Arial, Helvetica, sans-serif; font-size:24px; color:#000000; margin-bottom:0;'><b>" . _('Section') . "</b>: " . $row["name"] . "</p>" ;
				
				//Show Tutors
				try {
					$dataDetail=array("gibbonPersonIDTutor"=>$row["gibbonPersonIDTutor"], "gibbonPersonIDTutor2"=>$row["gibbonPersonIDTutor2"], "gibbonPersonIDTutor3"=>$row["gibbonPersonIDTutor3"]); 
					$sqlDetail="SELECT title, surname, preferredName FROM gibbonperson WHERE gibbonPersonID=:gibbonPersonIDTutor OR gibbonPersonID=:gibbonPersonIDTutor2 OR gibbonPersonID=:gibbonPersonIDTutor3" ;
					$resultDetail=$connection2->prepare($sqlDetail);
					$resultDetail->execute($dataDetail);
				}
				catch(PDOException $e) { 
					print "<div class='error'>" . $e->getMessage() . "</div>" ; 
				}
				
			}
		}
			
			
		try {
			if ($gibbonRollGroupID=="*") {
				$data=array("gibbonSchoolYearID"=>$_SESSION[$guid]["gibbonSchoolYearID"]); 
				//$sql="SELECT * FROM gibbonperson JOIN gibbonstudentenrolment ON (gibbonperson.gibbonPersonID=gibbonstudentenrolment.gibbonPersonID) JOIN gibbonrollgroup ON (gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID) WHERE status='Full' AND (dateStart IS NULL OR dateStart<='" . date("Y-m-d") . "') AND (dateEnd IS NULL  OR dateEnd>='" . date("Y-m-d") . "') AND gibbonstudentenrolment.gibbonSchoolYearID=:gibbonSchoolYearID ORDER BY gibbonrollgroup.nameShort, surname, preferredName" ;
				$sql="SELECT gibbonperson.*,gibbonstudentenrolment.gibbonStudentEnrolmentID,gibbonyeargroup.name AS class,gibbonrollgroup.name AS section,gibbonstudentenrolment.rollOrder AS roll_number FROM gibbonperson 
				JOIN gibbonstudentenrolment ON (gibbonperson.gibbonPersonID=gibbonstudentenrolment.gibbonPersonID) 
				JOIN gibbonrollgroup ON (gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID)
				LEFT JOIN gibbonyeargroup ON gibbonstudentenrolment.gibbonYearGroupId=gibbonyeargroup.gibbonYearGroupId 
				WHERE status='Full' AND (dateStart IS NULL OR dateStart<='" . date("Y-m-d") . "') AND (dateEnd IS NULL  OR dateEnd>='" . date("Y-m-d") . "') AND gibbonstudentenrolment.gibbonSchoolYearID=:gibbonSchoolYearID ORDER BY account_number ASC" ;
				
			}
			else {
				$data=array("gibbonRollGroupID"=>$gibbonRollGroupID); 
				//$sql="SELECT * FROM gibbonperson JOIN gibbonstudentenrolment ON (gibbonperson.gibbonPersonID=gibbonstudentenrolment.gibbonPersonID) JOIN gibbonrollgroup ON (gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID) WHERE status='Full' AND (dateStart IS NULL OR dateStart<='" . date("Y-m-d") . "') AND (dateEnd IS NULL  OR dateEnd>='" . date("Y-m-d") . "') AND gibbonstudentenrolment.gibbonRollGroupID=:gibbonRollGroupID ORDER BY surname, preferredName" ;
				$sql="SELECT gibbonperson.*,gibbonstudentenrolment.gibbonStudentEnrolmentID,gibbonyeargroup.name AS class,gibbonrollgroup.name AS section,gibbonstudentenrolment.rollOrder AS roll_number FROM gibbonperson 
				JOIN gibbonstudentenrolment ON (gibbonperson.gibbonPersonID=gibbonstudentenrolment.gibbonPersonID) 
				JOIN gibbonrollgroup ON (gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID) 
				LEFT JOIN gibbonyeargroup ON gibbonstudentenrolment.gibbonYearGroupId=gibbonyeargroup.gibbonYearGroupId  
				WHERE status='Full' AND (dateStart IS NULL OR dateStart<='" . date("Y-m-d") . "') AND (dateEnd IS NULL  OR dateEnd>='" . date("Y-m-d") . "') AND gibbonstudentenrolment.gibbonRollGroupID=:gibbonRollGroupID ORDER BY account_number ASC" ;
				
			}
			$result=$connection2->prepare($sql);
			$result->execute($data);
		}
		catch(PDOException $e) { 
			print "<div class='error'>" . $e->getMessage() . "</div>" ; 
		}
		
		print "<div class='linkTop'>" ;
		print "<a href='javascript:window.print()'>" .  _('Print') . "<img style='margin-left: 5px' title='" . _('Print') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/print.png'/></a>" ;
		print "</div>" ;
	
		print "<table class='mini' cellspacing='0' cellpadding='0' border='0' style='width: 100%'>" ;
			print "<tr class='head'>" ;
			print "<th style='font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000; border:1px solid #000000; padding:6px; text-align:center;'>" ;
					print _("Sl No") ;
				print "</th>" ;
				print "<th style='font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000; border:1px solid #000000; padding:6px; text-align:center;'>" ;
					print _("Acc No") ;
				print "</th>" ;
				/*
				print "<th style='font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000; border:1px solid #000000; padding:6px; text-align:center;'>" ;
					print _("Student ID") ;
				print "</th>" ;
				*/
				print "<th style='font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000; border:1px solid #000000; padding:6px; text-align:center;'>" ;
					print _("Student") ;
				print "</th>" ;
				print "<th style='font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000; border:1px solid #000000; padding:6px; text-align:center;'>" ;
					print _("Class") ;
				print "</th>" ;
				print "<th style='font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000; border:1px solid #000000; padding:6px; text-align:center;'>" ;
					print _("Sec") ;
				print "</th>" ;
				print "<th style='font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000; border:1px solid #000000; padding:6px; text-align:center;'>" ;
					print _("Roll No") ;
				print "</th>" ;
				print "<th style='font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000; border:1px solid #000000; padding:6px; text-align:center;'>" ;
					print _("Enrollment Date") ;
				print "</th>" ;
				print "<th style='font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000; border:1px solid #000000; padding:6px; text-align:center;'>" ;
					print _("Gender") ;
				print "</th>" ;
				print "<th style='font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000; border:1px solid #000000; padding:6px; text-align:center;'>" ;
					print _("Age") . "<br/>" ;
					print "<span style='font-style: italic; font-size: 85%'>" . _('DOB') . "</span>" ;
				print "</th>" ;
				/*
				print "<th>" ;
					print _("Nationality") ;
				print "</th>" ;
				print "<th>" ;
					print _("Transport") ;
				print "</th>" ;
				print "<th>" ;
					print _("House") ;
				print "</th>" ;
				print "<th>" ;
					print _("Locker") ;
				print "</th>" ;
				print "<th>" ;
					print _("Medical") ;
				print "</th>" ;
				*/
			print "</tr>" ;
			
			$count=0;
			$rowNum="odd" ;
			while ($row=$result->fetch()) {
				if ($count%2==0) {
					$rowNum="even" ;
				}
				else {
					$rowNum="odd" ;
				}
				$count++ ;
				
				//COLOR ROW BY STATUS!
				print "<tr class=$rowNum>" ;
				print "<td style='font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000; border:1px solid #000000; border-top:0; padding:6px; text-align:center;'>" ;
						print $count ;
					print "</td>" ;
					print "<td style='font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000; border:1px solid #000000; border-top:0; padding:6px; text-align:center;'>" ;
						print substr($row["account_number"],5) ;
					print "</td>" ;
					/*
					print "<td style='font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000; border:1px solid #000000; border-top:0; padding:6px;'>" ;
						print $row["gibbonStudentEnrolmentID"] ;
					print "</td>" ;
					*/
					print "<td style='font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000; border:1px solid #000000; border-top:0; padding:6px;'>" ;
						//print formatName("", $row["preferredName"], $row["surname"], "Student", true) ;
						print $row["officialName"];
					print "</td>" ;
					print "<td style='font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000; border:1px solid #000000; border-top:0; padding:6px; text-align:center;'>" ;
						print $row["class"];
					print "</td>" ;
					print "<td style='font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000; border:1px solid #000000; border-top:0; padding:6px; text-align:center;'>" ;
						print SectionFormater($row["section"]);
					print "</td>" ;
					print "<td style='font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000; border:1px solid #000000; border-top:0; padding:6px;'>" ;
						print $row["roll_number"];
					print "</td>" ;
					print "<td style='font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000; border:1px solid #000000; border-top:0; padding:6px; text-align:center;'>" ;
					if($row["enrollment_date"])
					{
						$enrolldatearr=explode("-", $row["enrollment_date"]);
						print $enrolldatearr[2].'/'.$enrolldatearr[1].'/'.$enrolldatearr[0] ;
					}
					print "</td>" ;
					print "<td style='font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000; border:1px solid #000000; border-top:0; padding:6px; text-align:center;'>" ;
						print $row["gender"] ;
					print "</td>" ;
					print "<td style='font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000; border:1px solid #000000; border-top:0; padding:6px;  text-align:center;'>" ;
						if (is_null($row["dob"])==FALSE AND $row["dob"]!="0000-00-00") {
							print getAge(dateConvertToTimestamp($row["dob"]), TRUE) . "<br/>" ;
							print "<span style='font-style: italic; font-size: 85%'>" . dateConvertBack($guid, $row["dob"]) . "</span>" ;
						}
					print "</td>" ;
					/*
					print "<td>" ;
						if ($row["citizenship1"]!="") {
							print $row["citizenship1"] . "<br/>" ;
						}
						if ($row["citizenship2"]!="") {
							print $row["citizenship2"] . "<br/>" ;
						}
					print "</td>" ;
					print "<td>" ;
						print $row["transport"] ;
					print "</td>" ;
					print "<td>" ;
						if ($row["gibbonHouseID"]!="") {
							try {
								$dataHouse=array("gibbonHouseID"=>$row["gibbonHouseID"]); 
								$sqlHouse="SELECT * FROM gibbonhouse WHERE gibbonHouseID=:gibbonHouseID" ;
								$resultHouse=$connection2->prepare($sqlHouse);
								$resultHouse->execute($dataHouse);
							}
							catch(PDOException $e) { 
								print "<div class='error'>" . $e->getMessage() . "</div>" ; 
							}
							if ($resultHouse->rowCount()==1) {
								$rowHouse=$resultHouse->fetch() ;
								print $rowHouse["name"] ;
							}
						}
					print "</td>" ;
					print "<td>" ;
						print $row["lockerNumber"] ;
					print "</td>" ;
					print "<td>" ;
						try {
							$dataForm=array("gibbonPersonID"=>$row["gibbonPersonID"]); 
							$sqlForm="SELECT * FROM gibbonpersonmedical WHERE gibbonPersonID=:gibbonPersonID" ;
							$resultForm=$connection2->prepare($sqlForm);
							$resultForm->execute($dataForm);
						}
						catch(PDOException $e) { 
							print "<div class='error'>" . $e->getMessage() . "</div>" ; 
						}
						
						if ($resultForm->rowCount()==1) {
							$rowForm=$resultForm->fetch() ;
							if ($rowForm["longTermMedication"]=='Y') {
								print "<b><i>" . _('Long Term Medication') . "</i></b>: " . $rowForm["longTermMedicationDetails"] . "<br/>" ;
							}
							$condCount=1 ;
							try {
								$dataConditions=array("gibbonPersonMedicalID"=>$rowForm["gibbonPersonMedicalID"]); 
								$sqlConditions="SELECT * FROM gibbonpersonmedicalcondition WHERE gibbonPersonMedicalID=:gibbonPersonMedicalID" ;
								$resultConditions=$connection2->prepare($sqlConditions);
								$resultConditions->execute($dataConditions);
							}
							catch(PDOException $e) { 
								print "<div class='error'>" . $e->getMessage() . "</div>" ; 
							}
	
							while ($rowConditions=$resultConditions->fetch()) {
								print "<b><i>" . _('Condition') . " $condCount</i></b> " ;
								print ": " . _($rowConditions["name"]) ;
								
								$alert=getAlert($connection2, $rowConditions["gibbonAlertLevelID"]) ;
								if ($alert!=FALSE) {
									print " <span style='color: #" . $alert["color"] . "; font-weight: bold'>(" . _($alert["name"]) . " " . _('Risk') . ")</span>" ;
									print "<br/>" ;									
									$condCount++ ;
								}
							}
						}
						else {
							print "<i>No medical data</i>" ;
						}
						
					print "</td>" ;
				print "</tr>" ;
				*/
			}
			if ($count==0) {
				print "<tr class=$rowNum>" ;
					print "<td colspan=2  style='font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000; border:1px solid #000000; border-top:0; padding:6px;'>" ;
						print _("There are no records to display.") ;
					print "</td>" ;
				print "</tr>" ;
			}
		print "</table>" ;
	}
}
?>