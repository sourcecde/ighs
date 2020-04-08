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
if (file_exists("./config.php")) {
	include "./config.php" ;
}
else { //no config, so go to installer
	$URL="./installer/install.php" ;
	header("Location: {$URL}");
}
include "./functions.php" ;
include "./version.php" ;
include "./custom/custom_menu_functions.php" ;
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

//Deal with caching
if (isset($_SESSION[$guid]["pageLoads"])) {
	$_SESSION[$guid]["pageLoads"]++ ;
}
else {
	$_SESSION[$guid]["pageLoads"]=0 ;
}
$cacheLoad=FALSE ;
if ($caching>0 AND is_numeric($caching)) {
	if ($_SESSION[$guid]["pageLoads"]%$caching==0) {
		$cacheLoad=TRUE ;
	}
}

//Check for cutting edge code
if (isset($_SESSION[$guid]["cuttingEdgeCode"])==FALSE) {
	$_SESSION[$guid]["cuttingEdgeCode"]=getSettingByScope($connection2, "System", "cuttingEdgeCode") ;
}

//Set sidebar values (from the entrySidebar field in gibbonaction and from $_GET variable)
$_SESSION[$guid]["sidebarExtra"]="" ;
$_SESSION[$guid]["sidebarExtraPosition"]="" ;
if (isset($_GET["sidebar"])) {
	$sidebar=$_GET["sidebar"] ;
}
else {
	$sidebar="" ;
}


//Deal with address param q
if (isset($_GET["q"])) {
	$_SESSION[$guid]["address"]=$_GET["q"] ;
}
else {
	$_SESSION[$guid]["address"]="" ;
}
$_SESSION[$guid]["module"]=getModuleName($_SESSION[$guid]["address"]) ;
$_SESSION[$guid]["action"]=getActionName($_SESSION[$guid]["address"]) ;
$q=NULL ;
if (isset($_GET["q"])) {
	$q=$_GET["q"] ;
}


//Check to see if system settings are set from databases
if (@$_SESSION[$guid]["systemSettingsSet"]==FALSE) {
	getSystemSettings($guid, $connection2) ;
}

//Set up for i18n via gettext
if (isset($_SESSION[$guid]["i18n"]["code"])) {
	if ($_SESSION[$guid]["i18n"]["code"]!=NULL) {
		putenv("LC_ALL=" . $_SESSION[$guid]["i18n"]["code"]);
		setlocale(LC_ALL, $_SESSION[$guid]["i18n"]["code"]);
		bindtextdomain("gibbon", "./i18n");
		textdomain("gibbon");
		bind_textdomain_codeset("gibbon", 'UTF-8');
	}
}

//Try to autoset user's calendar feed if not set already
if (isset($_SESSION[$guid]["calendarFeedPersonal"]) AND isset($_SESSION[$guid]['googleAPIAccessToken'])) {
	if ($_SESSION[$guid]["calendarFeedPersonal"]=="" AND $_SESSION[$guid]['googleAPIAccessToken']!=NULL) {
		require_once $_SESSION[$guid]["absolutePath"] . '/lib/google/google-api-php-client/autoload.php';
		$client2=new Google_Client();
		$client2->setAccessToken($_SESSION[$guid]['googleAPIAccessToken']);
		$service=new Google_Service_Calendar($client2);
		$calendar=$service->calendars->get('primary');
	
		if ($calendar["id"]!="") {
			try {
				$dataCalendar=array("calendarFeedPersonal"=>$calendar["id"], "gibbonPersonID"=>$_SESSION[$guid]['gibbonPersonID']); 
				$sqlCalendar="UPDATE gibbonperson SET calendarFeedPersonal=:calendarFeedPersonal WHERE gibbonPersonID=:gibbonPersonID";
				$resultCalendar=$connection2->prepare($sqlCalendar);
				$resultCalendar->execute($dataCalendar); 
			}
			catch(PDOException $e) { }
			$_SESSION[$guid]["calendarFeedPersonal"]=$calendar["id"] ;
		}
	}
}

