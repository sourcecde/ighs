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
if($_POST){
	if(isset($_POST['create_p_s'])){
		$myD=date('Y')."-".$_REQUEST['month_s']."-01";
		$myDate=date('Y-m-d',strtotime($myD));
		echo $myDate;
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
		$sql="SELECT `gibbonstaffID`,gibbonstaff.preferredName FROM `gibbonstaff`
		LEFT JOIN gibbonperson on gibbonperson.gibbonpersonID=gibbonstaff.gibbonpersonID"; 
		$sql.=" WHERE (gibbonperson.dateEnd IS NULL OR (gibbonperson.dateEnd<='".date('Y-m-t',strtotime($myDate))."' AND gibbonperson.dateEnd>='$myDate'))"; 
                $sql.=" ORDER BY gibbonstaff.priority";
    

		$result1=$connection2->prepare($sql);
		$result1->execute();
		$staff=$result1->fetchAll();
		}
		
	
		
		catch(PDOException $e){
			echo $e;
		}
			$year=$_REQUEST['year_s'];
			$month=$_REQUEST['month_s'];
					if($_REQUEST['duplicate_entry']==1){
						$sql4="DELETE FROM `lakshyasalarymaster` WHERE `month`=".$month." AND `year_id`=".$year;
						$result4=$connection2->prepare($sql4);
						$result4->execute();
						$sql5="DELETE FROM `lakshyasalaryadvance` WHERE `salaryMonth`=".$month." AND `schoolYearID`=".$year;
						$result5=$connection2->prepare($sql5);
						$result5->execute();
						
					}
		foreach($staff as $s){
				$s_id=$s['gibbonstaffID']+0;
				
					try{
					$sql="INSERT INTO `lakshyasalarymaster`(`master_id`, `staff_id`, `rule_id`, `amount`, `month`, `year_id`) VALUES "; 
						$i=0;
					foreach($rule as $r){
						$r_id=$r['rule_id']+0;
						$r_s=$s_id."_".$r_id;
						$r_v=$_REQUEST[$r_s]+0;
						$sql.=$i++!=0?',':'';
						$sql.=" (NULL,".$s['gibbonstaffID'].",".$r['rule_id'].",".$r_v.",".$month.",".$year.")";
					}
					$a=$s_id."_deductn";
					$r_d=$_REQUEST[$a]+0;
					$sql.=", (NULL,".$s['gibbonstaffID'].",99,".$r_d.",".$month.",".$year.")";
					$b=$s_id."_grossPF";
					$r_d=$_REQUEST[$b]+0;
					$sql.=", (NULL,".$s['gibbonstaffID'].",98,".$r_d.",".$month.",".$year.")";
					$result3=$connection2->prepare($sql);
					$result3->execute();
					//echo $sql;
					}
					catch(PDOException $e){
						echo $e;
					}
		}
		try {
			$sql.=", (NULL,0,97,{$_REQUEST['percentagePF']},".$month.",".$year."), (NULL,0,96,{$_REQUEST['percentageESI']},".$month.",".$year.")";
			$result3=$connection2->prepare($sql);
			$result3->execute();
					//echo $sql;
			}
			catch(PDOException $e){
				echo $e;
			}
		$url=$_SESSION[$guid]["absoluteURL"]."/index.php?q=/modules/Staff/create_pay_structure.php";
		header("Location:{$url}");
	}


	else if($_REQUEST['action']=='check_duplicate')	{
		try{
			$sql="SELECT `master_id` FROM `lakshyasalarymaster` WHERE `month`=".$_REQUEST['month']." AND `year_id`=".$_REQUEST['year'];
		$result=$connection2->prepare($sql);
		$result->execute();
		echo $result->rowcount();
		}
		catch(PDOException $e){
			echo $e;
		}
	}
	else if($_REQUEST['action']=='fetch_data')	{
		$month=$_REQUEST['month'];
		$year=$_REQUEST['year'];
		switch($month)
		{
			case  1:
				$month=12;
				$year-=1;
				break;
			case 4:
			case 2:
			case 3:
			case 5:
			case 6:
			case 7:
			case 8:
			case 9:
			case 10:
			case 11:
			case 12:
				$month-=1;
				break;	
		}
			//echo "m: ".$month." y: ".$year;
		try{
			$sql="SELECT lakshyasalarymaster.* FROM `lakshyasalarymaster`
			 LEFT JOIN gibbonstaff ON gibbonstaff.gibbonstaffID=lakshyasalarymaster.staff_id
			WHERE  `month`=".$month." AND `year_id`=".$year;
		$result1=$connection2->prepare($sql);
		$result1->execute();
		$data=$result1->fetchAll();
		}
		catch(PDOException $e){
			echo $e;
		} 
		$out_str='';
		$i=0;
		foreach($data as $s){
			if($i++!=0)
			$out_str.="#";
			$out_str.=$s['staff_id']."_".$s['rule_id']."@".$s['amount'];
		}
		echo $out_str;
	}
	else if($_REQUEST['action']=='create_table'){
		$myD=date('Y')."-".$_REQUEST['month']."-01";
		$myDate=date('Y-m-d',strtotime($myD));
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
		$sql="SELECT `gibbonstaffID`,priority,gibbonstaff.preferredName FROM `gibbonstaff`
		LEFT JOIN gibbonperson on gibbonperson.gibbonpersonID=gibbonstaff.gibbonpersonID";
		$sql.=" WHERE (gibbonperson.dateEnd IS NULL OR (gibbonperson.dateEnd<='".date('Y-m-t',strtotime($myDate))."' AND gibbonperson.dateEnd>='$myDate'))"; 
		$sql.=" ORDER BY gibbonstaff.priority";
		$result1=$connection2->prepare($sql);
		$result1->execute();
		$staff=$result1->fetchAll();
		}
		catch(PDOException $e){
			echo $e;
		}
		$firstDayOfMonth=date('Y')."-{$_REQUEST['month']}-01";
		try{
		$sql="SELECT a.`staffID`, a.`amount` AS total,a.`nEMI`,count(b.`advanceID`) AS N, SUM(b.`amount`) AS T,a.`advanceID` AS emiAdvanceID 
				FROM `lakshyasalaryadvance` a  
				LEFT JOIN `lakshyasalaryadvance` b ON a.`advanceID` =b.`emiAdvanceID`
				WHERE a.`isPaid`='N' AND a.`date`<='$firstDayOfMonth'  
                GROUP BY a.`advanceID`";
		$result1=$connection2->prepare($sql);
		$result1->execute();
		$advanceD=$result1->fetchAll();
		//print "</pre>";
		//print_r($advanceD);
		//print "</pre>";
		}
		catch(PDOException $e){
			echo $e;
		}
		$advanceData=array();
		foreach($advanceD as $a){
			$k=$a['staffID']+0;
			$advanceData[$k]=$a;
		}
		$table="<table width='100%' cellpadding='0' cellspacing='0'><tr><th>Staff Name</th>";
		foreach($rule as $r){
				$table.="<th>".$r['caption']."</th>";
		}
		$table.="<th>Gross PF</th><th>Advance</th></tr>";
				foreach($staff as $s){
					$s_id=$s['gibbonstaffID']+0;
					/* EMI For Advance */
					$advance=0;
					if(array_key_exists($s_id,$advanceData)){
						//echo $s_id;
						$r=$advanceData[$s_id]['nEMI']-$advanceData[$s_id]['N'];
						if($r==1){
							$advance=$advanceData[$s_id]['total']-$advanceData[$s_id]['T'];
						}
						else{
							$emi=$advanceData[$s_id]['total']/ $advanceData[$s_id]['nEMI'];
							//echo "</br>";
							$advance=$emi;
							if($advanceData[$s_id]['T']<$emi*$advanceData[$s_id]['N'])
								$advance+=$emi*$advanceData[$s_id]['N']-$advanceData[$s_id]['T'];
								//echo $emi*$advanceData[$s_id]['N']." ------ ".$advanceData[$s_id]['T']."<br>";
						}
					}
					/* EMI For Advance */
				 $table.="<tr>";
					$table.= "<td>  ".$s['preferredName']."</td>";
						foreach($rule as $r){
							$r_id=$r['rule_id']+0;
							$table.= "<td><input type='text' name='".$s_id."_".$r_id."' id='".$s_id."_".$r_id."' value='' style='width:100%'></td>";
						}
						$table.= "<td><input type='text' name='".$s_id."_grossPF' id='".$s_id."_98' value='' style='width:100%'></td>";
					
						$table.= "<td><input type='text' name='".$s_id."_deductn' id='".$s_id."_deductn' value='$advance' style='width:100%'></td>";
				 $table.= "</tr>";	
				}
		$colspan=sizeof($rule)+2;
		$table.="<tr><td colspan='2'><b>ESI Percentage </b><input type='text' name='percentageESI' id='percentageESI' style='width:50px' required></td>
					<td colspan='2'><b>PF Percentage </b><input type='text' name='percentagePF' id='percentagePF' style='width:50px' required></td>
					<td colspan='$colspan-3'><center><input type='submit' name='create_p_s' value='Create'></center></td></tr>";
		echo $table;
	}
} 
?>
