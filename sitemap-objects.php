<?php
header('Content-Type: application/xml; charset=UTF-8');
echo '<?xml version="1.0" encoding="UTF-8"?>';

include_once($_SERVER['DOCUMENT_ROOT'].'/include/config.php');
$mysqli = new mysqli($dbhost, $dbuser, $dbpasswd, $dbname);
$mysqli->set_charset('utf8');

// --- Определяем режим ---
// Если передан параметр ?type=XXX — выдаём карту по типу,
// иначе создаём общий индекс
$type = isset($_GET['type']) ? trim($_GET['type']) : null;

if ($type) {
    // ---------- карта по типу ----------
    echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
    $stmt = $mysqli->prepare("SELECT id, timestamp FROM exciting WHERE type=? ORDER BY id ASC");
    $stmt->bind_param('s', $type);
    $stmt->execute();
    $res = $stmt->get_result();

    while ($row = $res->fetch_assoc()) {
        $loc  = "https://autotravel.ru/object.php/" . $row['id'];
        $date = substr($row['timestamp'], 0, 10);
        echo "  <url>\n";
        echo "    <loc>$loc</loc>\n";
        echo "    <lastmod>$date</lastmod>\n";
        echo "    <changefreq>monthly</changefreq>\n";
        echo "    <priority>0.7</priority>\n";
        echo "  </url>\n";
    }
    echo "</urlset>";

} else {
    // ---------- индексный файл ----------
    echo "<sitemapindex xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
    $res = $mysqli->query("SELECT id FROM type_ex ORDER BY id ASC");
    while ($row = $res->fetch_assoc()) {
        $tid = strtolower($row['id']);
        echo "  <sitemap>\n";
        echo "    <loc>https://autotravel.ru/sitemap-exciting.php?type=$tid</loc>\n";
        echo "  </sitemap>\n";
    }
    echo "</sitemapindex>";
}
?>
