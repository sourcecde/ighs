<?php
	try{
	$sql1="SELECT * FROM `lakshya_library_publisher`";
	$result1=$connection2->prepare($sql1);
	$result1->execute();
	$publishers=$result1->fetchAll();
	}
	catch(PDOException $e) { 
	echo $e;
	}
	try{
	$sql2="SELECT * FROM `lakshya_library_category`";
	$result2=$connection2->prepare($sql2);
	$result2->execute();
	$categories=$result2->fetchAll();
	}
	catch(PDOException $e) { 
	echo $e;
	}
	try{
	$sql3="SELECT * FROM `lakshya_library_status`";
	$result3=$connection2->prepare($sql3);
	$result3->execute();
	$statuses=$result3->fetchAll();
	}
	catch(PDOException $e) { 
	echo $e;
	}
	try{
	$sql4="SELECT DISTINCT `author` FROM `lakshya_library_booknamemaster`";
	$result4=$connection2->prepare($sql4);
	$result4->execute();
	$authors=$result4->fetchAll();
	$availableAuthors=array();
	foreach($authors as $a){
		$availableAuthors[]=$a['author'];
	}
	}
	catch(PDOException $e) { 
	echo $e;
	}
	try{
	$sql5="SELECT DISTINCT `publicationPlace` FROM `lakshya_library_booknamemaster`";
	$result5=$connection2->prepare($sql5);
	$result5->execute();
	$places=$result5->fetchAll();
	$availablePlaces=array();
	foreach($places as $p){
		$availablePlaces[]=$p['publicationPlace'];
	}
	}
	catch(PDOException $e) { 
	echo $e;
	}

 ?>
<table width='100%'>
	<tr>
		<td><b>Title: </b></td>
		<td>
			<input type='text' name='title' id='title' style=" width:100%;">
			<br><br>
			<span>
				<select id='bookFinder' size='5' style=" width:100%; height: 100px; display: none;">
				</select>
			</span>
		</td>
		<td><b>Author: </b></td>
		<td><input type='text' name='author' id='author' style=" width:100%;"></td>
	</tr>
	<tr>
		<td><b>Publisher: </b><br><small  style='float: right'><a href='javascript:void(0)' id='addPub' style=' font-size: 12px;'>Add Publisher</a></small></td>
		<td>
			<select name="publisher" id="publisher" style=" width:100%;">
				<option value="">Select</option>
			<?php
				foreach($publishers as $p){
					echo "<option value='{$p['publisherID']}'>{$p['publisher']}</option>";
				}
			?>
			</select>
		</td>
		<td><b>Publication Place: </b></td>
		<td><input type='text' name='pub_place' id='pub_place' style=" width:100%;"></td>
	</tr>
	<tr>
		<td><b>Category: </b><br><small  style='float: right'><a href='javascript:void(0)' id='addCat' style=' font-size: 12px;'>Add Category</a></small></td>
		<td>
			<select name="category" id="category" style=" width:100%;">
				<option value="">Select</option>
			<?php
				foreach($categories as $c){
					echo "<option value='{$c['categoryID']}'>{$c['category']}</option>";
				}
			?>
			</select>
		</td>
		<td><b>ISBN: </b></td>
		<td><input type='text' name='isbn' id='isbn' style=" width:100%;"></td>
	</tr>
	<tr>
		<td><b>Acc No: </b></td>
		<td><input type='text' name='acc_no' id='acc_no' style=" width:100%;"></td>
		<td><b>Call No: </b></td>
		<td><input type='text' name='call_no' id='call_no' style=" width:100%;"></td>
	</tr>
	<tr>
		<td><b>Added Date: </b></td>
		<td><input type="text" name='date_added' id='date_added' style=" width:100%;"></td>
		<td><b>Year: </b></td>
		<td><input type='text' name='year' id='year' style=" width:100%;"></td>
	</tr>
	<tr>
		<td><b>Edition: </b></td>
		<td><input type='text' name='edition' id='edition' style=" width:100%;"></td>
		<td><b>Volume: </b></td>
		<td><input type='text' name='volume' id='volume' style=" width:100%;"></td>
	</tr>
	<tr>
		<td><b>Price: </b></td>
		<td><input type='text' name='price' id='price' style=" width:100%;"></td>
		<td><b>Currency: </b></td>
		<td><input type='text' name='currency' id='currency' style=" width:100%;"></td>
	</tr>
	<tr>
		<td><b>Page: </b></td>
		<td><input type='text' name='page' id='page' style=" width:100%;"></td>
		<td><b>Status: </b></td>
		<td>
			<select name="status" id="status" style=" width:100%;">
				<option value="">Select</option>
			<?php
				foreach($statuses as $s){
					echo "<option value='{$s['statusID']}'>{$s['status']}</option>";
				}
			?>
			</select>
		</td>
	</tr>
	<tr>
		<td><b>Remarks:</b></td>
		<td colspan='3'>
			<input type='text' name='remarks' id='remarks' style=" width:100%;">
		</td>
	</tr>
