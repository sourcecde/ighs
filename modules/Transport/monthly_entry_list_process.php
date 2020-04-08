<?php 
@session_start() ;
if(isset($_REQUEST['action'])) {
		$transport_month_entryid= $_REQUEST['transport_month_entryid'];
if($_REQUEST['action']=="edit")
{	try{
		$sql="SELECT transport_month_entry.*,gibbonperson.officialName as officialname,transport_spot_price.spot_name, vehicles.vehicle_id as vehicle 
			FROM transport_month_entry 
			LEFT JOIN transport_spot_price on transport_spot_price.transport_spot_price_id=transport_month_entry.transport_spot_price_id 
			LEFT JOIN gibbonperson ON transport_month_entry.gibbonPersonID=gibbonperson.gibbonPersonID
			LEFT JOIN vehicles ON vehicles.vehicle_id=transport_month_entry.vehicle_id
			WHERE transport_month_entryid=".$transport_month_entryid;
		$result=$connection2->prepare($sql);
		$result->execute();
		$row=$result->fetch();
		
		$sqlFile="SELECT * from  transport_spot_price";
		$resultFile=$connection2->prepare($sqlFile);
		$resultFile->execute();
		$spot_details=$resultFile->fetchAll();
		
		$sqlFile1="SELECT * from  vehicles";
		$resultFile1=$connection2->prepare($sqlFile1);
		$resultFile1->execute();
		$vehicles=$resultFile1->fetchAll();
		
		$sql="SELECT * from gibbonschoolyear ORDER BY firstDay DESC";
		$result=$connection2->prepare($sql);
		$result->execute();
		$yearresult=$result->fetchAll();
	}
	catch(PDOException $e)
	{
		echo $e;
	}
	$montharrr=array('jan'=>'January','feb'=>'February','mar'=>'March','apr'=>'April','may'=>'May','jun'=>'June','jul'=>'July','aug'=>'August','sep'=>'September','oct'=>'October','nov'=>'November','dec'=>'December');
?>
<form name="f1" id="f1" method="post" action=" <?php echo $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/monthly_entry_list_process.php";?>" >
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
	<td>Name</td>
	<td><span  style="float:right;font-size:16px; padding-right:5px;"><?php echo $row['officialname'];?></span>
		<input type='hidden' name='transport_month_entryid' id='transport_month_entryid' value='<?php echo $transport_month_entryid; ?>'>
	</td>
</tr>
<tr>
	<td>Month</td>
	<td>
		<select name="payment_month" id="payment_month">
	    	<option value=""> Select Month </option>
	    	<?php foreach ($montharrr as $key=>$value) { ?>
	    		<option value="<?php echo $key;?>" <?php if($row['month_name']==$key){?> selected="selected"<?php } ?>><?php echo $value;?></option>
	    	<?php }?>
		</select>
	</td>
</tr>
<tr>
	<td>Year</td>
	<td>
	<select name="year_name" id="year_name">
			<option value="0">Select Year</option>
			 <?php foreach ($yearresult as $value) { ?>
			<option value="<?php echo $value['gibbonSchoolYearID']?>" <?php if($row['gibbonSchoolYearID']==$value['gibbonSchoolYearID']) echo "selected";?>><?php echo $value['name']." (".$value['status']." year)"?> </option>
			<?php } ?>
	</select>	
</td>
</tr>
<tr>	
	<td>Location</td>
	<td><select name='spot_name' id='spot_name' onchange="jsSetPrice(this.value)">
	<?php foreach($spot_details as $value) {?>
			<option value='<?php echo $value['transport_spot_price_id']; ?>' <?php  if($value['spot_name']==$row['spot_name']) echo "selected";?>>&nbsp;&nbsp;<?php echo $value['spot_name']; ?>&nbsp;</option>
	<?php } ?>		
		</select>
	</td>
</tr>

<tr>
	<td>Price</td>
	<td><input type="text" name="price" id="price" value="<?php echo $row['price'];?>" style="text-align:center; width:110px;"></td>
</tr>
<tr>	
	<td>Vehicle</td>
	<td><select name='vehicle' id='vehicle'>
	<?php foreach($vehicles as $value) {?>
			<option value='<?php echo $value['vehicle_id']; ?>' <?php  if($value['vehicle_id']==$row['vehicle']) echo "selected";?>>&nbsp;&nbsp;<?php echo $value['details']; ?>&nbsp;</option>
	<?php } ?>		
		</select>
	</td>
</tr>
<tr>
	<td></td>
	<td><input type="submit" name="submit_edit" id="submit_edit" value="Submit"></td>
</tr>
</table>
</form>
<script type="text/javascript">
var spot_price= {<?php
					foreach ($spot_details as $a) { echo '"'.$a["transport_spot_price_id"].'":"'.$a["price"].'",'; }
					?> 1:1};
function jsSetPrice(id) {
document.getElementById("price").value = spot_price[id];
}
</script>
<?php	
}
if($_REQUEST['action']=="delete") {
try {
	$sql="DELETE FROM transport_month_entry where transport_month_entryid=".$transport_month_entryid." AND payment_master_id=0";
	$result=$connection2->prepare($sql);
	$result->execute();	
	echo "<h3>Deleted Sucesfully!!</h3>";
echo "<center><a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/monthly_entry_list.php'>Go Back</a></center>";
}
catch (PDOException $e)
{
	echo $e;
}
}
}
else {
	if(isset($_POST['submit_edit']))
	{
	$transport_month_entryid=$_POST['transport_month_entryid'];
	$month_name=$_POST['payment_month'];
	$gibbonSchoolYearID=$_POST['year_name'];
	$transport_spot_price_id=$_POST['spot_name'];
	$vehicle_id=$_POST['vehicle'];
	$price=$_POST['price'];
	try {
	$sql="UPDATE transport_month_entry SET transport_spot_price_id=".$transport_spot_price_id.", price=".$price.", vehicle_id=".$vehicle_id.", gibbonSchoolYearID=".$gibbonSchoolYearID.", month_name='".$month_name."' WHERE transport_month_entryid=".$transport_month_entryid." AND payment_master_id=0";
	$result=$connection2->prepare($sql);
	$result->execute();
	echo "<h3>Edited Sucesfully!!</h3>";
echo "<center><a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/monthly_entry_list.php'>Go Back</a></center>";
	}
	catch (PDOException $e) { echo $e;}
	}
}
?>