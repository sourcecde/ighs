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
	<h5>Manage Activity Progress/ Achievement :</h5>
	<table width='60%'>
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
			<input type='submit' name='sectionWise' value='Submit'  style='float: right; margin:0 5px;'>
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
				$sql="SELECT `preferredName`,`account_number`,`gibbonstudentenrolment`.`rollOrder`,`gibbonstudentenrolment`.`gibbonPersonID` 
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
		<div style="width:25%; float:left">
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
		<div style="width:75%; float:left; display: none;" id='viewPanel'>
			<ul>
				<li><a href="#tabP">Progress</a></li>
				<li><a href="#tabA">Achievement</a></li>
			</ul>
			<div id="tabP" class='collapse'>
				
			</div>
			<div id="tabA" class='collapse'>
			</div>
		</div>
		<?php
		}
	}
}
?>
<div id='hide_body'style='background-color :rgba(0,0,0, 0.7); width:100%; height:100%; position:fixed; left:0px; top:0px; z-index:100; display:none;'>
</div>
<div  id='modal_edit_master' class='modal' style="position:fixed; left:500px; top:250px; z-index:200; border:1px; padding:10px ; background-color :rgba(0,0,0, 0.6); color:white; width:300px; display:none;">
	<table style='width: 100%'>
			<input type='hidden' id='editMasterID'>
				<!--
				<tr>
					<td><b>Activity :</b></td>
					<td>
						<select id='editMasterActivityID'>
						<?php
							foreach($activities as $a){
								echo "<option value='{$a['activityID']}'>{$a['activityName']}</option>";
							}
						?>
						</select>
					</td>
				</tr>
				-->
				<tr>
					<td><b>Start Date :</b></td>
					<td><input type='text' id='editMasterStartDate' class='dateP'></td>
				</tr>
				<tr>
					<td><b>Entry time Level :</b></td>
					<td><input type='text' id='editMasterEntryLevel'></td>
				</tr>
				<tr>
					<td><b>Remarks :</b></td>
					<td><textarea id='editMasterRemarks' rows="3"></textarea></td>
				</tr>
				<tr>
					<td style='text-align: center'><button id='editActivityMaster'  class='cButton'>Update</button></td>
					<td style='text-align: center'><button   class='cButton closeModal'>Close</button></td>
				</tr>
			</table>
</div>
<div  id='modal_edit_progress' class='modal' style="position:fixed; left:500px; top:250px; z-index:200; border:1px; padding:10px ; background-color :rgba(0,0,0, 0.6); color:white; width:300px; display:none;">
			<table style='width: 100%'>
			<input type='hidden' id='editProgressID'>
				<tr>
					<td><b>Date :</b></td>
					<td><input type='text' id='editProgressDate' class='dateP'></td>
				</tr>
				<tr>
					<td><b>Progress :</b></td>
					<td><textarea id='editProgressProgress' rows="3"></textarea></td>
				</tr>
				<tr>
					<td style='text-align: center'><button id='editProgress'  class='cButton'>Update</button></td>
					<td style='text-align: center'><button   class='cButton closeModal'>Close</button></td>
				</tr>
			</table>
</div>
<div  id='modal_edit_achievement' class='modal' style="position:fixed; left:500px; top:250px; z-index:200; border:1px; padding:10px ; background-color :rgba(0,0,0, 0.6); color:white; width:300px; display:none;">
	<table style='width: 100%'>
			<input type='hidden' id='editAchievementID'>
				<tr>
					<td><b>Date :</b></td>
					<td><input type='text' id='editAchievementDate' class='dateP'></td>
				</tr>
				<tr>
					<td><b>Competition :</b></td>
					<td><input type='text' id='editAchievementName'></td>
				</tr>
				<tr>
					<td><b>Activity :</b></td>
					<td>
						<select id='editAchievementActivityID'>
							<option value='0'>Select</option>
						<?php
							foreach($activities as $a){
								echo "<option value='{$a['activityID']}'>{$a['activityName']}</option>";
							}
						?>
						</select>
					</td>
				</tr>
				<tr>
					<td><b>Achievement :</b></td>
					<td><textarea id='editAchievementRemarks' rows="3"></textarea></td>
				</tr>
				<tr>
					<td style='text-align: center'><button id='editAchievement'  class='cButton'>Update</button></td>
					<td style='text-align: center'><button   class='cButton closeModal'>Close</button></td>
				</tr>
			</table>
