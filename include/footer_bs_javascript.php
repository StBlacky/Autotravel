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
if ($_totop) {
  echo '<a href="#" id="cltotop" title="Наверх страницы">&uarr;</a>';
  $customscripttop .= "\n  $('#cltotop').hide();\n";
  $customscript .= '
    window.onscroll = function() {scrollFunction()};  
    function scrollFunction() {
      if (document.body.scrollTop > 200 || document.documentElement.scrollTop > 200) {
        $(\'#cltotop\').show();
      } else {
        $(\'#cltotop\').hide();
      }
    };
    $(\'#cltotop\').on(\'click\', function (e) {
        e.preventDefault();
        $(\'html,body\').animate({
            scrollTop: 0
        }, 700);
    });
  ';
}
if ($validator) {
  $customscript .= '
    $(\'#otklikform\').validate({
        ignore: []
    });
  ';
}

if ($colorbox) {
    echo "<script type=\"text/javascript\">\n"; 
    echo "jQuery('a.atcbox').colorbox({";
    if ($colorbox == 1) { // показывать галерею
      echo "rel:'atcbox', ";
    }
    echo "maxWidth:'95%', maxHeight:'95%', opacity:'0.3', transition:'fade', title: function() {\n";
    //echo "var url=\$(this).attr('href');\n";
    //echo "var urln = url.substring(0,(url.length - 4)) + '.html';\n";
    //echo "return '<a href=\"'+ urln +'\" target=\"_blank\">' + \$(this).attr('title') + '</a>';\n ";
    echo "return \$(this).attr('title');\n ";
    echo "}\n";
    echo "});</script>\n";
}

?>

<script type="text/javascript">
  //
  <?php
  if ($masonry) {
  ?>    
    function autocardmason(obj) {
      obj.imagesLoaded().always( 
        function() {
          obj.masonry({
           "itemSelector": ".autocard",
           "columnWidth": ".autocard"
          });
        }
      );          
    }
  <?php 
  }
  if ($ug_gallery) {
  ?>
    function atug() {
      $("#fotoimages").unitegallery({
       gallery_theme: "tiles",
       tiles_type: "justified",			//must option for the tiles - justified type
       tiles_justified_row_height: 130,	//base row height of the justified type
       tile_enable_textpanel: false,
       tile_textpanel_appear_type: "fade",
       lightbox_type: "compact",	
       lightbox_textpanel_enable_description: true
      });      
    }
  <?php
  }
  echo $customscripttop; 
  ?>
  //
  $(document).ready(function () {
    <?php 
    echo $customscript; 
    if ($ug_gallery) {
    ?>
      atug();
    <?php    
    }
    ?>
    $('#sidebar-collapse').on('click', function () {
      $('#sidebar').toggleClass('active');
    });
    $('.tocollapse').on('shown.bs.collapse', function (event) {
      thid = $(this).attr("id");
      iconid='#ico-' + thid;
      txtid='#txt-' + thid;
      $(iconid).removeClass("glyphicon-chevron-down").addClass("glyphicon-chevron-up");
      $(txtid).text("Спрятать");
      if (thid == 'maphidden') {
        showmap();
      }  
      event.stopPropagation();
    });
    $('.tocollapse').on('hidden.bs.collapse', function (event) {
      thid = $(this).attr("id");
      iconid='#ico-' + thid;
      txtid='#txt-' + thid;
      $(iconid).removeClass("glyphicon-chevron-up").addClass("glyphicon-chevron-down");
      $(txtid).text("Раскрыть");
      event.stopPropagation();
    }); 
    <?php
    if ($masonry) {
    ?>    
    $('.autocard-masonry').each( function(){
      autocardmason($(this));
    });
    $('.autocard-mason-light').masonry ({
           "itemSelector": ".autocard",
           "columnWidth": ".autocard"
    });    
    <?php
    }
    ?>    
  });
</script>
