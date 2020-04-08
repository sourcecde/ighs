<?php
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
if(isset($_REQUEST['action'])){
	if($_REQUEST['action']=='update_a_d'){
	extract($_POST);
	$timeStamp=time();
	$sql="UPDATE `lakshyastaffattendancelog` SET `type`='$type',`comment`='$reason',`StaffIDTaker`={$_SESSION[$guid]['gibbonPersonID']},`timeStamp`=$timeStamp WHERE `attendanceLogID`=$id";
	$result=$connection2->prepare($sql);
	$result->execute();
	//echo $sql;
	}
}
	if($_REQUEST['action']=='add')	{
		$caption=$_REQUEST['caption'];
		$short_name=$_REQUEST['short_name'];
		try{
		$sql="INSERT INTO `lakshyastaffattendancerule`(`rule_id`, `short_name`, `caption`) VALUES (NULL,'$short_name','$caption')";
		$result=$connection2->prepare($sql);
		$result->execute();
		}
		catch(PDOException $e){
			echo $e;
		}
	}
	if($_REQUEST['action']=='update')	{
		$caption=$_REQUEST['caption'];
		$short_name=$_REQUEST['short_name'];
		$id=$_REQUEST['id'];
		try{
		$sql="UPDATE `lakshyastaffattendancerule` SET `caption`='$caption',short_name='$short_name' WHERE `rule_id`=".$id;
		$result=$connection2->prepare($sql);
		$result->execute();
		}
		catch(PDOException $e){
			echo $e;
		}
	}
	if($_REQUEST['action']=='delete')	{
		$id=$_REQUEST['id'];
		try{
		$sql="DELETE FROM `lakshyastaffattendancerule` WHERE `rule_id`=".$id;
		$result=$connection2->prepare($sql);
		$result->execute();
		}
		catch(PDOException $e){
			echo $e;
		}
	}
	if($_REQUEST['action']=='leave_update')	{
		$id=$_REQUEST['id'];
		$value=$_REQUEST['value']+0;
		
		try{
		$sql="UPDATE `lakshyastaffleavecredit` SET `value`=$value WHERE `credit_id`=".$id;
		$result=$connection2->prepare($sql);
		$result->execute();
		}
		catch(PDOException $e){
			echo $e;
		}
	}
?>