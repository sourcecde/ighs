<?php
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
	if($action=='changeClass'){
		try {
		$sql1="SELECT gibbonperson.preferredName,gibbonstudentenrolment.gibbonStudentEnrolmentID from gibbonstudentenrolment
		LEFT JOIN gibbonperson ON gibbonperson.gibbonPersonID=gibbonstudentenrolment.gibbonPersonID ";
		if($classID!=''){
			$sql1.="WHERE gibbonstudentenrolment.gibbonYearGroupID = $classID AND ";
		}
		else{
			$sql1.="WHERE ";
		}
		$sql1.="`gibbonperson`.`dateEnd` IS NULL
		ORDER BY `gibbonYearGroupID`,`gibbonperson`.`preferredName`";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		$person=$result1->fetchAll();
		}
		catch(PDOException $e) { 
			echo $e;
		}
		$msg="";
		foreach($person as $t){
			$msg.= "<option value='{$t['gibbonStudentEnrolmentID']}' >{$t['preferredName']}</option>";
		}
		echo $msg;
	}
}
?>