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
 ?>
<h3>Select Student :</h3>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td align="center">	    
		  <select name="schoolYear" id="schoolYear">
		  <?php foreach ($schoolyear as $value) {
			$s=$value['status']=='Current'?'selected':'';
		  ?>
		  <option value="<?php echo $value['gibbonSchoolYearID']?>" <?=$s?> ><?php echo $value['name']." (".$value['status']." year)"?></option>
		  <?php } ?>
		  </select>
	</td>
	<td>
		 <select name="personID" id="personID" style="float: left;">
			<option value=""> - Select Student - </option>
			<?php foreach ($students as $s) { 
				$ac_no=$s['account_number']+0;
				$pID=$s['gibbonPersonID']+0;
				echo "<option value='$pID'>{$s['preferredName']} ( $ac_no )</option>";
			} ?>
		 </select>
		 <button name="selectStudent" id="selectStudent" style="float: right;" class="cButton">Go</button>
		<input type="text" name="account_number" id="account_number" style="float: right;" placeholder="Account Number">
		
	</td>
	<td>
		<button name="fetchBorrowData" id="fetchBorrowData" style="float: right;" class="cButton">Select</button>
	</td>
  </tr>
	<tr id='detail_panel' style='display: none'>
		<td colspan='3' style="border: 2px solid #7030a0;">
			<div>
				<b>Name: </b><span id='s_name' style='padding: 20px'></span> | 
				<b>Class: </b><span id='s_class' style='padding: 20px'></span> | 
				<b>Roll: </b><span id='s_roll' style='padding: 20px'></span> | 
				<b>Account No: </b><span id='s_accno' style='padding: 20px'></span><b>
			</div>
		</td>
	</tr>
</table>
<table width='100%' id='borrow_panel' style='display: none'>
<tr>
	<td><b>Acc No</b></td>
	<td><b>Title</b></td>
	<td><b>Author</b></td>
	<td><b>Borrow Date</b></td>
	<td><b>Due Date</b></td>
</tr>
<tbody id='borrow_details'>
	
</tbody>
</table>
<h3>Select Book :</h3>
<table width='100%'>
<tr>
	<td>
		<b>Acc No:</b> <input type='text' id='f_accNo'><br><br>
		<select id='search_accNo' class='search_hint'  size="3" style='width:100%; height: 50px;'>
		</select>
	</td>
	<td>
		<b>Title:</b> <input type='text' id='f_title'><br><br>
		<select id='search_title' class='search_hint'  size="3" style='width:100%; height: 50px;'>
		</select>
	</td>
	<td>
		<b>Author:</b> <input type='text' id='f_author'><br><br>
		<select id='search_author' class='search_hint' size="3" style='width:100%; height: 50px;'>
		</select>
	</td>
</tr>
</table>
<table width='100%'>
<tr>
	<td><b>Acc No</b></td>
	<td><b>Title</b></td>
	<td><b>Author</b></td>
	<td><b>Available</b></td>
	<td></td>
</tr>
<tbody id='books_result'>
	
</tbody>
</table>
<table width="100%">
<tr>
	<td>Borrow Date: <input type='text' id='dateBorrow' value='<?=date('d/m/y')?>'></td>
	<td>Due Date: <input type='text' id='dateDue'></td>
	<td><button id='submitBorrow' class='cButton'>Borrow</button></td>
</tr>
</table>
<input type='hidden' id='personDetailsURL' value='<?=$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajax/getPersonDetails.php"?>'>
<input type='hidden' id='bookSearchURL' value='<?=$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajax/book_searchT.php"?>'>

<script>
	var personDetailsURL=$('#personDetailsURL').val();
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
	$('#fetchBorrowData').click(function(){
		var yearID=$('#schoolYear').val();
		var personID=$('#personID').val();
		if(personID!=""){
			fetchStudentData(yearID,personID);
			fetchBorrowData(yearID,personID);
		}
	});
	$('#f_accNo').keyup(function(){
		var key=$(this).val();
		searchHint(key,'search_accNo','accNo');
	});
	$('#f_title').keyup(function(){
		var key=$(this).val();
		searchHint(key,'search_title','title');
	});
	$('#f_author').keyup(function(){
		var key=$(this).val();
		searchHint(key,'search_author','author');
	});
	$('.search_hint').click(function(){
		var key=$(this).val();
		var id=$(this).attr('id');
		if(key!=null){
			$.ajax
			({
				type: "POST",
				url: bookSearchURL,
				data: { action:'getBooksResult',key:key,id:id},
				success: function(msg)
				{ 
					//console.log(msg);
					$('#books_result').html(msg);
				}
			});
		}
		else
			$('#books_result').html('');
	});
	$('#submitBorrow').click(function(){
		var bookID=$('#selectedBookID').val();
		var personID=$('#personID').val();
		var dateBorrow=$('#dateBorrow').val();
		var dateDue=$('#dateDue').val();
		var yearID=$('#schoolYear').val();
		if(personID==''){
			alert("Please select a student.");
			$('#personID').focus();
			return;
		}
		else if(dateBorrow==''){
			alert("Please select due date.");
			$('#dateBorrow').focus();
			return;
		}
		else if(dateDue==''){
			alert("Please select due date.");
			$('#dateDue').focus();
			return;
		}
		if(bookID){
			$.ajax
			({
				type: "POST",
				url: bookSearchURL,
				data: { action:'borrowBook',personID:personID,bookID:bookID,dateBorrow:dateBorrow,dateDue:dateDue,yearID:yearID},
				success: function(msg)
				{ 
					console.log(msg);
					alert('Borrowed successfully!');
					$('#books_result').html('');
					fetchBorrowData(yearID,personID);
				}
			});
		}
	});
	function fetchStudentData(yearID,personID){
		$.ajax
		({
			type: "POST",
			url: personDetailsURL,
			data: { action:'fetchStudentData',yearID:yearID,personID:personID},
			success: function(msg)
			{ 
				console.log(msg);
				var msg=$.parseJSON(msg);
				$('#s_name').html(msg['preferredName']);
				$('#s_class').html(msg['class']);
				$('#s_roll').html(msg['rollOrder']);
				$('#s_accno').html(parseInt(msg['account_number'])+0);
				$('#detail_panel').show();
			}
		});
	};
	function fetchBorrowData(yearID,personID){
		$.ajax
		({
			type: "POST",
			url: personDetailsURL,
			data: { action:'fetchBorrowData',personID:personID,yearID:yearID},
			success: function(msg)
			{ 
				console.log(msg);
				$('#borrow_details').html(msg);
				$('#borrow_panel').show();
			}
		});
	};
	function searchHint(key,id,type){
		$.ajax
		({
			type: "POST",
			url: bookSearchURL,
			data: { action:'getHint',key:key,type:type},
			success: function(msg)
			{ 
				console.log(msg);
				$('#'+id).empty().append(msg);
			}
		});
	};
	$('#dateBorrow').datepicker({ dateFormat: 'dd/mm/yy' });
	$('#dateDue').datepicker({ dateFormat: 'dd/mm/yy' });
</script>