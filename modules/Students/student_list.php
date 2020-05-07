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

@session_start() ;

	$_SESSION['varname'] = $_SESSION[$guid]["gibbonSchoolYearIDCurrent"];
	$filterClass="";
	$filterSection="";
	$filterYear="";
	$filterBoarder="";
	$sqlFilter="";
	// $sql="SELECT `gibbonYearGroupID`,`name` FROM `gibbonyeargroup`";
	// $result=$connection2->prepare($sql);
	// $result->execute();
	// $classDB=$result->fetchAll();
		$sql="SELECT `gibbonSchoolYearID`,`name`,`status` FROM `gibbonschoolyear`";
	$result=$connection2->prepare($sql);
	$result->execute();
	$yearDB=$result->fetchAll();
	if($_POST){
		if(isset($_REQUEST['search_filter'])){
		
			extract($_REQUEST);
			$sql="SELECT `gibbonRollGroupID`,`name` FROM `gibbonrollgroup` WHERE 1";
			// if($filterClass!=""){
			// 	$sqlFilter.=" AND gibbonstudentenrolment.gibbonYearGroupID=$filterClass ";
			// 	$sql.=" AND `gibbonYearGroupID`=$filterClass";
			// }
			// if($filterSection!="")
			// 	$sqlFilter.=" AND gibbonstudentenrolment.gibbonRollGroupID=$filterSection ";
			if($filterYear!="" && $filterSection=="" && $filterSection==""){
				$sqlFilter.=" AND gibbonstudentenrolment.gibbonSchoolYearID=$filterYear ";
				$sql.=" AND gibbonrollgroup.gibbonSchoolYearID=$filterYear ";
				$_SESSION['varname'] = $filterYear;
				//echo $_SESSION['varname'];

				$sql_drop = "DROP TABLE IF EXISTS `a_view_create`;";
				$result_drop=$connection2->prepare($sql_drop);
				$result_drop->execute();

				$sql_table = "CREATE TABLE a_view_create(SELECT a.account_number as AccNo, 
			       IFNULL(b.rollOrder,0) AS RollNo, 
			       a.preferredname StudentName, 
			       c.classgroup as Class, 
			       IF(rtrim(ltrim(c.name))<>rtrim(ltrim(c.classgroup)),rtrim(ltrim(c.name)),'General') AS Stream,
			       d.name as Section, 
			       DATE_FORMAT(a.dob,'%d/%m/%Y') AS DOB,
			       IF(gender='M','Male', 'Female') AS Gender,
			       fathername as Father,
			       phone1 as FatherMobile,
			       mothername as Mother,
			       phone2 as MotherMobile,
			       upper(a.address1) as Address,
			       upper(a.languageSecond) as SecondLanguage,
			       nationalIDCardNumber,
			       admission_number,
			       DATE_FORMAT(a.dateStart,'%d/%m/%Y') as admission_date
			  FROM gibbonperson a, 
			       gibbonstudentenrolment b, 
			       gibbonyeargroup c, 
			       gibbonrollgroup d
			 where a.gibbonpersonid=b.gibbonpersonid 
			   and b.gibbonyeargroupid=c.gibbonyeargroupid 
			   and b.gibbonrollgroupid=d.gibbonrollgroupid 
			   and b.gibbonschoolyearid=$filterYear
			   and a.gibbonpersonid not in (SELECT `student_id` from `leftstudenttracker`) 
			order by b.gibbonyeargroupid, b.gibbonrollgroupid, b.rollOrder)";
				$result_table=$connection2->prepare($sql_table);
				$result_table->execute();


				$sql="SELECT DISTINCT `Class` FROM `a_view_create`";
				$result=$connection2->prepare($sql);
				$result->execute();
				$classDB=$result->fetchAll();

			}
			// else
			// 	$sql.=" AND gibbonrollgroup.gibbonSchoolYearID=".$_SESSION[$guid]["gibbonSchoolYearIDCurrent"];
			// if($filterBoarder!="")
			// 	$sqlFilter.=" AND gibbonperson.boarder='$filterBoarder' ";
			// $result=$connection2->prepare($sql);
			// $result->execute();
			// $sectionDB=$result->fetchAll();
		}	
	}
	else

			if ($result->rowCount()<1) {
				print "<div class='error'>" ;
				print _("Access denieds.") ;
				print "</div>" ;
			}
			else {
				//Get child list
				$count=0 ;
				$options="" ;
				$students=array() ;
			}
			
			?>

			<form method="post" action="">
				<table width='100%' style='border: 2px solid #7030a0;'>
					<tr>
						<td><select name='filterYear' id='schoolYear1'>
								<option value=''>Select Year</option>
								<?php
								if(isset($yearDB) && !empty($yearDB)){
								foreach($yearDB as $y){
									if($filterYear!="")
										$s=$filterYear==$y['gibbonSchoolYearID']?"selected":"";
									else
										$s=$y['status']=='Current'?"selected":"";
								echo "<option value='{$y['gibbonSchoolYearID']}' $s>{$y['name']}</option>";
								}
								}
								?>
							</select>
						</td>
						<td><input type='submit' name='search_filter' value="Submit Year">
						</td>
						<td><b>Select Class/Section</b></td>
						<td>
						<select name='filterClass' id='filterClass1'>
								<option value=''>Select Class</option>
								<?php
								foreach($classDB as $c){
									$s=$filterClass==$c['Class']?"selected":"";
								echo "<option value='{$c['Class']}' $s>{$c['Class']}</option>";
								}
								?>
							</select>
						</td>
						<td><select name='filterSection' id='filterSection'>
								<option value=''>Select Section</option>
								<?php
								if(isset($sectionDB) && !empty($sectionDB)){
								foreach($sectionDB as $sc){
									$s=$filterSection==$sc['gibbonRollGroupID']?"selected":"";
								echo "<option value='{$sc['gibbonRollGroupID']}' $s>{$sc['name']}</option>";
								}
								}
								?>
							</select>
						</td>
						<td>
							<input type="checkbox" id="doj" name="doj" value="doj" <?=(isset($_REQUEST['doj'])?' checked':'')?>>
							<label for="doj">Date of Admission</label><br>
						</td>
						<td>
							<input type="checkbox" id="dob" name="dob" value="dob" <?=(isset($_REQUEST['dob'])?' checked':'')?>>
							<label for="dob">Date of Birth</label><br>
						</td>

						<td>
							<input type="checkbox" id="aadhar" name="aadhar" value="aadhar" <?=(isset($_REQUEST['aadhar'])?' checked':'')?>>
							<label for="aadhar">Aadhar Number</label><br>
						</td>
						</tr>
				</table>
				<table width='100%' style='border: 2px solid #7030a0;'>
					<tr>
						<td>
							<input type="checkbox" id="gender" name="gender" value="gender" <?=(isset($_REQUEST['gender'])?' checked':'')?>>
							<label for="gender">Gender</label><br>
						</td>
						<td>
							<input type="checkbox" id="father" name="father" value="father" <?=(isset($_REQUEST['father'])?' checked':'')?>>
							<label for="father">Father's Name</label><br>
						</td>
						<td>
							<input type="checkbox" id="mobile" name="mobile" value="mobile" <?=(isset($_REQUEST['mobile'])?' checked':'')?>>
							<label for="mobile">Mobile</label><br>
						</td>
						<td>
							<input type="checkbox" id="address" name="address" value="address" <?=(isset($_REQUEST['address'])?' checked':'')?>>
							<label for="address">Address</label><br>
						</td>
						<td>
							<input type="checkbox" id="admission_no" name="admission_no" value="admission_no" <?=(isset($_REQUEST['admission_no'])?' checked':'')?>>
							<label for="admission_no">Admission No</label><br>
						</td>
						<td>
							<input type="checkbox" id="lang" name="lang" value="lang" <?=(isset($_REQUEST['lang'])?' checked':'')?>>
							<label for="lang">2nd Language</label><br>
						</td>
						<td><input type='submit' name='search_filter'>
						</td>
					</tr>
				</table>
			</form>
			<!-- <input  type='button' value='Print' class='printdata' > -->
