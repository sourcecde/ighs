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
			$sql2="SELECT * FROM `lakshyastaffleavecredit`";
			$result2=$connection2->prepare($sql2);
			$result2->execute();
			$data=$result2->fetchAll();
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
			$data_array=array();
			foreach($data as $d){
					$data_array[$d['staff_id']][$d['timeStamp']][$d['rule_id']]=array($d['value'],$d['credit_id']);
			}
			$rule_array=array();
			foreach($rule as $r){
			$rule_array[$r['rule_id']]=$r['short_name'];	
			}
			echo "<h3>View credited Leave :</h3>";
			echo "<table width='80%' cellpadding='0' cellspacing='0' id='myTable'>";
				echo "<thead>";
				echo "<tr>";
					echo "<th style='text-align:center'>Name</th>";
					echo "<th>";
						echo "<table width='100%' cellpadding='0' cellspacing='0'>";
						echo "<th style='text-align:center width:150px;'>Date</th>";
						foreach($rule_array as $k=>$v){
							echo "<th style='width:100px;'>$v</th>";
						}
						echo "</table>";
					echo "</th>";
				echo "</tr>";
				echo "</thead>";
				echo "<tbody>";
			foreach($staffs as $staff){
				$id=$staff['gibbonStaffID']+0;
				if(!array_key_exists($id,$data_array))
					continue;
				echo "<tr>";
					echo "<td><b>{$staff['preferredName']}</span></b><br><small>{$staff['jobTitle']}</small></td>";
					echo "<td>";
					echo "<table width='100%' cellpadding='0' cellspacing='0'>";
					foreach($data_array[$id] as $key=>$value){
						echo "<tr>";
						echo "<td style='width:150px;'><b> ".date('d/m/Y', $key)."  </b></td>";
						foreach($rule_array as $k=>$v){
							echo "<td contenteditable='true' class='leave_box' id='{$value[$k][1]}' style='width:100px;'>{$value[$k][0]}</td>";	
						}
						echo "</tr>";
					}
					echo "</table>";
					echo "</td>";
				echo "</tr>";
			}
			echo "</tbody>";
			echo "</table>";

}			
?>
 <script src="<?php echo $_SESSION[$guid]["absoluteURL"] ;?>/modules/Staff/js/jquery.dataTables.min.js"></script>
 <script>
	 $(document).ready(function(){
		$('#myTable').DataTable({
    "oLanguage": {
      "sLengthMenu": '<select>'+
        '<option value="10">10</option>'+
        '<option value="50">50</option>'+
        '<option value="100">100</option>'+
        '<option value="-1">All</option>'+
        '</select>'
    }
  });
	});
 </script>