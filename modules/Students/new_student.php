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
if (isActionAccessible($guid, $connection2, "/modules/Students/new_student.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {

	$sql="SELECT `gibbonYearGroupID`,`name` FROM `gibbonyeargroup`";
	$result=$connection2->prepare($sql);
	$result->execute();
	$classDB=$result->fetchAll();
	$sql1="SELECT `gibbonSchoolYearID`,`name`,`status` FROM `gibbonschoolyear`";
	$result1=$connection2->prepare($sql1);
	$result1->execute();
	//echo "HULS";
	$yearDB=$result1->fetchAll();
	if(isset($_REQUEST['fail'])){
		echo "<div class='error'>";
		echo "Failed to add student. Please contact administrator.";
		echo "</div>";
	}
	else if(isset($_REQUEST['success'])){
		echo "<div class='success'>";
		echo "Student added successfully.Add another entry.";
		echo "</div>";		
	}
?>
<form method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/process_new_student.php" ?>" enctype="multipart/form-data" class="studentappform">
		<table class='smallIntBorder' cellspacing='0' style="width: 100%">	 
			<tr class='break'>
				<td colspan=2> 
					<h3><?php print _('Student') ?></h3>
				</td>
			</tr>
			<tr>
				<td> 
					<b><?php print _('First Name') ?> *</b><br/>
				</td>
				<td class="right">
					<input name="firstName" id="firstName" maxlength=30 value="" type="text" style="width: 300px">
					<script type="text/javascript">
						var firstName=new LiveValidation('firstName');
						firstName.add(Validate.Presence);
					 </script>
				</td>
			</tr>
			<tr>
				<td style='width: 275px'> 
					<b><?php print _('Surname') ?> *</b><br/>
				</td>
				<td class="right">
					<input name="surname" id="surname" maxlength=30 value="" type="text" style="width: 300px">
					<script type="text/javascript">
						var surname=new LiveValidation('surname');
						surname.add(Validate.Presence);
					 </script>
				</td>
			</tr>
			<tr>
				<td> 
					<b><?php print _('Gender') ?> *</b><br/>
				</td>
				<td class="right">
					<select name="gender" id="gender" style="width: 302px">
						<option value="Please select..."><?php print _('Please select...') ?></option>
						<option value="F"><?php print _('Female') ?></option>
						<option value="M"><?php print _('Male') ?></option>
					</select>
					<script type="text/javascript">
						var gender=new LiveValidation('gender');
						gender.add(Validate.Exclusion, { within: ['Please select...'], failureMessage: "<?php print _('Select something!') ?>"});
					 </script>
				</td>
			</tr>
							<tr>
					<td> 
						<b><?php print _('Home Address') ?> *</b><br/>
					</td>
					<td class="right">
						<textarea name="homeAddress" id="homeAddress" rows=6 maxlength=255 value="" type="text" style="width: 300px"></textarea>
						<script type="text/javascript">
							var homeAddress=new LiveValidation('homeAddress');
							homeAddress.add(Validate.Presence);
						 </script>
					</td>
				</tr>
				<tr>
					<td> 
						<b><?php print _('Home Address (State)') ?> *</b><br/>
					</td>
					<td class="right">
						<input name="homeAddressDistrict" id="homeAddressDistrict" maxlength=30 value="West Bengal" type="text" style="width: 300px">
					</td>
					<script type="text/javascript">
						$(function() {
							var availableTags=[
								<?php
								try {
									$dataAuto=array(); 
									$sqlAuto="SELECT DISTINCT name FROM gibbondistrict ORDER BY name" ;
									$resultAuto=$connection2->prepare($sqlAuto);
									$resultAuto->execute($dataAuto);
								}
								catch(PDOException $e) { }
								while ($rowAuto=$resultAuto->fetch()) {
									print "\"" . $rowAuto["name"] . "\", " ;
								}
								?>
							];
							$( "#homeAddressDistrict" ).autocomplete({source: availableTags});
						});
					</script>
					<script type="text/javascript">
						var homeAddressDistrict=new LiveValidation('homeAddressDistrict');
						homeAddressDistrict.add(Validate.Presence);
					 </script>
				</tr>
				<tr>
					<td> 
						<b><?php print _('Home Address (Country)') ?> *</b><br/>
					</td>
					<td class="right">
						<select name="homeAddressCountry" id="homeAddressCountry" style="width: 302px">
							<?php
							try {
								$dataSelect=array(); 
								$sqlSelect="SELECT printable_name FROM gibboncountry where iddCountryCode!=91 ORDER BY printable_name" ;
								$resultSelect=$connection2->prepare($sqlSelect);
								$resultSelect->execute($dataSelect);
							}
							catch(PDOException $e) { }
							print "<option value='India'>India</option>" ;
							while ($rowSelect=$resultSelect->fetch()) {
								print "<option value='" . $rowSelect["printable_name"] . "'>" . htmlPrep(_($rowSelect["printable_name"])) . "</option>" ;
							}
							?>				
						</select>
						<script type="text/javascript">
							var homeAddressCountry=new LiveValidation('homeAddressCountry');
							homeAddressCountry.add(Validate.Exclusion, { within: ['Please select...'], failureMessage: "<?php print _('Select something!') ?>"});
						 </script>
					</td>
				</tr>
			<tr>
				<td> 
					<b><?php print _('Email') ?></b><br/>
				</td>
				<td class="right">
					<input name="email" id="email" maxlength=50 value="" type="text" style="width: 300px">
					<script type="text/javascript">
						var email=new LiveValidation('email');
						email.add(Validate.Email);
					</script>
				</td>
			</tr>
<?php							print "<tr class='break'>" ;
					print "<td colspan=2>" ;
						print "<h3>";
						print _('Father\'s Details');
						print "</h3>";
					print "</td>";
				print "</tr>";
				print "<tr>";
					print "<td>";
						print _("<b>Official Name *</b>");
					print "</td>";
					print "<td class='right'>";
						print ("<input type='text' name='officialName1' id='officialName1' value='".$adults[0]["officialName"]."' style='width:300px'>");
					print "</td>";
					?>
					<script type="text/javascript">
						var officialName1=new LiveValidation('officialName1');
						officialName1.add(Validate.Presence);
					 </script>
				<?php
				print "</tr>";
								print "<tr>";
					print "<td>";
						print _("<b>Profession </b>");
					print "</td>";
					print "<td class='right'>";
						print ("<input type='text' name='profession1' id='profession1' value='".$adults[0]["profession"]."' style='width:300px'>");
					print "</td>";
									?>
					<script type="text/javascript">
						var profession1=new LiveValidation('profession1');
						profession1.add(Validate.Presence);
					 </script>
				<?php
				print "</tr>";
				print "<tr>";
					print "<td>";
						print _("<b>Phone1</b>");
						print _("<br><i>Type,Country Code, Number</i>");
					print "</td>";
					print "<td class='right'>";
						print ("<input type='text' name='phone11' maxlength=10 id='phone11' value='".$adults[0]["phone1"]."' style='width:160px'>");
						print ("<select name='phone1CountryCode1' id='phone1CountryCode1' style='width:60px'");
							print "<option value=''></option>" ;
							try {
								$dataSelect=array(); 
								$sqlSelect="SELECT * FROM gibboncountry ORDER BY printable_name" ;
								$resultSelect=$connection2->prepare($sqlSelect);
								$resultSelect->execute($dataSelect);
							}
							catch(PDOException $e) { }
							while ($rowSelect=$resultSelect->fetch()) {
								$selected="" ;
								if ($rowSelect["iddCountryCode"]=='91') {
									$selected="selected" ;
								}	
								print "<option $selected value='" . $rowSelect["iddCountryCode"] . "'>" . htmlPrep($rowSelect["iddCountryCode"]) . " - " .  htmlPrep(_($rowSelect["printable_name"])) . "</option>" ;
							}				
							print "</select>";?>
							<select style='width: 70px' name='phone1Type1' id='Phone1Type1'>
								<option  value=""></option>
								<option  selected value="Mobile"><?php print _('Mobile') ?></option>
								<option  value="Home"><?php print _('Home') ?></option>
								<option  value="Work"><?php print _('Work') ?></option>
								<option  value="Fax"><?php print _('Fax') ?></option>
								<option  value="Pager"><?php print _('Pager') ?></option>
								<option  value="Other"><?php print _('Other') ?></option>
							</select>
<?php 				print "</td>";
				print "</tr>";
				print "<tr>";
					print "<td>";
						print _("<b>Phone2 </b>");
						print _("<br><i>Type,Country Code, Number</i>");
					print "</td>";
					print "<td class='right'>";
						print ("<input type='text' name='phone21' maxlength=10 id='phone21' value='".$adults[0]["phone2"]."' style='width:160px'>");
						print ("<select name='phone2CountryCode1' id='phone2CountryCode1' style='width:60px'");
							print "<option value=''></option>" ;
							try {
								$dataSelect=array(); 
								$sqlSelect="SELECT * FROM gibboncountry ORDER BY printable_name" ;
								$resultSelect=$connection2->prepare($sqlSelect);
								$resultSelect->execute($dataSelect);
							}
							catch(PDOException $e) { }
							while ($rowSelect=$resultSelect->fetch()) {
								$selected="" ;
								if ($rowSelect["iddCountryCode"]=='91') {
									$selected="selected" ;
								}	
								print "<option $selected value='" . $rowSelect["iddCountryCode"] . "'>" . htmlPrep($rowSelect["iddCountryCode"]) . " - " .  htmlPrep(_($rowSelect["printable_name"])) . "</option>" ;
							}				
							print "</select>";?>
							<select style='width: 70px' name='phone2Type1' id='phone2Type1'>
								<option  value=""></option>
								<option  value="Mobile"><?php print _('Mobile') ?></option>
								<option selected value="Home"><?php print _('Home') ?></option>
								<option  value="Work"><?php print _('Work') ?></option>
								<option  value="Fax"><?php print _('Fax') ?></option>
								<option  value="Pager"><?php print _('Pager') ?></option>
								<option  value="Other"><?php print _('Other') ?></option>
							</select>
							</select>
<?php 				print "</td>";
				print "</tr>";?>
				<tr>
				<td> 
					<b><?php print _('Email') ?></b><br/>
				</td>
				<td class="right">
					<input name="email1" id="email1" maxlength=50 value="" type="text" style="width: 300px">
					<script type="text/javascript">
						var email=new LiveValidation('email');
						email.add(Validate.Email);
					</script>
				</td>
				</tr>
<?php				print "<tr>";
					print "<td>";
						print _("<b>Contact Priority</b>");
					print "</td>";
					print "<td class='right'>";?>
						<input type='radio' name='contactPriority1' value='1' checked><span style='font-size:15px;margin:10px'>First</span>
						<input type='radio' name='contactPriority1' value='2'><span style='font-size:15px;margin:10px'>Second</span>
						<input type='hidden' name='relationship1' value='Father'>
<?php				print "</td>";
				print "</tr>";
				print "<tr class='break'>" ;
					print "<td colspan=2>" ; 
						print "<h3>";
						print _('Mother\'s Details');
						print "</h3>";
					print "</td>";
				print "</tr>";
				print "<tr>";
					print "<td>";
						print _("<b>Official Name *</b>");
					print "</td>";
					print "<td class='right'>";
						print ("<input type='text' name='officialName2' id='officialName2' value='".$adults[1]["officialName"]."' style='width:300px' required>");
					print "</td>";
				print "</tr>";
				
				print "<tr>";
					print "<td>";
						print _("<b>Profession </b>");
					print "</td>";
					print "<td class='right'>";
						print ("<input type='text' name='profession2' id='profession2' value='".$adults[1]["profession"]."' style='width:300px'>");
					print "</td>";
				print "</tr>";
				print "<tr>";
					print "<td>";
						print _("<b>Phone1 *</b>");
						print _("<br><i>Type,Country Code, Number</i>");
					print "</td>";
					print "<td class='right'>";
						print ("<input type='text' name='phone12' maxlength=10 id='phone12' value='".$adults[1]["phone1"]."' style='width:160px' required>");
						print ("<select name='phone1CountryCode2' id='phone1CountryCode2' style='width:60px'");
							print "<option value=''></option>" ;
							try {
								$dataSelect=array(); 
								$sqlSelect="SELECT * FROM gibboncountry ORDER BY printable_name" ;
								$resultSelect=$connection2->prepare($sqlSelect);
								$resultSelect->execute($dataSelect);
							}
							catch(PDOException $e) { }
							while ($rowSelect=$resultSelect->fetch()) {
								$selected="" ;
								if ($rowSelect["iddCountryCode"]=='91') {
									$selected="selected" ;
								}	
								print "<option $selected value='" . $rowSelect["iddCountryCode"] . "'>" . htmlPrep($rowSelect["iddCountryCode"]) . " - " .  htmlPrep(_($rowSelect["printable_name"])) . "</option>" ;
							}				
							print "</select>";?>
							<select style='width: 70px' name='phone1Type2' id='phone1Type2'>
								<option  value=""></option>
								<option  selected value="Mobile"><?php print _('Mobile') ?></option>
								<option  value="Home"><?php print _('Home') ?></option>
								<option  value="Work"><?php print _('Work') ?></option>
								<option  value="Fax"><?php print _('Fax') ?></option>
								<option  value="Pager"><?php print _('Pager') ?></option>
								<option  value="Other"><?php print _('Other') ?></option>
							</select>
							</select>
<?php 				print "</td>";
				print "</tr>";
				print "<tr>";
					print "<td>";
						print _("<b>Phone2 </b>");
						print _("<br><i>Type,Country Code, Number</i>");
					print "</td>";
					print "<td class='right'>";
						print ("<input type='text' name='phone22' maxlength=10 id='phone22' value='".$adults[1]["phone2"]."' style='width:160px'>");
						print ("<select name='phone2CountryCode2' id='phone2CountryCode2' style='width:60px'");
							print "<option value=''></option>" ;
							try {
								$dataSelect=array(); 
								$sqlSelect="SELECT * FROM gibboncountry ORDER BY printable_name" ;
								$resultSelect=$connection2->prepare($sqlSelect);
								$resultSelect->execute($dataSelect);
							}
							catch(PDOException $e) { }
							while ($rowSelect=$resultSelect->fetch()) {
								$selected="" ;
								if ($rowSelect["iddCountryCode"]=='91') {
									$selected="selected" ;
								}	
								print "<option $selected value='" . $rowSelect["iddCountryCode"] . "'>" . htmlPrep($rowSelect["iddCountryCode"]) . " - " .  htmlPrep(_($rowSelect["printable_name"])) . "</option>" ;
							}				
							print "</select>";?>
							<select style='width: 70px' name='phone2Type2' id='phone2Type2'>
								<option  value=""></option>
								<option  value="Mobile"><?php print _('Mobile') ?></option>
								<option selected value="Home"><?php print _('Home') ?></option>
								<option  value="Work"><?php print _('Work') ?></option>
								<option  value="Fax"><?php print _('Fax') ?></option>
								<option  value="Pager"><?php print _('Pager') ?></option>
								<option  value="Other"><?php print _('Other') ?></option>
							</select>
							</select>
<?php 				print "</td>";
				print "</tr>";
?>				<tr>
				<td> 
					<b><?php print _('Email') ?></b><br/>
				</td>
				<td class="right">
					<input name="email2" id="email2" maxlength=50 value="" type="text" style="width: 300px">
					<script type="text/javascript">
						var email=new LiveValidation('email');
						email.add(Validate.Email);
					</script>
				</td>
			</tr>
<?php
				print "<tr>";
					print "<td>";
						print _("<b>Contact Priority</b>");
					print "</td>";
					print "<td class='right'>";?>
						<input type='radio' name='contactPriority2' value='1'><span style='font-size:15px;margin:10px'>First</span>
						<input type='radio' name='contactPriority2' value='2' checked><span style='font-size:15px;margin:10px'>Second</span>
						<input type='hidden' name='relationship2' value='Mother'>
<?php				print "</td>";
				print "</tr>";
				print "<tr class='break'>" ;
					print "<td colspan=2>" ; 
						print "<h3>";
						print _('Guardian\'s Details');
						print "</h3>";
					print "</td>";
				print "</tr>";
				print "<tr>" ;
					print "<td colspan=2 class='left'>";
						if(count($adults)==3){?>
							<script>
								$(document).ready(function(){
									$('#officialName3').prop("required",true);
									$('#email3').prop("required",true);
									$('#phone13').prop("required",true);
								});
							</script>
<?php					}
						else{
						echo "<input type='checkbox' id='addGuardian' name='addGuardian' value='on'>";
						echo "<b> Add Guardian's Details</b>";?>
							<script>
								$(document).ready(function(){
									$('.guardian').hide();
								});
							</script>
<?php					}
					print "</td>";
				print "</tr>";
				print "<tr class='guardian'>";
					print "<td>";
						print _("<b>Official Name *</b>");
					print "</td>";
					print "<td class='right'>";
						print ("<input type='text' name='officialName3' id='officialName3' value='".$adults[2]["officialName"]."' style='width:300px' >");
					print "</td>";
				print "</tr>";
				print "<tr class='guardian'>";
					print "<td>";
						print _("<b>Profession </b>");
					print "</td>";
					print "<td class='right'>";
						print ("<input type='text' name='profession3' id='profession3' value='".$adults[2]["profession"]."' style='width:300px'>");
					print "</td>";
				print "</tr>";
				print "<tr class='guardian'>";
					print "<td>";
						print _("<b>Phone1 *</b>");
						print _("<br><i>Type,Country Code, Number</i>");
					print "</td>";
					print "<td class='right'>";
						print ("<input type='text' name='phone13' maxlength=10 id='phone13' value='".$adults[2]["phone1"]."' style='width:160px' >");
						print ("<select name='phone1CountryCode3' id='phone1CountryCode3' style='width:60px'");
							print "<option value=''></option>" ;
							try {
								$dataSelect=array(); 
								$sqlSelect="SELECT * FROM gibboncountry ORDER BY printable_name" ;
								$resultSelect=$connection2->prepare($sqlSelect);
								$resultSelect->execute($dataSelect);
							}
							catch(PDOException $e) { }
							while ($rowSelect=$resultSelect->fetch()) {
								$selected="" ;
								if ($rowSelect["iddCountryCode"]=='91') {
									$selected="selected" ;
								}
								print "<option $selected value='" . $rowSelect["iddCountryCode"] . "'>" . htmlPrep($rowSelect["iddCountryCode"]) . " - " .  htmlPrep(_($rowSelect["printable_name"])) . "</option>" ;
							}				
						print "</select>";?>
						<select style='width: 70px' name='phone1Type3' id='phone1Type3'>
								<option  value=""></option>
								<option selected value="Mobile"><?php print _('Mobile') ?></option>
								<option  value="Home"><?php print _('Home') ?></option>
								<option  value="Work"><?php print _('Work') ?></option>
								<option  value="Fax"><?php print _('Fax') ?></option>
								<option  value="Pager"><?php print _('Pager') ?></option>
								<option  value="Other"><?php print _('Other') ?></option>
						</select>
<?php				print "</td>";
				print "</tr>";
				print "<tr class='guardian'>";
					print "<td>";
						print _("<b>Phone2 </b>");
						print _("<br><i>Type,Country Code, Number</i>");
					print "</td>";
					print "<td class='right'>";
						print ("<input type='text' name='phone23' maxlength=10 id='phone23' value='".$adults[2]["phone2"]."' style='width:160px'>");
						print ("<select name='phone2CountryCode3' id='phone2CountryCode3' style='width:60px'");
							print "<option value=''></option>" ;
							try {
								$dataSelect=array(); 
								$sqlSelect="SELECT * FROM gibboncountry ORDER BY printable_name" ;
								$resultSelect=$connection2->prepare($sqlSelect);
								$resultSelect->execute($dataSelect);
							}
							catch(PDOException $e) { }
							while ($rowSelect=$resultSelect->fetch()) {
							$selected="" ;
								if ($adults[2]["phone1CountryCode"]==$rowSelect["iddCountryCode"]) {
									$selected="selected" ;
								}
								else{
									if($rowSelect["iddCountryCode"]=='91' && $selected=="")
										$selected="selected" ;
								}	
							print "<option $selected value='" . $rowSelect["iddCountryCode"] . "'>" . htmlPrep($rowSelect["iddCountryCode"]) . " - " .  htmlPrep(_($rowSelect["printable_name"])) . "</option>" ;
							}				
						print "</select>";?>
						<select style='width: 70px' name='phone2Type3' id='phone2Type3'>
								<option  value=""></option>
								<option  value="Mobile"><?php print _('Mobile') ?></option>
								<option selected value="Home"><?php print _('Home') ?></option>
								<option  value="Work"><?php print _('Work') ?></option>
								<option  value="Fax"><?php print _('Fax') ?></option>
								<option  value="Pager"><?php print _('Pager') ?></option>
								<option  value="Other"><?php print _('Other') ?></option>
						</select>
<?php				print "</td>";
				print "</tr>";
				print "<tr class='guardian' id='guardianRelationRow' style='display:none'>";
					print "<td>";
						print _("<b>Relationship</b>");
					print "</td>";
					print "<td class='right'>";?>
						<select name="relationship3" id='guardianRelation' style="width: 300px;">
							<option value=""></option>
							<option value="Step-Mother"><?php print _('Step-Mother') ?></option>
							<option value="Step-Father"><?php print _('Step-Father') ?></option>
							<option value="Adoptive Parent"><?php print _('Adoptive Parent') ?></option>
							<option value="Guardian"><?php print _('Guardian') ?></option>
							<option value="Grandmother"><?php print _('Grandmother') ?></option>
							<option value="Grandfather"><?php print _('Grandfather') ?></option>
							<option value="Aunt"><?php print _('Aunt') ?></option>
							<option value="Uncle"><?php print _('Uncle') ?></option>
							<option value="Nanny/Helper"><?php print _('Nanny/Helper') ?></option>
							<option value="Other"><?php print _('Other') ?></option>
						</select>
<?php					print "</td>";
				print "</tr>";
				print "<tr class='guardian'>";
					print "<td>";
						print _("<b>Contact Priority</b>");
					print "</td>";
					print "<td class='right'>";?>
						<input type='radio' name='contactPriority3' value='1' <?php if($adults[2]["contactPriority"]==1){echo "checked";}?>><span style='font-size:15px;margin:10px'>First</span>
						<input type='radio' name='contactPriority3' value='2' <?php if($adults[2]["contactPriority"]==2){echo "checked";}?>><span style='font-size:15px;margin:10px'>Second</span>
<?php				print "</td>";
				print "</tr>";
			?>

			<tr class="break">
				<td colspan="2'">
					<h3>Other details</h3>
				</td>
			</tr>
			<tr>
				<td> 
					<b><?php print _('Date of Birth') ?> *</b><br/>
					<span style="font-size: 90%"><i><?php print _('Format:') . " " . $_SESSION[$guid]["i18n"]["dateFormat"]  ?></i></span>
				</td>
				<td class="right">
					<input name="dob" id="dob" maxlength=10 value="" type="text" style="width: 300px">
					<script type="text/javascript">
						var dob=new LiveValidation('dob');
						dob.add( Validate.Format, {pattern: <?php if ($_SESSION[$guid]["i18n"]["dateFormatRegEx"]=="") {  print "/^(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)\d\d$/i" ; } else { print $_SESSION[$guid]["i18n"]["dateFormatRegEx"] ; } ?>, failureMessage: "Use <?php if ($_SESSION[$guid]["i18n"]["dateFormat"]=="") { print "dd/mm/yyyy" ; } else { print $_SESSION[$guid]["i18n"]["dateFormat"] ; }?>." } ); 
					 	dob.add(Validate.Presence);
					 </script>
					 <script type="text/javascript">
						$(function() {
							$( "#dob" ).datepicker({dateFormat : 'dd/mm/yy'});
						});
					</script>
				</td>
			</tr>
			<tr>
				<td> 
					<?php
						print "<b>" . _('Category') . "</b><br/>" ;
					?>
				</td>
				<td class="right">
					<select name="category" id="category" style="width: 300px">
						<option value='Gen'>General</option>
						<option value='OBC'>OBC</option>
						<option value='SC'>SC</option>
						<option value='ST'>ST</option>
					</select>
				</td>
			</tr>
			<tr>
				<td> 
					<b><?php print _('Nationality') ?></b><br/>
				</td>
				<td class="right">
					<select name="countryOfBirth" id="countryOfBirth" style="width: 302px">
						<?php
						try {
							$dataSelect=array(); 
							$sqlSelect="SELECT printable_name FROM gibboncountry where iddCountryCode!=91 ORDER BY printable_name" ;
							$resultSelect=$connection2->prepare($sqlSelect);
							$resultSelect->execute($dataSelect);
						}
						catch(PDOException $e) { }
						print "<option value='India'>India</option>" ;
						while ($rowSelect=$resultSelect->fetch()) {
							print "<option value='" . $rowSelect["printable_name"] . "'>" . htmlPrep(_($rowSelect["printable_name"])) . "</option>" ;
						}
						?>				
					</select>
				</td>
			</tr>
					<tr>
			<td> 
					<b><?php print _('Religion') ?></b><br/>
			</td>
			<td class="right">
				<select name='religion' id='religion'>
					<option value='Hindu' <?php if($data['religion']=='Hindu'){echo "selected";}?>>Hindu</option>
					<option value='Muslim' <?php if($data['religion']=='Muslim'){echo "selected";}?>>Muslim</option>
					<option value='Sikh' <?php if($data['religion']=='Sikh'){echo "selected";}?>>Sikh</option>
					<option value='Christian' <?php if($data['religion']=='Christian'){echo "selected";}?>>Christian</option>
					<option value='Jain' <?php if($data['religion']=='Jain'){echo "selected";}?>>Jain</option>
					<option value='Buddhist' <?php if($data['religion']=='Buddhist'){echo "selected";}?>>Buddhist</option>
					<option value='Others' <?php if($data['religion']=='Others'){echo "selected";}?>>Others</option>
				</select>
			</td>
		</tr>
		<tr>
				<td> 
					<b><?php print _('Aadhar Card No.') ?> </b><br/>
				</td>
				<td class="right">
					<input name="nationalIDCardNumber" id="nationalIDCardNumber" maxlength=30 value="<?php echo $data["nationalIDCardNumber"];?>" type="text" style="width: 300px">
				</td>
		</tr>
				<tr>
				<td> 
					<b><?php print _('Blood Type') ?> </b><br/>
				</td>
				<td class="right">
				<select name='bloodType' id='bloodType'>
					<option value=''>NA</option>
					<option value='A+' <?php if($data['bloodType']=='A+'){echo "selected";}?>>A+</option>
					<option value='B+' <?php if($data['bloodType']=='B+'){echo "selected";}?>>B+</option>
					<option value='O+' <?php if($data['bloodType']=='O+'){echo "selected";}?>>O+</option>
					<option value='AB+' <?php if($data['bloodType']=='AB+'){echo "selected";}?>>AB+</option>
					<option value='A-' <?php if($data['bloodType']=='A-'){echo "selected";}?>>A-</option>
					<option value='B-' <?php if($data['bloodType']=='B-'){echo "selected";}?>>B-</option>
					<option value='O-' <?php if($data['bloodType']=='O-'){echo "selected";}?>>O-</option>
					<option value='AB-' <?php if($data['bloodType']=='AB-'){echo "selected";}?>>AB-</option>
				</select>
				</td>
		</tr>
			<tr>
				<td> 
					<b><?php print _('Previous School') ?> </b><br/>
				</td>
				<td class="right">
					<input name="lastSchool" id="lastSchool" maxlength=30 value="" type="text" style="width: 300px">
				</td>
			</tr>
			<tr>
				<td> 
					<b><?php print _('Annual Income (Per Annum)') ?> </b><br/>
				</td>
				<td class="right">
					<input name="annual_income" id="annual_income" maxlength=30 value="" type="text" style="width: 300px">
				</td>
			</tr>
			<tr>
				<td> 
					<b><?php print _('Mother Tongue') ?> *</b><br/>
				</td>
				<td class="right">
					<input name="languageFirst" id="languageFirst" maxlength=30 value="" type="text" style="width: 300px">
					<script type="text/javascript">
						var languageFirst=new LiveValidation('languageFirst');
						languageFirst.add(Validate.Presence);
					</script>
				</td>
			</tr>
			<tr>
				<td> 
					<b><?php print _('Second Language') ?> *</b><br/>
				</td>
				<td class="right">
					<input name="languageSecond" id="languageSecond" maxlength=30 value="" type="text" style="width: 300px">
					<script type="text/javascript">
						var languageSecond=new LiveValidation('languageSecond');
						languageSecond.add(Validate.Presence);
					</script>
				</td>
			</tr>
				<!---		<tr>
				<td> 
					<b><?php print _('Avail Transport?') ?></b><br/>
				</td>
				<td class="right">
					<select name="avail_transport" id="avail_transport" style="width: 300px">
					<option value="Y">Yes</option>
					<option value="N" selected="selected">No</option>
					</select>
				</td>
			</tr>--->
			<tr class="break">
				<td colspan="2'">
					<h3>Enrolment details</h3>
				</td>
			</tr>
			<tr>
				<td> 
					<b><?php print _('Date of Admission') ?> </b><br/>
				</td>
				<td class="right">
					<input name="dateStart" id="dateStart" maxlength=30 value="" type="text" style="width: 300px">
				</td>
			</tr>
			<script type="text/javascript">
			$(function() {
				$( "#dateStart" ).datepicker({dateFormat : 'dd/mm/yy'});
			});
			</script>
			<tr>
				<td> 
					<b><?php print _('Admission No.') ?> *</b><br/>
					<span>(Format : XXX/XXXX)</span>
				</td>
				<td class="right">
					<input name="admission_number" id="admission_number" maxlength=30 value="" pattern="[0-9]{3}/[0-9]{4}" type="text" style="width: 300px" title="XXX/XXXX">
					<script type="text/javascript">
						var admission_number=new LiveValidation('admission_number');
						admission_number.add(Validate.Presence);
					</script>
				</td>
			</tr>
						<tr>
				<td> 
					<b><?php print _('Account No.') ?> *</b><br/>
				</td>
				<td class="right">
					<input name="account_number" id="account_number" maxlength=30 value="" type="text" style="width: 300px"><br>
					<span id="account_number_error" style="color: red;display: none;"></span>
					<span id="account_number_correct" style="display: none;color: green;"></span>
					<script type="text/javascript">
						var account_number=new LiveValidation('account_number');
						account_number.add(Validate.Presence);
					</script>
				</td>
			</tr>
						<tr>
										<td> 
					<b><?php print _('Year of Admission') ?></b><br/>
				</td>
						<td><select name='gibbonSchoolYearID' id='schoolYear'>
								<?php
								foreach($yearDB as $sc){
								$selected="";
								if($sc['status']=="Upcoming"){
									$selected="selected";
								}
								else if($sc['status']=="Current"){
									$selected="selected";
								}
								echo "<option value='{$sc['gibbonSchoolYearID']}' $selected>{$sc['name']}</option>";
								}
								?>
							</select>
						</td>
						<script type="text/javascript">
						var gibbonSchoolYearID=new LiveValidation('gibbonSchoolYearID');
						gibbonSchoolYearID.add(Validate.Presence);
						</script>
			</tr>
			<tr>
							<td> 
					<b><?php print _('Class in which admision is granted') ?> *</b><br/>
				</td>
						<td>
						<select name='filterClass' id='filterClass'>
								<option value=''>Select Class</option>
								<?php
								foreach($classDB as $c){
								echo "<option value='{$c['gibbonYearGroupID']}' $s>{$c['name']}</option>";
								}
								?>
							</select>
						</td>
						<script type="text/javascript">
						var filterClass=new LiveValidation('filterClass');
						filterClass.add(Validate.Presence);
						</script>
						</tr>
						<tr>
										<td> 
					<b><?php print _('Section') ?></b><br/>
				</td>
						<td><select name='filterSection' id='filterSection'>
								<?php
								if(isset($sectionDB) && !empty($sectionDB)){
								foreach($sectionDB as $sc){
								echo "<option value='{$sc['gibbonRollGroupID']}' $s>{$sc['name']}</option>";
								}
								}
								?>
							</select>
						</td>
			</tr>
						<tr>
				<td> 
					<b><?php print _('Roll No.') ?> </b><br/>
				</td>
				<td class="right">
					<input name="rollOrder" id="rollOrder" maxlength=30 value="" type="text" style="width: 300px">
				</td>
			</tr>
								<tr class='break'>
						<td colspan=2> 
							<h3><?php print _('Documents & Image') ?></h3>
						</td>
					</tr>
								<tr>
						<td> 
							<b>Medium Portrait</b><br/>
							<span style="font-size: 90%"><i><?php print _('240px by 320px') ?><br/>
							<?php if ($row["image_240"]!="") {
							 print _('Will overwrite existing attachment.');
							} ?>
							</i></span>
						</td>
						<td class="right">
							<?php
							if ($row["image_240"]!="") {
								print _("Current attachment:") . " <a target='_blank' href='" . $_SESSION[$guid]["absoluteURL"] . "/" . $row["image_240"] . "'>" . $row["image_240"] . "</a> <a href='" . $_SESSION[$guid]["absoluteURL"] . "/modules/User Admin/user_manage_edit_photoDeleteProcess.php?gibbonPersonID=$gibbonPersonID&size=240' onclick='return confirm(\"Are you sure you want to delete this record? Unsaved changes will be lost.\")'><img style='margin-bottom: -8px' id='image_75_delete' title='" . _('Delete') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/garbage.png'/></a><br/><br/>" ;
							}
							?>
							<input type="file" name="file1" id="file1"><br/><br/>
							<input type="hidden" name="attachment1" value='<?php print $row["image_240"] ?>'>
							<script type="text/javascript">
								var file1=new LiveValidation('file1');
								file1.add( Validate.Inclusion, { within: ['gif','jpg','jpeg','png'], failureMessage: "Illegal file type!", partialMatch: true, caseSensitive: false } );
							</script>
						</td>
					</tr>
					<?php for($i=1;$i<6;$i++) {?>
					<tr>
						<td> 
							<b>Document <?=$i?></b><br/>
							<input type='hidden' name='deleteUrl' id='deleteUrl' value='<?php echo $_SESSION[$guid]["absoluteURL"] . "/modules/Students/deleteStudentDocument.php"; ?>'>
							<span style="font-size: 90%"><i><br/>
							<?php
							$id=0; $label="";
								$id=$documents[$i-1]['documentsID'];
								$label=$documents[$i-1]['label'];
								print _("Current attachment:");
								echo "<a target='_blank' href='" .$documents[$i-1]['name']. "'> File: " .$documents[$i-1]['label']. "</a> ";
								echo  "<span id='$id' class='deleteDocx'><img  src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/garbage.png'/></span>" ;
								echo "<br>";
							 ?>
							</i></span>
							<input type='hidden' name="overwriteDOC<?=$i?>" value="<?=$id; ?>">
						</td>
						<td class="right">
						<br><input type="file" name="doc<?=$i?>" id="doc<?=$i?>"><br><br>
							<input type='text' name='note<?=$i?>' id='note<?=$i?>'style='width:400px;' value="<?=$label;?>"><br><b>Label:</b>
							
							
							
						<!--	<script type="text/javascript">
								var file1=new LiveValidation('doc<?=$i?>');
								file1.add( Validate.Inclusion, { within: ['gif','jpg','jpeg','png'], failureMessage: "Illegal file type!", partialMatch: true, caseSensitive: false } );
							</script>-->
						</td>
					</tr>
					<?php } ?>
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
<input type="hidden" name="rollgroup_url" id="rollgroup_url" value="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajax_change_rollgroup.php" ?>">
<input type="hidden" name="check_accountno_url" id="check_accountno_url" value="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajax_check_unique_account_number.php" ?>">
<script>
$(document).ready(function(){
$("#addGuardian").click(function(){
		if($(this).is(":checked")){
			$(".guardian").show();
			$('#guardianRelationRow').show();
			$('#guardianRelation').prop("required",true);
			$('#officialName3').prop("required",true);
			$('#email3').prop("required",true);
			$('#phone13').prop("required",true);			
		}
		else{
			$(".guardian").hide();
			$('#guardianRelationRow').hide();
			$('#guardianRelation').prop("required",false);
			$('#officialName3').prop("required",false);
			$('#email3').prop("required",false);
			$('#phone13').prop("required",false);	
		}
	});
	$("input[name='contactPriority1']").change(function(){
		var contactpriority=$(this).val();
		if(contactpriority==1){
			$("input[name='contactPriority2'][value='1']").prop("checked",false);
			$("input[name='contactPriority2'][value='2']").prop("checked",true);
			$("input[name='contactPriority3'][value='1']").prop("checked",false);
			$("input[name='contactPriority3'][value='2']").prop("checked",true);
		}
		else{
			$("input[name='contactPriority2'][value='1']").prop("checked",true);
			$("input[name='contactPriority2'][value='2']").prop("checked",false);			
		}
	});
	$("input[name='contactPriority2']").change(function(){
		var contactpriority=$(this).val();
		if(contactpriority==1){
			$("input[name='contactPriority1'][value='1']").prop("checked",false);
			$("input[name='contactPriority1'][value='2']").prop("checked",true);
			$("input[name='contactPriority3'][value='1']").prop("checked",false);
			$("input[name='contactPriority3'][value='2']").prop("checked",true);
		}
		else{
			$("input[name='contactPriority1'][value='1']").prop("checked",true);
			$("input[name='contactPriority1'][value='2']").prop("checked",false);			
		}
	});	
	$("input[name='contactPriority3']").change(function(){
		var contactpriority=$(this).val();
		if(contactpriority==1){
			$("input[name='contactPriority1'][value='1']").prop("checked",false);
			$("input[name='contactPriority1'][value='2']").prop("checked",true);
			$("input[name='contactPriority2'][value='1']").prop("checked",false);
			$("input[name='contactPriority2'][value='2']").prop("checked",true);
		}
		else{
			$("input[name='contactPriority2'][value='1']").prop("checked",true);
			$("input[name='contactPriority2'][value='2']").prop("checked",false);			
		}
	});
	$("#account_number").blur(function(){
		var account_number=$(this).val();
		//var personid=$('.select_student_dropdown').val();
		
		
		var linkurl=$("#check_accountno_url").val();
		//ajax_check_unique_account_number.php
		$.ajax
 		({
 			type: "POST",
 			url: linkurl,
 			data: {accountno:account_number},
 			success: function(msg)
 			{
 				
 				if(msg>0)
 					{
	 					$("#account_number_error").html("This number already exist!");
	 					$("#account_number_error").show();
	 					$("#account_number_correct").hide();
 					}
 				else
 					{
	 					$("#account_number_error").hide();
	 					$("#account_number_correct").html("Valid Number");
	 					$("#account_number_correct").show();
 					}
 			}
 			});
	});
});
</script>

<?php
};
?>

