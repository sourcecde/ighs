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
$month_sequence=array('Yearly','April','May','June','July','August','September','October','November','December','January','February','March');

$sql="SELECT `gibbonperson`.`gibbonPersonID`,`preferredName`,`account_number`,`gibbonstudentenrolment`.`rollOrder` FROM `gibbonperson` 
		LEFT JOIN `gibbonstudentenrolment` ON `gibbonstudentenrolment`.`gibbonPersonID`=`gibbonperson` .`gibbonPersonID` 
		WHERE `gibbonperson`.`gibbonPersonID` IN ($ids) AND `gibbonSchoolYearID`=$print_year
		ORDER BY `gibbonstudentenrolment`.`rollOrder`";
$result=$connection2->prepare($sql);
$result->execute();
$studentsData=$result->fetchAll();	

$sql="SELECT `gibbonPersonID`,`payment_master_id`,`net_total_amount`,`fine_amount`,`voucher_number`,`payment_date` FROM `payment_master` WHERE `gibbonPersonID` IN ($ids) AND `gibbonSchoolYearID`=$print_year ORDER BY `payment_date`";
	$result=$connection2->prepare($sql);
	$result->execute();
	$pmnt=$result->fetchAll();
	$payment_array=array();
	$fee_array=array();
	$id_arr=array();
	$total=array();
	foreach($pmnt as $p){
		$payment_array[$p['gibbonPersonID']+0][$p['payment_date']][$p['payment_master_id']]=array($p['net_total_amount'],$p['voucher_number']);
		$fee_array[$p['gibbonPersonID']+0][$p['payment_master_id']+0]['-']['Fine']=$p['fine_amount'];
		$id_arr[]=$p['payment_master_id'];
		if(!array_key_exists($p['gibbonPersonID']+0,$total))
			$total[$p['gibbonPersonID']+0]=$p['net_total_amount'];
		else
			$total[$p['gibbonPersonID']+0]+=$p['net_total_amount'];
	}
	$id_str=implode(',',$id_arr);
	$sql1="SELECT  `gibbonPersonID`,`month_no`, `net_amount`,`fee_payable`.`payment_master_id`, `fee_type_master`.`fee_type_name` 
		FROM `fee_payable` 
		LEFT JOIN fee_rule_master ON fee_payable.rule_id=fee_rule_master.fee_rule_master_id 
		LEFT JOIN fee_type_master ON fee_type_master.fee_type_master_id=fee_payable.fee_type_master_id 
		WHERE `fee_payable`.`payment_master_id` IN ($id_str) AND `net_amount` > 0";
	$result1=$connection2->prepare($sql1);
	$result1->execute();
	$fee=$result1->fetchAll();
	foreach($fee as $f){
		$m=$month_arr2[$month_arr1[$f['month_no']]];
		$fee_array[$f['gibbonPersonID']+0][$f['payment_master_id']+0][$m][$f['fee_type_name']]=$f['net_amount'];
	}
	$sql2="SELECT `gibbonPersonID`,`month_name`,`price`,`payment_master_id` FROM `transport_month_entry`
			WHERE `payment_master_id` IN ($id_str)";
	$result2=$connection2->prepare($sql2);
	$result2->execute();
	$trnsprt=$result2->fetchAll();
	foreach($trnsprt as $t){
		$m=$month_arr2[$t['month_name']];
		$fee_array[$t['gibbonPersonID']+0][$t['payment_master_id']+0][$m]['Transport']=$t['price'];
	}
/*         */
	$sql2="SELECT `name` FROM `gibbonschoolyear` WHERE `gibbonSchoolYearID`=".$print_year;
	$result2=$connection2->prepare($sql2);
	$result2->execute();
	$yearname=$result2->fetch();
	$sql3="SELECT `gibbonrollgroup`.`name` as `section`,`gibbonyeargroup`.`name` as `class` FROM `gibbonrollgroup`,`gibbonyeargroup` WHERE `gibbonrollgroup`.`gibbonYearGroupID`=`gibbonyeargroup`.`gibbonYearGroupID` AND `gibbonRollGroupID`=".$print_section;
	$result3=$connection2->prepare($sql3);
	$result3->execute();
	$sectionname=$result3->fetch();	$sql="SELECT * FROM gibbonsetting WHERE gibbonSystemSettingsID='147'";	$result=$connection2->prepare($sql);	$result->execute();	$header2=$result->fetch();
