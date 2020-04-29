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
	//$staff_type ='';
	if(isset($_REQUEST['month'])){
		//$mode=$_REQUEST['type'];
		$month_f=$_REQUEST['month'];
		$year_f=$_REQUEST['year'];


		//$staff_type = $_REQUEST['staff_type'];
    }
		$sql="SELECT * FROM `gibbonschoolyear` ORDER BY `gibbonSchoolYearID` ";
		$result2=$connection2->prepare($sql);
		$result2->execute();
		$year=$result2->fetchAll();
			$month_ar=array(3,2,1,12,11,10,9,8,7,6,5,4);
			$month_name=array('January','February','March','April','May','June','July','August','September','October','November','December');
if(isset($_REQUEST['print']))
{
		$sql1="SELECT * FROM lakshyasalarypayment,lakshyasalarymaster,lakshyasalaryrule,gibbonstaff
                where lakshyasalarymaster.master_id=lakshyasalarypayment.master_id
                and lakshyasalarymaster.rule_id=lakshyasalaryrule.rule_id
                and lakshyasalarymaster.staff_id=gibbonstaff.gibbonStaffID
                and lakshyasalarymaster.month= $month_f
                and lakshyasalarymaster.year_id=$year_f
                 order by lakshyasalarymaster.staff_id,lakshyasalaryrule.impact,lakshyasalaryrule.rule_id" ;

			$result1=$connection2->prepare($sql1);
			$result1->execute();
			$staff = $result1->fetchAll();
            $newStaff = array();
			//print_r($staff);
}
?>
<h3>Payslip List: </h3>
	<form  id="form_payment_option" action='<?php print $_SESSION[$guid]["absoluteURL"]?>/index.php'>
	<input type="hidden" name="q" value="/modules/<?php print $_SESSION[$guid]["module"] ?>/payslip_list.php">
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

                <td><input  type='submit' value='Search' name='print'></td>
			<td><input  type='submit' value='Submit' name='printing' ></td>
		</tr>
	</table>
    <?php if(isset($_REQUEST['print'])){
        $count = count($staff);
        if($count>0){
        ?>
	<table width="80%" cellpadding="0" cellspacing="0" align='center'>
		<tr>
			<td><input type="checkbox" id="staffName" name="staffName" class="selectall">
				<th>Staff Name</th>
			</td>
		</tr>
		<?php foreach($staff as $s){
            $newStaff[$s['gibbonStaffID']]['name'] =  $s['preferredName'];
            $newStaff[$s['gibbonStaffID']]['id'] =  $s['gibbonStaffID'];
        }

        foreach($newStaff as $newStaffs){
            // print_r($s);
            ?>
		<tr>
			<td><input type="checkbox" id="staff_id" name="staffID[]" value="<?php echo $newStaffs['id'];?>">
				<th><?php echo $newStaffs['name'];?></th>
			</td>
		</tr>
		<?php }?>
        <?php //print_r($newStaff);?>
	</table>
<?php }else{ echo "No Records Found";}?>
<?php }?>
	</form>
