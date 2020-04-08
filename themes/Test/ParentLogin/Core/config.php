<?php
/*
//Database connection information (AN)
$databaseServer="localhost"; 
$databaseUsername="hir_green_an"; 
$databasePassword="P@ssword!23"; 
$databaseName="hir_lakshya_green_an";
//Database connection information (JM)
$databaseServer="localhost"; 
$databaseUsername="hir_green_jm"; 
$databasePassword="P@ssword!23"; 
$databaseName="hir_lakshya_green_jm";
*/
//Test Db

$databaseServer="localhost"; 
$databaseUsername="hir_green_an"; 
$databasePassword="P@ssword!23"; 
$databaseName="hir_lakshya_payment_test";


//Local Db
/*
$databaseServer="localhost" ;
$databaseUsername="root" ;
$databasePassword="" ;
$databaseName="hir_lakshya_green_an" ;
*/
date_default_timezone_set("Asia/Kolkata");
$Payment = array("MerchantId"=>"165851", "AccessCode"=>"AVQL01FB51AS24LQSA", "WorkingKey"=>"632C2A6726A25E5C56D2E02AFFD893C7", "PaymentUrl"=>"https://test.ccavenue.com/transaction/transaction.do?command=initiateTransaction");
$ReturnURL="https://calcuttapublicschool.in/lakshya/lakshya_green_an/Test/ParentLogin/Core/PaymentResponse.php";
$PaymentGatewayDomain='test.ccavenue.com';
$PaymentBankId=4;
$ImageURL="../../";
$LastSchoolYear=23;
?>