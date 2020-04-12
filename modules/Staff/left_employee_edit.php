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

	<form  method='POST' action='<?php print $_SESSION[$guid]["absoluteURL"]?>/modules/<?php print $_SESSION[$guid]["module"] ?>/process_left_employee_details.php' enctype='multipart/form-data'>
			<input type='hidden' name='gibbonPersonID' value='<?php echo $data['gibbonPersonID']; ?>'> 
			<input type='hidden' name='gibbonStaffID' value='<?php echo $_REQUEST['gibbonStaffID']; ?>'> 
	<table width='80%'>
	<tr><th>Edit Details:</th></tr>
	<tr><td><b>Date of Leaving: </b><input name='dateEnd' id='dateEnd' type='text' style='width:250px;' value='<?php echo $data['dateEnd']>0?date("d/m/Y", strtotime($data['dateEnd'])):''; ?>'></td></tr>
	<tr><td><b>Reason Of Leaving: </b>	<select name='reasonOL' id='reasonOL' style='width:250px;'>
											<option value=''>Select</option>
											<option value='C' <?=$data['reasonOfLeaving']=='C'?'selected':'';?>>Cessation</option>
											<option value='S' <?=$data['reasonOfLeaving']=='S'?'selected':'';?>>Superannuation</option>
											<option value='R' <?=$data['reasonOfLeaving']=='R'?'selected':'';?>>Retirement</option>
											<option value='D' <?=$data['reasonOfLeaving']=='D'?'selected':'';?>>Death in Service</option>
											<option value='P' <?=$data['reasonOfLeaving']=='P'?'selected':'';?>>Permanent Disablement</option>
										</select></td></tr>
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
