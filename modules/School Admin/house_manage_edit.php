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
$sql="SELECT * FROM gibbonperson WHERE gibbonRoleIDPrimary IN(001,002,006)" ;
	$result=$connection2->prepare($sql);
	$result->execute();
	$stuff=$result->fetchAll();
	
	$sql="SELECT * FROM gibbonperson WHERE gibbonRoleIDPrimary=003" ;
	$result=$connection2->prepare($sql);
	$result->execute();
	$students=$result->fetchAll();
	
if (isActionAccessible($guid, $connection2, "/modules/School Admin/house_manage_edit.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
	//Proceed!
	print "<div class='trail'>" ;
	print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . _("Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . _(getModuleName($_GET["q"])) . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/house_manage.php'>" . _('Manage Houses') . "</a> > </div><div class='trailEnd'>" . _('Edit House') . "</div>" ;
	print "</div>" ;
	
	if (isset($_GET["updateReturn"])) { $updateReturn=$_GET["updateReturn"] ; } else { $updateReturn="" ; }
	$updateReturnMessage="" ;
	$class="error" ;
	if (!($updateReturn=="")) {
		if ($updateReturn=="fail0") {
			$updateReturnMessage=_("Your request failed because you do not have access to this action.") ;	
		}
		else if ($updateReturn=="fail1") {
			$updateReturnMessage=_("Your request failed because your inputs were invalid.") ;	
		}
		else if ($updateReturn=="fail2") {
			$updateReturnMessage=_("Your request failed due to a database error.") ;	
		}
		else if ($updateReturn=="fail3") {
			$updateReturnMessage=_("Your request failed because your inputs were invalid.") ;	
		}
		else if ($updateReturn=="fail4") {
			$updateReturnMessage=_("Your request failed because your inputs were invalid.") ;	
		}
		else if ($updateReturn=="success0") {
			$updateReturnMessage=_("Your request was completed successfully.") ;	
			$class="success" ;
		}
		print "<div class='$class'>" ;
			print $updateReturnMessage;
		print "</div>" ;
	} 
	
	//Check if school year specified
	$gibbonHouseID=$_GET["gibbonHouseID"] ;
	if ($gibbonHouseID=="") {
		print "<div class='error'>" ;
			print _("You have not specified one or more required parameters.") ;
		print "</div>" ;
	}
	else {
		try {
			$data=array("gibbonHouseID"=>$gibbonHouseID); 
			$sql="SELECT * FROM gibbonhouse WHERE gibbonHouseID=:gibbonHouseID" ;
			$result=$connection2->prepare($sql);
			$result->execute($data);
		}
		catch(PDOException $e) { 
			print "<div class='error'>" . $e->getMessage() . "</div>" ; 
		}
		
		if ($result->rowCount()!=1) {
			print "<div class='error'>" ;
				print _("The specified record cannot be found.") ;
			print "</div>" ;
		}
		else {
			//Let's go!
			$row=$result->fetch() ;
			?>
			<form method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/house_manage_editProcess.php?gibbonHouseID=$gibbonHouseID" ?>">
				<table class='smallIntBorder' cellspacing='0' style="width: 100%">	
					<tr>
						<td style='width: 275px'> 
							<b><?php print _('Name') ?> *</b><br/>
							<span style="font-size: 90%"><i><?php print _('Must be unique.') ?></i></span>
						</td>
						<td class="right">
							<input name="name" id="name"  value="<?php print htmlPrep($row["name"]) ?>" type="text" style="width: 300px">
							<script type="text/javascript">
								var name2=new LiveValidation('name');
								name2.add(Validate.Presence);
							 </script> 
						</td>
					</tr>
					<tr>
						<td> 
							<b><?php print _('Short Name') ?> *</b><br/>
							<span style="font-size: 90%"><i><?php print _('Must be unique.') ?></i></span>
						</td>
						<td class="right">
							<input name="nameShort" id="nameShort"  value="<?php print htmlPrep($row["nameShort"]) ?>" type="text" style="width: 300px">
							<script type="text/javascript">
								var nameShort=new LiveValidation('nameShort');
								nameShort.add(Validate.Presence);
							 </script> 
						</td>
					</tr>
					<tr>
				<td> 
					<b><?php print _('House Incharge') ?> *</b><br/>
					
				</td>
				<td class="right">
					
					<select name="stuff_personid" id="stuff_personid">
					<option> -select-</option>
					<?php foreach ($stuff as $value) { ?>
					<option value="<?php echo $value['gibbonPersonID'];?>" <?php if($row['stuff_personid']==$value['gibbonPersonID']){?> selected="selected"<?php } ?>><?php echo $value['officialName']?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td> 
					<b><?php print _('House Captain') ?> *</b><br/>
					
				</td>
				<td class="right">
					<select name="student_personid" id="student_personid">
					<option>-select-</option>
					<?php foreach ($students as $value) { ?>
					<option value="<?php echo $value['gibbonPersonID']?>" <?php if($row['student_personid']==$value['gibbonPersonID']){?> selected="selected"<?php } ?>><?php echo $value['officialName']?></option>
						<?php } ?>
					</select>
					
				</td>
			</tr>
					<tr>
						<td>
							<span style="font-size: 90%"><i>* <?php print _("denotes a required field") ; ?></i></span>
						</td>
						<td class="right">
							<input type="hidden" name="address" value="<?php print $_SESSION[$guid]["address"] ?>">
							<input type="submit" value="<?php print _("Submit") ; ?>">
						</td>
					</tr>
				</table>
			</form>
			<?php
		}
	}
}
?>