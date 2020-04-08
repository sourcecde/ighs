<?php 
@session_start() ;

if (isActionAccessible($guid, $connection2, "/modules/Fee/cheque_master.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
    
$search=NULL;
$vouchernumber='';
$bankID='';
$chequenumber='';
$payemntmode='';
$student_id=0;
$startdate='';
$enddate='';
$total_amount=0;
try{
	$sql="SELECT * FROM `payment_bankaccount`";
	$result=$connection2->prepare($sql);
	$result->execute();
	$bank =$result->fetchAll();
}
catch(PDOException $e) { 
	print "<div class='error'>" . $e->getMessage() . "</div>" ; 
}
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
	$sql="SELECT `cheque_master`.*,`fee_bank_master`.`bankAbbr` as bank
	FROM `cheque_master` 
	LEFT JOIN `fee_bank_master` ON `cheque_master`.bankMasterID=`fee_bank_master`.bankMasterID WHERE amount!=0" ;
					
	if (isset($_GET["search"])) {
		
		/*if(isset($_REQUEST['src_student']))
		{
			if($_REQUEST['src_student']!='')
			{
				$search.='src_student='.$_REQUEST['src_student'];
				$student_id=$_REQUEST['src_student'];
				$sql.=" AND `cheque_master`.gibbonPersonID=".$_REQUEST['src_student'];
			}
		}
		if(isset($_REQUEST['src_voucher_no']))
		{
			if($_REQUEST['src_voucher_no']!='')
			{
				$search.='&src_voucher_no='.$_REQUEST['src_voucher_no'];
				$vouchernumber=$_REQUEST['src_voucher_no'];
				$sql.=" AND voucher_no=".$_REQUEST['src_voucher_no'];
			}
		}*/
		if(isset($_REQUEST['src_bank']))
		{
			if($_REQUEST['src_bank']!='')
			{
				$search.='src_bank='.$_REQUEST['src_bank'];
				$bankID=$_REQUEST['src_bank'];
				$sql.=" AND `cheque_master`.bankMasterID=".$_REQUEST['src_bank'];
			}
		}
		if(isset($_REQUEST['src_status']))
		{
			if($_REQUEST['src_status']!='')
			{
				$search.='src_status='.$_REQUEST['src_status'];
				$bankID=$_REQUEST['src_status'];
				$sql.=" AND `cheque_master`.cheque_status_id=".$_REQUEST['src_status'];
			}
		}
		if(isset($_REQUEST['src_cheque_no']))
		{
			if($_REQUEST['src_cheque_no']!='')
			{
				$search.='&src_cheque_no='.$_REQUEST['src_cheque_no'];
				$chequenumber=$_REQUEST['src_cheque_no'];
				$sql.=" AND cheque_no=".$_REQUEST['src_cheque_no'];
			}
		}
		if(isset($_REQUEST['src_from_date']))
		{
			if($_REQUEST['src_from_date']!='')
			{
				$search.='&src_from_date='.$_REQUEST['src_from_date'];
				$startdate=$_REQUEST['src_from_date'];
				$datearr=explode("/", $startdate);
				$sql.=" AND `cheque_master`.cheque_date>='".$datearr[2]."-".$datearr[1]."-".$datearr[0]."'";
			}
		}
		if(isset($_REQUEST['src_to_date']))
		{
			if($_REQUEST['src_to_date']!='')
			{
				$search.='&src_to_date='.$_REQUEST['src_to_date'];
				$enddate=$_REQUEST['src_to_date'];
				$datearr=explode("/", $enddate);
				$sql.=" AND `cheque_master`.cheque_date<='".$datearr[2]."-".$datearr[1]."-".$datearr[0]."'";
			}
		}
	}
	$sql.=" order by `cheque_master_id` DESC";
	//if (!isset($_GET["search"])) 
	//$sql.=" LIMIT 500";
	$sqlPage=$sql . " LIMIT " . $_SESSION[$guid]["pagination"] . " OFFSET " . (($page-1)*$_SESSION[$guid]["pagination"]) ;
	//echo $sql;
	$result=$connection2->prepare($sql);
	$result->execute($data);
}
catch(PDOException $e) { 
	print "<div class='error'>" . $e->getMessage() . "</div>" ; 
}
?>
<form name="f1" id="f1" method="get" action="<?php print $_SESSION[$guid]["absoluteURL"]?>/index.php">
<input type="hidden" name="q" value="/modules/<?php print $_SESSION[$guid]["module"] ?>/cheque_master.php">
<input name="search" id="search" maxlength=20 value="<?php print $search ?>" type="hidden" style="width: 300px">
<table width="100%" cellpadding="0" cellspacing="0" border="0" class="search_table">
  <tr>
	<td>
	<!--<select name="src_bank" id="src_bank" style="width:130px; float:left;">
		    <option value=""> All Banks </option>
		    <?php foreach ($bank as $value) { 
				$s=$bankID==$value['bankMasterID']?'selected':'';
			?>
		    <option value="<?=$value['bankMasterID']?>"  <?=$s?>><?php echo $value['bankName']?> </option>
		    <?php } ?>
	</select>
	</td>-->
	<td>
	<select name="src_status" id="src_status" style="width:130px; float:left;">
			<option value=""> All Cheques</option>
		    <option value="0" <?php if(isset($_GET["src_status"]) && (int)$_GET["src_status"]==0){echo "selected";}?>> Rejected Cheques</option>
			<option value="1" <?php if(isset($_GET["src_status"]) && (int)$_GET["src_status"]==1){echo "selected";}?>> Accepted Cheques </option>
	</select>
	</td>
	<td>
		<input type="text" name="src_cheque_no" id="src_cheque_no" style="width: 180px; float:left;" value="<?php echo $chequenumber;?>" placeholder=" Enter Cheque Number...">
	</td>
  
   <td>
		<input type="text" name="src_from_date" id="src_from_date" style="width: 100px; float:left;" value="<?php echo $startdate;?>" placeholder=" From..">
  	</td>
	<td>
		<input type="text" name="src_to_date" id="src_to_date" style="width: 100px; float:right;" value="<?php echo $enddate;?>" placeholder=" To.."]>
    </td>
 	 <td><input type="submit"  value="Search" style="float: left; ">
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
		
	/*	if ($result->rowcount()>$_SESSION[$guid]["pagination"]) {
	printPagination($guid, $result->rowcount(), $page, $_SESSION[$guid]["pagination"], "top", "&search=$search") ;
	}*/
		print "<table cellspacing='0' style='width: 100%' id='myTable'>" ;
			print "<thead>";
				print "<tr class='head'>" ;
					print "<th>" ;
						print _("Cheque No.") ;
					print "</th>" ;
					print "<th>" ;
						print _("Bank") ;
					print "</th>" ;
					/*print "<th>" ;
							print _("Voucher No.") ;
					print "</th>" ;*/
					print "<th>" ;
						print _("Cheque Date") ;
					print "</th>" ;
					/*print "<th>" ;
						print _("Acc No") ;
					print "</th>" ;
					print "<th>" ;
						print _("Name") ;
					print "</th>" ;
					print "<th>" ;
						print _("Class") ;
					print "</th>" ;*/
					print "<th>" ;
						print _("Amt") ;
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
			$total_amount+=$row["amount"];
			$count++ ;
			$paymentdatearr=explode("-", $row["cheque_date"]);
			$cheque_id=$row['cheque_master_id'];
			$style="";
			if($row['cheque_status_id']=='0'){
				$style="background: -webkit-gradient(linear, left top, left bottom, from(rgb(241, 181, 181)), to(#efb3b3))!important;";
			}
			//COLOR ROW BY STATUS!
			print "<tr class=$rowNum>" ;
				print "<td style='$style'>" ;
					print $row["cheque_no"] ;
				print "</td>" ;
				print "<td style='$style'>" ;
					print $row["bank"] ;
				print "</td>" ;
				/*print "<td>" ;
					print $row["voucher_no"] ;
				print "</td>" ;*/
				print "<td style='$style'>" ;
					print "<span style='display:none'>".$paymentdatearr[0].$paymentdatearr[1].$paymentdatearr[2]."</span>";
					print $paymentdatearr[2].'/'.$paymentdatearr[1].'/'.$paymentdatearr[0] ;
				print "</td style='$style'>" ;
				/*print "<td>" ;
					print substr($row["account_number"], 5);
				print "</td>" ;
				print "<td>" ;
					print $row["officialname"] ;
				print "</td>" ;
				print "<td>" ;
					print $row["class"] ;
				print "</td>" ;*/
				print "<td style='text-align:right; $style'>" ;
					print $row["amount"] ;
				print "</td>" ;			
				print "<td style='$style'>" ;
				if($row['cheque_status_id']=='1'){
					echo "<button class='reject cButton' id='r$cheque_id'>Reject</button>";	
				}
				else if($row['cheque_status_id']=='0'){
					echo "Cheque Rejected/Bounced<br/>Reason: ".$row['reason'];
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
								print 'Total:';
							print "</td>" ;
							print "<td style='text-align:right;'>" ;
								printf("%.2f", $total_amount) ;
							print "</td>" ;
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
			
?>
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
<input type='hidden' id='processURL' value='<?=$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/process_cheque_status.php"?>'>
 <div  id='modal_sub_edit' class='modal' style="position:fixed; left:500px; top:250px; z-index:200; border:1px; padding:20px ; background-color :rgba(0,0,0, 0.6); color:white; width:300px; display:none;">
	<b>Reason:</b>
	<input type='hidden' id='rejectID' class='modalInput' style='width:180px;'>
	<input type='text' id='reason' class='modalInput' style='width:180px;'><br><br>
	<b>Bank Charge:</b>
	<input type='number' id='bankCharge' class='modalInput' style='width:180px;'><br><br>
	<div style='text-align: center; padding: 20px;'>
		<span class="cButton" id='rejectSubmit' style="padding: 10px 20px;">Submit</span>
		<span class='s_close cButton' style="padding: 10px 20px;">Close</span>
	</div>
</div> 
 <script src="<?php echo $_SESSION[$guid]["absoluteURL"] ;?>/modules/Fee/js/jquery.dataTables.min.js"></script>
 <script>
	 $(document).ready(function(){
		$('#myTable').DataTable();
		$('body').on('click', '.accept', function (){
			var id=$(this).prop('id').substr(1);
			var processURL=$('#processURL').val();
			$.ajax({
				type: "POST",
				url: processURL,
				data: {action:'accept',id:id},
				success: function(msg)
				{ 
					alert(msg);
					console.log(msg);
					window.location.reload();
				}
			});
		});
		$('body').on('click', '.reject', function (){
			$('#hide_body').show();
			$('#modal_sub_edit').show();
			var id=$(this).prop('id').substr(1);
			$('#rejectID').val(id);
		});
		$('#rejectSubmit').click(function(){
			var id=$('#rejectID').val();
			var reason=$('#reason').val();
			var bankCharge=$('#bankCharge').val();
			var processURL=$('#processURL').val();
			$.ajax({
				type: "POST",
				url: processURL,
				data: {action:'reject',id:id, bankCharge:bankCharge, reason:reason},
				success: function(msg)
				{ 
					alert(msg);
					console.log(msg);
					window.location.reload();
				}
			});
		});
		$('body').on('click','.s_close',function(){
			$('.modal').hide();
			$('#hide_body').hide();
			$('.modalInput').val('');
		});
		$('body').on('click', '.pending', function (){
			var id=$(this).prop('id').substr(1);
			var processURL=$('#processURL').val();
			$.ajax({
				type: "POST",
				url: processURL,
				data: {action:'pending',id:id},
				success: function(msg)
				{ 
					alert(msg);
					console.log(msg);
					window.location.reload();
				}
			});
		});
	});
 </script>
 <style>
 .accept, .reject, .pending {
    border: none;
    background-color: #ff731b;
    height: 28px;
    min-width: 55px;
    color: #ffffff;
    font-family: open_sanssemibold;
    font-weight: normal;
    margin: 2px;
    font-size: 14px;
    cursor: pointer;
    padding-left: 10px;
    padding-right: 10px;
}
 </style>

<?php
};
?>