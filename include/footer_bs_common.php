</div> <!--  конец вывода основной части -->
<?php
if (!($_short_footer)) { // реклама и соцсети
?>

<div class="footerline">
<!-- повторим верхнее текстовое меню -->
<?php
  if (!($_totop)) {
    echo "<nav id=\"navbottom\" class=\"navbar\" style=\"text-align:center\">"; 
    echo "<ul class=\"nav navbar-nav\" style=\"margin:auto; display:inline-block; float:none;\">";
    echo "<li><a title='Быстрый переход к странице, где перечислены все регионы России' class='travell5m neucha large' href='/areas.php'>Области России</a></li>";
    echo "<li><a title='Наиболее популярные туристические города, список городов по алфавиту' class='travell5m neucha large' href='/towns.php'>Города</a></li>";
    echo "<li><a title='Достопримечательности России в табличном виде - быстрый переход на любой тип достопримечательностей' class='travell5m neucha large' href='/cube.php'>Достопримечательности</a></li>";
    echo "<li><a title='Преднастроенная страница поиска любой гостиницы в базе сайта' class='travell5m neucha large' href='$searchurl"; echo "extype[]=2' rel='nofollow'>Где остановиться</a></li>";
    echo "<li><a title='Преднастроенная страница поиска любого общепита в базе сайта' class='travell5m neucha large' href='$searchurl"; echo "extype[]=3' rel='nofollow'>Где поесть</a></li>";
    echo "<li><a title='Список трасс с возможностью посмотреть описание и состояние трассы' class='travell5m neucha large' href='/trace.php'>Трассы</a></li>";
  
    if (!($_zagr_mode)) {
      echo "<li><a title='Наиболее популярные зарубежные страны' class='travell5m neucha large' href=\"/countries.php\">Зарубежные страны</a></li>\n";
    }
    echo "</ul></nav>"; //микроразметка навигации
  }  
 
 
  if ($_short_menu != 1) {//вывод счетчиков, фейсбука и вконтакта
  ?>
    <div class="row text-center"> <!-- подвал - соцсети, счетчики, реклама --> 
         <!-- место для чего-нибудь !-->
    <div class="col-sm-3" ></div>    
    
    <!-- место для фейсбука 
    <div class="col-sm-3" id="facebook-at">
    
  <div id="fb-root"></div>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/ru_RU/sdk.js#xfbml=1&version=v9.0" nonce="G18n1FKV"></script>
<div class="fb-share-button" data-href="https://www.facebook.com/autotravelall" data-layout="box_count" data-size="large"><a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fwww.facebook.com%2Fautotravelall&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore">Я рекомендую!</a></div>
    </div>
     FB -->

    <!--LiveInternet counter-->
    <!--noindex-->
    <div class="col-sm-1">
      <script language="JavaScript" type="text/javascript">
      document.write('<a href="//www.liveinternet.ru/click" '+
        'target=liveinternet><img src="//counter.yadro.ru/hit?t43.2;r'+
        escape(top.document.referrer)+((typeof(screen)=='undefined')?'':
        ';s'+screen.width+'*'+screen.height+'*'+(screen.colorDepth?
        screen.colorDepth:screen.pixelDepth))+';'+Math.random()+
        '" border=0 width=31 height=31 alt="liveinternet.ru"></a>');
    </script>
    <!--/noindex-->
    <!--/LiveInternet-->
   <!-- Yandex.Metrika informer -->
<a href="https://metrika.yandex.ru/stat/?id=30389502&amp;from=informer"
target="_blank" rel="nofollow"><img src="https://informer.yandex.ru/informer/30389502/3_1_FFFFFFFF_EFEFEFFF_0_pageviews"
style="width:88px; height:31px; border:0;" alt="Яндекс.Метрика" title="Яндекс.Метрика: данные за сегодня (просмотры, визиты и уникальные посетители)" class="ym-advanced-informer" data-cid="30389502" data-lang="ru" /></a>
<!-- /Yandex.Metrika informer -->

<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
   m[i].l=1*new Date();
   for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
   k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
   (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

   ym(30389502, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true
   });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/30389502" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
    <!-- GoogleAnalyticsObject 
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
         (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
         m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
        ga('create', 'UA-91763022-1', 'auto');
        ga('send', 'pageview');
    </script>
     GoogleAnalyticsObject -->
    </div>
    </div>

  <?php 
  } 
  echo '<div class="col-xs-12">' . $reclama11 . '</div>';
  ?>
</div> <!-- footerline  -->
<?php
} // реклама и соцсети

include_once('footer_bs_javascript.php');
?>
</div> <!-- //maincontainer -->
</body>
</html>

