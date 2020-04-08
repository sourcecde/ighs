<?php
include "../../config.php" ;
include "../../functions.php" ;
try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}
//print_r($_POST);
extract($_POST);
if($action=="welcome_sms"){
	$sql="Select gibbonperson.gibbonpersonid,phone1 from gibbonperson,lakshyasmsgroup where `_id` in ($P_ids) AND gibbonperson.gibbonPersonID=lakshyasmsgroup.gibbonPersonID";
	$result=$connection2->prepare($sql);
	$result->execute();
	$pArr=$result->fetchAll();
	$dataContact=array();
	foreach($pArr as $p){
		$dataContact[$p['gibbonpersonid']]=$p['phone1'];
	}
	$contact_data="";
	$i=0;
	foreach($dataContact as $k=>$v){
		if($i++!=0)
			$contact_data.=",";
		//print $k;
		//print $v;
		$contact_data.="91".$v;
	}
	//print $contact_data;
	$smsUsername=getSettingByScope( $connection2, "Messenger", "smsUsername" ) ;
	$smsPassword=getSettingByScope( $connection2, "Messenger", "smsPassword" ) ;
	$smsURL=getSettingByScope( $connection2, "Messenger", "smsURL" ) ;   
	//$senderID="NewReg";
	//$sender = "TXTLCL";
	//$sender = "SXSRGJ";
	$sender = "CPSBAG";
    //$message_data="Dear Parents, ".$message_data;
	/*$message_data="CALCUTTA PUBLIC SCHOOL, ASWININGAR Welcomes you to become one of the precious members of our family through your Child. Thanks you.";  */
	$message_data="Congratulations and Welcome to Calcutta Public School Family, Baguiati.";
    $message_data="Dear Parents, ".$message_data;
    $post_data = "username=".$smsUsername."&hash=".$smsPassword."&message=".rawurlencode($message_data)."&sender=".$sender."&numbers=".$contact_data."&test=".$test;
        //$post_data = "username=".$smsUsername."&hash=".$smsPassword."&message=".rawurlencode($message_data)."&sender=".$sender."&numbers='9674926299'&test=".$test;
	$ch = curl_init();  
	curl_setopt($ch,CURLOPT_URL,  $smsURL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	$result = curl_exec($ch);
	curl_close($ch);
	$obj = json_decode($result,true);
	$t1=explode('=',$result);
       
	if($obj['status']=="success"){   
		//echo "Your message is sent with Log ID :".$t1[1];
	
	$timestamp=time();
	$senderID=$gibbonPersonID;
	$sql="INSERT INTO `lakshyasmslog`(`SMSLogID`,`status`, `subject`, `message`, `senderPersonID`, `sendingTime`) VALUES (NULL,'{$obj['batch_id']}','$subject_data','$message_data','$senderID','$timestamp')";
	$result=$connection2->prepare($sql);
	$result->execute();
	$id=$connection2->lastInsertId();
			
	$sql1="INSERT INTO `lakshyasmsrecipients`(`id`, `SMSLogID`, `personID`, `phone`, `status`) VALUES ";
	$j=0;
	foreach($dataContact as $k=>$v){
		if($j++!=0)
			$sql1.=" ,";
		$sql1.="(NULL,'$id','$k','$v','{$obj['batch_id']}')";
	}
	$result1=$connection2->prepare($sql1);
	$result1->execute();
		echo "Your message is sent ";
	
	$sql1="DELETE FROM lakshyasmsgroup WHERE _id IN ($P_ids)";
	$result1=$connection2->prepare($sql1);
	$result1->execute();
	}
}
else if($action=="payment_sms"){
	$sql="Select lakshyasmsgroup.`_id`,gibbonperson.gibbonPersonID,phone1,`payment_date`,`account_number`,`net_total_amount`,`payment_mode` from gibbonperson,lakshyasmsgroup,payment_master where `_id` in ($P_ids) AND gibbonperson.gibbonPersonID=lakshyasmsgroup.gibbonPersonID AND lakshyasmsgroup.ref_id=payment_master.payment_master_id";
	$result=$connection2->prepare($sql);
	$result->execute();
	$pArr=$result->fetchAll();
	$dataContact=array();
	//echo "<pre>";print_r($pArr);die;
	$smsUsername=getSettingByScope( $connection2, "Messenger", "smsUsername" ) ;
	$smsPassword=getSettingByScope( $connection2, "Messenger", "smsPassword" ) ;
	$smsURL=getSettingByScope( $connection2, "Messenger", "smsURL" ) ;
	$sent_id="";
	$successcount=0;
	$failcount=0;
	$reason="";
	try{
		foreach($pArr as $p){
			 
			$senderID="NewReg";
			//$senderID="CPSBAG";
			$message_data="Dear Parents, "."CALCUTTA PUBLIC SCHOOL, ASHWININAGAR has received fee of Rs. ".$p['net_total_amount']." on ".dateformat($p['payment_date'])." for account no. : ".substr($p['account_number'],-5);
			if($p['payment_mode']=='cheque'){
				$message_data.=" subject to realisation of cheque.";
			}
			$subject_data='Payment SMS';

			//$post_data="username=$smsUsername&password=$smsPassword&sender=$senderID&sendto=919831921264&message=". rawurlencode($message_data) ; 
				$test="0";
			//$sender = "TXTLCL";
			$sender = "CPSBAG";
			$data = "username=".$smsUsername."&hash=".$smsPassword."&message=".rawurlencode($message_data)."&sender=".$sender."&numbers=91".$p['phone1']."&test=".$test;
			$ch = curl_init($smsURL);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$result = curl_exec($ch); // This is the result from the API
			curl_close($ch);
			$obj = json_decode($result,true);
			// echo "<pre>";
			// print_r($obj); 
			// echo "</pre>";
			if($obj["status"]=="success"){
					$timestamp=time();
					$count=1;
					$sql="insert into `lakshyasmslog`(`smslogid`,`status`, `subject`, `message`, `senderpersonid`, `sendingtime`,`count`) values (null,'{$obj['status']}_{$obj['batch_id']}','$subject_data','$message_data','$gibbonpersonid','$timestamp','$count')";
					$result=$connection2->prepare($sql);
					$result->execute();
					$id=$connection2->lastinsertid();
					$sql1="insert into `lakshyasmsrecipients`(`id`, `smslogid`, `personid`, `phone`,`status`,`success`) values ";
					$j=0;
					$count=0;
					foreach($obj["messages"] as $o){
						$apiid=$o["id"];
					}
					//foreach($datacontact as $k=>$v){
						//$count++;
					$sql1.="(null,'$id','{$p['gibbonpersonid']}','{$p['phone1']}','{$apiid}','1')";
						
					//echo $count;
					$result1=$connection2->prepare($sql1);
					$result1->execute();
					//}
					$successcount++;
					if($sent_id==""){
						$sent_id="{$p["_id"]}";
					}
					else{
						 $sent_id.=",{$p["_id"]}";
					}
			}
			else{
				$failcount++;
			}
		}
	
		echo $successcount." messages sent succesfully, ".$failcount. " failed.";
	}
	catch(Exception $e) {
	  echo 'Message:  ' .$e->getMessage();
	}
	if($successcount>0){
	 	$sql1="DELETE FROM lakshyasmsgroup WHERE _id IN ($sent_id)";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
	}
	
}
else if($action=="username_sms"){
	
	$sql="SELECT `gibbonPersonID`,`username`,`phone1` FROM `gibbonperson` WHERE `gibbonPersonID` IN ($P_ids)";
	$result=$connection2->prepare($sql);
	$result->execute();
	$pArr=$result->fetchAll();
	$dataContact=array();
	///print_r($pArr);
	$smsUsername=getSettingByScope( $connection2, "Messenger", "smsUsername" ) ;
	$smsPassword=getSettingByScope( $connection2, "Messenger", "smsPassword" ) ;
	$smsURL=getSettingByScope( $connection2, "Messenger", "smsURL" ) ;
	$sent_id="";
	$successcount=0;
	$failcount=0;
	$reason="";
	foreach($pArr as $p){
		$message_data="Dear Parents,Please note that Calcutta Public School Fees may be paid ONLINE.Username:{$p['username']} Password:Cps@1234.For more details visit www.calcuttapublicschool.in";
		$subject_data='Username SMS';
		$post_data="username=$smsUsername&password=$smsPassword&sender=$senderID&sendto=91".$p['phone1']."&message=". rawurlencode($message_data) ; 
		//$post_data="username=$smsUsername&password=$smsPassword&sender=$senderID&sendto=919831921264&message=". rawurlencode($message_data) ; 
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,  $smsURL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		$result = curl_exec($ch);
		curl_close($ch);
		$t1=explode('=',$result);
		if(isset($t1[1]) && (int)$t1[1]>0){
		//echo "Your message is sent with Log ID :".$t1[1];
		$successcount++;
		$timestamp=time();
		$senderID=$gibbonPersonID;
		$sql="INSERT INTO `lakshyasmslog`(`SMSLogID`,`status`, `subject`, `message`, `senderPersonID`, `sendingTime`) VALUES (NULL,'{$t1[1]}','$subject_data','$message_data','$senderID','$timestamp')";
		$result=$connection2->prepare($sql);
		$result->execute();
		$id=$connection2->lastInsertId();
		if($sent_id==""){
		$sent_id=$p['_id'];
		}
		else{
			$sent_id.=",".$p['_id'];
		}
		$sql1="INSERT INTO `lakshyasmsrecipients`(`id`, `SMSLogID`, `personID`, `phone`) VALUES (NULL,'$id','{$p['gibbonPersonID']}','{$p['phone1']}')";
		$result1=$connection2->prepare($sql1);
		$result1->execute();
		}
		else{
			$failcount++;
			$reason.=$result."\n";
		}
		$test="0";
		$sender = "TXTLCL";
	$data = "username=".$smsUsername."&hash=".$smsPassword."&message=".rawurlencode($message_data)."&sender=".$sender."&numbers=91".$p['phone1']."&test=".$test;
	$ch = curl_init($smsURL);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch); // This is the result from the API
	curl_close($ch);
	$obj = json_decode($result,true);
	//echo "<pre>";
	//print_r($obj);
	//echo "</pre>";
	if($obj["status"]=="success"){
			$timestamp=time();
			$count=1;
			$sql="INSERT INTO `lakshyasmslog`(`SMSLogID`,`status`, `subject`, `message`, `senderPersonID`, `sendingTime`,`count`) VALUES (NULL,'{$obj['status']}_{$obj['batch_id']}','$subject_data','$message_data','$gibbonPersonID','$timestamp','$count')";
			$result=$connection2->prepare($sql);
			$result->execute();
			$id=$connection2->lastInsertId();
			
			$sql1="INSERT INTO `lakshyasmsrecipients`(`id`, `SMSLogID`, `personID`, `phone`,`status`,`success`) VALUES ";
			$j=0;
			$count=0;
			foreach($obj["messages"] as $o){
				$apiID=$o["id"];
			}
			//foreach($dataContact as $k=>$v){
				//$count++;
			$sql1.="(NULL,'$id','{$p['gibbonPersonID']}','{$p['phone1']}','{$apiID}','1')";
				
			//echo $count;
			$result1=$connection2->prepare($sql1);
			$result1->execute();
			//}
			$successcount++;
	}
	else{
		$failcount++;
	}
	}
	echo $successcount." messages sent succesfully, ".$failcount. " failed.";
	
}

else if($action=="discard_sms"){
	try{
		$sql="DELETE FROM lakshyasmsgroup where `_id` in ($P_ids)";
		$result=$connection2->prepare($sql);
		$result->execute();
		echo "Discarded successfully!";
	}
	catch(Exception $e){
		echo $e;
	}
	
}

function dateformat($date){
	$dArr=explode('-',$date);
	return $dArr[2]."/".$dArr[1]."/".$dArr[0];
}
?>