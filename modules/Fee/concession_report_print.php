<?php
include "../../config.php" ;
@session_start();
try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}

		
		$sql="SELECT fee_payable.*,fee_rule_master.rule_name,fee_type_master.fee_type_name,fee_type_master.fee_type_master_id,gibbonperson.officialName,`gibbonperson`.`account_number`,  
		gibbonrollgroup.name AS section,gibbonyeargroup.name AS class,gibbonstudentenrolment.rollOrder AS roll
		 FROM fee_payable 
		 LEFT JOIN fee_rule_master ON fee_payable.rule_id=fee_rule_master.fee_rule_master_id
		 LEFT JOIN fee_type_master ON fee_type_master.fee_type_master_id=fee_rule_master.fee_type_master_id 
		 LEFT JOIN gibbonperson ON fee_payable.gibbonPersonID=gibbonperson.gibbonPersonID
		 LEFT JOIN gibbonstudentenrolment ON fee_payable.gibbonStudentEnrolmentID=gibbonstudentenrolment.gibbonStudentEnrolmentID
         LEFT JOIN gibbonyeargroup ON gibbonstudentenrolment.gibbonYearGroupID=gibbonyeargroup.gibbonYearGroupID 
         LEFT JOIN gibbonrollgroup ON gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID
		 where fee_payable.concession>0 ";
		 
		if(isset($_REQUEST['student_personID']))
		{
			$sql.=" AND fee_payable.gibbonPersonID=".$_REQUEST['student_personID'];
		} 
		if(isset($_REQUEST['year_id']))
		{
			if($_REQUEST['year_id']!='')
					{
					$year=$_REQUEST['year_id'];	
					$sql.=" AND fee_payable.gibbonSchoolYearID=".$year;
					}
		}
		if(isset($_REQUEST['class_id']))
		{
			$sql.=" AND gibbonyeargroup.gibbonYearGroupID=".$_REQUEST['class_id'];
		}
		if(isset($_REQUEST['month_filter']))
		{
			$sql.=" AND fee_payable.month_no=".$_REQUEST['month_filter'];
		}
		if(isset($_REQUEST['fee_type_filter']))
		{
			$sql.=" AND fee_type_master.fee_type_master_id=".$_REQUEST['fee_type_filter'];
		}
		if(isset($_REQUEST['payment_status_filter']))
		{
			$sql.=" AND payment_staus='".$_REQUEST['payment_status_filter']."'";
		}
		$sql.=" GROUP BY fee_payable.gibbonPersonID";
		$result=$connection2->prepare($sql);
		$result->execute();
		$studentlist=$result->fetchAll();
		
		$sql='Select fee_type_master_id,fee_type_name,boarder from fee_type_master';
$result=$connection2->prepare($sql);
$result->execute();
$all_fee_type=$result->fetchAll();

	$sql="SELECT * FROM gibbonsetting WHERE gibbonSystemSettingsID='147'";	$result=$connection2->prepare($sql);	$result->execute();	$header2=$result->fetch();
