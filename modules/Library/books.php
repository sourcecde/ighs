<?php
@session_start() ;
//if (isActionAccessible($guid, $connection2, "/modules/Messenger/sendSMS.php")==FALSE) {
if (False) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else{
	try{
	$sql1="SELECT * FROM `lakshya_library_category`";
	$result1=$connection2->prepare($sql1);
	$result1->execute();
	$cat=$result1->fetchAll();
	}
	catch(PDOException $e) { 
	echo $e;
	}
	try{
	$sql1="SELECT * FROM `lakshya_library_status`";
	$result1=$connection2->prepare($sql1);
	$result1->execute();
	$stat=$result1->fetchAll();
	}
	catch(PDOException $e) { 
	echo $e;
	}
	$addBookURL=$_SESSION[$guid]["absoluteURL"] . "index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/add_book.php";
	$bookCopiesURL=$_SESSION[$guid]["absoluteURL"] . "index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/book_copies.php";
?>
<table cellpadding='0' cellspacing='0' border='0' width='100%'>
	<tr>
		<td>
			<select id='category'>
				<option value='all'>Select Category</option>
				<?php foreach($cat as $c) {
					echo "<option value='{$c['categoryID']}'>{$c['category']}</option>";
				}
				?>
				
			</select>
		</td>
		<td>
			<select id='statusFilter'>
				<option>Select Status</option>
				<option>All</option>
				<?php foreach($stat as $s) {
					echo "<option value='{$s['statusID']}'>{$s['status']}</option>";
				}
				?>
			</select>
		</td>
		<td style='width:50%'></td>
	</tr>
</table>
	<div style="margin-bottom: 10px; padding: 5px 20px;">
		<a href="<?=$addBookURL;?>"><button class="cButton" style="float: left">Add Book</button></a>
	</div>
<table width='100%' class='booksTable'>
<thead>
	<tr>
		<th>Title</th>
		<th>Author</th>
		<th>Publisher</th>
		<th>Category</th>
		<th>Available</th>
		<th>Publication Place</th>
		<th>ISBN</th>
		<th></th>
	</tr>
</thead>
<tbody>
</tbody>
</table>
<?php	
}	
 ?>
<script src="<?php echo $_SESSION[$guid]["absoluteURL"] ;?>modules/<?=$_SESSION[$guid]["module"];?>/js/jquery.dataTables.min.js"></script>
<script>
	$(document).ready(function(){
		var table=$('.booksTable').DataTable({
			 "aoColumnDefs": [
								  { 'bSortable': false, 'aTargets': [ 7 ] }
							   ],
			"iDisplayLength": 50,
			"oLanguage": {
			  "sLengthMenu": '<select>'+
				'<option value="50">50</option>'+
				'<option value="100">100</option>'+
				'<option value="200">200</option>'+
				'<option value="500">500</option>'+
				'<option value="-1">All</option>'+
				'</select>'
			},
            "processing": true,
            "serverSide": true,
            "ajax":{
                url :'<?=$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajax/book_data.php";?>',
				type: "post",
				"data": function(d) {
                    d.category = $('#category').val();
                }
			}
		});
		$('#category').change(function(){
			table.ajax.reload();
		});
		$('#statusFilter').change(function(){
			id=$(this).val();
			if(id=='All')
				window.location='<?=$bookCopiesURL?>';
			else
				window.location='<?=$bookCopiesURL?>&statusID='+id;
				
		});
	});
</script>