<?php

@session_start() ;
	try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	}
	catch(PDOException $e) {
	echo $e->getMessage();
	}
	try{
	$sql1="SELECT `gibbonYearGroupID`, `name` FROM `gibbonyeargroup` ORDER BY `sequenceNumber`" ;
	$result1=$connection2->prepare($sql1);
	$result1->execute();	
	$class=$result1->fetchall();
	}
	catch(PDOException $e) { 
	echo $e;
	}
	try{
	$sql2="SELECT `gibbonSchoolYearID`, `name`, `status` FROM `gibbonschoolyear`" ;
	$result2=$connection2->prepare($sql2);
	$result2->execute();	
	$year=$result2->fetchall();
	}
	catch(PDOException $e) { 
	echo $e;
	}
	try{
	$sql3="SELECT `gibbonperson`.`preferredName`,`gibbonstudentenrolment`.`gibbonStudentEnrolmentID`
	FROM `gibbonstudentenrolment`
	LEFT JOIN `gibbonperson` ON `gibbonperson`.`gibbonPersonID`=`gibbonstudentenrolment`.`gibbonPersonID`
	WHERE `gibbonperson`.`dateEnd` IS NULL
	ORDER BY `gibbonYearGroupID`,`gibbonperson`.`preferredName`" ;
	$result3=$connection2->prepare($sql3);
	$result3->execute();	
	$student=$result3->fetchall();
	}
	catch(PDOException $e) { 
	echo $e;
	}
	try{
	$sql4="SELECT `gibbonSchoolYearTermID`,`gibbonschoolyearterm`.`name` as `term` FROM `gibbonschoolyearterm` 
			LEFT JOIN `gibbonschoolyear` ON `gibbonschoolyear`.`gibbonSchoolYearID`=`gibbonschoolyearterm`.`gibbonSchoolYearID` ";
		if($_POST)
			$sql4.=" WHERE `gibbonschoolyearterm`.`gibbonSchoolYearID`=".$_POST['year'];
		else
			$sql4.="WHERE `status`='Current'";
	$result4=$connection2->prepare($sql4);
	$result4->execute();
	$term=$result4->fetchAll();
	}
	catch(PDOException $e) { 
	echo $e;
	}
?>

<form action="modules/Exam/generate_report_card.php" method='post' target="_blank">
<table width='80%'>
	<tr>
		<td>Select Year: <select name='year' id='year'>
									<?php foreach($year as $a) {
									if($_POST)
										$s=$a['gibbonSchoolYearID']==$_POST['year']?'selected':'';
									else
										$s=$a['status']=='Current'?'selected':''; ?>
									<option value='<?php echo $a['gibbonSchoolYearID'];?>'><?php echo $a['name'];?></option>
									<?php } ?>
							</select></td>
	</tr>
	<tr>
		<td>Term<span style="color:red">*</span>: <select name='term' id='term' required>
									<option value=''> Select</option>
									<?php foreach($term as $a) {
									if($_POST)
										$s=$a['gibbonSchoolYearTermID']==$_POST['term']?'selected':'';	?>
									<option value='<?php echo $a['gibbonSchoolYearTermID'];?>'><?php echo $a['term'];?></option>
									<?php } ?>
							</select></td>
	</tr>
	<tr>
		<td>Class: <select name='class' id='class'>
									<option value=''> All </option>
									<?php foreach($class as $a) { ?>
									<option value='<?php echo $a['gibbonYearGroupID'];?>'><?php echo $a['name'];?></option>
									<?php } ?>
							</select></td>
	</tr>
	<tr>
		<td>Select Student: <select multiple name='student_id[]' id='student_id' style='height:10em;min-width: 225px;'>
									<?php foreach($student as $a) { ?>
									<option value='<?php echo $a['gibbonStudentEnrolmentID'];?>'><?php echo $a['preferredName'];?></option>
									<?php } ?>
							</select></td>
	</tr>
	<tr>
		<td style="text-align: center;padding-left: 55px;"><input type='submit' id='' value='Get Report Card(s)'><span style='float:right;'><input type='reset' id='' value=' Reset Form '></span></td>
	</tr>
</table>
</form>
<input type='hidden' id='changeYearIDURL' value='<?=$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajax/changeYearID.php"?>'>
<input type='hidden' id='changeClassURL' value='<?=$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajax/changeClassID.php"?>'>
<script>
$(document).ready(function(){
	var changeYearIDURL=$('#changeYearIDURL').val();
	var changeClassURL=$('#changeClassURL').val();
	
	$('#year').change(function(){
		var yearID=$(this).val();
			$.ajax
			({
				type: "POST",
				url: changeYearIDURL,
				data: { action: 'changeYearID', yearID:yearID},
				success: function(msg)
				{ 
					console.log(msg);
					$('#term').empty().append(msg);
				}
			});
	});
	$('#class').change(function(){
		var classID=$(this).val();
			$.ajax
			({
				type: "POST",
				url: changeClassURL,
				data: { action: 'changeClass', classID:classID},
				success: function(msg)
				{ 
					console.log(msg);
					$('#student_id').empty().append(msg);
				}
			});	
	});
});
</script>