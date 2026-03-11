<?php
// показ фото по городу
include_once('include/func.php');
include_once('include/func_bs.php');
include_once('include/head00.php');

// теперь всё как обычно

$town = 0;
$ex = 0;

if (preg_match("/^\/t\/(\d+)$/",$_pin,$exa)) {
 $town = $exa[1];
}
if (preg_match("/^\/e\/(\d+)$/",$_pin,$exa)) {
 $ex = $exa[1];
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf8">
<meta http-equiv="Content-language" content="ru">
</head>
<body>
<?php

if ($town) {
  echo getfototown($town,10000);
}  
if ($ex) {
  echo getfotoex($ex, 10000, "", -1);
}  

?>
</body>

