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
 
//echo "<pre>";print_r($_FILES["image"]);die;
if($_POST){
	$URL=$_SESSION[$guid]["absoluteURL"]."/index.php?q=/modules/Staff/staff_details_edit.php&gibbonStaffID=".$_REQUEST['gibbonStaffID'];
	//$url="http://13.233.101.108/ighs_lakshya_sr//index.php?q=/modules/Staff/staff_view.php";
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
			$attachment='';
			if($_FILES["image"]['error']==0){
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
			echo "<pre>kkkk=";print_r($attachment);die;
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
			//$fail=TRUE;
		}
	/*	$sql="UPDATE `gibbonperson` SET  
				preferredName='".$p_name."',gender='".$gender."',dob='".$dob."',email='".$email."',address1='".$address."',phone1='".$phone."',dateStart='".$dateStart."'";
	*/
	
	$sql="UPDATE `gibbonstaff` SET  
				preferredName='".$p_name."',gender='".$gender."',dob='".$dob."',email='".$email."',address1='".$address."',phone1='".$phone."',dateStart='".$dateStart."'";
			if($attachment!='')
				$sql.=",image_240='".$attachment."'";
				if($dateEnd!='') {
					$tDate = str_replace('/', '-', $dateEnd);
					$dateE=date("Y-m-d",strtotime($tDate));
					$sql.=",dateEnd='".$dateE."'";
				}
				$sql.=" WHERE `gibbonStaffID`=".$_REQUEST['gibbonStaffID'];
			$result=$connection2->prepare($sql);
			$result->execute();
		//echo $sql;
		
		if($pf_date!='') {
					$tDate = str_replace('/', '-', $pf_date);
					$pf_date=date("Y-m-d",strtotime($tDate));
					//$sql.=",dateEnd='".$dateE."'";
				}
		
	 	$sql="UPDATE `gibbonstaff` SET  
				type='".$type."',jobTitle='".$job_title."',bank_ac='".$bank_ac."',payment_mode='".$payment_mode."',qualifications='".$qualification."',
				pf_no='".$pf_no."',pf_date='".$pf_date."',uan_no='".$uan_no."',esi_no='".$esi_no."',priority='".$priority."',guardian='$guardian',relationship='$relationship',`reasonOfLeaving`='$reasonOL'    
				WHERE `gibbonStaffID`=".$_REQUEST['gibbonStaffID'];
		
			
			$result=$connection2->prepare($sql);
			$result->execute();
			
		/*	$sql1="UPDATE `staff_type` SET `staff_type`='".$_REQUEST['type']."' WHERE `gibbonStaffID`=".$_REQUEST['gibbonStaffID'];
			$result1=$connection2->prepare($sql1);
			$result1->execute();
			
			
			
			$sql2="UPDATE `staff_section` SET `section`='".$_REQUEST['staff_section']."' WHERE `gibbonStaffID`=".$_REQUEST['gibbonStaffID'];
			$result2=$connection2->prepare($sql2);
			$result2->execute();
			
		    $sql3="UPDATE `emp_type_table` SET `emp_type`='".$_REQUEST['emp_type']."' WHERE `gibbonStaffID`=".$_REQUEST['gibbonStaffID'];
			$result3=$connection2->prepare($sql3);
			$result3->execute();	
		*/	
			
			header("Location: http://ighs.in/ighs_lakshya_sr//index.php?q=/modules/Staff/staff_view.php");
			                  
			
	/*	if(!$fail)
		    return false;
		else
			$URL.='&status=imagesize';
		    header("Location: {$URL}");
		    
    */		    
}
?>
