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
if($_POST)
{
	if($_REQUEST['action']=='checkmonth'){
		$enrollid=$_REQUEST['studentenrollid'];
		$montharr=$_REQUEST['montharr'];
		$monthstr=implode(",", $montharr);
		$sql="SELECT SUM(price) as 'tot_price' FROM transport_month_entry WHERE month_name IN(".$monthstr.") AND gibbonStudentEnrolmentID='".$enrollid."' AND payment_master_id=0";
		
		$result=$connection2->prepare($sql);
		$result->execute();
		$dboutbut=$result->fetch();
		if($dboutbut['tot_price']){
			echo $dboutbut['tot_price'];
		}else{
		echo 0;
		}
		
	}
	
	
}
