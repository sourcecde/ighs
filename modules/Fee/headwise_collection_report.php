<?php
@session_start() ;

if (isActionAccessible($guid, $connection2, "/modules/Fee/headwise_collection_report")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {

$from_date='';
$todate='';
$p_mode='';
$p_monthduration='';
$gibbonPersonID=NULL ;
if (isset($_SESSION[$guid]["gibbonPersonID"])) {
	$gibbonPersonID=$_SESSION[$guid]["gibbonPersonID"] ;
}
$sql="SELECT * from `gibbonschoolyear` ORDER BY firstDay DESC";
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
	$p_mode=$_REQUEST['p_mode'];
	$p_monthduration=$_REQUEST['p_monthduration'];
	
}
$alldate=array();
$filteredarray=array();
$message="";
?>
<form name="f1" id="f1" method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/headwise_collection_report.php" ?>">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr style="height:100px;">
	<td>
	<select name="year_id" id="year_id" style="margin:5px 0px;">
	<option value=''>Select Year</option>
    <?php foreach ($yearresult as $value) { ?>
    	<option value="<?php echo $value['gibbonSchoolYearID']?>" <?php if($year==$value['gibbonSchoolYearID']){?> selected="selected"<?php } ?>><?php echo $value['name']." (".$value['status']." year)"?></option>
    	<?php } ?>
    </select>
    </td>
	<td><b>From Date :</b>
	<input type="text" name="from_date" id="from_date" value="<?php echo $from_date;?>" required style="text-align:center; width:100px;" placeholder=" From Date"></td>
	<td><b>To Date :</b>
	<input type="text" name="to_date" id="to_date" value="<?php echo $todate;?>" required style="text-align:center; width:100px;" placeholder=" To Date"></td>
	<td><b>Months Duration:</b>
		<select name="p_monthduration" id="p_monthduration">
			<option value="">All</option>
			<option value="1" <?=$p_monthduration=='1'?'selected':''?>>Jan - Mar</option>
			<option value="2" <?=$p_monthduration=='2'?'selected':''?>>Apr - Dec</option>
		</select>
	</td>
	<td><b>Payment Mode:</b>
		<select name='p_mode' id='p_mode'>
			<option value='' >All</option>
			<option value='cash' <?=$p_mode=='cash'?'selected':'';?>>Cash</option>
			<option value='cheque' <?=$p_mode=='cheque'?'selected':'';?>>Cheque</option>
			<option value='online' <?=$p_mode=='online'?'selected':'';?>>Online</option>
			<option value='card' <?=$p_mode=='card'?'selected':'';?>>Card</option>
		</select></td>
	<td><table cellpadding="0" cellspacing="0" border="0">
		<tr><td><input type="submit" name="submit" id="submit" value="Go" style=""></td>
	<?php if($_POST){?><td><a type="button" name="print_headwise" id="print_headwise"  style="color: #ffffff; background-color: seagreen; padding:5px 10px;pointer:cursor;">Print</a></td> <?php } ?>
	</tr><?php if($_POST){?><tr><td colspan="2">
	<a type="button" href="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"]?>/generateQIF.php?fromdate=<?php echo $from_date;?>&todate=<?php echo $todate;?>&year_id=<?php echo $year; ?>&p_mode=<?=$p_mode;?>" onclick="window.open(this.href, '_blank', 'left=20,top=20,width=700,height=400,toolbar=1,resizable=0'); return false;" style="color: #ffffff; background-color: seagreen; margin:5px; padding:0px 2px;height:40px;pointer:cursor;">Generate QIF File</a></td></tr>
	
	<?php } ?>
		</table>
	</td>
