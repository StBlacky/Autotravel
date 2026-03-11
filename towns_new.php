<?php
include_once('include/config.php');
include_once('include/func.php');
include_once('include/head00.php');
$bootstrap = 1;
$colorbox  = 1;
include('include/header_bs.php');
$a="";
$l=7;

if (preg_match("/^\/(a\d+)$/",$_pin,$exa)) {
 $l = $exa[1];
}
else {
 $l = $_REQUEST["l"];
 $l = mysafestring($l);
 if (!($l)) {
  $l = "";
 }
 $a = $_REQUEST["a"];
 $a = mysafestring($a);
 if (!($a)) {
  $a = "";
 }
}

include('include/towns_incl_new.php');
include('include/footer_bs.php');

?>
