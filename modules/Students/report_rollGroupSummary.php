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

if (isActionAccessible($guid, $connection2, "/modules/Students/report_rollGroupSummary.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
	//Proceed!
	print "<div class='trail'>" ;
	print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . _("Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . _(getModuleName($_GET["q"])) . "</a> > </div><div class='trailEnd'>" . _('Roll Group Summary') . "</div>" ;
	print "</div>" ;
	
	$today=time() ;
	
	//Get roll groups in current school year
	try {
		$data=array("gibbonSchoolYearID"=>$_SESSION[$guid]["gibbonSchoolYearID"]); 
		$sql="SELECT * FROM gibbonrollgroup WHERE gibbonSchoolYearID=:gibbonSchoolYearID ORDER BY gibbonRollGroupID ASC" ;
		$result=$connection2->prepare($sql);
		$result->execute($data); 
	}
	catch(PDOException $e) {}
	
	//Get all students
	try {
		$dataList=array("gibbonSchoolYearID"=>$_SESSION[$guid]["gibbonSchoolYearID"]); 
		$sqlList="SELECT gibbonrollgroup.name AS rollGroup, dob, gender FROM gibbonperson, gibbonstudentenrolment, gibbonrollgroup WHERE gibbonperson.gibbonPersonID=gibbonstudentenrolment.gibbonPersonID AND gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID AND status='FULL' AND (dateStart IS NULL OR dateStart<='" . date("Y-m-d") . "') AND (dateEnd IS NULL  OR dateEnd>='" . date("Y-m-d") . "') AND gibbonstudentenrolment.gibbonSchoolYearID=:gibbonSchoolYearID ORDER BY rollGroup" ;
		$resultList=$connection2->prepare($sqlList);
		$resultList->execute($dataList); 
	}
	catch(PDOException $e) {}
	
	$everything=array() ;
	$count=0 ;
	while ($rowList=$resultList->fetch()) {
		$everything[$count][0]=$rowList["dob"] ;
		$everything[$count][1]=$rowList["gender"] ;
		$everything[$count][2]=$rowList["rollGroup"] ;
		$count++ ;
	}
	
	if ($result->rowCount()==0) {
		print "<div class='error'>" ;
			print _("There are no records to display.") ;
		print "</div>" ;
	}
	else {
		print "<table cellspacing='0' style='width: 100%'>" ;
			print "<tr class='head'>" ;
				print "<th>" ;
					print _("Section") ;
				print "</th>" ;
				print "<th>" ;
					print _("Mean Age") ;
				print "</th>" ;
				print "<th>" ;
					print _("Male") ;
				print "</th>" ;
				print "<th>" ;
					print _("Female") ;
				print "</th>" ;
				print "<th>" ;
					print _("Total") ;
				print "</th>" ;
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
					print "<td>" ;
						print $row["name"] ;
					print "</td>" ;
					print "<td>" ;
						$cellCount=0 ;
						$total=0 ;
						foreach ($everything as $thing) {
							if ($thing[2]==$row["name"]) {
								$cellCount++ ;
								$total+=(($today-strtotime($thing[0]))/31556926) ;
							}
						}
						if ($cellCount!=0) {
							print round(($total/$cellCount),1) ;
						}
					print "</td>" ;
					print "<td>" ;
						$cellCount=0 ;
						foreach ($everything as $thing) {
							if ($thing[1]=="M" AND $thing[2]==$row["name"]) {
								$cellCount++ ;
							}
						}
						if ($cellCount!=0) {
							print $cellCount ;
						}
					print "</td>" ;
					print "<td>" ;
						$cellCount=0 ;
						foreach ($everything as $thing) {
							if ($thing[1]=="F" AND $thing[2]==$row["name"]) {
								$cellCount++ ;
							}
						}
						if ($cellCount!=0) {
							print $cellCount ;
						}
					print "</td>" ;
					print "<td>" ;
						$cellCount=0 ;
						foreach ($everything as $thing) {
							if ($thing[2]==$row["name"]) {
								$cellCount++ ;
							}
						}
						if ($cellCount!=0) {
							print "<b>" . $cellCount . "</b>" ;
						}
					print "</td>" ;
				print "</tr>" ;
			}
			print "<tr style='background-color: #FFD2A9'>" ;
				print "<td>" ;
					print "<b>" . _('Total') . "</b>" ;
				print "</td>" ;
				print "<td>" ;
						$cellCount=0 ;
						$total=0 ;
						foreach ($everything as $thing) {
							$cellCount++ ;
							$total+=(($today-strtotime($thing[0]))/31556926) ;
						}
						if ($cellCount!=0) {
							print "<b>" . round(($total/$cellCount),1) . "</b>" ;
						}
				print "</td>" ;
				print "<td>" ;
						$cellCount=0 ;
						foreach ($everything as $thing) {
							if ($thing[1]=="M") {
								$cellCount++ ;
							}
						}
						if ($cellCount!=0) {
							print "<b>" . $cellCount . "</b>" ;
						}
				print "</td>" ;
				print "<td>" ;
						$cellCount=0 ;
						foreach ($everything as $thing) {
							if ($thing[1]=="F") {
								$cellCount++ ;
							}
						}
						if ($cellCount!=0) {
							print "<b>" . $cellCount . "</b>" ;
						}
				print "</td>" ;
				print "<td>" ;
						if (count($everything)!=0) {
							print "<b>" . count($everything) . "</b>" ;
						}
				print "</td>" ;
			print "</tr>" ;
		print "</table>" ;
	}
}
?>