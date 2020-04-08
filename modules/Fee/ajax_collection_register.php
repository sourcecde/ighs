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

$month_arr1=array(0=>'yearly',1=>'jan',2=>'feb',3=>'mar',4=>'apr',5=>'may',6=>'jun',7=>'jul',8=>'aug',9=>'sep',10=>'oct',11=>'nov',12=>'dec');
$month_arr2=array('yearly'=>'Yearly','jan'=>'January','feb'=>'February','mar'=>'March','apr'=>'April','may'=>'May','jun'=>'June','jul'=>'July','aug'=>'August','sep'=>'September','oct'=>'October','nov'=>'November','dec'=>'December');
$month_sequence=array('Yearly','April','May','June','July','August','September','October','November','December','January','February','March');
if($_POST){
	extract($_POST);
	//print_r($_POST);
	if($action=='load_data'){
			$startDateP=$startDate;
			$endDateP=$endDate;
			$startDate_a=explode('/',$startDate);
			$startDate=$startDate_a[2]."-".$startDate_a[1]."-".$startDate_a[0];
			$endDate_a=explode('/',$endDate);
			$endDate=$endDate_a[2]."-".$endDate_a[1]."-".$endDate_a[0];
			try{
				$sql="SELECT * FROM gibbonsetting WHERE gibbonSystemSettingsID='147'";
				$result=$connection2->prepare($sql);
				$result->execute();
				$header2=$result->fetch();
			}
			catch(PDOException $e) { 
				print "<div class='error'>" . $e->getMessage() . "</div>" ; 
			}
		if($viewType=='Short'){
			try {
				$sql="SELECT SUM(net_total_amount) AS subtotal,payment_master.*,gibbonperson.officialName as officialname,gibbonperson.account_number,gibbonyeargroup.name AS class,accountShortName,
					gibbonrollgroup.name AS section,gibbonstudentenrolment.rollOrder AS roll FROM payment_master 
					LEFT JOIN gibbonperson ON payment_master.gibbonPersonID=gibbonperson.gibbonPersonID
					LEFT JOIN gibbonstudentenrolment ON payment_master.gibbonStudentEnrolmentID=gibbonstudentenrolment.gibbonStudentEnrolmentID
					LEFT JOIN gibbonyeargroup ON gibbonstudentenrolment.gibbonYearGroupID=gibbonyeargroup.gibbonYearGroupID
					LEFT JOIN gibbonrollgroup ON gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID
					LEFT JOIN payment_bankaccount ON payment_master.bankID=payment_bankaccount.bankID WHERE net_total_amount>0 " ;  
					
					if($paymentMode!='')
						$sql.=" AND payment_mode='$paymentMode'";
					if($startDate!="")
						$sql.=" AND payment_master.payment_date>='$startDate'";
					if($endDate!="")
						$sql.=" AND payment_master.payment_date<='$endDate'";
					if($yearID!="")
						$sql.=" AND payment_master.gibbonSchoolYearID=".$yearID;
					if($duration!=''){
						$q=$duration==1?"1 AND 3":"4 AND 12";
						$tmpQuery="SELECT distinct payment_master_id  FROM `fee_payable` WHERE `month_no` BETWEEN ".$q." AND payment_staus='paid'";
						$sql.=" AND payment_master.payment_master_id IN (".$tmpQuery.")";
					}
				$sql.=" GROUP BY payment_master.payment_date,payment_master.payment_master_id WITH ROLLUP";
				// echo $sql;
				$result=$connection2->prepare($sql);
				$result->execute();
				
			}
			catch(PDOException $e) { 
				print "<div class='error'>" . $e->getMessage() . "</div>" ; 
			}
			// gfdghdfjhdfj
			if ($result->rowcount()<1) {
					print "<div class='error'>" ;
					print _("There are no records to display.") ;
					print "</div>" ;
				}
				else {
			?>
			<table width="100%" cellpadding="0" cellspacing="0" border="0" style="overflow-x:auto" id='short-table'>
				<thead>
				<tr>
				<td colspan='11'>
					<p style='text-align:center; font-weight:bold; font-size:14px; margin: 2px;'>INDRA GOPAL HIGH SCHOOL</p>
					<p style='text-align:center;  font-size:12px;margin: 2px;'><?php echo $header2["value"];?></p>
					<p style='text-align:center;  font-size:12px;margin: 2px;'>
						Collection Register From <?=$startDateP?> To <?=$endDateP?>
					</p>
				</td>
				</tr>
				<tr class="tablehead" style="white-space:nowrap;font-size:95%;";>
				    <th>Sl No</th>
					<th>Date</th>
					<th style='font-size:95%;'>Voucher No</th>
					<th>Acc No</th>
					<th>Name</th>
					<th style='font-size:95%;'>Class</th>
					<th style='font-size:95%;'>Roll No</th>
					<th>Mode</th>
					<th>Amount</th>
					<th>Ref No.</th>
					<th>Bank</th>
					<!--<th>Ref Dt</th>-->
				</tr>
				</thead>
				<tbody>
				<?php 
				try {
						$resultPage=$connection2->prepare($sql);
						$resultPage->execute();
						}
						catch(PDOException $e) { 
						print "<div class='error'>" . $e->getMessage() . "</div>" ; 
					}
								$i=0;
								$sl_no=1;
				while ($row=$resultPage->fetch()) { $i++;
					if($row['payment_master_id']==null)
					{
					?>
					<tr class='footerT'>
					<td colspan="8" style="text-align: right"><?php if($result->rowcount()==$i){?>Total<?php } else {?>Sub Total<?php } ?></td>
					<td style="text-align: right"><?php echo $row['subtotal']?></td>  
					<td colspan="3"></td>
				</tr>
				<?php 
				$sl_no=1;
				} else {?>
					<tr>
					<td><?php echo  $sl_no++; ?></td>
					<td><?php echo IndianDateFormater($row['payment_date'])?></td>
					<td class='rightA'><?php echo $row['voucher_number']+0?></td>
					<td class='rightA'><?php echo substr($row['account_number'], 5);?></td>
					<td><?php echo $row['officialname']?></td>
					<td><?php echo $row['class']." ".$row['section'];?></td>
					<td class='rightA'><?php echo $row['roll']?></td>
					<?php $strpos=strpos($row['payment_mode'],"_");
					if($strpos!=0)
						$row['payment_mode']=str_replace('_',' ',$row['payment_mode']) ?>
					<td><?php echo ucwords($row['payment_mode'])?></td>
					<td class='rightA'><?php echo number_format((float)$row['net_total_amount'],2,".",",")?></td>  
					<td><?php echo $row['cheque_no'];?></td>
					<td><?php echo $row['accountShortName'];?></td>
				<!--<td><?php if($row['payment_mode']!='cash'){echo IndianDateFormater($row['cheque_date']);}else{echo "";}?></td>-->
				</tr>
				<?php 
				} }
				?>
				</tbody>
			</table>
		<?php
			}
		}
		if($viewType=='Details'){		
				$sql1="SELECT  `payment_master_id`,`gibbonPersonID`,`fine_amount`,`net_total_amount`,`voucher_number`,`payment_date`,`payment_mode`,
					`cheque_no`,`cheque_date`,`bankAbbr`,`accountShortName` FROM `payment_master`
					LEFT JOIN `payment_bankaccount` ON `payment_master`.`bankID`=`payment_bankaccount`.`bankID`
					LEFT JOIN `fee_bank_master` ON `payment_master`.`cheque_bank`=`fee_bank_master`.`bankMasterID`
					WHERE `payment_date`>='$startDate' AND `payment_date`<='$endDate' ";
					if($paymentMode!='')
						$sql1.=" AND payment_mode='$paymentMode'";
					if($yearID!="")
						$sql1.=" AND payment_master.gibbonSchoolYearID=".$yearID;
					if($duration!=''){
						$q=$duration==1?"1 AND 3":"4 AND 12";
						$tmpQuery="SELECT distinct payment_master_id  FROM `fee_payable` WHERE `month_no` BETWEEN ".$q." AND payment_staus='paid'";
						$sql1.=" AND payment_master.payment_master_id IN (".$tmpQuery.")";
					}
				$sql1.=" ORDER BY `payment_date`";
				$result1=$connection2->prepare($sql1);
				$result1->execute();
				$paymentData=$result1->fetchAll();
				if($result1->rowCount()>0){
				$paymentIDArray=array();
				$personIDArray=array();
				$feeArray=array();
				$netTotal=0;
				foreach($paymentData as $p){
					$paymentIDArray[]=$p['payment_master_id']+0;
					$personIDArray[]=$p['gibbonPersonID']+0;
					$netTotal+=$p['net_total_amount'];
				}
				$paymentID_str=implode(',',$paymentIDArray);
				$personID_str=implode(',',$personIDArray);

				$sql2="SELECT `gibbonperson`.`gibbonPersonID`,`preferredName`,`account_number`,`gibbonstudentenrolment`.`rollOrder`,
						`gibbonrollgroup`.`name`  as `section`,`gibbonyeargroup`.`name`  as `class`
						FROM `gibbonperson` 
						LEFT JOIN `gibbonstudentenrolment` ON `gibbonstudentenrolment`.`gibbonPersonID`=`gibbonperson` .`gibbonPersonID`
						LEFT JOIN `gibbonrollgroup` ON `gibbonrollgroup`.`gibbonRollGroupID`=`gibbonstudentenrolment`.`gibbonRollGroupID`
						LEFT JOIN `gibbonyeargroup` ON `gibbonyeargroup`.`gibbonYearGroupID`=`gibbonstudentenrolment`.`gibbonYearGroupID`
						WHERE `gibbonperson`.`gibbonPersonID` IN ($personID_str)";
				$result2=$connection2->prepare($sql2);
				$result2->execute();
				$studentsData=$result2->fetchAll();
				$studentsArray=array();
				foreach($studentsData as $s){
					$studentsArray[$s['gibbonPersonID']+0]=array($s['preferredName'],$s['section'],$s['rollOrder'],$s['account_number']+0,$s['class']);
				}

				$sql3="SELECT  `month_no`, `net_amount`,`fee_payable`.`payment_master_id`, `fee_type_master`.`fee_type_name` 
						FROM `fee_payable` 
						LEFT JOIN fee_rule_master ON fee_payable.rule_id=fee_rule_master.fee_rule_master_id 
						LEFT JOIN fee_type_master ON fee_type_master.fee_type_master_id=fee_payable.fee_type_master_id 
						WHERE `fee_payable`.`payment_master_id` IN ($paymentID_str) AND `net_amount` != 0 ORDER BY `month_no`";
				$result3=$connection2->prepare($sql3);
				$result3->execute();
				$feeData=$result3->fetchAll();		
				foreach($feeData as $f){
						$m=$month_arr2[$month_arr1[$f['month_no']]];
						$header=$f['fee_type_name']." - ".$m;
						$feeArray[$f['payment_master_id']+0][$header]=$f['net_amount'];
				}
				$sql4="SELECT `month_name`,`price`,`payment_master_id` FROM `transport_month_entry`
							WHERE `payment_master_id` IN ($paymentID_str)";
				$result4=$connection2->prepare($sql4);
				$result4->execute();
				$trnsprt=$result4->fetchAll();
				
				
	
				foreach($trnsprt as $t){
					$m=$month_arr2[$t['month_name']];
					$header='Transport - '.$m;
					$feeArray[$t['payment_master_id']+0][$header]=$t['price'];
				}
				$paymentArray=array();
				foreach($paymentData as $p){
					$paymentArray[$p['payment_date']][$p['payment_master_id']+0]=array($p['gibbonPersonID']+0,$p['voucher_number']+0,$p['payment_mode'],$p['net_total_amount'],$p['cheque_no'],$p['cheque_date'],$p['bankAbbr'],$p['accountShortName']);
					if($p['fine_amount']>0)
						$feeArray[$p['payment_master_id']+0]['Fine']=$p['fine_amount'];
				}
				
				print "<table width='100%' id='details-table'>";
					print "<thead>";
				?>
					<tr>
					<td colspan='9'>
						<p style='text-align:center; font-weight:bold; font-size:14px; margin: 2px;'>CALCUTTA PUBLIC SCHOOL</p>
						<p style='text-align:center;  font-size:12px;margin: 2px;'><?php echo $header2["value"];?></p>
						<p style='text-align:center;  font-size:12px;margin: 2px;'>
							Collection Register From <?=$startDateP?> To <?=$endDateP?>
						</p>
					</td>
					</tr>
				<?php
					print "<tr>";
						print "<th>Date</th>";
						print "<th>Student</th>";
						print "<th>Acc No</th>";
						print "<th>Class</th>";
						print "<th>Roll</th>";
						print "<th>Voucher No</th>";
						print "<th>Mode</th>";
						print "<th>Fee Head</th>";
						print "<th>Amount</th>";
					print "</tr>";
					print "</thead>";
					print "<tbody>";
				foreach($paymentArray as $d=>$v){
					$subTotal=0;
					foreach($v as $id=>$p){
						$personID=$p[0];
						$paymentID=$id;
						$rowCount=sizeOf($feeArray[$paymentID])+1;
						$subTotal+=$p[3];			
						echo "<tr>";
							echo "<td rowspan='$rowCount' class='border-bottom'>".date('d/m/Y',strtotime($d))."</td>";
							echo "<td rowspan='$rowCount' class='border-bottom'>{$studentsArray[$personID][0]}</td>";
							echo "<td rowspan='$rowCount' class='border-bottom'>{$studentsArray[$personID][3]}</td>";
							echo "<td rowspan='$rowCount' class='border-bottom'> {$studentsArray[$personID][4]} {$studentsArray[$personID][1]}</td>";
							echo "<td rowspan='$rowCount' class='border-bottom'>{$studentsArray[$personID][2]}</td>";
							echo "<td rowspan='$rowCount' class='border-bottom rightA'>{$p[1]}</td>";
							$strpos=strpos($p[2],"_");
							if($strpos!=0)
								$p[2]=str_replace('_',' ',$p[2]);
							echo "<td rowspan='$rowCount' class='border-bottom'>".ucwords($p[2]);
							if($p[2]!='cash'){
								echo "<br><span style='font-size: 10px'>Ref no. ".$p[4];
								echo "<br>Dated ".IndianDateFormater($p[5]);
								echo "<br>Recieved in ".$p[7]." account";
							}
							echo "</td>";
							$i=0;
							foreach($feeArray[$paymentID] as $h=>$v){
								if($i++!=0)
									echo "<tr>";
									echo "<td>$h</td>";
									$amount=number_format((float)$v, 2, '.', '');
									echo "<td class='rightA'>{$amount}</td>";
								echo "</tr>";		
							}
							echo "<tr class='footerT'><td class='rightA border-bottom' style='border-top:.5px dashed'>Total:</td><td class='rightA border-bottom' style='border-top:.5px dashed'>{$p[3]}</td></tr>";

							
					}
					
				}
					$n=number_format((float)$netTotal,2,".",",");
					print "<tr class='footerT'><th colspan='8' class='rightA'>Total:</th><th class='rightA'>$n</th></tr>";	
					print "</tbody>";
				print "</table>";
			}
			else{
				print "<div class='error'>" ;
				print _("There are no records to display.") ;
				print "</div>" ;
			}
		}
	// 
	}
}
?>
<?php 
function IndianDateFormater($date)
{	if($date==""){		return "";	}
	$datearr=explode("-", $date);
	$newdate=$datearr[2].'/'.$datearr[1].'/'.substr($datearr[0],2);
	return $newdate;
}
?>