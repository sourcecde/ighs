<?php
ob_start();
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
if(isset($_REQUEST['payment_s'])){ 
    
			$year=$_REQUEST['year'];
			$month=$_REQUEST['month'];
			$date=$_REQUEST['date'];
		    $wd=$_REQUEST['working_day'];
		try{
		$sql="SELECT * FROM `lakshyasalaryrule` where active=1";
		$result=$connection2->prepare($sql);
		$result->execute();
		$rule=$result->fetchAll();
		}
		catch(PDOException $e){
			echo $e;
		}	
		try{
	    $sql="SELECT `gibbonStaffID`,gibbonperson.preferredName,gibbonrollgroup.name as section  FROM `gibbonstaff`
		LEFT JOIN gibbonperson on gibbonperson.gibbonPersonID=gibbonstaff.gibbonPersonID
		LEFT JOIN gibbonrollgroup on gibbonperson.gibbonRoleIDPrimary=gibbonrollgroup.gibbonRollGroupID
		ORDER BY gibbonstaff.priority, gibbonstaff.category, section DESC ";
		
		//WHERE gibbonperson.dateEnd IS NULL";
		$result1=$connection2->prepare($sql);
		$result1->execute();
		$staff=$result1->fetchAll();
		
		
		}
		catch(PDOException $e){
			echo $e;
		}	
		try {
		$sql4="SELECT * FROM `lakshyasalarymaster` WHERE `month`=".$month." AND `year_id`=".$year;
		$result4=$connection2->prepare($sql4);
		$result4->execute();
		$m_id=$result4->fetchAll();
		}
		catch(PDOException $e){
			echo $e;
		}
		$master_id=array();
		foreach($m_id as $m){
			$master_id[$m['staff_id']][$m['rule_id']]=$m['master_id'];
		}
		//echo $_REQUEST['overwrite']."<br>";
		/* For Overwrite */
		if($_REQUEST['overwrite']==1){
				try {
				$sql="DELETE FROM `lakshyasalaryattendance` WHERE `month`=".$month." AND `year_id`=".$year;
				$result=$connection2->prepare($sql);
				$result->execute();
				}
				catch(PDOException $e){
					echo $e;
				}
				try {
				$sql="DELETE FROM `lakshyasalarypayment` WHERE `master_id` IN (SELECT `master_id` FROM `lakshyasalarymaster` WHERE `month`=$month AND `year_id`=$year)";
				$result=$connection2->prepare($sql);
				$result->execute();
				}
				catch(PDOException $e){
					echo $e;
				}
				/* Advance */
				try {
				$sql="DELETE FROM `lakshyasalaryadvance` WHERE  `salaryMonth`=$month AND `schoolYearID`=$year";
				$result=$connection2->prepare($sql);
				$result->execute();
				}
				catch(PDOException $e){
					echo $e;
				}
		
		}
		
		/* For Overwrite */
		//print_r($master_id);
		    $advanceArray=array();
			foreach($staff as $s){
			    
				$s_id=$s['gibbonStaffID']+0;
					if(!array_key_exists($s_id,$master_id))
						continue;
					try{
					$sql5="INSERT INTO `lakshyasalarypayment`(`payment_id`, `master_id`, `paid_amount`, `date`) VALUES "; 
						$i=0;
					foreach($rule as $r){
						$r_id=$r['rule_id']+0;
						$r_s=$s_id."_".$r_id;
						if(!isset($_REQUEST[$r_s]))
							continue;
						$r_v=$_REQUEST[$r_s]+0;
						$sql5.=$i++!=0?',':'';
						$sql5.=" (NULL,".$master_id[$s_id][$r_id].",".$r_v.",'".$date."')";
					}
					$d=$s_id."_deduction";
					$p=$s_id."_grossPF";
					$sql5.=", (NULL,".$master_id[$s_id][99].",".$_REQUEST[$d].",'".$date."')";
					$sql5.=", (NULL,".$master_id[$s_id][98].",".$_REQUEST[$p].",'".$date."')";
					 
					/* For Advance */
					if($_REQUEST[$d]>0)
						array_push($advanceArray,array($s_id,$_REQUEST[$d],$date));
					/* For Advane */
					$result5=$connection2->prepare($sql5);
					//echo $sql5."<br>";
					$result5->execute();
					}
					catch(PDOException $e){
						echo $e;
					}
					try{

					 if(isset($_REQUEST[$ad])){
					     $_REQUEST[$ad]=$_REQUEST[$ad];
					 }else{
					     
					     $_REQUEST[$ad]=0;
					 }
					 
					$ad=$s_id."_ad";
					$sql6="INSERT INTO `lakshyasalaryattendance`(`staff_id`, `month`, `year_id`, `attended`) VALUES (".$s_id.",".$month.",".$year.",".$_REQUEST[$ad].")";
					$result6=$connection2->prepare($sql6);
					$result6->execute();
					//echo $sql6;
					}
					catch(PDOException $e){
						echo $e;
					}
				//echo "Done";	
			}
				/* For Advance */ 
				if(!empty($advanceArray)){
					$sql="SELECT `advanceID`,`staffID` FROM `lakshyasalaryadvance` WHERE `isPaid`='N'"; 
					$result=$connection2->prepare($sql);
					$result->execute();                
					$EMIs=$result->fetchAll();
					$emiD=array();
					foreach($EMIs as $e){
						$emiD[$e['staffID']+0]=$e['advanceID'];
					}
					$sql7="INSERT INTO `lakshyasalaryadvance`(`advanceID`, `staffID`, `amount`, `type`, `date`, `schoolYearID`, `nEMI`, `isPaid`, `emiAdvanceID`,`salaryMonth`) VALUES ";
					$query="";
					foreach($advanceArray as $a){
						$date=dateFormatter($a[2]);
						$advanceID=$emiD[$a[0]];
						$query.=$query!=""?", ":"";
						$query.="(NULL,{$a[0]},{$a[1]},'Cr','$date',$year,0,NULL,$advanceID,$month)";
					}
					$sql7.=$query;
					$result7=$connection2->prepare($sql7);
					$result7->execute();
				}
				
				
				/* For Advance */
			$salary_p_massage="Succesfully saved Staff salary payment";	
			$url=$_SESSION[$guid]["absoluteURL"].'/index.php?q=/modules/'.$_SESSION[$guid]["module"]."/payment_salary.php&salary_p_massage=$salary_p_massage";
			header("Location:{$url}");	
}
function dateFormatter($date){
	$tmp=explode('/',$date);
	return $tmp[2]."-".$tmp[1]."-".$tmp[0];
}
//ob_flush();
?>
