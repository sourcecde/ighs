<?php
@session_start() ;
$gibbonPersonID=NULL ;
if (isset($_SESSION[$guid]["gibbonPersonID"])) {
	$gibbonPersonID=$_SESSION[$guid]["gibbonPersonID"] ;
}
$sql="SELECT `gibbonSchoolYearID`, `name`, `status` FROM `gibbonschoolyear`";
$result=$connection2->prepare($sql);
$result->execute();
$year=$result->fetchAll();
$sql1="SELECT `transport_spot_price_id`, `spot_name` FROM `transport_spot_price`";
$result1=$connection2->prepare($sql1);
$result1->execute();
$location=$result1->fetchAll();
?>
 <h3>Add Drop Location Price :</h3> 
 <form method='POST' action=''>
 <table width='90%'>
 <tr>
	<td><b>Select Year :</b>
		<select name='yearID' required>
			<option value=''>SELECT</option>
			<?php
			$Yid=($_POST)?$_POST['yearID']:'';
			foreach($year as $y){
				$s=$Yid==$y['gibbonSchoolYearID']?'selected':'';
				echo "<option value='{$y['gibbonSchoolYearID']}' $s>{$y['name']}</option>";	
			} ?>
		</select>
	</td>
	<td><b>All Location:</b>
		<input type='checkbox' name='all_location' <?=isset($_POST['all_location'])?'checked':''?>>
	</td>
	<td><b>Select Location:</b>
		<select name='locationID'>
			<option value=''>Select</option>
			<?php
				foreach($location as $l){
					$s="";
					if(isset($_POST['locationID']))
						$s=isset($_POST['all_location'])?'':$_POST['locationID']==$l['transport_spot_price_id']?'selected':'';
					echo "<option value='{$l['transport_spot_price_id']}' $s>{$l['spot_name']}</option>";
				}
			?>
		</select>
	</td>
	<td>
		<input type='submit' value='Go'>
	</td>
 </tr>
 </table>
 </form>
<?php
if($_POST){
	extract($_POST);

$sql2="SELECT `transport_spot_price_id`, `spot_name` FROM `transport_spot_price` WHERE `transport_spot_price_id` NOT IN(SELECT `transport_spot_price_id` FROM `transport_fee_yearwise` WHERE `gibbonSchoolYearID`=$yearID)";
$result2=$connection2->prepare($sql2);
$result2->execute();
$locationF=$result2->fetchAll();

$location_array=array();
foreach($locationF as $l){
	$location_array[$l['transport_spot_price_id']]=$l['spot_name'];
}
if(sizeOf($location_array)>0){
$url=$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/process_location_price.php";
echo "<form method='POST' action='$url'>";
echo "<input type='hidden' name='action' value='add'>";
echo "<input type='hidden' name='gibbonSchoolYearID' value='$yearID'>";	
echo "<table width='50%'>";
echo "<tr><th>Location</th><th>Price</th></tr>";
if(isset($all_location)){
	foreach($location_array as $k=>$v){
		echo "<tr><td>$v</td><td><input name='price_$k' id='price_$k' type='text'></td></tr>";
	}
}
else {
	if(array_key_exists($locationID,$location_array))
		echo "<tr><td>$location_array[$locationID]</td><td><input name='price_$locationID' id='price_$locationID' type='text'></td></tr>";
	else {
		print "<div class='error'>" ;
			print _("Selected location price is already set for selected year.") ;
		print "</div>" ;
	}
}
echo "<tr><th colspan='2'><input type='submit' value='ADD'></th></tr>";
echo "</table>";
echo "</form>";
}
else{
	print "<div class='error'>" ;
		print _("Selected location price is already set for selected year.") ;
	print "</div>" ;
}
}
 ?>