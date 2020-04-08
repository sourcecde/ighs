<?php
error_reporting(E_ALL);
include "../../config.php" ;
@session_start();
$alldate=array();
$filteredarray=array();
$total=0;
$alltotal=0;
try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}


	$src_fromdate=$_REQUEST['from_date'];

	$src_todate=$_REQUEST['to_date'];
	$year=$_REQUEST['year'];
	$p_mode=$_REQUEST['p_mode'];
	$p_monthduration=$_REQUEST['p_monthduration'];
	$sql="SELECT fee_payable.payment_date,SUM(net_amount) AS 'total_amount',fee_type_master.`fee_type_name` FROM fee_payable 
			LEFT JOIN fee_type_master ON fee_type_master.`fee_type_master_id`=fee_payable.`fee_type_master_id` 
			LEFT JOIN payment_master ON payment_master.payment_master_id=fee_payable.payment_master_id 
			where fee_payable.payment_date>='".$src_fromdate."' and fee_payable.payment_date<='".$src_todate."'"; 
	if($year!='')
		$sql.=" AND fee_payable.gibbonSchoolYearID=".$year;
	if($p_mode!='')
	{
		$sql.=" AND `payment_master`.payment_mode='".$p_mode."'";
	}
	if($p_monthduration!='')
	{	
		if($p_monthduration==1)
			$sql.=" AND `fee_payable`.month_no BETWEEN 0 AND 3";
		if($p_monthduration==2)
			$sql.=" AND `fee_payable`.month_no BETWEEN 4 AND 12";
	}
	$sql.=" GROUP BY fee_payable.payment_date,fee_payable.fee_type_short_name HAVING total_amount!=0";
	
	$result=$connection2->prepare($sql);
	$result->execute();
	$dboutbut=$result->fetchAll();
	
	$query="SELECT `payment_master`.payment_date, SUM(fine_amount) AS 'fine' 
	FROM `payment_master` 
	WHERE  `payment_master`.payment_date>='".$src_fromdate."' and `payment_master`.payment_date<='".$src_todate."'";
	if($year!='')
		$query.=" AND `payment_master`.gibbonSchoolYearID=".$year;
	if($p_mode!='')
	{
		$query.=" AND `payment_master`.payment_mode='".$p_mode."'";
	}
	if($p_monthduration!='')
	{	
		if($p_monthduration==1)
			$query.=" AND `fee_payable`.month_no BETWEEN 0 AND 3";
		if($p_monthduration==2)
			$query.=" AND `fee_payable`.month_no BETWEEN 4 AND 12";
	}
	
	$query.=" GROUP BY  `payment_master`.payment_date";
	
	$result1=$connection2->prepare($query);
	$result1->execute();
	$fine_result=$result1->fetchAll();
	
	$total_fine=0;
	$fine_array=array();
	
	foreach($fine_result as $a) {
		if($a['fine']>0){
			$fine_array[$a['payment_date']]= $a['fine'];
			$total_fine+=$a['fine'];
			}
	}
		$total=0;
	foreach ($dboutbut as $value) {
		if(!in_array($value['payment_date'], $alldate))
		{
			array_push($alldate, $value['payment_date']);
		}
		
	}
	foreach ($alldate as $alldatevalue) {
		$temparray=array();
		foreach ($dboutbut as $dbvalue) {
			if($alldatevalue==$dbvalue['payment_date'])
			{
				array_push($temparray, $dbvalue);
			}
		}
		$filteredarray[$alldatevalue]=$temparray;
	}
	
		//Transport Amount
	$query="SELECT payment_master.payment_date AS date,SUM(price) AS transport 
	FROM transport_month_entry
	LEFT JOIN payment_master ON payment_master.payment_master_id=transport_month_entry.payment_master_id
	WHERE  payment_master.payment_date>='".$src_fromdate."' and payment_master.payment_date<='".$src_todate."' AND transport_month_entry.payment_master_id >0";
	if($year!='')
		$query.=" AND payment_master.gibbonSchoolYearID=".$year;
	if($p_mode!='')
	{
        $query.=" AND `payment_master`.payment_mode='".$p_mode."'";
	}
	if($p_monthduration!='')
	{	
		if($p_monthduration==1)
			$query.=" AND `transport_month_entry`.`month_name` IN ('jan','feb','mar')";
		if($p_monthduration==2)
			$query.=" AND `transport_month_entry`.`month_name` NOT IN ('jan','feb','mar')";
	}
	$query.=" GROUP BY  payment_master.payment_date";
	$result2=$connection2->prepare($query);
	$result2->execute();
	$transport_result=$result2->fetchAll();
	$transport_arr=array();
	$total_transport=0;
	foreach($transport_result as $a)
	{
		$transport_arr[$a['date']]=$a['transport'];
		$total_transport+=$a['transport'];
	}
	
	if ($result->rowcount()<1 && $result2->rowcount()< 1) {
				print "<div class='error'>" ;
				print _("There are no records to display.") ;
				print "</div>" ;
			}
