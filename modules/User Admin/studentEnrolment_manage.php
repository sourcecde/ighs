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

if (isActionAccessible($guid, $connection2, "/modules/User Admin/studentEnrolment_manage.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
	
	//Proceed!
	print "<div class='trail'>" ;
	print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . _("Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . _(getModuleName($_GET["q"])) . "</a> > </div><div class='trailEnd'>" . _('Manage Student Enrolment') . "</div>" ;
	print "</div>" ;
	
	if (isset($_GET["deleteReturn"])) { $deleteReturn=$_GET["deleteReturn"] ; } else { $deleteReturn="" ; }
	$deleteReturnMessage="" ;
	$class="error" ;
	if (!($deleteReturn=="")) {
		if ($deleteReturn=="success0") {
			$deleteReturnMessage=_("Your request was completed successfully.") ;		
			$class="success" ;
		}
		print "<div class='$class'>" ;
			print $deleteReturnMessage;
		print "</div>" ;
	} 
	
	$gibbonSchoolYearID="" ;
	if (isset($_GET["gibbonSchoolYearID"])) {
		$gibbonSchoolYearID=$_GET["gibbonSchoolYearID"] ;
	}
	if ($gibbonSchoolYearID=="" OR $gibbonSchoolYearID==$_SESSION[$guid]["gibbonSchoolYearID"]) {
		$gibbonSchoolYearID=$_SESSION[$guid]["gibbonSchoolYearID"] ;
		$gibbonSchoolYearName=$_SESSION[$guid]["gibbonSchoolYearName"] ;
	}
	
	if ($gibbonSchoolYearID!=$_SESSION[$guid]["gibbonSchoolYearID"]) {
		try {
			$data=array("gibbonSchoolYearID"=>$_GET["gibbonSchoolYearID"]); 
			$sql="SELECT * FROM gibbonschoolyear WHERE gibbonSchoolYearID=:gibbonSchoolYearID" ;
			$result=$connection2->prepare($sql);
			$result->execute($data);
		}
		catch(PDOException $e) { 
			print "<div class='error'>" . $e->getMessage() . "</div>" ; 
		}
		if ($result->rowcount()!=1) {
			print "<div class='error'>" ;
				print _("The specified record does not exist.") ;
			print "</div>" ;
		}
		else {
			$row=$result->fetch() ;
			$gibbonSchoolYearID=$row["gibbonSchoolYearID"] ;
			$gibbonSchoolYearName=$row["name"] ;
		}
	}
	$sql="SELECT * from gibbonrollgroup WHERE `gibbonSchoolYearID`=$gibbonSchoolYearID";
	$result=$connection2->prepare($sql);
	$result->execute();
	$sectionlist=$result->fetchAll();
	
	$sql="SELECT * from gibbonyeargroup";
	$result=$connection2->prepare($sql);
	$result->execute();
	$classlist=$result->fetchAll();
	
	if ($gibbonSchoolYearID!="") {
		print "<h2>" ;
			print $gibbonSchoolYearName ;
		print "</h2>" ;
		
		print "<div class='linkTop'>" ;
			//Print year picker
			if (getPreviousSchoolYearID($gibbonSchoolYearID, $connection2)!=FALSE) {
				print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/studentEnrolment_manage.php&gibbonSchoolYearID=" . getPreviousSchoolYearID($gibbonSchoolYearID, $connection2) . "'>" . _('Previous Year') . "</a> " ;
			}
			else {
				print _("Previous Year") . " " ;
			}
			print " | " ;
			if (getNextSchoolYearID($gibbonSchoolYearID, $connection2)!=FALSE) {
				print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/studentEnrolment_manage.php&gibbonSchoolYearID=" . getNextSchoolYearID($gibbonSchoolYearID, $connection2) . "'>" . _('Next Year') . "</a> " ;
			}
			else {
				print _("Next Year") . " " ;
			}
		print "</div>" ;
	
		print "<h3>" ;
		print _("Search") ;
		print "</h3>" ;
		?>
		<form method="get" action="<?php print $_SESSION[$guid]["absoluteURL"]?>/index.php">
			<table class='noIntBorder' cellspacing='0' style="width: 100%">	
				<tr><td style="width: 30%"></td><td></td></tr>
				<tr>
					<td> 
						<b><?php print _('Search For') ?></b><br/>
						<span style="font-size: 90%"><i><?php print _('Preferred, surname, username.') ?></i></span>
					</td>
					<td class="right">
						<input name="search" id="search" maxlength=20 value="<?php if (isset($_GET["search"])) { print $_GET["search"] ; } ?>" type="text" style="width: 300px">
					</td>
				</tr>
				<tr>
					<td> 
						<b><?php //print _('Search For') ?></b><br/>
						<span style="font-size: 90%"><i>Class</i></span>
					</td>
					<td class="right">
						<select name="class" id="class">
						
					<?php if(isset($_REQUEST['class'])){
					
						?>
						<option value="">-Select Class -</option>
					<?php foreach ($classlist as $value) { ?>
							<option value="<?php echo $value['gibbonYearGroupID']?>" <?php if($_REQUEST['class']==$value['gibbonYearGroupID']){?> selected="selected"<?php } ?>><?php echo $value['name']?></option>
						<?php } ?>
						
					<?php  } else {?>
						<option value="">-Select Class -</option>
						<?php foreach ($classlist as $value) { ?>
							<option value="<?php echo $value['gibbonYearGroupID']?>" ><?php echo $value['name']?></option>
						<?php } ?>
						<?php } ?>
						
					</select>
					</td>
				</tr>
				<tr>
					<td> 
						<b><?php //print _('Search For') ?></b><br/>
						<span style="font-size: 90%"><i>Section</i></span>
					</td>
					<td class="right">
						<select name="section" id="section">
						
						<?php if(isset($_REQUEST['section'])){
					
						?>
						<option value="">-Select Section-</option>
						<?php foreach ($sectionlist as $value) { ?>
							<option value="<?php echo $value['gibbonRollGroupID'];?>" <?php if($_REQUEST['section']==$value['gibbonRollGroupID']){?> selected="selected"<?php } ?>><?php echo $value['name'];?></option>
					<?php } ?>
					
					<?php }  else {?>
						<option value="">-Select Section-</option>
						<?php foreach ($sectionlist as $value) { ?>
							<option value="<?php echo $value['gibbonRollGroupID'];?>" ><?php echo $value['name'];?></option>
					<?php } ?>
					<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan=2 class="right">
						<input type="hidden" name="q" value="/modules/<?php print $_SESSION[$guid]["module"] ?>/studentEnrolment_manage.php">
						<input type="hidden" name="address" value="<?php print $_SESSION[$guid]["address"] ?>">
						<input type="hidden" name="gibbonSchoolYearID" value="<?php print $gibbonSchoolYearID ?>">
						<?php
						print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/studentEnrolment_manage.php&gibbonSchoolYearID=$gibbonSchoolYearID'>" . _('Clear Search') . "</a>" ;
						?>
						<input type="submit" value="<?php print _("Submit") ; ?>">
					</td>
				</tr>
			</table>
		</form>
		<?php
		
		print "<h3>" ;
		print _("View") ;
		print "</h3>" ;
		print "<p>" ;
		print _("Students highlighted in red are marked as 'Full' but have either not reached their start date, or have exceeded their end date.") ;
		print "<p>" ;
		
		//Set pagination variable
		$page=1 ; if (isset($_GET["page"])) { $page=$_GET["page"] ; }
		if ((!is_numeric($page)) OR $page<1) {
			$page=1 ;
		}
		
		$search="" ;
		if (isset($_GET["search"])) {
			$search=$_GET["search"] ;
		}
		try {
			$data=array("gibbonSchoolYearID"=>$gibbonSchoolYearID); 
			$sql="SELECT `gibbonstudentenrolment`.`gibbonStudentEnrolmentID`, surname, preferredName,firstName, gibbonyeargroup.name AS yearGroup, gibbonrollgroup.nameShort AS rollGroup, dateStart, dateEnd, status, rollOrder,gibbonperson.account_number,gibbonperson.admission_number, count(payment_master_id) AS N  FROM gibbonperson
					LEFT JOIN gibbonstudentenrolment ON gibbonperson.gibbonPersonID=gibbonstudentenrolment.gibbonPersonID 
					LEFT JOIN gibbonyeargroup ON gibbonstudentenrolment.gibbonYearGroupID=gibbonyeargroup.gibbonYearGroupID
					LEFT JOIN gibbonrollgroup ON gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID 
					LEFT JOIN `payment_master` ON `payment_master`.`gibbonStudentEnrolmentID`=`gibbonstudentenrolment`.`gibbonStudentEnrolmentID` 
					WHERE gibbonrollgroup.gibbonSchoolYearID=:gibbonSchoolYearID " ; 
			if ($search!="") {
				$data=array("gibbonSchoolYearID"=>$gibbonSchoolYearID, "search1"=>"%$search%", "search2"=>"%$search%", "search3"=>"%$search%"); 
				$sql="SELECT `gibbonstudentenrolment`.gibbonStudentEnrolmentID, surname, preferredName,firstName, gibbonyeargroup.nameShort AS yearGroup, gibbonrollgroup.name AS rollGroup, dateStart, dateEnd, status, rollOrder,gibbonperson.account_number,gibbonperson.admission_number, count(payment_master_id) AS N  
				FROM gibbonperson 
				LEFT JOIN gibbonstudentenrolment ON gibbonperson.gibbonPersonID=gibbonstudentenrolment.gibbonPersonID 
				LEFT JOIN gibbonyeargroup ON gibbonstudentenrolment.gibbonYearGroupID=gibbonyeargroup.gibbonYearGroupID
				LEFT JOIN gibbonrollgroup ON gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID 
				LEFT JOIN `payment_master` ON `payment_master`.`gibbonStudentEnrolmentID`=`gibbonstudentenrolment`.`gibbonStudentEnrolmentID` 
				WHERE gibbonrollgroup.gibbonSchoolYearID=:gibbonSchoolYearID AND (preferredName LIKE :search1 OR surname LIKE :search2 OR username LIKE :search3)";
			}
			if(isset($_REQUEST['class']))
			{
					if($_REQUEST['class']!='')
					 {
					 	$sql.=" AND gibbonstudentenrolment.gibbonYearGroupID=".$_REQUEST['class'];
					 }
			}
			if(isset($_REQUEST['section']))
			{
			 if($_REQUEST['section'])
					 {
					 	$sql.=" AND gibbonstudentenrolment.gibbonRollGroupID=".$_REQUEST['section'];
					 }
			}
				
					 
			$sql.="GROUP BY `gibbonstudentenrolment`.`gibbonStudentEnrolmentID` ORDER BY gibbonperson.account_number" ;
			//echo $sql;
			$sqlPage=$sql . " LIMIT " . $_SESSION[$guid]["pagination"] . " OFFSET " . (($page-1)*$_SESSION[$guid]["pagination"]) ; 
			$result=$connection2->prepare($sql);
			$result->execute($data);
		}
		catch(PDOException $e) { 
			print "<div class='error'>" . $e->getMessage() . "</div>" ; 
		}
		
		print "<div class='linkTop'>" ;
			print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/studentEnrolment_manage_add.php&gibbonSchoolYearID=$gibbonSchoolYearID&search=$search'>" .  _('Add') . "<img style='margin-left: 5px' title='" . _('Add') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/page_new.png'/></a>" ;
		print "</div>" ;
	
		if ($result->rowCount()<1) {
			print "<div class='error'>" ;
			print _("There are no records to display.") ;
			print "</div>" ;
		}
		else {
			if ($result->rowCount()>$_SESSION[$guid]["pagination"]) {
				printPagination($guid, $result->rowCount(), $page, $_SESSION[$guid]["pagination"], "top", "gibbonSchoolYearID=$gibbonSchoolYearID&search=$search") ;
			}
		
			print "<table cellspacing='0' style='width: 100%'>" ;
				print "<tr class='head'>" ;
					print "<th>" ;
						print _("Acc&nbsp;No") ;
					print "</th>" ;
					print "<th>" ;
						print _("Admn&nbsp;No") ;
					print "</th>" ;
					print "<th>" ;
						print _("Name") ;
					print "</th>" ;
					print "<th>" ;
						print _("Class") ;
					print "</th>" ;
					print "<th>" ;
						print _("Section ")  ;
						//print "<span style='font-size: 85%; font-style: italic'>" . _("Roll Order") . "</span>" ;
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
					
					//Color rows based on start and end date
					if (!($row["dateStart"]=="" OR $row["dateStart"]<=date("Y-m-d")) AND ($row["dateEnd"]=="" OR $row["dateEnd"]>=date("Y-m-d")) OR $row["status"]!="Full") {
						$rowNum="error" ;
					}
					
					print "<tr class=$rowNum>" ;
						print "<td>" ;
							print _(substr($row["account_number"], 5)) ;
						print "</td>" ;
						print "<td>" ;
							print _($row["admission_number"]) ;
						print "</td>" ;
						print "<td>" ;
							//print formatName("", $row["preferredName"], $row["surname"], "Student", true) ;
							print $row["firstName"]." ".$row["surname"];
						print "</td>" ;
						print "<td>" ;
							print _($row["yearGroup"]) ;
						print "</td>" ;
						print "<td>" ;
							print SectionFormater($row["rollGroup"]) ;
							if ($row["rollOrder"]!="") {
								print "<br/><span style='font-size: 85%; font-style: italic'>" . $row["rollOrder"] . "</span>" ;
							}
						print "</td>" ;
						print "<td>" ;
							if($row['N']>0){
								print "Payment record exists.";
							}
							else{
							print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/studentEnrolment_manage_edit.php&gibbonStudentEnrolmentID=" . $row["gibbonStudentEnrolmentID"] . "&gibbonSchoolYearID=$gibbonSchoolYearID&search=$search'><img title='" . _('Edit') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/config.png'/></a> " ;
							print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/studentEnrolment_manage_delete.php&gibbonStudentEnrolmentID=" . $row["gibbonStudentEnrolmentID"] . "&gibbonSchoolYearID=$gibbonSchoolYearID&search=$search'><img title='" . _('Delete') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/garbage.png'/></a>" ;
							}
						print "</td>" ;
					print "</tr>" ;
				}
			print "</table>" ;
			
			if ($result->rowCount()>$_SESSION[$guid]["pagination"]) {
				printPagination($guid, $result->rowCount(), $page, $_SESSION[$guid]["pagination"], "bottom", "gibbonSchoolYearID=$gibbonSchoolYearID&search=$search") ;
			}
		}
	}
}
?>