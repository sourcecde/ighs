<?php
try{
	$sql="SELECT `gibbonPersonID`,`preferredName`,`account_number` FROM `gibbonperson` WHERE `gibbonPersonID` IN(SELECT DISTINCT `gibbonPersonID` FROM `gibbonstudentenrolment`) ORDER BY `preferredName`";
	$result=$connection2->prepare($sql);
	$result->execute();
	$students=$result->fetchAll();
}
catch(PDOException $e) { 
	echo $e;
}
try{
	$sql="SELECT * FROM `gibbonschoolyear`";
	$result=$connection2->prepare($sql);
	$result->execute();
	$schoolyear=$result->fetchAll();
}
catch(PDOException $e) { 
	echo $e;
}
try{
	$sql="SELECT `gibbonRollGroupID`,`name` FROM `gibbonrollgroup` WHERE `gibbonSchoolYearID`=(SELECT `gibbonSchoolYearID` FROM `gibbonschoolyear` WHERE `status`='Current')";
	$result=$connection2->prepare($sql);
	$result->execute();
	$sections=$result->fetchAll();
}
catch(PDOException $e) { 
	echo $e;
}
 ?>
<?php
if($_POST){
	//print_r($_POST);
	extract($_POST);
	if($section!=''){
		try{
			$sql="SELECT `gibbonRollGroupID`,`name` FROM `gibbonrollgroup` WHERE `gibbonSchoolYearID`=$schoolYear";
			$result=$connection2->prepare($sql);
			$result->execute();
			$sections=$result->fetchAll();
		}
		catch(PDOException $e) { 
			echo $e;
		}
	}
}
 ?>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<form action='' method='POST'>
  <tr>
    <td>	    
		  <select name="schoolYear" id="schoolYear" style="float: left;">
			  <?php foreach ($schoolyear as $value) {
				if($_POST){
					if($section!='')
						$s=$value['gibbonSchoolYearID']==$schoolYear?'selected':'';
					else
						$s=$value['status']=='Current'?'selected':'';
				}
				else
					$s=$value['status']=='Current'?'selected':'';
				echo "<option value='{$value['gibbonSchoolYearID']}' $s>{$value['name']} ({$value['status']})</option>";
				}
			  ?>
		  </select>
		  <select name="section" id="section" style="float: right;">
			<option value="">Select Section</option>
			<?php
				foreach($sections as $s){
					$sl="";
					if($_POST){
						if($section!='')
							$sl=$s['gibbonRollGroupID']==$section?'selected':'';
					}
					echo "<option value='{$s['gibbonRollGroupID']}' $sl>{$s['name']}</option>";
				}
			?>
		  </select>
	</td>
	<td>
		 <select name="personID" id="personID" style="float: left;">
			<option value=""> - Select Student - </option>
			<?php foreach ($students as $s) { 
				$ac_no=$s['account_number']+0;
				$pID=$s['gibbonPersonID']+0;
				$sl="";
				if($_POST){
					if($personID!='')
						$sl=$pID==$personID?'selected':'';
				}
				echo "<option value='$pID' $sl>{$s['preferredName']} ( $ac_no )</option>";
			} ?>
		 </select>
		 <span name="selectStudent" id="selectStudent"  class="cButton" style="float: right; text-align: center; min-width: 30px;">Go</span>
		<input type="text" name="account_number" id="account_number" style="float: right; width: 80px;" placeholder="Account No">
		
	</td>
	<td>
		<button name="fetchBorrower" id="fetchBorrower" style="float: right;" class="cButton">Search</button>
	</td>
  </tr>
 </form>
