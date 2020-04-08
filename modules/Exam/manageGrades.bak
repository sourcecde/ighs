<?php
@session_start() ;
//if (isActionAccessible($guid, $connection2, "/modules/Exam/manageExam.php")==FALSE) {
if (False) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else{
	try{
	$sql1="SELECT * FROM `lakshya_exam_grade`";
	$result1=$connection2->prepare($sql1);
	$result1->execute();
	$grade=$result1->fetchAll();
	}
	catch(PDOException $e) { 
	echo $e;
	}	
?>
	<div style="margin-bottom: 10px; padding: 5px 20px;">
		<button class="cButton" style="float: left"id='addGrade'>Add Grade</button>
	</div>
	<br>
	<table style='width: 60%; margin-left: 5%;' class='gradeTable'>
	<thead>
		<tr>
			<th>Grades</th>
			<th>Is Fail?</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
	<?php
		foreach($grade as $g){
	?>
		<tr>
			<td><p id='gName_<?=$g['gradeID']?>'><?=$g['grade']?></p></td>
			<td><input type="checkbox" id="f_<?=$g['gradeID']?>" class="isfail" <?php if($g['isfail']){echo 'checked="checked"';} ?>>Fail</td>
			<td><a href='#' class='gradeEdit' id='e_<?=$g['gradeID']?>'>Edit</a> | <a href='#' class='gradeDelete' id='d_<?=$g['gradeID']?>'>Delete</a></td>
		</tr>
	<?php } ?>
	</tbody>
	</table>
<?php	
}
 ?>
<div id='hide_body'style='background-color :rgba(0,0,0, 0.7); width:100%; height:100%; position:fixed; left:0px; top:0px; z-index:100; display:none;'>
</div>

<div  id='modal_sub_add' class='modal' style="position:fixed; left:500px; top:250px; z-index:200; border:1px; padding:20px ; background-color :rgba(0,0,0, 0.6); color:white; width:300px; display:none;">
	<b>New Grade:</b>
	<input type='text' id='gradeAddName' class='modalInput' style='width:180px;'><br><br>
	<b>Is Fail? :</b>
	<input type='checkbox' id='isfail' class='modalInput' style="width:25px;margin-right: 105px;">
	<div style='text-align: center; padding: 20px;'>
		<span class="cButton" id='gradeAddSubmit' style="padding: 10px 20px;">Add</span>
		<span class='s_close cButton' style="padding: 10px 20px;">Close</span>
	</div>
</div>
<div  id='modal_sub_edit' class='modal' style="position:fixed; left:500px; top:250px; z-index:200; border:1px; padding:20px ; background-color :rgba(0,0,0, 0.6); color:white; width:300px; display:none;">
	<b>Edited Grade:</b>
	<input type='hidden' id='gradeEditID' class='modalInput' style='width:180px;'>
	<input type='text' id='gradeEditName' class='modalInput' style='width:180px;'><br><br>
	<div style='text-align: center; padding: 20px;'>
		<span class="cButton" id='gradeEditSubmit' style="padding: 10px 20px;">Update</span>
		<span class='s_close cButton' style="padding: 10px 20px;">Close</span>
	</div>
</div> 
 
<input type='hidden' id='processURL' value='<?=$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajax/manageGrade.php"?>'>
<script src="<?php echo $_SESSION[$guid]["absoluteURL"] ;?>modules/<?=$_SESSION[$guid]["module"];?>/js/jquery.dataTables.min.js"></script>
<script>
	$(document).ready(function(){
		$('.gradeTable').DataTable();
		var processURL=$('#processURL').val();
		
		$('#addGrade').click(function(){
			$('#hide_body').show();
			$('#modal_sub_add').show();
		});
		$('body').on('click', '.gradeEdit', function (){
			$('#hide_body').show();
			$('#modal_sub_edit').show();
			var idArrr=$(this).attr('id').split('_');
			var id=idArrr[1];
			$('#gradeEditID').val(id);
			$('#gradeEditName').val($('#gName_'+id).text());
			
		});
		$('body').on('click', '.gradeDelete', function (){
			$('#hide_body').show();
			var idArrr=$(this).attr('id').split('_');
			var id=idArrr[1];
			$.ajax
			({
				type: "POST",
				url: processURL,
				data: { action: 'delGrade', gradeID:id},
				success: function(msg)
				{ 
					console.log(msg);
					alert(msg);
					$('#hide_body').hide();
					location.reload();
				}
			});
			
		});
		$('body').on('click', '.isfail', function (){
			if($(this).attr('checked')) {
				var fail = 1;
			}
			else {
				var fail = 0;
			}
			var idArrr=$(this).attr('id').split('_');
			var id=idArrr[1];
			$.ajax
			({
				type: "POST",
				url: processURL,
				data: { action: 'isfail', gradeID:id, isfail:fail}
			});
		});
		$('.s_close').click(function(){
			$('.modal').hide();
			$('#hide_body').hide();
			$('.modalInput').val('');
		});
	
		$('#gradeAddSubmit').click(function(){
			var grade=$('#gradeAddName').val();
			if($('#isfail').is(':checked')) {
				var fail = 1;
			}
			else {
				var fail = 0;
			}
			$('#modal_sub_add').hide();
			$.ajax
			({
				type: "POST",
				url: processURL,
				data: { action: 'addGrade', gradeName:grade,isfail:fail},
				success: function(msg)
				{ 
					console.log(msg);
					alert(msg);
					$('#hide_body').hide();
					location.reload();
				}
			});
		});
		$('#gradeEditSubmit').click(function(){
			var gradeId=$('#gradeEditID').val();
			var grade=$('#gradeEditName').val();
			$('#modal_sub_edit').hide();
			$.ajax
			({
				type: "POST",
				url: processURL,
				data: { action: 'editGrade', gradeName:grade, gradeID: gradeId},
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