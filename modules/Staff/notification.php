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
	try{
			if(isset($_REQUEST['days']) && $_REQUEST['days']!='') 
			{
				// Contract End Data //
				$sql_contract_end="SELECT * FROM staff_contract_detail LEFT JOIN gibbonstaff on staff_contract_detail.staff_id=gibbonstaff.gibbonStaffID WHERE `ending_date` >= NOW() AND TO_DAYS( `ending_date` ) - TO_DAYS( NOW() ) < ".$_REQUEST['days']."";
				$result_contract_end=$connection2->prepare($sql_contract_end);
				$result_contract_end->execute();
				$contract=$result_contract_end->fetchAll();

				// Retirement Data //
				$sql_retire="SELECT * FROM GIBBONSTAFF WHERE `date_of_returment` >= NOW() AND TO_DAYS( `date_of_returment` ) - TO_DAYS( NOW() ) < ".$_REQUEST['days']."";
				$result_retire=$connection2->prepare($sql_retire);
				$result_retire->execute();
				$dot=$result_retire->fetchAll();
			}
			else
			{
				// Contract End Data //
				$sql_contract_end="SELECT * FROM staff_contract_detail LEFT JOIN gibbonstaff on staff_contract_detail.staff_id=gibbonstaff.gibbonStaffID WHERE `ending_date` >= NOW() AND TO_DAYS( `ending_date` ) - TO_DAYS( NOW() ) < 30";
				$result_contract_end=$connection2->prepare($sql_contract_end);
				$result_contract_end->execute();
				$contract=$result_contract_end->fetchAll();

				// Retirement Data //
				$sql_retire="SELECT * FROM GIBBONSTAFF WHERE `date_of_returment` >= NOW() AND TO_DAYS( `date_of_returment` ) - TO_DAYS( NOW() ) < 30";
				$result_retire=$connection2->prepare($sql_retire);
				$result_retire->execute();
				$dot=$result_retire->fetchAll();
			}
		}
		catch(PDOException $e){
			echo $e;
		}
?>
 
<h3>Notification Alert:</h3>
<form name="notification" id="notification" method="POST">
			<select name='days' id='days' style="float:left;">
				<option value=''> Select Days </option>
				<option value='30'>30 Days</option>
				<option value='60'>60 Days</option>
				<option value='90'>90 Days</option>
			</select>
	<input type='submit' value='Submit' name="no_of_days" id="no_of_days"  style="float:left;">
</form>
	<br><br>

	<table width="80%" cellpadding="0" cellspacing="0" id='myTablecontract'>
	  <thead>
	  <tr>
	  	<th>Sl. No.</th>
		<th>Staff ID</th>
		<th>Staff Name</th>
		<th>End Date of Contract</th>
	  </tr>
	  </thead>
		<tbody>
		<?php 
		$i=1;
		foreach($contract as $contractEnd) {?>
			<tr>
				<td>
				<?php echo $i++; ?>
				</td>
				<td>
				<?php echo $contractEnd['contract_id']; ?>
				</td>
				<td>
				<?php echo $contractEnd['preferredName']; ?>
				</td>
				<td>
				<?php echo date("d/m/Y", strtotime($contractEnd['ending_date']));?>
				</td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
	
	<table width="80%" cellpadding="0" cellspacing="0" id='myTable'>
	  <thead>
	  <tr>
	  	<th>Sl. No.</th>
		<th>Staff ID</th>
		<th>Staff Name</th>
		<th>Date of Retirement</th>
	  </tr>
	  </thead>
		<tbody>
		<?php 
		$i=1;
		foreach($dot as $retirement) {?>
			<tr>
				<td>
				<?php echo $i++; ?>
				</td>
				<td>
				<?php echo $retirement['gibbonStaffID']; ?>
				</td>
				<td>
				<?php echo $retirement['preferredName']; ?>
				</td>
				<td>
				<?php echo date("d/m/Y", strtotime($retirement['date_of_returment']));?>
				</td>
			</tr>
		<?php } ?>
		</tbody>
	</table>

<?php
}
?>
 <script src="<?php echo $_SESSION[$guid]["absoluteURL"] ;?>/modules/Staff/js/jquery.dataTables.min.js"></script>
 <script>
	 $(document).ready(function(){
		$('#myTable').DataTable();
		$('#myTablecontract').DataTable();
	});
 </script>