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

if (isActionAccessible($guid, $connection2, "/modules/School Admin/house_manage.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
	//Proceed!
	print "<div class='trail'>" ;
	print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . _("Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . _(getModuleName($_GET["q"])) . "</a> > </div><div class='trailEnd'>" . _('Manage Houses') . "</div>" ;
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
	
	//Set pagination variable
	$page=1 ; if (isset($_GET["page"])) { $page=$_GET["page"] ; }
	if ((!is_numeric($page)) OR $page<1) {
		$page=1 ;
	}
	
	try {
		$data=array(); 
		//$sql="SELECT * FROM gibbonhouse ORDER BY name" ;
		$sql="SELECT gibbonhouse.*,p1.officialName AS 'student',p2.officialName AS 'stuff',
		(SELECT COUNT(*) FROM gibbonperson WHERE gibbonperson.gibbonHouseID=gibbonhouse.gibbonHouseID AND gibbonperson.dateEnd IS NULL) AS 'total_student' 
		 FROM gibbonhouse LEFT 
JOIN gibbonperson AS p1 ON gibbonhouse.student_personid=p1.gibbonPersonID
JOIN gibbonperson AS p2 ON gibbonhouse.stuff_personid=p2.gibbonPersonID
ORDER BY NAME";
		
		
		$result=$connection2->prepare($sql);
		$result->execute($data);
	}
	catch(PDOException $e) { 
		print "<div class='error'>" . $e->getMessage() . "</div>" ; 
	}

	print "<div class='linkTop'>" ;
	print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/house_manage_add.php'>" .  _('Add') . "<img style='margin-left: 5px' title='" . _('Add') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/page_new.png'/></a>" ;
	print "</div>" ;
	
	if ($result->rowCount()<1) {
		print "<div class='error'>" ;
		print _("There are no records to display.") ;
		print "</div>" ;
	}
	else {
		print "<table cellspacing='0' style='width: 100%'>" ;
			print "<tr class='head'>" ;
				print "<th>" ;
					print _("Name") ;
				print "</th>" ;
				print "<th>" ;
					print _("Short Name") ;
				print "</th>" ;
				print "<th>" ;
					print _("House Incharge") ;
				print "</th>" ;
				print "<th>" ;
					print _("House Captain") ;
				print "</th>" ;
				print "<th>" ;
					print _("Total Student") ;
				print "</th>" ;
				print "<th>" ;
					print _("Actions") ;
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
				
				
				//COLOR ROW BY STATUS!
				print "<tr class=$rowNum>" ;
					print "<td>" ;
						print $row["name"] ;
					print "</td>" ;
					print "<td>" ;
						print $row["nameShort"] ;
					print "</td>" ;
					print "<td>" ;
						print $row["stuff"] ;
					print "</td>" ;
					print "<td>" ;
						print $row["student"] ;
					print "</td>" ;
					print "<td>" ;
						print $row["total_student"] ;
					print "</td>" ;
					print "<td>" ;
						print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/house_manage_edit.php&gibbonHouseID=" . $row["gibbonHouseID"] . "'><img title='" . _('Edit') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/config.png'/></a> " ;
						print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/house_manage_delete.php&gibbonHouseID=" . $row["gibbonHouseID"] . "'><img title='" . _('Delete') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/garbage.png'/></a> " ;
						print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/house_manage_view.php&gibbonHouseID=" . $row["gibbonHouseID"] . "'><img title='" . _('House') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/house.png'/></a> " ;
						print "<a href='javascript:void(0)'><img title='" . _('Print') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/print.png' class='print_house' id='".$row["gibbonHouseID"]."'/></a> " ;
					print "</td>" ;
				print "</tr>" ;
				
				$count++ ;
			}
		print "</table>" ;
	}
}
?>
<input type="hidden" name="url" id="url" value="<?php echo $_SESSION[$guid]["absoluteURL"] . "/modules/School Admin/house_manage_print.php";?>">