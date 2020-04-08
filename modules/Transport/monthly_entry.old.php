<?php
@session_start() ;
$studentlist=array();
$student_id='';
$class='';
$location_id='';
$vehicle_id='';
try {
	$sql="SELECT * from gibbonschoolyear ORDER BY firstDay DESC";
$result=$connection2->prepare($sql);
$result->execute();
$yearresult=$result->fetchAll();
$year='';
	
$data=array(); 
$sql="SELECT gibbonperson.gibbonPersonID,gibbonperson.officialName as officialname, `transport_pickup_drop`.pd_point,`transport_pickup_drop`.priority, gibbonyeargroup.name AS class, gibbonrollgroup.name AS section,gibbonperson.account_number,gibbonstudentenrolment.rollOrder,transport_spot_price.spot_name,transport_spot_price.price, vehicles.details as vehicle 
FROM gibbonperson 
LEFT JOIN gibbonstudentenrolment ON gibbonstudentenrolment.gibbonPersonID=gibbonperson.gibbonPersonID
LEFT JOIN gibbonyeargroup ON gibbonstudentenrolment.gibbonYearGroupID=gibbonyeargroup.gibbonYearGroupID
LEFT JOIN transport_spot_price ON gibbonperson.transport_spot_price_id=transport_spot_price.transport_spot_price_id 
LEFT JOIN gibbonrollgroup ON gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID 
LEFT JOIN vehicles ON vehicles.vehicle_id=gibbonperson.vehicle_id 
LEFT JOIN `transport_pickup_drop` ON `transport_pickup_drop`.`gibbonPersonID`=gibbonperson.gibbonPersonID
WHERE gibbonperson.avail_transport='Y' and gibbonperson.active_transport='Y' AND (`gibbonperson`.`dateEnd` IS NULL OR `gibbonperson`.`dateEnd`>'".date('Y-m-d')."')" ;  

if (isset($_GET["search"])) {
					if(isset($_REQUEST['student_id']))
						{
							if($_REQUEST['student_id']!='')
							{
							
								$student_id=$_REQUEST['student_id'];
								$sql.=" AND gibbonperson.gibbonPersonID=".$student_id;
							}
						}
						if(isset($_REQUEST['class']))
						{
							if($_REQUEST['class']!='')
							{
							
								$class=$_REQUEST['class'];
								$sql.=" AND gibbonyeargroup.gibbonYearGroupId =".$class;
							}
						}
						if(isset($_REQUEST['location']))
						{
							if($_REQUEST['location']!='')
							{
								$location_id=$_REQUEST['location'];
								$sql.=" AND gibbonperson.transport_spot_price_id=".$location_id;
							}
						}
						if(isset($_REQUEST['vehicle']))
						{
							if($_REQUEST['vehicle']!='')
							{
								$vehicle_id=$_REQUEST['vehicle'];
								$sql.=" AND gibbonperson.vehicle_id=".$vehicle_id;
							}
						}
}
				$sql.=" ORDER BY `transport_pickup_drop`.`priority`, `vehicles`.`vehicle_id`";
				$result=$connection2->prepare($sql);
				$result->execute($data);
				$studentlist=$result->fetchAll();
			}
			catch(PDOException $e) { 
				print "<div class='error'>" . $e->getMessage() . "</div>" ; 
			}