$schoolyeararr=array(0=>'Yearly',1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December');
$month='';
if(isset($_REQUEST['month_filter']))
{
	$month=$_REQUEST['month_filter'];
}

$fee_type='';
if(isset($_REQUEST['fee_type_filter']))
{
	$fee_type=$_REQUEST['fee_type_filter'];
}

$payment_status='';
if(isset($_REQUEST['payment_status_filter']))
{
	$payment_status=$_REQUEST['payment_status_filter'];
}

$display_mode=$_REQUEST['display_mode_filter'];
?><html><body  onload="window.print()">

<?php if($studentlist){
?>
<table width="100%" cellpadding="6" cellspacing="0" style="border-left:1px solid #000000; border-top:1px solid #000000; font-family:Arial, Helvetica, sans-serif;" id="rule_table"><thead>	<tr>		<td colspan='10' style='border-bottom: 1px solid;border-right:1px solid;'>			<p style='text-align:center; font-weight:bold; font-size:14px; margin: 2px;'>CALCUTTA PUBLIC SCHOOL</p>			<p style='text-align:center;  font-size:12px;margin: 2px;'><?php echo $header2["value"];?></p>			<p style='text-align:center;  font-size:12px;margin: 2px;'>				Concession Report			</p>		</td>	</tr>
  <tr style="background:#dddddd;">
    <th align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size:12px">Acc No.</th>
    <th align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size:12px">Name</th>
    <th align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size:12px">Class</th>
    <th align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size:12px ">Roll</th>
<?php 
	if($display_mode!='short'){
?>
		<th align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size:12px; min-width:150px;">Fees Head</th>
		<th align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size:12px">Month</th>
<?php
	}
?>
    
    <th align="right" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size:12px">Amount</th>
    <th align="right" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size:12px">Concession</th>
<?php 
if($display_mode!='short'){
?>
	<th align="right" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size:12px ">Net Amount</th>
    <th align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size:12px;">Payment Status</th>
<?php
}
?>
  </tr></thead><tbody>
<?php	
$grandTotalConcession=0;
foreach ($studentlist as $s) {

    	$student_enrollid=$s['gibbonPersonID'];
    	
		$data=array('gibbonStudentEnrolmentID'=>$s['gibbonStudentEnrolmentID']);
		$sql="SELECT fee_payable.*,fee_rule_master.rule_name,fee_type_master.fee_type_name,fee_type_master.fee_type_master_id
		 FROM fee_payable 
		 LEFT JOIN fee_rule_master ON fee_payable.rule_id=fee_rule_master.fee_rule_master_id
		 LEFT JOIN fee_type_master ON fee_type_master.fee_type_master_id=fee_rule_master.fee_type_master_id 

		  where gibbonStudentEnrolmentID=:gibbonStudentEnrolmentID AND fee_payable.concession>0";
		
		if(isset($_REQUEST['month_filter']))
		{
			$sql.=" AND fee_payable.month_no=".$_REQUEST['month_filter'];
		}
		if(isset($_REQUEST['fee_type_filter']))
		{
			$sql.=" AND fee_type_master.fee_type_master_id=".$_REQUEST['fee_type_filter'];
		}
		if(isset($_REQUEST['payment_status_filter']))
		{
			$sql.=" AND payment_staus='".$_REQUEST['payment_status_filter']."'";
		}
		$sql.=" ORDER BY FIELD( `fee_payable`.`month_no`, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12)";
		$result=$connection2->prepare($sql);
		$result->execute($data);
		$payablelist=$result->fetchAll();
	  $total_amount=0;
	  $total_net_amount=0;
	  $total_consession=0;
	  $i=0;
	  if($display_mode=='short'){
		  foreach ($payablelist as $value) {
			  $total_amount+=$value['amount'];
			  $total_consession+=$value['concession'];
			  $grandTotalConcession+=$value['concession'];
		  }
		?>
		<tr>
			<td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:12px; color:#000000;" ><?php echo $s['account_number']+0;?></td>
			<td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:12px; color:#000000;" ><?php echo $s['officialName'];?></td>
			<td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:12px; color:#000000;"  ><?php echo $s['class']." - ".$s['section'];?></td>
			<td align="right" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:12px; color:#000000;"  ><?php echo $s['roll'];?></td>
			<td align="right" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:12px; color:#000000;"><?php echo number_format($total_amount,2);?></td>
			 <td align="right" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:12px; color:#000000;"><?php echo number_format($total_consession,2);?></td>
		</tr>
		<?php
	  }
	  else{
	  foreach ($payablelist as $value) {
	  $i++;
	  $total_amount+=$value['amount'];
	  $total_consession+=$value['concession'];
	  $total_net_amount+=$value['net_amount'];
	  $grandTotalConcession+=$value['concession'];
  	?>
  	
		  <tr>
			<td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:12px; color:#000000;" ><?php if($i==1)echo $s['account_number']+0;?></td>
			<td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:12px; color:#000000;" ><?php if($i==1)echo $s['officialName'];?></td>
			<td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:12px; color:#000000;"  ><?php if($i==1)echo $s['class']." ".$s['section'];?></td>
			<td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:12px; color:#000000;"  ><?php if($i==1)echo $s['roll'];?></td>
			<td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:12px; color:#000000;" id="<?php echo $value['fee_payable_id'];?>_fee_type_name"><?php echo $value['fee_type_name'];?></td>
			<td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:12px; color:#000000;" id="<?php echo $value['fee_payable_id'];?>_month_name"><?php echo $schoolyeararr[$value['month_no']];?></td>
			<td align="right" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:12px; color:#000000;"><?php echo $value['amount'];?></td>
			 <td align="right" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:12px; color:#000000;"><?php echo $value['concession'];?></td>
			  <td align="right" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:12px; color:#000000;"><?php echo $value['net_amount'];?></td>
			<td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:12px; color:#000000;"><?php
			if($value['payment_staus']=='paid')
			{
				echo ucfirst($value['payment_staus'])." - ".$value['voucher_number'];
			} 
			else 
			{
				echo ucfirst($value['payment_staus']);
			}
			?></td>
		  </tr>
  <?php 
		}
	}	 
  if($display_mode!='short'){
  ?>
	  <tr>
	  <td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:12px; color:#000000;"></td>
	  <td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:12px; color:#000000;"></td>
	  <td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:12px; color:#000000;"></td>
	  <td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:12px; color:#000000;"></td>
	  <td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:12px; color:#000000;"></td>
	  <td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:12px; color:#000000;"></td>
	  <td align="right" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:12px; color:#000000;"><b>Total</b></td>
	  <td align="right" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:12px; color:#000000;"><b><?php echo number_format($total_consession,2);?></b></td>
	  <td align="right" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:12px; color:#000000;"><!--  Total :--> <?php //echo $total_net_amount;?></td>
	  <td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:12px; color:#000000;"></td>
	  </tr>
<!--</table>
  </td>
  </tr>
</table>
</div>-->


<?php 
  }
	}
?>
  <tr>
  <td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:12px; color:#000000;"></td>
  <td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:12px; color:#000000;"></td>
  <td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:12px; color:#000000;"></td>
  <?php if($display_mode!='short'){
	?>
	<td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:12px; color:#000000;"></td>
	<td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:12px; color:#000000;"></td>
	<?php
  } ?>
  
  <td align="right" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:12px; color:#000000;" colspan="2"><b>Grand Total:</b></td>
  <td align="right" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:12px; color:#000000;"><b><?php echo number_format($grandTotalConcession,2);?></b></td>
  <?php if($display_mode!='short'){
	?>
	<td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:12px; color:#000000;"></td>
	<td align="center" style="border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:12px; color:#000000;"></td>
	<?php
	} ?>
  </tr></tbody>
</table></body></html>
<?php }?>