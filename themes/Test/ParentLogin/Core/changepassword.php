<?php 
session_start();
if (file_exists("./dbCon.php")) {
	include "./dbCon.php" ;
}
if(isset($_SESSION['user'])){
	if($_POST){
		$error="";
		extract($_POST);
		$gibbonPersonID=$_SESSION['user']['gibbonPersonID'];
		$passwordStrongSalt=$_SESSION['user']['passwordStrongSalt'];
		$passwordStrong=hash("sha256", $passwordStrongSalt.$current_password);
		try{
		$sql="SELECT * FROM `gibbonperson` WHERE `gibbonPersonID`=$gibbonPersonID AND `passwordStrong`='$passwordStrong'";
		$result=$connection1->prepare($sql);
		$result->execute();
		}
		catch(PDOException $e) {
		  //echo $e->getMessage();
		}
		if($result->rowCount()>0){
			if($new_password==$confirm_password){
				if(PasswordPolicyChecker($new_password)){
					try{
						$newSalt=getSalt();
						$newPasswordStrong=hash("sha256", $newSalt.$new_password);
						$sql="UPDATE gibbonperson SET passwordStrong='$newPasswordStrong', passwordStrongSalt='$newSalt' WHERE `gibbonPersonID`=$gibbonPersonID";
						$result=$connection1->prepare($sql);
						$result->execute();
						$_SESSION['user']['passwordStrongSalt']=$newSalt;
					}
					catch(PDOException $e) {
					  //echo $e->getMessage();
					}
					$error=0;
				}
				else{
					//Password policy doesn't meet.
					$error=3;
				}
			}
			else{
				//New password and confirm password doesn't match.
				$error=2;
			}
		}
		else{
			//Wrong current password.
			$error=1;
		}
		echo $error;
		$changePasswordUrl="../changepassword.php";
		header('Location: '.$changePasswordUrl.'?error='.$error);
	}
}
function PasswordPolicyChecker($passwordNew){
	return preg_match('/[a-z]/',$passwordNew) && preg_match('/[A-Z]/',$passwordNew) && preg_match('/[0-9]/',$passwordNew) && strlen($passwordNew)>=8;
}
function getSalt() {
  $c=explode(" ", ". / a A b B c C d D e E f F g G h H i I j J k K l L m M n N o O p P q Q r R s S t T u U v V w W x X y Y z Z 0 1 2 3 4 5 6 7 8 9");
  $ks=array_rand($c, 22);
  $s="";
  foreach($ks as $k) { $s .=$c[$k]; }
  return $s;
}
?>