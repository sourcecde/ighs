<?php
@session_start() ;
$gibbonPersonID=NULL ;
if (isset($_SESSION[$guid]["gibbonPersonID"])) {
	$gibbonPersonID=$_SESSION[$guid]["gibbonPersonID"] ;
}

?>
<form name="f1" id="f1" method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/spot_price_process.php" ?>">
<input type="hidden" name="event" id="event" value="add">
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
	<td>Spot Name</td>
	<td><input type="text" name="spot_name" id="spot_name"></td>
</tr>

<tr>
	<td>Price</td>
	<td><input type="text" name="price" id="price"></td>
</tr>
<tr>
	<td>Distance From school</td>
	<td><input type="text" name="distance" id="distance"></td>
</tr>
<tr>
	<td></td>
	<td><input type="submit" name="submit" id="submit" value="Submit"></td>
</tr>
</table>
</form>