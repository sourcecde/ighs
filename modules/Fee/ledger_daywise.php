<?php
@session_start() ;

if (isActionAccessible($guid, $connection2, "/modules/Fee/ledger_daywise.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
	
$postURL=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/ledger_daywise.php";

$sql="SELECT `gibbonSchoolYearID`, `name`, `status` FROM `gibbonschoolyear`";
$result=$connection2->prepare($sql);
$result->execute();
$schoolYear=$result->fetchAll();
 ?>
 <h3>View Ledger Daywise: </h3>
 <div style='border: 1px solid #7030a0; margin:10px; padding:5px;'>
	<form name="f1" id="f1" method="post" action="<?=$postURL;?>">
	<table width='100%' cellpadding='0' cellspacing='0'>
		<tr>
			<td>
				<select id='year_id' name='year_id' style='float:left' required>
					<option value=''> Select Year </option>
					<?php foreach($schoolYear as $s){
						$y=isset($_REQUEST['year_id'])?$_REQUEST['year_id']:"";
						$sl=$y==$s['gibbonSchoolYearID']?'selected':'';
						echo "<option value='{$s['gibbonSchoolYearID']}' $sl>{$s['name']} ({$s['status']})</option>";
					} ?>
				</select>
			</td>
			<td>
				<select id='section_name' name='section_name' style='float:left; width:200px' required>
					<option value=''> Select Section </option>
						<?php
							if(isset($_REQUEST['submit'])){
								$sql="SELECT `gibbonrollgroup`.`gibbonRollGroupID`,`gibbonrollgroup`.`name` as `section`,`gibbonyeargroup`.`name` as `class` FROM `gibbonrollgroup`,`gibbonyeargroup` WHERE `gibbonrollgroup`.`gibbonYearGroupID`=`gibbonyeargroup`.`gibbonYearGroupID` AND `gibbonSchoolYearID`=".$_REQUEST['year_id']." ORDER BY `gibbonrollgroup`.`gibbonYearGroupID`,`gibbonrollgroup`.`name`";
								$result=$connection2->prepare($sql);
								$result->execute();
								$section=$result->fetchAll();
								foreach($section as $s){
									$sl=$_REQUEST['section_name']==$s['gibbonRollGroupID']?'selected':'';
									echo "<option value='{$s['gibbonRollGroupID']}' $sl>{$s['class']} {$s['section']}</option>";
								}
							}
						?>
					<!-- Data will be loaded from ajax -->
				</select>
			</td>
			<td>
				<input type='submit' name='submit' value='GO'>
			</td>
			<?php if(isset($_REQUEST['submit'])) {?>
			<td>
				<span id='ledger_print' class='c_button'>Print</span>
			</td>
			<?php } ?>
		</tr>
	</table>
	</form>
</div>
<?php if(isset($_REQUEST['submit'])){

include("modules/" . $_SESSION[$guid]["module"] . "/ledger_template.php");
 ?>
 <input type='hidden' name='ajaxURL' id='ajaxURL' value='<?=$_SESSION[$guid]["absoluteURL"] ."/modules/" . $_SESSION[$guid]["module"] . "/ajax_ledger.php"?>'>
 <input type='hidden' name='action' id='action' value='getDaywise'>
 <input type='hidden' name='dataArray' id='dataArray' value='<?php echo json_encode(array('year_id'=>$_REQUEST['year_id'])); ?>'>
<div style="width:70%; border: 0px solid; float:left; display:none;" id='expand'>

</div>
<?php
 }
}
?>
<form class='form_ledger_print' method='POST' action='<?=$_SESSION[$guid]["absoluteURL"] ."/modules/" . $_SESSION[$guid]["module"] . "/ledger_daywise_print.php"?>' target='_blank'>
<input type='hidden' name='print_year' id='print_year' value=''>
<input type='hidden' name='print_section' id='print_section' value=''>
<input type='hidden' name='personID_array' id='personID_array' value=''>
</form>