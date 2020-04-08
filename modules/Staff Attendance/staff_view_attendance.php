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
	$month_array=array('January','February','March','April','May','June','July','August','September','October','November','December');
	if(!isset($_REQUEST['v_attd_month']) && !isset($_REQUEST['v_attd_year']) ){
		$month=date('m');
		$year=date('Y');
	}
	else{
		$month=$_REQUEST['v_attd_month'];
		$year=$_REQUEST['v_attd_year'];
		if(strlen($month)<2)
			$month='0'.$month;
	}
	
?>
<h3>Month Wise Staff Attendance Details:</h3>
	<table width="80%" cellpadding="0" cellspacing="0">
	<tr>
		<form  action='<?php print $_SESSION[$guid]["absoluteURL"]?>/index.php'>
		<input type="hidden" name="q" value="/modules/<?php print $_SESSION[$guid]["module"] ?>/staff_view_attendance.php">
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
		<td>
			<a href='<?php print $_SESSION[$guid]["absoluteURL"]?>/modules/<?php print $_SESSION[$guid]["module"] ?>/print_attendance_log.php?p_month=<?=$month?>&p_year=<?=$year?>' target="_blank"><input type='button'   style="background-color: #ff731b; color: #ffffff; float:right;"  value='Print'></a>
		</td>
	</tr>
	</table>
<?php
$condition= "/$month/$year";
$sql="SELECT `date`,`type` FROM `lakshyastaffattendancelog` WHERE `date` like '__{$condition}'";
$result=$connection2->prepare($sql);
$result->execute();
$data=$result->fetchAll();
$sql="SELECT DISTINCT `date` FROM `lakshyastaffattendancelog` WHERE `date` like '__{$condition}'";
$result=$connection2->prepare($sql);
$result->execute();
$date_data=$result->fetchAll();
$count_array=array();
foreach($date_data as $d){
	$count_array[(substr($d['date'],0,2)+0)]['P']=0;
	$count_array[(substr($d['date'],0,2)+0)]['A']=0;
}
foreach($data as $d){
	if($d['type']=='P')
		$count_array[(substr($d['date'],0,2)+0)]['P']++;
	else if($d['type']!='')
		$count_array[(substr($d['date'],0,2)+0)]['A']++;
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
			if(array_key_exists($i,$count_array))
				echo "<a href='{$linkurl}&date={$i}{$condition}'><h2>$i</h2><b>Present: {$count_array[$i]['P']}<br/>Absent: {$count_array[$i]['A']}</a>";
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
?>