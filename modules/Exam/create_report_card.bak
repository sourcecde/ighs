<?php
function create_report_card($studentID,$yearGroupID,$termID,$yearID){
	include "C:\wamp\www\lakshya\config.php";
	require_once('getAttendance.php');
	try {
		$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
		$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	}
	catch(PDOException $e) {
		echo $e->getMessage();
	}
	try{
		$sql1="SELECT `gibbonperson`.`preferredName`,`gibbonyeargroup`.`name` as class,`gibbonrollgroup`.`name` as section,`rollOrder`,
		`gibbonstudentenrolment`.`gibbonYearGroupID` as classID, `gibbonstudentenrolment`.`gibbonStudentEnrolmentID` as studentID, `gibbonstudentenrolment`.`gibbonRollGroupID`,
		`lakshya_exam_results`.`conduct` as conduct,`lakshya_exam_results`.`application` as application ,`lakshya_exam_results`.`remarks` as remarks,
		`lakshya_exam_results`.`Rank` as rank
		FROM `gibbonstudentenrolment` 
		LEFT JOIN `gibbonperson` on `gibbonperson`.`gibbonPersonID`=`gibbonstudentenrolment`.`gibbonPersonID` 
		LEFT JOIN `gibbonyeargroup` on `gibbonyeargroup`.`gibbonYearGroupID`=`gibbonstudentenrolment`.`gibbonYearGroupID` 
		LEFT JOIN `gibbonrollgroup` on `gibbonrollgroup`.`gibbonRollGroupID`=`gibbonstudentenrolment`.`gibbonRollGroupID` 
		LEFT JOIN `lakshya_exam_results` on `lakshya_exam_results`.`studentID`=`gibbonstudentenrolment`.`gibbonStudentEnrolmentID`
		WHERE `gibbonstudentenrolment`.`gibbonStudentEnrolmentID`=".$studentID;
		$result1=$connection2->prepare($sql1);
		$result1->execute();	
		$p=$result1->fetch();
	}
	catch(PDOException $e){
		echo $e;
	}
	try{
		$sql1="SELECT COUNT(`marksID`) AS N FROM `lakshya_exam_marks` WHERE `examID` IN 
			(SELECT `examID` FROM `lakshya_exam_master` WHERE `termID`=$termID AND `yearGroupID`=$yearGroupID)";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		$dData1=$result1->fetch();
	}
	catch(PDOException $e) { 
		echo $e;
	}
	try{
		$sql1="SELECT COUNT(`examID`) AS N FROM `lakshya_exam_master` WHERE `termID`=$termID AND `yearGroupID`=$yearGroupID";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		$dData2=$result1->fetch();
	}
	catch(PDOException $e) { 
		echo $e;
	}
	if($dData2['N']>0){
	try{
		$sql3="SELECT `lakshya_exam_master`.*,`s1`.`subjectName`,`s2`.`subjectName` AS parentSubjectName
				FROM `lakshya_exam_master`
				LEFT JOIN `lakshya_exam_subjects`  s1 ON `s1`.`subjectID`=`lakshya_exam_master`.`subjectID` 
				LEFT JOIN `lakshya_exam_subjects`  s2 ON `s2`.`subjectID`=`lakshya_exam_master`.`parentSubjectID` 
				WHERE `lakshya_exam_master`.`termID`= $termID AND `lakshya_exam_master`.`yearGroupID`=$yearGroupID";
		$result3=$connection2->prepare($sql3);
		$result3->execute();
		$examData=$result3->fetchall();
	}
	catch(PDOException $e){
		echo $e;
	}
	$examD=array();
	foreach($examData as $e){
		if($e['parentSubjectID']=='')
			$examD[$e['groupName']]['single'][$e['examID']]=$e;
		else
			$examD[$e['groupName']]['parent'][$e['parentSubjectName']][$e['examID']]=$e;
	}
	$eStr="";
	foreach($examData as $e){
		$eStr.=$eStr!=""?', ':'';
		$eStr.=$e['examID'];
	}
	try{
		$sql4="SELECT `theoryMarks`,`practicalMarks`,`gradeO`,`isabsent`,`examID`,`studentID` FROM `lakshya_exam_marks`
				LEFT JOIN `lakshya_exam_results` ON `lakshya_exam_results`.`resultsID`=`lakshya_exam_marks`.`resultsID`
				LEFT JOIN `gibbonstudentenrolment` ON `gibbonstudentenrolment`.`gibbonStudentEnrolmentID`=`lakshya_exam_results`.`studentID` 
				WHERE `gibbonstudentenrolment`.`gibbonStudentEnrolmentID`=$studentID AND `lakshya_exam_marks`.`examID` IN ($eStr)";
		$result4=$connection2->prepare($sql4);
		$result4->execute();
		$marksData=$result4->fetchall();
	}
	catch(PDOException $e){
		echo $e;
	}
	$marksD=array();
	foreach($marksData as $m){
		$marksD[$m['examID']]=$m;
	}
	try{
	$sql2="SELECT  `lakshya_exam_optionalsubjects`.*,`lakshya_exam_subjects`.`subjectName` FROM `lakshya_exam_optionalsubjects` 
	LEFT JOIN `lakshya_exam_subjects` ON `lakshya_exam_subjects`.`subjectID`=`lakshya_exam_optionalsubjects`.`subjectID`
	WHERE `examID` IN ($eStr) AND `studentID`=$studentID";
	$result2=$connection2->prepare($sql2);
	$result2->execute();
	$oData=$result2->fetchall();
	}
	catch(PDOException $e) { 
	echo $e;
	}
	$optData=array();
	foreach($oData as $o){
		$optData[$o['examID']]=$o;
	}
	}
	try{
	$sql2="SELECT  `name` FROM `gibbonschoolyearterm` WHERE `gibbonSchoolYearTermID`=".$termID;
	$result2=$connection2->prepare($sql2);
	$result2->execute();
	$term_name=$result2->fetch();
	}
	catch(PDOException $e) { 
	echo $e;
	}
	try{
	$sql2="SELECT  `name` FROM `gibbonschoolyear` WHERE `gibbonSchoolYearID`=".$yearID;
	$result2=$connection2->prepare($sql2);
	$result2->execute();
	$year_name=$result2->fetch();
	}
	catch(PDOException $e) { 
	echo $e;
	}
//-----------------------
	
	$scount=0;
	$grandTotalMarks=0;
	echo "<h4 style='text-align:center;'>".$term_name['name']." ".$year_name['name']."</h4>";
	echo "<div>";
	if($dData1['N']>0 && $dData2['N']>0){	
		echo "<table width='100%' style='padding:20px'>";
		echo "<tr>";
		echo "<td>";
		echo "Name : ".$p['preferredName'];
		echo "</td>";
		echo "<td>";
		echo "Class : ".$p['class'];
		echo "</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>";
		echo "Section : ".$p['section'];
		echo "</td>";
		echo "<td>";
		echo "Roll No. : ".$p['rollOrder'];
		echo "</td>";
		echo "</tr>";
		echo "</table>";
		$isgroup=0;
		$isPractical=0;
		$isParent=0;
		foreach($examD as $group=>$g){
			if($group!=''){
				$isgroup=1;
			}
			if(array_key_exists('single',$g)){
				foreach($g['single'] as $e){
					if($e['practical']=='Y'){
						$isPractical=1;	
					}
				}
			}
			if(array_key_exists('parent',$g)){
				$isParent=1;
			}
		}
		echo "<div style='min-height:17cm;max-height:19cm;'>";
		echo "<table width='100%' border='1' style='min-height:13cm;max-height:14.5cm;'>";
		if($isgroup==1){
			echo "<th style='text-align:center;'>Group</th>";
		}
		$max_width="";
		if($isPractical==1 && $isgroup==1 || $isgroup==1 && $isParent==1){$max_width="max-width:76px;";}
		echo "<th style='text-align:center;' colspan='2'>Subject Name</th>";
		echo "<th style='text-align:center;".$max_width."'>Total Marks</th>";
		echo "<th style='text-align:center;".$max_width."'>Pass Marks</th>";
		echo "<th style='text-align:center;".$max_width."'>Marks Scored</th>";
		if($isPractical==1 || $isParent==1){
			echo "<th style='text-align:center;".$max_width."'>Avg/Total Marks</th>";
		}
		foreach($examD as $group=>$g){
			echo "<tr>";
			if($isgroup==1){
				$grCount=count($g['single']);
				if(array_key_exists('parent',$g)){
					foreach($g['parent'] as $parent){
						$grCount+=count($parent);
						$grEnable=$grCount;
						foreach($parent as $e){
							if($e['practical']=='Y')
								$grCount+=1;
							if($e['grade']=='Y'){
								$grEnable-=1;
								$grCount=-1;
							}
						}
					}
				}
				if(array_key_exists('single',$g)){
					$grEnable=$grCount;
					foreach($g['single'] as $e){
						if($e['practical']=='Y')
							$grCount+=1;
						if($e['grade']=='Y'){
							$grEnable-=1;
							$grCount=-1;
						}
					}
				}
				if($grEnable>0){
				echo "<td rowspan='".$grCount."'>".$group."</td>";
				}
			}
			if(array_key_exists('parent',$g)){
				$i=0;
				foreach($g['parent'] as $parentName=>$parent){
					$parentTotal=0;
					$parentMarks=0;
					echo "<td rowspan='".count($parent)."'>".$parentName."</td>";
					foreach($parent as $e){
						$parentTotal+=$e['theoryTotalMarks'];
						$divisor=100/$e['theoryTotalMarks'];
						$parentMarks+=$marksD[$e['examID']]['theoryMarks']*$divisor;
					}
					$avgMarks=($parentMarks/$parentTotal)*100;
					$grandTotalMarks+=$avgMarks;
					$scount++;
					foreach($parent as $e){
						$eID=$e['examID'];
						if($e['grade']=='N'){
							$oName="";
							if(isset($optData[$eID]['subjectName'])){
								$oName=$optData[$eID]['subjectName'];
							}
							$sName=$e['optional']=='N'?$e['subjectName']:$e['optionalName']."(".$oName.")";
							echo "<td>".$sName."</td>";
							echo "<td style='width: 75px;text-align: center;'>".$e['theoryTotalMarks']."</td>";
							echo "<td style='width: 75px;text-align: center;'>".$e['theoryPassMarks']."</td>";
							echo "<td style='width: 75px;text-align: center;'>".$marksD[$eID]['theoryMarks']."</td>";
							if($i==0){
								echo "<td rowspan='".count($parent)."' style='width: 75px;text-align: center;'>".$avgMarks."</td>";	
								$i++;
							}
							echo "</tr>";
							echo "<tr>";
						}
					}
				}
			}
			if(array_key_exists('single',$g)){
				$i=0;
				foreach($g['single'] as $e){
					$eID=$e['examID'];
					if($e['grade']=='N'){
						$oName="";
						if(isset($optData[$eID]['subjectName'])){
								$oName=$optData[$eID]['subjectName'];
							}
						$sName=$e['optional']=='N'?$e['subjectName']:$e['optionalName']."(".$oName.")";
						$rowcolspan=$e['practical']=='Y'?" rowspan='2'":" colspan='2'";
						echo "<td".$rowcolspan.">".$sName."</td>";
						if($e['practical']=='Y'){
							echo "<td>Theory</td>";
						}
						echo "<td style='width: 75px;text-align: center;'>".$e['theoryTotalMarks']."</td>";
						echo "<td style='width: 75px;text-align: center;'>".$e['theoryPassMarks']."</td>";
						echo "<td style='width: 75px;text-align: center;'>".$marksD[$eID]['theoryMarks']."</td>";
						$totalMarks=intval($marksD[$eID]['theoryMarks']);
						if($e['practical']=='Y'){
							$totalMarks+=intval($marksD[$eID]['practicalMarks']);
							echo "<td rowspan='2' style='width: 75px;text-align: center;'>".$totalMarks."</td>";
							echo "<tr><td>Practical</td>";
							echo "<td style='width: 75px;text-align: center;'>".$e['practicalTotalMarks']."</td>";
							echo "<td style='width: 75px;text-align: center;'>".$e['practicalPassMarks']."</td>";
							echo "<td style='width: 75px;text-align: center;'>".$marksD[$eID]['practicalMarks']."</td>";
							echo "</tr>";
						}
						$divisor=100/($e['practicalTotalMarks']+$e['theoryTotalMarks']);
						$grandTotalMarks+=$totalMarks*$divisor;
						$scount++;
						if($e['practical']=='N' && $isPractical==1){
							echo "<td style='width: 75px;text-align: center;'>".$marksD[$eID]['theoryMarks']."</td>";
						}
						if($i<count($g['single'])-1){
							$i++;
							echo "</tr>";
							echo "<tr>";
						}
					}
				}
			}
		}
		$percent=number_format((float)($grandTotalMarks/$scount), 2, '.', '');
		echo "<td>Percentage : ".$percent." %</td>";
		echo "<td colspan='7'> Remarks: ".$p['remarks']."</td>";
		echo "<table border='1' width='100%' style='min-height:4cm;max-height:4.5cm;'><colgroup><col style='width: 33%' /><col style='width: 33%' /><col style='width: 33%' /></colgroup><tr>";
		echo "<td>";
		echo "Graded Subjects :- <br/><br/>";
		foreach($examD as $g){
			if(array_key_exists('single',$g)){
				foreach($g['single'] as $e){
					$eID=$e['examID'];
					if($e['grade']=='Y'){
						echo $e['subjectName']." : ".$marksD[$eID]['gradeO']."<br/>";
					}
				}
			}
		}
		echo "</td>";
		echo "<td>";
		echo "No. of School Days : ".getWorkingDays($p['gibbonRollGroupID'])."<br/><br/>";
		echo "No. of Days Attended :".getAttendance($p['studentID'])." <br/><br/>";
		$atPercent=number_format((float)((getAttendance($p['studentID'])/getWorkingDays($p['gibbonRollGroupID']))*100), 2, '.', '');
		echo "Attendance : ".$atPercent." %";
		echo "</td>";
		echo "<td>";
		echo "Conduct : ".$p['conduct']."<br/><br/>";
		echo "Application : ".$p['application']."<br/><br/>";
		$rank=($p['rank']==-1)?"N/A":(string)$p['rank'];
		echo "Rank : ".$rank;
		echo "</td>";
		echo "</tr></table>";
		echo "</tr>";
		echo "</table>";
		echo "</div>";
	}
	else{
		echo "<p>ERROR!!!MARKS/EXAMS ARE NOT SET FOR THIS CLASS/TERM!!</p>";
	}
	echo "</div>";
}
?>