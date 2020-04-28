<?php
include "../../config.php" ;
try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}
if($_POST){
	if($_REQUEST['action']=='fetch_data')	{
		$id=$_REQUEST['id'];
		try{
			$sql="SELECT * FROM `staff_contract_detail` where contract_id=".$id;
		$result=$connection2->prepare($sql);
		$result->execute();
		$editContract=$result->fetch();
		}
		catch(PDOException $e){
			echo $e;
		}
		echo $editContract['starting_date']."_".$editContract['ending_date']."_".$editContract['expired'];
	}
	if($_REQUEST['action']=='add')	{

		$staff_id=$_REQUEST['staff_id'];
		
		$startate = str_replace('/', '-', $_REQUEST['sdate']);
		$sdate=date("Y-m-d",strtotime($startate));

		$enddate = str_replace('/', '-', $_REQUEST['edate']);
		$edate=date("Y-m-d",strtotime($enddate));
				
		try{

		// Get Contract ID //

		$sql_select_contract = "SELECT contract_id FROM staff_contract_detail WHERE staff_id = $staff_id ORDER BY contract_id DESC LIMIT 0, 1";
		$result_select_contract=$connection2->prepare($sql_select_contract);
		$result_select_contract->execute();
		$contract_id=$result_select_contract->fetch();

		// Update Expired field of previous data //

		if($contract_id !='')
		{
			$sql_update_contract = "UPDATE staff_contract_detail SET expired = 'Y' WHERE contract_id = ".$contract_id['contract_id']."";
			$result_update_contract=$connection2->prepare($sql_update_contract);
			$result_update_contract->execute();
		}

		


		// Insert New Data //

		$sql="INSERT INTO `staff_contract_detail`(`staff_id`, `starting_date`, `ending_date`) VALUES ('".$staff_id."','".$sdate."','".$edate."')";
		$result=$connection2->prepare($sql);
		$result->execute();
		}
		catch(PDOException $e){
			echo $e;
		}
	}
	if($_REQUEST['action']=='update')	{
		$startate = str_replace('/', '-', $_REQUEST['sdate']);
		$sdate=date("Y-m-d",strtotime($startate));

		$enddate = str_replace('/', '-', $_REQUEST['edate']);
		$edate=date("Y-m-d",strtotime($enddate));

		$id=$_REQUEST['contract_id'];
		try{
		$sql="UPDATE `staff_contract_detail` SET `starting_date`='".$sdate."',`ending_date`='".$edate."' WHERE `contract_id`=".$id;
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


		// Get Contract ID //

		// $sql_select_contract = "SELECT contract_id FROM staff_contract_detail WHERE staff_id = (SELECT `staff_id` from staff_contract_detail where `contract_id` = $id) ORDER BY contract_id DESC LIMIT 1, 1";


//print_r($contract_id);
		$sql="DELETE FROM `staff_contract_detail` WHERE `contract_id`=".$id;
		$result=$connection2->prepare($sql);
		$result->execute();

		//print_r($contract_id);exit;


		$sql_select_contract = "SELECT contract_id FROM staff_contract_detail  ORDER BY contract_id DESC LIMIT 1";

		$result_select_contract=$connection2->prepare($sql_select_contract);
		$result_select_contract->execute();
		$contract_id=$result_select_contract->fetch();
		// Update Expired field of previous data //

		$sql_update_contract = "UPDATE staff_contract_detail SET expired = 'N' WHERE contract_id = ".$contract_id['contract_id']."";
		$result_update_contract=$connection2->prepare($sql_update_contract);
		$result_update_contract->execute();
		}
		catch(PDOException $e){
			echo $e;
		}
	}
	
}
?>