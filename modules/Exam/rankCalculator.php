<?php
	@session_start() ;
	//Including Global Functions & Dtabase Configuration.
	//New PDO DB connection
	try {
		$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
		$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	}
	catch(PDOException $e) {
		echo $e->getMessage();
	}
	if(isset($_GET)){
		$termID=$_GET['termID'];
		$yearGroupID=$_GET['yearGroupID'];
		$yearID=$_GET['yearID'];
		try{
			$sql1="SELECT `gibbonStudentEnrolmentID` FROM `gibbonstudentenrolment` 
			LEFT JOIN `gibbonperson` ON `gibbonperson`.`gibbonPersonID`=`gibbonstudentenrolment`.`gibbonPersonID`
			WHERE `gibbonSchoolYearID`=$yearID AND `gibbonYearGroupID`=$yearGroupID AND `gibbonperson`.`dateEnd` IS NULL ORDER BY `rollOrder`";
			$result1=$connection2->prepare($sql1);
			$result1->execute();
			$students=$result1->fetchAll();
		}
		catch(PDOException $e) { 
			echo $e;
		}
		$eIDArr=array();
		foreach($students as $st){
			$eIDArr[]=$st['gibbonStudentEnrolmentID']+0;
		}
		$eIDs=implode(',',$eIDArr);
		try{
			$sql1="SELECT * FROM `lakshya_exam_master` WHERE `yearGroupID`=$yearGroupID AND `termID`=$termID";
			$result1=$connection2->prepare($sql1);
			$result1->execute();
			$edata=$result1->fetchAll();
		}
		catch(PDOException $e) { 
			echo $e;
		}
		$examData=array();
		foreach($edata as $e){
		if($e['parentSubjectID']=='')
			$examData['single'][]=$e;
		else
			$examData['parent'][$e['parentSubjectID']][]=$e;
		}
		try{
			$sql1="SELECT `lakshya_exam_marks`.*,`lakshya_exam_results`.`studentID` FROM `lakshya_exam_marks`  
					LEFT JOIN `lakshya_exam_results` ON `lakshya_exam_results`.`resultsID`=`lakshya_exam_marks`.`resultsID`  
					WHERE `lakshya_exam_results`.`studentID` IN ($eIDs)";
			$result1=$connection2->prepare($sql1);
			$result1->execute();
			$marksD=$result1->fetchAll();
		}
		catch(PDOException $e) { 
			echo $e;
		}
		$marksData=array();
		foreach($marksD as $m){
			$marksData[$m['studentID']][$m['examID']]=$m;
		}
		$percent_arr=array();
		foreach($students as $st){
			$isrank=1;
			$grandtotal=0;
			$grandtotalmarks=0;
			$enID=$st['gibbonStudentEnrolmentID']+0;
			if (array_key_exists('parent',$examData)) {
				foreach($examData['parent'] as $parentSubjectID=>$s){
					$deno=sizeOf($s);  //number of sub subjects
					$marks=0;
					$totalmarks=0;
					foreach($s as $ed){
						$eID=$ed['examID'];
						if($marksData[$enID][$eID]['isabsent']==0){
							if($ed['grade']=='N'){
								if($marksData[$enID][$eID]['theoryMarks']>=$ed['theoryPassMarks'] && $marksData[$enID][$eID]['practicalMarks']>=$ed['practicalPassMarks']){
									$theoryM=isset($marksData[$enID][$eID]['theoryMarks'])?intval($marksData[$enID][$eID]['theoryMarks']):0;
									$marks+=$theoryM;
									$practicalM=isset($marksData[$enID][$eID]['practicalMarks'])?intval($marksData[$enID][$eID]['practicalMarks']):0;
									$marks+=$practicalM;
								}
								else{
									$isrank=0;
								}
							}
							else{
								try{
									$sql1="SELECT `isfail` FROM `lakshya_exam_grade` WHERE `grade`='".$marksData[$enID][$eID]['gradeO']."'";
									$result1=$connection2->prepare($sql1);
									$result1->execute();
									$isfail=$result1->fetch();
								}
								catch(PDOException $e) { 
									echo $e;
								}
								if($isfail['isfail']==1){
									$isrank=0;
								}	
							}
						}
						else{
							$marks=0;
							$isrank=0;
						}
						$totalmarks+=$ed['theoryTotalMarks'];
						$totalmarks+=$ed['practicalTotalMarks'];
					}
					$avg=intval($marks/$deno);
					$grandtotalmarks+=$avg;
					$grandtotal+=intval($totalmarks/$deno);
				}
			}
			if (array_key_exists('single', $examData)) {
				foreach($examData['single'] as $ed){
					$eID=$ed['examID'];
					$marks=0;
					if($marksData[$enID][$eID]['isabsent']==0){
						if($ed['grade']=='N'){
							if($marksData[$enID][$eID]['theoryMarks']>=$ed['theoryPassMarks'] && $marksData[$enID][$eID]['practicalMarks']>=$ed['practicalPassMarks']){
									$theoryM=isset($marksData[$enID][$eID]['theoryMarks'])?intval($marksData[$enID][$eID]['theoryMarks']):0;
									$marks+=$theoryM;
									$practicalM=isset($marksData[$enID][$eID]['practicalMarks'])?intval($marksData[$enID][$eID]['practicalMarks']):0;
									$marks+=$practicalM;
								}
							else{
								$isrank=0;
							}
						}
						else{
							try{
								$sql1="SELECT `isfail` FROM `lakshya_exam_grade` WHERE `grade`='".$marksData[$enID][$eID]['gradeO']."'";
								$result1=$connection2->prepare($sql1);
								$result1->execute();
								$isfail=$result1->fetch();
							}
							catch(PDOException $e) { 
								echo $e;
							}
							if($isfail['isfail']==1){
								$isrank=0;
							}
						}
					}
					else{
						$marks=0;
						$isrank=0;
					}
					$grandtotalmarks+=$marks;
					$grandtotal+=$ed['theoryTotalMarks'];
					$grandtotal+=$ed['practicalTotalMarks'];
				}
			}
			if($isrank){
			$percent=number_format((float)($grandtotalmarks/$grandtotal) * 100, 2, '.', '');
			$percent_arr[$enID]=$percent;
			}
			else{
				$percent_Arr[$enID]=-1;
			}
		}
		arsort($percent_arr);
		$temp=reset($percent_arr);
		$enID=key($percent_arr);
		unset($percent_arr[$enID]);
		$rank=1;
		try{
		$sql1="UPDATE `lakshya_exam_results` SET `Rank`=".$rank." WHERE `studentID`='".$enID."' AND `termID`='".$termID."'";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		}
		catch(PDOException $e) { 
			echo $e;
		}
		foreach($percent_arr as $enID=>$percent){
			if($percent!=-1){
			if($percent!=$temp){
				$rank++;
				$temp=$percent;
			}
			}
			else
				$rank=-1;
			try{
			$sql1="UPDATE `lakshya_exam_results` SET `Rank`=".$rank." WHERE `studentID`='".$enID."' AND `termID`='".$termID."'";
			$result1=$connection2->prepare($sql1);
			$result1->execute();
			}
			catch(PDOException $e) { 
				echo $e;
			}
		}
		$url=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/viewMarks.php";
		echo "<script type='text/javascript'> document.location = '$url'; </script>";
	}
?>