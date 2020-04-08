<?php 
@session_start() ;

if (isActionAccessible($guid, $connection2, "/modules/Fee/collection_register.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {

$payemntmode='';
$startdate='';
$enddate='';
$result='';
$sql='';
$data='';
$year='';
$duration='';		
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

						if(isset($_POST['src_payment_mode']))
							if($_POST['src_payment_mode']!='')
								$payemntmode=$_POST['src_payment_mode'];

						if(isset($_POST['src_from_date']))
							if($_POST['src_from_date']!='')
								$startdate=$_POST['src_from_date'];
					
						if(isset($_POST['src_to_date']))
							if($_POST['src_to_date']!='')
								$enddate=$_POST['src_to_date'];
						if(isset($_POST['year_id']))
							if($_POST['year_id']!='')
								$year=$_REQUEST['year_id'];	
						if(isset($_POST['p_monthduration']))
							if($_POST['p_monthduration']!='')
								$duration=$_REQUEST['p_monthduration'];
					
}

?>
<form name="f1" id="f1" method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/collection_register.php" ?>">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td><input type="text" name="src_from_date" id="src_from_date" style="width: 100px;" value="<?php echo $startdate;?>" placeholder=" From Date.." required></td>
    
    <td><input type="text" name="src_to_date" id="src_to_date" style="width: 100px;" value="<?php echo $enddate;?>" placeholder=" To Date.." required></td>
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
		<td>
			<select name="p_monthduration" id="p_monthduration">
				<option value="">Select Duration</option>
				<option value="1" <?php if($duration=='1'){?> selected="selected"<?php } ?>>Jan - Mar</option>
				<option value="2" <?php if($duration=='2'){?> selected="selected"<?php } ?>>Apr - Dec</option>
			</select>
		</td>
	    <td><input type="submit" name="submit" id="submit" value="Search" ></td>
		<td>
		<select id='view_type' style='float:left'>
			<option>Short</option>>
			<option>Details</option>>
		</select>
	    <?php if($_POST){?>
	    <input type="button" id="print" name="collection_register_print" value="Print" style="float: right; background:seagreen; color:white; border:0px solid;">
		<?php } ?>
	    </td>
  </tr>
</table>
</form>
<?php 
if($_POST)
{?>

<div id="display_panel"></div>
<form id='printForm' method="POST" action="<?php echo $_SESSION[$guid]["absoluteURL"] . "/modules/Fee/collection_register_print.php";?>" target="_blank">
<input type='hidden' name='print_page' id='print_page' value=''>
</form>
<script>
	var url="<?php echo $_SESSION[$guid]["absoluteURL"] . "/modules/Fee/ajax_collection_register.php";?>";
	var startDate=$('#src_from_date').val();
	var endDate=$('#src_to_date').val();
	var yearID=$('#year_id').val();
	var paymentMode=$('#src_payment_mode').val();
	getData(startDate,endDate,yearID,paymentMode,$('#view_type').val(), $('#p_monthduration').val());
	$('#view_type').change(function(){
		getData(startDate,endDate,yearID,paymentMode,$('#view_type').val(), $('#p_monthduration').val());
	});
	function getData(startDate,endDate,yearID,paymentMode,viewType,duration){
			$.ajax
	 		({
	 			type: "POST",
	 			url: url,
	 			data: {action:'load_data',startDate:startDate,endDate:endDate,yearID:yearID,paymentMode:paymentMode,viewType:viewType,duration:duration},
	 			success: function(msg)
	 			{ 
	 				//console.log(msg);
	 				$('#display_panel').html(msg);
	 			}
	 			});
	} 
	$('#print').click(function(){
		printElem($('#display_panel').html());
		
	});
	function printElem(table)
	{	
		var mywindow = window.open('', 'PRINT', 'height=400,width=600');

		mywindow.document.write('<html><head></head><body >');
		mywindow.document.write(table);
		var style="<style> table tr { border: 1px solid; -webkit-column-break-inside: avoid; page-break-inside: avoid; break-inside: avoid; } .rightA{ text-align: right; }";
		style+=" .footerT td{ font-weight: bold; } table { border:1px solid #000000; } table th{ padding: 1px 5px; font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 12px; }";
		style+=" table td{ padding: 1px 5px; font-size:12px; color:#000000; } #short-table th{ border-width: 0 .5px 1px 0;border-style: solid; }";
		style+=" #short-table td{ border-bottom:.5px solid #000000; border-right:.5px solid #000000; } thead {display: table-header-group;}";
		style+= " #details-table tr{ padding: 1px 5px; border-width:.5px;border-style: solid; font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 12px; } #details-table thead tr th{ border-width: 1px 0; border-style: solid; } .border-bottom{ border-bottom:1px solid #000000; } </style>";
		mywindow.document.write(style);
		mywindow.document.write('</body></html>');

		mywindow.document.close(); // necessary for IE >= 10
		mywindow.focus(); // necessary for IE >= 10*/

		mywindow.print();
		//mywindow.close();

		return true;
	}
</script>
<?php
/*

<?php } */
}
?>
<script type="text/javascript">
		$(function() {
			$( "#src_from_date" ).datepicker({ dateFormat: 'dd/mm/yy' });
			$( "#src_to_date" ).datepicker({ dateFormat: 'dd/mm/yy' });
		});
</script>
<style>
.rightA{
	text-align: right;
}
.footerT td{
	font-weight: bold;
}
</style>

<?php
};
?>
