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
		try{
			$sql="SELECT `gibbonStaffID`,gibbonperson.preferredName FROM `gibbonstaff`
			LEFT JOIN gibbonperson on gibbonperson.gibbonPersonID=gibbonstaff.gibbonPersonID WHERE gibbonperson.dateEnd IS NULL ORDER BY gibbonstaff.priority";
			$result1=$connection2->prepare($sql);
			$result1->execute();
			$staffs=$result1->fetchAll();
			}
			catch(PDOException $e){
				echo $e;
			}
	$month_array=array('January','February','March','April','May','June','July','August','September','October','November','December');
	if(!isset($_REQUEST['btn_attendance_day']) ){
		$month='';
		$year='';
		$staffID='';
	}
	else{
		$month=$_REQUEST['v_attd_month'];
		$year=$_REQUEST['v_attd_year'];
		$staffID=$_REQUEST['v_attd_staff'];
		if(strlen($month)<2)
			$month='0'.$month;
	}
	
?>
<h3>Month Wise Individual Staff Attendance Details:</h3>
	<table width="80%" cellpadding="0" cellspacing="0">
	<tr>
		<form  action='<?php print $_SESSION[$guid]["absoluteURL"]?>/index.php'>
		<input type="hidden" name="q" value="/modules/<?php print $_SESSION[$guid]["module"] ?>/staff_view_attendance_individual.php">
		<td>
			<select name='v_attd_staff' id='v_attd_staff' required>
				<option value=''> Select Staff</option>
				<?php foreach($staffs as $st){ $id=$st['gibbonStaffID']; $s=$staffID==$id?'selected':''; echo "<option value='$id' $s>{$st['preferredName']}</option>"; } ?>
			</select>
		</td>
		<td>
			<select name='v_attd_month' id='v_attd_month' required>
				<option value=''> Select Month</option>
				<?php $i=01;  foreach($month_array as $m){ $s=$month==$i?'selected':''; echo "<option value='".$i++."' $s>$m</option>"; } ?>
			</select>
		</td>
		<td>
			<select name='v_attd_year' id='v_attd_year' required>
				<option value=''> Select Year</option>
				<?php for($y=2015;$y<2051;$y++){ $s=$year==$y?'selected':''; echo "<option $s>$y</option>"; } ?>
			</select>
		</td>
		<td>
			<input type='submit' name="btn_attendance_day"  id="btn_attendance_day"  style="float:right;"  value='Select'>
		</td>
		</form>
	</tr>
	</table>
<?php
if(isset($_REQUEST['btn_attendance_day'])) {
$condition= "/$month/$year";
$sql="SELECT `date`,`type`,`comment`,`timeStamp`,gibbonperson.preferredName FROM `lakshyastaffattendancelog`
	    LEFT JOIN gibbonperson on gibbonperson.gibbonPersonID=lakshyastaffattendancelog.StaffIDTaker
		WHERE `date` like '__{$condition}' AND gibbonStaffID=$staffID";
$result=$connection2->prepare($sql);
$result->execute();
$data=$result->fetchAll();
$attd_data=array();
foreach($data as $d){
	$dt=substr($d['date'],0,2)+0;
	$time=date('d/m/Y h:i',$d['timeStamp']);
	$attd_data[$dt]=array($d['type'],$d['comment'],$d['preferredName'],$time);
}

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

$linkurl="{$_SESSION[$guid]["absoluteURL"]}/index.php?q=/modules/{$_SESSION[$guid]["module"]}/staff_view_attendance_daywise.php";
		
$firstDay= date('D', strtotime("01-$month-$year"));
$dateGap=findGap($firstDay);
$dayCount=0;
$numOfDays=cal_days_in_month(CAL_GREGORIAN,$month,$year);

 ?>
	<table class='mini' cellspacing='0' style='width: 700px; ' class='tableCalendar'>
	<tr class='head'>
	<th style='width: 10%; text-align:center;'>Mon</th>
	<th style='width: 10%; text-align:center;'>Tue</th>
	<th style='width: 10%; text-align:center;'>Wed</th>
	<th style='width: 10%; text-align:center;'>Thu</th>
	<th style='width: 10%; text-align:center;'>Fri</th>
	<th style='width: 10%; text-align:center;'>Sat</th>
	<th style='width: 10%; text-align:center;'>Sun</th>
	</tr>
	<tr style='height: 100px'>
	<?php
		for($i=0;$i<$dateGap;$i++){
			echo "<td style='text-align: center; border: 1px solid; background-color: #fff;' class='box'></td>";
			$dayCount++;
		}
		for($i=1;$i<=$numOfDays;$i++,$dayCount++){
			if($dayCount%7==0 && $dayCount!=0)
				echo "</tr><tr style='height: 100px'>";
			echo "<td style='text-align: center; border: 1px solid;' class='box'>";
			if(array_key_exists($i,$attd_data))
				echo "<div class='box_i'><h2>$i</h2><b>".getTypeName($attd_data[$i][0],$rule_array)."<small style='display:none'>".json_encode($attd_data[$i])."</small></div>";
			else
				echo "<h2 style='color:gray'>$i</h2>Attendance Not taken";
			echo "</td>";
		}
		while($dayCount++%7!=0)
			echo "<td style='text-align: center; border: 1px solid; background-color: #fff;' class='box'></td>";
		echo "</tr>";
	?>
	
	</table>
	<br>
<?php	
}
}
function findGap($firstDay){
	$r=0;
	switch($firstDay){
	case 'Mon':
		$r=0;
		break;
	case 'Tue':
		$r=1;
		break;
	case 'Wed':
		$r=2;
		break;
	case 'Thu':
		$r=3;
		break;
	case 'Fri':
		$r=4;
		break;
	case 'Sat':
		$r=5;
		break;
	case 'Sun':
		$r=7;
		break;	
	}
	return $r;
}
function getTypeName($t,$rule){
	$r='';
	if($t=='P')
		$r="<p style='color:green; text-align:center'>Present</p>";
	else if($t=='A')
		$r="<p style='color:red; text-align:center'>Absent</p>";
	else if($t=='')
		$r="";
	else
		$r="<p style='color:red; text-align:center'>{$rule[$t]}</p>";
	return $r;
}
?>
<div id='hide_body'style='background-color :rgba(0,0,0, 0.7); width:100%; height:100%; position:fixed; left:0px; top:0px; z-index:100; display:none;'>
</div>
<div id="details_panel" class='edit_panel' style="position:fixed; left:500px; top:250px; z-index:200; border:1px; padding:5px 10px; background-color :rgba(0,0,0, 0.6); color:white; width:250px; display:none;">
	<table width='100%'>
		<tr><td colspan='2' style='text-align:center'><b><span id='d_i_t'></span></b></td></tr>
		<tr><td>Reason: </td><td><span id='d_i_r'></span></td></tr>
		<tr><td>Taken By: </td><td><span id='d_i_tb'></span></td></tr>
		<tr><td>Time: </td><td><span id='d_i_ti'></span></td></tr>
		<tr><td colspan='2' style='text-align:center'><input type='button' class='close_panel' value='CLOSE' style="border:1px; padding:5px; background:#ff731b; color:white;"></td></tr>
	</table>
</div>