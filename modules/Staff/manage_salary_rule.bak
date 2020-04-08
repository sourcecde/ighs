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
		$sql="SELECT * FROM `lakshyasalaryrule`";
		$result=$connection2->prepare($sql);
		$result->execute();
		$rule=$result->fetchAll();
		}
		catch(PDOException $e){
			echo $e;
		}
?>
 
<h3>Manage Salary Rule:</h3>
	<a href="#" id="add_salary_rule" style="border:1px; padding:5px 10px; background:#ff731b; color:white; float:right;"><b>+ Add Rule</b></a>
	<br><br>
	<table width="80%" cellpadding="0" cellspacing="0" id='myTable'>
	  <thead>
	  <tr>
		<th>Rule ID</th>
		<th>Caption</th>
		<th>Impact</th>
		<th>Active</th>
		<th>Action</th>
	  </tr>
	  </thead>
		<tbody>
		<?php foreach($rule as $a) {?>
			<tr>
				<td>
				<?php echo $a['rule_id']; ?>
				</td>
				<td>
				<?php echo $a['caption']; ?>
				</td>
				<td>
				<?php echo $a['impact']=='+'?"Positive":"Negative"; ?>
				</td>
				<td>
				<?php echo $a['active']==1?"Yes":"No"; ?>
				</td>
				<td>
					<a href='#' id='id_<?php echo $a["rule_id"]; ?>' class='edit_rule' >Edit</a> | <a href='#' id='id_<?php echo $a["rule_id"]; ?>' class='delete_rule' >Delete</a>
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
	});
 </script>
 <div id='hide_body'style='background-color :rgba(0,0,0, 0.7); width:100%; height:100%; position:fixed; left:0px; top:0px; z-index:100; display:none;'>
 </div>
 <div id='create_salary_rule' style="position:fixed; left:500px; top:250px; z-index:200; border:1px; padding:5px 10px; background-color :rgba(0,0,0, 0.6); color:white; width:250px; display:none;">
	<table class='blank' style='color:white;'>
		<tr>
			<td><b>Caption :</b></td>
			<td><input type='text' id='caption'></td>
		</tr>
		<tr>
			<td><b>Impact :</b></td>
			<td>
				<select id='impact'>
					<option value=''> -SELECT- </option>
					<option value='+'> Positive </option>
					<option value='-'> Negative </option>
				</select>
			</td>
		</tr>
		<tr>
			<td><b>Active :</b></td>
			<td>
				<select id='active'>
					<option value='1'> Yes </option>
					<option value='0'> No </option>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan='2'>
			<center>
			<input type='button' id='add_rule' value='ADD' style="border:1px; padding:5px; background:#ff731b; color:white;">
			<input type='button' class='close_rule' value='CLOSE' style="border:1px; padding:5px; background:#ff731b; color:white;">
			<input type='hidden' id='action'>
			<input type='hidden' id='rule_id'>
			</center>
			</td>
		</tr>
		<tr>
	</table>
 </div>