</div>
<input type='hidden' id='basicURL' value='<?=$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajax/basic.php"?>'>
<input type='hidden' id='processURL' value='<?=$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajax/manageProgress.php"?>'>
<script>
$(document).ready(function(){
	var basicURL=$('#basicURL').val();
	var processURL=$('#processURL').val();
	$( ".dateP" ).datepicker({ dateFormat: 'dd/mm/yy' });
	$('#viewPanel').tabs();
	$( ".collapse" ).accordion({
       heightStyle: "content",
	   collapsible: true
    });
	
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
		//$('#viewPanel').fadeOut();
		var idarr=$(this).prop('id').split('_');
		var id=idarr[1];
		console.log(id);
		$.ajax({
			type: "POST",
			url: processURL,
			data: { action: 'fetchProgressAchievement',personID:id},
			success: function(msg){ 
				//console.log(msg);
				$( ".collapse" ).accordion("destroy");
				//var data=msg.split('_break_');
				var data=jQuery.parseJSON(msg);
				$('#tabP').html(data['Progress']);
				$('#tabA').html(data['Achievement']);
				$( ".collapse" ).accordion({
				   heightStyle: "content",
				   collapsible: true
				});
				$('#viewPanel').show();
			}
		});
	});
	$('body').on('click', '.closeModal', function (){
		$('.modal').hide();
		$('#hide_body').fadeOut();
	});
	$('body').on('click', '.editEnrolment', function (){
        $('#hide_body').show();
		var idArrr=$(this).attr('id').split('_');
		var id=idArrr[1];
		$.ajax({
			type: "POST",
			url: processURL,
			data: { action: 'fetchMasterData',masterID:id},
			success: function(msg){ 
				var data=jQuery.parseJSON(msg);
				var newDate=data.startDate.split('-');
				$('#editMasterStartDate').val(newDate[2]+"/"+newDate[1]+"/"+newDate[0]);
				$('#editMasterEntryLevel').val(data.entryLevel);
				$('#editMasterRemarks').val(data.remark);
				$('#editMasterID').val(id);
				//$('#editMasterActivityID option[value="'+data.activityID+'"]').attr("selected","selected");
				$('#modal_edit_master').show();
			}
		});
    });
	$('body').on('click', '.editProgress', function (){
        $('#hide_body').show();
		var idArrr=$(this).attr('id').split('_');
		var id=idArrr[1];
		$.ajax({
			type: "POST",
			url: processURL,
			data: { action: 'fetchProgressData',progressID:id},
			success: function(msg){
				console.log(msg);
				var data=jQuery.parseJSON(msg);
				var newDate=data.date.split('-');
				$('#editProgressDate').val(newDate[2]+"/"+newDate[1]+"/"+newDate[0]);
				$('#editProgressProgress').val(data.progress);
				$('#editProgressID').val(id);
				$('#modal_edit_progress').show();
			}
		});
    });
	$('body').on('click', '.editAchievement', function (){
        $('#hide_body').show();
		var idArrr=$(this).attr('id').split('_');
		var id=idArrr[1];
		$.ajax({
			type: "POST",
			url: processURL,
			data: { action: 'fetchAchievementData',achievementID:id},
			success: function(msg){
				console.log(msg);
				var data=jQuery.parseJSON(msg);
				var newDate=data.date.split('-');
				$('#editAchievementDate').val(newDate[2]+"/"+newDate[1]+"/"+newDate[0]);
				$('#editAchievementName').val(data.name);
				$('#editAchievementActivityID option[value="'+data.activityID+'"]').attr("selected","selected");
				$('#editAchievementRemarks').val(data.remarks);
				$('#editAchievementID').val(id);
				$('#modal_edit_achievement').show();
			}
		});
    });
	$('#editActivityMaster').click(function(){
		$('#modal_edit_master').hide();
		var data={};
		data['activityMasterID']=$('#editMasterID').val();
		data['startDate']=$('#editMasterStartDate').val();
		data['entryLevel']=$('#editMasterEntryLevel').val();
		data['remark']=$('#editMasterRemarks').val();
		//data['activityID']=$('#editMasterActivityID').val();
		$.ajax({
			type: "POST",
			url: processURL,
			data: { action: 'updateMasterData',data:data},
			success: function(msg){ 
				alert(msg);
				$('#hide_body').hide();
			}
		});
	});
	$('#editProgress').click(function(){
		$('#modal_edit_progress').hide();
		var data={};
		data['progressID']=$('#editProgressID').val();
		data['date']=$('#editProgressDate').val();
		data['progress']=$('#editProgressProgress').val();
		//data['activityID']=$('#editMasterActivityID').val();
		$.ajax({
			type: "POST",
			url: processURL,
			data: { action: 'updateProgressData',data:data},
			success: function(msg){ 
				alert(msg);
				$('#hide_body').hide();
			}
		});
	});
	$('#editAchievement').click(function(){
		$('#modal_edit_achievement').hide();
		var data={};
		data['achievementID']=$('#editAchievementID').val();
		data['date']=$('#editAchievementDate').val();
		data['name']=$('#editAchievementName').val();
		data['activityID']=$('#editAchievementActivityID').val();
		data['remarks']=$('#editAchievementRemarks').val();
		console.log(data);
		$.ajax({
			type: "POST",
			url: processURL,
			data: { action: 'updateAchievementData',data:data},
			success: function(msg){ 
				alert(msg);
				$('#hide_body').hide();
			}
		});
	});
	
});
</script>