<?php


	if(isset($_REQUEST['printing'])){

		if(isset($_REQUEST['staffID'])){
		$gibbonStaffID = implode(',', $_REQUEST['staffID']);
       // print_r($gibbonStaffID);
	}

$sql="SELECT *
FROM lakshyasalarypayment,lakshyasalarymaster,lakshyasalaryrule,gibbonstaff
				where lakshyasalarymaster.master_id=lakshyasalarypayment.master_id
                and lakshyasalarymaster.rule_id=lakshyasalaryrule.rule_id
                and lakshyasalarymaster.staff_id=gibbonstaff.gibbonStaffID
                and lakshyasalarymaster.month= $month_f
                and lakshyasalarymaster.year_id=$year_f
                and gibbonstaff.gibbonStaffID IN($gibbonStaffID)
                 order by lakshyasalarymaster.staff_id,lakshyasalaryrule.impact,lakshyasalaryrule.rule_id";

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
		$structure_d=array();
		
		// foreach($structure as $s){
		// 	$structure_d[$s['staff_id']+0][$s['rule_id']+0]=$s['paid_amount'];
		// }
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
		//echo $pf_arr['97'];
		//echo $pf_arr['96'];
		//print_r($pf_arr);
		//echo count($staff_payslip_details);
		foreach ($staff_payslip_details as $staffPayslip) {
			$payslip[$staffPayslip['gibbonStaffID']]['name'] =  $staffPayslip['preferredName'];
			$payslip[$staffPayslip['gibbonStaffID']][$staffPayslip['caption']] =  $staffPayslip['paid_amount'];
		}
		//print_r($payslip);
		echo "<input  type='button' value='Print' class='printdata' >";
		echo "<div id='print_page'>";
		?>
		
		<?php foreach($payslip as $generate_payslip){
				$total_salary = ($generate_payslip['PAYBAND'] + $generate_payslip['CONSAL']+$generate_payslip['GRPAY']+$generate_payslip['DA']+$generate_payslip['HRA']+$generate_payslip['MED']+$generate_payslip['SPLPAY']);

				if($total_salary <= 21000 && $generate_payslip['PF GROS']>0)
                    
					{ $esi = ceil(($total_salary * $pf_arr['96'])/100); }
				else{ $esi = 0;}

				$pf = round(($generate_payslip['PF GROS']*$pf_arr['97'])/100);

				$total_deduction = ($pf+$generate_payslip['P TAX']+$generate_payslip['I TAX']+$generate_payslip['ADVANCE']+$esi);
				$total_in_hand = ($total_salary - $total_deduction);
		?>
			<table width="100%" cellpadding="2" cellspacing="0" border="0">
				  <tr>
					<td align="center" style="font-family:Arial, Helvetica, sans-serif; font-size:20px; color:#000000;">Indra Gopal High School</td>
				  </tr>
				  <tr>
					<td align="center" style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000;">Jheel Bagan, P.O. Ghuni, Hatiara, Kolkata - 700 157</td>
				  </tr>
                  <tr>
		             <td align="center" colspan=15 style="font-family:Arial, Helvetica, sans-serif; font-size:16px; color:#000000;"> Salary For  the Month: <?php echo $month_name[($month_f-1)];?> of Year: <?php echo $y['name'];?></td>
	              </tr>

	           <tr>
         		<td align="center" colspan=15 style="font-family:Arial, Helvetica, sans-serif; font-size:16px; color:#000000;"> Name: <?php echo $generate_payslip['name'];?></td>
	           </tr>	
			
				  <tr>
				  </table>
				  <table width='100%' cellpadding='5px' style='border: 1px solid black; '>
				  	<tr style='border: 1px solid black; '>
				  		<th>Particulars</th>
				  		<th>Amount</th>
				  	</tr>
				  	<tr style='border: 1px solid black; '>
				  		<th>Salary Structure (A) [Earnings]</th>
				  		<th></th>
				  	</tr>
				  	<tr style='border: 1px solid black; '>
				  		<td style='border: 1px solid black; '>PAYBAND</td>
				  		<td style='border: 1px solid black; text-align: right;'><?php echo $generate_payslip['PAYBAND'];?></td>
				  	</tr>
				  	<tr style='border: 1px solid black; '>
				  		<td style='border: 1px solid black; '>CONSAL</td>
				  		<td style='border: 1px solid black; text-align: right;'><?php echo $generate_payslip['CONSAL'];?></td>
				  	</tr>
				  	<tr style='border: 1px solid black; '>
				  		<td style='border: 1px solid black; '>GRPAY</td>
				  		<td style='border: 1px solid black; text-align: right;'><?php echo $generate_payslip['GRPAY'];?></td>
				  	</tr>
				  	<tr style='border: 1px solid black; '>
				  		<td style='border: 1px solid black; '>DA</td>
				  		<td style='border: 1px solid black; text-align: right;'><?php echo $generate_payslip['DA'];?></td>
				  	</tr>
				  	<tr style='border: 1px solid black; '>
				  		<td style='border: 1px solid black; '>HRA</td>
				  		<td style='border: 1px solid black; text-align: right;'><?php echo $generate_payslip['HRA'];?></td>
				  	</tr>
				  	<tr style='border: 1px solid black; '>
				  		<td style='border: 1px solid black; '>MEDICAL</td>
				  		<td style='border: 1px solid black; text-align: right;'><?php echo $generate_payslip['MED'];?></td>
				  	</tr>
				  	<tr style='border: 1px solid black; '>
				  		<td style='border: 1px solid black; '>SPECIAL PAY</td>
				  		<td style='border: 1px solid black; text-align: right;'><?php echo $generate_payslip['SPLPAY'];?></td>
				  	</tr>
				  	<tr style='border: 1px solid black; '>
				  		<td style='border: 1px solid black; '>Total</td>
				  		<td style='border: 1px solid black; text-align: right;'><?php echo $total_salary;?></td>
				  	</tr>
				  </table>
				  <table width='100%' cellpadding='5px' style='border: 1px solid black; '>
				  	<tr style='border: 1px solid black; '>
				  		<th>Salary Structure (B) [Deduction]</th>
				  		<th></th>
				  	</tr>

				  	
				  	<tr style='border: 1px solid black; '>
				  		<td style='border: 1px solid black; '>P TAX</td>
				  		<td style='border: 1px solid black; text-align: right;'><?php echo $generate_payslip['P TAX'];?></td>
				  	</tr>
				  	<tr style='border: 1px solid black; '>
				  		<td style='border: 1px solid black; '>I TAX</td>
				  		<td style='border: 1px solid black; text-align: right;'><?php echo $generate_payslip['I TAX'];?></td>
				  	</tr>
				  	<tr style='border: 1px solid black; '>
				  		<td style='border: 1px solid black; '>ESI</td>
				  		<td style='border: 1px solid black; text-align: right;'><?php echo $esi;?></td>
				  	</tr>
				  	<tr style='border: 1px solid black; '>
				  		<td style='border: 1px solid black; '>PF</td>
				  		<td style='border: 1px solid black; text-align: right; '><?php echo $pf;?></td>
				  	</tr>
				  	<tr style='border: 1px solid black; '>
				  		<td style='border: 1px solid black; '>ADVANCE</td>
				  		<td style='border: 1px solid black; text-align: right;'><?php echo $generate_payslip['ADVANCE'];?></td>
				  	</tr>
				  	<tr style='border: 1px solid black; '>
				  		<td style='border: 1px solid black; '>Total</td>
				  		<td style='border: 1px solid black; text-align: right;'><?php echo $total_deduction?></td>
				  	</tr>
				  	<tr style='border: 1px solid black; '>
				  		<td style='border: 1px solid black; '>Total Salary (A - B)</td>
				  		<td style='border: 1px solid black; text-align: right;'><?php echo $total_in_hand?></td>
				  	</tr>
				  </table>
				  
				  </table>
				  <table width='100%'>
				  <tr>
				  		<td></td>
				  	    <td><img src="http://ighs.in/ighs_lakshya_sr//themes/Default/img/ki85Ed5dT.jpg" alt="Signature" width="150" height="75" align="right"></td>	
				  	</tr>
				  	<tr>
				  		<td><b>Date:<?php echo date('d/m/Y');?></b> </td>
				  		<td style="text-align:right ;font-weight: bold;">Signature<br>(Manager, Accounts)</td>
				  	</tr>
				  	</table>

				  
				  <br><br><br>
	<?php }?>
</div>
<?php	
	}
}
?>


<script type="text/javascript">

	$('.printdata').click(function() {
        //alert('aaaaa');
        var w=window.open("","","height=600,width=700,status=yes,toolbar=no,menubar=no,location=no");
        var html=$('#print_page').html();
        $(w.document.body).html(html);
        w.print();
});
     $('.selectall').click(function() {
    if ($(this).is(':checked')) {
        $('div input').attr('checked', true);
    } else {
        $('div input').attr('checked', false);
    }
});


</script>