<?php 
@session_start() ;

//Set timezone from session variable
date_default_timezone_set($_SESSION[$guid]["timezone"]);

if (isActionAccessible($guid, $connection2, "/modules/Staff/staff_view_details.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
	echo "<h3>Staff Daily Attendance: </h3>";
?>
	<form  action='<?php print $_SESSION[$guid]["absoluteURL"]?>/index.php'>
	<input type="hidden" name="q" value="/modules/<?php print $_SESSION[$guid]["module"] ?>/staff_attendance.php">
	<table width="40%" cellpadding="0" cellspacing="0">
	<tr>
		<td>
			<input type='text' id='attendance_date' name='attendance_date' style="float:left; text-align:center;" value='<?=!isset($_REQUEST['attendance_date'])?date('d/m/Y'):$_REQUEST['attendance_date'];?>' required>
		
			<input type='submit' name="btn_attendance_day"  id="btn_attendance_day"  style="float:right;"  value='Select'>
		</td>
	</tr>
	</table>
	</form>
<?php
	if(isset($_REQUEST['btn_attendance_day'])){
		$sql="SELECT `attendanceLogID` FROM `lakshyastaffattendancelog` WHERE `date`='{$_REQUEST['attendance_date']}'";
		$result=$connection2->prepare($sql);
		$result->execute();
		if($result->rowCount()>0){
			echo "<h2>Attendance is already taken on this Date.</h2>";
		}
		else{
			$t_date=$_REQUEST['attendance_date'];
			$t_date_a=explode('/',$t_date);
			$n_date=$t_date_a[2]."-".$t_date_a[1]."-".$t_date_a[0];
			try{
			$sql="SELECT `gibbonStaffID`,gibbonstaff.jobTitle,gibbonperson.preferredName FROM `gibbonstaff`
			LEFT JOIN gibbonperson on gibbonperson.gibbonPersonID=gibbonstaff.gibbonPersonID WHERE (gibbonperson.dateEnd IS NULL OR gibbonperson.dateEnd>'$n_date') AND gibbonperson.dateStart <='$n_date'  ORDER BY gibbonstaff.priority";
			$result1=$connection2->prepare($sql);
			$result1->execute();
			$staffs=$result1->fetchAll();
			}
			catch(PDOException $e){
				echo $e;
			}
			//print_r($staffs);
			try{
			$sql1="SELECT * FROM `lakshyastaffattendancerule`";
			$result1=$connection2->prepare($sql1);
			$result1->execute();
			$rule=$result1->fetchAll();
			}
			catch(PDOException $e){
				echo $e;
			}
			try{
			$sql2="SELECT `staff_id`,`short_name`, SUM(`value`) as t_value FROM `lakshyastaffleavecredit` LEFT JOIN `lakshyastaffattendancerule` ON `lakshyastaffattendancerule`.`rule_id`=`lakshyastaffleavecredit`.`rule_id` WHERE 1 GROUP BY staff_id,`lakshyastaffleavecredit`.rule_id";
			$result2=$connection2->prepare($sql2);
			$result2->execute();
			$c_leave=$result2->fetchAll();
			}
			catch(PDOException $e){
				echo $e;
			}
			try{
			$sql3="SELECT `gibbonStaffID`,`type`, COUNT(`type`) AS t_value FROM `lakshyastaffattendancelog` WHERE `type` IN(SELECT `short_name` FROM `lakshyastaffattendancerule`) GROUP BY `gibbonStaffID`,`type`";
			$result3=$connection2->prepare($sql3);
			$result3->execute();
			$d_leave=$result3->fetchAll();
			}
			catch(PDOException $e){
				echo $e;
			}
			$credited_a=array();
			$debited_a=array();
			foreach($c_leave as $l){
				$credited_a[$l['staff_id']][$l['short_name']]=$l['t_value'];
			}
			foreach($d_leave as $l){
				$debited_a[$l['gibbonStaffID']][$l['type']]=$l['t_value'];
			}
			echo "<form method='POST' action='{$_SESSION[$guid]["absoluteURL"]}/modules/{$_SESSION[$guid]["module"]}/staff_attendance_process.php' id='form_staff_attnd'>";
			echo "<input type='hidden' name='date' value='{$_REQUEST['attendance_date']}'>";
			echo "<table width='100%' cellpadding='0' cellspacing='0'>";
			foreach($staffs as $staff){
				$id=$staff['gibbonStaffID'];
			echo "<tr>";
			echo "<td>{$staff['preferredName']}<br><small>{$staff['jobTitle']}</small></td>";
			echo "<td>";
				/* Place for showing leave */
				echo "<b>Available: </b>";
					foreach($rule as $r){
						$c=0;
						if(array_key_exists($staff['gibbonStaffID']+0,$credited_a))
						if(array_key_exists($r['short_name'],$credited_a[$staff['gibbonStaffID']+0]))
							$c=$credited_a[$staff['gibbonStaffID']+0][$r['short_name']];
						$d=0;
						if(array_key_exists($staff['gibbonStaffID']+0,$debited_a))
						if(array_key_exists($r['short_name'],$debited_a[$staff['gibbonStaffID']+0]))
							$d=$debited_a[$staff['gibbonStaffID']+0][$r['short_name']];	
						echo " ".$r['short_name'].": ".($c-$d);
					}
				/* Place for showing leave */
				echo "<select name='attd_{$id}' id='attd_{$id}' class='staff_attd_select'><option value='P'>Present </option><option value='A'>Absent </option>";
					foreach ($rule as $r) {
						echo "<option value='{$r['short_name']}'>{$r['caption']}</option>";
				
					}
					echo "</select></td>";
			echo "<td><input type='text' name='reason_{$id}' id='reason_{$id}' placeholder=' Reason'></td>";
			echo "</tr>";
			}
			echo"<tr><td colspan='3'><center><input type='submit' name='sub_attendance' id='sub_attendance' value='SAVE'></center></td></tr>";
			echo "</table>";
			echo "</form>";
		
		}	
	}
}
?>
<script type="text/javascript">
	$(function() {
		$( "#attendance_date" ).datepicker({ dateFormat: 'dd/mm/yy' });
	});
</script>