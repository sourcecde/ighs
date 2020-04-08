<?php 
@session_start() ;

if (isActionAccessible($guid, $connection2, "/modules/Fee/payment.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
    
$gibbonPersonID=NULL ;
if (isset($_SESSION[$guid]["gibbonPersonID"])) {
	$gibbonPersonID=$_SESSION[$guid]["gibbonPersonID"] ;
}
$sql="SELECT * FROM `gibbonschoolyear` ORDER BY `sequenceNumber`";
$result=$connection2->prepare($sql);
$result->execute();
$schoolyear=$result->fetchAll();


//echo "<pre>";print_r($_SESSION);die;


/*
$sql="SELECT gibbonstudentenrolment.*,gibbonperson.firstName,gibbonperson.surname,gibbonyeargroup.name,gibbonrollgroup.name AS section,gibbonperson.account_number FROM gibbonstudentenrolment LEFT JOIN gibbonperson ON 
gibbonstudentenrolment.gibbonPersonId=gibbonperson.gibbonPersonId 
LEFT JOIN gibbonyeargroup ON gibbonstudentenrolment.gibbonYearGroupId=gibbonyeargroup.gibbonYearGroupId
LEFT JOIN gibbonrollgroup ON gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID
ORDER BY gibbonperson.firstName ASC"; */
$sql="SELECT `gibbonPersonID`,`preferredName`,`account_number` FROM `gibbonperson` WHERE `gibbonPersonID` IN(SELECT `gibbonPersonID` FROM `gibbonstudentenrolment`) ORDER BY `preferredName`";
$result=$connection2->prepare($sql);
$result->execute();
$dboutbut=$result->fetchAll();

//get rule type masteree
$student_id=0;
$paidmontharr=array();
$schoolyeararr=array(0=>'yearly',1=>'jan',2=>'feb',3=>'mar',4=>'apr',5=>'may',6=>'jun',7=>'jul',8=>'aug',9=>'sep',10=>'oct',11=>'nov',12=>'dec');
//getting fee type
$sql='Select fee_type_master_id,fee_type_name,boarder from fee_type_master';
$result=$connection2->prepare($sql);
$result->execute();
$all_fee_type=$result->fetchAll();
// end of getting fee type
$all_fee_payable=array();
$month_arr=array();
$grand_total=0;


$fee_jsonarr=array();
$sql='Select fee_type_master_id,fee_type_name,boarder,fee_type_desc from fee_type_master';
$result=$connection2->prepare($sql);
$result->execute();
$all_fee_type=$result->fetchAll();

//getting month sequence
$month_squence_arr=array();
$sql="SELECT * from gibbonschoolyear where status='Current'";
$result=$connection2->prepare($sql);
$result->execute();
$schoolyearresult=$result->fetch();
$firstdayarr=explode("-", $schoolyearresult['firstDay']);
$firstday=(int)$firstdayarr[1];

$lastdayarr=explode("-", $schoolyearresult['lastDay']);
$lastday=(int)$lastdayarr[1];


for($i=$firstday;$i<=12;$i++)
{
	array_push($month_squence_arr, $i);
}

//for($i=1;$i<=$lastday;$i++)
//{
//	array_push($month_squence_arr, $i);
//}

//Getting bank accounts.
$sql="SELECT * FROM `payment_bankaccount` where `active`!=0";
$result=$connection2->prepare($sql);
$result->execute();
$banks=$result->fetchAll();
//Getting bank names.
$sql="SELECT * FROM `fee_bank_master`  WHERE `bankMasterID`!=0 ORDER BY `bankName`";
$result=$connection2->prepare($sql);
$result->execute();
$allBanks=$result->fetchAll();
//Getting special Fees
$sql="SELECT `fee_type_master_id`, `fee_type_name` FROM `fee_type_master` 
		WHERE `yearly`=0 AND `jan`=0 AND `feb`=0 AND `mar`=0 AND `apr`=0 AND `may`=0 AND  `jun`=0 AND `jul`=0 AND `aug`=0 AND `sep`=0 AND  `oct`=0 AND `nov`=0 AND `dec`=0 AND  `onetime`=0 ";
$result=$connection2->prepare($sql);
$result->execute();
$specialFees=$result->fetchAll();

//echo "<pre>";print_r($_SESSION);die;


?>
<form name="f1" id="f1" method="post">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td align="center" colspan="2">
    
    <select name="student_personID" id="student_personID" style="float: left;">
		<option value="" class="hide_from_parent"> - Select Student - </option>
		<?php foreach ($dboutbut as $value) { 
			$ac_no=$value['account_number']+0;
			echo "<option value='{$value['gibbonPersonID']}'>{$value['preferredName']} ( $ac_no )</option>";
		} ?>
	 </select>
		    
		  <select name="fianacialyear" id="fianacialyear">
		  <?php foreach ($schoolyear as $value) { ?>
		 
		  <option value="<?php echo $value['gibbonSchoolYearID']?>" <?php if($value['status']=='Current'){?> selected="selected" <?php } ?>><?php echo $value['name']." (".$value['status']." year)"?></option>
		  <?php } ?>
		  </select>
		     <div id="student_payment_history"></div>
		    
		     
	</td>
	<td>
	 <div style="position: absolute;right: 6px; top: 10px;" class="hide_from_parent">
		    <input type="text" name="account_number" id="account_number" style="float: left;" placeholder="Account Number">
		     <input type="button" name="go" id="go" value="Go">
		     </div>
	</td>
  </tr>
	<tr id='detail_panel' style='display: none'>
		<td colspan='3' style="border: 2px solid #7030a0;">
			<div>
				<b>Name: </b><span id='s_name' style='padding: 20px'></span> | 
				<b>Class: </b><span id='s_class' style='padding: 20px'></span> | 
				<b>Roll: </b><span id='s_roll' style='padding: 20px'></span> | 
				<b>Account No: </b><span id='s_accno' style='padding: 20px'></span><b>
			</div>
		</td>
	</tr>
  <tr>
  	<td valign="top" width="10%">
		<select id='condition' name='condition'>
					<option value='all'>All</option>
					<option value='ex_trans'>Excluding Transport</option>
					<option value='only_trans'>Only Transport</option>
		</select>
		<br><br><hr>
  		<table width="100%" cellpadding="0" cellspacing="0">
		
			<tr><td colspan="2"><b>Select Month :</b></td></tr>
  			<?php foreach ($month_squence_arr as $value) { ?>
  			<tr>
  				<td id="tdchklbl_<?php echo $schoolyeararr[$value];?>">
  					<?php echo ucwords($schoolyeararr[$value]);?>
  					
  				</td>
  				<td id="tdchk_<?php echo $schoolyeararr[$value];?>">
  				<input type="checkbox" name="selected_month[]" id="<?php echo $schoolyeararr[$value];?>" value="<?php echo $value;?>" <?php if(in_array($value, $month_arr)){?> checked="checked"<?php } ?><?php if(in_array($value, $paidmontharr)){?> disabled="disabled"<?php } ?> class="selecte_month_class">
  				</td>
  			</tr>
  			<?php } ?> 
  		</table>
  	<td valign="top" width="45%">
  	<table width="100%" cellpadding="0" cellspacing="0">
			<tr class="hide_from_parent">
				<td><select id='specialFee'>
					<option value=''>Select Special Fee</option>
					<?php 
					foreach($specialFees as $s)
						echo "<option value='{$s['fee_type_master_id']}'>{$s['fee_type_name']}</option>";
					?>
				</select></td>
				<td><input type='text' id='specialFeeAmount' placeholder='Amount' style='width:96%'></td>
			</tr>
  			<?php foreach ($all_fee_type as $value) {
				array_push($fee_jsonarr, $value['fee_type_desc']);
					
  				?>
  			<tr id="fee_row<?=$value['fee_type_desc']?>"  class="fee_input" style="display:none;">
  				<td width="50%" style="text-align: right;"><?php echo ucwords($value['fee_type_name']);?></td>
  				<td>
  					<input type="text" name="fee_type_<?php echo $value['fee_type_master_id']?>" id="<?php echo $value['fee_type_desc']?>" disabled="disabled" value="0.00" class="fee_type_value_class">
  				</td>
  			</tr>
				<?php 
				} ?> 
  			<input type="hidden" name="all_fee_type_json" id="all_fee_type_json" value="<?php echo implode(",", $fee_jsonarr);?>">
			
  			
  			<tr id="fee_row_transport" style='display:none'>
	  			<td width="50%" style="text-align: right;"><input type="checkbox" name="include_transport" id="include_transport" value="1" hidden>Transport</td>
	  			<td>
	  			
	  			<input type="text" name="transport_amount" id="transport_amount" value="0.00" disabled="disabled">
	  			</td>
  			</tr>
			<tr class='hide_from_parent'>
	  			<td width="50%" style="text-align: right;">Fine</td>
	  			<td><input type="text" name="fine_amount" id="fine_amount" value="0.00"></td>
  			</tr>
  			<tr>
	  			<td width="50%" style="text-align: right;">Total</td>
	  			<td><input type="text" name="total_amount" id="total_amount" value="0.00" disabled="disabled"></td>
  			</tr>
  		</table>
  	</td>
  	<td valign="top" width="45%">
  		<table width="100%" cellpadding="0" cellspacing="0">
  		<tr class="hide_from_parent">
  			<td width="50%" style="text-align: right;">Payment Date</td>
  			<td>
  			<input type="text" name="payment_date" id="payment_date" required>
  			</td>
  		</tr>
  		<tr class="hide_from_parent">
  			<td width="50%" style="text-align: right;">Voucher No.
  			<br>
  			<div>
  			<input type="radio" name="voucher_type" id="voucher_type1" class="voucher_type_class" value="1" checked="checked">Auto &nbsp;&nbsp;
  			
  			 <input type="radio" name="voucher_type" id="voucher_type2" class="voucher_type_class" value="2" > Custom
  			</div>
  			 
  			</td>
  			<td>
  			<input type="text" name="vouchar_no" id="vouchar_no" readonly="readonly">
  			</td>
  		</tr>
  		<tr class="hide_from_parent">
  			<td width="50%" style="text-align: right;">Mode of Payment</td>
  			<td>
  			<select name="payment_mode" id="payment_mode">
  			<option value="cash">Cash</option>
  			<option value="cheque">Cheque</option>
			<!--
			<option value="bank_transfer">Bank Transfer</option>
			<option value="net_banking">Net Banking</option>
  			<option value="dd">Draft</option>
			<option value="credit_card">Credit Card</option>
			<option value="debit_card">Debit Card</option>-->
			<option value="card">Card</option>
			<option value="online">Online</option>
  			</select>
  			</td>
  		</tr>
  		<tr class="hide_from_parent cheque draft">
  			<td width="50%" style="text-align: right;">Receiving Bank</td>
  			<td>
			<select name='bankID' id='bankID'>
				<option value='0'> Select Bank </option>
				<?php foreach($banks as $b){
					echo "<option value='{$b['bankID']}'>{$b['accountName']}</option>";
				} ?>

			</select>
  			</td>
  		</tr>
		  		<tr class="hide_from_parent cheque">
  			<td width="50%" style="text-align: right;">Cheque/Payee Bank</td>
  			<td>
			<select name='cheque_bank' id='cheque_bank'>
				<option value='0'> Select Bank </option>
				<?php foreach($allBanks as $b){
					echo "<option value='{$b['bankMasterID']}'>".substr($b['bankName'],0,25)."</option>";
				} ?>

			</select>
  			</td>
  		</tr>
  		<tr class="hide_from_parent cheque">
  			<td width="50%" style="text-align: right;">Chq/Ref No</td>
  			<td>
  			<input type="text" name="cheque_no" id="cheque_no">
  			</td>
  		</tr>
  		 <tr class="hide_from_parent online">
  			<td width="50%" style="text-align: right;">Order ID</td>
  			<td>
  			<input type="text" name="order_id" id="order_id">
  			</td>
  		</tr>
  		 <tr class="hide_from_parent online">
  			<td width="50%" style="text-align: right;">Tracking ID</td>
  			<td>
  			<input type="text" name="tracking_id" id="tracking_id">
  			</td>
  		</tr>
  		<tr class="hide_from_parent cheque">
  			<td width="50%" style="text-align: right;">Chq/Ref Date</td>
  			<td>
  			<input type="text" name="cheque_date" id="cheque_date">
  			</td>
  		</tr>
  		<tr>
  			<td colspan="2">
  			<input type="button" name="pay" id="pay" value="Pay">&nbsp;
  			<input type="button" name="print" id="print" value="Print" style="display: none;" >
  			<input type="hidden" name="payment_master_id" id="payment_master_id" value="1">
			<div class="warning" style='display:none' id="payment-due-alert">
				Fees are unpaid in previous school session. Kindly pay those first.
			</div>
  			</td>
		</tr>
  		</table>
  	</td>
  </tr>
</table>
</form>

<input type="hidden" name="studentEnrolmentID" id="studentEnrolmentID" value="">
<input type="hidden" name="show_history_url" id="show_history_url" value="<?php echo $_SESSION[$guid]["absoluteURL"] . "/modules/Fee/payment_ajax.php";?>">
<input type="hidden" name="month_fee_url" id="month_fee_url" value="<?php echo $_SESSION[$guid]["absoluteURL"] . "/modules/Fee/ajax_payment_get_monthly_fee.php";?>">
<input type="hidden" name="check_transport_url" id="check_transport_url" value="<?php echo $_SESSION[$guid]["absoluteURL"] . "/modules/Fee/ajax_check_transport.php";?>">
<input type="hidden" name="print_page_url" id="print_page_url" value="<?php echo $_SESSION[$guid]["absoluteURL"] . "/modules/Fee/print_payment.php";?>">
<input type="hidden" name="get_personID_from_accno_url" id="get_personID_from_accno_url" value="<?php echo $_SESSION[$guid]["absoluteURL"] . "/modules/Fee/ajax_get_personid_by_accno.php";?>">
<input type="hidden" name="studentDetailsUrl" id="studentDetailsUrl" value="<?php echo $_SESSION[$guid]["absoluteURL"] . "/modules/Fee/ajax_getStudentDetails.php";?>">
<input type="hidden" name="checkdefaultURL" id="checkdefaultURL" value="<?php echo $_SESSION[$guid]["absoluteURL"] . "/modules/Fee/ajax_check_default.php";?>">
<script type="text/javascript">
		$(function() {
			$( "#payment_date" ).datepicker({ dateFormat: 'dd/mm/yy',minDate:new Date(2015, 3,1,0,0,0,0) });
			$( "#cheque_date" ).datepicker({ dateFormat: 'dd/mm/yy' });
		});
</script>
<script>
$(document).ready(function(){
	$( "#payment_date" ).val('<?php echo date('d/m/Y');?>');
	$(".cheque :input").prop("disabled",true);
	$("#payment_mode").change(function(){
		if($("#payment_mode").val()=='cash'){
			$(".cheque :input").prop("disabled",true);
		}
		else{
				$(".cheque :input").prop("disabled",false);
		}
	});
	$("#payment_mode").change(function(){
		if($("#payment_mode").val()=='online'){
			$(".online").show();
		}
		else{
            $(".online").hide();
        }
	});
})
</script>
 <div id='hide_body'style='background-color :rgba(0,0,0, 0.7); width:100%; height:100%; position:fixed; left:0px; top:0px; z-index:100; display:none;'>
 </div>
 <div id='voucher_no_panel' style="position:fixed; left:500px; top:250px; z-index:200; border:1px; padding:5px 10px; background-color :rgba(0,0,0, 0.6); color:white; width:250px; display:none;">
	<table width='100%' cellpadding='10px' cellspacing='10px' class='blank' style='color:white;'>
	<tr>
		<td><b>Sucessfully Paid!!</b></td>
	</tr>
	<tr>
			<td>
			<b><span id="show_voucher_no"></span></b>
			</td>
  	</tr>	
	<tr>
		<td>
			<center>
					<input type="button" name="print_voucher" id="print_voucher" value="Print" style="border:1px; padding:5px; background:#ff731b; color:white;">
					<input type="button" name="close_voucher_no" id="close_voucher_no" value="Close" style="border:1px; padding:5px; background:#ff731b; color:white;">
			</center>
		</td>
	<tr>
	</table>
 </div>
 
<div id='alert' style="position:fixed; left:500px; top:250px; z-index:90000; border:1px; padding:5px 10px; background-color :rgba(0,0,0, 0.6); color:white; width:250px; display:none;">
	<table width='100%' cellpadding='10px' cellspacing='10px' class='blank' style='color:white;'>
	<tr>
		<td><b><span id='alert_message'></span></b></td>
	</tr>
	<tr>
		<td align='center'>
			<input type="button" name="close_alert" id="close_alert" value="Close" style="border:1px; padding:5px; background:#ff731b; color:white;">	
		</td>
	<tr>
	</table>
 </div>
 
 
 <style>
#content {
  margin: 0 0 0 -1px;
  padding: 0 0px 0 0px;
  min-height: 470px;
  width: 900px;
  max-width: 900px!important;
  position: relative;
  overflow: hidden;
  float: left;
  border-right: 1px solid rgba(0,0,0,0.15);
}
#sidebar {
  float: right;
  width:150px;
  padding: 12px 26px 40px 22px;
  margin-top: -12px;
  min-height: 200px;
}
.online{
    display:none;
}
.payment-allow {
	border: none!important; 
    background-color: #ff731b!important;
    color: #ffffff!important;
    cursor: pointer;
}

.payment-block{
	cursor: not-allowed;
}
</style>

<?php
};
?>
