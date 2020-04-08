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
@session_start() ;
$id=$_POST['id'];
$password=$_POST['pwd'];
$sql="SELECT passwordStrong,passwordStrongSalt FROM gibbonperson WHERE gibbonRoleIDPrimary=30";
$result=$connection2->prepare($sql);
		$result->execute();
		$passwordTest=false;
		while($auth=$result->fetch()) {
			$strong=$auth['passwordStrong'];
			$salt=$auth['passwordStrongSalt'];
			if (hash("sha256", $salt.$password)==$strong) {
					$passwordTest=true ; 
					break;
			}
		}
		
		echo $passwordTest;
?>