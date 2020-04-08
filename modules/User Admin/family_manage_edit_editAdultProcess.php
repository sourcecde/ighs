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
	if (isActionAccessible($guid , $connection2 , "/modules/User Admin/family_manage_edit.php")==FALSE) {
		//Fail 0
		$URL.="&updateReturn=fail0" ;
		header("Location: {$URL}");
	}
	else{
		$count=2;
		if(isset($gibbonFamilyAdultID3) && $gibbonFamilyAdultID3!=''){
			
			$count=3;
		}
		try{
			for($i=1;$i<=$count;$i++){
				$sql="UPDATE `gibbonfamilyadult` SET `contactPriority`='${'contactPriority'.$i}' , `officialName`='${'officialName'.$i}' , `email`='${'email'.$i}' , `phone1Type`='${'phone1Type'.$i}' , `phone1CountryCode`='${'phone1CountryCode'.$i}' , `phone1`='${'phone1'.$i}' , `phone2Type`='${'phone2Type'.$i}' , `phone2CountryCode`='${'phone2CountryCode'.$i}' , `phone2`='${'phone2'.$i}' , `profession`='${'profession'.$i}' , `annual_income`='${'annual_income'.$i}' , `jobTitle`='${'jobTitle'.$i}' , `nationalIDCardNumber`='${'nationalIDCardNumber'.$i}' WHERE `gibbonFamilyAdultID`='${'gibbonFamilyAdultID'.$i}';<br><br>";
				$result=$connection2->prepare($sql);
				$result->execute();
			}
		}
		catch(PDOException $e){
				$URL.="&updateReturn=fail1" ;
				header("Location: {$URL}");
				echo $e;
				break ;
		}
		if($addGuardian=='on'){
			try{
				$sql="INSERT INTO `gibbonfamilyadult`(`gibbonFamilyAdultID`, `gibbonFamilyID`, `contactPriority`, `officialName`, `email`, `phone1Type`, `phone1CountryCode`, `phone1`, `phone2Type`, `phone2CountryCode`, `phone2`, `profession`, `employer`, `annual_income`, `jobTitle`, `nationalIDCardNumber`) VALUES(NULL,'$gibbonFamilyID','$contactPriority3','$officialName3','$email3','$phone1Type3','$phone1CountryCode3','$phone13','$phone2Type3','$phone2CountryCode3','$phone23','$profession3','$employer3','$annual_income3','$jobTitle3','$nationalIDCardNumber3')";
				$sql;
				$result=$connection2->prepare($sql);
				$result->execute();
			}
			catch(PDOException $e){
				$URL.="&updateReturn=fail1" ;
				header("Location: {$URL}");
				echo $e;
				break ;
			}
			$gibbonFamilyAdultID = $connection2->lastInsertId();
			try{
				$sql="SELECT `gibbonPersonID` FROM `gibbonfamilychild` WHERE `gibbonFamilyID`=$gibbonFamilyID";
				$result=$connection2->prepare($sql);
				$result->execute();
				$pArr=$result->fetchAll();
			}
			catch(PDOException $e){
				$URL.="&updateReturn=fail1" ;
				//header("Location: {$URL}");
				echo $e;
				break ;
			}
			//print_r($pArr);
			foreach($pArr as $p){
				//print_r($p);
				try{
					$sql="INSERT INTO `gibbonfamilyrelationship`(`gibbonFamilyRelationshipID`, `gibbonFamilyID`, `gibbonFamilyAdultID`, `gibbonPersonID`, `relationship`) VALUES (NULL,'$gibbonFamilyID','$gibbonFamilyAdultID','".$p['gibbonPersonID']."','$guardianRelation')";
					//echo $sql;
					$result=$connection2->prepare($sql);
					$result->execute();
				}
				catch(PDOException $e){
					$URL.="&updateReturn=fail1" ;
					//header("Location: {$URL}");
					echo $e;
					break ;
				}
				//echo $connection2->lastInsertId();
			}
		}
		$URL.="&updateReturn=success0";
		header("Location: {$URL}");
	}
}	
?>