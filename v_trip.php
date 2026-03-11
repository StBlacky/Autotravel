<?php 

// предназначается для показа выбранного маршрута
//
// пример маршрута:
// t51x1yz,r101x159,t48,r102x126,t47x2z,r33x180,t1061

include_once("include/f_trip.php");

// Начинается программа

// используется в поиске по отчётам

$townslist="";
$tlist = "";

if ($trip) {

// собственно показ маршрута

 $objs = explode(",",$trip);
 if (is_array($objs)) {
  echo "<div align=center><p class=travel01>\n";
  $lastnum = sizeof($objs) - 1;
  reset($objs);
  $totaltrip = 0;
  $num=0;
  foreach ($objs as $obje) {

   preg_match('/^(\D)(\d+)(x*)([^,]*)$/',$obje,$obja);

   $add="";

   if ($obja[3] == "x") {
     $add=$obja[4];
   }
   if ($obja[1] == "t") {
    if ($tlist) {
     $tlist = $tlist . "," . $obja[2];
    }
    else {
     $tlist = $obja[2];
    }
   }
   if ($obja[1] == "r") {
    if (($obja[3] == "x") and ($obja[4] <> "")) {
     $totaltrip = $totaltrip + (int) $obja[4];
     $add=$obja[4]." км. (итого $totaltrip)";
    }
    display_trace($obja[2],$add);
   }
   if ($obja[1] == "t") {

// view:
// "v" - просмотр c формой
// "w" - предпросмотр, в отдельном окне
// "a" - просмотр без формы
// "r" - печатный отчет
// "e" - для редактирования отчёта
// "t" - для редактирования маршрута

    if (($view == "a") or (($type == "t") and ($view == "t"))) {
      display_objt($obja[2]);
    }
    else {
     if ($view == "v") {
      display_obj($obja[2],$add);
     }
     if ($view == "w") {
      display_vobj($obja[2]);
     }
     if ($view == "r") {
      display_report($obja[2],$add);
     }
     if ($view == "t") {
      if ($num == $lastnum) {
       $foundid = "";
      }
      else {
       $foundid = CheckTrip($trip,$num);
      }
      display_etripobj($obja[2],$num,$foundid);
     }
     if ($view == "e") {
      display_editobj($obja[2],$add,$num);
     }
    }
   }
   $num++;
  }
#  echo "</table>\n";
  echo "</p></div>\n";
#  echo "<p class=\"t14 tb tc\">Итого $totaltrip км.\n</p>";

  echo "";
  if ($view == "v") {

// Если "просмотр" c формой
   echo "<br>\n";
   echo "<div align=center><font class=travell5>Маршрут (в отдельном окне):</font>\n";
   echo "<form name=report target=_blank method=post action=".$_SERVER["PHP_SELF"].">\n";
   echo "<input type=hidden name=trip value='$trip'>\n";
   echo "<input class=atb type=submit name=view value='Вывести на печать r'>\n";
   echo "<input class=atb type=submit name=view value='Редактировать t'>\n";
   echo "<input class=atb type=submit name=view value='Подготовить к печати e'>\n";
   echo "</form><br>\n";
   echo "<Form name=gmapx method=post action=kml.php target=_blank>\n";
   echo "<input type=hidden name=tlist value=\"$tlist\">\n";
   echo "<input class=atb type=submit name=f value='В файл GPX'>\n";
   echo "<input class=atb type=submit name=f value='В файл OZI WPT'>\n";
   echo "<input class=atb type=submit name=f value='В файл Google KML'>\n";
   echo "<input class=atb type=submit name=f value='На карте Google HTM'>\n";
   echo "</form>\n</div>\n";

   echo "<div align=center><p class=travell0>\n";
   echo "[ <a class=travell5m href='ch_trip.php?trip=$trip&view=v'>Прямой линк на этот маршрут</a> ]\n";
   echo "</p></div>\n";

   echo "<div align=center><form target=_blank name=scity method=post action=/rd_report.php>\n";
   echo "<input type=hidden name=rtype[] value=1>\n";
   echo "<input type=hidden name=rtype[] value=2>\n";
   echo "<input type=hidden name=rtype[] value=3>\n";
   echo "<input type=hidden name=selmonth value=3>\n";
   echo "<input type=hidden name=selyear value=1>\n";
   echo "<input type=hidden name=city value=\"$townslist\">\n";
   echo "<font class=travell5>Отчёты о путешествиях по городам $townslist :</font><br>\n";
   echo "<input class=atb type=submit name=submit value=\"Выбрать\"></form></div>\n";

  }

 }
}

?>
