<?php
include "../../config.php" ;
require_once('create_report_card.php');
@session_start();
try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}
?>
<?php
if($_POST){
	$year=$_POST['year'];
	$class=$_POST['class'];
	$term=$_POST['term'];
	if(isset($_POST['student_id'])){
	$studentID=$_POST['student_id'];
	$pStr="";
	for($i=0;$i<count($studentID);$i++){
		$pStr.=$pStr!=""?', ':'';
		$pStr.=$studentID[$i];
	}
	//echo $pStr;
	}
	try{
		$sql1="SELECT `gibbonStudentEnrolmentID`,`gibbonYearGroupID` FROM `gibbonstudentenrolment` 
		WHERE `gibbonstudentenrolment`.`gibbonSchoolYearID`=".$year;
		if(isset($_POST['student_id']))
			$sql1.=" AND `gibbonstudentenrolment`.`gibbonStudentEnrolmentID` IN ($pStr)";
		if($class!='')
			$sql1.=" AND `gibbonstudentenrolment`.`gibbonYearGroupID`=".$class;
		$sql1.=" ORDER BY `gibbonstudentenrolment`.`gibbonYearGroupID`,`gibbonstudentenrolment`.`gibbonRollGroupID`,`gibbonstudentenrolment`.`rollOrder`";
		//echo $sql1;
		$result1=$connection2->prepare($sql1);
		$result1->execute();	
		$pdata=$result1->fetchall();
	}
	catch(PDOException $e){
		echo $e;
	}
	foreach($pdata as $p){
		echo "<div name='reportCard' style='min-height:24.7cm;padding-top:5cm;'>";
		create_report_card($p['gibbonStudentEnrolmentID'],$p['gibbonYearGroupID'],$term,$year);
		echo "</div>";
	}
}
?>