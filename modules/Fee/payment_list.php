<?php 
@session_start() ;
if (isActionAccessible($guid, $connection2, "/modules/Fee/payment_list.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
$isAdmin=($_SESSION[$guid]['gibbonRoleIDPrimary']+0)==1;  //Admin role id=1   
$search=NULL;
$vouchernumber='';
$payemntmode='';
$student_id=0;
$startdate='';
$enddate='';
$total_net_amount=0;
$total_fine=0;
$total_amount=0;

$sql="SELECT `gibbonPersonID`,`preferredName`,`account_number` FROM `gibbonperson` WHERE `gibbonPersonID` IN (SELECT `gibbonPersonID` FROM `gibbonstudentenrolment`)";
$result=$connection2->prepare($sql);
$result->execute();
$dboutbut=$result->fetchAll();

			if (isset($_GET["search"])) {
				//$search=$_GET["search"] ;
				$search='true';
			}
			//Set pagination variable
			$page=1 ; if (isset($_GET["page"])) { $page=$_GET["page"] ; }
			if ((!is_numeric($page)) OR $page<1) {
				$page=1 ;
			}
			try {
					$data=array(); 
					$sql="SELECT payment_master.*,gibbonperson.officialName as officialname,gibbonyeargroup.name AS class,
gibbonrollgroup.name AS section,gibbonperson.account_number,gibbonstudentenrolment.rollOrder
 FROM payment_master 
LEFT JOIN gibbonperson ON payment_master.gibbonPersonID=gibbonperson.gibbonPersonID
LEFT JOIN gibbonstudentenrolment ON payment_master.gibbonStudentEnrolmentID=gibbonstudentenrolment.gibbonStudentEnrolmentID
LEFT JOIN gibbonyeargroup ON gibbonstudentenrolment.gibbonYearGroupID=gibbonyeargroup.gibbonYearGroupID
LEFT JOIN gibbonrollgroup ON gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID WHERE net_total_amount!=0 " ;  
					
					if (isset($_GET["search"])) {
						
						if(isset($_REQUEST['src_voucher_no']))
						{
							if($_REQUEST['src_voucher_no']!='')
							{
								$search.='&src_voucher_no='.$_REQUEST['src_voucher_no'];
								$vouchernumber=$_REQUEST['src_voucher_no'];
								$sql.=" AND voucher_number=".$_REQUEST['src_voucher_no'];
							}
							
						}
						if(isset($_REQUEST['src_payment_mode']))
						{
							if($_REQUEST['src_payment_mode']!='')
							{
								$search.='&src_payment_mode='.$_REQUEST['src_payment_mode'];
								$payemntmode=$_REQUEST['src_payment_mode'];
								$sql.=" AND payment_mode='".$_REQUEST['src_payment_mode']."'";
							}
						}
						if(isset($_REQUEST['src_student']))
						{
							if($_REQUEST['src_student']!='')
							{
								$search.='src_student='.$_REQUEST['src_student'];
								$student_id=$_REQUEST['src_student'];
								$sql.=" AND payment_master.gibbonPersonID=".$_REQUEST['src_student'];
							}
						}
						if(isset($_REQUEST['src_from_date']))
						{
							if($_REQUEST['src_from_date']!='')
							{
								$search.='&src_from_date='.$_REQUEST['src_from_date'];
								$startdate=$_REQUEST['src_from_date'];
								$datearr=explode("/", $startdate);
								$sql.=" AND payment_master.payment_date>='".$datearr[2]."-".$datearr[1]."-".$datearr[0]."'";
							}
						}
						if(isset($_REQUEST['src_to_date']))
						{
							if($_REQUEST['src_to_date']!='')
							{
								$search.='&src_to_date='.$_REQUEST['src_to_date'];
								$enddate=$_REQUEST['src_to_date'];
								$datearr=explode("/", $enddate);
								$sql.=" AND payment_master.payment_date<='".$datearr[2]."-".$datearr[1]."-".$datearr[0]."'";
							}
						}
						
						$sql.=" order by payment_master.payment_master_id DESC";
						//if (!isset($_GET["search"])) 
							//$sql.=" LIMIT 500";
						$sqlPage=$sql . " LIMIT " . $_SESSION[$guid]["pagination"] . " OFFSET " . (($page-1)*$_SESSION[$guid]["pagination"]) ;
						//echo $sql;
						$result=$connection2->prepare($sql);
						$result->execute($data);
					}
			}
			catch(PDOException $e) { 
				print "<div class='error'>" . $e->getMessage() . "</div>" ; 
			}
			
		?>
<!--  <form name="f1" id="f1" method="get" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/payment_list.php&search_submit=search" ?>">-->
<form name="f1" id="f1" method="get" action="<?php print $_SESSION[$guid]["absoluteURL"]?>/index.php">
<input type="hidden" name="q" value="/modules/<?php print $_SESSION[$guid]["module"] ?>/payment_list.php">
<input name="search" id="search" maxlength=20 value="<?php print $search ?>" type="hidden" style="width: 300px">
<table width="100%" cellpadding="0" cellspacing="0" border="0" class="search_table">
  <tr>
  
    <td colspan='2'>
		<select name="src_student" id="src_student" style="width:200px; float:left;">
		    <option value=""> Select Student </option>
		    <?php foreach ($dboutbut as $value) { 
				$s=$student_id==$value['gibbonPersonID']?'selected':'';
			?>
		    <option value="<?=$value['gibbonPersonID']?>"  <?=$s?>><?php echo $value['preferredName']?> (<?php echo $value['account_number']+0;?>)</option>
		    <?php } ?>
		</select>
			
			<span style="float:right">
			<input type="text" name="account_number" id="account_number" style="width:100px; float:left;" placeholder="Account Number">
			<input type="button" style=" float:left;" name="search_by_acc" id="search_by_acc" value="Go">
			<span>
    </td>
    <td colspan='2'>
		<input type="text" name="src_voucher_no" id="src_voucher_no" style="width: 200px; float:left;" value="<?php echo $vouchernumber;?>" placeholder=" Enter Voucher Number...">
	</td>
	
	
  </tr>
  <tr>
  
   <td>
		<input type="text" name="src_from_date" id="src_from_date" style="width: 100px; float:left;" value="<?php echo $startdate;?>" placeholder=" From..">
  	</td>
	<td>
		<input type="text" name="src_to_date" id="src_to_date" style="width: 100px; float:right;" value="<?php echo $enddate;?>" placeholder=" To.."]>
    </td>
   
	<td>
	    <select name="src_payment_mode" id="src_payment_mode" style="float:left;">
	    	<option value=""> Select Mode </option>
	    	<option value="cash" <?php if($payemntmode=='cash'){?> selected="selected"<?php } ?>>Cash</option>
	    	<option value="cheque" <?php if($payemntmode=='cheque'){?> selected="selected"<?php } ?>>Cheque</option>
			<!--
	    	<option value="dd" <?php if($payemntmode=='dd'){?> selected="selected"<?php } ?>>Draft</option>
			<option value="bank_transfer" <?php if($payemntmode=='bank_transfer'){?> selected="selected"<?php } ?>>Bank Transfer</option>
			<option value="net_banking" <?php if($payemntmode=='net_banking'){?> selected="selected"<?php } ?>>Net Banking</option>
			<option value="credit_card" <?php if($payemntmode=='credit_card'){?> selected="selected"<?php } ?>>Credit Card</option>
			<option value="debit_card" <?php if($payemntmode=='debit_card'){?> selected="selected"<?php } ?>>Debit Card</option>
			-->
			<option value="card" <?php if($payemntmode=='card'){?> selected="selected"<?php } ?>>Card</option>
			<option value="online" <?php if($payemntmode=='online'){?> selected="selected"<?php } ?>>Online</option>
	    </select>
    </td>
 	 <td><input type="submit"  value="Search" style="float: left; ">
	</td>
  </tr>
</table>
</form>

		<?php 
		if(isset($_GET["search"])){
		if ($result->rowcount()<1) {
				print "<div class='error'>" ;
				print _("There are no records to display.") ;
				print "</div>" ;
			}
			else 
			{
				
			/*	if ($result->rowcount()>$_SESSION[$guid]["pagination"]) {
					printPagination($guid, $result->rowcount(), $page, $_SESSION[$guid]["pagination"], "top", "&search=$search") ;
				}*/
				print "<table cellspacing='0' style='width: 100%;font-size:12.5px' id='myTable'>" ;
					print "<thead>";
					print "<tr class='head'>" ;
					print "<th style='display:none'></th>";
					print "<th>" ;
							print _("Voucher") ;
						print "</th>" ;
						print "<th>" ;
							print _("Pay&nbsp;Date") ;
						print "</th>" ;
						print "<th>" ;
							print _("Acc&nbsp;No") ;
						print "</th>" ;
						print "<th>" ;
							print _("Name") ;
						print "</th>" ;
						print "<th>" ;
							print _("Roll") ;
						print "</th>" ;
						print "<th>" ;
							print _("Class") ;
						print "</th>" ;
						print "<th>" ;
							print _("Sec") ;
						print "</th>" ;
						
						print "<th>" ;
							print _("Amt") ;
						print "</th>" ;
						print "<th>" ;
							print _("Fine") ;
						print "</th>" ;
						print "<th>" ;
							print _("Net&nbsp;Amt") ;
						print "</th>" ;
						
						print "<th>" ;
							print _("Mode") ;
						print "</th>" ;
						
						
						print "<th>" ;
							print _("Action") ;
						print "</th>" ;
						
					print "</tr>" ;
					print "</thead>" ;
					print "<tbody>" ;
					
					$count=0;
					$rowNum="odd" ;
				/*	try {
						$resultPage=$connection2->prepare($sqlPage);
						$resultPage->execute($data);
					}
					catch(PDOException $e) { 
						print "<div class='error'>" . $e->getMessage() . "</div>" ; 
					} */
					while ($row=$result->fetch()) {
						if ($count%2==0) {
							$rowNum="even" ;
						}
						else {
							$rowNum="odd" ;
						}
						$total_net_amount+=$row["net_total_amount"];
						$total_fine+=$row["fine_amount"];
						$total_amount+=$row["total_amount"];
						$count++ ;
						$paymentdatearr=explode("-", $row["payment_date"]);
						//COLOR ROW BY STATUS!
						print "<tr class=$rowNum>" ;
						$vNo=$row["voucher_number"]+0;
						print "<td style='display:none'>{$paymentdatearr[0]}{$paymentdatearr[1]}{$paymentdatearr[2]}{$vNo}</td>";
						print "<td>" ;
								print substr($row["voucher_number"], 3) ;
							print "</td>" ;
							print "<td>" ;
								print $paymentdatearr[2].'/'.$paymentdatearr[1].'/'.$paymentdatearr[0] ;
							print "</td>" ;
						print "<td>" ;
								print substr($row["account_number"], 5);
							print "</td>" ;
							print "<td>" ;
								print $row["officialname"] ;
							print "</td>" ;
							print "<td>" ;
								print $row["rollOrder"] ;
							print "</td>" ;
							print "<td>" ;
								print $row["class"] ;
							print "</td>" ;
							print "<td>" ;
								print $row["section"] ;
							print "</td>" ;
								
							print "<td style='text-align:right;'>" ;
								print $row["total_amount"] ;
							print "</td>" ;
							print "<td style='text-align:right;'>" ;
								print $row["fine_amount"] ;
							print "</td>" ;
							print "<td style='text-align:right;'>" ;
								print $row["net_total_amount"] ;
							print "</td>" ;
							
							print "<td>" ;
								print ucfirst($row["payment_mode"]) ;
							print "</td>" ;
							
							
							print "<td>" ;
								//print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/student_view_details.php&gibbonPersonID=" . $row["gibbonPersonID"] . "&search=$search'>detail</a> " ;
							//	print "<a href='javascript:void(9)' id='".$row["payment_master_id"]."_payment_list_print' class='print_list_print'>Print</a>";
							
							// Corrected by Shiva 21.02.2018
                                print "<a href='javascript:void(9)' id='".$row["payment_master_id"]."_payment_list_print' class='print_list_print'><img title='" . _('Print') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/print.png' width=17 height=17/></a>";											
							//	if($row['lock']==0)
							//		print	"&nbsp;|&nbsp;<a href='javascript:void(9)' id='".$row["payment_master_id"]."_payment_list_delete' class='print_list_delete2'>Delete</a>";
								if($isAdmin){
                                if($row['lock']==0)
                                    print	"<a href='javascript:void(9)' id='".$row["payment_master_id"]."_payment_list_delete' class='print_list_delete2'> <img title='" . _('Delete') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/garbage.png' width=20 height=17/></a>";									
								}
							print "</td>" ;
							
						print "</tr>" ;
					}
					print "<tfoot>" ;
					print "<tr class=$rowNum>" ;
							print "<td>" ;
							print "</td>" ;
							print "<td>" ;
							print "</td>" ;
							print "<td>" ;
							print "</td>" ;
							print "<td>" ;
							print "</td>" ;
							print "<td>" ;
								
							print "</td>" ;
							print "<td>" ;
														
							print "</td>" ;
							print "<td>" ;
								print 'Total:';
							print "</td>" ;
							print "<td style='text-align:right;'>" ;
								printf("%.2f", $total_amount) ;
							print "</td>" ;
							print "<td style='text-align:right;'>" ;
								printf("%.2f", $total_fine) ;
							print "</td>" ;
							print "<td style='text-align:right;'>" ;
								printf("%.2f", $total_net_amount) ;
							print "</td>" ;
							
							print "<td>" ;
								
							print "</td>" ;
							print "<td>" ;
								
							print "</td>" ;
							
						print "</tr>" ;
						print "</tfoot>" ;
						print "</tbody>" ;
				print "</table>" ;
				/*
				if ($result->rowcount()>$_SESSION[$guid]["pagination"]) {
					printPagination($guid, $result->rowcount(), $page, $_SESSION[$guid]["pagination"], "bottom", "search=$search") ;
				}
				*/
			}
			
		}
?>
<input type="hidden" name="print_page_url" id="print_page_url" value="<?php echo $_SESSION[$guid]["absoluteURL"] . "/modules/Fee/print_payment.php";?>">
<input type="hidden" name="delete_fee_url" id="delete_fee_url" value="<?php echo $_SESSION[$guid]["absoluteURL"] . "/modules/Fee/ajax_payment_get_monthly_fee.php";?>">
<script type="text/javascript">
	$(function() {
		$( "#src_from_date" ).datepicker({ dateFormat: 'dd/mm/yy' });
		$( "#src_to_date" ).datepicker({ dateFormat: 'dd/mm/yy' });
	});
</script>
<?php 
function moneyFormatIndia($num){
    $explrestunits = "" ;
    if(strlen($num)>3){
        $lastthree = substr($num, strlen($num)-3, strlen($num));
        $restunits = substr($num, 0, strlen($num)-3); // extracts the last three digits
        $restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; // explodes the remaining digits in 2's formats, adds a zero in the beginning to maintain the 2's grouping.
        $expunit = str_split($restunits, 2);
        for($i=0; $i<sizeof($expunit); $i++){
            // creates each of the 2's group and adds a comma to the end
            if($i==0)
            {
                $explrestunits .= (int)$expunit[$i].","; // if is first value , convert into integer
            }else{
                $explrestunits .= $expunit[$i].",";
            }
        }
        $thecash = $explrestunits.$lastthree;
    } else {
        $thecash = $num;
    }
    return $thecash; // writes the final format where $currency is the currency symbol.
}
?>
<input type="hidden" name="get_personID_from_accno_url" id="get_personID_from_accno_url" value="<?php echo $_SESSION[$guid]["absoluteURL"] . "/modules/Fee/ajax_get_personid_by_accno.php";?>">

 <script src="<?php echo $_SESSION[$guid]["absoluteURL"] ;?>/modules/Fee/js/jquery.dataTables.min.js"></script>
 <script>
	 $(document).ready(function(){
		$('#myTable').DataTable();
	});
 </script>
 
 
<?php
};
?>