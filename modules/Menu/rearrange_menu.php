<?php 
@session_start() ;
$gibbonPersonID=NULL ;
if (isset($_SESSION[$guid]["gibbonPersonID"])) {
	$gibbonPersonID=$_SESSION[$guid]["gibbonPersonID"] ;
}
$menu=getRawMenu($connection2,$guid) ;
$sql="SELECT * from menu ORDER BY order_sequence";
$result=$connection2->prepare($sql);
$result->execute();
$dboutbut=$result->fetchAll();

$sql="SELECT * from menu where menu_name='".$_REQUEST['menu']."'";
$result=$connection2->prepare($sql);
$result->execute();
$currentmenu=$result->fetch();

$current_top=$currentmenu["parent_id"];

$topmenuarray=array();
foreach ($dboutbut as $value) {
	if($value["menu_type"]=='top')
	{
		$topmenuarray[$value["id"]]=$value["menu_name"];
	}
}
if($_POST)
{
	echo $_REQUEST['top'];
	echo $_REQUEST['sub'];
}
?>
<form name="f1" id="f1" method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/menu_process.php" ?>">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td><?php print_r($menu);?></td>
    <td valign="top">
    <div style="width: 450px;">
			<h3><?php echo $_REQUEST['menu'];?></h3>
		</div>
    <table width="100%" cellpadding="0" cellspacing="0" >
    <tr>
    		<td style="font-weight: bold;">Top menu</td>
    		<td>
			<select name="top" id="top">
		<?php foreach ($topmenuarray as $key=>$value) { ?>
			<option value="<?php echo $key;?>" <?php if($key==$current_top){?> selected="selected"<?php } ?>><?php echo $value;?></option>
		<?php } ?>
		</select>
			</td>
    	</tr>
    	<tr>
    		<td style="font-weight: bold;">Name</td>
    		<td><input type="text" name="menu_name" id="menu_name" value="<?php echo $currentmenu['menu_name'];?>"></td>
    	</tr>
    	<tr>
    		<td style="font-weight: bold;">Position</td>
    		<td><input type="text" name="order_sequence" id="order_sequence" value="<?php echo $currentmenu['order_sequence'];?>"></td>
    	</tr>
    	<tr>
    		<td style="font-weight: bold;">Activitation</td>
    		<td>
    		<input type="checkbox" name="active_inactive" id="active_inactive" value="1" <?php if($currentmenu['active_inactive']==1){?> checked="checked"<?php } ?> style="float: right;">
    		</td>
    	</tr>
    	<tr>
    		<td></td>
    		<td>
    		<div style="float: right">
		<input type="submit" name="submit" value="submit" value="Save">
		</div>
    		</td>
    	</tr>
    </table>
		<input type="hidden" name="sub" id="sub" value="<?php echo $currentmenu["id"];?>">
		
	</td>
  </tr>
</table>
</form>

