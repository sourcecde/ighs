<?php 
@session_start();
$search=NULL;
//Set timezone from session variable
date_default_timezone_set($_SESSION[$guid]["timezone"]);
?>
 <script src="<?php echo $_SESSION[$guid]["absoluteURL"] ;?>/modules/Staff/js/jquery.dataTables.min.js"></script>
 <script src="<?php echo $_SESSION[$guid]["absoluteURL"] ;?>/modules/Staff/js/buttons.print.min.js"></script>
 <script src="<?php echo $_SESSION[$guid]["absoluteURL"] ;?>/modules/Staff/js/dataTables.buttons.min.js"></script>
<?php
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
if (isset($_GET["search"])) {
	if($_REQUEST['staff_f']!='')
	$staff_f=$_REQUEST['staff_f'];
	if($_REQUEST['month_f']!='')
	$month_f=$_REQUEST['month_f'];
	if($_REQUEST['year_f']!='')
	$year_f=$_REQUEST['year_f'];
	
	   $total_sql="SELECT SUM(`amount`) AS TOTAL FROM `lakshyasalarymaster` 
        WHERE `staff_id` = '".$_REQUEST['staff_f']."' AND `rule_id` IN (1,10,11,12,13,14) 
        AND `month` = '".$_REQUEST['month_f']."' AND `year_id` ='".$_REQUEST['year_f']."'";
     
	    $result_t=$connection2->prepare($total_sql);
		$result_t->execute();
		$total=$result_t->fetchAll();
	
	   //echo "<pre>ppp=";print_r($total);die;
	   
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
		
	    //echo "<pre>";print_r($rule);die;
	    
		$rule_impact=array();
		$rule_count=0;
		foreach($rule as $r)
		{
			$rule_impact[$r['rule_id']+0]=$r['impact'];
		}
		
		$sql1="SELECT gibbonstaff.gibbonStaffID,gibbonstaff.type,gibbonstaff.preferredName,gibbonstaff.sec_code,gibbonstaff.`priority` FROM gibbonstaff
				LEFT JOIN gibbonperson on gibbonperson.gibbonPersonID=gibbonstaff.gibbonPersonID";
		//$sql1.="WHERE (dateEnd IS NULL  OR dateEnd>='" . date("Y-m-d") . "')";		
		if($staff_f!='')
		$sql1.=" WHERE gibbonStaffID=".$staff_f;
		//$sql1.=" AND gibbonStaffID=".$staff_f;

		$sql1.=" ORDER BY gibbonstaff.`sec_code`,gibbonstaff.`priority`";
		
		
		
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
				LEFT JOIN gibbonperson on gibbonperson.gibbonPersonID=gibbonstaff.gibbonPersonID order by gibbonstaff.`sec_code`,gibbonstaff.priority";

		
		$result3=$connection2->prepare($sql3);
		$result3->execute();
		$staff_f_data=$result3->fetchAll();
	    //echo "<pre>";print_r($staff_f_data);die;
	
?>
			<form name="f1" id="f1" method="get" action="<?php print $_SESSION[$guid]["absoluteURL"]?>/index.php">
			<input type="hidden" name="q" value="/modules/<?php print $_SESSION[$guid]["module"] ?>/view_pay_structure.php">
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
									print "<option value='".$y['gibbonSchoolYearID']."'$s>".$y['name']."</option>";
								}?>
							</select>
						</td>
						<td>
							<input type='submit' value='Search'>
						</td>
					</tr>
			</table>
			</form>
			<h3>View Pay Structure:</h3>
		<?php
		
		$sql_year="SELECT * FROM `lakshyasalarymaster` ORDER BY master_id DESC LIMIT 1";
		$resultYear=$connection2->prepare($sql_year);
		$resultYear->execute();
		$resultYear=$resultYear->fetch(PDO::FETCH_ASSOC);
		$yearCode=$resultYear['year_id'];
		$currentMonth=$resultYear['month'];

		if($staff_f == '' && $month_f == '' && $year_f == ''){
			$sql5="SELECT * FROM `lakshyasalarymaster` 
					WHERE month = $currentMonth AND year_id= $yearCode";	
		}
		else{
			$sql5="SELECT * FROM `lakshyasalarymaster` WHERE 1";
			if($staff_f!='')
			$sql5.=" AND (staff_id=".$staff_f." OR staff_id=0)";
			if($month_f!='')
			$sql5.=" AND month=".$month_f;
			if($year_f!='')
			$sql5.=" AND year_id=".$year_f;
		}
		
		
		$result5=$connection2->prepare($sql5);
		$result5->execute();
		$structure=$result5->fetchAll();
		$structure_d=array();
		foreach($structure as $s){
			$structure_d[$s['year_id']][$s['month']][$s['staff_id']+0][$s['rule_id']+0]=$s['amount'];
		}
		$limit=6;
	foreach($year as $y){
		if(!array_key_exists($y['gibbonSchoolYearID']+0,$structure_d))
			continue;
			foreach($month_ar as $m){
					//echo "<pre>";print_r($month_ar);die;
				if(!array_key_exists($m,$structure_d[$y['gibbonSchoolYearID']+0]))
					continue;
				if($limit--==0)
						break;
?>	
	
	<center><b>Month: </b><i><?php echo $month_name[$m-1];?></i><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Year: </b><i><?php echo $y['name']; ?></i></center>
		<a href="#"><span class="button-print" id="<?php echo $m;?>_<?php echo $y['gibbonSchoolYearID']; ?>">Print</span></a>
		<a href="#"><span class="button-add" id="add_<?php echo $m;?>_<?php echo $y['gibbonSchoolYearID']; ?>" style='float:left; border: 1px; padding: 5px 10px; background: #ff731b; color: white;'>Add</span></a><br><br>
    <div style="width:1173px; height:707px; overflow:auto;">
    <?php if($staff_f == '' && $month_f == '' && $year_f == ''){?>
    <table width="100%" cellpadding="0" cellspacing="0">
	  <thead>
	  <tr>
	   	<th style='display:none'>Sec_code</th>
	
		<th style='display:none'>Priority</th>
		
		<th>Staff Name</th>
		<?php 
		//echo "<pre>pp=";print_r($rule);die;
		foreach($rule as $a) {
			print "<th>".$a['caption']."</th>";
		} ?>
		<th>Gross PF</th>
		<th>Total</th>
		<th>PF (<span id='PF_per'></span>%)</th>
		<th>ESI (<span id='ESI_per'></span>%)</th>
		<th>Advance</th>
		<th>Net Amount</th>
		<th>Action</th>
		</tr>
	  </thead>   
		<tbody>
		<?php 
		$total=0;
		foreach($staff_n as $sn) {
		//echo "<pre>";print_r($sn);die;
		$total_sql="SELECT SUM(`amount`) AS TOTAL FROM `lakshyasalarymaster` 
        WHERE `staff_id` = '".$sn['gibbonStaffID']."' AND `rule_id` IN (1,10,11,12,13,14) 
        AND `month` = '".$m."' AND `year_id` ='".$y['gibbonSchoolYearID']."'";
        
	    $result_t=$connection2->prepare($total_sql);
		$result_t->execute();
		$total=$result_t->fetchAll();
		
		$total_sql="SELECT  `amount` AS spl_amount FROM `lakshyasalarymaster` 
        WHERE `staff_id` = '".$sn['gibbonStaffID']."' AND `rule_id` = '14'
        AND `month` = '".$m."' AND `year_id` ='".$y['gibbonSchoolYearID']."'";
        
        $result_t=$connection2->prepare($total_sql);
		$result_t->execute();
		$spl_amount=$result_t->fetchAll();
		
		 $n_amount=0;
		 if(!array_key_exists($sn['gibbonStaffID']+0,$structure_d[$y['gibbonSchoolYearID']+0][$m]))
		  continue;
		?>
			<tr>
				<td style='display:none'>
					<?php echo $sn['sec_code'];?>
				</td>
            	<td style='display:none'>
					<?php echo $sn['priority'];?>
				</td>
			
				<td>
					<?php echo $sn['preferredName']."<br><small>".$sn['type']."</small>";?>
				</td>
				
				<?php
					foreach($rule as $r){
				?>
				<td style="text-align:right;" class='PAYBAND'>
					<?php
					if(array_key_exists($r['rule_id']+0,$structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0])){
						echo $structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][$r['rule_id']+0];
						if($rule_impact[$r['rule_id']+0]=='+')
							$n_amount+=$structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][$r['rule_id']+0]; 
						else
							$n_amount-=$structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][$r['rule_id']+0];
							
						//$total+=$n_amount;	
					}
					else
						echo "-";
					?>
				</td>
				
				<?php }
				           ?>
				<td  style="text-align:right;">
					<?php 
					echo $structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][98];
					?>
				</td >
				<td>
					<?php 
					//$a=$total[0]['TOTAL'];
					//$b=$structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][98];
					//$c=$a+$b;
					  echo $total[0]['TOTAL'];    
					?>
				</td>
				<td  style="text-align:right;">
					<?php 
						$pf=($structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][98]+0)*($structure_d[$y['gibbonSchoolYearID']+0][$m][0][97]+0)/100;
						echo round($pf);
						$n_amount-=round($pf);
					?>
				</td >
				<td  style="text-align:right;">
					<?php 
				//-----------------Commented by Shiva	
				//		$esi=($structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][98]+0)<=21000?ceil(($structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][98]+0)*($structure_d[$y['gibbonSchoolYearID']+0][$m][0][96]+0)/100):0;
                //-----------------Correction by Shiva Start
                        $pfmem=($structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][98]+0);
	                    $esi=(($structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][01]+0) +
	                          ($structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][04]+0) + 
	                          ($structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][10]+0) + 
	                          ($structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][11]+0) + 
	                          ($structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][12]+0) + 
	                          ($structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][13]+0) + 
	                          ($structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][14]+0) )<=21000 
	                          ?ceil((($structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][01]+0) +
	                                 ($structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][04]+0) +
	                                 ($structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][10]+0) +
	                                 ($structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][11]+0) + 
	                                 ($structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][12]+0) + 
	                                 ($structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][13]+0) + 
	                                 ($structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][14]+0) )
	                                 *($structure_d[$y['gibbonSchoolYearID']+0][$m][0][96]+0)/100):0;						
	            //-----------------Correction by Shiva Start        

					    //echo $esi+=$spl_amount;
					    //echo "<pre>";print_r($spl_amount);
					    
                        if ($pfmem<=1)  {
                            $esi=0 ;
                        }
                        
                        
						echo $esi;
						
						$n_amount-=$esi;
						
						?>
				</td >
				
				<td  style="text-align:right;">
					<?php 
					echo $structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][99];
					$n_amount-=$structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][99];
						?>
				</td >
				<td  style="text-align:right;">
					<?php echo $n_amount; ?>
				</td>
				<td>
					<a href='#' id='id_<?php echo $sn['gibbonStaffID']."_".$m."_".$y['gibbonSchoolYearID']; ?>' class='edit_rule_ps' ><img style="width: 16px" title='Edit' src='./themes/Default/img/config.png'/></a>
					<a href='#'  id='delid_<?php echo $sn['gibbonStaffID']."_".$m."_".$y['gibbonSchoolYearID']; ?>' class='delete_rule_ps'><img  style="width: 17px"  title='Edit' src='./themes/Default/img/garbage.png'/></a>
				</td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
	<?php }else{?>
	<table width="100%" cellpadding="0" cellspacing="0" class='myTable'>
	  <thead>
	  <tr>
	   	<th style='display:none'>Sec_code</th>
	
		<th style='display:none'>Priority</th>
		
		<th>Staff Name</th>
		<?php 
		//echo "<pre>pp=";print_r($rule);die;
		foreach($rule as $a) {
			print "<th>".$a['caption']."</th>";
		} ?>
		<th>Gross PF</th>
		<th>Total</th>
		<th>PF (<span id='PF_per'></span>%)</th>
		<th>ESI (<span id='ESI_per'></span>%)</th>
		<th>Advance</th>
		<th>Net Amount</th>
		<th>Action</th>
		</tr>
	  </thead>   
		<tbody>
		<?php 
		$total=0;
		foreach($staff_n as $sn) {
		//echo "<pre>";print_r($sn);die;
		$total_sql="SELECT SUM(`amount`) AS TOTAL FROM `lakshyasalarymaster` 
        WHERE `staff_id` = '".$sn['gibbonStaffID']."' AND `rule_id` IN (1,10,11,12,13,14) 
        AND `month` = '".$m."' AND `year_id` ='".$y['gibbonSchoolYearID']."'";
        
	    $result_t=$connection2->prepare($total_sql);
		$result_t->execute();
		$total=$result_t->fetchAll();
		
		$total_sql="SELECT  `amount` AS spl_amount FROM `lakshyasalarymaster` 
        WHERE `staff_id` = '".$sn['gibbonStaffID']."' AND `rule_id` = '14'
        AND `month` = '".$m."' AND `year_id` ='".$y['gibbonSchoolYearID']."'";
        
        $result_t=$connection2->prepare($total_sql);
		$result_t->execute();
		$spl_amount=$result_t->fetchAll();
		
		 $n_amount=0;
		 if(!array_key_exists($sn['gibbonStaffID']+0,$structure_d[$y['gibbonSchoolYearID']+0][$m]))
		  continue;
		?>
			<tr>
				<td style='display:none'>
					<?php echo $sn['sec_code'];?>
				</td>
            	<td style='display:none'>
					<?php echo $sn['priority'];?>
				</td>
			
				<td>
					<?php echo $sn['preferredName']."<br><small>".$sn['type']."</small>";?>
				</td>
				
				<?php
					foreach($rule as $r){
				?>
				<td style="text-align:right;" class='PAYBAND'>
					<?php
					if(array_key_exists($r['rule_id']+0,$structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0])){
						echo $structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][$r['rule_id']+0];
						if($rule_impact[$r['rule_id']+0]=='+')
							$n_amount+=$structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][$r['rule_id']+0]; 
						else
							$n_amount-=$structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][$r['rule_id']+0];
							
						//$total+=$n_amount;	
					}
					else
						echo "-";
					?>
				</td>
				
				<?php }
				           ?>
				<td  style="text-align:right;">
					<?php 
					echo $structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][98];
					?>
				</td >
				<td>
					<?php 
					//$a=$total[0]['TOTAL'];
					//$b=$structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][98];
					//$c=$a+$b;
					  echo $total[0]['TOTAL'];    
					?>
				</td>
				<td  style="text-align:right;">
					<?php 
						$pf=($structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][98]+0)*($structure_d[$y['gibbonSchoolYearID']+0][$m][0][97]+0)/100;
						echo round($pf);
						$n_amount-=round($pf);
					?>
				</td >
				<td  style="text-align:right;">
					<?php 
				//-----------------Commented by Shiva	
				//		$esi=($structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][98]+0)<=21000?ceil(($structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][98]+0)*($structure_d[$y['gibbonSchoolYearID']+0][$m][0][96]+0)/100):0;
                //-----------------Correction by Shiva Start
                        $pfmem=($structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][98]+0);
	                    $esi=(($structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][01]+0) +
	                          ($structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][04]+0) + 
	                          ($structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][10]+0) + 
	                          ($structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][11]+0) + 
	                          ($structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][12]+0) + 
	                          ($structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][13]+0) + 
	                          ($structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][14]+0) )<=21000 
	                          ?ceil((($structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][01]+0) +
	                                 ($structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][04]+0) +
	                                 ($structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][10]+0) +
	                                 ($structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][11]+0) + 
	                                 ($structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][12]+0) + 
	                                 ($structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][13]+0) + 
	                                 ($structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][14]+0) )
	                                 *($structure_d[$y['gibbonSchoolYearID']+0][$m][0][96]+0)/100):0;						
	            //-----------------Correction by Shiva Start        

					    //echo $esi+=$spl_amount;
					    //echo "<pre>";print_r($spl_amount);
					    
                        if ($pfmem<=1)  {
                            $esi=0 ;
                        }
                        
                        
						echo $esi;
						
						$n_amount-=$esi;
						
						?>
				</td >
				
				<td  style="text-align:right;">
					<?php 
					echo $structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][99];
					$n_amount-=$structure_d[$y['gibbonSchoolYearID']+0][$m][$sn['gibbonStaffID']+0][99];
						?>
				</td >
				<td  style="text-align:right;">
					<?php echo $n_amount; ?>
				</td>
				<td>
					<a href='#' id='id_<?php echo $sn['gibbonStaffID']."_".$m."_".$y['gibbonSchoolYearID']; ?>' class='edit_rule_ps' ><img style="width: 16px" title='Edit' src='./themes/Default/img/config.png'/></a>
					<a href='#'  id='delid_<?php echo $sn['gibbonStaffID']."_".$m."_".$y['gibbonSchoolYearID']; ?>' class='delete_rule_ps'><img  style="width: 17px"  title='Edit' src='./themes/Default/img/garbage.png'/></a>
				</td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
	<?php }?>
	</div>
	<input type='hidden' id='pf_per' value='<?php echo $structure_d[$y['gibbonSchoolYearID']+0][$m][0][97]+0;?>'>
	<input type='hidden' id='esi_per' value='<?php echo $structure_d[$y['gibbonSchoolYearID']+0][$m][0][96]+0;?>'>
	<script>	
	$(document).ready(function(){
		 $('#PF_per').html($('#pf_per').val());
		 $('#ESI_per').html($('#esi_per').val());
		});
	</script>	