</tr>
</table>
<?php 
if($_POST)
{
	$sql="SELECT `fee_payable`.payment_date,SUM(net_amount) AS 'total_amount',`fee_type_master`.`fee_type_name`
		FROM `fee_payable` 
		LEFT JOIN `fee_type_master` ON `fee_type_master`.`fee_type_master_id`=`fee_payable`.`fee_type_master_id` 
		LEFT JOIN `payment_master` ON `payment_master`.payment_master_id=`fee_payable`.payment_master_id 
		where `fee_payable`.payment_date>='".$src_fromdate."' and `fee_payable`.payment_date<='".$src_todate."'"; 
		if($year!='')
		$sql.=" AND `fee_payable`.gibbonSchoolYearID=".$year ;
		if($p_mode!='')
		{
			$sql.=" AND `payment_master`.payment_mode='".$p_mode."'";
		}
		if($p_monthduration!='')
		{	if($p_monthduration==1)
				$sql.=" AND `fee_payable`.month_no BETWEEN 0 AND 3";
			if($p_monthduration==2)
				$sql.=" AND `fee_payable`.month_no BETWEEN 4 AND 12";
		}
		$sql.=" GROUP BY `fee_payable`.payment_date,`fee_payable`.fee_type_short_name HAVING total_amount!=0";
	
	$result=$connection2->prepare($sql);
	$result->execute();
	$dboutbut=$result->fetchAll();
	
	
	$query=" SELECT payment_date,SUM(fine_amount) as fine FROM (SELECT `payment_master`.payment_date, fine_amount 
	FROM `payment_master`, `fee_payable` 
	WHERE  `fee_payable`.payment_master_id=`payment_master`.payment_master_id AND `payment_master`.payment_date>='".$src_fromdate."' and `payment_master`.payment_date<='".$src_todate."'";
	if($year!='')
		$query.=" AND `payment_master`.gibbonSchoolYearID=".$year;
	if($p_mode!='')
	{
       $query.=" AND `payment_master`.payment_mode='".$p_mode."'";
	}	
	if($p_monthduration!='')
	{	
		if($p_monthduration==1)
			$query.=" AND `fee_payable`.month_no BETWEEN 0 AND 3";
		if($p_monthduration==2)
			$query.=" AND `fee_payable`.month_no BETWEEN 4 AND 12";
	}
	
	$query.=" GROUP BY  `payment_master`.payment_date,  `payment_master`.payment_master_id) subquery GROUP BY  payment_date ";
	  
	$result1=$connection2->prepare($query);
	$result1->execute();
	$fine_result=$result1->fetchAll();
	$total_fine=0;
	$fine_array=array();
	foreach($fine_result as $a) {
		if($a['fine']>0){
			$fine_array[$a['payment_date']]= $a['fine'];
			$total_fine+=$a['fine'];
			}
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
	//Transport Amount
	$query="SELECT `payment_master`.payment_date AS date,SUM(price) AS transport 
	FROM transport_month_entry
	LEFT JOIN `payment_master` ON `payment_master`.payment_master_id=`transport_month_entry`.payment_master_id
	WHERE  `payment_master`.payment_date>='".$src_fromdate."' and `payment_master`.payment_date<='".$src_todate."' AND `transport_month_entry`.payment_master_id >0";
	if($year!='')
		$query.=" AND `payment_master`.gibbonSchoolYearID=".$year;
	if($p_mode!='')
	{
        $query.=" AND `payment_master`.payment_mode='".$p_mode."'";
	}
	if($p_monthduration!='')
	{	
		if($p_monthduration==1)
			$query.=" AND `transport_month_entry`.`month_name` IN ('jan','feb','mar')";
		if($p_monthduration==2)
			$query.=" AND `transport_month_entry`.`month_name` NOT IN ('jan','feb','mar')";
	}
	$query.=" GROUP BY  `payment_master`.payment_date";
	$result2=$connection2->prepare($query);
	$result2->execute();
	$transport_result=$result2->fetchAll();
	$transport_arr=array();
	$total_transport=0;
	foreach($transport_result as $a)
	{
		$transport_arr[$a['date']]=$a['transport'];
		$total_transport+=$a['transport'];
	}
if ($result->rowcount()<1 && $result2->rowcount()<1) {
				print "<div class='error'>" ;
				print _("There are no records to display.") ;
				print "</div>" ;
			}
else {
?>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><th>Date</th><th>Fee Head</th><th>Amount</th></tr>
<?php
$j=0;
for($date=$src_fromdate;strtotime($date) <= strtotime($src_todate);$date = date ("Y-m-d", strtotime("+1 day", strtotime($date)))){
                
if (array_key_exists($date,$filteredarray)) {
	$value=$filteredarray[$date];
// foreach ($filteredarray as $value) { 
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
} 

if (array_key_exists($dvalue['payment_date'],$transport_arr)) {
	$t_p=$transport_arr[$dvalue['payment_date']];
	$total+=$t_p;
?>
	<tr>
  	<td></td>
    <td>Transport</td>
    <td style="text-align:right"><?php echo number_format($t_p,2, '.', '');?></td>
	</tr>
<?php
} 
if(array_key_exists($dvalue['payment_date'],$fine_array)){
	$f_p=$fine_array[$dvalue['payment_date']];
	$total+=$f_p;
	?>
	<tr>
  	<td></td>
    <td>Fine</td>
    <td style="text-align:right"><?php echo number_format($f_p,2, '.', '');?></td>
	</tr>
<?php
 }
?>	
	<tr>
  	<td></td>
    <td><b>Sub Total</td>
    <td style="text-align:right"><b><?php echo number_format($total,2, '.', '');?></td>
  </tr>
  <?php 
  $total=0;
  }
  else {
	  $sub_tot=0;
	  if (array_key_exists($date,$transport_arr)) {
		  	$t_p=$transport_arr[$date];
			$total+=$t_p;
			$sub_tot+=$t_p;
	?>
	<tr>
  	<td><?php echo DateConverterIndianFormat($date);?></td>
    <td>Transport</td>
    <td style="text-align:right"><?php echo number_format($t_p,2, '.', '');?></td>
	</tr>
<?php
	  }
		if(array_key_exists($date,$fine_array)){
			$f_p=$fine_array[$date];
			$total+=$f_p;
			$sub_tot+=$f_p;
			?>
			<tr>
			<td></td>
			<td>Fine</td>
			<td style="text-align:right"><?php echo number_format($f_p,2, '.', '');?></td>
			</tr>
		<?php
		 }
		 if($sub_tot>0){
	?>
			<tr>
			<td></td>
			<td><b>Sub Total</td>
			<td style="text-align:right"><b><?php echo number_format($sub_tot,2, '.', '');?></td>
			</tr>
	<?php
			}
  }
  $total=0;
}
  ?>
  <tr>
  <td></td>
    <td><b>Total:</td>
    <td style="text-align:right"><b><?php echo number_format($alltotal+$total_fine+$total_transport, 2, '.', '');?></td>
  </tr>
</table>
<input type="hidden" value="<?php echo $message;?>">
<!--  this is for total table -->
<?php 
$sql="SELECT SUM(net_amount) AS 'total_amount',`fee_type_master`.`fee_type_name`
 FROM `fee_payable` 
LEFT JOIN `fee_type_master` ON `fee_type_master`.`fee_type_master_id`=`fee_payable`.`fee_type_master_id` 
LEFT JOIN `payment_master` ON `payment_master`.payment_master_id=`fee_payable`.payment_master_id 
where `fee_payable`.payment_date>='".$src_fromdate."' and `fee_payable`.payment_date<='".$src_todate."'"; 
if($year!='')
	$sql.=" AND `fee_payable`.gibbonSchoolYearID=".$year;
if($p_mode!='')
{
	    $sql.=" AND `payment_master`.payment_mode='".$p_mode."'";
}
if($p_monthduration!='')
{	
	if($p_monthduration==1)
		$sql.=" AND `fee_payable`.month_no BETWEEN 0 AND 3";
	if($p_monthduration==2)
		$sql.=" AND `fee_payable`.month_no BETWEEN 4 AND 12";
}
$sql.=" GROUP BY `fee_payable`.fee_type_short_name HAVING total_amount!=0";
	$result=$connection2->prepare($sql);
	$result->execute();
	$dboutbut=$result->fetchAll();
$total1=0;
?>
<br>
<div style="position: relative;left: 184px;">
<table width="50%" cellpadding="0" cellspacing="0" border="0" align="center">
<tr><th>Fee Head</th><th>Amount</th></tr>
<?php foreach ($dboutbut as $value) { 
$total1+=$value['total_amount'];
	?>
  <tr>
    <td><?php echo $value['fee_type_name'];?></td>
    <td style="text-align:right;"><?php echo $value['total_amount'];?></td>
  </tr>
  <?php } 
  if($total_transport>0) {
  ?>
  <tr>
    <td>Transport</td>
    <td style="text-align:right"><?php echo number_format($total_transport, 2, '.', '');?></td>
  </tr>
<?php
 }
 if($total_fine>0) {
?>  
  <tr>
    <td>Fine</td>
    <td style="text-align:right"><?php echo number_format($total_fine, 2, '.', '');?></td>
  </tr>
  <?php
}  ?>
  <tr>
    <td><b>Total:</td>
    <td style="text-align:right"><b><?php echo number_format($total1+$total_fine+$total_transport, 2, '.', '');?></td>
  </tr>
</table>
</div>
<!--  end total table -->
<input type="hidden" name="print_page_url" id="print_page_url" value="<?php echo $_SESSION[$guid]["absoluteURL"] . "/modules/Fee/headwise_collection_report_print.php";?>">
<input type="hidden" name="print_from_date" id="print_from_date" value="<?php echo $src_fromdate;?>">
<input type="hidden" name="print_to_date" id="print_to_date" value="<?php echo $src_todate;?>">
<?php
 } 
}
}
?>
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



