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
if($_POST){
extract($_POST);
$data=json_decode($dataString,true);
extract($data);
$month_arr1=array(0=>'yearly',1=>'jan',2=>'feb',3=>'mar',4=>'apr',5=>'may',6=>'jun',7=>'jul',8=>'aug',9=>'sep',10=>'oct',11=>'nov',12=>'dec');
$month_arr2=array('yearly'=>'Yearly','jan'=>'January','feb'=>'February','mar'=>'March','apr'=>'April','may'=>'May','jun'=>'June','jul'=>'July','aug'=>'August','sep'=>'September','oct'=>'October','nov'=>'November','dec'=>'December');
//$month_sequence=array('Yearly','April','May','June','July','August','September','October','November','December','January','February','March');

$month_sequence=array('Yearly','January','February','March','April','May','June','July','August','September','October','November','December');

if($action=='getMonthwise'){
	
	$sql="SELECT  `month_no`,`fee_payable`.`amount`, `concession`, `net_amount`, `payment_staus`, `payment_master_id`,  `voucher_number`,`fee_type_master`.`fee_type_name` 
		FROM `fee_payable` 
		LEFT JOIN fee_rule_master ON fee_payable.rule_id=fee_rule_master.fee_rule_master_id 
		LEFT JOIN fee_type_master ON fee_type_master.fee_type_master_id=fee_payable.fee_type_master_id 
		WHERE `gibbonPersonID`=$personID AND `fee_payable`.`gibbonSchoolYearID`=$year_id AND (`net_amount` != 0 OR `concession`!=0)";
		//WHERE `gibbonPersonID`=$personID AND `fee_payable`.`gibbonSchoolYearID`=$year_id AND `net_amount` > 0 ";
	$result=$connection2->prepare($sql);
	$result->execute();
	$dataDB=$result->fetchAll();
	$fee_data=array();
	$payment_status['Paid']=array();
	$payment_status['Unpaid']=array();
	$total=array();
	foreach($dataDB as $d){
		$m=$month_arr2[$month_arr1[$d['month_no']]];
		$status=ucfirst($d['payment_staus']);		if(array_key_exists($m, $fee_data)){			if(array_key_exists($d['fee_type_name'], $fee_data[$m])){				$fee_data[$m][$d['fee_type_name']][0]+=$d['amount'];				$fee_data[$m][$d['fee_type_name']][1]+=$d['concession'];				$fee_data[$m][$d['fee_type_name']][2]+=$d['net_amount'];			}			else{				$fee_data[$m][$d['fee_type_name']]=array($d['amount'],$d['concession'],$d['net_amount'],$status,$d['payment_master_id'],$d['voucher_number']+0);			}		}		else{
			$fee_data[$m][$d['fee_type_name']]=array($d['amount'],$d['concession'],$d['net_amount'],$status,$d['payment_master_id'],$d['voucher_number']+0);		}		
		if(array_key_exists($m,$payment_status[$status]))
			$payment_status[$status][$m]+=$d['net_amount'];
		else
			$payment_status[$status][$m]=$d['net_amount']; 
		if(array_key_exists($m,$total))
			$total[$m]+=$d['net_amount'];
		else
			$total[$m]=$d['net_amount'];
	}
	
	$sql1="SELECT `month_name`,`price`,`transport_month_entry`.`payment_master_id`,`payment_master`.`voucher_number` FROM `transport_month_entry`
			LEFT JOIN `payment_master` ON `payment_master`.`payment_master_id`= `transport_month_entry`.`payment_master_id`
			WHERE `transport_month_entry`.`gibbonPersonID`=$personID AND `transport_month_entry`.`gibbonSchoolYearID`=$year_id";
	$result1=$connection2->prepare($sql1);
	$result1->execute();
	$trnsprt=$result1->fetchAll();
	foreach($trnsprt as $t){
		$m=$month_arr2[$t['month_name']];
		$status=$t['payment_master_id']>0?'Paid':'Unpaid';
		$fee_data[$m]['Transport']=array($t['price'],'0.00',$t['price'],$status,$t['payment_master_id'],$t['voucher_number']+0);
		if(array_key_exists($m,$payment_status[$status]))
			$payment_status[$status][$m]+=$t['price'];
		else
			$payment_status[$status][$m]=$t['price'];
		if(array_key_exists($m,$total))
			$total[$m]+=$t['price'];
		else
			$total[$m]=$t['price'];
	}
	
	$head="<table width='100%'>
			<tr>
				<th>Month</th>
				<th>Amount</th>
				<th>Paid</th>
				<th>Unpaid</th>
			</tr>";
	$head1="";	
	$url=$_SESSION[$guid]["absoluteURL"]."/modules/Fee/print_payment.php?pmid=";
	foreach($month_sequence as $m){
		if(!array_key_exists($m,$fee_data))
			continue;
		$paid=array_key_exists($m,$payment_status['Paid'])?number_format($payment_status['Paid'][$m],2,'.',''):'0.00';
		$unpaid=array_key_exists($m,$payment_status['Unpaid'])?number_format($payment_status['Unpaid'][$m],2,'.',''):'0.00';
		$formatted_total=number_format($total[$m],2,'.','');
		$head1.="<tr class='headSlide'><td>$m</td><td class='rightA'>$formatted_total</td><td class='rightA'>$paid</td><td class='rightA'>$unpaid</td></tr>";
		$head1.="<tr style='display:none;' class='sub_table'>
					<td colspan='4'>
					<table style='width:100%;' >
					<tr><th>Fee Head</th><th>Amount</th><th>Concession</th><th>Net Amount</th><th>Payment Status</th></tr>";
					foreach($fee_data[$m] as $k=>$f){
						$link=$f[3]=='Paid'?"- <a href='$url$f[4]' target='_blank'>$f[5]</a>":"";
					$head1.="<tr><td>$k</td><td class='rightA'>$f[0]</td><td class='rightA'>$f[1]</td><td class='rightA'>$f[2]</td><td>$f[3] $link</td></tr>";
					}
			$head1.="</table>
					</td>
				</tr>";
		
	}
	$sub_total=number_format((float)array_sum($total), 2, '.', ',');
	$sub_paid=number_format((float)array_sum($payment_status['Paid']), 2, '.', ',');
	$sub_unpaid=number_format((float)array_sum($payment_status['Unpaid']), 2, '.', ',');
	$head2="<tr class='headSlide'> <th>Total:</th> <th class='rightA'>$sub_total</th><th class='rightA'>$sub_paid</th> <th class='rightA'>$sub_unpaid</th></tr></table>";
	echo $head.$head1.$head2;
	//print_r($payment_status);		
		
}	
else if($action='getDaywise'){
	$sql="SELECT `payment_master_id`,`net_total_amount`,`fine_amount`,`voucher_number`,`payment_date` FROM `payment_master` WHERE `gibbonPersonID`=$personID AND `gibbonSchoolYearID`=$year_id ORDER BY `payment_date`";
	$result=$connection2->prepare($sql);
	$result->execute();
	$pmnt=$result->fetchAll();
	if($result->rowCount()>0){
	$payment_array=array();
	$fee_array=array();
	$id_arr=array(); 
	$total=0;
	foreach($pmnt as $p){
		$payment_array[$p['payment_date']][$p['payment_master_id']]=array($p['net_total_amount'],$p['voucher_number']);
		$fee_array[$p['payment_master_id']+0]['-']['Fine']=$p['fine_amount'];
		$id_arr[]=$p['payment_master_id'];
		$total+=$p['net_total_amount'];
	}
	$id_str=implode(',',$id_arr);
	$sql1="SELECT  `month_no`, `net_amount`,`fee_payable`.`payment_master_id`, `fee_type_master`.`fee_type_name` 
		FROM `fee_payable` 
		LEFT JOIN fee_rule_master  ON fee_payable.rule_id=fee_rule_master.fee_rule_master_id 
		LEFT JOIN fee_type_master ON fee_type_master.fee_type_master_id=fee_payable.fee_type_master_id 
		WHERE `fee_payable`.`payment_master_id` IN ($id_str) AND `net_amount` != 0";
	$result1=$connection2->prepare($sql1);
	$result1->execute();
	$fee=$result1->fetchAll();
	foreach($fee as $f){
		$m=$month_arr2[$month_arr1[$f['month_no']]];		if(array_key_exists($f['payment_master_id']+0, $fee_array)){			if(array_key_exists($m, $fee_array[$f['payment_master_id']+0])){				if(array_key_exists($f['fee_type_name'], $fee_array[$f['payment_master_id']+0][$m])){					$fee_array[$f['payment_master_id']+0][$m][$f['fee_type_name']]+=$f['net_amount'];				}				else{					$fee_array[$f['payment_master_id']+0][$m][$f['fee_type_name']]=$f['net_amount'];				}			}			else{				$fee_array[$f['payment_master_id']+0][$m][$f['fee_type_name']]=$f['net_amount'];			}		}		else{			$fee_array[$f['payment_master_id']+0][$m][$f['fee_type_name']]=$f['net_amount'];		}		
	}
	$sql2="SELECT `month_name`,`price`,`payment_master_id` FROM `transport_month_entry`
			WHERE `payment_master_id` IN ($id_str)";
	$result2=$connection2->prepare($sql2);
	$result2->execute();
	$trnsprt=$result2->fetchAll();
	foreach($trnsprt as $t){
		$m=$month_arr2[$t['month_name']];
		$fee_array[$t['payment_master_id']+0][$m]['Transport']=$t['price'];
	}
	$head="<table width='100%'>
			<tr>
				<th>Date</th>
				<th>Amount</th>
				<th>Voucher No</th>
			</tr>";
	$head1="";	
	$url=$_SESSION[$guid]["absoluteURL"]."/modules/Fee/print_payment.php?pmid=";
	foreach($payment_array as $k=>$pi){
		$d=date('d/m/y',strtotime($k));
		foreach($pi as $id=>$p){
			$formatted_total=number_format((float)$p[0],2,'.','');
			$link="<a href='$url$id' target='_blank'>$p[1]</a>";
			$head1.="<tr class='headSlide'><td>$d</td><td class='rightA'>$formatted_total</td><td class='rightA' >$link</td></tr>";
			$head1.="<tr style='display:none;' class='sub_table'>
						<td colspan='3'>
						<table style='width:100%;'>
						<tr><th>Month</th><th>Fee Head</th><th>Amount</th></tr>";
			foreach($month_sequence as $m){
			if(!array_key_exists($m,$fee_array[$id]))
				continue;
				foreach($fee_array[$id][$m] as $h=>$f){					$amount=number_format($f,2,'.','');
					$head1.="<tr><td>$m</td><td>$h</td><td class='rightA'>$amount</td></tr>";	
				}
			}
			foreach($fee_array[$id]['-'] as $h=>$f){
				if($f>0)
				$head1.="<tr><td>-</td><td>$h</td><td class='rightA'>$f</td></tr>";	
			}
			$head1.="</table></tr>";
		}
	}
	$sub_total=number_format((float)$total, 2, '.', ',');
	$head2="<tr class='headSlide'> <th>Total:</th> <th class='rightA'>$sub_total</th><th></th></tr></table>";
	echo $head.$head1.$head2;
	}
}
	
}
 ?>
