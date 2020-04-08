<?php 
@session_start() ;

if (isActionAccessible($guid, $connection2, "/modules/Fee/transport_defaulter.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {


$exportStr="";
error_reporting(E_ALL);
$schoolyeararr=array(0=>'yearly',1=>'jan',2=>'feb',3=>'mar',4=>'apr',5=>'may',6=>'jun',7=>'jul',8=>'aug',9=>'sep',10=>'oct',11=>'nov',12=>'dec');
$fullNameArray=array('yearly'=>'Yearly','jan'=>'January','feb'=>'February','mar'=>'March  ','apr'=>'April  ','may'=>'May    ','jun'=>'June   ','jul'=>'July   ','aug'=>'August','sep'=>'September','oct'=>'October','nov'=>'November','dec'=>'December');
//For SchoolYear(Session) Info
$sql="SELECT * from `gibbonschoolyear` ORDER BY `status` ASC";
$result=$connection2->prepare($sql);
$result->execute();
$yearresult=$result->fetchAll();

// For Class Info
try{
$sql="SELECT * FROM `gibbonyeargroup`";
$result=$connection2->prepare($sql);
$result->execute();
$schoolyearresult=$result->fetchAll();
}
catch(PDOException $e) { 
	echo $e;
}

//For Section Info
$sql="SELECT * from `gibbonrollgroup` WHERE gibbonSchoolYearID=";
if(isset($_REQUEST['year_id']))
$sql.=$_REQUEST['year_id'];
else
$sql.=$yearresult[0]['gibbonSchoolYearID'];
$result=$connection2->prepare($sql);
$result->execute();
$sectionarr=$result->fetchAll();
// getting the months 
$month_squence_arr=array(0);
$firstdayarr=explode("-", $yearresult[0]['firstDay']);
$firstday=(int)$firstdayarr[1];

$lastdayarr=explode("-", $yearresult[0]['lastDay']);
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
$montharr=array();
$year='';
$class='';
$section='';
$left_student='';
$enddate='';
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
		$query="SELECT `transport_month_entry`.gibbonPersonID, price AS transport_price,month_name
		FROM `transport_month_entry` 
		LEFT JOIN `payment_master` ON `transport_month_entry`.payment_master_id=`payment_master`.payment_master_id 
		LEFT JOIN `gibbonperson` ON `transport_month_entry`.`gibbonPersonID`=`gibbonperson`.`gibbonPersonID`  
		WHERE (".$query_transport_mont_str.") AND ( `transport_month_entry`.payment_master_id =0    OR(`transport_month_entry`.payment_master_id>0 AND  `payment_master`.`payment_date` >  '".$src_end_date."')) AND `transport_month_entry`.`gibbonSchoolYearID` =$year";
		if($left_student=='')
			$query.=" AND `gibbonperson`.dateEnd IS NULL ";
		$result1=$connection2->prepare($query);
		$result1->execute();
		$transport_price=$result1->fetchAll();
		//echo $query."<br>";
		//print_r($transport_price);
	}
	catch(PDOException $e) { 
		print "<div class='error'>" . $e->getMessage() . "</div>" ; 
	}
			$data_array=array();
			
			foreach($transport_price as $f){
				if(array_key_exists($f['gibbonPersonID']+0,$data_array)){
					// if(array_key_exists($f['month_name'],$data_array[$f['gibbonPersonID']+0]))
						// $data_array[$f['gibbonPersonID']+0][$f['month_name']]+=$f['transport_price'];
					// else
						$data_array[$f['gibbonPersonID']+0][$f['month_name']]=$f['transport_price'];
				}
				else
				$data_array[$f['gibbonPersonID']+0][$f['month_name']]=$f['transport_price'];
			} 
			  //print_r($data_array);
			  $personIdList="";
				  foreach(array_keys($data_array) as $p){
					 $personIdList.=$personIdList!=""?",".$p:$p; 
				  }
			 // echo $personIdList;
			 $person_array=array();
			$sql1="SELECT  `gibbonperson`.gibbonPersonID,`gibbonperson`.officialName as officialname,`gibbonperson`.account_number,
					`gibbonyeargroup`.name AS class ,`gibbonrollgroup`.name AS section,`gibbonstudentenrolment`.rollOrder AS roll
					FROM  `gibbonperson` 
					LEFT JOIN `gibbonstudentenrolment` ON `gibbonperson`.gibbonPersonID=`gibbonstudentenrolment`.gibbonPersonID
					LEFT JOIN `gibbonyeargroup` ON `gibbonstudentenrolment`.gibbonYearGroupID=`gibbonyeargroup`.gibbonYearGroupID 
					LEFT JOIN `gibbonrollgroup` ON `gibbonstudentenrolment`.gibbonRollGroupID=`gibbonrollgroup`.gibbonRollGroupID 
					WHERE  `gibbonperson`.gibbonPersonID IN ($personIdList)";
					//WHERE `gibbonrollgroup`.gibbonSchoolYearID=$year AND `gibbonperson`.gibbonPersonID IN ($personIdList)";
					// if($class!='')
						// $sql1.=" AND `gibbonyeargroup`.gibbonYearGroupID=".$class;
					// if($section!='')
						// $sql1.=" AND `gibbonrollgroup`.gibbonRollGroupID=".$section;
					// if($left_student=='')
						// $sql1.=" AND `gibbonperson`.dateEnd IS NULL ";
					$sql1.=" ORDER BY `gibbonperson`.account_number";
			if($personIdList!=""){
				$result1=$connection2->prepare($sql1);
				$result1->execute();
				$person_data=$result1->fetchAll();
				foreach($person_data as $p){
					$person_array[$p['gibbonPersonID']+0]=array($p['account_number']+0,$p['officialname'],$p['class'],$p['section'],$p['roll']);
				}
			}
			//print_r($person_data);
}
?>
<form name="defaulter_form" id="defaulter_form" method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/transport_defaulter.php" ?>">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td><b>Select Month:</b></td>
<td colspan="5">
<?php foreach ($month_squence_arr as $value) { 
if($value!=0){?> 
<label><b><?php echo ucwords($schoolyeararr[$value]);?>&nbsp;&nbsp;&nbsp;
<input type="checkbox" class='m_box' name="selected_month_report[]"  value="<?php echo $value;?>"  class="selecte_month_class" <?php if(in_array($value, $montharr)){?> checked="checked"<?php } ?>> </label>|
<?php } 
}?></b>
</td>
</tr>
  <tr>
    <td > <b>Class&nbsp;&nbsp;</b> 
    <select name="class_name" id="class_name">
		<option value=""> All </option>
    <?php foreach ($schoolyearresult as $value) { ?>
    	<option value="<?php echo $value['gibbonYearGroupID']?>" <?php if($class==$value['gibbonYearGroupID']){?> selected="selected"<?php } ?>><?php echo $value['name']?></option>
    	<?php } ?>
    </select>
    </td>
    <td ><b>Section</b>
    <select name="section_name" id="section_name_all">
		<option value=""> All </option>
		<?php foreach($sectionarr as $s) { 
				$str=$section==$s['gibbonRollGroupID']?"selected":"";
		?>
		<option value='<?=$s['gibbonRollGroupID']?>' <?=$str?>><?=$s['name']?></option>
		<?php } ?>
    </select>
    </td>
	<td ><b>Select Year</b>
    <select name="year_id" id="year_id" style="" >
    <?php foreach ($yearresult as $value) { ?>
    	<option value="<?php echo $value['gibbonSchoolYearID']?>" <?php if($year==$value['gibbonSchoolYearID']){?> selected="selected"<?php } ?>><?php echo $value['name']." (".$value['status']." year)"?></option>
    	<?php } ?>
    </select>
    </td>
    
    <td ><b>To date</b><input type="text" name="src_to_date" id="src_to_date" style="width: 100px;" value="<?=$enddate!=''?$enddate:date('d/m/Y');?>" placeholder=" Select Date.."></td>
    <td >
		<label><b>Left</b> <input type="checkbox" name="left_student" id="left_student" value="1" <?php if($left_student=='1'){?> checked="checked" <?php } ?>></label>
    </td>
	    <td style="max-width: 220px;"><input type="submit" name="submit" id="submit" value="Search" >
	    <?php if($_POST){?>
		<select name='view' id='view' style='margin:0' ><option>Short</option><option>Deatils</option></select>
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
if (empty($data_array)) {
				print "<div class='error'>" ;
				print _("There are no records to display.") ;
				print "</div>" ;
			}
			else {
?>
<div id="print_page">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<thead>
	<tr class="tablehead">
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
		foreach($person_array as $key=>$p){
			if(array_key_exists($key, $data_array)){
			if($exportStr==""){
				$exportStr="(".$key.")";
			}
			else{
				$exportStr.=",(".$key.")";
			}
			echo "<tr>";
				echo "<td style='text-align:right'>{$p[0]}</td>";
				echo "<td>{$p[1]}</td>";
				echo "<td>{$p[2]}</td>";
				echo "<td>{$p[3]}</td>";
				echo "<td>{$p[4]}</td>";
				
				echo "<td class='detail'  colspan='2'  style='display:none;'>"; 
					echo "<table cellpadding='0' cellspacing='0' border='0' width='100%'>";
					$sub_t=0;
					
					$t_arr=sortMonthName($data_array[$key]);
						foreach($t_arr as $k=>$v){
							$sub_t+=$v;
							echo "<tr><td>{$fullNameArray[trim($k)]}</td><td style='text-align:right;'>"; echo number_format($v,2);echo "</td></tr>";
						}
						$total+=$sub_t;
						if(count($t_arr)>1){
							echo "<tr><td class='t'><b>Total:</b></td><td class='t' style='text-align:right; font-weight:bold;'>"; echo number_format($sub_t,2); echo "</td></tr>";
						}
						
					echo "</table>";
				echo "</td>";
			
				$m=""; $fm=""; $p=0; $i=0;
				$fm=array_keys($t_arr)[0];
				$m=array_keys($t_arr)[count($t_arr)-1];
				echo "<td class='short'>";
					echo $fullNameArray[trim($fm)];
					echo $m==$fm?"":" - ".$fullNameArray[trim($m)];
				echo "</td>";
				echo "<td class='short'>";
					echo number_format($sub_t,2);
				echo "</td>";
			
			}
		}
	?>
</tbody>
	<tr>
		<th colspan="6" style="text-align:right">Sub Total</th>
		<th style="text-align:right"><b><?php echo number_format($total, 2);?></b></th>
	</tr>	
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
}) 
</script>
<!--  Script for  loading section select list  -->
<span id='cstyle'>
<style>
	.p_table {
		border-left:1px solid #000000; border-top:1px solid #000000; 
	}
	.p_head{
		padding:5px; border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; 
	}
	.p_td{
		padding:5px; border-bottom:1px solid #000000; 	border-right:1px solid #000000; font-size:14px; color:#000000;
	}
</style>
</span>

<?php
};
?>
<?php 
function sortMonthName($arr){
	$month_sequence=array('yearly'=>0,'apr'=>4,'may'=>5,'jun'=>6,'jul'=>7,'aug'=>8,'sep'=>9,'oct'=>10,'nov'=>11,'dec'=>12,'jan'=>13,'feb'=>14,'mar'=>15);
	$tmp_array=array();
	foreach($month_sequence as $m=>$v){
		if(array_key_exists($m,$arr))
			$tmp_array[$m]=$arr[$m];
	}
	return $tmp_array;
}

?>