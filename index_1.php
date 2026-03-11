<?php


include_once('include/config.php');
require_once "include/func.php";
require_once "include/func_bs.php";
$bootstrap = 1;
include_once('include/head00.php');
$canon = "/index.php";
$title="Автопутешествия по России и за границу: Города, Достопримечательности, Где остановиться, Где поесть, Маршруты, Дороги";
$metadesc="Города и области России и зарубежных стран, описание достопримечательностей. Поиск по базе гостиниц, отелей, кафе. Сведения о дорогах. Прокладка маршрута. Отчёты о поездках.";
$metatags="Автопутешествия Города России Области Достопримечательности Поесть Остановиться Маршруты Дороги";

include("include/header_bs.php");
$sqlzagr = "";
if ($_zagr_mode) {
  $sqlzagr = " and e.area >=0 ";
}
$_iexc_mode = (int) $_iexc_mode;
if (!($_iexc_mode)) {
  $_iexc_mode = 10;
}
$_inws_mode = (int) $_inws_mode;
if (!($_inws_mode)) {
  $_inws_mode = 5;
}

echo "<div class=\"container-fluid\" style=\"width:100%;\"><div class=\"row\">\n";
if ($_text_mode) {
  echo "<div class=\"col-xs-12\">\n";
}
else {
  echo "<div class=\"col-sm-9 col-xs-12\">\n";
}  
// началась столбец среднего столбца
// он составлен из нескольких таблиц через <br>
// сам столбец объявлен в последней строчке header.php

// блок новостей и события - первая таблица

echo "<div class=\"bodyline-main travell0\" itemprop=\"name\" content=\"Новости По России на авто. За границу на авто.\">\n"; //центральная часть 
   
echo "<div class=\"bodyline-news\">\n"; //раздел новостей
$sql = "select id, date, title, spring from news order by date desc limit $_inws_mode";
$result = $dbi->query($sql);

if($result) {
  while ($myrow = mysqli_fetch_array($result)) {
    $id = $myrow["id"];
    $date = $myrow["date"];
    $datex = strftime("%d.%m.%Y", strtotime($date));
    $title = $myrow["title"];
    $spring = $myrow["spring"];
    echo "<b>$datex</b> - <a href='/news.php?id=$id' rel=\"nofollow\">$title</a><br>\n"; 
  } 
  mysqli_free_result($result);
}
echo "</div>\n"; //раздел новостей

 

echo "<div class=\"row\"><div class=\"col-xs-12\"> <!-- если надо два раздела -->\n";
  echo "<div class=\"col-sm-6 col-xs-12 newobjects-otkliks\"> <!-- новые отзывы -->\n";
  $sql = "SELECT o.nick as nick, t.id as town_id, o.exciting as ex_id, e.type as type, t.name as town_name,"
           . " t.hide as town_hide, e.name as ex_name, e.area as ex_area FROM otklik  o, towns t, exciting e where e.id = o.exciting and t.id = e.town $sqlzagr "
           . "order by o.time desc limit $_iexc_mode";
  if ($_bad_count>0) {
       $sql = "SELECT o.nick as nick, t.id as town_id, o.exciting as ex_id, o.time as o_time, e.type as type, t.name as town_name,"
          . " t.hide as town_hide, e.name as ex_name, e.area as ex_area FROM otklik  o, towns t, exciting e"
          . " where e.id = o.exciting and t.id = e.town $sqlzagr "
          . "UNION SELECT om.nick as nick, tm.id as town_id, om.exciting as ex_id, om.time as o_time, em.type as type, tm.name as town_name,"
          . " tm.hide as town_hide, em.name as ex_name, em.area as ex_area FROM otklikm  om, towns tm, exciting em"
          . " where em.id = om.exciting and om.cook='$ctext' and tm.id = em.town $sqlzagr "
          . "order by o_time desc limit $_iexc_mode";
  }
  $result = $dbi->query($sql);
  if ($_text_mode) {
    echo "<h2>Новые отзывы</h2>";
  }
  else {
    echo "<h2><img src='/png/n-opinions.png' alt=\"Новые отзывы\"></h2>";
  }  

  if($result) {
    while($myrow = mysqli_fetch_array($result)) {
      $s_nick = $myrow["nick"];
      $s_town = $myrow["town_name"];
    	$s_townid = $myrow["town_id"];
    	$t_hide = $myrow["town_hide"];
      $s_ex = $myrow["ex_id"];
      $s_exname = $myrow["ex_name"];
      $s_type = $myrow["type"];
		  echo "<a itemprop=\"significantLink\" href=\"/otklik.php/$s_ex\">$s_exname ($s_town)</a> - от $s_nick";
      echo"<br>\n";
	  }
	  mysqli_free_result($result);
  }
  echo "<br><a class=\"tz tb\" href='/archive.php/o'>&gt;&gt;&gt;Архив отзывов&gt;&gt;&gt;</a>";
  echo "</div>\n"; //новые отзывы

  echo "<div class=\"col-sm-6 col-xs-12 newobjects-objects\">\n"; //новые объекты
  if ($_text_mode) {
    echo "<h2>Новые объекты</h2>";
  }
  else {
    echo "<h2><img src='/png/n-excitings.png' alt=\"Новые объекты\"></h2>";
  }
  $sqlex = "SELECT t.id AS town_id, e.id AS ex_id, e.type AS TYPE, t.name AS town_name, t.hide AS town_hide, e.name AS ex_name, e.area as ex_area "
        . "FROM towns t, exciting e WHERE t.id = e.town $sqlzagr ORDER BY e.id DESC LIMIT $_iexc_mode ";
  $resultex = $dbi->query($sqlex);

  if($resultex) {
    while($myrowex = mysqli_fetch_array($resultex)) {
    	$s_town = $myrowex["town_name"];
    	$s_townid = $myrowex["town_id"];
    	$t_hide = $myrowex["town_hide"];
    	$s_ex = $myrowex["ex_id"];
    	$s_exname = $myrowex["ex_name"];
    	$s_type = $myrowex["type"];
		  echo "<a itemprop=\"significantLink\" href=\"/otklik.php/$s_ex\">$s_exname ($s_town)</a>";
   		echo"<br>\n";
  	} 
	  mysqli_free_result($resultex);
  }
  echo "<br><a class=\"tz tb\" href='/archive.php/e'>&gt;&gt;&gt;Архив объектов&gt;&gt;&gt;</a>";
  echo "</div>\n";
