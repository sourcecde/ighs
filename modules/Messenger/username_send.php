<?php
$sql="SELECT `gibbonYearGroupID`,`name` FROM `gibbonyeargroup`";
$result=$connection2->prepare($sql);
$result->execute();
$classDB=$result->fetchAll();

$sql="SELECT `gibbonperson`.`gibbonPersonID`,`account_number`,`preferredName`,`username`,`phone1` FROM `gibbonperson` 
	LEFT JOIN `gibbonstudentenrolment` ON `gibbonstudentenrolment`.`gibbonPersonID`=`gibbonperson`.`gibbonPersonID`
	WHERE `canLogin`='Y' AND `status`='Full' AND `gibbonRoleIDPrimary`=3 AND `passwordForceReset`='Y' 
	AND `phone1`!='' AND `gibbonSchoolYearID`=".$_SESSION[$guid]['gibbonSchoolYearID'];
	if(isset($_POST['select_class']) && $_POST['select_class']!=""){
		$sql.=" AND `gibbonstudentenrolment`.`gibbonYearGroupID`=".$_POST['select_class'];
	}
	if(isset($_POST['select_personID']) && $_POST['select_personID']!=""){
		$sql.=" AND `gibbonperson`.`gibbonPersonID`=".$_POST['select_personID'];
	}
	$sql.=" ORDER BY `account_number`";
//echo $sql;
$result=$connection2->prepare($sql);
$result->execute();
$students=$result->fetchAll();
echo "<pre>";
//print_r($students);
echo "</pre>";
?>
<form action="" method="POST">
<table width="100%">
 <tr>
 	<td>
	  <select name="select_class" id="select_class">
		<option value=""> - Select Class - </option>
		<?php foreach ($classDB as $value) {
			$s=isset($_POST['select_class']) && $_POST['select_class']==$value['gibbonYearGroupID']?"selected= 'selected'":"";
			echo "<option value='{$value['gibbonYearGroupID']}'>{$value['name']}</option>";
		} ?>
	  </select>
	</td>
    <td >
    <select name="select_personID" id="select_personID">
		<option value=""> - Select Student - </option>
		<?php foreach ($students as $value) { 
			$ac_no=substr($value['account_number']+0,-4);
			$s=isset($_POST['select_personID']) && $_POST['select_personID']==$value['gibbonPersonID']?"selected= 'selected'":"";
			echo "<option value='{$value['gibbonPersonID']}' $s>{$value['preferredName']} ( $ac_no )</option>";
		} ?>
	 </select>		     
	</td>
	<td>
		<input type="text" name="account_number" id="account_number" style="float: left;width:100px" placeholder="Account Number">
		<input type="button" name="go" id="go" value="Go">
	</td>
	<td>
		<input type='submit' name='Submit'>
		<input type='button' value='Reset' id='reset'>
  </tr>
 </table>
 </form>
 <table width="100%">
 <tr>
	<th style="text-align:center;"><input type='checkbox' id='selectall'></th>
	<th>Name</th>
	<th>Account Number</th>
	<th>Username</th>
	<th>Phone</th>
 </tr>
 <?php foreach($students as $s){
	echo "<tr><td><input type='checkbox' id='P_{$s['username']}' value='{$s['gibbonPersonID']}' class='select'</td>";
	echo "<td>{$s['preferredName']}</td>";
	echo "<td>".substr($s['account_number'],-4)."</td>";
	echo "<td>{$s['username']}</td>";
	echo "<td>{$s['phone1']}</td>";
	echo "</tr>";
 }
 ?>
 <tr>
	<td colspan=5><input type='button' id='submit' style='float:right;background-color:seagreen;color:white;padding: 6px;margin-right: 25px;' value='Send SMS'>
 </tr>
 </table>
<input type="hidden" name="get_personID_from_accno_url" id="get_personID_from_accno_url" value="<?php echo $_SESSION[$guid]["absoluteURL"] . "/modules/Messenger/ajax_get_personid_by_accno.php";?>">
<input type='hidden' id='linkurl' value='<?=$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajaxSendGroup.php"?>'>
<input type='hidden' id='gibbonPersonID' value=<?=abs($_SESSION[$guid]["gibbonPersonID"])?>>
echo "<div id='loading' style='display:none; position:fixed; width:100%;height:100%; top:0px; left:0px;'>";
	echo "
			<div id='loading'>
				<center><h2>It will take a long time. Please wait......</h2></center>
                <ul class='bokeh'>
                    <li></li>
                    <li></li>
                    <li></li>
                </ul>
            </div>
		";
echo "</div>";
 <script>
 $(document).ready(function(){
 	$("#go").click(function(){
		var account_number=$("#account_number").val();
		var checkurl=$("#get_personID_from_accno_url").val();
		$.ajax
 		({
 			type: "POST",
 			url: checkurl,
 			data: {account_number:account_number},
 			success: function(msg)
 			{
 				console.log(msg);
 				if(msg=='0')
 					{
 					alert("Account Number does not exist");
 					return false;
 					}
 				else {
					$('#select_personID option[value="' + msg + '"]').prop('selected', true);
 				}
 			}
 		});
	});
	$("#reset").click(function(){
		window.location = window.location.href;
	});
	$("#selectall").click(function(){
		if($(this).is(":checked")){
			$(".select").prop("checked",true);
		}
		else{
			$(".select").prop("checked",false);
		}
	});
	$("#submit").click(function(){
		console.log("Clicked");
		if($(".select:checked").length >= 1){
		var chkArray = [];
		var linkurl=$("#linkurl").val();
		var gibbonPersonID=$("#gibbonPersonID").val();
		//alert($("#gibbonPersonID").val());
		$(".select:checked").each(function() {
		chkArray.push($(this).val());
		});
	
		/* we join the array separated by the comma */
		var selected;
		selected = chkArray.join(',') ;
	
		/* check if there is selected checkboxes, by default the length is 1 as it contains one single comma */
		$("#loading").show();
		$.ajax({
			type: "POST",
 			url: linkurl,
 			data: {action:'username_sms',P_ids:selected,gibbonPersonID:gibbonPersonID},
 			success: function(msg)
 			{
 				 alert(msg);
				 console.log(msg);
				 location.reload();
 			},
 			complete: function(){
 			    $("#loading").hide();
 			}
		});
		}
		else{
			alert("Please at least one of the checkbox");
		}
	});
	/*$('#loading').bind('ajaxStart', function(){
    $(this).show();
    }).bind('ajaxStop', function(){
    $(this).hide();
    });*/
 });
</script>