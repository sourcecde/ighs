<?php 
@session_start() ;
$payemntmode='';
$startdate='';
$enddate='';
$result='';
$sql='';
$data='';
$src_month_start=0;
$src_month_end=0;
$src_end_date=0;
$query_mont_condition_str='';
$query_transport_mont_str='';
$schoolyeararr=array(1=>'jan',2=>'feb',3=>'mar',4=>'apr',5=>'may',6=>'jun',7=>'jul',8=>'aug',9=>'sep',10=>'oct',11=>'nov',12=>'dec');
//getting the months
$month_squence_arr=array();
$sql="SELECT * from gibbonschoolyear where status='Current'";
$result=$connection2->prepare($sql);
$result->execute();
$schoolyearresult=$result->fetch();
$firstdayarr=explode("-", $schoolyearresult['firstDay']);
$firstday=(int)$firstdayarr[1];

$lastdayarr=explode("-", $schoolyearresult['lastDay']);
$lastday=(int)$lastdayarr[1];

for($i=$firstday;$i<=12;$i++)
{
	array_push($month_squence_arr, $i);
}
for($i=1;$i<=$lastday;$i++)
{
	array_push($month_squence_arr, $i);
}
// end of getting month
$sql="SELECT * from gibbonschoolyear ORDER BY status";
$result=$connection2->prepare($sql);
$result->execute();
$yearresult=$result->fetchAll();

$sql="SELECT * from gibbonyeargroup";
$result=$connection2->prepare($sql);
$result->execute();
$schoolyearresult=$result->fetchAll();

