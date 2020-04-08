<?php
//include "../../config.php" ;
@session_start();
/*try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}
*/
$sql="SELECT `transport_spot_price_id`,`price` FROM `transport_spot_price`";
$result=$connection2->prepare($sql);
$result->execute();
$data=$result->fetchAll();
//print_r($data);

$query="INSERT INTO `transport_fee_yearwise`(`feeID`, `transport_spot_price_id`, `amount`, `gibbonSchoolYearID`) VALUES "; 
$i=0;
foreach($data as $d){
if($i++!=0)
		$query.=", ";
	$query.="(NULL,{$d['transport_spot_price_id']},{$d['price']},021)";
}
echo $query;
$result1=$connection2->prepare($query);
$result1->execute();
?>