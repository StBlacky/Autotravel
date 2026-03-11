<!-- пора закрывать таблицу основной части сайта -->
 </tr></table>

<!-- а теперь закроем и всю таблицу 
 </td></tr></table>  -->
</div>

<div class="footerline">
<!-- повторим верхнее текстовое меню -->
<?php
  
 // echo "<TABLE cellSpacing=0 cellpadding=0 border=0 width='100%'><tr>";
 // echo "<td ><center>";
 // echo "<TABLE cellSpacing=5 cellpadding=0 border=0><TBODY class=t13><tr>";
  echo "<div  itemprop=\"breadcrumb\">"; //микроразметка навигации
  echo "<ul class=\"menuline\">";
  echo "<li><a class=travell5m title='Главная' href='/'>Главная</a></li>";
  echo "<li><a class=travell5m title='Быстрый переход к странице, где перечислены все регионы России' href='/areas.php'>Области России</a></li>";
  echo "<li><a class=travell5m title='Наиболее популярные туристические города, список городов по алфавиту' href='/towns.php'>Города</a></li>";
  echo "<li><a class=travell5m title='Достопримечательности России в табличном виде - быстрый переход на любой тип достопримечательностей' href='/cube.php'>Достопримечательности</a></li>";
  echo "<li><a class=travell5m title='Преднастроенная страница поиска любой гостиницы в базе сайта ' href='$searchurl"; echo "extype[]=2' rel='nofollow'>Где&nbsp;остановиться</a></li>";
  echo "<li><a class=travell5m title='Преднастроенная страница поиска любого общепита в базе сайта ' href='$searchurl"; echo "extype[]=3' rel='nofollow'>Где&nbsp;поесть</a></li>";
  echo "<li><a class=travell5m title='Список трасс с возможностью посмотреть описание и состояние трассы' href='/trace.php'>Дороги</a></li>";
  
  if (!($_zagr_mode)) {
      echo "<li><a class=travell5m title='Наиболее популярные зарубежные страны' href=\"/countries.php\">Зарубежные страны</a></li>\n";
  }
  echo "</ul></div>"; //микроразметка навигации
 
 
  // Строго ПОСЛЕ ВСЕХ ТАБЛИЦ
  if ($fotorama) {
    echo $ramafill;  // пока не используется :(
  }
  if ($colorbox) {
    echo "<script type=\"text/javascript\">\n"; 
    echo "jQuery('a.atcbox').colorbox({rel:'atcbox',  opacity:'0.3', transition:'fade', title: function() {\n";
    //echo "var url=\$(this).attr('href');\n";
    //echo "var urln = url.substring(0,(url.length - 4)) + '.html';\n";
    //echo "return '<a href=\"'+ urln +'\" target=\"_blank\">' + \$(this).attr('title') + '</a>';\n ";
    echo "return \$(this).attr('title');\n ";
    echo "}\n";
    echo "});</script>\n";
  }
 if ($_short_menu != 1) {//вывод счетчиков, фейсбука и вконтакта
 ?>
 <!-- Вконтакт !-->  
 <script type="text/javascript" src="//vk.com/js/api/openapi.js?142"></script>
 <script type="text/javascript">VK.Widgets.Group("vk_groups", {mode: 3, no_cover: 1}, 142525944);</script>
 <!-- ФейсБук !-->  
 <script type="text/javascript">
   var fbdiv = document.getElementById('facebook-at');
   if ( fbdiv != null) {
     fbdiv.innerHTML='<div class="fb-like" data-href="https://www.facebook.com/autotravelall"' + 
             ' data-width="200" data-layout="box_count" data-action="recommend" data-size="large"' + 
             ' data-show-faces="true" data-share="true"></div>';
   }
 </script>    
 <!-- FB  выполняемый модуль -->
 <div id="fb-root"></div>
 <script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/ru_RU/sdk.js#xfbml=1&version=v2.8";
  fjs.parentNode.insertBefore(js, fjs);
 }(document, 'script', 'facebook-jssdk'));</script>
<!-- FB -->
<br>
<!--LiveInternet counter-->
 <!--noindex-->
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
target="_blank" rel="nofollow"><img src="//bs.yandex.ru/informer/30389502/3_1_FFFFFFFF_EFEFEFFF_0_pageviews"
style="width:88px; height:31px; border:0;" alt="Яндекс.Метрика" title="Яндекс.Метрика: данные за сегодня (просмотры, визиты и уникальные посетители)" onclick="try{Ya.Metrika.informer({i:this,id:30389502,lang:'ru'});return false}catch(e){}"></a>
<!-- /Yandex.Metrika informer -->

<!-- Yandex.Metrika counter -->
<script type="text/javascript">
(function (d, w, c) {
    (w[c] = w[c] || []).push(function() {
        try {
            w.yaCounter30389502 = new Ya.Metrika({id:30389502,
                    webvisor:true,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true});
        } catch(e) { }
    });

    var n = d.getElementsByTagName("script")[0],
        s = d.createElement("script"),
        f = function () { n.parentNode.insertBefore(s, n); };
    s.type = "text/javascript";
    s.async = true;
    s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

    if (w.opera == "[object Opera]") {
        d.addEventListener("DOMContentLoaded", f, false);
    } else { f(); }
})(document, window, "yandex_metrika_callbacks");
</script>
<noscript><div><img src="//mc.yandex.ru/watch/30389502" style="position:absolute; left:-9999px;" alt=""></div></noscript>
<!-- /Yandex.Metrika counter -->

<!-- GoogleAnalyticsObject -->
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-91763022-1', 'auto');
  ga('send', 'pageview');

</script>
<!-- GoogleAnalyticsObject -->

<?php } echo "$reclama11";?>

<script type="text/javascript">
  if (window.matchMedia("(max-width: 768px)").matches) {
   //document.write(' m');
  }
  else if (window.matchMedia("(max-width: 1024px)").matches) {
   //document.write(' i');
  }
  else {
   //document.write(' d');
  }
</script>


</center>

<script type="application/ld+json">
{
  "@context" : "http://schema.org",
  "@type" : "Organization",
  "name" : "По России на авто. За границу на авто.",
  "url" : "https://autotravel.ru",
  "sameAs" : [
    "https://www.facebook.com/autotravelall",
    "https://vk.com/public142525944"
   ]
}
</script>
</div> <!-- footerline </td></tr></table> -->
</div> <!-- //maincontainer -->
</body>
</html>

