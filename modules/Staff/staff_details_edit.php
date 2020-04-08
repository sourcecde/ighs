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
	/*	$sql="SELECT `preferredName`, `gender`, `dob`, `email`, `image_240`, `address1`, `address1District`, `phone1`, `dateStart` ,`dateEnd`, 
		`gibbonstaff`.`type`, `gibbonstaff`.`jobTitle`, `gibbonstaff`.`bank_ac`, `gibbonstaff`.`payment_mode`,`gibbonstaff`.qualifications,
		`gibbonstaff`.`pf_no`, `gibbonstaff`.`pf_active`, `gibbonstaff`.`esi_no`, `gibbonstaff`.`uan_no`,`gibbonstaff`.`esi_active`, `gibbonstaff`.gibbonPersonID,`gibbonstaff`.priority,`gibbonstaff`.`guardian`,`gibbonstaff`.`relationship`,`gibbonstaff`.`reasonOfLeaving`  
		FROM `gibbonperson`
		LEFT JOIN `gibbonstaff` ON `gibbonstaff`.`gibbonPersonID`=`gibbonperson`.`gibbonPersonID`
				WHERE `gibbonstaff`.gibbonStaffID=".$_REQUEST['gibbonStaffID'];
	*/
	
	$sql="SELECT gibbonstaff.*,staff_type.staff_type from `gibbonstaff` 
	left Join staff_type on staff_type.id=gibbonstaff.staff_type
	WHERE `gibbonstaff`.gibbonStaffID=".$_REQUEST['gibbonStaffID'];
	
		$result=$connection2->prepare($sql);
		$result->execute();
		$data=$result->fetch();
		}
		catch(PDOException $e){
			echo $e;
		}
		
		
	 /* $saff_type_sql="SELECT * from `staff_type` WHERE `staff_type`.gibbonStaffID=".$_REQUEST['gibbonStaffID'];
		$result1=$connection2->prepare($saff_type_sql);
		$result1->execute();
		$staff_type=$result1->fetch();
		
		$saff_section_sql="SELECT * from `staff_section` WHERE `staff_section`.gibbonStaffID=".$_REQUEST['gibbonStaffID'];
		$result2=$connection2->prepare($saff_section_sql);
		$result2->execute();
		$staff_section=$result2->fetch();
		
		
		$emp_type_sql="SELECT * from `emp_type_table` WHERE `emp_type_table`.gibbonStaffID=".$_REQUEST['gibbonStaffID'];
		$result3=$connection2->prepare($emp_type_sql);
		$result3->execute();
		$emp_type=$result3->fetch();
	*/

