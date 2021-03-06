<?php
include "../../config.php" ;
include '../../functions.php';
@session_start();
try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}
$currentDate=dateConvert($guid, $_GET["src_date"]) ;	
try {
	$sql="SELECT * FROM gibbonrollgroup WHERE gibbonRollGroupID=".$_REQUEST['scctionid'] ;
	$result=$connection2->prepare($sql);
	$result->execute();
	$section=$result->fetch() ;		
	
	$dataRollGroup=array("gibbonRollGroupID"=>$_REQUEST['scctionid']); 
	//$sqlRollGroup="SELECT * FROM gibbonstudentenrolment INNER JOIN gibbonperson ON gibbonstudentenrolment.gibbonPersonID=gibbonperson.gibbonPersonID WHERE gibbonRollGroupID=:gibbonRollGroupID AND status='Full' AND (dateStart IS NULL OR dateStart<='" . date("Y-m-d") . "') AND (dateEnd IS NULL  OR dateEnd>='" . date("Y-m-d") . "') ORDER BY rollOrder" ;
	if($_REQUEST['left']==1)
	{
	$sqlRollGroup="SELECT gibbonstudentenrolment.*,gibbonperson.account_number,gibbonperson.admission_number,gibbonperson.preferredName,gibbonperson.surname FROM gibbonstudentenrolment INNER JOIN gibbonperson ON gibbonstudentenrolment.gibbonPersonID=gibbonperson.gibbonPersonID WHERE gibbonRollGroupID=:gibbonRollGroupID AND status='Full' AND gibbonperson.dateStart<='".$currentDate."' ORDER BY rollOrder" ;
	}
	else 
	{
	$sqlRollGroup="SELECT gibbonstudentenrolment.*,gibbonperson.account_number,gibbonperson.admission_number,gibbonperson.preferredName,gibbonperson.surname FROM gibbonstudentenrolment INNER JOIN gibbonperson ON gibbonstudentenrolment.gibbonPersonID=gibbonperson.gibbonPersonID WHERE gibbonRollGroupID=:gibbonRollGroupID AND status='Full' AND gibbonperson.dateStart<='".$currentDate."' AND gibbonperson.dateEnd IS NULL ORDER BY rollOrder" ;
	}
		
	$resultRollGroup=$connection2->prepare($sqlRollGroup);
	$resultRollGroup->execute($dataRollGroup);
	}
	catch(PDOException $e) { 
		print "<div class='error'>" . $e->getMessage() . "</div>" ; 
	}
	?>
	<?php 
	if ($resultRollGroup->rowCount()<1) {
					print "<div class='error'>" ;
						print _("There are no records to display.") ;
					print "</div>" ;
				}
				else {
					$count=0 ;
					$countPresent=0 ;
					$columns=4 ;
					?>
<table width="50%" cellpadding="0" cellspacing="0" border="0">

  <tr>
    <td >Attendance Rerport for Class <?php echo $section['name'];?></td>
   
  </tr>
   <tr>
    <td >Date : <?php echo $_REQUEST['src_date'];?></td>
    
  </tr>
</table>

						<table class='smallIntBorder' cellspacing='0' style='width:100%' border="1">
						<tr class='break'>
							<th><b>Name</b></th>
							<th><b>Acc&nbsp;No</b></th>
							<th><b>Admn&nbsp;No</b></th>
							<th><b>Roll</b></th>
							<th><b>Sttatus</b></th>
							<th><b>Remark</b></th>
							<th><b>Comment</b></th>
						</tr>
						<?php
						while ($rowRollGroup=$resultRollGroup->fetch()) {
							if ($count%$columns==0) { ?>
								<tr>
							<?php }
							//Get student log data
							try {
								$dataLog=array("gibbonPersonID"=>$rowRollGroup["gibbonPersonID"], "date"=>$currentDate . "%"); 
								$sqlLog="SELECT * FROM gibbonattendancelogperson, gibbonperson WHERE gibbonattendancelogperson.gibbonPersonID=gibbonperson.gibbonPersonID AND gibbonattendancelogperson.gibbonPersonID=:gibbonPersonID AND date LIKE :date ORDER BY timestampTaken DESC" ;
								$resultLog=$connection2->prepare($sqlLog);
								$resultLog->execute($dataLog);
							}
							catch(PDOException $e) { 
								print "<div class='error'>" . $e->getMessage() . "</div>" ; 
							}
							
							$rowLog=$resultLog->fetch() ;
							
							/*
							if ($rowLog["type"]=="Absent") {
								print "<td style='border: 1px solid #CC0000!important; background: none; background-color: #F6CECB; width:20%; text-align: center; vertical-align: top'>" ;
							}
							else {
								print "<td style='border: 1px solid #ffffff; width:20%; text-align: center; vertical-align: top'>" ;
							}
							*/
							?>
							<td>
							<div style='padding-top: 5px'><?php echo formatName("", htmlPrep($rowRollGroup["preferredName"]), htmlPrep($rowRollGroup["surname"]), "Student", true);?></div>
							</td>
							<td>
							<?php echo substr($rowRollGroup["account_number"], 5);?>
							</td>
							<td>
							<?php echo substr($rowRollGroup["admission_number"], 5);?>
							</td>
							<td>
							<div style='padding-top: 5px'><?php echo $rowRollGroup["rollOrder"];?></div>
							</td>
							<td>
							<?php echo $rowLog["type"];?>
							</td>
							<td>
							<?php echo $rowLog["reason"];?>
							</td>
							<td>
							<?php echo $rowLog["comment"];?>
							</td>
							</tr>
								<?php 
								
								if ($rowLog["type"]=="Present" OR $rowLog["type"]=="Present - Late") {
									$countPresent++ ;
								}	
								
							$count++ ;
						}
						?>
						
						
						
					
						
						<tr>
							<td class='right' colspan=7>
								<div class='success'>
									<b>Total students:<?php echo $count;?></b><br/>
									
										<span title="Present or Present - Late">Total students present in room:<b><?php echo $countPresent;?></b><br/>
										<span title="not Present and not Present - Late">Total students absent from room: <b><?php echo $count-$countPresent;?></b><br/>
									
								</div>
							</td>
						</tr>
						<tr>
							<td  colspan="7" align="center">
								<input type="button" name="attandence_print" id="attandence_print" value='Print' onclick="return printFunction();">
							</td>
						</tr>
						</table>
					
				
				
	
	<?php 
				}
	?>
	
	<script type="text/javascript">
function printFunction()
{
	document.getElementById("attandence_print").style.display='none';
	window.print();
	}

function cancelFunction()
{
	window.close();
	}
</script>