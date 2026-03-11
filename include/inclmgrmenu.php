<?php
if ($forum_user_id>0) {
 if ($forum_user_level == 1) {
  $all_rights = ",admin";
 }
 else {
  $all_rights = "";
 }
 require_once "include/func.php";
 $jqueryon = 1;
 $datepicker = 1;
 include("include/head01.php");
 echo "<div id=\"mgrmenu\" width=100% align=center><h3>Здравствуйте, $forum_user_name\n";
 echo " <a class=\"tb tz\" href=/index.php?tforumlogout=1>[Выйти]</a><br><a class=\"tb tz\" href=/user.php/$forum_user_id>Посмотреть свои отклики на карте</a>\n</h3>";
 echo "<p>Авторегистрация заканчивается ". strftime("%d %B %Y %T", ($forum_user_time + 3600)) . "</p>\n";
 $sql_r = "select user_right from sitemgr where userid = $forum_user_id";
 $result_r = $dbi->query($sql_r);
 if($result_r) {
  while($myrow_r = mysqli_fetch_array($result_r)) {
   $user_right = $myrow_r['user_right'];
   $all_rights = $all_rights . "," . $user_right . ",";
  } 
  mysqli_free_result($result_r);
 }
 if (($script_name != 'fotoman.php')&&($script_name != 'basket_v.php')) {
  include("include/userset.php");
 }

//Теперь в all_rights хранится массив слов, обозначающих полномочия пользователя.
 $errights = 0;
 $fotoman_enable = 0;
 $news_enable = 0;
 $exciting_enable = 0;
 $text_enable = 0;
 $towns_enable = 0;
 $traces_enable = 0;

//Надо показать менюху выбора.
 echo "<table cellpadding=5 border=0 borderwidth=0><tbody class=travell5><tr>";
 echo "<td align=center><a class=\"tb tz\" href='/'>На главную</a></td>\n";
// echo "<td align=center><a class=travell5m href='/v_foto.php'>Просмотр результатов фотоконкурса 2008</a></td>\n";
 if (($forum_user_level>0) or ($forum_user_rank>0)) {
  echo "<td align=center><a class=\"tb tz\" href='/fotoman.php'>Ввод фотографий\n</a></td>\n";
  $fotoman_enable = 1;
  $errights = 1;
 }
 echo "<td align=center><a class=\"tb tz\" href='/sitemgr.php?admt=report'>Редактирование отчётов</a></td>\n";
 if (($forum_user_level>0) or ($forum_user_rank>0)) {
  echo "<td align=center><a class=\"tb tz\" href='/sitemgr.php?admt=text'>Редактирование описания\n</a></td>\n";
  $text_enable = 1;
  $errights = 1;
 }
 echo "<td align=center><a class=\"tb tz\" href='/ourtrip.php'>Совместные поездки</a></td>\n";
 echo "<td align=center><a class=\"tb tz\" href='/basket_v.php'>Свои поездки</a></td>\n";
 echo "</tr></tbody></table><table cellpadding=5 border=0 borderwidth=0><tbody class=travell5><tr>";
 if ((preg_match("/\,news\,/i",$all_rights)) || (preg_match("/\,admin\,/i",$all_rights))) {
  echo "<td align=center><a class=\"tb tz\" href='/sitemgr.php?admt=news'>Ввод новостей\n</a></td>\n";
  $news_enable = 1;
  $errights = 1;
 }
 if ((preg_match("/\,exciting\,/i",$all_rights)) || (preg_match("/\,admin\,/i",$all_rights))) {
  echo "<td align=center><a class=\"tb tz\" href='/sitemgr.php?admt=exciting'>Ввод объекта\n</a></td>\n";
  $exciting_enable = 1;
  $fotoman_enable = 2;
  $errights = 1;
 }
 if ((preg_match("/\,admin\,/i",$all_rights))) {
  echo "<td align=center><a class=\"tb tz\" href='/sitemgr.php?admt=town'>Ввод города\n</a></td>\n";
  $towns_enable = 1;
  $errights = 1;
 }
 if ((preg_match("/\,exciting\,/i",$all_rights)) || (preg_match("/\,admin\,/i",$all_rights))) {
  echo "<td align=center><a class=\"tb tz\" href='/sitemgr.php?admt=excited'>Редактирование объекта\n</a></td>\n";
 }
 if ((preg_match("/\,admin\,/i",$all_rights))) {
  echo "<td align=center><a class=\"tb tz\" href='/sitemgr.php?admt=towned'>Редактирование города\n</a></td>\n";
 }
 if ((preg_match("/\,admin\,/i",$all_rights))) {
  echo "<td align=center><a class=\"tb tz\" href='/sitemgr.php?admt=trace'>Создание трассы</a></td>\n";
   echo "<td align=center><a class=\"tb tz\" href='/sitemgr.php?admt=tracetown'>Размещение городов по трассе</a></td>\n";
 
  $traces_enable = 1;
 }
 if ((preg_match("/\,moderator\,/i",$all_rights)) || (preg_match("/\,admin\,/i",$all_rights))) {
  echo "<td align=center><a class=\"tb tz\" href='/sitemgr.php?admt=otklik'>Модерирование\n</a></td>\n";
 }
 if ((preg_match("/\,moderator\,/i",$all_rights)) || (preg_match("/\,admin\,/i",$all_rights))) {
  echo "<td align=center><a class=\"tb tz\" href='/sitemgr.php?admt=forum'>Модерирование форума\n</a></td>\n";
 }
 if ($errights == 0) {
//  echo "Увы. У Вас нет полномочий по редактированию/добавлению информации.";
 }
 echo "</tr></tbody></table></div>";
// echo $sqlii;
}
