<?php 
@session_start() ;

//if (isActionAccessible($guid, $connection2, "/modules/Fee/cheque_return_register.php")==FALSE) {
if (FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
    
$search=NULL;
$chequenumber='';
$accountno='';
$startdate='';
$enddate='';
$classid='';
$yearid='';
$total_amount=0;
try{
	$sql="SELECT * FROM `gibbonyeargroup`";
	$result=$connection2->prepare($sql);
	$result->execute();
	$classDb =$result->fetchAll();
	
	$sql="SELECT * FROM `gibbonschoolyear`";
	$result=$connection2->prepare($sql);
	$result->execute();
	$yearDb =$result->fetchAll();
}
catch(PDOException $e) { 
	print "<div class='error'>" . $e->getMessage() . "</div>" ; 
}
if($_POST)
{
	$chequenumber=$_POST['src_cheque_no'];
	$accountno=$_POST['src_account_no'];
	$startdate=$_POST['src_from_date'];
	$enddate=$_POST['src_to_date'];
	$classid=$_POST['src_classid'];
	$yearid=$_POST['src_yearid'];
	try{
		$sql="SELECT c.cheque_no,
		   c.cheque_date,
		   c.amount,
		   b.bankname,
		   p.voucher_number,
		   p.payment_date,	   
		   c.reason,
		   pr.account_number,
		   pr.preferredName,
		   y.name as class,
		   r.name as section
		 FROM `cheque_master` c
		 LEFT JOIN fee_bank_master b on c.bankmasterid=b.bankmasterid
		 LEFT JOIN payment_master p on c.payment_master_id=p.payment_master_id
		 LEFT JOIN gibbonstudentenrolment e ON (p.gibbonPersonID=e.gibbonPersonID AND p.gibbonSchoolYearID=e.gibbonSchoolYearID)
		 LEFT JOIN gibbonperson pr ON p.gibbonPersonID=pr.gibbonPersonID
		 LEFT JOIN gibbonyeargroup y ON e.gibbonYearGroupID=y.gibbonYearGroupID
		 LEFT JOIN gibbonrollgroup r ON e.gibbonRollGroupID=r.gibbonRollGroupID  
		 WHERE c.cheque_status_id=0";
		if($chequenumber!==''){
			$sql.=" AND c.cheque_no=$chequenumber";
		}
		if($accountno!==''){
			$sql.=" AND pr.account_number=$accountno";
		}
		if($startdate!==''){
			$sDate=getDBDate($startdate);
			$sql.=" AND p.payment_date>='$sDate'";
		}
		if($enddate!==''){
			$eDate=getDBDate($enddate);
			$sql.=" AND p.payment_date<='$eDate'";
		}
		if($classid!==''){
			$sql.=" AND e.gibbonYearGroupID=$classid";
		}
		if($yearid!==''){
			$sql.=" AND p.gibbonSchoolYearID=$yearid";
		}						
		$sql.=" order by p.payment_date DESC";
		$result=$connection2->prepare($sql);
		$result->execute($data);
	}
	catch(PDOException $e) { 
		print "<div class='error'>" . $e->getMessage() . "</div>" ; 
	}
}
?>
<form name="f1" id="f1" method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/cheque_return_register.php" ?>">
<table width="100%" cellpadding="0" cellspacing="0" border="0" class="search_table">
  <tr>
	<td>
		<input type="text" name="src_cheque_no" id="src_cheque_no" style="width: 120px; float:left;" value="<?php echo $chequenumber;?>" placeholder="Cheque Number">
	</td>
   <td>
		<input type="text" name="src_from_date" id="src_from_date" style="width: 80px; float:left;" value="<?php echo $startdate;?>" placeholder=" From..">
  	</td>
	<td>
		<input type="text" name="src_to_date" id="src_to_date" style="width: 80px; float:right;" value="<?php echo $enddate;?>" placeholder=" To.."]>
    </td>
	<td>
		<input type="text" name="src_account_no" id="src_account_no" style="width: 120px; float:left;" value="<?php echo $accountno;?>" placeholder="Account Number">
	</td>
	<td>
		<select name="src_classid" id="src_classid">
			<option value=''> Select Class</option>
		<?php foreach($classDb as $c){
			$s=$classid==$c['gibbonYearGroupID']?'selected':'';
			echo "<option value='{$c[gibbonYearGroupID]}' $s>{$c['name']}</option>";
		}?>
		</select>
	</td>
	<td>
		<select name="src_yearid" id="src_yearid">
			<option value=''> Select Year</option>
		<?php foreach($yearDb as $y){
			$s=$yearid==$y['gibbonSchoolYearID']?'selected':'';
			echo "<option value='{$y[gibbonSchoolYearID]}' $s>{$y['name']}</option>";
		}?>
		</select>
	</td>
 	 <td>
		<input type="submit"  value="Search" style="float: left; ">
		<?php if($_POST){?>
		<a type="button" id="print_cheque_return" style="color: #ffffff; background-color: seagreen; padding:1px 10px; margin: 10px; pointer:cursor;">Print</a>
		<?php } ?>
	</td>
  </tr>
