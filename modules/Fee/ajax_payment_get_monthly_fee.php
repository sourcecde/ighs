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

$monthfee=array();
if($_POST['action']=='getmonthfee')
{
	$studentEnrolmentID=$_REQUEST['studentEnrolmentID'];
	$monthno=$_REQUEST['monthno'];
	$gibbonSchoolYearID=$_REQUEST['financialyear'];
	$condition=$_REQUEST['condition'];
	if($condition!='only_trans') {
	$sql="SELECT fee_payable.*,fee_type_master.fee_type_desc 
	FROM fee_payable
	left join fee_type_master on fee_payable.fee_type_master_id=fee_type_master.fee_type_master_id 
	WHERE month_no=$monthno AND gibbonStudentEnrolmentID=$studentEnrolmentID AND gibbonSchoolYearID=$gibbonSchoolYearID  AND fee_payable.payment_staus='unpaid' AND `net_amount`!=0";
	//echo $sql;
		$result=$connection2->prepare($sql);
		$result->execute();
		$dboutbut=$result->fetchAll();
		foreach ($dboutbut as $value) {
			if(array_key_exists($value['fee_type_desc'],$monthfee)){
				$monthfee[$value['fee_type_desc']]+=$value['net_amount'];
			}
			else{
				$monthfee[$value['fee_type_desc']]=$value['net_amount'];
			}
		}
}
if($condition!='ex_trans') {
$month_name=array("1","jan","feb","mar","apr","may","jun","jul","aug","sep","oct","nov","dec");
$sql1="SELECT price FROM transport_month_entry where gibbonStudentEnrolmentID=$studentEnrolmentID AND month_name='".$month_name[$monthno]."' AND gibbonSchoolYearID=".$gibbonSchoolYearID." AND payment_master_id=0";
$result1=$connection2->prepare($sql1);
$result1->execute();
$transport_fee=$result1->fetch();
$monthfee['transport']=$transport_fee['price']>0?$transport_fee['price']:0;
}
//echo $sql;
header('Content-type: application/json');
echo json_encode($monthfee);
	
}

