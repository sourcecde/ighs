<?php 
include "../../config.php" ;
	try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	}
	catch(PDOException $e) {

	}
	
if(isset($_REQUEST['year_id_select_section']))
{
	$year_id=$_REQUEST['year_id_select_section'];
	$sql="SELECT `gibbonrollgroup`.`gibbonRollGroupID`,`gibbonrollgroup`.`name` as `section`,`gibbonyeargroup`.`name` as `class` FROM `gibbonrollgroup`,`gibbonyeargroup` WHERE `gibbonrollgroup`.`gibbonYearGroupID`=`gibbonyeargroup`.`gibbonYearGroupID` AND `gibbonSchoolYearID`=".$year_id." ORDER BY `gibbonrollgroup`.`gibbonYearGroupID`,`gibbonrollgroup`.`name`";
	$result=$connection2->prepare($sql);
	$result->execute();
	$return_message="";
	while($section=$result->fetch())
	{
		$return_message.="<option value='".$section['gibbonRollGroupID']."'>".$section['class']." ".$section['section']."</option>";
	}
	header('Content-Type: application/json');
	echo json_encode($return_message);
}
if(isset($_REQUEST['action'])){
if($_REQUEST['action']=='get_boarder_type'){
	$id=$_REQUEST['id'];
	$sql="SELECT boarder from fee_type_master WHERE fee_type_master_id=".$id;
	$result=$connection2->prepare($sql);
	$result->execute();
	$boarder=$result->fetch();
	
	$sql="SELECT * from fee_boarder_class where border='N' order by class";
	$result=$connection2->prepare($sql);
	$result->execute();
	$fee_boarder_class=$result->fetchAll();
	$msg='';
	foreach ($fee_boarder_class as $value) { 
    $msg.="<option value='".$value['fee_boarder_class_id']."'>Class -".$value['class']."</option>";
	  }
	 header('Content-Type: application/json');
	echo json_encode($msg);
} 
}
?>