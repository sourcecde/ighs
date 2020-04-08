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
			LEFT JOIN `gibbonschoolyear` ON `gibbonschoolyear`.`gibbonSchoolYearID`=`gibbonschoolyearterm`.`gibbonSchoolYearID` ";
		if($_POST)
			$sql1.=" WHERE `gibbonschoolyearterm`.`gibbonSchoolYearID`=".$_POST['yearID'];
		else
			$sql1.="WHERE `status`='Current'";
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
<h3>Manage Exam :</h3>
<table width='100%'>
<form method='POST' action=''>
<tr>
	<td>
		<b>Select Year:</b>
		<select name='yearID' id='yearID'>
		<?php
			foreach($year as $y){
				if($_POST)
					$s=$y['gibbonSchoolYearID']==$_POST['yearID']?'selected':'';
				else
					$s=$y['status']=='Current'?'selected':'';
			echo "<option value='{$y['gibbonSchoolYearID']}' $s>{$y['name']}</option>";
			}
		?>
		</select>
	</td>
	<td>
		<b>Class:</b>
		<select name='yearGroupID' id='yearGroupID'>
		<?php
			foreach($class as $c){
				if($_POST)
					$s=$c['gibbonYearGroupID']==$_POST['yearGroupID']?'selected':'';
			echo "<option value='{$c['gibbonYearGroupID']}' $s>{$c['name']}</option>";
			}
		?>
		</select>
	</td>
	<td>
		<b>Term:</b>
		<select name='termID' id='termID'>
			<option value=''>All</option>
		<?php
			foreach($term as $t){
				if($_POST)
					$s=$t['gibbonSchoolYearTermID']==$_POST['termID']?'selected':'';
			echo "<option value='{$t['gibbonSchoolYearTermID']}' $s>{$t['name']}</option>";
			}
		?>
		</select>
	</td>
	<td>
		<input type='submit' name='filterManageExam' value='Go'>
		</form>
		<?php $url=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/addExams.php";
		echo "<button onclick=\"location.href = '".$url."';\" class='cButton'>Add New Exam</button>";
		?>
	</td>
</tr>
</table>
<?php
	if($_POST){
		$query="";
		if(isset($_POST['yearID']))
		$query.=" AND `gibbonschoolyearterm`.`gibbonSchoolYearID`=".$_POST['yearID'];
		if($_POST['termID']!='')
			$query.=" AND `lakshya_exam_master`.`termID`=".$_POST['termID'];
		if($_POST['yearGroupID']!='')
			$query.=" AND `lakshya_exam_master`.`yearGroupID`=".$_POST['yearGroupID'];
		try {
		$sql1="SELECT `lakshya_exam_master`.*,`s1`.`subjectName`,`s2`.`subjectName` AS parentSubjectName,`gibbonschoolyearterm`.`name` as term   
				FROM `lakshya_exam_master` 
				LEFT JOIN `gibbonschoolyearterm` ON `gibbonschoolyearterm`.`gibbonSchoolYearTermID`=`lakshya_exam_master`.`termID` 
				LEFT JOIN `lakshya_exam_subjects`  s1 ON `s1`.`subjectID`=`lakshya_exam_master`.`subjectID` 
				LEFT JOIN `lakshya_exam_subjects`  s2 ON `s2`.`subjectID`=`lakshya_exam_master`.`parentSubjectID` 
				WHERE 1";
		$sql1.=$query;
		$sql1.=" ORDER BY `gibbonschoolyearterm`.`gibbonSchoolYearTermID` DESC";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		$eData=$result1->fetchAll();
		}
		catch(PDOException $e) { 
		echo $e;
		}
		//print_r($eData);
		$examData=array();
		foreach($eData as $e){
			if($e['parentSubjectID']=='')
				$examData[$e['term']][$e['groupName']]['single'][]=$e;
			else
				$examData[$e['term']][$e['groupName']]['parent'][$e['parentSubjectName']][]=$e;
		}
		//print_r($examData);
		
		
		echo "<div style=' overflow-x: scroll;'>";
		echo "<table style='min-width:100%;' class='mainTable'>";
		foreach($examData as $term=>$ed){
			echo "<tr>";
			echo "<td><b>$term</b></td>";
			echo $term;
			echo "<td style='padding:0;'><table style='width:100%; margin:0;'>";
			foreach($ed as $groupName=>$g){
				echo "<tr>";
					echo "<td><b>$groupName</b></td>";
				if(array_key_exists('parent',$g)){
				foreach($g['parent'] as $parentSubjectName=>$p){
					echo "<td style='padding:0;'><table style='width:100%; margin:0;'>";
						echo "<tr><td style='text-align:center' colspan='".sizeOf($p)."'><b>$parentSubjectName</b></td></tr>";
						foreach($p as $s){
							echo "<td style='text-align:center'>";
								echo $s['optional']=='N'?"<b>{$s['subjectName']}</b><br>":"<b>{$s['optionalName']} (Elective)</b><br>";
								if($s['grade']=='N'){
									echo "Theory : ".$s['theoryTotalMarks'];
									echo $s['practical']=='Y'?"<br>Practical : ".$s['practicalTotalMarks']:"";
									echo $s['practical']=='N'?"<br>Pass Marks : ".$s['theoryPassMarks']:"<br>Pass Marks(Theory) : ".$s['theoryPassMarks']."<br>Pass Marks(Practical) : ".$s['practicalPassMarks'];
								}
								else
									echo "Graded";
								$editURL=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"]."/editExam.php&examID=".$s['examID'];
								echo "<br><a style='color:red;' href='$editURL'>Edit</a> | ";
								echo "<a class='delExam' id='{$s['examID']}' style='color:red'>Delete</a>";
							echo "</td>"; 
						}
						echo "</tr>";
					echo "</table></td>";
				}
				}
				if(array_key_exists('single',$g)){
				foreach($g['single'] as $s){
					echo "<td style='text-align:center'>";
						echo $s['optional']=='N'?"<b>{$s['subjectName']}</b><br>":"<b>{$s['optionalName']} (Elective)</b><br>";
						if($s['grade']=='N'){
							echo "Theory : ".$s['theoryTotalMarks'];
							echo $s['practical']=='Y'?"<br>Practical : ".$s['practicalTotalMarks']:"";
							echo $s['practical']=='N'?"<br>Pass Marks : ".$s['theoryPassMarks']:"<br>Pass Marks(Theory) : ".$s['theoryPassMarks']."<br>Pass Marks(Practical) : ".$s['practicalPassMarks'];
						}
						else
							echo "Graded";
						$editURL=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"]."/editExam.php&examID=".$s['examID'];
						echo "<br><a style='color:red;' href='$editURL'>Edit</a> | ";
						echo "<a class='delExam' id='{$s['examID']}' style='color:red'>Delete</a>";
					echo "</td>";
				}
				}
				echo "</tr>";
			}	
			echo "</table></td>";
			echo "</tr>";
		}
		echo "</table>";
		echo "</div>";

	}
}
?>
<div id='hide_body'style='background-color :rgba(0,0,0, 0.7); width:100%; height:100%; position:fixed; left:0px; top:0px; z-index:100; display:none;'>
</div>
<input type='hidden' id='processURL' value='<?=$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajax/manageExam.php"?>'>
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
					$('#termID').empty().append("<option value=''>All</option>"+msg);
				}
			});
	});
	$('.delExam').click(function(){
		var r=confirm("Are you sure you want to delete this?");
		if(r){
			var examID=$(this).prop('id');
			$.ajax
			({
				type: "POST",
				url: processURL,
				data: { action: 'deleteExam', examID:examID},
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