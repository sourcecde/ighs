<?php 
@session_start() ;
$search=NULL;
$vouchernumber='';
$payemntmode='';
$student_id=0;
$startdate='';
$enddate='';
$sql="SELECT gibbonstudentenrolment.*,gibbonperson.officialName,gibbonperson.firstName,gibbonperson.surname,gibbonyeargroup.name FROM gibbonstudentenrolment LEFT JOIN gibbonperson ON 
gibbonstudentenrolment.gibbonPersonId=gibbonperson.gibbonPersonId LEFT JOIN gibbonyeargroup ON 
gibbonstudentenrolment.gibbonYearGroupId=gibbonyeargroup.gibbonYearGroupId";
$result=$connection2->prepare($sql);
$result->execute();
$dboutbut=$result->fetchAll();

			if (isset($_GET["search"])) {
				$search=$_GET["search"] ;
			}
			//Set pagination variable
			$page=1 ; if (isset($_GET["page"])) { $page=$_GET["page"] ; }
			if ((!is_numeric($page)) OR $page<1) {
				$page=1 ;
			}
			try {
					$data=array(); 
					$sql="SELECT payment_master.*,gibbonperson.officialName as officialname,gibbonyeargroup.name AS class,
gibbonrollgroup.name AS section FROM payment_master 
LEFT JOIN gibbonperson ON payment_master.gibbonPersonID=gibbonperson.gibbonPersonID
LEFT JOIN gibbonstudentenrolment ON payment_master.gibbonStudentEnrolmentID=gibbonstudentenrolment.gibbonStudentEnrolmentID
LEFT JOIN gibbonyeargroup ON gibbonstudentenrolment.gibbonYearGroupID=gibbonyeargroup.gibbonYearGroupID
LEFT JOIN gibbonrollgroup ON gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID WHERE net_total_amount!=0 " ;  
					
					if ($_POST) {
						
						if(isset($_POST['src_voucher_no']))
						{
							if($_POST['src_voucher_no']!='')
							{
								$vouchernumber=$_POST['src_voucher_no'];
								$sql.=" AND voucher_number=".$_POST['src_voucher_no'];
							}
							
						}
						if(isset($_POST['src_payment_mode']))
						{
							if($_POST['src_payment_mode']!='')
							{
								$payemntmode=$_POST['src_payment_mode'];
								$sql.=" AND payment_mode=".$_POST['src_payment_mode'];
							}
						}
						if(isset($_POST['src_student']))
						{
							if($_POST['src_student']!='')
							{
								$student_id=$_POST['src_student'];
								$sql.=" AND payment_master.gibbonStudentEnrolmentID=".$_POST['src_student'];
							}
						}
						if(isset($_POST['src_from_date']))
						{
							if($_POST['src_from_date']!='')
							{
								$startdate=$_POST['src_from_date'];
								$datearr=explode("/", $startdate);
								$sql.=" AND payment_master.payment_date>='".$datearr[2]."-".$datearr[1]."-".$datearr[0]."'";
							}
						}
						if(isset($_POST['src_to_date']))
						{
							if($_POST['src_to_date']!='')
							{
								$enddate=$_POST['src_to_date'];
								$datearr=explode("/", $enddate);
								$sql.=" AND payment_master.payment_date<='".$datearr[2]."-".$datearr[1]."-".$datearr[0]."'";
							}
						}
					}
				$sql.=" order by payment_master.payment_master_id DESC";
				$sqlPage=$sql . " LIMIT " . $_SESSION[$guid]["pagination"] . " OFFSET " . (($page-1)*$_SESSION[$guid]["pagination"]) ;
				
				$result=$connection2->prepare($sql);
				$result->execute($data);
			}
			catch(PDOException $e) { 
				print "<div class='error'>" . $e->getMessage() . "</div>" ; 
			}
			
		?>
