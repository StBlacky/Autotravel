<?php
include_once('include/config.php');
include_once('include/func.php');
include_once('include/head00.php');
$done     = 0;
$start    = 0;
$sqladd   = "";
$headadd   = " ";
$table    = "otklik";

if ($_pin == "") {
 $start = 0;
}

if (preg_match("/^\/o\/(\d+)$/",$_pin,$exa)) {
 $start = $exa[1];
 $table = "otklik";
}

if (preg_match("/^\/e\/(\d+)$/",$_pin,$exa)) {
 $start = $exa[1];
 $table = "exciting";
}

if (preg_match("/^\/n\/(\d+)$/",$_pin,$exa)) {
 $start = $exa[1];
 $table = "news";
}

if (($table == "otklik") and (!($done))) {
 $title = "Архив отзывов.";
 $sqlzagr="";
 include('include/head01.php');
 $f1 = $start - $_count_list;
 if ($f1 < 0) {
  $f1 = 0;
 }
 $f2 = $start + $_count_list;
 if ($_zagr_mode) {
   $sqlzagr = " and e.area>=0 ";
 }
 $sql = "SELECT o.nick as nick, FROM_UNIXTIME(o.time, '%e.%m.%Y') as o_time, t.id as town_id, o.exciting as ex_id,
         e.type as type, t.name as town_name, t.hide as town_hide, e.name as ex_name
         FROM otklik  o, towns t, exciting e where
         e.id = o.exciting and t.id = e.town $sqlzagr order by o.time desc limit $start,$_count_list";
 $result = $dbi->query($sql);
 while ($myrow = mysqli_fetch_array($result)) {
  $s_nick = $myrow["nick"];
  $s_town = $myrow["town_name"];
  $o_time = $myrow["o_time"];
  $s_townid = $myrow["town_id"];
  $t_hide = $myrow["town_hide"];
  $s_ex = $myrow["ex_id"];
  $s_exname = $myrow["ex_name"];
  $s_type = $myrow["type"];
  echo "<p class=\"page-element\">".$o_time."&nbsp;&nbsp;";
  echo "<a class=arc href=\"/otklik.php/$s_ex\">$s_exname ($s_town) - от $s_nick</a>";
  echo "</p>\n";
 };
 mysqli_free_result($result);
 $done = 1;
}

if (($table == "exciting") and (!($done))) {
 $title = "Архив объектов.";
 $sqlzagr = "";
 include('include/head01.php');
 if ($_zagr_mode) {
   $sqlzagr = " and t.area>=0 ";
 }
 $f1 = $start - $_count_list;
 if ($f1 < 0) {
  $f1 = 0;
 }
 $f2 = $start + $_count_list;
 $sqlex = "SELECT e.id AS ex_id, t.id AS town_id, e.type AS TYPE,
           t.name AS town_name, t.hide AS town_hide, e.name AS ex_name
           FROM towns t, exciting e WHERE t.id = e.town $sqlzagr ORDER BY e.id DESC
           limit $start,$_count_list";
 $resultex = $dbi->query($sqlex);
 if($myrowex = mysqli_fetch_array($resultex)) {
    	do {
      		$s_town = $myrowex["town_name"];
    		$s_townid = $myrowex["town_id"];
    		$t_hide = $myrowex["town_hide"];
      		$s_ex = $myrowex["ex_id"];
      		$s_exname = $myrowex["ex_name"];
      		$s_type = $myrowex["type"];
          echo "<p class=\"page-element\">";
          echo "<a class=arc href=\"/otklik.php/$s_ex\">$s_exname ($s_town)</a>";
          echo "</p>\n";
	} while($myrowex = mysqli_fetch_array($resultex));
 }
 mysqli_free_result($resultex);
 $done = 1;
}

if (($table == "news") and (!($done))) {
 $title = "Архив новостей.";
 include('include/head01.php');
 $f1 = $start - $_count_list;
 if ($f1 < 0) {
  $f1 = 0;
 }
 $f2 = $start + $_count_list;
 $sql = "select id, date, title, spring from news order by date desc limit $start,$_count_list";
 $result = $dbi->query($sql);

 while ($myrow = mysqli_fetch_array($result)) {
     $id = $myrow["id"];
     $date = $myrow["date"];
     $datex = strftime("%d.%m.%Y", strtotime($date));
     $title = $myrow["title"];
     $spring = $myrow["spring"];
     echo "<p class=\"page-element\">".$datex."&nbsp;&nbsp;";
     echo "<a class=arc href=\"/news.php?id=$id\">$title</a>";
     echo "</p>\n";
 }

 $ii = mysqli_num_rows($result);
 mysqli_free_result($result);

 echo "\n";
 $done = 1;
}

if (!($done)) {
 include "404.php";
}

?>
