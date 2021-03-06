<?php
ob_start();
@session_start() ;
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
if($_POST){
		$isLeft='Y';
		if($_POST['dateEnd']!=''){
			$tmp=explode("/",$_POST['dateEnd']);
			$dateEnd=$tmp[2]."-".$tmp[1]."-".$tmp[0];
		}
		$yearOfLeaving=$_POST['year_of_leaving'];
		if($_POST['entryDate']!=''){
			$tmp=explode("/",$_POST['entryDate']);
			$date_created=$tmp[2]."-".$tmp[1]."-".$tmp[0];
		}
		$leavingReason=$_POST['reason'];
		$hasTc=$_POST['hasTC'];
		$dateOfTc="0000-00-00";
		if(isset($_POST['tcDate']) && $hasTc=='Y'){
			$tmp=explode("/",$_POST['tcDate']);
			$dateOfTc=$tmp[2]."-".$tmp[1]."-".$tmp[0];
		}
		$tcNumber=$hasTc=='Y'?$_POST['tcNumber']:NULL;
		$preferredName=$_POST['preferredName'];
		$gibbonPersonID=$_POST['gibbonPersonID'];
		$nextSchool=$_POST['nextSchool'];
			if($isLeft=="Y")
			{	
			
				try {
				$data=array("studentId"=>$gibbonPersonID,"studentName"=>$preferredName, "isLeft"=>$isLeft,"yearOfLeaving"=>$yearOfLeaving,"leavingReason"=>$leavingReason, "hasTc"=>$hasTc, "dateOfTc"=>$dateOfTc, "tcNumber"=>$tcNumber, "date_created"=>$date_created);	
				$query="SELECT * FROM leftstudenttracker where student_id=".$gibbonPersonID;
				$result=$connection2->prepare($query);
				$result->execute();
						if ($result->rowCount()!=1)
						{
							$query1="INSERT INTO leftstudenttracker(student_id,studentName,isLeft,yearOfLeaving,leavingReason,hasTc,tcNumber,dateOfTc,date_created) 
								values(\"".$gibbonPersonID."\",\"".$preferredName."\",\"".$isLeft."\",\"".$yearOfLeaving."\",\"".$leavingReason."\",\"".$hasTc."\",\"".$tcNumber."\",\"".$dateOfTc."\",\"".$date_created."\");";
							$result1=$connection2->prepare($query1);
							$result1->execute();
							$query2="UPDATE `gibbonperson` SET `dateEnd`='$dateEnd',`nextSchool`='$nextSchool',`departureReason`='$leavingReason' WHERE `gibbonPersonID`=$gibbonPersonID";
							$result1=$connection2->prepare($query2);
							$result1->execute();
						} 
						 else{
							try{
							$query2="UPDATE leftstudenttracker SET studentName=:studentName, isLeft=:isLeft, yearOfLeaving=:yearOfLeaving, leavingReason=:leavingReason,hasTc=:hasTc, dateOfTc=:dateOfTc, tcNumber=:tcNumber,date_created=:date_created Where student_id=:studentId;";
							$result2=$connection2->prepare($query2);
							$result2->execute($data);
							$query3="UPDATE `gibbonperson` SET `dateEnd`='$dateEnd',`nextSchool`='$nextSchool',`departureReason`='$leavingReason' WHERE `gibbonPersonID`=$gibbonPersonID";
							$result1=$connection2->prepare($query3);
							$result1->execute();
							}
							catch(PDOException $e){
								echo $e->getMesssage();
							}
						}
						
				} 
				catch(PDOException $e) { 
						//Fail 2
						echo $e->getMessage();
					}
			}
	header("Location: ".$_SESSION[$guid]["absoluteURL"]."/index.php?q=/modules/User%20Admin/manage_left_students.php");
}
?>