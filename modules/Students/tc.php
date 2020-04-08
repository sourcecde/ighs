<?php
@session_start() ;
$gibbonPersonID=NULL ;
if (isset($_SESSION[$guid]["gibbonPersonID"])) {
	$gibbonPersonID=$_SESSION[$guid]["gibbonPersonID"] ;
}
$sql="SELECT * from gibbonschoolyear ORDER BY gibbonSchoolYearID DESC";
$result=$connection2->prepare($sql);
$result->execute();
$yearresult=$result->fetchAll();
foreach($yearresult as $value)
{
    if($value['name']==$_GET['year'])
    {
        $first_day=$value['firstDay'];
        $last_day=$value['lastDay'];
        $year_id=$value['gibbonSchoolYearID'];
        
    }
}

$student_id=$_GET['studentid'];
$character="Good";
$account_number=$_GET['account_number'];
$name=$_GET['name'];
$class=$_GET['class'];
$year=$_GET['year'];

$sql="SELECT * from left_student";
$result=$connection2->prepare($sql);
$result->execute();
$left_student=$result->fetchAll();
$ls="0";
foreach($left_student as $value)
{
    if($value['student_id']==$student_id)
    {
        $ls="1";
        $unique_id=$value['unique_id'];
        $promotion=$value['promotion'];
        
        $character=$value['s_character'];
        $sl_no=$value['sl_no'];
    }
}


$board="GEN";
$stream="GEN";
$sql0="SELECT * from gibbonperson Where gibbonPersonID = $student_id";
$result=$connection2->prepare($sql0);
$result->execute();
$enddate=$result->fetchAll();
foreach($enddate as $value)
{$dateEnd=$value['dateEnd'];
}
if($class=="09 Sci. with Comp.")
{
    $class="9";
    $stream="Sci. with Comp.";
    $board="ICSE";
}
else if($class=="09 Science")
{
    $class="9";
    $stream="Science";
    $board="ICSE";
}
else if($class=="09 Comm. with Comp.")
{
    $class="9";
    $stream="Comm. with Comp.";
    $board="ICSE";
}
else if($class=="10 Sci. with Comp.")
{
    $class="9";
    $stream="Sci. with Comp.";
    $board="ICSE";
}
else if($class=="10 Science")
{
    $class="10";
    $stream="Science";
    $board="ICSE";
}
else if($class=="10 Comm. with Comp.")
{
    $class="10";
    $stream="Comm. with Comp.";
    $board="ICSE";
}
else if($class=="11 Commerce")
{
    $class="11";
    $stream="Commerce";
    $board="ISC";
}
else if($class=="11 Humanities")
{
    $class="11";
    $stream="Humanities";
    $board="ISC";
}
else if($class=="11 Sci. with Comp.")
{
    $class="11";
    $stream="Sci. with Comp.";
    $board="ISC";
}
else if($class=="11 Science")
{
    $class="11";
    $stream="Science";
    $board="ISC";
}
else if($class=="11 Comm. with Comp.")
{
    $class="11";
    $stream="Comm. with Comp.";
    $board="ISC";
}
else if($class=="11 Humm. with Comp.")
{
    $class="11";
    $stream="Humm. with Comp.";
    $board="ISC";
}
else if($class=="12 Commerce")
{
    $class="12";
    $stream="Commerce";
    $board="ISC";
}
else if($class=="12 Humanities")
{
    $class="12";
    $stream="Humanities";
    $board="ISC";
}
else if($class=="12 Sci. with Comp.")
{
    $class="12";
    $stream="Sci. with Comp.";
    $board="ISC";
}
else if($class=="12 Science")
{
    $class="12";
    $stream="Science";
    $board="ISC";
}
else if($class=="12 Comm. with Comp.")
{
    $class="12";
    $stream="Comm. with Comp.";
    $board="ISC";
}
else if($class=="12 Humm. with Comp.")
{
    $class="12";
    $stream="Humm. with Comp.";
    $board="ISC";
}
	$sql="SELECT `gibbonYearGroupID`,`name` FROM `gibbonyeargroup`";
	$result=$connection2->prepare($sql);
	$result->execute();
	$classDB=$result->fetchAll();
	$sql1="SELECT `gibbonSchoolYearID`,`name`,`status` FROM `gibbonschoolyear`";
	$result1=$connection2->prepare($sql1);
	$result1->execute();
	$yearDB=$result1->fetchAll();
	$sql2="SELECT `gibbonfamily`.`homeAddress`,`gibbonfamily`.`homeAddressDistrict`,`gibbonfamily`.`homeAddressCountry`,`gibbonfamilychild`.`gibbonFamilyID`,`gibbonstudentenrolment`.*,`firstName`,`surname`,`gender`,`email`,`dob`,`category`,`countryOfBirth`,`religion`,`nationalIDCardNumber`,`lastSchool`,`annual_income`,`languageFirst`,`languageSecond`,`account_number`,`admission_number`,`bloodType`,`dateStart` FROM `gibbonperson`
			LEFT JOIN `gibbonstudentenrolment` ON `gibbonstudentenrolment`.`gibbonPersonID`=`gibbonperson`.`gibbonPersonID`
			LEFT JOIN `gibbonpersonmedical` ON `gibbonpersonmedical`.`gibbonPersonID`=`gibbonperson`.`gibbonPersonID`
			LEFT JOIN `gibbonfamilychild` ON `gibbonfamilychild`.`gibbonPersonID`=`gibbonperson`.`gibbonPersonID`
			LEFT JOIN `gibbonfamily` ON `gibbonfamily`.`gibbonFamilyID`=`gibbonfamilychild`.`gibbonFamilyID`
			WHERE `gibbonperson`.`gibbonPersonID`=".$student_id." AND `gibbonstudentenrolment`.`gibbonSchoolYearID`=".$year_id;
	$result2=$connection2->prepare($sql2);
	$result2->execute();
	$data=$result2->fetch();
	//print_r($data);
	$sql="SELECT `gibbonRollGroupID`,`name` FROM `gibbonrollgroup` WHERE `gibbonYearGroupID`=".$data["gibbonYearGroupID"]." AND `gibbonSchoolYearID`=".$year_id;
	$result=$connection2->prepare($sql);
	$result->execute();
	$sectionDB=$result->fetchAll();
				//Get relationships and prep array
		
				
			
			$sql2="Select * from gibbonfamilyadult where gibbonPersonID='$student_id' order by gibbonFamilyAdultID DESC";
	$result2=$connection2->prepare($sql2);
	$result2->execute();
	$adults=$result2->fetchAll();
	print_r($family);
