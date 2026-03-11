<?php 
include_once('include/config.php');
include_once('include/func.php');
include_once('include/func_bs.php');
include_once('include/head00.php');

$www_dir = "/home/blacky/data/www";
if (preg_match("/(\d+)$/",$_pin,$exa)) {
 $town = $exa[1];
}

if ($_fotomanager) {
 $sqladd = "";
}
else {
 $sqladd = " mode > 0 and ";
}

if ($_foto_mode) {
 $sqladd = "";
}

$off = (int) $_GET["page"];
$off = $off-1;
if ($off<0) {
  $off=0;
}

if ($town) {
 $name = gettown_bs($town);
 $tnname = $name[1];
 if ($off) {
   $offset = ' OFFSET '. $off * 40; 
 }
 else {
   $offset = '';  
 }
 $sql = "select file, exciting, author, comment, mode from foto
         where $sqladd town = '$town' order by mode desc, date desc LIMIT 40 $offset";
 //echo "<!-- $sql -->\n ";
 //var_dump($_GET);
 $result = $dbi->query($sql);
 if ($result) {
   $sp_slides = fotos_town($town, $tnname, $result);
   echo $sp_slides[0];
   mysqli_free_result($result);
 }  
}
else {
    echo $_pin;
}
