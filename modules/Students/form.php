<?php


@session_start() ;
	try {
		
		        							
		
				$sqlSelect1="SELECT * FROM `lakshya_online_payment_reference`";
				$resultSelect1=$connection2->prepare($sqlSelect1);
				$resultSelect1->execute();
				if($resultSelect1->rowCount()==0)
					continue;
		print "<table cellspacing='0' style='width: 100%; overflow-y:scroll; overflow-x:scroll' class='myTable'>" ;
				print "<thead>";
				print "<tr class='head'>" ;
					print "<th>" ;
						print _("Order Id") ;
					print "</th>" ;
					print "<th>" ;
						print _("Tracking ID") ;
					print "</th>" ;
					print "<th>" ;
						print _("Bank Reference Number") ;
					print "</th>" ;
					print "<th>" ;
						print _("Status") ;
					print "</th>" ;
					print "<th>" ;
						print _("Name") ;
					print "</th>" ;
					print "<th>" ;
						print _("Time") ;
					print "</th>" ;
					print "<th>" ;
						print _("Amount") ;
					print "</th>" ;
					print "<th>" ;
						print _("Paid Amount") ;
					print "</th>" ;
					print "<th>" ;
						print _("Year") ;
					print "</th>" ;
					print "<th>" ;
						print _("Months") ;
					print "</th>" ;
					print "<th>" ;
						print _("Fine") ;
					print "</th>" ;
					print "<th>" ;
						print _("Payment Master ID") ;
					print "</th>" ;
				print "</tr>" ;
				print "</thead>";
				print "<tbody>";
				$accNo=0;
				while($student=$resultSelect1->fetch()) {
					$sqlSelect2="SELECT * FROM `gibbonperson` WHERE gibbonPersonID=".$student['personId']." ;";
					$resultSelect2=$connection2->prepare($sqlSelect2);
					$resultSelect2->execute();
					$st_row=$resultSelect2->fetch(); 
					$name=$st_row['officialName'];
					
					$sqlSelect3="SELECT * FROM gibbonschoolyear WHERE gibbonSchoolYearID=".$student['yearId'].";";
					$resultSelect3=$connection2->prepare($sqlSelect3);
					$resultSelect3->execute();
					$class=$resultSelect3->fetch();
					$year=$class['name'];
				 print "<tr>";
				 print "<td>".$student['order_id']."</td>";
				 print "<td>".$student['tracking_id']."</td>";	
				 print "<td>".$student['bank_ref_number']."</td>";	
				 print "<td>".$student['status']."</td>";
				 print "<td>".$name."</td>";
				 print "<td>".$student['Time']."</td>";
				 print "<td>".$student['amount']."</td>";
				 print "<td>".$student['paidAmount']."</td>";
				 print "<td>".$year."</td>";
				 print "<td>".$student['months']."</td>";
				 print "<td>".$student['fine']."</td>";
				 print "<td>".$student['payment_master_id']."</td>";
				 print "</tr>";
				}
				print "</tbody>";
		print "</table>";

		
	}
	catch(PDOException $e) { 
		print "<div class='error'> ".$e-> getMessage()."</div>" ; 
	}

?>
<script src="<?php echo $_SESSION[$guid]["absoluteURL"] ;?>/modules/Students/js/jquery.dataTables.min.js"></script>
 <script>
	 $(document).ready(function(){
		$('.myTable').DataTable();
	});
 </script>