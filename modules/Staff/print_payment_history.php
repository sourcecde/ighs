<?php
@session_start() ;
include "../../config.php" ;


?>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Payment History</title>
<style type="text/css">
    table { page-break-inside:auto }
    tr  {
	page-break-inside:avoid; page-break-after:auto;
	}
    thead { 
	page-break-after:always;font-size:16px;
	}
   tbody { 
	page-break-after:always;font-size:16px;
	}
</style>
</head>
<body>
<?php
try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}
echo "<script type='text/javascript' src='".$_SESSION[$guid]['absoluteURL']."/lib/jquery/jquery.js'></script>";
	try {
	$data=array(); 
	$sql="SELECT * FROM gibbonsetting WHERE scope='System' AND name='organisationName'" ;
	$result=$connection2->prepare($sql);
	$result->execute($data);
	}
	catch(PDOException $e) { 
		print "<div class='error'>" . $e->getMessage() . "</div>" ; 
	}
	$row=$result->fetch() ;
	$organizationname=$row['value'];
$staff_id='';
$id=$_REQUEST['m_y'];
$m_y=explode("_",$id);
if(isset($_REQUEST['sid'])){
	$staff_id=$_REQUEST['sid']+0;
}
	
		$sql="SELECT * FROM `lakshyasalaryrule`";
		$result=$connection2->prepare($sql);
		$result->execute();
		$rule=$result->fetchAll();
		
		$rule_impact=array();
		$positive_rule=array();
		$negative_rule=array();
		$rule_count=0;
		foreach($rule as $r)
		{
			$rule_impact[$r['rule_id']+0]=$r['impact'];
			if($r['impact']=='+')
				$positive_rule[]=$r;
			else
				$negative_rule[]=$r;
		}
		  
		  
		$sql1="SELECT gibbonstaff.gibbonStaffID,gibbonstaff.jobTitle,gibbonstaff.payment_mode,gibbonstaff.bank_ac,gibbonstaff.preferredName,gibbonperson.dateStart,gibbonstaff.priority,gibbonstaff.sec_code FROM gibbonstaff
				LEFT JOIN gibbonperson on gibbonperson.gibbonPersonID=gibbonstaff.gibbonPersonID";
		        //$sql1.=" WHERE (dateEnd IS NULL  OR dateEnd>='" . date("Y-m-d") . "')";			
		if($staff_id!='')
		$sql1.=" WHERE gibbonStaffID=".$staff_id;		
		//$sql1.=" AND gibbonStaffID=".$staff_id;		
		$sql1.=" ORDER BY gibbonstaff.priority,gibbonstaff.category";		
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		$staff_n=$result1->fetchAll();
	
		$sql2="SELECT * FROM `gibbonschoolyear` WHERE `gibbonSchoolYearID`=".$m_y[1];
		$result2=$connection2->prepare($sql2);
		$result2->execute();
		$year=$result2->fetch();
		$month_name=array('January','February','March','April','May','June','July','August','September','October','November','December');
		
		$sql5="SELECT *,lakshyasalarymaster.* FROM `lakshyasalarypayment`
				LEFT JOIN `lakshyasalarymaster` ON  lakshyasalarymaster.master_id=lakshyasalarypayment.master_id
			WHERE 1";
		if($staff_id!='')
		$sql5.=" AND lakshyasalarymaster.staff_id=".$staff_id;
		$sql5.=" AND lakshyasalarymaster.month=".($m_y[0]+0);
		$sql5.=" AND lakshyasalarymaster.year_id=".($m_y[1]+0);
		
		$result5=$connection2->prepare($sql5);
		$result5->execute();
		$structure=$result5->fetchAll();
		    
		
		$structure_d=array();
		foreach($structure as $s){
			$structure_d[$s['staff_id']+0][$s['rule_id']+0]=$s['paid_amount'];
		}
		
		$sql6="SELECT amount,rule_id FROM `lakshyasalarymaster` WHERE `rule_id` IN (97,96) ";
		$sql6.=" AND month=".($m_y[0]+0);
		$sql6.=" AND year_id=".($m_y[1]+0);
		$result6=$connection2->prepare($sql6);
		$result6->execute();
		$pf_arr=$result6->fetchAll();
		foreach($pf_arr as $s){
			$pf[$s['rule_id']+0]=$s['amount'];
		}
		
		$sql7="SELECT staff_id,attended FROM `lakshyasalaryattendance` WHERE 1 ";
		$sql7.=" AND month=".($m_y[0]+0);
		$sql7.=" AND year_id=".($m_y[1]+0);
		$result7=$connection2->prepare($sql7);
		$result7->execute();
		$atndnc=$result7->fetchAll();
		$attendance=array();
		foreach($atndnc as $a){
			$attendance[$a['staff_id']]=$a['attended'];
		}
		
		
		
 ?>
 <table width="100%" cellpadding="5px" cellspacing="0" style="border: 1px solid black; border-collapse: collapse;">
	<thead>
	 <tr>
		<th align="center" colspan=15 style="font-family:Arial, Helvetica, sans-serif; font-size:25px; color:#000000;"><?php echo $organizationname;?></th>
	</tr>
	<tr>
		<td align="center" colspan=15 style="font-family:Arial, Helvetica, sans-serif; font-size:20px; color:#000000;">Jheel Bagan, P.O. Ghuni, Hatiara, Kolkata - 700 157</td>
	</tr>	
	<tr>
		<td align="center" colspan=15 style="font-family:Arial, Helvetica, sans-serif; font-size:20px; color:#000000;"> Salary For  the Month: <?php echo $month_name[($m_y[0]-1)];?> of Year: <?php echo $year['name'];?></td>
	</tr>	
	
	  <tr style="border: 1px solid black; border-collapse: collapse;">
        <th rowspan='2' style="border: 1px solid black; border-collapse: collapse; width:200px;">Sl.No.</th>	      
		<th rowspan='2' style="border: 1px solid black; border-collapse: collapse; width:200px;">Staff Name</th>
		<th rowspan='2' style='border: 1px solid black; border-collapse: collapse; width:50px;'>Gross Salary</th>
		<th rowspan='2' style='border: 1px solid black; border-collapse: collapse; width:20px;'>W. Day</th>
			<?php 
						$p_l=sizeOf($positive_rule);
						$n_l=sizeOf($negative_rule);
				if($p_l <= $n_l) 
					for($i=1;$i<$p_l-$n_l;$i++)
					echo "<th style='border: 1px solid black; border-collapse: collapse;'></th>";
			?>
			
		<?php foreach($positive_rule as $a) {
			print "<th style='border: 1px solid black; border-collapse: collapse; width:100px;'>".$a['caption']."</th>";
			$sub_rule[$a['rule_id']]=0;
			} ?>
		<th style='border: 1px solid black; border-collapse: collapse; width:100px;'>Gross PF</th>
		<th style='border: 1px solid black; border-collapse: collapse; width:100px;'>Total ERN.</th>
		<th rowspan='2' style='border: 1px solid black; border-collapse: collapse; width:80px;'>Net Amount</th>
		<th rowspan='2' style='border: 1px solid black; border-collapse: collapse; min-width: 200px' >Signature</th>
		</tr>
	
		<tr>
				<?php 
					if($p_l >= $n_l) 
						for($i=2;$i<$p_l-$n_l;$i++)
							echo "<th style='border: 1px solid black; border-collapse: collapse;'></th>";
				?>
		<?php foreach($negative_rule as $a) {
			print "<th style='border: 1px solid black; border-collapse: collapse;'>".$a['caption']."</th>";
			$sub_rule[$a['rule_id']]=0;
			} ?>
		<th style='border: 1px solid black; border-collapse: collapse; width:100px;'>PF (<?php  echo $pf['97']; ?>%)</th>
		<th style='border: 1px solid black; border-collapse: collapse; width:100px;'>ESI (<?php  echo $pf['96']; ?>%)</th>
		<th style='border: 1px solid black; border-collapse: collapse; width:100px;'>Advance</th>
		<th style='border: 1px solid black; border-collapse: collapse; width:100px;'>Total DED.</th>
		</tr>
	</thead>	
	<tbody>	
		<?php
				$sub_deduction=0;
				$sub_n_amount=0;
				$sub_grossPF=0;
				$sub_pf=0;
				$sub_esi=0;
				$sub_ern=0;
				$sub_ded=0;
				$count=0;
				$counter = 0;
				$secCode='';
				$SubTotalSectionWise=array();
				
		foreach($staff_n as $sn) {
			$n_amount=0;
			$total_ern=0;
			$total_ded=0;
			if(!array_key_exists($sn['gibbonStaffID']+0,$structure_d))
				continue;
		if(!array_key_exists($sn['sec_code'], $SubTotalSectionWise)){
				$SubTotalSectionWise[$sn['sec_code']]=array();
			}
			if($secCode!='' && $secCode!=$sn['sec_code']){
				$counter = 0;				
				?>
				<tr style="font-weight: bold;">		
					<td style='text-align:right; height:50px;' colspan='4'><b>Sub Total for Section <?php echo 'Secondary Section' ; ?>:</b></td>
					
					<?php 
								$gap_col=abs(sizeOf($positive_rule)-sizeOf($negative_rule));
						
					?>
					<?php
					if(sizeOf($positive_rule)>sizeOf($negative_rule)){
								$i=0;
								foreach($positive_rule as $r){
						?>
						<td style='position: relative; width:200px; margin:0; padding:0;'>
							<span style='position:absolute; top:0; left:0; padding:5px;'>
							<?php
							if (array_key_exists($r['caption'],$SubTotalSectionWise[$secCode])){
							echo "<b>{$SubTotalSectionWise[$secCode][$r['caption']]}</b>";
							}
							else
								echo "-";
							?>
							</span><hr>
							<span style='position:absolute; bottom:0; right:0; padding:5px;'>
							<?php
							if(--$gap_col<2 && $i<sizeOf($negative_rule)){
								/*if (array_key_exists($negative_rule[$i++]['rule_id'],$sub_rule)){
									echo "<b>{$sub_rule[$r['rule_id']+0]}</b>";
								}*/
								if (array_key_exists($negative_rule[$i]['caption'],$SubTotalSectionWise[$secCode])){
									echo "<b>{$SubTotalSectionWise[$secCode][$negative_rule[$i++]['caption']]}</b>";
								}
								else
									echo "-";
							}
							else if($i==sizeOf($negative_rule))
							{
								echo "<b>{$SubTotalSectionWise[$secCode]['sub_esi']}</b>";
								$i++;
							}
							else if($i==sizeOf($negative_rule)+1)
							{
								echo "<b>{$SubTotalSectionWise[$secCode]['sub_pf']}</b>";
								
							}
							else
									echo "-";
							?>
							</span>
						</td>
						<?php 
								}
							} 
					?>
					<td style='position: relative; width:200px; margin:0; padding:0;'>
							<span style='position:absolute; top:0; left:0; padding:5px;'>	
							<b><?php echo $SubTotalSectionWise[$secCode]['sub_grossPF'];?></b>
							</span><hr>
							<span style='position:absolute; bottom:0; right:0; padding:5px;'>	
							<b><?php echo $SubTotalSectionWise[$secCode]['sub_deduction'];?></b></span>
					</td>
					<td style='position: relative; width:200px; margin:0; padding:0;'>
							<span style='position:absolute; top:0; left:0; padding:5px;'>
								<?php echo $SubTotalSectionWise[$secCode]['sub_ern'];?>
							</span><hr>
							<span style='position:absolute; bottom:0; right:0; padding:5px;'>	
								<?php  echo $SubTotalSectionWise[$secCode]['sub_ded'];?>
							</span>	
                    
					</td>
					
					<td style='text-align:right;'><b><?php echo $SubTotalSectionWise[$secCode]['sub_n_amount'];?></b></td>					
				<?php
			}
			$secCode=$sn['sec_code'];			
		?>       
			<tr>
		        <td style="border: 1px solid black; border-collapse: collapse;">
					<?php echo ++$counter; ?>
				</td>			    
				<td style="border: 1px solid black; border-collapse: collapse;">
					<?php echo $sn['preferredName']."<br><small>(".$sn['jobTitle'].")</small><br>";?>
					<?php echo "<small>DOJ: ".date('d/m/y',strtotime($sn['dateStart']))."</small>"; ?>
				</td>
				<td style='border: 1px solid black; border-collapse: collapse; text-align:right;'>
					<span class='gross' id='g<?php echo $sn['gibbonStaffID'];?>'></span>
				</td>
				<td style="border: 1px solid black; border-collapse: collapse; text-align:right;">
					<?php echo $attendance[$sn['gibbonStaffID']+0];?>
				</td>
				<?php 
					$size_p=sizeOf($positive_rule);
					$size_n=sizeOf($negative_rule);
					$gap_col=abs($size_p-$size_n);
					if($size_p>$size_n){
						$i=0;
						$j=0;
						foreach($positive_rule as $r){
				?>
				<td style="position: relative; width:200px;   padding:0; margin:0; border: 1px solid black; border-collapse: collapse;">
					<span style='position:absolute; top:0; left:0; padding:5px;'>
					<?php
					if (array_key_exists($r['rule_id']+0,$structure_d[$sn['gibbonStaffID']+0])){
						echo $a=$structure_d[$sn['gibbonStaffID']+0][$r['rule_id']+0];
						$n_amount+=$a;
						$sub_rule[$r['rule_id']]+=$a;
						$total_ern+=$a;
					if(!array_key_exists($r['caption'], $SubTotalSectionWise[$sn['sec_code']])){
							$SubTotalSectionWise[$sn['sec_code']][$r['caption']]=$a;
						}
						else{
							$SubTotalSectionWise[$sn['sec_code']][$r['caption']]+=$a;
						}
												
					}
					else
						echo "-";
					?>
					</span><hr style="height:0; border-top:1px solid;">
					<span style='position:absolute; bottom:0; right:0; padding:5px;'>
					<?php
					if(--$gap_col<2 && $i<sizeOf($negative_rule)){
						if (array_key_exists($negative_rule[$i]['rule_id'],$structure_d[$sn['gibbonStaffID']+0])){
							echo $a=$structure_d[$sn['gibbonStaffID']+0][$negative_rule[$i]['rule_id']+0];
							$n_amount-=$a;
							$sub_rule[$r['rule_id']]-=$a;
							$total_ded+=$a;
						if(!array_key_exists($negative_rule[$i]['caption'], $SubTotalSectionWise[$sn['sec_code']])){
								$SubTotalSectionWise[$sn['sec_code']][$negative_rule[$i]['caption']]=$a;
							}
							else{
								$SubTotalSectionWise[$sn['sec_code']][$negative_rule[$i]['caption']]+=$a;
							}							
							$i++;
						}
						else
							echo "-";
					}
					else if($i>=sizeOf($negative_rule))
					{
						if($j==0){
						$PF=round($structure_d[$sn['gibbonStaffID']+0][98]*$pf['97']/100);
						echo $PF;
						$n_amount-=$PF;
						$sub_pf+=$PF;
						$total_ded+=$PF;
							if(!array_key_exists('sub_pf', $SubTotalSectionWise[$sn['sec_code']])){
								$SubTotalSectionWise[$sn['sec_code']]['sub_pf']=$PF;
							}
							else{
								$SubTotalSectionWise[$sn['sec_code']]['sub_pf']+=$PF;
							}
						
						$j++;
						}
						else{
					//	$ESI=$structure_d[$sn['gibbonStaffID']+0][98]<21000?ceil($structure_d[$sn['gibbonStaffID']+0][98]*$pf['96']/100):0;
						$ESI=($structure_d[$sn['gibbonStaffID']+0][98]+$structure_d[$sn['gibbonStaffID']+0][12])<21000?ceil(($structure_d[$sn['gibbonStaffID']+0][98]+$structure_d[$sn['gibbonStaffID']+0][12])*$pf['96']/100):0;
						echo $ESI;
						$n_amount-=$ESI;
						$sub_esi+=$ESI;
						$total_ded+=$ESI;
							if(!array_key_exists('sub_esi', $SubTotalSectionWise[$sn['sec_code']])){
								$SubTotalSectionWise[$sn['sec_code']]['sub_esi']=$ESI;
							}
							else{
								$SubTotalSectionWise[$sn['sec_code']]['sub_esi']+=$ESI;
							}						
						}
					}
					else
							echo "-";
					?>
					</span>
				</td>
				<?php 
						}
					} 
					//Not required now. Else part.
				
				?>				
				<td style='position: relative; width:200px; margin:0; padding:0; border: 1px solid black; border-collapse: collapse;'>
					<span style='position:absolute; top:0; left:0; padding:5px;'>
					<?php 
						echo $gross_pf=$structure_d[$sn['gibbonStaffID']+0][98];
						$sub_grossPF+=$gross_pf;
						if(!array_key_exists('sub_grossPF', $SubTotalSectionWise[$sn['sec_code']])){
								$SubTotalSectionWise[$sn['sec_code']]['sub_grossPF']=$gross_pf;
							}
							else{
								$SubTotalSectionWise[$sn['sec_code']]['sub_grossPF']+=$gross_pf;
							}						
					?>
					</span><hr style="height:0; border-top:1px solid;">
					<span style='position:absolute; bottom:0; right:0; padding:5px;'>	
					<?php
						echo $adv =$structure_d[$sn['gibbonStaffID']+0][99];
						$n_amount-=$adv;
						$sub_deduction+=$adv;
						$total_ded+=$adv;
						if(!array_key_exists('sub_deduction', $SubTotalSectionWise[$sn['sec_code']])){
							$SubTotalSectionWise[$sn['sec_code']]['sub_deduction']=$adv;
						}
						else{
							$SubTotalSectionWise[$sn['sec_code']]['sub_deduction']+=$adv;
						}
					?>
					</span>
				</td>
				<td style="position: relative; padding:0; margin:0; border: 1px solid black; border-collapse: collapse; text-align:left;" >
					<span style='position:absolute; top:0; left:0; padding:5px;' id='tg<?php echo $sn['gibbonStaffID'];?>'>
					<?php
						echo $total_ern;
						$sub_ern+=$total_ern;
							if(!array_key_exists('sub_ern', $SubTotalSectionWise[$sn['sec_code']])){
								$SubTotalSectionWise[$sn['sec_code']]['sub_ern']=$total_ern;
							}
							else{
								$SubTotalSectionWise[$sn['sec_code']]['sub_ern']+=$total_ern;
							}						
					?>
					</span><hr style="height:0; border-top:1px solid;">
					<span style='position:absolute; bottom:0; right:0; padding:5px;'>	
					<?php
						echo $total_ded;
						$sub_ded+=$total_ded;
								if(!array_key_exists('sub_ded', $SubTotalSectionWise[$sn['sec_code']])){
									$SubTotalSectionWise[$sn['sec_code']]['sub_ded']=$total_ded;
								}
								else{
									$SubTotalSectionWise[$sn['sec_code']]['sub_ded']+=$total_ded;
								}						
					?>
					</span>
				</td>
				<td style=" border: 1px solid black; border-collapse: collapse; text-align:right; font-weight:bold">
					<?php
					echo $n_amount;
					$sub_n_amount+=$n_amount;
								if(!array_key_exists('sub_n_amount', $SubTotalSectionWise[$sn['sec_code']])){
									$SubTotalSectionWise[$sn['sec_code']]['sub_n_amount']=$n_amount;
								}
								else{
									$SubTotalSectionWise[$sn['sec_code']]['sub_n_amount']+=$n_amount;
								}					
					?>
				</td>
				<td style="position: relative; border: 1px solid black; border-collapse: collapse; text-align:right;">
					<span style='position:absolute; bottom:0; right:0; padding:5px;'>
							<small><?php echo $sn['payment_mode']=='Cheque'?'Bank a/c: '.$sn['bank_ac']:$sn['payment_mode']; ?></small>
					</span>
				</td>
		<?php } ?>

		
		<tr style="font-weight: bold;">		
					<td style='text-align:right; height:50px;' colspan='4'><b>Sub Total for <?php echo 'Primary Section'; ?>:</b></td>					
					
					<?php 
						$gap_col=abs(sizeOf($positive_rule)-sizeOf($negative_rule));
					?>
					<?php
					if(sizeOf($positive_rule)>sizeOf($negative_rule)){
								$i=0;
								foreach($positive_rule as $r){
						?>
						<td style='position: relative; width:200px; margin:0; padding:0;'>
							<span style='position:absolute; top:0; left:0; padding:5px;'>
							<?php
							if (array_key_exists($r['caption'],$SubTotalSectionWise[$secCode])){
							echo "<b>{$SubTotalSectionWise[$secCode][$r['caption']]}</b>";
							}
							else
								echo "-";
							?>
							</span><hr>
							<span style='position:absolute; bottom:0; right:0; padding:5px;'>
							<?php
							if(--$gap_col<2 && $i<sizeOf($negative_rule)){
								/*if (array_key_exists($negative_rule[$i++]['rule_id'],$sub_rule)){
									echo "<b>{$sub_rule[$r['rule_id']+0]}</b>";
								}*/
								if (array_key_exists($negative_rule[$i]['caption'],$SubTotalSectionWise[$secCode])){
									echo "<b>{$SubTotalSectionWise[$secCode][$negative_rule[$i++]['caption']]}</b>";
								}
								else
									echo "-";
							}
							else if($i==sizeOf($negative_rule))
							{
								echo "<b>{$SubTotalSectionWise[$secCode]['sub_esi']}</b>";
								$i++;
							}
							else if($i==sizeOf($negative_rule)+1)
							{
								echo "<b>{$SubTotalSectionWise[$secCode]['sub_pf']}</b>";
								
							}
							else
									echo "-";
							?>
							</span>
						</td>
						<?php 
								}
							} 
					?>
					<td style='position: relative; width:200px; margin:0; padding:0;'>
							<span style='position:absolute; top:0; left:0; padding:5px;'>	
							<b><?php echo $SubTotalSectionWise[$secCode]['sub_grossPF'];?></b>
							</span><hr>
							<span style='position:absolute; bottom:0; right:0; padding:5px;'>	
							<b><?php echo $SubTotalSectionWise[$secCode]['sub_deduction'];?></b></span>
					</td>
					<td style='position: relative; width:200px; margin:0; padding:0;'>
							<span style='position:absolute; top:0; left:0; padding:5px;'>
								<?php echo $SubTotalSectionWise[$secCode]['sub_ern'];?>
							</span><hr>
							<span style='position:absolute; bottom:0; right:0; padding:5px;'>	
								<?php  echo $SubTotalSectionWise[$secCode]['sub_ded'];?>
							</span>	
					</td>
					<td style='text-align:right;'><b><?php echo $SubTotalSectionWise[$secCode]['sub_n_amount'];?></b></td>
				</tr>

		<tr >
			<td style='border: 1px solid black; border-collapse: collapse; text-align:right;' colspan='4' rowspan='2'>Total:</td>
			<?php 
			$col_gap=abs($size_n-$size_p);
			if($size_p<=$size_n){
				while($col_gap-->-1)
					echo "<td style='border: 1px solid black; border-collapse: collapse;'></td>";
				}
			 foreach($positive_rule as $a) {
				print "<td style='border: 1px solid black; border-collapse: collapse; text-align:left;'><b>{$sub_rule[$a['rule_id']]}</b></td>";
			} ?>
			<td style='border: 1px solid black; border-collapse: collapse; text-align:left;'><b><?php echo $sub_grossPF;?></b></td>
			<td style='border: 1px solid black; border-collapse: collapse; text-align:left;'><b><?php echo $sub_ern;?></b></td>
			<td style='border: 1px solid black; border-collapse: collapse; text-align:right;' rowspan='2'><b><?php echo $sub_n_amount;?></b></td>
		</tr>
		<tr>
			<?php        
			$col_gap=abs($size_n-$size_p);
			if($size_p>$size_n){
				while($col_gap-->1)
					echo "<td style='border: 1px solid black; border-collapse: collapse;'></td>";
				}
			foreach($negative_rule as $r){
				print "<td style='border: 1px solid black; border-collapse: collapse; text-align:right;'>{$sub_rule[$a['rule_id']]}</td>";
			} ?>		
			<td style='border: 1px solid black; border-collapse: collapse; text-align:right;'><b><?php echo $sub_pf; ?></b></td>
			<td style='border: 1px solid black; border-collapse: collapse; text-align:right;'><b><?php echo $sub_deduction; ?></b></td>
			<td style='border: 1px solid black; border-collapse: collapse; text-align:right;'><b><?php echo $sub_ded; ?></b></td>
		</tr>
		
		</tbody>
	</table>
	
	<script> 
	$(document).ready(function(){
	$('.gross').each(function(){
			var id=$(this).attr('id');
			$(this).text($('#t'+id).text());
		})
	window.print();
	})
</script>
</body>
</html>