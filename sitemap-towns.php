<?php
header('Content-Type: application/xml; charset=UTF-8');
echo '<?xml version="1.0" encoding="UTF-8"?>';

// подключаем конфиг
include_once($_SERVER['DOCUMENT_ROOT'].'/include/config.php');

// подключаемся к базе
$mysqli = new mysqli($dbhost, $dbuser, $dbpasswd, $dbname);
$mysqli->set_charset('utf8');

?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php
// выбираем только видимые города (hide=0)
$res = $mysqli->query("SELECT id, url, timestamp FROM towns WHERE hide=0 ORDER BY id ASC");

while ($row = $res->fetch_assoc()) {
    // если есть ЧПУ-ссылка — используем её
    if (!empty($row['url'])) {
        $loc = "https://autotravel.ru/town/" . $row['url'];
    } else {
        $loc = "https://autotravel.ru/town.php/" . $row['id'];
    }
    $date = substr($row['timestamp'], 0, 10);
    echo "  <url>\n";
    echo "    <loc>$loc</loc>\n";
    echo "    <lastmod>$date</lastmod>\n";
    echo "    <changefreq>yearly</changefreq>\n";
    echo "    <priority>0.6</priority>\n";
    echo "  </url>\n";
}
?>
</urlset>
