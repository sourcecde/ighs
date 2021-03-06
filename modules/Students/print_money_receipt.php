<?php
include "../../config.php" ;
include "../../functions.php" ;
@session_start();
try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}


$personid=$_REQUEST['id'];
try {
		$dataChild=array("gibbonPersonID"=>$personid); 
		$sqlChild="SELECT gibbonperson.gibbonPersonID,enrollment_date, surname, preferredName,officialName, gibbonyeargroup.nameShort AS yearGroup, gibbonrollgroup.nameShort AS rollGroup,gibbonperson.account_number,gibbonstudentenrolment.rollOrder, gibbonstudentenrolment.gibbonSchoolYearID AS gibbonSchoolYearID 
		FROM gibbonperson 
		LEFT JOIN gibbonstudentenrolment ON (gibbonperson.gibbonPersonID=gibbonstudentenrolment.gibbonPersonID) 
		LEFT JOIN gibbonyeargroup ON (gibbonstudentenrolment.gibbonYearGroupID=gibbonyeargroup.gibbonYearGroupID) 
		LEFT JOIN gibbonrollgroup ON (gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID) 
		WHERE gibbonperson.gibbonPersonID=:gibbonPersonID " ;
		
		$resultChild=$connection2->prepare($sqlChild);
		$resultChild->execute($dataChild);
		$result=$resultChild->fetch();
		
		$sqlSelect="SELECT * FROM gibbonsetting WHERE gibbonSystemSettingsID='00079'";
		$resultSelect=$connection2->prepare($sqlSelect);
		$resultSelect->execute();
		$amount=$resultSelect->fetch();
		//gibbonstudentenrolment.gibbonSchoolYearID
		$sqlSelect="SELECT * FROM gibbonschoolyear WHERE gibbonSchoolYearID=".$result['gibbonSchoolYearID'];
		$resultSelect=$connection2->prepare($sqlSelect);
		$resultSelect->execute();
		$year=$resultSelect->fetch();
	}
	catch(PDOException $e) { 
		print "<div class='error'>" . $e->getMessage() . "</div>" ; 
	}
	
	$dataPersonid=array("gibbonPersonID"=>$personid); 
	$sql="SELECT * FROM gibbonfamily LEFT JOIN gibbonfamilychild ON gibbonfamilychild.gibbonFamilyID=gibbonfamily.gibbonFamilyID
	WHERE gibbonfamilychild.gibbonPersonID=:gibbonPersonID";
	$resultChild=$connection2->prepare($sql);
		$resultChild->execute($dataPersonid);
		$familyresult=$resultChild->fetch();

	$date=date("d-m-Y", strtotime($result['enrollment_date']));		

?>
<div>
	<div class="print_header" style="text-align: center">
	<div><h2>CALCUTTA PUBLIC SCHOOL, RANCHI</h2></div>
	<div style="margin-left: 115px;"><h3>Ormanjhi, Jharkhand 835219</h3></div>
	</div>
	<div>Received Rs. <?php echo $amount['value'];?>.00/-  (<?php echo convert_number_to_words($amount['value']); ?> Rupees only. ) from</div><br>
	<div>Master / Miss <span id="candidate_name"> <?php echo $result['officialName']?></span></div><br>
	<div>Son / Daughter  of <span id="parentname_name"> <?php echo $familyresult['name'];?></span></div><br>
	<div>being the registration charges for his/her admission in class <span id="class"><?php echo $result['yearGroup'];?></span> in the academic session <span id="academic_session"><?php echo $year['name']; ?></span>.</div>
	<br><br><br>
	<div style="float: left; margin-left:20px;">Date <span id="enrollmentdate"><?php echo $date;?></span> </div>
	<div style="float: right;margin-right: 20px;"> Authorised Signatory</div>
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