<?php 
@session_start() ;

//Module includes
include "./modules/" . $_SESSION[$guid]["module"] . "/moduleFunctions.php" ;

if (isActionAccessible($guid, $connection2, "/modules/Attendance/attendance_take_byRollGroup.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
	//Proceed!
	print "<div class='trail'>" ;
	print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . _("Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . _(getModuleName($_GET["q"])) . "</a> > </div><div class='trailEnd'>" . _('View Monthwise Attendance') . "</div>" ;
	print "</div>" ;
	//Select School Year
	$sqlselect="SELECT `gibbonSchoolYearID`, `name`, `status` FROM `gibbonschoolyear` ORDER BY `gibbonSchoolYearID`";
	$resultselect=$connection2->prepare($sqlselect);
	$resultselect->execute();
	$schoolyear=$resultselect->fetchAll();
	//Select Section
	$sqlselect="SELECT `gibbonRollGroupID`, `gibbonrollgroup`.`name` FROM `gibbonrollgroup` LEFT JOIN `gibbonschoolyear` ON `gibbonschoolyear`.`gibbonSchoolYearID`=`gibbonrollgroup`.`gibbonSchoolYearID` WHERE ";
		if(isset($_REQUEST['gibbonSchoolYearID']))
			$sqlselect.="`gibbonschoolyear`.`gibbonschoolyearID`=".$_REQUEST['gibbonSchoolYearID'];
		else
			$sqlselect.="`gibbonschoolyear`.`status`='Current'";
	$resultselect=$connection2->prepare($sqlselect);
	$resultselect->execute();
	$section=$resultselect->fetchAll();
	//Month
	$month_arr=array('01'=>'January','02'=>'February','03'=>'March','04'=>'April','05'=>'May','06'=>'June','07'=>'July','08'=>'August','09'=>'September','10'=>'October','11'=>'November','12'=>'December');
	$schoolYear="";
	$rollGroup="";
	$monthNo="";
	$yearNo="";
	if(isset($_REQUEST['submit'])){
		$schoolYear=$_REQUEST['gibbonSchoolYearID'];
		$rollGroup=$_REQUEST['gibbonRollGroupID'];
		$monthNo=$_REQUEST['monthNo'];
		$yearNo=$_REQUEST['yearNo'];
	}
?>
	<form method="get" action="<?php print $_SESSION[$guid]["absoluteURL"]?>/index.php">
		<input type="hidden" name="q" value="/modules/<?php print $_SESSION[$guid]["module"] ?>/view_monthwise_attendance.php">
		<table class='smallIntBorder' cellspacing='0' style="width: 100%; border: 1px solid #7030a0;">	
			<tr>
				<td > 
					<b><?php print _('School Year :') ?></b><br/>
					<span style="font-size: 90%"><i></i></span>
				</td>
				<td class="right">
					<select style="width: 200px" name="gibbonSchoolYearID" id="gibbonSchoolYearID">
						<?php foreach($schoolyear as $s){
							if($schoolYear=="")
								$sl=$s['status']=='Current'?"selected":"";
							else
								$sl=$schoolYear==$s['gibbonSchoolYearID']?"selected":"";
							echo "<option value='{$s['gibbonSchoolYearID']}' $sl>{$s['name']}</option>";
						}?>			
					</select>
				</td>
				<td > 
					<b><?php print _('Section :') ?></b><br/>
					<span style="font-size: 90%"><i></i></span>
				</td>
				<td class="right">
					<select style="width: 200px" name="gibbonRollGroupID" id="gibbonRollGroupID">
						<?php foreach($section as $s){
							if($rollGroup!="")
								$sl=$rollGroup==$s['gibbonRollGroupID']?"selected":"";
							echo "<option value='{$s['gibbonRollGroupID']}' $sl>{$s['name']}</option>";
						}?>			
					</select>
				</td>
				<td>
					<input type='submit' Value='GO' name='submit' style='float:right'>
				</td>
			</tr>
			<tr>
				<td > 
					<b><?php print _('Month :') ?></b><br/>
					<span style="font-size: 90%"><i></i></span>
				</td>
				<td class="right">
					<select style="width: 200px" name="monthNo" id="monthNo">
						<?php $m=date('m'); foreach($month_arr as $k=>$v){
							if($monthNo=="")
								$sl=$m==$k?'selected':'';
							else
								$sl=$monthNo==$k?"selected":"";
							echo "<option value='$k' $sl>$v</option>";
						}?>			
					</select>
				</td>
			
				<td > 
					<b><?php print _('Year :') ?></b><br/>
					<span style="font-size: 90%"><i></i></span>
				</td>
				<td class="right">
					<select style="width: 200px" name="yearNo" id="yearNo">
						<?php $y=date('Y'); $a=2015;
						for($i=0;$i<200;$i++){
							$t=$i+$a;
							if($yearNo=="")
								$sl=$y==$t?'selected':'';
							else
								$sl=$yearNo==$t?'selected':'';
							echo "<option value='$t' $sl>$t</option>";
						}?>			
					</select>
				</td>
				<td>
					<?php if(isset($_REQUEST['submit'])){
					echo "<span style='border: 1px solid; background-color: #ff731b;  color: #ffffff;  float:right; padding:5px 13px; cursor: pointer; font-weight:bold;' name='print_button_MONTHWISE_ATTD' id='print_button_MONTHWISE_ATTD'>Print</span>";
					}?>
				</td>
			</tr>	
		</table>
	</form>	
<?php
if(isset($_REQUEST['submit'])){
	$month_yr=$_REQUEST['yearNo']."-".$_REQUEST['monthNo'];
	$sql="SELECT   `gibbonattendancelogperson`.`date`,`gibbonattendancelogperson`.`gibbonPersonID`,`gibbonattendancelogperson`.`type` 
	FROM `gibbonattendancelogperson`
	LEFT JOIN `gibbonstudentenrolment` ON `gibbonstudentenrolment`.`gibbonPersonID`=`gibbonattendancelogperson`.`gibbonPersonID`
	LEFT JOIN `gibbonperson` ON `gibbonperson`.`gibbonPersonID`=`gibbonattendancelogperson`.`gibbonPersonID`
	WHERE `gibbonstudentenrolment`.`gibbonRollGroupID`={$_REQUEST['gibbonRollGroupID']} AND `gibbonattendancelogperson`.`date` like '$month_yr-__'
	";
	$result=$connection2->prepare($sql);
	$result->execute();
	$attndnc=$result->fetchAll();
	$attendanceData=array();
	foreach($attndnc as $a){
		$day=substr($a['date'],8,2)+0;
		$attendanceData[$a['gibbonPersonID']+0][$day]=$a['type'];
	}
	//print_r($attendanceData);
	
	$sql1="SELECT `gibbonperson`.`gibbonPersonID`,`gibbonperson`.`officialName`,`gibbonperson`.`account_number`,`gibbonstudentenrolment`.`rollOrder` 
	 FROM `gibbonperson` 
	 LEFT JOIN `gibbonstudentenrolment` ON `gibbonstudentenrolment`.`gibbonPersonID`=`gibbonperson`.`gibbonPersonID`
	WHERE `gibbonstudentenrolment`.`gibbonRollGroupID`={$_REQUEST['gibbonRollGroupID']} ORDER BY `rollOrder`";
	$result1=$connection2->prepare($sql1);
	$result1->execute();
	$prsn=$result1->fetchAll();
	$personData=array();
	foreach($prsn as $p){
		$personData[$p['gibbonPersonID']+0]=array($p['officialName'],$p['account_number']+0,$p['rollOrder']);
	}
	$sql2="SELECT `date`,`name` FROM `gibbonschoolyearspecialday` WHERE `date` like '$month_yr-__'";
	$result2=$connection2->prepare($sql2);
	$result2->execute();
	$spclD=$result2->fetchAll();
	$specialDay=array();
	foreach($spclD as $s)
	{
		$d=substr($s['date'],8,2);
		$specialDay[$d+0]=verticalText($s['name']);
	}
	$sql3="SELECT `gibbonDaysOfWeekID` as D FROM `gibbondaysofweek` WHERE `schoolDay`='N'";
	$result3=$connection2->prepare($sql3);
	$result3->execute();
	$clsD=$result3->fetchAll();
	$closedDays=array();
	foreach($clsD as $c){
		array_push($closedDays,$c['D']+0);
	}
	//print_r($closedDays);
$numOfDays=cal_days_in_month(CAL_GREGORIAN,$monthNo,$yearNo);
$workingDay=0;
$monthName=$month_arr[$monthNo];
echo "<h3>Month: $monthName<span style='padding:80px;'></span>Year: $yearNo<span style='padding:80px;'></span>WorkingDay: <span id='wDay'></span></h3>";
echo "<table cellspacing='0' class='myTable'>";
echo "<thead>";
echo "<tr>";
	echo "<th style='padding:2px'>Roll</th>";
	echo "<th>Student Name</th>";
	for($i=1;$i<=$numOfDays;$i++){
		echo "<th>$i</th>";
	}
	echo "<th><small>Total Prsnt</small></th>";
	echo "<th><small>Total Absnt</small></th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";
$n=0;
$size=sizeOf($attendanceData)+3;
$totalP=array();
$totalA=array();
foreach($personData as $pID=>$val){
	if(!array_key_exists($pID,$attendanceData))
		continue;
	print "<tr>";
		echo "<td>$val[2]</td>";
		echo "<td style='width:120px'>$val[0]<br><span style='float:right;font-weight:italic;'>Account No: $val[1]</span></td>";
	$tP=0;
	$tA=0;
	$tmpWDay=0;
	$n++;
	for($i=1;$i<=$numOfDays;$i++){
		$iDay=$i<10?$yearNo."-".$monthNo."-0".$i:$yearNo."-".$monthNo."-".$i;
			if(in_array(date('N',strtotime($iDay)),$closedDays)){
				$D=date('l',strtotime($iDay));
				if($n==1)
					echo "<td rowspan='$size' style='width:20px; color:red; font-weight:bold;'><span style='writing-mode:tb-rl;'>{$D}</span>";
				else
					echo "<td style='display:none'>";
				
			}
			else if(array_key_exists($i,$specialDay)){
				if($n==1)
					echo "<td rowspan='$size' style='width:20px; color:red; font-weight:bold;'>{$specialDay[$i]}";
					
				else
					echo "<td style='display:none'>";
			}
			else{	
				if(array_key_exists($i,$attendanceData[$pID])){
					$t=substr($attendanceData[$pID][$i],0,1);
					if($t=='A'){
					echo "<td style='font-weight:bold; color:red;'>";
						$tA++;
						if(array_key_exists($i,$totalA))
							$totalA[$i]++;
						else
							$totalA[$i]=1;
					}
					else{
					echo "<td style='font-weight:bold; color:green;'>";
						$tP++;
						if(array_key_exists($i,$totalP))
							$totalP[$i]++;
						else
							$totalP[$i]=1;
					}
					echo $t;
					$tmpWDay++;
				}
				else{
					echo "<td style='font-weight:bold;'>";
					echo "-";
				}
			}
		echo "</td>";
	}
		echo "<td style='font-weight:bold;'>$tP</td>";
		echo "<td style='font-weight:bold;'>$tA</td>";
	print "</tr>";
	$workingDay=$tmpWDay>$workingDay?$tmpWDay:$workingDay;
}
	print "<tr>";
		print "<td colspan='2'>Total Present</td><td style='display: none'></td>";
	for($i=1;$i<=$numOfDays;$i++){
		$t=array_key_exists($i,$totalP)?$totalP[$i]:'-';
		$iDay=$i<10?$yearNo."-".$monthNo."-0".$i:$yearNo."-".$monthNo."-".$i;
		if(!in_array(date('N',strtotime($iDay)),$closedDays) && !array_key_exists($i,$specialDay))
			print "<td><b>$t</b></td>";
		else
			print "<td style='display: none'></td>";
	}
		print "<td></td><td></td>";
	print "</tr>";
	print "<tr>";
		print "<td colspan='2'>Total Absent</td><td style='display: none'></td>";
	for($i=1;$i<=$numOfDays;$i++){
		$t=array_key_exists($i,$totalA)?$totalA[$i]:'-';
		$iDay=$i<10?$yearNo."-".$monthNo."-0".$i:$yearNo."-".$monthNo."-".$i;
		if(!in_array(date('N',strtotime($iDay)),$closedDays) && !array_key_exists($i,$specialDay))
			print "<td><b>$t</b></td>";
		else
			print "<td style='display: none'></td>";
	}
		print "<td></td><td></td>";
	print "</tr>";
	print "<tr>";
		print "<td colspan='2'>Total Student</td><td style='display: none'></td>";
	for($i=1;$i<=$numOfDays;$i++){
		$p=array_key_exists($i,$totalP)?$totalP[$i]:0;
		$a=array_key_exists($i,$totalA)?$totalA[$i]:0;
		$t=$p+$a==0?'-':$p+$a;
		$iDay=$i<10?$yearNo."-".$monthNo."-0".$i:$yearNo."-".$monthNo."-".$i;
		if(!in_array(date('N',strtotime($iDay)),$closedDays) && !array_key_exists($i,$specialDay))
			print "<td><b>$t</b></td>";
		else
			print "<td style='display: none'></td>";
	}
		print "<td></td><td></td>";
	print "</tr>";
echo "</tbody>";
echo "</table>";
$sqlS="SELECT  `name` FROM `gibbonrollgroup` WHERE `gibbonRollGroupID`=".$rollGroup;
$resultS=$connection2->prepare($sqlS);
$resultS->execute();
$rollGroupName=$resultS->fetch();
$header=array('month'=>$monthName,'monthNo'=>$monthNo,'year'=>$yearNo,'wDay'=>$workingDay,'NODays'=>$numOfDays,'section'=>$rollGroupName['name']);
$attendance_json=json_encode($attendanceData);
$person_json=json_encode($personData);
$data_json=json_encode($header);
$spclDay_json=json_encode($specialDay);
$clsDay_json=json_encode($closedDays);
echo "<form id='print_form' method='POST' target='_blank' action='{$_SESSION[$guid]["absoluteURL"]}/modules/Attendance/print_monthwise_attendance.php'>";
	echo "<input type ='hidden' name='person' value='$person_json'>";
	echo "<input type ='hidden' name='attendance' value='$attendance_json'>";
	echo "<input type ='hidden' name='data' value='$data_json'>";
	echo "<input type ='hidden' name='specialdays' value='$spclDay_json'>";
	echo "<input type ='hidden' name='closedays' value='$clsDay_json'>";
echo "</form>";
}
}
?>
<script src="<?php echo $_SESSION[$guid]["absoluteURL"] ;?>/modules/Attendance/js/jquery.dataTables.min.js"></script>
<script>
	 $(document).ready(function(){
		<?php if(isset($_REQUEST['submit'])){?>
		$('#wDay').text('<?=$workingDay?>');
		<?php }?>
		$('.myTable').DataTable({
			"bSort" : false,
			"scrollX": true,
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