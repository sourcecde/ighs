<?php
@session_start() ;
$gibbonPersonID=NULL ;
if (isset($_SESSION[$guid]["gibbonPersonID"])) {
	$gibbonPersonID=$_SESSION[$guid]["gibbonPersonID"] ;
}
$sql="SELECT * from gibbonschoolyear ORDER BY gibbonSchoolYearID DESC";
$result=$connection2->prepare($sql);
$result->execute();
$yearresult=$result->fetchAll();

$sql="SELECT * from gibbonyeargroup";
$result=$connection2->prepare($sql);
$result->execute();
$schoolyearresult=$result->fetchAll();
?>
<h3>Appliaction Fee Reciept:</h3>
<table width="100%" cellpadding="0" cellspacing="0">
<form method='POST' action=''>
	<tr>
		<td><b>Student Name:</b></td>
		<td><input type='text' id='s_name' name='s_name' style="width:60%; text-align: center;" required></td>
	</tr>
	<tr>
		<td><b>Gurdian Name:</b></td>
		<td><input type='text' id='g_name' name='g_name' style="width:60%; text-align: center;" required></td>
	</tr>
	<tr>
		<td><b>Class:</b></td>
		<td>
			<select name="class" id="class" style="width:40%;" required>
				<option value=''>Select Class</option>
				<?php foreach ($schoolyearresult as $value) { ?>
				<option><?php echo $value['name']?></option>
				<?php } ?>
			</select>
		</td>
	</tr>
	<tr>
		<td><b>Year:</b></td>
		<td>
			 <select name="year" id="year" style="width:40%;" required>
				<option value=''> Select Year </option>
				<?php foreach ($yearresult as $value) { ?>
				<option><?php echo $value['name']." (".$value['status']." year)"?></option>
				<?php } ?>
			</select>
		</td>
	</tr>
	<tr>
		<td><b>Date:</b></td>
		<td><input type='text' id='date' name='date' value='<?php echo date('d/m/Y');?>' style=" width:40%; text-align: center;"></td>
	</tr>
	<tr>
		<td><b>Amount:</b></td>
		<td><input type='text' id='amount' name='amount' value='500' style=" width:40%; text-align: center;"></td>
	</tr>
	<tr>
		<td colspan="2"><center><input type="submit" name='print' value="Print" style="padding:5px 30px;"></center></td>
	</tr>
</form>	
</table>
<script type="text/javascript">
		$(function() {
			$( "#date" ).datepicker({ dateFormat: 'dd/mm/yy' });
		});
</script>
<?php 
if(isset($_POST['print']))
{

?>

<script>
$(document).ready(function(){
	var w=window.open("","","height=600,width=700,status=yes,toolbar=no,menubar=no,location=no");
	var html='	<table width="500px" cellpadding="4" cellspacing="0" border="0"> \
				  <tr> \
					<th align="center" style="padding-top:50px; font-family:Arial, Helvetica, sans-serif; font-size:25px; color:#000000;">Calcutta Public School, Ormanjhi</th> \
				  </tr> \
				  <tr>	\
					<td align="center" style="font-family:Arial, Helvetica, sans-serif; font-size:20px; color:#000000;">Ormanjhi, Ranchi</td> \
				  </tr> \
				  <tr> \
					<td align="center" style="font-family:Arial, Helvetica, sans-serif; font-size:20px; color:#000000;"> Jharkhand, Pin Code - 835219</td> \
				  </tr> \
				   <tr> \
					<td align="center" style="font-family:Arial, Helvetica, sans-serif; font-size:16px; color:#000000; font-weight:bold; padding-top:25px;">Receipt</td> \
				  </tr> \
				  <tr> \
				  </table> \
				  <br>\
					<p style="font-family:Arial, Helvetica, sans-serif; font-size:20px; color:#000000;"> \
					Received Rs.<?php echo $_POST['amount'];?>.00 /-  <br>from Master / Miss: <b> <?php echo $_POST['s_name']?></b><br> Son / Daughter  of: <b> <?php echo $_POST['g_name'];?></b><br> \
					being the registration charges for his/her admission in class <b><?php echo $_POST['class'];?></b> in the academic session <b><?php echo $_POST['year']; ?></b>.</p> \
					<br><br><br> \
					<div style="float: left; margin-left:30px; font-size:20px;">Date: <b><?php echo $_POST['date'];?></b> </div> \
					<div style="float: right; margin-right:30px; font-size:20px;"> Authorised Signatory</div> \
					';
				  
	$(w.document.body).html(html);
	w.print();
})

</script>
<?php	
}
?>