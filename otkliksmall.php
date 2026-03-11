<?php
// forumsess надо звать в самом начале. А то переменные переопределятся.
// Но внутри ничего не echo-ить, а то заголовки и куки покривеют.
include_once('include/func.php');
include_once('include/head00.php');

// теперь всё как обычно

if (preg_match("/^\/(\d+)$/",$_pin,$exa)) {
 $ex = $exa[1];
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf8">
<meta http-equiv="Content-language" content="ru">
</head>
<body>
<?php
/*
if (($forum_user_id>1)) {
 echo "<div id=basketadd$ex  class=travell0><a class=travell5m onclick='BasketAdd($ex,$forum_user_basket,\"basketadd$ex\")'>Добавить в $forum_user_basket_name</a></div>\n";
}
if ($photocount) {
 if (!($s_rek)) {
  echo "<div align=center>$photoimg</div><br>";
 }
}
*/

if (($_excc_mode) or ($_exct_mode)) {
  echo "<p class=\"t13 tc\">Не забудьте: у Вас включен фильтр отзывов";
  if ($_excc_mode > 0) {
   echo " [только $_excc_mode отзывов]";
  }
  if ($_excc_mode < 0) {
   echo " [отключены отзывы]";
  }
  else {
   if ($_exct_mode) {
    echo " [только участников форума]";
   }
  }
  echo ". Отключается в меню <a href=/sitemgr.php>настроек сайта</a></p>";
}
if (empty($_excc_mode)) {
  $_excc_mode=5;  
}
$otkl_rep = getotklik($ex,"","");
if ($otkl_rep) {
 echo "<p class=\"t13 tc\">&nbsp;</p>";
 echo $otkl_rep;
}
else {
 echo "<p class=\"t13 tc\">Пока нет ни одного отзыва. Значит... пора посетить это место и поделиться своими впечатлениями!</p>";
}
echo "<a target=_blank class=travell5n href=\"/otklik.php/$ex\">>>посмотреть полную информацию, оставить отклик>></a>";
?>
</body>

