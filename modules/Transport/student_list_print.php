<?php
include "../../config.php" ;
try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}
@session_start() ;
if($_GET) {
	if($_REQUEST['action']=='print')
	{
		$sql="SELECT gibbonperson.gibbonPersonID,gibbonperson.officialName as officialname,gibbonyeargroup.name AS class,
			gibbonrollgroup.name AS section,gibbonperson.account_number,gibbonstudentenrolment.rollOrder,transport_spot_price.spot_name,transport_spot_price.price,vehicles.details as vehicle, vehicles.vehicle_id,gibbonperson.account_number, gibbonperson.pd_point 
			 FROM gibbonperson 
			LEFT JOIN gibbonstudentenrolment ON gibbonstudentenrolment.gibbonPersonID=gibbonperson.gibbonPersonID
			LEFT JOIN gibbonyeargroup ON gibbonstudentenrolment.gibbonYearGroupID=gibbonyeargroup.gibbonYearGroupID
			LEFT JOIN transport_spot_price ON gibbonperson.transport_spot_price_id=transport_spot_price.transport_spot_price_id 
			LEFT JOIN gibbonrollgroup ON gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID 
			LEFT JOIN vehicles ON vehicles.vehicle_id=gibbonperson.vehicle_id 
			LEFT JOIN `transport_pickup_drop` ON `transport_pickup_drop`.`gibbonPersonID`=gibbonperson.gibbonPersonID
			WHERE gibbonperson.avail_transport='Y' AND `gibbonstudentenrolment`.`gibbonSchoolYearID`={$_SESSION[$guid]["gibbonSchoolYearID"]} AND (`gibbonperson`.`dateEnd` IS NULL OR `gibbonperson`.`dateEnd`>'".date('Y-m-d')."') " ;
			
		$sql.=$_REQUEST['sql'];
		$sql.=" ORDER BY `transport_pickup_drop`.`priority`, `vehicles`.`vehicle_id`";
		$result=$connection2->prepare($sql);
				$result->execute();
?>
	<table width="100%" cellpadding="6" cellspacing="0" style="border-left:1px solid #000000; border-top:1px solid #000000; font-family:Arial, Helvetica, sans-serif;">
	<tr style="background:#dddddd;">
		<td align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Sl No</td>
		<td align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Acc No</td>
		<td align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Name</td>
		<td align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Roll No</td>
		<td align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Class</td>
		<td align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Sec</td>
		<td align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Location</td>
		<td align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Pickup-Drop Point</td>
		<td align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Price</td>
		<td align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Vehicle</td>
		
	</tr>		
	
<?php
	$i=1;
		while ($row=$result->fetch()) {
						print "<tr>" ;
							print 	'<td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:14px; color:#000000;">'.$i++.'</td>';
							print 	'<td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:14px; color:#000000;">'.($row["account_number"]+0).'</td>';
							print 	'<td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:14px; color:#000000;">'.$row["officialname"].'</td>';
							print 	'<td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:14px; color:#000000;">'.$row["rollOrder"].'</td>';
							print 	'<td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:14px; color:#000000;">'.$row["class"].'</td>';
							print 	'<td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:14px; color:#000000;">'.$row["section"].'</td>';	
							print 	'<td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:14px; color:#000000;">'.$row["spot_name"] .'</td>';
							print 	'<td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:14px; color:#000000;">'.$row["pd_point"] .'</td>';
							print 	'<td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:14px; color:#000000;">'.$row["price"].'</td>';
							print 	'<td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:14px; color:#000000;">'.$row["vehicle"].'</td>';			
						print "</tr>" ;
		}
		print "</table>";
		print '<center><input type="button" name="print_button" id="print_button" value="Print" onclick="return printFunction()" style="background-color: #ff731b; border:none;  color: #ffffff;   cursor: pointer;  font-size: 14px;    font-weight: 600;    height: 28px;    margin: 2px;    min-width: 55px;  padding-left: 10px;
    padding-right: 10px;">
  	<input type="button" name="close_button" id="close_button" value="Close"  style="background-color: #ff731b; border:none;  color: #ffffff;   cursor: pointer;  font-size: 14px;    font-weight: 600;    height: 28px;    margin: 2px;    min-width: 55px;  padding-left: 10px;
    padding-right: 10px;" onclick="return cancelFunction();">';
	?>
	<script type="text/javascript">
function printFunction()
{
	document.getElementById("print_button").style.display='none';
	document.getElementById("close_button").style.display='none';
	window.print();
	}

function cancelFunction()
{
	window.close();
	}
</script>
	<?php
	}
}
?>