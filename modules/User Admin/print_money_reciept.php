<?php
include "../../config.php" ;
include "../../functions.php" ;
@session_start();
$alldate=array();
$filteredarray=array();
$total=0;
$alltotal=0;
try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}


$personid=$_GET['appllicationId'];
		
try {
	$sql="SELECT * FROM gibbonapplicationform WHERE gibbonApplicationFormID='".$personid."'";
	$result=$connection2->prepare($sql);
		$result->execute();
		$row=$result->fetch();
		
		$sqlSelect="SELECT * FROM gibbonyeargroup WHERE gibbonYearGroupID=".$row['gibbonYearGroupIDEntry'] ;
		$resultSelect=$connection2->prepare($sqlSelect);
		$resultSelect->execute();
		$class=$resultSelect->fetch();
		
		$sqlSelect="SELECT * FROM gibbonschoolyear WHERE gibbonSchoolYearID=".$row['gibbonSchoolYearIDEntry'];
		$resultSelect=$connection2->prepare($sqlSelect);
		$resultSelect->execute();
		$year=$resultSelect->fetch();
		
		$sqlSelect="SELECT * FROM gibbonsetting WHERE gibbonSystemSettingsID='00079'";
		$resultSelect=$connection2->prepare($sqlSelect);
		$resultSelect->execute();
		$amount=$resultSelect->fetch();
}	
catch(PDOException $e) {
  echo $e->getMessage();
}
$date=date("d-m-Y", strtotime($row['timestamp']));
?>
<div>
	<div class="print_header" style="text-align: center">
	<div><h2>CALCUTTA PUBLIC SCHOOL, RANCHI</h2></div>
	<div><h3>Ormanjhi, Jharkhand 835219</h3></div>
	</div>
	<div>Received Rs.<?php echo $amount['value'];?>.00 /- ( <?php echo convert_number_to_words($amount['value']); ?> Rupees only.) from</div> <br>
	<div>Master / Miss: <span id="candidate_name"> <?php echo $row['officialName']?></span></div><br>
	<div>Son / Daughter  of: <span id="parentname_name"> <?php echo $row['parent1officialName']." & ".$row['parent2officialName'];?></span></div><br>
	<div>being the registration charges for his/her admission in class <span id="class"><?php echo $class['name'];?></span> in the academic session <span id="academic_session"><?php echo $year['name']; ?></span>.</div>
	<br><br><br>
	<div style="float: left; margin-left:20px;">Date: <span id="enrollmentdate"><?php echo $date;?></span> </div>
	<div style="float: right; margin-right:20px;"> Authorised Signatory</div>
	<div id="receipt_print_button_area" style="margin-top: 40px;margin-left: 317px;"><input type="button" name="receipt_print_button" id="receipt_print_button" onclick="return PrintFucntion();" value="Print">
	<input type="button" name="receipt_close_button" id="receipt_close_button" value="Close" onclick="return CloseFunction();"></div>
</div>

<script type="text/javascript">
function PrintFucntion(){
	document.getElementById("receipt_print_button_area").style.display='none';
	window.print();
}

function CloseFunction(){
	window.close();
}

</script>