<?php 
@session_start() ;
$search=NULL;
$monthname='';
$payemntmode='';
$student_id=0;
$startdate='';
$enddate='';
$total_net_amount=0;
$total_fine=0;
$total_amount=0;
$paymentstatus=0;
$location_id='';
$vehicle_id='';$class='';
	$sql="SELECT * from gibbonschoolyear ORDER BY firstDay DESC";
$result=$connection2->prepare($sql);
$result->execute();
$yearresult=$result->fetchAll();
$year='';

$montharrr=array('jan'=>'January','feb'=>'February','mar'=>'March','apr'=>'April','may'=>'May','jun'=>'June','jul'=>'July','aug'=>'August','sep'=>'September','oct'=>'October','nov'=>'November','dec'=>'December');
$sql="SELECT gibbonperson.officialName,gibbonperson.account_number,`gibbonPersonID`
FROM gibbonperson 
WHERE gibbonperson.avail_transport='Y'";

$result=$connection2->prepare($sql);
$result->execute();
$dboutput=$result->fetchAll();

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
					$sql="SELECT transport_month_entry.*,gibbonperson.officialName as officialname,gibbonyeargroup.name AS class,
gibbonrollgroup.name AS section,gibbonperson.account_number,gibbonstudentenrolment.rollOrder,transport_spot_price.spot_name,gibbonschoolyear.status AS year, vehicles.details as vehicle, gibbonperson.pd_point,`payment_master`.`payment_date` 
FROM transport_month_entry 
LEFT JOIN transport_spot_price on transport_spot_price.transport_spot_price_id=transport_month_entry.transport_spot_price_id 
LEFT JOIN gibbonperson ON transport_month_entry.gibbonPersonID=gibbonperson.gibbonPersonID
LEFT JOIN gibbonstudentenrolment ON transport_month_entry.`gibbonStudentEnrolmentID`=gibbonstudentenrolment.`gibbonStudentEnrolmentID`
LEFT JOIN gibbonyeargroup ON gibbonstudentenrolment.gibbonYearGroupID=gibbonyeargroup.gibbonYearGroupID
LEFT JOIN gibbonschoolyear ON transport_month_entry.gibbonSchoolYearID=gibbonschoolyear.gibbonSchoolYearID
LEFT JOIN gibbonrollgroup ON gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID 
LEFT JOIN vehicles ON vehicles.vehicle_id=transport_month_entry.vehicle_id
LEFT JOIN `payment_master` ON `payment_master`.`payment_master_id`=`transport_month_entry`.`payment_master_id` 
WHERE transport_month_entry.price!=0 " ;  
$sqlp='';					
					if (isset($_GET["search"])) {
						
						if(isset($_REQUEST['paid_unpaid']))
						{
							if($_REQUEST['paid_unpaid']!='')
							{
								$search.='&paid_unpaid='.$_REQUEST['paid_unpaid'];
								$payemntmode=$_REQUEST['paid_unpaid'];
								if($payemntmode=='Unpaid')
								{
									$sql.=" AND `transport_month_entry`.payment_master_id=0";
									$sqlp.=" AND `transport_month_entry`.payment_master_id=0";
								}
								else{
									$sql.=" AND `transport_month_entry`.payment_master_id>0";
									$sqlp.=" AND `transport_month_entry`.payment_master_id>0";
								}
								
							}
							
						}
						if(isset($_REQUEST['src_payment_month']))
						{
							if($_REQUEST['src_payment_month']!='')
							{
								$search.='&month_name='.$_REQUEST['src_payment_month'];
								$monthname=$_REQUEST['src_payment_month'];
								$sql.=" AND month_name='".$_REQUEST['src_payment_month']."'";
								$sqlp.=" AND month_name='".$_REQUEST['src_payment_month']."'";
							}
						}
						if(isset($_REQUEST['src_student']))
						{
							if($_REQUEST['src_student']!='')
							{
								$search.='&src_student='.$_REQUEST['src_student'];
								$student_id=$_REQUEST['src_student'];
								$sql.=" AND transport_month_entry.gibbonPersonID=".$_REQUEST['src_student'];
								$sqlp.=" AND transport_month_entry.gibbonPersonID=".$_REQUEST['src_student'];
							}
						}
						if(isset($_REQUEST['location']))
						{
							if($_REQUEST['location']!='')
							{
								$location_id=$_REQUEST['location'];
								$sql.=" AND transport_month_entry.transport_spot_price_id=".$location_id;
								$sqlp.=" AND transport_month_entry.transport_spot_price_id=".$location_id;
							}
						}
						if(isset($_REQUEST['vehicle']))
						{
							if($_REQUEST['vehicle']!='')
							{
								$vehicle_id=$_REQUEST['vehicle'];
								$sql.=" AND transport_month_entry.vehicle_id=".$vehicle_id;
								$sqlp.=" AND transport_month_entry.vehicle_id=".$vehicle_id;
							}
						}
						if(isset($_REQUEST['year_name']))
						{
							if($_REQUEST['year_name']!='')
							{
								$year=$_REQUEST['year_name'];
								$search.='&year_name='.$year;
								$sql.=" AND transport_month_entry.gibbonSchoolYearID ='".$year."'";
								$sqlp.=" AND transport_month_entry.gibbonSchoolYearID ='".$year."'";
							}
						}						if(isset($_REQUEST['class']))						{							if($_REQUEST['class']!='')							{								$class=$_REQUEST['class'];								$sql.=" AND gibbonyeargroup.gibbonYearGroupId =".$class;								$sqlp.=" AND gibbonyeargroup.gibbonYearGroupId =".$class;							}						}
					}
				$sql.=" order by transport_month_entry.transport_month_entryid DESC";
				$sqlPage=$sql . " LIMIT " . $_SESSION[$guid]["pagination"] . " OFFSET " . (($page-1)*$_SESSION[$guid]["pagination"]) ;
				$result=$connection2->prepare($sql);
				$result->execute($data);
				$print_data=$result->fetchAll();
			}
			catch(PDOException $e) { 
				print "<div class='error'>" . $e->getMessage() . "</div>" ; 
			}
			
		?>	
		<input type='hidden' id="print_data" value="<?php echo $sqlp;?>">
