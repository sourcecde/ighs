<?php
include "../../config.php" ;
include "fee_functions.php";
try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}
@session_start() ;


if($_POST['job']=='create_rule')
{
	$URL=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Fee/rule_view.php" ;
try {
	
	$feename=CreateFeeName($_POST["fee_type_master_id"],$_POST["fee_boarder_class_id"]);
	$dataFile=array("rule_name"=>$feename, "rule_description"=>$_POST['rule_description'],"fee_type_master_id"=>$_POST["fee_type_master_id"],"fee_boarder_class_id"=>$_POST["fee_boarder_class_id"],"amount"=>$_POST["amount"],"year"=>$_POST['filter_year'],"effected_date_start"=>chageDMYtoYMD($_POST["effected_date_start"]),"onetime"=>$_POST['onetime'],"effected_date_end"=>chageDMYtoYMD($_POST["effected_date_end"])); 
	$sqlFile="Insert into  fee_rule_master SET rule_name=:rule_name,rule_description=:rule_description,fee_type_master_id=:fee_type_master_id,fee_boarder_class_id=:fee_boarder_class_id,amount=:amount,gibbonSchoolYearID=:year,effected_date_start=:effected_date_start,effected_date_end=:effected_date_end,onetime=:onetime" ;
	$resultFile=$connection2->prepare($sqlFile);
	$resultFile->execute($dataFile);
	}
	catch(PDOException $e) { }
	header("Location: {$URL}");
}

if($_POST['job']=='edit_rule')
{
	
	$fee_rule_master_id=$_POST['fee_rule_master_id'];
	$URL=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Fee/rule_view.php" ;
try {
	$feename=CreateFeeName($_POST["fee_type_master_id"],$_POST["fee_boarder_class_id"]);
	$dataFile=array("rule_name"=>$feename,"rule_description"=>$_POST['rule_description'],"fee_type_master_id"=>$_POST["fee_type_master_id"],"fee_boarder_class_id"=>$_POST["fee_boarder_class_id"],"amount"=>$_POST["amount"],"year"=>$_POST['filter_year'],"effected_date_start"=>chageDMYtoYMD($_POST["effected_date_start"]),"effected_date_end"=>chageDMYtoYMD($_POST["effected_date_end"]),"fee_rule_master_id"=>$fee_rule_master_id,"onetime"=>$_POST['onetime']); 
	$sqlFile="UPDATE  fee_rule_master SET rule_name=:rule_name,rule_description=:rule_description,fee_type_master_id=:fee_type_master_id,fee_boarder_class_id=:fee_boarder_class_id,amount=:amount,gibbonSchoolYearID=:year,effected_date_start=:effected_date_start,effected_date_end=:effected_date_end,onetime=:onetime where fee_rule_master_id=:fee_rule_master_id" ;
	$resultFile=$connection2->prepare($sqlFile);
	$resultFile->execute($dataFile);
	}
	catch(PDOException $e) { }
	header("Location: {$URL}");
}

function chageDMYtoYMD($date)
{
	$datearr=explode("/", $date);
	$defdate=$datearr[2]."-".$datearr[1]."-".$datearr[0];
	return $defdate;
}