?>
<h3>Transfer Certificate</h3>
<table width="100%" cellpadding="0" cellspacing="0">
<form method='POST' action=''>
<input type="hidden" name="student_id" id="student_id" value="<?php echo $student_id ?>">
<input type="hidden" name="sl_no" id="sl_no" value="<?php echo $sl_no ?>">
<input type="hidden" name="ls" id="ls" value="<?php echo $ls ?>">
	<tr>
		<td><b>Account Number:</b></td>
		<td><input type='text' id='s_acc_no' name='s_acc_no' value="<?php echo $account_number ?>" readonly="readonly" style="width:40%; text-align: left;" ></td>
	</tr>
	<tr>
		<td><b>Unique Id:</b></td>
		<td><input type='text' id='s_u_id' name='s_u_id' value="<?php echo $unique_id ?>" style="width:40%; text-align: left;" required></td>
	</tr>
	
	<tr>
		<td><b>Admission Number:</b></td>
		<td><input type='text' id='s_add_no' name='s_add_no' readonly="readonly" value="<?php echo $data["admission_number"]; ?>" style="width:40%; text-align: left;" ></td>
	</tr>
	<tr>
		<td><b>Date of Admission:</b></td>
		<td><input type='text' id='s_d_o_a' name='s_d_o_a' readonly="readonly" value="<?php echo dateformat($data["dateStart"]) ?>" style=" width:40%; text-align: left;"></td>
	</tr>
	<tr>
		<td><b>Previous School:</b></td>
		<td><input type='text' id='s_ps_name' name='s_ps_name'  value="<?php echo $data["lastSchool"]; ?>" style="width:40%; text-align: left;" ></td>
	</tr>
	<tr>
		<td><b>Student Name:</b></td>
		<td><input type='text' id='s_name' name='s_name' readonly="readonly" value="<?php echo $name ?>" style="width:40%; text-align: left;" ></td>
	</tr>
	<tr>
		<td><b>Left On:</b></td>
		<td><input type='text' id='s_left' name='s_left' readonly="readonly" value="<?php echo dateformat($dateEnd) ?>" style=" width:40%; text-align: left;"></td>
	</tr>
	<tr>
		<td><b>Gender:</b></td>
		<td><input type='text' id='s_gender' name='s_gender' readonly="readonly" value="<?php if($data["gender"]=="F")echo 'Female'; else echo 'Male'; ?>"style="width:40%; text-align: left;" ></td>
	</tr>
	<tr>
		<td><b>Date of Birth:</b></td>
		<td><input type='text' id='s_d_o_b' name='s_d_o_b' readonly="readonly" value="<?php echo dateformat($data["dob"]) ?>"style=" width:40%; text-align: left;"></td>
	</tr>
	<tr>
		<td><b>Father's Name:</b></td>
		<td><input type='text' id='f_name' name='f_name' readonly="readonly" value="<?php echo $adults[0]["officialName"] ?>"style="width:40%; text-align: left;" ></td>
	</tr>
	
	<tr>
		<td><b>Mother's Name:</b></td>
		<td><input type='text' id='m_name' name='m_name' readonly="readonly" value="<?php echo $adults[1]["officialName"] ?>"style="width:40%; text-align: left;" ></td>
	</tr>
	
	<tr>
		<td><b>Stream:</b></td>
		<td><input type='text' id='stream' name='stream' readonly="readonly" value="<?php echo $stream ?>"style="width:40%; text-align: left;" ></td>
	</tr>
	<tr>
		<td><b>Promotion:</b></td>
		<td><input type='text' id='promotion' name='promotion' value="<?php echo $promotion ?>" style="width:40%; text-align: left;" ></td>
	</tr>
	
	<tr>
		<td><b>Starting Date:</b></td>
		<td><input type='text' id='start_date' name='start_date' readonly="readonly" value="<?php echo dateformat($first_day) ?>" style=" width:40%; text-align: left;"></td>
	</tr>
	<tr>
		<td><b>End Date:</b></td>
		<td><input type='text' id='end_date' name='end_date' readonly="readonly" value="<?php echo dateformat($last_day) ?>" style=" width:40%; text-align: left;"></td>
	</tr>
	<tr>
		<td><b>Class:</b></td>
		<td>
			<input type='text' id='class' name='class' readonly="readonly" value="<?php echo $class ?>" style=" width:40%; text-align: left;"></td>
		</td>
	</tr>
	<tr>
		<td><b>Board:</b></td>
		<td><input type='text' id='board' name='board' readonly="readonly" value="<?php echo $board ?>" style="width:40%; text-align: left;" ></td>
	</tr>
	<tr>
		<td><b>Character:</b></td>
		<td><input type='text' id='character' name='character' value="<?php echo $character ?>" style="width:40%; text-align: left;" required></td>
	</tr>
	<tr>
		<td><b>Year:</b></td>
		<td>
			 <input type='text' id='year' name='year' readonly="readonly" value="<?php echo $year ?>" style=" width:40%; text-align: left;"></td>
		</td>
	</tr>
	<tr>
		<td><b>Date:</b></td>
		<td><input type='text' id='date' name='date' value='<?php echo date('d/m/Y');?>' style=" width:40%; text-align: left;"></td>
	</tr>
	<tr>
		<td><b>Countersigned:</b></td>
		<td><select type='text' id='countersigned' name='countersigned' style=" width:40%; text-align: left;">
		    <option>Yes</option>
		    <option>No</option>
		</select></td>
	</tr>
	<tr>
		<td colspan=2>
		    <center>
		        <input type="submit" name='print_tc' value="Print Transfer Certificate" style="padding:5px 30px;">
		        <?php if($class=='10'||$class=='12'){ ?>
		        <input type="submit" name='print_c' value="Print Character Certificate" style="padding:5px 30px;">
		        <input type="submit" name='print_m' value="Print Migration Certificate" style="padding:5px 30px;">
		        <?php }?>
		    </center>
	    </td>
	</tr>
