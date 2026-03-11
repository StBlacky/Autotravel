<?php
// ===============================
// Единый футер Autotravel.ru
// ===============================

// Закрываем основную часть контента (открывается в header_bs.php)
?>
</div> <!--  конец вывода основной части -->

<?php if (empty($_short_footer)) : ?>
<footer class="site-footer">
  <div class="site-footer-inner">

    <nav class="site-footer-nav">
      <a href="/areas.php" class="footer-link">Области России</a>
      <span class="footer-sep">·</span>
      <a href="/towns.php" class="footer-link">Города</a>
      <span class="footer-sep">·</span>
      <a href="/cube.php" class="footer-link">Достопримечательности</a>
      <span class="footer-sep">·</span>
      <a href="/trace.php" class="footer-link">Трассы</a>
      <span class="footer-sep">·</span>
      <a href="/phalbum.php" class="footer-link">Фотоархив</a>
      <span class="footer-sep">·</span>
      <a href="/countries.php" class="footer-link">Зарубежные страны</a>
      <span class="footer-sep">·</span>
      <a href="/faq.php" class="footer-link">ЧАВО</a>
    </nav>

    <div class="site-footer-contact">
      По вопросам сотрудничества и размещения рекламы:
      <a href="mailto:plo@autotravel.ru" class="footer-link">plo@autotravel.ru</a>
    </div>

    <div class="site-footer-copy">
      © 2004–<?php echo date('Y'); ?> Autotravel.ru
    </div>

    <div class="site-footer-counters">
      <!-- LiveInternet counter -->
      <!--noindex-->
      <span class="footer-counter">
        <script type="text/javascript">
        document.write('<a href="//www.liveinternet.ru/click" target="liveinternet"><img src="//counter.yadro.ru/hit?t43.2;r'
          + escape(top.document.referrer)
          + ((typeof(screen)=='undefined') ? '' : ';s' + screen.width + '*' + screen.height + '*' + (screen.colorDepth?screen.colorDepth:screen.pixelDepth))
          + ';' + Math.random()
          + '" border="0" width="31" height="31" alt="liveinternet.ru"></a>');
        </script>
      </span>
      <!--/noindex-->
      <!-- /LiveInternet -->

      <!-- Yandex.Metrika informer + counter -->
      <span class="footer-counter">
        <a href="https://metrika.yandex.ru/stat/?id=30389502&amp;from=informer"
           target="_blank" rel="nofollow">
          <img src="https://informer.yandex.ru/informer/30389502/3_1_FFFFFFFF_EFEFEFFF_0_pageviews"
               style="width:88px; height:31px; border:0;" alt="Яндекс.Метрика"
               title="Яндекс.Метрика: данные за сегодня"
               class="ym-advanced-informer" data-cid="30389502" data-lang="ru" />
        </a>
      </span>

      <script type="text/javascript">
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
      <!-- /Yandex.Metrika -->
    </div>

  </div>
</footer>
<?php endif; ?>

<!-- Организация (общий JSON-LD, один на весь сайт) -->
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

<?php
// Страничный JSON-LD (например, area.php) — сюда кладём, НО уже готовыми <script>...</script>
if (!empty($customjsonld)) {
  echo $customjsonld, "\n";
}

// Кнопка "наверх"
if (!empty($_totop)) {
  echo '<a href="#" id="cltotop" title="Наверх страницы">&uarr;</a>';
  $customscripttop .= "\n  $('#cltotop').hide();\n";
  $customscript    .= "
    window.onscroll = function() { scrollFunction(); };
    function scrollFunction() {
      if (document.body.scrollTop > 200 || document.documentElement.scrollTop > 200) {
        $('#cltotop').show();
      } else {
        $('#cltotop').hide();
      }
    }
    $('#cltotop').on('click', function (e) {
      e.preventDefault();
      $('html,body').animate({ scrollTop: 0 }, 700);
    });
  ";
}

// Валидация формы отзывов (если нужна)
if (!empty($validator)) {
  $customscript .= "
    $('#otklikform').validate({
      ignore: []
    });
  ";
}

// Colorbox для фоток, если используется
if (!empty($colorbox)) {
  ?>
  <script type="text/javascript">
  jQuery(function($){
    $('a.atcbox').colorbox({
      <?php if ($colorbox == 1) : ?>rel:'atcbox',<?php endif; ?>
      maxWidth:'95%',
      maxHeight:'95%',
      opacity:'0.3',
      transition:'fade',
      title:function(){ return $(this).attr('title'); }
    });
  });
  </script>
  <?php
}
?>

<script type="text/javascript">
<?php if (!empty($masonry)) : ?>
function autocardmason(obj) {
  obj.imagesLoaded().always(function() {
    obj.masonry({
      itemSelector: ".autocard",
      columnWidth: ".autocard"
    });
  });
}
<?php endif; ?>

<?php if (!empty($ug_gallery)) : ?>
function atug() {
  $("#fotoimages").unitegallery({
    gallery_theme: "tiles",
    tiles_type: "justified",
    tiles_justified_row_height: 130,
    tile_enable_textpanel: false,
    tile_textpanel_appear_type: "fade",
    lightbox_type: "compact",
    lightbox_textpanel_enable_description: true
  });
}
<?php endif; ?>

<?php
// ВАЖНО: здесь только "голый" JS, без <script>...</script>
if (!empty($customscripttop)) {
  echo $customscripttop, "\n";
}
?>

$(document).ready(function () {
  <?php
  if (!empty($customscript)) {
    echo $customscript, "\n";
  }
  if (!empty($ug_gallery)) {
    echo "atug();\n";
  }
  ?>

  $('#sidebar-collapse').on('click', function () {
    $('#sidebar').toggleClass('active');
  });

  $('.tocollapse').on('shown.bs.collapse', function (event) {
    var thid   = $(this).attr("id");
    var iconid = '#ico-' + thid;
    var txtid  = '#txt-' + thid;
    $(iconid).removeClass("glyphicon-chevron-down").addClass("glyphicon-chevron-up");
    $(txtid).text("Спрятать");
    if (thid === 'maphidden' && typeof showmap === 'function') {
      showmap();
    }
    event.stopPropagation();
  });

  $('.tocollapse').on('hidden.bs.collapse', function (event) {
    var thid   = $(this).attr("id");
    var iconid = '#ico-' + thid;
    var txtid  = '#txt-' + thid;
    $(iconid).removeClass("glyphicon-chevron-up").addClass("glyphicon-chevron-down");
    $(txtid).text("Раскрыть");
    event.stopPropagation();
  });

  <?php if (!empty($masonry)) : ?>
  $('.autocard-masonry').each(function(){
    autocardmason($(this));
  });
  $('.autocard-mason-light').masonry({
    itemSelector: ".autocard",
    columnWidth: ".autocard"
  });
  <?php endif; ?>
});
</script>

</div> <!-- //maincontainer -->
</body>
</html>
