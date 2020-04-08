<?php
@session_start();

if (isActionAccessible($guid, $connection2, "/modules/User Admin/manage_left_students.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {


$sql="SELECT `gibbonPersonID`,`preferredName`,`account_number` FROM `gibbonperson` WHERE `gibbonPersonID` IN (SELECT `gibbonPersonID` FROM `gibbonstudentenrolment`)";
$result=$connection2->prepare($sql);
$result->execute();
$Students=$result->fetchAll();
if (isActionAccessible($guid, $connection2, "/modules/User Admin/rollover.php")==FALSE) {
	//Acess denied
	print "<div class='error'>";
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
?>	
	<form method="post" action="">
	<table width="100%">
	<tbody>
	<tr><td>
		Select Student: 
		<select name="student_personID" id="student_personID" required style='width:320px'>
			<option value=''>Select Student</option>
			<?php
			foreach($Students as $st){
				$s=isset($_REQUEST['student_personID'])?($st['gibbonPersonID']==$_REQUEST['student_personID']?"selected":""):"";
				echo "<option value='{$st['gibbonPersonID']}' $s>{$st['preferredName']} -({$st['account_number']})</option>";
			}
			?>
		</select> 
	</td><td>	
		<input type='button' name="search_by_acc" id="search_by_acc" value='GO'>
		<input type='text'  name="account_number" id="account_number" placeholder='Account Number' style="float:left">
	</td><td>	
		<input type='submit' name='search' value='Search'>
	</td></tr>
	</tbody>
	</table>
	</form>
<?php } 
if($_POST){
	try{
		$sql="SELECT `leftstudenttracker`.*,`gibbonperson`.`nextSchool`,`gibbonperson`.`departureReason`,`gibbonperson`.`dateEnd` FROM `leftstudenttracker` LEFT JOIN `gibbonperson` ON `gibbonperson`.`gibbonPersonID`=`leftstudenttracker`.`student_id` WHERE `student_id`=".$_POST['student_personID'];
		$result=$connection2->prepare($sql);
		$result->execute();
		$leftData=$result->fetch();
		//print_r($leftData);
	}
	catch(PDOException $e){
		echo $e->getMesssage();
	}
	$year=empty($leftData)?$_SESSION[$guid]['gibbonSchoolYearIDCurrent']:$leftData['yearOfLeaving'];
	try{
		$sql="SELECT `gibbonschoolyear`.`name` as `year`,`gibbonstudentenrolment`.`rollOrder`,`gibbonyeargroup`.`name` AS `class`,`gibbonrollgroup`.`name` AS `section`,`gibbonstudentenrolment`.`gibbonPersonID`,`account_number`,`officialName`,`gender`,`dob`,`image_240`,`phone1` FROM `gibbonstudentenrolment` LEFT JOIN `gibbonschoolyear` ON `gibbonstudentenrolment`.`gibbonSchoolYearID`=`gibbonschoolyear`.`gibbonSchoolYearID` LEFT JOIN `gibbonyeargroup` ON `gibbonstudentenrolment`.`gibbonYearGroupID`=`gibbonyeargroup`.`gibbonYearGroupID` LEFT JOIN `gibbonrollgroup` ON `gibbonstudentenrolment`.`gibbonRollGroupID`=`gibbonrollgroup`.`gibbonRollGroupID` LEFT JOIN `gibbonperson` ON `gibbonstudentenrolment`.`gibbonPersonID`=`gibbonperson`.`gibbonPersonID` WHERE `gibbonstudentenrolment`.`gibbonSchoolYearID`=".$year." AND `gibbonperson`.`gibbonPersonID`=".$_POST['student_personID'];
		//echo $sql;
		$result=$connection2->prepare($sql);
		$result->execute();
		$sData=$result->fetch();
	}
	catch(PDOException $e){
		echo $e->getMesssage();
	}
	try{
		$sql="SELECT * FROM `gibbonschoolyear`";
		$result=$connection2->prepare($sql);
		$result->execute();
		$schoolYear=$result->fetchAll();
	}
	catch(PDOException $e){
		echo $e->getMessage();
	}
?>
<table width="100%">
<tbody>
  <tr><td width="50%">
	<table width="100%">
	<tbody>
	<tr>
	<td><b>Name : <?php echo $sData['officialName'];?></b></td>
	<td rowspan='2'><center><img src="<?php $image=$sData['image_240']!=NULL?$sData['image_240']:$_SESSION[$guid]['absoluteURL']."/themes/Default/img/anonymous_240.jpg"; echo $image;?>" style="width: 160px;"></center></td>
	</tr>
	<tr>
	<td><b>Phone : <?php echo $sData['phone1']; ?></b></td>
	</tr>
	<tr height="100px">
	<td><b>Class : <?php echo $sData['class'];?></b></td>
	<td><b>
	<?php
		if(empty($leftData))
			echo "Section : ".substr($sData['section'],-1);
		else{
			echo "Leaving Year : ".$sData['year'];
		}
	?>
	</b></td>
	</tr>
	<tr height="100px">
<td><b>Gender : <?php $gender=$sData['gender']=='M'?"Male":"Female"; echo $gender;?></b></td>
	<td><b>Acc. No : <?php echo substr($sData['account_number'],6);?></b></td>
	</tr>
	</tbody>
	</table>
  </td><td width="50%">
  <form method="post" action="<?php echo $_SESSION[$guid]["absoluteURL"] . "/modules/User Admin/process_left_students.php";?>">
  <table width="100%">
  <tbody>
  <tr>
	<td><b>Entry Date : </b></td>
	<td><input type="text" name="entryDate" id="entryDate" value="<?php echo dateformat($leftData['date_created']);?>"required></td>
	<script>
		$(function() {
			$("#entryDate").datepicker({ dateFormat: 'dd/mm/yy' });
		});
	</script>
  </tr>
  <tr>
	<td><b>End Date : </b></td>
	<td><input type="text" name="dateEnd" id="dateEnd" value="<?php echo dateformat($leftData['dateEnd']);?>" required></td>
	<script>
		$(function() {
			$("#dateEnd" ).datepicker({ dateFormat: 'dd/mm/yy' });
		});
	</script>
  </tr>
  <tr>
	<td><b>Year of Leaving : </b></td>
	 <td><select name="year_of_leaving" required>
		<?php foreach($schoolYear as $y){
			if(empty($leftData))
				$selected=$y['status']=='Current'?"selected":"";
			else
				$selected=intval($y['gibbonSchoolYearID'])==intval($leftData['yearOfLeaving'])?"selected":"";
			echo "<option value='{$y['gibbonSchoolYearID']}' $selected>{$y['name']}</option>";
		}
		?>
	</select></td>	
  </tr>
  <tr>
	<td><b>Reason :</b></td>
	<td><textarea name="reason" style="height: 6em;width: 100%;"><?php echo $leftData['leavingReason'];?></textarea></td>
  </tr>
  <tr>
    <td><b>Transfer Certificate :</b></td>
	<td><select name="hasTC" id="hasTC">
	<?php
		$selected1="";
		$selected2="selected";
		if(!empty($leftData)){
			$selected1=$leftData['hasTc']=='Y'?"selected":"";
			$selected2=$leftData['hasTc']=='N'?"selected":"";
		}
		echo "<option value='Y' $selected1>Yes</option>";
		echo "<option value='N' $selected2>No</option>";
	?>
	</select></td>
  </tr>
  <tr>
	<td><b>TC Number :</b></td>
	<td><input type="text" name="tcNumber" id="tcNumber" value="<?php if($leftData['TcNumber']>0){ echo $leftData['TcNumber'];}?>" <?php if($leftData['hasTc']!='Y'){echo "disabled";}?>></td>
  </tr>
  <tr>
	<td><b>TC Date :</b></td>
	<td><input type="text" name="tcDate" id="tcDate" value="<?php echo dateformat($leftData['dateOfTc']);?>" <?php if($leftData['hasTc']!='Y'){echo "disabled";}?>></td>
	<script>
		$(function() {
			$( "#tcDate" ).datepicker({ dateFormat: 'dd/mm/yy' });
		});
	</script>
  </tr>
    <tr>
	<td><b>Next School :</b></td>
	<td><input type="text" name="nextSchool" id="nextSchool" value="<?php echo $leftData['nextSchool'];?>"></td>
	<input type="hidden" name="preferredName" value="<?php echo $sData['officialName'];?>">
	<input type="hidden" name="gibbonPersonID" value="<?php echo $sData['gibbonPersonID'];?>">
  </tr>
  <tr>
	<td colspan='2'><center><input type="submit" name="submit" value="Submit"></center></td>
  </td></tr>
  </tbody>
  </table>
  </td></tr>
  </tbody>
  </table>
  </form>
<?php } 

?>
<input type="hidden" name="get_personID_from_accno_url" id="get_personID_from_accno_url" value="<?php echo $_SESSION[$guid]["absoluteURL"] . "/modules/User Admin/ajax_get_personid_by_accno.php";?>">

<?php
};
function dateFormat($date){
	$tmp=explode("-",$date);
	if($tmp[0]>0)
		return $tmp[2]."/".$tmp[1]."/".$tmp[0];
	else
		return "";
}
?>
