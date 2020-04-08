<?php
@session_start() ;
$student_id='';
$location_id='';
$class='';
$section='';
$vehicle_id='';
$active_u='';

$sql="SELECT * from transport_spot_price";
$result=$connection2->prepare($sql);
$result->execute();
$spotlist=$result->fetchAll();

$sql="SELECT * FROM `gibbonschoolyear`";
$result=$connection2->prepare($sql);
$result->execute();
$schoolYear=$result->fetchAll();
$currentYearID=0;
foreach($schoolYear as $y){
	if($y['status']=='Current')
		$currentYearID=$y['gibbonSchoolYearID'];
}
$year_id=isset($_REQUEST['year_id'])?$_REQUEST['year_id']:$currentYearID;
$search=NULL;
if (isset($_GET["search"])) {
				//$search=$_GET["search"] ;
				$search='true';
			}
			//Set pagination variable
			$page=1 ; if (isset($_GET["page"])) { $page=$_GET["page"] ; }
			if ((!is_numeric($page)) OR $page<1) {
				$page=1 ;
			}
			
		//$sql="SELECT `gibbonperson`.`gibbonPersonID`, `preferredName`, `transport_pickup_drop`.`pd_point`, `transport_spot_price`.`spot_name`, `vehicles`.`details` FROM `gibbonperson` LEFT JOIN `transport_pickup_drop` ON `transport_pickup_drop`.`gibbonPersonID`=`gibbonperson`.`gibbonPersonID` LEFT JOIN `transport_spot_price` ON `transport_spot_price`.`transport_spot_price_id`=`gibbonperson`.`transport_spot_price_id` LEFT JOIN `vehicles` ON `vehicles`.`vehicle_id`=`gibbonperson`.`vehicle_id` WHERE avail_transport='Y'";
			
			try {
				
					$sql="SELECT DISTINCT gibbonperson.gibbonPersonID,gibbonperson.officialName as officialname,gibbonyeargroup.name AS class,
gibbonrollgroup.name AS section,gibbonperson.account_number,gibbonstudentenrolment.rollOrder,transport_spot_price.spot_name,vehicles.details as vehicle, vehicles.vehicle_id,active_transport,transport_pickup_drop.pd_point,transport_pickup_drop.priority 
 FROM gibbonperson 
LEFT JOIN gibbonstudentenrolment ON gibbonstudentenrolment.gibbonPersonID=gibbonperson.gibbonPersonID
LEFT JOIN gibbonyeargroup ON gibbonstudentenrolment.gibbonYearGroupID=gibbonyeargroup.gibbonYearGroupID
LEFT JOIN transport_spot_price ON gibbonperson.transport_spot_price_id=transport_spot_price.transport_spot_price_id 
LEFT JOIN gibbonrollgroup ON gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID 
LEFT JOIN vehicles ON vehicles.vehicle_id=gibbonperson.vehicle_id 
LEFT JOIN `transport_pickup_drop` ON `transport_pickup_drop`.`gibbonPersonID`=gibbonperson.gibbonPersonID 
WHERE gibbonperson.avail_transport='Y' AND `gibbonstudentenrolment`.`gibbonSchoolYearID`='".$_SESSION[$guid]["gibbonSchoolYearID"]."' AND (`gibbonperson`.`dateEnd` IS NULL OR `gibbonperson`.`dateEnd`>'".date('Y-m-d')."') ";  
$sqlp='';
if (isset($_GET["search"])) {
					if(isset($_REQUEST['student_id']))
						{
							if($_REQUEST['student_id']!='')
							{
							
								$student_id=$_REQUEST['student_id'];
								$sql.=" AND gibbonperson.gibbonPersonID=".$student_id;
								$sqlp.=" AND gibbonperson.gibbonPersonID=".$student_id;
							}
						}
						if(isset($_REQUEST['class']))
						{
							if($_REQUEST['class']!='')
							{
							
								$class=$_REQUEST['class'];
								$sql.=" AND gibbonyeargroup.gibbonYearGroupId =".$class;
								$sqlp.=" AND gibbonyeargroup.gibbonYearGroupId =".$class;
							}
						}
						if(isset($_REQUEST['location']))
						{
							if($_REQUEST['location']!='')
							{
								$location_id=$_REQUEST['location'];
								$sql.=" AND gibbonperson.transport_spot_price_id=".$location_id;
								$sqlp.=" AND gibbonperson.transport_spot_price_id=".$location_id;
							}
						}
						if(isset($_REQUEST['vehicle']))
						{
							if($_REQUEST['vehicle']!='')
							{
								$vehicle_id=$_REQUEST['vehicle'];
								$sql.=" AND gibbonperson.vehicle_id=".$vehicle_id;
								$sqlp.=" AND gibbonperson.vehicle_id=".$vehicle_id;
							}
						}
					
						
							$sql.=" AND gibbonstudentenrolment.gibbonSchoolYearID=".$year_id;
							$sqlp.=" AND gibbonstudentenrolment.gibbonSchoolYearID=".$year_id;
							
						if(isset($_REQUEST['active_u']))
						{
							if($_REQUEST['active_u']!='')
							{
								$active_u=$_REQUEST['active_u'];
								$sql.=" AND active_transport='".$active_u."'";
								 $sqlp.=" AND active_transport='".$active_u."'";
							}
						}
}	
				$sql.="  ORDER BY  `transport_pickup_drop`.`priority`, `vehicles`.`vehicle_id`";
				$sqlPage=$sql . " LIMIT " . $_SESSION[$guid]["pagination"] . " OFFSET " . (($page-1)*$_SESSION[$guid]["pagination"]) ;
				//echo $sql;
				$result=$connection2->prepare($sql);
				$result->execute();
			}
			catch(PDOException $e) { 
				print "<div class='error'>" . $e->getMessage() . "</div>" ; 
			}
			?>
	
