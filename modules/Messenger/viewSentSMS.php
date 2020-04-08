<?php
@session_start() ;
if (isActionAccessible($guid, $connection2, "/modules/Messenger/viewSentSMS.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else{
	$sql="SELECT `lakshyasmslog`.*,`gibbonperson`.`preferredName`,SUM(CASE `success` WHEN 1 THEN 1 ELSE 0 END) AS N FROM `lakshyasmslog` 
			LEFT JOIN `gibbonperson` ON `gibbonperson`.`gibbonPersonID`=`lakshyasmslog`.`senderPersonID` 
			LEFT JOIN `lakshyasmsrecipients` ON `lakshyasmsrecipients`.`SMSLogID`=`lakshyasmslog`.`SMSLogID` 
			GROUP BY`lakshyasmslog`.`SMSLogID` ORDER BY `SMSLogID` DESC";
	$result=$connection2->prepare($sql);
	$result->execute();
	$smsLog=$result->fetchAll();
	//print_r($smsLog);
?>
<h3>View Sent SMS:</h3>
<table width="100%">
<tr><th>Date</th><th>Status</th><th>Subject</th><th>Message</th><th>Count</th><th>Sender</th><th>Time</th></tr>
<?php
foreach($smsLog as $s){
	$date=date('d/m/Y',$s['sendingTime']);
	$time=date('h:i A',$s['sendingTime']);
	echo "<tr class='smsLogHeader' id='{$s['SMSLogID']}'>";
		echo "<td>$date</td>";
		echo "<td>{$s['status']}</td>";
		echo "<td>{$s['subject']}</td>";
		echo "<td>{$s['message']}</td>";
		echo "<td>{$s['N']} of {$s['count']} </td>";
		echo "<td>{$s['preferredName']}</td>";
		echo "<td>$time</td>";
	echo "</tr>";
	echo "<tr class='smsLogDetails hidden_panel'><td colspan='5'><span id='DP{$s['SMSLogID']}'></span></td></tr>";
}
 ?>
</table>
<?php
}
?>