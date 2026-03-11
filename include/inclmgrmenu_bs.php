<?php
$_menu = '';
if ($forum_user_id>0) {
  if ($forum_user_level == 1) {
    $all_rights = ",admin";
  }
  else {
    $all_rights = "";
  }
  $_menu .= "<div id=\"mgrmenu\" class=\"col-md-12\" width=100% align=center><h3>Здравствуйте, $forum_user_name\n";
  $_menu .= " <a class='travell5m' href='/index.php?tforumlogout=1'>[Выйти]</a><br><a class='travell5m' href='/user.php/$forum_user_id'>Посмотреть свои отклики на карте</a>\n</h3>";
  echo "<!--\n$forum_user_time\n".time()."\n-->";
  if ($forum_user_time < (time())) {
    $_class = "alert-danger";
  }
  else {
    $_class = "alert-info";
  }
  $_menu .= "<p class=\"alert $_class\">Авторегистрация заканчивается ". strftime("%d %B %Y %T", ($forum_user_time + 3600)) . "</p></div>\n";
  $sql_r = "select user_right from sitemgr where userid = $forum_user_id";
  $result_r = $dbi->query($sql_r);
  if($result_r) {
    while($myrow_r = mysqli_fetch_array($result_r)) {
      $user_right = $myrow_r['user_right'];
      $all_rights .= "," . $user_right . ",";
    } 
    mysqli_free_result($result_r);
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
  $_menu .= "<div class=\"col-md-12\"><ul class=\"nav navbar-nav\">";
  if (!($admt == '')) {
    $_menu .= "<li><a class='travell5m' href='/'>На главную</a></li>\n";
  }
  if (($forum_user_level>0) or ($forum_user_rank>0)) {
    $_menu .= "<li><a class='travell5m' href='/fotoman.php'>Ввод фотографий\n</a></li>\n";
    $fotoman_enable = 1;
    $errights = 1;
  }
  $_menu .= "<li><a class='travell5m' href='/sitemgr.php?admt=report'>Редактирование отчётов</a></li>\n";
  if (($forum_user_level>0) or ($forum_user_rank>0)) {
    $_menu .= "<li><a class='travell5m' href='/sitemgr.php?admt=text'>Редактирование описания\n</a></li>\n";
    $text_enable = 1;
    $errights = 1;
  }
  $_menu .= "<li><a class='travell5m' href='/ourtrip.php'>Совместные поездки</a></li>\n";
  $_menu .= "<li><a class='travell5m' href='/basket_v.php'>Свои  поездки (корзина)</a></li>\n";
  if ((preg_match("/\,news\,/i",$all_rights)) || (preg_match("/\,admin\,/i",$all_rights))) {
    $_menu .= "<li><a class='travell5m' href='/sitemgr.php?admt=news'>Ввод новостей\n</a></li>\n";
    $news_enable = 1;
    $errights = 1;
  }
  if ((preg_match("/\,exciting\,/i",$all_rights)) || (preg_match("/\,admin\,/i",$all_rights))) {
    $_menu .= "<li><a class='travell5m' href='/sitemgr.php?admt=exciting'>Ввод объекта\n</a></li>\n";
    $exciting_enable = 1;
    $fotoman_enable = 2;
    $errights = 1;
  }
  if ((preg_match("/\,admin\,/i",$all_rights))) {
    $_menu .= "<li><a class='travell5m' href='/sitemgr.php?admt=town'>Ввод города\n</a></li>\n";
    $towns_enable = 1;
    $errights = 1;
  }
  if ((preg_match("/\,exciting\,/i",$all_rights)) || (preg_match("/\,admin\,/i",$all_rights))) {
    $_menu .= "<li><a class='travell5m' href='/sitemgr.php?admt=excited'>Редактирование объекта\n</a></li>\n";
  }
  if ((preg_match("/\,admin\,/i",$all_rights))) {
    $_menu .= "<li><a class='travell5m' href='/sitemgr.php?admt=towned'>Редактирование города\n</a></li>\n";
  }
  if ((preg_match("/\,admin\,/i",$all_rights))) {
    $_menu .= "<li><a class='travell5m' href='/sitemgr.php?admt=trace'>Создание трассы</a></li>\n";
    $_menu .= "<li><a class='travell5m' href='/sitemgr.php?admt=tracetown'>Размещение городов по трассе</a></li>\n";
    $traces_enable = 1;
  }
  if ((preg_match("/\,moderator\,/i",$all_rights)) || (preg_match("/\,admin\,/i",$all_rights))) {
    $_menu .= "<li><a class='travell5m' href='/sitemgr.php?admt=otklik'>Модерирование\n</a></li>\n";
  }
  if ((preg_match("/\,moderator\,/i",$all_rights)) || (preg_match("/\,admin\,/i",$all_rights))) {
    $_menu .= "<li><a class='travell5m' href='/sitemgr.php?admt=forum'>Модерирование форума\n</a></li>\n";
  }
  if ($errights == 0) {
    //  echo "Увы. У Вас нет полномочий по редактированию/добавлению информации.";
  }
  $_menu .= "</ul></div>";
  // echo $sqlii;
}
?>