/*      */
 ?>
 <center>
 <table width='100%'>
 <thead> <tr>	<td colspan='8'>		<p style='text-align:center; font-weight:bold; font-size:14px; margin: 2px;'>INDRA GOPAL HIGH SCHOOL</p>		<p style='text-align:center;  font-size:12px;margin: 2px;'><?php echo $header2["value"];?></p>		<p style='text-align:center;  font-size:12px;margin: 2px;'>			Day Wise Ledger Of Class: <i><?=$sectionname['class']." ".$sectionname['section']?></i>&nbsp;For Year: <i><?=$yearname['name']?></i>		</p>	</td> </tr>
 <tr style='border:1px solid;'>
	<th>Student</th><th>Acc No</th><th>Roll</th><th>Day</th><th>Total</th><th><small>Voucher No</small></th><th>Fee Head</th><th>Amount</th>
 </tr>
 </thead>
<?php
 foreach($studentsData as $s){
$perosnID=$s['gibbonPersonID']+0;
$acc_no=$s['account_number']+0;
if(!array_key_exists($perosnID,$fee_array))
	continue;
$i=0;
$L=0;
//Counting
foreach($fee_array[$perosnID] as $a)
	foreach($a as $b)
		foreach($b as $c)
			if($c>0)
				$L++;
	echo "<tbody class='nobreak'>";
	foreach($payment_array[$perosnID] as $k=>$pi){
		$d=date('d/m/y',strtotime($k));
		foreach($pi as $id=>$p){
			//Count
				$j=0;
				$N=0;
				foreach($fee_array[$perosnID][$id] as $a)
					foreach($a as $b)
						if($b>0)
							$N++;
			//Count
			$formatted_total=number_format((float)$p[0],2,'.','');
			$voucher=$p[1]+0;
			
			foreach($month_sequence as $m){
			if(!array_key_exists($m,$fee_array[$perosnID][$id]))
				continue;				
				foreach($fee_array[$perosnID][$id][$m] as $h=>$f){
					echo "<tr>";
					if($i++==0){
						echo "<td style='text-align:center' class='small-font'><b>".$s['preferredName']."</b></td><td class='small-font'>$acc_no</td><td class='small-font'>".$s['rollOrder']."</td>";					}					else{						echo "<td></td><td></td><td></td>";					}
					if($j++==0){
						echo "<td class='small-font'>$d</td><td  class='rightA small-font'>$formatted_total</td><td  class='rightA small-font'>$voucher</td>";					}					else{						echo "<td></td><td></td><td></td>";					}
					echo "<td class='small-font'>$h - $m</td><td class='rightA small-font'>$f</td>";
					echo "</tr>";	
				}				
			}
			foreach($fee_array[$perosnID][$id]['-'] as $h=>$f){
				if($f>0)
				echo "<tr><td></td><td></td><td></td><td></td><td></td><td></td><td class='small-font'>$h</td><td class='rightA small-font'>$f</td></tr>";	
			}
			
		}
	}
	$formatted_sub=number_format((float)$total[$perosnID],2,'.','');
	echo "<tr class='footer' ><td colspan='4'>Total: </td><td>$formatted_sub</td><td colspan='3'></td></tr>";	echo "</tbody>";
} 
?>	</tbody>
 </table>
</center> 
<style>
.rightA{
	text-align:right;
}
.footer td{
	text-align:right;
	font-weight:bold;	font-size: 12px;
}.footer{	 border: 1px solid black;}
table {
	border-collapse: collapse;	border: 1px solid black;
}.small-font{	font-size:12px;}th{	font-size:12px;}.nobreak{	-webkit-column-break-inside: avoid;          page-break-inside: avoid;               break-inside: avoid;}thead {display: table-header-group;}
</style>
<script>
window.print();
</script>
<?php
}
 ?>