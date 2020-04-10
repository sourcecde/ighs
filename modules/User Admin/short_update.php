<?php
@session_start() ;

if (isActionAccessible($guid, $connection2, "/modules/User Admin/changeRollNumber.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
    
    
	$sql="SELECT `gibbonYearGroupID`,`name` FROM `gibbonyeargroup`";
	$result=$connection2->prepare($sql);
	$result->execute();
	$classDB=$result->fetchAll();

	$sql="SELECT `gibbonSchoolYearID`,`name` FROM `gibbonschoolyear`";
	$result=$connection2->prepare($sql);
	$result->execute();
	$yearDB=$result->fetchAll();
	
	
	$filterClass="";
	$filterSection="";
	$filterYear=$_SESSION[$guid]["gibbonSchoolYearID"];
	$sqlFilter="";
	if($_POST){
		if(isset($_POST['search_filter'])){
		
			extract($_POST);
			if($filterClass!="")
				$sqlFilter.=" AND gibbonstudentenrolment.gibbonYearGroupID=$filterClass ";
			if($filterSection!="")
				$sqlFilter.=" AND gibbonstudentenrolment.gibbonRollGroupID=$filterSection ";
			if($filterYear!="")
				$sqlFilter.=" AND gibbonstudentenrolment.gibbonSchoolYearID=$filterYear ";
		}	
	}
?>
<h3>SHORT UPDATE: </h3>
			<form method="POST" action="">
				<table width='100%' style='border: 2px solid #7030a0;'>
					<tr>
						<td><b>Select Class</b><select name='filterClass' id='filterClass'> 
								<option value=''>Select Class</option>
								<?php
								foreach($classDB as $c){
									$s=$filterClass==$c['gibbonYearGroupID']?"selected":"";
								echo "<option value='{$c['gibbonYearGroupID']}' $s>{$c['name']}</option>";
								}
								?>
							</select>
						</td>
						<td><b>Select Section</b><select name='filterSection' id='filterSection'>
								<?php
								if(isset($_REQUEST['filterClass']) && $_REQUEST['filterClass']!=""){
								$sql="SELECT `gibbonRollGroupID`,`name`,`gibbonYearGroupID` FROM `gibbonrollgroup` WHERE `gibbonYearGroupID`=".$_REQUEST['filterClass']." AND `gibbonSchoolYearID`=".$filterYear;
								$result=$connection2->prepare($sql);
	                            $result->execute();
	                            $sectionDB=$result->fetchAll();
								}
	                            print "<option value=''>Select Section</option>";
								if(isset($sectionDB)){
								foreach($sectionDB as $sc){
									$s=$filterSection==$sc['gibbonRollGroupID']?"selected":"";
								echo "<option value='{$sc['gibbonRollGroupID']}' $s>{$sc['name']}</option>";
								}
								}
								?>
							</select>
						</td>
						<td><b>Select Year</b><select name='filterYear' id='filterYear'>
								<?php
								foreach($yearDB as $y){
								if($filterYear!="")
									$s=$filterYear==$y['gibbonSchoolYearID']?"selected":"";
								else
									$s=$_SESSION[$guid]["gibbonSchoolYearID"]==$y["gibbonSchoolYearID"]?"selected":"";
								echo "<option value='{$y['gibbonSchoolYearID']}' $s>{$y['name']}</option>";
								}
								?>
							</select>
						</td>
						<td><input type='submit' name='search_filter'></td>
					</tr>
				</table>
			</form>
<br>		
<?php	
try {
	$data=array("gibbonSchoolYearID"=>$filterYear);
	$sql="SELECT gibbonperson.gibbonPersonID, status, gibbonStudentEnrolmentID, surname, preferredName,officialName,gibbonstudentenrolment.gibbonYearGroupID, gibbonperson.boarder,`gibbonperson`.`phone1`,`gibbonperson`.`admission_number`,`gibbonperson`.`enrollment_date`, gibbonyeargroup.name AS yearGroup, gibbonrollgroup.nameShort AS rollGroup,`gibbonstudentenrolment`.`gibbonRollGroupID`,account_number,gibbonstudentenrolment.rollOrder FROM gibbonperson, gibbonstudentenrolment, gibbonyeargroup, gibbonrollgroup WHERE (gibbonperson.gibbonPersonID=gibbonstudentenrolment.gibbonPersonID) AND (gibbonstudentenrolment.gibbonYearGroupID=gibbonyeargroup.gibbonYearGroupID) AND (gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID) AND gibbonstudentenrolment.gibbonSchoolYearID={$filterYear} AND (dateStart IS NULL OR dateStart<='" . date("Y-m-d") . "') AND (dateEnd IS NULL  OR dateEnd>='" . date("Y-m-d") . "') AND gibbonperson.status='Full'";
	$sql.=$sqlFilter;
	$sql.=" ORDER BY  account_number" ;
	$result=$connection2->prepare($sql);
	$result->execute();
}
catch(PDOException $e) { 
	print "<div class='error'>" . $e->getMessage() . "</div>" ; 
}
			if ($result->rowcount()<1) {
				print "<div class='error'>" ;
				print _("There are no records to display.") ;
				print "</div>" ;
			}
			else {
			
				print "<table cellspacing='0' style='width: 100%' class='myTable'>" ;
				print "<thead>";
					print "<tr class='head'>" ;
					print "<th>" ;
							print _("Acc No") ;
						print "</th>" ;
						print "<th>" ;
							print _("Name") ;
						print "</th>" ;
						print "<th>" ;
							print _("Class") ;
						print "</th>" ;
						print "<th>" ;
							print _("Section") ;
						print "</th>" ;
						print "<th>" ;
							print _("Roll No") ;
						print "</th>" ;
						print "<th>" ;
							print _("Phone No") ;
						print "</th>" ;
						print "<th>" ;
							print _("Edit") ;
						print "</th>" ;
					print "</tr>" ;
				print "</thead>";
				print "<tbody>";
					
					$count=0;
					$rowNum="odd" ;
					try {
						//echo $sqlPage;
						//$resultPage=$connection2->prepare($sqlPage);
						$resultPage=$connection2->prepare($sql);
						$resultPage->execute();
					}
					catch(PDOException $e) { 
						print "<div class='error'>" . $e->getMessage() . "</div>" ; 
					}
					while ($row=$resultPage->fetch()) {
						if ($count%2==0) {
							$rowNum="even" ;
						}
						else {
							$rowNum="odd" ;
						}
						if ($row["status"]!="Full") {
							$rowNum="error" ;
						}
						$count++ ;
						//COLOR ROW BY STATUS!
						print "<tr class=$rowNum id='t_{$row['gibbonStudentEnrolmentID']}'>" ;
						print "<td id='t_Account_{$row['gibbonStudentEnrolmentID']}'>" ;
									print "<span id='Account_{$row['gibbonStudentEnrolmentID']}'>";
									print _(substr($row["account_number"], 5)) ;
									print "</span>";
								
							print "</td>" ;
							print "<td id ='t_Name_{$row['gibbonStudentEnrolmentID']}'>" ;
								print "<span id='Name_{$row['gibbonStudentEnrolmentID']}'>";
								print formatName("", $row["preferredName"],$row["surname"], "Student", true) ;
								//print $row["officialName"] ;
								print "</span>";
							print "</td>" ;
							print "<td id='t_Class_{$row['gibbonStudentEnrolmentID']}'>" ;
								print "<input type='hidden' id='ClassID_{$row['gibbonStudentEnrolmentID']}' value='{$row["gibbonYearGroupID"]}'>";
								print "<span id='Class_{$row['gibbonStudentEnrolmentID']}'>";
								print _($row["yearGroup"]) ;
								print "</span>";
							print "</td>" ;
							print "<td id='t_Section_{$row['gibbonStudentEnrolmentID']}'>" ;
								print "<span id='SectionID_{$row['gibbonStudentEnrolmentID']}' style='display: none;'>{$row["gibbonRollGroupID"]}</span>";
								print "<span id='Section_{$row['gibbonStudentEnrolmentID']}' >".SectionFormater($row["rollGroup"])."</span>" ;
							print "</td>" ;
							print "<td id='t_Roll_{$row['gibbonStudentEnrolmentID']}'>" ;
								print "<span id='Roll_{$row['gibbonStudentEnrolmentID']}'>".$row["rollOrder"]."</span>" ;
							print "</td>" ;
							print "<td id='t_Phone_{$row['gibbonStudentEnrolmentID']}'>" ;
								print "<span id='Phone_{$row['gibbonStudentEnrolmentID']}'>".$row["phone1"]."</span>" ;
							print "</td>" ;
							print "<td>" ;
								print "<span class='editRoll' id='{$row['gibbonStudentEnrolmentID']}'><img title='" . _('Edit Roll No') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/config.png'/></span>";
							print "</td>" ;
						print "</tr>" ;
					}
					print "</tbody>";
				print "</table>" ;
				
			}				
}
?>
<input type='hidden' id='updateUrl' value='<?php echo $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/short_update_process.php";?>'>
<div id='hide_body'style='background-color :rgba(0,0,0, 0.7); width:100%; height:100%; position:fixed; left:0px; top:0px; z-index:100; display:none;'>
 </div>
<div  id='modal_roll_edit' class='cModal' style="position:fixed; left:500px; top:115px; z-index:200; border:1px; padding:5px 10px; background-color :rgba(0,0,0, 0.6); color:white; width:400px; display:none;">
<div style="margin:20px;">
<input type='hidden' id='enrollID' value=''>
<span id="e_name" style="float: left"></span><br><br><br>
<input type='hidden' id='edit_name'><input type='hidden' id='edit_class'><input type='hidden' name='gibbonSchoolYearID' value='<?php echo $filterYear?>'>
<span style='float: left'>Roll No:</span> <input type='text' id='edit_roll' style='width:180px;'><br><br><br>
<span style='float: left'>Section:</span> <select id='edit_section' style="width: 180px">
								<?php
								foreach($sectionDB as $sc){
								echo "<option class='{$sc['gibbonYearGroupID']}' value='{$sc['gibbonRollGroupID']}'>{$sc['name']}</option>";
								}
								?>
							</select><br><br><br>
<span style='float: left'>Account No:</span> <input type='text' id='edit_account' style='width:180px;'><br><br><br>
<span style='float: left'>Admission No:</span> <input type='text' id='edit_admission' style='width:180px;'><br><br><br>
<span style='float: left'>Aadhar No:</span> <input type='text' id='edit_aadhar' style='width:180px;'><br><br><br>
<span style='float: left'>Phone No:</span> <input type='text' id='edit_phone' style='width:180px;'><br><br><br>
<span style='float: left'>Father's name:</span> <input type='text' id='edit_father' style='width:180px;'><br><br><br>
<span style='float: left'>Mother's name:</span> <input type='text' id='edit_mother' style='width:180px;'><br><br><br>
<span style='float: left'>Address:</span> <input type='text' id='edit_address' style='width:180px;'><br><br><br>
<center>
<button id='editRollNo' style='border:1px; padding:10px; background:#ff731b; color:white;'>Add</button>
<button class='mClose' style='border:1px; padding:10px; background:#ff731b; color:white;'>Close</button>
</center>
</div>
</div>
<div  id='messageAlert' class='cModal' style="position:fixed; left:500px; top:250px; z-index:200; border:1px; padding:5px 10px; background-color :rgba(0,0,0, 0.6); color:white; width:300px; display:none;">
<div style="margin:20px;">
<span id='message' style='font-size: 18px'> </span>
<br><br>
<button class='mClose' style='border:1px; padding:10px; background:#ff731b; color:white;'>Close</button>
</div>
</div>
<input type="hidden" id="rollGroupURL" value="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajax_change_rollgroup.php" ?>">
<script src="<?php echo $_SESSION[$guid]["absoluteURL"] ;?>/modules/User Admin/js/jquery.dataTables.min.js"></script>
 <script>
	 $(document).ready(function(){
		$('.myTable').DataTable({
			 "iDisplayLength": 50,
			"oLanguage": {
			  "sLengthMenu": '<select>'+
				'<option value="50">50</option>'+
				'<option value="100">100</option>'+
				'<option value="200">200</option>'+
				'<option value="300">300</option>'+
				'<option value="400">400</option>'+
				'<option value="400">500</option>'+
				'<option value="-1">All</option>'+
				'</select>'
			}
		  });
		$( "#edit_edate" ).datepicker({ dateFormat: 'dd/mm/yy' });
		$("#filterClass,#filterYear").change(function(){
			var yearID=$("#filterYear").val();
			var classID=$("#filterClass").val();
			var url = $("#rollGroupURL").val();
			$.ajax({
				type: "POST",
				url : url,
				data: {schoolYear:yearID, yearGroup:classID},
				success: function(msg){
					console.log(msg);
					$("#filterSection").empty().append("<option value=''> Select Section </option>" + msg);
				}
			});
		});
	});
 </script>
 