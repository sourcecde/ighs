<?php
$personData=json_decode($_REQUEST['person'],true);
$attendanceData=json_decode($_REQUEST['attendance'],true);
$header=json_decode($_REQUEST['data'],true);
$specialDay=json_decode($_REQUEST['specialdays'],true);
$closedDays=json_decode($_REQUEST['closedays'],true);
$numOfDays=$header['NODays'];
$yearNo=$header['year'];
$monthNo=$header['monthNo'];
?>
		
		<table width="100%" cellpadding="2" cellspacing="0" style="border: 1px solid black; border-collapse: collapse;">
		<caption>
			<table width="100%" cellpadding="2" cellspacing="0" border="0">
			 <tr>
				<th align="center" style="font-family:Arial, Helvetica, sans-serif; font-size:25px; color:#000000;">Calcutta Pubic School, Ormanjhi</th>
			  </tr>
			  <!--<tr>
				<td align="center" style="font-family:Arial, Helvetica, sans-serif; font-size:20px; color:#000000;">Ormanjhi, Ranchi, Jharkhand - 835219</td>
			  </tr>-->
			  <tr>
				<td align="center" style="font-family:Arial, Helvetica, sans-serif; font-size:20px; color:#000000;">Student Attendance Report of <i><?=$header['section']?></i> for Month: <?=$header['month']?> of Year: <?=$header['year']?> </td>
			  </tr>
			  <tr>
				<td align="center" style="font-size:18px; color:#000000;">Working Day: <b><?=$header['wDay']?></b></td>
			  </tr>
			</table>
		</caption>
		<thead>
		<tr style="border: 1px solid black; border-collapse: collapse;">
			<th style='border: 1px solid black; border-collapse: collapse;'>Roll</th>
			<th style='border: 1px solid black; border-collapse: collapse;'>Student<br>Name</th>
			<?php for($i=1;$i<=$numOfDays;$i++){echo "<th style='border: 1px solid black; border-collapse: collapse;'>$i</th>"; } ?>
			<th style='border: 1px solid black; border-collapse: collapse;'><small>Total<br>Prsnt</small></th>
			<th style='border: 1px solid black; border-collapse: collapse;'><small>Total<br>Absnt</small></th>
		</tr>
		</thead>
		</tbody>
<?php
		$n=0;
		$size=sizeOf($attendanceData)+3;
		$totalP=array();
		$totalA=array();
		
		foreach($personData as $pID=>$val){
			if(!array_key_exists($pID,$attendanceData))
				continue;
			print "<tr>";
				echo "<td style='border: 1px solid black; border-collapse: collapse;font-weight:bold'>$val[2]</td>";
				echo "<td style='border: 1px solid black; border-collapse: collapse; width:120px;'>$val[0]<br><span style='float:right;font-weight:italic;'>Account No: $val[1]</span></td>";
			$tP=0;
			$tA=0;
			$n++;
			for($i=1;$i<=$numOfDays;$i++){
				$iDay=$i<10?$yearNo."-".$monthNo."-0".$i:$yearNo."-".$monthNo."-".$i;
					if(in_array(date('N',strtotime($iDay)),$closedDays)){
						$D=date('l',strtotime($iDay));
						if($n==1)
							echo "<td rowspan='$size' style='border: 1px solid black; border-collapse: collapse; font-weight:bold; width:20px; vertical-align: top; padding-top:200px;'><span style='writing-mode:tb-rl;' class='test'>$D</span>";
					}
					else if(array_key_exists($i,$specialDay)){
						if($n==1)
							echo "<td rowspan='$size' style='border: 1px solid black; border-collapse: collapse; width:20px; font-weight: bold; vertical-align: top; padding-top:100px;'>{$specialDay[$i]}";
							
					}
					else{
					echo "<td style='border: 1px solid black; border-collapse: collapse;font-weight:bold'>";	
						if(array_key_exists($i,$attendanceData[$pID])){
							echo $t=substr($attendanceData[$pID][$i],0,1);
							if($t=='A'){
								$tA++;
								if(array_key_exists($i,$totalA))
									$totalA[$i]++;
								else
									$totalA[$i]=1;
							}
							else{
								$tP++;
								if(array_key_exists($i,$totalP))
									$totalP[$i]++;
								else
									$totalP[$i]=1;
							}
						}
						else
							echo "-";
					}
				echo "</td>";
			}
				echo "<td style='border: 1px solid black; border-collapse: collapse;font-weight:bold'>$tP</td>";
				echo "<td style='border: 1px solid black; border-collapse: collapse; font-weight:bold'>$tA</td>";
			print "</tr>";
		}
		
		print "<tr>";
			print "<td style='border: 1px solid black; border-collapse: collapse; font-weight:bold;' colspan='2'>Total Present</td>";
		for($i=1;$i<=$numOfDays;$i++){
			$t=array_key_exists($i,$totalP)?$totalP[$i]:'-';
			$iDay=$i<10?$yearNo."-".$monthNo."-0".$i:$yearNo."-".$monthNo."-".$i;
			if(!in_array(date('N',strtotime($iDay)),$closedDays) && !array_key_exists($i,$specialDay))
				print "<td style='border: 1px solid black; border-collapse: collapse;'><b>$t</b></td>";
		}
			print "<td style='border: 1px solid black; border-collapse: collapse;' colspan='2'></td>";
		print "</tr>";
		print "<tr>";
			print "<td style='border: 1px solid black; border-collapse: collapse;font-weight:bold;' colspan='2'>Total Absent</td>";
		for($i=1;$i<=$numOfDays;$i++){
			$t=array_key_exists($i,$totalA)?$totalA[$i]:'-';
			$iDay=$i<10?$yearNo."-".$monthNo."-0".$i:$yearNo."-".$monthNo."-".$i;
			if(!in_array(date('N',strtotime($iDay)),$closedDays) && !array_key_exists($i,$specialDay))
				print "<td style='border: 1px solid black; border-collapse: collapse;' ><b>$t</b></td>";
		}
			print "<td style='border: 1px solid black; border-collapse: collapse;' colspan='2'></td>";
		print "</tr>";
		print "<tr>";
			print "<td style='border: 1px solid black; border-collapse: collapse;font-weight:bold;' colspan='2'>Total Student</td>";
		for($i=1;$i<=$numOfDays;$i++){
			$p=array_key_exists($i,$totalP)?$totalP[$i]:0;
			$a=array_key_exists($i,$totalA)?$totalA[$i]:0;
			$t=$p+$a==0?'-':$p+$a;
			$iDay=$i<10?$yearNo."-".$monthNo."-0".$i:$yearNo."-".$monthNo."-".$i;
			if(!in_array(date('N',strtotime($iDay)),$closedDays) && !array_key_exists($i,$specialDay))
				print "<td style='border: 1px solid black; border-collapse: collapse;'><b>$t</b></td>";
		}
			print "<td style='border: 1px solid black; border-collapse: collapse;' colspan='2'></td>";
		print "</tr>";
?>
		</tbody>
		</table>
<style>
@media print {
   .test { display: table-footer-group; }
}	
</style>		
<script>
window.print();
</script>
