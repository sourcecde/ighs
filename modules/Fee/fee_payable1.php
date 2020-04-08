<?php 
@session_start() ;

if (isActionAccessible($guid, $connection2, "/modules/Fee/fee_payable.php")==FALSE) {
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
$sql="SELECT `gibbonPersonID`,`preferredName`,`account_number` FROM `gibbonperson` WHERE `gibbonPersonID` IN (SELECT `gibbonPersonID` FROM `gibbonstudentenrolment`)";
$result=$connection2->prepare($sql);
$result->execute();
$dboutbut=$result->fetchAll();

$sql="SELECT * from gibbonschoolyear ORDER BY firstDay DESC";
$result=$connection2->prepare($sql);
$result->execute();
$yearresult=$result->fetchAll();
$year='';
$payablelist='';
$student_personID=0;
$month_arr1=array(0=>'yearly',1=>'jan',2=>'feb',3=>'mar',4=>'apr',5=>'may',6=>'jun',7=>'jul',8=>'aug',9=>'sep',10=>'oct',11=>'nov',12=>'dec');
$month_arr2=array('yearly'=>'Yearly','jan'=>'January','feb'=>'February','mar'=>'March','apr'=>'April','may'=>'May','jun'=>'June','jul'=>'July','aug'=>'August','sep'=>'September','oct'=>'October','nov'=>'November','dec'=>'December');
$month_sequence=array('Yearly'=>0,'January'=>94,'February'=>95,'March'=>96,'April'=>4,'May'=>5,'June'=>6,'July'=>7,'August'=>8,'September'=>90,'October'=>91,'November'=>92,'December'=>93);
if($_POST)
{
	if($_REQUEST['student_personID']!=0)
	{	
		$student_personID=$_REQUEST['student_personID'];
		$sql="SELECT `dateEnd` FROM `gibbonperson` WHERE `gibbonPersonID`=$student_personID";
		$result=$connection2->prepare($sql);
		$result->execute();
		$isLeft=$result->fetch();
		//print_r($isLeft);
		
		$data=array('gibbonPersonID'=>$_REQUEST['student_personID']);
		$sql="SELECT fee_payable.*,fee_rule_master.rule_name,fee_type_master.fee_type_name,fee_type_master.fee_type_master_id, gibbonschoolyear.name AS year
		 FROM fee_payable 
		 LEFT JOIN fee_rule_master ON fee_payable.rule_id=fee_rule_master.fee_rule_master_id
		 LEFT JOIN fee_type_master ON fee_type_master.fee_type_master_id=fee_payable.fee_type_master_id 
		 LEFT JOIN gibbonschoolyear ON gibbonschoolyear.gibbonSchoolYearID=fee_payable.gibbonSchoolYearID
		 WHERE gibbonPersonID=:gibbonPersonID";
		
		$query="SELECT `transport_month_entry`.`transport_month_entryid`,month_name,price,gibbonschoolyear.name AS year,transport_month_entry.payment_master_id AS payment_master_id,payment_master.voucher_number AS v_no 
		FROM transport_month_entry 
		LEFT JOIN payment_master ON payment_master.payment_master_id=transport_month_entry.payment_master_id
		LEFT JOIN gibbonschoolyear ON gibbonschoolyear.gibbonSchoolYearID=transport_month_entry.gibbonSchoolYearID
		WHERE transport_month_entry.gibbonPersonID=".$student_personID;
		if($_REQUEST['month_filter']!='')
		{
			$sql.=" AND fee_payable.month_no=".$_REQUEST['month_filter'];
			$query.=" AND transport_month_entry.month_name='".$month_arr1[$_REQUEST['month_filter']]."'";
		}
		if($_REQUEST['year_filter'])
		{
			$year=$_REQUEST['year_filter'];
			$sql.=" AND fee_payable.gibbonSchoolYearID=".$year;
			$query.=" AND transport_month_entry.gibbonSchoolYearID=".$year;	
		}
		if($_REQUEST['fee_type_filter'])
		{	
			if($_REQUEST['fee_type_filter']!='transport')
			$sql.=" AND fee_type_master.fee_type_master_id=".$_REQUEST['fee_type_filter'];
		}
		if($_REQUEST['payment_status_filter'])
		{
			$sql.=" AND payment_staus='".$_REQUEST['payment_status_filter']."'";
			if($_REQUEST['payment_status_filter']=='paid')
				$condition=" > ";
			else
				$condition=" = ";
			$query.=" AND transport_month_entry.payment_master_id".$condition."0";
		}
		$query.=" ORDER BY transport_month_entry.transport_month_entryid DESC";
		
		$result=$connection2->prepare($sql);
		$result->execute($data);
		$payablelist=$result->fetchAll();
		
		$result1=$connection2->prepare($query);
		$result1->execute($data);
		$transportlist=$result1->fetchAll();
			
	}
}


//get rule type masteree
$sql='Select fee_type_master_id,fee_type_name,boarder,boarder_type_name from fee_type_master order by fee_type_name';
$result=$connection2->prepare($sql);
$result->execute();
$all_fee_type=$result->fetchAll();


$schoolyeararr=array(0=>'Yearly',1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December');
$month='100';
if(isset($_REQUEST['month_filter']))
{
	if($_REQUEST['month_filter']!='')
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
?>
<?php
$gibbonRoleIDPrimary=$_SESSION[$guid]["gibbonRoleIDPrimary"];
	            if($gibbonRoleIDPrimary==003)
	            { ?>
	                
<form name="f1" id="f1" method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/fee_payable1.php" ?>">
<!--<div class="payable_select">
<table width="70%" cellpadding="0" cellspacing="0" border="0">
  <tr>
	<td>
		<input type="text" name="account_number" id="account_number" style="float: left;" placeholder="Account Number">
		<input type="button" name="search_by_acc" id="search_by_acc" value="Go">
	</td>
    <td>Please Choose Student: 
	<select name="student_personID" id="student_personID" required>
<option value="">Select</option>
<?php foreach ($dboutbut as $value) { 
$s=$student_personID==$value['gibbonPersonID']?'selected':'';
?>
	<option value="<?=$value['gibbonPersonID']?>"  <?=$s?>><?php echo $value['preferredName']?> (<?php echo $value['account_number']+0;?>)</option>
<?php } ?>
</select></td>
  </tr>
</table>
</div>-->
<input type="hidden" name="student_personID" id="student_personID" required value='<?php echo $_SESSION[$guid]["gibbonPersonID"] ?>'>
<div>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td>
<select name="month_filter" id="month_filter">
<option value="">Select Month</option>
<?php for($i=0;$i<=12;$i++){?>
<option value="<?php echo $i;?>" <?php if($month==$i){?> selected="selected"<?php } ?>><?php echo $schoolyeararr[$i];?></option>
<?php } ?>
</select>
</td>
<td>
<select name="year_filter" id="year_filter">
<option value="">Select Year</option>
<?php foreach($yearresult as $a){?>
<option value="<?php echo $a['gibbonSchoolYearID'];?>" <?php if($year==$a['gibbonSchoolYearID']){?> selected="selected"<?php } ?>><?php echo $a['name'];?></option>
<?php } ?>
</select>
</td>
<td>
<select name="fee_type_filter" id="fee_type_filter">
<option value="">Select Type</option>
<?php foreach ($all_fee_type as $value) { 
       //	$boarder='';
       //	if($value['boarder']=='yes')$boarder='Boarder';else $boarder='Non-Boarder';
       
       	?>
       	   <option value="<?php echo $value['fee_type_master_id']?>" <?php if($fee_type==$value['fee_type_master_id']){?> selected="selected"<?php } ?>><?php echo $value['fee_type_name']?>  - <?php echo $value['boarder_type_name'];?></option>
      <?php } ?> 
			<option value='transport'>Transport</option>
</select>
</td>
<td>
<select name="payment_status_filter" id="payment_status_filter">
<option value="">Select Status</option>
<option value="paid" <?php if($payment_status=='paid'){?> selected="selected"<?php } ?>>Paid</option>
<option value="unpaid" <?php if($payment_status=='unpaid'){?> selected="selected"<?php } ?>>Unpaid</option>
</select>
</td>

<td>
<input type="submit" id="submit" name="search" id="search" value="Search">
</td>
<?php
 	 if(isset($_POST['student_personID'])==FALSE)
	echo '<script>$("#submit").trigger("click");</script>';
	
 	 ?>
</tr>
</table>

</div>
</form>
	   <?php             
	            }
	            else
	            { ?>
<form name="f1" id="f1" method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/fee_payable1.php" ?>">
<div class="payable_select">
<table width="70%" cellpadding="0" cellspacing="0" border="0">
  <tr>
	<td>
		<input type="text" name="account_number" id="account_number" style="float: left;" placeholder="Account Number">
		<input type="button" name="search_by_acc" id="search_by_acc" value="Go">
	</td>
    <td>Please Choose Student: 
	<select name="student_personID" id="student_personID" required>
<option value="">Select</option>
<?php foreach ($dboutbut as $value) { 
$s=$student_personID==$value['gibbonPersonID']?'selected':'';
?>
	<option value="<?=$value['gibbonPersonID']?>"  <?=$s?>><?php echo $value['preferredName']?> (<?php echo $value['account_number']+0;?>)</option>
<?php } ?>
</select></td>
  </tr>
</table>
</div>
<div>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td>
<select name="month_filter" id="month_filter">
<option value="">Select Month</option>
<?php for($i=0;$i<=12;$i++){?>
<option value="<?php echo $i;?>" <?php if($month==$i){?> selected="selected"<?php } ?>><?php echo $schoolyeararr[$i];?></option>
<?php } ?>
</select>
</td>
<td>
<select name="year_filter" id="year_filter">
<option value="">Select Year</option>
<?php foreach($yearresult as $a){?>
<option value="<?php echo $a['gibbonSchoolYearID'];?>" <?php if($year==$a['gibbonSchoolYearID']){?> selected="selected"<?php } ?>><?php echo $a['name'];?></option>
<?php } ?>
</select>
</td>
<td>
<select name="fee_type_filter" id="fee_type_filter">
<option value="">Select Type</option>
<?php foreach ($all_fee_type as $value) { 
       //	$boarder='';
       //	if($value['boarder']=='yes')$boarder='Boarder';else $boarder='Non-Boarder';
       
       	?>
       	   <option value="<?php echo $value['fee_type_master_id']?>" <?php if($fee_type==$value['fee_type_master_id']){?> selected="selected"<?php } ?>><?php echo $value['fee_type_name']?>  - <?php echo $value['boarder_type_name'];?></option>
      <?php } ?> 
			<option value='transport'>Transport</option>
</select>
</td>
<td>
<select name="payment_status_filter" id="payment_status_filter">
<option value="">Select Status</option>
<option value="paid" <?php if($payment_status=='paid'){?> selected="selected"<?php } ?>>Paid</option>
<option value="unpaid" <?php if($payment_status=='unpaid'){?> selected="selected"<?php } ?>>Unpaid</option>
</select>
</td>

<td>
<input type="submit" name="search" id="search" value="Search">
</td>

</tr>
</table>

</div>
</form>
<?php } if($payablelist){
    $fee = array();
    $months = array();
foreach ($payablelist as $value){
    if($value['payment_staus']!='paid'){
    $fee[$value['month_no']][$value['fee_type_name']]=$value['amount'];
    $fee[$value['month_no']]["Year"]=$value['year'];
    }
}
/*foreach($fee as $month_no=>$feeTypeArr){
    echo "\n\n".$schoolyeararr[$month_no]."\n\n";
    foreach($feeTypeArr as $fee_type=>$amount){
        echo $fee_type."  ->  ".$amount."\n";
    }
}*/
?>
<div class="container" style="text-align: center;" aling="center">
<div style="text-align: left; float: left; padding:20px; background-color:white" >

  
 
  	<?php 
	  
	  
	  foreach($fee as $month_no=>$feeTypeArr){ 
	      $total_amount=0;
	      echo '<b>CALCUTTA PUBLIC SCHOOL BAGUIHATI</b><br><br>';
	          
	  /*$total_amount+=$value['amount'];
	  $total_consession+=$value['concession'];
	  $total_net_amount+=$value['net_amount'];
	  if($value['amount']==0)   //For hiding amount==0.
		  continue;*/
		  ?> 
		  <div style="text-align: center;">
		      <?php
	    echo $schoolyeararr[$month_no]."&nbsp;".$feeTypeArr["Year"];
	   ?>
		  </div>
		  <br>
		  <?php
	foreach($feeTypeArr as $fee_type=>$amount){ 
	if($fee_type!="Year") {
	?>
		
	    <div width="49%" style="float: left;"><?php echo $fee_type;?></div>
	    <div width="49%" style="float: right;"><?php echo $amount;?></div>
	    <br>
	
   <?php 
   $total_amount+=$amount; }
   }	  ?>
	  <br>
	  <div width="49%" style="float: left;">Total</div>
	  <div width="49%" style="float: right;"><?php echo $total_amount;?></div>
	  <br>
	  <br>
	  Parent's Signature ............................
	  <br>
	  <br>
	<hr>
	<br>
  <?php } ?>
</div>
<div style="text-align: left; float: left; padding:20px; background-color:white" >

  
 
  	<?php 
	  
	  
	  foreach($fee as $month_no=>$feeTypeArr){ 
	      $total_amount=0;
	      echo '<b>CALCUTTA PUBLIC SCHOOL BAGUIHATI</b><br><br>';
	          
	  /*$total_amount+=$value['amount'];
	  $total_consession+=$value['concession'];
	  $total_net_amount+=$value['net_amount'];
	  if($value['amount']==0)   //For hiding amount==0.
		  continue;*/
		  ?> 
		  <div style="text-align: center;">
		      <?php
	    echo $schoolyeararr[$month_no]."&nbsp;".$feeTypeArr["Year"];
	   ?>
		  </div>
		  <br>
		  <?php
	foreach($feeTypeArr as $fee_type=>$amount){ 
	if($fee_type!="Year") {
	?>
		
	    <div width="49%" style="float: left;"><?php echo $fee_type;?></div>
	    <div width="49%" style="float: right;"><?php echo $amount;?></div>
	    <br>
	
   <?php 
   $total_amount+=$amount; }
   }	  ?>
	  <br>
	  <div width="49%" style="float: left;">Total</div>
	  <div width="49%" style="float: right;"><?php echo $total_amount;?></div>
	  <br>
	  <br>
	  School's Signature ............................
	  <br>
	  <br>
	<hr>
	<br>
  <?php } ?>
</div>

</div>

<?php
 } ?>
 
<div id="give_concession" style="display: none;">
<table width="100%" cellpadding="0" cellspacing="0" border="0" id="consation_table">
<tr>
	<td colspan="2" id="concession_text"></td>
</tr>
  <tr>
    <td>Insert Concession Amount</td>
    <td><input type="text" name="concession_amount" id="concession_amount"></td>
  </tr>
  <tr>
    <td></td>
    <td>
    <input type="button" name="close_concession" id="close_concession" value="Close" style="float:right;">
    <input type="button" name="submit_concession" id="submit_concession" value="Submit" style="float:right;">
    </td>
  </tr>
</table>
</div>
<input type="hidden" name="hidden_fee_payable_id" id="hidden_fee_payable_id">
<input type="hidden" name="cocession_url" id="cocession_url" value="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/fee_ajax.php" ?>">
<input type="hidden" name="get_personID_from_accno_url" id="get_personID_from_accno_url" value="<?php echo $_SESSION[$guid]["absoluteURL"] . "/modules/Fee/ajax_get_personid_by_accno.php";?>">
<input type="hidden" id="processURL" value="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/deleteFromPayable.php" ?>">
 <script src="<?php echo $_SESSION[$guid]["absoluteURL"] ;?>/modules/Transport/js/jquery.dataTables.min.js"></script>
 <script>
	 $(document).ready(function(){
		$('.myTable').DataTable({
			 "iDisplayLength": 50,
			"oLanguage": {
			  "sLengthMenu": '<select>'+
				'<option value="50">50</option>'+
				'<option value="100">100</option>'+
				'<option value="200">200</option>'+
				'<option value="-1">All</option>'+
				'</select>'
			}
		  });
		 var processURL=$('#processURL').val();
		 $('.feeDelete').click(function(){
			 var id=$(this).attr('id');
			$.ajax
	 		({
	 			type: "POST",
	 			url: processURL,
	 			data: {action:'feeDelete',id:id},
	 			success: function(msg)
	 			{
	 				alert('Deleted Successfully!!');
					console.log(msg);
					location.reload();
	 			}
	 		});
		 });
		 $('.transportDelete').click(function(){
			 var id=$(this).attr('id');
			$.ajax
	 		({
	 			type: "POST",
	 			url: processURL,
	 			data: {action:'transportDelete',id:id},
	 			success: function(msg)
	 			{
	 				alert('Deleted Successfully!!');
					console.log(msg);
					location.reload();
	 			}
	 		});
		 });
	});
 </script>
 
 
<?php
};
?>