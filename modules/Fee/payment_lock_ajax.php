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
	if($_REQUEST['action']=='lock_payment'){
	$id_arr=$_REQUEST['id'];
	$id_str='';
	$i=0;
	foreach($id_arr as $a)
	{	if($i++==0)
		$id_str.=$a;
		else
		$id_str.=','.$a;
	}
	//echo $id_str;
	$sql="UPDATE `payment_master` SET `lock`=1 WHERE `payment_master_id` IN (".$id_str.") ";
		
		//echo $sql;	
		$result=$connection2->prepare($sql);
		$result->execute();
	}
	
	
	if($_REQUEST['action']=='unlock_payment'){
	$id_arr=$_REQUEST['id'];
	$id_str='';
	$i=0;
	foreach($id_arr as $a)
	{	if($i++==0)
		$id_str.=$a;
		else
		$id_str.=','.$a;
	}
	//echo $id_str;
	$sql="UPDATE `payment_master` SET `lock`=0 WHERE `payment_master_id` IN (".$id_str.") ";
		
		//echo $sql;	
		$result=$connection2->prepare($sql);
		$result->execute();
	}
}

?>