<?php
@session_start() ;
$gibbonPersonID=$_REQUEST['gibbonPersonID'];
$sql1="SELECT * FROM transport_month_entry WHERE gibbonPersonID=".$gibbonPersonID;
$result1=$connection2->prepare($sql1);
$result1->execute();
if ($result1->rowcount()==0) {
try {
	$sql="UPDATE gibbonperson SET avail_transport='N',active_transport='N', transport_spot_price_id=NULL,vehicle_id=NULL WHERE gibbonPersonID=".$gibbonPersonID;
	$result=$connection2->prepare($sql);
	$result->execute();
	}
	catch(PDOException $pe) { echo $pe;}
	
	try {
	$sql1="DELETE FROM `transport_pickup_drop` WHERE gibbonPersonID=".$gibbonPersonID;
	$result1=$connection2->prepare($sql1);
	$result1->execute();
	}
	catch(PDOException $pe) { echo $pe;}
	print "<h3>Deleted Sucessfully!!</h3>";
	}
else
{
	print "<h3>Sorry you can't delete this entry!!<br><br> Transaction record exist for this user</h3>";	
}
print "<center><a href='" . $_SESSION[$guid]['absoluteURL'] . "/index.php?q=/modules/" . $_SESSION[$guid]['module'] . "/student_list.php'>Go Back</a></center>"

?>