<style>
    .site-intro {
  font-size: 15px;
  line-height: 1.6;
  padding: 50px;
  font-size: 16px;
}

.site-intro strong {
  font-family: var(--brand-font);
  font-size: 16px;
  color: #1a3b1a;
  font-weight: 600;
}

.site-intro a {
  color: #1a3b1a;
  text-decoration: none;
  border-bottom: 1px dotted #9ab89a;
}

.site-intro a:hover {
  color: #0f2b0f;
  border-bottom-color: #0f2b0f;
}

.site-intro em {
  color: #2f4f2f;
  font-style: italic;
}
</style>
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

// блок новостей и события - первая таблица

echo "<div class=\"bodyline-main\" itemprop=\"name\" content=\"Новости По России на авто. За границу на авто.\">\n"; //центральная часть 
   
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
  
//новые отзывы и отклики
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
  echo "<h2 class=\"neucha huge\"><a href=\"/archive.php/o\" rel=\"nofollow\">Новые отзывы >>></a></h2>";

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
   echo "</div>\n"; //новые отзывы

  echo "<div class=\"col-sm-6 col-xs-12 newobjects-objects\">\n"; //новые объекты
  echo "<h2 class=\"neucha huge\"><a href=\"/archive.php/e\" rel=\"nofollow\">Новые объекты >>></a></h2>";
  $sqlex = "SELECT t.id AS town_id, e.id AS ex_id, e.type AS TYPE, t.name AS town_name, t.hide AS town_hide, e.name AS ex_name, e.area as ex_area "
        . "FROM towns t, exciting e WHERE t.id = e.town and e.mainid>=0 $sqlzagr ORDER BY e.id DESC LIMIT $_iexc_mode ";
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
  //echo "<br><a class=\"tz tb\" href='/archive.php/e'>&gt;&gt;&gt;Архив объектов&gt;&gt;&gt;</a>";
  echo "</div>\n";  //новые объекты
  echo "</div></div>"; //верхняя часть с двумя столбцами 
  ?>

  <div class=\"row\">
  <div class="site-intro">
  <strong>По России на авто</strong> — старейший проект для тех, кто хочет увидеть мир своими глазами. 
  Здесь всё для самостоятельных поездок (уже не только на авто) и живого общения: 
  <a href="/towns.php">города</a>, 
  <a href="/cube.php">достопримечательности</a>, 
  <a href="rd_report.php">отчёты</a> и идеи от других путешественников. 
  <em>А если ты боишься начать, <a href="/ourtrip.php">присоединяйся к нашим совместным поездкам!</a></em>
</div></div> 
  <?php 
  echo "<div class=\"row\"><div class=\"col-md-12 col-xs-12\">\n"; // <!-- если надо два раздела -->
  echo "<div class=\"col-md-6 col-xs-12 newobjects-reports\">\n"; //новые отчеты
  echo "<h2 class=\"neucha huge\">Новые отчеты >>></h2>";
  $sql_wd = "select rid, key_towns, time from l_reports order by time desc limit 10";
  $result_wd = $dbi->query($sql_wd);
  while ($myrow_wd = mysqli_fetch_array($result_wd)) {
    $s_key = $myrow_wd["key_towns"];
    $rid   = $myrow_wd["rid"];
    echo "<a href='/v_report.php/$rid'>$s_key</a><br>\n";
  }
  mysqli_free_result($result_wd);
 echo "</div>\n";
  

echo "<div class=\"col-md-6 col-xs-12 newobjects-traces\">\n";
echo "<h2 class=\"neucha huge\">Новости о состоянии дорог >>></h2>";
echo "Недавно проехали по нашим дорогам и есть что рассказать?<br>";
echo "Поделитесь с другими автопутешественниками, перейдите на страницу <a href=\"/trace.php\">Дороги России</a>.<br>";

