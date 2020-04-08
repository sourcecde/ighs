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
@session_start() ;
if($_POST){
	extract($_POST);
		$sql="SELECT `feeID`,`amount`,`spot_name` FROM `transport_fee_yearwise`
				LEFT JOIN `transport_spot_price` ON `transport_spot_price`.`transport_spot_price_id`=`transport_fee_yearwise`.`transport_spot_price_id`
				WHERE `gibbonSchoolYearID`=$yearID";
		$result=$connection2->prepare($sql);
		$result->execute();
		$data=$result->fetchAll();
		$db=array();
		foreach($data as $d){
			$tmp="<input type='hidden' id='v_{$d['feeID']}' value='{$d['amount']}'><a href='#' id='e_{$d['feeID']}' class='spot_price_edit'>EDIT</a> | <a href='#' id='d_{$d['feeID']}' class='spot_price_delete'>DELETE</a>";
			$db[]=array($d['spot_name'],$d['amount'],$tmp);
		}
		echo json_encode(array('data'=>$db));
}
?>