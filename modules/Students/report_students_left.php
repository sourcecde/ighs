<?php


@session_start() ;

if (isActionAccessible($guid, $connection2, "/modules/Students/report_students_left.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
	print "<div class='trail'>" ;
		print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . _("Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . _(getModuleName($_GET["q"])) . "</a> > </div><div class='trailEnd'>" . _('Left student tracker') . "</div>" ;
	print "</div>" ;
		
	print "<p>" ;
			print _("This page shows all the students left year wise.") ;
	print "</p>" ;
	try {
		$dataSelect=array(); 
		$sqlSelect="SELECT * FROM gibbonschoolyear ORDER BY sequenceNumber" ;
		$resultSelect=$connection2->prepare($sqlSelect);
		$resultSelect->execute($dataSelect);
									
		while($year=$resultSelect->fetch()) {
				$sqlSelect1="SELECT * FROM leftstudenttracker WHERE yearOfLeaving=".$year['gibbonSchoolYearID'].";";
				$resultSelect1=$connection2->prepare($sqlSelect1);
				$resultSelect1->execute();
				if($resultSelect1->rowCount()==0)
					continue;
		print "<h3>Year of Living:<i style='color:blue;'>{$year['name']}</i></h3>";
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
						print _("T.C.") ;
					print "</th>" ;
					print "<th>" ;
						print _("T.C. Number") ;
					print "</th>" ;
					print "<th>" ;
						print _("T.C. Date") ;
					print "</th>" ;
					print "<th>" ;
						print _("Reason") ;
					print "</th>" ;
					print "<th>" ;
						print _("TC Print") ;
					print "</th>" ;
				print "</tr>" ;
				print "</thead>";
				print "<tbody>";
				$accNo=0;
				while($student=$resultSelect1->fetch()) {
					$accNo++;
					$sqlSelect2="SELECT gibbonYearGroupID,gibbonRollGroupID FROM gibbonstudentenrolment WHERE gibbonPersonID=".$student['student_id']." ;";
					$resultSelect2=$connection2->prepare($sqlSelect2);
					$resultSelect2->execute();
					$st_row=$resultSelect2->fetch(); 
					
					
					$sqlSelect3="SELECT * FROM gibbonyeargroup WHERE gibbonYearGroupID=".$st_row['gibbonYearGroupID'].";";
					$resultSelect3=$connection2->prepare($sqlSelect3);
					$resultSelect3->execute();
					$class=$resultSelect3->fetch();
					
					$sqlSelect4="SELECT * FROM gibbonrollgroup WHERE gibbonRollGroupID=".$st_row['gibbonRollGroupID'].";";
					$resultSelect4=$connection2->prepare($sqlSelect4);
					$resultSelect4->execute();
					$section=$resultSelect4->fetch();
					
					$sqlSelect5="SELECT account_number FROM gibbonperson WHERE gibbonPersonID={$student['student_id']}";
					$resultSelect5=$connection2->prepare($sqlSelect5);
					$resultSelect5->execute();
					$account_number=$resultSelect5->fetch();
					if($accNo%2==0)
						$rowNum="even";
					else
						$rowNum="odd";
				 print "<tr class=$rowNum>";
				 print "<td>".($account_number['account_number']+0)."</td>";
				 print "<td>".$student['studentName']."</td>";	
				 print "<td>".$class['name']."</td>";	
				 print "<td>".$section['name']."</td>";
				 print "<td>"; if($student['hasTc']=="N")echo "No"; else echo "Yes"; print "</td>";
				 print "<td>"; if($student['hasTc']=="N")echo "N/A"; else echo $student['TcNumber']; print "</td>";
				 print "<td>"; if($student['hasTc']=="N")echo "N/A"; else echo $student['dateOfTc']; print "</td>";
				 print "<td>".$student['leavingReason']."</td>";
				 print "<td><a href='https://calcuttapublicschool.in/lakshya/lakshya_green_an//index.php?q=/modules/Students/tc.php&studentid=".$student['student_id']."&account_number=".$account_number['account_number']."&name=".$student['studentName']."&class=".$class['name']."&year=".$year['name']."'>Print</a></td>";
				 print "</tr>";
				}
				print "</tbody>";
		print "</table>";

		}
	}
	catch(PDOException $e) { 
		print "<div class='error'> ".$e-> getMessage()."</div>" ; 
	}
}
?>
<script src="<?php echo $_SESSION[$guid]["absoluteURL"] ;?>/modules/Students/js/jquery.dataTables.min.js"></script>
 <script>
	 $(document).ready(function(){
		$('.myTable').DataTable();
	});
 </script>