<form name="f1" id="f1" method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/payment_list.php" ?>">
<table width="100%" cellpadding="0" cellspacing="0" border="0" class="search_table">
  <tr>
        <td>
		    <select name="src_student" id="src_student">
		    	<option value=""> Select Student </option>
		    	<?php foreach ($dboutbut as $value) { ?>
		    	<option value="<?php echo $value['gibbonStudentEnrolmentID']?>" <?php if($student_id==$value['gibbonStudentEnrolmentID']){?> selected="selected"<?php } ?>><?php echo $value['officialName']?> - <?php echo $value['name']?></option>
		    		<?php } ?>
		    </select>
    </td>

    <td>
	    <select name="src_payment_mode" id="src_payment_mode">
	    	<option value=""> Select Mode </option>
	    	<option value="cash" <?php if($payemntmode=='cash'){?> selected="selected"<?php } ?>>Cash</option>
	    	<option value="cheque" <?php if($payemntmode=='cheque'){?> selected="selected"<?php } ?>>Cheque</option>
	    	<option value="dd" <?php if($payemntmode=='dd'){?> selected="selected"<?php } ?>>Draft</option>
	    </select>
    </td>
    <td>Voucher Number</td>
    <td>
  	 <input type="text" name="src_voucher_no" id="src_voucher_no" style="width: 150px;" value="<?php echo $vouchernumber;?>">
    </td>
  </tr>
  <tr>
   <td>From <input type="text" name="src_from_date" id="src_from_date" style="width: 100px;" value="<?php echo $startdate;?>"></td>
   <td>
   	To <input type="text" name="src_to_date" id="src_to_date" style="width: 100px;" value="<?php echo $enddate;?>">
    </td>
     
  	<td>
   		
    </td>
 	 <td><input type="submit" name="submit" id="submit" value="Search" style="float: right;"></td>
  	
  </tr>
</table>
</form>

		<?php 
		if ($result->rowcount()<1) {
				print "<div class='error'>" ;
				print _("There are no records to display.") ;
				print "</div>" ;
			}
			else 
			{
				
				if ($result->rowcount()>$_SESSION[$guid]["pagination"]) {
					printPagination($guid, $result->rowcount(), $page, $_SESSION[$guid]["pagination"], "top", "&search=$search&allStudents=$allStudents") ;
				}
				print "<table cellspacing='0' style='width: 100%'>" ;
					print "<tr class='head'>" ;
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
							print _("Amount") ;
						print "</th>" ;
						print "<th>" ;
							print _("Fine") ;
						print "</th>" ;
						print "<th>" ;
							print _("Net Amount") ;
						print "</th>" ;
						print "<th>" ;
							print _("Mode") ;
						print "</th>" ;
						print "<th>" ;
							print _("Voucher N0") ;
						print "</th>" ;
						print "<th>" ;
							print _("Pay Date") ;
						print "</th>" ;
						
						print "<th>" ;
							print _("Actions") ;
						print "</th>" ;
						
					print "</tr>" ;
					
					$count=0;
					$rowNum="odd" ;
					try {
						$resultPage=$connection2->prepare($sqlPage);
						$resultPage->execute($data);
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
						
						$count++ ;
						
						//COLOR ROW BY STATUS!
						print "<tr class=$rowNum>" ;
							print "<td>" ;
								
								print $row["officialname"] ;
							print "</td>" ;
							print "<td>" ;
								print _($row["class"]) ;
							print "</td>" ;
							print "<td>" ;
								print $row["section"] ;
							print "</td>" ;
							print "<td>" ;
								print $row["total_amount"] ;
							print "</td>" ;
							print "<td>" ;
								print $row["fine_amount"] ;
							print "</td>" ;
							print "<td>" ;
								print $row["net_total_amount"] ;
							print "</td>" ;
							print "<td>" ;
								print $row["payment_mode"] ;
							print "</td>" ;
							print "<td>" ;
								print $row["voucher_number"] ;
							print "</td>" ;
							print "<td>" ;
								print $row["payment_date"] ;
							print "</td>" ;
							
							print "<td>" ;
								//print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/student_view_details.php&gibbonPersonID=" . $row["gibbonPersonID"] . "&search=$search'>detail</a> " ;
								print "<a href='javascript:void(9)' id='".$row["payment_master_id"]."_payment_list_print' class='print_list_print'>Print</a>&nbsp;|&nbsp;<a href='javascript:void(9)' id='".$row["payment_master_id"]."_payment_list_delete' class='print_list_delete'>Delete</a>";
								
							print "</td>" ;
							
						print "</tr>" ;
					}
				print "</table>" ;
				
				if ($result->rowcount()>$_SESSION[$guid]["pagination"]) {
					printPagination($guid, $result->rowcount(), $page, $_SESSION[$guid]["pagination"], "bottom", "search=$search") ;
				}
			
			}
			
?>
<input type="hidden" name="print_page_url" id="print_page_url" value="<?php echo $_SESSION[$guid]["absoluteURL"] . "/modules/Fee/print_payment.php";?>">
<input type="hidden" name="delete_fee_url" id="delete_fee_url" value="<?php echo $_SESSION[$guid]["absoluteURL"] . "/modules/Fee/ajax_payment_get_monthly_fee.php";?>">
<script type="text/javascript">
		$(function() {
			$( "#src_from_date" ).datepicker();
			$( "#src_to_date" ).datepicker();
		});
</script>