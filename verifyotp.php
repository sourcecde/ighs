<?php
include "functions.php" ;
include "config.php" ;

@session_start() ;

//New PDO DB connection
try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}

$URL="./index.php" ;

//Get and store POST variables from calling page
$otp=$_POST["otp"] ;
if(!isset($_SESSION[$guid]["login_otp"]) || $otp==""){
	header("Location: {$URL}");
}
else{
	$v_otp=$_SESSION[$guid]["login_otp"]["OTP"];
	if($otp==$v_otp){
		//OTP verification successful
		$data=array("personId"=>$_SESSION[$guid]["login_otp"]["personId"]); 
		$gibboni18nID=$_SESSION[$guid]["login_otp"]["gibboni18nID"];
		unset($_SESSION[$guid]["login_otp"]);
		$sql="SELECT * FROM gibbonperson WHERE gibbonpersonid=:personId" ;
		$result=$connection2->prepare($sql);
		$result->execute($data);
		$row=$result->fetch();
						$sql="UPDATE `login_user` SET `login_userid`='".$row["gibbonPersonID"]."' WHERE id=1" ;
						$result1=$connection2->prepare($sql);
						$result1->execute();
						
						
						$username=$row["username"];
						$_SESSION[$guid]["login_userid"]=$row["gibbonPersonID"];
						$_SESSION[$guid]["username"]=$username ;
						$_SESSION[$guid]["passwordStrong"]=$row["passwordStrong"] ;
						$_SESSION[$guid]["passwordStrongSalt"]=$row["passwordStrongSalt"] ;
						$_SESSION[$guid]["passwordForceReset"]=$row["passwordForceReset"] ;
						$_SESSION[$guid]["gibbonPersonID"]=$row["gibbonPersonID"] ;
						$_SESSION[$guid]["surname"]=$row["surname"] ;
						$_SESSION[$guid]["firstName"]=$row["firstName"] ;
						$_SESSION[$guid]["preferredName"]=$row["preferredName"] ;
						$_SESSION[$guid]["officialName"]=$row["officialName"] ;
						$_SESSION[$guid]["email"]=$row["email"] ;
						$_SESSION[$guid]["emailAlternate"]=$row["emailAlternate"] ;
						$_SESSION[$guid]["website"]=$row["website"] ;
						$_SESSION[$guid]["gender"]=$row["gender"] ;
						$_SESSION[$guid]["status"]=$row["status"] ;
						$_SESSION[$guid]["gibbonRoleIDPrimary"]=$row["gibbonRoleIDPrimary"] ;
						$_SESSION[$guid]["gibbonRoleIDCurrent"]=$row["gibbonRoleIDPrimary"] ;
						$_SESSION[$guid]["gibbonRoleIDAll"]=getRoleList($row["gibbonRoleIDAll"], $connection2) ;
						$_SESSION[$guid]["image_240"]=$row["image_240"] ;
						$_SESSION[$guid]["image_75"]=$row["image_75"] ;
						$_SESSION[$guid]["lastTimestamp"]=$row["lastTimestamp"] ;
						$_SESSION[$guid]["calendarFeedPersonal"]=$row["calendarFeedPersonal"] ;
						$_SESSION[$guid]["viewCalendarSchool"]=$row["viewCalendarSchool"] ;
						$_SESSION[$guid]["viewCalendarPersonal"]=$row["viewCalendarPersonal"] ;
						$_SESSION[$guid]["viewCalendarSpaceBooking"]=$row["viewCalendarSpaceBooking"] ;
						$_SESSION[$guid]["dateStart"]=$row["dateStart"] ;
						$_SESSION[$guid]["personalBackground"]=$row["personalBackground"] ;
						$_SESSION[$guid]["messengerLastBubble"]=$row["messengerLastBubble"] ;
						$_SESSION[$guid]["gibbonThemeIDPersonal"]=$row["gibbonThemeIDPersonal"] ;
						$_SESSION[$guid]["gibboni18nIDPersonal"]=$row["gibboni18nIDPersonal"] ;
						$_SESSION[$guid]["googleAPIRefreshToken"]=$row["googleAPIRefreshToken"] ;
						$_SESSION[$guid]['googleAPIAccessToken']=NULL ; //Set only when user logs in with Google
						$_SESSION[$guid]['receiveNoticiationEmails']=$row["receiveNoticiationEmails"] ;
						
						
						//Allow for non-system default language to be specified from login form
						if (@$gibboni18nID!=$_SESSION[$guid]["i18n"]["gibboni18nID"]) {
							try {
								$dataLanguage=array("gibboni18nID"=>$gibboni18nID); 
								$sqlLanguage="SELECT * FROM gibboni18n WHERE gibboni18nID=:gibboni18nID" ; 
								$resultLanguage=$connection2->prepare($sqlLanguage);
								$resultLanguage->execute($dataLanguage);
							}
							catch(PDOException $e) { }
							if ($resultLanguage->rowCount()==1) {
								$rowLanguage=$resultLanguage->fetch() ;
								setLanguageSession($guid, $rowLanguage) ;
							}
						}
						else {
							//If no language specified, get user preference if it exists
							if (!is_null($_SESSION[$guid]["gibboni18nIDPersonal"])) {
								try {
									$dataLanguage=array("gibboni18nID"=>$_SESSION[$guid]["gibboni18nIDPersonal"]); 
									$sqlLanguage="SELECT * FROM gibboni18n WHERE active='Y' AND gibboni18nID=:gibboni18nID" ; 
									$resultLanguage=$connection2->prepare($sqlLanguage);
									$resultLanguage->execute($dataLanguage);
								}
								catch(PDOException $e) { }
								if ($resultLanguage->rowCount()==1) {
									$rowLanguage=$resultLanguage->fetch() ;
									setLanguageSession($guid, $rowLanguage) ;
								}
							}
						}
						
						//Make best effort to set IP address and other details, but no need to error check etc.
						try {
							$data=array( "lastIPAddress"=> $_SERVER["REMOTE_ADDR"], "lastTimestamp"=> date("Y-m-d H:i:s"), "failCount"=>0, "username"=> $username ); 
							$sql="UPDATE gibbonperson SET lastIPAddress=:lastIPAddress, lastTimestamp=:lastTimestamp, failCount=:failCount WHERE username=:username" ;
							$result=$connection2->prepare($sql);
							$result->execute($data); 
						}
						catch(PDOException $e) { }
		header("Location: {$URL}");	
	}
	else{
		//OTP verification fail
		$count=++$_SESSION[$guid]["login_otp"]["attempt"];
		if($count==3)
		{
			unset($_SESSION[$guid]["login_otp"]);
			$URL.="?loginReturn=fail22" ;
			header("Location: {$URL}");	
		}
		else{
			$_SESSION[$guid]["login_otp"]["attempt"]=$count;
			$URL.="?loginReturn=fail21" ;
			header("Location: {$URL}");	
		}
	}
}
 ?>