/*
$sql="SELECT * from gibbonrollgroup";
$result=$connection2->prepare($sql);
$result->execute();
$sectionarr=$result->fetchAll();
*/
$montharr=array();
$year='';
$class='';
$section='';
$left_student='';
//getting the class
if($_POST)
{
	$montharr=$_REQUEST['selected_month_report'];
	$year=$_REQUEST['year_id'];	
	$class=$_REQUEST['class_name'];	
	$section=$_REQUEST['section_name'];	
	if(isset($_REQUEST['left_student']))
	{
		$left_student=$_REQUEST['left_student'];
	}
	
if($_POST['src_to_date']!='')
{
	$enddate=$_POST['src_to_date'];
	$datearr=explode("/", $enddate);
	$src_month_end=$datearr[1];
	$src_end_date=$datearr[2]."-".$datearr[1]."-".$datearr[0];
}

$count=count($montharr);
$i=1;
foreach ($montharr as $value) {
	if($i==$count)
	{
		$query_mont_condition_str.='month_no= '.$value;
	}
	else
	{
		$query_mont_condition_str.='month_no= '.$value." or ";
	}
	$i++;
}
$i=1;
foreach ($montharr as $value) {
	if($i==$count)
	{
		$query_transport_mont_str.='month_name="'.$schoolyeararr[$value].'"';
	}
	else
	{
		$query_transport_mont_str.='month_name= "'.$schoolyeararr[$value].'" or ';
	}
	$i++;
}
//echo $query_mont_condition_str;
/*
for($i=$montharr;$i<=$montharr;$i++)
{
	
	if($i==$src_month_end)
	{
		$query_mont_condition_str.='month_no= '.$i;
	}
	else
	{
		$query_mont_condition_str.='month_no= '.$i." or ";
	}
}
*/
		//echo 	$query_mont_condition_str;									
try {
					$data=array(); 
					$sql="SELECT  fee_payable.* ,SUM(net_amount) AS total_net_amount ,`payment_date` ,`voucher_number` ,`payment_staus`,gibbonperson.gibbonPersonID,gibbonperson.officialName as officialname,gibbonperson.account_number,
					gibbonyeargroup.name AS class ,gibbonrollgroup.name AS section,gibbonstudentenrolment.rollOrder AS roll,GROUP_CONCAT(month_no) AS months
        FROM  `fee_payable`
        LEFT JOIN gibbonperson ON fee_payable.gibbonPersonID=gibbonperson.gibbonPersonID 
        LEFT JOIN gibbonstudentenrolment ON fee_payable.gibbonStudentEnrolmentID=gibbonstudentenrolment.gibbonStudentEnrolmentID
         LEFT JOIN gibbonyeargroup ON gibbonstudentenrolment.gibbonYearGroupID=gibbonyeargroup.gibbonYearGroupID 
         LEFT JOIN gibbonrollgroup ON gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID 
        WHERE ( `payment_staus` =  'Unpaid'  OR ( `payment_staus` =  'Paid'  AND  `payment_date` >  '".$src_end_date."'))
        AND (".$query_mont_condition_str.") AND gibbonyeargroup.gibbonYearGroupID=".$class." AND gibbonrollgroup.gibbonRollGroupID=".$section." AND  fee_payable.gibbonSchoolYearID=".$year;  
        
				if($left_student=='')
				{
					$sql.=" AND gibbonperson.dateEnd IS NULL";
				}
				
				$sql.=" GROUP BY fee_payable.gibbonPersonID WITH ROLLUP";
				echo $sql."<br>";
				$result=$connection2->prepare($sql);
				$result->execute($data);
				
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
			
}
?>
<form name="defaulter_form" id="defaulter_form" method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/defaulter_report_old.php" ?>">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td><b>Select Month:</b></td>
<td colspan="5">
<?php foreach ($month_squence_arr as $value) { ?> 
<b><?php echo ucwords($schoolyeararr[$value]);?>&nbsp;&nbsp;&nbsp;
<input type="checkbox" name="selected_month_report[]"  value="<?php echo $value;?>"  class="selecte_month_class" <?php if(in_array($value, $montharr)){?> checked="checked"<?php } ?>> |
<?php } ?></b>
</td>
</tr>
  <tr>
    <td > <b>Class&nbsp;&nbsp;</b> 
    <select name="class_name" id="class_name">
    <?php foreach ($schoolyearresult as $value) { ?>
    	<option value="<?php echo $value['gibbonYearGroupID']?>" <?php if($class==$value['gibbonYearGroupID']){?> selected="selected"<?php } ?>><?php echo $value['name']?></option>
    	<?php } ?>
    </select>
    </td>
    <td ><b>Section</b>
    <select name="section_name" id="section_name">
     <!--- This part will be loaded by script written end of the page -->
    </select>
    </td>
	<td ><b>Select Year</b>
    <select name="year_id" id="year_id" style="" >
    <?php foreach ($yearresult as $value) { ?>
    	<option value="<?php echo $value['gibbonSchoolYearID']?>" <?php if($year==$value['gibbonSchoolYearID']){?> selected="selected"<?php } ?>><?php echo $value['name']." (".$value['status']." year)"?></option>
    	<?php } ?>
    </select>
    </td>
    
    <td ><b>To date</b><input type="text" name="src_to_date" id="src_to_date" style="width: 100px;" value="<?php echo $enddate;?>" placeholder=" Select Date.."></td>
    <td >
    <b>Left</b> <input type="checkbox" name="left_student" id="left_student" value="1" <?php if($left_student=='1'){?> checked="checked" <?php } ?>>
    </td>
	    <td ><input type="submit" name="submit" id="submit" value="Search" >
	    <?php if($_POST){?>
	    <input type="button" id="defaulter_print" name="defaulter_print" value="Print" style="float: right;">
	    <?php } ?>
	    </td>
  </tr>
</table>
</form>
<?php 
if($_POST)
{
if ($result->rowcount()<1) {
				print "<div class='error'>" ;
				print _("There are no records to display.") ;
				print "</div>" ;
			}
			else {
?>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr class="tablehead">
		<td>Acc No</td>
		<td>Name</td>
		<td>Class</td>
		<td>Sec</td>
		<td>Roll No</td>
		<td>Months</td>
		<td>Amount</td>
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
	while ($row=$resultPage->fetch()) {  $i++;
		
	if (array_key_exists($row['gibbonPersonID'],$transport_details)) {
		$row['total_net_amount']+=$transport_details[$row['gibbonPersonID']];
		$total_transport_price+=$transport_details[$row['gibbonPersonID']];
	}
	if($result->rowcount()==$i)
	{
	?>
		<tr>
		<td colspan="5"></td>
		<td><?php if($result->rowcount()==$i){?>Total<?php } else {?>Sub Total<?php } ?></td>
		<td style="text-align:right"><?php echo number_format((float)($row['total_net_amount']+$total_transport_price), 2, '.', '');?></td>
	</tr>
		<?php } else {
		if($row['total_net_amount']>0)
		{
			?>
		<tr>
		<td><?php echo substr($row['account_number'], 5)?></td>
		<td><?php echo $row['officialname']?></td>
		<td><?php echo $row['class']?></td>
		<td><?php echo SectionFormater($row['section'])?></td>
		<td><?php echo $row['roll']?></td>
		<td><?php echo getMontName($row['months'])?></td>
		<td style="text-align:right"><?php echo number_format((float)$row['total_net_amount'], 2, '.', '');?></td>
		
	</tr>
	<?php } } }?>
</table>
<?php } } ?>
<input type="hidden" name="month_condition" id="month_condition" value="<?php echo $query_mont_condition_str;?>">
<input type="hidden" name="month_name_condition" id="month_name_condition" value='<?php echo $query_transport_mont_str;?>'>

<script type="text/javascript">
		$(function() {
			$( "#src_from_date" ).datepicker({ dateFormat: 'dd/mm/yy' });
			$( "#src_to_date" ).datepicker({ dateFormat: 'dd/mm/yy' });
		});
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

?>
<input type="hidden" name="print_page_url" id="print_page_url" value="<?php echo $_SESSION[$guid]["absoluteURL"] . "/modules/Fee/defaulter_report_print.php";?>">

<!--  Script for  loading section select list  -->
<script>

$(document).ready(function(){
		var year_id=$("#year_id").val();

			$.ajax
	 		({
	 			type: "GET",
	 			url: "modules/Fee/custom_ajax.php",
	 			data: { year_id_select_section: year_id},
	 			success: function(msg)
	 			{ 
				$("#section_name").empty().append(msg);	 			
	 				//console.log(msg);
	 			}
	 			});
}) 
</script>

<!--  Script for  loading section select list  -->