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
<h3>Manage Optional Papers :</h3>
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
	$sql1="SELECT * FROM `lakshya_exam_master` WHERE `termID`=$termID AND `yearGroupID`=$yearGroupID AND `optional`='Y'";
	$result1=$connection2->prepare($sql1);
	$result1->execute();
	$eData=$result1->fetchAll();
	}
	catch(PDOException $e) { 
	echo $e;
	}
	$idStr="";
	foreach($eData as $e){
		$idStr.=$idStr!=""?', ':'';
		$idStr.=$e['examID'];
	}
	//echo $idStr;
	if(sizeOf($eData)>0){
		try{
		$sql1="SELECT COUNT(`optionalID`) AS N FROM `lakshya_exam_optionalsubjects` WHERE `examID` IN ($idStr)";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		$dData=$result1->fetch();
		}
		catch(PDOException $e) { 
		echo $e;
		}
		//print_r($dData);
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
			try{
				$sql="SELECT * FROM `lakshya_exam_subjects`";
				$result=$connection2->prepare($sql);
				$result->execute();
				$subjects=$result->fetchAll();
			}
			catch(PDOException $e) { 
				echo $e;
			}
			//print_r($eData);
		if($dData['N']==0){
			echo "<h1 style='text-align: center;'>Optional Subejcts has  not been set yet!!</h1>";
			$action=$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/processManageOptionalSubjects.php";
			echo "<div style='overflow-x: scroll;'>";
			echo "<form action='$action' method='POST'>";
			echo "<input type='hidden' name='action' value='add' id='formAction'>";
			echo "<table style='min-width:100%'>";
			echo "<tr>";
			echo "<th style='text-align: center'>Student</th>";
			foreach($eData as $s){
				echo "<th style='text-align: center'>{$s['optionalName']}</th>";
			}
			echo "<tr>";
			foreach($pData as $p){
				echo "<tr>";
				echo "<td><b>{$p['preferredName']}</b><br><span>Roll :<b>{$p['rollOrder']}</b></span><span style='float:right;'>Section: <b>{$p['section']}</b></span></td>";
				$enID=$p['gibbonStudentEnrolmentID']+0;
				foreach($eData as $e){
					echo "<td>";
					$eID=$e['examID'];
					echo "<select name='data[$enID][$eID]'>";
					//echo "<option value=''>Select</option>";
					foreach($subjects as $s)
						echo "<option value='{$s['subjectID']}'>{$s['subjectName']}</option>";
					echo "</select>";
					echo "</td>";
			}
			echo "</tr>";
		}
		echo "</table>";
		echo "<input type='submit' value='Submit'>";
		echo "</form>";
		echo "</div>";
	}
		else{
			try{
				$sql1="SELECT * FROM `lakshya_exam_optionalsubjects` WHERE `examID` IN ($idStr)";
				$result1=$connection2->prepare($sql1);
				$result1->execute();
				$oData=$result1->fetchAll();
			}
			catch(PDOException $e) { 
				echo $e;
			}
			$optData=array();
			foreach($oData as $o){
				$optData[$o['studentID']][$o['examID']]=$o;
			}
			$action=$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/processManageOptionalSubjects.php";
			echo "<div style='overflow-x: scroll;'>";
			echo "<form action='$action' method='POST'>";
			echo "<input type='hidden' name='action' value='edit' id='formAction'>";
			echo "<table style='min-width:100%'>";
			echo "<tr>";
			echo "<th style='text-align: center'>Student</th>";
			foreach($eData as $s){
				echo "<th style='text-align: center'>{$s['optionalName']}</th>";
			}
			echo "<tr>";
			foreach($pData as $p){
				echo "<tr>";
				echo "<td><b>{$p['preferredName']}</b><br><span>Roll :<b>{$p['rollOrder']}</b></span><span style='float:right;'>Section: <b>{$p['section']}</b></span></td>";
				$enID=$p['gibbonStudentEnrolmentID']+0;
				foreach($eData as $e){
					echo "<td>";
					$eID=$e['examID'];
					$oID=$optData[$enID][$eID]['optionalID'];
					echo "<select name='data[$oID]'>";
					foreach($subjects as $s){
						if($s['subjectID']==$optData[$enID][$eID]['subjectID'])
							echo "<option value='{$s['subjectID']}' selected>{$s['subjectName']}</option>";
						else
							echo "<option value='{$s['subjectID']}'>{$s['subjectName']}</option>";
					}
					echo "</select>";
					echo "</td>";
			}
			echo "</tr>";
		}
		echo "</table>";
		echo "<input type='submit' value='Update'>";
		echo "</form>";
		echo "</div>";	
		}
}
	else
		echo "<h1>No Optional Subject for the selected class in selected term.</h1>";
}
}
?>
<div id='hide_body'style='background-color :rgba(0,0,0, 0.7); width:100%; height:100%; position:fixed; left:0px; top:0px; z-index:100; display:none;'>
</div>
<!--<input type='hidden' id='processURL' value='<?=$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajax/manageExam.php"?>'>-->
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
});
</script>