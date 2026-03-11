<?php
  echo "<p class=travell0>";
  if (!($dispgmap)) {
   echo "Посмотреть на карте <a class=travell5m target=_blank href=\"" . $kml . ".htm\">Google MAP</a><br>\n";
  }
  echo "Скачать координаты достопримечательностей (POI) на навигатор в формате <a class=travell5m href='$kml" . ".wpt'>WPT</a>&nbsp;\n";
  echo "<a class=travell5m href='$kml" . ".kml'>KML</a>&nbsp;\n";
  echo "<a class=travell5m href='$kml" . ".gpx'>GPX</a>\n";
  echo "</p>\n";

?>