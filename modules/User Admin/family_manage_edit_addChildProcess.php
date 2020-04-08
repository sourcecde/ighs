<?php
include "../../functions.php" ;
include "../../config.php" ;

//New PDO DB connection
try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8" , $databaseUsername , $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE , PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE , PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}

@session_start() ;
if($_POST){
	extract($_POST);
	$URL=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/User Admin/family_manage_edit.php&gibbonFamilyID={$gibbonFamilyID}" ;
	/*if (isActionAccessible($guid , $connection2 , "/modules/User Admin/family_manage_edit.php")==FALSE) {
		//Fail 0
		$URL.="&updateReturn=fail0" ;
		header("Location: {$URL}");
	}
	else{*/
		//echo $gibbonFamilyID."<br>";
		//echo $gibbonPersonID;
		try{
			$sqlSelect="SELECT `gibbonFamilyID` FROM `gibbonfamilychild` WHERE `gibbonPersonID`='$gibbonPersonID'";
			$resultSelect=$connection2->prepare($sqlSelect);
			$resultSelect->execute();
			$oldFamily=$resultSelect->fetch();
		}
		catch(PDOException $e){
			$URL.="&updateReturn=fail1" ;
			//header("Location: {$URL}");
			//echo $e;
			break ;
		}
		try{
			$sqlSelect="SELECT * FROM `gibbonfamilychild` WHERE `gibbonFamilyID`=".$oldFamily['gibbonFamilyID'];
			$resultSelect=$connection2->prepare($sqlSelect);
			$resultSelect->execute();
		}
		catch(PDOException $e){
			$URL.="&updateReturn=fail1" ;
			header("Location: {$URL}");
			//echo $e;
			break ;
		}		
		if($resultSelect->rowCount()==1){
			try{
				$sql="UPDATE `gibbonfamilychild` SET `gibbonFamilyID`=$gibbonFamilyID WHERE `gibbonPersonID`=$gibbonPersonID";
				$result=$connection2->prepare($sql);
				$result->execute();
			}
			catch(PDOException $e){
				$URL.="&updateReturn=fail1" ;
				header("Location: {$URL}");
				echo $e;
				break ;
			}
			
			echo $oldFamily['gibbonFamilyID'];
			try{
				$sql="DELETE FROM `gibbonfamily` WHERE `gibbonFamilyID`=".$oldFamily['gibbonFamilyID'];
				$result=$connection2->prepare($sql);
				$result->execute();
			}
			catch(PDOException $e){
				$URL.="&updateReturn=fail1" ;
				header("Location: {$URL}");
				//echo $e;
				break ;
			}
			try{
				$sql="DELETE FROM `gibbonfamilyadult` WHERE `gibbonFamilyID`=".$oldFamily['gibbonFamilyID'];
				$result=$connection2->prepare($sql);
				$result->execute();
			}
			catch(PDOException $e){
				$URL.="&updateReturn=fail1" ;
				header("Location: {$URL}");
				//echo $e;
				break ;
			}
			try{
				$sql="DELETE FROM `gibbonfamilyrelationship` WHERE `gibbonFamilyID`=".$oldFamily['gibbonFamilyID'];
				$result=$connection2->prepare($sql);
				$result->execute();
			}
			catch(PDOException $e){
				$URL.="&updateReturn=fail1" ;
				header("Location: {$URL}");
				//echo $e;
				break ;
			}
		}
		else if($resultSelect->rowCount()>1){
			try{
				$sql="UPDATE `gibbonfamilychild` SET `gibbonFamilyID`=$gibbonFamilyID WHERE `gibbonPersonID`=$gibbonPersonID";
				$result=$connection2->prepare($sql);
				$result->execute();
			}
			catch(PDOException $e){
				$URL.="&updateReturn=fail1" ;
				header("Location: {$URL}");
				//echo $e;
				break ;
			}			
		}
		else{
			$URL.="&updateReturn=fail1" ;
			header("Location: {$URL}");
			//echo $e;
			break ;
		}
		$URL.="&updateReturn=success0" ;
		header("Location: {$URL}");
	//}
}	
?>