<?php
		}
	}
}
?>

<script>
	 $(document).ready(function(){
		$('.myTable').DataTable();
			$('.button-print').click(function(){ 
				var id=$(this).attr('id');
				var url="<?php echo $_SESSION[$guid]["absoluteURL"] ;?>/modules/Staff/print_pay_structure.php?m_y="+id;
					<?php if($staff_f!=''){
					echo "url+='&sid=".$staff_f."';";
					}?>
					window.open(url, "", "width=800, height=600");
			});
		});
</script>
 
 <div id='hide_body' style='background-color :rgba(0,0,0, 0.7); width:100%; height:100%; position:fixed; left:0px; top:0px; z-index:100; display:none;'>
 </div>
 <div id='edit_panel_ps'  class='edit_panel' style="position:fixed; left:500px; top:50px; z-index:200; border:1px; padding:5px 10px; background-color :rgba(0,0,0, 0.6); color:white; width:250px; display:none;">
	<table class='blank' style='color:white;'>
		<?php foreach($rule as $a) {
			print"<tr>";
			print "<td>".$a['caption']."</td>";
			print "<td><input type='text' class='rule_input' id='rule_input_".$a['rule_id']."'></td>";
			print "<td></td>";
			} ?>
		<tr><td>Gross PF</td><td><input type='text' class='rule_input' id='rule_input_98'></td></tr>
		<tr><td>Advance</td><td><input type='text' class='rule_input' id='rule_input_99'></td></tr>
		<tr>
			<td colspan='2'>
			<br>
			<center>
			<input type='button' id='update_ps' value='UPDATE' style="border:1px; padding:5px; background:#ff731b; color:white;">
			<input type='button' class='close_panel' value='CLOSE' style="border:1px; padding:5px; background:#ff731b; color:white;">
			<input type='hidden' id='sid_v'>
			<input type='hidden' id='month_v'>
			<input type='hidden' id='year_v'>
			</center>
			</td>
		</tr>
		<tr>
	</table>
 </div>
 <div id='add_panel_ps'  class='edit_panel' style=" position:fixed; left:500px; top:20px; z-index:200; border:1px; padding:5px 10px; background-color :rgba(0,0,0, 0.6); color:white; width:250px; display:none;">
	<table class='blank' style='color:white;'>
		<tr><td colspan='2'><select name='staff_add' id='staff_add'>
								<option value=''> Select Staff </option>
								<?php foreach($staff_f_data as $n){
									$s=$n['gibbonStaffID']==$staff_f?'selected':'';
									print "<option value='".$n['gibbonStaffID']."' ".$s.">".$n['preferredName']."</option>";
								}?>
							</select></td></tr>
		<?php foreach($rule as $a) {
			print"<tr>";
			print "<td>".$a['caption']."</td>";
			print "<td><input type='text' class='rule_input_a' id='rule_input_".$a['rule_id']."'></td>";
			print "<td></td>";
			} ?>
		<tr><td>Gross PF</td><td><input type='text' class='rule_input_a' id='rule_input_98'></td></tr>
		<tr><td>Advance</td><td><input type='text' class='rule_input_a' id='rule_input_99'></td></tr>
		<tr>
			<td colspan='2'>
			<br>
			<center>
			<input type='button' id='add_ps' value='ADD' style="border:1px; padding:5px; background:#ff731b; color:white;">
			<input type='button' class='close_panel' value='CLOSE' style="border:1px; padding:5px; background:#ff731b; color:white;">
			<input type='hidden' id='month_add'>
			<input type='hidden' id='year_add'>
			</center>
			</td>
		</tr>
		<tr>
	</table>
 </div>
 