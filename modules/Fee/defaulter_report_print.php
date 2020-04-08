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
$src_month_start=0;
$src_month_end=0;
$src_end_date=0;
$query_mont_condition_str='';



if($_REQUEST['src_to_date']!='')
{
	$enddate=$_REQUEST['src_to_date'];
	$datearr=explode("/", $enddate);
	$src_month_end=$datearr[1];
	$src_end_date=$datearr[2]."-".$datearr[1]."-".$datearr[0];
	$left_student=$_REQUEST['left'];
}
$query_mont_condition_str=$_REQUEST['monthcondition'];
$query_transport_mont_str=$_REQUEST['monthnamecondition'];
$year=$_REQUEST['year'];
$class=$_REQUEST['class'];
$sectionname=$_REQUEST['sectionname'];
$headingmonth=createMonthHeading($query_mont_condition_str);

try {
	
					$data=array(); 
					$sql="SELECT  fee_payable.* ,SUM(net_amount) AS total_net_amount ,`payment_date` ,`voucher_number` ,`payment_staus`,gibbonperson.officialName as officialname,gibbonperson.account_number,
					gibbonyeargroup.name AS class ,gibbonrollgroup.name AS section,gibbonstudentenrolment.rollOrder AS roll,GROUP_CONCAT(month_no) AS months
        FROM  `fee_payable`
        LEFT JOIN gibbonperson ON fee_payable.gibbonPersonID=gibbonperson.gibbonPersonID 
        LEFT JOIN gibbonstudentenrolment ON fee_payable.gibbonStudentEnrolmentID=gibbonstudentenrolment.gibbonStudentEnrolmentID
         LEFT JOIN gibbonyeargroup ON gibbonstudentenrolment.gibbonYearGroupID=gibbonyeargroup.gibbonYearGroupID 
         LEFT JOIN gibbonrollgroup ON gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID 
        WHERE ( `payment_staus` =  'Unpaid'  OR ( `payment_staus` =  'Paid'  AND  `payment_date` >  '".$src_end_date."'))
        AND (".$query_mont_condition_str.") AND gibbonyeargroup.gibbonYearGroupID=".$class." AND gibbonrollgroup.gibbonRollGroupID=".$sectionname." AND  fee_payable.gibbonSchoolYearID=".$year;  
				
					if($left_student==0)
					{
						$sql.=" AND gibbonperson.dateEnd IS NULL";
					}
					$sql.=" GROUP BY fee_payable.gibbonPersonID WITH ROLLUP";
					
				$result=$connection2->prepare($sql);
				$result->execute($data);
				//For transport data 
				$query="SELECT transport_month_entry.gibbonPersonID,SUM(price) AS transport_price
				FROM transport_month_entry 
				LEFT JOIN payment_master ON transport_month_entry.payment_master_id=payment_master.payment_master_id
				WHERE ".$query_transport_mont_str." AND ( transport_month_entry.payment_master_id =0    OR  payment_master.`payment_date` >  '".$src_end_date."') Group BY transport_month_entry.gibbonPersonID";

				$result1=$connection2->prepare($query);
				$result1->execute();
				$transport_price=$result1->fetchAll();
				}
				catch(PDOException $e) { 
				print "<div class='error'>" . $e->getMessage() . "</div>" ; 
			}
			$transport_details=array();
			foreach($transport_price as $a){
				$transport_details[$a['gibbonPersonID']]=$a['transport_price'];
			}
			?>
            <h1 style="text-align:center; font-family:Arial, Helvetica, sans-serif; font-size:25px; font-weight:bold; color:#000000; margin-bottom:15px; margin-top:15px;">Defaulter Report for the month of <span style="font-style:italic;"><?php echo $headingmonth;?></span>  as on <?php echo $_REQUEST['src_to_date'];?></h1>
			<table width="100%" cellpadding="5" cellspacing="0" border="1" bordercolor="#dddddd">
	<tr style="background-color:#CCC; font-family:Arial, Helvetica, sans-serif; font-size:18px; color:#000000;">
		<td>Acc No</td>
		<td>Name</td>
		<td align="center">Class</td>
		<td align="center">Sec</td>
		<td align="center">Roll No</td>
		<td align="center">Months</td>
		<td align="right">Amount</td>
	</tr>
		<?php 
