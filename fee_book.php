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
//$sql="SELECT `gibbonPersonID`,`preferredName`,`account_number` FROM `gibbonperson` WHERE `gibbonPersonID` IN (SELECT `gibbonPersonID` FROM `gibbonstudentenrolment`)";
$sql="SELECT * FROM `gibbonyeargroup`";
$result=$connection2->prepare($sql);
$result->execute();
$dboutbut=$result->fetchAll();
$sql="SELECT *  FROM `gibbonsetting` WHERE `gibbonSystemSettingsID` IN (147,148) ORDER BY `gibbonSystemSettingsID`  ASC";
$result=$connection2->prepare($sql);
$result->execute();
$headers=$result->fetchAll();
$sql="SELECT * from gibbonschoolyear ORDER BY firstDay DESC";
$result=$connection2->prepare($sql);
$result->execute();
$yearresult=$result->fetchAll();
$year='';
$payablelist='';
$student_personID="";
$gibbonYearGroupID=0;
$month_arr1=array(0=>'yearly',1=>'jan',2=>'feb',3=>'mar',4=>'apr',5=>'may',6=>'jun',7=>'jul',8=>'aug',9=>'sep',10=>'oct',11=>'nov',12=>'dec');
$month_arr2=array('yearly'=>'Yearly','jan'=>'January','feb'=>'February','mar'=>'March','apr'=>'April','may'=>'May','jun'=>'June','jul'=>'July','aug'=>'August','sep'=>'September','oct'=>'October','nov'=>'November','dec'=>'December');
$month_sequence=array('Yearly'=>0,'January'=>94,'February'=>95,'March'=>96,'April'=>4,'May'=>5,'June'=>6,'July'=>7,'August'=>8,'September'=>90,'October'=>91,'November'=>92,'December'=>93);
if($_POST)
{
	if($_REQUEST['gibbonYearGroupID']!=0)
	{	
		//$student_personID=$_REQUEST['student_personID'];
		$gibbonYearGroupID=$_REQUEST['gibbonYearGroupID'];
		$sql="SELECT `gibbonPersonID` FROM `gibbonstudentenrolment` WHERE `gibbonYearGroupID`=$gibbonYearGroupID AND `gibbonSchoolYearID`=".$_POST['year_filter'];
		//$sql="SELECT `dateEnd` FROM `gibbonperson` WHERE `gibbonPersonID`=$student_personID";
		$result=$connection2->prepare($sql);
		$result->execute();
		$sData=$result->fetchAll();
		
		foreach($sData as $s){
			if($student_personID==""){
				$student_personID=$s['gibbonPersonID'];
			}
			else{
				$student_personID.=",".$s['gibbonPersonID'];
			}
		}
		//print_r($isLeft);
		
		$sql="SELECT fee_payable.*,fee_rule_master.rule_name,fee_type_master.fee_type_name,fee_type_master.fee_type_master_id, gibbonschoolyear.name AS year
		 FROM fee_payable 
		 LEFT JOIN fee_rule_master ON fee_payable.rule_id=fee_rule_master.fee_rule_master_id
		 LEFT JOIN fee_type_master ON fee_type_master.fee_type_master_id=fee_payable.fee_type_master_id 
		 LEFT JOIN gibbonschoolyear ON gibbonschoolyear.gibbonSchoolYearID=fee_payable.gibbonSchoolYearID
		 WHERE gibbonPersonID IN ($student_personID)";
		
		$query="SELECT `transport_month_entry`.`transport_month_entryid`,month_name,price,gibbonschoolyear.name AS year,transport_month_entry.payment_master_id AS payment_master_id,payment_master.voucher_number AS v_no 
		FROM transport_month_entry 
		LEFT JOIN payment_master ON payment_master.payment_master_id=transport_month_entry.payment_master_id
		LEFT JOIN gibbonschoolyear ON gibbonschoolyear.gibbonSchoolYearID=transport_month_entry.gibbonSchoolYearID
		WHERE transport_month_entry.gibbonPersonID IN ($student_personID)";
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
		$query.=" ORDER BY transport_month_entry.transport_month_entryid DESC";
		//echo $sql;
		$result=$connection2->prepare($sql);
		$result->execute();
		$payablelist=$result->fetchAll();
		
		$result1=$connection2->prepare($query);
		$result1->execute();
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
	                
<form name="f1" id="f1" method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/fee_book.php" ?>">
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
<form name="f1" id="f1" method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/fee_book.php" ?>">
<div>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td>Please Choose Class: 
	<select name="gibbonYearGroupID" id="gibbonYearGroupID" required>
<option value="">Select</option>
<?php foreach ($dboutbut as $value) { 
$s=$gibbonYearGroupID==$value['gibbonYearGroupID']?'selected':'';
?>
	<option value="<?=$value['gibbonYearGroupID']?>"  <?=$s?>><?php echo $value['name']?> </option>
<?php } ?>
</select></td>
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
<?php foreach($yearresult as $a){
	if(isset($_POST['year_filter'])){
		$s=$_POST['year_filter']==$a['gibbonSchoolYearID']?"selected=\"Selected\"":"";
	}
	else{
		$s=$_SESSION[$guid]["gibbonSchoolYearID"]==$a['gibbonSchoolYearID']?"selected=\"Selected\"":"";
	}
?><option value="<?php echo $a['gibbonSchoolYearID'];?>" <?php echo $s;?>><?php echo $a['name'];?></option>
<?php } ?>
</select>
</td>
<td>
<input type="submit" name="search" id="search" value="Search">
<input type='button' id='print' value='Print' onclick='printDiv()'>
</td>

</tr>
</table>

</div>
</form>
<?php } 
if($payablelist){
    $fee = array();
    $months = array();
foreach ($payablelist as $value){
    if($value['payment_staus']!='paid'){
    $fee[$value['gibbonPersonID']][$value['month_no']][$value['fee_type_name']]=$value['amount'];
    $fee[$value['gibbonPersonID']][$value['month_no']]["Year"]=$value['year'];
    }
}
echo "<pre>";
//print_r($fee);
echo "</pre>";
/*foreach($fee as $month_no=>$feeTypeArr){
    echo "\n\n".$schoolyeararr[$month_no]."\n\n";
    foreach($feeTypeArr as $fee_type=>$amount){
        echo $fee_type."  ->  ".$amount."\n";
    }
}*/
?>
<div style="width:30%; border: 0px solid; float:left">
<table style='width:100%' >
<thead>
<tr>
	<th><small>All</small>&nbsp;<input type='checkbox' id='selectAll' checked></th>
	<th>Student</th>
</tr>
</thead>
<tbody>
<?php foreach($fee as $personID=>$fee_personID){ 
		  $sql="SELECT `preferredName`,`account_number`,`gibbonyeargroup`.`name` as `class`,`gibbonrollgroup`.`name` as `section`,`phone1` FROM `gibbonperson`,`gibbonstudentenrolment`,`gibbonyeargroup`,`gibbonrollgroup` WHERE `gibbonperson`.`gibbonPersonID`=`gibbonstudentenrolment`.`gibbonPersonID` AND `gibbonyeargroup`.`gibbonYearGroupID`=`gibbonstudentenrolment`.`gibbonYearGroupID` AND `gibbonrollgroup`.`gibbonRollGroupID`=`gibbonstudentenrolment`.`gibbonRollGroupID` AND `gibbonperson`.`gibbonPersonID`=".$personID;
		  $result=$connection2->prepare($sql);
		  $result->execute();
		  $info=$result->fetch();
?>
<tr>
	<td><input type='checkbox' class='name_select' id="ch_<?=$personID?>" checked></td>
	<td class='student' id='st_<?=$personID?>'><b><?=$info['preferredName']?></b><br><span style='float:left'>Acc No: <i><?=$info['account_number']+0?></i></span><span style='float:right'> Section: <i><?=$info['section']?></i></span></td>
</tr>
<?php }?>
</tbody>
</table>
</div>
	  <div class="container" id='printDiv' style="text-align: center;width:70%;float:left;" align="center">
  	<?php 
	  foreach($fee as $personID=>$fee_personID){
		  echo "<div id='$personID' style='display:none;'>";
		  $sql="SELECT `officialName`,`account_number`,`gibbonyeargroup`.`name` as `class`,`gibbonrollgroup`.`name` as `section`,`phone1` FROM `gibbonperson`,`gibbonstudentenrolment`,`gibbonyeargroup`,`gibbonrollgroup` WHERE `gibbonperson`.`gibbonPersonID`=`gibbonstudentenrolment`.`gibbonPersonID` AND `gibbonyeargroup`.`gibbonYearGroupID`=`gibbonstudentenrolment`.`gibbonYearGroupID` AND `gibbonrollgroup`.`gibbonRollGroupID`=`gibbonstudentenrolment`.`gibbonRollGroupID` AND `gibbonperson`.`gibbonPersonID`=".$personID;
		  $result=$connection2->prepare($sql);
		  $result->execute();
		  $info=$result->fetch();
		foreach($fee_personID as $month_no=>$feeTypeArr){?>
		<div style="text-align: left; float: left; padding:20px; background-color:white;width:43%" >
<?php	  $total_amount=0;
		  		  echo "<div style='text-align:center;'>";
	      echo "<b style='text-align:center;font-size:18px'>CALCUTTA PUBLIC SCHOOL</b><br>";
		  foreach($headers as $h){
			echo "<b style='text-align:center;font-size:15px'>".$h['value']."</b><br>";
		  }
		  echo "<p style='text-align:center;font-size:14px'>www.calcuttapublicschool.in</p><br>";
		  echo "<b style='text-align:center;font-size:16px;text-decoration:underline;'>Fee Challan</b>";
		  echo "</div>";
		  ?>
		  <br>
		  <div width="49%" style="float: left;text-align:left">
		  Name : <?php echo $info['officialName'];?>
		  </div>
		  <div width="49%" style="float: right;text-align:right">
		  Acc. No. : <?php echo substr($info['account_number'],-5);?>
		  </div><br>
		  <div width="49%" style="float: left;text-align:left">
		  Class :  <?php echo $info['class']." ". $info['section'];?>
		  </div>
		  <div width="49%" style="float: right;text-align:right">
		  Phone : <?php echo $info['phone1'];?>
		  </div><br><br>		  <?php
	  /*$total_amount+=$value['amount'];
	  $total_consession+=$value['concession'];
	  $total_net_amount+=$value['net_amount'];
	  if($value['amount']==0)   //For hiding amount==0.
		  continue;*/
		  ?> 
		      <?php
	    echo "Fees for the Month of ".$schoolyeararr[$month_no]."&nbsp;".$feeTypeArr["Year"]."<br>";
		echo "<hr>";
		echo "<div width=\"49%\" style=\"float: left;text-align:left\">&nbsp&nbspParticulars</div>";
		echo "<div width=\"49%\" style=\"float: right;text-align:right\">Amount&nbsp&nbsp</div>";
		echo "<br>";
		echo "<hr>";
	   ?>
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
	  <table width="100%" style="background:#ffffff00;">
	  <tr>
	  <td width="50%" style="text-align:left;padding:0px;background:#ffffff00;border:0;">Sub-Total</td>
	  <td width="50%" style="text-align:right;padding:0px;background:#ffffff00;border:0;"><?php echo $total_amount;?></td>
	  </tr>
	  <tr>
	  <td colspan=2 style="text-align:right;font-size:80%;padding:0px;background:#ffffff00;border:0;"> (Rs. <?php echo ucwords(convert_number_to_words(intval($total_amount)));?> Only)</td>
	  </tr>
	  <td width="50%" style="text-align:left;padding:0px;background:#ffffff00;border:0;">Fine</td>
	  <td width="50%" style="text-align:right;padding:0px;background:#ffffff00;border:0;">..........</td>
	  </tr>
	  <tr>
	  <td width="50%" style="text-align:left;padding:0px;background:#ffffff00;border:0;">Total</td>
	  <td width="50%" style="text-align:right;padding:0px;background:#ffffff00;border:0;">..........</td>
	  </tr>
	  <tr>
	  <td colspan=2 style="text-align:right;padding:0px;font-size:80%;background:#ffffff00;border:0;">(Rs. __________________________________________ Only)</td>
	  </tr>
	  </table>
	  <br>
	  Parent's Signature ............................
	  <br>
	  <br>
	<hr>
	<br>
	<div id='pagebreak' style='page-break-after:always;page-break-before:avoid;'></div>
	</div>
			<div style="text-align: left; float: right; padding:20px; background-color:white;width:43%" >
<?php	  $total_amount=0;
		  		  echo "<div style='text-align:center;'>";
	      echo "<b style='text-align:center;font-size:18px'>CALCUTTA PUBLIC SCHOOL</b><br>";
		  foreach($headers as $h){
			echo "<b style='text-align:center;font-size:15px'>".$h['value']."</b><br>";
		  }
		  echo "<p style='text-align:center;font-size:14px'>www.calcuttapublicschool.in</p><br>";
		  echo "<b style='text-align:center;font-size:16px;text-decoration:underline;'>Fee Challan</b>";
		  echo "</div>";
		  ?>
		  <br>
		  <div width="49%" style="float: left;text-align:left">
		  Name : <?php echo $info['officialName'];?>
		  </div>
		  <div width="49%" style="float: right;text-align:right">
		  Acc. No. : <?php echo substr($info['account_number'],-5);?>
		  </div><br>
		  <div width="49%" style="float: left;text-align:left">
		  Class :  <?php echo $info['class']." ". $info['section'];?>
		  </div>
		  <div width="49%" style="float: right;text-align:right">
		  Phone : <?php echo $info['phone1'];?>
		  </div><br><br>		  <?php
	  /*$total_amount+=$value['amount'];
	  $total_consession+=$value['concession'];
	  $total_net_amount+=$value['net_amount'];
	  if($value['amount']==0)   //For hiding amount==0.
		  continue;*/
		  ?> 
		      <?php
	    echo "Fees for the Month of ".$schoolyeararr[$month_no]."&nbsp;".$feeTypeArr["Year"]."<br>";
		echo "<hr>";
		echo "<div width=\"49%\" style=\"float: left;text-align:left\">&nbsp&nbspParticulars</div>";
		echo "<div width=\"49%\" style=\"float: right;text-align:right\">Amount&nbsp&nbsp</div>";
		echo "<br>";
		echo "<hr>";
	   ?>
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
	  <table width="100%" style="background:#ffffff00;">
	  <tr>
	  <td width="50%" style="text-align:left;padding:0px;background:#ffffff00;border:0;">Sub-Total</td>
	  <td width="50%" style="text-align:right;padding:0px;background:#ffffff00;border:0;"><?php echo $total_amount;?></td>
	  </tr>
	  <tr>
	  <td colspan=2 style="text-align:right;font-size:80%;padding:0px;background:#ffffff00;border:0;"> (Rs. <?php echo ucwords(convert_number_to_words(intval($total_amount)));?> Only)</td>
	  </tr>
	  <td width="50%" style="text-align:left;padding:0px;background:#ffffff00;border:0;">Fine</td>
	  <td width="50%" style="text-align:right;padding:0px;background:#ffffff00;border:0;">..........</td>
	  </tr>
	  <tr>
	  <td width="50%" style="text-align:left;padding:0px;background:#ffffff00;border:0;">Total</td>
	  <td width="50%" style="text-align:right;padding:0px;background:#ffffff00;border:0;">..........</td>
	  </tr>
	  <tr>
	  <td colspan=2 style="text-align:right;padding:0px;font-size:80%;background:#ffffff00;border:0;">(Rs. __________________________________________ Only)</td>
	  </tr>
	  </table>
	  <br>
	  School's Signature ............................
	  <br>
	  <br>
	<hr>
	<br>
	<div id='pagebreak' style='page-break-after:always;page-break-before:avoid;'></div>
	</div>

<?php }
	echo "</div>";
}?>
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
		 $('.student').click(function(){
			var show_arr=($(this).attr("id")).split("_");
			var show_id=show_arr[1];
			console.log(show_id);
			$('.expand').hide();
			$('.expand').removeClass('expand');
			$("#"+show_id).addClass('expand');
			$('.focused').removeClass('focused');
			$(this).addClass('focused');
			$('.expand').toggle('fold',{},1200);
			
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
	function printDiv() 
{
	var printText="";
  //var divToPrint=document.getElementById('printDiv');
  $('.name_select').each(function(){
	if(this.checked){
		var id=$(this).attr("id").split("_");
		printText+=document.getElementById(id[1]).innerHTML;
	}
  });

  var newWin=window.open('','Print-Window');

  newWin.document.open();

  newWin.document.write('<html><head><style>@media print{ @page { size:A5 Landscape; } }</style></head><body onload="window.print()">'+printText+'</body></html>');

  newWin.document.close();

  //setTimeout(function(){newWin.close();},10);

}
 </script>
 
 
<?php
};
?>