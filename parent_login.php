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
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
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
		
	<body class="homepage" style="background:white" <?php
			

			if (isset($_SESSION[$guid]["username"])) {
					print "";	
			} ?>>
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
		
		<center>
		    <div id="wrapOuter">
		        <?php
				print "<div class='minorLinks'><div class='minorlinkinner'>" ;
					print getMinorLinks($connection2, $guid, $cacheLoad) ;
				print "</div></div>" ;
				?>
    		    <br>
    				<div class="row">
    				    <div class="col-md-12">
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
    						        </div>
    						    </div>
    						    <div id="header-menu"><div class="menuinner">
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
    						</div>
					    </div>   
					</div>
					<br>
					<div class="row">
					    <div class="col-md-4"></div>
					    <div class="col-md-4" style="text-align:center">
					        
					       <form name="loginForm" method="post" action="./login1.php?<?php if (isset($_GET["q"])) { print "q=" . $_GET["q"] ; } ?>">
			                    <img src="http://lakshya.calcuttapublicschool.in/lakshya_green_an//themes/Default/img/schoollogo.png" alt="">
			                    <br>
			                    <br>
			                    <h2 style="color:green">Parent's Login</h2>
			                    <br>
				                <div class="form-group">
				                    <input name="username" class="form-control" id="username" width="150px" maxlength="20" type="text" placeholder="Username">
							    </div>
							    <br>
					            <div class="form-group">
							        <input name="password" class="form-control" id="password" maxlength="20" type="password" placeholder="Password">
							    </div>
						        <br>
								<span style="font-size: 13px">
								    <a href="http://lakshya.calcuttapublicschool.in/lakshya_green_an//index.php?q=passwordReset.php">Forgot Password?</a>
								</span>
						        <br>
						        <br>
						        <div class="form-group">
						            <a class="btn btn-primary" href="http://localhost/driver,html">&lt;-Back</a>
						            &nbsp;
							        <input type="submit" class="btn btn-primary" value="Login">
							</div>

			</form>
					   
					    </div>
					</div>
					<div id="content-wrap" >
						
							<br style="clear: both">
							</div>					</div>
					<div id="footer"><div class="footerinner">
						Managed by <a href="http://www.hirventures.com" target="_blank">H.I.R. Ventures</a>. Powered by <a target="_blank" href="http://gibbonedu.org">Gibbon</a> v9.0.00 <br>
						<span style="font-size: 90%; ">
							Created under the <a target="_blank" href="http://www.gnu.org/licenses/gpl.html">GNU GPL</a> at <a target="_blank" href="http://www.ichk.edu.hk">ICHK</a> | <a target="_blank" href="https://www.gibbonedu.org/contribute/">Credits</a><br>
							<br>
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
							?></span>
						<img style="z-index: 9999;" alt="Logo Small" src="./themes/Default/img/logoFooter.png" class="footerlogo">
					</div></div>
				</div>
			</center>						
			
			
		
	
	</body></html>
	<?php
}
?>