<?php
@session_start() ;
$gibbonPersonID=NULL ;
if (isset($_SESSION[$guid]["gibbonPersonID"])) {
	$gibbonPersonID=$_SESSION[$guid]["gibbonPersonID"] ;
}
$sql="SELECT * from gibbonschoolyear ORDER BY firstDay DESC";
$result=$connection2->prepare($sql);
$result->execute();
$yearresult=$result->fetchAll();

$sql="SELECT * from fee_type_master order by fee_type_name";
$result=$connection2->prepare($sql);
$result->execute();
$fee_type_master=$result->fetchAll();

$sql="SELECT * from fee_boarder_class";
$result=$connection2->prepare($sql);
$result->execute();
$fee_boarder_class=$result->fetchAll();

$sql="SELECT * from fee_rule_master where fee_rule_master_id=$_REQUEST[rule_id]";
$result=$connection2->prepare($sql);
$result->execute();
$fee_type_date=$result->fetch();
 $year=$fee_type_date['gibbonSchoolYearID'];
 $startDate = date("d/m/Y", strtotime($fee_type_date['effected_date_start']));
 $endtDate = date("d/m/Y", strtotime($fee_type_date['effected_date_end']));
?>
<form name="f1" id="f1" method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/fee_process.php" ?>">
<table width="100%" cellpadding="0" cellspacing="0" border="0">  
  
  <tr>
    <td>Fee Type</td>
    <td>
    <select name="fee_type_master_id" id="fee_type_master_id">
	<?php foreach ($fee_type_master as $value) { ?>
		
			<option value="<?php echo $value['fee_type_master_id'];?>" <?php if($fee_type_date['fee_type_master_id']==$value['fee_type_master_id']){?> selected="selected"<?php } ?>><?php echo $value['fee_type_name'];?> <?php //echo $value['boarder_type_name'];?> </option>
		
	<?php } ?>
	</select>
	</td>
  </tr>
   <tr>
    <td>Fee Border Class</td>
    <td>
    <select name="fee_boarder_class_id" id="fee_boarder_class_id">
<?php foreach ($fee_boarder_class as $value) { ?>
<option value="<?php echo $value['fee_boarder_class_id']?>" <?php if($fee_type_date['fee_boarder_class_id']==$value['fee_boarder_class_id']){?> selected="selected"<?php } ?>>Class - <?php echo $value['class']?> <?php //echo $value['border_type_name'];?></option>
	<?php  } ?>
	</select>
	</td>
  </tr>
   
   <tr>
   <td>Select Year </td>
    <td>
	<select name="filter_year" id="filter_year" onchange="jsSetDate(this.value)" style="width:170px"> 
			<option>Select Year</option>
			 <?php foreach ($yearresult as $value) { ?>			 
    	<option value="<?php echo $value['gibbonSchoolYearID']?>" <?php if($year==$value['gibbonSchoolYearID']){?> selected="selected"<?php } ?>><?php echo $value['name']." (".$value['status']." year)"?></option>
    	<?php } ?>
    </select>
	</td>
  </tr> 
  
   <tr>
    <td>Effective Date Start </td>
    <td>
	<input type="text" name="effected_date_start" id="effected_date_start" value="<?php echo $startDate;?>">
	</td>
  </tr>
   <tr>
    <td>Effective Date End </td>
    <td>
	<input type="text" name="effected_date_end" id="effected_date_end" value="<?php echo $endtDate;?>">
	</td>
  </tr>
  <tr>
    <td>Amount</td>
    <td>
	<input type="text" name="amount" id="amount" value="<?php echo $fee_type_date['amount'];?>">
	</td>
  </tr>
  <tr>
    <td>Is Onetime ?</td>
    <td>
		<select name='onetime' required style='width:160px'>
			<option value=''>Select</option>
			<option value='1' <?=$fee_type_date['onetime']==1?'selected':''?>> Yes </option>
			<option value='0' <?=$fee_type_date['onetime']==0?'selected':''?>> No </option>
		</select>
	</td>
  </tr>
  <tr>
    <td>Remark</td>
    <td>
		<textarea name="rule_description" id="rule_description"><?php echo $fee_type_date['rule_description']?></textarea>
	</td>
  </tr>
   <tr>
    <td></td>
    <td>
    <input type="hidden" name="fee_rule_master_id" id="fee_rule_master_id" value="<?php echo $fee_type_date['fee_rule_master_id'];?>">
    <input type="hidden" name="job" id="job" value="edit_rule">
	<input type="submit" name="save" id="save" value="Save">
	</td>
  </tr>
</table>
</form>
<!--<script type="text/javascript">
		$(function() {
			$( "#effected_date_start" ).datepicker({ dateFormat: 'dd/mm/yy' });
			$( "#effected_date_end" ).datepicker({ dateFormat: 'dd/mm/yy' });
		});
</script>-->
<script type="text/javascript">
var start_day = {<?php
					foreach ($yearresult as $a) { echo '"'.$a["gibbonSchoolYearID"].'":"'.date("d/m/Y",strtotime($a['firstDay'])).'",';}
					?> 1:1};
var end_day = {<?php
					foreach ($yearresult as $a) { echo '"'.$a['gibbonSchoolYearID'].'":"'.date("d/m/Y",strtotime($a['lastDay'])).'",';}
					?> 1:1};
function jsSetDate(year) {
document.getElementById("effected_date_start").value = start_day[year];
document.getElementById("effected_date_end").value =end_day[year];
}
</script>
<script>
$(document).ready(function(){

})
</script>