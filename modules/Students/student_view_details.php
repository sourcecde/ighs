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

//Set timezone from session variable
date_default_timezone_set($_SESSION[$guid]["timezone"]);

if (isActionAccessible($guid, $connection2, "/modules/Students/student_view_details.php")==FALSE) {
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
		$gibbonPersonID=$_GET["gibbonPersonID"] ;
		$search=NULL ;
		if (isset($_GET["search"])) {
			$search=$_GET["search"] ;
		}
		$allStudents="" ;
		if (isset($_GET["allStudents"])) {
			$allStudents=$_GET["allStudents"] ;
		}
		
		if ($gibbonPersonID==FALSE) {
			print "<div class='error'>" ;
			print _("You have not specified one or more required parameters.") ;
			print "</div>" ;
		}
		else {
			$skipBrief=FALSE ;
			//Test if View Student Profile_brief and View Student Profile_myChildren are both available and parent has access to this student...if so, skip brief, and go to full. 
			if (isActionAccessible($guid, $connection2, "/modules/Students/student_view_details.php", "View Student Profile_brief") AND isActionAccessible($guid, $connection2, "/modules/Students/student_view_details.php", "View Student Profile_myChildren")) {
				try {
					$data=array("gibbonSchoolYearID"=>$_SESSION[$guid]["gibbonSchoolYearID"], "gibbonPersonID1"=>$_GET["gibbonPersonID"], "gibbonPersonID2"=>$_SESSION[$guid]["gibbonPersonID"]); 
					$sql="SELECT * FROM gibbonfamilychild JOIN gibbonfamily ON (gibbonfamilychild.gibbonFamilyID=gibbonfamily.gibbonFamilyID) JOIN gibbonfamilyadult ON (gibbonfamilyadult.gibbonFamilyID=gibbonfamily.gibbonFamilyID) JOIN gibbonperson ON (gibbonfamilychild.gibbonPersonID=gibbonperson.gibbonPersonID) JOIN gibbonstudentenrolment ON (gibbonperson.gibbonPersonID=gibbonstudentenrolment.gibbonPersonID) WHERE gibbonSchoolYearID=:gibbonSchoolYearID AND gibbonperson.status='Full' AND (dateStart IS NULL OR dateStart<='" . date("Y-m-d") . "') AND (dateEnd IS NULL  OR dateEnd>='" . date("Y-m-d") . "') AND gibbonfamilychild.gibbonPersonID=:gibbonPersonID1 AND gibbonfamilyadult.gibbonPersonID=:gibbonPersonID2 AND childDataAccess='Y'" ;
					$result=$connection2->prepare($sql);
					$result->execute($data);
				}
				catch(PDOException $e) { }
				if ($result->rowCount()==1) {
					$skipBrief=TRUE ;
				}
			}
		
			if ($highestAction=="View Student Profile_brief" AND $skipBrief==FALSE) {
				//Proceed!
				try {
					$data=array("gibbonSchoolYearID"=>$_SESSION[$guid]["gibbonSchoolYearID"], "gibbonPersonID"=>$gibbonPersonID); 
					$sql="SELECT * FROM gibbonperson JOIN gibbonstudentenrolment ON (gibbonperson.gibbonPersonID=gibbonstudentenrolment.gibbonPersonID) WHERE gibbonSchoolYearID=:gibbonSchoolYearID AND status='Full' AND (dateStart IS NULL OR dateStart<='" . date("Y-m-d") . "') AND (dateEnd IS NULL  OR dateEnd>='" . date("Y-m-d") . "') AND gibbonperson.gibbonPersonID=:gibbonPersonID" ;
					$result=$connection2->prepare($sql);
					$result->execute($data);
				}
				catch(PDOException $e) { 
					print "<div class='error'>" . $e->getMessage() . "</div>" ; 
				}

				//if ($result->rowCount()!=1) {
				//	print "<div class='error'>" ;
				//	print _("The selected record does not exist, or you do not have access to it.") ;
				//	print "</div>" ;
				//}
				//else {
					$row=$result->fetch() ;
					
					print "<div class='trail'>" ;
					print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . _("Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . _(getModuleName($_GET["q"])) . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/student_view.php'>" . _('View Student Profiles') . "</a> > </div><div class='trailEnd'>" . formatName("", $row["preferredName"], $row["surname"], "Student") . "</div>" ;
					print "</div>" ;
					
					print "<table class='smallIntBorder' cellspacing='0' style='width: 100%'>" ;
						print "<tr>" ;
							print "<td style='width: 33%; vertical-align: top'>" ;
								print "<span style='font-size: 115%; font-weight: bold'>" . _('Class') . "</span><br/>" ;
								try {
									$dataDetail=array("gibbonYearGroupID"=>$row["gibbonYearGroupID"]); 
									$sqlDetail="SELECT * FROM gibbonyeargroup WHERE gibbonYearGroupID=:gibbonYearGroupID" ;
									$resultDetail=$connection2->prepare($sqlDetail);
									$resultDetail->execute($dataDetail);
								}
								catch(PDOException $e) { 
									print "<div class='error'>" . $e->getMessage() . "</div>" ; 
								}
								if ($resultDetail->rowCount()==1) {
									$rowDetail=$resultDetail->fetch() ;
									print _($rowDetail["name"]) ;
								}
							print "</td>" ;
							print "<td style='width: 34%; vertical-align: top'>" ;
								print "<span style='font-size: 115%; font-weight: bold'>" . _('Section') . "</span><br/>" ;
								try {
									$dataDetail=array("gibbonRollGroupID"=>$row["gibbonRollGroupID"]); 
									$sqlDetail="SELECT * FROM gibbonrollgroup WHERE gibbonRollGroupID=:gibbonRollGroupID" ;
									$resultDetail=$connection2->prepare($sqlDetail);
									$resultDetail->execute($dataDetail);
								}
								catch(PDOException $e) { 
									print "<div class='error'>" . $e->getMessage() . "</div>" ; 
								}
								if ($resultDetail->rowCount()==1) {
									$rowDetail=$resultDetail->fetch() ;
									print SectionFormater($rowDetail["name"]) ;
								}
							print "</td>" ;
							print "<td style='width: 34%; vertical-align: top'>" ;
								print "<span style='font-size: 115%; font-weight: bold'>" . _('House') . "</span><br/>" ;
								try {
									$dataDetail=array("gibbonHouseID"=>$row["gibbonHouseID"]); 
									$sqlDetail="SELECT * FROM gibbonhouse WHERE gibbonHouseID=:gibbonHouseID" ;
									$resultDetail=$connection2->prepare($sqlDetail);
									$resultDetail->execute($dataDetail);
								}
								catch(PDOException $e) { 
									print "<div class='error'>" . $e->getMessage() . "</div>" ; 
								}
								if ($resultDetail->rowCount()==1) {
									$rowDetail=$resultDetail->fetch() ;
									print $rowDetail["name"] ;
								}
							print "</td>" ;
						print "</tr>" ;
						print "<tr>" ;
							print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
								print "<span style='font-size: 115%; font-weight: bold'>" . _('Email') . "</span><br/>" ;
								if ($row["email"]!="") {
									print "<i><a href='mailto:" . $row["email"] . "'>" . $row["email"] . "</a></i>" ;
								}
							print "</td>" ;
							print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
								//print "<span style='font-size: 115%; font-weight: bold'>" . _('Website') . "</span><br/>" ;
								if ($row["website"]!="") {
									//print "<i><a href='" . $row["website"] . "'>" . $row["website"] . "</a></i>" ;
								}
							print "</td>" ;
							print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
								print "<span style='font-size: 115%; font-weight: bold'>" . _('Account Number') . "</span><br/>" ;
								//if ($row["studentID"]!="") {
									print "<i>" . $row["account_number"] . "</a></i>" ;
								//}
							print "</td>" ;
						print "</tr>" ;
					print "</table>" ;
						
					$extendedBriefProfile=getSettingByScope( $connection2, "Students", "extendedBriefProfile" ) ;
					if ($extendedBriefProfile=="Y") {
						print "<h3>" ;
							print _("Family Details") ;
						print "</h3>" ;
						
						try {
							$dataFamily=array("gibbonPersonID"=>$gibbonPersonID); 
							$sqlFamily="SELECT * FROM gibbonfamily JOIN gibbonfamilychild ON (gibbonfamily.gibbonFamilyID=gibbonfamilychild.gibbonFamilyID) WHERE gibbonPersonID=:gibbonPersonID" ;
							$resultFamily=$connection2->prepare($sqlFamily);
							$resultFamily->execute($dataFamily);
						}
						catch(PDOException $e) { 
							print "<div class='error'>" . $e->getMessage() . "</div>" ; 
						}
						
						if ($resultFamily->rowCount()<1) {
							print "<div class='error'>" ;
								print _("There are no records to display.");
							print "</div>" ;
						}
						else {
							while ($rowFamily=$resultFamily->fetch()) {
								$count=1 ;
							
								//Get adults
								try {
									$dataMember=array("gibbonFamilyID"=>$rowFamily["gibbonFamilyID"]); 
									$sqlMember="SELECT * FROM gibbonfamilyadult JOIN gibbonperson ON (gibbonfamilyadult.gibbonPersonID=gibbonperson.gibbonPersonID) WHERE gibbonFamilyID=:gibbonFamilyID ORDER BY contactPriority, surname, preferredName" ;
									$resultMember=$connection2->prepare($sqlMember);
									$resultMember->execute($dataMember);
								}
								catch(PDOException $e) { 
									print "<div class='error'>" . $e->getMessage() . "</div>" ; 
								}
						
								while ($rowMember=$resultMember->fetch()) {
									print "<h4>" ;
									print _("Adult") . $count ;
									print "</h4>" ;
									print "<table class='smallIntBorder' cellspacing='0' style='width: 100%'>" ;
										print "<tr>" ;
											print "<td style='width: 33%; vertical-align: top'>" ;
												print "<span style='font-size: 115%; font-weight: bold'>" . _('Name') . "</span><br/>" ;
												print formatName($rowMember["title"], $rowMember["preferredName"], $rowMember["surname"], "Parent") ;
											print "</td>" ;
											print "<td style='width: 33%; vertical-align: top'>" ;
												print "<span style='font-size: 115%; font-weight: bold'>" . _('First Language') . "</span><br/>" ;
												print $rowMember["languageFirst"] ;
											print "</td>" ;
											print "<td style='width: 34%; vertical-align: top' colspan=2>" ;
												print "<span style='font-size: 115%; font-weight: bold'>" . _('Second Language') . "</span><br/>" ;
												print $rowMember["languageSecond"] ;
											print "</td>" ;
										print "</tr>" ;
										print "<tr>" ;
											print "<td style='width: 33%; padding-top: 15px; width: 33%; vertical-align: top'>" ;
												print "<span style='font-size: 115%; font-weight: bold'>" . _('Contact By Phone') . "</span><br/>" ;
												if ($rowMember["contactCall"]=="N") {
													print _("Do not contact by phone.") ;
												}
												else if ($rowMember["contactCall"]=="Y" AND ($rowMember["phone1"]!="" OR $rowMember["phone2"]!="" OR $rowMember["phone3"]!="" OR $rowMember["phone4"]!="")) {
													for ($i=1; $i<5; $i++) {
														if ($rowMember["phone" . $i]!="") {
															if ($rowMember["phone" . $i . "Type"]!="") {
																print $rowMember["phone" . $i . "Type"] . ":</i> " ;
															}
															if ($rowMember["phone" . $i . "CountryCode"]!="") {
																print "+" . $rowMember["phone" . $i . "CountryCode"] . " " ;
															}
															print formatPhone($rowMember["phone" . $i]) . "<br/>" ;
														}
													}
												}
											print "</td>" ;
											print "<td style='width: 33%; padding-top: 15px; width: 34%; vertical-align: top' colspan=2>" ;
												print "<span style='font-size: 115%; font-weight: bold'>" . _('Contact By Email') . "</span><br/>" ;
												if ($rowMember["contactEmail"]=="N") {
													print _("Do not contact by email.") ;
												}
												else if ($rowMember["contactEmail"]=="Y" AND ($rowMember["email"]!="" OR $rowMember["emailAlternate"]!="")) {
													if ($rowMember["email"]!="") {
														print "<a href='mailto:" . $rowMember["email"] . "'>" . $rowMember["email"] . "</a><br/>" ;
													}
													print "<br/>" ;
												}
											print "</td>" ;
										print "</tr>" ;
									print "</table>" ;
									$count++ ;
								}	
							}
						}
					}
					//Set sidebar
					$_SESSION[$guid]["sidebarExtra"]=getUserPhoto($guid, $row["image_240"], 240) ;
				//}
			}
			else {
				try {
					if ($highestAction=="View Student Profile_myChildren") {
						$data=array("gibbonSchoolYearID"=>$_SESSION[$guid]["gibbonSchoolYearID"], "gibbonPersonID1"=>$_GET["gibbonPersonID"], "gibbonPersonID2"=>$_SESSION[$guid]["gibbonPersonID"]); 
						$sql="SELECT * FROM gibbonfamilychild JOIN gibbonfamily ON (gibbonfamilychild.gibbonFamilyID=gibbonfamily.gibbonFamilyID) JOIN gibbonfamilyadult ON (gibbonfamilyadult.gibbonFamilyID=gibbonfamily.gibbonFamilyID) JOIN gibbonperson ON (gibbonfamilychild.gibbonPersonID=gibbonperson.gibbonPersonID) JOIN gibbonstudentenrolment ON (gibbonperson.gibbonPersonID=gibbonstudentenrolment.gibbonPersonID) WHERE gibbonSchoolYearID=:gibbonSchoolYearID AND gibbonperson.status='Full' AND (dateStart IS NULL OR dateStart<='" . date("Y-m-d") . "') AND (dateEnd IS NULL  OR dateEnd>='" . date("Y-m-d") . "') AND gibbonfamilychild.gibbonPersonID=:gibbonPersonID1 AND gibbonfamilyadult.gibbonPersonID=:gibbonPersonID2 AND childDataAccess='Y'" ;
					}
					else {
						if ($allStudents!="on") {
						  //Correction by Shiva - Start
							$var_value = $_SESSION['varname'];
						//	echo $var_value; 
					   	  //$data=array("gibbonSchoolYearID"=>$_SESSION[$guid]["gibbonSchoolYearID"], "gibbonPersonID"=>$gibbonPersonID); 
						  //Correction by Shiva - End
							$data=array("gibbonSchoolYearID"=>$var_value, "gibbonPersonID"=>$gibbonPersonID); 
							//print_r ($data);							
							//$data=array("gibbonSchoolYearID"=>$_SESSION[$guid]["gibbonSchoolYearID"], "gibbonPersonID"=>$gibbonPersonID); 
							$sql="SELECT * FROM gibbonperson JOIN gibbonstudentenrolment ON (gibbonperson.gibbonPersonID=gibbonstudentenrolment.gibbonPersonID) WHERE gibbonSchoolYearID=:gibbonSchoolYearID AND status='Full' AND (dateStart IS NULL OR dateStart<='" . date("Y-m-d") . "') AND (dateEnd IS NULL  OR dateEnd>='" . date("Y-m-d") . "') AND gibbonperson.gibbonPersonID=:gibbonPersonID" ;
						}
						else {
							$data=array("gibbonPersonID"=>$gibbonPersonID); 
							$sql="SELECT DISTINCT gibbonperson.* FROM gibbonperson LEFT JOIN gibbonstudentenrolment ON (gibbonperson.gibbonPersonID=gibbonstudentenrolment.gibbonPersonID) WHERE gibbonperson.gibbonPersonID=:gibbonPersonID" ;
						}
					}
					$result=$connection2->prepare($sql);
					$result->execute($data);
				}
		

		catch(PDOException $e) { 
					print "<div class='error'>" . $e->getMessage() . "</div>" ; 
				}
				
				//if ($result->rowCount()!=1) {
				//	print "<div class='error'>" ;
				//	print _("The selected record does not exist, or you do not have access to it.") ;
				//	print "</div>" ;
				//}
				//else {
					$row=$result->fetch() ;
					
					print "<div class='trail'>" ;
					print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . _("Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . _(getModuleName($_GET["q"])) . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/student_view.php&search=$search&allStudents=$allStudents'>" . _('View Student Profiles') . "</a> > </div><div class='trailEnd'>" . formatName("", $row["preferredName"], $row["surname"], "Student") . "</div>" ;
					print "</div>" ;
					
					$subpage=NULL ;
					if (isset($_GET["subpage"])) {
						$subpage=$_GET["subpage"] ;
					}
					$hook=NULL ;
					if (isset($_GET["hook"])) {
						$hook=$_GET["hook"] ;
					}
					$module=NULL ;
					if (isset($_GET["module"])) {
						$module=$_GET["module"] ;
					}
					$action=NULL ;
					if (isset($_GET["action"])) {
						$action=$_GET["action"] ;
					}
					
					if ($subpage=="" AND ($hook=="" OR $module=="" OR $action=="")) {
						$subpage="Personal" ;
					}
					
					if ($search!="") {
						print "<div class='linkTop'>" ;
							print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Students/student_view.php&search=" . $search . "'>" . _('Back to Search Results') . "</a>" ;
						print "</div>" ;
					}
					
					print "<h2>" ;
						if ($subpage!="") {
							print $subpage ;
						}
						else {
							print $hook ;
						}
					print "</h2>" ;
					
					if ($subpage=="Summary") {
						if (isActionAccessible($guid, $connection2, "/modules/User Admin/user_manage.php")==TRUE) {
							/*print "<div class='linkTop'>" ;
							print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Students/student_view_edit.php&gibbonPersonID=$gibbonPersonID'>" . _('Edit') . "<img style='margin: 0 0 -4px 5px' title='" . _('Edit') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/config.png'/></a> " ;
							print "</div>" ;*/
						}
					
						//Medical alert!
						$alert=getHighestMedicalRisk( $gibbonPersonID, $connection2 ) ;
						if ($alert!=FALSE) {
							$highestLevel=$alert[1] ;
							$highestColour=$alert[3] ;
							$highestColourBG=$alert[4] ;
							print "<div class='error' style='background-color: #" . $highestColourBG . "; border: 1px solid #" . $highestColour . "; color: #" . $highestColour . "'>" ;
							print "<b>" . sprintf(_('This student has one or more %1$s risk medical conditions.'), strToLower(_($highestLevel))) . "</b>." ;
							print "</div>" ;
						}
						
						print "<table class='smallIntBorder' cellspacing='0' style='width: 100%'>" ;
							print "<tr>" ;
								print "<td style='width: 33%; vertical-align: top'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Preferred Name') . "</span><br/>" ;
									print formatName("", $row["preferredName"], $row["surname"], "Student") ;
								print "</td>" ;
								print "<td style='width: 34%; vertical-align: top'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Official Name') . "</span><br/>" ;
									print $row["officialName"] ;
								print "</td>" ;
								print "<td style='width: 34%; vertical-align: top'>" ;
								/*
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Name In Characters') . "</span><br/>" ;
									print $row["nameInCharacters"] ;
									*/
								print "</td>" ;
							print "</tr>" ;
							print "<tr>" ;
								print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Class') . "</span><br/>" ;
									if (isset($row["gibbonYearGroupID"])) {
										try {
											$dataDetail=array("gibbonYearGroupID"=>$row["gibbonYearGroupID"]); 
											$sqlDetail="SELECT * FROM gibbonyeargroup WHERE gibbonYearGroupID=:gibbonYearGroupID" ;
											$resultDetail=$connection2->prepare($sqlDetail);
											$resultDetail->execute($dataDetail);
										}
										catch(PDOException $e) { 
											print "<div class='error'>" . $e->getMessage() . "</div>" ; 
										}
										if ($resultDetail->rowCount()==1) {
											$rowDetail=$resultDetail->fetch() ;
											print _($rowDetail["name"]) ;
											$dayTypeOptions=getSettingByScope($connection2, 'User Admin', 'dayTypeOptions') ;
											if ($dayTypeOptions!="") {
												print " (" . $row["dayType"] . ")" ;
											}
											print "</i>" ;
										}
									}
								print "</td>" ;
								print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Section') . "</span><br/>" ;
									if (isset($row["gibbonRollGroupID"])) {
										try {
											$dataDetail=array("gibbonRollGroupID"=>$row["gibbonRollGroupID"]); 
											$sqlDetail="SELECT * FROM gibbonrollgroup WHERE gibbonRollGroupID=:gibbonRollGroupID" ;
											$resultDetail=$connection2->prepare($sqlDetail);
											$resultDetail->execute($dataDetail);
										}
										catch(PDOException $e) { 
											print "<div class='error'>" . $e->getMessage() . "</div>" ; 
										}
										if ($resultDetail->rowCount()==1) {
											$rowDetail=$resultDetail->fetch() ;
											if (isActionAccessible($guid, $connection2, "/modules/Sections/rollGroups_details.php")) {
												print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Sections/rollGroups_details.php&gibbonRollGroupID=" . $rowDetail["gibbonRollGroupID"] . "'>" . $rowDetail["name"] . "</a>" ;
											}
											else {
												print SectionFormater($rowDetail["name"]) ;
											}
											$primaryTutor=$rowDetail["gibbonPersonIDTutor"] ;
										}
									}
								print "</td>" ;
								print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Tutors') . "</span><br/>" ;
									if (isset($rowDetail["gibbonPersonIDTutor"])) {
										try {
											$dataDetail=array("gibbonPersonIDTutor"=>$rowDetail["gibbonPersonIDTutor"], "gibbonPersonIDTutor2"=>$rowDetail["gibbonPersonIDTutor2"], "gibbonPersonIDTutor3"=>$rowDetail["gibbonPersonIDTutor3"]); 
											$sqlDetail="SELECT gibbonPersonID, title, surname, preferredName FROM gibbonperson WHERE gibbonPersonID=:gibbonPersonIDTutor OR gibbonPersonID=:gibbonPersonIDTutor2 OR gibbonPersonID=:gibbonPersonIDTutor3" ;
											$resultDetail=$connection2->prepare($sqlDetail);
											$resultDetail->execute($dataDetail);
										}
										catch(PDOException $e) { 
											print "<div class='error'>" . $e->getMessage() . "</div>" ; 
										}
										while ($rowDetail=$resultDetail->fetch()) {
											if (isActionAccessible($guid, $connection2, "/modules/Staff/staff_view_details.php")) {
												print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Staff/staff_view_details.php&gibbonPersonID=" . $rowDetail["gibbonPersonID"] . "'>" . formatName("", $rowDetail["preferredName"], $rowDetail["surname"], "Staff", false, true) . "</a>" ;
											}
											else {
												print formatName($rowDetail["title"], $rowDetail["preferredName"], $rowDetail["surname"], "Staff") ;
											}
											if ($rowDetail["gibbonPersonID"]==$primaryTutor AND $resultDetail->rowCount()>1) {
												print " (" . _('Main Tutor') . ")" ;
											}
											print "<br/>" ;
										}
									}
								print "</td>" ;
							print "</tr>" ;
							print "<tr>" ;
								print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Username') . "</span><br/>" ;
									print $row["username"] ;
								print "</td>" ;
								print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Age') . "</span><br/>" ;
									if (is_null($row["dob"])==FALSE AND $row["dob"]!="0000-00-00") {
										print getAge(dateConvertToTimestamp($row["dob"])) ;
									}
								print "</td>" ;
								print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('House') . "</span><br/>" ;
									try {
										$dataDetail=array("gibbonHouseID"=>$row["gibbonHouseID"]); 
										$sqlDetail="SELECT * FROM gibbonhouse WHERE gibbonHouseID=:gibbonHouseID" ;
										$resultDetail=$connection2->prepare($sqlDetail);
										$resultDetail->execute($dataDetail);
									}
									catch(PDOException $e) { 
										print "<div class='error'>" . $e->getMessage() . "</div>" ; 
									}
									if ($resultDetail->rowCount()==1) {
										$rowDetail=$resultDetail->fetch() ;
										print $rowDetail["name"] ;
									}
								print "</td>" ;
							print "</tr>" ;
							print "<tr>" ;
								print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
								//	print "<span style='font-size: 115%; font-weight: bold'>" . _('Website') . "</span><br/>" ;
									if ($row["website"]!="") {
										//print "<i><a href='" . $row["website"] . "'>" . $row["website"] . "</a></i>" ;
									}
								print "</td>" ;
								print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Email') . "</span><br/>" ;
									if ($row["email"]!="") {
										print "<i><a href='mailto:" . $row["email"] . "'>" . $row["email"] . "</a></i>" ;
									}
								print "</td>" ;
								print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;

								print "</td>" ;
							print "</tr>" ;
							print "<tr>" ;
								print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
								/*
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Locker Number') . "</span><br/>" ;
									if ($row["lockerNumber"]!="") {
										print $row["lockerNumber"] ;
									}
									*/
								print "</td>" ;
								print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Account Number') . "</span><br/>" ;
									//if ($row["studentID"]!="") {
										print $row["account_number"] ;
									//}
								print "</td>" ;
								print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
									
								print "</td>" ;
							print "</tr>" ;
							$privacySetting=getSettingByScope( $connection2, "User Admin", "privacy" ) ;
							if ($privacySetting=="Y") {
								print "<tr>" ;
									print "<td style='width: 33%; padding-top: 15px; vertical-align: top' colspan=3>" ;
										print "<span style='font-size: 115%; font-weight: bold'>" . _('Image Privacy') . "</span><br/>" ;
										if ($row["privacy"]!="") {
											print "<span style='color: #cc0000; background-color: #F6CECB'>" ;
												print _("Privacy required:") . " " . $row["privacy"] ;
											print "</span>" ;
										}
										else {
											print "<span style='color: #390; background-color: #D4F6DC;'>" ;
												print _("Privacy not required or not set.") ;
											print "</span>" ;
										}
									
									print "</td>" ;
								print "</tr>" ;
							}
							/* Documents */
								$sql4="SELECT * FROM `studentdocuments` WHERE `gibbonPersonID`=".$gibbonPersonID;
								$result4=$connection2->prepare($sql4);
								$result4->execute();
								$docx=$result4->fetchAll();
								
								print "<tr>" ;
										print "<td style='width: 33%; padding-top: 15px; vertical-align: top' colspan=3>" ;
											print "<span style='font-size: 115%; font-weight: bold'>" . _('Documents List:') . "</span><br/>" ;
												foreach($docx as $d)
													echo " <a target='_blank' href='{$d['name']}'>Document: {$d['label']}</a>";
										print "</td>" ;
								print "</tr>" ;		
							/* Documents */
							
							/* Individual Need*/
							print "<tr>" ;
							print "<td style='width: 33%; padding-top: 15px; vertical-align: top' colspan=3>" ;
							print "<span style='font-size: 115%; font-weight: bold'>" . _('Individual Need:') . "</span><br/>" ;
							include "./modules/Individual Needs/moduleFunctions.php" ;
								
							$statusTable=printINStatusTable($connection2, $gibbonPersonID, "disabled") ;
							if ($statusTable==FALSE) {
								print "<div class='error'>" ;
								print _("Your request failed due to a database error.") ;
								print "</div>" ;
							}
							else {
								print $statusTable ;
							}
							
							print "<h3>" ;
								print _("Individual Education Plan") ;
							print "</h3>" ;
							try {
								$dataIN=array("gibbonPersonID"=>$gibbonPersonID); 
								$sqlIN="SELECT * FROM gibbonin WHERE gibbonPersonID=:gibbonPersonID" ;
								$resultIN=$connection2->prepare($sqlIN);
								$resultIN->execute($dataIN);
							}
							catch(PDOException $e) { 
								print "<div class='error'>" . $e->getMessage() . "</div>" ; 
							}
							
							if ($resultIN->rowCount()!=1) {
								print "<div class='error'>" ;
								print _("There are no records to display.") ;
								print "</div>" ;
							}
							else {
								$rowIN=$resultIN->fetch() ;
								
								print "<div style='font-weight: bold'>" . _('Targets') . "</div>" ;
								print "<p>" . $rowIN["targets"] . "</p>" ;
								
								print "<div style='font-weight: bold; margin-top: 30px'>" . _('Teaching Strategies') . "</div>" ;
								print "<p>" . $rowIN["strategies"] . "</p>" ;
								
								print "<div style='font-weight: bold; margin-top: 30px'>" . _('Notes & Review') . "s</div>" ;
								print "<p>" . $rowIN["notes"] . "</p>" ;
							}
							print "</td>";
							print "</tr>";
							/* Individual Need*/
							$studentAgreementOptions=getSettingByScope( $connection2, "School Admin", "studentAgreementOptions" ) ;
							if ($studentAgreementOptions!="") {
								print "<tr>" ;
									print "<td style='width: 33%; padding-top: 15px; vertical-align: top' colspan=3>" ;
										print "<span style='font-size: 115%; font-weight: bold'>" . _('Student Agreements') . "</span><br/>" ;
										print _("Agreements Signed:") . " " . $row["studentAgreements"] ;
									print "</td>" ;
								print "</tr>" ;
							}
							//Get list of teachers
							try {
								$dataDetail=array("gibbonPersonID"=>$gibbonPersonID); 
								$sqlDetail="SELECT DISTINCT teacher.surname, teacher.preferredName, teacher.email FROM gibbonperson AS teacher JOIN gibboncourseclassperson AS teacherClass ON (teacherClass.gibbonPersonID=teacher.gibbonPersonID)  JOIN gibboncourseclassperson AS studentClass ON (studentClass.gibbonCourseClassID=teacherClass.gibbonCourseClassID) JOIN gibbonperson AS student ON (studentClass.gibbonPersonID=student.gibbonPersonID) JOIN gibboncourseclass ON (studentClass.gibbonCourseClassID=gibboncourseclass.gibbonCourseClassID) JOIN gibboncourse ON (gibboncourseclass.gibbonCourseID=gibboncourse.gibbonCourseID) WHERE teacher.status='Full' AND teacherClass.role='Teacher' AND studentClass.role='Student' AND student.gibbonPersonID=:gibbonPersonID AND gibboncourse.gibbonSchoolYearID=(SELECT gibbonSchoolYearID FROM gibbonschoolyear WHERE status='Current') ORDER BY teacher.preferredName, teacher.surname, teacher.email ;" ;
								$resultDetail=$connection2->prepare($sqlDetail);
								$resultDetail->execute($dataDetail);
							}
							catch(PDOException $e) { 
								print "<div class='error'>" . $e->getMessage() . "</div>" ; 
							}
							if ($resultDetail->rowCount()>0) {
								print "<tr>" ;
									print "<td style='width: 33%; padding-top: 15px; vertical-align: top' colspan=3>" ;
										print "<span style='font-size: 115%; font-weight: bold'>" . _('Teachers') . "</span><br/>" ;
										print "<ul>" ;
											while ($rowDetail=$resultDetail->fetch()) {
												print "<li>" . htmlPrep(formatName("", $rowDetail["preferredName"], $rowDetail["surname"], "Student", FALSE) . " <" . $rowDetail["email"] . ">") . "</li>" ;
											}
										print "</ul>" ;
									print "</td>" ;
								print "</tr>" ;
							}
							
						print "</table>" ;
					}
					else if ($subpage=="Personal") {
						if (isActionAccessible($guid, $connection2, "/modules/User Admin/user_manage.php")==TRUE) {
							//print "<div class='linkTop'>" ;
							//print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/User Admin/user_manage_edit.php&gibbonPersonID=$gibbonPersonID&url=2'>" . _('Edit') . "<img style='margin: 0 0 -4px 5px' title='" . _('Edit') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/config.png'/></a> " ;
							//print "</div>" ;
						}

						print "<h4>" ;
						print _("Basic Information") ;
						print "</h4>" ;
						
						print "<table class='smallIntBorder' cellspacing='0' style='width: 100%'>" ;
							print "<tr>" ;
								print "<td style='width: 33%; vertical-align: top'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Surname') . "</span><br/>" ;
									print $row["surname"] ;
								print "</td>" ;
								print "<td style='width: 33%; vertical-align: top'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('First Name') . "</span><br/>" ;
									print $row["firstName"] ;
								print "</td>" ;
								print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Official Name') . "</span><br/>" ;
									print $row["officialName"] ;
								print "</td>" ;
							print "</tr>" ;
							print "<tr>" ;
								print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Gender') . "</span><br/>" ;
									print $row["gender"]=='M'?'Male':'Female' ;
								print "</td>" ;
								print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Date of Birth') . "</span><br/>" ;
									if (is_null($row["dob"])==FALSE AND $row["dob"]!="0000-00-00") {
										print dateConvertBack($guid, $row["dob"]) ;
									}
								print "</td>" ;
								print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Age') . "</span><br/>" ;
									if (is_null($row["dob"])==FALSE AND $row["dob"]!="0000-00-00") {
										print getAge(dateConvertToTimestamp($row["dob"])) ;
									}
								print "</td>" ;
							print "</tr>" ;
						print "</table>" ;
													
						/*print "<h4>" ;
						print _("School Information") ;
						print "</h4>" ;
						
						print "<table class='smallIntBorder' cellspacing='0' style='width: 100%'>" ;
							print "<tr>" ;
								print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Last School') . "</span><br/>" ;
									print $row["lastSchool"] ;
								print "</td>" ;
								print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Start Date') . "</span><br/>" ;
									print dateConvertBack($guid, $row["dateStart"]) ;
								print "</td>" ;
								print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Start Date') . "</span><br/>" ;
									print dateConvertBack($guid, $row["dateStart"]) ;
								print "</td>" ;
							print "</tr>";	
						print "</table>" ;*/
						
						print "<h4>" ;
						print "School Data" ;
						print "</h4>" ;
						print "<table class='smallIntBorder' cellspacing='0' style='width: 100%'>" ;
							print "<tr>" ;
								print "<td style='width: 33%; vertical-align: top'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Class') . "</span><br/>" ;
									if (isset($row["gibbonYearGroupID"])) {
										try {
											$dataDetail=array("gibbonYearGroupID"=>$row["gibbonYearGroupID"]); 
											$sqlDetail="SELECT * FROM gibbonyeargroup WHERE gibbonYearGroupID=:gibbonYearGroupID" ;
											$resultDetail=$connection2->prepare($sqlDetail);
											$resultDetail->execute($dataDetail);
										}
										catch(PDOException $e) { 
											print "<div class='error'>" . $e->getMessage() . "</div>" ; 
										}
										if ($resultDetail->rowCount()==1) {
											$rowDetail=$resultDetail->fetch() ;
											print _($rowDetail["name"]) ;
										}
									}
								print "</td>" ;
								print "<td style='width: 33%; vertical-align: top'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Section') . "</span><br/>" ;
									if (isset($row["gibbonRollGroupID"])) {
										$sqlDetail="SELECT * FROM gibbonrollgroup WHERE gibbonRollGroupID='" . $row["gibbonRollGroupID"] . "'" ;
										try {
											$dataDetail=array("gibbonRollGroupID"=>$row["gibbonRollGroupID"]); 
											$sqlDetail="SELECT * FROM gibbonrollgroup WHERE gibbonRollGroupID=:gibbonRollGroupID" ;
											$resultDetail=$connection2->prepare($sqlDetail);
											$resultDetail->execute($dataDetail);
										}
										catch(PDOException $e) { 
											print "<div class='error'>" . $e->getMessage() . "</div>" ; 
										}
										if ($resultDetail->rowCount()==1) {
											$rowDetail=$resultDetail->fetch() ;
											if (isActionAccessible($guid, $connection2, "/modules/Sections/rollGroups_details.php")) {
												print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Sections/rollGroups_details.php&gibbonRollGroupID=" . $rowDetail["gibbonRollGroupID"] . "'>" . $rowDetail["name"] . "</a>" ;
											}
											else {
												print SectionFormater($rowDetail["name"]) ;
											}
											$primaryTutor=$rowDetail["gibbonPersonIDTutor"] ;
										}
									}
								print "</td>" ;
								print "<td style='width: 34%; vertical-align: top'>" ;
								print "<span style='font-size: 115%; font-weight: bold'>" . _('Roll No.') . "</span><br/>" ;
								//print $rowDetail['rollOrder'];
								print "</td>" ;
								print "</tr>" ;
								print "<tr>" ;
								print "<td style='width: 34%; vertical-align: top'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Class Teacher') . "</span><br/>" ;
									if (isset($rowDetail["gibbonPersonIDTutor"])) {
										try {
											$dataDetail=array("gibbonPersonIDTutor"=>$rowDetail["gibbonPersonIDTutor"], "gibbonPersonIDTutor2"=>$rowDetail["gibbonPersonIDTutor2"], "gibbonPersonIDTutor3"=>$rowDetail["gibbonPersonIDTutor3"]); 
											$sqlDetail="SELECT gibbonPersonID, title, surname, preferredName FROM gibbonperson WHERE gibbonPersonID=:gibbonPersonIDTutor OR gibbonPersonID=:gibbonPersonIDTutor2 OR gibbonPersonID=:gibbonPersonIDTutor3" ;
											$resultDetail=$connection2->prepare($sqlDetail);
											$resultDetail->execute($dataDetail);
										}
										catch(PDOException $e) { 
											print "<div class='error'>" . $e->getMessage() . "</div>" ; 
										}
										while ($rowDetail=$resultDetail->fetch()) {
											if (isActionAccessible($guid, $connection2, "/modules/Staff/staff_view_details.php")) {
												print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Staff/staff_view_details.php&gibbonPersonID=" . $rowDetail["gibbonPersonID"] . "'>" . formatName("", $rowDetail["preferredName"], $rowDetail["surname"], "Staff", false, true) . "</a>" ;
											}
											else {
												print formatName($rowDetail["title"], $rowDetail["preferredName"], $rowDetail["surname"], "Staff") ;
											}
											if ($rowDetail["gibbonPersonID"]==$primaryTutor AND $resultDetail->rowCount()>1) {
												print " (" . _('Main Tutor') . ")" ;
											}
											print "<br/>" ;
										}
									}
								print "</td>" ;
								print "<td style='padding-top: 15px ; vertical-align: top'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('House') . "</span><br/>" ;
									try {
										$dataDetail=array("gibbonHouseID"=>$row["gibbonHouseID"]); 
										$sqlDetail="SELECT * FROM gibbonhouse WHERE gibbonHouseID=:gibbonHouseID" ;
										$resultDetail=$connection2->prepare($sqlDetail);
										$resultDetail->execute($dataDetail);
									}
									catch(PDOException $e) { 
										print "<div class='error'>" . $e->getMessage() . "</div>" ; 
									}
									if ($resultDetail->rowCount()==1) {
										$rowDetail=$resultDetail->fetch() ;
										print $rowDetail["name"] ;
									}
								print "</td>" ;
								print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
								print "<span style='font-size: 115%; font-weight: bold'>" . _('Boarder Type') . "</span><br/>" ;
								try{
									$sql="SELECT `value` FROM `gibbonsetting` WHERE `gibbonSystemSettingsID`=147";
									$result=$connection2->prepare($sql);
									$result->execute();
									$boarder_enable=$result->fetch();
								}
								catch(PDOException $e){echo $e;}
								if($boarder_enable['value']=='N'){
									print "NA";
								}
								else{
									print $row['boarder'];
								}	
								print "</td>" ;
								print "</tr>";
								print "<tr>";
								print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Account Number') . "</span><br/>" ;
									print substr($row["account_number"],-5) ;
								print "</td>" ;
								print "<td style='width: 34%; vertical-align: top'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Admission Number') . "</span><br/>" ;
									print $row["admission_number"] ;
								print "</td>" ;
								print "<td style='width: 34%; vertical-align: top'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Enrolment Date') . "</span><br/>" ;
									print dateConvertBack($guid, $row["enrollment_date"]) ;
								print "</td>" ;
							print "</tr>" ;
							print "<tr>";
								print "<td style='width: 34%; vertical-align: top'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Start Date') . "</span><br/>" ;
									print dateConvertBack($guid, $row["dateStart"]) ;
								print "</td>" ;
								print "<td colspan='2' style='width: 66%; vertical-align: top'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Last School') . "</span><br/>" ;
									print $row["lastSchool"] ;
								print "</td>" ;
							print "</tr>" ;
						print "</table>" ;
						
						print "<h4>" ;
						print _("Contacts") ;
						print "<span style='color:darkred;font-size:10px;vertical-align:middle;'>&nbsp&nbsp&nbsp&nbsp(NOTE: This is student's personal Contact Information.Go to Family tab for official Contact Information)</span>";
						print "</h4>" ;
						
						print "<table class='smallIntBorder' cellspacing='0' style='width: 100%'>" ;
							$numberCount=0 ;
							print "<tr>" ;
							print "<td width: 33%; style='vertical-align: middle'>" ;
								print "<b>Phone : </b>";
							print "</td>" ;
							if ($row["phone1"]!="" OR $row["phone2"]!="") {
									for ($i=1; $i<3; $i++) {
										if ($row["phone" . $i]!="") {
											$numberCount++ ;
											print "<td width: 33%; style='vertical-align: top'>" ;
												if ($row["phone" . $i . "Type"]!="") {
													//print $row["phone" . $i . "Type"] . ":</i> " ;
													print "<span style='font-size: 115%; font-weight: bold'>" . _($row["phone" . $i . "Type"]) . "</span><br/>" ;
												}
												if ($row["phone" . $i . "CountryCode"]!="") {
													print "+" . $row["phone" . $i . "CountryCode"] . " " ;
												}
												print $row["phone" . $i] . "<br/>" ;
											print "</td>" ;
										}
										else {
											print "<td width: 33%; style='vertical-align: top'>" ;
											print "<span style='font-size: 115%; font-weight: bold;vertical-align:unset;'>" . _('Phone2') . "</span><br/>Not Set" ;
											print "</td>" ;
										}
									}
								print "</tr>" ;
							}
							print "<tr>" ;
								print "<td style='width: 33%; padding-top: 15px; vertical-align: middle'>" ;
									print "<b>" . _('Email : ') . "</b>" ;
								print "</td>";
								if($row["email"]!=''){
								print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Email1') . "</span><br/>" ;
									if ($row["email"]!="") {
										print "<i><a href='mailto:" . $row["email"] . "'>" . $row["email"] . "</a></i>" ;
									}
								print "</td>" ;
								}
								else{
									print "<td style='width: 33%; padding-top: 15px; vertical-align: middle'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Email1') . "</span><br/> Not Set" ;
									print "</td>";
								}
								if($row["emailAlternate"]!=''){
								print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Email2') . "</span><br/>" ;
									if ($row["emailAlternate"]!="") {
										print "<i><a href='mailto:" . $row["emailAlternate"] . "'>" . $row["emailAlternate"] . "</a></i>" ;
									}
								print "</td>" ;
								}
								else{
									print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Email2') . "</span><br/>Not Set" ;
									print "</td>";
								}	
							print "</tr>" ;
							if ($row["address1"]!="") {
								print "<tr>" ;
									print "<td style='width: 33%; padding-top: 15px; vertical-align: top' colspan=4>" ;
										print "<span style='font-size: 115%; font-weight: bold'>" . _('Address 1') . "</span><br/>" ;
										$address1=addressFormat( $row["address1"], $row["address1District"], $row["address1Country"] ) ;
										if ($address1!=FALSE) {
											print $address1 ;
										}
									print "</td>" ;
								print "</tr>" ;
							}
							if ($row["address2"]!="") {
								print "<tr>" ;
									print "<td style='width: 33%; padding-top: 15px; vertical-align: top' colspan=3>" ;
										print "<span style='font-size: 115%; font-weight: bold'>" . _('Address 2') . "</span><br/>" ;
										$address2=addressFormat( $row["address2"], $row["address2District"], $row["address2Country"] ) ;
										if ($address2!=FALSE) {
											print $address2 ;
										}
									print "</td>" ;
								print "</tr>" ;
							}
						print "</table>" ;	
						
						print "<h4>" ;
						print _("Background") ;
						print "</h4>" ;
						
						print "<table class='smallIntBorder' cellspacing='0' style='width: 100%'>" ;
							print "<tr>" ;
								print "<td width: 33%; style='vertical-align: top'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Country of Birth') . "</span><br/>" ;
									print $row["countryOfBirth"] ;
								print "</td>" ;
								print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Nationality') . "</span><br/>" ;
									print $row["citizenship1"] ;
								print "</td>" ;
								print "<td style='width: 34%; vertical-align: top'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Religion') . "</span><br/>" ;
									print $row["religion"] ;
								print "</td>" ;
							print "</tr>" ;
							print "<tr>" ;
								print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
									if ($_SESSION[$guid]["country"]=="") {
										print "<span style='font-size: 115%; font-weight: bold'>" . _('Aadhar Card No.') . "</span><br/>" ;
									}
									else {
										print "<span style='font-size: 115%; font-weight: bold'>" . _('Aadhar Card No.') . "</span><br/>" ;
									}
									print $row["nationalIDCardNumber"] ;
								print "</td>" ;
								print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('First Language') . "</span><br/>" ;
									print $row["languageFirst"] ;
								print "</td>" ;
								print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Second Language') . "</span><br/>" ;
									print $row["languageSecond"] ;
								print "</td>" ;
							print "</tr>" ;
						print "</table>" ;			
						
						print "<h4>" ;
						print _("System Data") ;
						print "</h4>" ;
						
						print "<table class='smallIntBorder' cellspacing='0' style='width: 100%'>" ;
							print "<tr>" ;
								print "<td width: 33%; style='vertical-align: top'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Username') . "</span><br/>" ;
									print $row["username"] ;
								print "</td>" ;
								print "<td style='width: 33%; vertical-align: top'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Can Login?') . "</span><br/>" ;
									print $row["canLogin"] ;
								print "</td>" ;
								print "<td style='width: 34%; vertical-align: top'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Last IP Address') . "</span><br/>" ;
									print $row["lastIPAddress"] ;
								print "</td>" ;
							print "</tr>" ;
						print "</table>" ;
						/*
						print "<h4>" ;
						print _("Miscellaneous") ;
						print "</h4>" ;
						*/
						print "<table class='smallIntBorder' cellspacing='0' style='width: 100%'>" ;
							print "<tr>" ;
								print "<td style='width: 33%; vertical-align: top'>" ;
								/*
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Transport') . "</span><br/>" ;
									print $row["transport"] ;
									*/
								print "</td>" ;
								print "<td style='width: 33%'>" ;
								/*
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Vehicle Registration') . "</span><br/>" ;
									print $row["vehicleRegistration"] ;
									*/
								print "</td>" ;
								print "<td style='width: 33%'>" ;
								/*
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Locker Number') . "</span><br/>" ;
									print $row["lockerNumber"] ;
									*/
								print "</td>" ;
							print "</tr>" ;
							
							$privacySetting=getSettingByScope( $connection2, "User Admin", "privacy" ) ;
							if ($privacySetting=="Y") {
								print "<tr>" ;
									print "<td style='width: 33%; padding-top: 15px; vertical-align: top' colspan=3>" ;
										print "<span style='font-size: 115%; font-weight: bold'>" . _('Image Privacy') . "</span><br/>" ;
										if ($row["privacy"]!="") {
											print "<span style='color: #cc0000; background-color: #F6CECB'>" ;
												print _("Privacy required:") . " " . $row["privacy"] ;
											print "</span>" ;
										}
										else {
											print "<span style='color: #390; background-color: #D4F6DC;'>" ;
												print _("Privacy not required or not set.") ;
											print "</span>" ;
										}
									
									print "</td>" ;
								print "</tr>" ;
							}
							
							$studentAgreementOptions=getSettingByScope( $connection2, "School Admin", "studentAgreementOptions" ) ;
							if ($studentAgreementOptions!="") {
							
								print "<tr>" ;
									print "<td style='width: 33%; padding-top: 15px; vertical-align: top' colspan=3>" ;
										print "<span style='font-size: 115%; font-weight: bold'>" . _('Student Agreements') . "</span><br/>" ;
										print _("Agreements Signed:") . " " . $row["studentAgreements"] ;
									print "</td>" ;
								print "</tr>" ;
							}
						print "</table>" ;
					}
					else if ($subpage=="Family") {
						try {
							$dataFamily=array("gibbonPersonID"=>$gibbonPersonID); 
							$sqlFamily="SELECT * FROM gibbonfamily JOIN gibbonfamilychild ON (gibbonfamily.gibbonFamilyID=gibbonfamilychild.gibbonFamilyID) WHERE gibbonPersonID=:gibbonPersonID" ;
							$resultFamily=$connection2->prepare($sqlFamily);
							$resultFamily->execute($dataFamily);
						}
						catch(PDOException $e) { 
							print "<div class='error'>" . $e->getMessage() . "</div>" ; 
						}
						
						if ($resultFamily->rowCount()<1) {
							print "<div class='error'>" ;
								print _("There are no records to display.");
							print "</div>" ;
						}
						else {
							while ($rowFamily=$resultFamily->fetch()){
								$count=1 ;
								if (isActionAccessible($guid, $connection2, "/modules/User Admin/family_manage.php")==TRUE) {
									//print "<div class='linkTop'>" ;
									//print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/User Admin/family_manage_edit.php&gibbonFamilyID=" . $rowFamily["gibbonFamilyID"] . "'>" . _('Edit') . "<img style='margin: 0 0 -4px 5px' title='" . _('Edit') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/config.png'/></a> " ;
									//print "</div>" ;
								}
								
								print "<h3>" ;
								print ("Basic Information") ;
								print "</h3>" ;
								
								//Print family information
								print "<table class='smallIntBorder' cellspacing='0' style='width: 100%'>" ;
									print "<tr>" ;
											print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
												print "<span style='font-size: 115%; font-weight: bold'>" . _('Address Name') . "</span><br/>" ;
												print $rowFamily["nameAddress"] ;
											print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
												print "<span style='font-size: 115%; font-weight: bold'>" . _('Home Address1') . "</span><br/>" ;
												print $rowFamily["homeAddress"] ;
											print "</td>" ;
											print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
												print "<span style='font-size: 115%; font-weight: bold'>" . _('Home Address2') . "</span><br/>" ;
												print $rowFamily["homeAddressDistrict"]."<br>";
												print $rowFamily["homeAddressCountry"] ;
											print "</td>" ;
										print "</tr>" ;
								print "</table>" ;
								
								
								//Get adults
								try {
									$dataMember=array("gibbonFamilyID"=>$rowFamily["gibbonFamilyID"]); 
									$sqlMember="SELECT * FROM gibbonfamilyadult WHERE gibbonFamilyID=:gibbonFamilyID ORDER BY contactPriority, officialName" ;
									$resultMember=$connection2->prepare($sqlMember);
									$resultMember->execute($dataMember);
								}
								catch(PDOException $e) { 
									print "<div class='error'>" . $e->getMessage() . "</div>" ; 
								}
								if($resultMember->rowcount()>0){
									$rowMember=$resultMember->fetchAll();
									foreach($rowMember as $r){
										try {
											$dataMember=array("gibbonPersonID"=>$gibbonPersonID,"gibbonFamilyAdultID"=>$r['gibbonFamilyAdultID']); 
											$sqlMember="SELECT * FROM gibbonfamilyrelationship WHERE gibbonPersonID=:gibbonPersonID AND gibbonFamilyAdultID=:gibbonFamilyAdultID" ;
											$result=$connection2->prepare($sqlMember);
											$result->execute($dataMember);
											$relation=$result->fetch();
										}
										catch(PDOException $e) { 
											print "<div class='error'>" . $e->getMessage() . "</div>" ; 
										}
										print "<h3>" ;
										print ($relation['relationship']."'s Detail") ;
										print "</h3>" ;
										
										print "<table class='smallIntBorder' cellspacing='0' style='width: 100%'>" ;
										print "<tr>";
											print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
												print "<span style='font-size: 115%; font-weight: bold'>" . _('Name') . "</span><br/>" ;
												print $r["officialName"]."<br>";
											print "</td>" ;
											print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
												print "<span style='font-size: 115%; font-weight: bold'>" . _('Contact Priority') . "</span><br/>" ;
												print $r["contactPriority"]."<br>";
											print "</td>" ;
											print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
												print "<span style='font-size: 115%; font-weight: bold'>" . _('Aadhar Card No.') . "</span><br/>" ;
												print $r["nationalIDCardNumber"]."<br>";
											print "</td>" ;
										print "</tr>";
										print "<tr>";
											print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
												if($r['phone1']!="" && $r['phone1']!=0){
													print "<span style='font-size: 115%; font-weight: bold'>" .$r['phone1Type']. "</span><br/>" ;
													print "+".$r["phone1CountryCode"]." ".$r['phone1'];
												}
												else{
													print "<span style='font-size: 115%; font-weight: bold'>Phone 1</span><br/>" ;
													print "Not Set";		
												}	
											print "</td>" ;
											print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
												if($r['phone2']!=""  && $r['phone2']!=0){
													print "<span style='font-size: 115%; font-weight: bold'>" .$r['phone2Type']. "</span><br/>" ;
													print "+".$r["phone2CountryCode"]." ".$r['phone2'];
												}
												else{
													print "<span style='font-size: 115%; font-weight: bold'>Phone 2</span><br/>" ;
													print "Not Set";		
												}	
											print "</td>" ;
											print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
												print "<span style='font-size: 115%; font-weight: bold'>" . _('Email') . "</span><br/>" ;
												if($r['email']!=""){
													print $r["email"]."<br>";
												}
												else{
													print "Not Set";
												}
											print "</td>" ;
										print "</tr>";
										print "<tr>";
											print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
												print "<span style='font-size: 115%; font-weight: bold'>" . _('Profession') . "</span><br/>" ;
												print $r["profession"]."<br>";
											print "</td>" ;
											print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
												print "<span style='font-size: 115%; font-weight: bold'>" . _('Employer') . "</span><br/>" ;
												print $r["employer"]."<br>";
											print "</td>" ;
											print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
												print "<span style='font-size: 115%; font-weight: bold'>" . _('Annual Income') . "</span><br/>" ;
												print $r["annual_income"]=$r["annual_income"]==0?"":$r["annual_income"]."<br>";
											print "</td>" ;
										print "</tr>";
										print "</table>";
									}
								}
								
								print "<h3>" ;
								print ("EMERGENCY CONTACTS") ;
								print "</h3>" ;
								if($rowFamily['emergency1Phone']=="" && $rowFamily['emergency2Phone']==""){
									print "<b style='float:left;color:red;'>Emergency Contacts not set for this family.</b>";
								}
								else if($rowFamily['emergency1Phone']!="" || $rowFamily['emergency2Phone']!=""){
										print "<table class='smallIntBorder' cellspacing='0' style='width: 100%'>" ;
										$i=0;
										if($rowFamily['emergency1Phone']!=""){	
										print "<tr>";
											print "<td style='width: 10%; padding-top: 15px; vertical-align: middle'>" ;
												print "<span style='font-size: 115%; font-weight: bold'>" . _(++$i) . "</span><br/>" ;
											print "</td>" ;
											print "<td style='width: 30%; padding-top: 15px; vertical-align: top'>" ;
												print "<span style='font-size: 115%; font-weight: bold'>" . _('Name') . "</span><br/>" ;
												print $rowFamily["emergency1Name"]."<br>";
											print "</td>" ;
											print "<td style='width: 30%; padding-top: 15px; vertical-align: top'>" ;
												print "<span style='font-size: 115%; font-weight: bold'>" . _('Phone') . "</span><br/>" ;
												print $rowFamily["emergency1Phone"]."<br>";
											print "</td>" ;
											print "<td style='width: 30%; padding-top: 15px; vertical-align: top'>" ;
												print "<span style='font-size: 115%; font-weight: bold'>" . _('Relationship') . "</span><br/>" ;
												print $rowFamily["emergency1Relation"]."<br>";
											print "</td>" ;
										print "</tr>";
										}
										if($rowFamily['emergency2Phone']!=""){	
										print "<tr>";
											print "<td style='width: 10%; padding-top: 15px; vertical-align: middle'>" ;
												print "<span style='font-size: 115%; font-weight: bold'>" . _(++$i) . "</span><br/>" ;
											print "</td>" ;
											print "<td style='width: 30%; padding-top: 15px; vertical-align: top'>" ;
												print "<span style='font-size: 115%; font-weight: bold'>" . _('Name') . "</span><br/>" ;
												print $rowFamily["emergency2Name"]."<br>";
											print "</td>" ;
											print "<td style='width: 30%; padding-top: 15px; vertical-align: top'>" ;
												print "<span style='font-size: 115%; font-weight: bold'>" . _('Phone') . "</span><br/>" ;
												print $rowFamily["emergency2Phone"]."<br>";
											print "</td>" ;
											print "<td style='width: 30%; padding-top: 15px; vertical-align: top'>" ;
												print "<span style='font-size: 115%; font-weight: bold'>" . _('Relationship') . "</span><br/>" ;
												print $rowFamily["emergency2Relation"]."<br>";
											print "</td>" ;
										print "</tr>";
										}
										print "</table>";
								}
								
									
								//Get siblings
								try {
									$dataMember=array("gibbonFamilyID"=>$rowFamily["gibbonFamilyID"], "gibbonPersonID"=>$gibbonPersonID); 
									$sqlMember="SELECT * FROM gibbonfamilychild JOIN gibbonperson ON (gibbonfamilychild.gibbonPersonID=gibbonperson.gibbonPersonID) JOIN gibbonrole ON (gibbonperson.gibbonRoleIDPrimary=gibbonrole.gibbonRoleID) WHERE gibbonFamilyID=:gibbonFamilyID AND NOT gibbonperson.gibbonPersonID=:gibbonPersonID ORDER BY surname, preferredName" ;
									$resultMember=$connection2->prepare($sqlMember);
									$resultMember->execute($dataMember);
								}
								catch(PDOException $e) { 
									print "<div class='error'>" . $e->getMessage() . "</div>" ; 
								}
								
								if ($resultMember->rowCount()>0) {
									print "<h3>" ;
									print _("Siblings") ;
									print "</h3>" ;
								
									print "<table class='smallIntBorder' cellspacing='0' style='width:100%'>" ;
										$count=0 ;
										$columns=3 ;
	
										while ($rowMember=$resultMember->fetch()) {
											if ($count%$columns==0) {
												print "<tr>" ;
											}
											print "<td style='width:30%; text-align: left; vertical-align: top'>" ;
												//User photo
												print getUserPhoto($guid, $rowMember["image_75"], 75) ;	
												print "<div style='padding-top: 5px'><b>" ;
												if ($rowMember["status"]=="Full") {
													print "<a href='index.php?q=/modules/Students/student_view_details.php&gibbonPersonID=" . $rowMember["gibbonPersonID"] . "'>" . formatName("", $rowMember["preferredName"], $rowMember["surname"], "Student") . "</a><br/>" ;
												}
												else {
													print formatName("", $rowMember["preferredName"], $rowMember["surname"], "Student") . "<br/>" ;
												}
												print "<span style='font-weight: normal; font-style: italic'>" . _('Status') . ": " . $rowMember["status"] . "</span>" ;
												print "</div>" ;
											print "</td>" ;
		
											if ($count%$columns==($columns-1)) {
												print "</tr>" ;
											}
											$count++ ;
										}
	
										for ($i=0;$i<$columns-($count%$columns);$i++) {
											print "<td></td>" ;
										}
	
										if ($count%$columns!=0) {
											print "</tr>" ;
										}
	
										print "</table>" ;	
								}
							}
						}
					}
					else if ($subpage=="Emergency Contacts") {
						if (isActionAccessible($guid, $connection2, "/modules/User Admin/user_manage.php")==TRUE) {
							print "<div class='linkTop'>" ;
							print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/User Admin/user_manage_edit.php&gibbonPersonID=$gibbonPersonID'>" . _('Edit') . "<img style='margin: 0 0 -4px 5px' title='" . _('Edit') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/config.png'/></a> " ;
							print "</div>" ;
						}
						
						print "<p>" ;
						print _("In an emergency, please try and contact the adult family members listed below first. If these cannot be reached, then try the emergency contacts below.") ;
						print "</p>" ;
						
						print "<h4>" ;
						print _("Adult Family Members") ;
						print "</h4>" ;
						
						try {
							$dataFamily=array("gibbonPersonID"=>$gibbonPersonID); 
							$sqlFamily="SELECT * FROM gibbonfamily JOIN gibbonfamilychild ON (gibbonfamily.gibbonFamilyID=gibbonfamilychild.gibbonFamilyID) WHERE gibbonPersonID=:gibbonPersonID" ;
							$resultFamily=$connection2->prepare($sqlFamily);
							$resultFamily->execute($dataFamily);
						}
						catch(PDOException $e) { 
							print "<div class='error'>" . $e->getMessage() . "</div>" ; 
						}
						
						if ($resultFamily->rowCount()!=1) {
							print "<div class='error'>" ;
								print _("There are no records to display.");
							print "</div>" ;
						}
						else {
							$rowFamily=$resultFamily->fetch() ;
							$count=1 ;
							//Get adults
							try {
								$dataMember=array("gibbonFamilyID"=>$rowFamily["gibbonFamilyID"]); 
								$sqlMember="SELECT * FROM gibbonfamilyadult JOIN gibbonperson ON (gibbonfamilyadult.gibbonPersonID=gibbonperson.gibbonPersonID) WHERE gibbonFamilyID=:gibbonFamilyID ORDER BY contactPriority, surname, preferredName" ;
								$resultMember=$connection2->prepare($sqlMember);
								$resultMember->execute($dataMember);
							}
							catch(PDOException $e) { 
								print "<div class='error'>" . $e->getMessage() . "</div>" ; 
							}
							
							while ($rowMember=$resultMember->fetch()) {
								print "<table class='smallIntBorder' cellspacing='0' style='width: 100%'>" ;
									print "<tr>" ;
										print "<td style='width: 33%; vertical-align: top'>" ;
											print "<span style='font-size: 115%; font-weight: bold'>" . _('Name') . "</span><br/>" ;
												print formatName($rowMember["title"], $rowMember["preferredName"], $rowMember["surname"], "Parent") ;
										print "</td>" ;
										print "<td style='width: 33%; vertical-align: top'>" ;
											print "<span style='font-size: 115%; font-weight: bold'>" . _('Relationship') . "</span><br/>" ;
											try {
												$dataRelationship=array("gibbonPersonID1"=>$rowMember["gibbonPersonID"], "gibbonPersonID2"=>$gibbonPersonID, "gibbonFamilyID"=>$rowFamily["gibbonFamilyID"]); 
												$sqlRelationship="SELECT * FROM gibbonfamilyrelationship WHERE gibbonPersonID1=:gibbonPersonID1 AND gibbonPersonID2=:gibbonPersonID2 AND gibbonFamilyID=:gibbonFamilyID" ;
												$resultRelationship=$connection2->prepare($sqlRelationship);
												$resultRelationship->execute($dataRelationship);
											}
											catch(PDOException $e) { 
												print "<div class='error'>" . $e->getMessage() . "</div>" ; 
											}
											if ($resultRelationship->rowCount()==1) {
												$rowRelationship=$resultRelationship->fetch() ;
												print $rowRelationship["relationship"] ;
											}
											else {
												print "<i>" . _('Unknown') . "</i>" ;
											}
											
										print "</td>" ;
										print "<td style='width: 34%; vertical-align: top'>" ;
											print "<span style='font-size: 115%; font-weight: bold'>" . _('Contact By Phone') . "</span><br/>" ;
											for ($i=1; $i<5; $i++) {
												if ($rowMember["phone" . $i]!="") {
													if ($rowMember["phone" . $i . "Type"]!="") {
														print $rowMember["phone" . $i . "Type"] . ":</i> " ;
													}
													if ($rowMember["phone" . $i . "CountryCode"]!="") {
														print "+" . $rowMember["phone" . $i . "CountryCode"] . " " ;
													}
													print _($rowMember["phone" . $i]) . "<br/>" ;
												}
											}
										print "</td>" ;
									print "</tr>" ;
								print "</table>" ;
								$count++ ;
							}	
						}
							
						print "<h4>" ;
						print _("Emergency Contacts") ;
						print "</h4>" ;
						print "<table class='smallIntBorder' cellspacing='0' style='width: 100%'>" ;
							print "<tr>" ;
								print "<td style='width: 33%; vertical-align: top'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Contact 1') . "</span><br/>" ;
									print $row["emergency1Name"] ;
									if ($row["emergency1Relationship"]!="") {
										print " (" . $row["emergency1Relationship"] . ")" ;
									}
								print "</td>" ;
								print "<td style='width: 33%; vertical-align: top'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Number 1') . "</span><br/>" ;
									print $row["emergency1Number1"] ;
								print "</td>" ;
								print "<td style=width: 34%; 'vertical-align: top'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Number 2') . "</span><br/>" ;
									if ($row["website"]!="") {
										print $row["emergency1Number2"] ;
									}
								print "</td>" ;
							print "</tr>" ;
							print "<tr>" ;
								print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Contact 2') . "</span><br/>" ;
									print $row["emergency2Name"] ;
									if ($row["emergency2Relationship"]!="") {
										print " (" . $row["emergency2Relationship"] . ")" ;
									}
								print "</td>" ;
								print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Number 1') . "</span><br/>" ;
									print $row["emergency2Number1"] ;
								print "</td>" ;
								print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
									print "<span style='font-size: 115%; font-weight: bold'>" . _('Number 2') . "</span><br/>" ;
									if ($row["website"]!="") {
										print $row["emergency2Number2"] ;
									}
								print "</td>" ;
							print "</tr>" ;
						print "</table>" ;
					}
					else if ($subpage=="Medical") {
						try {
							$dataMedical=array("gibbonPersonID"=>$gibbonPersonID); 
							$sqlMedical="SELECT * FROM gibbonpersonmedical JOIN gibbonperson ON (gibbonpersonmedical.gibbonPersonID=gibbonperson.gibbonPersonID) WHERE gibbonperson.gibbonPersonID=:gibbonPersonID" ;
							$resultMedical=$connection2->prepare($sqlMedical);
							$resultMedical->execute($dataMedical);
						}
						catch(PDOException $e) { 
							print "<div class='error'>" . $e->getMessage() . "</div>" ; 
						}

						if ($resultMedical->rowCount()!=1) {
							if (isActionAccessible($guid, $connection2, "/modules/User Admin/medicalForm_manage_add.php")==TRUE) {
								print "<div class='linkTop'>" ;
								print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/User Admin/medicalForm_manage_add.php&gibbonPersonID={$_REQUEST['gibbonPersonID']}&search='>Add Medical Form<img style='margin: 0 0 -4px 3px' title='Add Medical Form' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/page_new.png'/></a> " ;
								print "</div>" ;
							}
							
							print "<div class='error'>" ;
								print _("There are no records to display.");
							print "</div>" ;
						}
						else {
							$rowMedical=$resultMedical->fetch() ;
							
							if (isActionAccessible($guid, $connection2, "/modules/User Admin/medicalForm_manage.php")==TRUE) {
								//print "<div class='linkTop'>" ;
								//print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/User Admin/medicalForm_manage_edit.php&gibbonPersonMedicalID=" . $rowMedical["gibbonPersonMedicalID"] . "'>" . _('Edit') . "<img style='margin: 0 0 -4px 5px' title='" . _('Edit') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/config.png'/></a> " ;
								//print "</div>" ;
							}
						
							//Medical alert!
							$alert=getHighestMedicalRisk( $gibbonPersonID, $connection2 ) ;
							if ($alert!=FALSE) {
								$highestLevel=$alert[1] ;
								$highestColour=$alert[3] ;
								$highestColourBG=$alert[4] ;
								print "<div class='error' style='background-color: #" . $highestColourBG . "; border: 1px solid #" . $highestColour . "; color: #" . $highestColour . "'>" ;
								print "<b>" . sprintf(_('This student has one or more %1$s risk medical conditions'), strToLower($highestLevel)) . "</b>." ;
								print "</div>" ;
							}
						
							//Get medical conditions
							try {
								$dataCondition=array("gibbonPersonMedicalID"=>$rowMedical["gibbonPersonMedicalID"]); 
								$sqlCondition="SELECT * FROM gibbonpersonmedicalcondition WHERE gibbonPersonMedicalID=:gibbonPersonMedicalID ORDER BY name" ;
								$resultCondition=$connection2->prepare($sqlCondition);
								$resultCondition->execute($dataCondition);
							}
							catch(PDOException $e) { 
								print "<div class='error'>" . $e->getMessage() . "</div>" ; 
							}

							print "<table class='smallIntBorder' cellspacing='0' style='width: 100%'>" ;
								print "<tr>" ;
									print "<td style='width: 33%; vertical-align: top'>" ;
										print "<span style='font-size: 115%; font-weight: bold'>" . _('Long Term Medication') . "</span><br/>" ;
										if ($rowMedical["longTermMedication"]=="") {
											print "<i>" . _('Unknown') . "</i>" ;
										}
										else {
											print $rowMedical["longTermMedication"] ;
										}
									print "</td>" ;
									print "<td style='width: 67%; vertical-align: top' colspan=2>" ;
										print "<span style='font-size: 115%; font-weight: bold'>" . _('Details') . "</span><br/>" ;
											print $rowMedical["longTermMedicationDetails"] ;
									print "</td>" ;
								print "</tr>" ;
								print "<tr>" ;
									print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
										print "<span style='font-size: 115%; font-weight: bold'>" . _('Tetanus Last 10 Years?') . "</span><br/>" ;
										if ($rowMedical["tetanusWithin10Years"]=="") {
											print "<i>" . _('Unknown') . "</i>" ;
										}
										else {
											print $rowMedical["tetanusWithin10Years"] ;
										}
									print "</td>" ;
									print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
										print "<span style='font-size: 115%; font-weight: bold'>" . _('Blood Type') . "</span><br/>" ;
										print $rowMedical["bloodType"] ;
									print "</td>" ;
									print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
										print "<span style='font-size: 115%; font-weight: bold'>" . _('Medical Conditions?') . "</span><br/>" ;
										if ($resultCondition->rowCount()>0) {
											print _("Yes") . ". " . _("Details below.") ;
										}
										else {
											_("No") ;
										}
									print "</td>" ;
								print "</tr>" ;
							print "</table>" ;
							
							while ($rowCondition=$resultCondition->fetch()) {
								print "<h4>" ;
								$alert=getAlert($connection2, $rowCondition["gibbonAlertLevelID"]) ;
								if ($alert!=FALSE) {
									print _($rowCondition["name"]) . " <span style='color: #" . $alert["color"] . "'>(" . _($alert["name"]) . " " . _('Risk') . ")</span>" ;
								}
								print "</h4>" ;
								
								print "<table class='smallIntBorder' cellspacing='0' style='width: 100%'>" ;
								print "<tr>" ;
									print "<td style='width: 50%; vertical-align: top'>" ;
										print "<span style='font-size: 115%; font-weight: bold'>" . _('Triggers') . "</span><br/>" ;
										print $rowCondition["triggers"] ;
									print "</td>" ;
									print "<td style='width: 50%; vertical-align: top' colspan=2>" ;
										print "<span style='font-size: 115%; font-weight: bold'>" . _('Reaction') . "</span><br/>" ;
										print $rowCondition["reaction"] ;
									print "</td>" ;
								print "</tr>" ;
								print "<tr>" ;
									print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
										print "<span style='font-size: 115%; font-weight: bold'>" . _('Response') . "</span><br/>" ;
										print $rowCondition["response"] ;
									print "</td>" ;
									print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
										print "<span style='font-size: 115%; font-weight: bold'>" . _('Medication') . "</span><br/>" ;
										print $rowCondition["medication"] ;
									print "</td>" ;
								print "</tr>" ;
								print "<tr>" ;
									print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
										print "<span style='font-size: 115%; font-weight: bold'>" . _('Last Episode Date') . "</span><br/>" ;
										if (is_null($row["dob"])==FALSE AND $row["dob"]!="0000-00-00") {
											print dateConvertBack($guid, $rowCondition["lastEpisode"]) ;
										}
									print "</td>" ;
									print "<td style='width: 33%; padding-top: 15px; vertical-align: top'>" ;
										print "<span style='font-size: 115%; font-weight: bold'>" . _('Last Episode Treatment') . "</span><br/>" ;
										print $rowCondition["lastEpisodeTreatment"] ;
									print "</td>" ;
								print "</tr>" ;
								print "<tr>" ;
									print "<td style='width: 33%; padding-top: 15px; vertical-align: top' colspan=2>" ;
										print "<span style='font-size: 115%; font-weight: bold'>" . _('Comments') . "</span><br/>" ;
										print $rowCondition["comment"] ;
									print "</td>" ;
								print "</tr>" ;
							print "</table>" ;
							}
						}
					}
					else if ($subpage=="Notes") {
						if (isActionAccessible($guid, $connection2, "/modules/Students/student_view_details_notes_add.php")==FALSE) {
							print "<div class='error'>" ;
								print _("Your request failed because you do not have access to this action.") ;
							print "</div>" ; 
						}
						else {
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
							
							print "<p>" ;
								print _("Student Notes provide a way to store information on students which does not fit elsewhere in the system, or which you want to be able to see quickly in one place.") . " <b>" . _('Please remember that notes are visible to other users who have access to full student profiles (this should not generally include parents).') . "</b>" ;
							print "</p>" ;
							
							$categories=FALSE ;
							$category=NULL ;
							if (isset($_GET["category"])) {
								$category=$_GET["category"] ;
							}
							
							try {
								$dataCategories=array(); 
								$sqlCategories="SELECT * FROM gibbonstudentnotecategory WHERE active='Y' ORDER BY name" ;
								$resultCategories=$connection2->prepare($sqlCategories);
								$resultCategories->execute($dataCategories);
							}
							catch(PDOException $e) { }
							if ($resultCategories->rowCount()>0) {
								$categories=TRUE ;
								
								print "<h3>" ;
								print _("Filter") ;
								print "</h3>" ;
								?>
								<form method="get" action="<?php print $_SESSION[$guid]["absoluteURL"]?>/index.php">
									<table class='noIntBorder' cellspacing='0' style="width: 100%">	
										<tr><td style="width: 30%"></td><td></td></tr>
										<tr>
											<td> 
												<b><?php print _('Category') ?></b><br/>
											</td>
											<td class="right">
												<?php
												print "<select name='category' id='category' style='width:302px'>" ;
													print "<option  value=''></option>" ;
													while ($rowCategories=$resultCategories->fetch()) {
														$selected="" ;
														if ($category==$rowCategories["gibbonStudentNoteCategoryID"]) {
															$selected="selected" ;
														}
														print "<option $selected value='" . $rowCategories["gibbonStudentNoteCategoryID"] . "'>" . $rowCategories["name"] . "</option>" ;
													}
												print "</select>" ;
												?>
											</td>
										</tr>
										<tr>
											<td colspan=2 class="right">
												<input type="hidden" name="q" value="/modules/<?php print $_SESSION[$guid]["module"] ?>/student_view_details.php">
												<input type="hidden" name="address" value="<?php print $_SESSION[$guid]["address"] ?>">
												<?php
												print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/student_view_details.php&gibbonPersonID=$gibbonPersonID&search=$search&allStudents=$allStudents&subpage=Notes'>" . _('Clear Search') . "</a>" ;
												?>
												<input type="hidden" name="gibbonPersonID" value="<?php print $gibbonPersonID ?>">
												<input type="hidden" name="allStudents" value="<?php print $allStudents ?>">
												<input type="hidden" name="search" value="<?php print $search ?>">
												<input type="hidden" name="subpage" value="Notes">
												<input type="submit" value="<?php print _("Submit") ; ?>">
											</td>
										</tr>
									</table>
								</form>
							<?php
							}
						
							try {
								if ($category==NULL) {
									$data=array("gibbonPersonID"=>$gibbonPersonID); 
									$sql="SELECT gibbonstudentnote.*, gibbonstudentnotecategory.name AS category, surname, preferredName FROM gibbonstudentnote LEFT JOIN gibbonstudentnotecategory ON (gibbonstudentnote.gibbonStudentNoteCategoryID=gibbonstudentnotecategory.gibbonStudentNoteCategoryID) JOIN gibbonperson ON (gibbonstudentnote.gibbonPersonIDCreator=gibbonperson.gibbonPersonID) WHERE gibbonstudentnote.gibbonPersonID=:gibbonPersonID ORDER BY timestamp DESC" ; 
								}
								else {
									$data=array("gibbonPersonID"=>$gibbonPersonID, "gibbonStudentNoteCategoryID"=>$category); 
									$sql="SELECT gibbonstudentnote.*, gibbonstudentnotecategory.name AS category, surname, preferredName FROM gibbonstudentnote LEFT JOIN gibbonstudentnotecategory ON (gibbonstudentnote.gibbonStudentNoteCategoryID=gibbonstudentnotecategory.gibbonStudentNoteCategoryID) JOIN gibbonperson ON (gibbonstudentnote.gibbonPersonIDCreator=gibbonperson.gibbonPersonID) WHERE gibbonstudentnote.gibbonPersonID=:gibbonPersonID AND gibbonstudentnote.gibbonStudentNoteCategoryID=:gibbonStudentNoteCategoryID ORDER BY timestamp DESC" ; 
								}
								$result=$connection2->prepare($sql);
								$result->execute($data);
							}
							catch(PDOException $e) { 
								print "<div class='error'>" . $e->getMessage() . "</div>" ; 
							}
							
							print "<div class='linkTop'>" ;
							print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/student_view_details_notes_add.php&gibbonPersonID=$gibbonPersonID&search=$search&allStudents=$allStudents&search=$search&allStudents=$allStudents&subpage=Notes&category=$category'>" .  _('Add') . "<img style='margin-left: 5px' title='" . _('Add') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/page_new.png'/></a>" ;
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
											print _("Date") . "<br/>" ;
											print "<span style='font-size: 75%; font-style: italic'>" . _('Time') . "</span>" ;
										print "</th>" ;
										print "<th>" ;
											print _("Category") ;
										print "</th>" ;
										print "<th>" ;
											print _("Title") . "<br/>" ;
											print "<span style='font-size: 75%; font-style: italic'>" . _('Summary') . "</span>" ;
										print "</th>" ;
										print "<th>" ;
											print _("Note Taker") ;
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
										$count++ ;
										
										//COLOR ROW BY STATUS!
										print "<tr class=$rowNum>" ;
											print "<td>" ;
												print dateConvertBack($guid, substr($row["timestamp"],0,10)) . "<br/>" ;
												print "<span style='font-size: 75%; font-style: italic'>" . substr($row["timestamp"],11,5) . "</span>" ;
											print "</td>" ;
											print "<td>" ;
												print $row["category"] ;
											print "</td>" ;
											print "<td>" ;
												if ($row["title"]=="") {
													print "<i>" . _('NA') . "</i><br/>" ;
												}
												else {
													print $row["title"] . "<br/>" ;
												}
												print "<span style='font-size: 75%; font-style: italic'>" .  substr(strip_tags($row["note"]),0,60)  . "</span>" ;
											print "</td>" ;
											print "<td>" ;
												print formatName("", $row["preferredName"], $row["surname"], "Staff", false, true) ;
											print "</td>" ;
											print "<td>" ;
												if ($row["gibbonPersonIDCreator"]==$_SESSION[$guid]["gibbonPersonID"]) {
													print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/student_view_details_notes_edit.php&search=" . $search . "&gibbonStudentNoteID=" . $row["gibbonStudentNoteID"] . "&gibbonPersonID=$gibbonPersonID&search=$search&allStudents=$allStudents&subpage=Notes&category=" . $_GET["category"] . "'><img title='" . _('Edit') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/config.png'/></a> " ;
													print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/student_view_details_notes_delete.php&search=" . $search . "&gibbonStudentNoteID=" . $row["gibbonStudentNoteID"] . "&gibbonPersonID=$gibbonPersonID&search=$search&allStudents=$allStudents&subpage=Notes&category=" . $_GET["category"] . "'><img title='" . _('Delete') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/garbage.png'/></a>" ;
												}
												print "<script type='text/javascript'>" ;	
													print "$(document).ready(function(){" ;
														print "\$(\".note-$count\").hide();" ;
														print "\$(\".show_hide-$count\").fadeIn(1000);" ;
														print "\$(\".show_hide-$count\").click(function(){" ;
														print "\$(\".note-$count\").fadeToggle(1000);" ;
														print "});" ;
													print "});" ;
												print "</script>" ;
												print "<a title='" . _('View Description') . "' class='show_hide-$count' onclick='return false;' href='#'><img title='" . _('View Details') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/page_down.png'/></a></span><br/>" ;
											print "</td>" ;
										print "</tr>" ;
										print "<tr class='note-$count' id='note-$count'>" ;
											print "<td colspan=6>" ;
												print $row["note"] ;
											print "</td>" ;
										print "</tr>" ;
									}
								print "</table>" ;
							}
						}
					}
					else if ($subpage=="School Attendance") {
						if (isActionAccessible($guid, $connection2, "/modules/Attendance/report_studentHistory.php")==FALSE) {
							print "<div class='error'>" ;
								print _("Your request failed because you do not have access to this action.") ;
							print "</div>" ;
						}
						else {
							include "./modules/Attendance/moduleFunctions.php" ;
							report_studentHistory($guid, $gibbonPersonID, TRUE, $_SESSION[$guid]["absoluteURL"] . "/report.php?q=/modules/Attendance/report_studentHistory_print.php&gibbonPersonID=$gibbonPersonID", $connection2, $row["dateStart"], $row["dateEnd"]) ;
						}
					}
					/* */
					else if ($subpage=="Activity and Achievement") {
						include "./modules/Students/activity_achievement.php" ;
						$data=fetchProgressAchievement($connection2,$gibbonPersonID);
						?>
						<div style="width:100%;" id='viewPanel'>
							<ul>
								<li><a href="#tabP">Progress</a></li>
								<li><a href="#tabA">Achievement</a></li>
							</ul>
							<div id="tabP" class='collapse'>
								<?=$data['Progress']?>
							</div>
							<div id="tabA" class='collapse'>
								<?=$data['Achievement']?>
							</div>
						</div>
						<script>
						$(document).ready(function(){
							$('#viewPanel').tabs();
							$( ".collapse" ).accordion({
							   heightStyle: "content",
							   collapsible: true
							});
						});
						</script>
						<?php
					}
					/* */
					else if ($subpage=="Markbook") {
						include "./modules/Exam/report_history.php" ;
						?>
						<script>
						$(document).ready(function(){
							$( ".collapse" ).accordion({
							   heightStyle: "content",
							   collapsible: true
							});
						});
						</script>
					<?php
					}
					/*
					else if ($subpage=="Markbook") {
						if (isActionAccessible($guid, $connection2, "/modules/Markbook/markbook_view.php")==FALSE) {
							print "<div class='error'>" ;
								print _("Your request failed because you do not have access to this action.");
							print "</div>" ;
						}
						else {
							$highestAction=getHighestGroupedAction($guid, "/modules/Markbook/markbook_view.php", $connection2) ;
							if ($highestAction==FALSE) {
								print "<div class='error'>" ;
									print _("The highest grouped action cannot be determined.") ;
								print "</div>" ;
							}
							else {
								//Get alternative header names
								$attainmentAlternativeName=getSettingByScope($connection2, "Markbook", "attainmentAlternativeName") ;
								$attainmentAlternativeNameAbrev=getSettingByScope($connection2, "Markbook", "attainmentAlternativeNameAbrev") ;
								$effortAlternativeName=getSettingByScope($connection2, "Markbook", "effortAlternativeName") ;
								$effortAlternativeNameAbrev=getSettingByScope($connection2, "Markbook", "effortAlternativeNameAbrev") ;

								$alert=getAlert($connection2, 002) ;
								$role=getRoleCategory($_SESSION[$guid]["gibbonRoleIDCurrent"], $connection2) ;
								if ($role=="Parent") {
									$showParentAttainmentWarning=getSettingByScope($connection2, "Markbook", "showParentAttainmentWarning" ) ; 
									$showParentEffortWarning=getSettingByScope($connection2, "Markbook", "showParentEffortWarning" ) ; 														
								}
								else {
									$showParentAttainmentWarning="Y" ;
									$showParentEffortWarning="Y" ;
								}
								$entryCount=0 ;
								
								$and="" ;
								$and2="" ;
								$dataList=array() ;
								$dataEntry=array() ;
								$filter=NULL ;
								if (isset($_GET["filter"])) {
									$filter=$_GET["filter"] ;
								}
								else if (isset($_POST["filter"])) {
									$filter=$_POST["filter"] ;
								}
								if ($filter=="") {
									$filter=$_SESSION[$guid]["gibbonSchoolYearID"] ;
								}
								if ($filter!="*") {
									$dataList["filter"]=$filter ;
									$and.=" AND gibbonSchoolYearID=:filter" ;
								}
								
								$filter2=NULL ;
								if (isset($_GET["filter2"])) {
									$filter2=$_GET["filter2"] ;
								}
								else if (isset($_POST["filter2"])) {
									$filter2=$_POST["filter2"] ;
								}
								if ($filter2!="") {
									$dataList["filter2"]=$filter2 ;
									$and.=" AND gibbonDepartmentID=:filter2" ;
								}
								
								$filter3=NULL ;
								if (isset($_GET["filter3"])) {
									$filter3=$_GET["filter3"] ;
								}
								else if (isset($_POST["filter3"])) {
									$filter3=$_POST["filter3"] ;
								}
								if ($filter3!="") {
									$dataEntry["filter3"]=$filter3 ;
									$and2.=" AND type=:filter3" ;
								}
								
								print "<p>" ;
									print _("This page displays academic results for a student throughout their school career. Only subjects with published results are shown.") ;
								print "</p>" ;
								
								print "<form method='post' action='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=" . $_GET["q"] . "&gibbonPersonID=$gibbonPersonID&search=$search&allStudents=$allStudents&subpage=Markbook'>" ;
									print"<table class='noIntBorder' cellspacing='0' style='width: 100%'>" ;	
										?>
										<tr>
											<td> 
												<b><?php print _('Learning Areas') ?></b><br/>
												<span style="font-size: 90%"><i></i></span>
											</td>
											<td class="right">
												<?php
												print "<select name='filter2' id='filter2' style='width:302px'>" ;
													print "<option value=''>" . _('All Learning Areas') . "</option>" ;
													try {
														$dataSelect=array(); 
														$sqlSelect="SELECT * FROM gibbondepartment WHERE type='Learning Area' ORDER BY name" ;
														$resultSelect=$connection2->prepare($sqlSelect);
														$resultSelect->execute($dataSelect);
													}
													catch(PDOException $e) { }
													while ($rowSelect=$resultSelect->fetch()) {
														$selected="" ;
														if ($rowSelect["gibbonDepartmentID"]==$filter2) {
															$selected="selected" ;
														}
														print "<option $selected value='" . $rowSelect["gibbonDepartmentID"] . "'>" . $rowSelect["name"] . "</option>" ;
													}
												print "</select>" ;
												?>
											</td>
										</tr>
										<tr>
											<td> 
												<b><?php print _('School Years') ?></b><br/>
												<span style="font-size: 90%"><i></i></span>
											</td>
											<td class="right">
												<?php
												print "<select name='filter' id='filter' style='width:302px'>" ;
													print "<option value='*'>" . _('All Years') . "</option>" ;
													try {
														$dataSelect=array("gibbonPersonID"=>$gibbonPersonID); 
														$sqlSelect="SELECT gibbonschoolyear.gibbonSchoolYearID, gibbonschoolyear.name AS year, gibbonyeargroup.name AS yearGroup FROM gibbonstudentenrolment JOIN gibbonschoolyear ON (gibbonstudentenrolment.gibbonSchoolYearID=gibbonschoolyear.gibbonSchoolYearID) JOIN gibbonyeargroup ON (gibbonstudentenrolment.gibbonYearGroupID=gibbonyeargroup.gibbonYearGroupID) WHERE gibbonPersonID=:gibbonPersonID ORDER BY gibbonschoolyear.sequenceNumber" ;
														$resultSelect=$connection2->prepare($sqlSelect);
														$resultSelect->execute($dataSelect);
													}
													catch(PDOException $e) { }
													while ($rowSelect=$resultSelect->fetch()) {
														$selected="" ;
														if ($rowSelect["gibbonSchoolYearID"]==$filter) {
															$selected="selected" ;
														}
														print "<option $selected value='" . $rowSelect["gibbonSchoolYearID"] . "'>" . $rowSelect["year"] . " (" . $rowSelect["yearGroup"] . ")</option>" ;
													}
												print "</select>" ;
												?>
											</td>
										</tr>
										<?php
										$types=getSettingByScope($connection2, "Markbook", "markbookType") ;
										if ($types!=FALSE) {
											$types=explode(",", $types) ;
											?>
											<tr>
												<td> 
													<b><?php print _('Type') ?></b><br/>
													<span style="font-size: 90%"><i></i></span>
												</td>
												<td class="right">
													<select name="filter3" id="filter3" style="width: 302px">
														<option value=""></option>
														<?php
														for ($i=0; $i<count($types); $i++) {
															$selected="" ;
															if ($filter3==$types[$i]) {
																$selected="selected" ;
															}
															?>
															<option <?php print $selected ?> value="<?php print trim($types[$i]) ?>"><?php print trim($types[$i]) ?></option>
														<?php
														}
														?>
													</select>
												</td>
											</tr>
											<?php
										}
										print "<tr>" ;
											print "<td class='right' colspan=2>" ;
												print "<input type='hidden' name='q' value='" . $_GET["q"] . "'>" ;
												print "<input checked type='checkbox' name='details' class='details' value='Yes' />" ;
												print "<span style='font-size: 85%; font-weight: normal; font-style: italic'> " ._('Show/Hide Details') . "</span>" ;
												?>
												<script type="text/javascript">
													/* Show/Hide detail control /
													$(document).ready(function(){
														$(".details").click(function(){
															if ($('input[name=details]:checked').val()=="Yes" ) {
																$(".detailItem").slideDown("fast", $("#detailItem").css("{'display' : 'table-row'}")); 
															} 
															else {
																$(".detailItem").slideUp("fast"); 
															}
														 });
													});
												</script>
												<?php
												print "<input type='submit' value='" . _('Go') . "'>" ;
											print "</td>" ;
										print "</tr>" ;
									print"</table>" ;
								print "</form>" ;
								
								//Get class list
								try {
									$dataList["gibbonPersonID"]=$gibbonPersonID ; 
									$dataList["gibbonPersonID2"]=$gibbonPersonID; 
									$sqlList="SELECT gibboncourse.nameShort AS course, gibboncourseclass.nameShort AS class, gibboncourse.name, gibboncourseclass.gibbonCourseClassID, gibbonscalegrade.value AS target FROM gibboncourse JOIN gibboncourseclass ON (gibboncourseclass.gibbonCourseID=gibboncourse.gibbonCourseID) JOIN gibboncourseclassperson ON (gibboncourseclassperson.gibbonCourseClassID=gibboncourseclass.gibbonCourseClassID) LEFT JOIN gibbonmarkbooktarget ON (gibbonmarkbooktarget.gibbonCourseClassID=gibboncourseclass.gibbonCourseClassID AND gibbonmarkbooktarget.gibbonPersonIDStudent=:gibbonPersonID2) LEFT JOIN gibbonscalegrade ON (gibbonmarkbooktarget.gibbonScaleGradeID=gibbonscalegrade.gibbonScaleGradeID) WHERE gibboncourseclassperson.gibbonPersonID=:gibbonPersonID $and ORDER BY course, class" ;
									$resultList=$connection2->prepare($sqlList);
									$resultList->execute($dataList);
								}
								catch(PDOException $e) { 
									print "<div class='error'>" . $e->getMessage() . "</div>" ; 
								}
								
								if ($resultList->rowCount()>0) {
									while ($rowList=$resultList->fetch()) {
										try {
											$dataEntry["gibbonPersonID"]=$gibbonPersonID ;
											$dataEntry["gibbonCourseClassID"]=$rowList["gibbonCourseClassID"] ;
											if ($highestAction=="Markbook_viewMyChildrensClasses") {
												$sqlEntry="SELECT *, gibbonmarkbookentry.comment AS comment FROM gibbonmarkbookentry JOIN gibbonmarkbookcolumn ON (gibbonmarkbookentry.gibbonMarkbookColumnID=gibbonmarkbookcolumn.gibbonMarkbookColumnID) WHERE gibbonPersonIDStudent=:gibbonPersonID AND gibbonCourseClassID=:gibbonCourseClassID AND complete='Y' AND completeDate<='" . date("Y-m-d") . "' AND viewableParents='Y' $and2 ORDER BY completeDate" ;
											}
											else {
												$sqlEntry="SELECT *, gibbonmarkbookentry.comment AS comment FROM gibbonmarkbookentry JOIN gibbonmarkbookcolumn ON (gibbonmarkbookentry.gibbonMarkbookColumnID=gibbonmarkbookcolumn.gibbonMarkbookColumnID) WHERE gibbonPersonIDStudent=:gibbonPersonID AND gibbonCourseClassID=:gibbonCourseClassID AND complete='Y' AND completeDate<='" . date("Y-m-d") . "' $and2 ORDER BY completeDate" ;
											}
											$resultEntry=$connection2->prepare($sqlEntry);
											$resultEntry->execute($dataEntry);
										}
										catch(PDOException $e) { 
											print "<div class='error'>" . $e->getMessage() . "</div>" ; 
										}
										
										if ($resultEntry->rowCount()>0) {
											print "<a name='" . $rowList["gibbonCourseClassID"] . "'></a><h4>" . $rowList["course"] . "." . $rowList["class"] . " <span style='font-size:85%; font-style: italic'>(" . $rowList["name"] . ")</span></h4>" ;
											
											try {
												$dataTeachers=array("gibbonCourseClassID"=>$rowList["gibbonCourseClassID"]); 
												$sqlTeachers="SELECT title, surname, preferredName FROM gibbonperson JOIN gibboncourseclassperson ON (gibboncourseclassperson.gibbonPersonID=gibbonperson.gibbonPersonID) WHERE role='Teacher' AND gibbonCourseClassID=:gibbonCourseClassID ORDER BY surname, preferredName" ;
												$resultTeachers=$connection2->prepare($sqlTeachers);
												$resultTeachers->execute($dataTeachers);
											}
											catch(PDOException $e) { 
												print "<div class='error'>" . $e->getMessage() . "</div>" ; 
											}
											
											$teachers="<p><b>" . _('Taught by:') . "</b> " ;
											while ($rowTeachers=$resultTeachers->fetch()) {
												$teachers=$teachers . $rowTeachers["title"] . " " . $rowTeachers["surname"] . ", " ;
											}
											$teachers=substr($teachers,0,-2) ;
											$teachers=$teachers . "</p>" ;
											print $teachers ;
											
											if ($rowList["target"]!="") {
												print "<div style='font-weight: bold' class='linkTop'>" ;
													print _("Target") . ": " . $rowList["target"] ;
												print "</div>" ; 
											}
						
											print "<table cellspacing='0' style='width: 100%'>" ;
											print "<tr class='head'>" ;
												print "<th style='width: 120px'>" ;
													print _("Assessment") ;
												print "</th>" ;
												print "<th style='width: 75px; text-align: center'>" ;
													if ($attainmentAlternativeName!="") { print $attainmentAlternativeName ; } else { print _('Attainment') ; }
												print "</th>" ;
												print "<th style='width: 75px; text-align: center'>" ;
													if ($effortAlternativeName!="") { print $effortAlternativeName ; } else { print _('Effort') ; }
												print "</th>" ;
												print "<th>" ;
													print _("Comment") ;
												print "</th>" ;
												print "<th style='width: 75px'>" ;
													print _("Submission") ;
												print "</th>" ;
											print "</tr>" ;
											
											$count=0 ;
											while ($rowEntry=$resultEntry->fetch()) {
												if ($count%2==0) {
													$rowNum="even" ;
												}
												else {
													$rowNum="odd" ;
												}
												$count++ ;
												$entryCount++ ;
												
												print "<tr class=$rowNum>" ;
													print "<td>" ;
														print "<span title='" . htmlPrep($rowEntry["description"]) . "'><b><u>" . $rowEntry["name"] . "</u></b></span><br/>" ;
														print "<span style='font-size: 90%; font-style: italic; font-weight: normal'>" ;
														$unit=getUnit($connection2, $rowEntry["gibbonUnitID"], $rowEntry["gibbonHookID"], $rowEntry["gibbonCourseClassID"]) ;
														if (isset($unit[0])) {
															print $unit[0] . "<br/>" ;
														}
														if (isset($unit[1])) {
															if ($unit[1]!="") {
																print $unit[1] . " " . _('Unit') . "</i><br/>" ;
															}
														}
														if ($rowEntry["completeDate"]!="") {
															print _("Marked on") . " " . dateConvertBack($guid, $rowEntry["completeDate"]) . "<br/>" ;
														}
														else {
															print _("Unmarked") . "<br/>" ;
														}
														print $rowEntry["type"] ;
														if ($rowEntry["attachment"]!="" AND file_exists($_SESSION[$guid]["absolutePath"] . "/" . $rowEntry["attachment"])) {
															print " | <a 'title='" . _('Download more information') . "' href='" . $_SESSION[$guid]["absoluteURL"] . "/" . $rowEntry["attachment"] . "'>" . _('More info') . "</a>"; 
														}
														print "</span><br/>" ;
													print "</td>" ;
													print "<td style='text-align: center'>" ;
														$attainmentExtra="" ;
														try {
															$dataAttainment=array("gibbonScaleIDAttainment"=>$rowEntry["gibbonScaleIDAttainment"]); 
															$sqlAttainment="SELECT * FROM gibbonscale WHERE gibbonScaleID=:gibbonScaleIDAttainment" ;
															$resultAttainment=$connection2->prepare($sqlAttainment);
															$resultAttainment->execute($dataAttainment);
														}
														catch(PDOException $e) { 
															print "<div class='error'>" . $e->getMessage() . "</div>" ; 
														}
														if ($resultAttainment->rowCount()==1) {
															$rowAttainment=$resultAttainment->fetch() ;
															$attainmentExtra="<br/>" . _($rowAttainment["usage"]) ;
														}
														$styleAttainment="style='font-weight: bold'" ;
														if ($rowEntry["attainmentConcern"]=="Y" AND $showParentAttainmentWarning=="Y") {
															$styleAttainment="style='color: #" . $alert["color"] . "; font-weight: bold; border: 2px solid #" . $alert["color"] . "; padding: 2px 4px; background-color: #" . $alert["colorBG"] . "'" ;
														}
														else if ($rowEntry["attainmentConcern"]=="P" AND $showParentAttainmentWarning=="Y") {
															$styleAttainment="style='color: #390; font-weight: bold; border: 2px solid #390; padding: 2px 4px; background-color: #D4F6DC'" ;
														}
														print "<div $styleAttainment>" . $rowEntry["attainmentValue"] ;
															if ($rowEntry["gibbonRubricIDAttainment"]!="") {
																print "<a class='thickbox' href='" . $_SESSION[$guid]["absoluteURL"] . "/fullscreen.php?q=/modules/Markbook/markbook_view_rubric.php&gibbonRubricID=" . $rowEntry["gibbonRubricIDAttainment"] . "&gibbonCourseClassID=" . $rowList["gibbonCourseClassID"] . "&gibbonMarkbookColumnID=" . $rowEntry["gibbonMarkbookColumnID"] . "&gibbonPersonID=$gibbonPersonID&mark=FALSE&type=attainment&width=1100&height=550'><img style='margin-bottom: -3px; margin-left: 3px' title='View Rubric' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/rubric.png'/></a>" ;
															}
														print "</div>" ;
														if ($rowEntry["attainmentValue"]!="") {
															print "<div class='detailItem' style='font-size: 75%; font-style: italic; margin-top: 2px'><b>" . htmlPrep(_($rowEntry["attainmentDescriptor"])) . "</b>" . _($attainmentExtra) . "</div>" ;
														}
													print "</td>" ;
													print "<td style='text-align: center'>" ;
														$effortExtra="" ;
														try {
															$dataEffort=array("gibbonScaleIDEffort"=>$rowEntry["gibbonScaleIDEffort"]); 
															$sqlEffort="SELECT * FROM gibbonscale WHERE gibbonScaleID=:gibbonScaleIDEffort" ;
															$resultEffort=$connection2->prepare($sqlEffort);
															$resultEffort->execute($dataEffort);
														}
														catch(PDOException $e) { 
															print "<div class='error'>" . $e->getMessage() . "</div>" ; 
														}
	
														if ($resultEffort->rowCount()==1) {
															$rowEffort=$resultEffort->fetch() ;
															$effortExtra="<br/>" . _($rowEffort["usage"]) ;
														}
														$styleEffort="style='font-weight: bold'" ;
														if ($rowEntry["effortConcern"]=="Y" AND $showParentEffortWarning=="Y") {
															$styleEffort="style='color: #" . $alert["color"] . "; font-weight: bold; border: 2px solid #" . $alert["color"] . "; padding: 2px 4px; background-color: #" . $alert["colorBG"] . "'" ;
														}
														print "<div $styleEffort>" . $rowEntry["effortValue"] ;
															if ($rowEntry["gibbonRubricIDEffort"]!="") {
																print "<a class='thickbox' href='" . $_SESSION[$guid]["absoluteURL"] . "/fullscreen.php?q=/modules/Markbook/markbook_view_rubric.php&gibbonRubricID=" . $rowEntry["gibbonRubricIDEffort"] . "&gibbonCourseClassID=" . $rowList["gibbonCourseClassID"] . "&gibbonMarkbookColumnID=" . $rowEntry["gibbonMarkbookColumnID"] . "&gibbonPersonID=$gibbonPersonID&mark=FALSE&type=effort&width=1100&height=550'><img style='margin-bottom: -3px; margin-left: 3px' title='View Rubric' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/rubric.png'/></a>" ;
															}
														print "</div>" ;
														if ($rowEntry["effortValue"]!="") {
															print "<div class='detailItem' style='font-size: 75%; font-style: italic; margin-top: 2px'><b>" . htmlPrep(_($rowEntry["effortDescriptor"])) . "</b>" . _($effortExtra) . "</div>" ;
														}
													print "</td>" ;
													print "<td>" ;
														if ($rowEntry["comment"]!="") {
															if (strlen($rowEntry["comment"])>50) {
																print "<script type='text/javascript'>" ;	
																	print "$(document).ready(function(){" ;
																		print "\$(\".comment-$entryCount\").hide();" ;
																		print "\$(\".show_hide-$entryCount\").fadeIn(1000);" ;
																		print "\$(\".show_hide-$entryCount\").click(function(){" ;
																		print "\$(\".comment-$entryCount\").fadeToggle(1000);" ;
																		print "});" ;
																	print "});" ;
																print "</script>" ;
																print "<span>" . substr($rowEntry["comment"], 0, 50) . "...<br/>" ;
																print "<a title='" . _('View Description') . "' class='show_hide-$entryCount' onclick='return false;' href='#'>" . _('Read more') . "</a></span><br/>" ;
															}
															else {
																print $rowEntry["comment"] ;
															}
															if ($rowEntry["response"]!="") {
																print "<a title='Uploaded Response' href='" . $_SESSION[$guid]["absoluteURL"] . "/" . $rowEntry["response"] . "'>" . _('Uploaded Response') . "</a><br/>" ;
															}
														}
													print "</td>" ;
													print "<td>" ;
														if ($rowEntry["gibbonPlannerEntryID"]!="") {
															try {
																$dataSub=array("gibbonPlannerEntryID"=>$rowEntry["gibbonPlannerEntryID"]); 
																$sqlSub="SELECT * FROM gibbonplannerentry WHERE gibbonPlannerEntryID=:gibbonPlannerEntryID AND homeworkSubmission='Y'" ;
																$resultSub=$connection2->prepare($sqlSub);
																$resultSub->execute($dataSub);
															}
															catch(PDOException $e) { 
																print "<div class='error'>" . $e->getMessage() . "</div>" ; 
															}
															if ($resultSub->rowCount()==1) {
																$rowSub=$resultSub->fetch() ;
																
																try {
																	$dataWork=array("gibbonPlannerEntryID"=>$rowEntry["gibbonPlannerEntryID"], "gibbonPersonID"=>$_GET["gibbonPersonID"]); 
																	$sqlWork="SELECT * FROM gibbonplannerentryhomework WHERE gibbonPlannerEntryID=:gibbonPlannerEntryID AND gibbonPersonID=:gibbonPersonID ORDER BY count DESC" ;
																	$resultWork=$connection2->prepare($sqlWork);
																	$resultWork->execute($dataWork);
																}
																catch(PDOException $e) { 
																	print "<div class='error'>" . $e->getMessage() . "</div>" ; 
																}
																if ($resultWork->rowCount()>0) {
																	$rowWork=$resultWork->fetch() ;
																	
																	if ($rowWork["status"]=="Exemption") {
																		$linkText=_("Exemption") ;
																	}
																	else if ($rowWork["version"]=="Final") {
																		$linkText=_("Final") ;
																	}
																	else {
																		$linkText=_("Draft") . " " . $rowWork["count"] ;
																	}
																	
																	$style="" ;
																	$status="On Time" ;
																	if ($rowWork["status"]=="Exemption") {
																		$status=_("Exemption") ;
																	}
																	else if ($rowWork["status"]=="Late") {
																		$style="style='color: #ff0000; font-weight: bold; border: 2px solid #ff0000; padding: 2px 4px'" ;
																		$status=_("Late") ;
																	}
																	
																	if ($rowWork["type"]=="File") {
																		print "<span title='" . $rowWork["version"] . ". $status. " . sprintf(_('Submitted at %1$s on %2$s'), substr($rowWork["timestamp"],11,5), dateConvertBack($guid, substr($rowWork["timestamp"],0,10))) . "' $style><a href='" . $_SESSION[$guid]["absoluteURL"] . "/" . $rowWork["location"] ."'>$linkText</a></span>" ;
																	}
																	else if ($rowWork["type"]=="Link") {
																		print "<span title='" . $rowWork["version"] . ". $status. " . sprintf(_('Submitted at %1$s on %2$s'), substr($rowWork["timestamp"],11,5), dateConvertBack($guid, substr($rowWork["timestamp"],0,10))) . "' $style><a target='_blank' href='" . $rowWork["location"] ."'>$linkText</a></span>" ;
																	}
																	else {
																		print "<span title='$status. " . sprintf(_('Recorded at %1$s on %2$s'), substr($rowWork["timestamp"],11,5), dateConvertBack($guid, substr($rowWork["timestamp"],0,10))) . "' $style>$linkText</span>" ;
																	}
																}
																else {
																	if (date("Y-m-d H:i:s")<$rowSub["homeworkDueDateTime"]) {
																		print "<span title='Pending'>" . _('Pending') . "</span>" ;
																	}
																	else {
																		if ($row["dateStart"]>$rowSub["date"]) {
																			print "<span title='" . _('Student joined school after assessment was given.') . "' style='color: #000; font-weight: normal; border: 2px none #ff0000; padding: 2px 4px'>" . _('NA') . "</span>" ;
																		}
																		else {
																			if ($rowSub["homeworkSubmissionRequired"]=="Compulsory") {
																				print "<div style='color: #ff0000; font-weight: bold; border: 2px solid #ff0000; padding: 2px 4px; margin: 2px 0px'>" . _('Incomplete') . "</div>" ;
																			}
																			else {
																				print _("Not submitted online") ;
																			}
																		}
																	}
																}
															}
														}
													print "</td>" ;
												print "</tr>" ;
												if (strlen($rowEntry["comment"])>50) {
													print "<tr class='comment-$entryCount' id='comment-$entryCount'>" ;
														print "<td colspan=6>" ;
															print $rowEntry["comment"] ;
														print "</td>" ;
													print "</tr>" ;
												}
											}
											print "</table>" ;
										}
									}
								}
								if ($entryCount<1) {
									print "<div class='error'>" ;
										print _("There are no records to display.") ;
									print "</div>" ;
								}
							}
						}
					}
					*/
					else if ($subpage=="Individual Needs") {
						if (isActionAccessible($guid, $connection2, "/modules/Attendance/report_studentHistory.php")==FALSE) {
							print "<div class='error'>" ;
								print _("Your request failed because you do not have access to this action.");
							print "</div>" ;
						}
						else {
							//Module includes
							include "./modules/Individual Needs/moduleFunctions.php" ;
								
							$statusTable=printINStatusTable($connection2, $gibbonPersonID, "disabled") ;
							if ($statusTable==FALSE) {
								print "<div class='error'>" ;
								print _("Your request failed due to a database error.") ;
								print "</div>" ;
							}
							else {
								print $statusTable ;
							}
							
							print "<h3>" ;
								print _("Individual Education Plan") ;
							print "</h3>" ;
							try {
								$dataIN=array("gibbonPersonID"=>$gibbonPersonID); 
								$sqlIN="SELECT * FROM gibbonin WHERE gibbonPersonID=:gibbonPersonID" ;
								$resultIN=$connection2->prepare($sqlIN);
								$resultIN->execute($dataIN);
							}
							catch(PDOException $e) { 
								print "<div class='error'>" . $e->getMessage() . "</div>" ; 
							}
							
							if ($resultIN->rowCount()!=1) {
								print "<div class='error'>" ;
								print _("There are no records to display.") ;
								print "</div>" ;
							}
							else {
								$rowIN=$resultIN->fetch() ;
								
								print "<div style='font-weight: bold'>" . _('Targets') . "</div>" ;
								print "<p>" . $rowIN["targets"] . "</p>" ;
								
								print "<div style='font-weight: bold; margin-top: 30px'>" . _('Teaching Strategies') . "</div>" ;
								print "<p>" . $rowIN["strategies"] . "</p>" ;
								
								print "<div style='font-weight: bold; margin-top: 30px'>" . _('Notes & Review') . "s</div>" ;
								print "<p>" . $rowIN["notes"] . "</p>" ;
							}
						}
					}
					else if ($subpage=="Library Borrowing Record") {
						if (isActionAccessible($guid, $connection2, "/modules/Library/report_studentBorrowingRecord.php")==FALSE) {
							print "<div class='error'>" ;
								print _("Your request failed because you do not have access to this action.");
							print "</div>" ;
						}
						else {
							include "./modules/Library/moduleFunctions.php" ;
							
							//Print borrowing record
							$output=getBorrowingRecord($guid, $connection2, $gibbonPersonID) ;
							if ($output==FALSE) {
								print "<div class='error'>" ;
									print _("Your request failed due to a database error.") ;
								print "</div>" ;
							}
							else {
								print $output ;
							}
						}
					}
					else if ($subpage=="Timetable") {
						if (isActionAccessible($guid, $connection2, "/modules/Timetable/tt_view.php")==FALSE) {
							print "<div class='error'>" ;
								print _("Your request failed because you do not have access to this action.");
							print "</div>" ;
						}
						else {
							if (isActionAccessible($guid, $connection2, "/modules/Timetable Admin/courseEnrolment_manage_byPerson_edit.php")==TRUE) {
								$role=getRoleCategory($row["gibbonRoleIDPrimary"], $connection2) ;
								if ($role=="Student" OR $role=="Staff") {
									print "<div class='linkTop'>" ;
									print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Timetable Admin/courseEnrolment_manage_byPerson_edit.php&gibbonPersonID=$gibbonPersonID&gibbonSchoolYearID=" . $_SESSION[$guid]["gibbonSchoolYearID"] . "&type=$role'>" . _('Edit') . "<img style='margin: 0 0 -4px 5px' title='" . _('Edit') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/config.png'/></a> " ;
									print "</div>" ;
								}
							}
						
							include "./modules/Timetable/moduleFunctions.php" ;
							$ttDate=NULL ;
							if (isset($_POST["ttDate"])) {
								$ttDate=dateConvertToTimestamp(dateConvert($guid, $_POST["ttDate"]));
							}
							$tt=renderTT($guid, $connection2,$gibbonPersonID, "", FALSE, $ttDate, "/modules/Students/student_view_details.php", "&gibbonPersonID=$gibbonPersonID&search=$search&allStudents=$allStudents&subpage=Timetable") ;
							if ($tt!=FALSE) {
								print $tt ;
							}
							else {
								print "<div class='error'>" ;
									print _("There are no records to display.") ;
								print "</div>" ;
							}
						}
					}
					else if ($subpage=="External Assessment") {
						if (isActionAccessible($guid, $connection2, "/modules/External Assessment/externalAssessment_details.php")==FALSE) {
							print "<div class='error'>" ;
								print _("Your request failed because you do not have access to this action.");
							print "</div>" ;
						}
						else {
							include "./modules/External Assessment/moduleFunctions.php" ;
							
							//Print assessments
							$gibbonYearGroupID="" ;
							if (isset($row["gibbonYearGroupID"])) {
								$gibbonYearGroupID=$row["gibbonYearGroupID"] ;
							}
							externalAssessmentDetails($guid, $gibbonPersonID, $connection2, $gibbonYearGroupID) ;
						}
					}
					else if ($subpage=="Activities") {
						if (!(isActionAccessible($guid, $connection2, "/modules/Activities/report_activityChoices_byStudent"))) {
							print "<div class='error'>" ;
								print _("Your request failed because you do not have access to this action.");
							print "</div>" ;
						}
						else {
							print "<p>" ;
							print _("This report shows the current and historical activities that a student has enroled in.") ;
							print "</p>" ;

							$dateType=getSettingByScope($connection2, 'Activities', 'dateType') ;
							if ($dateType=="Term" ) {
								$maxPerTerm=getSettingByScope($connection2, 'Activities', 'maxPerTerm') ;
							}
							
							try {
								$dataYears=array("gibbonPersonID"=>$gibbonPersonID); 
								$sqlYears="SELECT * FROM gibbonstudentenrolment JOIN gibbonschoolyear ON (gibbonstudentenrolment.gibbonSchoolYearID=gibbonschoolyear.gibbonSchoolYearID) WHERE gibbonPersonID=:gibbonPersonID ORDER BY sequenceNumber DESC" ;
								$resultYears=$connection2->prepare($sqlYears);
								$resultYears->execute($dataYears);
							}
							catch(PDOException $e) { 
								print "<div class='error'>" . $e->getMessage() . "</div>" ; 
							}

							if ($resultYears->rowCount()<1) {
								print "<div class='error'>" ;
								print _("There are no records to display.") ;
								print "</div>" ;
							}
							else {
								$yearCount=0 ;
								while ($rowYears=$resultYears->fetch()) {
			
									$class="" ;
									if ($yearCount==0) {
										$class="class='top'" ;
									}
									print "<h3 $class>" ;
									print $rowYears["name"] ;
									print "</h3>" ;
			
									$yearCount++ ;
									try {
										$data=array("gibbonPersonID"=>$gibbonPersonID, "gibbonSchoolYearID"=>$rowYears["gibbonSchoolYearID"]); 
										$sql="SELECT gibbonactivity.*, gibbonactivitystudent.status, NULL AS role FROM gibbonactivity JOIN gibbonactivitystudent ON (gibbonactivity.gibbonActivityID=gibbonactivitystudent.gibbonActivityID) WHERE gibbonactivitystudent.gibbonPersonID=:gibbonPersonID AND gibbonSchoolYearID=:gibbonSchoolYearID AND active='Y' ORDER BY name" ; 
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
										print "<table cellspacing='0' style='width: 100%'>" ;
											print "<tr class='head'>" ;
												print "<th>" ;
													print _("Activity") ;
												print "</th>" ;
												$options=getSettingByScope($connection2, "Activities", "activityTypes") ;
												if ($options!="") {
													print "<th>" ;
														print _("Type") ;
													print "</th>" ;
												}
												print "<th>" ;
													if ($dateType!="Date") {
														print _("Term") ;
													}
													else {
														print _("Dates") ;
													}
												print "</th>" ;
												print "<th>" ;
													print _("Status") ;
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
												$count++ ;
										
												//COLOR ROW BY STATUS!
												print "<tr class=$rowNum>" ;
													print "<td>" ;
														print $row["name"] ;
													print "</td>" ;
													if ($options!="") {
														print "<td>" ;
															print trim($row["type"]) ;
														print "</td>" ;
													}
													print "<td>" ;
														if ($dateType!="Date") {
															$terms=getTerms($connection2, $_SESSION[$guid]["gibbonSchoolYearID"], true) ;
															$termList="" ;
															for ($i=0; $i<count($terms); $i=$i+2) {
																if (is_numeric(strpos($row["gibbonSchoolYearTermIDList"], $terms[$i]))) {
																	$termList.=$terms[($i+1)] . "<br/>" ;
																}
															}
															print $termList ;
														}
														else {
															if (substr($row["programStart"],0,4)==substr($row["programEnd"],0,4)) {
																if (substr($row["programStart"],5,2)==substr($row["programEnd"],5,2)) {
																	print date("F", mktime(0, 0, 0, substr($row["programStart"],5,2))) . " " . substr($row["programStart"],0,4) ;
																}
																else {
																	print date("F", mktime(0, 0, 0, substr($row["programStart"],5,2))) . " - " . date("F", mktime(0, 0, 0, substr($row["programEnd"],5,2))) . "<br/>" . substr($row["programStart"],0,4) ;
																}
															}
															else {
																print date("F", mktime(0, 0, 0, substr($row["programStart"],5,2))) . " " . substr($row["programStart"],0,4) . " -<br/>" . date("F", mktime(0, 0, 0, substr($row["programEnd"],5,2))) . " " . substr($row["programEnd"],0,4) ;
															}
														}
													print "</td>" ;
													print "<td>" ;
														if ($row["status"]!="") {
															print $row["status"] ;
														}
														else {
															print "<i>" . _('NA') . "</i>" ;
														}
													print "</td>" ;
													print "<td>" ;
														print "<a class='thickbox' href='" . $_SESSION[$guid]["absoluteURL"] . "/fullscreen.php?q=/modules/Activities/activities_my_full.php&gibbonActivityID=" . $row["gibbonActivityID"] . "&width=1000&height=550'><img title='" . _('View Details') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/plus.png'/></a> " ;
													print "</td>" ;
												print "</tr>" ;
											}
										print "</table>" ;		
									}
								}
							}
						}
					}
					else if ($subpage=="Homework") {
						if (!(isActionAccessible($guid, $connection2, "/modules/Planner/planner_edit.php") OR isActionAccessible($guid, $connection2, "/modules/Planner/planner_view_full.php"))) {
							print "<div class='error'>" ;
								print _("Your request failed because you do not have access to this action.");
							print "</div>" ;
						}
						else {
							print "<h4>" ;
							print _("Upcoming Deadlines") ;
							print "</h4>" ;
							
							try {
								$data=array("gibbonPersonID"=>$gibbonPersonID, "gibbonSchoolYearID"=>$_SESSION[$guid]["gibbonSchoolYearID"]); 
								$sql="
								(SELECT 'teacherRecorded' AS type, gibbonPlannerEntryID, gibbonUnitID, gibboncourse.nameShort AS course, gibboncourseclass.nameShort AS class, gibbonplannerentry.name, date, timeStart, timeEnd, viewableStudents, viewableParents, homework, homeworkDueDateTime, role FROM gibbonplannerentry JOIN gibboncourseclass ON (gibbonplannerentry.gibbonCourseClassID=gibboncourseclass.gibbonCourseClassID) JOIN gibboncourseclassperson ON (gibboncourseclass.gibbonCourseClassID=gibboncourseclassperson.gibbonCourseClassID) JOIN gibboncourse ON (gibboncourse.gibbonCourseID=gibboncourseclass.gibbonCourseID) WHERE gibbonSchoolYearID=:gibbonSchoolYearID AND gibboncourseclassperson.gibbonPersonID=:gibbonPersonID AND NOT role='Student - Left' AND NOT role='Teacher - Left' AND homework='Y' AND (role='Teacher' OR (role='Student' AND viewableStudents='Y')) AND homeworkDueDateTime>'" . date("Y-m-d H:i:s") . "' AND ((date<'" . date("Y-m-d") . "') OR (date='" . date("Y-m-d") . "' AND timeEnd<='" . date("H:i:s") . "')))
								UNION
								(SELECT 'studentRecorded' AS type, gibbonplannerentry.gibbonPlannerEntryID, gibbonUnitID, gibboncourse.nameShort AS course, gibboncourseclass.nameShort AS class, gibbonplannerentry.name, date, timeStart, timeEnd, 'Y' AS viewableStudents, 'Y' AS viewableParents, 'Y' AS homework, gibbonplannerentrystudenthomework.homeworkDueDateTime, role FROM gibbonplannerentry JOIN gibboncourseclass ON (gibbonplannerentry.gibbonCourseClassID=gibboncourseclass.gibbonCourseClassID) JOIN gibboncourseclassperson ON (gibboncourseclass.gibbonCourseClassID=gibboncourseclassperson.gibbonCourseClassID) JOIN gibboncourse ON (gibboncourse.gibbonCourseID=gibboncourseclass.gibbonCourseID) JOIN gibbonplannerentrystudenthomework ON (gibbonplannerentrystudenthomework.gibbonPlannerEntryID=gibbonplannerentry.gibbonPlannerEntryID AND gibbonplannerentrystudenthomework.gibbonPersonID=gibboncourseclassperson.gibbonPersonID) WHERE gibbonSchoolYearID=:gibbonSchoolYearID AND gibboncourseclassperson.gibbonPersonID=:gibbonPersonID AND NOT role='Student - Left' AND NOT role='Teacher - Left' AND (role='Teacher' OR (role='Student' AND viewableStudents='Y')) AND gibbonplannerentrystudenthomework.homeworkDueDateTime>'" . date("Y-m-d H:i:s") . "' AND ((date<'" . date("Y-m-d") . "') OR (date='" . date("Y-m-d") . "' AND timeEnd<='" . date("H:i:s") . "')))
								 ORDER BY homeworkDueDateTime, type" ;
								 $result=$connection2->prepare($sql);
								$result->execute($data);
							}
							catch(PDOException $e) { 
								print "<div class='error'>" . $e->getMessage() . "</div>" ; 
							}
							
							if ($result->rowCount()<1) {
								print "<div class='success'>" ;
									print _("No upcoming deadlines!") ;
								print "</div>" ;
							}
							else {
								print "<ol>" ;
								while ($row=$result->fetch()) {
									$diff=(strtotime(substr($row["homeworkDueDateTime"],0,10)) - strtotime(date("Y-m-d")))/86400 ;
									$style="style='padding-right: 3px;'" ;
									if ($diff<2) {
										$style="style='padding-right: 3px; border-right: 10px solid #cc0000'" ;	
									}
									else if ($diff<4) {
										$style="style='padding-right: 3px; border-right: 10px solid #D87718'" ;	
									}
									print "<li $style>" ;
									print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Planner/planner_view_full.php&search=$gibbonPersonID&gibbonPlannerEntryID=" . $row["gibbonPlannerEntryID"] . "&viewBy=date&date=" . $row["date"] . "&width=1000&height=550'>" . $row["course"] . "." . $row["class"] . "</a><br/>" ;
									print "<span style='font-style: italic'>" . sprintf(_('Due at %1$s on %2$s'), substr($row["homeworkDueDateTime"],11,5), dateConvertBack($guid, substr($row["homeworkDueDateTime"],0,10))) ;
									print "</li>" ;
								}
								print "</ol>" ;
							}
							
							$style="" ;
							
							print "<h4>" ;
							print _("Homework History") ;
							print "</h4>" ;
							
							$gibbonCourseClassIDFilter=NULL ;
							$filter=NULL ;
							$filter2=NULL ;
							if (isset($_GET["gibbonCourseClassIDFilter"])) {
								$gibbonCourseClassIDFilter=$_GET["gibbonCourseClassIDFilter"] ;
							}
							$data=array() ;
							if ($gibbonCourseClassIDFilter!="") {
								$data["gibbonCourseClassIDFilter"]=$gibbonCourseClassIDFilter ;
								$data["gibbonCourseClassIDFilter2"]=$gibbonCourseClassIDFilter ;
								$filter=" AND gibbonplannerentry.gibbonCourseClassID=:gibbonCourseClassIDFilter" ;
								$filte2=" AND gibbonplannerentry.gibbonCourseClassID=:gibbonCourseClassIDFilte2" ;
							}
							
							try {
								$data["gibbonPersonID"]=$gibbonPersonID;
								$data["gibbonSchoolYearID"]=$_SESSION[$guid]["gibbonSchoolYearID"] ;
								$sql="
								(SELECT 'teacherRecorded' AS type, gibbonPlannerEntryID, gibbonUnitID, gibbonHookID, gibbonplannerentry.gibbonCourseClassID, gibboncourse.nameShort AS course, gibboncourseclass.nameShort AS class, gibbonplannerentry.name, date, timeStart, timeEnd, viewableStudents, viewableParents, homework, role, homeworkDueDateTime, homeworkDetails, homeworkSubmission, homeworkSubmissionRequired FROM gibbonplannerentry JOIN gibboncourseclass ON (gibbonplannerentry.gibbonCourseClassID=gibboncourseclass.gibbonCourseClassID) JOIN gibboncourseclassperson ON (gibboncourseclass.gibbonCourseClassID=gibboncourseclassperson.gibbonCourseClassID) JOIN gibboncourse ON (gibboncourse.gibbonCourseID=gibboncourseclass.gibbonCourseID) WHERE gibboncourseclassperson.gibbonPersonID=:gibbonPersonID AND NOT role='Student - Left' AND NOT role='Teacher - Left' AND homework='Y' AND gibbonSchoolYearID=:gibbonSchoolYearID AND (date<'" . date("Y-m-d") . "' OR (date='" . date("Y-m-d") . "' AND timeEnd<='" . date("H:i:s") . "')) $filter)
								UNION
								(SELECT 'studentRecorded' AS type, gibbonplannerentry.gibbonPlannerEntryID, gibbonUnitID, gibbonHookID, gibbonplannerentry.gibbonCourseClassID, gibboncourse.nameShort AS course, gibboncourseclass.nameShort AS class, gibbonplannerentry.name, date, timeStart, timeEnd, 'Y' AS viewableStudents, 'Y' AS viewableParents, 'Y' AS homework, role, gibbonplannerentrystudenthomework.homeworkDueDateTime AS homeworkDueDateTime, gibbonplannerentrystudenthomework.homeworkDetails AS homeworkDetails, 'N' AS homeworkSubmission, '' AS homeworkSubmissionRequired FROM gibbonplannerentry JOIN gibboncourseclass ON (gibbonplannerentry.gibbonCourseClassID=gibboncourseclass.gibbonCourseClassID) JOIN gibboncourseclassperson ON (gibboncourseclass.gibbonCourseClassID=gibboncourseclassperson.gibbonCourseClassID) JOIN gibboncourse ON (gibboncourse.gibbonCourseID=gibboncourseclass.gibbonCourseID) JOIN gibbonplannerentrystudenthomework ON (gibbonplannerentrystudenthomework.gibbonPlannerEntryID=gibbonplannerentry.gibbonPlannerEntryID AND gibbonplannerentrystudenthomework.gibbonPersonID=gibboncourseclassperson.gibbonPersonID) WHERE gibboncourseclassperson.gibbonPersonID=:gibbonPersonID AND NOT role='Student - Left' AND NOT role='Teacher - Left' AND gibbonSchoolYearID=:gibbonSchoolYearID AND (date<'" . date("Y-m-d") . "' OR (date='" . date("Y-m-d") . "' AND timeEnd<='" . date("H:i:s") . "')) $filter)
								ORDER BY date DESC, timeStart DESC" ; 
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
								print "<div class='linkTop'>" ;
									print "<form method='get' action='" . $_SESSION[$guid]["absoluteURL"] . "/index.php'>" ;
										print"<table class='blank' cellspacing='0' style='float: right; width: 250px; margin: 0px 0px'>" ;	
											print"<tr>" ;
												print"<td style='width: 190px'>" ; 
													print"<select name='gibbonCourseClassIDFilter' id='gibbonCourseClassIDFilter' style='width:190px'>" ;
														print"<option value=''></option>" ;
														try {
															$dataSelect=array("gibbonPersonID"=>$gibbonPersonID, "gibbonSchoolYearID"=>$_SESSION[$guid]["gibbonSchoolYearID"], "date"=>date("Y-m-d")); 
															$sqlSelect="SELECT DISTINCT gibboncourse.nameShort AS course, gibboncourseclass.nameShort AS class, gibbonplannerentry.gibbonCourseClassID FROM gibbonplannerentry JOIN gibboncourseclass ON (gibbonplannerentry.gibbonCourseClassID=gibboncourseclass.gibbonCourseClassID) JOIN gibboncourseclassperson ON (gibboncourseclass.gibbonCourseClassID=gibboncourseclassperson.gibbonCourseClassID) JOIN gibboncourse ON (gibboncourse.gibbonCourseID=gibboncourseclass.gibbonCourseID) WHERE gibboncourseclassperson.gibbonPersonID=:gibbonPersonID AND NOT role='Student - Left' AND NOT role='Teacher - Left' AND homework='Y' AND gibbonSchoolYearID=:gibbonSchoolYearID AND date<=:date ORDER BY course, class" ; 
															$resultSelect=$connection2->prepare($sqlSelect);
															$resultSelect->execute($dataSelect);
														}
														catch(PDOException $e) { }
														while ($rowSelect=$resultSelect->fetch()) {
															$selected="" ;
															if ($rowSelect["gibbonCourseClassID"]==$gibbonCourseClassIDFilter) {
																$selected="selected" ;
															}
															print"<option $selected value='" . $rowSelect["gibbonCourseClassID"] . "'>" . htmlPrep($rowSelect["course"]) . "." . htmlPrep($rowSelect["class"]) . "</option>" ;
														}
													 print"</select>" ;
												print"</td>" ;
												print"<td class='right'>" ;
													print"<input type='submit' value='" . _('Go') . "' style='margin-right: 0px'>" ;
													print"<input type='hidden' name='q' value='/modules/Students/student_view_details.php'>" ;
													print"<input type='hidden' name='subpage' value='Homework'>" ;
													print"<input type='hidden' name='gibbonPersonID' value='$gibbonPersonID'>" ;
												print"</td>" ;
											print"</tr>" ;
										print"</table>" ;
									print"</form>" ;
								print "</div>" ; 
								print "<table cellspacing='0' style='width: 100%'>" ;
									print "<tr class='head'>" ;
										print "<th>" ;
											print _("Class") . "</br>" ;
											print "<span style='font-size: 85%; font-style: italic'>" . _('Date') . "</span>" ;
										print "</th>" ;
										print "<th>" ;
											print _("Lesson") . "</br>" ;
											print "<span style='font-size: 85%; font-style: italic'>" . _('Unit') . "</span>" ;
										print "</th>" ;
										print "<th style='min-width: 25%'>" ;
											print _("Type") . "<br/>" ;
											print "<span style='font-size: 85%; font-style: italic'>" . _('Details') . "</span>" ;
										print "</th>" ;
										print "<th>" ;
											print _("Deadline") ;
										print "</th>" ;
										print "<th>" ;
											print _("Online Submission") ;
										print "</th>" ;
										print "<th>" ;
											print _("Actions") ;
										print "</th>" ;
									print "</tr>" ;
									
									$count=0;
									$rowNum="odd" ;
									while ($row=$result->fetch()) {
										if (!($row["role"]=="Student" AND $row["viewableParents"]=="N")) {
											if ($count%2==0) {
												$rowNum="even" ;
											}
											else {
												$rowNum="odd" ;
											}
											$count++ ;
											
											//Highlight class in progress
											if ((date("Y-m-d")==$row["date"]) AND (date("H:i:s")>$row["timeStart"]) AND (date("H:i:s")<$row["timeEnd"])) {
												$rowNum="current" ;
											}
											
											//COLOR ROW BY STATUS!
											print "<tr class=$rowNum>" ;
												print "<td>" ;
													print "<b>" . $row["course"] . "." . $row["class"] . "</b></br>" ;
													print "<span style='font-size: 85%; font-style: italic'>" . dateConvertBack($guid, $row["date"]) . "</span>" ;
												print "</td>" ;
												print "<td>" ;
													print "<b>" . $row["name"] . "</b><br/>" ;
													print "<span style='font-size: 85%; font-style: italic'>" ;
														if ($row["gibbonUnitID"]!="") {
															try {
																$dataUnit=array("gibbonUnitID"=>$row["gibbonUnitID"]); 
																$sqlUnit="SELECT * FROM gibbonunit WHERE gibbonUnitID=:gibbonUnitID" ;
																$resultUnit=$connection2->prepare($sqlUnit);
																$resultUnit->execute($dataUnit);
															}
															catch(PDOException $e) { 
																print "<div class='error'>" . $e->getMessage() . "</div>" ; 
															}
															if ($resultUnit->rowCount()==1) {
																$rowUnit=$resultUnit->fetch() ;
																print $rowUnit["name"] ;
															}
														}
													print "</span>" ;
												print "</td>" ;
												print "<td>" ;
													if ($row["type"]=="teacherRecorded") {
														print "Teacher Recorded" ;
													}
													else {
														print "Student Recorded" ;
													}
													print  "<br/>" ;
													print "<span style='font-size: 85%; font-style: italic'>" ;
														if ($row["homeworkDetails"]!="") {
															if (strlen(strip_tags($row["homeworkDetails"]))<21) {
																print strip_tags($row["homeworkDetails"]) ;
															}
															else {
																print "<span $style title='" . htmlPrep(strip_tags($row["homeworkDetails"])) . "'>" . substr(strip_tags($row["homeworkDetails"]), 0, 20) . "...</span>" ;
															}
														}
													print "</span>" ;
												print "</td>" ;
												print "<td>" ;
													print dateConvertBack($guid, substr($row["homeworkDueDateTime"],0,10)) ;
												print "</td>" ;
												print "<td>" ;
													if ($row["homeworkSubmission"]=="Y") {
														print "<b>" . $row["homeworkSubmissionRequired"] . "<br/></b>" ;
														if ($row["role"]=="Student") {
															try {
																$dataVersion=array("gibbonPlannerEntryID"=>$row["gibbonPlannerEntryID"], "gibbonPersonID"=>$gibbonPersonID); 
																$sqlVersion="SELECT * FROM gibbonplannerentryhomework WHERE gibbonPlannerEntryID=:gibbonPlannerEntryID AND gibbonPersonID=:gibbonPersonID ORDER BY count DESC" ;
																$resultVersion=$connection2->prepare($sqlVersion);
																$resultVersion->execute($dataVersion);
															}
															catch(PDOException $e) { 
																print "<div class='error'>" . $e->getMessage() . "</div>" ; 
															}
															if ($resultVersion->rowCount()<1) {
																//Before deadline
																if (date("Y-m-d H:i:s")<$row["homeworkDueDateTime"]) {
																	print "<span title='" . _('Pending') . "'>" . _('Pending') . "</span>" ;
																}
																//After
																else {
																	if (@$row["dateStart"]>@$rowSub["date"]) {
																		print "<span title='" . _('Student joined school after assessment was given.') . "' style='color: #000; font-weight: normal; border: 2px none #ff0000; padding: 2px 4px'>" . _('NA') . "</span>" ;
																	}
																	else {
																		if ($row["homeworkSubmissionRequired"]=="Compulsory") {
																			print "<div style='color: #ff0000; font-weight: bold; border: 2px solid #ff0000; padding: 2px 4px; margin: 2px 0px'>" . _('Incomplete') . "</div>" ;
																		}
																		else {
																			print _("Not submitted online") ;
																		}
																	}
																}
															}
															else {
																$rowVersion=$resultVersion->fetch() ;
																if ($rowVersion["status"]=="On Time" OR $rowVersion["status"]=="Exemption") {
																	print $rowVersion["status"] ;
																} 
																else {
																	if ($row["homeworkSubmissionRequired"]=="Compulsory") {
																		print "<div style='color: #ff0000; font-weight: bold; border: 2px solid #ff0000; padding: 2px 4px; margin: 2px 0px'>" . $rowVersion["status"] . "</div>" ;
																	}
																	else {
																		print $rowVersion["status"] ;
																	}
																}
															}
														}
													}
												print "</td>" ;
												print "<td>" ;
													print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Planner/planner_view_full.php&search=$gibbonPersonID&gibbonPlannerEntryID=" . $row["gibbonPlannerEntryID"] . "&viewBy=class&gibbonCourseClassID=" . $row["gibbonCourseClassID"] . "&width=1000&height=550'><img title='" . _('View Details') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/plus.png'/></a> " ;
												print "</td>" ;
											print "</tr>" ;
										}
								}
								print "</table>" ;
							}
						}								
					}
					else if ($subpage=="Behaviour Record") {
						if (isActionAccessible($guid, $connection2, "/modules/Behaviour/behaviour_view.php")==FALSE) {
							print "<div class='error'>" ;
								print _("Your request failed because you do not have access to this action.");
							print "</div>" ;
						}
						else {
							include "./modules/Behaviour/moduleFunctions.php" ;
							
							//Print assessments
							getBehaviourRecord($guid, $gibbonPersonID, $connection2) ;
						}
					}
					
					//GET HOOK IF SPECIFIED
					if ($hook!="" AND $module!="" AND $action!="") {
						//GET HOOKS AND DISPLAY LINKS
						//Check for hook
						try {
							$dataHook=array("gibbonHookID"=>$_GET["gibbonHookID"]); 
							$sqlHook="SELECT * FROM gibbonhook WHERE gibbonHookID=:gibbonHookID" ;
							$resultHook=$connection2->prepare($sqlHook);
							$resultHook->execute($dataHook);
						}
						catch(PDOException $e) { 
							print "<div class='error'>" . $e->getMessage() . "</div>" ; 
						}
						if ($resultHook->rowCount()!=1) {
							print "<div class='error'>" ;
								print _("There are no records to display.");
							print "</div>" ;
						}
						else {
							$rowHook=$resultHook->fetch() ;
							$options=unserialize($rowHook["options"]) ;
							
							//Check for permission to hook
							try {
								$dataHook=array("gibbonRoleIDCurrent"=>$_SESSION[$guid]["gibbonRoleIDCurrent"], "sourceModuleName"=>$options["sourceModuleName"]); 
								$sqlHook="SELECT gibbonhook.name, gibbonmodule.name AS module, gibbonaction.name AS action FROM gibbonhook JOIN gibbonmodule ON (gibbonmodule.name='" . $options["sourceModuleName"] . "') JOIN gibbonaction ON (gibbonaction.name='" . $options["sourceModuleAction"] . "') JOIN gibbonpermission ON (gibbonpermission.gibbonActionID=gibbonaction.gibbonActionID) WHERE gibbonaction.gibbonModuleID=(SELECT gibbonModuleID FROM gibbonmodule WHERE gibbonpermission.gibbonRoleID=:gibbonRoleIDCurrent AND name=:sourceModuleName) AND gibbonhook.type='Student Profile' ORDER BY name" ;
								$resultHook=$connection2->prepare($sqlHook);
								$resultHook->execute($dataHook);
							}
							catch(PDOException $e) { 
								print "<div class='error'>" . $e->getMessage() . "</div>" ; 
							}
							if ($resultHook->rowcount()!=1) {
								print "<div class='error'>" ;
									print _("Your request failed because you do not have access to this action.");
								print "</div>" ;
							}
							else {
								$include=$_SESSION[$guid]["absolutePath"] . "/modules/" . $options["sourceModuleName"] . "/" . $options["sourceModuleInclude"] ;
								if (!file_exists($include)) {
									print "<div class='error'>" ;
										print _("The selected page cannot be displayed due to a hook error.") ;
									print "</div>" ;
								}
								else {
									include $include ;
								}
							}
						}
					}
					
					//Set sidebar
					$_SESSION[$guid]["sidebarExtra"]="" ;
					
					//Show alerts
					$alert=getAlertBar($guid, $connection2, $gibbonPersonID, $row["privacy"], "", FALSE) ;
					$_SESSION[$guid]["sidebarExtra"].="<div style='border-top: 1px solid #c00; background-color: none; font-size: 12px; margin: 3px 0 0px 0; width: 240px; text-align: left; height: 16px; padding: 5px 5px;'>" ;
					if ($alert=="") {
						$_SESSION[$guid]["sidebarExtra"].="<b>" . _('No Current Alerts') . "</b>" ; 
					}
					else {
						$_SESSION[$guid]["sidebarExtra"].="<b>" . _('Current Alerts:') . "</b>$alert" ; 
					}
					$_SESSION[$guid]["sidebarExtra"].="</div>" ;

					$_SESSION[$guid]["sidebarExtra"].=getUserPhoto($guid, $row["image_240"], 240) ;
					
					
				
					//PERSONAL DATA menu ITEMS
					$_SESSION[$guid]["sidebarExtra"].="<h4>" . _('Personal') . "</h4>" ;
					$_SESSION[$guid]["sidebarExtra"].="<ul class='moduleMenu'>" ;
					$style="" ;
					if ($subpage=="Summary") {
						$style="style='font-weight: bold'" ;
					}
					//$_SESSION[$guid]["sidebarExtra"].="<li><a $style href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=" . $_GET["q"] . "&gibbonPersonID=$gibbonPersonID&search=" . $search . "&search=$search&allStudents=$allStudents&subpage=Summary'>" . _('Summary') . "</a></li>" ;
					$style="" ;
					if ($subpage=="Personal") {
						$style="style='font-weight: bold'" ;
					}
					$_SESSION[$guid]["sidebarExtra"].="<li><a $style href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=" . $_GET["q"] . "&gibbonPersonID=$gibbonPersonID&search=" . $search . "&search=$search&allStudents=$allStudents&subpage=Personal'>" . _('Personal') . "</a></li>" ;
					$style="" ;
					if ($subpage=="Family") {
						$style="style='font-weight: bold'" ;
					}
					$_SESSION[$guid]["sidebarExtra"].="<li><a $style href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=" . $_GET["q"] . "&gibbonPersonID=$gibbonPersonID&search=" . $search . "&search=$search&allStudents=$allStudents&subpage=Family'>" . _('Family') . "</a></li>" ;
					$style="" ;
					if ($subpage=="Emergency Contacts") {
						$style="style='font-weight: bold'" ;
					}
					//$_SESSION[$guid]["sidebarExtra"].="<li><a $style href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=" . $_GET["q"] . "&gibbonPersonID=$gibbonPersonID&search=" . $search . "&search=$search&allStudents=$allStudents&subpage=Emergency Contacts'>" . _('Emergency Contacts') . "</a></li>" ;
					$style="" ;
					if ($subpage=="Medical") {
						$style="style='font-weight: bold'" ;
					}
					$_SESSION[$guid]["sidebarExtra"].="<li><a $style href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=" . $_GET["q"] . "&gibbonPersonID=$gibbonPersonID&search=" . $search . "&search=$search&allStudents=$allStudents&subpage=Medical'>" . _('Medical') . "</a></li>" ;
					if (isActionAccessible($guid, $connection2, "/modules/Students/student_view_details_notes_add.php")) {
						$style="" ;
						if ($subpage=="Notes") {
							$style="style='font-weight: bold'" ;
						}
						$_SESSION[$guid]["sidebarExtra"].="<li><a $style href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=" . $_GET["q"] . "&gibbonPersonID=$gibbonPersonID&search=" . $search . "&search=$search&allStudents=$allStudents&subpage=Notes'>" . _('Notes') . "</a></li>" ;
					}
					if (isActionAccessible($guid, $connection2, "/modules/Attendance/report_studentHistory.php")) {
						$style="" ;
						if ($subpage=="School Attendance") {
							$style="style='font-weight: bold'" ;
						}
						$_SESSION[$guid]["sidebarExtra"].="<li><a $style href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=" . $_GET["q"] . "&gibbonPersonID=$gibbonPersonID&search=" . $search . "&search=$search&allStudents=$allStudents&subpage=School Attendance'>" . _('School Attendance') . "</a></li>" ;
					}
					//if (isActionAccessible($guid, $connection2, "/modules/Attendance/report_studentHistory.php")) {
					if (true) {
						$style="" ;
						if ($subpage=="Activity and Achievement") {
							$style="style='font-weight: bold'" ;
						}
						$_SESSION[$guid]["sidebarExtra"].="<li><a $style href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=" . $_GET["q"] . "&gibbonPersonID=$gibbonPersonID&search=" . $search . "&search=$search&allStudents=$allStudents&subpage=Activity and Achievement'>" . _('Activity & Achievement') . "</a></li>" ;
					}
					$_SESSION[$guid]["sidebarExtra"].="</ul>" ;
					
					
					//ARR menu ITEMS
					if (isActionAccessible($guid, $connection2, "/modules/Markbook/markbook_view.php") OR isActionAccessible($guid, $connection2, "/modules/External Assessment/externalAssessment_details.php")) {
						$_SESSION[$guid]["sidebarExtra"].="<h4>" . _('Assessment') . "</h4>" ;
						$_SESSION[$guid]["sidebarExtra"].="<ul class='moduleMenu'>" ;
						if (isActionAccessible($guid, $connection2, "/modules/Markbook/markbook_view.php")) {
							$style="" ;
							if ($subpage=="Markbook") {
								$style="style='font-weight: bold'" ;
							}
							$_SESSION[$guid]["sidebarExtra"].="<li><a $style href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=" . $_GET["q"] . "&gibbonPersonID=$gibbonPersonID&search=" . $search . "&search=$search&allStudents=$allStudents&subpage=Markbook'>" . _('Markbook') . "</a></li>" ;
						}
						if (isActionAccessible($guid, $connection2, "/modules/External Assessment/externalAssessment_details.php")) {
							$style="" ;
							if ($subpage=="External Assessment") {
								$style="style='font-weight: bold'" ;
							}
							$_SESSION[$guid]["sidebarExtra"].="<li><a $style href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=" . $_GET["q"] . "&gibbonPersonID=$gibbonPersonID&search=" . $search . "&search=$search&allStudents=$allStudents&subpage=External Assessment'>" . _('External Assessment') . "</a></li>" ;
						}
						$_SESSION[$guid]["sidebarExtra"].="</ul>" ;
					}
					
					//LEARNING menu ITEMS
					if (isActionAccessible($guid, $connection2, "/modules/Activities/report_activityChoices_byStudent.php") OR isActionAccessible($guid, $connection2, "/modules/Individual Needs/in_view.php") OR isActionAccessible($guid, $connection2, "/modules/Timetable/tt_view.php") OR isActionAccessible($guid, $connection2, "/modules/Planner/planner_edit.php") OR isActionAccessible($guid, $connection2, "/modules/Planner/planner_view_full.php")) {
						$_SESSION[$guid]["sidebarExtra"].="<h4>" . _('Learning') . "</h4>" ;
						$_SESSION[$guid]["sidebarExtra"].="<ul class='moduleMenu'>" ;
						if (isActionAccessible($guid, $connection2, "/modules/Activities/report_activityChoices_byStudent.php")) {
							$style="" ;
							if ($subpage=="Activities") {
								$style="style='font-weight: bold'" ;
							}
							$_SESSION[$guid]["sidebarExtra"].="<li><a $style href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=" . $_GET["q"] . "&gibbonPersonID=$gibbonPersonID&search=" . $search . "&search=$search&allStudents=$allStudents&subpage=Activities'>" . _('Activities') . "</a></li>" ;
						}
						if (isActionAccessible($guid, $connection2, "/modules/Planner/planner_edit.php") OR isActionAccessible($guid, $connection2, "/modules/Planner/planner_view_full.php")) {
							$style="" ;
							if ($subpage=="Homework") {
								$style="style='font-weight: bold'" ;
							}
							$_SESSION[$guid]["sidebarExtra"].="<li><a $style href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=" . $_GET["q"] . "&gibbonPersonID=$gibbonPersonID&search=" . $search . "&search=$search&allStudents=$allStudents&subpage=Homework'>" . _('Homework') . "</a></li>" ;
						}
						if (isActionAccessible($guid, $connection2, "/modules/Individual Needs/in_view.php")) {
							$style="" ;
							if ($subpage=="Individual Needs") {
								$style="style='font-weight: bold'" ;
							}
							$_SESSION[$guid]["sidebarExtra"].="<li><a $style href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=" . $_GET["q"] . "&gibbonPersonID=$gibbonPersonID&search=" . $search . "&search=$search&allStudents=$allStudents&subpage=Individual Needs'>" . _('Individual Needs') . "</a></li>" ;
						}
						if (isActionAccessible($guid, $connection2, "/modules/Library/report_studentBorrowingRecord.php")) {
							$style="" ;
							if ($subpage=="Library Borrowing Record") {
								$style="style='font-weight: bold'" ;
							}
							$_SESSION[$guid]["sidebarExtra"].="<li><a $style href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=" . $_GET["q"] . "&gibbonPersonID=$gibbonPersonID&search=" . $search . "&search=$search&allStudents=$allStudents&subpage=Library Borrowing Record'>" . _('Library Borrowing Record') . "</a></li>" ;
						}
						if (isActionAccessible($guid, $connection2, "/modules/Timetable/tt_view.php")) {
							$style="" ;
							if ($subpage=="Timetable") {
								$style="style='font-weight: bold'" ;
							}
							$_SESSION[$guid]["sidebarExtra"].="<li><a $style href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=" . $_GET["q"] . "&gibbonPersonID=$gibbonPersonID&search=" . $search . "&search=$search&allStudents=$allStudents&subpage=Timetable'>" . _('Timetable') . "</a></li>" ;
						}
						$_SESSION[$guid]["sidebarExtra"].="</ul>" ;
					}
					
					//PEOPLE menu ITEMS
					if (isActionAccessible($guid, $connection2, "/modules/Behaviour/behaviour_view.php")) {
						$_SESSION[$guid]["sidebarExtra"].="<h4>" . _('People') . "</h4>" ;
						$_SESSION[$guid]["sidebarExtra"].="<ul class='moduleMenu'>" ;
						if (isActionAccessible($guid, $connection2, "/modules/Behaviour/behaviour_view.php")) {
							$style="" ;
							if ($subpage=="Behaviour Record") {
								$style="style='font-weight: bold'" ;
							}
							$_SESSION[$guid]["sidebarExtra"].="<li><a $style href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=" . $_GET["q"] . "&gibbonPersonID=$gibbonPersonID&search=" . $search . "&search=$search&allStudents=$allStudents&subpage=Behaviour Record'>" . _('Behaviour Record') . "</a></li>" ;
						}
						$_SESSION[$guid]["sidebarExtra"].="</ul>" ;
					}
					
					//GET HOOKS AND DISPLAY LINKS
					//Check for hooks
					try {
						$dataHooks=array(); 
						$sqlHooks="SELECT * FROM gibbonhook WHERE type='Student Profile'" ;
						$resultHooks=$connection2->prepare($sqlHooks);
						$resultHooks->execute($dataHooks);
					}
					catch(PDOException $e) { 
						print "<div class='error'>" . $e->getMessage() . "</div>" ; 
					}
					if ($resultHooks->rowCount()>0) {
						$hooks=array() ;
						$count=0 ;
						while ($rowHooks=$resultHooks->fetch()) {
							$options=unserialize($rowHooks["options"]) ;
							//Check for permission to hook
							try {
								$dataHook=array("gibbonRoleIDCurrent"=>$_SESSION[$guid]["gibbonRoleIDCurrent"], "sourceModuleName"=>$options["sourceModuleName"]); 
								$sqlHook="SELECT gibbonhook.name, gibbonmodule.name AS module, gibbonaction.name AS action FROM gibbonhook JOIN gibbonmodule ON (gibbonmodule.name='" . $options["sourceModuleName"] . "') JOIN gibbonaction ON (gibbonaction.name='" . $options["sourceModuleAction"] . "') JOIN gibbonpermission ON (gibbonpermission.gibbonActionID=gibbonaction.gibbonActionID) WHERE gibbonaction.gibbonModuleID=(SELECT gibbonModuleID FROM gibbonmodule WHERE gibbonpermission.gibbonRoleID=:gibbonRoleIDCurrent AND name=:sourceModuleName) AND gibbonhook.type='Student Profile' ORDER BY name" ;
								$resultHook=$connection2->prepare($sqlHook);
								$resultHook->execute($dataHook);
							}
							catch(PDOException $e) { 
								print "<div class='error'>" . $e->getMessage() . "</div>" ; 
							}
							if ($resultHook->rowCount()==1) {
								$style="" ;
								if ($hook==$rowHooks["name"] AND $_GET["module"]==$options["sourceModuleName"]) {
									$style="style='font-weight: bold'" ;
								}
								$hooks[$count]="<li><a $style href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=" . $_GET["q"] . "&gibbonPersonID=$gibbonPersonID&search=" . $search . "&hook=" . $rowHooks["name"] . "&module=" . $options["sourceModuleName"] . "&action=" . $options["sourceModuleAction"] . "&gibbonHookID=" . $rowHooks["gibbonHookID"] . "'>" . $rowHooks["name"] . "</a></li>" ;
								$count++ ;
							}
						}
						
						if (count($hooks)>0) {
							$_SESSION[$guid]["sidebarExtra"].="<h4>Extras</h4>" ;
							$_SESSION[$guid]["sidebarExtra"].="<ul class='moduleMenu'>" ;
								foreach ($hooks as $hook) {
									$_SESSION[$guid]["sidebarExtra"].=$hook ;
								}
							$_SESSION[$guid]["sidebarExtra"].="</ul>" ;
						}
					}
				//}
			}
		}
	}
}
?>
