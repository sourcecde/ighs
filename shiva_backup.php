<?php
include 'config.php';


$tableName  = 'fee_payable';
$backupFile = 'fee_payable.sql';
$query      = "SELECT * INTO OUTFILE '$backupFile' FROM $tableName";
$result = mysql_query($query);


?> 