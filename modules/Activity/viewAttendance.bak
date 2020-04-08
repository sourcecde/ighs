<?php
@session_start();
//if (isActionAccessible($guid, $connection2, "/modules/Activity/viewAttendance.php")==FALSE) {
if (FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
	//Select School Year
	$sqlselect="SELECT `gibbonSchoolYearID`, `name`, `status` FROM `gibbonschoolyear` ORDER BY `gibbonSchoolYearID`";
	$resultselect=$connection2->prepare($sqlselect);
	$resultselect->execute();
	$schoolyear=$resultselect->fetchAll();
	//Select Activity
	$sqlselect="SELECT `activityID`, `activityName` FROM `lakshya_activity_activities` ";
	$resultselect=$connection2->prepare($sqlselect);
	$resultselect->execute();
	$activities=$resultselect->fetchAll();
	//Sectons
	try{
	$sql1="SELECT `gibbonRollGroupID`, `name` FROM `gibbonrollgroup` WHERE `gibbonSchoolYearID`=".$_SESSION[$guid]['gibbonSchoolYearID'];
	$result1=$connection2->prepare($sql1);
	$result1->execute();
	$sections=$result1->fetchAll();
	}
	catch(PDOException $e) { 
	echo $e;
	}
	//Month
	$month_arr=array('01'=>'January','02'=>'February','03'=>'March','04'=>'April','05'=>'May','06'=>'June','07'=>'July','08'=>'August','09'=>'September','10'=>'October','11'=>'November','12'=>'December');
	echo "<form method='POST' action=''>";
	echo "<table width='100%'>";
	echo "<tr>";
		echo "<td>";
			echo "<b>Activity:</b>";
			echo "<select name='activityID'>";
				foreach($activities as $a){
					$s=isset($_POST['activityID'])?($a['activityID']==$_POST['activityID']?'selected':''):'';
					echo "<option value='{$a['activityID']}' $s>{$a['activityName']}</option>";
				}
			echo "</select>";
		echo "</td>";
		echo "<td>";
			echo "<b>Month:</b>";
			echo "<select name='sMonth'>";
				foreach($month_arr as $k=>$m){
					$s=isset($_POST['sMonth'])?($k==$_POST['sMonth']?'selected':''):(date('m')==$k?'selected':'');
					echo "<option value='$k' $s>{$m}</option>";
				}
			echo "</select>";
		echo "</td>";
		echo "<td>";
			echo "<b>Year:</b>";
			echo "<select name='sYear'>";
				for($i=2015;$i<2066;$i++){
					$s=isset($_POST['sYear'])?($i==$_POST['sYear']?'selected':''):(date('Y')==$i?'selected':'');
					echo "<option  $s>$i</option>";
				}
			echo "</select>";
		echo "</td>";
		echo "<td>";
			echo "<b>Section :</b>";
			echo "<select name='sectionID'>";
			foreach($sections as $s){
				$sl=isset($_POST['sectionID'])?($s['gibbonRollGroupID']==$_POST['sectionID']?'selected':''):'';
				echo "<option value='{$s['gibbonRollGroupID']}' $sl>{$s['name']}</option>";
			}
			echo "</select>";
		echo "</td>";
		echo "<td>";
			echo "<input type='submit' value='Submit' name='viewAttendance'>";
			echo isset($_POST['viewAttendance'])?"<span id='printAttendance' style='border: none; background-color: #ff731b; color: #ffffff; text-align: center; padding: 5px; float: right'>Print</span>":"";
		echo "</td>";
	echo "</tr>";
	echo "</table>";
	echo "</form>";
	if(isset($_POST['viewAttendance'])){
		try{
			$sql1="SELECT `gibbonperson`.`preferredName`,`rollOrder`,`gibbonrollgroup`.`name`,`gibbonstudentenrolment`.`gibbonStudentEnrolmentID` 
						FROM `lakshya_activity_master` 
						LEFT JOIN `gibbonstudentenrolment` ON `lakshya_activity_master`.`personID`=`gibbonstudentenrolment`.`gibbonPersonID` 
						LEFT JOIN `gibbonperson` ON `lakshya_activity_master`.`personID`=`gibbonperson`.`gibbonPersonID` 
						LEFT JOIN `gibbonrollgroup` ON `gibbonstudentenrolment`.`gibbonRollGroupID`=`gibbonrollgroup`.`gibbonRollGroupID`
						WHERE `activityID`={$_POST['activityID']} AND `gibbonstudentenrolment`.`gibbonSchoolYearID`={$_SESSION[$guid]['gibbonSchoolYearID']} 
							AND `gibbonstudentenrolment`.`gibbonRollGroupID`={$_POST['sectionID']} 
						ORDER BY `gibbonstudentenrolment`.`gibbonRollGroupID`, `rollOrder`";
			$result1=$connection2->prepare($sql1);
			$result1->execute();
			$studentData=$result1->fetchAll();
		} 
		catch(PDOException $e) { 
			echo $e;
		}
		$date=$_POST['sYear']."-".$_POST['sMonth']."-__";
		try{
			$sql1="SELECT `enrolmentID`,`type`, DATE_FORMAT(`date`,'%e') AS day 
						FROM `lakshya_activity_attendance` 
						LEFT JOIN `gibbonstudentenrolment` ON `lakshya_activity_attendance`.`enrolmentID`=`gibbonstudentenrolment`.`gibbonStudentEnrolmentID` 
						WHERE `date` LIKE '$date' AND `activityID`={$_POST['activityID']} AND `gibbonstudentenrolment`.`gibbonSchoolYearID`={$_SESSION[$guid]['gibbonSchoolYearID']} ";
			$result1=$connection2->prepare($sql1);
			$result1->execute();
			$attD=$result1->fetchAll();
		} 
		catch(PDOException $e) { 
			echo $e;
		}
		$attData=array();
		foreach($attD as $a){
			$attData[$a['enrolmentID']][$a['day']]=$a['type'];
		}
		$nDay=cal_days_in_month(CAL_GREGORIAN,$_POST['sMonth'],$_POST['sYear']);
		echo "<table cellspacing='0' id='aTable' style='width: 100%; display:block;  overflow-x:auto;'>";
		echo "<thead>";
		echo "<tr>";
			echo "<th>Student Name</th>";
			echo "<th><smal>Section</small></th>";
			echo "<th>Roll</th>";
			for($i=1;$i<=$nDay;$i++){
				$dy=$_POST['sYear']."-".$_POST['sMonth']."-";
				$dy.=$i<10?"0".$i:$i;
				$d=date('D',strtotime($dy));
				echo "<th>$i<br><small>$d</small></th>";
			}
			echo "<th><small>Total Prsnt</small></th>";
			echo "<th><small>Total Absnt</small></th>";
		echo "</tr>";
		echo "</thead>";
		echo "<tbody>";
		$tPresent=array();
		$tAbsent=array();
		foreach($studentData as $s){
			$eID=$s['gibbonStudentEnrolmentID']+0;
			if(!array_key_exists($eID,$attData))
				continue;
			echo "<tr>";
			echo "<td>{$s['preferredName']}</td>";
			echo "<td>{$s['name']}</td>";
			echo "<td>{$s['rollOrder']}</td>";
			$tp=0; $ta=0;
			for($i=1;$i<=$nDay;$i++){
				$type=array_key_exists($i,$attData[$eID])?$attData[$eID][$i]:'';
				echo "<td>$type</td>";
				if($type=='P'){
					$tp++;
					if(array_key_exists($i,$tPresent))
						$tPresent[$i]++;
					else
						$tPresent[$i]=1;
				}
				else if($type=='A'){
					$ta++;
					if(array_key_exists($i,$tPresent))
						$tAbsent[$i]++;
					else
						$tAbsent[$i]=1;
				}
			}
			echo "<td>$tp</td>";
			echo "<td>$ta</td>";
			echo "</tr>";
		}
		echo "</tbody>";
		echo "<tfoot>";
		echo "<tr>";
			echo "<td colspan='3'><b>Total Present :</b></td><td style='display:none'></td><td style='display:none'></td>";
			for($i=1;$i<=$nDay;$i++){
				$t=array_key_exists($i,$tPresent)?$tPresent[$i]:'';
				echo "<th>$t</th>";
			}
			echo "<th></th><th></th>";
		echo "</tr>";
		echo "<tr>";
			echo "<td colspan='3'><b>Total Absent :</b></td><td style='display:none'></td><td style='display:none'></td>";
			for($i=1;$i<=$nDay;$i++){
				$t=array_key_exists($i,$tAbsent)?$tAbsent[$i]:'';
				echo "<th>$t</th>";
			}
			echo "<th></th><th></th>";
		echo "</tr>";
		echo "</tfoot>";
		echo "</table>";
		$printData=array('studentData'=>$studentData,'attData'=>$attData,'nDay'=>$nDay,'dy'=>$dy);
		echo "<form id='print_form' method='POST' target='_blank' action='{$_SESSION[$guid]["absoluteURL"]}/modules/Activity/printAttendance.php'>";
		echo "<input type='hidden' name='printData' value='".json_encode($printData)."'>";
		echo "</form>";
	}
}
 ?>
 
 <script src="<?php echo $_SESSION[$guid]["absoluteURL"] ;?>/modules/Activity/js/jquery.dataTables.min.js"></script>
<script>
	$(document).ready(function(){
		$('#printAttendance').click(function(){
			$('#print_form').submit();
		}); 
		$('#aTable').DataTable({
			"bSort" : false,
			"iDisplayLength":50,
			"oLanguage": {
			  "sLengthMenu": '<select>'+
				'<option value="50">50</option>'+
				'<option value="100">100</option>'+
				'<option value="200">200</option>'+
				'<option value="-1">All</option>'+
				'</select>'
			}
		}); 
	});
 </script>