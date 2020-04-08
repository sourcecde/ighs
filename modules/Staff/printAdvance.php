<?php
@session_start() ;

if (isActionAccessible($guid, $connection2, "/modules/Staff/staff_view.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
		$yearID=$_POST['yearID'];
		try{
		$sql="SELECT `lakshyasalaryadvance`.*,`gibbonperson`.`preferredName`,`gibbonschoolyear`.`name`  
				FROM `lakshyasalaryadvance` 
				LEFT JOIN `gibbonstaff` ON `lakshyasalaryadvance`.`staffID`=`gibbonstaff`.`gibbonStaffID` 
				LEFT JOIN `gibbonperson` ON `gibbonstaff`.`gibbonPersonID`=`gibbonperson`.`gibbonPersonID` 
				LEFT JOIN `gibbonschoolyear` ON `lakshyasalaryadvance`.`schoolYearID`=`gibbonschoolyear`.`gibbonSchoolYearID`
				WHERE `lakshyasalaryadvance`.`schoolYearID`=".$yearID.
				" ORDER BY `gibbonstaff`.`priority`,`date`";
		$result=$connection2->prepare($sql);
		$result->execute();
		$advanceD=$result->fetchAll();
		}
		catch(PDOException $e){
			echo $e;
		}
		try{
		$sql="SELECT `name` from `gibbonschoolyear` where `gibbonSchoolYearID`=".$yearID;
		$result=$connection2->prepare($sql);
		$result->execute();
		$year=$result->fetch();
		}
		catch(PDOException $e){
			echo $e;
		}
		$advanceData=array();
		foreach($advanceD as $a){
			$advanceData[$a['preferredName']][]=$a;
		}
		//echo "<pre>";
		//print_r($advanceData);
		//echo "</pre>";
?>
	<h1>Advance Statement (<?php echo $year['name']?>):</h1>
	<div class='print'>
	<?php 
	foreach($advanceData as $staffName=>$a){
		echo "<table width='100%'>";
		echo "<tr><th colspan=4>$staffName</th></td>";
		echo "<tr>";
		echo "<th>Date</th>";
		echo "<th style='text-align: right'>Debit</th>";
		echo "<th style='text-align: right'>Credit</th>";
		//echo "<th>Action</th>";
		echo "</tr>";
		$total=0;
		foreach($a as $d){
			$total+=$d['type']=='Cr'?$d['amount']:(0-$d['amount']);
			$style=$d['type']=='Dr'?"display:none":"";
			echo "<tr>";
			$date=dateFormatterR($d['date']);
			$amount=number_format($d['amount'],2);
			//$action="<a class='editAdvance' id='e_{$d['advanceID']}'>Edit</a> <span style='{$style}'>|</span> <a class='deleteAdvance' id='d_{$d['advanceID']}' style='{$style}'>Delete</a>";
			echo "<td>$date</td>";
			echo $d['type']=='Cr'?"<td></td><td style='text-align: right'>$amount</td>":"<td  style='text-align: right'>$amount</td><td></td>";
			//echo $d['salaryMonth']=='0'?"<td>$action</td>":"<td></td>";
			echo "</tr>";
		}
				$amount=number_format(abs($total),2);
					echo "<tr>";
						echo "<td style='text-align: right'><b>Total :</b></td>";
						echo $total<0?"<td style='text-align: right'><b>$amount</b></td>":"<td></td>";
						echo $total>=0?"<td style='text-align: right'><b>$amount</b></td>":"<td></td>";
						//echo "<td></td>";
					echo "</tr>";
				echo "</table>";
	}
		echo "</div>";
	?>
	</div>
<?php
}
function dateFormatterR($date){
	$tmp=explode("-",$date);
	return $tmp[2]."/".$tmp[1]."/".$tmp[0];
}
?>
<script>
$(document).ready(function(){
	$('#header,.minorlinkinner,#sidebar,#footer').hide();
	$('.print').show();
	window.print();
	window.close();
});
</script>