</table>
<?php
		try{
		$sql="SELECT `borrowID`,`dateBorrow`,`dateDue`,`dateReturn`,`lakshya_library_bookmaster`.`bookID`,`lakshya_library_bookmaster`.`acc_no`,`lakshya_library_booknamemaster`.`title`,`lakshya_library_booknamemaster`.`author`,   
				`gibbonperson`.`preferredName`,`gibbonperson`.`account_number`,`gibbonstudentenrolment`.`rollOrder`,`gibbonrollgroup`.`name` AS section	
				FROM `lakshya_library_borrowmaster` 
				LEFT JOIN `lakshya_library_bookmaster` ON `lakshya_library_bookmaster`.`bookID`=`lakshya_library_borrowmaster`.`bookID`
				LEFT JOIN `lakshya_library_booknamemaster` ON `lakshya_library_booknamemaster`.`bookNameID`=`lakshya_library_bookmaster`.`bookNameID`
				LEFT JOIN `gibbonstudentenrolment` ON `lakshya_library_borrowmaster`.`studentID`=`gibbonstudentenrolment`.`gibbonStudentEnrolmentID`
				LEFT JOIN `gibbonperson` ON `gibbonperson`.`gibbonPersonID`=`gibbonstudentenrolment`.`gibbonPersonID`
				LEFT JOIN `gibbonrollgroup` ON `gibbonrollgroup`.`gibbonRollGroupID`=`gibbonstudentenrolment`.`gibbonRollGroupID`
				WHERE `borrowStatus`='Returned'";
		if($_POST){
			if($section!='')
				$sql.=" AND `gibbonstudentenrolment`.`gibbonRollGroupID`=$section ";
			else if($personID!='')
				$sql.=" AND `gibbonperson`.`gibbonPersonID`=$personID ";
		}
		$result=$connection2->prepare($sql);
		$result->execute();
		$data=$result->fetchAll();
		}
		catch(PDOException $e) {
		  echo $e->getMessage();
		}
 ?>

 <table width='100%'>
 <tr>
	<th>Acc No.</th>
	<th>Book Details</th>
	<th>Student Details</th>
	<th>Borrow Date</th>
	<th>Due Date</th>
	<th>Returned Date</th>
 </tr>
 <?php
	if(!empty($data)){
		foreach($data as $d){
			echo "<tr>";
				echo "<td>{$d['acc_no']}</td>";
				echo "<td>{$d['title']}<br><span style='float: right'><b>Author: </b>{$d['author']}</span></td>";
				echo "<td>{$d['preferredName']}&nbsp; &nbsp; ({$d['account_number']})<br><span style='float: left'><b>Class:</b> {$d['section']}</span><span style='float: right'><b>Roll:</b> {$d['rollOrder']}</span></td>";
					$dB=dateFormatR($d['dateBorrow']);
					$dD=dateFormatR($d['dateDue']);
					$dR=dateFormatR($d['dateReturn']);
				echo "<td>$dB</td>";
				echo "<td>$dD</td>";
				echo "<td>$dR</td>";
			echo "</tr>";
		}
	}
	else
		echo "<tr><td colspan='6'><h3>No result found.</h3></td></tr>";
 ?>
 </table>
 <div id='hideBody' style='position: fixed; z-axis: 100; left:0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,.8); display: none;'>
 </div>
 <div id='returnPanel' class="cModal" style='position: fixed; z-axis: 150; left:35%; top: 35%; width: 300px; color: #fff; background:#000; padding: 20px 50px; display: none;'>
	<input type='hidden' id='rID'>
	<b>Return Date: </b><input type='text' id='returnDate' value='<?=date('d/m/Y')?>'><br><br><br>
	<button class='cButton' id='cReturnB'>Return</button>
	<button class='cButton closeM' style='float: right;'>Cancel</button>
 </div>
 <?php
function dateFormatR($oDate){
	$tmp=explode('-',$oDate);
	return $tmp[2]."/".$tmp[1]."/".$tmp[0];
}
 ?>
 <input type='hidden' id='personDetailsURL' value='<?=$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajax/getPersonDetails.php"?>'>
 <input type='hidden' id='sectionChaneURL' value='<?=$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajax/change_section.php"?>'>
<script>
 var personDetailsURL=$('#personDetailsURL').val();
 var sectionChaneURL=$('#sectionChaneURL').val();
 var bookSearchURL=$('#bookSearchURL').val();
	$('#selectStudent').click(function(){
		var account_number=$('#account_number').val();
		if(account_number!=''){
			$.ajax
			({
				type: "POST",
				url: personDetailsURL,
				data: { action:'getPersonIDbyAccountNo',account_number:account_number},
				success: function(msg)
				{ 
					$('#personID option[value="'+msg+'"]').prop('selected', true);
					console.log(msg);
				}
			});
		}
		else
			return;
	});
	$('#schoolYear').change(function(){
		var yearID=$(this).val();
		$.ajax({
			type: 'POST',
			url: sectionChaneURL,
			data: {action: 'changeSection',yearID:yearID},
			success: function(msg){
				console.log(msg);
				$('#section').empty().append(msg);
			}
		});
	});
 </script>