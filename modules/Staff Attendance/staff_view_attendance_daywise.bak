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
if(isset($_REQUEST['date'])){
		$date=$_REQUEST['date'];
		if(strlen($date)<10)
			$date='0'.$date;
	echo "<h3>Staff's  Attendance Day Wise Details: </h3>";
			try{
			$sql1="SELECT * FROM `lakshyastaffattendancerule`";
			$result1=$connection2->prepare($sql1);
			$result1->execute();
			$rule=$result1->fetchAll();
			}
			catch(PDOException $e){
				echo $e;
			}

			foreach ($rule as $r) {
				$rule_array[$r['short_name']]=$r['caption'];
			}

			try{
			$sql="SELECT `gibbonStaffID`,gibbonstaff.jobTitle,gibbonperson.preferredName FROM `gibbonstaff`
			LEFT JOIN gibbonperson on gibbonperson.gibbonPersonID=gibbonstaff.gibbonPersonID WHERE gibbonperson.dateEnd IS NULL ORDER BY gibbonstaff.priority";
			$result1=$connection2->prepare($sql);
			$result1->execute();
			$staffs=$result1->fetchAll();
			}
			catch(PDOException $e){
				echo $e;
			}
			$sql="SELECT  `attendanceLogID`, `gibbonStaffID`, `type`, `comment`,`timeStamp`,gibbonperson.preferredName FROM `lakshyastaffattendancelog`
			 LEFT JOIN gibbonperson on gibbonperson.gibbonPersonID=lakshyastaffattendancelog.StaffIDTaker
			 WHERE `date`='{$date}'";
			$result=$connection2->prepare($sql);
			$result->execute();
			$data=$result->fetchAll();
			
			$attendanceData=array();
			foreach($data as $d){
				$time=date('d/m/Y h:i',$d['timeStamp']);
				$attendanceData[$d['gibbonStaffID']]=array($d['type'],$d['comment'],$d['attendanceLogID'],$d['preferredName'],$time);
			}
			
			echo "<table width='80%' cellpadding='0' cellspacing='0'>";
			echo "<tr><th>Staff</th><th>Type</th><th>Reason</th><th>Taken By</th><th>Edit</th></tr>";
			foreach($staffs as $staff){
				if(!array_key_exists($staff['gibbonStaffID']+0, $attendanceData))
					continue;
			echo "<tr>";
				$t=getTypeName($attendanceData[$staff['gibbonStaffID']+0][0],$rule_array);
				$id=$attendanceData[$staff['gibbonStaffID']+0][2];
			echo "<td><b><span id='name_$id'>{$staff['preferredName']}</span></b><br><small>{$staff['jobTitle']}</small></td>";
			echo "<td id='t_$id'>{$t}<input type='hidden' id='type_{$id}' value='{$attendanceData[$staff['gibbonStaffID']+0][0]}'></td>";
			echo "<td id='r_$id'>{$attendanceData[$staff['gibbonStaffID']+0][1]}</td>";
			echo "<td id='r_$id'>{$attendanceData[$staff['gibbonStaffID']+0][3]}<br><small>{$attendanceData[$staff['gibbonStaffID']+0][4]}</small></td>";
			echo "<td><span id='{$id}' class='edit_attendance'><img title='Edit' src='./themes/Default/img/config.png'/></span></td>";
			echo "</tr>";
			}
			echo "</table>";
}		
}
function getTypeName($t,$rule){
	$r='';
	if($t=='P')
		$r="Present";
	else if($t=='A')
		$r="Absent";
	else if($t=='')
		$r="";
	else
		$r=$rule[$t];
		//$r=$t." Test";
	return $r;
}
?>
<div id='hide_body'style='background-color :rgba(0,0,0, 0.7); width:100%; height:100%; position:fixed; left:0px; top:0px; z-index:100; display:none;'>
 </div>
 <div id='edit_panel_ad'  class='edit_panel' style="position:fixed; left:500px; top:200px; z-index:200; border:1px; padding:5px 10px; background-color :rgba(0,0,0, 0.6); color:white; width:250px; display:none;">
	<table class='blank' style='color:white; width:100%;' cellpadding='5' cellspacing='10'>
		<tr><td style='font-weight:bold; text-align:center;'><span id='name_e'></span></td></tr>
		<!--<tr><td style='text-align:center;'>Available CL: 4 PL: 6 SL: 7</td></tr>-->
		<tr><td><select name='type_e' id='type_e'>
					<option value='P'>Present</option>
					<option value='A'>Absent</option>
			<?php foreach ($rule as $r) {
						echo "<option value='{$r['short_name']}'>{$r['caption']}</option>";
				
					}
			?>
				</select>
			</td></tr>
		<tr><td>Reason: <input type='text' name='reason_e' id='reason_e'></td></tr>
		<tr><td><center>
			<input type='hidden' name='logID' id='logID' value=''>
			<input type='button' id='update_a_d' value='UPDATE' style="border:1px; padding:5px; background:#ff731b; color:white;">
			<input type='button' class='close_panel' value='CLOSE' style="border:1px; padding:5px; background:#ff731b; color:white;">
			</center>
		<tr></td>	
	</table>
 </div>	