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
	//$mode='';
	$month_f='';
	$year_f='';
	$staff_type ='';
	if(isset($_REQUEST['month'])){
		//$mode=$_REQUEST['type'];
		$month_f=$_REQUEST['month'];
		$year_f=$_REQUEST['year'];
		$staff_type = $_REQUEST['staff_type'];
    }
		$sql="SELECT * FROM `gibbonschoolyear` ORDER BY `gibbonSchoolYearID` ";
		$result2=$connection2->prepare($sql);
		$result2->execute();
		$year=$result2->fetchAll();
			$month_ar=array(3,2,1,12,11,10,9,8,7,6,5,4);
			$month_name=array('January','February','March','April','May','June','July','August','September','October','November','December');

		$sql1="SELECT * from gibbonstaff" ;
			$result1=$connection2->prepare($sql1);
			$result1->execute();
			$staff = $result1->fetchAll();
			//print_r($staff);
?>
<h3>Summary: </h3>
	<form  id="form_payment_option" action='<?php print $_SESSION[$guid]["absoluteURL"]?>/index.php'>
	<input type="hidden" name="q" value="/modules/<?php print $_SESSION[$guid]["module"] ?>/summary.php">
	<table width="80%" cellpadding="0" cellspacing="0" align='center'>
		<tr>
			<td><select name='month' required>
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
				</select></td>
			<td><select name='year' required><option value=''> Select Year </option>
					<?php foreach($year as $y){
						$s=$y['gibbonSchoolYearID']==$year_f?'selected':'';
						print "<option value='".$y['gibbonSchoolYearID']."' $s>".$y['name']."</option>";
					}?>
				</select></td>
			<td><select name='staff_type' required><option value=''> Select Group </option>
				<option value='1' <?php echo $staff_type==1?'selected':''; ?>>Sr. Section</option>
				<option value='2' <?php echo $staff_type==2?'selected':''; ?>>Jr. Section</option>
				<option value='3' <?php echo $staff_type==3?'selected':''; ?>>All</option>
				</select></td>
			<td><input  type='submit' value='Submit' name='print'></td>
			<td><input  type='button' value='Print' name='print-btn' class="print-btn"></td>
		</tr>
	</table>
	</form>
