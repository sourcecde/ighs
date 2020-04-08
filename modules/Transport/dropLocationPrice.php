<?php
@session_start() ;
$gibbonPersonID=NULL ;
if (isset($_SESSION[$guid]["gibbonPersonID"])) {
	$gibbonPersonID=$_SESSION[$guid]["gibbonPersonID"] ;
}
		
$sql="SELECT `gibbonSchoolYearID`, `name`, `status` FROM `gibbonschoolyear`";
$result=$connection2->prepare($sql);
$result->execute();
$year=$result->fetchAll();	
?>
<table width='60%' style='border:1px solid #7030a0;'>
<tr>
<td>
<b>Select Year:</b>
<select id='schoolYearIDSpotprice'>
<?php foreach($year as $y){
$s=$y['status']=='Current'?'selected':'';
echo "<option value='{$y['gibbonSchoolYearID']}' $s>{$y['name']}</option>";	
}
 ?>
</select>
</td>
</tr>
</table>
<a href="<?php echo $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/". $_SESSION[$guid]["module"]."/add_drop_location_price.php";?>"  style="border:1px; padding:10px 10px; background:#ff731b; color:white; float:right;"><b>+ Add</b></a>
	<h3>Manage Drop Location Price:</h3>
<table width="80%" cellpadding="0" cellspacing="0" id='myTable'>
  <thead>
  <tr>
    <th>Location</th>
    <th>Price</th>
    <th>Action</th>
  </tr>
   </thead>
</table>
<input type='hidden' id='data_url' value='<?php echo $_SESSION[$guid]["absoluteURL"]."modules/".$_SESSION[$guid]["module"]."/ajaxTransportSpotPrice.php";?>'>
 <script src="<?php echo $_SESSION[$guid]["absoluteURL"] ;?>modules/Transport/js/jquery.dataTables.min.js"></script>
 <script>
	$(document).ready(function(){
		 var url=$('#data_url').val();
		var table=$('#myTable').DataTable({
        "ajax": {
			"type"	: "POST",
			"url"  	:url,
			"data"   : function( d ){d.yearID=$('#schoolYearIDSpotprice').val()}
			}
		});
		$('#schoolYearIDSpotprice').change(function(){
			table.ajax.reload();
		});
	});

 </script>
  <input type='hidden' name='process_url' id='process_url' value='<?php echo $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/process_location_price.php";?>'>
 <div id='hide_body'style='background-color :rgba(0,0,0, 0.7); width:100%; height:100%; position:fixed; left:0px; top:0px; z-index:100; display:none;'>
 </div>

<div  id='modal_v_edit' class='modal_v' style="position:fixed; left:500px; top:250px; z-index:200; border:1px; padding:5px 10px; background-color :rgba(0,0,0, 0.6); color:white; width:300px; display:none;">
<div style="margin:20px;">
Price: <input type='text' id='price_edit' style='width:180px;'><br><br>
<button id='location_price_update' style='border:1px; padding:10px; margin-left:50px;  background:#ff731b; color:white;'>Update</button>
<button class='v_close' style='border:1px; padding:10px; background:#ff731b; color:white;'>Close</button>
<input type='hidden' id='location_price_update_id' value=''>
</div>
</div>
