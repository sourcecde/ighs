<?php 
@session_start() ;
$gibbonPersonID=NULL ;
if (isset($_SESSION[$guid]["gibbonPersonID"])) {
	$gibbonPersonID=$_SESSION[$guid]["gibbonPersonID"] ;
}
?>
<?php if(isset($_REQUEST['menu_id'])){
$sql="SELECT menu_name,id,order_sequence,active_inactive FROM menu where id=$_REQUEST[menu_id]";
			$result=$connection2->prepare($sql);
			$result->execute();
			$dboutbut=$result->fetch();
			
	?>
	<form name="f1" id="f1" method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/menu_process.php" ?>">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td>menu Name</td>
<td><input type="text" name="new_menu" id="new_menu" value="<?php echo $dboutbut['menu_name'];?>"></td>
</tr>
<tr>
<td>menu Position</td>
<td><input type="text" name="new_menu_position" id="new_menu_position" value="<?php echo $dboutbut['order_sequence'];?>"></td>
</tr>
<tr>
<td >Activation</td>
<td >
<input type="checkbox" name="active_inactive" id="active_inactive" <?php if($dboutbut['active_inactive']==1){?> checked="checked"<?php } ?> value="1" style="float: right;">
</td>
</tr>
<tr>
<td></td>
<td ><input type="submit" name="save_new_menu" id="save_new_menu" style="float: right;" value="Submit"></td>
</tr>
</table>
<input type="hidden" name="editmenu" id="editmenu" value="editmenu">
<input type="hidden" name="menu_id" id="menu_id" value="<?php echo $_REQUEST['menu_id'];?>">
</form>
<?php } else {?>
<form name="f1" id="f1" method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/menu_process.php" ?>">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td>menu Name</td>
<td><input type="text" name="new_menu" id="new_menu"></td>
</tr>
<tr>
<td>menu Position</td>
<td><input type="text" name="new_menu_position" id="new_menu_position"></td>
</tr>
<tr>
<td >Activation</td>
<td >
<input type="checkbox" name="active_inactive" id="active_inactive"  value="1" style="float: right;">
</td>
</tr>
<tr>
<td></td>
<td ><input type="submit" name="save_new_menu" id="save_new_menu" style="float: right;" value="Submit"></td>
</tr>
</table>
<input type="hidden" name="newmenu" id="newmenu" value="newmenu">
</form>
<?php } ?>
