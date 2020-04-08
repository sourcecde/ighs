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
		<input type='submit' name='filterManageExam' id='submit' value='Go'>
		<input type='submit' name='filterManageExam' id='print' value='Print'>
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
		if($dData['N']>0){
			try{
				$sql1="SELECT `gibbonperson`.`preferredName`, `gibbonstudentenrolment`.`rollOrder`, `gibbonstudentenrolment`.`gibbonStudentEnrolmentID`, `gibbonrollgroup`.`name` as `section` 
				FROM `gibbonstudentenrolment` 
				LEFT JOIN `gibbonperson` ON `gibbonperson`.`gibbonPersonID`=`gibbonstudentenrolment`.`gibbonPersonID` 
				LEFT JOIN `gibbonrollgroup` ON `gibbonrollgroup`.`gibbonRollGroupID`=`gibbonstudentenrolment`.`gibbonRollGroupID`
				WHERE `gibbonstudentenrolment`.`gibbonSchoolYearID`=$yearID AND `gibbonstudentenrolment`.`gibbonYearGroupID`=$yearGroupID AND `gibbonperson`.`dateEnd` IS NULL 
				ORDER BY `gibbonstudentenrolment`.`gibbonRollGroupID`,`gibbonstudentenrolment`.`rollOrder`";
				$result1=$connection2->prepare($sql1);
				$result1->execute();
				$pData=$result1->fetchAll();
			}
			catch(PDOException $e) { 
				echo $e;
			}
			$eIDArr=array();
			foreach($pData as $p){
				$eIDArr[]=$p['gibbonStudentEnrolmentID']+0;
			}
			$eIDs=implode(',',$eIDArr);
			try {
				$sql1="SELECT `lakshya_exam_master`.*,`lakshya_exam_subjects`.`subjectName`,`lakshya_exam_subjects`.`shortName`,`gibbonschoolyear`.`status`,`gibbonschoolyearterm`.`name` as term,`gibbonyeargroup`.`name` as class  
					FROM `lakshya_exam_master` 
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
				$sql1="SELECT `lakshya_exam_marks`.*,`lakshya_exam_results`.`studentID`,`lakshya_exam_grade`.`isfail` FROM `lakshya_exam_marks`  
						LEFT JOIN `lakshya_exam_results` ON `lakshya_exam_results`.`resultsID`=`lakshya_exam_marks`.`resultsID` 
						LEFT JOIN `lakshya_exam_grade` ON `lakshya_exam_grade`.`grade`=`lakshya_exam_marks`.`gradeO` 
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
			$pcount=0;
			echo "<div style='overflow-x: scroll;' id='tabulationSheet'>";
			echo "<table style='min-width:100%' id='marksTable'>";
			echo "<tr>";
			echo "<th  rowspan='2' style='text-align: center'>Student</th>";
			foreach($eData as $e){
				$sName=$e['optional']=='N'?$e['shortName']:$e['optionalName']." <small>Elective</small>";
				if($e['practical']=='Y'){
					echo "<th colspan ='2' style='text-align: center'>$sName</th>";
					$pcount++;
				}
				else{
					echo "<th rowspan='2' style='text-align: center'>$sName</th>";
				}
			}
			echo "<th style='text-align: center' rowspan='2'>Conduct</th>";
			echo "<th style='text-align: center' rowspan='2'>Application</th>";
			echo "<th style='text-align: center' rowspan='2'>Remarks</th>";
			echo "<th style='text-align: center' rowspan='2'>Rank</th>";
			echo "</tr>";
			echo "<tr>";
			for($i=0;$i<$pcount;$i++){
			echo "<td style='text-align: center;background: #e1e1e1;'><b>Th</b></td>";
			echo "<td style='text-align: center;background: #e1e1e1;'><b>Pr</b></td>";
			}
			echo "</tr>";
			foreach($pData as $p){
				echo "<tr>";
				echo "<td style='min-width: 11ch;'><b>{$p['preferredName']}</b><br><span>Roll :<b>{$p['rollOrder']}</b></span><span style='float:left;'>Section: <b>{$p['section']}</b></span></td>";
				$enID=$p['gibbonStudentEnrolmentID']+0;
				if(isset($enID,$marksData) && isset($enID,$resultsData)){
					foreach($eData as $e){
						echo "<td style='text-align:center;'>";
						$eID=$e['examID'];
						if($e['optional']=='Y'){
							$sql2="SELECT `lakshya_exam_optionalsubjects`.`subjectID`,`lakshya_exam_subjects`.`shortName` FROM `lakshya_exam_optionalsubjects`
								LEFT JOIN `lakshya_exam_subjects` ON `lakshya_exam_subjects`.`subjectID`=`lakshya_exam_optionalsubjects`.`subjectID`
								WHERE `lakshya_exam_optionalsubjects`.`examID`=".$eID
								." AND `lakshya_exam_optionalsubjects`.`studentID`=".$p['gibbonStudentEnrolmentID'];
							$result2=$connection2->prepare($sql2);
							$result2->execute();
							$odata=$result2->fetch();
							$optname=$odata['shortName'];
						}
						else{
							$optname="";
						}
						if($marksData[$enID][$eID]['isabsent']==0){
							$theoryM=isset($marksData[$enID][$eID]['theoryMarks'])?$marksData[$enID][$eID]['theoryMarks']:"";
							$theoryFailStyle=($theoryM<$e['theoryPassMarks'])?";color:red;":"";
							$practicalM=isset($marksData[$enID][$eID]['practicalMarks'])?$marksData[$enID][$eID]['practicalMarks']:"";
							$practicalFailStyle=($practicalM<$e['practicalPassMarks'])?";color:red;":"";
							$grade=isset($marksData[$enID][$eID]['gradeO'])?$marksData[$enID][$eID]['gradeO']:"";
							$gradeFailStyle=($marksData[$enID][$eID]['isfail']==1)?";color:red;":"";
							echo $e['grade']=='N'?"<b style='".$theoryFailStyle."'><span style='font-size:11px;float:left;padding-right:12px;color:black;'>".$optname."</span>{$theoryM}</b>":""; 
							echo ($e['practical']=='Y' && $e['grade']=='N')?"</td><td style='text-align:center;'><b style='".$practicalFailStyle."'><span style='font-size:11px;float:left;padding-right:12px;color:black;'>".$optname."</span>{$practicalM}</b>":""; 
							echo $e['grade']=='Y'?"<b style='".$gradeFailStyle."'><span style='font-size:11px;float:left;padding-right:12px;color:black;'>".$optname."</span>{$grade}</b>":""; 
						}
						else
							echo "<p style='color:red'>Absent<span style='font-size:11px;color: black;'> ".$optname." </span></p>";
						echo "</td>";
					}
					echo "<td>{$resultsData[$enID]['conduct']}</td>";
					echo "<td>{$resultsData[$enID]['application']}</td>";
					echo "<td>{$resultsData[$enID]['remarks']}</td>";
					if($resultsData[$enID]['Rank']==-1)
					{
						$rank='N/A';
					}
					else{
						$rank=$resultsData[$enID]['Rank'];
					}
					echo "<td>{$rank}</td>";
				}
				echo "</tr>";
			}
			echo "</table>";
			echo "</div>";
			}
		else{
			echo "<h1>Marks aren't entered for selected Class in selected term.</h1>";
		}
		}
}	
?>
<div id='hide_body'style='background-color :rgba(0,0,0, 0.7); width:100%; height:100%; position:fixed; left:0px; top:0px; z-index:100; display:none;'>
</div>
<!--<input type='hidden' id='processURL' value='<?=$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajax/manageExam.php"?>'>-->
<input type='hidden' id='changeYearIDURL' value='<?=$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajax/changeYearID.php"?>'>
<script>
function printDiv() 
{

  var divToPrint=document.getElementById('tabulationSheet');

  var newWin=window.open('','Print-Window');

  newWin.document.open();

  newWin.document.write('<html><body onload="window.print()">'+divToPrint.innerHTML+'</body></html>');

  newWin.document.close();

  setTimeout(function(){newWin.close();},10);

}

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
	$('#print').click(function(){
		$('#submit').click();
		$('#marksTable').attr('border','1');
		printDiv();
	});
});

</script>