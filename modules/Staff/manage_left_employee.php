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
	$sql="UPDATE `gibbonperson` SET `status`='Left' WHERE `gibbonPersonID` IN (SELECT `gibbonPersonID` FROM `gibbonstaff` WHERE 1) AND `gibbonperson`.`dateEnd`<'".date('Y-m-d')."'";
	$result=$connection2->prepare($sql);
	$result->execute();
if (isActionAccessible($guid, $connection2, "/modules/Staff/staff_view.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
	//Get action with highest precendence
	// $highestAction=getHighestGroupedAction($guid, $_GET["q"], $connection2) ;
	// if ($highestAction==FALSE) {
	// 	print "<div class='error'>" ;
	// 	print _("The highest grouped action cannot be determined.") ;
	// 	print "</div>" ;
	// }
	//else {
		//Proceed!
		print "<div class='trail'>" ;
		print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . _("Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . _(getModuleName($_GET["q"])) . "</a> > </div><div class='trailEnd'>" . _('View Staff Profiles') . "</div>" ;
		print "</div>" ;
		
		$search=NULL ;
		if (isset($_GET["search"])) {
			$search=$_GET["search"] ;
		}
		$allStaff="" ;
		if (isset($_GET["allStaff"])) {
			$allStaff=$_GET["allStaff"] ;
		} 
		
		print "<h2>" ;
		print _("Manage Left Employee: ") ;
		print "</h2>" ;
		echo "<br><b style='float:left; color:black'>Including Left: </b><input type='checkbox' name='left' id='left' style='float:left'><br><br>";
		//Set pagination variable
		$page=1 ; if (isset($_GET["page"])) { $page=$_GET["page"] ; }
		if ((!is_numeric($page)) OR $page<1) {
			$page=1 ;
		}
		echo "<div id='records'></div>";
	//}
	
	
}
?>
<input type="hidden" id="linkURL" value="<?php echo $_SESSION[$guid]["absoluteURL"] ;?>/modules/Staff/ajax_staff_leave.php">
 <script src="<?php echo $_SESSION[$guid]["absoluteURL"] ;?>/modules/Staff/js/jquery.dataTables.min.js"></script>
 <style>
 </style>