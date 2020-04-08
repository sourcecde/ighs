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
@session_start() ;
if($_POST){
extract($_POST);
$perosnIDs=json_decode($personID_array,true);
$ids=implode(',',$perosnIDs);
$month_arr1=array(0=>'yearly',1=>'jan',2=>'feb',3=>'mar',4=>'apr',5=>'may',6=>'jun',7=>'jul',8=>'aug',9=>'sep',10=>'oct',11=>'nov',12=>'dec');
$month_arr2=array('yearly'=>'Yearly','jan'=>'January','feb'=>'February','mar'=>'March','apr'=>'April','may'=>'May','jun'=>'June','jul'=>'July','aug'=>'August','sep'=>'September','oct'=>'October','nov'=>'November','dec'=>'December');
//$month_sequence=array('Yearly','April','May','June','July','August','September','October','November','December','January','February','March');

$month_sequence=array('Yearly','January','February','March','April','May','June','July','August','September','October','November','December');

$sql="SELECT `gibbonperson`.`gibbonPersonID`,`preferredName`,`account_number`,`gibbonstudentenrolment`.`rollOrder` FROM `gibbonperson` 
		LEFT JOIN `gibbonstudentenrolment` ON `gibbonstudentenrolment`.`gibbonPersonID`=`gibbonperson` .`gibbonPersonID` 
		WHERE `gibbonperson`.`gibbonPersonID` IN ($ids)
		ORDER BY `gibbonstudentenrolment`.`rollOrder`";
$result=$connection2->prepare($sql);
$result->execute();
$studentsData=$result->fetchAll();	
$sql="SELECT  `gibbonPersonID`,`month_no`,`fee_payable`.`amount`, `concession`, `net_amount`, `payment_staus`, `payment_master_id`,  `voucher_number`,`fee_type_master`.`fee_type_name` 
		FROM `fee_payable` 
		LEFT JOIN fee_rule_master ON fee_payable.rule_id=fee_rule_master.fee_rule_master_id 
		LEFT JOIN fee_type_master ON fee_type_master.fee_type_master_id=fee_payable.fee_type_master_id 
		WHERE `gibbonPersonID` IN ($ids) AND `fee_payable`.`gibbonSchoolYearID`=$print_year AND (`net_amount` != 0 OR `concession`!=0)";
		//WHERE `gibbonPersonID` IN ($ids) AND `fee_payable`.`gibbonSchoolYearID`=$print_year AND `net_amount` > 0";
	$result=$connection2->prepare($sql);
	$result->execute();
	$dataDB=$result->fetchAll();
	$fee_data=array();
	$payment_status['Paid']=array();
	$payment_status['Unpaid']=array();
	$total=array();
	foreach($dataDB as $d){
		$m=$month_arr2[$month_arr1[$d['month_no']]];
		$status=ucfirst($d['payment_staus']);				if(array_key_exists($d['gibbonPersonID']+0, $fee_data)){			if(array_key_exists($m, $fee_data[$d['gibbonPersonID']+0])){				if(array_key_exists($d['fee_type_name'], $fee_data[$d['gibbonPersonID']+0][$m])){					$fee_data[$d['gibbonPersonID']+0][$m][$d['fee_type_name']][0]+=$d['amount'];					$fee_data[$d['gibbonPersonID']+0][$m][$d['fee_type_name']][1]+=$d['concession'];					$fee_data[$d['gibbonPersonID']+0][$m][$d['fee_type_name']][2]+=$d['net_amount'];				}				else{					$fee_data[$d['gibbonPersonID']+0][$m][$d['fee_type_name']]=array($d['amount'],$d['concession'],$d['net_amount'],$status,$d['payment_master_id'],$d['voucher_number']+0);				}			}			else{				$fee_data[$d['gibbonPersonID']+0][$m][$d['fee_type_name']]=array($d['amount'],$d['concession'],$d['net_amount'],$status,$d['payment_master_id'],$d['voucher_number']+0);			}		}		else{			$fee_data[$d['gibbonPersonID']+0][$m][$d['fee_type_name']]=array($d['amount'],$d['concession'],$d['net_amount'],$status,$d['payment_master_id'],$d['voucher_number']+0);		}
		
		if(array_key_exists($d['gibbonPersonID']+0,$payment_status[$status])){
			if(array_key_exists($m,$payment_status[$status][$d['gibbonPersonID']+0]))
				$payment_status[$status][$d['gibbonPersonID']+0][$m]+=$d['net_amount'];
			else
				$payment_status[$status][$d['gibbonPersonID']+0][$m]=$d['net_amount']; 
		}
		else
			$payment_status[$status][$d['gibbonPersonID']+0][$m]=$d['net_amount']; 
		
		if(array_key_exists($d['gibbonPersonID']+0,$total)){
			if(array_key_exists($m,$total[$d['gibbonPersonID']+0]))
				$total[$d['gibbonPersonID']+0][$m]+=$d['net_amount'];
			else
				$total[$d['gibbonPersonID']+0][$m]=$d['net_amount'];
		}
		else
				$total[$d['gibbonPersonID']+0][$m]=$d['net_amount'];
	}
	
	$sql1="SELECT `transport_month_entry`.`gibbonPersonID`,`month_name`,`price`,`transport_month_entry`.`payment_master_id`,`payment_master`.`voucher_number` FROM `transport_month_entry`
			LEFT JOIN `payment_master` ON `payment_master`.`payment_master_id`= `transport_month_entry`.`payment_master_id`
			WHERE `transport_month_entry`.`gibbonPersonID` IN ($ids) AND `transport_month_entry`.`gibbonSchoolYearID`=$print_year";
	$result1=$connection2->prepare($sql1);
	$result1->execute();
	$trnsprt=$result1->fetchAll();
	foreach($trnsprt as $t){
		$m=$month_arr2[$t['month_name']];
		$status=$t['payment_master_id']>0?'Paid':'Unpaid';
		$fee_data[$t['gibbonPersonID']+0][$m]['Transport']=array($t['price'],'0.00',$t['price'],$status,$t['payment_master_id'],$t['voucher_number']+0);
		if(array_key_exists($t['gibbonPersonID']+0,$payment_status[$status])){
			if(array_key_exists($m,$payment_status[$status][$t['gibbonPersonID']+0]))
				$payment_status[$status][$t['gibbonPersonID']+0][$m]+=$t['price'];
			else
				$payment_status[$status][$t['gibbonPersonID']+0][$m]=$t['price'];
		}
		else
			$payment_status[$status][$t['gibbonPersonID']+0][$m]=$t['price'];
		
		if(array_key_exists($m,$total[$t['gibbonPersonID']+0]))
			$total[$t['gibbonPersonID']+0][$m]+=$t['price'];
		else
			$total[$t['gibbonPersonID']+0][$m]=$t['price'];
	}
	
	$sql2="SELECT `name` FROM `gibbonschoolyear` WHERE `gibbonSchoolYearID`=".$print_year;
	$result2=$connection2->prepare($sql2);
	$result2->execute();
	$yearname=$result2->fetch();
	$sql3="SELECT `gibbonrollgroup`.`name` as `section`,`gibbonyeargroup`.`name` as `class` FROM `gibbonrollgroup`,`gibbonyeargroup` WHERE `gibbonrollgroup`.`gibbonYearGroupID`=`gibbonyeargroup`.`gibbonYearGroupID` AND `gibbonRollGroupID`=".$print_section;
	$result3=$connection2->prepare($sql3);
	$result3->execute();
	$sectionname=$result3->fetch();
	$sql="SELECT * FROM gibbonsetting WHERE gibbonSystemSettingsID='147'";	$result=$connection2->prepare($sql);	$result->execute();	$header2=$result->fetch();
 ?>
 <table width='100%'>
 <thead>  <tr>	<td colspan='10'>		<p style='text-align:center; font-weight:bold; font-size:14px; margin: 2px;'>INDRA GOPAL HIGH SCHOOL</p>		<p style='text-align:center;  font-size:12px;margin: 2px;'><?php echo $header2["value"];?></p>		<p style='text-align:center;  font-size:12px;margin: 2px;'>			Month Wise Ledger Of Class: <i><?=$sectionname['class']." ".$sectionname['section']?></i>&nbsp;For Year: <i><?=$yearname['name']?></i>		</p>	</td> </tr>
 <tr style='border:1px solid;'>
	<th>Student</th><th>Month</th><th>Total</th><th>Paid</th><th>Unpaid</th><th>Fee Head</th><th>Amount</th><th><small>Concession</small></th><th><small>Net Amount</small></th><th><small>Status</small></th>
 </tr>
 </thead>
 <tbody>
<?php
 foreach($studentsData as $s){
$perosnID=$s['gibbonPersonID']+0;
$acc_no=$s['account_number']+0;
if(!array_key_exists($perosnID,$fee_data))
	continue;
$i=0;
$L=0;
//For Counting
	foreach($month_sequence as $m){
		if(!array_key_exists($m,$fee_data[$perosnID]))
			continue;
		$L+=sizeOf($fee_data[$perosnID][$m]);
	}
//For Counting	
	foreach($month_sequence as $m){
		$j=0;		$border="";
		if(!array_key_exists($m,$fee_data[$perosnID]))
			continue;
		if(array_key_exists($perosnID,$payment_status['Paid']))
			$paid=array_key_exists($m,$payment_status['Paid'][$perosnID])?number_format($payment_status['Paid'][$perosnID][$m],2,'.',''):'0.00';
		else
			$paid='0.00';
		if(array_key_exists($perosnID,$payment_status['Unpaid']))
			$unpaid=array_key_exists($m,$payment_status['Unpaid'][$perosnID])?number_format($payment_status['Unpaid'][$perosnID][$m],2,'.',''):'0.00';
		else
			$unpaid='0.00';
		$formatted_total=number_format($total[$perosnID][$m],2,'.','');
		$n=sizeOf($fee_data[$perosnID][$m]);
		foreach($fee_data[$perosnID][$m] as $k=>$f){
			echo "<tr>";
			if($i++==0)
				echo "<td rowspan='$L' style='text-align:center'><b>".$s['preferredName']."</b><br>Acc No: <i>$acc_no</i><br>Roll: <i>".$s['rollOrder']."</i></td>";
			if($j++==0){				$border="border-top";
				echo "<td rowspan='$n' class='$border'>$m</td><td rowspan='$n' class='rightA $border'>$formatted_total</td><td rowspan='$n' class='rightA $border'>$paid</td><td rowspan='$n' class='rightA $border'>$unpaid</td>";			}			else{				$border="";			}
			echo "<td class='$border'><small>$k</small></td><td class='rightA $border'>$f[0]</td><td class='rightA $border'>$f[1]</td><td class='rightA $border'>$f[2]</td><td class='$border'>$f[3]</td>";
			echo "</tr>";
		}			
	}
	$sub_total=number_format((float)array_sum($total[$perosnID]), 2, '.', ',');
	$sub_paid=array_key_exists($perosnID,$payment_status['Paid'])?number_format((float)array_sum($payment_status['Paid'][$perosnID]), 2, '.', ','):'0.00';
	$sub_unpaid=array_key_exists($perosnID,$payment_status['Unpaid'])?number_format((float)array_sum($payment_status['Unpaid'][$perosnID]), 2, '.', ','):'0.00';	
	echo "<tr class='footer'><td colspan='2'></b>Total:</b></td><td>$sub_total</td><td>$sub_paid</td><td>$sub_unpaid</td><td colspan='5'></td></tr>";
} 
?>
</tbody>
 </table>
</center> 
<style>

.rightA{
	text-align:right;
}
.footer td{
	text-align:right;
	font-weight:bold;
}.footer{	 border: 1px solid black;}
table {
	border-collapse: collapse;	border: 1px solid black;
}.small-font{	font-size:12px;}th{	font-size:12px;}td{	font-size:12px;}.nobreak{	-webkit-column-break-inside: avoid;          page-break-inside: avoid;               break-inside: avoid;}thead {display: table-header-group;}.border-top{	border-top:.5px dashed;}
</style>
<script>
window.print();
</script>
<?php
}
 ?>