<?php

	if(isset($_REQUEST['print'])){

if($staff_type != 3)
{
$sql="SELECT *
FROM lakshyasalarypayment,lakshyasalarymaster,lakshyasalaryrule,gibbonstaff
				where lakshyasalarymaster.master_id=lakshyasalarypayment.master_id
                and lakshyasalarymaster.rule_id=lakshyasalaryrule.rule_id
                and lakshyasalarymaster.staff_id=gibbonstaff.gibbonStaffID
                and lakshyasalarymaster.month= $month_f
                and lakshyasalarymaster.year_id=$year_f
                AND gibbonstaff.sec_code = $staff_type
                order by lakshyasalarymaster.staff_id,lakshyasalaryrule.impact,lakshyasalaryrule.rule_id";
}else{
	$sql="SELECT *
FROM lakshyasalarypayment,lakshyasalarymaster,lakshyasalaryrule,gibbonstaff
				where lakshyasalarymaster.master_id=lakshyasalarypayment.master_id
                and lakshyasalarymaster.rule_id=lakshyasalaryrule.rule_id
                and lakshyasalarymaster.staff_id=gibbonstaff.gibbonStaffID
                and lakshyasalarymaster.month= $month_f
                and lakshyasalarymaster.year_id=$year_f
                order by lakshyasalarymaster.staff_id,lakshyasalaryrule.impact,lakshyasalaryrule.rule_id";
}
		$result=$connection2->prepare($sql);
		$result->execute();
		$staff_payslip_details=$result->fetchAll();
		$payslip = array();

		
		$sql5="SELECT *,lakshyasalarymaster.* FROM lakshyasalarypayment
				LEFT JOIN lakshyasalarymaster ON  lakshyasalarymaster.master_id=lakshyasalarypayment.master_id
			WHERE 1";
		$sql5.=" AND lakshyasalarymaster.month=".$month_f;
		$sql5.=" AND lakshyasalarymaster.year_id=".$year_f;
		$result5=$connection2->prepare($sql5);
		$result5->execute();
		$structure=$result5->fetchAll();		

		$sql6="SELECT * FROM `lakshyasalarymaster` WHERE `rule_id` IN (97,96)";
		$sql6.=" AND month=".$month_f;
		$sql6.=" AND year_id=".$year_f;
		$result6=$connection2->prepare($sql6);
		$result6->execute();
		$structure=$result6->fetchAll();
		$pf_arr = array();
		foreach($structure as $s){
			$pf_arr[$s['rule_id']]=$s['amount'];
		}

		foreach ($staff_payslip_details as $staffPayslip) {
			$payslip[$staffPayslip['gibbonStaffID']]['name'] =  $staffPayslip['preferredName'];
			$payslip[$staffPayslip['gibbonStaffID']][$staffPayslip['caption']] =  $staffPayslip['paid_amount'];
		}
		?>
	<?php 
		$totalpayband = 0;
		$totalconsal = 0;
		$totalgrpay = 0;
		$totalda = 0;
		$totalhra = 0;
		$totalmed = 0;
		$totalsplpay = 0;
		$total = 0;
		$totalptax = 0;
		$totalitax = 0;
		$totaladvance = 0;
		$totalesi= 0;
		$totalpf= 0;
		$totaldeduction= 0;
		$totalinhand= 0;
		foreach($payslip as $generate_payslip){
			$totalpayband += $generate_payslip['PAYBAND'];
			$totalconsal += $generate_payslip['CONSAL'];
			$totalgrpay += $generate_payslip['GRPAY'];
			$totalda += $generate_payslip['DA'];
			$totalhra += $generate_payslip['HRA'];
			$totalmed += $generate_payslip['MED'];
			$totalsplpay += $generate_payslip['SPLPAY'];

				
				$total_salary = ($generate_payslip['PAYBAND'] + $generate_payslip['CONSAL']+$generate_payslip['GRPAY']+$generate_payslip['DA']+$generate_payslip['HRA']+$generate_payslip['MED']+$generate_payslip['SPLPAY']);

			$total+= $total_salary;

			$totalptax += $generate_payslip['P TAX'];
			$totalitax += $generate_payslip['I TAX'];
			$totaladvance += $generate_payslip['ADVANCE'];

				if($total_salary <= 21000 && $generate_payslip['PF GROS']>0) 
					{ $esi = ceil(($total_salary * $pf_arr['96'])/100); }
				else{ $esi = 0;}
			$totalesi += $esi;

				$pf = round(($generate_payslip['PF GROS']*$pf_arr['97'])/100);

			$totalpf += $pf;

				$total_deduction = ($pf+$generate_payslip['P TAX']+$generate_payslip['I TAX']+$generate_payslip['ADVANCE']+$esi);
			$totaldeduction += $total_deduction;
				$total_in_hand = ($total_salary - $total_deduction);

			$totalinhand += $total_in_hand;
		?>
	<?php }	?>
	<div id="print_page">
		<table width="100%" cellpadding="2" cellspacing="0" border="0">
				  <tr>
					<td align="center" style="font-family:Arial, Helvetica, sans-serif; font-size:20px; color:#000000;">Indra Gopal High School</td>
				  </tr>				  
				  <tr>
					<td align="center" style="font-family:Arial, Helvetica, sans-serif; font-size:14; color:#000000;">Jheel Bagan, P.O. Ghuni, Hatiara, Kolkata - 700 157</td>
				  </tr>
	               <tr>
		           <td align="center" colspan=15 style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000;">Summary For  the Month: <?php echo $month_name[($month_f-1)];?> of Year: <?php echo ($y['name']);?></td>
	               </tr>				  
				   <tr>
				   	<td align="center" colspan=15 style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000;"> Group: <?php if($staff_type == 1){echo "Sr. Section";}if($staff_type == 2){echo "Jr. Section";}if($staff_type == 3){echo "All";}?></td>
				  </tr>
				  <tr>
				  </table>
	<table width='100%' cellpadding='5px' style='border: 1px solid black; border-collapse: collapse;'>
				  	<tr style='border: 1px solid black; border-collapse: collapse;'>
				  		<th>Particulars</th>
				  		<th>Amount</th>
				  	</tr>
				  	<tr style='border: 1px solid black; border-collapse: collapse;'>
				  		<th>Salary Structure (A) [Earnings]</th>
				  		<th></th>
				  	</tr>
				  	<tr style='border: 1px solid black; border-collapse: collapse;'>
				  		<td style='border: 1px solid black; border-collapse: collapse;'>PAYBAND</td>
				  		<td style='border: 1px solid black; border-collapse: collapse;'><?php echo $totalpayband;?></td>
				  	</tr>
				  	<tr style='border: 1px solid black; border-collapse: collapse;'>
				  		<td style='border: 1px solid black; border-collapse: collapse;'>CONSAL</td>
				  		<td style='border: 1px solid black; border-collapse: collapse;'><?php echo $totalconsal;?></td>
				  	</tr>
				  	<tr style='border: 1px solid black; border-collapse: collapse;'>
				  		<td style='border: 1px solid black; border-collapse: collapse;'>GRPAY</td>
				  		<td style='border: 1px solid black; border-collapse: collapse;'><?php echo $totalgrpay;?></td>
				  	</tr>
				  	<tr style='border: 1px solid black; border-collapse: collapse;'>
				  		<td style='border: 1px solid black; border-collapse: collapse;'>DA</td>
				  		<td style='border: 1px solid black; border-collapse: collapse;'><?php echo $totalda;?></td>
				  	</tr>
				  	<tr style='border: 1px solid black; border-collapse: collapse;'>
				  		<td style='border: 1px solid black; border-collapse: collapse;'>HRA</td>
				  		<td style='border: 1px solid black; border-collapse: collapse;'><?php echo $totalhra;?></td>
				  	</tr>
				  	<tr style='border: 1px solid black; border-collapse: collapse;'>
				  		<td style='border: 1px solid black; border-collapse: collapse;'>MEDICAL</td>
				  		<td style='border: 1px solid black; border-collapse: collapse;'><?php echo $totalmed;?></td>
				  	</tr>
				  	<tr style='border: 1px solid black; border-collapse: collapse;'>
				  		<td style='border: 1px solid black; border-collapse: collapse;'>SPECIAL PAY</td>
				  		<td style='border: 1px solid black; border-collapse: collapse;'><?php echo $totalsplpay;?></td>
				  	</tr>
				  	<tr style='border: 1px solid black; border-collapse: collapse;'>
				  		<td style='border: 1px solid black; border-collapse: collapse;'>Total</td>
				  		<td style='border: 1px solid black; border-collapse: collapse;'><?php echo $total;?></td>
				  	</tr>
				  </table>
				  <table width='100%' cellpadding='5px' style='border: 1px solid black; border-collapse: collapse;'>
				  	<tr style='border: 1px solid black; border-collapse: collapse;'>
				  		<th></th>
				  		<th></th>
				  	</tr>
				  	<tr style='border: 1px solid black; border-collapse: collapse;'>
				  		<th>Salary Structure (B) [Deductions]</th>
				  		<th></th>
				  	</tr>

				  	
				  	<tr style='border: 1px solid black; border-collapse: collapse;'>
				  		<td style='border: 1px solid black; border-collapse: collapse;'>P TAX</td>
				  		<td style='border: 1px solid black; border-collapse: collapse;'><?php echo $totalptax;?></td>
				  	</tr>
				  	<tr style='border: 1px solid black; border-collapse: collapse;'>
				  		<td style='border: 1px solid black; border-collapse: collapse;'>I TAX</td>
				  		<td style='border: 1px solid black; border-collapse: collapse;'><?php echo $totalitax;?></td>
				  	</tr>
				  	<tr style='border: 1px solid black; border-collapse: collapse;'>
				  		<td style='border: 1px solid black; border-collapse: collapse;'>ESI</td>
				  		<td style='border: 1px solid black; border-collapse: collapse;'>
				  			<?php echo $totalesi;?>
				  		</td>
				  	</tr>
				  	<tr style='border: 1px solid black; border-collapse: collapse;'>
				  		<td style='border: 1px solid black; border-collapse: collapse;'>PF</td>
				  		<td style='border: 1px solid black; border-collapse: collapse;'><?php echo $totalpf;?></td>
				  	</tr>
				  	<tr style='border: 1px solid black; border-collapse: collapse;'>
				  		<td style='border: 1px solid black; border-collapse: collapse;'>ADVANCE</td>
				  		<td style='border: 1px solid black; border-collapse: collapse;'><?php echo $totaladvance;?></td>
				  	</tr>
				  	<tr style='border: 1px solid black; border-collapse: collapse;'>
				  		<td style='border: 1px solid black; border-collapse: collapse;'>Total</td>
				  		<td style='border: 1px solid black; border-collapse: collapse;'><?php echo $totaldeduction?></td>
				  	</tr>
				  	<tr style='border: 1px solid black; border-collapse: collapse;'>
				  		<td style='border: 1px solid black; border-collapse: collapse;'>Total Salary (A - B)</td>
				  		<td style='border: 1px solid black; border-collapse: collapse;'><?php echo $totalinhand?></td>
				  	</tr>
				  </table>
<?php	
	}
}
?>
</div>
<script type="text/javascript">

	//  $('.print-btn').click(function() {
	//  	alert('fgfgf');
	// 	var w=window.open("","","height=600,width=700,status=yes,toolbar=no,menubar=no,location=no");
	// 	var html=$('#print_page').html();
	// 	$(w.document.body).html(html);
	// 	w.print();
	// })
	 $('.print-btn').click(function() {
	 	var w=window.open("","","height=600,width=700,status=yes,toolbar=no,menubar=no,location=no");
		var html=$('#print_page').html();
		$(w.document.body).html(html);
		w.print();
});
</script>