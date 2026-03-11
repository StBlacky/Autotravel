<?php  

include_once('include/config.php');
require_once "include/func.php";
include_once "include/head00.php";

header("Content-type: application/rss+xml");
setlocale(LC_TIME, "C");
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
echo "<rss version=\"2.0\">\n<channel>\n";
echo "<title>Отзывы по объектам autotravel.ru</title>\n";
echo "<link>$sitename/</link>\n";
echo "<description>Объекты сайта \"По России на авто\": достопримечательности, кафе, гостиницы, музеи. Отзывы посетителей</description>\n";
// echo "<image>http://autotravel.ru/jpg/chudick.gif</image>\n";
$printed = "";

$sql = "SELECT o.nick as nick, o.time as o_time, t.id as town_id, 
         o.mark as mark, o.descrip as descr,
         o.exciting as ex_id, e.type as type, t.name as town_name, 
         t.hide as town_hide, e.name as ex_name 
         FROM otklik  o, towns t, exciting e where
         e.id = o.exciting and t.id = e.town order by o.time desc limit 25";
$result = $dbi->query($sql);

while ($myrow = mysqli_fetch_array($result)) {
    $s_nick = $myrow["nick"];
    $s_town = $myrow["town_name"];
    $o_time = $myrow["o_time"];
    $s_townid = $myrow["town_id"];
    $o_mark  = $myrow["mark"];
    $o_descr = $myrow["descr"];
    $t_hide = $myrow["town_hide"];
    $s_ex = $myrow["ex_id"];
    $s_exname = $myrow["ex_name"];
    $s_type = $myrow["type"];
    if (!($printed)) {
     echo "<lastBuildDate>" . strftime("%a, %d %b %Y %H:%M:%S %z",$o_time) . "</lastBuildDate>\n";
     $printed = "1";
    }
    $title = "$s_exname ($s_town) - от $s_nick ($o_mark баллов)";

    echo "<item>\n";
    echo "<title>$title</title>\n";
    echo "<link>$sitename/otklik.php/$s_ex</link>\n";
    echo "<description><![CDATA[$o_descr]]></description>\n";
    echo "<pubDate>". strftime("%a, %d %b %Y %H:%M:%S %z",$o_time) ."</pubDate>\n";
    echo "</item>\n";
}
echo "</channel>\n</rss>\n";

?>

