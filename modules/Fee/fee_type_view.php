<?php
@session_start() ;
if (isActionAccessible($guid, $connection2, "/modules/Fee/fee_type_view.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else 
{
$gibbonPersonID=NULL ;
if (isset($_SESSION[$guid]["gibbonPersonID"])) {
	$gibbonPersonID=$_SESSION[$guid]["gibbonPersonID"] ;
}
$sql="SELECT * from fee_type_master order by fee_type_name";
$result=$connection2->prepare($sql);
$result->execute();
$dboutbut=$result->fetchAll();

$sql="SELECT * from gibbonschoolyear where status='Current'";
$result=$connection2->prepare($sql);
$result->execute();
$schoolyearresult=$result->fetch();
$firstdayarr=explode("-", $schoolyearresult['firstDay']);
$firstday=(int)$firstdayarr[1];

$lastdayarr=explode("-", $schoolyearresult['lastDay']);
$lastday=(int)$lastdayarr[1];

$schoolyeararr=array(1=>'jan',2=>'feb',3=>'mar',4=>'apr',5=>'may',6=>'jun',7=>'jul',8=>'aug',9=>'sep',10=>'oct',11=>'nov',12=>'dec');
?>
<table width="100%" cellpadding="0" cellspacing="0" border="0" class="feemaster">
	<tr>
		<th>Fee Type</th>
		<th>Boarder</th>
		<th>Yearly</th>
		<?php for($i=$firstday;$i<=12;$i++){?>
		<th><?php echo ucfirst($schoolyeararr[$i]);?></th>
		<?php } ?>
		<?php for($i=1;$i<=$lastday;$i++) {?>
		<th><?php echo ucfirst($schoolyeararr[$i]);?></th>
		<?php } ?>
	</tr>
<?php foreach ($dboutbut as $value) { ?>
		<tr>
		<td><?php echo $value['fee_type_name'];?></td>
		<td><?php echo ucfirst($value['boarder_type_name']);?></td>
		<td><?php echo $value['yearly']==0?'No':'Yes';?></td>
		<?php for($i=$firstday;$i<=12;$i++){?>
		<td><?php echo $value[$schoolyeararr[$i]]==0?'No':'Yes';?></td>
		<?php } ?>
		<?php for($i=1;$i<=$lastday;$i++) {?>
		<td><?php echo $value[$schoolyeararr[$i]]==0?'No':'Yes';?></td>
		<?php } ?>
		
	</tr>
	<?php }?>
</table>
<?php } ?>