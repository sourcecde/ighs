<?php
@session_start() ;
include "../../functions.php" ;
include "../../config.php" ;

//New PDO DB connection
try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}
if(isset($_REQUEST['p_month']) && isset($_REQUEST['p_year']) ){
		$month=$_REQUEST['p_month'];
		$year=$_REQUEST['p_year'];
		$month_array=array('January','February','March','April','May','June','July','August','September','October','November','December');
		$condition= "/$month/$year";
		$sql="SELECT `gibbonStaffID`,`date`,`type` FROM `lakshyastaffattendancelog` WHERE `date` like '__{$condition}'";
		$result=$connection2->prepare($sql);
		$result->execute();
		$data=$result->fetchAll();
		$data_array=array();
		foreach($data as $d){
		$data_array[$d['gibbonStaffID']+0][(substr($d['date'],0,2)+0)]=$d['type'];
		}
		
		$sql1="SELECT gibbonstaff.gibbonStaffID,gibbonstaff.type,gibbonperson.preferredName FROM gibbonstaff
				LEFT JOIN gibbonperson on gibbonperson.gibbonPersonID=gibbonstaff.gibbonPersonID";
		$sql1.=" WHERE (dateEnd IS NULL  OR dateEnd>='" . date("Y-m-d") . "')";	
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		$staff=$result1->fetchAll();
		
		$numOfDays=cal_days_in_month(CAL_GREGORIAN,$month,$year);
		
		?>
		<table width="100%" cellpadding="2" cellspacing="0" border="0">
		 <tr>
			<th align="center" style="font-family:Arial, Helvetica, sans-serif; font-size:25px; color:#000000;">Indra Gopal High School</th>
		  </tr>
		  <tr>
			<td align="center" style="font-family:Arial, Helvetica, sans-serif; font-size:20px; color:#000000;">Jheel Bagan, Hatiara, Baguiati, Kolkata-700157</td>
		  </tr>
		  <tr>
			<td align="center" style="font-family:Arial, Helvetica, sans-serif; font-size:20px; color:#000000;">Staff Attendance Report for Month: <?=$month_array[$month+0]?> of Year:<?=$year?> </td>
		  </tr>
		</table><br>
		<table width="100%" cellpadding="0" cellspacing="0" style="border: 1px solid black; border-collapse: collapse;">
		<thead>
		<tr style="border: 1px solid black; border-collapse: collapse;">
			<th style='border: 1px solid black; border-collapse: collapse;'>Staff Name</th>
			<?php for($i=1;$i<=$numOfDays;$i++){ echo "<th style='border: 1px solid black; border-collapse: collapse;'>$i</th>"; } ?>
			<th style='border: 1px solid black; border-collapse: collapse;'>Pr.</th>
			<th style='border: 1px solid black; border-collapse: collapse;'>Ab.</th>
		</tr>
		</thead>
		<?php
		foreach($staff as $s){
			if(!array_key_exists($s['gibbonStaffID']+0,$data_array))
				continue;
			$pr=0;
			$ab=0;
			echo "<tr class='a_row'>";
				echo "<td style='border: 1px solid black; border-collapse: collapse;'>{$s['preferredName']}<br><small>{$s['type']}</small></td>";
				for($i=1;$i<=$numOfDays;$i++){
					echo "<td style='border: 1px solid black; border-collapse: collapse; width:25px; padding:2px;'>";
						if(array_key_exists($i,$data_array[$s['gibbonStaffID']+0])) {
							echo $data_array[$s['gibbonStaffID']+0][$i];
							if($data_array[$s['gibbonStaffID']+0][$i]=='P')
								$pr++;
							else if($data_array[$s['gibbonStaffID']+0][$i]!='')
								$ab++;
						}
					echo "</td>";
				}
				echo "<td style='border: 1px solid black; border-collapse: collapse; width:25px; padding:2px;'><span>$pr</span></td>";
				echo "<td style='border: 1px solid black; border-collapse: collapse; width:25px; padding:2px;'>$ab</td>";
			echo "</tr style='display:none'>";
		}
}
?>
<script type="text/javascript" src="<?=$_SESSION[$guid]["absoluteURL"]?>/lib/jquery/jquery.js"></script>
<script>
$('.a_row').each(function(){
	//if($(this).find('span').text()=='0')
		//$(this).css('display','none');
})
window.print();
</script>