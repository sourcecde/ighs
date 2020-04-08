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
 ?>
 <h3>Add Transport Fee For Transport User:</h3> 
 <form method='POST' action=''>
 <table width='100%'>
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
	<td><b>All Transport User:</b>
		<input type='checkbox' name='all_user' <?=isset($_POST['all_user'])?'checked':''?>>
	</td>
	<td style='border:1px solid ;'>
		<span style="float:left">
			<input type="text" name="account_number" id="account_number" style="width:100px; float:left;" placeholder="Account Number">
			<input type="button" style=" float:left;" name="search_by_acc_pID" id="search_by_acc_pID" value="Go">
		</span>
		<select name='src_student' id='src_student'>
			<option value=""> Select User </option>
		   <?php
			$sql1="SELECT `gibbonPersonID`, `preferredName`,`account_number` FROM `gibbonperson` WHERE `gibbonperson`.`avail_transport`='Y' ";
			$result1=$connection2->prepare($sql1);
			$result1->execute();
			$student=$result1->fetchAll();
			$student_id=$_POST?(isset($_POST['all_user'])?'':$_POST['src_student']):'';
			foreach($student as $a){
				$s=$student_id==$a['gibbonPersonID']?"selected":"";
				echo "<option value='".$a['gibbonPersonID']."' ".$s.">".$a['preferredName']." ( ".($a['account_number']+0).")</option>";
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
$sql="SELECT gibbonperson.gibbonPersonID,gibbonperson.officialName, `transport_pickup_drop`.pd_point,`transport_pickup_drop`.priority, gibbonyeargroup.name AS class, gibbonrollgroup.name AS section,gibbonperson.account_number,gibbonstudentenrolment.rollOrder,transport_spot_price.spot_name,`transport_fee_yearwise`.`amount`, vehicles.details as vehicle 
FROM gibbonperson 
LEFT JOIN gibbonstudentenrolment ON gibbonstudentenrolment.gibbonPersonID=gibbonperson.gibbonPersonID
LEFT JOIN gibbonyeargroup ON gibbonstudentenrolment.gibbonYearGroupID=gibbonyeargroup.gibbonYearGroupID
LEFT JOIN transport_spot_price ON gibbonperson.transport_spot_price_id=transport_spot_price.transport_spot_price_id 
LEFT JOIN `transport_fee_yearwise` ON `transport_fee_yearwise`.`transport_spot_price_id`=transport_spot_price.transport_spot_price_id 
LEFT JOIN gibbonrollgroup ON gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID 
LEFT JOIN vehicles ON vehicles.vehicle_id=gibbonperson.vehicle_id 
LEFT JOIN `transport_pickup_drop` ON `transport_pickup_drop`.`gibbonPersonID`=gibbonperson.gibbonPersonID
WHERE gibbonperson.avail_transport='Y' and gibbonperson.active_transport='Y' AND (`gibbonperson`.`dateEnd` IS NULL OR `gibbonperson`.`dateEnd`>'".date('Y-m-d')."') AND `transport_fee_yearwise`.`gibbonSchoolYearID`=$yearID AND `gibbonstudentenrolment`.`gibbonSchoolYearID`=$yearID";
$sql.=" AND gibbonperson.gibbonPersonID NOT IN (SELECT DISTINCT `gibbonPersonID` FROM `transport_month_entry` WHERE `gibbonSchoolYearID`=$yearID) ";
$sql.=" ORDER BY gibbonyeargroup.gibbonYearGroupID,section,gibbonstudentenrolment.rollOrder";
$result=$connection2->prepare($sql);
$result->execute();
$data=$result->fetchAll();
if(sizeOf($data)>0){
if(isset($_POST['all_user']) || $src_student!=''){
$url=$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/monthly_entry_process.php";
echo "<form method='POST' action='$url'>";
echo "<input type='hidden' name='schoolYearID' value='$yearID'>";	
	echo "<table width='100%'>";
		
		if(isset($_POST['all_user'])){
			echo "<tr>";
				echo "<th style='width:15px'>Add</th> <th>Name</th> <th>Class</th> <th>Roll</th> <th>Acc No</th> <th>Location</th><th>Price</th><th>Vehicle</th> ";
			echo "</tr>";
			foreach($data as $d){
				echo "<tr>";
					echo "<td><input type='checkbox' name='personID[]' value='".($d['gibbonPersonID']+0)."' checked></td>";
					echo "<td>{$d['officialName']}</td>";
					echo "<td>{$d['section']}</td>";
					echo "<td style='text-align:right'>{$d['rollOrder']}</td>";
					echo "<td style='text-align:right'>".($d['account_number']+0)."</td>";
					echo "<td>{$d['spot_name']}</td>";
					echo "<td style='text-align:right'>{$d['amount']}</td>";
					echo "<td>{$d['vehicle']}</td>";
				echo "</tr>";
			}
			echo "<tr><th colspan='8'><center><input type='submit' value='ADD'></center></th></tr>";
	
		}
		else if($src_student!=''){
			$flag=false;
			foreach($data as $d){
				if($d['gibbonPersonID']==$src_student){
					$flag=true;
					echo "<tr>";
						echo "<th style='width:15px'>Add</th> <th>Name</th> <th>Class</th> <th>Roll</th> <th>Acc No</th> <th>Location</th><th>Price</th><th>Vehicle</th> ";
					echo "</tr>";
					echo "<tr>";
						echo "<td><input type='checkbox' name='personID[]' value='".($d['gibbonPersonID']+0)."' checked></td>";
						echo "<td>{$d['officialName']}</td>";
						echo "<td>{$d['section']}</td>";
						echo "<td style='text-align:right'>{$d['rollOrder']}</td>";
						echo "<td style='text-align:right'>".($d['account_number']+0)."</td>";
						echo "<td>{$d['spot_name']}</td>";
						echo "<td style='text-align:right'>{$d['amount']}</td>";
						echo "<td>{$d['vehicle']}</td>";
					echo "</tr>";
					echo "<tr><th colspan='8'><center><input type='submit' value='ADD'></center></th></tr>";
	
				}
			}
			if(!$flag){
				print "<div class='error'>" ;
					print _("<b>Drop Location Price of this student hasn't set yet for selected year!!</b>") ;
				print "</div>" ;
			}
		}
		echo "</table>";
echo "</form>";	
}
else{
	print "<div class='error'>" ;
		print _("No user is selected!!!") ;
	print "</div>" ;
}	
}
else{
	print "<div class='error'>" ;
		print _("Either price hasn't set yet for selected year!!<br>Or, No User is left for adding.!!") ;
	print "</div>" ;
}
}
 ?>