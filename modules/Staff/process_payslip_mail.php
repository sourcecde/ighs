<?php
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

	if($_REQUEST['action']=='mail')	{
		$id=$_REQUEST['id'];
		$month_f=$_REQUEST['month'];
		$year_f=$_REQUEST['year'];

		$sql="SELECT * FROM `gibbonschoolyear` ORDER BY `gibbonSchoolYearID` ";
		$result2=$connection2->prepare($sql);
		$result2->execute();
		$year=$result2->fetchAll();
			$month_ar=array(3,2,1,12,11,10,9,8,7,6,5,4);
			$month_name=array('January','February','March','April','May','June','July','August','September','October','November','December');

		try{

		$sql="SELECT *
FROM lakshyasalarypayment,lakshyasalarymaster,lakshyasalaryrule,gibbonstaff
				where lakshyasalarymaster.master_id=lakshyasalarypayment.master_id
                and lakshyasalarymaster.rule_id=lakshyasalaryrule.rule_id
                and lakshyasalarymaster.staff_id=gibbonstaff.gibbonStaffID
                and lakshyasalarymaster.month=$month_f
                and lakshyasalarymaster.year_id=$year_f
                and gibbonstaff.gibbonStaffID=$id";
		$result=$connection2->prepare($sql);
		$result->execute();
		$staff_payslip_details=$result->fetchAll();
		$payslip = array();

		}
		catch(PDOException $e){
			echo $e;
		}
	}
	print_r($staff_payslip_details);
	foreach ($staff_payslip_details as $staffPayslip) {
			$payslip[$staffPayslip['gibbonStaffID']]['name'] =  $staffPayslip['preferredName'];
			$payslip[$staffPayslip['gibbonStaffID']]['email'] =  $staffPayslip['email'];
			$payslip[$staffPayslip['gibbonStaffID']][$staffPayslip['caption']] =  $staffPayslip['paid_amount'];
		}
		//print_r($payslip);


		$sql6="SELECT * FROM `lakshyasalarymaster` WHERE `rule_id` IN (97,96)";
		$sql6.=" AND month=".$month_f;
		$sql6.=" AND year_id=".$year_f;
		$result6=$connection2->prepare($sql6);
		$result6->execute();
		$structure=$result6->fetchAll();
		$pf_arr = array();
		foreach($structure as $s){
			$pf_arr[$s['rule_id']]=$s['amount'];
		}
		//print_r($pf_arr);

		foreach($payslip as $generate_payslip){

				$total_salary = ($generate_payslip['PAYBAND'] + $generate_payslip['CONSAL']+$generate_payslip['GRPAY']+$generate_payslip['DA']+$generate_payslip['HRA']+$generate_payslip['MED']+$generate_payslip['SPLPAY']);

				if($total_salary <= 21000 && $generate_payslip['PF GROS']>0)
                    
					{ $esi = ceil(($total_salary * $pf_arr['96'])/100); }
				else{ $esi = 0;}

				$pf = round(($generate_payslip['PF GROS']*$pf_arr['97'])/100);

				$total_deduction = ($pf+$generate_payslip['P TAX']+$generate_payslip['I TAX']+$generate_payslip['ADVANCE']+$esi);
				$total_in_hand = ($total_salary - $total_deduction);
foreach ($year as $y) {
	//print_r($y);
}
// 				$to = $generate_payslip['email'];
// 				$subject = "Payslip Details";
// 				$msg = "<html><body>
// 				<table width='100%' cellpadding='2' cellspacing='0' border='0'>
// 				  <tr>
// 					<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:20px; color:#000000;'>Indra Gopal High School</td>
// 				  </tr>
// 				  <tr>
// 					<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000;'>Jheel Bagan, P.O. Ghuni, Hatiara, Kolkata - 700 157</td>
// 				  </tr>

//                   <tr>
// 		             <td align='center' colspan=15 style='font-family:Arial, Helvetica, sans-serif; font-size:16px; color:#000000;'> Salary For  the Month: ".$month_name[($month_f-1)]." of Year: ".($y['name']-1)."</td>
// 	              </tr>

// 	           <tr>
//          		<td align='center' colspan=15 style='font-family:Arial, Helvetica, sans-serif; font-size:16px; color:#000000;'> Name:".$generate_payslip['name']."</td>
// 	           </tr>	
			
// 				  <tr>
// 				  </table>
// 				  <table width='100%' cellpadding='5px' style='border: 1px solid black; '>
// 				  	<tr style='border: 1px solid black; '>
// 				  		<th>Particulars</th>
// 				  		<th>Amount</th>
// 				  	</tr>
// 				  	<tr style='border: 1px solid black; '>
// 				  		<th>Salary Structure (A) [Earnings]</th>
// 				  		<th></th>
// 				  	</tr>
// 				  	<tr style='border: 1px solid black; '>
// 				  		<td style='border: 1px solid black; '>PAYBAND</td>
// 				  		<td style='border: 1px solid black; text-align: right;'>".$generate_payslip['PAYBAND']."</td>
// 				  	</tr>
// 				  	<tr style='border: 1px solid black; '>
// 				  		<td style='border: 1px solid black; '>CONSAL</td>
// 				  		<td style='border: 1px solid black; text-align: right;'>".$generate_payslip['CONSAL']."</td>
// 				  	</tr>
// 				  	<tr style='border: 1px solid black; '>
// 				  		<td style='border: 1px solid black; '>GRPAY</td>
// 				  		<td style='border: 1px solid black; text-align: right;'>".$generate_payslip['GRPAY']."</td>
// 				  	</tr>
// 				  	<tr style='border: 1px solid black; '>
// 				  		<td style='border: 1px solid black; '>DA</td>
// 				  		<td style='border: 1px solid black; text-align: right;'>".$generate_payslip['DA']."</td>
// 				  	</tr>
// 				  	<tr style='border: 1px solid black; '>
// 				  		<td style='border: 1px solid black; '>HRA</td>
// 				  		<td style='border: 1px solid black; text-align: right;'>".$generate_payslip['HRA']."</td>
// 				  	</tr>
// 				  	<tr style='border: 1px solid black; '>
// 				  		<td style='border: 1px solid black; '>MEDICAL</td>
// 				  		<td style='border: 1px solid black; text-align: right;'>".$generate_payslip['MED']."</td>
// 				  	</tr>
// 				  	<tr style='border: 1px solid black; '>
// 				  		<td style='border: 1px solid black; '>SPECIAL PAY</td>
// 				  		<td style='border: 1px solid black; text-align: right;'>".$generate_payslip['SPLPAY']."</td>
// 				  	</tr>
// 				  	<tr style='border: 1px solid black; '>
// 				  		<td style='border: 1px solid black; '>Total</td>
// 				  		<td style='border: 1px solid black; text-align: right;'>".$total_salary."</td>
// 				  	</tr>
// 				  </table>
// 				  <table width='100%' cellpadding='5px' style='border: 1px solid black; '>
// 				  	<tr style='border: 1px solid black; '>
// 				  		<th>Salary Structure (B) [Deduction]</th>
// 				  		<th></th>
// 				  	</tr>

				  	
// 				  	<tr style='border: 1px solid black; '>
// 				  		<td style='border: 1px solid black; '>P TAX</td>
// 				  		<td style='border: 1px solid black; text-align: right;'>".$generate_payslip['P TAX']."</td>
// 				  	</tr>
// 				  	<tr style='border: 1px solid black; '>
// 				  		<td style='border: 1px solid black; '>I TAX</td>
// 				  		<td style='border: 1px solid black; text-align: right;'>".$generate_payslip['I TAX']."</td>
// 				  	</tr>
// 				  	<tr style='border: 1px solid black; '>
// 				  		<td style='border: 1px solid black; '>ESI</td>
// 				  		<td style='border: 1px solid black; text-align: right;'>".$esi."</td>
// 				  	</tr>
// 				  	<tr style='border: 1px solid black; '>
// 				  		<td style='border: 1px solid black; '>PF</td>
// 				  		<td style='border: 1px solid black; text-align: right; '>".$pf."</td>
// 				  	</tr>
// 				  	<tr style='border: 1px solid black; '>
// 				  		<td style='border: 1px solid black; '>ADVANCE</td>
// 				  		<td style='border: 1px solid black; text-align: right;'>".$generate_payslip['ADVANCE']."</td>
// 				  	</tr>
// 				  	<tr style='border: 1px solid black; '>
// 				  		<td style='border: 1px solid black; '>Total</td>
// 				  		<td style='border: 1px solid black; text-align: right;'>".$total_deduction."</td>
// 				  	</tr>
// 				  	<tr style='border: 1px solid black; '>
// 				  		<td style='border: 1px solid black; '>Total Salary (A - B)</td>
// 				  		<td style='border: 1px solid black; text-align: right;'>".$total_in_hand."</td>
// 				  	</tr>
// 				  </table>
				  
// 				  </table>
// 				  <table width='100%'>
// 				  <tr>
// 				  		<td></td>
// 				  	    <td><img src='http://ighs.in/ighs_lakshya_sr//themes/Default/img/ki85Ed5dT.jpg' alt='Signature' width='150' height='75' align='right'></td>	
// 				  	</tr>
// 				  	<tr>
// 				  		<td><b>Date:".date('d/m/Y')."</b> </td>
// 				  		<td style='text-align:right ;font-weight: bold;'>Signature<br>(Manager, Accounts)</td>
// 				  	</tr>
// 				  	</table>
// 				  	</body></html>";

// // Always set content-type when sending HTML email
// $headers = "MIME-Version: 1.0" . "\r\n";
// $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// // More headers
// $headers .= 'From: <dbrj.prof@gmail.com>' . "\r\n";

//mail($to,$subject,$msg,$headers);

require 'PHPMailerAutoload.php';
			require 'credential.php';

			$mail = new PHPMailer;

			// $mail->SMTPDebug = 4;                               // Enable verbose debug output

			$mail->isSMTP();                                      // Set mailer to use SMTP
			$mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
			$mail->SMTPAuth = true;                               // Enable SMTP authentication
			$mail->Username = EMAIL;                 // SMTP username
			$mail->Password = PASS;                           // SMTP password
			$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
			$mail->Port = 587;                                    // TCP port to connect to

			$mail->Mailer = 'smtp';

			$mail->setFrom(EMAIL, 'IGHS');
			$mail->addAddress($generate_payslip['email']);     // Add a recipient

			$mail->addReplyTo(EMAIL);
			// print_r($_FILES['file']); exit;
			for ($i=0; $i < count($_FILES['file']['tmp_name']) ; $i++) { 
				$mail->addAttachment($_FILES['file']['tmp_name'][$i], $_FILES['file']['name'][$i]);    // Optional name
			}
			$mail->isHTML(true);                                  // Set email format to HTML

			$mail->Subject = "Payslip for the month of ".$month_name[($month_f-1)]."";
			$mail->Body    = "<html><body>
				<table width='100%' cellpadding='2' cellspacing='0' border='0'>
				  <tr>
					<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:20px; color:#000000;'>Indra Gopal High School</td>
				  </tr>
				  <tr>
					<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000;'>Jheel Bagan, P.O. Ghuni, Hatiara, Kolkata - 700 157</td>
				  </tr>

                  <tr>
		             <td align='center' colspan=15 style='font-family:Arial, Helvetica, sans-serif; font-size:16px; color:#000000;'> Salary For  the Month: ".$month_name[($month_f-1)]." of Year: ".($y['name']-1)."</td>
	              </tr>

	           <tr>
         		<td align='center' colspan=15 style='font-family:Arial, Helvetica, sans-serif; font-size:16px; color:#000000;'> Name:".$generate_payslip['name']."</td>
	           </tr>	
			
				  <tr>
				  </table>
				  <table width='100%' cellpadding='5px' style='border: 1px solid black; '>
				  	<tr style='border: 1px solid black; '>
				  		<th>Particulars</th>
				  		<th>Amount</th>
				  	</tr>
				  	<tr style='border: 1px solid black; '>
				  		<th>Salary Structure (A) [Earnings]</th>
				  		<th></th>
				  	</tr>
				  	<tr style='border: 1px solid black; '>
				  		<td style='border: 1px solid black; '>PAYBAND</td>
				  		<td style='border: 1px solid black; text-align: right;'>".$generate_payslip['PAYBAND']."</td>
				  	</tr>
				  	<tr style='border: 1px solid black; '>
				  		<td style='border: 1px solid black; '>CONSAL</td>
				  		<td style='border: 1px solid black; text-align: right;'>".$generate_payslip['CONSAL']."</td>
				  	</tr>
				  	<tr style='border: 1px solid black; '>
				  		<td style='border: 1px solid black; '>GRPAY</td>
				  		<td style='border: 1px solid black; text-align: right;'>".$generate_payslip['GRPAY']."</td>
				  	</tr>
				  	<tr style='border: 1px solid black; '>
				  		<td style='border: 1px solid black; '>DA</td>
				  		<td style='border: 1px solid black; text-align: right;'>".$generate_payslip['DA']."</td>
				  	</tr>
				  	<tr style='border: 1px solid black; '>
				  		<td style='border: 1px solid black; '>HRA</td>
				  		<td style='border: 1px solid black; text-align: right;'>".$generate_payslip['HRA']."</td>
				  	</tr>
				  	<tr style='border: 1px solid black; '>
				  		<td style='border: 1px solid black; '>MEDICAL</td>
				  		<td style='border: 1px solid black; text-align: right;'>".$generate_payslip['MED']."</td>
				  	</tr>
				  	<tr style='border: 1px solid black; '>
				  		<td style='border: 1px solid black; '>SPECIAL PAY</td>
				  		<td style='border: 1px solid black; text-align: right;'>".$generate_payslip['SPLPAY']."</td>
				  	</tr>
				  	<tr style='border: 1px solid black; '>
				  		<td style='border: 1px solid black; '>Total</td>
				  		<td style='border: 1px solid black; text-align: right;'>".$total_salary."</td>
				  	</tr>
				  </table>
				  <table width='100%' cellpadding='5px' style='border: 1px solid black; '>
				  	<tr style='border: 1px solid black; '>
				  		<th>Salary Structure (B) [Deduction]</th>
				  		<th></th>
				  	</tr>

				  	
				  	<tr style='border: 1px solid black; '>
				  		<td style='border: 1px solid black; '>P TAX</td>
				  		<td style='border: 1px solid black; text-align: right;'>".$generate_payslip['P TAX']."</td>
				  	</tr>
				  	<tr style='border: 1px solid black; '>
				  		<td style='border: 1px solid black; '>I TAX</td>
				  		<td style='border: 1px solid black; text-align: right;'>".$generate_payslip['I TAX']."</td>
				  	</tr>
				  	<tr style='border: 1px solid black; '>
				  		<td style='border: 1px solid black; '>ESI</td>
				  		<td style='border: 1px solid black; text-align: right;'>".$esi."</td>
				  	</tr>
				  	<tr style='border: 1px solid black; '>
				  		<td style='border: 1px solid black; '>PF</td>
				  		<td style='border: 1px solid black; text-align: right; '>".$pf."</td>
				  	</tr>
				  	<tr style='border: 1px solid black; '>
				  		<td style='border: 1px solid black; '>ADVANCE</td>
				  		<td style='border: 1px solid black; text-align: right;'>".$generate_payslip['ADVANCE']."</td>
				  	</tr>
				  	<tr style='border: 1px solid black; '>
				  		<td style='border: 1px solid black; '>Total</td>
				  		<td style='border: 1px solid black; text-align: right;'>".$total_deduction."</td>
				  	</tr>
				  	<tr style='border: 1px solid black; '>
				  		<td style='border: 1px solid black; '>Total Salary (A - B)</td>
				  		<td style='border: 1px solid black; text-align: right;'>".$total_in_hand."</td>
				  	</tr>
				  </table>
				  
				  </table>
				  <table width='100%'>
				  <tr>
				  		<td></td>
				  	    <td><img src='http://ighs.in/ighs_lakshya_sr//themes/Default/img/ki85Ed5dT.jpg' alt='Signature' width='150' height='75' align='right'></td>	
				  	</tr>
				  	<tr>
				  		<td><b>Date:".date('d/m/Y')."</b> </td>
				  		<td style='text-align:right ;font-weight: bold;'>Signature<br>(Manager, Accounts)</td>
				  	</tr>
				  	</table>
				  	</body></html>";
			//$mail->AltBody = "Hiiiiii";

			if(!$mail->send()) {
			    echo 'Message could not be sent.';
			    echo 'Mailer Error: ' . $mail->ErrorInfo;
			} else {
			    echo 'Message has been sent';
			}
	}
	
}
?>