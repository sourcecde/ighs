<?php
@session_start() ;
$gibbonPersonID=NULL ;
if (isset($_SESSION[$guid]["gibbonPersonID"])) {
	$gibbonPersonID=$_SESSION[$guid]["gibbonPersonID"] ;
}

$sql="SELECT transport_route.*,vehicles.details,vehicles.type FROM transport_route LEFT JOIN vehicles ON transport_route.vehicle_id=vehicles.vehicle_id";
			$result=$connection2->prepare($sql);
			$result->execute();
			$dboutbut=$result->fetchAll();
$sql="SELECT * FROM vehicles";
			$result=$connection2->prepare($sql);
			$result->execute();
			$vehicles=$result->fetchAll();
			
?>
<h3>Manage Routes :</h3>
<table width="80%" cellpadding="0" cellspacing="0" id='myTable'>
<thead>
  <tr>
    <th>No.</th>
    <th>Route</th>
	<th>Vehicle</th>
     <th> <input type="button" id="add_route"  style='border:1px; padding:5px 20px;; background:#ff731b; color:white;' value="+ Add"></th>
  </tr>
  </thead>
  <tbody> 
  <?php $i=0; foreach ($dboutbut as $value) { ?> 
  <tr>
    <td><?php echo ++$i;?></td>
    <td><?php echo $value['route'];?><input type='hidden' id="route_<?php echo $value['route_id'];?>" value="<?php echo $value['route'];?>"></td>
	<td><?php echo $value['type']." - ".$value['details'];?></td>
    <td><a href="#" class="r_edit" id="<?php echo $value['route_id']."_".$value['route']."_".$value["vehicle_id"];?>">Edit</a> | 
		<a href="<?php echo $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/route_process.php?event=delete&route_id=".$value['route_id']; ?>" onclick="return confirm('Are you sure you want to delete it?');">Delete</a></td>
  </tr>
  <?php } ?>
</tbody>
</table>
<input type="hidden" id="r_edit_url" value='<?php echo $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/route_process.php";?>'>
<div  id='modal_r' class='modal_r' style="width:300px; height:200px; border:1px solid; position:fixed; z-index:100; top:250px; left:400px; background:rgba(0,0,0,0.4); color:white; display:none;">
<div style="margin:20px;">
<p>Route: <input type='text' id='route' style='width:180px;'></p><br>
<p>Vehicle: <select id='vehicle_id' style='width:180px;' required>
<option value=''>Select Vehicle</option>
<?php foreach($vehicles as $v){
	print "<option value={$v["vehicle_id"]}>{$v["type"]} - {$v["details"]}</option>";
}?>
</select></p><br>
<button id='r_add' style='border:1px; padding:10px; margin:50px;  background:#ff731b; color:white;'>Add</button>
<button class='r_close' style='border:1px; padding:10px; background:#ff731b; color:white;'>Close</button>
</div>
</div>

<div  id='modal_r_edit' class='modal_r' style="width:300px; height:200px; border:1px solid; position:fixed; z-index:100; top:250px; left:400px; background:rgba(0,0,0,0.4); color:white; display:none;">
<div style="margin:20px;">
<p>Route: <input type='text' id='route_e' style='width:180px;'><br></p>
<p>Vehicle: <select id='vehicle_id_e' style='width:180px;' required>
<option value=''>Select Vehicle</option>
<?php foreach($vehicles as $v){
	print "<option value={$v["vehicle_id"]}>{$v["type"]} - {$v["details"]}</option>";
}?>
</select></p><br>
<button id='r_update' style='border:1px; padding:10px; margin:50px;  background:#ff731b; color:white;'>Update</button>
<button class='r_close' style='border:1px; padding:10px; background:#ff731b; color:white;'>Close</button>
<input type='hidden' id='r_id_e'>
<input type='hidden' name='route_process_url' id='route_process_url' value='<?php echo $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/route_process.php";?>'>
</div>
</div>

 <script src="<?php echo $_SESSION[$guid]["absoluteURL"] ;?>modules/Transport/js/jquery.dataTables.min.js"></script>
 <script>
	 $(document).ready(function(){
		$('#myTable').DataTable();
	});
 </script>