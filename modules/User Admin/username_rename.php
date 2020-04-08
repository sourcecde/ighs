<?php
$sql="SELECT `gibbonPersonID`,`firstName`,`account_number` FROM `gibbonperson` WHERE `gibbonRoleIDPrimary`=003 AND account_number!=0";
$result=$connection2->prepare($sql);
$result->execute();
$students=$result->fetchAll();
//print_r($students);
foreach($students as $s){
	$salt=getSalt() ;
	$passwordStrong=hash("sha256", $salt."Cps@1234") ;
	$username=strtolower(current(explode(' ',$s["firstName"]))).sprintf("%04d",$s["account_number"]);
	echo $sql="UPDATE `gibbonperson` SET `username`='$username',`passwordStrong`='$passwordStrong',`passwordStrongSalt`='$salt' WHERE `gibbonpersonID`=".$s["gibbonPersonID"];
	$result=$connection2->prepare($sql);
	$result->execute();
	echo "<br><br>";
}
?>