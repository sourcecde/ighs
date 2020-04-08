<?php
$url=$_SERVER['REQUEST_URI'];
echo "<h3>Edit Book :</h3>";
$processURL=$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/process_managebook.php";
echo "<form method='post' action='$processURL'>";
echo "<input type='hidden' name='action' value='update'>";
echo "<input type='hidden' name='bookNameID' id='bookNameID'>";
echo "<input type='hidden' name='bookID' id='bookID'>";
include("bookentry_template.php");
echo "<input type='submit' value='Update'>";
echo "</form>";
 if(isset($_REQUEST['bookid'])){
	try{
	$sql1="SELECT * FROM `lakshya_library_bookmaster` 
			LEFT JOIN `lakshya_library_booknamemaster` ON `lakshya_library_booknamemaster`.`bookNameID`=`lakshya_library_bookmaster`.`bookNameID` 
			WHERE `bookID`=".$_REQUEST['bookid'];
	$result1=$connection2->prepare($sql1);
	$result1->execute();
	$data=$result1->fetch();
	}
	catch(PDOException $e) { 
	echo $e;
	}
?>
	<script>
		$('#bookID').val('<?=$data['bookID']?>');
		$('#bookNameID').val('<?=$data['bookNameID']?>');
		$('#title').val('<?=$data['title']?>');
		$('#author').val('<?=$data['author']?>');
		$('#pub_place').val('<?=$data['publicationPlace']?>');
		$('#isbn').val('<?=$data['isbn']?>');
		$('#acc_no').val('<?=$data['acc_no']?>');
		$('#call_no').val('<?=$data['call_no']?>');
		$('#year').val('<?=$data['year']?>');
		$('#edition').val('<?=$data['edition']?>');
		$('#volume').val('<?=$data['volume']?>');
		$('#page').val('<?=$data['page']?>');
		$('#price').val('<?=$data['price']?>');
		$('#currency').val('<?=$data['currency']?>');
		$('#remarks').val('<?=$data['remarks']?>');
		$('#publisher option[value="<?=$data['publisherID']?>"]').prop('selected', true);
		$('#category option[value="<?=$data['categoryID']?>"]').prop('selected', true);
		$('#status option[value="<?=$data['statusID']?>"]').prop('selected', true);
	</script>
<?php
}