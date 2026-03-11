<?php
header('Content-Type: application/xml; charset=UTF-8');
echo '<?xml version="1.0" encoding="UTF-8"?>';

include_once($_SERVER['DOCUMENT_ROOT'].'/include/config.php');
$mysqli = new mysqli($dbhost, $dbuser, $dbpasswd, $dbname);
$mysqli->set_charset('utf8');
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php
$res = $mysqli->query("SELECT rid, time FROM l_reports ORDER BY rid ASC");

while ($row = $res->fetch_assoc()) {
    $loc = "https://autotravel.ru/v_report.php/" . $row['rid'];
    $date = date('Y-m-d', $row['time']);
    echo "  <url>\n";
    echo "    <loc>$loc</loc>\n";
    echo "    <lastmod>$date</lastmod>\n";
    echo "    <changefreq>yearly</changefreq>\n";
    echo "    <priority>0.4</priority>\n";
    echo "  </url>\n";
}
?>
</urlset>
