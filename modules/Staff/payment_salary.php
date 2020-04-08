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
	try{
		$sql="SELECT * FROM `lakshyasalaryrule` where active=1";
		$result=$connection2->prepare($sql);
		$result->execute();
		$rule=$result->fetchAll();
		}
		catch(PDOException $e){
			echo $e;
		}
		try{
		$sql="SELECT `gibbonStaffID`,gibbonstaff.preferredName,gibbonrollgroup.name as section  FROM `gibbonstaff`
		LEFT JOIN gibbonperson on gibbonperson.gibbonPersonID=gibbonstaff.gibbonPersonID 
		LEFT JOIN gibbonrollgroup on gibbonperson.gibbonRoleIDPrimary=gibbonrollgroup.gibbonRollGroupID
		ORDER BY gibbonstaff.priority, gibbonstaff.category DESC";
		//ORDER BY gibbonstaff.priority";
		//LEFT JOIN gibbonperson on gibbonperson.gibbonPersonID=gibbonstaff.gibbonPersonID WHERE gibbonperson.dateEnd IS NULL ORDER BY gibbonstaff.priority";
		$result1=$connection2->prepare($sql);
		$result1->execute();
		$staff=$result1->fetchAll();

		}
		catch(PDOException $e){
			echo $e;
		}
		try{
		$sql="SELECT `gibbonSchoolYearID`, `name` FROM `gibbonschoolyear` ORDER BY name";
		$result2=$connection2->prepare($sql);
		$result2->execute();
		$year=$result2->fetchAll();
		}
		catch(PDOException $e){
			echo $e;
		}
     $url="{$_SESSION[$guid]["absoluteURL"]}/modules/{$_SESSION[$guid]["module"]}/process_payment.php";
