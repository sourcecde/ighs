<?php

@session_start() ;
if($_POST)
{
	$personid=$_REQUEST['student_personid'];
	$transport_spot_price_id=$_REQUEST['location'];
	$vehicle_id=$_REQUEST['vehicle'];
	$pd_point=$_REQUEST['pd_point'];
	$priority=$_REQUEST['priority'];
try {
		$dataFile=array("gibbonPersonID"=>$personid,"transport_spot_price_id"=>$transport_spot_price_id,"vehicle_id"=>$vehicle_id); 
		$sqlFile="UPDATE  gibbonperson SET avail_transport='Y',active_transport='Y',transport_spot_price_id=:transport_spot_price_id,vehicle_id=:vehicle_id where gibbonPersonID=:gibbonPersonID" ;
		$resultFile=$connection2->prepare($sqlFile);
		$resultFile->execute($dataFile);
		
		$sql="INSERT INTO `transport_pickup_drop`(`_id`, `gibbonPersonID`, `priority`, `pd_point`) VALUES (NULL,$personid,'$priority','$pd_point')";
		$result1=$connection2->prepare($sql);
		$result1->execute();
		//header("Location: ".$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Transport/student_list.php");
		}
		catch(PDOException $e) { 
		echo $e;
		}
	echo "<h3>Student Added Sucessfully!!</h3>
		<a href='".$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Transport/student_list.php'>Go Back</a>";
}
else {
$sql="SELECT gibbonstudentenrolment.*,gibbonperson.firstName,gibbonperson.surname,gibbonyeargroup.nameShort as class,gibbonrollgroup.name AS section,gibbonperson.account_number,gibbonperson.avail_transport,vehicles.details as vehicle 
FROM gibbonstudentenrolment 
LEFT JOIN gibbonperson ON gibbonstudentenrolment.gibbonPersonId=gibbonperson.gibbonPersonId 
LEFT JOIN gibbonyeargroup ON gibbonstudentenrolment.gibbonYearGroupId=gibbonyeargroup.gibbonYearGroupId 
LEFT JOIN gibbonrollgroup ON gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID
LEFT JOIN vehicles ON vehicles.vehicle_id=gibbonperson.vehicle_id where gibbonperson.avail_transport !='Y'";

$result=$connection2->prepare($sql);
$result->execute();
$dboutbut=$result->fetchAll();
?>
<form name="f1" id="f1" method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/student_list_process.php" ?>">
<table width="90%" cellpadding="0" cellspacing="0" border="0">
 <tr>
    <td><b>Please Choose Student</b></td>
    <td>
	<span style="float:left">
		<input type="text" name="account_number" id="account_number" style="width:100px; float:left;" placeholder="Account Number">
		<input type="button" style=" float:left;" name="search_by_acc_pID" id="search_by_acc_pID" value="Go">
	</span>
	<select name="student_personid" id="student_id" required>
	<option value="">Select</option>
	<?php foreach ($dboutbut as $value) { ?>
	<option value="<?php echo $value['gibbonPersonID']?>"><?php echo $value['firstName']?> <?php echo $value['surname']?> - <?php echo $value['class']." ".$value['section']?> &nbsp; (<?php echo substr($value['account_number'], 5);?>)</option>
	<?php } ?>
	</select>
	</td>
</tr>
<tr>
	<td><b>Location:</b></td>
	<td>
		<select name='location' id='location' required>
			<option value='' required>Select Location</option>
			<?php 
				$sql1="SELECT * FROM `transport_spot_price`";
				$result1=$connection2->prepare($sql1);
				$result1->execute();
				$location=$result1->fetchAll();
				foreach($location as $a)
				{
					print "<option value='".$a['transport_spot_price_id']."' >".$a['spot_name']."</option>";
				}
			?>
		</select>
   </td>
</tr>
<tr>
	<td><b>Pickup & Drop Point</b></td>
	<td><input type='text' name='pd_point' id='pd_point'></td>
</tr>
<tr>
	<td><b>Priority</b></td>
	<td><input type='text' name='priority' id='priority'></td>
</tr>
<tr>
	<td><b>Vehicle:</b></td>
	<td>
	<select name='vehicle' id='vehicle' required>
		<option value=''>Select Vehicle</option>
		<?php 
			$sql2="SELECT * FROM `vehicles`";
			$result2=$connection2->prepare($sql2);
			$result2->execute();
			$vehicles=$result2->fetchAll();
			foreach($vehicles as $a)
			{
				print "<option value='".$a['vehicle_id']."'>".$a['details']."</option>";
			}
		?>
	</select>
    </td>
</tr>
<tr>
<td></td><td><input type="submit" value="Add"></td>
</tr>	
</table>
</form>
<?php
}
?>