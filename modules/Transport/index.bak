<?php
@session_start() ;
$gibbonPersonID=NULL ;
if (isset($_SESSION[$guid]["gibbonPersonID"])) {
	$gibbonPersonID=$_SESSION[$guid]["gibbonPersonID"] ;
}

$sql="SELECT * from transport_spot_price";
			$result=$connection2->prepare($sql);
			$result->execute();
			$dboutbut=$result->fetchAll();
			
?>

	<h3>Manage Drop Location:</h3>
<table width="80%" cellpadding="0" cellspacing="0" id='myTable'>
  <thead>
  <tr>
    <th>Location</th>
	<th>Distance &nbsp;&nbsp;&nbsp;<p><small>From School</small></p></th>
     <th><button id="add_drop_location" style="border:1px; padding:10px 10px; background:#ff731b; color:white; float:left;"><b>+ ADD</b></a></th>
  </tr>
   </thead>
<tbody>
  <?php foreach ($dboutbut as $value) { ?>
  <tr>
    <td><span id='location_<?=$value['transport_spot_price_id']?>'><?php echo $value['spot_name'];?></span></td>
    <td><span id='distance_<?=$value['transport_spot_price_id']?>'><?php echo $value['distance'];?></span></td>
    <td><a href="javascript:void(0)" class='drop_location_edit' id='<?=$value['transport_spot_price_id']?>'>Edit</a> | <a href="<?php echo $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/process_drop_location.php?event=delete&transport_spot_price_id=".$value['transport_spot_price_id']; ?>" onclick="return confirm('Are you sure you want to delete it?');">Delete</a></td>
  </tr>
  <?php } ?>
  </tbody>
</table>
 <script src="<?php echo $_SESSION[$guid]["absoluteURL"] ;?>modules/Transport/js/jquery.dataTables.min.js"></script>
 <script>
	 $(document).ready(function(){
		$('#myTable').DataTable();
	});
 </script>
 <input type='hidden' name='process_url' id='process_url' value='<?php echo $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/process_drop_location.php";?>'>
  <div id='hide_body'style='background-color :rgba(0,0,0, 0.7); width:100%; height:100%; position:fixed; left:0px; top:0px; z-index:100; display:none;'>
 </div>
<div  id='modal_v_add' class='modal_v' style="position:fixed; left:500px; top:250px; z-index:200; border:1px; padding:5px 10px; background-color :rgba(0,0,0, 0.6); color:white; width:300px; display:none;">
<div style="margin:20px;">
Location: <input type='text' id='location_add' style='width:180px;'><br><br>
Distance: <input type='text' id='distance_add' style='width:180px;'><br><br>
<button id='drop_location_add' style='border:1px; padding:10px; margin-left:50px;  background:#ff731b; color:white;'>Add</button>
<button class='v_close' style='border:1px; padding:10px; background:#ff731b; color:white;'>Close</button>
</div>
</div>

<div  id='modal_v_edit' class='modal_v' style="position:fixed; left:500px; top:250px; z-index:200; border:1px; padding:5px 10px; background-color :rgba(0,0,0, 0.6); color:white; width:300px; display:none;">
<div style="margin:20px;">
Location: <input type='text' id='location_edit' style='width:180px;'><br><br>
Distance: <input type='text' id='distance_edit' style='width:180px;'><br><br>
<button id='drop_location_update' style='border:1px; padding:10px; margin-left:50px;  background:#ff731b; color:white;'>Update</button>
<button class='v_close' style='border:1px; padding:10px; background:#ff731b; color:white;'>Close</button>
<input type='hidden' id='drop_location_update_id' value=''>
</div>
</div>