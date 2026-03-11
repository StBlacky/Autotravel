<?php
 echo "<div align=center>\n";
 echo "<form action='/exi.php' target='_blank'><table width='95%' border=1 cellpadding=1>";
 echo "<tr><td width=300 class=\"t14 tb tc\">Вывести всю информацию по $s_namepo:\n<br><br>";
 if ($exlist) {
  echo "<input type=hidden name=list value='$exlist'>\n";
 }
 if ($addhead) {
  echo "<input type=hidden name=head value='$addhead'>\n";
 }

 echo "<input type=hidden name=town value=$town>\n";
 echo "<input type=submit value='Отчёт (для печати)'>\n</td><td width=350 class=travell0>";
 while(list($type_id,$type_name)=each($r_excitings)) {
   echo "<input type=checkbox name=et0".$type_id." value=1 CHECKED>$type_name<br>\n";
 }
 echo "<input type=checkbox name=otk value=1>Отзывы посетителей<br>\n";
 echo "<input type=checkbox name=photo value=1>Фотографии<br>\n";
 //if ($r_reports0) {
 // echo "<input type=checkbox name=et00 value=1>Отчёты о поездках в $s_nameto<br>\n";
 //}
 echo "</td></tr></table></form>\n";

// echo "<form action='/cgi-bin/bibel-cgi.pl'><table border=1 width=95% cellpadding=1>";
// echo "<tr><td class=\"t14 tb tc\" width=300>Вывести всю информацию по городу $s_name в формате PalmDOC:\n<br><br>";
// echo "<input type=hidden name=town value=$town>\n";
// echo "<input type=submit value='Отчёт (для Palm)'>\n</td><td width=350 class=travell0>";
// reset($r_excitings);
// while(list($type_id,$type_name)=each($r_excitings)) {
//   echo "<input type=checkbox name=et".$type_id." value=1 CHECKED>$type_name<br>\n";
// }
// echo "</td></tr></table></form>\n";
 echo "</div>\n";
?>