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

if (isActionAccessible($guid, $connection2, "/modules/Students/student_edit.php")==FALSE) {
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
	$yearDB=$result1->fetchAll();
	$sql2="SELECT address1,address1District,address1Country,fatherName,fatherProfation,mothername,motherprofetion,phone1,phone2,`gibbonfamily`.`homeAddress`,`gibbonfamily`.`homeAddressDistrict`,`gibbonfamily`.`homeAddressCountry`,`gibbonfamilychild`.`gibbonFamilyID`,`gibbonstudentenrolment`.*,`firstName`,`surname`,`gender`,`email`,`dob`,`category`,`countryOfBirth`,`religion`,`nationalIDCardNumber`,`lastSchool`,`annual_income`,`languageFirst`,`languageSecond`,`account_number`,`admission_number`,`bloodType`,`dateStart`,`avail_transport` FROM `gibbonperson`
			LEFT JOIN `gibbonstudentenrolment` ON `gibbonstudentenrolment`.`gibbonPersonID`=`gibbonperson`.`gibbonPersonID`
			LEFT JOIN `gibbonpersonmedical` ON `gibbonpersonmedical`.`gibbonPersonID`=`gibbonperson`.`gibbonPersonID`
			LEFT JOIN `gibbonfamilychild` ON `gibbonfamilychild`.`gibbonPersonID`=`gibbonperson`.`gibbonPersonID`
			LEFT JOIN `gibbonfamily` ON `gibbonfamily`.`gibbonFamilyID`=`gibbonfamilychild`.`gibbonFamilyID`
			WHERE `gibbonperson`.`gibbonPersonID`=".$_GET["gibbonPersonID"]." AND `gibbonstudentenrolment`.`gibbonSchoolYearID`=".$_GET["gibbonSchoolYearID"];
	$result2=$connection2->prepare($sql2);
	$result2->execute();
	$data=$result2->fetch();
	//print_r($data);
	$sql="SELECT `gibbonRollGroupID`,`name` FROM `gibbonrollgroup` WHERE `gibbonYearGroupID`=".$data["gibbonYearGroupID"]." AND `gibbonSchoolYearID`=".$_GET["gibbonSchoolYearID"];
	$result=$connection2->prepare($sql);
	$result->execute();
	$sectionDB=$result->fetchAll();
				//Get relationships and prep array
			try {
				$dataRelationships=array("gibbonFamilyID"=>$data["gibbonFamilyID"]); 
				$sqlRelationships="SELECT * FROM gibbonfamilyrelationship WHERE gibbonFamilyID=:gibbonFamilyID" ; 
				$resultRelationships=$connection2->prepare($sqlRelationships);
				$resultRelationships->execute($dataRelationships);
			}
			catch(PDOException $e) { 
				print "<div class='error'>" . $e->getMessage() . "</div>" ; 
			}
			$relationships=array() ;
			$count=0 ;
			while ($rowRelationships=$resultRelationships->fetch()) {
				$relationships[$rowRelationships["gibbonFamilyAdultID"]][$rowRelationships["gibbonPersonID"]]=$rowRelationships["relationship"] ;
				$count++ ;
			}
				try {
				$dataAdults=array("gibbonFamilyID"=>$data["gibbonFamilyID"]); 
				$sqlAdults="SELECT * FROM gibbonfamilyadult WHERE gibbonFamilyID=:gibbonFamilyID ORDER BY gibbonFamilyAdultID" ; 
				$resultAdults=$connection2->prepare($sqlAdults);
				$resultAdults->execute($dataAdults);
			}
			catch(PDOException $e) { 
				print "<div class='error'>" . $e->getMessage() . "</div>" ; 
			}
			$adults=array() ;
			$count=0 ;
			while ($rowAdults=$resultAdults->fetch()) {
				if($relationships[$rowAdults["gibbonFamilyAdultID"]][$_GET["gibbonPersonID"]]=="Father"){
					$count=0;
				}
				else if($relationships[$rowAdults["gibbonFamilyAdultID"]][$_GET["gibbonPersonID"]]=="Mother"){
					$count=1;
				}
				else{
					$count=2;
				}
				//$adults[$count]["image_75"]=$rowAdults["image_75"] ;
				$adults[$count]["gibbonFamilyAdultID"]=$rowAdults["gibbonFamilyAdultID"] ;
				//$adults[$count]["title"]=$rowAdults["title"] ;
				$adults[$count]["officialName"]=$rowAdults["officialName"] ;
				
		        //$adults[$count]["officialName"]=$data["fatherName"] ;
				$adults[$count]["email"]=$rowAdults["email"] ;
				$adults[$count]["phone1Type"]=$rowAdults["phone1Type"] ;
				$adults[$count]["phone1CountryCode"]=$rowAdults["phone1CountryCode"] ;
				//$adults[$count]["phone1"]=$rowAdults["phone1"]==0?"":$rowAdults["phone1"] ;
				
				$adults[$count]["phone1"]=$data["phone1"] ;
				$adults[$count]["phone2Type"]=$rowAdults["phone2Type"] ;
				$adults[$count]["phone2CountryCode"]=$rowAdults["phone2CountryCode"] ;
				//$adults[$count]["phone2"]=$rowAdults["phone2"]==0?"":$rowAdults["phone2"] ;
				
				$adults[$count]["phone2"]=$data["phone2"] ;
				$adults[$count]["profession"]=$rowAdults["profession"] ;
				$adults[$count]["employer"]=$rowAdults["employer"] ;
				$adults[$count]["annual_income"]=$rowAdults["annual_income"]==0?"":$rowAdults["annual_income"] ;
				$adults[$count]["nationalIDCardNumber"]=$rowAdults["nationalIDCardNumber"] ;
				//$adults[$count]["surname"]=$rowAdults["surname"] ;
				//$adults[$count]["status"]=$rowAdults["status"] ;
				$adults[$count]["comment"]=$rowAdults["comment"] ;
				$adults[$count]["childDataAccess"]=$rowAdults["childDataAccess"] ;
				$adults[$count]["contactPriority"]=$rowAdults["contactPriority"] ;
				$adults[$count]["contactCall"]=$rowAdults["contactCall"] ;
				$adults[$count]["contactSMS"]=$rowAdults["contactSMS"] ;
				$adults[$count]["contactEmail"]=$rowAdults["contactEmail"] ;
				$adults[$count]["contactMail"]=$rowAdults["contactMail"] ;
				$adults[$count]["fathername"]=$data["fatherName"] ;
				$adults[$count]["fatherprofetion"]=$data["fatherProfation"] ;
				$adults[$count]["fathername"]=$data["fatherName"] ;
				$adults[$count]["mothername"]=$data["mothername"] ;
				$count++ ;
			}
	//print_r($data);
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
	//echo $data[0]["firstName"];
		$sql4="SELECT * FROM `studentdocuments` WHERE `gibbonPersonID`=".$_GET["gibbonPersonID"];
		$result4=$connection2->prepare($sql4);
		$result4->execute();
		$docx=$result4->fetchAll();
		$documents=array();
		foreach($docx as $d){
			$documents[]=$d;
		}
?>
<form method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/student_edit_process.php" ?>" enctype="multipart/form-data" class="studentappform">
		<input type='hidden' name='gibbonPersonID' value='<?php echo $_GET["gibbonPersonID"];?>'>
		<input type='hidden' name='gibbonFamilyID' value='<?php echo $data["gibbonFamilyID"];?>'>
		<input type='hidden' name='gibbonSchoolYearID' value='<?php echo $_GET["gibbonSchoolYearID"];?>'>
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
					<input name="firstName" id="firstName" maxlength=30 value="<?php echo $data["firstName"];?>" type="text" style="width: 300px">
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
					<input name="surname" id="surname" maxlength=30 value="<?php echo $data["surname"];?>" type="text" style="width: 300px">
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
						<option value="F" <?php if($data["gender"]=="F"){echo "selected='selected'";}?>><?php print _('Female') ?></option>
						<option value="M" <?php if($data["gender"]=="M"){echo "selected='selected'";}?>><?php print _('Male') ?></option>
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
						<textarea name="homeAddress" id="homeAddress" rows=6 maxlength=255 value="" type="text" style="width: 300px"><?php echo $data["address1"];?></textarea>
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
						<input name="homeAddressDistrict" id="homeAddressDistrict" maxlength=30 value="<?php echo $data["address1District"];?>" type="text" style="width: 300px">
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
					<input name="email" id="email" maxlength=50 value="<?php echo $data["email"];?>" type="text" style="width: 300px">
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
					//echo "<pre>";print_r($adults);
					print "<td class='right'>";
						print ("<input type='text' name='officialName1' id='officialName1' value='".$adults[1]["fathername"]."' style='width:300px'>");
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
					//fatherprofetion
						//print ("<input type='text' name='profession1' id='profession1' value='".$adults[0]["profession"]."' style='width:300px'>");
						print ("<input type='text' name='profession1' id='profession1' value='".$adults[1]["fatherprofetion"]."' style='width:300px'>");
						
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
						print ("<input type='text' name='phone11' maxlength=10 id='phone11' value='".$adults[1]["phone1"]."' style='width:160px'>");
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
							<input type='radio' name='contactPriority1' value='1'>
<?php 			print "</td>";
				print "</tr>";
			/*	print "<tr>";
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
							print "</select>";
							*/
							?>
						<!--	<select style='width: 70px' name='phone2Type1' id='phone2Type1'>
								<option  value=""></option>
								<option  value="Mobile"><?php print _('Mobile') ?></option>
								<option selected value="Home"><?php print _('Home') ?></option>
								<option  value="Work"><?php print _('Work') ?></option>
								<option  value="Fax"><?php print _('Fax') ?></option>
								<option  value="Pager"><?php print _('Pager') ?></option>
								<option  value="Other"><?php print _('Other') ?></option>
							</select>
							</select> -->
<?php 				//print "</td>";
				   //print "</tr>";
?>				<tr>
				<td> 
					<b><?php print _('Email') ?></b><br/>
				</td>
				<td class="right">
					<input name="email1" id="email1" maxlength=50 value="" type="text" style="width: 300px">
					<script type="text/javascript">
						var email=new LiveValidation('email');
						email.add(Validate.Email);
					</script>
					<input type='hidden' name='gibbonFamilyAdultID1' value='<?php echo $adults[0]["gibbonFamilyAdultID"];?>'>
				</td>
			</tr>
<?php
 /*                 print "<tr>";
					print "<td>";
					print _("<b>Contact Priority</b>");
					print "</td>";
					print "<td class='right'>";
*/
?>
<!--						<input type='radio' name='contactPriority1' value='1' <?php if($adults[0]["contactPriority"]==1){echo "checked";}?>><span style='font-size:15px;margin:10px'>First</span>
						<input type='radio' name='contactPriority1' value='2' <?php if($adults[0]["contactPriority"]==2){echo "checked";}?>><span style='font-size:15px;margin:10px'>Second</span>
						<input type='hidden' name='relationship1' value='Father'>
						<input type='hidden' name='gibbonFamilyAdultID1' value='<?php echo $adults[0]["gibbonFamilyAdultID"];?>'>
-->						
<?php			//print "</td>";
				//print "</tr>";
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
						print ("<input type='text' name='phone12' maxlength=10 id='phone12' value='".$adults[1]["phone2"]."' style='width:160px' required>");
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
							<input type='radio' name='contactPriority2' value='2'>
<?php 				print "</td>";
				print "</tr>";
			/*	print "<tr>";
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
							print "</select>";
							*/
							?>
							<!--<select style='width: 70px' name='phone2Type2' id='phone2Type2'>
								<option  value=""></option>
								<option  value="Mobile"><?php print _('Mobile') ?></option>
								<option selected value="Home"><?php print _('Home') ?></option>
								<option  value="Work"><?php print _('Work') ?></option>
								<option  value="Fax"><?php print _('Fax') ?></option>
								<option  value="Pager"><?php print _('Pager') ?></option>
								<option  value="Other"><?php print _('Other') ?></option>
							</select>
							</select>-->
<?php 				
               // print "</td>";
			//	print "</tr>";
?>

<tr>
				<td> 
					<b><?php print _('Email') ?></b><br/>
				</td>
				<td class="right">
					<input name="email2" id="email2" maxlength=50 value="" type="text" style="width: 300px">
					<script type="text/javascript">
						var email=new LiveValidation('email');
						email.add(Validate.Email);
					</script>
					<input type='hidden' name='gibbonFamilyAdultID2' value='<?php echo $adults[1]["gibbonFamilyAdultID"];?>'>
				</td>
				
			</tr>
<?php
/*				print "<tr>";
					print "<td>";
						print _("<b>Contact Priority</b>");
					print "</td>";
					print "<td class='right'>";
					
*/?>
<!--						<input type='radio' name='contactPriority2' value='1' <?php if($adults[1]["contactPriority"]==1){echo "checked";}?>><span style='font-size:15px;margin:10px'>First</span>
						<input type='radio' name='contactPriority2' value='2' <?php if($adults[1]["contactPriority"]==2){echo "checked";}?>><span style='font-size:15px;margin:10px'>Second</span>
						<input type='hidden' name='relationship2' value='Mother'>
						<input type='hidden' name='gibbonFamilyAdultID2' value='<?php echo $adults[1]["gibbonFamilyAdultID"];?>'>
-->
<?php			//print "</td>";
				//print "</tr>"; 
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
		/*		print "<tr class='guardian'>";
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
				print "</tr>"; */
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
						<input type='hidden' name='gibbonFamilyAdultID3' value='<?php echo $adults[2]["gibbonFamilyAdultID"];?>'>
<?php				
				print "</td>";
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
					<input name="dob" id="dob" maxlength=10 value="<?php echo dateformat($data["dob"]);?>" type="text" style="width: 300px">
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
						<option value='Gen' <?php if($data["category"]=="Gen"){echo "selected";}?>>General</option>
						<option value='OBC' <?php if($data["category"]=="OBC"){echo "selected";}?>>OBC</option>
						<option value='SC' <?php if($data["category"]=="SC"){echo "selected";}?>>SC</option>
						<option value='ST' <?php if($data["category"]=="ST"){echo "selected";}?>>ST</option>
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
							$selected="";
							if($rowSelect["printable_name"]==$data["countryOfBirth"]){
									$selected="selected";
							}
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
					<option></option>
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
					<input name="lastSchool" id="lastSchool" maxlength=30 value="<?php echo $data["lastSchool"];?>" type="text" style="width: 300px">
				</td>
			</tr>
			<tr>
				<td> 
					<b><?php print _('Annual Income') ?> </b><br/>
				</td>
				<td class="right">
					<input name="annual_income" id="annual_income" maxlength=30 value="<?php echo $data["annual_income"];?>" type="text" style="width: 300px">
				</td>
			</tr>
			<tr>
				<td> 
					<b><?php print _('Home Language') ?> *</b><br/>
				</td>
				<td class="right">
					<input name="languageFirst" id="languageFirst" maxlength=30 value="<?php echo $data["languageFirst"];?>" type="text" style="width: 300px">
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
					<input name="languageSecond" id="languageSecond" maxlength=30 value="<?php echo $data["languageSecond"];?>" type="text" style="width: 300px">
					<script type="text/javascript">
						var languageSecond=new LiveValidation('languageSecond');
						languageSecond.add(Validate.Presence);
					</script>
				</td>
			</tr>
			<!---<tr>
				<td> 
					<b><?php print _('Avail Transport?') ?> *</b><br/>
				</td>
				<td class="right">
					<select name="avail_transport" id="avail_transport" style="width: 300px">
					    <option value="Y" <?php if($data["avail_transport"]=='Y'){echo "selected ='selected'";}?> >Yes</option>
					    <option value="N" <?php if($data["avail_transport"]=='N'){echo "selected='selected'";}?> >No</option>
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
					<input name="dateStart" id="dateStart" maxlength=30 value="<?php if(isset($data["dateStart"])){echo dateformat($data["dateStart"]);}?>" type="text" style="width: 300px">
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
				</td>
				<td class="right">
					<input name="admission_number" id="admission_number" maxlength=30 value="<?php echo $data["admission_number"];?>" type="text" style="width: 300px">
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
					<input name="account_number" id="account_number" maxlength=30 value="<?php echo $data["account_number"];?>" type="text" style="width: 300px">
					<script type="text/javascript">
						var account_number=new LiveValidation('account_number');
						account_number.add(Validate.Presence);
					</script>
				</td>
			</tr>
			<tr>
				<td> 
					<b><?php print _('School Year') ?></b><br/>
				</td>
						<td><select name='gibbonSchoolYearID' disabled>
								<?php
								foreach($yearDB as $sc){
								$selected="";
								if($sc["gibbonSchoolYearID"]==$_GET["gibbonSchoolYearID"]){
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
					<b><?php print _('Class') ?> *</b><br/>
				</td>
						<td>
						<select name='filterClass' id='filterClass' disabled>
								<option value=''>Select Class</option>
								<?php
								foreach($classDB as $c){
								$s='';
								if($c["gibbonYearGroupID"]==$data["gibbonYearGroupID"]){
									$s="selected";
								}								
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
								<option value=''>Select Section</option>
								<?php
								if(isset($sectionDB) && !empty($sectionDB)){
								foreach($sectionDB as $sc){
									$s='';
								if($sc["gibbonRollGroupID"]==$data["gibbonRollGroupID"]){
									$s="selected";
								}
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
					<input name="rollOrder" id="rollOrder" maxlength=30 value="<?php echo $data["rollOrder"];?>" type="text" style="width: 300px">
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
							<?php if ($data["image_240"]!="") {
							 print _('Will overwrite existing attachment.');
							} ?>
							</i></span>
						</td>
						<td class="right">
							<?php
							if ($data["image_240"]!="") {
								print _("Current attachment:") . " <a target='_blank' href='" . $_SESSION[$guid]["absoluteURL"] . "/" . $data["image_240"] . "'>" . $data["image_240"] . "</a> <a href='" . $_SESSION[$guid]["absoluteURL"] . "/modules/User Admin/user_manage_edit_photoDeleteProcess.php?gibbonPersonID=$gibbonPersonID&size=240' onclick='return confirm(\"Are you sure you want to delete this record? Unsaved changes will be lost.\")'><img style='margin-bottom: -8px' id='image_75_delete' title='" . _('Delete') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/garbage.png'/></a><br/><br/>" ;
							}
							?>
							<input type="file" name="file1" id="file1"><br/><br/>
							<input type="hidden" name="attachment1" value='<?php print $data["image_240"] ?>'>
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
<input type="hidden" name="schoolYear" id="schoolYear" value="<?php print $_GET["gibbonSchoolYearID"]; ?>">
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
});
</script>

<?php
};
?>
<?php
function dateformat($a){
$date=explode("-",$a);
return $date[2]."/".$date[1]."/".$date[0];	
}
?>