//Check for force password reset flag
if (isset($_SESSION[$guid]["passwordForceReset"])) {
	if ($_SESSION[$guid]["passwordForceReset"]=="Y" AND $q!="preferences.php") {
		$URL=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=preferences.php" ;
		$URL=$URL. "&forceReset=Y" ;
		header("Location: {$URL}") ;
		break ;
	}
}

 if ($_SESSION[$guid]["address"]!="" AND $sidebar!=true) {
	try {
		$dataSidebar=array("action"=>"%" . $_SESSION[$guid]["action"] . "%", "name"=>$_SESSION[$guid]["module"]); 
		$sqlSidebar="SELECT gibbonaction.name FROM gibbonaction JOIN gibbonmodule ON (gibbonaction.gibbonModuleID=gibbonmodule.gibbonModuleID) WHERE gibbonaction.URLList LIKE :action AND entrySidebar='N' AND gibbonmodule.name=:name" ;
		$resultSidebar=$connection2->prepare($sqlSidebar);
		$resultSidebar->execute($dataSidebar); 
	}
	catch(PDOException $e) { }
	if ($resultSidebar->rowCount()>0) {
		$sidebar="false" ;
	}
}
//If still false, show warning, otherwise display page
if ($_SESSION[$guid]["systemSettingsSet"]==FALSE) {
	print _("System Settings are not set: the system cannot be displayed") ;
}
else {
	?>
	<!doctype html>
	<html>
		<head>
			<title>
				<?php 
				print $_SESSION[$guid]["organisationNameShort"] . " - " . $_SESSION[$guid]["systemName"] ;
				if ($_SESSION[$guid]["address"]!="") {
					if (strstr($_SESSION[$guid]["address"],"..")==FALSE) {
						if (getModuleName($_SESSION[$guid]["address"])!="") {
							print " - " . getModuleName($_SESSION[$guid]["address"]) ;
						}
					}
				}
				?>
			</title>
			<meta charset="utf-8">
            <meta name="viewport" content="width=device-width">
			<meta name="author" content="Ross Parker, International College Hong Kong">
			<meta name="ROBOTS" content="none">
			
			<link rel="shortcut icon" type="image/x-icon" href="./favicon.ico">
			  <script type="text/javascript" src="./lib/LiveValidation/livevalidation_standalone.compressed.js"></script>

			<script type="text/javascript" src="<?php print $_SESSION[$guid]["absoluteURL"] ?>/lib/jquery/jquery.js"></script>
			<script type="text/javascript" src="<?php print $_SESSION[$guid]["absoluteURL"] ?>/lib/jquery-ui/js/jquery-ui.min.js"></script>
			<?php 
			if (isset($_SESSION[$guid]["i18n"]["code"])) {
				if (is_file($_SESSION[$guid]["absolutePath"] . "/lib/jquery-ui/i18n/jquery.ui.datepicker-" . substr($_SESSION[$guid]["i18n"]["code"],0,2) . ".js")) {
					print "<script type='text/javascript' src='" . $_SESSION[$guid]["absoluteURL"] . "/lib/jquery-ui/i18n/jquery.ui.datepicker-" .  substr($_SESSION[$guid]["i18n"]["code"],0,2) . ".js'></script>" ;
					print "<script type='text/javascript'>$.datepicker.setDefaults($.datepicker.regional['" .  substr($_SESSION[$guid]["i18n"]["code"],0,2) . "']);</script>" ;
				}
				else if (is_file($_SESSION[$guid]["absolutePath"] . "/lib/jquery-ui/i18n/jquery.ui.datepicker-" . str_replace("_","-",$_SESSION[$guid]["i18n"]["code"]) . ".js")) {
					print "<script type='text/javascript' src='" . $_SESSION[$guid]["absoluteURL"] . "/lib/jquery-ui/i18n/jquery.ui.datepicker-" .  str_replace("_","-",$_SESSION[$guid]["i18n"]["code"]) . ".js'></script>" ;
					print "<script type='text/javascript'>$.datepicker.setDefaults($.datepicker.regional['" .  str_replace("_","-",$_SESSION[$guid]["i18n"]["code"]) . "']);</script>" ;
				}
			}
			
			?>
			<script type="text/javascript">$(function() { $( document ).tooltip({  show: 800, hide: false, content: function () { return $(this).prop('title')}, position: { my: "center bottom-20", at: "center top", using: function( position, feedback ) { $( this ).css( position ); $( "<div>" ).addClass( "arrow" ).addClass( feedback.vertical ).addClass( feedback.horizontal ).appendTo( this ); } } }); });</script>
			<script type="text/javascript" src="<?php print $_SESSION[$guid]["absoluteURL"] ?>/lib/jquery-jslatex/jquery.jslatex.js"></script>
			<script type="text/javascript">$(function () { $(".latex").latex();});</script>
			<script type="text/javascript" src="<?php print $_SESSION[$guid]["absoluteURL"] ?>/lib/jquery-form/jquery.form.js"></script>
			<link rel="stylesheet" href="<?php print $_SESSION[$guid]["absoluteURL"] ?>/lib/jquery-ui/css/blitzer/jquery-ui.css" type="text/css" media="screen">
			<script type="text/javascript" src="<?php print $_SESSION[$guid]["absoluteURL"] ?>/lib/chained/jquery.chained.mini.js"></script>
			<script type="text/javascript" src="<?php print $_SESSION[$guid]["absoluteURL"] ?>/lib/thickbox/thickbox-compressed.js"></script>
			<script type="text/javascript"> var tb_pathToImage="<?php print $_SESSION[$guid]["absoluteURL"] ?>/lib/thickbox/loadingAnimation.gif"</script>
			<link rel="stylesheet" href="<?php print $_SESSION[$guid]["absoluteURL"] ?>/lib/thickbox/thickbox.css" type="text/css" media="screen" />
			<script type="text/javascript" src="<?php print $_SESSION[$guid]["absoluteURL"] ?>/lib/jquery-autosize/jquery.autosize.min.js"></script>
			<script type="text/javascript" src="<?php print $_SESSION[$guid]["absoluteURL"] ?>/lib/jquery-sessionTimeout/jquery.sessionTimeout.min.js"></script>
			<?php
			if (isset($_SESSION[$guid]["username"])) {
				$sessionDuration=getSettingByScope($connection2, "System", "sessionDuration") ;
				if (is_numeric($sessionDuration)==FALSE) {
					$sessionDuration=1200 ;
				}
				if ($sessionDuration<1200) {
					$sessionDuration=1200 ;
				}
				?>
				<script type="text/javascript">
					$(document).ready(function(){
						$.sessionTimeout({
							message: '<?php print _("Your session is about to expire: you will be logged out shortly.") ?>',
							keepAliveUrl: 'keepAlive.php' ,
							redirUrl: 'logout.php?timeout=true', 
							logoutUrl: 'logout.php' , 
							warnAfter: <?php print ($sessionDuration*1000) ?>,
							redirAfter: <?php print ($sessionDuration*1000)+600000 ?>
			 			});
					});
				</script>
			<?php
			}
			//Set theme
			if ($cacheLoad OR $_SESSION[$guid]["themeCSS"]=="" OR isset($_SESSION[$guid]["themeJS"])==FALSE OR $_SESSION[$guid]["gibbonThemeID"]=="" OR $_SESSION[$guid]["gibbonThemeName"]=="") {
				$_SESSION[$guid]["themeCSS"]="<link rel='stylesheet' type='text/css' href='./themes/Default/css/main.css'>" ;
				if ($_SESSION[$guid]["i18n"]["rtl"]=="Y") {
					$_SESSION[$guid]["themeCSS"].="<link rel='stylesheet' type='text/css' href='./themes/Default/css/main_rtl.css'>" ;
				}
				$_SESSION[$guid]["themeJS"]="<script type='text/javascript' src='./themes/Default/js/common.js'></script>" ;
				$_SESSION[$guid]["gibbonThemeID"]="001" ;
				$_SESSION[$guid]["gibbonThemeName"]="Default" ;
				$_SESSION[$guid]["gibbonThemeAuthor"]="" ;
				$_SESSION[$guid]["gibbonThemeURL"]="" ;
				try {
					if (isset($_SESSION[$guid]["gibbonThemeIDPersonal"])) {
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
						$_SESSION[$guid]["themeCSS"]="<link rel='stylesheet' type='text/css' href='./themes/" . $rowTheme["name"] . "/css/main.css'>" ;
						if ($_SESSION[$guid]["i18n"]["rtl"]=="Y") {
							$_SESSION[$guid]["themeCSS"].="<link rel='stylesheet' type='text/css' href='./themes/" . $rowTheme["name"] . "/css/main_rtl.css'>" ;
						}
						$_SESSION[$guid]["themeJS"]="<script type='text/javascript' src='./themes/" . $rowTheme["name"] . "/js/common.js'></script>" ;
						$_SESSION[$guid]["gibbonThemeID"]=$rowTheme["gibbonThemeID"] ;
						$_SESSION[$guid]["gibbonThemeName"]=$rowTheme["name"] ;
						$_SESSION[$guid]["gibbonThemeAuthor"]=$rowTheme["author"] ;
						$_SESSION[$guid]["gibbonThemeURL"]=$rowTheme["url"] ;
					}
				}
				catch(PDOException $e) {
					print "<div class='error'>" ;
						print $e->getMessage();
					print "</div>" ;
				}
			}
			
			print $_SESSION[$guid]["themeCSS"] ;
			print $_SESSION[$guid]["themeJS"] ;
			
			//Set module CSS & JS
			if (isset($_GET["q"])) {
				$moduleCSS="<link rel='stylesheet' type='text/css' href='./modules/" . $_SESSION[$guid]["module"] . "/css/module.css' />" ;
				$moduleJS="<script type='text/javascript' src='./modules/" . $_SESSION[$guid]["module"] . "/js/module.js'></script>" ;
				print $moduleCSS ;
				print $moduleJS ;
			}
			
			//Set personalised background, if permitted
			if ($personalBackground=getSettingByScope($connection2, "User Admin", "personalBackground")=="Y" AND isset($_SESSION[$guid]["personalBackground"])) {
				if ($_SESSION[$guid]["personalBackground"]!="") {
					print "<style type=\"text/css\">" ;
						print "body {" ;
							print "background: url(\"" . $_SESSION[$guid]["personalBackground"] . "\") repeat scroll center top #A88EDB!important;" ;
						print "}" ;
					print "</style>" ;
				}
			}
	
			//Set timezone from session variable
			date_default_timezone_set($_SESSION[$guid]["timezone"]);
			
			//Initialise tinymce
			?>
			<script type="text/javascript" src="<?php print $_SESSION[$guid]["absoluteURL"] ?>/lib/tinymce/tinymce.min.js"></script>
			<script type="text/javascript">
			tinymce.init({
				selector: "div#editorcontainer textarea",
				width: '860px',
				menubar : false,
				toolbar: 'bold, italic, underline,forecolor,backcolor,|,alignleft, aligncenter, alignright, alignjustify, |, formatselect, fontselect, fontsizeselect, |, table, |, bullist, numlist,outdent, indent, |, link, unlink, image, media, hr, charmap, |, cut, copy, paste, undo, redo, fullscreen',
				plugins: 'table, template, paste, visualchars, image, link, template, textcolor, hr, charmap, fullscreen, media',
			 	statusbar: false,
			 	valid_elements: '<?php print getSettingByScope($connection2, "System", "allowableHTML") ?>',
			 	apply_source_formatting : true,
			 	browser_spellcheck: true,
			 	convert_urls: false,
			 	relative_urls: false
			 });
			</script>
			<style>
				div.mce-listbox button, div.mce-menubtn button { padding-top: 2px!important ; padding-bottom: 2px!important }
			</style>
			<script type="text/javascript" src="<?php print $_SESSION[$guid]["absoluteURL"] ?>/lib/jquery-tokeninput/src/jquery.tokeninput.js"></script>
			<link rel="stylesheet" href="<?php print $_SESSION[$guid]["absoluteURL"] ?>/lib/jquery-tokeninput/styles/token-input-facebook.css" type="text/css" />
			
			<?php
			//Analytics setting
			if ($_SESSION[$guid]["analytics"]!="") {
				print $_SESSION[$guid]["analytics"] ;
			}
			
			?>
            <script src="<?php print $_SESSION[$guid]["absoluteURL"] ?>/themes/Default/js/jquery.meanmenu.js"></script>
             <link href='<?php print $_SESSION[$guid]["absoluteURL"] ?>/themes/Default/css/meanmenu.css' rel='stylesheet' type='text/css'>
<script>
	jQuery(document).ready(function () {
	    jQuery('.menuinner').meanmenu();
	});
</script>

		</head>
		
		<body <?php
			//Show warning if not in the current school year

			if (isset($_SESSION[$guid]["username"])) {
					print "";	
			}
			else if(isset($_REQUEST['q'])=='/modules/Application Form/applicationForm.php')
			{
				print "class='application'";
			}
			else if(isset($_REQUEST['q'])=='passwordReset.php')
			{
				print "class='forgotpassord'";
			}
			else {print "class='homepage'";}
			?>>
		
			<?php
			//Show warning if not in the current school year
			if (isset($_SESSION[$guid]["username"])) {
				if ($_SESSION[$guid]["gibbonSchoolYearID"]!=$_SESSION[$guid]["gibbonSchoolYearIDCurrent"]) {
					print "<div style='margin: 10px auto; width:1101px;' class='warning'>" ;
						print "<b><u>" . sprintf(_('Warning: you are logged into the system in school year %1$s, which is not the current year.'), $_SESSION[$guid]["gibbonSchoolYearName"]) . "</b></u>" . _('Your data may not look quite right (for example, students who have left the school will not appear in previous years), but you should be able to edit information from other years which is not available in the current year.') ;
					print "</div>" ;
				}
			}
			?>
						
			<div id="wrapOuter">
				<?php
				print "<div class='minorLinks'><div class='minorlinkinner'>" ;
					print getMinorLinks($connection2, $guid, $cacheLoad) ;
				print "</div></div>" ;
				?>
				<div id="wrap">
					<div id="header">
                    <div class="headerinner">
						<div id="header-logo">
							<a href='<?php print $_SESSION[$guid]["absoluteURL"] ?>'><img height='115px' width='388px' class="logo" alt="Logo" src="<?php print $_SESSION[$guid]["absoluteURL"] . "/" . $_SESSION[$guid]["organisationLogo"] ; ?>"/></a>
						</div>
						<div id="header-finder">
							<?php
							//Show student and staff quick finder
							if (isset($_SESSION[$guid]["username"])) {
								if ($cacheLoad) {
									$_SESSION[$guid]["studentFastFinder"]=getStudentFastFinder($connection2, $guid) ;
								}
								print $_SESSION[$guid]["studentFastFinder"] ;
							}
							?>
						</div></div>
						
<?php 
                    if((int)$_SESSION[$guid]["gibbonRoleIDPrimary"]!=3){
?>
					<div id="header-menu">	<div class="menuinner">
							<?php 
								//Get main menu
								
								if ($cacheLoad) {
									//$_SESSION[$guid]["mainMenu"]=mainMenu($connection2, $guid) ;
									$_SESSION[$guid]["mainMenu"]=MymainMenu($connection2,$guid) ;
								}
								
								
								//
								print $_SESSION[$guid]["mainMenu"];
								
															?>
															<!--  
															<ul id="nav">
															<li><a href="http://localhost/gibbon/index.php">Home</a></li>
															<li><a href="#">Admin</a>
															<ul>
															<li><a href="http://localhost/gibbon/index.php?q=/modules/School Admin/schoolYear_manage.php">School Admin</a></li>
															<li><a href="http://localhost/gibbon/index.php?q=/modules/System Admin/systemSettings.php">System Admin</a></li>
															<li><a href="http://localhost/gibbon/index.php?q=/modules/Timetable Admin/tt.php">Timetable Admin</a></li>
															<li><a href="http://localhost/gibbon/index.php?q=/modules/User Admin/user_manage.php">User Admin</a></li>
															</ul>
															</li>
															<li><a href="#">Assess</a><ul><li><a href="http://localhost/gibbon/index.php?q=/modules/Crowd Assessment/crowdAssess.php">Crowd Assessment</a></li><li><a href="http://localhost/gibbon/index.php?q=/modules/External Assessment/externalAssessment.php">External Assessment</a></li><li><a href="http://localhost/gibbon/index.php?q=/modules/Markbook/markbook_view.php">Markbook</a></li><li><a href="http://localhost/gibbon/index.php?q=/modules/Rubrics/rubrics.php">Rubrics</a></li></ul></li><li><a href="#">Learn</a><ul><li><a href="http://localhost/gibbon/index.php?q=/modules/Activities/activities_view.php">Activities</a></li><li><a href="http://localhost/gibbon/index.php?q=/modules/Departments/departments.php">Departments</a></li><li><a href="http://localhost/gibbon/index.php?q=/modules/Individual Needs/in_view.php">Individual Needs</a></li><li><a href="http://localhost/gibbon/index.php?q=/modules/Library/library_manage_catalog.php">Library</a></li><li><a href="http://localhost/gibbon/index.php?q=/modules/Planner/planner.php">Planner</a></li><li><a href="http://localhost/gibbon/index.php?q=/modules/Resources/resources_view.php">Resources</a></li><li><a href="http://localhost/gibbon/index.php?q=/modules/Timetable/tt.php">Timetable</a></li></ul></li><li><a href="#">People</a><ul><li><a href="http://localhost/gibbon/index.php?q=/modules/Application Form/applicationForm.php">Application Form</a></li><li><a href="http://localhost/gibbon/index.php?q=/modules/Attendance/attendance_take_byRollGroup.php">Attendance</a></li><li><a href="http://localhost/gibbon/index.php?q=/modules/Behaviour/behaviour_manage.php">Behavior</a></li><li><a href="http://localhost/gibbon/index.php?q=/modules/Data Updater/data_personal.php">Data Updater</a></li><li><a href="http://localhost/gibbon/index.php?q=/modules/Roll Groups/rollGroups.php">Roll Groups</a></li><li><a href="http://localhost/gibbon/index.php?q=/modules/Staff/staff_view.php">Staff</a></li><li><a href="http://localhost/gibbon/index.php?q=/modules/Students/student_view.php">Students</a></li></ul></li><li><a href="#">Other</a><ul><li><a href="http://localhost/gibbon/index.php?q=/modules/Finance/invoices_manage.php">Finance</a></li><li><a href="http://localhost/gibbon/index.php?q=/modules/Messenger/messenger_manage.php">Messenger</a></li></ul></li></ul>
															-->
						</div></div>
						<?php } 
						else if($_SESSION[$guid]["address"]!=""){
						    print "<div style='padding: 15px;width: 100%;float: left;margin-bottom: 20px;'><a style='float: left;
    padding: 15px;
    margin-left: 90px;
    background: teal;' href='http://lakshya.calcuttapublicschool.in/lakshya_green_an/'><-Back To Home</a></div>";
						}
						?>
						
					</div>
					<div id="content-wrap">
						<?php
						//Allow for wide pages (no sidebar)
						if ($sidebar=="false") {
							print "<div id='content-wide'><div class='contentinner'>" ;
								//Get floating module menu
								if (substr($_SESSION[$guid]["address"],0,8)=="/modules") {
									$moduleID=checkModuleReady($_SESSION[$guid]["address"], $connection2 );
									if ($moduleID!=FALSE) {
										$gibbonRoleIDCurrent=NULL ;
										if (isset($_SESSION[$guid]["gibbonRoleIDCurrent"])) {
											$gibbonRoleIDCurrent=$_SESSION[$guid]["gibbonRoleIDCurrent"] ;
										}
										try {
											$data=array("gibbonModuleID"=>$moduleID, "gibbonRoleID"=>$gibbonRoleIDCurrent); 
											$sql="SELECT gibbonmodule.entryURL AS moduleEntry, gibbonmodule.name AS moduleName, gibbonaction.name, gibbonaction.precedence, gibbonaction.category, gibbonaction.entryURL, URLList FROM gibbonmodule, gibbonaction, gibbonpermission WHERE (gibbonmodule.gibbonModuleID=:gibbonModuleID) AND (gibbonmodule.gibbonModuleID=gibbonaction.gibbonModuleID) AND (gibbonaction.gibbonActionID=gibbonpermission.gibbonActionID) AND (gibbonpermission.gibbonRoleID=:gibbonRoleID) AND NOT gibbonaction.entryURL='' ORDER BY gibbonmodule.name, category, gibbonaction.name, precedence DESC";
											$result=$connection2->prepare($sql);
											$result->execute($data);
										}
										catch(PDOException $e) { }
	
										if ($result->rowCount()>0) {			
											
											$currentCategory="" ;
											$lastCategory="" ;
											$currentName="" ;
											$lastName="" ;
											$count=0;
											$links=0 ;
											$menu="" ;
											while ($row=$result->fetch()) {
												$moduleName=$row["moduleName"] ;
												$moduleEntry=$row["moduleEntry"] ;
			
												$currentCategory=$row["category"] ;
												if (strpos($row["name"],"_")>0) {
													$currentName=_(substr($row["name"],0,strpos($row["name"],"_"))) ;
												}
												else {
													$currentName=_($row["name"]) ;
												}
					
												if ($currentName!=$lastName) {
													if ($currentCategory!=$lastCategory) {
														$menu.="<optgroup label='--" .  _($currentCategory) . "--'/>" ;
													}
													$selected="" ;
													if ($_GET["q"]=="/modules/" . $row["moduleName"] . "/" . $row["entryURL"]) {
														$selected="selected" ;
													}
													$menu.="<option value='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $row["moduleName"] . "/" . $row["entryURL"] . "' $selected>" . _($currentName) . "</option>" ;
													$links++ ;
												}
												$lastCategory=$currentCategory ;
												$lastName=$currentName ;
												$count++ ;
											}
											
											$menu.="<script>
												$(\"#floatingModuleMenu\").change(function() {
													document.location.href = $(this).val();
												});
											</script>" ;
		
											if ($links>1) {
												print "<div class='linkTop'>" ;
													print "<select id='floatingModuleMenu' style='width: 200px'>" ;
														print $menu ;
													print "</select>" ;
													print "<div style='float: right; padding-top: 10px'>" ;
														print _("Module Menu") ;
													print "</div>" ;
												print "</div>" ;
											}
										}
									}
								}
								
							//No closing </div> required here
						}
						else {
							print "<div id='content'><div class='contentinner'>" ;
						}
						
						//Show index page Content
							if ($_SESSION[$guid]["address"]=="") {
								//Welcome message
								if (isset($_SESSION[$guid]["username"])==FALSE) {
									//Create auto timeout message
									if (isset($_GET["timeout"])) {
										if ($_GET["timeout"]=="true") {
											print "<div class='warning'>" ;
												print _('Your session expired, so you were automatically logged out of the system.');
											print "</div>" ;
										}
									}			
								}
								else {
									$category=getRoleCategory($_SESSION[$guid]["gibbonRoleIDCurrent"], $connection2) ;
									if ($category==FALSE) {
										print "<div class='error'>" ;
										print _("Your current role type cannot be determined.") ;
										print "</div>" ;
									}
									//Display Parent Dashboard
									else if ($category=="Parent" || $category=="Student") {?>
										<script>
											$("#header-menu").hide();
										</script>
<?php									try {
											$dataChild=array("gibbonSchoolYearID"=>$_SESSION[$guid]["gibbonSchoolYearID"]); 
											$sqlChild="SELECT gibbonperson.gibbonPersonID, image_75, surname, preferredName, dateStart, gibbonyeargroup.nameShort AS yearGroup, gibbonrollgroup.nameShort AS rollGroup, gibbonrollgroup.website AS rollGroupWebsite, gibbonrollgroup.gibbonRollGroupID FROM gibbonperson 
											JOIN gibbonstudentenrolment ON (gibbonperson.gibbonPersonID=gibbonstudentenrolment.gibbonPersonID) 
											JOIN gibbonyeargroup ON (gibbonstudentenrolment.gibbonYearGroupID=gibbonyeargroup.gibbonYearGroupID) 
											JOIN gibbonrollgroup ON (gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID)
											WHERE gibbonstudentenrolment.gibbonSchoolYearID=:gibbonSchoolYearID AND gibbonperson.gibbonPersonID=".$_SESSION[$guid]["gibbonPersonID"] ;
											$resultChild=$connection2->prepare($sqlChild);
											$resultChild->execute($dataChild);
											$rowChild=$resultChild->fetch()	;										
										}
										catch(PDOException $e) { 
											print "<div class='error'>" . $e->getMessage() . "</div>" ; 
										}
											$students[0]=$rowChild["surname"] ;
											$students[1]=$rowChild["preferredName"] ;
											$students[2]=$rowChild["yearGroup"] ;
											$students[3]=$rowChild["rollGroup"] ;
											$students[4]=$rowChild["gibbonPersonID"] ;
											$students[5]=$rowChild["image_75"] ;
											$students[6]=$rowChild["dateStart"] ;
											$students[7]=$rowChild["gibbonRollGroupID"] ;
											$students[8]=$rowChild["rollGroupWebsite"] ;
										print "<h2>" ;
											print _("Parental Dashboard") ;
										print "</h2>" ;
										include "./modules/Timetable/moduleFunctions.php" ;
										
										
										print "<h4 style='width: 135px;text-align: center;'>" ;
											print $students[1];
										print "</h4>" ;
											
										print "<div style='margin-right: 1%; float:left; width: 15%; text-align: center'>" ;
										print getUserPhoto($guid, $students[5], 75) ;
										print "<div style='height: 5px'></div>" ;
											print "<span style='font-size: 70%'>" ;
												print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Students/student_view_details.php&gibbonPersonID=" . $students[4] . "'>" . _('Student Profile') . "</a><br/>" ;
												if (isActionAccessible($guid, $connection2, "/modules/Roll Groups/rollGroups_details.php")) {
													print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Roll Groups/rollGroups_details.php&gibbonRollGroupID=" . $students[7] . "'>" . _('Section') . " (" . $students[3] . ")</a><br/>" ;
												}
												/*if ($students[8]!="") {
													print "<a target='_blank' href='" . $students[8] . "'>" . $students[3] . " " . _('Website') . "</a>" ;
												}	*/
											print "</span>" ;
										print "</div>" ;
										print "<div style='margin-bottom: 30px; margin-left: 1%; float: left; width: 83%'>" ;
										$dashboardContents=getParentalDashboardContents($connection2, $guid, $students[4]) ;
											if ($dashboardContents==FALSE) {
												print "<div class='error'>" ;
													print _("There are no records to display.") ;
												print "</div>" ;
											}
											else {
												print $dashboardContents ;
											}
										print "</div>" ;
											
										
									}
									//else if ($category=="Student" OR $category=="Staff") {
									/*else if ($category=="Student") {
										//Get Smart Workflow help message
										if ($category=="Staff") {
											$smartWorkflowHelp=getSmartWorkflowHelp($connection2, $guid) ;
											if ($smartWorkflowHelp!=false) {
												print $smartWorkflowHelp ;
											}
										}
										//Display planner
										$date=date("Y-m-d") ;
										if (isSchoolOpen($guid, $date, $connection2)==TRUE AND isActionAccessible($guid, $connection2, "/modules/Planner/planner.php") AND $_SESSION[$guid]["username"]!="") {			
											try {
												$data=array("gibbonSchoolYearID"=>$_SESSION[$guid]["gibbonSchoolYearID"],"date"=>$date,"gibbonPersonID"=>$_SESSION[$guid]["gibbonPersonID"],"gibbonSchoolYearID2"=>$_SESSION[$guid]["gibbonSchoolYearID"],"date2"=>$date,"gibbonPersonID2"=>$_SESSION[$guid]["gibbonPersonID"]); 
												$sql="(SELECT gibboncourseclass.gibbonCourseClassID, gibbonplannerentry.gibbonPlannerEntryID, gibbonUnitID, gibbonHookID, gibbonplannerentry.gibbonCourseClassID, gibboncourse.nameShort AS course, gibboncourseclass.nameShort AS class, gibbonplannerentry.name, timeStart, timeEnd, viewableStudents, viewableParents, homework, homeworkSubmission, homeworkCrowdAssess, role, date, summary, gibbonplannerentrystudenthomework.homeworkDueDateTime AS myHomeworkDueDateTime FROM gibbonplannerentry JOIN gibboncourseclass ON (gibbonplannerentry.gibbonCourseClassID=gibboncourseclass.gibbonCourseClassID) JOIN gibboncourseclassperson ON (gibboncourseclass.gibbonCourseClassID=gibboncourseclassperson.gibbonCourseClassID) JOIN gibboncourse ON (gibboncourse.gibbonCourseID=gibboncourseclass.gibbonCourseID) LEFT JOIN gibbonplannerentrystudenthomework ON (gibbonplannerentrystudenthomework.gibbonPlannerEntryID=gibbonplannerentry.gibbonPlannerEntryID AND gibbonplannerentrystudenthomework.gibbonPersonID=gibboncourseclassperson.gibbonPersonID) WHERE gibbonSchoolYearID=:gibbonSchoolYearID AND date=:date AND gibboncourseclassperson.gibbonPersonID=:gibbonPersonID AND NOT role='Student - Left' AND NOT role='Teacher - Left') UNION (SELECT gibboncourseclass.gibbonCourseClassID, gibbonplannerentry.gibbonPlannerEntryID, gibbonUnitID, gibbonHookID, gibbonplannerentry.gibbonCourseClassID, gibboncourse.nameShort AS course, gibboncourseclass.nameShort AS class, gibbonplannerentry.name, timeStart, timeEnd, viewableStudents, viewableParents, homework, homeworkSubmission, homeworkCrowdAssess,  role, date, summary, NULL AS myHomeworkDueDateTime FROM gibbonplannerentry JOIN gibboncourseclass ON (gibbonplannerentry.gibbonCourseClassID=gibboncourseclass.gibbonCourseClassID) JOIN gibbonplannerentryguest ON (gibbonplannerentryguest.gibbonPlannerEntryID=gibbonplannerentry.gibbonPlannerEntryID) JOIN gibboncourse ON (gibboncourse.gibbonCourseID=gibboncourseclass.gibbonCourseID) WHERE gibbonSchoolYearID=:gibbonSchoolYearID2 AND date=:date2 AND gibbonplannerentryguest.gibbonPersonID=:gibbonPersonID2) ORDER BY date, timeStart, course, class" ; 
												$result=$connection2->prepare($sql);
												$result->execute($data);
											}
											catch(PDOException $e) {
												print "<div class='error'>" . $e->getMessage() . "</div>" ; 
											}
											if ($result->rowCount()>0) {
												print "<h2>" ;
													print _("Today's Lessons") ;
												print "</h2>" ;
												
												if (isset($_GET["updateReturn"])) { $updateReturn=$_GET["updateReturn"] ; } else { $updateReturn="" ; }
												$updateReturnMessage="" ;
												$class="error" ;
												if (!($updateReturn=="")) {
													if ($updateReturn=="fail0") {
														$updateReturnMessage=_("Your request failed because you do not have access to this action.") ;	
													}
													else if ($updateReturn=="fail1") {
														$updateReturnMessage=_("Your request failed because your inputs were invalid.") ;	
													}
													else if ($updateReturn=="fail2") {
														$updateReturnMessage=_("Your request failed due to a database error.") ;	
													}
													else if ($updateReturn=="success0") {
														$updateReturnMessage=_("Your request was completed successfully.") ;	
														$class="success" ;
													}
													print "<div class='$class'>" ;
														print $updateReturnMessage;
													print "</div>" ;
												} 
												
												print "<div class='linkTop'>" ;
													print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Planner/planner.php'>" . _('View Planner') . "</a>" ;
												print "</div>" ;
												
												print "<table cellspacing='0' style='width: 100%'>" ;
													print "<tr class='head'>" ;
														print "<th>" ;
															print _("Class") . "<br/>" ;
														print "</th>" ;
														print "<th>" ;
															print _("Lesson") . "</br>" ;
															print "<span style='font-size: 85%; font-style: italic'>" . _('Unit') . "</span>" ;
														print "</th>" ;
														print "<th>" ;
															print _("Homework") ;
														print "</th>" ;
														print "<th>" ;
															print _("Summary") ;
														print "</th>" ;
														print "<th>" ;
															print _("Like") ;
														print "</th>" ;
														print "<th>" ;
															print _("Action") ;
														print "</th>" ;
													print "</tr>" ;
													
													$count=0;
													$rowNum="odd" ;
													while ($row=$result->fetch()) {
														if (!($row["role"]=="Student" AND $row["viewableStudents"]=="N")) {
															if ($count%2==0) {
																$rowNum="even" ;
															}
															else {
																$rowNum="odd" ;
															}
															$count++ ;
															
															//Highlight class in progress
															if ((date("H:i:s")>$row["timeStart"]) AND (date("H:i:s")<$row["timeEnd"]) AND ($date)==date("Y-m-d")) {
																$rowNum="current" ;
															}
															
															//COLOR ROW BY STATUS!
															print "<tr class=$rowNum>" ;
																print "<td>" ;
																	print $row["course"] . "." . $row["class"] . "<br/>" ;
																	print "<span style='font-style: italic; font-size: 75%'>" . substr($row["timeStart"],0,5) . "-" . substr($row["timeEnd"],0,5) . "</span>" ;
																print "</td>" ;
																print "<td>" ;
																	print "<b>" . $row["name"] . "</b><br/>" ;
																	print "<span style='font-size: 85%; font-style: italic'>" ;
																		$unit=getUnit($connection2, $row["gibbonUnitID"], $row["gibbonHookID"], $row["gibbonCourseClassID"]) ;
																		if (isset($unit[0])) {
																			print $unit[0] ;
																			if ($unit[1]!="") {
																				print "<br/><i>" . $unit[1] . " " . _('Unit') . "</i>" ;
																			}
																		}
																	print "</span>" ;
																print "</td>" ;
																print "<td>" ;
																	if ($row["homework"]=="N" AND $row["myHomeworkDueDateTime"]=="") {
																		print _("No") ;
																	}
																	else {
																		if ($row["homework"]=="Y") {
																			print _("Yes") . ": " . _("Teacher Recorded") . "<br/>" ;
																			if ($row["homeworkSubmission"]=="Y") {
																				print "<span style='font-size: 85%; font-style: italic'>+" . _("Submission") . "</span><br/>" ;
																				if ($row["homeworkCrowdAssess"]=="Y") {
																					print "<span style='font-size: 85%; font-style: italic'>+" . _("Crowd Assessment") . "</span><br/>" ;
																				}
																			}
																		}
																		if ($row["myHomeworkDueDateTime"]!="") {
																			print _("Yes") . ": " . _("Student Recorded") . "</br>" ;
																		}
																	}
																print "</td>" ;
																print "<td>" ;
																	print $row["summary"] ;
																print "</td>" ;
																print "<td>" ;
																	if ($row["role"]=="Teacher") {
																		try {
																			$dataLike=array("gibbonPlannerEntryID"=>$row["gibbonPlannerEntryID"]); 
																			$sqlLike="SELECT * FROM gibbonplannerentrylike WHERE gibbonPlannerEntryID=:gibbonPlannerEntryID" ;
																			$resultLike=$connection2->prepare($sqlLike);
																			$resultLike->execute($dataLike); 
																		}
																		catch(PDOException $e) { }
																		print $resultLike->rowCount() ;
																	}
																	else {
																		try {
																			$dataLike=array("gibbonPlannerEntryID"=>$row["gibbonPlannerEntryID"],"gibbonPersonID"=>$_SESSION[$guid]["gibbonPersonID"]); 
																			$sqlLike="SELECT * FROM gibbonplannerentrylike WHERE gibbonPlannerEntryID=:gibbonPlannerEntryID AND gibbonPersonID=:gibbonPersonID" ;
																			$resultLike=$connection2->prepare($sqlLike);
																			$resultLike->execute($dataLike); 
																		}
																		catch(PDOException $e) { }
																		if ($resultLike->rowCount()!=1) {
																			print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/modules/Planner/plannerProcess.php?gibbonPlannerEntryID=" . $row["gibbonPlannerEntryID"] . "&address=/modules/Planner/planner.php&viewBy=Class&gibbonCourseClassID=" . $row["gibbonPlannerEntryID"] . "&date=&returnToIndex=Y'><img src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/like_off.png'></a>" ;
																		}
																		else {
																			print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/modules/Planner/plannerProcess.php?gibbonPlannerEntryID=" . $row["gibbonPlannerEntryID"] . "&address=/modules/Planner/planner.php&viewBy=Class&gibbonCourseClassID=" . $row["gibbonPlannerEntryID"] . "&date=&returnToIndex=Y'><img src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/like_on.png'></a>" ;
																		}
																	}
																print "</td>" ;
																print "<td>" ;
																	print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Planner/planner_view_full.php&viewBy=class&gibbonCourseClassID=" . $row["gibbonCourseClassID"] . "&gibbonPlannerEntryID=" . $row["gibbonPlannerEntryID"] . "'><img title='" . _('View') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/plus.png'/></a>" ;
																print "</td>" ;
															print "</tr>" ;
														}
													}
												print "</table>" ;
											}
										}
										
										//Display TT
										if (isActionAccessible($guid, $connection2, "/modules/Timetable/tt.php") AND $_SESSION[$guid]["username"]!="" AND (getRoleCategory($_SESSION[$guid]["gibbonRoleIDCurrent"], $connection2)=="Staff" OR getRoleCategory($_SESSION[$guid]["gibbonRoleIDCurrent"], $connection2)=="Student")) {			
											?>
											<script type="text/javascript">
												$(document).ready(function(){
													$("#tt").load("<?php print $_SESSION[$guid]["absoluteURL"] ?>/index_tt_ajax.php",{"ttDate": "<?php print @$_POST["ttDate"] ?>", "fromTT": "<?php print @$_POST["fromTT"] ?>", "personalCalendar": "<?php print @$_POST["personalCalendar"] ?>", "schoolCalendar": "<?php print @$_POST["schoolCalendar"] ?>", "spaceBookingCalendar": "<?php print @$_POST["spaceBookingCalendar"] ?>"});
												});
											</script>
											<?php
											print "<h2>" . _("My Timetable") . "</h2>" ;
											print "<div id='tt' name='tt' style='width: 100%; min-height: 40px; text-align: center'>" ;
												print "<img style='margin: 10px 0 5px 0' src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/Default/img/loading.gif' alt='" . _('Loading') . "' onclick='return false;' /><br/><p style='text-align: center'>" . _('Loading') . "</p>" ;
											print "</div>" ;
										}
										
										//Display "My Roll Groups"
										?>
										<script type='text/javascript'>
											$(function() {
												$( "#tabs" ).tabs({
													ajaxOptions: {
														error: function( xhr, status, index, anchor ) {
															$( anchor.hash ).html(
																"<?php
																print _("Couldn't load this tab.") ; 
																?>"
															);
														}
													}
												});
											});
										</script>
	
										<?php
										try {
											$data=array("gibbonPersonIDTutor"=>$_SESSION[$guid]["gibbonPersonID"], "gibbonPersonIDTutor2"=>$_SESSION[$guid]["gibbonPersonID"], "gibbonPersonIDTutor3"=>$_SESSION[$guid]["gibbonPersonID"],"gibbonSchoolYearID"=>$_SESSION[$guid]["gibbonSchoolYearID"]); 
											$sql="SELECT * FROM gibbonrollgroup WHERE (gibbonPersonIDTutor=:gibbonPersonIDTutor OR gibbonPersonIDTutor2=:gibbonPersonIDTutor2 OR gibbonPersonIDTutor3=:gibbonPersonIDTutor3) AND gibbonSchoolYearID=:gibbonSchoolYearID" ;
											$result=$connection2->prepare($sql);
											$result->execute($data);
										}
										catch(PDOException $e) { 
											print "<div class='error'>" . $e->getMessage() . "</div>" ; 
										}
										
										$h2=_("My Roll Groups") ;
										if ($result->rowCount()==1) {
											$h2=_("My Roll Group") ;
										}
										if ($result->rowCount()>0) {
											print "<h2>" ;
												print $h2 ;
											print "</h2>" ;
											
											?>
											<div id="tabs" style='margin: 10px 0 20px 0'>
												<ul>
													<li><a href="#tabs-1"><?php print _('Students') ?></a></li>
													<li><a href="#tabs-2"><?php print _('Behaviour') ?></a></li>
												</ul>
												<div id="tabs-1">
													<?php
													//Students
													$sqlWhere="" ;
													while ($row=$result->fetch()) {
														$sqlWhere.="gibbonRollGroupID=" . $row["gibbonRollGroupID"] . " OR " ;
														if ($result->fetch()>1) {
															print "<h4>" ;
																print $row["name"] ;
															print "</h4>" ;
														}
														print "<div class='linkTop' style='margin-top: 0px'>" ;
														print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Attendance/attendance_take_byRollGroup.php&gibbonRollGroupID=" . $row["gibbonRollGroupID"] . "'>" . _('Take Attendance') . "<img style='margin-left: 5px' title='" . _('Take Attendance') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/attendance.png'/></a> | " ;
														print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/indexExport.php?gibbonRollGroupID=" . $row["gibbonRollGroupID"] . "'>" . _('Export to Excel') . "<img style='margin-left: 5px' title='" . _('Export to Excel') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/download.png'/></a>" ;
														print "</div>" ;
														
														printRollGroupTable($guid, $row["gibbonRollGroupID"],5,$connection2) ;
													}
													$sqlWhere=substr($sqlWhere,0,-4) ;
													?>
												</div>
												<div id="tabs-2">
													<?php
													$plural="s" ;
													if ($result->rowCount()==1) {
														$plural="" ;
													}
													try {
														$data=array("gibbonSchoolYearID"=>$_SESSION[$guid]["gibbonSchoolYearID"], "gibbonSchoolYearID2"=>$_SESSION[$guid]["gibbonSchoolYearID"]); 
														$sql="SELECT gibbonbehaviour.*, student.surname AS surnameStudent, student.preferredName AS preferredNameStudent, creator.surname AS surnameCreator, creator.preferredName AS preferredNameCreator, creator.title FROM gibbonbehaviour JOIN gibbonperson AS student ON (gibbonbehaviour.gibbonPersonID=student.gibbonPersonID) JOIN gibbonstudentenrolment ON (gibbonstudentenrolment.gibbonPersonID=student.gibbonPersonID) JOIN gibbonperson AS creator ON (gibbonbehaviour.gibbonPersonIDCreator=creator.gibbonPersonID) WHERE gibbonstudentenrolment.gibbonSchoolYearID=:gibbonSchoolYearID AND gibbonbehaviour.gibbonSchoolYearID=:gibbonSchoolYearID2 AND ($sqlWhere) ORDER BY timestamp DESC" ; 
														$result=$connection2->prepare($sql);
														$result->execute($data);
													}
													catch(PDOException $e) { 
														print "<div class='error'>" . $e->getMessage() . "</div>" ; 
													}
													
													if (isActionAccessible($guid, $connection2, "/modules/Behaviour/behaviour_manage_add.php")) {
														print "<div class='linkTop'>" ;
															print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Behaviour/behaviour_manage_add.php&gibbonPersonID=&gibbonRollGroupID=&gibbonYearGroupID=&type='>" . _('Add') . "<img style='margin: 0 0 -4px 5px' title='" . _('Add') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/page_new.png'/></a>" ;
															$policyLink=getSettingByScope($connection2, "Behaviour", "policyLink") ;
															if ($policyLink!="") {
																print " | <a target='_blank' href='$policyLink'>" . _('View Behaviour Policy') . "</a>" ;
															}
														print "</div>" ;
													}
													
													if ($result->rowCount()<1) {
														print "<div class='error'>" ;
														print _("There are no records to display.") ;
														print "</div>" ;
													}
													else {
														print "<table cellspacing='0' style='width: 100%'>" ;
															print "<tr class='head'>" ;
																print "<th>" ;
																	print _("Student & Date") ;
																print "</th>" ;
																print "<th>" ;
																	print _("Type") ;
																print "</th>" ;
																print "<th>" ;
																	print _("Descriptor") ;
																print "</th>" ;
																print "<th>" ;
																	print _("Level") ;
																print "</th>" ;
																print "<th>" ;
																	print _("Teacher") ;
																print "</th>" ;
																print "<th>" ;
																	print _("Action") ;
																print "</th>" ;
															print "</tr>" ;
															
															$count=0;
															$rowNum="odd" ;
															while ($row=$result->fetch()) {
																if ($count%2==0) {
																	$rowNum="even" ;
																}
																else {
																	$rowNum="odd" ;
																}
																$count++ ;
																
																//COLOR ROW BY STATUS!
																print "<tr class=$rowNum>" ;
																	print "<td>" ;
																		print "<b>" . formatName("", $row["preferredNameStudent"], $row["surnameStudent"], "Student", false ) . "</b><br/>" ;
																		if (substr($row["timestamp"],0,10)>$row["date"]) {
																			print _("Date Updated") . ": " . dateConvertBack($guid, substr($row["timestamp"],0,10)) . "<br/>" ;
																			print _("Incident Date") . ": " . dateConvertBack($guid, $row["date"]) . "<br/>" ;
																		}
																		else {
																			print dateConvertBack($guid, $row["date"]) . "<br/>" ;
																		}
																	print "</td>" ;
																	print "<td style='text-align: center'>" ;
																		if ($row["type"]=="Negative") {
																			print "<img title='" . _('Negative') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/iconCross.png'/> " ;
																		}
																		else if ($row["type"]=="Positive") {
																			print "<img title='" . _('Position') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/iconTick.png'/> " ;
																		}
																	print "</td>" ;
																	print "<td>" ;
																		print trim($row["descriptor"]) ;
																	print "</td>" ;
																	print "<td>" ;
																		print trim($row["level"]) ;
																	print "</td>" ;
																	print "<td>" ;
																		print formatName($row["title"], $row["preferredNameCreator"], $row["surnameCreator"], "Staff", false ) . "<br/>" ;
																	print "</td>" ;
																	print "<td>" ;
																		print "<script type='text/javascript'>" ;	
																			print "$(document).ready(function(){" ;
																				print "\$(\".comment-$count\").hide();" ;
																				print "\$(\".show_hide-$count\").fadeIn(1000);" ;
																				print "\$(\".show_hide-$count\").click(function(){" ;
																				print "\$(\".comment-$count\").fadeToggle(1000);" ;
																				print "});" ;
																			print "});" ;
																		print "</script>" ;
																		if ($row["comment"]!="") {
																			print "<a title='" . _('View Description') . "' class='show_hide-$count' onclick='false' href='#'><img style='padding-right: 5px' src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/Default/img/page_down.png' alt='" . _('Show Comment') . "' onclick='return false;' /></a>" ;
																		}
																	print "</td>" ;
																print "</tr>" ;
																if ($row["comment"]!="") {
																	if ($row["type"]=="Positive") {
																		$bg="background-color: #D4F6DC;" ;
																	}
																	else {
																		$bg="background-color: #F6CECB;" ;
																	}
																	print "<tr class='comment-$count' id='comment-$count'>" ;
																		print "<td style='$bg' colspan=6>" ;
																			print $row["comment"] ;
																		print "</td>" ;
																	print "</tr>" ;
																}
																print "</tr>" ;
																print "</tr>" ;
															}
														print "</table>" ;
													}
													?>
												</div>
											</div>
											<?php
										}
									}*/
									/*-------------------------*/
									else if($category=="Staff"){
										$sidebar="false";
										include "modules/Dashboard/template.php";	
									}
									/*-------------------------*/
								}
							}
							else {
								if (strstr($_SESSION[$guid]["address"],"..")!=FALSE) {
									print "<div class='error'>" ;
									print _("Illegal address detected: access denied.") ;
									print "</div>" ;
								}
								else {
									if(is_file("./" . $_SESSION[$guid]["address"])) {
										//Include the page
										include ("./" . $_SESSION[$guid]["address"]) ;
									}
									else {
										include "./error.php" ;
									}
								}
							}
							?>
						</div></div>
						<?php
						if ($sidebar!="false") {
							?>
							<div id="sidebar">
								<?php sidebar($connection2, $guid) ; ?>
							</div>
							<br style="clear: both">
							<?php
						}
								//Show index page Content
							if ($_SESSION[$guid]["address"]=="") {
								//Welcome message
								if (isset($_SESSION[$guid]["username"])==FALSE) {
									//Create auto timeout message
									if (isset($_GET["timeout"])) {
										if ($_GET["timeout"]=="true") {
											print "<div class='warning'>" ;
												print _('Your session expired, so you were automatically logged out of the system.');
											print "</div>" ;
										}
									}
									/*
									print "<div class='welcometext'>";	
									print "<div class='textbox'>";		
									print "<h2>" ;
									print _("Welcome") ;
									print "</h2>" ;
									print "<p>" ;
									print $_SESSION[$guid]["indexText"] ;
									print "</p>" ;
									print "</div>";	
									*/
									//Public applications permitted?
									$publicApplications=getSettingByScope($connection2, "Application Form", "publicApplications" ) ; 
									if ($publicApplications=="Y") {
										print "<div class='textbox'>";
										print "<h2>" ;
											print _("Applications") ;
										print "</h2>" ;
										print "<p>" ;
											print sprintf(_('Parents of students interested in study at %1$s may use our %2$s online form%3$s to initiate the application process.'), $_SESSION[$guid]["organisationName"], "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/?q=/modules/Application Form/applicationForm.php'>", "</a>") ;
										print "</p>" ;
										print "</div>";	
									}
									
									//Public departments permitted?
									$makeDepartmentsPublic=getSettingByScope($connection2, "Departments", "makeDepartmentsPublic" ) ; 
									if ($makeDepartmentsPublic=="Y") {
										print "<div class='textbox'>";
										print "<h2>" ;
											print _("Departments") ;
										print "</h2>" ;
										print "<p>" ;
											print sprintf(_('Please feel free to %1$sbrowse our departmental information%2$s, to learn more about %3$s.'), "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/?q=/modules/Departments/departments.php'>", "</a>", $_SESSION[$guid]["organisationName"]) ;
										print "</p>" ;
										print "</div>";	
									}
									
									//Public units permitted?
									$makeUnitsPublic=getSettingByScope($connection2, "Planner", "makeUnitsPublic" ) ; 
									if ($makeUnitsPublic=="Y") {
										print "<div class='textbox'>";
										print "<h2>" ;
											print _("Learn With Us") ;
										print "</h2>" ;
										print "<p>" ;
											print sprintf(_('We are sharing some of our units of study with members of the public, so you can learn with us. Feel free to %1$sbrowse our public units%2$s.'), "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/?q=/modules/Planner/units_public.php&sidebar=false'>", "</a>", $_SESSION[$guid]["organisationName"]) ;
										print "</p>" ;
										print "</div>";	
									}
									
									//Get any elements hooked into public home page, checking if they are turned on
									try {
										$dataHook=array(); 
										$sqlHook="SELECT * FROM gibbonhook WHERE type='Public Home Page' ORDER BY name" ;
										$resultHook=$connection2->prepare($sqlHook);
										$resultHook->execute($dataHook);
									}
									catch(PDOException $e) { }
									while ($rowHook=$resultHook->fetch()) {
										$options=unserialize(str_replace("'", "\'", $rowHook["options"])) ;
										$check=getSettingByScope($connection2, $options["toggleSettingScope"], $options["toggleSettingName"]) ;
										if ($check==$options["toggleSettingValue"]) { //If its turned on, display it
										print "<div class='textbox'>";
											print "<h2>" ;
												print $options["title"] ;
											print "</h2>" ;
											print "<p>" ;
												print stripslashes($options["text"]) ;
											print "</p>" ;
											print "</div>";	
										}
									}
									print "</div>";
								}
						
							}
						?>
					</div>
					<div id="footer"><div class="footerinner">
						Managed by <a href="http://www.hirventures.com" target="_blank">H.I.R. Ventures</a>. <?php print _("Powered by") ?> <a target="_blank" href="http://gibbonedu.org">Gibbon</a> v9.0.00 <br>
						<span style='font-size: 90%; '>
							Created under the <a target="_blank" href="http://www.gnu.org/licenses/gpl.html">GNU GPL</a> at <a target="_blank" href="http://www.ichk.edu.hk">ICHK</a> | <a target="_blank" href="https://www.gibbonedu.org/contribute/">Credits</a><br>
							<?php
								$seperator=FALSE ;
								$thirdLine=FALSE ;
								if ($_SESSION[$guid]["i18n"]["maintainerName"]!="" AND $_SESSION[$guid]["i18n"]["maintainerName"]!="Gibbon") {
									if ($_SESSION[$guid]["i18n"]["maintainerWebsite"]!="") {
										//print _("Translation led by") . " <a target='_blank' href='" . $_SESSION[$guid]["i18n"]["maintainerWebsite"] . "'>" . $_SESSION[$guid]["i18n"]["maintainerName"] . "</a>" ;
									}
									else {
										//print _("Translation led by") . " " . $_SESSION[$guid]["i18n"]["maintainerName"] ;
									}
									$seperator=TRUE ;
									$thirdLine=TRUE ;
								}
								if ($_SESSION[$guid]["gibbonThemeName"]!="Default" AND $_SESSION[$guid]["gibbonThemeAuthor"]!="") {
									if ($seperator) {
										//print " | " ;
									}
									if ($_SESSION[$guid]["gibbonThemeURL"]!="") {
										print _("Theme by") . " <a target='_blank' href='" . $_SESSION[$guid]["gibbonThemeURL"] . "'>" . $_SESSION[$guid]["gibbonThemeAuthor"] . "</a>" ;
									}
									else {
										//print _("Theme by") . " " . $_SESSION[$guid]["gibbonThemeAuthor"] ;
									}
									$thirdLine=TRUE ;
								}
								if ($thirdLine==FALSE) {
									print "<br/>" ; 
								}
							?>
						</span>
						<img style='z-index: 9999;' alt='Logo Small' src='./themes/Default/img/logoFooter.png' class='footerlogo'>
					</div></div>
				</div>
			</div>
		</body>
	</html>
	<?php
}
?>