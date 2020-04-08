<?php
session_start();
session_unset();
session_destroy();
$loginUrl="./login.php";
header('Location: '.$loginUrl);
 ?>