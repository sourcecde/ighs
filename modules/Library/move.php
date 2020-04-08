<?php 
include "../../config.php" ;
/* For LMS database */
$dbname="eb_lms";
try {
  	$connection1=new PDO("mysql:host=$databaseServer;dbname=$dbname;charset=utf8", $databaseUsername, $databasePassword);
	$connection1->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection1->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}
/* For LMS database */
try{
$sql1="SELECT * FROM `book` LIMIT 500";
$result1=$connection1->prepare($sql1);
$result1->execute();
$bookD=$result1->fetchAll();
}
catch(PDOException $e) { 
echo $e;
}
$books=array();
 foreach($bookD as $b){
	$books[$b['book_name_id']][]=$b;
 }
 /*
 echo "<pre>";
 print_r($books);
 echo "</pre>";
 */
/* For Lakshya database */
try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}

foreach($books as $B){
	
	$title=$B[0]['book_title'];
	$author=$B[0]['author'];
	try{
	$data=array('title'=>$title,'author'=>$author);
	$sql="INSERT INTO `lakshya_library_booknamemaster`(`bookNameID`, `title`, `author`, `categoryID`, `publisherID`, `publicationPlace`, `isbn`) VALUES 
			(NULL,:title,:author,1,1,'','')";
	$result=$connection2->prepare($sql);
	$result->execute($data);
	$id=$connection2->lastInsertId();
	}
	catch(PDOException $e) { 
	echo $e."<br>";
	}
	$sql1="INSERT INTO `lakshya_library_bookmaster`(`bookID`, `bookNameID`, `acc_no`, `call_no`, `date_added`, `year`, `edition`, `volume`, `page`, `price`, `currency`, `statusID`, `imageID`, `borrow`, `remarks`, `archive`) VALUES ";
	$i=0;
	foreach($B as $b){
		if($i++>0)
			$sql1.=", ";
		$sql1.="(NULL,$id,'{$b['acc_no']}','','{$b['date_added']}','','','',0,{$b['price']},'{$b['currency']}',0,0,0,'',0)";
	}
	try{
	$result1=$connection2->prepare($sql1);
	$result1->execute();
	}
	catch(PDOException $e) { 
	echo $e."<br>";
	}
	
}

/* For Lakshya database */


?>