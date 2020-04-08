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
		$sql="SELECT `gibbonStaffID`,`preferredName` FROM `gibbonstaff` 
			LEFT JOIN `gibbonperson` ON `gibbonstaff`.`gibbonPersonID`=`gibbonperson`.`gibbonPersonID` 
			WHERE `dateEnd` IS NULL";
		$result=$connection2->prepare($sql);
		$result->execute();
		$staffD=$result->fetchAll();
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
			Amount : <input type='text' id='amount'>
		</td>
		<td>
			Date : <input type='text' id='date'>
		</td>
		<td>
			<input type='submit' id='addAdvance' value='Add'>
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
	$('#date').datepicker({ dateFormat: 'dd/mm/yy' });
	$('#addAdvance').click(function(){
		var staffID=$('#staffID').val();
		var amount=$('#amount').val();
		var date=$('#date').val();
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
		if(date==''){
			alert("Enter date!!");
			$('#date').focus();
			return;
		}
		var data={};
		data['staffID']=staffID;
		data['amount']=amount;
		data['date']=date;
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
});
</script>