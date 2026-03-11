<?php
// всплывающее окно по поиску темы форума. Засовывает id выбранной темы в
// указанное поле указанной формы родительского документа

include_once('include/config.php');
include_once("include/func.php");
include("include/head01.php");

$submitx = $_REQUEST["submitx"];

$tname = mysafestring($_REQUEST["find"]);
$form = mysafestring($_REQUEST["form"]);
$field = mysafestring($_REQUEST["field"]);
$id = (int)mysafestring($_REQUEST["id"]);

$tnameid     = (int)$tname;
$tnamestring = (string)$tnameid;

if ($tnamestring === $tname) {
 $sqladd = " and t.topic_id = $tnameid and p.post_id = t.topic_last_post_id";
}
else {
 $sqladd = " and t.topic_title like '%$tname%'  and p.post_id = t.topic_last_post_id and p.post_time > (unix_timestamp() - 3600*24*30) ";
}

if (!$submitx) {
 $listtown="";

 $sql2 = "SELECT t.topic_id as topic_id, t.topic_title as name,
           p.post_id as post_id
           FROM bl_topics t, bl_posts p 
           WHERE (t.forum_id = 8 or t.forum_id = 20) $sqladd ";
 $result2 = $dbi->query($sql2); 
 if($myrow2 = mysqli_fetch_array($result2)) {
   do {
    $id = $myrow2["topic_id"];
    $name = $myrow2["name"];
    $listtown=$listtown . "<option value=\"$id\">$name\n";   
   } while($myrow2 = mysqli_fetch_array($result2));
 }
 else {
   $error = "!";
 }
 mysqli_free_result($result2);
 echo "<div class=travel0 align=center>\n";
 if (!$error) {
  echo "<form name=find><table border=0>";
  echo "<TR>\n";
  echo "<TD align=right valign=top width=225><b>Выбрать из найденного:</b><TD>\n";
  echo "<TD align=left>\n";
  echo "<input type=hidden name=form value=$form>\n";
  echo "<input type=hidden name=field value=$field>\n";
  echo "<select size=1 name=id>";  
  echo "$listtown\n</select>\n";
  echo "</TD>\n</TR>\n";
  echo "<TR><TD colspan=2 align=center>\n";
  echo "<input type=submit name=submitx value='Нашёл!' Onclick=AddId('$form','$field')>&nbsp;\n";
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
