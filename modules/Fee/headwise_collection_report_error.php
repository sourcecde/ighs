<?php
@session_start() ;
$from_date='';
$todate='';
$gibbonPersonID=NULL ;
if (isset($_SESSION[$guid]["gibbonPersonID"])) {
	$gibbonPersonID=$_SESSION[$guid]["gibbonPersonID"] ;
}

$sql="SELECT * from gibbonschoolyear ORDER BY firstDay DESC";
$result=$connection2->prepare($sql);
$result->execute();
$yearresult=$result->fetchAll();
$year='';
$total=0;
$alltotal=0;
if($_POST)
{
	$from_date=$_REQUEST['from_date'];
	$src_fromdate=DateConverter($from_date);
	$todate=$_REQUEST['to_date'];
	$src_todate=DateConverter($todate);
	if($_REQUEST['year_id']!='')
		$year=$_REQUEST['year_id'];	
}
$alldate=array();
$filteredarray=array();
$message="";
?>
<form name="f1" id="f1" method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/headwise_collection_report.php" ?>">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
	<td>
	<select name="year_id" id="year_id" style="margin:5px 0px;">
	<option value=''>Select Year</option>
    <?php foreach ($yearresult as $value) { ?>
    	<option value="<?php echo $value['gibbonSchoolYearID']?>" <?php if($year==$value['gibbonSchoolYearID']){?> selected="selected"<?php } ?>><?php echo $value['name']?></option>
    	<?php } ?>
    </select>
    </td>
	<td>From Date
	<input type="text" name="from_date" id="from_date" value="<?php echo $from_date;?>" required style="text-align:center"></td>
	<td>To Date
	<input type="text" name="to_date" id="to_date" value="<?php echo $todate;?>" required style="text-align:center"></td>
	<td><input type="submit" name="submit" id="submit" value="Go">
	<?php if($_POST){?><input type="button" name="print_headwise" id="print_headwise" value="Print"> <?php } ?>
	<?php if($_POST){?>
	<span style="border:1px solid; padding:5px;">
	<a href="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"]?>/generateQIF.php?fromdate=<?php echo $from_date;?>&todate=<?php echo $todate;?>" onclick="window.open(this.href, '_blank', 'left=20,top=20,width=500,height=300,toolbar=1,resizable=0'); return false;">Generate QIF File</a>
	</span>
	<?php } ?>
	</td>
