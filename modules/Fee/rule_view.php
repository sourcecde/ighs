 <?php
@session_start() ;

if (isActionAccessible($guid, $connection2, "/modules/Fee/rule_view.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {

$gibbonPersonID=NULL ;
if (isset($_SESSION[$guid]["gibbonPersonID"])) {
	$gibbonPersonID=$_SESSION[$guid]["gibbonPersonID"] ;
}
$sql="SELECT * from gibbonschoolyear ORDER BY firstDay DESC";
$result=$connection2->prepare($sql);
$result->execute();
$yearresult=$result->fetchAll();
$year='';
$fee_type='';
$class='';
$border='';
$isOnetime=-1;
if($_POST)
{
	$sql="SELECT fee_rule_master.*,fee_type_master.fee_type_name,fee_boarder_class.class,fee_boarder_class.border,fee_boarder_class.border_type_name
	FROM fee_rule_master
	LEFT JOIN fee_type_master ON fee_type_master.fee_type_master_id=fee_rule_master.fee_type_master_id 
	LEFT JOIN fee_boarder_class ON fee_boarder_class.fee_boarder_class_id=fee_rule_master.fee_boarder_class_id where active_inactive=1";
	if($_REQUEST['filter_type'])
	{
		$sql.=" AND fee_rule_master.fee_type_master_id=".$_REQUEST['filter_type'];
		$fee_type=$_REQUEST['filter_type'];
	}
		if($_REQUEST['filter_year'])
	{
		$year=$_REQUEST['filter_year'];
		if($year!=0)
		$sql.=" AND fee_rule_master.gibbonSchoolYearID=".$_REQUEST['filter_year'];
		
	}
	if($_REQUEST['filter_class'])
	{
		$sql.=" AND fee_boarder_class.class='".$_REQUEST['filter_class']."'";
		$class=$_REQUEST['filter_class'];
	}
	
	if($_REQUEST['border_fileter'])
	{
		$sql.=" AND fee_boarder_class.border='".$_REQUEST['border_fileter']."'";
		$border=$_REQUEST['border_fileter'];
	}
	if($_REQUEST['onetime_filter']!=-1)
	{	
		$sql.=" AND `fee_rule_master`.`onetime`='{$_REQUEST['onetime_filter']}'";
		$isOnetime=$_REQUEST['onetime_filter'];
	}
	$sql.=" order by fee_rule_master_id";
}
else 
{
	$sql="SELECT fee_rule_master.*,fee_type_master.fee_type_name,fee_boarder_class.class,fee_boarder_class.border,fee_boarder_class.border_type_name FROM fee_rule_master LEFT JOIN fee_type_master 
ON fee_type_master.fee_type_master_id=fee_rule_master.fee_type_master_id 
LEFT JOIN fee_boarder_class ON fee_boarder_class.fee_boarder_class_id=fee_rule_master.fee_boarder_class_id order by fee_rule_master_id;";
}
$result=$connection2->prepare($sql);
$result->execute();
$dboutbut=$result->fetchAll();
$URL=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Fee/rule_edit.php" ;


if(isset($_REQUEST['filter_type']))
{
	
}

if(isset($_REQUEST['filter_class']))
{
	
}

if(isset($_REQUEST['border_fileter']))
{
	
}

//get rule type masteree
$sql='Select fee_type_master_id,fee_type_name,boarder,boarder_type_name from fee_type_master';
$result=$connection2->prepare($sql);
$result->execute();
$all_fee_type=$result->fetchAll();

//get class
$sql='SELECT class FROM fee_boarder_class GROUP BY class';
$result=$connection2->prepare($sql);
$result->execute();
$all_class=$result->fetchAll();

?>
<form name="f1" id="f1" method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/rule_view.php" ?>">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td>
    <select name="filter_type" id="filter_type" style="width:250px;">
       <option value="">Select Fee</option>
       <?php foreach ($all_fee_type as $value) { 
       	$boarder='';
       	if($value['boarder']=='yes')$boarder='Boarder';else $boarder='Non-Boarder';
       
       	?>
       <option value="<?php echo $value['fee_type_master_id']?>" <?php if($fee_type==$value['fee_type_master_id']){?> selected="selected"<?php } ?>><?php echo $value['fee_type_name']?>  - <?php echo $value['boarder_type_name'];?></option>
      <?php } ?> 
    	
    </select>
    </td>
	 <td>
    <select name="filter_year" id="filter_year" style="width:100px;">
		<option>Select Year</option>
			 <?php foreach ($yearresult as $value) { ?>
    	<option value="<?php echo $value['gibbonSchoolYearID']?>" <?php if($year==$value['gibbonSchoolYearID']){?> selected="selected"<?php } ?>><?php echo $value['name']." (".$value['status']." year)"?></option>
    	<?php } ?>
    </select>
    </td>
    <td>
    <select name="filter_class" id="filter_class" style="width:100px;">
    	<option value="">Select Class</option>
    	
    	<?php foreach ($all_class as $value) { ?>
    		
    	<option value="<?php echo $value['class'];?>" <?php if($value['class']==$class){?> selected="selected"<?php } ?>><?php echo $value['class'];?></option>
    	<?php } ?>
    	
    </select>
    </td>
    <!---<td>
		<select name="border_fileter" id="border_fileter">
		<option value="">Select Border</option>
		<option value="Y" <?php if($border=='Y'){?> selected="selected"<?php } ?>>Border</option>
		<option value="N" <?php if($border=='N'){?> selected="selected"<?php } ?>>Non Border</option>
		 <option value="D" <?php if($border=='D'){?> selected="selected"<?php } ?>>Day Border</option>
		</select>
    </td>--->
	<input type="hidden" name="border_fileter" value="N">
	<td>
		<select name='onetime_filter' id='onetime_filter'>
			<option value='-1' >Select Onetime</option>
			<option value='1' <?=$isOnetime==1?'selected':''?>>Yes</option>
			<option value='0' <?=$isOnetime==0?'selected':''?>>No</option>
		</select>
	</td>
    <td>
    <input type="submit" name="search" id="search" value="Search">
    </td>
  </tr>
</table>
</form>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <th>Type</th>
    <th>Class</th>
    <th>Border</th>
    <th>Date Start</th>
    <th>Date End</th>
    <th>Amount</th>
    <th>One Time</th>
     <th>Remark</th>
    <th><div style='background: #419562;padding: 6px;text-align: center;'><a href="<?php print $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Fee/rule_create.php" ?>" style='color:white;'>Create Rule</a></div></th>
  </tr>
  <?php
	$total=0;
  foreach ($dboutbut as $value) { 
  $startDate = date("d/m/Y", strtotime($value['effected_date_start']));
  $endtDate = date("d/m/Y", strtotime($value['effected_date_end']));
  	?>
  <tr>
   
    <td><?php echo $value['fee_type_name'];?></td>
    <td><?php echo $value['class'];?></td>
    <td><?php echo ucfirst($value['border_type_name']);?></td>
    <td><?php echo $startDate;?></td>
    <td><?php echo $endtDate;?></td>
    <td style="text-align:right;"><?php echo $value['amount'];?></td>
    <td><?=$value['onetime']==1?'Yes':'No';?></td>
    <td><?php echo $value['rule_description'];?></td>
    <td><a href="<?php echo $URL.'&rule_id='.$value['fee_rule_master_id']?>" style='color:darkblue;text-decoration:underline;'>Edit</a> | <a href="javascript:void(0)" class="delete_fee" id="<?php echo $value['fee_rule_master_id'];?>" style='color:darkblue;text-decoration:underline;'>Delete</a></td>
  </tr>
  <?php 
	$total+=$value['amount'];
  }?>
  <tr>
   
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td style="text-align:right;">Total: <?php echo $total;?></td>
    <td></td>
    <td></td>
  </tr>
</table>
<input type="hidden" name="delete_url" id="delete_url" value="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/fee_ajax.php" ?>">

<?php
};
?>