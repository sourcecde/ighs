<?php
include "../../config.php" ;
@session_start();
try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}
$sql="SELECT `gibbonPersonID`,`account_number` FROM `gibbonperson` WHERE `gibbonRoleIDPrimary`=3 AND `account_number`<=1631";
$result=$connection2->prepare($sql);
$result->execute();
$studentArr=$result->fetchAll();
foreach($studentArr as $s){
echo $sql="UPDATE `gibbonperson` SET `image_240`='uploads/2017/all_240/".abs($s['account_number']).".jpg' WHERE `gibbonPersonID`={$s['gibbonPersonID']}; <br>";
}
?>


<pre>
<?php //echo abs($studentArr[5]['account_number']); ?>
</pre>