</tr>
</table>
<?php 
if($_POST)
{
	$sql="SELECT fee_payable.payment_date,SUM(net_amount) AS 'total_amount',fee_type_master.`fee_type_name`
		FROM fee_payable 
		LEFT JOIN fee_type_master ON fee_type_master.`fee_type_master_id`=fee_payable.`fee_type_master_id`
		where payment_date>='".$src_fromdate."' and payment_date<='".$src_todate."'" ;
		
	if($year!='')
		$sql.=" AND gibbonSchoolYearID=".$year;
	$sql.=" GROUP BY fee_payable.payment_date,fee_payable.fee_type_short_name HAVING total_amount>0";
	$result=$connection2->prepare($sql);
	$result->execute();
	$dboutbut=$result->fetchAll();
	
	$query="SELECT payment_date, SUM(fine_amount) AS 'fine' 
	FROM `payment_master` 
	WHERE  payment_date>='".$src_fromdate."' and payment_date<='".$src_todate."'";
	if($year!='')
		$query.=" AND gibbonSchoolYearID=".$year;
	$query.=" GROUP BY  payment_date";
	echo $query;
	$result=$connection2->prepare($query);
	$result->execute();
	$fine_result=$result->fetchAll();
	
	$total_fine=0;
	$fine_array=array();
	foreach($fine_result as $a) {
	//echo $a['payment_date']." - ".$a['fine']."<br>";
	array_push($fine_array, $a['fine']);
	$total_fine+=$a['fine'];
	}
		
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
	
?>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<?php
$j=0;
 foreach ($filteredarray as $value) { 
//$total+=$value['total_amount'];
$i=0;
foreach ($value as $dvalue) {
	
	$total+=$dvalue['total_amount'];
	$alltotal+=$dvalue['total_amount'];
	?>
  <tr>
  	<td><?php if($i==0){ echo DateConverterIndianFormat($dvalue['payment_date']);}?></td>
    <td><?php echo $dvalue['fee_type_name'];?></td>
    <td style="text-align:right"><?php echo $dvalue['total_amount'];?></td>
  </tr>
  <?php 
$i++;
} ?>
	<tr>
  	<td></td>
    <td>Fine</td>
    <td style="text-align:right"><?php echo number_format($fine_array[$j],2, '.', '');?></td>
	</tr>	 
	<tr>
  	<td></td>
    <td><b>Sub Total</td>
    <td style="text-align:right"><b><?php echo number_format($total+$fine_array[$j],2, '.', '');?></td>
  </tr>
  <?php 
  $total=0;
  $j++;
  }

  ?>
  <tr>
  <td></td>
    <td><b>Total:</td>
    <td style="text-align:right"><b><?php echo number_format($alltotal+$total_fine, 2, '.', '');?></td>
  </tr>
</table>
<input type="hidden" value="<?php echo $message;?>">
<!--  this is for total table -->
<?php 
$sql="SELECT SUM(net_amount) AS 'total_amount',fee_type_master.`fee_type_name` FROM fee_payable 
LEFT JOIN fee_type_master ON fee_type_master.`fee_type_master_id`=fee_payable.`fee_type_master_id` where payment_date>='".$src_fromdate."' and payment_date<='".$src_todate."' 
GROUP BY fee_payable.fee_type_short_name HAVING total_amount>0";
	
	$result=$connection2->prepare($sql);
	$result->execute();
	$dboutbut=$result->fetchAll();
?>
<div style="position: relative;left: 184px;">
<table width="50%" cellpadding="0" cellspacing="0" border="0" align="center">
<?php foreach ($dboutbut as $value) { 
$total+=$value['total_amount'];
	?>
  <tr>
    <td><?php echo $value['fee_type_name'];?></td>
    <td style="text-align:right;"><?php echo $value['total_amount'];?></td>
  </tr>
  <?php } ?>
  <tr>
    <td>Fine</td>
    <td style="text-align:right"><?php echo $total_fine;?></td>
  </tr>
  <tr>
    <td><b>Total:</td>
    <td style="text-align:right"><b><?php echo number_format($total+$total_fine, 2, '.', '');?></td>
  </tr>
</table>
</div>
<!--  end total table -->
<input type="hidden" name="print_page_url" id="print_page_url" value="<?php echo $_SESSION[$guid]["absoluteURL"] . "/modules/Fee/headwise_collection_report_print.php";?>">
<input type="hidden" name="print_from_date" id="print_from_date" value="<?php echo $src_fromdate;?>">
<input type="hidden" name="print_to_date" id="print_to_date" value="<?php echo $src_todate;?>">
<?php } ?>
<?php 
function moneyFormatIndia($num){
    $explrestunits = "" ;
    if(strlen($num)>3){
        $lastthree = substr($num, strlen($num)-3, strlen($num));
        $restunits = substr($num, 0, strlen($num)-3); // extracts the last three digits
        $restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; // explodes the remaining digits in 2's formats, adds a zero in the beginning to maintain the 2's grouping.
        $expunit = str_split($restunits, 2);
        for($i=0; $i<sizeof($expunit); $i++){
            // creates each of the 2's group and adds a comma to the end
            if($i==0)
            {
                $explrestunits .= (int)$expunit[$i].","; // if is first value , convert into integer
            }else{
                $explrestunits .= $expunit[$i].",";
            }
        }
        $thecash = $explrestunits.$lastthree;
    } else {
        $thecash = $num;
    }
    return $thecash; // writes the final format where $currency is the currency symbol.
}

function DateConverter($date)
{
	$datearr=explode("/", $date);
	$systemdate=$datearr[2].'-'.$datearr[1].'-'.$datearr[0];
	return $systemdate;
}

function DateConverterIndianFormat($date)
{
	$datearr=explode("-", $date);
	$systemdate=$datearr[2].'/'.$datearr[1].'/'.$datearr[0];
	return $systemdate;
}

?>
<script type="text/javascript">
	$(function() {
		$( "#from_date" ).datepicker({ dateFormat: 'dd/mm/yy' });
		$( "#to_date" ).datepicker({ dateFormat: 'dd/mm/yy' });
	});
</script>