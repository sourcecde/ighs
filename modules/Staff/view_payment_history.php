<?php 
@session_start() ; 
$search=NULL;
//Set timezone from session variable
date_default_timezone_set($_SESSION[$guid]["timezone"]);

if (isActionAccessible($guid, $connection2, "/modules/Staff/staff_view_details.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;      
	print "</div>" ;
}
else {
$staff_f='';
$month_f='';
$year_f='';
if (isset($_GET["search"])){
    	
    	if($_REQUEST['staff_f']!='')
    	$staff_f=$_REQUEST['staff_f'];
    	if($_REQUEST['month_f']!='')
    	$month_f=$_REQUEST['month_f'];
    	if($_REQUEST['year_f']!='')
    	$year_f=$_REQUEST['year_f'];
   
    }
	try{
	    
	    
		$sql="SELECT * FROM `lakshyasalaryrule` ORDER BY `impact`";
		$result=$connection2->prepare($sql);
		$result->execute();
		$rule=$result->fetchAll();
	    
	    }
		catch(PDOException $e){
			echo $e;
		}
		$rule_impact=array();
		$positive_rule=array();
		$negative_rule=array();
		foreach($rule as $r)
		{
			$rule_impact[$r['rule_id']+0]=$r['impact'];
			if($r['impact']=='+')
				$positive_rule[]=$r;
			else
				$negative_rule[]=$r;
		}
		$sql1="SELECT gibbonstaff.gibbonStaffID,gibbonstaff.jobTitle,gibbonstaff.priority,gibbonstaff.preferredName,gibbonstaff.dateStart,sec_code FROM gibbonstaff
				LEFT JOIN gibbonperson on gibbonperson.gibbonPersonID=gibbonstaff.gibbonPersonID";
		//$sql1.=" WHERE (dateEnd IS NULL  OR dateEnd>='" . date("Y-m-d") ."')";		
				if($staff_f!='')
		$sql1.=" WHERE gibbonStaffID=".$staff_f;
		//$sql1.=" AND gibbonStaffID=".$staff_f;
		$sql1.=" ORDER BY gibbonstaff.priority,gibbonstaff.category";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		$staff_n=$result1->fetchAll();
		
		$staff_name=array();
		$sql2="SELECT * FROM `gibbonschoolyear` ORDER BY `gibbonSchoolYearID` DESC";
		$result2=$connection2->prepare($sql2);
		$result2->execute();
		$year=$result2->fetchAll();
		
			$month_ar=array(3,2,1,12,11,10,9,8,7,6,5,4);
			$month_name=array('January','February','March','April','May','June','July','August','September','October','November','December');
			
		$sql3="SELECT gibbonstaff.gibbonStaffID,gibbonstaff.preferredName FROM gibbonstaff
				LEFT JOIN gibbonperson on gibbonperson.gibbonPersonID=gibbonstaff.gibbonPersonID";
		$result3=$connection2->prepare($sql3);
		$result3->execute();
		$staff_f_data=$result3->fetchAll();
		?>
			<form name="f1" id="f1" method="get" action="<?php print $_SESSION[$guid]["absoluteURL"]?>/index.php">
			<input type="hidden" name="q" value="/modules/<?php print $_SESSION[$guid]["module"] ?>/view_payment_history.php">
			<input name="search" id="search" maxlength=20 value="<?php print $search ?>" type="hidden">
			<table width="80%" cellpadding="0" cellspacing="0">
					<tr>
						<td>
							<select name='staff_f' id='staff_f'>
								<option value=''> Select Staff </option>
								<?php foreach($staff_f_data as $n){
									$s=$n['gibbonStaffID']==$staff_f?'selected':'';
									print "<option value='".$n['gibbonStaffID']."' ".$s.">".$n['preferredName']."</option>";
								}?>
							</select>
						</td>
						<td>
							<select name='month_f' id='month_f'>
								<option value=''> Select Month </option>
								<option value='4' <?php echo $month_f==4?'selected':''; ?>>April</option>
								<option value='5' <?php echo $month_f==5?'selected':''; ?>>May</option>
								<option value='6' <?php echo $month_f==6?'selected':''; ?>>June</option>
								<option value='7' <?php echo $month_f==7?'selected':''; ?>>July</option>
								<option value='8' <?php echo $month_f==8?'selected':''; ?>>August</option>
								<option value='9' <?php echo $month_f==9?'selected':''; ?>>September</option>
								<option value='10' <?php echo $month_f==10?'selected':''; ?>>October</option>
								<option value='11' <?php echo $month_f==11?'selected':''; ?>>November</option>
								<option value='12' <?php echo $month_f==12?'selected':''; ?>>December</option>
								<option value='1' <?php echo $month_f==1?'selected':''; ?>>January</option>
								<option value='2' <?php echo $month_f==2?'selected':''; ?>>February</option>
								<option value='3' <?php echo $month_f==3?'selected':''; ?>>March</option>
							</select>
						</td> 
						<td>
							<select name='year_f' id='year_f'>
								<option value=''> Select Year </option>
								<?php foreach($year as $y){
									$s=$y['gibbonSchoolYearID']==$year_f?'selected':'';
									print "<option value='".$y['gibbonSchoolYearID']."' $s>".$y['name']."</option>";
								}?>
							</select>
						</td>
						<td>
							<input type='submit' value='Search'>
						</td>
					</tr>
			</table>
			</form>
			<h3>View Payment History:</h3>
		<?php
		$sql5="SELECT *,lakshyasalarymaster.* FROM lakshyasalarypayment
				LEFT JOIN lakshyasalarymaster ON  lakshyasalarymaster.master_id=lakshyasalarypayment.master_id
			WHERE 1";
		if($staff_f!='')
		$sql5.=" AND lakshyasalarymaster.staff_id=".$staff_f;
		if($month_f!='')
		$sql5.=" AND lakshyasalarymaster.month=".$month_f;
		if($year_f!='')
		$sql5.=" AND lakshyasalarymaster.year_id=".$year_f;
		
		$result5=$connection2->prepare($sql5);
		$result5->execute();
		$structure=$result5->fetchAll();
		$structure_d=array();
		foreach($structure as $s){
			$structure_d[$s['year_id']][$s['month']][$s['staff_id']+0][$s['rule_id']+0]=$s['paid_amount'];
		}
		
		$sql6="SELECT * FROM `lakshyasalarymaster` WHERE `rule_id` IN (97,96)";
		if($month_f!='')
		$sql6.=" AND month=".$month_f;
		if($year_f!='')
		$sql6.=" AND year_id=".$year_f;
		$result6=$connection2->prepare($sql6);
		$result6->execute();
		$structure=$result6->fetchAll();
		$pf_arr=array();
		foreach($structure as $s){
			$pf_arr[$s['year_id']][$s['month']][$s['staff_id']+0][$s['rule_id']+0]=$s['amount'];
		} 
		
		$sql7="SELECT * FROM `lakshyasalaryattendance` WHERE 1";
		$result7=$connection2->prepare($sql7);
		$result7->execute();
		$atndnc=$result7->fetchAll();
		$attendance=array();
		foreach($atndnc as $a){
			$attendance[$a['year_id']][$a['month']][$a['staff_id']]=$a['attended'];
		}
			
		$limit=6;
	foreach($year as $y){
		if(!array_key_exists($y['gibbonSchoolYearID']+0,$structure_d))
			continue;
			foreach($month_ar as $m){
				
				if(!array_key_exists($m,$structure_d[$y['gibbonSchoolYearID']+0]))
					continue;
				 if($limit--==0)
						break;
					
?>	


	<center><b>Month: </b><i><?php echo $month_name[$m-1];?></i><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Year: </b><i><?php echo $y['name']; ?></i></center>
		<a href="#"><span class="button-print" id="<?php echo $m;?>_<?php echo $y['gibbonSchoolYearID']; ?>">Print</span></a><br><br>
	<div style='width:1154px; height:586px; overflow:auto;'>
	
	<table width="100%" cellpadding="0" cellspacing="0" class='myTable'>
	  <thead>
	  <tr>
		<th rowspan='2' style='display:none'>#</th>
		<th rowspan='2'>Staff Name</th>
		<th rowspan='2'>Gross Salary Pay</th>
		<th rowspan='2'>W.Day</th>
			<?php 
						$p_l=sizeOf($positive_rule);
						$n_l=sizeOf($negative_rule);
				if($p_l <= $n_l) 
					for($i=1;$i<$p_l-$n_l;$i++)
					echo "<th></th>";
			?>
		<?php foreach($positive_rule as $a) {
			print "<th>".$a['caption']."</th>";
			$sub_rule[$a['rule_id']]=0;
			} ?>
		<th>Gross PF</th>
		<th>Total ERN.</th>
		<th rowspan='2'>Net Amount</th>
	  </tr>
	  <tr>
        <?php 
					if($p_l >= $n_l) 
						for($i=2;$i<$p_l-$n_l;$i++)
							echo "<th></th>";
		?>
		<?php foreach($negative_rule as $a) {
			print "<th>".$a['caption']."</th>";
			$sub_rule[$a['rule_id']]=0;
			} ?>
		<th>ESI (<span class='ESI_per' id='<?php echo $m; ?>'></span>%)</th>	
		<th>PF (<span class='PF_per' id='<?php echo $m; ?>'></span>%)</th>
		<th>Advance</th>
		<th>Total DED.</th>
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
		$counter = 0;
		$secCode='';
		$SubTotalSectionWise=array();
		foreach($staff_n as $sn) {
			$n_amount=0;	
			$total_ern=0;
			$total_ded=0;
			if(!array_key_exists($sn['gibbonStaffID']+0,$structure_d[$y['gibbonSchoolYearID']+0][$m]))
				continue;
			if(!array_key_exists($sn['sec_code'], $SubTotalSectionWise)){
				$SubTotalSectionWise[$sn['sec_code']]=array();
			}
			if($secCode!='' && $secCode!=$sn['sec_code']){
				?>
				<tr style="font-weight: bold;">
					<td style='text-align:right; height:50px;' colspan='3'><b>Sub Total for Section <?php echo $secCode; ?>:</b></td>
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
				
				
				
				
				
				
				<?php
			}
			$secCode=$sn['sec_code'];
		?>
			<tr>
				<td style='display:none'>
					<?= $sn['priority']; ?>
				</td>

				<td style='vertical-align:top'>
					<?php
					echo "<span><b>".$sn['preferredName']."<b></span><br>";
					echo "(<small>".$sn['jobTitle']."</small>)<br>";
					echo "<small><b>DOJ: </b>".date('d/m/y',strtotime($sn['dateStart']))."</small>";
					?>
				</td>
				
				<td>
					<span class='gross' id='g<?= $sn['gibbonStaffID']."_".$m;?>'></span>
				</td>
				<td>
					<?php echo $attendance[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0];?>
				</td>
						<?php 
						      $gap_col=abs(sizeOf($positive_rule)-sizeOf($negative_rule));							  
						?>
				<?php
					if(sizeOf($positive_rule)>sizeOf($negative_rule)){
						$i=0;
						$j=0;
			foreach($positive_rule as $r){
				?>
				<td style="position: relative; width:200px;   padding:0; margin:0;">
					<span style='position:absolute; top:0; left:0; padding:5px;'>
					<?php
					if (array_key_exists($r['rule_id']+0,$structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0])){
						echo $a=$structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][$r['rule_id']+0];
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
					</span><hr style="width:100%;">
					<span style='position:absolute; bottom:0; right:0; padding:5px;'>
					<?php
					if(--$gap_col<2 && $i<sizeOf($negative_rule)){
						if (array_key_exists($negative_rule[$i]['rule_id'],$structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0])){
							echo $b=$structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][$negative_rule[$i]['rule_id']+0];
							$n_amount-=$b;
							$sub_rule[$negative_rule[$i]['rule_id']]+=$b;
							$total_ded+=$b;
							if(!array_key_exists($negative_rule[$i]['caption'], $SubTotalSectionWise[$sn['sec_code']])){
								$SubTotalSectionWise[$sn['sec_code']][$negative_rule[$i]['caption']]=$b;
							}
							else{
								$SubTotalSectionWise[$sn['sec_code']][$negative_rule[$i]['caption']]+=$b;
							}
						$i++;
						}
						else
							echo "-";
					}
					else if($i>=sizeOf($negative_rule))
					{
						if($j!=0){
						$pf=round($structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][98]*$pf_arr[$y['gibbonSchoolYearID']+0][$m][0][97]/100);
						echo $pf;
						$n_amount-=$pf;
						$sub_pf+=$pf;
						$total_ded+=$pf;
							if(!array_key_exists('sub_pf', $SubTotalSectionWise[$sn['sec_code']])){
								$SubTotalSectionWise[$sn['sec_code']]['sub_pf']=$pf;
							}
							else{
								$SubTotalSectionWise[$sn['sec_code']]['sub_pf']+=$pf;
							}
						}
						else{
   					  //$esi=($structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][98]+0)<=21000?ceil(($structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][98]+0)*($pf_arr[$y['gibbonSchoolYearID']+0][$m][0][96]+0)/100):0;
						$esi=(($structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][98]+0)+($structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][12]+0))<=21000?ceil((($structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][98]+0)+($structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][12]+0))*($pf_arr[$y['gibbonSchoolYearID']+0][$m][0][96]+0)/100):0;
						echo $esi;
						$n_amount-=$esi;
						$sub_esi+=$esi;
						$total_ded+=$esi;
							if(!array_key_exists('sub_esi', $SubTotalSectionWise[$sn['sec_code']])){
								$SubTotalSectionWise[$sn['sec_code']]['sub_esi']=$esi;
							}
							else{
								$SubTotalSectionWise[$sn['sec_code']]['sub_esi']+=$esi;
							}
						$j++;
						}
					}
					else
							echo "-";
					?>
					</span>
					<!--Testing -->
				</td>
				<?php 
						}
					} 
					//Not required now. Else part.
				?>
				<td style='position: relative; width:200px; margin:0; padding:0'>
					<span style='position:absolute; top:0; left:0; padding:5px;'>
					<?php 
					echo $gross_pf=($structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][98]+0);
					$sub_grossPF+=$gross_pf;
							if(!array_key_exists('sub_grossPF', $SubTotalSectionWise[$sn['sec_code']])){
								$SubTotalSectionWise[$sn['sec_code']]['sub_grossPF']=$gross_pf;
							}
							else{
								$SubTotalSectionWise[$sn['sec_code']]['sub_grossPF']+=$gross_pf;
							}
					?>
					</span><hr>
					<span style='position:absolute; bottom:0; right:0; padding:5px;'>	
						<?php 
							echo $advance=$structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][99];
							$n_amount-=$advance;
							$sub_deduction+=$advance;
							$total_ded+=$advance;
							if(!array_key_exists('sub_deduction', $SubTotalSectionWise[$sn['sec_code']])){
								$SubTotalSectionWise[$sn['sec_code']]['sub_deduction']=$advance;
							}
							else{
								$SubTotalSectionWise[$sn['sec_code']]['sub_deduction']+=$advance;
							}
						?>
					</span>	
				</td>
				<td style='position: relative; width:200px; margin:0; padding:0'>
					<span style='position:absolute; top:0; left:0; padding:5px;' id='tg<?php echo $sn['gibbonStaffID']."_".$m; ?>'>
						<?php echo $total_ern;
							$sub_ern+=$total_ern;
							if(!array_key_exists('sub_ern', $SubTotalSectionWise[$sn['sec_code']])){
								$SubTotalSectionWise[$sn['sec_code']]['sub_ern']=$total_ern;
							}
							else{
								$SubTotalSectionWise[$sn['sec_code']]['sub_ern']+=$total_ern;
							}
						?>
					</span><hr>
					<span style='position:absolute; bottom:0; right:0; padding:5px;'>	
						<?php  echo $total_ded;
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
				<td style='text-align:right;'>
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
			</tr>
		<?php } ?>
		
		
		<tr style="font-weight: bold;">
					<td style='text-align:right; height:50px;' colspan='3'><b>Sub Total for Section <?php echo $secCode; ?>:</b></td>
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
				
				
		
		<tfoot>
		<tr style="font-weight: bold;">
			<td style='text-align:right; height:50px;' colspan='3'><b>Sub Total:</b></td>
			
			
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
					if (array_key_exists($r['rule_id']+0,$sub_rule)){
					echo "<b>{$sub_rule[$r['rule_id']+0]}</b>";
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
						if (array_key_exists($negative_rule[$i]['rule_id'],$sub_rule)){
							echo "<b>{$sub_rule[$negative_rule[$i++]['rule_id']]}</b>";
						}
						else
							echo "-";
					}
					else if($i==sizeOf($negative_rule))
					{
						echo "<b>{$sub_esi}</b>";
						$i++;
					}
					else if($i==sizeOf($negative_rule)+1)
					{
						echo "<b>{$sub_pf}</b>";
						
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
					<b><?php echo $sub_grossPF;?></b>
					</span><hr>
					<span style='position:absolute; bottom:0; right:0; padding:5px;'>	
					<b><?php echo $sub_deduction;?></b></span>
			</td>
			<td style='position: relative; width:200px; margin:0; padding:0;'>
					<span style='position:absolute; top:0; left:0; padding:5px;'>
						<?php echo $sub_ern;?>
					</span><hr>
					<span style='position:absolute; bottom:0; right:0; padding:5px;'>	
						<?php  echo $sub_ded;?>
					</span>	
			</td>
			<td style='text-align:right;'><b><?php echo $sub_n_amount;?></b></td>
		</tr>
		</tfoot>
		</tbody>
	</table>
	</div>
	<input type='hidden' id='pf_per<?php echo $m; ?>' value='<?php echo $pf_arr[$y['gibbonSchoolYearID']+0][$m][0][97];?>'>
	<input type='hidden' id='esi_per<?php echo $m; ?>' value='<?php echo $pf_arr[$y['gibbonSchoolYearID']+0][$m][0][96];?>'>
<?php
		}
	}
}


?>

<script src="<?php echo $_SESSION[$guid]["absoluteURL"] ;?>/modules/Staff/js/jquery.dataTables.min.js"></script>

 <script>
	 $(document).ready(function(){
        $('.button-print').click(function(){ 		
			var id=$(this).attr('id');
			var url="<?php echo $_SESSION[$guid]["absoluteURL"] ;?>/modules/Staff/print_payment_history.php?m_y="+id;
			<?php if($staff_f!=''){
			echo "url+='&sid=".$staff_f."';";
			}?>
			window.open(url, "", "width=800, height=600");
		});		 
		$('.PF_per').each(function(){
			var id=$(this).attr('id');
			$(this).html($('#pf_per'+id).val());
		})
		$('.ESI_per').each(function(){
			var id=$(this).attr('id');
			$(this).html($('#esi_per'+id).val());
		})
		/*$('.gross').each(function(){
			var id=$(this).attr('id');
			$(this).text($('#t'+id).text());
		})*/
		$('.myTable').DataTable();
		
	});
 </script>

