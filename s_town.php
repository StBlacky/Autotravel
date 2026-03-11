<?php
// всплывающее окно по поиску города. Засовывает id выбранного города в
// указанное поле указанной формы родительского документа

include_once('include/config.php');
include_once("include/func.php");
include("include/head01.php");

$submitx = $_REQUEST["submitx"];

$tname = $_REQUEST["f"];
$tname = mysafestring($tname);
$xid   = $_REQUEST["x"];
$xid   = (int)mysafestring($xid);
$form  = mysafestring($_REQUEST["o"]);
$field = mysafestring($_REQUEST["i"]);
$hid   = (int)mysafestring($_REQUEST["h"]);
$id    = (int)mysafestring($_REQUEST["id"]);

$sqladd = " and t.hide<>1 ";

if ($hid > 0) {
 $sqladd = "";
}

echo "<div class=travel0 align=center>\n";
echo "<form name=find><table border=0>";
echo "<TR><TD colspan=2 class=travell0 align=center>\n";
echo "<h3>Поискать города на букву:";
echo "</h3>";
echo "<a class=travell5m href=/s_town.php?o=$form&h=$hid&x=$xid&i=$field&f=А> А </a>&nbsp;";
echo "<a class=travell5m href=/s_town.php?o=$form&h=$hid&x=$xid&i=$field&f=Б> Б </a>&nbsp;";
echo "<a class=travell5m href=/s_town.php?o=$form&h=$hid&x=$xid&i=$field&f=В> В </a>&nbsp;";
echo "<a class=travell5m href=/s_town.php?o=$form&h=$hid&x=$xid&i=$field&f=Г> Г </a>&nbsp;";
echo "<a class=travell5m href=/s_town.php?o=$form&h=$hid&x=$xid&i=$field&f=Д> Д </a>&nbsp;";
echo "<a class=travell5m href=/s_town.php?o=$form&h=$hid&x=$xid&i=$field&f=Е> Е </a>&nbsp;";
echo "<a class=travell5m href=/s_town.php?o=$form&h=$hid&x=$xid&i=$field&f=Ж> Ж </a>&nbsp;";
echo "<a class=travell5m href=/s_town.php?o=$form&h=$hid&x=$xid&i=$field&f=З> З </a>&nbsp;";
echo "<a class=travell5m href=/s_town.php?o=$form&h=$hid&x=$xid&i=$field&f=И> И </a>&nbsp;";
echo "<a class=travell5m href=/s_town.php?o=$form&h=$hid&x=$xid&i=$field&f=Й> Й </a>&nbsp;";
echo "<a class=travell5m href=/s_town.php?o=$form&h=$hid&x=$xid&i=$field&f=К> К </a>&nbsp;";
echo "<a class=travell5m href=/s_town.php?o=$form&h=$hid&x=$xid&i=$field&f=Л> Л </a>&nbsp;";
echo "<a class=travell5m href=/s_town.php?o=$form&h=$hid&x=$xid&i=$field&f=М> М </a>&nbsp;";
echo "<a class=travell5m href=/s_town.php?o=$form&h=$hid&x=$xid&i=$field&f=Н> Н </a>&nbsp;";
echo "<a class=travell5m href=/s_town.php?o=$form&h=$hid&x=$xid&i=$field&f=О> О </a>&nbsp;";
echo "<a class=travell5m href=/s_town.php?o=$form&h=$hid&x=$xid&i=$field&f=П> П </a>&nbsp;";
echo "<a class=travell5m href=/s_town.php?o=$form&h=$hid&x=$xid&i=$field&f=Р> Р </a>&nbsp;";
echo "<a class=travell5m href=/s_town.php?o=$form&h=$hid&x=$xid&i=$field&f=С> С </a>&nbsp;";
echo "<a class=travell5m href=/s_town.php?o=$form&h=$hid&x=$xid&i=$field&f=Т> Т </a>&nbsp;";
echo "<a class=travell5m href=/s_town.php?o=$form&h=$hid&x=$xid&i=$field&f=У> У </a>&nbsp;";
echo "<a class=travell5m href=/s_town.php?o=$form&h=$hid&x=$xid&i=$field&f=Ф> Ф </a>&nbsp;";
echo "<a class=travell5m href=/s_town.php?o=$form&h=$hid&x=$xid&i=$field&f=Х> Х </a>&nbsp;";
echo "<a class=travell5m href=/s_town.php?o=$form&h=$hid&x=$xid&i=$field&f=Ц> Ц </a>&nbsp;";
echo "<a class=travell5m href=/s_town.php?o=$form&h=$hid&x=$xid&i=$field&f=Ч> Ч </a>&nbsp;";
echo "<a class=travell5m href=/s_town.php?o=$form&h=$hid&x=$xid&i=$field&f=Ш> Ш </a>&nbsp;";
echo "<a class=travell5m href=/s_town.php?o=$form&h=$hid&x=$xid&i=$field&f=Щ> Щ </a>&nbsp;";
echo "<a class=travell5m href=/s_town.php?o=$form&h=$hid&x=$xid&i=$field&f=Э> Э </a>&nbsp;";
echo "<a class=travell5m href=/s_town.php?o=$form&h=$hid&x=$xid&i=$field&f=Ю> Ю </a>&nbsp;";
echo "<a class=travell5m href=/s_town.php?o=$form&h=$hid&x=$xid&i=$field&f=Я> Я </a>&nbsp;";
echo "</TD></TR>\n";

