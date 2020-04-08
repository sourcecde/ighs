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
?>
<body onload="window.print()">
<?php
	$year=$_POST['year'];
	$account_no=$_POST['account_no'];
	$person_id=$_POST['person_id'];
	$class=$_POST['class'];
	$section=$_POST['section'];
	
	$sql1="SELECT gibbonperson.preferredName,gibbonyeargroup.name as class,gibbonrollgroup.name as section,rollOrder,gibbonperson.account_number FROM gibbonstudentenrolment " ;
	$sql1.=" LEFT JOIN gibbonperson on gibbonperson.gibbonPersonID=gibbonstudentenrolment.gibbonPersonID ";
	$sql1.=" LEFT JOIN gibbonyeargroup on gibbonyeargroup.gibbonYearGroupID=gibbonstudentenrolment.gibbonYearGroupID ";
	$sql1.=" LEFT JOIN gibbonrollgroup on gibbonrollgroup.gibbonRollGroupID=gibbonstudentenrolment.gibbonRollGroupID ";
	$sql1.=" where gibbonperson.gibbonPersonID NOT IN (SELECT `student_id` FROM `leftstudenttracker`) AND gibbonstudentenrolment.gibbonSchoolYearID=".$year;
	if($account_no!='')
		$sql1.=" AND gibbonperson.account_number=".$account_no;
	if($person_id!='')
		$sql1.=" AND gibbonperson.gibbonPersonID=".$person_id;
	if($class!='')
		$sql1.=" AND gibbonstudentenrolment.gibbonYearGroupID=".$class;
	if($section!='')
		$sql1.=" AND gibbonstudentenrolment.gibbonRollGroupID=".$section;
	$sql1.=" ORDER BY gibbonstudentenrolment.gibbonRollGroupID,rollOrder";
	$result1=$connection2->prepare($sql1);
	$result1->execute();	
	$data_arr=$result1->fetchall();
	
	$sql2="SELECT  `name` FROM `gibbonschoolyear` WHERE `gibbonSchoolYearID`=".$year;
	$result2=$connection2->prepare($sql2);
	$result2->execute();
	$year_name=$result2->fetch();

									$i=1;
									echo "<table width='100%' style='font-size:14px;'>";
									foreach($data_arr as $row){
										switch($_POST["term"]){
										case "1":
										$termStr="Half-Yearly";
										break;
										case "2":
										$termStr="Annual";
										break;
										}
										if($i%2!=0)
											echo "<tr><td width='50%'>";
											
											echo "<br><br><br><br>";
											if($i%2==0)
											    echo "&nbsp;&nbsp;&nbsp;&nbsp;";
											echo "&nbsp;&nbsp;&nbsp;&nbsp;<b>For ".$termStr." Examination ".$year_name["name"]."</b><br>";
											if($i%2==0)
											    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
											echo "&nbsp;&nbsp;&nbsp;&nbsp; A/c No.&nbsp;&nbsp;:&nbsp;".abs($row["account_number"])."<br>";
											if($i%2==0)
											    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";											
											echo "&nbsp;&nbsp;&nbsp;&nbsp; Name&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;".$row["preferredName"]."<br>";
											if($i%2==0)
											    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";											
											echo "&nbsp;&nbsp;&nbsp;&nbsp; Class&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;".$row["class"]."<br>";
											if($i%2==0)
											    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";											
											echo "&nbsp;&nbsp;&nbsp;&nbsp; Section&nbsp;&nbsp;&nbsp;:&nbsp;".substr($row["section"],-1)."<br>";
											if($i%2==0)
											    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";											
											echo "&nbsp;&nbsp;&nbsp;&nbsp; Roll No.&nbsp;:&nbsp;".$row["rollOrder"]."<br>";
									
                                            if(($i-5)%8==0 || ($i-6)%8==0){
                                                echo "<br>";
                                            }
                                            if(($i-3)%8==0 || ($i-4)%8==0){
                                                echo "<br><br>";
                                            }
                                            if(($i-1)%8==0 || ($i-2)%8==0){
                                                echo "<br>";
                                            }
                                            echo "<br><br><br><br><br>";
										//echo "<img src='create_admit.php?data=".$data."' style='padding: 10px 5px; width:47%;'>";
										
										if($i%2==0)
											echo "</td></tr>";
										else
											echo "</td><td width='50%'>";
										$i++;
									}									
									echo "</table>";
									echo "</body>";

?>
