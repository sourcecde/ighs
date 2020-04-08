<?php
include "../../config.php" ;
@session_start();
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
if($action=='getSection'){
	$sql="SELECT s.`gibbonRollGroupID`, CONCAT(c.`name`,' ',s.name) as name
	FROM `gibbonrollgroup` s
	LEFT JOIN `gibbonyeargroup` c ON s.`gibbonYearGroupID`=c.`gibbonYearGroupID`
	WHERE gibbonSchoolYearID=".$year_id;
	$result=$connection2->prepare($sql);
	$result->execute();
	//$return_message="<option value='all'>All</option>";
	$return_message="";
	while($section=$result->fetch())
	{
		$return_message.="<option value='".$section['gibbonRollGroupID']."'>".$section['name']."</option>";
	}
	header('Content-Type: application/json');
	echo json_encode($return_message);
}
else if($action=='getRecipient'){
	$sql="SELECT `lakshyasmsrecipients`.*,`gibbonperson`.`preferredName` FROM `lakshyasmsrecipients` 
	 LEFT JOIN `gibbonperson` ON `gibbonperson`.`gibbonPersonID`=`lakshyasmsrecipients`.`personID` 
	 WHERE `SMSLogID`=$id ORDER BY `success` DESC";
	 $result=$connection2->prepare($sql);
	$result->execute();
	$data=$result->fetchAll();
	$return_message="<table width='100%'><tr><th>Recipient</th><th>Phone</th><th>Status</th></tr>";
	foreach($data as $d){
		if((int)$d["success"]==1){
			$return_message.="<tr><td style='background:#91e691;'>{$d['preferredName']}</td><td style='background:#91e691;' class='rightA'>{$d['phone']}</td><td style='background:#91e691;' class='rightA'>{$d['status']}</td></tr>";
		}
		else{
			$return_message.="<tr><td style='background:#f37575;'>{$d['preferredName']}</td><td style='background:#f37575;' class='rightA'>{$d['phone']}</td><td style='background:#f37575;' class='rightA'>{$d['status']}</td></tr>";
		}
	}
	$return_message.="</table>";
	echo $return_message;
	 
}
}
 ?>