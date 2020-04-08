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
	$bookNameID=$_REQUEST['bookNameID'];
	try{
	$sql1="SELECT `lakshya_library_booknamemaster`.*, COUNT(`bookID`) AS copy, SUM(`borrow`) AS borrowed, SUM(`lakshya_library_status`.`available`) as available ,`category`,`publisher` 
			FROM `lakshya_library_booknamemaster`  
			LEFT JOIN `lakshya_library_publisher` ON `lakshya_library_publisher`.`publisherID`=`lakshya_library_booknamemaster`.`publisherID` 
			LEFT JOIN `lakshya_library_category` ON `lakshya_library_category`.`categoryID`=`lakshya_library_booknamemaster`.`categoryID` 
			LEFT JOIN `lakshya_library_bookmaster`ON `lakshya_library_bookmaster`.`bookNameID`=`lakshya_library_booknamemaster`.`bookNameID` 
			LEFT JOIN `lakshya_library_status`ON `lakshya_library_status`.`statusID`=`lakshya_library_bookmaster`.`statusID` 
			WHERE `lakshya_library_booknamemaster`.`bookNameID`=$bookNameID AND `lakshya_library_bookmaster`.`archive`=0 
			GROUP BY `lakshya_library_booknamemaster`.`bookNameID` ";
	$result1=$connection2->prepare($sql1);
	$result1->execute();
	$bookD=$result1->fetch();
	}
	catch(PDOException $e) { 
	echo $e;
	}
	$addBookURL=$_SESSION[$guid]["absoluteURL"] . "index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/add_book.php&booknameid=".$bookNameID;
	$editBookURL=$_SESSION[$guid]["absoluteURL"] . "index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/edit_book.php&bookid=";
	$trackBookURL=$_SESSION[$guid]["absoluteURL"] . "index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/view_borrowed.php&trackid=";

?>
	<div style="padding: 5px 20px;">
		<button class="cButton" style="float: left" onClick="window.history.back()">Back</button>
		<a href='<?=$addBookURL?>'><button class="cButton">Add Book Copy</button></a>
	</div>
<table width='100%' style="margin-top: 50px">
<thead>
	<tr>
		<th>Title</th>
		<th>Author</th>
		<th>Category</th>
		<th>Publisher</th>
		<th>Available</th>
		<th>Publication Place</th>
		<th>ISBN</th>
	</tr>
</thead>
<tbody>
<?php
$available=$bookD['available']-$bookD['borrowed'];
	echo "<tr>";
		echo "<td>{$bookD['title']}</td>";
		echo "<td>{$bookD['author']}</td>";
		echo "<td>{$bookD['category']}</td>";
		echo "<td>{$bookD['publisher']}</td>";
		echo "<td>($available/{$bookD['copy']})</td>";
		echo "<td>{$bookD['publicationPlace']}</td>";
		echo "<td>{$bookD['isbn']}</td>";
	echo "</tr>";
?>
</tbody>
</table>
<span style="margin: 10px 0"><span>
<?php
	try{
	$sql2="SELECT * 
			FROM `lakshya_library_bookmaster`
			LEFT JOIN `lakshya_library_status`ON `lakshya_library_status`.`statusID`=`lakshya_library_bookmaster`.`statusID`
			WHERE `bookNameID`=$bookNameID AND `lakshya_library_bookmaster`.`archive`=0";
	$result2=$connection2->prepare($sql2);
	$result2->execute();
	$booksD=$result2->fetchAll();
	}
	catch(PDOException $e) { 
	echo $e;
	}
 ?>
<table width='100%' style="margin-top: 50px">
<thead>
	<tr>
		<th>Acc No</th>
		<th>Call No</th>
		<th>Added Date</th>
		<th>Year</th>
		<th>Edition</th>
		<th>Volume</th>
		<th>Page</th>
		<th>Status</th>
		<th>Price</th>
		<th>Borrowed</th>
		<th>Action</th>
	</tr>
</thead>
<tbody>
<?php
	foreach($booksD as $b){
		$class='';
		if($b['borrow']!=0)
			$class="style='color: white; background: #ff731b'";
		if($b['available']==0)
			$class="style='color: white; background: red'";
		echo "<tr>";
			echo "<td $class>{$b['acc_no']}</td>";
			echo "<td $class>{$b['call_no']}</td>";
				$aDate=date('d/m/Y',strtotime($b['date_added']));
			echo "<td $class>{$aDate}</td>";
			echo "<td $class>{$b['year']}</td>";
			echo "<td $class>{$b['edition']}</td>";
			echo "<td $class>{$b['volume']}</td>";
			echo "<td $class>{$b['page']}</td>";
			echo "<td $class>{$b['status']}</td>";
			echo "<td $class>{$b['price']} {$b['currency']}</td>";
			$borrow=$b['borrow']!=0?"Yes  <a href='$trackBookURL{$b['bookID']}'><small>Track</small></a>":'No';
			echo "<td $class>$borrow</td>";
			echo "<td>";
				echo "<a href='$editBookURL{$b['bookID']}'>Edit</a>";
					echo "<b style='color: #ff731b;'> | </b>";
				echo "<a href='javascript:void(0)' class='deleteBook' id='{$b['bookID']}'>Delete</a>";
			echo "</td>";
		echo "</tr>";
	}
 ?>
</tbody>
</table>

<?php	
}	
 ?>
 <input type='hidden' id='deleteURL' value='<?=$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajax/deleteBook.php"?>'>
 <script>
 $('.deleteBook').click(function(){
	 if(confirm("Are you sure you want to archive this book copy?")){
		var id=$(this).attr('id');
		var deleteURL=$('#deleteURL').val();
			$.ajax
			({
				type: "POST",
				url: deleteURL,
				data: { id:id},
				success: function(msg)
				{ 
					console.log(msg);
					location.reload();
				}
			});
	 }
	 else
		 return;
 });
 </script>