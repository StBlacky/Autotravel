<?php
include_once('include/config.php');
$bootstrap = 1;

include_once('include/func.php');
include_once('include/head00.php');
$done     = 0;
$start    = 0;
$sqladd   = "";
$headadd   = " ";
$table    = "otklik";
$url = "/tech-archive.php/o/";

if ($_pin == "") {
 $start = 0;
}

if (preg_match("/^\/o\/(\d+)$/",$_pin,$exa)) {
 $newuri = "/archive.php/o";
 Header( "HTTP/1.1 301 Moved Permanently" );
 Header( "Location: $newuri" );
 exit;
}
if (preg_match("/^\/o$/",$_pin,$exa)) {
 $table = "otklik";
 $url = "/tech-archive.php/o/";
}

if (preg_match("/^\/t\/(\d+)$/",$_pin,$exa)) {
 // тут $start это id юзера   
 $start = $exa[1];
 $table = "trip";
 $url = "";
}

if (preg_match("/^\/x\/(\d+)$/",$_pin,$exa)) {
 $sqladd = " and typeuser = 1 ";
 $start = $exa[1];
 $headadd = ", организованные ";
 $table = "trip";
 $url = "";
}

if (preg_match("/^\/e\/(\d+)$/",$_pin,$exa)) {
 $newuri = "/archive.php/e";
 Header( "HTTP/1.1 301 Moved Permanently" );
 Header( "Location: $newuri" );
 exit;
}
if (preg_match("/^\/e$/",$_pin,$exa)) {
 $table = "exciting";
 $url = "/tech-archive.php/e/";
}

if (preg_match("/^\/n\/(\d+)$/",$_pin,$exa)) {
 $newuri = "/archive.php/n";
 Header( "HTTP/1.1 301 Moved Permanently" );
 Header( "Location: $newuri" );
 exit;
}
if (preg_match("/^\/n$/",$_pin,$exa)) {
 $table = "news";
 $url = "/tech-archive.php/n/";
}

if ($url) {
 $infinitescroll = 1;
 $customscript = '
  $(\'#page-container\').infiniteScroll({
    path: function() {
      var pageNumber = ( this.loadCount + 1 ) * '.$_count_list.';
      return \''.$url.'\' + pageNumber;
    },
    append: \'.page-element\',
    history: false,
  });
  ';
}
$_totop = 1;

if (($table == "otklik") and (!($done))) {
 $title = "Новые отзывы";
 $sqlzagr="";
 include('include/header_bs.php');
 if ($_zagr_mode) {
   $sqlzagr = " and e.area>=0 ";
 }
 $sql = "SELECT o.nick as nick, FROM_UNIXTIME(o.time, '%e.%m.%Y') as o_time, t.id as town_id, o.exciting as ex_id,
         e.type as type, t.name as town_name, t.hide as town_hide, e.name as ex_name
         FROM otklik  o, towns t, exciting e where
         e.id = o.exciting and t.id = e.town and e.mainid>=0 $sqlzagr order by o.time desc limit $_count_list";
 $result = $dbi->query($sql);
 echo "<div class=\"container-fluid\" style=\"width:100%\">";
 echo "<h2 class=\"neucha\">$title</h2>\n";
 echo "<div id=\"page-container\" class=\"bodyline-main\" style=\"width:100%\">";
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
 echo "</div>\n";
 mysqli_free_result($result);
 echo "</div>\n";
 include('include/footer_bs.php');
 $done = 1;
}

if (($table == "exciting") and (!($done))) {
 $title = "Новые объекты";
 $sqlzagr = "";
 include('include/header_bs.php');
 if ($_zagr_mode) {
   $sqlzagr = " and t.area>=0 ";
 }
 echo "<div class=\"container-fluid\" style=\"width:100%\">";
 echo "<h2 class=\"neucha\">$title</h2>\n";
 echo "<div id=\"page-container\" class=\"bodyline-main\" style=\"width:100%\">";
 $sqlex = "SELECT e.id AS ex_id, t.id AS town_id, e.type AS TYPE,
           t.name AS town_name, t.hide AS town_hide, e.name AS ex_name
           FROM towns t, exciting e WHERE t.id = e.town $sqlzagr ORDER BY e.id DESC
           limit $_count_list";
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
 $ii = mysqli_num_rows($resultex);
 mysqli_free_result($resultex);
 echo "</div>\n</div>\n";

 include('include/footer_bs.php');
 $done = 1;
}

if (($table == "news") and (!($done))) {
 $title = "Архив новостей.";
 include('include/header_bs.php');

 echo "<div class=\"container-fluid\" style=\"width:100%\">";
 echo "<h1 class=\"neucha\">$title</h1>\n";
 echo "<div id=\"page-container\" style=\"width:100%\">";
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

 echo "</div>\n</div>\n";

 echo "\n";
 include('include/footer_bs.php');
 $done = 1;
}

if (($table == "trip") and (!($done))) {
 $title = "Поездки" . $headadd .fusername($start);
 include('include/header_bs.php');

 $sql = "SELECT o.id as id, o.datebeg as datebeg, o.txtobj as txtobj FROM ourtrip o, trip_user u WHERE u.iduser=$start AND o.id = u.idtrip $sqladd ORDER BY 2 DESC ";
 $result = $dbi->query($sql);

 echo "<div class=\"container-fluid\" style=\"width:100%\">";
 echo "<h1>$title</h1>\n";
 while ($myrow = mysqli_fetch_array($result)) {
  $s_id = $myrow["id"];
  $s_datebeg = $myrow["datebeg"];
  $s_txtobj = $myrow["txtobj"];
  echo "<p>".$s_datebeg."&nbsp;&nbsp;";
  echo "<a class=arc href=\"/ourtrip.php?it=$s_id\">$s_txtobj</a>";
  echo "</p>\n";
 };
 mysqli_free_result($result);
 echo "</div>\n";
 include('include/footer_bs.php');
 $done=1;
}

if (!($done)) {
 include "404.php";
}

?>
