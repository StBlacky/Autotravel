<?php
// показ 5 последних откликов в всплывающем окне/сдвигающейся рамке
include_once('include/func.php');
include_once('include/func_bs.php');
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

$otkl_rep = bs_getotklik($ex);

if (!($_exct_mode == 0)) {
  echo "<p class=\"t13 tc\">";
  if ($_exct_mode > 0) {
    echo "Не забудьте: у Вас включен фильтр отзывов [только участников форума]";
  }
  if ($_exct_mode < 0) {
    echo "Не забудьте: у Вас отключено отображение отзывов ";
  }
  echo ". Отключается в меню <a class=travell5r href=/sitemgr.php>настроек сайта</a></p>";
}

if ($otkl_rep) {
  $ii = 0;
  $otkl_total .= '<div class="o_list well">';
  foreach ($otkl_rep as $otkl) {
    if ($ii < 7) {
      $otkl_total .= '<div class="o_block"><div class="row">';
      $otkl_total .= '<div class="col-xs-6 o_mark">'.$otkl["nick"]." ".s_stars($otkl["mark"])."</div>";
      $otkl_total .= '<div class="col-xs-6"><span class="o_date pull-right">&nbsp;('.$otkl["date"].")</span>\n";
      //$otkl_total .= '<span class="o_nick pull-right">'.$otkl["nick"]."</span>";
      $otkl_total .= "</div>\n";
      $otkl_total .= "</div>\n";
      $otkl_total .= '<div class="o_opinion">'.$otkl["comment"]."</div>\n";
      $otkl_total .= '</div>';
      $ii++;
    }
  }
  $otkl_total .= '</div>';
 echo $otkl_total;
}
else {
 echo "<p class=\"t13 tc\">Пока нет ни одного отзыва. Значит... пора посетить это место и поделиться своими впечатлениями!</p>";
}
//echo "<a class=\"btn btn-default\" target=_blank class=travell5n href=\"/otklik.php/$ex\">>>посмотреть полную информацию, оставить отклик>></a>";
?>
</body>