<form name="f1" id="f1" method="get" action="<?php print $_SESSION[$guid]["absoluteURL"]?>/index.php">
<input type='hidden' name='print_data' id='print_data' value="<?php echo $sqlp;?>">
<input type="hidden" name="q" value="/modules/<?php print $_SESSION[$guid]["module"] ?>/student_list.php">
<input type="hidden" name="search" id="search" value='search'>
<table width="100%" cellpadding="0" cellspacing="0" border="0" class="search_table" style='word-wrap: break-word;'>
  <tr>
    <td colspan="2">
		<span style="float:left">
			<input type="text" name="account_number" id="account_number" style="width:100px; float:left;" placeholder="Account Number">
			<input type="button" style=" float:left;" name="search_by_acc_pID" id="search_by_acc_pID" value="Go">
		</span>
		<select name="student_id" id="student_id" style='width:150px; float:right;'>
		    <option value=""> Select Student </option>
		   <?php
			$sql1="SELECT gibbonPersonID, officialname from gibbonperson WHERE gibbonperson.avail_transport='Y' ";
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
	Class
	<select name='class' id='class'>
		<option value=''>Select</option>
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
	<td>Location:
	<select name='location' id='location'>
		<option value=''>Select</option>
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
  <tr> 
  </tr> 
   <td>
	<select name='vehicle' id='vehicle' style="width:150px;">
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
		Active: 
		<select name='active_u' id='active_u'>
			<option value=''>Select</option>
			<option value='Y' <?php if($active_u=='Y') echo "selected"; ?>>Yes</option>
			<option value='N' <?php if($active_u=='N') echo "selected"; ?>>No</option>
		</select>
	</td>
	<td>
	 <input type="submit"  value="Search"> &nbsp;&nbsp;&nbsp;&nbsp;
	</td>
	<td>
	 <span name="print_student_list" id="print_student_list" style='border:1px; padding:5px; margin: 2px;  background-color:#ff731b; color:white;'><b>Print</b></span>
	 <span name="print_student_list_contact" id="print_student_list_contact" style='border:1px; padding:5px; margin: 2px;  background-color:#ff731b; color:white;'><b>Print Contact</b></span>
     <a href="<?php echo $_SESSION[$guid]["absoluteURL"]; ?>/index.php?q=/modules/<?php echo $_SESSION[$guid]["module"]?>/student_list_process.php"><span id='add_student' style='border:1px; padding:5px; margin: 2px;  background-color:#ff731b; color:white;'>Add Student</span></a>

	</td>
  </tr>   
