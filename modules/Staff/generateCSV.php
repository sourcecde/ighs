<?php
if($_POST){
extract($_POST);
$data=json_decode($dataCSV,true);
$header=array('Member ID','Member Name','EPF Wages','EPS Wages','EPF Contribution (EE Share) due	','EPF Contribution (EE Share) being remitted','EPS Contribution due','EPS Contribution being remitted','Diff EPF and EPS Contribution (ER Share) due','Diff EPF and EPS Contribution (ER Share) being remitted','NCP Days','Refund of Advances','Arrear EPF Wages','Arrear EPF EE Share','Arrear EPF ER Share','Arrear EPS Share','Father\'s/Husband\'s Name','Relationship with the Member','Date of Birth','Gender','Date of Joining EPF','Date of Joining EPS','Date of Exit from EPF','Date of Exit from EPS','Reason for leaving');

if (is_dir('../../ECR')==FALSE) {
	mkdir('../../ECR', 0777, TRUE) ;					
}
$filename="../../ECR/ECR_".date("d-m-Y").".csv";
$myfile = fopen($filename, "w") or die("Unable to open file!");
fputcsv($myfile, $header);
foreach ($data as $fields) {
    fputcsv($myfile, $fields);
}
fclose($myfile); 
$file=$filename;
if (file_exists($file)) {
	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename="'.basename($file).'"');
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	header('Content-Length: ' . filesize($file));
	readfile($file);
	exit;
} 
}
 ?>