<?php 
if($filterClass !='' && $filterSection !='')
{

try {
	$data=array("gibbonSchoolYearID"=>$filterYear);
	$sql = "SELECT * FROM `a_view_create` WHERE `Class` = ".$filterClass." AND `Section` = '$filterSection'";
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
			echo "<div id='print_page'>";
				print "<table cellspacing='0' style='width: 100% border: 1px solid black;' class='myTable'>" ;
				print "<thead>";
			print "<h1 style='text-align:left;''>Student Register as on :".date('d/m/Y')."</h1>";
					print "<tr class='head'>" ;
						print "<th>" ;
							print _("Sl. No") ;
						print "</th>" ;
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
							print _("Stream") ;
						print "</th>" ;
						print "<th>" ;
							print _("Roll No") ;
						print "</th>" ;
						if(isset($_REQUEST['doj'])){
						print "<th>" ;
							print _("DOA") ;
						print "</th>" ;
						}if(isset($_REQUEST['dob'])){
						print "<th>" ;
							print _("DOB") ;
						print "</th>" ;
						}if(isset($_REQUEST['aadhar'])){
						print "<th>" ;
							print _("Aadhar No.") ;
						print "</th>" ;
						}if(isset($_REQUEST['gender'])){
						print "<th>" ;
							print _("Gender") ;
						print "</th>" ;
						}if(isset($_REQUEST['father'])){
						print "<th>" ;
							print _("Father's Name") ;
						print "</th>" ;
						}if(isset($_REQUEST['mobile'])){
						print "<th>" ;
							print _("Mobile") ;
						print "</th>" ;
						}if(isset($_REQUEST['address'])){
						print "<th>" ;
							print _("Address") ;
						print "</th>" ;
						}if(isset($_REQUEST['admission_no'])){
						print "<th>" ;
							print _("Admission No.") ;
						print "</th>" ;
						}if(isset($_REQUEST['lang'])){
						print "<th>" ;
							print _("2nd Language") ;
						print "</th>" ;
						}
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
					$i=1;
					while ($row=$resultPage->fetch()) {
						if ($count%2==0) {
							$rowNum="even" ;
						}
						else {
							$rowNum="odd" ;
						}
						// if ($row["status"]!="Full") {
						// 	$rowNum="error" ;
						// }
						$count++ ;
						//COLOR ROW BY STATUS!

						print "<tr class=$rowNum>" ;
						print "<td>" ;
									print "<span>";
									print $i++ ;
									print "</span>";
								
						print "</td>" ;
						print "<td>" ;
									print "<span>";
									print _(substr($row["AccNo"], 5)) ;
									print "</span>";
								
						print "</td>" ;
						print "<td>" ;
									print "<span>";
									print $row["StudentName"] ;
									print "</span>";
								
						print "</td>" ;
						print "<td>" ;
									print "<span>";
									print $row["Class"] ;
									print "</span>";
								
						print "</td>" ;
						print "<td>" ;
									print "<span>";
									print $row["Section"] ;
									print "</span>";
								
						print "</td>" ;
						print "<td>" ;
									print "<span>";
									print $row["Stream"] ;
									print "</span>";
								
						print "</td>" ;
						print "<td>" ;
									print "<span>";
									print $row["RollNo"] ;
									print "</span>";
								
						print "</td>" ;
						if(isset($_REQUEST['doj'])){
						print "<td>" ;
									print "<span>";
									print $row["admission_date"] ;
									print "</span>";
								
						print "</td>" ;
						}if(isset($_REQUEST['dob'])){
						print "<td>" ;
									print "<span>";
									print $row["DOB"] ;
									print "</span>";
								
						print "</td>" ;
						}if(isset($_REQUEST['aadhar'])){
						print "<td>" ;
									print "<span>";
									print $row["nationalIDCardNumber"] ;
									print "</span>";
								
						print "</td>" ;
						}if(isset($_REQUEST['gender'])){
						print "<td>" ;
									print "<span>";
									print $row["Gender"] ;
									print "</span>";
								
						print "</td>" ;
						}if(isset($_REQUEST['father'])){
						print "<td>" ;
									print "<span>";
									print $row["Father"] ;
									print "</span>";
								
						print "</td>" ;
						}if(isset($_REQUEST['mobile'])){
						print "<td>" ;
									print "<span>";
									print $row["FatherMobile"] ;
									print "</span>";
								
						print "</td>" ;
						}if(isset($_REQUEST['address'])){
						print "<td>" ;
									print "<span>";
									print $row["Address"] ;
									print "</span>";
								
						print "</td>" ;
						}if(isset($_REQUEST['admission_no'])){
						print "<td>" ;
									print "<span>";
									print $row["admission_number"] ;
									print "</span>";
								
						print "</td>" ;
						}if(isset($_REQUEST['lang'])){
						print "<td>" ;
									print "<span>";
									print $row["SecondLanguage"] ;
									print "</span>";
								
						print "</td>" ;
						}
						print "</tr>" ;
					}
					print "</tbody>";
				print "</table>" ;
			echo "</div>";	
			}
		}
