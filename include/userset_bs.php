<?php
$_setting = "";
$_setting .= "<form name='_settings' method=post action='".$_SERVER["PHP_SELF"]."'>\n";
$_setting .= "<div class=\"col-md-12 text-center\"><h3>Настройки сайта</h3></div>\n";
  #----
$_setting .= my_echo_input(array('name'=>'exct','label'=>'Отзывы посетителей','type'=>'select','default'=>$_exct_mode,'options'=>array(0=>"Все",1=>"Только участников форума",-1=>"Никакие")));
$_setting .= my_echo_input(array('name'=>'excv','label'=>'Отзывов на странице','type'=>'select','default'=>$_excv_mode,'options'=>array(0=>"По 7/25",1=>"Показывать все")));
$_setting .= my_echo_input(array('name'=>'otkl','label'=>'Знаков в отзыве','type'=>'select','default'=>$_otkl_mode,'options'=>array(0=>"До 500 знаков",1=>"Полностью")));
//$_setting .= my_echo_input(array('name'=>'salp','label'=>'Сортировка объектов','type'=>'select','default'=>$_salp_mode,'options'=>array(0=>"По рейтингу",1=>"По алфавиту")));
$_setting .= my_echo_input(array('name'=>'desc','label'=>'Подробные описания','type'=>'select','default'=>$_desc_mode,'options'=>array(0=>"Полностью",1=>"Не показывать",2=>"До 1000 знаков")));
$_setting .= my_echo_input(array('name'=>'foto','label'=>'Фотографии','type'=>'select','default'=>$_foto_mode,'options'=>array(0=>"Только одобренные",1=>"Все")));
  #----
$_setting .= my_echo_input(array('name'=>'gmap','label'=>'Географические карты','type'=>'select','default'=>$_gmap_mode,'options'=>array(0=>"Google Maps",1=>"OpenStreetMap",2=>"Yandex Map",9=>"Не показывать")));
$_setting .= my_echo_input(array('name'=>'text','label'=>'Текстовое меню','type'=>'select','default'=>$_text_mode,'options'=>array(0=>"Графическое",1=>"Текстовое")));
  #----
$_setting .= my_echo_input(array('name'=>'zagr','label'=>'Заграницу','type'=>'select','default'=>$_zagr_mode,'options'=>array(0=>"Показывать",1=>"Не показывать")));
$_setting .= my_echo_input(array('name'=>'inws','label'=>'Новостей на главной','type'=>'text','default'=>$_inws_mode));
  #----
$_setting .= my_echo_input(array('name'=>'iexc','label'=>'Откликов на главной','type'=>'text','default'=>$_iexc_mode));
  #----
$_setting .= my_echo_input(array('name'=>'tblx','label'=>'В городах отображать объекты','type'=>'select','default'=>$_tblx_mode,'options'=>array(0=>"По умолчанию",1=>"Таблицей")));
$_setting .= "<div class=\"col-md-12 text-center\"><input class=\"button\" type=\"submit\" name=\"xsubmit\" value='Запомнить'></div>\n";
  #----
$_setting .="</form>\n";

?>
