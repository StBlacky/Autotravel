<?php
include_once("include/head00.php");
include_once("include/fotoman.php");
$_short_footer = 0;
# autotravel.ru     <script src="//maps.google.com/maps?file=api&amp;v=2&amp;hl=ru&amp;key=ABQIAAAAFMZwoCk_W0FlOrGWEcG8-hSkiWjGOTaHXZ4RFhdKUD1ygS1joBQivUjpo-_GuMI6Qp1El3LLZxDekQ" type="text/javascript"></script>
# autotravel.org.ru <script src="//maps.google.com/maps?file=api&amp;v=2&amp;hl=ru&amp;key=ABQIAAAAFMZwoCk_W0FlOrGWEcG8-hSrOn_KGLvWfx-bYd1aYMTYLrRKqBR-yj6I_NPUaZ-KZCinzOBr_cGd9w" type="text/javascript"></script>
#<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
# <meta name="viewport" content="width=device-width">
?>
<!DOCTYPE html>
<html lang="ru">
<head>
 <meta property="og:type" content="website">
 <meta name='yandex-verification' content='7df66c2dcc98af4c'>
 <meta name="yandex-verification" content="eea83a5c57875900" />
<meta name="google-site-verification" content="AllKXyMFj-fb-sWkxHW7tiS4xcuK5s0yyFfhfwgYvho" />
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
 <link rel="shortcut icon" href="/jpg/favicon.ico">
 <link rel="preconnect" href="https://fonts.gstatic.com">
 <script type="text/javascript" language="JavaScript" src="/js/travel12.js"></script>
    <?php
    // каноникл
    if ($canon) {
      echo '<link rel="canonical" href="https://autotravel.ru'.$canon.'" />';
    } 
    // 
    //микроразметка
    $uri = "https:" . $sitename . $_SERVER["PHP_SELF"];
    echo ' <meta property="og:url" content="'.$uri.'" />';
    //микроразметка
  
    if (($colorbox) or ($jhtmleditor) or ($jqueryon) or ($fotorama) or ($bootstrap)) {
      echo ' <script type="text/javascript" language="JavaScript" src="/js/jquery-2.1.4.min.js"></script>';
      echo ' <script type="text/javascript" language="JavaScript" src="/js/jquery.actual.js"></script>';
    }
    if ($bootstrap) {
      echo ' <script type="text/javascript" language="JavaScript" src="/jcss/bootstrap.min.js"></script>';
      echo ' <script type="text/javascript" language="JavaScript" src="/jcss/bootstrap-multiselect.min.js"></script>';
      echo ' <link rel="stylesheet" href="/jcss/bootstrap.min.2.css" type="text/css">';
      echo ' <link rel="stylesheet" href="/jcss/bootstrap-multiselect.css" type="text/css">';
      echo ' <meta name="viewport" content="width=device-width, initial-scale=0.5, maximum-scale=5.0, minimum-scale=0.86">';            
    }
    else { // мультиселект НЕ бутстрап
      echo ' <script type="text/javascript" language="JavaScript" src="/js/jquery.multiselect.min.js"></script>';
    }
    if (($ug_gallery)) {
      ?>  
	<script type='text/javascript' src='/jcss/ug/js/unitegallery.js'></script>	
	<link rel='stylesheet' href='/jcss/ug/css/unite-gallery.css' type='text/css' />
	<script type='text/javascript' src='/jcss/ug/themes/tiles/ug-theme-tiles.js'></script>
      <?php 
    }
    if (($ftg_gallery)) {
      ?>  
        <script type='text/javascript' src='/jcss/ftg/js/jquery.finaltilesgallery.js'></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
        <link rel='stylesheet' href='/jcss/ftg/css/finaltilesgallery.css' type='text/css' />
        <script src="/jcss/ftg/js/lightbox2.js"></script>
	<link rel="stylesheet" href="/jcss/ftg/css/lightbox2.css">
      <?php 
    }
    if ($masonry) {
      echo ' <script src="/jcss/masonry.pkgd.min.js"></script> ';
      echo ' <script src="/jcss/imagesloaded.pkgd.min.js"></script> ';
    }
    if ($footable) {
      echo ' <link rel="stylesheet" href="/jcss/footable.bootstrap.min.css" type="text/css">';
      echo ' <script src="/jcss/footable.min.js"></script> ';
    }
    if ($jplist) {
      echo ' <script src="/jcss/jplist.core.min.js"></script> ';
      echo ' <script src="/jcss/jplist.pagination-bundle.min.js"></script> ';
    }
    if ($validator) {
      echo ' <script src="/jcss/jquery.validate.min.js"></script> ';
    }
    if ($ratingjs) {
      echo ' <script src="/jcss/bootstrap-rating-input.min.js"></script> ';
    }
    if ($datepicker) { //календарь на сайте
      echo ' <script type="text/javascript" language="JavaScript" src="/js/zebra_datepicker.src.min.js"></script>';
      echo ' <link rel="stylesheet" href="/jcss/defaultzebra.css" type="text/css">';
    }
    if ($colorbox) {
      echo ' <link rel="stylesheet" href="/colorbox.css" >';
      echo ' <script type="text/javascript" language="JavaScript" src="/js/jquery.colorbox.min.js"></script>';
    }
    //if ($fotorama) {
    //  echo ' <link rel="stylesheet" href="/jcss/fotorama.css" >';
    //  echo ' <script type="text/javascript" language="JavaScript" src="/js/fotorama.min.js"></script>';
    //}
    if ($infinitescroll) {
      echo ' <script type="text/javascript" language="JavaScript" src="/jcss/infinite-scroll.pkgd.js"></script>';
    }
    if ($jhtmleditor) {
      echo ' <link rel="stylesheet" href="/jcss/jHtmlArea.css" >';
      echo ' <script type="text/javascript" language="JavaScript" src="/js/jHtmlArea-0.8.min.js"></script>';
      echo ' <script>$(document).ready(function () { $("#msginput").htmlarea({
       toolbar: [
        "html", "|", "bold", "italic", "underline",
        "|",
        "h3", "h4", "h5", "h6", 
        "|", "p", "horizontalrule",
        "|",
        "unorderedList", "indent", "outdent","justifyleft","justifycenter","justifyright",
        "|",
        "link", "unlink", "image"
      ]}); });</script>';
    }
   
    if ($_noindex) {
      echo " <meta name=\"robots\" content=\"noindex\">\n";
    }
    if ($metadesc) {
     
  echo ' <meta name="description" content="' . htmlspecialchars($metadesc, ENT_QUOTES) . '">' . "\n";

    }
 if ($metafbtitle) {
  echo ' <meta property="og:title" content="' . htmlspecialchars($metafbtitle, ENT_QUOTES) . '">' . "\n";
} else {
  echo ' <meta property="og:title" content="' . htmlspecialchars($title, ENT_QUOTES) . '">' . "\n";
}
        
     if ($metafbdescription) {//разметка ФБ описание
      echo ' <meta property="og:description"  content="' . $metafbdescription . '">' . "\n";
    }
     
    if ($metafbimage) {//разметка ФБ картинка
      echo ' <meta property="og:image"  content="' . $metafbimage . '">' . "\n";
    }
    else {
      echo ' <meta property="og:image"  content="//autotravel.ru/jpg/chudick.gif">' . "\n";
    }
    if ($metacoord) {
      echo " $metacoord\n";
    }
    echo " <link rel=\"alternate\" type=\"application/rss+xml\" title=\"Отзывы на сайте autotravel.ru\" ";
    echo "href=\"$sitename/rsso.php\">\n";
    if ($dispgmap == 1) {        
      ?>
      <script src="/js/leaflet.js" type="text/javascript"></script>
      <script src="/js/KML.js" type="text/javascript"></script>
      <script src="/js/OSM.js" type="text/javascript"></script>
      <link rel="stylesheet" type="text/css" href="/jcss/leaflet.css">
      <?php
    }
    if ($dispgmap == 2) {        
      ?>
      <script src="/js/leaflet.js" type="text/javascript"></script>
      <script src="/js/KML.js" type="text/javascript"></script>
      <script src="/js/Yandex.js" type="text/javascript"></script>
      <script src="//api-maps.yandex.ru/2.0/?load=package.map&lang=ru-RU" type="text/javascript"></script>
      <link rel="stylesheet" type="text/css" href="/jcss/leaflet.css">
      <?php
    }
    if ($dispgmap == 3) {
      ?>
      <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $_maps_api_key; ?>&hl=ru&libraries=marker" type="text/javascript"></script>
     
      <script src="/js/geoxmlv3.js" type="text/javascript"></script>
      <script src="/js/ProjectedOverlay.js" type="text/javascript"></script>
      <?php
    }
    if ($dispgmap == 4) {
      ?>
      <script src="//maps.google.com/maps/api/js?v=3&amp;hl=ru&amp;sensor=false&amp;key=<?php echo $_maps_api_key; ?>" type="text/javascript"></script>
      <script src="/js/KmlMapParser_at.js" type="text/javascript"></script>
      <script type="text/javascript">
         var map;
         var parser;
      </script>
      <?php
      echo '<link rel="stylesheet" type="text/css" href="/KmlMapParser_at.css">';
   }
   echo " <title>";
   if ($title) {
     $title = mysafestring($title);
     echo "$title";
   } 
   else {
     echo "Автопутешествия по России и всему миру"; //07.02.2017 добавила  - и всему миру
   }
   ?>
   </title>
   <?php
   echo $newstyle;
   if ($bootstrap) {
     echo " <link type=\"text/css\" rel=\"StyleSheet\" href=\"$BSCSS\">\n";
   }
   else {
     echo " <link type=\"text/css\" rel=\"StyleSheet\" href=\"$CSS\">\n";  
   }
   //echo " <link type=\"text/css\" rel=\"StyleSheet\" href=\"$NEWCSS\">\n";
   echo "<link rel=\"stylesheet\" href=\"$NEWCSS?v=".time()."\">\n";
   ?>
</head>

<body itemscope itemtype="http://schema.org/WebPage" class="travell0t">
   

