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
		$sql="SELECT * FROM `lakshya_activity_activities`";
		$result=$connection2->prepare($sql);
		$result->execute();
		$activities=$result->fetchAll();
		}
		catch(PDOException $e){
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
		$activitiesN=array();
		foreach($activities as $a){
			$activitiesN[$a['activityID']]=$a['activityName'];
		}
?>
<h1>Create Timetable :</h1>
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
	<b style='float: left; margin: 5px;'>Select Section:</b>
	<select id='selecetedRollID' style='float: left; margin: 5px;'>
	<?php 
		foreach($sections as $s){
			echo "<option value='{$s['gibbonRollGroupID']}' >{$s['name']}</option>";
		}
	?>
	</select>
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
					echo "<td class='drop' data-row='$i' data-col='$j'></td>";
				}
				echo "</tr>";
			}
		?>
		</table>
	</div>
</div>
<div style='text-align:center'><input type='submit' id='submitTimetable' value='Submit'></div>
<input type='hidden' id='posturl' value='<?php print $_SESSION[$guid]["absoluteURL"]?>/modules/<?php print $_SESSION[$guid]["module"] ?>/ajax/manageTimetable.php'>
<?php
}
 ?>
 <script>
 $(function(){
			var posturl=$('#posturl').val();
			var timetableData={};
			//console.log(timetableData);  
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
								delete timetableData[row][col];
					}
				}
			});
			$('#submitTimetable').click(function(){
				var c=confirm("Are you sure you want to submit?");
				if(c){
					var rollID=$('#selecetedRollID').val();
					$.ajax({
						type: "POST",
						url: posturl,
						data: { action: 'createTimetable', data:timetableData, rollID:rollID},
						success: function(msg)
						{ 
							console.log(msg);
							alert(msg);
							//location.reload();
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