?>	
	<br>
	<?php if(isset($_REQUEST['status'])){ 
	?>
	<div id='status'><center><h4>
	<?php 
		if($_REQUEST['status']=='success')
			echo "Edited Successfully!!!";
		else if($_REQUEST['status']=='imagesize')
			echo "Only 240px*320px image size is allowed!";
	?>
	</h4></center></div>
	<script>
	$('#status').delay(2000).fadeOut('slow');
	location.replace();
	</script>

	<br>
	<?php } ?>

	<form  method='POST' action='<?php print $_SESSION[$guid]["absoluteURL"]?>/modules/<?php print $_SESSION[$guid]["module"] ?>/process_staff_details_edit.php' enctype='multipart/form-data'>
			<input type='hidden' name='gibbonPersonID' value='<?php echo $data['gibbonPersonID']; ?>'> 
			<input type='hidden' name='gibbonStaffID' value='<?php echo $_REQUEST['gibbonStaffID']; ?>'> 
	<table width='80%'>
	<tr><th>Edit Details:</th></tr>
	<tr><td><b>Name: </b><input name='p_name' type='text' style='width:250px;' value='<?php echo $data['preferredName']; ?>'></td></tr>
	<tr><td><b>Staff Type: </b><select name='type'  style='width:250px;'>
									<option <?php echo $data['staff_type']=='Senior Management'?'selected':''; ?>>Senior Management</option>
									<option <?php echo $data['staff_type']=='Non-Teaching'?'selected':''; ?>>Non-Teaching</option>
									<option <?php echo $data['staff_type']=='Teaching'?'selected':''; ?>>Teaching</option>
									<option <?php echo $data['staff_type']=='Class IV Staff'?'selected':''; ?>>Class IV Staff</option>
									<option <?php echo $data['staff_type']=='Clerk'?'selected':''; ?>>Clerk</option>
									<option <?php echo $data['staff_type']=='Class V Staff'?'selected':''; ?>>Class V Staff</option>
								</select></td></tr>
	
	<tr><td><b>Staff Section: </b><select name='staff_section'  style='width:250px;'>
									<option <?php //echo $staff_section['section']=='Senior Section'?'selected':''; ?>>Senior Section</option>
									<option <?php //echo $staff_section['section']=='Junior Section'?'selected':''; ?>>Junior Section</option>
								</select></td></tr>
								
	<tr><td><b>Emp Type: </b><select name='emp_type' id="emp_type"  style='width:250px;'>
									<option <?php //echo $emp_type['emp_type']=='Contract'?'selected':''; ?>>Contract</option>
									<option <?php //echo $emp_type['emp_type']=='Permanent'?'selected':''; ?>>Permanent</option>
								</select></td></tr>							
							
	<tr><td><b>Designation: </b><input name='job_title' type='text' style='width:250px;' value='<?php echo $data['jobTitle']; ?>'></td></tr>
	<tr><td><b>Priority: </b><input name='priority' type='text' style='width:250px;' value='<?php echo $data['priority']; ?>'></td></tr>
	<tr><td><b>Gender: </b><select name='gender'  style='width:250px;'>
								<option value='F'<?php echo $data['gender']=='F'?'Selected':''; ?>>Female</option>
								<option value='M' <?php echo $data['gender']=='M'?'Selected':''; ?>>Male</option>
							</select></td></tr>
	<tr><td><b>Date of Birth: </b><input name='dob' id='dob' type='text' style='width:250px;' value='<?php echo date('d/m/Y',strtotime($data['dob'])); ?>'></td></tr>
	<tr><td><b>Qualification: </b><input name='qualification' type='text' style='width:250px;' value='<?php echo $data['qualifications']; ?>'></td></tr>
	<!--<tr><td><b>Address: </b><input name='address' type='text' style='width:250px;' value='<?php //echo $data['address1']; ?>'></td></tr>-->
	<tr><td><b>Address: </b><textarea name='address'  row="3" style='width:250px; height:100px' ><?php echo $data['address1']; ?></textarea></td></tr>
	<tr><td><b>Email ID: </b><input name='email' type='text' style='width:250px;' value='<?php echo $data['email']; ?>'></td></tr>
	<tr><td><b>Contact No: </b><input name='phone' type='text' style='width:250px;'value='<?php echo $data['phone1']; ?>'></td></tr>
	<tr><td><b>Bank a/c: </b><input name='bank_ac' type='text' style='width:250px;' value='<?php echo $data['bank_ac']; ?>'></td></tr>
	<tr><td><b>PF No: </b><input name='pf_no' type='text' style='width:250px;' value='<?php echo $data['pf_no']; ?>'></td></tr>
	<tr><td><b>PF Date: </b><input name='pf_date' type='text' style='width:248px;height:26px;margin-left: 364px' value='<?php echo $data['pf_date']; ?>'></td></tr>
	<tr><td><b>UAN No: </b><input name='uan_no' type='text' style='width:250px;' maxlength=12 value='<?php echo $data['uan_no']; ?>'></td></tr>
	<tr><td><b>ESI No: </b><input name='esi_no' type='text' style='width:250px;' value='<?php echo $data['esi_no']; ?>'></td></tr>
	<tr><td><b>Mode Of Payment: </b><select name='payment_mode'  style='width:250px;'>
										<option value=''> Select </option>
										<option <?php echo $data['payment_mode']=='Cash'?'selected':''; ?>>Cash</option>
										<option <?php echo $data['payment_mode']=='Cheque'?'selected':''; ?>>Cheque</option>
										</select></td></tr>
	<tr><td><b>Date of Joining: </b><input name='dateStart' id='dateStart' type='text' style='width:250px;' value='<?php echo $data['dateStart']>0?date("d/m/Y", strtotime($data['dateStart'])):''; ?>'></td></tr>
	<tr><td><b>Date of Leaving: </b><input name='dateEnd' id='dateEnd' type='text' style='width:250px;' value='<?php echo $data['dateEnd']>0?date("d/m/Y", strtotime($data['dateEnd'])):''; ?>'></td></tr>
	<tr><td><b>Gurdian Name: </b><input name='guardian' id='guardian' type='text' style='width:250px;' value='<?=$data['guardian']?>'></td></tr>
	<tr><td><b>Relationship with Staff: </b><select name='relationship' id='relationship' style='width:250px;'><option value='F' <?=$data['relationship']=='F'?'selected':'';?>>Father</option><option value='S' <?=$data['relationship']=='S'?'selected':'';?>>Husband</option></select></td></tr>
	<tr><td><b>Reason Of Leaving: </b>	<select name='reasonOL' id='reasonOL' style='width:250px;'>
											<option value=''>Select</option>
											<option value='C' <?=$data['reasonOfLeaving']=='C'?'selected':'';?>>Cessation</option>
											<option value='S' <?=$data['reasonOfLeaving']=='S'?'selected':'';?>>Superannuation</option>
											<option value='R' <?=$data['reasonOfLeaving']=='R'?'selected':'';?>>Retirement</option>
											<option value='D' <?=$data['reasonOfLeaving']=='D'?'selected':'';?>>Death in Service</option>
											<option value='P' <?=$data['reasonOfLeaving']=='P'?'selected':'';?>>Permanent Disablement</option>
										</select></td></tr>
	<tr><td><b>Image: </b><small>240px*320px</small>
	    <div class=''>
					<?php if($data['image_240']!='') {?>
					<img src="<?php echo $data['image_240'];?>" alt="Staff Photo" width="50" height="45">
					<?php }
					else{
					?>
					<img src="themes/Default/img/anonymous_240.jpg" alt="Staff Photo" width="50" height="45">
					<?php }?>
		</div>
	
	
	<input type="file" name="image" id="image" multiple accept='image/*'  style="margin-top: -39px;">
		<?php if($data['image_240']!='') {?><br><small>Current Attachmment: <a href='<?php echo $data['image_240'] ?>'>Image</a> </small><?php } ?></td></tr>
	<tr><td><center><input  type='submit' name='submit' value='Update'></center></td></tr>
	</table>
	</form>
<?php	
}
?>
<script type="text/javascript">
	$(function() {
		$( "#dateStart" ).datepicker({ dateFormat: 'dd/mm/yy' });
		$( "#dateEnd" ).datepicker({ dateFormat: 'dd/mm/yy' });
		$( "#dob" ).datepicker({ dateFormat: 'dd/mm/yy' });
		$( "#pf_date" ).datepicker({ dateFormat: 'dd/mm/yy' }); 
	});

</script>
