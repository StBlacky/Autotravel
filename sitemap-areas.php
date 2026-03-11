<?php
# sitemap-areas.php
header('Content-Type: application/xml; charset=UTF-8');
echo '<?xml version="1.0" encoding="UTF-8"?>';


# подключаем настройки
include_once($_SERVER['DOCUMENT_ROOT'].'/include/config.php');

# подключение к базе (использует переменные из config.php)
$mysqli = new mysqli($dbhost, $dbuser, $dbpasswd, $dbname);
$mysqli->set_charset('utf8');

?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php
$res = $mysqli->query("SELECT id, timestamp FROM areas ORDER BY id ASC");
while ($row = $res->fetch_assoc()) {
    $loc  = "https://autotravel.ru/area.php/" . $row['id'];
    $date = substr($row['timestamp'], 0, 10);
    echo "  <url>\n";
    echo "    <loc>$loc</loc>\n";
    echo "    <lastmod>$date</lastmod>\n";
    echo "    <changefreq>yearly</changefreq>\n";
    echo "    <priority>0.5</priority>\n";
    echo "  </url>\n";
}
?>
</urlset>
