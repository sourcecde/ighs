<?php
@session_start() ;
$gibbonPersonID=NULL ;
if (isset($_SESSION[$guid]["gibbonPersonID"])) {
	$gibbonPersonID=$_SESSION[$guid]["gibbonPersonID"] ;
}

$id=$_REQUEST['transport_spot_price_id'];

$dboutput='';
try {
		$dataFile=array("transport_spot_price_id"=>$id); 
		$sqlFile="SELECT * from  transport_spot_price where transport_spot_price_id=:transport_spot_price_id" ;
		$resultFile=$connection2->prepare($sqlFile);
		$resultFile->execute($dataFile);
		$dboutbut=$resultFile->fetch();
		}
		catch(PDOException $e) { }
		
		
?>
<form name="f1" id="f1" method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/spot_price_process.php" ?>">
<input type="hidden" name="event" id="event" value="edit">
<input type="hidden" name="id" id="id" value="<?php echo $dboutbut['transport_spot_price_id'];?>">
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
	<td>Location</td>
	<td><input type="text" name="spot_name" id="spot_name" value="<?php echo $dboutbut['spot_name'];?>"></td>
</tr>
<tr>
	<td>Distance From school</td>
	<td><input type="text" name="distance" id="distance" value="<?php echo $dboutbut['distance'];?>"></td>
</tr>
<tr>
	<td>Price</td>
	<td><input type="text" name="price" id="price" value="<?php echo $dboutbut['price'];?>"></td>
</tr>
<tr>
	<td></td>
	<td><input type="submit" name="submit" id="submit" value="Submit"></td>
</tr>
</table>
</form>