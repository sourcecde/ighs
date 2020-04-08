<?php
if (substr($_SERVER['HTTP_HOST'], 0, 4) == 'www.'){
	$pageURL = "https://";
	$pageURL .= substr($_SERVER['SERVER_NAME'], 4).$_SERVER["REQUEST_URI"];
	header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $pageURL);
    exit();
}
?>