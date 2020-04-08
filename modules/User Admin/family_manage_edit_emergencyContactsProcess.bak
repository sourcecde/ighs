<?php
include "../../functions.php" ;
include "../../config.php" ;

//New PDO DB connection
try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}

@session_start() ;
if($_POST){
	$URL=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/User Admin/family_manage_edit.php&gibbonFamilyID={$_POST['gibbonFamilyID']}" ;
	if (isActionAccessible($guid, $connection2, "/modules/User Admin/family_manage_edit.php")==FALSE) {
		//Fail 0
		$URL.="&updateReturn=fail0" ;
		header("Location: {$URL}");
	}
	else{
		try{
			$sql="UPDATE `gibbonfamily` SET `emergency1Name`='{$_POST['emergency1Name']}',`emergency1Phone`='{$_POST['emergency1Phone']}',`emergency1Relation`='{$_POST['emergency1Relation']}',`emergency2Name`='{$_POST['emergency2Name']}',`emergency2Phone`='{$_POST['emergency2Phone']}',`emergency2Relation`='{$_POST['emergency2Relation']}' WHERE `gibbonFamilyID`={$_POST['gibbonFamilyID']}";
			print $sql;
			$result=$connection2->prepare($sql);
			$result->execute();
			
		}
		catch(PDOException $e){
				$URL.="&updateReturn=fail1" ;
				header("Location: {$URL}");
				//echo $e;
				break ;
		}
		$URL.="&updateReturn=success0";
		header("Location: {$URL}");
	}
}	
?>