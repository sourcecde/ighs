<?php 
@session_start() ;

//Set timezone from session variable
date_default_timezone_set($_SESSION[$guid]["timezone"]);

// if (isActionAccessible($guid, $connection2, "/modules/Staff/staff_view_details.php")==FALSE) {
// 	//Acess denied
// 	print "<div class='error'>" ;
// 		print _("You do not have access to this action.") ;
// 	print "</div>" ;
// }
//else {
	try{
		$sql="SELECT * FROM `gibbonstaff` WHERE gibbonStaffID = ".$_REQUEST['gibbonStaffID']."";
		$result=$connection2->prepare($sql);
		$result->execute();
		$rule=$result->fetch();

		$sql1="SELECT * FROM `staff_contract_detail` WHERE staff_id = ".$_REQUEST['gibbonStaffID']."";
		$result=$connection2->prepare($sql1);
		$result->execute();
		$contractList=$result->fetchAll();
		$countcontract = count($contractList);
		}
		catch(PDOException $e){
			echo $e;
		}
?>
 
<h3>Employee Contract View:</h3>
<div style="border:1px; padding:5px 10px;float:left;"><a href="index.php?q=/modules/Staff/manage_contract_employee.php">BACK</a></div><br><br><br>
<div align="left" style="border:1px; padding:5px 10px;float:left;"><b>Staff Id:</b> <?php echo $rule['gibbonStaffID'];?><br>
<b>Staff Name:</b> <?php echo $rule['preferredName'];?></div>


	<a href="#" id="add_contract_employee" style="border:1px; padding:5px 10px; background:#ff731b; color:white; float:right;"><b>+ Add</b></a>
	<br><br>
	<?php if($countcontract > 0){?>
	<table width="80%" cellpadding="0" cellspacing="0" id='myTable'>
	  <thead>
	  <tr>
		<th>Sl. No.</th>
		<th>Starting Date</th>
		<th>Ending Date</th>
		<th>Expired</th>
		<th>Action</th>
	  </tr>
	  </thead>
		<tbody>
		<?php 
		$i=1; 
		foreach($contractList as $countContract) {?>
			<tr>
				<td>
				<?php echo $i++; ?>
				</td>
				<td>
				<?php echo date("d/m/Y", strtotime($countContract['starting_date']));?>
				</td>
				<td>
				<?php echo date("d/m/Y", strtotime($countContract['ending_date']));?>
				</td>
				<td>
				<?php echo $countContract['expired']; ?>
				</td>
				<td>
					<a href='#' id='id_<?php echo $countContract["contract_id"]; ?>' class='edit_contract' >Edit</a> | <a href='javascript:void(0);' id='id_<?php echo $countContract["contract_id"]; ?>' class='delete_contract' >Delete</a>
				</td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
<?php }?>
<?php
//}
?>
 <script src="<?php echo $_SESSION[$guid]["absoluteURL"] ;?>/modules/Staff/js/jquery.dataTables.min.js"></script>
 <script>
	 $(document).ready(function(){
		$('#myTable').DataTable();
	});
 </script>
 <div id='hide_body'style='background-color :rgba(0,0,0, 0.7); width:100%; height:100%; position:fixed; left:0px; top:0px; z-index:100; display:none;'>
 </div>
 <div id='create_employee_contract' style="position:fixed; left:350px; top:250px; z-index:200; border:1px; padding:5px 10px; background-color :rgba(0,0,0, 0.6); color:white; width:300px; display:none;">
	<table class='blank' style='color:white;'>
		<tr>
			<td><b>Starting Date :</b></td>
			<td><input type='text' id='sdate'>
					<script type="text/javascript">
						$(function() {
							$( "#sdate" ).datepicker({ dateFormat: 'dd/mm/yy' });
						});
					</script>
			</td>
		</tr>
		<tr>
			<td><b>Ending Date :</b></td>
			<td><input type='text' id='edate'>
					<script type="text/javascript">
						$(function() {
							$( "#edate" ).datepicker({ dateFormat: 'dd/mm/yy' });
						});
					</script>
			</td>
		</tr>
		<tr>
			<td colspan='2'>
			<center>
			<input type='button' id='add_contract' value='ADD' style="border:1px; padding:5px; background:#ff731b; color:white;">
			<input type='button' class='close_contract' value='CLOSE' style="border:1px; padding:5px; background:#ff731b; color:white;">
			<input type='hidden' id='action'>
			<input type='hidden' value='<?php echo $rule['gibbonStaffID'];?>' id='staff_id'>
			<input type='hidden' id='contract_id'>
			</center>
			</td>
		</tr>
		<tr>
	</table>
 </div>
