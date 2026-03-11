<?php

$admt = $_REQUEST["admt"];
$_text = "";
include_once('include/config.php');
include_once('include/func.php');
include_once('include/func_bs.php');
include_once('include/head00.php');
$_short_menu = 1;
if ((isset($_REQUEST['return'])) && ($forum_user_id)) {
  error_log("Returning ".$forum_user_id." -> ".$_REQUEST['return']);
  Header( "HTTP/1.1 301 Moved Permanently" );
  Header( "Location: ".urldecode($_REQUEST['return']) );
  exit;
}

$bootstrap = 1;
$noads = 1;
$return = '';
if ($admt == '') {
  // полное меню слева
  include_once("include/header_bs.php");
  if (isset($_REQUEST['return'])) {
    $return = $_REQUEST['return'];
  }
  else {
    $return = urlencode($_SERVER['HTTP_REFERER']);
  }
}
else {
  // нет полного меню.
  include 'include/head01.php';
  echo "<div id=\"maincontainer\"> "; // контейнер всего сайта
}
//
include_once("include/userset_bs.php");
include_once('include/inclmgrmenu_bs.php');
//
echo "<div class=\"container-fluid\" style=\"width:100%\">";
// меню в два столбца - для незарегенных
if ($forum_user_id == 0) {
  if (!(isset($tforumtime))) {
    $tforumtime = 365;
  }
  echo "<div class=\"row\"><div class=\"col-md-4\"><div class=\"row\">\n";
  if ($_regerror) {
    echo "<p class=\"alert alert-danger\">". $_regerror . "</p>\n";

  }
  ?>
  <div class="col-md-12 text-center"><h3>Вход на сайт</h3></div>
  <div class="col-md-12">
  <p class="tj">Пользователи, зарегистрированные на <a class="tb" href="//forum.aurotravel.ru/">форуме</a>, могут авторизоваться для работы с сайтом.</p>
  <p class="tj">Авторизованным пользователям сайт не показывает никакой иной информации, кроме общедоступной. Настройки сайта (справа) также не связаны с авторизацией. 
    Авторизация не влияет на выдачу рекламных объявлений; равно не используется внутрисайтовый трекинг.
    Авторизация просто помогает посетителям управлять своими (размещенными на сайте) откликами, отчетами и поездками.</p>
  </div>
  <form name="_forum_enter" method="post" action='<?php echo $_SERVER["PHP_SELF"]; ?>'>
  <?php
  if ($return) {
    echo "<input type=\"hidden\" name=\"return\" value=\"".$return."\">\n";
  }
  echo my_echo_input(array('name'=>'tforumname','label'=>'Имя:','type'=>'text','default'=>$tforumname,'placeholder'=>'Имя, которое Вы себе выбрали на форуме'));
  echo my_echo_input(array('name'=>'tforumpassword','label'=>'Пароль:','type'=>'password','default'=>$tforumpassword,'placeholder'=>'*********'));
  echo my_echo_input(array('name'=>'tforumtime','label'=>'Запомнить меня:','type'=>'select','default'=>$tforumtime,'options'=>array(0=>"На час",1=>"На день",7=>"На неделю",30=>"На месяц",365=>"На год",3650=>"Навсегда")));
  echo "<div class=\"col-md-12 text-center\"><input class=\"button\" type=\"submit\" value='Войти'></div></form>\n";
  echo "<div class=\"col-md-12\"><hr style=\"border-top:1px solid #00552B; width:100%;\">";
  echo "<pclass=\"tj\">Если Вы зарегистрированы на форуме с почтовым ящиком Яндекс-почты или почты Мейл.Ру, можете</p><a class=\"button\" href=\"/oauth.php?location=ya&return=".$return."\">Войти через Яндекс</a> <a class=\"button\" href=\"/oauth.php?location=mr&return=".$return."\">Войти через Mail.Ru</a>";
  echo "</div></div>\n";
  echo "</div><div class=\"col-md-8\"><div class=\"row\">\n";
  echo $_setting;
  echo "</div></div>\n</div>\n";
  $customscripttop .= '
    document.addEventListener( \'keydown\', function( event ) {
      var caps = event.getModifierState && event.getModifierState( \'CapsLock\' );
      console.log( \'caps!\' );
    });  
  ';
}
else {
  if ($admt == '') {
    echo "<div class=\"row\"><div class=\"col-md-4\"><div class=\"row\">\n";
    echo $_menu;
    echo "</div></div><div class=\"col-md-8\"><div class=\"row\">\n";
    echo $_setting;
    echo "</div>\n";
  }
  else {
    echo "<div class=\"row\"><div class=\"col-md-12\">";
    echo $_menu;
  }  
  echo "</div></div>";
  //////////////////////////////////////////////////////////////////////////////
  // редактирование отчётов. Просто показ списка - чего можно отредактировать.
  if (($admt == 'report' )) {
    echo "<p class=\"tmt20\"><a class=\"button\" href=/wr_report.php>Написать новый отчёт</a></p>\n";
    $sql="select rid,year,month,key_towns,spring from m_reports where cook='$ctext' and rid>0 order by time desc";
    $result = $dbi->query($sql);
    if ($result) {
      if (mysqli_num_rows($result)) {
        echo "<h3>Редактировать черновик:</h3>\n";
        while ($myrow = mysqli_fetch_array($result)) {
          $rid   = $myrow["rid"];
          $year  = $myrow["year"];
          $month = $myrow["month"];
          $key   = getskeytext($myrow["key_towns"],$rid);
          $keys  = $myrow["spring"];
          echo "<p class=\"tmt20\">$year-$month: <a href=/wr_report.php?rid=$rid&submitback=edit&submitraw=edit>($keys) $key</a></p>\n";
        }
      }  
      mysqli_free_result($result);
    }
    $sql="select rid,year,month,key_towns,spring from l_reports where cook='$ctext' and rid>0 order by time desc";
    $result = $dbi->query($sql);
    if ($result) {
      if (mysqli_num_rows($result)) {
        echo "<h3>Редактировать отчет на сайте:</h3>\n";
        while ($myrow = mysqli_fetch_array($result)) {
          $rid   = $myrow["rid"];
          $year  = $myrow["year"];
          $month = $myrow["month"];
          $key   = getskeytext($myrow["key_towns"],$rid);
          $keys  = $myrow["spring"];
          echo "<p class=\"tmt20\">$year-$month: <a href=/wr_report.php?rid=$rid&submitback=edit>($keys) $key</a></p>\n";
        }
      }
      mysqli_free_result($result);
    }
  }
  include("include/sitemgr.news.php");
  include("include/sitemgr.obj.php");
  include("include/sitemgr.trip.php");
  include("include/sitemgr.text.php");
  include("include/sitemgr.otklikm.php");
  include("include/sitemgr.del.php");
  include("include/sitemgr.forum.php");
  include("include/sitemgr.town.php");
  include("include/sitemgr.trace.php");
  include("include/sitemgr.loadfile.php");
   
}
//
$_short_footer = 1;
echo "</div>\n<!-- /container-fluid -->\n";
include_once('include/footer_bs.php')
?>

