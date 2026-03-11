<?php

/* 
 * Получение html-кода "фотографии" для вставки в страницу объекта.
 * Используется при посылке фотографии из объекта, чтобы не перегружать всю страницу.
 * 
 */
include_once('include/func.php');
include_once('include/func_bs.php');
include_once('include/head00.php');
if (isset($_REQUEST["message"])) {
  $_err_message = urldecode($_REQUEST["message"]);
}
else {
  $_err_message = "";
}
$ex = (int) $_REQUEST["ex"];  
if (!($ex)) {
  die;
}
$photoimg = getfotoex($ex,3,"",1);
$photocount = $photoimg["display"] + $photoimg["hidden"]; //countfotoexc($ex);
$town = $photoimg["town"];
$s_ex = $photoimg["exname"];

include_once 'include/getfotoex.php';
include_once 'include/head01.php';
echo $fotodisplay;
?>
</body>
</html>