</table>
<input type='hidden' id='finderURL' value='<?=$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajax/book_finder.php"?>'>
<input type='hidden' id='modalAddURL' value='<?=$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajax/add_pub_cat.php"?>'>

<div id='hide_body'style='background-color :rgba(0,0,0, 0.7); width:100%; height:100%; position:fixed; left:0px; top:0px; z-index:100; display:none;'>
</div>

<div  id='modal_pub_add' class='modal' style="position:fixed; left:500px; top:250px; z-index:200; border:1px; padding:20px ; background-color :rgba(0,0,0, 0.6); color:white; width:300px; display:none;">
	<b>Publisher:</b>
	<input type='text' id='publisherAddName' class='modalInput' style='width:180px;'><br><br>
	<div style='text-align: center; padding: 20px;'>
		<span class="cButton" id='publisherAddSubmit' style="padding: 10px 20px;">Add</span>
		<span class='b_close cButton' style="padding: 10px 20px;">Close</span>
	</div>
</div>
<div  id='modal_cat_add' class='modal' style="position:fixed; left:500px; top:250px; z-index:200; border:1px; padding:20px ; background-color :rgba(0,0,0, 0.6); color:white; width:300px; display:none;">
	<b>Category:</b> 
	<input type='text' id='categoryAddName' class='modalInput' style='width:180px;'><br><br>
	<div style='text-align: center; padding: 20px;'>
		<span class="cButton" id='categoryAddSubmit' style="padding: 10px 20px;">Add</span>
		<span class='b_close cButton' style="padding: 10px 20px;">Close</span>
	</div>
</div>


<script>
	$( "#date_added" ).datepicker({ dateFormat: 'dd/mm/yy' });
	$('#title').keyup(function(){
		var url=$('#finderURL').val();
		var search=$(this).val();
		if(search!=''){
			$.ajax
	 		({
	 			type: "GET",
	 			url: url,
	 			data: { title: search},
	 			success: function(msg)
	 			{ 
					$('#bookFinder').empty().append(msg);
					$('#bookFinder').show();
					console.log(msg);
	 			}
	 		});
		}
		else
			$('#bookFinder').hide();
	});
	$('#bookFinder').click(function(){
	  var url='<?=$url?>&booknameid='+$(this).val();
	  window.location=url;
	});
  $(function() {
    var availableAuthors = <?php echo json_encode($availableAuthors);?>;
    $( "#author" ).autocomplete({
      source: availableAuthors
    });
	var availablePlaces = <?php echo json_encode($availablePlaces);?>;
    $( "#pub_place" ).autocomplete({
      source: availablePlaces
    });
	
  });
	var modalAddURL=$('#modalAddURL').val();
	function modalClose(){
		$('.modal').hide();
		$('#hide_body').fadeOut();
		$('.modalInput').val('');
	};
	$('.b_close').click(function(){
		modalClose();
	});
	$('#addPub').click(function(){
		$('#hide_body').show();
		$('#modal_pub_add').fadeIn();
	});
	$('#addCat').click(function(){
		$('#hide_body').show();
		$('#modal_cat_add').fadeIn();
	});
	$('#publisherAddSubmit').click(function(){
		var name=$('#publisherAddName').val();
		if(name!=''){
			$.ajax
			({
				type: "GET",
				url: modalAddURL,
				data: { action: 'publisher', name:name},
				success: function(msg)
				{ 
					$('#publisher').empty().append(msg);
					console.log(msg);
					modalClose();
				}
			});
		}
		else
			return;
	});
	$('#categoryAddSubmit').click(function(){
		var name=$('#categoryAddName').val();
		if(name!=''){
			$.ajax
			({
				type: "POST",
				url: modalAddURL,
				data: { action: 'category', name:name},
				success: function(msg)
				{ 
					$('#category').empty().append(msg);
					console.log(msg);
					modalClose();
				}
			});
		}
		else
			return;
	});

  </script>