/* 1. Основные записи */
$sql_main = "
    SELECT 
        ts.ido AS tsid,
        ts.kbeg,
        ts.kend,
        ts.time,
        tr.id AS trace,
        tr.name AS tr_name,
        tr.of_name AS of_name
    FROM otklikt ts
    LEFT JOIN trace tr ON tr.id = ts.idtr
    WHERE ts.ido >= 0
";

/* 2. Личные черновики пользователя */
$sql_user = "
    SELECT 
        ts2.ido AS tsid,
        ts2.kbeg,
        ts2.kend,
        ts2.time,
        tr2.id AS trace,
        tr2.name AS tr_name,
        tr2.of_name AS of_name
    FROM otklikt ts2
    LEFT JOIN trace tr2 ON tr2.id = ts2.idtr
    WHERE ts2.ido < 0 AND ts2.cook = '".$dbi->real_escape_string($ctext)."'
";

/* 3. Итоговая выборка */
if (!empty($ctext)) {
    $sql_tr = "($sql_main) UNION ($sql_user) ORDER BY time DESC LIMIT 10";
} else {
    $sql_tr = "$sql_main ORDER BY time DESC LIMIT 10";
}

/* 4. Выполнение */
$result_tr = $dbi->query($sql_tr);

/* 5. Вывод */
if ($result_tr) {
    while ($row = $result_tr->fetch_assoc()) {

        $tsid  = (int)$row['tsid'];
        $trace = (int)$row['trace'];
        $kbeg  = (int)$row['kbeg'];
        $kend  = (int)$row['kend'];

        $name = trim(
            ($row['of_name'] ? $row['of_name'].' ' : '') .
            $row['tr_name']
        );

        /* ЧПУ-ссылка: trace.php/ID#ts-IDO */
        echo "<a itemprop=\"significantLink\" 
                 href=\"/trace.php/{$trace}#ts-{$tsid}\">
                 $name (от $kbeg км до $kend км)
              </a><br>\n";
    }

    $result_tr->free();
}

echo "</div>\n";
echo "</div></div>\n";


// города
if (!($_zagr_mode)) {
  $sqltt = "select t.name as town, t.url as turl, t.hide as hide, t.rate as rate, t.id as tid, a.name as area from towns t, areas a where t.rate > ($thighrate) and a.id=t.area order by t.name";
}
else {
  $sqltt = "select t.name as town, t.url as turl, t.hide as hide, t.rate as rate, t.id as tid, a.name as area from towns t, areas a where a.id > 0 and t.rate > ($thighrate) and a.id=t.area order by t.name";    
}
$resultt = $dbi->query($sqltt);
$numrowst = mysqli_num_rows($resultt);
//echo " <div class=\"row\">\n  <div class=\"col-xs-12 bodyline-main\">\n";
$townstext = "<p>Здесь ";
$townstext .= echo_int($numrowst, array("перечислены","перечислен","перечислены"))." <strong>$numrowst ";
$townstext .= echo_int($numrowst,array("наиболее популярных среди путешественников городов и населенных пунктов","наиболее популярный среди путешественников город и населенный пункт","наиболее популярных среди путешественников города и населенных пункта"));
if (!($_zagr_mode)) {
  $townstext .= " России и зарубежья";
}
else {
  $townstext .= " России";
}
$townstext .= "</strong></p>";
$townstext .= "\n\n<div class=column>\n".gettownsresultex($resultt,1)."\n</div>\n\n";

echo show_panel("Популярные туристические направления >>>","h2 class=\"neucha\"",$townstext,1,"townsdirections");

echo "</div><!-- /bodyline-main -->\n";// закрыли средний столбец

echo "</div>\n";
if (!($_text_mode)) {
  echo "<div class=\"col-sm-3 col-xs-12\" style=\"border-left: solid 1px #888;\">\n";
  include("include/reklama_bs.php");
  echo "\n</div><!-- /row main -->\n\n";
}
echo "</div></div>\n";
$_totop = 1;
include("include/footer_bs.php");