?>
<form name="f1" id="f1" method="get" action="<?php print $_SESSION[$guid]["absoluteURL"]?>/index.php">
<input type="hidden" name="q" value="/modules/<?php print $_SESSION[$guid]["module"] ?>/monthly_entry.php">
<input type="hidden" name="search" id="search" value='search'>
<table width="90%" cellpadding="0" cellspacing="0" border="0" class="search_table">
  <tr>
    <td>
		<span style="float:left">
			<input type="text" name="account_number" id="account_number" style="width:80px; float:left;" placeholder="Account Number">
			<input type="button" style=" float:left;" name="search_by_acc_pID" id="search_by_acc_pID" value="Go">
		</span>
		<select name="student_id" id="student_id" style="width:120px;">
		    <option value=""> Select Student </option>
		   <?php
			$sql1="SELECT gibbonPersonID, officialname,account_number from gibbonperson WHERE gibbonperson.avail_transport='Y' ";
			$result1=$connection2->prepare($sql1);
			$result1->execute();
			$student=$result1->fetchAll();
			foreach($student as $a){
				$s=$student_id==$a['gibbonPersonID']?"selected":"";
				echo "<option value='".$a['gibbonPersonID']."' ".$s.">".$a['officialname']."</option>";
			}
		   ?>
		</select>
		
    </td>

    <td>
	<select name='class' id='class' style="width:80px;">
		<option value=''> - Class -</option>
		<?php
		$sql2="SELECT * FROM gibbonyeargroup";
		$result2=$connection2->prepare($sql2);
		$result2->execute();
		$class_result=$result2->fetchAll();
		foreach($class_result as $a){
			$s=$class==$a['gibbonYearGroupID']?"selected":"";
			echo "<option value='".$a['gibbonYearGroupID']."' ".$s.">".$a['name']."</option>";
		}
		
		?>
	</select>
	</td>
	<td>
	<select name='location' id='location' style="width:120px;">
		<option value=''>Select Location</option>
		<?php 
			$sql1="SELECT * FROM `transport_spot_price`";
			$result1=$connection2->prepare($sql1);
			$result1->execute();
			$location=$result1->fetchAll();
			foreach($location as $a)
			{
				$s=$location_id==$a['transport_spot_price_id']?"selected":"";
				print "<option value='".$a['transport_spot_price_id']."' ".$s.">".$a['spot_name']."</option>";
			}
		?>
	</select>
   </td>
   <td>
	<select name='vehicle' id='vehicle' style="width:120px;">
		<option value=''>Select Vehicle</option>
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
	<td>
	 <input type="submit"  value="Search" style="float:right;">
	 </td>
  </tr>   
</table>
<hr style='width:90%'><hr style='width:90%'>
</form>
<table width="90%" cellpadding="0" cellspacing="0">
<tr>
	<td><b>Select Month:</b> 
	<select name="month_name" id="month_name">
	<option value="jan">January</option>
	<option value="feb">February</option>
	<option value="mar">March</option>
	<option value="apr">April</option>
	<option value="may">May</option>
	<option value="jun">June</option>
	<option value="jul">July</option>
	<option value="aug">August</option>
	<option value="sep">September</option>
	<option value="oct">October</option>
	<option value="nov">November</option>
	<option value="dec">December</option>
	</select>
	</td>
	<td><b>Select Year:</b>
		<select name="year_name" id="year_name">
			<option value=''>Select</option>
			 <?php foreach ($yearresult as $value) { ?>
			<option value="<?php echo $value['gibbonSchoolYearID']?>" <?php if($value['status']=='Current') echo "selected"; ?>><?php echo $value['name']." (".$value['status']." year)"?> </option>
			<?php } ?>
		</select>
	</td>
	<td>
	<input type="button" name="add_monthly_entry" id="add_monthly_entry" style='border:1px; padding:5px; float:right; background-color:#ff731b; color:white;'value="Add">
	</td>
</tr>
</table>

<table width="90%" cellpadding="0" cellspacing="0">
<tr>
	<th>Acc No</th>
	<th>Name</th>
	<th>Roll</th>
	<th>Class</th>
	<th>Section</th>
	<th>Location</th>
	<!--<th>Pickup-Drop Point</th>-->
	<th>Vehicle</th>
	<th>Price</th>
	<th>All <input type="checkbox" name="checkall" id="checkall" value="1"></th>
</tr>
<?php foreach ($studentlist as $value) { ?>
<tr>
	<td><?php print $value["account_number"]+0 ;?></td>
	<td><?php print $value["officialname"] ;?></td>
	<td><?php print $value["rollOrder"] ;?></td>
	<td><?php print $value["class"] ;?></td>
	<td><?php print SectionFormater($value["section"]) ;?></td>
	<td><?php print $value["spot_name"] ;?></td>
	<!--<td><?php print $value["pd_point"] ;?></td>-->
	<td><?php print $value["vehicle"] ;?></td>
	<td><?php print $value["price"] ;?></td>
	<td>
	<input type="checkbox" name="chk_<?php echo $value["gibbonPersonID"];?>" id="chk_<?php echo $value["gibbonPersonID"];?>" value="<?php echo $value["gibbonPersonID"];?>" class="student_list_chk">
	</td>
</tr>
<?php }?>
</table>
<input type="hidden" name="process_url" id="process_url" value="<?php echo $_SESSION[$guid]["absoluteURL"] . "/modules/Transport/monthly_entry_process.php";?>">