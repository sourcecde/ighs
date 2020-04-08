<?php
/* 
	This File Url:
	$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajax/getPersonDetails.php" ;
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
	if($action=='changeSection'){
		$msg="<option value=''>Select Section</option>";
		try{
			$sql="SELECT `gibbonRollGroupID`,`name` FROM `gibbonrollgroup` WHERE `gibbonSchoolYearID`=$yearID";
			$result=$connection2->prepare($sql);
			$result->execute();
			$sections=$result->fetchAll();
		}
		catch(PDOException $e) { 
			echo $e;
		}
		foreach($sections as $s){
			$msg.="<option value='{$s['gibbonRollGroupID']}'>{$s['name']}</option>";
		}
		echo $msg;
	}
}
?>