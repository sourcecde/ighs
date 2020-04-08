<?php
$url=$_SERVER['REQUEST_URI'];
echo "<h3>Add Book :</h3>";
$processURL=$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/process_managebook.php";
echo "<form method='post' action='$processURL'>";
echo "<input type='hidden' name='action' value='add'>";
include("bookentry_template.php");
echo "<input type='submit' name='Add'>";
echo "</form>";
 if(isset($_REQUEST['booknameid'])){
	try{
	$sql1="SELECT * FROM `lakshya_library_booknamemaster` WHERE `bookNameID`=".$_REQUEST['booknameid'];
	$result1=$connection2->prepare($sql1);
	$result1->execute();
	$data=$result1->fetch();
	}
	catch(PDOException $e) { 
	echo $e;
	}
?>
	<script>
		$('#title').val('<?=$data['title']?>');
		$('#author').val('<?=$data['author']?>');
		$('#pub_place').val('<?=$data['publicationPlace']?>');
		$('#isbn').val('<?=$data['isbn']?>');
		$('#publisher option[value="<?=$data['publisherID']?>"]').prop('selected', true);
		$('#category option[value="<?=$data['categoryID']?>"]').prop('selected', true);
	</script>
<?php
}