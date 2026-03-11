<?php  

include_once('include/config.php');
require_once "include/func.php";
include_once "include/head00.php";

header("Content-type: application/rss+xml");
setlocale(LC_TIME, "C");
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
echo "<rss version=\"2.0\">\n<channel>\n";
echo "<title>Новости сайта autotravel.ru</title>\n";
echo "<link>$sitename/</link>\n";
echo "<description>Сайт \"По России на авто\": города, достопримечательности, кафе, гостиницы, музеи. Вся необходимая информация для автопутешественника</description>\n";
// echo "<image>http://autotravel.ru/jpg/chudick.gif</image>\n";
// echo "<lastBuildDate>" . strftime("%a, %d %b %Y %H:%m:%S %z",time()) . "</lastBuildDate>\n";
$printed = "";

$sql = "select id, date, title, text, url from news order by date desc limit 12";
$result = $dbi->query($sql);

while ($myrow = mysqli_fetch_array($result)) {
    $id    = $myrow["id"];
    $date  = $myrow["date"];
    $title = $myrow["title"];
    $text  = $myrow["text"];
    $url   = $myrow["url"];
    $url   = str_replace("&","&amp;",$url);
    if (!($printed)) {
     echo "<lastBuildDate>" . strftime("%a, %d %b %Y %H:%M:%S %z",strtotime($date) + 36000) . "</lastBuildDate>\n";
     $printed = "1";
    }
    echo "<item>\n";
    echo "<title>$title</title>\n";
    echo "<link>$sitename/index.php?id=$id</link>\n";
    echo "<description><![CDATA[$text]]></description>\n";
    echo "<pubDate>". strftime("%a, %d %b %Y %H:%M:%S %z",strtotime($date) + 36000) ."</pubDate>\n";
//    echo "<guid>http://autotravel.ru/</guid>\n";
    echo "</item>\n";
}
echo "</channel>\n</rss>\n";

?>

