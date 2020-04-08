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
		$sql="SELECT * FROM `lakshyastaffattendancerule` WHERE 1";
		$result=$connection2->prepare($sql);
		$result->execute();
		$rule=$result->fetchAll();
		}
		catch(PDOException $e){
			echo $e;
		}
?>
	<h3>Manage Attendance Leave Rule:</h3>
	<a href="#" id="add_leave_rule" style="border:1px; padding:5px 10px; background:#ff731b; color:white; float:right;"><b>+ Add Rule</b></a>
	<br><br>
	<table width="60%" cellpadding="0" cellspacing="0" id='myTable'>
	  <thead>
	  <tr>
		<th>Short Name</th>
		<th>Caption</th>
		<th>Action</th>
	  </tr>
	  </thead>
	  <tbody>
		<?php foreach($rule as $a) {?>
			<tr>
				<td><span id='<?=$a["rule_id"]; ?>_sn'>
				<?php echo $a['short_name']; ?></span>
				</td>
				<td><span id='<?=$a["rule_id"]; ?>_caption'>
				<?php echo $a['caption']; ?></span>
				</td>
				<td>
					<a href='#' id='editid_<?php echo $a["rule_id"]; ?>' class='edit_leave_rule' >Edit</a> | <a href='#' id='delid_<?php echo $a["rule_id"]; ?>' class='delete_leave_rule' >Delete</a>
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
 <div id='create_leave_rule' class='edit_panel' style="position:fixed; left:500px; top:250px; z-index:200; border:1px; padding:5px 10px; background-color :rgba(0,0,0, 0.6); color:white; width:300px; display:none;">
	<table class='blank' style='color:white;'>
		<tr>
			<td><b>Short Name:</b><br><small>(2 character only)</small></td>
			<td><input type='text' id='short_name'></td>
		</tr>
		<tr>
			<td><b>Caption :</b></td>
			<td><input type='text' id='caption'></td>
		</tr>
		<tr>
			<td colspan='2'>
			<center>
			<input type='button' id='submit_rule' value='ADD' style="border:1px; padding:5px; background:#ff731b; color:white;">
			<input type='button' class='close_panel' value='CLOSE' style="border:1px; padding:5px; background:#ff731b; color:white;">
			<input type='hidden' id='action'>
			<input type='hidden' id='rule_id'>
			</center>
			</td>
		</tr>
		<tr>
	</table>
 </div>