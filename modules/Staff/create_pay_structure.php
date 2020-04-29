<script>
/*tbody {
 width: 200px;
 height: 400px;
 overflow: auto;
}
*/
</script>

<?php 
@session_start() ;

//Set timezone from session variable
date_default_timezone_set($_SESSION[$guid]["timezone"]);

if (isActionAccessible($guid, $connection2, "/modules/Staff/staff_view_details.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
	/*
	try{
		$sql="SELECT * FROM `lakshyasalaryrule` where active=1";
		$result=$connection2->prepare($sql);
		$result->execute();
		$rule=$result->fetchAll();
		}
		catch(PDOException $e){
			echo $e;
		}
		try{
		$sql="SELECT `gibbonStaffID`,priority,preferredName FROM `gibbonstaff`";
		$sql.=" WHERE (dateEnd IS NULL OR dateEnd>'".date('Y-m-d')."') and status='Full"; 
		$sql.=" ORDER BY gibbonstaff.priority";
		$result1=$connection2->prepare($sql);
		$result1->execute();
		$staff=$result1->fetchAll();
		}
		catch(PDOException $e){
			echo $e;
		}
	*/
		try{
		$sql="SELECT `gibbonSchoolYearID`, `name` FROM `gibbonschoolyear` ORDER BY name";
		$result2=$connection2->prepare($sql);
		$result2->execute();
		$year=$result2->fetchAll();
		}
		catch(PDOException $e){
			echo $e;
		}
		if(!isset($_REQUEST['create_p_s'])){
?>	
<h3>Create Pay Structure: </h3>
	<table width="60%" cellpadding="0" cellspacing="0">
	<tr>
		<td>
			<select name='month_f' id='month_f'>
				<option value=''> Select Month </option>
				<option value='4'>April</option>
				<option value='5'>May</option>
				<option value='6'>June</option>
				<option value='7'>July</option>
				<option value='8'>August</option>
				<option value='9'>September</option>
				<option value='10'>October</option>
				<option value='11'>November</option>
				<option value='12'>December</option>
				<option value='1'>January</option>
				<option value='2'>February</option>
				<option value='3'>March</option>
			</select>
		</td>
		<td>
			<select name='year_f' id='year_f'>
				<option value=''> Select Year </option>
				<?php foreach($year as $y){
					print "<option value='".$y['gibbonSchoolYearID']."'>".$y['name']."</option>";
				}?>
			</select>
		</td>
		<td>
			<input type='button' value='Select' id="select_month_year"  style="border:1px; padding:5px 10px; background:#ff731b; color:white; float:right;">
		</td>
	</tr>
	</table>
	<form method='POST' action='<?php print $_SESSION[$guid]["absoluteURL"]?>/modules/<?php print $_SESSION[$guid]["module"] ?>/process_pay_structure.php'>
	<div id='create_panel' style='display:none'>
	</div>
				<input type='hidden' name='month_s' id='month_s'>
				<input type='hidden' name='year_s' id='year_s'>
				<input type='hidden' name='duplicate_entry' id='duplicate_entry' value='0'>
	</form>
	
<?php 
}

} ?>