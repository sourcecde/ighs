<?php
 if(!$_POST) {
include "../../config.php" ;
$fromdate=DateConverter($_GET['fromdate']);
$todate=DateConverter($_GET['todate']);
$year_id=$_GET['year_id'];
$p_mode=$_GET['p_mode'];
$alldate=array();
$filteredarray=array();
$total=0;
$alltotal=0;
$message="";
try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}

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
	$dboutbut=$result->fetchAll();
//echo $sql."<br><br>";
	foreach ($dboutbut as $value) {
		if(!in_array($value['payment_date'], $alldate))
		{
			array_push($alldate, $value['payment_date']);
		}
		
	}
	foreach ($alldate as $alldatevalue) {
		$temparray=array();
		foreach ($dboutbut as $dbvalue) {
			if($alldatevalue==$dbvalue['payment_date'])
			{
				array_push($temparray, $dbvalue);
			}
		}
		$filteredarray[$alldatevalue]=$temparray;
	}
//For Fine Amount
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
	$total_fine=0;
	$fine_array=array();
	foreach($fine_result as $a) {
	$fine_array[$a['payment_date']]=array($a['fine'],$a['status'],$a['payment_mode']);
	$total_fine+=$a['fine'];
	}	
//For transport Amount
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
	$transport_arr=array();
	$total_transport=0;
	//echo $query."<br>";
	foreach($transport_result as $a)
	{
		$transport_arr[$a['date']]=array($a['transport'],$a['status'],$a['payment_mode']);
	}
if($result->rowcount()>0 || $result2->rowcount()>0) {
for($date=$fromdate;strtotime($date) <= strtotime($todate);$date = date ("Y-m-d", strtotime("+1 day", strtotime($date)))){
                
if (array_key_exists($date,$filteredarray)) {
	$value=$filteredarray[$date];
$j=0;
 
	foreach ($value as $dvalue) {	
		$total+=$dvalue['total_amount'];
		$alltotal+=$dvalue['total_amount'];
		$mode_str="";
		$mode_type="";
		if($dvalue['payment_mode']=='cash'){
			$mode_str="Cash";
			$mode_type="Cash";
		}
		else{
			$mode_str="Bank:CANARA BANK(5708201000007)";
			$mode_type="Bank";
		}
	$status_str='';
	$income_state="INCOME";
	if($dvalue['status']!='Current'){
		$status_str=" (".$dvalue['status']." Year) ";
		$income_state="ADVANCE";
	}
	$message.="!Account \nN".$mode_str."\nT".$mode_type."\n^ \n";
	$message.="!Type:".$mode_type." \nD".date("d-m-Y", strtotime($dvalue['payment_date']))." \nT".$dvalue['total_amount']." \nP".$dvalue['fee_type_name'].$status_str."\nL".$income_state.":".$dvalue['fee_type_name'].$status_str."\n^ \n";
	}
	//if($fine_array[$j][0]>0)
	if (array_key_exists($value[0]['payment_date'],$fine_array)) {
		$mode_str="";
		$mode_type="";
		if($fine_array[$value[0]['payment_date']][2]=='cash'){
			$mode_str="Cash";
			$mode_type="Cash";
		}
		else{
			$mode_str="Bank:CANARA BANK(5708201000007)";
			$mode_type="Bank";
		}
		$status_str='';
		$income_state="INCOME";
		if($fine_array[$value[0]['payment_date']][1]!='Current'){
			$status_str=" (".$fine_array[$value[0]['payment_date']][1]." Year) ";
			$income_state="ADVANCE";
		}
	if($fine_array[$value[0]['payment_date']][0]>0){
		$message.="!Account \nN".$mode_str."\nT".$mode_type."\n^ \n";
		$message.="!Type:".$mode_type." \nD".date("d-m-Y", strtotime($value[0]['payment_date']))." \nT".$fine_array[$value[0]['payment_date']][0]." \nPFine".$status_str."\nL".$income_state.":Fine".$status_str."\n^ \n";
	}
	}
	if (array_key_exists($value[0]['payment_date'],$transport_arr)) {
		$mode_str="";
		$mode_type="";
		if($transport_arr[$value[0]['payment_date']][2]=='cash'){
			$mode_str="Cash";
			$mode_type="Cash";
		}
		else{
			$mode_str="Bank:CANARA BANK(5708201000007)";
			$mode_type="Bank";
		}
		$status_str='';
		$income_state="INCOME";
		
		if($transport_arr[$value[0]['payment_date']][1]!='Current'){
			$status_str=" (".$transport_arr[$value[0]['payment_date']][1]." Year) ";
			$income_state="ADVANCE";
		}
		$message.="!Account \nN".$mode_str."\nT".$mode_type."\n^ \n";
		$message.="!Type:".$mode_type." \nD".date("d-m-Y", strtotime($value[0]['payment_date']))." \nT".$transport_arr[$value[0]['payment_date']][0]." \nPTransport Fee".$status_str."\nL".$income_state.":Transport Fee".$status_str."\n^ \n";
	}
	$j++;
 }
   else {
	  if (array_key_exists($date,$transport_arr)) {
		  	$t_p=$transport_arr[$date];
			$mode_str="";
			$mode_type="";
		if($transport_arr[$date][2]=='cash'){
			$mode_str="Cash";
			$mode_type="Cash";
		}
		else{
			$mode_str="Bank:CANARA BANK(5708201000007)";
			$mode_type="Bank";
		}
			$status_str='';
			$income_state="INCOME";
		if($transport_arr[$date][1]!='Current'){
			$status_str=" (".$transport_arr[$date][1]." Year) ";
			$income_state="ADVANCE";
		}
		$message.="!Account \nN".$mode_str."\nT".$mode_type."\n^ \n";
		$message.="!Type:".$mode_type." \nD".date("d-m-Y", strtotime($date))." \nT".$transport_arr[$date][0]." \nPTransport Fee".$status_str."\nL".$income_state.":Transport Fee".$status_str."\n^ \n";
	  }
  }
}

 $filename="../../qif/".date("dmhm").".qif";
$myfile = fopen($filename, "w") or die("Unable to open file!");
fwrite($myfile, $message);
fclose($myfile); 
echo "<br><br><br><center> Qif File Sucessfully Generated.<br><br><br>";
?>
<form action="" method="POST">
<input type="hidden" value="<?php echo $filename; ?>" name="filename">
<input  type="submit" value="Download Qif File">
</center>
</form>
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