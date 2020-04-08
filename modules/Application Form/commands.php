

////////////////O N    D A S H B O A R D///////////////////<br>
<?php  $sql="SELECT count(distinct `gibbonperson`.`gibbonPersonID`) as N,`gibbonyeargroup`.`nameShort` 
	FROM `gibbonstudentenrolment`
	LEFT JOIN `gibbonperson` ON `gibbonperson`.`gibbonPersonID`=`gibbonstudentenrolment`.`gibbonPersonID`
	LEFT JOIN `gibbonyeargroup` ON `gibbonyeargroup`.`gibbonYearGroupID`=`gibbonstudentenrolment`.`gibbonYearGroupID` 
	WHERE status='Full' AND `gibbonstudentenrolment`.`gibbonSchoolYearID`=$year_id ";
	//$sql.=" AND (dateStart IS NULL OR dateStart<='" . date("Y-m-d") . "')";
	$sql.=" AND (dateEnd IS NULL OR dateEnd>='" . date("Y-m-d") . "')";
	echo $sql.=" GROUP BY `gibbonyeargroup`.`gibbonYearGroupID`";
	
?>	<br>
///////////////H E A D E R////////////////////////<br>
<?php echo $sqlList="SELECT gibbonperson.gibbonPersonID, preferredName, surname, gibbonrollgroup.name AS name, 'Student' AS role FROM gibbonperson, gibbonstudentenrolment, gibbonrollgroup WHERE gibbonperson.gibbonPersonID=gibbonstudentenrolment.gibbonPersonID AND gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID AND status='FULL' AND (dateStart IS NULL OR dateStart<='" . date("Y-m-d") . "') AND (dateEnd IS NULL  OR dateEnd>='" . date("Y-m-d") . "') AND gibbonrollgroup.gibbonSchoolYearID=:gibbonSchoolYearID ORDER BY surname, preferredName" ;
?>