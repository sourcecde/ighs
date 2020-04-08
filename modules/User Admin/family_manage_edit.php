<?php
error_reporting(E_ALL ^ E_NOTICE);
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

if (isActionAccessible($guid, $connection2, "/modules/User Admin/family_manage_edit.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
	//Proceed!
	print "<div class='trail'>" ;
	print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . _("Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . _(getModuleName($_GET["q"])) . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/User Admin/family_manage.php'>" . _('Manage Families') . "</a> > </div><div class='trailEnd'>" . _('Edit Family') . "</div>" ;
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
	
	if (isset($_GET["addReturn"])) { $addReturn=$_GET["addReturn"] ; } else { $addReturn="" ; }
	$addReturnMessage="" ;
	$class="error" ;
	if (!($addReturn=="")) {
		if ($addReturn=="fail0") {
			$addReturnMessage=_("Your request failed because you do not have access to this action.") ;	
		}
		else if ($addReturn=="fail1") {
			$addReturnMessage=_("Your request failed because your inputs were invalid.") ;	
		}
		else if ($addReturn=="fail2") {
			$addReturnMessage=_("Your request failed due to a database error.") ;	
		}
		else if ($addReturn=="fail3") {
			$addReturnMessage=_("Your request failed because your inputs were invalid.") ;	
		}
		else if ($addReturn=="fail4") {
			$addReturnMessage=_("Your request failed because the person already exists as a member of this family.") ;	
		}
		else if ($addReturn=="success0") {
			$addReturnMessage=_("Your request was completed successfully.") ;	
			$class="success" ;
		}
		print "<div class='$class'>" ;
			print $addReturnMessage;
		print "</div>" ;
	} 
	
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
	
	//Check if school year specified
	$gibbonFamilyID=$_GET["gibbonFamilyID"] ;
	$search=NULL ;
	if (isset($_GET["search"])) {
		$search=$_GET["search"] ;
	}
	if ($gibbonFamilyID=="") {
		print "<h1>" ;
		print _("Edit Family") ;
		print "</h1>" ;
		print "<div class='error'>" ;
			print _("You have not specified one or more required parameters.") ;
		print "</div>" ;
	}
	else {
		try {
			$data=array("gibbonFamilyID"=>$gibbonFamilyID); 
			$sql="SELECT * FROM gibbonfamily WHERE gibbonFamilyID=:gibbonFamilyID" ;
			$result=$connection2->prepare($sql);
			$result->execute($data);
		}
		catch(PDOException $e) { 
			print "<div class='error'>" . $e->getMessage() . "</div>" ; 
		}
		
		if ($result->rowCount()!=1) {
			print "<h1>" ;
			print "Edit Family" ;
			print "</h1>" ;
			print "<div class='error'>" ;
				print _("The specified record cannot be found.") ;
			print "</div>" ;
		}
		else {
			//Let's go!
			$row=$result->fetch() ;
			
			if ($search!="") {
				print "<div class='linkTop'>" ;
					print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/User Admin/family_manage.php&search=$search'>" . _('Back to Search Results') . "</a>" ;
				print "</div>" ;
			}
			?>
			
			<form method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/family_manage_editProcess.php?gibbonFamilyID=$gibbonFamilyID&search=$search" ?>">
				<table class='smallIntBorder' cellspacing='0' style="width: 100%">	
					<tr class='break'>
						<td colspan=2> 
							<h3>
								<?php print _('General Information') ?>
							</h3>
						</td>
					</tr>
					<!---<tr>
						<td style='width: 275px'> 
							<b><?php print _('Family Name') ?> *</b><br/>
							<span style="font-size: 90%"><i></i></span>
						</td>
						<td class="right">
							<input name="name" id="name" maxlength=100 value="<?php print $row["name"] ?>" type="text" style="width: 300px">
						</td>
					</tr>
					<tr>
						<td> 
							<b><?php print _('Status') ?></b><br/>
						</td>
						<td class="right">
							<select name="status" id="status" style="width: 302px">
								<option <?php if ($row["status"]=="Married") { print "selected " ; } ?>value="Married"><?php print _('Married') ?></option>
								<option <?php if ($row["status"]=="Separated") { print "selected " ; } ?>value="Separated"><?php print _('Separated') ?></option>
								<option <?php if ($row["status"]=="Divorced") { print "selected " ; } ?>value="Divorced"><?php print _('Divorced') ?></option>
								<option <?php if ($row["status"]=="De Facto") { print "selected " ; } ?>value="De Facto"><?php print _('De Facto') ?></option>
								<option <?php if ($row["status"]=="Other") { print "selected " ; } ?>value="Other"><?php print _('Other') ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td> 
							<b><?php print _('Home Language') ?></b><br/>
						</td>
						<td class="right">
							<input name="languageHome" id="languageHome" maxlength=100 value="<?php print $row["languageHome"] ?>" type="text" style="width: 300px">
						</td>
					</tr>--->
					<tr>
						<td> 
							<b><?php print _('Address Name') ?> *</b><br/>
							<span style="font-size: 90%"><i><?php print _('Formal name to address parents with.') ?></i></span>
						</td>
						<td class="right">
							<input name="nameAddress" id="nameAddress" maxlength=100 value="<?php print $row["nameAddress"] ?>" type="text" style="width: 300px">
							<script type="text/javascript">
								var nameAddress=new LiveValidation('nameAddress');
								nameAddress.add(Validate.Presence);
							 </script>
						</td>
					</tr>
					<tr>
						<td> 
							<b><?php print _('Home Address') ?></b><br/>
							<span style="font-size: 90%"><i><?php print _('Unit, Building, Street') ?></i></span>
						</td>
						<td class="right">
							<textarea name="homeAddress" id="homeAddress" maxlength=255 rows="4" style="width: 300px"><?php print $row["homeAddress"] ?></textarea>
						</td>
					</tr>
					<tr>
						<td> 
							<b><?php print _('Home Address (District)') ?></b><br/>
							<span style="font-size: 90%"><i><?php print _('County, State, District') ?></i></span>
						</td>
						<td class="right">
							<input name="homeAddressDistrict" id="homeAddressDistrict" maxlength=30 value="<?php print $row["homeAddressDistrict"] ?>" type="text" style="width: 300px">
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
					</tr>
					<tr>
						<td> 
							<b><?php print _('Home Address (Country)') ?></b><br/>
						</td>
						<td class="right">
							<select name="homeAddressCountry" id="homeAddressCountry" style="width: 302px">
								<?php
								print "<option value=''></option>" ;
								try {
									$dataSelect=array(); 
									$sqlSelect="SELECT printable_name FROM gibboncountry ORDER BY printable_name" ;
									$resultSelect=$connection2->prepare($sqlSelect);
									$resultSelect->execute($dataSelect);
								}
								catch(PDOException $e) { }
								while ($rowSelect=$resultSelect->fetch()) {
									$selected="" ;
									if ($rowSelect["printable_name"]==$row["homeAddressCountry"]) {
										$selected=" selected" ;
									}
									print "<option $selected value='" . $rowSelect["printable_name"] . "'>" . htmlPrep(_($rowSelect["printable_name"])) . "</option>" ;
								}
								?>				
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
			//Get children and prep array
			try {
				$dataChildren=array("gibbonFamilyID"=>$gibbonFamilyID); 
				$sqlChildren="SELECT * FROM gibbonfamilychild JOIN gibbonperson ON (gibbonfamilychild.gibbonPersonID=gibbonperson.gibbonPersonID) WHERE gibbonFamilyID=:gibbonFamilyID ORDER BY surname, preferredName" ;
				$resultChildren=$connection2->prepare($sqlChildren);
				$resultChildren->execute($dataChildren);
			}
			catch(PDOException $e) { 
				print "<div class='error'>" . $e->getMessage() . "</div>" ; 
			}
			$children=array() ;
			$count=0 ;
			while ($rowChildren=$resultChildren->fetch()) {
				$children[$count]["image_75"]=$rowChildren["image_75"] ;
				$children[$count]["gibbonPersonID"]=$rowChildren["gibbonPersonID"] ;
				$children[$count]["preferredName"]=$rowChildren["preferredName"] ;
				$children[$count]["surname"]=$rowChildren["surname"] ;
				$children[$count]["status"]=$rowChildren["status"] ;
				$children[$count]["comment"]=$rowChildren["comment"] ;
				$count++ ;
			}
			//Get adults and prep array
			try {
				$dataAdults=array("gibbonFamilyID"=>$gibbonFamilyID); 
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
				//$adults[$count]["image_75"]=$rowAdults["image_75"] ;
				$adults[$count]["gibbonFamilyAdultID"]=$rowAdults["gibbonFamilyAdultID"] ;
				//$adults[$count]["title"]=$rowAdults["title"] ;
				$adults[$count]["officialName"]=$rowAdults["officialName"] ;
				$adults[$count]["email"]=$rowAdults["email"] ;
				$adults[$count]["phone1Type"]=$rowAdults["phone1Type"] ;
				$adults[$count]["phone1CountryCode"]=$rowAdults["phone1CountryCode"] ;
				$adults[$count]["phone1"]=$rowAdults["phone1"]==0?"":$rowAdults["phone1"] ;
				$adults[$count]["phone2Type"]=$rowAdults["phone2Type"] ;
				$adults[$count]["phone2CountryCode"]=$rowAdults["phone2CountryCode"] ;
				$adults[$count]["phone2"]=$rowAdults["phone2"]==0?"":$rowAdults["phone2"] ;
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
				$count++ ;
			}
			
			//Get relationships and prep array
			try {
				$dataRelationships=array("gibbonFamilyID"=>$gibbonFamilyID); 
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

			
			print "<h3>" ;
			print _("Relationships") ;
			print "</h3>" ;
			print "<p>" ;
			print _("Use the table below to show how each child is related to each adult in the family.") ;
			print "</p>" ;
			if ($resultChildren->rowCount()<1 OR $resultAdults->rowCount()<1) {
				print "<div class='error'>" . _('There are not enough people in this family to form relationships.') . "</div>" ; 
			}			
			else {
				print "<form method='post' action='" . $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/family_manage_edit_relationshipsProcess.php?gibbonFamilyID=$gibbonFamilyID&search=$search'>" ;
					print "<table cellspacing='0' style='width: 100%'>" ;
						print "<tr class='head'>" ;
							print "<th>" ;
								print _("Adults") ;
							print "</th>" ;
							foreach ($children AS $child) {
								print "<th>" ;
									print formatName("", $child["preferredName"], $child["surname"], "Student") ;
								print "</th>" ;
							}
						print "</tr>" ;
						$count=0 ;
						foreach ($adults AS $adult) {
							if ($count%2==0) {
								$rowNum="even" ;
							}
							else {
								$rowNum="odd" ;
							}
							$count++ ;
							print "<tr class='$rowNum'>" ;
								print "<td>" ;
									print "<b>" . $adult['officialName'] . "<b>" ;
								print "</td>" ;
								foreach ($children AS $child) {
									print "<td>" ;
									//echo $relationships[$adult["gibbonFamilyAdultID"]][$child["gibbonPersonID"]];
									//echo $adult["gibbonFamilyAdultID"];
									//echo $child["gibbonPersonID"];
									//print_r($relationships);?>
										<select name="relationships[]" id="relationships[]" style="width: 100%" <?php if($relationships[$adult["gibbonFamilyAdultID"]][$child["gibbonPersonID"]]=="Mother" || $relationships[$adult["gibbonFamilyAdultID"]][$child["gibbonPersonID"]]=="Father"){print " disabled";}?>>
											<option <?php if (@$relationships[$adult["gibbonFamilyAdultID"]][$child["gibbonPersonID"]]=="") { print "selected" ; } ?> value=""></option>
											<option <?php if (@$relationships[$adult["gibbonFamilyAdultID"]][$child["gibbonPersonID"]]=="Mother") { print "selected" ; } ?> value="Mother"><?php print _('Mother') ?></option>
											<option <?php if (@$relationships[$adult["gibbonFamilyAdultID"]][$child["gibbonPersonID"]]=="Father") { print "selected" ; } ?> value="Father"><?php print _('Father') ?></option>
											<option <?php if (@$relationships[$adult["gibbonFamilyAdultID"]][$child["gibbonPersonID"]]=="Step-Mother") { print "selected" ; } ?> value="Step-Mother"><?php print _('Step-Mother') ?></option>
											<option <?php if (@$relationships[$adult["gibbonFamilyAdultID"]][$child["gibbonPersonID"]]=="Step-Father") { print "selected" ; } ?> value="Step-Father"><?php print _('Step-Father') ?></option>
											<option <?php if (@$relationships[$adult["gibbonFamilyAdultID"]][$child["gibbonPersonID"]]=="Adoptive Parent") { print "selected" ; } ?> value="Adoptive Parent"><?php print _('Adoptive Parent') ?></option>
											<option <?php if (@$relationships[$adult["gibbonFamilyAdultID"]][$child["gibbonPersonID"]]=="Guardian") { print "selected" ; } ?> value="Guardian"><?php print _('Guardian') ?></option>
											<option <?php if (@$relationships[$adult["gibbonFamilyAdultID"]][$child["gibbonPersonID"]]=="Grandmother") { print "selected" ; } ?> value="Grandmother"><?php print _('Grandmother') ?></option>
											<option <?php if (@$relationships[$adult["gibbonFamilyAdultID"]][$child["gibbonPersonID"]]=="Grandfather") { print "selected" ; } ?> value="Grandfather"><?php print _('Grandfather') ?></option>
											<option <?php if (@$relationships[$adult["gibbonFamilyAdultID"]][$child["gibbonPersonID"]]=="Aunt") { print "selected" ; } ?> value="Aunt"><?php print _('Aunt') ?></option>
											<option <?php if (@$relationships[$adult["gibbonFamilyAdultID"]][$child["gibbonPersonID"]]=="Uncle") { print "selected" ; } ?> value="Uncle"><?php print _('Uncle') ?></option>
											<option <?php if (@$relationships[$adult["gibbonFamilyAdultID"]][$child["gibbonPersonID"]]=="Nanny/Helper") { print "selected" ; } ?> value="Nanny/Helper"><?php print _('Nanny/Helper') ?></option>
											<option <?php if (@$relationships[$adult["gibbonFamilyAdultID"]][$child["gibbonPersonID"]]=="Other") { print "selected" ; } ?> value="Other"><?php print _('Other') ?></option>
										</select>
										<input type="hidden" name="gibbonFamilyAdultID[]" value="<?php print $adult["gibbonFamilyAdultID"] ?>">
										<input type="hidden" name="gibbonPersonID[]" value="<?php print $child["gibbonPersonID"] ?>">
										<?php
									print "</td>" ;
								}
							print "</tr>" ;
						}
						?>
						<tr><td colspan="<?php print (count($children)+1) ?>" class="right">
							<input type="hidden" name="address" value="<?php print $_SESSION[$guid]["address"] ?>">
							<input type="submit" value="<?php print _("Submit") ; ?>">
						</td></tr>
						<?php
					print "</table>" ;
				print "</form>" ;
			}
			
			print "<h3>" ;
			print _("Edit Adult") ;
			print "</h3>" ;
			
			print "<form method='post' action='" . $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"]  . "/family_manage_edit_editAdultProcess.php'>" ;
			print "<table cellspacing='0' class='smallIntBorder' style='width: 100%'>" ;
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
						print ("<input type='text' name='officialName1' id='officialName1' value='".$adults[0]["officialName"]."' style='width:300px' required>");
					print "</td>";
				print "</tr>";
				print "<tr>";
					print "<td>";
						print _("<b>Email *</b>");
					print "</td>";
					print "<td class='right'>";
						print ("<input type='text' name='email1' id='email1' value='".$adults[0]["email"]."' style='width:300px' required>");
					print "</td>";
				print "</tr>";
				print "<tr>";
					print "<td>";
						print _("<b>Phone1 *</b>");
						print _("<br><i>Type,Country Code, Number</i>");
					print "</td>";
					print "<td class='right'>";
						print ("<input type='text' name='phone11' maxlength=10 id='phone11' value='".$adults[0]["phone1"]."' style='width:160px' required>");
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
								if ($adults[0]["phone1CountryCode"]==$rowSelect["iddCountryCode"]) {
									$selected="selected" ;
								}	
								print "<option $selected value='" . $rowSelect["iddCountryCode"] . "'>" . htmlPrep($rowSelect["iddCountryCode"]) . " - " .  htmlPrep(_($rowSelect["printable_name"])) . "</option>" ;
							}				
							print "</select>";?>
							<select style='width: 70px' name='phone1Type1' id='Phone1Type1'>
								<option <?php if ($adults[0]["phone1Type"]=="") { print "selected" ; }?> value=""></option>
								<option <?php if ($adults[0]["phone1Type"]=="Mobile") { print "selected" ; }?> value="Mobile"><?php print _('Mobile') ?></option>
								<option <?php if ($adults[0]["phone1Type"]=="Home") { print "selected" ; }?> value="Home"><?php print _('Home') ?></option>
								<option <?php if ($adults[0]["phone1Type"]=="Work") { print "selected" ; }?> value="Work"><?php print _('Work') ?></option>
								<option <?php if ($adults[0]["phone1Type"]=="Fax") { print "selected" ; }?> value="Fax"><?php print _('Fax') ?></option>
								<option <?php if ($adults[0]["phone1Type"]=="Pager") { print "selected" ; }?> value="Pager"><?php print _('Pager') ?></option>
								<option <?php if ($adults[0]["phone1Type"]=="Other") { print "selected" ; }?> value="Other"><?php print _('Other') ?></option>
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
								if ($adults[0]["phone2CountryCode"]==$rowSelect["iddCountryCode"]) {
									$selected="selected" ;
								}	
								print "<option $selected value='" . $rowSelect["iddCountryCode"] . "'>" . htmlPrep($rowSelect["iddCountryCode"]) . " - " .  htmlPrep(_($rowSelect["printable_name"])) . "</option>" ;
							}				
							print "</select>";?>
							<select style='width: 70px' name='phone2Type1' id='phone2Type1'>
								<option <?php if ($adults[0]["phone2Type"]=="") { print "selected" ; }?> value=""></option>
								<option <?php if ($adults[0]["phone2Type"]=="Mobile") { print "selected" ; }?> value="Mobile"><?php print _('Mobile') ?></option>
								<option <?php if ($adults[0]["phone2Type"]=="Home") { print "selected" ; }?> value="Home"><?php print _('Home') ?></option>
								<option <?php if ($adults[0]["phone2Type"]=="Work") { print "selected" ; }?> value="Work"><?php print _('Work') ?></option>
								<option <?php if ($adults[0]["phone2Type"]=="Fax") { print "selected" ; }?> value="Fax"><?php print _('Fax') ?></option>
								<option <?php if ($adults[0]["phone2Type"]=="Pager") { print "selected" ; }?> value="Pager"><?php print _('Pager') ?></option>
								<option <?php if ($adults[0]["phone2Type"]=="Other") { print "selected" ; }?> value="Other"><?php print _('Other') ?></option>
							</select>
<?php 				print "</td>";
				print "</tr>";
				print "<tr>";
					print "<td>";
						print _("<b>Profession </b>");
					print "</td>";
					print "<td class='right'>";
						print ("<input type='text' name='profession1' id='profession1' value='".$adults[0]["profession"]."' style='width:300px'>");
					print "</td>";
				print "</tr>";
				print "<tr>";
					print "<td>";
						print _("<b>Employer </b>");
					print "</td>";
					print "<td class='right'>";
						print ("<input type='text' name='employer1' id='employer1' value='".$adults[0]["employer"]."' style='width:300px'>");
					print "</td>";
				print "</tr>";
				print "<tr>";
					print "<td>";
						print _("<b>Annual Income </b>");
					print "</td>";
					print "<td class='right'>";
						print ("<input type='text' name='annual_income1' id='annual_income1' value='".$adults[0]["annual_income"]."' style='width:300px'>");
					print "</td>";
				print "</tr>";
				print "<tr>";
					print "<td>";
						print _("<b>Aadhar Card No. </b>");
					print "</td>";
					print "<td class='right'>";
						print ("<input type='text' name='nationalIDCardNumber1' id='nationalIDCardNumber1' value='".$adults[0]["nationalIDCardNumber"]."' style='width:300px'>");
					print "</td>";
				print "</tr>";
				print "<tr>";
					print "<td>";
						print _("<b>Contact Priority</b>");
					print "</td>";
					print "<td class='right'>";?>
						<input type='radio' name='contactPriority1' value='1' <?php if($adults[0]["contactPriority"]==1){echo "checked";}?>><span style='font-size:15px;margin:10px'>First</span>
						<input type='radio' name='contactPriority1' value='2' <?php if($adults[0]["contactPriority"]==2){echo "checked";}?>><span style='font-size:15px;margin:10px'>Second</span>
<?php				print "</td>";
				print "</tr>";
				print "<tr class='break'>" ;
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
						print ("<input type='text' name='officialName2' id='officialName2' value='".$adults[1]["officialName"]."' style='width:300px' required>");
					print "</td>";
				print "</tr>";
				print "<tr>";
					print "<td>";
						print _("<b>Email *</b>");
					print "</td>";
					print "<td class='right'>";
						print ("<input type='text' name='email2' id='email2' value='".$adults[1]["email"]."' style='width:300px' required>");
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
								if ($adults[1]["phone1CountryCode"]==$rowSelect["iddCountryCode"]) {
									$selected="selected" ;
								}	
								print "<option $selected value='" . $rowSelect["iddCountryCode"] . "'>" . htmlPrep($rowSelect["iddCountryCode"]) . " - " .  htmlPrep(_($rowSelect["printable_name"])) . "</option>" ;
							}				
							print "</select>";?>
							<select style='width: 70px' name='phone1Type2' id='phone1Type2'>
								<option <?php if ($adults[1]["phone1Type"]=="") { print "selected" ; }?> value=""></option>
								<option <?php if ($adults[1]["phone1Type"]=="Mobile") { print "selected" ; }?> value="Mobile"><?php print _('Mobile') ?></option>
								<option <?php if ($adults[1]["phone1Type"]=="Home") { print "selected" ; }?> value="Home"><?php print _('Home') ?></option>
								<option <?php if ($adults[1]["phone1Type"]=="Work") { print "selected" ; }?> value="Work"><?php print _('Work') ?></option>
								<option <?php if ($adults[1]["phone1Type"]=="Fax") { print "selected" ; }?> value="Fax"><?php print _('Fax') ?></option>
								<option <?php if ($adults[1]["phone1Type"]=="Pager") { print "selected" ; }?> value="Pager"><?php print _('Pager') ?></option>
								<option <?php if ($adults[1]["phone1Type"]=="Other") { print "selected" ; }?> value="Other"><?php print _('Other') ?></option>
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
								if ($adults[1]["phone2CountryCode"]==$rowSelect["iddCountryCode"]) {
									$selected="selected" ;
								}	
								print "<option $selected value='" . $rowSelect["iddCountryCode"] . "'>" . htmlPrep($rowSelect["iddCountryCode"]) . " - " .  htmlPrep(_($rowSelect["printable_name"])) . "</option>" ;
							}				
							print "</select>";?>
							<select style='width: 70px' name='phone2Type2' id='phone2Type2'>
								<option <?php if ($adults[1]["phone2Type"]=="") { print "selected" ; }?> value=""></option>
								<option <?php if ($adults[1]["phone2Type"]=="Mobile") { print "selected" ; }?> value="Mobile"><?php print _('Mobile') ?></option>
								<option <?php if ($adults[1]["phone2Type"]=="Home") { print "selected" ; }?> value="Home"><?php print _('Home') ?></option>
								<option <?php if ($adults[1]["phone2Type"]=="Work") { print "selected" ; }?> value="Work"><?php print _('Work') ?></option>
								<option <?php if ($adults[1]["phone2Type"]=="Fax") { print "selected" ; }?> value="Fax"><?php print _('Fax') ?></option>
								<option <?php if ($adults[1]["phone2Type"]=="Pager") { print "selected" ; }?> value="Pager"><?php print _('Pager') ?></option>
								<option <?php if ($adults[1]["phone2Type"]=="Other") { print "selected" ; }?> value="Other"><?php print _('Other') ?></option>
							</select>
<?php 				print "</td>";
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
						print _("<b>Employer </b>");
					print "</td>";
					print "<td class='right'>";
						print ("<input type='text' name='employer2' id='employer2' value='".$adults[1]["employer"]."' style='width:300px'>");
					print "</td>";
				print "</tr>";
				print "<tr>";
					print "<td>";
						print _("<b>Annual Income </b>");
					print "</td>";
					print "<td class='right'>";
						print ("<input type='text' name='annual_income2' id='annual_income2' value='".$adults[1]["annual_income"]."' style='width:300px'>");
					print "</td>";
				print "</tr>";
				print "<tr>";
					print "<td>";
						print _("<b>Aadhar Card No. </b>");
					print "</td>";
					print "<td class='right'>";
						print ("<input type='text' name='nationalIDCardNumber2' id='nationalIDCardNumber2' value='".$adults[1]["nationalIDCardNumber"]."' style='width:300px'>");
					print "</td>";
				print "</tr>";
				print "<tr>";
					print "<td>";
						print _("<b>Contact Priority</b>");
					print "</td>";
					print "<td class='right'>";?>
						<input type='radio' name='contactPriority2' value='1' <?php if($adults[1]["contactPriority"]==1){echo "checked";}?>><span style='font-size:15px;margin:10px'>First</span>
						<input type='radio' name='contactPriority2' value='2' <?php if($adults[1]["contactPriority"]==2){echo "checked";}?>><span style='font-size:15px;margin:10px'>Second</span>
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
						print _("<b>Email *</b>");
					print "</td>";
					print "<td class='right'>";
						print ("<input type='text' name='email3' id='email3' value='".$adults[2]["email"]."' style='width:300px' >");
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
						<select style='width: 70px' name='phone1Type3' id='phone1Type3'>
							<option <?php if ($adults[2]["phone1Type"]=="") { print "selected" ; }?> value=""></option>
							<option <?php if ($adults[2]["phone1Type"]=="Mobile") { print "selected" ; }?> value="Mobile"><?php print _('Mobile') ?></option>
							<option <?php if ($adults[2]["phone1Type"]=="Home") { print "selected" ; }?> value="Home"><?php print _('Home') ?></option>
							<option <?php if ($adults[2]["phone1Type"]=="Work") { print "selected" ; }?> value="Work"><?php print _('Work') ?></option>
							<option <?php if ($adults[2]["phone1Type"]=="Fax") { print "selected" ; }?> value="Fax"><?php print _('Fax') ?></option>
							<option <?php if ($adults[2]["phone1Type"]=="Pager") { print "selected" ; }?> value="Pager"><?php print _('Pager') ?></option>
							<option <?php if ($adults[2]["phone1Type"]=="Other") { print "selected" ; }?> value="Other"><?php print _('Other') ?></option>
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
							<option <?php if ($adults[2]["phone2Type"]=="") { print "selected" ; }?> value=""></option>
							<option <?php if ($adults[2]["phone2Type"]=="Mobile") { print "selected" ; }?> value="Mobile"><?php print _('Mobile') ?></option>
							<option <?php if ($adults[2]["phone2Type"]=="Home") { print "selected" ; }?> value="Home"><?php print _('Home') ?></option>
							<option <?php if ($adults[2]["phone2Type"]=="Work") { print "selected" ; }?> value="Work"><?php print _('Work') ?></option>
							<option <?php if ($adults[2]["phone2Type"]=="Fax") { print "selected" ; }?> value="Fax"><?php print _('Fax') ?></option>
							<option <?php if ($adults[2]["phone2Type"]=="Pager") { print "selected" ; }?> value="Pager"><?php print _('Pager') ?></option>
							<option <?php if ($adults[2]["phone2Type"]=="Other") { print "selected" ; }?> value="Other"><?php print _('Other') ?></option>
						</select>
<?php				print "</td>";
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
						print _("<b>Employer </b>");
					print "</td>";
					print "<td class='right'>";
						print ("<input type='text' name='employer3' id='employer3' value='".$adults[2]["employer"]."' style='width:300px'>");
					print "</td>";
				print "</tr>";
				print "<tr class='guardian'>";
					print "<td>";
						print _("<b>Annual Income </b>");
					print "</td>";
					print "<td class='right'>";
						print ("<input type='text' name='annual_income3' id='annual_income3' value='".$adults[2]["annual_income"]."' style='width:300px'>");
					print "</td>";
				print "</tr>";
				print "<tr class='guardian'>";
					print "<td>";
						print _("<b>Aadhar Card No. </b>");
					print "</td>";
					print "<td class='right'>";
						print ("<input type='text' name='nationalIDCardNumber3' id='nationalIDCardNumber2' value='".$adults[2]["nationalIDCardNumber"]."' style='width:300px'>");
					print "</td>";
				print "</tr>";
				print "<tr class='guardian' id='guardianRelationRow' style='display:none'>";
					print "<td>";
						print _("<b>Relationship</b>");
					print "</td>";
					print "<td class='right'>";?>
						<select name="guardianRelation" id='guardianRelation' style="width: 300px;">
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
				print "<tr>";
					print "<td colspan=2 class='right'>";
						//echo $adults[2]["gibbonFamilyAdultID"];
						echo "<input type='hidden' name='gibbonFamilyAdultID1' id='gibbonFamilyAdultID1' value='".$adults[0]["gibbonFamilyAdultID"]."'>";
						echo "<input type='hidden' name='gibbonFamilyAdultID2' id='gibbonFamilyAdultID2' value='".$adults[1]["gibbonFamilyAdultID"]."'>";
						echo "<input type='hidden' name='gibbonFamilyAdultID3' id='gibbonFamilyAdultID3' value='".$adults[2]["gibbonFamilyAdultID"]."'>";
						echo "<input type='hidden' name='gibbonFamilyID' id='gibbonFamilyID' value='".$gibbonFamilyID."'>";
						echo "<input type='submit' value='Submit'>";
					print "</td>";
				print "</tr>";
				print "</table>" ;
			print "</form>";
			
			
			print "<h3>" ;
			print _("Edit Emergency Contacts") ;
			print "</h3>" ;
				
			print "<form method='post' action='" . $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/family_manage_edit_emergencyContactsProcess.php?gibbonFamilyID=$gibbonFamilyID&search=$search'>" ;
			print "<table cellspacing='0' class='smallIntBorder' style='width: 100%'>" ;
				print "<tr class='break'>" ;
					print "<td colspan=2>" ; 
						print "<h3>";
						print _('Emergency Contact1');
						print "</h3>";
					print "</td>";
				print "</tr>";
				print "<tr>";
					print "<td>";
						print _("<b>Contact1 Name<b>");
					print "</td>";
					print "<td class='right'>";
						print ("<input type='text' name='emergency1Name' id='emergency1Name' value='".$row["emergency1Name"]."' style='width:300px'>");
					print "</td>";
				print "</tr>";
				print "<tr>";
					print "<td>";
						print _("<b>Contact1 Phone*<b>");
					print "</td>";
					print "<td class='right'>";
						print ("<input type='text' name='emergency1Phone' id='emergency1Phone' value='".$row["emergency1Phone"]."' style='width:300px'>");
					print "</td>";
				print "</tr>";
				print "</tr>";
				print "<tr>";
					print "<td>";
						print _("<b>Contact1 Relation<b>");
					print "</td>";
					print "<td class='right'>";?>
						<select name="emergency1Relation" id="emergency1Relation" style="width: 300px">
							<option <?php if ($row["emergency1Relation"]=="") { print "selected" ; } ?> value=""></option>
							<option <?php if ($row["emergency1Relation"]=="Step-Mother") { print "selected" ; } ?> value="Step-Mother"><?php print _('Step-Mother') ?></option>
							<option <?php if ($row["emergency1Relation"]=="Step-Father") { print "selected" ; } ?> value="Step-Father"><?php print _('Step-Father') ?></option>
							<option <?php if ($row["emergency1Relation"]=="Adoptive Parent") { print "selected" ; } ?> value="Adoptive Parent"><?php print _('Adoptive Parent') ?></option>
							<option <?php if ($row["emergency1Relation"]=="Guardian") { print "selected" ; } ?> value="Guardian"><?php print _('Guardian') ?></option>
							<option <?php if ($row["emergency1Relation"]=="Grandmother") { print "selected" ; } ?> value="Grandmother"><?php print _('Grandmother') ?></option>
							<option <?php if ($row["emergency1Relation"]=="Grandfather") { print "selected" ; } ?> value="Grandfather"><?php print _('Grandfather') ?></option>
							<option <?php if ($row["emergency1Relation"]=="Aunt") { print "selected" ; } ?> value="Aunt"><?php print _('Aunt') ?></option>
							<option <?php if ($row["emergency1Relation"]=="Uncle") { print "selected" ; } ?> value="Uncle"><?php print _('Uncle') ?></option>
							<option <?php if ($row["emergency1Relation"]=="Nanny/Helper") { print "selected" ; } ?> value="Nanny/Helper"><?php print _('Nanny/Helper') ?></option>
							<option <?php if ($row["emergency2Relation"]=="Neighbour") { print "selected" ; } ?> value="Neighbour"><?php print _('Neighbour') ?></option>
							<option <?php if ($row["emergency2Relation"]=="FamilyFriend") { print "selected" ; } ?> value="FamilyFriend"><?php print _('Family Friend') ?></option>
							<option <?php if ($row["emergency1Relation"]=="Other") { print "selected" ; } ?> value="Other"><?php print _('Other') ?></option>
						</select>
<?php				print "</td>";
				print "</tr>";
				print "<tr class='break'>" ;
					print "<td colspan=2>" ; 
						print "<h3>";
						print _('Emergency Contact2');
						print "</h3>";
					print "</td>";
				print "</tr>";
				print "<tr>";
					print "<td>";
						print _("<b>Contact2 Name<b>");
					print "</td>";
					print "<td class='right'>";
						print ("<input type='text' name='emergency2Name' id='emergency2Name' value='".$row["emergency2Name"]."' style='width:300px'>");
					print "</td>";
				print "</tr>";
				print "<tr>";
					print "<td>";
						print _("<b>Contact2 Phone*<b>");
					print "</td>";
					print "<td class='right'>";
						print ("<input type='text' name='emergency2Phone' id='emergency2Phone' value='".$row["emergency2Phone"]."' style='width:300px'>");
					print "</td>";
				print "</tr>";
				print "</tr>";
				print "<tr>";
					print "<td>";
						print _("<b>Contact2 Relation<b>");
					print "</td>";
					print "<td class='right'>";?>
						<select name="emergency2Relation" id="emergency2Relation" style="width: 300px">
							<option <?php if ($row["emergency2Relation"]=="") { print "selected" ; } ?> value=""></option>
							<option <?php if ($row["emergency2Relation"]=="Step-Mother") { print "selected" ; } ?> value="Step-Mother"><?php print _('Step-Mother') ?></option>
							<option <?php if ($row["emergency2Relation"]=="Step-Father") { print "selected" ; } ?> value="Step-Father"><?php print _('Step-Father') ?></option>
							<option <?php if ($row["emergency2Relation"]=="Adoptive Parent") { print "selected" ; } ?> value="Adoptive Parent"><?php print _('Adoptive Parent') ?></option>
							<option <?php if ($row["emergency2Relation"]=="Guardian") { print "selected" ; } ?> value="Guardian"><?php print _('Guardian') ?></option>
							<option <?php if ($row["emergency2Relation"]=="Grandmother") { print "selected" ; } ?> value="Grandmother"><?php print _('Grandmother') ?></option>
							<option <?php if ($row["emergency2Relation"]=="Grandfather") { print "selected" ; } ?> value="Grandfather"><?php print _('Grandfather') ?></option>
							<option <?php if ($row["emergency2Relation"]=="Aunt") { print "selected" ; } ?> value="Aunt"><?php print _('Aunt') ?></option>
							<option <?php if ($row["emergency2Relation"]=="Uncle") { print "selected" ; } ?> value="Uncle"><?php print _('Uncle') ?></option>
							<option <?php if ($row["emergency2Relation"]=="Nanny/Helper") { print "selected" ; } ?> value="Nanny/Helper"><?php print _('Nanny/Helper') ?></option>
							<option <?php if ($row["emergency2Relation"]=="Neighbour") { print "selected" ; } ?> value="Neighbour"><?php print _('Neighbour') ?></option>
							<option <?php if ($row["emergency2Relation"]=="FamilyFriend") { print "selected" ; } ?> value="FamilyFriend"><?php print _('Family Friend') ?></option>
							<option <?php if ($row["emergency2Relation"]=="Other") { print "selected" ; } ?> value="Other"><?php print _('Other') ?></option>
						</select>
<?php				print "</td>";
				print "</tr>";
				print "<tr>";
					print "<td colspan=2 class='right'>";
						print ("<input id='emergencySubmit' type='submit' value='Submit'>");
						print ("<input type='hidden' name='gibbonFamilyID' id='gibbonFamilyID' value='".$gibbonFamilyID."'");
					print "</td>";
				print "</tr>";
			print "</table>";
			print "</form>";
			
			print "<h3>" ;
			print _("Edit Children") ;
			print "</h3>" ;
				
			if ($resultChildren->rowCount()<1) {
				print "<div class='error'>" ;
				print _("There are no records to display.") ;
				print "</div>" ;
			}
			else {
				print "<table cellspacing='0' style='width: 100%'>" ;
					print "<tr class='head'>" ;
						print "<th>" ;
							print _("Photo") ;
						print "</th>" ;
						print "<th>" ;
							print _("Name") ;
						print "</th>" ;
						print "<th>" ;
							print _("Status") ;
						print "</th>" ;
						print "<th>" ;
							print _("Class") ;
						print "</th>" ;
						print "<th>" ;
							print _("Comment") ;
						print "</th>" ;
						print "<th>" ;
							print _("Actions") ;
						print "</th>" ;
					print "</tr>" ;
					
					$count=0;
					$rowNum="odd" ;
					foreach ($children AS $child) {
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
								print getUserPhoto($guid, $child["image_75"], 75) ;
							print "</td>" ;
							print "<td>" ;
								print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/User Admin/user_manage_edit.php&gibbonPersonID=" . $child["gibbonPersonID"] . "'>" . formatName("", $child["preferredName"], $child["surname"], "Student") . "</a>" ;
							print "</td>" ;
							print "<td>" ;
								print $child["status"] ;
							print "</td>" ;
							print "<td>" ;
								try {
									$dataDetail=array("gibbonPersonID"=>$child["gibbonPersonID"], "gibbonSchoolYearID"=>$_SESSION[$guid]["gibbonSchoolYearID"]); 
									$sqlDetail="SELECT * FROM gibbonrollgroup JOIN gibbonstudentenrolment ON (gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID) WHERE gibbonPersonID=:gibbonPersonID AND gibbonstudentenrolment.gibbonSchoolYearID=:gibbonSchoolYearID AND gibbonrollgroup.gibbonSchoolYearID=:gibbonSchoolYearID" ;
									$sectionDetail=$connection2->prepare($sqlDetail);
									$sectionDetail->execute($dataDetail);
								}
								catch(PDOException $e) { 
									print "<div class='error'>" . $e->getMessage() . "</div>" ; 
								}
								try {
									$dataDetail=array("gibbonPersonID"=>$child["gibbonPersonID"], "gibbonSchoolYearID"=>$_SESSION[$guid]["gibbonSchoolYearID"]); 
									$sqlDetail="SELECT * FROM gibbonyeargroup JOIN gibbonstudentenrolment ON (gibbonstudentenrolment.gibbonYearGroupID=gibbonyeargroup.gibbonYearGroupID) WHERE gibbonPersonID=:gibbonPersonID AND gibbonstudentenrolment.gibbonSchoolYearID=:gibbonSchoolYearID" ;
									$classDetail=$connection2->prepare($sqlDetail);
									$classDetail->execute($dataDetail);
								}
								catch(PDOException $e) { 
									print "<div class='error'>" . $e->getMessage() . "</div>" ; 
								}
								if ($sectionDetail->rowCount()==1 || $classDetail->rowCount()==1) {
									$sectionResult=$sectionDetail->fetch() ;
									$classResult=$classDetail->fetch() ;
									print $classResult["name"]." ".$sectionResult["name"] ;
								}
							print "</td>" ;
							print "<td>" ;
								print nl2brr($child["comment"]) ;
							print "</td>" ;
							print "<td>" ;
								print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/family_manage_edit_editChild.php&gibbonFamilyID=$gibbonFamilyID&gibbonPersonID=" . $child["gibbonPersonID"] . "&search=$search'><img title='" . _('Edit') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/config.png'/></a> " ;
								print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/family_manage_edit_deleteChild.php&gibbonFamilyID=$gibbonFamilyID&gibbonPersonID=" . $child["gibbonPersonID"] . "&search=$search'><img title='" . _('Delete') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/garbage.png'/></a>" ;
								print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/user_manage_password.php&gibbonPersonID=" . $child["gibbonPersonID"] . "&search=$search'><img title='" ._('Change Password') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/key.png'/></a>" ;
							print "</td>" ;
						print "</tr>" ;
					}
				print "</table>" ;
			}
			
			?>
			<form method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/family_manage_edit_addChildProcess.php" ?>">
				<table class='smallIntBorder' cellspacing='0' style="width: 100%">	
					<tr class='break'>
						<td colspan=2>
							<h3>
							<?php print _('Add Child') ?>
							</h3>
						</td>
					</tr>
					<tr>
						<td style='width: 275px'> 
							<b><?php print _('Child\'s Name') ?> *</b><br/>
							<span style="font-size: 90%"><i></i></span>
						</td>
						<td class="right">

							<select name="gibbonPersonID" id="gibbonPersonID" style="width: 302px">
								<?php
								print "<option value='0'>" . _('Please select...') . "</option>" ;
								?>
								<optgroup label='--<?php print _('Enroled Students') ?>--'>
								<?php
								try {
									$dataSelect=array("gibbonSchoolYearID"=>$_SESSION[$guid]["gibbonSchoolYearID"]); 
									$sqlSelect="SELECT gibbonperson.gibbonPersonID, preferredName, account_number, surname, gibbonyeargroup.name AS name FROM gibbonperson, gibbonstudentenrolment, gibbonyeargroup WHERE gibbonperson.gibbonPersonID=gibbonstudentenrolment.gibbonPersonID AND gibbonstudentenrolment.gibbonyearGroupID=gibbonyeargroup.gibbonyearGroupID AND status='FULL' AND gibbonstudentenrolment.gibbonSchoolYearID=:gibbonSchoolYearID ORDER BY name, surname, preferredName" ;
									$resultSelect=$connection2->prepare($sqlSelect);
									$resultSelect->execute($dataSelect);
								}
								catch(PDOException $e) { }
								while ($rowSelect=$resultSelect->fetch()) {
									print $rowSelect["gibbonPersonID"] ;
									print "<option value='" . $rowSelect["gibbonPersonID"] . "'>" . htmlPrep($rowSelect["name"]) . " - " . formatName("", htmlPrep($rowSelect["preferredName"]), htmlPrep($rowSelect["surname"]), "Student") ." ( Acc. no. ".substr($rowSelect['account_number'],-5)." ) </option>" ;
								}
								?>
								<?php
								try {
									$dataSelect=array(); 
									$sqlSelect="SELECT * FROM gibbonperson WHERE (status='Full' OR status='Expected') AND `gibbonRoleIDAll` LIKE '%003%' AND `gibbonPersonID` NOT IN (SELECT `gibbonPersonID` FROM `gibbonstudentenrolment` WHERE `gibbonSchoolYearID`='".$_SESSION[$guid]["gibbonSchoolYearID"]."') ORDER BY surname, preferredName" ;
									$resultSelect=$connection2->prepare($sqlSelect);
									$resultSelect->execute($dataSelect);
								}
								catch(PDOException $e) { }
								if($resultSelect->rowCount() > 0){?>
									</optgroup>
									<optgroup label='--<?php print _('All Users') ?>--'>
<?php							}
								while ($rowSelect=$resultSelect->fetch()) {
									$expected="" ;
									if ($rowSelect["status"]=="Expected") {
										$expected=" (Expected)" ;
									}
									print "<option value='" . $rowSelect["gibbonPersonID"] . "'>" . formatName("", htmlPrep($rowSelect["preferredName"]), htmlPrep($rowSelect["surname"]), "Student", true) . "$expected( Apln no. ".substr($rowSelect["gibbonApplicationFormID"],-5)." )</option>" ;
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
							<b><?php print _('Comment') ?></b><br/>
						</td>
						<td class="right">
							<textarea name="comment" id="comment" rows=8 style="width: 300px"></textarea>
						</td>
					</tr>
					</tr>
					<tr>
						<td>
							<span style="font-size: 90%"><i>* <?php print _("denotes a required field") ; ?></i></span>
						</td>
						<td class="right">
							<input type="hidden" name="address" value="<?php print $_SESSION[$guid]["address"] ?>">
							<input type="hidden" name="gibbonFamilyID" value="<?php echo $_GET['gibbonFamilyID']?>">
							<input type="hidden" name="get_personID_from_accno_url" id="get_personID_from_accno_url" value="<?php echo $_SESSION[$guid]["absoluteURL"] . "/modules/User%20Admin/ajax_get_personid_by_accno.php";?>">
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
<script>

</script>