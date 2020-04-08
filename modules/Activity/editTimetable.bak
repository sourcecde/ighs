<?php
@session_start() ;

if (isActionAccessible($guid, $connection2, "/modules/Staff/staff_view.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
		$timetableID=$_REQUEST['timetableID'];
		try{
		$sql="SELECT * FROM `lakshya_activity_activities`";
		$result=$connection2->prepare($sql);
		$result->execute();
		$activities=$result->fetchAll();
		}
		catch(PDOException $e){
			echo $e;
		}
		$activitiesN=array();
		foreach($activities as $a){
			$activitiesN[$a['activityID']]=$a['activityName'];
		}
		try{
		$sql="SELECT `gibbonrollgroup`.`name` FROM `lakshya_activity_timetable_master` 
				LEFT JOIN `gibbonrollgroup` ON `lakshya_activity_timetable_master`.`gibbonRollGroupID`=`gibbonrollgroup`.`gibbonRollGroupID`  
				WHERE `timetableID`=$timetableID";
		$result=$connection2->prepare($sql);
		$result->execute();
		$section=$result->fetch();
		}
		catch(PDOException $e){
			echo $e;
		}
		try{
		$sql="SELECT `row`,`col`,`activityID` FROM `lakshya_activity_timetable_data` WHERE `timetableID`=$timetableID";
		$result=$connection2->prepare($sql);
		$result->execute();
		$tData=$result->fetchAll();
		}
		catch(PDOException $e){
			echo $e;
		}
		$timetableData=array();
		//$timetableData[0][3]=1;
		foreach($tData as $t){
			$timetableData[$t['row']][$t['col']]=$t['activityID'];
		}
		$td=json_encode($timetableData);
		echo "<input type='hidden' id='timetableData' value='$td'>";
		echo "<input type='hidden' id='timetableID' value='$timetableID'>";
?>
<h1>Edit Timetable :</h1>
<div style='width:15%; border: 1px solid #7030a0; margin:1%; float: left;'>
	<h5 style='text-align: center'>Activities:</h5>
	<div class='left'>
	<table width='100%' id='activitiesTable'>
	<?php 
		foreach($activities as $a){
			echo "<tr><td><div class='item ui-widget-content' data-activityID='{$a['activityID']}'>{$a['activityName']}</div></td></tr>";
		}
	?>
		<tr><td><div class="item ui-widget-content trash" id='erase'>Delete</div></td></tr>
	</table>
	</div>
</div>
<div style='width:80%; border: 1px solid red; margin:1%; float: right;'>
	<b style='float: left; margin: 5px;'>Section: <?=$section['name']?></b>
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
					echo "<td class='drop' data-row='$i' data-col='$j'>";
						if(array_key_exists($i,$timetableData))
							if(array_key_exists($j,$timetableData[$i]))
								echo "<div class='item ui-widget-content' data-activityID='{$timetableData[$i][$j]}'>{$activitiesN[$timetableData[$i][$j]]}</div>";
					echo "</td>";
				}
				echo "</tr>";
			}
		?>
		</table>
	</div>
</div>
<div style='text-align:center'><input type='submit' id='updateTimetable' value='Update'></div><br>
<div style='text-align:center'><a href="<?php print $_SESSION[$guid]["absoluteURL"]?>/index.php?q=/modules/<?php print $_SESSION[$guid]["module"] ?>/timetable.php"><span class='cButton' style='padding: 5px'>Back</span></a></div>
<input type='hidden' id='posturl' value='<?php print $_SESSION[$guid]["absoluteURL"]?>/modules/<?php print $_SESSION[$guid]["module"] ?>/ajax/manageTimetable.php'>
<?php
}
 ?>
 <script>
 $(function(){
			var posturl=$('#posturl').val();
			//var timetableData={};
			var timetableData=jQuery.parseJSON($('#timetableData').val());
			console.log(timetableData);  
			$('.left .item').draggable({
					revert:'invalid',
					helper:'clone'
			});
			$('.right td.drop').droppable({
				accept: '.item',
				drop:function(e,ui){
					$(this).find('.item').remove();
					var activityID=ui.draggable.attr('data-activityID');
					var row=$(this).attr('data-row');
					var col=$(this).attr('data-col');
					if(ui.draggable.attr('id')!='erase'){
						  $(ui.draggable).clone().appendTo(this);
						
						if(row in timetableData)
							timetableData[row][col]=activityID;
						else{
							timetableData[row]={};
							timetableData[row][col]=activityID;
						}
						//console.log(timetableData);  
					}
					else{
						if(row in timetableData)
							if(col in timetableData[row])
								timetableData[row][col]=0;
					}
				}
			});
			$('#updateTimetable').click(function(){
				var c=confirm("Are you sure you want to update?");
				if(c){
					var timetableID=$('#timetableID').val();
					$.ajax({
						type: "POST",
						url: posturl,
						data: { action: 'updateTimetable', data:timetableData, timetableID:timetableID},
						success: function(msg)
						{ 
							console.log(msg);
							alert(msg);
							location.reload();
						}
					});
				}
			});
 });
 
</script>
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
 .assigned{
	border:1px solid #BC2A4D;
}
.trash{
	background: #ff731b;
}
 </style>