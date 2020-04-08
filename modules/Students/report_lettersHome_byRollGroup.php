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

if (isActionAccessible($guid, $connection2, "/modules/Students/report_students_new")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
	//Proceed!
	print "<div class='trail'>" ;
	print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . _("Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . _(getModuleName($_GET["q"])) . "</a> > </div><div class='trailEnd'>" . _('Letters Home by Roll Group') . "</div>" ;
	print "</div>" ;
	
	try {
		$data=array("gibbonSchoolYearID"=>$_SESSION[$guid]["gibbonSchoolYearID"]); 
		$sql="SELECT gibbonperson.gibbonPersonID, surname, preferredName, gibbonrollgroup.nameShort AS rollGroup, gibbonfamily.gibbonFamilyID FROM gibbonperson JOIN gibbonstudentenrolment ON (gibbonstudentenrolment.gibbonPersonID=gibbonperson.gibbonPersonID) JOIN gibbonrollgroup ON (gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID) LEFT JOIN gibbonfamilychild ON (gibbonfamilychild.gibbonPersonID=gibbonperson.gibbonPersonID) LEFT JOIN gibbonfamily ON (gibbonfamilychild.gibbonFamilyID=gibbonfamily.gibbonFamilyID) WHERE gibbonstudentenrolment.gibbonSchoolYearID=:gibbonSchoolYearID AND gibbonperson.status='Full' ORDER BY rollGroup, surname, preferredName" ;
		$result=$connection2->prepare($sql);
		$result->execute($data); 
	}
	catch(PDOException $e) { 
		print "<div class='error'>" . $e->getMessage() . "</div>" ; 
	}
	if ($result->rowCount()<1) {
		print "<div class='error'>" ;
			print _("There are no records to display.") ;
		print "</div>" ;
	}
	else {
		$currentRollGroup="" ;
		$lastRollGroup="" ;
		$count=0;
		$countTotal=0;
		$rowNum="odd" ;
		while ($row=$result->fetch()) {
			$currentRollGroup=$row["rollGroup"] ;
			
			//SPLIT INTO ROLL GROUPS
			if ($currentRollGroup!=$lastRollGroup) {
				if ($lastRollGroup!="") {
					print "</table>" ;
				}
				print "<h2>" . $row["rollGroup"] . "</h2>" ;
				$count=0;
				$rowNum="odd" ;
				print "<table cellspacing='0' style='width: 100%'>" ;
					print "<tr class='head'>" ;
						print "<th>" ;
							print _("Total Count") ;
						print "</th>" ;
						print "<th>" ;
							print _("Section Count") ;
						print "</th>" ;
						print "<th>" ;
							print _("Student") ;
						print "</th>" ;
						print "<th>" ;
							print _("Sibling Count") ;
						print "</th>" ;
					print "</tr>" ;
			}
			$lastRollGroup=$row["rollGroup"] ;
			
			//PUMP OUT STUDENT DATA
			//Check for older siblings
			$proceed=FALSE ;
			try {
				$dataSibling=array("gibbonSchoolYearID"=>$_SESSION[$guid]["gibbonSchoolYearID"], "gibbonFamilyID"=>$row["gibbonFamilyID"]); 
				$sqlSibling="SELECT gibbonperson.gibbonPersonID, surname, preferredName, gibbonfamily.name FROM gibbonperson JOIN gibbonstudentenrolment ON (gibbonstudentenrolment.gibbonPersonID=gibbonperson.gibbonPersonID) JOIN gibbonfamilychild ON (gibbonfamilychild.gibbonPersonID=gibbonperson.gibbonPersonID) JOIN gibbonfamily ON (gibbonfamilychild.gibbonFamilyID=gibbonfamily.gibbonFamilyID) WHERE gibbonstudentenrolment.gibbonSchoolYearID=:gibbonSchoolYearID AND gibbonperson.status='Full' AND gibbonfamily.gibbonFamilyID=:gibbonFamilyID ORDER BY gibbonfamily.gibbonFamilyID, dob" ;
				$resultSibling=$connection2->prepare($sqlSibling);
				$resultSibling->execute($dataSibling); 
			}
			catch(PDOException $e) { 
				print "<div class='error'>" . $e->getMessage() . "</div>" ; 
			}
			
			if ($resultSibling->rowCount()==1) {
				$proceed=TRUE ;
			}
			else {
				$rowSibling=$resultSibling->fetch() ;
				if ($rowSibling["gibbonPersonID"]==$row["gibbonPersonID"]) {
					$proceed=TRUE ;
				}
			}
			
			if ($proceed==TRUE) {
				if ($count%2==0) {
					$rowNum="even" ;
				}
				else {
					$rowNum="odd" ;
				}
				print "<tr class=$rowNum>" ;
					print "<td style='width: 20%'>" ;
						print $countTotal+1 ;
					print "</td>" ;
					print "<td style='width: 20%'>" ;
						print $count+1 ;
					print "</td>" ;
					print "<td>" ;
						print formatName("", $row["preferredName"], $row["surname"], "Student", TRUE) ;
					print "</td>" ;
					print "<td style='width: 20%'>" ;
						print ($resultSibling->rowCount()-1) ;
					print "</td>" ;
				print "</tr>" ;
				$count++ ;
				$countTotal++ ;
			}
		}
		print "</table>" ;
	}
}
?>