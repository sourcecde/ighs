<?php
include "../../config.php" ;
include "../../functions.php" ;
@session_start();
try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}
if($_POST){  
	extract($_POST);
	// if($action=='left'){ 
	// 	try {
	// 		$data=array(); 
	// 	   $sql="SELECT gibbonperson.gibbonPersonID,gibbonperson.dateEnd,gibbonstaff.gibbonStaffID, status, surname, preferredName, initials,	type,gibbonstaff.jobTitle, gibbonstaff.priority
	// 		FROM gibbonperson
	// 		JOIN gibbonstaff ON (gibbonstaff.gibbonPersonID=gibbonperson.gibbonPersonID)
	// 		WHERE 1 ORDER BY gibbonstaff.priority" ; 
			
 //      	$sql="SELECT * from gibbonstaff WHERE 1 ORDER BY gibbonstaff.priority" ;
	// 		$result=$connection2->prepare($sql);
	// 		$result->execute($data);
		
	// 	}
	// 	catch(PDOException $e) { 
	// 		print "<div class='error'>" . $e->getMessage() . "</div>" ; 
	// 	}
	// }
	//else if($action=='full'){
		try {
			$data=array(); 
			/*$sql="SELECT gibbonperson.gibbonPersonID,gibbonperson.dateEnd,gibbonstaff.gibbonStaffID, status, surname, preferredName, initials,	type,gibbonstaff.jobTitle, gibbonstaff.priority
			FROM gibbonperson
			JOIN gibbonstaff ON (gibbonstaff.gibbonPersonID=gibbonperson.gibbonPersonID)
			WHERE gibbonperson.status='Full'  ORDER BY gibbonstaff.priority" ; 
			*/
			
			$sql="SELECT * from gibbonstaff WHERE gibbonstaff.empty_type = 'Contract'" ;
			$result=$connection2->prepare($sql);
			$result->execute($data);
		}
		catch(PDOException $e) { 
			print "<div class='error'>" . $e->getMessage() . "</div>" ; 
		}
	//}
	
	

		if ($result->rowcount()<1) {
			print "<div class='error'>" ;
			print _("There are no records to display.") ;
			print "</div>" ;
		}
		else {
			
		
			print "<table cellspacing='0' style='width: 100%' id='myTable'>" ;
				print "<thead>" ;
				print "<tr class='head'>" ;
					print "<th style='display:none;'>" ;
						print _("Priority") ;
					print "</th>" ;
					print "<th>" ;
						print _("Staff ID") ;
					print "</th>" ;
					print "<th>" ;
						print _("Name") . "<br/>" ;
					//	print "<span style='font-size: 85%; font-style: italic'>" . _('Initials') . "</span>" ;
					print "</th>" ;
				/*	print "<th>" ;
						print _("Job Title") ;
					print "</th>" ;
				*/	
					print "<th>" ;
						print _("Actions") ;
						print "<a style='font-weight: bold;' href='".$_SESSION[$guid]["absoluteURL"]."/index.php?q=/modules/".$_SESSION[$guid]["module"]."/staff_info.php'></a> " ; 
					    print "</th>" ;
				print "</tr>" ;
				print "</thead>" ;
				print "<tbody>" ;
				$count=0;
				$rowNum="odd" ;
				try {
					$resultPage=$connection2->prepare($sql);
					$resultPage->execute($data);
				}
				catch(PDOException $e) { 
					print "<div class='error'>" . $e->getMessage() . "</div>" ; 
				}
				while ($row=$resultPage->fetch()) {
				    
				    //echo "<pre>";print_r($row);die;
				    /*$saff_type_sql="SELECT * from `staff_type` WHERE `staff_type`.gibbonStaffID=".$row['gibbonStaffID'];
            		$result1=$connection2->prepare($saff_type_sql);
            		$result1->execute();
            		$staff_type=$result1->fetch();
            		*/
				    
				    
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
					print "<tr class='$rowNum'>" ;
						print "<td style='display:none;'>" ;
							echo $row["priority"];
						print "</td>" ;
						print "<td>" ;
							echo $row["gibbonStaffID"];
						print "</td>" ;
						print "<td>" ;
							print formatName("", $row["preferredName"],@$row["surname"], "Student", true) . "<br/>" ;
						print "</td>" ;

						print "<td>" ;
					        print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/contract_employee_edit.php&gibbonStaffID=" . $row["gibbonStaffID"] . "'><img title='" . _('Edit Details') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/plus.png'/></a> " ;
					        // print "  |<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/staff_view.php&gibbonStaffID=" . $row["gibbonStaffID"] . "'><img id='delete_staff' style='height: 7%' title='" . _('Delete') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/delete-button.jpg'/></a>" ;
					        
						print "</td>" ;
					print "</tr>" ;
				}
				print "</tbody>" ;
			print "</table>" ;
		
		}
}