//this is for payment
if($_POST['action']=='payment')
{
    $sql1="SELECT login_userid FROM login_user where id=1";
    $result1=$connection2->prepare($sql1);
    $result1->execute();
    $loginuser=$result1->fetch();
	
	$fine_amount=$_REQUEST['fine_amount'];
	$total_amount=$_REQUEST['total_amount'];
	$payment_date=$_REQUEST['payment_date'];
	$payment_mode=$_REQUEST['payment_mode'];
	$order_id=$_REQUEST['order_id'];
	$tracking_id=$_REQUEST['tracking_id'];
	$bankID=$_REQUEST['bankID'];
	$cheque_no=$_REQUEST['cheque_no'];
	$cheque_date=$_REQUEST['cheque_date'];
	$cheque_bank=$_REQUEST['cheque_bank'];
	$studentEnrolmentID=$_REQUEST['studentEnrolmentID'];
	$gibbonPersonID=$_REQUEST['gibbonPersonID'];
	$gibbonSchoolYearID=$_REQUEST['schoolyear'];
	$transport=$_REQUEST['transport'];
	$condition=$_REQUEST['condition'];
	
	if($_REQUEST['vouchar_no']=='')	{		$vouchar_no=generateVoucherNumber($connection2,$payment_date);	}	else 	{		$vouchar_no=$_REQUEST['vouchar_no'];	}

try {
	$dataFile=array("gibbonPersonID"=>$gibbonPersonID,"gibbonStudentEnrolmentID"=>$studentEnrolmentID, "fine_amount"=>$fine_amount,"total_amount"=>($total_amount-$fine_amount),"net_total_amount"=>$total_amount,"payment_date"=>$payment_date,"voucher_number"=>$vouchar_no,"payment_mode"=>$payment_mode,'bankID'=>$bankID,'cheque_no'=>$cheque_no,'cheque_date'=>$cheque_date,'cheque_bank'=>$cheque_bank,'payment_date'=>$payment_date,'gibbonSchoolYearID'=>$gibbonSchoolYearID); 
	$sqlFile="Insert into  payment_master SET login_userid ='".$loginuser['login_userid']."', gibbonPersonID=:gibbonPersonID,gibbonStudentEnrolmentID=:gibbonStudentEnrolmentID,fine_amount=:fine_amount,total_amount=:total_amount,net_total_amount=:net_total_amount,voucher_number=:voucher_number,payment_mode=:payment_mode,bankID=:bankID,cheque_no=:cheque_no,cheque_date=:cheque_date,cheque_bank=:cheque_bank,payment_date=:payment_date,gibbonSchoolYearID=:gibbonSchoolYearID" ;
	$resultFile=$connection2->prepare($sqlFile);
	$resultFile->execute($dataFile);
	}
	catch(PDOException $e) { }
	$payment_master_id=$connection2->lastInsertId();
	$returnvouchr=getVoucherNo($connection2,$payment_master_id);
	
	try {
		$sqlFile="INSERT INTO `lakshyasmsgroup`(`gibbonPersonID`, `groupID`,`ref_id`) VALUES ('$gibbonPersonID','2','$payment_master_id')" ;
		$resultFile=$connection2->prepare($sqlFile);
		$resultFile->execute();
	}
	catch(PDOException $e) { }
	
	if(isset($_REQUEST['montharr'])){
		$montharr=implode(",", $_REQUEST['montharr']);
		
		if($condition!='only_trans') {
		//update fee payable table
				try {
						foreach($_REQUEST['montharr'] as $a){
								$sqlFile="UPDATE fee_payable SET payment_staus='paid',payment_master_id=".$payment_master_id.",payment_date='".$payment_date."',voucher_number=".$returnvouchr." where month_no=".$a." AND gibbonStudentEnrolmentID=".$studentEnrolmentID." AND gibbonSchoolYearID=".$gibbonSchoolYearID." AND `payment_staus`='unpaid'";
								
								$resultFile=$connection2->prepare($sqlFile);
								$resultFile->execute();
						}
					}
					catch(PDOException $e) { } 
		}

		//for transport 
		if($transport==1)
		{ 
			try {
			$monthnamearr=$_REQUEST['monthnamearr'];
			$monthnamearrstr=implode(",", $monthnamearr);
					$sqlFile="UPDATE transport_month_entry SET payment_master_id=".$payment_master_id." WHERE month_name IN (".$monthnamearrstr.") AND gibbonStudentEnrolmentID=".$studentEnrolmentID." AND gibbonSchoolYearID=$gibbonSchoolYearID AND `transport_month_entry`.`payment_master_id`=0";
					$resultFile=$connection2->prepare($sqlFile);
					$resultFile->execute();

			}
			catch(PDOException $e) { }
			
			try {
			$sqlFile="SELECT GROUP_CONCAT(transport_month_entryid) AS transport_month_entryidstr FROM transport_month_entry WHERE month_name IN (".$monthnamearrstr.") AND gibbonStudentEnrolmentID=".$studentEnrolmentID;
			$result=$connection2->prepare($sqlFile);
			$result->execute();
			$dboutbut=$result->fetch();
			$transport_month_entryidstr=$dboutbut['transport_month_entryidstr'];
			}
			catch(PDOException $e) { }
			
			try {
			$sqlFile="UPDATE payment_master SET transport_month_entryid='".$transport_month_entryidstr."' WHERE payment_master_id=".$payment_master_id;
			$resultFile=$connection2->prepare($sqlFile);
			$resultFile->execute();
			}
			catch(PDOException $e) { }
			
		}
	}
	$specialFee=$_REQUEST['specialFee'];
	if($specialFee['amount']>0){
		$month_name=strtolower(date('M'));
		$month_no=date('n');
		try {
		$sqlFile="SELECT `fee_type_desc` AS sn FROM `fee_type_master` WHERE `fee_type_master_id`=".$specialFee['id'];
		$resultFile=$connection2->prepare($sqlFile);
		$resultFile->execute();
		$shortName=$resultFile->fetch();
		}
		catch(PDOException $e) { }
		try {
		$sqlFile="INSERT INTO `fee_payable`(`fee_payable_id`, `gibbonSchoolYearID`, `gibbonPersonID`, `gibbonStudentEnrolmentID`, `rule_id`, `fee_type_master_id`, `month_no`, `month_name`, `amount`, `concession`, `net_amount`, `payment_staus`, `created_date`, `payment_master_id`, `payment_date`, `voucher_number`, `fee_type_short_name`) 
										VALUES (NULL,$gibbonSchoolYearID,$gibbonPersonID,0,0,{$specialFee['id']},$month_no,'$month_name',{$specialFee['amount']},0,{$specialFee['amount']},'paid',CURRENT_TIMESTAMP,$payment_master_id,'$payment_date','$returnvouchr','{$shortName['sn']}')";
		$resultFile=$connection2->prepare($sqlFile);
		$resultFile->execute();
		}
		catch(PDOException $e) { 
		echo $e;
		}
		if($specialFee['id']==41){
			$sql="SELECT * FROM `leftstudenttracker` WHERE `student_id`=".$gibbonPersonID;
			$resultcount=$connection2->prepare($sql);
			$resultcount->execute();
			if($resultcount->rowCount()==0){
				$sqlFile="INSERT INTO `leftstudenttracker`(`date_created`,`_id`, `student_id`, `studentName`, `isLeft`, `yearOfLeaving`,`leavingReason`,`hasTC`) VALUES ('".date('Y-m-d')."',NULL,'$gibbonPersonID',(SELECT `preferredName` FROM `gibbonperson` WHERE `gibbonPersonID`=$gibbonPersonID),'Y','$gibbonSchoolYearID','TC Fee Received','N')";
				$resultFile=$connection2->prepare($sqlFile);
				$resultFile->execute();
			}
			//$sqlFile="UPDATE `leftstudenttracker` SET `studentName` =  WHERE `student_id` = $gibbonPersonID";
			//$resultFile=$connection2->prepare($sqlFile);
			//$resultFile->execute();
			$sqlFile="UPDATE `gibbonperson` SET `status` = 'Left' WHERE `gibbonperson`.`gibbonPersonID` = $gibbonPersonID;";
			$resultFile=$connection2->prepare($sqlFile);
			$resultFile->execute();
		}
	}
	if($payment_mode=='cheque'){
			$sql1="INSERT INTO `cheque_master`(`cheque_master_id`, `cheque_no`, `cheque_date`, `bankMasterID`, `amount`, `payment_master_id`, `cheque_status_id`) VALUES (NULL,'$cheque_no','$cheque_date',$cheque_bank,$total_amount,$payment_master_id,1)";
			$result1=$connection2->prepare($sql1);
			$result1->execute();
	}
	else if($payment_mode=='online'){
	        $sql1="DELETE FROM `lakshya_online_payment_reference` WHERE `order_id`=$order_id";
	        $result1=$connection2->prepare($sql1);
			$result1->execute();
			$sql1="INSERT INTO `lakshya_online_payment_reference`(`id`, `order_id`, `tracking_id`, `bank_ref_number`, `status`, `personId`, `Time`, `amount`, `paidAmount`, `yearId`, `months`, `fine`, `payment_master_id`) 
			        VALUES (NULL,'$order_id','$tracking_id','$cheque_no','Success','$gibbonPersonID',NOW(),'$total_amount','$total_amount','$gibbonSchoolYearID','$montharr','$fine_amount','$payment_master_id')";
		//	$result1=$connection2->prepare($sql1);
		//	$result1->execute();
	}
	echo $payment_master_id."_".$returnvouchr; 
	
}

