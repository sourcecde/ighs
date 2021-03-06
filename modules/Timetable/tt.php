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

if (isActionAccessible($guid, $connection2, "/modules/Timetable/tt.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
	//Get action with highest precendence
	$highestAction=getHighestGroupedAction($guid, $_GET["q"], $connection2) ;
	if ($highestAction==FALSE) {
		print "<div class='error'>" ;
		print _("The highest grouped action cannot be determined.") ;
		print "</div>" ;
	}
	else {
		print "<div class='trail'>" ;
		print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . _("Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . _(getModuleName($_GET["q"])) . "</a> > </div><div class='trailEnd'>" . _('View Timetable by Person') . "</div>" ;
		print "</div>" ;
		
		print "<h2>" ;
		print _("Filters") ;
		print "</h2>" ;
		
		$gibbonPersonID=NULL ;
		if (isset($_GET["gibbonPersonID"])) {
			$gibbonPersonID=$_GET["gibbonPersonID"] ;
		}
		$search=NULL ;
		if (isset($_GET["search"])) {
			$search=$_GET["search"] ;
		}
		$allUsers=NULL ;
		if (isset($_GET["allUsers"])) {
			$allUsers=$_GET["allUsers"] ;
		}
	
		?>
		<form method="get" action="<?php print $_SESSION[$guid]["absoluteURL"]?>/index.php">
			<table class='noIntBorder' cellspacing='0' style="width: 100%">	
				<tr><td style="width: 30%"></td><td></td></tr>
				<tr>
					<td> 
						<b><?php print _('Search For') ?></b><br/>
						<span style="font-size: 90%"><i>Preferred, surname, username.</i></span>
					</td>
					<td class="right">
						<input name="search" id="search" maxlength=20 value="<?php print $search ?>" type="text" style="width: 300px">
					</td>
				</tr>
				<tr>
					<td> 
						<b><?php print _('All Users') ?></b><br/>
						<span style="font-size: 90%"><i><?php print _('Include non-staff, non-student users.') ?></i></span>
					</td>
					<td class="right">
						<?php
						$checked="" ;
						if ($allUsers=="on") {
							$checked="checked" ;
						}
						print "<input $checked name=\"allUsers\" id=\"allUsers\" type=\"checkbox\">" ;
						?>
					</td>
				</tr>
				<tr>
					<td colspan=2 class="right">
						<input type="hidden" name="q" value="/modules/<?php print $_SESSION[$guid]["module"] ?>/tt.php">
						<input type="hidden" name="address" value="<?php print $_SESSION[$guid]["address"] ?>">
						<?php
						print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/tt.php'>" . _('Clear Filters') . "</a>" ;
						?>
						<input type="submit" value="<?php print _("Submit") ; ?>">
					</td>
				</tr>
			</table>
		</form>
		<?php
		
		print "<h2>" ;
		print _("Choose A Person") ;
		print "</h2>" ;
		
		//Set pagination variable
		$page=1 ; if (isset($_GET["page"])) { $page=$_GET["page"] ; }
		if ((!is_numeric($page)) OR $page<1) {
			$page=1 ;
		}
		
		try {
			if ($allUsers=="on") {
				$data=array("gibbonSchoolYearID"=>$_SESSION[$guid]["gibbonSchoolYearID"]); 
				$sql="SELECT gibbonperson.gibbonPersonID, surname, preferredName, title, gibbonyeargroup.nameShort AS yearGroup, gibbonrollgroup.nameShort AS rollGroup, 'Student' AS type FROM gibbonperson LEFT JOIN gibbonstudentenrolment ON (gibbonstudentenrolment.gibbonPersonID=gibbonperson.gibbonPersonID AND gibbonSchoolYearID=:gibbonSchoolYearID) LEFT JOIN gibbonrollgroup ON (gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID) LEFT JOIN gibbonyeargroup ON (gibbonstudentenrolment.gibbonYearGroupID=gibbonyeargroup.gibbonYearGroupID) WHERE gibbonperson.status='Full' ORDER BY surname, preferredName" ; 
				if ($search!="") {
					$data=array("gibbonSchoolYearID"=>$_SESSION[$guid]["gibbonSchoolYearID"], "search1"=>"%$search%", "search2"=>"%$search%", "search3"=>"%$search%"); 
					$sql="SELECT gibbonperson.gibbonPersonID, surname, preferredName, title, gibbonyeargroup.nameShort AS yearGroup, gibbonrollgroup.nameShort AS rollGroup, 'Student' AS type FROM gibbonperson LEFT JOIN gibbonstudentenrolment ON (gibbonstudentenrolment.gibbonPersonID=gibbonperson.gibbonPersonID AND gibbonSchoolYearID=:gibbonSchoolYearID) LEFT JOIN gibbonrollgroup ON (gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID) LEFT JOIN gibbonyeargroup ON (gibbonstudentenrolment.gibbonYearGroupID=gibbonyeargroup.gibbonYearGroupID) WHERE gibbonperson.status='Full' AND ((preferredName LIKE :search1) OR (surname LIKE :search2) OR (username LIKE :search3)) ORDER BY surname, preferredName" ; 
				}
			}
			else {
				$data=array("gibbonSchoolYearID"=>$_SESSION[$guid]["gibbonSchoolYearID"]); 
				$sql="(SELECT gibbonperson.gibbonPersonID, gibbonStudentEnrolmentID, surname, preferredName, title, gibbonyeargroup.nameShort AS yearGroup, gibbonrollgroup.nameShort AS rollGroup, 'Student' AS type FROM gibbonperson, gibbonstudentenrolment, gibbonyeargroup, gibbonrollgroup WHERE (gibbonperson.gibbonPersonID=gibbonstudentenrolment.gibbonPersonID) AND (gibbonstudentenrolment.gibbonYearGroupID=gibbonyeargroup.gibbonYearGroupID) AND (gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID) AND gibbonstudentenrolment.gibbonSchoolYearID=:gibbonSchoolYearID AND gibbonperson.status='Full') UNION (SELECT gibbonperson.gibbonPersonID, NULL AS gibbonStudentEnrolmentID, surname, preferredName, title, NULL AS yearGroup, NULL AS rollGroup, 'Staff' as type FROM gibbonperson JOIN gibbonstaff ON (gibbonperson.gibbonPersonID=gibbonstaff.gibbonPersonID) WHERE type='Teaching' AND gibbonperson.status='Full') ORDER BY surname, preferredName" ; 
				if ($search!="") {
					$data=array("gibbonSchoolYearID"=>$_SESSION[$guid]["gibbonSchoolYearID"], "search1"=>"%$search%", "search2"=>"%$search%", "search3"=>"%$search%", "search4"=>"%$search%", "search5"=>"%$search%", "search6"=>"%$search%"); 
					$sql="(SELECT gibbonperson.gibbonPersonID, gibbonStudentEnrolmentID, surname, preferredName, title, gibbonyeargroup.nameShort AS yearGroup, gibbonrollgroup.nameShort AS rollGroup, 'Student' AS type FROM gibbonperson, gibbonstudentenrolment, gibbonyeargroup, gibbonrollgroup WHERE (gibbonperson.gibbonPersonID=gibbonstudentenrolment.gibbonPersonID) AND (gibbonstudentenrolment.gibbonYearGroupID=gibbonyeargroup.gibbonYearGroupID) AND (gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID) AND gibbonstudentenrolment.gibbonSchoolYearID=:gibbonSchoolYearID AND gibbonperson.status='Full' AND ((preferredName LIKE :search1) OR (surname LIKE :search2) OR (username LIKE :search3))) UNION (SELECT gibbonperson.gibbonPersonID, NULL AS gibbonStudentEnrolmentID, surname, preferredName, title, NULL AS yearGroup, NULL AS rollGroup, 'Staff' as type FROM gibbonperson JOIN gibbonstaff ON (gibbonperson.gibbonPersonID=gibbonstaff.gibbonPersonID) WHERE type='Teaching' AND gibbonperson.status='Full' AND ((preferredName LIKE :search4) OR (surname LIKE :search5) OR (username LIKE :search6))) ORDER BY surname, preferredName" ; 
				}
			}
			$sqlPage=$sql . " LIMIT " . $_SESSION[$guid]["pagination"] . " OFFSET " . (($page-1)*$_SESSION[$guid]["pagination"]) ; 
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
			if ($result->rowCount()>$_SESSION[$guid]["pagination"]) {
				printPagination($guid, $result->rowCount(), $page, $_SESSION[$guid]["pagination"], "top", "allUsers=$allUsers&search=$search") ;
			}
		
			print "<table cellspacing='0' style='width: 100%'>" ;
				print "<tr class='head'>" ;
					print "<th>" ;
						print _("Name") ;
					print "</th>" ;
					print "<th>" ;
						print _("Class") ;
					print "</th>" ;
					print "<th>" ;
						print _("Section") ;
					print "</th>" ;
					print "<th>" ;
						print _("Actions") ;
					print "</th>" ;
				print "</tr>" ;
				
				$count=0;
				$rowNum="odd" ;
				try {
					$resultPage=$connection2->prepare($sqlPage);
					$resultPage->execute($data);
				}
				catch(PDOException $e) { 
					print "<div class='error'>" . $e->getMessage() . "</div>" ; 
				}
				while ($row=$resultPage->fetch()) {
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
							if ($row["type"]=="Staff") {
								print formatName($row["title"], $row["preferredName"], $row["surname"], $row["type"], true ) ;
							}
							if ($row["type"]=="Student") {
								print formatName($row["title"], $row["preferredName"], $row["surname"], $row["type"], true ) ;
							}
						print "</td>" ;
						print "<td>" ;
							if ($row["yearGroup"]!="") {
								print _($row["yearGroup"]) ;
							}
						print "</td>" ;
						print "<td>" ;
							if ($row["rollGroup"]!="") {
								print SectionFormater($row["rollGroup"]) ;
							}
						print "</td>" ;
						print "<td>" ;
							print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/tt_view.php&gibbonPersonID=" . $row["gibbonPersonID"] . "&allUsers=$allUsers&search=$search'><img title='" . _('View Details') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/plus.png'/></a> " ;
						print "</td>" ;
					print "</tr>" ;
				}
			print "</table>" ;
			
			if ($result->rowCount()>$_SESSION[$guid]["pagination"]) {
				printPagination($guid, $result->rowCount(), $page, $_SESSION[$guid]["pagination"], "bottom", "allUsers=$allUsers&search=$search") ;
			}
		}
	}
}
?>