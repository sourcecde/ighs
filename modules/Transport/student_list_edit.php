<?php
@session_start() ;
$gibbonPersonID=$_REQUEST['gibbonPersonID'];
if(isset($_POST['submit']))
{
	$spot_id=$_POST['spot_name'];
	$vehicle_id=$_POST['vehicle'];
	$pd_point=$_POST['pd_point'];
	$priority=$_POST['priority'];
	$active=$_POST['active'];
	try {
	$sql="UPDATE gibbonperson SET transport_spot_price_id=".$spot_id.", vehicle_id=".$vehicle_id.",pd_point='".$pd_point."',active_transport='".$active."' WHERE gibbonPersonID=".$gibbonPersonID;
	$result=$connection2->prepare($sql);
	$result->execute();
	}
	catch(PDOException $pe) { echo $pe;}
	try {
	echo $sql="UPDATE `transport_pickup_drop` SET `priority`='$priority',`pd_point`='$pd_point' WHERE gibbonPersonID=".$gibbonPersonID;
	$result=$connection2->prepare($sql);
	$result->execute();
	}
	catch(PDOException $pe) { echo $pe;}
	print "<h3>Saved Sucessfylly!!</h3>";
	print "<center><a href='" . $_SESSION[$guid]['absoluteURL'] . "/index.php?q=/modules/" . $_SESSION[$guid]['module'] . "/student_list.php'>Go Back</a></center>";
}
else {
try { 
		$sqlFile="SELECT * from  transport_spot_price";
		$resultFile=$connection2->prepare($sqlFile);
		$resultFile->execute();
		$spot_details=$resultFile->fetchAll();
	}
		catch(PDOException $e) {
			}	
$spot_name=$_REQUEST['spot_name'];
$vehicle_id=$_REQUEST['vehicle'];
	try {
	$sql="SELECT preferredName AS name,active_transport,`transport_pickup_drop`.pd_point,`transport_pickup_drop`.priority
	FROM gibbonperson
	LEFT JOIN `transport_pickup_drop` ON `transport_pickup_drop`.`gibbonPersonID`=gibbonperson.gibbonPersonID
	WHERE gibbonperson.gibbonPersonID=".$gibbonPersonID;
	$result=$connection2->prepare($sql);
	$result->execute();
	$person=$result->fetch();
	}
	catch(PDOException $pe) { echo $pe;}		
?>
<form name="f1" id="f1" method="post" action="">
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
	<td>Name</td>
	<td><span  style="float:right;font-size:16px; padding-right:5px;"><?php echo $person['name'];?></span></td>
</tr>
<tr>
	<td>   	Active: </td>
	<td>
	<select name='active' id='active'>
		<option value='N'>No</option>
		<option value='Y' <?php if($person['active_transport']=='Y') echo "selected";?>>Yes</option>
	</select>
</tr>		
<tr>	
	<td>Location</td>
	<td><select name='spot_name' id='spot_name'>
	<?php foreach($spot_details as $value) {?>
			<option value='<?php echo $value['transport_spot_price_id']; ?>' <?php  if($value['spot_name']==$spot_name) echo "selected";?>>&nbsp;&nbsp;<?php echo $value['spot_name']; ?>&nbsp;</option>
	<?php } ?>		
		</select>
	</td>
</tr>

<tr>
	<td>Priority</td>
	<td><input type="text" name="priority" id="priority" value="<?php echo $person['priority'];?>" style="text-align:center; width:110px;"></td>
</tr>
<tr>
	<td>Pickup & Drop Point</td>
	<td><input type='text' name='pd_point' id='pd_point' value='<?php echo $person['pd_point'] ?>'></td>
</tr>
<tr>
	<td>   	Vehicle: </td>
	<td>
	<select name='vehicle' id='vehicle'>
		<option value=''>Select</option>
		<?php 
			$sql2="SELECT * FROM `vehicles`";
			$result2=$connection2->prepare($sql2);
			$result2->execute();
			$vehicles=$result2->fetchAll();
			foreach($vehicles as $a)
			{
				$s=$vehicle_id==$a['vehicle_id']?"selected":"";
				print "<option value='".$a['vehicle_id']."' ".$s.">".$a['details']."</option>";
			}
		?>
	</select>
	</td>
</tr>
<tr>
	<th colspan='2'><center><input type="submit" name="submit" id="submit" value="Submit"></center></th>
</tr>
</table>
</form>
<?php } ?>