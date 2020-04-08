<?php
@session_start() ;
//if (isActionAccessible($guid, $connection2, "/modules/Activity/manageActivity.php")==FALSE) {
if (False) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else{
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
	<div style="margin-bottom: 10px; padding: 5px 20px;">
		<button class="cButton" style="float: left"id='addActivity'>Add Activity</button>
	</div>
	<br>
	<table style='width: 60%; margin-left: 5%;' class='activityTable'>
	<thead>
		<tr>
			<th>Activity</th>
			<th>Type</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
	<?php
		foreach($activities as $a){
	?>
		<tr>
			<td><p id='aName_<?=$a['activityID']?>'><?=$a['activityName']?></p></td>
			<td><?=$a['isPaid']==1?'PAID':'FREE'?> <input type='hidden' id='aType_<?=$a['activityID']?>' value='<?=$a['isPaid']?>'></td>
			<td><a href='#' class='actvtEdit' id='e_<?=$a['activityID']?>'>Edit</a> | <a href='#' class='actvtDelete' id='d_<?=$a['activityID']?>'>Delete</a></td>
		</tr>
	<?php } ?>
	</tbody>
	</table>
<?php	
}
 ?>
<div id='hide_body'style='background-color :rgba(0,0,0, 0.7); width:100%; height:100%; position:fixed; left:0px; top:0px; z-index:100; display:none;'>
</div>

<div  id='modal_actvt_add' class='modal' style="position:fixed; left:500px; top:250px; z-index:200; border:1px; padding:20px ; background-color :rgba(0,0,0, 0.6); color:white; width:300px; display:none;">
	<b>Activity Name:</b>
	<input type='text' id='actvtAddName' class='modalInput' style='width:180px;'><br><br>
	<b>Type:</b>
	<select id='actvtAddType' style='width:180px;'>
		<option value='1'>Paid</option>
		<option value='0'>Free</option>
	</select><br><br>
	<div style='text-align: center; padding: 20px;'>
		<span class="cButton" id='actvtAddSubmit' style="padding: 10px 20px;">Add</span>
		<span class='s_close cButton' style="padding: 10px 20px;">Close</span>
	</div>
</div>
<div  id='modal_actvt_edit' class='modal' style="position:fixed; left:500px; top:250px; z-index:200; border:1px; padding:20px ; background-color :rgba(0,0,0, 0.6); color:white; width:300px; display:none;">
	<b>Activity Name:</b>
	<input type='text' id='actvtEditName' class='modalInput' style='width:180px;'><br><br>
	<input type='text' id='actvtEditID' class='modalInput' style='display:none'>
	<b>Type:</b>
	<select id='actvtEditType' style='width:180px;'>
		<option value='1'>Paid</option>
		<option value='0'>Free</option>
	</select><br><br>
	<div style='text-align: center; padding: 20px;'>
		<span class="cButton" id='actvtEditSubmit' style="padding: 10px 20px;">Add</span>
		<span class='s_close cButton' style="padding: 10px 20px;">Close</span>
	</div>
</div>
 
<input type='hidden' id='processURL' value='<?=$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajax/manageActivity.php"?>'>
<script src="<?php echo $_SESSION[$guid]["absoluteURL"] ;?>modules/<?=$_SESSION[$guid]["module"];?>/js/jquery.dataTables.min.js"></script>
<script>
	$(document).ready(function(){
		$('.activityTable').DataTable();
		
		var processURL=$('#processURL').val();
		
		$('#addActivity').click(function(){
			$('#hide_body').show();
			$('#modal_actvt_add').show();
		});
		$('body').on('click', '.actvtEdit', function (){
			$('#hide_body').show();
			$('#modal_actvt_edit').show();
			var idArrr=$(this).attr('id').split('_');
			var id=idArrr[1];
			var type=$('#aType_'+id).val();
			console.log(type);
			$('#actvtEditID').val(id);
			$('#actvtEditName').val($('#aName_'+id).text());
			$("#actvtEditType option[value='"+type+"']").attr("selected","selected");
			
		});
		$('body').on('click','.actvtDelete',function(){
			$('#hide_body').show();
			var idArrr=$(this).attr('id').split('_');
			var id=idArrr[1];
			$.ajax
			({
				type: "POST",
				url: processURL,
				data: { action: 'deleteActivity', activityID:id},
				success: function(msg)
				{ 
					console.log(msg);
					alert(msg);
					$('#hide_body').hide();
					location.reload();
				}
			});
			
		});
		$('.s_close').click(function(){
			$('.modal').hide();
			$('#hide_body').hide();
			$('.modalInput').val('');
		});
	
		$('#actvtAddSubmit').click(function(){
			var activityName=$('#actvtAddName').val();
			var isPaid=$('#actvtAddType').val();
			$('#modal_sub_add').hide();
			$.ajax
			({
				type: "POST",
				url: processURL,
				data: { action: 'addActivity', activityName:activityName,isPaid:isPaid},
				success: function(msg)
				{ 
					console.log(msg);
					alert(msg);
					$('#hide_body').hide();
					location.reload();
				}
			});
		});
		$('#actvtEditSubmit').click(function(){
			var activityID=$('#actvtEditID').val();
			var activityName=$('#actvtEditName').val();
			var isPaid=$('#actvtEditType').val();
			$('#modal_sub_edit').hide();
			$.ajax
			({
				type: "POST",
				url: processURL,
				data: { action: 'editActivity', activityName:activityName, isPaid: isPaid, activityID:activityID},
				success: function(msg)
				{ 
					console.log(msg);
					alert(msg);
					$('#hide_body').hide();
					location.reload();
				}
			});
		});
	});
</script>