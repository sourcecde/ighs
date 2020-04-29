<?php
@session_start() ;
include "../../config.php" ;
?>
<html>
<head>
<title>Payment History</title>
<style type="text/css">
    table { page-break-inside:auto }
    tr  {
	page-break-inside:avoid; page-break-after:auto;
	}
    thead { 
	font-size:16px;
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
	
		$sql="SELECT * FROM `lakshyasalaryrule` where active=1";
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
		$sql1="SELECT gibbonstaff.gibbonstaffID,gibbonstaff.jobTitle,lakshyasalaryattendance.payment_mode,lakshyasalaryattendance.bank_ac,gibbonperson.preferredName,gibbonperson.dateStart FROM gibbonstaff
				LEFT JOIN gibbonperson on gibbonperson.gibbonpersonID=gibbonstaff.gibbonpersonID LEFT JOIN lakshyasalaryattendance on lakshyasalaryattendance.staff_id=gibbonstaff.gibbonstaffID";
		//$sql1.=" WHERE (dateEnd IS NULL  OR dateEnd>='" . date("Y-m-d") . "')";			
				if($staff_id!='')
		$sql1.=" WHERE gibbonstaffID=".$staff_id;
		$sql1.=" AND lakshyasalaryattendance.month=".($m_y[0]+0);
		$sql1.=" AND lakshyasalaryattendance.year_id=".($m_y[1]+0);	
		//$sql1.=" AND gibbonstaffID=".$staff_id;		
		$sql1.=" ORDER BY gibbonstaff.priority";		
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		$staff_n=$result1->fetchAll();
		
		$sql2="SELECT * FROM `gibbonschoolyear` WHERE `gibbonschoolyearID`=".$m_y[1];
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
			if($s['rule_id']+0==1){
			$structure_d[$s['staff_id']+0]["basic"]=$s['amount'];
			}
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
		
		//echo print_r($pf_arr);die;
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
 <table width="100%" cellpadding="2" cellspacing="0" border="0">
  <tr>
    <th align="center" style="font-family:Arial, Helvetica, sans-serif; font-size:25px; color:#000000;"><?php echo $organizationname;?>, Ormanjhi</th>
  </tr>
  <tr>
    <td align="center" style="font-family:Arial, Helvetica, sans-serif; font-size:20px; color:#000000;">Ormanjhi, Ranchi, Jharkhand - 835219</td>
  </tr>
  <tr>
    <td align="center" style="font-family:Arial, Helvetica, sans-serif; font-size:20px; color:#000000;"> Salary For  the Month: <?php echo $month_name[($m_y[0]-1)];?> of Year: <?php echo $year['name'];?></td>
  </tr>
</table> 

 <br>
 <table width="100%" cellpadding="5px" cellspacing="0" style="border: 1px solid black; border-collapse: collapse;">
	<thead>
	  <tr style="border: 1px solid black; border-collapse: collapse;">
	    <th rowspan='2' style="border: 1px solid black; border-collapse: collapse; width:200px;">Sl No</th>
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
				$sl=1;
		foreach($staff_n as $sn) {
			$n_amount=0;
			$total_ern=0;
			$total_ded=0;
			if(!array_key_exists($sn['gibbonstaffID']+0,$structure_d))
				continue;
		?>
			<tr>
			<td><?php echo $sl++; ?></td>
				<td style="border: 1px solid black; border-collapse: collapse;">
					<?php echo $sn['preferredName']."<br><small>(".$sn['jobTitle'].")</small><br>";?>
					<?php echo "<small>DOJ: ".date('d/m/y',strtotime($sn['dateStart']))."</small>"; ?>
				</td>
				<td style='border: 1px solid black; border-collapse: collapse; text-align:right;'>
					<span class='gross' id='g<?php echo $sn['gibbonstaffID'];?>'><?php echo (int)$structure_d[$sn['gibbonstaffID']+0]["basic"];?></span>
				</td>
				<td style="border: 1px solid black; border-collapse: collapse; text-align:right;">
					<?php echo $attendance[$sn['gibbonstaffID']+0];?>
				</td>
				<?php 
					$size_p=sizeOf($positive_rule);
					$size_n=sizeOf($negative_rule);
					$gap_col=abs($size_p-$size_n);
					if($size_p>$size_n){
						$i=0;
						$j=0;
				foreach($negative_rule as $r){
					if (array_key_exists($r['rule_id']+0,$structure_d[$sn['gibbonstaffID']+0])){
						$sub_rule[$r['rule_id']]+=$structure_d[$sn['gibbonstaffID']+0][$r['rule_id']+0];
					}
				}
						foreach($positive_rule as $r){
				?>
				<td style="position: relative; width:200px;   padding:0; margin:0; border: 1px solid black; border-collapse: collapse;">
					<span style='position:absolute; top:0; left:0; padding:5px;'>
					<?php
					if (array_key_exists($r['rule_id']+0,$structure_d[$sn['gibbonstaffID']+0])){
						echo $a=$structure_d[$sn['gibbonstaffID']+0][$r['rule_id']+0];
						$n_amount+=$a;
						$sub_rule[$r['rule_id']]+=$a;
						$total_ern+=$a;
					}
					else
						echo "-";
					?>
					</span><hr style="height:0; border-top:1px solid;">
					<span style='position:absolute; bottom:0; right:0; padding:5px;'>
					<?php
					if(--$gap_col<2 && $i<sizeOf($negative_rule)){
						if (array_key_exists($negative_rule[$i]['rule_id'],$structure_d[$sn['gibbonstaffID']+0])){
							echo $a=$structure_d[$sn['gibbonstaffID']+0][$negative_rule[$i]['rule_id']+0];
							$n_amount-=$a;
							//$sub_rule[$r['rule_id']]-=$a;
							$total_ded+=$a;
							$i++;
						}
						else
							echo "-";
					}
					else if($i>=sizeOf($negative_rule))
					{
						if($j==0){
						$PF=round($structure_d[$sn['gibbonstaffID']+0][98]*$pf['97']/100);						
						if($structure_d[$sn['gibbonstaffID']+0][98]<15000){
						   $PF=0;
						}
						echo $PF; 
						$n_amount-=$PF;
						$sub_pf+=$PF;
						$total_ded+=$PF;
						$j++;
						}
						else{
						//$ESI=$structure_d[$sn['gibbonstaffID']+0][98]<21000?ceil($structure_d[$sn['gibbonstaffID']+0][98]*$pf['96']/100):0;
						if($attendance[$sn['gibbonstaffID']+0]>0 && $m_y[1]+0!=22)
						$ESI=($structure_d[$sn['gibbonstaffID']+0]["basic"]+0)<=21000 && (($structure_d[$sn['gibbonstaffID']+0][98]+0)/$attendance[$sn['gibbonstaffID']+0]>137 || (($m_y[0]+0>8 && $m_y[0]+0<3 && $m_y[1]+0==23) || ($m_y[0]+0<6 && $m_y[0]+0>3 && $m_y[1]+0==25)))?ceil(($structure_d[$sn['gibbonstaffID']+0][98]+0)*($pf[96]+0)/100):0;
						else
						$ESI=0;

//-----------------------ESI Correction Done By Shiva
                        if (($m_y[1]+0==26 && ($m_y[0]+0==11 || $m_y[0]+0==12 || $m_y[0]+0==1 || $m_y[0]+0==2 || $m_y[0]+0==3))|| $m_y[1]+0>26) {
      					   $ESI=($structure_d[$sn['gibbonstaffID']+0]["basic"]+0)<=21000 && (($structure_d[$sn['gibbonstaffID']+0][98]+0)/$attendance[$sn['gibbonstaffID']+0]>176)?ceil(($structure_d[$sn['gibbonstaffID']+0][98]+0)*($pf[96]+0)/100):0;
					    }						
						else{
     					   $ESI=($structure_d[$sn['gibbonstaffID']+0]["basic"]+0)<=21000 && (($structure_d[$sn['gibbonstaffID']+0][98]+0)/$attendance[$sn['gibbonstaffID']+0]>137 || (($m_y[0]+0>8 && $m_y[0]+0<3 && $m_y[1]+0==23) || ($m_y[0]+0<6 && $m_y[0]+0>3 && $m_y[1]+0==25)))?ceil(($structure_d[$sn['gibbonstaffID']+0][98]+0)*($pf[96]+0)/100):0;
     					}
//---------------------------------------------------------------------------------------------------------------------------------//												
						echo $ESI;
						$n_amount-=$ESI;
						$sub_esi+=$ESI;
						$total_ded+=$ESI;
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
						echo $gross_pf=$structure_d[$sn['gibbonstaffID']+0][98];
						$sub_grossPF+=$gross_pf;
					?>
					</span><hr style="height:0; border-top:1px solid;">
					<span style='position:absolute; bottom:0; right:0; padding:5px;'>	
					<?php
						echo $adv=$structure_d[$sn['gibbonstaffID']+0][99];
						$n_amount-=$adv;
						$sub_deduction+=$adv;
					?>
					</span>
				</td>
				<td style="position: relative; padding:0; margin:0; border: 1px solid black; border-collapse: collapse; text-align:left;" >
					<span style='position:absolute; top:0; left:0; padding:5px;' id='tg<?php echo $sn['gibbonstaffID'];?>'>
					<?php
						echo $total_ern;
						$sub_ern+=$total_ern;
					?>
					</span><hr style="height:0; border-top:1px solid;">
					<span style='position:absolute; bottom:0; right:0; padding:5px;'>	
					<?php
						echo $total_ded;
						$sub_ded+=$total_ded;
					?>
					</span>
				</td>
				<td style=" border: 1px solid black; border-collapse: collapse; text-align:right; font-weight:bold">
					<?php
					echo $n_amount;
					$sub_n_amount+=$n_amount;
					?>
				</td>
				<td style="position: relative; border: 1px solid black; border-collapse: collapse; text-align:right;">
					<span style='position:absolute; bottom:0; right:0; padding:5px;'>
							<small><?php echo $sn['payment_mode']=='Cheque'?'Bank a/c: '.$sn['bank_ac']:$sn['payment_mode']; ?></small>
					</span>
				</td>
			
		<?php } ?>
		<tr >
			<td style='border: 1px solid black; border-collapse: collapse; text-align:right;' colspan='4' rowspan='2'>Sub Total:</td>
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
							while($col_gap-->2)
								echo "<td style='border: 1px solid black; border-collapse: collapse;'></td>";
						}
					foreach($negative_rule as $r){
						print "<td style='border: 1px solid black; border-collapse: collapse; text-align:right;'>{$sub_rule[$r['rule_id']]}</td>";
					}
			?>		
			<td style='border: 1px solid black; border-collapse: collapse; text-align:right;'><?php echo $sub_pf; ?></td>
			<td style='border: 1px solid black; border-collapse: collapse; text-align:right;'><?php echo $sub_esi; ?></td>
			<td style='border: 1px solid black; border-collapse: collapse; text-align:right;'><?php echo $sub_deduction; ?></td>
			<td style='border: 1px solid black; border-collapse: collapse; text-align:right;'><?php echo $sub_ded+$sub_deduction; ?></td>			<td></td>
		</tr>
		</tbody>
	</table>
	
	<script> 
	$(document).ready(function(){
	/*$('.gross').each(function(){
			var id=$(this).attr('id');
			$(this).text($('#t'+id).text());
		})*/
	window.print();
	})
</script>
</body>
</html>
