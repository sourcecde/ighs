<?php 
@session_start() ;

//Set timezone from session variable
date_default_timezone_set($_SESSION[$guid]["timezone"]);

if (isActionAccessible($guid, $connection2, "/modules/Staff/staff_view_details.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
	if(!isset($_REQUEST['year_ld'])){
		?>
		<form  action='<?php print $_SESSION[$guid]["absoluteURL"]?>/index.php'>
		<input type="hidden" name="q" value="/modules/<?php print $_SESSION[$guid]["module"] ?>/attendance/create_leave_data.php">
		<table width="60%" cellpadding="0" cellspacing="0">
		<tr>
			<td></td>
			<td><input type='submit' name='year_ld' value='Select'></td>
		</tr>
		</table>
		</form>
		<?php
	}
	else {
		
	}
}
?>