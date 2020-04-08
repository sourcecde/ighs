<?php
		/*
		else if(isset($statusID)){
			if($statusID!='')
				$query=" AND `lakshya_library_bookmaster`.`statusID`=$statusID";
		}
		*/
		try{
		$sql="SELECT `bookID`,`acc_no`,`edition`,`year`,`volume`,`lakshya_library_category`.`category`,`lakshya_library_publisher`.`publisher`,`title`,`author` 
			FROM `lakshya_library_bookmaster`
			LEFT JOIN `lakshya_library_booknamemaster` ON `lakshya_library_booknamemaster`.`bookNameID`=`lakshya_library_bookmaster`.`bookNameID` 
			LEFT JOIN `lakshya_library_category` ON `lakshya_library_category`.`categoryID`=`lakshya_library_booknamemaster`.`categoryID` 
			LEFT JOIN `lakshya_library_publisher` ON `lakshya_library_publisher`.`publisherID`=`lakshya_library_booknamemaster`.`publisherID` 
			WHERE `lakshya_library_bookmaster`.`archive`=1";
		$result=$connection2->prepare($sql);
		$result->execute();
		$data=$result->fetchAll();
		}
		catch(PDOException $e) {
		  echo $e->getMessage();
		}
 ?>
 <h2>Archieved Book: </h3>
 <table width='100%' id='booksTable'>
 <thead>
 <tr>
	<th>Acc No</th>
	<th>Title</th>
	<th>Author</th>
	<th>Publisher</th>
	<th>Category</th>
	<th>Year</th>
	<th>Edition</th>
	<th>Volume</th>
	<th></th>
 </tr>
 </thead>
 <tbody>
 <?php
	foreach($data as $d){
		echo "<tr>";
			echo "<td>{$d['acc_no']}</td>";
			echo "<td>{$d['title']}</td>";
			echo "<td>{$d['author']}</td>";
			echo "<td>{$d['publisher']}</td>";
			echo "<td>{$d['category']}</td>";
			echo "<td>{$d['year']}</td>";
			echo "<td>{$d['edition']}</td>";
			echo "<td>{$d['volume']}</td>";
			echo "<td><button class='cButton' id='{$d['bookID']}'>Restore</button></td>";
		echo "</tr>";
	}
 ?>
 </tbody>
 </table>
 <script src="<?php echo $_SESSION[$guid]["absoluteURL"] ;?>modules/<?=$_SESSION[$guid]["module"];?>/js/jquery.dataTables.min.js"></script>
 <script>
	$('#booksTable').DataTable();
 </script>