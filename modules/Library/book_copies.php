<?php
	$query="";
	if($_REQUEST){
		extract($_REQUEST);
		if(isset($statusID)){
			if($statusID!='')
				$query=" AND `lakshya_library_bookmaster`.`statusID`=$statusID";
		}
	}
		try{
		$sql="SELECT `bookID`,`acc_no`,`edition`,`year`,`volume`,`lakshya_library_category`.`category`,`lakshya_library_publisher`.`publisher`,`title`,`author`,
				`isbn`,`borrow`,`lakshya_library_status`.`status`,`lakshya_library_status`.`available` 
			FROM `lakshya_library_bookmaster`
			LEFT JOIN `lakshya_library_booknamemaster` ON `lakshya_library_booknamemaster`.`bookNameID`=`lakshya_library_bookmaster`.`bookNameID` 
			LEFT JOIN `lakshya_library_status` ON `lakshya_library_status`.`statusID`=`lakshya_library_bookmaster`.`statusID` 
			LEFT JOIN `lakshya_library_category` ON `lakshya_library_category`.`categoryID`=`lakshya_library_booknamemaster`.`categoryID` 
			LEFT JOIN `lakshya_library_publisher` ON `lakshya_library_publisher`.`publisherID`=`lakshya_library_booknamemaster`.`publisherID` 
			WHERE 1";
		$sql.=$query;
		$result=$connection2->prepare($sql);
		$result->execute();
		$data=$result->fetchAll();
		}
		catch(PDOException $e) {
		  echo $e->getMessage();
		}
 ?>
 <h2>Book Copies :</h2>
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
	<th>ISBN</th>
	<th>Status</th>
	<th>Borrowed</th>
	<th>Available</th>
 </tr>
 </thead>
 <tbody>
 <?php
	foreach($data as $d){
		$color=$d['available']==0?'red':($d['borrow']==1?'#ff731b':'');
		$style=$color!=''?"style='background: ".$color."; color: #fff; '":'';
		echo "<tr>";
			echo "<td $style>{$d['acc_no']}</td>";
			echo "<td $style>{$d['title']}</td>";
			echo "<td $style>{$d['author']}</td>";
			echo "<td $style>{$d['publisher']}</td>";
			echo "<td $style>{$d['category']}</td>";
			echo "<td $style>{$d['year']}</td>";
			echo "<td $style>{$d['edition']}</td>";
			echo "<td $style>{$d['volume']}</td>";
			echo "<td $style>{$d['isbn']}</td>";
			echo "<td $style>{$d['status']}</td>";
				$borrow=$d['borrow']==0?'No':'Yes';
			echo "<td $style>$borrow</td>";
				$available=$d['available']==0?'No':'Yes';
			echo "<td $style>$available</td>";
		echo "</tr>";
	}
 ?>
 </tbody>
 </table>
 <script src="<?php echo $_SESSION[$guid]["absoluteURL"] ;?>modules/<?=$_SESSION[$guid]["module"];?>/js/jquery.dataTables.min.js"></script>
 <script>
	$('#booksTable').DataTable();
 </script>