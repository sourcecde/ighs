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
    <th>Price</th>
     <th><a href="<?php echo $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Transport/spot_price_add.php";?>" id="add_spot_price_link" style="border:1px; padding:10px 10px; background:#ff731b; color:white; float:left;"><b>+ Add</b></a></th>
  </tr>
   </thead>

</table>
 <script src="<?php echo $_SESSION[$guid]["absoluteURL"] ;?>modules/Transport/js/jquery.dataTables.min.js"></script>
 <script>
	 $(document).ready(function(){
		$('#myTable').DataTable({
            "processing": true,
            "serverSide": true,
           // "ajax":"<?php echo $_SESSION[$guid]["absoluteURL"] ;?>modules/Transport/index_data_ajax.php"
		   "ajax":{
                url :"<?php echo $_SESSION[$guid]["absoluteURL"] ;?>modules/Transport/index_data_ajax.php",
				type: "post"
			}
		});
	});
 </script>
 <!--<tbody>
  <?php //foreach ($dboutbut as $value) { ?>
  <tr>
    <td><?php //echo $value['spot_name'];?></td>
    <td><?php //echo $value['distance'];?></td>
    <td><?php //echo $value['price'];?></td>
    <td><a href="<?php //echo $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Transport/spot_price_edit.php&transport_spot_price_id=".$value['transport_spot_price_id'];?>">Edit</a> | <a href="<?php //echo $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/spot_price_process.php?event=delete&transport_spot_price_id=".$value['transport_spot_price_id']; ?>" onclick="return confirm('Are you sure you want to delete it?');">Delete</a></td>
  </tr>
  <?php// } ?>
  </tbody>-->