echo "</div></div>\n"; //сдвоенный блок

echo "<div class=\"row\"><div class=\"col-md-12 col-xs-12\">\n"; // <!-- если надо два раздела -->
  echo "<div class=\"col-md-6 col-xs-12 newobjects-reports\">\n"; //новые отчеты
  if ($_text_mode) {
    echo "<h2>Новые отчеты</h2>";
  }
  else {
    echo "<h2><img src='/png/n-reports.png' alt=\"Новые отчеты\"></h2>";
  }  
  $sql_wd = "select rid, key_towns, time from l_reports order by time desc limit 5";
  $result_wd = $dbi->query($sql_wd);
  while ($myrow_wd = mysqli_fetch_array($result_wd)) {
    $s_key = $myrow_wd["key_towns"];
    $rid   = $myrow_wd["rid"];
    echo "<a href='/v_report.php/$rid'>$s_key</a><br>\n";
  }
  mysqli_free_result($result_wd);
  echo "</div>\n";

  echo "<div class=\"col-md-6 col-xs-12 newobjects-descriptions\">\n"; //новые описания
  if ($_text_mode) {
    echo "<h2>Новые описания</h2>";
  }
  else {
    echo "<h2><img src='/png/n-descriptions.png' alt=\"Новые описания\"></h2>";
  }  
  $sql_wd="select cook, obj, dtizm from l_text order by dtizm desc limit 5";
  $result_wd = $dbi->query($sql_wd);
  while ($myrow_wd = mysqli_fetch_array($result_wd)) {
     $obj   = $myrow_wd["obj"];
     $cook  = $myrow_wd["cook"];
     $type  = substr($obj,0,1);
     $key   = (int) substr($obj,1);
     $link = "";
     if ($type == 't') {
        $link = "<a itemprop=\"significantLink\" href='/town.php/$key'>" . gettownname($key) . "</a>";
     }
     if ($type == 'e') {
       $link = "<a itemprop=\"significantLink\" href='/otklik.php/$key'>" . getexname($key) . "</a>";
     }
     echo "$link<br>\n";

  }
  mysqli_free_result($result_wd);
  echo "</div>\n"; //описания
echo "</div></div>\n"; //сдвоенный блок