</table>
</form>
<div id="printArea">
<?php 
if($_POST){
	if ($result->rowcount()<1) {
		print "<div class='error'>" ;
		print _("There are no records to display.") ;
		print "</div>" ;
	}
	else 
	{
		print "<table cellspacing='0' style='width: 100%'>" ;
			print "<thead>";
				print "<tr>";
					print "<td colspan='10' class='border-bottom'>";
					print "<p style='text-align:center; font-weight:bold; font-size:14px; margin: 2px;'>INDRA GOPAL HIGH SCHOOL (Sr)</p>";
					print "<p style='text-align:center;  font-size:12px;margin: 2px;'>Jheel Bagan ,Hatiara, Kolkata-700 157</p>";
					$filterString="";
					if($startdate!==''){
						$filterString.= " from ".$startdate;
					}
					if($enddate!==''){
						$filterString.= " to ".$enddate;
					}
					print "<p style='text-align:center;  font-size:12px;margin: 2px;'>Cheque return register".$filterString."</p>";
					print "</td>";
				print "</tr>";
				print "<tr class='head'>" ;
					print "<th>" ;
						print _("Sl.No.") ;
					print "</th>" ;
					print "<th>" ;
						print _("Cheque No.") ;
					print "</th>" ;
					print "<th>" ;
						print _("Bank") ;
					print "</th>" ;	
					print "<th>" ;
						print _("Cheque Date") ;
					print "</th>" ;
					print "<th>" ;
						print _("Payment Date") ;
					print "</th>" ;
					print "<th>" ;
						print _("Amount") ;
					print "</th>" ;	
					print "<th>" ;
						print _("Reason") ;
					print "</th>" ;	
					print "<th>" ;
						print _(" Name") ;
					print "</th>" ;	
					print "<th>" ;
						print _("Account No") ;
					print "</th>" ;	
					print "<th>" ;
						print _("Class") ;
					print "</th>" ;	
				print "</tr>" ;
			print "</thead>" ;
		print "<tbody>" ;
		
		$slNo=0;			
		$count=0;
		$rowNum="odd" ;
		while ($row=$result->fetch()) {
			if ($count%2==0) {
				$rowNum="even" ;
			}
			else {
				$rowNum="odd" ;
			}
			$total_amount+=$row["amount"];
			$slNo++;
			//COLOR ROW BY STATUS!
			print "<tr class=$rowNum>" ;
				print "<td>" ;
					print $slNo ;
				print "</td>" ;
				print "<td>" ;
					print $row["cheque_no"] ;
				print "</td>" ;
				print "<td>" ;
					print $row["bankname"] ;
				print "</td>" ;
				print "<td>" ;
					print getDisplayDate($row["cheque_date"]) ;
				print "</td>" ;
				print "<td>" ;
					print getDisplayDate($row["payment_date"]) ;
				print "</td>" ;
				print "<td style='text-align:right;'>" ;
					printf("%.2f", $row["amount"]);
				print "</td>" ;	
				print "<td>" ;
					print $row["reason"];
				print "</td>" ;
				print "<td>" ;
					print $row["preferredName"] ;
				print "</td>" ;
				print "<td>" ;
					print $row["account_number"] ;
				print "</td>" ;
				print "<td>" ;
					print $row["class"]." - ".$row["section"] ;
				print "</td>" ;
			print "</tr>" ;
		}
			print "<tr class=$rowNum>" ;
				print "<td colspan='5' style='text-align:right;'><b>Total :</b></td>";
				print "<td style='text-align:right;'><b>" ;
					printf("%.2f", $total_amount) ;
				print "</b><td colspan='4'></td>";						
			print "</tr>" ;
		print "</tbody>" ;
		print "</table>" ;
	}
}		
?>
</div>
<script type="text/javascript">
	$(function() {
		$( "#src_from_date" ).datepicker({ dateFormat: 'dd/mm/yy' });
		$( "#src_to_date" ).datepicker({ dateFormat: 'dd/mm/yy' });
	});
	$('#print_cheque_return').click(function(){
			var winPrint = window.open('', '', 'left=0,top=0,width=800,height=600,toolbar=0,scrollbars=0,status=0');
			winPrint.document.write($('#printArea').html());
			winPrint.document.write("<style>table { border-collapse: collapse;} table, th, td {border: 1px solid black; padding: 5px;}</style>");
			winPrint.document.close();
			winPrint.focus();
			winPrint.print();
	});
</script>
<?php
};

function getDisplayDate($date){
	$datearr=explode("-", $date);
	return $datearr[2]."/".$datearr[1]."/".$datearr[0];
}
function getDBDate($date){
	$datearr=explode("/", $date);
	return $datearr[2]."-".$datearr[1]."-".$datearr[0];
}
?>