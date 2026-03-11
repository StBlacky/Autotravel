<?php

include_once("include/head00.php");  
# autotravel.ru     <script src="http://maps.google.com/maps?file=api&v=2&hl=ru&key=ABQIAAAAFMZwoCk_W0FlOrGWEcG8-hSkiWjGOTaHXZ4RFhdKUD1ygS1joBQivUjpo-_GuMI6Qp1El3LLZxDekQ" type="text/javascript"></script>
# autotravel.org.ru <script src="http://maps.google.com/maps?file=api&v=2&hl=ru&key=ABQIAAAAFMZwoCk_W0FlOrGWEcG8-hSrOn_KGLvWfx-bYd1aYMTYLrRKqBR-yj6I_NPUaZ-KZCinzOBr_cGd9w" type="text/javascript"></script>
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<head>  
<meta name='yandex-verification' content='7df66c2dcc98af4c'>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="Content-language" content="ru">
<link rel="shortcut icon" href="/jpg/favicon.ico">
<script type="text/javascript" language="JavaScript" src="/js/travel5.js"></script>
<script type="text/javascript" language="JavaScript" src="/js/trip.js"></script>
<?php 
 if ($metatags) {
  echo '<meta name=keywords content="' . $metatags . '">' ."\n";
 }
 else {
  echo '<meta name=keywords content="автопутешествия отчеты страны города достопримечательности гостиницы карты">' ."\n";
 }

 if ($_noindex) {
  echo "<meta name=\"robots\" content=\"noindex\">\n";
 }

 if ($metadesc) {
  echo '<meta name=description content="' . $metadesc . '">' ."\n";
 }
 if ($metacoord) {
  echo "$metacoord\n";
 }
 

// --- Канонический URL, если определён ранее (например, в excite.php или town.php) ---
if (isset($canon) && $canon) {
    // превращаем относительный путь в абсолютный, если надо
    $canon_url = (strpos($canon, 'http') === 0)
        ? $canon
        : 'https://' . $_SERVER['HTTP_HOST'] . $canon;
    echo '<link rel="canonical" href="' . htmlspecialchars($canon_url, ENT_QUOTES) . '">' . "\n";
}


// if ($script_name === "index.php") {
  echo "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"Новости сайта autotravel.ru\" ";
  echo "href=\"$sitename/rss.php\">\n";
  echo "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"Отзывы на сайте autotravel.ru\" ";
  echo "href=\"$sitename/rsso.php\">\n";
// }
 if ($dispgmap == 2) {
?>
<script src="//maps.google.com/maps?file=api&v=2&hl=ru&key=ABQIAAAAFMZwoCk_W0FlOrGWEcG8-hSkiWjGOTaHXZ4RFhdKUD1ygS1joBQivUjpo-_GuMI6Qp1El3LLZxDekQ" type="text/javascript"></script>
<script src="/js/gmap2.js" type="text/javascript"></script>
<?php
 }
 if ($dispgmap == 3) {
?>
<script src="https://maps.google.com/maps/api/js?v=3&hl=ru&sensor=false&key=<?php echo $_maps_api_key; ?>" type="text/javascript"></script>
<script src="/js/geoxmlv3.js" type="text/javascript"></script>
<script src="/js/ProjectedOverlay.js" type="text/javascript"></script>
<?php
 }
 if ($dispgmap == 4) {
?>
<script src="https://maps.google.com/maps/api/js?v=3&hl=ru&sensor=false&key=<?php echo $_maps_api_key; ?>" type="text/javascript"></script>
<script src="/js/KmlMapParserAT.js" type="text/javascript"></script>
<script type="text/javascript">
 var map;
 var parser;
 function initmapv5(kmlfile) {
  var options = {
      center: new google.maps.LatLng(0,0),
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      mapTypeControl: true,
      navigationControl: true,
      draggingCursor: 'move'
  };
  var mapdiv = document.getElementById("gmapv4");
  mapdiv.style.visibility = 'visible';
  mapdiv.style.display = 'block';
  map = new google.maps.Map(mapdiv, options);
  parser = new KmlMapParser({ map: map,
      zoomOnClick: false,
      kml: kmlfile,
      showSidebar: true,
      showFolders: true,
      showSidebarDescriptions: true,
      showSidebarBubble: true,
      showRootName: true,
      allFoldersOpen: false
  });
  var bounds = new google.maps.LatLngBounds();
  //parser.parse(kmlfile);
  map.fitBounds(bounds);
  var listenx = google.maps.event.addListener(map, "idle", function() { 
    if (map.getZoom() > 14) map.setZoom(14); 
    google.maps.event.removeListener(listenx); 
  });
 }
 function filterSidebar(searchText){
	parser.filterMap(searchText);    
 } 

 function filter(){
	document.forms[0].search.value='';
	parser.filterMap();
 }
</script>

<?php
 }
 echo "<title>";
 if ($title) {
   $title = mysafestring($title);
   echo "$title";
 }
 else {
   echo "Автопутешествия по России";
 }
?>
</title>
<link  type="text/css" rel="StyleSheet" href="$CSS" type="text/css"> 
</meta>
</head>  
<body itemscope itemtype="http://schema.org/WebPage" <?php //добавлю разметку вебстраницы 17.02.2017
 if ($dispgmap) {
  echo " onload=\"initialize()\" onunload=\"GUnload()\" ";
 } 
 
?> class=travell0>