$listtown="<option value=\"9999\">/город не указан/\n";
$error = "";

// список популярных
$sql2 = "select t.name as tname, t.id as tid, a.name as aname
          from towns t, areas a where a.id= t.area and t.id <> $xid
          and t.rate > ($thighrate)
          order by t.name asc";

if (($xid) and (!($tname))) {
// передан город старта. Список популярных городов в окрестностях до 300 км
 $sql2    = "SELECT Nshirota, Edolgota from towns where id = $xid";
 $result2 = $dbi->query($sql2);
 $nsh = 0;
 $edg = 0;
 if ($myrow = mysqli_fetch_array($result2)) {
   $nsh = (float)$myrow["Nshirota"];
   $edg = (float)$myrow["Edolgota"];
 }
 if (($edg) and ($nsh)) {
  $ctt = cos(deg2rad($nsh));
  $ctt = $ctt*$ctt;
  if ($ctt < 0.1) {
     $ctt = 0.1;
  }
  $sql2 = "select t.name as tname, t.id as tid, a.name as aname
          from towns t, areas a where a.id= t.area and t.id <> $xid
          and t.rate > ($thighrate)
          and (
          ((Nshirota - $nsh)*(Nshirota - $nsh) +
          ((Edolgota - $edg)*(Edolgota - $edg)*$ctt)) < 8
          )
          order by t.name asc";
 }
}

if ((!$submitx) and ($tname != "")) {
// передана часть названия. Надо поискать города и построить список.
 $sql2 = "select t.name as tname, t.id as tid, a.name as aname
          from towns t, areas a where a.id=t.area and t.name like '".$tname."%'
          $sqladd
          order by t.name asc";
}
if ($sql2) {
 $result2 = $dbi->query($sql2);
 if($myrow2 = mysqli_fetch_array($result2)) {
  do {
   $id = $myrow2["tid"];
   $name = $myrow2["tname"];
   $area = $myrow2["aname"];
   preg_match('/^\w(\d+).*/',$name,$xtmp);
   if (!($xtmp[1] != "")) {
    $listtown=$listtown . "<option value=\"$id\">$name ($area)\n";
   }
  } while($myrow2 = mysqli_fetch_array($result2));
 }
 mysqli_free_result($result2);
}

if (!$error) {
  echo "<TR><TD valign=top align=right class=travell0>\n";
  echo "<h3>или выбрать из списка";
  if ($tname) {
   echo " на букву $tname";
  }
  else {
   if ($xid) {
    echo " наиболее популярных";
   }
  }
  echo ":</h3>";
  echo "<TD align=left>\n";
  echo "<input type=hidden name=o value=$form>\n";
  echo "<input type=hidden name=i value=$field>\n";
  echo "<input type=hidden name=f value=$find>\n";
  echo "<select size=1 name=id>";
  echo "$listtown\n</select>\n";
  echo "</TD>\n</TR>\n";
  echo "<TR><TD colspan=2 align=center>\n";
  echo "<input type=submit name=submitx value='Нашёл!' Onclick=NewAddId('$form','$field')>&nbsp;\n";
  echo "<input type=submit name=submitx value='Отмена' Onclick='window.close()'></center>\n";
  echo "</TD></TR>\n";
}
//else {
//  echo "<TR><TD colspan=2 align=center>\n";
//  echo "<h3>Увы, ничего не нашлось";
//  echo "</h3>";
//  echo "<input type=submit name=submitx value='Отмена' Onclick='window.close()'>\n";
//  echo "</TD></TR>\n";
//}
echo "</table></form>";
echo "</div>\n";

?>
