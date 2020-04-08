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
		try{
			$sql1="SELECT * FROM `lakshyastaffattendancerule`";
			$result1=$connection2->prepare($sql1);
			$result1->execute();
			$rule=$result1->fetchAll();
			}
			catch(PDOException $e){
				echo $e;
			}

			try{
			$sql="SELECT `gibbonStaffID`,gibbonstaff.jobTitle,gibbonperson.preferredName FROM `gibbonstaff`
			LEFT JOIN gibbonperson on gibbonperson.gibbonPersonID=gibbonstaff.gibbonPersonID WHERE gibbonperson.dateEnd IS NULL ORDER BY gibbonstaff.priority";
			$result1=$connection2->prepare($sql);
			$result1->execute();
			$staffs=$result1->fetchAll();
			}
			catch(PDOException $e){
				echo $e;
			}
if(isset($_REQUEST['success']))
{
	echo "<div id='message' style='border:2px solid green;margin:5px; width:400px;'><h3 style='text-align:center'>Added Successfully!!</h3></div>";
}	
		echo "<form method='POST' action='{$_SESSION[$guid]["absoluteURL"]}/modules/{$_SESSION[$guid]["module"]}/credit_leave_process.php'>";	
			echo "<table width='80%' cellpadding='0' cellspacing='0'>";
				echo "<tr>";
					echo "<th style='text-align:center'>Name</th>";
				foreach($rule as $r){
					echo "<th style='text-align:center'>{$r['caption']}</th>";
				}
				echo "</tr>";
			foreach($staffs as $staff){
				$id=$staff['gibbonStaffID']+0;
				echo "<tr>";
					echo "<td><b>{$staff['preferredName']}</span></b><br><small>{$staff['jobTitle']}</small></td>";
				foreach($rule as $r){
				echo "<td><input type='text' name='rule_{$id}_{$r['rule_id']}' id='rule_{$r['rule_id']}'></td>";
				}
				echo "</tr>";
			}
				echo "<tr>";
						$g=sizeOf($rule)+1;
					echo "<td colspan='$g' style='text-align:center'><input type='submit' name='credit_leave' value='Save'></td>";
				echo "</tr>";	
			echo "</table>";
		echo "</form>";	
}
?>
	<script type="text/javascript"> 
      $(document).ready( function() {
        $('#message').delay(900).fadeOut();
      });
    </script>