<?php
$title="Фотоконкурс";
include_once("include/header.php");
$vkdescs  = array();

$fotod = "/home/blacky/data/www/foto";
$year  = "";
if (preg_match("/^\/(\d+)$/",$_pin,$exa)) {
 $year = $exa[1];
}

if (is_dir("$fotod/y$year")) {
 $fotod = $fotod . "/y" . $year;
 echo "<h3>Архив " . $year . " Результаты:</h3>\n";
}
else {
 $year = "";
 echo "<h3>Всего проголосовало 18 человек. Результаты:</h3>\n";
}

echo "<table border = 1 width = 100%>\n<tr>";
$fotocount = 1;

$fd = fopen("$fotod/total", "r");
while (!feof ($fd)) {
    $buffer = fgets($fd, 4096);
    list($votef,$namef,$commentf)=explode("\t",$buffer);
    if ($namef > "")  {
      $names = "foto/$namef" . "-s.jpg";
      if (!(is_file($fotod."/".$namef."-s.jpg"))) {
       $names = "foto/$namef" . ".jpg";
      }
      echo "<td><a target=_blank href=/foto/$namef.jpg><IMG border=0 SRC=/$names></a><br>\n";
      echo "Балл: $votef<br>$commentf</td>\n";
      $currfoto = (int) ($fotocount / 3);
      $currfoto = $currfoto * 3;
      if ($fotocount === $currfoto ) {
            echo "</tr>\n";
            echo "<tr>\n";
      }
      $fotocount++;
    }
}
fclose ($fd);

echo "</tr></table>\n";

include_once("include/footer.php");

?>