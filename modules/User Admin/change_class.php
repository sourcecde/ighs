<?php
@session_start() ;

if (isActionAccessible($guid, $connection2, "/modules/User Admin/change_class.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {


//Module includes
//include "./modules/" . $_SESSION[$guid]["module"] . "/moduleFunctions.php" ;
if (isActionAccessible($guid, $connection2, "/modules/User Admin/rollover.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
	print "<div class='trail'>" ;
	print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . _("Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . _(getModuleName($_GET["q"])) . "</a> > </div><div class='trailEnd'>" . _('Change Boarder Type') . "</div>" ;
	print "</div>";
	
	$sql="SELECT `gibbonPersonID`,`preferredName`,`account_number` FROM `gibbonperson` WHERE `gibbonPersonID` IN (SELECT `gibbonPersonID` FROM `gibbonstudentenrolment`)";
	$result=$connection2->prepare($sql);
	$result->execute();
	$Students=$result->fetchAll();
	$currentYear=0;
	$sql="SELECT * from `gibbonschoolyear`";
	$result=$connection2->prepare($sql);
	$result->execute();
	$yearresult=$result->fetchAll();
	foreach($yearresult as $y){
		if($y['status']=='Current')
			$currentYear=$y['gibbonSchoolYearID'];
	}
	$year_id=isset($_REQUEST['schoolYearID'])?$_REQUEST['schoolYearID']:$currentYear;
?>	
	<table style='width:100%; border:2px solid #7030a0;'>
		<form method='POST' action=''>
		<tr>
			<td>Select Year: 
				<select name='schoolYearID' id='schoolYearID' required>
				<?php
					foreach($yearresult as $y){
						$s=$y['gibbonSchoolYearID']==$year_id?"selected":"";
						echo "<option value='{$y['gibbonSchoolYearID']}' $s>{$y['name']}</option>";
					}
				?>
				</select>
			</td>
			<td>	
					<input type='text'  name="account_number" id="account_number" placeholder='Account Number' style="float:left">
					<input type='button' name="search_by_acc" id="search_by_acc" value='GO'>
					Select Student: 
						<select name="student_personID" id="student_personID" required style='width:200px'>
							<option value=''>Select</option>
							<?php
								foreach($Students as $st){
									$s=isset($_REQUEST['student_personID'])?($st['gibbonPersonID']==$_REQUEST['student_personID']?"selected":""):"";
									echo "<option value='{$st['gibbonPersonID']}' $s>{$st['preferredName']} -({$st['account_number']})</option>";
								}
							?>
						</select> 
			</td>
			<td>
				<input type='submit' value='Submit'>
			</td>
		</tr>
		</form>
	</table>
<?php
	if($_POST){
		extract($_POST);
		$sql1="SELECT * FROM `gibbonstudentenrolment` WHERE `gibbonPersonID`=$student_personID AND `gibbonSchoolYearID`=$schoolYearID";
	$result1=$connection2->prepare($sql1);
	$result1->execute();
	$enrolmentData=$result1->fetch();
	//print_r($enrolmentData);
	if($result1->rowCount()>0){
	try{
	$sql="SELECT fee_payable.*,fee_rule_master.rule_name,fee_type_master.fee_type_name,fee_type_master.fee_type_master_id, gibbonschoolyear.name AS year
		 FROM fee_payable 
		 LEFT JOIN fee_rule_master ON fee_payable.rule_id=fee_rule_master.fee_rule_master_id
		 LEFT JOIN fee_type_master ON fee_type_master.fee_type_master_id=fee_rule_master.fee_type_master_id 
		 LEFT JOIN gibbonschoolyear ON gibbonschoolyear.gibbonSchoolYearID=fee_payable.gibbonSchoolYearID
		 WHERE gibbonPersonID=$student_personID AND fee_payable.gibbonSchoolYearID=$schoolYearID";
	$result=$connection2->prepare($sql);
	$result->execute();
	$payablelist=$result->fetchAll();
	}
	catch(PDOException $e){
		echo $e;
	}
	$fee_data=array();
	$paid_month=array();
	foreach($payablelist as $p){
		$m=$p['month_no']>3?($p['month_no']-3):($p['month_no']>0?$p['month_no']+12:$p['month_no']);
		$fee_data[$m][]=array($p['payment_staus'],$p['month_no'],$p['fee_type_name'],$p['amount'],$p['voucher_number']);
		if($p['payment_staus']=='paid')
			$paid_month[]=$p['month_no'];
	}
	ksort($fee_data);
	$paid_month=array_unique($paid_month);
	$schoolyeararr=array(0=>'Yearly',1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December');
	
	$sql1="SELECT `gibbonYearGroupID`,`gibbonRollGroupID` FROM `gibbonstudentenrolment` WHERE `gibbonPersonID`=$student_personID AND `gibbonSchoolYearID`=$schoolYearID";
	$result1=$connection2->prepare($sql1);
	$result1->execute();
	$class_current=$result1->fetch();
	
	$sql="SELECT * FROM `gibbonyeargroup`";
	$result=$connection2->prepare($sql);
	$result->execute();
	$classDB=$result->fetchAll();
	$sql="SELECT * FROM `gibbonrollgroup` WHERE `gibbonYearGroupID`=".$class_current['gibbonYearGroupID'];
	$result=$connection2->prepare($sql);
	$result->execute();
	$sectionDB=$result->fetchAll();
	$classDetails=array();
	foreach($classDB as $b){
		$classDetails[$b['gibbonYearGroupID']]=$b['name'];
	}
	$sql="SELECT * from payment_master WHERE `gibbonStudentEnrolmentID`=".$enrolmentData['gibbonStudentEnrolmentID'];
	$result1=$connection2->prepare($sql);
	$result1->execute();
	$paymentData=$result1->fetchAll();
	//print_r($paymentData);
	if($result1->rowCount()>0){?>
		<script>
			$(document).ready(function(){
				alert("Payment Record already exists.");
				$("#class_select").prop('disabled', true);
				$("#submit").prop('disabled', true);
			});
		</script>
<?php
	}
	else{
?>
		<script>
			$(document).ready(function(){
				$("#class_select").prop('disabled', false);\
				$("#section_select").prop('disabled', false);
				$("#submit").prop('disabled', false);
			});
		</script>
<?php	
	}
?>
	<h1>Current Fee Structure: </h1>
	<span style='float:left; width:65%; color:black'>
		<?php if($payablelist){?>
	
		<table width="100%" cellpadding="0" cellspacing="0" id="rule_table" class="myTable">
		<thead>
		  <tr>
			<th>Month</th>
			<th>Rule Type</th>
			<th>Amount</th>
			<th>Payment Status</th>
		  </tr>
		 </thead>
		<tbody> 
			<?php
				$total=0;
			  foreach ($fee_data as $f) {
				foreach($f as $value){
				if($value[3]==0)   //For hiding amount==0.
				  continue;
				$total+=$value[3];
			?>
			
		  <tr <?php echo $value[0]=='paid'?'style="color:green"':'style="color:red"';?>>
			<td><?php echo $schoolyeararr[$value[1]];?></td>
			<td><?php echo $value[2];?></td>
			<td style='text-align:right'><?php echo $value[3];?></td>
			<td><?php echo $value[0]=='paid'?ucfirst($value[0])." - ".$value[4]:ucfirst($value[0]);?></td>
		  </tr>
		  
		<?php }
		}
		?>
		<tr>
			<th></th>
			<th>Total: </th>
			<th style='text-align:right'><?=$total?>.00</th>
			<th></th>
		  </tr>
		  	
		</table>
		<?php
		} ?>
		
	</span>
	<span style=' width:30%;  color:black; padding:10px; margin:10px;'>
		<table>
		<form method="POST" action="<?php echo $_SESSION[$guid]["absoluteURL"] . "/modules/User Admin/change_class_process.php";?>">
		<input type="hidden" name='gibbonPersonID' value='<?=$student_personID?>'>
		<input type="hidden" name='gibbonSchoolYearID' value='<?=$schoolYearID?>'>
		<tr><td><b>Present Class:</td><td> <?php print_r($classDetails[$class_current['gibbonYearGroupID']])?></b></td></tr>
		<tr><td><b>Set Class:</b></td>
			<td><select name='next_class' id='class_select' required>
					<option value=''>Select</option>
					<?php
						foreach($classDetails as $k=>$v){
							if($class_current['gibbonYearGroupID']==$k)
								continue;
							echo "<option value='$k'>$v</option>";
						}
					?>
				</select></td>
		</tr>
		<tr><td><b>Set Section:</b></td>
			<td><select name='next_section' id='section_select' required>
					<option value=''>Select</option>
				</select></td>
		</tr>
		<!--<tr>
			<td><b>Effected Month:</b></td>
			<td>
				<?php 
					$month_squence_arr=array(0,4,5,6,7,8,9,10,11,12,1,2,3);
				foreach ($month_squence_arr as $value) { 
					$d=in_array($value,$paid_month)?"disabled":"checked";
				?> 
				<label>
				<input type="checkbox" class='m_box' name="selected_month[]"  value="<?php echo $value;?>" <?=$d?>>
				<b><?php echo ucwords($schoolyeararr[$value]);?>&nbsp;&nbsp;&nbsp;
				</label><br>
				<?php } ?>
			</td>
		</tr>-->
		<tr>
			<td colspan='2'><input type='submit' id='submit' value="Update"></td>
		</tr>
		</form>
		</table>	
	</span>
<?php
	}
	else{
		echo "<div class='error'>";
			echo "Selected Student isn't enroled for selected Year";
		echo '</div>';
	}
	}
}	
?>
<input type="hidden" name="get_personID_from_accno_url" id="get_personID_from_accno_url" value="<?php echo $_SESSION[$guid]["absoluteURL"] . "/modules/User Admin/ajax_get_personid_by_accno.php";?>">
<input type="hidden" id="rollGroupURL" value="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajax_change_rollgroup.php" ?>">
<?php
};
?>
<script>
$("#class_select").click(function(){
	var yearGroup=$(this).val();
	var schoolYear=$("#schoolYearID").val();
	var url = $("#rollGroupURL").val();
			$.ajax({
				type: "POST",
				url : url,
				data: {schoolYear:schoolYear, yearGroup:yearGroup},
				success: function(msg){
					console.log(msg);
					$("#section_select").empty().append("<option value=''> Select Section </option>" + msg);
				}
			});
});
</script>
