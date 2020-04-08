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
		$sql="SELECT `gibbonStaffID`,gibbonstaff.`preferredName` FROM `gibbonstaff` 
			LEFT JOIN `gibbonperson` ON `gibbonstaff`.`gibbonPersonID`=`gibbonperson`.`gibbonPersonID` 
			WHERE gibbonstaff.`dateEnd` IS NULL";
		$result=$connection2->prepare($sql);
		$result->execute();
		$staffD=$result->fetchAll();
		}
		catch(PDOException $e){
			echo $e;
		}
		try{
		$sql="SELECT `lakshyasalaryadvance`.`staffID`,gibbonstaff.`preferredName` FROM `lakshyasalaryadvance` 
			LEFT JOIN `gibbonstaff` ON `lakshyasalaryadvance`.`staffID`=`gibbonstaff`.`gibbonStaffID` 
			LEFT JOIN `gibbonperson` ON `gibbonstaff`.`gibbonPersonID`=`gibbonperson`.`gibbonPersonID` 
			WHERE gibbonstaff.`dateEnd` IS NULL AND `isPaid`='N'";
		$result=$connection2->prepare($sql);
		$result->execute();
		$staffDR=$result->fetchAll();
		}
		catch(PDOException $e){
			echo $e;
		}
		try{
		$sql="SELECT `gibbonSchoolYearID`,`name`,`status` FROM `gibbonschoolyear`";
		$result=$connection2->prepare($sql);
		$result->execute();
		$schoolYears=$result->fetchAll();
		}
		catch(PDOException $e){
			echo $e;
		}
?>
	<h1>Add Advance :</h1>
	<table width='100%'>
	<tr>
		<td>
			Staff : <select id='staffID'>
						<option value=''>Select Staff </option>
				<?php
					foreach($staffD as $s){
						echo "<option value='{$s['gibbonStaffID']}'>{$s['preferredName']}</option>";
					}
				?>
					</select>
		</td>
		<td>
			Amount : <input type='text' id='amount' style='width:100px;'>
		</td>
		<td>
			Date : <input type='text' id='date' value='<?=date('d/m/Y')?>' style='width:100px;'>
		</td>
		<td>
			No Of Month : <input type='text' id='nEMI' value='10' style='width:50px;'>
		</td>
		<td>
			Year : 
			<select id='schoolYearID'>
			<?php
			foreach($schoolYears as $y){
				$s=$y['status']=='Current';
				echo "<option value='{$y['gibbonSchoolYearID']}' $s>{$y['name']}</option>";
			}
			?>
			</select>
		</td>
		<td>
			<input type='submit' id='addAdvance' value='Add'>
		</td>
	</tr>
	</table>
	<br><hr style='border: 1px solid #7030a0'>
	<h1>Return Advance :</h1>
	<table width='100%'>
	<tr>
		<td>
			Staff : <select id='RstaffID'>
						<option value=''>Select Staff </option>
				<?php
					foreach($staffDR as $s){
						echo "<option value='{$s['staffID']}'>{$s['preferredName']}</option>";
					}
				?>
					</select>
		</td>
		<td>
			Amount : <input type='text' id='Ramount' style='width:100px;'>
		</td>
		<td>
			Date : <input type='text' id='Rdate' value='<?=date('d/m/Y')?>' style='width:100px;'>
		</td>
		<td>
			Year : 
			<select id='RschoolYearID'>
			<?php
			foreach($schoolYears as $y){
				$s=$y['status']=='Current';
				echo "<option value='{$y['gibbonSchoolYearID']}' $s>{$y['name']}</option>";
			}
			?>
			</select>
		</td>
		<td>
			<input type='submit' id='returnAdvance' value='Return'>
		</td>
	</tr>
	</table>
	<input type='hidden' id='posturl' value='<?php print $_SESSION[$guid]["absoluteURL"]?>/modules/<?php print $_SESSION[$guid]["module"] ?>/ajax_advance.php'>
<?php
}
?>
<script>
$(document).ready(function(){
	var posturl=$('#posturl').val();
	$('#date,#Rdate').datepicker({ dateFormat: 'dd/mm/yy' });
	$('#addAdvance').click(function(){
		var staffID=$('#staffID').val();
		var amount=$('#amount').val();
		var date=$('#date').val();
		var nEMI=$('#nEMI').val();
		if(staffID==''){
			alert("Select a staff!!");
			$('#staffID').focus();
			return;
		}
		if(amount==''){
			alert("Enter amount!!");
			$('#amount').focus();
			return;
		}
		var data={};
		data['staffID']=staffID;
		data['amount']=amount;
		data['date']=date;
		data['nEMI']=nEMI;
		data['schoolYearID']=$('#schoolYearID').val();
		$.ajax
 		({
 			type: "POST",
 			url: posturl,
 			data: {action:'addAdvance',data:data},
 			success: function(msg)
 			{
				alert(msg);
				location.reload();
 			}
 		});
	});
	$('#returnAdvance').click(function(){
		var staffID=$('#RstaffID').val();
		var amount=$('#Ramount').val();
		var date=$('#Rdate').val();
		if(staffID==''){
			alert("Select a staff!!");
			$('#RstaffID').focus();
			return;
		}
		if(amount==''){
			alert("Enter amount!!");
			$('#Ramount').focus();
			return;
		}
		var data={};
		data['staffID']=staffID;
		data['amount']=amount;
		data['date']=date;
		data['schoolYearID']=$('#schoolYearID').val();
		$.ajax
 		({
 			type: "POST",
 			url: posturl,
 			data: {action:'returnAdvance',data:data},
 			success: function(msg)
 			{
				alert(msg);
				location.reload();
 			}
 		});
	});
});
</script>