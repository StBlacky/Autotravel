<?php
$noads = 1;

echo "<div id=\"maincontainer\"> "; // контейнер всего сайта
?>
<!-- шапка страницы -->
  <!-- верхнее меню-->
  <nav class="navbar nav" role="navigation" style="border-bottom:solid 1px #bbb">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-top-navbar-collapse" aria-expanded="false">
        <span class="sr-only neucha large">Меню</span>
        <span class="glyphicon glyphicon-menu-down"></span>
      </button>
      <a class="navbar-brand travell5r atborder neucha large" href="/">По России на авто</a>
    </div>

    <!--Само меню-->
    <div class="collapse navbar-collapse navbar-right" id="bs-top-navbar-collapse">  
      <?php if ($forum_user_basket) { ?>
      <p class="navbar-text">
        <a id="basket" href="/basket_v.php/<?php echo $forum_user_basket; ?>" rel="nofollow">Поездка <?php echo $forum_user_basket_name; ?>:
        <?php  if ($forum_user_basket_count>0) {
                 echo "$forum_user_basket_count объектов";
               } 
               else {
                 echo "пустая"; 
               } ?> 
        </a>
      </p>
      <?php } ?>
      <ul class="nav navbar-nav" >
        <li><a class="travell5r atborder neucha large" href="<?php echo $searchurl; ?>extype[]=1&amp;extype[]=6" rel="nofollow">Поиск</a></li>
        <li class="dropdown"><a class="dropdown-toggle travell5r atborder neucha large" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Меню сайта<span class="caret"></span></a>
        <ul class="dropdown-menu">
          <li><A class="travell5m neucha large" href="https:<?php echo $sitename;?>/sitemgr.php" rel="nofollow">
          <?php
          if ($forum_user_id > 0) {
   	     echo "Для форумчан</A></li>";
          } 
          else {
             echo "Настройки сайта</A></li>";
          }     
          ?>
          <li><A class="travell5m neucha large" target=_blank href="<?php echo $forumname; ?>/index.php">Форум</A></li>
          <li><A class="travell5m neucha large" href='<?php echo $calname;?>'>Календарь поездок</A></li>
          <li><A class="travell5m neucha large" href="/rd_report.php">Отчёты о поездках</a></li>
          <li><A class="travell5m neucha large" href="/wr_report.php">Написать отчет</a></li>
          <li><A class="travell5mr neucha large" href="/faq.php">ЧаВо</a></li>
          <?php        
          if (($forum_user_level>0) or ($forum_user_rank>0)) {
            echo "<li><A class=\"travell5mr neucha large\" href=\"/$_fotoalbum/author/".$forum_user_id."/0\">";
            echo "Фотографии";
            echo "</a></li>";
          }
          ?>
        </ul></li>
        <li class="dropdown"><a class="dropdown-toggle travell5r atborder neucha large" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Навигация<span class="caret"></span></a>
        <ul class="dropdown-menu">
          <li><a class="travell5m neucha large" href="/towns.php">Города</a></li>
          <li><a class="travell5m neucha large" href="/cube.php">Достопримечательности</a></li>
          <li><A class="travell5m neucha large"  href="/marshruty.php">Маршруты</a></li>
          <li><a class="travell5m neucha large" href="/areas.php">Области России</a></li>
          <li><a class="travell5m neucha large" href="/countries.php">Зарубежные страны</a></li>
          <li><a class="travell5m neucha large" href="<?php echo $searchurl; ?>extype=2" rel="nofollow">Где остановиться</a></li>
          <li><a class="travell5m neucha large" href="<?php echo $searchurl; ?>extype=3" rel="nofollow">Где поесть</a></li>
          <li><a class="travell5m neucha large" href="/trace.php">Трассы</a></li>
        </ul></li>  
        </ul>
    </div> <!-- конец меню -->
  </div>  
  </nav>
<!-- шапка -->
<!-- закончилась шапка, начался средний уровень - основной -->
<div id="wraper" style="margin-top:10px">
  
