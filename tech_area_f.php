<?php 
include_once('include/config.php');
include_once('include/func.php');
include_once('include/func_bs.php');
include_once('include/head00.php');

$www_dir = "/home/blacky/data/www";
if (preg_match("/(\-*\d+)$/",$_pin,$exa)) {
 $a = $exa[1];
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
$off = $off--;
if ($off<0) {
  $off=0;
}

if ($a) {
 if ($off) {
   $offset = ' OFFSET '. $off * 40; 
 }
 else {
   $offset = '';  
 }
 $sql = "select f.file as file, f.exciting as exciting, f.author as author, f.comment as comment, f.mode as mode, t.name as tname from foto f, towns t
         where $sqladd f.town = t.id and t.area = $a order by mode desc, date desc LIMIT 40 $offset";
 //echo "<!-- $sql -->\n ";
 //var_dump($_GET);
 $result = $dbi->query($sql);
 if ($result) {
   $sp_slides = fotos_area($result);
   echo $sp_slides;
   mysqli_free_result($result);
 }  
}
else {
  echo $_pin;
}
