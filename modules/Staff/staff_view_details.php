<?php 
@session_start() ;

//Set timezone from session variable
date_default_timezone_set($_SESSION[$guid]["timezone"]);

if (isActionAccessible($guid, $connection2, "/modules/Staff/staff_view_details.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
	if(isset($_REQUEST['gibbonPersonID'])){
		$sql="SELECT gibbonStaffID FROM `gibbonstaff` WHERE gibbonPersonID=".$_REQUEST['gibbonPersonID'];
		$result=$connection2->prepare($sql);
		$result->execute();
		$a=$result->fetch();
		$_REQUEST['gibbonStaffID']=$a['gibbonStaffID'];
	}
	try{
	/*	$sql="SELECT `preferredName`, `gender`, `dob`, `email`, `image_240`, `address1`, `address1District`, `phone1`, `dateStart` ,`dateEnd`, 
		`gibbonstaff`.`type`, `gibbonstaff`.`jobTitle`, `gibbonstaff`.`bank_ac`, `gibbonstaff`.`payment_mode`,`gibbonstaff`.qualifications,
		`gibbonstaff`.`pf_no`, `gibbonstaff`.`uan_no`,`gibbonstaff`.`pf_active`, `gibbonstaff`.`esi_no`, `gibbonstaff`.`esi_active`, `gibbonstaff`.priority,`gibbonstaff`.`guardian`,`gibbonstaff`.`relationship`,`gibbonstaff`.`reasonOfLeaving` 
		FROM `gibbonperson`
		LEFT JOIN `gibbonstaff` ON `gibbonstaff`.`gibbonPersonID`=`gibbonperson`.`gibbonPersonID`
				WHERE `gibbonstaff`.gibbonStaffID=".$_REQUEST['gibbonStaffID'];
	*/		
			
		$sql="SELECT * from `gibbonstaff` WHERE `gibbonstaff`.gibbonStaffID=".$_REQUEST['gibbonStaffID'];	
				
				
		$result=$connection2->prepare($sql);
		$result->execute();
		$data=$result->fetch();
		}
		catch(PDOException $e){
			echo $e;
		}
		try{
		$sql="SELECT * FROM `lakshyasalaryrule`";
		$result=$connection2->prepare($sql);
		$result->execute();
		$rule=$result->fetchAll();
		}
		catch(PDOException $e){
			echo $e;
		}
		$rule_count=2;
		$rule_impact=array();
		foreach($rule as $r)
 		{
			$rule_count++;
			$rule_impact[$r['rule_id']+0]=$r['impact'];
		}
		$sql5="SELECT * FROM `lakshyasalarymaster` WHERE 1";
		$sql5.=" AND staff_id=".$_REQUEST['gibbonStaffID'];
		$sql5.=" ORDER BY master_id DESC";
		$sql5.=" LIMIT ".$rule_count;
		$result5=$connection2->prepare($sql5);
		$result5->execute();
		$structure=$result5->fetchAll();
		$count=$result5->rowCount();
		$structure_d=array();
		foreach($structure as $s){
			$structure_d[$s['rule_id']]=$s['amount'];
		}
		
		if($count>0){
		$sql6="SELECT *,`lakshyasalarymaster`.* FROM `lakshyasalarypayment`
				LEFT JOIN `lakshyasalarymaster` ON  `lakshyasalarymaster`.master_id=`lakshyasalarypayment`.master_id
			WHERE 1";
		$sql6.=" AND staff_id=".$_REQUEST['gibbonStaffID'];
		$sql6.=" ORDER BY `lakshyasalarymaster`.master_id DESC";
		$sql6.=" LIMIT ".$rule_count;
		$result6=$connection2->prepare($sql6);
		$result6->execute();
		$payment=$result6->fetchAll();
		$payment_d=array();
		foreach($payment as $p){
			$payment_d[$p['rule_id']+0]=$p['paid_amount'];
		}
		$sql7="SELECT `amount`,`rule_id` FROM `lakshyasalarymaster` WHERE `rule_id`IN (97,96) AND `month`={$structure[0]['month']} AND `year_id`={$structure[0]['year_id']}";
		$result7=$connection2->prepare($sql7);
		$result7->execute();
		$pf_arr=$result7->fetchAll();
		}
		foreach($pf_arr as $s){
			$PF[$s['rule_id']+0]=$s['amount'];
		}
		$month_array=array(1=>'January',2=>'Februaray',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'Aug',9=>'September',10=>'October',11=>'November',12=>'December');
		$sql8="SELECT `gibbonSchoolYearID`, `name` FROM `gibbonschoolyear`";
		$result8=$connection2->prepare($sql8);
		$result8->execute();
		$year=$result8->fetchAll();
		$year_array=array();
		foreach($year as $y){
			$year_array[$y['gibbonSchoolYearID']+0]=$y['name'];
		}
?>
	<h3>Staff Summary:</h3>
	<a href='<?php print $_SESSION[$guid]["absoluteURL"]?>/index.php?q=/modules/Staff/staff_details_edit.php&gibbonStaffID=<?php echo $_REQUEST['gibbonStaffID']?>' style=' padding: 5px 20px; background:#ff731b; color:white; float:right; margin:5px 10px;'>Edit</a>
	<div class='c_span'>
		<table class='blank' cellspacing='0' style='width:100%; margin-top: 20px'>
			<tr>
				<td class='c_row'>
				
					<h5>Name :</h5>
					<b><?php echo $data['preferredName']; ?></b>
				
				</td>
				<td class='c_row'>
				
					<h5>Staff Type :</h5>
					<b><?php echo $data['type']; ?></b>
				
				</td>
				<td class='c_row'>
				
					<h5>Designation :</h5>
					<b><?php echo $data['jobTitle']; ?></b>
				
				</td>
				
				<td class='c_row' rowspan='4'>
			
					<div class='c_span'>
					<?php if($data['image_240']!='') {?>
					<img src="<?php echo $data['image_240'];?>" alt="Staff Photo" width="180" height="240">
					<?php }
					else{
					?>
					<img src="themes/Default/img/anonymous_240.jpg" alt="Staff Photo" width="180" height="240">
					<?php }?>
					</div>
				
				</td>
			</tr>
			<tr>
				<td class='c_row'>
				
					<h5>Gender :</h5>
					<b><?php echo $data['gender']=='F'?'Female':'Male'; ?></b>
				
				</td>
				<td class='c_row'>
				
					<h5>Date of Birth :</h5>
					<b><?php echo date("d/m/Y", strtotime($data['dob'])); ?></b>
				</td>
				<td class='c_row'>
				
					<h5>Qualification :</h5>
					<b><?php echo $data['qualifications']; ?></b>
			
				</td>
			</tr>
			<tr>
				<td class='c_row'>
					
					<h5>Address :</h5>
					<b><?php echo $data['address1']; ?></b>
					
				</td>
				<td class='c_row'>
				
					<h5>Email id :</h5>
					<b><?php echo $data['email']; ?></b>
				
				</td>
				<td class='c_row'>
				
					<h5>Contact No :</h5>
					<b><?php echo $data['phone1']; ?></b>
			
				</td>
			</tr>
			<tr>
				<td class='c_row'>
					
					<h5>Bank a/c No :</h5>
					<b><?php echo $data['bank_ac']; ?></b>
				</td>
				<td class='c_row'>
				
					<h5>P.F. No :</h5>
					<b><?php echo $data['pf_no']; ?></b>
				
				</td>
				<td class='c_row'>
				
					<h5>UAN No :</h5>
					<b><?php echo $data['uan_no']; ?></b>
				
				</td>
			</tr>
			<tr>
				<td class='c_row'>
				
					<h5>ESI No :</h5>
					<b><?php echo $data['esi_no']; ?></b>
			
				</td>
				<td class='c_row'>
					
					<h5>Mode Of Payment :</h5>
					<b><?php echo $data['payment_mode']; ?></b>
					
				</div>
				</td>
				<td class='c_row'>
				
					<h5>Date of Joining :</h5>
					<b><?php echo $data['dateStart']>0?date("d/m/Y", strtotime($data['dateStart'])):''; ?></b>
				
				</td>
				<td class='c_row'>
				
					<h5>Date of Leaving :</h5>
					<b><?php echo $data['dateEnd']>0?date("d/m/Y", strtotime($data['dateEnd'])):''; ?></b>
			
				</td>
				<td class='c_row'>
					<h5>Priority:</h5>
					<b><?php echo $data['priority'];?></b>
				</td>
			</tr>
			<tr>
				<td class='c_row' colspan='2'>
					<h5>Guardian:</h5>
					<b><?=$data['guardian'] ?> <?=$data['relationship']!=NULL?($data['relationship']=='F'?'( Father )':'( Husband )'):''?></b>
				</td>
				<td class='c_row'>
					<h5>Reason of Leaving:</h5>
					<b><?=LeavingReason($data['reasonOfLeaving'])?></b>
				</td>
			</tr>
		</table>
		
		
	</div>
	<?php if($count>0) {?>
	<h3>Current Pay structure ( <?=$month_array[$structure[0]['month']]?> of <?=$year_array[$structure[0]['year_id']]?>) :</h3>
	<div class='c_span'>
		<table  cellspacing='0' style='width:100%; margin-top: 20px'>
		<tr>
			<?php foreach($rule as $a) {
			print "<th style='text-align:center'>".$a['caption']."</th>";
			} ?>
			<th style='text-align:center'>Gross PF</th>
			<th style='text-align:center'>PF</th>
			<th style='text-align:center'>ESI</th>
			<th style='text-align:center'>Net Amount</th>
		</tr>
		<tr>
				<?php
					$n_amount=0;
					foreach($rule as $r){
				?>
				<td style='text-align:right'>
				<?php
					if (array_key_exists($r['rule_id']+0,$structure_d)){
						echo $structure_d[$r['rule_id']+0];
						if($rule_impact[$r['rule_id']+0]=='+')
							$n_amount+=$structure_d[$r['rule_id']+0]; 
						else
							$n_amount-=$structure_d[$r['rule_id']+0];
					}
					else
						echo "-";
					?>
				</td>
				<?php
					} ?>
				<td style='text-align:right'>
				<?php
					if (array_key_exists(98,$structure_d)){
						echo $structure_d[98];
					}
					else
						echo "-";
					?>
				</td>
				<td style='text-align:right'>
				<?php
					
						echo $pf=round($structure_d[98]*$PF['97']/100);
						$n_amount-=$pf;
						
					
					?>
				</td>	
				<td style='text-align:right'>
				<?php
					
						echo $esi=$structure_d[98]<21000?ceil($structure_d[98]*$PF['96']/100):0;
						$n_amount-=$esi;
						
					
					?>
				</td>	
				<td style='text-align:right'>
					<?php echo $n_amount; ?>
				</td>
		</tr>
		</table>
	</div>
	<?php if(sizeOf($payment)>0){?>
	<h3>Last Payment History ( <?=$month_array[$payment[0]['month']]?> of <?=$year_array[$payment[0]['year_id']]?>) :</h3>
	<div class='c_span'>
		<table  cellspacing='0' style='width:100%; margin-top: 20px'>
		<tr>
			<?php foreach($rule as $a) {
			print "<th style='text-align:center'>".$a['caption']."</th>";
			} ?>
			<th style='text-align:center'>Gross PF</th>
			<th style='text-align:center'>PF</th>
			<th style='text-align:center'>ESI</th>
			<th style='text-align:center'>Advance</th>
			<th style='text-align:center'>Net Amount</th>
		</tr>
		<tr>
				<?php
					$n_amount=0;
					foreach($rule as $r){
				?>
				<td style='text-align:right'>
				<?php
					if (array_key_exists($r['rule_id']+0,$payment_d)){
						echo $payment_d[$r['rule_id']+0];
						if($rule_impact[$r['rule_id']+0]=='+')
							$n_amount+=$payment_d[$r['rule_id']+0]; 
						else
							$n_amount-=$payment_d[$r['rule_id']+0];
					}
					else
						echo "-";
					?>
				</td>
				<?php
					} ?>
				<td style='text-align:right'>
					<?php
					if (array_key_exists(98,$payment_d)){
						echo $payment_d[98];
					}
					else
						echo "-";
					?>
				</td>
				<td style='text-align:right'>
				<?php
					
						echo $pf=round($structure_d[98]*$PF['97']/100);
						$n_amount-=$pf;
						
					
					?>
				</td>	
				<td style='text-align:right'>
				<?php
					
						echo $esi=$structure_d[98]<21000?ceil($structure_d[98]*$PF['96']/100):0;
						$n_amount-=$esi;
						
					
					?>
				</td>
				<td style='text-align:right'>
					<?php
					if (array_key_exists(99,$payment_d)){
						echo $payment_d[99];
						$n_amount-=$payment_d[99];
					}
					else
						echo "-";
					?>
				</td>
				<td style='text-align:right'>
					<?php echo $n_amount; ?>
				</td>
		</tr>
		</table>
	</div>
	<?php } ?>
	<?php } ?>
<?php	
}
function LeavingReason($r){
	if ($r=="C")
		return "Cessation";
	else if ($r=="S")
		return "Superannuation";
	else if ($r=="R")
		return "Retirement";
	else if ($r=="D")
		return "Death in Service";
	else if ($r=="P")
		return "Permanent Disablement";
	else
		return "";
}
?>