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
include "custom_funcions.php";
if (isActionAccessible($guid, $connection2, "/modules/User Admin/studentEnrolment_manage_add.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
	//Proceed!
	print "<div class='trail'>" ;
	print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . _("Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . _(getModuleName($_GET["q"])) . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/User Admin/studentEnrolment_manage.php&gibbonSchoolYearID=" . $_GET["gibbonSchoolYearID"] . "'>" . _('Student Enrolment') . "</a> > </div><div class='trailEnd'>" . _('Add Student Enrolment') . "</div>" ;
	print "</div>" ;
	
	if (isset($_GET["addReturn"])) { $addReturn=$_GET["addReturn"] ; } else { $addReturn="" ; }
	$addReturnMessage="" ;
	$class="error" ;
	if (!($addReturn=="")) {
		if ($addReturn=="fail0") {
			$addReturnMessage=_("Your request failed because you do not have access to this action.") ;	
		}
		else if ($addReturn=="fail2") {
			$addReturnMessage=_("Your request failed due to a database error.") ;	
		}
		else if ($addReturn=="fail3") {
			$addReturnMessage=_("Your request failed because your inputs were invalid.") ;	
		}
		else if ($addReturn=="fail4") {
			$addReturnMessage=_("Your request failed because your inputs were invalid.") ;	
		}
		else if ($addReturn=="fail5") {
			$addReturnMessage="Your request failed because your passwords did not match." ;	
		}
		else if ($addReturn=="fail6") {
			$addReturnMessage="Your request failed because your inputs were invalid." ;	
		}
		else if ($addReturn=="success0") {
			$addReturnMessage=_("Your request was completed successfully. You can now add another record if you wish.") ;	
			$class="success" ;
		}
		print "<div class='$class'>" ;
			print $addReturnMessage;
		print "</div>" ;
	} 
	
	//Check if school year specified
	$gibbonSchoolYearID=$_GET["gibbonSchoolYearID"] ;
	$search=$_GET["search"] ;
	if ($gibbonSchoolYearID=="") {
		print "<div class='error'>" ;
			print _("You have not specified one or more required parameters.") ;
		print "</div>" ;
	}
	else {
		if ($search!="") {
			print "<div class='linkTop'>" ;
				print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/User Admin/studentEnrolment_manage.php&gibbonSchoolYearID=$gibbonSchoolYearID&search=$search'>" . _('Back to Search Results') . "</a>" ;
			print "</div>" ;
		}
		?>
		<form method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/studentEnrolment_manage_addProcess.php?gibbonSchoolYearID=$gibbonSchoolYearID&search=$search" ?>">
			<table class='smallIntBorder' cellspacing='0' style="width: 100%">	
				<tr>
					<td style='width: 275px'> 
						<b><?php print _('School Year') ?> *</b><br/>
						<span style="font-size: 90%"><i><?php print _('This value cannot be changed.') ?></i></span>
					</td>
					<td class="right">
						<?php
						$yearName="" ;
						try {
							$dataYear=array("gibbonSchoolYearID"=>$gibbonSchoolYearID); 
							$sqlYear="SELECT * FROM gibbonschoolyear WHERE gibbonSchoolYearID=:gibbonSchoolYearID" ;
							$resultYear=$connection2->prepare($sqlYear);
							$resultYear->execute($dataYear);
						}
						catch(PDOException $e) { 
							print "<div class='error'>" . $e->getMessage() . "</div>" ; 
						}
						if ($resultYear->rowCount()==1) {
							$rowYear=$resultYear->fetch() ;
							$yearName=$rowYear["name"] ;
						}
						?>
						<input readonly name="yearName" id="yearName" maxlength=20 value="<?php print $yearName ?>" type="text" style="width: 300px">
						<script type="text/javascript">
							var yearName=new LiveValidation('yearName');
							//yearname2.add(Validate.Presence);
						</script>
					</td>
				</tr>
				<tr>
					<td> 
						<b><?php print _('Student') ?> *</b><br/>
						<span style="font-size: 90%"><i></i></span>
					</td>
					<td class="right">
						<select name="gibbonPersonID" id="gibbonPersonID" style="width: 302px" class="select_student_dropdown">
							<?php
			
							print "<option value=''>" . _('Please select...') . "</option>" ;
							try {
								$dataSelect=array(); 
								$sqlSelect="SELECT gibbonPersonID, preferredName, surname,firstName,officialName,gibbonyeargroup.name as class
								FROM gibbonperson
								LEFT JOIN gibbonyeargroup on gibbonyeargroup.gibbonYearGroupID=gibbonperson.gibbonYearGroupIDEntry
								WHERE gibbonperson.status='Full' AND gibbonRoleIDPrimary=003 AND gibbonPersonID NOT IN (SELECT gibbonPersonID FROM gibbonstudentenrolment WHERE 1) ORDER BY gibbonPersonID ASC" ;
								$resultSelect=$connection2->prepare($sqlSelect);
								$resultSelect->execute($dataSelect);
							}
							catch(PDOException $e) { }
							while ($rowSelect=$resultSelect->fetch()) {
								//print "<option value='" . $rowSelect["gibbonPersonID"] . "'>" . formatName("", htmlPrep($rowSelect["preferredName"]), htmlPrep($rowSelect["surname"]), "Student", true) . "</option>" ;
								print "<option value='" . $rowSelect["gibbonPersonID"] . "'>" . htmlPrep($rowSelect["officialName"])." - ".$rowSelect['class']." (".substr($rowSelect["gibbonPersonID"],-5).") </option>" ;
							}
							?>				
						</select>
						<script type="text/javascript">
							var gibbonPersonID=new LiveValidation('gibbonPersonID');
							gibbonPersonID.add(Validate.Exclusion, { within: ['Please select...'], failureMessage: "<?php print _('Select something!') ?>"});
						 </script>
						 
					</td>
				</tr>
				<tr>
					<td> 
						<b><?php print _('Class') ?> *</b><br/>
						<span style="font-size: 90%"></span>
					</td>
					<td class="right">
						<select name="gibbonYearGroupID" id="gibbonYearGroupID" style="width: 302px" class="select_year_id">
							<?php
							print "<option value='Please select...'>" . _('Please select...') . "</option>" ;
							try {
								$dataSelect=array(); 
								$sqlSelect="SELECT gibbonYearGroupID, name FROM gibbonyeargroup ORDER BY sequenceNumber" ;
								$resultSelect=$connection2->prepare($sqlSelect);
								$resultSelect->execute($dataSelect);
							}
							catch(PDOException $e) { }
							while ($rowSelect=$resultSelect->fetch()) {
								print "<option value='" . $rowSelect["gibbonYearGroupID"] . "'>" . htmlPrep(_($rowSelect["name"])) . "</option>" ;
							}
							?>				
						</select>
						<script type="text/javascript">
							var gibbonYearGroupID=new LiveValidation('gibbonYearGroupID');
							gibbonYearGroupID.add(Validate.Exclusion, { within: ['Please select...'], failureMessage: "<?php print _('Select something!') ?>"});
						 </script>
					</td>
				</tr>
	










	<tr>
					<td> 
						<b><?php print _('Section') ?> *</b><br/>
						<span style="font-size: 90%"></span>
					</td>
					<td class="right">
						<select name="gibbonRollGroupID" id="gibbonRollGroupID" style="width: 302px">
							<?php
							print "<option value='Please select...'>" . _('Please select...') . "</option>" ;
							try {
								$dataSelect=array("gibbonSchoolYearID"=>$gibbonSchoolYearID); 
								$sqlSelect="SELECT gibbonRollGroupID, name FROM gibbonrollgroup WHERE gibbonSchoolYearID=:gibbonSchoolYearID ORDER BY name" ;
								$resultSelect=$connection2->prepare($sqlSelect);
								$resultSelect->execute($dataSelect);
							}
							catch(PDOException $e) { }
							while ($rowSelect=$resultSelect->fetch()) {
								print "<option value='" . $rowSelect["gibbonRollGroupID"] . "'>" . htmlPrep($rowSelect["name"]) . "</option>" ;
							}
							?>				
						</select>
						<script type="text/javascript">
							var gibbonRollGroupID=new LiveValidation('gibbonRollGroupID');
							gibbonRollGroupID.add(Validate.Exclusion, { within: ['Please select...'], failureMessage: "<?php print _('Select something!') ?>"});
						 </script>
					</td>
				</tr>
				<tr>
					<td> 
						<b><?php print _('Roll Order') ?></b><br/>
						<span style="font-size: 90%"><i><?php print _('Must be unique to roll gorup if set.') ?></i></span>
					</td>
					<td class="right">
						<input name="rollOrder" id="rollOrder" maxlength=2 value="" type="text" style="width: 300px">
						<script type="text/javascript">
							var rollOrder=new LiveValidation('rollOrder');
							rollOrder.add(Validate.Numericality);
						</script>
					</td>
				</tr>
				<tr>
					<td> 
						<b><?php print _('Enrollment Date') ?></b><br/>
					</td>
					<td class="right">
						<input name="enrollment_date" id="enrollment_date"  value="" type="text" style="width: 300px">
						<script type="text/javascript">
								$(function() {
									$( "#enrollment_date" ).datepicker({ dateFormat: 'dd/mm/yy' }).datepicker("setDate","0");
								});
						</script>
					</td>
				</tr>
				<tr>
					<td> 
						<b><?php print _('Account Number') ?></b><br/>
						<span style="font-size: 90%"><i><?php print _('Must be unique.') ?></i></span>
					</td>
					<td class="right">
					<span id="account_number_error" style="color: red;display: none;"></span>
					<span id="account_number_correct" style="display: none;color: green;"></span>
						<input name="account_number" id="account_number"  value="" type="text" style="width: 300px">
						
					</td>
				</tr>
				<tr>
					<td> 
						<b><?php print _('Admission Number') ?></b><br/>
						<span style="font-size: 90%"><i><?php print _('Must be unique.') ?></i></span>
					</td>
					<td class="right">
						<input name="admission_number" id="admission_number"  value="" type="text" style="width: 300px">
						
					</td>
				</tr>
				<tr>
					<td>
						<span style="font-size: 90%"><i>* <?php print _("denotes a required field") ; ?></i></span>
					</td>
					<td class="right">
						<!--<input name="gibbonStudentEnrolmentID" id="gibbonStudentEnrolmentID" value="<?php //print $gibbonStudentEnrolmentID ?>" type="hidden">-->
						<input type="hidden" name="address" value="<?php print $_SESSION[$guid]["address"] ?>">
						<input type="submit" value="<?php print _("Submit") ; ?>">
					</td>
				</tr>
			</table>
		</form>
		<?php
	}
}
?>
<input type="hidden" name="check_student_class_url" id="check_student_class_url" value="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajax_get_student_class.php" ?>">
<input type="hidden" name="check_accountno_url" id="check_accountno_url" value="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajax_check_unique_account_number.php" ?>">
<input type="hidden" name="get_enrol_data" id="get_enrol_data" value="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajax_get_enrolment_data_temp.php" ?>">
<input type="hidden" name="changeRollGroupIDURL" id="changeRollGroupIDURL" value="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajax_change_rollgroup.php" ?>">
<input type="hidden" name="schoolYear" id="schoolYear" value="<?php echo $gibbonSchoolYearID?>">
<script>
$(document).ready(function(){
	//Temporary Section
	$('.select_student_dropdown').change(function(){
		var gibbonPersonID = $(this).val();
		var getDataURL = $('#get_enrol_data').val();
		$.ajax({
			type: "POST",
			url: getDataURL,
			data: {gibbonPersonID:gibbonPersonID},
			success : function(msg){
				var output = $.parseJSON(msg);
				$("#account_number").val(output['account_number']);
				$('#gibbonYearGroupID option[value="'+ output['gibbonYearGroupID'] +'"]').attr("selected","selected");
				$('#gibbonRollGroupID option[value="'+ output['gibbonRollGroupID'] +'"]').attr("selected","selected");
			}
		});
	});
	//Temporary Section
	$('#gibbonYearGroupID').change(function(){
		var yearGroup = $(this).val();
		var schoolYear = $('#schoolYear').val();
		var changeRollGroupIDURL = $('#changeRollGroupIDURL').val();
		$.ajax
		({
			type: "POST",
			url: changeRollGroupIDURL,
			data: {yearGroup:yearGroup,schoolYear:schoolYear},
			success: function(msg)
			{ 
				console.log(msg);
				$('#gibbonRollGroupID').empty().append(msg);
			}
		});
	});
});
</script>