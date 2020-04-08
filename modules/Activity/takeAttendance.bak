<?php
@session_start();
//if (isActionAccessible($guid, $connection2, "/modules/Activity/takeAttendance.php")==FALSE) {
if (FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
	try{
	$sql1="SELECT * FROM `lakshya_activity_activities`";
	$result1=$connection2->prepare($sql1);
	$result1->execute();
	$activities=$result1->fetchAll();
	}
	catch(PDOException $e) { 
	echo $e;
	}
	try{
	$sql1="SELECT `gibbonRollGroupID`, `name` FROM `gibbonrollgroup` WHERE `gibbonSchoolYearID`=".$_SESSION[$guid]['gibbonSchoolYearID'];
	$result1=$connection2->prepare($sql1);
	$result1->execute();
	$sections=$result1->fetchAll();
	}
	catch(PDOException $e) { 
	echo $e;
	}
	?>
	<form action='' method='POST'>
	<table width='100%'>
	<tr>
		<td>
			<b>Activity :</b>
			<select name='activityID'>
		<?php
			foreach($activities as $a){
				$s=isset($_POST['activityID'])?($a['activityID']==$_POST['activityID']?'selected':''):'';
				echo "<option value='{$a['activityID']}' $s>{$a['activityName']}</option>";
			}
		?>
			</select>
		</td>
		<td>
			<b>Section :</b>
			<select name='sectionID'>
		<?php
			foreach($sections as $s){
				$sl=isset($_POST['sectionID'])?($s['gibbonRollGroupID']==$_POST['sectionID']?'selected':''):'';
				echo "<option value='{$s['gibbonRollGroupID']}' $sl>{$s['name']}</option>";
			}
		?>
			</select>
		</td>
		<td>
			<b>Date :</b>
			<input type='text' name='date' id='date' value='<?=$_POST?$_POST['date']:date('d/m/Y')?>'>
		</td>
		<td>
			<input type='submit' value='Submit'>
		</td>
	</tr>
	</table>
	</form>
	<?php
	if($_POST){
		$date=dateFormatter($_POST['date']);
		try{
		$sql="SELECT `attendanceID`, `enrolmentID`,`type`  FROM `lakshya_activity_attendance` WHERE `date`='$date' AND `activityID`=".$_POST['activityID'];
		$result=$connection2->prepare($sql);
		$result->execute();
		$attendanceD=$result->fetchAll();
		}
		catch(PDOException $e) { 
			echo $e;
		}
		$attendanceData=array();
		foreach($attendanceD as $a){
			$attendanceData[$a['enrolmentID']]=array($a['attendanceID'],$a['type']);
		}
		if(sizeof($attendanceData)>0)
			echo "<h3>Attendance already taken on selected date  for selected activity!!</h3>";
				try{
				$sql1="SELECT `gibbonperson`.`preferredName`,`rollOrder`,`gibbonrollgroup`.`name`,`gibbonstudentenrolment`.`gibbonStudentEnrolmentID` 
						FROM `lakshya_activity_master` 
						LEFT JOIN `gibbonstudentenrolment` ON `lakshya_activity_master`.`personID`=`gibbonstudentenrolment`.`gibbonPersonID` 
						LEFT JOIN `gibbonperson` ON `lakshya_activity_master`.`personID`=`gibbonperson`.`gibbonPersonID` 
						LEFT JOIN `gibbonrollgroup` ON `gibbonstudentenrolment`.`gibbonRollGroupID`=`gibbonrollgroup`.`gibbonRollGroupID`
						WHERE `activityID`={$_POST['activityID']} AND `gibbonstudentenrolment`.`gibbonSchoolYearID`={$_SESSION[$guid]['gibbonSchoolYearID']} AND `lakshya_activity_master`.`endDate` IS NULL 
							AND `gibbonstudentenrolment`.`gibbonRollGroupID`={$_POST['sectionID']} 
						ORDER BY `gibbonstudentenrolment`.`gibbonRollGroupID`, `rollOrder`";
				$result1=$connection2->prepare($sql1);
				$result1->execute();
				$studentData=$result1->fetchAll();
				} 
				catch(PDOException $e) { 
				echo $e;
				}
				
				echo "<form action='".$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/processAttendance.php' method='POST'>";
				$action=sizeof($attendanceData)>0?'updateAttendance':'addAttendance';
				echo "<input type='hidden' name='action' value='$action'>";
				echo "<input type='hidden' name='activityID' value='{$_POST['activityID']}'>";
				echo "<input type='hidden' name='date' value='$date'>";
				echo "<table width='80%'>";
				echo "<tr><th>Name</th><th>Section</th><th>Roll</th><th>Status</th></tr>";
				foreach($studentData as $s){
				echo "<tr><td>{$s['preferredName']}</td><td>{$s['name']}</td><td>{$s['rollOrder']}</td><td>";
				$enrolmentID=$s['gibbonStudentEnrolmentID']+0;
				if(sizeof($attendanceData)>0){
					$type=$attendanceData[$enrolmentID][1]; 
					$s1=$type=='P'?'selected':'';
					$s2=$type=='A'?'selected':'';
					$attendanceID=$attendanceData[$enrolmentID][0];
					echo "<select name='attendance[$attendanceID]'><option value='P' $s1>Present</option><option value='A' $s2>Absent</option></select>";
				}
				else
					echo "<select name='attendance[$enrolmentID]'><option value='P'>Present</option><option value='A'>Absent</option></select>";
				echo "</td></tr>";
				}
				if(sizeof($attendanceData)>0)
					echo "<tr><td colspan='4' style='text-align: center;'><input type='submit' value='Update'></td></tr>";
				else
					echo "<tr><td colspan='4' style='text-align: center;'><input type='submit' value='Submit'></td></tr>";
				echo "</table>";
				echo "</form>";
	}
}
function dateFormatter($d){
	$tmp=explode("/",$d);
	return $tmp[2]."-".$tmp[1]."-".$tmp[0];
}
 ?>
 <script>
 $(function(){
	 $('#date').datepicker({ dateFormat: 'dd/mm/yy' });
 })
 </script>