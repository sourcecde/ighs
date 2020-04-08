<?php
@session_start() ;
include "../../config.php" ;
try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}

if($_POST){
	extract($_POST);
	if($action=='add' || $action=='update'){
		$addedDate=date('Y-m-d',strtotime($date_added));
		$dataMaster=array('title'=>$title,'author'=>$author,'publisherID'=>$publisher,'publicationPlace'=>$pub_place,
					'categoryID'=>$category,'isbn'=>$isbn);
		$data=array('acc_no'=>$acc_no,'call_no'=>$call_no,'date_added'=>$date_added,'year'=>$year,'edition'=>$edition,'volume'=>$volume,
					'page'=>$page,'price'=>$price,'currency'=>$currency,'statusID'=>$status,'remarks'=>$remarks);
		if($action=='add'){
			try{
				$sql2="INSERT INTO `lakshya_library_booknamemaster`
						(`bookNameID`, `title`, `author`, `categoryID`, `publisherID`, `publicationPlace`, `isbn`) VALUES
						(NULL,:title,:author,:categoryID,:publisherID,:publicationPlace,:isbn)";
				$result2=$connection2->prepare($sql2);
				$result2->execute($dataMaster);
				echo $id=$connection2->lastInsertID();
			}
			catch(PDOException $e) {
			  echo $e->getMessage();
			}
			try{
				$sql3="INSERT INTO `lakshya_library_bookmaster`
						(`bookID`, `bookNameID`, `acc_no`, `call_no`, `date_added`, `year`, `edition`, `volume`, `page`, `price`, `currency`, `statusID`, `imageID`, `borrow`, `remarks`, `archive`) VALUES 
							(NULL,$id,:acc_no,:call_no,:date_added,:year,:edition,:volume,:page,:price,:currency,:statusID,0,0,:remarks,0)";
				$result3=$connection2->prepare($sql3);
				$result3->execute($data);
			}
			catch(PDOException $e) {
			  echo $e->getMessage();
			}
		}
		
		
		else if($action=='update'){
			
			try{
				$sql1="SELECT `title` FROM `lakshya_library_booknamemaster` WHERE `bookNameID`=$bookNameID";
				$result1=$connection2->prepare($sql1);
				$result1->execute();
				$old=$result1->fetch();
			}
			catch(PDOException $e) {
			  echo $e->getMessage();
			}
			if($old['title']==$title){
				try{
					$sql2="UPDATE `lakshya_library_booknamemaster` SET `title`=:title,`author`=:author,`categoryID`=:categoryID,`publisherID`=:publisherID,`publicationPlace`=:publicationPlace,`isbn`=:isbn WHERE `bookNameID`=".$bookNameID;
					$result2=$connection2->prepare($sql2);
					$result2->execute($dataMaster);
				}
				catch(PDOException $e) {
				  echo $e->getMessage();
				}
				$id=$bookNameID;
			}
			else{
				try{
					$sql2="SELECT `bookNameID` FROM `lakshya_library_booknamemaster` WHERE `title`='$title'";
					$result2=$connection2->prepare($sql2);
					$result2->execute();
					$bE=$result2->fetch();
				}
				catch(PDOException $e) {
				  echo $e->getMessage();
				}
				if(empty($bE)){
					try{
						$sql3="INSERT INTO `lakshya_library_booknamemaster`
								(`bookNameID`, `title`, `author`, `categoryID`, `publisherID`, `publicationPlace`, `isbn`) VALUES
								(NULL,:title,:author,:categoryID,:publisherID,:publicationPlace,:isbn)";
						$result3=$connection2->prepare($sql3);
						$result3->execute($dataMaster);
						$id=$connection2->lastInsertID();
					}
					catch(PDOException $e) {
					  echo $e->getMessage();
					}
				}
				else{
					$id=$bE['bookNameID'];
				}
			}
			try{
				$sql3="UPDATE `lakshya_library_bookmaster` SET 
						`bookNameID`=$id,`acc_no`=:acc_no,`call_no`=:call_no,`date_added`=:date_added,`year`=:year,`edition`=:edition,`volume`=:volume,
						`page`=:page,`price`=:price,`currency`=:currency,`statusID`=:statusID,`remarks`=:remarks 
						WHERE `bookID`=$bookID";
				$result3=$connection2->prepare($sql3);
				$result3->execute($data);
			}
			catch(PDOException $e) {
			  echo $e->getMessage();
			  echo "<br>$sql3</b>";
			}
			
		}
	}
}
 ?>
 <script>
 window.history.back();
 </script>