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
		$data=$_REQUEST['sql'];
		$montharrr=array('jan'=>'January','feb'=>'February','mar'=>'March','apr'=>'April','may'=>'May','jun'=>'June','jul'=>'July','aug'=>'August','sep'=>'September','oct'=>'October','nov'=>'November','dec'=>'December');

		$sql="SELECT transport_month_entry.*,gibbonperson.officialName as officialname,gibbonyeargroup.name AS class,
		gibbonrollgroup.name AS section,gibbonperson.account_number,gibbonstudentenrolment.rollOrder,transport_spot_price.spot_name,gibbonschoolyear.status AS year, vehicles.details as vehicle, gibbonperson.pd_point,`payment_master`.`payment_date` 
		FROM transport_month_entry 
		LEFT JOIN transport_spot_price on transport_spot_price.transport_spot_price_id=transport_month_entry.transport_spot_price_id 
		LEFT JOIN gibbonperson ON transport_month_entry.gibbonPersonID=gibbonperson.gibbonPersonID
		LEFT JOIN gibbonstudentenrolment ON transport_month_entry.`gibbonStudentEnrolmentID`=gibbonstudentenrolment.`gibbonStudentEnrolmentID`
		LEFT JOIN gibbonyeargroup ON gibbonstudentenrolment.gibbonYearGroupID=gibbonyeargroup.gibbonYearGroupID
		LEFT JOIN gibbonschoolyear ON transport_month_entry.gibbonSchoolYearID=gibbonschoolyear.gibbonSchoolYearID
		LEFT JOIN gibbonrollgroup ON gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID 
		LEFT JOIN vehicles ON vehicles.vehicle_id=transport_month_entry.vehicle_id
		LEFT JOIN `payment_master` ON `payment_master`.`payment_master_id`=`transport_month_entry`.`payment_master_id` 
		where transport_month_entry.price!=0 " ; 
		$sql.=$data;
		$sql.=" order by transport_month_entry.transport_month_entryid DESC";
		$result=$connection2->prepare($sql);
		$result->execute();
		$row=$result->fetchAll();
		$total_amount=0;
?>
<table width="100%" cellpadding="6" cellspacing="0" style="border-left:1px solid #000000; border-top:1px solid #000000; font-family:Arial, Helvetica, sans-serif;">
	<tr style="background:#dddddd;">
			<td align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Entry&nbsp;Date</td>
		<td align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Acc&nbsp;No</td>
		<td align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Name</td>
		<td align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Roll No</td>
		<td align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Class</td>
		<td align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Sec</td>
		<td align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Amount</td>
		<td align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Month</td>
		<td align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Year</td>
	<!--	<td align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Location</td>
		<td align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Pickup-Drop Point</td> -->
		<td align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Vehicle</td>
		<td align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Status</td>
	</tr>
<?php
					$count=0;
					foreach($row as $row) {
						$total_amount+=$row["price"];
						$count++ ;
						$paymentdatearrr=explode(" ", $row["created_date"]);
						$paymentdatearr=explode("-", $paymentdatearrr[0]);
						
						if($row["payment_master_id"]==0){
							$paymentstatus='Unpaid';
						}else{
							$date=date('d/m/Y',strtotime($row['payment_date']));
							$paymentstatus="Paid<br>($date)";
						}
						//COLOR ROW BY STATUS!
						print "<tr>" ;
						
						print 	'<td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:14px; color:#000000;">'.$paymentdatearr[2].'/'.$paymentdatearr[1].'/'.$paymentdatearr[0].'</td>';
								
						
						print 	'<td align="right" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:14px; color:#000000;">'.substr($row["account_number"], 5).'</td>';
								
							
							print 	'<td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:14px; color:#000000;">'.$row["officialname"].'</td>';
							print 	'<td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:14px; color:#000000;">'.$row["rollOrder"].'</td>';
							print 	'<td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:14px; color:#000000;">'.$row["class"].'</td>';
							print 	'<td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:14px; color:#000000;">'.substr($row["section"],2).'</td>';
						
							print 	'<td align="right" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:14px; color:#000000;">'.$row["price"].'</td>';
						
							print 	'<td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:14px; color:#000000;">'.$montharrr[$row["month_name"]].'</td>';
							
							print 	'<td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:14px; color:#000000;">'.$row["year"].'</td>';
							
						/*	print 	'<td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:14px; color:#000000;">'.$row["spot_name"] .'</td>';
							print 	'<td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:14px; color:#000000;">'.$row["pd_point"] .'</td>';     */
						
							print 	'<td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:14px; color:#000000;">'.$row["vehicle"].'</td>';
								
							print 	'<td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:14px; color:#000000;">'.$paymentstatus.'</td>';
										
						print "</tr>" ;
					}
					print "<tr>" ;
							print "<td>" ;
							print "</td>" ;
							print "<td>" ;
							print "</td>" ;
							print "<td>" ;
							print "</td>" ;
							print "<td>" ;
							print "</td>" ;
							print 	'<td></td>';
							print 	'<td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; border-left:1px solid #000000; font-size:14px; color:#000000;">Total:</td>';
							print 	'<td align="right" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:14px; color:#000000;">'.$total_amount.'.00</td>';
						
							print "<td>" ;
							print "</td>" ;
							print "<td>" ;
							print "</td>" ;
							print "<td>" ;
							print "</td>" ;
							print "<td>" ;
							print "</td>" ;
							print "<td>" ;
							print "</td>" ;
						print "</tr>" ;
				print "</table>" ;
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