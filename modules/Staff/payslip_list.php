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

		$sql1="SELECT * from gibbonstaff" ;
			$result1=$connection2->prepare($sql1);
			$result1->execute();
			$staff = $result1->fetchAll();
			//print_r($staff);
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
			<td><input  type='submit' value='Print' name='print-btn'></td>
		</tr>
	</table>
	<table width="80%" cellpadding="0" cellspacing="0" align='center'>
		<tr>
			<td><input type="checkbox" id="vehicle1" name="vehicle1" 
				onclick="CheckAll('box1', this)" >
				<th>Staff Name</th>
			</td>
		</tr>
		<?php foreach($staff as $s){?>
		<tr>
			<td><input type="checkbox" id="staff_id" name="staff_id" value="<?php echo $s['gibbonStaffID'];?>" class="box1">
				<th><?php echo $s['preferredName'];?></th>
			</td>
		</tr>
		<?php }?>
	</table>
	</form>
<?php


	if(isset($_REQUEST['print-btn'])){

		$sql="SELECT 
                 lakshyasalarypayment.payment_id,
                 lakshyasalarypayment.master_id,
                 lakshyasalarypayment.paid_amount,
                 lakshyasalarymaster.staff_id,
                 lakshyasalarymaster.rule_id,
                 lakshyasalaryrule.caption,
                 gibbonstaff.preferredName,
                 gibbonstaff.jobtitle
FROM lakshyasalarypayment,lakshyasalarymaster,lakshyasalaryrule,gibbonstaff
				where lakshyasalarymaster.master_id=lakshyasalarypayment.master_id
                and lakshyasalarymaster.rule_id=lakshyasalaryrule.rule_id
                and lakshyasalarymaster.staff_id=gibbonstaff.gibbonStaffID
                and lakshyasalarymaster.month= '".$month_f."'
                and lakshyasalarymaster.year_id='".$year_f."'
                 order by lakshyasalarymaster.staff_id,lakshyasalaryrule.impact,lakshyasalaryrule.rule_id ";

		$result=$connection2->prepare($sql);
		$result->execute();
		$staff=$result->fetchAll();

		
		$sql5="SELECT *,lakshyasalarymaster.* FROM lakshyasalarypayment
				LEFT JOIN lakshyasalarymaster ON  lakshyasalarymaster.master_id=lakshyasalarypayment.master_id
			WHERE 1";
		$sql5.=" AND lakshyasalarymaster.month=".$month_f;
		$sql5.=" AND lakshyasalarymaster.year_id=".$year_f;
		$result5=$connection2->prepare($sql5);
		$result5->execute();
		$structure=$result5->fetchAll();
		$structure_d=array();
		
		foreach($structure as $s){
			$structure_d[$s['staff_id']+0][$s['rule_id']+0]=$s['paid_amount'];
		}
		$sql6="SELECT * FROM `lakshyasalarymaster` WHERE `rule_id` IN (97,96)";
		$sql6.=" AND month=".$month_f;
		$sql6.=" AND year_id=".$year_f;
		$result6=$connection2->prepare($sql6);
		$result6->execute();
		$structure=$result6->fetchAll();
		foreach($structure as $s){
			$pf_arr[$s['rule_id']+0]=$s['amount'];
		} 
		
		echo "<div id='print_page' style='display:none'>";
		?>
			<table width="100%" cellpadding="2" cellspacing="0" border="0">
				  <tr>
					<th align="center" style="padding-top:5px; font-family:Arial, Helvetica, sans-serif; font-size:25px; color:#000000;">Indra Gopal High School</th>
				  </tr>
				  <tr>
					<td align="center" style="font-family:Arial, Helvetica, sans-serif; font-size:20px; color:#000000;">Jheel Bagan, P.O. Ghuni, Hatiara, Kolkata - 700 157</td>
				  </tr>
				  <tr>
					<td align="center" style="font-family:Arial, Helvetica, sans-serif; font-size:20px; color:#000000;"> </td>
				  </tr>
	<tr>
		<td align="center" colspan=15 style="font-family:Arial, Helvetica, sans-serif; font-size:20px; color:#000000;"> Salary For  the Month: <?php echo $month_name[($month_f-1)];?> of Year: <?php echo $y['name'];?></td>
	</tr>
	<tr>
		<td align="center" colspan=15 style="font-family:Arial, Helvetica, sans-serif; font-size:20px; color:#000000;"> Group: <?php if($staff_type == 1){echo "Senior Section";}else{echo "Junior Section";}?></td>
	</tr>	
				  
				   <tr>
				  </tr>
				  <tr>
				  </table>
				  <br>
		<?php
		echo "<table width='100%' cellpadding='5px' style='border: 1px solid black; border-collapse: collapse;' id='table'>";
		echo "<thead>";
		echo "<tr style='border: 1px solid black; border-collapse: collapse;'>";
			echo "<th>Staff</th>";
			if($mode=='Cheque')
				echo "<th>Bank a/c</th>";
			echo "<th>Amount</th>";
		echo "</tr>";
		echo "</thead>";
		foreach($staff as $s){
			if (!array_key_exists($s['gibbonStaffID']+0,$structure_d))
				continue;
			$p=0;
			$n=0;
				foreach($positive_rule as $r){
					if (array_key_exists($r['rule_id']+0,$structure_d[$s['gibbonStaffID']+0])){
						 $p+=$structure_d[$s['gibbonStaffID']+0][$r['rule_id']+0];	
					}
				}
				foreach($negative_rule as $r){
					if (array_key_exists($r['rule_id']+0,$structure_d[$s['gibbonStaffID']+0])){
						 $n+=$structure_d[$s['gibbonStaffID']+0][$r['rule_id']+0];	
					}
				}
		$pf=round($structure_d[$s['gibbonStaffID']+0][98]*$pf_arr['97']/100);
				$n+=$pf;
				$advance=$structure_d[$s['gibbonStaffID']+0][99];
				$n+=$advance;
				$esi=($structure_d[$s['gibbonStaffID']+0][98])<=21000?ceil(($structure_d[$s['gibbonStaffID']+0][98]+0)*($pf_arr['96']+0)/100):0;
				$n+=$esi;
				$total=$p-$n;
						
			echo "<tr style='border: 1px solid black; border-collapse: collapse;'>";
				echo "<td style='border: 1px solid black; border-collapse: collapse;'>{$s['preferredName']}<br><small>{$s['type']}</small></td>";
				if($mode=='Cheque')
					echo "<td style='border: 1px solid black; border-collapse: collapse; text-align:right;'>{$s['bank_ac']}</td>";
				echo "<td style='border: 1px solid black; border-collapse: collapse; text-align:right;'>{$total}</td>";
			echo "</tr>";
		}
		echo "<tr style='border: 1px solid black; border-collapse: collapse;'>";
				echo "<td style='border: 1px solid black; border-collapse: collapse;'></td>";
				echo "<td style='border: 1px solid black; border-collapse: collapse; text-align:right;'>Total</td>";
				echo "<td style='border: 1px solid black; border-collapse: collapse; text-align:right;'><span id='total_value'></span></td>";
			echo "</tr>";

		echo "</table>";
		echo "</div>";
	?>
	<script>
	$(document).ready(function(){
		var w=window.open("","","height=600,width=700,status=yes,toolbar=no,menubar=no,location=no");
		var html=$('#print_page').html();
		$(w.document.body).html(html);
		w.print();
	})

	// Total Calculation of the table
	var table = document.getElementById("table");

	var sumVal = 0;

	for(var i =1; i<table.rows.length; i++)
	{
		var cellValue = table.rows[i].cells[2].innerHTML;

		// check that the value is a number before adding it to the total
		if (!isNaN(cellValue)) {
		    sumVal = sumVal + parseFloat(cellValue);
		}
	}
	//console.log(sumVal);
	document.getElementById('total_value').innerHTML = sumVal;

	function CheckAll(className, elem) {
		alert('mimi');
        var elements = document.getElementsByClassName(className);
        var l = elements.length;

        if (elem.checked) {
            for (var i = 0; i < l; i++) {
                elements[i].checked = true;
            }
        } else {
            for (var i = 0; i < l; i++) {
                elements[i].checked = false;
            }
        }
    }
	</script>
<?php	
	}
}
?>