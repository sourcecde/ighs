<?php
function CreateFeeName($type,$borderclassid)
{
	$borarderclass=array(1=>'01',2=>'01',3=>'02',4=>'02',5=>'03',6=>'03',7=>'04',8=>'04',9=>'05',10=>'05',11=>'06',12=>'06',13=>'07',14=>'07',15=>'08',16=>'08',17=>'09',18=>'09',19=>'10',20=>'10',21=>'11',22=>'11',23=>'12',24=>'12');
	$str='';
	switch ($type) {
		case 1:
		$str='TF-';
		break;
		
		case 2:
		$str='SF-';
		break;
		
		case 3:
		$str='AF-';
		break;
		
		case 4:
		$str='LF-';
		break;
		
		case 5:
		$str='CF-';
		break;
	}
	$border='';
	
	if($borderclassid%2==0)
	{
		$border='NB';
		
	}
	else 
	{
		$border='B';
	}
	$class=$borarderclass[$borderclassid];
	$str.=$border."-".$class;
	return $str;
}
?>