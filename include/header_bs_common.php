<?php

//
// место на странице - 1 в заголовке, 2 - слева под всем, 3 - в подвале только это все уже тыщураз сломано ради гугла, яндекса, фейсбука и вконтакта.
$banners_show_1 = "";
$banners_show_2 = ""; //"<a class=\"travell5r\" href=\"https://triproom.ru\">Квартиры посуточно по всей России без посредников!</a>"; 
$banners_show_3 = "";

echo "<div id=\"maincontainer\"> "; // контейнер всего сайта
?>
<!-- начало шапки страницы -->
<!-- первая строка - картинки и баннеры  -->
<div class="headerline">
  <div class="logo"><a href="/areas.php"><img  src='/jpg/head01.png' alt='Россия: города, достопримечательности'></a></div>
  <div class="header-top"> <!-- вторая и третья картинки -->
     <div class="logo-left">
         <a href="/index.php"><img src='/jpg/head02.png' alt='Россия: города, достопримечательности' title='Области и регионы России: города, достопримечательности, маршруты путешествий'></a>
     </div>   
      
     <!-- Баннер, если есть --> 
     <?php
     if (($banners_show_1) and (!(isset($noads))))  {
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
            echo "<div class=\"logo-right\"><a href='/countries.php'><img  src='/jpg/logo-zagranizu.png' alt='Автопутешествия: зарубежные страны ' title='Переход на страницу Зарубежные страны, популярные туристические места'></a></div>";
        }
      }
    ?>
     <!-- Временно, пока стилевые файлы не обновятся-->
     <style> .logo-right{
       background: linear-gradient(90deg, #f4f1e7, #005b33 50%);
       max-width: 50%;
     }
     .logo-left {
        
      background-color: #f4f1e7;
     }
     .menu-closeser, .menu-close {
         float: right;
         border: solid 1px;
         border-color: #005b33;
         border-radius: 3px;
         margin-right: 2em;
         font-size: 1.2em;
         padding: 2px 8px;
     }
     .menu-popupserv ul, .menu-popup ul {
         padding-top: 2em;
     }
     
     @media screen and (max-width: 992px) {
          .bodyline-main, body {
                font-size: 1.3em;
            }
            .header-bottom nav {
        margin-left: 3px;
}
     }
    </style>
  </div> <!-- header-top - вторая и третья картинки -->
  
  <!-- вторая строка - верхнее меню-->
  <div class="text-left header-bottom ">
    <nav  class="navbar navbar-default menu">
      <button type="button" class="navbar-toggle neucha large menu-trigerser"><!-- кнопка меню мобильного -->
        <span class="sr-only"></span>Сервисы
        <span class="glyphicon glyphicon-menu-down"></span>
      </button>
         <!--Верхнее меню Десктопа-->
      <div class="collapse navbar-collapse">  
        <ul class="nav navbar-nav" >
          <li><a class="neucha large" href="/towns.php">Города</a></li>
          <li><a class="neucha large" href="/cube.php">Достопримечательности</a></li>
          <li><a class="neucha large" href="<?php echo $searchurl; ?>extype=2" rel="nofollow">Где остановиться</a></li>
          <li><a class="neucha large" href="<?php echo $searchurl; ?>extype=3" rel="nofollow">Где поесть</a></li>
          <li><a class="neucha large" href="/excite_bring.php">Что привезти</a></li>
          <li><a class="neucha large" href="/trace.php">Трассы</a></li>
          <li><A class="neucha large"  href="/marshruty.php">Маршруты</a></li>
          <?php if ($forum_user_basket > 0 && $forum_user_basket_name !== "") { ?>
<li>
  <a class="neucha" id="basket"
     href="/basket_v.php/<?php echo (int)$forum_user_basket; ?>"
     rel="nofollow">

    <?php echo htmlspecialchars($forum_user_basket_name); ?>
    <?php if ($forum_user_basket_count > 0) { echo "(" . $forum_user_basket_count . ")"; } ?>

  </a>
</li>
<?php } ?>


          
        </ul>
      </div> <!-- конец меню -->  
    </nav>    
    <nav class="navbar navbar-default menuserv" >
       <button type="button" class="navbar-toggle neucha large menu-triger">
        <span class="sr-only"></span>Объекты
        <span class="glyphicon glyphicon-menu-down"></span>
        </button>
       </nav>
      <nav class="navbar navbar-default">
           <a  class="navbar-toggle neucha large" href="/#events" >
            <span class="sr-only"></span>Куда поехать
            <span class="glyphicon glyphicon-menu-down"></span>
          </a>
     </nav> 
    </div>
      
    
      
       <!--Меню мобильного Сервисы-->
      <div class="menu-popupserv"  > 
          <span class="neucha large menu-closeser">Х</span>
        <ul class="nav navbar-nav"> 
            
            <li><a href="https://t.me/officialautotravel" rel="nofollow"  class="neucha"><img src="/jpg/tautotravel1.png" style="height: 4em; padding-right: 5px;" alt="Наш Telegram-канал" title="Наш Telegram-канал" >Наш Telegram-канал</a></li>
            <li><a class="neucha large" href="<?php echo $searchurl; ?>extype[]=1&amp;extype[]=6" rel="nofollow">Поиск</a></li>
            <li><A class="neucha large" href="/rd_report.php">Отчёты о поездках</a></li>
            <li><A class="neucha large" href="/wr_report.php" rel="nofollow">Написать отчёт</a></li>
            <li><A class="neucha large" href="/faq.php" rel="nofollow">ЧаВо</a></li>
            <li><A class="neucha large" href="https:<?php echo $sitename;?>/sitemgr.php" rel="nofollow"><?php
            if ($forum_user_id > 0) { echo "Для форумчан</A></li>";  } 
            else { echo "Настройки сайта</A></li>"; }     
            if (($forum_user_level>0) or ($forum_user_rank>0)) {
                echo "<li><A class=\"travell5m neucha large\" href=\"/$_fotoalbum/author/".$forum_user_id."/0\" rel=\"nofollow\">";
                echo "Фотографии";
                echo "</a></li>";
            }
            ?>
            <li><A class="neucha large" target=_blank href="<?php echo $forumname; ?>/index.php">Форум</A></li>
            <li>  <?php  echo "<A class=\"travell5m neucha large\" href='$calname' rel=\"nofollow\">"; echo "Календарь поездок</A></li>";  ?>
        </ul>
      </div>
      <!-- конец мобильного меню Сервисы-->  
      <!--Меню мобильного Объекты-->
      <div class="menu-popup"  >  
        <span class="neucha large menu-close">Х</span>
        <ul class="nav navbar-nav" >
          <li><a class="neucha large" href="/towns.php">Города</a></li>
          <li><a class="neucha large" href="/cube.php">Достопримечательности</a></li>
          <li><a class="neucha large" href="<?php echo $searchurl; ?>extype=2" rel="nofollow">Где остановиться</a></li>
          <li><a class="neucha large" href="<?php echo $searchurl; ?>extype=3" rel="nofollow">Где поесть</a></li>
          <li><a class="neucha large" href="/excite_bring.php">Что привезти</a></li>
          <li><a class="neucha large" href="/trace.php">Трассы</a></li>
          <li><A class="neucha large"  href="/marshruty.php">Маршруты</a></li>
        <?php if ($forum_user_basket > 0 && $forum_user_basket_name !== "") { ?>
<li>
  <a class="neucha" id="basket"
     href="/basket_v.php/<?php echo (int)$forum_user_basket; ?>"
     rel="nofollow">

    <?php echo htmlspecialchars($forum_user_basket_name); ?>
    <?php if ($forum_user_basket_count > 0) { echo "(" . $forum_user_basket_count . ")"; } ?>

  </a>
</li>
<?php } ?>



        </ul>
      </div> 
      <!-- конец мобильного меню Объекты-->  
          
</div>
<!-- шапка -->

<!-- закончилась шапка, начался средний уровень - основной -->
<div id="wraper">
  <nav id="sidebar" class="sidebar travell01t"><!--Левый столбец -->
    <ul class="nav nav-stacked"> 
        <li><a href="https://t.me/officialautotravel" rel="nofollow"  class="neucha"><img src="/jpg/tautotravel1.png" style="height: 4em; padding-right: 5px;" alt="Наш Telegram-канал" title="Наш Telegram-канал" >Наш Telegram-канал</a></li>
        <li><a class="neucha large" href="<?php echo $searchurl; ?>extype[]=1&amp;extype[]=6" rel="nofollow">Поиск</a></li>
        <li><A class="neucha large" href="/rd_report.php">Отчёты о поездках</a></li>
        <li><A class="neucha large" href="/wr_report.php" rel="nofollow">Написать отчёт</a></li>
        <li><A class="neucha large" href="/faq.php" rel="nofollow">ЧаВо</a></li>
        <li><A class="neucha large" href="https:<?php echo $sitename;?>/sitemgr.php" rel="nofollow"><?php
        if ($forum_user_id > 0) {
   	      echo "Для форумчан</A></li>";
        } 
        else {
          echo "Настройки сайта</A></li>";
        }     
        if (($forum_user_level>0) or ($forum_user_rank>0)) {
          echo "<li><A class=\"travell5m neucha large\" href=\"/$_fotoalbum/author/".$forum_user_id."/0\" rel=\"nofollow\">";
          echo "Фотографии";
          echo "</a></li>";
        }
        ?>
        <li><A class="neucha large" target=_blank href="<?php echo $forumname; ?>/index.php">Форум</A></li>
        <li>
        <?php
          echo "<A class=\"travell5m neucha large\" href='$calname' rel=\"nofollow\">";
          echo "Календарь поездок</A></li>";
        ?>
    </ul>
    <!-- закончилось левое меню-->
    
    
    <?php
    if (($_short_menu != 1)) {
      if ((!(isset($noads))))  {
      ?>              
      <!-- Yandex.RTB R-A-208330-1 -->
      <div class="left-column left-column-reclama">
        <div id="yandex_rtb_R-A-208330-1">
        </div>      
      </div>
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
        <h3 class="neucha tu2">Где бывали:</h3>
        <span class="travell0">Больше всех оставили отзывов:<br>
        <?php
          $sql_wd="select user_id, username, user_excitings from bl_users order by user_excitings desc limit 10";
          $result_wd = $dbi->query($sql_wd);
          while ($myrow_wd = mysqli_fetch_array($result_wd)) {
            $ttuname   = $myrow_wd["username"];
            $ttuid  = $myrow_wd["user_id"];
            $ttucount  = $myrow_wd["user_excitings"];
            echo "&nbsp;&nbsp;<a onclick=\"PoLoad($(this))\" data-poload=\"/profile.php/$ttuid\">$ttuname ($ttucount)</a><br>";  
            //echo "&nbsp;&nbsp;<a href='/user.php/$ttuid' rel=\"nofollow\"> $ttuname ($ttucount)</a><br>";
          } 
        ?>
        </span>
      </div>
      <?php 
      if ($banners_show_2) {   
         echo "<div class=\"left-column left-column-reclama-botom\"><span class=travell0>";
         echo $banners_show_2;
         echo "</span></div>";
      }
    } // конец "краткого меню"  
  ?>
  </nav>

<script>
 /* Скрипты для всплывающих меню */ 
      $(function() {
	var $menu_popup = $('.menu-popup');
	var $menu_popupserv = $('.menu-popupserv');
	$(".menu-triger, .menu-close").click(function(){
		$menu_popup.slideToggle(300, function(){
			if ($menu_popup.is(':hidden')) {
				$('body').removeClass('body_pointer');
			} else {
				$('body').addClass('body_pointer');
			}					
		});  
		return false;
	});
        
        $(".menu-trigerser, .menu-closeser").click(function(){
		$menu_popupserv.slideToggle(300, function(){
			if ($menu_popupserv.is(':hidden')) {
				$('body').removeClass('body_pointer');
			} else {
				$('body').addClass('body_pointer');
			}					
		});  
		return false;
	});
	
	$(document).on('click', function(e){
		if (!$(e.target).closest('.menu').length){
			$('body').removeClass('body_pointer');
			$menu_popup.slideUp(300);
                       
		}
                if (!$(e.target).closest('.menuserv').length){
			$('body').removeClass('body_pointer');
			$menu_popupserv.slideUp(300);
		}
	});
});
      </script>