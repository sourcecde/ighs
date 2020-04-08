<?php
@session_start() ;
include "../../config.php" ;
try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}
if($_REQUEST['action']=='fetch_data'){
	    //$sql="SELECT rule_id,amount FROM lakshyaSalaryMaster WHERE staff_id=".$_REQUEST['sid'];
	    $sql="SELECT rule_id,amount FROM lakshyasalarymaster WHERE staff_id=".$_REQUEST['sid'];  
		$sql.=" AND month=".$_REQUEST['month'];
		$sql.=" AND year_id=".$_REQUEST['year'];
		$result=$connection2->prepare($sql);
		$result->execute();
		$data=$result->fetchAll();
		$out_str='';
		$i=0;
		foreach($data as $d){
			if($i++!=0)
				$out_str.="#";
			$out_str.=$d['rule_id']."_".$d['amount'];
		}
		echo $out_str;
}
if($_REQUEST['action']=='update_ps'){
		
		$data=substr($_REQUEST['data'],0,strlen($_REQUEST['data'])-1);
		$data_a=explode('/',$data);
		$rule_a=array();
		$case_str='';
		$in_str='';
		$i=0;
		foreach($data_a as $d){
			$t=explode('_',$d);
			$case_str.=" WHEN rule_id =".$t[2]."  THEN ".$t[3];
			if($i++!=0)
				$in_str.=",";
			$in_str.=$t[2];
		}
		//$sql="UPDATE lakshyaSalaryMaster SET
		 //  amount=  CASE";
	    $sql="UPDATE lakshyasalarymaster SET
	     amount=  CASE";
        $sql.=$case_str;               
        $sql.="            END 
				WHERE   rule_id IN (".$in_str.")";
		$sql.=" AND staff_id=".($_REQUEST['sid']+0);		
		$sql.=" AND month=".$_REQUEST['month'];		
		$sql.=" AND year_id=".($_REQUEST['year']+0);
		
		//echo "<pre>";print_r($sql);die;
		
		
		$result=$connection2->prepare($sql);
		$result->execute();
}
if($_REQUEST['action']=='delete_ps'){
	$sid=$_REQUEST['sid']+0;
	$month=$_REQUEST['month'];
	$year=$_REQUEST['year']+0;
	$sql="DELETE FROM `lakshyasalarymaster` WHERE `staff_id`=$sid AND `month`=$month AND `year_id`=$year";
	$result=$connection2->prepare($sql);
		$result->execute();
}
if($_REQUEST['action']=='add_ps'){
		
		
		$data=substr($_REQUEST['data'],0,strlen($_REQUEST['data'])-1);
		$data_a=explode('/',$data);
		$rule_a=array();
		$month=$_REQUEST['month']+0;
		$year=$_REQUEST['year']+0;
		$sid=$_REQUEST['sid']+0;
		$sql="INSERT INTO `lakshyasalarymaster`(`master_id`, `staff_id`, `rule_id`, `amount`, `month`, `year_id`) VALUES ";
        $i=0;
		foreach($data_a as $d){
			if($i++!=0)
				$sql.=", ";
			$t=explode('_',$d);
			$rule=$t[2]+0;
			$am=$t[3]+0;
			$sql.="(NULL,$sid,$rule,$am,$month,$year)";
		}

		$result=$connection2->prepare($sql);
		$result->execute();
}
?>
