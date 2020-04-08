<?php
@session_start() ;
//if (isActionAccessible($guid, $connection2, "/modules/Exam/manageExam.php")==FALSE) {
if (False) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else{
	try {
	$sql1="SELECT `gibbonStaffID`,`pf_no`,`preferredName`,`uan_no` FROM `gibbonstaff` LEFT JOIN `gibbonperson` ON `gibbonperson`.`gibbonPersonID`=`gibbonstaff`.`gibbonPersonID` WHERE `pf_no`!='' AND (`dateEnd` IS NULL OR `dateEnd`>='2017-12-01') AND (`dateStart`<='2017-12-31')";
	$result1=$connection2->prepare($sql1);
	$result1->execute();
	$pData=$result1->fetchAll();
	}
	catch(PDOException $e) { 
	echo $e;
	}
			echo "<form method='POST' action='".$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] ."/process_pf_update.php'>";
			echo "<div id='aadharcard'>";
			echo "<table style='min-width:100%' id='marksTable'>";
			echo "<tr>";
			echo "<th>Student's Details</th>";
			echo "<th>Student's Aadhar No.</th>";
			echo "</tr>";
			foreach($pData as $p){
				$acc=substr($p['account_number'],-4);
				echo "<tr>";
				echo "<td style='min-width: 11ch;'><b>{$p['preferredName']}</b>";
				if($_GET['yearGroupID']!='all')
					echo "<br><span style='float:left;'>Roll :<b>{$p['rollOrder']}</b></span><span style='float:right;'><b>Acc. No. {$acc}</b></span></td>";
				else
					echo "<br><span><b>Acc. No. {$acc}</b></span></td>";
				$enID=$p['gibbonPersonID']+0;
				echo "<td><input type='text' id='{$enID}' name='{$enID}' class='sAadhar' value='{$p['nationalIDCardNumber']}'></td></tr>";
			}
			echo "<tr><td colspan='2' style='text-align:center'><input type='submit' id='submit' name='submit' value='Submit'></td></tr>"; 
			echo "</table>";
			echo "</div>";
			echo "<input type='hidden' name='yearGroupID' value='".$_GET['yearGroupID']."'>";
			echo "<input type='hidden' name='rollGroupID' value='".$_GET['rollGroupID']."'>";
			echo "</form>";	
?>
<div id='hide_body'style='background-color :rgba(0,0,0, 0.7); width:100%; height:100%; position:fixed; left:0px; top:0px; z-index:100; display:none;'>
</div>
<input type='hidden' id='changeRollGroupIDURL' value='<?=$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajaxChangeRollGroupID.php"?>'>
<script>
function printDiv() 
{

  var divToPrint=document.getElementById('aadharcard');

  var newWin=window.open('','Print-Window');

  newWin.document.open();

  newWin.document.write('<html><body onload="window.print()">'+divToPrint.innerHTML+'</body></html>');

  newWin.document.close();

  setTimeout(function(){newWin.close();},10);

}

$(document).ready(function(){
	var processURL=$('#processURL').val();
	var changeRollGroupIDURL=$('#changeRollGroupIDURL').val();
	
	$('#yearGroupID').change(function(){
		var yearGroupID=$(this).val();
			$.ajax
			({
				type: "POST",
				url: changeRollGroupIDURL,
				data: {yearGroupID:yearGroupID},
				success: function(msg)
				{ 
					console.log(msg);
					$('#rollGroupID').empty().append(msg);
				}
			});
	});
	$('#print').click(function(){
		$('#submit').click();
		$('#marksTable').attr('border','1');
		printDiv();
	});
});

</script>