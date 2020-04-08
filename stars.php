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

print "<div class='trail'>" ;
print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . _("Home") . "</a> > </div><div class='trailEnd'>" . _("Stars") . "</div>" ;
print "</div>" ;
print "<p>" ;
print _("This page shows you a break down of how your stars have been earned, as well as where your most recent stars in each category have come from.") ;
print "</p>" ;

//Count planner likes
try {
	$dataLike=array("gibbonPersonID"=>$_SESSION[$guid]["gibbonPersonID"], "gibbonSchoolYearID"=>$_SESSION[$guid]["gibbonSchoolYearID"]); 
	$sqlLike="SELECT timestamp, gibbonperson.gibbonPersonID, surname, preferredName, gibbonRoleIDPrimary, image_75, gibbonRoleIDPrimary, gibbonplannerentry.name, gibboncourse.nameShort AS course, gibboncourseclass.nameShort AS class, gibboncourseclass.gibbonCourseClassID FROM gibbonplannerentrylike JOIN gibbonplannerentry ON (gibbonplannerentrylike.gibbonPlannerEntryID=gibbonplannerentry.gibbonPlannerEntryID) JOIN gibboncourseclass ON (gibbonplannerentry.gibbonCourseClassID=gibboncourseclass.gibbonCourseClassID) JOIN gibboncourse ON (gibboncourse.gibbonCourseID=gibboncourseclass.gibbonCourseID) JOIN gibboncourseclassperson ON (gibboncourseclassperson.gibbonCourseClassID=gibboncourseclass.gibbonCourseClassID) JOIN gibbonperson ON (gibbonperson.gibbonPersonID=.gibbonplannerentrylike.gibbonPersonID) WHERE gibboncourseclassperson.gibbonPersonID=:gibbonPersonID AND role='Teacher' AND gibbonSchoolYearID=:gibbonSchoolYearID ORDER BY timestamp DESC" ;
	$resultLike=$connection2->prepare($sqlLike);
	$resultLike->execute($dataLike);
}
catch(PDOException $e) { 
	print "<div class='error'>" . $e->getMessage() . "</div>" ; 
}
if ($resultLike->rowCount()>0) {
	print "<h2>" ;
	print _("Planner Stars") . " <span style='font-size: 65%; font-style: italic; font-weight: normal'> x" . $resultLike->rowCount() . "</span>" ;
	print "</h2>" ;
	print "<table cellspacing='0' style='width: 100%'>" ;
		print "<tr class='head'>" ;
			print "<th style='width: 90px'>" ;
				print _("Photo") ;
			print "</th>" ;
			print "<th style='width: 180px'>" ;
				print _("Name") ;
			print "</th>" ;
			print "<th>" ;
				print _("Class/Lesson") ;
			print "</th>" ;
			print "<th style='width: 70px'>" ;
				print _("Date") ;
			print "</th>" ;
		print "</tr>" ;
		
		$count=0;
		$rowNum="odd" ;
		while ($row=$resultLike->fetch() AND $count<20) {
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
					print getUserPhoto($guid, $row["image_75"], 75) ;
				print "</td>" ;
				print "<td>" ;
					$roleCategory=getRoleCategory($row["gibbonRoleIDPrimary"], $connection2) ;
					if ($roleCategory=="Student" AND isActionAccessible($guid, $connection2, "/modules/Students/student_view_details.php")) {
						print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Students/student_view_details.php&gibbonPersonID=" . $row["gibbonPersonID"] . "'>" . formatName("", $row["preferredName"], $row["surname"], $roleCategory, false) . "</a><br/>" ;
						print "<i>$roleCategory</i>" ;
					}
					else {
						print formatName("", $row["preferredName"], $row["surname"], $roleCategory, false) . "<br/>" ;
						print "<i>$roleCategory</i>" ;
					}
				print "</td>" ;
				print "<td>" ;
					print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Planner/planner.php&viewBy=class&gibbonCourseClassID=" . $row["gibbonCourseClassID"] . "'>" . $row["course"] . "." . $row["class"] . "</a><br/>" ;
					print $row["name"] ;
				print "</td>" ;
				print "<td>" ;
					print dateConvertBack($guid, substr($row["timestamp"],0,10)) ;
				print "</td>" ;
			print "</tr>" ;
		}
	print "</table>" ;
}

