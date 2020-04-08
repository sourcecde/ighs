<?php


@session_start() ;
if (false) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
	$fromDate=date("d/m/Y");
	$toDate=date("d/m/Y"); 
	$paymentStatus="Success";
	$startTime=' 00:00:00';
	$endTime=' 23:59:59';
	if($_POST){
		$fromDate=$_POST["fromDate"];
		$toDate=$_POST["toDate"];
		$paymentStatus=$_POST["paymentStatus"];
	}
	$startDate=DateConverter($fromDate).$startTime;
	$endDate=DateConverter($toDate).$endTime;
	$statusQuery="";
	$statusArr=array("Success","Failure","Aborted");
	if(in_array($paymentStatus,$statusArr)){
		$statusQuery="`status`='$paymentStatus' AND";
	}
	else if($paymentStatus=='Blank'){
		$statusQuery="`status` IS NULL AND";
	}
?>
<form name="f1" id="f1" method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/form.php" ?>">
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr style="height:100px;">
		<td>
			<b>From Date : </b>
			<input type='text' name='fromDate' id='fromDate' required value='<?=$fromDate?>'>
		</td>
		<td>
			<b>To Date : </b>
			<input type='text' name='toDate' id='toDate' required value='<?=$toDate?>'>
		</td>
		<td>
			<b>Status :</b>
			<select name='paymentStatus' style='min-width:150px'>
				<option value='Success' <?=$paymentStatus=='Success'?"selected":""?>  >Success</option>
				<option value='Failure' <?=$paymentStatus=='Failure'?"selected":""?> >Failure</option>
				<option value='Aborted' <?=$paymentStatus=='Aborted'?"selected":""?> >Aborted</option>
				<!--<option value='Blank' 	<?=$paymentStatus=='Blank'?"selected":""?>   >Blank</option>-->
				<option value='All' 	<?=$paymentStatus=='All'?"selected":""?>     >All</option>
			</select>
		</td>
		<td>
			<input type='submit' value='Search'>
		</td>
	</tr>
	</table>
</form>
<?php
	try {
				$sqlSelect1="SELECT * FROM `lakshya_online_payment_reference` WHERE $statusQuery `Time` BETWEEN '$startDate' AND '$endDate'";
				$resultSelect1=$connection2->prepare($sqlSelect1);
				$resultSelect1->execute();
		print "<div style='overflow-x: scroll;'>";		
		print "<table cellspacing='0' style='width: 100%; class='myTable'>" ;
				print "<thead>";
				print "<tr class='head'>" ;
					print "<th>" ;
						print _("Order Id") ;
					print "</th>" ;
					print "<th>" ;
						print _("Tracking ID") ;
					print "</th>" ;
					print "<th>" ;
						print _("Bank Reference Number") ;
					print "</th>" ;
					print "<th>" ;
						print _("Status") ;
					print "</th>" ;
					print "<th>" ;
						print _("Name") ;
					print "</th>" ;
					print "<th>" ;
						print _("Acc No") ;
					print "</th>" ;
					print "<th>" ;
						print _("Time") ;
					print "</th>" ;
					print "<th>" ;
						print _("Amount") ;
					print "</th>" ;
					print "<th>" ;
						print _("Paid Amount") ;
					print "</th>" ;
					print "<th>" ;
						print _("Year") ;
					print "</th>" ;
					print "<th>" ;
						print _("Months") ;
					print "</th>" ;
					print "<th>" ;
						print _("Fine") ;
					print "</th>" ;
					print "<th>" ;
						print _("Payment Master ID") ;
					print "</th>" ;
				print "</tr>" ;
				print "</thead>";
				print "<tbody>";
			if($resultSelect1->rowCount()>0){
				while($student=$resultSelect1->fetch()) {
					$sqlSelect2="SELECT * FROM `gibbonperson` WHERE gibbonPersonID=".$student['personId']." ;";
					$resultSelect2=$connection2->prepare($sqlSelect2);
					$resultSelect2->execute();
					$st_row=$resultSelect2->fetch(); 
					$name=$st_row['officialName'];
					$accNo=$st_row['account_number']+0;
					$sqlSelect3="SELECT * FROM gibbonschoolyear WHERE gibbonSchoolYearID=".$student['yearId'].";";
					$resultSelect3=$connection2->prepare($sqlSelect3);
					$resultSelect3->execute();
					$class=$resultSelect3->fetch();
					$year=$class['name'];
				 print "<tr>";
				 print "<td>".$student['order_id']."</td>";
				 print "<td>".$student['tracking_id']."</td>";	
				 print "<td>".$student['bank_ref_number']."</td>";	
				 print "<td>".$student['status']."</td>";
				 print "<td>".$name."</td>";
				 print "<td>".$accNo."</td>";
				 print "<td>".$student['Time']."</td>";
				 print "<td>".$student['amount']."</td>";
				 print "<td>".$student['paidAmount']."</td>";
				 print "<td>".$year."</td>";
				 print "<td>".FormatMonthsName($student['months'])."</td>";
				 print "<td>".$student['fine']."</td>";
				 print "<td>".$student['payment_master_id']."</td>";
				 print "</tr>";
				}
			}
				print "</tbody>";
		print "</table>";
		print "</div>";
		
	}
	catch(PDOException $e) { 
		print "<div class='error'> ".$e-> getMessage()."</div>" ; 
	}
}

	
function DateConverter($date)
{
	if($date=='')
		return "";
	$datearr=explode("/", $date);
	$systemdate=$datearr[2].'-'.$datearr[1].'-'.$datearr[0];
	return $systemdate;
}
function FormatMonthsName($months){
	if($months=="")
		return "";
	$monthArr=explode(",",$months);
	$MonthNameArr=array_map("MapMonths",$monthArr);
	return implode(", ",$MonthNameArr); 
}
function MapMonths($month){
	$monthName=['Yearly', 'January','February','March','April','May','June','July','August','September','October','November','December'];
	if(($month<=12) && ($month>0)){
		return $monthName[$month];
	}
	return "";
}
?>
<script src="<?php echo $_SESSION[$guid]["absoluteURL"] ;?>/modules/Students/js/jquery.dataTables.min.js"></script>
 <script>
	 $(document).ready(function(){
		$( "#fromDate" ).datepicker({ dateFormat: 'dd/mm/yy' });
		$( "#toDate" ).datepicker({ dateFormat: 'dd/mm/yy' });
		$('.myTable').DataTable();
	});
 </script>