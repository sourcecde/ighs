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

//Module includes
include "./modules/" . $_SESSION[$guid]["module"] . "/moduleFunctions.php" ;


if (isActionAccessible($guid, $connection2, "/modules/User Admin/data_finance_edit.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
	//Proceed!
	print "<div class='trail'>" ;
	print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . _("Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . _(getModuleName($_GET["q"])) . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/User Admin/data_finance.php'>" . _('Finance Data Updates') . "</a> > </div><div class='trailEnd'>". _('Edit Request') . "</div>" ;
	print "</div>" ;
	
	//Check if school year specified
	$gibbonFinanceInvoiceeUpdateID=$_GET["gibbonFinanceInvoiceeUpdateID"];
	if ($gibbonFinanceInvoiceeUpdateID=="Y") {
		print "<div class='error'>" ;
			print _("You have not specified one or more required parameters.") ;
		print "</div>" ;
	}
	else {
		try {
			$data=array("gibbonFinanceInvoiceeUpdateID"=>$gibbonFinanceInvoiceeUpdateID); 
			$sql="SELECT gibbonfinanceinvoiceeupdate.gibbonFinanceInvoiceeID, gibbonfinanceinvoicee.invoiceTo AS invoiceTo, gibbonfinanceinvoicee.companyName AS companyName, gibbonfinanceinvoicee.companyContact AS companyContact, gibbonfinanceinvoicee.companyAddress AS companyAddress, gibbonfinanceinvoicee.companyEmail AS companyEmail, gibbonfinanceinvoicee.companyCCFamily AS companyCCFamily, gibbonfinanceinvoicee.companyPhone AS companyPhone, gibbonfinanceinvoicee.companyAll AS companyAll, gibbonfinanceinvoicee.gibbonFinanceFeeCategoryIDList AS gibbonFinanceFeeCategoryIDList, gibbonfinanceinvoiceeupdate.invoiceTo AS newinvoiceTo, gibbonfinanceinvoiceeupdate.companyName AS newcompanyName, gibbonfinanceinvoiceeupdate.companyContact AS newcompanyContact, gibbonfinanceinvoiceeupdate.companyAddress AS newcompanyAddress, gibbonfinanceinvoiceeupdate.companyEmail AS newcompanyEmail, gibbonfinanceinvoiceeupdate.companyCCFamily AS newcompanyCCFamily, gibbonfinanceinvoiceeupdate.companyPhone AS newcompanyPhone, gibbonfinanceinvoiceeupdate.companyAll AS newcompanyAll, gibbonfinanceinvoiceeupdate.gibbonFinanceFeeCategoryIDList AS newgibbonFinanceFeeCategoryIDList FROM gibbonfinanceinvoiceeupdate JOIN gibbonfinanceinvoicee ON (gibbonfinanceinvoiceeupdate.gibbonFinanceInvoiceeID=gibbonfinanceinvoicee.gibbonFinanceInvoiceeID) WHERE gibbonFinanceInvoiceeUpdateID=:gibbonFinanceInvoiceeUpdateID" ;
			$result=$connection2->prepare($sql);
			$result->execute($data);
		}
		catch(PDOException $e) { 
			print "<div class='error'>" . $e->getMessage() . "</div>" ; 
		}

		if ($result->rowCount()!=1) {
			print "<div class='error'>" ;
				print _("The selected record does not exist, or you do not have access to it.") ;
			print "</div>" ;
		}
		else {
			if (isset($_GET["updateReturn"])) { $updateReturn=$_GET["updateReturn"] ; } else { $updateReturn="" ; }
			$updateReturnMessage="" ;
			$class="error" ;
			if (!($updateReturn=="")) {
				if ($updateReturn=="fail0") {
					$updateReturnMessage=_("Your request failed because you do not have access to this action.") ;	
				}
				else if ($updateReturn=="fail1") {
					$updateReturnMessage=_("Your request failed because your inputs were invalid.") ;	
				}
				else if ($updateReturn=="fail2") {
					$updateReturnMessage=_("Your request failed due to a database error.") ;	
				}
				else if ($updateReturn=="fail3") {
					$updateReturnMessage=_("Your request failed because your inputs were invalid.") ;	
				}
				else if ($updateReturn=="success1") {
					$updateReturnMessage=_("Your request was completed successfully, but status could not be updated.") ;	
				}
				else if ($updateReturn=="success0") {
					$updateReturnMessage=_("Your request was completed successfully.") ;	
					$class="success" ;
				}
				print "<div class='$class'>" ;
					print $updateReturnMessage;
				print "</div>" ;
			} 

			//Let's go!
			$row=$result->fetch() ;
			?>
			<form method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/data_finance_editProcess.php?gibbonFinanceInvoiceeUpdateID=$gibbonFinanceInvoiceeUpdateID" ?>">
				<?php
				print "<table cellspacing='0' style='width: 100%'>" ;
					print "<tr class='head'>" ;
						print "<th>" ;
							print _("Field") ;
						print "</th>" ;
						print "<th>" ;
							print _("Current Value") ;
						print "</th>" ;
						print "<th>" ;
							print _("New Value") ;
						print "</th>" ;
						print "<th>" ;
							print _("Accept") ;
						print "</th>" ;
					print "</tr>" ;
					
					$rowNum="even" ;
						
					//COLOR ROW BY STATUS!
					print "<tr class='odd'>" ;
						print "<td>" ;
							print _("Invoice To") ;
						print "</td>" ;
						print "<td>" ;
							print $row["invoiceTo"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["invoiceTo"]!=$row["newinvoiceTo"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newinvoiceTo"] ;
						print "</td>" ;
						print "<td>" ;
							if ($row["invoiceTo"]!=$row["newinvoiceTo"]) { print "<input checked type='checkbox' name='newinvoiceToOn'><input name='newinvoiceTo' type='hidden' value='" . htmlprep($row["newinvoiceTo"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					print "<tr class='even'>" ;
						print "<td>" ;
							print _("Company Name") ;
						print "</td>" ;
						print "<td>" ;
							print $row["companyName"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["companyName"]!=$row["newcompanyName"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newcompanyName"] ;
						print "</td>" ;
						print "<td>" ;
							if ($row["companyName"]!=$row["newcompanyName"]) { print "<input checked type='checkbox' name='newcompanyNameOn'><input name='newcompanyName' type='hidden' value='" . htmlprep($row["newcompanyName"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					print "<tr class='odd'>" ;
						print "<td>" ;
							print _("Company Contact Person") ;
						print "</td>" ;
						print "<td>" ;
							print $row["companyContact"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["companyContact"]!=$row["newcompanyContact"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newcompanyContact"] ;
							print "</span>" ;
						print "</td>" ;
						print "<td>" ;
							if ($row["companyContact"]!=$row["newcompanyContact"]) { print "<input checked type='checkbox' name='newcompanyContactOn'><input name='newcompanyContact' type='hidden' value='" . htmlprep($row["newcompanyContact"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					print "<tr class='even'>" ;
						print "<td>" ;
							print _("Company Address") ;
						print "</td>" ;
						print "<td>" ;
							print $row["companyAddress"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["companyAddress"]!=$row["newcompanyAddress"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newcompanyAddress"] ;
							print "</span>" ;
						print "</td>" ;
						print "<td>" ;
							if ($row["companyAddress"]!=$row["newcompanyAddress"]) { print "<input checked type='checkbox' name='newcompanyAddressOn'><input name='newcompanyAddress' type='hidden' value='" . htmlprep($row["newcompanyAddress"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					print "<tr class='odd'>" ;
						print "<td>" ;
							print _("Company Email") ;
						print "</td>" ;
						print "<td>" ;
							print $row["companyEmail"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["companyEmail"]!=$row["newcompanyEmail"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newcompanyEmail"] ;
							print "</span>" ;
						print "</td>" ;
						print "<td>" ;
							if ($row["companyEmail"]!=$row["newcompanyEmail"]) { print "<input checked type='checkbox' name='newcompanyEmailOn'><input name='newcompanyEmail' type='hidden' value='" . htmlprep($row["newcompanyEmail"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					print "<tr class='even'>" ;
						print "<td>" ;
							print _("CC Family?") ;
						print "</td>" ;
						print "<td>" ;
							print $row["companyCCFamily"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["companyCCFamily"]!=$row["newcompanyCCFamily"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newcompanyCCFamily"] ;
							print "</span>" ;
						print "</td>" ;
						print "<td>" ;
							if ($row["companyCCFamily"]!=$row["newcompanyCCFamily"]) { print "<input checked type='checkbox' name='newcompanyCCFamilyOn'><input name='newcompanyCCFamily' type='hidden' value='" . htmlprep($row["newcompanyCCFamily"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					print "<tr class='odd'>" ;
						print "<td>" ;
							print _("Company Phone") ;
						print "</td>" ;
						print "<td>" ;
							print $row["companyPhone"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["companyPhone"]!=$row["newcompanyPhone"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newcompanyPhone"] ;
							print "</span>" ;
						print "</td>" ;
						print "<td>" ;
							if ($row["companyPhone"]!=$row["newcompanyPhone"]) { print "<input checked type='checkbox' name='newcompanyPhoneOn'><input name='newcompanyPhone' type='hidden' value='" . htmlprep($row["newcompanyPhone"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					print "<tr class='even'>" ;
						print "<td>" ;
							print _("Company All?") ;
						print "</td>" ;
						print "<td>" ;
							print $row["companyAll"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["companyAll"]!=$row["newcompanyAll"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newcompanyAll"] ;
							print "</span>" ;
						print "</td>" ;
						print "<td>" ;
							if ($row["companyAll"]!=$row["newcompanyAll"]) { print "<input checked type='checkbox' name='newcompanyAllOn'><input name='newcompanyAll' type='hidden' value='" . htmlprep($row["newcompanyAll"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;
					print "<tr class='odd'>" ;
						print "<td>" ;
							print _("Company Fee Categories") ;
						print "</td>" ;
						print "<td>" ;
							print $row["gibbonFinanceFeeCategoryIDList"] ;
						print "</td>" ;
						print "<td>" ;
							$style="" ;
							if ($row["gibbonFinanceFeeCategoryIDList"]!=$row["newgibbonFinanceFeeCategoryIDList"]) {
								$style="style='color: #ff0000'" ;
							}
							print "<span $style>" ;
							print $row["newgibbonFinanceFeeCategoryIDList"] ;
							print "</span>" ;
						print "</td>" ;
						print "<td>" ;
							if ($row["gibbonFinanceFeeCategoryIDList"]!=$row["newgibbonFinanceFeeCategoryIDList"]) { print "<input checked type='checkbox' name='newgibbonFinanceFeeCategoryIDListOn'><input name='newgibbonFinanceFeeCategoryIDList' type='hidden' value='" . htmlprep($row["newgibbonFinanceFeeCategoryIDList"]) . "'>" ; }
						print "</td>" ;
					print "</tr>" ;					
					
					print "<tr>" ;
							print "<td class='right' colspan=4>" ;
								print "<input name='gibbonFinanceInvoiceeID' type='hidden' value='" . $row["gibbonFinanceInvoiceeID"] . "'>" ;
								print "<input name='address' type='hidden' value='" . $_GET["q"] . "'>" ;
								print "<input type='submit' value='Submit'>" ;
							print "</td>" ;
						print "</tr>" ;
				print "</table>" ;
				?>
			</form>
			<?php
		}
	}
}
?>