<?php
# sitemap-bring-area.php
header('Content-Type: application/xml; charset=UTF-8');
echo '<?xml version="1.0" encoding="UTF-8"?>';

include_once($_SERVER['DOCUMENT_ROOT'].'/include/config.php');

$mysqli = new mysqli($dbhost, $dbuser, $dbpasswd, $dbname);
$mysqli->set_charset('utf8');

?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php
$sql = "
  SELECT a.slug, a.timestamp
  FROM areas a
  JOIN exciting e
    ON e.area = a.id
   AND e.type = 5
   AND (e.mainid IS NULL OR e.mainid <= 0)
  WHERE a.slug <> '' AND a.slug IS NOT NULL
  GROUP BY a.slug, a.timestamp
  ORDER BY a.slug
";

$res = $mysqli->query($sql);
if ($res) {
  while ($row = $res->fetch_assoc()) {
    $slug = $row['slug'];
    $loc  = "https://autotravel.ru/excite_bring_area/" . $slug . "/";
    $date = substr($row['timestamp'], 0, 10);

    echo "  <url>\n";
    echo "    <loc>$loc</loc>\n";
    echo "    <lastmod>$date</lastmod>\n";
    echo "    <changefreq>yearly</changefreq>\n";
    echo "    <priority>0.5</priority>\n";
    echo "  </url>\n";
  }
  $res->free();
}
?>
</urlset>

