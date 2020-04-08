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
	$data=array();
foreach($_POST as $key => $value){
	if($value!='Save'){
		$temp=explode("_",$key);
		$staffid=$temp[1];
		$ruleid=$temp[2];
		$data[$staffid][$ruleid]=$value;
	}
}
			try{
			$sql1="SELECT * FROM `lakshyastaffattendancerule`";
			$result1=$connection2->prepare($sql1);
			$result1->execute();
			$rule=$result1->fetchAll();
			}
			catch(PDOException $e){
				echo $e;
			}

			try{
			$sql="SELECT `gibbonStaffID` FROM `gibbonstaff`
			 ORDER BY gibbonstaff.priority";
			$result1=$connection2->prepare($sql);
			$result1->execute();
			$staffs=$result1->fetchAll();
			}
			catch(PDOException $e){
				echo $e;
			}
			$i=0;
		$timeStamp=time();	
		$query="INSERT INTO `lakshyastaffleavecredit`(`credit_id`, `staff_id`, `rule_id`, `value`, `timeStamp`) VALUES ";	
		foreach($staffs as $staff){
			$sid=$staff['gibbonStaffID']+0;
			if(!array_key_exists($sid,$data))
				continue;
			foreach($rule as $r){
				$rid=$r['rule_id']+0;
				if(!array_key_exists($rid,$data[$sid]))
				continue;
				$val=$data[$sid][$rid];
					if($val=="")
						continue;
				if($i++!=0)
					$query.=", ";
			$query.="(NULL,$sid,$rid,$val,$timeStamp)";
			}
		}
	try{	
	$result=$connection2->prepare($query);
	$result->execute();
	}
	catch(PDOException $e){
		echo $e;
	}
	$url="{$_SESSION[$guid]["absoluteURL"]}/index.php?q=/modules/{$_SESSION[$guid]["module"]}/credit_leave.php";
	header("Location:{$url}&success=true");
}
ob_flush();
?>