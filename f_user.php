<?php
// всплывающее окно по поиску пользователя. Засовывает id выбранного пользователя в
// указанное поле указанной формы родительского документа

include_once('include/config.php');
include_once("include/func.php");
include("include/head01.php");

$tname = mysafestring($_REQUEST["find"]);
$form = mysafestring($_REQUEST["form"]);
$field = mysafestring($_REQUEST["field"]);
$id = (int)mysafestring($_REQUEST["id"]);

$submitx = $_REQUEST["submitx"];

if (!$submitx) {
 $listuser="";
 if ($tname != "") {
// передана часть имени. Надо поискать пользователей и построить список.

  $sql2 = "select username, user_id from bl_users where username like '".$tname."%' order by username asc";
  $result2 = $dbi->query($sql2); 
  if($myrow2 = mysqli_fetch_array($result2)) {
   do {
    $id = $myrow2["user_id"];
    $name = $myrow2["username"];
    $listuser=$listuser . "<option value=\"$id\">$name\n";   
   } while($myrow2 = mysqli_fetch_array($result2));
  }
  else {
   $error = "!";
  }
  mysqli_free_result($result2);
 }
 echo "<div class=travel0 align=center>\n";
 if (!$error) {
  echo "<form name=find><table border=0>";
  echo "<TR>\n";
  echo "<TD align=right valign=top width=225><b>Выбрать из найденного:</b><TD>\n";
  echo "<TD align=left>\n";
  echo "<input type=hidden name=form value=$form>\n";
  echo "<input type=hidden name=field value=$field>\n";
  echo "<select size=1 name=id>";  
  echo "$listuser\n</select>\n";
  echo "</TD>\n</TR>\n";
  echo "<TR><TD colspan=2 align=center>\n";
  echo "<input type=submit name=submitx value='Нашёл!' Onclick=AddIdN('$form','$field')>&nbsp;\n";
  echo "<input type=submit name=submitx value='Отмена' Onclick='window.close()'></center>\n";
  echo "</TD></TR>\n";
  echo "</table></form>";
 }
 else {
   echo "<h3>Увы, ничего не нашлось";
   echo "</h3>";
  echo "<input type=submit name=submitx value='Отмена' Onclick='window.close()'>\n";
 }
 echo "</div>\n";
}
?>
