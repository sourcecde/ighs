<?php 
include "../../config.php" ;
try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}
@session_start() ;

//Set timezone from session variable


$URL=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/menu/manage_menu.php" ;
if($_POST['top'] && $_POST['sub'])
{
try {
	$dataFile=array("parent_id"=>$_POST['top'], "id"=>$_POST['sub'],"menu_name"=>$_REQUEST['menu_name'],"order_sequence"=>$_POST["order_sequence"],"active_inactive"=>$_POST["active_inactive"]); 
	$sqlFile="Update menu SET parent_id=:parent_id,menu_name=:menu_name,order_sequence=:order_sequence,active_inactive=:active_inactive where id=:id" ;
	$resultFile=$connection2->prepare($sqlFile);
	$resultFile->execute($dataFile);
	}
	catch(PDOException $e) { }
	header("Location: {$URL}");
}

if($_POST['newmenu'])
{
	
	$activeinactive=0;
	if(isset($_POST['active_inactive']))
	{
		$activeinactive=0;
	}
	
try {
	$dataFile=array("menu_name"=>$_POST['new_menu'],"parent_id"=>0,"menu_type"=>'top',"url"=>'#', "order_sequence"=>$_POST['new_menu_position'],"active_inactive"=>$activeinactive); 
	$sqlFile="Insert into menu SET menu_name=:menu_name,parent_id=:parent_id,menu_type=:menu_type,url=:url,order_sequence=:order_sequence,active_inactive=:active_inactive" ;
	$resultFile=$connection2->prepare($sqlFile);
	$resultFile->execute($dataFile);
	}
	catch(PDOException $e) { }
	header("Location: {$URL}");
}

if($_POST['editmenu'])
{
	
	try {
	$dataFile=array("menu_name"=>$_POST['new_menu'], "order_sequence"=>$_POST['new_menu_position'],"id"=>$_POST['menu_id'],"active_inactive"=>$_POST['active_inactive']); 
	$sqlFile="UPDATE menu SET menu_name=:menu_name,order_sequence=:order_sequence,active_inactive=:active_inactive where id=:id" ;
	$resultFile=$connection2->prepare($sqlFile);
	$resultFile->execute($dataFile);
	}
	catch(PDOException $e) { }
	header("Location: {$URL}");
}
?>
