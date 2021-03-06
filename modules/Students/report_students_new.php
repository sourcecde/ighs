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

if (isActionAccessible($guid, $connection2, "/modules/Students/report_students_new.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
    
$search_type=1;
//Module includes
include "./modules/" . $_SESSION[$guid]["module"] . "/moduleFunctions.php" ;
if (isActionAccessible($guid, $connection2, "/modules/Students/report_students_new")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
	//Proceed!
	print "<div class='trail'>" ;
	print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . _("Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . _(getModuleName($_GET["q"])) . "</a> > </div><div class='trailEnd'>" . _('New Students') . "</div>" ;
	print "</div>" ;
	
	print "<h2>" ;
	print _("Choose Options") ;
	print "</h2>" ;
	
	$type=NULL ;
	if (isset($_GET["type"])) {
		$type=$_GET["type"] ;
	}
	$ignoreEnrolment=NULL ;
	if (isset($_GET["ignoreEnrolment"])) {
		$ignoreEnrolment=$_GET["ignoreEnrolment"] ;
	}
	$startDateFrom=NULL ;
	if (isset($_GET["startDateFrom"])) {
		$startDateFrom=$_GET["startDateFrom"] ;
	}
	$startDateTo=NULL ;
	if (isset($_GET["startDateTo"])) {
		$startDateTo=$_GET["startDateTo"] ;
	}
	
	$sql="SELECT * from gibbonrollgroup WHERE 1";
	if(isset($select_class) && $select_class!="" && isset($select_year) && $select_year!="")
		$sql.=" AND `gibbonyeargroup`=".$select_class." AND `gibbonSchoolYearID`=".$select_year;
	$result=$connection2->prepare($sql);
	$result->execute();
	$section=$result->fetchAll();
	
	$sql="SELECT * FROM `gibbonschoolyear` WHERE `status`='Current' OR `status`='Upcoming'";
	$result=$connection2->prepare($sql);
	$result->execute();
	$year=$result->fetchAll();
	
	$sql="SELECT * from gibbonyeargroup";
	$result=$connection2->prepare($sql);
	$result->execute();
	$class=$result->fetchAll();
	
	$sql="SELECT DISTINCT `border`,`border_type_name` FROM `fee_boarder_class`";
	$result=$connection2->prepare($sql);
	$result->execute();
	$boarderDB=$result->fetchAll();
	$boarderDetails=array();
	foreach($boarderDB as $b){
		$boarderDetails[$b['border']]=$b['border_type_name'];
	}
	?>
	
	
	
	<form method="get" action="<?php print $_SESSION[$guid]["absoluteURL"]?>/index.php">
		<table class='smallIntBorder' cellspacing='0' style="width: 100%">	
			<!-- FIELDS & CONTROLS FOR TYPE -->
			<script type="text/javascript">
				$(document).ready(function(){
					$("#type").change(function(){
						if ($('select.type option:selected').val()=="Date Range" ) {
							$("#startDateFromRow").slideDown("fast", $("#startDateFromRow").css("display","table-row")); 
							$("#startDateToRow").slideDown("fast", $("#startDateToRow").css("display","table-row")); 
						} else {
							$("#startDateFromRow").css("display","none");
							$("#startDateToRow").css("display","none");
						} 
					 });
				});
			</script>
			<!----<tr>
				<td style='width: 275px'> 
					<b><?php print _('Type') ?> *</b><br/>
				</td>
				<td class="right">
					<select style="width: 302px" name="type" id="type" class="type">
						<?php
						print "<option" ; if ($type=="Current School Year") { print " selected" ; } print " value='Current School Year'>" . _('Current School Year') . "</option>" ;
						print "<option" ; if ($type=="Date Range") { print " selected" ; } print " value='Date Range'>" . _('Date Range') . "</option>" ;
						?>
					</select>
				</td>
			</tr>---->
						<tr>
				<td>Academic Year</td>
				<td>
					<select name="select_year" id="select_year">
					<?php if(isset($_REQUEST['select_year'])){
					
						?>
					<?php foreach ($year as $value) { ?>
							<option value="<?php echo $value['gibbonSchoolYearID']?>" <?php if($_REQUEST['select_class']==$value['gibbonSchoolYearID']){?> selected="selected"<?php } ?>><?php echo $value['name']?></option>
						<?php } ?>
						
					<?php  } else {?>
						<?php foreach ($year as $value) { ?>
							<option value="<?php echo $value['gibbonSchoolYearID']?>" ><?php echo $value['name']?></option>
						<?php } ?>
						<?php } ?>
						
					</select>
				</td>
			</tr>
			<tr>
				<td>Class</td>
				<td>
					<select name="select_class" id="select_class">
					<?php if(isset($_REQUEST['select_class'])){
					
						?>
						<option value="">-Select Class -</option>
					<?php foreach ($class as $value) { ?>
							<option value="<?php echo $value['gibbonYearGroupID']?>" <?php if($_REQUEST['select_class']==$value['gibbonYearGroupID']){?> selected="selected"<?php } ?>><?php echo $value['name']?></option>
						<?php } ?>
						
					<?php  } else {?>
						<option value="">-Select Class -</option>
						<?php foreach ($class as $value) { ?>
							<option value="<?php echo $value['gibbonYearGroupID']?>" ><?php echo $value['name']?></option>
						<?php } ?>
						<?php } ?>
						
					</select>
				</td>
			</tr>
			<tr>
				<td>Section</td>
				<td>
					<select name="select_section" id="select_section">
					<option value="">-Select Section-</option>
					<?php if(isset($_REQUEST['select_class'])){
					
						?>
						<?php foreach ($section as $value) { ?>
							<option value="<?php echo $value['gibbonRollGroupID'];?>" <?php if($_REQUEST['select_section']==$value['gibbonRollGroupID']){?> selected="selected"<?php } ?>><?php echo $value['name'];?></option>
					<?php } ?>
					<?php } ?>
					</select>
				</td>
			</tr>
<!------------------------------------- Shiva
		    <tr>
				<td><b>Boarder Type</b></td>
				<td>
					<select name='select_boarder' id="select_boarder">
							<option value="">Select Boarder</option>
					<?php
/*					foreach($boarderDB as $b){
						$s=isset($_REQUEST['select_boarder'])?($_REQUEST['select_boarder']==$b['border']?"selected":""):"";
						echo "<option value='{$b['border']}' $s>{$b['border_type_name']}</option>";
					}
*/					?>
					</select>
				</td>
			</tr> 
//-->
 
			
			<tr id='startDateFromRow'>
				<td> 
					<b><?php print _('From Date') ?></b><br/>
					<span style="font-size: 90%"><i><?php print _('Earliest student start date to include.') ?><br/><?php print _('Format:') ?> <?php if ($_SESSION[$guid]["i18n"]["dateFormat"]=="") { print "dd/mm/yyyy" ; } else { print $_SESSION[$guid]["i18n"]["dateFormat"] ; }?></i></span>
				</td>
				<td class="right">
					<input name="startDateFrom" id="startDateFrom" maxlength=10 value="<?php print $startDateFrom ?>" type="text" style="width: 300px">
					<script type="text/javascript">
					/*
					var startDateFrom=new LiveValidation('startDateFrom');
						startDateFrom.add( Validate.Format, {pattern: <?php if ($_SESSION[$guid]["i18n"]["dateFormatRegEx"]=="") {  print "/^(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)\d\d$/i" ; } else { print $_SESSION[$guid]["i18n"]["dateFormatRegEx"] ; } ?>, failureMessage: "Use <?php if ($_SESSION[$guid]["i18n"]["dateFormat"]=="") { print "dd/mm/yyyy" ; } else { print $_SESSION[$guid]["i18n"]["dateFormat"] ; }?>." } );
						*/
						 
					</script>
					<script type="text/javascript">
						$(function() {
							$( "#startDateFrom" ).datepicker({ dateFormat: 'dd/mm/yy' });
						});
					</script>
				</td>
			</tr>
			<tr id='startDateToRow' >
				<td> 
				
					<b><?php print _('To Date') ?></b><br/>
					<span style="font-size: 90%"><i><?php print _('Latest student start date to include.') ?><br/><?php print _('Format:') ?> <?php if ($_SESSION[$guid]["i18n"]["dateFormat"]=="") { print "dd/mm/yyyy" ; } else { print $_SESSION[$guid]["i18n"]["dateFormat"] ; }?></i></span>
				</td>
				<td class="right">
					<input name="startDateTo" id="startDateTo" maxlength=10 value="<?php print $startDateTo ?>" type="text" style="width: 300px">
					<script type="text/javascript">
					/*
						var startDateTo=new LiveValidation('startDateTo');
						startDateTo.add( Validate.Format, {pattern: <?php if ($_SESSION[$guid]["i18n"]["dateFormatRegEx"]=="") {  print "/^(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)\d\d$/i" ; } else { print $_SESSION[$guid]["i18n"]["dateFormatRegEx"] ; } ?>, failureMessage: "Use <?php if ($_SESSION[$guid]["i18n"]["dateFormat"]=="") { print "dd/mm/yyyy" ; } else { print $_SESSION[$guid]["i18n"]["dateFormat"] ; }?>." } );
						*/ 
					</script>
					<script type="text/javascript">
						$(function() {
							$( "#startDateTo" ).datepicker({ dateFormat: 'dd/mm/yy' });
						});
					</script>
				</td>
			</tr>
			<tr>
				<td colspan=2 class="right">
					<input type="hidden" name="q" value="/modules/<?php print $_SESSION[$guid]["module"] ?>/report_students_new.php">
					<input type="submit" value="<?php print _("Submit") ; ?>">
					<?php
					if(isset($_GET["startDateFrom"]) && isset($_GET["startDateTo"])){
					?>
					<input type="button" id="print" value="Print" onclick="printDiv();">
					<?php } ?>
				</td>
			</tr>
		</table>
	</form>
<div id="printArea">
	<?php
	if ($startDateFrom!="" OR $startDateTo!="") {
		$proceed=TRUE ;
		    $sql="SELECT * FROM gibbonsetting WHERE gibbonSystemSettingsID='147'";
		    $result=$connection2->prepare($sql);
		    $result->execute();
		    $header2=$result->fetch();
		    
		if ($proceed==FALSE) {
			print "<div class='error'>" ;
				print _("Your request failed because your inputs were invalid.") ;
			print "</div>" ;
		}
		else {
			try {
						$sql="SELECT DISTINCT account_number,admission_number,dateStart,gender,gibbonperson.gibbonPersonID,account_number,dateStart, surname, preferredName, username, dateStart,dob, lastSchool,boarder,gibbonyeargroup.name as class,gibbonrollgroup.name AS section,gibbonstudentenrolment.gibbonStudentEnrolmentID,gibbonstudentenrolment.rollOrder 
								FROM gibbonstudentenrolment 
								JOIN gibbonperson ON (gibbonstudentenrolment.gibbonPersonID=gibbonperson.gibbonPersonID)
								LEFT JOIN gibbonyeargroup ON gibbonstudentenrolment.gibbonYearGroupId=gibbonyeargroup.gibbonYearGroupId
								LEFT JOIN gibbonrollgroup ON gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID  
								WHERE dateStart>='".dateConvert($guid, $startDateFrom)."' AND dateStart<='".dateConvert($guid, $startDateTo)."'";
					 if($_REQUEST['select_class']!='')
					 {
					 	$sql.=" AND gibbonstudentenrolment.gibbonYearGroupID=".$_REQUEST['select_class'];
					 }
					 if($_REQUEST['select_section'])
					 {
					 	$sql.=" AND gibbonstudentenrolment.gibbonRollGroupID=".$_REQUEST['select_section'];
					 }
					 if($_REQUEST['select_year'])
					 {
					 	$sql.=" AND gibbonperson.gibbonSchoolYearIDEntry=".$_REQUEST['select_year'];
					 }
						$sql.=" AND status='Full' ORDER BY account_number ASC" ;
						//echo $sql;
				
				$result=$connection2->prepare($sql);
				$result->execute(); 
				//echo $sql;
			}
			catch(PDOException $e) { print "<div class='error'>" . $e->getMessage() . "</div>" ; }
			if ($result->rowCount()>0) {
					print "<table cellspacing='0' style='width: 100%'>" ;
					print "<thead>"; 
						print "<tr><td colspan='10'>";
							print "<p style='text-align:center; font-weight:bold; font-size:14px; margin-bottom: 5px;'>INDRA GOPAL HIGH SCHOOL</p>" ;
							print "<p style='text-align:center; font-weight:bold; font-size:12px;margin-bottom: 5px;'>{$header2["value"]}</p>" ;
							print "<p style='text-align:center; font-weight:bold; font-size:12px;margin-bottom: 5px;'>" ;
								echo "ADMISSION REGISTER (".$startDateFrom." TO ".$startDateTo.")";
							print "</p>" ;
						print "</td></tr>";
						print "<tr class='head'>" ;
							print "<th>" ;
								print _("Sl.No") ;
							print "</th>" ;
							print "<th>" ;
								print _("Admission&nbsp;Date") ;
							print "</th>" ;
							print "<th>" ;
								print _("Acc&nbsp;No") ;
							print "</th>" ;
							print "<th>" ;
							print _("Admn&nbsp;No") ;
								
							print "</th>" ;
							
							/*
							print "<th>" ;
								print _("Student ID") ;
							print "</th>" ;
							*/
							print "<th>" ;
								print _("Name") ;
							print "</th>" ;
							print "<th>" ;
								print _("Class") ;
							print "</th>" ;
							print "<th>" ;
								print _("Sec") ;
							print "</th>" ;
							print "<th>" ;
								print _("Roll&nbsp;No");
							print "</th>" ;
							print "<th>" ;
								print _("DOB") ;
							print "</th>" ;
								print "<th>" ;
								print _("Gender") ;
							print "</th>" ;

							/*
							print "<th>" ;
								print "Start Date" ;
							print "</th>" ;
							
							
							print "<th>" ;
								print _("Parents") ;
							print "</th>" ;
							*/
						print "</tr>" ;
					print "</thead>";
					print "<tbody>";
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
							print "<tr class=$rowNum>" ;
								print "<td>" ;
									print $count ;
								print "</td>" ;
								print "<td>" ;
				                	if($row["dateStart"])
				                	{
					                	$enrolldatearr=explode("-",     $row["dateStart"]);
					                	print $enrolldatearr[2].'/'.$enrolldatearr[1].'/'.$enrolldatearr[0] ;
					                }
									//print $row["enrollment_date"] ;
								print "</td>" ;
								print "<td>" ;
									print substr($row["account_number"],5) ;
								print "</td>" ;
								print "<td>" ;
								print $row["admission_number"];
									
								print "</td>" ;
								
								/*
								print "<td>" ;
									print $row["gibbonStudentEnrolmentID"] ;
								print "</td>" ;
								*/
								print "<td>" ;
									print formatName("", $row["preferredName"], $row["surname"], "Student", TRUE) ;
								print "</td>" ;
								print "<td>" ;
									print $row["class"] ;
								print "</td>" ;
								print "<td>" ;
									print SectionFormater($row["section"]) ;
								print "</td>" ;
								print "<td>" ;
									print $row["rollOrder"] ;
								print "</td>" ;
								print "<td>" ;
								$dobarr=explode("-", $row["dob"]);
								print $dobarr[2].'/'.$dobarr[1].'/'.$dobarr[0] ;
								print "</td>" ;
								print "<td>" ;
									print $row["gender"] ;
								print "</td>" ;

								/*
								 * print "<td>" ;
									print dateConvertBack($guid, $row["dateStart"]) ;
								print "</td>" ;
								print "<td>" ;
									try {
										$dataFamily=array("gibbonPersonID"=>$row["gibbonPersonID"]); 
										$sqlFamily="SELECT gibbonFamilyID FROM gibbonfamilychild WHERE gibbonPersonID=:gibbonPersonID" ;
										$resultFamily=$connection2->prepare($sqlFamily);
										$resultFamily->execute($dataFamily);
									}
									catch(PDOException $e) { 
										print "<div class='error'>" . $e->getMessage() . "</div>" ; 
									}
									while ($rowFamily=$resultFamily->fetch()) {
										try {
											$dataFamily2=array("gibbonFamilyID"=>$rowFamily["gibbonFamilyID"]); 
											$sqlFamily2="SELECT gibbonperson.* FROM gibbonperson JOIN gibbonfamilyadult ON (gibbonperson.gibbonPersonID=gibbonfamilyadult.gibbonPersonID) WHERE gibbonFamilyID=:gibbonFamilyID ORDER BY contactPriority, surname, preferredName" ;
											$resultFamily2=$connection2->prepare($sqlFamily2);
											$resultFamily2->execute($dataFamily2);
										}
										catch(PDOException $e) { 
											print "<div class='error'>" . $e->getMessage() . "</div>" ; 
										}
										while ($rowFamily2=$resultFamily2->fetch()) {
											print "<u>" . formatName($rowFamily2["title"], $rowFamily2["preferredName"], $rowFamily2["surname"], "Parent") . "</u><br/>" ;
											$numbers=0 ;
											for ($i=1; $i<5; $i++) {
												if ($rowFamily2["phone" . $i]!="") {
													if ($rowFamily2["phone" . $i . "Type"]!="") {
														print "<i>" . $rowFamily2["phone" . $i . "Type"] . ":</i> " ;
													}
													if ($rowFamily2["phone" . $i . "CountryCode"]!="") {
														print "+" . $rowFamily2["phone" . $i . "CountryCode"] . " " ;
													}
													print $rowFamily2["phone" . $i] . "<br/>" ;
													$numbers++ ;
												}
											}
											if ($rowFamily2["citizenship1"]!="" OR $rowFamily2["citizenship1Passport"]!="") {
												print "<i>Passport</i>: " . $rowFamily2["citizenship1"] . " " . $rowFamily2["citizenship1Passport"] . "<br/>" ;
											}
											if ($rowFamily2["nationalIDCardNumber"]!="") {
												if ($_SESSION[$guid]["country"]=="") {
													print "<i>National ID Card</i>: " ;
												}
												else {
													print "<i>" . $_SESSION[$guid]["country"] . " ID Card</i>: " ;
												}
												print $rowFamily2["nationalIDCardNumber"] . "<br/>" ;
											}
										}
									}
								print "</td>" ;
								*/
							print "</tr>" ;
						}
						print "<tbody>";
					print "</table>" ; 		
			}
			else {
				print "<div class='warning'>" ;
					print _("There are no records to display.") ;
				print "</div>" ;
			}
		}
	}
}
?>
</div>
<input type="hidden" name="print_page_url" id="print_page_url" value="<?php echo $_SESSION[$guid]["absoluteURL"] . "/modules/Students/report_student_new_print.php";?>">
<input type="hidden" name="rollgroup_url" id="rollgroup_url" value="<?php echo $_SESSION[$guid]["absoluteURL"] . "/modules/Students/ajax_change_rollgroup.php";?>">
<input type="hidden" name="search_type" id="search_type" value="<?php echo $search_type;?>">

<script>
function printDiv() 
{
  var divToPrint=document.getElementById('printArea');

  var newWin=window.open('','Print-Window');

  newWin.document.open();
	var style="<style> tbody,td,th{border:1px solid; font-size:12px;} thead {display: table-header-group;} thead td{ border: 0px;} tr{page-break-inside: avoid;} </style>";
  newWin.document.write('<html><head>'+style+'</head><body onload="window.print()">'+divToPrint.innerHTML+'</body></html>');
  
  newWin.window.print();

  //newWin.document.close();

  //setTimeout(function(){newWin.close();},10);

}
$("#select_class,#select_year").change(function(){
	//alert("Hululu");
	var yearGroup=$("#select_class").val();
	var schoolYear=$("#select_year").val();
	var url=$("#rollgroup_url").val();
	$.ajax({
		type: "POST",
		url: url,
		data: {yearGroup: yearGroup, schoolYear: schoolYear},
		success: function(msg)
		{
			console.log(msg);
			$("#select_section").empty().append("<option value =''>Select Section</option>" + msg);
		}
	});
});
</script>
<?php
};
?>
