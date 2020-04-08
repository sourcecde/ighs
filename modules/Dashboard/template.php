<?php
$month=array('04'=>'April','05'=>'May','06'=>'June','07'=>'July','08'=>'August','09'=>'September','10'=>'October','11'=>'November','12'=>'December','01'=>'January','02'=>'February','03'=>'March');
$sql="SELECT `gibbonSchoolYearID`, `name`, `status` FROM `gibbonschoolyear` ";
$result=$connection2->prepare($sql);
$result->execute();
$yearDB=$result->fetchAll();


?>
	<span id='studentNo'  class='box box-small box-left'>
		<div id="studentNoContainer" style="height: 300px; width: 100%;;"></div>
	</span>
	<span id='todaysCollection'  class='box box-small box-left'>
		<div id="todaysCollectionContainer" style="height: 300px; width: 100%;"></div>
	</span>
	<span id='todaysAttendance'  class='box box-large box-left'>
		<div id="todaysAttendanceContainer" style="height: 300px; width: 100%;"></div>
	</span>
	<span id='paymentHistory'  class='box box-large box-left'>
		<div id="paymentHistoryContainer" style="height: 300px; width: 100%;"></div>
	</span>

</div>
</div>
<div   id='side_panel'>
	<table width='100%' cellpadding='5px' cellspacing='5px'>
		<tr>
			<td class='tile0'>
				
				<span style='width:40%; float:left; padding-top:10px;'>Year:</span>
				<span style='width:60%; float:right;'>
					<select id='school_year' style='width:100%;'>
						<?php
						foreach($yearDB as $y){
							$s=$y['status']=='Current'?'selected':'';
							echo "<option value='{$y['gibbonSchoolYearID']}' $s>{$y['name']}</option>";
						}
						?>
					</select>
				</span>
				<br>
				<span style='width:40%; float:left; padding-top:10px;'>Month:</span>
				<span style='width:60%; float:right;padding:0;'>
					<select id='data_month' style='width:100%;'>
						<?php
							$m=date('m');
							foreach($month as $k=>$v){
								$s=$m==$k?'selected':'';
								echo "<option value='$k' $s>$v</option>";
							}
						?>
					</select>
				</span>
				<br>
				<span style='width:40%; float:left; padding-top:10px;'>Date:</span>
				<span style='width:60%; float:right;'>
					<input type='text' id='data_day' value='<?=date('d/m/Y')?>' style='width:98%;'>
				</span>
				<br>
			</td>
			</td>
		</tr>
		<tr>
			<td class='tile tile1'><p style='float: left; font-size:20px;'>Total<br>Staff:<br><span id='staff_no' class='d'> </span></p><i class="fa fa-users fa-5x" style='float:right;'></i></td>
		</tr>
		<tr>
			<td class='tile tile2'><p style='float: right; font-size:20px;'>Fee Paid on <br><span id="fee_paid_date"></span>:<br><span id='fee_count' class='d'></span></p><i class="fa fa-inr fa-5x" style='float:left;'></i></td>
		</tr>
		<tr>
			<td class='tile tile3'><p style='float: left; font-size:20px;'>Transport<br>User:<br><span id='transport_user' class='d'></span></p><i class="fa fa-bus fa-5x" style='float:right;'></i></td>
		</tr>
		<tr>
			<td class='tile tile4'><p style='float: right; font-size:20px;'>Pending<br>Application:<br><span id='pending_application' class='d'></span></p><i class="fa fa-bell fa-5x" style='float:left;'></i></td>
		</tr>
		<tr>
			<td class='tile tile5'><p style='float: left; font-size:20px;'>Birthday<br>Today:<br><span id='birth_day'> </span></p><i class="fa fa-birthday-cake fa-5x" style='float:right;'></i></td>
		</tr>
	</table>	
</div>
<div>
<div>
<input type='hidden' id='ajax_url' value='<?php echo $_SESSION[$guid]["absoluteURL"] ;?>modules/Dashboard/ajax.php'>
<script src="<?php echo $_SESSION[$guid]["absoluteURL"] ;?>modules/Dashboard/js/canvasjs.min.js"></script>
<script src="<?php echo $_SESSION[$guid]["absoluteURL"] ;?>modules/Dashboard/js/data.js"></script>
<script src="<?php echo $_SESSION[$guid]["absoluteURL"] ;?>modules/Dashboard/js/function.js"></script>
<link rel="stylesheet" href="<?php echo $_SESSION[$guid]["absoluteURL"] ;?>modules/Dashboard/css/style.css"/>
<link rel="stylesheet" href="<?php echo $_SESSION[$guid]["absoluteURL"] ;?>modules/Dashboard/css/font-awesome.min.css"/>
<script type="text/javascript">
		$(function() {
			$( "#data_day" ).datepicker({ dateFormat: 'dd/mm/yy' });
		});
</script>