<?php
@session_start() ;
include "../../config.php" ;
try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}
$staff_id='';
$id=$_REQUEST['m_y'];
$m_y=explode("_",$id);
if(isset($_REQUEST['sid'])){
	$staff_id=$_REQUEST['sid']+0;
}
	
		$sql="SELECT * FROM `lakshyasalaryrule` where active=1";
		$result=$connection2->prepare($sql);
		$result->execute();
		$rule=$result->fetchAll();
		
		$rule_impact=array();
		$rule_count=0;
		foreach($rule as $r)
		{
			$rule_impact[$r['rule_id']+0]=$r['impact'];
		}
		
		$sql1="SELECT gibbonstaff.gibbonStaffID,gibbonstaff.type,gibbonstaff.preferredName FROM gibbonstaff";
		//$sql1.=" WHERE (dateEnd IS NULL  OR dateEnd>='" . date("Y-m-d") . "')";			
				if($staff_id!='')
		$sql1.=" WHERE gibbonStaffID=".$staff_id;
		//$sql1.=" AND gibbonStaffID=".$staff_id;
		$sql1.=" ORDER BY gibbonstaff.priority";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		$staff_n=$result1->fetchAll();
		
		$sql2="SELECT * FROM `gibbonschoolyear` WHERE `gibbonSchoolYearID`=".$m_y[1];
		$result2=$connection2->prepare($sql2);
		$result2->execute();
		$year=$result2->fetch();
			$month_name=array('January','February','March','April','May','June','July','August','September','October','November','December');
		
		$sql5="SELECT * FROM `lakshyasalarymaster` WHERE 1";
		if($staff_id!='')
		$sql5.=" AND (staff_id=".$staff_id." OR staff_id=0)";
	
		$sql5.=" AND month=".($m_y[0]+0);
		$sql5.=" AND year_id=".($m_y[1]+0);
		
		$result5=$connection2->prepare($sql5);
		$result5->execute();
		$structure=$result5->fetchAll();
		$structure_d=array();
		foreach($structure as $s){
			$structure_d[$s['staff_id']+0][$s['rule_id']+0]=$s['amount'];
		}
 ?>
 <script type="text/javascript" src="<?php echo $_SESSION[$guid]["absoluteURL"] ;?>/lib/jquery/jquery.js"></script>
 <h3>Pay Structure Register For Month: <?php echo $month_name[($m_y[0]-1)];?> of Year: <?php echo $year['name'];?></h3>
 
 <table width="100%" cellpadding="5px" cellspacing="0" style="border: 1px solid black; border-collapse: collapse;">
 	<thead>
	  <tr style="border: 1px solid black; border-collapse: collapse;">
		<th>Staff Name</th>
		<?php foreach($rule as $a) {
		print "<th>{$a['caption']}</th>";
			} ?>
		<th>Gross PF</th>
		<th>PF (<span id='PF_per'></span>%)</th>
		<th>ESI (<span id='ESI_per'></span>%)</th>
		<th>Advance</th>
		<th>Net Amount</th>
		</tr>
	</thead>
	 
		<?php foreach($staff_n as $sn) {
			$n_amount=0;
			if(!array_key_exists($sn['gibbonStaffID']+0,$structure_d))
				continue;
		?>
			<tr >
				<td style="border: 1px solid black; border-collapse: collapse;">
					<?php echo $sn['preferredName']."<br><small>".$sn['type']."</small>";?>
				</td>
				<?php
					foreach($rule as $r){
				?>
				<td style="border: 1px solid black; border-collapse: collapse; text-align:right;">
					<?php
					if (array_key_exists($r['rule_id']+0,$structure_d[$sn['gibbonStaffID']+0])){
						echo $structure_d[$sn['gibbonStaffID']+0][$r['rule_id']+0];
						if($rule_impact[$r['rule_id']+0]=='+')
							$n_amount+=$structure_d[$sn['gibbonStaffID']+0][$r['rule_id']+0]; 
						else
							$n_amount-=$structure_d[$sn['gibbonStaffID']+0][$r['rule_id']+0];
					}
					else
						echo "-";
					?>
				</td>
				<?php }
				?>
				<td style="border: 1px solid black; border-collapse: collapse; text-align:right;">
					<?php 
					echo $structure_d[$sn['gibbonStaffID']+0][98];
					?>
				</td >
				<td style="border: 1px solid black; border-collapse: collapse; text-align:right;">
					<?php 
					$pf=$structure_d[$sn['gibbonStaffID']+0][98]*$structure_d[0][97]/100;
					echo round($pf);
					$n_amount-=round($pf);
						?>
				</td >
				<td style="border: 1px solid black; border-collapse: collapse; text-align:right;">
					<?php 
					$esi=($structure_d[$sn['gibbonStaffID']+0][98] + $structure_d[$sn['gibbonStaffID']+0][12])<21000?ceil(($structure_d[$sn['gibbonStaffID']+0][98] + $structure_d[$sn['gibbonStaffID']+0][12])*$structure_d[0][96]/100):0;
	
	           
					
					
					
					echo $esi;
					$n_amount-=$esi;
						?>
				</td >
				<td style="border: 1px solid black; border-collapse: collapse; text-align:right;">
					<?php 
					echo $structure_d[$sn['gibbonStaffID']+0][99];
					$n_amount-=$structure_d[$sn['gibbonStaffID']+0][99];
						?>
				</td >
				<td style="border: 1px solid black; border-collapse: collapse; text-align:right;">
					<?php echo $n_amount; ?>
				</td>
			</tr>
		<?php } ?>
	</table>
	<input type='hidden' id='pf_per' value='<?php echo $structure_d[0][97];?>'>
	<input type='hidden' id='esi_per' value='<?php echo $structure_d[0][96];?>'>	
 <script>
	 $(document).ready(function(){
			$('#PF_per').html($('#pf_per').val());
			$('#ESI_per').html($('#esi_per').val());
			window.print();
		});
 </script>
</script>