//Count positive haviour
try {
	$dataLike=array("gibbonPersonID"=>$_SESSION[$guid]["gibbonPersonID"], "gibbonSchoolYearID"=>$_SESSION[$guid]["gibbonSchoolYearID"]); 
	$sqlLike="SELECT descriptor, comment, timestamp, surname, preferredName, gibbonRoleIDPrimary, image_75, gibbonRoleIDPrimary FROM gibbonbehaviour JOIN gibbonperson ON (gibbonperson.gibbonPersonID=gibbonbehaviour.gibbonPersonIDCreator) WHERE gibbonbehaviour.gibbonPersonID=:gibbonPersonID AND type='Positive' AND gibbonSchoolYearID=:gibbonSchoolYearID ORDER BY timestamp DESC" ;
	$resultLike=$connection2->prepare($sqlLike);
	$resultLike->execute($dataLike);
}
catch(PDOException $e) { 
	print "<div class='error'>" . $e->getMessage() . "</div>" ; 
}
if ($resultLike->rowCount()>0) {
	print "<h2>" ;
	print _("Behaviour Stars") . " <span style='font-size: 65%; font-style: italic; font-weight: normal'> x" . $resultLike->rowCount() . "</span>" ;
	print "</h2>" ;
	print "<table cellspacing='0' style='width: 100%'>" ;
		print "<tr class='head'>" ;
			print "<th style='width: 90px'>" ;
				print _("Photo") ;
			print "</th>" ;
			print "<th style='width: 180px'>" ;
				print _("Name") ;
			print "</th>" ;
			print "<th>" ;
				print _("Details") ;
			print "</th>" ;
			print "<th style='width: 70px'>" ;
				print _("Date") ;
			print "</th>" ;
		print "</tr>" ;
		
		$count=0;
		$rowNum="odd" ;
		while ($row=$resultLike->fetch() AND $count<10) {
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
					print getUserPhoto($guid, $row["image_75"], 75) ;
				print "</td>" ;
				print "<td>" ;
					$roleCategory=getRoleCategory($row["gibbonRoleIDPrimary"], $connection2) ;
					print formatName("", $row["preferredName"], $row["surname"], $roleCategory, false) . "<br/>" ;
				print "</td>" ;
				print "<td>" ;
					print "<b>" . $row["descriptor"] . "</b><br/>" ;
					if ($row["comment"]!="") {
						print "<i>\"" . $row["comment"] . "\"</i>" ;
					}
				print "</td>" ;
				print "<td>" ;
					print dateConvertBack($guid, substr($row["timestamp"],0,10)) ;
				print "</td>" ;
			print "</tr>" ;
		}
	print "</table>" ;
}


//Count crowd assessment likes
try {
	$dataLike=array("gibbonPersonID"=>$_SESSION[$guid]["gibbonPersonID"], "gibbonSchoolYearID"=>$_SESSION[$guid]["gibbonSchoolYearID"]); 
	$sqlLike="SELECT gibbonplannerentryhomework.gibbonPlannerEntryHomeworkID, gibbonplannerentry.gibbonPlannerEntryID, gibbonperson.gibbonPersonID, surname, preferredName, gibbonRoleIDPrimary, image_75, gibbonplannerentry.name, gibboncrowdassesslike.timestamp FROM gibboncrowdassesslike JOIN gibbonplannerentryhomework ON (gibboncrowdassesslike.gibbonPlannerEntryHomeworkID=gibbonplannerentryhomework.gibbonPlannerEntryHomeworkID) JOIN gibbonplannerentry ON (gibbonplannerentryhomework.gibbonPlannerEntryID=gibbonplannerentry.gibbonPlannerEntryID) JOIN gibbonperson ON (gibboncrowdassesslike.gibbonPersonID=gibbonperson.gibbonPersonID) JOIN gibboncourseclass ON (gibbonplannerentry.gibbonCourseClassID=gibboncourseclass.gibbonCourseClassID) JOIN gibboncourse ON (gibboncourseclass.gibbonCourseID=gibboncourse.gibbonCourseID) WHERE gibbonplannerentryhomework.gibbonPersonID=:gibbonPersonID AND gibbonSchoolYearID=:gibbonSchoolYearID ORDER BY timestamp DESC" ;
	$resultLike=$connection2->prepare($sqlLike);
	$resultLike->execute($dataLike);
}
catch(PDOException $e) { 
	print "<div class='error'>" . $e->getMessage() . "</div>" ; 
}
if ($resultLike->rowCount()>0) {
	print "<h2>" ;
	print _("Crowd Assessment Stars") ." <span style='font-size: 65%; font-style: italic; font-weight: normal'> x" . $resultLike->rowCount() . "</span>" ;
	print "</h2>" ;
	print "<table cellspacing='0' style='width: 100%'>" ;
		print "<tr class='head'>" ;
			print "<th style='width: 90px'>" ;
				print _("Photo") ;
			print "</th>" ;
			print "<th style='width: 180px'>" ;
				print _("Name") ;
			print "</th>" ;
			print "<th>" ;
				print _("Lesson") ;
			print "</th>" ;
			print "<th style='width: 70px'>" ;
				print _("Date") ;
			print "</th>" ;
		print "</tr>" ;
		
		$count=0;
		$rowNum="odd" ;
		while ($row=$resultLike->fetch() AND $count<10) {
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
					print getUserPhoto($guid, $row["image_75"], 75) ;
				print "</td>" ;
				print "<td>" ;
					$roleCategory=getRoleCategory($row["gibbonRoleIDPrimary"], $connection2) ;
					if ($roleCategory=="Student" AND isActionAccessible($guid, $connection2, "/modules/Students/student_view_details.php")) {
						print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Students/student_view_details.php&gibbonPersonID=" . $row["gibbonPersonID"] . "'>" . formatName("", $row["preferredName"], $row["surname"], $roleCategory, false) . "</a><br/>" ;
						print "<i>$roleCategory</i>" ;
					}
					else {
						
						print "<i>$roleCategory</i>" ;
					}
				print "</td>" ;
				print "<td>" ;
					print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Crowd Assessment/crowdAssess_view_discuss.php&gibbonPlannerEntryID=" . $row["gibbonPlannerEntryID"] . "&gibbonPlannerEntryHomeworkID=" . $row["gibbonPlannerEntryHomeworkID"] . "&gibbonPersonID=" . $_SESSION[$guid]["gibbonPersonID"] . "'>" . $row["name"] . "</a>" ;
				print "</td>" ;
				print "<td>" ;
					print dateConvertBack($guid, substr($row["timestamp"],0,10)) ;
				print "</td>" ;
			print "</tr>" ;
		}
	print "</table>" ;
}




?>



