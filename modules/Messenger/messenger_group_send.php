<?php
/*
Gibbon, Flexible & Open School System
Copyright (C) 2010, Ross Parker

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

@session_start() ;
/*
if (isActionAccessible($guid, $connection2, "/modules/Students/messenger_group_send.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {*/
	$sql="SELECT `lakshyasmsgroup`.*,`preferredName`,`phone1`,`account_number`,admission_number FROM `lakshyasmsgroup`,`gibbonperson` WHERE `lakshyasmsgroup`.`gibbonPersonID`=`gibbonperson`.`gibbonPersonID` AND `lakshyasmsgroup`.`groupID`=1";
	$result=$connection2->prepare($sql);
	$result->execute();
	$data=$result->fetchAll();
	//print_r($data);
?>
	<h1 style=''>Welcome SMS</h1>
	<table width='100%'>
	<thead>
	<th style='text-align:center;'><input type='checkbox' id='selectall'></th>
	<th>Name</th>
	<th>Acc. No.</th>
	<th>Contact Number</th>
<!--<th>Application Form No.</th>-->
	<th>Admission Number</th>
<!--<th>Date & Time of Admission</th>-->
	</thead>
	<tbody>
	<?php
	foreach($data as $d){
	print "<tr>";
	print "<td><input type='checkbox' id='P_{$d['gibbonPersonID']}' value='{$d['_id']}' class='select'></td>";
	print "<td>{$d['preferredName']}</td>";
	print "<td>".substr($d['account_number'],-5)."</td>";
	print "<td>{$d['phone1']}</td>";
	//print "<td>{$d['ref_id']}</td>";
	print "<td>{$d['admission_number']}</td>"; 
    /*print "<td>";
	print datetimeformat($d['added_date']);
	print"</td>";*/
	print "</tr>";
	}
	?>
	</tbody>
	</table>
	<!-----<input type="submit" value="Send SMS" id="submit">----->
	<span class="cButton" id='send_sms' style="background-color:seagreen">Send</span>
<input type="submit" value="Discard Records" id="discard_pw"> 
	<input type='hidden' id='linkurl' value='<?=$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajaxSendGroup.php"?>'>
	<input type='hidden' id='gibbonPersonIDsms' value="<?=abs($_SESSION[$guid]["gibbonPersonID"])?>">
	<div  id='modal_send_sms' class='modal' style="position:fixed; left:450px; top:200px; z-index:200; border:1px; padding:20px ; background-color :rgba(0,0,0, 0.6); color:white; width:400px; display:none;">
	<b>Subject:</b>
	<input type='text' id='subject' readonly class='modalInput' value="welcome SMS" style='width:280px;'><br><br>
	<div style="">
	<b>Message:</b>
	<input id='message_body' readonly class='modalInput' value="CALCUTTA PUBLIC SCHOOL, ASWININGAR Welcome you to become one of the precious members of our family through your Child. Thanks you." style='width:280px;'><br><br>
	</div>
	<div style='text-align: center; padding: 20px;margin-top:35px;'>
		<span class="cButton" id='send_sms'>Send</span>
		<span class='s_close cButton' >Close</span>
	</div>
</div>
<?php
//}
function datetimeformat($date){
	$tmp1=explode(" ",$date);
	$tmp2=explode("-",$tmp1[0]);
	return $tmp2[2]."/".$tmp2[1]."/".$tmp2[0]." (".$tmp1[1].")";
}
?>
<script>
$(document).ready(function(){
	$("#selectall").click(function(){
		if($(this).is(":checked")){
			$(".select").prop("checked",true);
		}
		else{
			$(".select").prop("checked",false);
		}
	});
	$("#submit").click(function(){
		if($(".select:checked").length >= 1){
			$("#modal_send_sms").show();
		}
		else{
			alert("Please at least one of the checkbox");
		}
	});
	$(".s_close").click(function(){
		$("#modal_send_sms").hide();
	});	
	$("#send_sms").click(function(){
		var chkArray = [];
		var subject=$("#subject").val();
		var message=$("#message_body").val();
		var linkurl=$("#linkurl").val();
		var gibbonPersonID=$("#gibbonPersonIDsms").val();
		//alert($("#gibbonPersonID").val());
		$(".select:checked").each(function() {
		chkArray.push($(this).val());
		});
	
		/* we join the array separated by the comma */
		var selected;
		selected = chkArray.join(',') ;
	
		/* check if there is selected checkboxes, by default the length is 1 as it contains one single comma */
		if(message.length >= 1 && subject.length >= 1){
		$.ajax({
			type: "POST",
 			url: linkurl,
 			data: {action:'welcome_sms',P_ids:selected,message_data:message,subject_data:subject,gibbonPersonID:gibbonPersonID},
 			success: function(msg)
 			{
 				 alert(msg);
 				 //console.log(msg);
				 location.reload();
 			}
		});
		}
	});
});
</script>
<style>
.cButton{
	border: none;
    background-color: #ff731b;
    height: 28px;
    min-width: 55px;
    color: #ffffff;
    font-family: open_sanssemibold;
    font-weight: normal;
    margin: 2px;
    font-size: 14px;
    cursor: pointer;
    padding-left: 10px;
    padding-right: 10px;
	padding-top: 5px;
    padding-bottom: 5px;
}
</style>