else {
?>
<div><h2 style="font-family:Arial, Helvetica, sans-serif; text-align:center; font-weight: bold;font-size: 20px; color:#000000; padding-bottom:10px;">Head Wise Collection Report from <?php echo DateConverterIndianFormat($src_fromdate);?> to <?php echo DateConverterIndianFormat($src_todate);?>
<?php if($p_monthduration!=''){ echo $p_monthduration==1?" For Months Duration : January-March":" For Months Duration : April-Decemeber"; }?>
</h2></div>
<div>
<table width="60%" cellpadding="6" cellspacing="0" style="border-left:1px solid #000000; border-top:1px solid #000000; font-family:Arial, Helvetica, sans-serif;" align="center">
<tr style="background:#bcbbbb;">
	<th style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Date</th>
	<th style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Fee Head</th>
	<th style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Amount</th>
</tr>
<?php 
$j=0;
for($date=$src_fromdate;strtotime($date) <= strtotime($src_todate);$date = date ("Y-m-d", strtotime("+1 day", strtotime($date)))){
                
if (array_key_exists($date,$filteredarray)) {
	$value=$filteredarray[$date];
//foreach ($filteredarray as $value) { 
//$total+=$value['total_amount'];
$i=0;
foreach ($value as $dvalue) {
	
	$total+=$dvalue['total_amount'];
	$alltotal+=$dvalue['total_amount'];
	?>

 <tr>
  	<td align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;"><?php if($i==0){ echo DateConverterIndianFormat($dvalue['payment_date']);}?></td>
    <td align="left" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;"><?php echo $dvalue['fee_type_name'];?></td>
    <td align="right" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;"><?php echo $dvalue['total_amount'];?></td>
  </tr>
   <?php 
$i++;
} 
if (array_key_exists($dvalue['payment_date'],$transport_arr)) {
	$t_p=$transport_arr[$dvalue['payment_date']];
	$total+=$t_p;
	?>
<tr>
  	<td align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;"></td>
    <td align="left" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Transport</td>
    <td align="right" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;"><?php echo number_format($t_p,2, '.', '');?></td>
  </tr>
 <?php
}
if(array_key_exists($dvalue['payment_date'],$fine_array)){
	$f_p=$fine_array[$dvalue['payment_date']];
	$total+=$f_p;
	?>
  <tr>
  	<td align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;"></td>
    <td align="left" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Fine</td>
    <td align="right" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;"><?php echo number_format($f_p,2, '.', '');?></td>
  </tr>
 <?php 
}?> 
<tr  style="background:#dddddd;">
  	<td align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;"></td>
    <td align="left" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Sub Total</td>
    <td align="right" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;"><?php echo number_format($total,2, '.', '');?></td>
  </tr>
  <?php 
  $total=0;
  $j++;
  } 
  else {
	  $sub_tot=0;
	  if (array_key_exists($date,$transport_arr)) {
		  	$t_p=$transport_arr[$date];
			$total+=$t_p;
	?>
	<tr>
  	<td style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 16px; color:#000000;"><?php echo DateConverterIndianFormat($date);?></td>
    <td style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 16px; color:#000000;">Transport</td>
    <td align="right" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 16px; color:#000000;"><?php echo number_format($t_p,2, '.', '');?></td>
	</tr>
<?php
	  }
	  if(array_key_exists($date,$fine_array)){
			$f_p=$fine_array[$date];
			$total+=$f_p;
			$sub_tot+=$f_p;
			?>
		  <tr>
			<td align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;"></td>
			<td align="left" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Fine</td>
			<td align="right" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;"><?php echo number_format($f_p,2, '.', '');?></td>
		  </tr>	
		<?php
		 }	
		  if($sub_tot>0){
		?>
			<tr  style="background:#dddddd;">
				<td align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;"></td>
				<td align="left" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Sub Total</td>
				<td align="right" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;"><?php echo number_format($sub_tot,2, '.', '');?></td>
			  </tr>
		<?php
			}
  }
}
  ?>
  <tr  style="background:#bcbbbb;">
  <td align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 16px; color:#000000;"></td>
    <td align="left" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 16px; color:#000000;">Total:</td>
    <td align="right" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 16px; color:#000000;"><?php echo number_format($alltotal+$total_fine+$total_transport, 2, '.', '');?></td>
  </tr>
   
</table>
</div>
<br>
<!--  this is for total table -->
<?php 
$sql="SELECT SUM(net_amount) AS 'total_amount',fee_type_master.`fee_type_name` FROM fee_payable 
LEFT JOIN fee_type_master ON fee_type_master.`fee_type_master_id`=fee_payable.`fee_type_master_id` 
LEFT JOIN payment_master ON payment_master.payment_master_id=fee_payable.payment_master_id 
WHERE fee_payable.payment_date>='".$src_fromdate."' and fee_payable.payment_date<='".$src_todate."'"; 
if($year!='')
		$sql.=" AND fee_payable.gibbonSchoolYearID=".$year; 
if($p_mode!='')
{
	    $sql.=" AND `payment_master`.payment_mode='".$p_mode."'";
}
if($p_monthduration!='')
{	
	if($p_monthduration==1)
		$sql.=" AND `fee_payable`.month_no BETWEEN 0 AND 3";
	if($p_monthduration==2)
		$sql.=" AND `fee_payable`.month_no BETWEEN 4 AND 12";
}
$sql.=" GROUP BY fee_payable.fee_type_short_name HAVING total_amount!=0";
	
	$result=$connection2->prepare($sql);
	$result->execute();
	$dboutbut=$result->fetchAll();
	$total1=0;
?>
<div >
<table width="60%" cellpadding="6" cellspacing="0" style="border-left:1px solid #000000; border-top:1px solid #000000; font-family:Arial, Helvetica, sans-serif;" align="center">
<tr style="background:#bcbbbb;">
	<th style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Fee Head</th>
	<th style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Amount</th>
</tr>
<?php foreach ($dboutbut as $value) { 
$total1+=$value['total_amount'];
	?>
  <tr>
    <td  align="left" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;"><?php echo $value['fee_type_name'];?></td>
    <td align="right" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;"><?php echo $value['total_amount'];?></td>
  </tr>
  <?php }
if($total_transport>0) {
  ?>
  
  <tr>
    <td  align="left" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 16px; color:#000000;">Transport</td>
    <td align="right" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 16px; color:#000000;"><?php echo number_format($total_transport, 2, '.', '');?></td>
  </tr>
  <?php 
}
if($total_fine>0) {
 ?>
  <tr>
    <td  align="left" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 16px; color:#000000;">Fine</td>
    <td align="right" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 16px; color:#000000;"><?php echo number_format($total_fine, 2, '.', '');?></td>
  </tr>
 <?php
} ?> 
  <tr  style="background:#bcbbbb;">
    <td  align="left" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 16px; color:#000000;">Total:</td>
    <td align="right" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 16px; color:#000000;"><?php echo number_format($total1+$total_fine+$total_transport, 2, '.', '');?></td>
  </tr>
</table>
</div>
<br>
<div style="text-align:center; margin:10px 0px;">
 <input type="button" name="print_button" id="print_button" value="Print" onclick="return printfunction();" style="background-color: #ff731b; border:none;  color: #ffffff;   cursor: pointer;  font-size: 14px;    font-weight: 600;    height: 28px;    margin:0px auto;    min-width: 55px;  padding-left: 10px;
    padding-right: 10px;">
</div>
<!--  end total table -->
<?php } ?>
<script type="text/javascript">
function printfunction()
{
window.print();
	}

function closefunction()
{
window.close();
	}
</script>
<?php 
function DateConverterIndianFormat($date)
{
	$datearr=explode("-", $date);
	$systemdate=$datearr[2].'/'.$datearr[1].'/'.$datearr[0];
	return $systemdate;
}
?>