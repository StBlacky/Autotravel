<?php
include_once('include/config.php');
include_once('include/func.php');
include_once('include/func_bs.php');
include_once('include/head00.php');
$colorbox = 0;
$masonry = 0;
$_display_tables = 0; // но может быть переопределена в настройках
if ($_text_mode) {
  $ug_gallery = 1;
}
else {
  $ug_gallery = 1;
}
$bootstrap = 1;

$town_url = '';
if (preg_match("/^\/(.+)\.html$/",$_pin,$exa)) {
 //if (preg_match("/^\/(.+)$/",$_pin,$exa)) {
 $town_url = $exa[1];
}
else {
  if (preg_match("/^\/([^.]+)$/",$_pin,$exa)) {
    $town_url = $exa[1];
  }   
}
if (!($town_url)) {
  include('404.php');
  exit;
}

$meta_type = "all";
$meta_key = "";
$kmladd = "";
$coord = "";

$sql="select t.id as townid, t.ffile as ffile, t.hide as thide, t.name as name, t.nameto as nameto, t.namefrom as namefrom, t.namepo as namepo, t.namearound as namearound, t.booking as booking, "
    . "t.namein as namein,"
    . "t.descrip as description, t.area as id_area, t.Nshirota as Nshirota, t.Edolgota as Edolgota, t.metatitle as metatitle, t.metadescription as metadesc  from towns t where t.url='$town_url'";
$result = $dbi->query($sql);

if (!($myrow = mysqli_fetch_array($result))) {
  include("404.php");
  exit;
}
$town = $myrow["townid"];
$t_hide = $myrow["thide"];
$canon = "/gorod/$town_url.html";
// дальше общий текст для "нового" города и "старого".
include('include/gorod.php');