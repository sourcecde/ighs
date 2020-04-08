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


if (isActionAccessible($guid, $connection2, "/modules/User Admin/data_personal_edit.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
	//Proceed!
	print "<div class='trail'>" ;
	print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . _("Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . _(getModuleName($_GET["q"])) . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/User Admin/data_personal.php'>" . _('Personal Data Updates') . "</a> > </div><div class='trailEnd'>" . _('Edit Request') . "</div>" ;
	print "</div>" ;
	
	//Check if school year specified
	$gibbonPersonUpdateID=$_GET["gibbonPersonUpdateID"];
	if ($gibbonPersonUpdateID=="Y") {
		print "<div class='error'>" ;
			print _("You have not specified one or more required parameters.") ;
		print "</div>" ;
	}
	else {
		try {
			$data=array("gibbonPersonUpdateID"=>$gibbonPersonUpdateID); 
			$sql="SELECT gibbonpersonupdate.gibbonPersonID, gibbonperson.title AS title, gibbonperson.surname AS surname, gibbonperson.firstName AS firstName, gibbonperson.preferredName AS preferredName, gibbonperson.officialName AS officialName, gibbonperson.nameInCharacters AS nameInCharacters, gibbonperson.dob AS dob, gibbonperson.email AS email, gibbonperson.emailAlternate AS emailAlternate, gibbonperson.address1 AS address1, gibbonperson.address1District AS address1District, gibbonperson.address1Country AS address1Country, gibbonperson.address2 AS address2, gibbonperson.address2District AS address2District, gibbonperson.address2Country AS address2Country, gibbonperson.phone1Type AS phone1Type, gibbonperson.phone1CountryCode AS phone1CountryCode, gibbonperson.phone1 AS phone1, gibbonperson.phone2Type AS phone2Type, gibbonperson.phone2CountryCode AS phone2CountryCode, gibbonperson.phone2 AS phone2, gibbonperson.phone3Type AS phone3Type, gibbonperson.phone3CountryCode AS phone3CountryCode, gibbonperson.phone3 AS phone3, gibbonperson.phone4Type AS phone4Type, gibbonperson.phone4CountryCode AS phone4CountryCode, gibbonperson.phone4 AS phone4, gibbonperson.languageFirst AS languageFirst, gibbonperson.languageSecond AS languageSecond, gibbonperson.languageThird AS languageThird, gibbonperson.countryOfBirth AS countryOfBirth, gibbonperson.ethnicity AS ethnicity, gibbonperson.citizenship1 AS citizenship1, gibbonperson.citizenship1Passport AS citizenship1Passport, gibbonperson.citizenship2 AS citizenship2, gibbonperson.citizenship2Passport AS citizenship2Passport, gibbonperson.religion AS religion, gibbonperson.nationalIDCardNumber AS nationalIDCardNumber, gibbonperson.residencyStatus AS residencyStatus, gibbonperson.visaExpiryDate AS visaExpiryDate, gibbonperson.profession AS profession , gibbonperson.employer AS employer, gibbonperson.jobTitle AS jobTitle, gibbonperson.emergency1Name AS emergency1Name, gibbonperson.emergency1Number1 AS emergency1Number1, gibbonperson.emergency1Number2 AS emergency1Number2, gibbonperson.emergency1Relationship AS emergency1Relationship, gibbonperson.emergency2Name AS emergency2Name, gibbonperson.emergency2Number1 AS emergency2Number1, gibbonperson.emergency2Number2 AS emergency2Number2, gibbonperson.emergency2Relationship AS emergency2Relationship, gibbonperson.vehicleRegistration AS vehicleRegistration, gibbonperson.privacy AS privacy, gibbonpersonupdate.title AS newtitle, gibbonpersonupdate.surname AS newsurname, gibbonpersonupdate.firstName AS newfirstName, gibbonpersonupdate.preferredName AS newpreferredName, gibbonpersonupdate.officialName AS newofficialName, gibbonpersonupdate.nameInCharacters AS newnameInCharacters, gibbonpersonupdate.dob AS newdob, gibbonpersonupdate.email AS newemail, gibbonpersonupdate.emailAlternate AS newemailAlternate, gibbonpersonupdate.address1 AS newaddress1, gibbonpersonupdate.address1District AS newaddress1District, gibbonpersonupdate.address1Country AS newaddress1Country, gibbonpersonupdate.address2 AS newaddress2, gibbonpersonupdate.address2District AS newaddress2District, gibbonpersonupdate.address2Country AS newaddress2Country, gibbonpersonupdate.phone1Type AS newphone1Type, gibbonpersonupdate.phone1CountryCode AS newphone1CountryCode, gibbonpersonupdate.phone1 AS newphone1, gibbonpersonupdate.phone2Type AS newphone2Type, gibbonpersonupdate.phone2CountryCode AS newphone2CountryCode, gibbonpersonupdate.phone2 AS newphone2, gibbonpersonupdate.phone3Type AS newphone3Type, gibbonpersonupdate.phone3CountryCode AS newphone3CountryCode, gibbonpersonupdate.phone3 AS newphone3, gibbonpersonupdate.phone4Type AS newphone4Type, gibbonpersonupdate.phone4CountryCode AS newphone4CountryCode, gibbonpersonupdate.phone4 AS newphone4, gibbonpersonupdate.languageFirst AS newlanguageFirst, gibbonpersonupdate.languageSecond AS newlanguageSecond, gibbonpersonupdate.languageThird AS newlanguageThird, gibbonpersonupdate.countryOfBirth AS newcountryOfBirth, gibbonpersonupdate.ethnicity AS newethnicity, gibbonpersonupdate.citizenship1 AS newcitizenship1, gibbonpersonupdate.citizenship1Passport AS newcitizenship1Passport, gibbonpersonupdate.citizenship2 AS newcitizenship2, gibbonpersonupdate.citizenship2Passport AS newcitizenship2Passport, gibbonpersonupdate.religion AS newreligion, gibbonpersonupdate.nationalIDCardNumber AS newnationalIDCardNumber, gibbonpersonupdate.residencyStatus AS newresidencyStatus, gibbonpersonupdate.visaExpiryDate AS newvisaExpiryDate, gibbonpersonupdate.profession AS newprofession , gibbonpersonupdate.employer AS newemployer, gibbonpersonupdate.jobTitle AS newjobTitle, gibbonpersonupdate.emergency1Name AS newemergency1Name, gibbonpersonupdate.emergency1Number1 AS newemergency1Number1, gibbonpersonupdate.emergency1Number2 AS newemergency1Number2, gibbonpersonupdate.emergency1Relationship AS newemergency1Relationship, gibbonpersonupdate.emergency2Name AS newemergency2Name, gibbonpersonupdate.emergency2Number1 AS newemergency2Number1, gibbonpersonupdate.emergency2Number2 AS newemergency2Number2, gibbonpersonupdate.emergency2Relationship AS newemergency2Relationship, gibbonpersonupdate.vehicleRegistration AS newvehicleRegistration, gibbonpersonupdate.privacy AS newprivacy FROM gibbonpersonupdate JOIN gibbonperson ON (gibbonpersonupdate.gibbonPersonID=gibbonperson.gibbonPersonID) WHERE gibbonPersonUpdateID=:gibbonPersonUpdateID" ;
			$result=$connection2->prepare($sql);
			$result->execute($data);
		}
		catch(PDOException $e) { 
			print "<div class='error'>" . $e->getMessage() . "</div>" ; 
		}

		if ($result->rowCount()!=1) {
			print "<div class='error'>" ;
				print _("The selected record does not exist, or you do not have access to it.") ;
			print "</div>" ;
		}
		else {
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
				else if ($updateReturn=="success1") {
					$updateReturnMessage=_("Your request was completed successfully, but status could not be updated.") ;	
				}
				else if ($updateReturn=="success0") {
					$updateReturnMessage=_("Your request was completed successfully.") ;	
					$class="success" ;
				}
				print "<div class='$class'>" ;
					print $updateReturnMessage;
				print "</div>" ;
			} 

			//Let's go!
			$row=$result->fetch() ;
			?>
			<form method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/data_personal_editProcess.php?gibbonPersonUpdateID=$gibbonPersonUpdateID" ?>">
				<?php
				print "<table cellspacing='0' style='width: 100%'>" ;
					print "<tr class='head'>" ;
						print "<th>" ;
							print _("Field") ;
						print "</th>" ;
						print "<th>" ;
							print _("Current Value") ;
						print "</th>" ;
						print "<th>" ;
							print _("New Value") ;
						print "</th>" ;
						print "<th>" ;
							print _("Accept") ;
						print "</th>" ;
					print "</tr>" ;
					
					$rowNum="even" ;
						
					//COLOR ROW BY STATUS!
					print "<tr class='odd'>" ;
						print "<td>" ;
							print _("Title") ;
						print "</td>" ;
						print "<td>" ;
							print $row["title"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["title"]!=$row["newtitle"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newtitle"] ;
						print "</td>" ;
						print "<td>" ;
							if ($row["title"]!=$row["newtitle"]) { print "<input checked type='checkbox' name='newtitleOn'><input name='newtitle' type='hidden' value='" . htmlprep($row["newtitle"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					print "<tr class='even'>" ;
						print "<td>" ;
							print _("Surname") ;
						print "</td>" ;
						print "<td>" ;
							print $row["surname"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["surname"]!=$row["newsurname"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newsurname"] ;
						print "</td>" ;
						print "<td>" ;
							if ($row["surname"]!=$row["newsurname"]) { print "<input checked type='checkbox' name='newsurnameOn'><input name='newsurname' type='hidden' value='" . htmlprep($row["newsurname"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					print "<tr class='odd'>" ;
						print "<td>" ;
							print _("First Name") ;
						print "</td>" ;
						print "<td>" ;
							print $row["firstName"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["firstName"]!=$row["newfirstName"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newfirstName"] ;
							print "</span>" ;
						print "</td>" ;
						print "<td>" ;
							if ($row["firstName"]!=$row["newfirstName"]) { print "<input checked type='checkbox' name='newfirstNameOn'><input name='newfirstName' type='hidden' value='" . htmlprep($row["newfirstName"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					print "<tr class='odd'>" ;
						print "<td>" ;
							print _("Preferred Name") ;
						print "</td>" ;
						print "<td>" ;
							print $row["preferredName"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["preferredName"]!=$row["newpreferredName"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newpreferredName"] ;
							print "</span>" ;
						print "</td>" ;
						print "<td>" ;
							if ($row["preferredName"]!=$row["newpreferredName"]) { print "<input checked type='checkbox' name='newpreferredNameOn'><input name='newpreferredName' type='hidden' value='" . htmlprep($row["newpreferredName"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					print "<tr class='even'>" ;
						print "<td>" ;
							print _("Official Name") ;
						print "</td>" ;
						print "<td>" ;
							print $row["officialName"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["officialName"]!=$row["newofficialName"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newofficialName"] ;
							print "</span>" ;
						print "</td>" ;
						print "<td>" ;
							if ($row["officialName"]!=$row["newofficialName"]) { print "<input checked type='checkbox' name='newofficialNameOn'><input name='newofficialName' type='hidden' value='" . htmlprep($row["newofficialName"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					print "<tr class='odd'>" ;
						print "<td>" ;
							print _("Name In Characters") ;
						print "</td>" ;
						print "<td>" ;
							print $row["nameInCharacters"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["nameInCharacters"]!=$row["newnameInCharacters"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newnameInCharacters"] ;
							print "</span>" ;
						print "</td>" ;
						print "<td>" ;
							if ($row["nameInCharacters"]!=$row["newnameInCharacters"]) { print "<input checked type='checkbox' name='newnameInCharactersOn'><input name='newnameInCharacters' type='hidden' value='" . htmlprep($row["newnameInCharacters"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					print "<tr class='even'>" ;
						print "<td>" ;
							print _("Date of Birth") ;
						print "</td>" ;
						print "<td>" ;
							print dateConvertBack($guid, $row["dob"]) ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["dob"]!=$row["newdob"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print dateConvertBack($guid, $row["newdob"]) ;
							print "</span>" ;
						print "</td>" ;
						print "<td>" ;
							if ($row["dob"]!=$row["newdob"]) { print "<input checked type='checkbox' name='newdobOn'><input name='newdob' type='hidden' value='" . htmlprep($row["newdob"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					print "<tr class='odd'>" ;
						print "<td>" ;
							print _("Email") ;
						print "</td>" ;
						print "<td>" ;
							print $row["email"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["email"]!=$row["newemail"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newemail"] ;
							print "</span>" ;
						print "</td>" ;
						print "<td>" ;
							if ($row["email"]!=$row["newemail"]) { print "<input checked type='checkbox' name='newemailOn'><input name='newemail' type='hidden' value='" . htmlprep($row["newemail"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					print "<tr class='even'>" ;
						print "<td>" ;
							print _("Alternate Email") ;
						print "</td>" ;
						print "<td>" ;
							print $row["emailAlternate"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["emailAlternate"]!=$row["newemailAlternate"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newemailAlternate"] ;
							print "</span>" ;
						print "</td>" ;
						print "<td>" ;
							if ($row["emailAlternate"]!=$row["newemailAlternate"]) { print "<input checked type='checkbox' name='newemailAlternateOn'><input name='newemailAlternate' type='hidden' value='" . htmlprep($row["newemailAlternate"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					print "<tr class='odd'>" ;
						print "<td>" ;
							print _("Address 1") ;
						print "</td>" ;
						print "<td>" ;
							print $row["address1"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["address1"]!=$row["newaddress1"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newaddress1"] ;
							print "</span>" ;
						print "</td>" ;
						print "<td>" ;
							if ($row["address1"]!=$row["newaddress1"]) { print "<input checked type='checkbox' name='newaddress1On'><input name='newaddress1' type='hidden' value='" . htmlprep($row["newaddress1"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					print "<tr class='even'>" ;
						print "<td>" ;
							print _("Address 1 District") ;
						print "</td>" ;
						print "<td>" ;
							print $row["address1District"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["address1District"]!=$row["newaddress1District"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newaddress1District"] ;
							print "</span>" ;
						print "</td>" ;
						print "<td>" ;
							if ($row["address1District"]!=$row["newaddress1District"]) { print "<input checked type='checkbox' name='newaddress1DistrictOn'><input name='newaddress1District' type='hidden' value='" . htmlprep($row["newaddress1District"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					print "<tr class='odd'>" ;
						print "<td>" ;
							print _("Address 1 Country") ;
						print "</td>" ;
						print "<td>" ;
							print $row["address1Country"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["address1Country"]!=$row["newaddress1Country"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newaddress1Country"] ;
							print "</span>" ;
						print "</td>" ;
						print "<td>" ;
							if ($row["address1Country"]!=$row["newaddress1Country"]) { print "<input checked type='checkbox' name='newaddress1CountryOn'><input name='newaddress1Country' type='hidden' value='" . htmlprep($row["newaddress1Country"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					print "<tr class='even'>" ;
						print "<td>" ;
							print _("Address 2") ;
						print "</td>" ;
						print "<td>" ;
							print $row["address2"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["address2"]!=$row["newaddress2"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newaddress2"] ;
							print "</span>" ;
						print "</td>" ;
						print "<td>" ;
							if ($row["address2"]!=$row["newaddress2"]) { print "<input checked type='checkbox' name='newaddress2On'><input name='newaddress2' type='hidden' value='" . htmlprep($row["newaddress2"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					print "<tr class='odd'>" ;
						print "<td>" ;
							print _("Address 2 District") ;
						print "</td>" ;
						print "<td>" ;
							print $row["address2District"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["address2District"]!=$row["newaddress2District"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newaddress2District"] ;
							print "</span>" ;
						print "</td>" ;
						print "<td>" ;
							if ($row["address2District"]!=$row["newaddress2District"]) { print "<input checked type='checkbox' name='newaddress2DistrictOn'><input name='newaddress2District' type='hidden' value='" . htmlprep($row["newaddress2District"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					print "<tr class='even'>" ;
						print "<td>" ;
							print _("Address 2 Country") ;
						print "</td>" ;
						print "<td>" ;
							print $row["address2Country"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["address2Country"]!=$row["newaddress2Country"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newaddress2Country"] ;
							print "</span>" ;
						print "</td>" ;
						print "<td>" ;
							if ($row["address2Country"]!=$row["newaddress2Country"]) { print "<input checked type='checkbox' name='newaddress2CountryOn'><input name='newaddress2Country' type='hidden' value='" . htmlprep($row["newaddress2Country"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					$phoneCount=0 ;
					for ($i=1; $i<5; $i++) {
						$phoneCount++ ;
						$class="odd" ;
						if ($phoneCount%2==0) {
							$class="even" ;
						}
						print "<tr class='$class'>" ;
							print "<td>" ;
								print sprintf(_('Phone %1$s Type'), $i) ;
							print "</td>" ;
							print "<td>" ;
								print $row["phone" . $i . "Type"] ;
							print "</td>" ;
							print "<td>" ;
								$style="" ;
								if ($row["phone" . $i . "Type"]!=$row["newphone" . $i . "Type"]) {
									$style="style='color: #ff0000'" ;
								}
								print "<span $style>" ;
								print $row["newphone" . $i . "Type"] ;
								print "</span>" ;
							print "</td>" ;
							print "<td>" ;
								if ($row["phone" . $i . "Type"]!=$row["newphone" . $i . "Type"]) { print "<input checked type='checkbox' name='newphone" . $i . "TypeOn'><input name='newphone" . $i . "Type' type='hidden' value='" . htmlprep($row["newphone" . $i . "Type"]) . "'>" ; }
							print "</td>" ;
						print "</tr>" ;
						$phoneCount++ ;
						$class="odd" ;
						if ($phoneCount%2==0) {
							$class="even" ;
						}
						print "<tr class='$class'>" ;
							print "<td>" ;
								print sprintf(_('Phone %1$s Country Code'), $i) ;
							print "</td>" ;
							print "<td>" ;
								print $row["phone" . $i . "CountryCode"] ;
							print "</td>" ;
							print "<td>" ;
								$style="" ;
								if ($row["phone" . $i . "CountryCode"]!=$row["newphone" . $i . "CountryCode"]) {
									$style="style='color: #ff0000'" ;
								}
								print "<span $style>" ;
								print $row["newphone" . $i . "CountryCode"] ;
								print "</span>" ;
							print "</td>" ;
							print "<td>" ;
								if ($row["phone" . $i . "CountryCode"]!=$row["newphone" . $i . "CountryCode"]) { print "<input checked type='checkbox' name='newphone" . $i . "CountryCodeOn'><input name='newphone" . $i . "CountryCode' type='hidden' value='" . htmlprep($row["newphone" . $i . "CountryCode"]) . "'>" ; }
							print "</td>" ;
						print "</tr>" ;
						$phoneCount++ ;
						$class="odd" ;
						if ($phoneCount%2==0) {
							$class="even" ;
						}
						print "<tr class='$class'>" ;
							print "<td>" ;
								print _("Phone") . " " . $i ;
							print "</td>" ;
							print "<td>" ;
								print $row["phone" . $i] ;
							print "</td>" ;
							print "<td>" ;
								$style="" ;
								if ($row["phone" . $i]!=$row["newphone" . $i]) {
									$style="style='color: #ff0000'" ;
								}
								print "<span $style>" ;
								print $row["newphone" . $i] ;
								print "</span>" ;
							print "</td>" ;
							print "<td>" ;
								if ($row["phone" . $i]!=$row["newphone" . $i]) { print "<input checked type='checkbox' name='newphone" . $i . "On'><input name='newphone" . $i . "' type='hidden' value='" . htmlprep($row["newphone" . $i]) . "'>" ; }
							print "</td>" ;
						print "</tr>" ;
					}
					print "<tr class='odd'>" ;
						print "<td>" ;
							print _("First Language") ;
						print "</td>" ;
						print "<td>" ;
							print $row["languageFirst"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["languageFirst"]!=$row["newlanguageFirst"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newlanguageFirst"] ;
							print "</span>" ;
						print "</td>" ;
						print "<td>" ;
							if ($row["languageFirst"]!=$row["newlanguageFirst"]) { print "<input checked type='checkbox' name='newlanguageFirstOn'><input name='newlanguageFirst' type='hidden' value='" . htmlprep($row["newlanguageFirst"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					print "<tr class='even'>" ;
						print "<td>" ;
							print _("Second Language") ;
						print "</td>" ;
						print "<td>" ;
							print $row["languageSecond"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["languageSecond"]!=$row["newlanguageSecond"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newlanguageSecond"] ;
							print "</span>" ;
						print "</td>" ;
						print "<td>" ;
							if ($row["languageSecond"]!=$row["newlanguageSecond"]) { print "<input checked type='checkbox' name='newlanguageSecondOn'><input name='newlanguageSecond' type='hidden' value='" . htmlprep($row["newlanguageSecond"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					print "<tr class='odd'>" ;
						print "<td>" ;
							print _("Third Language") ;
						print "</td>" ;
						print "<td>" ;
							print $row["languageThird"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["languageThird"]!=$row["newlanguageThird"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newlanguageThird"] ;
							print "</span>" ;
						print "</td>" ;
						print "<td>" ;
							if ($row["languageThird"]!=$row["newlanguageThird"]) { print "<input checked type='checkbox' name='newlanguageThirdOn'><input name='newlanguageThird' type='hidden' value='" . htmlprep($row["newlanguageThird"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					
					print "<tr class='even'>" ;
						print "<td>" ;
							print _("Country of Birth") ;
						print "</td>" ;
						print "<td>" ;
							print $row["countryOfBirth"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["countryOfBirth"]!=$row["newcountryOfBirth"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newcountryOfBirth"] ;
							print "</span>" ;
						print "</td>" ;
						print "<td>" ;
							if ($row["countryOfBirth"]!=$row["newcountryOfBirth"]) { print "<input checked type='checkbox' name='newcountryOfBirthOn'><input name='newcountryOfBirth' type='hidden' value='" . htmlprep($row["newcountryOfBirth"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					print "<tr class='odd'>" ;
						print "<td>" ;
							print _("Ethnicity") ;
						print "</td>" ;
						print "<td>" ;
							print $row["ethnicity"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["ethnicity"]!=$row["newethnicity"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newethnicity"] ;
							print "</span>" ;
						print "</td>" ;
						print "<td>" ;
							if ($row["ethnicity"]!=$row["newethnicity"]) { print "<input checked type='checkbox' name='newethnicityOn'><input name='newethnicity' type='hidden' value='" . htmlprep($row["newethnicity"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					print "<tr class='even'>" ;
						print "<td>" ;
							print _("Citizenship 1") ;
						print "</td>" ;
						print "<td>" ;
							print $row["citizenship1"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["citizenship1"]!=$row["newcitizenship1"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newcitizenship1"] ;
							print "</span>" ;
						print "</td>" ;
						print "<td>" ;
							if ($row["citizenship1"]!=$row["newcitizenship1"]) { print "<input checked type='checkbox' name='newcitizenship1On'><input name='newcitizenship1' type='hidden' value='" . htmlprep($row["newcitizenship1"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					print "<tr class='odd'>" ;
						print "<td>" ;
							print _("Citizenship 1 Passport") ;
						print "</td>" ;
						print "<td>" ;
							print $row["citizenship1Passport"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["citizenship1Passport"]!=$row["newcitizenship1Passport"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newcitizenship1Passport"] ;
							print "</span>" ;
						print "</td>" ;
						print "<td>" ;
							if ($row["citizenship1Passport"]!=$row["newcitizenship1Passport"]) { print "<input checked type='checkbox' name='newcitizenship1PassportOn'><input name='newcitizenship1Passport' type='hidden' value='" . htmlprep($row["newcitizenship1Passport"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					print "<tr class='even'>" ;
						print "<td>" ;
							print _("Citizenship 2") ;
						print "</td>" ;
						print "<td>" ;
							print $row["citizenship2"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["citizenship2"]!=$row["newcitizenship2"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newcitizenship2"] ;
							print "</span>" ;
						print "</td>" ;
						print "<td>" ;
							if ($row["citizenship2"]!=$row["newcitizenship2"]) { print "<input checked type='checkbox' name='newcitizenship2On'><input name='newcitizenship2' type='hidden' value='" . htmlprep($row["newcitizenship2"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					print "<tr class='odd'>" ;
						print "<td>" ;
							print _("Citizenship 2 Passport") ;
						print "</td>" ;
						print "<td>" ;
							print $row["citizenship2Passport"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["citizenship2Passport"]!=$row["newcitizenship2Passport"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newcitizenship2Passport"] ;
							print "</span>" ;
						print "</td>" ;
						print "<td>" ;
							if ($row["citizenship2Passport"]!=$row["newcitizenship2Passport"]) { print "<input checked type='checkbox' name='newcitizenship2PassportOn'><input name='newcitizenship2Passport' type='hidden' value='" . htmlprep($row["newcitizenship2Passport"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					print "<tr class='even'>" ;
						print "<td>" ;
							print _("Religion") ;
						print "</td>" ;
						print "<td>" ;
							print $row["religion"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["religion"]!=$row["newreligion"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newreligion"] ;
							print "</span>" ;
						print "</td>" ;
						print "<td>" ;
							if ($row["religion"]!=$row["newreligion"]) { print "<input checked type='checkbox' name='newreligionOn'><input name='newreligion' type='hidden' value='" . htmlprep($row["newreligion"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					print "<tr class='odd'>" ;
						print "<td>" ;
							print _("National ID Card Number") ;
						print "</td>" ;
						print "<td>" ;
							print $row["nationalIDCardNumber"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["nationalIDCardNumber"]!=$row["newnationalIDCardNumber"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newnationalIDCardNumber"] ;
							print "</span>" ;
						print "</td>" ;
						print "<td>" ;
							if ($row["nationalIDCardNumber"]!=$row["newnationalIDCardNumber"]) { print "<input checked type='checkbox' name='newnationalIDCardNumberOn'><input name='newnationalIDCardNumber' type='hidden' value='" . htmlprep($row["newnationalIDCardNumber"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					print "<tr class='even'>" ;
						print "<td>" ;
							print _("Residency Status") ;
						print "</td>" ;
						print "<td>" ;
							print $row["residencyStatus"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["residencyStatus"]!=$row["newresidencyStatus"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newresidencyStatus"] ;
							print "</span>" ;
						print "</td>" ;
						print "<td>" ;
							if ($row["residencyStatus"]!=$row["newresidencyStatus"]) { print "<input checked type='checkbox' name='newresidencyStatusOn'><input name='newresidencyStatus' type='hidden' value='" . htmlprep($row["newresidencyStatus"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					print "<tr class='odd'>" ;
						print "<td>" ;
							print _("Visa Expiry Date") ;
						print "</td>" ;
						print "<td>" ;
							print dateConvertBack($guid, $row["visaExpiryDate"]) ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["visaExpiryDate"]!=$row["newvisaExpiryDate"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print dateConvertBack($guid, $row["newvisaExpiryDate"]) ;
							print "</span>" ;
						print "</td>" ;
						print "<td>" ;
							if ($row["visaExpiryDate"]!=$row["newvisaExpiryDate"]) { print "<input checked type='checkbox' name='newvisaExpiryDateOn'><input name='newvisaExpiryDate' type='hidden' value='" . htmlprep($row["newvisaExpiryDate"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					print "<tr class='even'>" ;
						print "<td>" ;
							print _("Profession") ;
						print "</td>" ;
						print "<td>" ;
							print $row["profession"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["profession"]!=$row["newprofession"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newprofession"] ;
							print "</span>" ;
						print "</td>" ;
						print "<td>" ;
							if ($row["profession"]!=$row["newprofession"]) { print "<input checked type='checkbox' name='newprofessionOn'><input name='newprofession' type='hidden' value='" . htmlprep($row["newprofession"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					print "<tr class='odd'>" ;
						print "<td>" ;
							print _("Employer") ;
						print "</td>" ;
						print "<td>" ;
							print $row["employer"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["employer"]!=$row["newemployer"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newemployer"] ;
							print "</span>" ;
						print "</td>" ;
						print "<td>" ;
							if ($row["employer"]!=$row["newemployer"]) { print "<input checked type='checkbox' name='newemployerOn'><input name='newemployer' type='hidden' value='" . htmlprep($row["newemployer"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					print "<tr class='even'>" ;
						print "<td>" ;
							print _("Job Title") ;
						print "</td>" ;
						print "<td>" ;
							print $row["jobTitle"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["jobTitle"]!=$row["newjobTitle"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newjobTitle"] ;
							print "</span>" ;
						print "</td>" ;
						print "<td>" ;
							if ($row["jobTitle"]!=$row["newjobTitle"]) { print "<input checked type='checkbox' name='newjobTitleOn'><input name='newjobTitle' type='hidden' value='" . htmlprep($row["newjobTitle"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					print "<tr class='odd'>" ;
						print "<td>" ;
							print _("Emergency 1 Name") ;
						print "</td>" ;
						print "<td>" ;
							print $row["emergency1Name"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["emergency1Name"]!=$row["newemergency1Name"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newemergency1Name"] ;
							print "</span>" ;
						print "</td>" ;
						print "<td>" ;
							if ($row["emergency1Name"]!=$row["newemergency1Name"]) { print "<input checked type='checkbox' name='newemergency1NameOn'><input name='newemergency1Name' type='hidden' value='" . htmlprep($row["newemergency1Name"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					print "<tr class='even'>" ;
						print "<td>" ;
							print _("Emergency 1 Number 1") ;
						print "</td>" ;
						print "<td>" ;
							print $row["emergency1Number1"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["emergency1Number1"]!=$row["newemergency1Number1"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newemergency1Number1"] ;
							print "</span>" ;
						print "</td>" ;
						print "<td>" ;
							if ($row["emergency1Number1"]!=$row["newemergency1Number1"]) { print "<input checked type='checkbox' name='newemergency1Number1On'><input name='newemergency1Number1' type='hidden' value='" . htmlprep($row["newemergency1Number1"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					print "<tr class='odd'>" ;
						print "<td>" ;
							print _("Emergency 1 Number 2") ;
						print "</td>" ;
						print "<td>" ;
							print $row["emergency1Number2"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["emergency1Number2"]!=$row["newemergency1Number2"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newemergency1Number2"] ;
							print "</span>" ;
						print "</td>" ;
						print "<td>" ;
							if ($row["emergency1Number2"]!=$row["newemergency1Number2"]) { print "<input checked type='checkbox' name='newemergency1Number2On'><input name='newemergency1Number2' type='hidden' value='" . htmlprep($row["newemergency1Number2"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					print "<tr class='even'>" ;
						print "<td>" ;
							print _("Emergency 1 Relationship") ;
						print "</td>" ;
						print "<td>" ;
							print $row["emergency1Relationship"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["emergency1Relationship"]!=$row["newemergency1Relationship"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newemergency1Relationship"] ;
							print "</span>" ;
						print "</td>" ;
						print "<td>" ;
							if ($row["emergency1Relationship"]!=$row["newemergency1Relationship"]) { print "<input checked type='checkbox' name='newemergency1RelationshipOn'><input name='newemergency1Relationship' type='hidden' value='" . htmlprep($row["newemergency1Relationship"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					print "<tr class='odd'>" ;
						print "<td>" ;
							print _("Emergency 2 Name") ;
						print "</td>" ;
						print "<td>" ;
							print $row["emergency2Name"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["emergency2Name"]!=$row["newemergency2Name"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newemergency2Name"] ;
							print "</span>" ;
						print "</td>" ;
						print "<td>" ;
							if ($row["emergency2Name"]!=$row["newemergency2Name"]) { print "<input checked type='checkbox' name='newemergency2NameOn'><input name='newemergency2Name' type='hidden' value='" . htmlprep($row["newemergency2Name"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					print "<tr class='even'>" ;
						print "<td>" ;
							print _("Emergency 2 Number 1") ;
						print "</td>" ;
						print "<td>" ;
							print $row["emergency2Number1"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["emergency2Number1"]!=$row["newemergency2Number1"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newemergency2Number1"] ;
							print "</span>" ;
						print "</td>" ;
						print "<td>" ;
							if ($row["emergency2Number1"]!=$row["newemergency2Number1"]) { print "<input checked type='checkbox' name='newemergency2Number1On'><input name='newemergency2Number1' type='hidden' value='" . htmlprep($row["newemergency2Number1"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					print "<tr class='odd'>" ;
						print "<td>" ;
							print _("Emergency 2 Number 2") ;
						print "</td>" ;
						print "<td>" ;
							print $row["emergency2Number2"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["emergency2Number2"]!=$row["newemergency2Number2"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newemergency2Number2"] ;
							print "</span>" ;
						print "</td>" ;
						print "<td>" ;
							if ($row["emergency2Number2"]!=$row["newemergency2Number2"]) { print "<input checked type='checkbox' name='newemergency2Number2On'><input name='newemergency2Number2' type='hidden' value='" . htmlprep($row["newemergency2Number2"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					print "<tr class='even'>" ;
						print "<td>" ;
							print _("Emergency 2 Relationship") ;
						print "</td>" ;
						print "<td>" ;
							print $row["emergency2Relationship"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["emergency2Relationship"]!=$row["newemergency2Relationship"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newemergency2Relationship"] ;
							print "</span>" ;
						print "</td>" ;
						print "<td>" ;
							if ($row["emergency2Relationship"]!=$row["newemergency2Relationship"]) { print "<input checked type='checkbox' name='newemergency2RelationshipOn'><input name='newemergency2Relationship' type='hidden' value='" . htmlprep($row["newemergency2Relationship"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					print "<tr class='odd'>" ;
						print "<td>" ;
							print _("Vehicle Registration") ;
						print "</td>" ;
						print "<td>" ;
							print $row["vehicleRegistration"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["vehicleRegistration"]!=$row["newvehicleRegistration"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newvehicleRegistration"] ;
							print "</span>" ;
						print "</td>" ;
						print "<td>" ;
							if ($row["vehicleRegistration"]!=$row["newvehicleRegistration"]) { print "<input checked type='checkbox' name='newvehicleRegistrationOn'><input name='newvehicleRegistration' type='hidden' value='" . htmlprep($row["newvehicleRegistration"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					//Check if any roles are "Student"
					$privacySet=false ;
					try {
						$dataRoles=array("gibbonPersonID"=>$row["gibbonPersonID"]); 
						$sqlRoles="SELECT gibbonRoleIDAll FROM gibbonperson WHERE gibbonPersonID=:gibbonPersonID" ;
						$resultRoles=$connection2->prepare($sqlRoles);
						$resultRoles->execute($dataRoles);
					}
					catch(PDOException $e) { }
					if ($resultRoles->rowCount()==1) {
						$rowRoles=$resultRoles->fetch() ;
					
						$isStudent=false ;
						$roles=explode(",", $rowRoles["gibbonRoleIDAll"]) ;
						foreach ($roles as $role) {
							if (getRoleCategory($role, $connection2)=="Student") {
								$isStudent=true ;
							}
						}
						if ($isStudent) {
							$privacySetting=getSettingByScope( $connection2, "User Admin", "privacy" ) ;
							$privacyBlurb=getSettingByScope( $connection2, "User Admin", "privacyBlurb" ) ;
							if ($privacySetting=="Y" AND $privacyBlurb!="") {
								print "<tr class='even'>" ;
									print "<td>" ;
										print _("Image Privacy") ;
									print "</td>" ;
									print "<td>" ;
										print $row["privacy"] ;
									print "</td>" ;
									print "<td>" ;
										$style="" ;
										if ($row["privacy"]!=$row["newprivacy"]) {
											$style="style='color: #ff0000'" ;
										}
										print "<span $style>" ;
										print $row["newprivacy"] ;
										print "</span>" ;
									print "</td>" ;
									print "<td>" ;
										if ($row["privacy"]!=$row["newprivacy"]) { print "<input checked type='checkbox' name='newprivacyOn'><input name='newprivacy' type='hidden' value='" . htmlprep($row["newprivacy"]) . "'>" ; }
									print "</td>" ;
								print "</tr>" ;
								$privacySet=true ;
							}
						}
					}
					if ($privacySet==false) {
						print "<input type=\"hidden\" name=\"newprivacyOn\" value=\"\">" ;
					}
					
					print "<tr>" ;
							print "<td class='right' colspan=4>" ;
								print "<input name='gibbonPersonID' type='hidden' value='" . $row["gibbonPersonID"] . "'>" ;
								print "<input name='address' type='hidden' value='" . $_GET["q"] . "'>" ;
								print "<input type='submit' value='Submit'>" ;
							print "</td>" ;
						print "</tr>" ;
				print "</table>" ;
				?>
			</form>
			<?php
		}
	}
}
?>