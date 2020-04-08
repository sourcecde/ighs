<?php
/* 
	This File Url:
	$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/ajax/book_data.php" ;
*/
@session_start() ;
//Including Global Functions & Dtabase Configuration.
include "../../../functions.php" ;
include "../../../config.php" ;

//New PDO DB connection
try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}
$search=$_REQUEST['search']['value'];
$offset=intval( $_REQUEST['start'] );
$limit=intval( $_REQUEST['length'] );

/* Query Part */
$collumn_name=array("`title`","`author`","`lakshya_library_publisher`.`publisher`","`lakshya_library_category`.`category`","COUNT(`bookID`) AS copy","SUM(`borrow`) AS borrowed","SUM(`lakshya_library_status`.`available`) as available","`publicationPlace`","`isbn`","`lakshya_library_booknamemaster`.`bookNameID`");
$collumn_order=array("`title`","`author`","`lakshya_library_publisher`.`publisher`","`lakshya_library_category`.`category`","copy","`publicationPlace`","`isbn`","`lakshya_library_booknamemaster`.`bookNameID`");
$table="`lakshya_library_booknamemaster`";
$key="bookNameID";
//Select Part
$sqlS="SELECT ".implode(",",$collumn_name);
//From Part
$sqlF=" FROM $table ";
//Join Part
$sqlJ=" LEFT JOIN `lakshya_library_publisher` ON `lakshya_library_publisher`.`publisherID`=`lakshya_library_booknamemaster`.`publisherID` ";
$sqlJ.=" LEFT JOIN `lakshya_library_category` ON `lakshya_library_category`.`categoryID`=`lakshya_library_booknamemaster`.`categoryID` ";
$sqlJ.=" LEFT JOIN `lakshya_library_bookmaster`ON `lakshya_library_bookmaster`.`bookNameID`=`lakshya_library_booknamemaster`.`bookNameID` ";
$sqlJ.=" LEFT JOIN `lakshya_library_status`ON `lakshya_library_status`.`statusID`=`lakshya_library_bookmaster`.`statusID` ";
//Where Part
$sqlW="  WHERE `lakshya_library_bookmaster`.`archive`=0  ";
	if($search!='')
		$sqlW.=" AND (`title` like :search OR `author` like :search OR `lakshya_library_publisher`.`publisher` like :search) ";
	if($_REQUEST['category']!='all')
		$sqlW.=" AND `lakshya_library_booknamemaster`.`categoryID`=".$_REQUEST['category'];
//Group By Part
$sqlG=" GROUP BY `lakshya_library_booknamemaster`.`bookNameID` ";
//Order By Part
$sqlO=" ORDER BY {$collumn_order[$_REQUEST['order'][0]['column']]} {$_REQUEST['order'][0]['dir']} ";
//Limit Part
$sqlL="";
if($limit!=-1)
	$sqlL=" LIMIT $offset,$limit ";
//Final Query
$sql=$sqlS.$sqlF.$sqlJ.$sqlW.$sqlG.$sqlO.$sqlL;
/* Query Part */
$result=$connection2->prepare($sql);
$result->execute(array(':search'=>'%'.$search.'%'));
$book=$result->fetchAll();
$book_data_array=array();
//For Total Value
$sql1="SELECT COUNT(`lakshya_library_booknamemaster`.`$key`) AS F $sqlF $sqlJ $sqlW ";
$result1=$connection2->prepare($sql1);
$result1->execute(array(':search'=>'%'.$search.'%'));
$filteredData=$result1->fetch();
$sql2="SELECT COUNT(`$key`) AS T $sqlF ";
$result2=$connection2->prepare($sql2);
$result2->execute();
$totalData=$result2->fetch();
foreach($book as $b){
	$url=$_SESSION[$guid]["absoluteURL"]."/index.php?q=/modules/".$_SESSION[$guid]["module"]."/book_details.php&bookNameID=".$b[$key];
$link="<a href='$url'>Details</a>";
$available=$b['available']-$b['borrowed'];
$book_data_array[]=array($b['title'],$b['author'],$b['publisher'],$b['category'],"($available/{$b['copy']})",$b['publicationPlace'],$b['isbn'],$link);
}
$json_data = array(
                "draw"            => intval( $_REQUEST['draw'] ),
                "recordsTotal"    => intval($totalData['T']),
                "recordsFiltered" => intval($filteredData['F']),
                "data"            => $book_data_array
            );
echo json_encode($json_data);
 ?>