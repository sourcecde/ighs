<?php
/*
Gibbon, Flexible & Open School System
Copyright (C) 2010, Ross Parker

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/
include "custom_funcions.php" ;

$sql="SELECT `gibbonstudentenrolment`.*,`new` FROM `gibbonperson`,`gibbonstudentenrolment` WHERE `gibbonperson`.`gibbonPersonID`=`gibbonstudentenrolment`.`gibbonPersonID` AND `gibbonperson`.`gibbonPersonID` NOT IN (SELECT `student_id` FROM `leftstudenttracker` WHERE `yearOfLeaving`<=26) AND `gibbonSchoolYearID`=26";
echo $sq;

$result=$connection2->prepare($sql);
$result->execute();
$students=$result->fetchAll();

$sql="DELETE FROM `fee_payable` WHERE gibbonSchoolYearID=23";
$result=$connection2->prepare($sql);
$result->execute();
/*echo "<pre>";
print_r($students);
echo "</pre>";*/
foreach($students as $s){
	
	//echo "<br>";
	//echo "1";
	$student_type=$s["new"]=="Y"?"new":"old";
	//echo "2";
	PopulateStudentPayableFee($s["gibbonPersonID"],$s["gibbonSchoolYearID"],$s["gibbonYearGroupID"],$s["gibbonRollGroupID"],$s["gibbonStudentEnrolmentID"],$connection2,$student_type);
}