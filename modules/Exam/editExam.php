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
if(isset($_REQUEST['examID'])){
$examID=$_REQUEST['examID'];
	try {
	$sql1="SELECT `lakshya_exam_master`.*,`gibbonschoolyearterm`.`gibbonSchoolYearID` FROM `lakshya_exam_master`
			LEFT JOIN `gibbonschoolyearterm` ON `gibbonschoolyearterm`.`gibbonSchoolYearTermID`=`lakshya_exam_master`.`termID` 
			WHERE  `examID`=$examID";
	$result1=$connection2->prepare($sql1);
	$result1->execute();
	$exam=$result1->fetch();
	}
	catch(PDOException $e) { 
	echo $e;
	}
	//print_r($exam);
	try {
	$sql1="SELECT `gibbonSchoolYearID`, `name`, `status` FROM `gibbonschoolyear`";
	$result1=$connection2->prepare($sql1);
	$result1->execute();
	$year=$result1->fetchAll();
	}
	catch(PDOException $e) { 
	echo $e;
	}
	try {
	$sql1="SELECT `gibbonSchoolYearTermID`,`gibbonschoolyearterm`.`name` FROM `gibbonschoolyearterm` 
			LEFT JOIN `gibbonschoolyear` ON `gibbonschoolyear`.`gibbonSchoolYearID`=`gibbonschoolyearterm`.`gibbonSchoolYearID` 
			WHERE `gibbonschoolyearterm`.`gibbonSchoolYearID`={$exam['gibbonSchoolYearID']}";
	$result1=$connection2->prepare($sql1);
	$result1->execute();
	$term=$result1->fetchAll();
	}
	catch(PDOException $e) { 
	echo $e;
	}
	try {
	$sql1="SELECT `gibbonYearGroupID`,`name` FROM `gibbonyeargroup`";
	$result1=$connection2->prepare($sql1);
	$result1->execute();
	$class=$result1->fetchAll();
	}
	catch(PDOException $e) { 
	echo $e;
	}
	try {
	$sql1="SELECT * FROM `lakshya_exam_subjects`";
	$result1=$connection2->prepare($sql1);
	$result1->execute();
	$subjects=$result1->fetchAll();
	}
	catch(PDOException $e) { 
	echo $e;
	}
?>
<h1>Edit Exam</h1>
<table width='100%'>
<colgroup>
<col width="33%"></col>
<col width="33%"></col>
<col width="33%"></col>
</colgroup>
<tr>
	<td>
		<b>Select Year:</b>
		<select name='yearID' id='yearID'>
		<?php
			foreach($year as $y){
				$s=$y['gibbonSchoolYearID']==$exam['gibbonSchoolYearID']?'selected':'';
			echo "<option value='{$y['gibbonSchoolYearID']}' $s>{$y['name']}</option>";
			}
		?>
		</select>
	</td>
	<td>
		<b>Term:</b>
		<select name='termID' id='termID'>
		<?php
			foreach($term as $t){
				$s=$t['gibbonSchoolYearTermID']==$exam['termID']?'selected':'';
			echo "<option value='{$t['gibbonSchoolYearTermID']}' $s>{$t['name']}</option>";
			}
		?>
		</select>
	</td>
	<td>
		<b>Class:</b>
		<select name='yearGroupID' id='yearGroupID'>
		<?php
			foreach($class as $c){
				$s=$c['gibbonYearGroupID']==$exam['yearGroupID']?'selected':'';
			echo "<option value='{$c['gibbonYearGroupID']}' $s>{$c['name']}</option>";
			}
		?>
		</select>
	</td>
</tr>
<tr>
	<td>
		<b>Elective ?</b> &nbsp;&nbsp;
		<input type='checkbox' id='isOptional' <?=$exam['optional']=='Y'?'checked':''?>>
		<input type='text' name='optionalName' id='optionalName' <?=$exam['optional']=='Y'?"value='".$exam['optionalName']."'":"style='display:none;'"?> >
	</td>
	<td>
		<b>Subject Name :</b> &nbsp;&nbsp;
		<select name='subjectID' id='subjectID' <?=$exam['optional']=='Y'?'disabled':''?>>
			<option value=''>--Select Subject--</option>
			<?php
			foreach($subjects as $s){
				$sl=$exam['optional']!='Y'?($s['subjectID']==$exam['subjectID']?'selected':''):'';
				echo "<option value='{$s['subjectID']}' $sl>{$s['subjectName']}</option>";
			}
			?>
		</select>
	</td>
	<td>
		<b>Parent Subject ?</b> &nbsp;&nbsp;
		<input type='checkbox' id='isParentSubject' name='isParentSubject' <?php if(isset($exam['parentSubjectID'])){echo "checked";}?>>
		<select name='parentSubjectID' id='parentSubjectID' <?php if(!isset($exam['parentSubjectID'])){echo "style='display:none;width: 85%;float: left;'";}?>>
			<option value=''>--Parent Subject--</option>
			<?php
			foreach($subjects as $s){
				$sl=$exam['optional']!='Y'?($s['subjectID']==$exam['parentSubjectID']?'selected':''):'';
				echo "<option value='{$s['subjectID']}' $sl>{$s['subjectName']}</option>";
			}
			?>
		</select>
	</td>
	</tr>
	<tr>
	<td>
		<input type='text' id='groupName' <?php if(isset($exam['groupName'])){echo "style='float:left;width: 140px;'value='".$exam['groupName']."'";} else{echo "style='display:none'";}?>>
		<span style='float:right'>
		<b>With out Group ?</b> &nbsp;&nbsp;
		<input type='checkbox' id='isWGroup' <?php if(isset($exam['groupName'])==false){echo "checked";}?>>
		</span>
	</td>
	<td>
		<b>Practical :</b>
		<select name='isPractical' id='isPractical'>
			<option value='N' <?=$exam['practical']=='N'?'selected':''?>>No</option>
			<option value='Y' <?=$exam['practical']=='Y'?'selected':''?>>Yes</option>
		</select>
	</td>
	<td>
		<b>Graded :</b>
		<select name='isGraded' id='isGraded'>
			<option value='N' <?=$exam['grade']=='N'?'selected':''?>>No</option>
			<option value='Y' <?=$exam['grade']=='Y'?'selected':''?>>Yes</option>
		</select>
	</td>
</tr>
<tr>
	<td>
		<b>Theory Total Marks :</b>
		<input type='text' name='theoryTotalMarks' id='theoryTotalMarks' class='marks' style='width: 20%' value='<?=$exam['grade']=='N'?$exam['theoryTotalMarks']:''?>' <?=$exam['grade']=='Y'?'disabled':''?>>
	</td>
	<td>
		<b>Practical Total Marks :</b>
		<input type='text' name='practicalTotalMarks' id='practicalTotalMarks' class='marks' style='width: 20%' value='<?=($exam['grade']=='N' && $exam['practical']=='Y')?$exam['practicalTotalMarks']:''?>' <?=($exam['grade']=='Y' || $exam['practical']=='N')?'disabled':''?>>
	</td>
	<td>
		<b>Pass Marks :</b>
		<input type='text' name='practicalPassMarks' id='practicalPassMarks' class='marks' style='width: 20%' value='<?=$exam['grade']=='N'?$exam['practicalPassMarks']:''?>' <?=$exam['grade']=='Y' || $exam['practical']=='N'?"style='display:none'":""?>>
		<input type='text' name='theoryPassMarks' id='theoryPassMarks' class='marks' style='width: 20%' value='<?=$exam['grade']=='N'?$exam['theoryPassMarks']:''?>' <?=$exam['grade']=='Y'?'disabled':''?>>
	</td>
</tr>
<tr>
	<td colspan='3'>
<?php	$url=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/manageExam.php";
		echo "<button onclick=\"location.href = '".$url."';\" class='cButton'>< Back to Manage Exams</button>";
?>
		<input type='hidden' name='examID' id='examID' value='<?=$examID?>'>
		<button class="cButton" id='editExamSubmit' style='float: right;'>Update</button>
	</td>
</tr>
</table>
<?php
}
}
?>
<div id='hide_body'style='background-color :rgba(0,0,0, 0.7); width:100%; height:100%; position:fixed; left:0px; top:0px; z-index:100; display:none;'>
</div>
<input type='hidden' id='processURL' value='<?=$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajax/editExam.php"?>'>
<input type='hidden' id='changeYearIDURL' value='<?=$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajax/changeYearID.php"?>'>
<script>
$(document).ready(function(){
	var processURL=$('#processURL').val();
	var changeYearIDURL=$('#changeYearIDURL').val();
	
	$('#yearID').change(function(){
		var yearID=$(this).val();
		$.ajax
			({
				type: "POST",
				url: changeYearIDURL,
				data: { action: 'changeYearID', yearID:yearID},
				success: function(msg)
				{ 
					console.log(msg);
					$('#termID').empty().append(msg);
				}
			});
	});
	$('#isOptional').click(function(){
		$('#subjectID option[value=""]').prop('selected', true);
		$('#subjectID').prop( "disabled", $(this).is(":checked") );
		if($(this).is(":checked"))
			$('#optionalName').show();
		else
			$('#optionalName').hide();
			
	});
	$('#isParentSubject').click(function(){
		$('#parentSubjectID option[value=""]').prop('selected', true);
		if($(this).is(":checked"))
			$('#parentSubjectID').show();
		else
			$('#parentSubjectID').hide();
		
	});
	$('#isWGroup').click(function(){
		if($(this).is(":checked"))
			$('#groupName').hide();
		else
			$('#groupName').show();
		
	});
	$('#isPractical').change(function(){
		if($(this).val()=='N'){
			$('#practicalTotalMarks').prop( "disabled", true);
			$('#practicalPassMarks').hide();
			$('#practicalTotalMarks').val('');
			$('#practicalPassMarks').val('');
			$('#theoryPassMarks').attr("placeholder", "");
		}
		else{
			$('#practicalTotalMarks').prop( "disabled", false);
			$('#practicalPassMarks').show();
			$('#theoryPassMarks').attr("placeholder", "Theory");
		}
	});
	$('#isGraded').change(function(){
		if($(this).val()=='Y'){
			$('.marks').prop( "disabled", true);
			$('.marks').val('');
		}
		else{
			$('.marks').prop( "disabled", false);
			$('#practicalTotalMarks').prop( "disabled", $('#isPractical').val()=='N');
		}
	});
	$('#editExamSubmit').click(function(){
		var inputdata={};
		inputdata["examID"]=$('#examID').val();
		inputdata["yearID"]=$('#yearID').val();
		inputdata["termID"]=$('#termID').val();
		inputdata["yearGroupID"]=$('#yearGroupID').val();
		inputdata["subjectID"]=$('#subjectID').val();
		inputdata["optional"]=$('#isOptional').is(":checked")?'Y':'N';
		inputdata["optionalName"]=$('#optionalName').val();
		inputdata["group"]=$('#isWGroup').is(":checked")?'N':'Y';
		inputdata["groupName"]=$('#groupName').val();
		inputdata["parentSubject"]=$('#isParentSubject').is(":checked")?'Y':'N';
		inputdata["parentSubjectID"]=$('#parentSubjectID').val();
		inputdata["practical"]=$('#isPractical').val();
		inputdata["grade"]=$('#isGraded').val();
		inputdata["theoryTotalMarks"]=$('#theoryTotalMarks').val();
		inputdata["practicalTotalMarks"]=$('#practicalTotalMarks').val();
		inputdata["theoryPassMarks"]=$('#theoryPassMarks').val();
		inputdata["practicalPassMarks"]=$('#practicalPassMarks').val();
		if(!$('#isOptional').is(":checked") && $('#subjectID').val()==''){
			alert("Select a subject");
			$('#subjectID').focus();
			return;
		}
		else if(!$('#isWGroup').is(":checked") && $('#groupName').val()==''){
			alert("Enter a group name.");
			$('#groupName').focus();
			return;
		}
		else if($('#theoryTotalMarks').val().length === 0){
			alert("Enter the Theory Total Marks");
			$('#theoryTotalMarks').focus();
			return;
		}
		else if($('#isPractical').val()=='Y' && $('#practicalTotalMarks').val().length === 0){
			alert("Enter the Practical Total Marks");
			$('#practicalTotalMarks').focus();
			return;
		}
		$('#hide_body').show();
		$.ajax
			({
				type: "POST",
				url: processURL,
				data: { action: 'updateExam', inputdata:inputdata},
				success: function(msg)
				{ 
					$('#subjectID option[value=""]').prop('selected', true);
					console.log(msg);
					alert("Updated Sucessfully!!");
					$('#hide_body').fadeOut();
					location.reload();
				}
			});
	});
});
</script>