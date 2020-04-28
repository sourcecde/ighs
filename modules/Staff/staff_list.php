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

	$staff_type ='';
	$doj ='';
	$dob ='';
	$aadhar = '';

	if(isset($_REQUEST['staff_type'])){

		$staff_type = $_REQUEST['staff_type'];
		// $doj = @$_REQUEST['doj'];
		// $dob = @$_REQUEST['dob'];
		// $aadhar = @$_REQUEST['aadhar'];
    }

?>
<h3>Staff List: </h3>
	<form  id="form_payment_option" action='<?php print $_SESSION[$guid]["absoluteURL"]?>/index.php'>
	<input type="hidden" name="q" value="/modules/<?php print $_SESSION[$guid]["module"] ?>/staff_list.php">
	<table width="80%" cellpadding="0" cellspacing="0" align='center'>
		<tr>
			<td><select name='staff_type' required><option value=''> Select Group </option>
				<option value='1' <?php echo $staff_type==1?'selected':''; ?>>Sr. Section</option>
				<option value='2' <?php echo $staff_type==2?'selected':''; ?>>Jr. Section</option>
				<option value='3' <?php echo $staff_type==3?'selected':''; ?>>All</option>
				</select></td>
			<td>
				<input type="checkbox" id="doj" name="doj" value="doj" <?=(isset($_REQUEST['doj'])?' checked':'')?>>
				<label for="doj">Date of Joining</label><br>
			</td>
			<td>
				<input type="checkbox" id="dob" name="dob" value="dob" <?=(isset($_REQUEST['dob'])?' checked':'')?>>
				<label for="dob">Date of Birth</label><br>
			</td>

			<td>
				<input type="checkbox" id="aadhar" name="aadhar" value="aadhar" <?=(isset($_REQUEST['aadhar'])?' checked':'')?>>
				<label for="aadhar">Aadhar Number</label><br>
			</td>

			<td><input  type='submit' value='Submit' name='print'></td>
			<td><input  type='button' value='Print' name='print-btn' class="print-btn"></td>
		</tr>
	</table>
	</form>
<?php

	if(isset($_REQUEST['print']))
	{
		if($staff_type != 3)
		{
			$sql="SELECT * FROM gibbonstaff 
						where gibbonstaff.sec_code = $staff_type";
		}else{
			$sql="SELECT * FROM gibbonstaff";
		}
			$result=$connection2->prepare($sql);
			$result->execute();
			$staff_list=$result->fetchAll();		
		?>
	<div id="print_page">
		<table width="100%" cellpadding="2" cellspacing="0" border="0">
				  <tr>
					<th align="center" style="padding-top:5px; font-family:Arial, Helvetica, sans-serif; font-size:25px; color:#000000;">Indra Gopal High School</th>
				  </tr>
				  <tr>
					<td align="center" style="font-family:Arial, Helvetica, sans-serif; font-size:20px; color:#000000;">Jheel Bagan, P.O. Ghuni, Hatiara, Kolkata - 700 157</td>
				  </tr>
				  <tr>
					<td align="center" style="font-family:Arial, Helvetica, sans-serif; font-size:20px; color:#000000;"> </td>
				  </tr>			  
				   <tr>
				   	<td align="center" colspan=15 style="font-family:Arial, Helvetica, sans-serif; font-size:20px; color:#000000;"> Group: <?php if($staff_type == 1){echo "Sr. Section";}if($staff_type == 2){echo "Jr. Section";}if($staff_type == 3){echo "All";}?></td>
				  </tr>
				  <tr>
				  </table>
	<table width='100%' cellpadding='5px' style='border: 1px solid black; border-collapse: collapse;'>
				  	<tr style='border: 1px solid black; border-collapse: collapse;'>
				  		<th>Staff ID</th>
				  		<th>Name</th>
				  		<th>Designation</th>
				  		<th>Phone</th>
				  	<?php if(isset($_REQUEST['doj'])){?>
				  		<th>DOJ</th>
				  	<?php }if(isset($_REQUEST['dob'])){?>
				  		<th>DOB</th>
				  	<?php }if(isset($_REQUEST['aadhar'])){?>
				  		<th>Aadhar</th>
				  	<?php }?>
				  	</tr>
				  	<?php foreach($staff_list as $staffList){?>
				  	<tr style='border: 1px solid black; border-collapse: collapse;'>
				  		<td style='border: 1px solid black; border-collapse: collapse;'><?php echo $staffList['gibbonStaffID'];?></td>
				  		<td style='border: 1px solid black; border-collapse: collapse;'><?php echo $staffList['preferredName'];?></td>
				  		<td style='border: 1px solid black; border-collapse: collapse;'><?php echo $staffList['jobTitle'];?></td>
				  		<td style='border: 1px solid black; border-collapse: collapse;'><?php echo $staffList['phone1'];?></td>
				  		<?php if(isset($_REQUEST['doj'])){?>
				  		<td style='border: 1px solid black; border-collapse: collapse;'><?php echo $staffList['dateStart'];?></td>
				  		<?php }if(isset($_REQUEST['dob'])){?>
				  		<td style='border: 1px solid black; border-collapse: collapse;'><?php echo $staffList['dob'];?></td>
				  		<?php }if(isset($_REQUEST['aadhar'])){?>
				  		<td style='border: 1px solid black; border-collapse: collapse;'><?php echo $staffList['nationalIDCardNumber'];?></td>
				  	<?php }?>
				  	</tr>
				  <?php }?>
				  </table>
	</div>
<?php }?>
<script type="text/javascript">

	 $('.print-btn').click(function() {
	 	var w=window.open("","","height=600,width=700,status=yes,toolbar=no,menubar=no,location=no");
		var html=$('#print_page').html();
		$(w.document.body).html(html);
		w.print();
});
</script>