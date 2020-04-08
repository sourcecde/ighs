<?php
$sql="SELECT `gibbonperson`.`gibbonPersonID`,`preferredName`,`account_number`,`gibbonstudentenrolment`.`rollOrder` FROM `gibbonperson` 
		LEFT JOIN `gibbonstudentenrolment` ON `gibbonstudentenrolment`.`gibbonPersonID`=`gibbonperson` .`gibbonPersonID` 
		WHERE `gibbonstudentenrolment`.`gibbonSchoolYearID`={$_REQUEST['year_id']} AND `gibbonstudentenrolment`.`gibbonRollGroupID`={$_REQUEST['section_name']} 
		ORDER BY `gibbonstudentenrolment`.`rollOrder`";
$result=$connection2->prepare($sql);
$result->execute();
$studentsData=$result->fetchAll();		
?>

<div style="width:30%; border: 0px solid; float:left">
<table style='width:100%' >
<thead>
<tr>
	<th><small>All</small>&nbsp;<input type='checkbox' id='selectAll' checked></th>
	<th>Student</th>
</tr>
</thead>
<tbody>
<?php foreach($studentsData as $s){?>
<tr>
	<td><input type='checkbox' class='name_select' id="ch_<?=$s['gibbonPersonID']+0?>" checked></td>
	<td class='student' id='st_<?=$s['gibbonPersonID']+0?>'><b><?=$s['preferredName']?></b><br><span style='float:left'>Acc No: <i><?=$s['account_number']+0?></i></span><span style='float:right'> Roll: <i><?=$s['rollOrder']?></i></span></td>
</tr>
<?php }?>
</tbody>
</table>
</div>
<!--
<script src="<?php echo $_SESSION[$guid]["absoluteURL"] ;?>/modules/Fee/js/jquery.dataTables.min.js"></script>
 <script>
	 $(document).ready(function(){
		$('.myTable').DataTable({
			"iDisplayLength": 50,
			"oLanguage": {
			  "sLengthMenu": '<select>'+
				'<option value="50">50</option>'+
				'<option value="100">100</option>'+
				'<option value="200">200</option>'+
				'<option value="-1">All</option>'+
				'</select>'
			}
		});
	});
 </script>
 -->