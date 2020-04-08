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


try {
		$data=array('gibbonHouseID'=>$_REQUEST['gibbonHouseID']); 
		$sql="SELECT gibbonhouse.*,p1.officialName AS 'student',p2.officialName AS 'stuff',
		(SELECT COUNT(*) FROM gibbonperson WHERE gibbonperson.gibbonHouseID=gibbonhouse.gibbonHouseID AND gibbonperson.dateEnd IS NULL) AS 'total_student' 
		 FROM gibbonhouse LEFT 
JOIN gibbonperson AS p1 ON gibbonhouse.student_personid=p1.gibbonPersonID
JOIN gibbonperson AS p2 ON gibbonhouse.stuff_personid=p2.gibbonPersonID where gibbonhouse.gibbonHouseID=:gibbonHouseID ";
		
		$result=$connection2->prepare($sql);
		$result->execute($data);
		$header=$result->fetch();
		
		$sql="SELECT gibbonperson.gibbonPersonID, surname, preferredName,officialName,
		 gibbonyeargroup.nameShort AS yearGroup,
		 gibbonrollgroup.nameShort AS rollGroup,
		 gibbonperson.account_number,gibbonperson.admission_number,gibbonstudentenrolment.rollOrder 
		 FROM gibbonperson 
		 LEFT JOIN gibbonstudentenrolment ON (gibbonperson.gibbonPersonID=gibbonstudentenrolment.gibbonPersonID) 
		 LEFT JOIN gibbonschoolyear ON (gibbonstudentenrolment.gibbonSchoolYearID=gibbonschoolyear.gibbonSchoolYearID)  
		 LEFT JOIN gibbonyeargroup ON (gibbonstudentenrolment.gibbonYearGroupID=gibbonyeargroup.gibbonYearGroupID) 
		 LEFT JOIN gibbonrollgroup ON (gibbonstudentenrolment.gibbonRollGroupID=gibbonrollgroup.gibbonRollGroupID) 
		 WHERE gibbonperson.gibbonHouseID=:gibbonHouseID AND gibbonperson.dateEnd IS NULL AND gibbonschoolyear.status='Current' ORDER BY  gibbonyeargroup.gibbonYearGroupID,gibbonstudentenrolment.rollOrder " ;
		
		$result=$connection2->prepare($sql);
		$result->execute($data);
	}
	catch(PDOException $e) { 
		print "<div class='error'>" . $e->getMessage() . "</div>" ; 
	}
	?>
	<div style="padding:15px;"><div style="font-family:Arial, Helvetica, sans-serif; text-align:center; font-weight: bold;font-size: 18px; color:#000000; padding-bottom:6px; text-transform:uppercase;">House Name: <?php echo $header['name'];?></div>
	<div style="font-family:Arial, Helvetica, sans-serif; text-align:center; font-weight: bold;font-size: 18px; color:#000000; padding-bottom:6px; text-transform:uppercase;">House Incharge: <?php echo $header['stuff'];?></div>
	<div style="font-family:Arial, Helvetica, sans-serif; text-align:center; font-weight: bold;font-size: 18px; color:#000000; padding-bottom:12px; text-transform:uppercase;">House Captain: <?php echo $header['student'];?></div>
<table width="100%" cellpadding="6" cellspacing="0" style="border-left:1px solid #000000; border-top:1px solid #000000; font-family:Arial, Helvetica, sans-serif;" align="center">
<tr style="background:#dddddd;">
	<td align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Acc No</td>
	<td align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Admn No</td>
	<td align="left" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Name</td>
	<td align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Class</td>
	<td align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Section</td>
	<td align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;">Roll</td>
</tr>
<?php while ($row=$result->fetch()) {?>
<tr>
	<td align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;"><?php echo $row["account_number"];?></td>
	<td align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;"><?php echo $row["admission_number"];?></td>
	<td align="left" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;"><?php echo $row["officialName"];?></td>
	<td align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;"><?php echo $row["yearGroup"];?></td>
	<td align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;"><?php echo $row["rollGroup"];?></td>
	<td align="center" style="border-bottom:1px solid #000000; border-right:1px solid  #000000;font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 14px; color:#000000;"><?php echo $row["rollOrder"];?></td>
</tr>
<?php } ?>
</table>
<div style="text-align:center; padding-top:10px; padding-bottom:10px;"><input type="button" name="print_button" id="print_button" value="Print" onclick="return printfunction();" style="background-color: #ff731b; border:none;  color: #ffffff;   cursor: pointer;  font-size: 14px;    font-weight: 600;    height: 28px;    margin: 2px;    min-width: 55px;  padding-left: 10px;
    padding-right: 10px;">
  	<input type="button" name="close_button" id="close_button" value="Close"  style="background-color: #ff731b; border:none;  color: #ffffff;   cursor: pointer;  font-size: 14px;    font-weight: 600;    height: 28px;    margin: 2px;    min-width: 55px;  padding-left: 10px;
    padding-right: 10px;" onclick="return closefunction();"></div>
<script type="text/javascript">
function printfunction()
{
window.print();
	}

function closefunction()
{
window.close();
	}
</script></div>