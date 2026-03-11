<?php
if (!($admt)) {
  echo "<form name='_settings' method=post action='".$_SERVER["PHP_SELF"]."'>\n";
  echo "<table border=0 width=90%><tbody class=travell5>";
  echo "<tr><td colspan=2 align=center>Настройки сайта</td></tr>\n";
  echo "<tr><td align=right width=50%>Отзывы посетителей\n</td>";
  echo "<td align=left><select size=1 name=exct>\n";
  echo show_int_options($_exct_mode, array(0=>"Все",1=>"Только участников форума",-1=>"Никакие"));
  echo "</select></td>\n</tr>\n";
  echo "<tr><td align=right width=50%>Отзывов на странице\n</td>";
  echo "<td align=left><select size=1 name=excv>\n";
  echo show_int_options($_excv_mode, array(0=>"По 7/25",1=>"Показывать все"));
  echo "</select></td>\n</tr>\n";
  echo "<tr><td align=right width=50%>Знаков в отзыве\n</td>";
  echo "<td align=left><select size=1 name=otkl>\n";
  echo show_int_options($_otkl_mode, array(0=>"До 500 знаков",1=>"Полностью"));
  echo "</select></td>\n</tr>\n";
  echo "<tr><td align=right width=50%>Сортировка объектов\n</td>";
  echo "<td align=left><select size=1 name=salp>\n";
  echo show_int_options($_salp_mode, array(0=>"По рейтингу",1=>"По алфавиту"));
  echo "</select></td>\n</tr>\n";
  echo "<tr><td align=right width=50%>Подробные описания\n</td>";
  echo "<td align=left><select size=1 name=desc>\n";
  echo show_int_options($_desc_mode, array(0=>"Полностью",1=>"Не показывать",2=>"До 1000 знаков"));
  echo "</select></td>\n</tr>\n";
  echo "<tr><td align=right width=50%>Фотографии\n</td>";
  echo "<td align=left><select size=1 name=foto>\n";
  echo show_int_options($_foto_mode, array(0=>"Только одобренные",1=>"Все"));
  echo "</select></td>\n</tr>\n";
  #-------------
  echo "<tr><td align=right width=50%>Географические карты\n</td>";
  echo "<td align=left><select size=1 name=gmap>\n";
  echo show_int_options($_gmap_mode, array(0=>"Google Maps",1=>"OpenStreetMap",2=>"Yandex Map",9=>"Не показывать"));
  echo "</select>";
  echo "</td>\n</tr>\n";
  #-------------
  //echo "<tr><td align=right width=50%>Старое меню\n</td>";
  //echo "<td align=left><select size=1 name=text>\n";
  //echo show_int_options($_text_mode, array(0=>"Графическое",1=>"Текстовое"));
  //echo "</select></td>\n</tr>\n";
  echo "<tr><td align=right width=50%>Заграницу\n</td>";
  echo "<td align=left><select size=1 name=zagr>\n";
  echo show_int_options($_zagr_mode, array(0=>"Показывать",1=>"Не показывать"));
  echo "</select></td>\n</tr>\n";
  #------
  echo "<tr><td align=right width=50%>Новостей на главной\n</td>";
  echo "<td align=left><input type=text name=inws value='";
  echo $_inws_mode;
  echo "'>\n";
  echo "</td>\n</tr>\n";
  #----
  echo "<tr><td align=right width=50%>Откликов на главной\n</td>";
  echo "<td align=left><input type=text name=iexc value='";
  echo $_iexc_mode;
  echo "'>\n";
  echo "</td>\n</tr>\n";
  #----
  echo "<tr><td colspan=2 align=center><input type=submit name=xsubmit value='Запомнить'></td></tr></table></form>\n";
}
?>
