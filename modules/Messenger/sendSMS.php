<?php
@session_start() ;
if (isActionAccessible($guid, $connection2, "/modules/Messenger/sendSMS.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else{
	$balance[1]="Unable to use Internet";
	$smsUsername=getSettingByScope( $connection2, "Messenger", "smsUsername" ) ;
	$smsPassword=getSettingByScope( $connection2, "Messenger", "smsPassword" ) ;
	$balanceURL="http://api.textlocal.in/balance/?username=$smsUsername&hash=$smsPassword"; 
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,  $balanceURL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	//curl_setopt($ch, CURLOPT_POST, 1);
	$Bresult = curl_exec($ch);
	curl_close($ch);
	$balanceArr=json_decode($Bresult,true);
		//print_r($balanceArr);
    if($balanceArr['status']=='success'){
        $balanceStatus=$balanceArr['balance']['sms'];
    }
    else{
        foreach($balanceArr['errors'] as $b){
            $balanceStatus=$b['message'];
        }
    }
?>
<center style="padding-left: 363px"><h5><?php if(isset($_GET["message"])){ echo $_GET["message"]; } ?></h5></center>
<h3>Send SMS:</h3>
<div style='float: left'><b>Remaining Balance: <i style='color: red'><?=$balanceStatus?></i></b></div><br>
<input type='hidden' id='contact_url' value="<?=$_SESSION[$guid]["absoluteURL"] . "/modules/Messenger/processSendSMS.php" ;?>">
<div style='width=60%; float:left'>
<table>
<tr class='selector_head' id='rollwise'>
<td><b style='color:#e05f0d;'>Send Role Wise:<b></td>
</tr>
<tr  class='selector_body hidden_panel'>
<td>
	<table style='width:100%'>
		<tr>
			<td>Select Role:</td>
			<td>
				<select name="roles[]" id="roles" multiple  style="width: 302px; height: 100px">
								<?php
								try {
									$sqlSelect="SELECT * FROM gibbonrole ORDER BY name" ;
									$resultSelect=$connection2->prepare($sqlSelect);
									$resultSelect->execute();
								}
								catch(PDOException $e) { }
								while ($rowSelect=$resultSelect->fetch()) {
									print "<option value='" .($rowSelect["gibbonRoleID"]+0). "'>" . htmlPrep(_($rowSelect["name"])) . " (" . htmlPrep(_($rowSelect["category"])) . ")</option>" ;
								}
								?>
				</select>
			</td>
		</tr>
	</table>
</td>
</tr>
<tr class='selector_head' id='groupwise'>
<td><b style='color:#e05f0d;'>Send Group Wise:<b></td>
</tr>
<tr class='selector_body hidden_panel'>
	<td>
	<table style='width:100%'>
		<tr>
			<td>Year:</td>
			<td>
				<select name='filter_year' id='filter_year'>
					<option value=''>Select</option>
					<?php
					$sql="SELECT `gibbonSchoolYearID`, `name`, `status` FROM `gibbonschoolyear` ";
					$result=$connection2->prepare($sql);
					$result->execute();
					$yearDB=$result->fetchAll();
						foreach($yearDB as $y){
							echo "<option value='{$y['gibbonSchoolYearID']}' >{$y['name']}</option>";
						}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Section:</td>
			<td>
				<select name="rollGroups[]" id="rollGroups" multiple class='multi_selector' style="width: 302px; height: 100px">
					<option value=''>Loading....</option>
				</select>
			</td>
		</tr>
	</table>	
</td>
</tr>
<tr class='selector_head' id='transportuser'>
<td><b style='color:#e05f0d;'>Transport:<b></td>
</tr>
<tr class='selector_body hidden_panel'>
	<td>
	<table style='width:100%'>
		<tr>
			<td>Select Location:</td>
			<td>
				<select name="transports[]" id="transports" multiple  style="width: 302px; height: 100px">
					<option value='all'>All</option>
								<?php
								try {
									$sqlSelect="SELECT * FROM `transport_spot_price` ORDER BY `spot_name`" ;
									$resultSelect=$connection2->prepare($sqlSelect);
									$resultSelect->execute();
								}
								catch(PDOException $e) { }
								while ($rowSelect=$resultSelect->fetch()) {
									print "<option value='" .($rowSelect["transport_spot_price_id"]+0). "'>" . htmlPrep(_($rowSelect["spot_name"])) . "</option>" ;
								}
								?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Select Vehicle:</td>
			<td>
				<select name="vehicles[]" id="vehicles" multiple  style="width: 302px; height: 100px">
								<?php
								try {
									$sqlSelect="SELECT `vehicle_id`,`details` FROM `vehicles`" ;
									$resultSelect=$connection2->prepare($sqlSelect);
									$resultSelect->execute();
								}
								catch(PDOException $e) { }
								while ($rowSelect=$resultSelect->fetch()) {
									print "<option value='" .($rowSelect["vehicle_id"]+0). "'>" . htmlPrep(_($rowSelect["details"])) . "</option>" ;
								}
								?>
				</select>
			</td>
		</tr>
	</table>
	</td>
</tr>
<tr class='selector_head'>
<td><b style='color:#e05f0d;'>Send Defaulters:<b></td>
</tr>
<tr  class='selector_body hidden_panel'>
<td>
	<table style='width:100%'>
		<tr>
			<td>Select Student:</td>
			<td>
				<select name='defaulter[]' multiple id='defaulter' style="width: 302px; height: 100px">
					<?php
					$sql="SELECT gibbonstudentenrolment.*,gibbonperson.officialName,gibbonrollgroup.name AS section,gibbonperson.account_number
							FROM gibbonstudentenrolment 
							LEFT JOIN gibbonperson ON gibbonstudentenrolment.gibbonPersonId=gibbonperson.gibbonPersonId 
							LEFT JOIN gibbonyeargroup ON gibbonstudentenrolment.gibbonYearGroupId=gibbonyeargroup.gibbonYearGroupId 
							LEFT JOIN gibbonrollgroup ON gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID 
							WHERE gibbonperson.gibbonPersonID IN (Select export_to_messenger.gibbonPersonID FROM export_to_messenger WHERE 1)
							AND `gibbonstudentenrolment`.`gibbonSchoolYearID`={$_SESSION[$guid]["gibbonSchoolYearIDCurrent"]}
							ORDER BY gibbonperson.account_number";
					$result=$connection2->prepare($sql);
					$result->execute();
					$dboutbut=$result->fetchAll();
					foreach ($dboutbut as $value) { ?>
						<option value="<?php echo $value['gibbonStudentEnrolmentID']+0?>"> <?php echo $value['officialName'];?> - <?php echo $value['section'];?>(<?php echo substr($value['account_number'], 5);?>)</option>
					<?php } ?>
				</select>
			</td>
		</tr>
	</table>
</td>
</tr>
<tr class='selector_head'>
<td><b style='color:#e05f0d;'>Send New Students:<b></td>
</tr>
<tr  class='selector_body hidden_panel'>
<td>
	<table style='width:100%'>
		<tr>
			<td>Select Student:</td>
			<td>&nbsp&nbsp&nbsp&nbsp<input type="radio" name="year" id="upcoming" class="year_radio" checked>&nbsp&nbspUpcoming Year&nbsp&nbsp&nbsp&nbsp
					<input type="radio" name="year" id="current" class="year_radio">&nbsp&nbspCurrent Year<br>
				<select name='new_admission[]' multiple id='new_admission' style="width: 302px; height: 100px; font-size: 12px">
					<option value=''>Select Student</option>
					<?php
					$sql="SELECT `gibbonstudentenrolment`.`gibbonStudentEnrolmentID`,`gibbonperson`.`officialName`,`gibbonperson`.`account_number`,`gibbonperson`.`enrollment_date`
					FROM `gibbonstudentenrolment`
					LEFT JOIN `gibbonschoolyear` ON `gibbonschoolyear`.`gibbonSchoolYearID`=`gibbonstudentenrolment`.`gibbonSchoolYearID`
					LEFT JOIN `gibbonperson` ON `gibbonperson`.`gibbonPersonID`=`gibbonstudentenrolment`.`gibbonPersonID`
					WHERE `gibbonschoolyear`.`status`='Upcoming'
					AND `gibbonstudentenrolment`.`gibbonPersonID` NOT IN (SELECT `gibbonstudentenrolment`.`gibbonPersonID` FROM `gibbonstudentenrolment` 
																			LEFT JOIN `gibbonschoolyear` ON `gibbonschoolyear`.`gibbonSchoolYearID`=`gibbonstudentenrolment`.`gibbonSchoolYearID`
																			WHERE `gibbonschoolyear`.`status`='Current')
					ORDER BY `gibbonperson`.`account_number`";
					$result=$connection2->prepare($sql);
					$result->execute();
					$dboutbut=$result->fetchAll();
					foreach ($dboutbut as $value) { ?>
						<option value="<?php echo $value['gibbonStudentEnrolmentID']+0?>"><?php echo substr($value['account_number'], 5);?> - <?php echo $value['officialName'];?> (<?php echo $value['enrollment_date'];?>)</span></option>
					<?php } ?>
				</select>
			</td>
		</tr>
	</table>
</td>
</tr>
<tr class='selector_head'>
<td><b style='color:#e05f0d;'>Send Individual:<b></td>
</tr>
<tr  class='selector_body hidden_panel'>
<td>
	<table style='width:100%'>
		<tr>
			<td>Select Student:</td>
			<td>
				&nbsp&nbsp&nbsp&nbsp<input type="radio" name="order" id="account" class="sort_radio" checked>&nbsp&nbspOrder By Account&nbsp&nbsp&nbsp&nbsp
				<input type="radio" name="order" id="name" class="sort_radio">&nbsp&nbspOrder By Names<br>
				<select name='filter_studentID[]' multiple id='filter_studentID' style="width: 302px; height: 100px">
					<option value=""> Select Student </option>
					<?php
					$sql="SELECT gibbonstudentenrolment.*,gibbonperson.officialName,gibbonrollgroup.name AS section,gibbonperson.account_number
							FROM gibbonstudentenrolment 
							LEFT JOIN gibbonperson ON gibbonstudentenrolment.gibbonPersonId=gibbonperson.gibbonPersonId 
							LEFT JOIN gibbonyeargroup ON gibbonstudentenrolment.gibbonYearGroupId=gibbonyeargroup.gibbonYearGroupId 
							LEFT JOIN gibbonrollgroup ON gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID 
							WHERE `gibbonstudentenrolment`.`gibbonSchoolYearID`={$_SESSION[$guid]["gibbonSchoolYearIDCurrent"]} 
							 AND (`gibbonperson`.`dateEnd` IS NULL  OR `gibbonperson`.`dateEnd`>='" . date("Y-m-d") . "')
							ORDER BY gibbonperson.account_number";

					$result=$connection2->prepare($sql);
					$result->execute();
					$dboutbut=$result->fetchAll();

					foreach ($dboutbut as $value) { ?>
					<option value="<?php echo $value['gibbonStudentEnrolmentID']+0?>"> <?php echo substr($value['account_number'], 5);?> - <?php echo $value['officialName'];?> (<?php echo $value['section'];?>) </option>
					<?php } ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Select Staff:</td>
			<td>
				<select name='filter_staffID[]' multiple id='filter_staffID' style="width: 302px; height: 100px">
					<option value=''>Select</option>
					<?php 
					$sql3="SELECT gibbonstaff.gibbonStaffID,gibbonperson.preferredName FROM gibbonstaff
							LEFT JOIN gibbonperson on gibbonperson.gibbonPersonID=gibbonstaff.gibbonPersonID WHERE (gibbonperson.dateEnd IS NULL OR gibbonperson.dateEnd> '".date('Y-m-d')."')";
				
					$result3=$connection2->prepare($sql3);
					$result3->execute();
					$staff_f_data=$result3->fetchAll();
					if(isset($staff_f_data)){
					foreach($staff_f_data as $n){
						print "<option value='".($n['gibbonStaffID']+0)."'>".$n['preferredName']."</option>";
					}
					
					}
					
					?>
				</select>
			</td>
		</tr>
	</table>
</td>
</tr>
<tr>
<td>
<b style='color:#e05f0d;'>Subject: </b>
<br>
<input type='text' name='subject' id='subject' placeholder='Enter Subject Name...'>
</td>
</tr>
<tr>
<td>
<b style='color:#e05f0d;'>Message:</b>
<br>(<span id='character_count'>0</span>/160)<br>
<textarea name='message_body' id='message_body' maxlength="160" style='float:left; width:300px;height:350px; font-size:16px;'></textarea>
</td>
</tr>
<tr>
<td>
<input type='submit' value='SEND' name='send_sms' id='send_sms'>
</tr>
</td>
</table>
</div>
<div style='width:40%; float:right;display:f;' id='confirm-box'>
You are going to Send <b id='message_count'></b> message:

<form method='POST' action="<?=$_SESSION[$guid]["absoluteURL"] . "/modules/Messenger/processSendSMS.php" ;?>">
<table width="100%">
<thead>
<tr><th>Person</th><th>Phone</th><tr>
</thead>
<tbody id='contact_list'>
</tbody>
<tfoot>
<tr><td><input type='submit' name='confirm' value='Confirm'></td><td><span  class='cancel c_button'>Cancel</span></td></tr>
</tfoot>
</table>
<input type='hidden' name='contact_data' id='contact_data' value=''>
<input type='hidden' name='subject_data' id='subject_data' value=''>
<input type='hidden' name='message_data' id='message_data' value=''>
<input type='hidden' name='action'  value='sendSMS'>
</form>
</div>
<?php
}
 ?>
<input type='hidden' id='processURL' value='<?=$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajaxSendIndivudual.php"?>'>