<?php
@session_start() ;

if (isActionAccessible($guid, $connection2, "/modules/User Admin/rollover.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {


//Module includes
include "./modules/" . $_SESSION[$guid]["module"] . "/moduleFunctions.php" ;
if (isActionAccessible($guid, $connection2, "/modules/User Admin/rollover.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
	print "<div class='trail'>" ;
	print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . _("Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . _(getModuleName($_GET["q"])) . "</a> > </div><div class='trailEnd'>" . _('Rollover') . "</div>" ;
	print "</div>" ;
if(!$_POST){
	$nextYear=getNextSchoolYearID($_SESSION[$guid]["gibbonSchoolYearID"], $connection2) ;
		if ($nextYear==FALSE) {
			print "<div class='error'>" ;
			print _("The next school year cannot be determined, so this action cannot be performed.") ;
			print "</div>" ;
			}
		else {
			try {
				$dataNext=array("gibbonSchoolYearID"=>$nextYear); 
				$sqlNext="SELECT * FROM gibbonschoolyear WHERE gibbonSchoolYearID=:gibbonSchoolYearID" ;
				$resultNext=$connection2->prepare($sqlNext);
				$resultNext->execute($dataNext);
			}
			catch(PDOException $e) { 
				print "<div class='error'>" . $e->getMessage() . "</div>" ; 
			}
			if ($resultNext->rowCount()==1) {
				$rowNext=$resultNext->fetch() ;	
			}
			$nameNext=$rowNext["name"] ;
			?>
				<form method="post" action="">
					<table class='smallIntBorder' cellspacing='0' style="width: 100%">	
						<tr>
							<td colspan=2 style='text-align: justify'> 
								<?php
								print sprintf(_('By clicking the "Proceed" button below you will initiate the rollover from %1$s to %2$s.  This will change data in numerous tables across the system! %3$sYou are really, very strongly advised to backup all data before you proceed%4$s.'), "<b>" . $_SESSION[$guid]["gibbonSchoolYearName"] . "</b>", "<b>" . $nameNext. "</b>", "<span style=\"color: #cc0000\"><i>", "</i></span>") ;
								?>
							</td>
						</tr>
						<tr>
							<td style='color:red; font-weight:bold'>
								<input type='checkbox' name='confirmDelete'> Delete previous rollover data?
							</td>
						</tr>
						</tr>
						<tr>
							<td class="right" colspan=2>
								<input type="hidden" name="nextYear" value="<?php print $nextYear ?>">
								<input type="submit" value="Proceed" class='proceed'>
							</td>
						</tr>
					</table>
				</form>	
				<?php
		}
}
else{
	//print_r($_POST);
	$nextYearID=$_POST['nextYear'];
	$currentYearID=$_SESSION[$guid]["gibbonSchoolYearID"];
	if(isset($_POST['confirmDelete'])){
		$sqlEnrolID="SELECT `gibbonStudentEnrolmentID` FROM `gibbonstudentenrolment` WHERE `gibbonSchoolYearID`=$nextYearID AND `gibbonPersonID` IN (SELECT `gibbonPersonID` FROM `gibbonstudentenrolment` WHERE `gibbonSchoolYearID`=$currentYearID )";
		$result1=$connection2->prepare($sqlEnrolID);
		$result1->execute();
		$enrolID=$result1->fetchAll();
		if($result1->rowCount()>0){
			$enrolIDs=implode(",",array_column($enrolID, 'gibbonStudentEnrolmentID'));
			$sql2="DELETE FROM `fee_payable` WHERE `gibbonStudentEnrolmentID` IN ($enrolIDs)";
			$result2=$connection2->prepare($sql2);
			$result2->execute();
			
			$sql3="DELETE FROM `transport_month_entry` WHERE `gibbonStudentEnrolmentID` IN ($enrolIDs)";
			$result3=$connection2->prepare($sql3);
			$result3->execute();
			
			$sql4="DELETE FROM `gibbonstudentenrolment` WHERE `gibbonStudentEnrolmentID` IN ($enrolIDs)";
			$result4=$connection2->prepare($sql4);
			$result4->execute();
			
			$sql5="DELETE FROM `payment_master` WHERE `gibbonStudentEnrolmentID` IN ($enrolIDs)";
			$result5=$connection2->prepare($sql5);
			$result5->execute();
		}
	}
	print "<h3>RE-ENROLMENT OF OLD STUDENTS</h3>";
						try {
							$dataReenrol=array("gibbonSchoolYearID"=>$_SESSION[$guid]["gibbonSchoolYearID"]); 
							$sqlReenrol="SELECT gibbonperson.gibbonPersonID, account_number, preferredName, `gibbonyeargroup`.`sequenceNumber`,`gibbonyeargroup`.`name` as class,`gibbonrollgroup`.`name` as section, `gibbonstudentenrolment`.`rollOrder`,gibbonRollGroupIDNext 
								FROM gibbonperson 
								JOIN gibbonstudentenrolment ON (gibbonstudentenrolment.gibbonPersonID=gibbonperson.gibbonPersonID) 
								JOIN gibbonrollgroup ON (gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID) 
								JOIN gibbonyeargroup ON (gibbonstudentenrolment.gibbonYearGroupID=gibbonyeargroup.gibbonYearGroupID) 
								WHERE gibbonstudentenrolment.gibbonSchoolYearID=$currentYearID AND status='Full' AND (`gibbonperson`.`dateEnd` IS NULL OR `gibbonperson`.`dateEnd`>'".date('Y-m-d')."') AND `gibbonperson`.`gibbonPersonID` NOT IN (SELECT `gibbonstudentenrolment`.`gibbonPersonID` FROM `gibbonstudentenrolment` WHERE `gibbonstudentenrolment`.`gibbonSchoolYearID`=$nextYearID) 
								AND `gibbonperson`.`gibbonPersonID` NOT IN (SELECT `student_id` FROM `leftstudenttracker` WHERE 1) AND `gibbonyeargroup`.`gibbonYearGroupID` NOT IN (15,16,17,24,25,26,27,28,29)
								ORDER BY `gibbonrollgroup`.`gibbonRollGroupID`,`rollOrder`";
							//echo $sqlReenrol;
							$resultReenrol=$connection2->prepare($sqlReenrol);
							$resultReenrol->execute();
							$data=$resultReenrol->fetchAll();
						}
						catch(PDOException $e) { 
							print "<div class='error'>" . $e->getMessage() . "</div>" ; 
						}	
			$sqlClass="SELECT `gibbonYearGroupID`, `name`, `sequenceNumber` FROM `gibbonyeargroup` ";
			$resultClass=$connection2->prepare($sqlClass);
			$resultClass->execute();
			$classData=$resultClass->fetchAll();
			
	$url=$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/rolloverProcess.php";
	print "<form method='POST' action='$url'>";	
	print "<input type='hidden' name='nextYearID' id='nextYearID' value='$nextYearID'>";
	print "<table width='100%'>";
		print "<tr>";
			print "<th>Re-enrol</th>";
			//print "<th>Left</th>";
			print "<th>Name</th>";
			print "<th>Present Status</th>";
			print "<th>Next Class</th>";
			print "<th>Next Section</th>";
			//print "<th>Boarder Type</th>";
		print "</tr>";
	foreach($data as $d){
		$sequenceNumberNext=(int)$d['sequenceNumber']+1;
		$sqlSection="SELECT `gibbonRollGroupID`,`gibbonrollgroup`.`name`FROM `gibbonrollgroup`  JOIN gibbonyeargroup ON gibbonyeargroup.gibbonYearGroupID=gibbonrollgroup.gibbonYearGroupID WHERE `gibbonSchoolYearID`=$nextYearID AND sequenceNumber=".$sequenceNumberNext;
		$resultSection=$connection2->prepare($sqlSection);
		$resultSection->execute();
		$sectionData=$resultSection->fetchAll();
		$id=$d['gibbonPersonID']+0;
		print "<tr>";	
			print "<td><input type='checkbox' name='re_enrolID[]' value='$id' checked></td>";
			//print "<td><input type='checkbox' name='leftID[]' value='$id'></td>";
			print "<td>{$d['preferredName']}<br><small style='float:right'>Account No:".($d['account_number']+0)."</small></td>";
			print "<td>Section: {$d['class']} {$d['section']} <br><span style='float:right'> Roll: {$d['rollOrder']}</span></td>";
				$classSelector="";
				foreach($classData as $c){
					$s=$c['sequenceNumber']==$d['sequenceNumber']+1?'selected':'';
					$classSelector.="<option value='{$c['gibbonYearGroupID']}' $s>{$c['name']}</option>";
				}
			print "<td><select name='nextClass_$id' id='nextClass_$id' class='class_filter'>$classSelector</select></td>";
				$sectionSelector="";
				foreach($sectionData as $sd){
					$s=$sd['gibbonRollGroupID']==$d['gibbonRollGroupIDNext']?'selected':'';
					$sectionSelector.="<option value='{$sd['gibbonRollGroupID']}' $s>{$sd['name']}</option>";
				}
			print "<td><select name='nextSection_$id' id='nextSection_$id'>$sectionSelector</select></td>";
		print "</tr>";	
	}
		print "<tr>";
			print "<th colspan='6'><center><input type='submit' value='Proceed' class='proceed'></center></th>";
		print "</tr>";
	print "</table>";
	print "</form>";
}	
}
echo "<div id='loading' style='display:none; position:fixed; width:100%;height:100%; top:0px; left:0px;'>";
	echo "
			<div id='loading'>
				<center><h2>It will take a long time. Please wait......</h2></center>
                <ul class='bokeh'>
                    <li></li>
                    <li></li>
                    <li></li>
                </ul>
            </div>
		";
echo "</div>";
 ?>
 <input type="hidden" name="rollgroup_url" id="rollgroup_url" value="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajax_change_rollgroup.php" ?>">
 <script>
 $('.proceed').click(function(){
	 $('#loading').show();
 });
 $('.class_filter').change(function(){
	var yearID=$('#nextYearID').val();
	//alert(yearID);
	var classID=$(this).val();
	var id=$(this).attr("id").split("_");
	var url=$("#rollgroup_url").val();
	//alert("HULULU");
	$.ajax({
		type: "POST",
		url: url,
		data: {yearGroup: classID, schoolYear: yearID},
		success: function(msg)
		{
			console.log(msg);
			$("#nextSection_"+id[1]).empty().append(msg);
		}
	});
 });
 </script>
 
<?php
};
?>