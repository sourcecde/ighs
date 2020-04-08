<?php
$sql="SELECT `username`,`phone1` FROM `gibbonperson` WHERE `canLogin`='Y' AND `status`='Full' AND `gibbonRoleIDPrimary`=003 AND `phone1`!=''";
$result=$connection2->prepare($sql);
$result->execute();
$students=$result->fetchAll();
echo "<pre>";
print_r($students);
echo "</pre>";
?>