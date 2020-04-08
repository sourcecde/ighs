<?php
include "../../config.php" ;
@session_start();
try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}
@session_start() ;
			$sql="SELECT * from transport_spot_price";
			$result=$connection2->prepare($sql);
			$result->execute();
			$dboutput=$result->fetchAll();	
			$data=array();
	foreach ($dboutput as $value) { 
		$spot=$value['spot_name'];
		$distance=$value['distance'];
		$price=$value['price'];
    //<a href="<?php //echo $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Transport/spot_price_edit.php&transport_spot_price_id=".$value['transport_spot_price_id'];">Edit</a> | <a href="<?php echo $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/spot_price_process.php?event=delete&transport_spot_price_id=".$value['transport_spot_price_id']; " onclick="return confirm('Are you sure you want to delete it?');">Delete</a></td>
	$data[]=array($spot,$distance,$price,"Test");
  } 
  $json_data = array(
                "draw"            => intval( $_REQUEST['draw'] ),
                "recordsTotal"    => intval( count($data) ),
                "recordsFiltered" => intval( count($data) ),
                "data"            => $data
            );
echo json_encode($json_data);
//print_r($_REQUEST);
?>