?>
    <center><h4><?php  if(isset($_GET['salary_p_massage'])){ echo $_GET['salary_p_massage'];} ?></h4></center>
	<h3>Process Payment: </h3>
	<form  id="form_month_year" action='<?php print $_SESSION[$guid]["absoluteURL"]?>/index.php'>
	<input type="hidden" name="q" value="/modules/<?php print $_SESSION[$guid]["module"] ?>/payment_salary.php">
	
	<table width="60%" cellpadding="0" cellspacing="0">
	<tr>
		<td>
			<select name='month_f' id='month_f'>
				<?php $s_m=''; if(isset($_REQUEST['month_f']))$s_m=$_REQUEST['month_f'];?>
				<option value=''> Select Month </option>
				<option value='4' <?php echo $s_m==4?'selected':'';?>>April</option>
				<option value='5' <?php echo $s_m==5?'selected':'';?>>May</option>
				<option value='6' <?php echo $s_m==6?'selected':'';?>>June</option>
				<option value='7' <?php echo $s_m==7?'selected':'';?>>July</option>
				<option value='8' <?php echo $s_m==8?'selected':'';?>>August</option>
				<option value='9' <?php echo $s_m==9?'selected':'';?>>September</option>
				<option value='10' <?php echo $s_m==10?'selected':'';?>>October</option>
				<option value='11' <?php echo $s_m==11?'selected':'';?>>November</option>
				<option value='12' <?php echo $s_m==12?'selected':'';?>>December</option>
				<option value='1' <?php echo $s_m==1?'selected':'';?>>January</option>
				<option value='2' <?php echo $s_m==2?'selected':'';?>>February</option>
				<option value='3' <?php echo $s_m==3?'selected':'';?>>March</option>
			</select>
		</td>
		<td>
			<select name='year_f' id='year_f'>
				<option value=''> Select Year </option>
				<?php foreach($year as $y){
					$s=''; if(isset($_REQUEST['year_f'])){$s=$_REQUEST['year_f']==$y['gibbonSchoolYearID']?'selected':'';}
					print "<option value='".$y['gibbonSchoolYearID']."' ".$s.">".$y['name']."</option>";
				}?>
			</select>
		</td>
		<td>
			<input type='submit' value='Select' name="month_year_s_p" id="month_year_s_p"  style="float:right;">
		</td>
	</tr>
	</table>
	
	</form>
	<?php 
	if(isset($_REQUEST['month_year_s_p'])){
		$query="SELECT `payment_id` FROM `lakshyasalarypayment` WHERE `master_id` IN (SELECT `master_id` FROM `lakshyasalarymaster` WHERE `month`=".$_REQUEST['month_f']." AND `year_id`=".$_REQUEST['year_f'].")";
		$rslt=$connection2->prepare($query);
		$rslt->execute();
		if($rslt->rowcount()>0){
			print "<h3>Payment already done for selected month & year. Do you want to recreate it?</h3>";
			?>
			
		<!--	<div style='width:869px; height:564px; overflow:auto;'>-->
				<form  id="form_overwrite" action='<?php print $_SESSION[$guid]["absoluteURL"]?>/index.php'>
				<input type="hidden" name="q" value="/modules/<?php print $_SESSION[$guid]["module"] ?>/payment_salary.php">
					<input type='hidden' name='month_f' value='<?php echo $_REQUEST['month_f']; ?>'>
					<input type='hidden' name='year_f' value='<?php echo $_REQUEST['year_f']; ?>'>
				<table width="40%" cellpadding="0" cellspacing="0">
					<tr>
						<td>
							<input type='submit' value='Recreate' name="overwrite" id="overwrite"  style="float:right;">
						</td>
				</form>
						<td>
							<a href='#'><input type='submit' value='Cancel'  style="float:right;"></a>
						</td>
					</tr>
				</table>
			<?php
		}
		else{
			try {
			$sql3="SELECT * FROM `lakshyasalarymaster` WHERE `month`=".$_REQUEST['month_f']." AND `year_id`=".$_REQUEST['year_f'];
			$result3=$connection2->prepare($sql3);
			$result3->execute();
			$structure=$result3->fetchAll();
			}
			catch(PDOException $e){
				echo $e;
			}
		//print_r($structure);
			if($result3->rowcount()==0){
				print "<h3>Pay Structure hasn't created for the selected month & year yet. Please Create it First.</h3>";
			}
			else{
				$structure_d=array();
				foreach($structure as $sd){
					$structure_d[$sd['staff_id']][$sd['rule_id']]=$sd['amount'];
				}
				$attendance=array();
				$wd=cal_days_in_month(CAL_GREGORIAN,$_REQUEST['month_f'],date('Y'));
				foreach($staff as $s){
					$attendance[$s['gibbonStaffID']+0]=$wd;
				}
				$overwrite=false;
				displayPanel($url,$staff,$structure_d,$rule,$attendance,$overwrite);
			}
		}
	}
	else if(isset($_REQUEST['overwrite']))
	{
			$numOfDays=cal_days_in_month(CAL_GREGORIAN,$_REQUEST['month_f'],$_REQUEST['year_f']);
			try {
			$sql3="SELECT staff_id,rule_id,amount
			FROM `lakshyasalarymaster` 
			LEFT JOIN `lakshyasalarypayment` ON `lakshyasalarypayment`.master_id=`lakshyasalarymaster`.master_id
			WHERE `month`=".$_REQUEST['month_f']." AND `year_id`=".$_REQUEST['year_f'];
			$result3=$connection2->prepare($sql3);
			$result3->execute();
			$structure=$result3->fetchAll();
			}
			catch(PDOException $e){
				echo $e;
			}
			
			 
			try {
			$sql4="SELECT `staff_id`, `attended` FROM `lakshyasalaryattendance` 
			WHERE `month`=".$_REQUEST['month_f']." AND `year_id`=".$_REQUEST['year_f'];
			$result4=$connection2->prepare($sql4);
			$result4->execute();
			$atd=$result4->fetchAll();
			}
			catch(PDOException $e){
				echo $e;
			}
			$attendance=array();
			foreach($atd as $a){
				$attendance[$a['staff_id']]=$a['attended'];
			}
			$structure_d=array();
				foreach($structure as $sd){
					if($sd['staff_id']!=0)
					  if ($structure_d[$sd['staff_id']][$sd['rule_id']]!=7)
					      $structure_d[$sd['staff_id']][$sd['rule_id']]=round($sd['amount']*$attendance[$sd['staff_id']]/$numOfDays);
					  if ($structure_d[$sd['staff_id']][$sd['rule_id']] =7)
					      $structure_d[$sd['staff_id']][$sd['rule_id']]=round($sd['amount']);
				}
			
				$overwrite=true;
				displayPanel($url,$staff,$structure_d,$rule,$attendance,$overwrite);
	}
	?>	
		
