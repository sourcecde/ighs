<?php


@session_start() ;
include "../../config.php" ;
try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}

if($_POST){
    
	$URL=$_SESSION[$guid]["absoluteURL"]."/index.php?q=/modules/Staff/staff_info.php";
	$fail=FALSE;	
			$p_name=$_REQUEST['p_name'];
				$tDob = str_replace('/', '-', $_REQUEST['dob']);
			$dob=date("Y-m-d",strtotime($tDob));
			$gender=$_REQUEST['gender'];	
			$address=$_REQUEST['address'];	
			$email=$_REQUEST['email'];	
			$phone=$_REQUEST['phone'];	
				$tDOS=str_replace('/', '-',$_REQUEST['dateStart']);
			$dateStart=date("Y-m-d",strtotime($tDOS));
			$dateEnd=$_REQUEST['dateEnd'];
			$type=$_REQUEST['type'];
			$job_title=$_REQUEST['job_title'];
			$qualification=$_REQUEST['qualification'];
			$priority=$_REQUEST['priority'];
			$pf_no=$_REQUEST['pf_no'];
			$pf_date=$_REQUEST['pf_date'];
			$uan_no=$_REQUEST['uan_no'];
			$esi_no=$_REQUEST['esi_no'];
			$bank_ac=$_REQUEST['bank_ac'];
			$payment_mode=$_REQUEST['payment_mode'];
			$guardian=$_REQUEST['guardian'];
			$relationship=$_REQUEST['relationship'];
			$reasonOL=$_REQUEST['reasonOL'];
			$contract_start_date=$_REQUEST['con_date'];
			//echo "<pre>";print_r($contract_start_date);die;
			$contract_year=$_REQUEST['c_year'];
			$contract_leave_day="";
			if($contract_start_date!="" || $contract_year!=""){
			    $splite_value=explode("/",$contract_start_date);
			    $year=$splite_value[2];
			    $contract_leave_year=$year+$contract_year;
			    $contract_leave_day=$splite_value[0]."/".$splite_value[1]."/".$contract_leave_year;
			}
			$attachment='';
			
			/*if($_FILES["image"]['error']==0){
			$size1=getimagesize($_FILES["image"]["tmp_name"]);
				$attachment="uploads/" . date("Y") . "/" . date("m") . "/" . $_REQUEST['gibbonStaffID'] . "_240_" .$_FILES["image"]["name"] ;
			//echo "<pre>kkkk=";print_r($attachment);die;
			if (is_dir("../../uploads/" . date("Y") . "/" . date("m"))==FALSE) {
				mkdir("../../uploads/" . date("Y") . "/" . date("m"), 0777, TRUE) ;					
			}
			if(file_exists("../../".$attachment))
				unlink("../../".$attachment);
			move_uploaded_file($_FILES["image"]["tmp_name"],"../../".$attachment);
			
			
		/*	if($size1[0]==240 && $size1[1]==320){
			$attachment="uploads/" . date("Y") . "/" . date("m") . "/" . $_REQUEST['gibbonPersonID'] . "_240" . strrchr($_FILES["image"]["name"], ".") ;
			if (is_dir("../../uploads/" . date("Y") . "/" . date("m"))==FALSE) {
				mkdir("../../uploads/" . date("Y") . "/" . date("m"), 0777, TRUE) ;					
			}
			if(file_exists("../../".$attachment))
				unlink("../../".$attachment);
			move_uploaded_file($_FILES["image"]["tmp_name"],"../../".$attachment); 
			}
			else{
				$fail=TRUE;
			}
		*/	
			
		//} 
		
		if($pf_date!='') {
					$tDate = str_replace('/', '-', $pf_date);
					$pf_date=date("Y-m-d",strtotime($tDate));
					//$sql.=",dateEnd='".$dateE."'";
				}
				
		if($contract_start_date!='') {
					$tDate = str_replace('/', '-', $contract_start_date);
					$contract_start_date=date("Y-m-d",strtotime($tDate));
					//$sql.=",dateEnd='".$dateE."'";
				}
		if($contract_leave_day!='') {
					$tDate1 = str_replace('/', '-', $contract_leave_day);
					$contract_leave_day=date("Y-m-d",strtotime($tDate1));
					//$sql.=",dateEnd='".$dateE."'";
				}		
		
		
		
		
		if($p_name!=""){
		    
		    
		  /*  $sql1="INSERT INTO `staff_type`(`gibbonStaffID`, `staff_type`) VALUES ([value-2],'".$_REQUEST['type']."');
			$result1=$connection2->prepare($sql1);
			$result1->execute();
			
			
			
			$sql2="UPDATE `staff_section` SET `section`='".$_REQUEST['staff_section']."' WHERE `gibbonStaffID`=".$_REQUEST['gibbonStaffID'];
			$result2=$connection2->prepare($sql2);
			$result2->execute();
			
		    $sql3="UPDATE `emp_type_table` SET `emp_type`='".$_REQUEST['emp_type']."' WHERE `gibbonStaffID`=".$_REQUEST['gibbonStaffID'];
			$result3=$connection2->prepare($sql3);
			$result3->execute();
		    
		    */
		    
		    
		    
		    
		
    	$sql = "INSERT INTO gibbonstaff (preferredName, gender, dob,email,address1,phone1,dateStart,dateEnd,type,jobTitle,bank_ac,payment_mode,qualifications,pf_no,uan_no,esi_no,priority,guardian,relationship,reasonOfLeaving,pf_date,contact_start_date,contact_year,contact_renual_date) 
    	VALUES ('$p_name','$gender','$dob','$email','$address','$phone','$dateStart','$dateEnd','$type','$job_title','$bank_ac','$payment_mode','$qualification','$pf_no','$uan_no','$esi_no','$priority','$guardian','$relationship','$reasonOL','$pf_date','$contract_start_date','$contract_year','$contract_leave_day')";
    	$result = $connection2->query($sql);
    	$insertid=$connection2->lastInsertId();
    	
    /*	$sql1="INSERT INTO `staff_type`(`gibbonStaffID`, `staff_type`) VALUES ('".$insertid."','".$_REQUEST['type']."')";
		$result1=$connection2->query($sql1);
		
		$sql1="INSERT INTO `staff_section`(`gibbonStaffID`, `section`) VALUES ('".$insertid."','".$_REQUEST['staff_section']."')";
		$result1=$connection2->query($sql1);
		
		$sql1="INSERT INTO `emp_type_table`(`gibbonStaffID`, `emp_type`) VALUES ('".$insertid."','".$_REQUEST['emp_type']."')";
		$result1=$connection2->query($sql1);
	*/	
		
		
		
		if($_FILES["image"]['error']==0){
			$size1=getimagesize($_FILES["image"]["tmp_name"]);
				$attachment="uploads/" . date("Y") . "/" . date("m") . "/" . $insertid . "_240_" .$_FILES["image"]["name"] ;
			//echo "<pre>kkkk=";print_r($attachment);die;
			if (is_dir("../../uploads/" . date("Y") . "/" . date("m"))==FALSE) {
				mkdir("../../uploads/" . date("Y") . "/" . date("m"), 0777, TRUE) ;					
			}
			if(file_exists("../../".$attachment))
				unlink("../../".$attachment);
			move_uploaded_file($_FILES["image"]["tmp_name"],"../../".$attachment);
			
		} 


			header("Location: https://calcuttapublicschool.in/lakshya/lakshya_green_an//index.php?q=/modules/Staff/staff_view.php");
			
    	
	/*	if($result>0)
			$URL.="&status=success";	
		else
			$URL.='&status=Error';
	*/
	//	header("Location: {$URL}");
		
		}else{
		        $URL.='&status=Please_fill_up_the_form';
		    	header("Location: {$URL}");
		}
	
}
?>