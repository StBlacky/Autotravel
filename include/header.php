<?php
include 'include/head01.php';
if (!(isset($noads))) {
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
}
//
// место на странице - 1 в заголовке, 2 - слева под всем, 3 - в подвале только это все уже тыщураз сломано ради гугла, яндекса, фейсбука и вконтакта.
$banners_show_1 = "";
$banners_show_2 = ""; // "<a class=\"travell5r\" href=\"http://triproom.ru\">http://triproom.ru</a><br><b>Квартиры посуточно по всей России без посредников!</b>"; # квартиры до 16.04.2017
$banners_show_3 = "";
echo "<div class=\"maincontainer\"> ";
?>
<!-- начало шапки страницы -->
<?php 
if ($_text_mode != 1) { 
 // графический режим
?>
<!-- первая строка - картинки и баннеры  -->
<div class="headerline">
  <div class="logo"><a href="/areas.php"><img  src='/jpg/head01.jpg' alt='Россия: города, достопримечательности'></a></div>
  <div class="header-top"> <!-- вторая и третья картинки -->
     <div class="logo-left">
       <a href="/index.php"><img src='/jpg/head02.jpg' alt='Россия: города, достопримечательности' title='Области и регионы России: города, достопримечательности, маршруты путешествий'></a>
     </div>   
     <!-- Баннер, если есть --> 
     <?php
     if (($banners_show_1)) {
    //<!-- заграница - если есть width='480' -->
        echo "<div class=\"logo-with-banner\">"; 
           if (!($_zagr_mode)) {
              echo "<div class=\"logo-right\"><a href='/countries.php'><img src='/jpg/smallabroad.jpg' alt='Автопутешествия: зарубежные страны ' title='Переход на страницу Зарубежные страны, популярные туристические места'></a></div>";
           } 
           echo "<div class=\"logo-banner\">";  // первое баннерное место - в самом верху под заграницей, пока вакантно
           echo $banners_show_1;
           echo "</div>";
        echo "</div>";
      }  else {  // баннера нет
        if (!($_zagr_mode)) {
            echo "<div class=\"logo-right\"><a href='/countries.php'><img  src='/jpg/head03.jpg' alt='Автопутешествия: зарубежные страны ' title='Переход на страницу Зарубежные страны, популярные туристические места'></a></div>";
        }
      }
    ?>
  </div> <!-- header-top - вторая и третья картинки -->
 <!-- вторая строка - верхнее меню-->
  <div class="header-bottom"><nav><ul class="maintopmenu" >
       <li><a class=travell5m href="/towns.php"><img src="/jpg/1p-towns.jpg" alt="Города" title="Список городов России и мира по алфавиту, рейтинг городов." ></a></li>
       <li><a class=travell5m href="/cube.php"><img src="/jpg/1p-ex.jpg"  alt="Достопримечательности" title="Достопримечательности России и мира - как добраться, отзывы, рейтинг." ></a></li>
       <li><a class=travell5m href="<?php echo $searchurl; ?>extype=2" rel="nofollow"><img src="/jpg/1p-hotel.jpg"  alt="Где остановиться" title="Поиск гостиниц по России и всему миру - отзывы, рейтинг, бронирование." ></a></li>
       <li><a class=travell5m href="<?php echo $searchurl; ?>extype=3" rel="nofollow"><img src="/jpg/1p-cafe.jpg" alt="Где поесть" title="Поиск кафе и ресторанов по России и всему миру - отзывы, рейтинг." ></a></li>
       <li><a class=travell5m href="/trace.php"><img src="/jpg/1p-trace.jpg" alt="Трассы" title="Описание и состояние дорог, расстояние между городами." ></a></li>
       <?php  if ($forum_user_basket) {  ?>
       <li><p class="travell0">
         <a class=travell5r id="basket" href="/basket_v.php/<?php echo $forum_user_basket; ?>" rel="nofollow">Поездка <?php echo $forum_user_basket_name; ?>:
       <?php  if ($forum_user_basket_count>0) {echo "$forum_user_basket_count объектов";
	  } else {echo "пустая"; } ?> </a></p></li>
        <?php  }  ?>
          </ul></nav></div>
  <!-- шапка -->
<!-- вторая строка заголовка -->
</div>
 <?php
}
else {
 // текстовый режим 
   echo "<div>"; 
   echo "<TABLE cellSpacing=0 cellpadding=0 width='100%'><tr>";
   echo "<td width='215'><a class=travell5q href='/index.php'>Автопутешествия по России</a></td>\n<td>";
   echo "<TABLE cellSpacing=5 cellpadding=0 ><TBODY class=\"t13 tl travell0t\"><tr>";
   echo "<td><a class=travell5m href='/towns.php'>&#9674;Города</a></td>\n";
   echo "<td><a class=travell5m href='/cube.php'>&#9674;Достопримечательности</a></TD>\n";
   echo "<td><a class=travell5m href='$searchurl"; echo "extype=2' rel='nofollow'>&#9674;Где&nbsp;остановиться</a></TD>\n";
   echo "<td><a class=travell5m href='$searchurl"; echo "extype=3' rel='nofollow'>&#9674;Где&nbsp;поесть</a></TD>\n";
   echo "<td><a class=travell5m href='/trace.php'>&#9674;Трассы</a></td>\n";
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
<!-- закончилась шапка, начался средний уровень - основной -->
<div class="bodyline">
<TABLE width='100%' style="min-width:699px;" cellspacing=0 cellpadding=0>
 <tr><td style="width:215px" valign=top align=left class=travell01t>
   <!--Левое меню -->
   <div class="bodyline-left-column">
        <nav class="left-menu">
             <ul> 
                 <li><a class=travell5m href="<?php echo $searchurl; ?>extype[]=1&amp;extype[]=6" rel="nofollow"><?php if ($_text_mode != 1) { ?><img src="/jpg/1p-find.jpg" alt="Поиск" ><?php } else {echo "&#9674;Поиск";}?></a></li>
   <li><A class=travell5m href="/marshruty.php"><?php if ($_text_mode != 1) { ?><IMG SRC="/jpg/1p-marshrut.jpg" title = "Готовые маршруты, маршрутизатор" alt="Маршруты" ><?php } else {echo "&#9674;Маршруты";}?></A></li>
   <li><A class=travell5m href="/rd_report.php"><?php if ($_text_mode != 1) { ?><IMG SRC="/jpg/2p-read.jpg" alt="Отчёты о поездках" ><?php } else {echo "&#9674;Отчеты&nbsp;о&nbsp;поездках";}?></A></li>
   <li><A class=travell5m href="/wr_report.php" rel="nofollow"><?php if ($_text_mode != 1) { ?><IMG SRC="/jpg/1p-write.jpg" alt="Написать отчет"><?php } else {echo "&#9674;Написать&nbsp;отчет";}?></A></li>
   <li><A class=travell5m href="/faq.php" rel="nofollow"><?php if ($_text_mode != 1) { ?><IMG SRC="/jpg/1p-faq.jpg" alt="ЧаВо"><?php } else {echo "&#9674;ЧаВо";}?></A></li>
   <li><A class=travell5m href="https:<?php echo $sitename;?>/sitemgr.php" rel="nofollow">
   <?php
   if ($forum_user_id > 0) {
     if ($_text_mode != 1) {
     	echo "<IMG SRC=\"/jpg/1p-club.jpg\" alt=\"Для форумчан\" ></A></li>";
     } 
     else { 
        echo "&#9674;Для форумчан</A></li>";         
     }
   } 
   else {
     if ($_text_mode != 1) {
        echo "<IMG SRC=\"/jpg/1p-logon.jpg\"  alt=\"Войти на сайт\" ></A></li>";
     } 
     else {
         echo "&#9674;Войти&nbsp;на&nbsp;сайт</A></li>";          
     }
   }     
   if (($forum_user_level>0) or ($forum_user_rank>0)) {
    echo "<li><A class=travell5m href=\"/$_fotoalbum/author/".$forum_user_id."/0\" rel=\"nofollow\">";
    if ($_text_mode != 1) { 
        echo "<IMG SRC=\"/jpg/1p-foto.jpg\" alt = \"Фотографии\" >";
     }
     else {
        echo "&#9674;Фотоархив";         
     }
       echo "</a></li>";
   }
   ?>
   <li><A class=travell5m target=_blank href="<?php echo $forumname; ?>/index.php"><?php if ($_text_mode != 1) { ?><IMG SRC="/jpg/1p-forum.jpg" alt="Форум" ><?php } else {echo "&#9674;Форум";}?></A></li>
   <li>
    <?php
      echo "<A class=travell5m href='$calname' rel=\"nofollow\">";
      if ($_text_mode != 1) {
      	 echo "<IMG SRC='/jpg/1p-cal.jpg' alt='Календарь поездок' ></A></li>";
      } else { 
          echo "&#9674;Календарь&nbsp;поездок</a></li>";  
      }
      echo "</ul></nav>"; //закончилось левое меню
      if ($_short_menu != 1) {
        //echo "<div class=\"left-column-calendar\">";
        //include "include/ourdates.php";}
        //echo "</div>"
        // баннерное место яндекса. Показывается только в графическом режиме, разумеется.
        if (($_text_mode != 1) and (1==0)) { //
        ?>
        <!-- Yandex.RTB R-A-208330-1 -->
        <div class="left-column left-column-reclama"><div id="yandex_rtb_R-A-208330-1"></div></div>
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
        <div class="left-column left-column-topusers">
          <font class=travell5m>&#9674;&nbsp;Где бывали:</font><br><span class=travell0>Больше всех оставили отзывов:<br>
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
           </span>
        </div>
 
        <div class="left-column left-column-social">
          <!-- место для фейсбука !-->
          <div id="facebook-at"></div>
          <!-- место для вконтакта !--> 
          <div id="vk_groups"></div>
        </div>
        <?php 
        // второе баннерное место. Показывается только в графическом режиме, разумеется. квартир до 16.04.2017
        if (($_text_mode != 1)) { 
            if ($banners_show_2) {   
              echo "<div class=\"left-column left-column-reclama-botom\"><span class=travell0>";
              echo $banners_show_2;
              echo "</span></div>";
            }
        }
      }     

   ?>
   </div>
    <!-- закончилось левое меню -->  
   </td> 
 <td valign=top class=travell01s>