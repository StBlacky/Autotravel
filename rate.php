<?php
include_once('include/config.php');
include_once('include/func.php');
include_once('include/header.php');

if (preg_match("/^\/(\d+)$/",$_pin,$exa)) {
 $userid = $exa[1];
}

$userid = (int)$userid;

$rating = array();
$excitings = array();

if ($userid) {
 $sql="select o.exciting as exciting, e.town as town, o.mark as mark from otklik o, exciting e "
         . "where o.cook like 'forum:$userid:%' and o.mark > 3 and e.id=o.exciting and e.type=1 and e.mainid>=0";
// echo $sql;
 $result = $dbi->query($sql);

 while ($row = mysqli_fetch_array($result)) {
    $town     = $row["town"];
    $exciting = $row["exciting"];
    $mark     = (int)$row["mark"];
    $mark     = $mark - 3;
    if ($mark) {
// 3 балла - не оценка
     if ($excitings["$town"]) {
      $excitings["$town"] = $excitings["$town"] .",".$exciting . "/" . $mark;
      $rating["$town"] = $rating["$town"] + $mark;
     }
     else {
      $excitings["$town"] = $exciting . "/" . $mark;
      $rating["$town"] = $mark;
     }
    }
 }
 arsort($rating);
 reset($rating);
 echo "<h1>Рекомендации от " . fusername($userid) . "</h1>\n";
 echo "<table border=1 width=95% cellpadding=2><tbody class=travell0>\n";
 echo "<tr><td width=50 class=travell5>Рейтинг</td>\n";
 echo "<td>Город, достопримечательности</td></tr>\n";
 while (list ($town, $rate) = each ($rating)) {
    echo "<tr><td width=50 class=travell5>$rate</td>\n";
    echo "<td><a class=travell5n href=/town.php/$town>" . gettownname($town) . "</a><br>\n";
//    echo "<td width=50>&nbsp;      : " . $excitings["$town"] . "\n<br>";
    $excs = explode(",",$excitings["$town"]);
    reset($excs);
    while (list($key, $val) = each($excs)) {
     list($exciting,$mark) = explode("/",$val);
     $mark = (int)$mark;
     $mark = $mark + 3;
     echo mark2stars($mark) . "&nbsp;" . getexname($exciting) . "<br>\n";
    }
    echo "</td></tr>\n";
 }
 mysqli_free_result($result);

 include('include/footer.php');
}
else {
 include("404.php");
}
?>
