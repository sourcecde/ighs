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

if (isActionAccessible($guid, $connection2, "/modules/Students/report_privacy_student.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
	//Proceed!
	print "<div class='trail'>" ;
	print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . _("Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . _(getModuleName($_GET["q"])) . "</a> > </div><div class='trailEnd'>" . _('Privacy Choices by Student') . "</div>" ;
	print "</div>" ;
	
	try {
		$data=array("gibbonSchoolYearID"=>$_SESSION[$guid]["gibbonSchoolYearID"]); 
		$sql="SELECT gibbonperson.gibbonPersonID, privacy, surname, preferredName, nameShort FROM gibbonperson JOIN gibbonstudentenrolment ON (gibbonperson.gibbonPersonID=gibbonstudentenrolment.gibbonPersonID) JOIN gibbonrollgroup ON (gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID) WHERE gibbonstudentenrolment.gibbonSchoolYearID=:gibbonSchoolYearID AND status='Full' AND NOT privacy='' AND (dateStart IS NULL OR dateStart<='" . date("Y-m-d") . "') AND (dateEnd IS NULL  OR dateEnd>='" . date("Y-m-d") . "') ORDER BY nameShort, surname, preferredName" ;
		$result=$connection2->prepare($sql);
		$result->execute($data);
	}
	catch(PDOException $e) { 
		print "<div class='error'>" . $e->getMessage() . "</div>" ; 
	}
	
	$privacy=getSettingByScope( $connection2, "User Admin", "privacy") ;
	$privacyOptions=explode(",", getSettingByScope( $connection2, "User Admin", "privacyOptions" )) ;
	
	if (count($privacyOptions)<1 OR $privacy=="N") {
		print "<div class='error'>" ;
			print _("There are no privacy options in place.") ;
		print "</div>" ;
	}
	else {
		print "<table cellspacing='0' style='width: 100%'>" ;
			print "<tr class='head'>" ;
				print "<th rowspan=2>" ;
					print _("Count") ;
				print "</th>" ;
				print "<th rowspan=2>" ;
					print _("Class & Section") ;
				print "</th>" ;
				print "<th rowspan=2>" ;
					print _("Student") ;
				print "</th>" ;
				print "<th colspan=" . count($privacyOptions) . ">" ;
					print _("Privacy") ;
				print "</th>" ;
			print "</tr>" ;
		
			print "<tr class='head'>" ;
				foreach ($privacyOptions AS $option) {
					print "<th>" ;
						print $option ;
					print "</th>" ;
				}
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
						print $count ;
					print "</td>" ;
					print "<td>" ;
						print $row["nameShort"] ;
					print "</td>" ;
					print "<td>" ;
						print formatName("", $row["preferredName"], $row["surname"], "Student", true) ;
					print "</td>" ;
					$studentPrivacyOptions=explode(",", $row["privacy"]) ;
					foreach ($privacyOptions AS $option) {
						print "<td>" ;
							foreach ($studentPrivacyOptions AS $studentOption) {
								if (trim($studentOption)==trim($option)) {
									print _("Yes") ;
								}
							}
						print "</td>" ;
					}
				print "</tr>" ;
			}
			if ($count==0) {
				print "<tr class=$rowNum>" ;
					print "<td colspan=3>" ;
						print _("There are no records to display.") ;
					print "</td>" ;
				print "</tr>" ;
			}
		print "</table>" ;
	}
}
?>