echo "<div class=\"row\"><div class=\"col-xs-12\">\n"; // <!-- если надо два раздела -->
  echo "<div class=\"col-md-6 col-xs-12 newobjects-traces\">\n"; //cостояние трасс
  if ($_text_mode) {
    echo "<h2>Новости о состоянии дорог</h2>";
  }
  else {
    echo "<h2><img src='/png/n-tracestates.png' alt=\"Новости о состоянии дорог\"></h2>";
  }
  echo "Недавно проехали по нашим дорогам и есть что рассказать? <br>Поделитесь с другими автопутешественниками,  перейдите на страницу <a href=\"trace.php\">Дороги России</a>.<br>";

  $sql_tr = "select ts.kbeg as kbeg, ts.kend as kend, tr.id as trace, tr.name as tr_name, tr.of_name as of_name  "
        . "from otklikt as ts, trace as tr where ts.ido>0 and tr.id=ts.idtr ORDER BY ts.time DESC LIMIT 10 ";
  if ($_bad_count>0) {
    $sql_tr = "SELECT ts.kbeg as kbeg, ts.time as time, ts.kend as kend, tr.id as trace, tr.name as tr_name, tr.of_name as of_name  "
      . "from otklikt as ts, trace as tr where ts.ido>0 and tr.id=ts.idtr UNION "
      . "SELECT ts2.kbeg as kbeg, ts2.time as time, ts2.kend as kend, tr2.id as trace, tr2.name as tr_name, tr2.of_name as of_name  "
      . "from otklikt as ts2, trace as tr2 where ts2.ido<0 and ts2.cook='".$ctext."' and tr2.id=ts2.idtr ORDER BY time DESC LIMIT 10 ";
  }
  $result_tr = $dbi->query($sql_tr);
   
  if($result_tr) {
 	  while($myrow_tr = mysqli_fetch_array($result_tr)) {
     	$kbeg = $myrow_tr["kbeg"];
 	  	$kend = $myrow_tr["kend"];
      $trace = $myrow_tr["trace"];
      $tr_name = $myrow_tr["tr_name"];
		  $of_name = $myrow_tr["of_name"];
 		  if (!$of_name){
        $tr_name = $of_name . " " . $tr_name;          
      }
		  echo "<a itemprop=\"significantLink\" href=\"/tracestate.php/$trace\">$tr_name (от $kbeg км до $kend км)</a>";
   	  echo"<br>\n";
      } 
    mysqli_free_result($result_tr);
  }
  echo "</div>\n"; //состояние трасс
  echo "<div class=\"col-md-6 col-xs-12 bodyline-marshruts\">"; // новые маршруты
  $sql_wd="select distinct trip_desc.id as id, trip_ex.descrip as dex, trip_desc.descrip as descrip, trip_d.descrip as dd "
    . " from trip_desc, trip_ex, trip_d where trip_ex.id = trip_desc.ex  and trip_d.id = trip_desc.d order by id desc limit 2";
  $result_wd = $dbi->query($sql_wd);
  if ($_text_mode) {
    echo "<h2>Новые маршруты самостоятельных автопутешествий</h2>";
  }
  else {
    echo "<h2><img src='/png/n-routes.png' alt=\"Новые маршруты самостоятельных автопутешествий\"></h2>\n";
  }
  while ($myrow_wd = mysqli_fetch_array($result_wd)) {
    $trip_id = $myrow_wd["id"];
    $trip_ex = $myrow_wd["dex"];
    $trip_d = $myrow_wd["dd"];
    $descrip  = $myrow_wd["descrip"];
    echo "<a class=travell5 href='/trip_rec.php/$trip_id'> $trip_ex, $trip_d</a><br>";
    echo "<p class=travell0>$descrip</p>\n"; 
  }
  mysqli_free_result($result_wd);
  echo "</div>\n"; //маршруты
echo "</div></div>\n"; //сдвоенный блок

echo "</div><!-- /bodyline-main -->\n";// закрыли средний столбец

// города
if (!($_zagr_mode)) {
  $sqltt = "select t.name as town, t.url as turl, t.hide as hide, t.rate as rate, t.id as tid, a.name as area from towns t, areas a where t.rate > ($thighrate) and a.id=t.area order by t.name";
}
else {
  $sqltt = "select t.name as town, t.url as turl, t.hide as hide, t.rate as rate, t.id as tid, a.name as area from towns t, areas a where a.id > 0 and t.rate > ($thighrate) and a.id=t.area order by t.name";    
}
$resultt = $dbi->query($sqltt);
$numrowst = mysqli_num_rows($resultt);
echo " <div class=\"row\">\n  <div class=\"col-xs-12\">\n";
echo "<h2>Популярные туристические направления</h2>\n";
echo "<p>Здесь ";
echo echo_int($numrowst, array("перечислены","перечислен","перечислены"))." <strong>$numrowst ";
echo echo_int($numrowst,array("наиболее популярных среди путешественников городов и населенных пунктов","наиболее популярный среди путешественников город и населенный пункт","наиболее популярных среди путешественников города и населенных пункта"));
if (!($_zagr_mode)) {
  echo " России и зарубежья";
}
else {
  echo " России";
}
echo "</strong></p>";
echo "\n\n<div class=column>\n".gettownsresultex($resultt,1)."\n</div>\n\n";
echo " </div>\n";
echo "</div>\n";
echo "</div>\n";
if (!($_text_mode)) {
  echo "<div class=\"col-sm-3 col-xs-12\" style=\"border-left: solid 1px #888;\">\n";
  include("include/reklama_bs.php");
  echo "\n</div><!-- /row main -->\n\n";
}
echo "</div></div>\n";
$_totop = 1;
include("include/footer_bs.php");