</form>	
</table>
<script type="text/javascript">
		$(function() {
			
		
			$( "#s_left" ).datepicker({ dateFormat: 'dd/mm/yy' });
			
		});
</script>
<?php 
if(isset($_POST['print_tc']))
{
    $sl_no=$_POST['sl_no'];
    $unique_id=$_POST['s_u_id'];
    $promotion=$_POST['promotion'];
    
    $character=$_POST['character'];
    $student_id=$_POST['student_id'];
    $date=$_POST['date'];
    
if($_POST["ls"]=="0")
{
    $sql="insert into left_student (unique_id, promotion, s_character, student_id, date) Values('$unique_id', '$promotion', '$character', '$student_id', '$date')";
$result=$connection2->prepare($sql);
$result->execute();
$sql="select * from left_student";
$result=$connection2->prepare($sql);
$result->execute();
$left_student1=$result->fetchAll();
foreach($left_student1 as $value)
{
    if($value['student_id']==$student_id)
    {
        $sl_no=$value['sl_no'];
        
    }
}
}
?>

<script>
$(document).ready(function(){
	var w=window.open("","","height=1200,width=1400,status=yes,toolbar=no,menubar=no,location=no");
	var html='<br><br><br><br><br><br><br><br><div style="float: left; margin-left:30px; font-size:16px;">Sl. No. <b>TC/<?php echo $_POST['board']."/".substr($year, -2)."/".$sl_no; ?></b> </div> \
					<div style="float: right; margin-right:30px; font-size:16px;">Admin. No. <b><?php echo $_POST['s_add_no']?> </b> </div> \
					<br> \
					<p style="font-family:Arial, Helvetica, sans-serif; font-size:16px; color:#000000;"> \
					This is to certify that <?php if($_POST['gender']=='Male'){echo 'Master';}else{echo 'Miss';}?>: <b> <?php echo $_POST['s_name']?></b>, <?php if($_POST['gender']=='Male'){echo 'Son';}else{echo 'Daughter';}?> of: <b> <?php echo $_POST['f_name'];?></b> was admitted into this school on <b><?php echo $_POST['s_d_o_a'] ?></b> on transfer from <b><?php echo $_POST['s_ps_name'] ?></b> and left on <b><?php echo $_POST['s_left'] ?></b> with a <b><?php echo $_POST['character'] ?></b> character. <br><br> <?php if($_POST['class']=="10"||$_POST['class']=="12") { if($_POST['gender']=='Male'){echo 'He';}else{echo 'She';} echo " appeared at the ".$_POST["board"]." Examination March ".substr($year, -4)." bearing Unique ID No. ".$_POST['s_u_id'].".<br><br>"; }?><?php if($_POST['gender']=='Male'){echo 'He';}else{echo 'She';}?> was then studying in the <b><?php echo ucwords(convert_number_to_words(intval($_POST['class']))) ?></b> Class of <b><?php echo $_POST['board'] ?></b> stream, the school year being from <b><?php echo date("F Y", strtotime(dateformat2($_POST['start_date'])))." to ".date("F Y", strtotime(dateformat2($_POST['end_date']))); ?></b>. <br><br> All sums due to this school on <?php if($_POST['gender']=='Male'){echo 'his';}else{echo 'her';}?> account have been remitted or satisfactorily arranged for. <br><br> <?php if($_POST['gender']=='Male'){echo 'His';}else{echo 'Her';}?> date of birth, according to Admission Register, is (in figures) <b><?php echo $_POST['s_d_o_b'] ?></b> (in words) <b><?php echo dateformat3($_POST['s_d_o_b']) ?></b>. <br> <br> <?php if($_POST['class']=="10"||$_POST['class']=="12") {  if($_POST['gender']=='Male'){echo 'He';}else{echo 'She';} echo " passed the ".$_POST["board"]." Examination March ".substr($year, -4); } else { ?> Promotion has been <b><?php echo $_POST['promotion'] ?></b><?php } ?>.</p> \
					<br><br> \
					<div style="float: left; margin-left:30px; font-size:16px;">Date: <b><?php echo $_POST['date'];?></b> </div> \
					<div style="float: right; margin-right:30px; font-size:16px;"> Signature Principle</div> \
					<br><br><br> \
					<div style="float: left; margin-left:30px; font-size:16px;"><?php if($_POST['countersigned']=='Yes'){?><b>COUNTERSIGNED</b><?php } ?> </div> \
					<div style="float: right; margin-right:30px; font-size:16px;"> School Seal</div> \
					<br><br><br><br><br><br><br><br><br><br><br><br><br><br><?php if($_POST['class']!="10"&&$_POST['class']!="12") echo "<br><br><br>" ?> \
<div style="float: left; margin-left:30px; font-size:12px;">Sl. No. <b>TC/<?php echo $_POST['board']."/".substr($year, -2)."/".$sl_no; ?></b> </div> \
					<div style="float: right; margin-right:30px; font-size:16px;">Admin. No. <b><?php echo $_POST['s_add_no']?> </b> </div> \
					<br> \
					<p style="font-family:Arial, Helvetica, sans-serif; font-size:16px; color:#000000;"> \
					This is to certify that <?php if($_POST['gender']=='Male'){echo 'Master';}else{echo 'Miss';}?>: <b> <?php echo $_POST['s_name']?></b>, <?php if($_POST['gender']=='Male'){echo 'Son';}else{echo 'Daughter';}?> of: <b> <?php echo $_POST['f_name'];?></b> was admitted into this school on <b><?php echo $_POST['s_d_o_a'] ?></b> on transfer from <b><?php echo $_POST['s_ps_name'] ?></b> and left on <b><?php echo $_POST['s_left'] ?></b> with a <b><?php echo $_POST['character'] ?></b> character. <br><br> <?php if($_POST['class']=="10"||$_POST['class']=="12") { if($_POST['gender']=='Male'){echo 'He';}else{echo 'She';} echo " appeared at the ".$_POST["board"]." Examination March ".substr($year, -4)." bearing Unique ID No. ".$_POST['s_u_id'].".<br><br>"; }?><?php if($_POST['gender']=='Male'){echo 'He';}else{echo 'She';}?> was then studying in the <b><?php echo ucwords(convert_number_to_words(intval($_POST['class']))) ?></b> Class of <b><?php echo $_POST['board'] ?></b> stream, the school year being from <b><?php echo date("F Y", strtotime(dateformat2($_POST['start_date'])))." to ".date("F Y", strtotime(dateformat2($_POST['end_date']))); ?></b>. <br><br> All sums due to this school on <?php if($_POST['gender']=='Male'){echo 'his';}else{echo 'her';}?> account have been remitted or satisfactorily arranged for. <br><br> <?php if($_POST['gender']=='Male'){echo 'His';}else{echo 'Her';}?> date of birth, according to Admission Register, is (in figures) <b><?php echo $_POST['s_d_o_b'] ?></b> (in words) <b><?php echo dateformat3($_POST['s_d_o_b']) ?></b>. <br> <br> <?php if($_POST['class']=="10"||$_POST['class']=="12") {  if($_POST['gender']=='Male'){echo 'He';}else{echo 'She';} echo " passed the ".$_POST["board"]." Examination March ".substr($year, -4); } else { ?> Promotion has been <b><?php echo $_POST['promotion'] ?></b><?php } ?>.</p> \
					<br><br> \
					<div style="float: left; margin-left:30px; font-size:16px;">Date: <b><?php echo $_POST['date'];?></b> </div> \
					<div style="float: right; margin-right:30px; font-size:16px;"> Signature Principle</div> \
					<br><br><br> \
					<div style="float: left; margin-left:30px; font-size:16px;"><?php if($_POST['countersigned']=='Yes'){?><b>COUNTERSIGNED</b><?php } ?> </div> \
					<div style="float: right; margin-right:30px; font-size:16px;"> School Seal</div> \
					';
				  
	$(w.document.body).html(html);
	w.print();
})

</script>
<?php	
}
if(isset($_POST['print_c']))
{
    $sl_no=$_POST['sl_no'];
    $unique_id=$_POST['s_u_id'];
    $promotion=$_POST['promotion'];
   
    $character=$_POST['character'];
    $student_id=$_POST['student_id'];
    
if($_POST["ls"]=="0")
{
    $sql="insert into left_student (unique_id, promotion, s_character, student_id) Values('$unique_id', '$promotion', '$character', '$student_id')";
$result=$connection2->prepare($sql);
$result->execute();
$sql="select * from left_student";
$result=$connection2->prepare($sql);
$result->execute();
$left_student1=$result->fetchAll();
foreach($left_student1 as $value)
{
    if($value['student_id']==$student_id)
    {
        $sl_no=$value['sl_no'];
        
    }
}
}
?>
<script>
$(document).ready(function(){
	var w=window.open("","","height=1200,width=1400,status=yes,toolbar=no,menubar=no,location=no");
	var html='<br><br><br><br><br><br><br><br><div style="float: left; margin-left:30px; font-size:20px;">Sl. No. <b>CC/<?php echo $_POST['board']."/".substr($year, -2)."/".$sl_no; ?></b> </div> \
					<div style="float: right; margin-right:30px; font-size:20px;">Admin. No. <b><?php echo $_POST['s_add_no']?> </b> </div> \
					<br> \
					<p style="font-family:Arial, Helvetica, sans-serif; font-size:20px; color:#000000;"> \
					This is to certify that <?php if($_POST['gender']=='Male'){echo 'Master';}else{echo 'Miss';}?>: <b> <?php echo $_POST['s_name']?></b>, <?php if($_POST['gender']=='Male'){echo 'Son';}else{echo 'Daughter';}?>  of: <b> <?php echo $_POST['f_name'];?></b> has been a bonafide student of this school. <br><br> <?php if($_POST['class']=="10"||$_POST['class']=="12") { if($_POST['gender']=='Male'){echo 'He';}else{echo 'She';}echo " appeared at the ".$_POST["board"]." Examination March ".substr($year, -4)." bearing Unique ID No. ".$_POST['s_u_id']."  and has been awarded Pass Certificate.<br><br>"; }?> <?php if($_POST['gender']=='Male'){echo 'His';}else{echo 'Her';}?> date of birth, according to Admission Register, is (in figures) <b><?php echo $_POST['s_d_o_b'] ?></b> (in words) <b><?php echo dateformat3($_POST['s_d_o_b']) ?></b>. <br> <br> <?php if($_POST['gender']=='Male'){echo 'He';}else{echo 'She';}?> bears a <b><?php echo $_POST['character'] ?></b> moral character. We wish <?php if($_POST['gender']=='Male'){echo 'him';}else{echo 'her';}?> every success in life.</p> \
					<br><br><br> \
					<div style="float: left; margin-left:30px; font-size:20px;">Date: <b><?php echo $_POST['date'];?></b> </div> \
					<div style="float: right; margin-right:30px; font-size:20px;"> Signature Principle</div> \
					<br><br> \
					<div style="float: right; margin-right:30px; font-size:20px;"> School Seal</div> \
					<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br> \
<div style="float: left; margin-left:30px; font-size:20px;">Sl. No. <b>CC/<?php echo $_POST['board']."/".substr($year, -2)."/".$sl_no; ?></b> </div> \
					<div style="float: right; margin-right:30px; font-size:20px;">Admin. No. <b><?php echo $_POST['s_add_no']?> </b> </div> \
					<br> \
					<p style="font-family:Arial, Helvetica, sans-serif; font-size:20px; color:#000000;"> \
					This is to certify that <?php if($_POST['gender']=='Male'){echo 'Master';}else{echo 'Miss';}?>: <b> <?php echo $_POST['s_name']?></b>, <?php if($_POST['gender']=='Male'){echo 'Son';}else{echo 'Daughter';}?>  of: <b> <?php echo $_POST['f_name'];?></b> has been a bonafide student of this school. <br><br><?php if($_POST['class']=="10"||$_POST['class']=="12") { if($_POST['gender']=='Male'){echo 'He';}else{echo 'She';}echo " appeared at the ".$_POST["board"]." Examination March ".substr($year, -4)." bearing Unique ID No. ".$_POST['s_u_id']."  and has been awarded Pass Certificate.<br><br>"; }?> <?php if($_POST['gender']=='Male'){echo 'His';}else{echo 'Her';}?> date of birth, according to Admission Register, is (in figures) <b><?php echo $_POST['s_d_o_b'] ?></b> (in words) <b><?php echo dateformat3($_POST['s_d_o_b']) ?></b>. <br> <br> <?php if($_POST['gender']=='Male'){echo 'He';}else{echo 'She';}?> bears a <b><?php echo $_POST['character'] ?></b> moral character. We wish <?php if($_POST['gender']=='Male'){echo 'him';}else{echo 'her';}?> every success in life.</p> \
					<br><br><br> \
					<div style="float: left; margin-left:30px; font-size:20px;">Date: <b><?php echo $_POST['date'];?></b> </div> \
					<div style="float: right; margin-right:30px; font-size:20px;"> Signature Principle</div> \
					<br><br> \
					<div style="float: right; margin-right:30px; font-size:20px;"> School Seal</div> \
					';
				  
	$(w.document.body).html(html);
	w.print();
})

</script>
<?php	
}
if(isset($_POST['print_m']))
{
    $sl_no=$_POST['sl_no'];
    $unique_id=$_POST['s_u_id'];
    $promotion=$_POST['promotion'];
   
    $character=$_POST['character'];
    $student_id=$_POST['student_id'];
    
if($_POST["ls"]=="0")
{
    $sql="insert into left_student (unique_id, promotion, s_character, student_id) Values('$unique_id', '$promotion', '$character', '$student_id')";
$result=$connection2->prepare($sql);
$result->execute();
$sql="select * from left_student";
$result=$connection2->prepare($sql);
$result->execute();
$left_student1=$result->fetchAll();
foreach($left_student1 as $value)
{
    if($value['student_id']==$student_id)
    {
        $sl_no=$value['sl_no'];
        
    }
}
}
?>
<script>
$(document).ready(function(){
	var w=window.open("","","height=576px,width=787px,status=yes,toolbar=no,menubar=no,location=no");
	var html='<html> \
	            <head> \
	                <style> \
	                    body { \
                            width: 787px; \
                            height: 576px ; \
                        } \
                    </style> \
                </head> \
                <body> \
                    <div style="font-size:18px"> \
                        <br><br><br><br><br><br><br><br> \
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; \
                        <b style="line-height:3.7em"><?php echo $_POST["s_name"] ?></b> <br> \
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; \
                        <b><?php echo "CALCUTTA PUBLIC SCHOOL" ?></b><br> \
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; \
                        <b style="line-height:4.6em"><?php echo $_POST["s_u_id"] ?></b> <br> \
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><?php if($_POST['class']=='10'){echo "INDIAN CERTIFICATE SCHOOL EXAMINATION";} else if($_POST['class']=='12'){echo "INDIAN SCHOOL CERTIFICATE EXAMINATION";} ?></b> <br><br><br><br><br><br><br><br><br><br><br><br><br><br>\
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b style="line-height:1.6em"><?php echo $_POST["f_name"] ?> <br> \
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><?php echo $_POST["m_name"] ?> <br><br> \
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; \
                            <b><?php echo $_POST["date"] ?> <br> \
                    </div> \
                </body> \
            </html> \
					';
				  
	$(w.document.body).html(html);
	w.print();
})

</script>
<?php	
}
?>
<?php
function dateformat($a){
$date=explode("-",$a);
return $date[2]."/".$date[1]."/".$date[0];	
}
function dateformat2($a){
    $date=explode("/",$a);
    return $date[2]."-".$date[1]."-".$date[0];
}
function dateformat3($a){
    $date=explode("/",$a);
    return ucwords(convert_number_to_words1(intval($date[0])))." ".date("F", strtotime(dateformat2($a)))." ".ucwords(convert_number_to_words(intval($date[2])));
}
?>
