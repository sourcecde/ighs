<?php
 if(!$_POST) {
include "../../config.php" ;
$fromdate=DateConverter($_GET['fromdate']);
$todate=DateConverter($_GET['todate']);
$year_id=$_GET['year_id'];
$p_mode=$_GET['p_mode'];

try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}
$fee_data_arr=array();
$message="";
/* For Fee Data */
$sql="SELECT fee_payable.payment_date,SUM(net_amount) AS 'total_amount',fee_type_master.`fee_type_name`,gibbonschoolyear.status, payment_master.payment_mode
 FROM fee_payable
LEFT JOIN fee_type_master ON fee_type_master.`fee_type_master_id`=fee_payable.`fee_type_master_id`
LEFT JOIN gibbonschoolyear ON gibbonschoolyear.gibbonSchoolYearID=fee_payable.gibbonSchoolYearID
LEFT JOIN payment_master ON payment_master.payment_master_id=fee_payable.payment_master_id 
WHERE fee_payable.payment_date>='".$fromdate."' AND fee_payable.payment_date<='".$todate."'";
if($year_id!='')
	$sql.=" AND fee_payable.gibbonSchoolYearID=".$year_id;
if($p_mode!='')
{
	if($p_mode=='cash')
		$sql.=" AND payment_master.payment_mode='cash' ";
	else
		$sql.=" AND payment_master.payment_mode!='cash' ";
}
$sql.=" GROUP BY fee_payable.payment_date,fee_payable.fee_type_short_name,fee_payable.gibbonSchoolYearID,payment_master.payment_mode HAVING total_amount>0";
	$result=$connection2->prepare($sql);
	$result->execute();
	$dboutput=$result->fetchAll();

foreach($dboutput as $value){
	$fee_data_arr[$value['payment_date']][$value['fee_type_name']][$value['status']][$value['payment_mode']]=$value['total_amount'];
}
/* For Fee Data */

/* For Fine Amount */
$query="SELECT payment_date, SUM(fine_amount) AS 'fine',gibbonschoolyear.status AS status,payment_mode 
	FROM `payment_master`
	LEFT JOIN gibbonschoolyear ON gibbonschoolyear.gibbonSchoolYearID=payment_master.gibbonSchoolYearID
	WHERE  fine_amount>0 AND payment_date>='".$fromdate."' and payment_date<='".$todate."'";
	if($year_id!='')
		$query.=" AND `payment_master`.gibbonSchoolYearID=".$year_id;
	if($p_mode!='')
	{
				if($p_mode=='cash')
					$query.=" AND payment_master.payment_mode='cash' ";
				else
					$query.=" AND payment_master.payment_mode!='cash' ";
	}
	$query.=" GROUP BY  payment_date,payment_master.gibbonSchoolYearID,payment_master.payment_mode";
	$result1=$connection2->prepare($query);
	$result1->execute();
	$fine_result=$result1->fetchAll();
	//echo $query."<br>";
	//print_r($fine_result);
	foreach($fine_result as $a) {
	$fee_data_arr[$a['payment_date']]['Fine'][$a['status']][$a['payment_mode']]=$a['fine'];
	}	
/* For Fine Amount */

/* For transport Amount */
	$query="SELECT payment_master.payment_date AS date,SUM(price) AS transport,gibbonschoolyear.status AS status,payment_master.payment_mode 
	FROM transport_month_entry
	LEFT JOIN payment_master ON payment_master.payment_master_id=transport_month_entry.payment_master_id
	LEFT JOIN gibbonschoolyear ON gibbonschoolyear.gibbonSchoolYearID=payment_master.gibbonSchoolYearID
	WHERE  payment_master.payment_date>='".$fromdate."' and payment_master.payment_date<='".$todate."' AND transport_month_entry.payment_master_id >0";
	if($year_id!='')
		$query.=" AND payment_master.gibbonSchoolYearID=".$year_id;
	if($p_mode!='')
	{
				if($p_mode=='cash')
					$query.=" AND payment_master.payment_mode='cash' ";
				else
					$query.=" AND payment_master.payment_mode!='cash' ";
	}	
	$query.=" GROUP BY  payment_master.payment_date,payment_master.gibbonSchoolYearID,payment_master.payment_mode";
	$result2=$connection2->prepare($query);
	$result2->execute();
	$transport_result=$result2->fetchAll();
	//print_r($transport_result);
	
	foreach($transport_result as $a)
	{
		$fee_data_arr[$a['date']]['Transport'][$a['status']][$a['payment_mode']]=$a['transport'];
	}
/* For transport Amount */
	
if($result->rowcount()>0 || $result2->rowcount()>0) {
	foreach($fee_data_arr as $date=>$data1){
		foreach($data1 as $type=>$data2){
			foreach($data2 as $status=>$data3){
				foreach($data3 as $mode=>$amount){
					$mode_str="";
					$mode_type="";
					$status_str="";
					$income_state="INCOME";
						if($mode=='cash'){
							$mode_str="Cash";
							$mode_type="Cash";
						}
						else{
							$mode_str="Bank:CANARA BANK(5708201000007)";
							$mode_type="Bank";
						}
						if($status=='Upcoming'){
							$status_str=" (Upcoming Year)";
							$income_state="ADVANCE";
						}
						$message.="!Account \nN".$mode_str."\nT".$mode_type."\n^ \n";
						$message.="!Type:".$mode_type." \nD".date("d-m-Y", strtotime($date))." \nT".$amount." \nP".$type.$status_str."\nL".$income_state.":".$type.$status_str."\n^ \n";
				}
			}
		}
	}

date_default_timezone_set("Asia/Kolkata");
 $filename="../../qif/".date("dmhi").".qif";
$myfile = fopen($filename, "w") or die("Unable to open file!");
fwrite($myfile, $message);
fclose($myfile); 
?>
<div style='width:100%;height:100%;background-color:#ebebeb;'>
<br><br><br><br><br>
<center><h2 style="color:#7030a0;">QIF File Generated Sucessfully!!</h2>
<br>
<form action="" method="POST">
<input type="hidden" value="<?php echo $filename; ?>" name="filename">
<input  type="submit" value="Download Qif File" Style="background: #ff731b;color: #ffffff;font-size: 20px;border:1px solid; padding:5px;">
</center>
</form>
</center>
</div>
<?php
}
else {
	echo "No result";
}
}
else {
				$file=$_POST['filename'];	 
				if (file_exists($file)) {
					header('Content-Description: File Transfer');
					header('Content-Type: application/octet-stream');
					header('Content-Disposition: attachment; filename="'.basename($file).'"');
					header('Expires: 0');
					header('Cache-Control: must-revalidate');
					header('Pragma: public');
					header('Content-Length: ' . filesize($file));
					readfile($file);
					exit;
				} 
 }
function DateConverter($date)
{
	$datearr=explode("/", $date);
	$systemdate=$datearr[2].'-'.$datearr[1].'-'.$datearr[0];
	return $systemdate;
}
?>