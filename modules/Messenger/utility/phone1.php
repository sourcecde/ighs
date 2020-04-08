<?php
$sql="SELECT `gibbonPersonID`,`phone1` FROM `gibbonperson` WHERE `gibbonRoleIDPrimary`='003'";
$result=$connection2->prepare($sql);
$result->execute();
$students=$result->fetchAll();
foreach($students as $s){
if($s['phone1']==''){
echo $sql="UPDATE `gibbonperson` SET `phone1`=(SELECT `phone1` FROM `gibbonfamilyadult` WHERE `gibbonPersonID`={$s['gibbonPersonID']} AND `contactPriority`=2) WHERE `gibbonPersonID`='{$s['gibbonPersonID']}' AND `phone1` LIKE ''";
}
$result=$connection2->prepare($sql);
$result->execute();
echo "<br><br>";
}
?>