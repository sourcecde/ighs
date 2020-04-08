<?php
@session_start() ;
//if (isActionAccessible($guid, $connection2, "/modules/Activity/activityEnrolment.php")==FALSE) {
if (False) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else{
	try{
		$sql="SELECT `gibbonSchoolYearID`, `name`, `status` FROM `gibbonschoolyear`";
		$result=$connection2->prepare($sql);
		$result->execute();
		$schoolYear=$result->fetchAll();
	}
	catch(PDOException $e){
		echo $e;
	}
	try{
		$sql="SELECT `gibbonRollGroupID`,`gibbonrollgroup`.`name` FROM `gibbonrollgroup`
				LEFT JOIN `gibbonschoolyear` ON `gibbonschoolyear`.`gibbonSchoolYearID`=`gibbonrollgroup`.`gibbonSchoolYearID`
				WHERE 1";
		if(isset($_POST['schoolYearID']))
			$sql.=" AND `gibbonrollgroup`.`gibbonSchoolYearID`=".$_POST['schoolYearID'];
		else
			$sql.=" AND `status`='Current'";
		$result=$connection2->prepare($sql);
		$result->execute();
		$sectionsCurrent=$result->fetchAll();
	}
	catch(PDOException $e){
		echo $e;
	}
	try{
	$sql1="SELECT * FROM `lakshya_activity_activities`";
	$result1=$connection2->prepare($sql1);
	$result1->execute();
	$activities=$result1->fetchAll();
	}
	catch(PDOException $e) { 
	echo $e;
	}	
	?>
	<h5>Activity Enrolment :</h5>
	<table width='80%'>
	<tr>
		<td>
			<!--<h5>Section Wise :</h5>-->
			<form action='' method='POST'>
			<select name='schoolYearID' id='schoolYearID' style='float: left;'>
			<?php
			foreach($schoolYear as $y){
				$s=isset($_POST['schoolYearID'])?($_POST['schoolYearID']==$y['gibbonSchoolYearID']?'Selected':''):($y['status']=='Current'?'selected':'');
				echo "<option value='{$y['gibbonSchoolYearID']}' $s> {$y['name']} ({$y['status']})</option>";
			}
			?>
			</select>
			<select name='sectionID' id='sectionID' style='float: left;'>
			<?php
			foreach($sectionsCurrent as $sc){
				echo $s=isset($_POST['sectionID'])?($_POST['sectionID']==$sc['gibbonRollGroupID']?'Selected':''):'';
				echo "<option value='{$sc['gibbonRollGroupID']}' $s > {$sc['name']}</option>";
			}
			?>
			</select>
			<input type='submit' name='sectionWise' value='Submit'  style='float: right;'>
			</form>
		</td>
		<!--
		<td>
			<h5>Student Wise :</h5>
		</td>
		-->
	</tr>
	</table>
	<?php	
	if($_POST){
		extract($_POST);
		if($sectionWise){
			try{
				$sql="SELECT `preferredName`,`account_number`,`gibbonstudentenrolment`.`rollOrder`,`gibbonperson`.`gibbonPersonID` 
						FROM `gibbonperson` 
						LEFT JOIN `gibbonstudentenrolment` ON `gibbonstudentenrolment`.`gibbonPersonID`=`gibbonperson` .`gibbonPersonID` 
						WHERE `gibbonstudentenrolment`.`gibbonSchoolYearID`=$schoolYearID AND `gibbonstudentenrolment`.`gibbonRollGroupID`=$sectionID  
						AND `gibbonperson`.`dateEnd` IS NULL 
						ORDER BY `gibbonstudentenrolment`.`rollOrder`";
				$result=$connection2->prepare($sql);
				$result->execute();
				$studentsData=$result->fetchAll();
			}
			catch(PDOException $e){
				echo $e;
			}
		?>
		<div style="width:30%; float:left">
		<table style='width:100%' >
		<thead>
		<tr>
			<th>Student</th>
		</tr>
		</thead>
		<tbody>
		<?php foreach($studentsData as $s){?>
		<tr>
			<td class='student' id='st_<?=$s['gibbonPersonID']+0?>'><b><?=$s['preferredName']?></b><br><span style='float:left'>Acc No: <i><?=$s['account_number']+0?></i></span><span style='float:right'> Roll: <i><?=$s['rollOrder']?></i></span></td>
		</tr>
		<?php }?>
		</tbody>
		</table>
		</div>
		<div style="width:65%; float:left; display: none;" id='addPanel'>
			<div id='msgView' style="border: 2px solid #7030a0; padding: 2%; display: none; margin-top: 5%; margin-left: 10%;">
				<h5 id='message'></h5>
			</div>
			<table style='margin-top: 20%; margin-left: 10%; width: 80%'>
				<input type='hidden' id='selectedPersonID'>
				<tr>
					<td><b>Activity :</b></td>
					<td>
						<select id='selectedActivityID'>
						<?php
							foreach($activities as $a){
								echo "<option value='{$a['activityID']}'>{$a['activityName']}</option>";
							}
						?>
						</select>
					</td>
				</tr>
				<tr>
					<td><b>Start Date :</b></td>
					<td><input type='text' id='selectedStartDate'></td>
				</tr>
				<tr>
					<td><b>Entry time Level :</b></td>
					<td><input type='text' id='selectedEntryLevel'></td>
				</tr>
				<tr>
					<td><b>Remarks :</b></td>
					<td><textarea id='selectedRemarks' rows="3"></textarea></td>
				</tr>
				<tr>
					<td colspan='2' style='text-align: center'><button id='addActivityEnrolment'  class='cButton'>Enroll</button></td>
				</tr>
			</table>
		</div>
		<?php
		}
	}
}
?>
<input type='hidden' id='basicURL' value='<?=$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajax/basic.php"?>'>
<input type='hidden' id='processURL' value='<?=$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajax/activityEnrolment.php"?>'>
<script>
$(document).ready(function(){
	var basicURL=$('#basicURL').val();
	var processURL=$('#processURL').val();
	$( "#selectedStartDate" ).datepicker({ dateFormat: 'dd/mm/yy' });
	$('#schoolYearID').change(function(){
		var yearID=$(this).val();
		$.ajax({
			type: "POST",
			url: basicURL,
			data: { action: 'sectionIDbyYearID', yearID:yearID},
			success: function(msg){ 
				console.log(msg);
				$('#sectionID').empty().append(msg);
			}
		});
	});
	$('.student').click(function(){
		$('.focused').removeClass('focused');
		$(this).addClass('focused');
		var idarr=$(this).prop('id').split('_');
		var id=idarr[1];
		$('#selectedPersonID').val(id);
		$('#addPanel').show();
	});
	$('#addActivityEnrolment').click(function(){
		var personID=$("#selectedPersonID").val();
		var activityID=$("#selectedActivityID").val();
		var startDate=$("#selectedStartDate").val();
		var entryLevel=$("#selectedEntryLevel").val();
		var remark=$("#selectedRemarks").val();
		if(startDate==''){
			$("#selectedStartDate").focus();
			return;
		}
		$.ajax({
			type: "POST",
			url: processURL,
			data: { action: 'addActivityEnrolment', personID:personID,activityID:activityID,
							startDate:startDate,entryLevel:entryLevel,remark:remark},
			success: function(msg){ 
				console.log(msg);
				$("#message").text(msg);
				$("#msgView").show().delay(1500).fadeOut();;
			}
		});
	});
	
	
});
</script>