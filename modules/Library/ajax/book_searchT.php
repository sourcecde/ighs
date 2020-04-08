<?php
/* 
	This File Url:
	$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajax/book_searchT.php" ;
*/
@session_start() ;
//Including Global Functions & Dtabase Configuration.
include "../../../functions.php" ;
include "../../../config.php" ;

//New PDO DB connection
try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}
if(isset($_REQUEST)){
extract($_REQUEST);
//print_r($_REQUEST);
	if($action=='getHint'){
		try{
		if($type=='accNo')
			$sql="SELECT `lakshya_library_bookmaster`.`acc_no` FROM `lakshya_library_bookmaster` WHERE `acc_no` LIKE '$key%' LIMIT 5";
		else if($type=='title')
			$sql="SELECT `lakshya_library_booknamemaster`.`title` FROM `lakshya_library_booknamemaster` WHERE `title` LIKE '%$key%' LIMIT 7";
		else if($type=='author')
			$sql="SELECT `lakshya_library_booknamemaster`.`author` FROM `lakshya_library_booknamemaster` WHERE `author` LIKE '%$key%' LIMIT 7";
		$result=$connection2->prepare($sql);
		$result->execute();
		$data=$result->fetchAll();
		}
		catch(PDOException $e) {
		  echo $e->getMessage();
		}
		//print_r($data);
		$msg="";
		foreach($data as $d){
			if($type=='accNo')
				$tmp=$d['acc_no'];
			else if($type=='title')
				$tmp=$d['title'];
			else if($type=='author')
				$tmp=$d['author'];
			$msg.="<option>$tmp</option>";
		}
		echo $msg;
	}
	else if($action=='getBooksResult'){
		try{
		$sql="SELECT `lakshya_library_bookmaster`.`bookID`,`lakshya_library_bookmaster`.`acc_no`,`lakshya_library_booknamemaster`.`title`,`lakshya_library_booknamemaster`.`author`,`available`,`borrow`,`status`   
				FROM `lakshya_library_bookmaster` 
				LEFT JOIN `lakshya_library_booknamemaster` ON `lakshya_library_booknamemaster`.`bookNameID`=`lakshya_library_bookmaster`.`bookNameID`
				LEFT JOIN `lakshya_library_status` ON `lakshya_library_status`.`statusID`=`lakshya_library_bookmaster`.`statusID`
				WHERE 1";
		if($id=='search_accNo')
			$sql.=" AND `lakshya_library_bookmaster`.`acc_no`='$key'";
		else if($id=='search_title')
			$sql.=" AND `lakshya_library_booknamemaster`.`title`='$key'";
		else if($id=='search_author')
			$sql.=" AND `lakshya_library_booknamemaster`.`author`='$key'";
		$result=$connection2->prepare($sql);
		$result->execute();
		$data=$result->fetchAll();
		}
		catch(PDOException $e) {
		  echo $e->getMessage();
		}
		$msg="";
		foreach($data as $d){
			$msg.="<tr>";
				$msg.="<td>{$d['acc_no']}</td>";
				$msg.="<td>{$d['title']}</td>";
				$msg.="<td>{$d['author']}</td>";
				$a=FALSE;
				if($d['available']==1 && $d['borrow']==0){
					$a=TRUE;
					$available='Yes';
				}
				else if($d['available']==0)
					$available=="No ({$d['status']})";
				else if($d['borrow']==1)
					$available="No (Borrowed)";
				$msg.="<td>$available</td>";
				$msg.=$a?"<td><input type='checkbox' name='selectedBookID' id='selectedBookID' value='{$d['bookID']}'></td>":"<td></td>";
			$msg.="</tr>";
		}
		echo $msg;
	}
	else if($action=='borrowBook'){
		print_r($_REQUEST);
		$dateBorrow=dateFormat($dateBorrow);
		$dateDue=dateFormat($dateDue);
		
		try{
		echo $sql="SELECT `gibbonStudentEnrolmentID` FROM `gibbonstudentenrolment` WHERE `gibbonPersonID`=$personID AND `gibbonSchoolYearID`=$yearID";
		$result=$connection2->prepare($sql);
		$result->execute();
		$ID=$result->fetch();
		}
		catch(PDOException $e) {
		  echo $e->getMessage();
		}
		print_r($ID);
		$enrollID=$ID['gibbonStudentEnrolmentID'];
		
		try{
		$sql="INSERT INTO `lakshya_library_borrowmaster`(`borrowID`, `bookID`, `studentID`, `staffID`, `dateBorrow`, `dateDue`, `dateReturn`, `borrowStatus`) 
				VALUES (NULL,$bookID,$enrollID,0,'$dateBorrow','$dateDue','','Pending')";
		$result=$connection2->prepare($sql);
		$result->execute();
		}
		catch(PDOException $e) {
		  echo $e->getMessage();
		  echo $sql;
		}
		
		try{
		$sql="UPDATE `lakshya_library_bookmaster` SET `borrow`=1 WHERE `bookID`=$bookID";
		$result=$connection2->prepare($sql);
		$result->execute();
		}
		catch(PDOException $e) {
		  echo $e->getMessage();
		}
		
	}
	else if($action=='returnBook'){
		$idArr=explode('_',$id);
		$returnDate=dateFormat($returnDate);
		
		try{
		$sql="UPDATE `lakshya_library_borrowmaster` SET `dateReturn`='$returnDate',`borrowStatus`='Returned' WHERE `borrowID`={$idArr[0]}";
		$result=$connection2->prepare($sql);
		$result->execute();
		}
		catch(PDOException $e) {
		  echo $e->getMessage();
		}
		try{
		$sql="UPDATE `lakshya_library_bookmaster` SET `borrow`=1 WHERE `bookID`={$idArr[1]}";
		$result=$connection2->prepare($sql);
		$result->execute();
		}
		catch(PDOException $e) {
		  echo $e->getMessage();
		}
	}
}
function dateFormat($oDate){
	$tmp=explode('/',$oDate);
	return $tmp[2]."-".$tmp[1]."-".$tmp[0];
}
?>