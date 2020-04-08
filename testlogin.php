<?php

include "functions.php" ;
include "config.php" ;

@session_start() ;

//New PDO DB connection
try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); 
}
catch(PDOException $e) {
  echo $e->getMessage();
}

if(isset($_POST['login'])){
                
             $sql="Select value from gibbonsetting where gibbonSystemSettingsID='00149'" ;
		     $result=$connection2->prepare($sql);
		     $result->execute();
             $otp_value=$result->fetch();
             //echo "<pre>";print_r($otp_value);die; 
             
             if(isset($otp_value['value']) && $otp_value['value']=='Y'){ 
            //$number = '8768828947';
            $number = '9674926299';
            $sender = urlencode('TXTLCL');
            $OTP=rand(1, 1000000); 
            $message = "Use ".$OTP." as OTP to login"; 
            $apiKey="kWcHb0UKv+g-Imp0vyI6sItib4MQksmMQ3c0PRv3cY";
            // Prepare data for POST request
            $data = array('apikey' => $apiKey, 'numbers' => $number, "sender" => $sender, "message" => $message);
         
              //Send the POST request with cURL
            $ch = curl_init('https://api.textlocal.in/send/');
            curl_setopt($ch, CURLOPT_POST, true); 
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);
         
		
		     //$userid=$data['gibbonPersonID'];
		     $userid=00001;
		     /*inactive users befor otp*/
		     $sql="UPDATE `login_otp` SET valid='Inacive' where user_id=".$userid; 
		     $result=$connection2->prepare($sql);
		     $result->execute($data);
		     $sql="INSERT INTO `login_otp`(`otp_num`,`user_id`) VALUES ('$OTP','$userid')" ;
		     $result=$connection2->prepare($sql);
		     $result->execute($data);
		     
		     
		     $URL="./otp_page.php" ;
			 header("Location: {$URL}");
    }else{
        echo "opt not sent because otp setting value  is N, want to sent otp go to seeting table and do Y";
    }
    
}


?>





<html>
<title></title>
<head></head>
<body>
    <form action="#" method="POST">
        <input type='submit' name='login' value="login">
    </form>
</body>
</html> 