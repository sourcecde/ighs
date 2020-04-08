<?php
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


	$month_filter=$_REQUEST['month_filter'];
	$year_id='';
	if(isset($_REQUEST['year_id']))
	$year_id=$_REQUEST['year_id'];
	
	$sql="SELECT month_name,SUM(amount) as n_amount, SUM(concession) as n_concession, SUM(net_amount) as n_net_amount ,fee_type_master.fee_type_name,fee_type_master.boarder_type_name 
		FROM `fee_payable` 
		LEFT JOIN fee_type_master ON fee_type_master.fee_type_master_id=fee_payable.fee_type_master_id 
		where fee_payable.concession>0 ";
		if($year_id!='')	
		$sql.=" AND fee_payable.gibbonSchoolYearID=".$year_id;
		
		$sql.=" AND fee_payable.month_name in('".$month_filter."')";

		$sql.=" group by `month_name`,fee_payable.`fee_type_master_id` order by `month_no`";
		$result=$connection2->prepare($sql);
		$result->execute();
		$month_list=$result->fetchAll();
	$fullNameArray=array('yearly'=>'Yearly','jan'=>'January','feb'=>'February','mar'=>'March  ','apr'=>'April  ','may'=>'May    ','jun'=>'June   ','jul'=>'July   ','aug'=>'August','sep'=>'September','oct'=>'October','nov'=>'November','dec'=>'December');	
		
?>
<?php if(isset($month_list)) { ?>
<div><h2 style="font-family:Arial, Helvetica, sans-serif; text-align:center; font-weight: bold;font-size: 20px; color:#000000; padding-bottom:10px;">Head Wise Concession Report of '<?php echo $month_filter;?>'</h2></div>
<table width="100%" cellpadding="6" cellspacing="0" style="border-left:1px solid #000000; border-top:1px solid #000000; font-family:Arial, Helvetica, sans-serif;" id="rule_table">
  <tr style="background:#dddddd;">
    <th align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Month</th>
    <th align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Fees Head</th>
    <th align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Boarder Type</th>
    <th align="right" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Amount</th>
    <th align="right" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Concession</th>
    <th align="right" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Net Amount</th>
  </tr>
  <?php
	$total_concession=0;
	$m_name='';
  foreach($month_list as $a) { ?>
  <tr>
	<td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:14px; color:#000000;"><?php if($m_name!=$a['month_name'])echo $fullNameArray[$a['month_name']];?></td>
	<td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:14px; color:#000000;"><?php echo $a['fee_type_name'];?></td>
	<td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:14px; color:#000000;"><?php echo $a['boarder_type_name'];?></td>
	<td align="right" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:14px; color:#000000;"><?php echo $a['n_amount'];?></td>
	<td align="right" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:14px; color:#000000;"><?php echo $a['n_concession'];?></td>
	<td align="right" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:14px; color:#000000;"><?php echo $a['n_net_amount'];?></td>
  </tr>
  <?php
	$total_concession+=$a['n_concession'];
	$m_name=$a['month_name'];
  } ?>
  <tr>
  <td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:14px; color:#000000;"></td>
  <td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:14px; color:#000000;"></td>
  <td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:14px; color:#000000;"></td>
  <td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:14px; color:#000000;"></td>
  <td align="right" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:14px; color:#000000;"><b>Total : <?php echo $total_concession;?>.00</b></td> 
  <td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:14px; color:#000000;"></td>
  </tr>
</table>
<?php } ?>
<!-- Total Table -->
<?php
$sql1="SELECT SUM(concession) as n_concession, fee_type_master.fee_type_name,fee_type_master.boarder_type_name 
		FROM `fee_payable` 
		LEFT JOIN fee_type_master ON fee_type_master.fee_type_master_id=fee_payable.fee_type_master_id 
		where fee_payable.concession>0 ";
		if($year_id!='')	
		$sql1.=" AND fee_payable.gibbonSchoolYearID=".$year_id;
		
		$sql1.=" AND fee_payable.month_name in('".$month_filter."')";

		$sql1.=" group by fee_payable.`fee_type_master_id` order by fee_payable.`fee_type_master_id`";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		$concession_list=$result1->fetchAll();
if(isset($concession_list)) {
 ?>
 <br><br>
<table width="60%" cellpadding="6" cellspacing="0" style="border-left:1px solid #000000; border-top:1px solid #000000; font-family:Arial, Helvetica, sans-serif;" id="rule_table">
	<tr style="background:#dddddd;">
		<th align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Fee Head</th>
		<th align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Boarder Type</th>
		<th align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Amount</th>
	</tr>    
	<?php 
	$c_total=0;
	foreach($concession_list as $a) {?>
	<tr>
	<td  align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:14px; color:#000000;"><?php echo $a['fee_type_name'];?></td>
	<td  align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:14px; color:#000000;"><?php echo $a['boarder_type_name'];?></td>
	<td  align="right" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:14px; color:#000000;"><?php echo $a['n_concession'];?></td>
	</tr>
	<?php
	$c_total+=$a['n_concession'];
	} ?>
	<tr>
	
	<td  align="right" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:14px; color:#000000;" colspan='3'><b>Total: <?php echo $c_total;?>.00</b></td>
	</tr>
  </table>
<?php }?>
<!-- Total Table -->
<div id="print_button_area" style="position: relative; text-align:center; padding:15px 0px;">
<input type="button" name="print_collecttion" id="print_collecttion" onclick="return printFunction()" value="Print" style="background:#ff731b; color:#ffffff; font-size:14px; font-weight:bold; padding:5px 10px; border:none; outline:none; cursor:pointer;">
	<input type="button" name="cancel_collecttion" id="cancel_collecttion" onclick="return cancelFunction()" value="Close" style="background:#ff731b; color:#ffffff; font-size:14px; font-weight:bold; padding:5px 10px; border:none; outline:none; cursor:pointer;">
</div>
<script type="text/javascript">
function printFunction()
{
	document.getElementById("print_button_area").style.display='none';
	window.print();
	}

function cancelFunction()
{
	window.close();
	}
</script>  