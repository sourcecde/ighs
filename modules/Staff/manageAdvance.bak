<?php
@session_start() ;

if (isActionAccessible($guid, $connection2, "/modules/Staff/staff_view.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
		try{
		$sql="SELECT `lakshyasalaryadvance`.*,`gibbonperson`.`preferredName`,`gibbonschoolyear`.`name`  
				FROM `lakshyasalaryadvance` 
				LEFT JOIN `gibbonstaff` ON `lakshyasalaryadvance`.`staffID`=`gibbonstaff`.`gibbonStaffID` 
				LEFT JOIN `gibbonperson` ON `gibbonstaff`.`gibbonPersonID`=`gibbonperson`.`gibbonPersonID` 
				LEFT JOIN `gibbonschoolyear` ON `lakshyasalaryadvance`.`schoolYearID`=`gibbonschoolyear`.`gibbonSchoolYearID` 
				ORDER BY `lakshyasalaryadvance`.`schoolYearID` DESC,`gibbonstaff`.`priority`,`date`";
		$result=$connection2->prepare($sql);
		$result->execute();
		$advanceD=$result->fetchAll();
		}
		catch(PDOException $e){
			echo $e;
		}
		$advanceData=array();
		foreach($advanceD as $a){
			$advanceData[$a['preferredName']][$a['name']][]=$a;
		}
?>
	<h1>Manage Advance :</h1>
	<div class="accordion">
	<?php 
	foreach($advanceData as $staffName=>$y){
		echo "<h3>$staffName</h3>";
		echo "<div class='accordion'>";
		foreach($y as $year=>$a){
			echo "<h3>$year</h3>";
			echo "<div>";
				echo "<table width='100%'>";
					echo "<tr>";
						echo "<th>Date</th>";
						echo "<th style='text-align: right'>Debit</th>";
						echo "<th style='text-align: right'>Credit</th>";
						echo "<th>Action</th>";
					echo "</tr>";
				$total=0;
				foreach($a as $d){
					$total+=$d['type']=='Cr'?$d['amount']:(0-$d['amount']);
					echo "<tr>";
					$date=dateFormatterR($d['date']);
					$amount=number_format($d['amount'],2);
					$action="<a class='editAdvance' id='e_{$d['advanceID']}'>Edit</a> | <a class='deleteAdvance' id='d_{$d['advanceID']}'>Delete</a>";
						echo "<td>$date</td>";
						echo $d['type']=='Cr'?"<td></td><td style='text-align: right'>$amount</td>":"<td  style='text-align: right'>$amount</td><td></td>";
						echo $d['salaryMonth']=='0'?"<td>$action</td>":"<td></td>";
					echo "</tr>";
				}
				$amount=number_format(abs($total),2);
					echo "<tr>";
						echo "<td style='text-align: right'><b>Total :</b></td>";
						echo $total<0?"<td style='text-align: right'><b>$amount</b></td>":"<td></td>";
						echo $total>=0?"<td style='text-align: right'><b>$amount</b></td>":"<td></td>";
						echo "<td></td>";
					echo "</tr>";
				echo "</table>";
			echo "</div>";
		}
		echo "</div>";
	}
	?>
	</div>
  
	<input type='hidden' id='posturl' value='<?php print $_SESSION[$guid]["absoluteURL"]?>/modules/<?php print $_SESSION[$guid]["module"] ?>/ajax_advance.php'>
<?php
}
function dateFormatterR($date){
	$tmp=explode("-",$date);
	return $tmp[2]."/".$tmp[1]."/".$tmp[0];
}
?>
<div id='hide_body'style='background-color :rgba(0,0,0, 0.7); width:100%; height:100%; position:fixed; left:0px; top:0px; z-index:100; display:none;'>
</div>

<div  id='modal_advance_edit' class='modal' style="position:fixed; left:500px; top:250px; z-index:200; border:1px; padding:20px ; background-color :rgba(0,0,0, 0.6); color:white; width:300px; display:none;">
	<b>Amount:</b>
	<input type='text' id='eAmount' class='inputV'><br><br>
	<b>Date:</b>
	<input type='text' id='eDate' class='inputV'><br><br>
	<b>No of Month:</b>
	<input type='text' id='enEMI' class='inputV'><br><br>
	<input type='hidden' id='eAdvanceID' class='inputV'>
	<div style='text-align: center; padding: 20px;'>
		<span class="cButton" id='advanceUpdate' style="padding: 10px 20px;">Update</span>
		<span class='s_close cButton' style="padding: 10px 20px;">Close</span>
	</div>
</div>
<script>
$(document).ready(function(){
	var posturl=$('#posturl').val();
	$( ".accordion" ).accordion({
		heightStyle: "content",
	   collapsible: true
	});	
	$("#eDate").datepicker({dateFormat: 'dd/mm/yy'});
	$('.s_close').click(function(){
		$('.modal').hide();
		$('#hide_body').fadeOut();
		$('.inputV').val('');
	});
	$('body').on('click','.editAdvance',function(){
		$('#hide_body').show();
		var idArrr=$(this).attr('id').split('_');
			var id=idArrr[1];
			$.ajax
			({
				type: "POST",
				url: posturl,
				data: { action: 'fetchAdvanceData', advanceID:id},
				success: function(msg)
				{ 
					var data=jQuery.parseJSON(msg);
					$("#eAdvanceID").val(id);
					$("#eAmount").val(data['amount']);
					$("#eDate").val(data['date']);
					$("#enEMI").val(data['nEMI']);
					$('#modal_advance_edit').fadeIn();
				}
			});
	});
	$('#advanceUpdate').click(function(){
		var data={};
		data['advanceID']=$("#eAdvanceID").val();
		data['amount']=$("#eAmount").val();
		data['date']=$("#eDate").val();
		data['nEMI']=$("#enEMI").val();
		$.ajax
			({
				type: "POST",
				url: posturl,
				data: { action: 'advanceUpdate', data:data},
				success: function(msg)
				{ 
					alert(msg);
					location.reload();
				}
			});
	});
	$('body').on('click','.deleteAdvance',function(){
		$('#hide_body').show();
		var idArrr=$(this).attr('id').split('_');
			var id=idArrr[1];
			$.ajax
			({
				type: "POST",
				url: posturl,
				data: { action: 'deleteAdvance', advanceID:id},
				success: function(msg)
				{ 
					alert(msg);
					location.reload();
				}
			});
	});
});
</script>