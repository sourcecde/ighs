<?php
@session_start() ;

if (isActionAccessible($guid, $connection2, "/modules/Staff/staff_view.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
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
	<table width='100%'>
	<tr>
		<form action='' method="POST">
		<td>
			<b style='float: left'> Select Section :</b>
			<select name='rollGroupID' style='float: left'>
			<?php
			foreach($sections as $s){
				echo "<option value='{$s['gibbonRollGroupID']}'>{$s['name']}</option>";
			}
			?>
			</select>
			<input type='submit' name='viewTimetable' value='View' style='float: right'>
		</td>
		</form>
		<td><a href="<?php print $_SESSION[$guid]["absoluteURL"]?>/index.php?q=/modules/<?php print $_SESSION[$guid]["module"] ?>/createTimetable.php"><span class='cButton' style='padding: 5px; float: right'>Create Timetable</span></a></td>
	</tr>
	</table>
	<?php
	if($_POST){
		$rollGroupID=$_POST['rollGroupID']+0;
		try{
		$sql="SELECT `row`,`col`,`activityName`,`lakshya_activity_timetable_data`.`timetableID` FROM `lakshya_activity_timetable_data` 
				LEFT JOIN `lakshya_activity_activities` ON `lakshya_activity_timetable_data`.`activityID`=`lakshya_activity_activities`.`activityID` 
				LEFT JOIN `lakshya_activity_timetable_master` ON `lakshya_activity_timetable_data`.`timetableID`=`lakshya_activity_timetable_master`.`timetableID` 
				WHERE `lakshya_activity_timetable_master`.`gibbonRollGroupID`=$rollGroupID";
		$result=$connection2->prepare($sql);
		$result->execute();
		$tData=$result->fetchAll();
		}
		catch(PDOException $e){
			echo $e;
		}
		if(sizeOf($tData)>0){
			$timetableData=array();
			foreach($tData as $t){
				$timetableData[$t['row']][$t['col']]=$t['activityName'];
			}
			$timetableID=$tData[0]['timetableID'];
	?>
	
			<a href="<?php print $_SESSION[$guid]["absoluteURL"]?>/index.php?q=/modules/<?php print $_SESSION[$guid]["module"] ?>/editTimetable.php&timetableID=<?=$timetableID?>"><span class='cButton' style='padding: 5px'>Edit</span></a>
	
			<div class='right'>
				<table width='100%'>
						<tr>
							<th></th>
							<th>1st Period</th>
							<th>2nd Period</th>
							<th>3rd Period</th>
							<th>4th Period</th>
							<th>5th Period</th>
							<th>6th Period</th>
							<th>7th Period</th>
							<th>8th Period</th>
						</tr>
				<?php
					$daysName=array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
					for($i=0;$i<7;$i++){
						echo "<tr><th>{$daysName[$i]}</th>";
						for($j=0;$j<8;$j++){
								$f=false;
								if(array_key_exists($i,$timetableData))
									if(array_key_exists($j,$timetableData[$i]))
										$f=!$f;
								if($f)	
									echo "<td class='item'>{$timetableData[$i][$j]}</td>";
								else
									echo "<td></td>";	
						}
						echo "</tr>";
					}
				?>
				</table>
			</div>
	<?php
		}
		else
			echo "<h3>Timetable hasn't created for selected section!!</h3>";
	}
}
 ?>
  
<style>
 #activitiesTable tr td {
	 padding: 0;
 }
 .item, .removable {
	background: #7030a0;
	color: #fff;
	padding: 5px;
	text-align: center;
 }
</style>