if($_POST['action']=='deletepayment')
{
	$payment_master_id=$_REQUEST['payment_master_id'];
try {
	$sqlFile="SELECT payment_mode,net_total_amount FROM payment_master where payment_master_id=".$payment_master_id;
	$resultFile=$connection2->prepare($sqlFile);
	$resultFile->execute();
	$pmData=$resultFile->fetch();
	}
	catch(PDOException $e) { }
try {
	$sqlFile="SELECT * FROM fee_payable where fee_type_master_id='41' AND payment_master_id=".$payment_master_id;
	$resultFile=$connection2->prepare($sqlFile);
	$resultFile->execute();
	$tcData=$resultFile->fetch();
	}
	catch(PDOException $e) { }
if(!empty($tcData)){
	try{
	$sqlFile="DELETE FROM `leftstudenttracker` WHERE `student_id`=".$tcData['gibbonPersonID'];
	$resultFile=$connection2->prepare($sqlFile);
	$resultFile->execute();
	$sqlFile="UPDATE `gibbonperson` SET `status`='Full' WHERE `gibbonPersonID`=".$tcData['gibbonPersonID'];
	$resultFile=$connection2->prepare($sqlFile);
	$resultFile->execute();
	}
	catch(PDOException $e){ }
}
if($pmData['payment_mode']=='cheque'){
	$amount=$pmData['net_total_amount'] + 0;
	$sqlFile="UPDATE `cheque_master` SET `amount` = `amount` - $amount  WHERE `payment_master_id` LIKE '%".$payment_master_id."%'";
	echo $sqlFile;
	$resultFile=$connection2->prepare($sqlFile);
	$resultFile->execute();
}
try {
	$sqlFile="DELETE FROM `cheque_master` WHERE `amount`=0";
	$resultFile=$connection2->prepare($sqlFile);
	$resultFile->execute();
	}
catch(PDOException $e) { }
try {
	$sqlFile="UPDATE fee_payable SET payment_staus='unpaid',payment_master_id=0,payment_date='0000-00-00' where payment_master_id=$payment_master_id AND `rule_id`>0";
	$resultFile=$connection2->prepare($sqlFile);
	$resultFile->execute();
	}
	catch(PDOException $e) { }
	
try {
	$sqlFile="DELETE from  payment_master where payment_master_id=".$payment_master_id;
	$resultFile=$connection2->prepare($sqlFile);
	$resultFile->execute();
	}
	catch(PDOException $e) { }
try {
	$sqlFile="UPDATE transport_month_entry SET payment_master_id=0 WHERE payment_master_id=".$payment_master_id;
	$resultFile=$connection2->prepare($sqlFile);
	$resultFile->execute();
	}
	catch(PDOException $e) { } 
try {
	$sqlFile="DELETE from  `fee_payable` where payment_master_id=$payment_master_id AND `rule_id`=0";
	$resultFile=$connection2->prepare($sqlFile);
	$resultFile->execute();
	}
	catch(PDOException $e) { }

}
function generateVoucherNumber($connection2,$payment_date)
{
	$sql="SELECT Max(voucher_number) as tableid FROM payment_master where payment_date='".$payment_date."' AND `voucher_number`!=0";
	$result=$connection2->prepare($sql);
	$result->execute();
	$dboutbut=$result->fetch();
	$date=explode("-", $payment_date);
	$dboutbut['tableid']=(int)substr($dboutbut['tableid'],-3,3);
	if($dboutbut['tableid']==0)
	{
		$tableid='001';
	}
	else 
	{
		$tableid=$dboutbut['tableid']+1;
		$tableidlen=strlen($tableid);
		switch ($tableidlen) {
			case 1:
				$tableid='00'.$tableid;
			break;
			
			case 2:
				$tableid='0'.$tableid;
			break;
			
			case 3:
				$tableid=$tableid;
			break;
		}
	}
	
	$voucharnumber=$date[2].$date[1].$tableid;
	return $voucharnumber;
}

function getVoucherNo($connection2,$id)
{
	$sql="SELECT voucher_number FROM payment_master where payment_master_id=".$id;
	$result=$connection2->prepare($sql);
	$result->execute();
	$dboutbut=$result->fetch();
	return $dboutbut['voucher_number'];
}
//This is for check Lock
if($_POST['action']=='checklock')
{
$payment_date=$_REQUEST['payment_date'];
$payment_date_s=substr($payment_date,0,8)."__";
$sql="SELECT COUNT(`payment_master_id`) AS count FROM `payment_master` WHERE `lock` = 1 AND payment_date like '{$payment_date_s}'";	
$result=$connection2->prepare($sql);
$result->execute();
$row=$result->fetch();
echo $row['count'];
}
if($_POST['action']=='checkPaymentDate'){
	extract($_POST);
	$sql="SELECT `gibbonSchoolYearID` FROM `gibbonschoolyear` WHERE `gibbonSchoolYearID`=$year AND `firstDay`< '$payment_date'";
	$result=$connection2->prepare($sql);
	$result->execute();
	echo $result->rowCount();
}
?>