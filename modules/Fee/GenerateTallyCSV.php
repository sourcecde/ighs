<?php
if($_POST){
$startDate=convertDate($_POST['startDate']);
$endDate=convertDate($_POST['endDate']);

#region  Variables
global $DbName;
global $paymentDb;
global $transportDb;
global $feePayebleDb;
$TallyDb=array();
#end

#region Data Model
class Tally{
	public function __construct($id,$voucher_no,$date,$ref_no,$ref_date,$ledger,$amount,$type,$group_name,$instrument_no,$instrument_date,$transaction_type,$chequecrosscomment,$bankdate,$narration){
		$this->UNIQUE_ID=$id;
		$this->BASE_VCH_TYPE="receipt";
		$this->VCH_TYPE="receipt";
		$this->VCH_NO=$voucher_no;
		$this->VCH_DATE=$date;
		$this->REF_NO=$ref_no;
		$this->REF_DATE=$ref_date;
		$this->LEDGERNAME=$ledger;
		$this->AMOUNT=$amount;
		$this->AMTTYPE=$type;
		$this->GROUP_NAME_FOR_LEGDER=$group_name;
		$this->INSTRUMENT_NO=$instrument_no;
		$this->INSTRUMENT_DATE=$instrument_date;
		$this->TRANSACTION_TYPE=$transaction_type;
		$this->FAVOURING="";
		$this->CHEQUECROSSCOMMENT=$chequecrosscomment;
		$this->BANKDATE=$bankdate;
		$this->NARRATION=$narration;
		$this->COMPANYNAME="";
	}	
}
class Year{
	public function __construct($id,$name,$status){
		$this->id=$id;
		$this->name=$name;
		$this->status=$status;
	}
}
class PaymentMaster{
	public function __construct($id,$date,$amount,$voucher,$payment,$bank,$bank_acc,$fine,$year,$ref,$ref_date,$Db){
		$this->id=$id;
		$this->date=$date;
		$this->amount=$amount;
		$this->voucher=$voucher;
		$this->head=$payment!="cash"?$bank." (".$bank_acc.")":$payment;
		$this->acc_no=$bank_acc;
		$this->mode=$payment;
		$this->fine=$fine;
		$this->year=$year;
		$this->ref=$ref;
		$this->ref_date=$ref_date;
		$this->DbName=$Db;
	}
}
class Transport{
	public function __construct($id,$amount,$year){
		$this->paymentId=$id;
		$this->amount=$amount;
		$this->year=$year;
	}
}
class FeePayable{
	public function __construct($id, $year, $amount, $name){
		$this->paymentId=$id;
		$this->name=$name;
		$this->amount=$amount;
	}
}
#end

#region DbHelper Method
function GetPaymentDb($id,$date,$amount,$voucher,$payment,$bank,$bank_acc,$fine,$year,$ref,$ref_date){
	global $DbName;
	global $paymentDb;
	$paymentDb[$date][]=new PaymentMaster($id+0,$date,$amount,$voucher,$payment,$bank,$bank_acc,$fine,$year,$ref,$ref_date,$DbName);
}
function GetTransortDb($id,$amount,$year){
	global $DbName;
	global $transportDb;
	$transportDb[$DbName][$id+0]=new Transport($id+0,$amount,$year);
}
function GetFeePayableDb($id, $year,$paymentDate,$month, $amount, $name){
	global $DbName;
	global $feePayebleDb;
	$namePrefix= getYearPrefix($paymentDate, $year, $month);
	$name=$namePrefix.$name;
	$feePayebleDb[$DbName][$id+0][]=new FeePayable($id, $year, $amount, $name);
}
function GetChqPaymentDb($id,$date,$amount,$voucher,$payment,$bank,$bank_acc,$fine,$year,$ref,$ref_date){
	global $DbName;
	global $chqPaymentDb;
	$chqPaymentDb[$date][]=new PaymentMaster($id+0,$date,$amount,$voucher,$payment,$bank,$bank_acc,$fine,$year,$ref,$ref_date,$DbName);
}
#end

#region Sql Queries
$paymentSql=	"SELECT `payment_master_id`,`payment_date`,`net_total_amount`,`voucher_number`,`payment_mode`,`payment_bankaccount`.`accountName`,`payment_bankaccount`.`acc_no`,`fine_amount`,`gibbonschoolyear`.`name`,`cheque_no`,`cheque_date`
				FROM `payment_master` 
				JOIN `payment_bankaccount`ON `payment_master`.`bankID`=`payment_bankaccount`.`bankID` 
				JOIN `gibbonschoolyear` ON `payment_master`.gibbonSchoolYearID=`gibbonschoolyear`.`gibbonSchoolYearID`
				WHERE `payment_date` BETWEEN '$startDate' AND '$endDate'
				ORDER BY `payment_master`.`created_date`";
				
$transportSql=	"SELECT `transport_month_entry`.`payment_master_id`,sum(price),`gibbonschoolyear`.`status`
				FROM `transport_month_entry`  
				JOIN `payment_master` ON `transport_month_entry`.`payment_master_id`=`payment_master`.`payment_master_id`
				JOIN `gibbonschoolyear` ON `transport_month_entry`.`gibbonSchoolYearID`=`gibbonschoolyear`.`gibbonSchoolYearID` 
				where `transport_month_entry`.`payment_master_id`>0 
				AND `payment_master`.`payment_date` BETWEEN '$startDate' AND '$endDate' 
				group by `transport_month_entry`.`payment_master_id`";
				
$feeSql=		"SELECT `fee_payable`.`payment_master_id`, `gibbonschoolyear`.`name`,`payment_master`.`payment_date`, `fee_payable`.`month_no`,
						`fee_payable`.`net_amount`, `fee_type_master`.`fee_type_name`  
				FROM `fee_payable` 
				JOIN `payment_master` ON `fee_payable`.`payment_master_id`=`payment_master`.`payment_master_id` 
				JOIN `fee_type_master` ON `fee_payable`.fee_type_master_id=`fee_type_master`.`fee_type_master_id`
				JOIN `gibbonschoolyear` ON `fee_payable`.`gibbonSchoolYearID`=`gibbonschoolyear`.`gibbonSchoolYearID`
				WHERE `payment_master`.`payment_date` BETWEEN '$startDate' AND '$endDate'";
				
$chqPaymentSql=	"SELECT `payment_master_id`,`payment_date`,`net_total_amount`,`voucher_number`,`payment_mode`,`payment_bankaccount`.`accountName`,`payment_bankaccount`.`acc_no`,`fine_amount`,`gibbonschoolyear`.`status`,`cheque_no`,`cheque_date`
				FROM `payment_master` 
				JOIN `payment_bankaccount`ON `payment_master`.`bankID`=`payment_bankaccount`.`bankID` 
				JOIN `gibbonschoolyear` ON `payment_master`.gibbonSchoolYearID=`gibbonschoolyear`.`gibbonSchoolYearID`
				WHERE `payment_date` BETWEEN '$startDate' AND '$endDate' AND `payment_master_id` IN (SELECT `payment_master_id`  FROM `cheque_master` WHERE `cheque_status_id`=0)
				ORDER BY `payment_master`.`created_date`";
#end

#region Db Config

//Database connection information (Junior)
// $databaseServer1="localhost"; 
// $databaseUsername1="hir_ighs_lak_jr"; 
// $databasePassword1="P@ssword!23"; 
// $databaseName1="hir_ighs_lakshya_jr";
//Database connection information (Senior)
$databaseServer2="localhost"; 
$databaseUsername2="hir_ighs_lak_sr"; 
$databasePassword2="P@ssword!23"; 
$databaseName2="hir_ighs_lakshya_sr";

try {
  	// $connectionA=new PDO("mysql:host=$databaseServer1;dbname=$databaseName1;charset=utf8", $databaseUsername1, $databasePassword1);
	// $connectionA->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	// $connectionA->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	
	$connectionB=new PDO("mysql:host=$databaseServer2;dbname=$databaseName2;charset=utf8", $databaseUsername2, $databasePassword2);
	$connectionB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connectionB->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}
#end
	
#region Db1 Opearations
// $DbName="DbA";
// //PaymentDb
// $result1=$connectionA->prepare($paymentSql);
// $result1->execute();
// $result1->fetchAll(PDO::FETCH_FUNC,"GetPaymentDb");
// //TransportDb
// $result2=$connectionA->prepare($transportSql);
// $result2->execute();
// $result2->fetchAll(PDO::FETCH_FUNC,"GetTransortDb");
// //FeeDb
// $result3=$connectionA->prepare($feeSql);
// $result3->execute();
// $result3->fetchAll(PDO::FETCH_FUNC,"GetFeePayableDb");
// //ChqPaymentDb
// $result4=$connectionA->prepare($chqPaymentSql);
// $result4->execute();
// $result4->fetchAll(PDO::FETCH_FUNC,"GetChqPaymentDb");

// $result1=null;
// $result2=null;
// $result3=null;
// $result4=null;
// $connectionA=null;
#end	

#region Db2 Opearations
$DbName="DbB";
//PaymentDb
$result1=$connectionB->prepare($paymentSql);
$result1->execute();
$result1->fetchAll(PDO::FETCH_FUNC,"GetPaymentDb");
//TransportDb
$result2=$connectionB->prepare($transportSql);
$result2->execute();
$result2->fetchAll(PDO::FETCH_FUNC,"GetTransortDb");
//FeeDb
$result3=$connectionB->prepare($feeSql);
$result3->execute();
$result3->fetchAll(PDO::FETCH_FUNC,"GetFeePayableDb");
//ChqPaymentDb
$result4=$connectionB->prepare($chqPaymentSql);
$result4->execute();
$result4->fetchAll(PDO::FETCH_FUNC,"GetChqPaymentDb");

$result1=null;
$result2=null;
$result3=null;
$result4=null;
$connectionB=null;
#end
//print_r($feePayebleDb);
#region Logic For Creating Tally Data
if(!empty($paymentDb)){
	$date=$startDate;	
	while (strtotime($date) <= strtotime($endDate)) {
		$bankCount=0;
		$ref_no="";
		$ref_date="";
		$group_name="";
		$transaction_type="";
		$instrument_no="";
		$instrument_date="";
		$chequecrosscomment="";
		$bankdate="";
		$cashFeeHead=array();
		$onlineFeeHead=array();
		$cardFeeHead=array();
		$totalCash=0;
		$totalOnline=array();
		$totalCard=array();
		if(array_key_exists($date,$paymentDb)){
			foreach($paymentDb[$date] as $payment){
			    $ref_no="";
		        $ref_date="";
		        $group_name="";
	        	$transaction_type="";
		        $instrument_no="";
		        $instrument_date="";
		        $chequecrosscomment="";
		        $bankdate="";
				$name= "";
				//$name=$payment->year=='Current'?'':($payment->year=='Past'?'Arr. ': 'Adv. ');
				//For Bank
				if($payment->mode=='cheque'){
					$voucher=GenerateVoucher('B',$date,++$bankCount);
					$bankFeeHead=array();
					if(array_key_exists($payment->DbName,$feePayebleDb)){
						if(array_key_exists($payment->id,$feePayebleDb[$payment->DbName])){
							foreach($feePayebleDb[$payment->DbName][$payment->id] as $fee){
								if(array_key_exists($fee->name,$bankFeeHead)){
									$bankFeeHead[$fee->name]+=$fee->amount;
								}
								else{
									$bankFeeHead[$fee->name]=$fee->amount;
								}
							}
						}
					}
					if (!empty($transportDb)) {
						if(array_key_exists($payment->DbName,$transportDb)){
							if(array_key_exists($payment->id,$transportDb[$payment->DbName])){
								$bankFeeHead['Transport']=$transportDb[$payment->DbName][$payment->id]->amount;
							}
						}
					}
					if($payment->fine > 0){
						$bankFeeHead['Fine']=$payment->fine;
					}
					//echo $payment->mode;
					$instrument_no=$payment->ref;
					$instrument_date=$payment->ref_date;
					$transaction_type="Cheque";
					$id=GenerateUniqueID("B",$payment->id);
					
					$TallyDb[]=new Tally($id,$voucher,$date,$ref_no,$ref_date,ucwords($payment->head),$payment->amount,'DR',"Bank Accounts",$instrument_no,$instrument_date,$transaction_type,$chequecrosscomment,$bankdate,"Bank Collection(Chq No. - $instrument_no)");
					foreach($bankFeeHead as $head=>$amount){
						$feeHeadName=$head=='Fine'? $head :$name.ucwords($head);
						$TallyDb[]=new Tally($id,$voucher,$date,$ref_no,$ref_date,$feeHeadName,$amount,'CR',"Bank Accounts",$instrument_no,$instrument_date,$transaction_type,$chequecrosscomment,$bankdate,"Bank Collection(Chq No. - $instrument_no)");
					}
				}
				else if($payment->mode=="online"){
					//$voucher=GenerateVoucher('B',$date,++$bankCount);
					//$bankFeeHead=array();
					if(array_key_exists($payment->DbName,$feePayebleDb)){
						if(array_key_exists($payment->id,$feePayebleDb[$payment->DbName])){
							foreach($feePayebleDb[$payment->DbName][$payment->id] as $fee){
								if(array_key_exists($fee->name,$onlineFeeHead[$payment->head])){
									$onlineFeeHead[$payment->head][$fee->name]+=$fee->amount;
								}
								else{
									$onlineFeeHead[$payment->head][$fee->name]=$fee->amount;
								}
							}
						}
					}
					if (!empty($transportDb)) {
						if(array_key_exists($payment->DbName,$transportDb)){
							if(array_key_exists($payment->id,$transportDb[$payment->DbName])){
							    if(array_key_exists("Transport",$onlineFeeHead[$payment->head])){
									$onlineFeeHead[$payment->head]['Transport']+=$transportDb[$payment->DbName][$payment->id]->amount;
								}
								else{
									$onlineFeeHead[$payment->head]['Transport']=$transportDb[$payment->DbName][$payment->id]->amount;
								}
							}
						}
					}
					if($payment->fine > 0){
					    if(array_key_exists("Fine",$onlineFeeHead[$payment->head])){
						    $onlineFeeHead[$payment->head]['Fine']+=$payment->fine;
					    }
					    else{
					        $onlineFeeHead[$payment->head]['Fine']+=$payment->fine;
					    }
						
					}
					if(array_key_exists($payment->head,$totalOnline)){
						$totalOnline[$payment->head]["amount"]+=$payment->amount;
					}
					else{
						$totalOnline[$payment->head]["amount"]=$payment->amount;
						$totalOnline[$payment->head]["Acc_no"]=$payment->acc_no;
					}
				}
				else if($payment->mode=="card"){

					//$voucher=GenerateVoucher('B',$date,++$bankCount);
					//$bankFeeHead=array();
					if(array_key_exists($payment->DbName,$feePayebleDb)){
						if(array_key_exists($payment->id,$feePayebleDb[$payment->DbName])){
							foreach($feePayebleDb[$payment->DbName][$payment->id] as $fee){
								if(array_key_exists($fee->name,$cardFeeHead[$payment->head])){
									$cardFeeHead[$payment->head][$fee->name]+=$fee->amount;
								}
								else{
									$cardFeeHead[$payment->head][$fee->name]=$fee->amount;
								}
							}
						}
					}
					if (!empty($transportDb)) {
						if(array_key_exists($payment->DbName,$transportDb)){
							if(array_key_exists($payment->id,$transportDb[$payment->DbName])){
							    if(array_key_exists("Transport",$cardFeeHead[$payment->head])){
								$cardFeeHead[$payment->head]['Transport']+=$transportDb[$payment->DbName][$payment->id]->amount;
								}
								else{
								$cardFeeHead[$payment->head]['Transport']=$transportDb[$payment->DbName][$payment->id]->amount;
								}
							}
						}
					}
					if($payment->fine > 0){
						 if(array_key_exists("Fine",$cardFeeHead[$payment->head])){
						    $cardFeeHead[$payment->head]['Fine']+=$payment->fine;
					    }
					    else{
					        $cardFeeHead[$payment->head]['Fine']+=$payment->fine;
					    }
					}
					if(array_key_exists($payment->head,$totalCard)){
						$totalCard[$payment->head]["amount"]+=$payment->amount;
					}
					else{
						$totalCard[$payment->head]["amount"]=$payment->amount;
						$totalCard[$payment->head]["Acc_no"]=$payment->acc_no;
					}
				}
				else{
					if(array_key_exists($payment->DbName,$feePayebleDb)){
						if(array_key_exists($payment->id,$feePayebleDb[$payment->DbName])){
							foreach($feePayebleDb[$payment->DbName][$payment->id] as $fee){
								if(array_key_exists(ucwords($name.$fee->name),$cashFeeHead)){
									$cashFeeHead[ucwords($name.$fee->name)]+=$fee->amount;
								}
								else{
									$cashFeeHead[ucwords($name.$fee->name)]=$fee->amount;
								}
							}
						}
					}
					if (!empty($transportDb)) {
						if(array_key_exists($payment->DbName,$transportDb)){
							if(array_key_exists($payment->id,$transportDb[$payment->DbName])){
								if(array_key_exists($name.'Transport',$cashFeeHead)){
									$cashFeeHead[$name.'Transport']+=$transportDb[$payment->DbName][$payment->id]->amount;
								}
								else{
									$cashFeeHead[$name.'Transport']=$transportDb[$payment->DbName][$payment->id]->amount;
								}
							}
						}
					}
					if($payment->fine > 0){
						if(array_key_exists('Fine',$cashFeeHead)){
							$cashFeeHead['Fine']+=$payment->fine;
						}
						else{
							$cashFeeHead['Fine']=$payment->fine;
						}
					}
					$totalCash+=$payment->amount;
				}
			}
		    $instrument_date="";
			foreach($onlineFeeHead as $k=>$o){
				$voucher=GenerateVoucher('B',$date,++$bankCount);
				$id=GenerateVoucher('O',$date,$totalOnline[$k]["Acc_no"]);
				$instrument_no=GenerateInstrumentNo("ET",$date,$totalOnline[$k]["Acc_no"]);
				if($totalOnline[$k] != 0){
					$TallyDb[]=new Tally($id,$voucher,$date,$ref_no,$ref_date,ucwords($k),$totalOnline[$k]["amount"],'DR',"Bank Accounts",$instrument_no,$date,"ECS",$chequecrosscomment,$bankdate,"Bank Collection [Online - {$k}]");
				}
				foreach($o as $head=>$amount){
						$feeHeadName=$head=='Fine'? $head :$name.ucwords($head);
						$TallyDb[]=new Tally($id,$voucher,$date,$ref_no,$ref_date,$feeHeadName,$amount,'CR',"Bank Accounts",$instrument_no,$date,"ECS",$chequecrosscomment,$bankdate,"Bank Collection [Online - {$k}]");
				}
			}
			foreach($cardFeeHead as $k=>$o){
				$voucher=GenerateVoucher('B',$date,++$bankCount);
				$id=GenerateVoucher('CA',$date,$totalCard[$k]["Acc_no"]);
				$instrument_no=GenerateInstrumentNo("C",$date,$totalCard[$k]["Acc_no"]);
				if($totalCard[$k] != 0){
					$TallyDb[]=new Tally($id,$voucher,$date,$ref_no,$ref_date,ucwords($k),$totalCard[$k]["amount"],'DR',"Bank Accounts",$instrument_no,$date,"Card",$chequecrosscomment,$bankdate,"Bank Collection [Card- {$k}]");
				}
				foreach($o as $head=>$amount){
						$feeHeadName=$head=='Fine'? $head :$name.ucwords($head);
						$TallyDb[]=new Tally($id,$voucher,$date,$ref_no,$ref_date,$feeHeadName,$amount,'CR',"Bank Accounts",$instrument_no,$date,"Card",$chequecrosscomment,$bankdate,"Bank Collection [Card- {$k}]");
				}
			}
			$voucher=GenerateVoucher('C',$date,'');
			//PrintArr($cashFeeHead);
			if($totalCash != 0){
				$TallyDb[]=new Tally($voucher,$voucher,$date,$ref_no,$ref_date,'Cash',$totalCash,'DR','Cash-in-Hand',"","","",$chequecrosscomment,$bankdate,'Cash Collection');
			}
			foreach($cashFeeHead as $head=>$amount){
				$TallyDb[]=new Tally($voucher,$voucher,$date,$ref_no,$ref_date,$head,$amount,'CR','Cash-in-Hand',"","","",$chequecrosscomment,$bankdate,"Cash Collection");
			}
		}
		$date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
	}

}
if(!empty($chqPaymentDb)){
	$date=$startDate;
	$ref_no="";
		$ref_date="";
		$group_name="";
		$transaction_type="Cheque";
		$instrument_no="";
		$instrument_date="";
		$chequecrosscomment="";
		$bankdate="";
	while (strtotime($date) <= strtotime($endDate)) {
		//$cashFeeHead=array();
		$chqCount=0;
		if(array_key_exists($date,$chqPaymentDb)){
			foreach($chqPaymentDb[$date] as $payment){
				$name=$payment->year=='Current'?'':($payment->year=='Past'?'Arr. ': 'Adv. ');
				//For Bank
					$voucher=GenerateVoucher('B',$date,++$bankCount);
					$bankFeeHead=array();
					if(array_key_exists($payment->DbName,$feePayebleDb)){
						if(array_key_exists($payment->id,$feePayebleDb[$payment->DbName])){
							foreach($feePayebleDb[$payment->DbName][$payment->id] as $fee){
								if(array_key_exists($fee->name,$bankFeeHead)){
									$bankFeeHead[$fee->name]+=$fee->amount;
								}
								else{
									$bankFeeHead[$fee->name]=$fee->amount;
								}
							}
						}
					}
					if (!empty($transportDb)) {
						if(array_key_exists($payment->DbName,$transportDb)){
							if(array_key_exists($payment->id,$transportDb[$payment->DbName])){
								$bankFeeHead['Transport']=$transportDb[$payment->DbName][$payment->id]->amount;
							}
						}
					}
					if($payment->fine > 0){
						$bankFeeHead['Fine']=$payment->fine;
					}

					$voucher=GenerateVoucher('RE',$date,++$chqCount);
					$id=GenerateUniqueID("RE",$payment->id);
					$TallyDb[]=new Tally($id,$voucher,$date,$ref_no,$ref_date,ucwords($payment->head),$payment->amount,'CR',"Bank Accounts",$payment->ref,$payment->ref_date,$transaction_type,$chequecrosscomment,$bankdate,'Cheque Returned');
					//$TallyDb[]=new Tally($voucher,$date,$voucher,ucwords($payment->head),$payment->amount,'CR',"Cheque Returned (Ref No. - $payment->ref)");
					foreach($bankFeeHead as $head=>$amount){
						$TallyDb[]=new Tally($id,$voucher,$date,$ref_no,$ref_date,$name.ucwords($head),$amount,'DR',"Bank Accounts",$payment->ref,$payment->ref_date,$transaction_type,$chequecrosscomment,$bankdate,'Cheque Returned');
						//$TallyDb[]=new Tally($voucher,$date,$voucher,$name.ucwords($head),$amount,'DR','Fee Collection(Cheque Returned)');
					}
			}
			
		}
		$date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
	}

}
#end	

#region CSV generation 

if(!empty($TallyDb)){
	$header=array('UNIQUE ID', 'BASE_VCH_TYPE', 'VCH_TYPE', 'VCH_NO', 'VCH_DATE',  'REF_NO', 'REF_DATE', 'LEDGERNAME', 'AMOUNT', 'AMTTYPE', 'GROUP_NAME_FOR_LEGDER', 'INSTRUMENT_NO', 'INSTRUMENT_DATE', 'TRANSACTION_TYPE', 'FAVOURING', 'CHEQUECROSSCOMMENT', 'BANKDATE', 'NARRATION', 'COMPANYNAME');
	if (is_dir('TallyExort')==FALSE) {
		mkdir('TallyExort', 0777, TRUE) ;					
	}
	$filename="TallyExort/lakshya_tally_".date("d-m-Y").".csv";
	
	$myfile = fopen($filename, "w") or die("Unable to open file!");
	fputcsv($myfile, $header);
	foreach ($TallyDb as $fields) {
		fputcsv($myfile, array($fields->UNIQUE_ID,$fields->BASE_VCH_TYPE,$fields->VCH_TYPE,$fields->VCH_NO,$fields->VCH_DATE,"","",$fields->LEDGERNAME,$fields->AMOUNT,$fields->AMTTYPE,$fields->GROUP_NAME_FOR_LEGDER,$fields->INSTRUMENT_NO,$fields->INSTRUMENT_DATE,$fields->TRANSACTION_TYPE,"","","",$fields->NARRATION,""));
	}
	fclose($myfile); 
	$file=$filename;
	if (file_exists($file)) {
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="'.basename($file).'"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file));
		readfile($file);
		unlink($file);
		exit;
	}
}
/*echo "<pre>";
print_r($TallyDb);
echo "</pre>";*/
#end

}
#region Utility Functions
	function GenerateVoucher($type,$date,$add){
		$dateArr=explode('-',$date);
		return $type.$dateArr[0].$dateArr[1].$dateArr[2].$add;
	}
	function GenerateInstrumentNo($type,$date,$add){
	    $dateArr=explode('-',$date);
		return $type."-(".$dateArr[0]."/".$dateArr[1]."/".$dateArr[2].")-".$add;
	}
	function GenerateUniqueID($type,$id){
		return $type.$id;
	}
	function convertDate($date){
		$dateArr=explode('/',$date);
		return $dateArr[2]."-".$dateArr[1]."-".$dateArr[0];
	}
	function getFinancialYear($year,$month,$financialYearStartMonth){
		return $month<$financialYearStartMonth ? (intval($year)-1) : intval($year);
	}
	function getYearPrefix($paymentDate, $year, $month){
		$financialYearStartMonth=4;
		$dateArr=explode('-',$paymentDate);
		$paymentFinancialYear=getFinancialYear($dateArr[0], $dateArr[1], $financialYearStartMonth);
		$feeFinancialYear=getFinancialYear($year, $month, $financialYearStartMonth);
		
		if($feeFinancialYear<$paymentFinancialYear)
			return "Arr. ";
		else if($feeFinancialYear==$paymentFinancialYear)
			return "";
		else 
			return "Adv. ";
	}
#end
 ?>
