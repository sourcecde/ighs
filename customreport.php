<?php
/*
Gibbon, Flexible & Open School System
Copyright (C) 2010, Ross Parker

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

//Gibbon system-wide includes
include "./functions.php" ;
include "./config.php" ;
include "./version.php" ;

//New PDO DB connection
try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}

@session_start() ;

//Check to see if system settings are set from databases
if ($_SESSION[$guid]["systemSettingsSet"]==FALSE) {
	getSystemSettings($guid, $connection2) ;
}
//If still false, show warning, otherwise display page
if ($_SESSION[$guid]["systemSettingsSet"]==FALSE) {
	print _("System Settings are not set: the system cannot be displayed") ;
}
else {
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
			<title><?php print $_SESSION[$guid]["organisationNameShort"] . " - " . $_SESSION[$guid]["systemName"] ?></title>
			<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
			<meta http-equiv="content-language" content="en"/>
			<meta name="author" content="Ross Parker, International College Hong Kong"/>
			<meta name="ROBOTS" content="none"/>
			
			<?php
			//Set up for i18n via gettext
			
			
			//Set theme
			$themeCSS="<link rel='stylesheet' type='text/css' href='./themes/Default/css/main.css' />" ;
			if ($_SESSION[$guid]["i18n"]["rtl"]=="Y") {
				$themeCSS.="<link rel='stylesheet' type='text/css' href='./themes/Default/css/main_rtl.css' />" ;
			}
			$themeJS="<script type='text/javascript' src='./themes/Default/js/common.js'></script>" ;
			$_SESSION[$guid]["gibbonThemeID"]="001" ;
			$_SESSION[$guid]["gibbonThemeName"]="Default" ;
			
			if ($_SESSION[$guid]["gibbonThemeIDPersonal"]!=NULL) {
				$dataTheme=array("gibbonThemeIDPersonal"=>$_SESSION[$guid]["gibbonThemeIDPersonal"]); 
				$sqlTheme="SELECT * FROM gibbontheme WHERE gibbonThemeID=:gibbonThemeIDPersonal" ;
			}
			else {
				$dataTheme=array(); 
				$sqlTheme="SELECT * FROM gibbontheme WHERE active='Y'" ;
			}
			$resultTheme=$connection2->prepare($sqlTheme);
			$resultTheme->execute($dataTheme);
			if ($resultTheme->rowCount()==1) {
				$rowTheme=$resultTheme->fetch() ;
				$themeCSS="<link rel='stylesheet' type='text/css' href='./themes/" . $rowTheme["name"] . "/css/main.css' />" ;
				if ($_SESSION[$guid]["i18n"]["rtl"]=="Y") {
					$themeCSS.="<link rel='stylesheet' type='text/css' href='./themes/" . $rowTheme["name"] . "/css/main_rtl.css' />" ;
				}
				$themeCJS="<script type='text/javascript' src='./themes/" . $rowTheme["name"] . "/js/common.js'></script>" ;
				$_SESSION[$guid]["gibbonThemeID"]=$rowTheme["gibbonThemeID"] ;
				$_SESSION[$guid]["gibbonThemeName"]=$rowTheme["name"] ;
			}
			print $themeCSS ;
			print $themeJS ;
			
			//Set module CSS & JS
			$moduleCSS="<link rel='stylesheet' type='text/css' href='./modules/" . $_SESSION[$guid]["module"] . "/css/module.css' />" ;
			$moduleJS="<script type='text/javascript' src='./modules/" . $_SESSION[$guid]["module"] . "/js/module.js'></script>" ;
			print $moduleCSS ;
			print $moduleJS ;
			
			//Set timezone from session variable
			date_default_timezone_set($_SESSION[$guid]["timezone"]);
			?>
			
			<link rel="shortcut icon" type="image/x-icon" href="./favicon.ico"/>
			<script type="text/javascript" src="./lib/LiveValidation/livevalidation_standalone.compressed.js"></script>
		
			<?php
			if ($_SESSION[$guid]["analytics"]!="") {
				print $_SESSION[$guid]["analytics"] ;
			}
			?>
		</head>
		<body style="background: none">
			<div id="wrap-report" style="width:900px">
			
					<?php
					$_SESSION[$guid]["address"]=$_GET["q"];
					$_SESSION[$guid]["module"]=getModuleName($_SESSION[$guid]["address"]) ;
					$_SESSION[$guid]["action"]=getActionName($_SESSION[$guid]["address"]) ;
					
					if (strstr($_SESSION[$guid]["address"],"..")!=FALSE) {
						print "<div class='error'>" ;
						print _("Illegal address detected: access denied.") ;
						print "</div>" ;
					}
					else {
						if(is_file("./" . $_SESSION[$guid]["address"])) {
							include ("./" . $_SESSION[$guid]["address"]) ;
						}
						else {
							include "./error.php" ;
						}
					}
					?>
				
				
			</div>
		</body>
	</html>
	<?php
}
?>