try {
						$resultPage=$connection2->prepare($sql);
						$resultPage->execute($data);
					}
					catch(PDOException $e) { 
						print "<div class='error'>" . $e->getMessage() . "</div>" ; 
					}
					$i=0;
					$total_transport_price=0;
	while ($row=$resultPage->fetch()) { $i++;
		if (array_key_exists($row['gibbonPersonID'],$transport_details)) {
		$row['total_net_amount']+=$transport_details[$row['gibbonPersonID']];
		$total_transport_price+=$transport_details[$row['gibbonPersonID']];
	}
		if($result->rowcount()==$i)
	{?>
		<tr  style="font-family:Arial, Helvetica, sans-serif; font-size:18px; color:#000000;">
		<td colspan="5"></td>
		<td><?php if($result->rowcount()==$i){?>Total<?php } else {?>Sub Total<?php } ?></td>
		<td align="right"><?php echo number_format((float)($row['total_net_amount']+$total_transport_price), 2, '.', '');?></td>
	</tr>
		<?php } else {
		if($row['total_net_amount']>0)
		{
			?>
		<tr style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000;">
		<td><?php echo substr($row['account_number'], 5)?></td>
		<td><?php echo $row['officialname']?></td>
		<td align="center"><?php echo $row['class']?></td>
		<td align="center"><?php echo SectionFormater($row['section']);?></td>
		<td align="center"><?php echo $row['roll']?></td>
		<td align="center"><?php echo getMontName($row['months'])?></td>
		<td align="right"><?php echo number_format((float)$row['total_net_amount'], 2, '.', '');?></td>
	</tr>
	<?php } } }?>
</table>
<div id="collection_register_print_button_area" style="position: relative;top:30px;">
<table width="100%" cellpadding="0" cellspacing="0" border="0">

  <tr>
    <td align="center" style="padding-bottom:15px;"><input type="button" name="print_collecttion" id="print_collecttion" onclick="return printFunction()" value="Print" style="background-color: #ff731b; border:none;  color: #ffffff;   cursor: pointer;  font-size: 14px;    font-weight: 600;    height: 28px;    margin: 2px;    min-width: 55px;  padding-left: 10px;
    padding-right: 10px;">
	<input type="button" name="cancel_collecttion" id="cancel_collecttion" onclick="return cancelFunction()" value="Close" style="background-color: #ff731b; border:none;  color: #ffffff;   cursor: pointer;  font-size: 14px;    font-weight: 600;    height: 28px;    margin: 2px;    min-width: 55px;  padding-left: 10px;
    padding-right: 10px;"></td>
    
  </tr>
</table>

</div>
<script type="text/javascript">
function printFunction()
{
	document.getElementById("collection_register_print_button_area").style.display='none';
	window.print();
	}

function cancelFunction()
{
	window.close();
	}
</script>
<?php 
function getMontName($monthstr)
{
	$newarr=array();
	$dupmontharr=explode(",", $monthstr);
	$montharr=array_unique($dupmontharr);
	foreach ($montharr as $value) {
		
		array_push($newarr, $value);
	}
	sort($newarr);
	$schoolyeararr=array(1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December');
	if(count($newarr)>1)
	{
		$firstelement=$newarr[0];
		$lastelement=$newarr[count($newarr)-1];
		$returnstring=$schoolyeararr[$firstelement]." - ".$schoolyeararr[$lastelement];
	}
	else 
	{
		$returnstring=$schoolyeararr[$newarr[0]];
	}
	echo $returnstring;
}
function SectionFormater($section)
{
	return substr($section, -1);
}

function createMonthHeading($monthstr)
{
	$str='';
	$montharr=array();
	$arr=explode("or", $monthstr);
	foreach ($arr as $value) {
		$mon=substr($value, 10);
		array_push($montharr, (int)$mon);
	}
	
		$schoolyeararr=array(1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec');
	foreach ($montharr as $value) {
		if($str=='')
		{
			$str=$schoolyeararr[$value];
		}
		else 
		{
			$str.=", ".$schoolyeararr[$value];
		}
		
	}
	return $str;
}
?>