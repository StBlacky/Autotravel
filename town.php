<?php
include_once('include/config.php');
include_once('include/func.php');
include_once('include/func_bs.php');
include_once('include/head00.php');
$colorbox = 0;
$masonry = 0;
$_display_tables = 1; // но может быть переопределена в настройках
if (!($_text_mode)) {
  $ug_gallery = 1;
}
else {
  $ug_gallery = 0;
}
$bootstrap = 1;

if (preg_match("/^\/(\d+)$/",$_pin,$exa)) {
 $town = $exa[1];
}
else {
 $town = $_REQUEST["town"];
 $town = (int) mysafestring($town);
}

if (!($town)) {
  include("404.php");
  exit;
}

$sql="select t.hide as thide, t.ffile as ffile, t.url as turl, t.name as name, t.nameto as nameto, t.namefrom as namefrom, t.namepo as namepo, t.namearound as namearound, t.namein as namein, t.booking as booking, "
        . "t.descrip as description, t.area as id_area, t.Nshirota as Nshirota, t.Edolgota as Edolgota  from towns t where t.id='$town'";
$result = $dbi->query($sql);
if (!($myrow = mysqli_fetch_array($result))) {
  include("404.php");
  exit;
}

if ($myrow["turl"]) {
  // редирект на gorod
  $e_id = getextown($town);
  $newuri = "/gorod/" . $myrow["turl"] . ".html";
  Header( "HTTP/1.1 301 Moved Permanently" );
  Header( "Location: $newuri" );
  exit;
}

$t_hide = $myrow["thide"];

$canon = "/town.php/$town";
// дальше общий текст для "нового" города и "старого".
include('include/gorod.php');
