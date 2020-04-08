<?php 
@session_start() ;

if (isActionAccessible($guid, $connection2, "/modules/Fee/concession_report.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {

$gibbonPersonID=NULL ;
if (isset($_SESSION[$guid]["gibbonPersonID"])) {
	$gibbonPersonID=$_SESSION[$guid]["gibbonPersonID"] ;
}
$year='';
$sql="SELECT * from gibbonschoolyear ORDER BY status";
$result=$connection2->prepare($sql);
$result->execute();
$yearresult=$result->fetchAll();

$class='';
$sql="SELECT * from gibbonyeargroup ORDER BY sequenceNumber";
$result=$connection2->prepare($sql);
$result->execute();
$classresult=$result->fetchAll();

$sql="SELECT `gibbonPersonID`,`preferredName`,`account_number` FROM `gibbonperson` WHERE `gibbonPersonID` IN (SELECT `gibbonPersonID` FROM `gibbonstudentenrolment`)";
$result=$connection2->prepare($sql);
$result->execute();
$dboutbut=$result->fetchAll();


$payablelist='';
$studentlist='';
$student_personID=0;
if($_POST)
{
	
		$student_personID=$_REQUEST['student_personID'];
		$data=array('gibbonPersonID'=>$_REQUEST['student_personID']);
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
		if($student_personID)
		{
			$sql.=" AND fee_payable.gibbonPersonID=".$student_personID;
		}
		if(isset($_POST['year_id']))
		{
				if($_POST['year_id']!='')
					{
					$year=$_REQUEST['year_id'];	
					$sql.=" AND fee_payable.gibbonSchoolYearID=".$year;
					}
		}
		if(isset($_POST['class_id']))
		{
			if($_POST['class_id']!=''){
				$class=$_POST['class_id'];
				$sql.=" AND gibbonyeargroup.gibbonYearGroupID=".$class;
			}
		}
		if($_REQUEST['month_filter'])
		{
			$sql.=" AND fee_payable.month_no=".$_REQUEST['month_filter'];
		}
		if($_REQUEST['fee_type_filter'])
		{
			$sql.=" AND fee_type_master.fee_type_master_id=".$_REQUEST['fee_type_filter'];
		}
		if($_REQUEST['payment_status_filter'])
		{
			$sql.=" AND payment_staus='".$_REQUEST['payment_status_filter']."'";
		}
		$sql.=" GROUP BY fee_payable.gibbonPersonID ORDER BY `account_number`";
		$result=$connection2->prepare($sql);
		$result->execute($data);
		$studentlist=$result->fetchAll();
	
}


//get rule type masteree
$sql='Select fee_type_master_id,fee_type_name,boarder,boarder_type_name from fee_type_master order by fee_type_name';
$result=$connection2->prepare($sql);
$result->execute();
$all_fee_type=$result->fetchAll();


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
$display_mode='';
if(isset($_REQUEST['display_mode_filter'])){
	$display_mode=$_REQUEST['display_mode_filter'];
}
?>
<form name="f1" id="f1" method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/concession_report.php" ?>">
<div>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td align="right" colspan='2'>
		<span style=''>
		<input type="text" name="account_number" id="account_number" style="width:100px; float:left;" placeholder="Account Number">
		<input type="button" style=" float:left;" name="search_by_acc" id="search_by_acc" value="Go">
		<span>
	<select name="student_personID" id="student_personID" style="width:200px; float:right;">
	<option value="0">Select Student</option>
	<?php foreach ($dboutbut as $value) { 
	$s=$student_personID==$value['gibbonPersonID']?'selected':'';
	?>
	<option value="<?=$value['gibbonPersonID']?>"  <?=$s?>><?php echo $value['preferredName']?> (<?php echo $value['account_number']+0;?>)</option>
	<?php } ?>

	</select>
		
	</td>
	<td>
    <select name="year_id" id="year_id" style="width:110px; float:left;">
	<option value=''>Select Year</option>
    <?php foreach ($yearresult as $value) { ?>
    	<option value="<?php echo $value['gibbonSchoolYearID']?>" <?php if($year==$value['gibbonSchoolYearID']){?> selected="selected"<?php } ?>><?php echo $value['name']." (".$value['status']." year)"?></option>
    	<?php } ?>
    </select>
    </td>
	<td>
    <select name="class_id" id="class_id" style="width:110px; float:left;">
	<option value=''>Select Class</option>
    <?php foreach ($classresult as $value) { ?>
    	<option value="<?php echo $value['gibbonYearGroupID']?>" <?php if($class==$value['gibbonYearGroupID']){?> selected="selected"<?php } ?>><?php echo $value['name'];?></option>
    	<?php } ?>
    </select>
    </td>
	<td>
	<select name="month_filter" id="month_filter" style="float:left;">
		<option value=''>Select Month</option>
		<?php for($i=1;$i<=12;$i++){?>
		<option value="<?php echo $i;?>" <?php if($month==$i){?> selected="selected"<?php } ?>><?php echo $schoolyeararr[$i];?></option>
		<?php } ?>
	</select>
	</td>
  </tr>
  <tr>

<td>
<select name="fee_type_filter" id="fee_type_filter" style="width:200px; float:left;">
<option value="">Select Type</option>
<?php foreach ($all_fee_type as $value) {        
       	?>
       	   <option value="<?php echo $value['fee_type_master_id']?>" <?php if($fee_type==$value['fee_type_master_id']){?> selected="selected"<?php } ?>><?php echo $value['fee_type_name']?>  - <?php echo $value['boarder_type_name'];?></option>
      <?php } ?> 

</select>
</td>
<td>
<select name="payment_status_filter" id="payment_status_filter" style="width:110px; float:left;">
<option value="">Select Status</option>
<option value="paid" <?php if($payment_status=='paid'){?> selected="selected"<?php } ?>>Paid</option>
<option value="unpaid" <?php if($payment_status=='unpaid'){?> selected="selected"<?php } ?>>Unpaid</option>
</select>
</td>

<td>
	<select name="display_mode_filter" id="display_mode_filter" style="width:110px; float:left;">
		<option value="details" >Details</option>
		<option value="short" <?php echo $display_mode=='short'?'selected':''; ?>>Short</option>
	</select>
</td>
<td>
<input type="submit" name="search" id="search" value="Search"> &nbsp;&nbsp;&nbsp;
</td>
<td>
<?php if($_POST){?>
<input type="button" name="concession_print_page" id="concession_print_page" value="Print" style="background:seagreen; color:#ffffff; font-size:14px; font-weight:bold; padding:5px 10px; border:none; outline:none; cursor:pointer; float:right;">
<?php } ?>
</td></tr>
</table>

</div>

</form>
<?php if($studentlist){
	?>
	    <table width="100%" cellpadding="0" cellspacing="0" id="rule_table">
  <tr>
    <th>Account Number</th>
    <th>Name</th>
    <th>Class</th>
    <th>Roll</th>
	<?php 
		if($display_mode=='short'){
	?>
	<th>Amount</th>
    <th>Concession</th>
	<?php
		}
		else{
	?>
	<th>Fees Head</th>
    <th>Month</th>
    <th>Amount</th>
    <th>Concession</th>
    <th>Net Amount</th>
    <th>Payment Status</th>
	<?php
		}
	?>
    
  </tr>
  <?php
  $grandTotalConcession=0;
foreach ($studentlist as $s) {

	?>
 <!--<div  class="correctiontable">
   <table width="100%">
  <tr>
    <td class="officialname"><?php echo $s['officialName']?></td>
     </tr>
     <tr>
    <td>-->
    <?php 
    	$student_enrollid=$s['gibbonPersonID'];
    	
		$data=array('gibbonStudentEnrolmentID'=>$s['gibbonStudentEnrolmentID']);
		$sql="SELECT fee_payable.*,fee_rule_master.rule_name,fee_type_master.fee_type_name,fee_type_master.fee_type_master_id
		 FROM fee_payable 
		 LEFT JOIN fee_rule_master ON fee_payable.rule_id=fee_rule_master.fee_rule_master_id
		 LEFT JOIN fee_type_master ON fee_type_master.fee_type_master_id=fee_rule_master.fee_type_master_id 

		  where gibbonStudentEnrolmentID=:gibbonStudentEnrolmentID AND fee_payable.concession>0";
		
		if($_REQUEST['month_filter'])
		{
			$sql.=" AND fee_payable.month_no=".$_REQUEST['month_filter'];
		}
		if($_REQUEST['fee_type_filter'])
		{
			$sql.=" AND fee_type_master.fee_type_master_id=".$_REQUEST['fee_type_filter'];
		}
		if($_REQUEST['payment_status_filter'])
		{
			$sql.=" AND payment_staus='".$_REQUEST['payment_status_filter']."'";
		}
		$sql.=" ORDER BY FIELD( `fee_payable`.`month_no`, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12)";
		$result=$connection2->prepare($sql);
		$result->execute($data);
		$payablelist=$result->fetchAll();
    ?>
<!--    <table width="100%" cellpadding="0" cellspacing="0" id="rule_table">
  <tr>
    <th>Name</th>
    <th>Class</th>
    <th>Roll</th>
    <th>Fees Head</th>
    <th>Month</th>
    <th>Amount</th>
    <th>Concession</th>
    <th>Net Amount</th>
    <th>Payment Status</th>
  </tr>-->
  	<?php 
	  $total_amount=0;
	  $total_net_amount=0;
	  $total_consession=0;
	  $i=0;
	  if($display_mode=='short'){
		  foreach ($payablelist as $value) {
			  $i++;
			  $total_amount+=$value['amount'];
			  $total_consession+=$value['concession'];
			  $grandTotalConcession+=$value['concession'];
		  }
		?>
		<tr>
			<td><b><?php echo $s['account_number']+0; ?></b></td>
			<td><b><?php echo $s['officialName']; ?></b></td>
			<td><?php echo $s['class']." - ".$s['section']; ?></td>
			<td style="text-align: right"><?php echo $s['roll']; ?></td>
			<td style="text-align: right"><?php echo number_format($total_amount,2); ?></td>
			<td style="text-align: right"><?php echo number_format($total_consession,2); ?></td>
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
  <td><b><?php if($i==1)echo $s['account_number']+0;?></b></td>
  <td><b><?php if($i==1)echo $s['officialName']?></b></td>
  <td><?php if($i==1)echo $s['class']." - ".$s['section']?></td>
  <td style="text-align: right"><?php if($i==1)echo $s['roll']?></td>
    <td id="<?php echo $value['fee_payable_id'];?>_fee_type_name"><?php echo $value['fee_type_name'];?></td>
    <td id="<?php echo $value['fee_payable_id'];?>_month_name"><?php echo $schoolyeararr[$value['month_no']];?></td>
    <td style="text-align: right"><?php echo $value['amount'];?></td>
     <td style="text-align: right"><?php echo $value['concession'];?></td>
      <td style="text-align: right"><?php echo $value['net_amount'];?></td>
    <td><?php
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
	<?php }} 
	if($display_mode!='short'){
	?>
  <tr>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td style="text-align: right"><b>Total :</b></td>
  <td style="text-align: right"><b><?php echo number_format($total_consession,2);?></b></td> 
  <td><!--  Total :--><?php //echo $total_net_amount;?></td>
  <td></td>
  </tr>
<!--</table>
    </td>
  </tr>
</table> </div>-->



<?php
	}
 }
?>
<tr>
  <td colspan='<?php echo $display_mode=='short'?3:5; ?>'></td>
  <td colspan='2' style="text-align: right"><b> Grand Total: </b></td>
  <td style="text-align: right"><b><?php echo number_format($grandTotalConcession,2);?></b></td> 
  <?php if($display_mode!='short'){ echo "<td></td><td></td>"; }?>
</tr>
</table> 
<?php
}?>

<input type="hidden" name="hidden_fee_payable_id" id="hidden_fee_payable_id">
<input type="hidden" name="cocession_report_url" id="cocession_report_url" value="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/concession_report_print.php" ?>">
<input type="hidden" name="get_personID_from_accno_url" id="get_personID_from_accno_url" value="<?php echo $_SESSION[$guid]["absoluteURL"] . "/modules/Fee/ajax_get_personid_by_accno.php";?>">
<?php
};
?>
