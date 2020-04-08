<?php
$str=$_GET['data'];
$a=explode("_",$str);

$im = imagecreatefromjpeg("../../uploads/media/admit.jpg");
$text_color = imagecolorallocate($im, 0, 0, 0);
//Year1
imagestring($im, 40, 352, 158,  substr($a[0],2,2), $text_color);
//Year2
imagestring($im, 40, 400, 158,  substr($a[0],7,2), $text_color);
//Name
imagestring($im, 40, 75, 200, $a[1], $text_color);
//class
imagestring($im, 40, 68, 232,  $a[2], $text_color);
//Section
imagestring($im, 40, 182, 232,  substr($a[4],sizeof($a[4])-2,1), $text_color);
//Roll
imagestring($im, 40, 322, 232,  $a[3], $text_color);


// Set the content type header - in this case image/jpeg
header('Content-Type: image/jpeg');

// Output the image
imagejpeg($im);

// Free up memory
imagedestroy($im);
?>