<?php
 } 
 
 function displayPanel($url,$staff,$structure_d,$rule,$attendance,$overwrite)
 {
	?> 
	<div style='width:1161px; height:586px; overflow:auto;'>
	    
	    
	    
	<div id='create_panel'>
		<form method="POST" action='<?=$url?>'>
		<table width="40%" cellpadding="0" cellspacing="0">
		<tr><td>Date: <input type='text' id='date' name='date' value='<?php echo date('d/m/Y');?>'></td></tr>
		<input type='hidden' name='month' value='<?php echo $_REQUEST['month_f'];?>'>
		<input type='hidden' name='year' value='<?php echo $_REQUEST['year_f'];?>'>
		<input type='hidden' name='overwrite' value='<?php echo $overwrite;?>'>
		</table>
		<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<th>Staff<br>Name</th>
			<?php foreach($rule as $r){
				print "<th>".$r['caption']."</th>";
			}?>
			<th>Gross PF</th>
			<th>Advance</th>
			<th><small>Attended<br>Day</small></th>
		</tr>
			<?php
				foreach($staff as $s){
					$s_id=$s['gibbonStaffID']+0;
					$n_amount=0;
					if(!array_key_exists($s_id,$structure_d))
						continue;
				 print "<tr>";
					print "<td> ".$s['preferredName']."</td>";
						foreach($rule as $r){
							$r_id=$r['rule_id']+0;
							print"<td>";
							if(array_key_exists($r_id,$structure_d[$s_id])){
							print "<input type='text' name='".$s_id."_".$r_id."' id='".$s_id."_".$r_id."' class='".$s_id."_rule' value='".$structure_d[$s_id][$r_id]."' style='width:100%' readonly>";
							print "<input type='hidden'  id='".$s_id."_".$r_id."_old'  value='".$structure_d[$s_id][$r_id]."'>";
							}
							print "</td>";
						}
					print "<td><input type='text' name='".$s_id."_grossPF' id='".$s_id."_grossPF' value='".$structure_d[$s_id][98]."' class='".$s_id."_rule' style='width:100%' readonly>"; 
					print "<input type='hidden' name='".$s_id."_grossPF_old' id='".$s_id."_grossPF_old' value='".$structure_d[$s_id][98]."'></td>"; 
					print "<td><input type='text' name='".$s_id."_deduction' id='".$s_id."_deduction' value='".$structure_d[$s_id][99]."' style='width:100%' readonly></td>"; 
						
						
					print "<td><input type='text' name='".$s_id."_ad' id='".$s_id."#ad' class='atnd_day' value='".$attendance[$s_id]."' style='width:100%'></td>"; 
					print "<td><input type='hidden' name='".$s_id."_working_day' id='".$s_id."_working_day' value='".$attendance[$s_id]."'></td>"; 
		
				 print "</tr>";	
				}
			?>
				<tr><td colspan='<?php echo sizeof($rule)+4;?>'><center><input type='submit' name='payment_s' value='Payment'></center></td></tr>
				<input type='hidden' name='month_s' id='month_s'>
				<input type='hidden' name='year_s' id='year_s'>
			</form>
		</table>
	</div>
	</div>
<?php		
 }
 
 ?>
<script type="text/javascript">
	$(function() {
		$( "#date" ).datepicker({ dateFormat: 'dd/mm/yy' });
	});
</script>