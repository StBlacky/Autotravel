<?php
include 'include/head01.php';
?>
<!-- Гугл эдсенс -->
<script>
  (adsbygoogle = window.adsbygoogle || []).push({
    google_ad_client: "ca-pub-2266145788867998",
    enable_page_level_ads: true
  });
</script>
<!-- Гугл эдсенс --> 
<?php 
//
// место на странице - 1 в заголовке, 2 - слева под всем, 3 - в подвале только это все уже тыщураз сломано ради гугла, яндекса, фейсбука и вконтакта.
$banners_show_1 = "";
$banners_show_2 = "<a class=\"travell5r\" href=\"http://triproom.ru\">http://triproom.ru</a><br><b>Квартиры посуточно по всей России без посредников!</b>"; # квартиры до 16.04.2017
$banners_show_3 = "";

?>
<!-- контейнер всей страницы -->
<div class="maincontainer"> 
<!-- начало шапки страницы -->
<?php 
if ($_text_mode != 1) { 
 // графический режим
?>
<!-- первая строка - картинки и баннеры  -->
<div class="headerline">
  <div class="logo"><a href="/areas.php"><img border=0 src='/jpg/head01.jpg' alt='Россия: города, достопримечательности'></a></div>
  <div class="header-top"> <!-- вторая и третья картинки -->
     <div class="logo-left">
       <a href="/index.php"><img border=0 src='/jpg/head02.jpg' alt='Россия: города, достопримечательности' title='Области и регионы России: города, достопримечательности, маршруты путешествий'></a>
     </div>   
     <!-- Баннер, если есть --> 
     <?php
     if (($banners_show_1)) {
    //<!-- заграница - если есть width='480' -->
        echo "<div class=\"logo-with-banner\">"; 
           if (!($_zagr_mode)) {
              echo "<div class=\"logo-right\"><a href='/countries.php'><img border=0 src='/jpg/smallabroad.jpg' alt='Автопутешествия: зарубежные страны ' title='Переход на страницу Зарубежные страны, популярные туристические места'></a></div>";
           } 
           echo "<div class=\"logo-banner\">";  // первое баннерное место - в самом верху под заграницей, пока вакантно
           echo $banners_show_1;
           echo "</div>";
        echo "</div>";
      }  else {  // баннера нет
        if (!($_zagr_mode)) {
            echo "<div class=\"logo-right\"><a href='/countries.php'><img border=0 src='/jpg/head03.jpg' alt='Автопутешествия: зарубежные страны ' title='Переход на страницу Зарубежные страны, популярные туристические места'></a></div>";
        }
      }
    ?>
  </div> <!-- header-top - вторая и третья картинки -->
 <!-- вторая строка - верхнее меню -->
  <div class="header-bottom"><nav><ul class="maintopmenu" >
       <li><a class=travell5m href="/towns.php"><img src="/jpg/1p-towns.jpg" alt="Города" title="Список городов России и мира по алфавиту, рейтинг городов." border=0></a></li>
       <li><a class=travell5m href="/cube.php"><img src="/jpg/1p-ex.jpg"  alt="Достопримечательности" title="Достопримечательности России и мира - как добраться, отзывы, рейтинг." border=0></a></li>
       <li><a class=travell5m href="<?php echo $searchurl; ?>extype[]=2" rel="nofollow"><img src="/jpg/1p-hotel.jpg"  alt="Где остановиться" title="Поиск гостиниц по России и всему миру - отзывы, рейтинг, бронирование." border=0></a></li>
       <li><a class=travell5m href="<?php echo $searchurl; ?>extype[]=3" rel="nofollow"><img src="/jpg/1p-cafe.jpg" alt="Где поесть" title="Поиск кафе и ресторанов по России и всему миру - отзывы, рейтинг." border=0></a></li>
       <li><a class=travell5m href="/trace.php"><img src="/jpg/1p-trace.jpg" alt="Трассы" title="Описание и состояние дорог, расстояние между городами." border = 0></a></li>
       <?php  if ($forum_user_basket) {  ?>
       <li><p class="travell0">
         <a class=travell5r id="basket" href="/basket_v.php/<?php echo $forum_user_basket; ?>" rel="nofollow">Поездка <?php echo $forum_user_basket_name; ?>:
       <?php  if ($forum_user_basket_count>0) {echo "$forum_user_basket_count объектов";
	  } else {echo "пустая"; } ?> </a></p></li>
        <?php  }  ?>
          </ul></nav></div>
</div>
 <?php
}
else {
 // текстовый режим 
   //echo "<tr><td>"; строка столбец главной таблицы
   echo "<div>"; 
   echo "<TABLE cellSpacing=0 cellpadding=0 border=0 width='100%'><tr>";
   echo "<td width='169'><a class=travell5q href='/index.php'>Автопутешествия по России</a></td>\n<td>";
   echo "<TABLE cellSpacing=5 cellpadding=0 border=0><TBODY class=\"t13 tl travell0t\"><tr>";
   echo "<td><a class=travell5m href='/towns.php'>&diams;Города</a></td>\n";
   echo "<td><a class=travell5m href='/cube.php'>&diams;Достопримечательности</a></TD>\n";
   echo "<td><a class=travell5m href='$searchurl"; echo "extype[]=2' rel='nofollow'>&diams;Где&nbsp;остановиться</a></TD>\n";
   echo "<td><a class=travell5m href='$searchurl"; echo "extype[]=3' rel='nofollow'>&diams;Где&nbsp;поесть</a></TD>\n";
   echo "<td><a class=travell5m href='/trace.php'>&diams;Трассы</a></td>\n";
   if ($forum_user_basket) {
     echo "<td><p class=travell0><a class=travell5r id='basket' href='/basket_v.php/$forum_user_basket' rel='nofollow'>Поездка $forum_user_basket_name: ";
     if ($forum_user_basket_count>0) {echo "$forum_user_basket_count объектов";} else { echo "пустая";}
     echo "</a></p></td>";
   }
   echo "</tr></tbody></table></td>\n";
   if (!($_zagr_mode)) {
      echo "<td align=right><a class=travell5q href=\"/countries.php\">Зарубежные страны</a></td>\n";
   }
  echo "</tr></table>";
  echo "</td></tr>";
}  
?>
<!-- закончилась шапка, начался средний уровень - основной  -->
<div class="bodyline">
<TABLE width='100%' border=0 cellspacing=0 cellpadding=0>
 <tr><td width='169' valign=top align=left class=travell01t>
   <!--Левое меню -->
   <div class="bodyline-left-column">
        <nav class="left-menu">
             <ul> 
                 <li><a class=travell5m href="<?php echo $searchurl; ?>extype[]=1&amp;extype[]=6" rel="nofollow"><?php if ($_text_mode != 1) { ?><img src="/jpg/1p-find.jpg" alt="Поиск" border = 0><?php } else {echo "&diams;Поиск";}?></a></li>
   <li><A class=travell5m href="/marshruty.php"><?php if ($_text_mode != 1) { ?><IMG SRC="/jpg/1p-marshrut.jpg" title = "Готовые маршруты, маршрутизатор" alt="Маршруты" border="0"><?php } else {echo "&diams;Маршруты";}?></A></li>
   <li><A class=travell5m href="/rd_report.php"><?php if ($_text_mode != 1) { ?><IMG SRC="/jpg/2p-read.jpg" alt="Отчёты о поездках" border="0"><?php } else {echo "&diams;Отчеты&nbsp;о&nbsp;поездках";}?></A></li>
   <li><A class=travell5m href="/wr_report.php" rel="nofollow"><?php if ($_text_mode != 1) { ?><IMG SRC="/jpg/1p-write.jpg" alt="Написать отчет" border="0"><?php } else {echo "&diams;Написать&nbsp;отчет";}?></A></li>
   <li><A class=travell5m href="/faq.php" rel="nofollow"><?php if ($_text_mode != 1) { ?><IMG SRC="/jpg/1p-faq.jpg" alt="ЧаВо" border="0"><?php } else {echo "&diams;ЧаВо";}?></A></li>
   <li><A class=travell5m href="/sitemgr.php" rel="nofollow">
   <?php
   if ($forum_user_id > 0) {
     if ($_text_mode != 1) {
     	echo "<IMG SRC=\"/jpg/1p-club.jpg\" alt=\"Для форумчан\" border=\"0\"></A></li>";
     } 
     else { 
        echo "&diams;Для форумчан</A></li>";         
     }
   } 
   else {
     if ($_text_mode != 1) {
        echo "<IMG SRC=\"/jpg/1p-logon.jpg\"  alt=\"Войти на сайт\" border=\"0\"></A></li>";
     } 
     else {
         echo "&diams;Войти&nbsp;на&nbsp;сайт</A></li>";          
     }
   }     
   if (($forum_user_level>0) or ($forum_user_rank>0)) {
    echo "<li><A class=travell5m href=\"/$_fotoalbum/author/".$forum_user_id."/0\" rel=\"nofollow\">";
    if ($_text_mode != 1) { 
        echo "<IMG SRC=\"/jpg/1p-foto.jpg\" alt = \"Фотографии\" border=\"0\">";
     }
     else {
        echo "&diams;Фотоархив";         
     }
     echo "</a></li>";
   }
   ?>
   <li><A class=travell5m target=_blank href="<?php echo $forumname; ?>/index.php"><?php if ($_text_mode != 1) { ?><IMG SRC="/jpg/1p-forum.jpg" alt="Форум" border="0"><?php } else {echo "&diams;Форум";}?></A></li>
   <li>
    <?php
      echo "<A class=travell5m href='$calname' rel=\"nofollow\">";
      if ($_text_mode != 1) {
      	 echo "<IMG SRC='/jpg/1p-cal.jpg' alt='Календарь поездок' border='0'></A></li>";
      } else { 
          echo "&diams;Календарь&nbsp;поездок</a></li>";  
      }
      echo "</ul></nav>"; //закончилось левое меню
      if ($_short_menu != 1) {
          echo "<div class=\"left-column-calendar\">";
          include "include/ourdates.php";}
          echo "</div>"
    ?>
               
                  
  <?php
    // баннерное место яндекса. Показывается только в графическом режиме, разумеется.
   if (($_text_mode != 1)) { // 
  ?>
    <!-- Yandex.RTB R-A-208330-1 -->
     <div class="left-column-reclama"><div id="yandex_rtb_R-A-208330-1"></div></div>
          <script type="text/javascript">
          (function(w, d, n, s, t) {
        w[n] = w[n] || [];
        w[n].push(function() {
            Ya.Context.AdvManager.render({
                blockId: "R-A-208330-1",
                renderTo: "yandex_rtb_R-A-208330-1",
                horizontalAlign: true,
                async: true
            });
        });
        t = d.getElementsByTagName("script")[0];
        s = d.createElement("script");
        s.type = "text/javascript";
        s.src = "//an.yandex.ru/system/context.js";
        s.async = true;
        t.parentNode.insertBefore(s, t);
       })(this, this.document, "yandexContextAsyncCallbacks");
       </script>
     <!-- Увы, яндекс скрипт никуда уносить от яндекс div нельзя, иначе все искривляется !-->
   <?php 
   } 
  ?>
                   
    <div class="left-column-topusers">
        <font class=travell5m>&diams;Где бывали:</font><br><font class=travell0>Больше всех оставили отзывов:<br>
    <?php
     $sql_wd="select user_id, username, user_excitings from bl_users order by user_excitings desc limit 10";
     $result_wd = $dbi->query($sql_wd);
     while ($myrow_wd = mysqli_fetch_array($result_wd)) {
      $ttuname   = $myrow_wd["username"];
      $ttuid  = $myrow_wd["user_id"];
      $ttucount  = $myrow_wd["user_excitings"];
      echo "&nbsp;&nbsp;<a href='/user.php/$ttuid' rel=\"nofollow\"> $ttuname ($ttucount)</a><br>";
     } 
     ?>
    </font>
    </div>
 
    <div class="left-column-social">
      <!-- место для фейсбука !-->
      <div id="facebook-at"></div>
      <!-- место для вконтакта !--> 
      <div id="vk_groups"></div>
    </div>

   <?php 
    // второе баннерное место. Показывается только в графическом режиме, разумеется. квартир до 16.04.2017
   if (($_text_mode != 1)) { 
    if ($banners_show_2) {   
     echo "<div class=\"left-column-reclama-botom\">";
       echo $banners_show_2;
     echo "</div>";
    }
   }
   ?>

   </div>
    <!-- закончилось левое меню -->  
   </td> 
 <td valign=top class=travell01s>
