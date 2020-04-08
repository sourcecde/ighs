<?php
@session_start() ;

//if (isActionAccessible($guid, $connection2, "/modules/Exam/manageExam.php"//)==FALSE) {
//if (False) {
	//Acess denied
//	print "<div class='error'>" ;
//		print _("You do not have access to this action.") ;
//	print "</div>" ;
//}
//else{
    
if (isActionAccessible($guid, $connection2, "/modules/Fee/manage_banks.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {    
	try{
	$sql1="SELECT * FROM `fee_bank_master` ORDER BY `bankName`";
	$result1=$connection2->prepare($sql1);
	$result1->execute();
	$banks=$result1->fetchAll();
	}
	catch(PDOException $e) { 
	echo $e;
	}	
?>
	<div style="margin-bottom: 10px; padding: 5px 20px;">
		<button class="cButton" style="float: left"id='addSub'>Add Banks</button>
	</div>
	<br>
	<table style='width: 60%; margin-left: 5%;' id='banksTable'>
	<thead>
		<tr>
			<th>Bank</th>
			<th>Short Name</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
	<?php
		foreach($banks as $b){
	?>
		<tr>
			<td><p id='bName_<?=$b['bankMasterID']?>'><?=$b['bankName']?></p></td>
			<td><p id='shortName_<?=$b['bankMasterID']?>'><?=$b['bankAbbr']?></p></td>
			<td><a href='#' class='subEdit' id='e_<?=$b['bankMasterID']?>'>Edit</a> | <a href='#' class='subDelete' id='d_<?=$b['bankMasterID']?>'>Delete</a></td>
		</tr>
	<?php } ?>
	</tbody>
	</table>
<?php	
}
 ?>
<div id='hide_body'style='background-color :rgba(0,0,0, 0.7); width:100%; height:100%; position:fixed; left:0px; top:0px; z-index:100; display:none;'>
</div>

<div  id='modal_sub_add' class='modal' style="position:fixed; left:500px; top:250px; z-index:200; border:1px; padding:20px ; background-color :rgba(0,0,0, 0.6); color:white; width:300px; display:none;">
	<b>Bank Name:</b>
	<input type='text' id='subAddName' class='modalInput' style='width:180px;'><br><br>
	<b>Short Name:</b>
	<input type='text' id='subAddShortName' class='modalInput' maxlength='4' style='width:180px;' placeholder='4 Characters Max'><br><br>
	<div style='text-align: center; padding: 20px;'>
		<span class="cButton" id='subAddSubmit' style="padding: 10px 20px;">Add</span>
		<span class='s_close cButton' style="padding: 10px 20px;">Close</span>
	</div>
</div>
<div  id='modal_sub_edit' class='modal' style="position:fixed; left:500px; top:250px; z-index:200; border:1px; padding:20px ; background-color :rgba(0,0,0, 0.6); color:white; width:300px; display:none;">
	<b>Bank Name:</b>
	<input type='hidden' id='subEditID' class='modalInput' style='width:180px;'>
	<input type='text' id='subEditName' class='modalInput' style='width:180px;'><br><br>
	<b>Short Name:</b>
	<input type='text' id='subEditShortName' class='modalInput' maxlength='4' style='width:180px;'><br><br>
	<div style='text-align: center; padding: 20px;'>
		<span class="cButton" id='subEditSubmit' style="padding: 10px 20px;">Update</span>
		<span class='s_close cButton' style="padding: 10px 20px;">Close</span>
	</div>
</div> 
 
<input type='hidden' id='processURL' value='<?=$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/processManageBanks.php"?>'>
<script src="<?php echo $_SESSION[$guid]["absoluteURL"] ;?>modules/<?=$_SESSION[$guid]["module"];?>/js/jquery.dataTables.min.js"></script>
<script>
	$(document).ready(function(){
		$('#banksTable').DataTable();
		
		var processURL=$('#processURL').val();
		
		$('#addSub').click(function(){
			$('#hide_body').show();
			$('#modal_sub_add').show();
		});
		$('body').on('click', '.subEdit', function (){
			$('#hide_body').show();
			$('#modal_sub_edit').show();
			var idArrr=$(this).attr('id').split('_');
			var id=idArrr[1];
			$('#subEditID').val(id);
			$('#subEditName').val($('#bName_'+id).text());
			$('#subEditShortName').val($('#shortName_'+id).text());
		});		
		$('body').on('click', '.subDelete', function (){
			$('#hide_body').show();
			var idArrr=$(this).attr('id').split('_');
			var id=idArrr[1];
			$.ajax
			({
				type: "POST",
				url: processURL,
				data: { action: 'deleteSub', bankMasterID:id},
				success: function(msg)
				{ 
					console.log(msg);
					alert(msg);
					$('#hide_body').hide();
					location.reload();
				}
			});
			
		});
		$('.s_close').click(function(){
			$('.modal').hide();
			$('#hide_body').hide();
			$('.modalInput').val('');
		});
	
		$('#subAddSubmit').click(function(){
			var bankName=$('#subAddName').val();
			var bankAbbr=$('#subAddShortName').val();
			$('#modal_sub_add').hide();
			$.ajax
			({
				type: "POST",
				url: processURL,
				data: { action: 'addSub', bankName:bankName,bankAbbr:bankAbbr},
				success: function(msg)
				{ 
					console.log(msg);
					alert(msg);
					$('#hide_body').hide();
					location.reload();
				}
			});
		});
		$('#subEditSubmit').click(function(){
			var bankMasterID=$('#subEditID').val();
			var bankName=$('#subEditName').val();
			var bankAbbr=$('#subEditShortName').val();
			$('#modal_sub_edit').hide();
			$.ajax
			({
				type: "POST",
				url: processURL,
				data: { action: 'editSub', bankName:bankName, bankMasterID: bankMasterID, bankAbbr:bankAbbr},
				success: function(msg)
				{ 
					console.log(msg);
					alert(msg);
					$('#hide_body').hide();
					location.reload();
				}
			});
		});
	});
</script>
