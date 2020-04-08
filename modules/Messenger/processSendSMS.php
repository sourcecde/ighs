<?php
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

@session_start() ;
if($_POST){
extract($_POST);
//print_r($_POST);
$sql="SELECT `name`, `phone` FROM `lakshyasmscc`";
$result=$connection2->prepare($sql);
$result->execute();
$SMSCc=$result->fetchAll();
$CcContacts=array();
foreach($SMSCc as $c){
	$CcContacts[]=$c['phone'];
}

/* ***Fetch Contact*** */

if($action=='fetchContact'){
$phoneDB=array();
$dataDB=array();
if(!empty($roles)){
	$role_ids=implode(',',$roles);
	$sql="SELECT `gibbonperson`.`gibbonPersonID`,`preferredName`,`phone1` FROM `gibbonperson` WHERE (`dateEnd` IS NULL OR `dateEnd`>'".date('Y-m-d')."') AND `phone1`!='' AND `gibbonRoleIDPrimary` IN ($role_ids)";
	$result=$connection2->prepare($sql);
	$result->execute();
	$data=$result->fetchAll();
	//print_r($data);
	foreach($data as $d){
		$dataDB[$d['gibbonPersonID']+0]=array('name'=>$d['preferredName'],'phone'=>$d['phone1']);
	}
}
if(!empty($rollGroups)){
	$roll_ids=implode(',',$rollGroups);
	$sql="SELECT `gibbonstudentenrolment`.`gibbonPersonID`,`gibbonperson`.`phone1` FROM `gibbonperson` LEFT JOIN `gibbonfamilyadult` ON `gibbonfamilyadult`.`gibbonPersonID`=`gibbonperson`.`gibbonPersonID` LEFT JOIN `gibbonfamilychild` ON `gibbonfamilychild`.`gibbonFamilyID`=`gibbonfamilyadult`.`gibbonfamilyID` LEFT JOIN `gibbonstudentenrolment` ON `gibbonstudentenrolment`.`gibbonPersonID`=`gibbonfamilychild`.`gibbonPersonID` WHERE `gibbonfamilyadult`.`contactPriority`=1 AND `gibbonstudentenrolment`.`gibbonRollGroupID` IN($roll_ids)";
	$result=$connection2->prepare($sql);
	$result->execute();
	$data=$result->fetchAll();
	foreach($data as $d){
		$phoneDB[$d['gibbonPersonID']+0]=$d['phone1'];
	}
	$sql="SELECT `gibbonperson`.`gibbonPersonID`,`preferredName` FROM `gibbonperson` LEFT JOIN `gibbonstudentenrolment` ON `gibbonstudentenrolment`.`gibbonPersonID`=`gibbonperson`.`gibbonPersonID` WHERE `gibbonstudentenrolment`.`gibbonRollGroupID` IN($roll_ids) AND (`gibbonperson`.`dateEnd` IS NULL  OR `gibbonperson`.`dateEnd`>='" . date("Y-m-d") . "')";
	$result=$connection2->prepare($sql);
	$result->execute();
	$data=$result->fetchAll();
	foreach($data as $d){
		if(array_key_exists($d['gibbonPersonID']+0,$phoneDB)){
			$dataDB[$d['gibbonPersonID']+0]=array('name'=>$d['preferredName'],'phone'=>$phoneDB[$d['gibbonPersonID']+0]);
		}
	}
	
}
if(!empty($transports)){
	$sqlFilter=" SELECT `gibbonPersonID` FROM `gibbonperson` ";
	if($transports[0]=='all')
		$sqlFilter.=" WHERE `active_transport`='Y' ";
	else {
		$id=implode(',',$transports);
		$sqlFilter.=" WHERE  `active_transport`='Y' AND `transport_spot_price_id` IN ($id) ";
	}
	$sqlFilter.="AND (`dateEnd` IS NULL OR `dateEnd`>'".date('Y-m-d')."')";
	$sql="SELECT `gibbonfamilychild`.`gibbonPersonID`,`phone1` FROM `gibbonperson` LEFT JOIN `gibbonfamilyadult` ON `gibbonfamilyadult`.`gibbonPersonID`=`gibbonperson`.`gibbonPersonID` LEFT JOIN `gibbonfamilychild` ON `gibbonfamilychild`.`gibbonFamilyID`=`gibbonfamilyadult`.`gibbonfamilyID`  WHERE `gibbonfamilyadult`.`contactPriority`=1 AND `gibbonfamilychild`.`gibbonPersonID` IN($sqlFilter)";
	$result=$connection2->prepare($sql);
	$result->execute();
	$data=$result->fetchAll();
	//print_r($data);
	foreach($data as $d){
		$phoneDB[$d['gibbonPersonID']+0]=$d['phone1'];
	}
	$sql="SELECT `gibbonperson`.`gibbonPersonID`,`preferredName` FROM `gibbonperson` WHERE `gibbonpersonID` IN ($sqlFilter) AND (`gibbonperson`.`dateEnd` IS NULL  OR `gibbonperson`.`dateEnd`>='" . date("Y-m-d") . "')";
	$result=$connection2->prepare($sql);
	$result->execute();
	$data=$result->fetchAll();
	//print_r($data);
	foreach($data as $d){
		
		if(array_key_exists($d['gibbonPersonID']+0,$phoneDB)){
			$dataDB[$d['gibbonPersonID']+0]=array('name'=>$d['preferredName'],'phone'=>$phoneDB[$d['gibbonPersonID']+0]);
		}
	}
	//print_r($dataDB);	
}

if(!empty($vehicles)){
	$sqlFilter=" SELECT `gibbonPersonID` FROM `gibbonperson` ";
	$id=implode(',',$vehicles);
	$sqlFilter.=" WHERE  `active_transport`='Y' AND `vehicle_id` IN ($id) ";
	$sqlFilter.="AND (`dateEnd` IS NULL OR `dateEnd`>'".date('Y-m-d')."')";
	
	$sql="SELECT `gibbonfamilychild`.`gibbonPersonID`,`phone1` FROM `gibbonperson` LEFT JOIN `gibbonfamilyadult` ON `gibbonfamilyadult`.`gibbonPersonID`=`gibbonperson`.`gibbonPersonID` LEFT JOIN `gibbonfamilychild` ON `gibbonfamilychild`.`gibbonFamilyID`=`gibbonfamilyadult`.`gibbonfamilyID`  WHERE `gibbonfamilyadult`.`contactPriority`=1 AND `gibbonfamilychild`.`gibbonPersonID` IN($sqlFilter)";
	$result=$connection2->prepare($sql);
	$result->execute();
	$data=$result->fetchAll();
	//print_r($data);
	foreach($data as $d){
		$phoneDB[$d['gibbonPersonID']+0]=$d['phone1'];
	}
	$sql="SELECT `gibbonperson`.`gibbonPersonID`,`preferredName` FROM `gibbonperson` WHERE `gibbonpersonID` IN ($sqlFilter) AND (`gibbonperson`.`dateEnd` IS NULL  OR `gibbonperson`.`dateEnd`>='" . date("Y-m-d") . "')";
	$result=$connection2->prepare($sql);
	$result->execute();
	$data=$result->fetchAll();
	//print_r($data);
	foreach($data as $d){
		
		if(array_key_exists($d['gibbonPersonID']+0,$phoneDB)){
			$dataDB[$d['gibbonPersonID']+0]=array('name'=>$d['preferredName'],'phone'=>$phoneDB[$d['gibbonPersonID']+0]);
		}
	}
	//print_r($dataDB);	
}
if($filter_studentID!=''){
/*	$id=$filter_studentID;
	$idS=implode(',',$id);
	
	$sql="SELECT `gibbonstudentenrolment`.`gibbonPersonID`,`phone1` FROM `gibbonperson` LEFT JOIN `gibbonfamilyadult` ON `gibbonfamilyadult`.`gibbonPersonID`=`gibbonperson`.`gibbonPersonID` LEFT JOIN `gibbonfamilychild` ON `gibbonfamilychild`.`gibbonFamilyID`=`gibbonfamilyadult`.`gibbonfamilyID` LEFT JOIN `gibbonstudentenrolment` ON `gibbonstudentenrolment`.`gibbonPersonID`=`gibbonfamilychild`.`gibbonPersonID` WHERE `gibbonfamilyadult`.`contactPriority`=1 AND `gibbonstudentenrolment`.`gibbonStudentEnrolmentID` IN($idS)";
	$result=$connection2->prepare($sql);
	$result->execute();
	$data=$result->fetchAll();
	//print_r($data);
	foreach($data as $d){
		$phoneDB[$d['gibbonPersonID']+0]=$d['phone1'];
	}
	$sql="SELECT `gibbonperson`.`gibbonPersonID`,`preferredName` FROM `gibbonperson` LEFT JOIN `gibbonstudentenrolment` ON `gibbonstudentenrolment`.`gibbonPersonID`=`gibbonperson`.`gibbonPersonID` WHERE `gibbonstudentenrolment`.`gibbonStudentEnrolmentID` IN ($idS) AND (`gibbonperson`.`dateEnd` IS NULL  OR `gibbonperson`.`dateEnd`>='" . date("Y-m-d") . "')";
	$result=$connection2->prepare($sql);
	$result->execute();
	$data=$result->fetchAll();
	foreach($data as $d){
		if(array_key_exists($d['gibbonPersonID']+0,$phoneDB)){
			$dataDB[$d['gibbonPersonID']+0]=array('name'=>$d['preferredName'],'phone'=>$phoneDB[$d['gibbonPersonID']+0]);
		}
	}
	
	*/
	
	
	$id=$filter_studentID;
	$idS=implode(',',$id);
    $sql="SELECT `gibbonperson`.`gibbonPersonID`,`gibbonperson`.`phone1` FROM `gibbonperson` where  `gibbonperson`.`gibbonPersonID` IN($idS)"; 
	$result=$connection2->prepare($sql);
	$result->execute();
	$data=$result->fetchAll();
    
	foreach($data as $d){
		$phoneDB[$d['gibbonPersonID']+0]=$d['phone1'];
	}
	
	$sql="SELECT `gibbonperson`.`gibbonPersonID`,`preferredName` ,`gibbonperson`.`phone1` FROM `gibbonperson` LEFT JOIN `gibbonstudentenrolment` ON `gibbonstudentenrolment`.`gibbonPersonID`=`gibbonperson`.`gibbonPersonID` WHERE `gibbonstudentenrolment`.`gibbonStudentEnrolmentID` IN ($idS) AND (`gibbonperson`.`dateEnd` IS NULL  OR `gibbonperson`.`dateEnd`>='" . date("Y-m-d") . "')";
	$result=$connection2->prepare($sql);
	$result->execute();
	$data1=$result->fetchAll();
	foreach($data1 as $d){
	    
		//if(array_key_exists($d['gibbonPersonID']+0,$phoneDB)){
			//[$d['gibbonPersonID']+0]=array('name'=>$d['preferredName'],'phone'=>$phoneDB[$d['gibbonPersonID']+0]);
			$dataDB[$d['gibbonPersonID']+0]=array('name'=>$d['preferredName'],'phone'=>$d['phone1']);   
		//}
	}
	
	
	
	
	
	
}
if($filter_staffID!=''){
	$id=$filter_staffID;
	$idS=implode(',',$id);
	$sql="SELECT `gibbonperson`.`gibbonPersonID`,`preferredName`,`phone1` FROM `gibbonperson` LEFT JOIN `gibbonstaff` ON `gibbonstaff`.`gibbonPersonID`=`gibbonperson`.`gibbonPersonID` WHERE (`dateEnd` IS NULL OR `dateEnd`>'".date('Y-m-d')."') AND `phone1`!='' AND `gibbonStaffID` IN($idS)";
	$result=$connection2->prepare($sql);
	$result->execute();
	$data=$result->fetchAll();
	//print_r($data);
	foreach($data as $d){
		$dataDB[$d['gibbonPersonID']+0]=array('name'=>$d['preferredName'],'phone'=>$d['phone1']);
	}

}
if($defaulter!=''){
/*	$id=$defaulter;
	$idS=implode(',',$id);
		$sql="SELECT `gibbonstudentenrolment`.`gibbonPersonID`,`phone1` FROM `gibbonperson` LEFT JOIN `gibbonfamilyadult` ON `gibbonfamilyadult`.`gibbonPersonID`=`gibbonperson`.`gibbonPersonID` LEFT JOIN `gibbonfamilychild` ON `gibbonfamilychild`.`gibbonFamilyID`=`gibbonfamilyadult`.`gibbonfamilyID` LEFT JOIN `gibbonstudentenrolment` ON `gibbonstudentenrolment`.`gibbonPersonID`=`gibbonfamilychild`.`gibbonPersonID` WHERE `gibbonfamilyadult`.`contactPriority`=1 AND `gibbonstudentenrolment`.`gibbonStudentEnrolmentID` IN($idS)";
	$result=$connection2->prepare($sql);
	$result->execute();
	$data=$result->fetchAll();
	//print_r($data);
	foreach($data as $d){
		$phoneDB[$d['gibbonPersonID']+0]=$d['phone1'];
	}
	$sql="SELECT `gibbonperson`.`gibbonPersonID`,`preferredName` FROM `gibbonperson` LEFT JOIN `gibbonstudentenrolment` ON `gibbonstudentenrolment`.`gibbonPersonID`=`gibbonperson`.`gibbonPersonID` WHERE `gibbonstudentenrolment`.`gibbonStudentEnrolmentID` IN ($idS) AND (`gibbonperson`.`dateEnd` IS NULL  OR `gibbonperson`.`dateEnd`>='" . date("Y-m-d") . "')";
	$result=$connection2->prepare($sql);
	$result->execute();
	$data=$result->fetchAll();
	foreach($data as $d){
		if(array_key_exists($d['gibbonPersonID']+0,$phoneDB)){
			$dataDB[$d['gibbonPersonID']+0]=array('name'=>$d['preferredName'],'phone'=>$phoneDB[$d['gibbonPersonID']+0]);
		}
	}
	
	
	*/
	
	
	
	$id=$defaulter;
	$idS=implode(',',$id);
    $sql="SELECT `gibbonperson`.`gibbonPersonID`,`gibbonperson`.`phone1` FROM `gibbonperson` where  `gibbonperson`.`gibbonPersonID` IN($idS)"; 
	$result=$connection2->prepare($sql);
	$result->execute();
	$data=$result->fetchAll();
    
	foreach($data as $d){
		$phoneDB[$d['gibbonPersonID']+0]=$d['phone1'];
	}
	
	$sql="SELECT `gibbonperson`.`gibbonPersonID`,`preferredName` ,`gibbonperson`.`phone1` FROM `gibbonperson` LEFT JOIN `gibbonstudentenrolment` ON `gibbonstudentenrolment`.`gibbonPersonID`=`gibbonperson`.`gibbonPersonID` WHERE `gibbonstudentenrolment`.`gibbonStudentEnrolmentID` IN ($idS) AND (`gibbonperson`.`dateEnd` IS NULL  OR `gibbonperson`.`dateEnd`>='" . date("Y-m-d") . "')";
	$result=$connection2->prepare($sql);
	$result->execute();
	$data1=$result->fetchAll();
	foreach($data1 as $d){
	    
		//if(array_key_exists($d['gibbonPersonID']+0,$phoneDB)){
			//[$d['gibbonPersonID']+0]=array('name'=>$d['preferredName'],'phone'=>$phoneDB[$d['gibbonPersonID']+0]);
			$dataDB[$d['gibbonPersonID']+0]=array('name'=>$d['preferredName'],'phone'=>$d['phone1']);   
		//}
	}
	
	
	
	
}
if($new_admission!=''){
/*	$id=$new_admission;
	$idS=implode(',',$id);
		$sql="SELECT `gibbonstudentenrolment`.`gibbonPersonID`,`phone1` FROM `gibbonperson` LEFT JOIN `gibbonfamilyadult` ON `gibbonfamilyadult`.`gibbonPersonID`=`gibbonperson`.`gibbonPersonID` LEFT JOIN `gibbonfamilychild` ON `gibbonfamilychild`.`gibbonFamilyID`=`gibbonfamilyadult`.`gibbonfamilyID` LEFT JOIN `gibbonstudentenrolment` ON `gibbonstudentenrolment`.`gibbonPersonID`=`gibbonfamilychild`.`gibbonPersonID` WHERE `gibbonfamilyadult`.`contactPriority`=1 AND `gibbonstudentenrolment`.`gibbonStudentEnrolmentID` IN($idS)";
	$result=$connection2->prepare($sql);
	$result->execute();
	$data=$result->fetchAll();
	//print_r($data);
	foreach($data as $d){
		$phoneDB[$d['gibbonPersonID']+0]=$d['phone1'];
	}
	$sql="SELECT `gibbonperson`.`gibbonPersonID`,`preferredName` FROM `gibbonperson` LEFT JOIN `gibbonstudentenrolment` ON `gibbonstudentenrolment`.`gibbonPersonID`=`gibbonperson`.`gibbonPersonID` WHERE `gibbonstudentenrolment`.`gibbonStudentEnrolmentID` IN ($idS) AND (`gibbonperson`.`dateEnd` IS NULL  OR `gibbonperson`.`dateEnd`>='" . date("Y-m-d") . "')";
	$result=$connection2->prepare($sql);
	$result->execute();
	$data=$result->fetchAll();
	foreach($data as $d){
		if(array_key_exists($d['gibbonPersonID']+0,$phoneDB)){
			$dataDB[$d['gibbonPersonID']+0]=array('name'=>$d['preferredName'],'phone'=>$phoneDB[$d['gibbonPersonID']+0]);
		}
	}
*/

    $id=$new_admission;
	$idS=implode(',',$id);
    $sql="SELECT `gibbonperson`.`gibbonPersonID`,`gibbonperson`.`phone1` FROM `gibbonperson` where  `gibbonperson`.`gibbonPersonID` IN($idS)"; 
	$result=$connection2->prepare($sql);
	$result->execute();
	$data=$result->fetchAll();
    
	foreach($data as $d){
		$phoneDB[$d['gibbonPersonID']+0]=$d['phone1'];
	}
	
	$sql="SELECT `gibbonperson`.`gibbonPersonID`,`preferredName` ,`gibbonperson`.`phone1` FROM `gibbonperson` LEFT JOIN `gibbonstudentenrolment` ON `gibbonstudentenrolment`.`gibbonPersonID`=`gibbonperson`.`gibbonPersonID` WHERE `gibbonstudentenrolment`.`gibbonStudentEnrolmentID` IN ($idS) AND (`gibbonperson`.`dateEnd` IS NULL  OR `gibbonperson`.`dateEnd`>='" . date("Y-m-d") . "')";
	$result=$connection2->prepare($sql);
	$result->execute();
	$data1=$result->fetchAll();
	foreach($data1 as $d){
	    
		//if(array_key_exists($d['gibbonPersonID']+0,$phoneDB)){
			//[$d['gibbonPersonID']+0]=array('name'=>$d['preferredName'],'phone'=>$phoneDB[$d['gibbonPersonID']+0]);
			$dataDB[$d['gibbonPersonID']+0]=array('name'=>$d['preferredName'],'phone'=>$d['phone1']);   
		//}
	}
	
}
echo json_encode(array($dataDB,$SMSCc));
}

/*    ****Send SMS****  */

else if($action=='sendSMS'){
	//print_r($_POST);
	$contact_data='';
	$i=0;
	//var_dump($_POST['contact_data']);
	$dataContact=json_decode($_POST['contact_data'],true);
	foreach($dataContact as $k=>$v){
		if($i++!=0)
			$contact_data.=",";
		$contact_data.=$v['phone'];
	}
	$smsUsername=getSettingByScope( $connection2, "Messenger", "smsUsername" ) ;
	$smsPassword=getSettingByScope( $connection2, "Messenger", "smsPassword" ) ;
	$smsURL=getSettingByScope( $connection2, "Messenger", "smsURL" ) ;
	/*$senderID="TEST SMS"; 
	$contact_data.=",".implode(',',$CcContacts);
	$post_data="user=$smsUsername:$smsPassword&senderID=".$senderID."&receipientno=".$contact_data."&dcs=0&msgtxt=". rawurlencode($message_data) ."&state=1" ;        
	
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,  $smsURL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	$result = curl_exec($ch);
	curl_close($ch);
	echo $smsURL."<br>".$post_data;
	
	echo "Result: $result<br>";
	$t1=explode(',',$result);
	//$t2=explode('=',$t1[0]);
	//$result_status=$t2[1];*/

	// Config variables. Consult http://api.textlocal.in/docs for more info.
	$test = "0";

	// Data for text message. This is the text message data.
	//$sender = "TXTLCL"; // This is who the message appears to be from.
	$sender = "CPSBAG";
	// 612 chars or less
	// A single number or a comma-seperated list of numbers
	$message ="Dear Parents, ".urlencode($message_data);
	$data = "username=".$smsUsername."&hash=".$smsPassword."&message=".rawurlencode($message)."&sender=".$sender."&numbers=".$contact_data."&test=".$test;
	$ch = curl_init($smsURL);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch); // This is the result from the API
	curl_close($ch);
	$obj = json_decode($result,true);
	echo "<pre>";
	print_r($obj);
	echo "</pre>";
	if($obj["status"]=="success"){
			$senderID=$_SESSION[$guid]['gibbonPersonID'];
			$timestamp=time();
			$count=count($dataContact);
			$sql="INSERT INTO `lakshyasmslog`(`SMSLogID`,`status`, `subject`, `message`, `senderPersonID`, `sendingTime`,`count`) VALUES (NULL,'{$obj['status']}_{$obj['batch_id']}','$subject_data','$message','$senderID','$timestamp','$count')";
			$result=$connection2->prepare($sql);
			$result->execute();
			$id=$connection2->lastInsertId();
			
			$sql1="INSERT INTO `lakshyasmsrecipients`(`id`, `SMSLogID`, `personID`, `phone`,`status`,`success`) VALUES ";
			$j=0;
			$count=0;
			foreach($dataContact as $k=>$v){
			$apiID=searchForId("91".$v["phone"],$obj["messages"]);
			$reason=searchForId1("91".$v["phone"],$obj["warnings"]);
			if($apiID!=null){
				//$count++;
				if($j++!=0)
					$sql1.=" ,";
				$sql1.="(NULL,'$id','$k','{$v['phone']}','$apiID','1')";
			}
			else if($reason!=null){
				$count++;
				if($j++!=0)
					$sql1.=" ,";
				$sql1.="(NULL,'$id','$k','{$v['phone']}','$reason','0')";
			}
			else{
				if($j++!=0)
					$sql1.=" ,";
				$sql1.="(NULL,'$id','$k','{$v['phone']}','Invalid Number','0')";
			}
			}
			echo $count;
			$result1=$connection2->prepare($sql1);
			$result1->execute();
	
	$URL=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Messenger/sendSMS.php&message=sms successfully sent" ;
	header("Location: {$URL}") ;
	}
}
}
function searchForId($id, $array) {
   foreach ($array as $key => $val) {
       if ($val['recipient'] == $id) {
           return $val['id'];
       }
   }
   return null;
}	
function searchForId1($id, $array) {
   foreach ($array as $key => $val) {
	   if(array_key_exists("numbers",$val)){
	//echo $val["numbers"]."=".$id."<br>";
       if ($val["numbers"] == $id) {
		   echo $val["message"]."<br>";
           return $val["message"];
       }
	   }
   }
   return null;
}
 ?>