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

if (isActionAccessible($guid, $connection2, "/modules/Students/student_view.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
	/*$sql="SELECT DISTINCT `border`,`border_type_name` FROM `fee_boarder_class`";
	$result=$connection2->prepare($sql);
	$result->execute();
	$boarderDB=$result->fetchAll();
	$boarderDetails=array();
	foreach($boarderDB as $b){
		$boarderDetails[$b['border']]=$b['border_type_name'];
	}*/
	$_SESSION['varname'] = $_SESSION[$guid]["gibbonSchoolYearIDCurrent"];
	$filterClass="";
	$filterSection="";
	$filterYear="";
	$filterBoarder="";
	$sqlFilter="";
	$sql="SELECT `gibbonYearGroupID`,`name` FROM `gibbonyeargroup`";
	$result=$connection2->prepare($sql);
	$result->execute();
	$classDB=$result->fetchAll();
		$sql="SELECT `gibbonSchoolYearID`,`name`,`status` FROM `gibbonschoolyear`";
	$result=$connection2->prepare($sql);
	$result->execute();
	$yearDB=$result->fetchAll();
	if($_POST){
		if(isset($_REQUEST['search_filter'])){
		
			extract($_REQUEST);
			$sql="SELECT `gibbonRollGroupID`,`name` FROM `gibbonrollgroup` WHERE 1";
			if($filterClass!=""){
				$sqlFilter.=" AND gibbonstudentenrolment.gibbonYearGroupID=$filterClass ";
				$sql.=" AND `gibbonYearGroupID`=$filterClass";
			}
			if($filterSection!="")
				$sqlFilter.=" AND gibbonstudentenrolment.gibbonRollGroupID=$filterSection ";
			if($filterYear!=""){
				$sqlFilter.=" AND gibbonstudentenrolment.gibbonSchoolYearID=$filterYear ";
				$sql.=" AND gibbonrollgroup.gibbonSchoolYearID=$filterYear ";
				$_SESSION['varname'] = $filterYear;
				//echo $_SESSION['varname'];
			}
			else
				$sql.=" AND gibbonrollgroup.gibbonSchoolYearID=".$_SESSION[$guid]["gibbonSchoolYearIDCurrent"];
			if($filterBoarder!="")
				$sqlFilter.=" AND gibbonperson.boarder='$filterBoarder' ";
			$result=$connection2->prepare($sql);
			$result->execute();
			$sectionDB=$result->fetchAll();
			//echo "<br>$sqlFilter<br>";
		}	
	}
	else
		$sqlFilter.=" AND gibbonstudentenrolment.gibbonSchoolYearID=".$_SESSION[$guid]["gibbonSchoolYearIDCurrent"];
	
	
	//Get action with highest precendence
	$highestAction=getHighestGroupedAction($guid, $_GET["q"], $connection2) ;
	if ($highestAction==FALSE) {
		print "<div class='error'>" ;
		print _("The highest grouped action cannot be determined.") ;
		print "</div>" ;
	}
	else {
		if ($highestAction=="View Student Profile_myChildren") {
			print "<div class='trail'>" ;
			print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . _("Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . _(getModuleName($_GET["q"])) . "</a> > </div><div class='trailEnd'>" . _('View Student Profiles') . "</div>" ;
			print "</div>" ;
				
			//Test data access field for permission
			try {
				$data=array("gibbonPersonID"=>$_SESSION[$guid]["gibbonPersonID"]); 
				$sql="SELECT * FROM gibbonfamilyadult WHERE gibbonPersonID=:gibbonPersonID AND childDataAccess='Y'" ;
				$result=$connection2->prepare($sql);
				$result->execute($data);
			}
			catch(PDOException $e) { 
				print "<div class='error'>" . $e->getMessage() . "</div>" ; 
			}
			if ($result->rowCount()<1) {
				print "<div class='error'>" ;
				print _("Access denied.") ;
				print "</div>" ;
			}
			else {
				//Get child list
				$count=0 ;
				$options="" ;
				$students=array() ;
				while ($row=$result->fetch()) {
					try {
						$dataChild=array("gibbonFamilyID"=>$row["gibbonFamilyID"]); 
						$sqlChild="SELECT gibbonperson.gibbonPersonID,phone1, surname, gibbonperson.preferredName,officialName,gibbonperson.boarder gibbonyeargroup.nameShort AS yearGroup, gibbonrollgroup.nameShort AS rollGroup,gibbonperson.account_number,gibbonstudentenrolment.rollOrder FROM gibbonfamilychild JOIN gibbonperson ON (gibbonfamilychild.gibbonPersonID=gibbonperson.gibbonPersonID) JOIN gibbonstudentenrolment ON (gibbonperson.gibbonPersonID=gibbonstudentenrolment.gibbonPersonID) JOIN gibbonyeargroup ON (gibbonstudentenrolment.gibbonYearGroupID=gibbonyeargroup.gibbonYearGroupID) JOIN gibbonrollgroup ON (gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID) WHERE gibbonFamilyID=:gibbonFamilyID AND gibbonperson.status='Full' AND (dateStart IS NULL OR dateStart<='" . date("Y-m-d") . "') AND (dateEnd IS NULL  OR dateEnd>='" . date("Y-m-d") . "') AND gibbonstudentenrolment.gibbonSchoolYearID=" . $_SESSION[$guid]["gibbonSchoolYearID"] . " ORDER BY  account_number " ;
						
						$resultChild=$connection2->prepare($sqlChild);
						$resultChild->execute($dataChild);
					}
					catch(PDOException $e) { 
						print "<div class='error'>" . $e->getMessage() . "</div>" ; 
					}
					while ($rowChild=$resultChild->fetch()) {
						$students[$count][0]=$rowChild["surname"] ;
						$students[$count][1]=$rowChild["preferredName"] ;
						$students[$count][2]=$rowChild["yearGroup"] ;
						$students[$count][3]=$rowChild["rollGroup"] ;
						$students[$count][4]=$rowChild["gibbonPersonID"] ;
						$students[$count][5]=$rowChild["officialName"] ;
						$students[$count][6]=$rowChild["account_number"] ;
						$students[$count][7]=$rowChild["rollOrder"] ;
						$students[$count][8]=$rowChild["phone1"] ;
						$count++ ;
					}
				}
				
				if ($count==0) {
					print "<div class='error'>" ;
					print _("Access denied.") ;
					print "</div>" ;
				}
				else {
					print "<table cellspacing='0' style='width: 100%'>" ;
						print "<tr class='head'>" ;
							print "<th>" ;
								print _("Sl No.") ;
							print "</th>" ;

						    print "<th>" ;
								print _("Account No") ;
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
								print _("Actions") ;
							print "</th>" ;
						print "</tr>" ;
						$slno=1;
						for ($i=0;$i<$count;$i++) {
							if ($i%2==0) {
								$rowNum="even" ;
							}
							else {
								$rowNum="odd" ;
							}
							
							//COLOR ROW BY STATUS!
							print "<tr class=$rowNum>" ;
								print "<td>" ;
									print $slno++ ;
								print "</td>" ;
							    print "<td>" ;
									print _($students[$i][6]) ;
								print "</td>" ;
								print "<td>" ;
									print formatName("", $students[$i][1], $students[$i][0], "Student", true) ;
									//print $students[$i][0] ;
								print "</td>" ;
								print "<td>" ;
									print _($students[$i][2]) ;
								print "</td>" ;
								print "<td>" ;
									print SectionFormater($students[$i][3]) ;
								print "</td>" ;
								print "<td>" ;
									print $a=$students[$i][7]=='0'?"":$students[$i][7] ;
								print "</td>" ;
								print "<td>" ;
									print $students[$i][8] ;
								print "</td>" ;
								print "<td>" ;
									print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/student_view_details.php&gibbonPersonID=" . $students[$i][4] . "'><img title='" . _('View Details') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/plus.png'/></a> " ;
								print "</td>" ;
							print "</tr>" ;
						}
					print "</table>" ;
				}
			}
		}
		else {
			//Proceed!
			print "<div class='trail'>" ;
			print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . _("Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . _(getModuleName($_GET["q"])) . "</a> > </div><div class='trailEnd'>" . _('View Student Profiles') . "</div>" ;
			print "</div>" ;
			
			
			$gibbonPersonID=NULL;
			if (isset($_GET["gibbonPersonID"])) {
				$gibbonPersonID=$_GET["gibbonPersonID"] ;
			}
			$search=NULL;
			if (isset($_GET["search"])) {
				$search=$_GET["search"] ;
			}
			$allStudents="" ;
			if (isset($_GET["allStudents"])) {
				$allStudents=$_GET["allStudents"] ;
			}
			
			?>
			<!--
			<form method="get" action="<?php print $_SESSION[$guid]["absoluteURL"]?>/index.php">
				<table class='noIntBorder' cellspacing='0' style="width: 100%">	
					<tr><td style="width: 30%"></td><td></td></tr>
					<tr>
						<td> 
							<b><?php print _('Search For') ?></b><br/>
							<span style="font-size: 90%"><i><?php print _('Preferred, surname, username.') ?></i></span>
						</td>
						<td class="right">
							<input name="search" id="search" maxlength=20 value="<?php print $search ?>" type="text" style="width: 300px">
						</td>
					</tr>
					<?php if ($highestAction=="View Student Profile_full") { ?>
						<tr>
							<td> 
								<b><?php print _('All Students') ?></b><br/>
								<span style="font-size: 90%"><i><?php print _('Include all students, regardless of status and current enrolment. Some data may not display.') ?></i></span>
							</td>
							<td class="right">
								<?php
								$checked="" ;
								if ($allStudents=="on") {
									$checked="checked" ;
								}
								print "<input $checked name=\"allStudents\" id=\"allStudents\" type=\"checkbox\">" ;
								?>
							</td>
						</tr>
					<?php } ?>
					<tr>
						<td colspan=2 class="right">
							<input type="hidden" name="q" value="/modules/<?php print $_SESSION[$guid]["module"] ?>/student_view.php">
							<input type="hidden" name="address" value="<?php print $_SESSION[$guid]["address"] ?>">
							<?php
							print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/student_view.php'>" . _('Clear Search') . "</a>" ;
							?>
							<input type="submit" value="<?php print _("Submit") ; ?>">
						</td>
					</tr>
				</table>
			</form>
			-->
			<form method="post" action="">
				<table width='100%' style='border: 2px solid #7030a0;'>
					<tr>
						<td><b>Select Class/Section</b></td>
						<td>
						<select name='filterClass' id='filterClass1'>
								<option value=''>Select Class</option>
								<?php
								foreach($classDB as $c){
									$s=$filterClass==$c['gibbonYearGroupID']?"selected":"";
								echo "<option value='{$c['gibbonYearGroupID']}' $s>{$c['name']}</option>";
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
						<td><input type='submit' name='search_filter'>
						<!--<button onclick='print_page()'>Print</button>--></td>
					</tr>
				</table>
			</form>
			<?php
		
			
			//Set pagination variable
			$page=1 ; if (isset($_GET["page"])) { $page=$_GET["page"] ; }
			if ((!is_numeric($page)) OR $page<1) {
				$page=1 ;
			}
			
			try {
				if ($allStudents!="on") {
					$data=array("gibbonSchoolYearID"=>$_SESSION[$guid]["gibbonSchoolYearID"]); 
					$sql="SELECT gibbonperson.gibbonPersonID,phone1, status, gibbonStudentEnrolmentID, surname, gibbonperson.preferredName,officialName, gibbonperson.boarder, gibbonyeargroup.nameShort AS yearGroup, gibbonrollgroup.nameShort AS rollGroup,account_number,gibbonstudentenrolment.rollOrder FROM gibbonperson, gibbonstudentenrolment, gibbonyeargroup, gibbonrollgroup WHERE (gibbonperson.gibbonPersonID=gibbonstudentenrolment.gibbonPersonID) AND (gibbonstudentenrolment.gibbonYearGroupID=gibbonyeargroup.gibbonYearGroupID) AND (gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID) AND (dateStart IS NULL OR dateStart<='" . date("Y-m-d") . "') AND (dateEnd IS NULL  OR dateEnd>='" . date("Y-m-d") . "') AND gibbonperson.status='Full'";
					$sql.=$sqlFilter;
					$sql.=" ORDER BY  account_number" ;
					
					if ($search!="") {
						$data=array("gibbonSchoolYearID"=>$_SESSION[$guid]["gibbonSchoolYearID"], "search1"=>"%$search%", "search2"=>"%$search%", "search3"=>"%$search%"); 
						$sql="SELECT gibbonperson.gibbonPersonID,phone1, status, gibbonStudentEnrolmentID, surname, gibbonperson.preferredName,officialName, gibbonperson.boarder, gibbonyeargroup.nameShort AS yearGroup, gibbonrollgroup.nameShort AS rollGroup,account_number,gibbonstudentenrolment.rollOrder FROM gibbonperson, gibbonstudentenrolment, gibbonyeargroup, gibbonrollgroup WHERE (gibbonperson.gibbonPersonID=gibbonstudentenrolment.gibbonPersonID) AND (gibbonstudentenrolment.gibbonYearGroupID=gibbonyeargroup.gibbonYearGroupID) AND (gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID) AND (gibbonperson.preferredName LIKE :search1 OR surname LIKE :search2 OR username LIKE :search3) AND (dateStart IS NULL OR dateStart<='" . date("Y-m-d") . "') AND (dateEnd IS NULL  OR dateEnd>='" . date("Y-m-d") . "') AND gibbonperson.status='Full'";
						$sql.=$sqlFilter;
						$sql.=" ORDER BY  account_number" ; 
					}
				}
				else {
					$data=array(); 
					$sql="SELECT DISTINCT gibbonperson.gibbonPersonID, phone1,status, surname, gibbonperson.preferredName,officialName,gibbonperson.boarder, NULL AS yearGroup, NULL AS rollGroup,account_number,gibbonstudentenrolment.rollOrder FROM gibbonperson, gibbonstudentenrolment WHERE (gibbonperson.gibbonPersonID=gibbonstudentenrolment.gibbonPersonID) ORDER BY  account_number" ; 
					if ($search!="") {
						$data=array("search1"=>"%$search%", "search2"=>"%$search%", "search3"=>"%$search%"); 
						$sql="SELECT DISTINCT gibbonperson.gibbonPersonID,phone1, status, surname, gibbonperson.preferredName, NULL AS yearGroup, NULL AS rollGroup,account_number,gibbonstudentenrolment.rollOrder FROM gibbonperson, gibbonstudentenrolment WHERE (gibbonperson.gibbonPersonID=gibbonstudentenrolment.gibbonPersonID) AND (gibbonperson.preferredName LIKE :search1 OR surname LIKE :search2 OR username LIKE :search3) ";
						$sql.=$sqlFilter;
						$sql.="ORDER BY  account_number" ; 
					}
				}
				$sqlPage=$sql . " LIMIT " . $_SESSION[$guid]["pagination"] . " OFFSET " . (($page-1)*$_SESSION[$guid]["pagination"]) ;
				//echo $sql;
				$result=$connection2->prepare($sql);
				$result->execute($data);
				
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
				/* if ($result->rowcount()>$_SESSION[$guid]["pagination"]) {
					printPagination($guid, $result->rowcount(), $page, $_SESSION[$guid]["pagination"], "top", "&search=$search&allStudents=$allStudents") ;
				} */
                print "<div id='printable'>";
				print "<table cellspacing='0' style='width: 100%' class='myTable' id='myTable'>" ;
				print "<thead>";
					print "<tr class='head'>" ;
						print "<th>" ;
							print _("Sl No") ;
						print "</th>" ;
					
						print "<th>" ;
							print _("Account No") ;
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
						print "<th class='hide'>" ;
							print _("Actions") ;
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
						$resultPage->execute($data);
					}
					catch(PDOException $e) { 
						print "<div class='error'>" . $e->getMessage() . "</div>" ; 
					}
					$slno=1;
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
						print "<tr class=$rowNum>" ;
							print "<td>" ;
								print $slno++ ;
							print "</td>" ;

							print "<td>" ;
								print _(substr($row["account_number"], 5)) ;
							print "</td>" ;
							print "<td>" ;
								 print formatName("", $row["preferredName"],$row["surname"], "Student", true);  
								 //print $row["officialName"];
							print "</td>" ;
							print "<td>" ;
								if ($row["yearGroup"]!="") {
									print _($row["yearGroup"]) ;
								}
							print "</td>" ;
							print "<td>" ;
								print SectionFormater($row["rollGroup"]) ;
							print "</td>" ;
							print "<td>" ;
								print $a=$row["rollOrder"]=='0'?"":$row["rollOrder"] ;
							print "</td>" ;
							print "<td>" ;
								print $row["phone1"] ;
							print "</td>" ;
							print "<td class='hide'>" ;
							$yearID=$filterYear!=""?$filterYear:$_SESSION[$guid]["gibbonSchoolYearIDCurrent"];
								print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/student_edit.php&gibbonPersonID=" . $row["gibbonPersonID"]."&gibbonSchoolYearID=".$yearID."'><img title='" . _('Edit Details') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/config.png'/></a>";
								print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/student_view_details.php&gibbonPersonID=" . $row["gibbonPersonID"] . "&search=$search&allStudents=$allStudents'><img title='" . _('View Details') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/plus.png'/></a>";
								//<a href='javascript:void(0)' class='print_receipt' id='print_receipt_".$row["gibbonPersonID"]."'><img title='" . _('Print Receipt') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/print.png'/></a> " ;
							print "</td>" ;
						print "</tr>" ;
					}
					print "</tbody>";
				print "</table>" ;
				print "</div>";
				
				/*if ($result->rowcount()>$_SESSION[$guid]["pagination"]) {
					printPagination($guid, $result->rowcount(), $page, $_SESSION[$guid]["pagination"], "bottom", "search=$search") ;
				} */
			}
		}
	}
}


?>

<input type="hidden" name="print_money_receipt_url" id="print_money_receipt_url" value="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/print_money_receipt.php" ?>">
<input type="hidden" name="rollgroup_url" id="rollgroup_url" value="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajax_change_rollgroup.php" ?>">
<script src="<?php echo $_SESSION[$guid]["absoluteURL"] ;?>/modules/Students/js/jquery.dataTables.min.js"></script>
 <script>
    $("#filterClass1,#schoolYear1").change(function(){
    	//alert("Hululu");
    	var yearGroup=$("#filterClass1").val();
    	var schoolYear=$("#schoolYear1").val();
	    var url=$("#rollgroup_url").val();
    	$.ajax({
	    	type: "POST",
	    	url: url,
	    	data: {yearGroup: yearGroup, schoolYear: schoolYear},
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
	function print_page(){
	var mywindow = window.open('', 'PRINT', 'height=400,width=600');

    mywindow.document.write('<html><head><title>' + document.title  + '</title>');
    mywindow.document.write('</head><body >');
	mywindow.document.write("<h1 style='text-align:center;'>Calcutta Public School</h1>");
	mywindow.document.write("<h4 style='text-align:center;'>AshwiniNagar, Baguiati, Kolkata-159</h4>");
    mywindow.document.write(document.getElementById("printable").innerHTML);
	mywindow.document.write('<style>td,th{border:1px solid #000}.hide{display:none;}</style>');
    mywindow.document.write('</body></html>');

    mywindow.document.close(); // necessary for IE >= 10
    mywindow.focus(); // necessary for IE >= 10*/

    mywindow.print();
    mywindow.close();
	}
	
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