</table>
</form>

			<?php 
		if ($result->rowcount()<1) {
				print "<div class='error'>" ;
				print _("There are no records to display.") ;
				print "</div>" ;
			}
			else 
			{
				
			/*	if ($result->rowcount()>$_SESSION[$guid]["pagination"]) {
					printPagination($guid, $result->rowcount(), $page, $_SESSION[$guid]["pagination"], "top", "&search=$search") ;
				}*/
				print "<table cellspacing='0' style='width: 100%' class='myTable'>" ;
					print "<thead>" ;
					print "<tr class='head'>" ;
					
						print "<th>" ;
							print _("Sl No.") ;
						print "</th>" ;
						print "<th>" ;
							print _("Priority") ;
						print "</th>" ;
						print "<th>" ;
							print _("Acc No") ;
						print "</th>" ;
						print "<th>" ;
							print _("Name") ;
						print "</th>" ;
						print "<th>" ;
							print _("Roll") ;
						print "</th>" ;
						print "<th>" ;
							print _("Class") ;
						print "</th>" ;
						print "<th>" ;
							print _("Location") ;
						print "</th>" ;
						print "<th>" ;
							print _("Pickup & Drop Point") ;
						print "</th>" ;
						print "<th>" ;
							print _("Vehicle") ;
						print "</th>" ;
						print "<th>" ;
							print _("Active") ;
						print "</th>" ;
						print "<th>" ;
						print "</th>" ;
						
					print "</tr>" ;
					print "</thead>" ;
					print "<tbody>" ;
					$count=0;
					$rowNum="odd" ;
					try {
						//$resultPage=$connection2->prepare($sqlPage);
						$resultPage=$connection2->prepare($sql);
						$resultPage->execute();
					}
					catch(PDOException $e) { 
						print "<div class='error'>" . $e->getMessage() . "</div>" ; 
					}
					while ($row=$resultPage->fetch()) {
						if ($count%2==0) {
							$rowNum="even" ;
						}
						else {
							$rowNum="odd" ;
						}
						
						$count++ ;
						
						print "<tr class=$rowNum>" ;
						
							print "<td>" ;
								print $count;
							print "</td>" ;
							print "<td>" ;
								print $row["priority"] ;
							print "</td>" ;
							print "<td>" ;
								print ($row["account_number"]+0) ;
							print "</td>" ;
							print "<td>" ;
								print $row["officialname"] ;
							print "</td>" ;
							print "<td>" ;
								print $row["rollOrder"] ;
							print "</td>" ;
							print "<td>" ;
								print _($row["class"]) ;
								print "-".SectionFormater($row["section"]) ;
							print "</td>" ;
							print "<td>" ;
								print $row["spot_name"] ;
							print "</td>" ;
							print "<td>" ;
								print $row["pd_point"] ;
							print "</td>" ;
							print "<td>" ;
								print $row["vehicle"] ;
							print "</td>" ;
							print "<td>" ;
								if($row['active_transport']=='Y')
									$a_t="Yes";
								else
									$a_t="No";
								print  $a_t;
							print "</td>" ;
							print "<td>" ;
								//print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/student_view_details.php&gibbonPersonID=" . $row["gibbonPersonID"] . "&search=$search'>detail</a> " ;
								//print "<a href='javascript:void(9)' id='".$row["gibbonPersonID"]."_payment_list_print' class='print_list_print'>Print</a>&nbsp;|&nbsp;";
								
								print "<a href='" . $_SESSION[$guid]['absoluteURL'] . "/index.php?q=/modules/" . $_SESSION[$guid]['module'] . "/student_list_edit.php&gibbonPersonID=" . $row['gibbonPersonID']."&spot_name=".$row['spot_name']."&vehicle=".$row['vehicle_id']."'>Edit</a>";
								print ' | ';
								print "<a href='" . $_SESSION[$guid]['absoluteURL'] . "/index.php?q=/modules/" . $_SESSION[$guid]['module'] . "/student_list_delete.php&gibbonPersonID=" . $row['gibbonPersonID']."' onclick='return confirm(\"Are You sureely want to delete it?\")'>Delete</a>";
								
							print "</td>" ;
							
						print "</tr>" ;
					}
					print "</tbody>" ;
				print "</table>" ;
				
			/*	if ($result->rowcount()>$_SESSION[$guid]["pagination"]) {
					printPagination($guid, $result->rowcount(), $page, $_SESSION[$guid]["pagination"], "bottom", "search=$search") ;
				}*/
			
			}
			
?>
<script src="<?php echo $_SESSION[$guid]["absoluteURL"] ;?>/modules/Transport/js/jquery.dataTables.min.js"></script>
<script>
	 $(document).ready(function(){
		$('.myTable').DataTable({
			"scrollX": true,
			"iDisplayLength":50,
			"oLanguage": {
			  "sLengthMenu": '<select>'+
				'<option value="50">50</option>'+
				'<option value="100">100</option>'+
				'<option value="200">200</option>'+
				'<option value="300">300</option>'+
				'<option value="400">400</option>'+
				'<option value="500">500</option>'+
				'<option value="-1">All</option>'+
				'</select>'
			}
		  });
	});
 </script>