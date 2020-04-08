<?php

@session_start() ;

//Module includes
include "./modules/" . $_SESSION[$guid]["module"] . "/moduleFunctions.php" ;

/*if (isActionAccessible($guid, $connection2, "/modules/Students/report_student_dataUpdaterHistory.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {*/
	//Proceed!
	print "<div class='trail'>" ;
	print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . _("Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . _(getModuleName($_GET["q"])) . "</a> > </div><div class='trailEnd'>" . _('Student Admit Cards') . "</div>" ;
	print "</div>" ;
	print "<p>" ;
	print _("This report allows a user to select a range of students and create admit cards for those students.") ;
	print "</p>" ;
	
	print "<h2>" ;
	print "Print Admit" ;
	print "</h2>" ;
	
$sql1="SELECT `gibbonYearGroupID`, `name` FROM `gibbonyeargroup` ORDER BY `sequenceNumber`" ;
$result1=$connection2->prepare($sql1);
$result1->execute();	
$class=$result1->fetchall();

$sql1="SELECT `gibbonRollGroupID`, `name` FROM `gibbonrollgroup` WHERE `gibbonYearGroupID`=1 AND `gibbonSchoolYearID`=".$_SESSION[$guid]["gibbonSchoolYearID"] ;
$result1=$connection2->prepare($sql1);
$result1->execute();	
$section=$result1->fetchall();

$sql2="SELECT `gibbonSchoolYearID`,`name` FROM `gibbonschoolyear`  ORDER BY status" ;
$result2=$connection2->prepare($sql2);
$result2->execute();	
$year=$result2->fetchall();	

$sql3="SELECT gibbonperson.preferredName,gibbonstudentenrolment.gibbonPersonID from gibbonstudentenrolment LEFT JOIN gibbonperson ON gibbonperson.gibbonPersonID=gibbonstudentenrolment.gibbonPersonID ORDER BY gibbonstudentenrolment.gibbonYearGroupID,gibbonperson.preferredName" ;
$result3=$connection2->prepare($sql3);
$result3->execute();	
$student=$result3->fetchall();
//print_r($student);

	?>
<form action="modules/Students/generate_admit_card.php" method='post' target="_blank">
<table width='80%'>
	<tr>
		<td>Select Year: <select name='year' id='year'>
									<?php foreach($year as $a) {?>
									<option value='<?php echo $a['gibbonSchoolYearID'];?>'><?php echo $a['name'];?></option>
									<?php } ?>
							</select></td>
	</tr>
	<tr>
		<td>Select Term: <select name='term'>
			<option value="1">1st Summative</option>
			<option value="2">2nd Summative</option>
			<option value="3">Final Term</option>
			<option value="4">Half-yearly Exam</option>
		    <option value="5">Annual Exam</option>
		    <option value="6">Selection Test</option>
                    <option value="7">3rd Summative </option>
		</select></td>
	</tr>
	<tr>
		<td>Account No: <input type='text' name='account_no'></td>
	</tr>
	<tr>
		<td>Select Student: <select name='person_id' style='height: 104px;' multiple> 
									<option value='' selected> Select</option>
									<?php foreach($student as $a) {?>
									<option value='<?php echo $a['gibbonPersonID'];?>'><?php echo $a['preferredName'];?></option>
									<?php } ?>
							</select></td>
	</tr>
	<tr>
		<td>Class: <select name='class' id='class'>
		    	<option value='' > Select</option>
									<?php foreach($class as $a) {?>
									<option value='<?php echo $a['gibbonYearGroupID'];?>'><?php echo $a['name'];?></option>
									<?php } ?>
							</select></td>
	</tr>
	<tr>
		<td>Section: <select name='section' id='section'>
		    <option value=''> Select</option>
		    	<?php foreach($section as $a) {?>
					<option value='<?php echo $a['gibbonRollGroupID'];?>'><?php echo $a['name'];?></option>
				<?php } ?>
        </select></td>
	</tr>
	<tr>
		<td><center><input type='submit' id='print_admit' value=' Print Admit '></center></td>
	</tr>
</table>
</form>
<input type="hidden" id="ajaxURL" value="https://calcuttapublicschool.in/lakshya/lakshya_green_an/modules/Students/ajax_change_rollgroup.php">
<?php 
//}
?>
<script>
    $(document).ready(function(){
        $("#class").change(function(){
            console.log("HULU");
            var url=$("#ajaxURL").val();
            var schoolYear=$("#year").val();
            var yearGroup=$("#class").val();
            $.ajax({
                type: "POST",
 			    url: url,
 			    data: {schoolYear:schoolYear,yearGroup:yearGroup},
 			success: function(msg)
 			{
 			    console.log(msg);
 				$("#section").empty().append("<option value=''>Select</option>" + msg);
 			}
            });
        });
    });
</script>
