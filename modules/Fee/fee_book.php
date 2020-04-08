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
$gibbonRollGroupID=0;

$month_arr1=array(0=>'yearly',1=>'jan',2=>'feb',3=>'mar',4=>'apr',5=>'may',6=>'jun',7=>'jul',8=>'aug',9=>'sep',10=>'oct',11=>'nov',12=>'dec');

$month_arr2=array('yearly'=>'Yearly','jan'=>'January','feb'=>'February','mar'=>'March','apr'=>'April','may'=>'May','jun'=>'June','jul'=>'July','aug'=>'August','sep'=>'September','oct'=>'October','nov'=>'November','dec'=>'December');
$month_sequence=array('Yearly'=>0,'January'=>94,'February'=>95,'March'=>96,'April'=>4,'May'=>5,'June'=>6,'July'=>7,'August'=>8,'September'=>90,'October'=>91,'November'=>92,'December'=>93);




///echo "<pre>";print_r($_REQUEST);
if($_POST)
{
	if($_REQUEST['gibbonYearGroupID']!=0)
	{	

		//$student_personID=$_REQUEST['student_personID'];
		$gibbonYearGroupID=$_REQUEST['gibbonYearGroupID'];
			$sql="Select gibbonRollGroupID,name FROM gibbonrollgroup WHERE gibbonSchoolYearID=".$_POST['year_filter']." AND gibbonYearGroupID=".$_POST['gibbonYearGroupID'];
	$resultSection=$connection2->prepare($sql);
	$resultSection->execute();
		$gibbonRollGroupID=$_REQUEST['gibbonRollGroupID'];
		$sql="SELECT `gibbonstudentenrolment`.`gibbonPersonID` FROM `gibbonstudentenrolment`,`gibbonperson` WHERE `gibbonperson`.`gibbonPersonID`=`gibbonstudentenrolment`.`gibbonPersonID` AND `gibbonstudentenrolment`.`gibbonYearGroupID`=$gibbonYearGroupID AND `gibbonstudentenrolment`.`gibbonSchoolYearID`=".$_POST['year_filter'];
		//echo $sql;
		if($gibbonRollGroupID!=0){
			$sql.=" AND `gibbonstudentenrolment`.`gibbonRollGroupID`=".$gibbonRollGroupID;
		}
		$sql.=" ORDER BY `account_number`";
	        ///echo $sql;die;
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
		//echo $student_personID;
		
		
		$sql="SELECT fee_payable.*,fee_rule_master.rule_name,fee_type_master.fee_type_name,fee_type_master.fee_type_master_id, gibbonschoolyear.name AS year
		 FROM fee_payable 
		 LEFT JOIN fee_rule_master ON fee_payable.rule_id=fee_rule_master.fee_rule_master_id
		 LEFT JOIN fee_type_master ON fee_type_master.fee_type_master_id=fee_payable.fee_type_master_id 
		 LEFT JOIN gibbonschoolyear ON gibbonschoolyear.gibbonSchoolYearID=fee_payable.gibbonSchoolYearID
		 WHERE gibbonPersonID IN ($student_personID)";
		 
		//echo $sql;
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
		$sql.=" ORDER BY `fee_payable`.`month_no`,`fee_type_master`.`pseq`";
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
    <td>
	<select name="gibbonYearGroupID" id="gibbonYearGroupID" required>
<option value="">Select Class</option>
<?php foreach ($dboutbut as $value) { 
$s=$gibbonYearGroupID==$value['gibbonYearGroupID']?'selected':'';
?>
	<option value="<?=$value['gibbonYearGroupID']?>"  <?=$s?>><?php echo $value['name']?> </option>
<?php } ?>
</select></td>
<td>
<select id='gibbonRollGroupID' name='gibbonRollGroupID'>
<option>Select Section</option>
<?php
	if($gibbonYearGroupID!=0 && $resultSection->rowcount()>0){
	$sectionDB=$resultSection->fetchAll();
	foreach($sectionDB as $sd){
	$s=$sd['gibbonRollGroupID']==$gibbonRollGroupID?"selected = 'selected'":"";
	echo "<option value='{$sd['gibbonRollGroupID']}' $s>{$sd['name']}</option>";
	}
}
?>
</select>
</td>
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
        $month1=$value['month_no'];
        if($month1==0)
        {
            $month1=4;
        }
    $fee[$value['gibbonPersonID']][$month1][$value['fee_type_name']]=$value['net_amount'];
    $fee[$value['gibbonPersonID']][$month1]["Year"]=$value['year'];
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
<?php //print_r($fee); ?>
<?php foreach($fee as $personID=>$fee_personID){ 

			//echo "<pre>";
			//print_r($fee_personID)."</pre><br>";
 	  $sql="SELECT `preferredName`,`account_number`,`gibbonyeargroup`.`name` as `class`,`gibbonrollgroup`.`name` as `section`,`phone1` FROM `gibbonperson`,`gibbonstudentenrolment`,`gibbonyeargroup`,`gibbonrollgroup` WHERE `gibbonperson`.`gibbonPersonID`=`gibbonstudentenrolment`.`gibbonPersonID` AND `gibbonyeargroup`.`gibbonYearGroupID`=`gibbonstudentenrolment`.`gibbonYearGroupID` AND `gibbonrollgroup`.`gibbonRollGroupID`=`gibbonstudentenrolment`.`gibbonRollGroupID` AND `gibbonperson`.`gibbonPersonID`=".$personID." AND `gibbonstudentenrolment`.`gibbonSchoolYearID`=".$_REQUEST['year_filter'];;
		  
		  $result=$connection2->prepare($sql);
		  $result->execute();
		  $info=$result->fetch();
		 // echo "<hr>";
		 //echo "<tr><td>$sql</td></tr>";
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
	      /*
		  	$keys = array_keys($fee_personID);
			//print_r($keys);
			$j=$keys[0]==1?3:4;
			for($i=$j-3;$i<$j;$i++){
			if(isset($fee_personID[$keys[$i]]) && ($keys[$i]==1 || $keys[$i]==2 || $keys[$i]==3)){
			$val = $fee_personID[$keys[$i]];
			unset($fee_personID[$keys[$i]]);
			$fee_personID[$keys[$i]] = $val;
			}
			else{
				break;
			}
			}
		    */
		    
        



		  echo "<div id='$personID' style='display:none;'>";
		  
		  $sql="SELECT `officialName`,`account_number`,`gibbonyeargroup`.`name` as `class`,`gibbonrollgroup`.`name` as `section`,`phone1` FROM `gibbonperson`,`gibbonstudentenrolment`,`gibbonyeargroup`,`gibbonrollgroup` WHERE `gibbonperson`.`gibbonPersonID`=`gibbonstudentenrolment`.`gibbonPersonID` AND `gibbonyeargroup`.`gibbonYearGroupID`=`gibbonstudentenrolment`.`gibbonYearGroupID` AND `gibbonrollgroup`.`gibbonRollGroupID`=`gibbonstudentenrolment`.`gibbonRollGroupID` AND  `gibbonperson`.`gibbonPersonID`=".$personID." AND `gibbonstudentenrolment`.`gibbonSchoolYearID`=".$_REQUEST['year_filter'];
		  $result=$connection2->prepare($sql);
		  $result->execute();
		  $info=$result->fetch();
		   
		foreach($fee_personID as $month_no=>$feeTypeArr){?>
		<div style="text-align: left; float: left; padding:20px; background-color:white;width:43%" >
<?php	  $total_amount=0;
				echo '<div width="49%" style="text-align:right">';
			echo "<p>Date : ________</p></div>"; 
		  		  echo "<div style='text-align:center;'>";
		    echo '<div width="10%" style="float: left;text-align:left">';
			///echo "<img src='reportlogo.png' width='60px' height='60px'></div>";
			
		  echo "<img src='http://ighs.in/ighs_lakshya_sr/themes/Default/img/reportlogo.png' width='60px' height='60px'></div>";
	      echo "<b style='text-align:center;font-size:20px'>Indra Gopal High School</b><br>";
		  foreach($headers as $h){
			echo "<b style='text-align:center;font-size:10px'>".$h['value']."</b><br>";
		  }
		  echo "<p style='display: inline; text-align:center;font-size:14px'>www.ighs.in</p>";
		  echo "<br><br><b style='text-align:center;font-size:16px;text-decoration:underline;'>Fees Challan</b>";
		  echo "</div>";
		  ?>
		  <br>
		  <div width="49%" style="float: left;text-align:left">
		  Name : <?php echo $info['officialName'];?>
		  </div>
		  <div width="49%" style="float: right;text-align:right">
		  Acc. No. : <?php echo substr($info['account_number'],-4);?>
		  </div><br>
		  <div width="49%" style="float: left;text-align:left">
		  Class :  <?php echo $info['class'];?>
		  </div>
          <div width="49%" style="float: center;text-align:center">
		  Section : <?php //echo $info['section'];?>     
		  </div>		  
		  <br>		  <?php
	  /*$total_amount+=$value['amount'];
	  $total_consession+=$value['concession'];
	  $total_net_amount+=$value['net_amount'];
	  if($value['amount']==0)   //For hiding amount==0.
		  continue;*/
		  ?> 
		      <?php
	    if($month_no!=1&&$month_no!=2&&$month_no!=3)
		      $monthof=substr($feeTypeArr["Year"],0,4);
		      else
		      $monthof=substr($feeTypeArr["Year"],-4);
	    echo "Fees for the Month of ".$schoolyeararr[$month_no]."&nbsp;".$monthof."<br>";
		echo "<hr>";
		echo "<div width=\"49%\" style=\"float: left;text-align:left\">&nbsp&nbspParticulars</div>";
		echo "<div width=\"49%\" style=\"float: right;text-align:right\">Amount&nbsp&nbsp</div>";
		echo "<br>";
		echo "<hr>";
	   ?>
		  
		  <?php
	foreach($feeTypeArr as $fee_type=>$amount){ 
	if($fee_type!="Year"){
	?>
		
	    <div width="49%" style="float: left;"><?php echo $fee_type;?></div>
	    <div width="49%" style="float: right;"><?php echo number_format($amount, 2);?></div>
	    <br>
	
   <?php 
	$total_amount+=$amount;}
   }	  ?>
<hr>
<div width="49%" style="float: left;">Total</div>
	  <div width="49%" style="float: right;"><?php echo number_format($total_amount,2);?></div>
	  <br>
	 <div > (Rs. <?php echo ucwords(convert_number_to_words(intval($total_amount)));?> Only)</div>
	    <br>
	    <?php if($info['class']=='09'){?>
	    <div >Computer Fee</div>
	    <br>
	    <?php } ?>
	  <div >Late Fine</div>
	  <hr>
	  <div>Total</div>
	  <hr>
	  <br>
	  <br>
	  <br>
	  <br>
	   <br>
	  <div style="text-align:right;padding:0px;background:#ffffff00;border:0;">Receiving Officer</div>
	<div style="page-break-before: always"> </div>
	</div>
			<div style="text-align: left; float: right; padding:20px; background-color:white;width:43%" >
<?php	  $total_amount=0;
           echo '<div width="49%" style="text-align:right">';
			echo "<p>Date : ________</p></div>"; 
		  		  echo "<div style='text-align:center;'>";
		    echo '<div width="10%" style="float: left;text-align:left">';
			//echo "<img src='reportlogo.png' width='60px' height='60px'></div>";
	      echo "<img src='http://ighs.in/ighs_lakshya_sr//themes/Default/img/reportlogo.png' width='60px' height='60px'></div>";
	      echo "<b style='text-align:center;font-size:20px'>Indra Gopal High School</b><br>";
		  foreach($headers as $h){
			echo "<b style='text-align:center;font-size:10px'>".$h['value']."</b><br>";
		  }
		  echo "<p style='display: inline; text-align:center;font-size:14px'>www.ighs.in</p>";
		  echo "<br><br><b style='text-align:center;font-size:16px;text-decoration:underline;'>Fees Challan</b>";
		  echo "</div>";
		  ?>
		  <br>
		  <div width="49%" style="float: left;text-align:left">
		  Name : <?php echo $info['officialName'];?>
		  </div>
		  <div width="49%" style="float: right;text-align:right">
		  Acc. No. : <?php echo substr($info['account_number'],-4);?>
		  </div><br>
		  <div width="49%" style="float: left;text-align:left">
		  Class :  <?php echo $info['class'];?>
		  </div>
          <div width="49%" style="float: center;text-align:center">
		  Section :      
		  </div>		 		  
		  
		  <br>		  <?php
	  /*$total_amount+=$value['amount'];
	  $total_consession+=$value['concession'];
	  $total_net_amount+=$value['net_amount'];
	  if($value['amount']==0)   //For hiding amount==0.
		  continue;*/
		  ?> 
		      <?php
		      if($month_no!=1&&$month_no!=2&&$month_no!=3)
		      $monthof=substr($feeTypeArr["Year"],0,4);
		      else
		      $monthof=substr($feeTypeArr["Year"],-4);
	    echo "Fees for the Month of ".$schoolyeararr[$month_no]."&nbsp;".$monthof."<br>";
		echo "<hr>";
		echo "<div width=\"49%\" style=\"float: left;text-align:left\">&nbsp&nbspParticulars</div>";
		echo "<div width=\"49%\" style=\"float: right;text-align:right\">Amount&nbsp&nbsp</div>";
		echo "<br>";
		echo "<hr>";
	   ?>
		  
		  <?php
	foreach($feeTypeArr as $fee_type=>$amount){ 
	if($fee_type!="Year"){
	?>
		
	    <div width="49%" style="float: left;"><?php echo $fee_type;?></div>
	    <div width="49%" style="float: right;"><?php echo number_format($amount,2);?></div>
	    <br>
	
   <?php 
	$total_amount+=$amount;}   }	  ?>
<hr>
<div width="49%" style="float: left;">Total</div>
	  <div width="49%" style="float: right;"><?php echo number_format($total_amount,2);?></div>
	  <br>
	 <div > (Rs. <?php echo ucwords(convert_number_to_words(intval($total_amount)));?> Only)</div>
	 <br>
	 
	 <?php if($info['class']=='09'){?>
	    <div >Computer Fee</div>
	    <br>
	    <?php } ?>
	  <div>Late Fine</div>
	  <hr>
	  <div>Total</div>
	  <hr>
	  <br>
	  <br>
	  <br>
	  <br>
	   <br>
	  <div style="text-align:right;padding:0px;background:#ffffff00;border:0;">Receiving Officer</div>
	
	<div style="page-break-before: always"> </div>
	
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
<input type="hidden" id="rollgroup_url" value="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajax_change_rollgroup.php" ?>">
 <script src="<?php echo $_SESSION[$guid]["absoluteURL"] ;?>/modules/Transport/js/jquery.dataTables.min.js"></script>
 <script>
	 $(document).ready(function(){
		 $("#year_filter,#gibbonYearGroupID").change(function(){
			var schoolYear=$("#year_filter").val();
			var yearGroup=$("#gibbonYearGroupID").val();
			var ajaxurl=$("#rollgroup_url").val();
			$.ajax({
				type: "POST",
				url: ajaxurl,
				data: {schoolYear:schoolYear,yearGroup:yearGroup},
				success: function(msg){
					//console.log(msg);
					$("#gibbonRollGroupID").empty().append("<option>Select Section</option>" + msg);
					
				}
			});
		 });
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

  newWin.document.write('<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><style>@media print{ @page {margin-top: 80mm;margin-bottom: 80mm;margin-left:50mm; margin-right:50mm; } }</style></head><body onload="window.print()">'+printText+'</body></html>');

  newWin.document.close();

  //setTimeout(function(){newWin.close();},10);

}
 </script>
 
 
<?php
};
?>
