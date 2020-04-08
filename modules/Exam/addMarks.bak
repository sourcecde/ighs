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
	try {
	$sql1="SELECT * FROM `lakshya_exam_grade`";
	$result1=$connection2->prepare($sql1);
	$result1->execute();
	$grades=$result1->fetchAll();
	}
	catch(PDOException $e) { 
	echo $e;
	}
?>
<h3>Manage Marks :</h3>
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
		<b>Term:</b>
		<select name='termID' id='termID'>
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
		<input type='submit' name='filterManageExam' value='Go'>
	</td>
</tr>
</form>
</table>
<?php
if($_POST){
	extract($_POST);
	try{
	$sql1="SELECT COUNT(`marksID`) AS N FROM `lakshya_exam_marks` WHERE `examID` IN 
		(SELECT `examID` FROM `lakshya_exam_master` WHERE `termID`=$termID AND `yearGroupID`=$yearGroupID)";
	$result1=$connection2->prepare($sql1);
	$result1->execute();
	$dData=$result1->fetch();
	}
	catch(PDOException $e) { 
	echo $e;
	}
	try{
	$sql1="SELECT `gibbonperson`.`preferredName`, `gibbonstudentenrolment`.`rollOrder`, `gibbonstudentenrolment`.`gibbonStudentEnrolmentID`, `gibbonrollgroup`.`name` as `section`  
			FROM `gibbonstudentenrolment` 
			LEFT JOIN `gibbonperson` ON `gibbonperson`.`gibbonPersonID`=`gibbonstudentenrolment`.`gibbonPersonID` 
			LEFT JOIN `gibbonrollgroup` ON `gibbonrollgroup`.`gibbonRollGroupID`=`gibbonstudentenrolment`.`gibbonRollGroupID`
			WHERE `gibbonstudentenrolment`.`gibbonSchoolYearID`=$yearID AND `gibbonstudentenrolment`.`gibbonYearGroupID`=$yearGroupID 
			AND `gibbonperson`.`dateEnd` IS NULL
			ORDER BY `gibbonstudentenrolment`.`gibbonRollGroupID`,`gibbonstudentenrolment`.`rollOrder`";
	$result1=$connection2->prepare($sql1);
	$result1->execute();
	$pData=$result1->fetchAll();
	}
	catch(PDOException $e) { 
	echo $e;
	}
	try {
	$sql1="SELECT `lakshya_exam_master`.*,`lakshya_exam_subjects`.`subjectName`,`lakshya_exam_subjects`.`shortName`,`gibbonschoolyear`.`status`,`gibbonschoolyearterm`.`name` as term,`gibbonyeargroup`.`name` as class,
			`lakshya_exam_subjects`.`subjectID` as `subID` FROM `lakshya_exam_master` 
			LEFT JOIN `gibbonschoolyearterm` ON `gibbonschoolyearterm`.`gibbonSchoolYearTermID`=`lakshya_exam_master`.`termID` 
			LEFT JOIN `gibbonschoolyear` ON `gibbonschoolyear`.`gibbonSchoolYearID`=`gibbonschoolyearterm`.`gibbonSchoolYearID` 
			LEFT JOIN `gibbonyeargroup` ON `gibbonyeargroup`.`gibbonYearGroupID`=`lakshya_exam_master`.`yearGroupID` 
			LEFT JOIN `lakshya_exam_subjects` ON `lakshya_exam_subjects`.`subjectID`=`lakshya_exam_master`.`subjectID` WHERE 1";
			
	$sql1.=" AND `gibbonschoolyearterm`.`gibbonSchoolYearID`=$yearID";
	$sql1.="  AND `lakshya_exam_master`.`termID`=$termID";
	$sql1.=" AND `lakshya_exam_master`.`yearGroupID`=$yearGroupID";
	$sql1.=" ORDER BY `gibbonschoolyear`.`gibbonSchoolYearID` DESC,`gibbonschoolyearterm`.`gibbonSchoolYearTermID` DESC";
	$result1=$connection2->prepare($sql1);
	$result1->execute();
	$eData=$result1->fetchAll();
	}
	catch(PDOException $e) { 
	echo $e;
	}
	//print_r($eData);
	if($dData['N']==0){	
	$action=$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/processManageMarks.php";
	echo "<form action='$action' method='POST'>";
	echo "<div style='overflow-x: scroll;'>";
		echo "<input type='hidden' name='action' value='add' id='formAction'>";
		echo "<input type='hidden' name='yearID' value='$yearID'>";
		echo "<input type='hidden' name='yearGroupID' value='$yearGroupID'>";
		echo "<input type='hidden' name='termID' value='$termID'>";
	echo "<table style='min-width:100%'>";
	echo "<tr>";
		echo "<th style='text-align: center'>Student</th>";
		$i=0;
		foreach($eData as $e){
			$sName=$e['optional']=='N'?$e['shortName']:$e['optionalName']." <small>(Elective)</small>";
			$sID=$e['optional']=='N'?$e['subID']:"e".$i++;
			echo "<th style='text-align: center'>$sName<br><input type='checkbox' name='sub".$sID."' class='subcheck' checked='checked'></th>";
		}
		echo "<th style='text-align: center'>Conduct<br><input type='checkbox' class='subcheck' name='conduct' checked='checked'></th>";
		echo "<th style='text-align: center'>Application<br><input type='checkbox' class='subcheck' name='application' checked='checked'></th>";
		echo "<th style='text-align: center'>Remarks<br><input type='checkbox' class='subcheck' name='remarks' checked='checked'></th>";
	echo "<tr>";
	foreach($pData as $p){
		echo "<tr>";
			echo "<td><b>{$p['preferredName']}</b><br><span>Roll :<b>{$p['rollOrder']}</b></span><span style='float:right;'>Section: <b>{$p['section']}</b></span></td>";
			$enID=$p['gibbonStudentEnrolmentID']+0;
			$j=0;
			foreach($eData as $e){
				$sID=$e['optional']=='N'?$e['subID']:"e".$j++;
				echo "<td class='sub".$sID."'>";
				$eID=$e['examID'];
				echo "Absent <input type='checkbox' name='data[$enID][marks][$eID][isabsent]' class='absentC' id='a_$enID$eID'>";
				echo "<span id='ma_$enID$eID'>";
					echo $e['grade']=='N'?"<input type='text' name='data[$enID][marks][$eID][theoryMarks]' style='width:100%;' placeholder='Theory'>":""; 
					echo ($e['practical']=='Y' && $e['grade']=='N')?"<input type='text' name='data[$enID][marks][$eID][practicalMarks]' style='width:100%;' placeholder='Practical'>":""; 
					if($e['optional']=='Y')
					{
						$sql2="SELECT `lakshya_exam_optionalsubjects`.`subjectID`,`lakshya_exam_subjects`.`subjectName` FROM `lakshya_exam_optionalsubjects`
						LEFT JOIN `lakshya_exam_subjects` ON `lakshya_exam_subjects`.`subjectID`=`lakshya_exam_optionalsubjects`.`subjectID`
						WHERE `lakshya_exam_optionalsubjects`.`examID`=".$eID
						." AND `lakshya_exam_optionalsubjects`.`studentID`=".$p['gibbonStudentEnrolmentID'];
						$result2=$connection2->prepare($sql2);
						$result2->execute();
						$optname=$result2->fetch();
						echo "<span style='font-size:11px'>Opted for: ".$optname['subjectName']."</span>";
					}
					$gStr="";
					foreach($grades as $g){
						$gStr.="<option>{$g['grade']}</option>";
					}
					echo $e['grade']=='Y'?"<select name='data[$enID][marks][$eID][gradeO]'><option value=''>Select Grade</option>$gStr</select>":""; 
				echo "</span>";
				echo "</td>";
			}
			echo "<td class='conduct'><input type='text' name='data[$enID][conduct]' value='Good' style='width:100%;'></td>";
			echo "<td class='application'><input type='text' name='data[$enID][application]' value='Good' style='width:100%;'></td>";
			echo "<td class='remarks'><input type='text' name='data[$enID][remarks]' style='width:100%;'></td>";
		echo "</tr>";
	}
	echo "</table>";
	echo "</div>";
	echo "<input type='submit' value='Submit' id='submit'>";
	echo "</form>";
}
else{
	echo "<h1>Marks already entered for selected Class in selected term.</h1>";
	$eIDArr=array();
	foreach($pData as $p){
		$eIDArr[]=$p['gibbonStudentEnrolmentID']+0;
	}
	$eIDs=implode(',',$eIDArr);
	try{
		$sql1="SELECT * FROM `lakshya_exam_results` WHERE `termID`=$termID  AND `studentID` IN ($eIDs)";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		$resultsD=$result1->fetchAll();
	}
	catch(PDOException $e) { 
		echo $e;
	}
	$resultsData=array();
	foreach($resultsD as $r){
		$resultsData[$r['studentID']]=$r;
	}
	try{
		$sql1="SELECT `lakshya_exam_marks`.*,`lakshya_exam_results`.`studentID` FROM `lakshya_exam_marks`  
						LEFT JOIN `lakshya_exam_results` ON `lakshya_exam_results`.`resultsID`=`lakshya_exam_marks`.`resultsID`  
						WHERE `lakshya_exam_results`.`studentID` IN ($eIDs)";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		$marksD=$result1->fetchAll();
	}
	catch(PDOException $e) { 
		echo $e;
	}
	$marksData=array();
	foreach($marksD as $m){
		$marksData[$m['studentID']][$m['examID']]=$m;
	}
	$action=$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/processManageMarks.php";
	echo "<form action='$action' method='POST'>";
	echo "<div style='overflow-x: scroll;'>";
	echo "<input type='hidden' name='action' value='edit' id='formAction'>";
	echo "<input type='hidden' name='yearID' value='$yearID'>";
	echo "<input type='hidden' name='yearGroupID' value='$yearGroupID'>";
	echo "<input type='hidden' name='termID' value='$termID'>";
	echo "<table style='min-width:100%'>";
	echo "<tr>";
	echo "<th style='text-align: center'>Student</th>";
	$i=0;
	foreach($eData as $e){
		$sName=$e['optional']=='N'?$e['shortName']:$e['optionalName']." <small>(Elective)</small>";
		$sID=$e['optional']=='N'?$e['subID']:"e".$i++;
		echo "<th style='text-align: center'>$sName<br><input type='checkbox' name='sub".$sID."' class='subcheck' checked='checked'></th>";
	}
	echo "<th style='text-align: center'>Conduct<br><input type='checkbox' class='subcheck' name='conduct' checked='checked'></th>";
	echo "<th style='text-align: center'>Application<br><input type='checkbox' class='subcheck' name='application' checked='checked'></th>";
	echo "<th style='text-align: center'>Remarks<br><input type='checkbox' class='subcheck' name='remarks' checked='checked'></th>";
	echo "<tr>";
	foreach($pData as $p){
		echo "<tr>";
		echo "<td><b>{$p['preferredName']}</b><br><span>Roll :<b>{$p['rollOrder']}</b></span><span style='float:right;'>Section: <b>{$p['section']}</b></span></td>";
		$enID=$p['gibbonStudentEnrolmentID']+0;
		$j=0;
		foreach($eData as $e){
			$eID=$e['examID'];
			$rID=$marksData[$enID][$eID]['resultsID'];
			$mID=$marksData[$enID][$eID]['marksID'];
			$sID=$e['optional']=='N'?$e['subID']:"e".$j++;
			echo "<td class='sub".$sID."'>";
			$eID=$e['examID'];
			$checked=($marksData[$enID][$eID]['isabsent']==1)?"checked":"";
			$style=($marksData[$enID][$eID]['isabsent']==1)?"display:none;":"display:block;";
			echo "Absent <input type='checkbox' name='data[$rID][marks][$mID][isabsent]' class='absentC' id='a_$enID$eID' ".$checked.">";
			echo "<span id='ma_$enID$eID' style='".$style."'>";
			$theoryM=isset($marksData[$enID][$eID]['theoryMarks'])?(string)$marksData[$enID][$eID]['theoryMarks']:"";
			$placeholderTheory=isset($marksData[$enID][$eID]['theoryMarks'])?"placeholder='Theory'":"";
			$practicalM=isset($marksData[$enID][$eID]['practicalMarks'])?(string)$marksData[$enID][$eID]['practicalMarks']:"";
			$grade=isset($marksData[$enID][$eID]['gradeO'])?(string)$marksData[$enID][$eID]['gradeO']:"";
			echo $e['grade']=='N'?"<input type='text' name='data[$rID][marks][$mID][theoryMarks]' style='width:100%;' value='$theoryM' ".$placeholderTheory.">":""; 
			echo ($e['practical']=='Y' && $e['grade']=='N')?"<input type='text' name='data[$rID][marks][$mID][practicalMarks]' style='width:100%;' value='$practicalM'>":""; 
			if($e['optional']=='Y')
			{
				$sql2="SELECT `lakshya_exam_optionalsubjects`.`subjectID`,`lakshya_exam_subjects`.`subjectName` FROM `lakshya_exam_optionalsubjects`
				LEFT JOIN `lakshya_exam_subjects` ON `lakshya_exam_subjects`.`subjectID`=`lakshya_exam_optionalsubjects`.`subjectID`
				WHERE `lakshya_exam_optionalsubjects`.`examID`=".$eID
				." AND `lakshya_exam_optionalsubjects`.`studentID`=".$p['gibbonStudentEnrolmentID'];
				$result2=$connection2->prepare($sql2);
				$result2->execute();
				$optname=$result2->fetch();
				echo "<span style='font-size:11px'>Opted for: ".$optname['subjectName']."</span>";
			}
			$gStr="";
			foreach($grades as $g){
				if($g['grade']==$grade){
					$gStr.="<option selected>{$g['grade']}</option>";
				}
				else{
					$gStr.="<option>{$g['grade']}</option>";
				}
			}
			echo $e['grade']=='Y'?"<select name='data[$rID][marks][$mID][gradeO]'><option value=''>Select</option>$gStr</select>":""; 
			echo "</span>";
			echo "</td>";
		}
		$conduct=(string)$resultsData[$enID]['conduct'];
		$application=(string)$resultsData[$enID]['application'];
		$remarks=(string)$resultsData[$enID]['remarks'];
		echo "<td class='conduct'><input type='text' name='data[$rID][conduct]' value='$conduct' style='width:100%;'></td>";
		echo "<td class='application'><input type='text' name='data[$rID][application]' value='$application' style='width:100%;'></td>";
		echo "<td class='remarks'><input type='text' name='data[$rID][remarks]' value='$remarks' style='width:100%;'></td>";
		echo "</tr>";
	}
	echo "</table>";
	echo "</div>";
	echo "<input type='submit' value='Update' id='submit'>";
	echo "</form>";
}
}
}
?>
<div id='hide_body'style='background-color :rgba(0,0,0, 0.7); width:100%; height:100%; position:fixed; left:0px; top:0px; z-index:100; display:none;'>
</div>
<!--<input type='hidden' id='processURL' value='<?=$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajax/manageExam.php"?>'>-->
<input type='hidden' id='changeYearIDURL' value='<?=$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajax/changeYearID.php"?>'>
<input type='hidden' id='rankCalculator' value='<?=$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajax/rankCalculator.php"?>'>
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
	$('#submit').click(function(){
		$(":input").prop("disabled", false);
	});
	$('.absentC').change(function(){
		var id=$(this).attr('id');
		if($(this).is(":checked")){
			$('#m'+id).hide();
		}
		else
			$('#m'+id).show();
	});
	$('.subcheck').change(function(){
		var ColToHide = $(this).attr("name"); 
		if(this.checked){
			$("td[class='" + ColToHide + "']").find(':input').prop("disabled", false);      
		}
		else{
			$("td[class='" + ColToHide + "']").find(':input').prop("disabled", true);
		}
	});
});
</script>
