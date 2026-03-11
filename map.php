<?php

include_once('include/config.php');
include_once('include/func.php');
include_once('include/head00.php');
?>
<?php
$metatags = "Интерактивная карта";
$title = "Интерактивная карта";
$banners_shown = "yes";
$bootstrap = 1;
#include_once("include/head01.php");

// выбираем режим карты
if ($_gmap_mode==9) { // не показывать
  $dispgmap = "";   
}
elseif($_gmap_mode==1) { // osm
  $dispgmap = 1; 
}
elseif($_gmap_mode==2) { // yandex
  $dispgmap = 2;         
}
else { // google
  $dispgmap = 3;            
}
$jplist = 1;
$ratingjs = 1;
$validator = 1;
if (!($_text_mode)) {
  $colorbox  = 1;
}
else {
  $colorbox  = 0;
}
include('include/header_bs.php'); //должно стоять после всех мета-тегов
echo "\n<!-- |$_pin| ".$_SERVER["QUERY_STRING"]." | -->\n";

?>
<table width="100%">
<tr><td valign="top" width=100%>
<div id="gmapv3" class="map-ct" style="display:none"></div>
<?php
 if ($_pin) {
   $customscript .= "\n initmapv".$dispgmap."(\"/kml.php".$_pin.".kml\",null,null,null);\n";
 }
 else {
   $customscript .= "\n initmapv".$dispgmap."(\"/kml.php?".$_SERVER["QUERY_STRING"]."\",null,null,null);\n";
 }
?> 
</td></tr>
</table>
<?php
include('include/footer_bs.php');
?>

