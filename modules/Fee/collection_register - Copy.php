<?php 
@session_start() ;
$payemntmode='';
$startdate='';
$enddate='';
$result='';
$sql='';
$data='';
$year='';	
try {
	$sql="SELECT * from gibbonschoolyear ORDER BY status";
	$result=$connection2->prepare($sql);
	$result->execute();
	$yearresult=$result->fetchAll();
	}
	catch(PDOException $e) { 
	print "<div class='error'>" . $e->getMessage() . "</div>" ; 
	}

if($_POST)
{
try {
					$data=array(); 
					$sql="SELECT SUM(net_total_amount) AS subtotal,payment_master.*,gibbonperson.officialName as officialname,gibbonperson.account_number,gibbonyeargroup.name AS class,
gibbonrollgroup.name AS section,gibbonstudentenrolment.rollOrder AS roll FROM payment_master 
LEFT JOIN gibbonperson ON payment_master.gibbonPersonID=gibbonperson.gibbonPersonID
LEFT JOIN gibbonstudentenrolment ON payment_master.gibbonStudentEnrolmentID=gibbonstudentenrolment.gibbonStudentEnrolmentID
LEFT JOIN gibbonyeargroup ON gibbonstudentenrolment.gibbonYearGroupID=gibbonyeargroup.gibbonYearGroupID
LEFT JOIN gibbonrollgroup ON gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID WHERE net_total_amount!=0 " ;  
					
					if ($_POST) {

						if(isset($_POST['src_payment_mode']))
						{
							if($_POST['src_payment_mode']!='')
							{
								$payemntmode=$_POST['src_payment_mode'];
								$sql.=" AND payment_mode='".$_POST['src_payment_mode']."'";
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
						if(isset($_POST['year_id']))
						{
							if($_POST['year_id']!='')
							{
								$year=$_REQUEST['year_id'];	
								$sql.=" AND payment_master.gibbonSchoolYearID=".$year;
							}
						}
					}
				//$sql.=" order by payment_master.payment_date, DESC";
				$sql.=" GROUP BY payment_master.payment_date,payment_master.payment_master_id WITH ROLLUP";
				//echo $sql;
				
				$result=$connection2->prepare($sql);
				$result->execute($data);
			}
			catch(PDOException $e) { 
				print "<div class='error'>" . $e->getMessage() . "</div>" ; 
			}
}

	?>
<form name="f1" id="f1" method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/collection_register.php" ?>">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td><input type="text" name="src_from_date" id="src_from_date" style="width: 100px;" value="<?php echo $startdate;?>" placeholder=" From Date.."></td>
    
    <td><input type="text" name="src_to_date" id="src_to_date" style="width: 100px;" value="<?php echo $enddate;?>" placeholder=" To Date.."></td>
    <td>
		<select name="year_id" id="year_id" style="width:150px;">
		<option value=''>Select Year</option>
		<?php foreach ($yearresult as $value) { ?>
    	<option value="<?php echo $value['gibbonSchoolYearID']?>" <?php if($year==$value['gibbonSchoolYearID']){?> selected="selected"<?php } ?>><?php echo $value['name']." (".$value['status']." year)"?></option>
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
	    <td><input type="submit" name="submit" id="submit" value="Search" ></td>
	    <?php if($_POST){?>
		<td>
	    <input type="button" id="collection_register_print" name="collection_register_print" value="Print" style="float: right;">
	    </td>
		<?php } ?>
  </tr>
</table>
<?php 
if($_POST)
{
if ($result->rowcount()<1) {
				print "<div class='error'>" ;
				print _("There are no records to display.") ;
				print "</div>" ;
			}
			else {
?>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr class="tablehead">
		<td>Date</td>
		<td>Voucher No</td>
		<td>Acc No</td>
		<td>Name</td>
		<td>Class</td>
		<td>Sec</td>
		<td>Roll No</td>
		<td>Mode</td>
		<td>Amount</td>
	</tr>
	<?php 
	try {
			$resultPage=$connection2->prepare($sql);
			$resultPage->execute($data);
			}
			catch(PDOException $e) { 
			print "<div class='error'>" . $e->getMessage() . "</div>" ; 
		}
					$i=0;
	while ($row=$resultPage->fetch()) { $i++;
		if($row['payment_master_id']==null)
		{
		?>
		<tr>
		<td colspan="7"></td>
		<td><?php if($result->rowcount()==$i){?>Total<?php } else {?>Sub Total<?php } ?></td>
		<td style="text-align: right"><?php echo $row['subtotal']?></td>  
	</tr>
	<?php } else {?>
		<tr>
		<td><?php echo IndianDateFormater($row['payment_date'])?></td>
		<td><?php echo $row['voucher_number']?></td>
		<td><?php echo substr($row['account_number'], 5);?></td>
		<td><?php echo $row['officialname']?></td>
		
		
		<td><?php echo $row['class']?></td>
		<td><?php echo SectionFormater($row['section']);?></td>
		<td><?php echo $row['roll']?></td>
		<td><?php echo ucfirst($row['payment_mode'])?></td>
		<td style="text-align: right"><?php echo $row['net_total_amount']?></td>  
	</tr>
	<?php 
	} }
	?>
</table>
<?php } } ?>
</form>
<script type="text/javascript">
		$(function() {
			$( "#src_from_date" ).datepicker({ dateFormat: 'dd/mm/yy' });
			$( "#src_to_date" ).datepicker({ dateFormat: 'dd/mm/yy' });
		});
</script>
<?php 
function IndianDateFormater($date)
{
	$datearr=explode("-", $date);
	$newdate=$datearr[2].'/'.$datearr[1].'/'.$datearr[0];
	return $newdate;
}
?>
<input type="hidden" name="print_page_url" id="print_page_url" value="<?php echo $_SESSION[$guid]["absoluteURL"] . "/modules/Fee/collection_register_print.php";?>">
