<style>
  .tab_scroll {
    display: block;
    overflow: scroll;
    height:800px;
}
</style>


<?php 
@session_start() ;

if (isActionAccessible($guid, $connection2, "/modules/Fee/lock_payment.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {

$vouchernumber='';
$payemntmode='';
$student_id=0;
$startdate='';
$enddate='';
$lock=-1;
/*  Query for generating Filter list */
$sql="SELECT gibbonstudentenrolment.*,gibbonperson.officialName,gibbonperson.firstName,gibbonperson.surname,gibbonyeargroup.name,gibbonrollgroup.name AS section,gibbonperson.account_number
FROM gibbonstudentenrolment 
LEFT JOIN gibbonperson ON gibbonstudentenrolment.gibbonPersonId=gibbonperson.gibbonPersonId 
LEFT JOIN gibbonyeargroup ON gibbonstudentenrolment.gibbonYearGroupId=gibbonyeargroup.gibbonYearGroupId 
LEFT JOIN gibbonrollgroup ON gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID";

$result=$connection2->prepare($sql);
$result->execute();
$dboutbut=$result->fetchAll();

/* Query for generating Table data. */

	try {
					$data=array(); 
					$sql="SELECT payment_master.*,gibbonperson.officialName as officialname,gibbonyeargroup.name AS class,
							gibbonrollgroup.name AS section,gibbonperson.account_number,gibbonstudentenrolment.rollOrder
							 FROM payment_master 
							LEFT JOIN gibbonperson ON payment_master.gibbonPersonID=gibbonperson.gibbonPersonID
							LEFT JOIN gibbonstudentenrolment ON payment_master.gibbonStudentEnrolmentID=gibbonstudentenrolment.gibbonStudentEnrolmentID
							LEFT JOIN gibbonyeargroup ON gibbonstudentenrolment.gibbonYearGroupID=gibbonyeargroup.gibbonYearGroupID
							LEFT JOIN gibbonrollgroup ON gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID WHERE net_total_amount!=0 " ;  
					
					
						if(isset($_REQUEST['src_voucher_no']))
						{
							if($_REQUEST['src_voucher_no']!='')
							{
								$vouchernumber=$_REQUEST['src_voucher_no'];
								$sql.=" AND voucher_number=".$_REQUEST['src_voucher_no'];
							}
							
						}
						if(isset($_REQUEST['src_payment_mode']))
						{
							if($_REQUEST['src_payment_mode']!='')
							{
								$payemntmode=$_REQUEST['src_payment_mode'];
								$sql.=" AND payment_mode='".$_REQUEST['src_payment_mode']."'";
							}
						}
						if(isset($_REQUEST['lock_status']))
						{
							if($_REQUEST['lock_status']!='')
							{
								$lock=$_REQUEST['lock_status'];
								$sql.=" AND payment_master.lock=".$_REQUEST['lock_status'];
							}
						}
						if(isset($_REQUEST['src_student']))
						{
							if($_REQUEST['src_student']!='')
							{
								$student_id=$_REQUEST['src_student'];
								$sql.=" AND payment_master.gibbonStudentEnrolmentID=".$_REQUEST['src_student'];
							}
						}
						if(isset($_REQUEST['src_from_date']))
						{
							if($_REQUEST['src_from_date']!='')
							{
								$startdate=$_REQUEST['src_from_date'];
								$datearr=explode("/", $startdate);
								$sql.=" AND payment_master.payment_date>='".$datearr[2]."-".$datearr[1]."-".$datearr[0]."'";
							}
						}
						if(isset($_REQUEST['src_to_date']))
						{
							if($_REQUEST['src_to_date']!='')
							{
								$enddate=$_REQUEST['src_to_date'];
								$datearr=explode("/", $enddate);
								$sql.=" AND payment_master.payment_date<='".$datearr[2]."-".$datearr[1]."-".$datearr[0]."'";
							}
						}
					
			$sql.=" order by payment_master.payment_master_id DESC";
			$result=$connection2->prepare($sql);
				$result->execute($data);
				//echo $sql;
	}
	catch(PDOException $e) { 
		print "<div class='error'>" . $e->getMessage() . "</div>" ; 
	}
?>
	<h3>Lock Payment List:</h3>

	<form name="f1" id="f1" method="get" action="<?php print $_SESSION[$guid]["absoluteURL"]?>/index.php">
	<input type="hidden" name="q" value="/modules/<?php print $_SESSION[$guid]["module"] ?>/lock_payment.php">
	<table width="100%" cellpadding="0" cellspacing="0" border="0" class="search_table">
	  <tr>
	  
		<td colspan='2'>
			<select name="src_student" id="src_student" style="width:200px; float:left;"> 
				<option value=""> Select Student </option>
				<?php foreach ($dboutbut as $value) { ?> 
				<option value="<?php echo $value['gibbonStudentEnrolmentID']?>" <?php if($student_id==$value['gibbonStudentEnrolmentID']){?> selected="selected"<?php } ?>><?php echo $value['officialName']?> (<?php echo substr($value['account_number'], 5);?>)</option>
				<?php } ?>
			</select>
				
				<span style="float:right">
				<input type="text" name="account_number" id="account_number" style="width:100px; float:left;" placeholder="Account Number">
				<input type="button" style=" float:left;" name="search_by_acc" id="search_by_acc" value="Go">
				<span>
		</td>
		<td>
			<input type="text" name="src_voucher_no" id="src_voucher_no" style="width: 200px; float:left;" value="<?php echo $vouchernumber;?>" placeholder=" Enter Voucher Number...">
		</td>
		<td rowspan='2'>
			<input type="submit"  value="Search" style="float: left; ">
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
				<option value="dd" <?php if($payemntmode=='dd'){?> selected="selected"<?php } ?>>Draft</option>
			</select>
			<select name='lock_status' id='lock_status'>
				<option value=''> Status </option>
				<option value='0' <?php if($lock==0){?> selected="selected"<?php } ?>>Unlocked</option>
				<option value='1' <?php if($lock==1){?> selected="selected"<?php } ?>>Locked</option>
			</select>
		</td>
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
				
			
				print "<table cellspacing='0' style='width: 100%;' class='tab_scroll' >" ;
					print "<tr class='head'>" ;
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
							print _("Status") ;
						print "</th>" ;
						print "<th>" ;
							print _("All <input type='checkbox' name='checkall' id='checkall' value='1' checked='checked'>") ;	
						print "</th>" ;
					print "</tr>" ;
					
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
						if ($count%2==0) {
							$rowNum="even" ;
						}
						else {
							$rowNum="odd" ;
						}
						$count++ ;
						$paymentdatearr=explode("-", $row["payment_date"]);
						//COLOR ROW BY STATUS!
						$c_s=$row['lock']==0?'style="color:green"':'style="color:red"';
						print "<tr class=$rowNum ".$c_s.">" ;
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
								print _($row["class"]) ;
							print "</td>" ;
							print "<td>" ;
								print SectionFormater($row["section"]) ;
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
								$str=$row['lock']==0?'Unlocked':'Locked';
								print $str ;
							print "</td>" ;
						
							print "<td>" ;
						?>
								<input type="checkbox" name="chk_<?php echo $row["payment_master_id"];?>" id="chk_<?php echo $row['payment_master_id'];?>" value="<?php echo $row["payment_master_id"];?>" class="list_chk" checked="checked">
						<?php
							print "</td>" ;
							
						print "</tr>" ;
					}
				print "</table>" ;
			}
?>
	<center>
	<input style="margin:20px; border:1px; padding:5px 20px; background:#ff731b; color:white; width:100px;" type="button" id="lock_payment" value="Lock">
	<input style="margin:20px; border:1px; padding:5px 20px; background:#ff731b; color:white; width:100px;" type="button" id="unlock_payment" value="Unlock">
	</center>


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
<script type="text/javascript">
	$(function() {
		$( "#src_from_date" ).datepicker({ dateFormat: 'dd/mm/yy' });
		$( "#src_to_date" ).datepicker({ dateFormat: 'dd/mm/yy' });
	});
</script>
<input type="hidden" name="get_enrollment_from_person_url" id="get_enrollment_from_person_url" value="<?php echo $_SESSION[$guid]["absoluteURL"] . "/modules/Fee/ajax_get_enrollemnt_id_by_personid.php";?>">

<?php
};
?>