?>

<input type="hidden" name="print_money_receipt_url" id="print_money_receipt_url" value="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/print_money_receipt.php" ?>">
<input type="hidden" name="rollgroup_url" id="rollgroup_url" value="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/getSection.php" ?>">
<script src="<?php echo $_SESSION[$guid]["absoluteURL"] ;?>/modules/Students/js/jquery.dataTables.min.js"></script>
 <script>
    $("#filterClass1").change(function(){
    	//alert("Hululu");
    	var yearGroup=$("#filterClass1").val();
    	//var schoolYear=$("#schoolYear1").val();
	    //alert(yearGroup);
	    var url=$("#rollgroup_url").val();
    	$.ajax({
	    	type: "POST",
	    	url: url,
	    	data: {yearGroup: yearGroup},
	    	success: function(msg)
		    {
			console.log(msg);
			$("#filterSection").empty().append("<option value=''>Select Section</option>" + msg);
	    	}
    	});
    });
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
	});
	
 </script>
 
 
 <input type="hidden" name="print_money_receipt_url" id="print_money_receipt_url" value="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/print_money_receipt.php" ?>">
<script src="<?php echo $_SESSION[$guid]["absoluteURL"] ;?>/modules/Students/js/jquery.dataTables.min.js"></script>

    <link rel="stylesheet" href="http://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.6/css/buttons.dataTables.min.css" />
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
 <script>
	$(document).ready(function() {
    $('.myTable').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            'print'
        ]
    } );
} );
 </script>
 <script type="text/javascript">
        $('.printdata').click(function() {
        //alert('aaaaa');
        var w=window.open("","","height=600,width=700,status=yes,toolbar=no,menubar=no,location=no");
        var html=$('#print_page').html();
        $(w.document.body).html(html);
        w.print();
});
  </script>