<!--  <form name="f1" id="f1" method="get" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/payment_list.php&search_submit=search" ?>">-->
<form name="f1" id="f1" method="get" action="<?php print $_SESSION[$guid]["absoluteURL"]?>/index.php">
<input type="hidden" name="q" value="/modules/<?php print $_SESSION[$guid]["module"] ?>/monthly_entry_list.php">
<input name="search" id="search" maxlength=20 value="<?php print $search ?>" type="hidden" style="width: 300px">
<table width="90%" cellpadding="0" cellspacing="0" border="0" >
  <tr>
    <td colspan='2'>
			
		<select name="src_student" id="src_student" style="width:150px; float:left">
		    <option value=""> Select Student </option>
		    <?php foreach ($dboutput as $value) { ?>
		    <option value="<?php echo $value['gibbonPersonID']?>" <?php if($student_id==$value['gibbonPersonID']){?> selected="selected"<?php } ?>><?php echo $value['officialName']?>  (<?php echo substr($value['account_number'], 5);?>)</option>
		    <?php } ?>
		</select>
			<span style="float:right">
			<input type="text" name="account_number" id="account_number" style="width:100px; float:left;" placeholder="Account Number">
			<input type="button" style=" float:left;" name="search_by_acc_pID" id="search_by_acc_pID" value="Go">
			<span>
    </td>
    <td colspan='2'>
	    <select name="src_payment_month" id="src_payment_month">
	    	<option value=""> Select Month </option>
	    	<?php foreach ($montharrr as $key=>$value) { ?>
	    		<option value="<?php echo $key;?>" <?php if($monthname==$key){?> selected="selected"<?php } ?>><?php echo $value;?></option>
	    	<?php }?>
	    	
	    </select>
   
     <select name="paid_unpaid" id="paid_unpaid" style='float:left'>
     <option value="">Select Status</option>
  	 	<option value="Paid" <?php if($payemntmode=='Paid'){?>selected<?php }?>>Paid</option>
  	 	<option value="Unpaid" <?php if($payemntmode=='Unpaid'){?>selected<?php }?>>Unpaid</option>
  	 </select>
    </td>
    <td rowspan='2'>
		<input type="submit"  value="Search"> <br><br>
		<span name="print_monthly_entry" id="print_monthly_entry" style='border:1px; padding:5px 15px; background-color:#ff731b; color:white;'><b>Print</b></span>
	 
    </td>
  </tr>
  <tr>
    
   <td>Location:
	<select name='location' id='location' style="width:150px;">
		<option value=''>Select</option>
		<?php 
			$sql1="SELECT * FROM `transport_spot_price`";
			$result1=$connection2->prepare($sql1);
			$result1->execute();
			$location=$result1->fetchAll();
			foreach($location as $a)
			{
				$s=$location_id==$a['transport_spot_price_id']?"selected":"";
				print "<option value='".$a['transport_spot_price_id']."' ".$s.">".$a['spot_name']."</option>";
			}
		?>
	</select>
   </td>
   <td>
   	Vehicle:
	<select name='vehicle' id='vehicle' style="width:150px;">
		<option value=''>Select</option>
		<?php 
			$sql2="SELECT * FROM `vehicles`";
			$result2=$connection2->prepare($sql2);
			$result2->execute();
			$vehicles=$result2->fetchAll();
			foreach($vehicles as $a)
			{
				$s=$vehicle_id==$a['vehicle_id']?"selected":"";
				print "<option value='".$a['vehicle_id']."' ".$s.">".$a['details']."</option>";
			}
		?>
	</select>
    </td>	<td>		Year :		<select name="year_name" id="year_name" style="width:100px;">			<option value=''>Select Year</option>			 <?php foreach ($yearresult as $value) { ?>			<option value="<?php echo $value['gibbonSchoolYearID']?>" <?php if($year==$value['gibbonSchoolYearID']) echo "selected";?>><?php echo $value['name']." (".$value['status']." year)"?> </option>			<?php } ?>		</select>	    </td>
  	<td>		Class :		<select name='class' id='class'>			<option value=''>Select</option>			<?php			$sql2="SELECT * FROM gibbonyeargroup";			$result2=$connection2->prepare($sql2);			$result2->execute();			$class_result=$result2->fetchAll();			foreach($class_result as $a){				$s=$class==$a['gibbonYearGroupID']?"selected":"";				echo "<option value='".$a['gibbonYearGroupID']."' ".$s.">".$a['name']."</option>";			}						?>		</select>	</td>
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
					printPagination($guid, $result->rowcount(), $page, $_SESSION[$guid]["pagination"], "top", "&search=$search") ;
				}
				?>
				<table  width="90%" cellpadding="0" cellspacing="0">
				<?php
					print "<tr class='head'>" ;
					
						print "<th>" ;
							print _("Entry<br>Date") ;
						print "</th>" ;
						print "<th>" ;
							print _("Acc No") ;
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
				/*		print "<th>" ;
							print _("Sec") ;
						print "</th>" ; */
						
						print "<th>" ;
							print _("Amount") ;
						print "</th>" ;
						print "<th>" ;
							print _("Month") ;
						print "</th>" ;
						print "<th>" ;
							print _("Year") ;
						print "</th>" ;
				/*		print "<th>" ;
							print _("Location") ;
						print "</th>" ;
						print "<th>" ;
							print _("Pickup Drop Point") ;
						print "</th>" ; */
						print "<th>" ;
							print _("Vehicle") ;
						print "</th>" ;
						print "<th>" ;
							print _("Status") ;
						print "</th>" ;
						print "<th>" ;
							print _("Action") ;
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
						$total_amount+=$row["price"];
						$count++ ;
						$paymentdatearrr=explode(" ", $row["created_date"]);
						$paymentdatearr=explode("-", $paymentdatearrr[0]);
						
						if($row["payment_master_id"]==0){
							$paymentstatus='Unpaid';
						}else{
							$date=date('d/m/Y',strtotime($row['payment_date']));
							$paymentstatus="Paid<br>($date)";
						}
						$c_s=$row["payment_master_id"]==0?'style="color:red"':'style="color:green"';
						//COLOR ROW BY STATUS!
						print "<tr class=$rowNum ".$c_s.">" ;
						
							print "<td>" ;
								print $paymentdatearr[2].'/'.$paymentdatearr[1].'/'.$paymentdatearr[0] ;
							print "</td>" ;
						print "<td>" ;
								print substr($row["account_number"], 6,10);
							print "</td>" ;
							print "<td>" ;
								print $row["officialname"] ;
							print "</td>" ;
							print "<td>" ;
								print $row["rollOrder"] ;
							print "</td>" ;
							print "<td>" ;
								print _($row["class"]) ;
							print "</td>" ; 							/*
							print "<td>" ;
								print $row["section"] ;
							print "</td>" ;
							*/	
							print "<td style='text-align:right'>" ;
								print $row["price"] ;
							print "</td>" ;
							print "<td>" ;
								print $montharrr[$row["month_name"]] ;
							print "</td>" ;
							print "<td>" ;
								print $row["year"] ;
							print "</td>" ;
				/*			print "<td>" ;
								print $row["spot_name"] ;
							print "</td>" ;
							print "<td>" ;
								print $row["pd_point"] ;
							print "</td>" ;				*/
							print "<td>" ;
								print $row["vehicle"] ;
							print "</td>" ;
							print "<td>" ;
								print $paymentstatus;
							print "</td>" ;
							
							print "<td>" ;
							if($row["payment_master_id"]==0){
							
								print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/monthly_entry_list_process.php&action=edit&transport_month_entryid=".$row['transport_month_entryid']."' disable='true'>Edit</a> " ;
								print " | ";
								print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/monthly_entry_list_process.php&action=delete&transport_month_entryid=".$row['transport_month_entryid']."' onclick='return confirm(\"Are you surely want to Delete?\")'>Delete</a> " ;
							}	
							print "</td>" ;
							
						print "</tr>" ;
					}
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
										print 'Total:';				
							print "</td>" ;
							print "<td style='text-align:right'>" ;
								printf("%.2f", $total_amount) ;
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
						print "</tr>" ;
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
