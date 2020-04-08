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


if(isset($_POST['submit_otp'])){
    
       $sql ="SELECT * FROM login_otp WHERE valid='Active' AND otp_num=".$_POST['otp_number'] ; 
       
		$result=$connection2->prepare($sql);
		$result->execute($data);
		$data=$result->fetch(); 
  
        if($data==""){
           
			$URL.="?loginReturn=fail0b" ;
	        header("Location: {$URL}");
			 
        }else{
         
		    $URL="./index.php";
            header("Location: {$URL}");
        }
}

?>

<html>
<title></title>
<head></head>
<body>
    <form action="#" method="POST">
     Put Your OTP  Number  <input type='text' name='otp_number'>
        <input type='submit' name='submit_otp' value="Submit">
    </form>
</body>
</html> 