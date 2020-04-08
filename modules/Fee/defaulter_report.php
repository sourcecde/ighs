<?php 
@session_start() ;

if (isActionAccessible($guid, $connection2, "/modules/Fee/defaulter_report.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {

$exportStr="";
$schoolyeararr=array(0=>'yearly',1=>'jan',2=>'feb',3=>'mar',4=>'apr',5=>'may',6=>'jun',7=>'jul',8=>'aug',9=>'sep',10=>'oct',11=>'nov',12=>'dec');
$fullNameArray=array('yearly'=>'Yearly','jan'=>'January','feb'=>'February','mar'=>'March  ','apr'=>'April  ','may'=>'May    ','jun'=>'June   ','jul'=>'July   ','aug'=>'August','sep'=>'September','oct'=>'October','nov'=>'November','dec'=>'December');
//For SchoolYear(Session) Info
$sql="SELECT * from gibbonschoolyear ORDER BY `status` ASC";
$result=$connection2->prepare($sql);
$result->execute();
$yearresult=$result->fetchAll();
// For Class Info
$sql="SELECT * from gibbonyeargroup";
$result=$connection2->prepare($sql);
$result->execute();
$schoolyearresult=$result->fetchAll();


$year='';
if(isset($_POST['year_id'])){
	$year=$_REQUEST['year_id'];
}
else{
	$sqly="select `gibbonSchoolYearID` from gibbonschoolyear where status='Current'";
	$result=$connection2->prepare($sqly);
	$result->execute();
	$year=$result->fetchColumn();
}

//For Section Info
$sql="select r.`gibbonRollGroupID`, CONCAT(y.name, ' - ', r.name) as name 
		FROM gibbonrollgroup r JOIN gibbonyeargroup y on r.gibbonYearGroupID=y.gibbonYearGroupID 
		WHERE r.gibbonSchoolYearID=$year 
		ORDER BY y.sequenceNumber, r.name";
$result=$connection2->prepare($sql);
$result->execute();
$sectionarr=$result->fetchAll();

// getting the months 
$month_squence_arr=array();
$firstdayarr=explode("-", $yearresult[0]['firstDay']);
$firstday=(int)$firstdayarr[1];

//$lastdayarr=explode("-", $yearresult[0]['lastDay']);
//$lastday=(int)$lastdayarr[1];

for($i=$firstday;$i<=12;$i++)
{
	array_push($month_squence_arr, $i);
}

//for($i=1;$i<=$lastday;$i++)
//{
//	array_push($month_squence_arr, $i);
//}
// end of getting month 
$montharr=array();
$class='';
$sections='';
$left_student='';
$enddate='';

if($_POST)
{
	$sections=join(',', $_POST['section_name']);
	$montharr=$_REQUEST['selected_month_report'];
	$year=$_REQUEST['year_id'];	
	$class=$_REQUEST['class_name'];	
	if(isset($_REQUEST['left_student']))
	{
		$left_student=$_REQUEST['left_student'];
	}
	
if($_POST['src_to_date']!='')
{
	$enddate=$_POST['src_to_date'];
	$datearr=explode("/", $enddate);
	$src_end_date=$datearr[2]."-".$datearr[1]."-".$datearr[0];
}

$count=count($montharr);
$query_mont_condition_str="";
$query_transport_mont_str="";
$firstMonth="";
$lastMonth="";
$i=0;
foreach ($montharr as $value) {
	if($i==0)
		$firstMonth=$fullNameArray[$schoolyeararr[$value]];
	if($i++!=0){
		$query_mont_condition_str.=" OR ";
		$query_transport_mont_str.=" OR ";
		$lastMonth=$fullNameArray[$schoolyeararr[$value]];
	}
	$query_mont_condition_str.='month_no= '.$value;
	$query_transport_mont_str.="month_name='".$schoolyeararr[$value]."'";
}	
$month_name_str=$firstMonth;
	if($lastMonth!="" AND $firstMonth!=$lastMonth)
		$month_name_str.=" - ".$lastMonth;
try {
		$sql="SELECT * FROM gibbonsetting WHERE gibbonSystemSettingsID='147'";
		$result=$connection2->prepare($sql);
		$result->execute();
		$header2=$result->fetch();

		$sql="SELECT  gibbonperson.gibbonPersonID,month_name, SUM(net_amount) as net_amount ";
		
        $sql_condition="FROM  `fee_payable`
        LEFT JOIN gibbonperson ON fee_payable.gibbonPersonID=gibbonperson.gibbonPersonID 
        LEFT JOIN gibbonstudentenrolment ON fee_payable.gibbonStudentEnrolmentID=gibbonstudentenrolment.gibbonStudentEnrolmentID
        LEFT JOIN gibbonyeargroup ON gibbonstudentenrolment.gibbonYearGroupID=gibbonyeargroup.gibbonYearGroupID 
        LEFT JOIN gibbonrollgroup ON gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID 
		WHERE ( `payment_staus` =  'Unpaid'  OR ( `payment_staus` =  'Paid'  AND  `payment_date` >  '".$src_end_date."')) 
		AND (".$query_mont_condition_str.") ";

		if($class!='')
			$sql_condition.=" AND gibbonyeargroup.gibbonYearGroupID=".$class;
		if($sections!='')
			$sql_condition.=" AND gibbonrollgroup.gibbonRollGroupID IN ($sections)";
		$sql_condition.=" AND  fee_payable.gibbonSchoolYearID=".$year;  
		//echo $left_student;
		// if($left_student=='')
		  // $sql_condition.=" AND gibbonperson.dateEnd IS NULL ";
		if($left_student=='')
			$sql_condition.=" AND gibbonperson.gibbonPersonID NOT IN (SELECT `student_id` FROM `leftstudenttracker` WHERE `yearOfLeaving`<=".abs($year).")";
		else
			$sql_condition.=" AND gibbonperson.gibbonPersonID NOT IN (SELECT `student_id` FROM `leftstudenttracker` WHERE `yearOfLeaving`<".abs($year).")";
		  $sql_condition.="  AND net_amount!=0 GROUP BY fee_payable.gibbonPersonID,fee_payable.month_name";
		$sql.=$sql_condition;
		$result=$connection2->prepare($sql);
		$result->execute();
		$fee_data=$result->fetchAll();
		//echo $sql."<br>";		
		//print_r($fee_data);
				
		$query="SELECT transport_month_entry.gibbonPersonID, price AS transport_price,month_name
		FROM transport_month_entry 
		LEFT JOIN payment_master ON transport_month_entry.payment_master_id=payment_master.payment_master_id 
		LEFT JOIN `gibbonperson` ON `transport_month_entry`.`gibbonPersonID`=`gibbonperson`.`gibbonPersonID`  
		WHERE (".$query_transport_mont_str.") AND ( transport_month_entry.payment_master_id =0    OR(transport_month_entry.payment_master_id>0 AND  payment_master.`payment_date` >  '".$src_end_date."')) AND `transport_month_entry`.`gibbonSchoolYearID` =$year";
		if($left_student=='')
		   $query.=" AND gibbonperson.dateEnd IS NULL ";
		$result1=$connection2->prepare($query);
		$result1->execute();
		$transport_price=$result1->fetchAll();
		//echo $query."<br>";
		//print_r($transport_price);
		
		//$sql_condition="SELECT SUM(net_amount) AS 'total_amount',`fee_type_master`.`fee_type_name`
		//	FROM `fee_payable` 
		//	LEFT JOIN `fee_type_master` ON `fee_type_master`.`fee_type_master_id`=`fee_payable`.`fee_type_master_id` 
		//	JOIN gibbonperson ON fee_payable.gibbonPersonID=gibbonperson.gibbonPersonID
		//	JOIN gibbonstudentenrolment ON (gibbonperson.gibbonPersonID=gibbonstudentenrolment.gibbonPersonID AND gibbonstudentenrolment.gibbonSchoolYearID=$year)
		//	JOIN gibbonyeargroup ON gibbonstudentenrolment.gibbonYearGroupID=gibbonyeargroup.gibbonYearGroupID 
		//	JOIN gibbonrollgroup ON gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID
		//	LEFT JOIN cheque_master  ON   fee_payable.`payment_master_id`=cheque_master.`payment_master_id`
		//	WHERE ( `payment_staus` =  'Unpaid'  OR ( `payment_staus` =  'Paid'  AND  `payment_date` >  '".$src_end_date."') OR cheque_master.`cheque_status_id`=0) 
		//	AND (".$query_mont_condition_str.") ";

        $sql_condition="SELECT SUM(net_amount) AS 'total_amount',`fee_type_master`.`fee_type_name`
			FROM `fee_payable` 
			LEFT JOIN `fee_type_master` ON `fee_type_master`.`fee_type_master_id`=`fee_payable`.`fee_type_master_id` 
			JOIN gibbonperson ON fee_payable.gibbonPersonID=gibbonperson.gibbonPersonID
			JOIN gibbonstudentenrolment ON (gibbonperson.gibbonPersonID=gibbonstudentenrolment.gibbonPersonID AND gibbonstudentenrolment.gibbonSchoolYearID=$year)
			JOIN gibbonyeargroup ON gibbonstudentenrolment.gibbonYearGroupID=gibbonyeargroup.gibbonYearGroupID 
			JOIN gibbonrollgroup ON gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID
			WHERE ( `payment_staus` =  'Unpaid'  OR ( `payment_staus` =  'Paid'  AND  `payment_date` >  '".$src_end_date."')) 
			AND (".$query_mont_condition_str.") ";
					
			
		if($class!='')
			$sql_condition.=" AND gibbonyeargroup.gibbonYearGroupID=".$class;
		if($sections!='')
			$sql_condition.=" AND gibbonrollgroup.gibbonRollGroupID IN ($sections)";
		$sql_condition.=" AND  fee_payable.gibbonSchoolYearID=".$year;  
		//echo $left_student;
		if($left_student=='')
			$sql_condition.=" AND gibbonperson.gibbonPersonID NOT IN (SELECT `student_id` FROM `leftstudenttracker` WHERE `yearOfLeaving`<=".abs($year).")";
		else
			$sql_condition.=" AND gibbonperson.gibbonPersonID NOT IN (SELECT `student_id` FROM `leftstudenttracker` WHERE `yearOfLeaving`<".abs($year).")";
		if($filterBoarder!="")
				$sql_condition.=" AND gibbonperson.boarder='$filterBoarder' ";
		$sql_condition.=" AND net_amount>0 GROUP BY `fee_payable`.fee_type_short_name HAVING total_amount>0";
		$result=$connection2->prepare($sql_condition);
		$result->execute();
		$fee_data_summary=$result->fetchAll();
		$total_transport_summarry=0;
	}
	catch(PDOException $e) { 
		print "<div class='error'>" . $e->getMessage() . "</div>" ; 
	}
			$data_array=array();
			 foreach($fee_data as $f){
				$data_array[$f['gibbonPersonID']+0][$f['month_name']]=$f['net_amount'];	
			} 
			foreach($transport_price as $f){
				if(array_key_exists($f['gibbonPersonID']+0,$data_array)){
					if(array_key_exists($f['month_name'],$data_array[$f['gibbonPersonID']+0]))
						$data_array[$f['gibbonPersonID']+0][$f['month_name']]+=$f['transport_price'];
					else
						$data_array[$f['gibbonPersonID']+0][$f['month_name']]=$f['transport_price'];
				}
				else
				$data_array[$f['gibbonPersonID']+0][$f['month_name']]=$f['transport_price'];
				
				$total_transport_summarry+=$f['transport_price'];
			} 
			  //print_r($data_array);
			$sql1="SELECT  gibbonperson.gibbonPersonID,gibbonperson.officialName as officialname,gibbonperson.account_number,
					gibbonyeargroup.name AS class ,gibbonrollgroup.name AS section,gibbonstudentenrolment.rollOrder AS roll
					FROM  gibbonperson 
					LEFT JOIN gibbonstudentenrolment ON gibbonperson.gibbonPersonID=gibbonstudentenrolment.gibbonPersonID
					LEFT JOIN gibbonyeargroup ON gibbonstudentenrolment.gibbonYearGroupID=gibbonyeargroup.gibbonYearGroupID 
					LEFT JOIN gibbonrollgroup ON gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID 
					WHERE `gibbonrollgroup`.gibbonSchoolYearID=".$year;
					if($class!='')
						$sql1.=" AND gibbonyeargroup.gibbonYearGroupID=".$class;
					if($sections!='')
						$sql1.=" AND gibbonrollgroup.gibbonRollGroupID IN ($sections)";
					if($left_student=='')
					    $sql_condition.=" AND gibbonperson.dateEnd IS NULL ";
					$sql1.=" ORDER BY gibbonyeargroup.gibbonYearGroupID, gibbonrollgroup.gibbonRollGroupID, roll";
			$result1=$connection2->prepare($sql1);
			$result1->execute();
			$person_data=$result1->fetchAll();
			//$person_array=array();
			$person_group_array=array();
			foreach($person_data as $p){
				//$person_array[$p['gibbonPersonID']+0]=array($p['account_number']+0,$p['officialname'],$p['class'],$p['section'],$p['roll']);
				$person_group_array[$p['class']][$p['section']][$p['gibbonPersonID']+0]=array($p['account_number']+0,$p['officialname'],$p['class'],$p['section'],$p['roll']);
			}
			//print_r($person_data);
			//print_r($person_group_array);
}
?>
<form name="defaulter_form" id="defaulter_form" method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/defaulter_report.php" ?>">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td><b>Select Month:</b></td>
<td colspan="5">
<?php foreach ($month_squence_arr as $value) { ?> 
<label><b><?php echo ucwords($schoolyeararr[$value]);?>&nbsp;&nbsp;&nbsp;
<input type="checkbox" class='m_box' name="selected_month_report[]"  value="<?php echo $value;?>"  class="selecte_month_class" <?php if(in_array($value, $montharr)){?> checked="checked"<?php } ?>> </label>|
<?php } ?></b>
</td>
</tr>
  <tr>
	<td ><b>Select Year</b>
    <select name="year_id" id="year_id" style="" >
    <?php foreach ($yearresult as $value) { ?>
    	<option value="<?php echo $value['gibbonSchoolYearID']?>" <?php if($year==$value['gibbonSchoolYearID']){?> selected="selected"<?php } ?>><?php echo $value['name']." (".$value['status']." year)"?></option>
    	<?php } ?>
    </select>
    </td>
   <td colspan='2' ><b>Class</b>
    <select name="section_name[]" id="section_name_all" multiple  size='8' style='height:100%'>
		<option value=""> All </option>
		<?php foreach($sectionarr as $s) { 
			$str='';
			if(isset($_POST['section_name'])){
				if(in_array($s['gibbonRollGroupID'], $_POST['section_name'])){
					$str='selected';
				}
			}
				 //$section==$s['gibbonRollGroupID']?"selected":"";
		?>
		<option value='<?=$s['gibbonRollGroupID']?>' <?=$str?>><?=$s['name']?></option>
		<?php } ?>
    </select>
    </td>
    <td ><b>To date</b><input type="text" name="src_to_date" id="src_to_date" style="width: 100px;" value="<?=$enddate!=''?$enddate:date('d/m/Y');?>" placeholder=" Select Date.."></td>
    <td >
		<label><b>Left</b> <input type="checkbox" name="left_student" id="left_student" value="1" <?php if($left_student=='1'){?> checked="checked" <?php } ?>></label>
    </td>
	    <td style="max-width: 220px;"><input type="submit" name="submit" id="submit" value="Search" >
	    <?php if($_POST){?>
		<select name='view' id='view' style='margin:0' ><option>Details</option><option>Short</option></select>
		<input type="button" id="export" name="export" value="Export to messenger" style="float: right;">
	    <input type="button" id="defaulter_print_c" name="defaulter_print_c" value="Print" style="float: right;">
	    <?php } ?>
	    </td>
  </tr>
</table>
</form>

<?php 
if($_POST)
{
if ($result->rowcount()<1 && $result1->rowcount()<1) {
				print "<div class='error'>" ;
				print _("There are no records to display.") ;
				print "</div>" ;
			}
			else {
?>
<div id="print_page">
<table width="100%" cellpadding="0" cellspacing="0" border="0" id='parent-table'>
<thead>
	<tr>
		<td colspan='7' class='border-bottom'>
			<p style='text-align:center; font-weight:bold; font-size:14px; margin: 2px;'>INDRA GOPAL HIGH SCHOOL</p>
			<p style='text-align:center;  font-size:12px;margin: 2px;'><?php echo $header2["value"];?></p>
			<p style='text-align:center;  font-size:12px;margin: 2px;'>
				Defaulter Report for the month of <?=$month_name_str;?> as on <?=$enddate?>
			</p>
		</td>
	 </tr>
	<tr class="tablehead">
	    <th>Sl No</th>
		<th>Acc No</th>
		<th>Name</th>
		<th>Class</th>
		<th>Sec</th>
		<th>Roll No</th>
		<th>Months</th>
		<th>Amount</th>
	</tr>
</thead>	
<tbody>	
	<?php 
	$total=0;
	$total_slno=0;
	$classCount=count($person_group_array);
	foreach($person_group_array as $classGroup=>$sData){
		$classWiseTotal=0;
		$sectionCount=count($sData);
	foreach($sData as $sectionGroup=>$personData){
		$sectionWiseTotal=0;
		//foreach($person_array as $key=>$p){
		$slno=1;
		foreach($personData as $key=>$p){
			if(array_key_exists($key, $data_array)){
			if($exportStr==""){
				$exportStr="(".$key.")";
			}
			else{
				$exportStr.=",(".$key.")";
			}
			echo "<tr>";
				echo "<td class='border-bottom'>".$slno++."</td>";  
				echo "<td class='border-bottom'>{$p[0]}</td>";
				echo "<td class='border-bottom'>{$p[1]}</td>";
				echo "<td class='border-bottom'>{$p[2]}</td>";
				echo "<td class='border-bottom'>{$p[3]}</td>";
				echo "<td class='border-bottom'>{$p[4]}</td>";
				$total_slno+=1;
				echo "<td class='detail'  colspan='2'>"; 
					echo "<table cellpadding='0' cellspacing='0' border='0' width='100%' class='border-bottom'>";
					$sub_t=0; $i=0;
					$t_arr=sortMonthName($data_array[$key]);
						foreach($t_arr as $k){
							$sub_t+=$k[1]; $i++;
						echo "<tr><td>{$fullNameArray[trim($k[0])]}</td><td style='text-align:right;'>"; echo number_format($k[1],2);echo "</td></tr>";
						}
						$total+=$sub_t;
							if($i>1) {
								echo "<tr><td class='t'><b>Total:</b></td><td class='t' style='text-align:right; font-weight:bold;'>"; echo number_format($sub_t,2); echo "</td></tr>";
							}
					echo "</table>";
				echo "</td>";
			
				$m=""; $fm=""; $p=0; $i=0;
					foreach($t_arr as $k){
						if($i++==0) $fm=$fullNameArray[trim($k[0])];
							$p+=$k[1];
							$m=$fullNameArray[trim($k[0])];
						}
				echo "<td class='short' style='display:none;'>";
					echo $fm;
					echo $m==$fm?"":" - ".$m;
				echo "</td>";
				echo "<td class='short' style='display:none;text-align:right;'>";
					echo number_format($p,2);
				echo "</td>";
				$classWiseTotal+=$p;
				$sectionWiseTotal+=$p;
			}
		}
		if($sectionCount>1 && $sectionWiseTotal>0){
		?>
		<tr>
			<td colspan="6" style="text-align:right" class='border-bottom'><b>Total for <?=$classGroup?> - <?=$sectionGroup?>:</b></td>
			<td style="text-align:right" class='border-bottom'><b><?php echo number_format($sectionWiseTotal, 2);?></b></td>
		</tr>
		<?php
		}
	}
	if($classCount>1){
	?>
	<tr>
		<td colspan="6" style="text-align:right" class='border-bottom'><b>Sub Total for Class <?=$classGroup?> :</b></td>
		<td style="text-align:right" class='border-bottom'><b><?php echo number_format($classWiseTotal, 2);?></b></td>
	</tr>
	<?php
	}
	}
	//echo $exportStr;
	?>
</tbody>
	<tr>
		<th colspan="6" style="text-align:right">Grand Total</th>
		<th style="text-align:right"><b><?php echo number_format($total, 2);?></b></th>
	</tr>	
</table>

<table width="50%" cellpadding="0" cellspacing="0" border="0" align="center" style="page-break-inside: avoid;">
	<tbody>
		<tr><th>Fee Head</th><th>Amount</th></tr>
	<?php
	$total1=0;
	foreach ($fee_data_summary as $value) { 
		$total1+=$value['total_amount'];
		?>
		  <tr>
			<td><?php echo $value['fee_type_name'];?></td>
			<td style="text-align:right;"><?php echo $value['total_amount'];?></td>
		  </tr>
	<?php 
	}
	if($total_transport_summarry>0){
	?>
		<tr>
		<td>Transport</td>
		<td style="text-align:right"><?php echo number_format($total_transport_summarry, 2, '.', ''); ?></td>
	  </tr>
	<?php } ?>
	  <tr>
		<td><b>Total :  <?php echo ' ',$total_slno,' Student/s';?></b></td>
		<td style="text-align:right"><b><?php echo number_format($total1+$total_transport_summarry, 2, '.', ',');?></b></td>
	  </tr>
	</tbody>
</table>

</div>
<input type='hidden' name='month_name' id='month_name' value='<?=$month_name_str;?>'>
<?php } } ?>
<input type='hidden' name='exportStr' id='exportStr' value='<?=$exportStr;?>'>
<script type="text/javascript">
		$(function() {
			$( "#src_from_date" ).datepicker({ dateFormat: 'dd/mm/yy' });
			$( "#src_to_date" ).datepicker({ dateFormat: 'dd/mm/yy' });
		});
</script>
<input type="hidden" name="print_page_url" id="print_page_url" value="<?php echo $_SESSION[$guid]["absoluteURL"] . "/modules/Fee/defaulter_report_print.php";?>">
<input type="hidden" name="export_page_url" id="export_page_url" value="<?php echo $_SESSION[$guid]["absoluteURL"] . "/modules/Fee/export_to_messenger.php";?>">
<input type="hidden" name="rollgroup_url" id="rollgroup_url" value="<?php echo $_SESSION[$guid]["absoluteURL"] . "/modules/Fee/ajax_change_rollgroup.php";?>">

<!--  Script for  loading section select list  -->
<script>

$(document).ready(function(){
		<?php if(!$_POST) { ?>
		$('.m_box').each(function(){
			$(this).prop('checked',true);
		});
		<?php } ?>
		$('#export').click(function(){
			var val = $('#exportStr').val();
			var processURL=$('#export_page_url').val();
			$.ajax({
				type: "POST",
				url: processURL,
				data: {value:val},
				success: function(msg)
				{ 
					alert(msg);
				}
			});
		});
	$("#class_name,#year_id").change(function(){
	//alert("Hululu");
	var yearGroup=$("#class_name").val();
	var schoolYear=$("#year_id").val();
	var url=$("#rollgroup_url").val();
	$.ajax({
		type: "POST",
		url: url,
		data: {yearGroup: yearGroup, schoolYear: schoolYear},
		success: function(msg)
		{
			console.log(msg);
			$("#section_name_all").empty().append("<option value=''>All</option>" + msg);
		}
	});
});
}) 
var style="<style>#parent-table{ border: 1px solid;}td,th{font-size:12px;} thead th {border-bottom: 1px solid}";
	style+=".border-bottom{border-bottom: 1px solid;} .t{border-top: .5px dashed;}";
	style+=" thead {display: table-header-group;}  tr{page-break-inside: avoid;}</style>";
</script>
<!--  Script for  loading section select list  -->
<?php
};
function sortMonthName($arr){
	//$month_sequence=array('yearly'=>0,'apr'=>4,'may'=>5,'jun'=>6,'jul'=>7,'aug'=>8,'sep'=>9,'oct'=>10,'nov'=>11,'dec'=>12,'jan'=>13,'feb'=>14,'mar'=>15);
	
	$month_sequence=array('yearly'=>0,'jan'=>1,'feb'=>2,'mar'=>3,'apr'=>4,'may'=>5,'jun'=>6,'jul'=>7,'aug'=>8,'sep'=>9,'oct'=>10,'nov'=>11,'dec'=>12);
	
	
	
	$r_array=array();
	foreach($arr as $k=>$v){
		if(!array_key_exists($k,$month_sequence))
			continue;
		$a=$month_sequence[$k]+0;
		$r_array[$a]=array($k,$v);
	}
	ksort($r_array);
	return $r_array;
}
?>
