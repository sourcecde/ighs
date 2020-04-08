<?php
if($_POST){
extract($_POST);
$data=json_decode($dataECR,true);
$message="";
foreach($data as $d){
	$i=0;
	foreach($d as $v){
		if($i++!=0)
			$message.="#~#";
		$message.=$v;
	}
	$message.="\r\n" ;
}
if (is_dir('../../ECR')==FALSE) {
	mkdir('../../ECR', 0777, TRUE) ;					
}
$filename="../../ECR/ECR_".date("d-m-Y").".txt";
$myfile = fopen($filename, "w") or die("Unable to open file!");
fwrite($myfile, $message);
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