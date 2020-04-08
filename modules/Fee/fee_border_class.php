<?php
@session_start() ;
$gibbonPersonID=NULL ;
if (isset($_SESSION[$guid]["gibbonPersonID"])) {
	$gibbonPersonID=$_SESSION[$guid]["gibbonPersonID"] ;
}

$sql="SELECT * from fee_boarder_class order by class";
$result=$connection2->prepare($sql);
$result->execute();
$dboutbut=$result->fetchAll();
?>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<th>Class</th>
<th>Border</th>
</tr>
<?php foreach ($dboutbut as $value) { ?>
<tr>
<td><?php echo $value['class'];?></td>
<td><?php echo ucfirst($value['border_type_name']);?></td>
</tr>
	<?php } ?>
</table>