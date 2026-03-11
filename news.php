<?php


include_once('include/config.php');
require_once "include/func.php";
require_once "include/func_bs.php";
$id = $_REQUEST["id"];
$id = (int) mysafestring($id);
$ex = $_REQUEST["ex"];
$ex = (int) mysafestring($ex);
$tn = $_REQUEST["tn"];
$tn = (int) mysafestring($tn);
$tr = $_REQUEST["tr"];
$tr = (int) mysafestring($tr);
$ar = $_REQUEST["ar"];
$ar = (int) mysafestring($ar);

$title="Автопутешествия по России: новости";
$metadesc="Новости в области туризма и путешествий";
$metatags="Автопутешествия Новости";
if (($id) or ($ex) or ($tn) or ($tr) or ($ar)) {
 $_noindex = 1;
}
$bootstrap = 1;
include("include/header_bs.php");

echo "<div class=\"container-fluid\" style=\"width:100%;\"><div class=\"row\">\n<div class=\"col-md-9 col-xs-12\">\n";

$sqlzagr = "";
if ($_zagr_mode) {
  $sqlzagr = " and e.area >=0 ";
}

//echo "\n$sqlii\n";


if (($id) or ($ex) or ($tn) or ($tr) or ($ar)) {
 if ($id > 0) {
  $sql = "select * from news where id=$id";
  $oid = $id;
 }
 if ($ex > 0) {
  $sql = "select * from news where exciting='e$ex' order by date desc ";
  $ex_name=getexname($ex);
  echo "<h3><a href='/otklik.php/$ex'>$ex_name:</A></h3>\n";
 }
 if ($tn > 0) {
  $sql = "select * from news where exciting='t$tn' order by date desc ";
  $tt=gettownnamearr($tn);
  $ex_name=$tt[0] . " (".$tt[2].")";
  if ($tt[10]) {
    echo "<h3><a href='/gorod/".$tt[10].".html'>$ex_name:</A></h3>\n";
  }
  else {
    echo "<h3><a href='/town.php/$tn'>$ex_name:</A></h3>\n";
  }
 }
 if ($tr > 0) {
  $sql = "select * from news where exciting='r$tr' order by date desc ";
  $ex_name=getareaname($ar);
  echo "<h3><a href='/area.php/$ar'>$ex_name:</A></h3>\n";
 }
 if ($ar > 0) {
  $sql = "select * from news where exciting='a$ar' order by date desc ";
  $ex_name=getareaname($ar);
  echo "<h3><a href='/area.php/$ar'>$ex_name:</A></h3>\n";
 }
 $result = $dbi->query($sql);

 while ($myrow = mysqli_fetch_array($result)) {
    $id = $myrow["id"];
    $date = $myrow["date"];
    $title = $myrow["title"];
    $spring = $myrow["spring"];
    $url = $myrow["url"];
    $exciting = $myrow["exciting"];
    $text = $myrow["text"];
    if ($oid > 0) {
     if ($exciting) {
         echo "<h3>". news_excite($exciting) . "</h3>\n";
     }
    }
    draw_news($date,$title,$spring,$url,$text);
    if ($_user_is_admin == '1') {
            $descr_local = "<div align=center><form name=editex action=\"/sitemgr.php\">\n";
            $descr_local .= "<input type=hidden name=admt value=\"news\">";
            $descr_local .= "<input type=hidden name=newsid value=\"$id\">";
            $descr_local .= "<input type=hidden name=submitexn value=\"1\">";
            $descr_local .= "<input class=\"ttb2 tzh t14 ttr tp10\" type=submit value=\"Отредактировать новость\"></form></div>\n";
    
            echo $descr_local;
    }
 }
 if ($ex > 0) {
  echo "<p class=travell0><br><br><br><a href='/otklik.php/$ex'>Назад к описанию объекта</p>\n";
 }
 if ($tn > 0) {
/* если город - надо вывести все новости по всем примечательностям */
   $sql = "select n.date as date, n.title as title, n.spring as spring, n.url as url, n.text as txt, e.id as exc from news n, exciting e where n.exciting=concat('e',e.id) and e.town='$tn' order by n.date desc";
   $result = $dbi->query($sql);
   while ($myrow = mysqli_fetch_array($result)) {
    $date = $myrow["date"];
    $title = $myrow["title"];
    $spring = $myrow["spring"];
    $url = $myrow["url"];
    $exc = $myrow["exc"];
    $text = $myrow["txt"];
    $ex_name=getexname($exc);
    echo "<h3><a href='/otklik.php/$exc'>$ex_name:</A></h3>\n";
    draw_news($date,$title,$spring,$url,$text);
   }
   echo "<p class=travell0><br><br><br><a href='/town.php/$tn'>Назад к описанию города</p>\n";
 }
 if ($tr > 0) {
  echo "<p class=travell0><br><br><br><a href='/trace.php?town=$tn'>Назад к описанию трассы</p>\n";
 }
 if ($ar > 0) {
/* если область - надо вывести все новости по всем городам */
   $tlist = "0";
   $sql = "select n.date as date, n.title as title, n.spring as spring, n.url as url, n.text as txt, t.id as exc from news n, towns t where n.exciting=concat('t',t.id) and t.area='$ar' order by n.date desc";
   $result = $dbi->query($sql);
   while ($myrow = mysqli_fetch_array($result)) {
    $date = $myrow["date"];
    $title = $myrow["title"];
    $spring = $myrow["spring"];
    $url = $myrow["url"];
    $exc = $myrow["exc"];
    $tlist = $tlist.",".$exc;
    $text = $myrow["txt"];
    $ex_name=gettownname($exc);
    echo "<h1><a href='/town.php/$exc'>$ex_name:</A></h1>\n";
    draw_news($date,$title,$spring,$url,$text);
   }
/* и по примечательностям тоже, блин */
   $sql = "select n.date as date, n.title as title, n.spring as spring, n.url as url, n.text as txt, e.id as exc from news n, exciting e where n.exciting=concat('e',e.id) and e.area='$ar' order by n.date desc";
   $result = $dbi->query($sql);
   while ($myrow = mysqli_fetch_array($result)) {
    $date = $myrow["date"];
    $title = $myrow["title"];
    $spring = $myrow["spring"];
    $url = $myrow["url"];
    $exc = $myrow["exc"];
    $text = $myrow["txt"];
    $ex_name=getexname($exc);
    echo "<h1><a href='/otklik.php/$exc'>$ex_name:</A></h1>\n";
    draw_news($date,$title,$spring,$url,$text);
   }
  echo "<p class=travell0><br><br><br><a href='/area.php/$ar'>Назад к описанию области</p>\n";
 }

 if (!(($ex > 0) or ($tn > 0) or ($tr > 0) or ($ar > 0))) {
  echo "<p class=travell0><br><br><br><a href=".$_SERVER["PHP_SELF"].">Назад к списку новостей</p>\n";
 }
}
else {
 $sql = "select id, date, title, spring from news where date>(CURDATE() - INTERVAL 60 DAY) order by date desc ";
 $result = $dbi->query($sql);
 echo "<div class=\"bodyline-main travell0\" itemprop=\"name\" content=\"Новости По России на авто. За границу на авто.\">\n"; //центральная часть 
 //echo "<h1>Архив федеральных новостей туризма</h1>";

 if($myrow = mysqli_fetch_array($result)) {
  do {
     $id = $myrow["id"];
     $date = $myrow["date"];
     $datex = strftime("%d.%m.%Y", strtotime($date));
     $title = $myrow["title"];
     $spring = $myrow["spring"];
     echo "<p><b>$datex</b>\n";
     echo "<a href='/news.php?id=$id'>$title</a></p>";
  } while ($myrow = mysqli_fetch_array($result));
 }

 echo "</div>\n";
}

echo "</div></div></div>\n";

include("include/footer_bs.php");
?>

