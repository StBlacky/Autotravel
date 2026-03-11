<?php
// всплывающее окно по поиску примечательности. Засовывает id выбранного города в
// указанное поле указанной формы родительского документа

include_once('include/config.php');
include_once("include/func.php");
include("include/head01.php");
$submitx = $_REQUEST["submitx"];

$form  = mysafestring($_REQUEST["o"]);
$field = mysafestring($_REQUEST["f"]);
$id = (int)mysafestring($_REQUEST["id"]);

$sqladd = "";
$listtown = "<option value=\"0\">/объект не указан/\n";

if (!$submitx) {
 if ($form == 'ltext') {
  $sqladd = " and type = 1 ";
 }
 if ($id != "") {
// передан город
  $sql2 = "select name, id from exciting where town = $id $sqladd order by name asc";
  $result2 = $dbi->query($sql2); 
  if($myrow2 = mysqli_fetch_array($result2)) {
   do {
    $id = $myrow2["id"];
    $name = $myrow2["name"];
    $listtown=$listtown . "<option value=\"$id\">$name\n";   
   } while($myrow2 = mysqli_fetch_array($result2));
  }
  mysqli_free_result($result2);
 }
 else {
  $error = "Не указан город!";
 }
 echo "<div class=travel0 align=center>\n";
 if (!$error) {
  echo "<form name=find><table border=0>";
  echo "<TR>\n";
  echo "<TD align=right valign=top width=225><b>Выбрать из найденного:</b><TD>\n";
  echo "<TD align=left>\n";
  echo "<input type=hidden name=o value=$form>\n";
  echo "<input type=hidden name=f value=$field>\n";
  echo "<select size=1 name=id>";  
  echo "$listtown\n</select>\n";
  echo "</TD>\n</TR>\n";
  echo "<TR><TD colspan=2 align=center>\n";
  echo "<input type=submit name=submitx value='Нашёл!' Onclick=AddExId('$form','$field')>&nbsp;\n";
  echo "<input type=submit name=submitx value='Отмена' Onclick='window.close()'></center>\n";
  echo "</TD></TR>\n";
  echo "</table></form>";
 }
 else {
   echo "<h3>Увы, ничего не нашлось $error";
   echo "</h3>";
   echo "<input type=submit name=submitx value='Отмена' Onclick='window.close()'>\n";
 }
 echo "</div>\n";
}
?>