<?php
header('Content-Type: application/xml; charset=UTF-8');
echo '<?xml version="1.0" encoding="UTF-8"?>';

include_once($_SERVER['DOCUMENT_ROOT'].'/include/config.php');
$mysqli = new mysqli($dbhost, $dbuser, $dbpasswd, $dbname);
$mysqli->set_charset('utf8');
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php
$res = $mysqli->query("SELECT id FROM trip_desc ORDER BY id ASC");
$today = date('Y-m-d');

while ($row = $res->fetch_assoc()) {
    $loc = "https://autotravel.ru/trip.php/" . $row['id'];
    echo "  <url>\n";
    echo "    <loc>$loc</loc>\n";
    echo "    <lastmod>$today</lastmod>\n";
    echo "    <changefreq>yearly</changefreq>\n";
    echo "    <priority>0.5</priority>\n";
    echo "  </url>\n";
}
?>
</urlset>
