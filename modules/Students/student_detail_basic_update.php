<?php
//session_start();
if (isActionAccessible($guid, $connection2, "/modules/Students/student_detail_basic_update.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
	//Proceed!
	print "<div class='trail'>" ;
	print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . _("Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . _(getModuleName($_GET["q"])) . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/User Admin/family_manage.php'>" . _('Manage Families') . "</a> > </div><div class='trailEnd'>" . _('Edit Family') . "</div>" ;
	print "</div>" ;
	$sql="SELECT `gibbonperson`.`gibbonPersonID`,`officialName`,`account_number`,`gibbonyeargroup`.`name` as `class`,`gibbonrollgroup`.`name` as `section`
	FROM `gibbonperson`,`gibbonstudentenrolment`,`gibbonyeargroup`,`gibbonrollgroup`
	WHERE `gibbonperson`.`gibbonPersonID`=`gibbonstudentenrolment`.`gibbonPersonID` 
	AND `gibbonstudentenrolment`.`gibbonYearGroupID`=`gibbonyeargroup`.`gibbonYearGroupID`
	AND `gibbonstudentenrolment`.`gibbonRollGroupID`=`gibbonrollgroup`.`gibbonRollGroupID` ORDER BY `gibbonperson`.`officialName`";
	$result=$connection2->prepare($sql);
	$result->execute();
	$students=$result->fetchAll();
	//print_r($students);
	$gibbonRoleIDPrimary=$_SESSION[$guid]["gibbonRoleIDPrimary"];
	if($gibbonRoleIDPrimary==003)
	{
	    echo "<form method='post' action='".$_SESSION[$guid]["absoluteURL"] . "index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/student_detail_basic_update.php'>";
	echo "<input type='hidden' name='gibbonPersonID' id='gibbonPersonID' required value='{$_SESSION[$guid]['gibbonPersonID']}'>"; 
	echo "<input type='submit' id='submit' style='display:none' value='Submit'></td>";
	echo "</form>";
	if(isset($_POST['gibbonPersonID'])==FALSE)
	echo '<script>$("#submit").trigger("click");</script>';
	}
	else
	{
	echo "<form method='post' action='".$_SESSION[$guid]["absoluteURL"] . "index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/student_detail_basic_update.php'>";
	echo "<table width=98.5%>";
	echo "<tr>";
	echo "<td>";
	echo "<input type='text' name'account_number' id='account_number' style='width:100px; float:left;' placeholder='Account Number'>";
	echo "<input type='button' style=' float:left;' name='search_by_acc' id='search_by_acc' value='Go'>";
	echo "</td>";
	echo "<td>";
	echo "<select name='gibbonPersonID' id='gibbonPersonID' required>";
	echo "<option value=''>Select Student . .</option>";
	foreach($students as $s){
		echo "<option value='{$s['gibbonPersonID']}'>{$s['officialName']} - {$s['class']} {$s['section']}</option>";
	}  
	
	echo "</select>";
	echo "</td>";
	echo "<td><input type='submit' value='Submit'></td>";
	echo "</tr>";
	echo "</table>";
	echo "</form>";
	}
	if($_POST || $_GET['source']=='new'){
	$sql="SELECT `firstName`,`surname`,`account_number`,`gender`,`dob`,`citizenship1`,`religion`,`nationalIDCardNumber`,`gibbonstudentenrolment`.*,`homeAddress`
	,`gibbonfamily`.`gibbonFamilyID`,`gibbonpersonmedical`.`bloodType`,`gibbonpersonmedical`.`gibbonPersonMedicalID`,`image_240`
	FROM `gibbonperson`,`gibbonstudentenrolment`,`gibbonfamilychild`,`gibbonfamily`,`gibbonpersonmedical`
	WHERE `gibbonperson`.`gibbonPersonID`=`gibbonstudentenrolment`.`gibbonPersonID` AND `gibbonpersonmedical`.`gibbonPersonID`=`gibbonperson`.`gibbonPersonID`
	AND `gibbonstudentenrolment`.`gibbonSchoolYearID`=".$_SESSION[$guid]['gibbonSchoolYearIDCurrent']." AND `gibbonfamilychild`.`gibbonPersonID`=`gibbonperson`.`gibbonPersonID` 
	AND `gibbonfamilychild`.`gibbonFamilyID`=`gibbonfamily`.`gibbonFamilyID` AND `gibbonperson`.`gibbonPersonID`=".$_REQUEST['gibbonPersonID'];
	$result=$connection2->prepare($sql);
	$result->execute();
	$basicDetail=$result->fetch();
	//print_r($basicDetail);
	$sql="SELECT `gibbonfamilyadult`.`gibbonFamilyAdultID`,`officialName`,`profession`,`phone1CountryCode`,`phone1`,`email`,`relationship` FROM `gibbonfamilyadult`,`gibbonfamilyrelationship` 
	WHERE `gibbonfamilyadult`.`gibbonFamilyAdultID`=`gibbonfamilyrelationship`.`gibbonFamilyAdultID` AND `gibbonfamilyadult`.`gibbonFamilyID`=".$basicDetail['gibbonFamilyID'];
	$sql.=" AND `gibbonfamilyrelationship`.`relationship` IN ('Mother','Father') AND `gibbonfamilyrelationship`.`gibbonPersonID`=".$_REQUEST['gibbonPersonID']." ORDER BY `contactPriority` DESC";
	$result=$connection2->prepare($sql);
	$result->execute();
	$parentsDetail=$result->fetchAll();
	/*echo "<pre>";
	print_r($basicDetail);
	echo "</pre>";*/
?>
<form method='post' action='<?php echo $_SESSION[$guid]["absoluteURL"] . "/modules/Students/process_basic_detail.php";?>'>
	<table>
	<tr>
	<td style='padding:0;margin:0;'>
	<table width=100% class='basicUpdateTable'>
		<tr>
			<td width=33%>
				<span>Given Name<span style='color:red'>*</span></span>
				<input type='text' name='firstName' value='<?php echo $basicDetail['firstName'];?>' style='width:60%' required='required'>
			</td>
			<td  width=33%>
				<span>Last Name<span style='color:red'>*</span></span>
				<input type='text' name='surname' value='<?php echo $basicDetail['surname'];?>' style='width:60%' required='required'>
			</td>
			<td rowspan='4' style='text-align:center;'  width=33%>
			<b style='font-size:16px;'>Account No. : <?php echo abs($basicDetail['account_number']);?></b><br>
			<img src="<?php echo $_SESSION[$guid]['absoluteURL'].$basicDetail['image_240'];?>" height=200px><br>
			<span style='font-size:10px'>Lakshya System ID : <?php echo substr($_POST['gibbonPersonID'],-6);?></span>
			</td>
		</tr>
		<tr>
			<!---<td>
				<span>Account No.<span style='color:red'>*</span></span>
				<input type='text' name='account_number' value='<?php echo substr($basicDetail['account_number'],-5);?>' required='required' readonly>
			</td>-->
			<td>
				<span>Class<span style='color:red'>*</span></span>
				<select name='gibbonYearGroupID' id='gibbonYearGroupID' required='required'>
				<?php
					$sql="SELECT * FROM `gibbonyeargroup` ORDER BY `sequenceNumber`";
					$result=$connection2->prepare($sql);
					$result->execute();
					$class=$result->fetchAll();
					foreach($class as $c){
						$selected="";
						if($c['gibbonYearGroupID']==$basicDetail['gibbonYearGroupID']){
							$selected="selected";
						}
						echo "<option value='".$c['gibbonYearGroupID']."' $selected>".$c['name']."</option>";
					}
				?>
				</select>
			</td>
			<td>
				<span>Section<span style='color:red'>*</span></span>
				<select name='gibbonRollGroupID' id='gibbonRollGroupID' required='required'>
				<?php
					echo $sql="SELECT * FROM `gibbonrollgroup` WHERE `gibbonYearGroupID`=".$basicDetail['gibbonYearGroupID']." AND `gibbonSchoolYearID`=".$_SESSION[$guid]['gibbonSchoolYearIDCurrent'];
					$result=$connection2->prepare($sql);
					$result->execute();
					$section=$result->fetchAll();
					foreach($section as $s){
						$selected="";
						if($s['gibbonRollGroupID']==$basicDetail['gibbonRollGroupID']){
							$selected="selected";
						}
						echo "<option value='".$s['gibbonRollGroupID']."' $selected>".$s['name']."</option>";
					}
				?>
				</select>
			</td>
		</tr>
		<tr>
			<td>
				<span>Roll No.<span style='color:red'>*</span></span>
				<input type='text' name='rollOrder' value='<?php echo $basicDetail['rollOrder'];?>' style='width:60%' required='required'>
			</td>
			<td>
				<span>Gender<span style='color:red'>*</span></span>
				<span style='float:right'>
				<select name='gender'>
					<option value='M' <?php if($basicDetail['gender']=='M'){echo "selected='selected'";}?>>Male</option>
					<option value='F' <?php if($basicDetail['gender']=='F'){echo "selected='selected'";}?>>Female</option>
				</select>
				<?php //echo $basicDetail['gender'];?>
				</span>
			</td>
		</tr>
		<tr>
			<td>
				<span>DOB<span style='color:red'>*</span></span>
				<input type='text' name='dob' id='dob' value='<?php echo dateformat($basicDetail['dob']);?>' style='width:60%' required='required'>
				<script>
				$(document).ready(function(){
					$('#dob').datepicker({ dateFormat: 'dd/mm/yy' });
				});	
				</script>
			</td>
			<td>
				<span>Category<span style='color:red'>*</span></span>
				<select name='category'>
					<option value='Gen' <?php if($basicDetail['gender']=='Gen'){echo "selected='selected'";}?>>General</option>
					<option value='OBC' <?php if($basicDetail['gender']=='OBC'){echo "selected='selected'";}?>>OBC</option>
					<option value='SC' <?php if($basicDetail['gender']=='SC'){echo "selected='selected'";}?>>SC</option>
					<option value='ST' <?php if($basicDetail['gender']=='ST'){echo "selected='selected'";}?>>ST</option>
				</select>
			</td>
		<tr>
			<td>
				<span>Nationality<span style='color:red'>*</span></span>
				<input type='text' name='citizenship1' value='<?php echo $basicDetail['citizenship1'];?>' style='width:60%' required='required'>	
			</td>
			<td>
				<span>Religion<span style='color:red'>*</span></span>
				<select name='religion' id='religion'>
					<option value='Hindu' <?php if($basicDetail['religion']=='Hindu'){echo "selected";}?>>Hindu</option>
					<option value='Muslim' <?php if($basicDetail['religion']=='Muslim'){echo "selected";}?>>Muslim</option>
					<option value='Sikh' <?php if($basicDetail['religion']=='Sikh'){echo "selected";}?>>Sikh</option>
					<option value='Christian' <?php if($basicDetail['religion']=='Christian'){echo "selected";}?>>Christian</option>
					<option value='Jain' <?php if($basicDetail['religion']=='Jain'){echo "selected";}?>>Jain</option>
					<option value='Buddhist' <?php if($basicDetail['religion']=='Buddhist'){echo "selected";}?>>Buddhist</option>
					<option value='Others' <?php if($basicDetail['religion']=='Others'){echo "selected";}?>>Others</option>
				</select>
			</td>
			<td>
				<span>Aadhar Card No.</span>
				<input type='text' style='width:50%' name='nationalIDCardNumber' value='<?php echo $basicDetail['nationalIDCardNumber'];?>' maxlength='12' minlength='12'>
			</td>
		</tr>
		<tr>
			<td>
				<span>Blood Group</span>
				<select name='bloodType' id='bloodType'>
					<option value=''>NA</option>
					<option value='A+' <?php if($basicDetail['bloodType']=='A+'){echo "selected";}?>>A+</option>
					<option value='B+' <?php if($basicDetail['bloodType']=='B+'){echo "selected";}?>>B+</option>
					<option value='O+' <?php if($basicDetail['bloodType']=='O+'){echo "selected";}?>>O+</option>
					<option value='AB+' <?php if($basicDetail['bloodType']=='AB+'){echo "selected";}?>>AB+</option>
					<option value='A-' <?php if($basicDetail['bloodType']=='A-'){echo "selected";}?>>A-</option>
					<option value='B-' <?php if($basicDetail['bloodType']=='B-'){echo "selected";}?>>B-</option>
					<option value='O-' <?php if($basicDetail['bloodType']=='O-'){echo "selected";}?>>O-</option>
					<option value='AB-' <?php if($basicDetail['bloodType']=='AB-'){echo "selected";}?>>AB-</option>
				</select>
			</td>
			<td colspan='2'>
				<span>Home Address<span style='color:red'>*</span></span>
				<textarea name='homeAddress' cols='60' rows='4' maxlength='255' required='required'><?php echo $basicDetail['homeAddress'];?></textarea>
				<input type='hidden' name='gibbonPersonID' value='<?php echo $_REQUEST['gibbonPersonID'];?>'>
				<input type='hidden' name='gibbonFamilyID' value='<?php echo $basicDetail['gibbonFamilyID'];?>'>
				<input type='hidden' name='gibbonPersonMedicalID' value='<?php echo $basicDetail['gibbonPersonMedicalID'];?>'>
			</td>
		</tr>
	</table>
	<table width=100%>
	<tr>
<?php
	$i=0;
	foreach($parentsDetail as $p){
		echo "<td style='padding:0;margin:0'>";
		echo "<table width=100%>";
		echo "<tr>";
		echo "<th colspan='2' style='text-align:center'>";
		echo $p['relationship']."'s Detail";
		echo "</th>";		
		echo "</tr>";
		echo "<tr>";
		echo "<td>";
		echo "<span>Name<span style='color:red'>*</span></span>";
		echo "</td><td>";
		echo "<input type='hidden' style='width:250px' name='gibbonFamilyAdultID".$i."' value='".$p['gibbonFamilyAdultID']."' required='required'>";
		echo "<input type='text' style='width:250px' name='officialName".$i."' value='".$p['officialName']."' required='required'>";
		echo "</td>";		
		echo "</tr>";
		echo "<tr>";
		echo "<td>";
		echo "<span>Occupation</span>";
		echo "</td><td>";
		echo "<select style='width:250px' name='profession".$i."'>";?>
		<option value='' <?php if($p['profession']==''){echo "selected";}?>>Please Select . .</option>
		<option value='Business' <?php if($p['profession']=='Business'){echo "selected";}?>>Business</option>
		<option value='Doctor' <?php if($p['profession']=='Doctor'){echo "selected";}?>>Doctor</option>
		<option value='Farmer' <?php if($p['profession']=='Farmer'){echo "selected";}?>>Farmer</option>
		<option value='Housewife' <?php if($p['profession']=='Housewife'){echo "selected";}?>>Housewife</option>
		<option value='Others' <?php if($p['profession']=='Others'){echo "selected";}?>>Others</option>
		<option value='Service' <?php if($p['profession']=='Service'){echo "selected";}?>>Service</option>
		<option value='Self_Employed' <?php if($p['profession']=='Self_Employed'){echo "selected";}?>>Self-Employed</option>
		<option value='Teacher' <?php if($p['profession']=='Teacher'){echo "selected";}?>>Teacher</option>
		<option value='Unemployed' <?php if($p['profession']=='Umemployed'){echo "selected";}?>>Unemployed</option>
<?php	echo "</select>";
		echo "</td>";		
		echo "</tr>";
		echo "<tr>";
		echo "<td>";
		echo "<span>Mobile No.</span>";
		echo "</td><td>";
		echo "<select name='phone1CountryCode".$i."' style='width:60px;float: left;margin-left: 12%;'>";
		try {
			$dataSelect=array(); 
			$sqlSelect="SELECT * FROM gibboncountry ORDER BY printable_name" ;
			$resultSelect=$connection2->prepare($sqlSelect);
			$resultSelect->execute($dataSelect);
		}
		catch(PDOException $e) { }
		while ($rowSelect=$resultSelect->fetch()) {
			$selected="" ;
			if ($p["phone1CountryCode"]==$rowSelect["iddCountryCode"]) {
				$selected="selected" ;
			}	
			print "<option $selected value='" . $rowSelect["iddCountryCode"] . "'>" . htmlPrep($rowSelect["iddCountryCode"]) . " - " .  htmlPrep(_($rowSelect["printable_name"])) . "</option>" ;
							}
		echo "</select>";
		echo "<input type='text' style='width:180px;' maxlength='10'  minlength='10' name='phone1".$i."' value='".$p['phone1']."'>";
		echo "</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>";
		echo "<span>Email</span>";
		echo "</td><td>";
		echo "<input type='text' style='width:250px' name='email".$i."' value='".$p['email']."'>";
		echo "</td>";		
		echo "</tr>";
		echo "</table>";
		echo "</td>";
		$i++;
	}
?>
	</tr>
	</table>
	<input style='float:right' type='submit' value='Submit'>
	</td>
	</tr>
	</table>
</form>
<?php
}
}
function dateformat($a){
	$dob=explode("-",$a);
	return $dob[2]."/".$dob[1]."/".$dob[0];
}
?>
<input type="hidden" name="changeRollGroupIDURL" id="changeRollGroupIDURL" value="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajax_change_rollgroup.php" ?>">
<input type="hidden" name="schoolYear" id="schoolYear" value="<?php echo $_SESSION[$guid]['gibbonSchoolYearIDCurrent'];?>">
<input type="hidden" name="get_personID_from_accno_url" id="get_personID_from_accno_url" value="<?php echo $_SESSION[$guid]["absoluteURL"] . "/modules/Students/ajax_get_personid_by_accno.php";?>">
<script>
$(document).ready(function(){
	$('#gibbonYearGroupID').change(function(){
		var yearGroup = $(this).val();
		var schoolYear = $('#schoolYear').val();
		var changeRollGroupIDURL = $('#changeRollGroupIDURL').val();
		$.ajax
		({
			type: "POST",
			url: changeRollGroupIDURL,
			data: {yearGroup:yearGroup,schoolYear:schoolYear},
			success: function(msg)
			{ 
				console.log(msg);
				$('#gibbonRollGroupID').empty().append(msg);
			}
		});
	});
	$("#search_by_acc").click(function(){
		//alert("Hulul");
		var account_number=$("#account_number").val();
		var enrollid='';
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
					$('#gibbonPersonID option[value="' + msg + '"]').prop('selected', true);
 				}
 			}
 			});
	})

});
</script>