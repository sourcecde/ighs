<?php 
include "../../config.php" ;
include "../../functions.php" ;
@session_start();
try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}
//getting the schoole name
try {
	$data=array(); 
	$sql="SELECT * FROM gibbonsetting WHERE scope='System' AND name IN ('organisationName','organisationHeader1','organisationHeader2') ORDER BY `gibbonSystemSettingsID`" ;
	$result=$connection2->prepare($sql);
	$result->execute($data);
	}
	catch(PDOException $e) { 
		print "<div class='error'>" . $e->getMessage() . "</div>" ; 
	}
	$row=$result->fetchAll() ;
	$organizationHeader=array();
	$i=0;
	foreach($row as $r){
	$organizationHeader[$i]=$r['value'];
	$i++;
	}
	//print_r($organizationHeader);
	// getting the student information
	try {
	$data=array('payment_master_id'=>$_REQUEST['pmid']); 
	$sql="SELECT payment_master.*,gibbonperson.*,gibbonstudentenrolment.*,gibbonyeargroup.name as class,gibbonrollgroup.name AS section,gibbonperson.account_number,`accountName`,`bankName`
	FROM payment_master 
	LEFT JOIN gibbonperson ON  payment_master.gibbonPersonID=gibbonperson.gibbonPersonID 
	LEFT JOIN gibbonstudentenrolment ON payment_master.gibbonStudentEnrolmentID=gibbonstudentenrolment.gibbonStudentEnrolmentID
	LEFT JOIN gibbonyeargroup ON gibbonstudentenrolment.gibbonYearGroupId=gibbonyeargroup.gibbonYearGroupId  
	LEFT JOIN gibbonrollgroup ON gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID
 	LEFT JOIN `payment_bankaccount` ON `payment_bankaccount`.`bankID`=`payment_master`.`bankID` 
	LEFT JOIN `fee_bank_master` ON `fee_bank_master`.`bankMasterID` = `payment_master`.`cheque_bank` 
	WHERE `payment_master`.`payment_master_id`=:payment_master_id" ;
	$result=$connection2->prepare($sql);
	$result->execute($data);
	}
	catch(PDOException $e) { 
		print "<div class='error'>" . $e->getMessage() . "</div>" ; 
	}
	$studentinfo=$result->fetch() ;
		//echo $sql;
	
	// getting the fee breakups
	try {
	$data=array('payment_master_id'=>$_REQUEST['pmid']); 
	$sql="SELECT SUM(net_amount) AS net_amount,fee_type_master.fee_type_name
	FROM fee_payable
	LEFT JOIN fee_type_master ON fee_payable.fee_type_master_id=fee_type_master.fee_type_master_id 
	WHERE fee_payable.payment_master_id=:payment_master_id GROUP BY fee_payable.fee_type_master_id" ;
	$result=$connection2->prepare($sql);
	$result->execute($data);
	}
	catch(PDOException $e) { 
		print "<div class='error'>" . $e->getMessage() . "</div>" ; 
	}
	$allfeebreakups=$result->fetchAll() ;
	$schoolyeararr=array(0=>'Yearly',1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December');
	//getting the month name where payment has been done
	 
	try {
	$data=array('payment_master_id'=>$_REQUEST['pmid']); 
	//$sql="SELECT month_no FROM fee_payable WHERE  GROUP BY month_no" ;
	$sql="SELECT month_name, gibbonschoolyear.name FROM fee_payable LEFT JOIN gibbonschoolyear ON gibbonschoolyear.gibbonSchoolYearID=fee_payable.gibbonSchoolYearID WHERE payment_master_id=:payment_master_id GROUP BY fee_payable.gibbonSchoolYearID,month_no" ;
	$result=$connection2->prepare($sql);
	$result->execute($data);
	}
	catch(PDOException $e) { 
		print "<div class='error'>" . $e->getMessage() . "</div>" ; 
	}
	$monthlist=$result->fetchAll();
	
	//$sql1="SELECT `month_name`, gibbonschoolyear.name FROM `transport_month_entry` LEFT JOIN gibbonschoolyear ON gibbonschoolyear.gibbonSchoolYearID=transport_month_entry.gibbonSchoolYearID WHERE  `payment_master_id`=".$_REQUEST['pmid'];
	$sql1="SELECT `month_name`, gibbonschoolyear.name FROM `transport_month_entry` LEFT JOIN gibbonschoolyear ON gibbonschoolyear.gibbonSchoolYearID=transport_month_entry.gibbonSchoolYearID WHERE  `payment_master_id`=".$_REQUEST['pmid'];
	$sql1.="  GROUP BY transport_month_entry.gibbonSchoolYearID, month_name";
	$result1=$connection2->prepare($sql1);
	$result1->execute();
	$t_monthlist=$result1->fetchAll();
	$month_arr=array('yearly'=>'Yearly','jan'=>'Jan','feb'=>'Feb','mar'=>'Mar','apr'=>'Apr','may'=>'May','jun'=>'Jun','jul'=>'Jul','aug'=>'Aug','sep'=>'Sep','oct'=>'Oct','nov'=>'Nov','dec'=>'Dec');
	foreach($t_monthlist as $a){
		if(!in_array($a,$monthlist))
			array_push($monthlist,$a);	
	}
	$monthlist=sortMonthName($monthlist);
	//For phone Number
	try{
	$sql="SELECT `phone1CountryCode`,`phone1` FROM `gibbonfamilyadult` 
		LEFT JOIN `payment_master` ON `gibbonfamilyadult`.`gibbonPersonID`=`payment_master`.`gibbonPersonID` 
		WHERE `gibbonfamilyadult`.`contactPriority`=1 AND `payment_master`.`payment_master_id`=".$_REQUEST['pmid'];
	$result=$connection2->prepare($sql);
	$result->execute();
	$phone=$result->fetch();
	//print_r($phone);
	}
	catch(PDOException $e) { 
		print "<div class='error'>" . $e->getMessage() . "</div>" ; 
	}
	$studentinfo['payment_mode'] = str_replace('_', ' ', $studentinfo['payment_mode']);
?>
<div style='width:100%'>
<span style="float:left; width:50%;">
	<center>(Office Copy)
<table width="400px" cellpadding="2" cellspacing="0" border="0" style="margin-right:30px;">
  <tr>
    <th align="center" style="padding-top:0px; font-family:Arial, Helvetica, sans-serif; font-size:25px; color:#000000;"><?php echo $organizationHeader[0];?></th>
  </tr>
  <tr>
    <td align="center" style="font-family:Arial, Helvetica, sans-serif; font-size:18px; color:#000000; white-space:pre;"><?php echo $organizationHeader[1];?></td>
  </tr>
  <tr>
    <td align="center" style="font-family:Arial, Helvetica, sans-serif; font-size:18px; color:#000000; white-space:pre;"><?php echo $organizationHeader[2];?></td>
  </tr>
   <tr>
    <td align="center" style="font-family:Arial, Helvetica, sans-serif; font-size:16px; color:#000000; font-weight:bold; padding-top:5px;">Fee Receipt</td>
  </tr>
   <tr>
    <td><table width="100%"  cellpadding="0" cellspacing="0" border="0"><tr><td align="left" style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000; font-weight:bold; padding-top:4px;">Voucher No.<?php $a=$studentinfo['voucher_number']; if($a!=0){echo substr($a,strlen($a)-7);}?></td><td align="right" style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000; font-weight:bold; padding-top:4px;">Date : <?php echo dmydate($studentinfo['payment_date']);?></td></tr></table></td>
  </tr>
   <tr>
    <td><table width="100%"  cellpadding="0" cellspacing="0" border="0"><tr><td  align="left" style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000;">Student Name : <?php echo $studentinfo['officialName'];?></td><td align="right" style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000; padding-top:5px;">Account No : <?php $a=$studentinfo['account_number']; echo substr($a,strlen($a)-5);?></td></tr></table>
  </tr>
   <tr>
  	<td style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000;">Class: <?php echo $studentinfo['class'];?>&nbsp;<?php echo SectionFormater($studentinfo['section']);?><span style="float:right"> Phone: <?=$phone['phone1']?></span></td>
  </tr>
   <tr>
    <td align="left" style="font-family:Arial, Helvetica, sans-serif; font-size:16px; color:#000000; padding-top:10px;">Fees paid for the Month of : <small><?php foreach ($monthlist as $value) { echo $month_arr[$value[0]]."-"; echo getYear($value[0],$value[1])."&nbsp"; }?></small>
    	
   </td>
  </tr>
  <tr>
    <td>
    	<table width="100%" cellpadding="0" cellspacing="0" border="0">
    		 <tr>
    		<th colspan="2" style="border-top:1px solid #000000; border-bottom:1px solid #000000;"><table width="100%"  cellpadding="0" cellspacing="0" border="0"><tr><td align="left" style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000; font-weight:bold; padding-top:4px;">Fee Particulars</td><td align="right" style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000; font-weight:bold; padding-top:4px;">Amount</td></tr></table></th>
  			</tr>
  			<?php 
  			foreach ($allfeebreakups as $value) { ?>
  			 <tr>
    		<td  align="left" style="font-family:Arial, Helvetica, sans-serif; font-size:12.5px; font-weight:bold; color:#000000; padding-top:2px; padding-bottom:2px;white-space:nowrap;<?php if(strlen($value['fee_type_name'])>=20){echo "font-size:11px";}?>"><?php echo $value['fee_type_name']?></td>
    		<td  align="right" style="font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000000; padding-top:2px; padding-bottom:2px;"><?php echo $value['net_amount']?></td> 
  			</tr>
  			<?php } ?>
  			<?php if($studentinfo['transport_month_entryid']){
  				try {
	$sql="SELECT SUM(price) tot_transport_price from transport_month_entry where payment_master_id=".$_REQUEST['pmid'];
	$result=$connection2->prepare($sql);
	$result->execute($data);
	$transportinfo=$result->fetch() ;	
	}
	catch(PDOException $e) { 
		print "<div class='error'>" . $e->getMessage() . "</div>" ; 
	}
	
  				?>
  			 <tr>
    		<td  align="left" style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#000000; padding-top:2px; padding-bottom:2px;">Transport Fee</td>
    		<td  align="right" style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000; padding-top:2px; padding-bottom:2px;"><?php echo $transportinfo['tot_transport_price']?></td> 
  			</tr>
  			<?php } ?>
			<tr>
    		<td  align="left" style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#000000; padding-top:2px; padding-bottom:2px;">Fine</td>
    		<td  align="right" style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000; padding-top:2px; padding-bottom:2px;"><?php echo $studentinfo['fine_amount'];?></td> 
  			</tr>
  			<tr>
    		<td style="border-top:1px solid #000000; border-bottom:1px solid #000000; font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000; font-weight:bold; ">Total</td>
    		<td align="right" style="border-top:1px solid #000000; border-bottom:1px solid #000000; font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000; font-weight:bold; "><?php echo $studentinfo['net_total_amount'];?><br>
			<span style="white-space:nowrap;">(<?php echo ucwords(convert_number_to_words(intval($studentinfo['net_total_amount'])));?> Only.)</span></td>
  			</tr>
    	</table>
    </td>
  </tr>
  <tr>
  	<td style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000;">Payment Mode :<?php echo ucwords($studentinfo['payment_mode']);?></td>
  </tr>
   <tr>
  	<td style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000;">Bank:<?php echo $studentinfo['bankName'];?></td>
  </tr>
  <tr>
  	<td style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000;">Cheque/Draft/Ref No. <?php if($studentinfo['payment_mode']!='cash'){echo $studentinfo['cheque_no'];}else{echo ": N/A";}?></td>
  </tr>
  <tr>
  	<td style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000;">Cheque/Draft/Transfer Date : <?php if($studentinfo['payment_mode']!='cash'){echo dmydate($studentinfo['cheque_date']);}else{echo "N/A";}?></td>
  </tr>
    <tr>
  	<td style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000;"><?php if($studentinfo['payment_mode']!='cash'){echo "Deposited in ".$studentinfo['accountName'];}?></td>
  </tr>
  <tr>
  	<td style="padding-top:50px;"><table width="100%"  cellpadding="0" cellspacing="0" border="0"><tr><td align="left" width="50%" style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000; padding-top:4px;"><!--Date &amp; Time : <?php  date_default_timezone_set($_SESSION[$guid]["timezone"]); echo date('d/m/y h:i');?>--></td><td width="50%"  align="right" style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000; padding-top:4px;">Signature<br />
(with Stamp)</td></tr></table></td>
  </tr>
</table>
</center>
</span>


<span style="float:right; width:50%;">
<center>(Parent's Copy)
<table width="400px" cellpadding="2" cellspacing="0" border="0" style="margin-left:30px;">
  <tr>
    <th align="center" style="padding-top:0px; font-family:Arial, Helvetica, sans-serif; font-size:25px; color:#000000;"><?php echo $organizationHeader[0];?></th>
  </tr>
  <tr>
    <td align="center" style="font-family:Arial, Helvetica, sans-serif; font-size:18px; color:#000000; white-space:pre;"><?php echo $organizationHeader[1];?></td>
  </tr>
  <tr>
    <td align="center" style="font-family:Arial, Helvetica, sans-serif; font-size:18px; color:#000000; white-space:pre;"><?php echo $organizationHeader[2];?></td>
  </tr>
   <tr>
    <td align="center" style="font-family:Arial, Helvetica, sans-serif; font-size:16px; color:#000000; font-weight:bold; padding-top:5px;">Fee Receipt</td>
  </tr>
   <tr>
    <td><table width="100%"  cellpadding="0" cellspacing="0" border="0"><tr><td align="left" style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000; font-weight:bold; padding-top:4px;">Voucher No.<?php $a=$studentinfo['voucher_number']; if($a!=0){echo substr($a,strlen($a)-7);}?></td><td align="right" style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000; font-weight:bold; padding-top:4px;">Date : <?php echo dmydate($studentinfo['payment_date']);?></td></tr></table></td>
  </tr>
   
   <tr>
		<td><table width="100%"  cellpadding="0" cellspacing="0" border="0"><tr><td  align="left" style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000;">Student Name : <?php echo $studentinfo['officialName'];?></td><td align="right" style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000; padding-top:5px;">Account No : <?php $a=$studentinfo['account_number']; echo substr($a,strlen($a)-5);?></td></tr></table>
   </tr>
  <tr>
  	<td style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000;">Class: <?php echo $studentinfo['class'];?>&nbsp;<?php echo SectionFormater($studentinfo['section']);?><span style="float:right"> Phone: <?=$phone['phone1']?></span></td>
  </tr>
   <tr>
    <td align="left" style="font-family:Arial, Helvetica, sans-serif; font-size:16px; color:#000000; padding-top:10px;">Fees paid for the Month of : <small><?php foreach ($monthlist as $value) { echo $month_arr[$value[0]]."-"; echo getYear($value[0],$value[1])."&nbsp"; }?></small>
    	
   </td>
  </tr>
  <tr>
    <td>
    	<table width="80%" cellpadding="0" cellspacing="0" border="0">
    		 <tr>
    		<th colspan="2" style="border-top:1px solid #000000; border-bottom:1px solid #000000;"><table width="100%"  cellpadding="0" cellspacing="0" border="0"><tr><td align="left" style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000; font-weight:bold; padding-top:4px;">Fee Particulars</td><td align="right" style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000; font-weight:bold; padding-top:4px;">Amount</td></tr></table></th>
  			</tr>
  			<?php 
  			foreach ($allfeebreakups as $value) { ?>
  			 <tr>
    		<td  align="left" style="font-family:Arial, Helvetica, sans-serif; font-size:12.5px; font-weight:bold; color:#000000; padding-top:2px; padding-bottom:2px;white-space:nowrap;<?php if(strlen($value['fee_type_name'])>=25){echo "font-size:11px;";}?>"><?php echo $value['fee_type_name']?></td>
    		<td  align="right" style="font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000000; padding-top:2px; padding-bottom:2px;"><?php echo $value['net_amount']?></td> 
  			</tr>
  			<?php } ?>
  			<?php if($studentinfo['transport_month_entryid']){
  				try {
	$sql="SELECT SUM(price) tot_transport_price from transport_month_entry where payment_master_id=".$_REQUEST['pmid'];
	$result=$connection2->prepare($sql);
	$result->execute($data);
	$transportinfo=$result->fetch() ;	
	}
	catch(PDOException $e) { 
		print "<div class='error'>" . $e->getMessage() . "</div>" ; 
	}
	
  				?>
  			 <tr>
    		<td  align="left" style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#000000; padding-top:2px; padding-bottom:2px;">Transport Fee</td>
    		<td  align="right" style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000; padding-top:2px; padding-bottom:2px;"><?php echo $transportinfo['tot_transport_price']?></td> 
  			</tr>
  			<?php } ?>
			<tr>
    		<td  align="left" style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#000000; padding-top:2px; padding-bottom:2px;">Fine</td>
    		<td  align="right" style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000; padding-top:2px; padding-bottom:2px;"><?php echo $studentinfo['fine_amount'];?></td> 
  			</tr>
  			<tr>
    		<td style="border-top:1px solid #000000; border-bottom:1px solid #000000; font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000; font-weight:bold; ">Total</td>
			<td align="right" style="border-top:1px solid #000000; border-bottom:1px solid #000000; font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000; font-weight:bold; "><?php echo $studentinfo['net_total_amount'];?><br>
			<span style="white-space:nowrap;">(<?php echo ucwords(convert_number_to_words(intval($studentinfo['net_total_amount'])));?> Only.)</span></td>
  			</tr>
    	</table>
    </td>
  </tr>
   <tr>
  	<td style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000;">Payment Mode :<?php echo ucwords($studentinfo['payment_mode']);?></td>
  </tr>
   <tr>
  	<td style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000;">Bank:<?php echo $studentinfo['bankName'];?></td>
  </tr>
  <tr>
  	<td style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000;">Cheque/Draft/Ref No. <?php if($studentinfo['payment_mode']!='cash'){echo $studentinfo['cheque_no'];}else{echo ": N/A";}?></td>
  </tr>
  <tr>
  	<td style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000;">Cheque/Draft/Transfer Date : <?php if($studentinfo['payment_mode']!='cash'){echo dmydate($studentinfo['cheque_date']);}else{echo "N/A";}?></td>
  </tr> 
  <?php if($studentinfo['payment_mode']!='cash'){
  echo "<tr><td><br/></td></tr>";
  } ?>
  	<td style="padding-top:50px;"><table width="100%"  cellpadding="0" cellspacing="0" border="0"><tr><td align="left" width="50%" style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000; padding-top:4px;"><!--Date &amp; Time: <?php  echo date('d/m/y h:i');?>--></td>
	<td width="50%"  align="right" style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000; padding-top:4px;">Signature<br />
(with Stamp)</td></tr></table></td>
  </tr>
</table>
</center>
</span>
</div>
<script type="text/javascript">
	window.print();
</script>
<?php function dmydate($ymd)
{
	$tempdate=explode("-", $ymd);
	return $tempdate[2].'/'.$tempdate[1].'/'.$tempdate[0];
}

function getYear($m,$y){
	switch($m){
		case 'yearly':
		$r="( $y )";
		break;
		case 'jan':
		case 'feb':
		case 'mar':
		$r=substr($y,7,2);
		break;
		case 'apr':
		case 'may':
		case 'jun':
		case 'jul':
		case 'aug':
		case 'sep':
		case 'oct':
		case 'nov':
		case 'dec':
		$r=substr($y,2,2);
		break;
	}
	return $r;
}
function sortMonthName($arr){
	$month_sequence=array('yearly'=>0,'apr'=>4,'may'=>5,'jun'=>6,'jul'=>7,'aug'=>8,'sep'=>9,'oct'=>10,'nov'=>11,'dec'=>12,'jan'=>13,'feb'=>14,'mar'=>15);
	$r_array=array();
	foreach($arr as $k){
		$a=$month_sequence[$k['month_name']];
		$r_array[$a]=array($k['month_name'],$k['name']);
	}
	ksort($r_array);
	return $r_array;
}
?>
